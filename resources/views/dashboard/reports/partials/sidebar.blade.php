@php
  $activePage = request()->path();
  $isReportsPage = str_contains($activePage, 'admin/reports');
@endphp

<ul class="nav nav-pills flex-column" id="reportsSidebar" style="list-style: none; padding: 0;">
  {{-- Reports Dashboard --}}
  <li class="nav-item">
    <a class="nav-link {{ str_contains($activePage, 'admin/reports/index') && !str_contains($activePage, 'admin/reports/bookings') && !str_contains($activePage, 'admin/reports/revenue') && !str_contains($activePage, 'admin/reports/profitability') && !str_contains($activePage, 'admin/reports/cash-flow') && !str_contains($activePage, 'admin/reports/daily-operations') && !str_contains($activePage, 'admin/reports/weekly-performance') && !str_contains($activePage, 'admin/reports/guest-satisfaction') && !str_contains($activePage, 'admin/reports/general') && !str_contains($activePage, 'admin/reports/other') ? 'active' : '' }}" 
       href="{{ route('admin.reports.index') }}">
      <i class="fa fa-home"></i> Reports Dashboard
    </a>
  </li>
  
  <li class="nav-item" style="margin: 10px 0; border-top: 1px solid #eee; padding-top: 10px;"></li>
  
  {{-- Booking Reports --}}
  <li class="nav-item">
    <a class="nav-link {{ str_contains($activePage, 'admin/reports/bookings') ? 'active' : '' }}" 
       href="#" data-toggle="collapse" data-target="#bookingReports">
      <i class="fa fa-calendar-check-o"></i> Booking Reports
      <i class="fa fa-angle-down float-right"></i>
    </a>
    <ul class="collapse {{ str_contains($activePage, 'admin/reports/bookings') ? 'show' : '' }}" id="bookingReports">
      <li class="nav-item">
        <a class="nav-link {{ str_contains($activePage, 'admin/reports/bookings/room-occupancy') ? 'active' : '' }}" 
           href="{{ route('admin.reports.bookings.room-occupancy') }}">
          <i class="fa fa-bed"></i> Room Occupancy
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ str_contains($activePage, 'admin/reports/bookings/performance') ? 'active' : '' }}" 
           href="{{ route('admin.reports.bookings.performance') }}">
          <i class="fa fa-bar-chart"></i> Booking Performance
        </a>
      </li>
    </ul>
  </li>

  {{-- Day Services Reports --}}
  <li class="nav-item">
    <a class="nav-link {{ str_contains($activePage, 'admin/day-services/reports') ? 'active' : '' }}" 
       href="{{ route('admin.day-services.reports') }}">
      <i class="fa fa-coffee"></i> Day Services Reports
    </a>
  </li>

  {{-- Bar & Drinks Reports --}}
  <li class="nav-item">
    <a class="nav-link {{ str_contains($activePage, 'bar-keeper/reports') ? 'active' : '' }}" 
       href="{{ route('bar-keeper.reports') }}">
      <i class="fa fa-glass"></i> Bar & Drinks Reports
    </a>
  </li>

  {{-- Kitchen & Food Reports --}}
  <li class="nav-item">
    <a class="nav-link {{ str_contains($activePage, 'restaurant-reports') ? 'active' : '' }}" 
       href="{{ route('admin.restaurants.reports') }}">
      <i class="fa fa-cutlery"></i> Kitchen & Food Reports
    </a>
  </li>

  {{-- Financial Reports --}}
  <li class="nav-item">
    <a class="nav-link {{ str_contains($activePage, 'admin/reports') && (str_contains($activePage, 'revenue') || str_contains($activePage, 'profitability') || str_contains($activePage, 'cash-flow')) ? 'active' : '' }}" 
       href="#" data-toggle="collapse" data-target="#financialReports">
      <i class="fa fa-dollar"></i> Financial Reports
      <i class="fa fa-angle-down float-right"></i>
    </a>
    <ul class="collapse {{ str_contains($activePage, 'admin/reports') && (str_contains($activePage, 'revenue') || str_contains($activePage, 'profitability') || str_contains($activePage, 'cash-flow')) ? 'show' : '' }}" id="financialReports">
      <li class="nav-item">
        <a class="nav-link {{ str_contains($activePage, 'admin/reports/revenue-breakdown') ? 'active' : '' }}" 
           href="{{ route('admin.reports.revenue-breakdown') }}">
          <i class="fa fa-line-chart"></i> Revenue Breakdown
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ str_contains($activePage, 'admin/reports/profitability') ? 'active' : '' }}" 
           href="{{ route('admin.reports.profitability') }}">
          <i class="fa fa-calculator"></i> Profitability
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ str_contains($activePage, 'admin/reports/cash-flow') ? 'active' : '' }}" 
           href="{{ route('admin.reports.cash-flow') }}">
          <i class="fa fa-money"></i> Cash Flow
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ str_contains($activePage, 'admin/reports/revenue-forecast') ? 'active' : '' }}" 
           href="{{ route('admin.reports.revenue-forecast') }}">
          <i class="fa fa-trending-up"></i> Revenue Forecast
        </a>
      </li>
    </ul>
  </li>

  {{-- Operational Reports --}}
  <li class="nav-item">
    <a class="nav-link {{ str_contains($activePage, 'admin/reports') && (str_contains($activePage, 'daily-operations') || str_contains($activePage, 'weekly-performance')) ? 'active' : '' }}" 
       href="#" data-toggle="collapse" data-target="#operationalReports">
      <i class="fa fa-cogs"></i> Operational Reports
      <i class="fa fa-angle-down float-right"></i>
    </a>
    <ul class="collapse {{ str_contains($activePage, 'admin/reports') && (str_contains($activePage, 'daily-operations') || str_contains($activePage, 'weekly-performance') || str_contains($activePage, 'guest-satisfaction')) ? 'show' : '' }}" id="operationalReports">
      <li class="nav-item">
        <a class="nav-link {{ str_contains($activePage, 'admin/reports/daily-operations') ? 'active' : '' }}" 
           href="{{ route('admin.reports.daily-operations') }}">
          <i class="fa fa-calendar-check-o"></i> Daily Operations
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ str_contains($activePage, 'admin/reports/weekly-performance') ? 'active' : '' }}" 
           href="{{ route('admin.reports.weekly-performance') }}">
          <i class="fa fa-calendar-week"></i> Weekly Performance
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ str_contains($activePage, 'admin/reports/guest-satisfaction') ? 'active' : '' }}" 
           href="{{ route('admin.reports.guest-satisfaction') }}">
          <i class="fa fa-smile-o"></i> Guest Satisfaction
        </a>
      </li>
    </ul>
  </li>

  {{-- General Report --}}
  <li class="nav-item">
    <a class="nav-link {{ (str_contains($activePage, 'admin/reports/index') || str_contains($activePage, 'admin/reports/general')) && !str_contains($activePage, 'bookings') && !str_contains($activePage, 'revenue') && !str_contains($activePage, 'profitability') && !str_contains($activePage, 'cash-flow') && !str_contains($activePage, 'daily-operations') && !str_contains($activePage, 'weekly-performance') && !str_contains($activePage, 'guest-satisfaction') && !str_contains($activePage, 'other') ? 'active' : '' }}" 
       href="{{ route('admin.reports.general') }}">
      <i class="fa fa-dashboard"></i> General Report
    </a>
  </li>

  {{-- Other Reports --}}
  <li class="nav-item">
    <a class="nav-link {{ str_contains($activePage, 'admin/reports/other') ? 'active' : '' }}" 
       href="#" data-toggle="collapse" data-target="#otherReports">
      <i class="fa fa-file-text"></i> Other Reports
      <i class="fa fa-angle-down float-right"></i>
    </a>
    <ul class="collapse {{ str_contains($activePage, 'admin/reports/other') ? 'show' : '' }}" id="otherReports">
      <li class="nav-item">
        <a class="nav-link {{ str_contains($activePage, 'admin/reports/other/payment-methods') ? 'active' : '' }}" 
           href="{{ route('admin.reports.other.payment-methods') }}">
          <i class="fa fa-credit-card"></i> Payment Methods
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ str_contains($activePage, 'admin/reports/other/satisfaction') ? 'active' : '' }}" 
           href="{{ route('admin.reports.other.satisfaction') }}">
          <i class="fa fa-smile-o"></i> Satisfaction Reports
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ str_contains($activePage, 'admin/reports/other/period-comparison') ? 'active' : '' }}" 
           href="{{ route('admin.reports.other.period-comparison') }}">
          <i class="fa fa-exchange"></i> Period Comparison
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ str_contains($activePage, 'admin/reports/other/role-performance') ? 'active' : '' }}" 
           href="{{ route('admin.reports.other.role-performance') }}">
          <i class="fa fa-users"></i> Role-Based Performance
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ str_contains($activePage, 'admin/reports/other/staff-activity') ? 'active' : '' }}" 
           href="{{ route('admin.reports.other.staff-activity') }}">
          <i class="fa fa-history"></i> Staff Activity Log
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ str_contains($activePage, 'admin/reports/other/staff-productivity') ? 'active' : '' }}" 
           href="{{ route('admin.reports.other.staff-productivity') }}">
          <i class="fa fa-tasks"></i> Staff Productivity
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ str_contains($activePage, 'admin/reports/other/issue-resolution') ? 'active' : '' }}" 
           href="{{ route('admin.reports.other.issue-resolution') }}">
          <i class="fa fa-wrench"></i> Issue Resolution
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ str_contains($activePage, 'admin/reports/other/service-response-time') ? 'active' : '' }}" 
           href="{{ route('admin.reports.other.service-response-time') }}">
          <i class="fa fa-clock-o"></i> Service Response Time
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ str_contains($activePage, 'admin/reports/other/stock-valuation') ? 'active' : '' }}" 
           href="{{ route('admin.reports.other.stock-valuation') }}">
          <i class="fa fa-cubes"></i> Stock Valuation
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ str_contains($activePage, 'admin/reports/other/food-cost-analysis') ? 'active' : '' }}" 
           href="{{ route('admin.reports.other.food-cost-analysis') }}">
          <i class="fa fa-cutlery"></i> Food Cost Analysis
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ str_contains($activePage, 'admin/reports/other/bar-sales-analysis') ? 'active' : '' }}" 
           href="{{ route('admin.reports.other.bar-sales-analysis') }}">
          <i class="fa fa-glass"></i> Bar Sales Analysis
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ str_contains($activePage, 'admin/reports/other/menu-performance') ? 'active' : '' }}" 
           href="{{ route('admin.reports.other.menu-performance') }}">
          <i class="fa fa-book"></i> Menu Performance
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ str_contains($activePage, 'admin/reports/other/guest-demographics') ? 'active' : '' }}" 
           href="{{ route('admin.reports.other.guest-demographics') }}">
          <i class="fa fa-user"></i> Guest Demographics
        </a>
      </li>
    </ul>
  </li>
</ul>

<style>
#reportsSidebar {
  list-style: none;
  padding: 0;
  margin: 0;
}

#reportsSidebar .nav-item {
  margin-bottom: 5px;
}

#reportsSidebar .nav-link {
  color: #333;
  padding: 12px 15px;
  border-radius: 5px;
  display: block;
  text-decoration: none;
  transition: all 0.3s;
  border-left: 3px solid transparent;
}

#reportsSidebar .nav-link:hover {
  background-color: #f5f5f5;
  color: #e07632;
  border-left-color: #e07632;
}

#reportsSidebar .nav-link.active {
  background-color: #e07632;
  color: white;
  border-left-color: #c55a1f;
  font-weight: 600;
}

#reportsSidebar .nav-link i.fa-angle-down {
  transition: transform 0.3s;
  float: right;
  margin-top: 3px;
}

#reportsSidebar .nav-link[aria-expanded="true"] i.fa-angle-down {
  transform: rotate(180deg);
}

#reportsSidebar .collapse {
  margin-top: 5px;
}

#reportsSidebar .collapse ul {
  list-style: none;
  padding-left: 25px;
  margin: 0;
}

#reportsSidebar .collapse .nav-item {
  margin-bottom: 3px;
}

#reportsSidebar .collapse .nav-link {
  font-size: 0.9em;
  padding: 8px 15px;
  border-left: 2px solid transparent;
}

#reportsSidebar .collapse .nav-link:hover {
  border-left-color: #e07632;
}

#reportsSidebar .collapse .nav-link.active {
  border-left-color: #c55a1f;
}
</style>
