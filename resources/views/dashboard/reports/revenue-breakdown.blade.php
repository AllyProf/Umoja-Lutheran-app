@extends('dashboard.layouts.reports')

@section('reports-content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-line-chart"></i> Revenue Breakdown Report</h1>
    <p>Detailed revenue analysis by source and payment method</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></li>
    <li class="breadcrumb-item"><a href="#">Revenue Breakdown</a></li>
  </ul>
</div>

<!-- Report Filter -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-body">
        <form method="GET" action="{{ route('admin.reports.revenue-breakdown') }}" id="reportForm">
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label for="report_type"><strong>Report Type:</strong></label>
                <select name="report_type" id="report_type" class="form-control" onchange="toggleDateInputs()">
                  <option value="daily" {{ $reportType == 'daily' ? 'selected' : '' }}>Daily</option>
                  <option value="weekly" {{ $reportType == 'weekly' ? 'selected' : '' }}>Weekly</option>
                  <option value="monthly" {{ $reportType == 'monthly' ? 'selected' : '' }}>Monthly</option>
                  <option value="yearly" {{ $reportType == 'yearly' ? 'selected' : '' }}>Yearly</option>
                  <option value="custom" {{ $reportType == 'custom' ? 'selected' : '' }}>Custom Date Range</option>
                </select>
              </div>
            </div>
            <div class="col-md-3" id="singleDateContainer">
              <div class="form-group">
                <label for="date"><strong>Select Date:</strong></label>
                <input type="date" name="date" id="date" class="form-control" value="{{ $reportDate }}">
              </div>
            </div>
            <div class="col-md-3" id="startDateContainer" style="display: {{ $reportType == 'custom' ? 'block' : 'none' }};">
              <div class="form-group">
                <label for="start_date"><strong>Start Date:</strong></label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
              </div>
            </div>
            <div class="col-md-3" id="endDateContainer" style="display: {{ $reportType == 'custom' ? 'block' : 'none' }};">
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

<!-- Summary Cards -->
<div class="row mb-3">
  <div class="col-md-3">
    <div class="widget-small primary coloured-icon">
      <i class="icon fa fa-bed fa-2x"></i>
      <div class="info">
        <h4>Room Revenue</h4>
        <p><b>{{ number_format($roomRevenueTZS, 0) }} TZS</b></p>
        <small>≈ ${{ number_format($roomRevenueTZS / $exchangeRate, 2) }}</small>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small info coloured-icon">
      <i class="icon fa fa-cutlery fa-2x"></i>
      <div class="info">
        <h4>Service Revenue</h4>
        <p><b>{{ number_format($serviceRevenueTZS, 0) }} TZS</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small warning coloured-icon">
      <i class="icon fa fa-calendar fa-2x"></i>
      <div class="info">
        <h4>Day Services</h4>
        <p><b>{{ number_format($dayServiceRevenueTZS, 0) }} TZS</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small danger coloured-icon">
      <i class="icon fa fa-dollar fa-2x"></i>
      <div class="info">
        <h4>Total Revenue</h4>
        <p><b>{{ number_format($totalRevenueTZS, 0) }} TZS</b></p>
        <small>≈ ${{ number_format($totalRevenueUSD ?? ($totalRevenueTZS / $exchangeRate), 2) }}</small>
      </div>
    </div>
  </div>
</div>

<!-- Charts Row -->
<div class="row mb-3">
  <!-- Revenue by Source Pie Chart -->
  <div class="col-md-6">
    <div class="tile">
      <h3 class="tile-title">Revenue by Source</h3>
      <div class="tile-body">
        <canvas id="revenueBySourceChart" height="250"></canvas>
      </div>
    </div>
  </div>
  
  <!-- Revenue by Payment Method -->
  <div class="col-md-6">
    <div class="tile">
      <h3 class="tile-title">Revenue by Payment Method</h3>
      <div class="tile-body">
        <canvas id="revenueByPaymentMethodChart" height="250"></canvas>
      </div>
    </div>
  </div>
</div>

<!-- Daily Revenue Trend -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title">Daily Revenue Trend - {{ $dateRange['label'] }}</h3>
      <div class="tile-body">
        <canvas id="dailyRevenueChart" height="100"></canvas>
      </div>
    </div>
  </div>
</div>

<!-- Revenue Details Tables -->
<div class="row mb-3">
  <!-- Revenue by Payment Method Table -->
  <div class="col-md-6">
    <div class="tile">
      <h3 class="tile-title">Revenue by Payment Method</h3>
      <div class="tile-body">
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>Payment Method</th>
                <th>Transactions</th>
                <th>Revenue (TZS)</th>
                <th>Percentage</th>
              </tr>
            </thead>
            <tbody>
              @php
                $totalTransactions = $revenueByPaymentMethod->sum('count');
              @endphp
              @foreach($revenueByPaymentMethod as $method => $data)
              <tr>
                <td><strong>{{ ucfirst($method ?? 'Unknown') }}</strong></td>
                <td>{{ $data['count'] }}</td>
                <td>{{ number_format($data['revenue_tzs'], 0) }}</td>
                <td>
                  @if($totalRevenueTZS > 0)
                    {{ number_format(($data['revenue_tzs'] / $totalRevenueTZS) * 100, 1) }}%
                  @else
                    0%
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Revenue by Guest Type -->
  <div class="col-md-6">
    <div class="tile">
      <h3 class="tile-title">Revenue by Guest Type</h3>
      <div class="tile-body">
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>Guest Type</th>
                <th>Bookings</th>
                <th>Revenue (TZS)</th>
                <th>Percentage</th>
              </tr>
            </thead>
            <tbody>
              @php
                $totalGuestBookings = $revenueByGuestType->sum('count');
              @endphp
              @foreach($revenueByGuestType as $type => $data)
              <tr>
                <td><strong>{{ ucfirst($type ?? 'Unknown') }}</strong></td>
                <td>{{ $data['count'] }}</td>
                <td>{{ number_format($data['revenue_tzs'], 0) }}</td>
                <td>
                  @if($totalRevenueTZS > 0)
                    {{ number_format(($data['revenue_tzs'] / $totalRevenueTZS) * 100, 1) }}%
                  @else
                    0%
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/chart.js') }}"></script>
<script>
function toggleDateInputs() {
  const reportType = document.getElementById('report_type').value;
  const singleDateContainer = document.getElementById('singleDateContainer');
  const startDateContainer = document.getElementById('startDateContainer');
  const endDateContainer = document.getElementById('endDateContainer');
  
  if (reportType === 'custom') {
    singleDateContainer.style.display = 'none';
    startDateContainer.style.display = 'block';
    endDateContainer.style.display = 'block';
  } else {
    singleDateContainer.style.display = 'block';
    startDateContainer.style.display = 'none';
    endDateContainer.style.display = 'none';
  }
}

// Revenue by Source Pie Chart
var revenueBySourceData = [
  {
    value: {{ $roomRevenueTZS }},
    color: "#3498db",
    highlight: "#5dade2",
    label: "Room Bookings"
  },
  {
    value: {{ $serviceRevenueTZS }},
    color: "#2ecc71",
    highlight: "#58d68d",
    label: "Service Requests"
  },
  {
    value: {{ $dayServiceRevenueTZS }},
    color: "#f39c12",
    highlight: "#f7dc6f",
    label: "Day Services"
  }
];

var ctx1 = document.getElementById("revenueBySourceChart").getContext("2d");
var revenueBySourceChart = new Chart(ctx1).Pie(revenueBySourceData, {
  responsive: true,
  maintainAspectRatio: false
});

// Revenue by Payment Method Bar Chart
var paymentMethodLabels = {!! json_encode($revenueByPaymentMethod->keys()->map(function($k) { return ucfirst($k ?? 'Unknown'); })->toArray()) !!};
var paymentMethodData = {!! json_encode($revenueByPaymentMethod->pluck('revenue_tzs')->toArray()) !!};

var ctx2 = document.getElementById("revenueByPaymentMethodChart").getContext("2d");
var revenueByPaymentMethodChart = new Chart(ctx2).Bar({
  labels: paymentMethodLabels,
  datasets: [{
    label: "Revenue (TZS)",
    fillColor: "rgba(151,187,205,0.5)",
    strokeColor: "rgba(151,187,205,0.8)",
    highlightFill: "rgba(151,187,205,0.75)",
    highlightStroke: "rgba(151,187,205,1)",
    data: paymentMethodData
  }]
}, {
  responsive: true,
  maintainAspectRatio: false,
  scaleLabel: "<%=value%> TZS"
});

// Daily Revenue Trend Line Chart
var dailyLabels = {!! json_encode($dailyRevenue->keys()->toArray()) !!};
var dailyData = {!! json_encode($dailyRevenue->values()->toArray()) !!};

var ctx3 = document.getElementById("dailyRevenueChart").getContext("2d");
var dailyRevenueChart = new Chart(ctx3).Line({
  labels: dailyLabels,
  datasets: [{
    label: "Daily Revenue (TZS)",
    fillColor: "rgba(220,220,220,0.2)",
    strokeColor: "rgba(151,187,205,1)",
    pointColor: "rgba(151,187,205,1)",
    pointStrokeColor: "#fff",
    pointHighlightFill: "#fff",
    pointHighlightStroke: "rgba(151,187,205,1)",
    data: dailyData
  }]
}, {
  responsive: true,
  maintainAspectRatio: false
});
</script>
@endsection
