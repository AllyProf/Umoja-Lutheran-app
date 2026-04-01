@extends('dashboard.layouts.app')

@section('content')
  <div class="app-title">
    <div>
      <h1><i class="fa fa-plus"></i> {{ isset($type) ? ucfirst($type) : '' }} Stock Receipt</h1>
      <p>Receive products from suppliers</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
      <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
      <li class="breadcrumb-item"><a href="{{ route($role . '.dashboard') }}">Dashboard</a></li>
      <li class="breadcrumb-item"><a
          href="{{ route('storekeeper.stock-receipts.index', ['type' => $type ?? '']) }}">{{ isset($type) ? ucfirst($type) : 'Stock' }}
          Receipts</a></li>
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
                    <h4 id="calc_items_per_package" class="text-secondary mb-0 font-weight-bold">-</h4>
                  </div>
                </div>
              </div>
              <hr>
              <div class="row">
                <div class="col-md-3">
                  <div class="text-center">
                    <h6 class="text-muted mb-1"><span id="profit_label">Profit per Unit</span></h6>
                    <h4 id="calc_profit_per_bottle" class="text-warning mb-0">0.00</h4>
                    <small class="text-muted">TSh</small>
                    <br><small class="text-muted" id="profit_hint">Selling Price - Buying Price</small>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="text-center">
                    <h6 class="text-muted mb-1">Total Buying Cost</h6>
                    <h4 id="calc_total_buying_cost" class="text-danger mb-0">0.00</h4>
                    <small class="text-muted">TSh</small>
                    <br><small class="text-muted" id="buying_cost_hint">Total Units × Buying Price</small>
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
                <i class="fa fa-info-circle"></i> <strong>Note:</strong> Calculations update automatically as you enter
                values.
              </div>
            </div>
          </div>

          <!-- Bulk Purchase Calculator (Hidden by default, shown for Food/Kitchen) -->
          <div id="bulkCalculatorSection" class="card border-primary mb-4" style="display: none;">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
              <h5 class="mb-0"><i class="fa fa-calculator"></i> Bulk Purchase Calculator (e.g. Sados to Kg)</h5>
              <button type="button" class="close text-white" onclick="toggleBulkCalculator()" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="card-body">
              <p class="text-muted small">Use this to calculate unit price when buying in sados/bags but storing in
                kg/liters.</p>
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="small font-weight-bold">Bulk Unit Name</label>
                    <input type="text" id="bulk_unit_name" class="form-control form-control-sm"
                      placeholder="e.g. Sado, Bag" value="Sado">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="small font-weight-bold">Bulk Quantity</label>
                    <input type="number" id="bulk_quantity" class="form-control form-control-sm" placeholder="e.g., 3"
                      oninput="calculateBulkUnit()">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="small font-weight-bold">Total Measured (<span class="base-unit-label">Kg</span>)</label>
                    <input type="number" id="total_measured_qty" class="form-control form-control-sm"
                      placeholder="e.g., 5" oninput="calculateBulkUnit()">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="small font-weight-bold">Total Price (TSh)</label>
                    <input type="number" id="total_bulk_price" class="form-control form-control-sm"
                      placeholder="e.g., 30000" oninput="calculateBulkUnit()">
                  </div>
                </div>
              </div>
              <div id="bulkResultAlert" class="alert alert-info py-2 mb-3" style="display: none;">
                <i class="fa fa-info-circle"></i> Result: <strong>3</strong> <span id="res_bulk_unit">Sado</span> =
                <strong>5</strong> <span class="base-unit-label">Kg</span>.
                Unit Price: <strong id="calc_unit_price">6,000</strong> TSh / <span class="base-unit-label">Kg</span>
              </div>
              <button type="button" class="btn btn-sm btn-success" id="applyBulkBtn" disabled
                onclick="applyBulkCalculation()">
                <i class="fa fa-check"></i> Apply result to form below
              </button>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0"><i class="fa fa-info-circle"></i> Stock Receipt Information</h4>
            <button type="button" class="btn btn-outline-primary btn-sm" id="btnShowCalculator" style="display: none;"
              onclick="toggleBulkCalculator()">
              <i class="fa fa-calculator"></i> Open Bulk Calculator
            </button>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="product_id">Product <span class="text-danger">*</span></label>
                <select class="form-control" id="product_id" name="product_id" required onchange="loadProductVariants()">
                  <option value="">Select product...</option>
                  @foreach($products as $product)
                    <option value="{{ $product->id }}" data-type="{{ $product->type }}">
                      {{ $product->name }} ({{ $product->supplier->name ?? 'No Supplier' }})
                    </option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="product_variant_id">Product Variant <span class="text-danger">*</span></label>
                <select class="form-control" id="product_variant_id" name="product_variant_id" required
                  onchange="updateCalculations(); updateMinimumStockLevel();">
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
                <label for="quantity_received_packages">Quantity Received (<span
                    id="packaging_label_input">Packages</span>) <span class="text-danger">*</span></label>
                <input class="form-control" type="number" id="quantity_received_packages"
                  name="quantity_received_packages" min="0.01" step="0.01" required oninput="updateCalculations()"
                  placeholder="e.g., 10 or 8.5">
                <small class="form-text text-muted" id="packaging_hint">For fruits (Passion/Oranges), enter actual KG
                  received here.</small>
              </div>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-12">
              <label class="d-block">Price Type Selection <span class="text-danger">*</span></label>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="price_type" id="price_per_unit" value="unit" checked
                  onchange="updateCalculations()">
                <label class="form-check-label" for="price_per_unit">Price is per Individual Unit (<span
                    class="unit-name-label">PIC</span>)</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="price_type" id="price_per_package" value="package"
                  onchange="updateCalculations()">
                <label class="form-check-label" for="price_per_package">Price is per Full Package (<span
                    class="package-name-label">Package</span>)</label>
              </div>
              <small class="form-text text-muted">Select 'Package' if the price you enter below is for a full
                crate/box/bag.</small>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="buying_price_per_bottle">Buying Price (TSh) <span class="text-danger">*</span></label>
                <input class="form-control" type="number" id="buying_price_per_bottle" name="buying_price_per_bottle"
                  step="0.01" min="0" required oninput="updateCalculations()">
                <small class="form-text text-muted" id="buying_price_hint_text">Enter price for one PIC</small>
              </div>
            </div>
            <div class="col-md-4" id="selling_price_col">
              <div class="form-group">
                <label for="selling_price_per_bottle"><span id="selling_label">Selling Price per PIC</span> (TSh) <span
                    class="text-danger">*</span></label>
                <input class="form-control" type="number" id="selling_price_per_bottle" name="selling_price_per_bottle"
                  step="0.01" min="0" required oninput="updateCalculations()">
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
                <input class="form-control" type="number" id="discount_amount" name="discount_amount" step="0.01" min="0"
                  oninput="updateCalculations()" placeholder="Enter discount amount">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="received_date">Received Date <span class="text-danger">*</span></label>
                <input class="form-control" type="date" id="received_date" name="received_date" required
                  value="{{ date('Y-m-d') }}">
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
                <select class="form-control" id="minimum_stock_level_unit" name="minimum_stock_level_unit" required
                  onchange="updateMinimumStockLevelHint()">
                  <option value="bottles" id="bottles_option">PIC</option>
                  <option value="packages" id="packages_option" style="display: none;">Packages</option>
                </select>
                <small class="form-text text-muted">Select whether to set minimum stock by PIC or packages</small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="minimum_stock_level">Minimum Stock Level <span class="text-danger">*</span></label>
                <input class="form-control" type="number" id="minimum_stock_level" name="minimum_stock_level" min="0"
                  required placeholder="e.g., 50" oninput="convertMinimumStockLevel()">
                <small class="form-text text-muted" id="minimum_stock_level_hint">Number of PIC below which to trigger low
                  stock notification</small>
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
            <a href="{{ route('storekeeper.stock-receipts.index', ['type' => $type ?? '']) }}" class="btn btn-secondary">
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
    // Bulk Calculator Functions
    function toggleBulkCalculator() {
      const section = document.getElementById('bulkCalculatorSection');
      const btnShow = document.getElementById('btnShowCalculator');
      if (section.style.display === 'none') {
        section.style.display = 'block';
        btnShow.style.display = 'none';
        section.scrollIntoView({ behavior: 'smooth' });
      } else {
        section.style.display = 'none';
        btnShow.style.display = 'block';
      }
    }

    function calculateBulkUnit() {
      const bulkQty = parseFloat(document.getElementById('bulk_quantity').value) || 0;
      const measuredQty = parseFloat(document.getElementById('total_measured_qty').value) || 0;
      const totalPrice = parseFloat(document.getElementById('total_bulk_price').value) || 0;
      const bulkUnitName = document.getElementById('bulk_unit_name').value || 'Bulk Unit';

      const resultAlert = document.getElementById('bulkResultAlert');
      const applyBtn = document.getElementById('applyBulkBtn');

      if (measuredQty > 0 && totalPrice > 0) {
        const unitPrice = totalPrice / measuredQty;
        document.getElementById('calc_unit_price').textContent = unitPrice.toLocaleString('en-US', { maximumFractionDigits: 2 });
        document.getElementById('res_bulk_unit').textContent = bulkUnitName;
        resultAlert.style.display = 'block';
        applyBtn.disabled = false;
      } else {
        resultAlert.style.display = 'none';
        applyBtn.disabled = true;
      }
    }

    function applyBulkCalculation() {
      const measuredQty = parseFloat(document.getElementById('total_measured_qty').value) || 0;
      const totalPrice = parseFloat(document.getElementById('total_bulk_price').value) || 0;
      const bulkQty = parseFloat(document.getElementById('bulk_quantity').value) || 0;
      const bulkName = document.getElementById('bulk_unit_name').value || 'Sado';

      if (measuredQty > 0 && totalPrice > 0) {
        const unitPrice = totalPrice / measuredQty;

        document.getElementById('quantity_received_packages').value = measuredQty;
        document.getElementById('buying_price_per_bottle').value = unitPrice.toFixed(2);

        // Add note to the notes field
        const notesField = document.getElementById('notes');
        const unitLabel = document.querySelector('.base-unit-label').textContent;
        const bulkNote = `Bulk Purchase: ${bulkQty} ${bulkName} (Measured as ${measuredQty} ${unitLabel}) Total Cost: ${totalPrice.toLocaleString()} TSh.`;
        if (notesField.value) {
          if (!notesField.value.includes(bulkNote)) {
            notesField.value += "\n" + bulkNote;
          }
        } else {
          notesField.value = bulkNote;
        }

        swal("Applied!", "Bulk calculation has been applied to the form.", "success");
        updateCalculations();
        toggleBulkCalculator();
      }
    }

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

      fetch('{{ route("storekeeper.stock-receipts.get-variants", ":id") }}'.replace(':id', productId), {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        }
      })
        .then(response => response.json())
        .then(data => {
          if (data.success && data.variants.length > 0) {
            // Store product details for calculations
            products[productId] = data.product;

            variantSelect.innerHTML = '<option value="">Select variant...</option>';
            data.variants.forEach(variant => {
              const option = document.createElement('option');
              option.value = variant.id;

              // Strip (Kg) from name for cleaner display if it's a food item
              let displayName = variant.name || '';
              const foodCategories = ['food', 'meat_poultry', 'seafood', 'vegetables', 'dairy', 'pantry_baking', 'spices_herbs', 'oils_fats', 'kitchen', 'snacks'];
              if (data.product && foodCategories.includes(data.product.category)) {
                displayName = displayName.replace(/\(Kg\)|\(kg\)|\(KG\)/g, '').trim();
              }

              option.textContent = `${displayName} (${variant.packaging_name})`;
              option.dataset.itemsPerPackage = variant.items_per_package;
              option.dataset.packaging = variant.packaging;
              option.dataset.packagingName = variant.packaging_name || (variant.packaging.charAt(0).toUpperCase() + variant.packaging.slice(1));
              option.dataset.minimumStockLevel = variant.minimum_stock_level || '';
              option.dataset.minimumStockLevelUnit = variant.minimum_stock_level_unit || 'bottles';
              option.dataset.receivingUnit = variant.receiving_unit;
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
      const productId = document.getElementById('product_id').value;
      const variantSelect = document.getElementById('product_variant_id');
      const quantityInput = document.getElementById('quantity_received_packages');
      const buyingPriceInput = document.getElementById('buying_price_per_bottle');
      const sellingPriceInput = document.getElementById('selling_price_per_bottle');
      const discountTypeSelect = document.getElementById('discount_type');
      const discountAmountInput = document.getElementById('discount_amount');

      const selectedOption = variantSelect.options[variantSelect.selectedIndex];
      const quantity = parseFloat(quantityInput.value) || 0;
      const buyingPrice = parseFloat(buyingPriceInput.value) || 0;
      const sellingPrice = parseFloat(sellingPriceInput.value) || 0;
      const discountType = discountTypeSelect ? discountTypeSelect.value : 'none';
      const discountAmount = parseFloat(discountAmountInput.value) || 0;

      // Determine product type
      let productType = '';
      if (productId && products[productId]) {
        productType = (products[productId].category || '').toLowerCase();
      }
      const foodCategories = ['food', 'meat_poultry', 'seafood', 'vegetables', 'dairy', 'pantry_baking', 'spices_herbs', 'oils_fats', 'kitchen', 'snacks'];
      const housekeepingCategories = ['cleaning_supplies', 'linens', 'housekeeping'];
      const isFood = foodCategories.includes(productType);
      const isHousekeeping = housekeepingCategories.includes(productType);

      // Default Labels
      let quantityLabel = 'Packages';
      let unitLabel = 'package';
      let isPicSystem = false;

      // Determine System (Legacy Packaging vs New PIC System vs Food)
      if (isFood && selectedOption) {
        // Food uses receiving unit as primary label (Kg, Ltr, etc.)
        const receivingUnit = selectedOption.dataset.receivingUnit || 'Kg';
        quantityLabel = receivingUnit;
        unitLabel = receivingUnit;
        isPicSystem = true; // Treat as 1:1 items
      } else if (selectedOption && selectedOption.dataset.packaging === 'unit') {
        isPicSystem = true;
        quantityLabel = 'PICs';
        unitLabel = 'PIC';
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

      document.getElementById('packaging_unit_1').textContent = unitLabel.toLowerCase() + (unitLabel.length > 2 ? 's' : '');
      document.getElementById('packaging_unit_2').textContent = unitLabel.toLowerCase() + (unitLabel.length > 2 ? 's' : '');
      document.getElementById('packaging_hint').textContent = `Enter total ${quantityLabel} received`;

      // Update Unit/PIC labels across form
      const unitText = isFood ? unitLabel : (isHousekeeping ? 'Unit' : 'PIC');
      document.querySelector('label[for="buying_price_per_bottle"]').innerHTML = `Buying Price per ${unitText} (TSh) <span class="text-danger">*</span>`;
      document.getElementById('calc_total_bottles').nextElementSibling.textContent = `${unitText}(s)`;
      document.getElementById('profit_label').textContent = `Profit per ${unitText}`;
      document.getElementById('profit_hint').textContent = `Selling Price - Buying Price`;
      document.getElementById('buying_cost_hint').textContent = `Total ${unitText}s × Buying Price`;

      // Update Min Stock Level labels
      document.getElementById('bottles_option').textContent = unitText;
      if (document.getElementById('minimum_stock_level_hint')) {
        document.getElementById('minimum_stock_level_hint').textContent = `Number of ${unitText} below which to trigger low stock notification`;
      }

      if (document.getElementById('selling_label')) {
        document.getElementById('selling_label').textContent = `Selling Price per ${unitText}`;
      }

      // Show/Hide Bulk Calculator button for food items
      const btnShowCalc = document.getElementById('btnShowCalculator');
      if (isFood) {
        btnShowCalc.style.display = 'block';
        // Update labels in the calculator
        const baseUnit = isFood ? (selectedOption ? selectedOption.dataset.receivingUnit || 'Kg' : 'Kg') : 'Unit';
        document.querySelectorAll('.base-unit-label').forEach(el => el.textContent = baseUnit);
      } else {
        btnShowCalc.style.display = 'none';
        document.getElementById('bulkCalculatorSection').style.display = 'none';
      }

      // Hide Selling/Profit for Housekeeping
      if (isHousekeeping) {
        document.getElementById('selling_price_col').style.display = 'none';
        document.getElementById('selling_price_per_bottle').required = false;
        document.getElementById('selling_price_per_bottle').value = 0;
        document.getElementById('calc_profit_per_bottle').parentElement.parentElement.style.display = 'none';
        document.getElementById('calc_total_profit').parentElement.parentElement.style.display = 'none';
      } else {
        document.getElementById('selling_price_col').style.display = 'block';
        document.getElementById('selling_price_per_bottle').required = true;
        document.getElementById('calc_profit_per_bottle').parentElement.parentElement.style.display = 'block';
        document.getElementById('calc_total_profit').parentElement.parentElement.style.display = 'block';
      }

      if (selectedOption && selectedOption.value) {
        const itemsPerPackage = parseInt(selectedOption.dataset.itemsPerPackage) || 1;
        const priceType = document.querySelector('input[name="price_type"]:checked').value;

        // Normalize buying price to per-unit cost
        let unitBuyingPrice = buyingPrice;
        if (priceType === 'package' && itemsPerPackage > 0) {
          unitBuyingPrice = buyingPrice / itemsPerPackage;
        }

        // For PIC/Food system, items per package is 1 or we treat quantity as total
        const totalUnits = isFood ? quantity : (quantity * itemsPerPackage);

        // Update hints
        const unitName = isFood ? unitLabel : (isHousekeeping ? 'Unit' : 'PIC');
        document.querySelector('.unit-name-label').textContent = unitName;
        document.querySelector('.package-name-label').textContent = quantityLabel;
        document.getElementById('buying_price_hint_text').textContent = `Enter price for one ${priceType === 'unit' ? unitName : quantityLabel}`;

        // If PIC/Food system, hide "Total Bottles/Units" distinct display or make it identical
        if (isPicSystem) {
          document.getElementById('calc_total_bottles').parentElement.parentElement.style.opacity = '0.5';
          document.getElementById('calc_items_per_package').parentElement.parentElement.style.opacity = '0.5';
        } else {
          document.getElementById('calc_total_bottles').parentElement.parentElement.style.opacity = '1';
          document.getElementById('calc_items_per_package').parentElement.parentElement.style.opacity = '1';
        }

        const profitPerUnit = sellingPrice - unitBuyingPrice;
        let totalBuyingCost = totalUnits * unitBuyingPrice;

        // Calculate Discount
        let discountValue = 0;
        if (discountType === 'percentage' && discountAmount > 0) {
          discountValue = (totalBuyingCost * discountAmount) / 100;
        } else if (discountType === 'fixed' && discountAmount > 0) {
          discountValue = discountAmount;
        }

        const finalTotalCost = Math.max(0, totalBuyingCost - discountValue);
        const totalProfit = (totalUnits * sellingPrice) - finalTotalCost;

        // Update Display Values
        document.getElementById('calc_quantity_received').textContent = quantity;
        document.getElementById('calc_total_packages').textContent = quantity;
        document.getElementById('calc_total_bottles').textContent = totalUnits.toLocaleString();
        document.getElementById('calc_items_per_package').textContent = isFood ? '1' : itemsPerPackage;

        document.getElementById('calc_profit_per_bottle').textContent = profitPerUnit.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('calc_total_buying_cost').textContent = totalBuyingCost.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('calc_discount_amount').textContent = discountValue.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('calc_total_profit').textContent = totalProfit.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        // Update the actual buying_price_per_bottle value for submission if it's package-based
        // Actually, we should probably do this normalization on the BACKEND to be safe,
        // or add a hidden field for the normalized unit price.
        // For now, let's keep it in the form but add a note.
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

    $(document).ready(function () {
      const form = document.getElementById('stockReceiptForm');

      form.addEventListener('submit', function (e) {
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

        fetch('{{ route("storekeeper.stock-receipts.store") }}', {
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
              // Open receipt report in a new tab
              if (data.stock_receipt && data.stock_receipt.id) {
                const downloadUrl = '{{ route("storekeeper.stock-receipts.download", ":id") }}'.replace(':id', data.stock_receipt.id);
                window.open(downloadUrl, '_blank');
              }

              swal({
                title: "Success!",
                text: "Stock receipt created successfully! The report has been opened in a new tab.",
                type: "success",
                confirmButtonColor: "#28a745"
              }, function () {
                window.location.href = '{{ route("storekeeper.stock-receipts.index", ["type" => $type ?? ""]) }}';
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