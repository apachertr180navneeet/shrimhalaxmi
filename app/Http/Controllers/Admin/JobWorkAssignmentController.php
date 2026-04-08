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
    public function __construct()
    {
        $this->middleware('permission:jobassign-list', ['only' => ['index']]);
        $this->middleware('permission:jobassign-create', ['only' => ['create','store']]);
        $this->middleware('permission:jobassign-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:jobassign-delete', ['only' => ['destroy']]);
    }


    /**
     * Generate the next assignment number (assign_no) for job work assignments.
     * This does not depend on auto-increment IDs, but finds the max assign_no and increments it.
     *
     * @param int|null $ignoreId Optionally ignore a specific assignment ID (for updates)
     * @return string The next assignment number, zero-padded to 4 digits
     */
    private function nextAssignNo(?int $ignoreId = null): string
    {
        // If the table does not exist, return the initial number
        if (! Schema::hasTable('job_work_assignments')) {
            return '0001';
        }

        $query = JobWorkAssignment::withTrashed();

        // Ignore a specific ID if provided (useful for updates)
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        // Extract numeric part of assign_no, find max, increment
        $max = $query->pluck('assign_no')
            ->map(fn ($assignNo) => (int) preg_replace('/\D/', '', (string) $assignNo))
            ->max() ?? 0;

        return str_pad((string) ($max + 1), 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get all active process options for dropdowns.
     *
     * @return \Illuminate\Support\Collection List of active processes (id, item_id, name)
     */
    private function processOptions()
    {
        return Process::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'item_id', 'name']);
    }

    /**
     * Resolve a process name by its ID.
     *
     * @param int|string $processId
     * @return string Process name or empty string if not found
     */
    private function resolveProcessName($processId): string
    {
        return Process::query()->whereKey($processId)->value('name') ?: '';
    }

    /**
     * Get default values for a new job work assignment form.
     *
     * @return array
     */
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

    /**
     * Get all lot sources from purchase items, including related item and purchase info.
     * Used to populate assignment rows.
     *
     * @return \Illuminate\Support\Collection
     */
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

    /**
     * Show the job work assignments index page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin.job_work_assignments.index');
    }

    /**
     * Get all job work assignments for DataTables AJAX request.
     * Supports searching and filtering.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll(Request $request)
    {
        try {
            $query = JobWorkAssignment::with(['jobWorker', 'items'])->latest();

            // Filter by assignment number if provided
            if ($request->filled('assign_no')) {
                $query->where('assign_no', 'like', '%' . $request->assign_no . '%');
            }

            // General search across multiple fields and relations
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

            // Format columns for DataTables
            return DataTables::of($query)
                ->addColumn('date', fn ($row) => optional($row->assignment_date)->format('d/m/Y') ?: '-')
                ->addColumn('job_worker_name', fn ($row) => $row->jobWorker?->name ?: '-')
                ->addColumn('lr_no', fn ($row) => optional($row->items->first())->lr_no ?: '-')
                ->addColumn('process', function ($row) {
                    $processes = $row->items->pluck('process')->filter()->unique()->values();
                    return $processes->isEmpty() ? '-' : e($processes->join(', '));
                })
                ->addColumn('action', function ($row) {
                    // Action buttons for edit and delete
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

    /**
     * Show the create job work assignment form.
     *
     * @return \Illuminate\View\View
     */
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

    /**
     * Store a new job work assignment and its items.
     * Validates input and saves assignment and related items in a transaction.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
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
            'items_data.*.color' => 'nullable|string|max:150',
            'items_data.*.meter' => 'required|numeric|min:0',
            'items_data.*.fold' => 'required|numeric|min:0',
            'items_data.*.net_meter' => 'required|numeric|min:0',
            'items_data.*.process_id' => 'required',
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

                // Create the assignment
                $assignment = JobWorkAssignment::create([
                    'assignment_date' => $request->date,
                    'assign_no' => $request->assign_no,
                    'job_worker_id' => $request->job_worker_id,
                    'freight' => $request->freight,
                    'remark' => $request->remark,
                    'total_meter' => $itemsData->sum(fn ($item) => (float) $item['meter']),
                    'total_net_meter' => $itemsData->sum(fn ($item) => (float) $item['net_meter']),
                ]);

                // Create assignment items
                foreach ($itemsData as $itemRow) {
                    JobWorkAssignmentItem::create([
                        'job_work_assignment_id' => $assignment->id,
                        'purchase_item_id' => $itemRow['purchase_item_id'] ?? null,
                        'item_id' => $itemRow['item_id'],
                        'sort_order' => $itemRow['sort_order'],
                        'lot_no' => $itemRow['lot_no'],
                        'color' => $itemRow['color'] ?? null,
                        'meter' => $itemRow['meter'],
                        'fold' => $itemRow['fold'],
                        'net_meter' => $itemRow['net_meter'],
                        'process' => $itemRow['process_id'],
                        'lr_no' => $itemRow['lr_no'] ?? null,
                        'transport' => $itemRow['transport'] ?? null,
                    ]);

                    // Decrease stock for the assigned item
                    $item = Item::find($itemRow['item_id']);
                    if ($item) {
                        $item->decreaseStock((float) $itemRow['meter'], (float) $itemRow['net_meter']);
                    }
                }
            });

            return redirect()->route('admin.jobworkassignments.index')->with('success', 'Job work assignment added successfully');
        } catch (\Exception $e) {
            \Log::error('Job Work Assignment Store Error: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Something went wrong!')->withInput();
        }
    }

    /**
     * Show the edit form for a job work assignment.
     * Loads assignment, items, and related dropdown data.
     *
     * @param int $id Assignment ID
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
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

    /**
     * Update an existing job work assignment and its items.
     * Validates input and updates assignment and related items in a transaction.
     *
     * @param Request $request
     * @param int $id Assignment ID
     * @return \Illuminate\Http\RedirectResponse
     */
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
            'items_data.*.process_id' => 'required',
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

                // Update assignment
                $assignment->update([
                    'assignment_date' => $request->date,
                    'assign_no' => $request->assign_no,
                    'job_worker_id' => $request->job_worker_id,
                    'freight' => $request->freight,
                    'remark' => $request->remark,
                    'total_meter' => $itemsData->sum(fn ($item) => (float) $item['meter']),
                    'total_net_meter' => $itemsData->sum(fn ($item) => (float) $item['net_meter']),
                ]);

                // Remove old assignment items
                $assignment->items()->delete();

                // Create new assignment items
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
                        'process' => $itemRow['process_id'],
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

    /**
     * Delete a job work assignment by ID (soft delete).
     *
     * @param int $id Assignment ID
     * @return \Illuminate\Http\JsonResponse
     */
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
