@extends('admin.layouts.app')
@section('style')
@endsection  

@section('content')

<div class="container-fluid py-4">

    <div class="row g-4">

        <!-- Vendors -->
        <div class="col-lg-3 col-md-6">
            <div class="card shadow border-0 rounded-4 p-3 bg-gradient-primary text-white">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6>Vendors</h6>
                        <h4 class="fw-bold">15</h4>
                        <small>Active: 10 | Inactive: 5</small>
                    </div>
                    <i class="bx bx-store fs-1"></i>
                </div>
            </div>
        </div>

        <!-- Job Workers -->
        <div class="col-lg-3 col-md-6">
            <div class="card shadow border-0 rounded-4 p-3 bg-gradient-success text-white">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6>Job Workers</h6>
                        <h4 class="fw-bold">15</h4>
                        <small>Active: 10 | Inactive: 5</small>
                    </div>
                    <i class="bx bx-user fs-1"></i>
                </div>
            </div>
        </div>

        <!-- Items -->
        <div class="col-lg-3 col-md-6">
            <div class="card shadow border-0 rounded-4 p-3 bg-gradient-warning text-white">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6>Items</h6>
                        <h4 class="fw-bold">50</h4>
                        <small>Total Items</small>
                    </div>
                    <i class="bx bx-box fs-1"></i>
                </div>
            </div>
        </div>

        <!-- Customers -->
        <div class="col-lg-3 col-md-6">
            <div class="card shadow border-0 rounded-4 p-3 bg-gradient-danger text-white">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6>Customers</h6>
                        <h4 class="fw-bold">15</h4>
                        <small>Active: 10 | Inactive: 5</small>
                    </div>
                    <i class="bx bx-group fs-1"></i>
                </div>
            </div>
        </div>

    </div>

    <!-- JOB ASSIGN + DISPATCH -->
    <div class="row mt-4 g-4">

        <!-- Month Job Assign -->
        <div class="col-lg-6">
            <div class="card shadow rounded-4 border-0 p-4">
                <h5 class="mb-3">📦 Monthly Job Assign</h5>

                <p>Total Assign: <strong>50</strong></p>

                <div class="mb-2">Completed (40%)</div>
                <div class="progress mb-3">
                    <div class="progress-bar bg-success" style="width:40%">20</div>
                </div>

                <div class="mb-2">Processing (60%)</div>
                <div class="progress">
                    <div class="progress-bar bg-warning" style="width:60%">30</div>
                </div>
            </div>
        </div>

        <!-- Today Job Assign -->
        <div class="col-lg-6">
            <div class="card shadow rounded-4 border-0 p-4">
                <h5 class="mb-3">⚡ Today Job Assign</h5>

                <p>Total Assign: <strong>5</strong></p>

                <div class="mb-2">Completed</div>
                <div class="progress mb-3">
                    <div class="progress-bar bg-success" style="width:40%">2</div>
                </div>

                <div class="mb-2">Processing</div>
                <div class="progress">
                    <div class="progress-bar bg-warning" style="width:60%">3</div>
                </div>
            </div>
        </div>

    </div>

    <!-- DISPATCH -->
    <div class="row mt-4 g-4">

        <!-- Monthly Dispatch -->
        <div class="col-lg-6">
            <div class="card shadow rounded-4 border-0 p-4">
                <h5 class="mb-3">🚚 Monthly Dispatch</h5>

                <p>Total Orders: <strong>50</strong></p>

                <div class="mb-2">Completed</div>
                <div class="progress mb-3">
                    <div class="progress-bar bg-success" style="width:40%">20</div>
                </div>

                <div class="mb-2">Pending</div>
                <div class="progress">
                    <div class="progress-bar bg-danger" style="width:60%">30</div>
                </div>
            </div>
        </div>

        <!-- Today Dispatch -->
        <div class="col-lg-6">
            <div class="card shadow rounded-4 border-0 p-4">
                <h5 class="mb-3">📍 Today Dispatch</h5>

                <p>Total Orders: <strong>5</strong></p>

                <div class="mb-2">Completed</div>
                <div class="progress mb-3">
                    <div class="progress-bar bg-success" style="width:40%">2</div>
                </div>

                <div class="mb-2">Pending</div>
                <div class="progress">
                    <div class="progress-bar bg-danger" style="width:60%">3</div>
                </div>
            </div>
        </div>

    </div>

</div>

                   
@endsection

@section('script')
@endsection