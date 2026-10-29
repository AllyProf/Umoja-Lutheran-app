{{-- Storekeeper Sidebar Menu — grouped dropdowns for easier navigation --}}
@php
    $currentRoute = request()->route() ? request()->route()->getName() : '';
    $activePage = request()->path();

    $isInventory = str_contains($activePage, 'storekeeper/inventory')
        || str_contains($activePage, 'storekeeper/products')
        || str_contains($activePage, 'storekeeper/stock-receipts')
        || str_contains($activePage, 'storekeeper/transfers');

    $isPurchasing = str_contains($activePage, 'storekeeper/shopping-lists')
        || str_contains($activePage, 'storekeeper/purchase-requests')
        || str_contains($activePage, 'supplier-orders')
        || str_contains($activePage, 'lpo')
        || str_contains($activePage, 'restaurants/suppliers');

    $isStockMgmt = str_contains($activePage, 'stock-requests')
        || str_contains($activePage, 'storekeeper/stock-returns');
@endphp

{{-- 1. DASHBOARD --}}
<li>
    <a class="app-menu__item {{ str_contains($activePage, 'storekeeper/dashboard') ? 'active' : '' }}"
        href="{{ route('storekeeper.dashboard') }}">
        <i class="app-menu__icon fa fa-dashboard"></i>
        <span class="app-menu__label">Dashboard</span>
    </a>
</li>

{{-- 2. STORE INVENTORY --}}
<li class="treeview-item-header"
    style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">
    Inventory
</li>

<li class="treeview {{ $isInventory ? 'is-expanded' : '' }}">
    <a class="app-menu__item" href="#" data-toggle="treeview">
        <i class="app-menu__icon fa fa-cubes"></i>
        <span class="app-menu__label">Store Inventory</span>
        <i class="treeview-indicator fa fa-angle-right"></i>
    </a>
    <ul class="treeview-menu">
        <li>
            <a class="treeview-item {{ str_contains($activePage, 'storekeeper/inventory') ? 'active' : '' }}"
                href="{{ route('storekeeper.inventory') }}">
                <i class="icon fa fa-archive"></i> Main Store Stock
            </a>
        </li>
        <li>
            <a class="treeview-item {{ str_contains($activePage, 'storekeeper/products') && !str_contains($activePage, 'products/create') ? 'active' : '' }}"
                href="{{ route('storekeeper.products.index') }}">
                <i class="icon fa fa-database"></i> All Products
            </a>
        </li>
        <li>
            <a class="treeview-item {{ str_contains($activePage, 'storekeeper/products/create') ? 'active' : '' }}"
                href="{{ route('storekeeper.products.create') }}">
                <i class="icon fa fa-plus-circle"></i> Register Product
            </a>
        </li>
        <li>
            <a class="treeview-item {{ str_contains($activePage, 'storekeeper/stock-receipts') ? 'active' : '' }}"
                href="{{ route('storekeeper.stock-receipts.create') }}">
                <i class="icon fa fa-inbox"></i> Receive Stock
            </a>
        </li>
        <li>
            <a class="treeview-item {{ str_contains($activePage, 'storekeeper/transfers/create') ? 'active' : '' }}"
                href="{{ route('storekeeper.transfers.create') }}">
                <i class="icon fa fa-exchange"></i> Distribute Stock
            </a>
        </li>
        <li>
            <a class="treeview-item {{ str_contains($activePage, 'storekeeper/transfers') && !str_contains($activePage, 'transfers/create') ? 'active' : '' }}"
                href="{{ route('storekeeper.transfers.index') }}">
                <i class="icon fa fa-history"></i> Transfer History
            </a>
        </li>
    </ul>
</li>

{{-- 3. PURCHASING & SUPPLIERS --}}
<li class="treeview-item-header"
    style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">
    Purchasing
</li>

<li class="treeview {{ $isPurchasing ? 'is-expanded' : '' }}">
    <a class="app-menu__item" href="#" data-toggle="treeview">
        <i class="app-menu__icon fa fa-shopping-cart"></i>
        <span class="app-menu__label">Purchasing & Suppliers</span>
        <i class="treeview-indicator fa fa-angle-right"></i>
    </a>
    <ul class="treeview-menu">
        <li>
            <a class="treeview-item {{ str_contains($activePage, 'storekeeper/shopping-lists') ? 'active' : '' }}"
                href="{{ route('storekeeper.shopping-list.index') }}">
                <i class="icon fa fa-list"></i> Shopping Lists
            </a>
        </li>
        <li>
            <a class="treeview-item {{ str_contains($activePage, 'restaurants/suppliers') ? 'active' : '' }}"
                href="{{ route('admin.suppliers.index') }}">
                <i class="icon fa fa-address-book"></i> Suppliers
            </a>
        </li>
        <li>
            <a class="treeview-item {{ str_contains($activePage, 'supplier-orders') ? 'active' : '' }}"
                href="{{ route('supplier-orders.index') }}">
                <i class="icon fa fa-truck"></i> Weekly Supplier Orders
            </a>
        </li>
        <li>
            <a class="treeview-item {{ str_contains($activePage, 'lpo') ? 'active' : '' }}"
                href="{{ route('lpo.index') }}">
                <i class="icon fa fa-file-text"></i> Local Purchase Orders
            </a>
        </li>
        <li class="treeview-divider"></li>
        <li>
            <a class="treeview-item {{ str_contains($activePage, 'storekeeper/purchase-requests/emergency') ? 'active' : '' }}"
                href="{{ route('storekeeper.purchase-requests.emergency') }}">
                <i class="icon fa fa-ambulance text-danger"></i> Emergency Purchase
            </a>
        </li>
    </ul>
</li>

{{-- 4. STOCK REQUESTS --}}
<li class="treeview-item-header"
    style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">
    Stock Requests
</li>

<li class="treeview {{ $isStockMgmt ? 'is-expanded' : '' }}">
    <a class="app-menu__item" href="#" data-toggle="treeview">
        <i class="app-menu__icon fa fa-tasks"></i>
        <span class="app-menu__label">Department Requests</span>
        <i class="treeview-indicator fa fa-angle-right"></i>
    </a>
    <ul class="treeview-menu">
        <li>
            <a class="treeview-item {{ str_contains($activePage, 'stock-requests') && !request('type') ? 'active' : '' }}"
                href="{{ route('stock-requests.index') }}">
                <i class="icon fa fa-list"></i> All
            </a>
        </li>
        <li>
            <a class="treeview-item {{ request('type') === 'restaurant' || in_array(request('type'), ['drink', 'food']) ? 'active' : '' }}"
                href="{{ route('stock-requests.index', ['type' => 'restaurant']) }}">
                <i class="icon fa fa-cutlery"></i> Restaurant
            </a>
        </li>
        <li>
            <a class="treeview-item {{ request('type') === 'housekeeping' ? 'active' : '' }}"
                href="{{ route('stock-requests.index', ['type' => 'housekeeping']) }}">
                <i class="icon fa fa-bed"></i> Housekeeping
            </a>
        </li>
        <li class="treeview-divider"></li>
        <li>
            <a class="treeview-item {{ str_contains($activePage, 'storekeeper/stock-returns') ? 'active' : '' }}"
                href="{{ route('storekeeper.stock-returns.index') }}">
                <i class="icon fa fa-undo"></i> Process Returns
            </a>
        </li>
    </ul>
</li>

{{-- 5. ALERTS --}}
<li>
    <a class="app-menu__item {{ str_contains($activePage, 'storekeeper/announcements') ? 'active' : '' }}"
        href="{{ route('storekeeper.announcements.index') }}">
        <i class="app-menu__icon fa fa-bullhorn text-danger"></i>
        <span class="app-menu__label">Broadcast Alerts</span>
    </a>
</li>

{{-- 6. ACCOUNT --}}
<li class="treeview-item-header"
    style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">
    Account
</li>
<li>
    <a class="app-menu__item {{ str_contains($activePage, 'profile') ? 'active' : '' }}"
        href="{{ route('storekeeper.profile') }}">
        <i class="app-menu__icon fa fa-user"></i>
        <span class="app-menu__label">My Profile</span>
    </a>
</li>
<li>
    <a class="app-menu__item" href="{{ route('storekeeper.logout') }}"
        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="app-menu__icon fa fa-sign-out"></i>
        <span class="app-menu__label">Logout</span>
    </a>
</li>
