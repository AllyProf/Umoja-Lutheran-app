@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-bolt"></i> Quick Actions</h1>
    <p>Quick access to common actions</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="#">Quick Actions</a></li>
  </ul>
</div>

@if($activeBookings->count() > 0)
<!-- Quick Actions Grid -->
<div class="row mb-4">
  @foreach($activeBookings as $booking)
  <div class="col-md-6 mb-4">
    <div class="tile" style="border-left: 4px solid #940000;">
      <h4 class="tile-title">
        <i class="fa fa-calendar-check-o"></i> Booking: {{ $booking->booking_reference }}
      </h4>
      <div class="tile-body">
        <p class="mb-2">
          <strong>Room:</strong> {{ $booking->room->room_type ?? 'N/A' }} ({{ $booking->room->room_number ?? 'N/A' }})<br>
          <strong>Dates:</strong> {{ $booking->check_in->format('M d, Y') }} - {{ $booking->check_out->format('M d, Y') }}
        </p>
        
        <div class="btn-group-vertical" style="width: 100%; gap: 8px;">
          @if($booking->status === 'confirmed' && $booking->payment_status === 'paid' && $booking->check_in_status === 'pending')
            @php
              $checkInDate = \Carbon\Carbon::parse($booking->check_in);
              $today = \Carbon\Carbon::today();
              $canCheckIn = $checkInDate <= $today;
            @endphp
            @if($canCheckIn)
              <button class="btn btn-success" onclick="checkInBooking({{ $booking->id }}, '{{ $booking->booking_reference }}')">
                <i class="fa fa-sign-in"></i> Check In Now
              </button>
            @else
              <button class="btn btn-outline-secondary" disabled>
                <i class="fa fa-clock-o"></i> Check-in available on {{ $booking->check_in->format('M d, Y') }}
              </button>
            @endif
          @endif
          
          <button class="btn btn-primary" onclick="requestService({{ $booking->id }})">
            <i class="fa fa-plus-circle"></i> Request Service
          </button>
          
          <a href="{{ route('customer.bookings.identity-card', $booking) }}" target="_blank" class="btn btn-info">
            <i class="fa fa-id-card"></i> Download ID Card
          </a>
          
          @if($booking->payment_status === 'paid')
          <a href="{{ route('customer.payment.receipt.download', $booking) }}?download=1" target="_blank" class="btn btn-secondary">
            <i class="fa fa-download"></i> Download Receipt
          </a>
          @endif
          
          <a href="{{ route('check-in.index') }}?ref={{ $booking->booking_reference }}" class="btn btn-outline-info">
            <i class="fa fa-eye"></i> View Full Details
          </a>
          
          @if($booking->check_in_status === 'checked_in')
          <a href="{{ route('customer.bookings.checkout-bill', $booking) }}" class="btn btn-warning">
            <i class="fa fa-file-text"></i> View Bill
          </a>
          @endif
        </div>
      </div>
    </div>
  </div>
  @endforeach
</div>

<!-- Service Request Modal -->
<div class="modal fade" id="serviceRequestModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-plus-circle"></i> Request a Service</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="serviceRequestForm">
          <input type="hidden" id="service_booking_id" name="booking_id">
          <div class="form-group">
            <label for="service_id">Select Service *</label>
            <select class="form-control" id="service_id" name="service_id" required>
              <option value="">Loading services...</option>
            </select>
          </div>
          <div class="form-group">
            <label for="service_quantity">Quantity <small class="text-muted">(Optional)</small></label>
            <input type="number" class="form-control" id="service_quantity" name="quantity" min="1" value="1">
            <small class="form-text text-muted" id="service_unit">Leave as 1 if requesting a single service</small>
          </div>
          <div class="form-group">
            <label for="guest_request">Additional Notes (Optional)</label>
            <textarea class="form-control" id="guest_request" name="guest_request" rows="3" placeholder="Any special requests or notes..."></textarea>
          </div>
          <div class="alert alert-info" id="service_price_info" style="display: none;">
            <strong>Estimated Price:</strong> <span id="service_total_price">0</span> TZS
          </div>
          <div id="serviceRequestAlert"></div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="submitServiceRequest()">
          <i class="fa fa-paper-plane"></i> Submit Request
        </button>
      </div>
    </div>
  </div>
</div>
@else
<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-body text-center" style="padding: 50px;">
        <i class="fa fa-bolt fa-5x text-muted mb-3"></i>
        <h3>No Active Bookings</h3>
        <p class="text-muted">You need to have an active booking to access quick actions.</p>
        <a href="{{ route('booking.index') }}" class="btn btn-primary">
          <i class="fa fa-plus"></i> Book a Room
        </a>
      </div>
    </div>
  </div>
</div>
@endif
@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<script>
let availableServices = [];

// Load available services
function loadServices() {
    fetch('{{ route("customer.services.available") }}', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            availableServices = data.services;
            const serviceSelect = document.getElementById('service_id');
            serviceSelect.innerHTML = '<option value="">Select a service...</option>';
            
            data.services.forEach(service => {
                const option = document.createElement('option');
                option.value = service.id;
                option.textContent = service.name + ' - ' + service.price_tsh.toLocaleString() + ' TZS';
                option.dataset.price = service.price_tsh;
                option.dataset.unit = service.unit;
                serviceSelect.appendChild(option);
            });
        }
    })
    .catch(error => {
        console.error('Error loading services:', error);
    });
}

// Calculate service price
function calculateServicePrice() {
    const serviceId = document.getElementById('service_id').value;
    const quantity = parseInt(document.getElementById('service_quantity').value) || 1;
    const serviceSelect = document.getElementById('service_id');
    const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
    
    if (serviceId && selectedOption.dataset.price) {
        const price = parseFloat(selectedOption.dataset.price);
        const total = price * quantity;
        document.getElementById('service_total_price').textContent = total.toLocaleString();
        document.getElementById('service_price_info').style.display = 'block';
        document.getElementById('service_unit').textContent = 'Unit: ' + (selectedOption.dataset.unit || 'per item');
    } else {
        document.getElementById('service_price_info').style.display = 'none';
    }
}

// Request service modal
function requestService(bookingId) {
    document.getElementById('service_booking_id').value = bookingId;
    document.getElementById('service_id').value = '';
    document.getElementById('service_quantity').value = 1;
    document.getElementById('guest_request').value = '';
    document.getElementById('service_price_info').style.display = 'none';
    document.getElementById('serviceRequestAlert').innerHTML = '';
    $('#serviceRequestModal').modal('show');
}

// Submit service request
function submitServiceRequest() {
    const bookingId = document.getElementById('service_booking_id').value;
    const serviceId = document.getElementById('service_id').value;
    const quantity = document.getElementById('service_quantity').value;
    const guestRequest = document.getElementById('guest_request').value;
    const alertContainer = document.getElementById('serviceRequestAlert');
    
    if (!serviceId) {
        alertContainer.innerHTML = '<div class="alert alert-danger">Please select a service.</div>';
        return;
    }
    
    // Default quantity to 1 if not provided
    const finalQuantity = quantity || 1;
    
    alertContainer.innerHTML = '<div class="alert alert-info">Submitting request...</div>';
    
    fetch('{{ route("customer.services.request") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            booking_id: bookingId,
            service_id: serviceId,
            quantity: parseInt(finalQuantity),
            guest_request: guestRequest
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            swal({
                title: "Success!",
                text: data.message,
                type: "success",
                confirmButtonColor: "#28a745"
            }, function() {
                $('#serviceRequestModal').modal('hide');
                location.reload();
            });
        } else {
            alertContainer.innerHTML = '<div class="alert alert-danger">' + (data.message || 'Failed to submit request.') + '</div>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alertContainer.innerHTML = '<div class="alert alert-danger">An error occurred. Please try again.</div>';
    });
}

function checkInBooking(bookingId, bookingReference) {
    swal({
        title: "Check In?",
        text: "Are you sure you want to check in to this booking?",
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
            fetch('{{ url("/check-in") }}/' + bookingId, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    booking_reference: bookingReference,
                    email: '{{ $user->email ?? "" }}'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    swal({
                        title: "Success!",
                        text: data.message || "Check-in successful!",
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

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    loadServices();
    
    const serviceId = document.getElementById('service_id');
    const serviceQuantity = document.getElementById('service_quantity');
    
    if (serviceId) {
        serviceId.addEventListener('change', calculateServicePrice);
    }
    if (serviceQuantity) {
        serviceQuantity.addEventListener('input', calculateServicePrice);
    }
});
</script>
@endsection




