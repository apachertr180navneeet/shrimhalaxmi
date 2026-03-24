<style>
    .purchase-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 22px 28px;
    }

    .purchase-field-grid {
        display: grid;
        grid-template-columns: 160px 1fr;
        align-items: center;
        gap: 12px;
        margin-bottom: 14px;
    }

    .purchase-field-grid label {
        margin: 0;
        font-weight: 500;
        color: #111827;
    }

    .purchase-field-grid .form-control,
    .purchase-field-grid .form-select {
        border: 1px solid #2f2f2f;
        border-radius: 0;
        min-height: 38px;
        box-shadow: none;
    }

    .purchase-field-grid textarea.form-control {
        min-height: 74px;
    }

    .purchase-inline-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 18px 24px;
        margin-top: 44px;
    }

    .purchase-bottom-grid {
        margin-top: 26px;
    }

    .purchase-item-table th,
    .purchase-item-table td {
        font-size: 14px;
        vertical-align: middle;
        border: 1px solid #2f2f2f;
    }

    .purchase-item-table thead th {
        background: #f5f5f9;
        color: #111827;
        font-weight: 600;
    }

    .remove-link {
        color: #dc3545;
        font-weight: 600;
        text-decoration: none;
    }

    .purchase-empty-state {
        text-align: center;
        color: #6b7280;
        padding: 18px;
    }

    .d-none {
        display: none !important;
    }

    @media (max-width: 991.98px) {
        .purchase-form-grid,
        .purchase-inline-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 575.98px) {
        .purchase-field-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

@php
    $purchase = $purchase ?? [];
    $vendors = $vendors ?? collect();
    $items = $items ?? collect();
    $purchaseItems = $purchaseItems ?? collect();
    $defaultItemAbbr = $items->first()->abbr ?? 'ITEM';
    $defaultVendorAbbr = $vendors->first()->abbr ?? 'VENDOR';
    $pchNo = old('pch_no', $purchase['pch_no'] ?? '0001');
    $itemAbbr = old('item_abbr', $purchase['item_abbr'] ?? $defaultItemAbbr);
    $vendorAbbr = old('vendor_abbr', $purchase['vendor_abbr'] ?? $defaultVendorAbbr);
    $lotNo = old('lot_no', $purchase['lot_no'] ?? (($vendorAbbr ?: $defaultVendorAbbr) . ' / ' . ($pchNo ?: '0001') . ' / 0001'));
@endphp

<input type="hidden" name="vendor_abbr" id="vendor_abbr" value="{{ $vendorAbbr }}">
<input type="hidden" name="item_abbr" id="item_abbr" value="{{ $itemAbbr }}">
<input type="hidden" name="amount" id="amount" value="{{ old('amount', $purchase['amount'] ?? '') }}">

<div class="purchase-form-grid">
    <div>
        <div class="purchase-field-grid">
            <label>Date</label>
            <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', $purchase['date'] ?? '') }}">
        </div>

        <div class="purchase-field-grid">
            <label>BNO</label>
            <input type="text" name="bno" class="form-control @error('bno') is-invalid @enderror" value="{{ old('bno', $purchase['bno'] ?? '') }}">
        </div>
    </div>

    <div>
        <div class="purchase-field-grid">
            <label>PCH. NO.</label>
            <input type="text" name="pch_no" id="pch_no" class="form-control @error('pch_no') is-invalid @enderror" value="{{ $pchNo }}" readonly>
        </div>

        <div class="purchase-field-grid">
            <label>Vendor Name</label>
            <select name="vendor_id" id="vendor_id" class="form-select @error('vendor_id') is-invalid @enderror">
                <option value="">Select Vendor</option>
                @foreach ($vendors as $vendor)
                    <option value="{{ $vendor->id }}" data-abbr="{{ $vendor->abbr ?: $defaultVendorAbbr }}" {{ (string) old('vendor_id', $purchase['vendor_id'] ?? '') === (string) $vendor->id ? 'selected' : '' }}>
                        {{ $vendor->vendor_name }}{{ $vendor->abbr ? ' (' . $vendor->abbr . ')' : '' }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="purchase-form-grid">
    <div>
        <div class="purchase-field-grid">
            <label>Remark</label>
            <textarea name="remark" class="form-control">{{ old('remark', $purchase['remark'] ?? '') }}</textarea>
        </div>
    </div>

    <div>
        <div class="purchase-field-grid">
            <label>Freight</label>
            <select name="freight" class="form-select">
                <option value="">Select Freight</option>
                <option value="Paid" {{ old('freight', $purchase['freight'] ?? '') === 'Paid' ? 'selected' : '' }}>Paid</option>
                <option value="To be Paid" {{ old('freight', $purchase['freight'] ?? '') === 'To be Paid' ? 'selected' : '' }}>To be Paid</option>
                <option value="To be Shiped" {{ old('freight', $purchase['freight'] ?? '') === 'To be Shiped' ? 'selected' : '' }}>To be Shiped</option>
            </select>
        </div>
    </div>
</div>

<div class="purchase-inline-grid">
    <div class="purchase-field-grid">
        <label>LOT NO.</label>
        <input type="text" id="lot_no" class="form-control" value="{{ $lotNo }}" readonly>
    </div>

    <div class="purchase-field-grid">
        <label>Item Name</label>
        <select id="item_id" class="form-select">
            <option value="">Select Item</option>
            @foreach ($items as $item)
                <option value="{{ $item->id }}" data-abbr="{{ $item->abbr ?: $defaultItemAbbr }}" data-name="{{ $item->item_name }}">
                    {{ $item->item_name }}{{ $item->abbr ? ' (' . $item->abbr . ')' : '' }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="purchase-field-grid">
        <label>Quality</label>
        <input type="text" id="quality" class="form-control" value="{{ old('quality', $purchase['quality'] ?? '') }}">
    </div>

    <div class="purchase-field-grid">
        <label>Qty. (M)</label>
        <input type="text" id="qty_m" class="form-control" value="{{ old('qty_m', $purchase['qty_m'] ?? '') }}">
    </div>

    <div class="purchase-field-grid">
        <label>Fold</label>
        <input type="text" id="fold" class="form-control" value="{{ old('fold', $purchase['fold'] ?? '') }}">
    </div>

    <div class="purchase-field-grid">
        <label>Rate</label>
        <input type="text" id="rate" class="form-control" value="{{ old('rate', $purchase['rate'] ?? '') }}">
    </div>

    <div class="purchase-field-grid">
        <label>Transport</label>
        <input type="text" id="transport" class="form-control" value="{{ old('transport', $purchase['transport'] ?? '') }}">
    </div>

    <div class="purchase-field-grid">
        <label>LR NO.</label>
        <input type="text" id="lr_no" class="form-control" value="{{ old('lr_no', $purchase['lr_no'] ?? '') }}">
    </div>

    <div class="purchase-field-grid">
        <label>Net Meter</label>
        <input type="text" id="net_meter" class="form-control" value="{{ old('net_meter', $purchase['net_meter'] ?? '') }}" readonly>
    </div>
</div>

@error('items_data')
<div class="text-danger small mb-2">{{ $message }}</div>
@enderror

<div class="purchase-bottom-grid">
    <div class="text-end mb-3">
        <button type="button" id="add_more_item" class="btn btn-primary">Add More Item</button>
    </div>

    <div class="table-responsive">
        <table class="table purchase-item-table align-middle mb-0">
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>LOT No.</th>
                    <th>Item Name</th>
                    <th>Quality</th>
                    <th>Rate</th>
                    <th>Qty. (M)</th>
                    <th>Fold</th>
                    <th>Transport</th>
                    <th>LR NO.</th>
                    <th>Net Meter</th>
                    <th>Amount</th>
                    <th>Remove</th>
                </tr>
            </thead>
            <tbody id="purchase_items_body">
                @forelse ($purchaseItems as $index => $row)
                    <tr data-index="{{ $index }}" data-item-id="{{ $row->item_id }}" data-quality="{{ $row->quality }}" data-qty="{{ $row->qty_m }}" data-fold="{{ $row->fold }}" data-rate="{{ $row->rate }}" data-transport="{{ $row->transport }}" data-lr-no="{{ $row->lr_no }}" data-net-meter="{{ $row->net_meter }}" data-amount="{{ $row->amount }}">
                        <td>{{ $index + 1 }}.</td>
                        <td>{{ $row->lot_no }}</td>
                        <td>{{ $row->item?->item_name }}</td>
                        <td>{{ $row->quality }}</td>
                        <td>{{ rtrim(rtrim(number_format((float) $row->rate, 2, '.', ''), '0'), '.') }}</td>
                        <td>{{ rtrim(rtrim(number_format((float) $row->qty_m, 2, '.', ''), '0'), '.') }}</td>
                        <td>{{ rtrim(rtrim(number_format((float) $row->fold, 2, '.', ''), '0'), '.') }}</td>
                        <td>{{ $row->transport }}</td>
                        <td>{{ $row->lr_no }}</td>
                        <td>{{ number_format((float) $row->net_meter, 2, '.', '') }}</td>
                        <td>{{ number_format((float) $row->amount, 2, '.', '') }}</td>
                        <td><a href="javascript:void(0)" class="remove-link remove-row">Remove</a></td>
                        <td class="d-none row-hidden-inputs">
                            <input type="hidden" name="items_data[{{ $index }}][item_id]" value="{{ $row->item_id }}">
                            <input type="hidden" name="items_data[{{ $index }}][lot_no]" value="{{ $row->lot_no }}">
                            <input type="hidden" name="items_data[{{ $index }}][item_code]" value="{{ $row->item_code ?: $row->lot_no }}">
                            <input type="hidden" name="items_data[{{ $index }}][quality]" value="{{ $row->quality }}">
                            <input type="hidden" name="items_data[{{ $index }}][qty_m]" value="{{ $row->qty_m }}">
                            <input type="hidden" name="items_data[{{ $index }}][fold]" value="{{ $row->fold }}">
                            <input type="hidden" name="items_data[{{ $index }}][rate]" value="{{ $row->rate }}">
                            <input type="hidden" name="items_data[{{ $index }}][transport]" value="{{ $row->transport }}">
                            <input type="hidden" name="items_data[{{ $index }}][lr_no]" value="{{ $row->lr_no }}">
                            <input type="hidden" name="items_data[{{ $index }}][net_meter]" value="{{ $row->net_meter }}">
                            <input type="hidden" name="items_data[{{ $index }}][amount]" value="{{ $row->amount }}">
                            <input type="hidden" name="items_data[{{ $index }}][sort_order]" value="{{ $row->sort_order }}">
                        </td>
                    </tr>
                @empty
                    <tr id="purchase_empty_row">
                        <td colspan="12" class="purchase-empty-state">No items added yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const itemSelect = document.getElementById('item_id');
        const vendorSelect = document.getElementById('vendor_id');
        const vendorAbbrInput = document.getElementById('vendor_abbr');
        const itemAbbrInput = document.getElementById('item_abbr');
        const pchNoInput = document.getElementById('pch_no');
        const lotNoInput = document.getElementById('lot_no');
        const qtyInput = document.getElementById('qty_m');
        const foldInput = document.getElementById('fold');
        const rateInput = document.getElementById('rate');
        const transportInput = document.getElementById('transport');
        const lrNoInput = document.getElementById('lr_no');
        const netMeterInput = document.getElementById('net_meter');
        const amountInput = document.getElementById('amount');
        const qualityInput = document.getElementById('quality');
        const addMoreButton = document.getElementById('add_more_item');
        const tableBody = document.getElementById('purchase_items_body');
        const emptyRowId = 'purchase_empty_row';
        const defaultItemAbbr = '{{ $defaultItemAbbr }}';
        const defaultVendorAbbr = '{{ $defaultVendorAbbr }}';

        function toNumber(value) {
            const number = parseFloat(value);
            return Number.isFinite(number) ? number : 0;
        }

        function formatFixed(value) {
            return (Math.round(value * 100) / 100).toFixed(2);
        }

        function currentItemOption() {
            return itemSelect ? itemSelect.options[itemSelect.selectedIndex] : null;
        }

        function currentItemAbbr() {
            const option = currentItemOption();
            return option && option.dataset.abbr ? option.dataset.abbr : defaultItemAbbr;
        }

        function currentVendorAbbr() {
            const option = vendorSelect ? vendorSelect.options[vendorSelect.selectedIndex] : null;
            return option && option.dataset.abbr ? option.dataset.abbr : defaultVendorAbbr;
        }

        function currentItemName() {
            const option = currentItemOption();
            return option && option.dataset.name ? option.dataset.name : '';
        }

        function buildLotNo(serial) {
            const paddedSerial = String(serial).padStart(4, '0');
            const pchNo = pchNoInput && pchNoInput.value ? pchNoInput.value : '0001';
            return currentVendorAbbr() + ' / ' + pchNo + ' / ' + paddedSerial;
        }

        function getDataRows() {
            return Array.from(tableBody.querySelectorAll('tr')).filter(function (row) {
                return row.id !== emptyRowId;
            });
        }

        function recalculate() {
            const qty = toNumber(qtyInput.value);
            const fold = toNumber(foldInput.value);
            const rate = toNumber(rateInput.value);
            const netMeter = (qty * fold) / 100;
            const amount = netMeter * rate;

            netMeterInput.value = qtyInput.value || foldInput.value ? formatFixed(netMeter) : '';
            amountInput.value = netMeterInput.value && rateInput.value ? formatFixed(amount) : '';
        }

        function syncAbbrFields() {
            vendorAbbrInput.value = currentVendorAbbr();
            itemAbbrInput.value = currentItemAbbr();
        }

        function updateCurrentLotNo() {
            const serial = getDataRows().length + 1;
            lotNoInput.value = buildLotNo(serial);
        }

        function updateRowHiddenInputs(row, index, data) {
            const holder = row.querySelector('.row-hidden-inputs');
            holder.innerHTML = '' +
                '<input type="hidden" name="items_data[' + index + '][item_id]" value="' + data.item_id + '">' +
                '<input type="hidden" name="items_data[' + index + '][lot_no]" value="' + data.lot_no + '">' +
                '<input type="hidden" name="items_data[' + index + '][item_code]" value="' + data.item_code + '">' +
                '<input type="hidden" name="items_data[' + index + '][quality]" value="' + data.quality + '">' +
                '<input type="hidden" name="items_data[' + index + '][qty_m]" value="' + data.qty_m + '">' +
                '<input type="hidden" name="items_data[' + index + '][fold]" value="' + data.fold + '">' +
                '<input type="hidden" name="items_data[' + index + '][rate]" value="' + data.rate + '">' +
                '<input type="hidden" name="items_data[' + index + '][transport]" value="' + data.transport + '">' +
                '<input type="hidden" name="items_data[' + index + '][lr_no]" value="' + data.lr_no + '">' +
                '<input type="hidden" name="items_data[' + index + '][net_meter]" value="' + data.net_meter + '">' +
                '<input type="hidden" name="items_data[' + index + '][amount]" value="' + data.amount + '">' +
                '<input type="hidden" name="items_data[' + index + '][sort_order]" value="' + data.sort_order + '">';
        }

        function reindexRows() {
            const rows = getDataRows();
            rows.forEach(function (row, index) {
                const serial = index + 1;
                row.setAttribute('data-index', index);
                const lotNo = buildLotNo(serial);
                row.children[0].textContent = serial + '.';
                row.children[1].textContent = lotNo;

                updateRowHiddenInputs(row, index, {
                    item_id: row.dataset.itemId,
                    lot_no: lotNo,
                    item_code: lotNo,
                    quality: row.dataset.quality,
                    qty_m: row.dataset.qty,
                    fold: row.dataset.fold,
                    rate: row.dataset.rate,
                    transport: row.dataset.transport || '',
                    lr_no: row.dataset.lrNo || '',
                    net_meter: row.dataset.netMeter,
                    amount: row.dataset.amount,
                    sort_order: serial,
                });
            });
            updateCurrentLotNo();
        }

        function ensureEmptyState() {
            const rows = getDataRows();
            const emptyRow = document.getElementById(emptyRowId);

            if (rows.length === 0) {
                if (!emptyRow) {
                    const row = document.createElement('tr');
                    row.id = emptyRowId;
                    row.innerHTML = '<td colspan="12" class="purchase-empty-state">No items added yet.</td>';
                    tableBody.appendChild(row);
                }
            } else if (emptyRow) {
                emptyRow.remove();
            }
        }

        function clearItemEntryFields() {
            itemSelect.value = '';
            qualityInput.value = '';
            qtyInput.value = '';
            foldInput.value = '';
            rateInput.value = '';
            transportInput.value = '';
            lrNoInput.value = '';
            netMeterInput.value = '';
            amountInput.value = '';
            syncAbbrFields();
        }

        function addRow() {
            const itemOption = currentItemOption();
            if (!itemOption || !itemOption.value) {
                toastr.error('Select item first');
                return;
            }

            const qty = qtyInput.value.trim();
            const fold = foldInput.value.trim();
            const rate = rateInput.value.trim();

            if (!qty || !fold || !rate) {
                toastr.error('Enter qty, fold and rate');
                return;
            }

            ensureEmptyState();

            const nextIndex = getDataRows().length;
            const serial = nextIndex + 1;
            const lotNo = buildLotNo(serial);
            const row = document.createElement('tr');
            row.dataset.itemId = itemOption.value;
            row.dataset.quality = qualityInput.value || '';
            row.dataset.qty = qty;
            row.dataset.fold = fold;
            row.dataset.rate = rate;
            row.dataset.transport = transportInput.value || '';
            row.dataset.lrNo = lrNoInput.value || '';
            row.dataset.netMeter = netMeterInput.value || '0.00';
            row.dataset.amount = amountInput.value || '0.00';

            row.innerHTML = '' +
                '<td>' + serial + '.</td>' +
                '<td>' + lotNo + '</td>' +
                '<td>' + currentItemName() + '</td>' +
                '<td>' + (qualityInput.value || '') + '</td>' +
                '<td>' + rate + '</td>' +
                '<td>' + qty + '</td>' +
                '<td>' + fold + '</td>' +
                '<td>' + (transportInput.value || '') + '</td>' +
                '<td>' + (lrNoInput.value || '') + '</td>' +
                '<td>' + (netMeterInput.value || '0.00') + '</td>' +
                '<td>' + (amountInput.value || '0.00') + '</td>' +
                '<td><a href="javascript:void(0)" class="remove-link remove-row">Remove</a></td>' +
                '<td class="d-none row-hidden-inputs"></td>';

            tableBody.appendChild(row);

            updateRowHiddenInputs(row, nextIndex, {
                item_id: itemOption.value,
                lot_no: lotNo,
                item_code: lotNo,
                quality: qualityInput.value || '',
                qty_m: qty,
                fold: fold,
                rate: rate,
                transport: transportInput.value || '',
                lr_no: lrNoInput.value || '',
                net_meter: netMeterInput.value || '0.00',
                amount: amountInput.value || '0.00',
                sort_order: serial,
            });

            ensureEmptyState();
            clearItemEntryFields();
            updateCurrentLotNo();
        }

        qtyInput?.addEventListener('input', recalculate);
        foldInput?.addEventListener('input', recalculate);
        rateInput?.addEventListener('input', recalculate);
        itemSelect?.addEventListener('change', syncAbbrFields);
        vendorSelect?.addEventListener('change', function () {
            syncAbbrFields();
            reindexRows();
        });
        addMoreButton?.addEventListener('click', addRow);

        tableBody?.addEventListener('click', function (event) {
            if (!event.target.classList.contains('remove-row')) return;
            event.preventDefault();
            const row = event.target.closest('tr');
            if (!row || row.id === emptyRowId) return;
            row.remove();
            ensureEmptyState();
            reindexRows();
        });

        ensureEmptyState();
        syncAbbrFields();
        recalculate();
        reindexRows();
    });
</script>
