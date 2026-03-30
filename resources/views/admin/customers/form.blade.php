<style>
   .form-label {
        font-weight: 600;
        margin-bottom: 4px;
    }

    .form-control {
        border-radius: 6px;
    }

    .invalid-feedback {
        font-size: 12px;
    } 
</style>

<div class="container-fluid">

    <!-- Row 1 -->
    <div class="row mb-3">

        <!-- Customer Name -->
        <div class="col-md-5">
            <label class="form-label">Customer Name</label>
            <input type="text" name="name"
                class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name', $customer->name ?? '') }}">

            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Abbr -->
        <div class="col-md-2">
            <label class="form-label">Abbr.</label>
            <input type="text" name="abbr"
                class="form-control"
                value="{{ old('abbr', $customer->abbr ?? '') }}">
        </div>

        <!-- Mobile -->
        <div class="col-md-5">
            <label class="form-label">Mobile Number</label>
            <input type="text" name="phone"
                class="form-control @error('phone') is-invalid @enderror"
                value="{{ old('phone', $customer->phone ?? '') }}">

            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

    </div>

    <!-- Row 2 -->
    <div class="row mb-3">

        <!-- Email -->
        <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email"
                class="form-control"
                value="{{ old('email', $customer->email ?? '') }}">
        </div>

        <!-- Location -->
        <div class="col-md-6">
            <label class="form-label">Address</label>
            <input type="text" name="location"
                class="form-control"
                value="{{ old('location', $customer->location ?? '') }}">
        </div>

    </div>

    <!-- Row 3 -->
    <div class="row mb-3">

        <!-- Firm Name -->
        <div class="col-md-6">
            <label class="form-label">Firm Name</label>
            <input type="text" name="firm_name"
                class="form-control"
                value="{{ old('firm_name', $customer->firm_name ?? '') }}">
        </div>

        <!-- GST -->
        <div class="col-md-6">
            <label class="form-label">GST</label>
            <input type="text" name="gst_no"
                class="form-control"
                value="{{ old('gst_no', $customer->gst_no ?? '') }}">
        </div>

    </div>

</div>