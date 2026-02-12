@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-birthday-cake"></i> Ceremony Service Registration</h1>
    <p>Register ceremony/birthday package services for guests</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ $role === 'reception' ? route('reception.dashboard') : route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ $role === 'reception' ? route('reception.day-services.index') : route('admin.day-services.index') }}">Day Services</a></li>
    <li class="breadcrumb-item"><a href="#">Ceremony Service</a></li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title">Ceremony/Birthday Package Registration</h3>
      <div class="tile-body">
        <form id="ceremonyForm" method="POST" action="{{ $role === 'reception' ? route('reception.day-services.store') : route('admin.day-services.store') }}">
          @csrf
          <input type="hidden" name="service_type" value="{{ $ceremonyPackage->service_key }}">
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
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">+255</span>
                  </div>
                  <input class="form-control" type="text" id="guest_phone" name="guest_phone" placeholder="712345678" oninput="formatPhoneNumber()">
                </div>
                <small class="form-text text-muted">Enter number without country code (e.g., 712345678)</small>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="guest_email">Email</label>
                <input class="form-control" type="email" id="guest_email" name="guest_email" placeholder="guest@example.com">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="number_of_people">Number of People <span class="text-danger">*</span></label>
                <input class="form-control" type="number" id="number_of_people" name="number_of_people" value="1" min="1" required oninput="calculateTotal()">
              </div>
            </div>
          </div>

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

                 <h4 class="mb-4 mt-4"><i class="fa fa-gift"></i> Package Items</h4>
                 
                 <div class="alert alert-info">
                   <i class="fa fa-info-circle"></i> <strong>Note:</strong> Select the package items you want to include by checking the boxes. Uncheck items you don't need (e.g., if guest doesn't want Photos or Drinks). Enter prices for selected items. Check "Paid Upfront" for items that will be paid during registration.
                 </div>
                 
                 <div class="row" id="packageItemsContainer">
                   @foreach($packageItems as $key => $label)
                   <div class="col-md-6 package-item-row" data-item-key="{{ $key }}">
                     <div class="form-group">
                       <label for="package_item_{{ $key }}">
                         <div class="form-check form-check-inline">
                           <input class="form-check-input package-item-include-checkbox" 
                                  type="checkbox" 
                                  id="package_item_include_{{ $key }}" 
                                  checked
                                  onchange="togglePackageItem('{{ $key }}'); calculateTotal(); calculatePaymentBreakdown();">
                           <label class="form-check-label" for="package_item_include_{{ $key }}" style="font-weight: bold;">
                             {{ $label }}
                           </label>
                         </div>
                         <div class="form-check form-check-inline ml-3">
                           <input class="form-check-input package-item-paid-checkbox" 
                                  type="checkbox" 
                                  id="package_item_paid_{{ $key }}" 
                                  name="package_items_paid[{{ $key }}]" 
                                  value="1"
                                  onchange="calculatePaymentBreakdown()">
                           <label class="form-check-label" for="package_item_paid_{{ $key }}" style="font-size: 12px; font-weight: normal;">
                             Paid Upfront
                           </label>
                         </div>
                       </label>
                       <div class="input-group">
                         <div class="input-group-prepend">
                           <span class="input-group-text">TZS</span>
                         </div>
                         <input class="form-control package-item-price" type="number" 
                                id="package_item_{{ $key }}" 
                                name="package_items[{{ $key }}]" 
                                step="0.01" min="0" value="0" 
                                oninput="calculateTotal(); calculatePaymentBreakdown();">
                       </div>
                     </div>
                   </div>
                   @endforeach
                 </div>
                 
                  <!-- Payment Breakdown -->
                  <div class="row mt-3">
                    <div class="col-md-12">
                      <div class="card">
                        <div class="card-header bg-light">
                          <strong><i class="fa fa-calculator"></i> Payment Breakdown</strong>
                        </div>
                        <div class="card-body">
                          <div class="row">
                            <div class="col-md-12">
                              <p><strong>Amount to Pay Now:</strong> <span id="upfront_amount" class="text-success">0.00 TZS</span></p>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

          <!-- Additional Items Section -->
          <div class="row mt-3">
            <div class="col-md-12">
              <button type="button" class="btn btn-sm btn-outline-primary" onclick="addAdditionalItem()">
                <i class="fa fa-plus"></i> Add Additional Item
              </button>
            </div>
          </div>

          <div id="additionalItemsContainer" class="mt-3">
            <!-- Additional items will be added here dynamically -->
          </div>

          <h4 class="mb-4 mt-4"><i class="fa fa-money"></i> Payment Information</h4>
          
           <div class="row">
             <div class="col-md-6">
               <div class="form-group">
                 <label for="total_amount">Total Amount <span class="text-danger">*</span></label>
                 <div class="input-group">
                   <div class="input-group-prepend">
                     <span class="input-group-text" id="currency_symbol">TZS</span>
                   </div>
                   <input class="form-control" type="number" id="total_amount" name="amount" step="0.01" min="0" readonly>
                 </div>
                 <small class="form-text text-muted">Calculated from package items</small>
               </div>
             </div>
             <div class="col-md-6">
               <div class="form-group">
                 <label for="amount_paid">Amount Paid (Upfront) <span class="text-danger">*</span></label>
                 <div class="input-group">
                   <div class="input-group-prepend">
                     <span class="input-group-text" id="paid_currency_symbol">TZS</span>
                   </div>
                   <input class="form-control" type="number" id="amount_paid" name="amount_paid" step="0.01" min="0" required oninput="updateAmountPaid()">
                 </div>
                 <small class="form-text text-muted">This should match the "Amount to Pay Now" above. Remaining amount can be paid after the ceremony.</small>
               </div>
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
                <label for="payment_provider">Payment Provider / Platform <span class="text-danger" id="payment_provider_required"></span></label>
                <select class="form-control" id="payment_provider" name="payment_provider">
                  <option value="">Select provider/platform...</option>
                </select>
                <small class="form-text text-muted">Select the payment platform or provider</small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="payment_reference">Reference Number / Transaction ID <span class="text-danger" id="payment_reference_required"></span></label>
                <input class="form-control" type="text" id="payment_reference" name="payment_reference" placeholder="Enter transaction reference">
                <small class="form-text text-muted">Enter the transaction reference or ID</small>
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
// Toggle package item inclusion
window.togglePackageItem = function(itemKey) {
  const includeCheckbox = document.getElementById('package_item_include_' + itemKey);
  const priceInput = document.getElementById('package_item_' + itemKey);
  const paidCheckbox = document.getElementById('package_item_paid_' + itemKey);
  
  if (includeCheckbox && priceInput) {
    if (includeCheckbox.checked) {
      // Item is included - enable price input
      priceInput.disabled = false;
      priceInput.required = false;
      if (paidCheckbox) {
        paidCheckbox.disabled = false;
      }
    } else {
      // Item is excluded - disable and reset price input
      priceInput.disabled = true;
      priceInput.value = 0;
      priceInput.required = false;
      if (paidCheckbox) {
        paidCheckbox.disabled = true;
        paidCheckbox.checked = false;
      }
    }
  }
};

// Make calculateTotal globally accessible (outside document.ready)
function calculateTotal() {
  let total = 0;
  
  // Calculate from predefined package items (only if included)
  const packageItems = document.querySelectorAll('.package-item-price');
  packageItems.forEach(item => {
    const itemKey = item.closest('.package-item-row').dataset.itemKey;
    const includeCheckbox = document.getElementById('package_item_include_' + itemKey);
    
    // Only include if checkbox is checked
    if (includeCheckbox && includeCheckbox.checked && !item.disabled) {
      const price = parseFloat(item.value) || 0;
      total += price;
    }
  });
  
  // Calculate from additional items
  const additionalItems = document.querySelectorAll('.additional-item-price');
  additionalItems.forEach(item => {
    const price = parseFloat(item.value) || 0;
    total += price;
  });
  
  const totalAmountInput = document.getElementById('total_amount');
  if (totalAmountInput) {
    totalAmountInput.value = total.toFixed(2);
  }
  
  // Calculate payment breakdown
  calculatePaymentBreakdown();
}

// Calculate payment breakdown (upfront vs later) - globally accessible
function calculatePaymentBreakdown() {
  let upfrontAmount = 0;
  let laterAmount = 0;
  
  // Calculate from predefined package items (only if included)
  document.querySelectorAll('.package-item-row').forEach(row => {
    const itemKey = row.dataset.itemKey;
    const includeCheckbox = document.getElementById('package_item_include_' + itemKey);
    const priceInput = row.querySelector('.package-item-price');
    const paidCheckbox = row.querySelector('.package-item-paid-checkbox');
    
    // Only calculate if item is included
    if (includeCheckbox && includeCheckbox.checked && priceInput && !priceInput.disabled) {
      const price = parseFloat(priceInput.value) || 0;
      
      if (paidCheckbox && paidCheckbox.checked) {
        upfrontAmount += price;
      } else {
        laterAmount += price;
      }
    }
  });
  
  // Calculate from additional items (based on "Paid Upfront" checkbox)
  document.querySelectorAll('.additional-item-row').forEach(row => {
    const priceInput = row.querySelector('.additional-item-price');
    const paidCheckbox = row.querySelector('.additional-item-paid-checkbox');
    const price = parseFloat(priceInput ? priceInput.value : 0) || 0;
    
    if (paidCheckbox && paidCheckbox.checked) {
      upfrontAmount += price;
    } else {
      laterAmount += price;
    }
  });
  
  // Update display
  const upfrontDisplay = document.getElementById('upfront_amount');
  const laterDisplay = document.getElementById('later_amount');
  
  if (upfrontDisplay) {
    upfrontDisplay.textContent = upfrontAmount.toFixed(2) + ' TZS';
  }
  if (laterDisplay) {
    laterDisplay.textContent = laterAmount.toFixed(2) + ' TZS';
  }
  
  // Auto-update amount paid to match upfront amount (only if not manually changed)
  const amountPaid = document.getElementById('amount_paid');
  if (amountPaid) {
    const currentPaid = parseFloat(amountPaid.value) || 0;
    const total = upfrontAmount + laterAmount;
    
    // Only auto-update if user hasn't manually changed it
    // Initialize the flag if not set
    if (typeof window.userManuallyChangedAmountPaid === 'undefined') {
      window.userManuallyChangedAmountPaid = false;
    }
    
    // Initialize the flag if not set
    if (typeof window.userManuallyChangedAmountPaid === 'undefined') {
      window.userManuallyChangedAmountPaid = false;
    }
    
    if (!window.userManuallyChangedAmountPaid) {
      // Auto-update if:
      // 1. Current value is 0 or very small (< 10) - likely not manually set
      // 2. Or current value is significantly different from upfront amount (was old value)
      // 3. Or upfront amount changed and current paid doesn't match
      if (currentPaid === 0 || currentPaid < 10 || Math.abs(currentPaid - upfrontAmount) > 100) {
        amountPaid.value = upfrontAmount.toFixed(2);
        // Reset flag after auto-update since we're setting it automatically
        window.userManuallyChangedAmountPaid = false;
      }
    }
  }
  
  // Note: Amount Remaining and Percent Paid fields have been removed
}

// updatePaymentSummary function removed - Amount Remaining and Percent Paid fields have been removed

$(document).ready(function() {
  const form = document.getElementById('ceremonyForm');
  let additionalItemCounter = 0;
  
  // Format phone number - ensure it doesn't include +255 prefix
  window.formatPhoneNumber = function() {
    const phoneInput = document.getElementById('guest_phone');
    if (phoneInput) {
      let value = phoneInput.value.replace(/\D/g, ''); // Remove all non-digits
      
      // Remove +255 if user typed it
      if (value.startsWith('255')) {
        value = value.substring(3);
      }
      
      phoneInput.value = value;
    }
  };
  
  // Initialize flag to track manual changes
  window.userManuallyChangedAmountPaid = false;
  
  // Update amount paid - track if user manually changed it
  const amountPaidInput = document.getElementById('amount_paid');
  if (amountPaidInput) {
    // Track manual changes - only mark as manual if value is significantly different from upfront
    amountPaidInput.addEventListener('input', function() {
      const currentValue = parseFloat(this.value) || 0;
      const upfrontDisplay = document.getElementById('upfront_amount');
      const upfrontText = upfrontDisplay ? upfrontDisplay.textContent.replace(/[^\d.]/g, '') : '0';
      const upfrontAmount = parseFloat(upfrontText) || 0;
      
      // Mark as manually changed if value differs significantly from upfront amount
      if (Math.abs(currentValue - upfrontAmount) > 1 && upfrontAmount > 0) {
        window.userManuallyChangedAmountPaid = true;
      } else {
        // If user types a value close to upfront amount, don't mark as manual
        window.userManuallyChangedAmountPaid = false;
      }
    });
  }
  
  window.updateAmountPaid = function() {
    // Amount Remaining and Percent Paid fields have been removed
  }
  
  // Initialize payment breakdown on page load
  setTimeout(function() {
    calculatePaymentBreakdown();
  }, 100);
  
  // Add additional item
  window.addAdditionalItem = function() {
    additionalItemCounter++;
    const container = document.getElementById('additionalItemsContainer');
    
    const itemHtml = `
      <div class="row additional-item-row mb-2" id="additional_item_${additionalItemCounter}">
        <div class="col-md-4">
          <div class="form-group">
            <label for="additional_item_name_${additionalItemCounter}">Item Name <span class="text-danger">*</span></label>
            <input class="form-control additional-item-name" type="text" 
                   id="additional_item_name_${additionalItemCounter}" 
                   name="additional_items[${additionalItemCounter}][name]" 
                   placeholder="Enter item name" required>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label for="additional_item_price_${additionalItemCounter}">
              Price <span class="text-danger">*</span>
              <div class="form-check form-check-inline ml-2">
                <input class="form-check-input additional-item-paid-checkbox" 
                       type="checkbox" 
                       id="additional_item_paid_${additionalItemCounter}" 
                       name="additional_items_paid[${additionalItemCounter}]" 
                       value="1"
                       checked
                       onchange="calculatePaymentBreakdown()">
                <label class="form-check-label" for="additional_item_paid_${additionalItemCounter}" style="font-size: 12px; font-weight: normal;">
                  Paid Upfront
                </label>
              </div>
            </label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">TZS</span>
              </div>
              <input class="form-control additional-item-price" type="number" 
                     id="additional_item_price_${additionalItemCounter}" 
                     name="additional_items[${additionalItemCounter}][price]" 
                     step="0.01" min="0" value="0" 
                     required oninput="calculateTotal(); calculatePaymentBreakdown();">
            </div>
          </div>
        </div>
        <div class="col-md-2">
          <div class="form-group">
            <label>&nbsp;</label>
            <button type="button" class="btn btn-sm btn-danger btn-block" onclick="removeAdditionalItem(${additionalItemCounter})">
              <i class="fa fa-times"></i> Remove
            </button>
          </div>
        </div>
      </div>
    `;
    
    container.insertAdjacentHTML('beforeend', itemHtml);
    // Recalculate after adding
    calculatePaymentBreakdown();
  };
  
  // Remove additional item
  window.removeAdditionalItem = function(itemId) {
    const itemRow = document.getElementById('additional_item_' + itemId);
    if (itemRow) {
      itemRow.remove();
      calculateTotal();
    }
  };
  
  // Toggle payment provider based on payment method - Make globally accessible
  window.togglePaymentProvider = function() {
    const paymentMethod = document.getElementById('payment_method');
    const paymentProviderSection = document.getElementById('payment_provider_section');
    const paymentProviderSelect = document.getElementById('payment_provider');
    const paymentReferenceInput = document.getElementById('payment_reference');
    
    if (!paymentMethod || !paymentProviderSection || !paymentProviderSelect) {
      return;
    }
    
    const selectedMethod = paymentMethod.value;
    
    if (selectedMethod === 'mobile' || selectedMethod === 'bank' || selectedMethod === 'card') {
      paymentProviderSection.style.display = 'block';
      
      paymentProviderSelect.innerHTML = '<option value="">Select provider...</option>';
      
      if (selectedMethod === 'mobile') {
        paymentProviderSelect.innerHTML += `
          <option value="m-pesa">M-PESA</option>
          <option value="halopesa">HALOPESA</option>
          <option value="mixx-by-yas">MIXX BY YAS</option>
          <option value="airtel-money">AIRTEL MONEY</option>
        `;
        document.getElementById('payment_provider_required').textContent = '*';
        document.getElementById('payment_reference_required').textContent = '*';
        if (paymentReferenceInput) {
          paymentReferenceInput.required = true;
          paymentReferenceInput.placeholder = "Enter transaction reference (Required)";
        }
        paymentProviderSelect.required = true;
      } else if (selectedMethod === 'bank') {
        paymentProviderSelect.innerHTML += `
          <option value="nmb">NMB</option>
          <option value="crdb">CRDB</option>
          <option value="kcb">KCB Bank</option>
          <option value="exim">Exim Bank</option>
          <option value="equity">Equity Bank</option>
          <option value="stanbic">Stanbic Bank</option>
        `;
        document.getElementById('payment_provider_required').textContent = '*';
        document.getElementById('payment_reference_required').textContent = '*';
        if (paymentReferenceInput) {
          paymentReferenceInput.required = true;
          paymentReferenceInput.placeholder = "Enter transaction reference (Required)";
        }
        paymentProviderSelect.required = true;
      } else if (selectedMethod === 'card') {
        paymentProviderSelect.innerHTML += `
          <option value="visa">Visa</option>
          <option value="mastercard">Mastercard</option>
          <option value="amex">American Express</option>
        `;
        document.getElementById('payment_provider_required').textContent = '*';
        document.getElementById('payment_reference_required').textContent = '';
        if (paymentReferenceInput) {
          paymentReferenceInput.required = false;
          paymentReferenceInput.placeholder = "Transaction ID (Optional)";
        }
        paymentProviderSelect.required = true;
      }
    } else {
      // Cash or Other - Hide provider section
      paymentProviderSection.style.display = 'none';
      paymentProviderSelect.value = '';
      paymentProviderSelect.required = false;
      document.getElementById('payment_provider_required').textContent = '';
      document.getElementById('payment_reference_required').textContent = '';
      if (paymentReferenceInput) {
        paymentReferenceInput.value = '';
        paymentReferenceInput.required = false;
        paymentReferenceInput.placeholder = "Enter transaction reference";
      }
    }
  }
  
  // Form submission
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!form.checkValidity()) {
      form.reportValidity();
      return;
    }
    
    // Collect package items as an object (key: price pairs) - only included items
    const packageItemsData = {};
    const packageItemInputs = document.querySelectorAll('.package-item-price');
    packageItemInputs.forEach(input => {
      const itemKey = input.closest('.package-item-row').dataset.itemKey;
      const includeCheckbox = document.getElementById('package_item_include_' + itemKey);
      
      // Only include if checkbox is checked and item is not disabled
      if (includeCheckbox && includeCheckbox.checked && !input.disabled) {
        const price = parseFloat(input.value) || 0;
        // Only include items with price > 0
        if (price > 0) {
          packageItemsData[itemKey] = price;
        }
      }
    });
    
    // Collect additional items
    const additionalItemsData = {};
    const additionalItemRows = document.querySelectorAll('.additional-item-row');
    additionalItemRows.forEach(row => {
      const nameInput = row.querySelector('.additional-item-name');
      const priceInput = row.querySelector('.additional-item-price');
      if (nameInput && priceInput) {
        const name = nameInput.value.trim();
        const price = parseFloat(priceInput.value) || 0;
        if (name && price > 0) {
          additionalItemsData[name] = price;
        }
      }
    });
    
    // Merge package items and additional items
    const allPackageItems = { ...packageItemsData, ...additionalItemsData };
    
    const formData = new FormData(form);
    
    // Remove the package_items form fields (they're sent as package_items[key])
    // We'll send them as JSON instead
    formData.delete('package_items[food]');
    formData.delete('package_items[swimming]');
    formData.delete('package_items[drinks]');
    formData.delete('package_items[photos]');
    formData.delete('package_items[decoration]');
    
    // Add package items as JSON string
    if (Object.keys(allPackageItems).length > 0) {
      formData.append('package_items', JSON.stringify(allPackageItems));
    } else {
      // Send empty object as JSON if no items
      formData.append('package_items', JSON.stringify({}));
    }
    
    // Show loading
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
  calculateTotal();
});
</script>
@endsection

