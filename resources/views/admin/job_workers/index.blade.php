@extends('admin.layouts.app')

@section('style')
<style>

/* =========================
    TABLE UI STYLING
========================== */

#jobWorkerTable th, 
#jobWorkerTable td {
    padding: 12px 14px;
    vertical-align: middle;
    font-size: 14px;
}

#jobWorkerTable thead th {
    background: #f5f5f9;
    color: #566a7f;
    font-weight: 600;
}

#jobWorkerTable tbody tr:hover {
    background-color: #f9fafb;
    transition: 0.2s;
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

    <div class="card shadow-sm border-0 rounded-4 p-3">

        <!-- =========================
            HEADER
        ========================== -->
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">

            <h5 class="mb-0 text-primary">Job Worker Management</h5>

            <a href="{{ route('admin.jobworkers.create') }}" class="btn btn-primary">
                Add Job Worker
            </a>

        </div>

        <!-- =========================
            TABLE
        ========================== -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle" id="jobWorkerTable" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Job Worker Name</th>
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

@endsection


@section('script')

<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Toastr -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
$(function () {

    /**
     * =========================
     * DATATABLE INIT
     * =========================
     */
    let table = $('#jobWorkerTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.jobworkers.getall') }}",
        searching: false,

        columns: [
            { data: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name' },
            { data: 'phone' },
            { data: 'email' },
            { data: 'city' },
            { data: 'status', orderable: false, searchable: false },
            { data: 'action', orderable: false, searchable: false }
        ]
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
            text: "Change job worker status?",
            icon: 'warning',
            showCancelButton: true,
        }).then((result) => {

            if (result.isConfirmed) {

                $.post("{{ route('admin.jobworkers.status') }}", {
                    _token: "{{ csrf_token() }}",
                    id: id
                }, function () {

                    toastr.success('Status updated successfully');
                    table.ajax.reload();

                });

            } else {
                checkbox.prop('checked', !isChecked);
            }

        });

    });


    /**
     * =========================
     * DELETE
     * =========================
     */
    $(document).on('click', '.deleteBtn', function () {

        let id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "Delete this job worker?",
            icon: 'warning',
            showCancelButton: true,
        }).then((result) => {

            if (result.isConfirmed) {

                $.ajax({
                    url: '/admin/jobworkers/delete/' + id,
                    type: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function () {

                        toastr.success('Deleted successfully');
                        table.ajax.reload();
                    }
                });

            }

        });

    });

});
</script>

@endsection