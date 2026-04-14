@extends('admin.layouts.app')

@section('style')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .customer-form-shell .select2-container {
        width: 100% !important;
    }

    .customer-form-shell .select2-container--default .select2-selection--single {
        border: 1px solid #cfd8e8 !important;
        border-radius: 10px !important;
        min-height: 42px !important;
    }

    .customer-form-shell .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 40px !important;
        padding-left: 12px !important;
        color: #4c6281 !important;
    }

    .customer-form-shell .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
    }
</style>
@endsection

@section('content')

<div class="container-fluid flex-grow-1 container-p-y">

    <!-- =========================
        PAGE HEADER
    ========================== -->
    <div class="row">

        <div class="col-md-6">
            <h5 class="mb-2 text-primary">Edit Customer</h5>
        </div>

        <div class="col-md-6 text-end">
            <a href="{{ route('admin.customers.index') }}" class="btn btn-primary">
                Back
            </a>
        </div>

    </div>

    <!-- =========================
        FORM CARD
    ========================== -->
    <div class="card">
        <div class="card-body">

            <!-- =========================
                UPDATE FORM
            ========================== -->
            <form action="{{ route('admin.customers.update', $customer->id) }}" method="POST">
                @csrf

                @include('admin.customers.form', ['customer' => $customer])

                <div class="text-end mt-3">
                    <button class="btn btn-success">
                        Update Customer
                    </button>
                </div>

            </form>


        </div>
    </div>

</div>

@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        $('.state-select').select2({
            placeholder: 'Select State',
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endsection
