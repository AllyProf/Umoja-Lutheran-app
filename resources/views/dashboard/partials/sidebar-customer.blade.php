{{-- Customer/Guest Sidebar Menu --}}
@php
  use App\Services\RolePermissionService;
  
  $currentRoute = request()->route() ? request()->route()->getName() : '';
  $activePage = request()->path();
  $badges = $sidebarBadges ?? [
    'notifications' => 0,
    'extensions' => 0,
    'payments' => 0,
    'issues' => 0,
    'service_requests' => 0,
  ];
  
  // This sidebar only appears for users with 'customer' role (guests) - checked in app.blade.php
  // All menu items are visible to customers by default
@endphp

{{-- ============================================ --}}
{{-- DASHBOARD --}}
{{-- ============================================ --}}
<li><a class="app-menu__item {{ $currentRoute === 'customer.dashboard' || str_contains($activePage, 'customer/dashboard') ? 'active' : '' }}" href="{{ route('customer.dashboard') }}"><i class="app-menu__icon fa fa-dashboard"></i><span class="app-menu__label">Dashboard</span></a></li>

{{-- ============================================ --}}
{{-- BOOKINGS --}}
{{-- ============================================ --}}
<li class="treeview-item-header" style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">Bookings</li>

<li><a class="app-menu__item" href="#"><i class="app-menu__icon fa fa-plus-circle"></i><span class="app-menu__label">Book a Room</span></a></li>

@php
  // Get the most recent active booking for identity card link
  $user = auth()->guard('staff')->user() ?? auth()->guard('guest')->user();
  $latestBooking = null;
  if ($user) {
    $latestBooking = \App\Models\Booking::where('guest_email', $user->email)
      ->where('status', 'confirmed')
      ->orderBy('check_in', 'desc')
      ->first();
  }
@endphp

<li class="treeview {{ str_contains($activePage, 'customer/my-bookings') || str_contains($activePage, 'customer/booking-history') || str_contains($activePage, 'customer/extensions') || str_contains($activePage, 'customer/calendar') || str_contains($activePage, 'customer/room-information') || str_contains($activePage, 'customer/bookings') && str_contains($activePage, 'identity-card') ? 'is-expanded' : '' }}">
  <a class="app-menu__item" href="#" data-toggle="treeview">
    <i class="app-menu__icon fa fa-calendar"></i>
    <span class="app-menu__label">My Bookings @if(($badges['extensions'] + $badges['service_requests']) > 0)<span class="badge badge-warning badge-pill ml-2">{{ $badges['extensions'] + $badges['service_requests'] }}</span>@endif</span>
    <i class="treeview-indicator fa fa-angle-right"></i>
  </a>
  <ul class="treeview-menu">
    <li><a class="treeview-item {{ $currentRoute === 'customer.my-bookings' || str_contains($activePage, 'customer/my-bookings') ? 'active' : '' }}" href="{{ route('customer.my-bookings') }}"><i class="icon fa fa-list"></i> My Bookings</a></li>
    <li><a class="treeview-item {{ $currentRoute === 'customer.booking-history' || str_contains($activePage, 'customer/booking-history') ? 'active' : '' }}" href="{{ route('customer.booking-history') }}"><i class="icon fa fa-history"></i> Booking History</a></li>
    <li><a class="treeview-item {{ $currentRoute === 'customer.extensions' || str_contains($activePage, 'customer/extensions') ? 'active' : '' }}" href="{{ route('customer.extensions') }}"><i class="icon fa fa-clock-o"></i> Extension Requests @if($badges['extensions'] > 0)<span class="badge badge-warning badge-pill ml-2">{{ $badges['extensions'] }}</span>@endif</a></li>
    <li><a class="treeview-item {{ $currentRoute === 'customer.calendar' || str_contains($activePage, 'customer/calendar') ? 'active' : '' }}" href="{{ route('customer.calendar') }}"><i class="icon fa fa-calendar"></i> Calendar View</a></li>
    <li><a class="treeview-item {{ $currentRoute === 'customer.room-information' || str_contains($activePage, 'customer/room-information') ? 'active' : '' }}" href="{{ route('customer.room-information') }}"><i class="icon fa fa-bed"></i> Room Information</a></li>
    @if($latestBooking)
    <li><a class="treeview-item {{ str_contains($activePage, 'customer/bookings') && str_contains($activePage, 'identity-card') ? 'active' : '' }}" href="{{ route('customer.bookings.identity-card', $latestBooking) }}"><i class="icon fa fa-id-card"></i> Identity Card</a></li>
    @endif
  </ul>
</li>

{{-- ============================================ --}}
{{-- PAYMENTS & SERVICES --}}
{{-- ============================================ --}}
<li class="treeview-item-header" style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">Payments & Services</li>

<li><a class="app-menu__item {{ $currentRoute === 'customer.payments' || str_contains($activePage, 'customer/payments') ? 'active' : '' }}" href="{{ route('customer.payments') }}"><i class="app-menu__icon fa fa-credit-card"></i><span class="app-menu__label">My Payments @if($badges['payments'] > 0)<span class="badge badge-danger badge-pill ml-2">{{ $badges['payments'] }}</span>@endif</span></a></li>

<li><a class="app-menu__item {{ $currentRoute === 'customer.issues.index' || str_contains($activePage, 'customer/issues') ? 'active' : '' }}" href="{{ route('customer.issues.index') }}"><i class="app-menu__icon fa fa-exclamation-triangle"></i><span class="app-menu__label">My Issues @if($badges['issues'] > 0)<span class="badge badge-warning badge-pill ml-2">{{ $badges['issues'] }}</span>@endif</span></a></li>

{{-- ============================================ --}}
{{-- INFORMATION --}}
{{-- ============================================ --}}
<li class="treeview-item-header" style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">Information</li>

<li class="treeview {{ str_contains($activePage, 'customer/notifications') || str_contains($activePage, 'customer/local-info') || str_contains($activePage, 'exchange-rates') ? 'is-expanded' : '' }}">
  <a class="app-menu__item" href="#" data-toggle="treeview">
    <i class="app-menu__icon fa fa-info-circle"></i>
    <span class="app-menu__label">Information & Tools @if($badges['notifications'] > 0)<span class="badge badge-info badge-pill ml-2">{{ $badges['notifications'] }}</span>@endif</span>
    <i class="treeview-indicator fa fa-angle-right"></i>
  </a>
  <ul class="treeview-menu">
    <li><a class="treeview-item {{ $currentRoute === 'customer.notifications' || str_contains($activePage, 'customer/notifications') ? 'active' : '' }}" href="{{ route('customer.notifications') }}"><i class="icon fa fa-bell"></i> Notifications @if($badges['notifications'] > 0)<span class="badge badge-info badge-pill ml-2">{{ $badges['notifications'] }}</span>@endif</a></li>
    <li><a class="treeview-item {{ $currentRoute === 'customer.local-info' || str_contains($activePage, 'customer/local-info') ? 'active' : '' }}" href="{{ route('customer.local-info') }}"><i class="icon fa fa-map-marker"></i> Local Information</a></li>
    <li><a class="treeview-item {{ $currentRoute === 'exchange-rates' || str_contains($activePage, 'exchange-rates') ? 'active' : '' }}" href="{{ route('exchange-rates') }}"><i class="icon fa fa-exchange"></i> Exchange Rates</a></li>
  </ul>
</li>

{{-- ============================================ --}}
{{-- ACCOUNT & SUPPORT --}}
{{-- ============================================ --}}
<li class="treeview-item-header" style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">Account & Support</li>

<li class="treeview {{ str_contains($activePage, 'customer/profile') || str_contains($activePage, 'customer/feedback') ? 'is-expanded' : '' }}">
  <a class="app-menu__item" href="#" data-toggle="treeview">
    <i class="app-menu__icon fa fa-user"></i>
    <span class="app-menu__label">My Account</span>
    <i class="treeview-indicator fa fa-angle-right"></i>
  </a>
  <ul class="treeview-menu">
    <li><a class="treeview-item {{ $currentRoute === 'customer.profile' || str_contains($activePage, 'customer/profile') ? 'active' : '' }}" href="{{ route('customer.profile') }}"><i class="icon fa fa-user"></i> Profile</a></li>
    <li><a class="treeview-item {{ $currentRoute === 'customer.feedback' || str_contains($activePage, 'customer/feedback') ? 'active' : '' }}" href="{{ route('customer.feedback') }}"><i class="icon fa fa-star"></i> Feedback & Reviews</a></li>
  </ul>
</li>

<li><a class="app-menu__item {{ $currentRoute === 'customer.support' || str_contains($activePage, 'customer/support') ? 'active' : '' }}" href="{{ route('customer.support') }}"><i class="app-menu__icon fa fa-headphones"></i><span class="app-menu__label">Support</span></a></li>
