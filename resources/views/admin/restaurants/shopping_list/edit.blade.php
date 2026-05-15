@extends('dashboard.layouts.app')

@section('content')
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --glass-bg: rgba(255, 255, 255, 0.9);
            --glass-border: rgba(255, 255, 255, 0.2);
            --shadow-premium: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
        }

        .picker-container {
            display: flex;
            gap: 20px;
            height: calc(100vh - 180px);
            min-height: 600px;
        }

        /* Sidebar Categories */
        .category-sidebar {
            width: 250px;
            background: var(--glass-bg);
            backdrop-filter: blur(8px);
            border-radius: 15px;
            border: 1px solid var(--glass-border);
            padding: 15px;
            overflow-y: auto;
            box-shadow: var(--shadow-premium);
        }

        .category-btn {
            display: block;
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 8px;
            border: none;
            background: transparent;
            text-align: left;
            border-radius: 10px;
            color: #4a5568;
            font-weight: 500;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }

        .category-btn:hover {
            background: rgba(118, 75, 162, 0.05);
            padding-left: 20px;
            color: #764ba2;
        }

        .category-btn.active {
            background: var(--primary-gradient);
            color: white;
            box-shadow: 0 4px 15px rgba(118, 75, 162, 0.3);
        }

        /* Product Grid Area */
        .product-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .search-container {
            position: relative;
            background: white;
            border-radius: 12px;
            box-shadow: var(--shadow-premium);
        }

        .search-input {
            width: 100%;
            padding: 15px 20px 15px 50px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            outline: none;
        }

        .search-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
            overflow-y: auto;
            padding: 5px;
        }

        .product-card {
            background: white;
            border-radius: 15px;
            padding: 15px;
            border: 1px solid #edf2f7;
            transition: all 0.3s ease;
            position: relative;
            cursor: pointer;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            border-color: #764ba2;
        }

        .product-card .cat-tag {
            font-size: 10px;
            text-transform: uppercase;
            color: #718096;
            margin-bottom: 5px;
            display: block;
        }

        .product-card h5 {
            margin: 0 0 10px 0;
            font-size: 15px;
            color: #2d3748;
            font-weight: 600;
        }

        .product-card .price {
            font-weight: 700;
            color: #764ba2;
            font-size: 14px;
        }

        .product-card .add-btn {
            position: absolute;
            bottom: 15px;
            right: 15px;
            background: var(--primary-gradient);
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            transition: transform 0.2s;
        }

        .product-card:hover .add-btn {
            transform: scale(1.15);
        }

        /* Cart Sidebar */
        .cart-sidebar {
            width: 350px;
            background: white;
            border-radius: 15px;
            display: flex;
            flex-direction: column;
            box-shadow: var(--shadow-premium);
            border: 1px solid #edf2f7;
        }

        .cart-header {
            padding: 20px;
            border-bottom: 1px solid #edf2f7;
            background: var(--primary-gradient);
            border-radius: 15px 15px 0 0;
            color: white;
        }

        .cart-items {
            flex: 1;
            overflow-y: auto;
            padding: 15px;
        }

        .cart-item {
            display: flex;
            gap: 10px;
            padding-bottom: 15px;
            margin-bottom: 15px;
            border-bottom: 1px solid #f7fafc;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .cart-item-info {
            flex: 1;
        }

        .cart-item-title {
            font-size: 13px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 5px;
        }

        .cart-item-controls {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .qty-input {
            width: 60px;
            text-align: center;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            padding: 2px;
        }

        .remove-item {
            color: #e53e3e;
            cursor: pointer;
            font-size: 14px;
        }

        .cart-footer {
            padding: 20px;
            border-top: 1px solid #edf2f7;
            background: #f8fafc;
            border-radius: 0 0 15px 15px;
        }

        .total-box {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-weight: 700;
            font-size: 18px;
        }

        /* Form Details Modal Content Style */
        .form-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-details .form-group {
            margin-bottom: 0;
        }

        .form-details label {
            font-size: 11px;
            text-transform: uppercase;
            color: #718096;
            margin-bottom: 3px;
            display: block;
        }

        /* Pulse Animation for Add */
        @keyframes pulse-purple {
            0% {
                box-shadow: 0 0 0 0 rgba(118, 75, 162, 0.4);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(118, 75, 162, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(118, 75, 162, 0);
            }
        }

        .pulse-add {
            animation: pulse-purple 1s infinite;
        }

        .market-list-mode {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            background: #e2e8f0;
            cursor: pointer;
            transition: all 0.2s;
        }

        .market-list-mode.active {
            background: #764ba2;
            color: white;
        }
    </style>

    <div class="app-title">
        <div>
            <h1><i class="fa fa-pencil"></i> Edit Market List</h1>
            <p>Modify items in: {{ $shoppingList->name }}</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="market-list-mode active mr-3" id="pickerModeBtn" onclick="setMode('picker')">Picker Mode</div>
            <div class="market-list-mode" id="classicModeBtn" onclick="setMode('classic')">Classic Table</div>
        </div>
    </div>

    <form action="{{ route('admin.restaurants.shopping-list.update', $shoppingList->id) }}" method="POST" id="mainForm">
        @csrf
        @method('PUT')

        {{-- Hidden Form Data --}}
        <div id="hiddenItemsContainer"></div>

        <div id="pickerContainer" class="picker-container">
            <!-- Sidebar -->
            <div class="category-sidebar">
                <h6 class="text-uppercase text-muted mb-3 font-weight-bold" style="font-size: 11px;">Categories</h6>
                <button type="button" class="category-btn active" onclick="filterCategory('all', this)">All Items</button>
                @php
                    $categories = $products->pluck('category')->unique()->sort();
                @endphp
                @foreach($categories as $cat)
                    <button type="button" class="category-btn" onclick="filterCategory('{{ $cat }}', this)">
                        {{ ucfirst(str_replace('_', ' ', $cat)) }}
                    </button>
                @endforeach
                <button type="button" class="category-btn text-primary border-top mt-3" onclick="addManualItem()">
                    <i class="fa fa-plus-circle"></i> Custom Item
                </button>
            </div>

            <!-- Content -->
            <div class="product-area">
                <div class="search-container">
                    <i class="fa fa-search search-icon"></i>
                    <input type="text" class="search-input" id="searchItems"
                        placeholder="Search products, brands, or descriptions..." oninput="handleSearch()">
                </div>

                <div class="product-grid" id="productGrid">
                    @foreach($products as $product)
                        @if($product->variants->count() > 0)
                            @foreach($product->variants as $variant)
                                @php
                                    $cartItem = [
                                        "product_id" => $product->id,
                                        "variant_id" => $variant->id,
                                        "name" => $product->name . ($variant->variant_name && strtolower($variant->variant_name) != "standard" ? " - " . $variant->variant_name : ""),
                                        "unit" => $variant->purchasing_unit ?: $variant->receiving_unit ?: $variant->measurement ?: "pcs",
                                        "price" => 0,
                                        "category" => $product->category
                                    ];
                                @endphp
                                <div class="product-card" data-category="{{ $product->category }}"
                                    data-name="{{ strtolower($product->name . ' ' . $variant->variant_name) }}"
                                    onclick='addToCart(@json($cartItem))'>
                                    <span class="cat-tag">{{ $product->category }}</span>
                                    <h5>{{ $product->name }}</h5>
                                    @if($variant->variant_name && strtolower($variant->variant_name) != 'standard')
                                        <small class="text-muted d-block mb-2">{{ $variant->variant_name }}</small>
                                    @endif
                                    <div class="price">
                                        {{ $variant->purchasing_unit ?: $variant->receiving_unit ?: $variant->measurement ?: 'unit' }}
                                    </div>
                                    <button type="button" class="add-btn"><i class="fa fa-plus"></i></button>
                                </div>
                            @endforeach
                        @else
                            @php
                                $cartItem = [
                                    "product_id" => $product->id,
                                    "variant_id" => null,
                                    "name" => $product->name,
                                    "unit" => $product->unit ?: "pcs",
                                    "price" => 0,
                                    "category" => $product->category
                                ];
                            @endphp
                            <div class="product-card" data-category="{{ $product->category }}"
                                data-name="{{ strtolower($product->name) }}" onclick='addToCart(@json($cartItem))'>
                                <span class="cat-tag">{{ $product->category }}</span>
                                <h5>{{ $product->name }}</h5>
                                <div class="price">pcs</div>
                                <button type="button" class="add-btn"><i class="fa fa-plus"></i></button>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Cart Sidebar -->
            <div class="cart-sidebar">
                <div class="cart-header">
                    <h5 class="mb-0 font-weight-bold"><i class="fa fa-shopping-cart"></i> Your List</h5>
                    <small id="itemCount">0 items selected</small>
                </div>

                <div class="p-3 bg-light border-bottom">
                    <div class="form-details">
                        <div class="form-group">
                            <label>Purchaser Name</label>
                            <input type="text" name="name" class="form-control form-control-sm"
                                placeholder="Name of person buying..." required value="{{ $shoppingList->name }}">
                        </div>
                        <div class="form-group">
                            <label>Market Name</label>
                            <input type="text" name="market_name" class="form-control form-control-sm"
                                placeholder="City Market" value="{{ $shoppingList->market_name }}">
                        </div>
                    </div>
                    <div class="form-group mb-2">
                        <label>LPO Budget Period</label>
                        <select name="lpo_id" class="form-control form-control-sm">
                            <option value="">-- Standalone (No Budget) --</option>
                            @foreach($lpos as $lpo)
                                <option value="{{ $lpo->id }}" {{ $shoppingList->lpo_id == $lpo->id ? 'selected' : '' }}>
                                    LPO #{{ $lpo->id }} ({{ $lpo->start_date->format('d M') }} -
                                    {{ $lpo->end_date->format('d M Y') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label style="font-size: 11px; text-transform: uppercase;">Estimated Date</label>
                        <input type="date" name="shopping_date" class="form-control form-control-sm"
                            value="{{ $shoppingList->shopping_date ? $shoppingList->shopping_date->format('Y-m-d') : '' }}">
                    </div>
                </div>

                <div class="cart-items">
                    <div id="cartContent"></div>
                    <div class="text-center text-muted mt-5" id="emptyCart">
                        <i class="fa fa-shopping-basket fa-3x mb-3 opacity-25"></i>
                        <p>No items picked yet.</p>
                    </div>
                </div>

                <div class="cart-footer">
                    <div class="total-box">
                        <span>EST. TOTAL</span>
                        <span id="displayTotal">0.00 TZS</span>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block p-3 font-weight-bold"
                        style="border-radius: 12px;">
                        <i class="fa fa-check-circle mr-2"></i> UPDATE LIST
                    </button>
                    <a href="{{ route('admin.restaurants.shopping-list.index') }}"
                        class="btn btn-link btn-block text-muted btn-sm mt-2">Discard Changes</a>
                </div>
            </div>
        </div>

        {{-- Classic Table Mode (Hidden by default) --}}
        <div id="classicContainer" class="tile d-none">
            <div class="tile-title-w-btn">
                <h3 class="title">Manual Entry</h3>
                <button type="button" class="btn btn-primary btn-sm" onclick="addManualItem()"><i class="fa fa-plus"></i>
                    Add Row</button>
            </div>
            <div class="tile-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Qty</th>
                            <th>Unit</th>
                            <th>Est. Total Price</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="classicTbody">
                        {{-- Managed by JS same as cart --}}
                    </tbody>
                </table>
            </div>
        </div>
    </form>

    {{-- Manual Item Modal --}}
    <div class="modal fade" id="manualItemModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Add Custom Item</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Item Name</label>
                        <input type="text" id="manual_name" class="form-control" placeholder="e.g. Special Seasoning">
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>Category</label>
                                <select id="manual_cat" class="form-control">
                                    <option value="other">Other</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}">{{ ucfirst(str_replace('_', ' ', $cat)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Unit</label>
                                <input type="text" id="manual_unit" class="form-control" placeholder="pcs, kg, etc.">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Estimated Price per Unit (TZS)</label>
                        <input type="number" id="manual_price" class="form-control" placeholder="e.g. 5000">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="confirmManualAdd()">Add to List</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let cart = [];
        let viewMode = 'picker';
        const existingItems = @json($shoppingList->items);
        const lpos = @json($lpos);
        const unitOptions = ['pcs', 'kg', 'g', 'liters', 'ml', 'Sado', 'Debe', 'boxes', 'bottles', 'rolls', 'packs', 'cartons', 'bags', 'bunches', 'crates', 'trays', 'other'];

        document.addEventListener('DOMContentLoaded', () => {
            // Listen for LPO change
            const lpoSelect = document.querySelector('select[name="lpo_id"]');
            if (lpoSelect) {
                lpoSelect.addEventListener('change', function () {
                    syncPricesWithLPO(this.value);
                });
            }

            if (existingItems && existingItems.length > 0) {
                existingItems.forEach(item => {
                    cart.push({
                        id: item.id,
                        product_id: item.product_id || null,
                        variant_id: item.product_variant_id || null,
                        name: item.product_name,
                        unit: item.unit || 'pcs',
                        price: item.estimated_price / (item.quantity || 1), // Store unit price for calculations
                        quantity: item.quantity || 1,
                        category: item.category || 'other',
                        purchase_request_id: item.purchase_request_id || null
                    });
                });
                renderCart();
            }
        });

        function addToCart(item) {
            // Auto-fill price from selected LPO if available
            const lpoId = document.querySelector('select[name="lpo_id"]').value;
            if (lpoId) {
                const selectedLpo = lpos.find(l => l.id == lpoId);
                if (selectedLpo && selectedLpo.items) {
                    const lpoItem = selectedLpo.items.find(li =>
                        (li.product_variant_id && li.product_variant_id == item.variant_id) ||
                        (!li.product_variant_id && li.item_name && li.item_name.toLowerCase() === item.name.toLowerCase())
                    );
                    if (lpoItem) {
                        item.price = lpoItem.unit_price;
                    }
                }
            }

            // Check if already in cart
            const existing = cart.find(i =>
                (i.variant_id && i.variant_id === item.variant_id) ||
                (!i.variant_id && i.product_id && i.product_id === item.product_id && i.name === item.name) ||
                (!item.product_id && i.name === item.name)
            );

            if (existing) {
                existing.quantity = (parseFloat(existing.quantity) || 0) + 1;
            } else {
                item.quantity = item.quantity || 1;
                cart.push(item);
            }

            renderCart();
            showNotification('Item added to list');
        }

        function renderCart() {
            const container = document.getElementById('cartContent');
            const hiddenContainer = document.getElementById('hiddenItemsContainer');
            const itemCount = document.getElementById('itemCount');
            const emptyCart = document.getElementById('emptyCart');
            const displayTotal = document.getElementById('displayTotal');
            const classicTbody = document.getElementById('classicTbody');

            let cartHtml = '';
            let hiddenHtml = '';
            let classicHtml = '';
            let total = 0;

            if (cart.length === 0) {
                emptyCart.style.display = 'block';
                container.innerHTML = '';
                classicTbody.innerHTML = '';
            } else {
                emptyCart.style.display = 'none';

                cart.forEach((item, index) => {
                    const priceVal = Number(item.price) || 0;
                    const qtyVal = Number(item.quantity) || 0;
                    const itemTotal = qtyVal * priceVal;
                    total += itemTotal;

                    // Render Cart Item
                    cartHtml += `
                                                    <div class="cart-item">
                                                        <div class="cart-item-info">
                                                            <div class="cart-item-title">${item.name}</div>
                                                            <div class="cart-item-controls mt-2">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="mr-2">
                                                                        <small class="d-block text-muted">Quantity</small>
                                                                        <input type="number" step="0.01" class="qty-input" value="${item.quantity}" 
                                                                               oninput="liveUpdate(${index}, 'quantity', this.value)">
                                                                        <small class="text-muted ml-1">${item.unit}</small>
                                                                    </div>
                                                                    <div>
                                                                        <small class="d-block text-muted">Unit Price</small>
                                                                        <input type="number" step="1" class="qty-input" value="${item.price}" 
                                                                               style="width: 90px" placeholder="Price"
                                                                               oninput="liveUpdate(${index}, 'price', this.value)">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="text-right">
                                                            <small class="text-muted d-block" style="font-size: 9px;">Line Total</small>
                                                            <div class="font-weight-bold text-primary" style="font-size: 13px;" id="lineTotal-${index}">${itemTotal.toLocaleString()}</div>
                                                            <i class="fa fa-times remove-item mt-2" onclick="removeFromCart(${index})" title="Remove item"></i>
                                                        </div>
                                                    </div>
                                                `;

                    // Render Classic Table Row
                    classicHtml += `
                                                        <tr>
                                                            <td>${item.name}</td>
                                                            <td><input type="number" step="0.01" class="form-control form-control-sm" value="${item.quantity}" oninput="liveUpdate(${index}, 'quantity', this.value)"></td>
                                                            <td>
                                                                <select class="form-control form-control-sm" onchange="updateCartItem(${index}, 'unit', this.value)">
                                                                    ${unitOptions.map(u => `<option value="${u}" ${item.unit === u ? 'selected' : ''}>${u}</option>`).join('')}
                                                                </select>
                                                            </td>
                                                            <td><input type="number" step="1" class="form-control form-control-sm" value="${item.price}" oninput="liveUpdate(${index}, 'price', this.value)"></td>
                                                            <td><button type="button" class="btn btn-danger btn-sm" onclick="removeFromCart(${index})"><i class="fa fa-trash"></i></button></td>
                                                        </tr>
                                                    `;

                    // Hidden Inputs
                    hiddenHtml += `
                                                        <input type="hidden" name="items[${index}][id]" value="${item.id || ''}">
                                                        <input type="hidden" name="items[${index}][product_id]" value="${item.product_id || ''}">
                                                        <input type="hidden" name="items[${index}][product_variant_id]" value="${item.variant_id || ''}">
                                                        <input type="hidden" name="items[${index}][product_name]" value="${item.name}">
                                                        <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}" id="hiddenQty-${index}">
                                                        <input type="hidden" name="items[${index}][unit]" value="${item.unit}">
                                                        <input type="hidden" name="items[${index}][estimated_price]" value="${itemTotal || 0}" id="hiddenPrice-${index}">
                                                        <input type="hidden" name="items[${index}][category]" value="${item.category || 'other'}">
                                                        <input type="hidden" name="items[${index}][purchase_request_id]" value="${item.purchase_request_id || ''}">
                                                    `;
                });

                container.innerHTML = cartHtml;
                classicTbody.innerHTML = classicHtml;
            }

            hiddenContainer.innerHTML = hiddenHtml;
            itemCount.textContent = `${cart.length} items picked`;
            displayTotal.textContent = total.toLocaleString() + ' TZS';
        }

        function liveUpdate(index, key, value) {
            cart[index][key] = Number(value) || 0;

            let total = 0;
            cart.forEach((item, idx) => {
                const itemTotal = (Number(item.quantity) || 0) * (Number(item.price) || 0);
                total += itemTotal;

                const lineTotalEl = document.getElementById(`lineTotal-${idx}`);
                if (lineTotalEl) lineTotalEl.textContent = itemTotal.toLocaleString();

                const hQty = document.getElementById(`hiddenQty-${idx}`);
                if (hQty) hQty.value = item.quantity;

                const hPrice = document.getElementById(`hiddenPrice-${idx}`);
                if (hPrice) hPrice.value = itemTotal;
            });

            document.getElementById('displayTotal').textContent = total.toLocaleString() + ' TZS';
        }

        function updateCartItem(index, key, value) {
            if (key === 'quantity' || key === 'price') {
                cart[index][key] = Number(value) || 0;
            } else {
                cart[index][key] = value;
            }
            renderCart();
        }

        function removeFromCart(index) {
            // Confirm if it's an existing item being deleted
            if (cart[index].id && !confirm('Remove this existing item from the list?')) return;
            cart.splice(index, 1);
            renderCart();
        }

        function filterCategory(cat, btn) {
            document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            document.querySelectorAll('.product-card').forEach(card => {
                if (cat === 'all' || card.dataset.category === cat) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function handleSearch() {
            const term = document.getElementById('searchItems').value.toLowerCase();
            document.querySelectorAll('.product-card').forEach(card => {
                if (card.dataset.name.includes(term)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function setMode(mode) {
            viewMode = mode;
            if (mode === 'classic') {
                document.getElementById('pickerContainer').classList.add('d-none');
                document.getElementById('classicContainer').classList.remove('d-none');
                document.getElementById('classicModeBtn').classList.add('active');
                document.getElementById('pickerModeBtn').classList.remove('active');
            } else {
                document.getElementById('pickerContainer').classList.remove('d-none');
                document.getElementById('classicContainer').classList.add('d-none');
                document.getElementById('classicModeBtn').classList.remove('active');
                document.getElementById('pickerModeBtn').classList.add('active');
            }
        }

        function addManualItem() {
            $('#manualItemModal').modal('show');
        }

        function confirmManualAdd() {
            const name = document.getElementById('manual_name').value;
            const unit = document.getElementById('manual_unit').value || 'pcs';
            const cat = document.getElementById('manual_cat').value;
            const price = document.getElementById('manual_price').value || 0;

            if (!name) return alert('Item name is required');

            addToCart({
                product_id: null,
                variant_id: null,
                name: name,
                unit: unit,
                price: parseFloat(price),
                category: cat
            });

            $('#manualItemModal').modal('hide');
            document.getElementById('manual_name').value = '';
            document.getElementById('manual_price').value = '';
        }

        function showNotification(msg) {
            // Simple toast simulation
            const toast = document.createElement('div');
            toast.style.cssText = `
                                                                position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%);
                                                                background: rgba(45, 55, 72, 0.9); color: white; padding: 10px 20px;
                                                                border-radius: 30px; z-index: 9999; font-size: 14px;
                                                                box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                                                            `;
            toast.textContent = msg;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 2000);
        }

        function syncPricesWithLPO(lpoId) {
            if (!lpoId) return;
            const selectedLpo = lpos.find(l => l.id == lpoId);
            if (!selectedLpo || !selectedLpo.items) return;

            cart.forEach(item => {
                const lpoItem = selectedLpo.items.find(li =>
                    (li.product_variant_id && li.product_variant_id == item.variant_id) ||
                    (!li.product_variant_id && li.item_name && li.item_name.toLowerCase() === item.name.toLowerCase())
                );
                if (lpoItem) {
                    item.price = lpoItem.unit_price;
                }
            });
            renderCart();
            showNotification('Prices updated from LPO Budget');
        }
    </script>
@endsection
