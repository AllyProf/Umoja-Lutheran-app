{{-- Accountant Sidebar Menu (simplified) --}}
@php
    $activePage = request()->path();
    $pendingLpoCount = \App\Models\LocalPurchaseOrder::where('status', 'sent_to_accountant')->count();
    $pendingShoppingLists = \App\Models\ShoppingList::where('status', 'pending')->count();
    $pendingEmergency = \App\Models\PurchaseRequest::where('is_emergency', true)
        ->whereIn('status', ['pending', 'approved', 'on_list'])
        ->count();
    $toPayCount = \App\Models\ShoppingList::where('status', 'approved')->count()
        + \App\Models\LocalPurchaseOrder::where('status', 'verified_by_manager')->count();
    $storekeeperRequestCount = $pendingLpoCount + $pendingShoppingLists + $pendingEmergency;
@endphp

{{-- 1. DASHBOARD --}}
<li>
    <a class="app-menu__item {{ str_contains($activePage, 'accountant/dashboard') ? 'active' : '' }}"
        href="{{ route('accountant.dashboard') }}">
        <i class="app-menu__icon fa fa-dashboard"></i>
        <span class="app-menu__label">Dashboard</span>
    </a>
</li>

{{-- 2. STOREKEEPER REQUESTS (LPO + Purchase List + Emergency) --}}
<li class="treeview-item-header"
    style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">
    Storekeeper Requests</li>

<li class="treeview {{ str_contains($activePage, 'lpo') || str_contains($activePage, 'shopping-lists') || str_contains($activePage, 'emergency-orders') || str_contains($activePage, 'purchase-requests') ? 'is-expanded' : '' }}">
    <a class="app-menu__item" href="#" data-toggle="treeview">
        <i class="app-menu__icon fa fa-clipboard"></i>
        <span class="app-menu__label">Storekeeper Requests</span>
        @if($storekeeperRequestCount > 0)
            <span class="badge badge-danger badge-pill ml-auto" id="badge-storekeeper-requests">
                {{ $storekeeperRequestCount > 99 ? '99+' : $storekeeperRequestCount }}
            </span>
        @else
            <span class="badge badge-danger badge-pill ml-auto" id="badge-storekeeper-requests" style="display:none;">0</span>
        @endif
        <i class="treeview-indicator fa fa-angle-right"></i>
    </a>
    <ul class="treeview-menu">
        <li>
            <a class="treeview-item {{ str_contains($activePage, 'lpo') ? 'active' : '' }}"
                href="{{ route('lpo.index', ['tab' => 'review']) }}">
                <i class="icon fa fa-file-text"></i> LPO Budgets
                <span class="badge badge-danger badge-pill ml-1" id="badge-lpo-pending"
                    style="{{ $pendingLpoCount > 0 ? '' : 'display:none;' }}">
                    {{ $pendingLpoCount > 99 ? '99+' : $pendingLpoCount }}
                </span>
            </a>
        </li>
        <li>
            <a class="treeview-item {{ str_contains($activePage, 'shopping-lists') && !request('tab') ? 'active' : '' }}{{ request('tab') === 'pending' ? 'active' : '' }}"
                href="{{ route('accountant.shopping-lists', ['tab' => 'pending']) }}">
                <i class="icon fa fa-list-alt"></i> Purchase Lists
                @if($pendingShoppingLists > 0)
                    <span class="badge badge-danger badge-pill ml-1" id="badge-purchase-lists">{{ $pendingShoppingLists }}</span>
                @else
                    <span class="badge badge-danger badge-pill ml-1" id="badge-purchase-lists" style="display:none;">0</span>
                @endif
            </a>
        </li>
        <li>
            <a class="treeview-item {{ str_contains($activePage, 'emergency-orders') ? 'active' : '' }}"
                href="{{ route('accountant.emergency-orders') }}">
                <i class="icon fa fa-ambulance"></i> Emergency Orders
                @if($pendingEmergency > 0)
                    <span class="badge badge-danger badge-pill ml-1" id="badge-emergency">{{ $pendingEmergency }}</span>
                @else
                    <span class="badge badge-danger badge-pill ml-1" id="badge-emergency" style="display:none;">0</span>
                @endif
            </a>
        </li>
    </ul>
</li>

{{-- 3. PAY STOREKEEPER (after manager approval) --}}
<li class="treeview-item-header"
    style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">
    Payments</li>

<li>
    <a class="app-menu__item {{ str_contains($activePage, 'pay-storekeeper') ? 'active' : '' }}"
        href="{{ route('accountant.pay-storekeeper') }}">
        <i class="app-menu__icon fa fa-money"></i>
        <span class="app-menu__label">Pay Storekeeper</span>
        @if($toPayCount > 0)
            <span class="badge badge-info badge-pill ml-auto" id="badge-pay-storekeeper">{{ $toPayCount > 99 ? '99+' : $toPayCount }}</span>
        @else
            <span class="badge badge-info badge-pill ml-auto" id="badge-pay-storekeeper" style="display:none;">0</span>
        @endif
    </a>
</li>

<li>
    <a class="app-menu__item {{ str_contains($activePage, 'accountant/payments') ? 'active' : '' }}"
        href="{{ route('accountant.payments') }}">
        <i class="app-menu__icon fa fa-check-square"></i>
        <span class="app-menu__label">Verify Purchases</span>
    </a>
</li>

{{-- 4. CASHIER REVENUE --}}
<li class="treeview-item-header"
    style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">
    Cashier Revenue</li>

<li class="treeview {{ str_contains($activePage, 'cashier-collections') ? 'is-expanded' : '' }}">
    <a class="app-menu__item" href="#" data-toggle="treeview">
        <i class="app-menu__icon fa fa-bank"></i>
        <span class="app-menu__label">Receive Revenue</span>
        <i class="treeview-indicator fa fa-angle-right"></i>
    </a>
    <ul class="treeview-menu">
        <li>
            <a class="treeview-item {{ str_contains($activePage, 'cashier-collections') && request('category', 'restaurant') === 'restaurant' ? 'active' : '' }}"
                href="{{ route('accountant.cashier-collections', ['category' => 'restaurant']) }}">
                <i class="icon fa fa-cutlery"></i> Restaurant
            </a>
        </li>
        <li>
            <a class="treeview-item {{ str_contains($activePage, 'cashier-collections') && request('category') === 'reception' ? 'active' : '' }}"
                href="{{ route('accountant.cashier-collections', ['category' => 'reception']) }}">
                <i class="icon fa fa-bed"></i> Reception
            </a>
        </li>
    </ul>
</li>

{{-- 5. REPORTS --}}
<li class="treeview-item-header"
    style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">
    Reports</li>

<li>
    <a class="app-menu__item {{ str_contains($activePage, 'accountant/reports') ? 'active' : '' }}"
        href="{{ route('accountant.reports') }}">
        <i class="app-menu__icon fa fa-bar-chart"></i>
        <span class="app-menu__label">Purchase Reports</span>
    </a>
</li>

{{-- 6. ACCOUNT --}}
<li class="treeview-item-header"
    style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">
    Account</li>
<li>
    <a class="app-menu__item {{ str_contains($activePage, 'profile') ? 'active' : '' }}"
        href="{{ route('accountant.profile') }}">
        <i class="app-menu__icon fa fa-user"></i>
        <span class="app-menu__label">My Profile</span>
    </a>
</li>
<li>
    <a class="app-menu__item" href="{{ route('accountant.logout') }}"
        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="app-menu__icon fa fa-sign-out"></i>
        <span class="app-menu__label">Logout</span>
    </a>
</li>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        function setBadge(id, count) {
            const badge = document.getElementById(id);
            if (!badge) return;
            const n = parseInt(count || 0, 10);
            if (n > 0) {
                badge.textContent = n > 99 ? '99+' : n;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        }

        function refreshAccountantBadges() {
            fetch(@json(route('lpo.pending-count')))
                .then(r => r.json())
                .then(data => setBadge('badge-lpo-pending', data.count))
                .catch(() => {});

            fetch(@json(route('accountant.pending-counts')))
                .then(r => r.json())
                .then(data => {
                    setBadge('badge-purchase-lists', data.purchase_lists);
                    setBadge('badge-emergency', data.emergency);
                    setBadge('badge-pay-storekeeper', data.to_pay);
                    setBadge('badge-storekeeper-requests',
                        (parseInt(data.lpo || 0, 10)
                            + parseInt(data.purchase_lists || 0, 10)
                            + parseInt(data.emergency || 0, 10)));
                })
                .catch(() => {});
        }

        refreshAccountantBadges();
        setInterval(refreshAccountantBadges, 30000);
    });
</script>
