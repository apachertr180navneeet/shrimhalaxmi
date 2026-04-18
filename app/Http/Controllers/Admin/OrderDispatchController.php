<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Item;
use App\Models\JobWorkerInwardItem;
use App\Models\OrderDispatch;
use App\Models\OrderDispatchItem;
use App\Models\PurchaseItem;
use App\Models\JobWorkAssignmentItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class OrderDispatchController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:dispatch-list', ['only' => ['index', 'preview']]);
        $this->middleware('permission:dispatch-create', ['only' => ['create','store']]);
        $this->middleware('permission:dispatch-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:dispatch-delete', ['only' => ['destroy']]);
    }

    private function nextDispatchNo(?int $ignoreId = null): string
    {
        if (! Schema::hasTable('order_dispatches')) {
            return '0001';
        }

        $query = OrderDispatch::withTrashed();

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        $max = $query->pluck('dispatch_no')->map(fn ($dispatchNo) => (int) preg_replace('/\D/', '', (string) $dispatchNo))->max() ?? 0;

        return str_pad((string) ($max + 1), 4, '0', STR_PAD_LEFT);
    }

    private function lotSources()
    {

        $purchaseRows = PurchaseItem::query()
            ->with([
                'purchase:id,purchase_date,bno,vendor_id',
                'purchase.vendor:id,vendor_name',
                'item:id,item_name',
            ])
            ->whereHas('purchase')
            ->orderByRaw('(select purchase_date from purchases where purchases.id = purchase_items.purchase_id) asc')
            ->orderBy('sort_order')
            ->get();

        $purchaseLotKeys = $purchaseRows
            ->mapWithKeys(function (PurchaseItem $row) {
                $key = trim((string) $row->lot_no) . '|' . (int) $row->item_id;

                return [$key => true];
            });

        // ✅ Assignment (purchase_item_id wise)
        $assignedByPurchaseItem = JobWorkAssignmentItem::query()
            ->whereNotNull('purchase_item_id')
            ->get()
            ->groupBy('purchase_item_id')
            ->map(fn ($rows) => (float) $rows->sum('meter'));

        // ✅ Dispatch (lot + item wise)
        $dispatchByLotAndItem = OrderDispatchItem::query()
            ->get()
            ->groupBy(fn (OrderDispatchItem $row) => trim((string) $row->lot_no) . '|' . (int) $row->item_id)
            ->map(fn ($rows) => (float) $rows->sum('meter'));

        // ✅ NEW: Job Work Inward (lot + item wise)
        $inwardByLotAndItem = DB::table('job_worker_inward_items')
            ->select('item_id', 'lot_no', DB::raw('SUM(meter) as inward_meter'))
            ->groupBy('item_id', 'lot_no')
            ->get()
            ->groupBy(fn ($row) => trim((string) $row->lot_no) . '|' . (int) $row->item_id)
            ->map(fn ($rows) => (float) $rows->sum('inward_meter'));

        $purchaseBalanceRows = $purchaseRows
            ->map(function (PurchaseItem $row) use (
                $assignedByPurchaseItem,
                $dispatchByLotAndItem,
                $inwardByLotAndItem
            ) {

                $purchaseDate = optional($row->purchase?->purchase_date);

                $assigned = (float) ($assignedByPurchaseItem[$row->id] ?? 0);

                $key = trim((string) $row->lot_no) . '|' . (int) $row->item_id;

                $dispatched = (float) ($dispatchByLotAndItem[$key] ?? 0);

                // ✅ NEW inward
                $inward = (float) ($inwardByLotAndItem[$key] ?? 0);

                // ✅ FINAL BALANCE (UPDATED)
                $balance = (float) $row->qty_m 
                    + $inward 
                    - $assigned 
                    - $dispatched;

                return [
                    'item_id' => (int) $row->item_id,
                    'date' => $purchaseDate?->format('d.m.y') ?: '-',
                    'sort_date' => $purchaseDate?->format('Y-m-d') ?: '0000-00-00',
                    'supplier_name' => (string) ($row->purchase?->vendor?->vendor_name ?? '-'),
                    'bill_no' => (string) ($row->purchase?->bno ?? '-'),
                    'lot_no' => (string) ($row->lot_no ?? '-'),
                    'quality' => (string) ($row->item?->item_name ?? 'Unknown Quality'),

                    // ✅ FINAL OUTPUT
                    'total_meter' => max($balance, 0),

                    'lr_number' => (string) ($row->lr_no ?: '-'),
                    'transport' => (string) ($row->transport ?: '-'),
                ];
            })
            ->filter(fn (array $row) => $row['total_meter'] > 0.0001);

        $inwardOnlyRows = JobWorkerInwardItem::query()
            ->with([
                'inward:id,inward_date,ch_no,job_worker_id',
                'inward.jobWorker:id,name',
                'item:id,item_name',
            ])
            ->whereHas('inward')
            ->get()
            ->groupBy(fn (JobWorkerInwardItem $row) => trim((string) $row->lot_no) . '|' . (int) $row->item_id)
            ->reject(fn ($rows, string $key) => isset($purchaseLotKeys[$key]))
            ->map(function ($rows, string $key) use ($dispatchByLotAndItem) {
                $sortedRows = $rows->sortBy(fn (JobWorkerInwardItem $row) => $row->inward?->inward_date?->format('Y-m-d') ?: '9999-12-31');
                $first = $sortedRows->first();
                $inwardDate = $first?->inward?->inward_date;
                $inwardMeter = (float) $rows->sum('meter');
                $dispatched = (float) ($dispatchByLotAndItem[$key] ?? 0);
                $balance = $inwardMeter - $dispatched;

                return [
                    'item_id' => (int) ($first?->item_id ?? 0),
                    'date' => $inwardDate?->format('d.m.y') ?: '-',
                    'sort_date' => $inwardDate?->format('Y-m-d') ?: '0000-00-00',
                    'supplier_name' => (string) ($first?->inward?->jobWorker?->name ?? 'Job Inward'),
                    'bill_no' => (string) ($first?->inward?->ch_no ?? '-'),
                    'lot_no' => (string) ($first?->lot_no ?? '-'),
                    'quality' => (string) ($first?->item?->item_name ?? 'Unknown Quality'),
                    'total_meter' => max($balance, 0),
                    'lr_number' => '-',
                    'transport' => '-',
                ];
            })
            ->filter(fn (array $row) => $row['total_meter'] > 0.0001);

        return $purchaseBalanceRows
            ->concat($inwardOnlyRows)
            ->sort(function (array $a, array $b) {
                $dateSort = strcmp($a['sort_date'], $b['sort_date']);
                if ($dateSort !== 0) {
                    return $dateSort;
                }

                return strcmp($a['lot_no'], $b['lot_no']);
            })
            ->values();
    }

    public function index()
    {
        return view('admin.order_dispatches.index');
    }

    public function getAll(Request $request)
    {
        try {
            $query = OrderDispatch::with('customer')->latest();

            if ($request->filled('customer_name')) {
                $search = $request->customer_name;
                $query->whereHas('customer', fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('abbr', 'like', "%{$search}%"));
            }

            if ($request->filled('dispatch_date')) {
                $query->whereDate('dispatch_date', $request->dispatch_date);
            }

            if ($request->filled('search_value')) {
                $search = $request->search_value;
                $query->where(function ($q) use ($search) {
                    $q->where('dispatch_no', 'like', "%{$search}%")
                        ->orWhere('bill_no', 'like', "%{$search}%")
                        ->orWhere('transport', 'like', "%{$search}%")
                        ->orWhere('mobile_number', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhereHas('customer', fn ($customerQuery) => $customerQuery->where('name', 'like', "%{$search}%")->orWhere('abbr', 'like', "%{$search}%"));
                });
            }

            return DataTables::of($query)
                ->addColumn('date', fn ($row) => optional($row->dispatch_date)->format('d/m/Y') ?: '-')
                ->addColumn('customer_name', fn ($row) => $row->customer?->name ?: '-')
                ->addColumn('customer_phone', fn ($row) => $row->customer?->phone ?: '-')
                ->addColumn('status', function ($row) {
                    $color = 'secondary';
                    switch ($row->status) {
                        case 'Pending': $color = 'warning'; break;
                        case 'In Transit': $color = 'info'; break;
                        case 'Complete': $color = 'success'; break;
                        case 'Cancelled': $color = 'danger'; break;
                    }
                    return '<button class="btn btn-sm btn-' . $color . '" disabled>' . htmlspecialchars($row->status) . '</button>';
                })
                ->addColumn('action', fn ($row) => '<a href="' . route('admin.orderdispatches.preview', $row->id) . '" class="btn btn-sm btn-info" target="_blank">Challan</a> <a href="' . route('admin.orderdispatches.edit', $row->id) . '" class="btn btn-sm btn-primary">Edit</a> <button class="btn btn-sm btn-danger deleteBtn" data-id="' . $row->id . '">Delete</button>')
                ->rawColumns(['action', 'status'])
                ->make(true);
        } catch (\Exception $e) {
            \Log::error('OrderDispatch DataTable Error: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong!']);
        }
    }

    public function create()
    {
        $dispatch = [
            'dispatch_date' => now()->format('Y-m-d'),
            'dispatch_no' => $this->nextDispatchNo(),
            'bill_no' => '',
            'customer_id' => '',
            'mobile_number' => '',
            'transport' => '',
            'status' => 'Pending',
        ];

        $customers = Customer::orderBy('name')->get(['id', 'name', 'abbr']);
        $items = Item::where('status','active')->orderBy('item_name')->get(['id', 'item_name', 'abbr']);
        $lotSources = $this->lotSources();
        $dispatchItems = collect();

        return view('admin.order_dispatches.create', compact('dispatch', 'customers', 'items', 'lotSources', 'dispatchItems'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dispatch_date' => 'required|date',
            'dispatch_no' => 'required|string|max:50|unique:order_dispatches,dispatch_no',
            'bill_no' => 'nullable|string|max:50',
            'customer_id' => 'required|exists:customers,id',
            'mobile_number' => 'nullable|string|max:25',
            'transport' => 'nullable|string|max:150',
            'status' => ['required', Rule::in(['Pending', 'In Transit', 'Complete', 'Cancelled'])],
            'items_data' => 'required|array|min:1',
            'items_data.*.item_id' => 'required|exists:items,id',
            'items_data.*.lot_no' => 'required|string|max:100',
            'items_data.*.meter' => 'required|numeric|min:0',
            'items_data.*.rate' => 'required|numeric|min:0',
            'items_data.*.amount' => 'required|numeric|min:0',
            'items_data.*.gst' => 'required|numeric|min:0',
            'items_data.*.total_amount' => 'required|numeric|min:0',
            'items_data.*.sort_order' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::transaction(function () use ($request) {
                $itemsData = collect($request->items_data);

                $dispatch = OrderDispatch::create([
                    'dispatch_date' => $request->dispatch_date,
                    'dispatch_no' => $request->dispatch_no,
                    'bill_no' => $request->bill_no,
                    'customer_id' => $request->customer_id,
                    'mobile_number' => $request->mobile_number,
                    'transport' => $request->transport,
                    'status' => $request->status,
                    'total_meter' => $itemsData->sum(fn ($item) => (float) $item['meter']),
                    'total_amount' => $itemsData->sum(fn ($item) => (float) ($item['total_amount'] ?? $item['amount'] ?? 0)),
                ]);

                foreach ($itemsData as $itemRow) {
                    OrderDispatchItem::create([
                        'order_dispatch_id' => $dispatch->id,
                        'item_id' => $itemRow['item_id'],
                        'lot_no' => $itemRow['lot_no'],
                        'item_code' => $itemRow['item_code'] ?? null,
                        'meter' => $itemRow['meter'],
                        'rate' => $itemRow['rate'],
                        'amount' => $itemRow['amount'],
                        'gst' => $itemRow['gst'] ?? 0,
                        'total_amount' => $itemRow['total_amount'] ?? $itemRow['amount'],
                        'sort_order' => $itemRow['sort_order'],
                    ]);
                }
            });

            return redirect()->route('admin.orderdispatches.index')->with('success', 'Order dispatch added successfully');
        } catch (\Exception $e) {
            dd($e);
            \Log::error('OrderDispatch Store Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong!')->withInput();
        }
    }

    public function edit($id)
    {
        try {
            $dispatchRecord = OrderDispatch::with(['customer', 'items.item'])->findOrFail($id);

            $dispatch = [
                'id' => $dispatchRecord->id,
                'dispatch_date' => optional($dispatchRecord->dispatch_date)->format('Y-m-d'),
                'dispatch_no' => $dispatchRecord->dispatch_no,
                'bill_no' => $dispatchRecord->bill_no,
                'customer_id' => $dispatchRecord->customer_id,
                'mobile_number' => $dispatchRecord->mobile_number,
                'transport' => $dispatchRecord->transport,
                'status' => $dispatchRecord->status,
            ];

            $customers = Customer::orderBy('name')->get(['id', 'name', 'abbr']);
            $items = Item::orderBy('item_name')->get(['id', 'item_name', 'abbr']);
            $lotSources = $this->lotSources();
            $dispatchItems = $dispatchRecord->items()->orderBy('sort_order')->get();

            return view('admin.order_dispatches.edit', compact('dispatch', 'customers', 'items', 'lotSources', 'dispatchItems'));
        } catch (\Exception $e) {
            \Log::error('OrderDispatch Edit Error: ' . $e->getMessage());
            return redirect()->route('admin.orderdispatches.index')->with('error', 'Record not found');
        }
    }

    public function preview($id)
    {
        try {
            $dispatch = OrderDispatch::with([
                'customer:id,name,abbr,phone,firm_name,gst_no,location,address_2',
                'items' => function ($query) {
                    $query->with('item:id,item_name,abbr')->orderBy('sort_order');
                },
            ])->findOrFail($id);

            return view('admin.order_dispatches.preview', compact('dispatch'));
        } catch (\Exception $e) {
            \Log::error('OrderDispatch Preview Error: ' . $e->getMessage());

            return redirect()->route('admin.orderdispatches.index')->with('error', 'Record not found');
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'dispatch_date' => 'required|date',
            'dispatch_no' => ['required', 'string', 'max:50', Rule::unique('order_dispatches', 'dispatch_no')->ignore($id)],
            'bill_no' => 'nullable|string|max:50',
            'customer_id' => 'required|exists:customers,id',
            'mobile_number' => 'nullable|string|max:25',
            'transport' => 'nullable|string|max:150',
            'status' => ['required', Rule::in(['Pending', 'In Transit', 'Complete', 'Cancelled'])],
            'items_data' => 'required|array|min:1',
            'items_data.*.item_id' => 'required|exists:items,id',
            'items_data.*.lot_no' => 'required|string|max:100',
            'items_data.*.meter' => 'required|numeric|min:0',
            'items_data.*.rate' => 'required|numeric|min:0',
            'items_data.*.amount' => 'required|numeric|min:0',
            'items_data.*.gst' => 'required|numeric|min:0',
            'items_data.*.total_amount' => 'required|numeric|min:0',
            'items_data.*.sort_order' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::transaction(function () use ($request, $id) {
                $dispatch = OrderDispatch::findOrFail($id);
                $itemsData = collect($request->items_data);

                // Restore old qty before update
                $oldItems = $dispatch->items;
                foreach ($oldItems as $oldItem) {
                    Item::where('id', $oldItem->item_id)->increment('stock_net_meter', $oldItem->meter);
                }

                $dispatch->update([
                    'dispatch_date' => $request->dispatch_date,
                    'dispatch_no' => $request->dispatch_no,
                    'bill_no' => $request->bill_no,
                    'customer_id' => $request->customer_id,
                    'mobile_number' => $request->mobile_number,
                    'transport' => $request->transport,
                    'status' => $request->status,
                    'total_meter' => $itemsData->sum(fn ($item) => (float) $item['meter']),
                    'total_amount' => $itemsData->sum(fn ($item) => (float) ($item['total_amount'] ?? $item['amount'] ?? 0)),
                ]);

                $dispatch->items()->delete();

                foreach ($itemsData as $itemRow) {
                    OrderDispatchItem::create([
                        'order_dispatch_id' => $dispatch->id,
                        'item_id' => $itemRow['item_id'],
                        'lot_no' => $itemRow['lot_no'],
                        'item_code' => $itemRow['item_code'] ?? null,
                        'meter' => $itemRow['meter'],
                        'rate' => $itemRow['rate'],
                        'amount' => $itemRow['amount'],
                        'gst' => $itemRow['gst'] ?? 0,
                        'total_amount' => $itemRow['total_amount'] ?? $itemRow['amount'],
                        'sort_order' => $itemRow['sort_order'],
                    ]);
                }
            });

            return redirect()->route('admin.orderdispatches.index')->with('success', 'Order dispatch updated successfully');
        } catch (\Exception $e) {
            dd($e);
            \Log::error('OrderDispatch Update Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong!')->withInput();
        }
    }

    public function delete($id)
    {
        try {
            OrderDispatch::findOrFail($id)->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('OrderDispatch Delete Error: ' . $e->getMessage());
            return response()->json(['success' => false]);
        }
    }
}
