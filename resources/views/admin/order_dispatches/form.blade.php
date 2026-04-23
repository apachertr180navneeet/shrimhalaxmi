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
<script>
    (function($) {
        "use strict";

        // 1. Data initialization
        var lotSources = @json($lotSources->toArray());

        $(document).ready(function() {
            // 2. Initialize Select2 on the Lot Number field
            $('#lot_no').select2({
                placeholder: "Select lot",
                allowClear: true,
                width: '100%'
            });

            // 3. Helper Functions
            function toNumber(value) {
                var n = parseFloat(value);
                return Number.isFinite(n) ? n : 0;
            }

            function refreshAmount() {
                var meter = toNumber($('#meter').val());
                var rate = toNumber($('#rate').val());
                var amount = meter * rate;
                var gst = amount * 0.18;
                var itemTotalAmount = amount + gst;

                $('#amount').val(amount.toFixed(2));
                $('#gst').val(gst.toFixed(2));
                $('#item_total_amount').val(itemTotalAmount.toFixed(2));
            }

            // 4. Populate Lot Dropdown logic
            window.populateLotDropdown = function(itemId) {
                var lotSelect = $('#lot_no');
                lotSelect.empty().append('<option value="">Select lot</option>');

                if (!itemId) {
                    lotSelect.prop('disabled', true).trigger('change');
                    return;
                }

                var seen = new Set();
                lotSources.forEach(function(source) {
                    if (String(source.item_id) !== String(itemId)) return;

                    var lotNo = source.lot_no;
                    var available = parseFloat(source.total_meter || 0);

                    if (!lotNo || seen.has(lotNo)) return;
                    seen.add(lotNo);

                    var opt = new Option(lotNo + ' (Available: ' + available + ')', lotNo,
                        false, false);
                    $(opt).attr('data-meter', available);
                    lotSelect.append(opt);
                });

                lotSelect.prop('disabled', false).trigger('change');
            };

            // 5. Event Listeners

            // Item selection change
            $('#item_id').on('change', function() {
                window.populateLotDropdown(this.value);
            });

            // Lot selection change (Select2 compatible)
            $('#lot_no').on('change', function() {
                var meterInput = $('#meter');
                var selected = $(this).find(':selected');

                if (!this.value) {
                    meterInput.prop('disabled', true).val('');
                    $('#available_meter').val('');
                    return;
                }

                var available = parseFloat(selected.attr('data-meter') || 0);
                meterInput.prop('disabled', false).attr('max', available);
                $('#available_meter').val(available.toFixed(2));
            });

            // Meter input validation and calculation
            $('#meter').on('input', function() {
                var selected = $('#lot_no').find(':selected');
                var available = parseFloat(selected.attr('data-meter') || 0);
                var entered = parseFloat($(this).val() || 0);

                if (entered > available) {
                    $(this).val(available);
                    if (window.toastr) toastr.warning('Max allowed: ' + available);
                }
                refreshAmount();
            });

            // Rate input change
            $('#rate').on('input', refreshAmount);

            // 6. Add Item to Table
            $('#addItemBtn').on('click', function() {
                var itemSelect = $('#item_id');
                var lotSelect = $('#lot_no');
                var selectedLot = lotSelect.find(':selected');

                var itemId = itemSelect.val();
                var itemName = itemSelect.find(':selected').text();
                var lotNo = lotSelect.val();

                if (!selectedLot.length || !lotNo) {
                    if (window.toastr) toastr.error('Select lot first');
                    return;
                }

                var available = parseFloat(selectedLot.attr('data-meter') || 0);
                var meter = toNumber($('#meter').val());
                var rate = toNumber($('#rate').val());

                if (!itemId || meter <= 0 || rate <= 0) {
                    if (window.toastr) toastr.error('Fill all fields correctly');
                    return;
                }

                if (meter > available) {
                    if (window.toastr) toastr.error('Meter exceeds available stock!');
                    return;
                }

                // Check for duplicate lot in table
                var exists = false;
                $('#dispatchItemsBody tr').each(function() {
                    if ($(this).find('td:first').text() === lotNo) {
                        exists = true;
                    }
                });

                if (exists) {
                    if (window.toastr) toastr.error('This lot already added!');
                    return;
                }

                var amount = toNumber($('#amount').val());
                var gst = toNumber($('#gst').val());
                var total = toNumber($('#item_total_amount').val());
                var tbody = $('#dispatchItemsBody');
                $('#noItemsRow').remove();

                var index = tbody.find('tr').length;

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
                        <input type="hidden" name="items_data[${index}][sort_order]" value="${index + 1}">
                    </td>
                </tr>`;

                tbody.append(row);

                // Reset item entry fields
                $('#meter').val('').prop('disabled', true);
                $('#rate').val('');
                $('#amount').val('');
                $('#gst').val('');
                $('#item_total_amount').val('');
                $('#available_meter').val('');
                lotSelect.val(null).trigger('change');
            });

            // 7. Remove Item from Table
            $(document).on('click', '.removeItemBtn', function() {
                $(this).closest('tr').remove();
                if ($('#dispatchItemsBody tr').length === 0) {
                    $('#dispatchItemsBody').append(
                        '<tr id="noItemsRow"><td colspan="8" class="text-center">No items added yet.</td></tr>'
                        );
                }
            });
        });
    })(jQuery);
</script>
