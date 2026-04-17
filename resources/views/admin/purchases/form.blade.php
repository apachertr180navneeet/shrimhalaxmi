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

    /* =========================
    FOCUS HIGHLIGHT EFFECT
    ========================= */
    .form-control:focus,
    .form-select:focus {
        border: 2px solid #007bff !important;
        outline: none;
        box-shadow: 0 0 6px rgba(0, 123, 255, 0.5);
        background-color: #f0f8ff;
        transform: scale(1.02);
        transition: all 0.2s ease;
    }

    /* Active field class (extra strong highlight) */
    .active-field {
        border: 2px solid #28a745 !important;
        background-color: #eaffea !important;
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
    $lotNo = old(
        'lot_no',
        $purchase['lot_no'] ?? ($vendorAbbr ?: $defaultVendorAbbr) . ' / ' . ($pchNo ?: '0001') . ' / 0001',
    );
@endphp

<input type="hidden" name="vendor_abbr" id="vendor_abbr" value="{{ $vendorAbbr }}">
<input type="hidden" name="item_abbr" id="item_abbr" value="{{ $itemAbbr }}">
<input type="hidden" name="amount" id="amount" value="{{ old('amount', $purchase['amount'] ?? '') }}">

<div class="purchase-form-grid">
    <div>
        <div class="purchase-field-grid">
            <label>Date</label>
            <input type="date" name="date" class="form-control @error('date') is-invalid @enderror"
                value="{{ old('date', $purchase['date'] ?? '') }}">
        </div>

        <div class="purchase-field-grid">
            <label>BNO</label>
            <input type="text" name="bno" class="form-control @error('bno') is-invalid @enderror"
                value="{{ old('bno', $purchase['bno'] ?? '') }}">
        </div>
    </div>

    <div>
        <div class="purchase-field-grid">
            <label>PCH. NO.</label>
            <input type="text" name="pch_no" id="pch_no"
                class="form-control @error('pch_no') is-invalid @enderror" value="{{ $pchNo }}" readonly>
        </div>

        <div class="purchase-field-grid">
            <label>Vendor Name</label>
            <select name="vendor_id" id="vendor_id" class="form-select @error('vendor_id') is-invalid @enderror">
                <option value="">Select Vendor</option>
                @foreach ($vendors as $vendor)
                    <option value="{{ $vendor->id }}" data-abbr="{{ $vendor->abbr ?: $defaultVendorAbbr }}"
                        {{ (string) old('vendor_id', $purchase['vendor_id'] ?? '') === (string) $vendor->id ? 'selected' : '' }}>
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
                <option value="Paid" {{ old('freight', $purchase['freight'] ?? '') === 'Paid' ? 'selected' : '' }}>
                    Paid</option>
                <option value="To be Paid"
                    {{ old('freight', $purchase['freight'] ?? '') === 'To be Paid' ? 'selected' : '' }}>To be Paid
                </option>
                <option value="To be Shiped"
                    {{ old('freight', $purchase['freight'] ?? '') === 'To be Shiped' ? 'selected' : '' }}>To be Shiped
                </option>
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
        <label>Item/Quality</label>
        <select id="item_id" class="form-select">
            <option value="">Select Item</option>
            @foreach ($items as $item)
                <option value="{{ $item->id }}" data-abbr="{{ $item->abbr ?: $defaultItemAbbr }}"
                    data-name="{{ $item->item_name }}">
                    {{ $item->item_name }}{{ $item->abbr ? ' (' . $item->abbr . ')' : '' }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="purchase-field-grid">
        <label>Stage</label>
        <select id="color" class="form-select select2-stage" data-placeholder="Select Stage">
            <option value="">Select Stage</option>
            @php $selectedStage = old('color', $purchase['color'] ?? ''); @endphp
            <option value="Grey" {{ $selectedStage === 'Grey' ? 'selected' : '' }}>Grey</option>
            <option value="Bleach" {{ $selectedStage === 'Bleach' ? 'selected' : '' }}>Bleach</option>
            <option value="Dyed" {{ $selectedStage === 'Dyed' ? 'selected' : '' }}>Dyed</option>
            <option value="RFD" {{ $selectedStage === 'RFD' ? 'selected' : '' }}>RFD</option>
            <option value="Tie-Dye" {{ $selectedStage === 'Tie-Dye' ? 'selected' : '' }}>Tie-Dye</option>
        </select>
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
        <input type="text" id="transport" class="form-control"
            value="{{ old('transport', $purchase['transport'] ?? '') }}">
    </div>

    <div class="purchase-field-grid">
        <label>LR NO.</label>
        <input type="text" id="lr_no" class="form-control" value="{{ old('lr_no', $purchase['lr_no'] ?? '') }}">
    </div>

    <div class="purchase-field-grid">
        <label>Net Meter</label>
        <input type="text" id="net_meter" class="form-control"
            value="{{ old('net_meter', $purchase['net_meter'] ?? '') }}" readonly>
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
                    <th>Stage</th>
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
                    <tr data-index="{{ $index }}" data-item-id="{{ $row->item_id }}"
                        data-color="{{ $row->color }}" data-qty="{{ $row->qty_m }}"
                        data-fold="{{ $row->fold }}" data-rate="{{ $row->rate }}"
                        data-transport="{{ $row->transport }}" data-lr-no="{{ $row->lr_no }}"
                        data-net-meter="{{ $row->net_meter }}" data-amount="{{ $row->amount }}">
                        <td>{{ $index + 1 }}.</td>
                        <td>{{ $row->lot_no }}</td>
                        <td>{{ $row->item?->item_name }}</td>
                        <td>{{ $row->color }}</td>
                        <td>{{ rtrim(rtrim(number_format((float) $row->rate, 2, '.', ''), '0'), '.') }}</td>
                        <td>{{ rtrim(rtrim(number_format((float) $row->qty_m, 2, '.', ''), '0'), '.') }}</td>
                        <td>{{ rtrim(rtrim(number_format((float) $row->fold, 2, '.', ''), '0'), '.') }}</td>
                        <td>{{ $row->transport }}</td>
                        <td>{{ $row->lr_no }}</td>
                        <td>{{ number_format((float) $row->net_meter, 2, '.', '') }}</td>
                        <td>{{ number_format((float) $row->amount, 2, '.', '') }}</td>
                        <td><a href="javascript:void(0)" class="remove-link remove-row">Remove</a></td>
                        <td class="d-none row-hidden-inputs">
                            <input type="hidden" name="items_data[{{ $index }}][item_id]"
                                value="{{ $row->item_id }}">
                            <input type="hidden" name="items_data[{{ $index }}][lot_no]"
                                value="{{ $row->lot_no }}">
                            <input type="hidden" name="items_data[{{ $index }}][item_code]"
                                value="{{ $row->item_code ?: $row->lot_no }}">
                            <input type="hidden" name="items_data[{{ $index }}][color]"
                                value="{{ $row->color }}">
                            <input type="hidden" name="items_data[{{ $index }}][qty_m]"
                                value="{{ $row->qty_m }}">
                            <input type="hidden" name="items_data[{{ $index }}][fold]"
                                value="{{ $row->fold }}">
                            <input type="hidden" name="items_data[{{ $index }}][rate]"
                                value="{{ $row->rate }}">
                            <input type="hidden" name="items_data[{{ $index }}][transport]"
                                value="{{ $row->transport }}">
                            <input type="hidden" name="items_data[{{ $index }}][lr_no]"
                                value="{{ $row->lr_no }}">
                            <input type="hidden" name="items_data[{{ $index }}][net_meter]"
                                value="{{ $row->net_meter }}">
                            <input type="hidden" name="items_data[{{ $index }}][amount]"
                                value="{{ $row->amount }}">
                            <input type="hidden" name="items_data[{{ $index }}][sort_order]"
                                value="{{ $row->sort_order }}">
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
    document.addEventListener('DOMContentLoaded', () => {

        const $ = id => document.getElementById(id);
        const q = sel => document.querySelector(sel);

        const item = $('item_id');
        const vendor = $('vendor_id');
        const table = $('purchase_items_body');
        const addBtn = $('add_more_item');

        const i = {
            vendorAbbr: $('vendor_abbr'),
            itemAbbr: $('item_abbr'),
            pch: $('pch_no'),
            lot: $('lot_no'),
            bno: q('[name="bno"]'),
            qty: $('qty_m'),
            fold: $('fold'),
            rate: $('rate'),
            transport: $('transport'),
            lr: $('lr_no'),
            net: $('net_meter'),
            amt: $('amount'),
            color: $('color')
        };

        const defaults = {
            item: '{{ $defaultItemAbbr }}',
            vendor: '{{ $defaultVendorAbbr }}'
        };

        /* =========================
           EMPTY STATE
        ========================= */
        function ensureEmptyState() {
            const existingRows = rows();
            const emptyRow = document.getElementById('purchase_empty_row');

            if (existingRows.length === 0) {
                if (!emptyRow) {
                    const tr = document.createElement('tr');
                    tr.id = 'purchase_empty_row';
                    tr.innerHTML = `<td colspan="12" class="text-center">No items added yet.</td>`;
                    table.appendChild(tr);
                }
            } else {
                emptyRow?.remove();
            }
        }

        /* =========================
           HELPERS
        ========================= */
        const num = v => parseFloat(v) || 0;
        const fix = v => (Math.round(v * 100) / 100).toFixed(2);

        const opt = el => el?.options[el.selectedIndex];
        const abbr = (el, d) => opt(el)?.dataset.abbr || d;
        const name = el => opt(el)?.dataset.name || '';

        const rows = () => [...table.querySelectorAll('tr')].filter(r => r.id !== 'purchase_empty_row');

        const rowData = r => {
            const d = r.dataset;
            return {
                item_id: d.itemId || d.item_id || '',
                lot_no: d.lotNo || d.lot_no || '',
                item_code: d.itemCode || d.item_code || d.lotNo || d.lot_no || '',
                color: d.color || '',
                qty_m: d.qtyM || d.qty_m || d.qty || '',
                fold: d.fold || '',
                rate: d.rate || '',
                transport: d.transport || '',
                lr_no: d.lrNo || d.lr_no || '',
                net_meter: d.netMeter || d.net_meter || '',
                amount: d.amount || '',
                sort_order: d.sortOrder || d.sort_order || ''
            };
        };

        const setRowData = (r, data) => {
            r.dataset.itemId = data.item_id ?? '';
            r.dataset.lotNo = data.lot_no ?? '';
            r.dataset.itemCode = data.item_code ?? '';
            r.dataset.color = data.color ?? '';
            r.dataset.qtyM = data.qty_m ?? '';
            r.dataset.fold = data.fold ?? '';
            r.dataset.rate = data.rate ?? '';
            r.dataset.transport = data.transport ?? '';
            r.dataset.lrNo = data.lr_no ?? '';
            r.dataset.netMeter = data.net_meter ?? '';
            r.dataset.amount = data.amount ?? '';
            r.dataset.sortOrder = data.sort_order ?? '';
        };

        /* =========================
           LOT NUMBER
        ========================= */
        const lotNo = s =>
            `${abbr(vendor, defaults.vendor).toLowerCase()}/${
            String(i.bno?.value || 0).padStart(4,'0')
        }/${String(i.pch.value || 1).padStart(4,'0')}/${
            String(s).padStart(4,'0')
        }`;

        const updatePreview = () => {
            i.lot.value = lotNo(rows().length + 1);
        };

        /* =========================
           CALCULATION
        ========================= */
        const calc = () => {
            const net = (num(i.qty.value) * num(i.fold.value)) / 100;
            i.net.value = net ? fix(net) : '';
            i.amt.value = (net && i.rate.value) ? fix(net * num(i.rate.value)) : '';
        };

        /* =========================
           SYNC
        ========================= */
        const sync = () => {
            i.vendorAbbr.value = abbr(vendor, defaults.vendor);
            i.itemAbbr.value = abbr(item, defaults.item);
        };

        /* =========================
           BUILD DATA
        ========================= */
        const buildData = (lot, s) => ({
            item_id: item.value,
            lot_no: lot,
            item_code: lot,
            color: i.color.value || '',
            qty_m: i.qty.value,
            fold: i.fold.value,
            rate: i.rate.value,
            transport: i.transport.value || '',
            lr_no: i.lr.value || '',
            net_meter: i.net.value || '0.00',
            amount: i.amt.value || '0.00',
            sort_order: s
        });

        /* =========================
           HIDDEN INPUTS
        ========================= */
        const hidden = (idx, data) =>
            Object.entries(data).map(([k, v]) =>
                `<input type="hidden" name="items_data[${idx}][${k}]" value="${v}">`
            ).join('');

        /* =========================
           REINDEX
        ========================= */
        const reindex = () => {
            rows().forEach((r, iIdx) => {
                const s = iIdx + 1;
                const lot = lotNo(s);
                const data = rowData(r);

                data.lot_no = lot;
                data.item_code = lot;
                data.sort_order = s;

                r.children[0].textContent = s + '.';
                r.children[1].textContent = lot;

                setRowData(r, data);

                r.querySelector('.row-hidden-inputs').innerHTML =
                    hidden(iIdx, data);
            });

            updatePreview();
            ensureEmptyState();
        };

        /* =========================
           CLEAR
        ========================= */
        const clearInputs = () => {
            ['qty', 'fold', 'rate', 'transport', 'lr', 'net', 'amt', 'color']
            .forEach(k => i[k] && (i[k].value = ''));
            item.value = '';
        };

        /* =========================
           ADD ROW
        ========================= */
        const addRow = () => {

            if (!item.value) return toastr.error('Select item');

            // OPTIONAL: require color
            if (!i.color.value.trim()) {
                toastr.error('Select stage');
                return;
            }

            if (!i.qty.value || !i.fold.value || !i.rate.value)
                return toastr.error('Enter qty, fold, rate');

            ensureEmptyState();

            const s = rows().length + 1;
            const lot = lotNo(s);
            const data = buildData(lot, s);

            const r = document.createElement('tr');
            setRowData(r, data);

            r.innerHTML = `
            <td>${s}.</td>
            <td>${lot}</td>
            <td>${name(item)}</td>
            <td>${data.color}</td>
            <td>${data.rate}</td>
            <td>${data.qty_m}</td>
            <td>${data.fold}</td>
            <td>${data.transport}</td>
            <td>${data.lr_no}</td>
            <td>${data.net_meter}</td>
            <td>${data.amount}</td>
            <td><a href="#" class="remove-row">Remove</a></td>
            <td class="d-none row-hidden-inputs">${hidden(s-1,data)}</td>
        `;

            table.appendChild(r);

            clearInputs();
            sync();
            reindex();
        };

        /* =========================
           EVENTS
        ========================= */

        ['qty', 'fold', 'rate'].forEach(k =>
            i[k]?.addEventListener('input', calc)
        );

        item?.addEventListener('change', sync);

        vendor?.addEventListener('change', function() {
            sync();
            reindex();

            const vendorHidden = $('vendor_id_hidden');
            if (this.value && vendorHidden) {
                vendorHidden.value = this.value;
                this.disabled = true;
            }
        });

        addBtn?.addEventListener('click', addRow);

        table?.addEventListener('click', e => {
            if (!e.target.classList.contains('remove-row')) return;
            e.preventDefault();

            e.target.closest('tr').remove();
            reindex();
        });

        i.bno?.addEventListener('change', function() {
            if (this.value.trim()) this.readOnly = true;
            updatePreview();
            reindex();
        });

        /* =========================
           INIT
        ========================= */
        sync();
        calc();
        reindex();
        ensureEmptyState();

        if (window.jQuery && window.jQuery.fn && window.jQuery.fn.select2) {
            window.jQuery('#color').select2({
                placeholder: 'Select Stage',
                allowClear: true,
                width: '100%'
            });
        }

        /* =========================
        FOCUS HIGHLIGHT (TAB SUPPORT)
        ========================= */
        document.querySelectorAll('.form-control, .form-select').forEach(el => {

            el.addEventListener('focus', function() {

                // Remove previous highlight
                document.querySelectorAll('.active-field').forEach(e => {
                    e.classList.remove('active-field');
                });

                // Add highlight to current field
                this.classList.add('active-field');
            });

        });

        /* =========================
        ENTER KEY = NEXT FIELD
        ========================= */
        const fields = document.querySelectorAll('.form-control, .form-select');

        fields.forEach((field, index) => {
            field.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();

                    let next = fields[index + 1];
                    if (next) {
                        next.focus();
                    }
                }
            });
        });

    });
</script>
