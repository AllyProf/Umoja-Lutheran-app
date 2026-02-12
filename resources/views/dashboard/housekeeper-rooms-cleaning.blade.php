@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-bed"></i> Rooms Needing Cleaning</h1>
    <p>Manage rooms that require cleaning</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('housekeeper.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Rooms Cleaning</a></li>
  </ul>
</div>

@if($rooms->count() > 0)
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-bed"></i> Rooms Needing Cleaning ({{ $rooms->count() }})</h3>
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
              @foreach($rooms as $room)
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
                  <button class="btn btn-sm btn-success mark-cleaned-btn" data-room-id="{{ $room->id }}" data-room-number="{{ $room->room_number }}">
                    <i class="fa fa-check"></i> Mark Cleaned
                  </button>
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
@else
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-body text-center" style="padding: 50px;">
        <i class="fa fa-check-circle fa-4x text-success mb-3"></i>
        <h3>All Rooms Are Clean!</h3>
        <p class="text-muted">There are no rooms currently needing cleaning.</p>
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

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<script>
$(document).ready(function() {
    $('.mark-cleaned-btn').on('click', function() {
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
