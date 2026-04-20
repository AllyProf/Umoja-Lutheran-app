{{-- Storekeeper Sidebar Menu --}}
@php
    $currentRoute = request()->route() ? request()->route()->getName() : '';
    $activePage = request()->path();
@endphp

{{-- 1. DASHBOARD --}}
<li>
    <a class="app-menu__item {{ str_contains($activePage, 'storekeeper/dashboard') ? 'active' : '' }}"
        href="{{ route('storekeeper.dashboard') }}">
        <i class="app-menu__icon fa fa-dashboard"></i>
        <span class="app-menu__label">Dashboard</span>
    </a>
</li>

{{-- 2. INVENTORY --}}
<li class="treeview-item-header"
    style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">
    Store Inventory</li>

<li>
    <a class="app-menu__item {{ str_contains($activePage, 'storekeeper/inventory') ? 'active' : '' }}"
        href="{{ route('storekeeper.inventory') }}">
        <i class="app-menu__icon fa fa-cubes"></i>
        <span class="app-menu__label">Main Store Stock</span>
    </a>
</li>

<li>
    <a class="app-menu__item {{ str_contains($activePage, 'storekeeper/products') ? 'active' : '' }}"
        href="{{ route('storekeeper.products.index') }}">
        <i class="app-menu__icon fa fa-database"></i>
        <span class="app-menu__label">Products & Departments</span>
    </a>
</li>

<li>
    <a class="app-menu__item {{ str_contains($activePage, 'storekeeper/purchase-requests/emergency') ? 'active' : '' }}"
        href="{{ route('storekeeper.purchase-requests.emergency') }}">
        <i class="app-menu__icon fa fa-ambulance text-danger"></i>
        <span class="app-menu__label">Emergency Purchase</span>
    </a>
</li>



<li class="treeview {{ str_contains($activePage, 'storekeeper/shopping-lists') ? 'is-expanded' : '' }}">
    <a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-list-alt"></i><span
            class="app-menu__label">Shopping Lists</span><i class="treeview-indicator fa fa-angle-right"></i></a>
    <ul class="treeview-menu">
        <li><a class="treeview-item" href="{{ route('storekeeper.shopping-list.index') }}"><i
                    class="icon fa fa-list"></i> All Lists</a></li>
        <li><a class="treeview-item" href="{{ route('storekeeper.shopping-list.create') }}"><i
                    class="icon fa fa-plus-circle"></i> New Shopping List</a></li>
    </ul>
</li>

{{-- 4. STOCK MANAGEMENT --}}
<li class="treeview-item-header"
    style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">
    Stock Management</li>


<li>
    <a class="app-menu__item {{ str_contains($activePage, 'storekeeper/stock-returns') ? 'active' : '' }}"
        href="{{ route('storekeeper.stock-returns.index') }}">
        <i class="app-menu__icon fa fa-undo text-warning"></i>
        <span class="app-menu__label">Process Returns (Staff)</span>
    </a>
</li>



<li class="treeview {{ str_contains($activePage, 'stock-requests') ? 'is-expanded' : '' }}">
    <a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-tasks"></i><span
            class="app-menu__label">Stock Requests</span><i class="treeview-indicator fa fa-angle-right"></i></a>
    <ul class="treeview-menu">
        <li><a class="treeview-item" href="{{ route('stock-requests.index', ['type' => 'drink']) }}"><i
                    class="icon fa fa-glass"></i> Counter Requests</a></li>
        <li><a class="treeview-item" href="{{ route('stock-requests.index', ['type' => 'food']) }}"><i
                    class="icon fa fa-cutlery"></i> Chef Request</a></li>
        <li><a class="treeview-item" href="{{ route('stock-requests.index', ['type' => 'housekeeping']) }}"><i
                    class="icon fa fa-bed"></i> Housekeeping Requests</a></li>
        <li class="treeview-divider"></li>
        <li><a class="treeview-item" href="{{ route('stock-requests.index') }}"><i class="icon fa fa-list"></i> All
                Requests</a></li>
    </ul>
</li>

<li>
    <a class="app-menu__item {{ str_contains($activePage, 'storekeeper/announcements') ? 'active' : '' }}"
        href="{{ route('storekeeper.announcements.index') }}">
        <i class="app-menu__icon fa fa-bullhorn text-danger"></i>
        <span class="app-menu__label">Broadcast Alerts</span>
    </a>
</li>

{{-- 5. ACCOUNT --}}
<li class="treeview-item-header"
    style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">
    Account</li>
<li><a class="app-menu__item {{ str_contains($activePage, 'profile') ? 'active' : '' }}"
        href="{{ route('storekeeper.profile') }}"><i class="app-menu__icon fa fa-user"></i><span
            class="app-menu__label">My Profile</span></a></li>
<li>
    <a class="app-menu__item" href="{{ route('storekeeper.logout') }}"
        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="app-menu__icon fa fa-sign-out"></i><span class="app-menu__label">Logout</span>
    </a>
</li>