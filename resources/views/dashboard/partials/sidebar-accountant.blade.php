{{-- Accountant Sidebar Menu --}}
@php
    $activePage = request()->path();
@endphp

{{-- 1. DASHBOARD --}}
<li>
    <a class="app-menu__item {{ str_contains($activePage, 'accountant/dashboard') ? 'active' : '' }}"
        href="{{ route('accountant.dashboard') }}">
        <i class="app-menu__icon fa fa-dashboard"></i>
        <span class="app-menu__label">Dashboard</span>
    </a>
</li>

{{-- 2. PURCHASE APPROVAL --}}
<li class="treeview-item-header"
    style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">
    Purchase Control</li>

<li class="treeview {{ str_contains($activePage, 'accountant/shopping-lists') ? 'is-expanded' : '' }}">
    <a class="app-menu__item" href="#" data-toggle="treeview">
        <i class="app-menu__icon fa fa-list-alt"></i>
        <span class="app-menu__label">Shopping Lists</span>
        <i class="treeview-indicator fa fa-angle-right"></i>
    </a>
    <ul class="treeview-menu">
        <li><a class="treeview-item {{ str_contains($activePage, 'shopping-lists') && !str_contains($activePage, 'tab=approved') && !str_contains($activePage, 'tab=purchased') ? 'active' : '' }}"
                href="{{ route('accountant.shopping-lists') }}">
                <i class="icon fa fa-clock-o"></i> Pending Approval
            </a></li>
        <li><a class="treeview-item" href="{{ route('accountant.shopping-lists', ['tab' => 'approved']) }}">
                <i class="icon fa fa-check-circle"></i> Approved Lists
            </a></li>
        <li><a class="treeview-item" href="{{ route('accountant.shopping-lists', ['tab' => 'purchased']) }}">
                <i class="icon fa fa-shopping-bag"></i> Purchased Lists
            </a></li>
    </ul>
</li>

{{-- 3. BEVERAGE REQUESTS --}}
<li class="treeview-item-header"
    style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">
    Beverage Control</li>

<li>
    <a class="app-menu__item {{ request('type') === 'drink' ? 'active' : '' }}"
        href="{{ route('stock-requests.index', ['type' => 'drink']) }}">
        <i class="app-menu__icon fa fa-glass"></i>
        <span class="app-menu__label">Beverage Requests</span>
        <span class="badge badge-warning ml-auto stock-req-badge" id="badge-drink" style="display:none;">0</span>
    </a>
</li>
<li>
    <a class="app-menu__item {{ request('type') === 'food' ? 'active' : '' }}"
        href="{{ route('stock-requests.index', ['type' => 'food']) }}">
        <i class="app-menu__icon fa fa-cutlery"></i>
        <span class="app-menu__label">Kitchen Requests</span>
        <span class="badge badge-warning ml-auto stock-req-badge" id="badge-food" style="display:none;">0</span>
    </a>
</li>
<li>
    <a class="app-menu__item {{ request('type') === 'housekeeping' ? 'active' : '' }}"
        href="{{ route('stock-requests.index', ['type' => 'housekeeping']) }}">
        <i class="app-menu__icon fa fa-home"></i>
        <span class="app-menu__label">Housekeeping Requests</span>
        <span class="badge badge-warning ml-auto stock-req-badge" id="badge-housekeeping" style="display:none;">0</span>
    </a>
</li>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        function fetchAccountantStockCounts() {
            fetch('{{ route("stock-requests.pending-counts") }}')
                .then(response => response.json())
                .then(data => {
                    const categories = ['drink', 'food', 'housekeeping'];
                    categories.forEach(cat => {
                        const badge = document.getElementById('badge-' + cat);
                        if (badge) {
                            if (data[cat] && data[cat] > 0) {
                                badge.textContent = data[cat];
                                badge.style.display = 'inline-block';
                            } else {
                                badge.style.display = 'none';
                            }
                        }
                    });
                })
                .catch(err => console.error('Error fetching stock counts', err));
        }

        // Fetch immediately, then every 30 seconds
        fetchAccountantStockCounts();
        setInterval(fetchAccountantStockCounts, 30000);
    });
</script>

{{-- 4. PAYMENT VERIFICATION (was 3) --}}
<li class="treeview-item-header"
    style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">
    Payment Verification</li>

<li class="treeview {{ str_contains($activePage, 'accountant/payments') ? 'is-expanded' : '' }}">
    <a class="app-menu__item" href="#" data-toggle="treeview">
        <i class="app-menu__icon fa fa-money"></i>
        <span class="app-menu__label">Payments</span>
        <i class="treeview-indicator fa fa-angle-right"></i>
    </a>
    <ul class="treeview-menu">
        <li><a class="treeview-item {{ str_contains($activePage, 'payments') && !str_contains($activePage, 'tab=verified') ? 'active' : '' }}"
                href="{{ route('accountant.payments') }}">
                <i class="icon fa fa-hourglass-half"></i> Shopping List Payments
            </a></li>
        <li><a class="treeview-item" href="{{ route('accountant.payments', ['tab' => 'verified']) }}">
                <i class="icon fa fa-check-square"></i> Verified Shopping Lists
            </a></li>
        <li><a class="treeview-item {{ str_contains($activePage, 'cashier-collections') ? 'active' : '' }}"
                href="{{ route('accountant.cashier-collections') }}">
                <i class="icon fa fa-bank"></i> Cashier Collections
            </a></li>
    </ul>
</li>

<li>
    <a class="app-menu__item {{ str_contains($activePage, 'supplier-orders') ? 'active' : '' }}"
        href="{{ route('supplier-orders.index') }}">
        <i class="app-menu__icon fa fa-truck"></i>
        <span class="app-menu__label">Weekly Supplier Orders</span>
    </a>
</li>

<li>
    <a class="app-menu__item {{ str_contains($activePage, 'lpo') ? 'active' : '' }}" href="{{ route('lpo.index') }}">
        <i class="app-menu__icon fa fa-file-text"></i>
        <span class="app-menu__label">Local Purchase Orders (LPO)</span>
    </a>
</li>

<li>
    <a class="app-menu__item {{ str_contains($activePage, 'restaurants/suppliers') ? 'active' : '' }}"
        href="{{ route('admin.suppliers.index') }}">
        <i class="app-menu__icon fa fa-address-book"></i>
        <span class="app-menu__label">Manage Suppliers</span>
    </a>
</li>

{{-- 4. REPORTS --}}
<li class="treeview-item-header"
    style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">
    Financial Reports</li>

<li>
    <a class="app-menu__item {{ str_contains($activePage, 'accountant/reports') ? 'active' : '' }}"
        href="{{ route('accountant.reports') }}">
        <i class="app-menu__icon fa fa-bar-chart"></i>
        <span class="app-menu__label">Purchase Reports</span>
    </a>
</li>

<li>
    <a class="app-menu__item {{ str_contains($activePage, 'reception/reports/financial') ? 'active' : '' }}"
        href="{{ route('reception.reports.financial') }}">
        <i class="app-menu__icon fa fa-money-bill"></i>
        <span class="app-menu__label">Reception Revenue Report</span>
    </a>
</li>

{{-- 5. ACCOUNT --}}
<li class="treeview-item-header"
    style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">
    Account</li>
<li><a class="app-menu__item {{ str_contains($activePage, 'profile') ? 'active' : '' }}"
        href="{{ route('accountant.profile') }}">
        <i class="app-menu__icon fa fa-user"></i><span class="app-menu__label">My Profile</span>
    </a></li>
<li>
    <a class="app-menu__item" href="{{ route('accountant.logout') }}"
        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="app-menu__icon fa fa-sign-out"></i><span class="app-menu__label">Logout</span>
    </a>
</li>