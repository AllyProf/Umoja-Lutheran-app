{{-- Manager/Admin/HeadChef Sidebar Menu --}}
@php
  use App\Services\RolePermissionService;
  
  $currentRoute = request()->route() ? request()->route()->getName() : '';
  $activePage = request()->path();
  
  // Local helper functions for cleaner code
  $hasPermission = function($permission) use ($currentUser) {
    return \App\Services\RolePermissionService::hasPermission($currentUser, $permission);
  };
  
  $isSuperAdmin = function() use ($currentUser) {
    return $currentUser && method_exists($currentUser, 'isSuperAdmin') ? $currentUser->isSuperAdmin() : false;
  };

  $hasRole = function($role) use ($currentUser) {
    return \App\Services\RolePermissionService::hasRole($currentUser, $role);
  };
  
  $isChef = $hasRole('head_chef');
@endphp

{{-- 1. DASHBOARD --}}
<li>
    <a class="app-menu__item {{ (str_contains($activePage, 'admin/dashboard') || str_contains($activePage, 'restaurant/food/dashboard') || str_contains($activePage, 'chef-master/dashboard')) ? 'active' : '' }}" 
       href="{{ $isChef ? route('chef-master.dashboard') : route('admin.dashboard') }}">
        <i class="app-menu__icon fa fa-dashboard"></i>
        <span class="app-menu__label">Dashboard</span>
    </a>
</li>

@if($isChef)
    {{-- HEAD CHEF SPECIFIC VIEW --}}
    
    <li class="treeview-item-header" style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">Kitchen Operations</li>
    
    <li>
        <a class="app-menu__item {{ str_contains($activePage, 'chef-master/inventory') ? 'active' : '' }}" href="{{ route('chef-master.inventory') }}">
            <i class="app-menu__icon fa fa-cubes"></i>
            <span class="app-menu__label">Kitchen Inventory</span>
        </a>
    </li>

    <li>
        <a class="app-menu__item {{ str_contains($activePage, 'orders') ? 'active' : '' }}" href="{{ route('admin.restaurants.kitchen.orders') }}">
            <i class="app-menu__icon fa fa-bell"></i>
            <span class="app-menu__label">Live Orders</span>
        </a>
    </li>

    <li>
        <a class="app-menu__item {{ str_contains($activePage, 'recipes') ? 'active' : '' }}" href="{{ route('admin.recipes.index') }}">
            <i class="app-menu__icon fa fa-book"></i>
            <span class="app-menu__label">Customer Menu</span>
        </a>
    </li>

    <li class="treeview-item-header" style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">Purchasing</li>
    
    <li class="treeview {{ str_contains($activePage, 'purchase-requests') ? 'is-expanded' : '' }}">
        <a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-shopping-cart"></i><span class="app-menu__label">Purchase Requests</span><i class="treeview-indicator fa fa-angle-right"></i></a>
        <ul class="treeview-menu">
            <li><a class="treeview-item {{ str_contains($activePage, 'create') ? 'active' : '' }}" href="{{ route('chef-master.purchase-requests.create') }}"><i class="icon fa fa-plus-circle"></i> New Request</a></li>
            <li><a class="treeview-item {{ str_contains($activePage, 'my-requests') ? 'active' : '' }}" href="{{ route('chef-master.purchase-requests.my') }}"><i class="icon fa fa-list-alt"></i> My Requests</a></li>
            <li><a class="treeview-item {{ str_contains($activePage, 'history') ? 'active' : '' }}" href="{{ route('chef-master.purchase-requests.history') }}"><i class="icon fa fa-history"></i> Purchase History</a></li>
            <li><a class="treeview-item {{ str_contains($activePage, 'templates') ? 'active' : '' }}" href="{{ route('chef-master.purchase-requests.templates') }}"><i class="icon fa fa-clipboard-list"></i> Templates</a></li>
        </ul>
    </li>

    <li class="treeview-item-header" style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">Reports</li>
    
    <li>
        <a class="app-menu__item {{ (str_contains($activePage, 'chef-master/reports') && request('type') != 'weekly') ? 'active' : '' }}" href="{{ route('chef-master.reports') }}">
            <i class="app-menu__icon fa fa-file-pdf-o"></i>
            <span class="app-menu__label">Daily Stock Sheet</span>
        </a>
    </li>

    <li>
        <a class="app-menu__item {{ (str_contains($activePage, 'chef-master/reports') && request('type') == 'weekly') ? 'active' : '' }}" href="{{ route('chef-master.reports', ['type' => 'weekly']) }}">
            <i class="app-menu__icon fa fa-calendar"></i>
            <span class="app-menu__label">Weekly Stock Summary</span>
        </a>
    </li>

@else
    {{-- MANAGER/ADMIN VIEW --}}

    {{-- 2. KITCHEN & RESTAURANT --}}
    <li class="treeview-item-header" style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">Kitchen & Restaurant</li>
    <li class="treeview {{ (str_contains($activePage, 'restaurant/food') || str_contains($activePage, 'recipes') || str_contains($activePage, 'shopping-list') || str_contains($activePage, 'restaurant-reports')) ? 'is-expanded' : '' }}">
        <a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-cutlery"></i><span class="app-menu__label">Food Operations</span><i class="treeview-indicator fa fa-angle-right"></i></a>
        <ul class="treeview-menu">
            <li><a class="treeview-item" href="{{ route('admin.recipes.index') }}"><i class="icon fa fa-book"></i> Menu Recipes</a></li>
            <li><a class="treeview-item" href="{{ route('chef-master.inventory') }}"><i class="icon fa fa-cubes"></i> Kitchen Inventory</a></li>
            <li><a class="treeview-item" href="{{ route('admin.restaurants.kitchen.orders') }}"><i class="icon fa fa-bell"></i> Live Orders</a></li>
            <li class="treeview-divider"></li>
            <li><a class="treeview-item" href="{{ route('admin.restaurants.shopping-list.index') }}"><i class="icon fa fa-shopping-basket"></i> Shopping Lists</a></li>
            <li class="treeview-divider"></li>
            <li><a class="treeview-item" href="{{ route('admin.restaurants.reports') }}"><i class="icon fa fa-bar-chart"></i> Restaurant Sales</a></li>
        </ul>
    </li>

    {{-- 3. BAR & DRINKS --}}
    <li class="treeview-item-header" style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">Bar Operations</li>
    <li class="treeview {{ (str_contains($activePage, 'restaurants/products') && request('type') == 'drink') || str_contains($activePage, 'bar-keeper') ? 'is-expanded' : '' }}">
        <a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-glass"></i><span class="app-menu__label">Bar & Drinks</span><i class="treeview-indicator fa fa-angle-right"></i></a>
        <ul class="treeview-menu">
            <li><a class="treeview-item" href="{{ route('bar-keeper.stock.index') }}"><i class="icon fa fa-cubes"></i> Bar Stock</a></li>
            <li><a class="treeview-item" href="{{ route('admin.products.index', ['type' => 'drink']) }}"><i class="icon fa fa-list-alt"></i> Product Registry</a></li>
        </ul>
    </li>

    {{-- 4. FRONT OFFICE --}}
    <li class="treeview-item-header" style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">Front Office</li>
    <li class="treeview {{ str_contains($activePage, 'admin/bookings') || str_contains($activePage, 'admin/reservations') || str_contains($activePage, 'admin/extension-requests') ? 'is-expanded' : '' }}">
        <a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-calendar-check-o"></i><span class="app-menu__label">Bookings</span><i class="treeview-indicator fa fa-angle-right"></i></a>
        <ul class="treeview-menu">
            <li><a class="treeview-item" href="{{ route('admin.bookings.index') }}"><i class="icon fa fa-list"></i> All Bookings</a></li>
            <li><a class="treeview-item" href="{{ route('admin.bookings.manual.create') }}"><i class="icon fa fa-plus-circle"></i> Individual Booking</a></li>
            {{-- <li><a class="treeview-item" href="{{ route('admin.bookings.corporate.create') }}"><i class="icon fa fa-building"></i> Corporate Booking</a></li> --}}
            <li><a class="treeview-item" href="{{ route('admin.bookings.calendar') }}"><i class="icon fa fa-calendar"></i> Calendar View</a></li>
            <li class="treeview-divider"></li>
            <li><a class="treeview-item" href="{{ route('admin.reservations.check-in') }}"><i class="icon fa fa-sign-in"></i> Guest Check-In</a></li>
            <li><a class="treeview-item" href="{{ route('admin.reservations.check-out') }}"><i class="icon fa fa-sign-out"></i> Guest Check-Out</a></li>
            <li><a class="treeview-item {{ str_contains($activePage, 'admin/extension-requests') ? 'active' : '' }}" href="{{ route('admin.extension-requests') }}"><i class="icon fa fa-clock-o"></i> Extension Requests @if(isset($sidebarBadges['extensions']) && $sidebarBadges['extensions'] > 0)<span class="badge badge-info badge-pill ml-2">{{ $sidebarBadges['extensions'] }}</span>@endif</a></li>
        </ul>
    </li>

    <li class="treeview {{ str_contains($activePage, 'admin/day-services') ? 'is-expanded' : '' }}">
        <a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-coffee"></i><span class="app-menu__label">Day Services</span><i class="treeview-indicator fa fa-angle-right"></i></a>
        <ul class="treeview-menu">
            <li><a class="treeview-item" href="{{ route('admin.day-services.index') }}"><i class="icon fa fa-list-alt"></i> All Services</a></li>
            <li><a class="treeview-item" href="{{ route('admin.day-services.swimming') }}"><i class="icon fa fa-tint"></i> Swimming Pool</a></li>
            <li><a class="treeview-item" href="{{ route('admin.day-services.ceremony') }}"><i class="icon fa fa-birthday-cake"></i> Ceremonies</a></li>
            <li class="treeview-divider"></li>
            <li><a class="treeview-item" href="{{ route('admin.day-services.reports') }}"><i class="icon fa fa-file-pdf-o"></i> Service Reports</a></li>
        </ul>
    </li>

    <li><a class="app-menu__item {{ str_contains($activePage, 'admin/guests') ? 'active' : '' }}" href="{{ route('admin.guests') }}"><i class="app-menu__icon fa fa-users"></i><span class="app-menu__label">Guest List</span></a></li>

    <li><a class="app-menu__item {{ str_contains($activePage, 'admin/room-issues') ? 'active' : '' }}" href="{{ route('admin.rooms.issues') }}"><i class="app-menu__icon fa fa-wrench"></i><span class="app-menu__label">Room Issues</span>@if(isset($sidebarBadges['room_issues']) && $sidebarBadges['room_issues'] > 0)<span class="badge badge-danger badge-pill ml-2">{{ $sidebarBadges['room_issues'] }}</span>@endif</a></li>
    
    <li><a class="app-menu__item {{ str_contains($activePage, 'admin/issues') ? 'active' : '' }}" href="{{ route('admin.issues.index') }}"><i class="app-menu__icon fa fa-exclamation-triangle"></i><span class="app-menu__label">Guest Issue Reports</span>@if(isset($sidebarBadges['issues']) && $sidebarBadges['issues'] > 0)<span class="badge badge-warning badge-pill ml-2">{{ $sidebarBadges['issues'] }}</span>@endif</a></li>

    <li class="treeview {{ str_contains($activePage, 'admin/rooms') ? 'is-expanded' : '' }}">
        <a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-bed"></i><span class="app-menu__label">Room Management</span><i class="treeview-indicator fa fa-angle-right"></i></a>
        <ul class="treeview-menu">
            <li><a class="treeview-item" href="{{ route('admin.rooms.status') }}"><i class="icon fa fa-info-circle"></i> View Occupancy</a></li>
            <li><a class="treeview-item" href="{{ route('admin.rooms.index') }}"><i class="icon fa fa-list"></i> Manage Rooms</a></li>
            <li><a class="treeview-item" href="{{ route('admin.rooms.create') }}"><i class="icon fa fa-plus-circle"></i> Add New Room</a></li>
            <li><a class="treeview-item" href="{{ route('admin.rooms.cleaning') }}"><i class="icon fa fa-broom"></i> Cleaning Schedule</a></li>
        </ul>
    </li>

    <li><a class="app-menu__item {{ str_contains($activePage, 'housekeeper/dashboard') ? 'active' : '' }}" href="{{ route('housekeeper.dashboard') }}"><i class="app-menu__icon fa fa-home"></i><span class="app-menu__label">Housekeeping Dashboard</span></a></li>

    {{-- 5. STOCK & PROCUREMENT --}}
    <li class="treeview-item-header" style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">Stock & Procurement</li>
    <li class="treeview {{ str_contains($activePage, 'suppliers') || str_contains($activePage, 'purchase-requests') || str_contains($activePage, 'housekeeping-inventory') || str_contains($activePage, 'restaurants/stock') || str_contains($activePage, 'shopping-list/transfers') || str_contains($activePage, 'bar-keeper/reports') || str_contains($activePage, 'chef-master/reports') || str_contains($activePage, 'housekeeper/reports') ? 'is-expanded' : '' }}">
        <a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-clipboard"></i><span class="app-menu__label">Daily Stock Sheets</span><i class="treeview-indicator fa fa-angle-right"></i></a>
        <ul class="treeview-menu">
            <li><a class="treeview-item" href="{{ route('bar-keeper.reports') }}"><i class="icon fa fa-glass"></i> Bar Report</a></li>
            <li><a class="treeview-item" href="{{ route('chef-master.reports') }}"><i class="icon fa fa-cutlery"></i> Kitchen Report</a></li>
            <li><a class="treeview-item" href="{{ route('housekeeper.reports') }}"><i class="icon fa fa-bed"></i> Housekeeping Report</a></li>
        </ul>
    </li>

    <li class="treeview {{ str_contains($activePage, 'suppliers') || str_contains($activePage, 'purchase-requests') || str_contains($activePage, 'housekeeping-inventory') || str_contains($activePage, 'restaurants/stock') || str_contains($activePage, 'shopping-list/transfers') ? 'is-expanded' : '' }}">
        <a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-cubes"></i><span class="app-menu__label">Inventory Control</span><i class="treeview-indicator fa fa-angle-right"></i></a>
        <ul class="treeview-menu">
            <li><a class="treeview-item" href="{{ route('admin.housekeeping-inventory') }}"><i class="icon fa fa-bed"></i> Housekeeping Stock</a></li>
            <li><a class="treeview-item" href="{{ route('admin.suppliers.index') }}"><i class="icon fa fa-truck"></i> Suppliers Directory</a></li>
            <li class="treeview-divider"></li>
            <li><a class="treeview-item" href="{{ route('admin.purchase-requests.index') }}"><i class="icon fa fa-shopping-cart"></i> Purchase Requests</a></li>
            <li><a class="treeview-item" href="{{ route('admin.purchase-requests.received') }}"><i class="icon fa fa-check-circle"></i> Received Items</a></li>
            <li><a class="treeview-item" href="{{ route('admin.purchase-reports.index') }}"><i class="icon fa fa-file-text"></i> Purchase Analysis</a></li>
            <li class="treeview-divider"></li>
            <li><a class="treeview-item" href="{{ route('admin.restaurants.shopping-list.transfers') }}"><i class="icon fa fa-exchange"></i> Stock Transfers</a></li>
        </ul>
    </li>

    {{-- 6. FINANCE & ANALYTICS --}}
    <li class="treeview-item-header" style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">Finance & Analytics</li>
    <li><a class="app-menu__item {{ str_contains($activePage, 'admin/reports') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}"><i class="app-menu__icon fa fa-bar-chart"></i><span class="app-menu__label">Reports Dashboard</span></a></li>
    <li><a class="app-menu__item {{ str_contains($activePage, 'admin/payments') ? 'active' : '' }}" href="{{ route('admin.payments') }}"><i class="app-menu__icon fa fa-money"></i><span class="app-menu__label">All Payments</span></a></li>

    <li><a class="app-menu__item {{ str_contains($activePage, 'admin/feedback') ? 'active' : '' }}" href="{{ route('admin.feedback') }}"><i class="app-menu__icon fa fa-star"></i><span class="app-menu__label">Guest Feedback</span></a></li>

    {{-- 7. ADMINISTRATION --}}
    <li class="treeview-item-header" style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">Administration</li>
    <li><a class="app-menu__item {{ str_contains($activePage, 'admin/users') ? 'active' : '' }}" href="{{ route('admin.users') }}"><i class="app-menu__icon fa fa-user-secret"></i><span class="app-menu__label">Staff Management</span></a></li>
    
    <li class="treeview {{ str_contains($activePage, 'admin/settings') ? 'is-expanded' : '' }}">
        <a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-cog"></i><span class="app-menu__label">Settings</span><i class="treeview-indicator fa fa-angle-right"></i></a>
        <ul class="treeview-menu">
            <li><a class="treeview-item" href="{{ route('admin.settings.hotel') }}"><i class="icon fa fa-building"></i> Hotel Info</a></li>
            <li><a class="treeview-item" href="{{ route('admin.settings.rooms') }}"><i class="icon fa fa-bed"></i> Room Settings</a></li>
        </ul>
    </li>
@endif

{{-- 5. Utilities (Always show) --}}

