{{-- Bar Keeper Sidebar Menu --}}
@php
  $currentRoute = request()->route() ? request()->route()->getName() : '';
  $activePage = request()->path();
@endphp

{{-- 1. DASHBOARD --}}
<li>
    <a class="app-menu__item {{ str_contains($activePage, 'bar-keeper/dashboard') ? 'active' : '' }}" href="{{ route('bar-keeper.dashboard') }}">
        <i class="app-menu__icon fa fa-dashboard"></i>
        <span class="app-menu__label">Dashboard</span>
    </a>
</li>

{{-- 2. ORDERS --}}
<li class="treeview-item-header" style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">Sales & Orders</li>

<li>
    <a class="app-menu__item {{ str_contains($activePage, 'orders') ? 'active' : '' }}" href="{{ route('bar-keeper.dashboard') }}#orders">
        <i class="app-menu__icon fa fa-glass"></i>
        <span class="app-menu__label">Guest Orders</span>
    </a>
</li>

<li>
    <a class="app-menu__item {{ str_contains($activePage, 'orders/index') ? 'active' : '' }}" href="{{ route('bar-keeper.orders.index') }}">
        <i class="app-menu__icon fa fa-history"></i>
        <span class="app-menu__label">Orders History</span>
    </a>
</li>

{{-- 3. STOCK MANAGEMENT --}}
<li class="treeview-item-header" style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">Stock Management</li>

<li>
    <a class="app-menu__item {{ str_contains($activePage, 'bar-keeper/products') ? 'active' : '' }}" href="{{ route('bar-keeper.products.index') }}">
        <i class="app-menu__icon fa fa-cube"></i>
        <span class="app-menu__label">Products & Registration</span>
    </a>
</li>
<li>
    <a class="app-menu__item {{ str_contains($activePage, 'bar-keeper/my-stock') ? 'active' : '' }}" href="{{ route('bar-keeper.stock.index') }}">
        <i class="app-menu__icon fa fa-cubes"></i>
        <span class="app-menu__label">Bar Inventory</span>
    </a>
</li>

<li>
    <a class="app-menu__item {{ str_contains($activePage, 'recorded-items') ? 'active' : '' }}" href="{{ route('bar-keeper.recorded-items') }}">
        <i class="app-menu__icon fa fa-list"></i>
        <span class="app-menu__label">Usage Records</span>
    </a>
</li>

<li>
    <a class="app-menu__item {{ str_contains($activePage, 'transfers') ? 'active' : '' }}" href="{{ route('bar-keeper.transfers.index') }}">
        <i class="app-menu__icon fa fa-exchange"></i>
        <span class="app-menu__label">Stock Transfers</span>
    </a>
</li>

{{-- 4. PURCHASING --}}
<li class="treeview-item-header" style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">Purchasing</li>

<li class="treeview {{ str_contains($activePage, 'purchase-requests') ? 'is-expanded' : '' }}">
    <a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-shopping-cart"></i><span class="app-menu__label">Requests</span><i class="treeview-indicator fa fa-angle-right"></i></a>
    <ul class="treeview-menu">
        <li><a class="treeview-item {{ str_contains($activePage, 'create') ? 'active' : '' }}" href="{{ route('bar-keeper.purchase-requests.create') }}"><i class="icon fa fa-plus-circle"></i> New Request</a></li>
        <li><a class="treeview-item {{ str_contains($activePage, 'my-requests') ? 'active' : '' }}" href="{{ route('bar-keeper.purchase-requests.my') }}"><i class="icon fa fa-list-alt"></i> My Requests</a></li>
        <li><a class="treeview-item {{ str_contains($activePage, 'history') ? 'active' : '' }}" href="{{ route('bar-keeper.purchase-requests.history') }}"><i class="icon fa fa-history"></i> Purchase History</a></li>
        <li><a class="treeview-item {{ str_contains($activePage, 'templates') ? 'active' : '' }}" href="{{ route('bar-keeper.purchase-requests.templates') }}"><i class="icon fa fa-book"></i> Templates</a></li>
    </ul>
</li>

{{-- 5. REPORTS & TOOLS --}}
<li class="treeview-item-header" style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">Analysis</li>

<li>
    <a class="app-menu__item {{ str_contains($activePage, 'reports') ? 'active' : '' }}" href="{{ route('bar-keeper.reports') }}">
        <i class="app-menu__icon fa fa-bar-chart"></i>
        <span class="app-menu__label">Daily Stock Sheet</span>
    </a>
</li>

<li>
    <a class="app-menu__item {{ str_contains($activePage, 'exchange-rates') ? 'active' : '' }}" href="{{ route('exchange-rates') }}">
        <i class="app-menu__icon fa fa-money"></i>
        <span class="app-menu__label">Exchange Rates</span>
    </a>
</li>

{{-- 6. ACCOUNT --}}
<li class="treeview-item-header" style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">Account</li>
<li><a class="app-menu__item {{ $currentRoute === 'bar-keeper.profile' ? 'active' : '' }}" href="{{ route('bar-keeper.profile') }}"><i class="app-menu__icon fa fa-user"></i><span class="app-menu__label">My Profile</span></a></li>
<li>
    <a class="app-menu__item" href="{{ route('bar-keeper.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="app-menu__icon fa fa-sign-out"></i><span class="app-menu__label">Logout</span>
    </a>
</li>
