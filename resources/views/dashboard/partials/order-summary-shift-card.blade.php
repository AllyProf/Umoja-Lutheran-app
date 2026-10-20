@php
    $isLive = !empty($data['is_live']);
    $isOrphan = !empty($data['is_orphan']);
    $premium = !empty($premium);
    $isCashierCtx = str_starts_with(optional(request()->route())->getName() ?? '', 'cashier.');
    $shiftPdfRoute = $isCashierCtx ? 'cashier.shift-reports.pdf' : 'bar-keeper.shifts.report';
@endphp

@if($premium)
<div class="osx-shift {{ $isLive ? 'is-live' : '' }}">
    <div class="osx-shift-toggle js-osx-shift-toggle" role="button" tabindex="0">
        <div>
            <h4 class="osx-shift-title">
                {{ $data['counter_name'] }}
                @if($isLive)
                    <span class="osx-pill is-live live-badge"><i class="fa fa-circle"></i> LIVE</span>
                @elseif($isOrphan)
                    <span class="osx-pill is-muted">Other sales</span>
                @else
                    <span class="osx-pill is-muted">CLOSED</span>
                @endif
            </h4>
            <div class="osx-shift-meta">
                @if(!empty($data['opened_at']))
                    <span><i class="fa fa-sign-in"></i> {{ $data['opened_at']->format('d M H:i') }}</span>
                @endif
                @if($isLive)
                    <span class="text-danger"><i class="fa fa-clock-o"></i> Running {{ $data['duration_label'] ?? '—' }}</span>
                @elseif(!empty($data['closed_at']))
                    <span><i class="fa fa-sign-out"></i> {{ $data['closed_at']->format('d M H:i') }}</span>
                    <span><i class="fa fa-hourglass-half"></i> {{ $data['duration_label'] ?? '—' }}</span>
                @elseif($isOrphan)
                    <span>Sales without a closed-shift link</span>
                @endif
            </div>
        </div>
        <div class="osx-shift-stats">
            @if($isManager && !$isCashierCtx && $isLive && !empty($data['shift_id']))
                <button type="button"
                        class="btn btn-sm btn-outline-danger js-force-close-shift"
                        style="border-radius:8px;"
                        data-shift-id="{{ $data['shift_id'] }}"
                        data-counter-name="{{ $data['counter_name'] }}">
                    <i class="fa fa-lock"></i> Force Close
                </button>
            @endif
            @if(!empty($data['closed_shift_id']))
                <a href="{{ route($shiftPdfRoute, $data['closed_shift_id'], false) }}"
                   class="btn btn-sm btn-outline-primary"
                   style="border-radius:8px;"
                   onclick="event.stopPropagation();"
                   target="_blank">
                    <i class="fa fa-print"></i> PDF
                </a>
            @endif
            <span class="osx-pill is-ok">Counter paid {{ number_format($data['paid_revenue']) }}</span>
            @if(($data['pending_revenue'] ?? 0) > 0)
                <span class="osx-pill is-warn">Unpaid {{ number_format($data['pending_revenue']) }}</span>
            @endif
            @if(($data['reception_unpaid'] ?? 0) > 0)
                <span class="osx-pill osx-pill-reception">Reception unpaid {{ number_format($data['reception_unpaid']) }}</span>
            @endif
            @if(($data['reception_paid'] ?? 0) > 0)
                <span class="osx-pill osx-pill-reception">Reception paid (not counter)</span>
            @endif
            <span class="osx-pill">{{ $data['total_orders'] }} orders</span>
            <i class="fa fa-chevron-down osx-chevron"></i>
        </div>
    </div>

    <div class="osx-shift-body">
        @if($data['orders']->isEmpty())
            <div class="osx-empty">{{ $isLive ? 'Shift is live — no sales yet.' : 'No orders on this card.' }}</div>
        @else
            @php
                $groupedOrders = $data['orders']->groupBy(function ($item) {
                    if ($item->is_walk_in) {
                        return 'w_' . strtolower(trim((string) ($item->walk_in_name ?? 'general')));
                    }
                    return 'b_' . ($item->booking_id ?? 'unknown');
                });
            @endphp
            <div class="table-responsive">
                <table class="table table-sm mb-0 osx-table">
                    <thead>
                        <tr>
                            <th>Guest</th>
                            <th>Items</th>
                            <th class="text-center">Qty</th>
                            <th class="text-right">Total</th>
                            <th>Pay Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($groupedOrders as $orderGroup)
                            @php
                                $first = $orderGroup->first();
                                $latest = $orderGroup->sortByDesc(fn ($o) => $o->completed_at ?? $o->requested_at)->first();
                                $groupTotal = (float) $orderGroup->sum('total_price_tsh');
                                $groupQty = (float) $orderGroup->sum('quantity');
                                $hasPriorDebt = $orderGroup->contains(fn ($o) => $o->wasPaidFromPriorDebt());
                                $allCounterPaid = $orderGroup->every(fn ($o) => $o->countsAsCounterCash());
                                $allReceptionUnpaid = $orderGroup->every(fn ($o) => $o->payment_status === 'room_charge');
                                $allReceptionPaid = $orderGroup->every(fn ($o) => $o->wasPaidAtReception());
                                $hasReceptionUnpaid = $orderGroup->contains(fn ($o) => $o->payment_status === 'room_charge');
                                $hasReceptionPaid = $orderGroup->contains(fn ($o) => $o->wasPaidAtReception());
                                $hasPending = $orderGroup->contains(fn ($o) => $o->payment_status === 'pending');
                                $counterCashTotal = (float) $orderGroup->filter(fn ($o) => $o->countsAsCounterCash())->sum('total_price_tsh');
                                $guestLabel = $first->is_walk_in
                                    ? str_ireplace('Walk-in ', '', $first->walk_in_name ?? 'Walk-in')
                                    : ('Rm ' . ($first->booking?->room?->room_number ?? 'N/A') . ($first->booking?->guest_name ? ' — ' . $first->booking->guest_name : ''));
                            @endphp
                            <tr class="osx-guest-group {{ $hasPriorDebt ? 'row-prior-debt' : '' }}">
                                <td style="vertical-align: top; min-width: 140px;">
                                    <strong>{{ $guestLabel }}</strong>
                                    <div class="small text-muted">
                                        {{ optional($latest->completed_at ?? $latest->requested_at)->format('d M H:i') }}
                                        · {{ $orderGroup->count() }} item(s)
                                    </div>
                                    @if($first->is_walk_in)
                                        <span class="osx-pill is-muted" style="font-size:9px;">WALK-IN</span>
                                    @endif
                                    @if($hasReceptionUnpaid || $hasReceptionPaid)
                                        <div class="mt-1"><span class="osx-pill osx-pill-reception" style="font-size:9px;">PAY AT RECEPTION</span></div>
                                    @endif
                                    @if($hasPriorDebt)
                                        <div class="mt-1"><span class="osx-pill is-warn" style="font-size:9px;">From prior debt</span></div>
                                    @endif
                                </td>
                                <td style="vertical-align: top;">
                                    @foreach($orderGroup->sortBy(fn ($o) => $o->completed_at ?? $o->requested_at) as $order)
                                        @php $fromPriorDebt = $order->wasPaidFromPriorDebt(); @endphp
                                        <div class="osx-item-line d-flex justify-content-between align-items-start" style="gap:8px; margin-bottom:4px;">
                                            <div>
                                                <span class="t">{{ optional($order->completed_at ?? $order->requested_at)->format('H:i') }}</span>
                                                {{ $order->service_specific_data['item_name'] ?? $order->service->name ?? 'Item' }}
                                                @if(!empty($order->service_specific_data['placed_by_name']))
                                                    <br><small class="text-info">Placed by: {{ $order->service_specific_data['placed_by_name'] }}</small>
                                                @endif
                                                ×{{ $order->quantity }}
                                                @if($fromPriorDebt && !$order->wasPaidAtReception())
                                                    <span class="osx-pill is-warn" style="font-size:8px;">Debt</span>
                                                @endif
                                            </div>
                                            <div class="text-nowrap">
                                                @if($order->wasPaidAtReception())
                                                    <span class="osx-pill osx-pill-reception" style="font-size:8px;">Reception</span>
                                                    <span class="osx-pill is-ok" style="font-size:8px;">PAID</span>
                                                @elseif($order->payment_status === 'paid')
                                                    {{ number_format($order->total_price_tsh) }}
                                                    <span class="osx-pill is-ok" style="font-size:8px;">PAID</span>
                                                @elseif($order->payment_status === 'room_charge')
                                                    {{ number_format($order->total_price_tsh) }}
                                                    <span class="osx-pill osx-pill-reception" style="font-size:8px;">Reception</span>
                                                    <span class="osx-pill is-warn" style="font-size:8px;">UNPAID</span>
                                                @else
                                                    {{ number_format($order->total_price_tsh) }}
                                                    <span class="osx-pill is-warn" style="font-size:8px;">UNPAID</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </td>
                                <td class="text-center" style="vertical-align: top;">{{ $groupQty == (int) $groupQty ? (int) $groupQty : $groupQty }}</td>
                                <td class="text-right font-weight-bold" style="vertical-align: top;">
                                    @if($allReceptionPaid)
                                        <span class="small text-muted">—</span>
                                    @else
                                        {{ number_format($counterCashTotal + (float) $orderGroup->whereIn('payment_status', ['pending', 'room_charge'])->sum('total_price_tsh')) }}
                                    @endif
                                </td>
                                <td style="vertical-align: top;">
                                    @if($allCounterPaid)
                                        <span class="osx-pill is-ok">PAID</span>
                                    @elseif($allReceptionPaid)
                                        <span class="osx-pill osx-pill-reception">Pay at Reception</span>
                                        <div class="mt-1"><span class="osx-pill is-ok">PAID</span></div>
                                        <div class="small text-muted mt-1">Not counter cash</div>
                                    @elseif($allReceptionUnpaid)
                                        <span class="osx-pill osx-pill-reception">Pay at Reception</span>
                                        <div class="mt-1"><span class="osx-pill is-warn">UNPAID</span></div>
                                    @elseif($hasReceptionUnpaid || $hasReceptionPaid)
                                        @if($hasPending)
                                            <span class="osx-pill is-warn">UNPAID</span>
                                        @endif
                                        <div class="mt-1"><span class="osx-pill osx-pill-reception" style="font-size:9px;">+ Reception</span></div>
                                    @else
                                        <span class="osx-pill is-warn">UNPAID</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@else
{{-- Legacy compact card (fallback) --}}
<div class="os-shift-card {{ $isLive ? 'is-live' : '' }} {{ $isOrphan ? 'is-orphan' : '' }}">
    <div class="os-shift-top">
        <div>
            <h4 class="os-shift-title">
                {{ $data['counter_name'] }}
                @if($isLive)
                    <span class="badge badge-danger live-badge"><i class="fa fa-circle"></i> LIVE</span>
                @elseif($isOrphan)
                    <span class="badge badge-secondary">Other sales</span>
                @else
                    <span class="badge badge-secondary">CLOSED</span>
                @endif
            </h4>
            <div class="os-shift-meta">
                @if(!empty($data['opened_at']))
                    <span><i class="fa fa-sign-in"></i> {{ $data['opened_at']->format('d M H:i') }}</span>
                @endif
                @if($isLive)
                    <span class="text-danger"><i class="fa fa-clock-o"></i> Running {{ $data['duration_label'] ?? '—' }}</span>
                @elseif(!empty($data['closed_at']))
                    <span><i class="fa fa-sign-out"></i> {{ $data['closed_at']->format('d M H:i') }}</span>
                    <span><i class="fa fa-hourglass-half"></i> {{ $data['duration_label'] ?? '—' }}</span>
                @elseif($isOrphan)
                    <span>Sales in this period without a closed-shift link</span>
                @endif
            </div>
        </div>
        <div class="os-shift-badges">
            @if($isManager && !$isCashierCtx && $isLive && !empty($data['shift_id']))
                <button type="button"
                        class="btn btn-sm btn-outline-danger js-force-close-shift"
                        data-shift-id="{{ $data['shift_id'] }}"
                        data-counter-name="{{ $data['counter_name'] }}">
                    <i class="fa fa-lock"></i> Force Close
                </button>
            @endif
            @if(!empty($data['closed_shift_id']))
                <a href="{{ route($shiftPdfRoute, $data['closed_shift_id'], false) }}"
                   class="btn btn-sm btn-outline-primary"
                   target="_blank">
                    <i class="fa fa-print"></i> Report
                </a>
            @endif
            <span class="badge badge-success">Paid {{ number_format($data['paid_revenue']) }}</span>
            @if(($data['pending_revenue'] ?? 0) > 0)
                <span class="badge badge-warning">Pending {{ number_format($data['pending_revenue']) }}</span>
            @endif
            <span class="badge badge-primary">{{ $data['total_orders'] }} orders · {{ number_format($data['total_revenue']) }}</span>
        </div>
    </div>

    @if($data['orders']->isEmpty())
        <p class="os-empty mb-0">{{ $isLive ? 'Shift is live — no sales yet.' : 'No orders on this card.' }}</p>
    @else
        <div class="os-orders-wrap table-responsive">
            <table class="table table-sm mb-0 os-table">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Item</th>
                        <th>Guest</th>
                        <th class="text-center">Qty</th>
                        <th class="text-right">Total</th>
                        <th>Pay</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['orders'] as $order)
                        @php $fromPriorDebt = $order->wasPaidFromPriorDebt(); @endphp
                        <tr class="{{ $fromPriorDebt ? 'row-prior-debt' : '' }}">
                            <td class="text-nowrap">
                                {{ $order->completed_at ? $order->completed_at->format('d M H:i') : '—' }}
                            </td>
                            <td>
                                {{ $order->service_specific_data['item_name'] ?? $order->service->name ?? 'Item' }}
                                @if(!empty($order->service_specific_data['placed_by_name']))
                                    <br><small class="text-info">Placed by: {{ $order->service_specific_data['placed_by_name'] }}</small>
                                @endif
                                @if($fromPriorDebt)
                                    <br><span class="badge badge-warning" style="font-size:9px;">From prior debt</span>
                                @endif
                            </td>
                            <td>
                                @if($order->is_walk_in)
                                    {{ str_ireplace('Walk-in ', '', $order->walk_in_name ?? 'Walk-in') }}
                                @elseif($order->booking)
                                    Rm {{ $order->booking->room->room_number ?? 'N/A' }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="text-center">{{ $order->quantity }}</td>
                            <td class="text-right font-weight-bold">{{ number_format($order->total_price_tsh) }}</td>
                            <td>
                                @if($order->payment_status === 'paid')
                                    <span class="badge badge-success">PAID</span>
                                    @if($fromPriorDebt)
                                        <br><span class="badge badge-warning" style="font-size:9px;">Debt</span>
                                    @endif
                                @elseif($order->payment_status === 'room_charge')
                                    <span class="badge badge-info">ROOM</span>
                                @else
                                    <span class="badge badge-warning">UNPAID</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endif
