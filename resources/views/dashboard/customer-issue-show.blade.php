@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-exclamation-triangle"></i> Issue Details</h1>
    <p>View issue report details</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('customer.issues.index') }}">My Issues</a></li>
    <li class="breadcrumb-item"><a href="#">Issue #{{ $issue->id }}</a></li>
  </ul>
</div>

<div class="row">
  <div class="col-md-8">
    <div class="tile">
      <div class="tile-title-w-btn">
        <h3 class="title"><i class="fa fa-info-circle"></i> Issue Information</h3>
        <div>
          <a href="{{ route('customer.issues.index') }}" class="btn btn-secondary">
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

        @if($issue->admin_notes)
        <div class="mb-3" style="background-color: #f8f9fa; padding: 15px; border-left: 4px solid #007bff; border-radius: 4px;">
          <strong><i class="fa fa-comment"></i> Staff Notes:</strong>
          <p class="mt-2" style="white-space: pre-wrap;">{{ $issue->admin_notes }}</p>
        </div>
        @endif

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
  </div>

  <div class="col-md-4">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-info"></i> Related Information</h3>
      <div class="tile-body">
        @if($issue->booking)
        <div class="mb-3">
          <strong>Booking Reference:</strong><br>
          <a href="{{ route('customer.my-bookings') }}" target="_blank">
            {{ $issue->booking->booking_reference }}
          </a>
        </div>
        @endif

        @if($issue->room)
        <div class="mb-3">
          <strong>Room:</strong><br>
          <span class="badge badge-primary">{{ $issue->room->room_number ?? 'N/A' }}</span>
          @if($issue->room->room_type)
            <br><small>{{ $issue->room->room_type }}</small>
          @endif
        </div>
        @endif

        <div class="mb-3">
          <strong>Reported By:</strong><br>
          @php $reporter = $issue->getReporter(); @endphp
          <small>{{ $reporter->name ?? 'N/A' }}</small><br>
          <small class="text-muted">{{ $reporter->email ?? 'N/A' }}</small>
        </div>
      </div>
    </div>

    @if($issue->status !== 'resolved')
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-question-circle"></i> Need Help?</h3>
      <div class="tile-body">
        <p>If you have additional information about this issue or need to follow up, please contact reception.</p>
        <a href="{{ route('customer.dashboard') }}" class="btn btn-primary btn-block" onclick="openReportIssueModal(); return false;">
          <i class="fa fa-plus"></i> Report Another Issue
        </a>
      </div>
    </div>
    @endif
  </div>
</div>

@endsection

@section('styles')
<style>
  /* Mobile Responsive Styles */
  @media (max-width: 768px) {
    /* Main Layout */
    .col-md-8,
    .col-md-4 {
      flex: 0 0 100%;
      max-width: 100%;
      margin-bottom: 20px;
    }
    
    /* Tile Title */
    .tile-title-w-btn {
      flex-direction: column;
      align-items: flex-start !important;
      gap: 15px;
    }
    
    .tile-title-w-btn .title {
      font-size: 18px;
      margin-bottom: 0;
    }
    
    .tile-title-w-btn .btn {
      width: 100%;
      font-size: 14px;
      padding: 10px 15px;
    }
    
    /* Tile Title (sidebar) */
    .tile-title {
      font-size: 16px;
    }
    
    /* Info Rows */
    .col-md-6 {
      flex: 0 0 100%;
      max-width: 100%;
      margin-bottom: 15px;
    }
    
    .row.mb-3 {
      margin-bottom: 20px;
    }
    
    /* Badges */
    .badge {
      font-size: 12px;
      padding: 6px 10px;
      display: inline-block;
      margin-top: 5px;
    }
    
    /* Text Content */
    .mb-3 strong {
      font-size: 14px;
      display: block;
      margin-bottom: 8px;
    }
    
    .mb-3 p {
      font-size: 13px;
      line-height: 1.6;
      margin-top: 8px;
    }
    
    .mb-3 small {
      font-size: 12px;
    }
    
    /* Staff Notes Box */
    .mb-3[style*="background-color"] {
      padding: 12px !important;
      margin-bottom: 20px;
    }
    
    .mb-3[style*="background-color"] strong {
      font-size: 14px;
    }
    
    .mb-3[style*="background-color"] p {
      font-size: 13px;
      margin-top: 10px;
    }
    
    /* Related Information Sidebar */
    .col-md-4 .tile {
      margin-bottom: 20px;
    }
    
    .col-md-4 .mb-3 {
      margin-bottom: 20px;
      padding-bottom: 15px;
      border-bottom: 1px solid #eee;
    }
    
    .col-md-4 .mb-3:last-child {
      border-bottom: none;
    }
    
    .col-md-4 strong {
      font-size: 13px;
      display: block;
      margin-bottom: 8px;
    }
    
    .col-md-4 small {
      font-size: 12px;
      display: block;
      margin-bottom: 5px;
    }
    
    /* Buttons */
    .btn-block {
      width: 100%;
      font-size: 14px;
      padding: 12px 20px;
    }
    
    /* Links */
    a {
      word-break: break-word;
    }
  }
  
  /* Small Mobile Devices */
  @media (max-width: 480px) {
    .tile-title-w-btn .title {
      font-size: 16px;
    }
    
    .tile-title {
      font-size: 15px;
    }
    
    .mb-3 p {
      font-size: 12px;
    }
    
    .mb-3 small {
      font-size: 11px;
    }
    
    .badge {
      font-size: 11px;
      padding: 5px 8px;
    }
  }
</style>
@endsection

@section('scripts')
<script>
function openReportIssueModal() {
  window.location.href = "{{ route('customer.dashboard') }}#report-issue";
}
</script>
@endsection





