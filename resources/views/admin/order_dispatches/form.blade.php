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

<style>
    .dispatch-form-card {
        border: 1px solid #dde4f0;
        border-radius: 12px;
        padding: 18px;
        background: #f9fbff;
    }

    .dispatch-form-card .form-control,
    .dispatch-form-card .form-select {
        border: 1px solid #cfd6e4;
        border-radius: 8px;
        min-height: 38px;
        box-shadow: none;
    }

    #dispatchItemsTable th {
        background: #eef3fb;
        font-weight: 600;
    }
</style>

<div class="dispatch-form-card">
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Date</label>
            <input type="date" name="dispatch_date" class="form-control @error('dispatch_date') is-invalid @enderror"
                value="{{ $dispatchDate }}" required>
            @error('dispatch_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-4">
            <label class="form-label">BALE No.</label>
            <input type="text" name="dispatch_no" class="form-control @error('dispatch_no') is-invalid @enderror"
                value="{{ $dispatchNo }}" readonly required>
            @error('dispatch_no')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-4">
            <label class="form-label">Bill No</label>
            <input type="text" name="bill_no" class="form-control @error('bill_no') is-invalid @enderror"
                value="{{ $billNo }}">
            @error('bill_no')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="row g-3 mt-3">
        <div class="col-md-6">
            <label class="form-label">Customer</label>
            <select id="customer_id" name="customer_id" class="form-select @error('customer_id') is-invalid @enderror"
                required>
                <option value="" data-abbr="">Select customer</option>
                @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}" data-abbr="{{ $customer->abbr ?? '' }}"
                        {{ (string) $customerId === (string) $customer->id ? 'selected' : '' }}>
                        {{ $customer->name }}{{ $customer->abbr ? ' (' . $customer->abbr . ')' : '' }}</option>
                @endforeach
            </select>
            @error('customer_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Transport</label>
            <input type="text" name="transport" class="form-control @error('transport') is-invalid @enderror"
                value="{{ $transport }}">
            @error('transport')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
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
            @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <h6 class="mt-4">Items Details</h6>
    <div class="row g-3">
        <div class="col-md-2">
            <label class="form-label">Item</label>
            <select id="item_id" class="form-select">
                <option value="">Select item</option>
                @foreach ($items as $item)
                    <option value="{{ $item->id }}" data-name="{{ $item->item_name }}"
                        data-abbr="{{ $item->abbr }}">{{ $item->item_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Lot No</label>
            <select id="lot_no" class="form-select" disabled>
                <option value="">Select lot</option>
            </select>
        </div>
        <!-- ✅ ADD THIS FIELD (Available Meter) -->
        <div class="col-md-2">
            <label class="form-label">Available Meter</label>
            <input type="text" id="available_meter" class="form-control" readonly>
        </div>
        <div class="col-md-1">
            <label class="form-label">Meter</label>
            <input type="number" step="0.01" id="meter" class="form-control" placeholder="Meter" disabled>
        </div>
        <div class="col-md-1">
            <label class="form-label">Rate</label>
            <input type="number" step="0.01" id="rate" class="form-control" placeholder="Rate">
        </div>
        <div class="col-md-2">
            <label class="form-label">Amount</label>
            <input type="number" step="0.01" id="amount" class="form-control" placeholder="Amount" readonly>
        </div>
        <div class="col-md-2">
            <label class="form-label">GST (18%)</label>
            <input type="number" step="0.01" id="gst" class="form-control" placeholder="GST" readonly>
        </div>
        <div class="col-md-2">
            <label class="form-label">Total Amount</label>
            <input type="number" step="0.01" id="item_total_amount" class="form-control"
                placeholder="Total Amount" readonly>
        </div>
    </div>

    <div class="mt-2">
        <button type="button" id="addItemBtn" class="btn btn-primary">Add Item</button>
    </div>

    <div class="table-responsive mt-3">
        <table class="table table-bordered" id="dispatchItemsTable">
            <thead>
                <tr>
                    <th>Lot No</th>
                    <th>Item</th>
                    <th>Meter</th>
                    <th>Rate</th>
                    <th>Amount</th>
                    <th>GST (18%)</th>
                    <th>Total Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="dispatchItemsBody">
                @forelse ($dispatchItems as $index => $row)
                    <tr data-index="{{ $index }}">
                        <td>{{ $row->lot_no }}</td>
                        <td>{{ $row->item?->item_name ?? '-' }}</td>
                        <td>{{ number_format($row->meter, 2, '.', '') }}</td>
                        <td>{{ number_format($row->rate, 2, '.', '') }}</td>
                        <td>{{ number_format($row->amount, 2, '.', '') }}</td>
                        <td>{{ number_format((float) ($row->gst ?? 0), 2, '.', '') }}</td>
                        <td>{{ number_format((float) ($row->total_amount ?? $row->amount), 2, '.', '') }}</td>
                        <td><button type="button" class="btn btn-sm btn-danger removeItemBtn">Remove</button></td>
                        <td class="d-none">
                            <input type="hidden" name="items_data[{{ $index }}][item_id]"
                                value="{{ $row->item_id }}">
                            <input type="hidden" name="items_data[{{ $index }}][lot_no]"
                                value="{{ $row->lot_no }}">
                            <input type="hidden" name="items_data[{{ $index }}][item_code]"
                                value="{{ $row->item_code }}">
                            <input type="hidden" name="items_data[{{ $index }}][meter]"
                                value="{{ $row->meter }}">
                            <input type="hidden" name="items_data[{{ $index }}][rate]"
                                value="{{ $row->rate }}">
                            <input type="hidden" name="items_data[{{ $index }}][amount]"
                                value="{{ $row->amount }}">
                            <input type="hidden" name="items_data[{{ $index }}][gst]"
                                value="{{ $row->gst ?? 0 }}">
                            <input type="hidden" name="items_data[{{ $index }}][total_amount]"
                                value="{{ $row->total_amount ?? $row->amount }}">
                            <input type="hidden" name="items_data[{{ $index }}][sort_order]"
                                value="{{ $row->sort_order }}">
                        </td>
                    </tr>
                @empty
                    <tr id="noItemsRow">
                        <td colspan="8" class="text-center">No items added yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
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
    (function() {

        var lotSources = @json($lotSources->toArray());
        

        function toNumber(value) {
            var n = parseFloat(value);
            return Number.isFinite(n) ? n : 0;
        }

        function refreshAmount() {
            var meter = toNumber(document.getElementById('meter').value);
            var rate = toNumber(document.getElementById('rate').value);
            var amount = meter * rate;
            var gst = amount * 0.18;
            var itemTotalAmount = amount + gst;

            document.getElementById('amount').value = amount.toFixed(2);
            document.getElementById('gst').value = gst.toFixed(2);
            document.getElementById('item_total_amount').value = itemTotalAmount.toFixed(2);
        }

        function populateLotDropdown(itemId) {

            var lotSelect = document.getElementById('lot_no');
            lotSelect.innerHTML = '<option value="">Select lot</option>';

            if (!itemId) {
                lotSelect.disabled = true;
                return;
            }

            var seen = new Set();

            lotSources.forEach(function(source) {

                console.log(source)

                if (String(source.item_id) !== String(itemId)) return;

                var lotNo = source.lot_no;
                var available = parseFloat(source.total_meter || 0);

                if (!lotNo || seen.has(lotNo)) return;

                seen.add(lotNo);

                var opt = document.createElement('option');
                opt.value = lotNo;
                opt.textContent = lotNo + ' (Available: ' + available + ')';
                opt.setAttribute('data-meter', available);

                lotSelect.appendChild(opt);

            });

            lotSelect.disabled = false;
        }

        // ✅ ITEM CHANGE
        document.getElementById('item_id').addEventListener('change', function() {
            populateLotDropdown(this.value);
        });

        // ✅ LOT CHANGE
        document.getElementById('lot_no').addEventListener('change', function() {

            var meterInput = document.getElementById('meter');

            if (!this.value) {
                meterInput.disabled = true;
                meterInput.value = '';
                return;
            }

            var selected = this.selectedOptions[0];
            var available = parseFloat(selected.getAttribute('data-meter') || 0);

            meterInput.disabled = false;
            meterInput.setAttribute('max', available);

            // show available
            document.getElementById('available_meter').value = available.toFixed(2);

        });

        // ✅ LIVE VALIDATION
        document.getElementById('meter').addEventListener('input', function() {

            var lotSelect = document.getElementById('lot_no');
            var selected = lotSelect.selectedOptions[0];
            if (!selected) return;

            var available = parseFloat(selected.getAttribute('data-meter') || 0);
            var entered = parseFloat(this.value || 0);

            if (entered > available) {
                this.value = available;
                toastr.warning('Max allowed: ' + available);
            }

            refreshAmount();
        });

        document.getElementById('rate').addEventListener('input', refreshAmount);

        // ✅ ADD ITEM
        document.getElementById('addItemBtn').addEventListener('click', function() {

            var itemSelect = document.getElementById('item_id');
            var lotSelect = document.getElementById('lot_no');

            var itemId = itemSelect.value;
            var itemName = itemSelect.selectedOptions[0]?.text || '';
            var lotNo = lotSelect.value;

            var selected = lotSelect.selectedOptions[0];

            if (!selected) {
                toastr.error('Select lot first');
                return;
            }

            var available = parseFloat(selected.getAttribute('data-meter') || 0);
            var meter = toNumber(document.getElementById('meter').value);

            // ❌ VALIDATION
            if (meter > available) {
                toastr.error('Meter exceeds available stock!');
                return;
            }

            // ❌ DUPLICATE LOT CHECK
            var exists = false;
            document.querySelectorAll('#dispatchItemsBody tr').forEach(function(row) {
                if (row.children[0]?.innerText === lotNo) {
                    exists = true;
                }
            });

            if (exists) {
                toastr.error('This lot already added!');
                return;
            }

            var rate = toNumber(document.getElementById('rate').value);
            var amount = toNumber(document.getElementById('amount').value);
            var gst = toNumber(document.getElementById('gst').value);
            var total = toNumber(document.getElementById('item_total_amount').value);

            if (!itemId || !lotNo || meter <= 0 || rate <= 0) {
                toastr.error('Fill all fields correctly');
                return;
            }

            var tbody = document.getElementById('dispatchItemsBody');
            document.getElementById('noItemsRow')?.remove();

            var index = tbody.querySelectorAll('tr').length;

            var row = `
        <tr>
            <td>${lotNo}</td>
            <td>${itemName}</td>
            <td>${meter.toFixed(2)}</td>
            <td>${rate.toFixed(2)}</td>
            <td>${amount.toFixed(2)}</td>
            <td>${gst.toFixed(2)}</td>
            <td>${total.toFixed(2)}</td>
            <td><button type="button" class="btn btn-danger btn-sm removeItemBtn">Remove</button></td>
            <td class="d-none">
                <input type="hidden" name="items_data[${index}][item_id]" value="${itemId}">
                <input type="hidden" name="items_data[${index}][lot_no]" value="${lotNo}">
                <input type="hidden" name="items_data[${index}][meter]" value="${meter}">
                <input type="hidden" name="items_data[${index}][rate]" value="${rate}">
                <input type="hidden" name="items_data[${index}][amount]" value="${amount}">
                <input type="hidden" name="items_data[${index}][gst]" value="${gst}">
                <input type="hidden" name="items_data[${index}][total_amount]" value="${total}">
                <input type="hidden" name="items_data[${index}][sort_order]" value="${index+1}">
            </td>
        </tr>`;

            tbody.insertAdjacentHTML('beforeend', row);

            // reset
            document.getElementById('meter').value = '';
            document.getElementById('rate').value = '';
            document.getElementById('amount').value = '';
            document.getElementById('gst').value = '';
            document.getElementById('item_total_amount').value = '';

        });

        // REMOVE ITEM
        document.getElementById('dispatchItemsBody').addEventListener('click', function(e) {
            if (e.target.classList.contains('removeItemBtn')) {
                e.target.closest('tr').remove();
            }
        });

    })();
</script>
