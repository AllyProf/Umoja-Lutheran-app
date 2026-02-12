@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-history"></i> Completed Orders History</h1>
        <p>View all completed bar and restaurant transactions</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route('bar-keeper.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item">Order History</li>
    </ul>
</div>

<div class="row">
    {{-- Statistics Cards --}}
    <div class="col-md-6 col-lg-4">
        <div class="widget-small primary coloured-icon">
            <i class="icon fa fa-shopping-cart fa-3x"></i>
            <div class="info">
                <h4>Today's Orders</h4>
                <p><b>{{ number_format($stats['today_orders']) }}</b></p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-4">
        <div class="widget-small info coloured-icon">
            <i class="icon fa fa-money fa-3x"></i>
            <div class="info">
                <h4>Today's Revenue</h4>
                <p><b>{{ number_format($stats['today_revenue']) }} TZS</b></p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-4">
        <div class="widget-small warning coloured-icon">
            <i class="icon fa fa-calendar fa-3x"></i>
            <div class="info">
                <h4>Monthly Revenue</h4>
                <p><b>{{ number_format($stats['month_revenue']) }} TZS</b></p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="tile shadow-sm border-0">
            <div class="tile-title-w-btn">
                <h3 class="title"><i class="fa fa-list mr-2"></i> Order History</h3>
            </div>
            <div class="tile-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="bg-light">
                            <tr>
                                <th>Date & Time</th>
                                <th>Guest / Room</th>
                                <th>Items</th>
                                <th>Qty</th>
                                <th>Payment Method</th>
                                <th>Reference</th>
                                <th class="text-right">Total (TZS)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td>
                                        <div class="font-weight-bold">{{ $order->completed_at ? $order->completed_at->format('M d, Y') : '-' }}</div>
                                        <small class="text-muted">{{ $order->completed_at ? $order->completed_at->format('H:i A') : '-' }}</small>
                                    </td>
                                    <td>
                                        @if($order->is_walk_in)
                                            <span class="badge badge-secondary mb-1">WALK-IN</span><br>
                                            <strong>{{ $order->walk_in_name ?? 'General Walk-in' }}</strong>
                                        @else
                                            <strong>Room {{ $order->booking->room->room_number ?? 'N/A' }}</strong><br>
                                            <small>{{ $order->booking->guest_name ?? 'Unknown Guest' }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="font-weight-bold">{{ $order->service_specific_data['item_name'] ?? ($order->service->name ?? 'Service') }}</div>
                                        
                                        {{-- Originator Info --}}
                                        @if($order->reception_notes)
                                            <div class="mt-1 small border-left pl-2" style="border-width: 2px !important; border-color: #009688 !important;">
                                                @if(str_contains($order->reception_notes, 'POS Order by Waiter:'))
                                                    @php
                                                        $info = str_replace('POS Order by Waiter: ', '', $order->reception_notes);
                                                        $parts = explode(' - Msg: ', $info);
                                                        $waiter = $parts[0] ?? 'Waiter';
                                                        $msg = $parts[1] ?? null;
                                                    @endphp
                                                    <span class="text-dark"><i class="fa fa-user-circle-o"></i> {{ $waiter }}</span>
                                                    @if($msg)
                                                        <br><span class="text-muted"><i>"{{ $msg }}"</i></span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">{{ $order->reception_notes }}</span>
                                                @endif
                                            </div>
                                        @endif
                                        
                                        @if(!empty($order->guest_request))
                                            <div class="mt-1"><small class="text-info"><i>Request: "{{ $order->guest_request }}"</i></small></div>
                                        @endif
                                    </td>
                                    <td>{{ $order->quantity }}</td>
                                    <td>
                                        @php
                                            $pStatus = strtoupper($order->payment_status ?? 'PENDING');
                                            $pMethod = strtoupper(str_replace('_', ' ', $order->payment_method ?? '-'));
                                            $statusClass = $pStatus === 'PAID' ? 'badge-success' : 'badge-danger';
                                        @endphp
                                        <span class="badge {{ $statusClass }} mb-1" style="{{ $pStatus !== 'PAID' ? 'background-color: #dc3545;' : '' }}">{{ $pStatus }}</span>
                                        <div class="small text-muted font-weight-bold">{{ $pMethod }}</div>
                                    </td>
                                    <td>
                                        @if($order->payment_reference)
                                            <code class="text-primary font-weight-bold">{{ $order->payment_reference }}</code>
                                        @else
                                            <span class="text-muted italic small">N/A</span>
                                        @endif
                                    </td>
                                    <td class="text-right font-weight-bold">{{ number_format($order->total_price_tsh) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fa fa-folder-open-o fa-3x mb-3"></i>
                                        <p>No completed orders found.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- Pagination --}}
                <div class="d-flex justify-content-center mt-4">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom styles for the order history page */
.widget-small {
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    transition: transform 0.2s;
}
.widget-small:hover {
    transform: translateY(-5px);
}
.table thead th {
    border-top: none;
    text-transform: uppercase;
    font-size: 11px;
    letter-spacing: 1px;
}
.badge {
    padding: 5px 10px;
    border-radius: 4px;
}
</style>
@endsection
