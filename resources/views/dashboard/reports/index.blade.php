@extends('dashboard.layouts.reports')

@section('reports-content')
<style>
  .rd-search {
    max-width: 420px;
  }
  .rd-section {
    margin-bottom: 1.25rem;
  }
  .rd-section .tile {
    margin-bottom: 0;
  }
  .rd-section .tile-title {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 12px;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
    font-size: 1.05rem;
  }
  .rd-section .tile-title .rd-icon {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    flex-shrink: 0;
  }
  .rd-icon.kitchen { background: #c0392b; }
  .rd-icon.reception { background: #16a085; }
  .rd-icon.bookings { background: #d68910; }
  .rd-icon.housekeeping { background: #7f8c8d; }
  .rd-icon.day { background: #27ae60; }
  .rd-icon.finance { background: #1e8449; }
  .rd-icon.ops { background: #5d6d7e; }
  .rd-icon.staff { background: #5b2c6f; }

  .rd-links {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 8px;
  }
  @media (max-width: 767px) {
    .rd-links { grid-template-columns: 1fr; }
  }
  .rd-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    background: #fafbfc;
    color: #333;
    text-decoration: none !important;
    transition: border-color .15s ease, background .15s ease, box-shadow .15s ease;
  }
  .rd-link:hover {
    background: #fff;
    border-color: #009688;
    box-shadow: 0 2px 8px rgba(0,150,136,.12);
    color: #009688;
  }
  .rd-link i.rd-link-ico {
    width: 18px;
    text-align: center;
    color: #6c757d;
  }
  .rd-link:hover i.rd-link-ico { color: #009688; }
  .rd-link span {
    flex: 1;
    font-size: 0.92rem;
    line-height: 1.3;
  }
  .rd-link i.rd-chevron {
    color: #adb5bd;
    font-size: 12px;
  }
  .rd-link.is-hidden { display: none !important; }
  .rd-section.is-hidden { display: none !important; }
  .rd-empty {
    display: none;
    text-align: center;
    padding: 2rem 1rem;
    color: #6c757d;
  }
  .rd-empty.is-visible { display: block; }
</style>

<div class="app-title">
  <div>
    <h1><i class="fa fa-bar-chart"></i> Reports Dashboard</h1>
    <p>All hotel reports in one place — by department</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item">Reports</li>
  </ul>
</div>

{{-- Quick Stats --}}
<div class="row">
  <div class="col-md-6 col-lg-3">
    <div class="widget-small primary coloured-icon">
      <i class="icon fa fa-calendar-check-o fa-3x"></i>
      <div class="info">
        <h4>Today's Bookings</h4>
        <p><b>{{ $todayBookings ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-lg-3">
    <div class="widget-small success coloured-icon">
      <i class="icon fa fa-money fa-3x"></i>
      <div class="info">
        <h4>Today's Revenue</h4>
        <p><b>{{ number_format($todayRevenueTZS ?? 0, 0) }} TZS</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-lg-3">
    <div class="widget-small warning coloured-icon">
      <i class="icon fa fa-bed fa-3x"></i>
      <div class="info">
        <h4>Room Occupancy</h4>
        <p><b>{{ $occupancyRate ?? 0 }}%</b></p>
        <small>{{ $occupiedRooms ?? 0 }}/{{ $totalRooms ?? 0 }} rooms</small>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-lg-3">
    <div class="widget-small info coloured-icon">
      <i class="icon fa fa-line-chart fa-3x"></i>
      <div class="info">
        <h4>Month Revenue</h4>
        <p><b>{{ number_format($monthRevenueTZS ?? 0, 0) }} TZS</b></p>
      </div>
    </div>
  </div>
</div>

{{-- Search --}}
<div class="tile mb-3">
  <div class="d-flex flex-wrap align-items-center justify-content-between" style="gap: 12px;">
    <div>
      <h5 class="mb-0"><i class="fa fa-folder-open text-primary"></i> Browse Reports</h5>
      <small class="text-muted">Click a report to open it. Use search to filter quickly.</small>
    </div>
    <div class="rd-search input-group">
      <div class="input-group-prepend">
        <span class="input-group-text"><i class="fa fa-search"></i></span>
      </div>
      <input type="search" id="rdSearch" class="form-control" placeholder="Search reports..." autocomplete="off">
    </div>
  </div>
</div>

<div id="rdCatalog">
  {{-- Restaurant & Kitchen (includes order summary / drinks) --}}
  <div class="rd-section" data-section="restaurant">
    <div class="tile">
      <h3 class="tile-title">
        <span class="rd-icon kitchen"><i class="fa fa-cutlery"></i></span>
        Restaurant &amp; Kitchen
      </h3>
      <div class="rd-links">
        <a class="rd-link" href="{{ route('admin.restaurants.reports') }}" data-label="restaurant sales kitchen">
          <i class="fa fa-line-chart rd-link-ico"></i><span>Restaurant Sales</span><i class="fa fa-angle-right rd-chevron"></i>
        </a>
        <a class="rd-link" href="{{ route('bar-keeper.order-summary') }}" data-label="order summary restaurant">
          <i class="fa fa-bar-chart rd-link-ico"></i><span>Order Summary</span><i class="fa fa-angle-right rd-chevron"></i>
        </a>
        <a class="rd-link" href="{{ route('bar-keeper.order-summary.unpaid-report') }}" data-label="unpaid orders report restaurant">
          <i class="fa fa-file-excel-o rd-link-ico"></i><span>Unpaid Orders Report</span><i class="fa fa-angle-right rd-chevron"></i>
        </a>
        <a class="rd-link" href="{{ route('chef-master.reports') }}" data-label="kitchen daily stock sheet">
          <i class="fa fa-file-pdf-o rd-link-ico"></i><span>Kitchen Daily Stock Sheet</span><i class="fa fa-angle-right rd-chevron"></i>
        </a>
        <a class="rd-link" href="{{ route('chef-master.reports', ['type' => 'weekly']) }}" data-label="kitchen weekly stock summary">
          <i class="fa fa-calendar rd-link-ico"></i><span>Kitchen Weekly Stock Summary</span><i class="fa fa-angle-right rd-chevron"></i>
        </a>
        <a class="rd-link" href="{{ route('bar-keeper.reports') }}" data-label="drinks daily stock sheet restaurant">
          <i class="fa fa-file-pdf-o rd-link-ico"></i><span>Drinks Daily Stock Sheet</span><i class="fa fa-angle-right rd-chevron"></i>
        </a>
        <a class="rd-link" href="{{ route('admin.reports.other.food-cost-analysis') }}" data-label="food cost analysis">
          <i class="fa fa-calculator rd-link-ico"></i><span>Food Cost Analysis</span><i class="fa fa-angle-right rd-chevron"></i>
        </a>
        <a class="rd-link" href="{{ route('admin.reports.other.menu-performance') }}" data-label="menu performance">
          <i class="fa fa-book rd-link-ico"></i><span>Menu Performance</span><i class="fa fa-angle-right rd-chevron"></i>
        </a>
        <a class="rd-link" href="{{ route('admin.reports.other.bar-sales-analysis') }}" data-label="drinks sales analysis restaurant">
          <i class="fa fa-line-chart rd-link-ico"></i><span>Drinks Sales Analysis</span><i class="fa fa-angle-right rd-chevron"></i>
        </a>
      </div>
    </div>
  </div>

  {{-- Reception --}}
  <div class="rd-section" data-section="reception">
    <div class="tile">
      <h3 class="tile-title">
        <span class="rd-icon reception"><i class="fa fa-building"></i></span>
        Reception &amp; Front Office
      </h3>
      <div class="rd-links">
        <a class="rd-link" href="{{ route('reception.reports.financial') }}" data-label="reception financial report">
          <i class="fa fa-money rd-link-ico"></i><span>Reception Financial Report</span><i class="fa fa-angle-right rd-chevron"></i>
        </a>
        <a class="rd-link" href="{{ route('reception.reports') }}" data-label="reception operational report">
          <i class="fa fa-clipboard rd-link-ico"></i><span>Reception Operational Report</span><i class="fa fa-angle-right rd-chevron"></i>
        </a>
        <a class="rd-link" href="{{ route('admin.reports.daily') }}" data-label="daily reception report">
          <i class="fa fa-calendar-check-o rd-link-ico"></i><span>Daily Reception Report</span><i class="fa fa-angle-right rd-chevron"></i>
        </a>
        <a class="rd-link" href="{{ route('admin.reports.other.guest-demographics') }}" data-label="guest demographics">
          <i class="fa fa-users rd-link-ico"></i><span>Guest Demographics</span><i class="fa fa-angle-right rd-chevron"></i>
        </a>
        <a class="rd-link" href="{{ route('admin.reports.guest-satisfaction') }}" data-label="guest satisfaction">
          <i class="fa fa-smile-o rd-link-ico"></i><span>Guest Satisfaction</span><i class="fa fa-angle-right rd-chevron"></i>
        </a>
      </div>
    </div>
  </div>

  {{-- Bookings + Housekeeping + Day Services --}}
  <div class="rd-section" data-section="bookings-hk-day">
    <div class="tile">
      <h3 class="tile-title">
        <span class="rd-icon bookings"><i class="fa fa-calendar"></i></span>
        Bookings, Housekeeping &amp; Day Services
      </h3>
      <div class="rd-links">
        <a class="rd-link" href="{{ route('admin.reports.bookings.room-occupancy') }}" data-label="room occupancy bookings">
          <i class="fa fa-bed rd-link-ico"></i><span>Room Occupancy</span><i class="fa fa-angle-right rd-chevron"></i>
        </a>
        <a class="rd-link" href="{{ route('admin.reports.bookings.performance') }}" data-label="booking performance">
          <i class="fa fa-bar-chart rd-link-ico"></i><span>Booking Performance</span><i class="fa fa-angle-right rd-chevron"></i>
        </a>
        <a class="rd-link" href="{{ route('housekeeper.reports') }}" data-label="housekeeping consumption report">
          <i class="fa fa-file-pdf-o rd-link-ico"></i><span>Housekeeping Consumption</span><i class="fa fa-angle-right rd-chevron"></i>
        </a>
        <a class="rd-link" href="{{ route('admin.reports.other.issue-resolution') }}" data-label="issue resolution housekeeping">
          <i class="fa fa-wrench rd-link-ico"></i><span>Issue Resolution</span><i class="fa fa-angle-right rd-chevron"></i>
        </a>
        <a class="rd-link" href="{{ route('admin.day-services.reports') }}" data-label="day services report">
          <i class="fa fa-coffee rd-link-ico"></i><span>Day Services Report</span><i class="fa fa-angle-right rd-chevron"></i>
        </a>
      </div>
    </div>
  </div>

  {{-- Finance --}}
  <div class="rd-section" data-section="finance">
    <div class="tile">
      <h3 class="tile-title">
        <span class="rd-icon finance"><i class="fa fa-money"></i></span>
        Finance &amp; Payments
      </h3>
      <div class="rd-links">
        <a class="rd-link" href="{{ route('admin.reports.revenue-breakdown') }}" data-label="revenue breakdown finance">
          <i class="fa fa-pie-chart rd-link-ico"></i><span>Revenue Breakdown</span><i class="fa fa-angle-right rd-chevron"></i>
        </a>
        <a class="rd-link" href="{{ route('admin.reports.cash-flow') }}" data-label="cash flow">
          <i class="fa fa-exchange rd-link-ico"></i><span>Cash Flow</span><i class="fa fa-angle-right rd-chevron"></i>
        </a>
        <a class="rd-link" href="{{ route('admin.reports.profitability') }}" data-label="profitability">
          <i class="fa fa-calculator rd-link-ico"></i><span>Profitability</span><i class="fa fa-angle-right rd-chevron"></i>
        </a>
        <a class="rd-link" href="{{ route('admin.reports.revenue-forecast') }}" data-label="revenue forecast">
          <i class="fa fa-line-chart rd-link-ico"></i><span>Revenue Forecast</span><i class="fa fa-angle-right rd-chevron"></i>
        </a>
        <a class="rd-link" href="{{ route('admin.payments.reports') }}" data-label="payment reports cashier">
          <i class="fa fa-credit-card rd-link-ico"></i><span>Payment Reports</span><i class="fa fa-angle-right rd-chevron"></i>
        </a>
        <a class="rd-link" href="{{ route('admin.reports.other.payment-methods') }}" data-label="payment methods analysis">
          <i class="fa fa-credit-card rd-link-ico"></i><span>Payment Methods Analysis</span><i class="fa fa-angle-right rd-chevron"></i>
        </a>
        <a class="rd-link" href="{{ route('accountant.reports') }}" data-label="accountant purchase reports">
          <i class="fa fa-briefcase rd-link-ico"></i><span>Accountant / Purchase Reports</span><i class="fa fa-angle-right rd-chevron"></i>
        </a>
        <a class="rd-link" href="{{ route('admin.purchase-reports.index') }}" data-label="purchase reports">
          <i class="fa fa-shopping-cart rd-link-ico"></i><span>Purchase Reports</span><i class="fa fa-angle-right rd-chevron"></i>
        </a>
      </div>
    </div>
  </div>

  {{-- Operations --}}
  <div class="rd-section" data-section="operations">
    <div class="tile">
      <h3 class="tile-title">
        <span class="rd-icon ops"><i class="fa fa-cogs"></i></span>
        Operations
      </h3>
      <div class="rd-links">
        <a class="rd-link" href="{{ route('admin.reports.daily-operations') }}" data-label="daily operations">
          <i class="fa fa-calendar-check-o rd-link-ico"></i><span>Daily Operations</span><i class="fa fa-angle-right rd-chevron"></i>
        </a>
        <a class="rd-link" href="{{ route('admin.reports.weekly-performance') }}" data-label="weekly performance">
          <i class="fa fa-calendar rd-link-ico"></i><span>Weekly Performance</span><i class="fa fa-angle-right rd-chevron"></i>
        </a>
        <a class="rd-link" href="{{ route('admin.reports.general') }}" data-label="general overview">
          <i class="fa fa-dashboard rd-link-ico"></i><span>General Overview</span><i class="fa fa-angle-right rd-chevron"></i>
        </a>
        <a class="rd-link" href="{{ route('admin.reports.other.period-comparison') }}" data-label="period comparison">
          <i class="fa fa-exchange rd-link-ico"></i><span>Period Comparison</span><i class="fa fa-angle-right rd-chevron"></i>
        </a>
        <a class="rd-link" href="{{ route('admin.reports.other.service-response-time') }}" data-label="service response time">
          <i class="fa fa-clock-o rd-link-ico"></i><span>Service Response Time</span><i class="fa fa-angle-right rd-chevron"></i>
        </a>
      </div>
    </div>
  </div>

  {{-- Staff & Stock --}}
  <div class="rd-section" data-section="staff">
    <div class="tile">
      <h3 class="tile-title">
        <span class="rd-icon staff"><i class="fa fa-users"></i></span>
        Staff &amp; Stock
      </h3>
      <div class="rd-links">
        <a class="rd-link" href="{{ route('admin.reports.other.role-performance') }}" data-label="role based performance staff">
          <i class="fa fa-user-circle rd-link-ico"></i><span>Role-Based Performance</span><i class="fa fa-angle-right rd-chevron"></i>
        </a>
        <a class="rd-link" href="{{ route('admin.reports.other.staff-activity') }}" data-label="staff activity log">
          <i class="fa fa-history rd-link-ico"></i><span>Staff Activity Log</span><i class="fa fa-angle-right rd-chevron"></i>
        </a>
        <a class="rd-link" href="{{ route('admin.reports.other.staff-productivity') }}" data-label="staff productivity">
          <i class="fa fa-tasks rd-link-ico"></i><span>Staff Productivity</span><i class="fa fa-angle-right rd-chevron"></i>
        </a>
        <a class="rd-link" href="{{ route('admin.reports.other.stock-valuation') }}" data-label="stock valuation">
          <i class="fa fa-cubes rd-link-ico"></i><span>Stock Valuation</span><i class="fa fa-angle-right rd-chevron"></i>
        </a>
        <a class="rd-link" href="{{ route('admin.reports.other.satisfaction') }}" data-label="satisfaction reports">
          <i class="fa fa-smile-o rd-link-ico"></i><span>Satisfaction Reports</span><i class="fa fa-angle-right rd-chevron"></i>
        </a>
      </div>
    </div>
  </div>
</div>

<div class="rd-empty" id="rdEmpty">
  <i class="fa fa-search fa-2x mb-2"></i>
  <p class="mb-0">No reports match your search.</p>
</div>

{{-- Insights --}}
<div class="app-title mt-4 mb-2" style="margin-left:0; margin-right:0; background:transparent; box-shadow:none; border:none; padding:0;">
  <div>
    <h4 class="mb-0"><i class="fa fa-line-chart"></i> Quick Insights</h4>
  </div>
</div>

<div class="row">
  <div class="col-md-6 mb-3">
    <div class="tile">
      <h3 class="tile-title">Revenue Trend (Last 7 Days)</h3>
      <div class="embed-responsive embed-responsive-16by9">
        <canvas class="embed-responsive-item" id="revenueChart"></canvas>
      </div>
    </div>
  </div>
  <div class="col-md-6 mb-3">
    <div class="tile">
      <h3 class="tile-title">Booking Analytics</h3>
      <div class="embed-responsive embed-responsive-16by9">
        <canvas class="embed-responsive-item" id="bookingsChart"></canvas>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-6 mb-3">
    <div class="tile">
      <h3 class="tile-title">Revenue Snapshot</h3>
      <div class="tile-body pt-0">
        <ul class="list-group">
          <li class="list-group-item d-flex justify-content-between align-items-center">
            Today's Revenue
            <span class="badge badge-primary badge-pill">{{ number_format($todayRevenueTZS ?? 0) }} TZS</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            Month's Revenue
            <span class="badge badge-success badge-pill">{{ number_format($monthRevenueTZS ?? 0) }} TZS</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            Occupancy Rate
            <span class="badge badge-warning badge-pill">{{ $occupancyRate ?? 0 }}%</span>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript" src="{{ asset('dashboard_assets/js/plugins/chart.js') }}"></script>
<script type="text/javascript">
  (function () {
    var search = document.getElementById('rdSearch');
    var empty = document.getElementById('rdEmpty');
    if (search) {
      search.addEventListener('input', function () {
        var q = (search.value || '').toLowerCase().trim();
        var sections = document.querySelectorAll('.rd-section');
        var anyVisible = false;

        sections.forEach(function (section) {
          var links = section.querySelectorAll('.rd-link');
          var sectionHas = false;
          links.forEach(function (link) {
            var label = (link.getAttribute('data-label') || link.textContent || '').toLowerCase();
            var show = !q || label.indexOf(q) !== -1;
            link.classList.toggle('is-hidden', !show);
            if (show) sectionHas = true;
          });
          section.classList.toggle('is-hidden', !sectionHas);
          if (sectionHas) anyVisible = true;
        });

        empty.classList.toggle('is-visible', !anyVisible);
      });
    }

    var revenueData = {
      labels: {!! json_encode($chartLabels ?? []) !!},
      datasets: [{
        label: "Revenue (TZS)",
        fillColor: "rgba(40, 167, 69, 0.2)",
        strokeColor: "rgba(40, 167, 69, 1)",
        pointColor: "rgba(40, 167, 69, 1)",
        pointStrokeColor: "#fff",
        pointHighlightFill: "#fff",
        pointHighlightStroke: "rgba(220,220,220,1)",
        data: {!! json_encode($chartRevenue ?? []) !!}
      }]
    };

    var bookingsData = {
      labels: {!! json_encode($chartLabels ?? []) !!},
      datasets: [{
        label: "Bookings",
        fillColor: "rgba(255, 193, 7, 0.2)",
        strokeColor: "rgba(255, 193, 7, 1)",
        pointColor: "rgba(255, 193, 7, 1)",
        pointStrokeColor: "#fff",
        pointHighlightFill: "#fff",
        pointHighlightStroke: "rgba(151,187,205,1)",
        data: {!! json_encode($chartBookings ?? []) !!}
      }]
    };

    var ctxRevenue = document.getElementById("revenueChart").getContext("2d");
    new Chart(ctxRevenue).Line(revenueData);

    var ctxBooking = document.getElementById("bookingsChart").getContext("2d");
    new Chart(ctxBooking).Bar(bookingsData);
  })();
</script>
@endsection
