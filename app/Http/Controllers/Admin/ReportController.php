<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\JobWorkAssignmentItem;
use App\Models\JobWorkerInwardItem;
use App\Models\JobWorker;
use App\Models\OrderDispatchItem;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function slipBook(Request $request)
    {
        $query = JobWorkAssignmentItem::query()
            ->with([
                'assignment:id,assignment_date,assign_no,job_worker_id',
                'assignment.jobWorker:id,name',
                'item:id,item_name',
                'processItem:id,item_name',
            ])
            ->whereHas('assignment')
            ->orderByRaw('(select assignment_date from job_work_assignments where job_work_assignments.id = job_work_assignment_items.job_work_assignment_id) asc')
            ->orderByRaw('(select assign_no from job_work_assignments where job_work_assignments.id = job_work_assignment_items.job_work_assignment_id) asc')
            ->orderBy('sort_order');

        if ($request->filled('date_from')) {
            $query->whereHas('assignment', function ($q) use ($request) {
                $q->whereDate('assignment_date', '>=', $request->date_from);
            });
        }

        if ($request->filled('date_to')) {
            $query->whereHas('assignment', function ($q) use ($request) {
                $q->whereDate('assignment_date', '<=', $request->date_to);
            });
        }

        if ($request->filled('job_worker_id')) {
            $query->whereHas('assignment', function ($q) use ($request) {
                $q->where('job_worker_id', $request->job_worker_id);
            });
        }

        $rows = $query->get();

        $jobWorkers = JobWorker::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.reports.slip_book', compact('rows', 'jobWorkers'));
    }

    public function netFabricBalance(Request $request)
    {
        $assignmentRows = JobWorkAssignmentItem::query()
            ->with([
                'assignment:id,job_worker_id',
                'item:id,item_name',
                'processItem:id,item_name',
            ])
            ->whereHas('assignment')
            ->get();

        $assignedByKey = $assignmentRows
            ->map(function (JobWorkAssignmentItem $row) {
                $processItemId = is_numeric((string) $row->process) ? (int) $row->process : null;
                $itemId = $processItemId ?: (int) $row->item_id;
                $itemName = $processItemId
                    ? (string) ($row->processItem?->item_name ?: '')
                    : (string) ($row->item?->item_name ?: '');

                return [
                    'job_worker_id' => (int) ($row->assignment?->job_worker_id ?? 0),
                    'item_id' => $itemId,
                    'item_name' => $itemName !== '' ? $itemName : 'Unknown Quality',
                    'meter' => (float) $row->meter,
                ];
            })
            ->filter(fn (array $r) => $r['job_worker_id'] > 0 && $r['item_id'] > 0)
            ->groupBy(fn (array $r) => $r['job_worker_id'] . '|' . $r['item_id'])
            ->map(function ($rows) {
                $first = $rows->first();
                return [
                    'job_worker_id' => $first['job_worker_id'],
                    'item_id' => $first['item_id'],
                    'item_name' => $first['item_name'],
                    'assigned_meter' => (float) collect($rows)->sum('meter'),
                ];
            });

        $inwardByKey = JobWorkerInwardItem::query()
            ->with('inward:id,job_worker_id')
            ->whereHas('inward')
            ->get()
            ->groupBy(function (JobWorkerInwardItem $row) {
                $jobWorkerId = (int) ($row->inward?->job_worker_id ?? 0);
                return $jobWorkerId . '|' . (int) $row->item_id;
            })
            ->map(fn ($rows) => (float) $rows->sum('meter'));

        $workerNameMap = JobWorker::query()
            ->pluck('name', 'id');

        $rows = $assignedByKey
            ->map(function (array $row) use ($inwardByKey, $workerNameMap) {
                $key = $row['job_worker_id'] . '|' . $row['item_id'];
                $inwardMeter = (float) ($inwardByKey[$key] ?? 0);
                $balance = $row['assigned_meter'] - $inwardMeter;

                return [
                    'job_worker_name' => (string) ($workerNameMap[$row['job_worker_id']] ?? 'Unknown Job Worker'),
                    'quality' => $row['item_name'],
                    'stock_mtrs' => max($balance, 0),
                ];
            })
            ->filter(fn (array $r) => $r['stock_mtrs'] > 0.0001)
            ->sort(function (array $a, array $b) {
                $byWorker = strcmp($a['job_worker_name'], $b['job_worker_name']);
                if ($byWorker !== 0) {
                    return $byWorker;
                }
                return strcmp($a['quality'], $b['quality']);
            })
            ->values();

        return view('admin.reports.net_fabric_balance', compact('rows'));
    }

    public function greyLotBalance(Request $request)
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

        $assignedByPurchaseItem = JobWorkAssignmentItem::query()
            ->whereNotNull('purchase_item_id')
            ->get()
            ->groupBy('purchase_item_id')
            ->map(fn ($rows) => (float) $rows->sum('meter'));

        $dispatchByLotAndItem = OrderDispatchItem::query()
            ->get()
            ->groupBy(fn (OrderDispatchItem $row) => trim((string) $row->lot_no) . '|' . (int) $row->item_id)
            ->map(fn ($rows) => (float) $rows->sum('meter'));

        $rows = $purchaseRows
            ->map(function (PurchaseItem $row) use ($assignedByPurchaseItem, $dispatchByLotAndItem) {
                $purchaseDate = optional($row->purchase?->purchase_date);
                $assigned = (float) ($assignedByPurchaseItem[$row->id] ?? 0);
                $dispatchKey = trim((string) $row->lot_no) . '|' . (int) $row->item_id;
                $dispatchedDirect = (float) ($dispatchByLotAndItem[$dispatchKey] ?? 0);
                $balance = (float) $row->qty_m - $assigned - $dispatchedDirect;

                return [
                    'date' => $purchaseDate?->format('d.m.y') ?: '-',
                    'sort_date' => $purchaseDate?->format('Y-m-d') ?: '0000-00-00',
                    'supplier_name' => (string) ($row->purchase?->vendor?->vendor_name ?? '-'),
                    'bill_no' => (string) ($row->purchase?->bno ?? '-'),
                    'lot_no' => (string) ($row->lot_no ?? '-'),
                    'quality' => (string) ($row->item?->item_name ?? 'Unknown Quality'),
                    'quantity' => max($balance, 0),
                    'lr_number' => (string) ($row->lr_no ?: '-'),
                    'transport' => (string) ($row->transport ?: '-'),
                ];
            })
            ->filter(fn (array $row) => $row['quantity'] > 0.0001)
            ->sort(function (array $a, array $b) {
                $dateSort = strcmp($a['sort_date'], $b['sort_date']);
                if ($dateSort !== 0) {
                    return $dateSort;
                }
                return strcmp($a['lot_no'], $b['lot_no']);
            })
            ->values();

        return view('admin.reports.grey_lot_balance', compact('rows'));
    }

    public function finishedGoodsLotWise(Request $request)
    {
        $dispatchByLotAndItem = OrderDispatchItem::query()
            ->get()
            ->groupBy(fn (OrderDispatchItem $row) => trim((string) $row->lot_no) . '|' . (int) $row->item_id)
            ->map(fn ($rows) => (float) $rows->sum('meter'));

        $rows = JobWorkerInwardItem::query()
            ->with([
                'inward:id,inward_date,job_worker_id',
                'inward.jobWorker:id,name',
                'item:id,item_name',
            ])
            ->whereHas('inward')
            ->get()
            ->map(function (JobWorkerInwardItem $row) use ($dispatchByLotAndItem) {
                $inwardDate = optional($row->inward?->inward_date);
                $dispatchKey = trim((string) $row->lot_no) . '|' . (int) $row->item_id;
                $dispatched = (float) ($dispatchByLotAndItem[$dispatchKey] ?? 0);
                $balance = (float) $row->meter - $dispatched;

                return [
                    'date' => $inwardDate?->format('d.m.y') ?: '-',
                    'sort_date' => $inwardDate?->format('Y-m-d') ?: '0000-00-00',
                    'lot_no' => (string) ($row->lot_no ?? '-'),
                    'quality' => (string) ($row->item?->item_name ?? 'Unknown Quality'),
                    'qty' => max($balance, 0),
                    'printed_dyed' => (string) ($row->type ?: '-'),
                    'design_no' => (string) ($row->quality ?: '-'),
                    'supplier' => (string) ($row->inward?->jobWorker?->name ?? '-'),
                ];
            })
            ->filter(fn (array $row) => $row['qty'] > 0.0001)
            ->sort(function (array $a, array $b) {
                $dateSort = strcmp($a['sort_date'], $b['sort_date']);
                if ($dateSort !== 0) {
                    return $dateSort;
                }
                return strcmp($a['lot_no'], $b['lot_no']);
            })
            ->values();

        return view('admin.reports.finished_goods_lot_wise', compact('rows'));
    }

    public function issuedChalaanBook(Request $request)
    {
        $rows = JobWorkAssignmentItem::query()
            ->with([
                'assignment:id,assignment_date,assign_no,job_worker_id',
                'assignment.jobWorker:id,name',
                'item:id,item_name',
                'purchaseItem:id,lot_no',
            ])
            ->whereHas('assignment')
            ->orderByRaw('(select assignment_date from job_work_assignments where job_work_assignments.id = job_work_assignment_items.job_work_assignment_id) asc')
            ->orderByRaw('(select assign_no from job_work_assignments where job_work_assignments.id = job_work_assignment_items.job_work_assignment_id) asc')
            ->orderBy('sort_order')
            ->get()
            ->map(function (JobWorkAssignmentItem $row) {
                $assignmentDate = optional($row->assignment?->assignment_date);
                $rawProcess = trim((string) $row->process);
                $itemStage = (! is_numeric($rawProcess) && $rawProcess !== '') ? strtoupper($rawProcess) : 'GREY';

                return [
                    'date' => $assignmentDate?->format('d.m.Y') ?: '-',
                    'ch_no' => (string) ($row->assignment?->assign_no ?? '-'),
                    'job_worker' => (string) ($row->assignment?->jobWorker?->name ?? '-'),
                    'transport' => (string) ($row->transport ?: '-'),
                    'lr_number' => (string) ($row->lr_no ?: '-'),
                    'quality' => (string) ($row->item?->item_name ?? 'Unknown Quality'),
                    'item_stage' => $itemStage,
                    'quantity' => (float) $row->meter,
                    'vendor_lot_no' => (string) ($row->purchaseItem?->lot_no ?: $row->lot_no ?: '-'),
                ];
            })
            ->values();

        return view('admin.reports.issued_chalaan_book', compact('rows'));
    }

    public function listReport(Request $request)
    {
        $jobWorkers = JobWorker::query()->orderBy('name')->get(['id', 'name']);
        $items = Item::query()->orderBy('item_name')->get(['id', 'item_name']);

        $selectedWorkerId = (int) $request->input('job_worker_id');
        $selectedItemId = (int) $request->input('item_id');

        $outwardRows = collect();
        $inwardRows = collect();
        $summary = [
            'outward_mtrs_l100' => 0,
            'inward_mtrs_l100' => 0,
            'balance' => 0,
        ];

        if ($selectedWorkerId > 0 && $selectedItemId > 0) {
            $outwardRows = JobWorkAssignmentItem::query()
                ->with([
                    'assignment:id,assign_no,job_worker_id,freight',
                ])
                ->where(function ($q) use ($selectedItemId) {
                    $q->where('item_id', $selectedItemId)
                        ->orWhere('process', (string) $selectedItemId);
                })
                ->whereHas('assignment', function ($q) use ($selectedWorkerId) {
                    $q->where('job_worker_id', $selectedWorkerId);
                })
                ->orderByRaw('(select assign_no from job_work_assignments where job_work_assignments.id = job_work_assignment_items.job_work_assignment_id) asc')
                ->orderBy('sort_order')
                ->get()
                ->map(function (JobWorkAssignmentItem $row) {
                    $mtrsL100 = (float) $row->net_meter;
                    if ($mtrsL100 <= 0) {
                        $mtrsL100 = ((float) $row->meter * (float) $row->fold) / 100;
                    }

                    return [
                        'ch_no' => (string) ($row->assignment?->assign_no ?? '-'),
                        'quantity' => (float) $row->meter,
                        'fold' => (float) $row->fold,
                        'lr_transport' => trim((string) (($row->lr_no ?: '') . ' ' . ($row->transport ?: ''))) ?: '-',
                        'grey_bleach' => 'GREY',
                        'freight' => strtoupper((string) ($row->assignment?->freight ?? '-')),
                        'mtrs_l100' => $mtrsL100,
                    ];
                })
                ->values();

            $inwardRows = JobWorkerInwardItem::query()
                ->with([
                    'inward:id,inward_date,ch_no,job_worker_id,remark',
                ])
                ->where('item_id', $selectedItemId)
                ->whereHas('inward', function ($q) use ($selectedWorkerId) {
                    $q->where('job_worker_id', $selectedWorkerId);
                })
                ->orderByRaw('(select inward_date from job_worker_inwards where job_worker_inwards.id = job_worker_inward_items.job_worker_inward_id) asc')
                ->orderBy('id')
                ->get()
                ->map(function (JobWorkerInwardItem $row) {
                    $mtrsL100 = (float) $row->total_meter;
                    if ($mtrsL100 <= 0) {
                        $mtrsL100 = ((float) $row->meter * (float) $row->fold) / 100;
                    }

                    $shrinkage = (float) ($row->shrinkage ?? 0);
                    $greyMtr = $mtrsL100;
                    if ($shrinkage > 0 && $shrinkage < 100) {
                        $greyMtr = ($mtrsL100 * 100) / (100 - $shrinkage);
                    }

                    return [
                        'date' => optional($row->inward?->inward_date)->format('Y-m-d') ?: '-',
                        'ch_no' => (string) ($row->inward?->ch_no ?? '-'),
                        'quantity' => (float) $row->meter,
                        'fold' => (float) $row->fold,
                        'process' => (string) ($row->type ?: '-'),
                        'design_no' => (string) ($row->quality ?: '-'),
                        'shgk' => $shrinkage,
                        'mtrs_l100' => $mtrsL100,
                        'grey_mtr' => $greyMtr,
                        'bill_no' => '-',
                        'remark' => (string) ($row->inward?->remark ?: '-'),
                    ];
                })
                ->values();

            $summary['outward_mtrs_l100'] = (float) $outwardRows->sum('mtrs_l100');
            $summary['inward_mtrs_l100'] = (float) $inwardRows->sum('mtrs_l100');
            $summary['balance'] = $summary['outward_mtrs_l100'] - $summary['inward_mtrs_l100'];
        }

        return view('admin.reports.list_report', compact(
            'jobWorkers',
            'items',
            'selectedWorkerId',
            'selectedItemId',
            'outwardRows',
            'inwardRows',
            'summary'
        ));
    }
}
