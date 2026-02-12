@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-calendar"></i> Booking Calendar</h1>
    <p>View all room bookings and occupancy at a glance</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="#">Bookings</a></li>
    <li class="breadcrumb-item"><a href="#">Calendar</a></li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="tile" style="box-shadow: 0 2px 8px rgba(0,0,0,0.1); border: none;">
      <div class="tile-body" style="padding: 25px;">
        <!-- Quick Actions Bar -->
        <div class="row mb-4">
          <div class="col-md-12">
            <div style="background: linear-gradient(135deg, #940000 0%, #d66a2a 100%); padding: 20px; border-radius: 8px; color: white; margin-bottom: 20px;">
              <div class="row align-items-center">
                <div class="col-md-8">
                  <h4 style="margin: 0; color: white; font-weight: 600;">
                    <i class="fa fa-lightbulb-o"></i> Quick Actions
                  </h4>
                  <p style="margin: 8px 0 0 0; color: rgba(255,255,255,0.9); font-size: 14px;">
                    Click on any empty date to create a new booking • Click on any booking to view details
                  </p>
                </div>
                <div class="col-md-4 text-right">
                  <a href="{{ route('admin.bookings.manual.create') }}" class="btn btn-light" style="font-weight: 600;">
                    <i class="fa fa-plus-circle"></i> Create New Booking
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Legend -->
        <div class="row mb-4">
          <div class="col-md-12">
            <div style="background: #f8f9fa; border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px;">
              <h5 style="margin: 0 0 15px 0; font-weight: 600; color: #333;">
                <i class="fa fa-info-circle" style="color: #940000;"></i> Status Legend
              </h5>
              <div class="row">
                <div class="col-md-3 col-sm-6 mb-2">
                  <div style="display: flex; align-items: center;">
                    <span style="display: inline-block; width: 24px; height: 24px; background: #dc3545; border-radius: 5px; margin-right: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.2);"></span>
                    <div>
                      <strong style="color: #333; font-size: 14px;">Occupied</strong>
                      <div style="color: #666; font-size: 12px;">Checked In</div>
                    </div>
                  </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                  <div style="display: flex; align-items: center;">
                    <span style="display: inline-block; width: 24px; height: 24px; background: #28a745; border-radius: 5px; margin-right: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.2);"></span>
                    <div>
                      <strong style="color: #333; font-size: 14px;">Confirmed</strong>
                      <div style="color: #666; font-size: 12px;">Paid</div>
                    </div>
                  </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                  <div style="display: flex; align-items: center;">
                    <span style="display: inline-block; width: 24px; height: 24px; background: #ffc107; border-radius: 5px; margin-right: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.2);"></span>
                    <div>
                      <strong style="color: #333; font-size: 14px;">Pending Payment</strong>
                      <div style="color: #666; font-size: 12px;">Awaiting Payment</div>
                    </div>
                  </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                  <div style="display: flex; align-items: center;">
                    <span style="display: inline-block; width: 24px; height: 24px; background: #17a2b8; border-radius: 5px; margin-right: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.2);"></span>
                    <div>
                      <strong style="color: #333; font-size: 14px;">Partial Payment</strong>
                      <div style="color: #666; font-size: 12px;">Partially Paid</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Calendar Container -->
        <div style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
          <div id="calendar"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Booking Details Modal -->
<div class="modal fade" id="bookingDetailsModal" tabindex="-1" role="dialog" aria-labelledby="bookingDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" style="border: none; box-shadow: 0 5px 20px rgba(0,0,0,0.2); border-radius: 10px;">
      <div class="modal-header" style="background: linear-gradient(135deg, #940000 0%, #d66a2a 100%); color: white; border-radius: 10px 10px 0 0; padding: 20px;">
        <h5 class="modal-title" id="bookingDetailsModalLabel" style="font-weight: 600; font-size: 18px;">
          <i class="fa fa-calendar-check-o"></i> Booking Details
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.9; font-size: 24px;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="bookingDetailsContent" style="padding: 25px;">
        <!-- Content will be loaded dynamically -->
      </div>
      <input type="hidden" id="currentBookingId" value="">
      <div class="modal-footer" style="padding: 20px; border-top: 1px solid #e0e0e0; border-radius: 0 0 10px 10px;">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" style="font-weight: 600; padding: 10px 20px; border-radius: 5px;">Close</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js"></script>
<!-- SweetAlert2 -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
/* SweetAlert2 Custom Styling - Brand Colors */
.swal2-popup {
    border-radius: 10px !important;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}

.swal2-title {
    font-weight: 600 !important;
    color: #333 !important;
}

.swal2-confirm {
    background-color: #940000 !important;
    border-color: #940000 !important;
    font-weight: 600 !important;
    border-radius: 5px !important;
    padding: 10px 24px !important;
    transition: all 0.3s !important;
}

.swal2-confirm:hover {
    background-color: #d66a2a !important;
    border-color: #d66a2a !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

.swal2-cancel {
    background-color: #6c757d !important;
    border-color: #6c757d !important;
    font-weight: 600 !important;
    border-radius: 5px !important;
    padding: 10px 24px !important;
}

.swal2-cancel:hover {
    background-color: #5a6268 !important;
    border-color: #5a6268 !important;
}

.swal2-select {
    border-radius: 5px !important;
    border: 1px solid #ddd !important;
    padding: 8px 12px !important;
}

.swal2-select:focus {
    border-color: #940000 !important;
    box-shadow: 0 0 0 0.2rem rgba(231, 122, 58, 0.25) !important;
}
</style>
<style>
/* Custom Calendar Styling */
.fc {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}

.fc-header-toolbar {
    margin-bottom: 1.5em;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}

.fc-button {
    background: #940000 !important;
    border-color: #940000 !important;
    color: white !important;
    font-weight: 600;
    padding: 8px 15px;
    border-radius: 5px;
    transition: all 0.3s;
}

.fc-button:hover {
    background: #d66a2a !important;
    border-color: #d66a2a !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

.fc-button-active {
    background: #c55a1a !important;
    border-color: #c55a1a !important;
}

.fc-today-button {
    background: #28a745 !important;
    border-color: #28a745 !important;
}

.fc-today-button:hover {
    background: #218838 !important;
    border-color: #218838 !important;
}

.fc-daygrid-day {
    border-color: #e0e0e0;
    transition: background-color 0.2s;
}

.fc-daygrid-day:hover {
    background-color: #f8f9fa;
}

.fc-day-today {
    background-color: #fff3cd !important;
}

.fc-col-header-cell {
    background: #f8f9fa;
    font-weight: 600;
    color: #333;
    padding: 10px;
    border-color: #e0e0e0;
}

.fc-daygrid-day-number {
    padding: 8px;
    font-weight: 500;
    color: #333;
}

.fc-day-today .fc-daygrid-day-number {
    background: #940000;
    color: white;
    border-radius: 50%;
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
}

.fc-event {
    border-radius: 5px !important;
    padding: 2px 5px;
    cursor: pointer;
    transition: all 0.2s;
}

.fc-event:hover {
    transform: translateY(-1px);
    box-shadow: 0 3px 8px rgba(0,0,0,0.3) !important;
    z-index: 10;
}

.fc-popover {
    border-radius: 8px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    border: none;
}

.fc-popover-header {
    background: #940000;
    color: white;
    padding: 10px;
    border-radius: 8px 8px 0 0;
}

.fc-more-link {
    color: #940000;
    font-weight: 600;
}

.fc-more-link:hover {
    color: #d66a2a;
    text-decoration: underline;
}

/* Modal Enhancements */
.modal-content {
    border-radius: 10px;
    overflow: hidden;
}

.modal-body table {
    margin-bottom: 0;
}

.modal-body table td {
    padding: 10px;
    border-color: #e0e0e0;
}

.modal-body h6 {
    color: #940000;
    font-weight: 600;
    margin-bottom: 15px;
    padding-bottom: 8px;
    border-bottom: 2px solid #940000;
}

.badge {
    padding: 6px 12px;
    font-size: 12px;
    font-weight: 600;
    border-radius: 5px;
}

/* Mobile Responsive Styles */
@media (max-width: 767px) {
  /* Quick Actions Bar - Mobile */
  .row.align-items-center .col-md-8,
  .row.align-items-center .col-md-4 {
    flex: 0 0 100%;
    max-width: 100%;
  }
  
  .row.align-items-center .col-md-4 {
    margin-top: 15px;
    text-align: left !important;
  }
  
  .row.align-items-center .col-md-4 .btn {
    width: 100%;
  }
  
  /* Legend - Mobile */
  .col-md-3.col-sm-6 {
    flex: 0 0 100%;
    max-width: 100%;
    margin-bottom: 15px;
  }
  
  /* Calendar Container */
  .tile-body {
    padding: 15px !important;
  }
  
  /* FullCalendar Responsive */
  .fc-header-toolbar {
    flex-direction: column;
    gap: 10px;
    padding: 10px !important;
  }
  
  .fc-toolbar-chunk {
    width: 100%;
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 5px;
  }
  
  .fc-toolbar-chunk:first-child {
    order: 2; /* Move prev/next/today to middle */
  }
  
  .fc-toolbar-chunk:nth-child(2) {
    order: 1; /* Move title to top */
    width: 100%;
    margin-bottom: 10px;
  }
  
  .fc-toolbar-chunk:last-child {
    order: 3; /* Move view buttons to bottom */
    width: 100%;
    justify-content: center;
  }
  
  .fc-toolbar-title {
    font-size: 18px !important;
    text-align: center;
  }
  
  .fc-button {
    padding: 6px 12px !important;
    font-size: 13px !important;
  }
  
  .fc-button-group .fc-button {
    padding: 6px 10px !important;
  }
  
  /* Calendar Grid - Mobile */
  .fc-col-header-cell {
    padding: 8px 4px !important;
    font-size: 12px;
  }
  
  .fc-daygrid-day-number {
    padding: 4px !important;
    font-size: 13px;
  }
  
  .fc-day-today .fc-daygrid-day-number {
    width: 24px;
    height: 24px;
    font-size: 12px;
  }
  
  .fc-event {
    font-size: 10px !important;
    padding: 2px 4px !important;
    margin: 1px 0;
  }
  
  .fc-event-title {
    font-size: 10px !important;
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
  
  .modal-body table {
    font-size: 14px;
  }
  
  .modal-body table td {
    padding: 8px;
    font-size: 13px;
  }
  
  .modal-header {
    padding: 15px !important;
  }
  
  .modal-footer {
    padding: 15px !important;
  }
  
  .modal-footer .btn {
    width: 100%;
    margin-bottom: 10px;
  }
  
  .modal-footer .btn:last-child {
    margin-bottom: 0;
  }
}

/* Very Small Screens */
@media (max-width: 480px) {
  .fc-toolbar-title {
    font-size: 16px !important;
  }
  
  .fc-button {
    padding: 5px 8px !important;
    font-size: 12px !important;
  }
  
  .fc-button-group .fc-button {
    padding: 5px 8px !important;
    font-size: 11px !important;
  }
  
  .fc-col-header-cell {
    padding: 6px 2px !important;
    font-size: 11px;
  }
  
  .fc-daygrid-day-number {
    font-size: 12px;
  }
  
  .fc-event {
    font-size: 9px !important;
    padding: 1px 3px !important;
  }
  
  .fc-event-title {
    font-size: 9px !important;
  }
  
  /* Quick Actions Bar */
  .row.align-items-center h4 {
    font-size: 16px !important;
  }
  
  .row.align-items-center p {
    font-size: 13px !important;
  }
  
  /* Legend */
  .col-md-3.col-sm-6 {
    margin-bottom: 12px;
  }
  
  /* Calendar Container Padding */
  .tile-body > div:last-child {
    padding: 15px !important;
  }
}

/* Tablet */
@media (min-width: 768px) and (max-width: 991px) {
  .fc-toolbar-title {
    font-size: 20px !important;
  }
  
  .fc-button {
    padding: 7px 12px !important;
  }
  
  .modal-dialog.modal-lg {
    max-width: 90%;
  }
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    if (calendarEl) {
        // Detect mobile device
        var isMobile = window.innerWidth <= 767;
        var initialView = isMobile ? 'listWeek' : 'dayGridMonth';
        
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: initialView,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },
            firstDay: 1, // Start week on Monday
            height: 'auto',
            aspectRatio: isMobile ? 1.2 : 1.8,
            editable: false, // Disable drag and drop (removed reschedule feature)
            dayMaxEvents: true,
            moreLinkClick: 'popover',
            eventDisplay: 'block',
            eventTextColor: '#ffffff',
            eventBorderColor: 'transparent',
            eventBackgroundColor: '#940000',
            dayHeaderFormat: { weekday: 'short' },
            buttonText: {
                today: 'Today',
                month: 'Month',
                week: 'Week',
                day: 'Day',
                list: 'List'
            },
            dateClick: function(info) {
                // Click on empty date to create new booking
                var clickedDate = info.dateStr;
                var today = new Date().toISOString().split('T')[0];
                
                if (clickedDate < today) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid Date',
                        text: 'Cannot create booking for past dates.',
                        confirmButtonColor: '#940000',
                        confirmButtonText: 'OK'
                    });
                    return;
                }
                
                // Redirect to manual booking page with pre-filled dates
                window.location.href = '{{ route("admin.bookings.manual.create") }}?check_in=' + clickedDate;
            },
            events: @json($calendarEvents),
            eventClick: function(info) {
                // Show booking details in modal
                var event = info.event;
                var props = event.extendedProps;
                
                // Determine status badge
                var statusBadge = '';
                var paymentBadge = '';
                var checkInBadge = '';
                
                // Status badge
                if (props.status === 'confirmed') {
                    statusBadge = '<span class="badge badge-success">Confirmed</span>';
                } else if (props.status === 'pending') {
                    statusBadge = '<span class="badge badge-warning">Pending</span>';
                } else if (props.status === 'cancelled') {
                    statusBadge = '<span class="badge badge-danger">Cancelled</span>';
                } else if (props.status === 'completed') {
                    statusBadge = '<span class="badge badge-info">Completed</span>';
                }
                
                // Payment status badge
                if (props.payment_status === 'paid') {
                    paymentBadge = '<span class="badge badge-success">Paid</span>';
                } else if (props.payment_status === 'partial') {
                    paymentBadge = '<span class="badge badge-info">Partial Payment</span>';
                } else if (props.payment_status === 'pending') {
                    paymentBadge = '<span class="badge badge-warning">Pending Payment</span>';
                }
                
                // Check-in status badge
                if (props.check_in_status === 'checked_in') {
                    checkInBadge = '<span class="badge badge-danger">Checked In</span>';
                } else if (props.check_in_status === 'checked_out') {
                    checkInBadge = '<span class="badge badge-secondary">Checked Out</span>';
                } else {
                    checkInBadge = '<span class="badge badge-light">Pending Check-in</span>';
                }
                
                var content = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fa fa-bed"></i> Room Information</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Room Number:</strong></td>
                                    <td>${props.room_number}</td>
                                </tr>
                                <tr>
                                    <td><strong>Room Type:</strong></td>
                                    <td>${props.room_type}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fa fa-user"></i> Guest Information</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Guest Name:</strong></td>
                                    <td>${props.guest_name}</td>
                                </tr>
                                <tr>
                                    <td><strong>Booking Reference:</strong></td>
                                    <td><code>${props.booking_reference}</code></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h6><i class="fa fa-calendar"></i> Dates</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Check-in:</strong></td>
                                    <td>${event.startStr}</td>
                                </tr>
                                <tr>
                                    <td><strong>Check-out:</strong></td>
                                    <td>${event.endStr}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fa fa-info-circle"></i> Status</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Booking Status:</strong></td>
                                    <td>${statusBadge}</td>
                                </tr>
                                <tr>
                                    <td><strong>Payment Status:</strong></td>
                                    <td>${paymentBadge}</td>
                                </tr>
                                <tr>
                                    <td><strong>Check-in Status:</strong></td>
                                    <td>${checkInBadge}</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Price:</strong></td>
                                    <td><strong>$${parseFloat(props.total_price).toFixed(2)}</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                `;
                
                document.getElementById('bookingDetailsContent').innerHTML = content;
                document.getElementById('currentBookingId').value = props.booking_id;
                $('#bookingDetailsModal').modal('show');
            },
            eventContent: function(arg) {
                var props = arg.event.extendedProps;
                var title = `Room ${props.room_number} - ${props.guest_name}`;
                var guestName = props.guest_name.length > 12 ? props.guest_name.substring(0, 12) + '...' : props.guest_name;
                
                return {
                    html: '<div style="padding: 6px 8px; border-radius: 5px; font-size: 12px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; box-shadow: 0 1px 3px rgba(0,0,0,0.2); cursor: pointer; transition: all 0.2s;" title="' + title + '" onmouseover="this.style.transform=\'scale(1.02)\'; this.style.boxShadow=\'0 2px 5px rgba(0,0,0,0.3)\';" onmouseout="this.style.transform=\'scale(1)\'; this.style.boxShadow=\'0 1px 3px rgba(0,0,0,0.2)\';">' +
                          '<i class="fa fa-bed" style="margin-right: 5px; font-size: 11px;"></i>' +
                          '<strong>R' + props.room_number + '</strong> - ' + guestName +
                          '</div>'
                };
            },
            eventDisplay: 'block',
            height: 'auto',
            dayMaxEvents: true,
            moreLinkClick: 'popover',
            eventMouseEnter: function(info) {
                // Enhanced tooltip on hover
                var props = info.event.extendedProps;
                var statusText = props.check_in_status === 'checked_in' ? 'Occupied' : 
                               props.payment_status === 'paid' ? 'Confirmed' :
                               props.payment_status === 'partial' ? 'Partial Payment' : 'Pending Payment';
                
                var tooltip = `
                    <div style="text-align: left; padding: 5px;">
                        <strong style="color: #940000; font-size: 14px;">Room ${props.room_number} (${props.room_type})</strong><br>
                        <i class="fa fa-user" style="margin-right: 5px;"></i><strong>Guest:</strong> ${props.guest_name}<br>
                        <i class="fa fa-calendar-check-o" style="margin-right: 5px;"></i><strong>Check-in:</strong> ${info.event.startStr}<br>
                        <i class="fa fa-calendar-times-o" style="margin-right: 5px;"></i><strong>Check-out:</strong> ${info.event.endStr}<br>
                        <i class="fa fa-info-circle" style="margin-right: 5px;"></i><strong>Status:</strong> ${statusText}<br>
                        <i class="fa fa-dollar" style="margin-right: 5px;"></i><strong>Total:</strong> $${parseFloat(props.total_price).toFixed(2)}
                    </div>
                `;
                $(info.el).tooltip({
                    title: tooltip,
                    html: true,
                    placement: 'top',
                    container: 'body',
                    trigger: 'hover'
                });
                $(info.el).tooltip('show');
            },
            eventMouseLeave: function(info) {
                $(info.el).tooltip('hide');
            }
        });
        calendar.render();
        
        // Handle window resize for responsive calendar
        var resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                calendar.updateSize();
            }, 250);
        });
    }
});
</script>
@endsection

