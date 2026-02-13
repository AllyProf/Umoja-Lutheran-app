@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-sign-in"></i> Check In</h1>
    <p>Check in guests for their reservations</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('reception.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Check In</a></li>
  </ul>
</div>

<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn mb-3">
        <h3 class="title">Ready for Check-In</h3>
      </div>
      
      @php
        // Determine which routes to use based on role
        $checkInRoute = ($role === 'manager') 
          ? 'admin.reservations.check-in' 
          : 'reception.reservations.check-in';
        
        // Get default check-in time from settings
        $defaultCheckInTime = \App\Models\HotelSetting::getValue('default_checkin_time', '14:00');
      @endphp
      
      <!-- Booking Type Tabs -->
      <div class="booking-tabs-wrapper mb-4">
        <ul class="nav nav-pills nav-justified" role="tablist" style="background: #f8f9fa; padding: 8px; border-radius: 8px;">
          <li class="nav-item">
            <a class="nav-link {{ ($bookingType ?? 'individual') == 'individual' ? 'active' : '' }}" 
               href="{{ route($checkInRoute, array_merge(request()->except(['type']), ['type' => 'individual'])) }}"
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
               href="{{ route($checkInRoute, array_merge(request()->except(['type']), ['type' => 'corporate'])) }}"
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
      
      <!-- Search Filter -->
      <div class="row mb-3">
        <div class="col-md-6 col-12 mb-2 mb-md-0">
          <input type="text" class="form-control" id="searchInput" placeholder="Search by reference, name, or email..." onkeyup="filterCheckIns()" oninput="filterCheckIns()" style="font-size: 16px;">
        </div>
        <div class="col-md-4 col-12 mb-2 mb-md-0">
          <input type="date" class="form-control" id="checkInDateFilter" onchange="filterCheckIns()" value="{{ request('check_in_date', today()->format('Y-m-d')) }}" style="font-size: 16px;">
        </div>
        <div class="col-md-2 col-12">
          <button class="btn btn-secondary btn-block" onclick="resetCheckInFilters()">
            <i class="fa fa-refresh"></i> Reset
          </button>
        </div>
      </div>
      
      <div class="tile-body">
        @if($bookings->count() > 0)
        <!-- Desktop Table View -->
        <div class="table-responsive">
          <table class="table table-hover table-bordered" id="checkInTable">
            <thead>
              <tr>
                <th>Booking Reference</th>
                <th>{{ ($bookingType ?? 'individual') == 'corporate' ? 'Company' : 'Guest' }}</th>
                <th>{{ ($bookingType ?? 'individual') == 'corporate' ? 'Guests' : 'Room' }}</th>
                <th>Check-in Date</th>
                <th>Check-out Date</th>
                <th>Nights</th>
                <th>Time Remaining</th>
                <th>Total Price</th>
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
                    $totalPrice = $companyBookings->sum('total_price');
                    $totalNights = $firstBooking ? $firstBooking->check_in->diffInDays($firstBooking->check_out) : 0;
                  @endphp
                  <tr class="checkin-row corporate-booking-group"
                      data-booking-ref="{{ strtolower($firstBooking->booking_reference ?? '') }}"
                      data-check-in-date="{{ $firstBooking->check_in->format('Y-m-d') ?? '' }}"
                      data-company-name="{{ strtolower($company->name ?? '') }}"
                      data-company-email="{{ strtolower($company->email ?? '') }}">
                    <td>
                      <strong>{{ $firstBooking->booking_reference ?? 'N/A' }}</strong>
                      <br><small class="text-muted">{{ $firstBooking->created_at->format('M d, Y') ?? 'N/A' }}</small>
                    </td>
                    <td>
                      @if($company)
                        <strong><i class="fa fa-building"></i> {{ $company->name }}</strong><br>
                        <small>{{ $company->email }}</small><br>
                        @if($company->phone)
                        <small>{{ $company->phone }}</small>
                        @endif
                      @else
                        <span class="text-muted">N/A</span>
                      @endif
                    </td>
                    <td>
                      <span class="badge badge-info">{{ $totalGuests }} guest{{ $totalGuests > 1 ? 's' : '' }}</span>
                      <br><small class="text-muted">{{ $companyBookings->pluck('room.room_number')->filter()->implode(', ') }}</small>
                    </td>
                    <td>{{ $firstBooking->check_in->format('M d, Y') ?? 'N/A' }}</td>
                    <td>{{ $firstBooking->check_out->format('M d, Y') ?? 'N/A' }}</td>
                    <td>{{ $totalNights }} nights</td>
                    <td>
                      @php
                        $now = \Carbon\Carbon::now();
                        $cTime = ($firstBooking->arrival_time ?? $firstBooking->room->checkin_time ?? $defaultCheckInTime);
                        $tParts = explode(':', $cTime);
                        $checkInDate = $firstBooking ? \Carbon\Carbon::parse($firstBooking->check_in)->setTime((int)$tParts[0], (int)($tParts[1] ?? 0), 0) : null;
                        
                        if ($checkInDate) {
                          $diffInDays = (int)$now->diffInDays($checkInDate, false);
                          $diffInHours = (int)$now->diffInHours($checkInDate, false);
                          $diffInMinutes = (int)$now->diffInMinutes($checkInDate, false);
                        }
                      @endphp
                      @if($checkInDate && $checkInDate->isPast())
                        @php
                          $daysOverdue = (int)$checkInDate->diffInDays($now);
                          $hoursOverdue = (int)$checkInDate->diffInHours($now);
                        @endphp
                        @if($daysOverdue >= 1)
                          <span class="badge badge-danger" title="Check-in date was {{ $daysOverdue }} day(s) ago">
                            <i class="fa fa-exclamation-triangle"></i> Overdue ({{ $daysOverdue }} day{{ $daysOverdue > 1 ? 's' : '' }})
                          </span>
                        @else
                          @if($hoursOverdue > 0)
                            <span class="badge badge-danger" title="Check-in date was {{ $hoursOverdue }} hour(s) ago">
                              <i class="fa fa-exclamation-triangle"></i> Overdue ({{ $hoursOverdue }} hour{{ $hoursOverdue > 1 ? 's' : '' }})
                            </span>
                          @else
                            @php $minutesOverdue = (int)$checkInDate->diffInMinutes($now); @endphp
                            <span class="badge badge-danger" title="Check-in date was {{ $minutesOverdue }} minute(s) ago">
                              <i class="fa fa-exclamation-triangle"></i> Overdue ({{ $minutesOverdue }} min{{ $minutesOverdue > 1 ? 's' : '' }})
                            </span>
                          @endif
                        @endif
                      @elseif($checkInDate && $diffInDays == 0)
                        @if($diffInHours > 0)
                          <span class="badge badge-warning">
                            <i class="fa fa-clock-o"></i> 
                            @php $remMin = $diffInMinutes % 60; @endphp
                            {{ $diffInHours }}h {{ $remMin > 0 ? $remMin.'m' : '' }} remaining
                          </span>
                        @elseif($diffInMinutes > 0)
                          <span class="badge badge-warning">
                            <i class="fa fa-clock-o"></i> {{ $diffInMinutes }} minute(s) remaining
                          </span>
                        @else
                          <span class="badge badge-success">
                            <i class="fa fa-check-circle"></i> Check-in Time!
                          </span>
                        @endif
                      @elseif($checkInDate && $diffInDays > 0)
                        <span class="badge badge-info">
                          <i class="fa fa-calendar"></i> {{ $diffInDays }} day(s) remaining
                        </span>
                      @else
                        <span class="text-muted">N/A</span>
                      @endif
                    </td>
                    <td>
                      <strong>${{ number_format($totalPrice, 2) }}</strong>
                      <br><small class="text-muted">{{ number_format($totalPrice * $exchangeRate, 2) }} TZS</small>
                    </td>
                    <td>
                      <button class="btn btn-sm btn-primary" onclick="checkInCompanyGroup({{ $company->id ?? 0 }})" title="Check In All Guests">
                        <i class="fa fa-sign-in"></i> Check In
                      </button>
                    </td>
                  </tr>
                @endforeach
              @else
                @foreach($bookings as $booking)
                  <tr class="checkin-row" 
                      data-booking-ref="{{ strtolower($booking->booking_reference) }}"
                      data-guest-name="{{ strtolower($booking->guest_name) }}"
                      data-guest-email="{{ strtolower($booking->guest_email) }}"
                      data-check-in-date="{{ $booking->check_in->format('Y-m-d') }}">
                    <td><strong>{{ $booking->booking_reference }}</strong></td>
                    <td>
                      <strong>{{ $booking->guest_name }}</strong><br>
                      <small>{{ $booking->guest_email }}</small><br>
                      @if($booking->guest_phone)
                      <small>{{ $booking->guest_phone }}</small>
                      @endif
                    </td>
                    <td>
                      <span class="badge badge-primary">{{ $booking->room->room_type ?? 'N/A' }}</span><br>
                      <small>{{ $booking->room->room_number ?? 'N/A' }}</small>
                    </td>
                    <td>{{ $booking->check_in->format('M d, Y') }}</td>
                    <td>{{ $booking->check_out->format('M d, Y') }}</td>
                    <td>{{ $booking->check_in->diffInDays($booking->check_out) }} nights</td>
                    <td>
                      @php
                        $now = \Carbon\Carbon::now();
                        $cTime = ($booking->arrival_time ?? $booking->room->checkin_time ?? $defaultCheckInTime);
                        $tParts = explode(':', $cTime);
                        $checkInDate = \Carbon\Carbon::parse($booking->check_in)->setTime((int)$tParts[0], (int)($tParts[1] ?? 0), 0);
                        $diffInDays = (int)$now->diffInDays($checkInDate, false);
                        $diffInHours = (int)$now->diffInHours($checkInDate, false);
                        $diffInMinutes = (int)$now->diffInMinutes($checkInDate, false);
                      @endphp
                      @if($checkInDate->isPast())
                        @php
                          $daysOverdue = (int)$checkInDate->diffInDays($now);
                          $hoursOverdue = (int)$checkInDate->diffInHours($now);
                        @endphp
                        @if($daysOverdue >= 1)
                          <span class="badge badge-danger" title="Check-in date was {{ $daysOverdue }} day(s) ago">
                            <i class="fa fa-exclamation-triangle"></i> Overdue ({{ $daysOverdue }} day{{ $daysOverdue > 1 ? 's' : '' }})
                          </span>
                        @else
                          @if($hoursOverdue > 0)
                            <span class="badge badge-danger" title="Check-in date was {{ $hoursOverdue }} hour(s) ago">
                              <i class="fa fa-exclamation-triangle"></i> Overdue ({{ $hoursOverdue }} hour{{ $hoursOverdue > 1 ? 's' : '' }})
                            </span>
                          @else
                            @php $minutesOverdue = (int)$checkInDate->diffInMinutes($now); @endphp
                            <span class="badge badge-danger" title="Check-in date was {{ $minutesOverdue }} minute(s) ago">
                              <i class="fa fa-exclamation-triangle"></i> Overdue ({{ $minutesOverdue }} min{{ $minutesOverdue > 1 ? 's' : '' }})
                            </span>
                          @endif
                        @endif
                      @elseif($diffInDays == 0)
                        @if($diffInHours > 0)
                          <span class="badge badge-warning">
                            <i class="fa fa-clock-o"></i> 
                            @php $remMin = $diffInMinutes % 60; @endphp
                            {{ $diffInHours }}h {{ $remMin > 0 ? $remMin.'m' : '' }} remaining
                          </span>
                        @elseif($diffInMinutes > 0)
                          <span class="badge badge-warning">
                            <i class="fa fa-clock-o"></i> {{ $diffInMinutes }} minute(s) remaining
                          </span>
                        @else
                          <span class="badge badge-success">
                            <i class="fa fa-check-circle"></i> Check-in Time!
                          </span>
                        @endif
                      @elseif($diffInDays == 1)
                        <span class="badge badge-info">
                          <i class="fa fa-calendar"></i> Tomorrow
                        </span>
                      @else
                        <span class="badge badge-primary">
                          <i class="fa fa-calendar"></i> {{ $diffInDays }} day(s) remaining
                        </span>
                      @endif
                    </td>
                    <td>
                      <strong>${{ number_format($booking->total_price, 2) }}</strong><br>
                      <small>{{ number_format($booking->total_price * $exchangeRate, 2) }} TZS</small>
                    </td>
                    <td>
                      <button class="btn btn-sm btn-success" onclick="checkInGuest({{ $booking->id }}, '{{ $booking->booking_reference }}')">
                        <i class="fa fa-sign-in"></i> Check In
                      </button>
                      <button class="btn btn-sm btn-info" onclick="viewBookingDetails({{ $booking->id }}, '{{ $booking->booking_reference }}')">
                        <i class="fa fa-eye"></i> View
                      </button>
                    </td>
                  </tr>
                @endforeach
              @endif
            </tbody>
          </table>
        </div>
        
        <!-- Mobile Card View -->
        <div class="mobile-checkin-cards">
          @if(($bookingType ?? 'individual') == 'corporate')
            @foreach($bookings as $group)
              @php
                $company = $group['company'] ?? null;
                $companyBookings = $group['bookings'] ?? collect();
                $firstBooking = $group['first_booking'] ?? $companyBookings->first();
                $totalGuests = $companyBookings->count();
                $totalPrice = $companyBookings->sum('total_price');
                $totalNights = $firstBooking ? $firstBooking->check_in->diffInDays($firstBooking->check_out) : 0;
                $now = \Carbon\Carbon::now();
                $cTime = ($firstBooking->arrival_time ?? $firstBooking->room->checkin_time ?? $defaultCheckInTime);
                $tParts = explode(':', $cTime);
                $checkInDate = $firstBooking ? \Carbon\Carbon::parse($firstBooking->check_in)->setTime((int)$tParts[0], (int)($tParts[1] ?? 0), 0) : null;
                
                if ($checkInDate) {
                  $diffInDays = (int)$now->diffInDays($checkInDate, false);
                  $diffInHours = (int)$now->diffInHours($checkInDate, false);
                  $diffInMinutes = (int)$now->diffInMinutes($checkInDate, false);
                }
              @endphp
              <div class="mobile-checkin-card checkin-row corporate-booking-group" 
                   style="background: white; border: 1px solid #dee2e6; border-radius: 8px; padding: 15px; margin-bottom: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"
                   data-booking-ref="{{ strtolower($firstBooking->booking_reference ?? '') }}"
                   data-check-in-date="{{ $firstBooking->check_in->format('Y-m-d') ?? '' }}"
                   data-company-name="{{ strtolower($company->name ?? '') }}"
                   data-company-email="{{ strtolower($company->email ?? '') }}">
                <div style="border-bottom: 2px solid #940000; padding-bottom: 10px; margin-bottom: 15px;">
                  <h5 style="color: #940000; font-size: 18px; font-weight: 600; margin: 0;"><i class="fa fa-building"></i> {{ $company->name ?? 'N/A' }}</h5>
                  <div style="font-size: 14px; color: #6c757d; margin-top: 5px;">Ref: {{ $firstBooking->booking_reference ?? 'N/A' }}</div>
                  <span class="badge badge-info mt-2">{{ $totalGuests }} guest{{ $totalGuests > 1 ? 's' : '' }}</span>
                </div>
                
                <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                  <span style="font-weight: 600; color: #495057; font-size: 14px; flex: 0 0 40%;">Guests:</span>
                  <span style="text-align: right; flex: 1;">{{ $totalGuests }} guest{{ $totalGuests > 1 ? 's' : '' }}</span>
                </div>
                
                <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                  <span style="font-weight: 600; color: #495057; font-size: 14px; flex: 0 0 40%;">Rooms:</span>
                  <span style="text-align: right; flex: 1;">{{ $companyBookings->pluck('room.room_number')->filter()->implode(', ') }}</span>
                </div>
                
                <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                  <span style="font-weight: 600; color: #495057; font-size: 14px; flex: 0 0 40%;">Check-in Date:</span>
                  <span style="text-align: right; flex: 1;">{{ $firstBooking->check_in->format('M d, Y') ?? 'N/A' }}</span>
                </div>
                
                <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                  <span style="font-weight: 600; color: #495057; font-size: 14px; flex: 0 0 40%;">Check-out Date:</span>
                  <span style="text-align: right; flex: 1;">{{ $firstBooking->check_out->format('M d, Y') ?? 'N/A' }}</span>
                </div>
                
                <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                  <span style="font-weight: 600; color: #495057; font-size: 14px; flex: 0 0 40%;">Nights:</span>
                  <span style="text-align: right; flex: 1;">{{ $totalNights }} nights</span>
                </div>
                
                <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                  <span style="font-weight: 600; color: #495057; font-size: 14px; flex: 0 0 40%;">Total Price:</span>
                  <span style="text-align: right; flex: 1;">
                    <strong>${{ number_format($totalPrice, 2) }}</strong><br>
                    <small>{{ number_format($totalPrice * $exchangeRate, 2) }} TZS</small>
                  </span>
                </div>
                
                <div style="margin-top: 15px; display: flex; gap: 10px;">
                  <button class="btn btn-sm btn-primary btn-block" onclick="checkInCompanyGroup({{ $company->id ?? 0 }})" style="flex: 1;">
                    <i class="fa fa-sign-in"></i> Check In All
                  </button>
                </div>
              </div>
            @endforeach
          @else
            @foreach($bookings as $booking)
              @php
                $now = \Carbon\Carbon::now();
                $cTime = ($booking->arrival_time ?? $booking->room->checkin_time ?? $defaultCheckInTime);
                $tParts = explode(':', $cTime);
                $checkInDate = \Carbon\Carbon::parse($booking->check_in)->setTime((int)$tParts[0], (int)($tParts[1] ?? 0), 0);
                $diffInDays = (int)$now->diffInDays($checkInDate, false);
                $diffInHours = (int)$now->diffInHours($checkInDate, false);
                $diffInMinutes = (int)$now->diffInMinutes($checkInDate, false);
              @endphp
              <div class="mobile-checkin-card checkin-row" 
                   style="background: white; border: 1px solid #dee2e6; border-radius: 8px; padding: 15px; margin-bottom: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"
                   data-booking-ref="{{ strtolower($booking->booking_reference) }}"
                   data-guest-name="{{ strtolower($booking->guest_name) }}"
                   data-guest-email="{{ strtolower($booking->guest_email) }}"
                   data-check-in-date="{{ $booking->check_in->format('Y-m-d') }}">
                <div style="border-bottom: 2px solid #940000; padding-bottom: 10px; margin-bottom: 15px;">
                  <h5 style="color: #940000; font-size: 18px; font-weight: 600; margin: 0;">{{ $booking->guest_name }}</h5>
                  <div style="font-size: 14px; color: #6c757d; margin-top: 5px;">Ref: {{ $booking->booking_reference }}</div>
                </div>
            
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
              <span style="font-weight: 600; color: #495057; font-size: 14px; flex: 0 0 40%;">Room:</span>
              <span style="text-align: right; flex: 1;">
                <span class="badge badge-primary">{{ $booking->room->room_type ?? 'N/A' }}</span>
                <br><small>{{ $booking->room->room_number ?? 'N/A' }}</small>
              </span>
            </div>
            
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
              <span style="font-weight: 600; color: #495057; font-size: 14px; flex: 0 0 40%;">Check-in Date:</span>
              <span style="text-align: right; flex: 1;">{{ $booking->check_in->format('M d, Y') }}</span>
            </div>
            
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
              <span style="font-weight: 600; color: #495057; font-size: 14px; flex: 0 0 40%;">Check-out Date:</span>
              <span style="text-align: right; flex: 1;">{{ $booking->check_out->format('M d, Y') }}</span>
            </div>
            
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
              <span style="font-weight: 600; color: #495057; font-size: 14px; flex: 0 0 40%;">Nights:</span>
              <span style="text-align: right; flex: 1;">{{ $booking->check_in->diffInDays($booking->check_out) }} nights</span>
            </div>
            
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
              <span style="font-weight: 600; color: #495057; font-size: 14px; flex: 0 0 40%;">Time Remaining:</span>
              <span style="text-align: right; flex: 1;">
                @if($checkInDate->isPast())
                  @php
                    $daysOverdue = (int)$checkInDate->diffInDays($now);
                    $hoursOverdue = (int)$checkInDate->diffInHours($now);
                  @endphp
                  @if($daysOverdue >= 1)
                    <span class="badge badge-danger" title="Check-in date was {{ $daysOverdue }} day(s) ago">
                      <i class="fa fa-exclamation-triangle"></i> Overdue ({{ $daysOverdue }} day{{ $daysOverdue > 1 ? 's' : '' }})
                    </span>
                  @else
                    @if($hoursOverdue > 0)
                      <span class="badge badge-danger" title="Check-in date was {{ $hoursOverdue }} hour(s) ago">
                        <i class="fa fa-exclamation-triangle"></i> Overdue ({{ $hoursOverdue }} hour{{ $hoursOverdue > 1 ? 's' : '' }})
                      </span>
                    @else
                      @php $minutesOverdue = (int)$checkInDate->diffInMinutes($now); @endphp
                      <span class="badge badge-danger" title="Check-in date was {{ $minutesOverdue }} minute(s) ago">
                        <i class="fa fa-exclamation-triangle"></i> Overdue ({{ $minutesOverdue }} min{{ $minutesOverdue > 1 ? 's' : '' }})
                      </span>
                    @endif
                  @endif
                @elseif($diffInDays == 0)
                  @if($diffInHours > 0)
                    <span class="badge badge-warning">
                      <i class="fa fa-clock-o"></i> 
                      @php $remMin = $diffInMinutes % 60; @endphp
                      {{ $diffInHours }}h {{ $remMin > 0 ? $remMin.'m' : '' }} remaining
                    </span>
                  @elseif($diffInMinutes > 0)
                    <span class="badge badge-warning">
                      <i class="fa fa-clock-o"></i> {{ $diffInMinutes }} minute(s) remaining
                    </span>
                  @else
                    <span class="badge badge-success">
                      <i class="fa fa-check-circle"></i> Check-in Time!
                    </span>
                  @endif
                @elseif($diffInDays == 1)
                  <span class="badge badge-info">
                    <i class="fa fa-calendar"></i> Tomorrow
                  </span>
                @else
                  <span class="badge badge-primary">
                    <i class="fa fa-calendar"></i> {{ $diffInDays }} day(s) remaining
                  </span>
                @endif
              </span>
            </div>
            
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
              <span style="font-weight: 600; color: #495057; font-size: 14px; flex: 0 0 40%;">Total Price:</span>
              <span style="text-align: right; flex: 1;">
                <strong>${{ number_format($booking->total_price, 2) }}</strong><br>
                <small>{{ number_format($booking->total_price * $exchangeRate, 2) }} TZS</small>
              </span>
            </div>
            
            <div style="padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
              <span style="font-weight: 600; color: #495057; font-size: 14px; display: block; margin-bottom: 5px;">Email:</span>
              <span style="font-size: 13px; color: #666;">{{ $booking->guest_email }}</span>
            </div>
            
            @if($booking->guest_phone)
            <div style="padding: 10px 0;">
              <span style="font-weight: 600; color: #495057; font-size: 14px; display: block; margin-bottom: 5px;">Phone:</span>
              <span style="font-size: 13px; color: #666;">{{ $booking->guest_phone }}</span>
            </div>
            @endif
            
            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #dee2e6; display: flex; gap: 8px; flex-wrap: wrap;">
              <button class="btn btn-sm btn-success" onclick="checkInGuest({{ $booking->id }}, '{{ $booking->booking_reference }}')" style="flex: 1; min-width: calc(50% - 4px);">
                <i class="fa fa-sign-in"></i> Check In
              </button>
              <button class="btn btn-sm btn-info" onclick="viewBookingDetails({{ $booking->id }}, '{{ $booking->booking_reference }}')" style="flex: 1; min-width: calc(50% - 4px);">
                <i class="fa fa-eye"></i> View
              </button>
            </div>
          </div>
          @endforeach
          @endif
        </div>
        
        <div class="d-flex justify-content-center mt-3" id="paginationContainer">
          {{ $bookings->links() }}
        </div>
        @else
        <div class="text-center" style="padding: 50px;">
          <i class="fa fa-sign-in fa-5x text-muted mb-3"></i>
          <h3>No Check-Ins Available</h3>
          <p class="text-muted">No bookings ready for check-in at this time.</p>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Booking Details Modal -->
<div class="modal fade" id="bookingDetailsModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background: #940000; color: white;">
        <h5 class="modal-title"><i class="fa fa-calendar-check-o"></i> Booking Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="bookingDetailsContent">
        <div class="text-center">
          <i class="fa fa-spinner fa-spin fa-2x"></i>
          <p>Loading booking details...</p>
        </div>
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
<style>
/* Mobile Responsive Styles */
@media (max-width: 767px) {
  /* Search Filter */
  #searchInput,
  #checkInDateFilter {
    margin-bottom: 10px;
  }
  
  /* Table - Hide on Mobile */
  #checkInTable {
    display: none;
  }
  
  /* Mobile Cards - Show on Mobile */
  .mobile-checkin-cards {
    display: block;
  }
  
  /* Pagination */
  .pagination {
    justify-content: center;
    flex-wrap: wrap;
  }
  
  .pagination .page-link {
    padding: 8px 12px;
    font-size: 14px;
  }
  
  /* Modal - Mobile */
  .modal-dialog.modal-lg {
    max-width: calc(100% - 20px);
    margin: 10px;
  }
  
  .modal-body .row .col-md-6 {
    flex: 0 0 100%;
    max-width: 100%;
    margin-bottom: 20px;
  }
  
  .modal-footer {
    flex-direction: column;
  }
  
  .modal-footer .btn {
    width: 100%;
    margin-bottom: 10px;
  }
  
  .modal-footer .btn:last-child {
    margin-bottom: 0;
  }
}

/* Desktop - Hide mobile cards */
@media (min-width: 768px) {
  .mobile-checkin-cards {
    display: none;
  }
  
  #checkInTable {
    display: table;
  }
}

/* Very Small Screens */
@media (max-width: 480px) {
  .mobile-checkin-card {
    padding: 12px !important;
  }
  
  .mobile-checkin-card h5 {
    font-size: 16px !important;
  }
  
  .mobile-checkin-card .btn {
    flex: 0 0 100% !important;
    min-width: 100% !important;
    margin-bottom: 8px;
  }
  
  .mobile-checkin-card .btn:last-child {
    margin-bottom: 0;
  }
}
</style>
<script>
function checkInGuest(bookingId, bookingReference) {
    swal({
        title: "Check In Guest?",
        text: "Are you sure you want to check in booking " + bookingReference + "?",
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
                $checkInRoute = ($role === 'manager') ? 'admin.bookings.update-checkin' : 'reception.bookings.update-checkin';
            @endphp
            fetch('{{ route($checkInRoute, ":id") }}'.replace(':id', bookingId), {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    check_in_status: 'checked_in'
                })
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

function checkInCompanyGroup(companyId) {
    console.log('checkInCompanyGroup called with companyId:', companyId); // Debug log
    
    if (!companyId || companyId === 0) {
        swal({
            title: "Error!",
            text: "Invalid company ID: " + companyId,
            type: "error",
            confirmButtonColor: "#d33"
        });
        return;
    }

    swal({
        title: "Check In All Guests?",
        text: "Are you sure you want to check in all guests for this company booking?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Check In All!",
        cancelButtonText: "Cancel",
        closeOnConfirm: false,
        showLoaderOnConfirm: true
    }, function(isConfirm) {
        if (isConfirm) {
            @php
                $checkInRoute = ($role === 'manager') ? 'admin.bookings.update-checkin' : 'reception.bookings.update-checkin';
            @endphp
            
            // First, fetch all bookings for this company
            // Route is under /manager prefix, so use the full path
            const companyBookingsUrl = '{{ url("/manager/bookings/company") }}/' + companyId;
            fetch(companyBookingsUrl, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    return response.text().then(text => {
                        throw new Error('Expected JSON but got: ' + contentType + '. Response: ' + text.substring(0, 200));
                    });
                }
                
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Network response was not ok: ' + response.status);
                    });
                }
                
                return response.json();
            })
            .then(data => {
                console.log('Company bookings data:', data); // Debug log
                
                if (!data.success) {
                    swal({
                        title: "Error!",
                        text: data.message || "Failed to fetch company bookings.",
                        type: "error",
                        confirmButtonColor: "#d33"
                    });
                    return;
                }
                
                if (!data.bookings || data.bookings.length === 0) {
                    swal({
                        title: "Info",
                        text: "No bookings found for this company. Please verify the company ID.",
                        type: "info",
                        confirmButtonColor: "#17a2b8"
                    });
                    return;
                }
                
                // Filter bookings that are pending check-in
                const pendingBookings = data.bookings.filter(booking => 
                    booking.check_in_status === 'pending' || booking.check_in_status === null
                );
                
                if (pendingBookings.length === 0) {
                    swal({
                        title: "Info",
                        text: "All guests for this company are already checked in.",
                        type: "info",
                        confirmButtonColor: "#17a2b8"
                    });
                    return;
                }
                    
                    // Check in all bookings sequentially
                    let completed = 0;
                    let failed = 0;
                    const total = pendingBookings.length;
                    
                    pendingBookings.forEach((booking, index) => {
                        fetch('{{ route($checkInRoute, ":id") }}'.replace(':id', booking.id), {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                check_in_status: 'checked_in'
                            })
                        })
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                completed++;
                            } else {
                                failed++;
                            }
                            
                            // Check if all requests are done
                            if (completed + failed === total) {
                                if (failed === 0) {
                                    swal({
                                        title: "Success!",
                                        text: `All ${completed} guest(s) checked in successfully!`,
                                        type: "success",
                                        confirmButtonColor: "#28a745"
                                    }, function() {
                                        location.reload();
                                    });
                                } else {
                                    swal({
                                        title: "Partial Success",
                                        text: `${completed} guest(s) checked in successfully, ${failed} failed.`,
                                        type: "warning",
                                        confirmButtonColor: "#ffc107"
                                    }, function() {
                                        location.reload();
                                    });
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error checking in booking:', booking.id, error);
                            failed++;
                            if (completed + failed === total) {
                                swal({
                                    title: "Partial Success",
                                    text: `${completed} guest(s) checked in successfully, ${failed} failed.`,
                                    type: "warning",
                                    confirmButtonColor: "#ffc107"
                                }, function() {
                                    location.reload();
                                });
                            }
                        });
                    });
            })
            .catch(error => {
                console.error('Error:', error);
                swal({
                    title: "Error!",
                    text: "Failed to fetch company bookings. Please try again.",
                    type: "error",
                    confirmButtonColor: "#d33"
                });
            });
        }
    });
}

function viewBookingDetails(bookingId, bookingRef) {
  $('#bookingDetailsModal').modal('show');
  document.getElementById('bookingDetailsContent').innerHTML = `
    <div class="text-center">
      <i class="fa fa-spinner fa-spin fa-2x"></i>
      <p>Loading booking details...</p>
    </div>
  `;
  
  @php
    // Determine which route to use based on role
    $showBookingRoute = ($role === 'manager') 
      ? 'admin.bookings.show' 
      : 'reception.bookings.show';
  @endphp
  fetch('{{ route($showBookingRoute, ":id") }}'.replace(':id', bookingId), {
    method: 'GET',
    headers: {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'X-Requested-With': 'XMLHttpRequest'
    }
  })
  .then(response => {
    if (!response.ok) {
      throw new Error('Network response was not ok: ' + response.status);
    }
    return response.json();
  })
  .then(data => {
    if (data.success) {
      const booking = data.booking;
      const room = booking.room || {};
      const exchangeRate = {{ $exchangeRate ?? 2500 }};
      const fallbackImage = '{{ asset("landing_page_assets/img/bg-img/1.jpg") }}';
      
      const detailsHtml = `
        <div class="booking-details-view">
          <div class="row">
            <div class="col-md-6">
              <h5 style="color: #940000; border-bottom: 2px solid #940000; padding-bottom: 5px; margin-bottom: 15px;"><i class="fa fa-user"></i> Guest Information</h5>
              <table class="table table-sm table-bordered">
                <tr><td width="40%"><strong>Name:</strong></td><td>${booking.guest_name || 'N/A'}</td></tr>
                <tr><td><strong>Guest ID:</strong></td><td>${booking.guest_id || 'N/A'}</td></tr>
                <tr><td><strong>Email:</strong></td><td>${booking.guest_email || 'N/A'}</td></tr>
                <tr><td><strong>Phone:</strong></td><td>${booking.guest_phone || 'N/A'}</td></tr>
                <tr><td><strong>Country:</strong></td><td>${booking.country || 'N/A'}</td></tr>
                <tr><td><strong>Number of Guests:</strong></td><td>${booking.number_of_guests || 1}</td></tr>
              </table>
            </div>
            <div class="col-md-6">
              <h5 style="color: #940000; border-bottom: 2px solid #940000; padding-bottom: 5px; margin-bottom: 15px;"><i class="fa fa-calendar"></i> Booking Information</h5>
              <table class="table table-sm table-bordered">
                <tr><td width="40%"><strong>Booking Reference:</strong></td><td><strong>${booking.booking_reference || 'N/A'}</strong></td></tr>
                <tr><td><strong>Check-in:</strong></td><td>${booking.check_in ? new Date(booking.check_in).toLocaleDateString('en-US', {year: 'numeric', month: 'long', day: 'numeric'}) : 'N/A'}</td></tr>
                <tr><td><strong>Check-out:</strong></td><td>${booking.check_out ? new Date(booking.check_out).toLocaleDateString('en-US', {year: 'numeric', month: 'long', day: 'numeric'}) : 'N/A'}</td></tr>
                <tr><td><strong>Status:</strong></td><td>
                  ${booking.status === 'pending' ? '<span class="badge badge-warning">Pending</span>' : ''}
                  ${booking.status === 'confirmed' ? '<span class="badge badge-success">Confirmed</span>' : ''}
                  ${booking.status === 'cancelled' ? '<span class="badge badge-danger">Cancelled</span>' : ''}
                </td></tr>
                <tr><td><strong>Payment Status:</strong></td><td>
                  ${booking.payment_status === 'paid' ? '<span class="badge badge-success">Paid</span>' : ''}
                  ${booking.payment_status === 'partial' ? '<span class="badge badge-info">Partial</span>' : ''}
                  ${booking.payment_status === 'pending' ? '<span class="badge badge-warning">Pending</span>' : ''}
                </td></tr>
                <tr><td><strong>Check-in Status:</strong></td><td>
                  ${booking.check_in_status === 'checked_in' ? '<span class="badge badge-success">Checked In</span>' : ''}
                  ${booking.check_in_status === 'checked_out' ? '<span class="badge badge-info">Checked Out</span>' : ''}
                  ${booking.check_in_status === 'pending' ? '<span class="badge badge-warning">Pending</span>' : ''}
                  ${booking.checked_in_at ? '<br><small>Checked in: ' + new Date(booking.checked_in_at).toLocaleDateString('en-US', {year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit'}) + '</small>' : ''}
                </td></tr>
              </table>
            </div>
          </div>
          ${room.images && room.images.length > 0 ? `
          <div class="row mt-3">
            <div class="col-md-12">
              <h5 style="color: #940000; border-bottom: 2px solid #940000; padding-bottom: 5px; margin-bottom: 15px;"><i class="fa fa-image"></i> Room Image</h5>
              <div class="text-center">
                ${(() => {
                  let imgPath = room.images[0];
                  if (imgPath.startsWith('rooms/') || imgPath.startsWith('storage/rooms/')) {
                    imgPath = imgPath.replace(/^storage\//, '');
                  } else if (!imgPath.startsWith('http') && !imgPath.startsWith('/')) {
                    imgPath = 'rooms/' + imgPath;
                  }
                  const storageBase = '{{ asset("storage") }}';
                  const imageUrl = imgPath.startsWith('http') ? imgPath : storageBase + '/' + imgPath;
                  return '<img src="' + imageUrl + '" alt="' + (room.room_type || 'Room') + '" class="img-fluid" style="max-height: 300px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);" onerror="this.src=\'' + fallbackImage + '\'">';
                })()}
              </div>
            </div>
          </div>
          ` : ''}
          <div class="row mt-3">
            <div class="col-md-6">
              <h5 style="color: #940000; border-bottom: 2px solid #940000; padding-bottom: 5px; margin-bottom: 15px;"><i class="fa fa-bed"></i> Room Information</h5>
              <table class="table table-sm table-bordered">
                <tr><td width="40%"><strong>Room Number:</strong></td><td><strong>${room.room_number || 'N/A'}</strong></td></tr>
                <tr><td><strong>Room Type:</strong></td><td>${room.room_type || 'N/A'}</td></tr>
                <tr><td><strong>Capacity:</strong></td><td>${room.capacity || 'N/A'} guests</td></tr>
                <tr><td><strong>Price per Night:</strong></td><td>$${parseFloat(room.price_per_night || 0).toFixed(2)} USD</td></tr>
              </table>
            </div>
            <div class="col-md-6">
              <h5 style="color: #940000; border-bottom: 2px solid #940000; padding-bottom: 5px; margin-bottom: 15px;"><i class="fa fa-dollar"></i> Payment Information</h5>
              <table class="table table-sm table-bordered">
                <tr><td width="40%"><strong>Total Price:</strong></td><td><strong>$${parseFloat(booking.total_price || 0).toFixed(2)} USD</strong></td></tr>
                <tr><td><strong>Total Price (TZS):</strong></td><td><strong>${(parseFloat(booking.total_price || 0) * exchangeRate).toLocaleString()} TZS</strong></td></tr>
                <tr><td><strong>Amount Paid:</strong></td><td>${booking.amount_paid ? '$' + parseFloat(booking.amount_paid).toFixed(2) + ' USD' : 'N/A'}</td></tr>
                ${booking.payment_status === 'partial' && booking.amount_paid ? `
                <tr><td><strong>Remaining Amount:</strong></td><td><strong style="color: #dc3545;">$${parseFloat((booking.total_price || 0) - (booking.amount_paid || 0)).toFixed(2)} USD</strong></td></tr>
                <tr><td><strong>Payment Percentage:</strong></td><td><span class="badge badge-info">${parseFloat(((booking.amount_paid || 0) / (booking.total_price || 1)) * 100).toFixed(0)}%</span></td></tr>
                ` : ''}
                <tr><td><strong>Payment Method:</strong></td><td>${booking.payment_method ? booking.payment_method.charAt(0).toUpperCase() + booking.payment_method.slice(1) : 'N/A'}</td></tr>
              </table>
            </div>
          </div>
        </div>
      `;
      
      document.getElementById('bookingDetailsContent').innerHTML = detailsHtml;
    } else {
      document.getElementById('bookingDetailsContent').innerHTML = `
        <div class="alert alert-danger">
          <i class="fa fa-exclamation-triangle"></i> Failed to load booking details.
        </div>
      `;
    }
  })
  .catch(error => {
    console.error('Error:', error);
    document.getElementById('bookingDetailsContent').innerHTML = `
      <div class="alert alert-danger">
        <i class="fa fa-exclamation-triangle"></i> An error occurred while loading booking details: ${error.message || 'Unknown error'}
      </div>
    `;
  });
}

// Real-time filtering functions
function filterCheckIns() {
  const searchInput = document.getElementById('searchInput').value.toLowerCase();
  const checkInDateFilter = document.getElementById('checkInDateFilter').value;
  
  // Filter both table rows and mobile cards
  const rows = document.querySelectorAll('.checkin-row');
  let visibleCount = 0;
  
  rows.forEach(row => {
    const bookingRef = row.getAttribute('data-booking-ref') || '';
    const guestName = row.getAttribute('data-guest-name') || '';
    const guestEmail = row.getAttribute('data-guest-email') || '';
    const checkInDate = row.getAttribute('data-check-in-date') || '';
    
    // Check if this is a corporate booking
    const isCorporate = row.classList.contains('corporate-booking-group');
    
    let show = true;
    
    // Search filter
    if (searchInput && searchInput.trim() !== '') {
      const searchLower = searchInput.toLowerCase();
      let matchesSearch = false;
      
      // Check booking reference
      if (bookingRef && bookingRef.includes(searchLower)) {
        matchesSearch = true;
      }
      
      // For individual bookings, check guest name and email
      if (!isCorporate) {
        if ((guestName && guestName.includes(searchLower)) || 
            (guestEmail && guestEmail.includes(searchLower))) {
          matchesSearch = true;
        }
      } else {
        // For corporate bookings, check company name and email from data attributes
        const companyName = row.getAttribute('data-company-name') || '';
        const companyEmail = row.getAttribute('data-company-email') || '';
        if ((companyName && companyName.includes(searchLower)) || 
            (companyEmail && companyEmail.includes(searchLower))) {
          matchesSearch = true;
        } else {
          // Fallback: check row text content
          const rowText = row.textContent || row.innerText || '';
          if (rowText.toLowerCase().includes(searchLower)) {
            matchesSearch = true;
          }
        }
      }
      
      if (!matchesSearch) {
        show = false;
      }
    }
    
    // Date filter
    if (checkInDateFilter && checkInDateFilter.trim() !== '') {
      if (checkInDate !== checkInDateFilter) {
        show = false;
      }
    }
    
    // Show/hide row
    if (show) {
      row.style.display = '';
      visibleCount++;
    } else {
      row.style.display = 'none';
    }
  });
  
  // Show/hide "no results" message
  const noResultsMsg = document.getElementById('noResultsMessage');
  if (visibleCount === 0 && rows.length > 0 && (searchInput || checkInDateFilter)) {
    if (!noResultsMsg) {
      const tileBody = document.querySelector('.tile-body');
      const msgDiv = document.createElement('div');
      msgDiv.id = 'noResultsMessage';
      msgDiv.className = 'text-center';
      msgDiv.style.padding = '50px';
      msgDiv.innerHTML = `
        <i class="fa fa-search fa-3x text-muted mb-3"></i>
        <p class="text-muted">No bookings found matching your search criteria.</p>
      `;
      tileBody.appendChild(msgDiv);
    } else {
      noResultsMsg.style.display = 'block';
    }
  } else {
    if (noResultsMsg) {
      noResultsMsg.style.display = 'none';
    }
  }
  
  // Hide pagination when filtering
  const paginationContainer = document.getElementById('paginationContainer');
  if (paginationContainer) {
    if (searchInput || checkInDateFilter) {
      paginationContainer.style.display = 'none';
    } else {
      paginationContainer.style.display = 'flex';
    }
  }
}

function resetCheckInFilters() {
  document.getElementById('searchInput').value = '';
  document.getElementById('checkInDateFilter').value = '{{ today()->format('Y-m-d') }}';
  filterCheckIns();
}

// Initialize filters on page load
document.addEventListener('DOMContentLoaded', function() {
  // Set initial date filter value if not set
  const dateFilter = document.getElementById('checkInDateFilter');
  if (dateFilter && !dateFilter.value) {
    dateFilter.value = '{{ today()->format('Y-m-d') }}';
  }
});
</script>
@endsection





