{{-- Cashier Sidebar Menu --}}
@php
  $activePage = request()->path();
  $onShiftHandover = str_contains($activePage, 'cashier/shift-handovers');
  $handoverType = request('type', 'restaurant');
@endphp

<li>
  <a class="app-menu__item {{ str_contains($activePage, 'cashier/dashboard') ? 'active' : '' }}"
    href="{{ route('cashier.dashboard') }}">
    <i class="app-menu__icon fa fa-dashboard"></i>
    <span class="app-menu__label">Dashboard</span>
  </a>
</li>

<li class="treeview-item-header"
  style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">
  3 cash steps</li>

<li class="treeview {{ $onShiftHandover || str_contains($activePage, 'cashier/accountant') ? 'is-expanded' : '' }}">
  <a class="app-menu__item" href="#" data-toggle="treeview">
    <i class="app-menu__icon fa fa-money"></i>
    <span class="app-menu__label">Cash</span>
    <i class="treeview-indicator fa fa-angle-right"></i>
  </a>
  <ul class="treeview-menu">
    <li>
      <a class="treeview-item {{ $onShiftHandover && $handoverType !== 'reception' ? 'active' : '' }}"
        href="{{ route('cashier.shift-handovers', ['type' => 'restaurant']) }}">
        <i class="icon fa fa-glass"></i> 1. Receive Counter
      </a>
    </li>
    <li>
      <a class="treeview-item {{ $onShiftHandover && $handoverType === 'reception' ? 'active' : '' }}"
        href="{{ route('cashier.shift-handovers', ['type' => 'reception']) }}">
        <i class="icon fa fa-bed"></i> 2. Receive Reception
      </a>
    </li>
    <li>
      <a class="treeview-item {{ str_contains($activePage, 'cashier/accountant') ? 'active' : '' }}"
        href="{{ route('cashier.accountant.handovers') }}">
        <i class="icon fa fa-send"></i> 3. Send to Accountant
      </a>
    </li>
  </ul>
</li>

<li class="treeview-divider"></li>

<li>
  <a class="app-menu__item {{ str_contains($activePage, 'profile') ? 'active' : '' }}"
    href="{{ route('cashier.profile') }}">
    <i class="app-menu__icon fa fa-user"></i>
    <span class="app-menu__label">My Profile</span>
  </a>
</li>
