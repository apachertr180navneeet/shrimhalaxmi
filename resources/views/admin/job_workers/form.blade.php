<div class="container-fluid">

    <!-- Row 1 -->
    <div class="d-flex gap-3 mb-2">

        <!-- Job Worker Name -->
        <div class="form-row-custom w-50">
            <label>Job Worker Name</label>
            <div class="form-group-custom">
                <input type="text" name="name"
                    class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name', $worker->name ?? '') }}">

                @error('name')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Abbr -->
        <div class="form-row-custom w-25">
            <label>Abbr.</label>
            <div class="form-group-custom">
                <input type="text" name="abbr"
                    class="form-control"
                    value="{{ old('abbr', $worker->abbr ?? '') }}">
            </div>
        </div>

        <!-- Phone -->
        <div class="form-row-custom w-50">
            <label>Phone</label>
            <div class="form-group-custom">
                <input type="text" name="phone"
                    class="form-control @error('phone') is-invalid @enderror"
                    value="{{ old('phone', $worker->phone ?? '') }}">

                @error('phone')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>
        </div>

    </div>

    <!-- Row 2 -->
    <div class="d-flex gap-3 mb-2">

        <!-- Email -->
        <div class="form-row-custom w-50">
            <label>Email</label>
            <div class="form-group-custom">
                <input type="email" name="email"
                    class="form-control"
                    value="{{ old('email', $worker->email ?? '') }}">
            </div>
        </div>

        <!-- City -->
        <div class="form-row-custom w-50">
            <label>City</label>
            <div class="form-group-custom">
                <input type="text" name="city"
                    class="form-control"
                    value="{{ old('city', $worker->city ?? '') }}">
            </div>
        </div>

    </div>

    <!-- Address -->
    <div class="form-row-custom mb-2">
        <label>Address</label>
        <div class="form-group-custom">
            <textarea name="address" class="form-control">{{ old('address', $worker->address ?? '') }}</textarea>
        </div>
    </div>

    <!-- Row 3 -->
    <div class="d-flex gap-3 mb-2">

        <!-- Firm Name -->
        <div class="form-row-custom w-50">
            <label>Firm Name (optional)</label>
            <div class="form-group-custom">
                <input type="text" name="firm_name"
                    class="form-control"
                    value="{{ old('firm_name', $worker->firm_name ?? '') }}">
            </div>
        </div>

        <!-- Pincode -->
        <div class="form-row-custom w-50">
            <label>Pin code</label>
            <div class="form-group-custom">
                <input type="text" name="pincode"
                    class="form-control"
                    value="{{ old('pincode', $worker->pincode ?? '') }}">
            </div>
        </div>

    </div>

    <!-- Row 4 -->
    <div class="d-flex gap-3">

        <!-- State -->
        <div class="form-row-custom w-50">
            <label>State</label>
            <div class="form-group-custom">
                <input type="text" name="state"
                    class="form-control"
                    value="{{ old('state', $worker->state ?? '') }}">
            </div>
        </div>

    </div>

</div>