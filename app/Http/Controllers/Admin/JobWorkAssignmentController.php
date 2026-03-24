<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\JobWorkAssignment;
use App\Models\JobWorkAssignmentItem;
use App\Models\JobWorker;
use App\Models\Process;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class JobWorkAssignmentController extends Controller
{
    // Build the next running assignment number without depending on auto-increment ids.
    private function nextAssignNo(?int $ignoreId = null): string
    {
        if (! Schema::hasTable('job_work_assignments')) {
            return '0001';
        }

        $query = JobWorkAssignment::withTrashed();

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        $max = $query->pluck('assign_no')
            ->map(fn ($assignNo) => (int) preg_replace('/\D/', '', (string) $assignNo))
            ->max() ?? 0;

        return str_pad((string) ($max + 1), 4, '0', STR_PAD_LEFT);
    }

    // Process dropdown is global for this screen, so load active processes once.
    private function processOptions()
    {
        return Process::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'item_id', 'name']);
    }

    private function resolveProcessName($processId): string
    {
        return Process::query()->whereKey($processId)->value('name') ?: '';
    }

    private function assignmentDefaults(): array
    {
        return [
            'date' => now()->format('Y-m-d'),
            'assign_no' => $this->nextAssignNo(),
            'job_worker_id' => '',
            'freight' => '',
            'remark' => '',
        ];
    }

    // Assignment rows are created from purchase item data keyed by lot number.
    private function lotSources()
    {
        return PurchaseItem::query()
            ->with(['item:id,item_name', 'purchase:id,purchase_date'])
            ->orderBy('lot_no')
            ->orderBy('id')
            ->get()
            ->map(function (PurchaseItem $row) {
                return [
                    'purchase_item_id' => $row->id,
                    'lot_no' => $row->lot_no,
                    'item_id' => $row->item_id,
                    'item_name' => $row->item?->item_name ?: 'Unknown Item',
                    'quality' => $row->quality ?: '',
                    'meter' => (string) $row->qty_m,
                    'fold' => (string) $row->fold,
                    'net_meter' => (string) $row->net_meter,
                    'transport' => $row->transport ?: '',
                    'lr_no' => $row->lr_no ?: '',
                    'purchase_date' => optional($row->purchase?->purchase_date)->format('d/m/Y'),
                ];
            })
            ->values();
    }

    public function index()
    {
        return view('admin.job_work_assignments.index');
    }

    public function getAll(Request $request)
    {
        try {
            $query = JobWorkAssignment::with(['jobWorker', 'items'])->latest();

            if ($request->filled('assign_no')) {
                $query->where('assign_no', 'like', '%' . $request->assign_no . '%');
            }

            if ($request->filled('search_value')) {
                $search = $request->search_value;

                $query->where(function ($q) use ($search) {
                    $q->where('assign_no', 'like', "%{$search}%")
                        ->orWhere('freight', 'like', "%{$search}%")
                        ->orWhereHas('jobWorker', function ($jobWorkerQuery) use ($search) {
                            $jobWorkerQuery->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('items', function ($itemQuery) use ($search) {
                            $itemQuery->where('lot_no', 'like', "%{$search}%")
                                ->orWhere('process', 'like', "%{$search}%")
                                ->orWhere('lr_no', 'like', "%{$search}%")
                                ->orWhere('transport', 'like', "%{$search}%");
                        });
                });
            }

            return DataTables::of($query)
                ->addColumn('date', fn ($row) => optional($row->assignment_date)->format('d/m/Y') ?: '-')
                ->addColumn('job_worker_name', fn ($row) => $row->jobWorker?->name ?: '-')
                ->addColumn('lr_no', fn ($row) => optional($row->items->first())->lr_no ?: '-')
                ->addColumn('process', function ($row) {
                    $processes = $row->items->pluck('process')->filter()->unique()->values();
                    return $processes->isEmpty() ? '-' : e($processes->join(', '));
                })
                ->addColumn('action', function ($row) {
                    return '
                        <a href="' . route('admin.jobworkassignments.edit', $row->id) . '" class="btn btn-sm btn-primary">Edit</a>
                        <button class="btn btn-sm btn-danger deleteBtn" data-id="' . $row->id . '">Delete</button>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        } catch (\Exception $e) {
            \Log::error('Job Work Assignment DataTable Error: ' . $e->getMessage());

            return response()->json(['error' => 'Something went wrong!']);
        }
    }

    public function create()
    {
        $assignment = $this->assignmentDefaults();
        $jobWorkers = JobWorker::query()->orderBy('name')->get(['id', 'name']);
        $items = Item::query()->orderBy('item_name')->get(['id', 'item_name']);
        $lotSources = $this->lotSources();
        $assignmentItems = collect();
        $processOptions = $this->processOptions();

        return view('admin.job_work_assignments.create', compact(
            'assignment',
            'jobWorkers',
            'items',
            'lotSources',
            'assignmentItems',
            'processOptions'
        ));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'assign_no' => 'required|string|max:30|unique:job_work_assignments,assign_no',
            'job_worker_id' => 'required|exists:job_workers,id',
            'freight' => 'nullable|string|max:100',
            'remark' => 'nullable|string',
            'items_data' => 'required|array|min:1',
            'items_data.*.purchase_item_id' => 'nullable|exists:purchase_items,id',
            'items_data.*.item_id' => 'required|exists:items,id',
            'items_data.*.lot_no' => 'required|string|max:100',
            'items_data.*.quality' => 'nullable|string|max:150',
            'items_data.*.meter' => 'required|numeric|min:0',
            'items_data.*.fold' => 'required|numeric|min:0',
            'items_data.*.net_meter' => 'required|numeric|min:0',
            'items_data.*.process_id' => 'required|exists:processes,id',
            'items_data.*.lr_no' => 'nullable|string|max:100',
            'items_data.*.transport' => 'nullable|string|max:150',
            'items_data.*.sort_order' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::transaction(function () use ($request) {
                $itemsData = collect($request->items_data)->values();

                $assignment = JobWorkAssignment::create([
                    'assignment_date' => $request->date,
                    'assign_no' => $request->assign_no,
                    'job_worker_id' => $request->job_worker_id,
                    'freight' => $request->freight,
                    'remark' => $request->remark,
                    'total_meter' => $itemsData->sum(fn ($item) => (float) $item['meter']),
                    'total_net_meter' => $itemsData->sum(fn ($item) => (float) $item['net_meter']),
                ]);

                foreach ($itemsData as $itemRow) {
                    JobWorkAssignmentItem::create([
                        'job_work_assignment_id' => $assignment->id,
                        'purchase_item_id' => $itemRow['purchase_item_id'] ?? null,
                        'item_id' => $itemRow['item_id'],
                        'sort_order' => $itemRow['sort_order'],
                        'lot_no' => $itemRow['lot_no'],
                        'quality' => $itemRow['quality'] ?? null,
                        'meter' => $itemRow['meter'],
                        'fold' => $itemRow['fold'],
                        'net_meter' => $itemRow['net_meter'],
                        'process' => $this->resolveProcessName($itemRow['process_id']),
                        'lr_no' => $itemRow['lr_no'] ?? null,
                        'transport' => $itemRow['transport'] ?? null,
                    ]);
                }
            });

            return redirect()->route('admin.jobworkassignments.index')->with('success', 'Job work assignment added successfully');
        } catch (\Exception $e) {
            \Log::error('Job Work Assignment Store Error: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Something went wrong!')->withInput();
        }
    }

    public function edit($id)
    {
        try {
            $assignmentRecord = JobWorkAssignment::with(['items.item'])->findOrFail($id);

            $assignment = [
                'id' => $assignmentRecord->id,
                'date' => optional($assignmentRecord->assignment_date)->format('Y-m-d'),
                'assign_no' => $assignmentRecord->assign_no,
                'job_worker_id' => $assignmentRecord->job_worker_id,
                'freight' => $assignmentRecord->freight,
                'remark' => $assignmentRecord->remark,
            ];

            $jobWorkers = JobWorker::query()->orderBy('name')->get(['id', 'name']);
            $items = Item::query()->orderBy('item_name')->get(['id', 'item_name']);
            $lotSources = $this->lotSources();
            $assignmentItems = $assignmentRecord->items()->with('item')->orderBy('sort_order')->get();
            $processOptions = $this->processOptions();

            return view('admin.job_work_assignments.edit', compact(
                'assignment',
                'jobWorkers',
                'items',
                'lotSources',
                'assignmentItems',
                'processOptions'
            ));
        } catch (\Exception $e) {
            \Log::error('Job Work Assignment Edit Error: ' . $e->getMessage());

            return redirect()->route('admin.jobworkassignments.index')->with('error', 'Record not found');
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'assign_no' => ['required', 'string', 'max:30', Rule::unique('job_work_assignments', 'assign_no')->ignore($id)],
            'job_worker_id' => 'required|exists:job_workers,id',
            'freight' => 'nullable|string|max:100',
            'remark' => 'nullable|string',
            'items_data' => 'required|array|min:1',
            'items_data.*.purchase_item_id' => 'nullable|exists:purchase_items,id',
            'items_data.*.item_id' => 'required|exists:items,id',
            'items_data.*.lot_no' => 'required|string|max:100',
            'items_data.*.quality' => 'nullable|string|max:150',
            'items_data.*.meter' => 'required|numeric|min:0',
            'items_data.*.fold' => 'required|numeric|min:0',
            'items_data.*.net_meter' => 'required|numeric|min:0',
            'items_data.*.process_id' => 'required|exists:processes,id',
            'items_data.*.lr_no' => 'nullable|string|max:100',
            'items_data.*.transport' => 'nullable|string|max:150',
            'items_data.*.sort_order' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::transaction(function () use ($request, $id) {
                $assignment = JobWorkAssignment::findOrFail($id);
                $itemsData = collect($request->items_data)->values();

                $assignment->update([
                    'assignment_date' => $request->date,
                    'assign_no' => $request->assign_no,
                    'job_worker_id' => $request->job_worker_id,
                    'freight' => $request->freight,
                    'remark' => $request->remark,
                    'total_meter' => $itemsData->sum(fn ($item) => (float) $item['meter']),
                    'total_net_meter' => $itemsData->sum(fn ($item) => (float) $item['net_meter']),
                ]);

                $assignment->items()->delete();

                foreach ($itemsData as $itemRow) {
                    JobWorkAssignmentItem::create([
                        'job_work_assignment_id' => $assignment->id,
                        'purchase_item_id' => $itemRow['purchase_item_id'] ?? null,
                        'item_id' => $itemRow['item_id'],
                        'sort_order' => $itemRow['sort_order'],
                        'lot_no' => $itemRow['lot_no'],
                        'quality' => $itemRow['quality'] ?? null,
                        'meter' => $itemRow['meter'],
                        'fold' => $itemRow['fold'],
                        'net_meter' => $itemRow['net_meter'],
                        'process' => $this->resolveProcessName($itemRow['process_id']),
                        'lr_no' => $itemRow['lr_no'] ?? null,
                        'transport' => $itemRow['transport'] ?? null,
                    ]);
                }
            });

            return redirect()->route('admin.jobworkassignments.index')->with('success', 'Job work assignment updated successfully');
        } catch (\Exception $e) {
            \Log::error('Job Work Assignment Update Error: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Something went wrong!')->withInput();
        }
    }

    public function delete($id)
    {
        try {
            JobWorkAssignment::findOrFail($id)->delete();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Job Work Assignment Delete Error: ' . $e->getMessage());

            return response()->json(['success' => false]);
        }
    }
}
