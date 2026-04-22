@extends('admin.layouts.app')

@section('style')
<style>
.dispatch-preview {
    max-width: 900px;
    margin: auto;
    font-family: Arial, sans-serif;
}

.dispatch-preview__sheet {
    background: #fff;
    border: 2px solid #000;
    padding: 20px 25px;
}

/* HEADER */
.dispatch-preview__header {
    border-bottom: 2px solid #000;
    margin-bottom: 10px;
    padding-bottom: 10px;
}

.dispatch-preview__brand {
    display: grid;
    grid-template-columns: 120px 1fr 120px;
    align-items: center;
}

.dispatch-preview__gst {
    font-size: 12px;
    font-weight: bold;
}

.dispatch-preview__logo {
    width: 70px;
    height: 70px;
    border: 2px solid #000;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-top: 10px;
}

.dispatch-preview__phones {
    text-align: right;
    font-size: 12px;
    font-weight: bold;
}

/* TITLE */
.dispatch-preview__title {
    text-align: center;
}

.dispatch-preview__title h1 {
    font-size: 24px;
    font-weight: 800;
    margin: 4px 0;
}

.dispatch-preview__title h2 {
    font-size: 14px;
    text-decoration: underline;
    margin: 0;
}

.dispatch-preview__subtitle {
    font-size: 13px;
    font-style: italic;
}

.dispatch-preview__address {
    font-size: 13px;
    margin-top: 4px;
}

/* META TABLE */
.dispatch-preview__meta {
    width: 100%;
    margin-top: 10px;
    border-collapse: collapse;
}

.dispatch-preview__meta td {
    padding: 6px;
    font-size: 13px;
}

.dispatch-preview__label {
    font-weight: bold;
    width: 100px;
}

.dispatch-preview__line {
    border-bottom: 1px solid #000;
}

/* MAIN TABLE */
.dispatch-preview__body {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

.dispatch-preview__body th,
.dispatch-preview__body td {
    border: 1px solid #000;
    padding: 6px;
    font-size: 13px;
}

.dispatch-preview__body th {
    text-align: center;
    font-weight: bold;
    background: #f5f5f5;
}

.numeric {
    text-align: right;
}

/* SUMMARY */
.dispatch-preview__summary-label {
    font-weight: bold;
    width: 150px;
}

/* FOOTER */
.dispatch-preview__footer {
    display: flex;
    justify-content: space-between;
    margin-top: 30px;
    font-weight: bold;
}

.dispatch-preview__note {
    margin-top: 10px;
    font-size: 11px;
}

/* PRINT */
@media print {
    .layout-navbar,
    .layout-menu,
    .content-footer,
    .btn,
    .no-print {
        display: none !important;
    }

    body * {
        visibility: hidden;
    }

    .dispatch-preview,
    .dispatch-preview * {
        visibility: visible;
    }

    .dispatch-preview {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }

    .dispatch-preview__sheet {
        border: none;
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

    $qualityNames = $dispatch->items
        ->map(fn ($item) => $item->item?->item_name)
        ->filter()
        ->unique()
        ->join(', ');

    $pieces = $dispatch->items->count();
@endphp

<div class="container-fluid py-4">

    <!-- TOP BAR -->
    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
        <h5 class="mb-0 text-primary">Order Dispatch Challan</h5>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.orderdispatches.index') }}" class="btn btn-outline-secondary">Back</a>
            <button onclick="window.print()" class="btn btn-primary">Print</button>
        </div>
    </div>

    <!-- CHALLAN -->
    <div class="dispatch-preview">
        <div class="dispatch-preview__sheet">

            <!-- HEADER -->
            <div class="dispatch-preview__header">
                <div class="dispatch-preview__brand">

                    <div>
                        <div class="dispatch-preview__gst">GSTIN : 08ADQFS3041N1ZI</div>
                        <div class="dispatch-preview__logo">SMT</div>
                    </div>

                    <div class="dispatch-preview__title">
                        <div>|| Shri Ganeshay Namah ||</div>
                        <h2>DISPATCH CHALLAN</h2>
                        <h1>Shree Mahalaxmi Textile Mills</h1>
                        <p class="dispatch-preview__subtitle">
                            Mfrs. & Printer of All Kinds of Export Fabrics
                        </p>
                        <p class="dispatch-preview__address">
                            F-716 (A), M.I.A., Basni 2nd Phase, Jodhpur (Raj.)
                        </p>
                    </div>

                    <div class="dispatch-preview__phones">
                        <div>98290-24057</div>
                    </div>

                </div>
            </div>

            <!-- META -->
            <table class="dispatch-preview__meta">
                <tr>
                    <td class="dispatch-preview__label">Bale No.</td>
                    <td class="dispatch-preview__line">{{ $dispatch->dispatch_no ?? '-' }}</td>

                    <td class="dispatch-preview__label text-end">Date</td>
                    <td class="dispatch-preview__line">
                        {{ optional($dispatch->dispatch_date)->format('d/m/Y') ?? '-' }}
                    </td>
                </tr>

                <tr>
                    <td class="dispatch-preview__label">M/s.</td>
                    <td colspan="3" class="dispatch-preview__line">
                        {{ $customerDisplay }}
                        {{ $customerAddress ? ' - ' . $customerAddress : '' }}
                    </td>
                </tr>

                <tr>
                    <td class="dispatch-preview__label">Transport</td>
                    <td class="dispatch-preview__line">{{ $dispatch->transport ?? '-' }}</td>

                    <td class="dispatch-preview__label text-end">Mobile</td>
                    <td class="dispatch-preview__line">
                        {{ $dispatch->mobile_number ?? ($dispatch->customer?->phone ?? '-') }}
                    </td>
                </tr>

                <tr>
                    <td class="dispatch-preview__label">Quality</td>
                    <td class="dispatch-preview__line">{{ $qualityNames ?? '-' }}</td>

                    <td class="dispatch-preview__label text-end">L.R. No.</td>
                    <td class="dispatch-preview__line">-</td>
                </tr>
            </table>

            <!-- ITEMS -->
            <table class="dispatch-preview__body">
                <thead>
                    <tr>
                        <th width="10%">S.No.</th>
                        <th width="25%">Lot No.</th>
                        <th width="35%">Quality</th>
                        <th width="20%">Meters</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($dispatch->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->lot_no }}</td>
                            <td>{{ $item->item?->item_name }}</td>
                            <td class="numeric">
                                {{ number_format((float) $item->meter, 2, '.', '') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No items found</td>
                        </tr>
                    @endforelse

                    <!-- TOTAL -->
                    <tr>
                        <td colspan="3" class="text-end"><strong>Total</strong></td>
                        <td class="numeric">
                            <strong>
                                {{ number_format((float) $dispatch->total_meter, 2, '.', '') }}
                            </strong>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- SUMMARY -->
            <table class="dispatch-preview__body mt-3">
                <tr>
                    <td class="dispatch-preview__summary-label">Quality</td>
                    <td>{{ $qualityNames }}</td>

                    <td class="dispatch-preview__summary-label">Pcs.</td>
                    <td>{{ $pieces }}</td>
                </tr>

                <tr>
                    <td class="dispatch-preview__summary-label">Total Mtrs.</td>
                    <td>{{ number_format((float) $dispatch->total_meter, 2) }}</td>

                    <td class="dispatch-preview__summary-label">Remarks</td>
                    <td>{{ $dispatch->status ?? '-' }}</td>
                </tr>
            </table>

            <!-- FOOTER -->
            <div class="dispatch-preview__footer">
                <div>Prepared by __________</div>
                <div>Packed by __________</div>
                <div>Checked by __________</div>
            </div>

            <div class="dispatch-preview__note">
                Note: One copy of dispatch challan should accompany the goods.
            </div>

        </div>
    </div>

</div>
@endsection