@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-bar-chart"></i> Order Summary</h1>
        <p>{{ $isManager ? 'All counters – sold items & cancellations' : 'Your sold items & cancellations' }}</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route('bar-keeper.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item">Order Summary</li>
    </ul>
</div>

{{-- ── Date Filter ── --}}
<div class="row mb-3">
    <div class="col-md-12">
        <div class="tile shadow-sm">
            <form method="GET" action="{{ route('bar-keeper.order-summary') }}" class="form-inline flex-wrap" style="gap:8px;">
                <div class="form-group mr-2">
                    <label class="mr-2 font-weight-bold">Period:</label>
                    <select name="date_type" class="form-control form-control-sm" onchange="this.form.submit()">
                        <option value="daily"   {{ $dateType === 'daily'   ? 'selected' : '' }}>Daily</option>
                        <option value="weekly"  {{ $dateType === 'weekly'  ? 'selected' : '' }}>Weekly</option>
                        <option value="monthly" {{ $dateType === 'monthly' ? 'selected' : '' }}>Monthly</option>
                    </select>
                </div>
                <div class="form-group mr-2">
                    <label class="mr-2 font-weight-bold">Date:</label>
                    <input type="date" name="date" class="form-control form-control-sm"
                           value="{{ $date->toDateString() }}" onchange="this.form.submit()">
                </div>
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="fa fa-search"></i> Filter
                </button>
                <a href="{{ route('bar-keeper.order-summary') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa fa-refresh"></i> Today
                </a>
                <span class="ml-auto text-muted small align-self-center">
                    {{ $startDate->format('M d, Y') }} – {{ $endDate->format('M d, Y') }}
                </span>
            </form>
        </div>
    </div>
</div>

{{-- ── Summary Cards ── --}}
<div class="row mb-3">
    <div class="col-6 col-md-3">
        <div class="widget-small primary coloured-icon">
            <i class="icon fa fa-shopping-cart fa-3x"></i>
            <div class="info">
                <h4>Orders Sold</h4>
                <p><b>{{ number_format($summary['total_sold']) }}</b></p>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="widget-small success coloured-icon">
            <i class="icon fa fa-money fa-3x"></i>
            <div class="info">
                <h4>Total Revenue</h4>
                <p><b>{{ number_format($summary['total_revenue']) }} TZS</b></p>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="widget-small info coloured-icon">
            <i class="icon fa fa-check-circle fa-3x"></i>
            <div class="info">
                <h4>Paid</h4>
                <p><b>{{ number_format($summary['paid_revenue']) }} TZS</b></p>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="widget-small danger coloured-icon">
            <i class="icon fa fa-times-circle fa-3x"></i>
            <div class="info">
                <h4>Cancelled</h4>
                <p><b>{{ number_format($summary['total_cancelled']) }} orders</b></p>
            </div>
        </div>
    </div>
</div>

{{-- ── Per-Counter Breakdown ── --}}
@if($byCounter->isEmpty())
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="tile shadow-sm text-center py-5">
                <i class="fa fa-coffee fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">Hakuna mauzo kwa kipindi hiki.</h4>
            </div>
        </div>
    </div>
@else
    @foreach($byCounter as $counterId => $data)
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="tile shadow-sm" style="border-top: 4px solid #009688;">
                <div class="tile-title-w-btn">
                    <h3 class="title">
                        <i class="fa fa-user-circle-o text-teal mr-1"></i>
                        Counter: <strong>{{ $data['counter_name'] }}</strong>
                    </h3>
                    <div>
                        <span class="badge badge-success mr-1">
                            Paid: {{ number_format($data['paid_revenue']) }} TZS
                        </span>
                        @if($data['pending_revenue'] > 0)
                            <span class="badge badge-warning mr-1">
                                Pending: {{ number_format($data['pending_revenue']) }} TZS
                            </span>
                        @endif
                        <span class="badge badge-primary">
                            {{ $data['total_orders'] }} orders | Total: {{ number_format($data['total_revenue']) }} TZS
                        </span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-hover table-bordered mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Time</th>
                                <th>Item</th>
                                <th>Guest / Room</th>
                                <th class="text-center">Qty</th>
                                <th class="text-right">Unit Price</th>
                                <th class="text-right">Total</th>
                                <th>Payment</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['orders'] as $order)
                            <tr>
                                <td class="text-nowrap">
                                    {{ $order->completed_at ? $order->completed_at->format('H:i') : '-' }}
                                </td>
                                <td>
                                    <strong>{{ $order->service_specific_data['item_name'] ?? $order->service->name ?? 'Item' }}</strong>
                                </td>
                                <td>
                                    @if($order->is_walk_in)
                                        <span class="badge badge-secondary badge-sm">Walk-in</span>
                                        {{ str_ireplace('Walk-in ', '', $order->walk_in_name ?? '') }}
                                    @elseif($order->booking)
                                        <span class="badge badge-primary badge-sm">Room {{ $order->booking->room->room_number ?? 'N/A' }}</span>
                                        {{ $order->booking->guest_name ?? '' }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $order->quantity }}</td>
                                <td class="text-right">{{ number_format($order->unit_price_tsh) }}</td>
                                <td class="text-right font-weight-bold">{{ number_format($order->total_price_tsh) }}</td>
                                <td>
                                    @if($order->payment_status === 'paid')
                                        <span class="badge badge-success">PAID</span>
                                        <small class="d-block text-muted">{{ strtoupper($order->payment_method ?? '') }}</small>
                                    @elseif($order->payment_status === 'room_charge')
                                        <span class="badge badge-info">ROOM</span>
                                    @else
                                        <span class="badge badge-warning">PENDING</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot style="background: #e0f2f1;">
                            <tr>
                                <td colspan="5" class="text-right font-weight-bold">Sub-total:</td>
                                <td class="text-right font-weight-bold">{{ number_format($data['total_revenue']) }} TZS</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endforeach
@endif

{{-- ── Cancelled Orders ── --}}
<div class="row mb-4">
    <div class="col-md-12">
        <div class="tile shadow-sm" style="border-top: 4px solid #dc3545;">
            <div class="tile-title-w-btn">
                <h3 class="title" style="color:#dc3545;">
                    <i class="fa fa-ban mr-1"></i> Cancelled Orders
                    <span class="badge badge-danger ml-2">{{ $cancelledOrders->count() }}</span>
                </h3>
            </div>

            @if($cancelledOrders->isEmpty())
                <div class="text-center py-4 text-muted">
                    <i class="fa fa-check-circle fa-2x text-success mb-2"></i>
                    <p>Hakuna order zilizofutwa kwa kipindi hiki. 👍</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-sm table-hover table-bordered mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Time Cancelled</th>
                                <th>Item</th>
                                <th>Guest / Room</th>
                                <th class="text-center">Qty</th>
                                <th class="text-right">Value (TZS)</th>
                                @if($isManager)
                                <th>Cancelled By</th>
                                @endif
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cancelledOrders as $order)
                            <tr class="table-danger" style="background: #fff5f5 !important;">
                                <td class="text-nowrap">
                                    {{ $order->cancelled_at ? $order->cancelled_at->format('H:i') : '-' }}
                                    <small class="d-block text-muted">{{ $order->cancelled_at ? $order->cancelled_at->format('M d') : '' }}</small>
                                </td>
                                <td>
                                    <strong>{{ $order->service_specific_data['item_name'] ?? $order->service->name ?? 'Item' }}</strong>
                                </td>
                                <td>
                                    @if($order->is_walk_in)
                                        <span class="badge badge-secondary badge-sm">Walk-in</span>
                                        {{ str_ireplace('Walk-in ', '', $order->walk_in_name ?? '') }}
                                    @elseif($order->booking)
                                        <span class="badge badge-primary badge-sm">Room {{ $order->booking->room->room_number ?? 'N/A' }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $order->quantity }}</td>
                                <td class="text-right font-weight-bold text-danger">{{ number_format($order->total_price_tsh) }}</td>
                                @if($isManager)
                                <td>
                                    <span class="badge badge-dark">{{ $order->cancelledBy->name ?? 'N/A' }}</span>
                                </td>
                                @endif
                                <td>
                                    @if($order->cancellation_reason)
                                        <span class="text-danger" style="font-size:12px;">
                                            <i class="fa fa-exclamation-circle"></i>
                                            {{ $order->cancellation_reason }}
                                        </span>
                                    @else
                                        <span class="text-muted small">Sababu haikuwekwa</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot style="background: #fde8e8;">
                            <tr>
                                <td colspan="{{ $isManager ? 4 : 3 }}" class="text-right font-weight-bold text-danger">Total Cancelled Value:</td>
                                <td class="text-right font-weight-bold text-danger">
                                    {{ number_format($cancelledOrders->sum('total_price_tsh')) }} TZS
                                </td>
                                <td colspan="{{ $isManager ? 2 : 1 }}"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .widget-small { border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,.06); transition: transform .2s; }
    .widget-small:hover { transform: translateY(-4px); }
    .text-teal { color: #009688; }
    .badge-sm { font-size: 10px; }
    .table tfoot td { border-top: 2px solid #dee2e6 !important; }
    .tile { border-radius: 8px; }
</style>
@endsection
