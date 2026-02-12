@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-pencil"></i> Edit Shopping List</h1>
        <p>Modify items in: {{ $shoppingList->name }}</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.restaurants.shopping-list.index') }}">Shopping Lists</a></li>
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
                                <input class="form-control" type="text" name="name" value="{{ $shoppingList->name }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">Market Name</label>
                                <input class="form-control" type="text" name="market_name" value="{{ $shoppingList->market_name }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">Shopping Date</label>
                                <input class="form-control" type="date" name="shopping_date" value="{{ $shoppingList->shopping_date ? $shoppingList->shopping_date->format('Y-m-d') : '' }}">
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
                            @foreach($shoppingList->items as $index => $item)
                            <tr id="row-{{ $index }}">
                                <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                <td>
                                    <div class="input-group">
                                        <select class="form-control product-select" name="items[{{ $index }}][product_id]" onchange="updateRowDetails(this, {{ $index }})">
                                            <option value="">-- Select or Type New --</option>
                                            @foreach($products as $p)
                                                <option value="{{ $p->id }}" data-cat="{{ $p->category_name }}" data-name="{{ $p->name }}" {{ $item->product_id == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                            @endforeach
                                        </select>
                                        <input type="text" class="form-control product-manual {{ $item->product_id ? 'd-none' : '' }}" name="items[{{ $index }}][product_name]" value="{{ $item->product_name }}" placeholder="Item Name">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" onclick="toggleManual(this, {{ $index }})" title="Toggle Search/Type"><i class="fa fa-pencil"></i></button>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="items[{{ $index }}][category]" id="cat-{{ $index }}" value="{{ $item->category }}">
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control" name="items[{{ $index }}][quantity]" required value="{{ $item->quantity + 0 }}">
                                </td>
                                <td>
                                    <select class="form-control" name="items[{{ $index }}][unit]">
                                        @foreach(['pcs', 'kg', 'grams', 'litres', 'bunches', 'crates', 'packets', 'bags', 'tins', 'bottles', 'tubs', 'trays', 'punnets'] as $u)
                                            <option value="{{ $u }}" {{ $item->unit == $u ? 'selected' : '' }}>{{ ucfirst($u) }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control estimated-price-input" name="items[{{ $index }}][estimated_price]" value="{{ $item->estimated_price + 0 }}" onchange="calculateTotal()">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeRow({{ $index }})"><i class="fa fa-trash"></i></button>
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
                                <span id="totalEstimatedCost" style="font-size: 18px; font-weight: bold;">0.00 TZS</span>
                            </div>
                        </div>
                        <div class="col-md-6 text-right">
                            <button class="btn btn-primary" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i>Update List</button>
                            <a class="btn btn-secondary" href="{{ route('admin.restaurants.shopping-list.index') }}"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cancel</a>
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
        
        // Build options manually or clone - building string for simplicity
        let productOptions = '<option value="">-- Select or Type New --</option>';
        availableProducts.forEach(p => {
            productOptions += `<option value="${p.id}" data-cat="${p.category_name}" data-name="${p.name}">${p.name}</option>`;
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
                <input type="text" class="form-control" name="items[${rowCount}][category]" id="cat-${rowCount}" placeholder="Category">
            </td>
            <td>
                <input type="number" step="0.01" class="form-control" name="items[${rowCount}][quantity]" required placeholder="Qty">
            </td>
            <td>
                <select class="form-control" name="items[${rowCount}][unit]">
                    <option value="pcs">Pcs</option>
                    <option value="kg">Kg</option>
                    <option value="grams">Grams</option>
                    <option value="litres">Litres</option>
                    <option value="bunches">Bunches</option>
                    <option value="crates">Crates</option>
                    <option value="packets">Packets</option>
                    <option value="bags">Bags</option>
                    <option value="tins">Tins</option>
                    <option value="bottles">Bottles</option>
                    <option value="tubs">Tubs</option>
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
            select.value = '';
        } else {
            manual.classList.add('d-none');
            // select.parentElement.classList.remove('d-none'); // Ensure wrapper is visible
            select.style.display = 'block';
            manual.value = '';
        }
    }

    function updateRowDetails(select, id) {
        const option = select.options[select.selectedIndex];
        if (option.value) {
            document.getElementById(`cat-${id}`).value = option.getAttribute('data-cat') || 'Food';
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
    
    // Recalculate when page loads (for existing items)
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(calculateTotal, 500);
    });
    
    // Also recalculate when rows are removed
    const originalRemoveRow = removeRow;
    removeRow = function(id) {
        originalRemoveRow(id);
        calculateTotal();
    };
</script>
<style>
    .d-none { display: none !important; }
</style>
@endsection
