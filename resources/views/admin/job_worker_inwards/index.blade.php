@extends('admin.layouts.app')

@section('style')
<style>
    #jobWorkerInwardTable {
        width: 100% !important;
        border-collapse: collapse;
    }

    #jobWorkerInwardTable th,
    #jobWorkerInwardTable td {
        padding: 10px 12px;
        font-size: 14px;
        vertical-align: middle;
    }

    #jobWorkerInwardTable thead th {
        background: #f5f5f9;
        color: #566a7f;
        font-weight: 600;
        white-space: nowrap;
    }

    #jobWorkerInwardTable tbody tr:hover {
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
                <h5 class="mb-0 text-primary">Job Worker Inward</h5>
            </div>

            <div class="d-flex gap-2 col-md-7 flex-wrap">
                <input type="text" id="globalSearch" class="form-control search-box" placeholder="Search here">
                <button type="button" id="searchBtn" class="btn btn-primary">Search</button>
                <a href="{{ route('admin.jobworkerinwards.create') }}" class="btn btn-primary">Add</a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle" id="jobWorkerInwardTable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>CH No.</th>
                        <th>Job Worker</th>
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
    let table = $('#jobWorkerInwardTable').DataTable({
        processing: true,
        serverSide: true,
        lengthChange: false,
        ajax: {
            url: "{{ route('admin.jobworkerinwards.getall') }}",
            data: function (d) {
                d.inward_date = $('#inwardDate').val();
                d.ch_no = $('#chNo').val();
                d.search_value = $('#globalSearch').val();
                d.item_name = $('#itemName').val();
            }
        },
        searching: false,
        columns: [
            { data: 'date', name: 'inward_date' },
            { data: 'ch_no', name: 'ch_no' },
            { data: 'job_worker_name', orderable: false, searchable: false },
            { data: 'action', orderable: false, searchable: false }
        ]
    });

    $('#searchBtn').on('click', function () {
        table.draw();
    });

    $('#chNo, #globalSearch, #itemName').on('keyup', function () {
        let value = $(this).val();
        $('#globalSearch, #itemName').val(value);
        table.draw();
    });

    $('#inwardDate').on('change', function () {
        table.draw();
    });

    $('#showEntries').on('change', function () {
        table.page.len(parseInt($(this).val())).draw();
    });

    $(document).on('click', '.deleteBtn', function () {
        let id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: 'Delete this job worker inward?',
            icon: 'warning',
            showCancelButton: true,
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/job-worker-inwards/delete/' + id,
                    type: 'DELETE',
                    data: { _token: "{{ csrf_token() }}" },
                    success: function () {
                        toastr.success('Job worker inward deleted successfully');
                        table.ajax.reload();
                    }
                });
            }
        });
    });
});
</script>
@endsection
