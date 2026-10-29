@extends('dashboard.layouts.app')

@section('content')
@php
    $formatVariantLabel = function ($product, $variant) {
        $name = $product->name ?? 'Item';
        $vName = trim((string) ($variant->variant_name ?? ''));
        if ($vName !== '' && !in_array(strtolower($vName), ['standard', 'std', 'default', 'n/a', 'na', '-'])) {
            return $name . ' · ' . $vName;
        }
        return $name;
    };
@endphp

<div class="app-title">
    <div>
        <h1><i class="fa fa-edit"></i>
            {{ ($role ?? '') === 'accountant' ? 'Adjust' : 'Edit' }} LPO Budget Items: #{{ $lpo->id }}
        </h1>
        <p>
            Period: {{ $lpo->start_date->format('M d, Y') }} to {{ $lpo->end_date->format('M d, Y') }}
            @if(($role ?? '') === 'accountant')
                · <span class="text-warning">Accountant review — change qty/price if needed, then Accept or Reject on the details page</span>
            @elseif($lpo->status === 'rejected')
                · <span class="text-danger">Returned by accountant — revise then resend</span>
            @endif
        </p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route('lpo.index') }}">LPO Budgets</a></li>
        <li class="breadcrumb-item">Edit Items</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="tile">
            <div class="tile-title-w-btn border-bottom pb-2">
                <h3 class="title">Budgeted Items</h3>
                <div class="btn-group">
                    <button type="button" class="btn btn-info mr-2" id="addAllItemsBtn">
                        <i class="fa fa-list"></i> Add All Stock Items
                    </button>
                    <button type="button" class="btn btn-primary" id="addItemBtn">
                        <i class="fa fa-plus"></i> Add Row
                    </button>
                </div>
            </div>

            <div class="tile-body pt-3">
                <form action="{{ route('lpo.update', $lpo->id) }}" method="POST" id="lpoForm">
                    @csrf
                    @method('PUT')

                    <div class="table-responsive">
                        <table class="table table-bordered" id="itemsTable">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 38%">Item Name / Product</th>
                                    <th style="width: 12%">Quantity</th>
                                    <th style="width: 16%">Unit</th>
                                    <th style="width: 14%">Unit Price (Est)</th>
                                    <th style="width: 14%">Total (Est)</th>
                                    <th style="width: 6%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lpo->items as $index => $item)
                                    @php
                                        $cleanName = preg_replace('/\s*[\(\-·]\s*standard\s*\)?\s*$/i', '', $item->item_name);
                                        $cleanName = trim(preg_replace('/\s+/', ' ', $cleanName));
                                    @endphp
                                    <tr class="item-row">
                                        <td>
                                            <input type="text" name="items[{{ $index }}][item_name]"
                                                class="form-control item-name" value="{{ $cleanName }}" required>
                                            <input type="hidden" name="items[{{ $index }}][product_variant_id]"
                                                value="{{ $item->product_variant_id }}" class="variant-id">
                                        </td>
                                        <td>
                                            <input type="number" name="items[{{ $index }}][quantity]"
                                                class="form-control quantity" value="{{ $item->quantity }}" step="0.01"
                                                required>
                                        </td>
                                        <td>
                                            <input type="text" name="items[{{ $index }}][unit]"
                                                class="form-control unit-input" value="{{ $item->unit }}"
                                                list="unitOptions" placeholder="e.g. Kg, Pcs, Carton">
                                        </td>
                                        <td>
                                            <input type="number" name="items[{{ $index }}][unit_price]"
                                                class="form-control unit-price" value="{{ $item->unit_price }}" step="0.01"
                                                required>
                                        </td>
                                        <td class="text-right total-price-cell align-middle">
                                            {{ number_format($item->total_price, 2) }}
                                        </td>
                                        <td class="text-center align-middle">
                                            <button type="button" class="btn btn-danger btn-sm remove-row">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="item-row">
                                        <td>
                                            <div class="product-selector-wrapper">
                                                <select class="form-control product-select mb-1">
                                                    <option value="">-- Search &amp; Pick Product --</option>
                                                    @foreach($products as $product)
                                                        @foreach($product->variants as $variant)
                                                            @php
                                                                $displayName = $formatVariantLabel($product, $variant);
                                                                $vUnit = $variant->receiving_unit ?: ($variant->purchasing_unit ?: '');
                                                                $vPrice = $variant->getLatestUnitCost();
                                                            @endphp
                                                            <option value="{{ $variant->id }}"
                                                                data-name="{{ $displayName }}"
                                                                data-unit="{{ $vUnit }}"
                                                                data-price="{{ $vPrice }}">
                                                                {{ $displayName }}
                                                            </option>
                                                        @endforeach
                                                    @endforeach
                                                </select>
                                            </div>
                                            <input type="text" name="items[0][item_name]" class="form-control item-name"
                                                placeholder="Item name..." required>
                                            <input type="hidden" name="items[0][product_variant_id]" class="variant-id">
                                        </td>
                                        <td>
                                            <input type="number" name="items[0][quantity]" class="form-control quantity"
                                                step="0.01" required>
                                        </td>
                                        <td>
                                            <input type="text" name="items[0][unit]" class="form-control unit-input"
                                                list="unitOptions" placeholder="e.g. Kg, Pcs, Carton">
                                        </td>
                                        <td>
                                            <input type="number" name="items[0][unit_price]" class="form-control unit-price"
                                                step="0.01" required>
                                        </td>
                                        <td class="text-right total-price-cell align-middle">0.00</td>
                                        <td class="text-center align-middle">
                                            <button type="button" class="btn btn-danger btn-sm remove-row">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="bg-light font-weight-bold">
                                    <td colspan="4" class="text-right h5">Estimated Grand Total:</td>
                                    <td class="text-right h4 text-primary" id="grandTotal">0.00</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="form-group mt-3">
                        <label class="font-weight-bold">Budget Notes</label>
                        <textarea name="notes" class="form-control" rows="3">{{ $lpo->notes }}</textarea>
                    </div>

                    <div class="tile-footer border-top pt-3 mt-4">
                        <button class="btn btn-primary btn-lg" type="submit">
                            <i class="fa fa-save"></i> Save Budget Items
                        </button>
                        <a href="{{ route('lpo.show', $lpo->id) }}" class="btn btn-secondary btn-lg ml-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/template" id="rowTemplate">
    <tr class="item-row">
        <td>
            <select class="form-control product-select mb-1">
                <option value="">-- Search Product --</option>
                @foreach($products as $product)
                    @foreach($product->variants as $variant)
                        @php
                            $displayName = $formatVariantLabel($product, $variant);
                            $vUnit = $variant->receiving_unit ?: ($variant->purchasing_unit ?: '');
                            $vPrice = $variant->getLatestUnitCost();
                        @endphp
                        <option value="{{ $variant->id }}"
                            data-name="{{ $displayName }}"
                            data-unit="{{ $vUnit }}"
                            data-price="{{ $vPrice }}">
                            {{ $displayName }}
                        </option>
                    @endforeach
                @endforeach
            </select>
            <input type="text" name="items[{index}][item_name]" class="form-control item-name" placeholder="Item name..." required>
            <input type="hidden" name="items[{index}][product_variant_id]" class="variant-id">
        </td>
        <td>
            <input type="number" name="items[{index}][quantity]" class="form-control quantity" step="0.01" required>
        </td>
        <td>
            <input type="text" name="items[{index}][unit]" class="form-control unit-input" list="unitOptions" placeholder="e.g. Kg, Pcs, Carton">
        </td>
        <td>
            <input type="number" name="items[{index}][unit_price]" class="form-control unit-price" step="0.01" required>
        </td>
        <td class="text-right total-price-cell align-middle">0.00</td>
        <td class="text-center align-middle">
            <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fa fa-trash"></i></button>
        </td>
    </tr>
</script>

<datalist id="unitOptions">
    <option value="Kg">
    <option value="Pcs">
    <option value="Pieces">
    <option value="Litre">
    <option value="L">
    <option value="Ml">
    <option value="Bottle">
    <option value="Carton">
    <option value="Crate">
    <option value="Box">
    <option value="Pack">
    <option value="Pkt">
    <option value="Bag">
    <option value="Sado">
    <option value="Tray">
    <option value="Dozen">
    <option value="Bundle">
</datalist>
@endsection

@section('scripts')
<style>
    #itemsTable .unit-input {
        min-width: 120px;
        width: 100%;
        font-size: 0.95rem;
        padding: 0.45rem 0.65rem;
    }
    #itemsTable th:nth-child(3),
    #itemsTable td:nth-child(3) {
        min-width: 140px;
        white-space: nowrap;
    }
    #itemsTable .product-select {
        width: 100%;
    }
    #itemsTable .item-name {
        font-weight: 600;
    }
</style>
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<script>
$(document).ready(function () {
    let rowCount = {{ $lpo->items->count() ?: 1 }};

    function cleanVariantLabel(productName, variantName) {
        const name = (productName || 'Item').trim();
        const v = (variantName || '').trim();
        const skip = ['standard', 'std', 'default', 'n/a', 'na', '-', ''];
        if (!v || skip.includes(v.toLowerCase())) {
            return name;
        }
        return name + ' · ' + v;
    }

    function calculateTotals() {
        let grandTotal = 0;
        $('.item-row').each(function () {
            let qty = parseFloat($(this).find('.quantity').val()) || 0;
            let price = parseFloat($(this).find('.unit-price').val()) || 0;
            let total = qty * price;
            $(this).find('.total-price-cell').text(total.toLocaleString('en-US', { minimumFractionDigits: 2 }));
            grandTotal += total;
        });
        $('#grandTotal').text(grandTotal.toLocaleString('en-US', { minimumFractionDigits: 2 }) + ' TZS');
    }

    $('#addItemBtn').click(function () {
        let template = $('#rowTemplate').html().replace(/\{index\}/g, rowCount);
        $('#itemsTable tbody').append(template);
        rowCount++;
        calculateTotals();
    });

    $(document).on('click', '.remove-row', function () {
        if ($('.item-row').length > 1) {
            $(this).closest('tr').remove();
            calculateTotals();
        }
    });

    $(document).on('input', '.quantity, .unit-price', function () {
        calculateTotals();
    });

    $(document).on('change', '.product-select', function () {
        let option = $(this).find(':selected');
        let row = $(this).closest('tr');
        if (option.val()) {
            row.find('.item-name').val(option.data('name'));
            row.find('.variant-id').val(option.val());
            row.find('.unit-input').val(option.data('unit') || '');
            row.find('.unit-price').val(option.data('price') || 0);
            $(this).hide();
            calculateTotals();
        }
    });

    $('#addAllItemsBtn').click(function () {
        Swal.fire({
            title: 'Add All Stock Items?',
            text: 'This will add every registered stock item to this budget. You can then adjust quantities and prices.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Add All'
        }).then((result) => {
            if (!result.isConfirmed) return;

            if ($('.item-row').length === 1 && !$('.item-row').find('.item-name').val()) {
                $('#itemsTable tbody').empty();
            }

            let products = @json($products);
            products.forEach(p => {
                (p.variants || []).forEach(v => {
                    let template = $('#rowTemplate').html().replace(/\{index\}/g, rowCount);
                    let $row = $(template);
                    let displayName = cleanVariantLabel(p.name, v.variant_name);
                    let vUnit = v.primary_unit || v.receiving_unit || v.purchasing_unit || '';
                    let vPrice = v.latest_cost || 0;

                    $row.find('.item-name').val(displayName);
                    $row.find('.variant-id').val(v.id);
                    $row.find('.unit-input').val(vUnit);
                    $row.find('.unit-price').val(vPrice);
                    $row.find('.product-select').hide();

                    $('#itemsTable tbody').append($row);
                    rowCount++;
                });
            });
            calculateTotals();
            Swal.fire('Added!', 'All stock items have been added to the list.', 'success');
        });
    });

    calculateTotals();
});
</script>
@endsection
