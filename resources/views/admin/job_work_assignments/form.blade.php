<!--
    =============================
    Job Work Assignment Form CSS
    =============================
    Custom styles for the assignment form layout and table
-->
<style>
    .assignment-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 22px 28px;
    }

    .assignment-field-grid {
        display: grid;
        grid-template-columns: 160px 1fr;
        align-items: center;
        gap: 12px;
        margin-bottom: 14px;
    }

    .assignment-field-grid label {
        margin: 0;
        font-weight: 500;
        color: #111827;
    }

    .assignment-field-grid .form-control,
    .assignment-field-grid .form-select {
        border: 1px solid #2f2f2f;
        border-radius: 0;
        min-height: 38px;
        box-shadow: none;
    }

    .assignment-field-grid textarea.form-control {
        min-height: 74px;
    }

    .assignment-inline-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 18px 24px;
        margin-top: 32px;
    }

    .assignment-bottom-grid {
        margin-top: 26px;
    }

    .assignment-item-table th,
    .assignment-item-table td {
        font-size: 14px;
        vertical-align: middle;
        border: 1px solid #2f2f2f;
    }

    .assignment-item-table thead th {
        background: #f5f5f9;
        color: #111827;
        font-weight: 600;
    }

    .remove-link {
        color: #dc3545;
        font-weight: 600;
        text-decoration: none;
    }

    .assignment-empty-state {
        text-align: center;
        color: #6b7280;
        padding: 18px;
    }

    .d-none {
        display: none !important;
    }

    @media (max-width: 991.98px) {
        .assignment-form-grid,
        .assignment-inline-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 575.98px) {
        .assignment-field-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

@php
    // =============================
    // Data Preparation for Form
    // =============================
    // Set up variables for assignment, job workers, items, lot sources, assignment items, and process options
    $assignment = $assignment ?? [];
    $jobWorkers = $jobWorkers ?? collect();
    $items = $items ?? collect();
    $lotSources = collect($lotSources ?? []);
    $assignmentItems = $assignmentItems ?? collect();
    $processOptions = collect($processOptions ?? []);
    $itemNameMap = $items->pluck('item_name', 'id');
    $processNameById = $processOptions->pluck('name', 'id');
    $processIdByName = $processOptions->pluck('id', 'name');

    // Prepare form rows: use old input if validation failed, otherwise use existing assignment items
    if (old('items_data')) {
        $formRows = collect(old('items_data'))->values()->map(function ($row) use ($itemNameMap, $lotSources, $processIdByName, $processNameById) {
            $source = $lotSources->firstWhere('purchase_item_id', (int) ($row['purchase_item_id'] ?? 0));
            return [
                'purchase_item_id' => $row['purchase_item_id'] ?? '',
                'item_id' => $row['item_id'] ?? '',
                'lot_no' => $row['lot_no'] ?? '',
                'item_name' => $source['item_name'] ?? ($itemNameMap[$row['item_id'] ?? ''] ?? ''),
                'quality' => $row['quality'] ?? '',
                'meter' => $row['meter'] ?? '',
                'fold' => $row['fold'] ?? '',
                'net_meter' => $row['net_meter'] ?? '',
                'process_id' => $row['process_id'] ?? ($processIdByName[$row['process'] ?? ''] ?? ''),
                'process_name' => $row['process_name'] ?? ($row['process'] ?? ''),
                'lr_no' => $row['lr_no'] ?? '',
                'transport' => $row['transport'] ?? '',
                'sort_order' => $row['sort_order'] ?? 1,
            ];
        });
    } else {
        $formRows = $assignmentItems->map(function ($row) use ($processIdByName) {
            return [
                'purchase_item_id' => $row->purchase_item_id,
                'item_id' => $row->item_id,
                'lot_no' => $row->lot_no,
                'item_name' => $row->item?->item_name ?: '',
                'quality' => $row->quality,
                'meter' => $row->meter,
                'fold' => $row->fold,
                'net_meter' => $row->net_meter,
                'process_id' => $processIdByName[$row->process] ?? '',
                'process_name' => $row->process,
                'lr_no' => $row->lr_no,
                'transport' => $row->transport,
                'sort_order' => $row->sort_order,
            ];
        });
    }

    // Get unique lot numbers for the lot dropdown
    $uniqueLots = $lotSources->pluck('lot_no')->filter()->unique()->values();
@endphp


{{--
    =============================
    Error Display Section
    =============================
    Show validation errors if any
--}}
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{--
    =============================
    Assignment Main Fields
    =============================
    Date, Job Worker, Assign No, Freight
--}}
<div class="assignment-form-grid">
    <div>
        <div class="assignment-field-grid">
            <label>Date</label>
            <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', $assignment['date'] ?? '') }}">
            @error('date')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        <div class="assignment-field-grid">
            <label>Job Worker</label>
            <select name="job_worker_id" class="form-select @error('job_worker_id') is-invalid @enderror">
                <option value="">Select Job Worker</option>
                @foreach ($jobWorkers as $jobWorker)
                    <option value="{{ $jobWorker->id }}" {{ (string) old('job_worker_id', $assignment['job_worker_id'] ?? '') === (string) $jobWorker->id ? 'selected' : '' }}>
                        {{ $jobWorker->name }}
                    </option>
                @endforeach
            </select>
            @error('job_worker_id')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div>
        <div class="assignment-field-grid">
            <label>Assign No.</label>
            <input type="text" name="assign_no" class="form-control @error('assign_no') is-invalid @enderror" value="{{ old('assign_no', $assignment['assign_no'] ?? '') }}" readonly>
            @error('assign_no')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        <div class="assignment-field-grid">
            <label>Freight</label>
            <input type="text" name="freight" class="form-control @error('freight') is-invalid @enderror" value="{{ old('freight', $assignment['freight'] ?? '') }}">
            @error('freight')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

{{--
    =============================
    Remark Field
    =============================
--}}
<div class="assignment-form-grid">
    <div class="assignment-field-grid">
        <label>Remark</label>
        <textarea name="remark" class="form-control">{{ old('remark', $assignment['remark'] ?? '') }}</textarea>
    </div>
</div>

{{--
    =============================
    Assignment Item Entry Fields
    =============================
    Fields for adding a new item to the assignment
--}}
<div class="assignment-inline-grid">
    <div class="assignment-field-grid">
        <label>Item</label>
        <select id="purchase_item_id" class="form-select">
            <option value="">Select Item</option>
        </select>
    </div>

    <div class="assignment-field-grid">
        <label>LOT NO.</label>
        <select id="lot_no" class="form-select" disabled>
            <option value="">Select Lot No.</option>
            @foreach ($uniqueLots as $lotNo)
                <option value="{{ $lotNo }}">{{ $lotNo }}</option>
            @endforeach
        </select>
    </div>

    <div class="assignment-field-grid">
        <label>Quality</label>
        <input type="text" id="quality" class="form-control" readonly>
    </div>

    <div class="assignment-field-grid">
        <label>Meter</label>
        <input type="text" id="meter" class="form-control">
    </div>

    <div class="assignment-field-grid">
        <label>Fold</label>
        <input type="text" id="fold" class="form-control">
    </div>

    <div class="assignment-field-grid">
        <label>Net Meter</label>
        <input type="text" id="net_meter" class="form-control">
    </div>
    <div class="assignment-field-grid">
        <label>Process</label>
        <select id="process" class="form-select">
            <option value="">Select Process</option>
            @foreach ($processOptions as $process)
                <option value="{{ $process->item_name }}">{{ $process->item_name }}</option>
            @endforeach
        </select>
    </div>

    <div class="assignment-field-grid">
        <label>LR NO.</label>
        <input type="text" id="lr_no" class="form-control">
    </div>

    <div class="assignment-field-grid">
        <label>Transport</label>
        <input type="text" id="transport" class="form-control">
    </div>
</div>


{{--
    =============================
    Items Data Error
    =============================
--}}
@error('items_data')
<div class="text-danger small mb-2">{{ $message }}</div>
@enderror

{{--
    =============================
    Assignment Items Table
    =============================
    Table of all items added to the assignment
--}}
<div class="assignment-bottom-grid">
    <div class="text-end mb-3">
        <button type="button" id="add_assignment_item" class="btn btn-primary">Add Item</button>
    </div>

    <div class="table-responsive">
        <table class="table assignment-item-table align-middle mb-0">
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>LOT NO.</th>
                    <th>Item</th>
                    <th>Quality</th>
                    <th>Meter</th>
                    <th>Fold</th>
                    <th>Net Meter</th>
                    <th>Process</th>
                    <th>LR NO.</th>
                    <th>Transport</th>
                    <th>Remove</th>
                </tr>
            </thead>
            <tbody id="assignment_items_body">
                @forelse ($formRows as $index => $row)
                    <tr
                        data-purchase-item-id="{{ $row['purchase_item_id'] }}"
                        data-item-id="{{ $row['item_id'] }}"
                        data-lot-no="{{ $row['lot_no'] }}"
                        data-item-name="{{ $row['item_name'] }}"
                        data-quality="{{ $row['quality'] }}"
                        data-meter="{{ $row['meter'] }}"
                        data-fold="{{ $row['fold'] }}"
                        data-net-meter="{{ $row['net_meter'] }}"
                        data-process-id="{{ $row['process_name'] }}"
                        data-process-name="{{ $row['process_name'] }}"
                        data-lr-no="{{ $row['lr_no'] }}"
                        data-transport="{{ $row['transport'] }}"
                    >
                        <td>{{ $index + 1 }}.</td>
                        <td>{{ $row['lot_no'] }}</td>
                        <td>{{ $row['item_name'] }}</td>
                        <td>{{ $row['quality'] }}</td>
                        <td>{{ $row['meter'] }}</td>
                        <td>{{ $row['fold'] }}</td>
                        <td>{{ $row['net_meter'] }}</td>
                        <td>{{ $row['process_name'] }}</td>
                        <td>{{ $row['lr_no'] }}</td>
                        <td>{{ $row['transport'] }}</td>
                        <td><a href="javascript:void(0)" class="remove-link remove-row">Remove</a></td>
                        <td class="d-none row-hidden-inputs">
                            <input type="hidden" name="items_data[{{ $index }}][purchase_item_id]" value="{{ $row['purchase_item_id'] }}">
                            <input type="hidden" name="items_data[{{ $index }}][item_id]" value="{{ $row['item_id'] }}">
                            <input type="hidden" name="items_data[{{ $index }}][lot_no]" value="{{ $row['lot_no'] }}">
                            <input type="hidden" name="items_data[{{ $index }}][quality]" value="{{ $row['quality'] }}">
                            <input type="hidden" name="items_data[{{ $index }}][meter]" value="{{ $row['meter'] }}">
                            <input type="hidden" name="items_data[{{ $index }}][fold]" value="{{ $row['fold'] }}">
                            <input type="hidden" name="items_data[{{ $index }}][net_meter]" value="{{ $row['net_meter'] }}">
                            <input type="hidden" name="items_data[{{ $index }}][process_id]" value="{{ $row['process_name'] }}">
                            <input type="hidden" name="items_data[{{ $index }}][lr_no]" value="{{ $row['lr_no'] }}">
                            <input type="hidden" name="items_data[{{ $index }}][transport]" value="{{ $row['transport'] }}">
                            <input type="hidden" name="items_data[{{ $index }}][sort_order]" value="{{ $row['sort_order'] }}">
                        </td>
                    </tr>
                @empty
                    <tr id="assignment_empty_row">
                        <td colspan="11" class="assignment-empty-state">No items added yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const lotSources = @json($lotSources);
        const processOptions = @json($processOptions);

        const lotSelect = document.getElementById('lot_no');
        const purchaseItemSelect = document.getElementById('purchase_item_id');
        const qualityInput = document.getElementById('quality');
        const meterInput = document.getElementById('meter');
        const foldInput = document.getElementById('fold');
        const netMeterInput = document.getElementById('net_meter');
        const processSelect = document.getElementById('process');
        const lrNoInput = document.getElementById('lr_no');
        const transportInput = document.getElementById('transport');
        const addItemButton = document.getElementById('add_assignment_item');
        const tableBody = document.getElementById('assignment_items_body');
        const emptyRowId = 'assignment_empty_row';

        function getDataRows() {
            return Array.from(tableBody.querySelectorAll('tr')).filter(row => row.id !== emptyRowId);
        }

        function currentSource() {
            const itemId = purchaseItemSelect.value;
            const lotNo = lotSelect.value;
            if (!itemId || !lotNo) return null;

            return lotSources.find(row =>
                String(row.item_id) === String(itemId) &&
                String(row.lot_no) === String(lotNo)
            ) || null;
        }

        // ✅ FIXED (item_name based)
        function populateProcessOptions() {
            const selectedValue = processSelect.value;

            processSelect.innerHTML = '<option value="">Select Process</option>';

            processOptions.forEach(function (row) {
                const option = document.createElement('option');

                option.value = row.item_name;
                option.textContent = row.item_name;

                if (String(row.item_name) === String(selectedValue)) {
                    option.selected = true;
                }

                processSelect.appendChild(option);
            });
        }

        function populateItemOptions() {
            const selectedItemId = purchaseItemSelect.value;
            const uniqueItems = [];
            const seen = new Set();
            purchaseItemSelect.innerHTML = '<option value="">Select Item</option>';

            lotSources.forEach(function (row) {
                const key = String(row.item_id);
                if (!key || seen.has(key)) return;
                seen.add(key);
                uniqueItems.push(row);
            });

            uniqueItems.forEach(function (row) {
                const option = document.createElement('option');
                option.value = row.item_id;
                option.textContent = row.item_name;
                option.selected = String(row.item_id) === String(selectedItemId);
                purchaseItemSelect.appendChild(option);
            });

            populateLotOptions();
            fillSourceFields(currentSource());
            populateProcessOptions();
        }

        function populateLotOptions() {
            const selectedItemId = purchaseItemSelect.value;
            const selectedLotNo = lotSelect.value;

            lotSelect.innerHTML = '<option value="">Select Lot No.</option>';
            lotSelect.disabled = !selectedItemId;

            if (!selectedItemId) {
                lotSelect.value = '';
                return;
            }

            const seenLots = new Set();
            lotSources
                .filter(row => String(row.item_id) === String(selectedItemId))
                .forEach(function (row) {
                    if (!row.lot_no || seenLots.has(String(row.lot_no))) return;
                    seenLots.add(String(row.lot_no));
                    const option = document.createElement('option');
                    option.value = row.lot_no;
                    option.textContent = row.lot_no;
                    option.selected = String(row.lot_no) === String(selectedLotNo);
                    lotSelect.appendChild(option);
                });

            if (!Array.from(lotSelect.options).some(option => option.value === selectedLotNo)) {
                lotSelect.value = '';
            }
        }

        function fillSourceFields(source) {
            qualityInput.value = source ? source.quality : '';
            meterInput.value = source ? source.meter : '';
            foldInput.value = source ? source.fold : '';
            netMeterInput.value = source ? source.net_meter : '';
            lrNoInput.value = '';
            transportInput.value = '';
        }

        // ✅ FIXED hidden input (process_name)
        function updateRowHiddenInputs(row, index) {
            const holder = row.querySelector('.row-hidden-inputs');

            holder.innerHTML = ''
                + `<input type="hidden" name="items_data[${index}][purchase_item_id]" value="${row.dataset.purchaseItemId || ''}">`
                + `<input type="hidden" name="items_data[${index}][item_id]" value="${row.dataset.itemId || ''}">`
                + `<input type="hidden" name="items_data[${index}][lot_no]" value="${row.dataset.lotNo || ''}">`
                + `<input type="hidden" name="items_data[${index}][quality]" value="${row.dataset.quality || ''}">`
                + `<input type="hidden" name="items_data[${index}][meter]" value="${row.dataset.meter || ''}">`
                + `<input type="hidden" name="items_data[${index}][fold]" value="${row.dataset.fold || ''}">`
                + `<input type="hidden" name="items_data[${index}][net_meter]" value="${row.dataset.netMeter || ''}">`
                + `<input type="hidden" name="items_data[${index}][process_id]" value="${row.dataset.processName || ''}">`
                + `<input type="hidden" name="items_data[${index}][lr_no]" value="${row.dataset.lrNo || ''}">`
                + `<input type="hidden" name="items_data[${index}][transport]" value="${row.dataset.transport || ''}">`
                + `<input type="hidden" name="items_data[${index}][sort_order]" value="${index + 1}">`;
        }

        function reindexRows() {
            getDataRows().forEach(function (row, index) {
                row.children[0].textContent = (index + 1) + '.';
                updateRowHiddenInputs(row, index);
            });
        }

        function ensureEmptyState() {
            const rows = getDataRows();
            const emptyRow = document.getElementById(emptyRowId);

            if (rows.length === 0) {
                if (!emptyRow) {
                    const row = document.createElement('tr');
                    row.id = emptyRowId;
                    row.innerHTML = '<td colspan="11" class="assignment-empty-state">No items added yet.</td>';
                    tableBody.appendChild(row);
                }
            } else if (emptyRow) {
                emptyRow.remove();
            }
        }

        function clearEntryFields() {
            populateItemOptions();
            purchaseItemSelect.value = '';
            populateLotOptions();
            lotSelect.value = '';
            processSelect.value = '';
            fillSourceFields(null);
            populateProcessOptions();
        }

        // ✅ FIXED EDIT MODE
        function populateEditorFromRow(row) {
            if (!row) return;

            purchaseItemSelect.value = row.dataset.itemId || '';
            populateLotOptions();
            lotSelect.value = row.dataset.lotNo || '';
            fillSourceFields(currentSource());

            meterInput.value = row.dataset.meter || '';
            foldInput.value = row.dataset.fold || '';
            netMeterInput.value = row.dataset.netMeter || '';
            lrNoInput.value = row.dataset.lrNo || '';
            transportInput.value = row.dataset.transport || '';

            // ✅ MAIN FIX
            processSelect.value = row.dataset.processName || '';
        }

        function addRow() {
            const source = currentSource();
            const processName = processSelect.value;

            if (!purchaseItemSelect.value) return toastr.error('Select item first');
            if (!lotSelect.value) return toastr.error('Select lot no first');
            if (!source) return toastr.error('Invalid item and lot selection');
            if (!processName) return toastr.error('Select process first');

            const duplicateRow = getDataRows().find(row =>
                String(row.dataset.purchaseItemId) === String(source.purchase_item_id)
            );

            if (duplicateRow) {
                toastr.error('This item is already added');
                return;
            }

            const row = document.createElement('tr');

            row.dataset.purchaseItemId = source.purchase_item_id;
            row.dataset.itemId = source.item_id;
            row.dataset.lotNo = source.lot_no;
            row.dataset.itemName = source.item_name;
            row.dataset.quality = source.quality || '';
            row.dataset.meter = meterInput.value || '';
            row.dataset.fold = foldInput.value || '';
            row.dataset.netMeter = netMeterInput.value || '';
            row.dataset.processName = processName;
            row.dataset.lrNo = lrNoInput.value || '';
            row.dataset.transport = transportInput.value || '';

            row.innerHTML = `
                <td></td>
                <td>${source.lot_no || ''}</td>
                <td>${source.item_name || ''}</td>
                <td>${source.quality || ''}</td>
                <td>${meterInput.value || ''}</td>
                <td>${foldInput.value || ''}</td>
                <td>${netMeterInput.value || ''}</td>
                <td>${processName}</td>
                <td>${lrNoInput.value || ''}</td>
                <td>${transportInput.value || ''}</td>
                <td><a href="javascript:void(0)" class="remove-link remove-row">Remove</a></td>
                <td class="d-none row-hidden-inputs"></td>
            `;

            tableBody.appendChild(row);
            ensureEmptyState();
            reindexRows();
            clearEntryFields();
        }

        purchaseItemSelect.addEventListener('change', function () {
            populateLotOptions();
            lotSelect.value = '';
            fillSourceFields(currentSource());
            populateProcessOptions();
        });
        lotSelect.addEventListener('change', function () {
            fillSourceFields(currentSource());
            populateProcessOptions();
        });

        addItemButton.addEventListener('click', addRow);

        tableBody.addEventListener('click', function (e) {
            if (!e.target.classList.contains('remove-row')) return;

            e.target.closest('tr').remove();
            ensureEmptyState();
            reindexRows();
        });

        ensureEmptyState();
        reindexRows();
        populateProcessOptions();
        populateItemOptions();

        const existingRows = getDataRows();
        if (existingRows.length > 0) {
            populateEditorFromRow(existingRows[0]);
        }
    });
</script>


