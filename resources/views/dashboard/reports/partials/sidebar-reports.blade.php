@php
  $activePage = request()->path();
@endphp

{{-- Reports Dashboard --}}
<li>
  <a class="app-menu__item {{ str_contains($activePage, 'reports/index') ? 'active' : '' }}" 
     href="{{ route('admin.reports.index') }}">
    <i class="app-menu__icon fa fa-home"></i>
    <span class="app-menu__label">Reports Dashboard</span>
  </a>
</li>

{{-- DAILY STOCK SHEETS (VITAL) --}}
<li class="treeview {{ str_contains($activePage, 'bar-keeper/reports') || str_contains($activePage, 'chef-master/reports') || str_contains($activePage, 'housekeeper/reports') ? 'is-expanded' : '' }}">
  <a class="app-menu__item" href="#" data-toggle="treeview">
    <i class="app-menu__icon fa fa-clipboard"></i>
    <span class="app-menu__label">Daily Stock Sheets</span>
    <i class="treeview-indicator fa fa-angle-right"></i>
  </a>
  <ul class="treeview-menu">
     <li><a class="treeview-item {{ str_contains($activePage, 'bar-keeper/reports') ? 'active' : '' }}" href="{{ route('bar-keeper.reports') }}"><i class="icon fa fa-glass"></i> Bar Report</a></li>
     <li><a class="treeview-item {{ str_contains($activePage, 'chef-master/reports') ? 'active' : '' }}" href="{{ route('chef-master.reports') }}"><i class="icon fa fa-cutlery"></i> Kitchen Report</a></li>
     <li><a class="treeview-item {{ str_contains($activePage, 'housekeeper/reports') ? 'active' : '' }}" href="{{ route('housekeeper.reports') }}"><i class="icon fa fa-bed"></i> Housekeeping Report</a></li>
  </ul>
</li>

{{-- BOOKING REPORTS --}}
<li class="treeview {{ str_contains($activePage, 'reports/bookings') ? 'is-expanded' : '' }}">
  <a class="app-menu__item" href="#" data-toggle="treeview">
    <i class="app-menu__icon fa fa-calendar-check-o"></i>
    <span class="app-menu__label">Booking Reports</span>
    <i class="treeview-indicator fa fa-angle-right"></i>
  </a>
  <ul class="treeview-menu">
    <li>
      <a class="treeview-item {{ str_contains($activePage, 'reports/bookings/room-occupancy') ? 'active' : '' }}" 
         href="{{ route('admin.reports.bookings.room-occupancy') }}">
        <i class="icon fa fa-bed"></i> Room Occupancy
      </a>
    </li>
    <li>
      <a class="treeview-item {{ str_contains($activePage, 'reports/bookings/performance') ? 'active' : '' }}" 
         href="{{ route('admin.reports.bookings.performance') }}">
        <i class="icon fa fa-bar-chart"></i> Booking Performance
      </a>
    </li>
  </ul>
</li>

{{-- FINANCIAL REPORTS --}}
<li class="treeview {{ str_contains($activePage, 'reports') && (str_contains($activePage, 'revenue') || str_contains($activePage, 'profitability') || str_contains($activePage, 'cash-flow')) ? 'is-expanded' : '' }}">
  <a class="app-menu__item" href="#" data-toggle="treeview">
    <i class="app-menu__icon fa fa-money"></i>
    <span class="app-menu__label">Financial Reports</span>
    <i class="treeview-indicator fa fa-angle-right"></i>
  </a>
  <ul class="treeview-menu">
    <li>
      <a class="treeview-item {{ str_contains($activePage, 'reports/revenue-breakdown') ? 'active' : '' }}" 
         href="{{ route('admin.reports.revenue-breakdown') }}">
        <i class="icon fa fa-pie-chart"></i> Revenue Breakdown
      </a>
    </li>
    <li>
      <a class="treeview-item {{ str_contains($activePage, 'reports/cash-flow') ? 'active' : '' }}" 
         href="{{ route('admin.reports.cash-flow') }}">
        <i class="icon fa fa-money"></i> Cash Flow
      </a>
    </li>
    <li>
      <a class="treeview-item {{ str_contains($activePage, 'reports/profitability') ? 'active' : '' }}" 
         href="{{ route('admin.reports.profitability') }}">
        <i class="icon fa fa-calculator"></i> Profitability
      </a>
    </li>
  </ul>
</li>

{{-- OPERATIONAL REPORTS --}}
<li class="treeview {{ str_contains($activePage, 'reports') && (str_contains($activePage, 'daily-operations') || str_contains($activePage, 'general') || str_contains($activePage, 'day-services')) ? 'is-expanded' : '' }}">
  <a class="app-menu__item" href="#" data-toggle="treeview">
    <i class="app-menu__icon fa fa-cogs"></i>
    <span class="app-menu__label">Operational Reports</span>
    <i class="treeview-indicator fa fa-angle-right"></i>
  </a>
  <ul class="treeview-menu">
    <li>
      <a class="treeview-item {{ str_contains($activePage, 'reports/daily-operations') ? 'active' : '' }}" 
         href="{{ route('admin.reports.daily-operations') }}">
        <i class="icon fa fa-calendar-check-o"></i> Daily Operations
      </a>
    </li>
    <li>
      <a class="treeview-item {{ str_contains($activePage, 'reports/general') ? 'active' : '' }}" 
         href="{{ route('admin.reports.general') }}">
        <i class="icon fa fa-dashboard"></i> General Overview
      </a>
    </li>
    <li>
      <a class="treeview-item {{ str_contains($activePage, 'day-services/reports') ? 'active' : '' }}" 
         href="{{ route('admin.day-services.reports') }}">
        <i class="icon fa fa-coffee"></i> Day Services
      </a>
    </li>
  </ul>
</li>
