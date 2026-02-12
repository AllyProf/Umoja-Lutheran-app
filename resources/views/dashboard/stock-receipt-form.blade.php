@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-plus"></i> {{ isset($type) ? ucfirst($type) : '' }} Stock Receipt</h1>
    <p>Receive products from suppliers</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.stock-receipts.index', ['type' => $type ?? '']) }}">{{ isset($type) ? ucfirst($type) : 'Stock' }} Receipts</a></li>
    <li class="breadcrumb-item"><a href="#">New {{ isset($type) ? ucfirst($type) : 'Receipt' }}</a></li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <form id="stockReceiptForm">
        @csrf
        
        <!-- Calculation Summary Card -->
        <div class="card bg-light mb-4">
          <div class="card-header">
            <h5 class="mb-0"><i class="fa fa-calculator"></i> Calculation Summary</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-3">
                <div class="text-center">
                  <h6 class="text-muted mb-1">Total <span id="packaging_label_1">Packages</span></h6>
                  <h4 id="calc_total_packages" class="text-primary mb-0">0</h4>
                  <small class="text-muted" id="packaging_unit_1">packages</small>
                </div>
              </div>
              <div class="col-md-3">
                <div class="text-center">
                  <h6 class="text-muted mb-1">Quantity Received</h6>
                  <h4 id="calc_quantity_received" class="text-info mb-0">0</h4>
                  <small class="text-muted" id="packaging_unit_2">packages</small>
                </div>
              </div>
              <div class="col-md-3">
                <div class="text-center">
                  <h6 class="text-muted mb-1">Total PIC</h6>
                  <h4 id="calc_total_bottles" class="text-success mb-0">0</h4>
                  <small class="text-muted">PIC(s)</small>
                </div>
              </div>
              <div class="col-md-3">
                <div class="text-center">
                  <h6 class="text-muted mb-1">Items per <span id="packaging_label_2">Package</span></h6>
                  <h4 id="calc_items_per_package" class="text-secondary mb-0">-</h4>
                </div>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-md-3">
                <div class="text-center">
                  <h6 class="text-muted mb-1">Profit per PIC</h6>
                  <h4 id="calc_profit_per_bottle" class="text-warning mb-0">0.00</h4>
                  <small class="text-muted">TSh</small>
                  <br><small class="text-muted">Selling Price - Buying Price</small>
                </div>
              </div>
              <div class="col-md-3">
                <div class="text-center">
                  <h6 class="text-muted mb-1">Total Buying Cost</h6>
                  <h4 id="calc_total_buying_cost" class="text-danger mb-0">0.00</h4>
                  <small class="text-muted">TSh</small>
                  <br><small class="text-muted">Total PIC × Buying Price</small>
                </div>
              </div>
              <div class="col-md-3">
                <div class="text-center">
                  <h6 class="text-muted mb-1">Discount Applied</h6>
                  <h4 id="calc_discount_amount" class="text-warning mb-0">0.00</h4>
                  <small class="text-muted">TSh</small>
                </div>
              </div>
              <div class="col-md-3">
                <div class="text-center">
                  <h6 class="text-muted mb-1">Total Profit</h6>
                  <h4 id="calc_total_profit" class="text-success mb-0">0.00</h4>
                  <small class="text-muted">TSh</small>
                  <br><small class="text-muted">After Discount</small>
                </div>
              </div>
            </div>
            <div class="alert alert-info mt-3 mb-0">
              <i class="fa fa-info-circle"></i> <strong>Note:</strong> Calculations update automatically as you enter values.
            </div>
          </div>
        </div>

        <h4 class="mb-4"><i class="fa fa-info-circle"></i> Stock Receipt Information</h4>
        
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="product_id">Product <span class="text-danger">*</span></label>
              <select class="form-control" id="product_id" name="product_id" required onchange="loadProductVariants()">
                <option value="">Select product...</option>
                @foreach($products as $product)
                <option value="{{ $product->id }}" data-type="{{ $product->type }}">
                  {{ $product->name }} ({{ $product->supplier->name }})
                </option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="product_variant_id">Product Variant <span class="text-danger">*</span></label>
              <select class="form-control" id="product_variant_id" name="product_variant_id" required onchange="updateCalculations(); updateMinimumStockLevel();">
                <option value="">Select product first...</option>
              </select>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="supplier_id">Supplier <span class="text-danger">*</span></label>
              <select class="form-control" id="supplier_id" name="supplier_id" required>
                <option value="">Select supplier...</option>
                @foreach($suppliers as $supplier)
                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="quantity_received_packages">Quantity Received (<span id="packaging_label_input">Packages</span>) <span class="text-danger">*</span></label>
              <input class="form-control" type="number" id="quantity_received_packages" 
                     name="quantity_received_packages" min="1" required 
                     oninput="updateCalculations()" placeholder="e.g., 10">
              <small class="form-text text-muted" id="packaging_hint">Select variant to see packaging type</small>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label for="buying_price_per_bottle">Buying Price per PIC (TSh) <span class="text-danger">*</span></label>
              <input class="form-control" type="number" id="buying_price_per_bottle" 
                     name="buying_price_per_bottle" step="0.01" min="0" required 
                     oninput="updateCalculations()">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="selling_price_per_bottle">Selling Price per PIC (TSh) <span class="text-danger">*</span></label>
              <input class="form-control" type="number" id="selling_price_per_bottle" 
                     name="selling_price_per_bottle" step="0.01" min="0" required 
                     oninput="updateCalculations()">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="discount_type">Discount Type (Optional)</label>
              <select class="form-control" id="discount_type" name="discount_type" onchange="toggleDiscountAmount()">
                <option value="none">No Discount</option>
                <option value="percentage">Percentage</option>
                <option value="fixed">Fixed Amount</option>
              </select>
            </div>
          </div>
        </div>

        <div class="row" id="discount_amount_row" style="display: none;">
          <div class="col-md-6">
            <div class="form-group">
              <label for="discount_amount">Discount Amount</label>
              <input class="form-control" type="number" id="discount_amount" 
                     name="discount_amount" step="0.01" min="0" 
                     oninput="updateCalculations()" placeholder="Enter discount amount">
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label for="received_date">Received Date <span class="text-danger">*</span></label>
              <input class="form-control" type="date" id="received_date" 
                     name="received_date" required value="{{ date('Y-m-d') }}">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="expiry_date">Expiry Date (Optional)</label>
              <input class="form-control" type="date" id="expiry_date" name="expiry_date">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="minimum_stock_level_unit">Stock Level Unit <span class="text-danger">*</span></label>
              <select class="form-control" id="minimum_stock_level_unit" 
                      name="minimum_stock_level_unit" required onchange="updateMinimumStockLevelHint()">
                <option value="bottles">PIC</option>
                <option value="packages" id="packages_option" style="display: none;">Packages</option>
              </select>
              <small class="form-text text-muted">Select whether to set minimum stock by PIC or packages</small>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="minimum_stock_level">Minimum Stock Level <span class="text-danger">*</span></label>
              <input class="form-control" type="number" id="minimum_stock_level" 
                     name="minimum_stock_level" min="0" required 
                     placeholder="e.g., 50" oninput="convertMinimumStockLevel()">
              <small class="form-text text-muted" id="minimum_stock_level_hint">Number of PIC below which to trigger low stock notification</small>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label for="notes">Notes</label>
              <textarea class="form-control" id="notes" name="notes" rows="3" 
                        placeholder="Any additional notes..."></textarea>
            </div>
          </div>
        </div>

        <div id="formAlert"></div>

        <div class="form-group mt-4">
          <button type="submit" class="btn btn-primary">
            <i class="fa fa-save"></i> Save Stock Receipt
          </button>
          <a href="{{ route('admin.stock-receipts.index', ['type' => $type ?? '']) }}" class="btn btn-secondary">
            <i class="fa fa-times"></i> Cancel
          </a>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<script>
const products = @json($products->keyBy('id'));
let currentVariant = null;

function loadProductVariants() {
  const productId = document.getElementById('product_id').value;
  const variantSelect = document.getElementById('product_variant_id');
  
  variantSelect.innerHTML = '<option value="">Loading variants...</option>';
  
  if (!productId) {
    variantSelect.innerHTML = '<option value="">Select product first...</option>';
    currentVariant = null;
    updateCalculations();
    return;
  }
  
      fetch('{{ route("admin.stock-receipts.get-variants", ":id") }}'.replace(':id', productId), {
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'application/json'
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.success && data.variants.length > 0) {
      variantSelect.innerHTML = '<option value="">Select variant...</option>';
      data.variants.forEach(variant => {
        const option = document.createElement('option');
        option.value = variant.id;
        option.textContent = `${variant.measurement} (${variant.packaging}, ${variant.items_per_package} items/pkg)`;
        option.dataset.itemsPerPackage = variant.items_per_package;
        option.dataset.packaging = variant.packaging;
        option.dataset.packagingName = variant.packaging_name || (variant.packaging.charAt(0).toUpperCase() + variant.packaging.slice(1));
        option.dataset.minimumStockLevel = variant.minimum_stock_level || '';
        option.dataset.minimumStockLevelUnit = variant.minimum_stock_level_unit || 'bottles';
        variantSelect.appendChild(option);
      });
      
      // Show packages option and update minimum stock level if variant is already selected
      const selectedOption = variantSelect.options[variantSelect.selectedIndex];
      if (selectedOption && selectedOption.value) {
        document.getElementById('packages_option').style.display = 'block';
        updateMinimumStockLevel();
      } else {
        document.getElementById('packages_option').style.display = 'none';
      }
    } else {
      variantSelect.innerHTML = '<option value="">No variants available</option>';
    }
    updateCalculations();
  })
  .catch(error => {
    console.error('Error:', error);
    variantSelect.innerHTML = '<option value="">Error loading variants</option>';
  });
}

function toggleDiscountAmount() {
  const discountType = document.getElementById('discount_type').value;
  const discountRow = document.getElementById('discount_amount_row');
  if (discountType !== 'none') {
    discountRow.style.display = 'block';
  } else {
    discountRow.style.display = 'none';
    document.getElementById('discount_amount').value = '';
  }
  updateCalculations();
}

// Get packaging name from packaging value
function getPackagingName(packaging) {
  const packagingMap = {
    'crates': 'Crates',
    'carton': 'Cartons',
    'boxes': 'Boxes',
    'bags': 'Bags',
    'packets': 'Packets'
  };
  return packagingMap[packaging] || 'Packages';
}

// Update minimum stock level field when variant changes
function updateMinimumStockLevel() {
  const variantSelect = document.getElementById('product_variant_id');
  const minimumStockLevelInput = document.getElementById('minimum_stock_level');
  const minimumStockLevelUnitSelect = document.getElementById('minimum_stock_level_unit');
  const selectedOption = variantSelect.options[variantSelect.selectedIndex];
  
  if (selectedOption && selectedOption.value) {
    // Show packages option
    document.getElementById('packages_option').style.display = 'block';
    
    // Set unit if variant has one
    if (selectedOption.dataset.minimumStockLevelUnit) {
      minimumStockLevelUnitSelect.value = selectedOption.dataset.minimumStockLevelUnit;
    }
    
    // Convert and set value based on unit
    if (selectedOption.dataset.minimumStockLevel) {
      const itemsPerPackage = parseInt(selectedOption.dataset.itemsPerPackage) || 1;
      const minimumStockLevelBottles = parseInt(selectedOption.dataset.minimumStockLevel) || 0;
      const unit = minimumStockLevelUnitSelect.value;
      
      if (unit === 'packages' && itemsPerPackage > 0) {
        // Convert from bottles to packages
        minimumStockLevelInput.value = Math.ceil(minimumStockLevelBottles / itemsPerPackage);
      } else {
        // Keep in PIC
        minimumStockLevelInput.value = minimumStockLevelBottles;
      }
    } else {
      minimumStockLevelInput.value = '';
    }
    
    updateMinimumStockLevelHint();
  } else {
    minimumStockLevelInput.value = '';
    document.getElementById('packages_option').style.display = 'none';
  }
}

// Update hint text based on selected unit
function updateMinimumStockLevelHint() {
  const variantSelect = document.getElementById('product_variant_id');
  const minimumStockLevelUnitSelect = document.getElementById('minimum_stock_level_unit');
  const hintElement = document.getElementById('minimum_stock_level_hint');
  const selectedOption = variantSelect.options[variantSelect.selectedIndex];
  const unit = minimumStockLevelUnitSelect.value;
  
  if (selectedOption && selectedOption.value) {
    const packaging = selectedOption.dataset.packagingName || 'packages';
    const packagingLower = packaging.toLowerCase();
    
    if (unit === 'packages') {
      hintElement.textContent = `Number of ${packagingLower} below which to trigger low stock notification`;
    } else {
      hintElement.textContent = 'Number of PIC below which to trigger low stock notification';
    }
  } else {
    hintElement.textContent = 'Number of bottles below which to trigger low stock notification';
  }
  
  // Convert existing value when unit changes
  convertMinimumStockLevel();
}

// Convert minimum stock level between packages and bottles when unit changes
function convertMinimumStockLevel() {
  const variantSelect = document.getElementById('product_variant_id');
  const minimumStockLevelInput = document.getElementById('minimum_stock_level');
  const minimumStockLevelUnitSelect = document.getElementById('minimum_stock_level_unit');
  const selectedOption = variantSelect.options[variantSelect.selectedIndex];
  
  if (!selectedOption || !selectedOption.value || !minimumStockLevelInput.value) {
    return;
  }
  
  const itemsPerPackage = parseInt(selectedOption.dataset.itemsPerPackage) || 1;
  const currentValue = parseFloat(minimumStockLevelInput.value) || 0;
  const unit = minimumStockLevelUnitSelect.value;
  
  // Store original value for conversion
  if (!minimumStockLevelInput.dataset.lastValue) {
    minimumStockLevelInput.dataset.lastValue = currentValue;
    minimumStockLevelInput.dataset.lastUnit = unit;
  }
  
  const lastValue = parseFloat(minimumStockLevelInput.dataset.lastValue) || currentValue;
  const lastUnit = minimumStockLevelInput.dataset.lastUnit || 'PIC';
  
  // Convert if unit changed
  if (lastUnit !== unit && itemsPerPackage > 0) {
    if (unit === 'packages' && lastUnit === 'bottles') {
      // Convert bottles to packages
      minimumStockLevelInput.value = Math.ceil(lastValue / itemsPerPackage);
    } else if (unit === 'bottles' && lastUnit === 'packages') {
      // Convert packages to PIC
      minimumStockLevelInput.value = lastValue * itemsPerPackage;
    }
    minimumStockLevelInput.dataset.lastValue = minimumStockLevelInput.value;
    minimumStockLevelInput.dataset.lastUnit = unit;
  }
}

// Update calculations based on simplified PIC logic
function updateCalculations() {
  const variantSelect = document.getElementById('product_variant_id');
  const quantityInput = document.getElementById('quantity_received_packages');
  const buyingPriceInput = document.getElementById('buying_price_per_bottle');
  const sellingPriceInput = document.getElementById('selling_price_per_bottle');
  const discountTypeSelect = document.getElementById('discount_type');
  const discountAmountInput = document.getElementById('discount_amount');
  
  const selectedOption = variantSelect.options[variantSelect.selectedIndex];
  const quantity = parseInt(quantityInput.value) || 0;
  const buyingPrice = parseFloat(buyingPriceInput.value) || 0;
  const sellingPrice = parseFloat(sellingPriceInput.value) || 0;
  const discountType = discountTypeSelect ? discountTypeSelect.value : 'none';
  const discountAmount = parseFloat(discountAmountInput.value) || 0;
  
  // Default Labels
  let quantityLabel = 'Packages';
  let unitLabel = 'package';
  let isPicSystem = false;

  // Determine System (Legacy Packaging vs New PIC System)
  if (selectedOption && selectedOption.dataset.packaging === 'unit') {
      isPicSystem = true;
      quantityLabel = 'PICs';
      unitLabel = 'PIC'; // or Bottle
  } else if (selectedOption && selectedOption.dataset.packaging) {
      quantityLabel = getPackagingName(selectedOption.dataset.packaging);
      unitLabel = quantityLabel.toLowerCase().slice(0, -1);
  }

  // Update UI Labels
  document.getElementById('packaging_label_1').textContent = quantityLabel;
  document.getElementById('packaging_label_2').textContent = unitLabel;
  // Update Input Label
  const inputLabel = document.querySelector('label[for="quantity_received_packages"]');
  if (inputLabel) inputLabel.innerHTML = `Quantity Received (${quantityLabel}) <span class="text-danger">*</span>`;
  
  document.getElementById('packaging_unit_1').textContent = unitLabel.toLowerCase() + 's';
  document.getElementById('packaging_unit_2').textContent = unitLabel.toLowerCase() + 's';
  document.getElementById('packaging_hint').textContent = `Enter number of ${quantityLabel}`;

  if (selectedOption && selectedOption.value) {
    const itemsPerPackage = parseInt(selectedOption.dataset.itemsPerPackage) || 1;
    
    // For PIC system, items per package is 1, so conversion is 1:1
    const totalBottles = quantity * itemsPerPackage;
    
    // If PIC system, hide "Total Bottles" distinct display or make it identical
    if (isPicSystem) {
        document.getElementById('calc_total_bottles').parentElement.parentElement.style.opacity = '0.5'; // Dim it
        document.getElementById('calc_items_per_package').parentElement.parentElement.style.opacity = '0.5'; 
    } else {
        document.getElementById('calc_total_bottles').parentElement.parentElement.style.opacity = '1';
        document.getElementById('calc_items_per_package').parentElement.parentElement.style.opacity = '1';
    }

    const profitPerBottle = sellingPrice - buyingPrice;
    let totalBuyingCost = totalBottles * buyingPrice; // Buying price is PER PIC
    
    // Calculate Discount
    let discountValue = 0;
    if (discountType === 'percentage' && discountAmount > 0) {
      // Percentage of TOTAL cost
      discountValue = (totalBuyingCost * discountAmount) / 100;
    } else if (discountType === 'fixed' && discountAmount > 0) {
      discountValue = discountAmount;
    }
    
    const finalTotalCost = Math.max(0, totalBuyingCost - discountValue);
    const totalProfit = (totalBottles * sellingPrice) - finalTotalCost;
    
    // Update Display Values
    document.getElementById('calc_quantity_received').textContent = quantity;
    document.getElementById('calc_total_packages').textContent = quantity;
    document.getElementById('calc_total_bottles').textContent = totalBottles.toLocaleString();
    document.getElementById('calc_items_per_package').textContent = itemsPerPackage;
    
    document.getElementById('calc_profit_per_bottle').textContent = profitPerBottle.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    
    // Buying Cost Display
    document.getElementById('calc_total_buying_cost').textContent = totalBuyingCost.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    
    document.getElementById('calc_discount_amount').textContent = discountValue.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    
    // Total Profit (Revenue - Cost)
    // Revenue = Total Bottles * Selling Price
    // Cost = Final Total Cost (after discount)
    document.getElementById('calc_total_profit').textContent = totalProfit.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});

  } else {
    // Reset Display
    document.getElementById('calc_total_packages').textContent = '0';
    document.getElementById('calc_quantity_received').textContent = '0';
    document.getElementById('calc_total_bottles').textContent = '0';
    document.getElementById('calc_items_per_package').textContent = '-';
    document.getElementById('calc_profit_per_bottle').textContent = '0.00';
    document.getElementById('calc_total_buying_cost').textContent = '0.00';
    document.getElementById('calc_discount_amount').textContent = '0.00';
    document.getElementById('calc_total_profit').textContent = '0.00';
    
    document.getElementById('calc_total_bottles').parentElement.parentElement.style.opacity = '1';
  }
}

$(document).ready(function() {
  const form = document.getElementById('stockReceiptForm');
  
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!form.checkValidity()) {
      form.reportValidity();
      return;
    }
    
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';

    const alertDiv = document.getElementById('formAlert');
    alertDiv.innerHTML = '';

    fetch('{{ route("admin.stock-receipts.store") }}', {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        swal({
          title: "Success!",
          text: data.message,
          type: "success",
          confirmButtonColor: "#28a745"
        }, function() {
          window.location.href = '{{ route("admin.stock-receipts.index", ["type" => $type ?? ""]) }}';
        });
      } else {
        let errorMsg = data.message || 'An error occurred. Please try again.';
        if (data.errors) {
          const errorList = Object.values(data.errors).flat().join('<br>');
          errorMsg = errorList;
        }
        alertDiv.innerHTML = '<div class="alert alert-danger">' + errorMsg + '</div>';
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alertDiv.innerHTML = '<div class="alert alert-danger">An error occurred. Please try again.</div>';
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalText;
    });
  });
});
</script>
@endsection

