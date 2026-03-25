@extends('admin.layouts.app')

@section('style')
<style>
    #roleTable {
        width: 100% !important;
        border-collapse: collapse;
    }

    #roleTable th,
    #roleTable td {
        padding: 10px 12px;
        font-size: 14px;
        vertical-align: middle;
    }

    #roleTable thead th {
        background: #f5f5f9;
        color: #566a7f;
        font-weight: 600;
        white-space: nowrap;
    }

    #roleTable tbody tr:hover {
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
                <h5 class="mb-0 text-primary">Role Management</h5>
            </div>

            <div class="d-flex gap-2 col-md-7 flex-wrap">
                <input type="text" id="globalSearch" class="form-control search-box" placeholder="Search role">

                <button type="button" id="searchBtn" class="btn btn-primary">Search</button>

                @can('role-create')
                <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">Add Role</a>
                @endcan
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle" id="roleTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Role Name</th>
                        {{--  <th>Permissions</th>  --}}
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

    let table = $('#roleTable').DataTable({
        processing: true,
        serverSide: true,
        lengthChange: false,
        searching: false,

        ajax: {
            url: "{{ route('admin.roles.getall') }}",
            data: function (d) {
                d.search_value = $('#globalSearch').val();
            }
        },

        columns: [
            { data: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            //{ data: 'permissions', orderable: false, searchable: false },
            { data: 'action', orderable: false, searchable: false }
        ]
    });

    $('#searchBtn').on('click', function () {
        table.draw();
    });

    $('#globalSearch').on('keyup', function () {
        table.draw();
    });

    // ✅ Delete Role
    $(document).on('click', '.deleteBtn', function () {

        let id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: 'Delete this role?',
            icon: 'warning',
            showCancelButton: true,
        }).then((result) => {

            if (result.isConfirmed) {

                $.ajax({
                    url: '/admin/roles/delete/' + id,
                    type: 'DELETE',
                    data: { _token: "{{ csrf_token() }}" },

                    success: function () {
                        toastr.success('Role deleted successfully');
                        table.ajax.reload();
                    }
                });
            }
        });
    });

});
</script>
@endsection