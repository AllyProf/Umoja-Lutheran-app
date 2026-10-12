@php
    $isLive = !empty($data['is_live']);
    $isOrphan = !empty($data['is_orphan']);
@endphp
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
            @if($isManager && $isLive && !empty($data['shift_id']))
                <button type="button"
                        class="btn btn-sm btn-outline-danger js-force-close-shift"
                        data-shift-id="{{ $data['shift_id'] }}"
                        data-counter-name="{{ $data['counter_name'] }}">
                    <i class="fa fa-lock"></i> Force Close
                </button>
            @endif
            @if(!empty($data['closed_shift_id']))
                <a href="{{ route('bar-keeper.shifts.report', $data['closed_shift_id'], false) }}"
                   class="btn btn-sm btn-outline-primary">
                    <i class="fa fa-download"></i> Report
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
                        <tr>
                            <td class="text-nowrap">
                                {{ $order->completed_at ? $order->completed_at->format('d M H:i') : '—' }}
                            </td>
                            <td>{{ $order->service_specific_data['item_name'] ?? $order->service->name ?? 'Item' }}</td>
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
