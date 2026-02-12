@php
    $stockStatus = $item['current_stock_pics'] <= 0 ? 'critical' : ($item['current_stock_pics'] <= ($item['minimum_stock'] ?? 5) ? 'low' : 'normal');
    $statusColor = $stockStatus === 'critical' ? 'danger' : ($stockStatus === 'low' ? 'warning' : 'success');
    $borderClass = $stockStatus === 'critical' ? 'border-danger' : ($stockStatus === 'low' ? 'border-warning' : 'border-success');
    $headerClass = $stockStatus === 'critical' ? 'bg-danger text-white' : ($stockStatus === 'low' ? 'bg-warning text-dark' : 'bg-success text-white');
    
    $soldPics = $item['total_sold_pics'];
    $currentPics = $item['current_stock_pics'];
    $receivedPics = $item['total_received_pics'];
    // Expiry Logic
    $expiryDate = $item['nearest_expiry'] ?? null;
    $daysToExpiry = null;
    $isExpiringSoon = false;
    
    if ($expiryDate) {
        $daysToExpiry = now()->startOfDay()->diffInDays($expiryDate, false);
        $isExpiringSoon = $daysToExpiry <= 10;
        
        // Override styling if expiring soon and not already critical
        if ($isExpiringSoon && $stockStatus !== 'critical') {
            $statusColor = 'warning';
            $borderClass = 'border-warning shadow';
            $headerClass = 'bg-warning text-dark';
            // Use a specific orange flash if very close
            if ($daysToExpiry <= 3) {
                $headerClass = 'bg-danger text-white animated pulse infinite';
                $borderClass = 'border-danger shadow-lg';
            }
        }
    }
@endphp

<div class="col-md-4 col-sm-6 mb-3 inventory-card" 
     data-name="{{ strtolower($item['product_name']) }}" 
     data-variant="{{ strtolower($item['variant_name']) }}"
     data-brand="{{ strtolower($item['brand_name'] ?? '') }}"
     data-category="{{ strtolower($item['product_category']) }}">
    
    <div class="card h-100 {{ $borderClass }}" style="box-shadow: 0 2px 4px rgba(0,0,0,0.1); border-width: 2px !important; {{ $isExpiringSoon && $daysToExpiry > 0 ? 'background-color: #fff9f0;' : '' }}">
        <div class="card-header {{ $headerClass }} py-2">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="custom-control custom-checkbox mr-2">
                        <input type="checkbox" class="custom-control-input stock-checkbox" id="check-{{ $item['variant_id'] }}" value="{{ $item['variant_id'] }}">
                        <label class="custom-control-label" for="check-{{ $item['variant_id'] }}"></label>
                    </div>
                    <div class="d-flex flex-column">
                        <h6 class="card-title mb-0 text-truncate font-weight-bold" title="{{ $item['product_name'] }}" style="max-width: 151px; font-size: 13px;">
                            <i class="fa fa-wine-bottle"></i> {{ $item['product_name'] }}
                        </h6>
                        @if(($item['brand_name'] ?? '') !== $item['product_name'])
                        <small class="font-italic" style="font-size: 10px; line-height: 1; opacity: 0.75;">{{ $item['brand_name'] }}</small>
                        @endif
                    </div>
                </div>
                <div class="btn-group">
                    <button class="btn btn-sm btn-info view-track-btn" 
                            data-variant-id="{{ $item['variant_id'] }}" 
                            data-product-id="{{ $item['product_id'] }}"
                            data-item-name="{{ $item['product_name'] }} ({{ $item['variant_name'] }})"
                            title="Track History & Price Changes"
                            style="background: #17a2b8; border-color: #17a2b8; color: white;">
                        <i class="fa fa-history"></i>
                    </button>
                    <button class="btn btn-sm btn-light settings-stock-btn" 
                            data-variant-id="{{ $item['variant_id'] }}" 
                            data-item-name="{{ $item['product_name'] }} ({{ $item['variant_name'] }})" 
                            data-minimum-stock="{{ $item['minimum_stock'] ?? 0 }}"
                            data-price-pic="{{ $item['selling_price_per_pic'] }}"
                            data-price-glass="{{ $item['selling_price_per_serving'] }}"
                            title="Update Prices & Thresholds">
                        <i class="fa fa-cog"></i>
                    </button>
                    <a href="{{ route('bar-keeper.purchase-requests.create', ['ids' => $item['variant_id']]) }}" 
                       class="btn btn-sm btn-warning" 
                       title="Request Restock"
                       style="background: #e77a31; border-color: #e77a31; color: white;">
                        <i class="fa fa-plus-circle"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="card-body p-0">
            <!-- Product Image Header -->
            <div class="position-relative" style="height: 150px; background: #f0f2f5; overflow: hidden; border-bottom: 1px solid #eee;">
                @if($item['product_image'])
                    <img src="{{ Storage::url($item['product_image']) }}" alt="{{ $item['product_name'] }}" class="w-100 h-100" style="object-fit: cover;">
                @else
                    <div class="d-flex align-items-center justify-content-center h-100" style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);">
                        <i class="fa fa-wine-glass fa-4x text-white opacity-50"></i>
                    </div>
                @endif
                
                <!-- Measurement Badge -->
                <div class="position-absolute" style="bottom: 10px; right: 10px;">
                    <span class="badge badge-primary shadow-sm px-2 py-1" style="font-size: 11px;">
                        {{ $item['variant_name'] }}
                    </span>
                </div>

                <!-- Category Bubble -->
                <div class="position-absolute" style="top: 10px; left: 10px;">
                    <span class="badge badge-light shadow-sm px-2 py-1" style="font-size: 10px; text-transform: uppercase; font-weight: 700; color: #555; background: rgba(255,255,255,0.9);">
                        {{ substr($item['category_name'], 0, 15) }}
                    </span>
                </div>
            </div>

            <div class="p-3">
                <!-- Stats Grid -->
                <div class="mb-3 p-2 bg-light rounded border shadow-sm">
                    <div class="row text-center" style="font-size: 11px;">
                        <div class="col-4 border-right">
                            <div class="text-info font-weight-bold" style="font-size: 14px;">{{ number_format($receivedPics, 1) }}</div>
                            <div class="text-muted" style="font-size: 10px;">Total Recv.</div>
                        </div>
                        <div class="col-4 border-right">
                            <div class="text-danger font-weight-bold" style="font-size: 14px;">
                                @if(($item['sold_servings'] ?? 0) > 0)
                                    {{ number_format($item['sold_full_bottles'], 0) }} <small class="text-muted" style="font-size: 10px;">+ {{ $item['sold_servings'] }} gls</small>
                                @else
                                    {{ number_format($item['total_sold_pics'], 1) }}
                                @endif
                            </div>
                            <div class="text-muted" style="font-size: 10px;">Sold</div>
                        </div>
                        <div class="col-4">
                            <div class="text-{{ $statusColor }} font-weight-bold" style="font-size: 14px;">
                                @if(($item['open_servings'] ?? 0) > 0)
                                    {{ number_format($item['full_bottles'], 0) }} <small class="text-muted" style="font-size: 10px;">+ {{ $item['open_servings'] }} gls</small>
                                @else
                                    {{ number_format($item['current_stock_pics'], 1) }}
                                @endif
                            </div>
                            <div class="text-muted" style="font-size: 10px;">In Stock</div>
                        </div>
                    </div>
                </div>

                <!-- Unit Conversion & Total Servings Info -->
                @if(($item['servings_per_pic'] ?? 1) > 1)
                <div class="mb-3 px-2 py-1 bg-white border rounded" style="font-size: 10.5px; color: #444; border-style: dashed !important; border-color: #dee2e6 !important;">
                        <div class="d-flex justify-content-between mb-1">
                        <span><i class="fa fa-exchange text-info"></i> Ratio:</span>
                        <strong>1 Bot = {{ $item['servings_per_pic'] }} gls</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                        <span><i class="fa fa-glass-whiskey text-warning"></i> Total Gls:</span>
                        <strong class="text-primary">{{ number_format($item['total_servings_available']) }} Glasses</strong>
                        </div>
                        <div class="d-flex justify-content-between pt-1 border-top" style="border-top-style: dotted !important;">
                        <span class="text-success"><i class="fa fa-money-bill-wave"></i> Profit/Gls:</span>
                        <strong class="text-success">{{ number_format($item['profit_per_serving'] ?? 0) }} <small>TSH</small></strong>
                        </div>
                </div>
                @endif
                
                <!-- Financial Stats -->
                <div class="mb-1 p-2 bg-light rounded shadow-sm border">
                    <div class="d-flex justify-content-between align-items-center p-1 rounded mb-1" style="font-size: 11px; background: #e3f2fd; border: 1px dashed #2196f3;">
                        <span class="font-weight-bold text-primary">Amount Generated:</span>
                        <strong class="text-primary" style="font-size: 12px;">{{ number_format($item['revenue_generated'], 0) }} <small>TSH</small></strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center" style="font-size: 11px;">
                        <span class="text-muted">Stock Value:</span>
                        <strong class="text-warning" style="color: #e77a31 !important;">{{ number_format($item['revenue_serving'], 0) }} <small>TSH</small></strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center border-top mt-1 pt-1" style="font-size: 11px;">
                        <span class="text-muted">Profit Generated:</span>
                        <strong class="text-info">{{ number_format($item['profit_generated'] ?? 0, 0) }} <small>TSH</small></strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-1" style="font-size: 11px;">
                        <span class="text-muted">Buying Price (Avg):</span>
                        <strong class="{{ $item['unit_cost'] > $item['selling_price_per_pic'] ? 'text-danger' : 'text-dark' }}">
                            {{ number_format($item['unit_cost'], 0) }} <small>TSH</small>
                            @if($item['unit_cost'] > $item['selling_price_per_pic'])
                                <i class="fa fa-arrow-up text-danger" title="Cost is higher than Selling Price!"></i>
                            @endif
                        </strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-1" style="font-size: 11px;">
                        <span class="text-muted">Profit Potential:</span>
                        <strong class="text-{{ $item['current_profit'] < 0 ? 'danger' : 'success' }}">{{ number_format($item['current_profit'], 0) }} <small>TSH</small></strong>
                    </div>
                </div>
            
            <!-- Expiry & Status Badges -->
            <div class="mt-2">
                @if($expiryDate)
                    @if($daysToExpiry < 0)
                        <div class="badge badge-dark w-100 py-1 mb-1" style="font-size: 10px;">
                            <i class="fa fa-times-circle"></i> EXPIRED: {{ $expiryDate->format('d M Y') }}
                        </div>
                    @elseif($daysToExpiry <= 10)
                        <div class="badge badge-warning w-100 py-1 mb-1 text-dark" style="font-size: 10px; background-color: #ffc107;">
                            <i class="fa fa-clock-o"></i> EXPIRING: {{ $daysToExpiry }} Days Left ({{ $expiryDate->format('d M') }})
                        </div>
                    @else
                        <div class="badge badge-light border w-100 py-1 mb-1 text-muted" style="font-size: 10px;">
                            <i class="fa fa-calendar"></i> Expiry: {{ $expiryDate->format('d M Y') }}
                        </div>
                    @endif
                @endif

                @if($stockStatus === 'critical')
                    <span class="badge badge-danger w-100 py-1" style="font-size: 11px;"><i class="fa fa-exclamation-circle"></i> Out of Stock</span>
                @elseif($stockStatus === 'low')
                    <span class="badge badge-warning w-100 py-1" style="font-size: 11px;"><i class="fa fa-warning"></i> Low Stock - Reorder Soon</span>
                @else
                    <span class="badge badge-success w-100 py-1" style="font-size: 11px;"><i class="fa fa-check-circle"></i> In Stock</span>
                @endif
            </div>
        </div>
    </div>
        
    <div class="card-footer bg-white p-2" style="font-size: 10px;">
            <div class="d-flex justify-content-between text-muted">
                <span><i class="fa fa-wine-bottle"></i> Pic: <strong>{{ number_format($item['selling_price_per_pic'], 0) }} Tsh Price</strong></span>
                
                @if($item['selling_price_per_serving'] > 0)
                <span><i class="fa fa-glass-martini"></i> Glass: <strong>{{ number_format($item['selling_price_per_serving'], 0) }} Tsh Price</strong></span>
                @else
                <span><i class="fa fa-ban text-light-gray"></i> No Glass</span>
                @endif
            </div>
        </div>
    </div>
</div>
