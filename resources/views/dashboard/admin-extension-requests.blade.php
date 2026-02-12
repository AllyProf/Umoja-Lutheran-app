@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-calendar-plus-o"></i> Extension Requests</h1>
    <p>Manage checkout extension requests from guests</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item">
      @php
        $dashRoute = 'admin.dashboard';
        $userRole = Auth::guard('staff')->user()->role ?? '';
        if ($userRole === 'reception') $dashRoute = 'reception.dashboard';
        elseif ($userRole === 'super_admin') $dashRoute = 'super_admin.dashboard';
      @endphp
      <a href="{{ route($dashRoute) }}">Dashboard</a>
    </li>
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
        <form method="GET" action="{{ route('admin.extension-requests') }}" class="mb-3">
          <div class="row">
            <div class="col-md-3 col-12 mb-2 mb-md-0">
              <select name="status" class="form-control" style="font-size: 16px;">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
              </select>
            </div>
            <div class="col-md-7 col-12 mb-2 mb-md-0">
              <input type="text" name="search" class="form-control" placeholder="Search by booking reference, guest name, or email..." value="{{ request('search') }}" style="font-size: 16px;">
            </div>
            <div class="col-md-2 col-12">
              <button type="submit" class="btn btn-primary btn-block">
                <i class="fa fa-search"></i> Filter
              </button>
            </div>
          </div>
        </form>

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
                // For approved extensions without original_check_out, we can't calculate accurately
                $originalCheckOutDate = $extension->original_check_out 
                    ? \Carbon\Carbon::parse($extension->original_check_out) 
                    : ($extension->extension_status === 'approved' 
                        ? null // Can't determine original for old approved extensions
                        : \Carbon\Carbon::parse($extension->check_out));
                $requestedCheckOut = $extension->extension_requested_to ? \Carbon\Carbon::parse($extension->extension_requested_to) : null;
                
                // Determine if it's a decrease request
                $isDecreaseRequest = false;
                if (isset($extension->extension_type) && $extension->extension_type === 'decrease') {
                    $isDecreaseRequest = true;
                } elseif ($originalCheckOutDate && $requestedCheckOut) {
                    $isDecreaseRequest = $requestedCheckOut->lt($originalCheckOutDate);
                }
                
                // Only calculate if we have both dates
                if ($originalCheckOutDate && $requestedCheckOut) {
                    // Calculate nights difference (positive for extension, negative for decrease)
                    if ($requestedCheckOut->gt($originalCheckOutDate)) {
                        // Extension: requested date is after original
                        $additionalNights = $originalCheckOutDate->diffInDays($requestedCheckOut);
                        $additionalCost = $extension->room ? $extension->room->price_per_night * $additionalNights : 0;
                    } else {
                        // Decrease: requested date is before original
                        $additionalNights = -$originalCheckOutDate->diffInDays($requestedCheckOut); // Negative
                        $additionalCost = $extension->room ? -($extension->room->price_per_night * abs($additionalNights)) : 0; // Negative (refund)
                    }
                } else {
                    $additionalNights = 0;
                    $additionalCost = 0;
                }
              @endphp
              <tr>
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
                  @elseif($additionalCost < 0)
                    <strong class="text-warning">${{ number_format($additionalCost, 2) }}</strong><br>
                    <small>{{ number_format($additionalCost * $exchangeRate, 2) }} TZS</small>
                    <br><small class="text-muted">(Refund)</small>
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
    const actionText = action === 'approve' ? 'approve' : 'reject';
    const confirmText = action === 'approve' 
        ? 'Are you sure you want to approve this extension request? The checkout date and total price will be updated.'
        : 'Are you sure you want to reject this extension request?';
    
    swal({
        title: "Confirm " + (action === 'approve' ? 'Approval' : 'Rejection'),
        text: confirmText,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: action === 'approve' ? "#28a745" : "#d33",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, " + actionText + " it!",
        cancelButtonText: "Cancel"
    }, function(isConfirm) {
        if (isConfirm) {
            if (action === 'reject') {
                swal({
                    title: "Rejection Reason (Optional)",
                    text: "Please provide a reason for rejection:",
                    type: "input",
                    inputType: "textarea",
                    showCancelButton: true,
                    confirmButtonText: "Reject",
                    cancelButtonText: "Cancel",
                    inputPlaceholder: "Reason for rejection...",
                    inputValidator: function(value) {
                        return new Promise(function(resolve, reject) {
                            resolve();
                        });
                    }
                }, function(inputValue) {
                    if (inputValue !== false) {
                        submitExtensionAction(bookingId, action, inputValue);
                    }
                });
            } else {
                submitExtensionAction(bookingId, action, '');
            }
        }
    });
}

function submitExtensionAction(bookingId, action, adminNotes) {
    fetch('{{ route("admin.bookings.extension", ":id") }}'.replace(':id', bookingId), {
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
</script>
@endsection

