<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar">
    <div class="app-sidebar__user">
        <img class="app-sidebar__user-avatar" src="{{ Auth::guard('staff')->user()->profile_photo ? asset('storage/' . Auth::guard('staff')->user()->profile_photo) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::guard('staff')->user()->name) . '&background=009688&color=fff' }}" alt="User Image" style="width: 48px; height: 48px; object-fit: cover; border-radius: 50%;">
        <div>
            <p class="app-sidebar__user-name">{{ Auth::guard('staff')->user()->name }}</p>
            <p class="app-sidebar__user-designation">{{ ucfirst(Auth::guard('staff')->user()->role ?? 'Staff') }}</p>
        </div>
    </div>
    <ul class="app-menu">
        <li>
            <a class="app-menu__item {{ Route::currentRouteName() == 'waiter.dashboard' ? 'active' : '' }}" href="{{ route('waiter.dashboard') }}">
                <i class="app-menu__icon fa fa-cutlery"></i>
                <span class="app-menu__label">Waiter POS</span>
            </a>
        </li>
        <li>
            <a class="app-menu__item {{ Route::currentRouteName() == 'waiter.sales-summary' ? 'active' : '' }}" href="{{ route('waiter.sales-summary') }}">
                <i class="app-menu__icon fa fa-line-chart"></i>
                <span class="app-menu__label">Sales Summary</span>
            </a>
        </li>
        <li>
            <a class="app-menu__item {{ Route::currentRouteName() == 'waiter.orders' ? 'active' : '' }}" href="{{ route('waiter.orders') }}">
                <i class="app-menu__icon fa fa-history"></i>
                <span class="app-menu__label">Order History</span>
            </a>
        </li>
        <li class="treeview {{ str_contains(Route::currentRouteName(), 'waiter.profile') ? 'is-expanded' : '' }}">
            <a class="app-menu__item" href="#" data-toggle="treeview">
                <i class="app-menu__icon fa fa-user"></i>
                <span class="app-menu__label">My Profile</span>
                <i class="treeview-indicator fa fa-angle-right"></i>
            </a>
            <ul class="treeview-menu">
                <li>
                    <a class="treeview-item {{ Route::currentRouteName() == 'waiter.profile' ? 'active' : '' }}" href="{{ route('waiter.profile') }}">
                        <i class="icon fa fa-circle-o"></i> View Profile
                    </a>
                </li>
            </ul>
        </li>
        <li>
            <a class="app-menu__item" href="javascript:void(0)" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="app-menu__icon fa fa-sign-out"></i>
                <span class="app-menu__label">Logout</span>
            </a>
            <form id="logout-form" action="{{ route('waiter.logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </li>
    </ul>
</aside>
