@extends('dashboard.layouts.reports')

@section('reports-content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-calendar-day"></i> Daily Operations Report</h1>
    <p>Today's operational snapshot and tomorrow's forecast</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></li>
    <li class="breadcrumb-item"><a href="#">Daily Operations</a></li>
  </ul>
</div>

<!-- Date Selector -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-body">
        <form method="GET" action="{{ route('admin.reports.daily-operations') }}" class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label for="date"><strong>Select Date:</strong></label>
              <input type="date" name="date" id="date" class="form-control" value="{{ $selectedDate->format('Y-m-d') }}">
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

<!-- Today's Summary -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-calendar-check-o"></i> {{ $selectedDate->format('l, F d, Y') }} - Operations Summary</h3>
      <div class="tile-body">
        <div class="row">
          <div class="col-md-3">
            <div class="widget-small primary coloured-icon">
              <i class="icon fa fa-calendar fa-2x"></i>
              <div class="info">
                <h4>New Bookings</h4>
                <p><b>{{ $todayNewBookings }}</b></p>
                <small>Confirmed: {{ $todayConfirmedBookings }}</small>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="widget-small success coloured-icon">
              <i class="icon fa fa-sign-in fa-2x"></i>
              <div class="info">
                <h4>Check-ins</h4>
                <p><b>{{ $todayCheckIns }}</b></p>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="widget-small info coloured-icon">
              <i class="icon fa fa-sign-out fa-2x"></i>
              <div class="info">
                <h4>Check-outs</h4>
                <p><b>{{ $todayCheckOuts }}</b></p>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="widget-small danger coloured-icon">
              <i class="icon fa fa-dollar fa-2x"></i>
              <div class="info">
                <h4>Total Daily Income</h4>
                <p><b>{{ number_format($grandTotalRevenueTZS, 0) }} TZS</b></p>
                <small>≈ ${{ number_format($grandTotalRevenueTZS / $exchangeRate, 2) }}</small>
                <div class="mt-2 small border-top pt-1 text-muted">
                  <div class="d-flex justify-content-between"><span>Bookings:</span> <span>{{ number_format($todayRevenueTZS, 0) }}</span></div>
                  <div class="d-flex justify-content-between"><span>Services (F&B):</span> <span>{{ number_format($todayServiceRevenueTZS, 0) }}</span></div>
                  <div class="d-flex justify-content-between"><span>Day Services:</span> <span>{{ number_format($todayDayServiceRevenueTZS, 0) }}</span></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Services & Activities -->
<div class="row mb-3">
  <div class="col-md-4">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-cutlery"></i> Service Requests</h3>
      <div class="tile-body">
        <div class="widget-small warning coloured-icon">
          <i class="icon fa fa-list fa-2x"></i>
          <div class="info">
            <h4>Total Requests</h4>
            <p><b>{{ $todayServiceRequestsCount }}</b></p>
            <small>Completed: {{ $todayServiceRequestsCompleted }}</small>
          </div>
        </div>
        <div class="mt-3">
          <strong>Total Order Revenue:</strong> {{ number_format($todayServiceRevenueTZS, 0) }} TZS
          <ul class="list-unstyled mt-2 small ml-3">
             <li class="d-flex justify-content-between"><span>Bar:</span> <span>{{ number_format($barRevenueTZS, 0) }}</span></li>
             <li class="d-flex justify-content-between"><span>Kitchen:</span> <span>{{ number_format($kitchenRevenueTZS, 0) }}</span></li>
             <li class="d-flex justify-content-between"><span>Other:</span> <span>{{ number_format($otherServiceRevenueTZS, 0) }}</span></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-calendar"></i> Day Services</h3>
      <div class="tile-body">
        <div class="widget-small info coloured-icon">
          <i class="icon fa fa-swimming-pool fa-2x"></i>
          <div class="info">
            <h4>Total Services</h4>
            <p><b>{{ $todayDayServicesCount }}</b></p>
            <small>Paid: {{ $todayDayServicesPaid }}</small>
          </div>
        </div>
        <div class="mt-3">
          <strong>Day Service Revenue:</strong> {{ number_format($todayDayServiceRevenueTZS, 0) }} TZS
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-exclamation-triangle"></i> Issues</h3>
      <div class="tile-body">
        <div class="widget-small danger coloured-icon">
          <i class="icon fa fa-warning fa-2x"></i>
          <div class="info">
            <h4>Reported</h4>
            <p><b>{{ $todayIssuesCount }}</b></p>
            <small>Resolved: {{ $todayIssuesResolved }}</small>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Room Occupancy -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-bed"></i> Current Room Status</h3>
      <div class="tile-body">
        <div class="row">
          <div class="col-md-3">
            <div class="widget-small info coloured-icon">
              <i class="icon fa fa-bed fa-2x"></i>
              <div class="info">
                <h4>Total Rooms</h4>
                <p><b>{{ $totalRooms }}</b></p>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="widget-small success coloured-icon">
              <i class="icon fa fa-check-circle fa-2x"></i>
              <div class="info">
                <h4>Available</h4>
                <p><b>{{ $availableRooms }}</b></p>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="widget-small warning coloured-icon">
              <i class="icon fa fa-broom fa-2x"></i>
              <div class="info" title="Rooms that are either being cleaned or under maintenance">
                <h4>Cleaning/Mainten.</h4>
                <p><b>{{ $cleaningMaintenanceRooms }}</b></p>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="widget-small primary coloured-icon">
              <i class="icon fa fa-users fa-2x"></i>
              <div class="info">
                <h4>Occupied</h4>
                <p><b>{{ $occupiedRooms }}</b></p>
                <small>Rate: {{ $occupancyRate }}%</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Tomorrow's Forecast -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-calendar-plus-o"></i> Tomorrow's Forecast - {{ $selectedDate->copy()->addDay()->format('l, F d, Y') }}</h3>
      <div class="tile-body">
        <div class="row">
          <div class="col-md-4">
            <div class="alert alert-info">
              <h5><i class="fa fa-sign-in"></i> Expected Check-ins</h5>
              <p class="mb-0"><strong>{{ $tomorrowCheckIns }}</strong> guests</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="alert alert-warning">
              <h5><i class="fa fa-sign-out"></i> Expected Check-outs</h5>
              <p class="mb-0"><strong>{{ $tomorrowCheckOuts }}</strong> guests</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="alert alert-success">
              <h5><i class="fa fa-dollar"></i> Expected Revenue</h5>
              <p class="mb-0"><strong>{{ number_format($tomorrowExpectedRevenueTZS, 0) }} TZS</strong></p>
              <small>≈ ${{ number_format($tomorrowExpectedRevenueTZS / $exchangeRate, 2) }}</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Pending Tasks -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-tasks"></i> Pending Tasks</h3>
      <div class="tile-body">
        <div class="row">
          <div class="col-md-4">
            <div class="alert alert-warning">
              <h5><i class="fa fa-list"></i> Pending Service Requests</h5>
              <p class="mb-0"><strong>{{ $pendingServiceRequests }}</strong> requests</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="alert alert-danger">
              <h5><i class="fa fa-exclamation-triangle"></i> Pending Issues</h5>
              <p class="mb-0"><strong>{{ $pendingIssues }}</strong> issues</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="alert alert-info">
              <h5><i class="fa fa-money"></i> Pending Payments</h5>
              <p class="mb-0"><strong>{{ $pendingPayments }}</strong> bookings</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
