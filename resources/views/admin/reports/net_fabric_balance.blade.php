@extends('admin.layouts.app')

@section('style')
<style>
    .balance-title {
        font-weight: 700;
        color: #2e7d32;
        border: 1px solid #cfd8dc;
        background: #f1f8e9;
        padding: 10px;
        text-align: center;
        margin-bottom: 0;
    }
    .balance-sub-title {
        font-weight: 600;
        border: 1px solid #cfd8dc;
        border-top: 0;
        background: #fafafa;
        padding: 8px;
        text-align: center;
        margin-bottom: 12px;
    }
    .balance-table th,
    .balance-table td {
        white-space: nowrap;
        font-size: 14px;
        vertical-align: middle;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow border-0 rounded-3 p-3">
        <h5 class="mb-3 text-primary">Net Fabric Balance Report</h5>

        <div class="table-responsive">
            <table class="table table-bordered balance-table">
                <thead class="table-light">
                    <tr>
                        <th>SNO</th>
                        <th>JOB WORKER NAME</th>
                        <th>QUALITY</th>
                        <th>STOCK IN MTRS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $row['job_worker_name'] }}</td>
                            <td>{{ $row['quality'] }}</td>
                            <td>{{ number_format((float) $row['stock_mtrs'], 2, '.', '') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No balance found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
