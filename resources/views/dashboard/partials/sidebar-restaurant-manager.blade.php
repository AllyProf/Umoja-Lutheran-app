{{-- Restaurant Manager (Assistant) — full restaurant access only --}}
@php
    $currentRoute = request()->route() ? request()->route()->getName() : '';
    $activePage = request()->path();
@endphp

<li>
    <a class="app-menu__item {{ str_contains($activePage, 'bar-keeper/order-summary') ? 'active' : '' }}"
        href="{{ route('bar-keeper.order-summary') }}">
        <i class="app-menu__icon fa fa-dashboard"></i>
        <span class="app-menu__label">Restaurant Hub</span>
    </a>
</li>

<li class="treeview-item-header"
    style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">
    Kitchen &amp; Restaurant</li>

<li>
    <a class="app-menu__item {{ str_contains($activePage, 'restaurants/recipes') ? 'active' : '' }}"
        href="{{ route('admin.recipes.index') }}">
        <i class="app-menu__icon fa fa-book"></i>
        <span class="app-menu__label">Menu Recipes</span>
    </a>
</li>
<li>
    <a class="app-menu__item {{ str_contains($activePage, 'chef-master/inventory') ? 'active' : '' }}"
        href="{{ route('chef-master.inventory') }}">
        <i class="app-menu__icon fa fa-cutlery"></i>
        <span class="app-menu__label">Kitchen Inventory</span>
    </a>
</li>
<li>
    <a class="app-menu__item {{ str_contains($activePage, 'restaurants/kitchen/orders') ? 'active' : '' }}"
        href="{{ route('admin.restaurants.kitchen.orders') }}">
        <i class="app-menu__icon fa fa-bell"></i>
        <span class="app-menu__label">Live Kitchen Orders</span>
    </a>
</li>
<li>
    <a class="app-menu__item {{ str_contains($activePage, 'restaurants/shopping-list') ? 'active' : '' }}"
        href="{{ route('admin.restaurants.shopping-list.index') }}">
        <i class="app-menu__icon fa fa-shopping-basket"></i>
        <span class="app-menu__label">Shopping Lists</span>
    </a>
</li>
<li>
    <a class="app-menu__item {{ str_contains($activePage, 'restaurant-reports') || str_contains($activePage, 'restaurants/reports') ? 'active' : '' }}"
        href="{{ route('admin.restaurants.reports') }}">
        <i class="app-menu__icon fa fa-line-chart"></i>
        <span class="app-menu__label">Restaurant Sales</span>
    </a>
</li>

<li class="treeview-item-header"
    style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">
    Counter Operations</li>

<li>
    <a class="app-menu__item {{ str_contains($activePage, 'bar-keeper/dashboard') ? 'active' : '' }}"
        href="{{ route('bar-keeper.dashboard') }}">
        <i class="app-menu__icon fa fa-glass"></i>
        <span class="app-menu__label">Counter POS</span>
    </a>
</li>
<li>
    <a class="app-menu__item {{ str_contains($activePage, 'bar-keeper/order-summary') ? 'active' : '' }}"
        href="{{ route('bar-keeper.order-summary') }}">
        <i class="app-menu__icon fa fa-bar-chart"></i>
        <span class="app-menu__label">Order Summary</span>
    </a>
</li>
<li>
    <a class="app-menu__item {{ str_contains($activePage, 'bar-keeper/debts') ? 'active' : '' }}"
        href="{{ route('bar-keeper.debts') }}">
        <i class="app-menu__icon fa fa-handshake-o"></i>
        <span class="app-menu__label">Debt Management</span>
    </a>
</li>
<li>
    <a class="app-menu__item {{ str_contains($activePage, 'bar-keeper/orders') ? 'active' : '' }}"
        href="{{ route('bar-keeper.orders.index') }}">
        <i class="app-menu__icon fa fa-history"></i>
        <span class="app-menu__label">Orders History</span>
    </a>
</li>
<li>
    <a class="app-menu__item {{ str_contains($activePage, 'bar-keeper/my-stock') ? 'active' : '' }}"
        href="{{ route('bar-keeper.stock.index') }}">
        <i class="app-menu__icon fa fa-cubes"></i>
        <span class="app-menu__label">Counter Inventory</span>
    </a>
</li>
<li>
    <a class="app-menu__item {{ str_contains($activePage, 'bar-keeper/transfers') ? 'active' : '' }}"
        href="{{ route('bar-keeper.transfers.index') }}">
        <i class="app-menu__icon fa fa-exchange"></i>
        <span class="app-menu__label">Stock Transfers</span>
    </a>
</li>
<li>
    <a class="app-menu__item {{ str_contains($activePage, 'bar-keeper/reports') ? 'active' : '' }}"
        href="{{ route('bar-keeper.reports') }}">
        <i class="app-menu__icon fa fa-file-text-o"></i>
        <span class="app-menu__label">Drinks Stock Sheet</span>
    </a>
</li>
<li>
    <a class="app-menu__item {{ str_contains($activePage, 'chef-master/reports') ? 'active' : '' }}"
        href="{{ route('chef-master.reports') }}">
        <i class="app-menu__icon fa fa-file-text"></i>
        <span class="app-menu__label">Kitchen Report</span>
    </a>
</li>

<li class="treeview-item-header"
    style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">
    Account</li>
<li>
    <a class="app-menu__item {{ $currentRoute === 'bar-keeper.profile' ? 'active' : '' }}"
        href="{{ route('bar-keeper.profile') }}">
        <i class="app-menu__icon fa fa-user"></i>
        <span class="app-menu__label">My Profile</span>
    </a>
</li>
<li>
    <a class="app-menu__item" href="{{ route('bar-keeper.logout') }}"
        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="app-menu__icon fa fa-sign-out"></i>
        <span class="app-menu__label">Logout</span>
    </a>
</li>
