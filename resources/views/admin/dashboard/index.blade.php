@extends('admin.layouts.app')
@section('style')
<style>
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    .progress-bar {
        transition: width 1s ease;
    }
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .bg-gradient-success {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }
    .bg-gradient-warning {
        background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
    }
    .bg-gradient-danger {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
    }
    .fs-1 {
        animation: bounce 2s infinite;
    }
    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% {
            transform: translateY(0);
        }
        40% {
            transform: translateY(-10px);
        }
        60% {
            transform: translateY(-5px);
        }
    }
</style>
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
                        <h4 class="fw-bold">{{ $vendors['total'] }}</h4>
                        <small>Active: {{ $vendors['active'] }} | Inactive: {{ $vendors['inactive'] }}</small>
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
                        <h4 class="fw-bold">{{ $jobWorkers['total'] }}</h4>
                        <small>Active: {{ $jobWorkers['active'] }} | Inactive: {{ $jobWorkers['inactive'] }}</small>
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
                        <h4 class="fw-bold">{{ $items['total'] }}</h4>
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
                        <h4 class="fw-bold">{{ $customers['total'] }}</h4>
                        <small>Active: {{ $customers['active'] }} | Inactive: {{ $customers['inactive'] }}</small>
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

                <p>Total Assign: <strong>{{ $monthlyJobAssign['total'] }}</strong></p>

                <div class="mb-2">Completed ({{ $monthlyJobAssign['total'] > 0 ? round(($monthlyJobAssign['completed'] / $monthlyJobAssign['total']) * 100) : 0 }}%)</div>
                <div class="progress mb-3">
                    <div class="progress-bar bg-success" style="width:{{ $monthlyJobAssign['total'] > 0 ? round(($monthlyJobAssign['completed'] / $monthlyJobAssign['total']) * 100) : 0 }}%">{{ $monthlyJobAssign['completed'] }}</div>
                </div>

                <div class="mb-2">Processing ({{ $monthlyJobAssign['total'] > 0 ? round(($monthlyJobAssign['processing'] / $monthlyJobAssign['total']) * 100) : 0 }}%)</div>
                <div class="progress">
                    <div class="progress-bar bg-warning" style="width:{{ $monthlyJobAssign['total'] > 0 ? round(($monthlyJobAssign['processing'] / $monthlyJobAssign['total']) * 100) : 0 }}%">{{ $monthlyJobAssign['processing'] }}</div>
                </div>
            </div>
        </div>

        <!-- Today Job Assign -->
        <div class="col-lg-6">
            <div class="card shadow rounded-4 border-0 p-4">
                <h5 class="mb-3">⚡ Today Job Assign</h5>

                <p>Total Assign: <strong>{{ $todayJobAssign['total'] }}</strong></p>

                <div class="mb-2">Completed</div>
                <div class="progress mb-3">
                    <div class="progress-bar bg-success" style="width:{{ $todayJobAssign['total'] > 0 ? round(($todayJobAssign['completed'] / $todayJobAssign['total']) * 100) : 0 }}%">{{ $todayJobAssign['completed'] }}</div>
                </div>

                <div class="mb-2">Processing</div>
                <div class="progress">
                    <div class="progress-bar bg-warning" style="width:{{ $todayJobAssign['total'] > 0 ? round(($todayJobAssign['processing'] / $todayJobAssign['total']) * 100) : 0 }}%">{{ $todayJobAssign['processing'] }}</div>
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

                <p>Total Orders: <strong>{{ $monthlyDispatch['total'] }}</strong></p>

                <div class="mb-2">Completed</div>
                <div class="progress mb-3">
                    <div class="progress-bar bg-success" style="width:{{ $monthlyDispatch['total'] > 0 ? round(($monthlyDispatch['completed'] / $monthlyDispatch['total']) * 100) : 0 }}%">{{ $monthlyDispatch['completed'] }}</div>
                </div>

                <div class="mb-2">Pending</div>
                <div class="progress">
                    <div class="progress-bar bg-danger" style="width:{{ $monthlyDispatch['total'] > 0 ? round(($monthlyDispatch['pending'] / $monthlyDispatch['total']) * 100) : 0 }}%">{{ $monthlyDispatch['pending'] }}</div>
                </div>
            </div>
        </div>

        <!-- Today Dispatch -->
        <div class="col-lg-6">
            <div class="card shadow rounded-4 border-0 p-4">
                <h5 class="mb-3">📍 Today Dispatch</h5>

                <p>Total Orders: <strong>{{ $todayDispatch['total'] }}</strong></p>

                <div class="mb-2">Completed</div>
                <div class="progress mb-3">
                    <div class="progress-bar bg-success" style="width:{{ $todayDispatch['total'] > 0 ? round(($todayDispatch['completed'] / $todayDispatch['total']) * 100) : 0 }}%">{{ $todayDispatch['completed'] }}</div>
                </div>

                <div class="mb-2">Pending</div>
                <div class="progress">
                    <div class="progress-bar bg-danger" style="width:{{ $todayDispatch['total'] > 0 ? round(($todayDispatch['pending'] / $todayDispatch['total']) * 100) : 0 }}%">{{ $todayDispatch['pending'] }}</div>
                </div>
            </div>
        </div>

    </div>

</div>

                   
@endsection

@section('script')
<script>
    // Animate numbers on load
    document.addEventListener('DOMContentLoaded', function() {
        const counters = document.querySelectorAll('.fw-bold');
        counters.forEach(counter => {
            const target = parseInt(counter.textContent);
            let count = 0;
            const increment = target / 100;
            const timer = setInterval(() => {
                count += increment;
                if (count >= target) {
                    counter.textContent = target;
                    clearInterval(timer);
                } else {
                    counter.textContent = Math.floor(count);
                }
            }, 20);
        });
    });
</script>
@endsection