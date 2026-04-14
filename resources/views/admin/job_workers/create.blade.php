@extends('admin.layouts.app')

@section('style')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>

/* =========================
    FORM LAYOUT STYLING
========================== */

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
    height: 70px;
}

.error-text {
    font-size: 12px;
    color: red;
}

.is-invalid {
    border: 1px solid red;
}

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
    <div class="d-flex justify-content-between mb-3">

        <!-- Page Title -->
        <h5 class="text-primary">Add Job Worker</h5>

        <!-- Back Button -->
        <a href="{{ route('admin.jobworkers.index') }}" class="btn btn-primary">
            Back
        </a>

    </div>

    <!-- =========================
        FORM CARD
    ========================== -->
    <div class="card">
        <div class="card-body">

            <!-- =========================
                JOB WORKER FORM
            ========================== -->
            <form action="{{ route('admin.jobworkers.store') }}" method="POST">

                <!-- CSRF Protection -->
                @csrf

                <!-- Reusable Form Fields -->
                {{-- 
                    This includes:
                    name, phone, email, city, etc.
                    Shared between create & edit pages
                --}}
                @include('admin.job_workers.form')

                <!-- Submit Button -->
                <div class="text-end mt-3">
                    <button class="btn btn-success">
                        Save Job Worker
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
