@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-file-text"></i> Checkout Bill</h1>
    <p>Booking Reference: {{ $booking->booking_reference }}</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="#">Checkout Bill</a></li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-body">
        <!-- Hotel Header -->
        <div class="text-center mb-4" style="border-bottom: 3px solid #e07632; padding-bottom: 20px;">
          <h2 style="color: #e07632; margin-bottom: 5px;">Umoj Lutheran Hostel</h2>
          <p style="color: #666; margin: 0;">Checkout Bill</p>
        </div>

        <!-- Guest Information -->
        <div class="row mb-4">
          <div class="col-md-6">
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
                <td><strong>Booking Reference:</strong></td>
                <td><strong>{{ $booking->booking_reference }}</strong></td>
              </tr>
              @if(($isStaffViewingCorporate ?? false) || ($isGuestViewingCorporate ?? false))
              <tr>
                <td><strong>Booking Type:</strong></td>
                <td><span class="badge badge-info"><i class="fa fa-building"></i> Corporate Booking</span></td>
              </tr>
              @if($booking->company)
              <tr>
                <td><strong>Company:</strong></td>
                <td><strong>{{ $booking->company->name }}</strong></td>
              </tr>
              @endif
              @endif
            </table>
          </div>
          <div class="col-md-6">
            <h5 style="color: #e07632; border-bottom: 2px solid #e07632; padding-bottom: 10px; margin-bottom: 15px;">Booking Details</h5>
            <table class="table table-borderless">
              <tr>
                <td><strong>Room:</strong></td>
                <td>{{ $booking->room->room_number }} ({{ $booking->room->room_type }})</td>
              </tr>
              <tr>
                <td><strong>Check-in:</strong></td>
                <td>{{ $booking->check_in->format('F d, Y') }}</td>
              </tr>
              <tr>
                <td><strong>Check-out:</strong></td>
                <td>
                  {{ $booking->check_out->format('F d, Y') }}
                  @if($booking->extension_status === 'approved' && $booking->original_check_out)
                    <br><small class="text-success">(Extended from {{ \Carbon\Carbon::parse($booking->original_check_out)->format('M d, Y') }})</small>
                  @endif
                </td>
              </tr>
              <tr>
                <td><strong>Nights:</strong></td>
                <td>
                  {{ $booking->check_in->diffInDays($booking->check_out) }} night(s)
                  @if($extensionNights > 0)
                    <br><small class="text-muted">(Original: {{ $originalNights }}, Extension: {{ $extensionNights }})</small>
                  @endif
                </td>
              </tr>
            </table>
          </div>
        </div>

        @php
          $isCorporateCompanyPaid = ($isGuestWithCompanyPaidServices ?? false) || ($isStaffViewingCompanyPaid ?? false);
          $isCorporateSelfPaid = ($isGuestWithSelfPaidServices ?? false) || ($isStaffViewingSelfPaid ?? false);
        @endphp
        @if($isCorporateCompanyPaid)
        <!-- Company Booking Information -->
        <div class="mb-4">
          <div class="alert alert-info" style="background-color: #e7f3ff; border-left: 4px solid #17a2b8; padding: 20px;">
            <h5 style="color: #17a2b8; margin-bottom: 15px;">
              <i class="fa fa-building"></i> Company Booking: {{ $booking->company->name ?? 'N/A' }}
            </h5>
            <div style="font-size: 14px; line-height: 1.8;">
              <div style="margin-bottom: 10px;">
                <i class="fa fa-check-circle" style="color: #28a745;"></i> <strong>Room Charges:</strong> Paid by Company
              </div>
              <div style="margin-bottom: 10px;">
                <i class="fa fa-check-circle" style="color: #28a745;"></i> <strong>Service Charges:</strong> Paid by Company
              </div>
              <div style="background-color: #d4edda; padding: 15px; border-radius: 4px; margin-top: 15px; text-align: center;">
                <div style="font-size: 16px; font-weight: bold; color: #28a745;">
                  <i class="fa fa-check-circle"></i> All charges for this booking are paid by the company.
                </div>
                <div style="font-size: 13px; color: #666; margin-top: 8px;">
                  @if($isGuestViewingCorporate ?? false)
                    You have no outstanding balance. Enjoy your stay!
                  @else
                    Guest has no outstanding balance.
                  @endif
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Bill Breakdown (For Reference) -->
        @php
          // Calculate room charges for display (even though paid by company)
          $originalCheckOut = $booking->original_check_out ? \Carbon\Carbon::parse($booking->original_check_out) : \Carbon\Carbon::parse($booking->check_out);
          $displayOriginalNights = $booking->check_in->diffInDays($originalCheckOut);
          $displayRoomPriceUsd = $booking->room ? ($booking->room->price_per_night * $displayOriginalNights) : 0;
          $displayRoomPriceTsh = $displayRoomPriceUsd * $exchangeRate;
          $displayExtensionUsd = 0;
          $displayExtensionTsh = 0;
          $displayExtensionNights = 0;
          if ($booking->extension_status === 'approved' && $booking->original_check_out && $booking->extension_requested_to) {
            $originalCheckOutDate = \Carbon\Carbon::parse($booking->original_check_out);
            $requestedCheckOut = \Carbon\Carbon::parse($booking->extension_requested_to);
            $displayExtensionNights = $originalCheckOutDate->diffInDays($requestedCheckOut);
            if ($displayExtensionNights > 0 && $booking->room) {
              $displayExtensionUsd = $booking->room->price_per_night * $displayExtensionNights;
              $displayExtensionTsh = $displayExtensionUsd * $exchangeRate;
            }
          }
          // Get actual service charges from service requests (for display purposes)
          $displayServiceRequests = $serviceRequests->whereIn('status', ['approved', 'completed']);
          $displayTotalServiceTsh = $displayServiceRequests->sum('total_price_tsh');
          $displayTotalBillTsh = $displayRoomPriceTsh + $displayExtensionTsh + $displayTotalServiceTsh;
          $displayTotalBillUsd = $displayTotalBillTsh / $exchangeRate;
        @endphp
        <div class="mb-4">
          <h5 style="color: #e07632; border-bottom: 2px solid #e07632; padding-bottom: 10px; margin-bottom: 15px;">Bill Breakdown (Paid by Company)</h5>
          <div class="row">
            <div class="col-md-8 offset-md-4">
              <div style="background-color: #f8f9fa; padding: 20px; border: 2px solid #e07632;">
                <table class="table table-borderless mb-0">
                  <tr>
                    <td><strong>Room Charges ({{ $displayOriginalNights }} night(s)):</strong></td>
                    <td class="text-right">
                      <strong>{{ number_format($displayRoomPriceTsh, 2) }} TZS</strong>
                      <br><small class="text-success"><i class="fa fa-check-circle"></i> Paid by Company</small>
                    </td>
                  </tr>
                  @if($displayExtensionTsh > 0)
                  <tr>
                    <td><strong>Extension Charges ({{ $displayExtensionNights }} night(s)):</strong></td>
                    <td class="text-right">
                      <strong>{{ number_format($displayExtensionTsh, 2) }} TZS</strong>
                      <br><small class="text-success"><i class="fa fa-check-circle"></i> Paid by Company</small>
                    </td>
                  </tr>
                  @endif
                  @if($displayTotalServiceTsh > 0)
                  <tr>
                    <td><strong>Service Charges:</strong></td>
                    <td class="text-right">
                      <strong>{{ number_format($displayTotalServiceTsh, 2) }} TZS</strong>
                      <br><small class="text-success"><i class="fa fa-check-circle"></i> Paid by Company</small>
                    </td>
                  </tr>
                  @endif
                  <tr style="border-top: 2px solid #e07632;">
                    <td><strong>Total Bill:</strong></td>
                    <td class="text-right">
                      <strong>{{ number_format($displayTotalBillTsh, 2) }} TZS</strong>
                      <br><small class="text-success"><i class="fa fa-check-circle"></i> Paid by {{ $booking->company->name ?? 'Company' }}</small>
                    </td>
                  </tr>
                </table>
              </div>
            </div>
          </div>
        </div>
        @elseif($isCorporateSelfPaid)
        <!-- Self-Paid Corporate Booking - Bill Breakdown -->
        @php
          // Calculate room charges (paid by company) and service charges (paid by guest)
          $originalCheckOut = $booking->original_check_out ? \Carbon\Carbon::parse($booking->original_check_out) : \Carbon\Carbon::parse($booking->check_out);
          $displayOriginalNights = $booking->check_in->diffInDays($originalCheckOut);
          $displayRoomPriceUsd = $booking->room ? ($booking->room->price_per_night * $displayOriginalNights) : 0;
          $displayRoomPriceTsh = $displayRoomPriceUsd * $exchangeRate;
          $displayExtensionUsd = 0;
          $displayExtensionTsh = 0;
          $displayExtensionNights = 0;
          if ($booking->extension_status === 'approved' && $booking->original_check_out && $booking->extension_requested_to) {
            $originalCheckOutDate = \Carbon\Carbon::parse($booking->original_check_out);
            $requestedCheckOut = \Carbon\Carbon::parse($booking->extension_requested_to);
            $displayExtensionNights = $originalCheckOutDate->diffInDays($requestedCheckOut);
            if ($displayExtensionNights > 0 && $booking->room) {
              $displayExtensionUsd = $booking->room->price_per_night * $displayExtensionNights;
              $displayExtensionTsh = $displayExtensionUsd * $exchangeRate;
            }
          }
          // Get unpaid self-paid service charges (include room_charge if responsabilidad is guest)
          $displayServiceRequests = $serviceRequests->filter(function($sr) {
            $paymentStatus = $sr->payment_status ?? 'pending';
            // Include everything that isn't already 'paid'
            return $paymentStatus !== 'paid';
          });
          $displayGuestServiceTsh = $displayServiceRequests->sum('total_price_tsh');
          $displayCompanyBillTsh = $displayRoomPriceTsh + $displayExtensionTsh;
          $displayTotalBillTsh = $displayCompanyBillTsh + $displayGuestServiceTsh;
        @endphp
        <div class="mb-4">
          <h5 style="color: #e07632; border-bottom: 2px solid #e07632; padding-bottom: 10px; margin-bottom: 15px;">Bill Breakdown</h5>
          <div class="row">
            <div class="col-md-8 offset-md-4">
              <div style="background-color: #f8f9fa; padding: 20px; border: 2px solid #e07632;">
                <table class="table table-borderless mb-0">
                  <tr>
                    <td><strong>Room Charges ({{ $displayOriginalNights }} night(s)):</strong></td>
                    <td class="text-right">
                      <strong>{{ number_format($displayRoomPriceTsh, 2) }} TZS</strong>
                      <br><small class="text-success"><i class="fa fa-building"></i> Paid by {{ $booking->company->name ?? 'Company' }}</small>
                    </td>
                  </tr>
                  @if($displayExtensionTsh > 0)
                  <tr>
                    <td><strong>Extension Charges ({{ $displayExtensionNights }} night(s)):</strong></td>
                    <td class="text-right">
                      <strong>{{ number_format($displayExtensionTsh, 2) }} TZS</strong>
                      <br><small class="text-success"><i class="fa fa-building"></i> Paid by {{ $booking->company->name ?? 'Company' }}</small>
                    </td>
                  </tr>
                  @endif
                  @if($displayGuestServiceTsh > 0)
                  <tr>
                    <td><strong>Service Charges (Self-Paid):</strong></td>
                    <td class="text-right">
                      <strong>{{ number_format($displayGuestServiceTsh, 2) }} TZS</strong>
                      <br><small class="text-warning"><i class="fa fa-user"></i> Paid by Guest</small>
                    </td>
                  </tr>
                  @endif
                  <tr style="border-top: 2px solid #e07632;">
                    <td><strong>Total Bill:</strong></td>
                    <td class="text-right">
                      <strong>{{ number_format($displayTotalBillTsh, 2) }} TZS</strong>
                    </td>
                  </tr>
                  <tr style="border-top: 1px solid #ddd;">
                    <td><strong>Company Portion:</strong></td>
                    <td class="text-right">
                      <strong style="color: #28a745;">{{ number_format($displayCompanyBillTsh, 2) }} TZS</strong>
                      <br><small class="text-success"><i class="fa fa-building"></i> Paid by {{ $booking->company->name ?? 'Company' }}</small>
                    </td>
                  </tr>
                  <tr>
                    <td><strong>Guest Portion:</strong></td>
                    <td class="text-right">
                      <strong style="color: {{ $displayGuestServiceTsh > 0 ? '#e07632' : '#28a745' }};">{{ number_format($displayGuestServiceTsh, 2) }} TZS</strong>
                      @if($displayGuestServiceTsh > 0)
                        <br><small class="text-warning"><i class="fa fa-user"></i> Self-Paid Services</small>
                      @else
                        <br><small class="text-success"><i class="fa fa-check-circle"></i> No Charges</small>
                      @endif
                    </td>
                  </tr>
                </table>
              </div>
            </div>
          </div>
        </div>
        @elseif($isStaffViewingCorporate)
        <!-- Staff Viewing Corporate Booking Information -->
        <div class="mb-4">
          <div class="alert alert-info" style="background-color: #e7f3ff; border-left: 4px solid #17a2b8; padding: 20px;">
            <h5 style="color: #17a2b8; margin-bottom: 15px;">
              <i class="fa fa-building"></i> Corporate Booking: {{ $booking->company->name ?? 'N/A' }}
            </h5>
            <div style="font-size: 14px; line-height: 1.8;">
              <div style="margin-bottom: 10px;">
                <i class="fa fa-check-circle" style="color: #28a745;"></i> <strong>Room Charges:</strong> Paid by Company
              </div>
              @if($paymentResponsibility === 'company')
              <div style="margin-bottom: 10px;">
                <i class="fa fa-check-circle" style="color: #28a745;"></i> <strong>Service Charges:</strong> Paid by Company
              </div>
              <div style="background-color: #d4edda; padding: 15px; border-radius: 4px; margin-top: 15px; text-align: center;">
                <div style="font-size: 16px; font-weight: bold; color: #28a745;">
                  <i class="fa fa-check-circle"></i> All charges for this booking are paid by the company.
                </div>
                <div style="font-size: 13px; color: #666; margin-top: 8px;">
                  Guest has no outstanding balance.
                </div>
              </div>
              @else
              <div style="margin-bottom: 10px;">
                <i class="fa fa-user" style="color: #ffc107;"></i> <strong>Service Charges:</strong> Self-Paid by Guest
              </div>
              <div style="background-color: #fff3cd; padding: 15px; border-radius: 4px; margin-top: 15px;">
                <div style="font-size: 14px; color: #856404;">
                  <i class="fa fa-info-circle"></i> Guest is responsible for paying their own service charges. Room charges are paid by the company.
                </div>
              </div>
              @endif
            </div>
          </div>
        </div>
        @elseif(!($isGuestWithSelfPaidServices ?? false) && !($isStaffViewingCorporate ?? false) && !($isStaffViewingSelfPaid ?? false) && !($isStaffViewingCompanyPaid ?? false))
        <!-- Room Charges (Only for Individual Bookings) -->
        <div class="mb-4">
          <h5 style="color: #e07632; border-bottom: 2px solid #e07632; padding-bottom: 10px; margin-bottom: 15px;">Room Charges</h5>
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead>
                <tr style="background-color: #f8f9fa;">
                  <th>Description</th>
                  <th>Nights</th>
                  <th>Total (TZS)</th>
                </tr>
              </thead>
              <tbody>
                @php
                  // Calculate original nights (excluding extension)
                  $originalCheckOut = $booking->original_check_out ? \Carbon\Carbon::parse($booking->original_check_out) : \Carbon\Carbon::parse($booking->check_out);
                  $originalNights = $booking->check_in->diffInDays($originalCheckOut);
                @endphp
                <tr>
                  <td>Room Accommodation (Original Booking)</td>
                  <td>{{ $originalNights }}</td>
                  <td><strong>{{ number_format($roomPriceTsh, 2) }} TZS</strong></td>
                </tr>
                @if($extensionCostUsd > 0 && $extensionNights > 0)
                <tr style="background-color: #fff3cd;">
                  <td>Room Extension (Additional Nights)</td>
                  <td>{{ $extensionNights }}</td>
                  <td><strong>{{ number_format($extensionCostTsh, 2) }} TZS</strong></td>
                </tr>
                @endif
              </tbody>
            </table>
          </div>
        </div>
        @endif

        <!-- Service Charges -->
         @php
          // Include all service requests for reference in the bill
          $displayServiceRequests = $serviceRequests;
          
          // If viewing a self-paid guest bill, we specifically want to highlight what they owe
          $isSelfPaidView = ($isGuestWithSelfPaidServices ?? false) || ($isStaffViewingSelfPaid ?? false);
        @endphp
        @if($displayServiceRequests->count() > 0)
        <div class="mb-4">
          <h5 style="color: #e07632; border-bottom: 2px solid #e07632; padding-bottom: 10px; margin-bottom: 15px;">Service Charges</h5>
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead>
                <tr style="background-color: #f8f9fa;">
                  <th>#</th>
                  <th>Service</th>
                  <th>Category</th>
                   <th>Quantity</th>
                  <th>Unit Price (TZS)</th>
                  <th>Total (TZS)</th>
                  <th>Payment</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @foreach($displayServiceRequests as $index => $request)
                 <tr>
                  <td>{{ $index + 1 }}</td>
                  <td>
                    <strong>{{ $request->service_specific_data['item_name'] ?? $request->service->name }}</strong>
                    @if($request->guest_request)
                      <br><small class="text-muted"><i class="fa fa-comment-o"></i> {{ $request->guest_request }}</small>
                    @endif
                  </td>
                  <td><span class="badge badge-secondary" style="font-weight: 500;">{{ ucfirst($request->service->category) }}</span></td>
                  <td>
                    <strong>{{ $request->quantity }}</strong>
                    @if($request->service->unit && strtolower($request->service->unit) !== 'per_item')
                      <small class="text-muted">{{ $request->service->unit }}</small>
                    @endif
                  </td>
                  <td>{{ number_format($request->unit_price_tsh, 0) }}</td>
                  <td><strong style="color: #2c3e50;">{{ number_format($request->total_price_tsh, 0) }}</strong></td>
                  <td>
                    @if($isCorporateCompanyPaid)
                        <span class="badge badge-info"><i class="fa fa-building"></i> Company</span>
                    @elseif($request->payment_status === 'paid' || (isset($outstandingBalanceTsh) && $outstandingBalanceTsh <= 50))
                        <span class="badge badge-success"><i class="fa fa-check"></i> Paid</span>
                    @elseif($request->payment_method === 'room_charge')
                        <span class="badge badge-warning"><i class="fa fa-bed"></i> Room Charge</span>
                    @else
                        <span class="badge badge-danger">Pending</span>
                    @endif
                  </td>
                  <td>
                    @if($request->status === 'completed')
                      <span class="badge badge-success">Completed</span>
                    @elseif($request->status === 'approved')
                      <span class="badge badge-info">Approved</span>
                    @endif
                  </td>
                </tr>
                @endforeach
              </tbody>
              <tfoot>
                <tr style="background-color: #f8f9fa;">
                   <td colspan="5" class="text-right"><strong>Total Service Charges:</strong></td>
                  <td colspan="3"><strong>{{ number_format($displayServiceRequests->sum('total_price_tsh'), 2) }} TZS</strong></td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
        @else
        <div class="mb-4">
          <h5 style="color: #e07632; border-bottom: 2px solid #e07632; padding-bottom: 10px; margin-bottom: 15px;">Service Charges</h5>
          @if($isGuestWithSelfPaidServices ?? false)
            <p class="text-muted">No self-paid services used during stay.</p>
            @if($booking->company)
              <div class="alert alert-info" style="background-color: #e7f3ff; border-left: 3px solid #17a2b8; padding: 10px; margin-top: 10px;">
                <small><i class="fa fa-info-circle"></i> Room charges are paid by {{ $booking->company->name }}. Any services you use will be charged to you.</small>
              </div>
            @endif
          @else
            <p class="text-muted">No services used during stay.</p>
          @endif
        </div>
        @endif

        <!-- Total Bill Summary -->
        @php
          // Reuse variables defined earlier
          if (!isset($isCorporateCompanyPaid)) {
            $isCorporateCompanyPaid = ($isGuestWithCompanyPaidServices ?? false) || ($isStaffViewingCompanyPaid ?? false);
          }
          if (!isset($isCorporateSelfPaid)) {
            $isCorporateSelfPaid = ($isGuestWithSelfPaidServices ?? false) || ($isStaffViewingSelfPaid ?? false);
          }
        @endphp
        @if(!$isCorporateCompanyPaid)
        <div class="row mb-4">
          <div class="col-md-8 offset-md-4">
            <div style="background-color: #f8f9fa; padding: 20px; border: 2px solid #e07632;">
              <table class="table table-borderless mb-0">
                @if(!$isCorporateSelfPaid && !($isStaffViewingCorporate ?? false))
                <tr>
                  <td><strong>Room Charges (Original):</strong></td>
                  <td class="text-right">
                    <strong>{{ number_format($roomPriceTsh ?? 0, 2) }} TZS</strong>
                    @if($booking->payment_status === 'paid')
                      <br><small class="text-success"><i class="fa fa-check-circle"></i> Paid</small>
                    @endif
                  </td>
                </tr>
                @if(($extensionCostTsh ?? 0) > 0)
                <tr>
                  <td><strong>Extension Charges:</strong></td>
                  <td class="text-right"><strong>{{ number_format($extensionCostTsh ?? 0, 2) }} TZS</strong></td>
                </tr>
                @endif
                @endif
                @if($isCorporateSelfPaid)
                <tr>
                  <td><strong>Service Charges (Self-Paid):</strong></td>
                  <td class="text-right">
                    <strong>{{ number_format($totalServiceChargesTsh ?? 0, 2) }} TZS</strong>
                    @if($booking->company)
                      <br><small class="text-muted"><i class="fa fa-info-circle"></i> Room charges paid by {{ $booking->company->name }}</small>
                    @endif
                  </td>
                </tr>
                @endif
                @if(!$isCorporateSelfPaid && !($isStaffViewingCorporate ?? false))
                <tr>
                  <td><strong>Service Charges:</strong></td>
                  <td class="text-right"><strong>{{ number_format($totalServiceChargesTsh ?? 0, 2) }} TZS</strong></td>
                </tr>
                @endif
                <tr style="border-top: 2px solid #e07632;">
                  <td><strong>{{ $isCorporateSelfPaid ? 'Total Bill (Guest Portion):' : (($isStaffViewingCorporate ?? false) ? 'Total Bill (Guest Portion):' : 'Total Bill:') }}</strong></td>
                  <td class="text-right">
                    <strong>{{ number_format($totalBillTsh ?? 0, 2) }} TZS</strong>
                  </td>
                </tr>
                @if(isset($amountPaidTsh) && $amountPaidTsh > 0)
                <tr>
                  <td><strong>Amount Paid:</strong></td>
                  <td class="text-right">
                    <strong style="color: #28a745;">{{ number_format($amountPaidTsh, 2) }} TZS</strong>
                  </td>
                </tr>
                @endif
                @if(isset($outstandingBalanceTsh) && $outstandingBalanceTsh > 50)
                <tr style="border-top: 2px solid #e07632; font-size: 18px;">
                  <td><strong>Outstanding Balance:</strong></td>
                  <td class="text-right">
                    <strong style="color: #e07632; font-size: 24px;">{{ number_format($outstandingBalanceTsh, 2) }} TZS</strong>
                  </td>
                </tr>
                @elseif(isset($outstandingBalanceTsh) && $outstandingBalanceTsh <= 50)
                <tr style="border-top: 2px solid #e07632; font-size: 18px;">
                  <td><strong>Status:</strong></td>
                  <td class="text-right">
                    <span class="badge badge-success" style="font-size: 16px;">All Paid</span>
                  </td>
                </tr>
                @endif
              </table>
            </div>
          </div>
        </div>
        @endif

        <!-- Payment Information -->
        @if(!$isCorporateCompanyPaid)
        <div class="mb-4">
          <h5 style="color: #e07632; border-bottom: 2px solid #e07632; padding-bottom: 10px; margin-bottom: 15px;">Payment Information</h5>
          <table class="table table-borderless">
            <tr>
              <td><strong>Payment Status:</strong></td>
              <td>
                 @if($isCorporateSelfPaid)
                  @php
                    $selfPaidTotal = $totalBillTsh ?? 0;
                    $selfPaidAmountPaid = $amountPaidTsh ?? 0;
                  @endphp
                  @if($outstandingBalanceTsh <= 50 && $selfPaidTotal > 0)
                    <span class="badge badge-success" style="font-size: 14px;"><i class="fa fa-check-circle"></i> Fully Paid</span>
                  @elseif($selfPaidAmountPaid > 50)
                    <span class="badge badge-warning" style="font-size: 14px;"><i class="fa fa-adjust"></i> Partially Paid</span>
                    <br><small class="text-muted">{{ number_format($amountPaidTsh, 2) }} TZS paid of {{ number_format($selfPaidTotal, 2) }} TZS total</small>
                  @else
                    <span class="badge badge-danger" style="font-size: 14px;"><i class="fa fa-clock-o"></i> Unpaid / Pending</span>
                    <br><small class="text-muted">Total Outstanding: {{ number_format($selfPaidTotal, 2) }} TZS</small>
                  @endif
                @elseif($isStaffViewingCorporate)
                  @if($booking->company)
                    <span class="badge badge-info"><i class="fa fa-building"></i> Company Paid</span>
                    <br><small class="text-muted">Room charges paid by {{ $booking->company->name }}</small>
                    @if(($outstandingBalanceTsh ?? 0) == 0)
                      <br><small class="text-success"><i class="fa fa-check-circle"></i> Guest has no outstanding balance</small>
                    @endif
                  @endif
                @else
                  @php
                    // Use pre-calculated values from controller
                    $totalExpectedTsh = $totalBillTsh ?? 0;
                    $totalPaidTsh = $amountPaidTsh ?? 0;
                    $totalOutstandingTsh = $outstandingBalanceTsh ?? 0;
                    $totalOutstandingUsd = $totalOutstandingTsh / $exchangeRate;
                  @endphp
                  
                  @if($totalOutstandingTsh <= 50)
                    <span class="badge badge-success">All Paid</span>
                    @if($booking->paid_at)
                      <small class="text-muted">({{ $booking->paid_at->format('M d, Y') }})</small>
                    @endif
                  @elseif($totalPaidTsh > 50)
                    <span class="badge badge-warning">Partially Paid</span>
                    <br><small class="text-muted">{{ number_format($totalPaidTsh, 2) }} TZS paid of {{ number_format($totalExpectedTsh, 2) }} TZS total</small>
                    <br><small class="text-danger"><strong>Outstanding: {{ number_format($totalOutstandingTsh, 2) }} TZS</strong></small>
                  @else
                    <span class="badge badge-warning">Pending Payment</span>
                    <br><small class="text-muted">Total Due: {{ number_format($totalExpectedTsh, 2) }} TZS</small>
                  @endif
                @endif
              </td>
            </tr>
            @if(($extensionCostTsh ?? 0) > 0 && !$isCorporateSelfPaid && !($isStaffViewingCorporate ?? false) && $totalOutstandingTsh > 50)
            <tr>
              <td><strong>Extension Charges Status:</strong></td>
              <td>
                <span class="badge badge-warning">Outstanding</span>
                <br>
                <small class="text-muted">
                  <i class="fa fa-info-circle"></i> Can be paid at checkout
                </small>
              </td>
            </tr>
            @endif
            @if(!$isCorporateSelfPaid && !($isStaffViewingCorporate ?? false) && ($totalServiceChargesTsh ?? 0) > 0 && $totalOutstandingTsh > 50)
            <tr>
              <td><strong>{{ $isCorporateSelfPaid ? 'Service Charges Status (Self-Paid):' : (($isStaffViewingCorporate ?? false) ? 'Service Charges Status:' : 'Service Charges Status:') }}</strong></td>
              <td>
                @if($isCorporateSelfPaid)
                  @if(($totalServiceChargesTsh ?? 0) > 0)
                    @if(($outstandingBalanceTsh ?? 0) > 0)
                      <span class="badge badge-warning">Outstanding</span>
                      <br>
                      <small class="text-muted">
                        {{ number_format($outstandingBalanceTsh ?? 0, 2) }} TZS remaining
                      </small>
                    @else
                      <span class="badge badge-success">Paid</span>
                      <br>
                      <small class="text-muted">
                        {{ number_format($totalServiceChargesTsh ?? 0, 2) }} TZS
                      </small>
                    @endif
                  @else
                    <span class="badge badge-info">No self-paid services</span>
                    @if($booking->company)
                      <br><small class="text-muted">Room charges paid by {{ $booking->company->name }}</small>
                    @endif
                  @endif
                @elseif($isStaffViewingCorporate)
                  @if($booking->company)
                    <span class="badge badge-info"><i class="fa fa-building"></i> Company Paid</span>
                    <br><small class="text-muted">All charges paid by {{ $booking->company->name }}</small>
                  @else
                    <span class="badge badge-info">No Charges</span>
                  @endif
                @else
                  @if($totalServiceChargesTsh > 0)
                    <span class="badge badge-warning">Outstanding</span>
                    <br>
                    <small class="text-muted">
                      <i class="fa fa-info-circle"></i> Can be paid at checkout
                    </small>
                  @else
                    <span class="badge badge-success">No service charges</span>
                  @endif
                @endif
              </td>
            </tr>
            @endif
          </table>
        </div>
        @endif

        <!-- Footer -->
        <div class="text-center mt-4" style="border-top: 2px solid #e07632; padding-top: 20px;">
          <p style="color: #666; margin: 0;">
            <strong>Thank you for staying with Umoj Lutheran Hostel!</strong><br>
            We hope to see you again soon.
          </p>
          <p style="color: #999; font-size: 12px; margin-top: 10px;">
            Bill Generated: {{ now()->format('F d, Y \a\t g:i A') }}
          </p>
        </div>

        <!-- Action Buttons -->
        <div class="text-center mt-4">
          <button onclick="window.print()" class="btn btn-primary">
            <i class="fa fa-print"></i> Print Bill
          </button>
          @if($role === 'customer')
          <a href="{{ route('customer.dashboard') }}" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> Back to Dashboard
          </a>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

<style>
@media print {
  .app-title, .breadcrumb, .btn, .app-sidebar, .app-header {
    display: none !important;
  }
  .tile {
    border: none !important;
    box-shadow: none !important;
  }
}
</style>

@endsection





