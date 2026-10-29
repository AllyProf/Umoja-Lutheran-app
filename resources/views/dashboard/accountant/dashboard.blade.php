@extends('dashboard.layouts.app')

@section('content')
    <div class="app-title">
        <div>
            <h1><i class="fa fa-money"></i> Accountant Dashboard</h1>
            <p>Monitor finances and approve purchase requests</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
        </ul>
    </div>

    @if(($stats['pending_lpo'] ?? 0) > 0)
        <div class="alert alert-danger shadow-sm d-flex align-items-center justify-content-between flex-wrap">
            <div>
                <strong><i class="fa fa-bell"></i> {{ $stats['pending_lpo'] }} LPO budget{{ $stats['pending_lpo'] === 1 ? '' : 's' }} awaiting your review</strong>
                <div class="small mb-0">Storekeeper sent a budget — accept, adjust, or reject it.</div>
            </div>
            <a href="{{ route('lpo.index', ['tab' => 'review']) }}" class="btn btn-sm btn-light text-danger font-weight-bold mt-2 mt-md-0">
                Open LPO review
            </a>
        </div>
    @endif

    <!-- Financial Summary -->
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="widget-small primary coloured-icon">
                <i class="icon fa fa-line-chart fa-3x"></i>
                <div class="info">
                    <h4>Total Revenue</h4>
                    <p><b>TZS {{ number_format($stats['total_revenue'] ?? 0, 0) }}</b></p>
                    <small class="text-muted">This month: TZS {{ number_format($stats['month_revenue'] ?? 0, 0) }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="widget-small danger coloured-icon">
                <i class="icon fa fa-shopping-cart fa-3x"></i>
                <div class="info">
                    <h4>Total Expenditure</h4>
                    <p><b>TZS {{ number_format($stats['total_expenditure'] ?? 0, 0) }}</b></p>
                    <small class="text-muted">This month: TZS {{ number_format($stats['month_expenditure'] ?? 0, 0) }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="widget-small {{ ($stats['net_income'] ?? 0) >= 0 ? 'success' : 'warning' }} coloured-icon">
                <i class="icon fa fa-balance-scale fa-3x"></i>
                <div class="info">
                    <h4>Net Income</h4>
                    <p><b>TZS {{ number_format($stats['net_income'] ?? 0, 0) }}</b></p>
                    <small class="text-muted">This month: TZS {{ number_format($stats['month_net_income'] ?? 0, 0) }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Operational Statistics -->
    <div class="row">
        <div class="col-md-3">
            <div class="widget-small danger coloured-icon">
                <i class="icon fa fa-file-text fa-3x"></i>
                <div class="info">
                    <h4>LPO to Review</h4>
                    <p><b>{{ $stats['pending_lpo'] ?? 0 }}</b></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="widget-small primary coloured-icon">
                <i class="icon fa fa-clock-o fa-3x"></i>
                <div class="info">
                    <h4>Pending Approval</h4>
                    <p><b>{{ $stats['pending_approval'] }}</b></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <a href="{{ route('accountant.pay-storekeeper') }}" style="text-decoration:none;color:inherit;">
                <div class="widget-small info coloured-icon">
                    <i class="icon fa fa-check-circle fa-3x"></i>
                    <div class="info">
                        <h4>Pay Now</h4>
                        <p><b>{{ $stats['approved_lists'] }}</b></p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <div class="widget-small warning coloured-icon">
                <i class="icon fa fa-truck fa-3x"></i>
                <div class="info">
                    <h4>To Buy</h4>
                    <p><b>{{ $stats['ready_lists'] }}</b></p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Pending LPO Reviews -->
        <div class="col-md-6 mb-4">
            <div class="tile">
                <div class="tile-title-w-btn">
                    <h3 class="title"><i class="fa fa-file-text mr-2 text-danger"></i> LPO Awaiting Review</h3>
                    <a href="{{ route('lpo.index', ['tab' => 'review']) }}" class="btn btn-sm btn-danger">Review</a>
                </div>

                @if(isset($pendingLpos) && $pendingLpos->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>LPO</th>
                                    <th>Period</th>
                                    <th>Budget</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingLpos as $lpo)
                                    <tr class="table-warning">
                                        <td>
                                            <strong>#{{ $lpo->id }}</strong>
                                            <div class="small text-muted">{{ $lpo->storekeeper->name ?? 'Storekeeper' }}</div>
                                        </td>
                                        <td class="small">
                                            {{ $lpo->start_date->format('d M') }} – {{ $lpo->end_date->format('d M') }}
                                        </td>
                                        <td>TZS {{ number_format($lpo->total_amount) }}</td>
                                        <td>
                                            <a href="{{ route('lpo.show', $lpo->id) }}" class="btn btn-xs btn-danger">Open</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fa fa-check-circle fa-3x text-success mb-2"></i>
                        <p class="text-muted mb-0">No LPO budgets waiting.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Pending Shopping Lists -->
        <div class="col-md-6 mb-4">
            <div class="tile">
                <div class="tile-title-w-btn">
                    <h3 class="title"><i class="fa fa-hourglass-half mr-2"></i> Awaiting Approval</h3>
                    <a href="{{ route('accountant.shopping-lists') }}" class="btn btn-sm btn-primary">View All</a>
                </div>

                @if($pendingLists->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>List Name</th>
                                    <th>Est. Cost</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingLists as $list)
                                    <tr>
                                        <td><strong>{{ $list->name }}</strong><br><small>{{ $list->items->count() }} items</small>
                                        </td>
                                        <td>TZS {{ number_format($list->total_estimated_cost) }}</td>
                                        <td>{{ $list->created_at->format('M d') }}</td>
                                        <td>
                                            <a href="{{ route('accountant.shopping-list.show', $list->id) }}"
                                                class="btn btn-xs btn-info">Review</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fa fa-smile-o fa-3x text-success mb-2"></i>
                        <p class="text-muted">No shopping lists awaiting approval.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="col-md-6">
            <div class="tile">
                <div class="tile-title-w-btn">
                    <h3 class="title"><i class="fa fa-history mr-2"></i> Recent Activity</h3>
                    <a href="{{ route('accountant.reports') }}" class="btn btn-sm btn-primary">Reports</a>
                </div>

                @if($recentLists->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>List Name</th>
                                    <th>Status</th>
                                    <th>Actual Cost</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentLists as $list)
                                    <tr>
                                        <td><strong>{{ $list->name }}</strong></td>
                                        <td>
                                            <span class="badge badge-{{ $list->status === 'purchased' ? 'success' : 'info' }}">
                                                {{ ucfirst($list->status) }}
                                            </span>
                                        </td>
                                        <td>TZS {{ number_format($list->total_actual_cost) }}</td>
                                        <td><small>{{ $list->updated_at->format('M d, H:i') }}</small></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center py-4">No recent activity found.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
