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
        margin-bottom: 0;
    }
    .report-subtitle {
        font-weight: 600;
        text-align: center;
        border: 1px solid #cfd8dc;
        border-top: 0;
        background: #f8f9fa;
        padding: 6px;
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
        <h5 class="mb-3 text-primary">Grey Lot Balance Report</h5>

        <div class="report-title">THIS WILL BE THE REPORT FOR THE GOODS WE HAVE PURCHASED AND HAVENT YET BEEN ASSIGNED TO JOB WORKER OR SOLD DIRECTLY</div>
        <div class="report-subtitle">GREY LOT BALANCE REPORT (SORTED BY DATE)</div>

        <div class="table-responsive">
            <table class="table table-bordered report-table">
                <thead class="table-light">
                    <tr>
                        <th>DATE</th>
                        <th>SUPPLIER NAME</th>
                        <th>BILL NO</th>
                        <th>LOT NO</th>
                        <th>QUALITY</th>
                        <th>QUANTITY</th>
                        <th>LR NUMBER</th>
                        <th>TRANSPORT</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        <tr>
                            <td>{{ $row['date'] }}</td>
                            <td>{{ $row['supplier_name'] }}</td>
                            <td>{{ $row['bill_no'] }}</td>
                            <td>{{ $row['lot_no'] }}</td>
                            <td>{{ $row['quality'] }}</td>
                            <td>{{ number_format((float) $row['quantity'], 2, '.', '') }}</td>
                            <td>{{ $row['lr_number'] }}</td>
                            <td>{{ $row['transport'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No data found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
