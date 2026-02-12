@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-history"></i> Activity Logs</h1>
    <p>Track all user activities in the system</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('super_admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Activity Logs</a></li>
  </ul>
</div>

<!-- Filters -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-body">
        <div class="row">
          <div class="col-md-3">
            <div class="form-group">
              <label for="filter_user">User</label>
              <select id="filter_user" class="form-control" onchange="filterActivityLogs()">
                <option value="">All Users</option>
                @foreach($users as $user)
                <option value="{{ $user['id'] }}">
                  {{ $user['name'] }} ({{ $user['email'] }})
                </option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label for="filter_action">Action</label>
              <select id="filter_action" class="form-control" onchange="filterActivityLogs()">
                <option value="">All Actions</option>
                @foreach($actions as $action)
                <option value="{{ $action }}">
                  {{ ucfirst($action) }}
                </option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label for="filter_model">Model Type</label>
              <select id="filter_model" class="form-control" onchange="filterActivityLogs()">
                <option value="">All Types</option>
                @foreach($modelTypes as $type)
                <option value="{{ $type }}">
                  {{ class_basename($type) }}
                </option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label for="filter_date_from">Date From</label>
              <input type="date" id="filter_date_from" class="form-control" onchange="filterActivityLogs()">
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label for="filter_date_to">Date To</label>
              <input type="date" id="filter_date_to" class="form-control" onchange="filterActivityLogs()">
            </div>
          </div>
          <div class="col-md-1">
            <div class="form-group">
              <label>&nbsp;</label>
              <button type="button" class="btn btn-secondary btn-block" onclick="resetActivityFilters()">
                <i class="fa fa-refresh"></i> Reset
              </button>
            </div>
          </div>
        </div>
        <div class="mt-2">
          <a href="{{ route('super_admin.activity-logs.export', request()->query()) }}" class="btn btn-success">
            <i class="fa fa-download"></i> Export Logs (CSV)
          </a>
          <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#clearLogsModal">
            <i class="fa fa-trash"></i> Clear Old Logs
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Activity Logs Table -->
<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn">
        <h3 class="title"><i class="fa fa-history"></i> Activity Logs (<span id="logCount">{{ $logs->count() }}</span> records)</h3>
      </div>
      <div class="tile-body">
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>Time</th>
                <th>User</th>
                <th>Action</th>
                <th>Model</th>
                <th>Description</th>
                <th>IP Address</th>
                <th>Details</th>
              </tr>
            </thead>
            <tbody>
              @forelse($logs as $log)
              @php
                // Check both Staff and Guest models (handle ID collisions)
                $logUser = null;
                if ($log->user_id) {
                  $logUser = \App\Models\Staff::find($log->user_id);
                  if (!$logUser) {
                    $logUser = \App\Models\Guest::find($log->user_id);
                  }
                }
              @endphp
              <tr class="activity-log-row" 
                  data-user-id="{{ $log->user_id ?? '' }}"
                  data-user-name="{{ $logUser ? strtolower($logUser->name) : 'system' }}"
                  data-user-email="{{ $logUser ? strtolower($logUser->email) : '' }}"
                  data-action="{{ strtolower($log->action) }}"
                  data-model-type="{{ $log->model_type ? strtolower($log->model_type) : '' }}"
                  data-model-name="{{ $log->model_type ? strtolower(class_basename($log->model_type)) : '' }}"
                  data-description="{{ strtolower($log->description ?? '') }}"
                  data-ip-address="{{ strtolower($log->ip_address ?? '') }}"
                  data-date="{{ $log->created_at->format('Y-m-d') }}"
                  data-timestamp="{{ $log->created_at->timestamp }}">
                <td>{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                <td>
                  @php
                    // Check both Staff and Guest models (handle ID collisions)
                    $logUser = null;
                    if ($log->user_id) {
                      $logUser = \App\Models\Staff::find($log->user_id);
                      if (!$logUser) {
                        $logUser = \App\Models\Guest::find($log->user_id);
                      }
                    }
                  @endphp
                  @if($logUser)
                    <strong>{{ $logUser->name }}</strong><br>
                    <small class="text-muted">{{ $logUser->email }}</small>
                  @else
                    <span class="text-muted">System</span>
                  @endif
                </td>
                <td>
                  <span class="badge badge-{{ $log->action == 'deleted' ? 'danger' : ($log->action == 'created' ? 'success' : 'info') }}">
                    {{ ucfirst($log->action) }}
                  </span>
                </td>
                <td>
                  @if($log->model_type)
                    <code>{{ class_basename($log->model_type) }}</code>
                    @if($log->model_id)
                      <br><small>ID: {{ $log->model_id }}</small>
                    @endif
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td>{{ $log->description }}</td>
                <td>
                  @if($log->ip_address && $log->ip_address !== '127.0.0.1' && $log->ip_address !== '::1')
                  <small>{{ $log->ip_address }}</small><br>
                  <button type="button" class="btn btn-xs btn-primary mt-1" 
                          onclick="lookupIpAddress('{{ $log->ip_address }}', {{ $log->id }})">
                    <i class="fa fa-map-marker"></i> Location
                  </button>
                  @else
                  <small>{{ $log->ip_address ?? 'N/A' }}</small>
                  @endif
                </td>
                <td>
                  @if($log->old_values || $log->new_values)
                  <button type="button" class="btn btn-sm btn-info" 
                          data-toggle="modal" 
                          data-target="#logDetailsModal{{ $log->id }}">
                    <i class="fa fa-eye"></i> View
                  </button>
                  @else
                  <span class="text-muted">-</span>
                  @endif
                </td>
              </tr>
              
              <!-- Log Details Modal -->
              @if($log->old_values || $log->new_values)
              <div class="modal fade" id="logDetailsModal{{ $log->id }}" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Activity Details</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body">
                      <div class="row">
                        @if($log->old_values)
                        <div class="col-md-6">
                          <h6>Old Values</h6>
                          <pre class="bg-light p-3">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                        @endif
                        @if($log->new_values)
                        <div class="col-md-6">
                          <h6>New Values</h6>
                          <pre class="bg-light p-3">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                        @endif
                      </div>
                      <div class="mt-3">
                        <strong>User Agent:</strong><br>
                        <small class="text-muted">{{ $log->user_agent }}</small>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                  </div>
                </div>
              </div>
              @endif
              @empty
              <tr>
                <td colspan="7" class="text-center">No activity logs found</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Clear Logs Modal -->
<div class="modal fade" id="clearLogsModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="{{ route('super_admin.logs.clear') }}" method="POST" id="clearLogsForm">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Clear Old Logs</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="type">Log Type</label>
            <select name="type" id="type" class="form-control" required>
              <option value="activity">Activity Logs</option>
              <option value="system">System Logs</option>
              <option value="both">Both</option>
            </select>
          </div>
          <div class="form-group">
            <label for="days">Older Than (Days)</label>
            <input type="number" name="days" id="days" class="form-control" 
                   value="30" min="1" max="365" required>
            <small class="form-text text-danger">This action cannot be undone!</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-danger" onclick="confirmClearLogs()">Clear Logs</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- IP Address Lookup Modal -->
<div class="modal fade" id="ipLookupModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-map-marker"></i> IP Address Information</h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="ipLookupContent">
          <div class="text-center">
            <i class="fa fa-spinner fa-spin fa-2x"></i>
            <p>Loading IP information...</p>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
function lookupIpAddress(ipAddress, logId) {
  // Show modal
  $('#ipLookupModal').modal('show');
  
  // Check if it's a localhost or private IP address
  const isLocalhost = ipAddress === '127.0.0.1' || ipAddress === '::1' || ipAddress === 'localhost';
  const isPrivateIP = /^(10\.|172\.(1[6-9]|2[0-9]|3[01])\.|192\.168\.)/.test(ipAddress);
  
  if (isLocalhost || isPrivateIP) {
    // Show localhost/private IP information
    $('#ipLookupContent').html(`
      <div class="alert alert-info">
        <strong><i class="fa fa-info-circle"></i> ${isLocalhost ? 'Localhost' : 'Private'} IP Address</strong><br>
        This IP address (${ipAddress}) is a ${isLocalhost ? 'localhost/loopback' : 'private network'} address.<br>
        <strong>Location:</strong> ${isLocalhost ? 'Local Machine' : 'Private Network'}<br>
        <strong>Country:</strong> N/A (${isLocalhost ? 'Local' : 'Private Network'})<br>
        <strong>ISP:</strong> N/A (${isLocalhost ? 'Local Network' : 'Private Network'})<br>
        <strong>Type:</strong> ${isLocalhost ? 'Loopback Address' : 'RFC 1918 Private Address'}
      </div>
    `);
    return;
  }
  
  // Reset content
  $('#ipLookupContent').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i><p>Loading IP information...</p></div>');
  
  // Use ipapi.co (free, HTTPS support, 1000 requests/day)
  fetch(`https://ipapi.co/${ipAddress}/json/`)
    .then(response => response.json())
    .then(data => {
      // Check if there's an error in the response
      if (data.error) {
        // Handle reserved/private IP addresses
        if (data.reason && (data.reason.includes('Reserved') || data.reason.includes('Private'))) {
          $('#ipLookupContent').html(`
            <div class="alert alert-info">
              <strong><i class="fa fa-info-circle"></i> Reserved/Private IP Address</strong><br>
              This IP address (${ipAddress}) is a reserved or private network address.<br>
              <strong>Location:</strong> Private Network/Local Network<br>
              <strong>Country:</strong> N/A (Private Network)<br>
              <strong>ISP:</strong> N/A (Private Network)<br>
              <strong>Type:</strong> ${data.reason}
            </div>
          `);
        } else {
          $('#ipLookupContent').html(`
            <div class="alert alert-warning">
              <i class="fa fa-exclamation-triangle"></i> Unable to retrieve IP information: ${data.reason || data.error || 'Unknown error'}
            </div>
          `);
        }
        return;
      }
      
      // ipapi.co returns data directly (no status field)
      let html = `
        <div class="row">
          <div class="col-md-6">
            <h6><i class="fa fa-globe"></i> Location Information</h6>
            <table class="table table-sm table-bordered">
              <tr>
                <th width="40%">IP Address:</th>
                <td><code>${data.ip || ipAddress}</code></td>
              </tr>
              <tr>
                <th>Country:</th>
                <td>${data.country_name || 'N/A'} (${data.country_code || 'N/A'})</td>
              </tr>
              <tr>
                <th>Region:</th>
                <td>${data.region || 'N/A'} (${data.region_code || 'N/A'})</td>
              </tr>
              <tr>
                <th>City:</th>
                <td>${data.city || 'N/A'}</td>
              </tr>
              <tr>
                <th>ZIP Code:</th>
                <td>${data.postal || 'N/A'}</td>
              </tr>
              <tr>
                <th>Coordinates:</th>
                <td>${data.latitude || 'N/A'}, ${data.longitude || 'N/A'}</td>
              </tr>
              <tr>
                <th>Timezone:</th>
                <td>${data.timezone || 'N/A'}</td>
              </tr>
            </table>
          </div>
          <div class="col-md-6">
            <h6><i class="fa fa-building"></i> Network Information</h6>
            <table class="table table-sm table-bordered">
              <tr>
                <th width="40%">ISP:</th>
                <td>${data.org || data.isp || 'N/A'}</td>
              </tr>
              <tr>
                <th>Organization:</th>
                <td>${data.org || 'N/A'}</td>
              </tr>
              <tr>
                <th>AS Number:</th>
                <td>${data.asn || 'N/A'}</td>
              </tr>
              <tr>
                <th>Currency:</th>
                <td>${data.currency || 'N/A'}</td>
              </tr>
            </table>
            <div class="mt-3">
              ${data.latitude && data.longitude ? `
              <a href="https://www.google.com/maps?q=${data.latitude},${data.longitude}" target="_blank" class="btn btn-sm btn-primary">
                <i class="fa fa-map"></i> View on Google Maps
              </a>
              ` : ''}
            </div>
          </div>
        </div>
      `;
      $('#ipLookupContent').html(html);
    })
    .catch(error => {
      $('#ipLookupContent').html(`
        <div class="alert alert-danger">
          <i class="fa fa-times-circle"></i> Error fetching IP information: ${error.message}
        </div>
      `);
    });
}

function filterActivityLogs() {
  const userId = document.getElementById('filter_user').value;
  const action = document.getElementById('filter_action').value.toLowerCase();
  const modelType = document.getElementById('filter_model').value.toLowerCase();
  const dateFrom = document.getElementById('filter_date_from').value;
  const dateTo = document.getElementById('filter_date_to').value;
  
  const rows = document.querySelectorAll('.activity-log-row');
  let visibleCount = 0;
  
  rows.forEach(row => {
    let show = true;
    
    // User filter
    if (userId && row.getAttribute('data-user-id') !== userId) {
      show = false;
    }
    
    // Action filter
    if (action && row.getAttribute('data-action') !== action) {
      show = false;
    }
    
    // Model type filter
    if (modelType) {
      const rowModelType = row.getAttribute('data-model-type');
      const rowModelName = row.getAttribute('data-model-name');
      if (!rowModelType.includes(modelType) && !rowModelName.includes(modelType)) {
        show = false;
      }
    }
    
    // Date range filter
    if (dateFrom) {
      const rowDate = row.getAttribute('data-date');
      if (rowDate < dateFrom) {
        show = false;
      }
    }
    
    if (dateTo) {
      const rowDate = row.getAttribute('data-date');
      if (rowDate > dateTo) {
        show = false;
      }
    }
    
    row.style.display = show ? '' : 'none';
    if (show) visibleCount++;
  });
  
  // Update count
  document.getElementById('logCount').textContent = visibleCount;
  
  // Show/hide "no results" message
  const tbody = document.querySelector('.table-responsive tbody');
  if (tbody) {
    let noResultsRow = tbody.querySelector('.no-results-row');
    
    if (visibleCount === 0 && (userId || action || modelType || dateFrom || dateTo)) {
      if (!noResultsRow) {
        noResultsRow = document.createElement('tr');
        noResultsRow.className = 'no-results-row';
        noResultsRow.innerHTML = `
          <td colspan="7" class="text-center">
            <i class="fa fa-search fa-3x text-muted mb-2"></i>
            <p>No activity logs found matching the selected filters</p>
          </td>
        `;
        tbody.appendChild(noResultsRow);
      }
    } else {
      if (noResultsRow) {
        noResultsRow.remove();
      }
    }
  }
}

function resetActivityFilters() {
  document.getElementById('filter_user').value = '';
  document.getElementById('filter_action').value = '';
  document.getElementById('filter_model').value = '';
  document.getElementById('filter_date_from').value = '';
  document.getElementById('filter_date_to').value = '';
  filterActivityLogs();
}

function confirmClearLogs() {
  const type = document.getElementById('type').value;
  const days = document.getElementById('days').value;
  const typeName = type === 'both' ? 'Activity and System' : (type === 'activity' ? 'Activity' : 'System');
  
  confirmAction(
    `Are you sure you want to permanently delete all ${typeName} logs older than ${days} days? This action cannot be undone!`,
    'Clear Old Logs',
    'Yes, delete logs!',
    'Cancel'
  ).then((result) => {
    if (result.isConfirmed) {
      document.getElementById('clearLogsForm').submit();
    }
  });
}

// Update export link with current filters
function updateExportLink() {
  const userId = document.getElementById('filter_user').value;
  const action = document.getElementById('filter_action').value;
  const modelType = document.getElementById('filter_model').value;
  const dateFrom = document.getElementById('filter_date_from').value;
  const dateTo = document.getElementById('filter_date_to').value;
  
  let params = new URLSearchParams();
  if (userId) params.append('user_id', userId);
  if (action) params.append('action', action);
  if (modelType) params.append('model_type', modelType);
  if (dateFrom) params.append('date_from', dateFrom);
  if (dateTo) params.append('date_to', dateTo);
  
  const exportBtn = document.getElementById('exportBtn');
  const baseUrl = exportBtn.getAttribute('href').split('?')[0];
  exportBtn.setAttribute('href', baseUrl + (params.toString() ? '?' + params.toString() : ''));
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
  filterActivityLogs();
  updateExportLink();
  
  // Update export link when filters change
  document.getElementById('filter_user').addEventListener('change', updateExportLink);
  document.getElementById('filter_action').addEventListener('change', updateExportLink);
  document.getElementById('filter_model').addEventListener('change', updateExportLink);
  document.getElementById('filter_date_from').addEventListener('change', updateExportLink);
  document.getElementById('filter_date_to').addEventListener('change', updateExportLink);
});
</script>
@endsection

