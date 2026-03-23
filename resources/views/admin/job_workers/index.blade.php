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
    white-space: nowrap;
}

/* Center align status & action */
#jobWorkerTable th:nth-child(6),
#jobWorkerTable th:nth-child(7),
#jobWorkerTable td:nth-child(6),
#jobWorkerTable td:nth-child(7) {
    text-align: center;
}

/* Row hover */
#jobWorkerTable tbody tr:hover {
    background-color: #f9fafb;
}

/* Search input */
.search-box {
    width: 180px;
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
            HEADER + SEARCH
        ========================== -->
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2 row">

            <!-- Title -->
            <div class="col-md-3">
                <h5 class="mb-0 text-primary">Job Worker Management</h5>
            </div>

            <!-- Filters -->
            <div class="d-flex gap-2 col-md-5">

                <input type="text" id="search_name" 
                    class="form-control search-box" 
                    placeholder="Job Worker">

                <input type="text" id="search_global" 
                    class="form-control search-box" 
                    placeholder="Search here">

            </div>

            <!-- Add Button -->
            <div class="col-md-2 text-end">
                <a href="{{ route('admin.jobworkers.create') }}" class="btn btn-primary">
                    Add Job Worker
                </a>
            </div>

        </div>

        <!-- =========================
            TABLE
        ========================== -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle w-100" id="jobWorkerTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Job Worker Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>City</th>
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

    /**
     * =========================
     * DATATABLE INIT
     * =========================
     */
    let table = $('#jobWorkerTable').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        responsive: true,

        ajax: {
            url: "{{ route('admin.jobworkers.getall') }}",
            data: function (d) {
                d.name = $('#search_name').val();
                d.search_value = $('#search_global').val();
            }
        },

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
     * AUTO SEARCH (DEBOUNCE)
     * =========================
     */
    let delayTimer;

    $('#search_name, #search_global').on('keyup', function () {

        clearTimeout(delayTimer);

        delayTimer = setTimeout(function () {
            table.ajax.reload();
        }, 400);

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