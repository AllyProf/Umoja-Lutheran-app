@extends('dashboard.layouts.app')

@section('content')
    <div class="app-title">
        <div>
            <h1><i class="fa fa-pencil"></i> Edit Shopping List</h1>
            <p>Modify items in: {{ $shoppingList->name }}</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.restaurants.shopping-list.index') }}">Shopping Lists</a>
            </li>
            <li class="breadcrumb-item active"><a href="#">Edit</a></li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <form action="{{ route('admin.restaurants.shopping-list.update', $shoppingList->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="tile">
                    <h3 class="tile-title">List Details</h3>
                    <div class="tile-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">List Name</label>
                                    <input class="form-control" type="text" name="name" value="{{ $shoppingList->name }}"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Market Name</label>
                                    <input class="form-control" type="text" name="market_name"
                                        value="{{ $shoppingList->market_name }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Shopping Date</label>
                                    <input class="form-control" type="date" name="shopping_date"
                                        value="{{ $shoppingList->shopping_date ? $shoppingList->shopping_date->format('Y-m-d') : '' }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Notes</label>
                            <textarea class="form-control" name="notes" rows="2">{{ $shoppingList->notes }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="tile">
                    <div class="tile-title-w-btn">
                        <h3 class="title">Items</h3>
                        <button type="button" class="btn btn-primary btn-sm" onclick="addItemRow()"><i
                                class="fa fa-plus"></i> Add Item</button>
                        <button type="button" class="btn btn-outline-primary btn-sm ml-2" onclick="openBulkModal()"><i
                                class="fa fa-list"></i> Bulk Add Items</button>
                    </div>
                    <div class="tile-body">
                        <table class="table table-bordered" id="itemsTable">
                            <thead>
                                <tr>
                                    <th width="35%">Item / Ingredient</th>
                                    <th width="15%">Quantity</th>
                                    <th width="15%">Unit</th>
                                    <th width="15%">Est. Unit Price</th>
                                    <th width="15%">Total Est. Price</th>
                                    <th width="5%"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsContainer">
                                @foreach($shoppingList->items as $index => $item)
                                    <tr id="row-{{ $index }}">
                                        <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                        <td>
                                            <div class="input-group mb-2">
                                                <select class="form-control product-select"
                                                    name="items[{{ $index }}][product_id]"
                                                    onchange="updateProductVariants(this, {{ $index }})">
                                                    <option value="">-- Select Product --</option>
                                                    @foreach($products as $p)
                                                        <option value="{{ $p->id }}" data-cat="{{ $p->category }}"
                                                            data-name="{{ $p->name }}" {{ $item->product_id == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                                    @endforeach
                                                </select>
                                                <input type="text"
                                                    class="form-control product-manual {{ $item->product_id ? 'd-none' : '' }}"
                                                    name="items[{{ $index }}][product_name]" value="{{ $item->product_name }}"
                                                    placeholder="Item Name">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary" type="button"
                                                        onclick="toggleManual(this, {{ $index }})" title="Toggle Search/Type"><i
                                                            class="fa fa-pencil"></i></button>
                                                </div>
                                            </div>
                                        </td>
                                        <input type="hidden" name="items[{{ $index }}][category]" id="cat-{{ $index }}" value="{{ $item->category }}">
                                        <input type="hidden" name="items[{{ $index }}][product_variant_id]" id="variant-{{ $index }}" value="{{ $item->product_variant_id }}">
                                        <td>
                                            <input type="number" step="0.01" class="form-control item-quantity"
                                                name="items[{{ $index }}][quantity]" required 
                                                value="{{ $item->quantity + 0 }}" onchange="updateLineTotal({{ $index }})">
                                        </td>
                                        <td>
                                            <select class="form-control unit-select-dropdown" name="items[{{ $index }}][unit]"
                                                id="unit-{{ $index }}">
                                                @foreach(['pcs', 'liters', 'ml', 'kg', 'g', 'Sado', 'Debe', 'boxes', 'bottles', 'rolls', 'packs', 'cartons', 'bags', 'bunches', 'crates', 'trays', 'custom'] as $u)
                                                    <option value="{{ $u }}" {{ $item->unit == $u ? 'selected' : '' }}>
                                                        {{ ucfirst($u) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            @php
                                                $unitPrice = $item->quantity > 0 ? $item->estimated_price / $item->quantity : 0;
                                            @endphp
                                            <input type="number" step="0.01" class="form-control est-unit-price"
                                                value="{{ round($unitPrice, 2) }}" placeholder="Price/Unit"
                                                onchange="updateLineTotal({{ $index }})">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control estimated-price-input total-est-price"
                                                name="items[{{ $index }}][estimated_price]"
                                                value="{{ $item->estimated_price + 0 }}" onchange="calculateTotal()">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm"
                                                onclick="removeRow({{ $index }})"><i class="fa fa-trash"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="tile-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="alert alert-info">
                                    <strong>Total Estimated Cost:</strong>
                                    <span id="totalEstimatedCost" style="font-size: 18px; font-weight: bold;">0.00
                                        TZS</span>
                                </div>
                            </div>
                            <div class="col-md-6 text-right">
                                <button class="btn btn-primary" type="submit"><i
                                        class="fa fa-fw fa-lg fa-check-circle"></i>Update List</button>
                                <a class="btn btn-secondary" href="{{ route('admin.restaurants.shopping-list.index') }}"><i
                                        class="fa fa-fw fa-lg fa-times-circle"></i>Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        var availableProducts = @json($products);
        // Continue counting from existing items to avoid ID conflicts in DOM
        let rowCount = {{ $shoppingList->items->count() }};

        function addItemRow() {
            const tbody = document.getElementById('itemsContainer');
            const tr = document.createElement('tr');
            tr.id = `row-${rowCount}`;

            let productOptions = '<option value="">-- Select Product --</option>';
            availableProducts.forEach(p => {
                productOptions += `<option value="${p.id}" data-cat="${p.category}" data-name="${p.name}">${p.name}</option>`;
            });

            tr.innerHTML = `
                        <td>
                            <div class="input-group mb-2">
                                <select class="form-control product-select" name="items[${rowCount}][product_id]" onchange="updateProductVariants(this, ${rowCount})">
                                    ${productOptions}
                                </select>
                                <input type="text" class="form-control product-manual d-none" name="items[${rowCount}][product_name]" placeholder="Item Name">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" onclick="toggleManual(this, ${rowCount})" title="Toggle Search/Type"><i class="fa fa-pencil"></i></button>
                                </div>
                            </div>
                        </td>
                        <input type="hidden" name="items[${rowCount}][category]" id="cat-${rowCount}" value="other">
                        <input type="hidden" name="items[${rowCount}][product_variant_id]" id="variant-${rowCount}" value="">
                        <td>
                        <input type="number" step="0.01" class="form-control item-quantity" name="items[${rowCount}][quantity]" required placeholder="Qty" onchange="updateLineTotal(${rowCount})">
                    </td>
                    <td>
                        <select class="form-control unit-select-dropdown" name="items[${rowCount}][unit]" id="unit-${rowCount}">
                            <option value="pcs">Pieces (pcs)</option>
                            <option value="liters">Liters (L)</option>
                            <option value="ml">Milliliters (ml)</option>
                            <option value="kg">Kilograms (kg)</option>
                            <option value="g">Grams (g)</option>
                            <option value="Sado">Sado</option>
                            <option value="Debe">Debe</option>
                            <option value="boxes">Boxes</option>
                            <option value="bottles">PIC (Bottle)</option>
                            <option value="rolls">Rolls</option>
                            <option value="packs">Packs</option>
                            <option value="cartons">Cartons</option>
                            <option value="bags">Bags</option>
                            <option value="bunches">Bunches</option>
                            <option value="crates">Crates</option>
                            <option value="trays">Trays</option>
                            <option value="custom">Custom Unit</option>
                        </select>
                    </td>
                    <td>
                        <input type="number" step="0.01" class="form-control est-unit-price" placeholder="Price/Unit" onchange="updateLineTotal(${rowCount})">
                    </td>
                    <td>
                        <input type="number" step="0.01" class="form-control estimated-price-input total-est-price" name="items[${rowCount}][estimated_price]" placeholder="0.00" onchange="calculateTotal()">
                    </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(${rowCount})"><i class="fa fa-trash"></i></button>
                        </td>
                    `;

            tbody.appendChild(tr);
            rowCount++;
        }

        function removeRow(id) {
            document.getElementById(`row-${id}`).remove();
        }

        function toggleManual(btn, id) {
            const row = document.getElementById(`row-${id}`);
            const select = row.querySelector('.product-select');
            const manual = row.querySelector('.product-manual');

            if (manual.classList.contains('d-none')) {
                manual.classList.remove('d-none');
                // select.parentElement.classList.add('d-none'); // Removed invalid logic
                select.style.display = 'none';
                select.value = '';
            } else {
                manual.classList.add('d-none');
                // select.parentElement.classList.remove('d-none'); // Ensure wrapper is visible
                select.style.display = 'block';
                manual.value = '';
            }
        }

        function updateProductVariants(select, id) {
            const productId = select.value;
            const categorySelect = document.getElementById(`cat-${id}`);
            const unitSelect = document.getElementById(`unit-${id}`);
            const variantSelect = document.getElementById(`variant-${id}`);

            if (!productId) return;

            // Find product in availableProducts
            const product = availableProducts.find(p => p.id == productId);
            if (product) {
                // Update category
                if(categorySelect) categorySelect.value = product.category || 'other';

                // Automatically select unit based on product variant
                if (unitSelect && product.variants && product.variants.length > 0) {
                    const firstVariant = product.variants[0];
                    if (variantSelect) variantSelect.value = firstVariant.id;
                    
                    // Prioritize purchasing_unit as requested
                    let unit = firstVariant.purchasing_unit || firstVariant.receiving_unit || firstVariant.measurement || '';

                    if (unit) {
                        let found = false;
                        for (let i = 0; i < unitSelect.options.length; i++) {
                            if (unitSelect.options[i].value.toLowerCase() === unit.toLowerCase() ||
                                (unitSelect.options[i].value === 'liters' && (unit.toLowerCase() === 'l' || unit.toLowerCase() === 'litre' || unit.toLowerCase() === 'litres')) ||
                                (unitSelect.options[i].value === 'pcs' && (unit.toLowerCase() === 'piece' || unit.toLowerCase() === 'pieces'))) {
                                unitSelect.value = unitSelect.options[i].value;
                                found = true;
                                break;
                            }
                        }

                        // Fallbacks for common names if exact match not found
                        if (!found) {
                            if (unit.toLowerCase().includes('sado')) unitSelect.value = 'Sado';
                            else if (unit.toLowerCase().includes('debe')) unitSelect.value = 'Debe';
                            else if (unit.toLowerCase().includes('kg')) unitSelect.value = 'kg';
                            else if (unit.toLowerCase().includes('ltr')) unitSelect.value = 'liters';
                            else if (unit.toLowerCase().includes('ml')) unitSelect.value = 'ml';
                            else if (unit.toLowerCase().includes('carton')) unitSelect.value = 'cartons';
                            else if (unit.toLowerCase().includes('box')) unitSelect.value = 'boxes';
                        }
                    }
                }
            }
        }

        function updateLineTotal(id) {
            const row = document.getElementById(`row-${id}`);
            if (!row) return;
            const qtyInput = row.querySelector('.item-quantity');
            const unitPriceInput = row.querySelector('.est-unit-price');
            const totalInput = row.querySelector('.total-est-price');
            
            const qty = parseFloat(qtyInput.value) || 0;
            const unitPrice = parseFloat(unitPriceInput.value) || 0;
            
            totalInput.value = (qty * unitPrice).toFixed(2);
            calculateTotal();
        }

        // Calculate total estimated cost
        function calculateTotal() {
            let total = 0;
            document.querySelectorAll('.estimated-price-input').forEach(function (input) {
                const value = parseFloat(input.value) || 0;
                total += value;
            });
            document.getElementById('totalEstimatedCost').textContent = total.toFixed(2) + ' TZS';
        }

        // Recalculate when page loads (for existing items)
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize existing rows
            document.querySelectorAll('.product-select').forEach((select, index) => {
                if (select.value) {
                    updateProductVariants(select, index);
                }
            });

            setTimeout(calculateTotal, 500);
        });
    </script>
    {{-- Bulk Add Modal --}}
    <div class="modal fade" id="bulkAddModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fa fa-list"></i> Bulk Add Items</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="input-group mb-3 text-center">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-search text-primary"></i></span>
                        </div>
                        <input type="text" id="productSearch" class="form-control" placeholder="Search products by name or category...">
                    </div>
                    <div id="productList" style="max-height: 450px; overflow-y: auto; padding: 10px; border: 1px solid #eee; border-radius: 4px;">
                        <!-- Products will be listed here -->
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <div class="mr-auto">
                        <span id="selectedCount" class="badge badge-info p-2">0 items selected</span>
                    </div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="addSelectedProducts()"><i class="fa fa-plus-circle"></i> Add Selected to List</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openBulkModal() {
            const productList = document.getElementById('productList');
            productList.innerHTML = '';
            
            // Group variants/products by category
            const grouped = {};
            availableProducts.forEach(p => {
                const catGroup = p.category || 'other';
                if (!grouped[catGroup]) grouped[catGroup] = [];
                
                const categoryTitle = catGroup.charAt(0).toUpperCase() + catGroup.slice(1).replace('_', ' ');
                
                if (p.variants && p.variants.length > 0) {
                    p.variants.forEach(v => {
                        let displayName = p.name;
                        // If there's more than one variant, or the variant name isn't generic, show it
                        if (p.variants.length > 1 || (v.variant_name && v.variant_name.toLowerCase() !== 'standard' && v.variant_name.toLowerCase() !== 'unit')) {
                            displayName += ` - ${v.variant_name}`;
                        }
                        
                        grouped[catGroup].push({
                            productId: p.id,
                            variantId: v.id,
                            displayName: displayName,
                            categoryName: categoryTitle.toLowerCase()
                        });
                    });
                } else {
                    grouped[catGroup].push({
                        productId: p.id,
                        variantId: '',
                        displayName: p.name,
                        categoryName: categoryTitle.toLowerCase()
                    });
                }
            });
            
            const sortedCategories = Object.keys(grouped).sort();
            
            sortedCategories.forEach(cat => {
                const catTitle = cat.charAt(0).toUpperCase() + cat.slice(1).replace('_', ' ');
                let html = `<div class="category-section mb-4" data-category="${cat}">
                    <h6 class="border-bottom pb-2 font-weight-bold text-primary">${catTitle}</h6>
                    <div class="row">`;
                
                grouped[cat].forEach(item => {
                    const uniqueId = item.variantId ? `v-${item.variantId}` : `p-${item.productId}`;
                    html += `<div class="col-md-4 mb-2 product-item" data-name="${item.displayName.toLowerCase()}" data-category-name="${item.categoryName}">
                        <div class="custom-control custom-checkbox p-2 border rounded hover-bg-light">
                            <input type="checkbox" class="custom-control-input product-check" 
                                id="bulk-${uniqueId}" 
                                data-product-id="${item.productId}" 
                                data-variant-id="${item.variantId}"
                                onchange="updateSelectedCount()">
                            <label class="custom-control-label d-block cursor-pointer" for="bulk-${uniqueId}">${item.displayName}</label>
                        </div>
                    </div>`;
                });
                
                html += `</div></div>`;
                productList.innerHTML += html;
            });
            
            updateSelectedCount();
            $('#bulkAddModal').modal('show');
        }

        function updateSelectedCount() {
            const count = document.querySelectorAll('.product-check:checked').length;
            document.getElementById('selectedCount').textContent = `${count} items selected`;
        }

        function addSelectedProducts() {
            const selected = document.querySelectorAll('.product-check:checked');
            if (selected.length === 0) {
                alert('Please select at least one item.');
                return;
            }

            selected.forEach(check => {
                const productId = check.dataset.productId;
                const variantId = check.dataset.variantId;
                
                addItemRow(); // This function manages rowCount
                const lastIndex = rowCount - 1;
                const row = document.getElementById(`row-${lastIndex}`);
                if (row) {
                    const productSelect = row.querySelector('.product-select');
                    if (productSelect) {
                        productSelect.value = productId;
                        updateProductVariants(productSelect, lastIndex);
                    }
                }
            });
            
            $('#bulkAddModal').modal('hide');
        }

        // Search functionality
        document.getElementById('productSearch').addEventListener('input', function() {
            const term = this.value.toLowerCase();
            document.querySelectorAll('.product-item').forEach(item => {
                const name = item.dataset.name;
                const catName = item.dataset.categoryName;
                if (name.includes(term) || catName.includes(term)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
            
            // Hide category titles if no products match
            document.querySelectorAll('.category-section').forEach(section => {
                const visibleProducts = Array.from(section.querySelectorAll('.product-item')).filter(i => i.style.display !== 'none').length;
                if (visibleProducts === 0 && term !== '') {
                    section.style.display = 'none';
                } else {
                    section.style.display = 'block';
                }
            });
        });
    </script>

    <style>
        .hover-bg-light:hover {
            background-color: #f8f9fa;
        }
        .cursor-pointer {
            cursor: pointer;
        }
        .d-none {
            display: none !important;
        }
    </style>
@endsection