@extends('dashboard.layouts.app')

@section('content')
<style>
  /* Fix horizontal scroll and responsiveness issues */
  .tile {
    overflow-x: hidden;
    word-wrap: break-word;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 20px;
  }
  
  /* Wrap buttons with long text */
  .btn {
    white-space: normal !important;
    word-wrap: break-word;
    height: auto !important;
  }
  
  /* Fix flex titles on mobile */
  .tile-title-w-btn {
    display: flex !important;
    flex-wrap: wrap !important;
    gap: 10px;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
  }

  /* Status Card System (Mimicking Active Booking Alert Style) */
  .status-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    width: 100%;
  }

  .status-card {
    background: #fcfcfc;
    border: 1px solid #f0f0f0;
    border-left: 4px solid #940000;
    border-radius: 6px;
    padding: 12px;
    display: flex;
    align-items: flex-start;
    flex: 1 1 calc(50% - 6px); /* 2 columns */
    min-width: 140px;
    transition: all 0.2s ease;
  }

  .status-card:hover {
    background: #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
  }

  .status-card.full-width {
    flex: 1 1 100%;
  }

  .status-card i {
    font-size: 1.4rem;
    color: #940000;
    margin-right: 12px;
    margin-top: 3px;
    width: 24px;
    text-align: center;
  }

  .status-card-content {
    flex-grow: 1;
    min-width: 0;
  }

  .status-card-label {
    display: block;
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: #888;
    margin-bottom: 4px;
  }

  .status-card-value {
    display: block;
    font-weight: 700;
    color: #333;
    font-size: 14px;
    word-wrap: break-word;
  }

  /* Different color themes for cards */
  .status-card.info { border-left-color: #17a2b8; }
  .status-card.info i { color: #17a2b8; }
  .status-card.success { border-left-color: #28a745; }
  .status-card.success i { color: #28a745; }
  .status-card.danger { border-left-color: #dc3545; }
  .status-card.danger i { color: #dc3545; }
  .status-card.primary { border-left-color: #007bff; }
  .status-card.primary i { color: #007bff; }

  /* Mobile Grid System */
  @media (max-width: 767px) {
    .app-title h1 { font-size: 20px !important; }
    .welcome-title { font-size: 18px !important; }
    .tile { padding: 15px !important; }
    
    .status-card {
      padding: 10px;
    }
    
    .status-card-value {
      font-size: 13px;
    }

    /* Input group fix inside status cards */
    .status-card .input-group {
      width: 100% !important;
      margin-top: 5px;
    }
    
    /* Quick Actions row */
    .quick-actions-row {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin: 0;
    }
    
    .quick-actions-row > div {
      flex: 1 1 calc(50% - 5px) !important;
      width: calc(50% - 5px) !important;
      max-width: none !important;
      padding: 0 !important;
      margin-bottom: 0 !important;
    }
  }
</style>
<div class="app-title">
  <div>
    <h1 id="time-greeting" style="font-size: 28px; font-weight: 600; margin: 0;">
      @php
        // Use application timezone for consistent greeting
        $timezone = config('app.timezone', 'Africa/Dar_es_Salaam');
        $now = \Carbon\Carbon::now($timezone);
        $hour = (int)$now->format('H');
        
        if ($hour >= 5 && $hour < 12) {
          $greeting = 'Good Morning';
          $emoji = '🌅';
        } elseif ($hour >= 12 && $hour < 17) {
          $greeting = 'Good Afternoon';
          $emoji = '☀️';
        } elseif ($hour >= 17 && $hour < 21) {
          $greeting = 'Good Evening';
          $emoji = '🌆';
        } else {
          $greeting = 'Good Night';
          $emoji = '🌙';
        }
        
        // Extract first name only
        $firstName = explode(' ', trim($userName))[0];
      @endphp
      <span id="greeting-emoji">{{ $emoji }}</span> <span id="greeting-text">{{ $greeting }}</span>, <span id="greeting-name">{{ $firstName }}</span>
    </h1>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
  </ul>
</div>

@php
  $checkedInBookings = $activeBookings->where('check_in_status', 'checked_in');
  $hasActiveStay = $checkedInBookings->count() > 0;
@endphp

{{-- Welcome Message - Always show for all guests --}}
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile" style="padding: 0;">
      <div class="tile-body" style="padding: 0;">
        <!-- Welcome Message (Top Bar) -->
        <div style="background: linear-gradient(135deg, #940000 0%, #d66a2a 100%); color: white; padding: 20px; border-radius: 4px 4px 0 0;">
          <div class="d-flex align-items-center justify-content-between flex-wrap">
            <div class="flex-grow-1" style="min-width: 0;">
              <h2 style="color: #18e7e5; margin: 0 0 10px 0; font-weight: 700; font-size: 24px;" class="welcome-title">
                Welcome to Umoj Lutheran Hostel, {{ $firstName }}!
              </h2>
              <p style="color: rgba(255,255,255,0.9); margin: 0; font-size: 15px; font-style: italic; min-height: 24px;">
                @if($hasActiveStay)
                  <span id="typing-text" style="border-right: 2px solid rgba(255,255,255,0.8); padding-right: 3px; animation: blink 1s infinite;"></span>
                @elseif($hasCheckedOutBookings ?? false)
                  <span>Thank you for staying with us! We hope you enjoyed your visit. Please share your feedback with us.</span>
                @else
                  <span>Welcome! We're excited to have you with us. Your booking details will appear here once confirmed.</span>
                @endif
              </p>
            </div>
            <div class="text-right d-none d-md-block">
              <i class="fa fa-hotel fa-3x" style="color: rgba(255,255,255,0.25);"></i>
            </div>
          </div>
        </div>
        
        @if($hasActiveStay)
        <!-- Weather & Check-out Content -->
        <div class="row" style="margin: 0;">
          <!-- Weather Widget (Left Side) -->
          @if($weather && is_array($weather) && !isset($weather['error']))
          <div class="col-md-6 col-12" style="padding: 0;">
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 20px; height: 100%; min-height: 120px;">
              <div class="d-flex align-items-center flex-wrap">
                <i class="fa {{ $weatherService->getWeatherIconClass($weather['icon']) }} fa-2x mr-3 weather-icon" style="color: #ffd700;"></i>
                <div class="flex-grow-1 weather-content">
                  <h4 style="color: white; margin: 0; font-size: 28px; font-weight: bold;" class="weather-temp">{{ $weather['temperature'] }}°C</h4>
                  <p style="color: rgba(255,255,255,0.9); margin: 3px 0; font-size: 14px;" class="weather-desc">{{ $weather['description'] }}</p>
                  <p style="color: rgba(255,255,255,0.8); margin: 0; font-size: 12px;" class="weather-feels">Feels like {{ $weather['feels_like'] }}°C</p>
                  <p style="color: rgba(255,255,255,0.9); margin: 5px 0 0 0; font-size: 12px;" class="weather-location">
                    <i class="fa fa-map-marker"></i> {{ $weather['city'] }}{{ isset($weather['country']) ? ', ' . $weather['country'] : '' }}
                  </p>
                </div>
                <div class="weather-btn-wrapper">
                  <a href="{{ route('customer.local-info') }}" class="btn btn-sm btn-light" style="color: #667eea; font-size: 11px; padding: 5px 10px;">
                    <i class="fa fa-info-circle"></i> Details
                  </a>
                </div>
              </div>
            </div>
          </div>
          @endif
          
          <!-- Upcoming Check-out Alerts (Right Side) -->
          <div class="{{ ($weather && is_array($weather) && !isset($weather['error'])) ? 'col-md-6 col-12' : 'col-md-12 col-12' }}" style="padding: 0;">
            @if(count($upcomingCheckOuts ?? []) > 0)
              @foreach($upcomingCheckOuts as $alert)
              @php
                $booking = $alert['booking'];
                $isCorporate = $booking->is_corporate_booking ?? false;
                $company = $booking->company ?? null;
                $paymentResponsibility = $booking->payment_responsibility ?? 'company';
                $hasPendingExtension = $booking->extension_status === 'pending';
                $hasApprovedExtension = $booking->extension_status === 'approved';
                $hasRejectedExtension = $booking->extension_status === 'rejected';
                // Check if it's a decrease request
                $isDecreaseRequest = false;
                if ($booking->extension_requested_to && $booking->check_out) {
                  $requestedDate = \Carbon\Carbon::parse($booking->extension_requested_to);
                  $currentCheckOut = \Carbon\Carbon::parse($booking->check_out);
                  $isDecreaseRequest = $requestedDate->lt($currentCheckOut);
                }
                // Also check extension_type field if it exists
                if (isset($booking->extension_type) && $booking->extension_type === 'decrease') {
                  $isDecreaseRequest = true;
                }
              @endphp
              <div class="alert alert-warning alert-dismissible fade show mb-0" role="alert" style="border-left: 4px solid {{ $isCorporate ? '#940000' : '#ffc107' }}; border-radius: 0; margin: 0; padding: 15px 20px;">
                <div class="d-flex align-items-start flex-wrap">
                  <i class="fa {{ $isCorporate ? 'fa-building' : 'fa-calendar-times-o' }} fa-lg mr-2" style="color: {{ $isCorporate ? '#940000' : '#ffc107' }}; flex-shrink: 0;"></i>
                  <div class="flex-grow-1" style="min-width: 0;">
                    <h6 class="alert-heading mb-1" style="font-size: 14px; margin: 0;">
                      <strong>{{ $isCorporate ? 'Company Booking - Upcoming Check-out!' : 'Upcoming Check-out!' }}</strong>
                      {{-- @if($isCorporate)
                        <span class="badge badge-warning ml-2" style="background-color: #940000; color: white; font-size: 10px;">Company</span>
                      @endif --}}
                      @if($hasPendingExtension)
                        @if($isDecreaseRequest)
                          <span class="badge badge-warning ml-2">Decrease Pending</span>
                        @else
                          <span class="badge badge-info ml-2">Extension Pending</span>
                        @endif
                      @elseif($hasApprovedExtension)
                        @if($isDecreaseRequest)
                          <span class="badge badge-success ml-2">Decreased to {{ $booking->check_out->format('M d, Y') }}</span>
                        @else
                          <span class="badge badge-success ml-2">Extended to {{ $booking->check_out->format('M d, Y') }}</span>
                        @endif
                      @elseif($hasRejectedExtension)
                        @if($isDecreaseRequest)
                          <span class="badge badge-danger ml-2">Decrease Rejected</span>
                        @else
                          <span class="badge badge-danger ml-2">Extension Rejected</span>
                        @endif
                      @endif
                    </h6>
                    <p class="mb-0" style="font-size: 13px;">
                      Booking <strong>{{ $booking->booking_reference }}</strong>
                      @if($isCorporate && $company)
                        <br><small style="font-size: 11px; color: #666;">
                          <i class="fa fa-building"></i> Company: <strong>{{ $company->name }}</strong>
                        </small>
                      @endif
                      in <strong><span class="countdown-checkout-{{ $booking->id }}" data-date="{{ $alert['date']->format('Y-m-d H:i:s') }}">{{ $alert['days_until'] }} day(s)</span></strong>
                    </p>
                    <p class="mb-0" style="font-size: 12px; color: #666;">
                      Check-out: <strong>{{ $alert['date']->format('M d, Y') }}</strong> at <strong>4:00 PM</strong>
                      {{-- @if($isCorporate)
                        <br><small style="font-size: 11px; margin-top: 5px; display: inline-block;">
                          <span class="badge badge-success" style="background-color: #28a745; font-size: 10px;">
                            <i class="fa fa-bed"></i> Room: Company Paid
                          </span>
                          <span class="badge {{ $paymentResponsibility == 'self' ? 'badge-warning' : 'badge-info' }}" style="font-size: 10px;">
                            <i class="fa fa-cutlery"></i> Services: {{ $paymentResponsibility == 'self' ? 'Self-Paid' : 'Company Paid' }}
                          </span>
                        </small>
                      @endif --}}
                      @if($hasApprovedExtension && $booking->extension_requested_to)
                        @if($isDecreaseRequest)
                          <br><small class="text-success">✓ Decreased to: <strong>{{ $booking->extension_requested_to->format('M d, Y') }}</strong></small>
                        @else
                          <br><small class="text-success">✓ Extended to: <strong>{{ $booking->extension_requested_to->format('M d, Y') }}</strong></small>
                        @endif
                      @elseif($hasPendingExtension && $booking->extension_requested_to)
                        @if($isDecreaseRequest)
                          <br><small class="text-warning">⏳ Decrease requested to: <strong>{{ $booking->extension_requested_to->format('M d, Y') }}</strong></small>
                        @else
                          <br><small class="text-info">⏳ Extension requested to: <strong>{{ $booking->extension_requested_to->format('M d, Y') }}</strong></small>
                        @endif
                      @endif
                    </p>
                    <small class="text-muted" style="font-size: 11px;">Room: {{ $booking->room->room_type ?? 'N/A' }} ({{ $booking->room->room_number ?? 'N/A' }})</small>
                  </div>
                  <div class="w-100 mt-2 mt-md-0" style="flex: 0 0 100%;">
                    @if(!$hasPendingExtension && $booking->check_in_status === 'checked_in')
                    <button onclick="openExtensionModal({{ $booking->id }}, '{{ $booking->check_out->format('Y-m-d') }}')" class="btn btn-sm btn-info mr-1 mb-1" style="font-size: 11px; padding: 4px 8px;" title="Request Stay Extension">
                      <i class="fa fa-calendar-plus-o"></i> Request Extension
                    </button>
                    <button onclick="openDecreaseModal({{ $booking->id }}, '{{ $booking->check_in->format('Y-m-d') }}', '{{ $booking->check_out->format('Y-m-d') }}')" class="btn btn-sm btn-warning mr-1 mb-1" style="font-size: 11px; padding: 4px 8px;" title="Request Stay Decrease">
                      <i class="fa fa-calendar-minus-o"></i> Request Decrease
                    </button>
                    @endif
                    <button onclick="showDirectionsMap({{ $booking->id }})" class="btn btn-sm btn-success mr-1 mb-1" style="font-size: 11px; padding: 4px 8px;" title="Get Directions">
                      <i class="fa fa-map-marker"></i> Get Directions
                    </button>
                    <a href="{{ route('customer.bookings.checkout-bill', $booking) }}" class="btn btn-sm btn-warning mb-1" style="font-size: 11px; padding: 4px 8px;">
                      <i class="fa fa-file-text"></i> Bill
                    </a>
                  </div>
                </div>
              </div>
              @endforeach
            @elseif($weather && is_array($weather) && !isset($weather['error']))
              <!-- Empty state when weather is shown but no check-out alerts -->
              <div style="padding: 15px 20px; text-align: center; color: #666; background: #f8f9fa; height: 100%; min-height: 120px; display: flex; align-items: center; justify-content: center;">
                <div>
                  <i class="fa fa-calendar-check-o fa-2x mb-2" style="color: #ccc;"></i>
                  <p style="margin: 0; font-size: 13px;">No upcoming check-outs</p>
                </div>
              </div>
            @endif
          </div>
        </div>
        @elseif($hasCheckedOutBookings ?? false)
        <!-- Thank You Message for Checked Out Guests -->
        <div class="row" style="margin: 0;">
          <div class="col-md-12" style="padding: 0;">
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px 20px; text-align: center;">
              <h3 style="color: white; margin: 0 0 10px 0; font-weight: 600;">Thank You for Staying with Us!</h3>
              <p style="color: rgba(255,255,255,0.9); margin: 0; font-size: 16px;">
                We hope you had a wonderful experience at Umoj Lutheran Hostel. Your feedback helps us improve our services.
              </p>
            </div>
          </div>
        </div>
        @else
        <!-- Welcome Message for New Guests (No Checked Out Bookings) -->
        <div class="row" style="margin: 0;">
          <div class="col-md-12" style="padding: 0;">
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px 20px; text-align: center;">
              <i class="fa fa-calendar-check-o fa-3x mb-3" style="color: #ffd700;"></i>
              <h3 style="color: white; margin: 0 0 10px 0; font-weight: 600;">Welcome to Umoj Lutheran Hostel!</h3>
              <p style="color: rgba(255,255,255,0.9); margin: 0; font-size: 16px;">
                We're looking forward to hosting you. Your booking information will be displayed here once it's confirmed.
              </p>
            </div>
          </div>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Upcoming Check-in Alerts -->
@if(count($upcomingCheckIns ?? []) > 0)
<div class="row mb-3">
  <div class="col-md-12">
    @foreach($upcomingCheckIns as $alert)
    @php
      $booking = $alert['booking'];
      // Show directions if not checked in (pending, null, or anything other than checked_in/checked_out)
      $hasNotCheckedIn = $booking->check_in_status !== 'checked_in' && $booking->check_in_status !== 'checked_out';
    @endphp
    @php
      $booking = $alert['booking'];
      $isCorporate = $booking->is_corporate_booking ?? false;
      $company = $booking->company ?? null;
      $paymentResponsibility = $booking->payment_responsibility ?? 'company';
    @endphp
    <div class="alert alert-info alert-dismissible fade show mb-2" role="alert" style="border-left: 4px solid {{ $isCorporate ? '#940000' : '#17a2b8' }}; padding: 12px 15px;">
      <div class="d-flex align-items-center flex-wrap">
        <i class="fa {{ $isCorporate ? 'fa-building' : 'fa-calendar-check-o' }} fa-lg mr-2" style="color: {{ $isCorporate ? '#940000' : '#17a2b8' }};"></i>
        <div class="flex-grow-1">
          <h6 class="alert-heading mb-1" style="font-size: 14px; margin: 0;">
            <strong>{{ $isCorporate ? 'Company Booking - Upcoming Check-in!' : 'Upcoming Check-in!' }}</strong>
            {{-- @if($isCorporate)
              <span class="badge badge-warning ml-2" style="background-color: #940000; color: white; font-size: 10px;">Company</span>
            @endif --}}
          </h6>
          <p class="mb-0" style="font-size: 13px;">
            Booking <strong>{{ $booking->booking_reference }}</strong>
            @if($isCorporate && $company)
              <br><small style="font-size: 11px; color: #666;">
                <i class="fa fa-building"></i> Company: <strong>{{ $company->name }}</strong>
              </small>
            @endif
            @if($alert['days_until'] == 0)
              <strong><span class="countdown-checkin-{{ $booking->id }}" data-date="{{ $alert['date']->format('Y-m-d H:i:s') }}">Check-in today!</span></strong>
            @else
              in <strong><span class="countdown-checkin-{{ $booking->id }}" data-date="{{ $alert['date']->format('Y-m-d H:i:s') }}">{{ $alert['days_until'] }} day(s)</span></strong>
              on <strong>{{ $alert['date']->format('M d, Y') }}</strong>
            @endif
          </p>
          <small class="text-muted" style="font-size: 11px;">Room: {{ $booking->room->room_type ?? 'N/A' }} ({{ $booking->room->room_number ?? 'N/A' }})</small>
          {{-- @if($isCorporate)
            <div class="mt-1" style="font-size: 11px;">
              <span class="badge badge-success" style="background-color: #28a745; font-size: 10px;">
                <i class="fa fa-bed"></i> Room Charges: Company Paid
              </span>
              <span class="badge {{ $paymentResponsibility == 'self' ? 'badge-warning' : 'badge-info' }}" style="font-size: 10px;">
                <i class="fa fa-cutlery"></i> Services: {{ $paymentResponsibility == 'self' ? 'Self-Paid' : 'Company Paid' }}
              </span>
            </div>
          @endif --}}
        </div>
        <div class="mt-2 mt-md-0 ml-2">
          <button onclick="showDirectionsMap({{ $booking->id }})" 
             class="btn btn-sm btn-success" 
             style="font-size: 12px; padding: 6px 12px; white-space: nowrap;">
            <i class="fa fa-map-marker"></i> Get Directions
          </button>
        </div>
      </div>
    </div>
    @endforeach
  </div>
</div>
@endif

<!-- Directions Map Widget - Only for guests who haven't checked in yet -->
@php
  // Show map widget for guests who haven't checked in yet
  // Include bookings within 7 days OR if check-in date has passed but guest hasn't checked in
  $hasPendingCheckIn = $activeBookings->filter(function($booking) {
      // Only show for bookings that haven't been checked in or checked out
      if ($booking->check_in_status === 'checked_in' || $booking->check_in_status === 'checked_out') {
          return false;
      }
      
      // Check if within the time window (7 days future or 2 days past)
      $checkInDate = \Carbon\Carbon::parse($booking->check_in);
      $today = \Carbon\Carbon::today();
      $daysUntilCheckIn = $today->diffInDays($checkInDate, false);
      
      return $daysUntilCheckIn <= 7 && $daysUntilCheckIn >= -2;
  })->count() > 0;
@endphp
@if($hasPendingCheckIn)
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn">
        <h3 class="tile-title"><i class="fa fa-map-marker"></i> Directions to Umoj Lutheran Hostel</h3>
        <div class="btn-group">
          <button onclick="showDirectionsMap()" class="btn btn-sm btn-primary">
            <i class="fa fa-expand"></i> Full Map
          </button>
        </div>
      </div>
      <div class="tile-body">
        <div class="row">
          <div class="col-md-8">
            <div id="dashboardMap" style="width: 100%; height: 250px; border-radius: 5px; overflow: hidden;"></div>
          </div>
          <div class="col-md-4">
            <div id="dashboardRouteInfo" style="padding: 15px;">
              <h6 style="color: #17a2b8; margin-bottom: 15px;"><i class="fa fa-route"></i> Route Information</h6>
              @if(empty(config('services.google.maps_api_key')))
                <div class="alert alert-danger p-2 mb-2" style="font-size: 11px;">
                  <i class="fa fa-exclamation-triangle"></i> <strong>Key Missing:</strong> Check <code>.env</code> and run <code>php artisan config:clear</code>
                </div>
              @endif
              <div id="dashboardLocationStatus" style="color: #6c757d; font-size: 13px; margin-bottom: 15px;">
                <i class="fa fa-spinner fa-spin"></i> Getting your location...
              </div>
              <div id="dashboardRouteDetails" style="display: none;">
                <div style="margin-bottom: 12px; padding: 10px; background: #f8f9fa; border-radius: 5px;">
                  <div style="margin-bottom: 8px;">
                    <strong><i class="fa fa-road"></i> Distance:</strong><br>
                    <span id="dashboardDistance" style="font-size: 16px; color: #333;">-</span>
                  </div>
                  <div>
                    <strong><i class="fa fa-clock-o"></i> Estimated Time:</strong><br>
                    <span id="dashboardDuration" style="font-size: 18px; color: #28a745; font-weight: bold;">-</span>
                  </div>
                </div>
                <div style="padding: 10px; background: #e7f3ff; border-left: 3px solid #17a2b8; border-radius: 5px;">
                  <small>
                    <strong><i class="fa fa-map-marker"></i> Umoj Lutheran Hostel</strong><br>
                    Sokoine Road, Moshi<br>
                    Kilimanjaro, Tanzania
                  </small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endif

<!-- Directions Map Modal -->
<div class="modal fade" id="directionsMapModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background: #17a2b8; color: white;">
        <h5 class="modal-title"><i class="fa fa-map-marker"></i> Directions to Umoj Lutheran Hostel</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="padding: 0;">
        <div id="directionsMap" style="width: 100%; height: 400px;"></div>
        <div style="padding: 15px; background: #f8f9fa; border-top: 1px solid #dee2e6;">
          <div class="row">
            <div class="col-md-12">
              <div id="locationStatus" style="color: #6c757d; font-size: 13px; margin-bottom: 15px;">
                <i class="fa fa-spinner fa-spin"></i> Getting your location...
              </div>
              
              <!-- Route Options -->
              <div id="routeOptions" style="display: none; margin-bottom: 15px;">
                <h6 style="color: #17a2b8; margin-bottom: 10px;"><i class="fa fa-route"></i> Route Options</h6>
                <div id="alternativeRoutes" class="list-group" style="max-height: 150px; overflow-y: auto;"></div>
              </div>
              
              <!-- Selected Route Info -->
              <div id="routeInfo" style="display: none; margin-bottom: 15px;">
                <h6 style="color: #17a2b8; margin-bottom: 10px;"><i class="fa fa-map-signs"></i> Selected Route</h6>
                <div class="row">
                  <div class="col-md-6">
                    <div id="distanceInfo" style="margin-bottom: 8px;">
                      <strong><i class="fa fa-road"></i> Distance:</strong> <span id="routeDistance">-</span>
                    </div>
                    <div id="durationInfo">
                      <strong><i class="fa fa-clock-o"></i> Estimated Time:</strong> <span id="routeDuration" style="color: #28a745; font-weight: bold;">-</span>
                    </div>
                  </div>
                  <div class="col-md-6 text-right">
                    <a href="https://www.google.com/maps/dir/?api=1&destination=Umoj Lutheran Hostel+Hotel,Sokoine+Road,Moshi,Kilimanjaro,Tanzania&key={{ config('services.google.maps_api_key') }}" 
                       target="_blank" 
                       class="btn btn-sm btn-primary">
                      <i class="fa fa-external-link"></i> Open in Google Maps
                    </a>
                  </div>
                </div>
              </div>
              
              <!-- Route Steps -->
              <div id="routeSteps" style="display: none;">
                <h6 style="color: #17a2b8; margin-bottom: 10px;">
                  <i class="fa fa-list"></i> Turn-by-Turn Directions
                  <button class="btn btn-sm btn-link p-0 ml-2" type="button" data-toggle="collapse" data-target="#stepsCollapse" aria-expanded="false" aria-controls="stepsCollapse" style="font-size: 12px;">
                    <i class="fa fa-chevron-down"></i> Show/Hide
                  </button>
                </h6>
                <div class="collapse" id="stepsCollapse">
                  <div id="stepsList" class="list-group" style="max-height: 200px; overflow-y: auto;"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Quick Actions: Request Service, Report Issue, My Feedback, My ID, My Receipt -->
@php
  $checkedInBookings = $activeBookings->where('check_in_status', 'checked_in');
  $hasActiveStay = $checkedInBookings->count() > 0;
  // Get the most recent booking (even if checked out) for ID card
  $mostRecentBooking = \App\Models\Booking::where('guest_email', $user->email)
    ->where('status', '!=', 'cancelled')
    ->orderBy('check_out', 'desc')
    ->first();
  // Get the most recent booking with receipt (paid/partial/confirmed status)
  $mostRecentBookingWithReceipt = \App\Models\Booking::where('guest_email', $user->email)
    ->where('status', '!=', 'cancelled')
    ->where(function($query) {
      $query->whereIn('payment_status', ['paid', 'partial'])
            ->orWhere('status', 'confirmed');
    })
    ->orderBy('check_out', 'desc')
    ->first();
@endphp
{{-- Always show Quick Actions section, especially for Feedback button --}}
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile" style="background: linear-gradient(135deg, #940000 0%, #d66a2a 100%); color: white;">
      <div class="tile-body">
        <div class="row quick-actions-row">
          @if($hasActiveStay)
          <div class="col-md-3 col-sm-6 col-6 mb-2">
            <a href="{{ route('customer.restaurant') }}" class="btn btn-light btn-block" style="min-height: 60px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none;">
              <i class="fa fa-cutlery fa-2x mb-1"></i><br>
              <strong>Restaurant Service</strong>
            </a>
          </div>
          <div class="col-md-3 col-sm-6 col-6 mb-2">
            <button onclick="openServiceRequestForFirstBooking()" class="btn btn-light btn-block" style="min-height: 60px;">
              <i class="fa fa-plus-circle fa-2x mb-1"></i><br>
              <strong>Other Services</strong>
            </button>
          </div>
          @endif
          @if($hasActiveStay)
          <div class="col-md-3 col-sm-6 col-6 mb-2">
            <a href="{{ route('exchange-rates') }}" class="btn btn-light btn-block" style="min-height: 60px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none;">
              <i class="fa fa-exchange fa-2x mb-1"></i><br>
              <strong>Exchange rate</strong>
            </a>
          </div>
          @endif
          {{-- Feedback button always visible --}}
          <div class="col-md-3 col-sm-6 col-6 mb-2">
            <a href="{{ route('customer.feedback') }}" class="btn btn-light btn-block" style="min-height: 60px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none;">
              <i class="fa fa-star fa-2x mb-1"></i><br>
              <strong>My Feedback</strong>
            </a>
          </div>
          @if($mostRecentBooking)
          <div class="col-md-3 col-sm-6 col-6 mb-2">
            <a href="{{ route('customer.bookings.identity-card', $mostRecentBooking) }}" target="_blank" class="btn btn-light btn-block" style="min-height: 60px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none;">
              <i class="fa fa-id-card fa-2x mb-1"></i><br>
              <strong>My ID</strong>
            </a>
          </div>
          @endif
          @if($mostRecentBookingWithReceipt)
          <div class="col-md-3 col-sm-6 col-6 mb-2">
            <a href="{{ route('customer.payment.receipt.download', $mostRecentBookingWithReceipt) }}" target="_blank" class="btn btn-light btn-block" style="min-height: 60px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none;">
              <i class="fa fa-file-text fa-2x mb-1"></i><br>
              <strong>My Receipt</strong>
            </a>
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Only show current stay summary if guest has an active stay (checked in) --}}
@if($hasActiveStay)
<!-- Current Stay Summary Widget -->
@php
  $currentBooking = $activeBookings->where('check_in_status', 'checked_in')->first();
@endphp
@if($currentBooking)
@php
  $currentRoom = $currentBooking->room;
  $isCorporate = $currentBooking->is_corporate_booking ?? false;
  $company = $currentBooking->company ?? null;
  $paymentResponsibility = $currentBooking->payment_responsibility ?? 'company';
@endphp
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn">
        <h3 class="tile-title">
          <i class="fa {{ $isCorporate ? 'fa-building' : 'fa-bed' }}"></i> Current Stay Information
          {{-- @if($isCorporate)
            <span class="badge badge-warning ml-2" style="background-color: #940000; color: white; font-size: 11px;">Company Booking</span>
          @endif --}}
        </h3>
        <div class="btn-group">
          <button onclick="showDirectionsMap()" class="btn btn-sm btn-success">
            <i class="fa fa-map-marker"></i> Get Directions
          </button>
        </div>
      </div>
      <div class="tile-body">
        {{-- @if($isCorporate && $company)
        <div class="alert alert-info mb-3" style="background-color: #e7f3ff; border-left: 3px solid #940000;">
          <div class="d-flex align-items-center">
            <i class="fa fa-building fa-lg mr-2" style="color: #940000;"></i>
            <div>
              <strong>Company Booking:</strong> {{ $company->name }}
              <br><small class="text-muted">Booking Reference: {{ $currentBooking->booking_reference }}</small>
            </div>
          </div>
        </div>
        @endif --}}

        @php
          // Get room-specific WiFi, fallback to hotel-wide WiFi
          $roomWifiNetwork = $currentRoom->wifi_network_name ?? $wifiNetworkName ?? 'Umoj Lutheran Hostel_Hotel';
          $roomWifiPassword = $currentRoom->wifi_password ?? $wifiPassword ?? null;
        @endphp

        <div class="status-grid">
          {{-- @if($isCorporate && $company)
          <div class="status-card full-width primary">
            <i class="fa fa-building"></i>
            <div class="status-card-content">
              <span class="status-card-label">Company Booking</span>
              <span class="status-card-value">{{ $company->name }}</span>
            </div>
          </div>
          @endif --}}

          <!-- Row 1: Room Number & Check-in -->
          <div class="status-card primary">
            <i class="fa fa-home"></i>
            <div class="status-card-content">
              <span class="status-card-label">Room Number</span>
              <span class="status-card-value">
                <span class="badge badge-primary" style="padding: 4px 8px;">{{ $currentRoom->room_number ?? 'N/A' }}</span>
              </span>
            </div>
          </div>
          <div class="status-card info">
            <i class="fa fa-calendar"></i>
            <div class="status-card-content">
              <span class="status-card-label">Check-in Date</span>
              <span class="status-card-value">{{ $currentBooking->check_in->format('M d, Y') }}</span>
            </div>
          </div>

          <!-- Row 2: Room Type & Check-out -->
          <div class="status-card info">
            <i class="fa fa-bed"></i>
            <div class="status-card-content">
              <span class="status-card-label">Room Type</span>
              <span class="status-card-value">{{ $currentRoom->room_type ?? 'N/A' }}</span>
            </div>
          </div>
          <div class="status-card warning" style="border-left-color: #ffc107;">
            <i class="fa fa-calendar-times-o" style="color: #ffc107;"></i>
            <div class="status-card-content">
              <span class="status-card-label">Check-out Date</span>
              <span class="status-card-value">{{ $currentBooking->check_out->format('M d, Y') }}</span>
            </div>
          </div>

          <!-- Row 3: WiFi Password (Full Width) -->
          <div class="status-card full-width info">
            <i class="fa fa-key"></i>
            <div class="status-card-content">
              <span class="status-card-label">WiFi Password</span>
              @if($roomWifiPassword)
                <div class="input-group">
                  <input type="password" class="form-control" id="wifiPasswordDisplay" value="{{ $roomWifiPassword }}" readonly style="font-family: monospace; font-weight: bold; background: white; border-right: none;">
                  <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button" onclick="toggleWifiPassword()" style="background: white; border-left: none;">
                      <i class="fa fa-eye" id="wifiPasswordToggle" style="margin: 0; font-size: 14px; position: static;"></i>
                    </button>
                    <button class="btn btn-outline-primary" type="button" onclick="copyWifiPassword()" style="background: white;">
                      <i class="fa fa-copy" style="margin: 0; font-size: 14px; position: static;"></i>
                    </button>
                  </div>
                </div>
              @else
                <span class="status-card-value text-muted">Not Set</span>
              @endif
            </div>
          </div>

          <!-- Row 4: WiFi Network & Report Issue -->
          <div class="status-card info">
            <i class="fa fa-wifi"></i>
            <div class="status-card-content">
              <span class="status-card-label">WiFi Network</span>
              <span class="status-card-value">{{ $roomWifiNetwork }}</span>
            </div>
          </div>
          <div class="status-card danger" style="cursor: pointer; background: #fff5f5; border-color: #feb2b2; border-left-color: #dc3545;" onclick="openReportIssueModal()">
            <i class="fa fa-exclamation-triangle"></i>
            <div class="status-card-content">
              <span class="status-card-label" style="color: #c53030;">Reception</span>
              <span class="status-card-value" style="color: #dc3545;">Report Issue</span>
            </div>
          </div>

          @if($isCorporate)
          <div class="status-card full-width success">
            <i class="fa fa-info-circle"></i>
            <div class="status-card-content">
              <span class="status-card-label">Payment Policy</span>
              <div class="d-flex flex-wrap gap-2 mt-1">
                <span class="badge badge-success" style="font-size: 10px;">Room: Company Paid</span>
                <span class="badge {{ $paymentResponsibility == 'self' ? 'badge-warning' : 'badge-info' }}" style="font-size: 10px;">Services: {{ $paymentResponsibility == 'self' ? 'Self-Paid' : 'Company Paid' }}</span>
              </div>
            </div>
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endif
@endif

{{-- Only show payment summary if guest has an active stay (checked in) --}}
@if($hasActiveStay && $activeBookings->count() > 0)
<div class="row mb-3">
  <div class="col-md-6">
    <div class="tile">
      <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <h3 class="tile-title mb-0 mb-2 mb-md-0"><i class="fa fa-money"></i> Payment Summary</h3>
        <a href="{{ route('customer.payments') }}" class="btn btn-primary btn-sm">
          <i class="fa fa-credit-card"></i> View Payments
        </a>
      </div>
      <div class="tile-body">
        @php
          $totalBillTZS = 0;
          $totalPaidTZS = 0;
          $bookingWithOutstanding = null;
          $hasCorporateBooking = false;
          $companyRoomChargesTZS = 0;
          $companyServiceChargesTZS = 0;
          $selfServiceChargesTZS = 0;
          
          foreach($activeBookings as $booking) {
            $isCorporate = $booking->is_corporate_booking ?? false;
            $paymentResponsibility = $booking->payment_responsibility ?? 'company';
            if ($isCorporate) {
              $hasCorporateBooking = true;
            }
            
            // Use locked exchange rate for each booking
            $bookingExchangeRate = $booking->locked_exchange_rate ?? $exchangeRate;
            
            // Get all approved/completed service requests
            $serviceRequests = $booking->serviceRequests()
                ->whereIn('status', ['approved', 'completed'])
                ->with('service')
                ->get();
                
            // For checked-out bookings, calculate additional charges only
            if ($booking->check_in_status === 'checked_out') {
              $extensionCostUsd = 0;
              if ($booking->extension_status === 'approved' && $booking->original_check_out && $booking->extension_requested_to) {
                $originalCheckOut = \Carbon\Carbon::parse($booking->original_check_out);
                $requestedCheckOut = \Carbon\Carbon::parse($booking->extension_requested_to);
                $extensionNights = $originalCheckOut->diffInDays($requestedCheckOut);
                if ($extensionNights > 0 && $booking->room) {
                  $extensionCostUsd = $booking->room->price_per_night * $extensionNights;
                }
              }
              
              // Handle corporate services based on responsibility
              if ($isCorporate) {
                  $extensionCostUSD = $extensionCostUsd;
                  $companyRoomChargesTZS += $extensionCostUSD * $bookingExchangeRate;
                  
                  if ($paymentResponsibility == 'self') {
                      // Guest pays for ALL services (including room_charge ones)
                      $selfServiceChargesTZS += $serviceRequests->sum('total_price_tsh');
                      $billTZS = $serviceRequests->sum('total_price_tsh');
                      
                      // Calculate paid services
                      $paidTZS = $serviceRequests->where('payment_status', 'paid')->sum('total_price_tsh');
                  } else {
                      // Company pays for everything
                      $companyServiceChargesTZS += $serviceRequests->sum('total_price_tsh');
                      $billTZS = 0;
                      $paidTZS = 0;
                  }
              } else {
                  // Individual booking check-out
                  $otherServiceChargesTZS = $serviceRequests->sum('total_price_tsh');
                  $extensionCostTZS = $extensionCostUsd * $bookingExchangeRate;
                  $billTZS = $extensionCostTZS + $otherServiceChargesTZS;
                  
                  // Calculate paid additional charges
                  $paidAdditionalChargesTZS = (($booking->amount_paid ?? 0) * $bookingExchangeRate) - ($booking->total_price * $bookingExchangeRate);
                  $paidTZS = max(0, $paidAdditionalChargesTZS);
              }
            } else {
              // For active bookings
              $roomPriceTZS = $booking->total_price * $bookingExchangeRate; 
              $extensionCostTZS = 0;
              
              if ($booking->extension_status === 'pending' && $booking->original_check_out && $booking->extension_requested_to) {
                $originalCheckOut = \Carbon\Carbon::parse($booking->original_check_out);
                $requestedCheckOut = \Carbon\Carbon::parse($booking->extension_requested_to);
                $extensionNights = $originalCheckOut->diffInDays($requestedCheckOut);
                if ($extensionNights > 0 && $booking->room) {
                   $extensionCostTZS = ($booking->room->price_per_night * $extensionNights) * $bookingExchangeRate;
                }
              }
              
              if ($isCorporate) {
                // Room charges are always paid by company
                $companyRoomChargesTZS += $roomPriceTZS + $extensionCostTZS;
                
                if ($paymentResponsibility == 'self') {
                  // Guest pays for ALL services
                  $selfServiceChargesTZS += $serviceRequests->sum('total_price_tsh');
                  $billTZS = $serviceRequests->sum('total_price_tsh');
                  
                  // Guest's payment is for their services
                  $paidTZS = $serviceRequests->where('payment_status', 'paid')->sum('total_price_tsh');
                } else {
                  // Company pays for everything
                  $companyServiceChargesTZS += $serviceRequests->sum('total_price_tsh');
                  $billTZS = 0;
                  $paidTZS = 0;
                }
              } else {
                // For individual bookings, guest pays everything
                $totalServiceChargesTZS = $serviceRequests->sum('total_price_tsh');
                $billTZS = $roomPriceTZS + $extensionCostTZS + $totalServiceChargesTZS;
                
                // Amount paid in TZS
                if ($booking->payment_status === 'paid' && !$booking->amount_paid) {
                  $paidTZS = $roomPriceTZS;
                } else {
                  $paidTZS = ($booking->amount_paid ?? 0) * $bookingExchangeRate;
                }
                
                // Add services that are already paid directly
                $paidServicesTZS = $serviceRequests->where('payment_status', 'paid')->sum('total_price_tsh');
                $paidTZS += $paidServicesTZS;
              }
            }
            
            $bookingOutstandingTZS = $billTZS - $paidTZS;
            if ($bookingOutstandingTZS > 50 && !$bookingWithOutstanding) {
              $bookingWithOutstanding = $booking;
            }
            
            $totalBillTZS += $billTZS;
            $totalPaidTZS += $paidTZS;
          }
          
          $outstandingTZS = $totalBillTZS - $totalPaidTZS;
          
          // Clear negligible balances
          if (abs($outstandingTZS) < 50) {
              $outstandingTZS = 0;
          }
        @endphp
        @if($hasCorporateBooking)
        <div class="alert alert-info mb-3" style="background-color: #e7f3ff; border-left: 4px solid #940000;">
          <h6 style="color: #940000; margin-bottom: 10px;"><i class="fa fa-building"></i> Company Booking Payment Breakdown</h6>
          <div style="font-size: 12px;">
            <div class="mb-2">
              <strong>Room Charges:</strong> 
              <span class="badge badge-success" style="background-color: #28a745;">Company Paid</span>
              <span class="text-muted">({{ number_format($companyRoomChargesTZS, 2) }} TZS)</span>
            </div>
            @php
              // Check payment responsibility for services
              $hasSelfPaidServices = false;
              $hasCompanyPaidServices = false;
              foreach($activeBookings as $b) {
                if ($b->is_corporate_booking ?? false) {
                  if (($b->payment_responsibility ?? 'company') == 'self') {
                    $hasSelfPaidServices = true;
                  } else {
                    $hasCompanyPaidServices = true;
                  }
                }
              }
            @endphp
            @if($hasSelfPaidServices)
            <div class="mb-2">
              <strong>Service Charges:</strong> 
              <span class="badge badge-warning">Self-Paid</span>
              @if($selfServiceChargesTZS > 0)
                <span class="text-muted">({{ number_format($selfServiceChargesTZS, 2) }} TZS)</span>
              @else
                <span class="text-muted">(0.00 TZS - No services used yet)</span>
              @endif
            </div>
            @endif
            @if($hasCompanyPaidServices || $companyServiceChargesTZS > 0)
            <div class="mb-2">
              <strong>Service Charges:</strong> 
              <span class="badge badge-info">Company Paid</span>
              @if($companyServiceChargesTZS > 0)
                <span class="text-muted">({{ number_format($companyServiceChargesTZS, 2) }} TZS)</span>
              @else
                <span class="text-muted">(0.00 TZS - No services used yet)</span>
              @endif
            </div>
            @endif
          </div>
        </div>
        @endif
        <div class="status-grid">
          <!-- Row 1: Total Bill & Amount Paid -->
          <div class="status-card info">
            <i class="fa fa-file-text-o"></i>
            <div class="status-card-content">
              <span class="status-card-label">Total Bill{{ $hasCorporateBooking ? ' (Your Portion)' : '' }}</span>
              <span class="status-card-value">
                {{ number_format($totalBillTZS, 2) }} TZS
              </span>
            </div>
          </div>
          <div class="status-card success">
            <i class="fa fa-check-circle"></i>
            <div class="status-card-content">
              <span class="status-card-label">Amount Paid</span>
              <span class="status-card-value text-success">
                {{ number_format($totalPaidTZS, 2) }} TZS
              </span>
            </div>
          </div>

          <!-- Row 2: Balance & Status -->
          <div class="status-card {{ $outstandingTZS > 0 ? 'danger' : 'success' }}" style="{{ $outstandingTZS > 0 ? 'background: #fff5f5;' : '' }}">
            <i class="fa fa-money"></i>
            <div class="status-card-content">
              <span class="status-card-label">{{ $outstandingTZS < 0 ? 'Credit Balance' : 'Balance Due' }}</span>
              <span class="status-card-value {{ $outstandingTZS > 0 ? 'text-danger' : 'text-success' }}">
                {{ number_format(abs($outstandingTZS), 2) }} TZS
              </span>
            </div>
          </div>
          <div class="status-card {{ $outstandingTZS > 0 ? 'info' : 'success' }}">
            <i class="fa fa-credit-card"></i>
            <div class="status-card-content">
              <span class="status-card-label">Payment Status</span>
              <span class="status-card-value">
                @if($outstandingTZS > 0)
                  <span class="badge badge-warning">Pending Payment</span>
                @else
                  <span class="badge badge-success">All Settled</span>
                @endif
              </span>
            </div>
          </div>
        </div>
        @if($outstandingTZS > 0 && $bookingWithOutstanding)
        <div class="alert alert-info mt-3" style="background-color: #e7f3ff; border-left: 4px solid #2196F3;">
          <div class="d-flex align-items-center">
            <i class="fa fa-info-circle fa-2x mr-3" style="color: #2196F3;"></i>
            <div>
              <strong style="color: #1976D2;">Payment Required</strong>
              <p class="mb-0 mt-1" style="color: #555;">
                Please visit the reception desk to settle your outstanding balance of 
                <strong>{{ number_format($outstandingTZS, 2) }} TZS</strong>.
              </p>
            </div>
          </div>
        </div>
        @endif
      </div>
    </div>
  </div>
  
  <!-- 5. Hotel Information Card -->
  <div class="col-md-6">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-hotel"></i> Hotel Information</h3>
      <div class="tile-body">
        <div class="status-grid">
          <!-- Row 1: Location & Phone -->
          <div class="status-card primary">
            <i class="fa fa-map-marker"></i>
            <div class="status-card-content">
              <span class="status-card-label">Location</span>
              <span class="status-card-value" style="font-size: 12px;">Sokoine Road, Moshi</span>
            </div>
          </div>
          <div class="status-card primary">
            <i class="fa fa-phone"></i>
            <div class="status-card-content">
              <span class="status-card-label">Reception</span>
              <span class="status-card-value"><a href="tel:+255677155156" style="color: inherit;">0677155156</a></span>
            </div>
          </div>

          <!-- Row 2: Email & Front Desk -->
          <div class="status-card primary">
            <i class="fa fa-envelope"></i>
            <div class="status-card-content">
              <span class="status-card-label">Email Support</span>
              <span class="status-card-value" style="font-size: 11px;">info@Umoj Lutheran Hostelhotel.com</span>
            </div>
          </div>
          <div class="status-card info">
            <i class="fa fa-phone-square"></i>
            <div class="status-card-content">
              <span class="status-card-label">Front Desk</span>
              <span class="status-card-value">24/7 Available</span>
            </div>
          </div>

          <!-- Row 3: Check-in & Check-out Times -->
          <div class="status-card info">
            <i class="fa fa-clock-o"></i>
            <div class="status-card-content">
              <span class="status-card-label">Check-in Time</span>
              <span class="status-card-value">2:00 PM onwards</span>
            </div>
          </div>
          <div class="status-card info">
            <i class="fa fa-sign-out"></i>
            <div class="status-card-content">
              <span class="status-card-label">Check-out Time</span>
              <span class="status-card-value">By 10:00 AM</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endif

<!-- Report Issue Modal -->
<div class="modal fade" id="reportIssueModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background: #ffc107; color: #333;">
        <h5 class="modal-title"><i class="fa fa-exclamation-triangle"></i> Report an Issue</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="reportIssueForm">
          @if($activeBookings->count() > 0)
          <input type="hidden" id="issue_booking_id" name="booking_id" value="{{ $activeBookings->first()->id }}">
          <input type="hidden" id="issue_room_id" name="room_id" value="{{ $activeBookings->first()->room_id }}">
          @endif
          <div class="form-group">
            <label for="issue_type">Issue Type *</label>
            <select class="form-control" id="issue_type" name="issue_type" required>
              <option value="">Select Issue Type</option>
              <option value="room_issue">Room Issue (e.g., AC not working, broken furniture)</option>
              <option value="service_issue">Service Issue (e.g., housekeeping, room service)</option>
              <option value="technical_issue">Technical Issue (e.g., WiFi, TV, plumbing)</option>
              <option value="other">Other</option>
            </select>
          </div>
          <div class="form-group">
            <label for="issue_priority">Priority *</label>
            <select class="form-control" id="issue_priority" name="priority" required>
              <option value="low">Low - Can wait</option>
              <option value="medium" selected>Medium - Normal priority</option>
              <option value="high">High - Urgent attention needed</option>
              <option value="urgent">Urgent - Immediate attention required</option>
            </select>
          </div>
          <div class="form-group">
            <label for="issue_subject">Subject *</label>
            <input type="text" class="form-control" id="issue_subject" name="subject" placeholder="Brief description of the issue" required maxlength="255">
          </div>
          <div class="form-group">
            <label for="issue_description">Description *</label>
            <textarea class="form-control" id="issue_description" name="description" rows="5" placeholder="Please provide detailed information about the issue..." required></textarea>
          </div>
          <div id="issueReportAlert"></div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-warning" id="submitIssueBtn" onclick="submitIssueReport(this)">
          <i class="fa fa-paper-plane"></i> Submit Report
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Decrease Request Modal -->
<div class="modal fade" id="decreaseModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background: #ffc107; color: white;">
        <h5 class="modal-title"><i class="fa fa-calendar-minus-o"></i> Request Stay Decrease</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="decreaseForm">
          <input type="hidden" id="decrease_booking_id" name="booking_id">
          <div class="alert alert-warning">
            <i class="fa fa-info-circle"></i> <strong>Important:</strong> Your decrease request will be reviewed by reception. <strong>Please note that no refund will be provided</strong> for the reduced nights. The booking will be adjusted, but you will not receive any money back.
          </div>
          <div class="form-group">
            <label for="decrease_requested_to">New Check-out Date *</label>
            <input type="date" class="form-control" id="decrease_requested_to" name="decrease_requested_to" required>
            <small class="form-text text-muted">Select a date before your current check-out date.</small>
          </div>
          <div class="form-group">
            <label for="decrease_reason">Reason for Decrease (Optional)</label>
            <textarea class="form-control" id="decrease_reason" name="decrease_reason" rows="3" placeholder="Please provide a reason for decreasing your stay..."></textarea>
            <small class="form-text text-muted">This will help reception process your request faster.</small>
          </div>
          <div id="decreaseCostPreview" style="display: none; padding: 15px; background: #fff3cd; border-radius: 5px; margin-bottom: 15px;">
            <p class="mb-0">
              <span id="decreaseNights">0</span> night(s) reduction × 
              $<span id="decreaseRoomPrice">0</span> per night = 
              <strong>Amount: $<span id="decreaseTotalRefund">0</span></strong>
            </p>
            <small class="text-danger"><strong>Note: No refund will be provided for the reduced nights.</strong></small>
          </div>
          <div id="decreaseAlert"></div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-warning" onclick="submitDecreaseRequest()">
          <i class="fa fa-paper-plane"></i> Submit Request
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Extension Request Modal -->
<div class="modal fade" id="extensionModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background: #17a2b8; color: white;">
        <h5 class="modal-title"><i class="fa fa-calendar-plus-o"></i> Request Stay Extension</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="extensionForm">
          <input type="hidden" id="extension_booking_id" name="booking_id">
          <div class="alert alert-info">
            <i class="fa fa-info-circle"></i> <strong>Important:</strong> Your extension request will be reviewed by reception. <strong>Additional payment will be required</strong> based on the number of extra nights. Please ensure you are ready to make the payment upon approval.
          </div>
          <div class="form-group">
            <label for="extension_requested_to">New Check-out Date *</label>
            <input type="date" class="form-control" id="extension_requested_to" name="extension_requested_to" required>
            <small class="form-text text-muted">Select a date after your current check-out date.</small>
          </div>
          <div class="form-group">
            <label for="extension_reason">Reason for Extension (Optional)</label>
            <textarea class="form-control" id="extension_reason" name="extension_reason" rows="3" placeholder="Please provide a reason for extending your stay..."></textarea>
            <small class="form-text text-muted">This will help reception process your request faster.</small>
          </div>
          <div id="extensionCostPreview" style="display: none; padding: 15px; background: #f8f9fa; border-radius: 5px; margin-bottom: 15px;">
            <h6><strong>Estimated Additional Cost:</strong></h6>
            <p class="mb-0">
              <span id="extensionNights">0</span> additional night(s) × 
              $<span id="extensionRoomPrice">0</span> per night = 
              <strong>$<span id="extensionTotalCost">0</span></strong>
            </p>
            <small class="text-muted">Final amount will be confirmed upon approval.</small>
          </div>
          <div id="extensionAlert"></div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-info" onclick="submitExtensionRequest()">
          <i class="fa fa-paper-plane"></i> Submit Request
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Service Request Modal -->
<div class="modal fade" id="serviceRequestModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-plus-circle"></i> Request a Service</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="serviceRequestForm">
          <input type="hidden" id="service_booking_id" name="booking_id">
          <div class="form-group">
            <label for="service_id">Select Service *</label>
            <select class="form-control" id="service_id" name="service_id" required onchange="calculateServicePrice()">
              <option value="">Loading services...</option>
            </select>
          </div>
          <!-- Adult/Child Quantity Selection (shown when service supports both adult and child) -->
          <div id="adultChildQuantityGroup" style="display: none;">
            <div class="form-group">
              <label for="service_adult_quantity">Number of Adults *</label>
              <input type="number" class="form-control" id="service_adult_quantity" name="adult_quantity" min="0" value="1" oninput="calculateServicePrice()">
              <small class="form-text text-muted">Enter the number of adults</small>
            </div>
            <div class="form-group">
              <label for="service_child_quantity">Number of Children <small class="text-muted">(Optional)</small></label>
              <div class="input-group">
                <input type="number" class="form-control" id="service_child_quantity" name="child_quantity" min="0" value="0" oninput="calculateServicePrice()">
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

          <!-- Single Quantity Field (shown when service is adult-only or child-only) -->
          <div class="form-group" id="singleQuantityGroup">
            <label for="service_quantity">Quantity <small class="text-muted">(Optional)</small></label>
            <input type="number" class="form-control" id="service_quantity" name="quantity" min="1" value="1" oninput="calculateServicePrice()">
            <small class="form-text text-muted" id="service_unit">Leave as 1 if requesting a single service</small>
          </div>
          
          <!-- Service-Specific Fields (Dynamic) -->
          <div id="serviceSpecificFields" style="display: none;">
            <hr style="margin: 20px 0; border-color: #e07632;">
            <h6 style="color: #e07632; margin-bottom: 15px;"><i class="fa fa-info-circle"></i> Service Details</h6>
            <!-- Fields will be dynamically added here -->
          </div>
          
          <div class="form-group">
            <label for="guest_request">Additional Notes (Optional)</label>
            <textarea class="form-control" id="guest_request" name="guest_request" rows="3" placeholder="Any special requests or notes..."></textarea>
          </div>
          <div class="alert alert-info" id="service_price_info" style="display: none;">
            <strong>Estimated Price:</strong> <span id="service_total_price">0</span> TZS
            <span id="service_price_details" style="display: none;"></span>
          </div>
          <div id="serviceRequestAlert"></div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="submitServiceRequest()">
          <i class="fa fa-paper-plane"></i> Submit Request
        </button>
      </div>
    </div>
  </div>
</div>

{{-- Only show active bookings if guest has an active stay (checked in) --}}
@if($hasActiveStay && $activeBookings->count() > 0)
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-calendar-check-o"></i> Active Bookings</h3>
      <div class="tile-body">
        <!-- Mobile Card View -->
        <div class="mobile-bookings-cards d-md-none">
          @foreach($activeBookings as $booking)
          @php
            // Check if booking was extended or decreased (for mobile view)
            $isExtended = false;
            $isDecreased = false;
            $extendedNights = 0;
            $decreasedNights = 0;
            if ($booking->original_check_out) {
              $originalCheckOut = \Carbon\Carbon::parse($booking->original_check_out);
              $currentCheckOut = \Carbon\Carbon::parse($booking->check_out);
              if ($currentCheckOut->gt($originalCheckOut)) {
                $isExtended = true;
                $extendedNights = $originalCheckOut->diffInDays($currentCheckOut);
              } elseif ($currentCheckOut->lt($originalCheckOut)) {
                $isDecreased = true;
                $decreasedNights = $currentCheckOut->diffInDays($originalCheckOut);
              }
            }
          @endphp
          <div class="booking-card-mobile mb-3" data-booking-id="{{ $booking->id }}" style="border: 1px solid #ddd; border-radius: 8px; padding: 15px; background: white; cursor: pointer;">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <div>
                <strong style="font-size: 16px; color: #e07632;">{{ $booking->booking_reference }}</strong>
                <br><small class="text-muted">Guest ID: {{ $booking->guest_id ?? 'N/A' }}</small>
              </div>
              <i class="fa fa-chevron-down toggle-details-mobile" style="color: #e07632; font-size: 18px;"></i>
            </div>
            <div class="row mb-2">
              <div class="col-6">
                <small class="text-muted">Room</small><br>
                <span class="badge badge-primary">{{ $booking->room->room_type ?? 'N/A' }}</span><br>
                <small>{{ $booking->room->room_number ?? 'N/A' }}</small>
              </div>
              <div class="col-6">
                <small class="text-muted">Nights</small><br>
                <strong>{{ $booking->check_in->diffInDays($booking->check_out) }} nights</strong>
                @if(($isExtended || $isDecreased) && $booking->original_check_out)
                  @php
                    $originalNights = $booking->check_in->diffInDays(\Carbon\Carbon::parse($booking->original_check_out));
                  @endphp
                  <br><small class="text-muted" style="font-size: 10px;">(Original: {{ $originalNights }} nights)</small>
                @endif
              </div>
            </div>
            <div class="row mb-2">
              <div class="col-6">
                <small class="text-muted">Check-in</small><br>
                <strong>{{ $booking->check_in->format('M d, Y') }}</strong>
                @if($booking->check_in_status === 'pending' && $booking->check_in->isFuture())
                  <br><small class="text-info">
                    <i class="fa fa-clock-o"></i> 
                    <span class="countdown-checkin-booking-{{ $booking->id }}" data-date="{{ $booking->check_in->format('Y-m-d H:i:s') }}">Calculating...</span>
                  </small>
                @endif
              </div>
              <div class="col-6">
                <small class="text-muted">Check-out</small><br>
                <strong>{{ $booking->check_out->format('M d, Y') }}</strong>
                @if($isExtended && $extendedNights > 0)
                  <br><span class="badge badge-info" style="margin-top: 3px; font-size: 10px;">
                    <i class="fa fa-calendar-plus-o"></i> Extended by {{ $extendedNights }} night{{ $extendedNights > 1 ? 's' : '' }}
                  </span>
                @elseif($isDecreased && $decreasedNights > 0)
                  <br><span class="badge badge-warning" style="margin-top: 3px; font-size: 10px;">
                    <i class="fa fa-calendar-minus-o"></i> Decreased by {{ $decreasedNights }} night{{ $decreasedNights > 1 ? 's' : '' }}
                  </span>
                @endif
                @if($booking->check_in_status === 'checked_in' && $booking->check_out->isFuture())
                  <br><small class="text-warning">
                    <i class="fa fa-clock-o"></i> 
                    <span class="countdown-checkout-booking-{{ $booking->id }}" data-date="{{ $booking->check_out->format('Y-m-d H:i:s') }}">Calculating...</span>
                  </small>
                @endif
              </div>
            </div>
            <div class="row mb-2">
              <div class="col-6">
                <small class="text-muted">Total Price</small><br>
                @php

                  // For corporate bookings, calculate guest's portion
                  $isCorporateMobile = $booking->is_corporate_booking ?? false;
                  $paymentResponsibilityMobile = $booking->payment_responsibility ?? 'company';
                  
                  if ($isCorporateMobile) {
                    // Calculate service charges
                    $serviceRequestsMobile = $booking->serviceRequests()
                      ->whereIn('status', ['approved', 'completed'])
                      ->with('service')
                      ->get();
                    
                    $otherServiceChargesTshMobile = $serviceRequestsMobile
                      ->where('service.category', '!=', 'transport')
                      ->sum('total_price_tsh');
                    
                    $transportationChargesTshMobile = 0;
                    if ($booking->airport_pickup_required) {
                      $airportPickupServiceMobile = $serviceRequestsMobile->firstWhere('service.category', 'transport');
                      if ($airportPickupServiceMobile) {
                        $transportationChargesTshMobile = $airportPickupServiceMobile->total_price_tsh;
                      }
                    }
                    
                    if ($paymentResponsibilityMobile == 'self') {
                      $guestBillTZSMobile = ($otherServiceChargesTshMobile + $transportationChargesTshMobile);
                    } else {
                      $guestBillTZSMobile = 0;
                    }
                    
                    $bookingPriceTZS = $guestBillTZSMobile;
                  } else {
                    $bookingPriceTZS = $booking->total_price * ($booking->locked_exchange_rate ?? $exchangeRate ?? 2500);
                  }
                @endphp
                @if($isCorporateMobile)
                  @if($paymentResponsibilityMobile == 'self' && $bookingPriceTZS > 0)
                    <strong>{{ number_format($bookingPriceTZS, 2) }} TZS</strong>
                    <br><small class="text-muted">(Services only)</small>
                  @else
                    <strong>0.00 TZS</strong><br>
                    <small class="text-muted">(Company Paid)</small>
                  @endif
                @else
                    <strong>{{ number_format($bookingPriceTZS, 2) }} TZS</strong>
                @endif
                @if($isExtended && $booking->room && $extendedNights > 0)
                  @php
                    $extensionCost = ($booking->room->price_per_night * $extendedNights) * ($booking->locked_exchange_rate ?? $exchangeRate ?? 2500);
                    $originalPrice = ($booking->total_price * ($booking->locked_exchange_rate ?? $exchangeRate ?? 2500)) - $extensionCost;
                  @endphp
                  <br><small class="text-info" style="font-size: 10px;">
                    <i class="fa fa-calendar-plus-o"></i> +{{ number_format($extensionCost, 2) }} TZS (extension)
                  </small>
                  <br><small class="text-muted" style="font-size: 10px;">Original: {{ number_format($originalPrice, 2) }} TZS</small>
                @elseif($isDecreased && $booking->room && $decreasedNights > 0)
                  @php
                    $decreaseRefund = ($booking->room->price_per_night * $decreasedNights) * ($booking->locked_exchange_rate ?? $exchangeRate ?? 2500);
                    $originalPrice = ($booking->total_price * ($booking->locked_exchange_rate ?? $exchangeRate ?? 2500)) + $decreaseRefund;
                  @endphp
                  <br><small class="text-warning" style="font-size: 10px;">
                    <i class="fa fa-calendar-minus-o"></i> -{{ number_format($decreaseRefund, 2) }} TZS (decrease)
                  </small>
                  <br><small class="text-muted" style="font-size: 10px;">Original: {{ number_format($originalPrice, 2) }} TZS</small>
                @endif
              </div>
              <div class="col-6">
                <small class="text-muted">Status</small><br>
                @if($booking->status === 'confirmed')
                  <span class="badge badge-success">Confirmed</span>
                @else
                  <span class="badge badge-warning">{{ ucfirst($booking->status) }}</span>
                @endif
                <br>
                @if($booking->check_in_status === 'pending')
                  <span class="badge badge-warning mt-1">Not Checked In</span>
                @elseif($booking->check_in_status === 'checked_in')
                  <span class="badge badge-success mt-1">Checked In</span>
                  @if($booking->checked_in_at)
                    <br><small>{{ $booking->checked_in_at->format('M d, H:i') }}</small>
                  @endif
                @elseif($booking->check_in_status === 'checked_out')
                  <span class="badge badge-warning mt-1">Checked Out</span>
                  @if($booking->checked_out_at)
                    <br><small>{{ $booking->checked_out_at->format('M d, H:i') }}</small>
                  @endif
                  @if($booking->payment_status !== 'paid')
                    <br><a href="{{ route('customer.bookings.checkout-payment', $booking) }}" class="btn btn-sm btn-primary mt-1" style="font-size: 10px; padding: 2px 6px;">
                      <i class="fa fa-credit-card"></i> Pay Now
                    </a>
                  @endif
                @else
                  <span class="badge badge-info mt-1">{{ ucfirst($booking->check_in_status) }}</span>
                @endif
              </div>
            </div>
            <!-- Expandable Details Mobile -->
            <div class="booking-details-mobile" id="details-mobile-{{ $booking->id }}" style="display: none; margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee;">
              <h6 style="color: #e07632; margin-bottom: 10px; font-size: 14px;"><i class="fa fa-info-circle"></i> Booking Details</h6>
              <div style="font-size: 12px;">
                <div class="mb-2"><strong>Guest ID:</strong> {{ $booking->guest_id ?? 'N/A' }}</div>
                <div class="mb-2"><strong>Room:</strong> {{ $booking->room->room_number ?? 'N/A' }} ({{ $booking->room->room_type ?? 'N/A' }})</div>
                <div class="mb-2"><strong>Check-in:</strong> {{ $booking->check_in->format('M d, Y') }}</div>
                <div class="mb-2"><strong>Check-out:</strong> {{ $booking->check_out->format('M d, Y') }}</div>
                <div class="mb-2"><strong>Nights:</strong> {{ $booking->check_in->diffInDays($booking->check_out) }} nights</div>
                @php
                  $bookingGuestType = $booking->guest_type ?? 'international';
                  $isBookingTanzanian = $bookingGuestType === 'tanzanian';
                  $bookingRate = $booking->locked_exchange_rate ?? $exchangeRate ?? 2500;
                  $bookingPriceTZS = $booking->total_price * $bookingRate;
                @endphp
                <div class="mb-2"><strong>Total Price:</strong> 
                  @if($isBookingTanzanian)
                    {{ number_format($bookingPriceTZS, 2) }} TZS
                  @else
                    ${{ number_format($booking->total_price, 2) }} (≈ {{ number_format($bookingPriceTZS, 2) }} TZS)
                  @endif
                </div>
                <div class="mb-2"><strong>Status:</strong> {{ ucfirst($booking->status) }}</div>
                <div class="mb-2"><strong>Check-in Status:</strong> {{ ucfirst(str_replace('_', ' ', $booking->check_in_status)) }}</div>
                @if($booking->checked_in_at)
                  <div class="mb-2"><strong>Checked In At:</strong> {{ $booking->checked_in_at->format('M d, Y H:i') }}</div>
                @endif
                @if($booking->checked_out_at)
                  <div class="mb-2"><strong>Checked Out At:</strong> {{ $booking->checked_out_at->format('M d, Y H:i') }}</div>
                @endif
              </div>
            </div>
          </div>
          @endforeach
        </div>
        
        <!-- Desktop Table View -->
        <div class="table-responsive d-none d-md-block">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>Booking Reference</th>
                <th>Room</th>
                <th>Check-in</th>
                <th>Check-out</th>
                <th>Nights</th>
                <th>Total Price</th>
                <th>Status</th>
                <th>Check-in Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($activeBookings as $booking)
              @php
                $isCorporate = $booking->is_corporate_booking ?? false;
                $company = $booking->company ?? null;
                $paymentResponsibility = $booking->payment_responsibility ?? 'company';
              @endphp
              <tr class="booking-row" data-booking-id="{{ $booking->id }}" style="cursor: pointer;">
                <td>
                  <strong>{{ $booking->booking_reference }}</strong>
                  @if($isCorporate)
                    <span class="badge badge-warning ml-1" style="background-color: #940000; color: white; font-size: 9px;">Company</span>
                  @endif
                  <br><small class="text-muted">Guest ID: {{ $booking->guest_id ?? 'N/A' }}</small>
                  @if($isCorporate && $company)
                    <br><small class="text-muted"><i class="fa fa-building"></i> {{ $company->name }}</small>
                  @endif
                </td>
                <td>
                  <span class="badge badge-primary">{{ $booking->room->room_type ?? 'N/A' }}</span><br>
                  <small>{{ $booking->room->room_number ?? 'N/A' }}</small>
                  @if($isCorporate)
                    <br><small class="text-muted" style="font-size: 10px;">
                      <span class="badge badge-success" style="background-color: #28a745; font-size: 9px;">Room: Company</span>
                      <span class="badge {{ $paymentResponsibility == 'self' ? 'badge-warning' : 'badge-info' }}" style="font-size: 9px;">Services: {{ $paymentResponsibility == 'self' ? 'Self' : 'Company' }}</span>
                    </small>
                  @endif
                </td>
                <td>
                  {{ $booking->check_in->format('M d, Y') }}
                  @if($booking->check_in_status === 'pending' && $booking->check_in->isFuture())
                    <br><small class="text-info">
                      <i class="fa fa-clock-o"></i> 
                      <span class="countdown-checkin-booking-{{ $booking->id }}" data-date="{{ $booking->check_in->format('Y-m-d H:i:s') }}">Calculating...</span>
                    </small>
                  @endif
                </td>
                <td>
                  {{ $booking->check_out->format('M d, Y') }}
                  @php
                    $isExtended = false;
                    $isDecreased = false;
                    $extendedNights = 0;
                    $decreasedNights = 0;
                    if ($booking->original_check_out) {
                      $originalCheckOut = \Carbon\Carbon::parse($booking->original_check_out);
                      $currentCheckOut = \Carbon\Carbon::parse($booking->check_out);
                      if ($currentCheckOut->gt($originalCheckOut)) {
                        $isExtended = true;
                        $extendedNights = $originalCheckOut->diffInDays($currentCheckOut);
                      } elseif ($currentCheckOut->lt($originalCheckOut)) {
                        $isDecreased = true;
                        $decreasedNights = $currentCheckOut->diffInDays($originalCheckOut);
                      }
                    }
                  @endphp
                  @if($isExtended && $extendedNights > 0)
                    <br><span class="badge badge-info" style="margin-top: 5px;" title="Originally scheduled to check out on {{ \Carbon\Carbon::parse($booking->original_check_out)->format('M d, Y') }}">
                      <i class="fa fa-calendar-plus-o"></i> Extended by {{ $extendedNights }} night{{ $extendedNights > 1 ? 's' : '' }}
                    </span>
                  @elseif($isDecreased && $decreasedNights > 0)
                    <br><span class="badge badge-warning" style="margin-top: 5px;" title="Originally scheduled to check out on {{ \Carbon\Carbon::parse($booking->original_check_out)->format('M d, Y') }}">
                      <i class="fa fa-calendar-minus-o"></i> Decreased by {{ $decreasedNights }} night{{ $decreasedNights > 1 ? 's' : '' }}
                    </span>
                  @endif
                  @if($booking->check_in_status === 'checked_in' && $booking->check_out->isFuture())
                    <br><small class="text-warning">
                      <i class="fa fa-clock-o"></i> 
                      <span class="countdown-checkout-booking-{{ $booking->id }}" data-date="{{ $booking->check_out->format('Y-m-d H:i:s') }}">Calculating...</span>
                    </small>
                  @endif
                </td>
                <td>
                  {{ $booking->check_in->diffInDays($booking->check_out) }} nights
                  @if(($isExtended || $isDecreased) && $booking->original_check_out)
                    @php
                      $originalNights = $booking->check_in->diffInDays(\Carbon\Carbon::parse($booking->original_check_out));
                    @endphp
                    <br><small class="text-muted">(Original: {{ $originalNights }} night{{ $originalNights > 1 ? 's' : '' }})</small>
                  @endif
                </td>
                <td>
                <td>
                  @php
                    $isCorporate = $booking->is_corporate_booking ?? false;
                    $paymentResponsibility = $booking->payment_responsibility ?? 'company';
                    $bookingRate = $booking->locked_exchange_rate ?? $exchangeRate ?? 2500;
                    
                    // For corporate bookings, calculate guest's portion
                    if ($isCorporate) {
                      // Calculate service charges
                      $serviceRequests = $booking->serviceRequests()
                        ->whereIn('status', ['approved', 'completed'])
                        ->with('service')
                        ->get();
                      
                      $otherServiceChargesTsh = $serviceRequests
                        ->where('service.category', '!=', 'transport')
                        ->sum('total_price_tsh');
                      
                      $transportationChargesTsh = 0;
                      if ($booking->airport_pickup_required) {
                        $airportPickupService = $serviceRequests->firstWhere('service.category', 'transport');
                        if ($airportPickupService) {
                          $transportationChargesTsh = $airportPickupService->total_price_tsh;
                        }
                      }
                      
                      if ($paymentResponsibility == 'self') {
                        $guestBillTZS = ($otherServiceChargesTsh + $transportationChargesTsh);
                      } else {
                        $guestBillTZS = 0;
                      }
                      
                      $bookingPriceTZS = $guestBillTZS;
                    } else {
                      $bookingPriceTZS = $booking->total_price * $bookingRate;
                    }
                  @endphp
                  @if($isCorporate)
                    @if($paymentResponsibility == 'self' && $bookingPriceTZS > 0)
                        <div><strong>{{ number_format($bookingPriceTZS, 2) }} TZS</strong></div>
                        <div><small class="text-muted">(Services only)</small></div>
                    @else
                      <div><strong>0.00 TZS</strong></div>
                      <div><small class="text-muted">(Company Paid)</small></div>
                    @endif
                  @else
                      <div><strong>{{ number_format($bookingPriceTZS, 2) }} TZS</strong></div>
                  @endif
                  @if($isExtended && $booking->room && $extendedNights > 0)
                    @php
                      $extensionCost = ($booking->room->price_per_night * $extendedNights) * $bookingRate;
                      $originalPrice = ($booking->total_price * $bookingRate) - $extensionCost;
                    @endphp
                    <br><small class="text-info" style="display: block; margin-top: 5px;">
                      <i class="fa fa-calendar-plus-o"></i> +{{ number_format($extensionCost, 2) }} TZS (extension)
                    </small>
                    <br><small class="text-muted">Original: {{ number_format($originalPrice, 2) }} TZS</small>
                  @elseif($isDecreased && $booking->room && $decreasedNights > 0)
                    @php
                      $decreaseRefund = ($booking->room->price_per_night * $decreasedNights) * $bookingRate;
                      $originalPrice = ($booking->total_price * $bookingRate) + $decreaseRefund;
                    @endphp
                    <br><small class="text-warning" style="display: block; margin-top: 5px;">
                      <i class="fa fa-calendar-minus-o"></i> -{{ number_format($decreaseRefund, 2) }} TZS (decrease)
                    </small>
                    <br><small class="text-muted">Original: {{ number_format($originalPrice, 2) }} TZS</small>
                  @endif
                </td>
                <td>
                  @if($booking->status === 'confirmed')
                    <span class="badge badge-success">Confirmed</span>
                  @else
                    <span class="badge badge-warning">{{ ucfirst($booking->status) }}</span>
                  @endif
                </td>
                <td>
                  @if($booking->check_in_status === 'pending')
                    <span class="badge badge-warning">Not Checked In</span>
                  @elseif($booking->check_in_status === 'checked_in')
                    <span class="badge badge-success">Checked In</span>
                    @if($booking->checked_in_at)
                      <br><small>{{ $booking->checked_in_at->format('M d, H:i') }}</small>
                    @endif
                  @elseif($booking->check_in_status === 'checked_out')
                    <span class="badge badge-warning">Checked Out</span>
                    @if($booking->checked_out_at)
                      <br><small>{{ $booking->checked_out_at->format('M d, H:i') }}</small>
                    @endif
                    @if($booking->payment_status !== 'paid')
                      <br><a href="{{ route('customer.bookings.checkout-payment', $booking) }}" class="btn btn-sm btn-primary mt-1" style="font-size: 10px; padding: 2px 6px;">
                        <i class="fa fa-credit-card"></i> Pay Now
                      </a>
                    @endif
                  @else
                    <span class="badge badge-info">{{ ucfirst($booking->check_in_status) }}</span>
                  @endif
                </td>
                <td>
                  <i class="fa fa-chevron-down toggle-details" style="color: #e07632;"></i>
                </td>
              </tr>
              <!-- Expandable Details Row -->
              <tr class="booking-details-row" id="details-{{ $booking->id }}" style="display: none;">
                <td colspan="8" style="background-color: #f8f9fa; padding: 20px;">
                  <div class="row">
                    <div class="col-md-12">
                      <h6 style="color: #e07632; margin-bottom: 15px;"><i class="fa fa-info-circle"></i> Booking Details</h6>
                      <table class="table table-sm table-borderless">
                        <tr>
                          <td><strong>Guest ID:</strong></td>
                          <td>{{ $booking->guest_id ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                          <td><strong>Guest Name:</strong></td>
                          <td>{{ $booking->guest_name }}</td>
                        </tr>
                        <tr>
                          <td><strong>Email:</strong></td>
                          <td>{{ $booking->guest_email }}</td>
                        </tr>
                        <tr>
                          <td><strong>Phone:</strong></td>
                          <td>{{ $booking->country_code ?? '' }}{{ $booking->guest_phone }}</td>
                        </tr>
                        <tr>
                          <td><strong>Number of Guests:</strong></td>
                          <td>{{ $booking->number_of_guests }}</td>
                        </tr>
                        @if($booking->special_requests)
                        <tr>
                          <td><strong>Special Requests:</strong></td>
                          <td>{{ $booking->special_requests }}</td>
                        </tr>
                        @endif
                        @if($booking->extension_status)
                        <tr>
                          <td colspan="2">
                            <strong style="color: #e07632;"><i class="fa fa-calendar-plus"></i> Extension Details:</strong>
                            <div class="mt-2" style="padding: 10px; background-color: #fff; border-left: 3px solid #e07632; border-radius: 3px;">
                              @php
                                $originalCheckOut = $booking->original_check_out ? \Carbon\Carbon::parse($booking->original_check_out) : $booking->check_out;
                                $extensionNights = 0;
                                $extensionCostUsd = 0;
                                if ($booking->extension_requested_to) {
                                  $requestedCheckOut = \Carbon\Carbon::parse($booking->extension_requested_to);
                                  $extensionNights = $originalCheckOut->diffInDays($requestedCheckOut);
                                  if ($booking->room && $extensionNights > 0) {
                                    $extensionCostUsd = $booking->room->price_per_night * $extensionNights;
                                  }
                                }
                                $bookingExchangeRate = $booking->locked_exchange_rate ?? $exchangeRate ?? 2500;
                                $extensionCostTsh = $extensionCostUsd * $bookingExchangeRate;
                              @endphp
                              <table class="table table-sm table-borderless mb-0">
                                <tr>
                                  <td width="40%"><strong>Status:</strong></td>
                                  <td>
                                    @if($booking->extension_status === 'pending')
                                      <span class="badge badge-warning">Pending Review</span>
                                    @elseif($booking->extension_status === 'approved')
                                      <span class="badge badge-success">Approved</span>
                                    @elseif($booking->extension_status === 'rejected')
                                      <span class="badge badge-danger">Rejected</span>
                                    @endif
                                  </td>
                                </tr>
                                <tr>
                                  <td><strong>Original Check-out:</strong></td>
                                  <td>{{ $originalCheckOut->format('M d, Y') }}</td>
                                </tr>
                                @if($booking->extension_requested_to)
                                <tr>
                                  <td><strong>Requested Check-out:</strong></td>
                                  <td>{{ \Carbon\Carbon::parse($booking->extension_requested_to)->format('M d, Y') }}</td>
                                </tr>
                                @endif
                                @if($extensionNights > 0)
                                <tr>
                                  <td><strong>Additional Nights:</strong></td>
                                  <td><strong>{{ $extensionNights }} night(s)</strong></td>
                                </tr>
                                @endif
                                @if($booking->extension_reason)
                                <tr>
                                  <td><strong>Reason:</strong></td>
                                  <td>{{ $booking->extension_reason }}</td>
                                </tr>
                                @endif
                                @if($extensionCostUsd > 0 && $booking->extension_status === 'approved')
                                <tr style="border-top: 1px solid #ddd;">
                                  <td><strong>Extension Cost:</strong></td>
                                  <td>
                                    <strong style="color: #e07632;">
                                      ${{ number_format($extensionCostUsd, 2) }} 
                                      ({{ number_format($extensionCostTsh, 2) }} TZS)
                                    </strong>
                                  </td>
                                </tr>
                                @endif
                                @if($booking->extension_status === 'rejected' && $booking->extension_rejection_reason)
                                <tr>
                                  <td><strong>Rejection Reason:</strong></td>
                                  <td style="color: #dc3545;">{{ $booking->extension_rejection_reason }}</td>
                                </tr>
                                @endif
                              </table>
                            </div>
                          </td>
                        </tr>
                        @endif
                        @if($booking->airport_pickup_required)
                        <tr>
                          <td colspan="2">
                            <strong style="color: #e07632;"><i class="fa fa-plane"></i> Airport Pickup:</strong>
                            <ul class="list-unstyled mt-2">
                              <li><strong>Flight:</strong> {{ $booking->flight_number ?? 'N/A' }}</li>
                              <li><strong>Airline:</strong> {{ $booking->airline ?? 'N/A' }}</li>
                              <li><strong>Arrival Time:</strong> {{ $booking->arrival_time_pickup ? \Carbon\Carbon::parse($booking->arrival_time_pickup)->format('M d, Y H:i') : 'N/A' }}</li>
                              <li><strong>Passengers:</strong> {{ $booking->pickup_passengers ?? 'N/A' }}</li>
                              <li><strong>Contact:</strong> {{ $booking->pickup_contact_number ?? 'N/A' }}</li>
                            </ul>
                          </td>
                        </tr>
                        @endif
                      </table>
                    </div>
                  </div>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        
        <!-- Show Service Requests for each active booking -->
        @foreach($activeBookings as $booking)
          @php
            $bookingServices = $booking->serviceRequests()->whereIn('status', ['pending', 'approved', 'preparing', 'completed'])->with('service')->get();
          @endphp
          @if($bookingServices->count() > 0)
          <div class="mt-3 mb-4" style="background-color: #f8f9fa; padding: 15px; border-left: 4px solid #e07632;">
            <h6 style="color: #e07632; margin-bottom: 15px;">
              <i class="fa fa-list"></i> Service Requests for Booking {{ $booking->booking_reference }}
            </h6>
            
            <!-- Mobile Card View -->
            <div class="mobile-service-cards d-md-none">
              @foreach($bookingServices as $serviceRequest)
              <div class="service-card-mobile mb-2" style="background: white; border: 1px solid #ddd; border-radius: 6px; padding: 12px;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <strong style="color: #e07632; font-size: 14px;">{{ $serviceRequest->service->name }}</strong>
                  @if($serviceRequest->status === 'pending')
                    <span class="badge badge-warning">Pending</span>
                  @elseif($serviceRequest->status === 'approved')
                    <span class="badge badge-info">Approved</span>
                  @elseif($serviceRequest->status === 'preparing')
                    <span class="badge badge-primary"><i class="fa fa-spinner fa-spin"></i> Preparing</span>
                  @elseif($serviceRequest->status === 'completed')
                    <span class="badge badge-success">Completed</span>
                  @endif
                </div>
                <div class="row">
                  <div class="col-6">
                    <small class="text-muted">Quantity</small><br>
                    <strong>{{ $serviceRequest->quantity }} {{ $serviceRequest->service->unit }}</strong>
                  </div>
                  <div class="col-6">
                    <small class="text-muted">Unit Price</small><br>
                    <strong>{{ number_format($serviceRequest->unit_price_tsh, 2) }} TZS</strong><br>
                  </div>
                </div>
                <div class="mt-2 pt-2" style="border-top: 1px solid #eee;">
                  <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">Total:</small>
                    <div class="text-right">
                      <strong style="color: #e07632;">{{ number_format($serviceRequest->total_price_tsh, 2) }} TZS</strong><br>
                    </div>
                  </div>
                </div>
              </div>
              @endforeach
              <div class="service-total-mobile mt-3 pt-3" style="border-top: 2px solid #e07632;">
                <div class="d-flex justify-content-between align-items-center">
                  <strong style="color: #e07632; font-size: 16px;">Total Service Charges:</strong>
                  <div class="text-right">
                    <strong style="color: #e07632; font-size: 16px;">{{ number_format($booking->total_service_charges_tsh ?? 0, 2) }} TZS</strong><br>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Desktop Table View -->
            <div class="table-responsive d-none d-md-block">
              <table class="table table-sm table-bordered">
                <thead>
                  <tr>
                    <th>Service</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($bookingServices as $serviceRequest)
                  <tr>
                    <td>{{ $serviceRequest->service->name }}</td>
                    <td>{{ $serviceRequest->quantity }} {{ $serviceRequest->service->unit }}</td>
                    <td>
                      <div>{{ number_format($serviceRequest->unit_price_tsh, 2) }} TZS</div>
                    </td>
                    <td>
                      <div><strong>{{ number_format($serviceRequest->total_price_tsh, 2) }} TZS</strong></div>
                    </td>
                    <td>
                      @if($serviceRequest->status === 'pending')
                        <span class="badge badge-warning">Pending Approval</span>
                      @elseif($serviceRequest->status === 'approved')
                        <span class="badge badge-info">Approved</span>
                      @elseif($serviceRequest->status === 'preparing')
                        <span class="badge badge-primary"><i class="fa fa-spinner fa-spin"></i> Preparing</span>
                      @elseif($serviceRequest->status === 'completed')
                        <span class="badge badge-success">Completed</span>
                      @endif
                    </td>
                  </tr>
                  @endforeach
                </tbody>
                <tfoot>
                  <tr>
                    <td colspan="3" class="text-right"><strong>Total Service Charges:</strong></td>
                    <td colspan="2">
                      <div><strong>{{ number_format($booking->total_service_charges_tsh ?? 0, 2) }} TZS</strong></div>
                    </td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
          @endif
        @endforeach
      </div>
    </div>
  </div>
</div>
@endif

{{-- Only show pending bookings if guest has an active stay (checked in) --}}
@if($hasActiveStay && $pendingBookings->count() > 0)
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-clock-o"></i> Pending Bookings (Awaiting Payment)</h3>
      <div class="tile-body">
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>Booking Reference</th>
                <th>Room</th>
                <th>Check-in</th>
                <th>Check-out</th>
                <th>Total Price</th>
                <th>Expires At</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($pendingBookings as $booking)
              <tr>
                <td><strong>{{ $booking->booking_reference }}</strong></td>
                <td>
                  <span class="badge badge-primary">{{ $booking->room->room_type ?? 'N/A' }}</span><br>
                  <small>{{ $booking->room->room_number ?? 'N/A' }}</small>
                </td>
                <td>{{ $booking->check_in->format('M d, Y') }}</td>
                <td>{{ $booking->check_out->format('M d, Y') }}</td>
                <td><strong>${{ number_format($booking->total_price, 2) }}</strong></td>
                <td>
                  @if($booking->expires_at)
                    <span class="text-danger">
                      {{ \Carbon\Carbon::parse($booking->expires_at)->setTimezone('Africa/Nairobi')->format('M d, Y H:i') }}
                    </span>
                  @else
                    <span class="text-muted">N/A</span>
                  @endif
                </td>
                <td>
                  <a href="{{ route('payment.create', ['booking_id' => $booking->id]) }}" class="btn btn-sm btn-warning">
                    <i class="fa fa-credit-card"></i> Complete Payment
                  </a>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endif

{{-- Removed "No Bookings Yet" section - feedback button is always available in Quick Actions --}}

@endsection

@section('styles')
<style>
@keyframes blink {
  0%, 50% { border-color: rgba(255,255,255,0.8); }
  51%, 100% { border-color: transparent; }
}


#typing-text {
  display: inline-block;
}

/* Mobile Responsive Styles */
@media (max-width: 768px) {
  /* App Title - Mobile */
  .app-title h1 {
    font-size: 20px !important;
    line-height: 1.3;
  }
  
  .app-title .breadcrumb {
    font-size: 12px;
    margin-top: 5px;
  }
  
  /* Welcome Section - Mobile */
  .app-title h1#time-greeting {
    font-size: 18px !important;
  }
  
  /* Welcome Message - Mobile */
  .tile-body > div[style*="background: linear-gradient"] h2 {
    font-size: 18px !important;
    line-height: 1.3;
  }
  
  .tile-body > div[style*="background: linear-gradient"] p {
    font-size: 13px !important;
  }
  
  .tile-body > div[style*="background: linear-gradient"] .fa-hotel {
    font-size: 2rem !important;
  }
  
  /* Weather Widget - Mobile */
  .col-md-6[style*="padding: 0"] {
    padding: 0 !important;
  }
  
  .col-md-6[style*="padding: 0"] .d-flex {
    flex-direction: column;
    align-items: flex-start !important;
  }
  
  .col-md-6[style*="padding: 0"] .fa-2x {
    font-size: 1.5rem !important;
    margin-bottom: 10px;
  }
  
  .col-md-6[style*="padding: 0"] h4 {
    font-size: 24px !important;
  }
  
  .col-md-6[style*="padding: 0"] p {
    font-size: 12px !important;
  }
  
  .col-md-6[style*="padding: 0"] .btn {
    margin-top: 10px;
    width: 100%;
  }
  
  /* Check-out Alerts - Mobile */
  .alert {
    padding: 12px 15px !important;
  }
  
  .alert .d-flex {
    flex-direction: column;
    align-items: flex-start !important;
  }
  
  .alert .fa-lg {
    margin-bottom: 8px;
  }
  
  .alert .btn-sm {
    margin-top: 10px;
    width: 100%;
    font-size: 12px;
  }
  
  /* Quick Actions - Mobile */
  .tile[style*="background: linear-gradient"] .row {
    margin: 0;
  }
  
  .tile[style*="background: linear-gradient"] .col-md-3 {
    flex: 0 0 50%;
    max-width: 50%;
    margin-bottom: 10px;
  }
  
  .tile[style*="background: linear-gradient"] .btn-block {
    min-height: 70px;
    padding: 10px 5px;
  }
  
  .tile[style*="background: linear-gradient"] .fa-2x {
    font-size: 1.5rem !important;
  }
  
  .tile[style*="background: linear-gradient"] strong {
    font-size: 12px;
  }
  
  /* Current Stay Information - Mobile */
  .tile-title {
    font-size: 18px;
  }
  
  .table-borderless td {
    padding: 8px 5px;
    font-size: 13px;
  }
  
  .table-borderless strong {
    font-size: 13px;
  }
  
  .table-borderless .badge {
    font-size: 12px;
    padding: 4px 8px;
  }
  
  .input-group {
    flex-wrap: wrap;
  }
  
  .input-group .form-control {
    font-size: 13px;
  }
  
  .input-group .btn {
    font-size: 12px;
    padding: 6px 10px;
  }
  
  /* Payment Summary - Mobile */
  .col-md-6 {
    margin-bottom: 20px;
  }
  
  .table-borderless .text-right {
    text-align: left !important;
  }
  
  .table-borderless .text-right strong {
    display: block;
    margin-top: 5px;
  }
  
  /* Hotel Information - Mobile */
  .table-borderless td i {
    font-size: 14px;
  }
  
  /* Active Bookings - Mobile Card View */
  .mobile-bookings-cards {
    display: block;
  }
  
  .booking-card-mobile {
    transition: all 0.3s ease;
  }
  
  .booking-card-mobile:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }
  
  .booking-details-mobile {
    font-size: 12px;
  }
  
  /* Service Requests - Mobile Card View */
  .mobile-service-cards {
    display: block;
  }
  
  .service-card-mobile {
    transition: all 0.3s ease;
  }
  
  .service-card-mobile:hover {
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  }
  
  /* Hide desktop tables on mobile */
  .table-responsive.d-none {
    display: none !important;
  }
  
  /* Active Bookings Table - Desktop only */
  @media (min-width: 768px) {
    .table-responsive {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }
    
    .table {
      font-size: 12px;
      min-width: 800px;
    }
    
    .table th,
    .table td {
      padding: 8px 5px;
      white-space: nowrap;
    }
    
    .table .badge {
      font-size: 11px;
      padding: 3px 6px;
    }
    
    .table .btn-sm {
      font-size: 10px;
      padding: 3px 6px;
    }
    
    /* Booking Details Row - Desktop */
    .booking-details-row td {
      padding: 15px 10px !important;
    }
    
    .booking-details-row .table-sm {
      font-size: 12px;
    }
    
    /* Service Requests Table - Desktop */
    .table-sm th,
    .table-sm td {
      padding: 6px 4px;
      font-size: 11px;
    }
  }
  
  /* Buttons - Mobile */
  .btn {
    font-size: 13px;
    padding: 8px 12px;
  }
  
  .btn-lg {
    font-size: 16px;
    padding: 12px 20px;
  }
  
  /* Modals - Mobile */
  .modal-dialog {
    margin: 10px;
  }
  
  .modal-content {
    border-radius: 5px;
  }
  
  .modal-body {
    padding: 15px;
  }
  
  .form-group {
    margin-bottom: 15px;
  }
  
  .form-control {
    font-size: 14px;
    padding: 8px 12px;
  }
  
  /* Empty State - Mobile */
  .text-center .fa-5x {
    font-size: 3rem !important;
  }
  
  .text-center h3 {
    font-size: 20px;
  }
  
  .text-center p {
    font-size: 14px;
  }
}

/* Small Mobile Devices */
@media (max-width: 480px) {
  .app-title h1 {
    font-size: 18px !important;
  }
  
  .app-title h1#time-greeting {
    font-size: 16px !important;
  }
  
  .tile-body > div[style*="background: linear-gradient"] h2 {
    font-size: 16px !important;
  }
  
  .tile-body > div[style*="background: linear-gradient"] p {
    font-size: 12px !important;
  }
  
  /* Quick Actions - Small Mobile */
  .tile[style*="background: linear-gradient"] .col-md-3 {
    flex: 0 0 100%;
    max-width: 100%;
  }
  
  .tile[style*="background: linear-gradient"] .btn-block {
    min-height: 60px;
  }
  
  /* Tables - Small Mobile */
  .table {
    font-size: 11px;
    min-width: 700px;
  }
  
  .table th,
  .table td {
    padding: 6px 4px;
  }
  
  /* Payment Summary - Small Mobile */
  .table-borderless td {
    font-size: 12px;
    padding: 6px 3px;
  }
  
  /* Buttons - Small Mobile */
  .btn {
    font-size: 12px;
    padding: 6px 10px;
  }
  
  .btn-sm {
    font-size: 10px;
    padding: 4px 8px;
  }
}

/* Landscape Mobile */
@media (max-width: 768px) and (orientation: landscape) {
  .tile[style*="background: linear-gradient"] .col-md-3 {
    flex: 0 0 25%;
    max-width: 25%;
  }
}
</style>
@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places,directions"></script>
<script>
let availableServices = [];
let selectedBookingId = null;

// Load available services
function loadServices() {
    fetch('{{ route("customer.services.available") }}', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            availableServices = data.services;
            const serviceSelect = document.getElementById('service_id');
            serviceSelect.innerHTML = '<option value="">Select a service...</option>';
            
            // Group services by category
            const categories = {};
            data.services.forEach(service => {
                if (!categories[service.category]) {
                    categories[service.category] = [];
                }
                categories[service.category].push(service);
            });
            
            // Populate select with grouped services
            Object.keys(categories).sort().forEach(category => {
                const optgroup = document.createElement('optgroup');
                optgroup.label = category.charAt(0).toUpperCase() + category.slice(1).replace('_', ' ');
                categories[category].forEach(service => {
                    const option = document.createElement('option');
                    option.value = service.id;
                    
                    // Build service name with pricing info
                    let serviceText = service.name;
                    if (service.is_free_for_internal) {
                        serviceText += ' - Free for Internal Guests';
                    } else {
                        const ageGroup = service.age_group || 'both';
                        if (ageGroup === 'both' && service.child_price_tsh && service.child_price_tsh > 0) {
                            serviceText += ` - Adult: ${parseFloat(service.price_tsh).toLocaleString()} TZS / Child: ${parseFloat(service.child_price_tsh).toLocaleString()} TZS`;
                        } else if (ageGroup === 'adult' || ageGroup === 'both') {
                            serviceText += ' - ' + parseFloat(service.price_tsh).toLocaleString() + ' TZS';
                        } else if (ageGroup === 'child') {
                            serviceText += ' - ' + parseFloat(service.child_price_tsh || service.price_tsh).toLocaleString() + ' TZS';
                        }
                    }
                    
                    option.textContent = serviceText;
                    option.dataset.price = service.price_tsh || 0;
                    option.dataset.childPrice = service.child_price_tsh || 0;
                    option.dataset.unit = service.unit;
                    option.dataset.ageGroup = service.age_group || 'both';
                    option.dataset.isFree = service.is_free_for_internal ? '1' : '0';
                    optgroup.appendChild(option);
                });
                serviceSelect.appendChild(optgroup);
            });
            
            // Re-attach event listener after services are loaded
            const serviceIdElement = document.getElementById('service_id');
            if (serviceIdElement) {
                // Remove any existing listeners by cloning the element
                const newSelect = serviceIdElement.cloneNode(true);
                serviceIdElement.parentNode.replaceChild(newSelect, serviceIdElement);
                // Re-attach the change event listener
                document.getElementById('service_id').addEventListener('change', function() {
                    calculateServicePrice();
                });
            }
        }
    })
    .catch(error => {
        console.error('Error loading services:', error);
    });
}

// Handle "I don't have children" checkbox
function handleNoChildrenCheckbox() {
    const checkbox = document.getElementById('no_children_checkbox');
    const childQuantityInput = document.getElementById('service_child_quantity');
    
    if (checkbox.checked) {
        childQuantityInput.value = 0;
        childQuantityInput.disabled = true;
    } else {
        childQuantityInput.disabled = false;
    }
    calculateServicePrice();
}

// Calculate service price
function calculateServicePrice() {
    const serviceId = document.getElementById('service_id').value;
    const serviceSelect = document.getElementById('service_id');
    const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
    const adultChildGroup = document.getElementById('adultChildQuantityGroup');
    const singleQuantityGroup = document.getElementById('singleQuantityGroup');
    
    if (serviceId && selectedOption) {
        const ageGroup = selectedOption.dataset.ageGroup || 'both';
        const isFree = selectedOption.dataset.isFree === '1';
        const adultPrice = parseFloat(selectedOption.dataset.price) || 0;
        const childPrice = parseFloat(selectedOption.dataset.childPrice) || 0;
        
        // Show/hide appropriate quantity fields
        if (ageGroup === 'both' && !isFree && childPrice > 0) {
            // Show adult/child quantity fields
            adultChildGroup.style.display = 'block';
            singleQuantityGroup.style.display = 'none';
            
            // Get quantities
            const adultQuantity = parseInt(document.getElementById('service_adult_quantity').value) || 0;
            const childQuantity = parseInt(document.getElementById('service_child_quantity').value) || 0;
            
            // Calculate total
            const adultTotal = adultPrice * adultQuantity;
            const childTotal = childPrice * childQuantity;
            const total = adultTotal + childTotal;
            
            // Display price breakdown
            const priceInfo = document.getElementById('service_price_info');
            const totalPriceSpan = document.getElementById('service_total_price');
            const priceDetailsSpan = document.getElementById('service_price_details');
            
            if (isFree) {
                totalPriceSpan.textContent = '0';
                priceDetailsSpan.innerHTML = '<br><small class="text-success">This service is free for internal guests</small>';
            } else {
                totalPriceSpan.textContent = total.toLocaleString() + ' TZS';
                let breakdown = [];
                if (adultQuantity > 0) {
                    breakdown.push(`${adultQuantity} adult(s) × ${adultPrice.toLocaleString()} TZS = ${adultTotal.toLocaleString()} TZS`);
                }
                if (childQuantity > 0) {
                    breakdown.push(`${childQuantity} child(ren) × ${childPrice.toLocaleString()} TZS = ${childTotal.toLocaleString()} TZS`);
                }
                if (breakdown.length > 0) {
                    priceDetailsSpan.innerHTML = '<br><small class="text-muted">' + breakdown.join('<br>') + '</small>';
                } else {
                    priceDetailsSpan.innerHTML = '<br><small class="text-warning">Please enter at least 1 adult or child</small>';
                }
                priceDetailsSpan.style.display = 'inline';
            }
            priceInfo.style.display = 'block';
            
        } else {
            // Show single quantity field
            adultChildGroup.style.display = 'none';
            singleQuantityGroup.style.display = 'block';
            
            const quantity = parseInt(document.getElementById('service_quantity').value) || 1;
            let price = 0;
            
            if (isFree) {
                price = 0;
            } else if (ageGroup === 'child') {
                price = childPrice || adultPrice;
            } else {
                price = adultPrice;
            }
            
            const total = price * quantity;
            const priceInfo = document.getElementById('service_price_info');
            const totalPriceSpan = document.getElementById('service_total_price');
            const priceDetailsSpan = document.getElementById('service_price_details');
            
            if (isFree) {
                totalPriceSpan.textContent = '0';
                priceDetailsSpan.innerHTML = '<br><small class="text-success">This service is free for internal guests</small>';
                priceDetailsSpan.style.display = 'inline';
            } else {
                totalPriceSpan.textContent = total.toLocaleString() + ' TZS';
                priceDetailsSpan.innerHTML = `<br><small class="text-muted">(${quantity} × ${price.toLocaleString()} TZS)</small>`;
                priceDetailsSpan.style.display = 'inline';
            }
            priceInfo.style.display = 'block';
            document.getElementById('service_unit').textContent = 'Unit: ' + (selectedOption.dataset.unit || 'per item').replace('_', ' ');
        }
        
        // Load and display service-specific fields
        const service = availableServices.find(s => s.id == serviceId);
        if (service && service.required_fields && service.required_fields.length > 0) {
            renderServiceSpecificFields(service.required_fields);
        } else {
            document.getElementById('serviceSpecificFields').style.display = 'none';
        }
    } else {
        document.getElementById('service_price_info').style.display = 'none';
        document.getElementById('serviceSpecificFields').style.display = 'none';
        if (adultChildGroup) {
            adultChildGroup.style.display = 'none';
        }
        if (singleQuantityGroup) {
            singleQuantityGroup.style.display = 'block';
        }
    }
}

// Render service-specific fields
function renderServiceSpecificFields(fields) {
    const container = document.getElementById('serviceSpecificFields');
    container.innerHTML = '<hr style="margin: 20px 0; border-color: #e07632;"><h6 style="color: #e07632; margin-bottom: 15px;"><i class="fa fa-info-circle"></i> Service Details</h6>';
    
    fields.forEach(field => {
        const fieldDiv = document.createElement('div');
        fieldDiv.className = 'form-group';
        
        const label = document.createElement('label');
        label.setAttribute('for', 'field_' + field.name);
        label.textContent = field.label + (field.required ? ' *' : '');
        fieldDiv.appendChild(label);
        
        let input;
        if (field.type === 'select') {
            input = document.createElement('select');
            input.className = 'form-control';
            input.id = 'field_' + field.name;
            input.name = 'service_specific_data[' + field.name + ']';
            if (field.required) input.required = true;
            
            // Add default option
            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.textContent = 'Select ' + field.label;
            input.appendChild(defaultOption);
            
            // Add options
            if (field.options && Array.isArray(field.options)) {
                field.options.forEach(option => {
                    const opt = document.createElement('option');
                    opt.value = option.value;
                    opt.textContent = option.label;
                    input.appendChild(opt);
                });
            }
            
            // Add change listener for conditional fields (e.g., room_number)
            if (field.name === 'service_location') {
                input.addEventListener('change', function() {
                    const roomNumberFieldDiv = document.getElementById('field_room_number')?.closest('.form-group');
                    if (roomNumberFieldDiv) {
                        roomNumberFieldDiv.style.display = this.value === 'room' ? 'block' : 'none';
                        const roomNumberInput = document.getElementById('field_room_number');
                        if (roomNumberInput && this.value !== 'room') {
                            roomNumberInput.value = '';
                            roomNumberInput.required = false;
                        } else if (roomNumberInput && this.value === 'room') {
                            // Check if the field itself is required
                            const roomField = fields.find(f => f.name === 'room_number');
                            if (roomField && roomField.required) {
                                roomNumberInput.required = true;
                            }
                        }
                    }
                });
            }
        } else {
            input = document.createElement('input');
            input.type = field.type || 'text';
            input.className = 'form-control';
            input.id = 'field_' + field.name;
            input.name = 'service_specific_data[' + field.name + ']';
            if (field.placeholder) input.placeholder = field.placeholder;
            if (field.required) input.required = true;
            if (field.min !== undefined) input.min = field.min;
            if (field.default !== undefined) input.value = field.default;
            
            // Hide room_number field initially if it's conditional
            if (field.name === 'room_number') {
                fieldDiv.style.display = 'none';
            }
        }
        
        fieldDiv.appendChild(input);
        container.appendChild(fieldDiv);
    });
    
    container.style.display = 'block';
}

// Request service modal
function requestService(bookingId) {
    selectedBookingId = bookingId;
    document.getElementById('service_booking_id').value = bookingId;
    document.getElementById('service_id').value = '';
    document.getElementById('service_quantity').value = 1;
    document.getElementById('service_adult_quantity').value = 1;
    document.getElementById('service_child_quantity').value = 0;
    document.getElementById('no_children_checkbox').checked = false;
    document.getElementById('service_child_quantity').disabled = false;
    document.getElementById('guest_request').value = '';
    document.getElementById('service_price_info').style.display = 'none';
    document.getElementById('serviceSpecificFields').style.display = 'none';
    document.getElementById('adultChildQuantityGroup').style.display = 'none';
    document.getElementById('singleQuantityGroup').style.display = 'block';
    document.getElementById('serviceRequestAlert').innerHTML = '';
    
    // Ensure event listener is attached when modal is shown
    $('#serviceRequestModal').off('shown.bs.modal').on('shown.bs.modal', function() {
        const serviceIdElement = document.getElementById('service_id');
        if (serviceIdElement) {
            // Re-attach change listener
            serviceIdElement.addEventListener('change', calculateServicePrice);
        }
    });
    
    $('#serviceRequestModal').modal('show');
}

// Submit service request
function submitServiceRequest() {
    const bookingId = document.getElementById('service_booking_id').value;
    const serviceId = document.getElementById('service_id').value;
    const serviceSelect = document.getElementById('service_id');
    const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
    const ageGroup = selectedOption ? (selectedOption.dataset.ageGroup || 'both') : 'both';
    const guestRequest = document.getElementById('guest_request').value;
    const alertContainer = document.getElementById('serviceRequestAlert');
    
    if (!serviceId) {
        alertContainer.innerHTML = '<div class="alert alert-danger">Please select a service.</div>';
        return;
    }
    
    // Determine quantities based on service type
    let adultQuantity = 0;
    let childQuantity = 0;
    let quantity = 1;
    
    const adultChildGroup = document.getElementById('adultChildQuantityGroup');
    if (adultChildGroup && adultChildGroup.style.display !== 'none') {
        // Service supports both adult and child
        adultQuantity = parseInt(document.getElementById('service_adult_quantity').value) || 0;
        childQuantity = parseInt(document.getElementById('service_child_quantity').value) || 0;
        
        if (adultQuantity === 0 && childQuantity === 0) {
            alertContainer.innerHTML = '<div class="alert alert-danger">Please enter at least 1 adult or child.</div>';
            return;
        }
        quantity = adultQuantity + childQuantity; // Total quantity
    } else {
        // Single quantity service
        quantity = parseInt(document.getElementById('service_quantity').value) || 1;
    }
    
    // Collect service-specific data
    const serviceSpecificData = {};
    const serviceSpecificInputs = document.querySelectorAll('#serviceSpecificFields input, #serviceSpecificFields select');
    serviceSpecificInputs.forEach(input => {
        if (input.name && input.name.startsWith('service_specific_data[')) {
            const fieldName = input.name.match(/\[([^\]]+)\]/)[1];
            if (input.value) {
                serviceSpecificData[fieldName] = input.value;
            }
        }
    });
    
    alertContainer.innerHTML = '<div class="alert alert-info">Submitting request...</div>';
    
    fetch('{{ route("customer.services.request") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            booking_id: bookingId,
            service_id: serviceId,
            quantity: quantity,
            adult_quantity: adultQuantity,
            child_quantity: childQuantity,
            guest_request: guestRequest,
            service_specific_data: serviceSpecificData
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alertContainer.innerHTML = '<div class="alert alert-success">' + data.message + '</div>';
            swal({
                title: "Success!",
                text: data.message,
                type: "success",
                confirmButtonColor: "#28a745"
            }, function() {
                $('#serviceRequestModal').modal('hide');
                location.reload();
            });
        } else {
            alertContainer.innerHTML = '<div class="alert alert-danger">' + (data.message || 'Failed to submit request.') + '</div>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alertContainer.innerHTML = '<div class="alert alert-danger">An error occurred. Please try again.</div>';
    });
}

// Typing animation for welcome message
function typeWriter(element, text, speed = 100) {
    let i = 0;
    element.textContent = '';
    
    function type() {
        if (i < text.length) {
            element.textContent += text.charAt(i);
            i++;
            setTimeout(type, speed);
        } else {
            // Keep the cursor blinking
            setTimeout(function() {
                element.style.borderRight = '2px solid rgba(255,255,255,0.8)';
            }, 500);
        }
    }
    
    type();
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    loadServices();
    
    const serviceIdElement = document.getElementById('service_id');
    const serviceQuantityElement = document.getElementById('service_quantity');
    
    if (serviceIdElement) {
        serviceIdElement.addEventListener('change', calculateServicePrice);
    }
    if (serviceQuantityElement) {
        serviceQuantityElement.addEventListener('input', calculateServicePrice);
    }
    
    // Initialize countdown timers
    initializeCountdownTimers();
    
    // Initialize check-in/check-out button timers
    initializeCheckInOutButtonTimers();
    
    // Initialize expandable booking rows
    initializeExpandableRows();
    
    // Update time-based greeting dynamically using server timezone
    function updateTimeGreeting() {
        const greetingElement = document.getElementById('time-greeting');
        const greetingEmoji = document.getElementById('greeting-emoji');
        const greetingText = document.getElementById('greeting-text');
        const greetingName = document.getElementById('greeting-name');
        
        if (greetingElement && greetingEmoji && greetingText) {
            // Get current time in Tanzania timezone (UTC+3)
            const now = new Date();
            // Convert to Tanzania time (UTC+3)
            const tanzaniaOffset = 3 * 60; // UTC+3 in minutes
            const utc = now.getTime() + (now.getTimezoneOffset() * 60000);
            const tanzaniaTime = new Date(utc + (tanzaniaOffset * 60000));
            const hour = tanzaniaTime.getHours();
            
            let greeting, emoji;
            
            if (hour >= 5 && hour < 12) {
                greeting = 'Good Morning';
                emoji = '🌅';
            } else if (hour >= 12 && hour < 17) {
                greeting = 'Good Afternoon';
                emoji = '☀️';
            } else if (hour >= 17 && hour < 21) {
                greeting = 'Good Evening';
                emoji = '🌆';
            } else {
                greeting = 'Good Night';
                emoji = '🌙';
            }
            
            // Update only the greeting parts, keep the first name
            greetingEmoji.textContent = emoji;
            greetingText.textContent = greeting;
            
            // Ensure first name is displayed (extract from full name if needed)
            if (greetingName) {
                const fullName = '{{ $userName }}';
                const firstName = fullName.split(' ')[0];
                greetingName.textContent = firstName;
            }
        }
    }
    
    // Update greeting on page load immediately
    updateTimeGreeting();
    
    // Update greeting every 5 minutes to catch time changes
    setInterval(updateTimeGreeting, 300000); // 5 minutes
    
    // Start typing animation for welcome message
    const typingElement = document.getElementById('typing-text');
    if (typingElement) {
        // Array of captions to cycle through
        const captions = [
            "Comfort in Every Stay",
            "Your Home Away From Home",
            "Experience Luxury & Hospitality",
            "Where Memories Are Made",
            "Excellence in Every Detail"
        ];
        
        // Start with first caption
        let currentIndex = 0;
        
        function showNextCaption() {
            typingElement.style.borderRight = '2px solid rgba(255,255,255,0.8)';
            typeWriter(typingElement, captions[currentIndex], 80);
            currentIndex = (currentIndex + 1) % captions.length;
        }
        
        // Start typing animation
        showNextCaption();
        
        // Change caption every 4 seconds (after typing completes)
        setInterval(function() {
            typingElement.textContent = '';
            typingElement.style.borderRight = 'none';
            setTimeout(showNextCaption, 500);
        }, 4000);
    }
});

// Initialize check-in/check-out button timers
function initializeCheckInOutButtonTimers() {
    // Check-in buttons
    document.querySelectorAll('[data-checkin-datetime]').forEach(function(button) {
        const checkInDateTime = new Date(button.getAttribute('data-checkin-datetime'));
        updateCheckInButton(button, checkInDateTime);
        
        // Check every minute if button should be enabled
        setInterval(function() {
            updateCheckInButton(button, checkInDateTime);
        }, 60000); // Check every minute
    });
    
    // Check-out buttons (if any exist)
    document.querySelectorAll('[data-checkout-datetime]').forEach(function(button) {
        const checkOutDateTime = new Date(button.getAttribute('data-checkout-datetime'));
        updateCheckOutButton(button, checkOutDateTime);
        
        // Check every minute if button should be enabled
        setInterval(function() {
            updateCheckOutButton(button, checkOutDateTime);
        }, 60000); // Check every minute
    });
}

function updateCheckInButton(button, checkInDateTime) {
    // Date restriction removed for testing - buttons always enabled
    const canCheckIn = true; // Always allow check-in for testing
    
    if (canCheckIn && button.disabled) {
        // Enable the button
        button.disabled = false;
        button.classList.remove('btn-secondary');
        button.classList.add('btn-success');
        
        // Update button text
        const icon = button.querySelector('i');
        if (icon && icon.classList.contains('fa-sign-in')) {
            const textNode = Array.from(button.childNodes).find(node => 
                node.nodeType === 3 && node.textContent.trim().includes('Check')
            );
            if (textNode) {
                textNode.textContent = textNode.textContent.replace(/Check-in at 4:00 PM|Available at 4:00 PM/, '').trim();
            }
            // Remove the small text if it exists
            const smallText = button.querySelector('small');
            if (smallText) {
                smallText.remove();
            }
            // Update button text for quick action buttons
            const strongText = button.querySelector('strong');
            if (strongText && strongText.textContent.includes('Check-in at 4:00 PM')) {
                strongText.textContent = 'Check In Now';
            }
        }
    } else if (!canCheckIn && !button.disabled) {
        // Disable the button
        button.disabled = true;
        button.classList.remove('btn-success');
        button.classList.add('btn-secondary');
    }
}

function updateCheckOutButton(button, checkOutDateTime) {
    const now = new Date();
    const canCheckOut = now >= checkOutDateTime;
    
    if (canCheckOut && button.disabled) {
        // Enable the button
        button.disabled = false;
        button.classList.remove('btn-secondary');
        button.classList.add('btn-danger');
        
        // Update button text
        const strongText = button.querySelector('strong');
        if (strongText && strongText.textContent.includes('Check-out at 4:00 PM')) {
            strongText.textContent = 'Check Out Now';
        }
    } else if (!canCheckOut && !button.disabled) {
        // Disable the button
        button.disabled = true;
        button.classList.remove('btn-danger');
        button.classList.add('btn-secondary');
    }
}

// Countdown timers for check-in/check-out alerts
function initializeCountdownTimers() {
    // Check-in countdowns (for alerts) - Real-time updates
    document.querySelectorAll('[class*="countdown-checkin-"]:not([class*="countdown-checkin-booking-"])').forEach(function(element) {
        const targetDate = new Date(element.getAttribute('data-date'));
        // The backend already sets the check-in time, so we use it as-is
        updateCountdownRealTimeCheckIn(element, targetDate);
        setInterval(function() {
            updateCountdownRealTimeCheckIn(element, targetDate);
        }, 1000); // Update every second for real-time
    });
    
    // Check-in countdowns for Active Bookings table
    document.querySelectorAll('[class*="countdown-checkin-booking-"]').forEach(function(element) {
        const targetDate = new Date(element.getAttribute('data-date'));
        updateBookingCheckInCountdown(element, targetDate);
        setInterval(function() {
            updateBookingCheckInCountdown(element, targetDate);
        }, 60000); // Update every minute
    });
    
    // Check-out countdowns for Active Bookings table
    document.querySelectorAll('[class*="countdown-checkout-booking-"]').forEach(function(element) {
        const targetDate = new Date(element.getAttribute('data-date'));
        // Set check-out time to 4:00 PM (16:00)
        targetDate.setHours(16, 0, 0, 0);
        updateBookingCheckOutCountdown(element, targetDate);
        setInterval(function() {
            updateBookingCheckOutCountdown(element, targetDate);
        }, 60000); // Update every minute
    });
    
    // Check-out countdowns (real-time for alerts)
    document.querySelectorAll('[class*="countdown-checkout-"]:not([class*="countdown-checkout-booking-"])').forEach(function(element) {
        const targetDate = new Date(element.getAttribute('data-date'));
        // Set check-out time to 4:00 PM (16:00)
        targetDate.setHours(16, 0, 0, 0);
        updateCountdownRealTime(element, targetDate);
        setInterval(function() {
            updateCountdownRealTime(element, targetDate);
        }, 1000); // Update every second for real-time
    });
}

function updateCountdown(element, targetDate) {
    const now = new Date();
    const diff = targetDate - now;
    
    if (diff <= 0) {
        element.textContent = 'Today';
        return;
    }
    
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    
    if (days > 0) {
        element.textContent = days + ' day(s)';
    } else if (hours > 0) {
        element.textContent = hours + ' hour(s)';
    } else {
        element.textContent = minutes + ' minute(s)';
    }
}

// Real-time check-in countdown for alerts
function updateCountdownRealTimeCheckIn(element, targetDate) {
    const now = new Date();
    const diff = targetDate - now;
    
    if (diff <= 0) {
        element.textContent = 'Check-in today!';
        element.style.color = '#28a745';
        element.style.fontWeight = 'bold';
        return;
    }
    
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((diff % (1000 * 60)) / 1000);
    
    // Format the countdown with detailed time
    let countdownText = '';
    if (days > 0) {
        countdownText = days + ' day' + (days !== 1 ? 's' : '');
        if (hours > 0) {
            countdownText += ', ' + hours + ' hour' + (hours !== 1 ? 's' : '');
        }
        if (days <= 1 && minutes > 0) {
            countdownText += ', ' + minutes + ' minute' + (minutes !== 1 ? 's' : '');
        }
    } else if (hours > 0) {
        countdownText = hours + ' hour' + (hours !== 1 ? 's' : '');
        if (minutes > 0) {
            countdownText += ', ' + minutes + ' minute' + (minutes !== 1 ? 's' : '');
        }
        if (hours <= 1 && seconds > 0) {
            countdownText += ', ' + seconds + ' second' + (seconds !== 1 ? 's' : '');
        }
    } else if (minutes > 0) {
        countdownText = minutes + ' minute' + (minutes !== 1 ? 's' : '');
        if (seconds > 0) {
            countdownText += ', ' + seconds + ' second' + (seconds !== 1 ? 's' : '');
        }
    } else {
        countdownText = seconds + ' second' + (seconds !== 1 ? 's' : '');
    }
    
    element.textContent = countdownText;
    element.style.color = '';
    element.style.fontWeight = '';
}

function updateBookingCheckInCountdown(element, targetDate) {
    const now = new Date();
    const diff = targetDate - now;
    
    if (diff <= 0) {
        element.textContent = 'Check-in today!';
        element.style.color = '#28a745';
        element.style.fontWeight = 'bold';
        return;
    }
    
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const weeks = Math.floor(days / 7);
    const months = Math.floor(days / 30);
    const years = Math.floor(days / 365);
    
    let countdownText = '';
    
    if (years > 0) {
        countdownText = years + ' year' + (years !== 1 ? 's' : '');
        const remainingDays = days % 365;
        const remainingMonths = Math.floor(remainingDays / 30);
        if (remainingMonths > 0) {
            countdownText += ', ' + remainingMonths + ' month' + (remainingMonths !== 1 ? 's' : '');
        } else if (remainingDays > 0) {
            countdownText += ', ' + remainingDays + ' day' + (remainingDays !== 1 ? 's' : '');
        }
    } else if (months > 0) {
        countdownText = months + ' month' + (months !== 1 ? 's' : '');
        const remainingDays = days % 30;
        const remainingWeeks = Math.floor(remainingDays / 7);
        if (remainingWeeks > 0) {
            countdownText += ', ' + remainingWeeks + ' week' + (remainingWeeks !== 1 ? 's' : '');
        } else if (remainingDays > 0) {
            countdownText += ', ' + remainingDays + ' day' + (remainingDays !== 1 ? 's' : '');
        }
    } else if (weeks > 0) {
        countdownText = weeks + ' week' + (weeks !== 1 ? 's' : '');
        const remainingDays = days % 7;
        if (remainingDays > 0) {
            countdownText += ', ' + remainingDays + ' day' + (remainingDays !== 1 ? 's' : '');
        }
    } else {
        countdownText = days + ' day' + (days !== 1 ? 's' : '');
    }
    
    element.textContent = 'in ' + countdownText;
    element.style.color = '';
    element.style.fontWeight = '';
}

function updateBookingCheckOutCountdown(element, targetDate) {
    const now = new Date();
    const diff = targetDate - now;
    
    if (diff <= 0) {
        element.textContent = 'Check-out today!';
        element.style.color = '#dc3545';
        element.style.fontWeight = 'bold';
        return;
    }
    
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const weeks = Math.floor(days / 7);
    const months = Math.floor(days / 30);
    const years = Math.floor(days / 365);
    
    let countdownText = '';
    
    if (years > 0) {
        countdownText = years + ' year' + (years !== 1 ? 's' : '');
        const remainingDays = days % 365;
        const remainingMonths = Math.floor(remainingDays / 30);
        if (remainingMonths > 0) {
            countdownText += ', ' + remainingMonths + ' month' + (remainingMonths !== 1 ? 's' : '');
        } else if (remainingDays > 0) {
            countdownText += ', ' + remainingDays + ' day' + (remainingDays !== 1 ? 's' : '');
        }
    } else if (months > 0) {
        countdownText = months + ' month' + (months !== 1 ? 's' : '');
        const remainingDays = days % 30;
        const remainingWeeks = Math.floor(remainingDays / 7);
        if (remainingWeeks > 0) {
            countdownText += ', ' + remainingWeeks + ' week' + (remainingWeeks !== 1 ? 's' : '');
        } else if (remainingDays > 0) {
            countdownText += ', ' + remainingDays + ' day' + (remainingDays !== 1 ? 's' : '');
        }
    } else if (weeks > 0) {
        countdownText = weeks + ' week' + (weeks !== 1 ? 's' : '');
        const remainingDays = days % 7;
        if (remainingDays > 0) {
            countdownText += ', ' + remainingDays + ' day' + (remainingDays !== 1 ? 's' : '');
        }
    } else {
        countdownText = days + ' day' + (days !== 1 ? 's' : '');
    }
    
    element.textContent = 'in ' + countdownText;
    element.style.color = '';
    element.style.fontWeight = '';
}

function updateCountdownRealTime(element, targetDate) {
    const now = new Date();
    const diff = targetDate - now;
    
    if (diff <= 0) {
        element.textContent = 'Today - Check-out time!';
        element.style.color = '#dc3545';
        element.style.fontWeight = 'bold';
        return;
    }
    
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((diff % (1000 * 60)) / 1000);
    
    // Format the countdown with detailed time
    let countdownText = '';
    if (days > 0) {
        countdownText = days + ' day' + (days !== 1 ? 's' : '');
        if (hours > 0) {
            countdownText += ', ' + hours + ' hour' + (hours !== 1 ? 's' : '');
        }
        if (days <= 1 && minutes > 0) {
            countdownText += ', ' + minutes + ' minute' + (minutes !== 1 ? 's' : '');
        }
    } else if (hours > 0) {
        countdownText = hours + ' hour' + (hours !== 1 ? 's' : '');
        if (minutes > 0) {
            countdownText += ', ' + minutes + ' minute' + (minutes !== 1 ? 's' : '');
        }
        if (hours <= 1 && seconds > 0) {
            countdownText += ', ' + seconds + ' second' + (seconds !== 1 ? 's' : '');
        }
    } else if (minutes > 0) {
        countdownText = minutes + ' minute' + (minutes !== 1 ? 's' : '');
        if (seconds > 0) {
            countdownText += ', ' + seconds + ' second' + (seconds !== 1 ? 's' : '');
        }
    } else {
        countdownText = seconds + ' second' + (seconds !== 1 ? 's' : '');
    }
    
    element.textContent = countdownText;
    element.style.color = '';
    element.style.fontWeight = '';
}

// Expandable booking rows
function initializeExpandableRows() {
    document.querySelectorAll('.booking-row').forEach(function(row) {
        row.addEventListener('click', function(e) {
            // Don't toggle if clicking on a button or link
            if (e.target.tagName === 'BUTTON' || e.target.tagName === 'A' || e.target.closest('button') || e.target.closest('a')) {
                return;
            }
            
            const bookingId = this.getAttribute('data-booking-id');
            const detailsRow = document.getElementById('details-' + bookingId);
            const toggleIcon = this.querySelector('.toggle-details');
            
            if (detailsRow) {
                if (detailsRow.style.display === 'none') {
                    detailsRow.style.display = 'table-row';
                    if (toggleIcon) {
                        toggleIcon.classList.remove('fa-chevron-down');
                        toggleIcon.classList.add('fa-chevron-up');
                    }
                } else {
                    detailsRow.style.display = 'none';
                    if (toggleIcon) {
                        toggleIcon.classList.remove('fa-chevron-up');
                        toggleIcon.classList.add('fa-chevron-down');
                    }
                }
            }
        });
    });
    
    // Mobile card toggle functionality
    document.querySelectorAll('.booking-card-mobile').forEach(function(card) {
        card.addEventListener('click', function(e) {
            // Don't toggle if clicking on a button or link
            if (e.target.tagName === 'BUTTON' || e.target.tagName === 'A' || e.target.closest('button') || e.target.closest('a')) {
                return;
            }
            
            const bookingId = this.getAttribute('data-booking-id');
            const detailsDiv = document.getElementById('details-mobile-' + bookingId);
            const toggleIcon = this.querySelector('.toggle-details-mobile');
            
            if (detailsDiv) {
                if (detailsDiv.style.display === 'none') {
                    detailsDiv.style.display = 'block';
                    if (toggleIcon) {
                        toggleIcon.classList.remove('fa-chevron-down');
                        toggleIcon.classList.add('fa-chevron-up');
                    }
                } else {
                    detailsDiv.style.display = 'none';
                    if (toggleIcon) {
                        toggleIcon.classList.remove('fa-chevron-up');
                        toggleIcon.classList.add('fa-chevron-down');
                    }
                }
            }
        });
    });
}

// WiFi Password Functions
function toggleWifiPassword() {
    const passwordInput = document.getElementById('wifiPassword');
    const icon = document.getElementById('wifiPasswordIcon');
    
    if (passwordInput && icon) {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
}

function copyWifiPassword() {
    const passwordInput = document.getElementById('wifiPassword');
    if (!passwordInput) return;
    
    passwordInput.select();
    passwordInput.setSelectionRange(0, 99999); // For mobile devices
    
    try {
        document.execCommand('copy');
        swal({
            title: "Copied!",
            text: "WiFi password copied to clipboard",
            type: "success",
            timer: 2000,
            confirmButtonText: false
        });
    } catch (err) {
        // Fallback for browsers that don't support execCommand
        swal({
            title: "Copy Manually",
            text: "Please select and copy the password manually",
            type: "info"
        });
    }
}

// Report Issue Functions
function openReportIssueModal() {
    $('#reportIssueModal').modal('show');
}

function submitIssueReport(button) {
    const form = document.getElementById('reportIssueForm');
    if (!form) return;
    
    const formData = new FormData(form);
    const alertDiv = document.getElementById('issueReportAlert');
    
    // Clear previous alerts
    if (alertDiv) {
        alertDiv.innerHTML = '';
    }
    
    // Validate form
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // Get submit button
    const submitBtn = button || document.getElementById('submitIssueBtn');
    const originalText = submitBtn ? submitBtn.innerHTML : '';
    
    // Show loading state
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Submitting...';
    }
    
    fetch('{{ route("customer.issues.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => {
        // Check if response is ok (status 200-299)
        if (!response.ok) {
            // Try to parse error response
            return response.json().then(err => {
                throw { status: response.status, data: err };
            }).catch(() => {
                throw { status: response.status, message: 'Server error occurred' };
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            swal({
                title: "Issue Reported!",
                text: data.message || "Our team will address your issue shortly.",
                type: "success",
                confirmButtonColor: "#28a745"
            }, function() {
                $('#reportIssueModal').modal('hide');
                form.reset();
                // Increment notification badge immediately
                if (typeof incrementNotificationBadge === 'function') {
                    incrementNotificationBadge(1);
                }
            });
        } else {
            let errorMsg = data.message || 'An error occurred. Please try again.';
            if (data.errors) {
                const errorList = Object.values(data.errors).flat().join('<br>');
                errorMsg = errorList;
            }
            if (alertDiv) {
                alertDiv.innerHTML = '<div class="alert alert-danger">' + errorMsg + '</div>';
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        let errorMsg = 'An error occurred. Please try again.';
        
        if (error.data) {
            if (error.data.message) {
                errorMsg = error.data.message;
            } else if (error.data.errors) {
                const errorList = Object.values(error.data.errors).flat().join('<br>');
                errorMsg = errorList;
            }
        } else if (error.message) {
            errorMsg = error.message;
        }
        
        if (alertDiv) {
            alertDiv.innerHTML = '<div class="alert alert-danger">' + errorMsg + '</div>';
        }
    })
    .finally(() => {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
}

// Open service request for first active booking
function openServiceRequestForFirstBooking() {
    @if($activeBookings->count() > 0)
        const firstBooking = {{ $activeBookings->first()->id }};
        requestService(firstBooking);
    @else
        swal({
            title: "No Active Bookings",
            text: "You need to have an active booking to request a service.",
            type: "info",
            confirmButtonColor: "#17a2b8"
        });
    @endif
}

function checkInBooking(bookingId, bookingReference) {
    // Find the button to get check-in datetime
    // Date restriction removed for testing - guests can check in at any time
    
    swal({
        title: "Check In?",
        text: "Are you sure you want to check in to this booking?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Check In!",
        cancelButtonText: "Cancel",
        closeOnConfirm: false,
        showLoaderOnConfirm: true
    }, function(isConfirm) {
        if (isConfirm) {
            fetch('{{ url("/check-in") }}/' + bookingId, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    booking_reference: bookingReference,
                    email: '{{ (auth()->guard("guest")->user() ?? auth()->guard("staff")->user())->email ?? "" }}'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    swal({
                        title: "Success!",
                        text: data.message || "Check-in successful!",
                        type: "success",
                        confirmButtonColor: "#28a745"
                    }, function() {
                        location.reload();
                    });
                } else {
                    swal({
                        title: "Error!",
                        text: data.message || "Failed to check in. Please try again.",
                        type: "error",
                        confirmButtonColor: "#d33"
                    });
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
            });
        }
    });
}

// WiFi Password Functions
function toggleWifiPassword() {
    const passwordInput = document.getElementById('wifiPasswordDisplay');
    if (!passwordInput) return;
    
    const toggleIcon = document.getElementById('wifiPasswordToggle');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

function copyWifiPassword() {
    const passwordInput = document.getElementById('wifiPasswordDisplay');
    if (!passwordInput) return;
    
    passwordInput.select();
    passwordInput.setSelectionRange(0, 99999); // For mobile devices
    
    try {
        document.execCommand('copy');
        swal({
            title: "Copied!",
            text: "WiFi password copied to clipboard",
            type: "success",
            timer: 2000,
            showConfirmButton: false
        });
    } catch (err) {
        // Fallback for browsers that don't support execCommand
        if (navigator.clipboard) {
            navigator.clipboard.writeText(passwordInput.value).then(function() {
                swal({
                    title: "Copied!",
                    text: "WiFi password copied to clipboard",
                    type: "success",
                    timer: 2000,
                    showConfirmButton: false
                });
            }, function(err) {
                swal({
                    title: "Error",
                    text: "Failed to copy password. Please select and copy manually.",
                    type: "error"
                });
            });
        } else {
            swal({
                title: "Error",
                text: "Failed to copy password. Please select and copy manually.",
                type: "error"
            });
        }
    }
}

// Extension Request Functions
let currentBookingData = null;
let bookingsData = {};

// Initialize bookings data
@if($activeBookings->count() > 0)
    @foreach($activeBookings as $booking)
        bookingsData[{{ $booking->id }}] = {
            id: {{ $booking->id }},
            roomPrice: {{ $booking->room->price_per_night ?? 0 }},
            currentCheckOut: '{{ $booking->check_out->format('Y-m-d') }}'
        };
    @endforeach
@endif

function openExtensionModal(bookingId, currentCheckOut) {
    // Get booking data from bookingsData object
    currentBookingData = bookingsData[bookingId];
    
    if (!currentBookingData) {
        swal("Error", "Booking information not found.", "error");
        return;
    }
    
    document.getElementById('extension_booking_id').value = currentBookingData.id;
    const dateInput = document.getElementById('extension_requested_to');
    dateInput.value = '';
    dateInput.min = currentCheckOut;
    document.getElementById('extension_reason').value = '';
    document.getElementById('extensionCostPreview').style.display = 'none';
    document.getElementById('extensionAlert').innerHTML = '';
    
    // Remove existing event listener and add new one
    dateInput.removeEventListener('change', calculateExtensionCost);
    dateInput.removeEventListener('input', calculateExtensionCost);
    dateInput.addEventListener('change', calculateExtensionCost);
    dateInput.addEventListener('input', calculateExtensionCost);
    
    $('#extensionModal').modal('show');
}

// Set up event listener for extension date input (once on page load)
$(document).ready(function() {
    $('#extension_requested_to').on('change', function() {
        calculateExtensionCost();
    });
});

function calculateExtensionCost() {
    const newDate = document.getElementById('extension_requested_to').value;
    if (!newDate || !currentBookingData) return;
    
    const currentCheckOut = new Date(currentBookingData.currentCheckOut);
    const requestedDate = new Date(newDate);
    
    if (requestedDate <= currentCheckOut) {
        document.getElementById('extensionCostPreview').style.display = 'none';
        return;
    }
    
    const diffTime = requestedDate - currentCheckOut;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays > 0) {
        const totalCost = currentBookingData.roomPrice * diffDays;
        document.getElementById('extensionNights').textContent = diffDays;
        document.getElementById('extensionRoomPrice').textContent = currentBookingData.roomPrice.toFixed(2);
        document.getElementById('extensionTotalCost').textContent = totalCost.toFixed(2);
        document.getElementById('extensionCostPreview').style.display = 'block';
    } else {
        document.getElementById('extensionCostPreview').style.display = 'none';
    }
}

function submitExtensionRequest() {
    const form = document.getElementById('extensionForm');
    const formData = new FormData(form);
    const alertDiv = document.getElementById('extensionAlert');
    const submitBtn = event.target;
    const originalText = submitBtn.innerHTML;
    
    if (alertDiv) {
        alertDiv.innerHTML = '';
    }
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Submitting...';
    
    const bookingId = document.getElementById('extension_booking_id').value;
    
    fetch('{{ url("/customer/bookings") }}/' + bookingId + '/extend', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            extension_requested_to: document.getElementById('extension_requested_to').value,
            extension_reason: document.getElementById('extension_reason').value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            swal({
                title: "Request Submitted!",
                text: data.message || "Your extension request has been submitted. Reception will review it shortly.",
                type: "success",
                confirmButtonColor: "#28a745"
            }, function() {
                $('#extensionModal').modal('hide');
                location.reload();
            });
        } else {
            let errorMsg = data.message || 'An error occurred. Please try again.';
            if (data.errors) {
                const errorList = Object.values(data.errors).flat().join('<br>');
                errorMsg = errorList;
            }
            if (alertDiv) {
                alertDiv.innerHTML = '<div class="alert alert-danger">' + errorMsg + '</div>';
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (alertDiv) {
            alertDiv.innerHTML = '<div class="alert alert-danger">An error occurred. Please try again.</div>';
        }
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

// Decrease Request Functions
function openDecreaseModal(bookingId, checkIn, currentCheckOut) {
    // Get booking data from bookingsData object
    currentBookingData = bookingsData[bookingId];
    
    if (!currentBookingData) {
        swal("Error", "Booking information not found.", "error");
        return;
    }
    
    document.getElementById('decrease_booking_id').value = currentBookingData.id;
    const dateInput = document.getElementById('decrease_requested_to');
    dateInput.value = '';
    dateInput.min = checkIn;
    dateInput.max = currentCheckOut;
    document.getElementById('decrease_reason').value = '';
    document.getElementById('decreaseCostPreview').style.display = 'none';
    document.getElementById('decreaseAlert').innerHTML = '';
    
    // Remove existing event listeners (both native and jQuery)
    dateInput.removeEventListener('change', calculateDecreaseRefund);
    dateInput.removeEventListener('input', calculateDecreaseRefund);
    $(dateInput).off('change input');
    
    $('#decreaseModal').modal('show');
    
    // Attach event listeners after modal is shown to ensure they work
    $('#decreaseModal').off('shown.bs.modal').on('shown.bs.modal', function() {
      const dateInputEl = document.getElementById('decrease_requested_to');
      // Remove any existing listeners
      dateInputEl.removeEventListener('change', calculateDecreaseRefund);
      dateInputEl.removeEventListener('input', calculateDecreaseRefund);
      $(dateInputEl).off('change input');
      
      // Add new listeners using both methods for maximum compatibility
      dateInputEl.addEventListener('change', calculateDecreaseRefund);
      dateInputEl.addEventListener('input', calculateDecreaseRefund);
      $(dateInputEl).on('change', calculateDecreaseRefund);
      $(dateInputEl).on('input', calculateDecreaseRefund);
    });
}

function calculateDecreaseRefund() {
    const newDate = document.getElementById('decrease_requested_to').value;
    if (!newDate || !currentBookingData) {
        document.getElementById('decreaseCostPreview').style.display = 'none';
        return;
    }
    
    const currentCheckOut = new Date(currentBookingData.currentCheckOut + 'T00:00:00');
    const requestedDate = new Date(newDate + 'T00:00:00');
    
    if (requestedDate >= currentCheckOut) {
        document.getElementById('decreaseCostPreview').style.display = 'none';
        return;
    }
    
    const diffTime = currentCheckOut - requestedDate;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays > 0) {
        const totalRefund = currentBookingData.roomPrice * diffDays;
        document.getElementById('decreaseNights').textContent = diffDays;
        document.getElementById('decreaseRoomPrice').textContent = currentBookingData.roomPrice.toFixed(2);
        document.getElementById('decreaseTotalRefund').textContent = totalRefund.toFixed(2);
        document.getElementById('decreaseCostPreview').style.display = 'block';
    } else {
        document.getElementById('decreaseCostPreview').style.display = 'none';
    }
}

function submitDecreaseRequest() {
    const form = document.getElementById('decreaseForm');
    const formData = new FormData(form);
    const alertDiv = document.getElementById('decreaseAlert');
    const submitBtn = event.target;
    const originalText = submitBtn.innerHTML;
    
    if (alertDiv) {
        alertDiv.innerHTML = '';
    }
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Submitting...';
    
    const bookingId = document.getElementById('decrease_booking_id').value;
    
    fetch('{{ url("/customer/bookings") }}/' + bookingId + '/decrease', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            decrease_requested_to: document.getElementById('decrease_requested_to').value,
            decrease_reason: document.getElementById('decrease_reason').value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            swal({
                title: "Success!",
                text: data.message || "Decrease request submitted successfully!",
                type: "success",
                confirmButtonColor: "#28a745"
            }, function() {
                $('#decreaseModal').modal('hide');
                location.reload();
            });
        } else {
            swal({
                title: "Error!",
                text: data.message || "Failed to submit decrease request.",
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
            text: "An error occurred. Please try again.",
            type: "error",
            confirmButtonColor: "#d33"
        });
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

// Google Maps Directions Functions
let directionsMap = null;
let directionsService = null;
let directionsRenderer = null;
let userLocation = null;
let dashboardMap = null;
let dashboardDirectionsService = null;
let dashboardDirectionsRenderer = null;

function showDirectionsMap(bookingId) {
  // Reset status
  document.getElementById('locationStatus').innerHTML = '<i class="fa fa-spinner fa-spin"></i> Getting your location...';
  document.getElementById('routeInfo').style.display = 'none';
  document.getElementById('routeDistance').textContent = '-';
  document.getElementById('routeDuration').textContent = '-';
  
  // Reset map if it exists
  if (directionsMap) {
    directionsRenderer.setDirections({routes: []});
  }
  
  $('#directionsMapModal').modal('show');
  
  // Initialize map when modal is shown
  $('#directionsMapModal').off('shown.bs.modal').on('shown.bs.modal', function() {
    // Small delay to ensure modal is fully rendered
    setTimeout(function() {
      if (!directionsMap) {
        initializeDirectionsMap();
      }
      getUserLocationAndShowRoute();
    }, 300);
  });
}

function initializeDirectionsMap() {
  // Hotel location (Umoj Lutheran Hostel, Sokoine Road, Moshi, Tanzania)
  // Coordinates for Sokoine Road, Moshi, Kilimanjaro, Tanzania
  const hotelLocation = { lat: -3.3547, lng: 37.3406 };
  
  directionsMap = new google.maps.Map(document.getElementById('directionsMap'), {
    zoom: 13,
    center: hotelLocation,
    mapTypeControl: true,
    streetViewControl: false,
    fullscreenControl: true,
    styles: [
      {
        featureType: 'poi',
        elementType: 'labels',
        stylers: [{ visibility: 'on' }]
      }
    ]
  });
  
  // Add hotel marker
  const hotelMarker = new google.maps.Marker({
    position: hotelLocation,
    map: directionsMap,
    title: 'Umoj Lutheran Hostel',
    icon: {
      url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png'
    }
  });
  
  // Add hotel info window
  const hotelInfoWindow = new google.maps.InfoWindow({
    content: `
      <div style="padding: 5px;">
        <h6 style="margin: 0 0 5px 0; color: #940000; font-weight: bold;">Umoj Lutheran Hostel</h6>
        <p style="margin: 0; font-size: 12px;">Sokoine Road<br>Moshi, Kilimanjaro, Tanzania</p>
      </div>
    `
  });
  
  hotelMarker.addListener('click', function() {
    hotelInfoWindow.open(directionsMap, hotelMarker);
  });
  
  directionsService = new google.maps.DirectionsService();
  directionsRenderer = new google.maps.DirectionsRenderer({
    map: directionsMap,
    suppressMarkers: false,
    polylineOptions: {
      strokeColor: '#17a2b8',
      strokeWeight: 5
    }
  });
}

function getUserLocationAndShowRoute() {
  if (!navigator.geolocation) {
    document.getElementById('locationStatus').innerHTML = '<i class="fa fa-exclamation-triangle text-warning"></i> Geolocation is not supported by your browser.';
    showHotelOnly();
    return;
  }
  
  navigator.geolocation.getCurrentPosition(
    function(position) {
      userLocation = {
        lat: position.coords.latitude,
        lng: position.coords.longitude
      };
      
      // Add user location marker
      const userMarker = new google.maps.Marker({
        position: userLocation,
        map: directionsMap,
        title: 'Your Location',
        icon: {
          url: 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png'
        },
        animation: google.maps.Animation.DROP
      });
      
      // Add user info window
      const userInfoWindow = new google.maps.InfoWindow({
        content: `
          <div style="padding: 5px;">
            <h6 style="margin: 0 0 5px 0; color: #17a2b8; font-weight: bold;">Your Location</h6>
            <p style="margin: 0; font-size: 12px;">Current position</p>
          </div>
        `
      });
      
      userMarker.addListener('click', function() {
        userInfoWindow.open(directionsMap, userMarker);
      });
      
      // Calculate and display route
      calculateRoute(userLocation);
      
      // Update status
      document.getElementById('locationStatus').innerHTML = '<i class="fa fa-check-circle text-success"></i> Location found';
    },
    function(error) {
      console.error('Geolocation error:', error);
      let errorMessage = 'Unable to get your location. ';
      switch(error.code) {
        case error.PERMISSION_DENIED:
          errorMessage += 'Location access denied.';
          break;
        case error.POSITION_UNAVAILABLE:
          errorMessage += 'Location information unavailable.';
          break;
        case error.TIMEOUT:
          errorMessage += 'Location request timeout.';
          break;
        default:
          errorMessage += 'Unknown error occurred.';
          break;
      }
      
      // Check for insecure origin (HTTP on non-localhost)
      if (window.location.protocol !== 'https:' && !['localhost', '127.0.0.1'].includes(window.location.hostname)) {
        errorMessage += ' <br><small><strong>Note:</strong> Browsers require <span class="text-danger">HTTPS</span> for location access on network IPs (like yours). Try using localhost or SSL.</small>';
      }
      
      document.getElementById('locationStatus').innerHTML = '<i class="fa fa-exclamation-triangle text-warning"></i> ' + errorMessage;
      showHotelOnly();
    },
    {
      enableHighAccuracy: true,
      timeout: 10000,
      maximumAge: 0
    }
  );
}

function calculateRoute(origin) {
  const hotelLocation = { lat: -3.3547, lng: 37.3406 };
  
  const request = {
    origin: origin,
    destination: hotelLocation,
    travelMode: google.maps.TravelMode.DRIVING,
    unitSystem: google.maps.UnitSystem.METRIC,
    provideRouteAlternatives: true  // Request alternative routes
  };
  
  directionsService.route(request, function(result, status) {
    if (status === 'OK') {
      // Display alternative routes
      displayAlternativeRoutes(result.routes, origin, hotelLocation);
      
      // Show first route by default
      selectRoute(0, result.routes);
      
      // Fit map to show entire route
      const bounds = new google.maps.LatLngBounds();
      bounds.extend(origin);
      bounds.extend(hotelLocation);
      directionsMap.fitBounds(bounds);
      
      // Add padding
      directionsMap.setOptions({
        zoom: directionsMap.getZoom() > 15 ? directionsMap.getZoom() : 15
      });
    } else {
      console.error('Directions request failed:', status);
      document.getElementById('locationStatus').innerHTML = '<i class="fa fa-exclamation-triangle text-warning"></i> Could not calculate route. Please try again.';
      showHotelOnly();
    }
  });
}

function displayAlternativeRoutes(routes, origin, hotelLocation) {
  const routesContainer = document.getElementById('alternativeRoutes');
  routesContainer.innerHTML = '';
  
  // Store routes globally for selection
  window.routesData = routes;
  
  if (routes.length > 1) {
    document.getElementById('routeOptions').style.display = 'block';
    
    routes.forEach((route, index) => {
      const leg = route.legs[0];
      const routeItem = document.createElement('div');
      routeItem.className = 'list-group-item list-group-item-action';
      routeItem.style.cursor = 'pointer';
      routeItem.style.padding = '10px';
      routeItem.style.borderLeft = index === 0 ? '3px solid #28a745' : '3px solid #17a2b8';
      if (index === 0) {
        routeItem.style.backgroundColor = '#f0f9ff';
      }
      
      routeItem.innerHTML = `
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <strong>Route ${index + 1}</strong>
            ${index === 0 ? '<span class="badge badge-success ml-2">Recommended</span>' : ''}
            <br>
            <small class="text-muted">
              <i class="fa fa-road"></i> ${leg.distance.text} • 
              <i class="fa fa-clock-o"></i> ${leg.duration.text}
            </small>
          </div>
          <button class="btn btn-sm btn-outline-primary" onclick="selectRoute(${index}, window.routesData)">
            <i class="fa fa-check"></i> Select
          </button>
        </div>
      `;
      
      routeItem.addEventListener('click', function(e) {
        // Don't trigger if clicking the button
        if (e.target.tagName !== 'BUTTON' && !e.target.closest('button')) {
          selectRoute(index, routes);
        }
      });
      
      routesContainer.appendChild(routeItem);
    });
  } else {
    document.getElementById('routeOptions').style.display = 'none';
    // Still show the single route
    selectRoute(0, routes);
  }
}

function selectRoute(routeIndex, routes) {
  const selectedRoute = routes[routeIndex];
  
  // Update directions renderer
  directionsRenderer.setDirections({
    routes: [selectedRoute],
    request: {}
  });
  
  // Update route info display
  const leg = selectedRoute.legs[0];
  document.getElementById('routeDistance').textContent = leg.distance.text;
  document.getElementById('routeDuration').textContent = leg.duration.text;
  document.getElementById('routeInfo').style.display = 'block';
  
  // Update alternative routes highlighting
  const routeItems = document.querySelectorAll('#alternativeRoutes .list-group-item');
  routeItems.forEach((item, index) => {
    if (index === routeIndex) {
      item.style.borderLeft = '3px solid #28a745';
      item.style.backgroundColor = '#f0f9ff';
    } else {
      item.style.borderLeft = '3px solid #17a2b8';
      item.style.backgroundColor = '';
    }
  });
  
  // Display route steps
  displayRouteSteps(selectedRoute);
  
  // Update location status
  document.getElementById('locationStatus').innerHTML = '<i class="fa fa-check-circle text-success"></i> Route selected';
}

function displayRouteSteps(route) {
  const stepsContainer = document.getElementById('stepsList');
  stepsContainer.innerHTML = '';
  
  const leg = route.legs[0];
  
  leg.steps.forEach((step, index) => {
    const stepItem = document.createElement('div');
    stepItem.className = 'list-group-item';
    stepItem.style.padding = '8px 12px';
    stepItem.style.borderLeft = '2px solid #17a2b8';
    stepItem.style.marginBottom = '5px';
    
    // Extract instruction text (remove HTML tags)
    const instruction = step.instructions.replace(/<[^>]*>/g, '');
    
    stepItem.innerHTML = `
      <div class="d-flex align-items-start">
        <span class="badge badge-info mr-2" style="min-width: 30px; margin-top: 2px;">${index + 1}</span>
        <div class="flex-grow-1">
          <div style="font-size: 13px;">${instruction}</div>
          <small class="text-muted">
            <i class="fa fa-arrows-h"></i> ${step.distance.text}
          </small>
        </div>
      </div>
    `;
    
    stepsContainer.appendChild(stepItem);
  });
  
  document.getElementById('routeSteps').style.display = 'block';
}

function showHotelOnly() {
  const hotelLocation = { lat: -3.3547, lng: 37.3406 };
  
  // Just show hotel location
  directionsMap.setCenter(hotelLocation);
  directionsMap.setZoom(15);
  
  // Add hotel marker if not already added
  new google.maps.Marker({
    position: hotelLocation,
    map: directionsMap,
    title: 'Umoj Lutheran Hostel',
    icon: {
      url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png'
    }
  });
}

// Dashboard Map Functions (for embedded small map)
function initializeDashboardMap() {
  const hotelLocation = { lat: -3.3547, lng: 37.3406 };
  
  dashboardMap = new google.maps.Map(document.getElementById('dashboardMap'), {
    zoom: 13,
    center: hotelLocation,
    mapTypeControl: false,
    streetViewControl: false,
    fullscreenControl: false,
    zoomControl: true,
    styles: [
      {
        featureType: 'poi',
        elementType: 'labels',
        stylers: [{ visibility: 'on' }]
      }
    ]
  });
  
  // Add hotel marker
  const hotelMarker = new google.maps.Marker({
    position: hotelLocation,
    map: dashboardMap,
    title: 'Umoj Lutheran Hostel',
    icon: {
      url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png'
    }
  });
  
  dashboardDirectionsService = new google.maps.DirectionsService();
  dashboardDirectionsRenderer = new google.maps.DirectionsRenderer({
    map: dashboardMap,
    suppressMarkers: false,
    polylineOptions: {
      strokeColor: '#17a2b8',
      strokeWeight: 4
    }
  });
  
  // Get user location and show route
  getDashboardUserLocation();
}

function getDashboardUserLocation() {
  if (!navigator.geolocation) {
    document.getElementById('dashboardLocationStatus').innerHTML = '<i class="fa fa-exclamation-triangle text-warning"></i> Geolocation not supported.';
    showDashboardHotelOnly();
    return;
  }
  
  navigator.geolocation.getCurrentPosition(
    function(position) {
      const userLoc = {
        lat: position.coords.latitude,
        lng: position.coords.longitude
      };
      
      // Add user location marker
      const userMarker = new google.maps.Marker({
        position: userLoc,
        map: dashboardMap,
        title: 'Your Location',
        icon: {
          url: 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png'
        },
        animation: google.maps.Animation.DROP
      });
      
      // Calculate route
      calculateDashboardRoute(userLoc);
      
      document.getElementById('dashboardLocationStatus').innerHTML = '<i class="fa fa-check-circle text-success"></i> Location found';
    },
    function(error) {
      console.error('Geolocation error:', error);
      let errorMsg = 'Could not get location. ';
      switch(error.code) {
        case error.PERMISSION_DENIED: errorMsg += 'Permission denied.'; break;
        case error.POSITION_UNAVAILABLE: errorMsg += 'Location unavailable.'; break;
        case error.TIMEOUT: errorMsg += 'Request timed out.'; break;
        default: errorMsg += 'An unknown error occurred.'; break;
      }
      
      // Check for insecure origin (HTTP on non-localhost)
      if (window.location.protocol !== 'https:' && !['localhost', '127.0.0.1'].includes(window.location.hostname)) {
        errorMsg += ' <br><small>(Browsers require <span class="text-danger">HTTPS</span> for location on IPs like yours)</small>';
      }
      
      document.getElementById('dashboardLocationStatus').innerHTML = '<i class="fa fa-exclamation-triangle text-warning"></i> ' + errorMsg;
      showDashboardHotelOnly();
    },
    {
      enableHighAccuracy: true,
      timeout: 10000,
      maximumAge: 0
    }
  );
}

function calculateDashboardRoute(origin) {
  const hotelLocation = { lat: -3.3547, lng: 37.3406 };
  
  const request = {
    origin: origin,
    destination: hotelLocation,
    travelMode: google.maps.TravelMode.DRIVING,
    unitSystem: google.maps.UnitSystem.METRIC,
    provideRouteAlternatives: true  // Request alternative routes
  };
  
  dashboardDirectionsService.route(request, function(result, status) {
    if (status === 'OK') {
      // Use the first (recommended) route for dashboard
      const route = result.routes[0];
      dashboardDirectionsRenderer.setDirections({
        routes: [route],
        request: {}
      });
      
      const leg = route.legs[0];
      
      // Display route information
      document.getElementById('dashboardDistance').textContent = leg.distance.text;
      document.getElementById('dashboardDuration').textContent = leg.duration.text;
      document.getElementById('dashboardRouteDetails').style.display = 'block';
      
      // Show alternative routes count if available
      if (result.routes.length > 1) {
        const routeInfo = document.getElementById('dashboardRouteDetails');
        const altRoutesInfo = document.createElement('div');
        altRoutesInfo.style.marginTop = '10px';
        altRoutesInfo.style.padding = '8px';
        altRoutesInfo.style.background = '#e7f3ff';
        altRoutesInfo.style.borderRadius = '5px';
        altRoutesInfo.style.fontSize = '12px';
        altRoutesInfo.innerHTML = `<i class="fa fa-info-circle"></i> <strong>${result.routes.length} routes available.</strong> Click "Full Map" to see alternatives.`;
        routeInfo.appendChild(altRoutesInfo);
      }
      
      // Fit map to show entire route
      const bounds = new google.maps.LatLngBounds();
      bounds.extend(origin);
      bounds.extend(hotelLocation);
      dashboardMap.fitBounds(bounds);
    } else {
      console.error('Directions request failed:', status);
      showDashboardHotelOnly();
    }
  });
}

function showDashboardHotelOnly() {
  const hotelLocation = { lat: -3.3547, lng: 37.3406 };
  dashboardMap.setCenter(hotelLocation);
  dashboardMap.setZoom(15);
  
  new google.maps.Marker({
    position: hotelLocation,
    map: dashboardMap,
    title: 'Umoj Lutheran Hostel',
    icon: {
      url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png'
    }
  });
}

// Initialize dashboard map when page loads (if element exists)
$(document).ready(function() {
  if (document.getElementById('dashboardMap')) {
    const initWithRetry = (retries) => {
      if (typeof google !== 'undefined' && google.maps) {
        initializeDashboardMap();
      } else if (retries < 3) {
        setTimeout(function() { initWithRetry(retries + 1); }, 1000);
      } else {
        const statusEl = document.getElementById('dashboardLocationStatus');
        if (statusEl) {
          statusEl.innerHTML = '<i class="fa fa-exclamation-circle text-danger"></i> Google Maps failed to load. Please check your API key and internet connection.';
        }
      }
    };
    initWithRetry(0);
  }
});
</script>
@endsection

