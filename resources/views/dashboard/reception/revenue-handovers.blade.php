@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-handshake-o"></i> Revenue Handovers</h1>
        <p>Track your daily revenue collections and cashier handovers</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route('reception.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item">Revenue Handovers</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="tile shadow-sm" style="border-radius: 12px;">
            <div class="tile-title-w-btn">
                <h3 class="title"><i class="fa fa-history"></i> Handover History</h3>
            </div>
            <div class="tile-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="bg-light">
                            <tr>
                                <th>Opened</th>
                                <th>Closed</th>
                                <th>Cash (TZS)</th>
                                <th>M-Pesa (TZS)</th>
                                <th>Submitted (TZS)</th>
                                <th>Difference</th>
                                <th>Received By</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($history as $shift)
                            <tr>
                                <td>{{ $shift->opened_at ? $shift->opened_at->format('M d, Y H:i') : '-' }}</td>
                                <td>
                                    @if($shift->closed_at)
                                        {{ $shift->closed_at->format('M d, Y H:i') }}
                                    @else
                                        <span class="text-muted">Still open</span>
                                    @endif
                                </td>
                                <td>{{ number_format($shift->total_cash_tzs ?? 0) }}</td>
                                <td>{{ number_format($shift->total_mpesa_tzs ?? 0) }}</td>
                                <td><strong>{{ number_format($shift->amount_submitted_tzs ?? 0) }}</strong></td>
                                <td>
                                    @php $difference = (float) ($shift->difference_tzs ?? 0); @endphp
                                    @if($difference > 0)
                                        <span class="text-success">+{{ number_format($difference) }}</span>
                                    @elseif($difference < 0)
                                        <span class="text-danger">{{ number_format($difference) }}</span>
                                    @else
                                        <span class="text-muted">0</span>
                                    @endif
                                </td>
                                <td>{{ $shift->receiver->name ?? '-' }}</td>
                                <td>
                                    @if($shift->status === 'finalized')
                                        <span class="badge badge-success">Finalized</span>
                                    @elseif($shift->status === 'pending_accountant')
                                        <span class="badge badge-info">With Accountant</span>
                                    @elseif($shift->status === 'received')
                                        <span class="badge badge-primary">Collected by Cashier</span>
                                    @elseif($shift->status === 'pending_cashier')
                                        <span class="badge badge-warning">Waiting for Cashier</span>
                                    @elseif($shift->status === 'active')
                                        <span class="badge badge-danger">Open</span>
                                    @else
                                        <span class="badge badge-secondary">{{ ucfirst(str_replace('_', ' ', $shift->status ?? 'unknown')) }}</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">No handover records found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $history->links() }}
                </div>
            </div>
            <div class="tile-footer">
                <p class="text-muted">
                    <i class="fa fa-info-circle"></i> 
                    This list shows your shift handovers: cash submitted, who received it, and whether the cashier or accountant has cleared it.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
