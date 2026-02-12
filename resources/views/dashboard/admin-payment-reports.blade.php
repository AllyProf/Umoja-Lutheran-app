@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-file-text"></i> Payment Reports</h1>
    <p>Detailed payment analysis and reports</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.payments') }}">Payments</a></li>
    <li class="breadcrumb-item"><a href="#">Reports</a></li>
  </ul>
</div>

<!-- Date Range Filter -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-body">
        <form method="GET" action="{{ route('admin.payments.reports') }}" class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label for="start_date">Start Date</label>
              <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="end_date">End Date</label>
              <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label>&nbsp;</label>
              <button type="submit" class="btn btn-primary btn-block">
                <i class="fa fa-filter"></i> Generate Report
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Statistics -->
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
    <div class="widget-small info coloured-icon">
      <i class="icon fa fa-list fa-2x"></i>
      <div class="info">
        <h4>Total Payments</h4>
        <p><b>{{ $stats['total_payments'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small success coloured-icon">
      <i class="icon fa fa-calculator fa-2x"></i>
      <div class="info">
        <h4>Average Payment</h4>
        <p><b>{{ number_format($stats['average_payment'] ?? 0, 0) }} TZS</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small warning coloured-icon">
      <i class="icon fa fa-calendar fa-2x"></i>
      <div class="info">
        <h4>Date Range</h4>
        <p><b>{{ \Carbon\Carbon::parse($startDate)->format('M d') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</b></p>
      </div>
    </div>
  </div>
</div>

<!-- Daily Revenue Chart -->
@if($dailyRevenue->count() > 0)
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title">Daily Revenue Breakdown</h3>
      <div class="tile-body">
        <canvas id="dailyRevenueChart" height="100"></canvas>
      </div>
    </div>
  </div>
</div>
@endif

<!-- Payments List -->
<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn">
        <h3 class="title">Payment Details</h3>
      </div>
      <div class="tile-body">
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>Date</th>
                <th>Booking Reference</th>
                <th>Guest</th>
                <th>Room</th>
                <th>Amount</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($payments as $payment)
              <tr>
                <td>
                  @if($payment->paid_at)
                    {{ \Carbon\Carbon::parse($payment->paid_at)->format('M d, Y') }}
                  @else
                    {{ $payment->created_at->format('M d, Y') }}
                  @endif
                </td>
                <td><strong>{{ $payment->booking_reference }}</strong></td>
                <td>
                  {{ $payment->guest_name }}
                  <br><small class="text-muted">{{ $payment->guest_email }}</small>
                </td>
                <td>{{ $payment->room->room_number ?? 'N/A' }} ({{ $payment->room->room_type ?? 'N/A' }})</td>
                <td>
                  <strong>{{ number_format($payment->amount_paid ?? 0, 0) }} TZS</strong>
                  @if($payment->payment_status === 'partial')
                    <br><small class="text-info">Partial Payment</small>
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
                <td colspan="6" class="text-center">No payments found for the selected date range</td>
              </tr>
              @endforelse
            </tbody>
          </table>
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
<script type="text/javascript" src="{{ asset('dashboard_assets/js/plugins/chart.js') }}"></script>
<script type="text/javascript">
  @if($dailyRevenue->count() > 0)
  var dailyRevenueData = {
    labels: {!! json_encode($dailyRevenue->keys()) !!},
    datasets: [{
      label: "Daily Revenue (TZS)",
      fillColor: "rgba(151,187,205,0.2)",
      strokeColor: "rgba(151,187,205,1)",
      pointColor: "rgba(151,187,205,1)",
      pointStrokeColor: "#fff",
      pointHighlightFill: "#fff",
      pointHighlightStroke: "rgba(151,187,205,1)",
      data: {!! json_encode($dailyRevenue->values()) !!}
    }]
  };
  
  var ctx = $("#dailyRevenueChart").get(0).getContext("2d");
  var dailyChart = new Chart(ctx).Line(dailyRevenueData);
  @endif

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





