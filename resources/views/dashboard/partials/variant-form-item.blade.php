@php
    // Parse measurement string "350 ml" -> 350, ml
    $measValue = floatval($variant->measurement);
    $unitValue = 'ml'; // default
    if (preg_match('/([0-9.]+)\s*([a-zA-Z]+)/', $variant->measurement, $matches)) {
        $measValue = $matches[1];
        $unitValue = strtolower($matches[2]);
    }
@endphp

<div class="variant-card mb-4 border rounded p-3" style="background: #fdfdfd; border-left: 5px solid #009688 !important;">
    <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
        <h5 class="text-primary font-weight-bold">Variant #{{ $index + 1 }}</h5>
        <span class="badge badge-info">Existing</span>
    </div>
    
    <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant->id }}">

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">Product Name</label>
                <input type="text" class="form-control" name="variants[{{ $index }}][name]" value="{{ $variant->variant_name }}" required>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">Measurement Unit</label>
                <div class="input-group">
                    <input type="number" class="form-control" name="variants[{{ $index }}][measurement]" value="{{ $measValue }}" step="any" required>
                    <div class="input-group-append">
                        <select class="form-control" name="variants[{{ $index }}][unit]" style="max-width: 90px; border-left: 0;">
                            <option value="ml" {{ $unitValue == 'ml' ? 'selected' : '' }}>ml</option>
                            <option value="l" {{ $unitValue == 'l' ? 'selected' : '' }}>L</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">Selling Method</label>
                <select class="form-control" name="variants[{{ $index }}][selling_method]" onchange="togglePricing(this)" required>
                    <option value="pic" {{ ($variant->can_sell_as_pic && !$variant->can_sell_as_serving) ? 'selected' : '' }}>Per Bottle / Item Only</option>
                    <option value="glass" {{ (!$variant->can_sell_as_pic && $variant->can_sell_as_serving) ? 'selected' : '' }}>Per Glass / Tot Only</option>
                    <option value="mixed" {{ ($variant->can_sell_as_pic && $variant->can_sell_as_serving) ? 'selected' : '' }}>Mixed (Both Bottle & Glass)</option>
                </select>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">Current Image</label>
                @if($variant->image)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $variant->image) }}" style="height: 60px; border-radius: 4px;">
                    </div>
                @else
                    <div class="text-muted small mb-2">No image</div>
                @endif
                <input type="file" class="form-control-file" name="variants[{{ $index }}][image]" accept="image/*">
            </div>
        </div>

        <div class="col-md-8">
            <!-- Servings Configuration (Only for Glass/Mixed) -->
            <div class="form-group pricing-glass" style="{{ ($variant->can_sell_as_serving) ? '' : 'display:none;' }}">
                <label class="control-label">Servings Per Bottle</label>
                <input type="number" class="form-control" name="variants[{{ $index }}][servings]" value="{{ $variant->servings_per_pic }}">
                <small class="text-muted">How many glasses in one bottle?</small>
            </div>
        </div>
    </div>
</div>
