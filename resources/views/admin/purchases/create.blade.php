@extends('admin.layouts.app')

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between mb-3">
        <h5 class="text-primary">Add Purchase</h5>
        <a href="{{ route('admin.purchases.index') }}" class="btn btn-primary">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.purchases.store') }}" method="POST" id="purchaseForm">
                @csrf
                @include('admin.purchases.form', ['purchase' => $purchase, 'vendors' => $vendors, 'items' => $items, 'purchaseItems' => $purchaseItems])

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-success">Save Purchase</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
