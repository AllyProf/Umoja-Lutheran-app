@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-credit-card"></i> Checkout Payment</h1>
    <p>Complete payment for your booking</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">Dashboard</a></li>
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
        <h5 style="color: #e07632; border-bottom: 2px solid #e07632; padding-bottom: 10px; margin-bottom: 15px;">Booking Information</h5>
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
            <li><strong>Pay Online:</strong> Pay securely via PayPal</li>
            <li><strong>Pay at Reception:</strong> Visit reception to pay with cash</li>
          </ul>
        </div>
        
        <div class="text-center mt-4">
          @if($totalAdditionalChargesTsh > 0)
            <a href="{{ route('payment.create', ['booking_id' => $booking->id]) }}" class="btn btn-primary btn-lg mr-3" style="min-width: 200px;">
              <i class="fa fa-paypal"></i> Pay Online (PayPal)
            </a>
            <a href="{{ route('customer.dashboard') }}" class="btn btn-secondary btn-lg" style="min-width: 200px;">
              <i class="fa fa-arrow-left"></i> Pay at Reception
            </a>
          @else
            <div class="alert alert-success">
              <i class="fa fa-check-circle"></i> <strong>No additional charges!</strong> Your booking is complete. You can proceed to checkout.
            </div>
            <a href="{{ route('customer.dashboard') }}" class="btn btn-secondary btn-lg" style="min-width: 200px;">
              <i class="fa fa-arrow-left"></i> Back to Dashboard
            </a>
          @endif
        </div>
        <div class="text-center mt-3">
          <small class="text-muted">If you choose to pay at reception, please visit the front desk with your booking reference: <strong>{{ $booking->booking_reference }}</strong></small>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-md-4">
    <div class="tile">
      <h5 class="tile-title">Payment Instructions</h5>
      <div class="tile-body">
        <div class="alert alert-warning">
          <i class="fa fa-exclamation-triangle"></i> <strong>Important:</strong>
          <ul class="mb-0 mt-2" style="padding-left: 20px;">
            <li>Payment must be completed before leaving</li>
            <li>After payment, your account will be deactivated</li>
            <li>You can pay online or at reception</li>
          </ul>
        </div>
        <div class="alert alert-success">
          <i class="fa fa-check-circle"></i> <strong>Secure Payment:</strong>
          <p class="mb-0 mt-2">PayPal payments are secure and encrypted. Your payment information is never stored on our servers.</p>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
@endsection

