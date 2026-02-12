@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-calendar"></i> Booking Calendar</h1>
    <p>View your bookings on a calendar</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="#">Calendar</a></li>
  </ul>
</div>

<!-- Calendar View -->
<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-calendar-check-o"></i> My Bookings Calendar</h3>
      <div class="tile-body">
        @if($bookings->count() > 0)
        <div id="calendar"></div>
        
        <!-- Legend -->
        <div class="mt-3" style="padding: 15px; background-color: #f8f9fa; border-radius: 5px;">
          <h6><strong>Legend:</strong></h6>
          <div class="row">
            <div class="col-md-3 col-6 mb-2 mb-md-0">
              <span class="badge badge-success" style="padding: 8px 12px;">Confirmed</span>
            </div>
            <div class="col-md-3 col-6 mb-2 mb-md-0">
              <span class="badge badge-warning" style="padding: 8px 12px;">Pending</span>
            </div>
            <div class="col-md-3 col-6 mb-2 mb-md-0">
              <span class="badge badge-info" style="padding: 8px 12px;">Completed</span>
            </div>
            <div class="col-md-3 col-6 mb-2 mb-md-0">
              <span class="badge badge-danger" style="padding: 8px 12px;">Cancelled</span>
            </div>
          </div>
        </div>
        
        <!-- Bookings List -->
        <div class="mt-4">
          <h5><strong>Upcoming Bookings</strong></h5>
          
          @if($bookings->count() > 0)
          <!-- Mobile Card View -->
          <div class="mobile-bookings-cards d-md-none">
            @foreach($bookings as $booking)
            <div class="booking-card-mobile mb-3" style="border: 1px solid #ddd; border-radius: 8px; padding: 15px; background: white;">
              <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                  <strong style="font-size: 16px; color: #e07632;">{{ $booking->booking_reference }}</strong>
                  <br><small class="text-muted">Room: {{ $booking->room->room_number ?? 'N/A' }}</small>
                </div>
                <div class="text-right">
                  @if($booking->status === 'confirmed')
                    <span class="badge badge-success">Confirmed</span>
                  @elseif($booking->status === 'completed')
                    <span class="badge badge-info">Completed</span>
                  @else
                    <span class="badge badge-warning">{{ ucfirst($booking->status) }}</span>
                  @endif
                </div>
              </div>
              
              <div class="row mb-2">
                <div class="col-6">
                  <small class="text-muted">Room Type</small><br>
                  <span class="badge badge-primary">{{ $booking->room->room_type ?? 'N/A' }}</span>
                </div>
                <div class="col-6">
                  <small class="text-muted">Nights</small><br>
                  <strong>{{ $booking->check_in->diffInDays($booking->check_out) }} nights</strong>
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
                </div>
              </div>
              
              <div class="mb-2">
                <small class="text-muted">Payment Status</small><br>
                @if($booking->is_corporate_booking && $booking->payment_responsibility == 'company')
                    <span class="badge badge-info">Company Billed</span>
                    <br><small class="text-muted">Paid by {{ $booking->company->name ?? 'Company' }}</small>
                @else
                    @if($booking->is_corporate_booking && $booking->payment_responsibility == 'self')
                        <span class="badge badge-light border border-warning text-dark mb-1" style="font-size: 10px;">Self-Pay</span><br>
                    @endif

                    @if($booking->payment_status === 'paid')
                      <span class="badge badge-success">Paid</span>
                    @elseif($booking->payment_status === 'partial')
                      <span class="badge badge-info">Partial</span>
                      @if($booking->amount_paid)
                        <br><small>${{ number_format($booking->amount_paid, 2) }} of ${{ number_format($booking->total_price, 2) }}</small>
                      @endif
                    @elseif($booking->payment_status === 'pending')
                      <span class="badge badge-warning">Pending</span>
                    @else
                      <span class="badge badge-secondary">{{ ucfirst($booking->payment_status ?? 'N/A') }}</span>
                    @endif
                @endif
              </div>
              
              <div class="mt-3 pt-3" style="border-top: 1px solid #eee;">
                <button onclick="viewBookingDetails({{ $booking->id }})" class="btn btn-sm btn-info btn-block mb-2" style="width: 100%;">
                  <i class="fa fa-eye"></i> View Details
                </button>
                @if(in_array($booking->payment_status, ['paid', 'partial']) || $booking->status == 'confirmed')
                <a href="{{ route('customer.payment.receipt.download', $booking) }}" class="btn btn-sm btn-success btn-block" target="_blank" style="width: 100%;">
                  <i class="fa fa-download"></i> Download Receipt
                </a>
                @endif
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
                  <th>Nights</th>
                  <th>Status</th>
                  <th>Payment Status</th>
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
                    @if($booking->status === 'confirmed')
                      <span class="badge badge-success">Confirmed</span>
                    @elseif($booking->status === 'completed')
                      <span class="badge badge-info">Completed</span>
                    @else
                      <span class="badge badge-warning">{{ ucfirst($booking->status) }}</span>
                    @endif
                  </td>
                  <td>
                    @if($booking->is_corporate_booking && $booking->payment_responsibility == 'company')
                        <span class="badge badge-info">Company Billed</span><br>
                        <small class="text-muted">{{ $booking->company->name ?? 'Company' }} Account</small>
                    @else
                        @if($booking->is_corporate_booking && $booking->payment_responsibility == 'self')
                            <span class="badge badge-light border border-warning text-dark mb-1" style="font-size: 10px;">Self-Pay</span><br>
                        @endif

                        @if($booking->payment_status === 'paid')
                          <span class="badge badge-success">Paid</span>
                        @elseif($booking->payment_status === 'partial')
                          <span class="badge badge-info">Partial</span>
                          @if($booking->amount_paid)
                            <br><small>${{ number_format($booking->amount_paid, 2) }} of ${{ number_format($booking->total_price, 2) }}</small>
                          @endif
                        @elseif($booking->payment_status === 'pending')
                          <span class="badge badge-warning">Pending</span>
                        @else
                          <span class="badge badge-secondary">{{ ucfirst($booking->payment_status ?? 'N/A') }}</span>
                        @endif
                    @endif
                  </td>
                  <td>
                    <button onclick="viewBookingDetails({{ $booking->id }})" class="btn btn-sm btn-info" title="View Details">
                      <i class="fa fa-eye"></i> View
                    </button>
                    @if(in_array($booking->payment_status, ['paid', 'partial']) || $booking->status == 'confirmed')
                    <a href="{{ route('customer.payment.receipt.download', $booking) }}" class="btn btn-sm btn-success mt-1" target="_blank" title="Download Receipt">
                      <i class="fa fa-download"></i>
                    </a>
                    @endif
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          @else
          <div class="text-center" style="padding: 30px;">
            <i class="fa fa-calendar-times-o fa-3x text-muted mb-3"></i>
            <p class="text-muted">No upcoming bookings. All your bookings have been completed.</p>
          </div>
          @endif
        </div>
        @else
        <div class="text-center" style="padding: 50px;">
          <i class="fa fa-calendar-times-o fa-5x text-muted mb-3"></i>
          <h3>No Bookings</h3>
          <p class="text-muted">You don't have any bookings yet. Book a room to see it on the calendar.</p>
          <a href="{{ route('booking.index') }}" class="btn btn-primary">
            <i class="fa fa-plus"></i> Book a Room
          </a>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

@section('styles')
<style>
  /* Calendar Mobile Responsive */
  @media (max-width: 768px) {
    #calendar {
      font-size: 12px;
    }
    
    .fc-toolbar {
      flex-direction: column;
      gap: 10px;
    }
    
    .fc-toolbar-title {
      font-size: 1.2em !important;
    }
    
    .fc-button {
      font-size: 0.85em;
      padding: 0.3em 0.6em;
    }
    
    .fc-daygrid-day {
      min-height: 60px;
    }
    
    .fc-daygrid-day-number {
      font-size: 0.9em;
      padding: 4px;
    }
    
    .fc-event-title {
      font-size: 0.75em;
      padding: 2px 4px;
    }
    
    .fc-col-header-cell {
      font-size: 0.8em;
      padding: 5px 2px;
    }
    
    /* Mobile Bookings Cards */
    .mobile-bookings-cards {
      display: block;
    }
    
    .booking-card-mobile {
      transition: all 0.3s ease;
    }
    
    .booking-card-mobile:hover {
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .booking-card-mobile .btn {
      font-size: 12px;
      padding: 8px 12px;
    }
    
    .booking-card-mobile .badge {
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
</style>
@endsection

@section('scripts')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    if (calendarEl) {
        // Detect mobile screen size
        var isMobile = window.innerWidth <= 768;
        
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: isMobile ? 'dayGridMonth' : 'dayGridMonth',
            headerToolbar: {
                left: isMobile ? 'prev,next' : 'prev,next today',
                center: 'title',
                right: isMobile ? '' : 'dayGridMonth,listWeek'
            },
            height: isMobile ? 'auto' : 'auto',
            events: @json($calendarEvents),
            eventClick: function(info) {
                // Prevent navigation - show booking details modal instead
                info.jsEvent.preventDefault();
                var bookingId = info.event.id;
                if (bookingId) {
                    viewBookingDetails(bookingId);
                }
            },
            eventContent: function(arg) {
                var statusColor = {
                    'confirmed': '#28a745',
                    'pending': '#ffc107',
                    'completed': '#17a2b8',
                    'cancelled': '#dc3545'
                };
                var isMobile = window.innerWidth <= 768;
                var eventText = isMobile ? 
                    arg.event.title + ' - ' + arg.event.extendedProps.room_number :
                    arg.event.title + ' - ' + arg.event.extendedProps.room_number;
                
                return {
                    html: '<div style="background-color: ' + (statusColor[arg.event.extendedProps.status] || '#6c757d') + '; color: white; padding: ' + (isMobile ? '3px' : '5px') + '; border-radius: 3px; font-size: ' + (isMobile ? '10px' : '12px') + '; line-height: 1.2;">' +
                          eventText +
                          '</div>'
                };
            },
            windowResize: function() {
                var isMobile = window.innerWidth <= 768;
                calendar.setOption('headerToolbar', {
                    left: isMobile ? 'prev,next' : 'prev,next today',
                    center: 'title',
                    right: isMobile ? '' : 'dayGridMonth,listWeek'
                });
            }
        });
        calendar.render();
    }
});
</script>

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
                                      <span class="font-weight-bold">$${parseFloat(room.price_per_night || 0).toFixed(2)}</span>
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
                                      <span class="font-weight-bold h5 mb-0" style="color: #e07632;">$${parseFloat(booking.total_price || 0).toFixed(2)}</span>
                                   </div>
                                   <div class="text-right text-muted small mb-3">
                                      ≈ ${(parseFloat(booking.total_price || 0) * exchangeRate).toLocaleString()} TZS
                                   </div>

                                   <div class="d-flex justify-content-between mb-2 small">
                                      <span class="text-muted">Amount Paid:</span>
                                      <span class="text-success font-weight-bold">-$${parseFloat(booking.amount_paid || 0).toFixed(2)}</span>
                                   </div>
                                   
                                   ${booking.payment_status === 'partial' ? `
                                   <hr class="my-2">
                                   <div class="d-flex justify-content-between align-items-end">
                                      <span class="font-weight-bold">Remaining:</span>
                                      <span class="h6 mb-0 text-danger">$${parseFloat((booking.total_price || 0) - (booking.amount_paid || 0)).toFixed(2)}</span>
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





