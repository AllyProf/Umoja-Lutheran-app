@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-exclamation-triangle"></i> Issue Details</h1>
    <p>View and manage issue report</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('reception.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('reception.issues.index') }}">Issues</a></li>
    <li class="breadcrumb-item"><a href="#">Issue #{{ $issue->id }}</a></li>
  </ul>
</div>

<div class="row">
  <div class="col-md-8">
    <div class="tile">
      <div class="tile-title-w-btn">
        <h3 class="title"><i class="fa fa-info-circle"></i> Issue Information</h3>
        <div>
          <a href="{{ route('reception.issues.index') }}" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> Back to Issues
          </a>
        </div>
      </div>
      <div class="tile-body">
        <div class="row mb-3">
          <div class="col-md-6">
            <strong>Issue ID:</strong><br>
            <span class="badge badge-primary">#{{ $issue->id }}</span>
          </div>
          <div class="col-md-6">
            <strong>Status:</strong><br>
            @if($issue->status === 'pending')
              <span class="badge badge-warning">Pending</span>
            @elseif($issue->status === 'in_progress')
              <span class="badge badge-info">In Progress</span>
            @elseif($issue->status === 'resolved')
              <span class="badge badge-success">Resolved</span>
            @else
              <span class="badge badge-secondary">{{ ucfirst($issue->status) }}</span>
            @endif
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <strong>Issue Type:</strong><br>
            @if($issue->issue_type === 'room_issue')
              <span class="badge badge-info">Room Issue</span>
            @elseif($issue->issue_type === 'service_issue')
              <span class="badge badge-primary">Service Issue</span>
            @elseif($issue->issue_type === 'technical_issue')
              <span class="badge badge-warning">Technical Issue</span>
            @else
              <span class="badge badge-secondary">Other</span>
            @endif
          </div>
          <div class="col-md-6">
            <strong>Priority:</strong><br>
            @if($issue->priority === 'urgent')
              <span class="badge badge-danger">Urgent</span>
            @elseif($issue->priority === 'high')
              <span class="badge badge-warning">High</span>
            @elseif($issue->priority === 'medium')
              <span class="badge badge-info">Medium</span>
            @else
              <span class="badge badge-secondary">Low</span>
            @endif
          </div>
        </div>

        <div class="mb-3">
          <strong>Subject:</strong>
          <p class="mt-2">{{ $issue->subject }}</p>
        </div>

        <div class="mb-3">
          <strong>Description:</strong>
          <p class="mt-2" style="white-space: pre-wrap;">{{ $issue->description }}</p>
        </div>

        <div class="row">
          <div class="col-md-6">
            <strong>Reported At:</strong><br>
            <small>{{ $issue->created_at->format('F d, Y \a\t H:i') }}</small>
          </div>
          @if($issue->resolved_at)
          <div class="col-md-6">
            <strong>Resolved At:</strong><br>
            <small class="text-success">{{ $issue->resolved_at->format('F d, Y \a\t H:i') }}</small>
          </div>
          @endif
        </div>
      </div>
    </div>

    <!-- Update Status Form -->
    @if($issue->status !== 'resolved')
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-edit"></i> Update Issue Status</h3>
      <div class="tile-body">
        <form id="updateIssueForm">
          @csrf
          <div class="form-group">
            <label for="status">Status *</label>
            <select class="form-control" id="status" name="status" required>
              <option value="pending" {{ $issue->status === 'pending' ? 'selected' : '' }}>Pending</option>
              <option value="in_progress" {{ $issue->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
              <option value="resolved" {{ $issue->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
            </select>
          </div>
          <div class="form-group">
            <label for="admin_notes">Staff Notes</label>
            <textarea class="form-control" id="admin_notes" name="admin_notes" rows="4" placeholder="Add notes about the issue resolution or status update...">{{ $issue->admin_notes }}</textarea>
            <small class="form-text text-muted">These notes will be visible to the guest.</small>
          </div>
          <div id="updateAlert"></div>
          <button type="submit" class="btn btn-primary">
            <i class="fa fa-save"></i> Update Issue
          </button>
        </form>
      </div>
    </div>
    @endif
  </div>

  <div class="col-md-4">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-info"></i> Guest Information</h3>
      <div class="tile-body">
        @php
          $reporter = $issue->getReporter();
        @endphp
        <div class="mb-3">
          <strong>Guest Name:</strong><br>
          <span>{{ $reporter->name ?? 'N/A' }}</span>
        </div>
        <div class="mb-3">
          <strong>Email:</strong><br>
          <small>{{ $reporter->email ?? 'N/A' }}</small>
        </div>
        @if($reporter && ($reporter->phone ?? null))
        <div class="mb-3">
          <strong>Phone:</strong><br>
          <small>{{ $reporter->phone }}</small>
        </div>
        @endif
      </div>
    </div>

    @if($issue->booking)
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-calendar"></i> Booking Information</h3>
      <div class="tile-body">
        <div class="mb-3">
          <strong>Booking Reference:</strong><br>
          <a href="{{ route('admin.bookings.show', $issue->booking_id) }}" target="_blank">
            {{ $issue->booking->booking_reference }}
          </a>
        </div>
        @if($issue->room)
        <div class="mb-3">
          <strong>Room:</strong><br>
          <span class="badge badge-primary">{{ $issue->room->room_number ?? 'N/A' }}</span>
          @if($issue->room->room_type)
            <br><small>{{ $issue->room->room_type }}</small>
          @endif
        </div>
        @endif
      </div>
    </div>
    @endif

    @if($issue->admin_notes)
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-comment"></i> Staff Notes</h3>
      <div class="tile-body">
        <p style="white-space: pre-wrap;">{{ $issue->admin_notes }}</p>
      </div>
    </div>
    @endif
  </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<script>
document.getElementById('updateIssueForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    const alertDiv = document.getElementById('updateAlert');
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Updating...';
    
    if (alertDiv) {
        alertDiv.innerHTML = '';
    }
    
    fetch('{{ route("reception.issues.update", $issue) }}', {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            status: document.getElementById('status').value,
            admin_notes: document.getElementById('admin_notes').value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            swal({
                title: "Success!",
                text: data.message || "Issue updated successfully.",
                type: "success",
                confirmButtonColor: "#28a745"
            }, function() {
                location.reload();
            });
        } else {
            let errorMsg = data.message || 'An error occurred. Please try again.';
            if (alertDiv) {
                alertDiv.innerHTML = '<div class="alert alert-danger">' + errorMsg + '</div>';
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (alertDiv) {
            alertDiv.innerHTML = '<div class="alert alert-danger">An error occurred. Please try again.</div>';
        }
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});
</script>
@endsection





