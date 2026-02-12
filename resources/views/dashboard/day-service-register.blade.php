@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-plus-circle"></i> Register Day Service</h1>
    <p>Register one-time services (Swimming, Restaurant, Bar)</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ $role === 'reception' ? route('reception.dashboard') : route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ $role === 'reception' ? route('reception.day-services.index') : route('admin.day-services.index') }}">Day Services</a></li>
    <li class="breadcrumb-item"><a href="#">Register</a></li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <form id="dayServiceForm">
        @csrf
        
        <h4 class="mb-4"><i class="fa fa-info-circle"></i> Service Information</h4>
        
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="service_type">Select Service <span class="text-danger">*</span></label>
              <select class="form-control" id="service_type" name="service_type" required>
                <option value="">Select a registered service...</option>
                @foreach($services as $service)
                <option value="{{ $service->service_key }}" 
                        data-pricing-type="{{ $service->pricing_type }}"
                        data-price-tanzanian="{{ $service->price_tanzanian }}"
                        data-price-international="{{ $service->price_international ?? $service->price_tanzanian }}"
                        data-payment-upfront="{{ $service->payment_required_upfront ? '1' : '0' }}"
                        data-requires-items="{{ $service->requires_items ? '1' : '0' }}"
                        {{ (isset($selectedServiceType) && $selectedServiceType === $service->service_key) ? 'selected' : '' }}>
                  {{ $service->service_name }}
                </option>
                @endforeach
              </select>
              <small class="form-text text-muted" id="service_type_hint">
                @if($services->count() === 0)
                  <span class="text-warning"><i class="fa fa-exclamation-triangle"></i> No services registered. <a href="{{ $role === 'reception' ? route('admin.service-catalog.index') : route('admin.service-catalog.index') }}" target="_blank">Register services in Service Catalog</a></span>
                @else
                  Select a service from registered services
                @endif
              </small>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="guest_type">Guest Type <span class="text-danger">*</span></label>
              <select class="form-control" id="guest_type" name="guest_type" required>
                <option value="tanzanian">Tanzanian Guest</option>
                <option value="international">International Guest</option>
              </select>
            </div>
          </div>
        </div>

        <h4 class="mb-4 mt-4"><i class="fa fa-user"></i> Guest Information</h4>
        
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="guest_name">Guest Name <span class="text-danger">*</span></label>
              <input class="form-control" type="text" id="guest_name" name="guest_name" placeholder="Enter guest name" required>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="guest_phone">Phone Number</label>
              <input class="form-control" type="text" id="guest_phone" name="guest_phone" placeholder="Enter phone number">
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="guest_email">Email</label>
              <input class="form-control" type="email" id="guest_email" name="guest_email" placeholder="Enter email address">
            </div>
          </div>
          <!-- Adult/Child Quantity Fields (shown when swimming service supports both) -->
          <div class="col-md-12" id="adultChildQuantityGroup" style="display: none;">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="adult_quantity">Number of Adults <span class="text-danger">*</span></label>
                  <input class="form-control" type="number" id="adult_quantity" name="adult_quantity" min="0" value="1" oninput="calculateDayServicePrice()">
                  <small class="form-text text-muted">Enter the number of adults</small>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="child_quantity">Number of Children <small class="text-muted">(Optional)</small></label>
                  <div class="input-group">
                    <input class="form-control" type="number" id="child_quantity" name="child_quantity" min="0" value="0" oninput="calculateDayServicePrice()">
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
          </div>

          <!-- Single Quantity Field (shown for non-swimming services) -->
          <div class="col-md-6" id="singleQuantityGroup">
            <div class="form-group">
              <label for="number_of_people_single">Number of People <span class="text-danger">*</span></label>
              <input class="form-control" type="number" id="number_of_people_single" name="number_of_people_single" min="1" value="1" oninput="calculateDayServicePrice()">
            </div>
          </div>
          <input type="hidden" id="adult_quantity" name="adult_quantity" value="0">
          <input type="hidden" id="child_quantity" name="child_quantity" value="0">
        </div>
        
        <!-- Package Items Section (shown for ceremony/birthday services) -->
        <div id="package_items_section" style="display: none;">
          <h4 class="mb-4 mt-4"><i class="fa fa-gift"></i> Package Items (Optional)</h4>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="checkbox" id="package_drinks" name="package_items[]" value="drinks" onchange="calculateDayServicePrice()">
                  <label class="form-check-label" for="package_drinks">Drinks</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="checkbox" id="package_foods" name="package_items[]" value="foods" onchange="calculateDayServicePrice()">
                  <label class="form-check-label" for="package_foods">Foods</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="checkbox" id="package_decoration" name="package_items[]" value="decoration" onchange="calculateDayServicePrice()">
                  <label class="form-check-label" for="package_decoration">Decoration</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="checkbox" id="package_photos" name="package_items[]" value="photos" onchange="calculateDayServicePrice()">
                  <label class="form-check-label" for="package_photos">Photos</label>
                </div>
              </div>
              <small class="form-text text-muted">Select package items for ceremony/birthday rental</small>
            </div>
          </div>
        </div>

        <h4 class="mb-4 mt-4"><i class="fa fa-calendar"></i> Service Date & Time</h4>
        
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="service_date">Service Date <span class="text-danger">*</span></label>
              <input class="form-control" type="date" id="service_date" name="service_date" value="{{ date('Y-m-d') }}" required>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="service_time">Service Time <span class="text-danger">*</span></label>
              <input class="form-control" type="time" id="service_time" name="service_time" value="{{ date('H:i') }}" required>
            </div>
          </div>
        </div>

        <div class="row" id="items_ordered_section" style="display: none;">
          <div class="col-md-12">
            <div class="form-group">
              <label for="items_ordered">Items Ordered</label>
              <textarea class="form-control" id="items_ordered" name="items_ordered" rows="3" placeholder="Enter items ordered (for restaurant/bar)"></textarea>
            </div>
          </div>
        </div>

        <h4 class="mb-4 mt-4"><i class="fa fa-dollar"></i> Payment Information</h4>
        
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="amount">Amount <span class="text-danger">*</span></label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text" id="currency_symbol">TZS</span>
                </div>
                <input class="form-control" type="number" id="amount" name="amount" step="0.01" min="0" placeholder="0.00" required>
              </div>
              <small class="form-text text-muted" id="currency_hint">Enter amount in TZS</small>
            </div>
          </div>
        </div>

        <div id="payment_section">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="payment_method">Payment Method <span id="payment_method_required" class="text-danger"></span></label>
                <select class="form-control" id="payment_method" name="payment_method">
                  <option value="">Select payment method...</option>
                  <option value="cash">Cash</option>
                  <option value="card">Card</option>
                  <option value="mobile">Mobile Money</option>
                  <option value="bank">Bank Transfer</option>
                  <option value="online">Online Payment</option>
                  <option value="other">Other</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="amount_paid">Amount Paid <span id="amount_paid_required" class="text-danger"></span></label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="paid_currency_symbol">TZS</span>
                  </div>
                  <input class="form-control" type="number" id="amount_paid" name="amount_paid" step="0.01" min="0" placeholder="0.00">
                </div>
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
          <button type="button" id="savePendingBtn" class="btn btn-warning" style="display: none;">
            <i class="fa fa-save"></i> Save as Pending
          </button>
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

@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<script>
$(document).ready(function() {
  const form = document.getElementById('dayServiceForm');
  const serviceTypeSelect = document.getElementById('service_type');
  const guestTypeSelect = document.getElementById('guest_type');
  const paymentMethodSelect = document.getElementById('payment_method');
  const paymentProviderSelect = document.getElementById('payment_provider');
  const paymentSection = document.getElementById('payment_section');
  const itemsOrderedSection = document.getElementById('items_ordered_section');
  const savePendingBtn = document.getElementById('savePendingBtn');
  const paymentMethodRequired = document.getElementById('payment_method_required');
  const amountPaidRequired = document.getElementById('amount_paid_required');
  
  // Use Service model pricing (passed from controller or set to null)
  // serviceModelPricing is already defined above from Blade
  
  // Handle "I don't have children" checkbox
  function handleNoChildrenCheckbox() {
    const checkbox = document.getElementById('no_children_checkbox');
    const childQuantityInput = document.getElementById('child_quantity');
    
    if (checkbox && childQuantityInput) {
      if (checkbox.checked) {
        childQuantityInput.value = 0;
        childQuantityInput.disabled = true;
      } else {
        childQuantityInput.disabled = false;
      }
      calculateDayServicePrice();
    }
  }
  
  // Calculate day service price
  function calculateDayServicePrice() {
    const serviceTypeSelect = document.getElementById('service_type');
    const selectedOption = serviceTypeSelect.options[serviceTypeSelect.selectedIndex];
    const serviceType = serviceTypeSelect.value;
    const guestType = guestTypeSelect.value;
    
    if (!serviceType) {
      document.getElementById('amount').value = '';
      return;
    }
    
    const adultChildGroup = document.getElementById('adultChildQuantityGroup');
    const singleQuantityGroup = document.getElementById('singleQuantityGroup');
    const packageItemsSection = document.getElementById('package_items_section');
    
    // Check if this is a swimming service with adult/child pricing
    const isSwimmingService = serviceType === 'swimming' || serviceType === 'swimming_with_bucket' || serviceType === 'swimming-with-bucket';
    const hasAdultChildPricing = serviceModelPricing && serviceModelPricing.age_group === 'both' && serviceModelPricing.child_price_tsh > 0;
    
    // Check if this is a ceremony/birthday service
    const isPackageService = serviceType === 'ceremony' || serviceType === 'birthday';
    
    // Show/hide appropriate quantity fields
    if (isSwimmingService && hasAdultChildPricing) {
      // Show adult/child quantity fields
      adultChildGroup.style.display = 'block';
      singleQuantityGroup.style.display = 'none';
      packageItemsSection.style.display = 'none';
      
      // Get quantities
      const adultQuantity = parseInt(document.getElementById('adult_quantity').value) || 0;
      const childQuantity = parseInt(document.getElementById('child_quantity').value) || 0;
      
      // Calculate total
      const adultPrice = serviceModelPricing.price_tsh;
      const childPrice = serviceModelPricing.child_price_tsh;
      const adultTotal = adultPrice * adultQuantity;
      const childTotal = childPrice * childQuantity;
      const calculatedAmount = adultTotal + childTotal;
      
      // Update number_of_people hidden field
      document.getElementById('number_of_people').value = adultQuantity + childQuantity;
      
      // Update amount
      document.getElementById('amount').value = calculatedAmount.toFixed(2);
      
    } else if (isPackageService) {
      // Show package items section
      adultChildGroup.style.display = 'none';
      singleQuantityGroup.style.display = 'block';
      packageItemsSection.style.display = 'block';
      
      // Calculate based on base price + selected package items
      const priceTanzanian = parseFloat(selectedOption.getAttribute('data-price-tanzanian') || 0);
      const priceInternational = parseFloat(selectedOption.getAttribute('data-price-international') || priceTanzanian);
      const basePrice = guestType === 'tanzanian' ? priceTanzanian : priceInternational;
      
      // TODO: Add pricing for package items (drinks, foods, decoration, photos)
      // For now, use base price only
      const numPeople = parseInt(document.getElementById('number_of_people_single').value) || 1;
      const pricingType = selectedOption.getAttribute('data-pricing-type');
      
      let calculatedAmount = 0;
      if (pricingType === 'per_person') {
        calculatedAmount = basePrice * numPeople;
      } else {
        calculatedAmount = basePrice;
      }
      
      // Update number_of_people hidden field
      document.getElementById('number_of_people').value = numPeople;
      
      // Update amount
      document.getElementById('amount').value = calculatedAmount.toFixed(2);
      
    } else {
      // Show single quantity field
      adultChildGroup.style.display = 'none';
      singleQuantityGroup.style.display = 'block';
      packageItemsSection.style.display = 'none';
      
      const numPeople = parseInt(document.getElementById('number_of_people_single').value) || 1;
      const pricingType = selectedOption.getAttribute('data-pricing-type');
      const priceTanzanian = parseFloat(selectedOption.getAttribute('data-price-tanzanian') || 0);
      const priceInternational = parseFloat(selectedOption.getAttribute('data-price-international') || priceTanzanian);
      const basePrice = guestType === 'tanzanian' ? priceTanzanian : priceInternational;
      
      let calculatedAmount = 0;
      if (pricingType === 'per_person') {
        calculatedAmount = basePrice * numPeople;
      } else if (pricingType === 'per_hour') {
        calculatedAmount = basePrice;
      } else {
        calculatedAmount = basePrice;
      }
      
      // Update number_of_people hidden field
      document.getElementById('number_of_people').value = numPeople;
      
      // Update amount
      document.getElementById('amount').value = calculatedAmount.toFixed(2);
    }
  }
  
  // Service type change handler
  serviceTypeSelect.addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const serviceType = this.value;
    
    if (!serviceType) {
      itemsOrderedSection.style.display = 'none';
      savePendingBtn.style.display = 'none';
      paymentMethodRequired.textContent = '';
      amountPaidRequired.textContent = '';
      document.getElementById('amount').value = '';
      document.getElementById('adultChildQuantityGroup').style.display = 'none';
      document.getElementById('singleQuantityGroup').style.display = 'block';
      document.getElementById('package_items_section').style.display = 'none';
      return;
    }
    
    // Get service configuration from data attributes
    const paymentUpfront = selectedOption.getAttribute('data-payment-upfront') === '1';
    const requiresItems = selectedOption.getAttribute('data-requires-items') === '1';
    const pricingType = selectedOption.getAttribute('data-pricing-type');
    const guestType = guestTypeSelect.value;
    
    // Calculate price
    calculateDayServicePrice();
    
    // Show/hide items ordered section
    if (requiresItems) {
      itemsOrderedSection.style.display = 'block';
    } else {
      itemsOrderedSection.style.display = 'none';
    }
    
    // Configure payment requirements
    if (paymentUpfront) {
      savePendingBtn.style.display = 'none';
      paymentMethodRequired.textContent = '*';
      amountPaidRequired.textContent = '*';
      document.getElementById('payment_method').setAttribute('required', 'required');
      document.getElementById('amount_paid').setAttribute('required', 'required');
      document.getElementById('service_type_hint').innerHTML = '<span class="text-info"><i class="fa fa-info-circle"></i> Payment is required upfront for this service.</span>';
    } else {
      savePendingBtn.style.display = 'inline-block';
      paymentMethodRequired.textContent = '';
      amountPaidRequired.textContent = '';
      document.getElementById('payment_method').removeAttribute('required');
      document.getElementById('amount_paid').removeAttribute('required');
      document.getElementById('service_type_hint').innerHTML = '<span class="text-muted"><i class="fa fa-info-circle"></i> Payment can be processed later for this service.</span>';
    }
  });

  // Guest type change handler
  guestTypeSelect.addEventListener('change', function() {
    const guestType = this.value;
    const currencySymbol = guestType === 'tanzanian' ? 'TZS' : 'USD';
    document.getElementById('currency_symbol').textContent = currencySymbol;
    document.getElementById('paid_currency_symbol').textContent = currencySymbol;
    document.getElementById('currency_hint').textContent = guestType === 'tanzanian' 
      ? 'Enter amount in TZS' 
      : 'Enter amount in USD';
    
    // Recalculate amount if service is selected
    const serviceTypeSelect = document.getElementById('service_type');
    if (serviceTypeSelect.value) {
      serviceTypeSelect.dispatchEvent(new Event('change'));
    }
  });
  
  // Number of people change handler (for per_person pricing)
  const numberPeopleSingleInput = document.getElementById('number_of_people_single');
  if (numberPeopleSingleInput) {
    numberPeopleSingleInput.addEventListener('input', calculateDayServicePrice);
  }
  
  // Adult/Child quantity change handlers
  const adultQuantityInput = document.getElementById('adult_quantity');
  const childQuantityInput = document.getElementById('child_quantity');
  if (adultQuantityInput) {
    adultQuantityInput.addEventListener('input', calculateDayServicePrice);
  }
  if (childQuantityInput) {
    childQuantityInput.addEventListener('input', calculateDayServicePrice);
  }

  // Payment method change handler
  paymentMethodSelect.addEventListener('change', function() {
    const paymentMethod = this.value;
    const providerSection = document.getElementById('payment_provider_section');
    
    if (paymentMethod === 'mobile') {
      providerSection.style.display = 'block';
      paymentProviderSelect.innerHTML = `
        <option value="">Select provider...</option>
        <option value="M-PESA">M-PESA</option>
        <option value="HALOPESA">HALOPESA</option>
        <option value="MIXX BY YAS">MIXX BY YAS</option>
        <option value="AIRTEL MONEY">AIRTEL MONEY</option>
      `;
    } else if (paymentMethod === 'bank') {
      providerSection.style.display = 'block';
      paymentProviderSelect.innerHTML = `
        <option value="">Select provider...</option>
        <option value="NMB">NMB</option>
        <option value="CRDB">CRDB</option>
        <option value="KCB">KCB BANK</option>
        <option value="NBC">NBC</option>
        <option value="EXIM">EXIM</option>
      `;
    } else {
      providerSection.style.display = 'none';
      paymentProviderSelect.value = '';
      document.getElementById('payment_reference').value = '';
    }
  });

  // Save as pending (for restaurant/bar)
  savePendingBtn.addEventListener('click', function() {
    if (!form.checkValidity()) {
      form.reportValidity();
      return;
    }

    const formData = new FormData(form);
    formData.append('payment_status', 'pending');
    formData.append('amount_paid', '0');

    submitForm(formData, false);
  });

  // Form submission
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!form.checkValidity()) {
      form.reportValidity();
      return;
    }

    const serviceType = serviceTypeSelect.value;
    
    // For swimming, payment is required
    if (serviceType === 'swimming') {
      if (!paymentMethodSelect.value || !document.getElementById('amount_paid').value) {
        swal({
          title: "Payment Required",
          text: "Payment method and amount are required for swimming/pool access.",
          type: "error",
          confirmButtonColor: "#d33"
        });
        return;
      }
    }

    const formData = new FormData(form);
    formData.append('payment_status', 'paid');
    
    if (!formData.get('amount_paid')) {
      formData.set('amount_paid', formData.get('amount'));
    }

    submitForm(formData, true);
  });

  function submitForm(formData, isPaid) {
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Processing...';

    const alertDiv = document.getElementById('formAlert');
    alertDiv.innerHTML = '';

    @php
      $storeRoute = ($role === 'reception') ? 'reception.day-services.store' : 'admin.day-services.store';
    @endphp

    fetch('{{ route($storeRoute) }}', {
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
          if (data.receipt_url && isPaid) {
            window.open(data.receipt_url, '_blank');
          }
          window.location.href = '{{ $role === "reception" ? route("reception.day-services.index") : route("admin.day-services.index") }}';
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
  }
});
</script>
@endsection

