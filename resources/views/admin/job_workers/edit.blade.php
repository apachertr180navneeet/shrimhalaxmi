@extends('admin.layouts.app')

@section('content')

<div class="container-fluid flex-grow-1 container-p-y">

    <!-- =========================
        PAGE HEADER
    ========================== -->
    <div class="row">

        <!-- Page Title -->
        <div class="col-md-6">
            <h5 class="mb-2 text-primary">Edit Job Worker</h5>
        </div>

        <!-- Back Button -->
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.jobworkers.index') }}" class="btn btn-primary">
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
            <form action="{{ route('admin.jobworkers.update', $worker->id) }}" method="POST">

                <!-- CSRF Protection -->
                @csrf

                <!-- 
                    Reusable Form Partial
                    - Same form used in create & edit
                    - Passing $worker to pre-fill data
                -->
                @include('admin.job_workers.form', ['worker' => $worker])

                <!-- Submit Button -->
                <div class="text-end mt-3">
                    <button class="btn btn-success">
                        Update Job Worker
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

@endsection