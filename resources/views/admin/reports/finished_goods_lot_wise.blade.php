@extends('admin.layouts.app')

@section('style')
<style>
    .report-title {
        font-weight: 700;
        color: #1b5e20;
        text-align: center;
        border: 1px solid #cfd8dc;
        background: #d7f0dc;
        padding: 8px;
        margin-bottom: 12px;
    }
    .report-table th,
    .report-table td {
        white-space: nowrap;
        font-size: 14px;
        vertical-align: middle;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow border-0 rounded-3 p-3">
        <h5 class="mb-3 text-primary">Lot Wise Finished Goods In Stock</h5>

        <div class="report-title">LOT WISE REPORT OF FINISHED GOODS IN STOCK</div>

        <div class="table-responsive">
            <table class="table table-bordered report-table">
                <thead class="table-light">
                    <tr>
                        <th>DATE</th>
                        <th>LOT NO</th>
                        <th>QUALITY</th>
                        <th>QTY</th>
                        <th>PRINTED / DYED</th>
                        <th>DESIGN NO</th>
                        <th>SUPPLIER</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        <tr>
                            <td>{{ $row['date'] }}</td>
                            <td>{{ $row['lot_no'] }}</td>
                            <td>{{ $row['quality'] }}</td>
                            <td>{{ number_format((float) $row['qty'], 2, '.', '') }}</td>
                            <td>{{ $row['printed_dyed'] }}</td>
                            <td>{{ $row['design_no'] }}</td>
                            <td>{{ $row['supplier'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No stock found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
