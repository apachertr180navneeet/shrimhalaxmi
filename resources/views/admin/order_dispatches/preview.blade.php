@extends('admin.layouts.app')

@section('style')
<style>
    .dispatch-preview {
        max-width: 980px;
        margin: 0 auto;
    }

    .dispatch-preview__sheet {
        background: #fff;
        color: #111;
        border: 1px solid #202020;
        padding: 26px 30px 22px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
    }

    .dispatch-preview__header {
        border-bottom: 2px solid #111;
        padding-bottom: 10px;
        margin-bottom: 14px;
    }

    .dispatch-preview__brand {
        display: grid;
        grid-template-columns: 140px 1fr 150px;
        gap: 12px;
        align-items: start;
    }

    .dispatch-preview__gst,
    .dispatch-preview__phones,
    .dispatch-preview__invocation {
        font-size: 12px;
        font-weight: 700;
    }

    .dispatch-preview__logo {
        width: 86px;
        height: 54px;
        border: 3px solid #111;
        border-radius: 999px;
        display: grid;
        place-items: center;
        font-size: 24px;
        font-weight: 800;
        margin-top: 10px;
    }

    .dispatch-preview__title {
        text-align: center;
    }

    .dispatch-preview__title h2,
    .dispatch-preview__title h1,
    .dispatch-preview__title p,
    .dispatch-preview__title div {
        margin: 0;
    }

    .dispatch-preview__title h2 {
        font-size: 16px;
        font-weight: 800;
        text-decoration: underline;
        letter-spacing: 0.04em;
    }

    .dispatch-preview__title h1 {
        font-size: 30px;
        font-weight: 800;
        line-height: 1.1;
        margin-top: 4px;
    }

    .dispatch-preview__subtitle {
        font-size: 15px;
        font-style: italic;
        font-weight: 600;
        margin-top: 4px;
    }

    .dispatch-preview__address {
        font-size: 16px;
        letter-spacing: 0.03em;
        margin-top: 6px;
    }

    .dispatch-preview__phones {
        text-align: right;
        line-height: 1.5;
        margin-top: 8px;
    }

    .dispatch-preview__meta {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
    }

    .dispatch-preview__meta td {
        padding: 6px 6px;
        font-size: 14px;
        vertical-align: bottom;
    }

    .dispatch-preview__label {
        width: 90px;
        font-weight: 700;
        white-space: nowrap;
    }

    .dispatch-preview__line {
        border-bottom: 1px solid #111;
        min-height: 24px;
    }

    .dispatch-preview__line span {
        display: inline-block;
        padding: 0 4px 2px;
        min-height: 20px;
    }

    .dispatch-preview__body {
        width: 100%;
        border-collapse: collapse;
    }

    .dispatch-preview__body th,
    .dispatch-preview__body td {
        border: 1px solid #111;
        padding: 6px 7px;
        font-size: 13px;
        vertical-align: top;
    }

    .dispatch-preview__body thead th {
        text-align: center;
        font-weight: 800;
    }

    .dispatch-preview__body td.numeric {
        text-align: right;
        white-space: nowrap;
    }

    .dispatch-preview__summary-label {
        width: 28%;
        font-weight: 700;
    }

    .dispatch-preview__summary-value {
        height: 34px;
    }

    .dispatch-preview__footer {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        margin-top: 26px;
        font-weight: 700;
    }

    .dispatch-preview__note {
        margin-top: 10px;
        font-size: 11px;
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

        .dispatch-preview__sheet {
            border: none;
            box-shadow: none;
            padding: 0;
        }
    }
</style>
@endsection

@section('content')
@php
    $customerDisplay = $dispatch->customer?->firm_name ?: $dispatch->customer?->name;
    $customerAddress = collect([
        $dispatch->customer?->location,
        $dispatch->customer?->address_2,
    ])->filter()->join(', ');
    $qualityNames = $dispatch->items->map(fn ($item) => $item->item?->item_name)->filter()->unique()->join(', ');
    $pieces = $dispatch->items->count();
@endphp

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
        <h5 class="mb-0 text-primary">Order Dispatch Challan</h5>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.orderdispatches.index') }}" class="btn btn-outline-secondary">Back</a>
            <button type="button" class="btn btn-primary" onclick="window.print()">Print</button>
        </div>
    </div>

    <div class="dispatch-preview">
        <div class="dispatch-preview__sheet">
            <div class="dispatch-preview__header">
                <div class="dispatch-preview__brand">
                    <div>
                        <div class="dispatch-preview__gst">GSTIN : 08ADQFS3041N1ZI</div>
                        <div class="dispatch-preview__logo">SMT</div>
                    </div>

                    <div class="dispatch-preview__title">
                        <div class="dispatch-preview__invocation">|| Shri Ganeshay Namah ||</div>
                        <h2>DISPATCH CHALLAN</h2>
                        <h1>Shree Mahalaxmi Textile Mills</h1>
                        <p class="dispatch-preview__subtitle">Mfrs. &amp; Printer of All Kinds of Export Fabrics</p>
                        <p class="dispatch-preview__address">F-716 (A), M.I.A., Basni 2nd Phase JODHPUR (Raj.)</p>
                    </div>

                    <div class="dispatch-preview__phones">
                        <div>98290-24057</div>
                    </div>
                </div>
            </div>

            <table class="dispatch-preview__meta">
                <tr>
                    <td class="dispatch-preview__label">Bale No.</td>
                    <td class="dispatch-preview__line"><span>{{ $dispatch->dispatch_no ?: '-' }}</span></td>
                    <td class="dispatch-preview__label text-end">Date</td>
                    <td class="dispatch-preview__line"><span>{{ optional($dispatch->dispatch_date)->format('d/m/Y') ?: '-' }}</span></td>
                </tr>
                <tr>
                    <td class="dispatch-preview__label">M/s.</td>
                    <td colspan="3" class="dispatch-preview__line"><span>{{ $customerDisplay ?: '-' }}{{ $customerAddress ? ' - ' . $customerAddress : '' }}</span></td>
                </tr>
                <tr>
                    <td class="dispatch-preview__label">Transport</td>
                    <td class="dispatch-preview__line"><span>{{ $dispatch->transport ?: '-' }}</span></td>
                    <td class="dispatch-preview__label text-end">Mobile</td>
                    <td class="dispatch-preview__line"><span>{{ $dispatch->mobile_number ?: ($dispatch->customer?->phone ?: '-') }}</span></td>
                </tr>
                <tr>
                    <td class="dispatch-preview__label">Quality</td>
                    <td class="dispatch-preview__line"><span>{{ $qualityNames ?: '-' }}</span></td>
                    <td class="dispatch-preview__label text-end">L.R. No.</td>
                    <td class="dispatch-preview__line"><span>-</span></td>
                </tr>
            </table>

            <table class="dispatch-preview__body">
                <thead>
                    <tr>
                        <th width="7%">S. No.</th>
                        <th width="18%">Lot No.</th>
                        <th width="18%">Quality</th>
                        <th width="12%">Meters</th>
                        <th width="12%">Rate</th>
                        <th width="13%">Amount</th>
                        <th width="10%">GST</th>
                        <th width="10%">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dispatch->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->lot_no ?: '' }}</td>
                            <td>{{ $item->item?->item_name ?: '' }}</td>
                            <td class="numeric">{{ number_format((float) $item->meter, 2, '.', '') }}</td>
                            <td class="numeric">{{ number_format((float) $item->rate, 2, '.', '') }}</td>
                            <td class="numeric">{{ number_format((float) $item->amount, 2, '.', '') }}</td>
                            <td class="numeric">{{ number_format((float) ($item->gst ?? 0), 2, '.', '') }}</td>
                            <td class="numeric">{{ number_format((float) ($item->total_amount ?? 0), 2, '.', '') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No items found.</td>
                        </tr>
                    @endforelse
                    <tr>
                        <td colspan="3" class="text-end"><strong>Total</strong></td>
                        <td class="numeric"><strong>{{ number_format((float) $dispatch->total_meter, 2, '.', '') }}</strong></td>
                        <td></td>
                        <td class="numeric"><strong>{{ number_format((float) $dispatch->items->sum('amount'), 2, '.', '') }}</strong></td>
                        <td class="numeric"><strong>{{ number_format((float) $dispatch->items->sum('gst'), 2, '.', '') }}</strong></td>
                        <td class="numeric"><strong>{{ number_format((float) $dispatch->total_amount, 2, '.', '') }}</strong></td>
                    </tr>
                </tbody>
            </table>

            <table class="dispatch-preview__body mt-3">
                <tbody>
                    <tr>
                        <td class="dispatch-preview__summary-label">Quality</td>
                        <td class="dispatch-preview__summary-value">{{ $qualityNames ?: '-' }}</td>
                        <td class="dispatch-preview__summary-label">Pcs.</td>
                        <td class="dispatch-preview__summary-value">{{ $pieces }}</td>
                    </tr>
                    <tr>
                        <td class="dispatch-preview__summary-label">Total Mtrs.</td>
                        <td class="dispatch-preview__summary-value">{{ number_format((float) $dispatch->total_meter, 2, '.', '') }}</td>
                        <td class="dispatch-preview__summary-label">Total Amount</td>
                        <td class="dispatch-preview__summary-value">{{ number_format((float) $dispatch->total_amount, 2, '.', '') }}</td>
                    </tr>
                    <tr>
                        <td class="dispatch-preview__summary-label">Remarks</td>
                        <td colspan="3" class="dispatch-preview__summary-value">{{ $dispatch->status ?: '-' }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="dispatch-preview__footer">
                <div>Prepared by ........................</div>
                <div>Packed by ........................</div>
                <div>Checked by ........................</div>
            </div>

            <div class="dispatch-preview__note">
                Note: One copy of dispatch challan should accompany the goods during transport.
            </div>
        </div>
    </div>
</div>
@endsection
