@extends('dashboard.layouts.app')

@section('content')
    <div class="app-title">
        <div>
            <h1><i class="fa fa-shopping-basket"></i> Shopping Lists</h1>
            <p>Manage kitchen procurement and market lists</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="#">Restaurants</a></li>
            <li class="breadcrumb-item active"><a href="#">Shopping Lists</a></li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-title-w-btn">
                    <h3 class="title">All Shopping Lists</h3>
                    <div class="btn-group">
                        <a class="btn btn-success icon-btn"
                            href="{{ route('admin.restaurants.shopping-list.purchased-items') }}">
                            <i class="fa fa-shopping-cart"></i> View Purchased Items
                        </a>
                        <a class="btn btn-primary icon-btn" href="{{ route('admin.restaurants.shopping-list.create') }}">
                            <i class="fa fa-plus"></i> Create New List
                        </a>
                    </div>
                </div>
                <div class="tile-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="shoppingTable">
                            <thead>
                                <tr>
                                    <th>Purchaser Name</th>
                                    <th>Market</th>
                                    <th>Date</th>
                                    <th>Items</th>
                                    <th>Status</th>
                                    <th>Total Cost (Est / Actual)</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($shoppingLists as $list)
                                    <tr>
                                        <td>{{ $list->name }}</td>
                                        <td>{{ $list->market_name ?? 'N/A' }}</td>
                                        <td>{{ $list->shopping_date ? $list->shopping_date->format('d M Y') : 'N/A' }}</td>
                                        <td>{{ $list->items_count }}</td>
                                        <td>
                                            @if($list->status == 'pending')
                                                <span class="badge badge-warning">Pending</span>
                                            @elseif($list->status == 'accountant_checked')
                                                <span class="badge badge-info">Awaiting Manager</span>
                                            @elseif($list->status == 'approved')
                                                <span class="badge badge-primary">To Pay</span>
                                            @elseif($list->status == 'ready_for_purchase')
                                                <span class="badge badge-light-warning text-dark border">To Buy</span>
                                            @elseif($list->status == 'purchased')
                                                <span class="badge badge-info text-white">To Verify</span>
                                            @elseif($list->status == 'completed')
                                                <span class="badge badge-success">Completed</span>
                                            @else
                                                <span
                                                    class="badge badge-secondary">{{ ucfirst(str_replace('_', ' ', $list->status)) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ number_format($list->total_estimated_cost ?? 0, 0) }} TZS</strong> /
                                            {{ number_format($list->total_actual_cost ?? 0, 0) }} TZS
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a class="btn btn-info btn-sm"
                                                    href="{{ route('admin.restaurants.shopping-list.show', $list->id) }}"
                                                    title="View Details">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a class="btn btn-secondary btn-sm"
                                                    href="{{ route('admin.restaurants.shopping-list.download', $list->id) }}"
                                                    target="_blank" title="Download">
                                                    <i class="fa fa-download"></i>
                                                </a>
                                                @if($list->status == 'pending')
                                                    <a class="btn btn-primary btn-sm"
                                                        href="{{ route('admin.restaurants.shopping-list.edit', $list->id) }}"
                                                        title="Edit List">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                @endif
                                                @if($list->status == 'ready_for_purchase')
                                                    <a class="btn btn-success btn-sm"
                                                        href="{{ route('admin.restaurants.shopping-list.record', $list->id) }}"
                                                        title="Record Purchases">
                                                        <i class="fa fa-check-square-o"></i> Receive
                                                    </a>
                                                @endif
                                                @if($list->status == 'pending')
                                                    <form action="{{ route('admin.restaurants.shopping-list.destroy', $list->id) }}"
                                                        method="POST" style="display:inline;"
                                                        onsubmit="return confirm('Are you sure you want to delete this list?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete"><i
                                                                class="fa fa-trash"></i></button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $shoppingLists->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection