@extends('admin.layouts.app')


@section('style')
<style>
/* =========================
    TABLE UI STYLING
========================== */
#dispatchTable th, 
#dispatchTable td {
    padding: 12px 14px;
    vertical-align: middle;
    font-size: 14px;
}
#dispatchTable thead th {
    background: #f5f5f9;
    color: #566a7f;
    font-weight: 600;
    white-space: nowrap;
}
#dispatchTable th:nth-child(6),
#dispatchTable th:nth-child(7),
#dispatchTable td:nth-child(6),
#dispatchTable td:nth-child(7) {
    text-align: center;
}
#dispatchTable tbody tr:hover {
    background-color: #f9fafb;
}
.search-box {
    width: 180px;
}
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm border-0 rounded-4 p-3">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2 row">
            <div class="col-md-3">
                <h5 class="mb-0 text-primary">Order Dispatch Management</h5>
            </div>
            <div class="d-flex gap-2 col-md-5">
                <input type="text" id="customerName" class="form-control search-box" placeholder="Customer Name">
                <input type="date" id="dispatchDate" class="form-control search-box">
                <input type="text" id="globalSearch" class="form-control search-box" placeholder="Search here">
            </div>
            <div class="col-md-2 text-end">
                <a href="{{ route('admin.orderdispatches.create') }}" class="btn btn-primary">
                    Add Dispatch
                </a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle w-100" id="dispatchTable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>CH No</th>
                        <th>Customer</th>
                        <th>Number</th>
                        <th>Transport</th>
                        <th>Status</th>
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
    let table = $('#dispatchTable').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        responsive: true,
        ajax: {
            url: "{{ route('admin.orderdispatches.getall') }}",
            data: function (d) {
                d.customer_name = $('#customerName').val();
                d.dispatch_date = $('#dispatchDate').val();
                d.search_value = $('#globalSearch').val();
            }
        },
        searching: false,
        columns: [
            { data: 'date', name: 'dispatch_date' },
            { data: 'dispatch_no', name: 'dispatch_no' },
            { data: 'customer_name', orderable: false, searchable: false },
            { data: 'mobile_number', name: 'mobile_number' },
            { data: 'transport', name: 'transport' },
            { data: 'status', orderable: false, searchable: false },
            { data: 'action', orderable: false, searchable: false }
        ]
    });

    let delayTimer;
    $('#customerName, #globalSearch').on('keyup', function () {
        clearTimeout(delayTimer);
        delayTimer = setTimeout(function () {
            table.ajax.reload();
        }, 400);
    });
    $('#dispatchDate').on('change', function () {
        table.ajax.reload();
    });

    $(document).on('click', '.deleteBtn', function () {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: 'Delete this order dispatch?',
            icon: 'warning',
            showCancelButton: true,
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/order-dispatches/delete/' + id,
                    type: 'DELETE',
                    data: { _token: "{{ csrf_token() }}" },
                    success: function () {
                        toastr.success('Order dispatch deleted successfully');
                        table.ajax.reload();
                    }
                });
            }
        });
    });
});
</script>
@endsection