@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-exchange"></i> Transfer Items to Departments</h1>
        <p>Distribute purchased items to requesting departments</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.restaurants.shopping-list.index') }}">Shopping Lists</a></li>
        <li class="breadcrumb-item active"><a href="#">Transfer</a></li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <form action="{{ route('admin.restaurants.shopping-list.process-transfer', $shoppingList->id) }}" method="POST" id="transferForm">
            @csrf
            <div class="tile">
                <div class="tile-title-w-btn">
                    <h3 class="title">Shopping List: {{ $shoppingList->name }}</h3>
                    <p>
                        <button class="btn btn-primary" type="button" onclick="window.print();"><i class="fa fa-print"></i> Print Transfer Note</button>
                    </p>
                </div>
                <div class="tile-body">
                    @if(count($itemsByDepartment) > 0)
                        @php
                            $totalItems = 0;
                            $totalCost = 0;
                            foreach ($itemsByDepartment as $items) {
                                foreach ($items as $item) {
                                    if ($item->is_found && $item->purchased_quantity > 0) {
                                        $totalItems++;
                                        $totalCost += $item->purchased_cost ?? 0;
                                    }
                                }
                            }
                        @endphp
                        <div class="alert alert-success mb-4">
                            <h5><i class="fa fa-info-circle"></i> Distribution Summary</h5>
                            <p class="mb-0">
                                <strong>Total Items:</strong> {{ $totalItems }} | 
                                <strong>Total Cost:</strong> {{ number_format($totalCost, 0) }} TZS | 
                                <strong>Departments:</strong> {{ count($itemsByDepartment) }}
                            </p>
                        </div>
                        @foreach($itemsByDepartment as $department => $items)
                        <div class="department-group mb-4" style="border: 1px solid #dee2e6; border-radius: 8px; padding: 10px; background: #f8f9fa;">
                            @php
                                $deptTotalCost = 0;
                                $deptItemCount = 0;
                                foreach ($items as $item) {
                                    if ($item->is_found && $item->purchased_quantity > 0) {
                                        $deptTotalCost += $item->purchased_cost ?? 0;
                                        $deptItemCount++;
                                    }
                                }
                            @endphp
                            <h4 class="mb-3" style="color: #333; border-bottom: 2px solid #333; padding-bottom: 10px;">
                                <i class="fa fa-building"></i> {{ $department }} Department
                                <span class="badge badge-primary float-right">{{ $deptItemCount }} item(s) - {{ number_format($deptTotalCost, 0) }} TZS</span>
                            </h4>
                            
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th class="vertical-align-middle">Item Name</th>
                                            <th class="vertical-align-middle">Category</th>
                                            <th class="text-center vertical-align-middle">Qty</th>
                                            <th class="text-center vertical-align-middle">Unit</th>
                                            <th class="text-right vertical-align-middle">Unit Price</th>
                                            <th class="text-right vertical-align-middle">Total Cost</th>
                                            <th class="vertical-align-middle" width="25%">Selling Configuration</th>
                                            <th class="text-center vertical-align-middle">Transfer Qty</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($items as $item)
                                        @if($item->is_found && $item->purchased_quantity > 0)
                                        <tr class="item-row" data-department="{{ $department }}">
                                            <td class="vertical-align-middle"><strong>{{ $item->product_name }}</strong></td>
                                            <td class="vertical-align-middle"><span class="badge badge-light border">{{ $item->category_name }}</span></td>
                                            <td class="text-center vertical-align-middle">{{ number_format($item->purchased_quantity, 0) }}</td>
                                            <td class="text-center vertical-align-middle">{{ $item->unit == 'bottles' ? 'PIC' : $item->unit }}</td>
                                            <td class="text-right vertical-align-middle">{{ number_format(round($item->unit_price ?? 0), 0) }} <small class="text-muted">TZS</small></td>
                                            <td class="text-right vertical-align-middle">{{ number_format(round($item->purchased_cost ?? 0), 0) }} <small class="text-muted">TZS</small></td>
                                            <td class="p-1 vertical-align-middle">
                                                @if(strtolower($department) == 'bar')
                                                <div class="card border-0 m-0 shadow-sm {{ $item->productVariant ? 'bg-light' : '' }}" style="border-radius: 8px; overflow: hidden;">
                                                    <div class="card-body p-2 {{ $item->productVariant ? 'bg-light' : 'bg-white' }}">
                                                        <input type="hidden" class="unit-cost" value="{{ $item->unit_price ?? 0 }}">
                                                        
                                                        @if($item->productVariant && ($item->productVariant->selling_price_per_pic > 0 || $item->productVariant->selling_price_per_serving > 0))
                                                            <div class="mb-2 p-1 bg-white border border-success rounded d-flex align-items-center" style="font-size: 10px; color: #155724;">
                                                                <i class="fa fa-check-circle mr-1"></i> 
                                                                <span><strong>Synced:</strong> {{ $item->productVariant->servings_per_pic }} {{ $item->productVariant->selling_unit_name }}s/Bottle</span>
                                                            </div>
                                                        @else
                                                            <div class="mb-2 p-1 bg-warning-light border border-warning rounded" style="font-size: 10px; background: #fff3cd; color: #856404;">
                                                                <i class="fa fa-warning mr-1"></i> <strong>New Item:</strong> Please configure
                                                            </div>
                                                        @endif

                                                        <div class="mb-2">
                                                            <label class="small text-muted mb-0 font-weight-bold">Selling Method</label>
                                                            <select class="form-control form-control-sm selling-method" name="transfers[{{ $item->id }}][selling_method]" style="border-radius: 4px; font-size: 11px; height: 30px;">
                                                                <option value="pic" {{ ($item->productVariant?->can_sell_as_pic && !$item->productVariant?->can_sell_as_serving) ? 'selected' : '' }}>Bottle Only</option>
                                                                <option value="serving" {{ (!$item->productVariant?->can_sell_as_pic && $item->productVariant?->can_sell_as_serving) ? 'selected' : '' }}>Glass/Tot Only</option>
                                                                <option value="mixed" {{ ($item->productVariant?->can_sell_as_pic && $item->productVariant?->can_sell_as_serving) ? 'selected' : ($item->product_variant_id ? '' : 'selected') }}>Bottle & Glass (Mixed)</option>
                                                            </select>
                                                        </div>

                                                        <div class="config-fields-serving" style="display: none;">
                                                            <div class="row no-gutters mb-1">
                                                                <div class="col-6 pr-1">
                                                                    <label class="small text-muted mb-0">Qty/Bottle</label>
                                                                    <input type="number" class="form-control form-control-sm servings-per-pic" name="transfers[{{ $item->id }}][servings_per_pic]" value="{{ $item->productVariant?->servings_per_pic ?? 12 }}" min="1">
                                                                </div>
                                                                <div class="col-6">
                                                                    <label class="small text-muted mb-0">Unit</label>
                                                                    <select class="form-control form-control-sm selling-unit" name="transfers[{{ $item->id }}][selling_unit]">
                                                                        <option value="pic" {{ ($item->productVariant?->selling_unit ?? '') == 'pic' ? 'selected' : '' }}>PIC</option>
                                                                        <option value="glass" {{ ($item->productVariant?->selling_unit ?? '') == 'glass' ? 'selected' : 'selected' }}>Glass</option>
                                                                        <option value="tot" {{ ($item->productVariant?->selling_unit ?? '') == 'tot' ? 'selected' : '' }}>Tot</option>
                                                                        <option value="shot" {{ ($item->productVariant?->selling_unit ?? '') == 'shot' ? 'selected' : '' }}>Shot</option>
                                                                        <option value="cocktail" {{ ($item->productVariant?->selling_unit ?? '') == 'cocktail' ? 'selected' : '' }}>Cocktail</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="row no-gutters">
                                                            <div class="col-6 pr-1 config-fields-pic">
                                                                <label class="small text-muted mb-0">Price/Bottle</label>
                                                                <input type="number" step="1" class="form-control form-control-sm selling-price-pic" name="transfers[{{ $item->id }}][selling_price_per_pic]" value="{{ $item->productVariant && $item->productVariant->selling_price_per_pic > 0 ? round($item->productVariant->selling_price_per_pic) : '' }}" placeholder="Price">
                                                            </div>
                                                            <div class="col-6 text-right config-fields-serving" style="display: none;">
                                                                <label class="small text-muted mb-0">Price/Glass</label>
                                                                <input type="number" step="1" class="form-control form-control-sm selling-price-serving" name="transfers[{{ $item->id }}][selling_price_per_serving]" value="{{ $item->productVariant && $item->productVariant->selling_price_per_serving > 0 ? round($item->productVariant->selling_price_per_serving) : '' }}" placeholder="Price">
                                                            </div>
                                                        </div>

                                                        <div class="profit-preview small p-1 mt-2 border rounded bg-white text-center" style="font-size: 10px; line-height: 1.2; box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);">
                                                            <span class="text-muted italic">Awaiting inputs...</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                @else
                                                <div class="text-center text-muted small mt-2">N/A</div>
                                                @endif
                                            </td>
                                            <td class="vertical-align-middle">
                                                <input type="hidden" name="transfers[{{ $item->id }}][item_id]" value="{{ $item->id }}">
                                                <input type="hidden" name="transfers[{{ $item->id }}][department]" value="{{ $department }}">
                                                <input type="number" step="1" class="form-control transfer-quantity text-center" 
                                                       name="transfers[{{ $item->id }}][quantity]" 
                                                       value="{{ round($item->purchased_quantity) }}" 
                                                       max="{{ round($item->purchased_quantity) }}" 
                                                       min="1" required>
                                            </td>
                                        </tr>
                                        @endif
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr style="background-color: #f8f9fa; font-weight: bold;">
                                            <td colspan="5" style="text-align: right; border-top: 2px solid #333;">Department Total:</td>
                                            <td colspan="3" style="color: #333; font-size: 16px; border-top: 2px solid #333;">
                                                {{ number_format($deptTotalCost, 0) }} <small>TZS</small>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i> No items found to transfer. Make sure items are marked as found and have purchased quantities.
                        </div>
                    @endif
                </div>
                <div class="tile-footer">
                    <button class="btn btn-primary" type="submit"><i class="fa fa-check-circle"></i> Complete Transfer</button>
                    <a class="btn btn-secondary" href="{{ route('admin.restaurants.shopping-list.index') }}"><i class="fa fa-times-circle"></i> Cancel</a>
                </div>
                <div class="alert alert-info mt-3">
                    <h6><i class="fa fa-info-circle"></i> <strong>Next Steps:</strong></h6>
                    <p class="mb-0">
                        After completing the transfer, department staff can receive items by going to their <strong>"My Requests"</strong> page:
                        <ul class="mb-0 mt-2">
                            <li><strong>Housekeeping:</strong> Purchase Requests → My Requests</li>
                            <li><strong>Reception:</strong> Purchase Requests → My Requests</li>
                            <li><strong>Bar:</strong> Purchase Requests → My Requests</li>
                            <li><strong>Food:</strong> Purchase Requests → My Requests</li>
                        </ul>
                    </p>
                </div>
            </div>
        </form>
    </div>
</div>

<style media="print">
    @page { 
        size: A4; 
        margin: 15mm;
    }
    .app-header, .app-sidebar, .app-title, .tile-title-w-btn, .tile-footer, .d-print-none {
        display: none !important;
    }
    .tile {
        border: none !important;
        box-shadow: none !important;
    }
    body {
        font-size: 12pt;
    }
    .department-group {
        page-break-inside: avoid;
        margin-bottom: 20px;
    }
    table {
        font-size: 11pt;
    }
    @media print {
        .transfer-note-header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .transfer-note-header h2 {
            margin: 0;
            font-size: 18pt;
        }
        .transfer-note-header p {
            margin: 5px 0;
            font-size: 11pt;
        }
        .transfer-note-footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10pt;
            border-top: 1px solid #000;
            padding-top: 10px;
        }
    }
</style>
<div class="transfer-note-header d-print-block" style="display: none;">
    <h2>PRIMELAND HOTEL</h2>
    <p><strong>TRANSFER NOTE</strong></p>
    <p>Date: {{ date('d M Y') }} | Ref: SL-{{ $shoppingList->id }}</p>
    <p>Shopping List: {{ $shoppingList->name }}</p>
</div>
<div class="transfer-note-footer d-print-block" style="display: none;">
    <p>Generated on {{ date('d M Y H:i') }} | PrimeLand Hotel - Kitchen Management System</p>
    <p>Powered By EmCa Technologies</p>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Toggle fields based on selling method
    function toggleMethodFields() {
        $('.item-row').each(function() {
            var row = $(this);
            var method = row.find('.selling-method').val();
            
            if (method === 'pic') {
                row.find('.config-fields-pic').show();
                row.find('.config-fields-serving').hide();
            } else if (method === 'serving') {
                row.find('.config-fields-pic').hide();
                row.find('.config-fields-serving').show();
            } else {
                row.find('.config-fields-pic').show();
                row.find('.config-fields-serving').show();
            }
        });
    }

    // Real-time Profit Calculation
    function calculateProfits() {
        $('.table tbody tr').each(function() {
            var row = $(this);
            var transferQty = parseFloat(row.find('.transfer-quantity').val()) || 0;
            var unitCost = parseFloat(row.find('.unit-cost').val()) || 0;
            var totalTransferredCost = transferQty * unitCost;
            
            var method = row.find('.selling-method').val();
            var servingsPerPic = parseInt(row.find('.servings-per-pic').val()) || 1;
            var totalServings = transferQty * servingsPerPic;
            var sellUnit = row.find('.selling-unit option:selected').text();
            
            var pricePic = parseFloat(row.find('.selling-price-pic').val()) || 0;
            var priceServing = parseFloat(row.find('.selling-price-serving').val()) || 0;
            
            var html = '<div class="border-bottom pb-1 mb-1">';
            var hasOutput = false;

            // PIC Calculation
            if ((method === 'pic' || method === 'mixed') && pricePic > 0) {
                var profitPerPic = pricePic - unitCost;
                var totalProfitPic = profitPerPic * transferQty;
                
                html += '<div class="d-flex justify-content-between"><span><strong>PIC Sales</strong> (Total):</span> <strong>' + Math.round(pricePic * transferQty).toLocaleString() + '</strong></div>';
                html += '<div class="d-flex justify-content-between text-muted" style="font-size: 9px;"><span>Profit per PC:</span> <span>' + Math.round(profitPerPic).toLocaleString() + '</span></div>';
                html += '<div class="d-flex justify-content-between text-info" style="font-size: 9px;"><span>Total Profit:</span> <span>' + Math.round(totalProfitPic).toLocaleString() + '</span></div>';
                hasOutput = true;
            }

            // Serving Calculation
            if ((method === 'serving' || method === 'mixed') && priceServing > 0) {
                if (hasOutput) html += '<div class="mt-2 border-top pt-1"></div>';
                
                var costPerServing = unitCost / servingsPerPic;
                var profitPerServing = priceServing - costPerServing;
                var totalProfitServing = profitPerServing * totalServings;
                
                html += '<div class="d-flex justify-content-between"><span><strong>' + sellUnit + ' Sales</strong> (Total):</span> <strong>' + Math.round(priceServing * totalServings).toLocaleString() + '</strong></div>';
                html += '<div class="d-flex justify-content-between text-muted" style="font-size: 9px;"><span>Pieces/Qty:</span> <span>' + (totalServings % 1 === 0 ? totalServings : totalServings.toFixed(1)) + ' ' + sellUnit + 's</span></div>';
                html += '<div class="d-flex justify-content-between text-muted" style="font-size: 9px;"><span>Profit per ' + sellUnit + ':</span> <span>' + Math.round(profitPerServing).toLocaleString() + '</span></div>';
                html += '<div class="d-flex justify-content-between text-info" style="font-size: 9px;"><span>Total Profit:</span> <span>' + Math.round(totalProfitServing).toLocaleString() + '</span></div>';
                hasOutput = true;
            }
            
            html += '</div>';
            
            if (method === 'mixed' && pricePic > 0 && priceServing > 0) {
                var totalProfitPic = (pricePic - unitCost) * transferQty;
                var totalProfitServing = (priceServing - (unitCost / servingsPerPic)) * totalServings;
                var diff = totalProfitServing - totalProfitPic;
                
                if (diff > 0) {
                    html += '<div class="text-success font-weight-bold">💡 Sell ' + sellUnit + ' to make +' + Math.round(diff).toLocaleString() + ' extra </div>';
                } else if (diff < 0) {
                     html += '<div class="text-primary font-weight-bold">💡 Sell PIC to make +' + Math.round(Math.abs(diff)).toLocaleString() + ' extra</div>';
                }
            }
            
            if (hasOutput) {
                row.find('.profit-preview').html(html);
            } else {
                row.find('.profit-preview').html('<span class="text-muted">Enter price to generate profit</span>');
            }
        });
    }

    // Bind events
    $(document).on('change', '.selling-method', function() {
        toggleMethodFields();
        calculateProfits();
    });
    
    $(document).on('input', '.transfer-quantity, .selling-price-pic, .selling-price-serving, .servings-per-pic', calculateProfits);
    $(document).on('change', '.selling-unit', calculateProfits);
    
    // Initial calls
    toggleMethodFields();
    calculateProfits();

    // Bind profit calculation to inputs
    $(document).on('input', '.transfer-quantity, .selling-price-pic, .selling-price-serving, .servings-per-pic', calculateProfits);
    
    // Initial calculation
    calculateProfits();

    // Validate transfer quantities don't exceed purchased quantities
    $('#transferForm').on('submit', function(e) {
        var isValid = true;
        $('.transfer-quantity').each(function() {
            var max = parseFloat($(this).attr('max'));
            var value = parseFloat($(this).val());
            if (value > max) {
                alert('Transfer quantity cannot exceed purchased quantity for ' + $(this).closest('tr').find('td:first').text());
                isValid = false;
                return false;
            }
        });
        return isValid;
    });
});
</script>
@endsection
