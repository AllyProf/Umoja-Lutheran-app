@php
    $stockStatus = $item['current_stock_pics'] <= 0 ? 'critical' : ($item['current_stock_pics'] <= ($item['minimum_stock'] ?? 5) ? 'low' : 'normal');
    $statusColor = $stockStatus === 'critical' ? 'danger' : ($stockStatus === 'low' ? 'warning' : 'success');

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
    }

    $categoryIcons = [
        'spirits' => 'fa-wine-bottle',
        'wines' => 'fa-wine-glass-alt',
        'alcoholic_beverage' => 'fa-beer',
        'non_alcoholic_beverage' => 'fa-glass-whiskey',
        'water' => 'fa-tint',
        'juices' => 'fa-lemon',
        'energy_drinks' => 'fa-bolt',
        'hot_beverages' => 'fa-mug-hot',
        'cocktails' => 'fa-cocktail',
    ];
    $icon = $categoryIcons[$item['product_category']] ?? 'fa-tag';
@endphp

<div class="col-md-4 col-lg-3 mb-4 inventory-card" data-name="{{ strtolower($item['product_name']) }}"
    data-variant="{{ strtolower($item['variant_name']) }}" data-brand="{{ strtolower($item['brand_name'] ?? '') }}"
    data-category="{{ strtolower($item['product_category']) }}">

    <div class="card h-100 border-0 shadow-sm"
        style="transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1); border-radius: 15px; overflow: hidden; background: #fff;"
        onmouseover="this.style.transform='translateY(-8px)'; this.style.boxShadow='0 15px 30px rgba(0,0,0,0.1)';"
        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.05)';">

        <!-- Checkbox Overlay -->
        <div class="position-absolute" style="top: 12px; right: 12px; z-index: 10;">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input stock-checkbox" id="check-{{ $item['variant_id'] }}"
                    value="{{ $item['variant_id'] }}">
                <label class="custom-control-label" for="check-{{ $item['variant_id'] }}"></label>
            </div>
        </div>

        <!-- Image Header -->
        <div class="card-header border-0 p-0 position-relative"
            style="height: 160px; background: #f0f2f5; cursor: pointer;">
            @if($item['product_image'])
                <img src="{{ Storage::url($item['product_image']) }}" alt="{{ $item['product_name'] }}" class="w-100 h-100"
                    style="object-fit: cover;">
            @else
                <div class="d-flex align-items-center justify-content-center h-100"
                    style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);">
                    <i class="fa {{ $icon }} fa-4x text-white opacity-50"></i>
                </div>
            @endif

            <!-- Category Bubble -->
            <div class="position-absolute" style="top: 12px; left: 12px;">
                <span class="badge badge-light shadow-sm px-2 py-1"
                    style="font-size: 10px; text-transform: uppercase; font-weight: 700; color: #555; background: rgba(255,255,255,0.9);">
                    <i class="fa {{ $icon }} mr-1"></i> {{ substr($item['category_name'], 0, 15) }}
                </span>
            </div>

            <!-- Measurement Badge -->
            <div class="position-absolute" style="bottom: 12px; right: 12px;">
                <span class="badge badge-primary shadow-sm px-2 py-1"
                    style="font-size: 11px; background-color: var(--primary-color);">
                    {{ $item['variant_name'] }}
                </span>
            </div>

            <!-- Expiry Warning -->
            @if($isExpiringSoon)
                <div class="position-absolute w-100 text-center"
                    style="bottom: 0; background: rgba(255, 193, 7, 0.9); padding: 2px 0; font-size: 10px; font-weight: bold; color: #000;">
                    <i class="fa fa-clock-o"></i> EXPIRING SOON
                </div>
            @endif
        </div>

        <div class="card-body p-3 d-flex flex-column">
            <!-- Product Info -->
            <div class="mb-3">
                <h5 class="card-title mb-1 font-weight-bold text-dark" style="font-size: 1.05rem; line-height: 1.2;">
                    {{ $item['product_name'] }}
                </h5>
                @if(($item['brand_name'] ?? '') !== $item['product_name'])
                    <p class="small text-muted mb-0 font-italic">{{ $item['brand_name'] }}</p>
                @endif
            </div>

            <!-- Enhanced Stock Bar -->
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small font-weight-bold text-muted">Stock Level</span>
                    <span class="badge badge-{{ $statusColor }} shadow-sm" style="font-size: 10px;">
                        @if(($item['open_servings'] ?? 0) > 0)
                            {{ number_format($item['full_bottles'], 0) }} Bot + {{ $item['open_servings'] }} Gls
                        @else
                            {{ number_format($item['current_stock_pics'], 1) }} PICs
                        @endif
                    </span>
                </div>
                @php
                    $percent = min(100, ($item['current_stock_pics'] / max(1, ($item['minimum_stock'] * 3))) * 100);
                @endphp
                <div class="progress" style="height: 6px; border-radius: 10px;">
                    <div class="progress-bar bg-{{ $statusColor }}" role="progressbar"
                        style="width: {{ $percent }}%; border-radius: 10px;"></div>
                </div>
            </div>

            <!-- Compact Metrics Grid (Only for saleable items) -->
            @php
                $isSaleable = !in_array($item['product_category'], ['supplies', 'cleaning_supplies', 'equipment', 'sauces']);
            @endphp

            @if($isSaleable)
                <div class="row no-gutters mb-3 p-2 bg-light rounded text-center border">
                    <div class="col-6 border-right">
                        <div class="small text-muted mb-0" style="font-size: 9px;">VALUE (Potential)</div>
                        <div class="font-weight-bold text-dark">{{ number_format($item['revenue_serving'], 0) }}</div>
                    </div>
                    <div class="col-6">
                        <div class="small text-muted mb-0" style="font-size: 9px;">PROFIT (Est.)</div>
                        <div class="font-weight-bold text-success">{{ number_format($item['current_profit'], 0) }}</div>
                    </div>
                </div>

                <!-- Price Footer (Modern style) -->
                <div class="mt-auto pt-2 d-flex justify-content-between align-items-center border-top">
                    <div class="small">
                        <div class="text-muted" style="font-size: 10px;">
                            {{ $item['product_category'] === 'drinks' ? 'BOTTLE' : 'UNIT' }} PRICE</div>
                        <div class="font-weight-bold">{{ number_format($item['selling_price_per_pic'], 0) }} TSH</div>
                    </div>
                    @if($item['selling_price_per_serving'] > 0)
                        <div class="small text-right">
                            <div class="text-muted" style="font-size: 10px;">GLASS PRICE</div>
                            <div class="font-weight-bold">{{ number_format($item['selling_price_per_serving'], 0) }} TSH</div>
                        </div>
                    @endif
                </div>
            @else
                <div class="mt-auto pt-2 text-center border-top">
                    <span class="badge badge-light border text-muted px-3 py-2">INVENTORY ONLY ITEM</span>
                </div>
            @endif
        </div>

        <div class="card-footer bg-white border-top-0 p-3">
            <div class="btn-group w-100 shadow-sm border rounded overflow-hidden">
                <button class="btn btn-sm btn-white text-primary py-2 border-0 view-track-btn"
                    data-variant-id="{{ $item['variant_id'] }}"
                    data-item-name="{{ $item['product_name'] }} ({{ $item['variant_name'] }})" title="History"
                    style="flex: 1; border-right: 1px solid #eee !important;">
                    <i class="fa fa-history"></i>
                </button>
                <button class="btn btn-sm btn-white text-info py-2 border-0 settings-stock-btn"
                    data-variant-id="{{ $item['variant_id'] }}"
                    data-item-name="{{ $item['product_name'] }} ({{ $item['variant_name'] }})"
                    data-minimum-stock="{{ $item['minimum_stock'] ?? 0 }}"
                    data-price-pic="{{ $item['selling_price_per_pic'] }}"
                    data-price-glass="{{ $item['selling_price_per_serving'] }}" title="Settings"
                    style="flex: 1; border-right: 1px solid #eee !important;">
                    <i class="fa fa-cog"></i>
                </button>
                <a href="{{ route('bar-keeper.purchase-requests.create', ['ids' => $item['variant_id']]) }}"
                    class="btn btn-sm btn-white text-warning py-2 border-0" title="Restock" style="flex: 1;">
                    <i class="fa fa-plus-circle"></i>
                </a>
            </div>
        </div>
    </div>
</div>