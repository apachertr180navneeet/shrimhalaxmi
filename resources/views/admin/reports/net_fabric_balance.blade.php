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

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="text-primary mb-0">Net Fabric Balance Report</h5>

                <!-- Export Button -->
                <button class="btn btn-success" onclick="exportBalanceToExcel()">
                    Export to Excel
                </button>
            </div>

            <div class="table-responsive">
                <table id="balanceTable" class="table table-bordered balance-table">
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

@section('script')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- SheetJS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <script>
        function exportBalanceToExcel() {

            let table = document.getElementById("balanceTable");

            // Clone table
            let clonedTable = table.cloneNode(true);

            // Clean unwanted elements
            $(clonedTable).find('button, input, select').remove();

            // Convert to workbook
            let wb = XLSX.utils.table_to_book(clonedTable, {
                sheet: "Net Fabric Balance"
            });

            let ws = wb.Sheets["Net Fabric Balance"];

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
            XLSX.writeFile(wb, "Net_Fabric_Balance_Report.xlsx");
        }
    </script>
@endsection
