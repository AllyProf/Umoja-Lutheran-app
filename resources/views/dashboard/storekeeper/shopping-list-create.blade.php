@extends('dashboard.layouts.app')

@section('content')
@php
    $staffName = auth()->guard('staff')->user()->name ?? '';
    $categories = $products->pluck('category')->unique()->filter()->sort()->values();
@endphp

<div class="app-title">
    <div>
        <h1><i class="fa fa-file-text-o"></i> Create Purchase List</h1>
        <p>Build the list, then send it to the accountant for review and payment</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route('storekeeper.shopping-list.index') }}">Shopping Lists</a></li>
        <li class="breadcrumb-item active">Create</li>
    </ul>
</div>

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fa fa-exclamation-circle mr-1"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
@endif

<form action="{{ route('storekeeper.shopping-list.store') }}" method="POST" id="purchaseListForm">
    @csrf
    <div id="hiddenItemsContainer"></div>

    <div class="row">
        {{-- LEFT: details + add products --}}
        <div class="col-lg-7 mb-4">
            <div class="tile shadow-sm mb-4">
                <h3 class="tile-title">
                    <span class="badge badge-primary mr-2">1</span> List details
                </h3>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold">Purchaser name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-lg" required
                            value="{{ old('name', $prefillName ?: $staffName) }}"
                            placeholder="Who will buy these items?">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold">Purchase date</label>
                        <input type="date" name="shopping_date" class="form-control form-control-lg"
                            value="{{ old('shopping_date', $prefillDate ?? date('Y-m-d')) }}">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold">Market / supplier</label>
                        <input type="text" name="market_name" class="form-control"
                            value="{{ old('market_name') }}" placeholder="e.g. City Market, Makumbusho">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold">LPO budget <span class="text-muted">(optional)</span></label>
                        <select name="lpo_id" id="lpoSelect" class="form-control">
                            <option value="">No LPO linked</option>
                            @forelse($lpos as $lpo)
                                <option value="{{ $lpo->id }}">
                                    LPO #{{ $lpo->id }}
                                    · {{ $lpo->start_date->format('d M') }} – {{ $lpo->end_date->format('d M Y') }}
                                    · {{ ucfirst(str_replace('_', ' ', $lpo->status)) }}
                                    @if($lpo->total_amount)
                                        · {{ number_format($lpo->total_amount) }} TZS
                                    @endif
                                </option>
                            @empty
                                <option value="" disabled>No open LPO budgets found</option>
                            @endforelse
                        </select>
                        <small class="text-muted">Create an LPO under Local Purchase Orders if the list is empty.</small>
                    </div>
                    <div class="col-12 form-group mb-0">
                        <label class="font-weight-bold">Notes for accountant</label>
                        <textarea name="notes" class="form-control" rows="2"
                            placeholder="Optional note about urgency, preferred supplier, etc.">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="tile shadow-sm">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3" style="gap:8px;">
                    <h3 class="tile-title mb-0">
                        <span class="badge badge-primary mr-2">2</span> Add products
                    </h3>
                    <span class="badge badge-info d-none" id="lpoFilterBadge"></span>
                </div>

                <div class="alert alert-light border d-none mb-3" id="lpoFilterHint">
                    <i class="fa fa-filter text-primary mr-1"></i>
                    Showing only items from the selected LPO budget.
                    <button type="button" class="btn btn-sm btn-outline-primary ml-2" id="addAllLpoBtn" onclick="addAllLpoItems()">
                        <i class="fa fa-plus-circle"></i> Add all LPO items
                    </button>
                </div>

                <div class="po-search-bar mb-3">
                    <i class="fa fa-search"></i>
                    <input type="text" id="productSearch" class="form-control form-control-lg"
                        placeholder="Search product name..." autocomplete="off">
                </div>

                <div class="po-filter-row mb-3" id="categoryFilters">
                    <button type="button" class="po-chip active" data-cat="all" onclick="filterProducts('all', this)">All</button>
                    @foreach($categories as $cat)
                        <button type="button" class="po-chip" data-cat="{{ $cat }}"
                            onclick="filterProducts('{{ $cat }}', this)">
                            {{ ucfirst(str_replace('_', ' ', $cat)) }}
                        </button>
                    @endforeach
                    <button type="button" class="po-chip po-chip-custom" onclick="openCustomModal()">
                        <i class="fa fa-plus"></i> Custom item
                    </button>
                </div>

                <div class="po-product-list" id="productList">
                    {{-- Filled by JS: all products, or only LPO items when budget selected --}}
                </div>
                <div class="text-center text-muted py-4 d-none" id="noProductsMsg">
                    <i class="fa fa-inbox fa-2x mb-2" style="opacity:.4;"></i>
                    <p class="mb-0" id="noProductsText">No products found.</p>
                </div>
            </div>
        </div>

        {{-- RIGHT: order summary --}}
        <div class="col-lg-5 mb-4">
            <div class="tile shadow-sm po-summary sticky-summary">
                <h3 class="tile-title d-flex justify-content-between align-items-center">
                    <span><span class="badge badge-primary mr-2">3</span> Purchase list</span>
                    <span class="badge badge-light border" id="itemCountBadge">0 items</span>
                </h3>

                <div id="emptyCart" class="po-empty text-center py-5">
                    <i class="fa fa-shopping-basket fa-3x text-muted mb-3" style="opacity:.35;"></i>
                    <p class="text-muted mb-0">No items yet.<br>Search and add products on the left.</p>
                </div>

                <div id="cartItems" class="po-cart-items"></div>

                <div class="po-footer mt-3 pt-3 border-top">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted font-weight-bold text-uppercase" style="font-size:.75rem;">Est. total</span>
                        <strong class="h4 mb-0 text-primary" id="displayTotal">0 TZS</strong>
                    </div>
                    <button type="submit" class="btn btn-success btn-lg btn-block" id="submitBtn" disabled>
                        <i class="fa fa-paper-plane mr-1"></i> Submit to Accountant
                    </button>
                    <a href="{{ route('storekeeper.shopping-list.index') }}"
                        class="btn btn-outline-secondary btn-block mt-2">Cancel</a>
                    <p class="text-muted small text-center mt-3 mb-0">
                        Status will be <strong>Pending</strong> until the accountant reviews and processes payment.
                    </p>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- Custom item modal --}}
<div class="modal fade" id="customItemModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add custom item</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Item name <span class="text-danger">*</span></label>
                    <input type="text" id="manual_name" class="form-control" placeholder="e.g. Fresh coriander">
                </div>
                <div class="row">
                    <div class="col-6 form-group">
                        <label>Category</label>
                        <select id="manual_cat" class="form-control">
                            <option value="other">Other</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}">{{ ucfirst(str_replace('_', ' ', $cat)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 form-group">
                        <label>Unit</label>
                        <input type="text" id="manual_unit" class="form-control" value="pcs" placeholder="pcs, kg…">
                    </div>
                </div>
                <div class="form-group mb-0">
                    <label>Est. unit price (TZS)</label>
                    <input type="number" id="manual_price" class="form-control" min="0" step="1" placeholder="Optional">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="confirmCustomAdd()">Add to list</button>
            </div>
        </div>
    </div>
</div>

<style>
    .po-search-bar {
        position: relative;
    }
    .po-search-bar i {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #858796;
    }
    .po-search-bar input {
        padding-left: 44px;
        border-radius: 8px;
    }
    .po-filter-row {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    .po-chip {
        border: 1px solid #d1d3e2;
        background: #fff;
        color: #5a5c69;
        border-radius: 20px;
        padding: 5px 12px;
        font-size: .8rem;
        font-weight: 600;
        cursor: pointer;
    }
    .po-chip:hover { border-color: #4e73df; color: #4e73df; }
    .po-chip.active {
        background: #4e73df;
        border-color: #4e73df;
        color: #fff;
    }
    .po-chip-custom {
        border-style: dashed;
        color: #1cc88a;
        border-color: #1cc88a;
    }
    .po-product-list {
        max-height: 420px;
        overflow-y: auto;
        border: 1px solid #e3e6f0;
        border-radius: 8px;
    }
    .po-product-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        text-align: left;
        padding: 12px 14px;
        border: 0;
        border-bottom: 1px solid #f0f2f7;
        background: #fff;
        cursor: pointer;
    }
    .po-product-row:last-child { border-bottom: 0; }
    .po-product-row:hover { background: #f8f9fc; }
    .po-product-row strong { color: #3a3b45; font-size: .95rem; }
    .po-add-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #eaecf4;
        color: #4e73df;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .po-product-row:hover .po-add-icon {
        background: #4e73df;
        color: #fff;
    }
    .sticky-summary {
        position: sticky;
        top: 80px;
    }
    .po-cart-items { max-height: 480px; overflow-y: auto; }
    .po-cart-row {
        border: 1px solid #e3e6f0;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 10px;
        background: #fff;
    }
    .po-cart-row-title {
        font-weight: 700;
        color: #3a3b45;
        font-size: .92rem;
        margin-bottom: 10px;
        padding-right: 24px;
        position: relative;
    }
    .po-cart-row-title .remove-btn {
        position: absolute;
        right: 0;
        top: 0;
        color: #e74a3b;
        border: 0;
        background: transparent;
        padding: 0;
        cursor: pointer;
    }
    .po-cart-fields {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 8px;
    }
    .po-cart-fields label {
        font-size: .65rem;
        text-transform: uppercase;
        font-weight: 700;
        color: #858796;
        margin-bottom: 2px;
        display: block;
    }
    .po-cart-fields input,
    .po-cart-fields select {
        font-size: .85rem;
        height: calc(1.5em + .5rem + 2px);
        padding: .25rem .5rem;
    }
    .po-line-total {
        font-size: .8rem;
        font-weight: 700;
        color: #4e73df;
        margin-top: 8px;
        text-align: right;
    }
</style>

@php
    $resolveDisplayName = function ($product, $variant = null, $fallback = null) {
        $productName = trim((string) ($product->name ?? ''));
        $variantName = trim((string) ($variant->variant_name ?? ''));
        $generic = ['', 'standard', 'unit', 'default', 'pcs', 'piece'];

        // Prefer the specific item/variant name when it is real (e.g. Cocacola)
        if ($variantName !== '' && !in_array(strtolower($variantName), $generic, true)) {
            return $variantName;
        }

        if ($productName !== '') {
            return $productName;
        }

        return $fallback ?: 'Item';
    };

    $resolveBuyingPrice = function ($variant = null) {
        if (!$variant) {
            return 0.0;
        }
        $price = (float) ($variant->buying_price ?? 0);
        if ($price > 0) {
            return $price;
        }
        if (method_exists($variant, 'getLatestUnitCost')) {
            return (float) $variant->getLatestUnitCost();
        }
        return 0.0;
    };

    $catalogProducts = [];
    foreach ($products as $product) {
        if ($product->variants->count() > 0) {
            foreach ($product->variants as $variant) {
                $catalogProducts[] = [
                    'product_id' => $product->id,
                    'variant_id' => $variant->id,
                    'name' => $resolveDisplayName($product, $variant),
                    'unit' => $variant->purchasing_unit ?: $variant->receiving_unit ?: 'pcs',
                    'price' => $resolveBuyingPrice($variant),
                    'category' => $product->category ?? 'other',
                ];
            }
        } else {
            $catalogProducts[] = [
                'product_id' => $product->id,
                'variant_id' => null,
                'name' => $resolveDisplayName($product),
                'unit' => $product->unit ?: 'pcs',
                'price' => 0,
                'category' => $product->category ?? 'other',
            ];
        }
    }

    $lpoCatalog = $lpos->map(function ($lpo) use ($resolveDisplayName, $resolveBuyingPrice) {
        return [
            'id' => $lpo->id,
            'items' => $lpo->items->map(function ($item) use ($resolveDisplayName, $resolveBuyingPrice) {
                $variant = $item->productVariant;
                $product = $variant?->product;
                $name = $product
                    ? $resolveDisplayName($product, $variant, $item->item_name)
                    : ($item->item_name ?: 'Item');
                $registeredPrice = $resolveBuyingPrice($variant);
                $lpoPrice = (float) ($item->unit_price ?? 0);

                return [
                    'product_id' => $product?->id,
                    'variant_id' => $item->product_variant_id,
                    'name' => $name,
                    'unit' => $item->unit ?: ($variant?->purchasing_unit ?? 'pcs'),
                    // Prefer LPO budget price, else registered buying price
                    'price' => $lpoPrice > 0 ? $lpoPrice : $registeredPrice,
                    'quantity' => (float) $item->quantity,
                    'category' => $product?->category ?? 'other',
                    'budget_qty' => (float) $item->quantity,
                    'budget_total' => (float) $item->total_price,
                ];
            })->values(),
        ];
    })->values();
@endphp

<script>
    let cart = [];
    let visibleCatalog = [];
    const prefillItems = @json($prefillItems ?? []);
    const allProducts = @json($catalogProducts);
    const lpoCatalog = @json($lpoCatalog);
    const unitOptions = ['pcs', 'kg', 'g', 'Litres', 'ml', 'Carton', 'Crate', 'Box', 'Pack', 'Bag', 'Bottle', 'Dozen', 'Bunch', 'Sado', 'Debe', 'Packet', 'Tray', 'Bucket', 'other'];

    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('lpoSelect').addEventListener('change', function () {
            onLpoChange(this.value);
        });

        document.getElementById('productSearch').addEventListener('input', applyProductFilters);

        visibleCatalog = allProducts.slice();
        renderProductList();

        if (prefillItems.length > 0) {
            prefillItems.forEach(item => {
                addToCart({
                    product_id: item.product_id || null,
                    variant_id: item.purchase_request_id ? null : (item.product_variant_id || null),
                    name: item.product_name,
                    unit: item.unit || 'pcs',
                    price: item.estimated_price || 0,
                    quantity: item.quantity || 1,
                    category: item.category || 'other',
                    purchase_request_id: item.purchase_request_id || null
                }, false);
            });
            renderCart();
        }
    });

    function getSelectedLpo() {
        const lpoId = document.getElementById('lpoSelect').value;
        if (!lpoId) return null;
        return lpoCatalog.find(l => l.id == lpoId) || null;
    }

    function onLpoChange(lpoId) {
        const badge = document.getElementById('lpoFilterBadge');
        const hint = document.getElementById('lpoFilterHint');
        const categoryFilters = document.getElementById('categoryFilters');

        if (!lpoId) {
            visibleCatalog = allProducts.slice();
            badge.classList.add('d-none');
            hint.classList.add('d-none');
            categoryFilters.style.display = '';
            renderProductList();
            showToast('Showing all products');
            return;
        }

        const selectedLpo = getSelectedLpo();
        if (!selectedLpo) {
            visibleCatalog = [];
            renderProductList();
            return;
        }

        visibleCatalog = (selectedLpo.items || []).map(item => ({
            product_id: item.product_id,
            variant_id: item.variant_id,
            name: item.name,
            unit: item.unit || 'pcs',
            price: item.price || 0,
            category: item.category || 'other',
            budget_qty: item.budget_qty,
            budget_total: item.budget_total,
            quantity: item.budget_qty || 1,
        }));

        badge.textContent = visibleCatalog.length + ' LPO item' + (visibleCatalog.length === 1 ? '' : 's');
        badge.classList.remove('d-none');
        hint.classList.remove('d-none');
        categoryFilters.style.display = 'none';

        // Sync prices for items already in cart
        cart.forEach(item => {
            const match = visibleCatalog.find(li =>
                (li.variant_id && li.variant_id == item.variant_id) ||
                (!li.variant_id && li.name.toLowerCase() === item.name.toLowerCase())
            );
            if (match) item.price = match.price;
        });
        renderCart();
        renderProductList();
        showToast('Showing LPO #' + lpoId + ' items only');
    }

    function renderProductList() {
        const list = document.getElementById('productList');
        const empty = document.getElementById('noProductsMsg');
        const emptyText = document.getElementById('noProductsText');
        const term = document.getElementById('productSearch').value.toLowerCase().trim();
        const activeChip = document.querySelector('.po-chip.active');
        const cat = activeChip ? activeChip.dataset.cat : 'all';
        const lpoMode = !!document.getElementById('lpoSelect').value;

        const filtered = visibleCatalog.filter(item => {
            const catOk = lpoMode || cat === 'all' || item.category === cat;
            const searchOk = !term || (item.name || '').toLowerCase().includes(term);
            return catOk && searchOk;
        });

        if (filtered.length === 0) {
            list.innerHTML = '';
            empty.classList.remove('d-none');
            emptyText.textContent = lpoMode
                ? 'This LPO has no items yet. Add items to the LPO budget first.'
                : 'No products match your search.';
            return;
        }

        empty.classList.add('d-none');
        list.innerHTML = filtered.map((item, idx) => {
            const priceLabel = Number(item.price || 0) > 0
                ? `${Number(item.price).toLocaleString()} TZS`
                : 'No price set';
            const meta = lpoMode
                ? `Budget qty: ${item.budget_qty || 0} ${item.unit || ''} · ${priceLabel}`
                : `${String(item.category || '').replace(/_/g, ' ')} · ${item.unit || 'pcs'} · ${priceLabel}`;
            const payload = {
                product_id: item.product_id,
                variant_id: item.variant_id,
                name: item.name,
                unit: item.unit || 'pcs',
                price: item.price || 0,
                quantity: lpoMode ? (item.budget_qty || 1) : 1,
                category: item.category || 'other',
            };
            return `
                <button type="button" class="po-product-row" data-idx="${idx}"
                    data-category="${escapeAttr(item.category || '')}"
                    data-name="${escapeAttr((item.name || '').toLowerCase())}">
                    <div>
                        <strong>${escapeHtml(item.name)}</strong>
                        <small class="d-block text-muted">${escapeHtml(meta)}</small>
                    </div>
                    <span class="po-add-icon"><i class="fa fa-plus"></i></span>
                </button>
            `;
        }).join('');

        list.querySelectorAll('.po-product-row').forEach((btn, i) => {
            btn.addEventListener('click', function () {
                const item = filtered[i];
                addToCart({
                    product_id: item.product_id,
                    variant_id: item.variant_id,
                    name: item.name,
                    unit: item.unit || 'pcs',
                    price: item.price || 0,
                    quantity: lpoMode ? (item.budget_qty || 1) : 1,
                    category: item.category || 'other',
                });
            });
        });
    }

    function addAllLpoItems() {
        const selectedLpo = getSelectedLpo();
        if (!selectedLpo || !selectedLpo.items || selectedLpo.items.length === 0) {
            alert('No LPO items to add.');
            return;
        }
        selectedLpo.items.forEach(item => {
            addToCart({
                product_id: item.product_id,
                variant_id: item.variant_id,
                name: item.name,
                unit: item.unit || 'pcs',
                price: item.price || 0,
                quantity: item.budget_qty || 1,
                category: item.category || 'other',
            }, false);
        });
        renderCart();
        showToast('Added all LPO items');
    }

    function addToCart(item, notify) {
        if (typeof notify === 'undefined') notify = true;

        const existing = cart.find(i =>
            (i.variant_id && item.variant_id && i.variant_id == item.variant_id) ||
            (!i.variant_id && !item.variant_id && i.product_id && i.product_id == item.product_id && i.name === item.name) ||
            (!item.product_id && !item.variant_id && i.name === item.name)
        );

        if (existing) {
            existing.quantity = (parseFloat(existing.quantity) || 0) + (parseFloat(item.quantity) || 1);
            if (item.price) existing.price = item.price;
        } else {
            item.quantity = item.quantity || 1;
            cart.push(item);
        }

        renderCart();
        if (notify) showToast('Added: ' + item.name);
    }

    function renderCart() {
        const cartEl = document.getElementById('cartItems');
        const emptyEl = document.getElementById('emptyCart');
        const hidden = document.getElementById('hiddenItemsContainer');
        const badge = document.getElementById('itemCountBadge');
        const totalEl = document.getElementById('displayTotal');
        const submitBtn = document.getElementById('submitBtn');

        let html = '';
        let hiddenHtml = '';
        let total = 0;

        if (cart.length === 0) {
            emptyEl.style.display = 'block';
            cartEl.innerHTML = '';
            hidden.innerHTML = '';
            badge.textContent = '0 items';
            totalEl.textContent = '0 TZS';
            submitBtn.disabled = true;
            return;
        }

        emptyEl.style.display = 'none';
        submitBtn.disabled = false;

        cart.forEach((item, index) => {
            const qty = Number(item.quantity) || 0;
            const price = Number(item.price) || 0;
            const lineTotal = qty * price;
            total += lineTotal;

            const unitSelect = unitOptions.map(u =>
                `<option value="${u}" ${item.unit === u ? 'selected' : ''}>${u}</option>`
            ).join('');

            html += `
                <div class="po-cart-row">
                    <div class="po-cart-row-title">
                        ${escapeHtml(item.name)}
                        <button type="button" class="remove-btn" onclick="removeFromCart(${index})" title="Remove">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                    <div class="po-cart-fields">
                        <div>
                            <label>Qty</label>
                            <input type="number" class="form-control" min="0.01" step="0.01"
                                value="${item.quantity}" oninput="liveUpdate(${index}, 'quantity', this.value)">
                        </div>
                        <div>
                            <label>Unit</label>
                            <select class="form-control" onchange="liveUpdate(${index}, 'unit', this.value)">
                                ${unitSelect}
                            </select>
                        </div>
                        <div>
                            <label>Unit price</label>
                            <input type="number" class="form-control" min="0" step="1"
                                value="${item.price}" oninput="liveUpdate(${index}, 'price', this.value)">
                        </div>
                    </div>
                    <div class="po-line-total">Line: ${lineTotal.toLocaleString()} TZS</div>
                </div>
            `;

            hiddenHtml += `
                <input type="hidden" name="items[${index}][product_id]" value="${item.product_id || ''}">
                <input type="hidden" name="items[${index}][product_variant_id]" value="${item.variant_id || ''}">
                <input type="hidden" name="items[${index}][product_name]" value="${escapeAttr(item.name)}">
                <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}" id="hiddenQty-${index}">
                <input type="hidden" name="items[${index}][unit]" value="${escapeAttr(item.unit)}" id="hiddenUnit-${index}">
                <input type="hidden" name="items[${index}][estimated_price]" value="${lineTotal}" id="hiddenPrice-${index}">
                <input type="hidden" name="items[${index}][category]" value="${escapeAttr(item.category || 'other')}">
                <input type="hidden" name="items[${index}][purchase_request_id]" value="${item.purchase_request_id || ''}">
            `;
        });

        cartEl.innerHTML = html;
        hidden.innerHTML = hiddenHtml;
        badge.textContent = cart.length + (cart.length === 1 ? ' item' : ' items');
        totalEl.textContent = total.toLocaleString() + ' TZS';
    }

    function liveUpdate(index, key, value) {
        if (key === 'quantity' || key === 'price') {
            cart[index][key] = Number(value) || 0;
        } else {
            cart[index][key] = value;
        }

        let total = 0;
        cart.forEach((item, idx) => {
            const lineTotal = (Number(item.quantity) || 0) * (Number(item.price) || 0);
            total += lineTotal;
            const hQty = document.getElementById('hiddenQty-' + idx);
            const hPrice = document.getElementById('hiddenPrice-' + idx);
            const hUnit = document.getElementById('hiddenUnit-' + idx);
            if (hQty) hQty.value = item.quantity;
            if (hPrice) hPrice.value = lineTotal;
            if (hUnit && key === 'unit') hUnit.value = item.unit;
        });
        document.getElementById('displayTotal').textContent = total.toLocaleString() + ' TZS';

        const rows = document.querySelectorAll('.po-line-total');
        if (rows[index]) {
            const lineTotal = (Number(cart[index].quantity) || 0) * (Number(cart[index].price) || 0);
            rows[index].textContent = 'Line: ' + lineTotal.toLocaleString() + ' TZS';
        }
    }

    function removeFromCart(index) {
        cart.splice(index, 1);
        renderCart();
    }

    function filterProducts(cat, btn) {
        document.querySelectorAll('.po-chip').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        applyProductFilters();
    }

    function applyProductFilters() {
        renderProductList();
    }

    function openCustomModal() {
        $('#customItemModal').modal('show');
    }

    function confirmCustomAdd() {
        const name = document.getElementById('manual_name').value.trim();
        if (!name) {
            alert('Item name is required');
            return;
        }
        addToCart({
            product_id: null,
            variant_id: null,
            name: name,
            unit: document.getElementById('manual_unit').value.trim() || 'pcs',
            price: parseFloat(document.getElementById('manual_price').value) || 0,
            category: document.getElementById('manual_cat').value
        });
        $('#customItemModal').modal('hide');
        document.getElementById('manual_name').value = '';
        document.getElementById('manual_price').value = '';
    }

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function escapeAttr(str) {
        return String(str).replace(/"/g, '&quot;');
    }

    function showToast(msg) {
        const toast = document.createElement('div');
        toast.style.cssText = 'position:fixed;bottom:24px;left:50%;transform:translateX(-50%);background:#2d3748;color:#fff;padding:10px 18px;border-radius:24px;z-index:9999;font-size:14px;';
        toast.textContent = msg;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 1800);
    }

    document.getElementById('purchaseListForm').addEventListener('submit', function (e) {
        if (cart.length === 0) {
            e.preventDefault();
            alert('Add at least one product before submitting.');
        }
    });
</script>
@endsection
