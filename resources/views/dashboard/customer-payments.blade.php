@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-credit-card"></i> My Payments</h1>
    <p>View your payment history</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">My Payments</a></li>
  </ul>
</div>

<!-- Payment Statistics -->
<div class="row mb-3">
  <div class="col-md-4">
    <div class="widget-small primary coloured-icon">
      <i class="icon fa fa-money fa-2x"></i>
      <div class="info">
        <h4>Total Paid</h4>
        <p><b>{{ number_format(($totalPaid ?? 0) * ($exchangeRate ?? 2500), 2) }} TZS</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="widget-small info coloured-icon">
      <i class="icon fa fa-check-circle fa-2x"></i>
      <div class="info">
        <h4>Paid/Partial Bookings</h4>
        <p><b>{{ $totalBookings ?? 0 }}</b></p>
        <small style="color: #666;">Bookings with payments</small>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="widget-small success coloured-icon">
      <i class="icon fa fa-calendar fa-2x"></i>
      <div class="info">
        <h4>Total Bookings</h4>
        <p><b>{{ $bookings->total() ?? 0 }}</b></p>
      </div>
    </div>
  </div>
</div>

<!-- Payments Table -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-list"></i> Payment History</h3>
      <div class="tile-body">
        @if($bookings->count() > 0)
        
        <!-- Mobile Card View -->
        <div class="mobile-payments-cards d-md-none">
          @foreach($bookings as $booking)
          <div class="payment-card-mobile mb-3" style="border: 1px solid #ddd; border-radius: 8px; padding: 15px; background: white;">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <div>
                <strong style="font-size: 16px; color: #e07632;">{{ $booking->booking_reference }}</strong>
                @if($booking->is_corporate_booking ?? false)
                  <br><span class="badge badge-info" style="font-size: 10px;"><i class="fa fa-building"></i> Company</span>
                @endif
                <br><small class="text-muted">Room: {{ $booking->room->room_number ?? 'N/A' }}</small>
              </div>
              <div class="text-right">
                @if($booking->payment_status === 'paid')
                  <span class="badge badge-success">Paid</span>
                @elseif($booking->payment_status === 'failed')
                  <span class="badge badge-danger">Failed</span>
                @elseif($booking->payment_status === 'partial')
                  <span class="badge badge-info">Partial</span>
                @elseif($booking->payment_status === 'refunded')
                  <span class="badge badge-warning">Refunded</span>
                @else
                  <span class="badge badge-secondary">{{ ucfirst($booking->payment_status ?? 'N/A') }}</span>
                @endif
              </div>
            </div>
            
            <div class="row mb-2">
              <div class="col-6">
                <small class="text-muted">Room Type</small><br>
                <span class="badge badge-primary">{{ $booking->room->room_type ?? 'N/A' }}</span>
              </div>
              <div class="col-6">
                <small class="text-muted">Amount Paid</small><br>
                @php
                  $displayAmount = $booking->amount_paid ?? $booking->total_price ?? 0;
                  $displayExchangeRate = $booking->locked_exchange_rate ?? $exchangeRate ?? 2500;
                @endphp
                <strong style="color: #e07632;">{{ number_format($displayAmount * $displayExchangeRate, 2) }} TZS</strong>
                @if($booking->is_service_payment_only ?? false)
                  <br><small class="text-muted" style="font-size: 10px;">(Services only)</small>
                @elseif($booking->is_corporate_booking ?? false)
                  <br><small class="text-info" style="font-size: 10px;">(Company Billed)</small>
                @endif
              </div>
            </div>
            
            <div class="row mb-2">
              <div class="col-6">
                <small class="text-muted">Check-in</small><br>
                <strong>{{ $booking->check_in->format('M d, Y') }}</strong>
              </div>
              <div class="col-6">
                <small class="text-muted">Check-out</small><br>
                <strong>{{ $booking->check_out->format('M d, Y') }}</strong>
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
                  <br><span class="badge badge-info" style="margin-top: 5px; font-size: 10px;" title="Originally scheduled to check out on {{ \Carbon\Carbon::parse($booking->original_check_out)->format('M d, Y') }}">
                    <i class="fa fa-calendar-plus-o"></i> Extended by {{ $extendedNights }} night{{ $extendedNights > 1 ? 's' : '' }}
                  </span>
                @elseif($isDecreased && $decreasedNights > 0)
                  <br><span class="badge badge-warning" style="margin-top: 5px; font-size: 10px;" title="Originally scheduled to check out on {{ \Carbon\Carbon::parse($booking->original_check_out)->format('M d, Y') }}">
                    <i class="fa fa-calendar-minus-o"></i> Decreased by {{ $decreasedNights }} night{{ $decreasedNights > 1 ? 's' : '' }}
                  </span>
                @endif
              </div>
            </div>
            
            @if($isExtended && $booking->room && $extendedNights > 0)
            <div class="row mb-2">
              <div class="col-12">
                <small class="text-muted">Extension Details</small><br>
                @php
                  $extensionCost = $booking->room->price_per_night * $extendedNights;
                  $originalPrice = $booking->total_price - $extensionCost;
                  $originalNights = $booking->check_in->diffInDays(\Carbon\Carbon::parse($booking->original_check_out));
                @endphp
                <small class="text-info">
                  <i class="fa fa-calendar-plus-o"></i> +{{ number_format($extensionCost * $displayExchangeRate, 2) }} TZS (extension)<br>
                  <span class="text-muted">Original: {{ $originalNights }} nights, {{ number_format($originalPrice * $displayExchangeRate, 2) }} TZS</span>
                </small>
              </div>
            </div>
            @elseif($isDecreased && $booking->room && $decreasedNights > 0)
            <div class="row mb-2">
              <div class="col-12">
                <small class="text-muted">Decrease Details</small><br>
                @php
                  $decreaseRefund = $booking->room->price_per_night * $decreasedNights;
                  $originalPrice = $booking->total_price + $decreaseRefund;
                  $originalNights = $booking->check_in->diffInDays(\Carbon\Carbon::parse($booking->original_check_out));
                @endphp
                <small class="text-warning">
                  <i class="fa fa-calendar-minus-o"></i> -{{ number_format($decreaseRefund * $displayExchangeRate, 2) }} TZS (decrease)<br>
                  <span class="text-muted">Original: {{ $originalNights }} nights, {{ number_format($originalPrice * $displayExchangeRate, 2) }} TZS</span>
                </small>
              </div>
            </div>
            @endif
            
            <div class="row mb-2">
              <div class="col-6">
                <small class="text-muted">Check-in Status</small><br>
                @if($booking->check_in_status === 'checked_in')
                  <span class="badge badge-success">Checked In</span>
                @elseif($booking->check_in_status === 'checked_out')
                  <span class="badge badge-info">Checked Out</span>
                @else
                  <span class="badge badge-warning">Pending</span>
                @endif
              </div>
              <div class="col-6">
                <small class="text-muted">Check-out Status</small><br>
                @if($booking->check_in_status === 'checked_out')
                  <span class="badge badge-info">Checked Out</span>
                @else
                  <span class="badge badge-secondary">Not Checked Out</span>
                @endif
              </div>
            </div>
            
            <div class="row mb-2">
              <div class="col-6">
                <small class="text-muted">Payment Method</small><br>
                @if($booking->payment_method)
                  <span class="badge badge-info">{{ ucfirst($booking->payment_method) }}</span>
                @else
                  <span class="text-muted">N/A</span>
                @endif
              </div>
              <div class="col-6">
                <small class="text-muted">Payment Date</small><br>
                @if($booking->paid_at)
                  <strong>{{ $booking->paid_at->format('M d, Y') }}</strong><br>
                  <small>{{ $booking->paid_at->format('H:i') }}</small>
                @else
                  <span class="text-muted">N/A</span>
                @endif
              </div>
            </div>
            
            @if($booking->payment_transaction_id)
            <div class="mb-2">
              <small class="text-muted">Transaction ID</small><br>
              <small style="word-break: break-all;">{{ $booking->payment_transaction_id }}</small>
            </div>
            @endif
            
            <div class="mt-3 pt-3" style="border-top: 1px solid #eee;">
              <div class="d-flex flex-column gap-2">
                <button onclick="viewBookingDetails({{ $booking->id }})" class="btn btn-sm btn-info btn-block mb-2" style="width: 100%;">
                  <i class="fa fa-eye"></i> View Booking Details
                </button>
                @if(in_array($booking->payment_status, ['paid', 'partial']) || $booking->status == 'confirmed')
                <a href="{{ route('customer.payment.receipt.download', $booking) }}?download=1" class="btn btn-sm btn-success btn-block" target="_blank" style="width: 100%;">
                  <i class="fa fa-download"></i> Download Receipt
                </a>
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
                <th>Check-in Status</th>
                <th>Check-out Status</th>
                <th>Amount Paid</th>
                <th>Payment Method</th>
                <th>Transaction ID</th>
                <th>Payment Date</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($bookings as $booking)
              <tr>
                <td>
                  <strong>{{ $booking->booking_reference }}</strong>
                  @if($booking->is_corporate_booking ?? false)
                    <br><span class="badge badge-info" style="font-size: 10px;"><i class="fa fa-building"></i> Company</span>
                  @endif
                </td>
                <td>
                  <span class="badge badge-primary">{{ $booking->room->room_type ?? 'N/A' }}</span><br>
                  <small>{{ $booking->room->room_number ?? 'N/A' }}</small>
                </td>
                <td>{{ $booking->check_in->format('M d, Y') }}</td>
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
                  @if($booking->check_in_status === 'checked_out')
                    <span class="badge badge-info">Checked Out</span>
                  @else
                    <span class="badge badge-secondary">Not Checked Out</span>
                  @endif
                </td>
                <td>
                  @php
                    $displayAmount = $booking->amount_paid ?? $booking->total_price ?? 0;
                    $displayExchangeRate = $booking->locked_exchange_rate ?? $exchangeRate ?? 2500;
                  @endphp
                  <div><strong>{{ number_format($displayAmount * $displayExchangeRate, 2) }} TZS</strong></div>
                  @if($booking->is_service_payment_only ?? false)
                    <br><small class="text-muted" style="font-size: 10px;">(Services only)</small>
                  @elseif($booking->is_corporate_booking ?? false)
                    <br><small class="text-info" style="font-size: 10px;">(Company Billed)</small>
                  @endif
                  @if($isExtended && $booking->room && $extendedNights > 0 && !($booking->is_corporate_booking ?? false))
                    @php
                      $extensionCost = $booking->room->price_per_night * $extendedNights;
                      $originalPrice = $booking->total_price - $extensionCost;
                    @endphp
                    <br><small class="text-info" style="display: block; margin-top: 5px;">
                      <i class="fa fa-calendar-plus-o"></i> +{{ number_format($extensionCost * $displayExchangeRate, 2) }} TZS (extension)
                    </small>
                    <br><small class="text-muted">Original: {{ number_format($originalPrice * $displayExchangeRate, 2) }} TZS</small>
                  @elseif($isDecreased && $booking->room && $decreasedNights > 0 && !($booking->is_corporate_booking ?? false))
                    @php
                      $decreaseRefund = $booking->room->price_per_night * $decreasedNights;
                      $originalPrice = $booking->total_price + $decreaseRefund;
                    @endphp
                    <br><small class="text-warning" style="display: block; margin-top: 5px;">
                      <i class="fa fa-calendar-minus-o"></i> -{{ number_format($decreaseRefund * $displayExchangeRate, 2) }} TZS (decrease)
                    </small>
                    <br><small class="text-muted">Original: {{ number_format($originalPrice * $displayExchangeRate, 2) }} TZS</small>
                  @endif
                </td>
                <td>
                  @if($booking->payment_method)
                    <span class="badge badge-info">{{ ucfirst($booking->payment_method) }}</span>
                  @else
                    <span class="text-muted">N/A</span>
                  @endif
                </td>
                <td>
                  @if($booking->payment_transaction_id)
                    <small>{{ $booking->payment_transaction_id }}</small>
                  @else
                    <span class="text-muted">N/A</span>
                  @endif
                </td>
                <td>
                  @if($booking->paid_at)
                    {{ $booking->paid_at->format('M d, Y H:i') }}
                  @else
                    <span class="text-muted">N/A</span>
                  @endif
                </td>
                <td>
                  @if($booking->payment_status === 'paid')
                    <span class="badge badge-success">Paid</span>
                  @elseif($booking->payment_status === 'failed')
                    <span class="badge badge-danger">Failed</span>
                  @elseif($booking->payment_status === 'partial')
                    <span class="badge badge-info">Partial</span>
                  @elseif($booking->payment_status === 'refunded')
                    <span class="badge badge-warning">Refunded</span>
                  @else
                    <span class="badge badge-secondary">{{ ucfirst($booking->payment_status ?? 'N/A') }}</span>
                  @endif
                </td>
                <td>
                  <button onclick="viewBookingDetails({{ $booking->id }})" class="btn btn-sm btn-info" title="View Booking">
                    <i class="fa fa-eye"></i> View
                  </button>
                  @if(in_array($booking->payment_status, ['paid', 'partial']) || $booking->status == 'confirmed')
                  <a href="{{ route('customer.payment.receipt.download', $booking) }}?download=1" class="btn btn-sm btn-success mt-1" target="_blank" title="Download Receipt">
                    <i class="fa fa-download"></i>
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
          <i class="fa fa-credit-card fa-5x text-muted mb-3"></i>
          <h3>No Payment History</h3>
          <p class="text-muted">You don't have any payment records yet.</p>
          <a href="{{ route('booking.index') }}" class="btn btn-primary btn-lg">
            <i class="fa fa-plus"></i> Book a Room
          </a>
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

@section('styles')
<style>
  /* Mobile Payments Cards */
  @media (max-width: 768px) {
    .mobile-payments-cards {
      display: block;
    }
    
    .payment-card-mobile {
      transition: all 0.3s ease;
    }
    
    .payment-card-mobile:hover {
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .payment-card-mobile .btn {
      font-size: 12px;
      padding: 8px 12px;
    }
    
    .payment-card-mobile .badge {
      font-size: 11px;
      padding: 4px 8px;
    }
    
    /* Hide desktop table on mobile */
    .table-responsive.d-none {
      display: none !important;
    }
  }
  
  /* Desktop Table Styles */
  @media (min-width: 768px) {
    .table-responsive {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }
    
    .table {
      font-size: 13px;
    }
    
    .table th,
    .table td {
      padding: 10px 8px;
      white-space: nowrap;
    }
    
    .table .badge {
      font-size: 11px;
      padding: 4px 8px;
    }
    
    .table .btn-sm {
      font-size: 11px;
      padding: 4px 8px;
    }
  }
  
  /* Statistics Widgets - Mobile */
  @media (max-width: 768px) {
    .widget-small {
      margin-bottom: 15px;
    }
    
    .widget-small .info h4 {
      font-size: 14px;
    }
    
    .widget-small .info p {
      font-size: 18px;
    }
    
    .widget-small .info small {
      font-size: 11px;
    }
  }
</style>
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
            const exchangeRate = booking.locked_exchange_rate || {{ $exchangeRate ?? 2500 }};
            const fallbackImage = '{{ asset("landing_page_assets/img/bg-img/1.jpg") }}';
            
            // Corporate Logic
            const company = booking.company || {};
            const isCorporate = booking.is_corporate_booking == 1 || booking.company_id != null;
            const companyPays = isCorporate && booking.payment_responsibility === 'company';
            const selfPays = isCorporate && booking.payment_responsibility === 'self';

            const detailsHtml = `
                <div class="booking-details-view">
                    <!-- Top Status Bar -->
                    <div class="d-flex justify-content-between align-items-center mb-4 bg-light p-3 rounded">
                        <div>
                           <h4 class="mb-0" style="color: #e07632;">${booking.guest_name || 'Guest'}</h4>
                           <span class="text-muted small"><i class="fa fa-hashtag"></i> ${booking.booking_reference}</span>
                        </div>
                        <div class="text-right">
                           ${booking.status === 'confirmed' ? '<span class="badge badge-success p-2">Confirmed</span>' : ''}
                           ${booking.status === 'pending' ? '<span class="badge badge-warning p-2">Pending</span>' : ''}
                           ${booking.status === 'cancelled' ? '<span class="badge badge-danger p-2">Cancelled</span>' : ''}
                           ${booking.status === 'completed' ? '<span class="badge badge-info p-2">Completed</span>' : ''}
                           <div class="mt-1 small text-muted">
                              ${booking.check_in_status === 'checked_in' ? '<i class="fa fa-check-circle text-success"></i> Checked In' : 
                                booking.check_in_status === 'checked_out' ? '<i class="fa fa-check-circle text-secondary"></i> Checked Out' : 
                                '<i class="fa fa-clock-o"></i> Status: ' + (booking.check_in_status === 'pending' ? 'Not Checked In' : (booking.check_in_status || 'Pending'))}
                           </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            
                            <!-- Guest Details Card -->
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-white border-bottom-0 pb-0">
                                    <h6 class="text-uppercase text-muted mb-0" style="font-size: 0.75rem; letter-spacing: 1px;">Guest Details</h6>
                                </div>
                                <div class="card-body pt-2">
                                   <div class="d-flex align-items-center mb-3">
                                      <div class="mr-3 text-center" style="width: 40px;">
                                        <i class="fa fa-user-circle fa-2x text-muted"></i>
                                      </div>
                                      <div>
                                        <div class="font-weight-bold">${booking.first_name || ''} ${booking.last_name || ''}</div>
                                        <div class="text-muted small">${booking.guest_email}</div>
                                      </div>
                                   </div>
                                   <div class="row mb-2">
                                     <div class="col-1 text-center"><i class="fa fa-phone text-muted"></i></div>
                                     <div class="col-11">${booking.guest_phone || 'N/A'}</div>
                                   </div>
                                   <div class="row mb-2">
                                     <div class="col-1 text-center"><i class="fa fa-globe text-muted"></i></div>
                                     <div class="col-11">${booking.country || 'N/A'}</div>
                                   </div>
                                   <div class="row">
                                     <div class="col-1 text-center"><i class="fa fa-users text-muted"></i></div>
                                     <div class="col-11">${booking.number_of_guests || 1} Person(s)</div>
                                   </div>
                                </div>
                            </div>

                            <!-- Stay Dates Card -->
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-white border-bottom-0 pb-0">
                                    <h6 class="text-uppercase text-muted mb-0" style="font-size: 0.75rem; letter-spacing: 1px;">Stay Information</h6>
                                </div>
                                <div class="card-body pt-2">
                                   <div class="d-flex justify-content-between align-items-center bg-light rounded p-3 mb-2">
                                      <div class="text-center">
                                         <div class="text-muted small text-uppercase">Check In</div>
                                         <div class="font-weight-bold" style="color: #e07632;">${booking.check_in ? new Date(booking.check_in).toLocaleDateString('en-US', {day: 'numeric', month: 'short', year: 'numeric'}) : 'N/A'}</div>
                                      </div>
                                      <div class="text-muted"><i class="fa fa-arrow-right"></i></div>
                                      <div class="text-center">
                                         <div class="text-muted small text-uppercase">Check Out</div>
                                         <div class="font-weight-bold" style="color: #e07632;">${booking.check_out ? new Date(booking.check_out).toLocaleDateString('en-US', {day: 'numeric', month: 'short', year: 'numeric'}) : 'N/A'}</div>
                                      </div>
                                   </div>
                                   
                                   ${room.images && room.images.length > 0 ? `
                                   <div class="mt-3 text-center">
                                      ${(() => {
                                          let imgPath = room.images[0];
                                          if (imgPath.startsWith('rooms/') || imgPath.startsWith('storage/rooms/')) {
                                              imgPath = imgPath.replace(/^storage\//, '');
                                          } else if (!imgPath.startsWith('http') && !imgPath.startsWith('/')) {
                                              imgPath = 'rooms/' + imgPath;
                                          }
                                          const storageBase = '{{ asset("storage") }}';
                                          const imageUrl = imgPath.startsWith('http') ? imgPath : storageBase + '/' + imgPath;
                                          return '<img src="' + imageUrl + '" alt="Room Image" class="img-fluid rounded shadow-sm" style="max-height: 150px; object-fit: cover; width: 100%;">';
                                      })()}
                                   </div>
                                   ` : ''}
                                </div>
                            </div>

                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">
                            
                             <!-- Room Details Card -->
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-white border-bottom-0 pb-0">
                                    <h6 class="text-uppercase text-muted mb-0" style="font-size: 0.75rem; letter-spacing: 1px;">Room Assigned</h6>
                                </div>
                                <div class="card-body pt-2">
                                   <div class="d-flex align-items-center mb-3">
                                      <div class="mr-3 text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background-color: #e07632;">
                                        <i class="fa fa-bed"></i>
                                      </div>
                                      <div>
                                         <div class="h5 mb-0">${room.room_number || 'Unassigned'}</div>
                                         <div class="text-muted small">${room.room_type || 'Standard'}</div>
                                      </div>
                                   </div>
                                   <div class="d-flex justify-content-between small text-muted border-top pt-2">
                                      <span>Price per Night:</span>
                                      <span class="font-weight-bold">${(parseFloat(room.price_per_night || 0) * exchangeRate).toLocaleString()} TZS</span>
                                   </div>
                                </div>
                            </div>

                            <!-- Payment Card -->
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-white border-bottom-0 pb-0">
                                    <h6 class="text-uppercase text-muted mb-0" style="font-size: 0.75rem; letter-spacing: 1px;">Payment & Billing</h6>
                                </div>
                                <div class="card-body pt-2">
                                   
                                   ${companyPays ? `
                                        <div class="alert alert-info small mb-3">
                                            <i class="fa fa-building-o"></i> <strong>Corporate Booking</strong><br>
                                            Room charges are billed directly to <strong>${company.name || 'the Company'}</strong>.
                                        </div>
                                   ` : ''}

                                   ${selfPays ? `
                                        <div class="alert alert-warning small mb-3">
                                            <i class="fa fa-user"></i> <strong>Self-Pay Corporate Booking</strong><br>
                                            You are responsible for these charges.
                                        </div>
                                   ` : ''}

                                   <div class="d-flex justify-content-between mb-2">
                                      <span class="text-muted">Total Price:</span>
                                      <span class="font-weight-bold h5 mb-0" style="color: #e07632;">${(parseFloat(booking.total_price || 0) * exchangeRate).toLocaleString()} TZS</span>
                                   </div>

                                   <div class="d-flex justify-content-between mb-2 small">
                                      <span class="text-muted">Amount Paid:</span>
                                      <span class="text-success font-weight-bold">-${(parseFloat(booking.amount_paid || 0) * exchangeRate).toLocaleString()} TZS</span>
                                   </div>
                                   
                                   ${booking.payment_status === 'partial' ? `
                                   <hr class="my-2">
                                   <div class="d-flex justify-content-between align-items-end">
                                      <span class="font-weight-bold">Remaining:</span>
                                      <span class="h6 mb-0 text-danger">${(parseFloat((booking.total_price || 0) - (booking.amount_paid || 0)) * exchangeRate).toLocaleString()} TZS</span>
                                   </div>
                                   
                                   ${!companyPays ? `
                                       ${selfPays ? `<small class="badge badge-light border border-warning text-dark mb-2">Self-Pay (Services)</small>` : ''}
                                       <div class="progress mt-2" style="height: 6px;">
                                          <div class="progress-bar bg-info" role="progressbar" style="width: ${parseFloat(booking.payment_percentage || 0)}%;" aria-valuenow="${parseFloat(booking.payment_percentage || 0)}" aria-valuemin="0" aria-valuemax="100"></div>
                                       </div>
                                       <small class="text-muted text-right d-block mt-1">${parseFloat(booking.payment_percentage || 0).toFixed(0)}% Paid</small>
                                   ` : '<small class="text-muted d-block mt-1 text-right">Payable by Company</small>'}
                                   ` : ''}
                                   
                                   ${booking.payment_status === 'pending' && !companyPays ? `
                                   <hr class="my-2">
                                   <div class="alert alert-warning py-2 mb-0 small">
                                      <i class="fa fa-exclamation-circle"></i> Payment is pending.
                                   </div>
                                   ` : ''}

                                   <div class="bg-light p-2 rounded mt-3 small">
                                      <div class="d-flex justify-content-between">
                                          <span>Status:</span>
                                          <strong>${booking.payment_status ? booking.payment_status.toUpperCase() : 'N/A'}</strong>
                                      </div>
                                      <div class="d-flex justify-content-between mt-1">
                                          <span>Method:</span>
                                          <span>${booking.payment_method || 'N/A'}</span>
                                      </div>
                                      ${booking.payment_transaction_id ? `
                                      <div class="d-flex justify-content-between mt-1 text-muted">
                                          <span>Transaction ID:</span>
                                          <span>${booking.payment_transaction_id}</span>
                                      </div>` : ''}
                                      ${booking.paid_at ? `
                                      <div class="d-flex justify-content-between mt-1 text-muted">
                                          <span>Paid On:</span>
                                          <span>${new Date(booking.paid_at).toLocaleDateString()}</span>
                                      </div>` : ''}
                                   </div>

                                </div>
                            </div>

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

