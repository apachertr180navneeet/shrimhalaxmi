
<style>
    .form-label {
        font-weight: 500;
    }

    textarea.form-control {
        resize: none;
    }
</style>
<div class="container-fluid">

    <!-- Row 1 -->
    <div class="row g-3">

        <!-- Job Worker Name -->
        <div class="col-12 col-md-6">
            <label class="form-label">Job Worker Name</label>
            <input type="text" name="name"
                class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name', $worker->name ?? '') }}">

            @error('name')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        <!-- Abbr -->
        <div class="col-12 col-md-3">
            <label class="form-label">Abbr.</label>
            <input type="text" name="abbr"
                class="form-control"
                value="{{ old('abbr', $worker->abbr ?? '') }}">
        </div>

        <!-- Phone -->
        <div class="col-12 col-md-3">
            <label class="form-label">Phone</label>
            <input type="text" name="phone"
                class="form-control @error('phone') is-invalid @enderror"
                value="{{ old('phone', $worker->phone ?? '') }}">

            @error('phone')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

    </div>

    <!-- Row 2 -->
    <div class="row g-3 mt-1">

        <!-- Email -->
        <div class="col-12 col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email"
                class="form-control"
                value="{{ old('email', $worker->email ?? '') }}">
        </div>

        <!-- City -->
        <div class="col-12 col-md-6">
            <label class="form-label">City</label>
            <input type="text" name="city"
                class="form-control"
                value="{{ old('city', $worker->city ?? '') }}">
        </div>

    </div>

    <!-- Address -->
    <div class="row g-3 mt-1">
        <div class="col-12">
            <label class="form-label">Address</label>
            <textarea name="address" class="form-control" rows="3">{{ old('address', $worker->address ?? '') }}</textarea>
        </div>
    </div>

    <!-- Row 3 -->
    <div class="row g-3 mt-1">

        <!-- Firm Name -->
        <div class="col-12 col-md-6">
            <label class="form-label">Firm Name (optional)</label>
            <input type="text" name="firm_name"
                class="form-control"
                value="{{ old('firm_name', $worker->firm_name ?? '') }}">
        </div>

        <!-- Pincode -->
        <div class="col-12 col-md-6">
            <label class="form-label">Pin code</label>
            <input type="text" name="pincode"
                class="form-control"
                value="{{ old('pincode', $worker->pincode ?? '') }}">
        </div>

    </div>

    <!-- Row 4 -->
    <div class="row g-3 mt-1">

        <!-- State -->
        <div class="col-12 col-md-6">
            <label class="form-label">State</label>
            @php
                $states = [
                    'Andhra Pradesh',
                    'Arunachal Pradesh',
                    'Assam',
                    'Bihar',
                    'Chhattisgarh',
                    'Goa',
                    'Gujarat',
                    'Haryana',
                    'Himachal Pradesh',
                    'Jharkhand',
                    'Karnataka',
                    'Kerala',
                    'Madhya Pradesh',
                    'Maharashtra',
                    'Manipur',
                    'Meghalaya',
                    'Mizoram',
                    'Nagaland',
                    'Odisha',
                    'Punjab',
                    'Rajasthan',
                    'Sikkim',
                    'Tamil Nadu',
                    'Telangana',
                    'Tripura',
                    'Uttar Pradesh',
                    'Uttarakhand',
                    'West Bengal',
                    'Andaman and Nicobar Islands',
                    'Chandigarh',
                    'Dadra and Nagar Haveli and Daman and Diu',
                    'Delhi',
                    'Jammu and Kashmir',
                    'Ladakh',
                    'Lakshadweep',
                    'Puducherry',
                ];
                $selectedState = old('state', $worker->state ?? '');
            @endphp
            <select name="state" id="state" class="form-control state-select @error('state') is-invalid @enderror">
                <option value="">Select State</option>
                @foreach ($states as $state)
                    <option value="{{ $state }}" {{ $selectedState === $state ? 'selected' : '' }}>
                        {{ $state }}
                    </option>
                @endforeach
                @if ($selectedState && !in_array($selectedState, $states))
                    <option value="{{ $selectedState }}" selected>{{ $selectedState }}</option>
                @endif
            </select>
            @error('state')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

    </div>

</div>
