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
                <h5 class="text-primary mb-0">Lot Wise Finished Goods In Stock</h5>

                <!-- Export Button -->
                <button class="btn btn-success" onclick="exportLotReportToExcel()">
                    Export to Excel
                </button>
            </div>

            <div class="report-title">
                LOT WISE REPORT OF FINISHED GOODS IN STOCK
            </div>

            <div class="table-responsive">
                <table id="lotReportTable" class="table table-bordered report-table">
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

@section('script')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- SheetJS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <script>
        function exportLotReportToExcel() {

            let table = document.getElementById("lotReportTable");

            if (!table) {
                alert("No data to export");
                return;
            }

            // Clone table (safe export)
            let clonedTable = table.cloneNode(true);

            // Remove unwanted elements
            $(clonedTable).find('button, input, select').remove();

            // Convert to workbook
            let wb = XLSX.utils.table_to_book(clonedTable, {
                sheet: "Lot Wise Report"
            });

            let ws = wb.Sheets["Lot Wise Report"];

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

            // Download Excel
            XLSX.writeFile(wb, "Lot_Wise_Finished_Goods_Report.xlsx");
        }
    </script>
@endsection
