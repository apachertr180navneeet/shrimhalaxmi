@extends('admin.layouts.app')

@section('style')
    <style>
        .report-title {}

        .report-subtitle {
            font-weight: 600;
            text-align: center;
            border: 1px solid #cfd8dc;
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

        tfoot tr {
            background: #e9ecef;
            font-weight: bold;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid py-4">
        <div class="card shadow border-0 rounded-3 p-3">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="text-primary mb-0">Grey Lot Balance Report</h5>

                <!-- Export Button -->
                <button class="btn btn-success" onclick="exportGreyLotToExcel()">
                    Export to Excel
                </button>
            </div>

            <div class="report-title"></div>

            <div class="report-subtitle">
                GREY LOT BALANCE REPORT (SORTED BY DATE)
            </div>

            <div class="table-responsive">
                <table id="greyLotTable" class="table table-bordered report-table">
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
                        @php $totalQty = 0; @endphp

                        @forelse ($rows as $row)
                            @php $totalQty += (float) $row['quantity']; @endphp
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

                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-end">TOTAL</td>
                            <td>{{ number_format($totalQty, 2, '.', '') }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>

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
        function exportGreyLotToExcel() {

            let table = document.getElementById("greyLotTable");

            if (!table) {
                alert("No data to export");
                return;
            }

            // Clone table
            let clonedTable = table.cloneNode(true);

            // Remove unwanted elements
            $(clonedTable).find('button, input, select').remove();

            // Convert to workbook
            let wb = XLSX.utils.table_to_book(clonedTable, {
                sheet: "Grey Lot Balance"
            });

            let ws = wb.Sheets["Grey Lot Balance"];

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
            XLSX.writeFile(wb, "Grey_Lot_Balance_Report.xlsx");
        }
    </script>
@endsection
