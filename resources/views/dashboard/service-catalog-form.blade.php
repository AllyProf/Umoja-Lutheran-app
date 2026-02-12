@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-{{ isset($serviceCatalog) ? 'edit' : 'plus' }}"></i> {{ isset($serviceCatalog) ? 'Edit' : 'Add' }} Service</h1>
    <p>{{ isset($serviceCatalog) ? 'Update service catalog item' : 'Add new service to catalog' }}</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.service-catalog.index') }}">Service Catalog</a></li>
    <li class="breadcrumb-item"><a href="#">{{ isset($serviceCatalog) ? 'Edit' : 'Add' }}</a></li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <form id="serviceCatalogForm">
        @csrf
        @if(isset($serviceCatalog))
          @method('PUT')
        @endif
        
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="service_name">Service Name <span class="text-danger">*</span></label>
              <input class="form-control" type="text" id="service_name" name="service_name" 
                     value="{{ $serviceCatalog->service_name ?? '' }}" 
                     placeholder="e.g., Swimming Pool Access" required>
              <small class="form-text text-muted">Display name for the service</small>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="service_key">Service Key <span class="text-danger">*</span></label>
              <input class="form-control" type="text" id="service_key" name="service_key" 
                     value="{{ $serviceCatalog->service_key ?? '' }}" 
                     placeholder="e.g., swimming" required>
              <small class="form-text text-muted">Unique identifier (lowercase, no spaces, e.g., swimming, ceremony)</small>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label for="description">Description</label>
              <textarea class="form-control" id="description" name="description" rows="3" 
                        placeholder="Brief description of the service...">{{ $serviceCatalog->description ?? '' }}</textarea>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label for="pricing_type">Pricing Type <span class="text-danger">*</span></label>
              <select class="form-control" id="pricing_type" name="pricing_type" required onchange="updatePriceFieldsVisibility()">
                <option value="fixed" {{ (isset($serviceCatalog) && $serviceCatalog->pricing_type === 'fixed') ? 'selected' : '' }}>Fixed Price</option>
                <option value="per_person" {{ (isset($serviceCatalog) && $serviceCatalog->pricing_type === 'per_person') ? 'selected' : '' }}>Per Person</option>
                <option value="per_hour" {{ (isset($serviceCatalog) && $serviceCatalog->pricing_type === 'per_hour') ? 'selected' : '' }}>Per Hour</option>
                <option value="custom" {{ (isset($serviceCatalog) && $serviceCatalog->pricing_type === 'custom') ? 'selected' : '' }}>Custom (Manual Entry)</option>
              </select>
              <small class="form-text text-muted">How the price is calculated</small>
            </div>
          </div>
          <div class="col-md-4" id="priceInternationalGroup">
            <div class="form-group">
              <label for="price_international">Price for International Guests (USD)</label>
              <input class="form-control" type="number" id="price_international" name="price_international" 
                     value="{{ $serviceCatalog->price_international ?? '' }}" 
                     step="0.01" min="0" placeholder="Leave empty to use Tanzanian price">
              <small class="form-text text-muted">Leave empty if same as Tanzanian price</small>
            </div>
          </div>
        </div>

        <!-- Age Group and Pricing Section (shown for swimming services) -->
        <div id="ageGroupSection" style="display: none;">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="age_group">Age Group <span class="text-danger">*</span></label>
                <select class="form-control" id="age_group" name="age_group" required onchange="window.togglePriceFields()">
                  <option value="both" {{ (isset($serviceCatalog) && ($serviceCatalog->age_group ?? 'both') === 'both') ? 'selected' : '' }}>Both (Adult & Child)</option>
                  <option value="adult" {{ (isset($serviceCatalog) && ($serviceCatalog->age_group ?? '') === 'adult') ? 'selected' : '' }}>Adult Only</option>
                  <option value="child" {{ (isset($serviceCatalog) && ($serviceCatalog->age_group ?? '') === 'child') ? 'selected' : '' }}>Child Only</option>
                </select>
                <small class="form-text text-muted">Who can use this service</small>
              </div>
            </div>
          </div>
        </div>

        <!-- Adult and Child Price Fields (shown for swimming services) -->
        <div id="pricingFieldsSection" style="display: none;">
          <div class="row">
            <div class="col-md-4" id="adult_priceGroup">
              <div class="form-group">
                <label for="price_tanzanian">Adult Price (TZS) <span class="text-danger" id="adult_price_required">*</span></label>
                <input class="form-control" type="number" id="price_tanzanian" name="price_tanzanian" 
                       value="{{ $serviceCatalog->price_tanzanian ?? '0' }}" 
                       step="0.01" min="0" required>
                <small class="form-text text-muted" id="price_tanzanian_hint">Price for adults</small>
              </div>
            </div>
            <div class="col-md-4" id="child_priceGroup" style="display: none;">
              <div class="form-group">
                <label for="child_price_tanzanian">Child Price (TZS) <span class="text-danger" id="child_price_required">*</span></label>
                <input class="form-control" type="number" id="child_price_tanzanian" name="child_price_tanzanian" 
                       value="{{ $serviceCatalog->child_price_tanzanian ?? '' }}" 
                       step="0.01" min="0" placeholder="Enter child price">
                <small class="form-text text-muted">Price for children</small>
              </div>
            </div>
          </div>
        </div>

        <!-- Default Price Field (shown when age group section is hidden) -->
        <div class="row" id="defaultPriceGroup">
          <div class="col-md-4" id="priceTanzanianGroup">
            <div class="form-group">
              <label for="price_tanzanian_default">Price for Tanzanian Guests (TZS) <span class="text-danger" id="price_tanzanian_required">*</span></label>
              <input class="form-control" type="number" id="price_tanzanian_default" name="price_tanzanian_default" 
                     value="{{ $serviceCatalog->price_tanzanian ?? '0' }}" 
                     step="0.01" min="0" required>
              <small class="form-text text-muted" id="price_tanzanian_hint_default">Default price in TZS. For ceremony packages, use 0 (prices set at registration)</small>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label for="payment_required_upfront">Payment Required Upfront <span class="text-danger">*</span></label>
              <select class="form-control" id="payment_required_upfront" name="payment_required_upfront" required>
                <option value="1" {{ (isset($serviceCatalog) && $serviceCatalog->payment_required_upfront) ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ (isset($serviceCatalog) && !$serviceCatalog->payment_required_upfront) ? 'selected' : '' }}>No</option>
              </select>
              <small class="form-text text-muted">If Yes, payment must be made when registering. If No, payment can be processed later.</small>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="requires_items">Requires Items Entry <span class="text-danger">*</span></label>
              <select class="form-control" id="requires_items" name="requires_items" required>
                <option value="0" {{ (isset($serviceCatalog) && !$serviceCatalog->requires_items) ? 'selected' : '' }}>No</option>
                <option value="1" {{ (isset($serviceCatalog) && $serviceCatalog->requires_items) ? 'selected' : '' }}>Yes</option>
              </select>
              <small class="form-text text-muted">If Yes, staff can enter specific items/orders when registering the service</small>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="display_order">Display Order</label>
              <input class="form-control" type="number" id="display_order" name="display_order" 
                     value="{{ $serviceCatalog->display_order ?? '0' }}" 
                     min="0" placeholder="0">
              <small class="form-text text-muted">Lower numbers appear first in dropdowns</small>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="is_active">Status <span class="text-danger">*</span></label>
              <select class="form-control" id="is_active" name="is_active" required>
                <option value="1" {{ (!isset($serviceCatalog) || $serviceCatalog->is_active) ? 'selected' : '' }}>Active</option>
                <option value="0" {{ (isset($serviceCatalog) && !$serviceCatalog->is_active) ? 'selected' : '' }}>Inactive</option>
              </select>
              <small class="form-text text-muted">Inactive services won't appear in registration forms</small>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label for="notes">Internal Notes (Optional)</label>
              <textarea class="form-control" id="notes" name="notes" rows="2" 
                        placeholder="Internal notes for staff...">{{ $serviceCatalog->notes ?? '' }}</textarea>
            </div>
          </div>
        </div>

        <!-- Package Items Section (shown for ceremony/package services) -->
        <div id="packageItemsSection" style="display: none;">
          <h4 class="mb-3 mt-4"><i class="fa fa-gift"></i> Package Items</h4>
          <div class="alert alert-info">
            <i class="fa fa-info-circle"></i> <strong>Package Items:</strong> Select the predefined items included in this ceremony/package service. Staff will enter prices for each item when registering guests.
          </div>
          
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <div class="form-check form-check-inline">
                  <input class="form-check-input package-item-checkbox" type="checkbox" id="package_item_food" name="package_items[]" value="food">
                  <label class="form-check-label" for="package_item_food">Food</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input package-item-checkbox" type="checkbox" id="package_item_swimming" name="package_items[]" value="swimming">
                  <label class="form-check-label" for="package_item_swimming">Swimming</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input package-item-checkbox" type="checkbox" id="package_item_drinks" name="package_items[]" value="drinks">
                  <label class="form-check-label" for="package_item_drinks">Drinks</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input package-item-checkbox" type="checkbox" id="package_item_photos" name="package_items[]" value="photos">
                  <label class="form-check-label" for="package_item_photos">Photos</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input package-item-checkbox" type="checkbox" id="package_item_decoration" name="package_items[]" value="decoration">
                  <label class="form-check-label" for="package_item_decoration">Decoration</label>
                </div>
              </div>
              <small class="form-text text-muted">Select the items included in this package. Prices are set at guest registration time, not here.</small>
            </div>
          </div>
        </div>

        <div id="formAlert"></div>

        <div class="form-group mt-4">
          <button type="submit" class="btn btn-primary">
            <i class="fa fa-save"></i> {{ isset($serviceCatalog) ? 'Update' : 'Create' }} Service
          </button>
          <a href="{{ route('admin.service-catalog.index') }}" class="btn btn-secondary">
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
  const form = document.getElementById('serviceCatalogForm');
  const serviceKeyInput = document.getElementById('service_key');
  const packageItemsSection = document.getElementById('packageItemsSection');
  
  // Show/hide package items section based on service key
  function togglePackageItemsSection() {
    if (!serviceKeyInput) {
      console.error('Package Items: service_key input not found!');
      return;
    }
    if (!packageItemsSection) {
      console.error('Package Items: packageItemsSection div not found!');
      return;
    }
    
    const serviceKey = (serviceKeyInput.value || '').toLowerCase().trim();
    // Check for ceremony, birthday, or package (including common typos)
    const isPackageService = serviceKey.includes('ceremony') || 
                             serviceKey.includes('ceremory') || // Common typo: ceremory
                             serviceKey.includes('birthday') || 
                             serviceKey.includes('package');
    
    console.log('Package Items: Checking', {
      serviceKey: serviceKey, 
      isPackageService: isPackageService,
      currentDisplay: packageItemsSection.style.display,
      sectionExists: !!packageItemsSection
    });
    
    if (isPackageService) {
      packageItemsSection.style.display = 'block';
      packageItemsSection.style.visibility = 'visible';
      console.log('Package Items: Section SHOWN');
      
      // Auto-set price to 0 and update hint for package services
      const priceTanzanianInput = document.getElementById('price_tanzanian');
      if (priceTanzanianInput && (!priceTanzanianInput.value || priceTanzanianInput.value === '0')) {
        priceTanzanianInput.value = '0';
        priceTanzanianInput.readOnly = false; // Keep editable but suggest 0
      }
      const priceTanzanianHint = document.getElementById('price_tanzanian_hint');
      if (priceTanzanianHint) {
        priceTanzanianHint.textContent = 'For ceremony packages, use 0 (prices are set at guest registration time)';
        priceTanzanianHint.className = 'form-text text-muted text-info';
      }
    } else {
      packageItemsSection.style.display = 'none';
      packageItemsSection.style.visibility = 'hidden';
      console.log('Package Items: Section HIDDEN');
      // Uncheck all package items if not a package service
      document.querySelectorAll('.package-item-checkbox').forEach(cb => cb.checked = false);
      
      // Reset price hint for non-package services
      const priceTanzanianHint = document.getElementById('price_tanzanian_hint');
      if (priceTanzanianHint) {
        priceTanzanianHint.textContent = 'Default price in TZS';
        priceTanzanianHint.className = 'form-text text-muted';
      }
    }
    
    // Also update price fields visibility and age group section
    updatePriceFieldsVisibility();
    toggleAgeGroupSection();
  }
  
  // Toggle age group section based on service key (show for swimming)
  function toggleAgeGroupSection() {
    const ageGroupSection = document.getElementById('ageGroupSection');
    const pricingFieldsSection = document.getElementById('pricingFieldsSection');
    const defaultPriceGroup = document.getElementById('defaultPriceGroup');
    
    if (!ageGroupSection || !pricingFieldsSection || !defaultPriceGroup) return;
    
    const serviceKey = (serviceKeyInput.value || '').toLowerCase().trim();
    const isSwimmingService = serviceKey === 'swimming' || serviceKey.includes('swimming');
    
    if (isSwimmingService) {
      ageGroupSection.style.display = 'block';
      pricingFieldsSection.style.display = 'block';
      defaultPriceGroup.style.display = 'none';
    } else {
      ageGroupSection.style.display = 'none';
      pricingFieldsSection.style.display = 'none';
      defaultPriceGroup.style.display = 'block';
    }
    
    // Update price fields visibility based on age group
    if (isSwimmingService) {
      window.togglePriceFields();
    }
  }
  
  // Toggle adult and child price fields based on age group (make globally accessible)
  window.togglePriceFields = function() {
    const ageGroupSelect = document.getElementById('age_group');
    if (!ageGroupSelect) return;
    
    const ageGroup = ageGroupSelect.value;
    const adultPriceGroup = document.getElementById('adult_priceGroup');
    const childPriceGroup = document.getElementById('child_priceGroup');
    const adultPriceInput = document.getElementById('price_tanzanian');
    const childPriceInput = document.getElementById('child_price_tanzanian');
    const adultPriceRequired = document.getElementById('adult_price_required');
    const childPriceRequired = document.getElementById('child_price_required');
    
    if (!adultPriceGroup || !childPriceGroup || !adultPriceInput || !childPriceInput) return;
    
    // Show/hide adult price field
    if (ageGroup === 'both' || ageGroup === 'adult') {
      adultPriceGroup.style.display = 'block';
      if (adultPriceRequired) {
        adultPriceRequired.textContent = '*';
      }
      adultPriceInput.required = true;
    } else {
      adultPriceGroup.style.display = 'none';
      adultPriceInput.value = '';
      adultPriceInput.required = false;
    }
    
    // Show/hide child price field
    if (ageGroup === 'both' || ageGroup === 'child') {
      childPriceGroup.style.display = 'block';
      if (childPriceRequired) {
        childPriceRequired.textContent = '*';
      }
      childPriceInput.required = true;
    } else {
      childPriceGroup.style.display = 'none';
      childPriceInput.value = '';
      childPriceInput.required = false;
    }
  }
  
  // Keep old function name for backwards compatibility
  window.toggleChildPriceField = window.togglePriceFields;
  
  // Update price fields visibility based on pricing type and service type
  function updatePriceFieldsVisibility() {
    const pricingType = document.getElementById('pricing_type').value;
    const serviceKey = (serviceKeyInput.value || '').toLowerCase().trim();
    const isPackageService = serviceKey.includes('ceremony') || 
                             serviceKey.includes('ceremory') || 
                             serviceKey.includes('birthday') || 
                             serviceKey.includes('package');
    
    // For package services with custom pricing, price can be 0
    // For other services, price is required
    const priceTanzanianInput = document.getElementById('price_tanzanian');
    const priceTanzanianRequired = document.getElementById('price_tanzanian_required');
    
    if (isPackageService) {
      // Allow 0 for package services
      if (priceTanzanianRequired) {
        priceTanzanianRequired.textContent = '*';
      }
      if (priceTanzanianInput) {
        priceTanzanianInput.min = '0';
        priceTanzanianInput.required = true; // Still required but can be 0
        if (!priceTanzanianInput.value || priceTanzanianInput.value === '') {
          priceTanzanianInput.value = '0';
        }
      }
    }
  }
  
  // Initialize on page load
  console.log('Package Items: Script loaded', {
    serviceKeyInput: !!serviceKeyInput,
    packageItemsSection: !!packageItemsSection,
    serviceKeyValue: serviceKeyInput ? serviceKeyInput.value : 'N/A'
  });
  
  // Auto-generate service key from service name (on blur if empty)
  const serviceNameInput = document.getElementById('service_name');
  if (serviceNameInput) {
    serviceNameInput.addEventListener('blur', function() {
      if (!serviceKeyInput.value) {
        const serviceName = this.value.toLowerCase()
          .replace(/[^a-z0-9\s-]/g, '')
          .replace(/\s+/g, '_')
          .replace(/_+/g, '_')
          .trim();
        serviceKeyInput.value = serviceName;
        togglePackageItemsSection();
        toggleAgeGroupSection();
      }
    });
  }
  
  // Initialize on page load
  toggleAgeGroupSection();
  
  // Check existing package items if editing
  @if(isset($serviceCatalog) && $serviceCatalog->package_items)
    const existingPackageItems = @json($serviceCatalog->package_items);
    if (existingPackageItems && Array.isArray(existingPackageItems)) {
      existingPackageItems.forEach(item => {
        const checkbox = document.getElementById('package_item_' + item);
        if (checkbox) {
          checkbox.checked = true;
        }
      });
    }
  @endif
  
    // Watch for service key changes (real-time)
    if (serviceKeyInput) {
      // Use multiple events to catch all input changes
      serviceKeyInput.addEventListener('input', function() {
        console.log('Service Key input event:', this.value);
        togglePackageItemsSection();
        toggleAgeGroupSection();
      });
      serviceKeyInput.addEventListener('change', function() {
        console.log('Service Key change event:', this.value);
        togglePackageItemsSection();
        toggleAgeGroupSection();
      });
      serviceKeyInput.addEventListener('keyup', function() {
        console.log('Service Key keyup event:', this.value);
        togglePackageItemsSection();
        toggleAgeGroupSection();
      });
      serviceKeyInput.addEventListener('paste', function() {
        setTimeout(function() {
          console.log('Service Key paste event:', serviceKeyInput.value);
          togglePackageItemsSection();
          toggleAgeGroupSection();
        }, 10);
      });
      
      // Check on page load if service key already has a value
      setTimeout(function() {
        if (serviceKeyInput.value) {
          console.log('Service Key on page load:', serviceKeyInput.value);
          togglePackageItemsSection();
          toggleAgeGroupSection();
        }
      }, 100);
    }

  // Form submission
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!form.checkValidity()) {
      form.reportValidity();
      return;
    }

    // Collect package items
    const packageItems = [];
    document.querySelectorAll('.package-item-checkbox:checked').forEach(checkbox => {
      packageItems.push(checkbox.value);
    });
    
    const formData = new FormData(form);
    
    // Handle price_tanzanian field - use the visible one
    const adultPriceInput = document.getElementById('price_tanzanian');
    const defaultPriceInput = document.getElementById('price_tanzanian_default');
    
    // Remove both price fields from form data to avoid conflicts
    formData.delete('price_tanzanian');
    
    // Add the correct price_tanzanian value based on which field is visible
    if (adultPriceInput && adultPriceInput.offsetParent !== null) {
      // Adult price field is visible (for swimming services with age_group)
      formData.append('price_tanzanian', adultPriceInput.value || '0');
    } else if (defaultPriceInput && defaultPriceInput.offsetParent !== null) {
      // Default price field is visible (for other services)
      formData.append('price_tanzanian', defaultPriceInput.value || '0');
    } else {
      // Fallback: use adult price if available, otherwise default
      const priceValue = adultPriceInput ? (adultPriceInput.value || '0') : (defaultPriceInput ? (defaultPriceInput.value || '0') : '0');
      formData.append('price_tanzanian', priceValue);
    }
    
    // Add package_items as JSON if it's a package service
    if (packageItems.length > 0) {
      formData.append('package_items', JSON.stringify(packageItems));
    }
    
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';

    const alertDiv = document.getElementById('formAlert');
    alertDiv.innerHTML = '';

    @if(isset($serviceCatalog))
      const url = '{{ route("admin.service-catalog.update", $serviceCatalog) }}';
      const method = 'POST';
      formData.append('_method', 'PUT');
    @else
      const url = '{{ route("admin.service-catalog.store") }}';
      const method = 'POST';
    @endif

    fetch(url, {
      method: method,
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
          window.location.href = '{{ route("admin.service-catalog.index") }}';
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

