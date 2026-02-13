@extends('dashboard.layouts.reports')

@section('reports-content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-bar-chart"></i> Booking Performance Report</h1>
    <p>Analyze booking statistics and performance metrics</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></li>
    <li class="breadcrumb-item"><a href="#">Booking Performance</a></li>
  </ul>
</div>

<!-- Report Filter -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-body">
        <form method="GET" action="{{ route('admin.reports.bookings.performance') }}" id="reportForm">
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

<!-- Summary Cards -->
<div class="row mb-3">
  <div class="col-md-3">
    <div class="widget-small primary coloured-icon">
      <i class="icon fa fa-calendar-check-o fa-2x"></i>
      <div class="info">
        <h4>Total Bookings</h4>
        <p><b>{{ $totalBookings }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small success coloured-icon">
      <i class="icon fa fa-check-circle fa-2x"></i>
      <div class="info">
        <h4 style="color: #000;">Confirmed</h4>
        <p style="color: #000;"><b>{{ $confirmedBookings }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small danger coloured-icon">
      <i class="icon fa fa-times-circle fa-2x"></i>
      <div class="info">
        <h4>Cancelled</h4>
        <p><b>{{ $cancelledBookings }}</b></p>
        <small>{{ $cancellationRate }}% rate</small>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small info coloured-icon">
      <i class="icon fa fa-money fa-2x"></i>
      <div class="info">
        <h4>Booking Revenue</h4>
        <p><b>{{ number_format($totalRevenueTZS, 0) }} TZS</b></p>
      </div>
    </div>
  </div>
</div>

<!-- Performance Metrics -->
<div class="row mb-3">
  <div class="col-md-6">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-sign-in"></i> Check-In/Check-Out Statistics</h3>
      <div class="tile-body">
        <div class="row">
          <div class="col-md-6">
            <div class="widget-small warning coloured-icon">
              <i class="icon fa fa-sign-in fa-2x"></i>
              <div class="info">
                <h4>Check-Ins</h4>
                <p><b>{{ $checkedInBookings }}</b></p>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="widget-small info coloured-icon">
              <i class="icon fa fa-sign-out fa-2x"></i>
              <div class="info">
                <h4>Check-Outs</h4>
                <p><b>{{ $checkedOutBookings }}</b></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-calculator"></i> Key Metrics</h3>
      <div class="tile-body">
        <table class="table table-bordered">
          <tr>
            <td><strong>Avg Revenue per Booking:</strong></td>
            <td><strong>{{ number_format($avgBookingValueTZS, 0) }} TZS</strong></td>
          </tr>
          <tr>
            <td><strong>Average Lead Time:</strong></td>
            <td><strong>{{ $avgLeadTime }}</strong> days</td>
          </tr>
          <tr>
            <td><strong>Cancellation Rate:</strong></td>
            <td>
              <span class="badge badge-{{ $cancellationRate > 20 ? 'danger' : ($cancellationRate > 10 ? 'warning' : 'success') }}">
                {{ $cancellationRate }}%
              </span>
            </td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Booking Status Breakdown -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-pie-chart"></i> Booking Status Breakdown</h3>
      <div class="tile-body">
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>Status</th>
                <th>Count</th>
                <th>Percentage</th>
                <th>Visual</th>
              </tr>
            </thead>
            <tbody>
              @forelse($statusBreakdown as $status)
              @php
                $percentage = $totalBookings > 0 ? round(($status->count / $totalBookings) * 100, 1) : 0;
                $badgeClass = $status->status == 'confirmed' ? 'success' : ($status->status == 'cancelled' ? 'danger' : 'warning');
              @endphp
              <tr>
                <td>
                  <span class="badge badge-{{ $badgeClass }}">{{ ucfirst($status->status) }}</span>
                </td>
                <td><strong>{{ $status->count }}</strong></td>
                <td>{{ $percentage }}%</td>
                <td>
                  <div class="progress" style="height: 20px;">
                    <div class="progress-bar bg-{{ $badgeClass }}" 
                         role="progressbar" 
                         style="width: {{ $percentage }}%"
                         aria-valuenow="{{ $percentage }}" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                      {{ $percentage }}%
                    </div>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="4" class="text-center">No data available</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Quick Actions -->
<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-body text-center">
        <a href="{{ route('admin.reports.bookings.room-occupancy') }}" class="btn btn-info">
          <i class="fa fa-bed"></i> View Room Occupancy
        </a>
        <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary">
          <i class="fa fa-list"></i> Back to Bookings
        </a>
      </div>
    </div>
  </div>
</div>

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
</script>
@endsection
