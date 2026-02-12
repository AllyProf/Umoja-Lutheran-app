@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-exchange-alt"></i> {{ isset($type) ? ucfirst($type) : 'Stock' }} Transfer</h1>
    <p>Transfer {{ $type ?? '' }} products to bar keeper</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.stock-transfers.index') }}">Stock Transfers</a></li>
    <li class="breadcrumb-item"><a href="#">New Transfer</a></li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <!-- Global Settings -->
    <div class="tile mb-4" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);">
      <h4 class="mb-3"><i class="fa fa-cog"></i> Transfer Settings</h4>
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label for="global_transfer_date">Transfer Date <span class="text-danger">*</span></label>
            <input class="form-control" type="date" id="global_transfer_date" required value="{{ date('Y-m-d') }}">
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label for="global_expiry_date">Expiry Date <span class="text-muted small">(Optional)</span></label>
            <input class="form-control" type="date" id="global_expiry_date" placeholder="Set if known">
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label for="global_search">Search Products</label>
            <input class="form-control" type="text" id="global_search" placeholder="Search by name..." oninput="filterProducts()">
          </div>
        </div>
      </div>
      <input type="hidden" id="global_received_by" value="{{ $barKeeper->id ?? '' }}">
      @if(!$barKeeper)
      <div class="alert alert-danger mt-2">
        <i class="fa fa-exclamation-triangle"></i> No bar keeper found. Please add a bar keeper to continue.
      </div>
      @endif
    </div>

    <!-- Products Grid -->
    @if($products->count() > 0)
    <div class="row" id="productsContainer">
      @foreach($products as $product)
      <div class="col-md-4 col-lg-3 mb-4 product-card-wrapper" 
           data-product-name="{{ strtolower($product->name) }}">
        <div class="card h-100 shadow-sm" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border: 1px solid rgba(0,0,0,0.1); transition: all 0.3s ease;" 
             onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.15)';" 
             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.1)';">  
          
          <!-- Product Image -->
          <div class="card-header bg-white p-0" style="height: 120px; overflow: hidden; border-radius: 4px 4px 0 0;">
            @if($product->image)
              <img src="{{ \Illuminate\Support\Facades\Storage::url($product->image) }}" alt="{{ $product->name }}" 
                   class="card-img-top" 
                   style="width: 100%; height: 100%; object-fit: cover;"
                   onerror="this.onerror=null; this.src='{{ asset('dashboard_assets/img/placeholder-product.png') }}';">
            @else
              <div class="d-flex align-items-center justify-content-center bg-light" style="width: 100%; height: 100%;">
                <i class="fa fa-cube fa-3x text-muted"></i>
              </div>
            @endif
          </div>
          
          <!-- Product Info -->
          <div class="card-body">
            <h5 class="card-title mb-2" style="font-size: 1.05rem; font-weight: 600; color: #333;">
              {{ $product->name }}
            </h5>
            
            <small class="text-muted d-block mb-3">
              <i class="fa fa-truck"></i> {{ $product->supplier->name }}
            </small>
            
            @if($product->variants->count() > 0)
            <div class="variants-container" style="max-height: 250px; overflow-y: auto;">
              @foreach($product->variants as $variant)
                @php
                  $available = $availableStock[$variant->id] ?? ['packages' => 0, 'bottles' => 0];
                  $packagingName = ucfirst(str_replace('_', ' ', $variant->packaging ?? 'packages'));
                @endphp
                <div class="variant-row mb-2 p-2 border rounded" style="background-color: #f8f9fa;">
                  <div class="form-check mb-1">
                    <input class="form-check-input variant-checkbox" 
                           type="checkbox" 
                           id="variant_check_{{ $product->id }}_{{ $variant->id }}" 
                           data-product-id="{{ $product->id }}"
                           data-variant-id="{{ $variant->id }}"
                           data-items-per-package="{{ $variant->items_per_package }}"
                           data-packaging-name="{{ $packagingName }}"
                           data-price-pic="{{ $variant->selling_price_per_pic ?? 0 }}"
                           data-price-serving="{{ $variant->selling_price_per_serving ?? 0 }}"
                           data-servings-pic="{{ $variant->servings_per_pic ?? 1 }}">
                    <label class="form-check-label" for="variant_check_{{ $product->id }}_{{ $variant->id }}" style="font-weight: 600; font-size: 0.9rem;">
                      {{ $variant->measurement }}
                    </label>
                  </div>
                  
                  <!-- Available Stock Display -->
                  <div class="mb-1">
                    <small class="d-block" style="font-size: 0.75rem;">
                      <strong>Available:</strong> 
                      <span class="text-success">{{ number_format($available['packages']) }} {{ strtolower($packagingName) }}</span>
                      <span class="text-muted">({{ number_format($available['bottles']) }} btls)</span>
                    </small>
                  </div>
                  
                  <div class="row">
                    <!-- Quantity Unit Selector -->
                    <div class="col-5 pr-1">
                      <select class="form-control form-control-sm quantity-unit-select" 
                              id="unit_{{ $product->id }}_{{ $variant->id }}"
                              data-product-id="{{ $product->id }}"
                              data-variant-id="{{ $variant->id }}"
                              onchange="updateQuantityHint({{ $product->id }}, {{ $variant->id }}, this.value)">
                        <option value="packages">Pkgs</option>
                        <option value="bottles">Btls</option>
                        <option value="pic">PIC</option>
                      </select>
                    </div>
                    
                    <!-- Quantity Input -->
                    <div class="col-7 pl-1">
                      <input class="form-control form-control-sm quantity-input" 
                             type="number" 
                             step="0.01"
                             id="quantity_{{ $product->id }}_{{ $variant->id }}"
                             data-product-id="{{ $product->id }}"
                             data-variant-id="{{ $variant->id }}"
                             min="0.01" 
                             placeholder="Qty"
                             disabled
                             style="font-size: 0.85rem;"
                             oninput="updateRevenueHint({{ $product->id }}, {{ $variant->id }})">
                    </div>
                  </div>
                  <small class="form-text text-muted quantity-hint" id="hint_{{ $product->id }}_{{ $variant->id }}" style="font-size: 0.7rem;">Check to enable</small>
                  <!-- Revenue Projection Hint -->
                  <div id="revenue_hint_{{ $product->id }}_{{ $variant->id }}" class="mt-1" style="display:none; font-size: 0.7rem; background: #e8f5e9; padding: 4px; border-radius: 4px;"></div>
                </div>
              @endforeach
            </div>
            @else
            <div class="alert alert-warning mb-3" style="padding: 8px; font-size: 0.85rem;">
              <i class="fa fa-exclamation-triangle"></i> No variants available
            </div>
            @endif
          </div>
          
          <!-- Transfer Button -->
          <div class="card-footer bg-white border-top">
            <button class="btn btn-primary btn-block btn-sm" 
                    onclick="transferSelectedVariants({{ $product->id }})"
                    id="transfer_btn_{{ $product->id }}"
                    {{ $product->variants->count() == 0 || !$barKeeper ? 'disabled' : '' }}>
              <i class="fa fa-exchange-alt"></i> Transfer Selected
            </button>
          </div>
        </div>
      </div>
      @endforeach
    </div>
    @else
    <div class="text-center" style="padding: 80px 20px;">
      <i class="fa fa-cube fa-5x text-muted mb-4" style="opacity: 0.5;"></i>
      <h3 class="mb-3">No Products Available</h3>
      <p class="text-muted mb-4">No active products found to transfer.</p>
      <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-lg">
        <i class="fa fa-plus"></i> Add New Product
      </a>
    </div>
    @endif
    
    <!-- No Results Message (hidden by default) -->
    <div id="noResultsMessage" class="text-center" style="display: none; padding: 80px 20px;">
      <i class="fa fa-search fa-5x text-muted mb-4" style="opacity: 0.5;"></i>
      <h3 class="mb-3">No Products Found</h3>
      <p class="text-muted mb-4">Try adjusting your search criteria.</p>
      <button class="btn btn-secondary" onclick="resetFilters()">
        <i class="fa fa-refresh"></i> Reset Search
      </button>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<script>
// Available stock data from PHP
const availableStock = @json($availableStock);

// Enable/disable quantity input when checkbox is toggled
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.variant-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
      const productId = this.dataset.productId;
      const variantId = this.dataset.variantId;
      const quantityInput = document.getElementById('quantity_' + productId + '_' + variantId);
      const quantityHint = document.getElementById('hint_' + productId + '_' + variantId);
      
      if (this.checked) {
        quantityInput.disabled = false;
        quantityInput.focus();
        const unit = document.getElementById('unit_' + productId + '_' + variantId).value;
        updateQuantityHint(productId, variantId, unit);
      } else {
        quantityInput.disabled = true;
        quantityInput.value = '';
        quantityHint.textContent = 'Check to enable';
      }
    });
  });
});

// Filter products by search
function filterProducts() {
  const searchTerm = document.getElementById('global_search').value.toLowerCase();
  const productCards = document.querySelectorAll('.product-card-wrapper');
  let visibleCount = 0;
  
  productCards.forEach(card => {
    const productName = card.dataset.productName || '';
    
    if (!searchTerm || productName.includes(searchTerm)) {
      card.style.display = 'block';
      visibleCount++;
    } else {
      card.style.display = 'none';
    }
  });
  
  // Show/hide no results message
  const noResultsMessage = document.getElementById('noResultsMessage');
  if (visibleCount === 0 && searchTerm) {
    noResultsMessage.style.display = 'block';
  } else {
    noResultsMessage.style.display = 'none';
  }
}

function resetFilters() {
  document.getElementById('global_search').value = '';
  filterProducts();
}

// Update quantity hint based on unit
function updateQuantityHint(productId, variantId, unit) {
  const checkbox = document.getElementById('variant_check_' + productId + '_' + variantId);
  const quantityHint = document.getElementById('hint_' + productId + '_' + variantId);
  const quantityInput = document.getElementById('quantity_' + productId + '_' + variantId);
  
  if (!checkbox || !checkbox.checked) {
    return;
  }
  
  const packagingName = checkbox.dataset.packagingName || 'Packages';
  const packagingLower = packagingName.toLowerCase();
  const available = availableStock[variantId] || {packages: 0, bottles: 0};
  
  if (unit === 'packages') {
    quantityHint.textContent = `Max: ${available.packages} ${packagingLower}`;
    quantityInput.max = available.packages;
  } else {
    quantityHint.textContent = `Max: ${available.bottles} bottles`;
    quantityInput.max = available.bottles;
  }
}

// Update revenue projection hint
function updateRevenueHint(productId, variantId) {
  const checkbox = document.getElementById('variant_check_' + productId + '_' + variantId);
  const unit = document.getElementById('unit_' + productId + '_' + variantId).value;
  const quantity = parseFloat(document.getElementById('quantity_' + productId + '_' + variantId).value) || 0;
  const hintDiv = document.getElementById('revenue_hint_' + productId + '_' + variantId);
  
  if (!checkbox || quantity <= 0) {
    hintDiv.style.display = 'none';
    return;
  }
  
  const pricePic = parseFloat(checkbox.dataset.pricePic) || 0;
  const priceServing = parseFloat(checkbox.dataset.priceServing) || 0;
  const servingsPic = parseInt(checkbox.dataset.servingsPic) || 1;
  const itemsPerPkg = parseInt(checkbox.dataset.itemsPerPackage) || 1;
  
  // Calculate total PICs involved
  let totalPics = 0;
  if (unit === 'packages') {
    totalPics = quantity * itemsPerPkg;
  } else {
    // bottles or pic
    totalPics = quantity;
  }
  
  if (totalPics > 0 && (pricePic > 0 || priceServing > 0)) {
    const revPic = totalPics * pricePic;
    const revServing = (totalPics * servingsPic) * priceServing;
    
    let html = `<strong>Revenue Potential:</strong><br>`;
    if (pricePic > 0) html += `Sell as PIC: ${revPic.toLocaleString()} TSH<br>`;
    if (priceServing > 0) html += `Sell as Serving: ${revServing.toLocaleString()} TSH`;
    
    if (pricePic > 0 && priceServing > 0) {
       const diff = revServing - revPic;
       if (diff > 0) {
         html += `<br><span class="text-success">Serving +${diff.toLocaleString()} TSH</span>`;
       }
    }
    
    hintDiv.innerHTML = html;
    hintDiv.style.display = 'block';
  } else {
    hintDiv.style.display = 'none';
  }
}

// Transfer selected variants for a product
function transferSelectedVariants(productId) {
  const globalReceivedBy = document.getElementById('global_received_by').value;
  const globalTransferDate = document.getElementById('global_transfer_date').value;
  const globalExpiryDate = document.getElementById('global_expiry_date').value;
  const transferBtn = document.getElementById('transfer_btn_' + productId);
  
  // Validation
  if (!globalReceivedBy) {
    swal({
      title: "Bar Keeper Required!",
      text: "No bar keeper is configured. Please contact administrator.",
      type: "error",
      confirmButtonColor: "#d33"
    });
    return;
  }
  
  if (!globalTransferDate) {
    swal({
      title: "Transfer Date Required!",
      text: "Please select a transfer date.",
      type: "warning",
      confirmButtonColor: "#ffc107"
    });
    return;
  }
  
  // Collect all checked variants for this product
  const selectedVariants = [];
  document.querySelectorAll('.variant-checkbox').forEach(checkbox => {
    if (checkbox.dataset.productId == productId && checkbox.checked) {
      const variantId = checkbox.dataset.variantId;
      const quantityInput = document.getElementById('quantity_' + productId + '_' + variantId);
      const quantityUnit = document.getElementById('unit_' + productId + '_' + variantId).value;
      const quantity = parseFloat(quantityInput.value); // Changed to parseFloat
      
      if (!quantity || quantity <= 0) { // Changed < 1 to <= 0
        return; // Skip invalid quantities
      }
      
      // Stock check logic (simplified for fractional)
      const available = availableStock[variantId] || {packages: 0, bottles: 0};
      // let maxQuantity = quantityUnit === 'packages' ? available.packages : available.bottles;
      // if (quantity > maxQuantity) ... (Disabled strict client-side check for fractional flexibility, server will validate)
      
      selectedVariants.push({
        variant_id: variantId,
        quantity: quantity,
        unit: quantityUnit
      });
    }
  });
  
  if (selectedVariants.length === 0) {
    swal({
      title: "No Variants Selected!",
      text: "Please select at least one variant and enter quantities.",
      type: "warning",
      confirmButtonColor: "#ffc107"
    });
    return;
  }
  
  // Transfer each variant (could be done in batch, but keeping simple for now)
  const originalText = transferBtn.innerHTML;
  transferBtn.disabled = true;
  transferBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Transferring...';
  
  let transferPromises = selectedVariants.map(variant => {
    const formData = new FormData();
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    formData.append('_token', csrfToken);
    formData.append('product_id', productId);
    formData.append('product_variant_id', variant.variant_id);
    formData.append('quantity_transferred', variant.quantity);
    formData.append('quantity_unit', variant.unit);
    formData.append('received_by', globalReceivedBy);
    formData.append('transfer_date', globalTransferDate);
    formData.append('expiry_date', globalExpiryDate);
    
    return fetch('{{ route("admin.stock-transfers.store") }}', {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    }).then(response => response.json());
  });
  
  Promise.all(transferPromises)
    .then(results => {
      const allSuccess = results.every(r => r.success);
      
      if (allSuccess) {
        swal({
          title: "Success!",
          text: `Successfully transferred ${selectedVariants.length} variant(s)!`,
          type: "success",
          confirmButtonColor: "#28a745"
        }, function() {
          // Reset all checked variants
          document.querySelectorAll('.variant-checkbox').forEach(checkbox => {
            if (checkbox.dataset.productId == productId && checkbox.checked) {
              checkbox.checked = false;
              const variantId = checkbox.dataset.variantId;
              const quantityInput = document.getElementById('quantity_' + productId + '_' + variantId);
              quantityInput.value = '';
              quantityInput.disabled = true;
              document.getElementById('hint_' + productId + '_' + variantId).textContent = 'Check to enable';
              document.getElementById('revenue_hint_' + productId + '_' + variantId).style.display = 'none'; // Hide hint
            }
          });
          transferBtn.disabled = false;
          transferBtn.innerHTML = originalText;
          // Reload page to update available stock
          location.reload();
        });
      } else {
        const errors = results.filter(r => !r.success).map(r => r.message).join('<br>');
        swal({
          title: "Transfer Failed!",
          text: errors || "Some transfers failed. Please try again.",
          type: "error",
          confirmButtonColor: "#d33"
        });
        transferBtn.disabled = false;
        transferBtn.innerHTML = originalText;
      }
    })
    .catch(error => {
      console.error('Error:', error);
      swal({
        title: "Error!",
        text: "An error occurred. Please try again.",
        type: "error",
        confirmButtonColor: "#d33"
      });
      transferBtn.disabled = false;
      transferBtn.innerHTML = originalText;
    });
}
</script>
@endsection
