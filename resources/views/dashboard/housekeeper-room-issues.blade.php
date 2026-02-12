@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-wrench"></i> Room Issues</h1>
    <p>Report and manage room maintenance issues</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('housekeeper.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Room Issues</a></li>
  </ul>
</div>

<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn">
        <h3 class="title"><i class="fa fa-wrench"></i> Room Issues</h3>
        <p><button class="btn btn-primary icon-btn" data-toggle="modal" data-target="#reportIssueModal"><i class="fa fa-plus"></i>Report Issue</button></p>
      </div>
      <div class="tile-body">
        @if($issues->count() > 0)
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>Room</th>
                <th>Issue Type</th>
                <th>Description</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Reported By</th>
                <th>Reported At</th>
              </tr>
            </thead>
            <tbody>
              @foreach($issues as $issue)
              <tr>
                <td><strong>{{ $issue->room->room_number }}</strong></td>
                <td>{{ $issue->issue_type }}</td>
                <td>{{ Str::limit($issue->description, 50) }}</td>
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
                  @elseif($issue->status === 'cancelled')
                    <span class="badge badge-secondary">Cancelled</span>
                  @endif
                </td>
                <td>{{ $issue->reportedBy->name }}</td>
                <td>{{ $issue->created_at->format('M d, Y H:i') }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        
        <div class="mt-3">
          {{ $issues->links() }}
        </div>
        @else
        <div class="text-center" style="padding: 50px;">
          <i class="fa fa-check-circle fa-4x text-success mb-3"></i>
          <h3>No Issues Reported</h3>
          <p class="text-muted">All rooms are in good condition.</p>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Report Issue Modal -->
<div class="modal fade" id="reportIssueModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-plus-circle"></i> Report Room Issue</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="reportIssueForm">
        <div class="modal-body">
          <div class="form-group">
            <label for="room_id">Room <span class="text-danger">*</span></label>
            <select class="form-control" id="room_id" name="room_id" required>
              <option value="">Select Room</option>
              @foreach(\App\Models\Room::orderBy('room_number')->get() as $room)
              <option value="{{ $room->id }}">{{ $room->room_number }} ({{ $room->room_type }})</option>
              @endforeach
            </select>
          </div>
          
          <div class="form-group">
            <label for="issue_type">Issue Type <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="issue_type" name="issue_type" required placeholder="e.g., Bulb Problem, AC Issue, Plumbing">
          </div>
          
          <div class="form-group">
            <label for="description">Description <span class="text-danger">*</span></label>
            <textarea class="form-control" id="description" name="description" rows="4" required placeholder="Describe the issue in detail..."></textarea>
          </div>
          
          <div class="form-group">
            <label for="priority">Priority <span class="text-danger">*</span></label>
            <select class="form-control" id="priority" name="priority" required>
              <option value="low">Low</option>
              <option value="medium" selected>Medium</option>
              <option value="high">High</option>
              <option value="urgent">Urgent</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="fa fa-paper-plane"></i> Report Issue
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
    $('#reportIssueForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = {
            _token: '{{ csrf_token() }}',
            room_id: $('#room_id').val(),
            issue_type: $('#issue_type').val(),
            description: $('#description').val(),
            priority: $('#priority').val()
        };
        
        $.ajax({
            url: '/housekeeper/room-issues/report',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    swal({
                        title: "Success!",
                        text: response.message,
                        type: "success",
                        timer: 2000,
                        showConfirmButton: false
                    });
                    $('#reportIssueModal').modal('hide');
                    $('#reportIssueForm')[0].reset();
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                }
            },
            error: function(xhr) {
                var errorMsg = xhr.responseJSON?.message || 'Failed to report issue.';
                swal("Error!", errorMsg, "error");
            }
        });
    });
});
</script>
@endsection
