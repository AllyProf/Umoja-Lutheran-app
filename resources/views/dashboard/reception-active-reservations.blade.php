@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-check-circle"></i> Active Reservations</h1>
    <p>View all currently active reservations</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('reception.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Active Reservations</a></li>
  </ul>
</div>

<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn mb-3">
        <h3 class="title">Active Reservations</h3>
      </div>
      
      <!-- Search Filter -->
      <div class="row mb-3">
        <div class="col-md-10 col-12 mb-2 mb-md-0">
          <input type="text" class="form-control" id="searchInput" placeholder="Search by booking reference, guest name, or email..." onkeyup="filterReservations()" style="font-size: 16px;">
        </div>
        <div class="col-md-2 col-12">
          <button class="btn btn-secondary btn-block" onclick="resetReservationFilters()">
            <i class="fa fa-refresh"></i> Reset
          </button>
        </div>
      </div>
      
      <div class="tile-body">
        @if($bookings->count() > 0)
        <!-- Desktop Table View -->
        <div class="table-responsive">
          <table class="table table-hover table-bordered" id="activeReservationsTable">
            <thead>
              <tr>
                <th>Booking Reference</th>
                <th>Guest</th>
                <th>Room</th>
                <th>Check-in</th>
                <th>Check-out</th>
                <th>Check-in Status</th>
                <th>Total Price</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($bookings as $booking)
              <tr class="reservation-row"
                  data-booking-ref="{{ strtolower($booking->booking_reference) }}"
                  data-guest-name="{{ strtolower($booking->guest_name) }}"
                  data-guest-email="{{ strtolower($booking->guest_email) }}"
                  data-booking-id="{{ $booking->id }}">
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
                  @if($booking->check_in_status === 'checked_in')
                    <span class="badge badge-success">Checked In</span>
                    @if($booking->checked_in_at)
                      <br><small>{{ $booking->checked_in_at->format('M d, H:i') }}</small>
                    @endif
                  @else
                    <span class="badge badge-warning">Pending</span>
                  @endif
                </td>
                <td>
                  <strong>${{ number_format($booking->total_price, 2) }}</strong><br>
                  <small>{{ number_format($booking->total_price * $exchangeRate, 2) }} TZS</small>
                </td>
                <td>
                  <button onclick="viewReservationDetails({{ $booking->id }}, '{{ $booking->booking_reference }}')" class="btn btn-sm btn-info" title="View Details">
                    <i class="fa fa-eye"></i> View
                  </button>
                  @if($booking->check_in_status === 'checked_in')
                  @php
                    // Determine which route to use based on current route name
                    $currentRoute = request()->route()->getName() ?? '';
                    $checkoutBillRoute = (str_starts_with($currentRoute, 'admin.')) 
                      ? 'admin.bookings.checkout-bill' 
                      : 'reception.bookings.checkout-bill';
                  @endphp
                  <a href="{{ route($checkoutBillRoute, $booking) }}" class="btn btn-sm btn-warning mt-1" target="_blank">
                    <i class="fa fa-file-text"></i> Bill
                  </a>
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        
        <!-- Mobile Card View -->
        <div class="mobile-active-reservations-cards">
          @foreach($bookings as $booking)
          @php
            $currentRoute = request()->route()->getName() ?? '';
            $checkoutBillRoute = (str_starts_with($currentRoute, 'admin.')) 
              ? 'admin.bookings.checkout-bill' 
              : 'reception.bookings.checkout-bill';
          @endphp
          <div class="mobile-active-reservation-card reservation-row" style="background: white; border: 1px solid #dee2e6; border-radius: 8px; padding: 15px; margin-bottom: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"
               data-booking-ref="{{ strtolower($booking->booking_reference) }}"
               data-guest-name="{{ strtolower($booking->guest_name) }}"
               data-guest-email="{{ strtolower($booking->guest_email) }}"
               data-booking-id="{{ $booking->id }}">
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
              <span style="font-weight: 600; color: #495057; font-size: 14px; flex: 0 0 40%;">Check-in:</span>
              <span style="text-align: right; flex: 1;">{{ $booking->check_in->format('M d, Y') }}</span>
            </div>
            
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
              <span style="font-weight: 600; color: #495057; font-size: 14px; flex: 0 0 40%;">Check-out:</span>
              <span style="text-align: right; flex: 1;">{{ $booking->check_out->format('M d, Y') }}</span>
            </div>
            
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
              <span style="font-weight: 600; color: #495057; font-size: 14px; flex: 0 0 40%;">Check-in Status:</span>
              <span style="text-align: right; flex: 1;">
                @if($booking->check_in_status === 'checked_in')
                  <span class="badge badge-success">Checked In</span>
                  @if($booking->checked_in_at)
                    <br><small>{{ $booking->checked_in_at->format('M d, H:i') }}</small>
                  @endif
                @else
                  <span class="badge badge-warning">Pending</span>
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
            
            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #dee2e6; display: flex; gap: 8px; flex-wrap: wrap;">
              <button onclick="viewReservationDetails({{ $booking->id }}, '{{ $booking->booking_reference }}')" class="btn btn-sm btn-info" title="View Details" style="flex: 1; min-width: calc(50% - 4px);">
                <i class="fa fa-eye"></i> View
              </button>
              @if($booking->check_in_status === 'checked_in')
              <a href="{{ route($checkoutBillRoute, $booking) }}" class="btn btn-sm btn-warning" target="_blank" style="flex: 1; min-width: calc(50% - 4px); text-align: center;">
                <i class="fa fa-file-text"></i> Bill
              </a>
              @endif
            </div>
          </div>
          @endforeach
        </div>
        
        <div class="d-flex justify-content-center mt-3">
          {{ $bookings->links() }}
        </div>
        @else
        <div class="text-center" style="padding: 50px;">
          <i class="fa fa-calendar-check-o fa-5x text-muted mb-3"></i>
          <h3>No Active Reservations</h3>
          <p class="text-muted">No active reservations at this time.</p>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Reservation Details Modal -->
<div class="modal fade" id="reservationDetailsModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-calendar-check-o"></i> Reservation Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="reservationDetailsContent">
        <div class="text-center">
          <i class="fa fa-spinner fa-spin fa-2x"></i>
          <p>Loading reservation details...</p>
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
<style>
/* Mobile Responsive Styles */
@media (max-width: 767px) {
  /* Filters */
  .row .col-md-10,
  .row .col-md-2 {
    margin-bottom: 10px;
  }
  
  /* Table - Hide on Mobile */
  #activeReservationsTable {
    display: none;
  }
  
  /* Mobile Cards - Show on Mobile */
  .mobile-active-reservations-cards {
    display: block;
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
  
  /* Pagination */
  .pagination {
    justify-content: center;
    flex-wrap: wrap;
  }
  
  .pagination .page-link {
    padding: 8px 12px;
    font-size: 14px;
  }
}

/* Desktop - Hide mobile cards */
@media (min-width: 768px) {
  .mobile-active-reservations-cards {
    display: none;
  }
  
  #activeReservationsTable {
    display: table;
  }
}

/* Very Small Screens */
@media (max-width: 480px) {
  .mobile-active-reservation-card {
    padding: 12px !important;
  }
  
  .mobile-active-reservation-card h5 {
    font-size: 16px !important;
  }
  
  .mobile-active-reservation-card .btn {
    flex: 0 0 100% !important;
    min-width: 100% !important;
    margin-bottom: 8px;
  }
  
  .mobile-active-reservation-card .btn:last-child {
    margin-bottom: 0;
  }
}
</style>
<script>
function filterReservations() {
  const searchInput = document.getElementById('searchInput').value.toLowerCase();
  
  // Filter both table rows and mobile cards
  const rows = document.querySelectorAll('.reservation-row');
  rows.forEach(row => {
    const bookingRef = row.getAttribute('data-booking-ref');
    const guestName = row.getAttribute('data-guest-name');
    const guestEmail = row.getAttribute('data-guest-email');
    
    let show = true;
    
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

function resetReservationFilters() {
  document.getElementById('searchInput').value = '';
  filterReservations();
}

function viewReservationDetails(bookingId, bookingRef) {
  $('#reservationDetailsModal').modal('show');
  document.getElementById('reservationDetailsContent').innerHTML = `
    <div class="text-center">
      <i class="fa fa-spinner fa-spin fa-2x"></i>
      <p>Loading reservation details...</p>
    </div>
  `;
  
  @php
    // Determine which route to use based on current route name
    $currentRoute = request()->route()->getName() ?? '';
    $bookingShowRoute = (str_starts_with($currentRoute, 'admin.')) 
      ? 'admin.bookings.show' 
      : 'reception.bookings.show';
  @endphp
  
  fetch('{{ route($bookingShowRoute, ":id") }}'.replace(':id', bookingId), {
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
        <div class="reservation-details-view">
          <div class="row">
            <div class="col-md-6">
              <h5 style="color: #e07632; border-bottom: 2px solid #e07632; padding-bottom: 5px; margin-bottom: 15px;"><i class="fa fa-user"></i> Guest Information</h5>
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
              <h5 style="color: #e07632; border-bottom: 2px solid #e07632; padding-bottom: 5px; margin-bottom: 15px;"><i class="fa fa-calendar"></i> Booking Information</h5>
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
              <h5 style="color: #e07632; border-bottom: 2px solid #e07632; padding-bottom: 5px; margin-bottom: 15px;"><i class="fa fa-image"></i> Room Image</h5>
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
              <h5 style="color: #e07632; border-bottom: 2px solid #e07632; padding-bottom: 5px; margin-bottom: 15px;"><i class="fa fa-bed"></i> Room Information</h5>
              <table class="table table-sm table-bordered">
                <tr><td width="40%"><strong>Room Number:</strong></td><td><strong>${room.room_number || 'N/A'}</strong></td></tr>
                <tr><td><strong>Room Type:</strong></td><td>${room.room_type || 'N/A'}</td></tr>
                <tr><td><strong>Capacity:</strong></td><td>${room.capacity || 'N/A'} guests</td></tr>
                <tr><td><strong>Price per Night:</strong></td><td>$${parseFloat(room.price_per_night || 0).toFixed(2)} USD</td></tr>
              </table>
              ${(() => {
                // Get amenities
                let amenities = [];
                if (room.amenities !== null && room.amenities !== undefined) {
                  if (Array.isArray(room.amenities)) {
                    amenities = room.amenities;
                  } else if (typeof room.amenities === 'string') {
                    try {
                      const parsed = JSON.parse(room.amenities);
                      amenities = Array.isArray(parsed) ? parsed : [];
                    } catch(e) {
                      amenities = room.amenities.split(',').map(a => a.trim()).filter(a => a.length > 0);
                    }
                  }
                }
                
                if (amenities.length > 0) {
                  let amenitiesHtml = '<div class="mt-3"><h6 style="color: #e07632; border-bottom: 2px solid #e07632; padding-bottom: 5px; margin-bottom: 10px;"><i class="fa fa-star"></i> Amenities</h6><div style="font-size: 14px;">';
                  amenities.forEach(function(amenity) {
                    let icon = '';
                    const amenityText = amenity.toString().trim();
                    
                    if (amenityText === 'Free Wi-Fi' || amenityText === 'Wi-Fi') {
                      icon = '<i class="fa fa-wifi" title="Wi-Fi"></i> ';
                    } else if (amenityText === 'Air-Conditioning' || amenityText === 'AC') {
                      icon = '<i class="fa fa-snowflake-o" title="Air Conditioning"></i> ';
                    } else if (amenityText === 'Smart TV Screen' || amenityText === 'TV') {
                      icon = '<i class="fa fa-tv" title="TV"></i> ';
                    } else if (amenityText === 'Telephone extension' || amenityText === 'Phone') {
                      icon = '<i class="fa fa-phone" title="Phone"></i> ';
                    } else if (amenityText === 'Mini Bar' || amenityText === 'Bar') {
                      icon = '<i class="fa fa-glass" title="Mini Bar"></i> ';
                    } else if (amenityText === 'Safe' || amenityText === 'Safe Deposit Box') {
                      icon = '<i class="fa fa-lock" title="Safe"></i> ';
                    } else if (amenityText === 'Balcony' || amenityText === 'Balcony View') {
                      icon = '<i class="fa fa-building" title="Balcony"></i> ';
                    }
                    
                    // Escape HTML to prevent XSS
                    const safeAmenity = amenityText.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                    amenitiesHtml += '<div class="mb-2">' + icon + '<strong>' + safeAmenity + '</strong></div>';
                  });
                  amenitiesHtml += '</div></div>';
                  return amenitiesHtml;
                }
                return '';
              })()}
            </div>
            <div class="col-md-6">
              <h5 style="color: #e07632; border-bottom: 2px solid #e07632; padding-bottom: 5px; margin-bottom: 15px;"><i class="fa fa-dollar"></i> Payment Information</h5>
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
      
      document.getElementById('reservationDetailsContent').innerHTML = detailsHtml;
    } else {
      document.getElementById('reservationDetailsContent').innerHTML = `
        <div class="alert alert-danger">
          <i class="fa fa-exclamation-triangle"></i> Failed to load reservation details.
        </div>
      `;
    }
  })
  .catch(error => {
    console.error('Error:', error);
    document.getElementById('reservationDetailsContent').innerHTML = `
      <div class="alert alert-danger">
        <i class="fa fa-exclamation-triangle"></i> An error occurred while loading reservation details: ${error.message || 'Unknown error'}
      </div>
    `;
  });
}
</script>
@endsection





