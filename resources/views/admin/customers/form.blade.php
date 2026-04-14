<style>
    .customer-form-shell {
        border: 1px solid #dbe4f3;
        border-radius: 14px;
        overflow: hidden;
        background: #ffffff;
    }

    .customer-form-head {
        padding: 14px 18px;
        border-bottom: 1px solid #e8eef8;
        background: linear-gradient(135deg, #eef5ff 0%, #f8fbff 100%);
    }

    .customer-form-head h6 {
        margin: 0;
        font-weight: 700;
        color: #274372;
        letter-spacing: 0.2px;
    }

    .customer-form-body {
        padding: 18px;
    }

    .customer-form-shell .form-label {
        font-weight: 600;
        margin-bottom: 6px;
        color: #4c6281;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.4px;
    }

    .customer-form-shell .form-control {
        border: 1px solid #cfd8e8;
        border-radius: 10px;
        min-height: 42px;
        padding: 8px 12px;
        box-shadow: none;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .customer-form-shell .form-control:focus {
        border-color: #5f8dd3;
        box-shadow: 0 0 0 3px rgba(95, 141, 211, 0.14);
    }

    .customer-form-shell .invalid-feedback {
        font-size: 12px;
    }
</style>

<div class="customer-form-shell">
    <div class="customer-form-head">
        <h6>Customer Profile</h6>
    </div>

    <div class="customer-form-body">
        <div class="row g-3">
            <div class="col-md-5">
                <label class="form-label">Customer Name</label>
                <input type="text" name="name"
                    class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name', $customer->name ?? '') }}"
                    placeholder="Enter customer name">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-2">
                <label class="form-label">Abbr.</label>
                <input type="text" name="abbr"
                    class="form-control"
                    value="{{ old('abbr', $customer->abbr ?? '') }}"
                    placeholder="Short code">
            </div>

            <div class="col-md-5">
                <label class="form-label">Mobile Number</label>
                <input type="text" name="phone"
                    class="form-control @error('phone') is-invalid @enderror"
                    value="{{ old('phone', $customer->phone ?? '') }}"
                    placeholder="10 digit mobile number">
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Email</label>
                <input type="email" name="email"
                    class="form-control"
                    value="{{ old('email', $customer->email ?? '') }}"
                    placeholder="example@email.com">
            </div>

            <div class="col-md-4">
                <label class="form-label">Address</label>
                <input type="text" name="location"
                    class="form-control"
                    value="{{ old('location', $customer->location ?? '') }}"
                    placeholder="Address line 1">
            </div>

            <div class="col-md-4">
                <label class="form-label">Address 2</label>
                <input type="text" name="address_2"
                    class="form-control"
                    value="{{ old('address_2', $customer->address_2 ?? '') }}"
                    placeholder="Address line 2">
            </div>

            <div class="col-md-6">
                <label class="form-label">Firm Name</label>
                <input type="text" name="firm_name"
                    class="form-control"
                    value="{{ old('firm_name', $customer->firm_name ?? '') }}"
                    placeholder="Firm / Company name">
            </div>

            <div class="col-md-6">
                <label class="form-label">GST</label>
                <input type="text" name="gst_no"
                    class="form-control"
                    value="{{ old('gst_no', $customer->gst_no ?? '') }}"
                    placeholder="GST number">
            </div>

            <div class="col-md-6">
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
                    $selectedState = old('state', $customer->state ?? '');
                @endphp
                <select name="state" id="state"
                    class="form-control state-select @error('state') is-invalid @enderror">
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
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</div>
