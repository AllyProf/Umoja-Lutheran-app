@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-ambulance"></i> Emergency Orders</h1>
        <p>Urgent purchase requests from the storekeeper</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route('accountant.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item">Emergency Orders</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="tile">
            <ul class="nav nav-tabs mb-4">
                <li class="nav-item">
                    <a class="nav-link {{ $tab === 'active' ? 'active' : '' }}"
                        href="{{ route('accountant.emergency-orders', ['tab' => 'active']) }}">
                        Active
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $tab === 'done' ? 'active' : '' }}"
                        href="{{ route('accountant.emergency-orders', ['tab' => 'done']) }}">
                        Completed / Rejected
                    </a>
                </li>
            </ul>

            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Requested By</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $i => $order)
                            <tr class="{{ $order->status === 'pending' ? 'table-warning' : '' }}">
                                <td>{{ $orders->firstItem() + $i }}</td>
                                <td>
                                    <strong>{{ $order->item_name ?? $order->product_name ?? 'Item' }}</strong>
                                    @if($order->unit)
                                        <br><small class="text-muted">{{ $order->unit }}</small>
                                    @endif
                                </td>
                                <td>{{ $order->quantity }}</td>
                                <td>{{ $order->requestedBy->name ?? 'Storekeeper' }}</td>
                                <td class="small">{{ \Illuminate\Support\Str::limit($order->reason ?? $order->notes ?? '—', 80) }}</td>
                                <td>
                                    <span class="badge badge-{{ $order->status === 'pending' ? 'warning' : ($order->status === 'rejected' ? 'danger' : 'info') }}">
                                        {{ strtoupper(str_replace('_', ' ', $order->status)) }}
                                    </span>
                                </td>
                                <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    No emergency orders {{ $tab === 'active' ? 'waiting' : 'in history' }}.
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
@endsection
