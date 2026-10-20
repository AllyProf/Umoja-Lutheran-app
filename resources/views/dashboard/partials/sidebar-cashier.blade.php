{{-- Cashier Sidebar Menu --}}
@php
  $activePage = request()->path();
  $routeName = request()->route()?->getName() ?? '';
  $onCash = str_contains($activePage, 'cashier/shift-handovers')
    || str_contains($activePage, 'cashier/accountant')
    || str_contains($activePage, 'cashier/reception');
  $handoverType = request('type', 'restaurant');
@endphp

<li>
  <a class="app-menu__item {{ str_contains($activePage, 'cashier/dashboard') ? 'active' : '' }}"
    href="{{ route('cashier.dashboard') }}">
    <i class="app-menu__icon fa fa-dashboard"></i>
    <span class="app-menu__label">Dashboard</span>
  </a>
</li>

{{-- CASH FLOW --}}
<li class="treeview-item-header"
  style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">
  Cash Flow
</li>

<li class="treeview {{ $onCash ? 'is-expanded' : '' }}">
  <a class="app-menu__item" href="#" data-toggle="treeview">
    <i class="app-menu__icon fa fa-money"></i>
    <span class="app-menu__label">Receive &amp; Send</span>
    <i class="treeview-indicator fa fa-angle-right"></i>
  </a>
  <ul class="treeview-menu">
    <li>
      <a class="treeview-item {{ str_contains($activePage, 'shift-handovers') && $handoverType !== 'reception' && !str_contains($activePage, 'sales') ? 'active' : '' }}"
        href="{{ route('cashier.shift-handovers', ['type' => 'restaurant']) }}">
        <i class="icon fa fa-glass"></i> Receive Counter
      </a>
    </li>
    <li>
      <a class="treeview-item {{ str_contains($activePage, 'shift-handovers') && $handoverType === 'reception' ? 'active' : '' }}"
        href="{{ route('cashier.shift-handovers', ['type' => 'reception']) }}">
        <i class="icon fa fa-bed"></i> Receive Reception
      </a>
    </li>
    <li>
      <a class="treeview-item {{ str_contains($activePage, 'cashier/reception/collections') ? 'active' : '' }}"
        href="{{ route('cashier.reception.collections') }}">
        <i class="icon fa fa-calendar-check-o"></i> Reception Day Cash
      </a>
    </li>
    <li>
      <a class="treeview-item {{ str_contains($activePage, 'cashier/accountant') ? 'active' : '' }}"
        href="{{ route('cashier.accountant.handovers') }}">
        <i class="icon fa fa-send"></i> Send to Accountant
      </a>
    </li>
  </ul>
</li>

{{-- REPORTS --}}
<li class="treeview-item-header"
  style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">
  Reports
</li>

<li>
  <a class="app-menu__item {{ str_contains($activePage, 'cashier/shift-reports') ? 'active' : '' }}"
    href="{{ route('cashier.shift-reports') }}">
    <i class="app-menu__icon fa fa-file-text-o"></i>
    <span class="app-menu__label">Closed Shift Reports</span>
  </a>
</li>

<li>
  <a class="app-menu__item {{ str_contains($activePage, 'order-summary') ? 'active' : '' }}"
    href="{{ route('cashier.order-summary') }}">
    <i class="app-menu__icon fa fa-bar-chart"></i>
    <span class="app-menu__label">Order Summary</span>
  </a>
</li>

{{-- COLLECTIONS --}}
<li class="treeview-item-header"
  style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">
  Collections
</li>

<li>
  <a class="app-menu__item {{ str_contains($activePage, 'cashier/debts') || str_contains($activePage, 'bar-keeper/debts') ? 'active' : '' }}"
    href="{{ route('cashier.debts') }}">
    <i class="app-menu__icon fa fa-handshake-o"></i>
    <span class="app-menu__label">Debt Management</span>
  </a>
</li>

<li class="treeview-divider"></li>

<li>
  <a class="app-menu__item {{ str_contains($activePage, 'profile') ? 'active' : '' }}"
    href="{{ route('cashier.profile') }}">
    <i class="app-menu__icon fa fa-user"></i>
    <span class="app-menu__label">My Profile</span>
  </a>
</li>
