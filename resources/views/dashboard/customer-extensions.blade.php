@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-calendar-plus"></i> My Extensions</h1>
    <p>View all your extension requests</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">My Extensions</a></li>
  </ul>
</div>

<!-- Extensions Table -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-list"></i> Extension Requests</h3>
      <div class="tile-body">
        @if($bookings->count() > 0)
        <!-- Desktop Table View -->
        <div class="table-responsive">
          <table class="table table-hover table-bordered" id="extensionsTable">
            <thead>
              <tr>
                <th>Booking Reference</th>
                <th>Room</th>
                <th>Original Check-out</th>
                <th>Requested Check-out</th>
                <th>Additional Nights</th>
                <th>Extension Cost</th>
                <th>Status</th>
                <th>Requested Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($bookings as $booking)
              @php
                $originalCheckOut = $booking->original_check_out ? \Carbon\Carbon::parse($booking->original_check_out) : $booking->check_out;
                $extensionNights = 0;
                $extensionCostUsd = 0;
                // Calculate extension nights from original check-out to current check-out (if approved) or requested check-out
                if ($booking->extension_status === 'approved' && $booking->check_out) {
                  $extensionNights = $originalCheckOut->diffInDays($booking->check_out);
                } elseif ($booking->extension_requested_to) {
                  $requestedCheckOut = \Carbon\Carbon::parse($booking->extension_requested_to);
                  $extensionNights = $originalCheckOut->diffInDays($requestedCheckOut);
                }
                if ($booking->room && $extensionNights > 0) {
                  $extensionCostUsd = $booking->room->price_per_night * $extensionNights;
                }
                $bookingExchangeRate = $booking->locked_exchange_rate ?? $exchangeRate;
                $extensionCostTsh = $extensionCostUsd * $bookingExchangeRate;
              @endphp
              <tr>
                <td><strong>{{ $booking->booking_reference }}</strong></td>
                <td>
                  <span class="badge badge-primary">{{ $booking->room->room_type ?? 'N/A' }}</span><br>
                  <small>{{ $booking->room->room_number ?? 'N/A' }}</small>
                </td>
                <td>
                  <strong>{{ $originalCheckOut->format('M d, Y') }}</strong>
                </td>
                <td>
                  @if($booking->extension_status === 'approved' && $booking->check_out)
                    <strong style="color: #28a745;">{{ $booking->check_out->format('M d, Y') }}</strong>
                    @if($booking->extension_requested_to && \Carbon\Carbon::parse($booking->extension_requested_to)->format('Y-m-d') !== $booking->check_out->format('Y-m-d'))
                      <br><small class="text-muted">Requested: {{ \Carbon\Carbon::parse($booking->extension_requested_to)->format('M d, Y') }}</small>
                    @endif
                  @elseif($booking->extension_requested_to)
                    <strong style="color: #e07632;">{{ \Carbon\Carbon::parse($booking->extension_requested_to)->format('M d, Y') }}</strong>
                  @else
                    <span class="text-muted">N/A</span>
                  @endif
                </td>
                <td>
                  @if($extensionNights > 0)
                    <strong>{{ $extensionNights }} night(s)</strong>
                  @elseif($booking->extension_status === 'approved' && $booking->original_check_out)
                    @php
                      $actualExtensionNights = \Carbon\Carbon::parse($booking->original_check_out)->diffInDays($booking->check_out);
                    @endphp
                    @if($actualExtensionNights > 0)
                      <strong>{{ $actualExtensionNights }} night(s)</strong>
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td>
                  @if($extensionCostUsd > 0 && $booking->extension_status === 'approved')
                    <div><strong>${{ number_format($extensionCostUsd, 2) }}</strong></div>
                    <div style="color: #e07632; font-size: 11px;">
                      <strong>≈ {{ number_format($extensionCostTsh, 2) }} TZS</strong>
                    </div>
                  @elseif($extensionCostUsd > 0)
                    <div><strong style="color: #666;">${{ number_format($extensionCostUsd, 2) }}</strong></div>
                    <div style="color: #666; font-size: 11px;">
                      <strong>≈ {{ number_format($extensionCostTsh, 2) }} TZS</strong>
                    </div>
                    <small class="text-muted">(Estimated)</small>
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td>
                  @if($booking->extension_status === 'pending')
                    <span class="badge badge-warning">Pending Review</span>
                  @elseif($booking->extension_status === 'approved')
                    <span class="badge badge-success">Approved</span>
                  @elseif($booking->extension_status === 'rejected')
                    <span class="badge badge-danger">Rejected</span>
                  @else
                    <span class="badge badge-secondary">{{ ucfirst($booking->extension_status) }}</span>
                  @endif
                </td>
                <td>
                  @if($booking->extension_requested_at)
                    {{ \Carbon\Carbon::parse($booking->extension_requested_at)->format('M d, Y H:i') }}
                  @else
                    <span class="text-muted">N/A</span>
                  @endif
                </td>
                <td>
                  <button onclick="viewExtensionDetails({{ $booking->id }})" class="btn btn-sm btn-info" title="View Details">
                    <i class="fa fa-eye"></i> Details
                  </button>
                </td>
              </tr>
              <!-- Extension Details Row -->
              <tr class="extension-details-row" id="extension-details-{{ $booking->id }}" style="display: none;">
                <td colspan="9" style="background-color: #f8f9fa; padding: 20px;">
                  <div class="row">
                    <div class="col-md-12">
                      <h6 style="color: #e07632; margin-bottom: 15px; border-bottom: 2px solid #e07632; padding-bottom: 10px;">
                        <i class="fa fa-calendar-plus"></i> Extension Details - {{ $booking->booking_reference }}
                      </h6>
                      <div class="row">
                        <div class="col-md-6">
                          <h6 style="color: #666; margin-bottom: 10px;">Booking Information</h6>
                          <table class="table table-sm table-borderless">
                            <tr>
                              <td width="40%"><strong>Booking Reference:</strong></td>
                              <td>{{ $booking->booking_reference }}</td>
                            </tr>
                            <tr>
                              <td><strong>Room:</strong></td>
                              <td>{{ $booking->room->room_type ?? 'N/A' }} ({{ $booking->room->room_number ?? 'N/A' }})</td>
                            </tr>
                            <tr>
                              <td><strong>Check-in Date:</strong></td>
                              <td>{{ $booking->check_in->format('M d, Y') }}</td>
                            </tr>
                            <tr>
                              <td><strong>Original Check-out:</strong></td>
                              <td><strong>{{ $originalCheckOut->format('M d, Y') }}</strong></td>
                            </tr>
                            <tr>
                              <td><strong>Current Check-out:</strong></td>
                              <td>{{ $booking->check_out->format('M d, Y') }}</td>
                            </tr>
                          </table>
                        </div>
                        <div class="col-md-6">
                          <h6 style="color: #666; margin-bottom: 10px;">Extension Information</h6>
                          <table class="table table-sm table-borderless">
                            <tr>
                              <td width="40%"><strong>Status:</strong></td>
                              <td>
                                @if($booking->extension_status === 'pending')
                                  <span class="badge badge-warning">Pending Review</span>
                                  <br><small class="text-muted">Your request is being reviewed by reception</small>
                                @elseif($booking->extension_status === 'approved')
                                  <span class="badge badge-success">Approved</span>
                                  <br><small class="text-success">Your extension has been approved!</small>
                                @elseif($booking->extension_status === 'rejected')
                                  <span class="badge badge-danger">Rejected</span>
                                  @if($booking->extension_rejection_reason)
                                    <br><small class="text-danger">{{ $booking->extension_rejection_reason }}</small>
                                  @endif
                                @endif
                              </td>
                            </tr>
                            @if($booking->extension_status === 'approved' && $booking->check_out)
                            <tr>
                              <td><strong>Current Check-out:</strong></td>
                              <td><strong style="color: #28a745;">{{ $booking->check_out->format('M d, Y') }}</strong></td>
                            </tr>
                            @if($booking->extension_requested_to && \Carbon\Carbon::parse($booking->extension_requested_to)->format('Y-m-d') !== $booking->check_out->format('Y-m-d'))
                            <tr>
                              <td><strong>Requested Check-out:</strong></td>
                              <td><strong style="color: #e07632;">{{ \Carbon\Carbon::parse($booking->extension_requested_to)->format('M d, Y') }}</strong></td>
                            </tr>
                            @endif
                            @elseif($booking->extension_requested_to)
                            <tr>
                              <td><strong>Requested Check-out:</strong></td>
                              <td><strong style="color: #e07632;">{{ \Carbon\Carbon::parse($booking->extension_requested_to)->format('M d, Y') }}</strong></td>
                            </tr>
                            @endif
                            @if($extensionNights > 0)
                            <tr>
                              <td><strong>Additional Nights:</strong></td>
                              <td><strong>{{ $extensionNights }} night(s)</strong></td>
                            </tr>
                            @endif
                            @if($booking->extension_reason)
                            <tr>
                              <td><strong>Reason:</strong></td>
                              <td>{{ $booking->extension_reason }}</td>
                            </tr>
                            @endif
                            @if($extensionCostUsd > 0)
                            <tr style="border-top: 1px solid #ddd;">
                              <td><strong>Extension Cost:</strong></td>
                              <td>
                                <strong style="color: #e07632; font-size: 16px;">
                                  ${{ number_format($extensionCostUsd, 2) }}
                                </strong>
                                <br>
                                <small class="text-muted">
                                  ≈ {{ number_format($extensionCostTsh, 2) }} TZS
                                </small>
                                @if($booking->extension_status === 'pending')
                                  <br><small class="text-muted">(Estimated - subject to approval)</small>
                                @endif
                              </td>
                            </tr>
                            @endif
                            @if($booking->extension_requested_at)
                            <tr>
                              <td><strong>Requested On:</strong></td>
                              <td>{{ \Carbon\Carbon::parse($booking->extension_requested_at)->format('M d, Y H:i') }}</td>
                            </tr>
                            @endif
                            @if($booking->extension_status === 'approved' && $booking->extension_approved_at)
                            <tr>
                              <td><strong>Approved On:</strong></td>
                              <td>{{ \Carbon\Carbon::parse($booking->extension_approved_at)->format('M d, Y H:i') }}</td>
                            </tr>
                            @endif
                            @if($booking->extension_status === 'rejected' && $booking->extension_rejected_at)
                            <tr>
                              <td><strong>Rejected On:</strong></td>
                              <td>{{ \Carbon\Carbon::parse($booking->extension_rejected_at)->format('M d, Y H:i') }}</td>
                            </tr>
                            @endif
                          </table>
                        </div>
                      </div>
                      @if($booking->extension_status === 'approved' && $extensionCostUsd > 0)
                      <div class="alert alert-info mt-3" style="background-color: #e7f3ff; border-left: 4px solid #2196F3;">
                        <i class="fa fa-info-circle"></i> <strong>Payment Information:</strong> 
                        The extension cost will be added to your final bill. You can pay it at checkout or via PayPal.
                      </div>
                      @endif
                    </div>
                  </div>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @else
        <div class="text-center" style="padding: 50px;">
          <i class="fa fa-calendar-plus fa-5x text-muted mb-3"></i>
          @if(!$hasCheckedIn)
          <h3>Check In Required</h3>
          <p class="text-muted">You need to check in first before you can request extensions.</p>
          @else
          <h3>No Extension Requests</h3>
          <p class="text-muted">You haven't requested any extensions yet.</p>
          @endif
          <a href="{{ route('customer.dashboard') }}" class="btn btn-primary mt-3">
            <i class="fa fa-arrow-left"></i> Back to Dashboard
          </a>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<style>
/* Mobile Responsive Styles */
@media (max-width: 767px) {
  /* Table - Hide on Mobile */
  #extensionsTable {
    display: none;
  }
  
  /* Mobile Cards - Show on Mobile */
  .mobile-extension-cards {
    display: block;
  }
  
  /* Extension Details Rows in Table - Hide on Mobile */
  .extension-details-row {
    display: none !important;
  }
}

/* Desktop - Hide mobile cards */
@media (min-width: 768px) {
  .mobile-extension-cards {
    display: none;
  }
  
  #extensionsTable {
    display: table;
  }
}

/* Very Small Screens */
@media (max-width: 480px) {
  .mobile-extension-card {
    padding: 12px !important;
  }
  
  .mobile-extension-card h5 {
    font-size: 16px !important;
  }
  
  .mobile-extension-card h6 {
    font-size: 15px !important;
  }
}
</style>
<script>
function viewExtensionDetails(bookingId) {
    const detailsRow = document.getElementById('extension-details-' + bookingId);
    const isVisible = detailsRow.style.display !== 'none';
    
    // Hide all other extension details
    document.querySelectorAll('.extension-details-row').forEach(row => {
        row.style.display = 'none';
    });
    
    // Toggle current row
    if (!isVisible) {
        detailsRow.style.display = 'table-row';
    }
}
</script>
@endsection


