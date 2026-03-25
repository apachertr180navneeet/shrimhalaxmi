@extends('admin.layouts.app')

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between mb-3">
        <h5 class="text-primary">Add Order Dispatch</h5>
        <a href="{{ route('admin.orderdispatches.index') }}" class="btn btn-primary">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.orderdispatches.store') }}" method="POST" id="dispatchForm">
                @csrf
                @include('admin.order_dispatches.form', ['dispatch' => $dispatch, 'customers' => $customers, 'items' => $items, 'dispatchItems' => $dispatchItems])
                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-success">Save Dispatch</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection