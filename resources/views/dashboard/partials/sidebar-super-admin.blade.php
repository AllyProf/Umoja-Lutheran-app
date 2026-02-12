{{-- Super Admin Sidebar Menu --}}
@php
  use App\Services\RolePermissionService;
  
  $currentRoute = request()->route() ? request()->route()->getName() : '';
  $activePage = request()->path();
  
  // Super admin always has all permissions - no need to check
  // This sidebar only appears for users with 'super_admin' role (checked in app.blade.php)
@endphp

{{-- ============================================ --}}
{{-- DASHBOARD --}}
{{-- ============================================ --}}
<li><a class="app-menu__item {{ $currentRoute === 'super_admin.dashboard' || str_contains($activePage, 'super-admin/dashboard') ? 'active' : '' }}" href="{{ route('super_admin.dashboard') }}"><i class="app-menu__icon fa fa-dashboard"></i><span class="app-menu__label">Dashboard</span></a></li>

{{-- ============================================ --}}
{{-- USER MANAGEMENT --}}
{{-- ============================================ --}}
<li class="treeview-item-header" style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">User Management</li>

<li class="treeview {{ str_contains($activePage ?? '', 'super-admin/users') ? 'is-expanded' : '' }}">
  <a class="app-menu__item {{ str_contains($activePage ?? '', 'super-admin/users') ? 'active' : '' }}" href="#" data-toggle="treeview">
    <i class="app-menu__icon fa fa-users"></i>
    <span class="app-menu__label">Users</span>
    <i class="treeview-indicator fa fa-angle-right"></i>
  </a>
  <ul class="treeview-menu">
    <li><a class="treeview-item {{ str_contains($activePage ?? '', 'super-admin/users') && !str_contains($activePage ?? '', 'create') && !str_contains($activePage ?? '', 'edit') ? 'active' : '' }}" href="{{ route('super_admin.users') }}"><i class="icon fa fa-list"></i> All Users</a></li>
    <li><a class="treeview-item {{ str_contains($activePage ?? '', 'super-admin/users/create') ? 'active' : '' }}" href="{{ route('super_admin.users.create') }}"><i class="icon fa fa-plus-circle"></i> Create User</a></li>
  </ul>
</li>

<li class="treeview {{ str_contains($activePage ?? '', 'super-admin/roles') || str_contains($activePage ?? '', 'super-admin/permissions') ? 'is-expanded' : '' }}">
  <a class="app-menu__item {{ str_contains($activePage ?? '', 'super-admin/roles') || str_contains($activePage ?? '', 'super-admin/permissions') ? 'active' : '' }}" href="#" data-toggle="treeview">
    <i class="app-menu__icon fa fa-key"></i>
    <span class="app-menu__label">Roles & Permissions</span>
    <i class="treeview-indicator fa fa-angle-right"></i>
  </a>
  <ul class="treeview-menu">
    <li><a class="treeview-item {{ str_contains($activePage ?? '', 'super-admin/roles') ? 'active' : '' }}" href="{{ route('super_admin.roles') }}"><i class="icon fa fa-shield"></i> Roles</a></li>
    <li><a class="treeview-item {{ str_contains($activePage ?? '', 'super-admin/permissions') ? 'active' : '' }}" href="{{ route('super_admin.permissions') }}"><i class="icon fa fa-lock"></i> Permissions</a></li>
  </ul>
</li>

{{-- ============================================ --}}
{{-- KITCHEN & RESTAURANT --}}
{{-- ============================================ --}}
<li class="treeview-item-header" style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">Kitchen & Restaurant</li>
<li class="treeview {{ (str_contains($activePage, 'restaurant/food') || str_contains($activePage, 'recipes')) ? 'is-expanded' : '' }}">
  <a class="app-menu__item {{ (str_contains($activePage, 'restaurant/food') || str_contains($activePage, 'recipes')) ? 'active' : '' }}" href="#" data-toggle="treeview">
    <i class="app-menu__icon fa fa-cutlery"></i>
    <span class="app-menu__label">Food Operations</span>
    <i class="treeview-indicator fa fa-angle-right"></i>
  </a>
  <ul class="treeview-menu">
    <li><a class="treeview-item {{ str_contains($activePage, 'recipes') ? 'active' : '' }}" href="{{ route('admin.recipes.index') }}"><i class="icon fa fa-book"></i> Menu Recipes</a></li>
    <li><a class="treeview-item {{ str_contains($activePage, 'restaurant/food/orders') ? 'active' : '' }}" href="{{ route('admin.restaurants.kitchen.orders') }}"><i class="icon fa fa-bell"></i> Live Orders</a></li>
  </ul>
</li>

{{-- ============================================ --}}
{{-- SYSTEM MONITORING --}}
{{-- ============================================ --}}
<li class="treeview-item-header" style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">System Monitoring</li>

<li class="treeview {{ str_contains($activePage ?? '', 'super-admin/activity-logs') || str_contains($activePage ?? '', 'super-admin/system-logs') || str_contains($activePage ?? '', 'super-admin/failed-login') ? 'is-expanded' : '' }}">
  <a class="app-menu__item {{ str_contains($activePage ?? '', 'super-admin/activity-logs') || str_contains($activePage ?? '', 'super-admin/system-logs') || str_contains($activePage ?? '', 'super-admin/failed-login') ? 'active' : '' }}" href="#" data-toggle="treeview">
    <i class="app-menu__icon fa fa-file-text-o"></i>
    <span class="app-menu__label">System Logs</span>
    <i class="treeview-indicator fa fa-angle-right"></i>
  </a>
  <ul class="treeview-menu">
    <li><a class="treeview-item {{ str_contains($activePage ?? '', 'super-admin/activity-logs') ? 'active' : '' }}" href="{{ route('super_admin.activity-logs') }}"><i class="icon fa fa-history"></i> Activity Logs</a></li>
    <li><a class="treeview-item {{ str_contains($activePage ?? '', 'super-admin/system-logs') ? 'active' : '' }}" href="{{ route('super_admin.system-logs') }}"><i class="icon fa fa-file-text"></i> System Logs</a></li>
    <li><a class="treeview-item {{ str_contains($activePage ?? '', 'super-admin/failed-login') ? 'active' : '' }}" href="{{ route('super_admin.failed-login-attempts') }}"><i class="icon fa fa-ban"></i> Failed Logins</a></li>
  </ul>
</li>

{{-- ============================================ --}}
{{-- SYSTEM MANAGEMENT --}}
{{-- ============================================ --}}
<li class="treeview-item-header" style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">System Management</li>

<li class="treeview {{ str_contains($activePage ?? '', 'super-admin/active-sessions') || str_contains($activePage ?? '', 'super-admin/cache') || str_contains($activePage ?? '', 'super-admin/system-settings') ? 'is-expanded' : '' }}">
  <a class="app-menu__item {{ str_contains($activePage ?? '', 'super-admin/active-sessions') || str_contains($activePage ?? '', 'super-admin/cache') || str_contains($activePage ?? '', 'super-admin/system-settings') ? 'active' : '' }}" href="#" data-toggle="treeview">
    <i class="app-menu__icon fa fa-cog"></i>
    <span class="app-menu__label">System Settings</span>
    <i class="treeview-indicator fa fa-angle-right"></i>
  </a>
  <ul class="treeview-menu">
    <li><a class="treeview-item {{ str_contains($activePage ?? '', 'super-admin/system-settings') ? 'active' : '' }}" href="{{ route('super_admin.system-settings') }}"><i class="icon fa fa-cog"></i> General Settings</a></li>
    <li><a class="treeview-item {{ str_contains($activePage ?? '', 'super-admin/active-sessions') ? 'active' : '' }}" href="{{ route('super_admin.active-sessions') }}"><i class="icon fa fa-users"></i> Active Sessions</a></li>
    <li><a class="treeview-item {{ str_contains($activePage ?? '', 'super-admin/cache') ? 'active' : '' }}" href="{{ route('super_admin.cache-management') }}"><i class="icon fa fa-database"></i> Cache Management</a></li>
  </ul>
</li>

{{-- ============================================ --}}
{{-- ACCOUNT --}}
{{-- ============================================ --}}
<li class="treeview-item-header" style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">Account</li>

<li><a class="app-menu__item {{ $currentRoute === 'super_admin.profile' || str_contains($activePage, 'super-admin/profile') ? 'active' : '' }}" href="{{ route('super_admin.profile') }}"><i class="app-menu__icon fa fa-user"></i><span class="app-menu__label">Profile</span></a></li>
