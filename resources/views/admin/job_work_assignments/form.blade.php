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
        $formRows = collect(old('items_data'))
            ->values()
            ->map(function ($row) use ($itemNameMap, $lotSources, $processIdByName, $processNameById) {
                $source = $lotSources->firstWhere('purchase_item_id', (int) ($row['purchase_item_id'] ?? 0));
                $rawProcess = $row['process_id'] ?? ($row['process'] ?? '');
                $processId = is_numeric((string) $rawProcess) ? (int) $rawProcess : ($processIdByName[$rawProcess] ?? '');
                return [
                    'purchase_item_id' => $row['purchase_item_id'] ?? '',
                    'item_id' => $row['item_id'] ?? '',
                    'lot_no' => $row['lot_no'] ?? '',
                    'item_name' => $source['item_name'] ?? ($itemNameMap[$row['item_id'] ?? ''] ?? ''),
                    'colour' => $row['color'] ?? '',
                    'meter' => $row['meter'] ?? '',
                    'fold' => $row['fold'] ?? '',
                    'net_meter' => $row['net_meter'] ?? '',
                    'process_id' => $processId,
                    'process_name' => $row['process_name'] ?? ($itemNameMap[$processId] ?? ($processNameById[$processId] ?? ($row['process'] ?? ''))),
                    'lr_no' => $row['lr_no'] ?? '',
                    'transport' => $row['transport'] ?? '',
                    'sort_order' => $row['sort_order'] ?? 1,
                ];
            });
    } else {
        $formRows = $assignmentItems->map(function ($row) use ($itemNameMap) {
            return [
                'purchase_item_id' => $row->purchase_item_id,
                'item_id' => $row->item_id,
                'lot_no' => $row->lot_no,
                'item_name' => $row->item?->item_name ?: '',
                'colour' => $row->colour,
                'meter' => $row->meter,
                'fold' => $row->fold,
                'net_meter' => $row->net_meter,
                'process_id' => $row->process,
                'process_name' => $itemNameMap[$row->process] ?? $row->process,
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
            <input type="date" name="date" class="form-control @error('date') is-invalid @enderror"
                value="{{ old('date', $assignment['date'] ?? '') }}">
            @error('date')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        <div class="assignment-field-grid">
            <label>Job Worker</label>
            <select name="job_worker_id" class="form-select @error('job_worker_id') is-invalid @enderror">
                <option value="">Select Job Worker</option>
                @foreach ($jobWorkers as $jobWorker)
                    <option value="{{ $jobWorker->id }}" data-abbr="{{ $jobWorker->abbr }}"
                        {{ (string) old('job_worker_id', $assignment['job_worker_id'] ?? '') === (string) $jobWorker->id ? 'selected' : '' }}>
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
            <input type="text" name="assign_no" class="form-control @error('assign_no') is-invalid @enderror"
                value="{{ old('assign_no', $assignment['assign_no'] ?? '') }}" readonly>
            @error('assign_no')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        <div class="assignment-field-grid">
            <label>Freight</label>
            <select name="freight" class="form-select">
                <option value="">Select Freight</option>
                <option value="Paid" {{ old('freight', $assignment['freight'] ?? '') === 'Paid' ? 'selected' : '' }}>
                    Paid</option>
                <option value="To be Paid"
                    {{ old('freight', $assignment['freight'] ?? '') === 'To be Paid' ? 'selected' : '' }}>To be Paid
                </option>
                <option value="To be Shiped"
                    {{ old('freight', $assignment['freight'] ?? '') === 'To be Shiped' ? 'selected' : '' }}>To be
                    Shiped</option>
            </select>
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
        <label>Item/Quality</label>
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
        <label>Colour</label>
        <input type="text" id="colour" class="form-control" readonly>
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
            @foreach ($items as $item)
                <option value="{{ $item->id }}" data-name="{{ $item->item_name }}">{{ $item->item_name }}</option>
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
                    <th>Colour</th>
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
                    <tr data-purchase-item-id="{{ $row['purchase_item_id'] }}" data-item-id="{{ $row['item_id'] }}"
                        data-lot-no="{{ $row['lot_no'] }}" data-item-name="{{ $row['item_name'] }}"
                        data-colour="{{ $row['colour'] }}" data-meter="{{ $row['meter'] }}"
                        data-fold="{{ $row['fold'] }}" data-net-meter="{{ $row['net_meter'] }}"
                        data-process-id="{{ $row['process_id'] }}" data-process-name="{{ $row['process_name'] }}"
                        data-lr-no="{{ $row['lr_no'] }}" data-transport="{{ $row['transport'] }}">
                        <td>{{ $index + 1 }}.</td>
                        <td>{{ $row['lot_no'] }}</td>
                        <td>{{ $row['item_name'] }}</td>
                        <td>{{ $row['colour'] }}</td>
                        <td>{{ $row['meter'] }}</td>
                        <td>{{ $row['fold'] }}</td>
                        <td>{{ $row['net_meter'] }}</td>
                        <td>{{ $row['process_name'] }}</td>
                        <td>{{ $row['lr_no'] }}</td>
                        <td>{{ $row['transport'] }}</td>
                        <td><a href="javascript:void(0)" class="remove-link remove-row">Remove</a></td>
                        <td class="d-none row-hidden-inputs">
                            <input type="hidden" name="items_data[{{ $index }}][purchase_item_id]"
                                value="{{ $row['purchase_item_id'] }}">
                            <input type="hidden" name="items_data[{{ $index }}][item_id]"
                                value="{{ $row['item_id'] }}">
                            <input type="hidden" name="items_data[{{ $index }}][lot_no]"
                                value="{{ $row['lot_no'] }}">
                            <input type="hidden" name="items_data[{{ $index }}][colour]"
                                value="{{ $row['colour'] }}">
                            <input type="hidden" name="items_data[{{ $index }}][meter]"
                                value="{{ $row['meter'] }}">
                            <input type="hidden" name="items_data[{{ $index }}][fold]"
                                value="{{ $row['fold'] }}">
                            <input type="hidden" name="items_data[{{ $index }}][net_meter]"
                                value="{{ $row['net_meter'] }}">
                            <input type="hidden" name="items_data[{{ $index }}][process]"
                                value="{{ $row['process_id'] }}">
                            <input type="hidden" name="items_data[{{ $index }}][lr_no]"
                                value="{{ $row['lr_no'] }}">
                            <input type="hidden" name="items_data[{{ $index }}][transport]"
                                value="{{ $row['transport'] }}">
                            <input type="hidden" name="items_data[{{ $index }}][sort_order]"
                                value="{{ $row['sort_order'] }}">
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
    document.addEventListener('DOMContentLoaded', function() {

        // =============================
        // DATA
        // =============================
        const lotSources = @json($lotSources);

        // =============================
        // INPUTS
        // =============================
        const lotSelect = document.getElementById('lot_no');
        const purchaseItemSelect = document.getElementById('purchase_item_id');

        const colourInput = document.getElementById('colour');
        const meterInput = document.getElementById('meter');
        const foldInput = document.getElementById('fold');
        const netMeterInput = document.getElementById('net_meter');

        const processSelect = document.getElementById('process');
        const lrNoInput = document.getElementById('lr_no');
        const transportInput = document.getElementById('transport');

        const addItemButton = document.getElementById('add_assignment_item');
        const tableBody = document.getElementById('assignment_items_body');

        const jobWorkerSelect = document.querySelector('[name="job_worker_id"]');
        const assignNoInput = document.querySelector('[name="assign_no"]');

        // =============================
        // ✅ START INDEX FROM EXISTING ROWS
        // =============================
        let rowIndex = document.querySelectorAll('#assignment_items_body tr').length;

        // =============================
        // LOT COUNTER
        // =============================
        let lotCounter = 1;

        function generateLotNumber() {
            const selectedOption = jobWorkerSelect.options[jobWorkerSelect.selectedIndex];
            const jobAbbr = selectedOption?.getAttribute('data-abbr') || 'JW';
            const assignNo = assignNoInput.value || '0000';

            const serial = String(lotCounter).padStart(5, '0');
            lotCounter++;

            return `${jobAbbr}/${assignNo}/${serial}`;
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

        function fillSourceFields(source) {
            if (!source) {
                colourInput.value = '';
                meterInput.value = '';
                foldInput.value = '';
                netMeterInput.value = '';
                lrNoInput.value = '';
                transportInput.value = '';
                return;
            }

            colourInput.value = source.colour || source.color || '';
            meterInput.value = source.meter || '';
            foldInput.value = source.fold || '';
            netMeterInput.value = source.net_meter || '';
            lrNoInput.value = source.lr_no || '';
            transportInput.value = source.transport || '';
        }

        function populateItemOptions() {
            const seen = new Set();
            purchaseItemSelect.innerHTML = '<option value="">Select Item</option>';

            lotSources.forEach(row => {
                if (!row.item_id || seen.has(row.item_id)) return;

                seen.add(row.item_id);

                const option = document.createElement('option');
                option.value = row.item_id;
                option.textContent = row.item_name;

                purchaseItemSelect.appendChild(option);
            });
        }

        function populateLotOptions() {
            const itemId = purchaseItemSelect.value;

            lotSelect.innerHTML = '<option value="">Select Lot</option>';
            lotSelect.disabled = !itemId;

            if (!itemId) return;

            const seen = new Set();

            lotSources
                .filter(row => String(row.item_id) === String(itemId))
                .forEach(row => {
                    if (!row.lot_no || seen.has(row.lot_no)) return;

                    seen.add(row.lot_no);

                    const option = document.createElement('option');
                    option.value = row.lot_no;
                    option.textContent = row.lot_no;

                    lotSelect.appendChild(option);
                });
        }

        // =============================
        // ADD ROW
        // =============================
        function addRow() {

            const emptyRow = document.getElementById('assignment_empty_row');
            if (emptyRow) emptyRow.remove();

            const source = currentSource();
            const processName = processSelect.value;

            let processNamedata = $('#process option:selected').data('name');
    
            if (!jobWorkerSelect.value) return toastr.error('Select Job Worker first');
            if (!assignNoInput.value) return toastr.error('Assign No missing');
            if (!purchaseItemSelect.value) return toastr.error('Select item first');
            if (!lotSelect.value) return toastr.error('Select lot first');
            if (!source) return toastr.error('Invalid selection');
            if (!processName) return toastr.error('Select process');

            const index = rowIndex++;
            const newLotNo = generateLotNumber();

            const row = document.createElement('tr');

            row.innerHTML = `
            <td></td>
            <td>${newLotNo}</td>
            <td>${source.item_name}</td>
            <td>${source.color || ''}</td>
            <td>${meterInput.value}</td>
            <td>${foldInput.value}</td>
            <td>${netMeterInput.value}</td>
            <td>${processNamedata}</td>
            <td>${lrNoInput.value}</td>
            <td>${transportInput.value}</td>
            <td><a href="#" class="remove-row">Remove</a></td>
            <td class="d-none">
                <input type="hidden" name="items_data[${index}][id]" value="">
                <input type="hidden" name="items_data[${index}][purchase_item_id]" value="${source.purchase_item_id}">
                <input type="hidden" name="items_data[${index}][item_id]" value="${source.item_id}">
                <input type="hidden" name="items_data[${index}][lot_no]" value="${newLotNo}">
                <input type="hidden" name="items_data[${index}][colour]" value="${source.color || ''}">
                <input type="hidden" name="items_data[${index}][meter]" value="${meterInput.value}">
                <input type="hidden" name="items_data[${index}][fold]" value="${foldInput.value}">
                <input type="hidden" name="items_data[${index}][net_meter]" value="${netMeterInput.value}">
                <input type="hidden" name="items_data[${index}][process]" value="${processName}">
                <input type="hidden" name="items_data[${index}][lr_no]" value="${lrNoInput.value}">
                <input type="hidden" name="items_data[${index}][transport]" value="${transportInput.value}">
                <input type="hidden" name="items_data[${index}][sort_order]" value="${index + 1}">
            </td>
        `;

            tableBody.appendChild(row);
            reindexRows();
            clearFields();
        }

        function reindexRows() {
            const rows = tableBody.querySelectorAll('tr');
            rows.forEach((row, index) => {
                row.children[0].textContent = (index + 1) + '.';
            });
        }

        function clearFields() {
            purchaseItemSelect.value = '';
            lotSelect.innerHTML = '<option value="">Select Lot</option>';
            lotSelect.disabled = true;

            fillSourceFields(null);
            processSelect.value = '';
        }

        purchaseItemSelect.addEventListener('change', function() {
            populateLotOptions();
            fillSourceFields(null);
        });

        lotSelect.addEventListener('change', function() {
            fillSourceFields(currentSource());
        });

        addItemButton.addEventListener('click', addRow);

        tableBody.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-row')) {
                e.preventDefault();
                e.target.closest('tr').remove();
                reindexRows();
            }
        });

        jobWorkerSelect.addEventListener('change', function() {
            lotCounter = 1;
        });

        populateItemOptions();

    });
</script>



