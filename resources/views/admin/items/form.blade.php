<style>
    .card-custom {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .form-label {
        font-weight: 600;
        font-size: 13px;
        margin-bottom: 5px;
    }

    .form-control {
        height: 42px;
        border-radius: 8px;
        font-size: 14px;
    }

    textarea.form-control {
        height: 80px;
        resize: none;
    }

    .btn-custom {
        background: #4CAF50;
        color: #fff;
        border-radius: 8px;
        padding: 10px 20px;
        border: none;
    }

    .btn-custom:hover {
        background: #43a047;
    }

    .error-text {
        color: red;
        font-size: 12px;
    }
</style>

<div class="container-fluid">
    <div class="row g-3">
        <!-- Item Name -->
        <div class="col-md-6 col-12">
            <label class="form-label">Item Name</label>
            <input type="text" name="item_name"
                placeholder="Enter item name"
                class="form-control @error('item_name') is-invalid @enderror"
                value="{{ old('item_name', $item->item_name ?? '') }}">

            @error('item_name')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <!-- Unit -->
        <div class="col-md-6 col-12">
            <label class="form-label">Unit</label>
            <select name="unit"
                class="form-control @error('unit') is-invalid @enderror">
                <option value="">Select Unit</option>
                <option value="kg" {{ old('unit', $item->unit ?? '') == 'kg' ? 'selected' : '' }}>KG</option>
                <option value="meter" {{ old('unit', $item->unit ?? '') == 'meter' ? 'selected' : '' }}>Meter</option>
                <option value="pieces" {{ old('unit', $item->unit ?? '') == 'pieces' ? 'selected' : '' }}>Pieces</option>
            </select>

            @error('unit')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <!-- Remark -->
        <div class="col-md-12 col-12">
            <label class="form-label">Remark</label>
            <textarea name="remark"
                placeholder="Enter remark (optional)"
                class="form-control @error('remark') is-invalid @enderror">{{ old('remark', $item->remark ?? '') }}</textarea>

            @error('remark')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

    </div>
</div>