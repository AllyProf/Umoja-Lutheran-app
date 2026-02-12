@extends('dashboard.layouts.reports')

@section('reports-content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-bed"></i> Room Occupancy Report</h1>
    <p>Track room utilization and identify underperforming rooms</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></li>
    <li class="breadcrumb-item"><a href="#">Room Occupancy</a></li>
  </ul>
</div>

<!-- Report Filter -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-body">
        <form method="GET" action="{{ route('admin.reports.bookings.room-occupancy') }}" id="reportForm">
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
  <div class="col-md-4">
    <div class="widget-small primary coloured-icon">
      <i class="icon fa fa-bed fa-2x"></i>
      <div class="info">
        <h4>Total Rooms</h4>
        <p><b>{{ $totalRooms }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="widget-small info coloured-icon">
      <i class="icon fa fa-key fa-2x"></i>
      <div class="info">
        <h4>Occupied Rooms</h4>
        <p><b>{{ $occupiedRooms }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="widget-small warning coloured-icon">
      <i class="icon fa fa-percent fa-2x"></i>
      <div class="info">
        <h4>Current Occupancy (Live)</h4>
        <p><b>{{ $overallOccupancyRate }}%</b> <small>({{ $occupiedRooms }}/{{ $totalRooms }} rooms)</small></p>
      </div>
    </div>
  </div>
</div>

<!-- Occupancy by Room Type -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-bar-chart"></i> Occupancy by Room Type (Selected Period)</h3>
      <div class="tile-body">
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>Room Type</th>
                <th>Total Rooms</th>
                <th>Avg Occupancy</th>
                <th>Revenue (TZS)</th>
                <th>Revenue (USD)</th>
              </tr>
            </thead>
            <tbody>
              @forelse($occupancyByType as $type)
              <tr>
                <td><strong>{{ ucfirst($type['type']) }}</strong></td>
                <td>{{ $type['total_rooms'] }}</td>
                <td>
                  <div class="progress" style="height: 20px;">
                    <div class="progress-bar {{ $type['occupancy_rate'] >= 70 ? 'bg-success' : ($type['occupancy_rate'] >= 50 ? 'bg-warning' : 'bg-danger') }}" 
                         role="progressbar" 
                         style="width: {{ $type['occupancy_rate'] }}%"
                         aria-valuenow="{{ $type['occupancy_rate'] }}" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                      {{ $type['occupancy_rate'] }}%
                    </div>
                  </div>
                </td>
                <td><strong>{{ number_format($type['revenue_usd'] * $exchangeRate, 0) }}</strong></td>
                <td>${{ number_format($type['revenue_usd'], 2) }}</td>
              </tr>
              @empty
              <tr>
                <td colspan="5" class="text-center">No data available</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Top Performing Rooms -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-trophy"></i> Top 10 Performing Rooms</h3>
      <div class="tile-body">
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>Rank</th>
                <th>Room Number</th>
                <th>Room Type</th>
                <th>Number of Bookings</th>
                <th>Revenue (TZS)</th>
                <th>Revenue (USD)</th>
              </tr>
            </thead>
            <tbody>
              @forelse($topRooms as $index => $roomData)
              @if($roomData['room'])
              <tr>
                <td>
                  @if($index == 0)
                    <span class="badge badge-success">#1</span>
                  @elseif($index == 1)
                    <span class="badge badge-info">#2</span>
                  @elseif($index == 2)
                    <span class="badge badge-warning">#3</span>
                  @else
                    <span class="badge badge-secondary">#{{ $index + 1 }}</span>
                  @endif
                </td>
                <td><strong>{{ $roomData['room']->room_number ?? 'N/A' }}</strong></td>
                <td>{{ ucfirst($roomData['room']->room_type ?? 'N/A') }}</td>
                <td>{{ $roomData['booking_count'] }}</td>
                <td><strong>{{ number_format($roomData['revenue_tzs'], 0) }}</strong></td>
                <td>${{ number_format($roomData['revenue_usd'], 2) }}</td>
              </tr>
              @endif
              @empty
              <tr>
                <td colspan="6" class="text-center">No bookings found for this period</td>
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
        <button onclick="window.print()" class="btn btn-primary">
          <i class="fa fa-print"></i> Print Report
        </button>
        <a href="{{ route('admin.reports.bookings.performance') }}" class="btn btn-info">
          <i class="fa fa-bar-chart"></i> View Booking Performance
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
