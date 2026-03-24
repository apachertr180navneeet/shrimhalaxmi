<div class="container-fluid">

    <div class="d-flex gap-3 mb-3">
        <div class="form-row-custom w-50">
            <label>Item Name</label>
            <div class="form-group-custom">
                <input type="text" name="item_name"
                    class="form-control @error('item_name') is-invalid @enderror"
                    value="{{ old('item_name', $item->item_name ?? '') }}">

                @error('item_name')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-row-custom w-50">
            <label>Abbreviation</label>
            <div class="form-group-custom">
                <input type="text" name="abbr"
                    class="form-control @error('abbr') is-invalid @enderror"
                    value="{{ old('abbr', $item->abbr ?? '') }}">

                @error('abbr')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="form-row-custom mb-2">
        <label>Remark</label>
        <div class="form-group-custom">
            <textarea name="remark" class="form-control @error('remark') is-invalid @enderror">{{ old('remark', $item->remark ?? '') }}</textarea>

            @error('remark')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>
    </div>

</div>
