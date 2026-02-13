@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-dashboard"></i> Manager Dashboard</h1>
    <p>Welcome back, {{ $userName ?? 'Manager' }}!</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
  </ul>
</div>

@php
  $stats = $stats ?? [];
  $recentBookings = $recentBookings ?? collect();
  $revenueData = $revenueData ?? [];
  $bookingStatusData = $bookingStatusData ?? [];
@endphp

<!-- Main Statistics Cards (4 Cards Only) -->
<div class="row mb-3">
  <div class="col-md-6 col-lg-3">
    <div class="widget-small primary coloured-icon">
      <i class="icon fa fa-users fa-3x"></i>
      <div class="info">
        <h4>Total Active Guests</h4>
        <p><b>{{ $stats['active_guests'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-lg-3">
    <div class="widget-small info coloured-icon">
      <i class="icon fa fa-bed fa-3x"></i>
      <div class="info">
        <h4>Total Rooms</h4>
        <p><b>{{ $stats['total_rooms'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-lg-3">
    <div class="widget-small warning coloured-icon">
      <i class="icon fa fa-calendar fa-3x"></i>
      <div class="info">
        <h4>Total Bookings</h4>
        <p><b>{{ $stats['total_bookings'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-lg-3">
    <div class="widget-small danger coloured-icon">
      <i class="icon fa fa-money fa-3x"></i>
      <div class="info">
        <h4>Total Revenue</h4>
        <p><b>{{ number_format($stats['total_revenue'] ?? 0, 0) }} TZS</b></p>
      </div>
    </div>
  </div>
</div>

<div class="row mb-3">
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('chef-master.inventory') }}" style="text-decoration: none;">
            <div class="tile shadow-sm border-0 d-flex align-items-center bg-primary text-white p-3" style="border-radius: 12px;">
                <div class="mr-3 bg-white text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="fa fa-cubes fa-2x"></i>
                </div>
                <div>
                    <h5 class="mb-0">Kitchen Stock</h5>
                    <p class="mb-0 small opacity-75">View current inventory</p>
                </div>
            </div>
        </a>
    </div>

    <!-- NEW ROOM STATUS WIDGET -->
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('admin.rooms.status') }}" style="text-decoration: none;">
            <div class="tile shadow-sm border-0 d-flex align-items-center bg-secondary text-white p-3" style="border-radius: 12px; position: relative; background-color: #6c757d !important;">
                <div class="mr-3 bg-white text-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="fa fa-th fa-2x"></i>
                </div>
                <div>
                    <h5 class="mb-0">Room Status</h5>
                    <p class="mb-0 small opacity-75">{{ $stats['active_guests'] ?? 0 }} occupied rooms</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('bar-keeper.stock.index') }}" style="text-decoration: none;">
            <div class="tile shadow-sm border-0 d-flex align-items-center bg-success text-white p-3" style="border-radius: 12px;">
                <div class="mr-3 bg-white text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="fa fa-glass fa-2x"></i>
                </div>
                <div>
                    <h5 class="mb-0">Bar Stock</h5>
                    <p class="mb-0 small opacity-75">View current bar items</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('admin.purchase-requests.index') }}" style="text-decoration: none;">
            <div class="tile shadow-sm border-0 d-flex align-items-center bg-warning text-white p-3" style="border-radius: 12px; position: relative;">
                @if(($stats['pending_purchase_requests'] ?? 0) > 0)
                <span class="badge badge-danger" style="position: absolute; top: -8px; right: -8px; font-size: 14px; padding: 6px 10px; border-radius: 50%; min-width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                    {{ $stats['pending_purchase_requests'] ?? 0 }}
                </span>
                @endif
                <div class="mr-3 bg-white text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="fa fa-shopping-cart fa-2x"></i>
                </div>
                <div style="flex: 1;">
                    <h5 class="mb-0">Purchase Requests</h5>
                    <p class="mb-0 small opacity-75">
                         @if(($stats['pending_purchase_requests'] ?? 0) > 0) <strong>{{ $stats['pending_purchase_requests'] }}</strong> @else No @endif pending
                    </p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('admin.extension-requests') }}" style="text-decoration: none;">
            <div class="tile shadow-sm border-0 d-flex align-items-center bg-info text-white p-3" style="border-radius: 12px; position: relative;">
                @if(($stats['pending_extensions'] ?? 0) > 0)
                <span class="badge badge-danger" style="position: absolute; top: -8px; right: -8px; font-size: 14px; padding: 6px 10px; border-radius: 50%; min-width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                    {{ $stats['pending_extensions'] ?? 0 }}
                </span>
                @endif
                <div class="mr-3 bg-white text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="fa fa-clock-o fa-2x"></i>
                </div>
                <div style="flex: 1;">
                    <h5 class="mb-0">Extension Requests</h5>
                    <p class="mb-0 small opacity-75">
                         @if(($stats['pending_extensions'] ?? 0) > 0) <strong>{{ $stats['pending_extensions'] }}</strong> @else No @endif pending
                    </p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-3 mt-3 mt-lg-0">
        <a href="{{ route('housekeeper.inventory') }}" style="text-decoration: none;">
            <div class="tile shadow-sm border-0 d-flex align-items-center bg-danger text-white p-3" style="border-radius: 12px;">
                <div class="mr-3 bg-white text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="fa fa-building fa-2x"></i>
                </div>
                <div>
                    <h5 class="mb-0">HK Stock</h5>
                    <p class="mb-0 small opacity-75">View housekeeping inventory</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-3 mt-3 mt-lg-0">
        <a href="{{ route('admin.recipes.index') }}" style="text-decoration: none;">
            <div class="tile shadow-sm border-0 d-flex align-items-center bg-dark text-white p-3" style="border-radius: 12px; background-color: #343a40 !important;">
                <div class="mr-3 bg-white text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="fa fa-book fa-2x"></i>
                </div>
                <div>
                    <h5 class="mb-0">Recipes</h5>
                    <p class="mb-0 small opacity-75">Manage menu items</p>
                </div>
            </div>
        </a>
    </div>
</div>


<!-- All Statistics (Collapsible) -->
<div class="collapse" id="allStatistics">
  <div class="row mb-3">
    <div class="col-md-12">
      <div class="tile">
        <h3 class="tile-title"><i class="fa fa-bar-chart"></i> Detailed Statistics</h3>
        <div class="tile-body">
          <!-- Today's Statistics -->
          <div class="row mb-3">
            <div class="col-md-12">
              <h5 class="mb-3"><i class="fa fa-calendar"></i> Today's Statistics</h5>
            </div>
            <div class="col-md-6 col-lg-3">
              <div class="widget-small success coloured-icon">
                <i class="icon fa fa-calendar-check-o fa-2x"></i>
                <div class="info">
                  <h4>Today's Bookings</h4>
                  <p><b>{{ $stats['today_bookings'] ?? 0 }}</b></p>
                </div>
              </div>
            </div>
            <div class="col-md-6 col-lg-3">
              <div class="widget-small primary coloured-icon">
                <i class="icon fa fa-money fa-2x"></i>
                <div class="info">
                  <h4>Today's Revenue</h4>
                  <p><b>{{ number_format($stats['today_revenue'] ?? 0, 0) }} TZS</b></p>
                </div>
              </div>
            </div>
            <div class="col-md-6 col-lg-3">
              <div class="widget-small warning coloured-icon">
                <i class="icon fa fa-clock-o fa-2x"></i>
                <div class="info">
                  <h4>Pending Requests</h4>
                  <p><b>{{ $stats['pending_requests'] ?? 0 }}</b></p>
                </div>
              </div>
            </div>
            <div class="col-md-6 col-lg-3">
              <div class="widget-small info coloured-icon">
                <i class="icon fa fa-sign-in fa-2x"></i>
                <div class="info">
                  <h4>Active Guests</h4>
                  <p><b>{{ $stats['active_guests'] ?? 0 }}</b></p>
                </div>
              </div>
            </div>
          </div>

          <!-- Booking Status Overview -->
          <div class="row mb-3">
            <div class="col-md-12">
              <h5 class="mb-3"><i class="fa fa-list"></i> Booking Status</h5>
            </div>
            <div class="col-md-6 col-lg-3">
              <div class="widget-small primary coloured-icon">
                <i class="icon fa fa-hourglass-half fa-2x"></i>
                <div class="info">
                  <h4>Pending</h4>
                  <p><b>{{ $stats['pending_bookings'] ?? 0 }}</b></p>
                </div>
              </div>
            </div>
            <div class="col-md-6 col-lg-3">
              <div class="widget-small success coloured-icon">
                <i class="icon fa fa-check-circle fa-2x"></i>
                <div class="info">
                  <h4 style="color: #000;">Confirmed</h4>
                  <p style="color: #000;"><b>{{ $stats['confirmed_bookings'] ?? 0 }}</b></p>
                </div>
              </div>
            </div>
            <div class="col-md-6 col-lg-3">
              <div class="widget-small info coloured-icon">
                <i class="icon fa fa-check fa-2x"></i>
                <div class="info">
                  <h4>Completed</h4>
                  <p><b>{{ $stats['completed_bookings'] ?? 0 }}</b></p>
                </div>
              </div>
            </div>
            <div class="col-md-6 col-lg-3">
              <div class="widget-small danger coloured-icon">
                <i class="icon fa fa-times fa-2x"></i>
                <div class="info">
                  <h4>Cancelled</h4>
                  <p><b>{{ $stats['cancelled_bookings'] ?? 0 }}</b></p>
                </div>
              </div>
            </div>
          </div>

          <!-- Payment & Service Statistics -->
          <div class="row">
            <div class="col-md-12">
              <h5 class="mb-3"><i class="fa fa-money"></i> Payment & Service Statistics</h5>
            </div>
            <div class="col-md-6 col-lg-3">
              <div class="widget-small success coloured-icon">
                <i class="icon fa fa-check-circle fa-2x"></i>
                <div class="info">
                  <h4>Paid Bookings</h4>
                  <p><b>{{ $stats['paid_bookings'] ?? 0 }}</b></p>
                </div>
              </div>
            </div>
            <div class="col-md-6 col-lg-3">
              <div class="widget-small warning coloured-icon">
                <i class="icon fa fa-exclamation-triangle fa-2x"></i>
                <div class="info">
                  <h4>Unpaid Bookings</h4>
                  <p><b>{{ $stats['unpaid_bookings'] ?? 0 }}</b></p>
                </div>
              </div>
            </div>
            <div class="col-md-6 col-lg-3">
              <div class="widget-small info coloured-icon">
                <i class="icon fa fa-check fa-2x"></i>
                <div class="info">
                  <h4>Approved Requests</h4>
                  <p><b>{{ $stats['approved_requests'] ?? 0 }}</b></p>
                </div>
              </div>
            </div>
            <div class="col-md-6 col-lg-3">
              <div class="widget-small primary coloured-icon">
                <i class="icon fa fa-calendar fa-2x"></i>
                <div class="info">
                  <h4>This Month Bookings</h4>
                  <p><b>{{ $stats['month_bookings'] ?? 0 }}</b></p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Charts and Recent Bookings -->
<div class="row">
  <div class="col-md-6">
    <div class="tile">
      <h3 class="tile-title">Revenue Trend (Last 6 Months)</h3>
      <div class="embed-responsive embed-responsive-16by9">
        <canvas class="embed-responsive-item" id="revenueChart"></canvas>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="tile">
      <h3 class="tile-title">Booking Status Distribution</h3>
      <div class="embed-responsive embed-responsive-16by9">
        <canvas class="embed-responsive-item" id="bookingStatusChart"></canvas>
      </div>
    </div>
  </div>
</div>

<!-- Recent Bookings -->
@if($recentBookings->count() > 0)
<div class="row mt-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn">
        <h3 class="title"><i class="fa fa-calendar"></i> Recent Bookings</h3>
        <p><a class="btn btn-primary icon-btn" href="{{ route('admin.bookings.index') }}"><i class="fa fa-list"></i>View All</a></p>
      </div>
      <div class="tile-body">
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>Reference</th>
                <th>Company</th>
                <th>Guest</th>
                <th>Room</th>
                <th>Check-in</th>
                <th>Check-out</th>
                <th>Nights</th>
                <th>Total Price</th>
                <th>Payment Responsibility</th>
                <th>Status</th>
                <th>Payment</th>
                <th>Check-in</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($recentBookings as $item)
              @php
                // Check if this is a grouped corporate booking or individual booking
                $isGrouped = isset($item['is_grouped']) && $item['is_grouped'];
                if ($isGrouped) {
                  $company = $item['company'] ?? null;
                  $companyBookings = $item['bookings'] ?? collect();
                  $firstBooking = $item['first_booking'] ?? $companyBookings->first();
                  $totalGuests = $companyBookings->count();
                  $totalRooms = $companyBookings->pluck('room_id')->unique()->count();
                  $totalPrice = $companyBookings->sum('total_price');
                  $totalPaid = $companyBookings->sum('amount_paid');
                  $totalNights = $firstBooking ? $firstBooking->check_in->diffInDays($firstBooking->check_out) : 0;
                  $checkInDate = $firstBooking ? \Carbon\Carbon::parse($firstBooking->check_in) : null;
                  $checkOutDate = $firstBooking ? \Carbon\Carbon::parse($firstBooking->check_out) : null;
                  $now = \Carbon\Carbon::now();
                  if ($checkOutDate) {
                    // Use diffInDays with false to get signed difference, then floor to get whole days
                    $daysDiff = $now->diffInDays($checkOutDate, false);
                    $daysRemaining = floor($daysDiff);
                    $hoursRemaining = (int)$now->diffInHours($checkOutDate, false);
                    // If less than 1 day but more than 0, show hours
                    if ($daysRemaining == 0 && $hoursRemaining > 0) {
                      $daysRemainingText = $hoursRemaining . ' hour' . ($hoursRemaining > 1 ? 's' : '') . ' remaining';
                    } elseif ($daysRemaining > 0) {
                      $daysRemainingText = $daysRemaining . ' day' . ($daysRemaining > 1 ? 's' : '') . ' remaining';
                    } elseif ($daysRemaining < 0) {
                      $daysRemainingText = abs($daysRemaining) . ' day' . (abs($daysRemaining) > 1 ? 's' : '') . ' overdue';
                    } else {
                      $daysRemainingText = null;
                    }
                  } else {
                    $daysRemainingText = null;
                    $daysRemaining = 0;
                  }
                  
                  // Determine payment responsibility
                  $hasCompanyPaid = $companyBookings->where('payment_responsibility', 'company')->count() > 0;
                  $hasSelfPaid = $companyBookings->where('payment_responsibility', 'self')->count() > 0;
                  $paymentResponsibility = ($hasCompanyPaid && $hasSelfPaid) ? 'mixed' : ($hasCompanyPaid ? 'company' : 'self');
                  
                  // Payment status
                  $paymentPercentage = $totalPrice > 0 ? round(($totalPaid / $totalPrice) * 100) : 0;
                  $paymentStatus = $firstBooking->payment_status ?? 'pending';
                  $checkInStatus = $firstBooking->check_in_status ?? 'pending';
                  $status = $firstBooking->status ?? 'pending';
                  
                  $booking = $firstBooking; // For actions
                } else {
                  $booking = $item;
                  $isExpired = false;
                  if ($booking->expires_at) {
                    $isExpired = \Carbon\Carbon::parse($booking->expires_at)->isPast();
                  }
                  $isExpiredBooking = ($booking->status == 'pending' && $booking->payment_status == 'pending' && $isExpired) || 
                                     ($booking->status == 'cancelled' && $booking->cancellation_reason && str_contains(strtolower($booking->cancellation_reason), 'expired'));
                }
              @endphp
              <tr class="{{ (!$isGrouped && isset($isExpiredBooking) && $isExpiredBooking) ? 'table-danger' : '' }} {{ $isGrouped ? 'table-info' : '' }}">
                <td>
                  @if($isGrouped)
                    <strong>{{ $firstBooking->booking_reference ?? 'N/A' }}</strong>
                    <br><small class="text-muted">{{ $checkInDate ? $checkInDate->format('M d, Y') : 'N/A' }}</small>
                    <br><span class="badge badge-info">{{ $totalGuests }} guest{{ $totalGuests > 1 ? 's' : '' }}</span>
                  @else
                    <strong>{{ $booking->booking_reference }}</strong>
                  @endif
                </td>
                <td>
                  @if($isGrouped && $company)
                    <strong>{{ $company->name ?? 'N/A' }}</strong>
                    <br><small>{{ $company->email ?? '' }}</small>
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td>
                  @if($isGrouped)
                    <span>{{ $totalGuests }} guest{{ $totalGuests > 1 ? 's' : '' }}</span>
                  @else
                    <strong>{{ $booking->guest_name }}</strong><br>
                    <small>{{ $booking->guest_email }}</small>
                  @endif
                </td>
                <td>
                  @if($isGrouped)
                    <span>{{ $totalRooms }} room{{ $totalRooms > 1 ? 's' : '' }}</span>
                  @else
                    {{ $booking->room->room_number ?? 'N/A' }} ({{ $booking->room->room_type ?? 'N/A' }})
                  @endif
                </td>
                <td>
                  @if($isGrouped)
                    {{ $checkInDate ? $checkInDate->format('M d, Y') : 'N/A' }}
                  @else
                    {{ \Carbon\Carbon::parse($booking->check_in)->format('M d, Y') }}
                  @endif
                </td>
                <td>
                  @if($isGrouped)
                    {{ $checkOutDate ? $checkOutDate->format('M d, Y') : 'N/A' }}
                    @if(isset($daysRemainingText) && $daysRemainingText)
                      <br><small class="text-info">{{ $daysRemainingText }}</small>
                    @endif
                  @else
                    {{ \Carbon\Carbon::parse($booking->check_out)->format('M d, Y') }}
                  @endif
                </td>
                <td>
                  @if($isGrouped)
                    {{ $totalNights }} night{{ $totalNights > 1 ? 's' : '' }}
                  @else
                    {{ \Carbon\Carbon::parse($booking->check_in)->diffInDays(\Carbon\Carbon::parse($booking->check_out)) }} night(s)
                  @endif
                </td>
                <td>
                  @php
                    $lockedRate = $isGrouped ? ($firstBooking->locked_exchange_rate ?? $exchangeRate ?? 2500) : ($booking->locked_exchange_rate ?? $exchangeRate ?? 2500);
                    $displayTotalPrice = $isGrouped ? $totalPrice : ($booking->total_price ?? 0);
                    $displayTotalPaid = $isGrouped ? $totalPaid : ($booking->amount_paid ?? 0);
                    $totalPriceTsh = $displayTotalPrice * $lockedRate;
                    $totalPaidTsh = $displayTotalPaid * $lockedRate;
                  @endphp
                  <strong>{{ number_format($totalPriceTsh, 0) }} TZS</strong>
                  @if($isGrouped)
                    <br><small class="text-muted">Total for all guests</small>
                  @endif
                  @if($displayTotalPaid > 0)
                    <br><small style="font-size: 10px; color: #158cba;">Paid: {{ number_format($totalPaidTsh, 0) }} TZS</small>
                  @endif
                </td>
                <td>
                  @if($isGrouped)
                    @if($paymentResponsibility === 'mixed')
                      <span class="badge badge-warning">Mixed</span>
                      <br><small class="text-muted">Some company, some self</small>
                    @elseif($paymentResponsibility === 'company')
                      <span class="badge badge-success">Company</span>
                    @else
                      <span class="badge badge-info">Self</span>
                    @endif
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td>
                  @if($isGrouped)
                    @if($status == 'confirmed')
                      <span class="badge badge-success">Confirmed</span>
                    @elseif($status == 'pending')
                      <span class="badge badge-warning">Pending</span>
                    @elseif($status == 'completed')
                      <span class="badge badge-info">Completed</span>
                    @else
                      <span class="badge badge-danger">Cancelled</span>
                    @endif
                  @else
                    @if(isset($isExpiredBooking) && $isExpiredBooking)
                      <span class="badge badge-danger">Expired</span>
                    @elseif($booking->status == 'confirmed')
                      <span class="badge badge-success">Confirmed</span>
                    @elseif($booking->status == 'pending')
                      <span class="badge badge-warning">Pending</span>
                    @elseif($booking->status == 'completed')
                      <span class="badge badge-info">Completed</span>
                    @else
                      <span class="badge badge-danger">Cancelled</span>
                    @endif
                  @endif
                </td>
                <td>
                  @if($isGrouped)
                    @php
                      $paymentPercent = $displayTotalPrice > 0 ? round(($displayTotalPaid / $displayTotalPrice) * 100) : 0;
                    @endphp
                    @if($paymentStatus === 'paid')
                      <span class="badge badge-success">Paid</span>
                    @elseif($paymentStatus === 'partial')
                      <span class="badge badge-warning">Partial</span>
                      <br><small class="text-muted">{{ $paymentPercent }}% paid</small>
                    @else
                      <span class="badge badge-secondary">Pending</span>
                    @endif
                    @if($firstBooking->payment_method)
                      <br><small class="text-muted" style="text-transform: capitalize;">{{ $firstBooking->payment_method }}</small>
                    @endif
                  @else
                    @if($booking->payment_status === 'paid')
                      <span class="badge badge-success">Paid</span>
                    @elseif($booking->payment_status === 'partial')
                      <span class="badge badge-warning">Partial</span>
                      @php
                        $percent = $booking->total_price > 0 ? round(($booking->amount_paid / $booking->total_price) * 100) : 0;
                      @endphp
                      <br><small class="text-muted">{{ $percent }}% paid</small>
                    @else
                      <span class="badge badge-secondary">Pending</span>
                    @endif
                    @if($booking->payment_method)
                      <br><small class="text-muted" style="text-transform: capitalize;">{{ $booking->payment_method }}</small>
                    @endif
                  @endif
                </td>
                <td>
                  @if($isGrouped)
                    @if($checkInStatus === 'checked_in')
                      <span class="badge badge-success">Checked In</span>
                    @elseif($checkInStatus === 'checked_out')
                      <span class="badge badge-info">Checked Out</span>
                    @else
                      <span class="badge badge-warning">Pending</span>
                    @endif
                  @else
                    @if($booking->check_in_status === 'checked_in')
                      <span class="badge badge-success">Checked In</span>
                    @elseif($booking->check_in_status === 'checked_out')
                      <span class="badge badge-info">Checked Out</span>
                    @else
                      <span class="badge badge-warning">Pending</span>
                    @endif
                  @endif
                </td>
                <td>
                  @if($isGrouped)
                    <a href="{{ route('admin.bookings.index', ['type' => 'corporate', 'company' => $company->id ?? '']) }}" class="btn btn-sm btn-primary" title="View Company Bookings">
                      <i class="fa fa-eye"></i> View
                    </a>
                  @else
                    @if(isset($isExpiredBooking) && $isExpiredBooking)
                      <a href="{{ route('admin.bookings.index', ['status' => 'expired']) }}" class="btn btn-sm btn-warning" title="View in Expired Bookings">
                        <i class="fa fa-clock-o"></i> View Expired
                      </a>
                    @else
                      <button class="btn btn-sm btn-primary" onclick="viewBooking({{ $booking->id }})" title="View Details">
                        <i class="fa fa-eye"></i> View
                      </button>
                      @if(in_array($booking->payment_status, ['paid', 'partial']) || $booking->status == 'confirmed')
                      <a href="{{ route('payment.receipt.download', $booking) }}?download=1" class="btn btn-sm btn-success mt-1" target="_blank" title="Download Receipt">
                        <i class="fa fa-download"></i>
                      </a>
                      @endif
                    @endif
                  @endif
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

<!-- Booking Details Modal -->
<div class="modal fade" id="bookingDetailsModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Booking Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<script>
function viewBooking(bookingId) {
  // Use the correct route - determine base URL from current path
  @php
    $baseUrl = url('/');
    if (request()->is('manager/*')) {
      $bookingsBaseUrl = $baseUrl . '/manager/bookings';
    } elseif (request()->is('admin/*')) {
      $bookingsBaseUrl = $baseUrl . '/admin/bookings';
    } else {
      $bookingsBaseUrl = $baseUrl . '/admin/bookings';
    }
  @endphp
  const bookingUrl = '{{ $bookingsBaseUrl }}/' + bookingId;
  fetch(bookingUrl, {
    method: 'GET',
    headers: {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'X-Requested-With': 'XMLHttpRequest'
    }
  })
  .then(response => {
    if (!response.ok) {
      throw new Error('HTTP error! status: ' + response.status);
    }
    return response.json();
  })
  .then(data => {
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
            datePart = dateString.split('T')[0];
          } else if (dateString.includes(' ')) {
            datePart = dateString.split(' ')[0];
          } else {
            datePart = dateString;
          }
          
          const parts = datePart.split('-');
          if (parts.length !== 3) return dateString;
          
          const year = parseInt(parts[0]);
          const month = parseInt(parts[1]) - 1;
          const day = parseInt(parts[2]);
          
          if (isNaN(year) || isNaN(month) || isNaN(day)) return dateString;
          
          const date = new Date(year, month, day);
          if (isNaN(date.getTime())) return dateString;
          
          return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
        } catch (e) {
          return dateString;
        }
      }
      
      // Helper function to calculate nights
      function calculateNights(checkIn, checkOut) {
        if (!checkIn || !checkOut) return 'N/A';
        try {
          const getDatePart = (ds) => ds.includes('T') ? ds.split('T')[0] : ds.split(' ')[0];
          const [yIn, mIn, dIn] = getDatePart(checkIn).split('-');
          const [yOut, mOut, dOut] = getDatePart(checkOut).split('-');
          
          if (!yIn || !mIn || !dIn || !yOut || !mOut || !dOut) return 'N/A';
          
          const dateIn = new Date(parseInt(yIn), parseInt(mIn) - 1, parseInt(dIn));
          const dateOut = new Date(parseInt(yOut), parseInt(mOut) - 1, parseInt(dOut));
          
          if (isNaN(dateIn.getTime()) || isNaN(dateOut.getTime())) return 'N/A';
          
          const diffDays = Math.ceil((dateOut - dateIn) / (1000 * 60 * 60 * 24));
          return diffDays + ' nights';
        } catch (e) {
          return 'N/A';
        }
      }
      
      const detailsHtml = `
        <div class="booking-details-view">
          <div class="row">
            <div class="col-md-6">
              <h5><i class="fa fa-user"></i> Guest Information</h5>
              <table class="table table-sm table-bordered">
                <tr><td><strong>Name:</strong></td><td>${booking.guest_name || 'N/A'}</td></tr>
                <tr><td><strong>First Name:</strong></td><td>${booking.first_name || 'N/A'}</td></tr>
                <tr><td><strong>Last Name:</strong></td><td>${booking.last_name || 'N/A'}</td></tr>
                <tr><td><strong>Email:</strong></td><td>${booking.guest_email || 'N/A'}</td></tr>
                <tr><td><strong>Phone:</strong></td><td>${booking.guest_phone || 'N/A'}</td></tr>
                <tr><td><strong>Country:</strong></td><td>${booking.country || 'N/A'}</td></tr>
                <tr><td><strong>Country Code:</strong></td><td>${booking.country_code || 'N/A'}</td></tr>
                <tr><td><strong>Number of Guests:</strong></td><td>${booking.number_of_guests || 1}</td></tr>
              </table>
            </div>
            <div class="col-md-6">
              <h5><i class="fa fa-bed"></i> Room Information</h5>
              <table class="table table-sm table-bordered">
                <tr><td><strong>Room Type:</strong></td><td><span class="badge badge-primary">${room.room_type || 'N/A'}</span></td></tr>
                <tr><td><strong>Room Number:</strong></td><td>${room.room_number || 'N/A'}</td></tr>
                <tr><td><strong>Capacity:</strong></td><td>${room.capacity || 'N/A'} Guest(s)</td></tr>
                <tr><td><strong>Bed Type:</strong></td><td>${room.bed_type || 'N/A'}</td></tr>
                <tr><td><strong>Floor:</strong></td><td>${room.floor_location || 'N/A'}</td></tr>
              </table>
            </div>
          </div>
          <div class="row mt-3">
            <div class="col-md-6">
              <h5><i class="fa fa-calendar"></i> Booking Dates</h5>
              <table class="table table-sm table-bordered">
                <tr><td><strong>Check-in:</strong></td><td>${formatDate(booking.check_in)}</td></tr>
                <tr><td><strong>Check-out:</strong></td><td>${formatDate(booking.check_out)}</td></tr>
                <tr><td><strong>Nights:</strong></td><td>${calculateNights(booking.check_in, booking.check_out)}</td></tr>
                ${booking.arrival_time ? `<tr><td><strong>Arrival Time:</strong></td><td>${booking.arrival_time}</td></tr>` : ''}
              </table>
            </div>
            <div class="col-md-6">
              <h5><i class="fa fa-money"></i> Payment Information</h5>
              <table class="table table-sm table-bordered">
                <tr><td><strong>Total Price (TZS):</strong></td><td><strong>${(parseFloat(booking.total_price || 0) * (booking.locked_exchange_rate || {{ $exchangeRate ?? 2500 }})).toLocaleString()} TZS</strong></td></tr>
                <tr><td><strong>Amount Paid (TZS):</strong></td><td><strong>${booking.amount_paid ? (parseFloat(booking.amount_paid) * (booking.locked_exchange_rate || {{ $exchangeRate ?? 2500 }})).toLocaleString() + ' TZS' : 'N/A'}</strong></td></tr>
                <tr><td><strong>Payment Method:</strong></td><td>${booking.payment_method ? booking.payment_method.charAt(0).toUpperCase() + booking.payment_method.slice(1) : 'N/A'}</td></tr>
                <tr><td><strong>Payment Status:</strong></td><td><span class="badge badge-${booking.payment_status === 'paid' ? 'success' : booking.payment_status === 'partial' ? 'info' : booking.payment_status === 'pending' ? 'warning' : 'danger'}">${booking.payment_status ? (booking.payment_status.charAt(0).toUpperCase() + booking.payment_status.slice(1) + (booking.payment_status === 'partial' && booking.payment_percentage ? ` (${booking.payment_percentage}% paid)` : '')) : 'N/A'}</span></td></tr>
                ${booking.payment_transaction_id ? `<tr><td><strong>Transaction ID:</strong></td><td><small>${booking.payment_transaction_id}</small></td></tr>` : ''}
                ${booking.paid_at ? `<tr><td><strong>Paid At:</strong></td><td>${new Date(booking.paid_at).toLocaleString()}</td></tr>` : ''}
              </table>
            </div>
          </div>
          <div class="row mt-3">
            <div class="col-md-6">
              <h5><i class="fa fa-info-circle"></i> Booking Details</h5>
              <table class="table table-sm table-bordered">
                <tr><td><strong>Booking Reference:</strong></td><td><strong>${booking.booking_reference || 'N/A'}</strong></td></tr>
                <tr><td><strong>Status:</strong></td><td><span class="badge badge-${booking.status === 'confirmed' ? 'success' : booking.status === 'pending' ? 'warning' : booking.status === 'cancelled' ? 'danger' : 'info'}">${booking.status ? booking.status.charAt(0).toUpperCase() + booking.status.slice(1) : 'N/A'}</span></td></tr>
                <tr><td><strong>Check-in Status:</strong></td><td><span class="badge badge-${booking.check_in_status === 'checked_in' ? 'success' : booking.check_in_status === 'pending' ? 'warning' : 'info'}">${booking.check_in_status ? booking.check_in_status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) : 'N/A'}</span></td></tr>
                <tr><td><strong>Booking For:</strong></td><td>${booking.booking_for === 'me' ? 'Main Guest' : 'Someone Else'}</td></tr>
                ${booking.guest_first_name ? `<tr><td><strong>Guest First Name:</strong></td><td>${booking.guest_first_name}</td></tr>` : ''}
                ${booking.guest_last_name ? `<tr><td><strong>Guest Last Name:</strong></td><td>${booking.guest_last_name}</td></tr>` : ''}
                ${booking.main_guest_name ? `<tr><td><strong>Main Guest:</strong></td><td>${booking.main_guest_name}</td></tr>` : ''}
              </table>
            </div>
            <div class="col-md-6">
              <h5><i class="fa fa-sticky-note"></i> Additional Information</h5>
              ${booking.special_requests ? `<p class="alert alert-info"><strong>Special Requests:</strong><br>${booking.special_requests}</p>` : '<p class="text-muted">No special requests</p>'}
              ${booking.cancellation_reason ? `<p class="alert alert-danger mt-2"><strong>Cancellation Reason:</strong><br>${booking.cancellation_reason}</p>` : ''}
              ${booking.admin_notes ? `<p class="alert alert-secondary mt-2"><strong>Admin Notes:</strong><br>${booking.admin_notes}</p>` : ''}
              ${booking.expires_at ? `<p class="alert alert-warning mt-2"><strong>Expires At:</strong><br>${new Date(booking.expires_at).toLocaleString()}</p>` : ''}
              ${booking.airport_pickup_required ? `
                <div class="alert alert-info mt-2">
                  <strong>Airport Pickup Required:</strong><br>
                  <strong>Flight Number:</strong> ${booking.flight_number || 'N/A'}<br>
                  <strong>Airline:</strong> ${booking.airline || 'N/A'}<br>
                  <strong>Arrival Time:</strong> ${booking.arrival_time_pickup ? new Date(booking.arrival_time_pickup).toLocaleString() : 'N/A'}<br>
                  <strong>Passengers:</strong> ${booking.pickup_passengers || 'N/A'}<br>
                  <strong>Contact Number:</strong> ${booking.pickup_contact_number || 'N/A'}
                </div>
              ` : ''}
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
</script>
<!-- Page specific javascripts-->
<script type="text/javascript" src="{{ asset('dashboard_assets/js/plugins/chart.js') }}"></script>
<script type="text/javascript">
  // Toggle button text when statistics are shown/hidden
  $('#allStatistics').on('show.bs.collapse', function () {
    $('#toggleStatsBtn').html('<i class="fa fa-chevron-up"></i> Hide Statistics');
  });
  
  $('#allStatistics').on('hide.bs.collapse', function () {
    $('#toggleStatsBtn').html('<i class="fa fa-bar-chart"></i> See All Statistics');
  });
  
  // Chart scripts
  // Revenue Chart Data
  var revenueData = {
    labels: {!! json_encode(array_column($revenueData, 'month')) !!},
    datasets: [
      {
        label: "Revenue (TZS)",
        fillColor: "rgba(151,187,205,0.2)",
        strokeColor: "rgba(151,187,205,1)",
        pointColor: "rgba(151,187,205,1)",
        pointStrokeColor: "#fff",
        pointHighlightFill: "#fff",
        pointHighlightStroke: "rgba(151,187,205,1)",
        data: {!! json_encode(array_column($revenueData, 'revenue')) !!}
      }
    ]
  };
  
  // Booking Status Chart Data
  var bookingStatusData = [
    @foreach($bookingStatusData as $status => $count)
    {
      value: {{ $count }},
      color: @if($status == 'Pending')"#F7464A"@elseif($status == 'Confirmed')"#46BFBD"@elseif($status == 'Completed')"#FDB45C"@else"#949FB1"@endif,
      highlight: @if($status == 'Pending')"#FF5A5E"@elseif($status == 'Confirmed')"#5AD3D1"@elseif($status == 'Completed')"#FFC870"@else"#A8B3C5"@endif,
      label: "{{ $status }}"
    }@if(!$loop->last),@endif
    @endforeach
  ];
  
  // Draw Revenue Chart
  var ctxRevenue = $("#revenueChart").get(0).getContext("2d");
  var revenueChart = new Chart(ctxRevenue).Line(revenueData);
  
  // Draw Booking Status Chart
  var ctxStatus = $("#bookingStatusChart").get(0).getContext("2d");
  var statusChart = new Chart(ctxStatus).Pie(bookingStatusData);
</script>
@endsection
