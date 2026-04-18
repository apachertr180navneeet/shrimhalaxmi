@extends('admin.layouts.app')

@section('style')
<style>
    .challan-preview {
        max-width: 1120px;
        margin: 0 auto;
        background: #fff;
        border: 1px solid #d8dde6;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
    }

    .challan-preview__sheet {
        padding: 28px;
    }

    .challan-preview__header {
        border-bottom: 2px solid #111827;
        padding-bottom: 14px;
        margin-bottom: 18px;
    }

    .challan-preview__brand {
        display: grid;
        grid-template-columns: 110px 1fr 180px;
        gap: 16px;
        align-items: center;
    }

    .challan-preview__logo {
        width: 96px;
        height: 96px;
        border: 2px solid #111827;
        border-radius: 24px;
        display: grid;
        place-items: center;
        font-size: 34px;
        font-weight: 800;
        background: #fff;
    }

    .challan-preview__title {
        text-align: center;
        color: #111827;
    }

    .challan-preview__title h1 {
        margin: 6px 0 4px;
        font-size: 30px;
        font-weight: 800;
    }

    .challan-preview__title h2 {
        margin: 4px 0;
        font-size: 18px;
        font-weight: 700;
        letter-spacing: 0.05em;
    }

    .challan-preview__title p,
    .challan-preview__title small,
    .challan-preview__contact {
        margin: 0;
        color: #374151;
    }

    .challan-preview__contact {
        text-align: right;
        font-weight: 700;
        line-height: 1.7;
    }

    .challan-preview__meta {
        width: 100%;
        border: 1px solid #111827;
        margin-bottom: 16px;
    }

    .challan-preview__meta td {
        border: 1px solid #111827;
        padding: 10px 12px;
        font-size: 14px;
    }

    .challan-preview__items {
        width: 100%;
        border-collapse: collapse;
    }

    .challan-preview__items th,
    .challan-preview__items td {
        border: 1px solid #111827;
        padding: 9px 10px;
        font-size: 14px;
        vertical-align: top;
    }

    .challan-preview__items th {
        background: #f7f7f7;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .challan-preview__footer {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        margin-top: 18px;
        font-size: 14px;
    }

    .challan-preview__totals {
        width: 320px;
        margin-left: auto;
        border-collapse: collapse;
    }

    .challan-preview__totals td {
        border: 1px solid #111827;
        padding: 8px 10px;
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

        .challan-preview {
            box-shadow: none;
            border: none;
        }

        .challan-preview__sheet {
            padding: 0;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
        <h5 class="mb-0 text-primary">Job Worker Inward Challan Preview</h5>
        <button type="button" class="btn btn-primary" onclick="window.print()">Print</button>
    </div>

    <div class="challan-preview">
        <div class="challan-preview__sheet">
            <div class="challan-preview__header">
                <div class="challan-preview__brand">
                    <div class="challan-preview__logo">SMT</div>

                    <div class="challan-preview__title">
                        <small>GSTIN : 08ADQFS3041N1ZI</small>
                        <small>|| Shri Ganeshay Namah ||</small>
                        <h2>JOB WORK INWARD CHALLAN</h2>
                        <h1>Shree Mahalaxmi Textile Mills</h1>
                        <p>Mfrs. &amp; Printer of All Kinds of Export Fabrics</p>
                        <p>F-716 (A), M.I.A., Basni 2nd Phase, Jodhpur (Raj.)</p>
                    </div>

                    <div class="challan-preview__contact">
                        <div>98290-24057</div>
                        <div>90249-24057</div>
                    </div>
                </div>
            </div>

            <table class="challan-preview__meta">
                <tr>
                    <td width="25%"><strong>Date:</strong> {{ optional($inward->inward_date)->format('d/m/Y') ?: '-' }}</td>
                    <td width="25%"><strong>Challan No:</strong> {{ $inward->ch_no ?: '-' }}</td>
                    <td width="25%"><strong>Job Worker:</strong> {{ $inward->jobWorker?->name ?: '-' }}</td>
                    <td width="25%"><strong>Status:</strong> {{ ucfirst($inward->status ?: 'inactive') }}</td>
                </tr>
                <tr>
                    <td colspan="4"><strong>Remark:</strong> {{ $inward->remark ?: '-' }}</td>
                </tr>
            </table>

            <table class="challan-preview__items">
                <thead>
                    <tr>
                        <th width="6%">Sr.</th>
                        <th width="16%">Lot No.</th>
                        <th>Item Name</th>
                        <th width="14%">Color</th>
                        <th width="10%">Mtr</th>
                        <th width="10%">Fold</th>
                        <th width="12%">Total Mtr</th>
                        <th width="10%">Shrinkage</th>
                        <th width="12%">After Shrk.</th>
                        <th width="10%">Type</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($inward->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->lot_no ?: '-' }}</td>
                            <td>{{ $item->item?->item_name ?: '-' }}</td>
                            <td>{{ $item->quality ?: '-' }}</td>
                            <td>{{ number_format((float) $item->meter, 2, '.', '') }}</td>
                            <td>{{ number_format((float) $item->fold, 2, '.', '') }}</td>
                            <td>{{ number_format((float) $item->total_meter, 2, '.', '') }}</td>
                            <td>{{ $item->shrinkage ?: '-' }}</td>
                            <td>{{ $item->after_shrinkage_meter ?: '-' }}</td>
                            <td>{{ $item->type ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">No items found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <table class="challan-preview__totals mt-3">
                <tr>
                    <td><strong>Total Lots</strong></td>
                    <td>{{ $inward->items->count() }}</td>
                </tr>
                <tr>
                    <td><strong>Total Meter</strong></td>
                    <td>{{ number_format((float) $inward->items->sum('meter'), 2, '.', '') }}</td>
                </tr>
                <tr>
                    <td><strong>Total Net Meter</strong></td>
                    <td>{{ number_format((float) $inward->items->sum('total_meter'), 2, '.', '') }}</td>
                </tr>
            </table>

            <div class="challan-preview__footer">
                <div>Prepared By: ____________________</div>
                <div>Authorized Signatory: ____________________</div>
            </div>
        </div>
    </div>
</div>
@endsection
