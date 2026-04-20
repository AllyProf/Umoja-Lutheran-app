@php
    // Parse measurement string "350 ml" -> 350, ml
    $measValue = floatval($variant->measurement);
    $unitValue = 'ml'; // default
    if (preg_match('/([0-9.]+)\s*([a-zA-Z]+)/', $variant->measurement, $matches)) {
        $measValue = $matches[1];
        $unitValue = strtolower($matches[2]);
    }
@endphp

<div class="variant-card mb-4 border rounded p-3"
    style="background: #fdfdfd; border-left: 5px solid #009688 !important;">
    @php
        $isFirstStandard = ($index === 0 && strtolower($variant->variant_name) === 'standard' && $product->variants->count() === 1);
    @endphp

    <div class="d-flex justify-content-between mb-3 border-bottom pb-2"
        style="{{ $isFirstStandard ? 'display: none !important;' : '' }}">
        <h5 class="text-primary font-weight-bold">Packaging Details #{{ $index + 1 }}</h5>
        <span class="badge badge-info">Level: 1</span>
    </div>

    <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant->id }}">

    <div class="row">
        <div class="col-md-4" style="{{ $isFirstStandard ? 'display: none;' : '' }}">
            <div class="form-group">
                <label class="control-label font-weight-bold">Packaging Label</label>
                <input type="text" class="form-control" name="variants[{{ $index }}][name]"
                    value="{{ $variant->variant_name }}" required>
            </div>
        </div>
        <div class="col-md-4 volume-weight-section">
            <div class="form-group">
                <label class="control-label font-weight-bold">VOLUME/WEIGHT</label>
                <div class="input-group">
                    <input type="number" class="form-control measurement-input"
                        name="variants[{{ $index }}][measurement]" value="{{ $measValue }}" step="any">
                    <div class="input-group-append">
                        <select class="form-control unit-select" name="variants[{{ $index }}][unit]"
                            style="max-width: 90px; border-left: 0;">
                            <option value="ml" {{ $unitValue == 'ml' ? 'selected' : '' }}>ml</option>
                            <option value="l" {{ $unitValue == 'l' ? 'selected' : '' }}>L</option>
                            <option value="kg" {{ $unitValue == 'kg' ? 'selected' : '' }}>kg</option>
                            <option value="g" {{ $unitValue == 'g' ? 'selected' : '' }}>g</option>
                            <option value="pcs" {{ $unitValue == 'pcs' ? 'selected' : '' }}>pcs</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 selling-type-section"
            style="{{ ($product->category == 'juices') ? 'display:none;' : '' }}">
            <div class="form-group">
                <label class="control-label font-weight-bold">SELLING TYPE</label>
                <select class="form-control selling-method-select" name="variants[{{ $index }}][selling_method]"
                    onchange="togglePricing(this)" required>
                    <option value="pic" {{ ($variant->can_sell_as_pic && !$variant->can_sell_as_serving) ? 'selected' : '' }}>Per Bottle / Item Only</option>
                    <option value="glass" {{ (!$variant->can_sell_as_pic && $variant->can_sell_as_serving) ? 'selected' : '' }}>Per Glass / Tot Only</option>
                    <option value="mixed" {{ ($variant->can_sell_as_pic || $product->category == 'juices') ? 'selected' : '' }}>Mixed (Both Bottle & Glass)</option>
                </select>
            </div>
        </div>

        <!-- Food Units (Hidden by default, shown for food items) -->
        <div class="col-md-12 form-group food-units-section"
            style="display: none; border-left: 3px solid #ff9800; padding-left: 10px;">
            <div class="row">
                <div class="col-md-6">
                    <label class="small font-weight-bold text-muted">PURCHASING UNIT</label>
                    <select class="form-control purchasing-unit-select" name="variants[{{ $index }}][purchasing_unit]">
                        <option value="">Select Unit</option>
                        <option value="Sado" {{ $variant->purchasing_unit == 'Sado' ? 'selected' : '' }}>Sado</option>
                        <option value="Debe" {{ $variant->purchasing_unit == 'Debe' ? 'selected' : '' }}>Debe</option>
                        <option value="Kiroba" {{ $variant->purchasing_unit == 'Kiroba' ? 'selected' : '' }}>Kiroba
                        </option>
                        <option value="Carton" {{ $variant->purchasing_unit == 'Carton' ? 'selected' : '' }}>Carton
                        </option>
                        <option value="Crate" {{ $variant->purchasing_unit == 'Crate' ? 'selected' : '' }}>Crate</option>
                        <option value="Kg" {{ $variant->purchasing_unit == 'Kg' ? 'selected' : '' }}>Kg</option>
                        <option value="L" {{ $variant->purchasing_unit == 'L' ? 'selected' : '' }}>L (Litre)</option>
                        <option value="Dozen" {{ $variant->purchasing_unit == 'Dozen' ? 'selected' : '' }}>Dozen</option>
                        <option value="Piece" {{ $variant->purchasing_unit == 'Piece' ? 'selected' : '' }}>Piece</option>
                        <option value="Packet" {{ $variant->purchasing_unit == 'Packet' ? 'selected' : '' }}>Packet
                        </option>
                        <option value="Bunch" {{ $variant->purchasing_unit == 'Bunch' ? 'selected' : '' }}>Bunch</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="small font-weight-bold text-muted">RECEIVING UNIT</label>
                    <select class="form-control receiving-unit-select" name="variants[{{ $index }}][receiving_unit]">
                        <option value="">Select Unit</option>
                        <option value="Kg" {{ $variant->receiving_unit == 'Kg' ? 'selected' : '' }}>Kg</option>
                        <option value="Grams" {{ $variant->receiving_unit == 'Grams' ? 'selected' : '' }}>Grams</option>
                        <option value="Litres" {{ $variant->receiving_unit == 'Litres' ? 'selected' : '' }}>Litres
                        </option>
                        <option value="Pieces" {{ strtolower($variant->receiving_unit) == 'pieces' ? 'selected' : '' }}>
                            Pieces
                        </option>
                        <option value="pcs" {{ strtolower($variant->receiving_unit) == 'pcs' ? 'selected' : '' }}>pcs
                        </option>
                        <option value="PIC" {{ strtolower($variant->receiving_unit) == 'pic' ? 'selected' : '' }}>PIC
                        </option>
                        <option value="Tray" {{ $variant->receiving_unit == 'Tray' ? 'selected' : '' }}>Tray</option>
                        <option value="Sado" {{ $variant->receiving_unit == 'Sado' ? 'selected' : '' }}>Sado</option>
                        <option value="Debe" {{ $variant->receiving_unit == 'Debe' ? 'selected' : '' }}>Debe</option>
                        <option value="Kiroba" {{ $variant->receiving_unit == 'Kiroba' ? 'selected' : '' }}>Kiroba
                        </option>
                        <option value="Carton" {{ $variant->receiving_unit == 'Carton' ? 'selected' : '' }}>Carton
                        </option>
                        <option value="Crate" {{ $variant->receiving_unit == 'Crate' ? 'selected' : '' }}>Crate</option>
                        <option value="Dozen" {{ $variant->receiving_unit == 'Dozen' ? 'selected' : '' }}>Dozen</option>
                        <option value="Packet" {{ $variant->receiving_unit == 'Packet' ? 'selected' : '' }}>Packet
                        </option>
                        <option value="Bunch" {{ $variant->receiving_unit == 'Bunch' ? 'selected' : '' }}>Bunch</option>
                    </select>
                </div>
                <div class="col-md-12 mt-2 ratio-entry-section"
                    style="{{ ($product->category == 'juices' || $variant->receiving_unit == 'Kg') ? 'display:none;' : '' }}">
                    <label class="small font-weight-bold text-warning"><i class="fa fa-balance-scale"></i> RATIO: How
                        many Receiving Units in 1 Purchasing Unit?</label>
                    <input type="number" class="form-control items-per-package-input"
                        name="variants[{{ $index }}][items_per_package]" value="{{ $variant->items_per_package ?: 1 }}"
                        placeholder="e.g. 5 (5 Kg per 1 Sado)" step="0.01">
                    <small class="text-muted">Used to automatically calculate total received inventory.</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3 pt-3 border-top">
        <div class="col-md-4">
            <div class="form-group mb-0">
                <label class="control-label font-weight-bold">Product Image</label>
                @if($variant->image)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $variant->image) }}" style="height: 60px; border-radius: 4px;"
                            class="shadow-sm border">
                    </div>
                @else
                    <div class="text-muted small mb-2"><i class="fa fa-image mr-1"></i> No image set</div>
                @endif
                <input type="file" class="form-control-file" name="variants[{{ $index }}][image]" accept="image/*">
            </div>
        </div>

        <div class="col-md-8">
            <div class="row">
                <!-- Selling Prices (Drink Only) -->
                <div class="col-md-6 drink-only-section">
                    <div class="form-group mb-2">
                        <label class="control-label font-weight-bold text-primary"><i class="fa fa-money-bill"></i>
                            Selling Price / PIC (TSH)</label>
                        <input type="number" class="form-control border-primary"
                            name="variants[{{ $index }}][selling_price_per_pic]"
                            value="{{ $variant->selling_price_per_pic }}" min="0" placeholder="0.00">
                    </div>
                </div>

                <div class="col-md-6 pricing-glass drink-only-section"
                    style="{{ ($variant->can_sell_as_serving) ? '' : 'display:none;' }}">
                    <div class="form-group mb-2">
                        <label class="control-label font-weight-bold text-info"><i class="fa fa-glass"></i> Selling
                            Price / Serving (TSH)</label>
                        <input type="number" class="form-control border-info"
                            name="variants[{{ $index }}][selling_price_per_serving]"
                            value="{{ $variant->selling_price_per_serving }}" min="0" placeholder="0.00">
                    </div>
                </div>

                <!-- Servings Configuration (Only for Glass/Mixed) -->
                <div class="col-md-12 pricing-glass servings-section mt-2"
                    style="{{ ($variant->can_sell_as_serving) ? '' : 'display:none;' }}">
                    <div class="form-group mb-0 p-2 rounded" style="background: #fff9c4; border: 1px dashed #fbc02d;">
                        <label class="control-label font-weight-bold text-dark mb-1"><i class="fa fa-calculator"></i>
                            Servings Per <span class="servings-unit-label">Bottle</span></label>
                        <input type="number" class="form-control" name="variants[{{ $index }}][servings]"
                            value="{{ $variant->servings_per_pic }}" min="1">
                        <small class="text-muted">How many glasses/servings in one unit?</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>