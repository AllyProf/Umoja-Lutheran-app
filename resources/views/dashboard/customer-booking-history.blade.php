@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-history"></i> Booking History</h1>
    <p>View your completed bookings</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Booking History</a></li>
  </ul>
</div>

<!-- Booking History Table -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-list"></i> Completed Bookings</h3>
      <div class="tile-body">
        @if($bookings->count() > 0)
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>Booking Reference</th>
                <th>Room</th>
                <th>Check-in</th>
                <th>Check-out</th>
                <th>Nights</th>
                <th>Total Price</th>
                <th>Payment Status</th>
                <th>Check-out Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($bookings as $booking)
              <tr>
                <td><strong>{{ $booking->booking_reference }}</strong></td>
                <td>
                  <span class="badge badge-primary">{{ $booking->room->room_type ?? 'N/A' }}</span><br>
                  <small>{{ $booking->room->room_number ?? 'N/A' }}</small>
                </td>
                <td>{{ $booking->check_in->format('M d, Y') }}</td>
                <td>{{ $booking->check_out->format('M d, Y') }}</td>
                <td>{{ $booking->check_in->diffInDays($booking->check_out) }} nights</td>
                <td>
                  <div><strong>${{ number_format($booking->total_price, 2) }}</strong></div>
                  <div style="color: #e07632; font-size: 11px;">
                    <strong>≈ {{ number_format($booking->total_price * ($booking->locked_exchange_rate ?? $exchangeRate ?? 2500), 2) }} TZS</strong>
                  </div>
                </td>
                <td>
                  @if($booking->payment_status === 'paid')
                    <span class="badge badge-success">Paid</span>
                    @if($booking->paid_at)
                      <br><small>{{ $booking->paid_at->format('M d, Y') }}</small>
                    @endif
                  @else
                    <span class="badge badge-secondary">{{ ucfirst($booking->payment_status ?? 'N/A') }}</span>
                  @endif
                </td>
                <td>
                  @if($booking->checked_out_at)
                    {{ $booking->checked_out_at->format('M d, Y H:i') }}
                  @else
                    <span class="text-muted">N/A</span>
                  @endif
                </td>
                <td>
                  <button onclick="viewBookingDetails({{ $booking->id }})" class="btn btn-sm btn-info" title="View Details">
                    <i class="fa fa-eye"></i> View Details
                  </button>
                  @if($booking->check_in_status === 'checked_out')
                  <a href="{{ route('customer.bookings.checkout-bill', $booking) }}" class="btn btn-sm btn-warning mt-1" title="View Bill">
                    <i class="fa fa-file-text"></i> View Bill
                  </a>
                  @endif
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
          <i class="fa fa-history fa-5x text-muted mb-3"></i>
          <h3>No Booking History</h3>
          <p class="text-muted">You don't have any completed bookings yet.</p>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- View Booking Details Modal -->
<div class="modal fade" id="bookingDetailsModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-calendar-check-o"></i> Booking Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
<script>
function viewBookingDetails(bookingId) {
    // Show modal with loading state
    $('#bookingDetailsModal').modal('show');
    document.getElementById('bookingDetailsContent').innerHTML = `
        <div class="text-center">
            <i class="fa fa-spinner fa-spin fa-2x"></i>
            <p>Loading booking details...</p>
        </div>
    `;
    
    fetch('{{ route("customer.bookings.show", ":id") }}'.replace(':id', bookingId), {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const booking = data.booking;
            const room = booking.room || {};
            // Use locked exchange rate from booking if available, otherwise use current rate
            const exchangeRate = booking.locked_exchange_rate || {{ $exchangeRate ?? 2500 }};
            const fallbackImage = '{{ asset("landing_page_assets/img/bg-img/1.jpg") }}';
            
            // Calculate totals using stored values if available
            const roomPriceUsd = parseFloat(booking.total_price || 0);
            const roomPriceTsh = roomPriceUsd * exchangeRate;
            const serviceChargesTsh = parseFloat(booking.total_service_charges_tsh || 0);
            const serviceChargesUsd = serviceChargesTsh / exchangeRate;
            // Use total_bill_tsh if available, otherwise calculate
            const totalBillTsh = booking.total_bill_tsh || (roomPriceTsh + serviceChargesTsh);
            const totalBillUsd = totalBillTsh / exchangeRate;
            // Amount paid - use the stored USD value and convert using the same exchange rate
            const amountPaidUsd = parseFloat(booking.amount_paid || (booking.payment_status === 'paid' ? totalBillUsd : 0));
            // Ensure Amount Paid TZS matches Total Bill TZS if fully paid
            const amountPaidTsh = booking.payment_status === 'paid' && Math.abs(amountPaidUsd - totalBillUsd) < 0.01 
                ? totalBillTsh 
                : amountPaidUsd * exchangeRate;
            
            const detailsHtml = `
                <div class="booking-details-view">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 style="color: #e07632; border-bottom: 2px solid #e07632; padding-bottom: 5px; margin-bottom: 15px;"><i class="fa fa-user"></i> Guest Information</h5>
                            <table class="table table-sm table-bordered">
                                <tr><td width="40%"><strong>Name:</strong></td><td>${booking.guest_name || 'N/A'}</td></tr>
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
                                <tr><td><strong>Check-in:</strong></td><td>${booking.check_in ? (() => {
                                    const date = new Date(booking.check_in);
                                    return date.toLocaleDateString('en-US', {year: 'numeric', month: 'long', day: 'numeric'});
                                })() : 'N/A'}</td></tr>
                                <tr><td><strong>Check-out:</strong></td><td>${booking.check_out ? (() => {
                                    const date = new Date(booking.check_out);
                                    return date.toLocaleDateString('en-US', {year: 'numeric', month: 'long', day: 'numeric'});
                                })() : 'N/A'}</td></tr>
                                <tr><td><strong>Status:</strong></td><td>
                                    ${booking.status === 'pending' ? '<span class="badge badge-warning">Pending</span>' : ''}
                                    ${booking.status === 'confirmed' ? '<span class="badge badge-success">Confirmed</span>' : ''}
                                    ${booking.status === 'completed' ? '<span class="badge badge-info">Completed</span>' : ''}
                                    ${booking.status === 'cancelled' ? '<span class="badge badge-danger">Cancelled</span>' : ''}
                                    ${!booking.status || (booking.status !== 'pending' && booking.status !== 'confirmed' && booking.status !== 'completed' && booking.status !== 'cancelled') ? '<span class="badge badge-secondary">' + (booking.status ? booking.status.charAt(0).toUpperCase() + booking.status.slice(1) : 'N/A') + '</span>' : ''}
                                </td></tr>
                                <tr><td><strong>Payment Status:</strong></td><td>
                                    ${booking.payment_status === 'paid' ? '<span class="badge badge-success">Paid</span>' : ''}
                                    ${booking.payment_status === 'partial' ? '<span class="badge badge-info">Partial</span>' : ''}
                                    ${booking.payment_status === 'pending' ? '<span class="badge badge-warning">Pending</span>' : ''}
                                    ${booking.payment_status === 'failed' ? '<span class="badge badge-danger">Failed</span>' : ''}
                                    ${!booking.payment_status || (booking.payment_status !== 'paid' && booking.payment_status !== 'partial' && booking.payment_status !== 'pending' && booking.payment_status !== 'failed') ? '<span class="badge badge-secondary">' + (booking.payment_status ? booking.payment_status.charAt(0).toUpperCase() + booking.payment_status.slice(1) : 'N/A') + '</span>' : ''}
                                </td></tr>
                                <tr><td><strong>Check-in Status:</strong></td><td>
                                    ${booking.check_in_status === 'pending' ? '<span class="badge badge-warning">Not Checked In</span>' : ''}
                                    ${booking.check_in_status === 'checked_in' ? '<span class="badge badge-success">Checked In</span>' : ''}
                                    ${booking.check_in_status === 'checked_out' ? '<span class="badge badge-info">Checked Out</span>' : ''}
                                    ${!booking.check_in_status || (booking.check_in_status !== 'pending' && booking.check_in_status !== 'checked_in' && booking.check_in_status !== 'checked_out') ? '<span class="badge badge-secondary">' + (booking.check_in_status ? booking.check_in_status.replace('_', ' ').split(' ').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ') : 'N/A') + '</span>' : ''}
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
                                <tr><td><strong>Price per Night:</strong></td><td>$${parseFloat(room.price_per_night || 0).toFixed(2)} USD</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 style="color: #e07632; border-bottom: 2px solid #e07632; padding-bottom: 5px; margin-bottom: 15px;"><i class="fa fa-dollar"></i> Payment Information</h5>
                            <table class="table table-sm table-bordered">
                                <tr><td width="40%"><strong>Room Price:</strong></td><td><strong>$${roomPriceUsd.toFixed(2)} USD</strong><br><small style="color: #666;">≈ ${roomPriceTsh.toLocaleString('en-US', {maximumFractionDigits: 2})} TZS</small></td></tr>
                                ${serviceChargesTsh > 0 ? `
                                <tr><td><strong>Service Charges:</strong></td><td><strong>${serviceChargesTsh.toLocaleString('en-US', {maximumFractionDigits: 2})} TZS</strong><br><small style="color: #666;">≈ $${serviceChargesUsd.toFixed(2)} USD</small></td></tr>
                                ` : ''}
                                <tr style="border-top: 2px solid #e07632;"><td><strong>Total Bill:</strong></td><td><strong style="color: #e07632; font-size: 16px;">$${totalBillUsd.toFixed(2)} USD</strong><br><small style="color: #666;">≈ ${totalBillTsh.toLocaleString('en-US', {maximumFractionDigits: 2})} TZS</small></td></tr>
                                <tr><td><strong>Amount Paid:</strong></td><td><strong style="color: #28a745;">$${amountPaidUsd.toFixed(2)} USD</strong><br><small style="color: #666;">≈ ${amountPaidTsh.toLocaleString('en-US', {maximumFractionDigits: 2})} TZS</small></td></tr>
                                ${booking.payment_status === 'partial' && amountPaidUsd > 0 ? `
                                <tr><td><strong>Remaining Amount:</strong></td><td><strong style="color: #dc3545;">$${(totalBillUsd - amountPaidUsd).toFixed(2)} USD</strong><br><small style="color: #666;">≈ ${(totalBillTsh - amountPaidTsh).toLocaleString('en-US', {maximumFractionDigits: 2})} TZS</small></td></tr>
                                ` : ''}
                                <tr><td><strong>Payment Method:</strong></td><td>${booking.payment_method ? booking.payment_method.charAt(0).toUpperCase() + booking.payment_method.slice(1) : 'N/A'}</td></tr>
                                ${booking.paid_at ? `<tr><td><strong>Paid At:</strong></td><td>${new Date(booking.paid_at).toLocaleString()}</td></tr>` : ''}
                                ${booking.checked_in_at ? `<tr><td><strong>Checked In At:</strong></td><td>${new Date(booking.checked_in_at).toLocaleString()}</td></tr>` : ''}
                                ${booking.checked_out_at ? `<tr><td><strong>Checked Out At:</strong></td><td>${new Date(booking.checked_out_at).toLocaleString()}</td></tr>` : ''}
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
                <i class="fa fa-exclamation-triangle"></i> An error occurred while loading booking details.
            </div>
        `;
    });
}
</script>
@endsection



