@extends('dashboard.layouts.app')

@section('styles')
<style>
  .sms-stat-card {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    padding: 18px 20px;
    margin-bottom: 15px;
    height: 100%;
    color: var(--text-main);
  }
  .sms-stat-card .stat-label {
    color: var(--text-muted);
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    margin-bottom: 6px;
  }
  .sms-stat-card .stat-value {
    font-size: 28px;
    font-weight: 700;
    color: var(--text-main);
    line-height: 1.1;
  }
  .sms-stat-card .stat-meta {
    color: var(--text-muted);
    font-size: 12px;
    margin-top: 4px;
  }
  .sms-feature-group {
    border: 1px solid var(--border-color);
    border-radius: 6px;
    padding: 14px 16px;
    margin-bottom: 15px;
    background: var(--bg-card-alt);
    height: 100%;
  }
  .sms-feature-group h5 {
    font-size: 14px;
    font-weight: 700;
    margin: 0 0 12px;
    padding-bottom: 8px;
    border-bottom: 1px solid var(--border-color);
    color: var(--text-main);
  }
  .sms-feature-row {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 10px 12px;
    margin-bottom: 8px;
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 5px;
  }
  .sms-feature-row.critical {
    background: rgba(255, 193, 7, 0.12);
    border-color: #f0d78c;
  }
  .sms-feature-row input[type="checkbox"] {
    margin-top: 3px;
    flex-shrink: 0;
  }
  .sms-feature-row label {
    margin: 0;
    cursor: pointer;
    flex: 1;
    color: var(--text-main);
  }
  .sms-feature-row label strong {
    display: block;
    font-size: 13px;
    color: var(--text-main);
  }
  .sms-feature-row label small {
    display: block;
    margin-top: 2px;
    color: var(--text-muted);
    line-height: 1.35;
  }
  .sms-chart-wrap {
    position: relative;
    height: 260px;
    width: 100%;
  }
</style>
@endsection

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-mobile"></i> {{ __('SMS Usage') }}</h1>
    <p>{{ __('Monitor daily usage and control which SMS notifications are sent') }}</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('super_admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('SMS Usage') }}</li>
  </ul>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
  <i class="fa fa-check-circle"></i> {{ session('success') }}
  <button type="button" class="close" data-dismiss="alert" aria-label="{{ __('Close') }}">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
@endif

{{-- Usage summary --}}
<div class="row">
  <div class="col-md-4">
    <div class="sms-stat-card">
      <div class="stat-label"><i class="fa fa-calendar"></i> {{ __('Today') }}</div>
      <div class="stat-value">{{ $usageStats['today'] ?? 0 }}</div>
      <div class="stat-meta">{{ $usageStats['today_success'] ?? 0 }} {{ __('successful') }}</div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="sms-stat-card">
      <div class="stat-label"><i class="fa fa-calendar-o"></i> {{ __('This Week') }}</div>
      <div class="stat-value">{{ $usageStats['this_week'] ?? 0 }}</div>
      <div class="stat-meta">{{ $usageStats['this_week_success'] ?? 0 }} {{ __('successful') }}</div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="sms-stat-card">
      <div class="stat-label"><i class="fa fa-calendar-check-o"></i> {{ __('This Month') }}</div>
      <div class="stat-value">{{ $usageStats['this_month'] ?? 0 }}</div>
      <div class="stat-meta">{{ $usageStats['this_month_success'] ?? 0 }} {{ __('successful') }}</div>
    </div>
  </div>
</div>

{{-- SMS toggles --}}
<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-toggle-on"></i> {{ __('SMS Notification Controls') }}</h3>
      <div class="tile-body">
        <p class="text-muted mb-3">
          {{ __('Uncheck any type to stop those SMS. To reduce booking spam, turn off') }}
          <strong>{{ __('Booking → Staff / Managers') }}</strong>.
        </p>
        <form action="{{ route('super_admin.sms-usage.settings') }}" method="POST">
          @csrf
          <div class="row">
            @foreach($smsFeatureGroups as $group => $features)
            <div class="col-lg-6 col-md-12 mb-3">
              <div class="sms-feature-group">
                <h5>{{ __($group) }}</h5>
                @foreach($features as $key => $meta)
                <div class="sms-feature-row {{ !empty($meta['warning']) ? 'critical' : '' }}">
                  <input type="checkbox"
                         id="{{ $key }}"
                         name="sms_features[]"
                         value="{{ $key }}"
                         {{ !empty($smsFeatureStates[$key]) ? 'checked' : '' }}>
                  <label for="{{ $key }}">
                    <strong>
                      {{ __($meta['label']) }}
                      @if(!empty($meta['warning']))
                        <span class="badge badge-warning">{{ __('Critical') }}</span>
                      @endif
                    </strong>
                    <small>{{ __($meta['description']) }}</small>
                  </label>
                </div>
                @endforeach
              </div>
            </div>
            @endforeach
          </div>
          <div class="mt-2">
            <button type="submit" class="btn btn-primary">
              <i class="fa fa-save"></i> {{ __('Save SMS Settings') }}
            </button>
            <button type="button" class="btn btn-outline-secondary" id="smsEnableAllBtn">{{ __('Enable All') }}</button>
            <button type="button" class="btn btn-outline-secondary" id="smsDisableAllBtn">{{ __('Disable All') }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- Chart --}}
<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-bar-chart"></i> {{ __('Daily SMS (Last 14 Days)') }}</h3>
      <div class="tile-body">
        <div class="sms-chart-wrap">
          <canvas id="smsDailyChart"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Filters --}}
<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-filter"></i> {{ __('Filter SMS Log') }}</h3>
      <div class="tile-body">
        <form method="GET" action="{{ route('super_admin.sms-usage') }}">
          <div class="row">
            <div class="col-md-2">
              <div class="form-group">
                <label for="status">{{ __('Status') }}</label>
                <select name="status" id="status" class="form-control">
                  <option value="">{{ __('All') }}</option>
                  <option value="success" {{ ($filters['status'] ?? '') === 'success' ? 'selected' : '' }}>{{ __('Success') }}</option>
                  <option value="failed" {{ ($filters['status'] ?? '') === 'failed' ? 'selected' : '' }}>{{ __('Failed') }}</option>
                </select>
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label for="context">{{ __('Source') }}</label>
                <select name="context" id="context" class="form-control">
                  <option value="">{{ __('All Sources') }}</option>
                  @foreach($contexts as $ctx)
                  <option value="{{ $ctx }}" {{ ($filters['context'] ?? '') === $ctx ? 'selected' : '' }}>
                    {{ $ctx }}
                  </option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label for="phone">{{ __('Phone') }}</label>
                <input type="text" name="phone" id="phone" class="form-control"
                       value="{{ $filters['phone'] ?? '' }}" placeholder="255...">
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label for="date_from">{{ __('Date From') }}</label>
                <input type="date" name="date_from" id="date_from" class="form-control"
                       value="{{ $filters['date_from'] ?? '' }}">
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label for="date_to">{{ __('Date To') }}</label>
                <input type="date" name="date_to" id="date_to" class="form-control"
                       value="{{ $filters['date_to'] ?? '' }}">
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label>&nbsp;</label>
                <div style="display:flex; gap:6px;">
                  <button type="submit" class="btn btn-primary" style="flex:1;">
                    <i class="fa fa-search"></i> {{ __('Filter') }}
                  </button>
                  <a href="{{ route('super_admin.sms-usage') }}" class="btn btn-secondary" title="{{ __('Reset') }}">
                    <i class="fa fa-refresh"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- Logs table --}}
<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title">
        <i class="fa fa-list"></i> {{ __('SMS Log') }}
        <small class="text-muted">({{ __('Total') }}: {{ $usageStats['total'] ?? 0 }})</small>
      </h3>
      <div class="tile-body">
        <div class="table-responsive">
          <table class="table table-hover table-bordered table-sm">
            <thead>
              <tr>
                <th style="width:140px;">{{ __('Time') }}</th>
                <th style="width:120px;">{{ __('Phone') }}</th>
                <th>{{ __('Message') }}</th>
                <th style="width:160px;">{{ __('Source') }}</th>
                <th style="width:90px;">{{ __('Status') }}</th>
                <th style="width:70px;">{{ __('HTTP') }}</th>
              </tr>
            </thead>
            <tbody>
              @forelse($logs as $log)
              <tr>
                <td>
                  {{ $log->created_at->format('Y-m-d H:i') }}
                  <br><small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                </td>
                <td>{{ $log->phone }}</td>
                <td>
                  <span title="{{ $log->message }}">
                    {{ \Illuminate\Support\Str::limit($log->message, 90) }}
                  </span>
                  @if($log->error)
                    <br><small class="text-danger">{{ \Illuminate\Support\Str::limit($log->error, 60) }}</small>
                  @endif
                </td>
                <td>
                  @if($log->context)
                    <span class="badge badge-secondary">{{ $log->context }}</span>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
                <td>
                  @if($log->success)
                    <span class="badge badge-success">{{ __('Success') }}</span>
                  @else
                    <span class="badge badge-danger">{{ __('Failed') }}</span>
                  @endif
                </td>
                <td>{{ $log->http_code ?? '—' }}</td>
              </tr>
              @empty
              <tr>
                <td colspan="6" class="text-center text-muted py-4">
                  {{ __('No SMS recorded yet. Usage will appear here after SMS are sent.') }}
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="mt-3">
          {{ $logs->links() }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
  (function () {
    function setAllSmsFeatures(enabled) {
      document.querySelectorAll('input[name="sms_features[]"]').forEach(function (el) {
        el.checked = !!enabled;
      });
    }

    var enableBtn = document.getElementById('smsEnableAllBtn');
    var disableBtn = document.getElementById('smsDisableAllBtn');
    if (enableBtn) enableBtn.addEventListener('click', function () { setAllSmsFeatures(true); });
    if (disableBtn) disableBtn.addEventListener('click', function () { setAllSmsFeatures(false); });

    var breakdown = @json($dailyBreakdown);
    var canvas = document.getElementById('smsDailyChart');
    if (!canvas || typeof Chart === 'undefined') return;

    new Chart(canvas.getContext('2d'), {
      type: 'bar',
      data: {
        labels: breakdown.map(function (d) { return d.label; }),
        datasets: [
          {
            label: @json(__('Total SMS')),
            data: breakdown.map(function (d) { return d.total; }),
            backgroundColor: 'rgba(54, 162, 235, 0.65)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
          },
          {
            label: @json(__('Successful')),
            data: breakdown.map(function (d) { return d.successful; }),
            backgroundColor: 'rgba(40, 167, 69, 0.55)',
            borderColor: 'rgba(40, 167, 69, 1)',
            borderWidth: 1
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: false,
        scales: {
          y: {
            beginAtZero: true,
            ticks: { precision: 0 }
          }
        }
      }
    });
  })();
</script>
@endsection
