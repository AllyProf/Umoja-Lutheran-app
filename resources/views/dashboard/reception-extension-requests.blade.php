@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-calendar-plus-o"></i> Extension Requests</h1>
    <p>Manage checkout extension requests from guests</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('reception.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Extension Requests</a></li>
  </ul>
</div>

<!-- Statistics Cards -->
<div class="row mb-3">
  <div class="col-md-3 col-lg-3">
    <div class="widget-small primary coloured-icon">
      <i class="icon fa fa-list fa-2x"></i>
      <div class="info">
        <h4>Total</h4>
        <p><b>{{ $stats['total'] }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-lg-3">
    <div class="widget-small warning coloured-icon">
      <i class="icon fa fa-clock-o fa-2x"></i>
      <div class="info">
        <h4>Pending</h4>
        <p><b>{{ $stats['pending'] }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-lg-3">
    <div class="widget-small success coloured-icon">
      <i class="icon fa fa-check fa-2x"></i>
      <div class="info">
        <h4>Approved</h4>
        <p><b>{{ $stats['approved'] }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-lg-3">
    <div class="widget-small danger coloured-icon">
      <i class="icon fa fa-times fa-2x"></i>
      <div class="info">
        <h4>Rejected</h4>
        <p><b>{{ $stats['rejected'] }}</b></p>
      </div>
    </div>
  </div>
</div>

<!-- Extension Requests Table -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-calendar-plus-o"></i> Extension Requests</h3>
      <div class="tile-body">
        <!-- Filters -->
        <div class="row mb-3">
          <div class="col-md-3 col-12 mb-2 mb-md-0">
            <select class="form-control" id="statusFilter" onchange="filterExtensions()" style="font-size: 16px;">
              <option value="all">All Statuses</option>
              <option value="pending">Pending</option>
              <option value="approved">Approved</option>
              <option value="rejected">Rejected</option>
            </select>
          </div>
          <div class="col-md-7 col-12 mb-2 mb-md-0">
            <input type="text" class="form-control" id="searchInput" placeholder="Search by booking reference, guest name, or email..." onkeyup="filterExtensions()" style="font-size: 16px;">
          </div>
          <div class="col-md-2 col-12">
            <button class="btn btn-secondary btn-block" onclick="resetExtensionFilters()">
              <i class="fa fa-refresh"></i> Reset
            </button>
          </div>
        </div>

        @if($extensions->count() > 0)
        <!-- Desktop Table View -->
        <div class="table-responsive">
          <table class="table table-hover table-bordered" id="extensionsTable">
            <thead>
              <tr>
                <th>Booking Reference</th>
                <th>Guest</th>
                <th>Room</th>
                <th>Current Check-out</th>
                <th>Requested Check-out</th>
                <th>Additional Nights</th>
                <th>Additional Cost</th>
                <th>Status</th>
                <th>Requested At</th>
                <th>Reason</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($extensions as $extension)
              @php
                // Use original_check_out if available (for approved extensions), otherwise use current check_out
                $originalCheckOutDate = $extension->original_check_out 
                    ? \Carbon\Carbon::parse($extension->original_check_out)->startOfDay()
                    : ($extension->extension_status === 'approved' 
                        ? null // Can't determine original for old approved extensions
                        : \Carbon\Carbon::parse($extension->check_out)->startOfDay());
                
                $requestedCheckOut = $extension->extension_requested_to 
                    ? \Carbon\Carbon::parse($extension->extension_requested_to)->startOfDay() 
                    : null;
                
                // Determine if it's a decrease request
                $isDecreaseRequest = false;
                if (isset($extension->extension_type) && $extension->extension_type === 'decrease') {
                    $isDecreaseRequest = true;
                } elseif ($originalCheckOutDate && $requestedCheckOut) {
                    $isDecreaseRequest = $requestedCheckOut->lt($originalCheckOutDate);
                }
                
                // Only calculate if we have both dates
                if ($originalCheckOutDate && $requestedCheckOut) {
                    $origDateStr = $originalCheckOutDate->format('Y-m-d');
                    $reqDateStr = $requestedCheckOut->format('Y-m-d');
                    
                    // Calculate absolute difference in days
                    $daysDiff = $requestedCheckOut->diffInDays($originalCheckOutDate, true);
                    // Ensure at least 1 day if strings are different but calculation gave 0 (due to time issues)
                    if ($daysDiff == 0 && $origDateStr != $reqDateStr) {
                        $daysDiff = 1;
                    }
                    
                    if ($reqDateStr > $origDateStr) {
                        // Extension: requested date is after original
                        $additionalNights = $daysDiff;
                        $additionalCost = $extension->room ? $extension->room->price_per_night * $additionalNights : 0;
                    } elseif ($reqDateStr < $origDateStr) {
                        // Decrease: requested date is before original
                        $additionalNights = -$daysDiff; // Make negative
                        $additionalCost = 0; // No refund for decreases
                    } else {
                        // Same date
                        $additionalNights = 0;
                        $additionalCost = 0;
                    }
                } else {
                    $additionalNights = 0;
                    $additionalCost = 0;
                }
              @endphp
              <tr class="extension-row"
                  data-status="{{ $extension->extension_status }}"
                  data-booking-ref="{{ strtolower($extension->booking_reference) }}"
                  data-guest-name="{{ strtolower($extension->guest_name) }}"
                  data-guest-email="{{ strtolower($extension->guest_email) }}">
                <td><strong>{{ $extension->booking_reference }}</strong></td>
                <td>
                  <strong>{{ $extension->guest_name }}</strong><br>
                  <small>{{ $extension->guest_email }}</small><br>
                  <small>{{ $extension->guest_phone }}</small>
                </td>
                <td>
                  <span class="badge badge-primary">{{ $extension->room->room_type ?? 'N/A' }}</span><br>
                  <small>{{ $extension->room->room_number ?? 'N/A' }}</small>
                </td>
                <td>
                  @if($originalCheckOutDate)
                    {{ $originalCheckOutDate->format('M d, Y') }}
                  @else
                    <span class="text-muted" title="Original checkout date not available for this approved extension">N/A</span>
                  @endif
                </td>
                <td>
                  @if($extension->extension_requested_to)
                    <strong>{{ $extension->extension_requested_to->format('M d, Y') }}</strong>
                  @else
                    <span class="text-muted">N/A</span>
                  @endif
                </td>
                <td>
                  @if($additionalNights > 0)
                    <strong class="text-success">+{{ $additionalNights }} night(s)</strong>
                  @elseif($additionalNights < 0)
                    <strong class="text-warning">{{ $additionalNights }} night(s)</strong>
                    <br><small class="text-muted">(Decrease)</small>
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td>
                  @if($additionalCost > 0)
                    <strong class="text-success">+${{ number_format($additionalCost, 2) }}</strong><br>
                    <small>+{{ number_format($additionalCost * $exchangeRate, 2) }} TZS</small>
                  @elseif($additionalNights < 0)
                    <span class="badge badge-secondary">No Refund</span>
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td>
                  @if($extension->extension_status === 'pending')
                    <span class="badge badge-warning">Pending</span>
                  @elseif($extension->extension_status === 'approved')
                    <span class="badge badge-success">Approved</span>
                  @elseif($extension->extension_status === 'rejected')
                    <span class="badge badge-danger">Rejected</span>
                  @endif
                </td>
                <td>
                  @if($extension->extension_requested_at)
                    {{ $extension->extension_requested_at->format('M d, Y H:i') }}
                  @else
                    <span class="text-muted">N/A</span>
                  @endif
                </td>
                <td>
                  @if($extension->extension_reason)
                    <small>{{ Str::limit($extension->extension_reason, 50) }}</small>
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td>
                  @if($extension->extension_status === 'pending')
                    <button onclick="handleExtension({{ $extension->id }}, 'approve')" class="btn btn-sm btn-success mr-1" title="Approve Extension">
                      <i class="fa fa-check"></i> Approve
                    </button>
                    <button onclick="handleExtension({{ $extension->id }}, 'reject')" class="btn btn-sm btn-danger" title="Reject Extension">
                      <i class="fa fa-times"></i> Reject
                    </button>
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-3">
          {{ $extensions->links() }}
        </div>
        @else
        <div class="text-center" style="padding: 50px;">
          <i class="fa fa-calendar-plus-o fa-5x text-muted mb-3"></i>
          <h3>No Extension Requests Found</h3>
          <p class="text-muted">No extension requests match your filters.</p>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Rejection Modal -->
<div class="modal fade" id="rejectionModal" tabindex="-1" role="dialog" aria-labelledby="rejectionModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="rejectionModalLabel">
          <i class="fa fa-times-circle"></i> Reject Extension Request
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="rejectBookingId">
        <div class="form-group">
          <label for="rejectionReason">Rejection Reason <span class="text-danger">*</span></label>
          <textarea 
            class="form-control" 
            id="rejectionReason" 
            rows="4" 
            placeholder="Please provide a reason for rejecting this extension request..."
            required></textarea>
          <small class="form-text text-muted">This reason will be saved and may be visible to the guest.</small>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          <i class="fa fa-times"></i> Cancel
        </button>
        <button type="button" class="btn btn-danger" onclick="submitRejection()">
          <i class="fa fa-ban"></i> Reject Request
        </button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<style>
/* Mobile Responsive Styles */
@media (max-width: 767px) {
  /* Statistics Cards */
  .col-md-3.col-lg-3 {
    margin-bottom: 15px;
  }
  
  /* Filters */
  .row .col-md-3,
  .row .col-md-7,
  .row .col-md-2 {
    margin-bottom: 10px;
  }
  
  /* Table - Hide on Mobile */
  #extensionsTable {
    display: none;
  }
  
  /* Mobile Cards - Show on Mobile */
  .mobile-extension-cards {
    display: block;
  }
  
  /* Pagination */
  .pagination {
    justify-content: center;
    flex-wrap: wrap;
  }
  
  .pagination .page-link {
    padding: 8px 12px;
    font-size: 14px;
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
  
  .mobile-extension-card .btn {
    flex: 0 0 100% !important;
    min-width: 100% !important;
    margin-bottom: 8px;
  }
  
  .mobile-extension-card .btn:last-child {
    margin-bottom: 0;
  }
  
  .widget-small {
    padding: 10px;
  }
  
  .widget-small .icon {
    font-size: 1.5rem !important;
  }
  
  .widget-small .info h4 {
    font-size: 14px;
  }
  
  .widget-small .info p {
    font-size: 20px;
  }
}
</style>
<script>
function handleExtension(bookingId, action) {
    if (action === 'approve') {
        swal({
            title: "Confirm Approval",
            text: "Are you sure you want to approve this extension request? The checkout date and total price will be updated.",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#28a745",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, approve it!",
            cancelButtonText: "Cancel"
        }, function(isConfirm) {
            if (isConfirm) {
                submitExtensionAction(bookingId, 'approve', '');
            }
        });
    } else if (action === 'reject') {
        // Show rejection modal
        $('#rejectBookingId').val(bookingId);
        $('#rejectionReason').val('');
        $('#rejectionModal').modal('show');
    }
}

function submitRejection() {
    const bookingId = $('#rejectBookingId').val();
    const reason = $('#rejectionReason').val().trim();
    
    if (!reason) {
        swal({
            title: "Reason Required",
            text: "Please provide a reason for rejecting this extension request.",
            type: "warning",
            confirmButtonColor: "#d33"
        });
        return;
    }
    
    $('#rejectionModal').modal('hide');
    submitExtensionAction(bookingId, 'reject', reason);
}

function submitExtensionAction(bookingId, action, adminNotes) {
    fetch('{{ route("reception.bookings.extension", ":id") }}'.replace(':id', bookingId), {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            action: action,
            admin_notes: adminNotes || ''
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
                location.reload();
            });
        } else {
            swal({
                title: "Error!",
                text: data.message || "An error occurred. Please try again.",
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

function filterExtensions() {
  const statusFilter = document.getElementById('statusFilter').value;
  const searchInput = document.getElementById('searchInput').value.toLowerCase();
  
  // Filter both table rows and mobile cards
  const rows = document.querySelectorAll('.extension-row');
  rows.forEach(row => {
    const status = row.getAttribute('data-status');
    const bookingRef = row.getAttribute('data-booking-ref');
    const guestName = row.getAttribute('data-guest-name');
    const guestEmail = row.getAttribute('data-guest-email');
    
    let show = true;
    
    // Status filter
    if (statusFilter !== 'all' && status !== statusFilter) {
      show = false;
    }
    
    // Search filter
    if (searchInput) {
      if (!bookingRef.includes(searchInput) && 
          !guestName.includes(searchInput) && 
          !guestEmail.includes(searchInput)) {
        show = false;
      }
    }
    
    row.style.display = show ? '' : 'none';
  });
}

function resetExtensionFilters() {
  document.getElementById('statusFilter').value = 'all';
  document.getElementById('searchInput').value = '';
  filterExtensions();
}
</script>
@endsection

