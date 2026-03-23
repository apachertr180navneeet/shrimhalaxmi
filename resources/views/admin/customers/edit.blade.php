@extends('admin.layouts.app')

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