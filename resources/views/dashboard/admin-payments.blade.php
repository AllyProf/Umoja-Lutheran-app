@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-money"></i> Payments</h1>
    <p>View all payment transactions</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Payments</a></li>
  </ul>
</div>

<!-- Statistics Cards -->
<div class="row mb-3">
  <div class="col-md-3">
    <div class="widget-small primary coloured-icon">
      <i class="icon fa fa-dollar fa-2x"></i>
      <div class="info">
        <h4>Total Revenue</h4>
        <p><b>{{ number_format($stats['total_revenue'] ?? 0, 0) }} TZS</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small success coloured-icon">
      <i class="icon fa fa-calendar fa-2x"></i>
      <div class="info">
        <h4>Today's Revenue</h4>
        <p><b>{{ number_format($stats['today_revenue'] ?? 0, 0) }} TZS</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small info coloured-icon">
      <i class="icon fa fa-calendar-check-o fa-2x"></i>
      <div class="info">
        <h4>This Month</h4>
        <p><b>{{ number_format($stats['month_revenue'] ?? 0, 0) }} TZS</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small warning coloured-icon">
      <i class="icon fa fa-list fa-2x"></i>
      <div class="info">
        <h4>Total Payments</h4>
        <p><b>{{ $stats['total_payments'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
</div>

<!-- Pending & Partial Payments Info -->
@if(($stats['pending_payments'] ?? 0) > 0 || ($stats['partial_payments'] ?? 0) > 0)
<div class="row mb-3">
  <div class="col-md-6">
    <div class="alert alert-warning" style="margin-bottom: 0;">
      <i class="fa fa-exclamation-triangle"></i> 
      <strong>Pending Payments:</strong> {{ $stats['pending_payments'] ?? 0 }} booking(s) 
      ({{ number_format($stats['pending_amount'] ?? 0, 0) }} TZS)
    </div>
  </div>
  <div class="col-md-6">
    <div class="alert alert-info" style="margin-bottom: 0;">
      <i class="fa fa-info-circle"></i> 
      <strong>Partial Payments:</strong> {{ $stats['partial_payments'] ?? 0 }} booking(s) 
      ({{ number_format($stats['partial_amount'] ?? 0, 0) }} TZS outstanding)
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
              <label for="paymentStatusFilter"><strong>Payment Status:</strong></label>
              <select id="paymentStatusFilter" class="form-control" onchange="filterPayments()">
                <option value="all" {{ ($filters['payment_status'] ?? 'all') === 'all' ? 'selected' : '' }}>All Status</option>
                <option value="paid" {{ ($filters['payment_status'] ?? '') === 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="partial" {{ ($filters['payment_status'] ?? '') === 'partial' ? 'selected' : '' }}>Partial</option>
                <option value="pending" {{ ($filters['payment_status'] ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
              </select>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label for="date_from"><strong>From Date:</strong></label>
              <input type="date" id="date_from" class="form-control" value="{{ $filters['date_from'] ?? '' }}" onchange="filterPayments()">
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label for="date_to"><strong>To Date:</strong></label>
              <input type="date" id="date_to" class="form-control" value="{{ $filters['date_to'] ?? '' }}" onchange="filterPayments()">
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label for="searchInput"><strong>Search:</strong></label>
              <input type="text" id="searchInput" class="form-control" 
                     placeholder="Search by booking ref, name, email..." 
                     value="{{ $filters['search'] ?? '' }}"
                     onkeyup="filterPayments()">
            </div>
          </div>
          <div class="col-md-1">
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

<!-- Payments Table -->
<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn">
        <h3 class="title">All Payments</h3>
        <p><a class="btn btn-primary icon-btn" href="{{ route('admin.payments.reports') }}">
          <i class="fa fa-file-text"></i>Payment Reports</a></p>
      </div>
      <div class="tile-body">
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>Booking Reference</th>
                <th>Guest</th>
                <th>Room</th>
                <th>Amount Paid</th>
                <th>Payment Date</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($payments as $payment)
              <tr class="payment-row"
                  data-booking-ref="{{ strtolower($payment->booking_reference) }}"
                  data-guest-name="{{ strtolower($payment->guest_name) }}"
                  data-guest-email="{{ strtolower($payment->guest_email) }}"
                  data-payment-date="{{ $payment->created_at->format('Y-m-d') }}"
                  data-payment-status="{{ $payment->payment_status }}">
                <td><strong>{{ $payment->booking_reference }}</strong></td>
                <td>
                  <strong>{{ $payment->guest_name }}</strong><br>
                  <small>{{ $payment->guest_email }}</small>
                </td>
                <td>{{ $payment->room->room_number ?? 'N/A' }} ({{ $payment->room->room_type ?? 'N/A' }})</td>
                <td>
                  @if($payment->payment_status == 'pending')
                    <strong style="color: #ffc107;">{{ number_format($payment->total_price, 0) }} TZS</strong>
                  @elseif($payment->payment_status == 'partial')
                    <strong>{{ number_format($payment->amount_paid ?? 0, 0) }} TZS</strong>
                    <br><small class="text-muted">of {{ number_format($payment->total_price, 0) }} TZS</small>
                    <br><small class="text-info">Outstanding: {{ number_format($payment->total_price - ($payment->amount_paid ?? 0), 0) }} TZS</small>
                  @else
                    <strong>{{ number_format($payment->amount_paid ?? 0, 0) }} TZS</strong>
                  @endif
                </td>
                <td>
                  @if($payment->paid_at)
                    {{ \Carbon\Carbon::parse($payment->paid_at)->format('M d, Y H:i') }}
                  @else
                    <span class="text-muted">{{ $payment->created_at->format('M d, Y H:i') }}</span>
                    <br><small class="text-muted">(Booking Date)</small>
                  @endif
                </td>
                <td>
                  @if($payment->payment_status == 'paid')
                    <span class="badge badge-success">Paid</span>
                  @elseif($payment->payment_status == 'partial')
                    <span class="badge badge-info">Partial</span>
                  @else
                    <span class="badge badge-warning">Pending</span>
                  @endif
                </td>
                <td>
                  <button onclick="viewPaymentDetails({{ $payment->id }}, '{{ $payment->booking_reference }}')" class="btn btn-sm btn-primary" title="View Details">
                    <i class="fa fa-eye"></i> View
                  </button>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="7" class="text-center">No payments found</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        
        <!-- Pagination -->
        <div class="mt-3">
          {{ $payments->links() }}
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Payment Details Modal -->
<div class="modal fade" id="paymentDetailsModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-money"></i> Payment Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="paymentDetailsContent">
        <div class="text-center">
          <i class="fa fa-spinner fa-spin fa-2x"></i>
          <p>Loading payment details...</p>
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
<script>
function filterPayments() {
  const paymentStatus = document.getElementById('paymentStatusFilter').value;
  const dateFrom = document.getElementById('date_from').value;
  const dateTo = document.getElementById('date_to').value;
  const searchInput = document.getElementById('searchInput').value.toLowerCase();
  
  // Build URL with filters
  const params = new URLSearchParams();
  if (paymentStatus && paymentStatus !== 'all') {
    params.append('payment_status', paymentStatus);
  }
  if (dateFrom) {
    params.append('date_from', dateFrom);
  }
  if (dateTo) {
    params.append('date_to', dateTo);
  }
  if (searchInput) {
    params.append('search', searchInput);
  }
  
  // Reload page with filters
  const url = '{{ route("admin.payments") }}' + (params.toString() ? '?' + params.toString() : '');
  window.location.href = url;
}

function resetFilters() {
  document.getElementById('paymentStatusFilter').value = 'all';
  document.getElementById('date_from').value = '';
  document.getElementById('date_to').value = '';
  document.getElementById('searchInput').value = '';
  window.location.href = '{{ route("admin.payments") }}';
}

function viewPaymentDetails(bookingId, bookingRef) {
  $('#paymentDetailsModal').modal('show');
  document.getElementById('paymentDetailsContent').innerHTML = `
    <div class="text-center">
      <i class="fa fa-spinner fa-spin fa-2x"></i>
      <p>Loading payment details...</p>
    </div>
  `;
  
  // Fetch booking details - payment ID is actually the booking ID
  fetch('{{ route("admin.bookings.show", ":id") }}'.replace(':id', bookingId), {
    method: 'GET',
    headers: {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'X-Requested-With': 'XMLHttpRequest'
    }
  })
  .then(async response => {
    if (!response.ok) {
      const errorText = await response.text();
      throw new Error('Network response was not ok: ' + response.status + ' - ' + errorText);
    }
    
    // Check if response is JSON
    const contentType = response.headers.get('content-type');
    if (!contentType || !contentType.includes('application/json')) {
      const text = await response.text();
      throw new Error('Expected JSON but got: ' + contentType + '. Response: ' + text.substring(0, 200));
    }
    
    return response.json();
  })
  .then(data => {
    if (data.success) {
      const booking = data.booking;
      const room = booking.room || {};
      const exchangeRate = 2500; // Default exchange rate
      const fallbackImage = '{{ asset("landing_page_assets/img/bg-img/1.jpg") }}';
      
      const detailsHtml = `
        <div class="payment-details-view">
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
              <h5 style="color: #e07632; border-bottom: 2px solid #e07632; padding-bottom: 5px; margin-bottom: 15px;"><i class="fa fa-dollar"></i> Payment Information</h5>
              <table class="table table-sm table-bordered">
                <tr><td width="40%"><strong>Booking Reference:</strong></td><td><strong>${booking.booking_reference || 'N/A'}</strong></td></tr>
                <tr><td><strong>Total Price:</strong></td><td><strong>${parseFloat(booking.total_price || 0).toLocaleString()} TZS</strong></td></tr>
                <tr><td><strong>Amount Paid:</strong></td><td><strong style="color: #28a745;">${parseFloat(booking.amount_paid || 0).toLocaleString()} TZS</strong></td></tr>
                ${booking.payment_percentage ? `<tr><td><strong>Payment Percentage:</strong></td><td><strong>${parseFloat(booking.payment_percentage).toFixed(2)}%</strong></td></tr>` : ''}
                ${booking.payment_status === 'partial' ? `
                <tr><td><strong>Remaining Amount:</strong></td><td><strong style="color: #dc3545;">${(parseFloat(booking.total_price || 0) - parseFloat(booking.amount_paid || 0)).toLocaleString()} TZS</strong></td></tr>
                ` : ''}
                <tr><td><strong>Payment Method:</strong></td><td>${booking.payment_method ? booking.payment_method.charAt(0).toUpperCase() + booking.payment_method.slice(1) : 'N/A'}</td></tr>
                <tr><td><strong>Transaction ID:</strong></td><td>${booking.payment_transaction_id || 'N/A'}</td></tr>
                <tr><td><strong>Payment Date:</strong></td><td>${booking.paid_at ? new Date(booking.paid_at).toLocaleDateString('en-US', {year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit'}) : 'N/A'}</td></tr>
                <tr><td><strong>Payment Status:</strong></td><td>
                  ${booking.payment_status === 'paid' ? '<span class="badge badge-success">Paid</span>' : ''}
                  ${booking.payment_status === 'partial' ? '<span class="badge badge-info">Partial</span>' : ''}
                  ${booking.payment_status === 'pending' ? '<span class="badge badge-warning">Pending</span>' : ''}
                  ${!['paid', 'partial', 'pending'].includes(booking.payment_status) ? '<span class="badge badge-secondary">' + (booking.payment_status || 'Unknown') + '</span>' : ''}
                </td></tr>
              </table>
            </div>
          </div>
          <div class="row mt-3">
            <div class="col-md-6">
              <h5 style="color: #e07632; border-bottom: 2px solid #e07632; padding-bottom: 5px; margin-bottom: 15px;"><i class="fa fa-calendar"></i> Booking Information</h5>
              <table class="table table-sm table-bordered">
                <tr><td width="40%"><strong>Check-in:</strong></td><td>${booking.check_in ? new Date(booking.check_in).toLocaleDateString('en-US', {year: 'numeric', month: 'long', day: 'numeric'}) : 'N/A'}</td></tr>
                <tr><td><strong>Check-out:</strong></td><td>${booking.check_out ? new Date(booking.check_out).toLocaleDateString('en-US', {year: 'numeric', month: 'long', day: 'numeric'}) : 'N/A'}</td></tr>
                <tr><td><strong>Status:</strong></td><td>
                  ${booking.status === 'pending' ? '<span class="badge badge-warning">Pending</span>' : ''}
                  ${booking.status === 'confirmed' ? '<span class="badge badge-success">Confirmed</span>' : ''}
                  ${booking.status === 'cancelled' ? '<span class="badge badge-danger">Cancelled</span>' : ''}
                  ${booking.status === 'completed' ? '<span class="badge badge-info">Completed</span>' : ''}
                </td></tr>
                <tr><td><strong>Check-in Status:</strong></td><td>
                  ${booking.check_in_status === 'checked_in' ? '<span class="badge badge-success">Checked In</span>' : ''}
                  ${booking.check_in_status === 'checked_out' ? '<span class="badge badge-info">Checked Out</span>' : ''}
                  ${booking.check_in_status === 'pending' ? '<span class="badge badge-warning">Pending</span>' : ''}
                  ${booking.checked_in_at ? '<br><small>Checked in: ' + new Date(booking.checked_in_at).toLocaleDateString('en-US', {year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit'}) + '</small>' : ''}
                </td></tr>
              </table>
            </div>
            <div class="col-md-6">
              <h5 style="color: #e07632; border-bottom: 2px solid #e07632; padding-bottom: 5px; margin-bottom: 15px;"><i class="fa fa-bed"></i> Room Information</h5>
              <table class="table table-sm table-bordered">
                <tr><td width="40%"><strong>Room Number:</strong></td><td><strong>${room.room_number || 'N/A'}</strong></td></tr>
                <tr><td><strong>Room Type:</strong></td><td>${room.room_type || 'N/A'}</td></tr>
                <tr><td><strong>Capacity:</strong></td><td>${room.capacity || 'N/A'} guests</td></tr>
                <tr><td><strong>Price per Night:</strong></td><td>${parseFloat(room.price_per_night || 0).toLocaleString()} TZS</td></tr>
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
        </div>
      `;
      
      document.getElementById('paymentDetailsContent').innerHTML = detailsHtml;
    } else {
      document.getElementById('paymentDetailsContent').innerHTML = `
        <div class="alert alert-danger">
          <i class="fa fa-exclamation-triangle"></i> Failed to load payment details.
        </div>
      `;
    }
  })
  .catch(error => {
    console.error('Error:', error);
    document.getElementById('paymentDetailsContent').innerHTML = `
      <div class="alert alert-danger">
        <i class="fa fa-exclamation-triangle"></i> An error occurred while loading payment details: ${error.message || 'Unknown error'}
      </div>
    `;
  });
}
</script>
@endsection






