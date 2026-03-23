@extends('admin.layouts.app')

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