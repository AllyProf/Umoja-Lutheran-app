@extends('dashboard.layouts.app')

@section('content')
@php
    $stats = $summaryStats ?? ['brands' => $products->total(), 'variants' => 0, 'categories' => 0, 'active' => 0];

    $productCards = $products->map(function ($product) {
        $codes = $product->departments->pluck('code')->filter()->map(fn ($c) => strtolower($c))->all();
        $cat = strtolower((string) $product->category);
        $typ = strtolower((string) $product->type);

        if (in_array('shared', $codes) || count(array_intersect($codes, ['bar', 'kitchen', 'housekeeping'])) >= 3 || $cat === 'general') {
            $deptGroup = 'shared';
            $deptLabel = 'Shared';
            $deptBadge = 'warning';
        } elseif (in_array('housekeeping', $codes) || $cat === 'housekeeping' || $typ === 'housekeeping') {
            $deptGroup = 'housekeeping';
            $deptLabel = 'Housekeeping';
            $deptBadge = 'secondary';
        } else {
            $deptGroup = 'restaurant';
            $deptLabel = 'Restaurant';
            $deptBadge = 'info';
        }

        if (in_array($cat, ['vinywaji', 'alcoholic_beverage', 'non_alcoholic_beverage', 'water', 'juices', 'spirits', 'wines']) || $typ === 'drink') {
            $kindLabel = 'Drinks';
        } elseif (in_array($cat, ['chakula', 'food', 'meat_poultry', 'seafood', 'vegetables', 'dairy', 'kitchen', 'snacks']) || $typ === 'food') {
            $kindLabel = 'Food';
        } elseif ($cat === 'housekeeping' || $typ === 'housekeeping') {
            $kindLabel = 'Housekeeping';
        } elseif ($cat === 'general' || $typ === 'general') {
            $kindLabel = 'General';
        } else {
            $kindLabel = $cat ? ucfirst(str_replace('_', ' ', $cat)) : '—';
        }

        $firstVariant = $product->variants->first();
        $receivingUnit = $firstVariant->receiving_unit ?? 'Pcs';
        $rawSell = strtolower((string) ($firstVariant->selling_unit ?? ''));
        // Legacy values (glass/pic/serving) display as Pcs — store sells by pieces
        $sellingUnit = in_array($rawSell, ['glass', 'pic', 'serving', 'tot', 'shot', 'cocktail', ''])
            ? 'Pcs'
            : ($firstVariant->selling_unit ?? 'Pcs');
        $buyingPrice = $firstVariant->buying_price ?? 0;
        $sellingPrice = $firstVariant->selling_price_per_pic ?? 0;

        $stockQty = 0.0;
        foreach ($product->variants as $variant) {
            $stockQty += (float) $variant->getCurrentStock();
        }
        $unitLower = strtolower($receivingUnit);
        $isWholeUnit = in_array($unitLower, ['pcs', 'pc', 'piece', 'pieces', 'unit', 'units', 'bottle', 'bottles', 'pic'], true)
            || !preg_match('/kg|litre|liter|gram|ml/i', $receivingUnit);
        $stockDisplay = $isWholeUnit
            ? number_format((int) round(max(0, $stockQty)))
            : rtrim(rtrim(number_format(max(0, $stockQty), 2, '.', ','), '0'), '.');

        return [
            'product' => $product,
            'deptGroup' => $deptGroup,
            'deptLabel' => $deptLabel,
            'deptBadge' => $deptBadge,
            'kindLabel' => $kindLabel,
            'usage' => $product->usage ?? 'for_sale',
            'is_returnable' => (bool) ($product->is_returnable ?? false),
            'receivingUnit' => $receivingUnit,
            'sellingUnit' => $sellingUnit,
            'buyingPrice' => $buyingPrice,
            'sellingPrice' => $sellingPrice,
            'stockQty' => $stockQty,
            'stockDisplay' => $stockDisplay,
            'stockLow' => $stockQty <= 0,
        ];
    });
@endphp

<div class="app-title">
    <div>
        <h1><i class="fa fa-cubes"></i> Store Products</h1>
        <p>Products registered for Restaurant, Housekeeping, and Shared use</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route('storekeeper.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Products</li>
    </ul>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fa fa-check-circle mr-1"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fa fa-exclamation-circle mr-1"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
@endif

<div class="row mb-4">
    <div class="col-md-3 col-6 mb-3">
        <div class="widget-small primary coloured-icon">
            <i class="icon fa fa-cube fa-2x"></i>
            <div class="info">
                <h4>Products</h4>
                <p><b>{{ $stats['brands'] ?? $products->total() }}</b></p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="widget-small info coloured-icon">
            <i class="icon fa fa-tags fa-2x"></i>
            <div class="info">
                <h4>Sizes / Packages</h4>
                <p><b>{{ $stats['variants'] ?? 0 }}</b></p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="widget-small warning coloured-icon">
            <i class="icon fa fa-sitemap fa-2x"></i>
            <div class="info">
                <h4>Groups</h4>
                <p><b>{{ $stats['categories'] ?? 0 }}</b></p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="widget-small success coloured-icon">
            <i class="icon fa fa-check fa-2x"></i>
            <div class="info">
                <h4>Active</h4>
                <p><b>{{ $stats['active'] ?? 0 }}</b></p>
            </div>
        </div>
    </div>
</div>

<div class="tile shadow-sm">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3" style="gap:12px;">
        <div class="products-search flex-grow-1" style="max-width:340px;">
            <i class="fa fa-search"></i>
            <input type="text" id="productSearch" class="form-control" placeholder="Search product name…">
        </div>
        <div class="d-flex flex-wrap align-items-center" style="gap:8px;">
            <div class="btn-group btn-group-sm" id="deptFilters" role="group">
                <button type="button" class="btn btn-outline-secondary active" data-filter="all">All</button>
                <button type="button" class="btn btn-outline-secondary" data-filter="restaurant">Restaurant</button>
                <button type="button" class="btn btn-outline-secondary" data-filter="housekeeping">Housekeeping</button>
                <button type="button" class="btn btn-outline-secondary" data-filter="shared">Shared</button>
            </div>
            <div class="btn-group btn-group-sm" id="viewToggle" role="group">
                <button type="button" class="btn btn-outline-primary active" data-view="grid" title="Grid view">
                    <i class="fa fa-th-large"></i>
                </button>
                <button type="button" class="btn btn-outline-primary" data-view="list" title="List view">
                    <i class="fa fa-list"></i>
                </button>
            </div>
            <a href="{{ route('storekeeper.products.create') }}" class="btn btn-primary">
                <i class="fa fa-plus"></i> Register Product
            </a>
        </div>
    </div>

    @if($products->count() > 0)
        {{-- GRID VIEW --}}
        <div id="productsGrid" class="products-grid">
            @foreach($productCards as $card)
                @php $product = $card['product']; @endphp
                <div class="product-card product-row"
                    data-name="{{ strtolower($product->name) }}"
                    data-dept="{{ $card['deptGroup'] }}">
                    <div class="product-card-top">
                        <div class="product-card-icon">
                            <i class="fa fa-cube"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">{{ $product->name }}</h5>
                            <span class="badge badge-{{ $card['deptBadge'] }}">{{ $card['deptLabel'] }}</span>
                            @if($card['usage'] === 'internal')
                                <span class="badge badge-light border">Internal</span>
                            @else
                                <span class="badge badge-success">For sale</span>
                            @endif
                            @if($card['is_returnable'])
                                <span class="badge badge-info"><i class="fa fa-undo"></i> Returnable</span>
                            @endif
                        </div>
                    </div>
                    <div class="product-card-meta">
                        <div>
                            <span>In stock</span>
                            <strong class="{{ $card['stockLow'] ? 'text-danger' : 'text-success' }}">
                                {{ $card['stockDisplay'] }} {{ $card['receivingUnit'] }}
                            </strong>
                        </div>
                        <div><span>Receive</span><strong>{{ $card['receivingUnit'] }}</strong></div>
                        @if($card['usage'] === 'for_sale')
                            <div><span>Sell</span><strong>{{ $card['sellingUnit'] }}</strong></div>
                        @endif
                        <div><span>Buy</span><strong>{{ number_format($card['buyingPrice']) }} TZS</strong></div>
                        @if($card['usage'] === 'for_sale')
                            <div><span>Sell price</span><strong>{{ number_format($card['sellingPrice']) }} TZS</strong></div>
                        @endif
                    </div>
                    <div class="product-card-actions">
                        <a href="{{ route('storekeeper.products.edit', $product->id) }}"
                            class="btn btn-sm btn-outline-primary">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('storekeeper.products.destroy', $product->id) }}"
                            method="POST" class="d-inline"
                            onsubmit="return confirm('Delete this product and all its sizes?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                <i class="fa fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- LIST VIEW --}}
        <div id="productsList" class="table-responsive" style="display:none;">
            <table class="table table-hover mb-0" id="productsTable">
                <thead class="thead-light">
                    <tr>
                        <th>Product</th>
                        <th>Department</th>
                        <th>Usage</th>
                        <th class="text-center">In stock</th>
                        <th>Units</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($productCards as $card)
                        @php $product = $card['product']; @endphp
                        <tr class="product-row"
                            data-name="{{ strtolower($product->name) }}"
                            data-dept="{{ $card['deptGroup'] }}">
                            <td>
                                <strong class="text-dark">{{ $product->name }}</strong>
                                <small class="d-block text-muted">
                                    Buy {{ number_format($card['buyingPrice']) }} TZS
                                    @if($card['usage'] === 'for_sale')
                                        · Sell {{ number_format($card['sellingPrice']) }} TZS
                                    @endif
                                </small>
                            </td>
                            <td>
                                <span class="badge badge-{{ $card['deptBadge'] }}">{{ $card['deptLabel'] }}</span>
                            </td>
                            <td>
                                @if($card['usage'] === 'internal')
                                    <span class="badge badge-light border">Internal</span>
                                @else
                                    <span class="badge badge-success">For sale</span>
                                @endif
                                @if($card['is_returnable'])
                                    <span class="badge badge-info ml-1"><i class="fa fa-undo"></i> Returnable</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <strong class="{{ $card['stockLow'] ? 'text-danger' : 'text-success' }}">
                                    {{ $card['stockDisplay'] }}
                                </strong>
                                <small class="d-block text-muted">{{ $card['receivingUnit'] }}</small>
                            </td>
                            <td>
                                <small class="text-muted">In:</small> {{ $card['receivingUnit'] }}
                                @if($card['usage'] === 'for_sale')
                                    <br><small class="text-muted">Out:</small> {{ $card['sellingUnit'] }}
                                @endif
                            </td>
                            <td class="text-right text-nowrap">
                                <a href="{{ route('storekeeper.products.edit', $product->id) }}"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('storekeeper.products.destroy', $product->id) }}"
                                    method="POST" class="d-inline"
                                    onsubmit="return confirm('Delete this product and all its sizes?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @include('dashboard.partials.bootstrap-pagination', ['paginator' => $products])
    @else
        <div class="text-center py-5">
            <i class="fa fa-cubes fa-4x text-muted mb-3" style="opacity:.35;"></i>
            <h4>No products yet</h4>
            <p class="text-muted">Register products for Restaurant, Housekeeping, or Shared.</p>
            <a href="{{ route('storekeeper.products.create') }}" class="btn btn-primary mt-2">
                <i class="fa fa-plus"></i> Register Product
            </a>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<style>
    .products-search { position: relative; }
    .products-search i {
        position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #858796;
    }
    .products-search input { padding-left: 36px; }
    #productsTable td { vertical-align: middle; }
    #deptFilters .btn.active,
    #viewToggle .btn.active {
        background: #4e73df;
        border-color: #4e73df;
        color: #fff;
    }
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 16px;
    }
    .product-card {
        border: 1px solid #e3e6f0;
        border-radius: 12px;
        padding: 16px;
        background: #fff;
        display: flex;
        flex-direction: column;
        gap: 12px;
        transition: box-shadow .15s, border-color .15s;
    }
    .product-card:hover {
        border-color: #4e73df;
        box-shadow: 0 6px 18px rgba(78, 115, 223, .12);
    }
    .product-card-top {
        display: flex;
        gap: 12px;
        align-items: flex-start;
    }
    .product-card-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        background: #f0f4ff;
        color: #4e73df;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.1rem;
    }
    .product-card h5 {
        font-size: 1rem;
        font-weight: 700;
        color: #3a3b45;
        margin: 0;
        line-height: 1.3;
    }
    .product-card-meta {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        background: #f8f9fc;
        border-radius: 8px;
        padding: 10px;
    }
    .product-card-meta span {
        display: block;
        font-size: .65rem;
        text-transform: uppercase;
        color: #858796;
        font-weight: 700;
    }
    .product-card-meta strong {
        font-size: .85rem;
        color: #3a3b45;
    }
    .product-card-actions {
        display: flex;
        gap: 8px;
        margin-top: auto;
    }
</style>
<script>
    let currentFilter = 'all';
    let currentView = localStorage.getItem('storekeeperProductsView') || 'grid';

    function applyFilters() {
        const term = (document.getElementById('productSearch').value || '').toLowerCase().trim();
        document.querySelectorAll('.product-row').forEach(row => {
            const nameOk = !term || (row.dataset.name || '').includes(term);
            const deptOk = currentFilter === 'all' || row.dataset.dept === currentFilter;
            row.style.display = (nameOk && deptOk) ? '' : 'none';
        });
    }

    function setView(view) {
        currentView = view;
        localStorage.setItem('storekeeperProductsView', view);

        const grid = document.getElementById('productsGrid');
        const list = document.getElementById('productsList');
        if (!grid || !list) return;

        if (view === 'grid') {
            grid.style.display = 'grid';
            list.style.display = 'none';
        } else {
            grid.style.display = 'none';
            list.style.display = 'block';
        }

        document.querySelectorAll('#viewToggle [data-view]').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.view === view);
        });
    }

    document.getElementById('productSearch')?.addEventListener('input', applyFilters);

    document.querySelectorAll('#deptFilters [data-filter]').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('#deptFilters [data-filter]').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.dataset.filter;
            applyFilters();
        });
    });

    document.querySelectorAll('#viewToggle [data-view]').forEach(btn => {
        btn.addEventListener('click', function () {
            setView(this.dataset.view);
        });
    });

    setView(currentView);
</script>
@endsection
