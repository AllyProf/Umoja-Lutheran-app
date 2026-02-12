@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-tint"></i> Swimming Pool Registration</h1>
    <p>Register swimming pool access for guests</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ $role === 'reception' ? route('reception.dashboard') : route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Day Services</a></li>
    <li class="breadcrumb-item"><a href="#">Swimming Pool</a></li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title">Swimming Pool Access Registration</h3>
      <div class="tile-body">
        <form id="swimmingForm" method="POST" action="{{ $role === 'reception' ? route('reception.day-services.store') : route('admin.day-services.store') }}">
          @csrf
          <input type="hidden" name="service_type" value="swimming">
          <input type="hidden" name="payment_status" value="paid">
          <input type="hidden" name="guest_type" id="guest_type" value="tanzanian">

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
          
          <!-- Adult/Child Quantity Fields (shown if service supports both) -->
          @if(isset($serviceModel) && $serviceModel->age_group === 'both' && $serviceModel->child_price_tsh && $serviceModel->child_price_tsh > 0)
          <div class="row" id="adultChildQuantityGroup">
            <div class="col-md-6">
              <div class="form-group">
                <label for="adult_quantity">Number of Adults <span class="text-danger">*</span></label>
                <input class="form-control" type="number" id="adult_quantity" name="adult_quantity" value="1" min="0" required oninput="calculateAmount()">
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
          @else
          <!-- Single Quantity Field (for services without adult/child pricing) -->
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="number_of_people">Number of People <span class="text-danger">*</span></label>
                <input class="form-control" type="number" id="number_of_people" name="number_of_people" value="1" min="1" required oninput="calculateAmount()">
              </div>
            </div>
          </div>
          <input type="hidden" id="adult_quantity" name="adult_quantity" value="0">
          <input type="hidden" id="child_quantity" name="child_quantity" value="0">
          @endif

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

          <h4 class="mb-4 mt-4"><i class="fa fa-dollar"></i> Payment Information</h4>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="amount">Amount <span class="text-danger">*</span></label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="currency_symbol">TZS</span>
                  </div>
                  <input class="form-control" type="number" step="0.01" id="amount" name="amount" 
                         value="{{ number_format($swimmingService->price_tanzanian, 2, '.', '') }}" 
                         min="0" required>
                </div>
                <small class="form-text text-muted" id="amount_conversion"></small>
                <small class="form-text text-info">
                  <i class="fa fa-info-circle"></i> Recommended: 
                  <span id="recommended_amount_tzs">{{ number_format($swimmingService->price_tanzanian, 2) }} TZS</span>
                  @if($swimmingService->price_international)
                    / <span id="recommended_amount_usd">${{ number_format($swimmingService->price_international, 2) }} USD</span>
                  @endif
                  (per person)
                </small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="payment_method">Payment Method <span class="text-danger">*</span></label>
                <select class="form-control" id="payment_method" name="payment_method" required>
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
                <input class="form-control" type="text" id="payment_reference" name="payment_reference" placeholder="Transaction ID or Reference Number">
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="amount_paid">Amount Paid <span class="text-danger">*</span></label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text" id="paid_currency_symbol">TZS</span>
              </div>
              <input class="form-control" type="number" step="0.01" id="amount_paid" name="amount_paid" 
                     value="{{ number_format($swimmingService->price_tanzanian, 2, '.', '') }}" 
                     min="0" required>
            </div>
            <small class="form-text text-muted" id="amount_paid_conversion"></small>
          </div>

          <div class="form-group">
            <label for="notes">Notes (Optional)</label>
            <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Any additional notes..."></textarea>
          </div>

          <div id="formAlert"></div>

          <div class="form-group mt-4">
            <button type="submit" class="btn btn-primary">
              <i class="fa fa-check"></i> Process Payment & Generate Receipt
            </button>
            <a href="{{ $role === 'reception' ? route('reception.dashboard') : route('admin.dashboard') }}" class="btn btn-secondary">
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
  const form = document.getElementById('swimmingForm');
  const guestTypeInput = document.getElementById('guest_type'); // Hidden input
  const numberPeopleInput = document.getElementById('number_of_people');
  const amountInput = document.getElementById('amount');
  const amountPaidInput = document.getElementById('amount_paid');
  const paymentMethodSelect = document.getElementById('payment_method');
  const paymentProviderSection = document.getElementById('payment_provider_section');
  const paymentProviderSelect = document.getElementById('payment_provider');
  const currencySymbol = document.getElementById('currency_symbol');
  const paidCurrencySymbol = document.getElementById('paid_currency_symbol');
  const amountConversion = document.getElementById('amount_conversion');
  const amountPaidConversion = document.getElementById('amount_paid_conversion');
  
  const exchangeRate = {{ $exchangeRate }};
  const priceTanzanian = {{ $swimmingService->price_tanzanian }};
  const pricingType = '{{ $swimmingService->pricing_type }}';
  
  // Get Service model pricing if available
  @if(isset($serviceModel) && $serviceModel->age_group === 'both' && $serviceModel->child_price_tsh && $serviceModel->child_price_tsh > 0)
  const adultPrice = {{ $serviceModel->price_tsh }};
  const childPrice = {{ $serviceModel->child_price_tsh }};
  const hasAdultChildPricing = true;
  @else
  const adultPrice = priceTanzanian;
  const childPrice = 0;
  const hasAdultChildPricing = false;
  @endif
  
  // Guest type is always Tanzanian for swimming pool
  const guestType = 'tanzanian';

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
      calculateAmount();
    }
  }

  // Update currency and calculate amount based on adult/child quantities or number of people
  function calculateAmount() {
    const currency = 'TZS'; // Always TZS for Tanzanian guests
    
    currencySymbol.textContent = currency;
    paidCurrencySymbol.textContent = currency;
    
    let calculatedAmount = 0;
    let breakdownText = '';
    const priceBreakdown = document.getElementById('price_breakdown');
    const breakdownTextEl = document.getElementById('breakdown_text');
    
    if (hasAdultChildPricing) {
      // Calculate based on adult/child quantities
      const adultQuantity = parseInt(document.getElementById('adult_quantity').value) || 0;
      const childQuantity = parseInt(document.getElementById('child_quantity').value) || 0;
      
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
        breakdownText = breakdown.join('<br>');
        priceBreakdown.style.display = 'block';
        breakdownTextEl.innerHTML = breakdownText;
      } else {
        priceBreakdown.style.display = 'none';
      }
    } else {
      // Calculate based on single quantity
      const numPeople = parseInt(numberPeopleInput.value) || 1;
      if (pricingType === 'per_person') {
        calculatedAmount = priceTanzanian * numPeople;
      } else {
        calculatedAmount = priceTanzanian;
      }
      priceBreakdown.style.display = 'none';
    }
    
    amountInput.value = calculatedAmount.toFixed(2);
    amountPaidInput.value = calculatedAmount.toFixed(2);
    
    // No conversion display needed for TZS only
    amountConversion.textContent = '';
    amountPaidConversion.textContent = '';
  }

  // Payment method change handler
  function updatePaymentProvider() {
    const paymentMethod = paymentMethodSelect.value;
    
    if (paymentMethod === 'mobile') {
      paymentProviderSection.style.display = 'block';
      paymentProviderSelect.innerHTML = `
        <option value="">Select provider...</option>
        <option value="M-PESA">M-PESA</option>
        <option value="HALOPESA">HALOPESA</option>
        <option value="MIXX BY YAS">MIXX BY YAS</option>
        <option value="AIRTEL MONEY">AIRTEL MONEY</option>
      `;
    } else if (paymentMethod === 'bank') {
      paymentProviderSection.style.display = 'block';
      paymentProviderSelect.innerHTML = `
        <option value="">Select provider...</option>
        <option value="NMB">NMB</option>
        <option value="CRDB">CRDB</option>
        <option value="KCB">KCB BANK</option>
        <option value="NBC">NBC</option>
        <option value="EXIM">EXIM</option>
      `;
    } else if (paymentMethod === 'cash' || paymentMethod === 'online' || paymentMethod === 'other') {
      paymentProviderSection.style.display = 'none';
      paymentProviderSelect.value = '';
      document.getElementById('payment_reference').value = '';
    } else {
      paymentProviderSection.style.display = 'none';
    }
  }

  // Event listeners
  if (hasAdultChildPricing) {
    const adultQuantityInput = document.getElementById('adult_quantity');
    const childQuantityInput = document.getElementById('child_quantity');
    if (adultQuantityInput) {
      adultQuantityInput.addEventListener('input', calculateAmount);
    }
    if (childQuantityInput) {
      childQuantityInput.addEventListener('input', calculateAmount);
    }
  } else {
    if (numberPeopleInput) {
      numberPeopleInput.addEventListener('input', calculateAmount);
    }
  }
  paymentMethodSelect.addEventListener('change', updatePaymentProvider);
  
  // Sync amount paid with amount when amount changes
  amountInput.addEventListener('input', function() {
    if (amountPaidInput.value === '' || parseFloat(amountPaidInput.value) === parseFloat(amountInput.value)) {
      amountPaidInput.value = amountInput.value;
    }
  });

  // Form submission
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
    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Processing...';

    const alertDiv = document.getElementById('formAlert');
    alertDiv.innerHTML = '';

    fetch(form.action, {
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
          text: data.message || "Swimming pool access registered successfully!",
          type: "success",
          confirmButtonColor: "#28a745"
        }, function() {
          if (data.receipt_url) {
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
  });

  // Initial calculation
  calculateAmount();
});
</script>
@endsection

