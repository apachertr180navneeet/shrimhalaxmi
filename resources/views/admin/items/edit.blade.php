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
    <div class="row">
        <div class="col-md-6">
            <h5 class="mb-2 text-primary">Edit Item</h5>
        </div>

        <div class="col-md-6 text-end">
            <a href="{{ route('admin.items.index') }}" class="btn btn-primary">
                Back
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.items.update', $item->id) }}" method="POST">
                @csrf

                @include('admin.items.form', ['item' => $item])

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
