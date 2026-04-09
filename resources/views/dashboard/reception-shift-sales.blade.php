@extends('dashboard.layouts.app')

@section('content')
    <div class="app-title">
        <div>
            <h1><i class="fa fa-list"></i> Shift Sales Details</h1>
            <p>Individual transactions for shift from {{ $shiftClosure->staff->name }}</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="{{ route('reception.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('reception.shift-handovers') }}">Shift Handovers</a></li>
            <li class="breadcrumb-item">Sales Details</li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="tile shadow-sm" style="border-radius: 12px; border-top: 4px solid #17a2b8;">
                <div class="tile-title-w-btn">
                    <h3 class="title">Sales Log: {{ $shiftClosure->opened_at->format('M d, H:i') }} -
                        {{ $shiftClosure->closed_at ? $shiftClosure->closed_at->format('H:i') : 'ACTIVE' }}</h3>
                    <a href="{{ route('reception.shift-handovers') }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i> Back to Handovers
                    </a>
                </div>

                <div class="tile-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded text-center">
                                <h6 class="text-muted mb-1 small">TOTAL CASH (TZS)</h6>
                                <h4 class="mb-0 text-primary">{{ number_format($shiftClosure->total_cash_tzs) }}</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded text-center">
                                <h6 class="text-muted mb-1 small">TOTAL M-PESA (TZS)</h6>
                                <h4 class="mb-0 text-success">{{ number_format($shiftClosure->total_mpesa_tzs) }}</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded text-center">
                                <h6 class="text-muted mb-1 small">TOTAL OTHER/POS (TZS)</h6>
                                <h4 class="mb-0 text-info">{{ number_format($shiftClosure->total_other_tzs) }}</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-dark rounded text-center text-white">
                                <h6 class="opacity-75 mb-1 small">GRAND TOTAL</h6>
                                <h4 class="mb-0">
                                    {{ number_format($shiftClosure->total_cash_tzs + $shiftClosure->total_mpesa_tzs + $shiftClosure->total_other_tzs) }}
                                </h4>
                            </div>
                        </div>
                    </div>

                    @if($sales->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Time</th>
                                        <th>Customer / Room</th>
                                        <th>Item / Service</th>
                                        <th>Qty</th>
                                        <th>Unit Price</th>
                                        <th>Total (TZS)</th>
                                        <th>Method</th>
                                        <th>Reference</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sales as $sale)
                                        <tr>
                                            <td>{{ $sale->completed_at ? $sale->completed_at->format('H:i') : $sale->updated_at->format('H:i') }}
                                            </td>
                                            <td>
                                                @if($sale->is_walk_in)
                                                    <span class="badge badge-info">Walk-in: {{ $sale->walk_in_name }}</span>
                                                @else
                                                    <strong>Room {{ $sale->booking->room->room_number ?? 'N/A' }}</strong><br>
                                                    <small class="text-muted">{{ $sale->booking->guest_name ?? '' }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $sale->service->name }}</td>
                                            <td>{{ $sale->quantity }}</td>
                                            <td>{{ number_format($sale->unit_price_tsh) }}</td>
                                            <td class="font-weight-bold">{{ number_format($sale->total_price_tsh) }}</td>
                                            <td>
                                                <span
                                                    class="badge badge-outline-secondary">{{ strtoupper($sale->payment_method) }}</span>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $sale->payment_reference ?: '-' }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fa fa-info-circle fa-4x text-muted mb-3"></i>
                            <h4>No individual sales records found</h4>
                            <p class="text-muted">This might happen if the shift was closed without any transactions.</p>
                        </div>
                    @endif
                </div>

                @if($shiftClosure->status === 'pending_reception')
                    <div class="tile-footer bg-light p-3 text-right">
                        <form action="{{ route('reception.shift-handovers.acknowledge', $shiftClosure->id) }}" method="POST"
                            onsubmit="return confirm('Confirm that you have verified these sales and received {{ number_format($shiftClosure->amount_submitted_tzs) }} TZS?')">
                            @csrf
                            <button type="submit" class="btn btn-lg btn-success">
                                <i class="fa fa-check-circle"></i> Verify & Acknowledge Shift
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection