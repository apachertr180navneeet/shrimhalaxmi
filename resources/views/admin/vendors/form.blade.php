<div class="container-fluid">

    <!-- =========================
        ROW 1 : BASIC DETAILS
    ========================== -->
    <div class="d-flex gap-3 mb-2">

        <!-- Vendor Name (Required Field) -->
        <div class="form-row-custom w-50">
            <label>Vendor Name</label>
            <div class="form-group-custom">

                <!-- Input: Vendor Name -->
                <input type="text" name="vendor_name"
                    class="form-control @error('vendor_name') is-invalid @enderror"
                    value="{{ old('vendor_name', $vendor->vendor_name ?? '') }}">

                <!-- Validation Error -->
                @error('vendor_name')
                    <div class="error-text">{{ $message }}</div>
                @enderror

            </div>
        </div>

        <!-- Firm Name -->
        <div class="form-row-custom w-25">
            <label>Firm Name</label>
            <div class="form-group-custom">

                <!-- Input: Firm Name -->
                <input type="text" name="firm_name"
                    class="form-control"
                    value="{{ old('firm_name', $vendor->firm_name ?? '') }}">

            </div>
        </div>

        <!-- Abbreviation -->
        <div class="form-row-custom w-25">
            <label>Abbr.</label>
            <div class="form-group-custom">

                <!-- Input: Abbreviation -->
                <input type="text" name="abbr"
                    class="form-control"
                    value="{{ old('abbr', $vendor->abbr ?? '') }}">

            </div>
        </div>

    </div>


    <!-- =========================
        ROW 2 : CONTACT DETAILS
    ========================== -->
    <div class="d-flex gap-3 mb-2">

        <!-- Phone (Required + Unique) -->
        <div class="form-row-custom w-50">
            <label>Phone</label>
            <div class="form-group-custom">

                <!-- Input: Phone Number -->
                <input type="text" name="phone"
                    class="form-control @error('phone') is-invalid @enderror"
                    value="{{ old('phone', $vendor->phone ?? '') }}">

                <!-- Validation Error -->
                @error('phone')
                    <div class="error-text">{{ $message }}</div>
                @enderror

            </div>
        </div>

        <!-- Email -->
        <div class="form-row-custom w-50">
            <label>Email</label>
            <div class="form-group-custom">

                <!-- Input: Email -->
                <input type="email" name="email"
                    class="form-control"
                    value="{{ old('email', $vendor->email ?? '') }}">

                <!-- Validation Error -->
                @error('email')
                    <div class="error-text">{{ $message }}</div>
                @enderror

            </div>
        </div>

    </div>


    <!-- =========================
        ADDRESS FIELD
    ========================== -->
    <div class="form-row-custom mb-2">

        <!-- Address Label -->
        <label>Address</label>

        <div class="form-group-custom">

            <!-- Input: Address (Textarea) -->
            <textarea name="address" class="form-control">
                {{ old('address', $vendor->address ?? '') }}
            </textarea>

        </div>

    </div>


    <!-- =========================
        ROW 3 : BUSINESS DETAILS
    ========================== -->
    <div class="d-flex gap-3 mb-2">

        <!-- GST Number -->
        <div class="form-row-custom w-50">
            <label>GST No.</label>
            <div class="form-group-custom">

                <!-- Input: GST Number -->
                <input type="text" name="gst_no"
                    class="form-control"
                    value="{{ old('gst_no', $vendor->gst_no ?? '') }}">

            </div>
        </div>

        <!-- City -->
        <div class="form-row-custom w-50">
            <label>City</label>
            <div class="form-group-custom">

                <!-- Input: City -->
                <input type="text" name="city"
                    class="form-control"
                    value="{{ old('city', $vendor->city ?? '') }}">

            </div>
        </div>

    </div>


    <!-- =========================
        ROW 4 : LOCATION DETAILS
    ========================== -->
    <div class="d-flex gap-3">

        <!-- Pincode -->
        <div class="form-row-custom w-50">
            <label>Pin code</label>
            <div class="form-group-custom">

                <!-- Input: Pincode -->
                <input type="text" name="pincode"
                    class="form-control"
                    value="{{ old('pincode', $vendor->pincode ?? '') }}">

            </div>
        </div>

        <!-- State -->
        <div class="form-row-custom w-50">
            <label>State</label>
            <div class="form-group-custom">

                <!-- Input: State -->
                <input type="text" name="state"
                    class="form-control"
                    value="{{ old('state', $vendor->state ?? '') }}">

            </div>
        </div>

    </div>

</div>