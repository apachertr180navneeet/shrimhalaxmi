@extends('admin.layouts.app')

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between mb-3">
        <h5 class="text-primary">Edit Job Work Assignment</h5>
        <a href="{{ route('admin.jobworkassignments.index') }}" class="btn btn-primary">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.jobworkerinwards.update', $inward['id']) }}" method="POST">
                @csrf
                @include('admin.job_worker_inwards.form')

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-success">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
