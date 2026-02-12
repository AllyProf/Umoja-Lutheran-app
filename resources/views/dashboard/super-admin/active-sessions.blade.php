@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-users"></i> Active Sessions</h1>
    <p>View and manage active user sessions</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('super_admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Active Sessions</a></li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn">
        <h3 class="title"><i class="fa fa-users"></i> Active User Sessions (<span id="sessionCount">{{ count($sessions) }}</span>)</h3>
      </div>
      <div class="tile-body">
        <!-- Search Filters -->
        <div class="row mb-3">
          <div class="col-md-4">
            <div class="form-group">
              <label for="search_email"><i class="fa fa-envelope"></i> Search by Email</label>
              <input type="text" id="search_email" class="form-control" placeholder="Enter email address..." onkeyup="filterActiveSessions()">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="search_ip"><i class="fa fa-globe"></i> Search by IP Address</label>
              <input type="text" id="search_ip" class="form-control" placeholder="Enter IP address..." onkeyup="filterActiveSessions()">
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label>&nbsp;</label>
              <button type="button" class="btn btn-secondary btn-block" onclick="resetSessionFilters()">
                <i class="fa fa-refresh"></i> Reset
              </button>
            </div>
          </div>
        </div>
        
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>User</th>
                <th>Role</th>
                <th>Email</th>
                <th>IP Address</th>
                <th>User Agent</th>
                <th>Last Activity</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($sessions as $session)
              <tr class="session-row"
                  data-email="{{ strtolower($session['user']->email) }}"
                  data-ip-address="{{ strtolower($session['ip_address'] ?? '') }}">
                <td><strong>{{ $session['user']->name }}</strong></td>
                <td>
                  @php
                    // Handle both Staff and Guest models
                    $userRole = $session['user']->role ?? 'guest';
                  @endphp
                  @if($userRole == 'super_admin')
                    <span class="badge badge-danger">Super Admin</span>
                  @elseif($userRole == 'manager')
                    <span class="badge badge-warning">Manager</span>
                  @elseif($userRole == 'reception')
                    <span class="badge badge-info">Reception</span>
                  @else
                    <span class="badge badge-success">Guest</span>
                  @endif
                </td>
                <td>{{ $session['user']->email }}</td>
                <td><code>{{ $session['ip_address'] ?? 'N/A' }}</code></td>
                <td><small>{{ Str::limit($session['user_agent'] ?? 'N/A', 60) }}</small></td>
                <td>
                  {{ $session['last_activity']->format('M d, Y H:i:s') }}<br>
                  <small class="text-muted">{{ $session['last_activity']->diffForHumans() }}</small>
                </td>
                <td>
                  <form action="{{ route('super_admin.force-logout', $session['session_id']) }}" method="POST" 
                        style="display: inline-block;"
                        onsubmit="event.preventDefault(); confirmAction('Are you sure you want to force logout this user?', 'Force Logout', 'Yes, logout!', 'Cancel').then((result) => { if (result.isConfirmed) { this.submit(); } });">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-danger" title="Force Logout">
                      <i class="fa fa-sign-out"></i> Force Logout
                    </button>
                  </form>
                  <form action="{{ route('super_admin.force-logout-user', $session['user']->id) }}" method="POST" 
                        style="display: inline-block;"
                        onsubmit="event.preventDefault(); confirmAction('This will logout user from ALL devices. Continue?', 'Logout All Devices', 'Yes, logout all!', 'Cancel').then((result) => { if (result.isConfirmed) { this.submit(); } });">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-warning" title="Logout All Devices">
                      <i class="fa fa-ban"></i> All Devices
                    </button>
                  </form>
                </td>
              </tr>
              @empty
              <tr class="no-results-row">
                <td colspan="7" class="text-center">No active sessions</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
function filterActiveSessions() {
  const emailSearch = document.getElementById('search_email').value.toLowerCase().trim();
  const ipSearch = document.getElementById('search_ip').value.toLowerCase().trim();
  
  const rows = document.querySelectorAll('.session-row');
  let visibleCount = 0;
  
  rows.forEach(row => {
    const email = row.getAttribute('data-email') || '';
    const ipAddress = row.getAttribute('data-ip-address') || '';
    
    let show = true;
    
    // Email filter
    if (emailSearch && !email.includes(emailSearch)) {
      show = false;
    }
    
    // IP Address filter
    if (ipSearch && !ipAddress.includes(ipSearch)) {
      show = false;
    }
    
    row.style.display = show ? '' : 'none';
    if (show) visibleCount++;
  });
  
  // Update count
  document.getElementById('sessionCount').textContent = visibleCount;
  
  // Show/hide "no results" message
  const tbody = document.querySelector('.table-responsive tbody');
  if (tbody) {
    let noResultsRow = tbody.querySelector('.no-results-row');
    
    // Remove existing no-results row if it exists
    if (noResultsRow && !noResultsRow.classList.contains('session-row')) {
      noResultsRow.remove();
    }
    
    // Add no-results row if no visible rows
    if (visibleCount === 0) {
      const newNoResultsRow = document.createElement('tr');
      newNoResultsRow.className = 'no-results-row';
      newNoResultsRow.innerHTML = '<td colspan="7" class="text-center text-muted"><i class="fa fa-info-circle"></i> No active sessions match your search criteria</td>';
      tbody.appendChild(newNoResultsRow);
    }
  }
}

function resetSessionFilters() {
  document.getElementById('search_email').value = '';
  document.getElementById('search_ip').value = '';
  filterActiveSessions();
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
  filterActiveSessions();
});
</script>
@endsection

