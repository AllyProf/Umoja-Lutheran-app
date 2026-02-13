@extends('dashboard.layouts.reports')

@section('reports-content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-dashboard"></i> General Report</h1>
    <p>High-level overview with charts and graphs for management</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></li>
    <li class="breadcrumb-item"><a href="#">General Report</a></li>
  </ul>
</div>

<!-- Report Filter -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-body">
        <form method="GET" action="{{ route('admin.reports.general') }}" id="reportForm">
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label for="period"><strong>Period:</strong></label>
                <select name="period" id="period" class="form-control" onchange="toggleDateInputs()">
                  <option value="today" {{ $period == 'today' ? 'selected' : '' }}>Today</option>
                  <option value="week" {{ $period == 'week' ? 'selected' : '' }}>This Week</option>
                  <option value="month" {{ $period == 'month' ? 'selected' : '' }}>This Month</option>
                  <option value="year" {{ $period == 'year' ? 'selected' : '' }}>This Year</option>
                  <option value="custom" {{ $period == 'custom' ? 'selected' : '' }}>Custom Range</option>
                </select>
              </div>
            </div>
            <div class="col-md-3" id="startDateContainer" style="display: {{ $period == 'custom' ? 'block' : 'none' }};">
              <div class="form-group">
                <label for="start_date"><strong>Start Date:</strong></label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
              </div>
            </div>
            <div class="col-md-3" id="endDateContainer" style="display: {{ $period == 'custom' ? 'block' : 'none' }};">
              <div class="form-group">
                <label for="end_date"><strong>End Date:</strong></label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary btn-block">
                  <i class="fa fa-file-text"></i> Generate Report
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Key Metrics Cards -->
<div class="row mb-3">
  <div class="col-md-3">
    <div class="widget-small primary coloured-icon">
      <i class="icon fa fa-money fa-2x"></i>
      <div class="info">
        <h4>Total Revenue</h4>
        <p><b>{{ number_format($totalRevenueTZS, 0) }} TZS</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small info coloured-icon">
      <i class="icon fa fa-calendar-check-o fa-2x"></i>
      <div class="info">
        <h4>Total Bookings</h4>
        <p><b>{{ $totalBookings }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small warning coloured-icon">
      <i class="icon fa fa-bed fa-2x"></i>
      <div class="info">
        <h4>Current Occupancy</h4>
        <p><b>{{ $occupancyRate }}%</b></p>
        <small>{{ $occupiedRooms }}/{{ $totalRooms }} rooms</small>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small success coloured-icon">
      <i class="icon fa fa-calculator fa-2x"></i>
      <div class="info">
        <h4>Avg Booking Value</h4>
        <p><b>{{ $totalBookings > 0 ? number_format($roomRevenueTZS / $totalBookings, 0) : 0 }} TZS</b></p>
        <small>Room revenue only</small>
      </div>
    </div>
  </div>
</div>

<!-- Charts Section -->
<div class="row mb-3">
  <div class="col-md-6">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-line-chart"></i> Revenue Trend</h3>
      <div class="tile-body">
        <canvas id="revenueChart" height="200"></canvas>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-pie-chart"></i> Revenue Distribution</h3>
      <div class="tile-body">
        <canvas id="revenueDistributionChart" height="200"></canvas>
      </div>
    </div>
  </div>
</div>

<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-bar-chart"></i> Booking Trends</h3>
      <div class="tile-body">
        <canvas id="bookingTrendsChart" height="100"></canvas>
      </div>
    </div>
  </div>
</div>

<!-- Quick Actions -->
<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-body text-center">
        <button onclick="window.print()" class="btn btn-primary">
          <i class="fa fa-print"></i> Print Report
        </button>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
          <i class="fa fa-arrow-left"></i> Back to Reports Dashboard
        </a>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function toggleDateInputs() {
  const period = document.getElementById('period').value;
  const startContainer = document.getElementById('startDateContainer');
  const endContainer = document.getElementById('endDateContainer');
  
  if (period === 'custom') {
    startContainer.style.display = 'block';
    endContainer.style.display = 'block';
  } else {
    startContainer.style.display = 'none';
    endContainer.style.display = 'none';
  }
}

// Data from controller
const trendLabels = {!! json_encode($trendLabels) !!};
const trendRevenue = {!! json_encode($trendRevenue) !!};
const trendBookings = {!! json_encode($trendBookings) !!};

// Revenue Trend Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revenueCtx, {
  type: 'line',
  data: {
    labels: trendLabels,
    datasets: [{
      label: 'Total Revenue (TZS)',
      data: trendRevenue,
      borderColor: '#e07632',
      backgroundColor: 'rgba(224, 118, 58, 0.1)',
      fill: true,
      tension: 0.4
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
      y: {
        beginAtZero: true,
        ticks: {
          callback: function(value) {
            return value.toLocaleString() + ' TZS';
          }
        }
      }
    }
  }
});

// Revenue Distribution Chart
const distributionCtx = document.getElementById('revenueDistributionChart').getContext('2d');
new Chart(distributionCtx, {
  type: 'doughnut',
  data: {
    labels: ['Room Bookings', 'F&B Services', 'Day Services'],
    datasets: [{
      data: [{{ $roomRevenueTZS }}, {{ $serviceRevenueTZS }}, {{ $dayServiceRevenueTZS }}],
      backgroundColor: ['#e07632', '#667eea', '#764ba2']
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      tooltip: {
        callbacks: {
          label: function(context) {
            let label = context.label || '';
            let value = context.raw || 0;
            return label + ': ' + value.toLocaleString() + ' TZS';
          }
        }
      }
    }
  }
});

// Booking Trends Chart
const trendsCtx = document.getElementById('bookingTrendsChart').getContext('2d');
new Chart(trendsCtx, {
  type: 'bar',
  data: {
    labels: trendLabels,
    datasets: [{
      label: 'Number of Bookings',
      data: trendBookings,
      backgroundColor: '#667eea'
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
      y: {
        beginAtZero: true,
        ticks: {
          stepSize: 1
        }
      }
    }
  }
});
</script>
@endsection
