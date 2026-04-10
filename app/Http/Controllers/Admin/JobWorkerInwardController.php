<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\JobWorkAssignmentItem;
use App\Models\JobWorker;
use App\Models\JobWorkerInward;
use App\Models\JobWorkerInwardItem;
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
        $hasSourceLotNo = Schema::hasColumn('job_worker_inward_items', 'source_lot_no');

        $inwardedMeterByKey = JobWorkerInwardItem::query()
            ->with(['inward:id,job_worker_id'])
            ->get()
            ->filter(function (JobWorkerInwardItem $row) {
                return ! empty($row->inward?->job_worker_id) && ! empty($row->item_id);
            })
            ->groupBy(function (JobWorkerInwardItem $row) use ($hasSourceLotNo) {
                $sourceLotNo = $hasSourceLotNo
                    ? ((string) ($row->source_lot_no ?: $row->lot_no))
                    : (string) $row->lot_no;

                return implode('|', [
                    (string) $row->inward->job_worker_id,
                    (string) $row->item_id,
                    $sourceLotNo,
                ]);
            })
            ->map(function ($rows) {
                return (float) $rows->sum(fn (JobWorkerInwardItem $row) => (float) $row->meter);
            });

        return JobWorkAssignmentItem::query()
            ->with([
                'assignment:id,job_worker_id',
                'item:id,item_name',
                'processItem:id,item_name',
            ])
            ->orderBy('lot_no')
            ->orderBy('id')
            ->get()
            ->map(function (JobWorkAssignmentItem $row) use ($inwardedMeterByKey) {
                $processItemId = is_numeric((string) $row->process) ? (int) $row->process : null;
                $itemId = $processItemId ?: $row->item_id;
                $itemName = $processItemId
                    ? ($row->processItem?->item_name ?: '')
                    : ($row->item?->item_name ?: '');
                $jobWorkerId = $row->assignment?->job_worker_id;
                $sourceLotNo = (string) ($row->lot_no ?? '');
                $assignedMeter = (float) $row->meter;
                $inwardedMeterKey = implode('|', [(string) $jobWorkerId, (string) $itemId, $sourceLotNo]);
                $inwardedMeter = (float) ($inwardedMeterByKey[$inwardedMeterKey] ?? 0);
                $remainingMeter = max($assignedMeter - $inwardedMeter, 0);

                return [
                    'assignment_item_id' => $row->id,
                    'job_worker_id' => $jobWorkerId,
                    'lot_no' => $row->lot_no,
                    'source_lot_no' => $sourceLotNo,
                    'item_id' => $itemId,
                    'item_name' => $itemName ?: ($row->item?->item_name ?: ''),
                    'quality' => ($row->quality ?? $row->colour ?? '') ?: '',
                    'meter' => (string) $row->meter,
                    'fold' => (string) $row->fold,
                    'total_meter' => (string) $row->net_meter,
                    'process' => (string) $row->process,
                    'assigned_meter' => number_format($assignedMeter, 2, '.', ''),
                    'inwarded_meter' => number_format($inwardedMeter, 2, '.', ''),
                    'remaining_meter' => number_format($remainingMeter, 2, '.', ''),
                ];
            })
            ->filter(function (array $row) {
                return ! empty($row['job_worker_id'])
                    && ! empty($row['item_id'])
                    && ! empty($row['lot_no'])
                    && (float) ($row['remaining_meter'] ?? 0) > 0;
            })
            ->values();
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

        $jobWorkers = JobWorker::query()->orderBy('name')->get(['id', 'name', 'abbr']);
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
            'items_data.*.source_lot_no' => 'nullable|string|max:100',
            'items_data.*.quality' => 'nullable|string|max:150',
            'items_data.*.meter' => 'required|numeric|min:0',
            'items_data.*.fold' => 'required|numeric|min:0',
            'items_data.*.total_meter' => 'required|numeric|min:0',
            'items_data.*.shrinkage' => 'nullable|string|max:50',
            'items_data.*.type' => 'nullable|string|max:50',
            'items_data.*.after_shrinkage_meter' => 'nullable|string|max:50',
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
                        'source_lot_no' => $itemRow['source_lot_no'] ?? $itemRow['lot_no'],
                        'quality' => $itemRow['quality'] ?? null,
                        'meter' => $itemRow['meter'],
                        'fold' => $itemRow['fold'],
                        'total_meter' => $itemRow['total_meter'],
                        'shrinkage' => $itemRow['shrinkage'] ?? null,
                        'type' => $itemRow['type'] ?? null,
                        'after_shrinkage_meter' => $itemRow['after_shrinkage_meter'] ?? null,
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

            $jobWorkers = JobWorker::query()->orderBy('name')->get(['id', 'name', 'abbr']);
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
            'items_data.*.source_lot_no' => 'nullable|string|max:100',
            'items_data.*.quality' => 'nullable|string|max:150',
            'items_data.*.meter' => 'required|numeric|min:0',
            'items_data.*.fold' => 'required|numeric|min:0',
            'items_data.*.total_meter' => 'required|numeric|min:0',
            'items_data.*.shrinkage' => 'nullable|string|max:50',
            'items_data.*.type' => 'nullable|string|max:50',
            'items_data.*.after_shrinkage_meter' => 'nullable|string|max:50',
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
                        'source_lot_no' => $itemRow['source_lot_no'] ?? $itemRow['lot_no'],
                        'quality' => $itemRow['quality'] ?? null,
                        'meter' => $itemRow['meter'],
                        'fold' => $itemRow['fold'],
                        'total_meter' => $itemRow['total_meter'],
                        'shrinkage' => $itemRow['shrinkage'] ?? null,
                        'type' => $itemRow['type'] ?? null,
                        'after_shrinkage_meter' => $itemRow['after_shrinkage_meter'] ?? null,
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
