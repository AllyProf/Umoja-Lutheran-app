@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-bell"></i> Notifications Center</h1>
    <p>Manage all your notifications</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="#">Notifications</a></li>
  </ul>
</div>

<!-- Statistics Cards -->
<div class="row mb-3">
  <div class="col-md-4">
    <div class="widget-small primary coloured-icon">
      <i class="icon fa fa-bell fa-2x"></i>
      <div class="info">
        <h4>Total</h4>
        <p><b>{{ $stats['total'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="widget-small warning coloured-icon">
      <i class="icon fa fa-bell-o fa-2x"></i>
      <div class="info">
        <h4>Unread</h4>
        <p><b>{{ $stats['unread'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="widget-small success coloured-icon">
      <i class="icon fa fa-check fa-2x"></i>
      <div class="info">
        <h4>Read</h4>
        <p><b>{{ $stats['read'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
</div>

<!-- Filters -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-body">
        <form method="GET" action="{{ route('customer.notifications') }}" class="row">
          <div class="col-md-4">
            <select name="filter" class="form-control">
              <option value="">All Notifications</option>
              <option value="unread" {{ request('filter') === 'unread' ? 'selected' : '' }}>Unread Only</option>
              <option value="read" {{ request('filter') === 'read' ? 'selected' : '' }}>Read Only</option>
            </select>
          </div>
          <div class="col-md-4">
            <select name="type" class="form-control">
              <option value="">All Types</option>
              <option value="booking" {{ request('type') === 'booking' ? 'selected' : '' }}>Bookings</option>
              <option value="payment" {{ request('type') === 'payment' ? 'selected' : '' }}>Payments</option>
              <option value="service_request" {{ request('type') === 'service_request' ? 'selected' : '' }}>Service Requests</option>
              <option value="maintenance" {{ request('type') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
            </select>
          </div>
          <div class="col-md-4">
            <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filter</button>
            <a href="{{ route('customer.notifications') }}" class="btn btn-secondary"><i class="fa fa-refresh"></i> Reset</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Notifications List -->
<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn">
        <h3 class="tile-title"><i class="fa fa-list"></i> All Notifications</h3>
        <div class="btn-group">
          <button class="btn btn-primary" onclick="markAllAsRead()">
            <i class="fa fa-check-double"></i> Mark All as Read
          </button>
        </div>
      </div>
      <div class="tile-body">
        @if($notifications->count() > 0)
        <div class="list-group">
          @foreach($notifications as $notification)
          <div class="list-group-item {{ !$notification->is_read ? 'list-group-item-action' : '' }}" 
               style="border-left: 4px solid {{ $notification->color === 'success' ? '#28a745' : ($notification->color === 'danger' ? '#dc3545' : ($notification->color === 'warning' ? '#ffc107' : '#17a2b8')) }}; {{ !$notification->is_read ? 'background-color: #f8f9fa; font-weight: 600;' : '' }}">
            <div class="d-flex w-100 justify-content-between">
              <div class="flex-grow-1">
                <div class="d-flex align-items-start">
                  <i class="fa {{ $notification->icon ?? 'fa-info-circle' }} fa-2x mr-3" 
                     style="color: {{ $notification->color === 'success' ? '#28a745' : ($notification->color === 'danger' ? '#dc3545' : ($notification->color === 'warning' ? '#ffc107' : '#17a2b8')) }};"></i>
                  <div class="flex-grow-1">
                    <h5 class="mb-1">
                      {{ $notification->title }}
                      @if(!$notification->is_read)
                      <span class="badge badge-primary">New</span>
                      @endif
                    </h5>
                    <p class="mb-1">{{ $notification->message }}</p>
                    <small class="text-muted">
                      <i class="fa fa-clock-o"></i> {{ $notification->created_at->diffForHumans() }}
                      @if($notification->read_at)
                      | <i class="fa fa-check"></i> Read {{ $notification->read_at->diffForHumans() }}
                      @endif
                    </small>
                  </div>
                </div>
              </div>
              <div class="ml-3">
                @if($notification->link)
                <a href="{{ $notification->link }}" class="btn btn-sm btn-outline-primary">
                  <i class="fa fa-external-link"></i> View
                </a>
                @endif
                @if(!$notification->is_read)
                <button class="btn btn-sm btn-outline-success" onclick="markAsRead({{ $notification->id }})">
                  <i class="fa fa-check"></i> Mark Read
                </button>
                @endif
              </div>
            </div>
          </div>
          @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="mt-3">
          {{ $notifications->links() }}
        </div>
        @else
        <div class="text-center" style="padding: 50px;">
          <i class="fa fa-bell-slash fa-5x text-muted mb-3"></i>
          <h3>No Notifications</h3>
          <p class="text-muted">You don't have any notifications yet.</p>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
function markAsRead(notificationId) {
    fetch('{{ url("/notifications") }}/' + notificationId + '/read', {
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
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to mark notification as read.');
    });
}

function markAllAsRead() {
    if (!confirm('Mark all notifications as read?')) {
        return;
    }
    
    fetch('{{ route("notifications.mark-all-read") }}', {
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
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to mark all notifications as read.');
    });
}
</script>
@endsection





