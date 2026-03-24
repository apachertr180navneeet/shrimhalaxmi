@extends('admin.layouts.app')

@section('style')
<style>
    #jobWorkAssignmentTable {
        width: 100% !important;
        border-collapse: collapse;
    }

    #jobWorkAssignmentTable th,
    #jobWorkAssignmentTable td {
        padding: 10px 12px;
        font-size: 14px;
        vertical-align: middle;
    }

    #jobWorkAssignmentTable thead th {
        background: #f5f5f9;
        color: #566a7f;
        font-weight: 600;
        white-space: nowrap;
    }

    #jobWorkAssignmentTable tbody tr:hover {
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
                <h5 class="mb-0 text-primary">Job Work Assignment</h5>
            </div>

            <div class="d-flex gap-2 col-md-7 flex-wrap">
                <input type="text" id="assignNo" class="form-control search-box" placeholder="Assign No.">
                <input type="text" id="globalSearch" class="form-control search-box" placeholder="Search here">
                <button type="button" id="searchBtn" class="btn btn-primary">Search</button>
                <a href="{{ route('admin.jobworkassignments.create') }}" class="btn btn-primary">Add</a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle" id="jobWorkAssignmentTable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Assign No.</th>
                        <th>Job Worker</th>
                        <th>LR No.</th>
                        <th>Process</th>
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
    let table = $('#jobWorkAssignmentTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.jobworkassignments.getall') }}",
            data: function (d) {
                d.assign_no = $('#assignNo').val();
                d.search_value = $('#globalSearch').val();
            }
        },
        searching: false,
        columns: [
            { data: 'date', name: 'assignment_date' },
            { data: 'assign_no', name: 'assign_no' },
            { data: 'job_worker_name', orderable: false, searchable: false },
            { data: 'lr_no', orderable: false, searchable: false },
            { data: 'process', orderable: false, searchable: false },
            { data: 'freight', name: 'freight' },
            { data: 'action', orderable: false, searchable: false }
        ]
    });

    $('#searchBtn').on('click', function () {
        table.draw();
    });

    $('#assignNo, #globalSearch').on('keyup', function () {
        table.draw();
    });

    $(document).on('click', '.deleteBtn', function () {
        let id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: 'Delete this job work assignment?',
            icon: 'warning',
            showCancelButton: true,
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/job-work-assignments/delete/' + id,
                    type: 'DELETE',
                    data: { _token: "{{ csrf_token() }}" },
                    success: function () {
                        toastr.success('Job work assignment deleted successfully');
                        table.ajax.reload();
                    }
                });
            }
        });
    });
});
</script>
@endsection
