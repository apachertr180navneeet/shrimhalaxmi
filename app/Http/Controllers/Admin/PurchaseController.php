<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:purchase-list', ['only' => ['index']]);
        $this->middleware('permission:purchase-create', ['only' => ['create','store']]);
        $this->middleware('permission:purchase-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:purchase-delete', ['only' => ['destroy']]);
    }

    private function defaultItemAbbr(): string
    {
        return Item::query()->orderBy('item_name')->value('abbr') ?: 'ITEM';
    }

    private function defaultVendorAbbr(): string
    {
        return Vendor::query()->orderBy('vendor_name')->value('abbr') ?: 'VENDOR';
    }

    private function nextPchNo(?int $ignoreId = null): string
    {
        if (! Schema::hasTable('purchases')) {
            return '0001';
        }

        $query = Purchase::withTrashed();

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        $max = $query->pluck('pch_no')->map(fn ($pchNo) => (int) preg_replace('/\D/', '', (string) $pchNo))->max() ?? 0;

        return str_pad((string) ($max + 1), 4, '0', STR_PAD_LEFT);
    }

    private function createDefaults(): array
    {
        $pchNo = $this->nextPchNo();
        $vendorAbbr = $this->defaultVendorAbbr();

        return [
            'date' => '',
            'pch_no' => $pchNo,
            'bno' => '',
            'vendor_id' => '',
            'remark' => '',
            'freight' => '',
            'transport' => '',
            'lr_no' => '',
            'vendor_abbr' => $vendorAbbr,
            'item_abbr' => $this->defaultItemAbbr(),
            'lot_no' => $vendorAbbr . ' / ' . $pchNo . ' / 0001',
        ];
    }

    public function index()
    {
        return view('admin.purchases.index');
    }

    public function getAll(Request $request)
    {
        try {
            $query = Purchase::with('vendor')->latest();

            if ($request->filled('vendor_name')) {
                $search = $request->vendor_name;
                $query->whereHas('vendor', function ($q) use ($search) {
                    $q->where('vendor_name', 'like', "%{$search}%")
                        ->orWhere('abbr', 'like', "%{$search}%");
                });
            }

            if ($request->filled('purchase_date')) {
                $query->whereDate('purchase_date', $request->purchase_date);
            }

            if ($request->filled('search_value')) {
                $search = $request->search_value;
                $query->where(function ($q) use ($search) {
                    $q->where('pch_no', 'like', "%{$search}%")
                        ->orWhere('bno', 'like', "%{$search}%")
                        ->orWhere('freight', 'like', "%{$search}%")
                        ->orWhereHas('vendor', function ($vendorQuery) use ($search) {
                            $vendorQuery->where('vendor_name', 'like', "%{$search}%")
                                ->orWhere('abbr', 'like', "%{$search}%");
                        });
                });
            }

            return DataTables::of($query)
                ->addColumn('date', fn ($row) => optional($row->purchase_date)->format('d/m/Y') ?: '-')
                ->addColumn('vendor_name', fn ($row) => $row->vendor?->vendor_name ?: '-')
                ->addColumn('action', function ($row) {
                    return '
                        <a href="' . route('admin.purchases.edit', $row->id) . '" class="btn btn-sm btn-primary">Edit</a>
                        <button class="btn btn-sm btn-danger deleteBtn" data-id="' . $row->id . '">Delete</button>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        } catch (\Exception $e) {
            \Log::error('Purchase DataTable Error: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong!']);
        }
    }

    public function create()
    {
        $purchase = $this->createDefaults();
        $vendors = Vendor::query()->orderBy('vendor_name')->get(['id', 'vendor_name', 'abbr']);
        $items = Item::query()->orderBy('item_name')->get(['id', 'item_name', 'abbr']);
        $purchaseItems = collect();

        return view('admin.purchases.create', compact('purchase', 'vendors', 'items', 'purchaseItems'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'pch_no' => 'required|string|max:20|unique:purchases,pch_no',
            'bno' => 'nullable|string|max:50',
            'vendor_id' => 'required|exists:vendors,id',
            'remark' => 'nullable|string',
            'freight' => ['nullable', Rule::in(['Paid', 'To be Paid', 'To be Shiped'])],
            'items_data' => 'required|array|min:1',
            'items_data.*.item_id' => 'required|exists:items,id',
            'items_data.*.lot_no' => 'required|string|max:100',
            'items_data.*.item_code' => 'required|string|max:100',
            'items_data.*.color' => 'nullable|string|max:150',
            'items_data.*.qty_m' => 'required|numeric|min:0',
            'items_data.*.fold' => 'required|numeric|min:0',
            'items_data.*.rate' => 'required|numeric|min:0',
            'items_data.*.transport' => 'nullable|string|max:150',
            'items_data.*.lr_no' => 'nullable|string|max:100',
            'items_data.*.net_meter' => 'required|numeric|min:0',
            'items_data.*.amount' => 'required|numeric|min:0',
            'items_data.*.sort_order' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::transaction(function () use ($request) {
                $vendor = Vendor::findOrFail($request->vendor_id);
                $itemsData = collect($request->items_data);

                $purchase = Purchase::create([
                    'purchase_date' => $request->date,
                    'pch_no' => $request->pch_no,
                    'bno' => $request->bno,
                    'vendor_id' => $vendor->id,
                    'vendor_abbr' => $vendor->abbr,
                    'remark' => $request->remark,
                    'freight' => $request->freight,
                    'total_qty_m' => $itemsData->sum(fn ($item) => (float) $item['qty_m']),
                    'total_net_meter' => $itemsData->sum(fn ($item) => (float) $item['net_meter']),
                    'total_amount' => $itemsData->sum(fn ($item) => (float) $item['amount']),
                ]);

                foreach ($itemsData as $itemRow) {
                    PurchaseItem::create([
                        'purchase_id' => $purchase->id,
                        'item_id' => $itemRow['item_id'],
                        'item_code' => $itemRow['item_code'],
                        'sort_order' => $itemRow['sort_order'],
                        'lot_no' => $itemRow['lot_no'],
                        'color' => $itemRow['color'] ?? null,
                        'qty_m' => $itemRow['qty_m'],
                        'fold' => $itemRow['fold'],
                        'rate' => $itemRow['rate'],
                        'transport' => $itemRow['transport'] ?? null,
                        'lr_no' => $itemRow['lr_no'] ?? null,
                        'net_meter' => $itemRow['net_meter'],
                        'amount' => $itemRow['amount'],
                    ]);
                }
            });

            return redirect()->route('admin.purchases.index')->with('success', 'Purchase added successfully');
        } catch (\Exception $e) {
            \Log::error('Purchase Store Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong!')->withInput();
        }
    }

    public function edit($id)
    {
        try {
            $purchaseRecord = Purchase::with(['vendor', 'items.item'])->findOrFail($id);
            $firstItem = $purchaseRecord->items->sortBy('sort_order')->first();
            $vendorAbbr = $purchaseRecord->vendor?->abbr ?: $this->defaultVendorAbbr();

            $purchase = [
                'id' => $purchaseRecord->id,
                'date' => optional($purchaseRecord->purchase_date)->format('Y-m-d'),
                'pch_no' => $purchaseRecord->pch_no,
                'bno' => $purchaseRecord->bno,
                'vendor_id' => $purchaseRecord->vendor_id,
                'remark' => $purchaseRecord->remark,
                'freight' => $purchaseRecord->freight,
                'transport' => $firstItem?->transport,
                'lr_no' => $firstItem?->lr_no,
                'vendor_abbr' => $vendorAbbr,
                'item_abbr' => $firstItem?->item?->abbr ?: $this->defaultItemAbbr(),
                'lot_no' => $firstItem?->lot_no ?: ($vendorAbbr . ' / ' . $purchaseRecord->pch_no . ' / 0001'),
            ];

            $vendors = Vendor::query()->orderBy('vendor_name')->get(['id', 'vendor_name', 'abbr']);
            $items = Item::query()->orderBy('item_name')->get(['id', 'item_name', 'abbr']);
            $purchaseItems = $purchaseRecord->items()->with('item')->orderBy('sort_order')->get();

            return view('admin.purchases.edit', compact('purchase', 'vendors', 'items', 'purchaseItems'));
        } catch (\Exception $e) {
            \Log::error('Purchase Edit Error: ' . $e->getMessage());
            return redirect()->route('admin.purchases.index')->with('error', 'Record not found');
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'pch_no' => ['required', 'string', 'max:20', Rule::unique('purchases', 'pch_no')->ignore($id)],
            'bno' => 'nullable|string|max:50',
            'vendor_id' => 'required|exists:vendors,id',
            'remark' => 'nullable|string',
            'freight' => ['nullable', Rule::in(['Paid', 'To be Paid', 'To be Shiped'])],
            'items_data' => 'required|array|min:1',
            'items_data.*.item_id' => 'required|exists:items,id',
            'items_data.*.lot_no' => 'required|string|max:100',
            'items_data.*.item_code' => 'required|string|max:100',
            'items_data.*.color' => 'nullable|string|max:150',
            'items_data.*.qty_m' => 'required|numeric|min:0',
            'items_data.*.fold' => 'required|numeric|min:0',
            'items_data.*.rate' => 'required|numeric|min:0',
            'items_data.*.transport' => 'nullable|string|max:150',
            'items_data.*.lr_no' => 'nullable|string|max:100',
            'items_data.*.net_meter' => 'required|numeric|min:0',
            'items_data.*.amount' => 'required|numeric|min:0',
            'items_data.*.sort_order' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::transaction(function () use ($request, $id) {
                $purchase = Purchase::findOrFail($id);
                $vendor = Vendor::findOrFail($request->vendor_id);
                $itemsData = collect($request->items_data);

                // Get existing items before deletion for stock adjustment
                $existingItems = $purchase->items;

                $purchase->update([
                    'purchase_date' => $request->date,
                    'pch_no' => $request->pch_no,
                    'bno' => $request->bno,
                    'vendor_id' => $vendor->id,
                    'vendor_abbr' => $vendor->abbr,
                    'remark' => $request->remark,
                    'freight' => $request->freight,
                    'total_qty_m' => $itemsData->sum(fn ($item) => (float) $item['qty_m']),
                    'total_net_meter' => $itemsData->sum(fn ($item) => (float) $item['net_meter']),
                    'total_amount' => $itemsData->sum(fn ($item) => (float) $item['amount']),
                ]);

                // Decrease stock for existing items being removed
                foreach ($existingItems as $existingItem) {
                    $item = Item::find($existingItem->item_id);
                    if ($item) {
                        $item->decreaseStock((float) $existingItem->qty_m, (float) $existingItem->net_meter);
                    }
                }

                $purchase->items()->delete();

                foreach ($itemsData as $itemRow) {
                    PurchaseItem::create([
                        'purchase_id' => $purchase->id,
                        'item_id' => $itemRow['item_id'],
                        'item_code' => $itemRow['item_code'],
                        'sort_order' => $itemRow['sort_order'],
                        'lot_no' => $itemRow['lot_no'],
                        'color' => $itemRow['color'] ?? null,
                        'qty_m' => $itemRow['qty_m'],
                        'fold' => $itemRow['fold'],
                        'rate' => $itemRow['rate'],
                        'transport' => $itemRow['transport'] ?? null,
                        'lr_no' => $itemRow['lr_no'] ?? null,
                        'net_meter' => $itemRow['net_meter'],
                        'amount' => $itemRow['amount'],
                    ]);
                }
            });

            return redirect()->route('admin.purchases.index')->with('success', 'Purchase updated successfully');
        } catch (\Exception $e) {
            \Log::error('Purchase Update Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong!')->withInput();
        }
    }

    public function delete($id)
    {
        try {
            Purchase::findOrFail($id)->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Purchase Delete Error: ' . $e->getMessage());
            return response()->json(['success' => false]);
        }
    }
}
