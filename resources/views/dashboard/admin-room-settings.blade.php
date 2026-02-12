@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-bed"></i> Room Settings</h1>
    <p>Configure default room settings, amenities, and operational rules</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Settings</a></li>
    <li class="breadcrumb-item"><a href="#">Room Settings</a></li>
  </ul>
</div>

<form id="roomSettingsForm" method="POST" action="{{ route('admin.settings.rooms.update') }}">
  @csrf

  @php
    $defaultAmenities = \App\Models\HotelSetting::getValue('default_amenities');
    $selectedAmenities = $defaultAmenities ? json_decode($defaultAmenities, true) : [];
  @endphp

  <!-- Default Check-in/Check-out Times -->
  <div class="row mb-3">
    <div class="col-md-12">
      <div class="tile">
        <h3 class="tile-title"><i class="fa fa-clock-o"></i> Default Check-in/Check-out Times</h3>
        <div class="tile-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="default_room_checkin_time">Default Check-in Time</label>
                <input type="time" class="form-control" id="default_room_checkin_time" name="default_room_checkin_time" 
                       value="{{ \App\Models\HotelSetting::getValue('default_room_checkin_time', '14:00') }}">
                <small class="form-text text-muted">Applied to new rooms by default</small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="default_room_checkout_time">Default Check-out Time</label>
                <input type="time" class="form-control" id="default_room_checkout_time" name="default_room_checkout_time" 
                       value="{{ \App\Models\HotelSetting::getValue('default_room_checkout_time', '12:00') }}">
                <small class="form-text text-muted">Applied to new rooms by default</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Default Amenities -->
  <div class="row mb-3">
    <div class="col-md-12">
      <div class="tile">
        <h3 class="tile-title"><i class="fa fa-star"></i> Default Amenities</h3>
        <div class="tile-body">
          <p class="text-muted">Select amenities that will be automatically added to new rooms.</p>
          <div class="row">
            @foreach($allAmenities as $amenity)
            <div class="col-md-3 mb-2">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="default_amenities[]" 
                       value="{{ $amenity }}" id="amenity_{{ $loop->index }}"
                       {{ in_array($amenity, $selectedAmenities) ? 'checked' : '' }}>
                <label class="form-check-label" for="amenity_{{ $loop->index }}">
                  {{ $amenity }}
                </label>
              </div>
            </div>
            @endforeach
            @if($allAmenities->isEmpty())
            <div class="col-md-12">
              <div class="alert alert-info">
                <i class="fa fa-info-circle"></i> No amenities found. Create rooms with amenities first.
              </div>
            </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Room Status Rules -->
  <div class="row mb-3">
    <div class="col-md-12">
      <div class="tile">
        <h3 class="tile-title"><i class="fa fa-cog"></i> Room Status Rules</h3>
        <div class="tile-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="auto_update_status_after_checkout" 
                         name="auto_update_status_after_checkout" value="1"
                         {{ \App\Models\HotelSetting::getValue('auto_update_status_after_checkout') == '1' ? 'checked' : '' }}>
                  <label class="form-check-label" for="auto_update_status_after_checkout">
                    Auto-update room status after checkout
                  </label>
                  <small class="form-text text-muted d-block">Automatically set room to "To Be Cleaned" after guest checkout</small>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="auto_maintenance_after_days">Auto-maintenance After (Days)</label>
                <input type="number" class="form-control" id="auto_maintenance_after_days" 
                       name="auto_maintenance_after_days" 
                       value="{{ \App\Models\HotelSetting::getValue('auto_maintenance_after_days', '0') }}" 
                       min="0">
                <small class="form-text text-muted">Set to 0 to disable auto-maintenance</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Cleaning Settings -->
  <div class="row mb-3">
    <div class="col-md-12">
      <div class="tile">
        <h3 class="tile-title"><i class="fa fa-broom"></i> Cleaning Settings</h3>
        <div class="tile-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="default_cleaning_duration_hours">Default Cleaning Duration (Hours)</label>
                <input type="number" class="form-control" id="default_cleaning_duration_hours" 
                       name="default_cleaning_duration_hours" 
                       value="{{ \App\Models\HotelSetting::getValue('default_cleaning_duration_hours', '2') }}" 
                       step="0.5" min="0" max="24">
                <small class="form-text text-muted">Estimated time to clean a room</small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <div class="form-check mt-4">
                  <input class="form-check-input" type="checkbox" id="auto_cleaning_status" 
                         name="auto_cleaning_status" value="1"
                         {{ \App\Models\HotelSetting::getValue('auto_cleaning_status') == '1' ? 'checked' : '' }}>
                  <label class="form-check-label" for="auto_cleaning_status">
                    Auto-update to "Available" after cleaning duration
                  </label>
                  <small class="form-text text-muted d-block">Automatically change status from "To Be Cleaned" to "Available"</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Maintenance Settings -->
  <div class="row mb-3">
    <div class="col-md-12">
      <div class="tile">
        <h3 class="tile-title"><i class="fa fa-wrench"></i> Maintenance Settings</h3>
        <div class="tile-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="maintenance_duration_hours">Maintenance Duration (Hours)</label>
                <input type="number" class="form-control" id="maintenance_duration_hours" 
                       name="maintenance_duration_hours" 
                       value="{{ \App\Models\HotelSetting::getValue('maintenance_duration_hours', '24') }}" 
                       min="0" max="168">
                <small class="form-text text-muted">Estimated time for maintenance (0-168 hours)</small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <div class="form-check mt-4">
                  <input class="form-check-input" type="checkbox" id="auto_maintenance_trigger" 
                         name="auto_maintenance_trigger" value="1"
                         {{ \App\Models\HotelSetting::getValue('auto_maintenance_trigger') == '1' ? 'checked' : '' }}>
                  <label class="form-check-label" for="auto_maintenance_trigger">
                    Enable auto-maintenance trigger
                  </label>
                  <small class="form-text text-muted d-block">Automatically trigger maintenance based on rules</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Default Pricing by Room Type -->
  <div class="row mb-3">
    <div class="col-md-12">
      <div class="tile">
        <h3 class="tile-title"><i class="fa fa-dollar"></i> Default Pricing by Room Type</h3>
        <div class="tile-body">
          <p class="text-muted">Set default prices that will be suggested when creating new rooms.</p>
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="default_price_single">Single Room (USD per night)</label>
                <input type="number" class="form-control" id="default_price_single" 
                       name="default_price_single" 
                       value="{{ \App\Models\HotelSetting::getValue('default_price_single') }}" 
                       step="0.01" min="0">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="default_price_double">Double Room (USD per night)</label>
                <input type="number" class="form-control" id="default_price_double" 
                       name="default_price_double" 
                       value="{{ \App\Models\HotelSetting::getValue('default_price_double') }}" 
                       step="0.01" min="0">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="default_price_twins">Standard Twin Room (USD per night)</label>
                <input type="number" class="form-control" id="default_price_twins" 
                       name="default_price_twins" 
                       value="{{ \App\Models\HotelSetting::getValue('default_price_twins') }}" 
                       step="0.01" min="0">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Submit Button -->
  <div class="row">
    <div class="col-md-12">
      <div class="tile">
        <div class="tile-body">
          <button type="submit" class="btn btn-primary btn-lg">
            <i class="fa fa-save"></i> Save All Settings
          </button>
          <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-lg">
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

document.getElementById('roomSettingsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';
    
    fetch(form.action, {
        method: 'POST',
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
            return { success: true, message: "Settings updated successfully." };
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
                text: data.message || "Room settings updated successfully.",
                type: "success",
                confirmButtonColor: "#28a745",
                timer: 2000,
                showConfirmButton: false
            }, function() {
                window.location.reload();
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
            text: "An error occurred while saving settings. Please try again.",
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

