@php
    $dispatch = $dispatch ?? [];
    $customers = $customers ?? collect();
    $items = $items ?? collect();
    $lotSources = $lotSources ?? collect();
    $dispatchItems = $dispatchItems ?? collect();

    $dispatchDate = old('dispatch_date', $dispatch['dispatch_date'] ?? now()->format('Y-m-d'));
    $dispatchNo = old('dispatch_no', $dispatch['dispatch_no'] ?? '');
    $billNo = old('bill_no', $dispatch['bill_no'] ?? '');
    $customerId = old('customer_id', $dispatch['customer_id'] ?? '');
    $mobileNumber = old('mobile_number', $dispatch['mobile_number'] ?? '');
    $transport = old('transport', $dispatch['transport'] ?? '');
    $status = old('status', $dispatch['status'] ?? 'Pending');
    $totalMeter = old('total_meter', $dispatch['total_meter'] ?? '0');
    $totalAmount = old('total_amount', $dispatch['total_amount'] ?? '0');
@endphp

<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">Date</label>
        <input type="date" name="dispatch_date" class="form-control @error('dispatch_date') is-invalid @enderror" value="{{ $dispatchDate }}" required>
        @error('dispatch_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">CH No</label>
        <input type="text" name="dispatch_no" class="form-control @error('dispatch_no') is-invalid @enderror" value="{{ $dispatchNo }}" readonly required>
        @error('dispatch_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Bill No</label>
        <input type="text" name="bill_no" class="form-control @error('bill_no') is-invalid @enderror" value="{{ $billNo }}">
        @error('bill_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="row g-3 mt-3">
    <div class="col-md-4">
        <label class="form-label">Customer</label>
        <select id="customer_id" name="customer_id" class="form-select @error('customer_id') is-invalid @enderror" required>
            <option value="" data-abbr="">Select customer</option>
            @foreach ($customers as $customer)
                <option value="{{ $customer->id }}" data-abbr="{{ $customer->abbr ?? '' }}" {{ (string) $customerId === (string) $customer->id ? 'selected' : '' }}>{{ $customer->name }}{{ $customer->abbr ? ' (' . $customer->abbr . ')' : '' }}</option>
            @endforeach
        </select>
        @error('customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Number</label>
        <input type="text" name="mobile_number" class="form-control @error('mobile_number') is-invalid @enderror" value="{{ $mobileNumber }}">
        @error('mobile_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Transport</label>
        <input type="text" name="transport" class="form-control @error('transport') is-invalid @enderror" value="{{ $transport }}">
        @error('transport')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="row g-3 mt-3">
    <div class="col-md-4">
        <label class="form-label">Status</label>
        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
            <option value="Pending" {{ $status === 'Pending' ? 'selected' : '' }}>Pending</option>
            <option value="In Transit" {{ $status === 'In Transit' ? 'selected' : '' }}>In Transit</option>
            <option value="Complete" {{ $status === 'Complete' ? 'selected' : '' }}>Complete</option>
            <option value="Cancelled" {{ $status === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<h6 class="mt-4">Items Details</h6>
<div class="row g-3">
    <div class="col-md-3">
        <label class="form-label">Item</label>
        <select id="item_id" class="form-select">
            <option value="">Select item</option>
            @foreach ($items as $item)
                <option value="{{ $item->id }}" data-name="{{ $item->item_name }}" data-abbr="{{ $item->abbr }}">{{ $item->item_name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Lot No</label>
        <select id="lot_no" class="form-select" disabled>
            <option value="">Select lot</option>
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label">Meter</label>
        <input type="number" step="0.01" id="meter" class="form-control" placeholder="Meter">
    </div>
    <div class="col-md-2">
        <label class="form-label">Rate</label>
        <input type="number" step="0.01" id="rate" class="form-control" placeholder="Rate">
    </div>
    <div class="col-md-2">
        <label class="form-label">Amount</label>
        <input type="number" step="0.01" id="amount" class="form-control" placeholder="Amount" readonly>
    </div>
</div>

<div class="mt-2">
    <button type="button" id="addItemBtn" class="btn btn-primary">Add Item</button>
</div>

<div class="table-responsive mt-3">
    <table class="table table-bordered" id="dispatchItemsTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Lot No</th>
                <th>Item</th>
                <th>Meter</th>
                <th>Rate</th>
                <th>Amount</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="dispatchItemsBody">
            @forelse ($dispatchItems as $index => $row)
                <tr data-index="{{ $index }}">
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row->lot_no }}</td>
                    <td>{{ $row->item?->item_name ?? '-' }}</td>
                    <td>{{ number_format($row->meter, 2, '.', '') }}</td>
                    <td>{{ number_format($row->rate, 2, '.', '') }}</td>
                    <td>{{ number_format($row->amount, 2, '.', '') }}</td>
                    <td><button type="button" class="btn btn-sm btn-danger removeItemBtn">Remove</button></td>
                    <td class="d-none">
                        <input type="hidden" name="items_data[{{ $index }}][item_id]" value="{{ $row->item_id }}">
                        <input type="hidden" name="items_data[{{ $index }}][lot_no]" value="{{ $row->lot_no }}">
                        <input type="hidden" name="items_data[{{ $index }}][item_code]" value="{{ $row->item_code }}">
                        <input type="hidden" name="items_data[{{ $index }}][meter]" value="{{ $row->meter }}">
                        <input type="hidden" name="items_data[{{ $index }}][rate]" value="{{ $row->rate }}">
                        <input type="hidden" name="items_data[{{ $index }}][amount]" value="{{ $row->amount }}">
                        <input type="hidden" name="items_data[{{ $index }}][sort_order]" value="{{ $row->sort_order }}">
                    </td>
                </tr>
            @empty
                <tr id="noItemsRow">
                    <td colspan="7" class="text-center">No items added yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{--  <div class="row mt-2">
    <div class="col-md-3">
        <strong>Total Meter:</strong> <span id="totalMeter">{{ number_format((float) $totalMeter, 2, '.', '') }}</span>
    </div>
    <div class="col-md-3">
        <strong>Total Amount:</strong> <span id="totalAmount">{{ number_format((float) $totalAmount, 2, '.', '') }}</span>
    </div>
</div>  --}}

<script>
(function () {
    var lotSources = @json($lotSources->toArray());

    function toNumber(value) {
        var n = parseFloat(value);
        return Number.isFinite(n) ? n : 0;
    }

    function refreshAmount() {
        var meter = toNumber(document.getElementById('meter').value);
        var rate = toNumber(document.getElementById('rate').value);
        document.getElementById('amount').value = (meter * rate).toFixed(2);
    }

    function getDataRows() {
        return Array.from(document.querySelectorAll('#dispatchItemsBody tr')).filter(function (row) {
            return row.id !== 'noItemsRow';
        });
    }

    function populateLotDropdown(itemId) {
        var lotSelect = document.getElementById('lot_no');
        if (!lotSelect) return;

        lotSelect.innerHTML = '<option value="">Select lot</option>';

        if (!itemId) {
            lotSelect.disabled = true;
            return;
        }

        var seenLots = new Set();
        lotSources.forEach(function (source) {
            if (String(source.item_id) !== String(itemId)) return;
            var lotNo = (source.lot_no || '').toString().trim();
            if (!lotNo || seenLots.has(lotNo)) return;
            seenLots.add(lotNo);
            var option = document.createElement('option');
            option.value = lotNo;
            option.textContent = lotNo;
            lotSelect.appendChild(option);
        });

        lotSelect.disabled = seenLots.size === 0;
    }

    function updateTotals() {
        var totalMeter = 0;
        var totalAmount = 0;
        document.querySelectorAll('#dispatchItemsBody tr').forEach(function (row) {
            if (row.id === 'noItemsRow') return;
            var meter = toNumber(row.querySelector('input[name$="[meter]"]').value);
            var amount = toNumber(row.querySelector('input[name$="[amount]"]').value);
            totalMeter += meter;
            totalAmount += amount;
        });
        //document.getElementById('totalMeter').textContent = totalMeter.toFixed(2);
        //document.getElementById('totalAmount').textContent = totalAmount.toFixed(2);
    }

    function reIndexItems() {
        var rows = document.querySelectorAll('#dispatchItemsBody tr');
        var count = 0;
        rows.forEach(function (row) {
            if (row.id === 'noItemsRow') return;
            count++;
            row.children[0].textContent = count;
            row.dataset.index = count - 1;

            var hiddenInputs = row.querySelector('td.d-none');
            hiddenInputs.innerHTML =
                '<input type="hidden" name="items_data[' + (count - 1) + '][item_id]" value="' + hiddenInputs.querySelector('input[name$="[item_id]"]').value + '">' +
                '<input type="hidden" name="items_data[' + (count - 1) + '][lot_no]" value="' + hiddenInputs.querySelector('input[name$="[lot_no]"]').value + '">' +
                '<input type="hidden" name="items_data[' + (count - 1) + '][item_code]" value="' + hiddenInputs.querySelector('input[name$="[item_code]"]').value + '">' +
                '<input type="hidden" name="items_data[' + (count - 1) + '][meter]" value="' + hiddenInputs.querySelector('input[name$="[meter]"]').value + '">' +
                '<input type="hidden" name="items_data[' + (count - 1) + '][rate]" value="' + hiddenInputs.querySelector('input[name$="[rate]"]').value + '">' +
                '<input type="hidden" name="items_data[' + (count - 1) + '][amount]" value="' + hiddenInputs.querySelector('input[name$="[amount]"]').value + '">' +
                '<input type="hidden" name="items_data[' + (count - 1) + '][sort_order]" value="' + count + '">';
        });
    }

    var itemSelectForLots = document.getElementById('item_id');
    if (itemSelectForLots) {
        itemSelectForLots.addEventListener('change', function () {
            populateLotDropdown(this.value);
        });
    }

    var meterInput = document.getElementById('meter');
    if (meterInput) {
        meterInput.addEventListener('input', refreshAmount);
    }

    var rateInput = document.getElementById('rate');
    if (rateInput) {
        rateInput.addEventListener('input', refreshAmount);
    }

    document.getElementById('addItemBtn').addEventListener('click', function () {
        var itemSelect = document.getElementById('item_id');
        var itemId = itemSelect.value;
        var itemName = itemSelect.selectedOptions.length ? itemSelect.selectedOptions[0].text : '';
        var lotSelect = document.getElementById('lot_no');
        var lotNo = lotSelect.value.trim();
        var meter = toNumber(document.getElementById('meter').value);
        var rate = toNumber(document.getElementById('rate').value);
        var amount = toNumber(document.getElementById('amount').value);

        if (!itemId || !lotNo || meter <= 0 || rate <= 0) {
            toastr.error('Please fill item, lot no, meter and rate correctly.');
            return;
        }

        var body = document.getElementById('dispatchItemsBody');
        var noItemRow = document.getElementById('noItemsRow');
        if (noItemRow) noItemRow.remove();

        var rowCount = getDataRows().length;
        var row = document.createElement('tr');
        row.dataset.index = rowCount;
        row.innerHTML =
            '<td>' + (rowCount + 1) + '</td>' +
            '<td>' + lotNo + '</td>' +
            '<td>' + itemName + '</td>' +
            '<td>' + meter.toFixed(2) + '</td>' +
            '<td>' + rate.toFixed(2) + '</td>' +
            '<td>' + amount.toFixed(2) + '</td>' +
            '<td><button type="button" class="btn btn-sm btn-danger removeItemBtn">Remove</button></td>' +
            '<td class="d-none">' +
            '<input type="hidden" name="items_data[' + rowCount + '][item_id]" value="' + itemId + '">' +
            '<input type="hidden" name="items_data[' + rowCount + '][lot_no]" value="' + lotNo + '">' +
            '<input type="hidden" name="items_data[' + rowCount + '][item_code]" value="' + lotNo + '">' +
            '<input type="hidden" name="items_data[' + rowCount + '][meter]" value="' + meter + '">' +
            '<input type="hidden" name="items_data[' + rowCount + '][rate]" value="' + rate + '">' +
            '<input type="hidden" name="items_data[' + rowCount + '][amount]" value="' + amount + '">' +
            '<input type="hidden" name="items_data[' + rowCount + '][sort_order]" value="' + (rowCount + 1) + '">' +
            '</td>';

        body.appendChild(row);

        document.getElementById('item_id').value = '';
        document.getElementById('lot_no').innerHTML = '<option value="">Select lot</option>';
        document.getElementById('lot_no').disabled = true;
        document.getElementById('meter').value = '';
        document.getElementById('rate').value = '';
        document.getElementById('amount').value = '';

        updateTotals();
    });

    document.getElementById('dispatchItemsBody').addEventListener('click', function (event) {
        if (event.target.classList.contains('removeItemBtn')) {
            var row = event.target.closest('tr');
            row.remove();
            if (document.querySelectorAll('#dispatchItemsBody tr').length === 0) {
                var noItemsRow = document.createElement('tr');
                noItemsRow.id = 'noItemsRow';
                noItemsRow.innerHTML = '<td colspan="7" class="text-center">No items added yet.</td>';
                document.getElementById('dispatchItemsBody').appendChild(noItemsRow);
            }
            reIndexItems();
            updateTotals();
        }
    });

    // initial totals and lot dropdown setup in case editing existing dispatch
    updateTotals();
    populateLotDropdown(document.getElementById('item_id').value);
})();
</script>
