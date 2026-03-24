@extends('admin.layouts.app')

@section('style')
<style>
    #purchaseTable {
        width: 100% !important;
        border-collapse: collapse;
    }

    #purchaseTable th,
    #purchaseTable td {
        padding: 10px 12px;
        font-size: 14px;
        vertical-align: middle;
    }

    #purchaseTable thead th {
        background: #f5f5f9;
        color: #566a7f;
        font-weight: 600;
        white-space: nowrap;
    }

    #purchaseTable tbody tr:hover {
        background: #f9fafb;
    }

    .search-box {
        width: 180px;
    }

    @media (max-width: 575.98px) {
        .search-box {
            width: 100%;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow border-0 rounded-3 p-3">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2 row">
            <div class="col-md-3">
                <h5 class="mb-0 text-primary">Purchase Management</h5>
            </div>

            <div class="d-flex gap-2 col-md-7 flex-wrap">
                <input type="text" id="vendorName" class="form-control search-box" placeholder="Vendor">
                <input type="date" id="purchaseDate" class="form-control search-box">
                <input type="text" id="globalSearch" class="form-control search-box" placeholder="Search">
                <button type="button" id="searchBtn" class="btn btn-primary">Search</button>
                <a href="{{ route('admin.purchases.create') }}" class="btn btn-primary">Add</a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle" id="purchaseTable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>PCH. NO.</th>
                        <th>BNO</th>
                        <th>Vendor Name</th>
                        <th>Freight</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(function () {
    let table = $('#purchaseTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.purchases.getall') }}",
            data: function (d) {
                d.vendor_name = $('#vendorName').val();
                d.purchase_date = $('#purchaseDate').val();
                d.search_value = $('#globalSearch').val();
            }
        },
        searching: false,
        columns: [
            { data: 'date', name: 'purchase_date' },
            { data: 'pch_no', name: 'pch_no' },
            { data: 'bno', name: 'bno' },
            { data: 'vendor_name', orderable: false, searchable: false },
            { data: 'freight', name: 'freight' },
            { data: 'action', orderable: false, searchable: false }
        ]
    });

    $('#searchBtn').on('click', function () {
        table.draw();
    });

    $('#vendorName, #globalSearch').on('keyup', function () {
        table.draw();
    });

    $('#purchaseDate').on('change', function () {
        table.draw();
    });

    $(document).on('click', '.deleteBtn', function () {
        let id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: 'Delete this purchase?',
            icon: 'warning',
            showCancelButton: true,
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/purchases/delete/' + id,
                    type: 'DELETE',
                    data: { _token: "{{ csrf_token() }}" },
                    success: function () {
                        toastr.success('Purchase deleted successfully');
                        table.ajax.reload();
                    }
                });
            }
        });
    });
});
</script>
@endsection
