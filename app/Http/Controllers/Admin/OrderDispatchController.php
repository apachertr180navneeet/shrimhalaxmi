<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Item;
use App\Models\OrderDispatch;
use App\Models\OrderDispatchItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class OrderDispatchController extends Controller
{
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
                ->addColumn('action', fn ($row) => '<a href="' . route('admin.orderdispatches.edit', $row->id) . '" class="btn btn-sm btn-primary">Edit</a>   <button class="btn btn-sm btn-danger deleteBtn" data-id="' . $row->id . '">Delete</button>')
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
        $items = Item::orderBy('item_name')->get(['id', 'item_name', 'abbr']);
        $dispatchItems = collect();

        return view('admin.order_dispatches.create', compact('dispatch', 'customers', 'items', 'dispatchItems'));
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
                    'total_amount' => $itemsData->sum(fn ($item) => (float) $item['amount']),
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
                        'sort_order' => $itemRow['sort_order'],
                    ]);
                }
            });

            return redirect()->route('admin.orderdispatches.index')->with('success', 'Order dispatch added successfully');
        } catch (\Exception $e) {
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
            $dispatchItems = $dispatchRecord->items()->orderBy('sort_order')->get();

            return view('admin.order_dispatches.edit', compact('dispatch', 'customers', 'items', 'dispatchItems'));
        } catch (\Exception $e) {
            \Log::error('OrderDispatch Edit Error: ' . $e->getMessage());
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
            'items_data.*.sort_order' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::transaction(function () use ($request, $id) {
                $dispatch = OrderDispatch::findOrFail($id);
                $itemsData = collect($request->items_data);

                $dispatch->update([
                    'dispatch_date' => $request->dispatch_date,
                    'dispatch_no' => $request->dispatch_no,
                    'bill_no' => $request->bill_no,
                    'customer_id' => $request->customer_id,
                    'mobile_number' => $request->mobile_number,
                    'transport' => $request->transport,
                    'status' => $request->status,
                    'total_meter' => $itemsData->sum(fn ($item) => (float) $item['meter']),
                    'total_amount' => $itemsData->sum(fn ($item) => (float) $item['amount']),
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
                        'sort_order' => $itemRow['sort_order'],
                    ]);
                }
            });

            return redirect()->route('admin.orderdispatches.index')->with('success', 'Order dispatch updated successfully');
        } catch (\Exception $e) {
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
