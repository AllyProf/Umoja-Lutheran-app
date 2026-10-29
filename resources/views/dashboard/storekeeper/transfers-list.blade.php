@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-exchange"></i> Stock Transfers</h1>
        <p>History of items issued from the main store to departments</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route('storekeeper.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Transfers</li>
    </ul>
</div>

<div class="mb-3 d-flex flex-wrap" style="gap:8px;">
    <a href="{{ route('storekeeper.transfers.create') }}" class="btn btn-success">
        <i class="fa fa-plus"></i> Distribute Stock
    </a>
    <a href="{{ route('stock-requests.index') }}" class="btn btn-outline-primary">
        <i class="fa fa-clipboard"></i> Department Requests
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fa fa-check-circle mr-1"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
@endif

<div class="tile mb-3">
    <form method="GET" action="{{ route('storekeeper.transfers.index') }}" class="transfers-filter-form">
        <div class="row align-items-end">
            <div class="col-lg-4 col-md-6 form-group mb-2 mb-md-3">
                <label class="font-weight-bold small mb-1">Search</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white"><i class="fa fa-search text-muted"></i></span>
                    </div>
                    <input type="text" name="search" class="form-control"
                        value="{{ request('search') }}"
                        placeholder="Product, receiver, or reference...">
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-6 form-group mb-2 mb-md-3">
                <label class="font-weight-bold small mb-1">From date</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-lg-2 col-md-3 col-6 form-group mb-2 mb-md-3">
                <label class="font-weight-bold small mb-1">To date</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-lg-2 col-md-4 col-6 form-group mb-2 mb-md-3">
                <label class="font-weight-bold small mb-1">Status</label>
                <select name="status" class="form-control">
                    <option value="">All statuses</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="col-lg-2 col-md-4 col-6 form-group mb-2 mb-md-3">
                <label class="font-weight-bold small mb-1 d-none d-md-block">&nbsp;</label>
                <div class="d-flex" style="gap:6px;">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="fa fa-filter"></i> Filter
                    </button>
                    @if(request()->hasAny(['search', 'date_from', 'date_to', 'status']))
                        <a href="{{ route('storekeeper.transfers.index') }}" class="btn btn-outline-secondary" title="Clear filters">
                            <i class="fa fa-times"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
        @if(request()->filled('search') || request()->filled('date_from') || request()->filled('date_to') || request()->filled('status'))
            <div class="small text-muted">
                Showing filtered results
                @if(request('search'))
                    · search “{{ request('search') }}”
                @endif
                @if(request('date_from') || request('date_to'))
                    ·
                    @if(request('date_from') && request('date_to'))
                        {{ request('date_from') }} → {{ request('date_to') }}
                    @elseif(request('date_from'))
                        from {{ request('date_from') }}
                    @else
                        until {{ request('date_to') }}
                    @endif
                @endif
                · <strong>{{ $transfers->total() }}</strong> transfer{{ $transfers->total() === 1 ? '' : 's' }}
            </div>
        @endif
    </form>
</div>

<div class="tile">
    <div class="table-responsive">
        <table class="table table-hover table-striped mb-0">
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Date</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>To</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($transfers as $transfer)
                    <tr>
                        <td><strong>{{ $transfer->transfer_reference }}</strong></td>
                        <td>{{ optional($transfer->transfer_date)->format('d M Y') }}</td>
                        <td>
                            {{ $transfer->product->name ?? '—' }}
                            @if($transfer->productVariant)
                                <div class="small text-muted">{{ $transfer->productVariant->variant_name }}</div>
                            @endif
                        </td>
                        <td>
                            {{ $transfer->formatted_quantity }}
                            <span class="text-muted">{{ $transfer->quantity_unit_name }}</span>
                        </td>
                        <td>
                            {{ $transfer->receivedBy->name ?? '—' }}
                            @if($transfer->receivedBy)
                                <div class="small text-muted">{{ $transfer->receivedBy->role }}</div>
                            @endif
                        </td>
                        <td>
                            @php
                                $badge = match($transfer->status) {
                                    'completed' => 'success',
                                    'pending' => 'warning',
                                    'cancelled' => 'secondary',
                                    default => 'light',
                                };
                            @endphp
                            <span class="badge badge-{{ $badge }}">{{ ucfirst($transfer->status) }}</span>
                        </td>
                        <td class="text-right">
                            <a href="{{ route('storekeeper.transfers.download', $transfer) }}"
                                class="btn btn-sm btn-outline-secondary" target="_blank">
                                <i class="fa fa-print"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            @if(request()->hasAny(['search', 'date_from', 'date_to', 'status']))
                                No transfers match your filters.
                                <a href="{{ route('storekeeper.transfers.index') }}">Clear filters</a>
                            @else
                                No transfers yet.
                                <a href="{{ route('storekeeper.transfers.create') }}">Distribute stock</a>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @include('dashboard.partials.bootstrap-pagination', ['paginator' => $transfers])
</div>
@endsection
