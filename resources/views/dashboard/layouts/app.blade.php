<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="description" content="Umoj Lutheran Hostel Management System">
    <title>{{ config('app.name') }} - {{ $role ?? 'Dashboard' }}</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Main CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboard_assets/css/main.css') }}">
    <!-- Font-icon css-->
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
      @font-face {
        font-family: 'Century Gothic';
        src: local('Century Gothic'), local('CenturyGothic'), url('https://fonts.cdnfonts.com/s/15454/GOTHIC.woff') format('woff');
      }

      :root {
        --primary-color: #940000;
        --secondary-color: #000000;
        --text-white: #ffffff;
      }

      body {
        font-family: 'Century Gothic', 'Segoe UI', Tahoma, sans-serif !important;
      }
      
      .app-header {
        background-color: var(--primary-color) !important;
      }
      
      .app-sidebar {
        background-color: var(--secondary-color) !important;
      }
      
      .app-sidebar__user {
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      }
      
      .app-menu__item.active, .app-menu__item:hover, .app-menu__item:focus {
        border-left-color: var(--primary-color) !important;
      }
      
      .sidebar-mini.sidebar-collapse .app-sidebar__toggle {
        background-color: var(--primary-color) !important;
      }
      
      .btn-primary {
        background-color: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
      }
      
      .btn-primary:hover, .btn-primary:focus {
        background-color: #7b0000 !important;
        border-color: #7b0000 !important;
      }
      
      .toggle-label:before {
        background-color: var(--primary-color) !important;
      }
      
      .badge-primary {
        background-color: var(--primary-color) !important;
      }
      
      .page-item.active .page-link {
        background-color: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
      }
      
      .loading-logo-text {
        color: var(--primary-color) !important;
      }
      
      .spinner, .spinner-inner, .loading-progress-bar {
        border-top-color: var(--primary-color) !important;
      }
      
      .spinner-dot {
        background: var(--primary-color) !important;
      }
      
      .loading-progress-bar {
        background: var(--primary-color) !important;
      }
      
      .swal2-confirm {
        background-color: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
      }
      
      .swal2-confirm:hover {
        background-color: #7b0000 !important;
        border-color: #7b0000 !important;
      }
      
      .text-primary {
        color: var(--primary-color) !important;
      }
      
      /* Loading Container Styles */
      .loading-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: #fff;
        z-index: 99999;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        transition: opacity 0.5s ease-out;
        visibility: visible;
        opacity: 1;
      }
      
      /* Prevent content flash by hiding body initially */
      body:not(.page-loaded) {
        overflow: hidden;
      }
      
      body.page-loaded {
        overflow: auto;
      }
      
      .loading-container.hidden {
        opacity: 0;
        pointer-events: none;
      }
      
      .loading-logo {
        text-align: center;
        margin-bottom: 30px;
      }
      
      .loading-logo img {
        max-width: 200px;
        height: auto;
        margin-bottom: 10px;
      }
      
      .loading-logo-text {
        font-size: 28px;
        font-weight: 700;
        color: var(--primary-color);
        letter-spacing: 2px;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
      }
      
      .spinner-wrapper {
        position: relative;
        width: 60px;
        height: 60px;
        margin: 20px auto;
      }
      
      .spinner {
        width: 60px;
        height: 60px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid var(--primary-color);
        border-radius: 50%;
        animation: spin 1s linear infinite;
      }
      
      .spinner-inner {
        position: absolute;
        top: 10px;
        left: 10px;
        width: 40px;
        height: 40px;
        border: 3px solid #f3f3f3;
        border-top: 3px solid var(--primary-color);
        border-radius: 50%;
        animation: spin 0.8s linear infinite reverse;
      }
      
      .spinner-dot {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 8px;
        height: 8px;
        background: var(--primary-color);
        border-radius: 50%;
        transform: translate(-50%, -50%);
      }
      
      @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
      }
      
      .loading-text {
        font-size: 16px;
        color: #666;
        margin-top: 20px;
        font-weight: 500;
      }
      
      .loading-progress {
        width: 200px;
        height: 4px;
        background: #f3f3f3;
        border-radius: 2px;
        margin-top: 20px;
        overflow: hidden;
      }
      
      .loading-progress-bar {
        height: 100%;
        background: var(--primary-color);
        width: 0%;
        animation: progress 2s ease-in-out infinite;
      }
      
      @keyframes progress {
        0% { width: 0%; }
        50% { width: 70%; }
        100% { width: 100%; }
      }
      
      /* Toast Notification Styles */
      #toast-container {
        position: fixed;
        top: 70px;
        right: 20px;
        z-index: 9999;
        max-width: 400px;
      }
      
      .toast-notification {
        background: white;
        border-radius: 4px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        margin-bottom: 0;
        padding: 15px;
        display: flex;
        align-items: flex-start;
        animation: slideInRight 0.3s ease;
        border-left: 4px solid;
        min-width: 300px;
      }
      
      /* Ensure only one notification is visible */
      #toast-container .toast-notification:not(:first-child) {
        display: none;
      }
      
      .toast-notification.toast-primary { border-left-color: #007bff; }
      .toast-notification.toast-success { border-left-color: #28a745; }
      .toast-notification.toast-danger { border-left-color: #dc3545; }
      .toast-notification.toast-warning { border-left-color: #ffc107; }
      .toast-notification.toast-info { border-left-color: #17a2b8; }
      
      .toast-notification .toast-icon {
        font-size: 20px;
        margin-right: 12px;
        flex-shrink: 0;
        margin-top: 2px;
      }
      
      .toast-notification.toast-primary .toast-icon { color: #007bff; }
      .toast-notification.toast-success .toast-icon { color: #28a745; }
      .toast-notification.toast-danger .toast-icon { color: #dc3545; }
      .toast-notification.toast-warning .toast-icon { color: #ffc107; }
      .toast-notification.toast-info .toast-icon { color: #17a2b8; }
      
      .toast-content {
        flex: 1;
      }
      
      .toast-title {
        font-weight: 600;
        margin-bottom: 4px;
        font-size: 14px;
      }
      
      .toast-message {
        font-size: 13px;
        color: #666;
        margin-bottom: 8px;
      }
      
      .toast-actions {
        margin-top: 8px;
      }
      
      .toast-actions a {
        font-size: 12px;
        padding: 4px 12px;
        margin-right: 8px;
        text-decoration: none;
        border-radius: 3px;
        display: inline-block;
        background-color: #007bff;
        color: white;
        transition: background-color 0.2s;
      }
      
      .toast-actions a:hover {
        background-color: #0056b3;
        color: white;
      }
      
      .toast-close {
        background: none;
        border: none;
        font-size: 18px;
        color: #999;
        cursor: pointer;
        padding: 0;
        margin-left: 10px;
        line-height: 1;
        flex-shrink: 0;
      }
      
      .toast-close:hover {
        color: #333;
      }
      
      @keyframes slideInRight {
        from {
          transform: translateX(100%);
          opacity: 0;
        }
        to {
          transform: translateX(0);
          opacity: 1;
        }
      }
      
      @keyframes slideOutRight {
        from {
          transform: translateX(0);
          opacity: 1;
        }
        to {
          transform: translateX(100%);
          opacity: 0;
        }
      }
      
      .toast-notification.removing {
        animation: slideOutRight 0.3s ease;
      }
      
      /* Mobile Header Logo - White Color and Centered */
      @media (max-width: 768px) {
        .app-header {
           padding-right: 5px !important; /* Ensure right padding is small */
        }
        
        .app-header__logo {
          color: #ffffff !important;
          text-align: center !important;
          flex: 1 !important; /* Allow shrinking if needed */
          font-size: 18px !important; /* Smaller logo font */
          white-space: nowrap;
          overflow: hidden;
          text-overflow: ellipsis;
        }
        
        /* Hide search on mobile to save space for icons */
        .app-search {
            display: none !important;
        }
        
        /* Ensure nav items are visible on mobile */
        .app-nav {
          display: flex !important;
          margin-bottom: 0 !important;
          padding-left: 0 !important;
        }
        
        .app-nav li:not(.app-search) {
          display: block !important;
        }
        
        /* Reduce padding on nav items for mobile to fit more icons */
        .app-nav__item {
            padding: 15px 8px !important;
        }
      }
    </style>
    <!-- Critical inline style to prevent FOUC -->
    <style>
      /* Prevent flash of unstyled content */
      body:not(.page-loaded) {
        overflow: hidden;
      }
      .loading-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: #fff;
        z-index: 99999;
        display: flex;
        visibility: visible;
        opacity: 1;
      }
      .loading-container.hidden {
        display: none !important;
        visibility: hidden !important;
      }
    </style>
    @yield('styles')
  </head>
  <body class="app sidebar-mini rtl">
    <!-- Loading overlay - must be first to prevent FOUC -->
    <div class="loading-container" id="loadingContainer">
        <div class="loading-logo">
            <div class="loading-logo-text">UMOJA LUTHERAN</div>
        </div>
        <div class="spinner-wrapper">
            <div class="spinner"></div>
            <div class="spinner-inner"></div>
            <div class="spinner-dot"></div>
        </div>
        <div class="loading-text">Loading Dashboard...</div>
        <div class="loading-progress">
            <div class="loading-progress-bar"></div>
        </div>
    </div>
    <!-- Navbar-->
    <header class="app-header">
      @php
        // Determine current role from authenticated user if not passed
        $currentRole = $role ?? null;
        if (!$currentRole) {
          $currentAuthUser = auth()->guard('staff')->user() ?? auth()->guard('guest')->user();
          if ($currentAuthUser instanceof \App\Models\Staff) {
            $rawRole = $currentAuthUser->role ?? '';
            $normalizedRole = strtolower(str_replace([' ', '_'], '', trim($rawRole)));
            
            if ($normalizedRole === 'superadmin' || strtolower($rawRole) === 'super_admin' || strtolower($rawRole) === 'super admin') {
              $currentRole = 'super_admin';
            } elseif ($normalizedRole === 'manager' || strtolower($rawRole) === 'manager') {
              $currentRole = 'manager';
            } elseif ($normalizedRole === 'reception' || strtolower($rawRole) === 'reception') {
              $currentRole = 'reception';
            } elseif ($normalizedRole === 'barkeeper' || $normalizedRole === 'barkeepr' || strtolower($rawRole) === 'bar_keeper' || strtolower($rawRole) === 'bar keeper' || strtolower($rawRole) === 'bartender') {
              $currentRole = 'bar_keeper';
            } elseif ($normalizedRole === 'headchef' || strtolower($rawRole) === 'head_chef' || strtolower($rawRole) === 'head chef') {
              $currentRole = 'head_chef';
            } elseif ($normalizedRole === 'housekeeper' || strtolower($rawRole) === 'housekeeper') {
              $currentRole = 'housekeeper';
            } elseif ($normalizedRole === 'waiter' || strtolower($rawRole) === 'waiter') {
              $currentRole = 'waiter';
            } else {
              $currentRole = 'manager'; // default fallback
            }
          } elseif ($currentAuthUser instanceof \App\Models\Guest) {
            $currentRole = 'customer';
          } else {
            $currentRole = 'manager'; // default fallback
          }
        }
        
        // Set logo route based on role
        if ($currentRole === 'super_admin') {
          $logoRoute = route('super_admin.dashboard');
        } elseif ($currentRole === 'manager') {
          $logoRoute = route('admin.dashboard');
        } elseif ($currentRole === 'head_chef') {
          $logoRoute = route('chef-master.dashboard');
        } elseif ($currentRole === 'housekeeper') {
          $logoRoute = route('housekeeper.dashboard');
        } elseif ($currentRole === 'bar_keeper') {
          $logoRoute = route('bar-keeper.dashboard');
        } elseif ($currentRole === 'waiter') {
          $logoRoute = route('waiter.dashboard');
        } elseif (in_array($currentRole, ['customer', 'guest'])) {
          $logoRoute = route('customer.dashboard');
        } else {
          $logoRoute = route('reception.dashboard');
        }
      @endphp
      <a class="app-header__logo" href="{{ $logoRoute }}" style="text-decoration: none; color: #ffffff; font-size: 20px; font-weight: 700; letter-spacing: 1px; text-shadow: 0 1px 2px rgba(0,0,0,0.1);">
        Umoja Lutheran
      </a>
      <!-- Sidebar toggle button-->
      <a class="app-sidebar__toggle" href="#" data-toggle="sidebar" aria-label="Hide Sidebar"></a>
      <!-- Navbar Right Menu-->
      <ul class="app-nav">
        <li class="app-search">
          <input class="app-search__input" type="search" placeholder="Search">
          <button class="app-search__button"><i class="fa fa-search"></i></button>
        </li>
        <!-- Low Stock Alert Menu -->
        @if(isset($lowStockHousekeepingCount) && $lowStockHousekeepingCount > 0)
        <li class="dropdown">
          <a class="app-nav__item" href="#" data-toggle="dropdown" aria-label="Show low stock alerts" style="position: relative; display: inline-block; color: #ffc107;">
            <i class="fa fa-exclamation-triangle fa-lg"></i>
            <span class="badge badge-warning" style="position: absolute; top: -5px; right: -8px; font-size: 10px; font-weight: bold; padding: 2px 6px; border-radius: 10px; min-width: 18px; height: 18px; text-align: center; line-height: 14px; z-index: 1000; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">{{ $lowStockHousekeepingCount }}</span>
          </a>
          <ul class="app-notification dropdown-menu dropdown-menu-right">
            <li class="app-notification__title text-danger">
              <i class="fa fa-warning"></i> Low Stock Alert ({{ $lowStockHousekeepingCount }} items)
            </li>
            <div class="app-notification__content">
              @foreach($lowStockHousekeepingItems as $item)
                <li>
                  <a class="app-notification__item" href="{{ route('housekeeper.inventory') }}">
                    <span class="app-notification__icon">
                      <span class="fa-stack fa-lg">
                        <i class="fa fa-circle fa-stack-2x text-warning"></i>
                        <i class="fa fa-cubes fa-stack-1x fa-inverse"></i>
                      </span>
                    </span>
                    <div>
                      <p class="app-notification__message"><strong>{{ $item->name }}</strong> is low on stock!</p>
                      <p class="app-notification__meta">Current: {{ $item->current_stock }} {{ $item->unit }} (Min: {{ $item->minimum_stock }})</p>
                    </div>
                  </a>
                </li>
              @endforeach
            </div>
            <li class="app-notification__footer">
              <a href="{{ route('housekeeper.inventory') }}">Manage Inventory</a>
            </li>
          </ul>
        </li>
        @endif

        <!--Notification Menu-->
        <li class="dropdown">
          <a class="app-nav__item" href="#" data-toggle="dropdown" aria-label="Show notifications" style="position: relative; display: inline-block;">
            <i class="fa fa-bell-o fa-lg"></i>
            @if(isset($unreadNotificationCount) && $unreadNotificationCount > 0)
              <span class="badge badge-danger notification-badge" style="position: absolute; top: -2px; right: -2px; background-color: #FF0000 !important; font-size: 10px; font-weight: bold; padding: 2px 5px; border-radius: 50%; min-width: 18px; height: 18px; text-align: center; line-height: 14px; z-index: 1000; border: 1px solid #fff; box-shadow: 0 1px 2px rgba(0,0,0,0.2);">{{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}</span>
            @endif
          </a>
          <ul class="app-notification dropdown-menu dropdown-menu-right" style="min-width: 350px; width: 350px;">
            <li class="app-notification__title">
              @if(isset($unreadNotificationCount) && $unreadNotificationCount > 0)
                You have {{ $unreadNotificationCount }} new {{ $unreadNotificationCount === 1 ? 'notification' : 'notifications' }}.
              @else
                You have no new notifications.
              @endif
            </li>
            <div class="app-notification__content">
              @if(isset($notifications) && $notifications->count() > 0)
                @foreach($notifications as $notification)
                  <li>
                    <a class="app-notification__item" href="{{ $notification->link ?? '#' }}" onclick="return markNotificationAsRead(event, {{ $notification->id }}, '{{ $notification->link ?? '' }}')">
                      <span class="app-notification__icon">
                        <span class="fa-stack fa-lg">
                          <i class="fa fa-circle fa-stack-2x text-{{ $notification->color }}"></i>
                          <i class="fa {{ $notification->icon }} fa-stack-1x fa-inverse"></i>
                        </span>
                      </span>
                      <div>
                        <p class="app-notification__message">{{ $notification->message }}</p>
                        <p class="app-notification__meta">{{ $notification->created_at->diffForHumans() }}</p>
                      </div>
                    </a>
                  </li>
                @endforeach
              @endif
            </div>
            @if(isset($unreadNotificationCount) && $unreadNotificationCount > 0)
              <li class="app-notification__footer">
                <a href="javascript:;" onclick="markAllNotificationsAsRead()">Mark all as read</a>
              </li>
            @endif
          </ul>
        </li>
        <!-- User Menu-->
        <li class="dropdown"><a class="app-nav__item" href="#" data-toggle="dropdown" aria-label="Open Profile Menu"><i class="fa fa-user fa-lg"></i></a>
          <ul class="dropdown-menu settings-menu dropdown-menu-right">
            @php
              // Use the role determined above, or determine it again if needed
              $headerRole = $currentRole ?? 'manager';
              $isCustomer = ($headerRole === 'customer' || $headerRole === 'guest');
              $isReception = ($headerRole === 'reception');
              
              // Determine profile route based on role
              if ($headerRole === 'super_admin') {
                $profileRoute = route('super_admin.profile');
                $logoutRoute = route('super_admin.logout');
              } elseif ($headerRole === 'manager') {
                $profileRoute = route('admin.profile');
                $logoutRoute = route('admin.logout');
              } elseif ($headerRole === 'reception') {
                $profileRoute = route('reception.profile');
                $logoutRoute = route('reception.logout');
              } elseif ($headerRole === 'housekeeper') {
                $profileRoute = route('housekeeper.profile');
                $logoutRoute = route('housekeeper.logout');
              } elseif ($headerRole === 'bar_keeper') {
                $profileRoute = route('bar-keeper.profile');
                $logoutRoute = route('bar-keeper.logout');
              } elseif ($headerRole === 'head_chef') {
                $profileRoute = route('chef-master.profile');
                $logoutRoute = route('chef-master.logout');
              } elseif ($headerRole === 'waiter') {
                $profileRoute = route('waiter.profile');
                $logoutRoute = route('waiter.logout');
              } else {
                $profileRoute = route('customer.profile');
                $logoutRoute = route('customer.logout');
              }
            @endphp
            @if(!$isCustomer && !$isReception)
            <li><a class="dropdown-item" href="{{ $profileRoute }}"><i class="fa fa-cog fa-lg"></i> Settings</a></li>
            @endif
            <li><a class="dropdown-item" href="{{ $profileRoute }}"><i class="fa fa-user fa-lg"></i> Profile</a></li>
            <li>
              <form id="logout-form" action="{{ $logoutRoute }}" method="POST" style="display: none;">
                @csrf
              </form>
              <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fa fa-sign-out fa-lg"></i> Logout
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </header>
    <!-- Sidebar menu-->
    <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
    <aside class="app-sidebar">
      @php
        $currentRoute = request()->route() ? request()->route()->getName() : '';
        $activePage = request()->path();
        $currentAuthUser = auth()->guard('staff')->user() ?? auth()->guard('guest')->user();
        
        // Get user profile photo or use default
        $userPhoto = null;
        if ($currentAuthUser && $currentAuthUser->profile_photo) {
          // Add cache-busting parameter to ensure fresh image after update
          $userPhoto = asset('storage/' . $currentAuthUser->profile_photo) . '?v=' . ($currentAuthUser->updated_at ? $currentAuthUser->updated_at->timestamp : time());
        } else {
          // Use UI Avatars as fallback with user's name
          $userNameForAvatar = $userName ?? ($currentAuthUser->name ?? 'User');
          $userPhoto = 'https://ui-avatars.com/api/?name=' . urlencode($userNameForAvatar) . '&size=128&background=007bff&color=fff';
        }
      @endphp
      <div class="app-sidebar__user">
        <img class="app-sidebar__user-avatar" src="{{ $userPhoto }}" alt="User Image" id="sidebarUserAvatar" style="object-fit: cover;">
        <div>
          <p class="app-sidebar__user-name">
            @php
              // Use passed $userName if available, otherwise use authenticated user's name
              $displayName = null;
              if (!empty($userName)) {
                $displayName = $userName;
              } elseif ($currentAuthUser && !empty($currentAuthUser->name)) {
                $displayName = $currentAuthUser->name;
              } else {
                $displayName = 'User';
              }
              echo $displayName;
            @endphp
          </p>
          <p class="app-sidebar__user-designation">
            @php
              // Determine role from authenticated user if not passed
              $displayRole = $role ?? null;
              if (!$displayRole && $currentAuthUser) {
                if ($currentAuthUser instanceof \App\Models\Staff) {
                  $rawRole = $currentAuthUser->role ?? '';
                  $normalizedRole = strtolower(str_replace([' ', '_'], '', trim($rawRole)));
                  $rawRoleLower = strtolower(trim($rawRole));
                  
                  if ($normalizedRole === 'superadmin' || $rawRoleLower === 'super_admin' || $rawRoleLower === 'super admin') {
                    $displayRole = 'super_admin';
                  } elseif ($normalizedRole === 'manager' || $rawRoleLower === 'manager') {
                    $displayRole = 'manager';
                  } elseif ($normalizedRole === 'reception' || $rawRoleLower === 'reception') {
                    $displayRole = 'reception';
                  } elseif ($normalizedRole === 'barkeeper' || $normalizedRole === 'barkeepr' || $rawRoleLower === 'bar_keeper' || $rawRoleLower === 'bar keeper' || $rawRoleLower === 'bartender') {
                    $displayRole = 'bar_keeper';
                  } elseif ($normalizedRole === 'headchef' || strtolower($rawRole) === 'head_chef' || strtolower($rawRole) === 'head chef') {
                    $displayRole = 'head_chef';
                  } elseif ($normalizedRole === 'housekeeper' || strtolower($rawRole) === 'housekeeper') {
                    $displayRole = 'housekeeper';
                  } elseif ($normalizedRole === 'waiter' || strtolower($rawRole) === 'waiter') {
                    $displayRole = 'waiter';
                  }
                } elseif ($currentAuthUser instanceof \App\Models\Guest) {
                  $displayRole = 'customer';
                }
              }
              
              // Display role name
              if ($displayRole === 'customer' || $displayRole === 'Customer') {
                echo 'Hostel Guest';
              } elseif ($displayRole === 'bar_keeper') {
                echo 'Bar Keeper';
              } elseif ($displayRole === 'head_chef') {
                echo 'Head Chef';
              } elseif ($displayRole === 'super_admin') {
                echo 'Super Admin';
              } else {
                echo ucfirst($displayRole ?: 'Manager');
              }
            @endphp
          </p>
        </div>
      </div>
      <ul class="app-menu">
        @php
          // Ensure we have a strongly-typed current user object for sidebar permissions
          $currentUser = $currentAuthUser ?? ($currentUser ?? null);
          
          // ROLE-BASED SIDEBAR SELECTION: Show sidebar based on user's ACTUAL role, not URL
          // This ensures users only see sidebars for roles they actually have
          
          // Determine actual role from session (source of truth)
          $sessionRole = null;
          if ($currentAuthUser) {
            if ($currentAuthUser instanceof \App\Models\Staff) {
              $rawRole = $currentAuthUser->role ?? '';
              $normalizedRole = strtolower(str_replace([' ', '_'], '', trim($rawRole)));
              
              if ($normalizedRole === 'superadmin' || $rawRole === 'super_admin' || strtolower($rawRole) === 'super admin') {
                $sessionRole = 'super_admin';
              } elseif ($normalizedRole === 'manager' || $rawRole === 'manager') {
                $sessionRole = 'manager';
              } elseif ($normalizedRole === 'reception' || $rawRole === 'reception') {
                $sessionRole = 'reception';
              } elseif ($normalizedRole === 'barkeeper' || $normalizedRole === 'barkeepr' || $rawRole === 'bar_keeper' || strtolower($rawRole) === 'bar keeper' || strtolower($rawRole) === 'bartender') {
                $sessionRole = 'bar_keeper';
              } elseif ($normalizedRole === 'headchef' || $rawRole === 'head_chef' || strtolower($rawRole) === 'head chef') {
                $sessionRole = 'head_chef';
              } elseif ($normalizedRole === 'housekeeper' || $rawRole === 'housekeeper') {
                $sessionRole = 'housekeeper';
              } elseif ($normalizedRole === 'waiter' || $rawRole === 'waiter') {
                $sessionRole = 'waiter';
              }
            } elseif ($currentAuthUser instanceof \App\Models\Guest) {
              $sessionRole = 'customer';
            }
          }

          // Use session role as priority, fallback to passed $role (for 404s/stateless pages)
          $sidebarUserRole = $sessionRole ?? ($role ?? null);
          $sidebarRole = null;
          $sidebarFile = null;
          
          // Only show sidebar if user has that role
          // This prevents showing wrong sidebars based on URL manipulation
          if ($sidebarUserRole === 'super_admin') {
            $sidebarRole = 'super_admin';
            $sidebarFile = 'dashboard.partials.sidebar-super-admin';
          } elseif ($sidebarUserRole === 'manager') {
            $sidebarRole = 'manager';
            $sidebarFile = 'dashboard.partials.sidebar-admin';
          } elseif ($sidebarUserRole === 'reception') {
            $sidebarRole = 'reception';
            $sidebarFile = 'dashboard.partials.sidebar-reception';
          } elseif ($sidebarUserRole === 'bar_keeper') {
            $sidebarRole = 'bar_keeper';
            $sidebarFile = 'dashboard.partials.sidebar-bar-keeper';
          } elseif ($sidebarUserRole === 'head_chef') {
            $sidebarRole = 'head_chef';
            $sidebarFile = 'dashboard.partials.sidebar-admin'; // Head chef uses admin sidebar with permissions
          } elseif ($sidebarUserRole === 'housekeeper') {
            $sidebarRole = 'housekeeper';
            $sidebarFile = 'dashboard.partials.sidebar-housekeeper';
          } elseif ($sidebarUserRole === 'waiter') {
            $sidebarRole = 'waiter';
            $sidebarFile = 'dashboard.partials.sidebar-waiter';
          } elseif ($sidebarUserRole === 'customer') {
            $sidebarRole = 'customer';
            $sidebarFile = 'dashboard.partials.sidebar-customer';
          } else {
            // Fallback: no sidebar if role cannot be determined
            $sidebarRole = null;
            $sidebarFile = null;
          }
        @endphp
        @php
          // Check if we're on a reports page - if so, show reports sidebar instead
          // Check for reports routes (manager/reports/*, day-services/reports, bar-keeper/reports, restaurant-reports)
          // Exclude housekeeper/reports and reception/reports as they have their own sidebars
          // ALSO EXCLUDE for Bar Keeper and Head Chef so they keep their specialized sidebars
          $isReportsPage = ((str_contains($activePage, 'manager/reports') || str_contains($activePage, 'admin/reports')) || 
                          str_contains($activePage, 'day-services/reports') ||
                          str_contains($activePage, 'bar-keeper/reports') ||
                          str_contains($activePage, 'restaurant-reports')) && 
                          in_array($sidebarUserRole, ['manager', 'super_admin']);
        @endphp
        
        @if($isReportsPage)
          {{-- Show Reports Sidebar --}}
          @include('dashboard.reports.partials.sidebar-reports', [
            'activePage' => $activePage,
            'currentUser' => $currentUser,
          ])
        @elseif($sidebarFile && view()->exists($sidebarFile))
          {{-- Show Regular Sidebar --}}
          @include($sidebarFile, [
            'activePage' => $activePage,
            'currentUser' => $currentUser,
            'sidebarBadges' => $sidebarBadges ?? []
          ])
        @elseif(!$sidebarFile)
          <li><a class="app-menu__item" href="#"><i class="app-menu__icon fa fa-exclamation-triangle"></i><span class="app-menu__label">No sidebar available for your role. Please contact administrator.</span></a></li>
        @else
          <li><a class="app-menu__item" href="#"><i class="app-menu__icon fa fa-exclamation-triangle"></i><span class="app-menu__label">Sidebar Error: {{ $sidebarFile }} not found</span></a></li>
        @endif
      </ul>
    </aside>
    <!-- Toast Notification Container -->
    <div id="toast-container"></div>
    
    <main class="app-content">
      @yield('content')
    </main>
    <!-- Essential javascripts for application to work-->
    <script src="{{ asset('dashboard_assets/js/jquery-3.2.1.min.js') }}"></script>
    <script src="{{ asset('dashboard_assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('dashboard_assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('dashboard_assets/js/main.js') }}"></script>
    <!-- SweetAlert2 - Standard Alert System -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
    /* Global SweetAlert2 Custom Styling - Brand Colors */
    .swal2-popup {
        border-radius: 10px !important;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }
    .swal2-title {
        font-weight: 600 !important;
        color: #333 !important;
    }
    .swal2-confirm {
        background-color: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
        font-weight: 600 !important;
        border-radius: 5px !important;
        padding: 10px 24px !important;
        transition: all 0.3s !important;
    }
    .swal2-confirm:hover {
        background-color: #7b0000 !important;
        border-color: #7b0000 !important;
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    .swal2-cancel {
        background-color: #6c757d !important;
        border-color: #6c757d !important;
        font-weight: 600 !important;
        border-radius: 5px !important;
        padding: 10px 24px !important;
    }
    .swal2-cancel:hover {
        background-color: #5a6268 !important;
        border-color: #5a6268 !important;
    }
    .swal2-select {
        border-radius: 5px !important;
        border: 1px solid #ddd !important;
        padding: 8px 12px !important;
    }
    .swal2-select:focus {
        border-color: #940000 !important;
        box-shadow: 0 0 0 0.2rem rgba(231, 122, 58, 0.25) !important;
    }
    </style>
    <script>
    // Global SweetAlert2 Configuration
    const SwalConfig = {
        confirmButtonColor: '#940000',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        allowOutsideClick: false,
        allowEscapeKey: true,
        customClass: {
            popup: 'swal2-popup-custom',
            confirmButton: 'swal2-confirm-custom',
            cancelButton: 'swal2-cancel-custom'
        }
    };
    
    // Helper function for success alerts
    function showSuccessAlert(title, text, callback) {
        Swal.fire(Object.assign({}, SwalConfig, {
            icon: 'success',
            title: title,
            text: text,
            showConfirmButton: true
        })).then(function(result) {
            if (callback) callback(result);
        });
    }
    
    // Helper function for error alerts
    function showErrorAlert(title, text, callback) {
        Swal.fire(Object.assign({}, SwalConfig, {
            icon: 'error',
            title: title,
            text: text,
            showConfirmButton: true
        })).then(function(result) {
            if (callback) callback(result);
        });
    }
    
    // Helper function for warning alerts
    function showWarningAlert(title, text, callback) {
        Swal.fire(Object.assign({}, SwalConfig, {
            icon: 'warning',
            title: title,
            text: text,
            showConfirmButton: true
        })).then(function(result) {
            if (callback) callback(result);
        });
    }
    
    // Helper function for confirmation dialogs
    function showConfirmDialog(title, text, confirmText, cancelText, callback) {
        Swal.fire(Object.assign({}, SwalConfig, {
            icon: 'question',
            title: title,
            text: text,
            showCancelButton: true,
            confirmButtonText: confirmText || 'Yes, proceed!',
            cancelButtonText: cancelText || 'Cancel'
        })).then(function(result) {
            if (callback) callback(result);
        });
    }
    </script>
    <!-- The javascript plugin to display page loading on top-->
    <script src="{{ asset('dashboard_assets/js/plugins/pace.min.js') }}"></script>
    
    <!-- SweetAlert2 - Auto Display Session Messages -->
    <script>
      // Auto-display session messages with SweetAlert2
      @if(session('success'))
        Swal.fire({
          icon: 'success',
          title: 'Success!',
          text: '{{ session('success') }}',
          confirmButtonColor: '#940000',
          confirmButtonText: 'OK',
          timer: 3000,
          timerProgressBar: true
        });
      @endif

      @if(session('error'))
        Swal.fire({
          icon: 'error',
          title: 'Error!',
          text: '{{ session('error') }}',
          confirmButtonColor: '#940000',
          confirmButtonText: 'OK'
        });
      @endif

      @if(session('warning'))
        Swal.fire({
          icon: 'warning',
          title: 'Warning!',
          text: '{{ session('warning') }}',
          confirmButtonColor: '#940000',
          confirmButtonText: 'OK'
        });
      @endif

      @if(session('info'))
        Swal.fire({
          icon: 'info',
          title: 'Information',
          text: '{{ session('info') }}',
          confirmButtonColor: '#940000',
          confirmButtonText: 'OK',
          timer: 3000,
          timerProgressBar: true
        });
      @endif

      @if(isset($errors) && $errors->any())
        Swal.fire({
          icon: 'error',
          title: 'Validation Error',
          html: '<ul style="text-align: left; margin: 10px 0;">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
          confirmButtonColor: '#940000',
          confirmButtonText: 'OK'
        });
      @endif

      // Global function to show success message
      function showSuccessMessage(message, title = 'Success!') {
        Swal.fire({
          icon: 'success',
          title: title,
          text: message,
          confirmButtonColor: '#940000',
          confirmButtonText: 'OK',
          timer: 3000,
          timerProgressBar: true
        });
      }

      // Global function to show error message
      function showErrorMessage(message, title = 'Error!') {
        Swal.fire({
          icon: 'error',
          title: title,
          text: message,
          confirmButtonColor: '#940000',
          confirmButtonText: 'OK'
        });
      }

      // Global function to show warning message
      function showWarningMessage(message, title = 'Warning!') {
        Swal.fire({
          icon: 'warning',
          title: title,
          text: message,
          confirmButtonColor: '#940000',
          confirmButtonText: 'OK'
        });
      }

      // Global function to show info message
      function showInfoMessage(message, title = 'Information') {
        Swal.fire({
          icon: 'info',
          title: title,
          text: message,
          confirmButtonColor: '#940000',
          confirmButtonText: 'OK',
          timer: 3000,
          timerProgressBar: true
        });
      }

      // Global function for confirmation dialogs
      function confirmAction(message, title = 'Confirm Action', confirmText = 'Yes, proceed!', cancelText = 'Cancel') {
        return Swal.fire({
          icon: 'question',
          title: title,
          text: message,
          showCancelButton: true,
          confirmButtonColor: '#940000',
          cancelButtonColor: '#6c757d',
          confirmButtonText: confirmText,
          cancelButtonText: cancelText
        });
      }
    </script>
    <script>
      function markNotificationAsRead(event, notificationId, url) {
        if (event) event.preventDefault();
        
        // If we have a URL, navigate to it immediately (optimistic navigation)
        // We trigger the validation in background
        const hasUrl = url && url !== 'javascript:;' && url !== '#';
        
        fetch(`/notifications/${notificationId}/read`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        }).catch(function(err) { console.error('Error marking read:', err); });
        
        if (hasUrl) {
            window.location.href = url;
            return false;
        }
        
        // DOM update logic (only relevant if we stay on page)
        // ... (preserving existing logic for single page app feel if no URL)

          // Update badge count
          const badge = document.querySelector('.notification-badge');
          if (badge) {
            const currentText = badge.textContent.trim();
            const count = parseInt(currentText.replace('+', '')) - 1;
            if (count > 0) {
              badge.textContent = count > 99 ? '99+' : count;
            } else {
              badge.remove();
            }
          }
          // Remove the notification from the dropdown
          const notificationItem = document.querySelector(`a[onclick*="${notificationId}"]`)?.closest('li');
          if (notificationItem) {
            notificationItem.remove();
          }
          // Update notification title
          updateNotificationTitle();
          // Dismiss toast if it exists
          dismissToast(notificationId);
      }
      
      function updateNotificationTitle() {
        const badge = document.querySelector('.notification-badge');
        const title = document.querySelector('.app-notification__title');
        if (title) {
          const count = badge ? parseInt(badge.textContent.trim().replace('+', '')) : 0;
          if (count > 0) {
            title.innerHTML = `You have ${count} new ${count === 1 ? 'notification' : 'notifications'}.`;
          } else {
            title.innerHTML = 'You have no new notifications.';
          }
        }
      }
      
      // Ensure notification badge is visible on page load
      document.addEventListener('DOMContentLoaded', function() {
        const badge = document.querySelector('.notification-badge');
        const unreadCount = {{ $unreadNotificationCount ?? 0 }};
        if (unreadCount > 0 && !badge) {
          // Badge should exist but doesn't - recreate it
          const notificationLink = document.querySelector('.app-nav__item[data-toggle="dropdown"]');
          if (notificationLink) {
            const newBadge = document.createElement('span');
            newBadge.className = 'badge badge-danger notification-badge';
            newBadge.style.cssText = 'position: absolute; top: -5px; right: -8px; font-size: 10px; font-weight: bold; padding: 2px 6px; border-radius: 10px; min-width: 18px; height: 18px; text-align: center; line-height: 14px; z-index: 1000; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.2);';
            newBadge.textContent = unreadCount > 99 ? '99+' : unreadCount;
            notificationLink.appendChild(newBadge);
          }
        }
      });

      function markAllNotificationsAsRead() {
        fetch('/notifications/mark-all-read', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        }).then(() => {
          location.reload();
        });
      }

      function incrementNotificationBadge(count = 1) {
        const badge = document.querySelector('.notification-badge');
        if (badge) {
          const currentCount = parseInt(badge.textContent.trim().replace('+', '')) || 0;
          const newCount = currentCount + count;
          badge.textContent = newCount > 99 ? '99+' : newCount;
          updateNotificationTitle();
        } else {
          const notificationLink = document.querySelector('.app-nav__item[data-toggle="dropdown"]');
          if (notificationLink) {
            const newBadge = document.createElement('span');
            newBadge.className = 'badge badge-danger notification-badge';
            newBadge.style.cssText = 'position: absolute; top: -5px; right: -8px; font-size: 10px; font-weight: bold; padding: 2px 6px; border-radius: 10px; min-width: 18px; height: 18px; text-align: center; line-height: 14px; z-index: 1000; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.2);';
            newBadge.textContent = count > 99 ? '99+' : count;
            notificationLink.appendChild(newBadge);
            updateNotificationTitle();
          }
        }
      }

      // Toast Notification System for Action Required Notifications
      // Load previously shown notification IDs from localStorage
      const storedShownIds = localStorage.getItem('shownToastNotificationIds');
      const shownToastIds = new Set(storedShownIds ? JSON.parse(storedShownIds) : []);
      
      // Function to save shown IDs to localStorage
      function saveShownToastIds() {
        localStorage.setItem('shownToastNotificationIds', JSON.stringify(Array.from(shownToastIds)));
      }
      
      // Notification queue system - show one at a time
      let notificationQueue = [];
      let currentNotificationTimer = null;
      let isShowingNotification = false;
      
      function showToastNotification(notification) {
        // Don't show if already shown or if notification is read
        if (shownToastIds.has(notification.id) || notification.is_read) {
          return;
        }
        
        // Only show notifications that require action (not informational ones)
        // The backend already filters to only actionable notifications, but we double-check here
        const userRole = '{{ $role ?? "" }}';
        let shouldShow = false;
        
        // Reception: see almost everything actionable
        if (userRole === 'reception') {
          shouldShow = notification.type === 'service_request' || 
                       notification.type === 'booking' || 
                       notification.type === 'extension_request' ||
                       notification.type === 'issue_report' ||
                       notification.type === 'maintenance';
        } 
        // Manager/Super Admin: see EVERYTHING actionable
        else if (userRole === 'manager' || userRole === 'super_admin') {
          shouldShow = true; // Managers get all alerts
        }
        // Bar Keeper: show relevant alerts
        else if (userRole === 'bar_keeper') {
          shouldShow = notification.type === 'booking' || 
                       notification.type === 'service_request' ||
                       notification.type === 'payment';
        }
        // Head Chef: show relevant alerts
        else if (userRole === 'head_chef') {
          shouldShow = notification.type === 'booking' || 
                       notification.type === 'service_request';
        }
        // Customer: show toast notifications for status updates
        else if (userRole === 'customer') {
          shouldShow = true;
        }
        
        if (!shouldShow) {
          return;
        }
        
        // Add to queue if not already there
        if (!notificationQueue.find(n => n.id === notification.id)) {
          notificationQueue.push(notification);
        }
        
        // Start showing if not already showing
        if (!isShowingNotification) {
          showNextNotification();
        }
      }
      
      function showNextNotification() {
        // Clear any existing timer
        if (currentNotificationTimer) {
          clearTimeout(currentNotificationTimer);
          currentNotificationTimer = null;
        }
        
        // Remove current notification if exists
        const container = document.getElementById('toast-container');
        container.innerHTML = '';
        
        // Check if there are more notifications
        if (notificationQueue.length === 0) {
          isShowingNotification = false;
          return;
        }
        
        isShowingNotification = true;
        const notification = notificationQueue.shift(); // Get and remove first notification
        
        // Don't show if already shown
        if (shownToastIds.has(notification.id)) {
          showNextNotification(); // Skip to next
          return;
        }
        
        shownToastIds.add(notification.id);
        saveShownToastIds(); // Save to localStorage
        
        const toast = document.createElement('div');
        toast.className = `toast-notification toast-${notification.color || 'info'}`;
        toast.id = `toast-${notification.id}`;
        
        const iconMap = {
          'primary': 'fa-info-circle',
          'success': 'fa-check-circle',
          'danger': 'fa-exclamation-circle',
          'warning': 'fa-exclamation-triangle',
          'info': 'fa-info-circle'
        };
        
        const icon = iconMap[notification.color] || 'fa-info-circle';
        const actionButton = notification.link ? `<a href="${notification.link}" class="btn btn-sm btn-primary">View</a>` : '';
        
        toast.innerHTML = `
          <i class="fa ${icon} toast-icon"></i>
          <div class="toast-content">
            <div class="toast-title">${notification.title || 'Notification'}</div>
            <div class="toast-message">${notification.message}</div>
            ${actionButton ? `<div class="toast-actions">${actionButton}</div>` : ''}
          </div>
          <button class="toast-close" onclick="dismissCurrentNotification()" title="Dismiss">&times;</button>
        `;
        
        container.appendChild(toast);
        
        // Mark as read when clicked (already marked above, but keep for link clicks)
        if (notification.link) {
          toast.querySelector('.toast-actions a').addEventListener('click', function() {
            markNotificationAsRead(notification.id);
          });
        }
        
        // Auto-advance to next notification after 5 seconds
        currentNotificationTimer = setTimeout(function() {
          dismissCurrentNotification();
        }, 5000);
      }
      
      function dismissCurrentNotification() {
        const container = document.getElementById('toast-container');
        const toast = container.querySelector('.toast-notification');
        
        if (toast) {
          toast.classList.add('removing');
          setTimeout(function() {
            toast.remove();
            // Show next notification
            showNextNotification();
          }, 300);
        } else {
          showNextNotification();
        }
      }
      
      function dismissToast(notificationId) {
        // Remove from queue if present
        notificationQueue = notificationQueue.filter(n => n.id !== notificationId);
        
        const toast = document.getElementById(`toast-${notificationId}`);
        if (toast) {
          dismissCurrentNotification();
        }
      }
      
      // Show actionable notifications after 2 seconds (one at a time, rotating every 5 seconds)
      // Only unread actionable notifications will be shown (already filtered by backend)
      @if(isset($notifications) && $notifications->count() > 0)
        @php
          // Backend already filters to only actionable notifications, so we just need unread ones
          $actionableNotifications = $notifications->filter(function($n) {
            return !$n->is_read;
          })->map(function($n) {
            return [
              'id' => $n->id,
              'type' => $n->type,
              'title' => $n->title,
              'message' => $n->message,
              'color' => $n->color,
              'link' => $n->link,
              'is_read' => $n->is_read,
            ];
          })->values();
        @endphp
        setTimeout(function() {
          const notifications = @json($actionableNotifications);
          
          // Add all unread actionable notifications to queue (they're already filtered by backend)
          notifications.forEach(function(notification) {
            if (!notification.is_read) {
              showToastNotification(notification);
            }
          });
        }, 2000);
      @endif
      
      // Poll for new actionable notifications every 30 seconds
      setInterval(function() {
        fetch('/notifications/actionable', {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
          }
        })
        .then(function(response) {
          const contentType = response.headers.get('content-type');
          if (contentType && contentType.includes('application/json')) {
            return response.json();
          } else {
            // If not JSON, return empty notifications
            return { success: false, notifications: [] };
          }
        })
        .then(function(data) {
          if (data.success && data.notifications && data.notifications.length > 0) {
            let newNotificationsCount = 0;
            data.notifications.forEach(function(notification) {
              if (!shownToastIds.has(notification.id) && !notification.is_read) {
                // Add to queue instead of showing immediately
                showToastNotification(notification);
                newNotificationsCount++;
              }
            });
            
            // Update badge count if there are new notifications
            if (newNotificationsCount > 0) {
              const badge = document.querySelector('.notification-badge');
              if (badge) {
                const currentCount = parseInt(badge.textContent.trim().replace('+', '')) || 0;
                const newCount = currentCount + newNotificationsCount;
                badge.textContent = newCount > 99 ? '99+' : newCount;
                updateNotificationTitle();
              } else {
                // Create badge if it doesn't exist
                const notificationLink = document.querySelector('.app-nav__item[data-toggle="dropdown"]');
                if (notificationLink) {
                  const newBadge = document.createElement('span');
                  newBadge.className = 'badge badge-danger notification-badge';
                  newBadge.style.cssText = 'position: absolute; top: -5px; right: -8px; font-size: 10px; font-weight: bold; padding: 2px 6px; border-radius: 10px; min-width: 18px; height: 18px; text-align: center; line-height: 14px; z-index: 1000; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.2);';
                  newBadge.textContent = newNotificationsCount > 99 ? '99+' : newNotificationsCount;
                  notificationLink.appendChild(newBadge);
                  updateNotificationTitle();
                }
              }
            }
          }
        })
        .catch(function(error) {
          // Silently fail - don't spam console with notification errors
          if (error.message && !error.message.includes('JSON')) {
            console.error('Error fetching actionable notifications:', error);
          }
        });
      }, 30000);
    </script>
    @yield('scripts')
  
  <script>
    (function() {
      // Hide loading container when page is fully loaded
      function hideLoadingContainer() {
        try {
          var loadingContainer = document.getElementById('loadingContainer');
          var body = document.body;
          
          if (!loadingContainer) {
            return;
          }
          
          if (body) {
            body.classList.add('page-loaded');
          }
          
          // Add hidden class first (uses !important)
          loadingContainer.classList.add('hidden');
          
          // Also set styles directly to ensure it hides
          loadingContainer.style.display = 'none';
          loadingContainer.style.visibility = 'hidden';
          loadingContainer.style.opacity = '0';
          loadingContainer.style.pointerEvents = 'none';
        } catch(e) {
          console.error('Error hiding loading container:', e);
        }
      }
      
      // Try to hide immediately if DOM is ready
      if (document.readyState === 'complete' || document.readyState === 'interactive') {
        hideLoadingContainer();
      } else {
        // Wait for DOM to be ready
        if (document.addEventListener) {
          document.addEventListener('DOMContentLoaded', hideLoadingContainer);
        } else if (document.attachEvent) {
          document.attachEvent('onreadystatechange', function() {
            if (document.readyState === 'complete') {
              hideLoadingContainer();
            }
          });
        }
        
        // Also wait for window load
        if (window.addEventListener) {
          window.addEventListener('load', hideLoadingContainer);
        } else if (window.attachEvent) {
          window.attachEvent('onload', hideLoadingContainer);
        }
      }
      
      // Ultimate fallback: Force hide after 500ms max
      setTimeout(hideLoadingContainer, 500);
    })();
  </script>
  </body>
</html>

