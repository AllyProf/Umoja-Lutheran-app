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

    <!-- Statistics Cards -->
    <div class="row">
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
            <div class="widget-small info coloured-icon">
                <i class="icon fa fa-check-circle fa-3x"></i>
                <div class="info">
                    <h4>Pay Now</h4>
                    <p><b>{{ $stats['approved_lists'] }}</b></p>
                </div>
            </div>
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
        <div class="col-md-3">
            <div class="widget-small success coloured-icon">
                <i class="icon fa fa-shopping-bag fa-3x"></i>
                <div class="info">
                    <h4>Purchased</h4>
                    <p><b>{{ $stats['purchased_lists'] }}</b></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="widget-small info coloured-icon" style="background-color: #f8f9fa;">
                <i class="icon fa fa-car fa-3x bg-success"></i>
                <div class="info">
                    <h4>Today's Day Services</h4>
                    <p><b>TZS {{ number_format($stats['total_day_services_revenue']) }}</b></p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Pending Shopping Lists -->
        <div class="col-md-6">
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
