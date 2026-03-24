@extends('admin.layouts.app')

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-6">
            <h5 class="mb-2 text-primary">Edit Purchase</h5>
        </div>

        <div class="col-md-6 text-end">
            <a href="{{ route('admin.purchases.index') }}" class="btn btn-primary">Back</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.purchases.update', $purchase['id']) }}" method="POST" id="purchaseForm">
                @csrf
                @include('admin.purchases.form', ['purchase' => $purchase, 'vendors' => $vendors, 'items' => $items, 'purchaseItems' => $purchaseItems])

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-success">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
