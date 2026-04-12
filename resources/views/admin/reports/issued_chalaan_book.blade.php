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

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="text-primary mb-0">Fabric Issued To Job Workers</h5>

                <!-- Export Button -->
                <button class="btn btn-success" onclick="exportFabricIssuedToExcel()">
                    Export to Excel
                </button>
            </div>

            <div class="report-title">
                DETAILS OF ALL THE FABRIC ISSUED TO JOB WORKERS WHOLE CHALAAN BOOK
            </div>

            <div class="table-responsive">
                <table id="fabricIssuedTable" class="table table-bordered report-table">
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

@section('script')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- SheetJS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <script>
        function exportFabricIssuedToExcel() {

            let table = document.getElementById("fabricIssuedTable");

            if (!table) {
                alert("No data to export");
                return;
            }

            // Clone table (safe)
            let clonedTable = table.cloneNode(true);

            // Remove unwanted elements
            $(clonedTable).find('button, input, select').remove();

            // Convert to workbook
            let wb = XLSX.utils.table_to_book(clonedTable, {
                sheet: "Fabric Issued"
            });

            let ws = wb.Sheets["Fabric Issued"];

            // Auto column width
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
            XLSX.writeFile(wb, "Fabric_Issued_To_Job_Workers.xlsx");
        }
    </script>
@endsection
