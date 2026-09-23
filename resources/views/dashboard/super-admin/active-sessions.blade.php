@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-users"></i> {{ __('Active Sessions') }}</h1>
    <p>{{ __('View and manage active user sessions') }}</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('super_admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="#">{{ __('Active Sessions') }}</a></li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn">
        <h3 class="title"><i class="fa fa-users"></i> {{ __('Active User Sessions') }} (<span id="sessionCount">{{ count($sessions) }}</span>)</h3>
      </div>
      <div class="tile-body">
        <!-- Search Filters -->
        <div class="row mb-3">
          <div class="col-md-4">
            <div class="form-group">
              <label for="search_email"><i class="fa fa-envelope"></i> {{ __('Search by Email') }}</label>
              <input type="text" id="search_email" class="form-control" placeholder="{{ __('Enter email address...') }}" onkeyup="filterActiveSessions()">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="search_ip"><i class="fa fa-globe"></i> {{ __('Search by IP Address') }}</label>
              <input type="text" id="search_ip" class="form-control" placeholder="{{ __('Enter IP address...') }}" onkeyup="filterActiveSessions()">
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label>&nbsp;</label>
              <button type="button" class="btn btn-secondary btn-block" onclick="resetSessionFilters()">
                <i class="fa fa-refresh"></i> {{ __('Reset') }}
              </button>
            </div>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>{{ __('User') }}</th>
                <th>{{ __('Role') }}</th>
                <th>{{ __('Email') }}</th>
                <th>{{ __('IP Address') }}</th>
                <th>{{ __('User Agent') }}</th>
                <th>{{ __('Last Activity') }}</th>
                <th>{{ __('Actions') }}</th>
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
                    $userRole = $session['user']->role ?? 'guest';
                  @endphp
                  @if($userRole == 'super_admin')
                    <span class="badge badge-danger">{{ __('Super Admin') }}</span>
                  @elseif($userRole == 'manager')
                    <span class="badge badge-warning">{{ __('Manager') }}</span>
                  @elseif($userRole == 'reception')
                    <span class="badge badge-info">{{ __('Reception') }}</span>
                  @else
                    <span class="badge badge-success">{{ __('Guest') }}</span>
                  @endif
                </td>
                <td>{{ $session['user']->email }}</td>
                <td><code>{{ $session['ip_address'] ?? __('N/A') }}</code></td>
                <td><small>{{ Str::limit($session['user_agent'] ?? __('N/A'), 60) }}</small></td>
                <td>
                  {{ $session['last_activity']->format('M d, Y H:i:s') }}<br>
                  <small class="text-muted">{{ $session['last_activity']->diffForHumans() }}</small>
                </td>
                <td>
                  <form action="{{ route('super_admin.force-logout', $session['session_id']) }}" method="POST"
                        style="display: inline-block;"
                        onsubmit="event.preventDefault(); confirmAction(@json(__('Are you sure you want to force logout this user?')), @json(__('Force Logout')), @json(__('Yes, logout!')), @json(__('Cancel'))).then((result) => { if (result.isConfirmed) { this.submit(); } });">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-danger" title="{{ __('Force Logout') }}">
                      <i class="fa fa-sign-out"></i> {{ __('Force Logout') }}
                    </button>
                  </form>
                  <form action="{{ route('super_admin.force-logout-user', $session['user']->id) }}" method="POST"
                        style="display: inline-block;"
                        onsubmit="event.preventDefault(); confirmAction(@json(__('This will logout user from ALL devices. Continue?')), @json(__('Logout All Devices')), @json(__('Yes, logout all!')), @json(__('Cancel'))).then((result) => { if (result.isConfirmed) { this.submit(); } });">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-warning" title="{{ __('Logout All Devices') }}">
                      <i class="fa fa-ban"></i> {{ __('All Devices') }}
                    </button>
                  </form>
                </td>
              </tr>
              @empty
              <tr class="no-results-row">
                <td colspan="7" class="text-center">{{ __('No active sessions') }}</td>
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

    if (emailSearch && !email.includes(emailSearch)) {
      show = false;
    }

    if (ipSearch && !ipAddress.includes(ipSearch)) {
      show = false;
    }

    row.style.display = show ? '' : 'none';
    if (show) visibleCount++;
  });

  document.getElementById('sessionCount').textContent = visibleCount;

  const tbody = document.querySelector('.table-responsive tbody');
  if (tbody) {
    let noResultsRow = tbody.querySelector('.no-results-row');

    if (noResultsRow && !noResultsRow.classList.contains('session-row')) {
      noResultsRow.remove();
    }

    if (visibleCount === 0) {
      const newNoResultsRow = document.createElement('tr');
      newNoResultsRow.className = 'no-results-row';
      newNoResultsRow.innerHTML = '<td colspan="7" class="text-center text-muted"><i class="fa fa-info-circle"></i> ' + @json(__('No active sessions match your search criteria')) + '</td>';
      tbody.appendChild(newNoResultsRow);
    }
  }
}

function resetSessionFilters() {
  document.getElementById('search_email').value = '';
  document.getElementById('search_ip').value = '';
  filterActiveSessions();
}

document.addEventListener('DOMContentLoaded', function() {
  filterActiveSessions();
});
</script>
@endsection
