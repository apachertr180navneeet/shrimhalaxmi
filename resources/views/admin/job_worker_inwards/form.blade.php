@php
    $inward = $inward ?? [];
    $jobWorkers = $jobWorkers ?? collect();
    $items = $items ?? collect();
    $lotSources = $lotSources ?? collect();
    $itemRows = $itemRows ?? collect();
@endphp

<div class="p-4 mb-4">

    <div class="row mb-3">
        <div class="col-md-3">
            <label>Date</label>
            <input type="date" name="inward_date" class="form-control"
                   value="{{ old('inward_date', $inward['inward_date'] ?? '') }}">
        </div>

        <div class="col-md-3">
            <label>JICH NO</label>
            <input type="text" name="ch_no" class="form-control"
                   value="{{ old('ch_no', $inward['ch_no'] ?? '') }}" readonly>
        </div>

        <div class="col-md-3">
            <label>Job Worker</label>
            <select name="job_worker_id" class="form-control">
                <option value="">Select</option>
                @foreach ($jobWorkers as $worker)
                    <option value="{{ $worker->id }}" data-abbr="{{ $worker->abbr }}"
                        {{ (string) old('job_worker_id', $inward['job_worker_id'] ?? '') === (string) $worker->id ? 'selected' : '' }}>
                        {{ $worker->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<!-- ITEM ENTRY SECTION -->
<div class="p-4 mb-4">
    <h6 class="mb-3">Items Details</h6>

    <div class="row g-3 align-items-end">

        <div class="col-md-2">
            <label>Item</label>
            <select id="item_id" class="form-control">
                <option value="">Select</option>
            </select>
        </div>

        <div class="col-md-2">
            <label>LOT NO</label>
            <select id="lot_no" class="form-control" disabled>
                <option value="">Select</option>
            </select>
        </div>

        <div class="col-md-2">
            <label>Type</label>
            <select id="type" class="form-control">
                <option value="LOT TO LOT">LOT TO LOT</option>
                <option value="LOT TO PART">LOT TO PART</option>
            </select>
        </div>

        <div class="col-md-2">
            <label>Color</label>
            <input type="text" id="quality" class="form-control">
        </div>

        <div class="col-md-2">
            <label>Meter</label>
            <input type="number" id="meter" class="form-control">
        </div>

        <div class="col-md-2">
            <label>Fold</label>
            <input type="number" id="fold" class="form-control">
        </div>

        <div class="col-md-2">
            <label>Total Meter</label>
            <input type="number" id="total_meter" class="form-control">
        </div>

        <div class="col-md-2">
            <label>Shrinkage</label>
            <input type="text" id="shrinkage" class="form-control">
        </div>

        <div class="col-md-2">
            <label>After Shrinkage</label>
            <input type="number" id="after_shrinkage_meter" class="form-control">
        </div>

        <div class="col-md-2">
            <button type="button" id="addItem" class="btn btn-primary w-100">
                Add Item
            </button>
        </div>
    </div>
</div>

<!-- TABLE -->
<div class="p-3">
    <table class="table table-bordered text-center" id="itemTable">
        <thead class="table-light">
        <tr>
            <th>SR</th>
            <th>LOT NO</th>
            <th>Item</th>
            <th>Color</th>
            <th>Meter</th>
            <th>Fold</th>
            <th>Total</th>
            <th>Shrinkage</th>
            <th>After Shrinkage</th>
            <th>Type</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody id="itemTableBody">
        @if($itemRows->isEmpty())
            <tr id="no_item_row">
                <td colspan="11" class="text-center">No items added yet.</td>
            </tr>
        @else
            @foreach($itemRows as $rowIndex => $row)
                @php
                    $rowShrinkage = (float) ($row->shrinkage ?? 0);
                    $rowMeter = (float) ($row->meter ?? 0);
                    $rowAfterShrinkage = $rowMeter - (($rowMeter * $rowShrinkage) / 100);
                @endphp
                <tr data-item-id="{{ $row->item_id }}" data-lot-no="{{ $row->lot_no }}" data-quality="{{ $row->quality }}" data-meter="{{ $row->meter }}" data-fold="{{ $row->fold }}" data-total="{{ $row->total_meter }}" data-shrinkage="{{ $row->shrinkage }}" data-after-shrinkage="{{ number_format($rowAfterShrinkage, 2, '.', '') }}" data-type="{{ $row->type }}">
                    <td>{{ $rowIndex + 1 }}</td>
                    <td>{{ $row->lot_no }}</td>
                    <td>{{ $row->item?->item_name }}</td>
                    <td>{{ $row->quality }}</td>
                    <td>{{ $row->meter }}</td>
                    <td>{{ $row->fold }}</td>
                    <td>{{ $row->total_meter }}</td>
                    <td>{{ $row->shrinkage }}</td>
                    <td>{{ number_format($rowAfterShrinkage, 2, '.', '') }}</td>
                    <td>{{ $row->type }}</td>
                    <td><button class="btn btn-danger btn-sm removeRow" type="button">Delete</button></td>
                    <td class="d-none row-hidden-inputs">
                        <input type="hidden" name="items_data[{{ $rowIndex }}][item_id]" value="{{ $row->item_id }}">
                        <input type="hidden" name="items_data[{{ $rowIndex }}][lot_no]" value="{{ $row->lot_no }}">
                        <input type="hidden" name="items_data[{{ $rowIndex }}][quality]" value="{{ $row->quality }}">
                        <input type="hidden" name="items_data[{{ $rowIndex }}][meter]" value="{{ $row->meter }}">
                        <input type="hidden" name="items_data[{{ $rowIndex }}][fold]" value="{{ $row->fold }}">
                        <input type="hidden" name="items_data[{{ $rowIndex }}][total_meter]" value="{{ $row->total_meter }}">
                        <input type="hidden" name="items_data[{{ $rowIndex }}][shrinkage]" value="{{ $row->shrinkage }}">
                        <input type="hidden" name="items_data[{{ $rowIndex }}][type]" value="{{ $row->type }}">
                    </td>
                </tr>
            @endforeach
        @endif
        </tbody>
    </table>
</div>

@section('script')
<script>
    const lotSources = @json($lotSources->toArray());
    const itemTableBody = $('#itemTableBody');
    const jobWorkerSelect = $('[name="job_worker_id"]');
    const itemSelect = $('#item_id');

    function calculateValues(triggerField = '') {
        const meter = parseFloat($('#meter').val()) || 0;
        const fold = parseFloat($('#fold').val()) || 0;
        const type = ($('#type').val() || '').toString().trim();
        const shrinkageRaw = ($('#shrinkage').val() || '').toString().trim();
        const afterRaw = ($('#after_shrinkage_meter').val() || '').toString().trim();
        let shrinkagePercent = parseFloat(shrinkageRaw) || 0;
        let afterShrinkageMeter = parseFloat(afterRaw) || 0;

        // Total meter remains meter * fold / 100 (common)
        if (type === 'LOT TO LOT') {
            const baseTotal = (meter * fold) / 100;
            $('#total_meter').val(baseTotal ? baseTotal.toFixed(2) : '');
            // Rule 1: lot-to-lot -> after shrinkage input se hi % niklega
            if (afterRaw === '') {
                $('#shrinkage').val('');
                return;
            }

            if (meter > 0) {
                // As requested: if meter=10000 and after-shrinkage input=500 then shrinkage=5%
                // Formula: Shrinkage % = (AfterShrinkageInput / Meter) * 100
                shrinkagePercent = (afterShrinkageMeter / meter) * 100;
            } else {
                shrinkagePercent = 0;
            }

            $('#shrinkage').val(shrinkagePercent.toFixed(2));
        } else {
            // Rule 2: lot-to-part -> % input se after-shrinkage niklega
            const total = (meter * fold) / 100;
            $('#total_meter').val(total ? total.toFixed(2) : '');

            if (shrinkageRaw === '') {
                $('#after_shrinkage_meter').val('');
                return;
            }

            shrinkagePercent = Math.max(0, Math.min(99.99, shrinkagePercent));
            afterShrinkageMeter = meter - ((meter * shrinkagePercent) / 100);
            $('#shrinkage').val(shrinkagePercent.toFixed(2));
            $('#after_shrinkage_meter').val(afterShrinkageMeter.toFixed(2));
        }
    }

    function updateTypeBehaviour() {
        const type = ($('#type').val() || '').toString().trim();
        const isLotToLot = type === 'LOT TO LOT';
        $('#shrinkage').prop('readonly', isLotToLot);
        $('#total_meter').prop('readonly', false);
        $('#after_shrinkage_meter').prop('readonly', !isLotToLot);
        $('#shrinkage').val('');
        $('#after_shrinkage_meter').val('');
        calculateValues();
    }

    function generateInwardLotNo() {
        const selectedWorker = jobWorkerSelect.find('option:selected');
        const abbr = (selectedWorker.data('abbr') || 'JW').toString().trim();
        const chNo = ($('input[name="ch_no"]').val() || '').toString().trim() || 'CH0000';

        let maxSerial = 0;
        itemTableBody.find('tr').not('#no_item_row').each(function () {
            const currentLot = ($(this).data('lot-no') || '').toString().trim();
            const match = currentLot.match(/\/(\d{5})$/);
            if (!match) return;
            const serial = parseInt(match[1], 10);
            if (!isNaN(serial) && serial > maxSerial) {
                maxSerial = serial;
            }
        });

        const nextSerial = String(maxSerial + 1).padStart(5, '0');
        return abbr + '/' + chNo + '/' + nextSerial;
    }

    function filteredSources() {
        const jobWorkerId = jobWorkerSelect.val();
        if (!jobWorkerId) return [];

        return lotSources.filter(function (source) {
            return String(source.job_worker_id) === String(jobWorkerId);
        });
    }

    function populateItemDropdown() {
        const sources = filteredSources();
        const seenItems = new Set();

        itemSelect.empty().append('<option value="">Select</option>');

        sources.forEach(function (source) {
            if (!source.item_id || seenItems.has(String(source.item_id))) return;
            seenItems.add(String(source.item_id));

            const itemName = (source.item_name || '').toString().trim();
            if (!itemName) return;

            itemSelect.append('<option value="' + source.item_id + '">' + itemName + '</option>');
        });

        itemSelect.prop('disabled', seenItems.size === 0);
    }

    function findSourceByItemAndLot(itemId, lotNo) {
        if (!itemId || !lotNo) return null;
        return filteredSources().find(function (source) {
            return String(source.item_id) === String(itemId) && String(source.lot_no) === String(lotNo);
        }) || null;
    }

    function populateLotDropdown(itemId) {
        const lotSelect = $('#lot_no');
        lotSelect.empty().append('<option value="">Select</option>');

        if (!itemId) {
            lotSelect.prop('disabled', true);
            return;
        }

        const seenLots = new Set();
        filteredSources().forEach(function (source) {
            if (String(source.item_id) !== String(itemId)) return;
            const lotNo = (source.lot_no || '').toString().trim();
            if (!lotNo || seenLots.has(lotNo)) return;
            seenLots.add(lotNo);
            lotSelect.append('<option value="' + lotNo + '">' + lotNo + '</option>');
        });

        lotSelect.prop('disabled', seenLots.size === 0);
    }

    function autoFillFromSelection() {
        const itemId = $('#item_id').val();
        const lotNo = $('#lot_no').val();
        const source = findSourceByItemAndLot(itemId, lotNo);
        const colorValue = source ? (source.quality || source.colour || source.color || '') : '';
        $('#quality').val(colorValue);
        $('#meter').val(source ? (source.meter || '') : '');
        $('#fold').val(source ? (source.fold || '') : '');
        calculateValues();
    }

    function setRowHiddenInputs(row, index) {
        const itemId = row.data('item-id') || '';
        const lotNo = row.data('lot-no') || '';
        const quality = row.data('quality') || '';
        const meter = row.data('meter') || '';
        const fold = row.data('fold') || '';
        const total = row.data('total') || '';
        const shrinkage = row.data('shrinkage') || '';
        const afterShrinkage = row.data('after-shrinkage') || '';
        const type = row.data('type') || '';

        let hidden = row.find('.row-hidden-inputs');
        if (!hidden.length) {
            hidden = $('<td class="d-none row-hidden-inputs"></td>');
            row.append(hidden);
        }

        hidden.html(
            '<input type="hidden" name="items_data[' + index + '][item_id]" value="' + itemId + '">' +
            '<input type="hidden" name="items_data[' + index + '][lot_no]" value="' + lotNo + '">' +
            '<input type="hidden" name="items_data[' + index + '][quality]" value="' + quality + '">' +
            '<input type="hidden" name="items_data[' + index + '][meter]" value="' + meter + '">' +
            '<input type="hidden" name="items_data[' + index + '][fold]" value="' + fold + '">' +
            '<input type="hidden" name="items_data[' + index + '][total_meter]" value="' + total + '">' +
            '<input type="hidden" name="items_data[' + index + '][shrinkage]" value="' + shrinkage + '">' +
            '<input type="hidden" name="items_data[' + index + '][after_shrinkage_meter]" value="' + afterShrinkage + '">' +
            '<input type="hidden" name="items_data[' + index + '][type]" value="' + type + '">'
        );
    }

    function reindexRows() {
        let rows = itemTableBody.find('tr').not('#no_item_row');

        if (rows.length === 0) {
            if ($('#no_item_row').length === 0) {
                itemTableBody.append('<tr id="no_item_row"><td colspan="11" class="text-center">No items added yet.</td></tr>');
            }
            return;
        }

        $('#no_item_row').remove();

        rows.each(function (index) {
            $(this).find('td').first().text(index + 1);
            setRowHiddenInputs($(this), index);
        });
    }

    function clearEntryFields() {
        $('#item_id').val('');
        $('#type').val('LOT TO LOT');
        $('#quality, #meter, #fold, #total_meter, #shrinkage, #after_shrinkage_meter').val('');
        updateTypeBehaviour();
        $('#lot_no').empty().append('<option value="">Select</option>').val('').prop('disabled', true);
    }

    $('#meter').on('input', function () { calculateValues(); });
    $('#fold').on('input', function () { calculateValues(); });
    $('#shrinkage').on('input', function () { calculateValues(); });
    $('#after_shrinkage_meter').on('input', function () { calculateValues('after_shrinkage'); });
    $('#total_meter').on('input', function () { calculateValues(); });
    $('#type').on('change', function () {
        updateTypeBehaviour();
        calculateValues();
    });

    jobWorkerSelect.on('change', function () {
        clearEntryFields();
        populateItemDropdown();
    });

    $('#item_id').on('change', function () {
        const itemId = $(this).val();
        populateLotDropdown(itemId);
        $('#lot_no').val('');
        autoFillFromSelection();
    });

    $('#lot_no').on('change', function () {
        autoFillFromSelection();
    });

    $('#addItem').on('click', function () {
        const itemId = $('#item_id').val();
        const itemText = $('#item_id option:selected').text();
        const sourceLotNo = $('#lot_no').val();

        if (!itemId) {
            alert('Please select an item first.');
            return;
        }

        if (!sourceLotNo) {
            alert('Please select LOT NO.');
            return;
        }

        if (!jobWorkerSelect.val()) {
            alert('Please select Job Worker first.');
            return;
        }

        let meter = parseFloat($('#meter').val()) || 0;
        let fold = parseFloat($('#fold').val()) || 0;
        let totalMeter = parseFloat($('#total_meter').val()) || 0;

        if (meter <= 0 && fold <= 0 && totalMeter <= 0) {
            alert('Please enter meter or fold values.');
            return;
        }

        // Prevent duplicate line with same item + lot
        let duplicate = itemTableBody.find('tr').not('#no_item_row').filter(function () {
            const rowSourceLot = $(this).data('source-lot') || $(this).data('lot-no');
            return $(this).data('item-id') == itemId && String(rowSourceLot) === String(sourceLotNo);
        });

        if (duplicate.length > 0) {
            alert('This item and LOT NO already added.');
            return;
        }

        const inwardLotNo = generateInwardLotNo();

        if ($('#no_item_row').length) {
            $('#no_item_row').remove();
        }

        let newRow = $('<tr></tr>');
        newRow.attr('data-item-id', itemId);
        newRow.attr('data-lot-no', inwardLotNo);
        newRow.attr('data-source-lot', sourceLotNo);
        newRow.attr('data-quality', $('#quality').val());
        newRow.attr('data-meter', $('#meter').val());
        newRow.attr('data-fold', $('#fold').val());
        newRow.attr('data-total', $('#total_meter').val());
        newRow.attr('data-shrinkage', $('#shrinkage').val());
        newRow.attr('data-after-shrinkage', $('#after_shrinkage_meter').val());
        newRow.attr('data-type', $('#type').val());

        newRow.html(
            '<td></td>' +
            '<td>' + inwardLotNo + '</td>' +
            '<td>' + itemText + '</td>' +
            '<td>' + $('#quality').val() + '</td>' +
            '<td>' + $('#meter').val() + '</td>' +
            '<td>' + $('#fold').val() + '</td>' +
            '<td>' + $('#total_meter').val() + '</td>' +
            '<td>' + $('#shrinkage').val() + '</td>' +
            '<td>' + $('#after_shrinkage_meter').val() + '</td>' +
            '<td>' + $('#type').val() + '</td>' +
            '<td><button class="btn btn-danger btn-sm removeRow" type="button">Delete</button></td>' +
            '<td class="d-none row-hidden-inputs"></td>'
        );

        itemTableBody.append(newRow);
        reindexRows();
        clearEntryFields();
    });

    $(document).on('click', '.removeRow', function () {
        $(this).closest('tr').remove();
        reindexRows();
    });

    $(document).ready(function () {
        reindexRows();
        populateItemDropdown();
        updateTypeBehaviour();
        calculateValues();
        $('#lot_no').empty().append('<option value="">Select</option>').val('').prop('disabled', true);
    });
</script>
@endsection
