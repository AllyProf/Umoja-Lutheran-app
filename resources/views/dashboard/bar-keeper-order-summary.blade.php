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
            <form method="GET" action="{{ route('bar-keeper.order-summary') }}" class="form-inline flex-wrap" id="orderSummaryFilterForm" style="gap:8px;">
                <div class="form-group mr-2">
                    <label class="mr-2 font-weight-bold">Period:</label>
                    <select name="date_type" id="orderSummaryDateType" class="form-control form-control-sm" onchange="onOrderSummaryPeriodChange(this)">
                        <option value="daily"   {{ $dateType === 'daily'   ? 'selected' : '' }}>Daily</option>
                        <option value="weekly"  {{ $dateType === 'weekly'  ? 'selected' : '' }}>Weekly</option>
                        <option value="monthly" {{ $dateType === 'monthly' ? 'selected' : '' }}>Monthly</option>
                    </select>
                </div>
                <div class="form-group mr-2">
                    <label class="mr-2 font-weight-bold">{{ $dateType === 'weekly' ? 'Week of:' : ($dateType === 'monthly' ? 'Month of:' : 'Date:') }}</label>
                    <input type="date" name="date" id="orderSummaryDate" class="form-control form-control-sm"
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
            <script>
                function onOrderSummaryPeriodChange(sel) {
                    const dateInput = document.getElementById('orderSummaryDate');
                    const today = new Date();
                    const pad = (n) => String(n).padStart(2, '0');
                    const toYmd = (d) => d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());

                    if (sel.value === 'weekly') {
                        // Monday of the previous full week
                        const d = new Date(today.getFullYear(), today.getMonth(), today.getDate());
                        const day = d.getDay(); // 0=Sun ... 6=Sat
                        const toMonday = day === 0 ? 6 : day - 1;
                        d.setDate(d.getDate() - toMonday - 7);
                        dateInput.value = toYmd(d);
                    } else if (sel.value === 'daily') {
                        dateInput.value = toYmd(today);
                    } else if (sel.value === 'monthly') {
                        const d = new Date(today.getFullYear(), today.getMonth(), 1);
                        dateInput.value = toYmd(d);
                    }
                    sel.form.submit();
                }
            </script>
        </div>
    </div>
</div>

{{-- ── Summary Cards ── --}}
<div class="row mb-3">
    <div class="col-6 col-md-2">
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
    <div class="col-6 col-md-2">
        <div class="widget-small info coloured-icon">
            <i class="icon fa fa-check-circle fa-3x"></i>
            <div class="info">
                <h4>Paid</h4>
                <p><b>{{ number_format($summary['paid_revenue']) }} TZS</b></p>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="widget-small warning coloured-icon">
            <i class="icon fa fa-exclamation-triangle fa-3x"></i>
            <div class="info">
                <h4>Unpaid</h4>
                <p><b>{{ number_format($summary['pending_revenue'] ?? 0) }} TZS</b></p>
                <small>{{ number_format($summary['unpaid_guests'] ?? 0) }} guests</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="widget-small warning coloured-icon">
            <i class="icon fa fa-circle text-danger fa-3x" style="animation: pulse-live 1.4s infinite;"></i>
            <div class="info">
                <h4>Live Now</h4>
                <p><b>{{ number_format($summary['live_counters'] ?? 0) }}</b></p>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="widget-small danger coloured-icon">
            <i class="icon fa fa-times-circle fa-3x"></i>
            <div class="info">
                <h4>Cancelled</h4>
                <p><b>{{ number_format($summary['total_cancelled']) }} orders</b></p>
            </div>
        </div>
    </div>
</div>

{{-- ── Unpaid Guests ── --}}
<div class="row mb-4">
    <div class="col-md-12">
        <div class="tile shadow-sm" style="border-top: 4px solid #f0ad4e;">
            <div class="tile-title-w-btn">
                <h3 class="title" style="color:#d58512;">
                    <i class="fa fa-user-times mr-1"></i> Unpaid Guests
                    <span class="badge badge-warning ml-2">{{ count($unpaidGuests ?? []) }}</span>
                </h3>
                <span class="badge badge-warning">
                    Due: {{ number_format($summary['pending_revenue'] ?? 0) }} TZS
                </span>
            </div>

            @if(empty($unpaidGuests))
                <div class="text-center py-4 text-muted">
                    <i class="fa fa-check-circle fa-2x text-success mb-2"></i>
                    <p>Hakuna mgeni aliyebaki kulipa kwa kipindi hiki.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-sm table-hover table-bordered mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Guest / Room</th>
                                <th>Items</th>
                                <th class="text-center">Orders</th>
                                <th class="text-center">Qty</th>
                                <th class="text-right">Amount Due</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($unpaidGuests as $i => $guest)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td><strong>{{ $guest['guest'] }}</strong></td>
                                    <td>
                                        @foreach(($guest['items'] ?? []) as $item)
                                            <div class="small">
                                                <span class="text-muted">{{ $item['time'] ?? '' }}</span>
                                                {{ $item['item'] ?? 'Item' }}
                                                <span class="text-muted">×{{ $item['qty'] ?? 0 }}</span>
                                                — {{ number_format($item['amount'] ?? 0) }}
                                            </div>
                                        @endforeach
                                    </td>
                                    <td class="text-center">{{ $guest['orders'] }}</td>
                                    <td class="text-center">{{ $guest['qty'] }}</td>
                                    <td class="text-right font-weight-bold text-warning">
                                        {{ number_format($guest['amount']) }} TZS
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" class="text-right font-weight-bold">Total unpaid:</td>
                                <td class="text-right font-weight-bold text-warning">
                                    {{ number_format($summary['pending_revenue'] ?? 0) }} TZS
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- ── Past closed shifts (download reports anytime) ── --}}
@if(isset($pastShifts) && $pastShifts->count() > 0)
<div class="row mb-4">
    <div class="col-md-12">
        <div class="tile shadow-sm">
            <div class="tile-title-w-btn">
                <h3 class="title"><i class="fa fa-file-pdf-o text-danger"></i> Closed Shift Reports</h3>
                <span class="badge badge-secondary">{{ $pastShifts->count() }} shifts</span>
            </div>
            <p class="text-muted small mb-3">
                Download any closed-shift report here if you did not take it when closing the shift.
            </p>
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover mb-0 sales-history-shifts">
                    <thead>
                        <tr>
                            @if($isManager)<th>Counter</th>@endif
                            <th>Opened</th>
                            <th>Closed</th>
                            <th>Duration</th>
                            <th class="text-right">Cash</th>
                            <th class="text-right">M-Pesa</th>
                            <th class="text-right">Total Sales</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pastShifts as $shift)
                            @php
                                $opened = $shift->opened_at ?? $shift->created_at;
                                $closed = $shift->closed_at;
                                $mins = ($opened && $closed) ? $opened->diffInMinutes($closed) : 0;
                                $hours = intdiv($mins, 60);
                                $rem = $mins % 60;
                                $duration = $hours > 0 ? ($hours.'h'.($rem ? ' '.$rem.'m' : '')) : ($rem.'m');
                                $totalSales = (float) $shift->total_cash_tzs + (float) $shift->total_mpesa_tzs + (float) $shift->total_other_tzs;
                            @endphp
                            <tr>
                                @if($isManager)
                                    <td><strong>{{ $shift->staff->name ?? 'Counter' }}</strong></td>
                                @endif
                                <td>{{ $opened ? $opened->format('d M Y H:i') : '—' }}</td>
                                <td>{{ $closed ? $closed->format('d M Y H:i') : '—' }}</td>
                                <td>{{ $duration }}</td>
                                <td class="text-right">{{ number_format($shift->total_cash_tzs) }}</td>
                                <td class="text-right">{{ number_format($shift->total_mpesa_tzs) }}</td>
                                <td class="text-right font-weight-bold">{{ number_format($totalSales) }}</td>
                                <td class="text-nowrap">
                                    <a href="{{ route('bar-keeper.shifts.report', $shift->id, false) }}"
                                       class="btn btn-sm btn-primary">
                                        <i class="fa fa-download"></i> PDF
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ── Per-Counter Breakdown ── --}}
@if($byCounter->isEmpty())
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="tile shadow-sm text-center py-5">
                <i class="fa fa-coffee fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">Hakuna mauzo wala shift live kwa kipindi hiki.</h4>
                <p class="text-muted mb-0">Unaweza bado kuona na kudownload ripoti za shift zilizofungwa hapo juu.</p>
            </div>
        </div>
    </div>
@else
    @foreach($byCounter as $counterId => $data)
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="tile shadow-sm counter-card {{ !empty($data['is_live']) ? 'is-live' : 'is-closed' }}">
                <div class="tile-title-w-btn flex-wrap" style="gap:10px;">
                    <div>
                        <h3 class="title mb-1">
                            <i class="fa fa-user-circle-o text-teal mr-1"></i>
                            Counter: <strong>{{ $data['counter_name'] }}</strong>
                            @if(!empty($data['is_live']))
                                <span class="badge badge-danger live-badge ml-2">
                                    <i class="fa fa-circle"></i> LIVE
                                </span>
                            @else
                                <span class="badge badge-secondary ml-2">CLOSED</span>
                            @endif
                        </h3>
                        <div class="shift-meta text-muted small">
                            @if(!empty($data['opened_at']))
                                <span class="mr-3">
                                    <i class="fa fa-sign-in"></i>
                                    Opened: <strong>{{ $data['opened_at']->format('M d, H:i') }}</strong>
                                </span>
                            @endif
                            @if(!empty($data['is_live']))
                                <span class="mr-3 text-danger">
                                    <i class="fa fa-clock-o"></i>
                                    Running: <strong>{{ $data['duration_label'] ?? '-' }}</strong>
                                </span>
                            @elseif(!empty($data['closed_at']))
                                <span class="mr-3">
                                    <i class="fa fa-sign-out"></i>
                                    Closed: <strong>{{ $data['closed_at']->format('M d, H:i') }}</strong>
                                </span>
                                <span>
                                    <i class="fa fa-hourglass-half"></i>
                                    Duration: <strong>{{ $data['duration_label'] ?? '-' }}</strong>
                                </span>
                            @else
                                <span>Shift details not available for this period.</span>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex flex-wrap align-items-center" style="gap:6px;">
                        @if($isManager && !empty($data['is_live']) && !empty($data['shift_id']))
                            <button type="button"
                                    class="btn btn-sm btn-outline-danger js-force-close-shift"
                                    data-shift-id="{{ $data['shift_id'] }}"
                                    data-counter-name="{{ $data['counter_name'] }}"
                                    data-cash="{{ $data['paid_revenue'] }}"
                                    title="Close shift for counter who forgot">
                                <i class="fa fa-lock"></i> Force Close Shift
                            </button>
                        @endif
                        @if(!empty($data['closed_shift_id']))
                            <a href="{{ route('bar-keeper.shifts.report', $data['closed_shift_id'], false) }}"
                               class="btn btn-sm btn-outline-primary"
                               title="Download shift PDF report">
                                <i class="fa fa-download"></i> Download Report
                            </a>
                        @endif
                        <span class="badge badge-success">
                            Paid: {{ number_format($data['paid_revenue']) }} TZS
                        </span>
                        @if($data['pending_revenue'] > 0)
                            <span class="badge badge-warning">
                                Pending: {{ number_format($data['pending_revenue']) }} TZS
                            </span>
                        @endif
                        <span class="badge badge-primary">
                            {{ $data['total_orders'] }} orders | Total: {{ number_format($data['total_revenue']) }} TZS
                        </span>
                    </div>
                </div>

                @if($data['orders']->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="fa fa-info-circle"></i> Shift is live — hakuna mauzo bado.
                    </div>
                @else
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
                        <tfoot class="order-subtotal">
                            <tr>
                                <td colspan="5" class="text-right font-weight-bold">Sub-total:</td>
                                <td class="text-right font-weight-bold">{{ number_format($data['total_revenue']) }} TZS</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @endif
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
                        <tfoot class="order-cancelled-total">
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
    .order-subtotal td {
        background: #e0f2f1 !important;
        color: #004d40 !important;
    }
    .order-cancelled-total td {
        background: #fde8e8 !important;
        color: #b71c1c !important;
    }
    html[data-theme="dark"] .order-subtotal td {
        background: #12332f !important;
        color: #b2dfdb !important;
    }
    html[data-theme="dark"] .order-cancelled-total td {
        background: #3a1518 !important;
        color: #ff8a80 !important;
    }
    html[data-theme="dark"] .sales-history-shifts th,
    html[data-theme="dark"] .sales-history-shifts td {
        background: #0d1117 !important;
        color: #e6edf3 !important;
        border-color: #30363d !important;
    }
    html[data-theme="dark"] .sales-history-shifts thead th {
        background: #21262d !important;
        color: #f0f6fc !important;
    }
    .tile { border-radius: 8px; }
    .counter-card.is-live {
        border-top: 4px solid #dc3545;
        box-shadow: 0 0 0 1px rgba(220,53,69,.15), 0 8px 20px rgba(220,53,69,.08);
    }
    .counter-card.is-closed { border-top: 4px solid #6c757d; }
    .live-badge {
        animation: pulse-live 1.4s infinite;
        letter-spacing: .4px;
    }
    .live-badge .fa-circle { font-size: 8px; vertical-align: middle; margin-right: 4px; }
    @keyframes pulse-live {
        0% { box-shadow: 0 0 0 0 rgba(220,53,69,.55); }
        70% { box-shadow: 0 0 0 8px rgba(220,53,69,0); }
        100% { box-shadow: 0 0 0 0 rgba(220,53,69,0); }
    }
    @keyframes pulse-dot {
        0%, 100% { opacity: 1; }
        50% { opacity: .35; }
    }
    .shift-meta { margin-top: 4px; }
</style>
<script>
    // Keep live durations fresh
    setTimeout(function () { window.location.reload(); }, 60000);

    document.querySelectorAll('.js-force-close-shift').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var shiftId = btn.getAttribute('data-shift-id');
            var counterName = btn.getAttribute('data-counter-name') || 'this counter';

            Swal.fire({
                title: 'Force Close Shift',
                html: 'Close shift for <strong>' + counterName + '</strong>?<br><small class="text-muted">Use this when the counter forgot to close after shift ended.</small>',
                input: 'text',
                inputLabel: 'Reason (optional)',
                inputValue: 'Forgot to close shift',
                inputPlaceholder: 'e.g. Forgot to close after shift ended',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, close shift',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#dc3545',
                reverseButtons: true,
                inputValidator: function () {
                    return null; // reason is optional
                }
            }).then(function (result) {
                if (!result.isConfirmed) return;

                var reason = (result.value || '').trim() || 'Forgot to close shift';
                btn.disabled = true;
                btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Closing...';

                Swal.fire({
                    title: 'Closing shift...',
                    allowOutsideClick: false,
                    didOpen: function () { Swal.showLoading(); }
                });

                fetch('{{ url('/bar-keeper/shifts') }}/' + shiftId + '/force-close', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ notes: reason })
                })
                .then(function (res) { return res.json().then(function (data) { return { ok: res.ok, data: data }; }); })
                .then(function (result) {
                    if (!result.ok || !result.data.success) {
                        throw new Error((result.data && result.data.message) || 'Failed to close shift');
                    }
                    return Swal.fire({
                        icon: 'success',
                        title: 'Shift Closed',
                        text: result.data.message || 'Shift closed successfully.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                })
                .then(function () {
                    window.location.reload();
                })
                .catch(function (err) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed',
                        text: err.message || 'Could not close shift.'
                    });
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa fa-lock"></i> Force Close Shift';
                });
            });
        });
    });
</script>
@endsection
