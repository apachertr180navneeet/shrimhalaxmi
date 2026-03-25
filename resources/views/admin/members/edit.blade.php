@extends('admin.layouts.app')

@section('style')
<style>
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
.error-text {
    font-size: 12px;
    color: red;
}
.is-invalid {
    border: 1px solid red;
}
</style>
@endsection

@section('content')

<div class="container-fluid flex-grow-1 container-p-y">

    <!-- Header -->
    <div class="d-flex justify-content-between mb-3">
        <h5 class="text-primary">Edit Member</h5>

        <a href="{{ route('admin.members.index') }}" class="btn btn-primary">
            Back
        </a>
    </div>

    <!-- Card -->
    <div class="card">
        <div class="card-body">

            <form action="{{ route('admin.members.update', $user->id) }}" method="POST">
                @csrf

                <!-- Name -->
                <div class="form-row-custom">
                    <label>Name</label>
                    <div class="form-group-custom">
                        <input type="text" name="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $user->full_name) }}">

                        @error('name')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Mobile -->
                <div class="form-row-custom">
                    <label>Mobile</label>
                    <div class="form-group-custom">
                        <input type="text" name="mobile"
                            class="form-control @error('mobile') is-invalid @enderror"
                            value="{{ old('mobile', $user->phone) }}">

                        @error('mobile')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Email -->
                <div class="form-row-custom">
                    <label>Email</label>
                    <div class="form-group-custom">
                        <input type="email" name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $user->email) }}">

                        @error('email')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Password -->
                <div class="form-row-custom">
                    <label>Password</label>
                    <div class="form-group-custom">
                        <input type="password" name="password"
                            class="form-control @error('password') is-invalid @enderror">

                        <small class="text-muted">Leave blank if not changing</small>

                        @error('password')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="form-row-custom">
                    <label>Confirm Password</label>
                    <div class="form-group-custom">
                        <input type="password" name="password_confirmation"
                            class="form-control">
                    </div>
                </div>

                <!-- Role -->
                <div class="form-row-custom">
                    <label>Role</label>
                    <div class="form-group-custom">
                        <select name="role"
                            class="form-control @error('role') is-invalid @enderror">

                            <option value="">Select Role</option>

                            @foreach($roles as $role)
                                <option value="{{ $role->name }}"
                                    {{ $user->roles->pluck('name')->first() == $role->name ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach

                        </select>

                        @error('role')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Submit -->
                <div class="text-end mt-3">
                    <button class="btn btn-success">
                        Update Member
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

{{-- Auto focus on first error --}}
@if ($errors->any())
<script>
    document.querySelector('.is-invalid')?.focus();
</script>
@endif

@endsection