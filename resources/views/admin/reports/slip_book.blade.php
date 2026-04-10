@extends('admin.layouts.app')

@section('style')
<style>
    .slip-book-title {
        font-weight: 700;
        color: #2e7d32;
        border: 1px solid #cfd8dc;
        background: #f1f8e9;
        padding: 10px;
        text-align: center;
        margin-bottom: 12px;
    }
    .slip-book-table th,
    .slip-book-table td {
        white-space: nowrap;
        font-size: 13px;
        vertical-align: middle;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow border-0 rounded-3 p-3">
        <h5 class="mb-3 text-primary">Slip Book Report</h5>

        <div class="table-responsive">
            <table class="table table-bordered slip-book-table">
                <thead class="table-light">
                    <tr>
                        <th>DATE</th>
                        <th>SLIP NO</th>
                        <th>PARTY NAME</th>
                        <th>QUALITY</th>
                        <th>PRINT/DYED/RFD/GRI</th>
                        <th>DESIGN NO</th>
                        <th>QUANTITY</th>
                        <th>FOLD</th>
                        <th>SUPPLIER LOT NUMBER</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        @php
                            $assignment = $row->assignment;
                            $processName = trim((string) ($row->processItem?->item_name ?? ''));
                            if ($processName === '') {
                                $rawProcess = trim((string) $row->process);
                                $processName = is_numeric($rawProcess) ? '' : $rawProcess;
                            }
                        @endphp
                        <tr>
                            <td>{{ optional($assignment?->assignment_date)->format('d.m.Y') ?: '-' }}</td>
                            <td>{{ $assignment?->assign_no ?: '-' }}</td>
                            <td>{{ $assignment?->jobWorker?->name ?: '-' }}</td>
                            <td>{{ $row->item?->item_name ?: '-' }}</td>
                            <td>{{ $processName ?: '-' }}</td>
                            <td>{{ $row->colour ?: ($row->quality ?? '-') }}</td>
                            <td>{{ number_format((float) $row->meter, 2, '.', '') }}</td>
                            <td>{{ number_format((float) $row->fold, 2, '.', '') }}</td>
                            <td>{{ $row->lot_no ?: '-' }}</td>
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
