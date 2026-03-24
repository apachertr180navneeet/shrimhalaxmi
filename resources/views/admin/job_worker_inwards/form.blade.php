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
                    <option value="{{ $worker->id }}">{{ $worker->name }}</option>
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
            <label>LOT NO</label>
            <input type="text" id="lot_no" class="form-control" readonly>
        </div>

        <div class="col-md-2">
            <label>Item</label>
            <select id="item_id" class="form-control">
                <option value="">Select</option>
                @foreach ($items as $item)
                    <option value="{{ $item->id }}">{{ $item->item_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label>Quality</label>
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
            <label>Type</label>
            <select id="type" class="form-control">
                <option value="LOT TO LOT">LOT TO LOT</option>
                <option value="LOT TO PART">LOT TO PART</option>
            </select>
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
            <th>Quality</th>
            <th>Meter</th>
            <th>Fold</th>
            <th>Total</th>
            <th>Shrinkage</th>
            <th>Type</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody id="itemTableBody">
        @if($itemRows->isEmpty())
            <tr id="no_item_row">
                <td colspan="10" class="text-center">No items added yet.</td>
            </tr>
        @else
            @foreach($itemRows as $rowIndex => $row)
                <tr data-item-id="{{ $row->item_id }}" data-lot-no="{{ $row->lot_no }}" data-quality="{{ $row->quality }}" data-meter="{{ $row->meter }}" data-fold="{{ $row->fold }}" data-total="{{ $row->total_meter }}" data-shrinkage="{{ $row->shrinkage }}" data-type="{{ $row->type }}">
                    <td>{{ $rowIndex + 1 }}</td>
                    <td>{{ $row->lot_no }}</td>
                    <td>{{ $row->item?->item_name }}</td>
                    <td>{{ $row->quality }}</td>
                    <td>{{ $row->meter }}</td>
                    <td>{{ $row->fold }}</td>
                    <td>{{ $row->total_meter }}</td>
                    <td>{{ $row->shrinkage }}</td>
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

    function calculateValues() {
        
    }

    function getNextLotNo() {
        let maxSeq = 0;
        itemTableBody.find('tr').each(function () {
            if ($(this).attr('id') === 'no_item_row') return;
            let lot = $(this).find('td').eq(1).text().trim();
            if (!lot) return;
            let digits = lot.match(/(\d+)/g);
            if (!digits) return;
            digits.forEach(function (num) {
                let n = parseInt(num, 10);
                if (!isNaN(n) && n > maxSeq) {
                    maxSeq = n;
                }
            });
        });
        let next = maxSeq + 1;
        return 'LOT' + String(next).padStart(4, '0');
    }

    function findSourceByItem(itemId) {
        if (!itemId) return null;
        return lotSources.find(function (source) {
            return String(source.item_id) === String(itemId);
        }) || null;
    }

    function autoFillFromItem(itemId) {
        const source = findSourceByItem(itemId);
        if (source) {
            $('#lot_no').val(getNextLotNo());
           
        } else {
            $('#lot_no').val(getNextLotNo());
        
        }
    }

    function setRowHiddenInputs(row, index) {
        const itemId = row.data('item-id') || '';
        const lotNo = row.data('lot-no') || '';
        const quality = row.data('quality') || '';
        const meter = row.data('meter') || '';
        const fold = row.data('fold') || '';
        const total = row.data('total') || '';
        const shrinkage = row.data('shrinkage') || '';
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
            '<input type="hidden" name="items_data[' + index + '][type]" value="' + type + '">' 
        );
    }

    function reindexRows() {
        let rows = itemTableBody.find('tr').not('#no_item_row');

        if (rows.length === 0) {
            if ($('#no_item_row').length === 0) {
                itemTableBody.append('<tr id="no_item_row"><td colspan="10" class="text-center">No items added yet.</td></tr>');
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
        $('#quality, #meter, #fold, #total_meter, #shrinkage').val('');
        $('#lot_no').val(getNextLotNo());
    }

    $('#meter, #fold').on('input', calculateValues);

    $('#item_id').on('change', function () {
        autoFillFromItem($(this).val());
    });

    $('#addItem').on('click', function () {
        const itemId = $('#item_id').val();
        const itemText = $('#item_id option:selected').text();
        const lotNo = $('#lot_no').val() || getNextLotNo();

        if (!itemId) {
            alert('Please select an item first.');
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
            return $(this).data('item-id') == itemId && $(this).data('lot-no') == lotNo;
        });

        if (duplicate.length > 0) {
            alert('This item and LOT NO already added.');
            return;
        }

        if ($('#no_item_row').length) {
            $('#no_item_row').remove();
        }

        let newRow = $('<tr></tr>');
        newRow.attr('data-item-id', itemId);
        newRow.attr('data-lot-no', lotNo);
        newRow.attr('data-quality', $('#quality').val());
        newRow.attr('data-meter', $('#meter').val());
        newRow.attr('data-fold', $('#fold').val());
        newRow.attr('data-total', $('#total_meter').val());
        newRow.attr('data-shrinkage', $('#shrinkage').val());
        newRow.attr('data-type', $('#type').val());

        newRow.html(
            '<td></td>' +
            '<td>' + lotNo + '</td>' +
            '<td>' + itemText + '</td>' +
            '<td>' + $('#quality').val() + '</td>' +
            '<td>' + $('#meter').val() + '</td>' +
            '<td>' + $('#fold').val() + '</td>' +
            '<td>' + $('#total_meter').val() + '</td>' +
            '<td>' + $('#shrinkage').val() + '</td>' +
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
        $('#lot_no').val(getNextLotNo());
    });
</script>
@endsection