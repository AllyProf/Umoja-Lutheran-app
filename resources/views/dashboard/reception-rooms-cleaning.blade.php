@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-broom"></i> Rooms Needs Cleaning</h1>
    <p>Manage rooms that need cleaning after guest checkout</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ $role === 'manager' ? route('admin.dashboard') : route('reception.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Rooms Cleaning</a></li>
  </ul>
</div>

<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn mb-3">
        <h3 class="title">Rooms That Need Cleaning</h3>
      </div>
      
      <!-- Search Filter -->
      <div class="row mb-3">
        <div class="col-md-12">
          <form method="GET" action="{{ $role === 'manager' ? route('admin.rooms.cleaning') : route('reception.rooms.cleaning') }}" class="form-inline">
            <div class="form-group mr-2">
              <input type="text" name="search" class="form-control" placeholder="Search by room number or type..." value="{{ request('search') }}" style="width: 300px;">
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
            <a href="{{ $role === 'manager' ? route('admin.rooms.cleaning') : route('reception.rooms.cleaning') }}" class="btn btn-secondary ml-2">Reset</a>
          </form>
        </div>
      </div>
      
      <div class="tile-body">
        @if($rooms->count() > 0)
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>Room Number</th>
                <th>Room Type</th>
                <th>Capacity</th>
                <th>Last Guest</th>
                <th>Checked Out At</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($rooms as $room)
              @php
                // Find booking checking out tomorrow (if any)
                $tomorrow = \Carbon\Carbon::today()->addDay();
                $upcomingCheckout = $room->bookings->filter(function($booking) use ($tomorrow) {
                    return $booking->check_in_status === 'checked_in' && 
                           \Carbon\Carbon::parse($booking->check_out)->format('Y-m-d') === $tomorrow->format('Y-m-d');
                })->first();
                
                // Find last checked out booking (if any)
                $lastCheckedOut = $room->bookings->filter(function($booking) {
                    return $booking->check_in_status === 'checked_out';
                })->sortByDesc('checked_out_at')->first();
                
                $displayBooking = $upcomingCheckout ?? $lastCheckedOut;
                $isUpcoming = $upcomingCheckout !== null;
              @endphp
              <tr>
                <td><strong>{{ $room->room_number }}</strong></td>
                <td>
                  <span class="badge badge-primary">{{ $room->room_type }}</span>
                </td>
                <td>{{ $room->capacity }} guests</td>
                <td>
                  @if($displayBooking)
                    <strong>{{ $displayBooking->guest_name }}</strong><br>
                    <small>{{ $displayBooking->booking_reference }}</small>
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td>
                  @if($isUpcoming && $displayBooking)
                    <span class="badge badge-info">Check-out: {{ \Carbon\Carbon::parse($displayBooking->check_out)->format('M d, Y') }}</span>
                    <br><small class="text-muted">Tomorrow</small>
                  @elseif($displayBooking && $displayBooking->checked_out_at)
                    {{ $displayBooking->checked_out_at->format('M d, Y H:i') }}
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td>
                  @if($isUpcoming)
                    <span class="badge badge-info">Upcoming Check-out</span>
                  @else
                    <span class="badge badge-warning">Needs Cleaning</span>
                  @endif
                </td>
                <td>
                  @if($isUpcoming)
                    <span class="text-muted small"><i class="fa fa-clock-o"></i> Waiting for check-out</span>
                  @else
                    @if($role === 'reception')
                      <span class="badge badge-secondary p-1"><i class="fa fa-eye"></i> View Only</span>
                    @else
                      <button onclick="markRoomCleaned({{ $room->id }}, '{{ $room->room_number }}')" class="btn btn-sm btn-success" title="Mark as Cleaned">
                        <i class="fa fa-check"></i> Done
                      </button>
                    @endif
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        
        <div class="d-flex justify-content-center mt-3">
          {{ $rooms->links() }}
        </div>
        @else
        <div class="text-center" style="padding: 50px;">
          <i class="fa fa-broom fa-5x text-muted mb-3"></i>
          <h3>No Rooms Need Cleaning</h3>
          <p class="text-muted">All rooms are clean and ready for guests.</p>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<script>
function markRoomCleaned(roomId, roomNumber) {
    swal({
        title: "Mark Room as Cleaned?",
        text: "Are you sure room " + roomNumber + " has been cleaned and is ready for booking?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, Mark as Cleaned!",
        cancelButtonText: "Cancel",
        closeOnConfirm: false,
        showLoaderOnConfirm: true
    }, function(isConfirm) {
        if (isConfirm) {
            const baseUrl = '{{ $role === 'manager' ? url("/manager/rooms") : url("/reception/rooms") }}';
            const url = baseUrl + '/' + roomId + '/mark-cleaned';
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    swal({
                        title: "Success!",
                        text: data.message || "Room marked as cleaned successfully!",
                        type: "success",
                        confirmButtonColor: "#28a745"
                    }, function() {
                        location.reload();
                    });
                } else {
                    swal({
                        title: "Error!",
                        text: data.message || "Failed to mark room as cleaned. Please try again.",
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
</script>
@endsection



