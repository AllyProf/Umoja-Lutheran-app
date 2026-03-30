@extends('dashboard.layouts.app')

@section('content')
    <div class="app-title">
        <div>
            <h1><i class="fa fa-archive"></i> Storekeeper Dashboard</h1>
            <p>Manage inventory, purchases, and distributions</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
        </ul>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="widget-small primary coloured-icon">
                <i class="icon fa fa-cubes fa-3x"></i>
                <div class="info">
                    <h4>Total Products</h4>
                    <p><b>{{ $stats['total_items'] }}</b></p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="widget-small warning coloured-icon">
                <i class="icon fa fa-list-alt fa-3x"></i>
                <div class="info">
                    <h4>Active Shopping Lists</h4>
                    <p><b>{{ $stats['active_shopping_lists'] }}</b></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="tile shadow-sm">
                <h3 class="tile-title">Quick Actions</h3>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('storekeeper.shopping-list.create') }}"
                            class="btn btn-primary btn-block p-4 shadow-sm d-flex flex-column align-items-center">
                            <i class="fa fa-plus-circle fa-2x mb-2"></i>
                            <span>New Shopping List</span>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('storekeeper.stock-receipts.create') }}"
                            class="btn btn-success btn-block p-4 shadow-sm d-flex flex-column align-items-center">
                            <i class="fa fa-inbox fa-2x mb-2"></i>
                            <span>Receive Stock</span>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('storekeeper.products.create') }}"
                            class="btn btn-warning text-white btn-block p-4 shadow-sm d-flex flex-column align-items-center">
                            <i class="fa fa-plus fa-2x mb-2"></i>
                            <span>Add New Product</span>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('storekeeper.inventory') }}"
                            class="btn btn-info btn-block p-4 shadow-sm d-flex flex-column align-items-center">
                            <i class="fa fa-cubes fa-2x mb-2"></i>
                            <span>View Inventory</span>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('storekeeper.purchase-requests.emergency') }}"
                            class="btn btn-danger btn-block p-4 shadow-sm d-flex flex-column align-items-center">
                            <i class="fa fa-ambulance fa-2x mb-2"></i>
                            <span>Emergency Purchase</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row mb-4">
            <!-- Recent Shopping Lists -->
            <div class="col-md-6">
                <div class="tile">
                    <div class="tile-title-w-btn">
                        <h3 class="title"><i class="fa fa-list-alt mr-2"></i> Recent Shopping Lists</h3>
                        <a href="{{ route('storekeeper.shopping-list.index') }}" class="btn btn-sm btn-primary">View All</a>
                    </div>

                    @if($recentShoppingLists->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-sm">
                                <thead>
                                    <tr>
                                        <th>List Name</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentShoppingLists as $list)
                                        <tr>
                                            <td><strong>{{ $list->name }}</strong></td>
                                            <td>
                                                @php
                                                    $statusClass = match ($list->status) {
                                                        'pending' => 'badge-warning',
                                                        'approved' => 'badge-info',
                                                        'on_list' => 'badge-primary',
                                                        'completed' => 'badge-success',
                                                        'rejected' => 'badge-danger',
                                                        default => 'badge-secondary'
                                                    };
                                                @endphp
                                                <span class="badge {{ $statusClass }}">{{ ucfirst($list->status) }}</span>
                                            </td>
                                            <td><small>{{ $list->created_at->diffForHumans() }}</small></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fa fa-list-ul fa-3x text-muted mb-2"></i>
                            <p class="text-muted">No recent shopping lists.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Transfers -->
            <div class="col-md-6">
                <div class="tile">
                    <div class="tile-title-w-btn">
                        <h3 class="title"><i class="fa fa-exchange mr-2"></i> Recent Internal Transfers</h3>
                        <a href="{{ route('storekeeper.transfers.index') }}" class="btn btn-sm btn-primary">View All</a>
                    </div>

                    @if($recentTransfers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-sm">
                                <thead>
                                    <tr>
                                        <th>Reference</th>
                                        <th>Product</th>
                                        <th>Qty</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentTransfers as $transfer)
                                        <tr>
                                            <td><strong>{{ $transfer->transfer_reference }}</strong></td>
                                            <td>{{ $transfer->product->name ?? 'N/A' }}</td>
                                            <td>{{ $transfer->quantity_transferred }}</td>
                                            <td>
                                                @if($transfer->status === 'completed')
                                                    <span class="badge badge-success">Completed</span>
                                                @else
                                                    <span class="badge badge-warning">Pending</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fa fa-exchange fa-3x text-muted mb-2"></i>
                            <p class="text-muted">No recent transfers.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
@endsection