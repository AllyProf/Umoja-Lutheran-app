@extends('dashboard.layouts.app')

@section('content')
    <div class="app-title">
        <div>
            <h1><i class="fa fa-cubes"></i> Main Store Inventory</h1>
            <p>Overview of all products and stock levels in the main store</p>
        </div>
        <ul class="app-breadcrumb breadcrumb mb-0">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="{{ route('storekeeper.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item">Inventory</li>
        </ul>
    </div>

    <!-- Inventory Stats -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-danger text-white border-0 shadow-sm rounded-lg overflow-hidden h-100">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="mr-4 bg-white-translucent p-3 rounded">
                        <i class="fa fa-cubes fa-3x"></i>
                    </div>
                    <div>
                        <div class="text-uppercase small font-weight-bold opacity-75">Total Items</div>
                        <div class="h2 font-weight-bold mb-0">{{ $totalItems ?? $products->total() }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-dark border-0 shadow-sm rounded-lg overflow-hidden h-100">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="mr-4 bg-white-translucent p-3 rounded text-warning">
                        <i class="fa fa-warning fa-3x"></i>
                    </div>
                    <div>
                        <div class="text-uppercase small font-weight-bold opacity-75 text-dark">Low Stock Items</div>
                        <div class="h2 font-weight-bold mb-0">{{ $lowStockCount ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tile border-0 shadow-sm rounded-lg">
        <div class="tile-title-w-btn border-bottom pb-4 mb-4">
            <h3 class="title">Inventory Stock</h3>
            <div class="btn-group">
                <a href="{{ route('storekeeper.stock-receipts.create') }}" class="btn btn-success rounded-pill px-4 mr-2">
                    <i class="fa fa-plus"></i> Receive New Stock
                </a>
                <button class="btn btn-outline-secondary rounded-pill px-3 mr-2 active" id="btnGridView">
                    <i class="fa fa-th-large"></i>
                </button>
                <button class="btn btn-outline-secondary rounded-pill px-3" id="btnListView">
                    <i class="fa fa-list"></i>
                </button>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <form action="{{ route('storekeeper.inventory') }}" method="GET" id="searchForm">
                    <div class="input-group search-box">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-right-0"><i class="fa fa-search text-muted"></i></span>
                        </div>
                        <input type="text" name="search" class="form-control border-left-0" placeholder="Search by name..." value="{{ request('search') }}">
                    </div>
                </form>
            </div>
            <div class="col-md-8">
                <div class="d-flex flex-wrap align-items-center justify-content-md-end">
                    <span class="mr-3 font-weight-bold text-muted small text-uppercase">Quick Filters:</span>
                    <div class="filter-buttons">
                        <a href="{{ route('storekeeper.inventory') }}" class="btn btn-sm {{ !request('category') ? 'btn-danger' : 'btn-outline-danger' }} rounded-pill px-3 m-1">ALL ITEMS</a>
                        @foreach($categories as $cat)
                            <a href="{{ route('storekeeper.inventory', ['category' => $cat]) }}" 
                               class="btn btn-sm {{ request('category') == $cat ? 'btn-danger' : 'btn-outline-danger' }} rounded-pill px-3 m-1">
                                {{ $cat === 'non_alcoholic_beverage' ? 'SOFT DRINKS' : ($cat === 'cleaning_supplies' ? 'HOUSEKEEPING' : strtoupper(str_replace('_', ' ', $cat))) }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Cards Grid -->
        @if($products->count() > 0)
            <div class="row row-grid" id="inventoryGrid">
                @foreach($products as $product)
                    @foreach($product->variants as $variant)
                        @php
                            $totalStock = (float) $variant->current_stock;
                            $itemsPerPackage = $variant->items_per_package > 0 ? $variant->items_per_package : 1;
                            $packages = floor($totalStock / $itemsPerPackage);
                            $remItems = $totalStock % $itemsPerPackage;
                            $isLowStock = $variant->isLowStock($totalStock);
                        @endphp
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 product-card border shadow-sm {{ $isLowStock ? 'border-danger' : '' }}" style="{{ $isLowStock ? 'border-left: 5px solid #dc3545 !important; background-color: #fff8f8;' : '' }}">
                                <div class="card-body p-4">
                                    @if($isLowStock)
                                        <div class="position-absolute" style="top: 10px; right: 10px; z-index: 10;">
                                            <span class="badge badge-danger shadow-sm px-2 py-1" style="font-size: 10px; letter-spacing: 0.5px;">
                                                <i class="fa fa-warning mr-1"></i> INSUFFICIENT STOCK
                                            </span>
                                        </div>
                                    @endif
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div style="max-width: {{ $isLowStock ? '70%' : '100%' }};">
                                            <h5 class="product-title mb-1 font-weight-bold" style="color: #900;">
                                                @php
                                                    $vName = $product->category === 'cleaning_supplies' ? trim(str_ireplace(['(ml)', 'ml', '(l)', 'l'], '', $variant->variant_name)) : $variant->variant_name;
                                                @endphp
                                                @if(strtolower($vName) === 'standard' || strtolower($vName) === strtolower($product->name) || empty($vName))
                                                    {{ $product->name }}
                                                @else
                                                    {{ $vName }}
                                                @endif
                                            </h5>
                                            <div class="text-muted small">
                                                {{ ucfirst($product->category_name) }}
                                            </div>
                                        </div>
                                        @if($product->category !== 'food' && $product->category !== 'cleaning_supplies' && $variant->measurement && !in_array(strtolower(trim($variant->measurement)), ['ml', '0 ml', '0ml', '0']))
                                        <span class="badge badge-secondary px-2 py-1">{{ $variant->measurement }}</span>
                                        @endif
                                    </div>

                                        <div class="inventory-levels mb-3">
                                            @php
                                                $beverageCategories = ['spirits', 'wines', 'alcoholic_beverage', 'non_alcoholic_beverage', 'energy_drinks', 'water', 'juices', 'hot_beverages', 'cocktails', 'soda', 'soft_drinks'];
                                                $isBeverage = in_array($product->category, $beverageCategories);
                                                
                                                // Robust unit detection
                                                $pUnit = $variant->purchasing_unit ?: ($variant->packaging ?: 'Package');
                                                $rUnit = $variant->receiving_unit ?: 'pcs';
                                                
                                                // Handle potential user swap (if Crate is the 'receiving' unit but it's the larger one)
                                                if ($isBeverage && strtolower($rUnit) === 'crate' && strtolower($pUnit) === 'bottle') {
                                                    $temp = $pUnit; $pUnit = $rUnit; $rUnit = $temp;
                                                }
                                            @endphp

                                            <div class="d-flex justify-content-between py-2 border-bottom">
                                                <span class="text-muted font-weight-bold small text-uppercase">Stock Level:</span>
                                                <span class="font-weight-bold {{ $isLowStock ? 'text-danger' : 'text-success' }}">
                                                    @if(($variant->items_per_package > 1 || $isBeverage) && $itemsPerPackage > 0)
                                                        @if($packages > 0)
                                                            {{ (float)$packages }} {{ $pUnit }}
                                                            @if($remItems > 0) + {{ (float)$remItems }} {{ $rUnit }} @endif
                                                        @else
                                                            {{ (float)$totalStock }} {{ $rUnit }}
                                                        @endif
                                                    @else
                                                        {{ (float)$totalStock }} {{ $rUnit }}
                                                    @endif
                                                    @if($isLowStock) <i class="fa fa-warning small"></i> @endif
                                                </span>
                                            </div>

                                            @if(($variant->items_per_package > 1 || $isBeverage) && $product->category !== 'food')
                                            <div class="d-flex justify-content-between py-2">
                                                <span class="text-muted font-weight-bold small text-uppercase">Total Units:</span>
                                                <span class="font-weight-bold text-dark">
                                                    {{ number_format($totalStock) }} {{ $rUnit }}
                                                </span>
                                            </div>
                                            @endif
                                        </div>

                                    @if($isBeverage)
                                    <div class="row no-gutters text-center border rounded overflow-hidden pricing-info">
                                        <div class="col border-right p-2 bg-light-gray">
                                            <div class="small text-muted mb-1 text-uppercase font-weight-bold" style="font-size: 10px;">Bottle Price</div>
                                            <div class="font-weight-bold font-italic" style="font-size: 13px;">Tsh {{ number_format($variant->selling_price_per_pic ?? 0, 0) }}</div>
                                        </div>
                                        <div class="col p-2 bg-light-gray">
                                            <div class="small text-muted mb-1 text-uppercase font-weight-bold" style="font-size: 10px;">Glass/Tot</div>
                                            <div class="font-weight-bold text-muted font-italic" style="font-size: 13px;">{{ $variant->can_sell_as_serving ? 'Tsh ' . number_format($variant->selling_price_per_serving, 0) : 'N/A' }}</div>
                                        </div>
                                    </div>
                                    @endif

                                    <div class="mt-4 d-flex justify-content-between align-items-center">
                                        <div class="btn-group">
                                            <a href="{{ route('storekeeper.stock-receipts.create', ['product_id' => $product->id, 'variant_id' => $variant->id]) }}" 
                                               class="btn btn-sm btn-outline-success" title="Receive Stock">
                                                <i class="fa fa-inbox"></i>
                                            </a>
                                            <a href="{{ route('storekeeper.transfers.create', ['product_id' => $product->id, 'variant_id' => $variant->id]) }}" 
                                               class="btn btn-sm btn-outline-warning" title="Distribute">
                                                <i class="fa fa-exchange"></i>
                                            </a>
                                        </div>
                                        <button class="btn btn-sm btn-outline-danger notification-btn border" title="Stock Alerts">
                                            <i class="fa {{ $isLowStock ? 'fa-bell text-danger animate-pulse' : 'fa-bell-o' }}"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $products->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-5 bg-light rounded shadow-sm">
                <i class="fa fa-search fa-4x text-muted mb-3"></i>
                <h3>No Items Found</h3>
                <p class="text-muted">Adjust your search or filters to find what you're looking for.</p>
                <a href="{{ route('storekeeper.inventory') }}" class="btn btn-danger rounded-pill px-4">Clear All Filters</a>
            </div>
        @endif
    </div>

    <style>
        .rounded-lg { border-radius: 0.75rem !important; }
        .bg-white-translucent { background-color: rgba(255, 255, 255, 0.2); }
        .opacity-75 { opacity: 0.75; }
        .search-box .form-control:focus {
            box-shadow: none;
            border-color: #ced4da;
        }
        .product-card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            border-radius: 10px;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
        .bg-light-gray { background-color: #fcfcfc; }
        .pricing-info div { line-height: 1.2; }
        .font-italic { font-style: italic; }
        .filter-buttons .btn {
            font-size: 11px;
            letter-spacing: 0.5px;
            font-weight: 700;
        }
        .notification-btn {
            padding: 5px 10px;
            border-radius: 5px;
            color: #900;
        }
        .animate-pulse {
            animation: pulse-red 2s infinite;
        }
        @keyframes pulse-red {
            0% { transform: scale(0.95); opacity: 0.7; }
            70% { transform: scale(1); opacity: 1; }
            100% { transform: scale(0.95); opacity: 0.7; }
        }
        .badge-secondary { background-color: #4a4a4a; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Optional: Implement grid/list toggle logic here if needed
            const btnGridView = document.getElementById('btnGridView');
            const btnListView = document.getElementById('btnListView');
            
            if(btnListView) {
                btnListView.addEventListener('click', function() {
                    alert('List view is currently under development to match the new design. Please use grid view for now.');
                });
            }
        });
    </script>
@endsection