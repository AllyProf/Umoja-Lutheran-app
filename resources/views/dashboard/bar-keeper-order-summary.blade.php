@extends('dashboard.layouts.app')

@section('content')
@php
    $liveCards = $liveCards ?? collect();
    $unpaidGuests = $unpaidGuests ?? [];
@endphp

<div class="app-title">
    <div>
        <h1><i class="fa fa-bar-chart"></i> Order Summary</h1>
        <p>{{ $isManager ? 'Live counters, period sales, and unpaid guests' : 'Your sales, unpaid guests, and cancellations' }}</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route('bar-keeper.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item">Order Summary</li>
    </ul>
</div>

{{-- Filter --}}
<div class="tile shadow-sm os-filter mb-3">
    <form method="GET" action="{{ route('bar-keeper.order-summary') }}" class="os-filter-form" id="orderSummaryFilterForm">
        <div class="os-filter-group">
            <label>Period</label>
            <select name="date_type" id="orderSummaryDateType" class="form-control form-control-sm" onchange="onOrderSummaryPeriodChange(this)">
                <option value="daily"   {{ $dateType === 'daily'   ? 'selected' : '' }}>Daily</option>
                <option value="weekly"  {{ $dateType === 'weekly'  ? 'selected' : '' }}>Weekly</option>
                <option value="monthly" {{ $dateType === 'monthly' ? 'selected' : '' }}>Monthly</option>
            </select>
        </div>
        <div class="os-filter-group">
            <label>{{ $dateType === 'weekly' ? 'Week of' : ($dateType === 'monthly' ? 'Month of' : 'Date') }}</label>
            <input type="date" name="date" id="orderSummaryDate" class="form-control form-control-sm"
                   value="{{ $date->toDateString() }}" onchange="this.form.submit()">
        </div>
        <div class="os-filter-actions">
            <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-search"></i> Filter</button>
            <a href="{{ route('bar-keeper.order-summary') }}" class="btn btn-sm btn-outline-secondary"><i class="fa fa-refresh"></i> Today</a>
        </div>
        <div class="os-filter-range">
            Showing <strong>{{ $startDate->format('d M Y') }}</strong>
            @if(!$startDate->isSameDay($endDate))
                – <strong>{{ $endDate->format('d M Y') }}</strong>
            @endif
        </div>
    </form>
    <script>
        function onOrderSummaryPeriodChange(sel) {
            const dateInput = document.getElementById('orderSummaryDate');
            const today = new Date();
            const pad = (n) => String(n).padStart(2, '0');
            const toYmd = (d) => d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());

            if (sel.value === 'weekly') {
                const d = new Date(today.getFullYear(), today.getMonth(), today.getDate());
                const day = d.getDay();
                const toMonday = day === 0 ? 6 : day - 1;
                d.setDate(d.getDate() - toMonday - 7);
                dateInput.value = toYmd(d);
            } else if (sel.value === 'daily') {
                dateInput.value = toYmd(today);
            } else if (sel.value === 'monthly') {
                dateInput.value = toYmd(new Date(today.getFullYear(), today.getMonth(), 1));
            }
            sel.form.submit();
        }
    </script>
</div>

{{-- KPIs --}}
<div class="row os-kpis mb-3">
    <div class="col-6 col-md-2">
        <div class="os-kpi">
            <span class="os-kpi-label">Period orders</span>
            <span class="os-kpi-value">{{ number_format($summary['total_sold']) }}</span>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="os-kpi">
            <span class="os-kpi-label">Period sales</span>
            <span class="os-kpi-value">{{ number_format($summary['total_revenue']) }}</span>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="os-kpi os-kpi-ok">
            <span class="os-kpi-label">Paid</span>
            <span class="os-kpi-value">{{ number_format($summary['paid_revenue']) }}</span>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="os-kpi os-kpi-warn">
            <span class="os-kpi-label">Unpaid</span>
            <span class="os-kpi-value">{{ number_format($summary['pending_revenue'] ?? 0) }}</span>
            <span class="os-kpi-sub">{{ number_format($summary['unpaid_guests'] ?? 0) }} guests</span>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="os-kpi os-kpi-live">
            <span class="os-kpi-label">Live now</span>
            <span class="os-kpi-value">{{ number_format($summary['live_counters'] ?? 0) }}</span>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="os-kpi os-kpi-danger">
            <span class="os-kpi-label">Cancelled</span>
            <span class="os-kpi-value">{{ number_format($summary['total_cancelled']) }}</span>
        </div>
    </div>
</div>

{{-- LIVE NOW (separate from period filter) --}}
@if($liveCards->isNotEmpty())
<div class="tile shadow-sm os-section os-live-section mb-3">
    <div class="os-section-head">
        <h3><i class="fa fa-circle text-danger"></i> Live Now</h3>
        <span class="text-muted small">Current open shifts only — not mixed with weekly/monthly totals</span>
    </div>
    @foreach($liveCards as $data)
        @include('dashboard.partials.order-summary-shift-card', ['data' => $data, 'isManager' => $isManager])
    @endforeach
</div>
@endif

{{-- Unpaid --}}
<div class="tile shadow-sm os-section mb-3">
    <div class="os-section-head">
        <h3><i class="fa fa-user-times text-warning"></i> Unpaid Guests
            <span class="badge badge-warning">{{ count($unpaidGuests) }}</span>
        </h3>
        <div class="os-section-actions">
            <span class="badge badge-light border">Due: {{ number_format($summary['pending_revenue'] ?? 0) }} TZS</span>
            <a href="{{ route('bar-keeper.order-summary.unpaid-report', request()->only(['date', 'date_type'])) }}"
               class="btn btn-sm btn-warning">
                <i class="fa fa-download"></i> PDF
            </a>
        </div>
    </div>
    @if(empty($unpaidGuests))
        <p class="os-empty mb-0">Hakuna mgeni anayedaiwa katika kipindi hiki.</p>
    @else
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0 os-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Guest / Room</th>
                        <th>Items</th>
                        <th class="text-center">Orders</th>
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
                                    <div class="os-item-line">
                                        <span class="text-muted">{{ $item['time'] ?? '' }}</span>
                                        {{ $item['item'] ?? 'Item' }} ×{{ $item['qty'] ?? 0 }}
                                        — {{ number_format($item['amount'] ?? 0) }}
                                    </div>
                                @endforeach
                            </td>
                            <td class="text-center">{{ $guest['orders'] }}</td>
                            <td class="text-right font-weight-bold">{{ number_format($guest['amount']) }} TZS</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{-- Closed shift PDFs --}}
@if(isset($pastShifts) && $pastShifts->count() > 0)
<div class="tile shadow-sm os-section mb-3">
    <div class="os-section-head">
        <h3><i class="fa fa-file-pdf-o text-danger"></i> Closed Shift Reports</h3>
        <span class="badge badge-secondary">{{ $pastShifts->count() }}</span>
    </div>
    <div class="table-responsive">
        <table class="table table-sm table-hover mb-0 os-table sales-history-shifts">
            <thead>
                <tr>
                    @if($isManager)<th>Counter</th>@endif
                    <th>Opened</th>
                    <th>Closed</th>
                    <th>Duration</th>
                    <th class="text-right">Total</th>
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
                        <td>{{ $opened ? $opened->format('d M H:i') : '—' }}</td>
                        <td>{{ $closed ? $closed->format('d M H:i') : '—' }}</td>
                        <td>{{ $duration }}</td>
                        <td class="text-right font-weight-bold">{{ number_format($totalSales) }}</td>
                        <td class="text-nowrap">
                            <a href="{{ route('bar-keeper.shifts.report', $shift->id, false) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fa fa-download"></i> PDF
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- Period sales by closed shift --}}
<div class="tile shadow-sm os-section mb-3">
    <div class="os-section-head">
        <h3><i class="fa fa-list-alt"></i> Period Sales
            <span class="badge badge-primary">{{ $byCounter->count() }}</span>
        </h3>
        <span class="text-muted small">Closed shifts / sales inside the selected date range only</span>
    </div>

    @if($byCounter->isEmpty())
        <p class="os-empty mb-0">Hakuna mauzo ya kipindi hiki.</p>
    @else
        @foreach($byCounter as $data)
            @include('dashboard.partials.order-summary-shift-card', ['data' => $data, 'isManager' => $isManager])
        @endforeach
    @endif
</div>

{{-- Cancelled --}}
<div class="tile shadow-sm os-section mb-3">
    <div class="os-section-head">
        <h3><i class="fa fa-ban text-danger"></i> Cancelled
            <span class="badge badge-danger">{{ $cancelledOrders->count() }}</span>
        </h3>
    </div>
    @if($cancelledOrders->isEmpty())
        <p class="os-empty mb-0">Hakuna order zilizofutwa kwa kipindi hiki.</p>
    @else
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0 os-table">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Item</th>
                        <th>Guest / Room</th>
                        <th class="text-center">Qty</th>
                        <th class="text-right">Value</th>
                        @if($isManager)<th>By</th>@endif
                        <th>Reason</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cancelledOrders as $order)
                        <tr>
                            <td class="text-nowrap">
                                {{ $order->cancelled_at ? $order->cancelled_at->format('H:i') : '-' }}
                                <small class="d-block text-muted">{{ $order->cancelled_at ? $order->cancelled_at->format('d M') : '' }}</small>
                            </td>
                            <td><strong>{{ $order->service_specific_data['item_name'] ?? $order->service->name ?? 'Item' }}</strong></td>
                            <td>
                                @if($order->is_walk_in)
                                    Walk-in: {{ str_ireplace('Walk-in ', '', $order->walk_in_name ?? '') }}
                                @elseif($order->booking)
                                    Room {{ $order->booking->room->room_number ?? 'N/A' }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="text-center">{{ $order->quantity }}</td>
                            <td class="text-right text-danger font-weight-bold">{{ number_format($order->total_price_tsh) }}</td>
                            @if($isManager)
                                <td>{{ $order->cancelledBy->name ?? 'N/A' }}</td>
                            @endif
                            <td class="small">{{ $order->cancellation_reason ?: '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<style>
    .os-filter { padding: 14px 16px; border-radius: 10px; }
    .os-filter-form { display: flex; flex-wrap: wrap; gap: 12px; align-items: end; }
    .os-filter-group label { display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; color: #6c757d; margin-bottom: 4px; }
    .os-filter-group .form-control { min-width: 140px; }
    .os-filter-actions { display: flex; gap: 6px; }
    .os-filter-range { margin-left: auto; font-size: 13px; color: #495057; padding-bottom: 4px; }

    .os-kpi { background: #fff; border: 1px solid #e9ecef; border-radius: 10px; padding: 12px 14px; height: 100%; }
    .os-kpi-label { display: block; font-size: 11px; text-transform: uppercase; letter-spacing: .04em; color: #6c757d; margin-bottom: 4px; }
    .os-kpi-value { display: block; font-size: 18px; font-weight: 700; color: #212529; line-height: 1.2; }
    .os-kpi-sub { display: block; font-size: 11px; color: #868e96; margin-top: 2px; }
    .os-kpi-ok .os-kpi-value { color: #2e7d32; }
    .os-kpi-warn .os-kpi-value { color: #b26a00; }
    .os-kpi-live .os-kpi-value { color: #c62828; }
    .os-kpi-danger .os-kpi-value { color: #c62828; }

    .os-section { border-radius: 10px; padding: 14px 16px; }
    .os-section-head { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 12px; }
    .os-section-head h3 { font-size: 16px; margin: 0; font-weight: 700; }
    .os-section-actions { display: flex; align-items: center; gap: 8px; }
    .os-live-section { border-left: 4px solid #dc3545; }
    .os-empty { color: #868e96; padding: 8px 0; }
    .os-item-line { font-size: 12px; line-height: 1.45; }

    .os-shift-card { border: 1px solid #e9ecef; border-radius: 8px; padding: 12px; margin-bottom: 12px; background: #fafbfc; }
    .os-shift-card:last-child { margin-bottom: 0; }
    .os-shift-card.is-live { border-color: #f5c2c7; background: #fff8f8; }
    .os-shift-card.is-orphan { border-style: dashed; }
    .os-shift-top { display: flex; flex-wrap: wrap; justify-content: space-between; gap: 10px; margin-bottom: 8px; }
    .os-shift-title { font-size: 15px; font-weight: 700; margin: 0 0 4px; }
    .os-shift-meta { font-size: 12px; color: #6c757d; }
    .os-shift-meta span { margin-right: 12px; }
    .os-shift-badges { display: flex; flex-wrap: wrap; gap: 6px; align-items: center; }
    .os-table th { font-size: 11px; text-transform: uppercase; letter-spacing: .03em; color: #6c757d; border-top: 0; }
    .os-table td { vertical-align: top; }
    .os-orders-wrap { max-height: 280px; overflow: auto; border-top: 1px solid #e9ecef; margin-top: 8px; }
    .live-badge { animation: pulse-live 1.4s infinite; }
    .live-badge .fa-circle { font-size: 8px; margin-right: 4px; }
    @keyframes pulse-live {
        0% { box-shadow: 0 0 0 0 rgba(220,53,69,.45); }
        70% { box-shadow: 0 0 0 8px rgba(220,53,69,0); }
        100% { box-shadow: 0 0 0 0 rgba(220,53,69,0); }
    }

    html[data-theme="dark"] .os-kpi,
    html[data-theme="dark"] .os-shift-card { background: #161b22; border-color: #30363d; }
    html[data-theme="dark"] .os-kpi-value { color: #e6edf3; }
    html[data-theme="dark"] .os-shift-card.is-live { background: #2a1215; border-color: #7f1d1d; }
    html[data-theme="dark"] .os-orders-wrap { border-color: #30363d; }
    html[data-theme="dark"] .sales-history-shifts th,
    html[data-theme="dark"] .sales-history-shifts td {
        background: #0d1117 !important; color: #e6edf3 !important; border-color: #30363d !important;
    }
</style>

<script>
    setTimeout(function () { window.location.reload(); }, 60000);

    document.querySelectorAll('.js-force-close-shift').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var shiftId = btn.getAttribute('data-shift-id');
            var counterName = btn.getAttribute('data-counter-name') || 'this counter';

            Swal.fire({
                title: 'Force Close Shift',
                html: 'Close shift for <strong>' + counterName + '</strong>?',
                input: 'text',
                inputLabel: 'Reason (optional)',
                inputValue: 'Forgot to close shift',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, close shift',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#dc3545',
                reverseButtons: true
            }).then(function (result) {
                if (!result.isConfirmed) return;
                var reason = (result.value || '').trim() || 'Forgot to close shift';
                btn.disabled = true;
                Swal.fire({ title: 'Closing shift...', allowOutsideClick: false, didOpen: function () { Swal.showLoading(); } });

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
                    if (!result.ok || !result.data.success) throw new Error((result.data && result.data.message) || 'Failed');
                    return Swal.fire({ icon: 'success', title: 'Shift Closed', text: result.data.message || 'Done', timer: 1800, showConfirmButton: false });
                })
                .then(function () { window.location.reload(); })
                .catch(function (err) {
                    Swal.fire({ icon: 'error', title: 'Failed', text: err.message || 'Could not close shift.' });
                    btn.disabled = false;
                });
            });
        });
    });
</script>
@endsection
