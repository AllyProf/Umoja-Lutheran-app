@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-list"></i> Shift Sales Details</h1>
        <p>Detailed sales breakdown for shift closed by {{ $shiftClosure->staff->name }}</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route('cashier.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('cashier.shift-handovers') }}">Handovers</a></li>
        <li class="breadcrumb-item">Sales Details</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="tile">
            <h3 class="tile-title">Shift Summary</h3>
            <div class="tile-body">
                <table class="table table-sm">
                    <tr>
                        <th>Opened:</th>
                        <td class="text-right">{{ $shiftClosure->opened_at->format('d M, H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Closed:</th>
                        <td class="text-right">{{ $shiftClosure->closed_at->format('d M, H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Cash Sales:</th>
                        <td class="text-right text-success">TZS {{ number_format($shiftClosure->total_cash_tzs) }}</td>
                    </tr>
                    <tr>
                        <th>M-Pesa Sales:</th>
                        <td class="text-right text-info">TZS {{ number_format($shiftClosure->total_mpesa_tzs) }}</td>
                    </tr>
                    <tr>
                        <th>Other Sales:</th>
                        <td class="text-right">TZS {{ number_format($shiftClosure->total_other_tzs) }}</td>
                    </tr>
                    <tr class="table-primary">
                        <th>Submitted Cash:</th>
                        <td class="text-right"><b>TZS {{ number_format($shiftClosure->amount_submitted_tzs) }}</b></td>
                    </tr>
                    <tr class="{{ $shiftClosure->difference_tzs < 0 ? 'table-danger' : 'table-success' }}">
                        <th>Difference:</th>
                        <td class="text-right"><b>TZS {{ number_format($shiftClosure->difference_tzs) }}</b></td>
                    </tr>
                </table>
            </div>
            @if($shiftClosure->status === 'pending_cashier')
            <div class="tile-footer">
                <form action="{{ route('cashier.shift-handovers.acknowledge', $shiftClosure->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success btn-block" onclick="return confirm('Collect TZS {{ number_format($shiftClosure->amount_submitted_tzs) }}?')">
                        <i class="fa fa-check"></i> Mark as Collected
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>

    <div class="col-md-8">
        <div class="tile">
            @if($shiftClosure->staff->role === 'reception' || $shiftClosure->staff->role === 'manager')
                <h3 class="tile-title">Reception Revenue Breakdown</h3>
                
                @if($bookings->count() > 0)
                <h5>Room Bookings</h5>
                <div class="table-responsive mb-4">
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Guest</th>
                                <th>Room</th>
                                <th>Paid At</th>
                                <th>Amount</th>
                                <th>Method</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookings as $booking)
                            <tr>
                                <td>{{ $booking->guest_name }}</td>
                                <td>{{ $booking->room->room_number ?? 'N/A' }}</td>
                                <td>{{ $booking->paid_at ? \Carbon\Carbon::parse($booking->paid_at)->format('H:i') : '-' }}</td>
                                <td>TZS {{ number_format($booking->amount_paid) }}</td>
                                <td><span class="badge badge-secondary">{{ ucfirst($booking->payment_method) }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

                @if($dayServices->count() > 0)
                <h5>Day Services (Parking, Garden, etc.)</h5>
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Service Type</th>
                                <th>Guest</th>
                                <th>Time</th>
                                <th>Amount</th>
                                <th>Method</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dayServices as $service)
                            <tr>
                                <td>
                                    @php
                                        $label = match (true) {
                                            str_contains($service->service_type, 'swimming')   => 'Swimming',
                                            str_contains($service->service_type, 'parking')    => 'Parking',
                                            str_contains($service->service_type, 'garden')     => 'Garden',
                                            str_contains($service->service_type, 'conference') => 'Conference Room',
                                            default => ucfirst(str_replace('_', ' ', $service->service_type)),
                                        };
                                    @endphp
                                    {{ $label }}
                                </td>
                                <td>{{ $service->guest_name }}</td>
                                <td>{{ $service->paid_at ? \Carbon\Carbon::parse($service->paid_at)->format('H:i') : '-' }}</td>
                                <td>TZS {{ number_format($service->amount_paid) }}</td>
                                <td><span class="badge badge-secondary">{{ ucfirst($service->payment_method) }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
                
                @if($bookings->count() == 0 && $dayServices->count() == 0)
                    <p class="text-center text-muted">No itemized sales found for this reception shift.</p>
                @endif

            @else
                <h3 class="tile-title">Itemized Sales (Counter)</h3>
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Total Price</th>
                                <th>Method</th>
                                <th>Ref</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sales as $sale)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($sale->completed_at)->format('H:i') }}</td>
                                <td>
                                    {{ $sale->service->service_name ?? 'N/A' }}
                                    @if($sale->booking)
                                        <br><small class="text-muted">Room: {{ $sale->booking->room->room_number }} ({{ $sale->booking->guest_name }})</small>
                                    @endif
                                </td>
                                <td>{{ $sale->quantity }}</td>
                                <td>TZS {{ number_format($sale->total_price_tsh) }}</td>
                                <td>
                                    <span class="badge badge-pill {{ $sale->payment_method === 'cash' ? 'badge-success' : 'badge-info' }}">
                                        {{ ucfirst($sale->payment_method) }}
                                    </span>
                                </td>
                                <td><small>{{ $sale->payment_reference ?? '-' }}</small></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
