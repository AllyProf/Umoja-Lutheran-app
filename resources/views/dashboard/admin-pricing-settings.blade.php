@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-money"></i> Pricing Settings</h1>
    <p>Configure pricing rules, taxes, discounts, and seasonal rates</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Settings</a></li>
    <li class="breadcrumb-item"><a href="#">Pricing</a></li>
  </ul>
</div>

<form id="pricingSettingsForm" method="POST" action="{{ route('admin.settings.pricing.update') }}">
  @csrf

  <!-- Exchange Rate -->
  <div class="row mb-3">
    <div class="col-md-12">
      <div class="tile">
        <h3 class="tile-title"><i class="fa fa-exchange"></i> Exchange Rate</h3>
        <div class="tile-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="exchange_rate_usd_to_tzs">Exchange Rate (1 USD = X TZS)</label>
                <input type="number" class="form-control" id="exchange_rate_usd_to_tzs" 
                       name="exchange_rate_usd_to_tzs" 
                       value="{{ \App\Models\HotelSetting::getValue('exchange_rate_usd_to_tzs', '2455') }}" 
                       step="0.01" min="0">
                <small class="form-text text-muted">Fallback rate used only if API fails. System prioritizes live API rates from Frankfurter.app</small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <div class="form-check mt-4">
                  <input class="form-check-input" type="checkbox" id="auto_update_exchange_rate" 
                         name="auto_update_exchange_rate" value="1"
                         {{ \App\Models\HotelSetting::getValue('auto_update_exchange_rate') == '1' ? 'checked' : '' }}>
                  <label class="form-check-label" for="auto_update_exchange_rate">
                    Enable auto-update exchange rate
                  </label>
                  <small class="form-text text-muted d-block">Automatically fetch latest exchange rates (requires API integration)</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Tax Rates -->
  <div class="row mb-3">
    <div class="col-md-12">
      <div class="tile">
        <h3 class="tile-title"><i class="fa fa-percent"></i> Tax Rates</h3>
        <div class="tile-body">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="vat_percentage">VAT (%)</label>
                <input type="number" class="form-control" id="vat_percentage" name="vat_percentage" 
                       value="{{ \App\Models\HotelSetting::getValue('vat_percentage', '0') }}" 
                       step="0.01" min="0" max="100">
                <small class="form-text text-muted">Value Added Tax percentage</small>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="service_tax_percentage">Service Tax (%)</label>
                <input type="number" class="form-control" id="service_tax_percentage" name="service_tax_percentage" 
                       value="{{ \App\Models\HotelSetting::getValue('service_tax_percentage', '0') }}" 
                       step="0.01" min="0" max="100">
                <small class="form-text text-muted">Service tax percentage</small>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="city_tax_percentage">City Tax (%)</label>
                <input type="number" class="form-control" id="city_tax_percentage" name="city_tax_percentage" 
                       value="{{ \App\Models\HotelSetting::getValue('city_tax_percentage', '0') }}" 
                       step="0.01" min="0" max="100">
                <small class="form-text text-muted">City tax percentage</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Service Charges -->
  <div class="row mb-3">
    <div class="col-md-12">
      <div class="tile">
        <h3 class="tile-title"><i class="fa fa-calculator"></i> Service Charges</h3>
        <div class="tile-body">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="service_charge_type">Service Charge Type</label>
                <select class="form-control" id="service_charge_type" name="service_charge_type">
                  <option value="percentage" {{ \App\Models\HotelSetting::getValue('service_charge_type', 'percentage') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                  <option value="fixed" {{ \App\Models\HotelSetting::getValue('service_charge_type') == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="service_charge_percentage">Service Charge (%)</label>
                <input type="number" class="form-control" id="service_charge_percentage" 
                       name="service_charge_percentage" 
                       value="{{ \App\Models\HotelSetting::getValue('service_charge_percentage', '0') }}" 
                       step="0.01" min="0" max="100">
                <small class="form-text text-muted">Used when type is "Percentage"</small>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="service_charge_fixed">Service Charge (Fixed TZS)</label>
                <input type="number" class="form-control" id="service_charge_fixed" 
                       name="service_charge_fixed" 
                       value="{{ \App\Models\HotelSetting::getValue('service_charge_fixed', '0') }}" 
                       step="0.01" min="0">
                <small class="form-text text-muted">Used when type is "Fixed Amount"</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Season Management -->
  <div class="row mb-3">
    <div class="col-md-12">
      <div class="tile">
        <h3 class="tile-title"><i class="fa fa-calendar"></i> Season Management</h3>
        <div class="tile-body">
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label for="peak_season_start_month">Peak Season Start Month</label>
                <select class="form-control" id="peak_season_start_month" name="peak_season_start_month">
                  <option value="">Select Month</option>
                  @for($i = 1; $i <= 12; $i++)
                  <option value="{{ $i }}" {{ \App\Models\HotelSetting::getValue('peak_season_start_month') == $i ? 'selected' : '' }}>
                    {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                  </option>
                  @endfor
                </select>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label for="peak_season_start_day">Peak Season Start Day</label>
                <input type="number" class="form-control" id="peak_season_start_day" 
                       name="peak_season_start_day" 
                       value="{{ \App\Models\HotelSetting::getValue('peak_season_start_day') }}" 
                       min="1" max="31">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label for="peak_season_end_month">Peak Season End Month</label>
                <select class="form-control" id="peak_season_end_month" name="peak_season_end_month">
                  <option value="">Select Month</option>
                  @for($i = 1; $i <= 12; $i++)
                  <option value="{{ $i }}" {{ \App\Models\HotelSetting::getValue('peak_season_end_month') == $i ? 'selected' : '' }}>
                    {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                  </option>
                  @endfor
                </select>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label for="peak_season_end_day">Peak Season End Day</label>
                <input type="number" class="form-control" id="peak_season_end_day" 
                       name="peak_season_end_day" 
                       value="{{ \App\Models\HotelSetting::getValue('peak_season_end_day') }}" 
                       min="1" max="31">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="peak_season_multiplier">Peak Season Price Multiplier</label>
                <input type="number" class="form-control" id="peak_season_multiplier" 
                       name="peak_season_multiplier" 
                       value="{{ \App\Models\HotelSetting::getValue('peak_season_multiplier', '1.2') }}" 
                       step="0.1" min="1">
                <small class="form-text text-muted">Multiply base TZS price by this factor during peak season (e.g., 1.2 = 20% increase)</small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="off_season_multiplier">Off-Season Price Multiplier</label>
                <input type="number" class="form-control" id="off_season_multiplier" 
                       name="off_season_multiplier" 
                       value="{{ \App\Models\HotelSetting::getValue('off_season_multiplier', '0.9') }}" 
                       step="0.1" min="0" max="1">
                <small class="form-text text-muted">Multiply base price by this factor during off-season (e.g., 0.9 = 10% discount)</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Discount Rules -->
  <div class="row mb-3">
    <div class="col-md-12">
      <div class="tile">
        <h3 class="tile-title"><i class="fa fa-tag"></i> Discount Rules</h3>
        <div class="tile-body">
          <div class="row">
            <div class="col-md-6">
              <h5>Early Bird Discount</h5>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="early_bird_discount_percentage">Discount (%)</label>
                    <input type="number" class="form-control" id="early_bird_discount_percentage" 
                           name="early_bird_discount_percentage" 
                           value="{{ \App\Models\HotelSetting::getValue('early_bird_discount_percentage', '0') }}" 
                           step="0.01" min="0" max="100">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="early_bird_days_advance">Days in Advance</label>
                    <input type="number" class="form-control" id="early_bird_days_advance" 
                           name="early_bird_days_advance" 
                           value="{{ \App\Models\HotelSetting::getValue('early_bird_days_advance', '30') }}" 
                           min="1">
                    <small class="form-text text-muted">Minimum days before check-in</small>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <h5>Long Stay Discount</h5>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="long_stay_discount_percentage">Discount (%)</label>
                    <input type="number" class="form-control" id="long_stay_discount_percentage" 
                           name="long_stay_discount_percentage" 
                           value="{{ \App\Models\HotelSetting::getValue('long_stay_discount_percentage', '0') }}" 
                           step="0.01" min="0" max="100">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="long_stay_min_nights">Minimum Nights</label>
                    <input type="number" class="form-control" id="long_stay_min_nights" 
                           name="long_stay_min_nights" 
                           value="{{ \App\Models\HotelSetting::getValue('long_stay_min_nights', '7') }}" 
                           min="1">
                    <small class="form-text text-muted">Minimum nights to qualify</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row mt-3">
            <div class="col-md-6">
              <h5>Last Minute Discount</h5>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="last_minute_discount_percentage">Discount (%)</label>
                    <input type="number" class="form-control" id="last_minute_discount_percentage" 
                           name="last_minute_discount_percentage" 
                           value="{{ \App\Models\HotelSetting::getValue('last_minute_discount_percentage', '0') }}" 
                           step="0.01" min="0" max="100">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="last_minute_max_days">Maximum Days Before</label>
                    <input type="number" class="form-control" id="last_minute_max_days" 
                           name="last_minute_max_days" 
                           value="{{ \App\Models\HotelSetting::getValue('last_minute_max_days', '3') }}" 
                           min="1">
                    <small class="form-text text-muted">Maximum days before check-in</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Dynamic Pricing -->
  <div class="row mb-3">
    <div class="col-md-12">
      <div class="tile">
        <h3 class="tile-title"><i class="fa fa-line-chart"></i> Dynamic Pricing</h3>
        <div class="tile-body">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="weekend_multiplier">Weekend Price Multiplier</label>
                <input type="number" class="form-control" id="weekend_multiplier" 
                       name="weekend_multiplier" 
                       value="{{ \App\Models\HotelSetting::getValue('weekend_multiplier', '1.1') }}" 
                       step="0.1" min="1">
                <small class="form-text text-muted">Multiply base price by this factor on weekends (e.g., 1.1 = 10% increase)</small>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="holiday_multiplier">Holiday Price Multiplier</label>
                <input type="number" class="form-control" id="holiday_multiplier" 
                       name="holiday_multiplier" 
                       value="{{ \App\Models\HotelSetting::getValue('holiday_multiplier', '1.3') }}" 
                       step="0.1" min="1">
                <small class="form-text text-muted">Multiply base price by this factor on holidays (e.g., 1.3 = 30% increase)</small>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <div class="form-check mt-4">
                  <input class="form-check-input" type="checkbox" id="enable_dynamic_pricing" 
                         name="enable_dynamic_pricing" value="1"
                         {{ \App\Models\HotelSetting::getValue('enable_dynamic_pricing') == '1' ? 'checked' : '' }}>
                  <label class="form-check-label" for="enable_dynamic_pricing">
                    Enable Dynamic Pricing
                  </label>
                  <small class="form-text text-muted d-block">Apply weekend and holiday multipliers automatically</small>
                </div>
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

document.getElementById('pricingSettingsForm').addEventListener('submit', function(e) {
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
                text: data.message || "Pricing settings updated successfully.",
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

