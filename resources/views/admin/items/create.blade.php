@extends('admin.layouts.app')

@section('style')
<style>
    .form-row-custom {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
    }

    .form-row-custom label {
        width: 140px;
        min-width: 140px;
        font-weight: 600;
        color: #566a7f;
        margin: 0;
    }

    .form-group-custom {
        flex: 1;
    }

    .form-control {
        width: 100%;
        height: 38px;
        padding: 6px 10px;
        border-radius: 6px;
    }

    textarea.form-control {
        min-height: 70px;
        height: 70px;
    }

    .error-text {
        font-size: 12px;
        color: red;
    }

    .is-invalid {
        border: 1px solid red;
    }
</style>
@endsection

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between mb-3">
        <h5 class="text-primary">Add Item</h5>

        <a href="{{ route('admin.items.index') }}" class="btn btn-primary">
            Back
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.items.store') }}" method="POST">
                @csrf

                @include('admin.items.form')

                <div class="text-end mt-3">
                    <button class="btn btn-success">
                        Save Item
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
