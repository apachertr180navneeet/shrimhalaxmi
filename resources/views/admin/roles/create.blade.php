@extends('admin.layouts.app')

@section('style')
<style>
    .permission-box {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 10px;
        background: #f9fafb;
        height: 100%;
    }

    .permission-title {
        font-weight: 600;
        color: #566a7f;
        margin-bottom: 8px;
        border-bottom: 1px solid #e5e7eb;
        padding-bottom: 5px;
    }

    .form-check {
        margin-bottom: 5px;
    }
</style>
@endsection


@section('content')
<div class="container-fluid flex-grow-1 container-p-y">

    <div class="d-flex justify-content-between mb-3">
        <h5 class="text-primary">Add Role</h5>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-primary">Back</a>
    </div>

    <div class="card">
        <div class="card-body">

            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf

                {{-- Role Name --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Role Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                </div>

                {{-- Select All --}}
                <div class="mb-2">
                    <input type="checkbox" id="selectAll"> 
                    <strong>Select All Permissions</strong>
                </div>

                {{-- Permissions --}}
                <div class="row">
                    <label class="form-label mb-2">Permissions</label>

                    @php
                        $groupedPermissions = collect($permissions)->groupBy(function($perm){
                            return explode('-', $perm->name)[0]; // module group
                        });
                    @endphp

                    @foreach($groupedPermissions as $module => $perms)
                        <div class="col-md-3 mb-3">
                            <div class="permission-box">

                                <div class="permission-title text-capitalize">
                                    {{ $module }}
                                </div>

                                @foreach($perms as $permission)
                                    <div class="form-check">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               name="permissions[]"
                                               value="{{ $permission->name }}"
                                               id="perm_{{ $permission->id }}">

                                        <label class="form-check-label" for="perm_{{ $permission->id }}">
                                            {{ ucfirst(str_replace($module.'-', '', $permission->name)) }}
                                        </label>
                                    </div>
                                @endforeach

                            </div>
                        </div>
                    @endforeach

                </div>

                {{-- Submit --}}
                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>

            </form>

        </div>
    </div>
</div>
@endsection


@section('script')
<script>
    // ✅ Select All
    $('#selectAll').on('click', function () {
        $('input[name="permissions[]"]').prop('checked', this.checked);
    });
</script>
@endsection