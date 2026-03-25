@extends('admin.layouts.app')

@section('style')
<style>
#memberTable th, #memberTable td {
    padding: 12px 14px;
    vertical-align: middle;
    font-size: 14px;
}

#memberTable thead th {
    background: #f5f5f9;
    color: #566a7f;
    font-weight: 600;
}

#memberTable tbody tr:hover {
    background-color: #f9fafb;
}

.switch {
    position: relative;
    display: inline-block;
    width: 45px;
    height: 22px;
}

.switch input { opacity: 0; }

.slider {
    position: absolute;
    cursor: pointer;
    background-color: #d9dee3;
    border-radius: 30px;
    top: 0; left: 0; right: 0; bottom: 0;
}

.slider:before {
    content: "";
    height: 16px;
    width: 16px;
    left: 3px;
    bottom: 3px;
    position: absolute;
    background: white;
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
    <div class="card p-3">

        <!-- Header -->
        <div class="row mb-3">
            <div class="col-md-3">
                <h5 class="text-primary">Member Management</h5>
            </div>

            <div class="col-md-5 d-flex gap-2">
                <input type="text" id="memberName" class="form-control" placeholder="Member Name">
                <input type="text" id="globalSearch" class="form-control" placeholder="Search here">
            </div>

            <div class="col-md-2">
                <a href="{{ route('admin.members.create') }}" class="btn btn-primary">Add Member</a>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-bordered" id="memberTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Mobile</th>
                        <th>Email</th>
                        <th>Role</th>
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

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Toastr -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
$(function () {

    let table = $('#memberTable').DataTable({
        processing: true,
        serverSide: true,

        ajax: {
            url: "{{ route('admin.members.getAll') }}",
            data: function (d) {
                d.member_name = $('#memberName').val();
                d.search_value = $('#globalSearch').val();
            }
        },

        searching: false,

        columns: [
            { data: 'DT_RowIndex', orderable: false },
            { data: 'full_name' },
            { data: 'phone' },
            { data: 'email' },
            { data: 'role' },
            { data: 'status', orderable: false },
            { data: 'action', orderable: false }
        ]
    });

    // Search Filters
    $('#memberName, #globalSearch').keyup(function () {
        table.draw();
    });

    // ✅ Status Toggle
    $(document).on('change', '.statusToggle', function () {

        let id = $(this).data('id');

        $.post("{{ route('admin.members.status') }}", {
            _token: "{{ csrf_token() }}",
            id: id
        }, function () {

            toastr.success('Status updated successfully');

            table.ajax.reload(null, false);
        });
    });

    // ✅ Delete with SweetAlert
    $(document).on('click', '.deleteBtn', function () {

        let id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "This member will be permanently deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#696cff',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {

            if (result.isConfirmed) {

                $.ajax({
                    url: '/admin/members/delete/' + id,
                    type: 'DELETE',
                    data: { _token: "{{ csrf_token() }}" },
                    success: function () {

                        toastr.success('Member deleted successfully');

                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: 'Member has been deleted.',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        table.ajax.reload(null, false);
                    },
                    error: function () {
                        toastr.error('Something went wrong!');
                    }
                });

            }
        });

    });

});
</script>

@endsection