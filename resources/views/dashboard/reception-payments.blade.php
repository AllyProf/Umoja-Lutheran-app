@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-credit-card"></i> Payments</h1>
    <p>View payment history and transactions</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('reception.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Payments</a></li>
  </ul>
</div>

<!-- Statistics Cards -->
<div class="row mb-3">
  <div class="col-md-4">
    <div class="widget-small primary coloured-icon">
      <i class="icon fa fa-dollar fa-2x"></i>
      <div class="info">
        <h4>Total Paid</h4>
        <p><b>{{ number_format(($stats['total_paid'] ?? 0) * ($exchangeRate ?? 2500), 0) }} TZS</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="widget-small success coloured-icon">
      <i class="icon fa fa-calendar fa-2x"></i>
      <div class="info">
        <h4>Today's Revenue</h4>
        <p><b>{{ number_format(($stats['total_paid_today'] ?? 0) * ($exchangeRate ?? 2500), 0) }} TZS</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="widget-small info coloured-icon">
      <i class="icon fa fa-list fa-2x"></i>
      <div class="info">
        <h4>Today's Payments</h4>
        <p><b>{{ $stats['total_payments_today'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
</div>

<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn mb-3">
        <h3 class="title">Payment History</h3>
      </div>
      
      <!-- Filters -->
      <div class="row mb-3">
        <div class="col-md-3">
          <select class="form-control" id="paymentStatusFilter" onchange="filterPayments()">
            <option value="all">All Status</option>
            <option value="paid">Paid</option>
            <option value="failed">Failed</option>
          </select>
        </div>
        <div class="col-md-7">
          <input type="text" class="form-control" id="searchInput" placeholder="Search by booking ref, guest name, email, or transaction ID..." onkeyup="filterPayments()">
        </div>
        <div class="col-md-2">
          <button class="btn btn-secondary btn-block" onclick="resetPaymentFilters()">
            <i class="fa fa-refresh"></i> Reset
          </button>
        </div>
      </div>
      
      <div class="tile-body">
        @if($payments->count() > 0)
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>Booking Reference</th>
                <th>Guest</th>
                <th>Room</th>
                <th>Amount Paid</th>
                <th>Payment Method</th>
                <th>Transaction ID</th>
                <th>Payment Date</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($payments as $payment)
              <tr class="payment-row"
                  data-payment-status="{{ $payment->payment_status }}"
                  data-booking-ref="{{ strtolower($payment->booking_reference) }}"
                  data-guest-name="{{ strtolower($payment->guest_name) }}"
                  data-guest-email="{{ strtolower($payment->guest_email) }}"
                  data-transaction-id="{{ strtolower($payment->payment_transaction_id ?? '') }}"
                  data-booking-id="{{ $payment->id }}">
                <td><strong>{{ $payment->booking_reference }}</strong></td>
                <td>
                  <strong>{{ $payment->guest_name }}</strong><br>
                  <small>{{ $payment->guest_email }}</small>
                </td>
                <td>
                  <span class="badge badge-primary">{{ $payment->room->room_type ?? 'N/A' }}</span><br>
                  <small>{{ $payment->room->room_number ?? 'N/A' }}</small>
                </td>
                <td>
                  <strong>{{ number_format(($payment->amount_paid ?? $payment->total_price) * $exchangeRate, 0) }} TZS</strong>
                </td>
                <td>{{ ucfirst($payment->payment_method ?? 'N/A') }}</td>
                <td>
                  @if($payment->payment_transaction_id)
                    <small>{{ $payment->payment_transaction_id }}</small>
                  @else
                    <span class="text-muted">N/A</span>
                  @endif
                </td>
                <td>
                  @if($payment->paid_at)
                    {{ $payment->paid_at->format('M d, Y H:i') }}
                  @else
                    <span class="text-muted">N/A</span>
                  @endif
                </td>
                <td>
                  @if($payment->payment_status === 'paid')
                    <span class="badge badge-success">Paid</span>
                  @else
                    <span class="badge badge-danger">{{ ucfirst($payment->payment_status) }}</span>
                  @endif
                </td>
                <td>
                  <button onclick="viewPaymentDetails({{ $payment->id }}, '{{ $payment->booking_reference }}')" class="btn btn-sm btn-info" title="View Details">
                    <i class="fa fa-eye"></i> View
                  </button>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        
        <div class="d-flex justify-content-center mt-3">
          {{ $payments->links() }}
        </div>
        @else
        <div class="text-center" style="padding: 50px;">
          <i class="fa fa-credit-card fa-5x text-muted mb-3"></i>
          <h3>No Payments Found</h3>
          <p class="text-muted">No payments match your filters.</p>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Payment Details Modal -->
<div class="modal fade" id="paymentDetailsModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-credit-card"></i> Payment Details</h5>
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
  const paymentStatusFilter = document.getElementById('paymentStatusFilter').value;
  const searchInput = document.getElementById('searchInput').value.toLowerCase();
  
  const rows = document.querySelectorAll('.payment-row');
  rows.forEach(row => {
    const paymentStatus = row.getAttribute('data-payment-status');
    const bookingRef = row.getAttribute('data-booking-ref');
    const guestName = row.getAttribute('data-guest-name');
    const guestEmail = row.getAttribute('data-guest-email');
    const transactionId = row.getAttribute('data-transaction-id');
    
    let show = true;
    
    // Payment status filter
    if (paymentStatusFilter !== 'all' && paymentStatus !== paymentStatusFilter) {
      show = false;
    }
    
    // Search filter
    if (searchInput) {
      if (!bookingRef.includes(searchInput) && 
          !guestName.includes(searchInput) && 
          !guestEmail.includes(searchInput) &&
          !transactionId.includes(searchInput)) {
        show = false;
      }
    }
    
    row.style.display = show ? '' : 'none';
  });
}

function resetPaymentFilters() {
  document.getElementById('paymentStatusFilter').value = 'all';
  document.getElementById('searchInput').value = '';
  filterPayments();
}

function viewPaymentDetails(bookingId, bookingRef) {
  $('#paymentDetailsModal').modal('show');
  document.getElementById('paymentDetailsContent').innerHTML = `
    <div class="text-center">
      <i class="fa fa-spinner fa-spin fa-2x"></i>
      <p>Loading payment details...</p>
    </div>
  `;
  
  @php
    // Determine which route to use based on current route name
    $currentRoute = request()->route()->getName() ?? '';
    $bookingShowRoute = (str_starts_with($currentRoute, 'admin.')) 
      ? 'admin.bookings.show' 
      : 'reception.bookings.show';
  @endphp
  
  // Fetch booking details - payment ID is actually the booking ID
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
      // If booking endpoint doesn't work, try to get payment details
      // We need to find the booking reference from the table row
      throw new Error('Booking not found');
    }
    return response.json();
  })
  .then(data => {
    if (data.success) {
      const booking = data.booking;
      const room = booking.room || {};
      const exchangeRate = {{ $exchangeRate ?? 2500 }};
      const fallbackImage = '{{ asset("landing_page_assets/img/bg-img/1.jpg") }}';
      
      // Get payment details from the booking
      const payment = booking.payments && booking.payments.length > 0 ? booking.payments[0] : null;
      
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
              </table>
            </div>
            <div class="col-md-6">
              <h5 style="color: #e07632; border-bottom: 2px solid #e07632; padding-bottom: 5px; margin-bottom: 15px;"><i class="fa fa-credit-card"></i> Payment Information</h5>
              <table class="table table-sm table-bordered">
                <tr><td width="40%"><strong>Booking Reference:</strong></td><td><strong>${booking.booking_reference || 'N/A'}</strong></td></tr>
                <tr><td><strong>Amount Paid (TZS):</strong></td><td><strong>${(parseFloat(booking.amount_paid || booking.total_price || 0) * (booking.locked_exchange_rate || exchangeRate)).toLocaleString()} TZS</strong></td></tr>
                <tr><td><strong>Payment Method:</strong></td><td>${booking.payment_method || payment?.payment_method || 'N/A'}</td></tr>
                <tr><td><strong>Transaction ID:</strong></td><td>${booking.payment_transaction_id || payment?.payment_transaction_id || 'N/A'}</td></tr>
                <tr><td><strong>Payment Date:</strong></td><td>${booking.paid_at || payment?.paid_at ? new Date(booking.paid_at || payment.paid_at).toLocaleDateString('en-US', {year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit'}) : 'N/A'}</td></tr>
                <tr><td><strong>Payment Status:</strong></td><td>
                  ${booking.payment_status === 'paid' ? '<span class="badge badge-success">Paid</span>' : ''}
                  ${booking.payment_status === 'partial' ? '<span class="badge badge-info">Partial</span>' : ''}
                  ${booking.payment_status === 'pending' ? '<span class="badge badge-warning">Pending</span>' : ''}
                </td></tr>
                ${booking.payment_status === 'partial' && booking.amount_paid ? `
                <tr><td><strong>Remaining Amount (TZS):</strong></td><td><strong style="color: #dc3545;">${(parseFloat((booking.total_price || 0) - (booking.amount_paid || 0)) * (booking.locked_exchange_rate || exchangeRate)).toLocaleString()} TZS</strong></td></tr>
                <tr><td><strong>Payment Percentage:</strong></td><td><span class="badge badge-info">${parseFloat(((booking.amount_paid || 0) / (booking.total_price || 1)) * 100).toFixed(0)}%</span></td></tr>
                ` : ''}
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
                </td></tr>
                <tr><td><strong>Check-in Status:</strong></td><td>
                  ${booking.check_in_status === 'checked_in' ? '<span class="badge badge-success">Checked In</span>' : ''}
                  ${booking.check_in_status === 'checked_out' ? '<span class="badge badge-info">Checked Out</span>' : ''}
                  ${booking.check_in_status === 'pending' ? '<span class="badge badge-warning">Pending</span>' : ''}
                </td></tr>
              </table>
            </div>
            <div class="col-md-6">
              <h5 style="color: #e07632; border-bottom: 2px solid #e07632; padding-bottom: 5px; margin-bottom: 15px;"><i class="fa fa-bed"></i> Room Information</h5>
              <table class="table table-sm table-bordered">
                <tr><td width="40%"><strong>Room Number:</strong></td><td><strong>${room.room_number || 'N/A'}</strong></td></tr>
                <tr><td><strong>Room Type:</strong></td><td>${room.room_type || 'N/A'}</td></tr>
                <tr><td><strong>Capacity:</strong></td><td>${room.capacity || 'N/A'} guests</td></tr>
                <tr><td><strong>Price per Night (TZS):</strong></td><td>${(parseFloat(room.price_per_night || 0) * (booking.locked_exchange_rate || exchangeRate)).toLocaleString()} TZS</td></tr>
                <tr><td><strong>Total Price (TZS):</strong></td><td><strong>${(parseFloat(booking.total_price || 0) * (booking.locked_exchange_rate || exchangeRate)).toLocaleString()} TZS</strong></td></tr>
              </table>
            </div>
          </div>
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
        <i class="fa fa-exclamation-triangle"></i> An error occurred while loading payment details. Please try again.
      </div>
    `;
  });
}
</script>
@endsection




