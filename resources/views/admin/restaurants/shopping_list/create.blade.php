@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-plus-square"></i> Create Shopping List</h1>
        <p>Prepare a new market list</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.restaurants.shopping-list.index') }}">Shopping Lists</a></li>
        <li class="breadcrumb-item active"><a href="#">Create</a></li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <form action="{{ route('admin.restaurants.shopping-list.store') }}" method="POST">
            @csrf
            <div class="tile">
                <h3 class="tile-title">List Details</h3>
                <div class="tile-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">List Name</label>
                                <input class="form-control" type="text" name="name" value="{{ $prefillName ?? '' }}" placeholder="e.g. Weekly Market Run" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">Market Name</label>
                                <input class="form-control" type="text" name="market_name" placeholder="e.g. City Market">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">Shopping Date</label>
                                <input class="form-control" type="date" name="shopping_date" value="{{ $prefillDate ?? date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="2"></textarea>
                    </div>
                </div>
            </div>

            <div class="tile">
                <div class="tile-title-w-btn">
                    <h3 class="title">Items</h3>
                    <button type="button" class="btn btn-primary btn-sm" onclick="addItemRow()"><i class="fa fa-plus"></i> Add Item</button>
                </div>
                <div class="tile-body">
                    <table class="table table-bordered" id="itemsTable">
                        <thead>
                            <tr>
                                <th width="30%">Item / Ingredient</th>
                                <th width="15%">Category</th>
                                <th width="15%">Quantity</th>
                                <th width="15%">Unit</th>
                                <th width="20%">Est. Price (Total)</th>
                                <th width="5%"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsContainer">
                            {{-- Rows will be added here --}}
                        </tbody>
                    </table>
                </div>
                <div class="tile-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="alert alert-info">
                                <strong>Total Estimated Cost:</strong> 
                                <span id="totalEstimatedCost" style="font-size: 18px; font-weight: bold;">0.00 TZS</span>
                            </div>
                        </div>
                        <div class="col-md-6 text-right">
                            <button class="btn btn-primary" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i>Save Template</button>
                            <a class="btn btn-secondary" href="{{ route('admin.restaurants.shopping-list.index') }}"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Data for JS suggestions --}}
<script>
    var availableProducts = @json($products);
</script>

<script>
    let rowCount = 0;

    function addItemRow() {
        const tbody = document.getElementById('itemsContainer');
        const tr = document.createElement('tr');
        tr.id = `row-${rowCount}`;
        
        let productOptions = '<option value="">-- Select or Type New --</option>';
        availableProducts.forEach(p => {
            productOptions += `<option value="${p.id}" data-cat="${p.category}" data-name="${p.name}">${p.name}</option>`;
        });

        tr.innerHTML = `
            <td>
                <div class="input-group">
                    <select class="form-control product-select" name="items[${rowCount}][product_id]" onchange="updateRowDetails(this, ${rowCount})">
                        ${productOptions}
                    </select>
                    <input type="text" class="form-control product-manual d-none" name="items[${rowCount}][product_name]" placeholder="Item Name">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" onclick="toggleManual(this, ${rowCount})" title="Toggle Search/Type"><i class="fa fa-pencil"></i></button>
                    </div>
                </div>
            </td>
            <td>
                <select class="form-control" name="items[${rowCount}][category]" id="cat-${rowCount}">
                    <option value="">Select Category</option>
                    <option value="meat_poultry">Meat & Poultry</option>
                    <option value="seafood">Seafood & Fish</option>
                    <option value="vegetables">Vegetables & Fruits</option>
                    <option value="dairy">Dairy & Eggs</option>
                    <option value="pantry_baking">Pantry & Baking</option>
                    <option value="spices_herbs">Spices & Herbs</option>
                    <option value="grains_pasta">Grains & Pasta</option>
                    <option value="bakery">Bakery & Bread</option>
                    <option value="oils_fats">Oils & Fats</option>
                    <option value="frozen_foods">Frozen Foods</option>
                    <option value="canned_goods">Canned & Packaged Goods</option>
                    <option value="beverages">Beverages (General)</option>
                    <option value="non_alcoholic_beverage">Soda / Soft Drinks</option>
                    <option value="energy_drinks">Energy Drinks</option>
                    <option value="juices">Juices</option>
                    <option value="water">Water</option>
                    <option value="alcoholic_beverage">Beer / Cider</option>
                    <option value="wines">Wines</option>
                    <option value="spirits">Spirits</option>
                    <option value="hot_beverages">Hot Beverages</option>
                    <option value="cocktails">Cocktails</option>
                    <option value="kitchen_disposables">Kitchen Disposables</option>
                    <option value="cleaning_supplies">Cleaning Supplies</option>
                    <option value="linens">Linens</option>
                    <option value="food">General Food</option>
                    <option value="other">Other</option>
                </select>
            </td>
            <td>
                <input type="number" step="0.01" class="form-control" name="items[${rowCount}][quantity]" required placeholder="Qty">
            </td>
            <td>
                <select class="form-control" name="items[${rowCount}][unit]">
                    <option value="pcs">Pieces (pcs)</option>
                    <option value="liters">Liters (L)</option>
                    <option value="ml">Milliliters (ml)</option>
                    <option value="kg">Kilograms (kg)</option>
                    <option value="g">Grams (g)</option>
                    <option value="boxes">Boxes</option>
                    <option value="bottles">PIC (Bottle)</option>
                    <option value="rolls">Rolls</option>
                    <option value="packs">Packs</option>
                    <option value="cartons">Cartons</option>
                    <option value="bags">Bags</option>
                    <option value="sado">Sado</option>
                    <option value="bunches">Bunches</option>
                    <option value="crates">Crates</option>
                    <option value="trays">Trays</option>
                    <option value="custom">Custom Unit</option>
                </select>
            </td>
            <td>
                <input type="number" step="0.01" class="form-control estimated-price-input" name="items[${rowCount}][estimated_price]" placeholder="0.00" onchange="calculateTotal()">
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
            // btn.innerHTML = '<i class="fa fa-list"></i>';
            select.value = '';
        } else {
            manual.classList.add('d-none');
            // select.parentElement.classList.remove('d-none'); // Ensure wrapper is visible
            select.style.display = 'block';
            // btn.innerHTML = '<i class="fa fa-pencil"></i>';
            manual.value = '';
        }
    }

    function updateRowDetails(select, id) {
        const option = select.options[select.selectedIndex];
        if (option.value) {
            document.getElementById(`cat-${id}`).value = option.getAttribute('data-cat') || 'food';
        }
    }
    
    // Calculate total estimated cost
    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.estimated-price-input').forEach(function(input) {
            const value = parseFloat(input.value) || 0;
            total += value;
        });
        document.getElementById('totalEstimatedCost').textContent = total.toFixed(2) + ' TZS';
    }
    
    // Recalculate when page loads (for pre-filled items)
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(calculateTotal, 500);
    });

    // Pre-fill items from purchase requests if available
    var prefillItems = @json($prefillItems ?? []);
    
    // Add initial row
    document.addEventListener('DOMContentLoaded', function() {
        if (prefillItems && prefillItems.length > 0) {
            // Pre-fill items from purchase requests
            prefillItems.forEach(function(item, index) {
                addItemRow();
                const currentRowIndex = rowCount - 1;
                const lastRow = document.getElementById(`row-${currentRowIndex}`);
                if (lastRow) {
                    // Set product name (manual input)
                    const manualInput = lastRow.querySelector('.product-manual');
                    const select = lastRow.querySelector('.product-select');
                    if (manualInput && select) {
                        manualInput.classList.remove('d-none');
                        select.style.display = 'none';
                        manualInput.value = item.product_name || '';
                    }
                    
                    // Set category
                    var mappedCategory = item.category || 'other';
                    
                    // Set category matching logic
                    const categorySelect = lastRow.querySelector(`select[name="items[${currentRowIndex}][category]"]`);
                    if (categorySelect) {
                        // Try to find exact match first
                        let exists = false;
                        for(let i=0; i<categorySelect.options.length; i++) {
                            if(categorySelect.options[i].value === mappedCategory) {
                                exists = true;
                                break;
                            }
                        }
                        
                        if (exists) {
                            categorySelect.value = mappedCategory;
                        } else {
                            // Fallback mappings for legacy data or small differences
                            const fallbackMap = {
                                'beverages': 'beverages',
                                'food': 'food',
                                'pantry': 'pantry_baking',
                                'baking': 'pantry_baking',
                                'soda': 'non_alcoholic_beverage',
                                'beer': 'alcoholic_beverage',
                                'wine': 'wines',
                                'spirit': 'spirits'
                            };
                            categorySelect.value = fallbackMap[mappedCategory] || 'other';
                        }
                    }
                    
                    // Set quantity
                    const quantityInput = lastRow.querySelector(`input[name="items[${currentRowIndex}][quantity]"]`);
                    if (quantityInput) {
                        quantityInput.value = item.quantity || '';
                    }
                    
                    // Set unit
                    const unitSelect = lastRow.querySelector(`select[name="items[${currentRowIndex}][unit]"]`);
                    if (unitSelect) {
                        var unitValue = item.unit || 'pcs';
                        // Handle common variations
                        if (unitValue === 'grams') unitValue = 'g';
                        if (unitValue === 'litres') unitValue = 'liters';
                        if (unitValue === 'packets') unitValue = 'packs';
                        
                        unitSelect.value = unitValue;
                    }
                    
                    // Set estimated price (empty, manager will fill)
                    const priceInput = lastRow.querySelector(`input[name="items[${currentRowIndex}][estimated_price]"]`);
                    if (priceInput) {
                        priceInput.value = item.estimated_price || '';
                        priceInput.placeholder = 'Enter price';
                        priceInput.classList.add('estimated-price-input');
                        priceInput.setAttribute('onchange', 'calculateTotal()');
                    }
                    
                    // Store purchase request ID as hidden input
                    if (item.purchase_request_id) {
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = `items[${currentRowIndex}][purchase_request_id]`;
                        hiddenInput.value = item.purchase_request_id;
                        lastRow.appendChild(hiddenInput);
                    }
                }
            });
        } else {
            // Add one empty row if no pre-fill data
            addItemRow();
        }
    });
</script>
<style>
    .d-none { display: none !important; }
</style>
@endsection
