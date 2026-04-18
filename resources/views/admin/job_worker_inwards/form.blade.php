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
            <input type="text" name="ch_no" class="form-control" value="{{ old('ch_no', $inward['ch_no'] ?? '') }}"
                readonly>
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
            <label>LOT NO</label>
            <select id="lot_no" class="form-control" disabled>
                <option value="">Select</option>
            </select>
        </div>


        <div class="col-md-2">
            <label>Item</label>
            <select id="item_id" class="form-control">
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
            <input type="number" id="total_meter" class="form-control" readonly>
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
            @if ($itemRows->isEmpty())
                <tr id="no_item_row">
                    <td colspan="11" class="text-center">No items added yet.</td>
                </tr>
            @else
                @foreach ($itemRows as $rowIndex => $row)
                    @php
                        $rowShrinkage = (float) ($row->shrinkage ?? 0);
                        $rowMeter = (float) ($row->meter ?? 0);
                        $rowAfterShrinkage = $rowMeter - ($rowMeter * $rowShrinkage) / 100;
                    @endphp
                    <tr data-item-id="{{ $row->item_id }}" data-lot-no="{{ $row->lot_no }}"
                        data-source-lot="{{ $row->source_lot_no ?? $row->lot_no }}" data-quality="{{ $row->quality }}"
                        data-meter="{{ $row->meter }}" data-fold="{{ $row->fold }}"
                        data-total="{{ $row->total_meter }}" data-shrinkage="{{ $row->shrinkage }}"
                        data-after-shrinkage="{{ number_format($rowAfterShrinkage, 2, '.', '') }}"
                        data-type="{{ $row->type }}">
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
                            <input type="hidden" name="items_data[{{ $rowIndex }}][item_id]"
                                value="{{ $row->item_id }}">
                            <input type="hidden" name="items_data[{{ $rowIndex }}][lot_no]"
                                value="{{ $row->lot_no }}">
                            <input type="hidden" name="items_data[{{ $rowIndex }}][source_lot_no]"
                                value="{{ $row->source_lot_no ?? $row->lot_no }}">
                            <input type="hidden" name="items_data[{{ $rowIndex }}][quality]"
                                value="{{ $row->quality }}">
                            <input type="hidden" name="items_data[{{ $rowIndex }}][meter]"
                                value="{{ $row->meter }}">
                            <input type="hidden" name="items_data[{{ $rowIndex }}][fold]"
                                value="{{ $row->fold }}">
                            <input type="hidden" name="items_data[{{ $rowIndex }}][total_meter]"
                                value="{{ $row->total_meter }}">
                            <input type="hidden" name="items_data[{{ $rowIndex }}][shrinkage]"
                                value="{{ $row->shrinkage }}">
                            <input type="hidden" name="items_data[{{ $rowIndex }}][type]"
                                value="{{ $row->type }}">
                            <input type="hidden" name="items_data[{{ $rowIndex }}][after_shrinkage_meter]"
                                value="{{ number_format($rowAfterShrinkage, 2, '.', '') }}">
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
        const lotSelect = $('#lot_no');
        const itemSelect = $('#item_id');

        /* ===============================
           HELPERS
        ================================*/
        function toNumber(value) {
            const n = parseFloat(value);
            return isNaN(n) ? 0 : n;
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/"/g, '&quot;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');
        }

        function buildHiddenInputs(index, data) {
            return Object.entries(data).map(([key, value]) =>
                `<input type="hidden" name="items_data[${index}][${key}]" value="${escapeHtml(value)}">`
            ).join('');
        }

        function sourceRemainingMeter(source) {
            if (!source) return 0;
            const remaining = toNumber(source.remaining_meter);
            if (remaining > 0) return remaining;
            return toNumber(source.meter);
        }

        function filteredSources() {
            const jobWorkerId = jobWorkerSelect.val();
            if (!jobWorkerId) return [];

            return lotSources.filter(source =>
                String(source.job_worker_id) === String(jobWorkerId) &&
                sourceRemainingMeter(source) > 0
            );
        }

        /* ===============================
           LOAD LOT
        ================================*/
        function loadLotDropdown() {
            lotSelect.empty().append('<option value="">Select</option>');
            const seen = new Set();

            filteredSources().forEach(source => {
                const lotNo = source.lot_no;
                if (!lotNo || seen.has(lotNo)) return;

                seen.add(lotNo);

                lotSelect.append(
                    `<option value="${lotNo}">
                ${lotNo} (Remain: ${sourceRemainingMeter(source).toFixed(2)})
            </option>`
                );
            });

            lotSelect.prop('disabled', seen.size === 0);
        }

        /* ===============================
           LOAD ITEM AFTER LOT
        ================================*/
        function loadItemByLot(lotNo) {
            const sources = filteredSources().filter(s =>
                String(s.lot_no) === String(lotNo)
            );

            itemSelect.empty().append('<option value="">Select</option>');

            const seen = new Set();

            sources.forEach(source => {
                if (seen.has(source.item_id)) return;
                seen.add(source.item_id);

                itemSelect.append(
                    `<option value="${source.item_id}">
                ${source.item_name}
            </option>`
                );
            });

            itemSelect.prop('disabled', seen.size === 0);

            if (seen.size === 1) {
                itemSelect.val(sources[0].item_id).trigger('change');
            }
        }

        /* ===============================
           FIND SOURCE
        ================================*/
        function findSource(itemId, lotNo) {
            return filteredSources().find(s =>
                String(s.item_id) === String(itemId) &&
                String(s.lot_no) === String(lotNo)
            ) || null;
        }

        /* ===============================
           AUTO FILL
        ================================*/
        function autoFill() {
            const source = findSource(itemSelect.val(), lotSelect.val());
            if (!source) return;

            $('#quality').val(source.quality || '');
            $('#meter').val(sourceRemainingMeter(source).toFixed(2));
            $('#fold').val(source.fold || '');

            calculateValues();
        }

        /* ===============================
           CALCULATION
        ================================*/
        function calculateValues() {
            const meter = toNumber($('#meter').val());
            const fold = toNumber($('#fold').val());
            const type = ($('#type').val() || '').trim();

            const shrinkageRaw = $('#shrinkage').val();
            const afterRaw = $('#after_shrinkage_meter').val();

            let shrinkage = parseFloat(shrinkageRaw) || 0;
            let after = parseFloat(afterRaw) || 0;

            const total = (meter * fold) / 100;
            $('#total_meter').val(total ? total.toFixed(2) : '');

            if (type === 'LOT TO LOT') {
                if (!afterRaw) {
                    $('#shrinkage').val('');
                    return;
                }

                shrinkage = meter > 0 ? ((meter - after) / meter) * 100 : 0;
                $('#shrinkage').val(shrinkage.toFixed(2));

            } else {
                if (!shrinkageRaw) {
                    $('#after_shrinkage_meter').val('');
                    return;
                }

                shrinkage = Math.max(0, Math.min(99.99, shrinkage));
                const denom = 100 - shrinkage;

                after = denom > 0 ? ((total * 100) / denom) : 0;

                $('#after_shrinkage_meter').val(after.toFixed(2));
                $('#shrinkage').val(shrinkage.toFixed(2));
            }
        }

        /* ===============================
           TYPE BEHAVIOUR
        ================================*/
        function updateTypeBehaviour() {
            const isLotToLot = $('#type').val() === 'LOT TO LOT';

            $('#shrinkage').prop('readonly', isLotToLot);
            $('#after_shrinkage_meter').prop('readonly', !isLotToLot);

            $('#shrinkage').val('');
            $('#after_shrinkage_meter').val('');

            calculateValues();
        }

        /* ===============================
           CLEAR
        ================================*/
        function clearEntryFields() {
            itemSelect.val('').prop('disabled', true);
            lotSelect.val('');

            $('#quality, #meter, #fold, #total_meter, #shrinkage, #after_shrinkage_meter').val('');
        }

        /* ===============================
           GENERATE LOT NO
        ================================*/
        function generateInwardLotNo() {
            const abbr = jobWorkerSelect.find('option:selected').data('abbr') || 'JW';
            const chNo = $('input[name="ch_no"]').val() || 'CH0000';

            let max = 0;

            itemTableBody.find('tr').not('#no_item_row').each(function() {
                const lot = $(this).data('lot-no') || '';
                const m = lot.match(/\/(\d{5})$/);
                if (m) max = Math.max(max, parseInt(m[1]));
            });

            return `${abbr}/${chNo}/${String(max + 1).padStart(5, '0')}`;
        }

        /* ===============================
           EVENTS
        ================================*/
        jobWorkerSelect.on('change', () => {
            clearEntryFields();
            loadLotDropdown();
        });

        lotSelect.on('change', () => loadItemByLot(lotSelect.val()));
        itemSelect.on('change', autoFill);

        $('#meter, #fold').on('input', calculateValues);
        $('#shrinkage, #after_shrinkage_meter').on('input', calculateValues);
        $('#type').on('change', updateTypeBehaviour);

        /* ===============================
           ADD ITEM
        ================================*/
        $('#addItem').on('click', function() {

            const itemId = itemSelect.val();
            const itemText = $('#item_id option:selected').text();
            const sourceLotNo = lotSelect.val();

            if (!itemId) return alert('Select Item');
            if (!sourceLotNo) return alert('Select LOT');
            if (!jobWorkerSelect.val()) return alert('Select Job Worker');

            const source = findSource(itemId, sourceLotNo);
            const remaining = sourceRemainingMeter(source);

            const meter = toNumber($('#meter').val());
            const fold = toNumber($('#fold').val());
            const total = toNumber($('#total_meter').val());

            if (meter <= 0 && fold <= 0 && total <= 0) {
                return alert('Enter values');
            }

            if (remaining > 0 && meter > remaining) {
                return alert('Exceeds remaining!');
            }

            let duplicate = itemTableBody.find('tr').not('#no_item_row').filter(function() {
                return $(this).data('item-id') == itemId &&
                    $(this).data('source-lot') == sourceLotNo;
            });

            if (duplicate.length) return alert('Already added');

            const inwardLotNo = generateInwardLotNo();

            $('#no_item_row').remove();

            let row = `<tr 
        data-item-id="${itemId}"
        data-lot-no="${inwardLotNo}"
        data-source-lot="${sourceLotNo}"
        data-quality="${$('#quality').val()}"
        data-meter="${meter}"
        data-fold="${fold}"
        data-total="${total}"
        data-shrinkage="${$('#shrinkage').val()}"
        data-after-shrinkage="${$('#after_shrinkage_meter').val()}"
        data-type="${$('#type').val()}"
    >
        <td></td>
        <td>${inwardLotNo}</td>
        <td>${itemText}</td>
        <td>${$('#quality').val()}</td>
        <td>${meter}</td>
        <td>${fold}</td>
        <td>${total}</td>
        <td>${$('#shrinkage').val()}</td>
        <td>${$('#after_shrinkage_meter').val()}</td>
        <td>${$('#type').val()}</td>
        <td><button class="removeRow btn btn-danger btn-sm">Delete</button></td>
        <td class="d-none row-hidden-inputs"></td>
    </tr>`;

            itemTableBody.append(row);

            reindexRows();
            clearEntryFields();
        });

        /* ===============================
           REMOVE
        ================================*/
        $(document).on('click', '.removeRow', function() {
            $(this).closest('tr').remove();
            reindexRows();
        });

        /* ===============================
           REINDEX
        ================================*/
        function reindexRows() {
            let rows = itemTableBody.find('tr').not('#no_item_row');

            if (!rows.length) {
                itemTableBody.append('<tr id="no_item_row"><td colspan="11">No items</td></tr>');
                return;
            }

            $('#no_item_row').remove();

            rows.each(function(i) {
                const row = $(this);
                row.find('td:first').text(i + 1);

                row.find('.row-hidden-inputs').html(buildHiddenInputs(i, {
                    item_id: row.data('item-id') ?? '',
                    lot_no: row.data('lot-no') ?? '',
                    source_lot_no: row.data('source-lot') ?? '',
                    quality: row.data('quality') ?? '',
                    meter: row.data('meter') ?? '',
                    fold: row.data('fold') ?? '',
                    total_meter: row.data('total') ?? '',
                    shrinkage: row.data('shrinkage') ?? '',
                    type: row.data('type') ?? '',
                    after_shrinkage_meter: row.data('after-shrinkage') ?? '',
                }));
            });
        }

        /* ===============================
           INIT
        ================================*/
        $(document).ready(function() {
            lotSelect.prop('disabled', true);
            itemSelect.prop('disabled', true);

            updateTypeBehaviour();
        });
    </script>
@endsection
