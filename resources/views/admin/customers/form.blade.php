<div class="container-fluid">

    <!-- Row 1 -->
    <div class="d-flex gap-3 mb-2">

        <!-- Customer Name -->
        <div class="form-row-custom w-50">
            <label>Customer Name</label>
            <div class="form-group-custom">
                <input type="text" name="name"
                    class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name', $customer->name ?? '') }}">

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
                    value="{{ old('abbr', $customer->abbr ?? '') }}">
            </div>
        </div>

        <!-- Mobile -->
        <div class="form-row-custom w-50">
            <label>Mobile Number</label>
            <div class="form-group-custom">
                <input type="text" name="phone"
                    class="form-control @error('phone') is-invalid @enderror"
                    value="{{ old('phone', $customer->phone ?? '') }}">

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
                    value="{{ old('email', $customer->email ?? '') }}">
            </div>
        </div>

        <!-- Location -->
        <div class="form-row-custom w-50">
            <label>Location</label>
            <div class="form-group-custom">
                <input type="text" name="location"
                    class="form-control"
                    value="{{ old('location', $customer->location ?? '') }}">
            </div>
        </div>

    </div>

    <!-- Row 3 -->
    <div class="d-flex gap-3 mb-2">

        <!-- Firm Name -->
        <div class="form-row-custom w-50">
            <label>Firm Name</label>
            <div class="form-group-custom">
                <input type="text" name="firm_name"
                    class="form-control"
                    value="{{ old('firm_name', $customer->firm_name ?? '') }}">
            </div>
        </div>

        <!-- GST -->
        <div class="form-row-custom w-50">
            <label>GST</label>
            <div class="form-group-custom">
                <input type="text" name="gst_no"
                    class="form-control"
                    value="{{ old('gst_no', $customer->gst_no ?? '') }}">
            </div>
        </div>

    </div>

</div>