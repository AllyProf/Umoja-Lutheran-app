@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-building"></i> Hotel Settings</h1>
    <p>Configure hotel information and operational settings</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Settings</a></li>
    <li class="breadcrumb-item"><a href="#">Hotel Settings</a></li>
  </ul>
</div>

<form id="hotelSettingsForm" method="POST" action="{{ route('admin.settings.hotel.update') }}">
  @csrf

  <!-- Basic Information -->
  <div class="row mb-3">
    <div class="col-md-12">
      <div class="tile">
        <h3 class="tile-title"><i class="fa fa-info-circle"></i> Basic Information</h3>
        <div class="tile-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="hotel_name">Hotel Name</label>
                <input type="text" class="form-control" id="hotel_name" name="hotel_name" 
                       value="{{ \App\Models\HotelSetting::getValue('hotel_name') }}" 
                       placeholder="Enter hotel name">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="hotel_phone">Phone Number</label>
                <input type="text" class="form-control" id="hotel_phone" name="hotel_phone" 
                       value="{{ \App\Models\HotelSetting::getValue('hotel_phone') }}" 
                       placeholder="+255 XXX XXX XXX">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="hotel_email">Email Address</label>
                <input type="email" class="form-control" id="hotel_email" name="hotel_email" 
                       value="{{ \App\Models\HotelSetting::getValue('hotel_email') }}" 
                       placeholder="info@hotel.com">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="hotel_website">Website</label>
                <input type="url" class="form-control" id="hotel_website" name="hotel_website" 
                       value="{{ \App\Models\HotelSetting::getValue('hotel_website') }}" 
                       placeholder="https://www.hotel.com">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="hotel_address">Address</label>
                <textarea class="form-control" id="hotel_address" name="hotel_address" rows="2" 
                          placeholder="Enter full address">{{ \App\Models\HotelSetting::getValue('hotel_address') }}</textarea>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Check-in/Check-out Times -->
  <div class="row mb-3">
    <div class="col-md-12">
      <div class="tile">
        <h3 class="tile-title"><i class="fa fa-clock-o"></i> Check-in/Check-out Times</h3>
        <div class="tile-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="default_checkin_time">Default Check-in Time</label>
                <input type="time" class="form-control" id="default_checkin_time" name="default_checkin_time" 
                       value="{{ \App\Models\HotelSetting::getValue('default_checkin_time', '14:00') }}">
                <small class="form-text text-muted">Default time for guest check-in</small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="default_checkout_time">Default Check-out Time</label>
                <input type="time" class="form-control" id="default_checkout_time" name="default_checkout_time" 
                       value="{{ \App\Models\HotelSetting::getValue('default_checkout_time', '12:00') }}">
                <small class="form-text text-muted">Default time for guest check-out</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Currency & Exchange -->
  <div class="row mb-3">
    <div class="col-md-12">
      <div class="tile">
        <h3 class="tile-title"><i class="fa fa-dollar"></i> Currency & Exchange</h3>
        <div class="tile-body">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="base_currency">Base Currency</label>
                <select class="form-control" id="base_currency" name="base_currency">
                  <option value="USD" {{ \App\Models\HotelSetting::getValue('base_currency', 'USD') == 'USD' ? 'selected' : '' }}>USD (US Dollar)</option>
                  <option value="TZS" {{ \App\Models\HotelSetting::getValue('base_currency') == 'TZS' ? 'selected' : '' }}>TZS (Tanzanian Shilling)</option>
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="exchange_rate_usd_to_tzs" style="font-weight: bold; color: #e07632;">
                  <i class="fa fa-pencil"></i> Manual Exchange Rate Override (USD to TZS)
                </label>
                <input type="number" class="form-control" id="exchange_rate_usd_to_tzs" name="exchange_rate_usd_to_tzs" 
                       value="{{ \App\Models\HotelSetting::getValue('exchange_rate_usd_to_tzs', '2540') }}" 
                       step="0.01" min="0" style="border: 2px solid #e07632;">
                <small class="form-text text-muted">
                  <strong>Priority:</strong> This manual rate will be used across the system. 
                  Leave empty or set to 0 to use the live automated API rates instead.
                </small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Contact Information -->
  <div class="row mb-3">
    <div class="col-md-12">
      <div class="tile">
        <h3 class="tile-title"><i class="fa fa-phone"></i> Contact Information</h3>
        <div class="tile-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="support_email">Support Email</label>
                <input type="email" class="form-control" id="support_email" name="support_email" 
                       value="{{ \App\Models\HotelSetting::getValue('support_email') }}" 
                       placeholder="support@hotel.com">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="support_phone">Support Phone</label>
                <input type="text" class="form-control" id="support_phone" name="support_phone" 
                       value="{{ \App\Models\HotelSetting::getValue('support_phone') }}" 
                       placeholder="+255 XXX XXX XXX">
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

document.getElementById('hotelSettingsForm').addEventListener('submit', function(e) {
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
            // If redirected, it means success (Laravel redirects on success for non-AJAX)
            return { success: true, message: "Settings updated successfully." };
        }
    })
    .then(data => {
        if (data && data.errors) {
            // Handle validation errors
            const errorMessages = Object.values(data.errors).flat().join('<br>');
            swal({
                title: "Validation Error!",
                html: true,
                text: errorMessages,
                type: "error",
                confirmButtonColor: "#d33"
            });
        } else if (data && data.success) {
            // Success - show SweetAlert and reload page
            swal({
                title: "Success!",
                text: data.message || "Hotel settings updated successfully.",
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

