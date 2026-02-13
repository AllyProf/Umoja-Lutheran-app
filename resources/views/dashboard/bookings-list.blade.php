@extends('dashboard.layouts.app')

@section('content')
@php
  $isReception = ($role ?? '') === 'reception';
  $bookingsRoute = $isReception ? 'reception.bookings' : 'admin.bookings.index';
  $manualCreateRoute = $isReception ? 'reception.bookings.manual.create' : 'admin.bookings.manual.create';
  $dashboardRoute = $isReception ? route('reception.dashboard') : route('admin.dashboard');
@endphp
<div class="app-title">
  <div>
    <h1><i class="fa fa-calendar-check-o"></i> Bookings Management</h1>
    <p>View and manage all hotel bookings</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ $dashboardRoute }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Bookings</a></li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn mb-3">
        <h3 class="title">
          @if(request('status') == 'expired')
            Expired Bookings
          @else
            @if(($bookingType ?? 'individual') == 'corporate')
              Company Bookings
            @else
              Individual Bookings
            @endif
          @endif
        </h3>
        <div class="btn-group">
          @if(request('status') == 'expired')
            @if(($bookingType ?? 'individual') == 'corporate')
              <a class="btn btn-primary" href="{{ route('admin.bookings.corporate.create') }}">
                <i class="fa fa-plus"></i> Create Corporate Booking
              </a>
            @else
              <a class="btn btn-primary" href="{{ route($manualCreateRoute) }}">
                <i class="fa fa-plus"></i> Create Manual Booking
              </a>
            @endif
            <a class="btn btn-info" href="{{ route($bookingsRoute, request()->only(['type'])) }}">
              <i class="fa fa-list"></i> View All Bookings
            </a>
          @else
            @if(($bookingType ?? 'individual') == 'corporate')
              <a class="btn btn-primary" href="{{ route('admin.bookings.corporate.create') }}">
                <i class="fa fa-plus"></i> Create Corporate Booking
              </a>
            @else
              <a class="btn btn-primary" href="{{ route($manualCreateRoute) }}">
                <i class="fa fa-plus"></i> Create Manual Booking
              </a>
            @endif
          @endif
        </div>
      </div>
      
      <!-- Booking Type Tabs -->
      <div class="booking-tabs-wrapper mb-4">
        <ul class="nav nav-pills nav-justified" role="tablist" style="background: #f8f9fa; padding: 8px; border-radius: 8px;">
          <li class="nav-item">
            <a class="nav-link {{ ($bookingType ?? 'individual') == 'individual' ? 'active' : '' }}" 
               href="{{ route($bookingsRoute, array_merge(request()->except(['type']), ['type' => 'individual'])) }}"
               style="
                 color: {{ ($bookingType ?? 'individual') == 'individual' ? '#fff' : '#6c757d' }}; 
                 background-color: {{ ($bookingType ?? 'individual') == 'individual' ? '#940000' : 'transparent' }};
                 border-radius: 6px;
                 padding: 10px 20px;
                 font-weight: {{ ($bookingType ?? 'individual') == 'individual' ? '600' : '400' }};
                 transition: all 0.3s ease;
               "
               onmouseover="this.style.backgroundColor='{{ ($bookingType ?? 'individual') == 'individual' ? '#940000' : '#e9ecef' }}'"
               onmouseout="this.style.backgroundColor='{{ ($bookingType ?? 'individual') == 'individual' ? '#940000' : 'transparent' }}'">
              <i class="fa fa-user"></i> Individual Bookings
              <span class="badge {{ ($bookingType ?? 'individual') == 'individual' ? 'badge-light' : 'badge-secondary' }} ml-2">{{ $stats['individual_total'] ?? 0 }}</span>
            </a>
          </li>
          {{-- <li class="nav-item">
            <a class="nav-link {{ ($bookingType ?? 'individual') == 'corporate' ? 'active' : '' }}" 
               href="{{ route($bookingsRoute, array_merge(request()->except(['type']), ['type' => 'corporate'])) }}"
               style="
                 color: {{ ($bookingType ?? 'individual') == 'corporate' ? '#fff' : '#6c757d' }}; 
                 background-color: {{ ($bookingType ?? 'individual') == 'corporate' ? '#940000' : 'transparent' }};
                 border-radius: 6px;
                 padding: 10px 20px;
                 font-weight: {{ ($bookingType ?? 'individual') == 'corporate' ? '600' : '400' }};
                 transition: all 0.3s ease;
               "
               onmouseover="this.style.backgroundColor='{{ ($bookingType ?? 'individual') == 'corporate' ? '#940000' : '#e9ecef' }}'"
               onmouseout="this.style.backgroundColor='{{ ($bookingType ?? 'individual') == 'corporate' ? '#940000' : 'transparent' }}'">
              <i class="fa fa-building"></i> Company Bookings
              <span class="badge {{ ($bookingType ?? 'individual') == 'corporate' ? 'badge-light' : 'badge-secondary' }} ml-2">{{ $stats['corporate_total'] ?? 0 }}</span>
            </a>
          </li> --}}
        </ul>
      </div>
      
      <!-- Statistics Cards -->
      @php
        $isCorporate = ($bookingType ?? 'individual') == 'corporate';
      @endphp
      
      @if($isCorporate)
      <!-- Corporate Bookings Statistics -->
      <div class="row mb-3">
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
          <div class="widget-small info coloured-icon">
            <i class="icon fa fa-building fa-2x"></i>
            <div class="info">
              <h4>Total Companies</h4>
              <p><b>{{ $stats['total'] ?? 0 }}</b></p>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
          <div class="widget-small dark coloured-icon">
            <i class="icon fa fa-check-circle fa-2x"></i>
            <div class="info">
              <h4 style="color: #000;">Confirmed</h4>
              <p style="color: #000;"><b>{{ $stats['confirmed'] ?? 0 }}</b></p>
              <small style="color: #000;">Companies</small>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
          <div class="widget-small primary coloured-icon">
            <i class="icon fa fa-sign-in fa-2x"></i>
            <div class="info">
              <h4>Checked In</h4>
              <p><b>{{ $stats['checked_in'] ?? 0 }}</b></p>
              <small>Companies</small>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
          <div class="widget-small warning coloured-icon">
            <i class="icon fa fa-sign-out fa-2x"></i>
            <div class="info">
              <h4>Checked Out</h4>
              <p><b>{{ $stats['checked_out'] ?? 0 }}</b></p>
              <small>Companies</small>
            </div>
          </div>
        </div>
      </div>
      @else
      <!-- Individual Bookings Statistics -->
      <div class="row mb-3">
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
          <div class="widget-small info coloured-icon">
            <i class="icon fa fa-list fa-2x"></i>
            <div class="info">
              <h4>Total Bookings</h4>
              <p><b>{{ $stats['total'] ?? 0 }}</b></p>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
          <div class="widget-small {{ ($stats['confirmed'] ?? 0) > 0 ? 'success' : 'dark' }} coloured-icon">
            <i class="icon fa fa-check-circle fa-2x"></i>
            <div class="info">
              <h4 style="color: #000;">Confirmed</h4>
              <p style="color: #000;"><b>{{ $stats['confirmed'] ?? 0 }}</b></p>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
          <div class="widget-small primary coloured-icon">
            <i class="icon fa fa-sign-in fa-2x"></i>
            <div class="info">
              <h4>Checked In</h4>
              <p><b>{{ $stats['checked_in'] ?? 0 }}</b></p>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
          <div class="widget-small warning coloured-icon">
            <i class="icon fa fa-sign-out fa-2x"></i>
            <div class="info">
              <h4>Checked Out</h4>
              <p><b>{{ $stats['checked_out'] ?? 0 }}</b></p>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Additional stats for individual bookings -->
      <div class="row mb-3">
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
          <div class="widget-small primary coloured-icon">
            <i class="icon fa fa-check-circle fa-2x"></i>
            <div class="info">
              <h4>Completed</h4>
              <p><b>{{ $stats['completed'] ?? 0 }}</b></p>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
          <div class="widget-small danger coloured-icon">
            <i class="icon fa fa-times fa-2x"></i>
            <div class="info">
              <h4>Cancelled</h4>
              <p><b>{{ $stats['cancelled'] ?? 0 }}</b></p>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
          <div class="widget-small danger coloured-icon">
            <i class="icon fa fa-exclamation-triangle fa-2x"></i>
            <div class="info">
              <h4>Expired</h4>
              <p><b>{{ $stats['expired'] ?? 0 }}</b></p>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
          <div class="widget-small warning coloured-icon">
            <i class="icon fa fa-clock-o fa-2x"></i>
            <div class="info">
              <h4>Pending</h4>
              <p><b>{{ $stats['pending'] ?? 0 }}</b></p>
            </div>
          </div>
        </div>
      </div>
      @endif
      
      <!-- Filters -->
      <div class="row mb-3">
        <div class="col-md-12">
          <div class="tile">
            <div class="tile-body">
              <div class="row">
                <div class="col-md-2">
                  <div class="form-group">
                    <label for="statusFilter"><strong>Status:</strong></label>
                    <select id="statusFilter" class="form-control" onchange="filterBookings()">
                      <option value="all" {{ request('status') == 'all' || !request('status') ? 'selected' : '' }}>All Status</option>
                      <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                      <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                      <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                      <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                      <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label for="checkInStatusFilter"><strong>Check-in Status:</strong></label>
                    <select id="checkInStatusFilter" class="form-control" onchange="filterBookings()">
                      <option value="all">All Check-in</option>
                      <option value="pending">Pending</option>
                      <option value="checked_in">Checked In</option>
                      <option value="checked_out">Checked Out</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label for="paymentStatusFilter"><strong>Payment Status:</strong></label>
                    <select id="paymentStatusFilter" class="form-control" onchange="filterBookings()">
                      <option value="all">All Payment</option>
                      <option value="pending">Pending</option>
                      <option value="paid">Paid</option>
                      <option value="failed">Failed</option>
                      <option value="cancelled">Cancelled</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="searchInput"><strong>Search:</strong></label>
                    <input type="text" id="searchInput" class="form-control" 
                           placeholder="Search by name, email, or reference..." 
                           onkeyup="filterBookings()">
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label>&nbsp;</label>
                    <button type="button" class="btn btn-secondary btn-block" onclick="resetFilters()">
                      <i class="fa fa-refresh"></i> Reset
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      @if($bookings->count() > 0)
      <!-- Desktop Table View -->
      <div class="table-responsive">
        <table class="table table-hover table-bordered" id="bookingsTable">
          <thead>
            <tr>
              <th>Reference</th>
              @if(($bookingType ?? 'individual') == 'corporate')
                <th>Company</th>
              @endif
              <th>Guest</th>
              <th>Room</th>
              <th>Check-in</th>
              <th>Check-out</th>
              <th>Nights</th>
              <th>Total Price</th>
              @if(($bookingType ?? 'individual') == 'corporate')
                <th>Payment Responsibility</th>
              @endif
              <th>Status</th>
              <th>Payment</th>
              <th>Check-in</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @if(($bookingType ?? 'individual') == 'corporate')
              @foreach($bookings as $group)
                @php
                  $company = $group['company'] ?? null;
                  $companyBookings = $group['bookings'] ?? collect();
                  $firstBooking = $group['first_booking'] ?? $companyBookings->first();
                  $totalGuests = $companyBookings->count();
                  $totalPrice = $companyBookings->sum(function($b) {
                      $svc = $b->serviceRequests ? $b->serviceRequests->whereIn('status', ['approved', 'completed']) : collect();
                      $svcTsh = $svc->sum('total_price_tsh');
                      return (float)$b->total_price + ($b->payment_responsibility !== 'self' ? $svcTsh : 0);
                  });
                  $totalPaid = $companyBookings->sum('amount_paid');
                  $totalNights = $firstBooking ? $firstBooking->check_in->diffInDays($firstBooking->check_out) : 0;
                @endphp
                <tr class="booking-row corporate-booking-group"
                    data-status="{{ $firstBooking->status ?? 'pending' }}"
                    data-check-in-status="{{ $firstBooking->check_in_status ?? 'pending' }}"
                    data-payment-status="{{ $firstBooking->payment_status ?? 'pending' }}"
                    data-company-id="{{ $company->id ?? '' }}"
                    style="background-color: #f8f9fa;">
                  <td>
                    <strong>{{ $firstBooking->booking_reference ?? 'N/A' }}</strong>
                    <br><small class="text-muted">{{ $firstBooking->created_at->format('M d, Y') ?? 'N/A' }}</small>
                    <br><small class="badge badge-info">{{ $totalGuests }} guest{{ $totalGuests > 1 ? 's' : '' }}</small>
                  </td>
                  <td>
                    @if($company)
                      <strong class="text-primary">
                        <i class="fa fa-building"></i> {{ $company->name }}
                      </strong>
                      <br><small class="text-muted"><i class="fa fa-envelope"></i> {{ $company->email }}</small>
                      @if($company->phone)
                        <br><small class="text-muted"><i class="fa fa-phone"></i> {{ $company->phone }}</small>
                      @endif
                    @else
                      <span class="text-muted">N/A</span>
                    @endif
                  </td>
                  <td>
                    <strong class="text-primary">{{ $totalGuests }} guest{{ $totalGuests > 1 ? 's' : '' }}</strong>
                  </td>
                  <td>
                    <span class="badge badge-info">{{ $companyBookings->unique('room_id')->count() }} room{{ $companyBookings->unique('room_id')->count() > 1 ? 's' : '' }}</span>
                  </td>
                  <td>
                    {{ $firstBooking->check_in->format('M d, Y') }}
                    @if($firstBooking->expires_at && $firstBooking->status == 'pending' && $firstBooking->payment_status == 'pending')
                      @php
                        $expiresAt = \Carbon\Carbon::parse($firstBooking->expires_at);
                        $now = \Carbon\Carbon::now();
                        $secondsRemaining = $now->diffInSeconds($expiresAt, false);
                      @endphp
                      @if($secondsRemaining > 0)
                        <br><small class="text-danger" id="countdown-{{ $firstBooking->id }}">
                          <i class="fa fa-clock-o"></i> Expires in: <span class="countdown-timer" data-expires="{{ $expiresAt->timestamp * 1000 }}">Calculating...</span>
                        </small>
                      @else
                        <br><small class="text-danger"><i class="fa fa-times-circle"></i> Expired</small>
                      @endif
                    @endif
                  </td>
                  <td>
                    {{ $firstBooking->check_out->format('M d, Y') }}
                    @php
                      $today = \Carbon\Carbon::today();
                      $checkOut = \Carbon\Carbon::parse($firstBooking->check_out);
                      $daysRemaining = $today->diffInDays($checkOut, false);
                      $weeksRemaining = floor($daysRemaining / 7);
                    @endphp
                    @php
                      $allCheckedOut = $companyBookings->every(function($b) { return ($b->check_in_status ?? 'pending') == 'checked_out'; });
                    @endphp
                    @if($allCheckedOut)
                      <br><small class="text-success"><i class="fa fa-check-circle"></i> All Checked Out</small>
                    @elseif($daysRemaining > 0)
                      <br>
                      @if($weeksRemaining > 0)
                        <small class="text-info">
                          <i class="fa fa-clock-o"></i> {{ $weeksRemaining }} week{{ $weeksRemaining > 1 ? 's' : '' }} remaining
                        </small>
                      @else
                        <small class="text-info">
                          <i class="fa fa-clock-o"></i> {{ $daysRemaining }} day{{ $daysRemaining > 1 ? 's' : '' }} remaining
                        </small>
                      @endif
                    @elseif($daysRemaining == 0)
                      @php
                        $checkoutTime = $firstBooking->room->checkout_time ?? 
                                       \App\Models\HotelSetting::getValue('default_checkout_time') ?? 
                                       \App\Models\HotelSetting::getValue('default_room_checkout_time') ?? 
                                       '12:00';
                        $timeParts = explode(':', $checkoutTime);
                        $hour = (int)$timeParts[0];
                        $minute = $timeParts[1] ?? '00';
                        $ampm = $hour >= 12 ? 'PM' : 'AM';
                        $hour12 = $hour > 12 ? $hour - 12 : ($hour == 0 ? 12 : $hour);
                        $formattedTime = $hour12 . ':' . $minute . ' ' . $ampm;
                      @endphp
                      <br><small class="text-warning"><i class="fa fa-exclamation-triangle"></i> Check-out today at {{ $formattedTime }}</small>
                    @else
                      <br><small class="text-danger"><i class="fa fa-times-circle"></i> {{ abs($daysRemaining) }} day{{ abs($daysRemaining) > 1 ? 's' : '' }} overdue</small>
                    @endif
                  </td>
                  <td>
                    {{ $totalNights }} night(s)
                    <br><small class="text-muted">{{ $totalGuests }} guest{{ $totalGuests > 1 ? 's' : '' }}</small>
                  </td>
                  <td>
                    <strong>{{ number_format($totalPrice, 0) }} TZS</strong>
                    <br><small class="text-muted">Total for all guests</small>
                    @if($totalPaid > 0)
                      <br><small class="text-success">Paid: {{ number_format($totalPaid, 0) }} TZS</small>
                    @endif
                  </td>
                  <td>
                    @php
                      $hasCompanyPays = $companyBookings->where('payment_responsibility', 'company')->count() > 0;
                      $hasSelfPays = $companyBookings->where('payment_responsibility', 'self')->count() > 0;
                    @endphp
                    @if($hasCompanyPays && $hasSelfPays)
                      <span class="badge badge-secondary">Mixed</span>
                      <br><small class="text-muted">Some company, some self</small>
                    @elseif($hasCompanyPays)
                      <span class="badge badge-info">
                        <i class="fa fa-building"></i> Company Pays
                      </span>
                      <br><small class="text-muted">(Room charges)</small>
                    @elseif($hasSelfPays)
                      <span class="badge badge-warning">
                        <i class="fa fa-user"></i> Self-Paid
                      </span>
                      <br><small class="text-muted">(Services only)</small>
                    @else
                      <span class="badge badge-secondary">N/A</span>
                    @endif
                  </td>
                  <td>
                    @php
                      $allCompleted = $companyBookings->every(function($b) { return $b->status == 'completed' || ($b->check_in_status == 'checked_out'); });
                      $allConfirmed = $companyBookings->every(function($b) { return $b->status == 'confirmed' && $b->check_in_status != 'checked_out'; });
                      $allPending = $companyBookings->every(function($b) { return $b->status == 'pending'; });
                      $allCancelled = $companyBookings->every(function($b) { return $b->status == 'cancelled'; });
                    @endphp
                    @if($allCompleted)
                      <span class="badge badge-info"><i class="fa fa-flag-checkered"></i> Completed</span>
                    @elseif($allConfirmed)
                      <span class="badge badge-success">Confirmed</span>
                    @elseif($allPending)
                      <span class="badge badge-warning">Pending</span>
                    @elseif($allCancelled)
                      <span class="badge badge-danger">Cancelled</span>
                    @else
                      <span class="badge badge-secondary">Mixed</span>
                    @endif
                  </td>
                  <td>
                    @php
                      $allPaid = $companyBookings->every(function($b) { return $b->payment_status == 'paid'; });
                      $allPartial = $companyBookings->every(function($b) { return $b->payment_status == 'partial'; });
                      $allPendingPay = $companyBookings->every(function($b) { return $b->payment_status == 'pending'; });
                      $totalPaymentPercentage = $totalPrice > 0 ? ($totalPaid / $totalPrice) * 100 : 0;
                    @endphp
                    @if($allPaid)
                      <span class="badge badge-success">Paid</span>
                    @elseif($allPartial || $totalPaymentPercentage > 0)
                      <span class="badge badge-info">Partial</span>
                      <br><small class="text-muted">{{ number_format($totalPaymentPercentage, 0) }}% paid</small>
                      <br><small class="text-muted">{{ $firstBooking->payment_method ?? 'N/A' }}</small>
                    @elseif($allPendingPay)
                      <span class="badge badge-warning">Pending</span>
                    @else
                      <span class="badge badge-secondary">Mixed</span>
                    @endif
                  </td>
                  <td>
                    @php
                      $allCheckedIn = $companyBookings->every(function($b) { return ($b->check_in_status ?? 'pending') == 'checked_in'; });
                      $allCheckedOut = $companyBookings->every(function($b) { return ($b->check_in_status ?? 'pending') == 'checked_out'; });
                      $allPendingCheckIn = $companyBookings->every(function($b) { return ($b->check_in_status ?? 'pending') == 'pending'; });
                    @endphp
                    @if($allCheckedOut)
                      <span class="badge badge-success"><i class="fa fa-check-circle"></i> Checked Out</span>
                    @elseif($allCheckedIn)
                      <span class="badge badge-info"><i class="fa fa-sign-in"></i> Checked In</span>
                    @elseif($allPendingCheckIn)
                      <span class="badge badge-warning"><i class="fa fa-clock-o"></i> Pending</span>
                    @else
                      <span class="badge badge-secondary">Mixed</span>
                    @endif
                  </td>
                  <td>
                    <button class="btn btn-sm btn-info" onclick="viewCompanyBookingGroup({{ $company->id ?? 'null' }}, {{ $firstBooking->id }})" title="View All Guests">
                      <i class="fa fa-eye"></i> View More
                    </button>
                    @if(in_array($firstBooking->payment_status, ['paid', 'partial']) || $firstBooking->status == 'confirmed')
                      @php
                        $firstPaidBooking = $companyBookings->where('payment_status', '!=', 'pending')->first();
                      @endphp
                      @if($firstPaidBooking)
                        <a href="{{ route('payment.receipt.download', $firstPaidBooking) }}?download=1" class="btn btn-sm btn-success mt-1" target="_blank" title="Download Receipt">
                          <i class="fa fa-download"></i>
                        </a>
                      @endif
                    @endif
                  </td>
                </tr>
              @endforeach
            @endif
            @if(($bookingType ?? 'individual') == 'individual')
              @foreach($bookings as $booking)
              <tr class="booking-row"
                  data-status="{{ $booking->status }}"
                  data-check-in-status="{{ $booking->check_in_status ?? 'pending' }}"
                  data-payment-status="{{ $booking->payment_status ?? 'pending' }}"
                  data-booking-ref="{{ strtolower($booking->booking_reference) }}"
                  data-guest-name="{{ strtolower($booking->guest_name) }}"
                  data-guest-email="{{ strtolower($booking->guest_email) }}">
                <td>
                  <strong>{{ $booking->booking_reference }}</strong>
                  <br><small class="text-muted">{{ $booking->created_at->format('M d, Y') }}</small>
                </td>
                <td>
                  <strong>{{ $booking->guest_name }}</strong>
                  <br><small class="text-muted">{{ $booking->guest_email }}</small>
                  <br><small class="text-muted">{{ $booking->guest_phone }}</small>
                </td>
                <td>
                  <span class="badge badge-primary">{{ $booking->room->room_type ?? 'N/A' }}</span>
                  <br><small class="text-muted">{{ $booking->room->room_number ?? 'N/A' }}</small>
                </td>
                <td>
                  {{ $booking->check_in->format('M d, Y') }}
                @if($booking->expires_at && $booking->status == 'pending' && $booking->payment_status == 'pending')
                  @php
                    $expiresAt = \Carbon\Carbon::parse($booking->expires_at);
                    $now = \Carbon\Carbon::now();
                    $secondsRemaining = $now->diffInSeconds($expiresAt, false);
                  @endphp
                  @if($secondsRemaining > 0)
                    <br><small class="text-danger" id="countdown-{{ $booking->id }}">
                      <i class="fa fa-clock-o"></i> Expires in: <span class="countdown-timer" data-expires="{{ $expiresAt->timestamp * 1000 }}">Calculating...</span>
                    </small>
                  @else
                    <br><small class="text-danger"><i class="fa fa-times-circle"></i> Expired</small>
                  @endif
                @endif
              </td>
              <td>
                {{ $booking->check_out->format('M d, Y') }}
                @php
                  $today = \Carbon\Carbon::today();
                  $checkOut = \Carbon\Carbon::parse($booking->check_out);
                  $daysRemaining = $today->diffInDays($checkOut, false);
                  $weeksRemaining = floor($daysRemaining / 7);
                  
                  // Check if booking was extended (only show if check_out > original_check_out)
                  $isExtended = false;
                  $isDecreased = false;
                  $extendedNights = 0;
                  $decreasedNights = 0;
                  if ($booking->original_check_out) {
                    $originalCheckOut = \Carbon\Carbon::parse($booking->original_check_out);
                    $currentCheckOut = \Carbon\Carbon::parse($booking->check_out);
                    if ($currentCheckOut->gt($originalCheckOut)) {
                      // Extended
                      $isExtended = true;
                      $extendedNights = $originalCheckOut->diffInDays($currentCheckOut);
                    } elseif ($currentCheckOut->lt($originalCheckOut)) {
                      // Decreased
                      $isDecreased = true;
                      $decreasedNights = $currentCheckOut->diffInDays($originalCheckOut);
                    }
                  }
                @endphp
                @if($isExtended && $extendedNights > 0)
                  <br><span class="badge badge-info" title="Originally scheduled to check out on {{ \Carbon\Carbon::parse($booking->original_check_out)->format('M d, Y') }}">
                    <i class="fa fa-calendar-plus-o"></i> Extended by {{ $extendedNights }} night{{ $extendedNights > 1 ? 's' : '' }}
                  </span>
                @elseif($isDecreased && $decreasedNights > 0)
                  <br><span class="badge badge-warning" title="Originally scheduled to check out on {{ \Carbon\Carbon::parse($booking->original_check_out)->format('M d, Y') }}">
                    <i class="fa fa-calendar-minus-o"></i> Decreased by {{ $decreasedNights }} night{{ $decreasedNights > 1 ? 's' : '' }}
                  </span>
                @endif
                @if($booking->extension_status === 'pending' && $booking->extension_requested_to)
                  @php
                    $requestedCheckOut = \Carbon\Carbon::parse($booking->extension_requested_to);
                    $pendingExtensionNights = $checkOut->diffInDays($requestedCheckOut);
                  @endphp
                  @if($pendingExtensionNights > 0)
                    <br><span class="badge badge-warning" title="Guest has requested extension to {{ $requestedCheckOut->format('M d, Y') }}">
                      <i class="fa fa-clock-o"></i> Extension Pending (+{{ $pendingExtensionNights }} night{{ $pendingExtensionNights > 1 ? 's' : '' }})
                    </span>
                    <br><small class="text-muted">Requested: {{ $requestedCheckOut->format('M d, Y') }}</small>
                  @endif
                @endif
                @if(($booking->check_in_status ?? 'pending') === 'checked_out')
                  {{-- Once guest is checked out, do NOT show remaining/overdue time based on dates --}}
                  <br><small class="text-success">
                    <i class="fa fa-check-circle"></i> Checked out
                  </small>
                @else
                  @if($daysRemaining > 0)
                    <br>
                    @if($weeksRemaining > 0)
                      <small class="text-info">
                        <i class="fa fa-clock-o"></i> {{ $weeksRemaining }} week{{ $weeksRemaining > 1 ? 's' : '' }} remaining
                      </small>
                    @else
                      <small class="text-info">
                        <i class="fa fa-clock-o"></i> {{ $daysRemaining }} day{{ $daysRemaining > 1 ? 's' : '' }} remaining
                      </small>
                    @endif
                  @elseif($daysRemaining == 0)
                    @php
                      // Get checkout time from room, or fall back to hotel settings, or default to 12:00
                      $checkoutTime = $booking->room->checkout_time ?? 
                                     \App\Models\HotelSetting::getValue('default_checkout_time') ?? 
                                     \App\Models\HotelSetting::getValue('default_room_checkout_time') ?? 
                                     '12:00';
                      // Format time for display (convert 24h to 12h format)
                      $timeParts = explode(':', $checkoutTime);
                      $hour = (int)$timeParts[0];
                      $minute = $timeParts[1] ?? '00';
                      $ampm = $hour >= 12 ? 'PM' : 'AM';
                      $hour12 = $hour > 12 ? $hour - 12 : ($hour == 0 ? 12 : $hour);
                      $formattedTime = $hour12 . ':' . $minute . ' ' . $ampm;
                    @endphp
                    <br><small class="text-warning"><i class="fa fa-exclamation-triangle"></i> Check-out today at {{ $formattedTime }}</small>
                  @else
                    <br><small class="text-danger"><i class="fa fa-times-circle"></i> {{ abs($daysRemaining) }} day{{ abs($daysRemaining) > 1 ? 's' : '' }} overdue</small>
                  @endif
                @endif
              </td>
              <td>
                {{ $booking->check_in->diffInDays($booking->check_out) }} nights
                @if(($isExtended || $isDecreased) && $booking->original_check_out)
                  @php
                    $originalNights = $booking->check_in->diffInDays(\Carbon\Carbon::parse($booking->original_check_out));
                  @endphp
                  <br><small class="text-muted">(Original: {{ $originalNights }} nights)</small>
                @endif
              </td>
              <td>
                @php
                    $svc = $booking->serviceRequests ? $booking->serviceRequests->whereIn('status', ['approved', 'completed']) : collect();
                    $svcTsh = $svc->sum('total_price_tsh');
                    $totalBill = (float)$booking->total_price + $svcTsh;
                @endphp
                <strong>{{ number_format($totalBill, 0) }} TZS</strong>
                @if($svcTsh > 0)
                  <br><small class="text-muted">Room: {{ number_format($booking->total_price, 0) }} TZS</small>
                  <br><small class="text-muted">Services: {{ number_format($svcTsh, 0) }} TZS</small>
                @endif
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
                @if($isExtended && $booking->room && $extendedNights > 0)
                  @php
                    $extensionCost = $booking->room->price_per_night * $extendedNights;
                    $originalPrice = $booking->total_price - $extensionCost;
                  @endphp
                  <br><small class="text-info">
                    <i class="fa fa-calendar-plus-o"></i> +{{ number_format($extensionCost, 0) }} TZS (extension)
                  </small>
                  <br><small class="text-muted">Original: {{ number_format($originalPrice, 0) }} TZS</small>
                @elseif($isDecreased && $booking->room && $decreasedNights > 0)
                  @php
                    $decreaseRefund = $booking->room->price_per_night * $decreasedNights;
                    $originalPrice = $booking->total_price + $decreaseRefund;
                  @endphp
                  <br><small class="text-warning">
                    <i class="fa fa-calendar-minus-o"></i> -{{ number_format($decreaseRefund, 0) }} TZS (decrease)
                  </small>
                  <br><small class="text-muted">Original: {{ number_format($originalPrice, 0) }} TZS</small>
                @endif
                @if($booking->extension_status === 'pending' && $booking->extension_requested_to && $booking->room)
                  @php
                    $requestedCheckOut = \Carbon\Carbon::parse($booking->extension_requested_to);
                    $pendingExtensionNights = $checkOut->diffInDays($requestedCheckOut);
                    $pendingExtensionCost = $booking->room->price_per_night * $pendingExtensionNights;
                  @endphp
                  @if($pendingExtensionNights > 0)
                    <br><small class="text-warning" style="display: block; margin-top: 5px;">
                      <i class="fa fa-clock-o"></i> +{{ number_format($pendingExtensionCost, 0) }} TZS (pending extension)
                    </small>
                    <br><small class="text-muted">If approved: {{ number_format($booking->total_price + $pendingExtensionCost, 0) }} TZS</small>
                  @endif
                @endif
                @if($booking->payment_status == 'paid')
                  <br><small class="text-success"><i class="fa fa-check"></i> Paid</small>
                @endif
              </td>
              @if(($bookingType ?? 'individual') == 'corporate')
                <td>
                  @if($booking->payment_responsibility == 'company')
                    <span class="badge badge-info">
                      <i class="fa fa-building"></i> Company Pays
                    </span>
                    <br><small class="text-muted">(Room charges)</small>
                  @elseif($booking->payment_responsibility == 'self')
                    <span class="badge badge-warning">
                      <i class="fa fa-user"></i> Self-Paid
                    </span>
                    <br><small class="text-muted">(Services only)</small>
                  @else
                    <span class="badge badge-secondary">Mixed</span>
                  @endif
                </td>
              @endif
              <td>
                @if($booking->status == 'pending')
                  <span class="badge badge-warning">Pending</span>
                @elseif($booking->status == 'confirmed')
                  <span class="badge badge-success">Confirmed</span>
                @elseif($booking->status == 'cancelled')
                  <span class="badge badge-danger">Cancelled</span>
                @elseif($booking->status == 'completed')
                  <span class="badge badge-info">Completed</span>
                @endif
              </td>
              <td>
                @if($booking->payment_status == 'pending')
                  <span class="badge badge-warning">Pending</span>
                @elseif($booking->payment_status == 'paid')
                  <span class="badge badge-success">Paid</span>
                @elseif($booking->payment_status == 'partial')
                  <span class="badge badge-info">Partial</span>
                  @if($booking->payment_percentage)
                    <br><small class="text-muted">{{ number_format($booking->payment_percentage, 0) }}% paid</small>
                  @endif
                @elseif($booking->payment_status == 'failed')
                  <span class="badge badge-danger">Failed</span>
                @elseif($booking->payment_status == 'cancelled')
                  <span class="badge badge-secondary">Cancelled</span>
                @endif
                @if($booking->payment_method)
                  <br><small class="text-muted">{{ ucfirst($booking->payment_method) }}</small>
                @endif
              </td>
              <td>
                @if($booking->check_in_status == 'pending')
                  <span class="badge badge-warning">Pending</span>
                @elseif($booking->check_in_status == 'checked_in')
                  <span class="badge badge-success">Checked In</span>
                  <br><small class="text-muted">{{ $booking->checked_in_at ? \Carbon\Carbon::parse($booking->checked_in_at)->format('M d, Y H:i') : '' }}</small>
                @elseif($booking->check_in_status == 'checked_out')
                  <span class="badge badge-info">Checked Out</span>
                  <br><small class="text-muted">{{ $booking->checked_out_at ? \Carbon\Carbon::parse($booking->checked_out_at)->format('M d, Y H:i') : '' }}</small>
                @endif
              </td>
              <td>
                <div class="btn-group" role="group">
                  @php
                    $isExpired = false;
                    if ($booking->expires_at) {
                      $isExpired = \Carbon\Carbon::parse($booking->expires_at)->isPast();
                    }
                    $showReminders = $booking->status == 'pending' && 
                                    $booking->payment_status == 'pending' && 
                                    request('status') != 'expired' && 
                                    !$isExpired;
                  @endphp
                  @if($showReminders)
                  {{-- For pending payment bookings (not expired): Reminders, View More, Delete --}}
                  <button class="btn btn-sm btn-warning" onclick="sendReminder({{ $booking->id }})" title="Send Reminders (Email & SMS)">
                    <i class="fa fa-bell"></i> Reminders
                  </button>
                  <button class="btn btn-sm btn-info" onclick="viewBooking({{ $booking->id }})" title="View Details">
                    <i class="fa fa-eye"></i> View More
                  </button>
                  @if(!$isReception)
                  <button class="btn btn-sm btn-danger" onclick="deleteBooking({{ $booking->id }})" title="Delete">
                    <i class="fa fa-trash"></i>
                  </button>
                  @endif
                  @elseif($booking->status == 'pending' && $booking->payment_status == 'pending')
                  {{-- For expired pending bookings: View More, Delete (no reminders) --}}
                  <button class="btn btn-sm btn-info" onclick="viewBooking({{ $booking->id }})" title="View Details">
                    <i class="fa fa-eye"></i> View More
                  </button>
                  @if(!$isReception)
                  <button class="btn btn-sm btn-danger" onclick="deleteBooking({{ $booking->id }})" title="Delete">
                    <i class="fa fa-trash"></i>
                  </button>
                  @endif
                  @else
                  {{-- For other bookings: View More, Check In (if applicable), Admin Notes, Delete (if applicable) --}}
                  <button class="btn btn-sm btn-info" onclick="viewBooking({{ $booking->id }})" title="View Details">
                    <i class="fa fa-eye"></i>@if($booking->status == 'pending' && $booking->payment_status == 'pending') View More @endif
                  </button>
                  @if(in_array($booking->payment_status, ['paid', 'partial']) || $booking->status == 'confirmed')
                  <a href="{{ route('payment.receipt.download', $booking) }}?download=1" class="btn btn-sm btn-success" target="_blank" title="Download Receipt">
                    <i class="fa fa-download"></i>
                  </a>
                  @endif
                  @if($booking->status == 'confirmed' && $booking->check_in_status == 'pending')
                  <button class="btn btn-sm btn-success" onclick="updateCheckIn({{ $booking->id }}, 'checked_in')" title="Check In">
                    <i class="fa fa-sign-in"></i>
                  </button>
                  @endif
                  @if($booking->check_in_status == 'checked_in')
                  <button class="btn btn-sm btn-info" onclick="openManagerExtensionModal({{ $booking->id }}, '{{ $booking->check_in->format('Y-m-d') }}', '{{ $booking->check_out->format('Y-m-d') }}')" title="Extend Stay">
                    <i class="fa fa-calendar-plus-o"></i> Extend
                  </button>
                  <button class="btn btn-sm btn-warning" onclick="openManagerDecreaseModal({{ $booking->id }}, '{{ $booking->check_in->format('Y-m-d') }}', '{{ $booking->check_out->format('Y-m-d') }}')" title="Decrease Stay">
                    <i class="fa fa-calendar-minus-o"></i> Decrease
                  </button>
                  @endif
                  <button class="btn btn-sm btn-secondary" onclick="showNotesModal({{ $booking->id }})" title="Admin Notes">
                    <i class="fa fa-sticky-note"></i>
                  </button>
                  @if(in_array($booking->status, ['pending', 'cancelled']) || ($booking->status == 'confirmed' && $booking->check_in_status == 'pending'))
                    @if(!$isReception)
                    <button class="btn btn-sm btn-danger" onclick="deleteBooking({{ $booking->id }})" title="Delete">
                      <i class="fa fa-trash"></i>
                    </button>
                    @endif
                  @endif
                  @endif
                </div>
              </td>
            </tr>
            @endforeach
            @endif
          </tbody>
        </table>
      </div>
      
      <!-- Mobile Card View -->
      <div class="mobile-booking-cards">
        @if(($bookingType ?? 'individual') == 'corporate')
          @foreach($bookings as $group)
            @php
              $company = $group['company'] ?? null;
              $companyBookings = $group['bookings'] ?? collect();
              $firstBooking = $group['first_booking'] ?? $companyBookings->first();
              $totalGuests = $companyBookings->count();
              $totalPrice = $companyBookings->sum('total_price');
              $totalPaid = $companyBookings->sum('amount_paid');
              $totalNights = $firstBooking ? $firstBooking->check_in->diffInDays($firstBooking->check_out) : 0;
            @endphp
            <div class="mobile-booking-card booking-row corporate-booking-group"
                 data-status="{{ $firstBooking->status ?? 'pending' }}"
                 data-check-in-status="{{ $firstBooking->check_in_status ?? 'pending' }}"
                 data-payment-status="{{ $firstBooking->payment_status ?? 'pending' }}"
                 data-company-id="{{ $company->id ?? '' }}">
              <div class="mobile-booking-card-header">
                <h5><i class="fa fa-building"></i> {{ $company->name ?? 'N/A' }}</h5>
                <div class="booking-ref">Ref: {{ $firstBooking->booking_reference ?? 'N/A' }}</div>
                <span class="badge badge-info">{{ $totalGuests }} guest(s)</span>
              </div>
              
              @if($company)
              <div class="mobile-booking-info-row" style="background: #f0f8ff; padding: 8px; border-radius: 4px; margin-bottom: 8px;">
                <span class="mobile-booking-info-label"><i class="fa fa-building text-primary"></i> Company:</span>
                <span class="mobile-booking-info-value">
                  <strong class="text-primary">{{ $company->name }}</strong>
                  <br><small class="text-muted">{{ $company->email }}</small>
                  @if($company->phone)
                    <br><small class="text-muted">{{ $company->phone }}</small>
                  @endif
                </span>
              </div>
              @endif
              
              <div class="mobile-booking-info-row">
                <span class="mobile-booking-info-label">Guests:</span>
                <span class="mobile-booking-info-value">
                  @foreach($companyBookings as $booking)
                    <div style="padding: 5px 0; border-bottom: 1px solid #eee;">
                      <strong>{{ $booking->guest_name }}</strong>
                      <br><small class="text-muted">{{ $booking->guest_email }}</small>
                      <br><small class="text-muted">{{ $booking->guest_phone }}</small>
                      <br><span class="badge badge-secondary">{{ $booking->room->room_number ?? 'N/A' }}</span>
                    </div>
                  @endforeach
                </span>
              </div>
              
              <div class="mobile-booking-info-row">
                <span class="mobile-booking-info-label">Check-in:</span>
                <span class="mobile-booking-info-value">{{ $firstBooking->check_in->format('M d, Y') ?? 'N/A' }}</span>
              </div>
              
              <div class="mobile-booking-info-row">
                <span class="mobile-booking-info-label">Check-out:</span>
                <span class="mobile-booking-info-value">{{ $firstBooking->check_out->format('M d, Y') ?? 'N/A' }}</span>
              </div>
              
              <div class="mobile-booking-info-row">
                <span class="mobile-booking-info-label">Nights:</span>
                <span class="mobile-booking-info-value">{{ $totalNights }} night(s)</span>
              </div>
              
              <div class="mobile-booking-info-row">
                <span class="mobile-booking-info-value"><strong>{{ number_format($totalPrice, 0) }} TZS</strong></span>
              </div>
              
              <div class="mobile-booking-info-row">
                <span class="mobile-booking-info-label">Status:</span>
                <span class="mobile-booking-info-value">
                  @php
                    $allConfirmed = $companyBookings->every(function($b) { return $b->status == 'confirmed'; });
                    $allPending = $companyBookings->every(function($b) { return $b->status == 'pending'; });
                  @endphp
                  @if($allConfirmed)
                    <span class="badge badge-success">Confirmed</span>
                  @elseif($allPending)
                    <span class="badge badge-warning">Pending</span>
                  @else
                    <span class="badge badge-secondary">Mixed</span>
                  @endif
                </span>
              </div>
              
              <div class="mobile-booking-info-row">
                <span class="mobile-booking-info-label">Payment:</span>
                <span class="mobile-booking-info-value">
                  @php
                    $totalPaymentPercentage = $totalPrice > 0 ? ($totalPaid / $totalPrice) * 100 : 0;
                  @endphp
                  @if($totalPaymentPercentage >= 100)
                    <span class="badge badge-success">Paid</span>
                  @elseif($totalPaymentPercentage > 0)
                    <span class="badge badge-info">Partial ({{ number_format($totalPaymentPercentage, 0) }}%)</span>
                  @else
                    <span class="badge badge-warning">Pending</span>
                  @endif
                </span>
              </div>
              
              <div class="mobile-booking-actions">
                <button class="btn btn-sm btn-info btn-block" onclick="viewCompanyBookingGroup({{ $company->id ?? 'null' }}, {{ $firstBooking->id ?? 'null' }})" title="View All Guests">
                  <i class="fa fa-eye"></i> View More
                </button>
              </div>
            </div>
          @endforeach
        @else
          @foreach($bookings as $booking)
          @php
            $isExpired = false;
            if ($booking->expires_at) {
              $isExpired = \Carbon\Carbon::parse($booking->expires_at)->isPast();
            }
            $showReminders = $booking->status == 'pending' && 
                            $booking->payment_status == 'pending' && 
                            request('status') != 'expired' && 
                            !$isExpired;
            
            $today = \Carbon\Carbon::today();
            $checkOut = \Carbon\Carbon::parse($booking->check_out);
            $daysRemaining = $today->diffInDays($checkOut, false);
            $weeksRemaining = floor($daysRemaining / 7);
            
            $checkoutTime = $booking->room->checkout_time ?? 
                           \App\Models\HotelSetting::getValue('default_checkout_time') ?? 
                           \App\Models\HotelSetting::getValue('default_room_checkout_time') ?? 
                           '12:00';
            $timeParts = explode(':', $checkoutTime);
            $hour = (int)$timeParts[0];
            $minute = $timeParts[1] ?? '00';
            $ampm = $hour >= 12 ? 'PM' : 'AM';
            $hour12 = $hour > 12 ? $hour - 12 : ($hour == 0 ? 12 : $hour);
            $formattedTime = $hour12 . ':' . $minute . ' ' . $ampm;
          @endphp
          <div class="mobile-booking-card booking-row"
               data-status="{{ $booking->status }}"
               data-check-in-status="{{ $booking->check_in_status ?? 'pending' }}"
               data-payment-status="{{ $booking->payment_status ?? 'pending' }}"
               data-booking-ref="{{ strtolower($booking->booking_reference) }}"
               data-guest-name="{{ strtolower($booking->guest_name) }}"
               data-guest-email="{{ strtolower($booking->guest_email) }}">
            <div class="mobile-booking-card-header">
              <h5>{{ $booking->guest_name }}</h5>
              <div class="booking-ref">Ref: {{ $booking->booking_reference }}</div>
            </div>
            
            @if(($bookingType ?? 'individual') == 'corporate' && $booking->company)
          <div class="mobile-booking-info-row" style="background: #f0f8ff; padding: 8px; border-radius: 4px; margin-bottom: 8px;">
            <span class="mobile-booking-info-label"><i class="fa fa-building text-primary"></i> Company:</span>
            <span class="mobile-booking-info-value">
              <strong class="text-primary">{{ $booking->company->name }}</strong>
              <br><small><i class="fa fa-envelope"></i> {{ $booking->company->email }}</small>
              @if($booking->company->phone)
                <br><small><i class="fa fa-phone"></i> {{ $booking->company->phone }}</small>
              @endif
            </span>
          </div>
          @endif
          
          @if(($bookingType ?? 'individual') == 'corporate' && $booking->payment_responsibility)
          <div class="mobile-booking-info-row">
            <span class="mobile-booking-info-label">Payment:</span>
            <span class="mobile-booking-info-value">
              @if($booking->payment_responsibility == 'company')
                <span class="badge badge-info"><i class="fa fa-building"></i> Company Pays (Room)</span>
              @elseif($booking->payment_responsibility == 'self')
                <span class="badge badge-warning"><i class="fa fa-user"></i> Self-Paid (Services)</span>
              @else
                <span class="badge badge-secondary">Mixed</span>
              @endif
            </span>
          </div>
          @endif
          
          <div class="mobile-booking-info-row">
            <span class="mobile-booking-info-label">Room:</span>
            <span class="mobile-booking-info-value">
              <span class="badge badge-primary">{{ $booking->room->room_type ?? 'N/A' }}</span>
              <br><small>{{ $booking->room->room_number ?? 'N/A' }}</small>
            </span>
          </div>
          
          <div class="mobile-booking-info-row">
            <span class="mobile-booking-info-label">Check-in:</span>
            <span class="mobile-booking-info-value">{{ $booking->check_in->format('M d, Y') }}</span>
          </div>
          
          <div class="mobile-booking-info-row">
            <span class="mobile-booking-info-label">Check-out:</span>
            <span class="mobile-booking-info-value">
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
                <br><span class="badge badge-info">
                  <i class="fa fa-calendar-plus-o"></i> Extended by {{ $extendedNights }} night{{ $extendedNights > 1 ? 's' : '' }}
                </span>
              @elseif($isDecreased && $decreasedNights > 0)
                <br><span class="badge badge-warning">
                  <i class="fa fa-calendar-minus-o"></i> Decreased by {{ $decreasedNights }} night{{ $decreasedNights > 1 ? 's' : '' }}
                </span>
              @endif
              @if($booking->extension_status === 'pending' && $booking->extension_requested_to)
                @php
                  $requestedCheckOut = \Carbon\Carbon::parse($booking->extension_requested_to);
                  $pendingExtensionNights = $currentCheckOut->diffInDays($requestedCheckOut);
                @endphp
                @if($pendingExtensionNights > 0)
                  <br><span class="badge badge-warning">
                    <i class="fa fa-clock-o"></i> Extension Pending (+{{ $pendingExtensionNights }} night{{ $pendingExtensionNights > 1 ? 's' : '' }})
                  </span>
                  <br><small class="text-muted">Requested: {{ $requestedCheckOut->format('M d, Y') }}</small>
                @endif
              @endif
              @if($daysRemaining > 0)
                @if($weeksRemaining > 0)
                  <br><small class="text-info">{{ $weeksRemaining }} week{{ $weeksRemaining > 1 ? 's' : '' }} remaining</small>
                @else
                  <br><small class="text-info">{{ $daysRemaining }} day{{ $daysRemaining > 1 ? 's' : '' }} remaining</small>
                @endif
              @elseif($daysRemaining == 0)
                <br><small class="text-warning">Check-out today at {{ $formattedTime }}</small>
              @else
                <br><small class="text-danger">{{ abs($daysRemaining) }} day{{ abs($daysRemaining) > 1 ? 's' : '' }} overdue</small>
              @endif
            </span>
          </div>
          
          <div class="mobile-booking-info-row">
            <span class="mobile-booking-info-label">Nights:</span>
            <span class="mobile-booking-info-value">
              {{ $booking->check_in->diffInDays($booking->check_out) }} nights
              @if(($isExtended || $isDecreased) && $booking->original_check_out)
                @php
                  $originalNights = $booking->check_in->diffInDays(\Carbon\Carbon::parse($booking->original_check_out));
                @endphp
                <br><small class="text-muted">(Original: {{ $originalNights }} nights)</small>
              @endif
            </span>
          </div>
          
          <div class="mobile-booking-info-row">
            <span class="mobile-booking-info-label">Total Price:</span>
            <span class="mobile-booking-info-value">
              <strong>{{ number_format($booking->total_price, 0) }} TZS</strong>
              @if($isExtended && $booking->room && $extendedNights > 0)
                @php
                  $extensionCost = $booking->room->price_per_night * $extendedNights;
                  $originalPrice = $booking->total_price - $extensionCost;
                @endphp
                  <br><small class="text-info">
                    <i class="fa fa-calendar-plus-o"></i> +{{ number_format($extensionCost, 0) }} TZS (extension)
                  </small>
                  <br><small class="text-muted">Original: {{ number_format($originalPrice, 0) }} TZS</small>
              @elseif($isDecreased && $booking->room && $decreasedNights > 0)
                @php
                  $decreaseRefund = $booking->room->price_per_night * $decreasedNights;
                  $originalPrice = $booking->total_price + $decreaseRefund;
                @endphp
                  <br><small class="text-warning">
                    <i class="fa fa-calendar-minus-o"></i> -{{ number_format($decreaseRefund, 0) }} TZS (decrease)
                  </small>
                  <br><small class="text-muted">Original: {{ number_format($originalPrice, 0) }} TZS</small>
              @endif
              @if($booking->extension_status === 'pending' && $booking->extension_requested_to && $booking->room)
                @php
                  $requestedCheckOut = \Carbon\Carbon::parse($booking->extension_requested_to);
                  $pendingExtensionNights = $currentCheckOut->diffInDays($requestedCheckOut);
                  $pendingExtensionCost = $booking->room->price_per_night * $pendingExtensionNights;
                @endphp
                @if($pendingExtensionNights > 0)
                  <br><small class="text-warning">
                    <i class="fa fa-clock-o"></i> +{{ number_format($pendingExtensionCost, 0) }} TZS (pending extension)
                  </small>
                  <br><small class="text-muted">If approved: {{ number_format($booking->total_price + $pendingExtensionCost, 0) }} TZS</small>
                @endif
              @endif
              @if($booking->payment_status == 'paid')
                <br><small class="text-success"><i class="fa fa-check"></i> Paid</small>
              @endif
            </span>
          </div>
          
          <div class="mobile-booking-info-row">
            <span class="mobile-booking-info-label">Status:</span>
            <span class="mobile-booking-info-value">
              @if($booking->status == 'pending')
                <span class="badge badge-warning">Pending</span>
              @elseif($booking->status == 'confirmed')
                <span class="badge badge-success">Confirmed</span>
              @elseif($booking->status == 'cancelled')
                <span class="badge badge-danger">Cancelled</span>
              @elseif($booking->status == 'completed')
                <span class="badge badge-info">Completed</span>
              @endif
            </span>
          </div>
          
          <div class="mobile-booking-info-row">
            <span class="mobile-booking-info-label">Payment:</span>
            <span class="mobile-booking-info-value">
              @if($booking->payment_status == 'pending')
                <span class="badge badge-warning">Pending</span>
              @elseif($booking->payment_status == 'paid')
                <span class="badge badge-success">Paid</span>
              @elseif($booking->payment_status == 'partial')
                <span class="badge badge-info">Partial</span>
                @if($booking->payment_percentage)
                  <br><small>{{ number_format($booking->payment_percentage, 0) }}% paid</small>
                @endif
              @elseif($booking->payment_status == 'failed')
                <span class="badge badge-danger">Failed</span>
              @elseif($booking->payment_status == 'cancelled')
                <span class="badge badge-secondary">Cancelled</span>
              @endif
              @if($booking->payment_method)
                <br><small>{{ ucfirst($booking->payment_method) }}</small>
              @endif
            </span>
          </div>
          
          <div class="mobile-booking-info-row">
            <span class="mobile-booking-info-label">Check-in Status:</span>
            <span class="mobile-booking-info-value">
              @if($booking->check_in_status == 'pending')
                <span class="badge badge-warning">Pending</span>
              @elseif($booking->check_in_status == 'checked_in')
                <span class="badge badge-success">Checked In</span>
                @if($booking->checked_in_at)
                  <br><small>{{ \Carbon\Carbon::parse($booking->checked_in_at)->format('M d, Y H:i') }}</small>
                @endif
              @elseif($booking->check_in_status == 'checked_out')
                <span class="badge badge-info">Checked Out</span>
                @if($booking->checked_out_at)
                  <br><small>{{ \Carbon\Carbon::parse($booking->checked_out_at)->format('M d, Y H:i') }}</small>
                @endif
              @endif
            </span>
          </div>
          
          <div class="mobile-booking-info-row">
            <span class="mobile-booking-info-label">Email:</span>
            <span class="mobile-booking-info-value"><small>{{ $booking->guest_email }}</small></span>
          </div>
          
          @if($booking->guest_phone)
          <div class="mobile-booking-info-row">
            <span class="mobile-booking-info-label">Phone:</span>
            <span class="mobile-booking-info-value"><small>{{ $booking->guest_phone }}</small></span>
          </div>
          @endif
          
          <div class="mobile-booking-actions">
            @if($showReminders)
            <button class="btn btn-sm btn-warning" onclick="sendReminder({{ $booking->id }})" title="Send Reminders">
              <i class="fa fa-bell"></i> Reminders
            </button>
            <button class="btn btn-sm btn-info" onclick="viewBooking({{ $booking->id }})" title="View Details">
              <i class="fa fa-eye"></i> View
            </button>
            @if(!$isReception)
            <button class="btn btn-sm btn-danger" onclick="deleteBooking({{ $booking->id }})" title="Delete">
              <i class="fa fa-trash"></i> Delete
            </button>
            @endif
            @elseif($booking->status == 'pending' && $booking->payment_status == 'pending')
            <button class="btn btn-sm btn-info" onclick="viewBooking({{ $booking->id }})" title="View Details">
              <i class="fa fa-eye"></i> View
            </button>
            @if(!$isReception)
            <button class="btn btn-sm btn-danger" onclick="deleteBooking({{ $booking->id }})" title="Delete">
              <i class="fa fa-trash"></i> Delete
            </button>
            @endif
            @else
            <button class="btn btn-sm btn-info" onclick="viewBooking({{ $booking->id }})" title="View Details">
              <i class="fa fa-eye"></i> View
            </button>
            <a href="{{ route('payment.receipt.download', $booking) }}?download=1" class="btn btn-sm btn-success" target="_blank" title="Download Receipt">
              <i class="fa fa-download"></i>
            </a>
            @if($booking->status == 'confirmed' && $booking->check_in_status == 'pending')
            <button class="btn btn-sm btn-success" onclick="updateCheckIn({{ $booking->id }}, 'checked_in')" title="Check In">
              <i class="fa fa-sign-in"></i> Check In
            </button>
            @endif
            <button class="btn btn-sm btn-secondary" onclick="showNotesModal({{ $booking->id }})" title="Admin Notes">
              <i class="fa fa-sticky-note"></i> Notes
            </button>
            @if(in_array($booking->status, ['pending', 'cancelled']) || ($booking->status == 'confirmed' && $booking->check_in_status == 'pending'))
              @if(!$isReception)
              <button class="btn btn-sm btn-danger" onclick="deleteBooking({{ $booking->id }})" title="Delete">
                <i class="fa fa-trash"></i> Delete
              </button>
              @endif
            @endif
            @endif
          </div>
        </div>
        @endforeach
        @endif
      </div>
      
      <!-- Pagination -->
      <div class="d-flex justify-content-center mt-3">
        {{ $bookings->appends(request()->query())->links('pagination::bootstrap-4') }}
      </div>
      @else
      <div class="alert alert-info text-center">
        <i class="fa fa-info-circle fa-2x mb-3"></i>
        <h4>No bookings found</h4>
        <p>There are no bookings matching your criteria.</p>
      </div>
      @endif
    </div>
  </div>
</div>

<!-- Booking Details Modal -->
<div class="modal fade" id="bookingDetailsModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background: #940000; color: white;">
        <h5 class="modal-title"><i class="fa fa-info-circle"></i> Booking Details & Financials</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="bookingDetailsContent">
        <!-- Content will be loaded here -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Manager Extension Modal -->
<div class="modal fade" id="managerExtensionModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background: #17a2b8; color: white;">
        <h5 class="modal-title"><i class="fa fa-calendar-plus-o"></i> Extend Booking</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="managerExtensionForm">
          <input type="hidden" id="manager_extension_booking_id" name="booking_id">
          <div class="alert alert-info">
            <i class="fa fa-info-circle"></i> <strong>Note:</strong> The booking will be extended and the price will be adjusted automatically.
          </div>
          <div class="form-group">
            <label for="manager_extension_new_check_out">New Check-out Date *</label>
            <input type="date" class="form-control" id="manager_extension_new_check_out" name="new_check_out" required>
            <small class="form-text text-muted">Select a date after the current check-out date.</small>
          </div>
          <div class="form-group">
            <label for="manager_extension_reason">Reason (Optional)</label>
            <textarea class="form-control" id="manager_extension_reason" name="reason" rows="3" placeholder="Reason for extending the booking..."></textarea>
          </div>
          <div id="managerExtensionCostPreview" style="display: none; padding: 15px; background: #f8f9fa; border-radius: 5px; margin-bottom: 15px;">
            <p class="mb-0">
              <span id="managerExtensionNights">0</span> additional night(s) × 
              <span id="managerExtensionRoomPrice">0</span> TZS per night = 
              <strong>Additional Cost: <span id="managerExtensionTotalCost">0</span> TZS</strong>
            </p>
          </div>
          <div id="managerExtensionAlert"></div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-info" onclick="submitManagerExtension()">
          <i class="fa fa-save"></i> Extend Booking
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Manager Decrease Modal -->
<div class="modal fade" id="managerDecreaseModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background: #ffc107; color: white;">
        <h5 class="modal-title"><i class="fa fa-calendar-minus-o"></i> Decrease Booking</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="managerDecreaseForm">
          <input type="hidden" id="manager_decrease_booking_id" name="booking_id">
          <div class="alert alert-warning">
            <i class="fa fa-info-circle"></i> <strong>Note:</strong> The booking will be decreased and the price will be adjusted automatically. A refund will be calculated.
          </div>
          <div class="form-group">
            <label for="manager_decrease_new_check_out">New Check-out Date *</label>
            <input type="date" class="form-control" id="manager_decrease_new_check_out" name="new_check_out" required>
            <small class="form-text text-muted">Select a date before the current check-out date.</small>
          </div>
          <div class="form-group">
            <label for="manager_decrease_reason">Reason (Optional)</label>
            <textarea class="form-control" id="manager_decrease_reason" name="reason" rows="3" placeholder="Reason for decreasing the booking..."></textarea>
          </div>
          <div id="managerDecreaseCostPreview" style="display: none; padding: 15px; background: #fff3cd; border-radius: 5px; margin-bottom: 15px;">
            <p class="mb-0">
              <span id="managerDecreaseNights">0</span> night(s) reduction × 
              <span id="managerDecreaseRoomPrice">0</span> TZS per night = 
              <strong>Refund: <span id="managerDecreaseTotalRefund">0</span> TZS</strong>
            </p>
          </div>
          <div id="managerDecreaseAlert"></div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-warning" onclick="submitManagerDecrease()">
          <i class="fa fa-save"></i> Decrease Booking
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Admin Notes Modal -->
<div class="modal fade" id="notesModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Admin Notes</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="notesForm">
          <input type="hidden" id="notes_booking_id" name="booking_id">
          <div class="form-group">
            <label for="admin_notes">Notes (Internal use only)</label>
            <textarea class="form-control" id="admin_notes" name="admin_notes" rows="5" placeholder="Add internal notes about this booking..."></textarea>
            <small class="form-text text-muted">These notes are only visible to managers.</small>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="saveNotes()">Save Notes</button>
      </div>
    </div>
  </div>
</div>

<!-- Company Booking Group Modal -->
<div class="modal fade" id="companyBookingGroupModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document" style="max-width: 95%; width: 95%;">
    <div class="modal-content">
      <div class="modal-header" style="background: #940000; color: white;">
        <h5 class="modal-title"><i class="fa fa-building"></i> Company Booking Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="companyBookingGroupContent">
        <div class="text-center">
          <i class="fa fa-spinner fa-spin fa-3x"></i>
          <p>Loading booking details...</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Cancel Booking Modal -->
<div class="modal fade" id="cancelBookingModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Cancel Booking</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="cancelBookingForm">
          <input type="hidden" id="cancel_booking_id" name="booking_id">
          <div class="form-group">
            <label for="cancellation_reason">Cancellation Reason *</label>
            <textarea class="form-control" id="cancellation_reason" name="cancellation_reason" rows="4" required placeholder="Enter reason for cancellation..."></textarea>
            <small class="form-text text-muted">This reason will be visible to the customer.</small>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-danger" onclick="confirmCancel()">Cancel Booking</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<style>
.badge-warning {
  background-color: #ffc107;
  color: #212529;
}

.badge-success {
  background-color: #28a745;
  color: white;
}

.badge-danger {
  background-color: #dc3545;
  color: white;
}

.badge-info {
  background-color: #17a2b8;
  color: white;
}

.badge-secondary {
  background-color: #6c757d;
  color: white;
}

/* Rounded borders for all widget-small cards */
.widget-small,
.widget-small.success,
.widget-small.success.coloured-icon,
.widget-small.primary,
.widget-small.primary.coloured-icon,
.widget-small.info,
.widget-small.info.coloured-icon,
.widget-small.warning,
.widget-small.warning.coloured-icon,
.widget-small.danger,
.widget-small.danger.coloured-icon,
.widget-small.dark,
.widget-small.dark.coloured-icon {
  border-radius: 8px !important;
  overflow: hidden;
}

/* Dark widget-small style for zero values */
.widget-small.dark.coloured-icon {
  background-color: #fff;
  color: #2a2a2a;
  border: 1px solid #2a2a2a;
}

.widget-small.dark.coloured-icon .icon {
  background-color: #2a2a2a;
  color: #fff;
  border-radius: 8px 0 0 8px !important;
}

/* Ensure icon border-radius matches card border-radius */
.widget-small.success.coloured-icon .icon,
.widget-small.primary.coloured-icon .icon,
.widget-small.info.coloured-icon .icon,
.widget-small.warning.coloured-icon .icon,
.widget-small.danger.coloured-icon .icon {
  border-radius: 8px 0 0 8px !important;
}

.btn-group {
  display: flex;
  gap: 5px;
}

.btn-group .btn {
  margin: 0;
}

.booking-details-view {
  padding: 10px;
}

.preview-container {
  background-color: #f8f9fa;
  padding: 15px;
  border-radius: 8px;
}

.preview-section {
  background-color: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  margin-bottom: 20px;
}

.preview-section:last-child {
  margin-bottom: 0;
}

.preview-section h5 {
  color: #940000;
  margin-bottom: 15px;
  font-weight: 600;
  border-bottom: 2px solid #940000;
  padding-bottom: 8px;
}

.preview-section table {
  margin-bottom: 0;
}

.preview-section th {
  background-color: #fcfcfc;
  width: 35%;
  color: #666;
  font-weight: 600;
}

.booking-details-view .table-sm td {
  padding: 8px;
}

.company-booking-group-view .card {
  margin-bottom: 20px;
}

.company-booking-group-view .card-header h6 {
  font-size: 16px;
  font-weight: 600;
}

.company-booking-group-view .table-sm {
  font-size: 13px;
}

.company-booking-group-view .table-sm td {
  padding: 8px 10px;
  vertical-align: middle;
}

.company-booking-group-view h6 {
  font-size: 14px;
  font-weight: 600;
  margin-bottom: 10px;
}

#bookingsTable {
  font-size: 14px;
}

#bookingsTable td {
  vertical-align: middle;
}

.dropdown-menu {
  min-width: 150px;
}

.dropdown-item {
  cursor: pointer;
}

.dropdown-item:hover {
  background-color: #f8f9fa;
}

/* Mobile Responsive Styles */
@media (max-width: 767px) {
  /* Title and Button Group - Mobile */
  .tile-title-w-btn {
    flex-direction: column;
    align-items: flex-start !important;
  }
  
  .tile-title-w-btn .title {
    margin-bottom: 15px;
    width: 100%;
  }
  
  .tile-title-w-btn .btn-group {
    width: 100%;
    flex-direction: column;
    gap: 10px;
  }
  
  .tile-title-w-btn .btn-group .btn {
    width: 100%;
    margin: 0;
  }
  
  /* Statistics Cards - Mobile */
  .col-lg-3.col-md-6.col-sm-6 {
    margin-bottom: 15px;
  }
  
  /* Filters - Mobile */
  .row .col-md-2,
  .row .col-md-4 {
    flex: 0 0 100%;
    max-width: 100%;
    margin-bottom: 15px;
  }
  
  .form-group label {
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 5px;
  }
  
  select.form-control,
  input.form-control {
    font-size: 16px; /* Prevents zoom on iOS */
    padding: 12px;
    min-height: 48px;
  }
  
  /* Table - Convert to Cards on Mobile */
  .table-responsive {
    overflow-x: visible;
  }
  
  #bookingsTable {
    display: none; /* Hide table on mobile */
  }
  
  /* Mobile Booking Cards */
  .mobile-booking-cards {
    display: block;
  }
  
  .mobile-booking-card {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  }
  
  .mobile-booking-card-header {
    border-bottom: 2px solid #940000;
    padding-bottom: 10px;
    margin-bottom: 15px;
  }
  
  .mobile-booking-card-header h5 {
    color: #940000;
    font-size: 18px;
    font-weight: 600;
    margin: 0;
  }
  
  .mobile-booking-card-header .booking-ref {
    font-size: 14px;
    color: #6c757d;
    margin-top: 5px;
  }
  
  .mobile-booking-info-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #f0f0f0;
  }
  
  .mobile-booking-info-row:last-child {
    border-bottom: none;
  }
  
  .mobile-booking-info-label {
    font-weight: 600;
    color: #495057;
    font-size: 14px;
    flex: 0 0 40%;
  }
  
  .mobile-booking-info-value {
    color: #212529;
    font-size: 14px;
    text-align: right;
    flex: 1;
  }
  
  .mobile-booking-actions {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #dee2e6;
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
  }
  
  .mobile-booking-actions .btn {
    flex: 1;
    min-width: calc(50% - 4px);
    font-size: 13px;
    padding: 8px 12px;
  }
  
  .mobile-booking-actions .btn i {
    margin-right: 5px;
  }
  
  /* Badges in mobile cards */
  .mobile-booking-info-value .badge {
    font-size: 12px;
    padding: 4px 8px;
  }
  
  /* Pagination - Mobile */
  .pagination {
    justify-content: center;
    flex-wrap: wrap;
  }
  
  .pagination .page-link {
    padding: 8px 12px;
    font-size: 14px;
  }
  
  /* Modal - Mobile */
  .modal-dialog {
    margin: 10px;
  }
  
  .modal-dialog.modal-lg {
    max-width: calc(100% - 20px);
  }
  
  .booking-details-view .row .col-md-6 {
    flex: 0 0 100%;
    max-width: 100%;
    margin-bottom: 20px;
  }
}

/* Desktop - Hide mobile cards */
@media (min-width: 768px) {
  .mobile-booking-cards {
    display: none;
  }
  
  #bookingsTable {
    display: table;
  }
}

/* Very Small Screens */
@media (max-width: 480px) {
  .mobile-booking-card {
    padding: 12px;
  }
  
  .mobile-booking-card-header h5 {
    font-size: 16px;
  }
  
  .mobile-booking-info-label,
  .mobile-booking-info-value {
    font-size: 13px;
  }
  
  .mobile-booking-actions .btn {
    flex: 0 0 100%;
    min-width: 100%;
    margin-bottom: 8px;
  }
  
  .mobile-booking-actions .btn:last-child {
    margin-bottom: 0;
  }
  
  .tile-title-w-btn .title {
    font-size: 18px;
  }
  
  .widget-small {
    padding: 10px;
  }
  
  .widget-small .icon {
    font-size: 1.5rem !important;
  }
  
  .widget-small .info h4 {
    font-size: 14px;
  }
  
  .widget-small .info p {
    font-size: 20px;
  }
  
  /* Rounded borders for all widget-small cards */
  .widget-small,
  .widget-small.success,
  .widget-small.success.coloured-icon,
  .widget-small.primary,
  .widget-small.primary.coloured-icon,
  .widget-small.info,
  .widget-small.info.coloured-icon,
  .widget-small.warning,
  .widget-small.warning.coloured-icon,
  .widget-small.danger,
  .widget-small.danger.coloured-icon,
  .widget-small.dark,
  .widget-small.dark.coloured-icon {
    border-radius: 8px !important;
    overflow: hidden;
  }
  
  /* Dark widget-small style for zero values */
  .widget-small.dark.coloured-icon {
    background-color: #fff;
    color: #2a2a2a;
    border: 1px solid #2a2a2a;
  }
  
  .widget-small.dark.coloured-icon .icon {
    background-color: #2a2a2a;
    color: #fff;
  }
}
</style>

<script>
function viewBooking(bookingId) {
  console.log('Loading booking:', bookingId);
  fetch('{{ url("/manager/bookings") }}/' + bookingId, {
    method: 'GET',
    headers: {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    }
  })
  .then(response => {
    console.log('Response status:', response.status);
    if (!response.ok) {
      return response.json().then(err => {
        throw new Error(err.message || 'HTTP error! status: ' + response.status);
      }).catch(() => {
        throw new Error('HTTP error! status: ' + response.status);
      });
    }
    return response.json();
  })
  .then(data => {
    console.log('Booking data:', data);
    if (data.success) {
      const booking = data.booking;
      const room = booking.room || {};
      
      // Helper function to format dates safely
      function formatDate(dateString) {
        if (!dateString) return 'N/A';
        try {
          // Extract date part only (YYYY-MM-DD) to avoid timezone issues
          let datePart;
          if (dateString.includes('T')) {
            // Extract date part before 'T' (e.g., "2025-12-11T00:00:00.000000Z" -> "2025-12-11")
            datePart = dateString.split('T')[0];
          } else if (dateString.includes(' ')) {
            datePart = dateString.split(' ')[0]; // In case there's a space
          } else {
            datePart = dateString; // Already just a date
          }
          
          // Parse date parts (YYYY-MM-DD)
          const parts = datePart.split('-');
          if (parts.length !== 3) {
            return dateString; // Return original if can't parse
          }
          
          const year = parseInt(parts[0]);
          const month = parseInt(parts[1]) - 1; // JavaScript months are 0-indexed
          const day = parseInt(parts[2]);
          
          if (isNaN(year) || isNaN(month) || isNaN(day)) {
            return dateString; // Return original if invalid
          }
          
          // Create date using local timezone (not UTC) - this ensures the date displays correctly
          const date = new Date(year, month, day);
          
          if (isNaN(date.getTime())) {
            return dateString; // Return original if invalid
          }
          
          return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
        } catch (e) {
          console.error('Date formatting error:', e, dateString);
          return dateString; // Return original if parsing fails
        }
      }
      
      // Helper function to calculate nights
      function calculateNights(checkIn, checkOut) {
        if (!checkIn || !checkOut) return 'N/A';
        try {
          // Extract date parts to avoid timezone issues
          const getDatePart = (dateString) => {
            if (dateString.includes('T')) {
              return dateString.split('T')[0];
            }
            return dateString.split(' ')[0];
          };
          
          const checkInPart = getDatePart(checkIn);
          const checkOutPart = getDatePart(checkOut);
          
          const [yearIn, monthIn, dayIn] = checkInPart.split('-');
          const [yearOut, monthOut, dayOut] = checkOutPart.split('-');
          
          if (!yearIn || !monthIn || !dayIn || !yearOut || !monthOut || !dayOut) {
            return 'N/A';
          }
          
          const checkInDate = new Date(parseInt(yearIn), parseInt(monthIn) - 1, parseInt(dayIn));
          const checkOutDate = new Date(parseInt(yearOut), parseInt(monthOut) - 1, parseInt(dayOut));
          
          if (isNaN(checkInDate.getTime()) || isNaN(checkOutDate.getTime())) {
            return 'N/A';
          }
          
          const diffTime = checkOutDate - checkInDate;
          const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
          return diffDays + (diffDays === 1 ? ' night' : ' nights');
        } catch (e) {
          return 'N/A';
        }
      }
      
      // Helper function to calculate nights between two dates (returns number)
      function calculateNightsNumber(checkIn, checkOut) {
        if (!checkIn || !checkOut) return 0;
        try {
          const getDatePart = (dateString) => {
            if (dateString.includes('T')) {
              return dateString.split('T')[0];
            }
            return dateString.split(' ')[0];
          };
          
          const checkInPart = getDatePart(checkIn);
          const checkOutPart = getDatePart(checkOut);
          
          const [yearIn, monthIn, dayIn] = checkInPart.split('-');
          const [yearOut, monthOut, dayOut] = checkOutPart.split('-');
          
          if (!yearIn || !monthIn || !dayIn || !yearOut || !monthOut || !dayOut) {
            return 0;
          }
          
          const checkInDate = new Date(parseInt(yearIn), parseInt(monthIn) - 1, parseInt(dayIn));
          const checkOutDate = new Date(parseInt(yearOut), parseInt(monthOut) - 1, parseInt(dayOut));
          
          if (isNaN(checkInDate.getTime()) || isNaN(checkOutDate.getTime())) {
            return 0;
          }
          
          const diffTime = checkOutDate - checkInDate;
          const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
          return diffDays;
        } catch (e) {
          return 0;
        }
      }
      
      // Check if booking was extended or decreased
      let isExtended = false;
      let isDecreased = false;
      let extendedNights = 0;
      let decreasedNights = 0;
      
      if (booking.original_check_out && booking.original_check_out !== booking.check_out) {
        const originalCheckOut = new Date(booking.original_check_out + 'T00:00:00');
        const currentCheckOut = new Date(booking.check_out + 'T00:00:00');
        
        if (currentCheckOut > originalCheckOut) {
          // Extended
          isExtended = true;
          extendedNights = calculateNightsNumber(booking.original_check_out, booking.check_out);
        } else if (currentCheckOut < originalCheckOut) {
          // Decreased
          isDecreased = true;
          decreasedNights = calculateNightsNumber(booking.check_out, booking.original_check_out);
        }
      }
      
      const originalNights = (isExtended || isDecreased) ? calculateNightsNumber(booking.check_in, booking.original_check_out) : calculateNightsNumber(booking.check_in, booking.check_out);
      
      // Calculate extension cost or decrease refund
      let extensionCost = 0;
      let decreaseRefund = 0;
      let originalPrice = parseFloat(booking.total_price || 0);
      
      if (isExtended && booking.room && extendedNights > 0 && booking.room.price_per_night) {
        extensionCost = parseFloat(booking.room.price_per_night) * extendedNights;
        // Calculate original price by subtracting extension cost from total price
        originalPrice = parseFloat(booking.total_price || 0) - extensionCost;
      } else if (isDecreased && booking.room && decreasedNights > 0 && booking.room.price_per_night) {
        decreaseRefund = parseFloat(booking.room.price_per_night) * decreasedNights;
        // Calculate original price by adding decrease refund to total price
        originalPrice = parseFloat(booking.total_price || 0) + decreaseRefund;
      }
      
      const detailsHtml = `
        <div class="booking-details-view">
          <div class="preview-container">
            
            <!-- Top Status Bar -->
            <div class="preview-section mb-4 d-flex justify-content-between align-items-center">
              <div>
                 <h4 class="mb-0 text-primary">${booking.guest_name}</h4>
                 <span class="text-muted small"><i class="fa fa-hashtag"></i> ${booking.booking_reference}</span>
              </div>
              <div class="text-right">
                 <span class="badge badge-${booking.status === 'confirmed' ? 'success' : booking.status === 'pending' ? 'warning' : 'danger'} p-2" style="font-size: 0.9rem;">
                    ${booking.status ? booking.status.toUpperCase() : 'N/A'}
                 </span>
                 <div class="mt-1 small text-muted">
                    ${booking.check_in_status === 'checked_in' ? '<i class="fa fa-check-circle text-success"></i> Checked In' : 
                      booking.check_in_status === 'checked_out' ? '<i class="fa fa-check-circle text-secondary"></i> Checked Out' : 
                      '<i class="fa fa-clock-o"></i> Status: ' + (booking.check_in_status || 'Pending')}
                 </div>
              </div>
            </div>

            <div class="row">
              <!-- Left Column -->
              <div class="col-md-6">
                
                <!-- Guest Details -->
                <div class="preview-section h-100">
                  <h5><i class="fa fa-user-circle"></i> Guest Details</h5>
                  <table class="table table-bordered table-sm">
                    <tr>
                      <th>Name:</th>
                      <td>
                        <div class="font-weight-bold">${booking.first_name || ''} ${booking.last_name || ''}</div>
                        <div class="text-muted small">${booking.booking_for === 'me' ? '(Main Guest)' : '(Booking for someone else)'}</div>
                      </td>
                    </tr>
                    <tr>
                      <th>Email:</th>
                      <td>${booking.guest_email}</td>
                    </tr>
                    <tr>
                      <th>Phone:</th>
                      <td>${booking.guest_phone || 'N/A'}</td>
                    </tr>
                    <tr>
                      <th>Country:</th>
                      <td>${booking.country || 'N/A'}</td>
                    </tr>
                    <tr>
                      <th>Guests:</th>
                      <td>${booking.number_of_guests || 1} Person(s)</td>
                    </tr>
                  </table>
                </div>
              </div>

              <!-- Right Column -->
              <div class="col-md-6">
                <!-- Stay Dates -->
                <div class="preview-section h-100">
                  <h5><i class="fa fa-calendar"></i> Stay Dates</h5>
                  <div class="d-flex justify-content-between align-items-center bg-light rounded p-3 mb-3">
                    <div class="text-center">
                       <div class="text-muted small text-uppercase">Check In</div>
                       <div class="font-weight-bold text-primary">${formatDate(booking.check_in)}</div>
                       ${booking.arrival_time ? '<div class="small text-muted">' + booking.arrival_time + '</div>' : ''}
                    </div>
                    <div class="text-muted"><i class="fa fa-arrow-right"></i></div>
                    <div class="text-center">
                       <div class="text-muted small text-uppercase">Check Out</div>
                       <div class="font-weight-bold text-primary">${formatDate(booking.check_out)}</div>
                       ${booking.departure_time ? '<div class="small text-muted">' + booking.departure_time + '</div>' : ''}
                    </div>
                  </div>
                  
                  <div class="text-center mb-0">
                    <span class="badge badge-pill badge-info px-4 py-2" style="font-size: 0.9rem;">${calculateNights(booking.check_in, booking.check_out)} Stay</span>
                  </div>

                  ${isExtended && extendedNights > 0 ? `<div class="alert alert-info py-2 mt-3 mb-0 small"><i class="fa fa-plus-circle"></i> Extended by ${extendedNights} nights</div>` : ''}
                  ${isDecreased && decreasedNights > 0 ? `<div class="alert alert-warning py-2 mt-3 mb-0 small"><i class="fa fa-minus-circle"></i> Decreased by ${decreasedNights} nights</div>` : ''}
                  ${(isExtended || isDecreased) && booking.original_check_out ? `<div class="text-muted small text-center mt-2">Original Checkout: ${formatDate(booking.original_check_out)}</div>` : ''}


                </div>
              </div>
            </div>

            <div class="row mt-4">
              <!-- Room details -->
              <div class="col-md-6">
                <div class="preview-section h-100">
                  <h5><i class="fa fa-bed"></i> Room Assigned</h5>
                  <table class="table table-bordered table-sm">
                    <tr>
                      <th>Room Number:</th>
                      <td class="h5 mb-0 text-primary">${room.room_number || 'Unassigned'}</td>
                    </tr>
                    <tr>
                      <th>Room Type:</th>
                      <td>${room.room_type || 'Standard'}</td>
                    </tr>
                    <tr>
                      <th>Floor:</th>
                      <td>${room.floor_location || 'N/A'}</td>
                    </tr>
                    <tr>
                      <th>Capacity:</th>
                      <td>${room.capacity || 'N/A'} ${parseInt(room.capacity) === 1 ? 'Guest' : 'Guests'}</td>
                    </tr>
                  </table>
                </div>
              </div>

              <!-- Financial Info -->
              <div class="col-md-6">
                <div class="preview-section h-100">
                  <h5><i class="fa fa-money"></i> Financial Info</h5>
                  <table class="table table-bordered table-sm">
                    ${(() => {
                      const serviceCharges = parseFloat(booking.service_charges_tsh || 0);
                      const totalRoomCharge = parseFloat(booking.total_price || 0);
                      const totalCharges = totalRoomCharge + serviceCharges;
                      const nights = calculateNightsNumber(booking.check_in, booking.check_out) || 1;
                      const pricePerNight = totalRoomCharge / nights;
                      
                      let html = '';
                      
                      // Price per Night
                      html += `<tr>
                        <th>Price/Night:</th>
                        <td>${pricePerNight.toLocaleString()} TZS</td>
                      </tr>`;

                      // Total Room Charge
                      html += `<tr>
                        <th>Room Charge:</th>
                        <td>${totalRoomCharge.toLocaleString()} TZS</td>
                      </tr>`;
                      
                      // Service Charges (if any)
                      if (serviceCharges > 0) {
                        html += `<tr>
                          <th>Service Charges:</th>
                          <td>${serviceCharges.toLocaleString()} TZS</td>
                        </tr>`;
                      }
                      
                      // Total Charges
                      html += `<tr class="table-active">
                        <th class="font-weight-bold">Total Bill:</th>
                        <td class="font-weight-bold h5 mb-0 text-primary">${totalCharges.toLocaleString()} TZS</td>
                      </tr>`;
                      
                      return html;
                    })()}
                    
                    ${isExtended && extensionCost > 0 ? `<tr><th class="text-info">Extension:</th><td class="text-info">+${extensionCost.toLocaleString()} TZS</td></tr>` : ''}
                    ${isDecreased && decreaseRefund > 0 ? `<tr><th class="text-warning">Refund:</th><td class="text-warning">-${decreaseRefund.toLocaleString()} TZS</td></tr>` : ''}

                    <tr>
                      <th>${booking.is_corporate_booking ? 'Paid by Company:' : 'Total Amount Paid:'}</th>
                      <td class="text-success font-weight-bold">${parseFloat(booking.amount_paid || 0).toLocaleString()} TZS</td>
                    </tr>
                    ${(() => {
                      const totalRoomCharge = parseFloat(booking.total_price || 0);
                      const serviceCharges = parseFloat(booking.service_charges_tsh || 0);
                      const totalCharges = totalRoomCharge + serviceCharges;
                      const totalPaid = parseFloat(booking.amount_paid || 0);
                      const balance = totalCharges - totalPaid;
                      
                      // If there's a positive balance (guest owes money)
                      if (balance > 1) {
                        return `<tr class="table-active">
                          <th class="font-weight-bold">Balance Due:</th>
                          <td class="font-weight-bold text-danger h5 mb-0">${balance.toLocaleString()} TZS</td>
                        </tr>`;
                      } 
                      // If fully paid or overpaid
                      else if (totalPaid >= (totalCharges - 1) && totalCharges > 0) {
                        let html = `<tr class="table-active">
                          <th class="font-weight-bold">Balance:</th>
                          <td><span class="badge badge-success px-3 py-1" style="font-size: 0.85rem;"><i class="fa fa-check-circle"></i> ALL PAID</span></td>
                        </tr>`;
                        
                        // If overpaid, show the overpayment amount as additional info
                        if (balance < -1) {
                          html += `<tr>
                            <th class="text-muted">Overpayment:</th>
                            <td class="text-info">${Math.abs(balance).toLocaleString()} TZS <small class="text-muted">(Credit)</small></td>
                          </tr>`;
                        }
                        return html;
                      }
                      return '';
                    })()}
                    <tr>
                      <th>Payment Status:</th>
                      <td>
                        <span class="badge badge-${booking.payment_status === 'paid' ? 'success' : booking.payment_status === 'partial' ? 'info' : 'warning'}">
                          ${booking.payment_status ? booking.payment_status.toUpperCase() : 'N/A'}
                          ${booking.payment_status === 'partial' ? '(' + parseFloat(booking.payment_percentage).toFixed(0) + '%)' : ''}
                        </span>
                      </td>
                    </tr>
                  </table>
                  ${booking.payment_transaction_id ? `<div class="mt-2 text-center"><small class="bg-light p-1 px-2 rounded font-monospace text-muted">ID: ${booking.payment_transaction_id}</small></div>` : ''}
                </div>
              </div>
            </div>

            <!-- Notes & Requests -->
            <div class="row mt-4">
              <div class="col-12">
                <div class="preview-section">
                  <h5><i class="fa fa-sticky-note"></i> Notes & Requests</h5>
                  ${booking.special_requests ? `<div class="alert alert-info mb-2"><i class="fa fa-comment-o"></i> <strong>Guest Request:</strong> ${booking.special_requests}</div>` : '<p class="text-muted small mb-2">No special requests from guest.</p>'}
                  <hr>
                  ${booking.admin_notes ? `<div class="alert alert-warning mb-2"><i class="fa fa-sticky-note-o"></i> <strong>Admin Notes:</strong> ${booking.admin_notes}</div>` : '<p class="text-muted small mb-0">No internal admin notes.</p>'}
                  ${booking.cancellation_reason ? `<div class="alert alert-danger mt-2"><i class="fa fa-ban"></i> <strong>Cancellation Reason:</strong> ${booking.cancellation_reason}</div>` : ''}
                </div>
              </div>
            </div>

          </div>
        </div>
      `;
      
      document.getElementById('bookingDetailsContent').innerHTML = detailsHtml;
      $('#bookingDetailsModal').modal('show');
    } else {
      swal({
        title: "Error",
        text: "Failed to load booking details",
        type: "error",
        confirmButtonColor: "#940000"
      });
    }
  })
  .catch(error => {
    console.error('Error:', error);
    swal({
      title: "Error",
      text: "An error occurred while loading booking details",
      type: "error",
      confirmButtonColor: "#940000"
    });
  });
}

function updateStatus(bookingId, status) {
  swal({
    title: "Update Status?",
    text: `Are you sure you want to change the booking status to "${status}"?`,
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: "#940000",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, update it!",
    cancelButtonText: "Cancel"
  }, function(isConfirm) {
    if (isConfirm) {
      fetch('{{ url("/manager/bookings") }}/' + bookingId + '/status', {
        method: 'PUT',
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ status: status })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          swal({
            title: "Updated!",
            text: data.message || "Booking status has been updated.",
            type: "success",
            confirmButtonColor: "#940000"
          }, function() {
            location.reload();
          });
        } else {
          swal({
            title: "Error!",
            text: data.message || "Failed to update booking status.",
            type: "error",
            confirmButtonColor: "#940000"
          });
        }
      })
      .catch(error => {
        console.error('Error:', error);
        swal({
          title: "Error!",
          text: "An error occurred while updating the booking.",
          type: "error",
          confirmButtonColor: "#940000"
        });
      });
    }
  });
}

function deleteBooking(bookingId) {
  swal({
    title: "Are you sure?",
    text: "You will not be able to recover this booking!",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: "#940000",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, delete it!",
    cancelButtonText: "Cancel"
  }, function(isConfirm) {
    if (isConfirm) {
      fetch('{{ url("/manager/bookings") }}/' + bookingId, {
        method: 'DELETE',
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Content-Type': 'application/json'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          swal({
            title: "Deleted!",
            text: data.message || "Booking has been deleted.",
            type: "success",
            confirmButtonColor: "#940000"
          }, function() {
            location.reload();
          });
        } else {
          swal({
            title: "Error!",
            text: data.message || "Failed to delete booking.",
            type: "error",
            confirmButtonColor: "#940000"
          });
        }
      })
      .catch(error => {
        console.error('Error:', error);
        swal({
          title: "Error!",
          text: "An error occurred while deleting the booking.",
          type: "error",
          confirmButtonColor: "#940000"
        });
      });
    }
  });
}

function showNotesModal(bookingId) {
  console.log('Loading booking for notes:', bookingId);
  fetch('{{ url("/manager/bookings") }}/' + bookingId, {
    method: 'GET',
    headers: {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    }
  })
  .then(response => {
    console.log('Notes response status:', response.status);
    if (!response.ok) {
      return response.json().then(err => {
        throw new Error(err.message || 'HTTP error! status: ' + response.status);
      }).catch(() => {
        throw new Error('HTTP error! status: ' + response.status);
      });
    }
    return response.json();
  })
  .then(data => {
    console.log('Notes booking data:', data);
    if (data.success) {
      document.getElementById('notes_booking_id').value = bookingId;
      document.getElementById('admin_notes').value = data.booking.admin_notes || '';
      $('#notesModal').modal('show');
    } else {
      swal({
        title: "Error",
        text: "Failed to load booking details",
        type: "error",
        confirmButtonColor: "#940000"
      });
    }
  })
  .catch(error => {
    console.error('Error:', error);
    swal({
      title: "Error",
      text: "An error occurred while loading booking details",
      type: "error",
      confirmButtonColor: "#940000"
    });
  });
}

function filterBookings() {
  const statusFilter = document.getElementById('statusFilter').value;
  const checkInStatusFilter = document.getElementById('checkInStatusFilter').value;
  const paymentStatusFilter = document.getElementById('paymentStatusFilter').value;
  const searchInput = document.getElementById('searchInput').value.toLowerCase();
  
  // Filter both table rows and mobile cards
  const rows = document.querySelectorAll('.booking-row');
  let visibleCount = 0;
  
  rows.forEach(row => {
    const status = row.getAttribute('data-status');
    const checkInStatus = row.getAttribute('data-check-in-status');
    const paymentStatus = row.getAttribute('data-payment-status');
    const bookingRef = row.getAttribute('data-booking-ref');
    const guestName = row.getAttribute('data-guest-name');
    const guestEmail = row.getAttribute('data-guest-email');
    
    let show = true;
    
    // Status filter
    if (statusFilter !== 'all' && status !== statusFilter) {
      show = false;
    }
    
    // Check-in status filter
    if (show && checkInStatusFilter !== 'all' && checkInStatus !== checkInStatusFilter) {
      show = false;
    }
    
    // Payment status filter
    if (show && paymentStatusFilter !== 'all' && paymentStatus !== paymentStatusFilter) {
      show = false;
    }
    
    // Search filter
    if (show && searchInput) {
      if (!bookingRef.includes(searchInput) && 
          !guestName.includes(searchInput) && 
          !guestEmail.includes(searchInput)) {
        show = false;
      }
    }
    
    row.style.display = show ? '' : 'none';
    if (show) visibleCount++;
  });
  
  // Show/hide "no results" message
  const tbody = document.querySelector('#bookingsTable tbody');
  if (tbody) {
    let noResultsRow = tbody.querySelector('.no-results-row');
    
    if (visibleCount === 0) {
      if (!noResultsRow) {
        noResultsRow = document.createElement('tr');
        noResultsRow.className = 'no-results-row';
        noResultsRow.innerHTML = `
          <td colspan="11" class="text-center">
            <i class="fa fa-search fa-3x text-muted mb-2"></i>
            <p>No bookings found matching your filters</p>
          </td>
        `;
        tbody.appendChild(noResultsRow);
      }
    } else {
      if (noResultsRow) {
        noResultsRow.remove();
      }
    }
  }
}

function resetFilters() {
  document.getElementById('statusFilter').value = 'all';
  document.getElementById('checkInStatusFilter').value = 'all';
  document.getElementById('paymentStatusFilter').value = 'all';
  document.getElementById('searchInput').value = '';
  filterBookings();
}

function viewCompanyBookingGroup(companyId, firstBookingId) {
  if (!companyId) {
    Swal.fire('Error', 'Company ID is missing', 'error');
    return;
  }
  
  $('#companyBookingGroupModal').modal('show');
  $('#companyBookingGroupContent').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-3x"></i><p>Loading booking details...</p></div>');
  
  fetch('{{ url("/manager/bookings/company") }}/' + companyId, {
    method: 'GET',
    headers: {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    }
  })
  .then(response => {
    if (!response.ok) {
      throw new Error('HTTP error! status: ' + response.status);
    }
    return response.json();
  })
  .then(data => {
    if (data.success && data.bookings) {
      const bookings = data.bookings;
      const company = data.company || {};
      let html = '<div class="company-booking-group-view">';
      
      // Calculate company payment breakdown
      let totalCompanyCharges = 0;
      let totalCompanyPaid = 0;
      let totalRemaining = 0;
      const exchangeRate = bookings.length > 0 ? (bookings[0].locked_exchange_rate || 2500) : 2500;
      
      bookings.forEach(function(booking) {
        totalCompanyCharges += parseFloat(booking.total_price || 0);
        // Only include service charges if company is responsible (Mixed or Company)
        if (booking.payment_responsibility !== 'self') {
          totalCompanyCharges += parseFloat(booking.service_charges_usd || 0);
        }
        totalCompanyPaid += parseFloat(booking.amount_paid || 0);
      });
      totalRemaining = totalCompanyCharges - totalCompanyPaid;
      
      // --- Top Summary Section (Company & Financials) ---
      html += '<div class="row mb-4">';
      
      // 1. Company Info Card
      html += '<div class="col-md-4">';
      html += '<div class="card h-100 border-0 shadow-sm">';
      html += '<div class="card-body">';
      html += '<h6 class="text-uppercase text-muted mb-3" style="font-size: 0.75rem; letter-spacing: 1px;">Company Details</h6>';
      html += '<h5 class="card-title text-primary"><i class="fa fa-building-o mr-2"></i>' + (company.name || 'N/A') + '</h5>';
      html += '<p class="card-text mb-1"><i class="fa fa-envelope-o mr-2 text-muted" style="width: 20px;"></i>' + (company.email || 'N/A') + '</p>';
      if (company.phone) {
        html += '<p class="card-text"><i class="fa fa-phone mr-2 text-muted" style="width: 20px;"></i>' + company.phone + '</p>';
      }
      html += '</div></div></div>';

      // 2. Contact Person / Leader
      html += '<div class="col-md-4">';
      html += '<div class="card h-100 border-0 shadow-sm">';
      html += '<div class="card-body">';
      html += '<h6 class="text-uppercase text-muted mb-3" style="font-size: 0.75rem; letter-spacing: 1px;">Group Leader</h6>';
      html += '<h5 class="card-title text-info"><i class="fa fa-user-circle-o mr-2"></i>' + (company.contact_person || 'N/A') + '</h5>';
      html += '<p class="card-text mb-1"><i class="fa fa-envelope-o mr-2 text-muted" style="width: 20px;"></i>' + (company.guider_email || 'N/A') + '</p>';
      if (company.guider_phone) {
        html += '<p class="card-text"><i class="fa fa-phone mr-2 text-muted" style="width: 20px;"></i>' + company.guider_phone + '</p>';
      }
      html += '</div></div></div>';

      // 3. Financial Summary
      html += '<div class="col-md-4">';
      html += '<div class="card h-100 border-0 shadow-sm bg-light">';
      html += '<div class="card-body">';
      // Fix negative zero issue
      if (Math.abs(totalRemaining) < 0.005) totalRemaining = 0;

      html += '<h6 class="text-uppercase text-muted mb-3" style="font-size: 0.75rem; letter-spacing: 1px;">Company Balance</h6>';
      html += '<div class="d-flex justify-content-between align-items-end mb-2">';
      html += '<span>Total Charges:</span>';
      html += '<span class="font-weight-bold">' + totalCompanyCharges.toLocaleString() + ' TZS</span>';
      html += '</div>';
      html += '<div class="d-flex justify-content-between align-items-end mb-2">';
      html += '<span>Total Paid:</span>';
      html += '<span class="text-success font-weight-bold">' + totalCompanyPaid.toLocaleString() + ' TZS</span>';
      html += '</div>';
      html += '<hr class="my-2">';
      html += '<div class="d-flex justify-content-between align-items-end">';
      html += '<span class="font-weight-bold">Remaining:</span>';
      html += '<span class="h5 mb-0 ' + (totalRemaining > 1 ? 'text-danger' : 'text-success') + '">' + totalRemaining.toLocaleString() + ' TZS</span>';
      html += '</div>';
      html += '</div></div></div>';
      
      html += '</div>'; // End Row
      
      // Note Alert
      html += '<div class="alert alert-info border-0 shadow-sm mb-4">';
      html += '<div class="d-flex">';
      html += '<div class="mr-3"><i class="fa fa-info-circle fa-2x"></i></div>';
      html += '<div><h6 class="alert-heading mb-1">Payment Responsibility</h6>All room charges linked to this booking are billed to the company. Individual guests are responsible for personal services (food, spa, etc.) if marked as self-pay.</div>';
      html += '</div></div>';

      // --- Guests Section with Tabs ---
      html += '<div class="card border-0 shadow-sm">';
      html += '<div class="card-header bg-white border-bottom-0 pt-4 px-4">';
      html += '<h5 class="mb-0"><i class="fa fa-users text-primary mr-2"></i>Guest Bookings (' + bookings.length + ')</h5>';
      html += '</div>';
      
      // Tabs Header
      html += '<div class="card-header bg-white border-bottom-0 px-4 pb-0">';
      html += '<ul class="nav nav-tabs card-header-tabs" id="companyGuestTabs" role="tablist">';
      bookings.forEach(function(booking, index) {
        const isActive = index === 0 ? 'active' : '';
        const tabId = 'guest-tab-' + index;
        const panelId = 'guest-panel-' + index;
        const guestName = (booking.guest_name || 'Guest ' + (index + 1)).split(' ')[0]; // First name only for tab
        
        html += '<li class="nav-item">';
        html += '<a class="nav-link ' + isActive + '" id="' + tabId + '" data-toggle="tab" href="#' + panelId + '" role="tab" aria-controls="' + panelId + '" aria-selected="' + (index === 0) + '">';
        html += '<i class="fa fa-user mr-1"></i> ' + guestName;
        html += '</a></li>';
      });
      html += '</ul></div>';

      // Tabs Content
      html += '<div class="card-body p-0">';
      html += '<div class="tab-content" id="companyGuestTabsContent">';
      
      bookings.forEach(function(booking, index) {
        const isActive = index === 0 ? 'show active' : '';
        const panelId = 'guest-panel-' + index;
        const tabId = 'guest-tab-' + index;
        
        // Data prep
        const checkIn = booking.check_in ? new Date(booking.check_in).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : 'N/A';
        const checkOut = booking.check_out ? new Date(booking.check_out).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : 'N/A';
        const room = booking.room || {};
        const exchangeRate = booking.locked_exchange_rate || 2500;
        const roomTotal = parseFloat(booking.total_price || 0);
        const paidAmount = parseFloat(booking.amount_paid || 0);
        const remaining = roomTotal - paidAmount;
        
        // Badges
        let statusText = booking.status;
        if (booking.check_in_status === 'checked_out' && statusText === 'confirmed') statusText = 'completed';
        
        const statusBadge = statusText === 'confirmed' ? '<span class="badge badge-success">Confirmed</span>' :
                            statusText === 'completed' ? '<span class="badge badge-primary"><i class="fa fa-flag-checkered"></i> Completed</span>' :
                            statusText === 'pending' ? '<span class="badge badge-warning">Pending</span>' :
                            statusText === 'cancelled' ? '<span class="badge badge-danger">Cancelled</span>' :
                            '<span class="badge badge-info">' + (statusText ? statusText.charAt(0).toUpperCase() + statusText.slice(1) : 'N/A') + '</span>';
                            
        const paymentBadge = booking.payment_status === 'paid' ? '<span class="badge badge-success">Paid</span>' :
                            booking.payment_status === 'partial' ? '<span class="badge badge-info">Partial</span>' :
                            '<span class="badge badge-warning">Unpaid</span>';
                            
        const checkInBadge = booking.check_in_status === 'checked_in' ? '<span class="badge badge-info"><i class="fa fa-sign-in"></i> Checked In</span>' :
                            booking.check_in_status === 'checked_out' ? '<span class="badge badge-success"><i class="fa fa-check-circle"></i> Checked Out</span>' :
                            '<span class="badge badge-warning"><i class="fa fa-clock-o"></i> Pending Check-in</span>';

        html += '<div class="tab-pane fade ' + isActive + '" id="' + panelId + '" role="tabpanel" aria-labelledby="' + tabId + '">';
        html += '<div class="p-4">';
        
        // Guest Header with Status Badges
        html += '<div class="preview-section mb-4 d-flex justify-content-between align-items-center">';
        html += '<div><h4 class="mb-0 text-primary">' + (booking.guest_name || 'N/A') + '</h4>';
        html += '<span class="text-muted small"><i class="fa fa-hashtag"></i> ' + booking.booking_reference + '</span></div>';
        html += '<div class="text-right">' + statusBadge + ' <span class="ml-1">' + paymentBadge + '</span> <span class="ml-1">' + checkInBadge + '</span></div>';
        html += '</div>';

        html += '<div class="preview-container">';
        html += '<div class="row">';
        
        // Col 1: Booking Information
        html += '<div class="col-md-6">';
        html += '<div class="preview-section h-100">';
        html += '<h5><i class="fa fa-calendar"></i> Booking Information</h5>';
        html += '<table class="table table-bordered table-sm">';
        html += '<tr><th>Room Number:</th><td class="h5 mb-0 text-primary">' + (room.room_number || 'N/A') + '</td></tr>';
        html += '<tr><th>Room Type:</th><td><span class="badge badge-light">' + (room.room_type || 'N/A') + '</span></td></tr>';
        html += '<tr><th>Check-in:</th><td>' + checkIn + '</td></tr>';
        html += '<tr><th>Check-out:</th><td>' + checkOut + '</td></tr>';
        html += '<tr><th>Nights:</th><td>' + (booking.nights || calculateNightsNumber(booking.check_in, booking.check_out)) + '</td></tr>';
        html += '<tr><th>Guest Email:</th><td>' + (booking.guest_email || 'N/A') + '</td></tr>';
        html += '<tr><th>Guest Phone:</th><td>' + (booking.guest_phone || 'N/A') + '</td></tr>';
        html += '</table>';
        html += '</div></div>';

        // Col 2: Room Financials
        html += '<div class="col-md-6">';
        html += '<div class="preview-section h-100">';
        html += '<h5><i class="fa fa-money"></i> Room Financials</h5>';
        
        const serviceCharges = parseFloat(booking.service_charges_usd || 0);
        const isSelfPay = booking.payment_responsibility === 'self';
        const totalBookingBill = roomTotal + (isSelfPay ? 0 : serviceCharges);
        const guestRemaining = totalBookingBill - paidAmount;

        html += '<table class="table table-bordered table-sm">';
        html += '<tr><th>Price/Night:</th><td>' + parseFloat(room.price_per_night || 0).toLocaleString() + ' TZS</td></tr>';
        html += '<tr><th>Room Charge:</th><td>' + roomTotal.toLocaleString() + ' TZS</td></tr>';
        
        if (serviceCharges > 0) {
          html += '<tr class="' + (isSelfPay ? 'text-muted' : '') + '"><th>Service Charges:</th><td>' + serviceCharges.toLocaleString() + ' TZS';
          if (isSelfPay) {
            html += ' <small class="text-muted">(Guest pays)</small>';
          }
          html += '</td></tr>';
        }

        html += '<tr class="table-active"><th class="font-weight-bold">Total Bill:</th><td class="font-weight-bold h5 mb-0 text-primary">' + totalBookingBill.toLocaleString() + ' TZS</td></tr>';
        html += '<tr><th>Paid by Company:</th><td class="text-success font-weight-bold">' + paidAmount.toLocaleString() + ' TZS</td></tr>';
        
        // Balance logic (same as individual booking modal)
        if (guestRemaining > 1) {
          html += '<tr class="table-active"><th class="font-weight-bold">Balance Due:</th><td class="font-weight-bold text-danger h5 mb-0">' + guestRemaining.toLocaleString() + ' TZS</td></tr>';
        } else if (paidAmount >= (totalBookingBill - 1) && totalBookingBill > 0) {
          html += '<tr class="table-active"><th class="font-weight-bold">Balance:</th><td><span class="badge badge-success px-3 py-1" style="font-size: 0.85rem;"><i class="fa fa-check-circle"></i> ALL PAID</span></td></tr>';
          
          if (guestRemaining < -1) {
            html += '<tr><th class="text-muted">Overpayment:</th><td class="text-info">' + Math.abs(guestRemaining).toLocaleString() + ' TZS <small class="text-muted">(Credit)</small></td></tr>';
          }
        }
        
        html += '<tr><th>Payment Status:</th><td>' + paymentBadge + '</td></tr>';
        html += '</table>';

        // Self-pay warning if applicable
        if (booking.payment_responsibility === 'self') {
          html += '<div class="alert alert-warning py-2 mb-0 mt-2" style="font-size: 0.9rem;">';
          html += '<i class="fa fa-exclamation-triangle mr-2"></i> This guest pays for their own services.';
          html += '</div>';
        }

        html += '</div></div>';
        html += '</div>'; // End row
        html += '</div>'; // End preview-container
        
        html += '</div></div>'; // End tab pane
      });
      
      html += '</div></div></div>'; // End card body & tabs container
      html += '</div>'; // End view container
      
       $('#companyBookingGroupContent').html(html);

       // Update Print Button
       var printBtn = '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
       $('#companyBookingGroupModal .modal-footer').html(printBtn);

    } else {
      $('#companyBookingGroupContent').html('<div class="alert alert-danger">Failed to load booking details.</div>');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    $('#companyBookingGroupContent').html('<div class="alert alert-danger">Error loading booking details: ' + error.message + '</div>');
  });
}

// Modify Dates Functions
function openModifyDatesModal(bookingId, checkIn, currentCheckOut) {
  document.getElementById('modify_booking_id').value = bookingId;
  const checkInInput = document.getElementById('modify_check_in');
  const checkOutInput = document.getElementById('modify_new_check_out');
  
  checkInInput.value = checkIn;
  checkOutInput.value = currentCheckOut;
  checkOutInput.min = checkIn;
  document.getElementById('modify_reason').value = '';
  document.getElementById('modifyDatesCostPreview').style.display = 'none';
  document.getElementById('modifyDatesAlert').innerHTML = '';
  
  // Add event listener for date change
  checkOutInput.onchange = function() {
    const checkInDate = new Date(checkInInput.value);
    const checkOutDate = new Date(checkOutInput.value);
    if (checkInDate && checkOutDate && checkOutDate > checkInDate) {
      const checkInParts = checkIn.split('-');
      const currentCheckOutParts = currentCheckOut.split('-');
      const currentCheckInDate = new Date(parseInt(checkInParts[0]), parseInt(checkInParts[1]) - 1, parseInt(checkInParts[2]));
      const currentCheckOutDate = new Date(parseInt(currentCheckOutParts[0]), parseInt(currentCheckOutParts[1]) - 1, parseInt(currentCheckOutParts[2]));
      const currentNights = Math.ceil((currentCheckOutDate - currentCheckInDate) / (1000 * 60 * 60 * 24));
      const diffTime = checkOutDate - checkInDate;
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
      const nightsDiff = diffDays - currentNights;
      
      if (nightsDiff !== 0) {
        document.getElementById('modifyNightsDiff').textContent = nightsDiff > 0 ? '+' + nightsDiff : nightsDiff;
        document.getElementById('modifyDatesCostPreview').style.display = 'block';
      } else {
        document.getElementById('modifyDatesCostPreview').style.display = 'none';
      }
    }
  };
  
  $('#modifyDatesModal').modal('show');
}

function submitModifyDates() {
  const form = document.getElementById('modifyDatesForm');
  const alertDiv = document.getElementById('modifyDatesAlert');
  const submitBtn = event.target;
  const originalText = submitBtn.innerHTML;
  
  if (alertDiv) {
    alertDiv.innerHTML = '';
  }
  
  if (!form.checkValidity()) {
    form.reportValidity();
    return;
  }
  
  const checkIn = document.getElementById('modify_check_in').value;
  const newCheckOut = document.getElementById('modify_new_check_out').value;
  
  if (new Date(newCheckOut) <= new Date(checkIn)) {
    if (alertDiv) {
      alertDiv.innerHTML = '<div class="alert alert-danger">Check-out date must be after check-in date.</div>';
    }
    return;
  }
  
  submitBtn.disabled = true;
  submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';
  
  const bookingId = document.getElementById('modify_booking_id').value;
  
  @php
    $modifyRoute = (str_starts_with(request()->route()->getName() ?? '', 'admin.')) 
      ? 'admin.bookings.modify-dates' 
      : 'reception.bookings.modify-dates';
  @endphp
  
  fetch('{{ route($modifyRoute, ":id") }}'.replace(':id', bookingId), {
    method: 'PUT',
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      new_check_out: newCheckOut,
      reason: document.getElementById('modify_reason').value
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      swal({
        title: "Success!",
        text: data.message || "Booking dates modified successfully!",
        type: "success",
        confirmButtonColor: "#28a745"
      }, function() {
        $('#modifyDatesModal').modal('hide');
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
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalText;
    }
  })
  .catch(error => {
    console.error('Error:', error);
    if (alertDiv) {
      alertDiv.innerHTML = '<div class="alert alert-danger">An error occurred. Please try again.</div>';
    }
    submitBtn.disabled = false;
    submitBtn.innerHTML = originalText;
  });
}

function updateCheckIn(bookingId, checkInStatus) {
  swal({
    title: "Check In Guest?",
    text: "Are you sure you want to check in this guest?",
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
      @php
        $checkInUpdateRoute = (str_starts_with(request()->route()->getName() ?? '', 'admin.')) 
          ? 'admin.bookings.update-checkin' 
          : 'reception.bookings.update-checkin';
      @endphp
      fetch('{{ route($checkInUpdateRoute, ":id") }}'.replace(':id', bookingId), {
        method: 'PUT',
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ check_in_status: checkInStatus })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          swal({
            title: "Success!",
            text: data.message || "Guest checked in successfully!",
            type: "success",
            confirmButtonColor: "#28a745"
          }, function() {
            location.reload();
          });
        } else {
          swal({
            title: "Error!",
            text: data.message || "Failed to check in guest.",
            type: "error",
            confirmButtonColor: "#940000"
          });
        }
      })
      .catch(error => {
        console.error('Error:', error);
        swal({
          title: "Error!",
          text: "An error occurred while checking in the guest.",
          type: "error",
          confirmButtonColor: "#940000"
        });
      });
    }
  });
}

function saveNotes() {
  const bookingId = document.getElementById('notes_booking_id').value;
  const notes = document.getElementById('admin_notes').value;
  
  fetch('{{ url("/manager/bookings") }}/' + bookingId + '/notes', {
    method: 'PUT',
    headers: {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ admin_notes: notes })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      swal({
        title: "Saved!",
        text: data.message || "Admin notes saved successfully.",
        type: "success",
        confirmButtonColor: "#940000"
      }, function() {
        $('#notesModal').modal('hide');
        location.reload();
      });
    } else {
      swal({
        title: "Error!",
        text: data.message || "Failed to save notes.",
        type: "error",
        confirmButtonColor: "#940000"
      });
    }
  })
  .catch(error => {
    console.error('Error:', error);
    swal({
      title: "Error!",
      text: "An error occurred while saving notes.",
      type: "error",
      confirmButtonColor: "#940000"
    });
  });
}

// Countdown timer for pending bookings
function updateCountdownTimers() {
  document.querySelectorAll('.countdown-timer').forEach(function(timer) {
    const expiresTimestamp = parseInt(timer.getAttribute('data-expires'));
    const now = Date.now();
    const remaining = expiresTimestamp - now;
    
    if (remaining > 0) {
      const minutes = Math.floor(remaining / 60000);
      const seconds = Math.floor((remaining % 60000) / 1000);
      timer.textContent = minutes + 'm ' + seconds + 's';
    } else {
      timer.textContent = 'Expired';
      timer.parentElement.classList.add('text-danger');
      // Reload page after 5 seconds if expired
      setTimeout(function() {
        location.reload();
      }, 5000);
    }
  });
}

// Update countdown timers every second
setInterval(updateCountdownTimers, 1000);
updateCountdownTimers();


// Send reminder function (sends both email and SMS)
function sendReminder(bookingId) {
  Swal.fire({
    title: 'Send Reminders?',
    text: 'Are you sure you want to send payment reminders via Email and SMS?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#940000',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Yes, Send Both!',
    cancelButtonText: 'Cancel'
  }).then((result) => {
    if (result.isConfirmed) {
      // Show loading
      Swal.fire({
        title: 'Sending...',
        text: 'Please wait while we send Email and SMS reminders.',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });
      
      fetch('{{ url("/manager/bookings") }}/' + bookingId + '/send-reminder', {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ reminder_type: 'both' })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          Swal.fire({
            icon: 'success',
            title: 'Reminders Sent!',
            text: data.message,
            confirmButtonColor: '#940000',
            confirmButtonText: 'OK'
          });
        } else {
          Swal.fire({
            icon: data.success === false ? 'warning' : 'error',
            title: data.success ? 'Partially Sent' : 'Failed to Send',
            text: data.message || 'Failed to send reminders.',
            confirmButtonColor: '#940000',
            confirmButtonText: 'OK'
          });
        }
      })
      .catch(error => {
        console.error('Error:', error);
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'An error occurred while sending the reminders.',
          confirmButtonColor: '#940000',
          confirmButtonText: 'OK'
        });
      });
    }
  });
}

// Manager Extension/Decrease Functions
let managerBookingData = {};

@if(($bookingType ?? 'individual') == 'corporate')
  @foreach($bookings as $group)
    @php
      $companyBookings = $group['bookings'] ?? collect();
    @endphp
    @foreach($companyBookings as $booking)
      @if($booking->check_in_status === 'checked_in' && $booking->room)
        managerBookingData[{{ $booking->id }}] = {
          id: {{ $booking->id }},
          roomPrice: {{ $booking->room->price_per_night ?? 0 }},
          currentCheckOut: '{{ $booking->check_out->format('Y-m-d') }}',
          checkIn: '{{ $booking->check_in->format('Y-m-d') }}'
        };
      @endif
    @endforeach
  @endforeach
@else
  @foreach($bookings as $booking)
    @if($booking->check_in_status === 'checked_in' && $booking->room)
      managerBookingData[{{ $booking->id }}] = {
        id: {{ $booking->id }},
        roomPrice: {{ $booking->room->price_per_night ?? 0 }},
        currentCheckOut: '{{ $booking->check_out->format('Y-m-d') }}',
        checkIn: '{{ $booking->check_in->format('Y-m-d') }}'
      };
    @endif
  @endforeach
@endif

function openManagerExtensionModal(bookingId, checkIn, currentCheckOut) {
  const bookingData = managerBookingData[bookingId];
  if (!bookingData) {
    swal("Error", "Booking information not found.", "error");
    return;
  }
  
  document.getElementById('manager_extension_booking_id').value = bookingId;
  const dateInput = document.getElementById('manager_extension_new_check_out');
  dateInput.value = '';
  dateInput.min = currentCheckOut;
  document.getElementById('manager_extension_reason').value = '';
  document.getElementById('managerExtensionCostPreview').style.display = 'none';
  document.getElementById('managerExtensionAlert').innerHTML = '';
  
  // Show modal first
  $('#managerExtensionModal').modal('show');
  
  // Attach event listeners after modal is shown
  $('#managerExtensionModal').off('shown.bs.modal').on('shown.bs.modal', function() {
    const dateInputEl = document.getElementById('manager_extension_new_check_out');
    // Remove any existing listeners
    dateInputEl.removeEventListener('change', calculateManagerExtensionCost);
    dateInputEl.removeEventListener('input', calculateManagerExtensionCost);
    $(dateInputEl).off('change input');
    
    // Add new listeners using both methods for maximum compatibility
    dateInputEl.addEventListener('change', calculateManagerExtensionCost);
    dateInputEl.addEventListener('input', calculateManagerExtensionCost);
    $(dateInputEl).on('change', calculateManagerExtensionCost);
    $(dateInputEl).on('input', calculateManagerExtensionCost);
  });
  
  // Trigger the event if modal is already shown
  if ($('#managerExtensionModal').hasClass('show')) {
    $('#managerExtensionModal').trigger('shown.bs.modal');
  }
}

function openManagerDecreaseModal(bookingId, checkIn, currentCheckOut) {
  const bookingData = managerBookingData[bookingId];
  if (!bookingData) {
    swal("Error", "Booking information not found.", "error");
    return;
  }
  
  document.getElementById('manager_decrease_booking_id').value = bookingId;
  const dateInput = document.getElementById('manager_decrease_new_check_out');
  dateInput.value = '';
  dateInput.min = checkIn;
  dateInput.max = currentCheckOut;
  document.getElementById('manager_decrease_reason').value = '';
  document.getElementById('managerDecreaseCostPreview').style.display = 'none';
  document.getElementById('managerDecreaseAlert').innerHTML = '';
  
  // Remove existing event listeners (both native and jQuery)
  dateInput.removeEventListener('change', calculateManagerDecreaseRefund);
  dateInput.removeEventListener('input', calculateManagerDecreaseRefund);
  $(dateInput).off('change input');
  
  $('#managerDecreaseModal').modal('show');
  
  // Attach event listeners after modal is shown to ensure they work
  $('#managerDecreaseModal').off('shown.bs.modal').on('shown.bs.modal', function() {
    const dateInputEl = document.getElementById('manager_decrease_new_check_out');
    // Remove any existing listeners
    dateInputEl.removeEventListener('change', calculateManagerDecreaseRefund);
    dateInputEl.removeEventListener('input', calculateManagerDecreaseRefund);
    $(dateInputEl).off('change input');
    
    // Add new listeners using both methods for maximum compatibility
    dateInputEl.addEventListener('change', calculateManagerDecreaseRefund);
    dateInputEl.addEventListener('input', calculateManagerDecreaseRefund);
    $(dateInputEl).on('change', calculateManagerDecreaseRefund);
    $(dateInputEl).on('input', calculateManagerDecreaseRefund);
  });
}

// Event listeners are attached when modals are opened for better reliability

function calculateManagerExtensionCost() {
  const bookingId = document.getElementById('manager_extension_booking_id').value;
  const bookingData = managerBookingData[bookingId];
  if (!bookingData) {
    document.getElementById('managerExtensionCostPreview').style.display = 'none';
    return;
  }
  
  const newDate = document.getElementById('manager_extension_new_check_out').value;
  if (!newDate) {
    document.getElementById('managerExtensionCostPreview').style.display = 'none';
    return;
  }
  
  const currentCheckOut = new Date(bookingData.currentCheckOut + 'T00:00:00');
  const requestedDate = new Date(newDate + 'T00:00:00');
  
  if (requestedDate <= currentCheckOut) {
    document.getElementById('managerExtensionCostPreview').style.display = 'none';
    return;
  }
  
  const diffTime = requestedDate - currentCheckOut;
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  
  if (diffDays > 0) {
    const totalCost = bookingData.roomPrice * diffDays;
    document.getElementById('managerExtensionNights').textContent = diffDays;
    document.getElementById('managerExtensionRoomPrice').textContent = bookingData.roomPrice.toFixed(2);
    document.getElementById('managerExtensionTotalCost').textContent = totalCost.toFixed(2);
    document.getElementById('managerExtensionCostPreview').style.display = 'block';
  } else {
    document.getElementById('managerExtensionCostPreview').style.display = 'none';
  }
}

function calculateManagerDecreaseRefund() {
  const bookingId = document.getElementById('manager_decrease_booking_id').value;
  const bookingData = managerBookingData[bookingId];
  if (!bookingData) {
    document.getElementById('managerDecreaseCostPreview').style.display = 'none';
    return;
  }
  
  const newDate = document.getElementById('manager_decrease_new_check_out').value;
  if (!newDate) {
    document.getElementById('managerDecreaseCostPreview').style.display = 'none';
    return;
  }
  
  const currentCheckOut = new Date(bookingData.currentCheckOut + 'T00:00:00');
  const requestedDate = new Date(newDate + 'T00:00:00');
  
  if (requestedDate >= currentCheckOut) {
    document.getElementById('managerDecreaseCostPreview').style.display = 'none';
    return;
  }
  
  const diffTime = currentCheckOut - requestedDate;
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  
  if (diffDays > 0) {
    const totalRefund = bookingData.roomPrice * diffDays;
    document.getElementById('managerDecreaseNights').textContent = diffDays;
    document.getElementById('managerDecreaseRoomPrice').textContent = bookingData.roomPrice.toFixed(2);
    document.getElementById('managerDecreaseTotalRefund').textContent = totalRefund.toFixed(2);
    document.getElementById('managerDecreaseCostPreview').style.display = 'block';
  } else {
    document.getElementById('managerDecreaseCostPreview').style.display = 'none';
  }
}

function submitManagerExtension() {
  const form = document.getElementById('managerExtensionForm');
  const alertDiv = document.getElementById('managerExtensionAlert');
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
  submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Processing...';
  
  const bookingId = document.getElementById('manager_extension_booking_id').value;
  const newCheckOut = document.getElementById('manager_extension_new_check_out').value;
  
  fetch('{{ route("admin.bookings.modify-dates", ":id") }}'.replace(':id', bookingId), {
    method: 'PUT',
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      new_check_out: newCheckOut,
      reason: document.getElementById('manager_extension_reason').value
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      swal({
        title: "Success!",
        text: data.message || "Booking extended successfully!",
        type: "success",
        confirmButtonColor: "#28a745"
      }, function() {
        $('#managerExtensionModal').modal('hide');
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
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalText;
    }
  })
  .catch(error => {
    console.error('Error:', error);
    if (alertDiv) {
      alertDiv.innerHTML = '<div class="alert alert-danger">An error occurred. Please try again.</div>';
    }
    submitBtn.disabled = false;
    submitBtn.innerHTML = originalText;
  });
}

function submitManagerDecrease() {
  const form = document.getElementById('managerDecreaseForm');
  const alertDiv = document.getElementById('managerDecreaseAlert');
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
  submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Processing...';
  
  const bookingId = document.getElementById('manager_decrease_booking_id').value;
  const newCheckOut = document.getElementById('manager_decrease_new_check_out').value;
  
  fetch('{{ route("admin.bookings.modify-dates", ":id") }}'.replace(':id', bookingId), {
    method: 'PUT',
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      new_check_out: newCheckOut,
      reason: document.getElementById('manager_decrease_reason').value
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      swal({
        title: "Success!",
        text: data.message || "Booking decreased successfully!",
        type: "success",
        confirmButtonColor: "#28a745"
      }, function() {
        $('#managerDecreaseModal').modal('hide');
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
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalText;
    }
  })
  .catch(error => {
    console.error('Error:', error);
    if (alertDiv) {
      alertDiv.innerHTML = '<div class="alert alert-danger">An error occurred. Please try again.</div>';
    }
    submitBtn.disabled = false;
    submitBtn.innerHTML = originalText;
  });
}
</script>
@endsection

