@extends('dashboard.layouts.app')

@php
  $currentUser = Auth::guard('staff')->user();
  $userRole = strtolower($currentUser->role ?? '');
  $isObserver = $currentUser && ($userRole === 'manager' || $userRole === 'reception' || $userRole === 'super_admin');
@endphp

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-dashboard"></i> Housekeeper Dashboard</h1>
    <p>Welcome back, {{ $currentUser->name }}!@if($isObserver) <span class="badge badge-info">Read-Only Mode</span>@endif</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
  </ul>
</div>

<!-- Statistics Cards -->
<div class="row mb-3">
  <div class="col-md-3 col-lg-3">
    <div class="widget-small warning coloured-icon">
      <i class="icon fa fa-bed fa-2x"></i>
      <div class="info">
        <h4>Rooms Needing Cleaning</h4>
        <p><b>{{ $stats['rooms_needing_cleaning'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-lg-3">
    <div class="widget-small danger coloured-icon">
      <i class="icon fa fa-exclamation-triangle fa-2x"></i>
      <div class="info">
        <h4>Low Stock Items</h4>
        <p><b>{{ $stats['low_stock_items'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-lg-3">
    <div class="widget-small info coloured-icon">
      <i class="icon fa fa-wrench fa-2x"></i>
      <div class="info">
        <h4>Pending Issues</h4>
        <p><b>{{ $stats['pending_issues'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-lg-3">
    <div class="widget-small success coloured-icon">
      <i class="icon fa fa-check-circle fa-2x"></i>
      <div class="info">
        <h4>Cleaned Today</h4>
        <p><b>{{ $stats['cleaned_today'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
</div>

<!-- All Rooms Overview -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn">
        <h3 class="title"><i class="fa fa-bed"></i> All Rooms Overview</h3>
        <div class="btn-group">
          <button class="btn btn-sm btn-secondary" id="filterAll" onclick="filterRooms('all')">All</button>
          <button class="btn btn-sm btn-warning" id="filterNeedsCleaning" onclick="filterRooms('needs_cleaning')">Needs Cleaning</button>
          <button class="btn btn-sm btn-success" id="filterAvailable" onclick="filterRooms('available')">Available</button>
          <button class="btn btn-sm btn-info" id="filterOccupied" onclick="filterRooms('occupied')">Occupied</button>
        </div>
      </div>
      <div class="tile-body">
        <div class="row" id="roomsGrid">
          @foreach($allRooms as $room)
          @php
            $roomStatus = $room->status;
            $statusClass = '';
            $statusBadge = '';
            $statusIcon = '';
            $cardBgColor = '';
            $cardBorderColor = '';
            
            if ($roomStatus === 'to_be_cleaned' || $roomStatus === 'needs_cleaning') {
              $statusClass = 'needs-cleaning';
              $statusBadge = 'warning';
              $statusIcon = 'fa-exclamation-circle';
              $statusText = 'Needs Cleaning';
              $cardBgColor = '#fff3cd'; // Light yellow background
              $cardBorderColor = '#ffc107'; // Yellow border
            } elseif ($roomStatus === 'occupied' || $room->currentBooking) {
              $statusClass = 'occupied';
              $statusBadge = 'info';
              $statusIcon = 'fa-user';
              $statusText = 'Occupied';
              $cardBgColor = '#d1ecf1'; // Light blue background
              $cardBorderColor = '#17a2b8'; // Blue border
            } elseif ($roomStatus === 'available') {
              $statusClass = 'available';
              $statusBadge = 'success';
              $statusIcon = 'fa-check-circle';
              $statusText = 'Available';
              $cardBgColor = '#d4edda'; // Light green background
              $cardBorderColor = '#28a745'; // Green border
            } elseif ($roomStatus === 'maintenance') {
              $statusClass = 'maintenance';
              $statusBadge = 'danger';
              $statusIcon = 'fa-wrench';
              $statusText = 'Maintenance';
              $cardBgColor = '#f8d7da'; // Light red background
              $cardBorderColor = '#dc3545'; // Red border
            } else {
              $statusClass = 'other';
              $statusBadge = 'secondary';
              $statusIcon = 'fa-bed';
              $statusText = ucfirst(str_replace('_', ' ', $roomStatus));
              $cardBgColor = '#e9ecef'; // Light gray background
              $cardBorderColor = '#6c757d'; // Gray border
            }
            
            // Get room image
            $roomImage = null;
            if ($room->images && is_array($room->images) && count($room->images) > 0) {
              $roomImage = asset('storage/' . $room->images[0]);
            } else {
              $roomImage = asset('dashboard_assets/images/room-placeholder.jpg');
            }
            
            // Get check-in/check-out info
            $checkInTime = null;
            $checkOutTime = null;
            $checkOutDateTime = null;
            $guestName = null;
            $isAboutToCheckOut = false;
            $hoursUntilCheckout = null;
            
            if ($room->currentBooking) {
              $checkInTime = $room->currentBooking->checked_in_at ? \Carbon\Carbon::parse($room->currentBooking->checked_in_at)->format('M d, Y H:i') : ($room->currentBooking->check_in ? \Carbon\Carbon::parse($room->currentBooking->check_in)->format('M d, Y') : null);
              if ($room->currentBooking->check_out) {
                // Parse check-out date and set time to checkout_time if available, otherwise default to 11:00 AM
                $checkOutDate = \Carbon\Carbon::parse($room->currentBooking->check_out);
                if ($room->checkout_time) {
                  $timeParts = explode(':', $room->checkout_time);
                  $checkOutDate->setTime($timeParts[0] ?? 11, $timeParts[1] ?? 0);
                } else {
                  $checkOutDate->setTime(11, 0); // Default checkout time 11:00 AM
                }
                $checkOutDateTime = $checkOutDate;
                $checkOutTime = $checkOutDateTime->format('M d, Y H:i');
                
                // Check if guest is about to check out (within 24 hours or today)
                $now = \Carbon\Carbon::now();
                $hoursUntilCheckout = $now->diffInHours($checkOutDateTime, false);
                $isAboutToCheckOut = $hoursUntilCheckout >= 0 && $hoursUntilCheckout <= 24;
              }
              $guestName = $room->currentBooking->guest_name;
            } elseif ($room->lastCheckout) {
              if ($room->lastCheckout->checked_out_at) {
                $checkOutDateTime = \Carbon\Carbon::parse($room->lastCheckout->checked_out_at);
                $checkOutTime = $checkOutDateTime->format('M d, Y H:i');
              } elseif ($room->lastCheckout->check_out) {
                $checkOutDateTime = \Carbon\Carbon::parse($room->lastCheckout->check_out);
                $checkOutTime = $checkOutDateTime->format('M d, Y');
              }
              $guestName = $room->lastCheckout->guest_name;
            }
            
            // Get last cleaned time
            $lastCleaned = null;
            if ($room->latestCleaningLog && $room->latestCleaningLog->cleaned_at) {
              $lastCleaned = \Carbon\Carbon::parse($room->latestCleaningLog->cleaned_at)->format('M d, Y H:i');
            }
            
            // Check for active issues
            $hasIssues = $room->activeIssues && $room->activeIssues->count() > 0;
            
            // Rooms about to check out get their own distinct color (priority over status)
            if ($isAboutToCheckOut) {
              $cardBgColor = '#ffeaa7'; // Light orange/yellow background
              $cardBorderColor = '#f39c12'; // Orange border
              $statusText = 'Checking Out Soon';
            }
            
            // Override colors if room has issues (highest priority - overrides everything)
            if ($hasIssues) {
              $cardBgColor = '#f8d7da'; // Light red background
              $cardBorderColor = '#dc3545'; // Red border
            }
          @endphp
          <div class="col-md-3 col-sm-6 mb-4 room-card" data-status="{{ $statusClass }}">
            <div class="card room-status-card h-100" 
                 style="box-shadow: 0 2px 8px rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden; background-color: {{ $cardBgColor }}; border: 3px solid {{ $cardBorderColor }} !important;">
              <!-- Room Image -->
              <div class="room-image-container" style="height: 180px; overflow: hidden; position: relative; background: #f0f0f0;">
                <img src="{{ $roomImage }}" alt="Room {{ $room->room_number }}" 
                     style="width: 100%; height: 100%; object-fit: cover;"
                     onerror="this.src='{{ asset('dashboard_assets/images/room-placeholder.jpg') }}'">
                <!-- Status Badge Overlay -->
                <div class="room-status-badge" style="position: absolute; top: 10px; right: 10px;">
                  @if($isAboutToCheckOut)
                  <span class="badge badge-warning badge-lg" style="font-size: 11px; padding: 6px 10px;">
                    <i class="fa fa-clock"></i> {{ $statusText }}
                  </span>
                  @else
                  <span class="badge badge-{{ $statusBadge }} badge-lg" style="font-size: 11px; padding: 6px 10px;">
                    <i class="fa {{ $statusIcon }}"></i> {{ $statusText }}
                  </span>
                  @endif
                </div>
                @if($hasIssues)
                <div style="position: absolute; top: 10px; left: 10px;">
                  <span class="badge badge-danger badge-lg" style="font-size: 11px; padding: 6px 10px;">
                    <i class="fa fa-exclamation-triangle"></i> Issue
                  </span>
                </div>
                @endif
                @if($isAboutToCheckOut)
                <div style="position: absolute; bottom: 10px; left: 10px; right: 10px;">
                  <span class="badge badge-warning badge-lg" style="font-size: 11px; padding: 6px 10px; width: 100%; display: block; text-align: center;">
                    <i class="fa fa-clock"></i> Checking Out Soon
                    @if($hoursUntilCheckout !== null)
                      <br><small>({{ $hoursUntilCheckout > 0 ? $hoursUntilCheckout . ' hours' : 'Today' }})</small>
                    @endif
                  </span>
                </div>
                @endif
              </div>
              
              <!-- Room Info -->
              <div class="card-body" style="padding: 15px;">
                <h5 class="card-title mb-2" style="font-size: 18px; font-weight: bold; color: #333;">
                  <i class="fa fa-bed"></i> Room {{ $room->room_number }}
                </h5>
                <p class="text-muted mb-2" style="font-size: 13px;">
                  <i class="fa fa-tag"></i> {{ $room->room_type }} | 
                  <i class="fa fa-users"></i> {{ $room->capacity }} Guests
                </p>
                
                <!-- Guest Info -->
                @if($guestName)
                <div class="mb-2" style="padding: 8px; background: #f8f9fa; border-radius: 4px;">
                  <small class="text-muted d-block"><i class="fa fa-user"></i> Guest:</small>
                  <strong style="font-size: 13px;">{{ $guestName }}</strong>
                </div>
                @endif
                
                <!-- Check-in/Check-out Times -->
                <div class="room-timeline mb-2" style="font-size: 12px;">
                  @if($checkInTime)
                  <div class="mb-1">
                    <i class="fa fa-sign-in text-success"></i> 
                    <strong>Check-in:</strong> {{ $checkInTime }}
                  </div>
                  @endif
                  @if($checkOutTime)
                  <div class="mb-1">
                    <i class="fa fa-sign-out text-danger"></i> 
                    <strong>Check-out:</strong> <span class="text-danger" style="font-weight: bold;">{{ $checkOutTime }}</span>
                  </div>
                  @endif
                  @if($lastCleaned && !$room->currentBooking)
                  <div class="mb-1">
                    <i class="fa fa-check-circle text-info"></i> 
                    <strong>Last cleaned:</strong> {{ $lastCleaned }}
                  </div>
                  @endif
                </div>
                
                <!-- Actions -->
                <div class="room-actions mt-3">
                  @if(!$isObserver && ($roomStatus === 'to_be_cleaned' || $roomStatus === 'needs_cleaning'))
                  <button class="btn btn-sm btn-success btn-block mark-cleaned-btn" 
                          data-room-id="{{ $room->id }}" 
                          data-room-number="{{ $room->room_number }}">
                    <i class="fa fa-check"></i> Mark Cleaned
                  </button>
                  @endif
                  @if($hasIssues)
                  <a href="{{ route('housekeeper.room-issues') }}" class="btn btn-sm btn-danger btn-block mt-1">
                    <i class="fa fa-exclamation-triangle"></i> View Issues
                  </a>
                  @endif
                  <a href="{{ route('housekeeper.rooms.status') }}" class="btn btn-sm btn-info btn-block mt-1">
                    <i class="fa fa-info-circle"></i> View Details
                  </a>
                </div>
              </div>
            </div>
          </div>
          @endforeach
        </div>
        
        @if($allRooms->count() === 0)
        <div class="text-center" style="padding: 50px;">
          <i class="fa fa-bed fa-4x text-muted mb-3"></i>
          <h3>No Rooms Found</h3>
          <p class="text-muted">No rooms are available in the system.</p>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Rooms Needing Cleaning -->
@if($roomsNeedingCleaning->count() > 0)
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-bed"></i> Rooms Needing Cleaning</h3>
      <div class="tile-body">
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>Room Number</th>
                <th>Room Type</th>
                <th>Last Guest</th>
                <th>Check-out Time</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($roomsNeedingCleaning as $room)
              <tr>
                <td><strong>{{ $room->room_number }}</strong></td>
                <td>{{ $room->room_type }}</td>
                <td>
                  @if($room->bookings->count() > 0)
                    {{ $room->bookings->first()->guest_name }}
                  @else
                    N/A
                  @endif
                </td>
                <td>
                  @if($room->bookings->count() > 0 && $room->bookings->first()->checked_out_at)
                    {{ \Carbon\Carbon::parse($room->bookings->first()->checked_out_at)->format('M d, Y H:i') }}
                  @else
                    N/A
                  @endif
                </td>
                <td>
                  <span class="badge badge-warning">Needs Cleaning</span>
                </td>
                <td>
                  @if(!$isObserver)
                  <button class="btn btn-sm btn-success mark-cleaned-btn" data-room-id="{{ $room->id }}" data-room-number="{{ $room->room_number }}">
                    <i class="fa fa-check"></i> Mark Cleaned
                  </button>
                  @else
                  <span class="text-muted"><i class="fa fa-eye"></i> View Only</span>
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endif

<!-- Low Stock Items -->
@if($lowStockItems->count() > 0)
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-exclamation-triangle text-danger"></i> Low Stock Alert</h3>
      <div class="tile-body">
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>Item Name</th>
                <th>Category</th>
                <th>Current Stock</th>
                <th>Minimum Stock</th>
                <th>Unit</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($lowStockItems as $item)
              <tr>
                <td><strong>{{ $item->name }}</strong></td>
                <td>{{ ucfirst(str_replace('_', ' ', $item->category)) }}</td>
                <td><strong class="text-danger">{{ $item->current_stock }}</strong></td>
                <td>{{ $item->minimum_stock }}</td>
                <td>{{ $item->unit }}</td>
                <td>
                  <span class="badge badge-danger">Low Stock</span>
                </td>
                <td>
                  @if(!$isObserver)
                  <a href="{{ route('housekeeper.purchase-requests.create', ['housekeeping_ids' => $item->id]) }}" class="btn btn-sm btn-primary">
                    <i class="fa fa-shopping-cart"></i> Restock
                  </a>
                  @else
                  <span class="text-muted"><i class="fa fa-eye"></i> View Only</span>
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endif

<!-- Recent Room Issues -->
@if($recentIssues->count() > 0)
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-wrench"></i> Recent Room Issues</h3>
      <div class="tile-body">
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>Room</th>
                <th>Issue Type</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Reported At</th>
              </tr>
            </thead>
            <tbody>
              @foreach($recentIssues as $issue)
              <tr>
                <td><strong>{{ $issue->room->room_number }}</strong></td>
                <td>{{ $issue->issue_type }}</td>
                <td>
                  @if($issue->priority === 'urgent')
                    <span class="badge badge-danger">Urgent</span>
                  @elseif($issue->priority === 'high')
                    <span class="badge badge-warning">High</span>
                  @elseif($issue->priority === 'medium')
                    <span class="badge badge-info">Medium</span>
                  @else
                    <span class="badge badge-secondary">Low</span>
                  @endif
                </td>
                <td>
                  @if($issue->status === 'reported')
                    <span class="badge badge-warning">Reported</span>
                  @elseif($issue->status === 'in_progress')
                    <span class="badge badge-info">In Progress</span>
                  @elseif($issue->status === 'resolved')
                    <span class="badge badge-success">Resolved</span>
                  @endif
                </td>
                <td>{{ $issue->created_at->format('M d, Y H:i') }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endif

<!-- Quick Actions -->
@if(!$isObserver)
<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-bolt"></i> Quick Actions</h3>
      <div class="tile-body">
        <div class="row">
          <div class="col-md-3">
            <a href="{{ route('housekeeper.rooms.cleaning') }}" class="btn btn-warning btn-block">
              <i class="fa fa-bed"></i><br>Rooms Needing Cleaning
            </a>
          </div>
          <div class="col-md-3">
            <a href="{{ route('housekeeper.inventory') }}" class="btn btn-info btn-block">
              <i class="fa fa-cubes"></i><br>Inventory Management
            </a>
          </div>
          <div class="col-md-3">
            <a href="{{ route('housekeeper.room-issues') }}" class="btn btn-danger btn-block">
              <i class="fa fa-wrench"></i><br>Room Issues
            </a>
          </div>
          <div class="col-md-3">
            <a href="{{ route('housekeeper.purchase-requests.create') }}" class="btn btn-primary btn-block">
              <i class="fa fa-shopping-cart"></i><br>Request Purchase
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endif

<!-- Mark Room Cleaned Modal -->
<div class="modal fade" id="markCleanedModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-check-circle"></i> Mark Room as Cleaned</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="markCleanedForm">
        <div class="modal-body">
          <input type="hidden" id="room_id" name="room_id">
          <p>Are you sure you want to mark <strong id="room_number_display"></strong> as cleaned?</p>
          <div class="form-group">
            <label for="notes">Notes (Optional)</label>
            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Add any notes about the cleaning..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">
            <i class="fa fa-check"></i> Mark as Cleaned
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('styles')
<style>
.room-card {
  transition: transform 0.2s, box-shadow 0.2s;
}
.room-card:hover {
  transform: translateY(-5px);
}
.room-status-card {
  transition: all 0.3s ease;
}
.room-status-card:hover {
  box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
}
.room-image-container {
  position: relative;
}
.room-image-container::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 30px;
  background: linear-gradient(to top, rgba(0,0,0,0.1), transparent);
}
.badge-lg {
  font-size: 11px;
  padding: 6px 10px;
  font-weight: 600;
}
.room-timeline {
  border-left: 2px solid #e0e0e0;
  padding-left: 10px;
  margin-left: 5px;
}
.room-timeline div {
  margin-bottom: 5px;
}
.filter-active {
  background-color: #007bff !important;
  color: white !important;
}
</style>
@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<script>
$(document).ready(function() {
    // Room filtering
    window.filterRooms = function(status) {
        $('.room-card').each(function() {
            var roomStatus = $(this).data('status');
            if (status === 'all' || roomStatus === status) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        
        // Update button states
        $('.btn-group button').removeClass('filter-active');
        if (status === 'all') {
            $('#filterAll').addClass('filter-active');
        } else if (status === 'needs_cleaning') {
            $('#filterNeedsCleaning').addClass('filter-active');
        } else if (status === 'available') {
            $('#filterAvailable').addClass('filter-active');
        } else if (status === 'occupied') {
            $('#filterOccupied').addClass('filter-active');
        }
    };
    
    // Set initial filter
    filterRooms('all');
    
    // Mark cleaned functionality
    $(document).on('click', '.mark-cleaned-btn', function() {
        var roomId = $(this).data('room-id');
        var roomNumber = $(this).data('room-number');
        
        $('#room_id').val(roomId);
        $('#room_number_display').text(roomNumber);
        $('#markCleanedModal').modal('show');
    });
    
    $('#markCleanedForm').on('submit', function(e) {
        e.preventDefault();
        
        var roomId = $('#room_id').val();
        var notes = $('#notes').val();
        
        $.ajax({
            url: '/housekeeper/rooms/' + roomId + '/mark-cleaned',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                notes: notes
            },
            success: function(response) {
                if (response.success) {
                    swal({
                        title: "Success!",
                        text: response.message,
                        type: "success",
                        timer: 2000,
                        showConfirmButton: false
                    });
                    $('#markCleanedModal').modal('hide');
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                }
            },
            error: function(xhr) {
                var errorMsg = xhr.responseJSON?.message || 'Failed to mark room as cleaned.';
                swal("Error!", errorMsg, "error");
            }
        });
    });
});
</script>
@endsection
