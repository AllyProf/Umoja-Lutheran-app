@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-cog"></i> Configure Serving Details</h1>
        <p>Set up PIC-based serving configuration for: <strong>{{ $variant->product->name }} ({{ $variant->measurement }})</strong></p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.products.show', $variant->product_id) }}">{{ $variant->product->name }}</a></li>
        <li class="breadcrumb-item active">Configure Serving</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="tile">
            <h3 class="tile-title">PIC Serving Configuration</h3>
            <div class="tile-body">
                <form action="{{ route('admin.products.update-serving', $variant->id) }}" method="POST" id="servingForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        <strong>What is a PIC?</strong> PIC stands for "piece" - typically a bottle. Configure how many servings (glasses/tots/shots) come from one PIC.
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="servings_per_pic">Servings per PIC <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="servings_per_pic" name="servings_per_pic" 
                                       value="{{ old('servings_per_pic', $variant->servings_per_pic ?? 1) }}" 
                                       min="1" max="100" required>
                                <small class="form-text text-muted">
                                    Examples: 6 glasses for wine, 30 tots for whiskey, 1 for soda
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="selling_unit">Selling Unit <span class="text-danger">*</span></label>
                                <select class="form-control" id="selling_unit" name="selling_unit" required>
                                    <option value="pic" {{ old('selling_unit', $variant->selling_unit) == 'pic' ? 'selected' : '' }}>PIC (Whole Bottle)</option>
                                    <option value="glass" {{ old('selling_unit', $variant->selling_unit) == 'glass' ? 'selected' : '' }}>Glass</option>
                                    <option value="tot" {{ old('selling_unit', $variant->selling_unit) == 'tot' ? 'selected' : '' }}>Tot/Shot</option>
                                    <option value="shot" {{ old('selling_unit', $variant->selling_unit) == 'shot' ? 'selected' : '' }}>Shot</option>
                                    <option value="cocktail" {{ old('selling_unit', $variant->selling_unit) == 'cocktail' ? 'selected' : '' }}>Cocktail</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="can_sell_as_pic" name="can_sell_as_pic" value="1"
                                           {{ old('can_sell_as_pic', $variant->can_sell_as_pic) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="can_sell_as_pic">Can sell as whole PIC</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="can_sell_as_serving" name="can_sell_as_serving" value="1"
                                           {{ old('can_sell_as_serving', $variant->can_sell_as_serving) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="can_sell_as_serving">Can sell by serving (glass/tot)</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h4>Selling Prices</h4>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="selling_price_per_pic">Selling Price per PIC (TSH)</label>
                                <input type="number" class="form-control" id="selling_price_per_pic" name="selling_price_per_pic" 
                                       value="{{ old('selling_price_per_pic', $variant->selling_price_per_pic) }}" 
                                       step="0.01" min="0">
                                <small class="form-text text-muted">Price for selling the whole PIC/bottle</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="selling_price_per_serving">Selling Price per Serving (TSH)</label>
                                <input type="number" class="form-control" id="selling_price_per_serving" name="selling_price_per_serving" 
                                       value="{{ old('selling_price_per_serving', $variant->selling_price_per_serving) }}" 
                                       step="0.01" min="0">
                                <small class="form-text text-muted">Price per glass/tot/shot</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Save Configuration
                        </button>
                        <a href="{{ route('admin.products.show', $variant->product_id) }}" class="btn btn-secondary">
                            <i class="fa fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="tile">
            <h4 class="tile-title">Profit Preview</h4>
            <div class="tile-body">
                <div id="profitPreview">
                    <p class="text-muted">Enter prices to see profit comparison</p>
                </div>
            </div>
        </div>
        
        <div class="tile mt-3">
            <h4 class="tile-title">Examples</h4>
            <div class="tile-body">
                <h6>Wine (750ml):</h6>
                <ul class="small">
                    <li>Servings per PIC: 6 glasses</li>
                    <li>Selling unit: Glass</li>
                    <li>Can sell both ways: ✓</li>
                </ul>
                
                <h6>Whiskey (1L):</h6>
                <ul class="small">
                    <li>Servings per PIC: 30 tots</li>
                    <li>Selling unit: Tot</li>
                    <li>Can sell both ways: ✓</li>
                </ul>
                
                <h6>Soda (500ml):</h6>
                <ul class="small">
                    <li>Servings per PIC: 1</li>
                    <li>Selling unit: PIC</li>
                    <li>Sell as PIC only: ✓</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Real-time profit calculation
function calculateProfit() {
    const servingsPerPic = parseInt($('#servings_per_pic').val()) || 1;
    const pricePic = parseFloat($('#selling_price_per_pic').val()) || 0;
    const priceServing = parseFloat($('#selling_price_per_serving').val()) || 0;
    
    if (pricePic > 0 || priceServing > 0) {
        const revenuePic = pricePic;
        const revenueServing = servingsPerPic * priceServing;
        const difference = revenueServing - revenuePic;
        
        let html = '<div class="alert alert-info">';
        html += '<h6>Revenue Comparison (per PIC):</h6>';
        html += '<p><strong>Sell as PIC:</strong> ' + revenuePic.toLocaleString() + ' TSH</p>';
        html += '<p><strong>Sell by Serving:</strong> ' + revenueServing.toLocaleString() + ' TSH (' + servingsPerPic + ' servings)</p>';
        
        if (difference > 0) {
            html += '<p class="text-success"><strong>💡 Selling by serving generates ' + difference.toLocaleString() + ' TSH more!</strong></p>';
        } else if (difference < 0) {
            html += '<p class="text-primary"><strong>Selling as PIC generates ' + Math.abs(difference).toLocaleString() + ' TSH more</strong></p>';
        } else {
            html += '<p class="text-muted">Both methods generate equal revenue</p>';
        }
        html += '</div>';
        
        $('#profitPreview').html(html);
    }
}

$('#selling_price_per_pic, #selling_price_per_serving, #servings_per_pic').on('input', calculateProfit);

$(document).ready(function() {
    calculateProfit();
});
</script>
@endsection
