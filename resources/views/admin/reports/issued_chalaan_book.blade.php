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
        <h5 class="mb-3 text-primary">Fabric Issued To Job Workers</h5>

        <div class="report-title">DETAILS OF ALL THE FABRIC ISSUED TO JOB WORKERS WHOLE CHALAAN BOOK</div>

        <div class="table-responsive">
            <table class="table table-bordered report-table">
                <thead class="table-light">
                    <tr>
                        <th>DATE</th>
                        <th>CHNO</th>
                        <th>JOB WORKER</th>
                        <th>TRANSPORT</th>
                        <th>LR NUMBER</th>
                        <th>QUALITY</th>
                        <th>ITEM (GREY/BLEAC)</th>
                        <th>QUANTITY</th>
                        <th>VENDOR LOT NUMBER</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        <tr>
                            <td>{{ $row['date'] }}</td>
                            <td>{{ $row['ch_no'] }}</td>
                            <td>{{ $row['job_worker'] }}</td>
                            <td>{{ $row['transport'] }}</td>
                            <td>{{ $row['lr_number'] }}</td>
                            <td>{{ $row['quality'] }}</td>
                            <td>{{ $row['item_stage'] }}</td>
                            <td>{{ number_format((float) $row['quantity'], 2, '.', '') }}</td>
                            <td>{{ $row['vendor_lot_no'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No data found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
