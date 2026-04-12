@extends('admin.layouts.app')

@section('style')
    <style>
        .list-report-title {
            font-weight: 700;
            text-align: center;
            border: 1px solid #cfd8dc;
            background: #d7f0dc;
            color: #1b5e20;
            padding: 8px;
            margin-bottom: 12px;
        }

        .list-report-table th,
        .list-report-table td {
            white-space: nowrap;
            font-size: 13px;
            vertical-align: middle;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid py-4">
        <div class="card shadow border-0 rounded-3 p-3">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="text-primary mb-0">List Report</h5>

                <!-- Export Button -->
                <button class="btn btn-success" onclick="exportListReportToExcel()">
                    Export to Excel
                </button>
            </div>

            <form method="GET" action="{{ route('admin.reports.listreport') }}" class="row g-2 mb-3">
                <div class="col-md-3">
                    <label class="form-label">Job Worker</label>
                    <select name="job_worker_id" class="form-control" required>
                        <option value="">Select</option>
                        @foreach ($jobWorkers as $worker)
                            <option value="{{ $worker->id }}"
                                {{ (int) $selectedWorkerId === (int) $worker->id ? 'selected' : '' }}>
                                {{ $worker->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Quality</label>
                    <select name="item_id" class="form-control" required>
                        <option value="">Select</option>
                        @foreach ($items as $item)
                            <option value="{{ $item->id }}"
                                {{ (int) $selectedItemId === (int) $item->id ? 'selected' : '' }}>
                                {{ $item->item_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">Show Report</button>
                    <a href="{{ route('admin.reports.listreport') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>

            @if ($selectedWorkerId > 0 && $selectedItemId > 0)
                <div class="list-report-title">
                    {{ optional($jobWorkers->firstWhere('id', $selectedWorkerId))->name }}
                    {{ optional($items->firstWhere('id', $selectedItemId))->item_name }}
                </div>

                @php
                    $maxRows = max($outwardRows->count(), $inwardRows->count());
                @endphp

                <div class="table-responsive">
                    <table id="listReportTable" class="table table-bordered list-report-table">
                        <thead>
                            <tr class="table-light">
                                <th colspan="7" class="text-center">OUTWARDS</th>
                                <th colspan="11" class="text-center">INWARD</th>
                            </tr>
                            <tr class="table-light">
                                <th>CH NO</th>
                                <th>QUANTITY</th>
                                <th>FOLD</th>
                                <th>LR NUMBER&TRP</th>
                                <th>GREY/BLEACH</th>
                                <th>FREIGHT(P/U)</th>
                                <th>MTRS L-100</th>

                                <th>DATE</th>
                                <th>CH NO</th>
                                <th>QUANTITY</th>
                                <th>FOLD</th>
                                <th>PROCESS</th>
                                <th>DESIGN NO</th>
                                <th>SHGK</th>
                                <th>MTRS L-100</th>
                                <th>GREY MTR</th>
                                <th>BILL NO</th>
                                <th>REMARK</th>
                            </tr>
                        </thead>

                        <tbody>
                            @for ($i = 0; $i < $maxRows; $i++)
                                @php
                                    $out = $outwardRows->get($i);
                                    $in = $inwardRows->get($i);
                                @endphp
                                <tr>
                                    <td>{{ $out['ch_no'] ?? '' }}</td>
                                    <td>{{ isset($out['quantity']) ? number_format((float) $out['quantity'], 2, '.', '') : '' }}
                                    </td>
                                    <td>{{ isset($out['fold']) ? number_format((float) $out['fold'], 2, '.', '') : '' }}
                                    </td>
                                    <td>{{ $out['lr_transport'] ?? '' }}</td>
                                    <td>{{ $out['grey_bleach'] ?? '' }}</td>
                                    <td>{{ $out['freight'] ?? '' }}</td>
                                    <td>{{ isset($out['mtrs_l100']) ? number_format((float) $out['mtrs_l100'], 3, '.', '') : '' }}
                                    </td>

                                    <td>{{ $in['date'] ?? '' }}</td>
                                    <td>{{ $in['ch_no'] ?? '' }}</td>
                                    <td>{{ isset($in['quantity']) ? number_format((float) $in['quantity'], 2, '.', '') : '' }}
                                    </td>
                                    <td>{{ isset($in['fold']) ? number_format((float) $in['fold'], 2, '.', '') : '' }}</td>
                                    <td>{{ $in['process'] ?? '' }}</td>
                                    <td>{{ $in['design_no'] ?? '' }}</td>
                                    <td>{{ isset($in['shgk']) ? number_format((float) $in['shgk'], 2, '.', '') : '' }}</td>
                                    <td>{{ isset($in['mtrs_l100']) ? number_format((float) $in['mtrs_l100'], 3, '.', '') : '' }}
                                    </td>
                                    <td>{{ isset($in['grey_mtr']) ? number_format((float) $in['grey_mtr'], 3, '.', '') : '' }}
                                    </td>
                                    <td>{{ $in['bill_no'] ?? '' }}</td>
                                    <td>{{ $in['remark'] ?? '' }}</td>
                                </tr>
                            @endfor

                            <!-- Totals -->
                            <tr class="table-light">
                                <td colspan="6" class="text-end fw-bold">OUTWARD TOTAL</td>
                                <td class="fw-bold">{{ number_format((float) $summary['outward_mtrs_l100'], 3, '.', '') }}
                                </td>
                                <td colspan="10" class="text-end fw-bold">INWARD TOTAL</td>
                                <td class="fw-bold">{{ number_format((float) $summary['inward_mtrs_l100'], 3, '.', '') }}
                                </td>
                            </tr>

                            <tr class="table-warning">
                                <td colspan="6" class="text-end fw-bold">BALANCE</td>
                                <td class="fw-bold">{{ number_format((float) $summary['balance'], 6, '.', '') }}</td>
                                <td colspan="11"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info mb-0">
                    Select Job Worker and Quality, then click Show Report.
                </div>
            @endif
        </div>
    </div>
@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <script>
        function exportListReportToExcel() {

            let table = document.getElementById("listReportTable");

            if (!table) {
                alert("No data to export");
                return;
            }

            let clonedTable = table.cloneNode(true);

            $(clonedTable).find('button, input, select').remove();

            let wb = XLSX.utils.table_to_book(clonedTable, {
                sheet: "List Report"
            });

            let ws = wb.Sheets["List Report"];

            // Auto width
            let colWidths = [];
            let range = XLSX.utils.decode_range(ws['!ref']);

            for (let C = range.s.c; C <= range.e.c; ++C) {
                let maxWidth = 10;

                for (let R = range.s.r; R <= range.e.r; ++R) {
                    let cell = ws[XLSX.utils.encode_cell({
                        r: R,
                        c: C
                    })];
                    if (cell && cell.v) {
                        maxWidth = Math.max(maxWidth, cell.v.toString().length);
                    }
                }

                colWidths.push({
                    wch: maxWidth + 2
                });
            }

            ws['!cols'] = colWidths;

            XLSX.writeFile(wb, "List_Report.xlsx");
        }
    </script>
@endsection
