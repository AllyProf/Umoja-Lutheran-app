@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-tint"></i> Swimming Service Registration</h1>
    <p>Register swimming pool access for guests (Swimming or Swimming with Bucket)</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ $role === 'reception' ? route('reception.dashboard') : route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ $role === 'reception' ? route('reception.day-services.index') : route('admin.day-services.index') }}">Day Services</a></li>
    <li class="breadcrumb-item"><a href="#">Swimming Service</a></li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title">Swimming Service Registration</h3>
      <div class="tile-body">
        <form id="swimmingServiceForm" method="POST" action="{{ $role === 'reception' ? route('reception.day-services.store') : route('admin.day-services.store') }}">
          @csrf
          <input type="hidden" name="payment_status" value="paid">
          <input type="hidden" name="guest_type" id="guest_type" value="tanzanian">

          <h4 class="mb-4 mt-4"><i class="fa fa-info-circle"></i> Service Selection</h4>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="service_type">Select Service Type <span class="text-danger">*</span></label>
                <select class="form-control" id="service_type" name="service_type" required onchange="updateServicePricing()">
                  <option value="">Select a service...</option>
                  @foreach($swimmingServices as $service)
                  <option value="{{ $service->service_key }}" 
                          data-service-name="{{ $service->service_name }}"
                          data-adult-price="{{ $service->price_tanzanian ?? 0 }}"
                          data-child-price="{{ $service->child_price_tanzanian ?? 0 }}"
                          data-age-group="{{ $service->age_group ?? 'both' }}">
                    {{ $service->service_name }}
                  </option>
                  @endforeach
                </select>
                <small class="form-text text-muted">Choose between Swimming or Swimming with Bucket</small>
              </div>
            </div>
          </div>

          <!-- Price Display Section (shown when service is selected) -->
          <div id="priceDisplaySection" style="display: none; margin-bottom: 20px;">
            <div class="alert alert-info">
              <h5><i class="fa fa-tag"></i> Service Pricing</h5>
              <div class="row">
                <div class="col-md-6">
                  <strong>Adult Price:</strong> <span id="display_adult_price" class="text-success">-</span>
                </div>
                <div class="col-md-6">
                  <strong>Child Price:</strong> <span id="display_child_price" class="text-success">-</span>
                </div>
              </div>
            </div>
          </div>

          <h4 class="mb-4 mt-4"><i class="fa fa-user"></i> Guest Information</h4>
          
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="guest_name">Guest Name <span class="text-danger">*</span></label>
                <input class="form-control" type="text" id="guest_name" name="guest_name" placeholder="Enter guest name" required>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="guest_phone">Phone Number</label>
                <input class="form-control" type="text" id="guest_phone" name="guest_phone" placeholder="e.g., +255712345678">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="guest_email">Email</label>
                <input class="form-control" type="email" id="guest_email" name="guest_email" placeholder="guest@example.com">
              </div>
            </div>
          </div>
          
          <!-- Adult/Child Quantity Fields -->
          <div class="row" id="adultChildQuantityGroup" style="display: none;">
            <div class="col-md-6">
              <div class="form-group">
                <label for="adult_quantity">Number of Adults <span class="text-danger">*</span></label>
                <input class="form-control" type="number" id="adult_quantity" name="adult_quantity" value="0" min="0" required oninput="calculateAmount()">
                <small class="form-text text-muted">Enter the number of adults</small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="child_quantity">Number of Children <small class="text-muted">(Optional)</small></label>
                <div class="input-group">
                  <input class="form-control" type="number" id="child_quantity" name="child_quantity" value="0" min="0" oninput="calculateAmount()">
                  <div class="input-group-append">
                    <div class="input-group-text">
                      <input type="checkbox" id="no_children_checkbox" onchange="handleNoChildrenCheckbox()">
                      <label for="no_children_checkbox" class="mb-0 ml-1" style="font-weight: normal; cursor: pointer;">I don't have children</label>
                    </div>
                  </div>
                </div>
                <small class="form-text text-muted">Enter the number of children, or check if you don't have any</small>
              </div>
            </div>
          </div>
          <input type="hidden" id="number_of_people" name="number_of_people" value="1">
          <input type="hidden" id="adult_price" value="0">
          <input type="hidden" id="child_price" value="0">

          <h4 class="mb-4 mt-4"><i class="fa fa-calendar"></i> Service Date & Time</h4>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="service_date">Service Date <span class="text-danger">*</span></label>
                <input class="form-control" type="date" id="service_date" name="service_date" value="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="service_time">Service Time <span class="text-danger">*</span></label>
                <input class="form-control" type="time" id="service_time" name="service_time" value="{{ \Carbon\Carbon::now()->format('H:i') }}" required>
              </div>
            </div>
          </div>

          <!-- Discount Section (Manager Only) -->
          @if($role !== 'reception')
          <div class="card bg-light mb-4">
            <div class="card-header">
              <i class="fa fa-percent"></i> <strong>Manager Discount (Optional)</strong>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="discount_type">Discount Type</label>
                    <select class="form-control" id="discount_type" name="discount_type" onchange="applyDiscount()">
                      <option value="none">No Discount</option>
                      <option value="percentage">Percentage (%)</option>
                      <option value="fixed">Fixed Amount (TZS)</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="discount_value">Discount Value</label>
                    <input class="form-control" type="number" id="discount_value" name="discount_value" value="0" step="0.01" min="0" oninput="applyDiscount()" disabled>
                    <small class="form-text text-muted" id="discount_hint">Select discount type first</small>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Discount Amount</label>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">TZS</span>
                      </div>
                      <input class="form-control" type="text" id="discount_amount_display" name="discount_amount" value="0.00" readonly style="background-color: #e9ecef;">
                    </div>
                    <small class="form-text text-muted">Calculated discount</small>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group mb-0">
                    <label for="discount_reason">Discount Reason</label>
                    <input class="form-control" type="text" id="discount_reason" name="discount_reason" placeholder="e.g., Loyalty customer, Special promotion" maxlength="255">
                    <small class="form-text text-muted">Optional: Provide a reason for the discount</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
          @endif

          <h4 class="mb-4 mt-4"><i class="fa fa-money"></i> Payment Information</h4>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="original_amount">Original Amount</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">TZS</span>
                  </div>
                  <input class="form-control" type="number" id="original_amount" step="0.01" min="0" readonly style="background-color: #e9ecef;">
                </div>
                <small class="form-text text-muted">Amount before discount</small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="amount">Final Amount <span class="text-danger">*</span></label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="currency_symbol">TZS</span>
                  </div>
                  <input class="form-control" type="number" id="amount" name="amount" step="0.01" min="0" readonly style="font-weight: bold; font-size: 1.1em;">
                </div>
                <small class="form-text text-muted" id="currency_hint">Amount after discount</small>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="amount_paid">Amount Paid <span class="text-danger">*</span></label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="paid_currency_symbol">TZS</span>
                  </div>
                  <input class="form-control" type="number" id="amount_paid" name="amount_paid" value="0" step="0.01" min="0" required>
                </div>
              </div>
            </div>
          </div>

          <!-- Price Breakdown (shown when adult/child pricing is used) -->
          <div id="priceBreakdown" style="display: none; margin-bottom: 15px;">
            <div class="alert alert-info">
              <strong>Price Breakdown:</strong>
              <div id="breakdown_text"></div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="payment_method">Payment Method <span class="text-danger">*</span></label>
                <select class="form-control" id="payment_method" name="payment_method" required onchange="togglePaymentProvider()">
                  <option value="">Select payment method...</option>
                  <option value="cash">Cash</option>
                  <option value="card">Card</option>
                  <option value="mobile">Mobile Money</option>
                  <option value="bank">Bank Transfer</option>
                  <option value="other">Other</option>
                </select>
              </div>
            </div>
          </div>

          <div class="row" id="payment_provider_section" style="display: none;">
            <div class="col-md-6">
              <div class="form-group">
                <label for="payment_provider">Payment Provider</label>
                <select class="form-control" id="payment_provider" name="payment_provider">
                  <option value="">Select provider...</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="payment_reference">Reference Number</label>
                <input class="form-control" type="text" id="payment_reference" name="payment_reference" placeholder="Enter transaction reference">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="notes">Notes (Optional)</label>
                <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Any additional notes..."></textarea>
              </div>
            </div>
          </div>

          <div id="formAlert"></div>

          <div class="form-group mt-4">
            <button type="submit" class="btn btn-primary">
              <i class="fa fa-check"></i> Process Payment & Generate Receipt
            </button>
            <a href="{{ $role === 'reception' ? route('reception.day-services.index') : route('admin.day-services.index') }}" class="btn btn-secondary">
              <i class="fa fa-times"></i> Cancel
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<script>
$(document).ready(function() {
  const form = document.getElementById('swimmingServiceForm');
  const serviceTypeSelect = document.getElementById('service_type');
  const exchangeRate = {{ $exchangeRate }};
  
  // Service model pricing (passed from controller) - Priority: Service model > ServiceCatalog
  @if(isset($swimmingServiceModel) && $swimmingServiceModel->age_group === 'both' && $swimmingServiceModel->child_price_tsh && $swimmingServiceModel->child_price_tsh > 0)
  const swimmingServiceModel = {
    age_group: '{{ $swimmingServiceModel->age_group }}',
    price_tsh: {{ $swimmingServiceModel->price_tsh }},
    child_price_tsh: {{ $swimmingServiceModel->child_price_tsh }}
  };
  @elseif(isset($swimmingCatalog) && $swimmingCatalog->age_group === 'both' && $swimmingCatalog->child_price_tanzanian && $swimmingCatalog->child_price_tanzanian > 0)
  const swimmingServiceModel = {
    age_group: '{{ $swimmingCatalog->age_group }}',
    price_tsh: {{ $swimmingCatalog->price_tanzanian }},
    child_price_tsh: {{ $swimmingCatalog->child_price_tanzanian }}
  };
  @else
  const swimmingServiceModel = null;
  @endif
  
  @if(isset($swimmingWithBucketServiceModel) && $swimmingWithBucketServiceModel->age_group === 'both' && $swimmingWithBucketServiceModel->child_price_tsh && $swimmingWithBucketServiceModel->child_price_tsh > 0)
  const swimmingWithBucketServiceModel = {
    age_group: '{{ $swimmingWithBucketServiceModel->age_group }}',
    price_tsh: {{ $swimmingWithBucketServiceModel->price_tsh }},
    child_price_tsh: {{ $swimmingWithBucketServiceModel->child_price_tsh }}
  };
  @elseif(isset($swimmingWithBucketCatalog) && $swimmingWithBucketCatalog->age_group === 'both' && $swimmingWithBucketCatalog->child_price_tanzanian && $swimmingWithBucketCatalog->child_price_tanzanian > 0)
  const swimmingWithBucketServiceModel = {
    age_group: '{{ $swimmingWithBucketCatalog->age_group }}',
    price_tsh: {{ $swimmingWithBucketCatalog->price_tanzanian }},
    child_price_tsh: {{ $swimmingWithBucketCatalog->child_price_tanzanian }}
  };
  @else
  const swimmingWithBucketServiceModel = null;
  @endif

  // Handle "I don't have children" checkbox - Make globally accessible
  window.handleNoChildrenCheckbox = function() {
    const checkbox = document.getElementById('no_children_checkbox');
    const childQuantityInput = document.getElementById('child_quantity');
    
    if (checkbox && childQuantityInput) {
      if (checkbox.checked) {
        childQuantityInput.value = 0;
        childQuantityInput.disabled = true;
      } else {
        childQuantityInput.disabled = false;
      }
      calculateAmount();
    }
  };
  
  // Update service pricing when service type changes - Make globally accessible
  window.updateServicePricing = function() {
    const serviceType = serviceTypeSelect.value;
    const priceDisplaySection = document.getElementById('priceDisplaySection');
    const adultChildGroup = document.getElementById('adultChildQuantityGroup');
    
    if (!serviceType) {
      priceDisplaySection.style.display = 'none';
      adultChildGroup.style.display = 'none';
      document.getElementById('amount').value = '';
      document.getElementById('adult_quantity').value = 0;
      document.getElementById('child_quantity').value = 0;
      return;
    }
    
    // Determine which service model to use
    // Always use catalog data from selected option (most reliable - reads directly from dropdown)
    let serviceModel = null;
    const selectedOption = serviceTypeSelect.options[serviceTypeSelect.selectedIndex];
    
    if (selectedOption) {
      const adultPriceFromData = parseFloat(selectedOption.dataset.adultPrice) || 0;
      const childPriceFromData = parseFloat(selectedOption.dataset.childPrice) || 0;
      const ageGroupFromData = selectedOption.dataset.ageGroup || 'both';
      
      if (adultPriceFromData > 0 && childPriceFromData > 0) {
        // Create a serviceModel-like object from catalog data
        serviceModel = {
          age_group: ageGroupFromData,
          price_tsh: adultPriceFromData,
          child_price_tsh: childPriceFromData
        };
      }
    }
    
    // Fallback: Try Service models if catalog data not available
    if (!serviceModel) {
      const serviceTypeLower = serviceType.toLowerCase();
      if (serviceTypeLower === 'swimming' && swimmingServiceModel) {
        serviceModel = swimmingServiceModel;
      } else if (serviceTypeLower.includes('bucket') && swimmingWithBucketServiceModel) {
        serviceModel = swimmingWithBucketServiceModel;
      }
    }
    
    // Show price display and adult/child quantity fields
    if (serviceModel && serviceModel.age_group === 'both' && serviceModel.child_price_tsh && serviceModel.child_price_tsh > 0) {
      // Display prices instantly
      const adultPrice = parseFloat(serviceModel.price_tsh);
      const childPrice = parseFloat(serviceModel.child_price_tsh);
      
      document.getElementById('display_adult_price').textContent = adultPrice.toLocaleString() + ' TZS';
      document.getElementById('display_child_price').textContent = childPrice.toLocaleString() + ' TZS';
      priceDisplaySection.style.display = 'block';
      
      // Show adult/child quantity fields
      adultChildGroup.style.display = 'block';
      document.getElementById('adult_price').value = adultPrice;
      document.getElementById('child_price').value = childPrice;
      
      // Reset quantities
      document.getElementById('adult_quantity').value = 0;
      document.getElementById('child_quantity').value = 0;
      document.getElementById('amount').value = '0.00';
    } else {
      // No pricing available
      priceDisplaySection.style.display = 'none';
      adultChildGroup.style.display = 'none';
    }
    
    calculateAmount();
  };
  
  // Calculate total amount - Make globally accessible
  window.calculateAmount = function() {
    const serviceType = serviceTypeSelect.value;
    
    if (!serviceType) {
      document.getElementById('amount').value = '';
      return;
    }
    
    // Determine which service model to use
    // Always use catalog data from selected option (most reliable)
    let serviceModel = null;
    const serviceTypeSelectEl = document.getElementById('service_type');
    const selectedOption = serviceTypeSelectEl ? serviceTypeSelectEl.options[serviceTypeSelectEl.selectedIndex] : null;
    
    if (selectedOption) {
      const adultPriceFromData = parseFloat(selectedOption.dataset.adultPrice) || 0;
      const childPriceFromData = parseFloat(selectedOption.dataset.childPrice) || 0;
      const ageGroupFromData = selectedOption.dataset.ageGroup || 'both';
      
      if (adultPriceFromData > 0 && childPriceFromData > 0) {
        // Create a serviceModel-like object from catalog data
        serviceModel = {
          age_group: ageGroupFromData,
          price_tsh: adultPriceFromData,
          child_price_tsh: childPriceFromData
        };
      }
    }
    
    // Fallback: Try Service models if catalog data not available
    if (!serviceModel) {
      const serviceTypeLower = serviceType.toLowerCase();
      if (serviceTypeLower === 'swimming' && swimmingServiceModel) {
        serviceModel = swimmingServiceModel;
      } else if (serviceTypeLower.includes('bucket') && swimmingWithBucketServiceModel) {
        serviceModel = swimmingWithBucketServiceModel;
      }
    }
    
    const adultChildGroup = document.getElementById('adultChildQuantityGroup');
    const priceBreakdown = document.getElementById('priceBreakdown');
    const breakdownText = document.getElementById('breakdown_text');
    
    let calculatedAmount = 0;
    
    if (serviceModel && serviceModel.age_group === 'both' && serviceModel.child_price_tsh && serviceModel.child_price_tsh > 0 && adultChildGroup.style.display !== 'none') {
      // Calculate based on adult/child quantities
      const adultQuantity = parseInt(document.getElementById('adult_quantity').value) || 0;
      const childQuantity = parseInt(document.getElementById('child_quantity').value) || 0;
      
      const adultPrice = parseFloat(serviceModel.price_tsh);
      const childPrice = parseFloat(serviceModel.child_price_tsh);
      const adultTotal = adultPrice * adultQuantity;
      const childTotal = childPrice * childQuantity;
      calculatedAmount = adultTotal + childTotal;
      
      // Update number_of_people hidden field
      document.getElementById('number_of_people').value = adultQuantity + childQuantity;
      
      // Show breakdown
      if (adultQuantity > 0 || childQuantity > 0) {
        let breakdown = [];
        if (adultQuantity > 0) {
          breakdown.push(`${adultQuantity} adult(s) × ${adultPrice.toLocaleString()} TZS = ${adultTotal.toLocaleString()} TZS`);
        }
        if (childQuantity > 0) {
          breakdown.push(`${childQuantity} child(ren) × ${childPrice.toLocaleString()} TZS = ${childTotal.toLocaleString()} TZS`);
        }
        breakdown.push(`<strong>Total: ${calculatedAmount.toLocaleString()} TZS</strong>`);
        breakdownText.innerHTML = breakdown.join('<br>');
        priceBreakdown.style.display = 'block';
      } else {
        priceBreakdown.style.display = 'none';
      }
    } else {
      // Use single quantity (fallback to catalog pricing)
      const numPeople = parseInt(document.getElementById('number_of_people').value) || 1;
      // TODO: Get price from catalog if service model not available
      calculatedAmount = 0; // Will be set from catalog
      priceBreakdown.style.display = 'none';
    }
    
    // Store original amount before discount
    document.getElementById('original_amount').value = calculatedAmount.toFixed(2);
    document.getElementById('amount').value = calculatedAmount.toFixed(2);
    
    // Apply discount if any
    applyDiscount();
  };
  
  // Apply discount - Make globally accessible
  window.applyDiscount = function() {
    const discountType = document.getElementById('discount_type').value;
    const discountValueInput = document.getElementById('discount_value');
    const discountHint = document.getElementById('discount_hint');
    const originalAmount = parseFloat(document.getElementById('original_amount').value) || 0;
    
    // Enable/disable discount value input based on type
    if (discountType === 'none') {
      discountValueInput.disabled = true;
      discountValueInput.value = 0;
      discountHint.textContent = 'Select discount type first';
    } else if (discountType === 'percentage') {
      discountValueInput.disabled = false;
      discountValueInput.max = 100;
      discountHint.textContent = 'Enter percentage (0-100%)';
    } else if (discountType === 'fixed') {
      discountValueInput.disabled = false;
      discountValueInput.max = originalAmount;
      discountHint.textContent = 'Enter fixed amount in TZS';
    }
    
    // Calculate discount amount
    let discountAmount = 0;
    const discountValue = parseFloat(discountValueInput.value) || 0;
    
    if (discountType === 'percentage' && discountValue > 0) {
      // Validate percentage
      if (discountValue > 100) {
        discountValueInput.value = 100;
      }
      discountAmount = (originalAmount * Math.min(discountValue, 100)) / 100;
    } else if (discountType === 'fixed' && discountValue > 0) {
      // Validate fixed amount doesn't exceed original
      if (discountValue > originalAmount) {
        discountValueInput.value = originalAmount;
      }
      discountAmount = Math.min(discountValue, originalAmount);
    }
    
    // Update discount amount display
    document.getElementById('discount_amount_display').value = discountAmount.toFixed(2);
    
    // Calculate final amount after discount
    const finalAmount = Math.max(0, originalAmount - discountAmount);
    document.getElementById('amount').value = finalAmount.toFixed(2);
    
    // Update amount paid to match final amount
    if (finalAmount > 0) {
      document.getElementById('amount_paid').value = finalAmount.toFixed(2);
    } else {
      document.getElementById('amount_paid').value = '0';
    }
  };
  
  // Update amount paid - Make globally accessible
  window.updateAmountPaid = function() {
    // Auto-fill amount paid with amount if empty
    const amount = parseFloat(document.getElementById('amount').value) || 0;
    const amountPaid = parseFloat(document.getElementById('amount_paid').value) || 0;
    
    if (amountPaid === 0 && amount > 0) {
      document.getElementById('amount_paid').value = amount.toFixed(2);
    }
  };
  
  // Toggle payment provider based on payment method - Make globally accessible
  window.togglePaymentProvider = function() {
    const paymentMethod = document.getElementById('payment_method').value;
    const paymentProviderSection = document.getElementById('payment_provider_section');
    const paymentProviderSelect = document.getElementById('payment_provider');
    
    if (paymentMethod === 'mobile' || paymentMethod === 'bank') {
      paymentProviderSection.style.display = 'block';
      
      // Populate provider options
      paymentProviderSelect.innerHTML = '<option value="">Select provider...</option>';
      
      if (paymentMethod === 'mobile') {
        paymentProviderSelect.innerHTML += `
          <option value="m-pesa">M-PESA</option>
          <option value="halopesa">HALOPESA</option>
          <option value="mixx-by-yas">MIXX BY YAS</option>
          <option value="airtel-money">AIRTEL MONEY</option>
        `;
      } else if (paymentMethod === 'bank') {
        paymentProviderSelect.innerHTML += `
          <option value="nmb">NMB</option>
          <option value="crdb">CRDB</option>
          <option value="kcb">KCB Bank</option>
          <option value="exim">Exim Bank</option>
          <option value="equity">Equity Bank</option>
          <option value="stanbic">Stanbic Bank</option>
        `;
      }
    } else {
      paymentProviderSection.style.display = 'none';
      paymentProviderSelect.value = '';
    }
  };
  
  // Form submission
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!form.checkValidity()) {
      form.reportValidity();
      return;
    }
    
    const formData = new FormData(form);
    
    // Show loading
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Processing...';
    
    fetch(form.action, {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
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
          if (data.receipt_url) {
            window.open(data.receipt_url, '_blank');
          }
          window.location.href = '{{ $role === "reception" ? route("reception.day-services.index") : route("admin.day-services.index") }}';
        });
      } else {
        swal({
          title: "Error!",
          text: data.message || 'An error occurred. Please try again.',
          type: "error",
          confirmButtonColor: "#d33"
        });
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
      }
    })
    .catch(error => {
      console.error('Error:', error);
      swal({
        title: "Error!",
        text: 'An error occurred. Please try again.',
        type: "error",
        confirmButtonColor: "#d33"
      });
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalText;
    });
  });
});
</script>
@endsection

