@extends('dashboard.layouts.reports')

@section('reports-content')
<!-- Quick Stats Overview -->
<div class="row mb-4">
  <div class="col-md-3">
    <div class="widget-small primary coloured-icon">
      <i class="icon fa fa-calendar-check-o fa-3x"></i>
      <div class="info">
        <h4>Today's Bookings</h4>
        <p><b>{{ $todayBookings ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small success coloured-icon">
      <i class="icon fa fa-money fa-3x"></i>
      <div class="info">
        <h4>Today's Revenue</h4>
        <p><b>{{ number_format($todayRevenueTZS ?? 0, 0) }} TZS</b></p>
        <small>≈ ${{ number_format($todayRevenueUSD ?? 0, 2) }}</small>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small warning coloured-icon">
      <i class="icon fa fa-bed fa-3x"></i>
      <div class="info">
        <h4>Room Occupancy</h4>
        <p><b>{{ $occupancyRate ?? 0 }}%</b></p>
        <small>{{ $occupiedRooms ?? 0 }}/{{ $totalRooms ?? 0 }} rooms</small>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small info coloured-icon">
      <i class="icon fa fa-line-chart fa-3x"></i>
      <div class="info">
        <h4>Month Revenue</h4>
        <p><b>{{ number_format($monthRevenueTZS ?? 0, 0) }} TZS</b></p>
        <small>≈ ${{ number_format($monthRevenueUSD ?? 0, 2) }}</small>
      </div>
    </div>
  </div>
</div>



<!-- Report Categories Quick Access -->
<div class="row">
  <div class="col-md-6 mb-3">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-calendar-check-o text-primary"></i> Quick Access</h3>
      <div class="tile-body">
        <div class="list-group">
          <a href="{{ route('admin.reports.daily-operations') }}" class="list-group-item list-group-item-action">
            <i class="fa fa-calendar-check-o text-primary"></i> Daily Operations Report
          </a>
          <a href="{{ route('admin.reports.revenue-breakdown') }}" class="list-group-item list-group-item-action">
            <i class="fa fa-line-chart text-success"></i> Revenue Breakdown
          </a>
          <a href="{{ route('admin.reports.general') }}" class="list-group-item list-group-item-action">
            <i class="fa fa-dashboard text-info"></i> General Overview Report
          </a>
          <a href="{{ route('admin.reports.bookings.room-occupancy') }}" class="list-group-item list-group-item-action">
            <i class="fa fa-bed text-warning"></i> Room Occupancy Report
          </a>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-md-6 mb-3">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-line-chart text-success"></i> Revenue Trend (Last 7 Days)</h3>
      <div class="embed-responsive embed-responsive-16by9">
        <canvas class="embed-responsive-item" id="revenueChart"></canvas>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-6 mb-3">
    <div class="tile">
       <h3 class="tile-title"><i class="fa fa-bar-chart text-warning"></i> Booking Analytics</h3>
       <div class="embed-responsive embed-responsive-16by9">
         <canvas class="embed-responsive-item" id="bookingsChart"></canvas>
       </div>
    </div>
  </div>
  
  <div class="col-md-6 mb-3">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-pie-chart text-info"></i> Revenue Composition</h3>
      <div class="tile-body">
          <p>Real-time tracking of revenue streams.</p>
          <ul class="list-group">
            <li class="list-group-item d-flex justify-content-between align-items-center">
                Today's Revenue
                <span class="badge badge-primary badge-pill">{{ number_format($todayRevenueTZS) }} TZS</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                Month's Revenue
                <span class="badge badge-success badge-pill">{{ number_format($monthRevenueTZS) }} TZS</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                Occupancy Rate
                <span class="badge badge-warning badge-pill">{{ $occupancyRate }}%</span>
            </li>
          </ul>
      </div>
    </div>
  </div>
</div>

<!-- Page specific javascripts-->
<script type="text/javascript" src="{{ asset('dashboard_assets/js/plugins/chart.js') }}"></script>
<script type="text/javascript">
  var revenueData = {
      labels: {!! json_encode($chartLabels) !!},
      datasets: [
          {
              label: "Revenue (TZS)",
              fillColor: "rgba(40, 167, 69, 0.2)",
              strokeColor: "rgba(40, 167, 69, 1)",
              pointColor: "rgba(40, 167, 69, 1)",
              pointStrokeColor: "#fff",
              pointHighlightFill: "#fff",
              pointHighlightStroke: "rgba(220,220,220,1)",
              data: {!! json_encode($chartRevenue) !!}
          }
      ]
  };
  
  var bookingsData = {
      labels: {!! json_encode($chartLabels) !!},
      datasets: [
          {
              label: "Bookings",
              fillColor: "rgba(255, 193, 7, 0.2)",
              strokeColor: "rgba(255, 193, 7, 1)",
              pointColor: "rgba(255, 193, 7, 1)",
              pointStrokeColor: "#fff",
              pointHighlightFill: "#fff",
              pointHighlightStroke: "rgba(151,187,205,1)",
              data: {!! json_encode($chartBookings) !!}
          }
      ]
  };

  var ctxRevenue = document.getElementById("revenueChart").getContext("2d");
  var revenueChart = new Chart(ctxRevenue).Line(revenueData);
  
  var ctxBooking = document.getElementById("bookingsChart").getContext("2d");
  var bookingChart = new Chart(ctxBooking).Bar(bookingsData);
</script>
</div>
@endsection
