@extends('dashboard.layouts.app')

@section('content')
@php
    $role = strtolower($role ?? auth()->guard('staff')->user()->role ?? '');
    $tab = $tab ?? request('tab', 'all');
    $pendingReviewCount = $pendingReviewCount ?? 0;
@endphp

<div class="app-title">
    <div>
        <h1><i class="fa fa-file-text"></i> Local Purchase Orders (LPO)</h1>
        <p>Manage and track bi-weekly stock budgets</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="#">LPO Budgets</a></li>
    </ul>
</div>

@if(in_array($role, ['accountant', 'manager', 'super_admin']) && $pendingReviewCount > 0)
    <div class="alert alert-warning shadow-sm d-flex align-items-center justify-content-between flex-wrap">
        <div>
            <strong><i class="fa fa-bell"></i> {{ $pendingReviewCount }} LPO budget{{ $pendingReviewCount === 1 ? '' : 's' }} waiting for your review</strong>
            <div class="small mb-0">
                @if($role === 'accountant')
                    Accept, adjust, or reject and return to the storekeeper.
                @else
                    Verify and approve budgets accepted by the accountant.
                @endif
            </div>
        </div>
        <a href="{{ route('lpo.index', ['tab' => 'review']) }}" class="btn btn-sm btn-danger mt-2 mt-md-0">
            Review now
        </a>
    </div>
@endif

<div class="row">
    <div class="col-md-12">
        <div class="tile shadow-sm">
            <div class="tile-title-w-btn">
                <h3 class="title">LPO History</h3>
                <p>
                    @if(in_array($role, ['storekeeper', 'super_admin']))
                        <a href="{{ route('lpo.create') }}" class="btn btn-primary icon-btn">
                            <i class="fa fa-plus"></i> New LPO Budget
                        </a>
                    @endif
                </p>
            </div>

            @if($role === 'accountant')
                <ul class="nav nav-pills mb-3">
                    <li class="nav-item">
                        <a class="nav-link {{ $tab === 'review' ? 'active' : '' }}"
                            href="{{ route('lpo.index', ['tab' => 'review']) }}">
                            Awaiting review
                            @if($pendingReviewCount > 0)
                                <span class="badge badge-danger">{{ $pendingReviewCount }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $tab === 'forwarded' ? 'active' : '' }}"
                            href="{{ route('lpo.index', ['tab' => 'forwarded']) }}">
                            Accepted / forwarded
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $tab === 'all' ? 'active' : '' }}"
                            href="{{ route('lpo.index', ['tab' => 'all']) }}">
                            All
                        </a>
                    </li>
                </ul>
            @elseif(in_array($role, ['manager', 'super_admin']))
                <ul class="nav nav-pills mb-3">
                    <li class="nav-item">
                        <a class="nav-link {{ $tab === 'review' ? 'active' : '' }}"
                            href="{{ route('lpo.index', ['tab' => 'review']) }}">
                            Awaiting verification
                            @if($pendingReviewCount > 0)
                                <span class="badge badge-danger">{{ $pendingReviewCount }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $tab === 'verified' ? 'active' : '' }}"
                            href="{{ route('lpo.index', ['tab' => 'verified']) }}">
                            Verified / closed
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $tab === 'all' ? 'active' : '' }}"
                            href="{{ route('lpo.index', ['tab' => 'all']) }}">
                            All
                        </a>
                    </li>
                </ul>
            @endif

            <div class="tile-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="lpoTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Period</th>
                                <th class="text-right">Budget Amount</th>
                                <th class="text-right">Spent Amount</th>
                                <th class="text-right">Remaining</th>
                                <th class="text-center">Status</th>
                                <th>Created By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr class="{{ in_array($order->status, ['sent_to_accountant', 'sent_to_manager', 'verified_by_manager']) && in_array($role, ['accountant', 'manager', 'super_admin']) ? 'table-warning' : '' }}">
                                    <td>
                                        <strong>#{{ $order->id }}</strong>
                                        @if($order->status === 'sent_to_accountant' && $role === 'accountant')
                                            <div><span class="badge badge-danger">Needs review</span></div>
                                        @elseif($order->status === 'sent_to_manager' && in_array($role, ['manager', 'super_admin']))
                                            <div><span class="badge badge-danger">Needs verification</span></div>
                                        @elseif($order->status === 'verified_by_manager' && in_array($role, ['accountant', 'super_admin']))
                                            <div><span class="badge badge-danger">Pay storekeeper</span></div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="small">{{ $order->start_date->format('M d, Y') }} to {{ $order->end_date->format('M d, Y') }}</span>
                                    </td>
                                    <td class="text-right font-weight-bold">{{ number_format($order->total_amount, 2) }}</td>
                                    <td class="text-right text-warning">{{ number_format($order->total_spent_amount, 2) }}</td>
                                    <td class="text-right text-success">{{ number_format($order->remaining_balance, 2) }}</td>
                                    <td class="text-center">
                                        @php
                                            $badgeClass = match ($order->status) {
                                                'pending' => 'badge-warning',
                                                'rejected' => 'badge-danger',
                                                'sent_to_accountant' => 'badge-info',
                                                'sent_to_manager' => 'badge-primary',
                                                'verified_by_manager' => 'badge-warning',
                                                'funds_disbursed' => 'badge-success',
                                                'closed' => 'badge-dark',
                                                'cancelled' => 'badge-danger',
                                                default => 'badge-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                                        @if($order->status === 'rejected' && $order->rejection_reason)
                                            <div class="small text-danger mt-1">Returned — revise &amp; resend</div>
                                        @endif
                                    </td>
                                    <td>{{ $order->storekeeper->name ?? 'System' }}</td>
                                    <td>
                                        <a href="{{ route('lpo.show', $order->id) }}" class="btn btn-sm btn-info">
                                            <i class="fa fa-eye"></i> Details
                                        </a>
                                        @if($order->status === 'sent_to_accountant' && in_array($role, ['accountant', 'super_admin']))
                                            <a href="{{ route('lpo.show', $order->id) }}" class="btn btn-sm btn-danger mt-1">
                                                <i class="fa fa-bell"></i> Review
                                            </a>
                                        @endif
                                        @if($order->status === 'sent_to_manager' && in_array($role, ['manager', 'super_admin']))
                                            <a href="{{ route('lpo.show', $order->id) }}" class="btn btn-sm btn-danger mt-1">
                                                <i class="fa fa-bell"></i> Verify
                                            </a>
                                        @endif
                                        @if($order->status === 'verified_by_manager' && in_array($role, ['accountant', 'super_admin']))
                                            <a href="{{ route('accountant.pay-storekeeper') }}" class="btn btn-sm btn-primary mt-1">
                                                <i class="fa fa-money"></i> Pay
                                            </a>
                                        @endif
                                        @if(in_array($order->status, ['pending', 'rejected']) && in_array($role, ['storekeeper', 'super_admin']))
                                            <a href="{{ route('lpo.edit', $order->id) }}" class="btn btn-sm btn-primary mt-1">
                                                <i class="fa fa-edit"></i> Edit
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        @if(in_array($role, ['accountant', 'manager', 'super_admin']) && $tab === 'review')
                                            No LPO budgets waiting for review.
                                        @else
                                            No LPO budgets found.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @include('dashboard.partials.bootstrap-pagination', ['paginator' => $orders])
            </div>
        </div>
    </div>
</div>
@endsection
