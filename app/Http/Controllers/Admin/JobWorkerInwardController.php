<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\JobWorker;
use App\Models\JobWorkerInward;
use App\Models\JobWorkerInwardItem;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class JobWorkerInwardController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:inward-list', ['only' => ['index']]);
        $this->middleware('permission:inward-create', ['only' => ['create','store']]);
        $this->middleware('permission:inward-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:inward-delete', ['only' => ['destroy']]);
    }

    private function nextChNo(?int $ignoreId = null): string
    {
        if (! Schema::hasTable('job_worker_inwards')) {
            return 'CH0001';
        }

        $query = JobWorkerInward::withTrashed();

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        $max = $query->pluck('ch_no')
            ->map(fn ($value) => (int) preg_replace('/[^0-9]/', '', (string) $value))
            ->max() ?? 0;

        return 'CH' . str_pad((string) ($max + 1), 4, '0', STR_PAD_LEFT);
    }

    private function lotSources()
    {
        return PurchaseItem::with('item')->orderBy('lot_no')->get()->map(function ($row) {
            return [
                'purchase_item_id' => $row->id,
                'lot_no' => $row->lot_no,
                'item_id' => $row->item_id,
                'item_name' => $row->item?->item_name ?: '',
                'quality' => $row->quality ?: '',
                'meter' => (string) $row->qty_m,
                'fold' => (string) $row->fold,
                'total_meter' => (string) $row->net_meter,
            ];
        })->values();
    }

    public function index()
    {
        return view('admin.job_worker_inwards.index');
    }

    public function getAll(Request $request)
    {
        try {
            $query = JobWorkerInward::with('jobWorker')->latest();

            if ($request->filled('ch_no')) {
                $query->where('ch_no', 'like', '%' . $request->ch_no . '%');
            }

            $search = $request->filled('search_value') ? $request->search_value : $request->item_name;

            if ($request->filled('inward_date')) {
                $query->whereDate('inward_date', $request->inward_date);
            }

            if (! empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('ch_no', 'like', "%{$search}%")
                        ->orWhere('remark', 'like', "%{$search}%")
                        ->orWhereHas('jobWorker', function ($jobWorkerQuery) use ($search) {
                            $jobWorkerQuery->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('items.item', function ($itemQuery) use ($search) {
                            $itemQuery->where('item_name', 'like', "%{$search}%");
                        });
                });
            }

            return DataTables::of($query)
                ->addColumn('date', fn ($row) => optional($row->inward_date)->format('d/m/Y') ?: '-')
                ->addColumn('job_worker_name', fn ($row) => $row->jobWorker?->name ?: '-')
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('admin.jobworkerinwards.edit', $row->id) . '" class="btn btn-sm btn-primary">Edit</a>' .
                        ' <button class="btn btn-sm btn-danger deleteBtn" data-id="' . $row->id . '">Delete</button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        } catch (\Exception $e) {
            \Log::error('JobWorkerInward DataTable Error: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong']);
        }
    }

    public function create()
    {
        $inward = [
            'inward_date' => now()->format('Y-m-d'),
            'ch_no' => $this->nextChNo(),
            'job_worker_id' => '',
            'remark' => '',
        ];

        $jobWorkers = JobWorker::query()->orderBy('name')->get(['id', 'name']);
        $items = Item::query()->orderBy('item_name')->get(['id', 'item_name']);
        $lotSources = $this->lotSources();

        return view('admin.job_worker_inwards.create', compact('inward', 'jobWorkers', 'items', 'lotSources'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'inward_date' => 'required|date',
            'ch_no' => 'required|string|max:30|unique:job_worker_inwards,ch_no',
            'job_worker_id' => 'required|exists:job_workers,id',
            'remark' => 'nullable|string',
            'items_data' => 'required|array|min:1',
            'items_data.*.item_id' => 'required|exists:items,id',
            'items_data.*.lot_no' => 'required|string|max:100',
            'items_data.*.quality' => 'nullable|string|max:150',
            'items_data.*.meter' => 'required|numeric|min:0',
            'items_data.*.fold' => 'required|numeric|min:0',
            'items_data.*.total_meter' => 'required|numeric|min:0',
            'items_data.*.shrinkage' => 'nullable|string|max:50',
            'items_data.*.type' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::transaction(function () use ($request) {
                $rows = collect($request->items_data)->values();

                $inward = JobWorkerInward::create([
                    'inward_date' => $request->inward_date,
                    'ch_no' => $request->ch_no,
                    'job_worker_id' => $request->job_worker_id,
                    'total_meter' => $rows->sum(fn ($r) => (float) $r['meter']),
                    'total_net_meter' => $rows->sum(fn ($r) => (float) $r['total_meter']),
                    'remark' => $request->remark,
                ]);

                foreach ($rows as $index => $itemRow) {
                    JobWorkerInwardItem::create([
                        'job_worker_inward_id' => $inward->id,
                        'item_id' => $itemRow['item_id'],
                        'lot_no' => $itemRow['lot_no'],
                        'quality' => $itemRow['quality'] ?? null,
                        'meter' => $itemRow['meter'],
                        'fold' => $itemRow['fold'],
                        'total_meter' => $itemRow['total_meter'],
                        'shrinkage' => $itemRow['shrinkage'] ?? null,
                        'type' => $itemRow['type'] ?? null,
                    ]);

                    // Increase stock for the returned item
                    $item = Item::find($itemRow['item_id']);
                    if ($item) {
                        $item->increaseStock((float) $itemRow['meter'], (float) $itemRow['total_meter']);
                    }
                }
            });

            return redirect()->route('admin.jobworkerinwards.index')->with('success', 'Job worker inward created successfully');
        } catch (\Exception $e) {
            \Log::error('JobWorkerInward Store Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong')->withInput();
        }
    }

    public function edit($id)
    {
        try {
            $inwardRecord = JobWorkerInward::with(['items.item'])->findOrFail($id);

            $inward = [
                'id' => $inwardRecord->id,
                'inward_date' => optional($inwardRecord->inward_date)->format('Y-m-d'),
                'ch_no' => $inwardRecord->ch_no,
                'job_worker_id' => $inwardRecord->job_worker_id,
                'remark' => $inwardRecord->remark,
            ];

            $jobWorkers = JobWorker::query()->orderBy('name')->get(['id', 'name']);
            $items = Item::query()->orderBy('item_name')->get(['id', 'item_name']);
            $lotSources = $this->lotSources();
            $itemRows = $inwardRecord->items;

            return view('admin.job_worker_inwards.edit', compact('inward', 'jobWorkers', 'items', 'lotSources', 'itemRows'));
        } catch (\Exception $e) {
            \Log::error('JobWorkerInward Edit Error: ' . $e->getMessage());
            return redirect()->route('admin.jobworkerinwards.index')->with('error', 'Record not found');
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'inward_date' => 'required|date',
            'ch_no' => ['required', 'string', 'max:30', Rule::unique('job_worker_inwards', 'ch_no')->ignore($id)],
            'job_worker_id' => 'required|exists:job_workers,id',
            'remark' => 'nullable|string',
            'items_data' => 'required|array|min:1',
            'items_data.*.item_id' => 'required|exists:items,id',
            'items_data.*.lot_no' => 'required|string|max:100',
            'items_data.*.quality' => 'nullable|string|max:150',
            'items_data.*.meter' => 'required|numeric|min:0',
            'items_data.*.fold' => 'required|numeric|min:0',
            'items_data.*.total_meter' => 'required|numeric|min:0',
            'items_data.*.shrinkage' => 'nullable|string|max:50',
            'items_data.*.type' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::transaction(function () use ($request, $id) {
                $inward = JobWorkerInward::findOrFail($id);
                $rows = collect($request->items_data)->values();

                $inward->update([
                    'inward_date' => $request->inward_date,
                    'ch_no' => $request->ch_no,
                    'job_worker_id' => $request->job_worker_id,
                    'total_meter' => $rows->sum(fn ($r) => (float) $r['meter']),
                    'total_net_meter' => $rows->sum(fn ($r) => (float) $r['total_meter']),
                    'remark' => $request->remark,
                ]);

                $inward->items()->delete();

                foreach ($rows as $itemRow) {
                    JobWorkerInwardItem::create([
                        'job_worker_inward_id' => $inward->id,
                        'item_id' => $itemRow['item_id'],
                        'lot_no' => $itemRow['lot_no'],
                        'quality' => $itemRow['quality'] ?? null,
                        'meter' => $itemRow['meter'],
                        'fold' => $itemRow['fold'],
                        'total_meter' => $itemRow['total_meter'],
                        'shrinkage' => $itemRow['shrinkage'] ?? null,
                        'type' => $itemRow['type'] ?? null,
                    ]);
                }
            });

            return redirect()->route('admin.jobworkerinwards.index')->with('success', 'Job worker inward updated successfully');
        } catch (\Exception $e) {
            \Log::error('JobWorkerInward Update Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong')->withInput();
        }
    }

    public function delete($id)
    {
        try {
            JobWorkerInward::findOrFail($id)->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('JobWorkerInward Delete Error: ' . $e->getMessage());
            return response()->json(['success' => false]);
        }
    }
}
