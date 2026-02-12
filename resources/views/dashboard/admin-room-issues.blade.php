@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-wrench"></i> Room Maintenance Issues</h1>
    <p>Monitor and resolve room maintenance requests</p>
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
    <li class="breadcrumb-item"><a href="#">Room Issues</a></li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn">
        <h3 class="title"><i class="fa fa-list"></i> Maintenance Log</h3>
      </div>
      <div class="tile-body">
        @if($issues->count() > 0)
        <div class="table-responsive">
          <table class="table table-hover table-bordered" id="sampleTable">
            <thead>
              <tr>
                <th>Room</th>
                <th>Issue Type</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Reported By</th>
                <th>Reported At</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($issues as $issue)
              <tr>
                <td><strong>Room {{ $issue->room->room_number }}</strong></td>
                <td>
                    <span class="font-weight-bold">{{ $issue->issue_type }}</span><br>
                    <small class="text-muted">{{ $issue->description }}</small>
                </td>
                <td>
                  @if($issue->priority === 'urgent')
                    <span class="badge badge-danger">Urgent</span>
                  @elseif($issue->priority === 'high')
                    <span class="badge badge-warning text-dark">High</span>
                  @elseif($issue->priority === 'medium')
                    <span class="badge badge-info text-white">Medium</span>
                  @else
                    <span class="badge badge-secondary">Low</span>
                  @endif
                </td>
                <td>
                  @if($issue->status === 'reported')
                    <span class="badge badge-warning text-dark"><i class="fa fa-exclamation-circle"></i> Reported</span>
                  @elseif($issue->status === 'in_progress')
                    <span class="badge badge-info text-white"><i class="fa fa-spinner fa-spin"></i> In Progress</span>
                  @elseif($issue->status === 'resolved')
                    <span class="badge badge-success"><i class="fa fa-check-circle"></i> Resolved</span>
                  @elseif($issue->status === 'cancelled')
                    <span class="badge badge-secondary">Cancelled</span>
                  @endif
                </td>
                <td>{{ $issue->reportedBy->name ?? 'N/A' }}</td>
                <td>{{ $issue->created_at->format('M d, Y H:i') }}</td>
                <td>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-info update-status-btn" 
                                data-id="{{ $issue->id }}" 
                                data-current-status="{{ $issue->status }}"
                                title="Update Status">
                            <i class="fa fa-edit"></i>
                        </button>
                    </div>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="mt-3">
          {{ $issues->links() }}
        </div>
        @else
        <div class="text-center py-5">
          <i class="fa fa-check-circle fa-4x text-success mb-3"></i>
          <h3>No Pending Issues</h3>
          <p class="text-muted">All guest rooms are reported to be in good condition.</p>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Edit Status Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title"><i class="fa fa-edit"></i> Update Maintenance Status</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="updateStatusForm">
        @csrf
        <input type="hidden" id="issue_id" name="issue_id">
        <div class="modal-body">
          <div class="form-group">
            <label for="status"><strong>Maintenance Status</strong></label>
            <select class="form-control" id="status" name="status" required>
              <option value="reported">Reported (New)</option>
              <option value="in_progress">In Progress (Repairing)</option>
              <option value="resolved">Resolved (Complete)</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>
          <div class="alert alert-info">
              <i class="fa fa-info-circle"></i> Setting status to <strong>Resolved</strong> will mark the maintenance as complete.
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
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
    $('.update-status-btn').on('click', function() {
        var id = $(this).data('id');
        var status = $(this).data('current-status');
        
        $('#issue_id').val(id);
        $('#status').val(status);
        $('#statusModal').modal('show');
    });

    $('#updateStatusForm').on('submit', function(e) {
        e.preventDefault();
        var id = $('#issue_id').val();
        
        $.ajax({
            url: '/manager/room-issues/' + id + '/update-status',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    swal({
                        title: "Updated!",
                        text: response.message,
                        type: "success",
                        timer: 1500,
                        showConfirmButton: false
                    });
                    $('#statusModal').modal('hide');
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                }
            },
            error: function(xhr) {
                swal("Error!", "Failed to update status", "error");
            }
        });
    });
});
</script>
@endsection
