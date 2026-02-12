@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-server"></i> System Health & Resources</h1>
    <p>Monitor server resources and system performance</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('super_admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">System Health</a></li>
  </ul>
</div>

<!-- Server Information -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-info-circle"></i> Server Information</h3>
      <div class="tile-body">
        <div class="row">
          <div class="col-md-3">
            <strong>Operating System:</strong><br>
            <span class="text-muted">{{ $serverInfo['os'] ?? 'Unknown' }}</span>
          </div>
          <div class="col-md-3">
            <strong>Server Software:</strong><br>
            <span class="text-muted">{{ $serverInfo['server_software'] ?? 'Unknown' }}</span>
          </div>
          <div class="col-md-3">
            <strong>PHP Version:</strong><br>
            <span class="text-muted">{{ $serverInfo['php_version'] ?? 'Unknown' }}</span>
          </div>
          <div class="col-md-3">
            <strong>Server Time:</strong><br>
            <span class="text-muted">{{ $serverInfo['server_time'] ?? 'Unknown' }}</span>
          </div>
        </div>
        <div class="row mt-3">
          <div class="col-md-3">
            <strong>Timezone:</strong><br>
            <span class="text-muted">{{ $serverInfo['timezone'] ?? 'Unknown' }}</span>
          </div>
          <div class="col-md-3">
            <strong>System Uptime:</strong><br>
            <span class="text-muted">{{ $serverInfo['uptime'] ?? 'N/A' }}</span>
          </div>
          <div class="col-md-3">
            <strong>Last Updated:</strong><br>
            <span class="text-muted" id="lastUpdate">{{ now()->format('H:i:s') }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Resource Usage -->
<div class="row mb-3">
  <!-- Memory Usage -->
  <div class="col-md-6">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-memory"></i> Memory Usage</h3>
      <div class="tile-body">
        @php
          $memoryUsage = $serverInfo['memory']['usage_percent'] ?? 0;
          $memoryColor = $memoryUsage > 80 ? 'danger' : ($memoryUsage > 60 ? 'warning' : 'success');
        @endphp
        <div class="progress" style="height: 30px;">
          <div class="progress-bar bg-{{ $memoryColor }}" role="progressbar" 
               style="width: {{ $memoryUsage }}%" 
               aria-valuenow="{{ $memoryUsage }}" 
               aria-valuemin="0" 
               aria-valuemax="100">
            <strong>{{ $memoryUsage }}%</strong>
          </div>
        </div>
        <div class="mt-3">
          <div class="row">
            <div class="col-md-6">
              <strong>Total:</strong> {{ $serverInfo['memory']['total'] ?? 'N/A' }}<br>
              <strong>Used:</strong> {{ $serverInfo['memory']['used'] ?? 'N/A' }}<br>
            </div>
            <div class="col-md-6">
              <strong>Free:</strong> {{ $serverInfo['memory']['free'] ?? 'N/A' }}<br>
              <strong>Usage:</strong> {{ $memoryUsage }}%
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- CPU Information -->
  <div class="col-md-6">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-microchip"></i> CPU Information</h3>
      <div class="tile-body">
        <div class="mb-3">
          <strong>CPU Cores:</strong> {{ $serverInfo['cpu']['cores'] ?? 'N/A' }}<br>
          <strong>CPU Model:</strong> <small>{{ $serverInfo['cpu']['model'] ?? 'N/A' }}</small>
        </div>
        @if($serverInfo['cpu']['usage_percent'] !== 'N/A' && $serverInfo['cpu']['usage_percent'] !== 'N/A (Requires additional monitoring)')
          @php
            $cpuUsage = (float)$serverInfo['cpu']['usage_percent'];
            $cpuColor = $cpuUsage > 80 ? 'danger' : ($cpuUsage > 60 ? 'warning' : 'success');
          @endphp
          <div class="progress" style="height: 30px;">
            <div class="progress-bar bg-{{ $cpuColor }}" role="progressbar" 
                 style="width: {{ $cpuUsage }}%" 
                 aria-valuenow="{{ $cpuUsage }}" 
                 aria-valuemin="0" 
                 aria-valuemax="100">
              <strong>{{ $cpuUsage }}%</strong>
            </div>
          </div>
          <div class="mt-2">
            <strong>CPU Usage:</strong> {{ $cpuUsage }}%
          </div>
        @else
          <div class="alert alert-info">
            <i class="fa fa-info-circle"></i> CPU usage monitoring requires additional system tools.
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Disk Usage -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-hdd-o"></i> Disk Usage</h3>
      <div class="tile-body">
        @php
          $diskUsage = $serverInfo['disk']['usage_percent'] ?? 0;
          $diskColor = $diskUsage > 80 ? 'danger' : ($diskUsage > 60 ? 'warning' : 'success');
        @endphp
        <div class="progress" style="height: 30px;">
          <div class="progress-bar bg-{{ $diskColor }}" role="progressbar" 
               style="width: {{ $diskUsage }}%" 
               aria-valuenow="{{ $diskUsage }}" 
               aria-valuemin="0" 
               aria-valuemax="100">
            <strong>{{ $diskUsage }}%</strong>
          </div>
        </div>
        <div class="mt-3">
          <div class="row">
            <div class="col-md-3">
              <strong>Total:</strong> {{ $serverInfo['disk']['total'] ?? 'N/A' }}
            </div>
            <div class="col-md-3">
              <strong>Used:</strong> {{ $serverInfo['disk']['used'] ?? 'N/A' }}
            </div>
            <div class="col-md-3">
              <strong>Free:</strong> {{ $serverInfo['disk']['free'] ?? 'N/A' }}
            </div>
            <div class="col-md-3">
              <strong>Path:</strong> <small>{{ $serverInfo['disk']['path'] ?? 'N/A' }}</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Database Statistics -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-database"></i> Database Statistics</h3>
      <div class="tile-body">
        <div class="row">
          <div class="col-md-3">
            <strong>Database Name:</strong><br>
            <span class="text-muted">{{ $dbStats['name'] ?? 'Unknown' }}</span>
          </div>
          <div class="col-md-3">
            <strong>Database Size:</strong><br>
            <span class="text-muted">{{ $dbStats['size'] ?? 'N/A' }}</span>
          </div>
          <div class="col-md-3">
            <strong>Total Tables:</strong><br>
            <span class="text-muted">{{ $dbStats['table_count'] ?? 0 }}</span>
          </div>
          <div class="col-md-3">
            <strong>Total Records:</strong><br>
            <span class="text-muted">{{ $dbStats['total_records'] ?? 0 }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- PHP Information -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-code"></i> PHP Configuration</h3>
      <div class="tile-body">
        <div class="row">
          <div class="col-md-3">
            <strong>PHP Version:</strong><br>
            <span class="text-muted">{{ $phpInfo['version'] ?? 'Unknown' }}</span>
          </div>
          <div class="col-md-3">
            <strong>Memory Limit:</strong><br>
            <span class="text-muted">{{ $phpInfo['memory_limit'] ?? 'Unknown' }}</span>
          </div>
          <div class="col-md-3">
            <strong>Current Memory Usage:</strong><br>
            <span class="text-muted">{{ $phpInfo['current_memory_usage'] ?? 'Unknown' }}</span>
          </div>
          <div class="col-md-3">
            <strong>Peak Memory Usage:</strong><br>
            <span class="text-muted">{{ $phpInfo['peak_memory_usage'] ?? 'Unknown' }}</span>
          </div>
        </div>
        <div class="row mt-3">
          <div class="col-md-3">
            <strong>Max Execution Time:</strong><br>
            <span class="text-muted">{{ $phpInfo['max_execution_time'] ?? 'Unknown' }} seconds</span>
          </div>
          <div class="col-md-3">
            <strong>Upload Max Filesize:</strong><br>
            <span class="text-muted">{{ $phpInfo['upload_max_filesize'] ?? 'Unknown' }}</span>
          </div>
          <div class="col-md-3">
            <strong>Post Max Size:</strong><br>
            <span class="text-muted">{{ $phpInfo['post_max_size'] ?? 'Unknown' }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Quick Actions -->
<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-bolt"></i> Quick Actions</h3>
      <div class="tile-body">
        <a href="{{ route('super_admin.cache-management') }}" class="btn btn-warning">
          <i class="fa fa-refresh"></i> Clear Cache
        </a>
        <a href="{{ route('super_admin.system-settings') }}" class="btn btn-primary">
          <i class="fa fa-cog"></i> System Settings
        </a>
        <button onclick="location.reload()" class="btn btn-info">
          <i class="fa fa-refresh"></i> Refresh Stats
        </button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
// Update last updated time every second
setInterval(function() {
  const now = new Date();
  document.getElementById('lastUpdate').textContent = now.toLocaleTimeString();
}, 1000);

// Auto-refresh every 60 seconds (optional - commented out to avoid disrupting user)
// setTimeout(function() {
//   location.reload();
// }, 60000);
</script>
@endsection

