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

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="text-primary mb-0">Slip Book Report</h5>

                <!-- Export Button -->
                <button class="btn btn-success" onclick="exportTableToExcel()">
                    Export to Excel
                </button>
            </div>

            <div class="table-responsive">
                <table id="slipBookTable" class="table table-bordered slip-book-table">
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
                                <td>{{ optional($assignment?->assignment_date)->format('d/m/Y') ?: '-' }}</td>
                                <td>{{ $assignment?->assign_no ?: '-' }}</td>
                                <td>{{ $assignment?->jobWorker?->name ?: '-' }}</td>
                                <td>{{ $row->item?->item_name ?: '-' }}</td>
                                <td>{{ $processName ?: '-' }}</td>
                                <td>{{ $row->colour ?: $row->quality ?? '-' }}</td>
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

@section('script')
    <!-- jQuery (if not already included) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- SheetJS (Excel Export Library) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <script>
        function exportTableToExcel() {

            // Get table
            let table = document.getElementById("slipBookTable");

            // Clone table (so UI not affected)
            let clonedTable = table.cloneNode(true);

            // Remove unwanted elements if any
            $(clonedTable).find('button, input, select').remove();

            // Convert to workbook
            let wb = XLSX.utils.table_to_book(clonedTable, {
                sheet: "Slip Book Report"
            });

            // Auto column width (optional improvement)
            let ws = wb.Sheets["Slip Book Report"];
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

            // Export file
            XLSX.writeFile(wb, "Slip_Book_Report.xlsx");
        }
    </script>
@endsection
