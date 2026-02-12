@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-line-chart"></i> Exchange Rates</h1>
    <p>Real-time USD to TZS exchange rate and trends</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    @php
      $dashboardRoute = $role === 'manager' ? 'admin.dashboard' : ($role === 'reception' ? 'reception.dashboard' : 'customer.dashboard');
    @endphp
    <li class="breadcrumb-item"><a href="{{ route($dashboardRoute) }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Exchange Rates</a></li>
  </ul>
</div>

<!-- Current Rate Card -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-body text-center current-rate-card" style="padding: 30px; background: linear-gradient(135deg, #e07632 0%, #c86528 100%); color: white; border-radius: 8px;">
        @php
          $manualRate = \App\Models\HotelSetting::getValue('exchange_rate_usd_to_tzs');
          $isManual = !empty($manualRate) && $manualRate > 0;
        @endphp
        
        <h2 style="color: white; margin-bottom: 10px;">
          {{ $isManual ? 'Current Manual Rate' : 'Current Market Rate' }}
        </h2>
        <div class="rate-display" style="font-size: 48px; font-weight: bold; margin: 20px 0;">
          1 USD = {{ number_format($currentRate, 2) }} TZS
        </div>
        <p style="margin: 0; opacity: 0.9;">
          <i class="fa fa-{{ $isManual ? 'pencil' : 'clock-o' }}"></i> 
          {{ $isManual ? 'Using Manual Override from Settings' : 'Last updated: ' . now()->format('F d, Y H:i') }}
        </p>
        <p style="margin: 10px 0 0 0; opacity: 0.8; font-size: 14px;">
          <i class="fa fa-{{ $isManual ? 'check-circle' : 'exchange' }}"></i> 
          {{ $isManual ? 'System is locked to this rate by Manager' : 'Rate fetched from API (exchangerate-api.com)' }}
        </p>
        @php
          $currentUser = auth()->guard('staff')->user() ?? auth()->guard('guest')->user();
        @endphp
        @if($currentUser && ($currentUser->role ?? null) === 'manager')
        <p style="margin: 10px 0 0 0; opacity: 0.8; font-size: 14px;">
          <i class="fa fa-cog"></i> {{ $isManual ? 'To go back to Live API rates' : 'To set a specific manual rate (like Google\'s)' }}, go to <a href="{{ route('admin.settings.hotel') }}" style="color: white; text-decoration: underline;">Hotel Settings</a>
        </p>
        @endif
        <button onclick="refreshRate()" class="btn btn-light mt-3">
          <i class="fa fa-refresh"></i> Refresh Rate
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-3">
  <div class="col-md-3">
    <div class="widget-small primary coloured-icon">
      <i class="icon fa fa-dollar fa-2x"></i>
      <div class="info">
        <h4>Current Rate</h4>
        <p><b>{{ number_format($stats['current'], 2) }}</b> TZS</p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small info coloured-icon">
      <i class="icon fa fa-calculator fa-2x"></i>
      <div class="info">
        <h4>Average ({{ $days }} days)</h4>
        <p><b>{{ number_format($stats['average'], 2) }}</b> TZS</p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small success coloured-icon">
      <i class="icon fa fa-arrow-up fa-2x"></i>
      <div class="info">
        <h4>Maximum</h4>
        <p><b>{{ number_format($stats['max'], 2) }}</b> TZS</p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small warning coloured-icon">
      <i class="icon fa fa-arrow-down fa-2x"></i>
      <div class="info">
        <h4>Minimum</h4>
        <p><b>{{ number_format($stats['min'], 2) }}</b> TZS</p>
      </div>
    </div>
  </div>
</div>

<!-- Change Indicator -->
@if($stats['change'] != 0)
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-body">
        <div class="alert {{ $stats['change'] > 0 ? 'alert-success' : 'alert-danger' }}" style="margin: 0;">
          <i class="fa fa-{{ $stats['change'] > 0 ? 'arrow-up' : 'arrow-down' }}"></i>
          <strong>Change over {{ $days }} days:</strong> 
          {{ $stats['change'] > 0 ? '+' : '' }}{{ number_format($stats['change'], 2) }} TZS
          ({{ $stats['change'] > 0 ? '+' : '' }}{{ number_format($stats['change_percent'], 2) }}%)
        </div>
      </div>
    </div>
  </div>
</div>
@endif

<!-- Trend Chart -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn mb-3">
        <h3 class="title"><i class="fa fa-line-chart"></i> Exchange Rate Trends</h3>
        <div class="btn-group chart-period-buttons" role="group">
          <a href="{{ route('exchange-rates', ['days' => 7]) }}" class="btn btn-sm {{ $days == 7 ? 'btn-primary' : 'btn-secondary' }}">7 Days</a>
          <a href="{{ route('exchange-rates', ['days' => 30]) }}" class="btn btn-sm {{ $days == 30 ? 'btn-primary' : 'btn-secondary' }}">30 Days</a>
          <a href="{{ route('exchange-rates', ['days' => 90]) }}" class="btn btn-sm {{ $days == 90 ? 'btn-primary' : 'btn-secondary' }}">90 Days</a>
          <a href="{{ route('exchange-rates', ['days' => 180]) }}" class="btn btn-sm {{ $days == 180 ? 'btn-primary' : 'btn-secondary' }}">180 Days</a>
          <a href="{{ route('exchange-rates', ['days' => 365]) }}" class="btn btn-sm {{ $days == 365 ? 'btn-primary' : 'btn-secondary' }}">365 Days</a>
        </div>
      </div>
      <div class="tile-body">
        @if(count($historicalRates) > 0)
        <div class="chart-container" style="position: relative; height: 400px;">
          <canvas id="exchangeRateChart"></canvas>
        </div>
        @else
        <div class="text-center" style="padding: 50px;">
          <i class="fa fa-line-chart fa-5x text-muted mb-3"></i>
          <h3>No Historical Data Available</h3>
          <p class="text-muted">Historical exchange rate data is not available at this time.</p>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Rate Table -->
@if(count($historicalRates) > 0)
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-table"></i> Historical Rates (Last {{ $days }} Days)</h3>
      <div class="tile-body">
        <!-- Desktop Table View -->
        <div class="table-responsive" id="ratesTableWrapper">
          <table class="table table-hover table-bordered" id="ratesTable">
            <thead>
              <tr>
                <th>Date</th>
                <th>Exchange Rate (TZS)</th>
                <th>Change</th>
                <th>Change %</th>
              </tr>
            </thead>
            <tbody>
              @php
                $previousRate = null;
              @endphp
              @foreach(array_reverse($historicalRates) as $index => $rateData)
              @php
                $change = $previousRate ? ($rateData['rate'] - $previousRate) : 0;
                $changePercent = $previousRate ? (($change / $previousRate) * 100) : 0;
                $previousRate = $rateData['rate'];
              @endphp
              <tr>
                <td>{{ \Carbon\Carbon::parse($rateData['date'])->format('M d, Y') }}</td>
                <td><strong>{{ number_format($rateData['rate'], 2) }}</strong> TZS</td>
                <td>
                  @if($change != 0)
                    <span class="badge badge-{{ $change > 0 ? 'success' : 'danger' }}">
                      {{ $change > 0 ? '+' : '' }}{{ number_format($change, 2) }}
                    </span>
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td>
                  @if($changePercent != 0)
                    <span class="badge badge-{{ $changePercent > 0 ? 'success' : 'danger' }}">
                      {{ $changePercent > 0 ? '+' : '' }}{{ number_format($changePercent, 2) }}%
                    </span>
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        
        <!-- Mobile Card View -->
        <div class="mobile-rates-cards">
          @php
            $previousRate = null;
          @endphp
          @foreach(array_reverse($historicalRates) as $index => $rateData)
          @php
            $change = $previousRate ? ($rateData['rate'] - $previousRate) : 0;
            $changePercent = $previousRate ? (($change / $previousRate) * 100) : 0;
            $previousRate = $rateData['rate'];
          @endphp
          <div class="mobile-rate-card" style="background: white; border: 1px solid #dee2e6; border-radius: 8px; padding: 15px; margin-bottom: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <div style="border-bottom: 2px solid #940000; padding-bottom: 10px; margin-bottom: 15px;">
              <h5 style="color: #940000; font-size: 18px; font-weight: 600; margin: 0;">
                {{ \Carbon\Carbon::parse($rateData['date'])->format('M d, Y') }}
              </h5>
            </div>
            
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
              <span style="font-weight: 600; color: #495057; font-size: 14px; flex: 0 0 50%;">Exchange Rate:</span>
              <span style="text-align: right; flex: 1; font-size: 16px; font-weight: 600; color: #333;">
                {{ number_format($rateData['rate'], 2) }} TZS
              </span>
            </div>
            
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
              <span style="font-weight: 600; color: #495057; font-size: 14px; flex: 0 0 50%;">Change:</span>
              <span style="text-align: right; flex: 1;">
                @if($change != 0)
                  <span class="badge badge-{{ $change > 0 ? 'success' : 'danger' }}" style="font-size: 13px;">
                    {{ $change > 0 ? '+' : '' }}{{ number_format($change, 2) }}
                  </span>
                @else
                  <span class="text-muted">-</span>
                @endif
              </span>
            </div>
            
            <div style="display: flex; justify-content: space-between; padding: 10px 0;">
              <span style="font-weight: 600; color: #495057; font-size: 14px; flex: 0 0 50%;">Change %:</span>
              <span style="text-align: right; flex: 1;">
                @if($changePercent != 0)
                  <span class="badge badge-{{ $changePercent > 0 ? 'success' : 'danger' }}" style="font-size: 13px;">
                    {{ $changePercent > 0 ? '+' : '' }}{{ number_format($changePercent, 2) }}%
                  </span>
                @else
                  <span class="text-muted">-</span>
                @endif
              </span>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>
@endif

@endsection

@section('styles')
<style>
/* Prevent horizontal scrolling - Global */
@media (max-width: 767px) {
  body {
    overflow-x: hidden;
  }
  
  .row {
    margin-left: 0;
    margin-right: 0;
  }
  
  .row > [class*="col-"] {
    padding-left: 10px;
    padding-right: 10px;
  }
  
  .tile {
    padding: 15px;
  }
  
  .tile-body {
    padding: 15px 10px;
  }
}

/* Mobile Responsive Styles */
@media (max-width: 767px) {
  /* Current Rate Card */
  .current-rate-card {
    padding: 20px 15px !important;
    max-width: 100%;
    box-sizing: border-box;
  }
  
  .current-rate-card h2 {
    font-size: 20px !important;
    word-wrap: break-word;
  }
  
  .rate-display {
    font-size: 32px !important;
    word-break: break-word;
    line-height: 1.2;
  }
  
  .current-rate-card p {
    font-size: 13px !important;
    word-wrap: break-word;
  }
  
  .current-rate-card a {
    word-break: break-all;
  }
  
  /* Statistics Cards */
  .col-md-3 {
    margin-bottom: 15px;
    padding-left: 10px;
    padding-right: 10px;
  }
  
  /* Chart Period Buttons */
  .tile-title-w-btn {
    flex-direction: column;
    align-items: flex-start !important;
    width: 100%;
  }
  
  .tile-title-w-btn .title {
    margin-bottom: 10px;
    width: 100%;
    word-wrap: break-word;
  }
  
  .chart-period-buttons {
    display: flex !important;
    flex-wrap: wrap !important;
    width: 100% !important;
    margin-top: 15px;
    margin-left: 0 !important;
    margin-right: 0 !important;
  }
  
  .chart-period-buttons .btn {
    flex: 1 1 calc(50% - 4px);
    min-width: calc(50% - 4px);
    max-width: calc(50% - 4px);
    margin: 2px;
    font-size: 12px;
    padding: 6px 8px;
    box-sizing: border-box;
  }
  
  /* Chart Container */
  .chart-container {
    height: 300px !important;
    width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box;
    overflow: hidden;
    position: relative !important;
    padding: 0 !important;
    margin: 0 !important;
  }
  
  #exchangeRateChart {
    max-width: 100% !important;
    width: 100% !important;
    height: 100% !important;
    display: block !important;
  }
  
  /* Adjust chart plugin styles for mobile */
  .chart-container canvas {
    max-width: 100% !important;
    height: auto !important;
  }
  
  /* Table - Hide on Mobile and prevent overflow */
  #ratesTableWrapper {
    display: none !important;
    overflow-x: hidden !important;
    max-width: 100%;
  }
  
  #ratesTable {
    display: none !important;
  }
  
  /* Mobile Cards - Show on Mobile */
  .mobile-rates-cards {
    display: block;
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
  }
  
  .mobile-rate-card {
    width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box;
  }
  
  .mobile-rate-card > div {
    word-wrap: break-word;
    overflow-wrap: break-word;
  }
}

/* Desktop - Hide mobile cards */
@media (min-width: 768px) {
  .mobile-rates-cards {
    display: none;
  }
  
  #ratesTableWrapper {
    display: block;
    overflow-x: auto;
  }
  
  #ratesTable {
    display: table;
  }
  
  /* Chart Container - Desktop */
  .chart-container {
    height: 400px !important;
    width: 100%;
    max-width: 100%;
  }
}

/* Very Small Screens */
@media (max-width: 480px) {
  .current-rate-card {
    padding: 15px 10px !important;
  }
  
  .rate-display {
    font-size: 28px !important;
    line-height: 1.2;
  }
  
  .chart-period-buttons .btn {
    flex: 1 1 calc(50% - 2px);
    min-width: calc(50% - 2px);
    max-width: calc(50% - 2px);
    font-size: 11px;
    padding: 5px 6px;
    margin: 1px;
  }
  
  .chart-container {
    height: 250px !important;
  }
  
  .mobile-rate-card {
    padding: 12px 10px !important;
    margin-left: 0;
    margin-right: 0;
  }
  
  .mobile-rate-card h5 {
    font-size: 16px !important;
  }
  
  .tile-body {
    padding: 10px 5px;
  }
}
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
// Chart data
const chartData = @json($historicalRates);
const currentRate = {{ $currentRate }};

// Prepare chart data
const labels = chartData.map(item => {
    const date = new Date(item.date);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
});

const rates = chartData.map(item => item.rate);

// Create chart
let exchangeRateChart = null;
if (chartData.length > 0) {
    const ctx = document.getElementById('exchangeRateChart').getContext('2d');
    exchangeRateChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'USD to TZS Exchange Rate',
                data: rates,
                borderColor: '#e07632',
                backgroundColor: 'rgba(224, 118, 50, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 3,
                pointHoverRadius: 5,
                pointBackgroundColor: '#e07632',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            return 'Rate: ' + context.parsed.y.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' TZS';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    title: {
                        display: true,
                        text: 'TZS per USD'
                    },
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) + ' TZS';
                        }
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Date'
                    },
                    ticks: {
                        maxRotation: window.innerWidth < 768 ? 45 : 0,
                        minRotation: window.innerWidth < 768 ? 45 : 0,
                        autoSkip: true,
                        maxTicksLimit: window.innerWidth < 768 ? 10 : 20
                    }
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            }
        }
    });
    
    // Resize chart on window resize for better responsiveness
    window.addEventListener('resize', function() {
        if (exchangeRateChart) {
            exchangeRateChart.resize();
        }
    });
    
    // Initial resize to ensure proper sizing
    setTimeout(function() {
        if (exchangeRateChart) {
            exchangeRateChart.resize();
        }
    }, 100);
}

// Refresh rate function
function refreshRate() {
    const btn = event.target;
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Refreshing...';
    btn.disabled = true;
    
    // Clear cache and reload
    fetch('{{ route("exchange-rates") }}?refresh=1&days={{ $days }}', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(() => {
        location.reload();
    })
    .catch(() => {
        btn.innerHTML = originalHtml;
        btn.disabled = false;
        alert('Failed to refresh rate. Please try again.');
    });
}

// Auto-refresh every 5 minutes
setInterval(function() {
    // Silently refresh the page data
    fetch('{{ route("exchange-rates") }}?days={{ $days }}', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    });
}, 300000); // 5 minutes
</script>
@endsection




