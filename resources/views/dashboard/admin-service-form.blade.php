@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-cogs"></i> {{ isset($service) ? 'Edit Service' : 'Add New Service' }}</h1>
    <p>{{ isset($service) ? 'Update service information' : 'Create a new hotel service' }}</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.services.index') }}">Services</a></li>
    <li class="breadcrumb-item"><a href="#">{{ isset($service) ? 'Edit' : 'Add' }}</a></li>
  </ul>
</div>

<form id="serviceForm" method="POST" action="{{ isset($service) ? route('admin.services.update', $service) : route('admin.services.store') }}">
  @csrf
  @if(isset($service))
    @method('PUT')
  @endif

  <div class="row">
    <div class="col-md-8">
      <div class="tile">
        <h3 class="tile-title">Service Information</h3>
        <div class="tile-body">
          <div class="form-group">
            <label for="name">Service Name *</label>
            <input type="text" class="form-control" id="name" name="name" 
                   value="{{ $service->name ?? '' }}" required placeholder="e.g., Airport Pickup">
          </div>

          <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3" 
                      placeholder="Describe the service...">{{ $service->description ?? '' }}</textarea>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="category">Category *</label>
                <select class="form-control" id="category" name="category" required>
                  <option value="">Select Category</option>
                  <option value="swimming" {{ (isset($service) && $service->category == 'swimming') ? 'selected' : '' }}>Swimming / Pool</option>
                  <option value="recreation" {{ (isset($service) && $service->category == 'recreation') ? 'selected' : '' }}>Recreation</option>
                  <option value="laundry" {{ (isset($service) && $service->category == 'laundry') ? 'selected' : '' }}>Laundry</option>
                  <option value="spa" {{ (isset($service) && $service->category == 'spa') ? 'selected' : '' }}>Spa / Wellness</option>
                  <option value="room_service" {{ (isset($service) && $service->category == 'room_service') ? 'selected' : '' }}>Room Service</option>
                  <option value="photography" {{ (isset($service) && $service->category == 'photography') ? 'selected' : '' }}>Photography</option>
                  <option value="general" {{ (isset($service) && $service->category == 'general') ? 'selected' : '' }}>General</option>
                </select>
                <small class="form-text text-muted">Note: Food and Transportation services are managed separately in Day Services.</small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="unit">Unit *</label>
                <select class="form-control" id="unit" name="unit" required>
                  <option value="">Select Unit</option>
                  <option value="per_session" {{ (isset($service) && $service->unit == 'per_session') ? 'selected' : '' }}>Per Session</option>
                  <option value="per_hour" {{ (isset($service) && $service->unit == 'per_hour') ? 'selected' : '' }}>Per Hour</option>
                  <option value="per_day" {{ (isset($service) && $service->unit == 'per_day') ? 'selected' : '' }}>Per Day</option>
                  <option value="per_person" {{ (isset($service) && $service->unit == 'per_person') ? 'selected' : '' }}>Per Person</option>
                  <option value="per_item" {{ (isset($service) && $service->unit == 'per_item') ? 'selected' : '' }}>Per Item</option>
                  <option value="per_photo" {{ (isset($service) && $service->unit == 'per_photo') ? 'selected' : '' }}>Per Photo</option>
                  <option value="per_package" {{ (isset($service) && $service->unit == 'per_package') ? 'selected' : '' }}>Per Package</option>
                  <option value="per_kg" {{ (isset($service) && $service->unit == 'per_kg') ? 'selected' : '' }}>Per Kilogram</option>
                </select>
                <small class="form-text text-muted">Examples: Swimming = Per Session, Photography = Per Photo, Laundry = Per Kilogram</small>
              </div>
            </div>
          </div>

          <div class="form-group">
            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" id="is_free_for_internal" name="is_free_for_internal" value="1"
                     {{ (isset($service) && $service->is_free_for_internal) ? 'checked' : '' }}
                     onchange="togglePriceField()">
              <label class="form-check-label" for="is_free_for_internal">
                <strong>Free for Internal Guests</strong>
              </label>
              <small class="form-text text-muted d-block">Check this if the service is free for internal guests (e.g., regular swimming). Uncheck for paid services (e.g., swimming with floating bucket).</small>
            </div>
          </div>

          <!-- Age Group Selection (especially for swimming) -->
          <div class="form-group">
            <label for="age_group">Age Group *</label>
            <select class="form-control" id="age_group" name="age_group" required onchange="toggleChildPriceField()">
              <option value="both" {{ (isset($service) && $service->age_group == 'both') ? 'selected' : '' }}>Both (Adult & Child)</option>
              <option value="adult" {{ (isset($service) && $service->age_group == 'adult') ? 'selected' : '' }}>Adult Only</option>
              <option value="child" {{ (isset($service) && $service->age_group == 'child') ? 'selected' : '' }}>Child Only</option>
            </select>
            <small class="form-text text-muted">Select if this service is for adults, children, or both (e.g., Swimming can be for adults or children with different pricing)</small>
          </div>

          <!-- Adult Price -->
          <div class="form-group" id="priceFieldGroup">
            <label for="price_tsh">Adult Price (TZS) <span id="priceRequired">*</span></label>
            <input type="number" class="form-control" id="price_tsh" name="price_tsh" 
                   value="{{ $service->price_tsh ?? '' }}" step="0.01" min="0" placeholder="0.00">
            <small class="form-text text-muted">Price in Tanzanian Shillings for adults (required for paid services)</small>
          </div>

          <!-- Child Price (shown when age_group is 'both' or 'child') -->
          <div class="form-group" id="childPriceFieldGroup" style="display: none;">
            <label for="child_price_tsh">Child Price (TZS) <span id="childPriceRequired"></span></label>
            <input type="number" class="form-control" id="child_price_tsh" name="child_price_tsh" 
                   value="{{ $service->child_price_tsh ?? '' }}" step="0.01" min="0" placeholder="0.00">
            <small class="form-text text-muted">Price in Tanzanian Shillings for children (leave empty if same as adult price or free)</small>
          </div>

          <div class="form-group">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                     {{ (isset($service) && $service->is_active) || !isset($service) ? 'checked' : '' }}>
              <label class="form-check-label" for="is_active">
                Active Service
              </label>
              <small class="form-text text-muted d-block">Only active services are visible to customers</small>
            </div>
          </div>
        </div>
      </div>

      <!-- Required Fields Section -->
      <div class="tile">
        <h3 class="tile-title">Additional Information Fields (Optional)</h3>
        <div class="tile-body">
          <div class="alert alert-info">
            <h6><i class="fa fa-info-circle"></i> What are Additional Information Fields?</h6>
            <p class="mb-2">These are <strong>extra questions</strong> that customers will see and must answer when requesting this service.</p>
            
            <p class="mb-2"><strong>When to use:</strong></p>
            <ul class="mb-2">
              <li><strong>Airport Pickup</strong> → Need: Flight Number, Arrival Date, Arrival Time, Number of Passengers</li>
              <li><strong>Laundry Service</strong> → Usually no extra fields needed</li>
              <li><strong>Room Service (Food)</strong> → Might need: Special dietary requirements, Delivery time</li>
              <li><strong>Spa Appointment</strong> → Might need: Preferred time, Treatment type</li>
            </ul>
            
            <p class="mb-0"><strong>Example for Airport Pickup:</strong></p>
            <ul class="mb-0">
              <li>Field Name: <code>flight_number</code> | Label: <strong>Flight Number</strong> | Type: Text | Required: Yes</li>
              <li>Field Name: <code>arrival_date</code> | Label: <strong>Arrival Date</strong> | Type: Date | Required: Yes</li>
              <li>Field Name: <code>arrival_time</code> | Label: <strong>Arrival Time</strong> | Type: Time | Required: Yes</li>
            </ul>
            <p class="mt-2 mb-0"><strong>💡 Tip:</strong> If your service doesn't need any special information, you can skip this entire section!</p>
          </div>
          <div id="requiredFieldsContainer">
            @if(isset($service) && $service->required_fields && count($service->required_fields) > 0)
              @foreach($service->required_fields as $index => $field)
              <div class="required-field-item mb-3 p-3 border rounded" data-index="{{ $index }}">
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Field Name *</label>
                      <input type="text" class="form-control" name="required_fields[{{ $index }}][name]" 
                             value="{{ $field['name'] ?? '' }}" placeholder="e.g., flight_number" required>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Field Label *</label>
                      <input type="text" class="form-control" name="required_fields[{{ $index }}][label]" 
                             value="{{ $field['label'] ?? '' }}" placeholder="e.g., Flight Number" required>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Field Type *</label>
                      <select class="form-control" name="required_fields[{{ $index }}][type]" required>
                        <option value="text" {{ ($field['type'] ?? 'text') == 'text' ? 'selected' : '' }}>Text</option>
                        <option value="number" {{ ($field['type'] ?? '') == 'number' ? 'selected' : '' }}>Number</option>
                        <option value="date" {{ ($field['type'] ?? '') == 'date' ? 'selected' : '' }}>Date</option>
                        <option value="time" {{ ($field['type'] ?? '') == 'time' ? 'selected' : '' }}>Time</option>
                        <option value="email" {{ ($field['type'] ?? '') == 'email' ? 'selected' : '' }}>Email</option>
                        <option value="textarea" {{ ($field['type'] ?? '') == 'textarea' ? 'selected' : '' }}>Textarea</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Placeholder</label>
                      <input type="text" class="form-control" name="required_fields[{{ $index }}][placeholder]" 
                             value="{{ $field['placeholder'] ?? '' }}" placeholder="e.g., EK 723">
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>Min Value</label>
                      <input type="number" class="form-control" name="required_fields[{{ $index }}][min]" 
                             value="{{ $field['min'] ?? '' }}" placeholder="Min">
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>Max Value</label>
                      <input type="number" class="form-control" name="required_fields[{{ $index }}][max]" 
                             value="{{ $field['max'] ?? '' }}" placeholder="Max">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Default Value</label>
                      <input type="text" class="form-control" name="required_fields[{{ $index }}][default]" 
                             value="{{ $field['default'] ?? '' }}" placeholder="Default value">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="required_fields[{{ $index }}][required]" value="1"
                               {{ ($field['required'] ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label">Required Field</label>
                      </div>
                    </div>
                  </div>
                </div>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeRequiredField(this)">
                  <i class="fa fa-trash"></i> Remove Field
                </button>
              </div>
              @endforeach
            @endif
          </div>
          <button type="button" class="btn btn-secondary" onclick="addRequiredField()">
            <i class="fa fa-plus"></i> Add Information Field
          </button>
          <p class="text-muted mt-2" style="font-size: 12px;">
            <i class="fa fa-lightbulb-o"></i> <strong>Tip:</strong> Only add fields if you need specific information from customers. 
            For simple services like "Laundry" or "Room Cleaning", you usually don't need any additional fields.
          </p>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="tile">
        <div class="tile-body">
          <button type="submit" class="btn btn-primary btn-lg btn-block">
            <i class="fa fa-save"></i> {{ isset($service) ? 'Update Service' : 'Create Service' }}
          </button>
          <a href="{{ route('admin.services.index') }}" class="btn btn-secondary btn-lg btn-block">
            <i class="fa fa-times"></i> Cancel
          </a>
        </div>
      </div>
    </div>
  </div>
</form>

@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<script>
let fieldIndex = {{ isset($service) && $service->required_fields ? count($service->required_fields) : 0 }};

function addRequiredField() {
  const container = document.getElementById('requiredFieldsContainer');
  const fieldHtml = `
    <div class="required-field-item mb-3 p-3 border rounded" data-index="${fieldIndex}">
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label>Field Name *</label>
            <input type="text" class="form-control" name="required_fields[${fieldIndex}][name]" 
                   placeholder="e.g., flight_number" required>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label>Field Label *</label>
            <input type="text" class="form-control" name="required_fields[${fieldIndex}][label]" 
                   placeholder="e.g., Flight Number" required>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label>Field Type *</label>
            <select class="form-control" name="required_fields[${fieldIndex}][type]" required>
              <option value="text">Text</option>
              <option value="number">Number</option>
              <option value="date">Date</option>
              <option value="time">Time</option>
              <option value="email">Email</option>
              <option value="textarea">Textarea</option>
            </select>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label>Placeholder</label>
            <input type="text" class="form-control" name="required_fields[${fieldIndex}][placeholder]" 
                   placeholder="e.g., EK 723">
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label>Min Value</label>
            <input type="number" class="form-control" name="required_fields[${fieldIndex}][min]" 
                   placeholder="Min">
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label>Max Value</label>
            <input type="number" class="form-control" name="required_fields[${fieldIndex}][max]" 
                   placeholder="Max">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label>Default Value</label>
            <input type="text" class="form-control" name="required_fields[${fieldIndex}][default]" 
                   placeholder="Default value">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <div class="form-check mt-4">
              <input class="form-check-input" type="checkbox" name="required_fields[${fieldIndex}][required]" value="1">
              <label class="form-check-label">Required Field</label>
            </div>
          </div>
        </div>
      </div>
      <button type="button" class="btn btn-sm btn-danger" onclick="removeRequiredField(this)">
        <i class="fa fa-trash"></i> Remove Field
      </button>
    </div>
  `;
  container.insertAdjacentHTML('beforeend', fieldHtml);
  fieldIndex++;
}

function removeRequiredField(button) {
  button.closest('.required-field-item').remove();
}

function togglePriceField() {
  const isFreeCheckbox = document.getElementById('is_free_for_internal');
  const priceField = document.getElementById('price_tsh');
  const childPriceField = document.getElementById('child_price_tsh');
  const priceFieldGroup = document.getElementById('priceFieldGroup');
  const childPriceFieldGroup = document.getElementById('childPriceFieldGroup');
  const priceRequired = document.getElementById('priceRequired');
  const childPriceRequired = document.getElementById('childPriceRequired');
  
  if (isFreeCheckbox.checked) {
    // Service is free for internal guests
    priceField.removeAttribute('required');
    priceField.value = '0';
    if (childPriceField) {
      childPriceField.removeAttribute('required');
      childPriceField.value = '0';
    }
    priceRequired.style.display = 'none';
    childPriceRequired.style.display = 'none';
    priceFieldGroup.style.opacity = '0.6';
    if (childPriceFieldGroup) {
      childPriceFieldGroup.style.opacity = '0.6';
    }
  } else {
    // Service requires payment
    const ageGroup = document.getElementById('age_group').value;
    if (ageGroup === 'adult' || ageGroup === 'both') {
      priceField.setAttribute('required', 'required');
      priceRequired.style.display = 'inline';
    } else {
      priceField.removeAttribute('required');
      priceRequired.style.display = 'none';
    }
    
    if (priceField.value === '0' || priceField.value === '') {
      priceField.value = '';
    }
    priceFieldGroup.style.opacity = '1';
    
    // Child price is optional even for paid services
    if (childPriceFieldGroup) {
      childPriceFieldGroup.style.opacity = '1';
      childPriceRequired.style.display = 'none';
    }
  }
  
  // Also update child price field visibility
  toggleChildPriceField();
}

function toggleChildPriceField() {
  const ageGroup = document.getElementById('age_group').value;
  const childPriceFieldGroup = document.getElementById('childPriceFieldGroup');
  const childPriceField = document.getElementById('child_price_tsh');
  const childPriceRequired = document.getElementById('childPriceRequired');
  const isFreeCheckbox = document.getElementById('is_free_for_internal');
  
  if (ageGroup === 'both' || ageGroup === 'child') {
    childPriceFieldGroup.style.display = 'block';
    // Child price is optional (can be same as adult or different)
    if (childPriceField) {
      childPriceField.removeAttribute('required');
      childPriceRequired.style.display = 'none';
    }
  } else {
    childPriceFieldGroup.style.display = 'none';
    if (childPriceField) {
      childPriceField.value = '';
      childPriceField.removeAttribute('required');
    }
  }
  
  // Update adult price requirement based on age group
  const priceField = document.getElementById('price_tsh');
  const priceRequired = document.getElementById('priceRequired');
  
  if (!isFreeCheckbox.checked) {
    if (ageGroup === 'adult' || ageGroup === 'both') {
      priceField.setAttribute('required', 'required');
      priceRequired.style.display = 'inline';
    } else if (ageGroup === 'child') {
      priceField.removeAttribute('required');
      priceRequired.style.display = 'none';
      // If child only, child price is required
      if (childPriceField) {
        childPriceField.setAttribute('required', 'required');
        childPriceRequired.style.display = 'inline';
        childPriceRequired.textContent = '*';
      }
    }
  }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
  togglePriceField();
  toggleChildPriceField();
});

@if(session('success'))
swal({
    title: "Success!",
    text: "{{ session('success') }}",
    type: "success",
    confirmButtonColor: "#28a745",
    timer: 3000,
    showConfirmButton: false
});
@endif

document.getElementById('serviceForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';
    
    fetch(form.action, {
        method: form.method,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(async response => {
        const contentType = response.headers.get("content-type");
        if (contentType && contentType.includes("application/json")) {
            return response.json();
        } else {
            return { success: true, message: "Service saved successfully." };
        }
    })
    .then(data => {
        if (data && data.errors) {
            const errorMessages = Object.values(data.errors).flat().join('<br>');
            swal({
                title: "Validation Error!",
                html: true,
                text: errorMessages,
                type: "error",
                confirmButtonColor: "#d33"
            });
        } else if (data && data.success) {
            swal({
                title: "Success!",
                text: data.message || "Service saved successfully.",
                type: "success",
                confirmButtonColor: "#28a745",
                timer: 2000,
                showConfirmButton: false
            }, function() {
                window.location.href = '{{ route("admin.services.index") }}';
            });
        } else {
            swal({
                title: "Error!",
                text: data.message || "An error occurred. Please try again.",
                type: "error",
                confirmButtonColor: "#d33"
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        swal({
            title: "Error!",
            text: "An error occurred while saving. Please try again.",
            type: "error",
            confirmButtonColor: "#d33"
        });
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});
</script>
@endsection

