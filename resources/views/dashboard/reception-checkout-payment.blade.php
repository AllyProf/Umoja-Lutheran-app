@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-credit-card"></i> Checkout Payment</h1>
    <p>Process payment for checked out guest</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('reception.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('reception.reservations.check-out') }}">Check Out</a></li>
    <li class="breadcrumb-item"><a href="#">Payment</a></li>
  </ul>
</div>

<div class="row">
  <div class="col-md-8">
    <div class="tile">
      <div class="tile-title-w-btn mb-3">
        <h3 class="title">Payment for Booking: {{ $booking->booking_reference }}</h3>
      </div>
      
      <!-- Guest Information -->
      <div class="mb-4">
        <h5 style="color: #e07632; border-bottom: 2px solid #e07632; padding-bottom: 10px; margin-bottom: 15px;">Guest Information</h5>
        <table class="table table-borderless">
          <tr>
            <td><strong>Name:</strong></td>
            <td>{{ $booking->guest_name }}</td>
          </tr>
          <tr>
            <td><strong>Email:</strong></td>
            <td>{{ $booking->guest_email }}</td>
          </tr>
          <tr>
            <td><strong>Phone:</strong></td>
            <td>{{ $booking->guest_phone }}</td>
          </tr>
          <tr>
            <td><strong>Room:</strong></td>
            <td>{{ $booking->room->room_number }} ({{ $booking->room->room_type }})</td>
          </tr>
          <tr>
            <td><strong>Check-out Date:</strong></td>
            <td>{{ $booking->check_out->format('M d, Y') }}</td>
          </tr>
        </table>
      </div>

      <!-- Additional Charges Summary -->
      <div class="mb-4">
        <div class="alert alert-info">
          <i class="fa fa-info-circle"></i> <strong>Note:</strong> Room booking charges were already paid via PayPal during booking. This payment is for additional charges only.
        </div>
        <h5 style="color: #e07632; border-bottom: 2px solid #e07632; padding-bottom: 10px; margin-bottom: 15px;">Additional Charges Summary</h5>
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr style="background-color: #f8f9fa;">
                <th>Description</th>
                <th class="text-right">Amount (TZS)</th>
              </tr>
            </thead>
            <tbody>
              @if($transportationChargesTsh > 0)
              <tr>
                <td>Transportation (Airport Pickup)</td>
                <td class="text-right">{{ number_format($transportationChargesTsh, 2) }}</td>
              </tr>
              @endif
              @if($otherServiceChargesTsh > 0)
              <tr>
                <td>Service Charges (Room Service, etc.)</td>
                <td class="text-right">{{ number_format($otherServiceChargesTsh, 2) }}</td>
              </tr>
              @endif
              @if($extensionCostTsh > 0)
              <tr>
                <td>Extension Charges ({{ $extensionNights }} night(s))</td>
                <td class="text-right">{{ number_format($extensionCostTsh, 2) }}</td>
              </tr>
              @endif
              @if($totalAdditionalChargesTsh == 0)
              <tr>
                <td colspan="2" class="text-center text-muted">
                  <i class="fa fa-check-circle"></i> No additional charges
                </td>
              </tr>
              @else
              <tr style="background-color: #f8f9fa; font-size: 18px;">
                <td><strong>TOTAL ADDITIONAL CHARGES</strong></td>
                <td class="text-right"><strong style="color: #e07632;">{{ number_format($totalAdditionalChargesTsh, 2) }} TZS</strong></td>
              </tr>
              @endif
            </tbody>
          </table>
        </div>
      </div>

      <!-- Payment Method Selection -->
      <div class="mb-4">
        <h5 style="color: #e07632; border-bottom: 2px solid #e07632; padding-bottom: 10px; margin-bottom: 15px;">Payment Method</h5>
        <div class="alert alert-info">
          <i class="fa fa-info-circle"></i> <strong>Payment Options:</strong>
          <ul class="mb-0 mt-2" style="padding-left: 20px;">
            <li><strong>Cash Payment:</strong> Guest pays cash at reception - mark as paid below</li>
            <li><strong>Online Payment:</strong> Guest can pay online via PayPal from their dashboard</li>
          </ul>
        </div>
        <form id="paymentForm">
          <div class="form-group">
            <div class="custom-control custom-radio">
              <input type="radio" id="payment_cash" name="payment_method" value="cash" class="custom-control-input" checked>
              <label class="custom-control-label" for="payment_cash">
                <strong>Mark as Paid (Cash)</strong>
                <br><small class="text-muted">Guest has paid cash at reception</small>
              </label>
            </div>
          </div>
          <div id="paymentAlert"></div>
          <div class="mt-3">
            @if($totalAdditionalChargesTsh > 0)
              <button type="button" onclick="processPayment()" class="btn btn-success btn-lg">
                <i class="fa fa-check"></i> Mark as Paid (Cash)
              </button>
            @else
              <div class="alert alert-success">
                <i class="fa fa-check-circle"></i> <strong>No additional charges!</strong> Guest can proceed to checkout.
              </div>
            @endif
            <a href="{{ route('reception.reservations.check-out') }}" class="btn btn-secondary btn-lg ml-2">
              <i class="fa fa-arrow-left"></i> Back
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
  
  <div class="col-md-4">
    <div class="tile">
      <h5 class="tile-title">Payment Instructions</h5>
      <div class="tile-body">
        <div class="alert alert-info">
          <i class="fa fa-info-circle"></i> <strong>Important:</strong>
          <ul class="mb-0 mt-2" style="padding-left: 20px;">
            <li>After payment, guest account will be deactivated</li>
            <li>For cash payment, mark as paid immediately</li>
            <li>For PayPal, guest will be redirected to PayPal</li>
            <li>Room will remain as "Needs Cleaning" until cleaned</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<script>
function processPayment() {
    const form = document.getElementById('paymentForm');
    const formData = new FormData(form);
    const paymentMethod = formData.get('payment_method');
    const alertDiv = document.getElementById('paymentAlert');
    const submitBtn = event.target;
    const originalText = submitBtn.innerHTML;
    
    if (alertDiv) {
        alertDiv.innerHTML = '';
    }
    
    if (!paymentMethod) {
        if (alertDiv) {
            alertDiv.innerHTML = '<div class="alert alert-danger">Please select a payment method.</div>';
        }
        return;
    }
    
    const confirmText = 'Mark this payment as paid with cash? Guest account will be deactivated after payment.';
    
    swal({
        title: "Confirm Payment",
        text: confirmText,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, Process Payment!",
        cancelButtonText: "Cancel",
        closeOnConfirm: false,
        showLoaderOnConfirm: true
    }, function(isConfirm) {
        if (isConfirm) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Processing...';
            
            fetch('{{ route("reception.checkout-payment.process", $booking) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    payment_method: paymentMethod
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.payment_method === 'paypal') {
                        // Redirect to PayPal
                        window.location.href = data.redirect;
                    } else {
                        // Cash payment - show success and redirect
                        swal({
                            title: "Payment Processed!",
                            text: data.message,
                            type: "success",
                            confirmButtonColor: "#28a745"
                        }, function() {
                            window.location.href = data.redirect;
                        });
                    }
                } else {
                    swal({
                        title: "Error!",
                        text: data.message || "An error occurred. Please try again.",
                        type: "error",
                        confirmButtonColor: "#d33"
                    });
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
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
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        }
    });
}
</script>
@endsection

