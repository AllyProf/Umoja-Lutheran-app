@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-shopping-cart"></i> Purchased Items from Market</h1>
        <p>View all shopping lists with purchased items</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.restaurants.shopping-list.index') }}">Shopping Lists</a></li>
        <li class="breadcrumb-item active"><a href="#">Purchased Items</a></li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <!-- Statistics Cards -->
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="widget-small primary coloured-icon">
                    <i class="icon fa fa-list fa-2x"></i>
                    <div class="info">
                        <h4>Shopping Lists</h4>
                        <p><b>{{ number_format($totalLists) }}</b></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="widget-small success coloured-icon">
                    <i class="icon fa fa-money fa-2x"></i>
                    <div class="info">
                        <h4>Total Market Price</h4>
                        <p><b>{{ number_format($totalCost, 2) }} TZS</b></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="widget-small warning coloured-icon">
                    <i class="icon fa fa-cubes fa-2x"></i>
                    <div class="info">
                        <h4>Total Items</h4>
                        <p><b>{{ number_format($totalItems) }}</b></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="tile">
            <h3 class="tile-title">Filters</h3>
            <div class="tile-body">
                <form method="GET" action="{{ route('admin.restaurants.shopping-list.purchased-items') }}" class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="shopping_list_id">Shopping List</label>
                            <select class="form-control" id="shopping_list_id" name="shopping_list_id">
                                <option value="">All Shopping Lists</option>
                                @if(isset($allShoppingListsForFilter))
                                    @foreach($allShoppingListsForFilter as $list)
                                    <option value="{{ $list->id }}" {{ request('shopping_list_id') == $list->id ? 'selected' : '' }}>
                                        {{ $list->name }} ({{ $list->shopping_date ? $list->shopping_date->format('d M Y') : 'N/A' }})
                                    </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date_from">Date From</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date_to">Date To</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label><br>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filter</button>
                            <a href="{{ route('admin.restaurants.shopping-list.purchased-items') }}" class="btn btn-secondary"><i class="fa fa-refresh"></i> Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Shopping Lists Table -->
        <div class="tile">
            <h3 class="tile-title">Purchased Shopping Lists ({{ $shoppingLists->total() }})</h3>
            <div class="tile-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Shopping List Name</th>
                                <th>Market</th>
                                <th>Items Count</th>
                                <th>Market Price</th>
                                <th>Budget Amount</th>
                                <th>Amount Remaining</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($shoppingLists as $list)
                            @php
                                $purchasedItems = $list->items->where('is_purchased', true)->where('is_found', true)->where('purchased_quantity', '>', 0);
                                $amountUsed = $purchasedItems->sum('purchased_cost');
                                $budgetAmount = $list->budget_amount ?? $list->total_estimated_cost ?? 0;
                                $amountRemaining = $budgetAmount - $amountUsed;
                            @endphp
                            <tr>
                                <td>
                                    @if($list->shopping_date)
                                        <strong>{{ \Carbon\Carbon::parse($list->shopping_date)->format('d M Y') }}</strong>
                                    @elseif($list->created_at)
                                        <strong>{{ $list->created_at->format('d M Y') }}</strong>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $list->name }}</strong><br>
                                    <small class="text-muted">Ref: SL-{{ $list->id }}</small>
                                </td>
                                <td>{{ $list->market_name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge badge-info">{{ $purchasedItems->count() }} item(s)</span>
                                </td>
                                <td>
                                    <strong class="text-success">{{ number_format($amountUsed, 2) }} TZS</strong>
                                </td>
                                <td>
                                    {{ number_format($budgetAmount, 2) }} TZS
                                </td>
                                <td>
                                    <strong class="{{ $amountRemaining < 0 ? 'text-danger' : 'text-success' }}">
                                        {{ number_format($amountRemaining, 2) }} TZS
                                    </strong>
                                </td>
                                <td>
                                    @if($list->status == 'completed')
                                        <span class="badge badge-success">Completed</span>
                                    @else
                                        <span class="badge badge-warning">{{ ucfirst($list->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.restaurants.shopping-list.receiving-report', $list->id) }}" target="_blank" class="btn btn-sm btn-primary" title="Print Receiving Report">
                                            <i class="fa fa-print"></i> Print
                                        </a>
                                        <a href="{{ route('admin.restaurants.shopping-list.download', $list->id) }}" target="_blank" class="btn btn-sm btn-secondary" title="Download Shopping List">
                                            <i class="fa fa-download"></i> Download
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center">
                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle"></i> <strong>No purchased shopping lists found.</strong>
                                        <br><br>
                                        <p class="mb-0">
                                            Shopping lists will appear here after you record purchases from the market.
                                            <br>
                                            Go to <a href="{{ route('admin.restaurants.shopping-list.index') }}">Shopping Lists</a> and click "Receive" to record purchases.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-right"><strong>Total Market Price:</strong></td>
                                <td><strong>{{ number_format($shoppingLists->sum(function($list) { return $list->items->where('is_purchased', true)->where('is_found', true)->sum('purchased_cost'); }), 2) }} TZS</strong></td>
                                <td colspan="4"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($shoppingLists->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $shoppingLists->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
