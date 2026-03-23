@extends('admin.layouts.app')

@section('style')
<style>

    /* =========================
        TABLE UI STYLING
    ========================== */

    /* Table cell spacing & font */
    #vendorTable th, 
    #vendorTable td {
        padding: 12px 14px;
        vertical-align: middle;
        font-size: 14px;
    }

    /* Table header styling */
    #vendorTable thead th {
        background: #f5f5f9;
        color: #566a7f;
        font-weight: 600;
    }

    /* Row hover effect */
    #vendorTable tbody tr:hover {
        background-color: #f9fafb;
        transition: 0.2s;
    }

    /* =========================
        STATUS BADGE (OPTIONAL)
    ========================== */

    .status-badge {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
    }

    .active-status {
        background: #e7f4e8;
        color: #28a745;
    }

    .inactive-status {
        background: #fdecea;
        color: #dc3545;
    }

    /* =========================
        ACTION BUTTONS
    ========================== */
    .action-btns .btn {
        margin: 0 3px;
    }

    /* =========================
        TOGGLE SWITCH UI
    ========================== */

    .switch {
        position: relative;
        display: inline-block;
        width: 45px;
        height: 22px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
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
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 3px;
        bottom: 3px;
        background: white;
        border-radius: 50%;
        transition: 0.3s;
    }

    /* Toggle ON state */
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

    <div class="row g-4">

        <div class="col-lg-12">
            <div class="card shadow-sm border-0 rounded-4 p-3">

                <!-- =========================
                    HEADER SECTION
                ========================== -->
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2 row">

                    <!-- Page Title -->
                    <div class="col-md-3">
                        <h5 class="mb-0 text-primary">Vendor Management</h5>
                    </div>
                
                    <!-- Search Inputs -->
                    <div class="d-flex gap-2 col-md-5">
                        <!-- Specific search -->
                        <input type="text" id="vendorName" class="form-control" placeholder="Vendor Name">

                        <!-- Global search -->
                        <input type="text" id="globalSearch" class="form-control" placeholder="Search here">
                    </div>

                    <!-- Add Vendor Button -->
                    <div class="col-md-2">
                        <a href="{{ route('admin.vendors.create') }}" class="btn btn-primary">
                            Add Vendor
                        </a>
                    </div>

                </div>

                <!-- =========================
                    DATA TABLE
                ========================== -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle" id="vendorTable" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Vendor Name</th>
                                <th>Firm Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>City</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>

</div>

@endsection


@section('script')

<script>
$(function () {

    /**
     * =========================
     * INITIALIZE DATATABLE
     * =========================
     */
    let table = $('#vendorTable').DataTable({
        processing: true,
        serverSide: true,

        // AJAX with custom filters
        ajax: {
            url: "{{ route('admin.vendors.getall') }}",
            data: function (d) {
                d.vendor_name = $('#vendorName').val();   // Vendor name filter
                d.search_value = $('#globalSearch').val(); // Global search
            }
        },

        // Disable default search box
        searching: false,

        columns: [
            { data: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'vendor_name' },
            { data: 'firm_name' },
            { data: 'phone' },
            { data: 'email' },
            { data: 'city' },
            { data: 'status', orderable: false, searchable: false },
            { data: 'action', orderable: false, searchable: false }
        ]
    });


    /**
     * =========================
     * SEARCH INPUT HANDLING
     * =========================
     */
    $('#vendorName, #globalSearch').on('keyup', function () {
        table.draw(); // reload table with filters
    });


    /**
     * =========================
     * STATUS TOGGLE
     * =========================
     */
    $(document).on('change', '.statusToggle', function () {

        let checkbox = $(this);
        let id = checkbox.data('id');
        let isChecked = checkbox.prop('checked');

        Swal.fire({
            title: 'Are you sure?',
            text: "Change vendor status?",
            icon: 'warning',
            showCancelButton: true,
        }).then((result) => {

            if (result.isConfirmed) {

                $.post("{{ route('admin.vendors.status') }}", {
                    _token: "{{ csrf_token() }}",
                    id: id
                }, function () {

                    toastr.success('Status updated successfully');
                    table.ajax.reload();

                });

            } else {
                // revert toggle if cancelled
                checkbox.prop('checked', !isChecked);
            }

        });

    });


    /**
     * =========================
     * DELETE VENDOR
     * =========================
     */
    $(document).on('click', '.deleteBtn', function () {

        let id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "Delete this vendor?",
            icon: 'warning',
            showCancelButton: true,
        }).then((result) => {

            if (result.isConfirmed) {

                $.ajax({
                    url: '/admin/vendors/delete/' + id,
                    type: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function () {

                        toastr.success('Vendor deleted successfully');
                        table.ajax.reload();
                    }
                });

            }

        });

    });

});
</script>

@endsection