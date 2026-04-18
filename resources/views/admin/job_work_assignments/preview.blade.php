@extends('admin.layouts.app')

@section('style')
<style>
    .assignment-preview {
        max-width: 1100px;
        margin: 0 auto;
    }

    .assignment-preview__sheet {
        background: #fff;
        border: 1px solid #111827;
        padding: 24px;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
    }

    .assignment-preview__header {
        border-bottom: 2px solid #111827;
        padding-bottom: 14px;
        margin-bottom: 16px;
    }

    .assignment-preview__brand {
        display: grid;
        grid-template-columns: 110px 1fr 180px;
        gap: 16px;
        align-items: center;
    }

    .assignment-preview__logo {
        width: 92px;
        height: 92px;
        border: 2px solid #111827;
        border-radius: 24px;
        display: grid;
        place-items: center;
        font-size: 32px;
        font-weight: 800;
        letter-spacing: 0.08em;
    }

    .assignment-preview__title {
        text-align: center;
        color: #111827;
    }

    .assignment-preview__title small,
    .assignment-preview__title p {
        display: block;
        margin: 0;
        color: #374151;
    }

    .assignment-preview__title h2 {
        margin: 2px 0 4px;
        font-size: 18px;
        font-weight: 700;
        letter-spacing: 0.08em;
    }

    .assignment-preview__title h1 {
        margin: 2px 0 4px;
        font-size: 30px;
        font-weight: 800;
    }

    .assignment-preview__contact {
        text-align: right;
        font-weight: 700;
        line-height: 1.7;
    }

    .assignment-preview__meta {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 14px;
    }

    .assignment-preview__meta td {
        border: 1px solid #111827;
        padding: 9px 10px;
        font-size: 14px;
        vertical-align: top;
    }

    .assignment-preview__items {
        width: 100%;
        border-collapse: collapse;
    }

    .assignment-preview__items th,
    .assignment-preview__items td {
        border: 1px solid #111827;
        padding: 8px 10px;
        font-size: 13px;
        vertical-align: top;
    }

    .assignment-preview__items th {
        background: #f5f5f5;
        font-weight: 700;
        text-transform: uppercase;
    }

    .assignment-preview__summary {
        width: 320px;
        margin-left: auto;
        margin-top: 14px;
        border-collapse: collapse;
    }

    .assignment-preview__summary td {
        border: 1px solid #111827;
        padding: 8px 10px;
        font-size: 14px;
    }

    .assignment-preview__footer {
        display: flex;
        justify-content: space-between;
        gap: 18px;
        margin-top: 28px;
        padding-top: 18px;
        border-top: 1px solid #111827;
        font-size: 14px;
    }

    @media print {
        .layout-navbar,
        .layout-menu,
        .layout-page .content-footer,
        .btn,
        .no-print {
            display: none !important;
        }

        .layout-wrapper,
        .layout-container,
        .layout-page,
        .content-wrapper,
        .content-body,
        .container-fluid {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }

        .assignment-preview__sheet {
            border: none;
            box-shadow: none;
            padding: 0;
        }
    }
</style>
@endsection

@section('content')
@php
    $firstItem = $assignment->items->first();
    $transport = $assignment->items->pluck('transport')->filter()->unique()->join(', ');
    $lrNo = $assignment->items->pluck('lr_no')->filter()->unique()->join(', ');
    $itemNames = $assignment->items->map(fn ($item) => $item->item?->item_name)->filter()->unique()->join(', ');
    $shadeNames = $assignment->items->map(fn ($item) => $item->quality ?? $item->colour)->filter()->unique()->join(', ');
    $processNames = $assignment->items->map(function ($item) {
        $processItemName = trim((string) ($item->processItem?->item_name ?? ''));
        if ($processItemName !== '') {
            return $processItemName;
        }

        $rawProcess = trim((string) $item->process);
        return ($rawProcess !== '' && ! is_numeric($rawProcess)) ? $rawProcess : null;
    })->filter()->unique()->join(', ');
@endphp

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
        <h5 class="mb-0 text-primary">Job Work Assignment Preview</h5>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.jobworkassignments.index') }}" class="btn btn-outline-secondary">Back</a>
            <button type="button" class="btn btn-primary" onclick="window.print()">Print</button>
        </div>
    </div>

    <div class="assignment-preview">
        <div class="assignment-preview__sheet">
            <div class="assignment-preview__header">
                <div class="assignment-preview__brand">
                    <div class="assignment-preview__logo">SMT</div>

                    <div class="assignment-preview__title">
                        <small>GSTIN : 08ADQFS3041N1ZI</small>
                        <small>|| Shri Ganeshay Namah ||</small>
                        <h2>PACKING SLIP</h2>
                        <h1>Shree Mahalaxmi Textile Mills</h1>
                        <p>Mfrs. &amp; Printer of All Kinds of Export Fabrics</p>
                        <p>F-716 (A), M.I.A., Basni 2nd Phase Jodhpur (Raj.)</p>
                    </div>

                    <div class="assignment-preview__contact">
                        <div>98290-24057</div>
                    </div>
                </div>
            </div>

            <table class="assignment-preview__meta">
                <tr>
                    <td width="25%"><strong>Bale No.</strong> {{ $assignment->assign_no ?: '-' }}</td>
                    <td width="25%"><strong>Date</strong> {{ optional($assignment->assignment_date)->format('d/m/Y') ?: '-' }}</td>
                    <td width="25%"><strong>M/s.</strong> {{ $assignment->jobWorker?->name ?: '-' }}</td>
                    <td width="25%"><strong>Freight</strong> {{ $assignment->freight ?: '-' }}</td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Transport</strong> {{ $transport ?: '-' }}</td>
                    <td colspan="2"><strong>LR No.</strong> {{ $lrNo ?: '-' }}</td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Item Name</strong> {{ $itemNames ?: '-' }}</td>
                    <td><strong>Shade</strong> {{ $shadeNames ?: '-' }}</td>
                    <td><strong>Fold</strong> {{ $firstItem ? number_format((float) $firstItem->fold, 2, '.', '') : '-' }}</td>
                </tr>
                <tr>
                    <td colspan="4"><strong>Process</strong> {{ $processNames ?: '-' }}</td>
                </tr>
            </table>

            <table class="assignment-preview__items">
                <thead>
                    <tr>
                        <th width="6%">S. No.</th>
                        <th width="14%">Lot No.</th>
                        <th>Item / Design</th>
                        <th width="14%">Shade</th>
                        <th width="10%">Fold</th>
                        <th width="12%">Meters</th>
                        <th width="12%">Net Meters</th>
                        <th width="16%">Process</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($assignment->items as $index => $item)
                        @php
                            $rowProcess = trim((string) ($item->processItem?->item_name ?? ''));
                            if ($rowProcess === '') {
                                $rawProcess = trim((string) $item->process);
                                $rowProcess = ($rawProcess !== '' && ! is_numeric($rawProcess)) ? $rawProcess : '-';
                            }
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->lot_no ?: '-' }}</td>
                            <td>{{ $item->item?->item_name ?: '-' }}</td>
                            <td>{{ $item->quality ?? $item->colour ?? '-' }}</td>
                            <td>{{ number_format((float) $item->fold, 2, '.', '') }}</td>
                            <td>{{ number_format((float) $item->meter, 2, '.', '') }}</td>
                            <td>{{ number_format((float) $item->net_meter, 2, '.', '') }}</td>
                            <td>{{ $rowProcess }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No items found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <table class="assignment-preview__summary">
                <tr>
                    <td><strong>Total Pcs.</strong></td>
                    <td>{{ $assignment->items->count() }}</td>
                </tr>
                <tr>
                    <td><strong>Total Mtrs.</strong></td>
                    <td>{{ number_format((float) $assignment->total_meter, 2, '.', '') }}</td>
                </tr>
                <tr>
                    <td><strong>Total Net Mtrs.</strong></td>
                    <td>{{ number_format((float) $assignment->total_net_meter, 2, '.', '') }}</td>
                </tr>
                <tr>
                    <td><strong>Remarks</strong></td>
                    <td>{{ $assignment->remark ?: '-' }}</td>
                </tr>
            </table>

            <div class="assignment-preview__footer">
                <div>Prepared by ____________________</div>
                <div>Packed by ____________________</div>
                <div>Checked by ____________________</div>
            </div>
        </div>
    </div>
</div>
@endsection
