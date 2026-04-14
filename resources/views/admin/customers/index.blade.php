@extends('admin.layouts.app')

@section('style')
<style>

/* =========================
    TABLE FIX
========================== */

#customerTable {
    width: 100% !important;
    border-collapse: collapse;
}

#customerTable th, 
#customerTable td {
    padding: 10px 12px;
    font-size: 14px;
    vertical-align: middle;
}

/* Header */
#customerTable thead th {
    background: #f5f5f9;
    color: #566a7f;
    font-weight: 600;
    white-space: nowrap;
}

/* Align center */
#customerTable th:nth-child(1),
#customerTable th:nth-child(6),
#customerTable th:nth-child(7),
#customerTable td:nth-child(1),
#customerTable td:nth-child(6),
#customerTable td:nth-child(7) {
    text-align: center;
}

/* Row hover */
#customerTable tbody tr:hover {
    background: #f9fafb;
}

/* Fix width issue */
.dataTables_wrapper {
    width: 100%;
}

/* =========================
    SEARCH
========================== */
.search-box {
    width: 160px;
}

/* =========================
    TOGGLE SWITCH
========================== */
.switch {
    position: relative;
    display: inline-block;
    width: 45px;
    height: 22px;
}

.switch input {
    opacity: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    background-color: #d9dee3;
    border-radius: 30px;
    top: 0; left: 0; right: 0; bottom: 0;
    transition: 0.3s;
}

.slider:before {
    content: "";
    position: absolute;
    height: 16px;
    width: 16px;
    left: 3px;
    bottom: 3px;
    background: #fff;
    border-radius: 50%;
    transition: 0.3s;
}

input:checked + .slider {
    background-color: #696cff;
}

input:checked + .slider:before {
    transform: translateX(22px);
}

</style>
@endsection


@section('content')

<div class="container-fluid py-4">

    <div class="card shadow border-0 rounded-3 p-3">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2 row">

            <!-- Page Title -->
            <div class="col-md-3">
                <h5 class="mb-0 text-primary">Customer Management</h5>
            </div>
        
            <!-- Search Inputs -->
            <div class="d-flex gap-2 col-md-5">
                <!-- Specific search -->
                <input type="text" id="search_name" class="form-control search-box" placeholder="Name">

                <input type="text" id="search_location" class="form-control search-box" placeholder="Location">

                <!-- Global search -->
                <input type="text" id="search_global" class="form-control search-box" placeholder="Search">
            </div>

            <!-- Add Vendor Button -->
            <div class="col-md-2">
                <a href="{{ route('admin.customers.create') }}" class="btn btn-primary">
                    Add Customer
                </a>
            </div>

        </div>

        <!-- TABLE -->
        <div class="table-responsive">
            <table class="table table-bordered align-middle" id="customerTable">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer Name</th>
                        <th>Mobile</th>
                        <th>Email</th>
                        <th>Address</th>
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

    let table = $('#customerTable').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        responsive: true,

        ajax: {
            url: "{{ route('admin.customers.getall') }}",
            data: function (d) {
                d.name = $('#search_name').val();
                d.location = $('#search_location').val();
                d.search_value = $('#search_global').val();
            }
        },

        searching: false,

        columns: [
            { data: 'DT_RowIndex', orderable: false },
            { data: 'name' },
            { data: 'phone' },
            { data: 'email' },
            { data: 'location' },
            { data: 'status', orderable: false },
            { data: 'action', orderable: false }
        ]
    });

    /* AUTO SEARCH */
    let delay;
    $('#search_name, #search_location, #search_global').on('keyup', function () {

        clearTimeout(delay);

        delay = setTimeout(() => {
            table.ajax.reload();
        }, 400);

    });

    /* STATUS */
    $(document).on('change', '.statusToggle', function () {

        let id = $(this).data('id');

        $.post("{{ route('admin.customers.status') }}", {
            _token: "{{ csrf_token() }}",
            id: id
        }, function () {
            table.ajax.reload();
        });

    });

    /* DELETE */
    $(document).on('click', '.deleteBtn', function () {

        let id = $(this).data('id');
        let deleteUrl = "{{ route('admin.customers.delete', ':id') }}".replace(':id', id);

        if(confirm('Delete this customer?')) {

            $.ajax({
                url: deleteUrl,
                type: 'DELETE',
                data: { _token: "{{ csrf_token() }}" },
                success: function () {
                    table.ajax.reload();
                }
            });

        }

    });

});
</script>

@endsection
