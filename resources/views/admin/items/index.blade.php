@extends('admin.layouts.app')

@section('style')
<style>
    #itemTable th,
    #itemTable td {
        padding: 12px 14px;
        vertical-align: middle;
        font-size: 14px;
    }

    #itemTable thead th {
        background: #f5f5f9;
        color: #566a7f;
        font-weight: 600;
    }

    #itemTable tbody tr:hover {
        background-color: #f9fafb;
        transition: 0.2s;
    }

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
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
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
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2 row">
                    <div class="col-md-3">
                        <h5 class="mb-0 text-primary">Item Management</h5>
                    </div>

                    <div class="d-flex gap-2 col-md-5">
                        <input type="text" id="itemName" class="form-control" placeholder="Item Name">
                        <input type="text" id="globalSearch" class="form-control" placeholder="Search here">
                    </div>

                    <div class="col-md-2">
                        <a href="{{ route('admin.items.create') }}" class="btn btn-primary">
                            Add Item
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle" id="itemTable" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Short Code</th>
                                <th>Item Name</th>
                                <th>Qty (Meter) / Balance</th>
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
    let table = $('#itemTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.items.getall') }}",
            data: function (d) {
                d.item_name = $('#itemName').val();
                d.search_value = $('#globalSearch').val();
            }
        },
        searching: false,
        columns: [
            { data: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'short_code', name: 'abbr' },
            { data: 'item_name', name: 'item_name' },
            { data: 'qty_balance', orderable: false, searchable: false },
            { data: 'status', orderable: false, searchable: false },
            { data: 'action', orderable: false, searchable: false }
        ]
    });

    $('#itemName, #globalSearch').on('keyup', function () {
        table.draw();
    });

    $(document).on('change', '.statusToggle', function () {
        let checkbox = $(this);
        let id = checkbox.data('id');
        let isChecked = checkbox.prop('checked');

        Swal.fire({
            title: 'Are you sure?',
            text: "Change item status?",
            icon: 'warning',
            showCancelButton: true,
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("{{ route('admin.items.status') }}", {
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

    $(document).on('click', '.deleteBtn', function () {
        let id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "Delete this item?",
            icon: 'warning',
            showCancelButton: true,
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/items/delete/' + id,
                    type: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function () {
                        toastr.success('Item deleted successfully');
                        table.ajax.reload();
                    }
                });
            }
        });
    });
});
</script>
@endsection
