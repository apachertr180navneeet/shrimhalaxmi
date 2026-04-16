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

        <!-- LOT FIRST -->
        <div class="col-md-2">
            <label>LOT NO</label>
            <select id="lot_no" class="form-control">
                <option value="">Select</option>
            </select>
        </div>

        <!-- ITEM SECOND -->
        <div class="col-md-2">
            <label>Item</label>
            <select id="item_id" class="form-control" disabled>
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

        const jobWorkerSelect = $('[name="job_worker_id"]');
        const lotSelect = $('#lot_no');
        const itemSelect = $('#item_id');

        function toNumber(val) {
            return parseFloat(val) || 0;
        }

        /* ===============================
           FILTER SOURCES
        ================================*/
        function filteredSources() {
            const jobWorkerId = jobWorkerSelect.val();

            if (!jobWorkerId) return [];

            return lotSources.filter(s =>
                String(s.job_worker_id) === String(jobWorkerId) &&
                parseFloat(s.remaining_meter) > 0
            );
        }

        /* ===============================
           LOAD LOT DROPDOWN
        ================================*/
        function loadLots() {
            const sources = filteredSources();
            const seen = new Set();

            lotSelect.empty().append('<option value="">Select</option>');

            sources.forEach(s => {
                if (!seen.has(s.lot_no)) {
                    seen.add(s.lot_no);

                    lotSelect.append(
                        `<option value="${s.lot_no}">
                    ${s.lot_no} (Remain: ${s.remaining_meter})
                </option>`
                    );
                }
            });

            lotSelect.prop('disabled', seen.size === 0);
        }

        /* ===============================
           LOAD ITEM BASED ON LOT
        ================================*/
        function loadItemByLot(lotNo) {
            const sources = filteredSources().filter(s => s.lot_no == lotNo);

            itemSelect.empty().append('<option value="">Select</option>');

            sources.forEach(s => {
                itemSelect.append(
                    `<option value="${s.item_id}">${s.item_name}</option>`
                );
            });

            itemSelect.prop('disabled', sources.length === 0);

            // 🔥 Auto select first item
            if (sources.length === 1) {
                itemSelect.val(sources[0].item_id);
                autoFill(sources[0]);
            }
        }

        /* ===============================
           AUTO FILL DATA
        ================================*/
        function autoFill(source) {
            if (!source) return;

            $('#quality').val(source.quality || '');
            $('#meter').val(source.remaining_meter || '');
            $('#fold').val(source.fold || '');

            calculateValues();
        }

        /* ===============================
           FIND SOURCE
        ================================*/
        function findSource(lotNo, itemId) {
            return filteredSources().find(s =>
                s.lot_no == lotNo && s.item_id == itemId
            );
        }

        /* ===============================
           CALCULATE
        ================================*/
        function calculateValues() {
            const meter = toNumber($('#meter').val());
            const fold = toNumber($('#fold').val());

            const total = (meter * fold) / 100;

            $('#total_meter').val(total ? total.toFixed(2) : '');
        }

        /* ===============================
           EVENTS
        ================================*/

        // Job worker change
        jobWorkerSelect.on('change', function() {
            loadLots();

            itemSelect.prop('disabled', true).val('');
            lotSelect.val('');

            $('#quality, #meter, #fold, #total_meter').val('');
        });

        // Lot change
        lotSelect.on('change', function() {
            const lotNo = $(this).val();

            loadItemByLot(lotNo);
        });

        // Item change
        itemSelect.on('change', function() {
            const lotNo = lotSelect.val();
            const itemId = $(this).val();

            const source = findSource(lotNo, itemId);

            autoFill(source);
        });

        // Live calc
        $('#meter, #fold').on('input', function() {
            calculateValues();
        });

        /* ===============================
           INIT
        ================================*/
        $(document).ready(function() {
            loadLots();
        });
    </script>
@endsection
