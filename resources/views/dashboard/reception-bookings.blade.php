@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-calendar-check-o"></i> Bookings</h1>
    <p>View and manage all bookings</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('reception.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Bookings</a></li>
  </ul>
</div>

<!-- Statistics Cards -->
<div class="row mb-3">
  <div class="col-md-3">
    <div class="widget-small info coloured-icon">
      <i class="icon fa fa-list fa-2x"></i>
      <div class="info">
        <h4>Total</h4>
        <p><b>{{ $stats['total'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small warning coloured-icon">
      <i class="icon fa fa-clock-o fa-2x"></i>
      <div class="info">
        <h4>Pending</h4>
        <p><b>{{ $stats['pending'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small success coloured-icon">
      <i class="icon fa fa-check-circle fa-2x"></i>
      <div class="info">
        <h4 style="color: #000;">Confirmed</h4>
        <p style="color: #000;"><b>{{ $stats['confirmed'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small primary coloured-icon">
      <i class="icon fa fa-sign-in fa-2x"></i>
      <div class="info">
        <h4>Checked In</h4>
        <p><b>{{ $stats['checked_in'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
</div>

<!-- Bookings Table -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn mb-3">
        <h3 class="title">All Bookings</h3>
      </div>
      
      <!-- Filters -->
      <div class="row mb-3">
        <div class="col-md-3">
          <select class="form-control" id="statusFilter" onchange="filterBookings()">
            <option value="all">All Status</option>
            <option value="pending">Pending</option>
            <option value="confirmed">Confirmed</option>
            <option value="cancelled">Cancelled</option>
            <option value="completed">Completed</option>
          </select>
        </div>
        <div class="col-md-3">
          <select class="form-control" id="paymentStatusFilter" onchange="filterBookings()">
            <option value="all">All Payment Status</option>
            <option value="pending">Pending</option>
            <option value="paid">Paid</option>
          </select>
        </div>
        <div class="col-md-3">
          <select class="form-control" id="checkInStatusFilter" onchange="filterBookings()">
            <option value="all">All Check-in Status</option>
            <option value="pending">Pending</option>
            <option value="checked_in">Checked In</option>
            <option value="checked_out">Checked Out</option>
          </select>
        </div>
        <div class="col-md-2">
          <input type="text" class="form-control" id="searchInput" placeholder="Search by booking ref, guest name, email..." onkeyup="filterBookings()">
        </div>
        <div class="col-md-1">
          <button class="btn btn-secondary btn-block" onclick="resetBookingFilters()">
            <i class="fa fa-refresh"></i> Reset
          </button>
        </div>
      </div>
      
      <div class="tile-body">
        @if($bookings->count() > 0)
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>Booking Reference</th>
                <th>Guest</th>
                <th>Room</th>
                <th>Check-in</th>
                <th>Check-out</th>
                <th>Status</th>
                <th>Payment Status</th>
                <th>Check-in Status</th>
                <th>Exchange Rate</th>
                <th>Total Price</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($bookings as $booking)
              <tr class="booking-row" 
                  data-status="{{ $booking->status }}"
                  data-payment-status="{{ $booking->payment_status }}"
                  data-check-in-status="{{ $booking->check_in_status }}"
                  data-booking-ref="{{ strtolower($booking->booking_reference) }}"
                  data-guest-name="{{ strtolower($booking->guest_name) }}"
                  data-guest-email="{{ strtolower($booking->guest_email) }}">
                <td><strong>{{ $booking->booking_reference }}</strong></td>
                <td>
                  <strong>{{ $booking->guest_name }}</strong><br>
                  <small>{{ $booking->guest_email }}</small>
                </td>
                <td>
                  <span class="badge badge-primary">{{ $booking->room->room_type ?? 'N/A' }}</span><br>
                  <small>{{ $booking->room->room_number ?? 'N/A' }}</small>
                </td>
                <td>{{ $booking->check_in->format('M d, Y') }}</td>
                <td>{{ $booking->check_out->format('M d, Y') }}</td>
                <td>
                  @if($booking->status === 'confirmed')
                    <span class="badge badge-success">Confirmed</span>
                  @elseif($booking->status === 'pending')
                    <span class="badge badge-warning">Pending</span>
                  @elseif($booking->status === 'completed')
                    <span class="badge badge-info">Completed</span>
                  @else
                    <span class="badge badge-danger">Cancelled</span>
                  @endif
                </td>
                <td>
                  @if($booking->payment_status === 'paid')
                    <span class="badge badge-success">Paid</span>
                  @elseif($booking->payment_status === 'partial')
                    <span class="badge badge-info">Partial</span>
                  @else
                    <span class="badge badge-warning">Pending</span>
                  @endif
                </td>
                <td>
                  @if($booking->check_in_status === 'checked_in')
                    <span class="badge badge-success">Checked In</span>
                  @elseif($booking->check_in_status === 'checked_out')
                    <span class="badge badge-info">Checked Out</span>
                  @else
                    <span class="badge badge-warning">Pending</span>
                  @endif
                </td>
                <td>
                  @if($booking->locked_exchange_rate)
                    <strong>{{ number_format($booking->locked_exchange_rate, 2) }}</strong><br>
                    <small class="text-muted">TZS/USD</small>
                  @else
                    <span class="text-muted">N/A</span><br>
                    <small class="text-muted">(Old booking)</small>
                  @endif
                </td>
                <td>
                  <strong>${{ number_format($booking->total_price, 2) }}</strong><br>
                  <small>
                    @if($booking->locked_exchange_rate)
                      {{ number_format($booking->total_price * $booking->locked_exchange_rate, 2) }} TZS
                    @else
                      {{ number_format($booking->total_price * $exchangeRate, 2) }} TZS
                    @endif
                  </small>
                </td>
                <td>
                  <button onclick="viewBookingDetailsModal({{ $booking->id }})" 
                          class="btn btn-sm btn-info" 
                          title="View Details"
                          data-booking-id="{{ $booking->id }}"
                          data-booking-ref="{{ $booking->booking_reference }}"
                          data-guest-name="{{ htmlspecialchars($booking->guest_name, ENT_QUOTES, 'UTF-8') }}"
                          data-guest-email="{{ $booking->guest_email }}"
                          data-guest-phone="{{ $booking->guest_phone }}"
                          data-country-code="{{ $booking->country_code }}"
                          data-country="{{ $booking->country }}"
                          data-guest-id="{{ $booking->guest_id }}"
                          data-room-type="{{ $booking->room->room_type ?? 'N/A' }}"
                          data-room-number="{{ $booking->room->room_number ?? 'N/A' }}"
                          data-check-in="{{ $booking->check_in->format('M d, Y') }}"
                          data-check-out="{{ $booking->check_out->format('M d, Y') }}"
                          data-nights="{{ $booking->check_in->diffInDays($booking->check_out) }}"
                          data-guests="{{ $booking->number_of_guests }}"
                          data-status="{{ $booking->status }}"
                          data-payment-status="{{ $booking->payment_status }}"
                          data-check-in-status="{{ $booking->check_in_status }}"
                          data-total-price="{{ $booking->total_price }}"
                          data-amount-paid="{{ $booking->amount_paid ?? 0 }}"
                          data-payment-percentage="{{ $booking->payment_percentage ?? 0 }}"
                          data-exchange-rate="{{ $booking->locked_exchange_rate ?? '' }}"
                          data-special-requests="{{ htmlspecialchars($booking->special_requests ?? '', ENT_QUOTES, 'UTF-8') }}"
                          data-airport-pickup="{{ $booking->airport_pickup_required ? '1' : '0' }}"
                          data-flight-number="{{ $booking->flight_number ?? '' }}"
                          data-airline="{{ $booking->airline ?? '' }}"
                          data-arrival-time="{{ $booking->arrival_time_pickup ? $booking->arrival_time_pickup->format('M d, Y H:i') : '' }}"
                          data-pickup-passengers="{{ $booking->pickup_passengers ?? '' }}"
                          data-pickup-contact="{{ $booking->pickup_contact_number ?? '' }}"
                          data-created-at="{{ $booking->created_at->format('M d, Y H:i') }}"
                          data-checked-in-at="{{ $booking->checked_in_at ? $booking->checked_in_at->format('M d, Y H:i') : '' }}"
                          data-checked-out-at="{{ $booking->checked_out_at ? $booking->checked_out_at->format('M d, Y H:i') : '' }}">
                    <i class="fa fa-eye"></i>
                  </button>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-3">
          {{ $bookings->links() }}
        </div>
        @else
        <div class="text-center" style="padding: 50px;">
          <i class="fa fa-calendar fa-5x text-muted mb-3"></i>
          <h3>No Bookings Found</h3>
          <p class="text-muted">No bookings match your filters.</p>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Booking Details Modal -->
<div class="modal fade" id="bookingDetailsModal" tabindex="-1" role="dialog" aria-labelledby="bookingDetailsModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background: #940000; color: white;">
        <h4 class="modal-title" id="bookingDetailsModalLabel">
          <i class="fa fa-calendar-check-o"></i> Booking Details
        </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="bookingDetailsContent">
        <!-- Content will be populated by JavaScript -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
function filterBookings() {
  const statusFilter = document.getElementById('statusFilter').value;
  const paymentStatusFilter = document.getElementById('paymentStatusFilter').value;
  const checkInStatusFilter = document.getElementById('checkInStatusFilter').value;
  const searchInput = document.getElementById('searchInput').value.toLowerCase();
  
  const rows = document.querySelectorAll('.booking-row');
  rows.forEach(row => {
    const status = row.getAttribute('data-status');
    const paymentStatus = row.getAttribute('data-payment-status');
    const checkInStatus = row.getAttribute('data-check-in-status');
    const bookingRef = row.getAttribute('data-booking-ref');
    const guestName = row.getAttribute('data-guest-name');
    const guestEmail = row.getAttribute('data-guest-email');
    
    let show = true;
    
    // Status filter
    if (statusFilter !== 'all' && status !== statusFilter) {
      show = false;
    }
    
    // Payment status filter
    if (paymentStatusFilter !== 'all' && paymentStatus !== paymentStatusFilter) {
      show = false;
    }
    
    // Check-in status filter
    if (checkInStatusFilter !== 'all' && checkInStatus !== checkInStatusFilter) {
      show = false;
    }
    
    // Search filter
    if (searchInput) {
      if (!bookingRef.includes(searchInput) && 
          !guestName.includes(searchInput) && 
          !guestEmail.includes(searchInput)) {
        show = false;
      }
    }
    
    row.style.display = show ? '' : 'none';
  });
}

function resetBookingFilters() {
  document.getElementById('statusFilter').value = 'all';
  document.getElementById('paymentStatusFilter').value = 'all';
  document.getElementById('checkInStatusFilter').value = 'all';
  document.getElementById('searchInput').value = '';
  filterBookings();
}

function viewBookingDetailsModal(bookingId) {
  const button = document.querySelector(`button[data-booking-id="${bookingId}"]`);
  if (!button) return;
  
  // Helper function to escape HTML
  function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }
  
  // Get data from data attributes
  const bookingData = {
    booking_reference: button.getAttribute('data-booking-ref'),
    guest_name: button.getAttribute('data-guest-name'),
    guest_email: button.getAttribute('data-guest-email'),
    guest_phone: button.getAttribute('data-guest-phone'),
    country_code: button.getAttribute('data-country-code'),
    country: button.getAttribute('data-country'),
    guest_id: button.getAttribute('data-guest-id'),
    room_type: button.getAttribute('data-room-type'),
    room_number: button.getAttribute('data-room-number'),
    check_in: button.getAttribute('data-check-in'),
    check_out: button.getAttribute('data-check-out'),
    nights: button.getAttribute('data-nights'),
    number_of_guests: button.getAttribute('data-guests'),
    status: button.getAttribute('data-status'),
    payment_status: button.getAttribute('data-payment-status'),
    check_in_status: button.getAttribute('data-check-in-status'),
    total_price: parseFloat(button.getAttribute('data-total-price')),
    amount_paid: parseFloat(button.getAttribute('data-amount-paid') || 0),
    payment_percentage: parseFloat(button.getAttribute('data-payment-percentage') || 0),
    locked_exchange_rate: button.getAttribute('data-exchange-rate') ? parseFloat(button.getAttribute('data-exchange-rate')) : null,
    special_requests: button.getAttribute('data-special-requests'),
    airport_pickup_required: button.getAttribute('data-airport-pickup') === '1',
    flight_number: button.getAttribute('data-flight-number'),
    airline: button.getAttribute('data-airline'),
    arrival_time_pickup: button.getAttribute('data-arrival-time'),
    pickup_passengers: button.getAttribute('data-pickup-passengers'),
    pickup_contact_number: button.getAttribute('data-pickup-contact'),
    created_at: button.getAttribute('data-created-at'),
    checked_in_at: button.getAttribute('data-checked-in-at'),
    checked_out_at: button.getAttribute('data-checked-out-at')
  };
  
  const exchangeRate = bookingData.locked_exchange_rate || {{ $exchangeRate ?? 2500 }};
  const totalPriceTsh = bookingData.total_price * exchangeRate;
  
  // Status badges
  const statusBadges = {
    'pending': '<span class="badge badge-warning">Pending</span>',
    'confirmed': '<span class="badge badge-success">Confirmed</span>',
    'cancelled': '<span class="badge badge-danger">Cancelled</span>',
    'completed': '<span class="badge badge-info">Completed</span>'
  };
  
  const paymentBadges = {
    'pending': '<span class="badge badge-warning">Pending</span>',
    'paid': '<span class="badge badge-success">Paid</span>',
    'partial': '<span class="badge badge-info">Partial</span>',
    'cancelled': '<span class="badge badge-danger">Cancelled</span>'
  };
  
  const checkInBadges = {
    'pending': '<span class="badge badge-warning">Pending</span>',
    'checked_in': '<span class="badge badge-success">Checked In</span>',
    'checked_out': '<span class="badge badge-info">Checked Out</span>'
  };
  
  // Format phone number
  let phoneDisplay = bookingData.guest_phone || 'N/A';
  if (bookingData.country_code && bookingData.guest_phone) {
    const phone = bookingData.guest_phone.trim();
    const countryCode = bookingData.country_code.trim();
    if (!phone.startsWith('+') && !phone.startsWith(countryCode)) {
      phoneDisplay = countryCode + phone;
    } else {
      phoneDisplay = phone;
    }
  }
  
  const detailsHtml = `
    <div class="row">
      <div class="col-md-6">
        <h5 style="color: #940000; border-bottom: 2px solid #940000; padding-bottom: 10px; margin-bottom: 15px;">
          <i class="fa fa-user"></i> Guest Information
        </h5>
        <table class="table table-sm table-borderless">
          <tr>
            <td width="40%"><strong>Booking Reference:</strong></td>
            <td><span class="badge badge-primary">${escapeHtml(bookingData.booking_reference)}</span></td>
          </tr>
          <tr>
            <td><strong>Guest ID:</strong></td>
            <td>${escapeHtml(bookingData.guest_id) || 'N/A'}</td>
          </tr>
          <tr>
            <td><strong>Guest Name:</strong></td>
            <td><strong>${escapeHtml(bookingData.guest_name)}</strong></td>
          </tr>
          <tr>
            <td><strong>Email:</strong></td>
            <td>${escapeHtml(bookingData.guest_email)}</td>
          </tr>
          <tr>
            <td><strong>Phone:</strong></td>
            <td>${escapeHtml(phoneDisplay)}</td>
          </tr>
          <tr>
            <td><strong>Country:</strong></td>
            <td>${escapeHtml(bookingData.country) || 'N/A'}</td>
          </tr>
          <tr>
            <td><strong>Number of Guests:</strong></td>
            <td>${bookingData.number_of_guests || 1}</td>
          </tr>
        </table>
      </div>
      <div class="col-md-6">
        <h5 style="color: #940000; border-bottom: 2px solid #940000; padding-bottom: 10px; margin-bottom: 15px;">
          <i class="fa fa-bed"></i> Booking Information
        </h5>
        <table class="table table-sm table-borderless">
          <tr>
            <td width="40%"><strong>Room:</strong></td>
            <td><span class="badge badge-primary">${escapeHtml(bookingData.room_type)}</span> (${escapeHtml(bookingData.room_number)})</td>
          </tr>
          <tr>
            <td><strong>Check-in:</strong></td>
            <td>${escapeHtml(bookingData.check_in)}</td>
          </tr>
          <tr>
            <td><strong>Check-out:</strong></td>
            <td>${escapeHtml(bookingData.check_out)}</td>
          </tr>
          <tr>
            <td><strong>Nights:</strong></td>
            <td>${bookingData.nights} night(s)</td>
          </tr>
          <tr>
            <td><strong>Status:</strong></td>
            <td>${statusBadges[bookingData.status] || escapeHtml(bookingData.status)}</td>
          </tr>
          <tr>
            <td><strong>Payment Status:</strong></td>
            <td>${paymentBadges[bookingData.payment_status] || escapeHtml(bookingData.payment_status)}</td>
          </tr>
          <tr>
            <td><strong>Check-in Status:</strong></td>
            <td>${checkInBadges[bookingData.check_in_status] || escapeHtml(bookingData.check_in_status)}</td>
          </tr>
        </table>
      </div>
    </div>
    
    <div class="row mt-3">
      <div class="col-md-6">
        <h5 style="color: #940000; border-bottom: 2px solid #940000; padding-bottom: 10px; margin-bottom: 15px;">
          <i class="fa fa-money"></i> Pricing Information
        </h5>
        <table class="table table-sm table-borderless">
          <tr>
            <td width="40%"><strong>Total Price (USD):</strong></td>
            <td><strong>$${parseFloat(bookingData.total_price).toFixed(2)}</strong></td>
          </tr>
          <tr>
            <td><strong>Exchange Rate:</strong></td>
            <td>${bookingData.locked_exchange_rate ? parseFloat(bookingData.locked_exchange_rate).toFixed(2) + ' TZS/USD' : 'N/A (Old booking)'}</td>
          </tr>
          <tr>
            <td><strong>Total Price (TZS):</strong></td>
            <td><strong>${parseFloat(totalPriceTsh).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})} TZS</strong></td>
          </tr>
          ${bookingData.payment_status === 'partial' && bookingData.amount_paid ? `
          <tr>
            <td><strong>Amount Paid (USD):</strong></td>
            <td><strong style="color: #17a2b8;">$${parseFloat(bookingData.amount_paid).toFixed(2)}</strong></td>
          </tr>
          <tr>
            <td><strong>Amount Paid (TZS):</strong></td>
            <td><strong style="color: #17a2b8;">${parseFloat((bookingData.amount_paid || 0) * (bookingData.locked_exchange_rate || 2442.54)).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})} TZS</strong></td>
          </tr>
          <tr>
            <td><strong>Remaining Amount (USD):</strong></td>
            <td><strong style="color: #dc3545;">$${parseFloat((bookingData.total_price || 0) - (bookingData.amount_paid || 0)).toFixed(2)}</strong></td>
          </tr>
          <tr>
            <td><strong>Remaining Amount (TZS):</strong></td>
            <td><strong style="color: #dc3545;">${parseFloat(((bookingData.total_price || 0) - (bookingData.amount_paid || 0)) * (bookingData.locked_exchange_rate || 2442.54)).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})} TZS</strong></td>
          </tr>
          <tr>
            <td><strong>Payment Percentage:</strong></td>
            <td><span class="badge badge-info">${parseFloat(((bookingData.amount_paid || 0) / (bookingData.total_price || 1)) * 100).toFixed(0)}%</span></td>
          </tr>
          ` : ''}
        </table>
      </div>
      <div class="col-md-6">
        <h5 style="color: #940000; border-bottom: 2px solid #940000; padding-bottom: 10px; margin-bottom: 15px;">
          <i class="fa fa-clock-o"></i> Timestamps
        </h5>
        <table class="table table-sm table-borderless">
          <tr>
            <td width="40%"><strong>Booking Created:</strong></td>
            <td>${escapeHtml(bookingData.created_at)}</td>
          </tr>
          ${bookingData.checked_in_at ? `
          <tr>
            <td><strong>Checked In At:</strong></td>
            <td>${escapeHtml(bookingData.checked_in_at)}</td>
          </tr>
          ` : ''}
          ${bookingData.checked_out_at ? `
          <tr>
            <td><strong>Checked Out At:</strong></td>
            <td>${escapeHtml(bookingData.checked_out_at)}</td>
          </tr>
          ` : ''}
        </table>
      </div>
    </div>
    
    ${bookingData.special_requests ? `
    <div class="row mt-3">
      <div class="col-md-12">
        <h5 style="color: #940000; border-bottom: 2px solid #940000; padding-bottom: 10px; margin-bottom: 15px;">
          <i class="fa fa-comment"></i> Special Requests
        </h5>
        <p style="background: #f8f9fa; padding: 10px; border-radius: 5px; white-space: pre-wrap;">${escapeHtml(bookingData.special_requests)}</p>
      </div>
    </div>
    ` : ''}
    
    ${bookingData.airport_pickup_required ? `
    <div class="row mt-3">
      <div class="col-md-12">
        <h5 style="color: #940000; border-bottom: 2px solid #940000; padding-bottom: 10px; margin-bottom: 15px;">
          <i class="fa fa-plane"></i> Airport Pickup Information
        </h5>
        <table class="table table-sm table-bordered">
          <tr>
            <td width="30%"><strong>Flight Number:</strong></td>
            <td>${escapeHtml(bookingData.flight_number) || 'N/A'}</td>
          </tr>
          <tr>
            <td><strong>Airline:</strong></td>
            <td>${escapeHtml(bookingData.airline) || 'N/A'}</td>
          </tr>
          <tr>
            <td><strong>Arrival Time:</strong></td>
            <td>${escapeHtml(bookingData.arrival_time_pickup) || 'N/A'}</td>
          </tr>
          <tr>
            <td><strong>Passengers:</strong></td>
            <td>${escapeHtml(bookingData.pickup_passengers) || 'N/A'}</td>
          </tr>
          <tr>
            <td><strong>Contact Number:</strong></td>
            <td>${escapeHtml(bookingData.pickup_contact_number) || 'N/A'}</td>
          </tr>
        </table>
      </div>
    </div>
    ` : ''}
  `;
  
  document.getElementById('bookingDetailsContent').innerHTML = detailsHtml;
  $('#bookingDetailsModal').modal('show');
}
</script>
@endsection



