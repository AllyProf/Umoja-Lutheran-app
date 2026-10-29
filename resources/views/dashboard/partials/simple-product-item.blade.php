@php
    $unitOptions = $unitOptions ?? ['Crate', 'Carton', 'Box', 'Pack', 'Bag', 'Tray', 'Sado', 'Debe', 'Kiroba', 'Dozen', 'Pcs', 'Kg', 'Grams', 'Litres', 'ml', 'Bucket', 'Bundle', 'Packet', 'Bottle', 'Glass', 'Shot', 'Bunch'];
    $purchasing = $variant->purchasing_unit ?? 'Carton';
    $receiving = $variant->receiving_unit ?? 'Pcs';
@endphp

<div class="simple-item-card" data-index="{{ $index }}">
    <div class="simple-item-head">
        <span class="simple-item-num">Item <span class="item-number">{{ $index + 1 }}</span></span>
        <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="removeItem(this)" title="Remove">
            <i class="fa fa-trash"></i> Remove
        </button>
    </div>
    <div class="simple-item-body">
        <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant->id }}">

        <div class="form-group simple-field-name">
            <label>Product name <span class="text-danger">*</span></label>
            <input type="text" class="form-control form-control-lg item-name-input"
                name="variants[{{ $index }}][name]" value="{{ $variant->variant_name }}"
                placeholder="e.g. Sugar 1kg, Safari Lager 500ml" required>
        </div>

        <div class="simple-field-row">
            <div class="form-group">
                <label>Bought as</label>
                <select class="form-control purchasing-unit-select" name="variants[{{ $index }}][purchasing_unit]" required>
                    @foreach($unitOptions as $unit)
                        <option value="{{ $unit }}" {{ $purchasing === $unit ? 'selected' : '' }}>{{ $unit }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Counted as</label>
                <select class="form-control receiving-unit-select" name="variants[{{ $index }}][receiving_unit]" required>
                    @foreach($unitOptions as $unit)
                        <option value="{{ $unit }}" {{ $receiving === $unit ? 'selected' : '' }}>{{ $unit }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group ratio-field" style="display:none;">
                <label>How many in 1 package?</label>
                <input type="number" class="form-control items-per-package-input"
                    name="variants[{{ $index }}][items_per_package]"
                    value="{{ $variant->items_per_package ?? 1 }}" min="0.01" step="any">
            </div>
        </div>

        <div class="simple-field-row">
            <div class="form-group">
                <label>Buying price <span class="text-muted">(optional)</span></label>
                <input type="number" step="any" min="0" class="form-control"
                    name="variants[{{ $index }}][buying_price]"
                    value="{{ $variant->buying_price ?: '' }}" placeholder="TZS">
            </div>
            <div class="form-group">
                <label>Selling price <span class="text-muted">(optional)</span></label>
                <input type="number" step="any" min="0" class="form-control"
                    name="variants[{{ $index }}][selling_price_per_pic]"
                    value="{{ $variant->selling_price_per_pic ?: '' }}" placeholder="TZS">
            </div>
            <div class="form-group drink-only-field" style="display:none;">
                <label>Glass / shot price</label>
                <input type="number" step="any" min="0" class="form-control"
                    name="variants[{{ $index }}][selling_price_per_serving]"
                    value="{{ $variant->selling_price_per_serving ?: '' }}" placeholder="TZS">
            </div>
        </div>
    </div>
</div>
