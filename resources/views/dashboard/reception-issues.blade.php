@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-exclamation-triangle"></i> Issue Reports</h1>
    <p>Manage and track all guest issues</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('reception.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Issues</a></li>
  </ul>
</div>

<!-- Statistics Cards -->
<div class="row mb-3">
  <div class="col-md-3 col-lg-3">
    <div class="widget-small primary coloured-icon">
      <i class="icon fa fa-list fa-2x"></i>
      <div class="info">
        <h4>Total Issues</h4>
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
    <div class="widget-small info coloured-icon">
      <i class="icon fa fa-cog fa-2x"></i>
      <div class="info">
        <h4>In Progress</h4>
        <p><b>{{ $stats['in_progress'] }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-lg-3">
    <div class="widget-small danger coloured-icon">
      <i class="icon fa fa-exclamation fa-2x"></i>
      <div class="info">
        <h4>Urgent</h4>
        <p><b>{{ $stats['urgent'] }}</b></p>
      </div>
    </div>
  </div>
</div>

<!-- Issues Table -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-list"></i> All Issues</h3>
      <div class="tile-body">
        <!-- Filters -->
        <div class="row mb-3">
          <div class="col-md-3">
            <select class="form-control" id="statusFilter" onchange="filterIssues()">
              <option value="all">All Statuses</option>
              <option value="pending">Pending</option>
              <option value="in_progress">In Progress</option>
              <option value="resolved">Resolved</option>
            </select>
          </div>
          <div class="col-md-3">
            <select class="form-control" id="priorityFilter" onchange="filterIssues()">
              <option value="all">All Priorities</option>
              <option value="urgent">Urgent</option>
              <option value="high">High</option>
              <option value="medium">Medium</option>
              <option value="low">Low</option>
            </select>
          </div>
          <div class="col-md-4">
            <input type="text" class="form-control" id="searchInput" placeholder="Search by subject, description, or guest name..." onkeyup="filterIssues()">
          </div>
          <div class="col-md-2">
            <button class="btn btn-secondary btn-block" onclick="resetIssueFilters()">
              <i class="fa fa-refresh"></i> Reset
            </button>
          </div>
        </div>

        @if($issues->count() > 0)
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>Issue ID</th>
                <th>Guest</th>
                <th>Subject</th>
                <th>Type</th>
                <th>Priority</th>
                <th>Room/Booking</th>
                <th>Status</th>
                <th>Reported</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($issues as $issue)
              <tr class="issue-row {{ $issue->priority === 'urgent' && $issue->status !== 'resolved' ? 'table-danger' : '' }}"
                  data-status="{{ $issue->status }}"
                  data-priority="{{ $issue->priority }}"
                  data-subject="{{ strtolower($issue->subject) }}"
                  data-description="{{ strtolower($issue->description) }}"
                  data-guest-name="{{ strtolower($issue->getReporter()->name ?? '') }}">
                <td><strong>#{{ $issue->id }}</strong></td>
                <td>
                  @php $reporter = $issue->getReporter(); @endphp
                  <strong>{{ $reporter->name ?? 'N/A' }}</strong><br>
                  <small>{{ $reporter->email ?? 'N/A' }}</small>
                </td>
                <td>
                  <strong>{{ $issue->subject }}</strong>
                  @if(strlen($issue->description) > 50)
                    <br><small class="text-muted">{{ substr($issue->description, 0, 50) }}...</small>
                  @endif
                </td>
                <td>
                  @if($issue->issue_type === 'room_issue')
                    <span class="badge badge-info">Room Issue</span>
                  @elseif($issue->issue_type === 'service_issue')
                    <span class="badge badge-primary">Service Issue</span>
                  @elseif($issue->issue_type === 'technical_issue')
                    <span class="badge badge-warning">Technical Issue</span>
                  @else
                    <span class="badge badge-secondary">Other</span>
                  @endif
                </td>
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
                  @if($issue->booking_id && $issue->booking)
                    <a href="{{ route('reception.bookings.show', $issue->booking_id) }}" target="_blank">
                      {{ $issue->booking->booking_reference }}
                    </a><br>
                    <small>{{ $issue->room->room_number ?? 'N/A' }}</small>
                  @elseif($issue->room)
                    <small>{{ $issue->room->room_number ?? 'N/A' }}</small>
                  @else
                    <small class="text-muted">N/A</small>
                  @endif
                </td>
                <td>
                  @if($issue->status === 'pending')
                    <span class="badge badge-warning">Pending</span>
                  @elseif($issue->status === 'in_progress')
                    <span class="badge badge-info">In Progress</span>
                  @elseif($issue->status === 'resolved')
                    <span class="badge badge-success">Resolved</span>
                  @else
                    <span class="badge badge-secondary">{{ ucfirst($issue->status) }}</span>
                  @endif
                </td>
                <td>
                  <small>{{ $issue->created_at->format('M d, Y') }}</small><br>
                  <small class="text-muted">{{ $issue->created_at->format('H:i') }}</small>
                </td>
                <td>
                  <div class="btn-group" role="group">
                    @if($issue->status !== 'resolved')
                    <div class="btn-group" role="group">
                      <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-edit"></i> Change Status
                      </button>
                      <div class="dropdown-menu">
                        <a class="dropdown-item" href="#" onclick="updateIssueStatus({{ $issue->id }}, 'pending'); return false;">
                          <i class="fa fa-clock-o"></i> Set to Pending
                        </a>
                        <a class="dropdown-item" href="#" onclick="updateIssueStatus({{ $issue->id }}, 'in_progress'); return false;">
                          <i class="fa fa-cog"></i> Set to In Progress
                        </a>
                        <a class="dropdown-item" href="#" onclick="updateIssueStatus({{ $issue->id }}, 'resolved'); return false;">
                          <i class="fa fa-check-circle"></i> Mark as Resolved
                        </a>
                      </div>
                    </div>
                    @endif
                    <a href="{{ route('reception.issues.show', $issue) }}" class="btn btn-sm btn-info" title="View Details">
                      <i class="fa fa-eye"></i> View
                    </a>
                  </div>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-center">
          {{ $issues->links() }}
        </div>
        @else
        <div class="text-center" style="padding: 40px;">
          <i class="fa fa-check-circle fa-3x text-success mb-3"></i>
          <h4>No Issues Found</h4>
          <p class="text-muted">No issues match your current filters.</p>
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
function updateIssueStatus(issueId, status) {
    const statusLabels = {
        'pending': 'Pending',
        'in_progress': 'In Progress',
        'resolved': 'Resolved'
    };
    
    swal({
        title: "Update Status?",
        text: `Are you sure you want to change the status to "${statusLabels[status]}"?`,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        confirmButtonText: "Yes, update it!",
        cancelButtonText: "Cancel",
        closeOnConfirm: false
    }, function(isConfirm) {
        if (isConfirm) {
            fetch(`/reception/issues/${issueId}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    status: status,
                    admin_notes: ''
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    swal({
                        title: "Success!",
                        text: data.message || "Issue status updated successfully.",
                        type: "success",
                        confirmButtonColor: "#28a745"
                    }, function() {
                        location.reload();
                    });
                } else {
                    swal("Error!", data.message || "An error occurred. Please try again.", "error");
                }
            })
            .catch(error => {
                console.error('Error:', error);
                swal("Error!", "An error occurred. Please try again.", "error");
            });
        }
    });
}

function filterIssues() {
  const statusFilter = document.getElementById('statusFilter').value;
  const priorityFilter = document.getElementById('priorityFilter').value;
  const searchInput = document.getElementById('searchInput').value.toLowerCase();
  
  const rows = document.querySelectorAll('.issue-row');
  rows.forEach(row => {
    const status = row.getAttribute('data-status');
    const priority = row.getAttribute('data-priority');
    const subject = row.getAttribute('data-subject');
    const description = row.getAttribute('data-description');
    const guestName = row.getAttribute('data-guest-name');
    
    let show = true;
    
    // Status filter
    if (statusFilter !== 'all' && status !== statusFilter) {
      show = false;
    }
    
    // Priority filter
    if (priorityFilter !== 'all' && priority !== priorityFilter) {
      show = false;
    }
    
    // Search filter
    if (searchInput) {
      if (!subject.includes(searchInput) && 
          !description.includes(searchInput) && 
          !guestName.includes(searchInput)) {
        show = false;
      }
    }
    
    row.style.display = show ? '' : 'none';
  });
}

function resetIssueFilters() {
  document.getElementById('statusFilter').value = 'all';
  document.getElementById('priorityFilter').value = 'all';
  document.getElementById('searchInput').value = '';
  filterIssues();
}
</script>
@endsection

