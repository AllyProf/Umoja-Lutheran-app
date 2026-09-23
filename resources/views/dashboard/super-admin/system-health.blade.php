@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-server"></i> {{ __('System Health & Resources') }}</h1>
    <p>{{ __('Monitor server resources and system performance') }}</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('super_admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="#">{{ __('System Health') }}</a></li>
  </ul>
</div>

<!-- Server Information -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-info-circle"></i> {{ __('Server Information') }}</h3>
      <div class="tile-body">
        <div class="row">
          <div class="col-md-3">
            <strong>{{ __('Operating System:') }}</strong><br>
            <span class="text-muted">{{ $serverInfo['os'] ?? __('Unknown') }}</span>
          </div>
          <div class="col-md-3">
            <strong>{{ __('Server Software:') }}</strong><br>
            <span class="text-muted">{{ $serverInfo['server_software'] ?? __('Unknown') }}</span>
          </div>
          <div class="col-md-3">
            <strong>{{ __('PHP Version:') }}</strong><br>
            <span class="text-muted">{{ $serverInfo['php_version'] ?? __('Unknown') }}</span>
          </div>
          <div class="col-md-3">
            <strong>{{ __('Server Time:') }}</strong><br>
            <span class="text-muted">{{ $serverInfo['server_time'] ?? __('Unknown') }}</span>
          </div>
        </div>
        <div class="row mt-3">
          <div class="col-md-3">
            <strong>{{ __('Timezone:') }}</strong><br>
            <span class="text-muted">{{ $serverInfo['timezone'] ?? __('Unknown') }}</span>
          </div>
          <div class="col-md-3">
            <strong>{{ __('System Uptime:') }}</strong><br>
            <span class="text-muted">{{ $serverInfo['uptime'] ?? __('N/A') }}</span>
          </div>
          <div class="col-md-3">
            <strong>{{ __('Last Updated:') }}</strong><br>
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
      <h3 class="tile-title"><i class="fa fa-memory"></i> {{ __('Memory Usage') }}</h3>
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
              <strong>{{ __('Total') }}:</strong> {{ $serverInfo['memory']['total'] ?? __('N/A') }}<br>
              <strong>{{ __('Used') }}:</strong> {{ $serverInfo['memory']['used'] ?? __('N/A') }}<br>
            </div>
            <div class="col-md-6">
              <strong>{{ __('Free') }}:</strong> {{ $serverInfo['memory']['free'] ?? __('N/A') }}<br>
              <strong>{{ __('Usage') }}:</strong> {{ $memoryUsage }}%
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- CPU Information -->
  <div class="col-md-6">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-microchip"></i> {{ __('CPU Information') }}</h3>
      <div class="tile-body">
        <div class="mb-3">
          <strong>{{ __('CPU Cores:') }}</strong> {{ $serverInfo['cpu']['cores'] ?? __('N/A') }}<br>
          <strong>{{ __('CPU Model:') }}</strong> <small>{{ $serverInfo['cpu']['model'] ?? __('N/A') }}</small>
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
            <strong>{{ __('CPU Usage:') }}</strong> {{ $cpuUsage }}%
          </div>
        @else
          <div class="alert alert-info">
            <i class="fa fa-info-circle"></i> {{ __('CPU usage monitoring requires additional system tools.') }}
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
      <h3 class="tile-title"><i class="fa fa-hdd-o"></i> {{ __('Disk Usage') }}</h3>
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
              <strong>{{ __('Total') }}:</strong> {{ $serverInfo['disk']['total'] ?? __('N/A') }}
            </div>
            <div class="col-md-3">
              <strong>{{ __('Used') }}:</strong> {{ $serverInfo['disk']['used'] ?? __('N/A') }}
            </div>
            <div class="col-md-3">
              <strong>{{ __('Free') }}:</strong> {{ $serverInfo['disk']['free'] ?? __('N/A') }}
            </div>
            <div class="col-md-3">
              <strong>{{ __('Path') }}:</strong> <small>{{ $serverInfo['disk']['path'] ?? __('N/A') }}</small>
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
      <h3 class="tile-title"><i class="fa fa-database"></i> {{ __('Database Statistics') }}</h3>
      <div class="tile-body">
        <div class="row">
          <div class="col-md-3">
            <strong>{{ __('Database Name:') }}</strong><br>
            <span class="text-muted">{{ $dbStats['name'] ?? __('Unknown') }}</span>
          </div>
          <div class="col-md-3">
            <strong>{{ __('Database Size:') }}</strong><br>
            <span class="text-muted">{{ $dbStats['size'] ?? __('N/A') }}</span>
          </div>
          <div class="col-md-3">
            <strong>{{ __('Total Tables:') }}</strong><br>
            <span class="text-muted">{{ $dbStats['table_count'] ?? 0 }}</span>
          </div>
          <div class="col-md-3">
            <strong>{{ __('Total Records:') }}</strong><br>
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
      <h3 class="tile-title"><i class="fa fa-code"></i> {{ __('PHP Configuration') }}</h3>
      <div class="tile-body">
        <div class="row">
          <div class="col-md-3">
            <strong>{{ __('PHP Version:') }}</strong><br>
            <span class="text-muted">{{ $phpInfo['version'] ?? __('Unknown') }}</span>
          </div>
          <div class="col-md-3">
            <strong>{{ __('Memory Limit:') }}</strong><br>
            <span class="text-muted">{{ $phpInfo['memory_limit'] ?? __('Unknown') }}</span>
          </div>
          <div class="col-md-3">
            <strong>{{ __('Current Memory Usage:') }}</strong><br>
            <span class="text-muted">{{ $phpInfo['current_memory_usage'] ?? __('Unknown') }}</span>
          </div>
          <div class="col-md-3">
            <strong>{{ __('Peak Memory Usage:') }}</strong><br>
            <span class="text-muted">{{ $phpInfo['peak_memory_usage'] ?? __('Unknown') }}</span>
          </div>
        </div>
        <div class="row mt-3">
          <div class="col-md-3">
            <strong>{{ __('Max Execution Time:') }}</strong><br>
            <span class="text-muted">{{ $phpInfo['max_execution_time'] ?? __('Unknown') }} {{ __('seconds') }}</span>
          </div>
          <div class="col-md-3">
            <strong>{{ __('Upload Max Filesize:') }}</strong><br>
            <span class="text-muted">{{ $phpInfo['upload_max_filesize'] ?? __('Unknown') }}</span>
          </div>
          <div class="col-md-3">
            <strong>{{ __('Post Max Size:') }}</strong><br>
            <span class="text-muted">{{ $phpInfo['post_max_size'] ?? __('Unknown') }}</span>
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
      <h3 class="tile-title"><i class="fa fa-bolt"></i> {{ __('Quick Actions') }}</h3>
      <div class="tile-body">
        <a href="{{ route('super_admin.cache-management') }}" class="btn btn-warning">
          <i class="fa fa-refresh"></i> {{ __('Clear Cache') }}
        </a>
        <a href="{{ route('super_admin.system-settings') }}" class="btn btn-primary">
          <i class="fa fa-cog"></i> {{ __('System Settings') }}
        </a>
        <button onclick="location.reload()" class="btn btn-info">
          <i class="fa fa-refresh"></i> {{ __('Refresh Stats') }}
        </button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
setInterval(function() {
  const now = new Date();
  document.getElementById('lastUpdate').textContent = now.toLocaleTimeString();
}, 1000);
</script>
@endsection
