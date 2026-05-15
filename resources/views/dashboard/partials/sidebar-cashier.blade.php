{{-- Cashier Sidebar Menu --}}
@php
  $activePage = request()->path();
@endphp

{{-- 1. DASHBOARD --}}
<li>
  <a class="app-menu__item {{ str_contains($activePage, 'cashier/dashboard') ? 'active' : '' }}"
    href="{{ route('cashier.dashboard') }}">
    <i class="app-menu__icon fa fa-dashboard"></i>
    <span class="app-menu__label">Dashboard</span>
  </a>
</li>

{{-- 2. COLLECTIONS --}}
<li class="treeview-item-header"
  style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">
  Collections & Finance</li>

<li
  class="treeview {{ str_contains($activePage, 'cashier/shift-handovers') || str_contains($activePage, 'cashier/reception/collections') || str_contains($activePage, 'cashier/accountant') ? 'is-expanded' : '' }}">
  <a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-money"></i><span
      class="app-menu__label">Handovers</span><i class="treeview-indicator fa fa-angle-right"></i></a>
  <ul class="treeview-menu">
    <li><a class="treeview-item {{ str_contains($activePage, 'cashier/shift-handovers') ? 'active' : '' }}" href="{{ route('cashier.shift-handovers') }}"><i class="icon fa fa-coffee"></i> Counter Handover</a></li>
    <li><a class="treeview-item {{ str_contains($activePage, 'cashier/reception/collections') ? 'active' : '' }}" href="{{ route('cashier.reception.collections') }}"><i class="icon fa fa-bed"></i> Reception Handover</a></li>
    <li><a class="treeview-item {{ str_contains($activePage, 'cashier/accountant') ? 'active' : '' }}" href="{{ route('cashier.accountant.handovers') }}"><i class="icon fa fa-send"></i> Submit to Accountant</a></li>
  </ul>
</li>

<li class="treeview-divider"></li>

<li><a class="app-menu__item {{ str_contains($activePage, 'profile') ? 'active' : '' }}"
    href="{{ route('cashier.profile') }}"><i class="app-menu__icon fa fa-user"></i><span class="app-menu__label">My
      Profile</span></a></li>
