@extends('dashboard.layouts.reports')

@section('reports-content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-calendar-week"></i> Weekly Performance Report</h1>
    <p>Weekly summary and performance analysis</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></li>
    <li class="breadcrumb-item"><a href="#">Weekly Performance</a></li>
  </ul>
</div>

<!-- Week Selector -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-body">
        <form method="GET" action="{{ route('admin.reports.weekly-performance') }}" class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label for="week_start"><strong>Week Starting:</strong></label>
              <input type="date" name="week_start" id="week_start" class="form-control" value="{{ $weekStartDate->format('Y-m-d') }}">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label>&nbsp;</label>
              <button type="submit" class="btn btn-primary btn-block">
                <i class="fa fa-calendar"></i> View Report
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Week Summary -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-calendar"></i> Week of {{ $weekStartDate->format('M d') }} - {{ $weekEndDate->format('M d, Y') }}</h3>
      <div class="tile-body">
        <div class="row">
          <div class="col-md-3">
            <div class="widget-small primary coloured-icon">
              <i class="icon fa fa-calendar fa-2x"></i>
              <div class="info">
                <h4>Total Bookings</h4>
                <p><b>{{ $weekBookingsCount }}</b></p>
                <small>Confirmed: {{ $weekConfirmedBookings }}</small>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="widget-small danger coloured-icon">
              <i class="icon fa fa-money fa-2x"></i>
              <div class="info">
                <h4>Week Revenue</h4>
                <p><b>{{ number_format($weekRevenueTZS, 0) }} TZS</b></p>
                <small style="color: {{ $revenueChange >= 0 ? '#28a745' : '#dc3545' }};">
                  {{ $revenueChange >= 0 ? '+' : '' }}{{ $revenueChange }}% vs last week
                </small>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="widget-small success coloured-icon">
              <i class="icon fa fa-sign-in fa-2x"></i>
              <div class="info">
                <h4>Check-ins</h4>
                <p><b>{{ $weekCheckIns }}</b></p>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="widget-small info coloured-icon">
              <i class="icon fa fa-sign-out fa-2x"></i>
              <div class="info">
                <h4>Check-outs</h4>
                <p><b>{{ $weekCheckOuts }}</b></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Daily Breakdown -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-bar-chart"></i> Daily Breakdown</h3>
      <div class="tile-body">
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>Day</th>
                <th>Date</th>
                <th>Bookings</th>
                <th>Revenue (TZS)</th>
              </tr>
            </thead>
            <tbody>
              @foreach($dailyBreakdown as $day)
              <tr class="{{ in_array($day, $topDays->pluck('day')->toArray()) ? 'table-success' : '' }}">
                <td><strong>{{ $day['day'] }}</strong></td>
                <td>{{ $day['date'] }}</td>
                <td>{{ $day['bookings'] }}</td>
                <td><strong>{{ number_format($day['revenue'], 0) }}</strong></td>
              </tr>
              @endforeach
              <tr class="table-info">
                <td colspan="2"><strong>Total</strong></td>
                <td><strong>{{ $weekBookingsCount }}</strong></td>
                <td><strong>{{ number_format($weekRevenueTZS, 0) }} TZS</strong></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Services & Activities -->
<div class="row mb-3">
  <div class="col-md-6">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-cutlery"></i> Service Requests</h3>
      <div class="tile-body">
        <div class="widget-small warning coloured-icon">
          <i class="icon fa fa-list fa-2x"></i>
          <div class="info">
            <h4>Total Requests</h4>
            <p><b>{{ $weekServiceRequestsCount }}</b></p>
            <small>Completed: {{ $weekServiceRequestsCompleted }}</small>
          </div>
        </div>
        <div class="mt-3">
          <strong>Service Revenue:</strong> {{ number_format($weekServiceRevenueTZS, 0) }} TZS
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-calendar"></i> Day Services</h3>
      <div class="tile-body">
        <div class="widget-small info coloured-icon">
          <i class="icon fa fa-swimming-pool fa-2x"></i>
          <div class="info">
            <h4>Total Services</h4>
            <p><b>{{ $weekDayServicesCount }}</b></p>
          </div>
        </div>
        <div class="mt-3">
          <strong>Day Service Revenue:</strong> {{ number_format($weekDayServiceRevenueTZS, 0) }} TZS
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Highlights & Challenges -->
<div class="row mb-3">
  <div class="col-md-6">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-star"></i> Highlights</h3>
      <div class="tile-body">
        @if(count($highlights) > 0)
          <ul class="list-group">
            @foreach($highlights as $highlight)
            <li class="list-group-item list-group-item-success">
              <i class="fa fa-check-circle"></i> {{ $highlight }}
            </li>
            @endforeach
          </ul>
        @else
          <p class="text-muted">No specific highlights this week.</p>
        @endif
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-exclamation-triangle"></i> Challenges</h3>
      <div class="tile-body">
        @if(count($challenges) > 0)
          <ul class="list-group">
            @foreach($challenges as $challenge)
            <li class="list-group-item list-group-item-{{ $challenge['type'] }}">
              <i class="fa fa-warning"></i> {{ $challenge['message'] }}
            </li>
            @endforeach
          </ul>
        @else
          <p class="text-success"><i class="fa fa-check-circle"></i> No major challenges this week.</p>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Next Week Outlook -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-eye"></i> Next Week Outlook</h3>
      <div class="tile-body">
        <div class="row">
          <div class="col-md-4">
            <div class="alert alert-info">
              <h5><i class="fa fa-calendar"></i> Expected Bookings</h5>
              <p class="mb-0"><strong>{{ $nextWeekBookings }}</strong> bookings</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="alert alert-success">
              <h5><i class="fa fa-money"></i> Expected Revenue</h5>
              <p class="mb-0"><strong>{{ number_format($nextWeekExpectedRevenueTZS, 0) }} TZS</strong></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
