@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-check-circle"></i> Received Items</h1>
        <p>View all items received by departments</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.purchase-requests.index') }}">Purchase Requests</a></li>
        <li class="breadcrumb-item active"><a href="#">Received Items</a></li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <!-- Filters -->
        <div class="tile">
            <h3 class="tile-title">Filters</h3>
            <div class="tile-body">
                <form method="GET" action="{{ route('admin.purchase-requests.received') }}" class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="department">Department</label>
                            <select class="form-control" id="department" name="department">
                                <option value="">All Departments</option>
                                <option value="housekeeping" {{ request('department') == 'housekeeping' ? 'selected' : '' }}>Housekeeping</option>
                                <option value="reception" {{ request('department') == 'reception' ? 'selected' : '' }}>Reception</option>
                                <option value="bar" {{ request('department') == 'bar' ? 'selected' : '' }}>Bar</option>
                                <option value="food" {{ request('department') == 'food' ? 'selected' : '' }}>Food</option>
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
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label><br>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filter</button>
                            <a href="{{ route('admin.purchase-requests.received') }}" class="btn btn-secondary"><i class="fa fa-refresh"></i> Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Department Statistics -->
        @if(isset($departmentStats) && $departmentStats->count() > 0)
        <div class="row mb-3">
            @foreach($departmentStats as $dept => $stats)
            <div class="col-md-3">
                <div class="tile">
                    <div class="tile-body text-center">
                        <h4>{{ $dept }}</h4>
                        <p class="mb-1"><strong>Items:</strong> {{ $stats['count'] }}</p>
                        <p class="mb-0"><strong>Total Qty:</strong> {{ number_format($stats['total_quantity'], 2) }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Received Items Table -->
        <div class="tile">
            <h3 class="tile-title">Received Items ({{ $receivedItems->total() }})</h3>
            <div class="tile-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th>Category</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th>Department</th>
                                <th>Requested By</th>
                                <th>Received Date</th>
                                <th>Shopping List</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($receivedItems as $item)
                            <tr>
                                <td><strong>{{ $item->product_name }}</strong></td>
                                <td>{{ ucfirst(str_replace('_', ' ', $item->category ?? 'other')) }}</td>
                                <td><strong class="text-success">{{ number_format($item->purchased_quantity ?? $item->quantity, 2) }}</strong></td>
                                <td>{{ $item->unit }}</td>
                                <td>
                                    @if($item->purchaseRequest && $item->purchaseRequest->requestedBy)
                                        <span class="badge badge-info">{{ $item->purchaseRequest->requestedBy->getDepartmentName() }}</span>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if($item->purchaseRequest && $item->purchaseRequest->requestedBy)
                                        {{ $item->purchaseRequest->requestedBy->name }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if($item->received_by_department_at)
                                        {{ \Carbon\Carbon::parse($item->received_by_department_at)->format('d M Y H:i') }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if($item->shoppingList)
                                        <a href="{{ route('admin.restaurants.shopping-list.receiving-report', $item->shoppingList->id) }}" target="_blank" class="btn btn-sm btn-info">
                                            <i class="fa fa-file"></i> {{ $item->shoppingList->name }}
                                        </a>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if($item->shoppingList)
                                        <a href="{{ route('admin.restaurants.shopping-list.receiving-report', $item->shoppingList->id) }}" target="_blank" class="btn btn-sm btn-primary" title="View Receiving Report">
                                            <i class="fa fa-print"></i> Report
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center">
                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle"></i> <strong>No received items found.</strong>
                                        <br><br>
                                        <p class="mb-0">
                                            Items will appear here after department staff receive them from the transfer process.
                                            <br>
                                            To see items ready for transfer, go to <a href="{{ route('admin.restaurants.shopping-list.index') }}">Shopping Lists</a> and check completed lists.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($receivedItems->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $receivedItems->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
