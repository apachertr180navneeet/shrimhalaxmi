@extends('admin.layouts.app')

@section('style')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single {
        height: 38px !important;
        border-radius: 6px !important;
        border: 1px solid #d9dee3 !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px !important;
        padding-left: 10px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }

    .select2-container {
        width: 100% !important;
    }
</style>
@endsection

@section('content')

<div class="container-fluid flex-grow-1 container-p-y">

    <!-- =========================
        PAGE HEADER
    ========================== -->
    <div class="row">

        <!-- Page Title -->
        <div class="col-md-6">
            <h5 class="mb-2 text-primary">Edit Vendor</h5>
        </div>

        <!-- Back Button -->
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.vendors.index') }}" class="btn btn-primary">
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
            <form action="{{ route('admin.vendors.update', $vendor->id) }}" method="POST">

                <!-- CSRF Protection -->
                @csrf

                <!-- 
                    Reusable Form Partial
                    - Same form used in create & edit
                    - Passing $vendor to pre-fill data
                -->
                @include('admin.vendors.form', ['vendor' => $vendor])

                <!-- Submit Button -->
                <div class="text-end mt-3">
                    <button class="btn btn-success">
                        Save Vendor
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
