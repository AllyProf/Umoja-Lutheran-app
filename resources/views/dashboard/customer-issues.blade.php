@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-exclamation-triangle"></i> My Issues</h1>
    <p>View and track all your reported issues</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">My Issues</a></li>
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
    <div class="widget-small success coloured-icon">
      <i class="icon fa fa-check-circle fa-2x"></i>
      <div class="info">
        <h4>Resolved</h4>
        <p><b>{{ $stats['resolved'] }}</b></p>
      </div>
    </div>
  </div>
</div>

<!-- Issues Table -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn">
        <h3 class="title"><i class="fa fa-list"></i> All Issues</h3>
        <div>
          <a href="{{ route('customer.dashboard') }}" class="btn btn-primary icon-btn" onclick="openReportIssueModal(); return false;">
            <i class="fa fa-plus"></i> Report New Issue
          </a>
        </div>
      </div>
      <div class="tile-body">
        <!-- Filter -->
        <div class="row mb-3">
          <div class="col-md-4">
            <form method="GET" action="{{ route('customer.issues.index') }}">
              <div class="input-group">
                <select name="status" class="form-control" onchange="this.form.submit()">
                  <option value="">All Statuses</option>
                  <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                  <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                  <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                </select>
              </div>
            </form>
          </div>
        </div>

        @if($issues->count() > 0)
        
        <!-- Mobile Card View -->
        <div class="mobile-issues-cards d-md-none">
          @foreach($issues as $issue)
          <div class="issue-card-mobile mb-3" style="border: 1px solid #ddd; border-radius: 8px; padding: 15px; background: white;">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <div>
                <strong style="font-size: 16px; color: #e07632;">Issue #{{ $issue->id }}</strong>
                <br><small class="text-muted">{{ $issue->created_at->format('M d, Y H:i') }}</small>
              </div>
              <div class="text-right">
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
            
            <div class="mb-2">
              <strong style="font-size: 14px;">{{ $issue->subject }}</strong>
              <br><small class="text-muted">{{ strlen($issue->description) > 80 ? substr($issue->description, 0, 80) . '...' : $issue->description }}</small>
            </div>
            
            <div class="row mb-2">
              <div class="col-6">
                <small class="text-muted">Type</small><br>
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
              <div class="col-6">
                <small class="text-muted">Priority</small><br>
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
            
            <div class="mb-2">
              <small class="text-muted">Room/Booking</small><br>
              @if($issue->booking)
                <small><strong>{{ $issue->booking->booking_reference }}</strong></small><br>
                <small>{{ $issue->room->room_number ?? 'N/A' }}</small>
              @elseif($issue->room)
                <small>{{ $issue->room->room_number ?? 'N/A' }}</small>
              @else
                <small class="text-muted">N/A</small>
              @endif
            </div>
            
            @if($issue->resolved_at)
            <div class="mb-2">
              <small class="text-muted">Resolved</small><br>
              <small>{{ $issue->resolved_at->format('M d, Y H:i') }}</small>
            </div>
            @endif
            
            <div class="mt-3 pt-3" style="border-top: 1px solid #eee;">
              <a href="{{ route('customer.issues.show', $issue) }}" class="btn btn-sm btn-info btn-block" style="width: 100%;">
                <i class="fa fa-eye"></i> View Details
              </a>
            </div>
          </div>
          @endforeach
        </div>
        
        <!-- Desktop Table View -->
        <div class="table-responsive d-none d-md-block">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>Issue ID</th>
                <th>Subject</th>
                <th>Type</th>
                <th>Priority</th>
                <th>Room/Booking</th>
                <th>Status</th>
                <th>Reported</th>
                <th>Resolved</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($issues as $issue)
              <tr>
                <td><strong>#{{ $issue->id }}</strong></td>
                <td>
                  <strong>{{ $issue->subject }}</strong>
                  @if(strlen($issue->description) > 50)
                    <br><small class="text-muted">{{ substr($issue->description, 0, 50) }}...</small>
                  @else
                    <br><small class="text-muted">{{ $issue->description }}</small>
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
                  @if($issue->booking)
                    <a href="{{ route('customer.my-bookings') }}" target="_blank">
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
                  @if($issue->resolved_at)
                    <small>{{ $issue->resolved_at->format('M d, Y') }}</small><br>
                    <small class="text-muted">{{ $issue->resolved_at->format('H:i') }}</small>
                  @else
                    <small class="text-muted">-</small>
                  @endif
                </td>
                <td>
                  <a href="{{ route('customer.issues.show', $issue) }}" class="btn btn-sm btn-info" title="View Details">
                    <i class="fa fa-eye"></i> View
                  </a>
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
          @if(!$hasCheckedIn)
          <i class="fa fa-exclamation-triangle fa-3x text-warning mb-3"></i>
          <h4>Check In Required</h4>
          <p class="text-muted">You need to check in first before you can report issues.</p>
          @else
          <i class="fa fa-check-circle fa-3x text-success mb-3"></i>
          <h4>No Issues Reported</h4>
          <p class="text-muted">You haven't reported any issues yet.</p>
          <a href="{{ route('customer.dashboard') }}" class="btn btn-primary" onclick="openReportIssueModal(); return false;">
            <i class="fa fa-plus"></i> Report an Issue
          </a>
          @endif
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

@endsection

@section('styles')
<style>
  /* Mobile Responsive Styles */
  @media (max-width: 768px) {
    /* Statistics Cards */
    .col-md-3.col-lg-3 {
      flex: 0 0 50%;
      max-width: 50%;
      margin-bottom: 15px;
    }
    
    .widget-small {
      margin-bottom: 0;
    }
    
    .widget-small .info h4 {
      font-size: 13px;
    }
    
    .widget-small .info p {
      font-size: 16px;
    }
    
    .widget-small .icon {
      font-size: 1.8rem !important;
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
    
    /* Filter */
    .col-md-4 {
      flex: 0 0 100%;
      max-width: 100%;
      margin-bottom: 15px;
    }
    
    .form-control {
      font-size: 14px;
      padding: 8px 12px;
    }
    
    /* Mobile Issues Cards */
    .mobile-issues-cards {
      display: block;
    }
    
    .issue-card-mobile {
      transition: all 0.3s ease;
    }
    
    .issue-card-mobile:hover {
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .issue-card-mobile .btn {
      font-size: 12px;
      padding: 8px 12px;
    }
    
    .issue-card-mobile .badge {
      font-size: 11px;
      padding: 4px 8px;
    }
    
    /* Hide desktop table on mobile */
    .table-responsive.d-none {
      display: none !important;
    }
    
    /* Empty State */
    .text-center {
      padding: 30px 15px !important;
    }
    
    .text-center h4 {
      font-size: 18px;
    }
    
    .text-center p {
      font-size: 14px;
    }
    
    .text-center .fa-3x {
      font-size: 2.5rem !important;
    }
    
    .text-center .btn {
      font-size: 14px;
      padding: 10px 20px;
    }
  }
  
  /* Desktop Table Styles */
  @media (min-width: 768px) {
    .table-responsive {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }
    
    .table {
      font-size: 13px;
    }
    
    .table th,
    .table td {
      padding: 10px 8px;
      white-space: nowrap;
    }
    
    .table .badge {
      font-size: 11px;
      padding: 4px 8px;
    }
    
    .table .btn-sm {
      font-size: 11px;
      padding: 4px 8px;
    }
  }
  
  /* Small Mobile Devices */
  @media (max-width: 480px) {
    .col-md-3.col-lg-3 {
      flex: 0 0 100%;
      max-width: 100%;
    }
    
    .widget-small .info h4 {
      font-size: 12px;
    }
    
    .widget-small .info p {
      font-size: 18px;
    }
    
    .tile-title-w-btn .title {
      font-size: 16px;
    }
  }
</style>
@endsection

@section('scripts')
<script>
function openReportIssueModal() {
  // Redirect to dashboard and trigger modal
  window.location.href = "{{ route('customer.dashboard') }}#report-issue";
}
</script>
@endsection





