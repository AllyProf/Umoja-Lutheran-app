{{-- Housekeeper Sidebar Menu --}}
@php
  $currentRoute = request()->route() ? request()->route()->getName() : '';
  $activePage = request()->path();
@endphp

{{-- 1. DASHBOARD --}}
<li>
    <a class="app-menu__item {{ str_contains($activePage, 'housekeeper/dashboard') ? 'active' : '' }}" href="{{ route('housekeeper.dashboard') }}">
        <i class="app-menu__icon fa fa-dashboard"></i>
        <span class="app-menu__label">Dashboard</span>
    </a>
</li>

{{-- 2. ROOM MANAGEMENT --}}
<li class="treeview-item-header" style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">Room Operations</li>

<li class="treeview {{ str_contains($activePage, 'housekeeper/rooms') ? 'is-expanded' : '' }}">
    <a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-bed"></i><span class="app-menu__label">Rooms</span><i class="treeview-indicator fa fa-angle-right"></i></a>
    <ul class="treeview-menu">
        <li><a class="treeview-item {{ str_contains($activePage, 'cleaning') ? 'active' : '' }}" href="{{ route('housekeeper.rooms.cleaning') }}"><i class="icon fa fa-broom"></i> Needs Cleaning</a></li>
        <li><a class="treeview-item {{ str_contains($activePage, 'status') ? 'active' : '' }}" href="{{ route('housekeeper.rooms.status') }}"><i class="icon fa fa-info-circle"></i> Room Status</a></li>
    </ul>
</li>

<li>
    <a class="app-menu__item {{ str_contains($activePage, 'issues') ? 'active' : '' }}" href="{{ route('housekeeper.room-issues') }}">
        <i class="app-menu__icon fa fa-wrench"></i>
        <span class="app-menu__label">Room Issues</span>
    </a>
</li>

{{-- 3. INVENTORY & STOCK --}}
<li class="treeview-item-header" style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">Inventory</li>

<li>
    <a class="app-menu__item {{ str_contains($activePage, 'housekeeper/inventory') ? 'active' : '' }}" href="{{ route('housekeeper.inventory') }}">
        <i class="app-menu__icon fa fa-cubes"></i>
        <span class="app-menu__label">HK Inventory</span>
    </a>
</li>

<li>
    <a class="app-menu__item {{ str_contains($activePage, 'reports') ? 'active' : '' }}" href="{{ route('housekeeper.reports') }}">
        <i class="app-menu__icon fa fa-file-text"></i>
        <span class="app-menu__label">Consumption Reports</span>
    </a>
</li>

{{-- 4. PURCHASING --}}
<li class="treeview-item-header" style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">Purchasing</li>

<li class="treeview {{ str_contains($activePage, 'purchase-requests') ? 'is-expanded' : '' }}">
    <a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-shopping-cart"></i><span class="app-menu__label">Requests</span><i class="treeview-indicator fa fa-angle-right"></i></a>
    <ul class="treeview-menu">
        <li><a class="treeview-item {{ str_contains($activePage, 'create') ? 'active' : '' }}" href="{{ route('housekeeper.purchase-requests.create') }}"><i class="icon fa fa-plus-circle"></i> New Request</a></li>
        <li><a class="treeview-item {{ str_contains($activePage, 'my-requests') ? 'active' : '' }}" href="{{ route('housekeeper.purchase-requests.my') }}"><i class="icon fa fa-list-alt"></i> My Requests</a></li>
        <li><a class="treeview-item {{ str_contains($activePage, 'history') ? 'active' : '' }}" href="{{ route('housekeeper.purchase-requests.history') }}"><i class="icon fa fa-history"></i> Purchase History</a></li>
        <li><a class="treeview-item {{ str_contains($activePage, 'templates') ? 'active' : '' }}" href="{{ route('housekeeper.purchase-requests.templates') }}"><i class="icon fa fa-book"></i> Request Templates</a></li>
    </ul>
</li>

{{-- 5. ACCOUNT --}}
<li class="treeview-item-header" style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">Account</li>
<li><a class="app-menu__item {{ str_contains($activePage, 'profile') ? 'active' : '' }}" href="{{ route('housekeeper.profile') }}"><i class="app-menu__icon fa fa-user"></i><span class="app-menu__label">My Profile</span></a></li>
<li>
    <a class="app-menu__item" href="{{ route('housekeeper.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="app-menu__icon fa fa-sign-out"></i><span class="app-menu__label">Logout</span>
    </a>
</li>
