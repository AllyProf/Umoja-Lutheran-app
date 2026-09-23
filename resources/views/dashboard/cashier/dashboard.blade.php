@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-dashboard"></i> Cashier</h1>
        <p>Three steps: receive Counter, receive Reception, then send to the Accountant.</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="alert alert-info">
            <strong>How the cash moves:</strong>
            Counter or Reception closes a shift and brings you the cash. You click <strong>Receive cash</strong>, then <strong>Send to Accountant</strong>. The Accountant closes the account. M-Pesa is not handed over in cash.
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="widget-small warning coloured-icon">
            <i class="icon fa fa-glass fa-3x"></i>
            <div class="info">
                <h4>1. Counter waiting</h4>
                <p><b>{{ $stats['counter_waiting_count'] }} shift</b></p>
                <small>TZS {{ number_format($stats['counter_waiting_amount']) }}</small>
            </div>
        </div>
        <a href="{{ route('cashier.shift-handovers', ['type' => 'restaurant']) }}" class="btn btn-warning btn-block mb-3">Receive Counter</a>
    </div>
    <div class="col-md-4">
        <div class="widget-small info coloured-icon">
            <i class="icon fa fa-bed fa-3x"></i>
            <div class="info">
                <h4>2. Reception waiting</h4>
                <p><b>{{ $stats['reception_waiting_count'] }} shift</b></p>
                <small>TZS {{ number_format($stats['reception_waiting_amount']) }}</small>
            </div>
        </div>
        <a href="{{ route('cashier.shift-handovers', ['type' => 'reception']) }}" class="btn btn-info btn-block mb-3">Receive Reception</a>
    </div>
    <div class="col-md-4">
        <div class="widget-small primary coloured-icon">
            <i class="icon fa fa-send fa-3x"></i>
            <div class="info">
                <h4>3. Send to Accountant</h4>
                <p><b>{{ $stats['ready_count'] }} shift</b></p>
                <small>TZS {{ number_format($stats['ready_amount']) }}</small>
            </div>
        </div>
        <a href="{{ route('cashier.accountant.handovers') }}" class="btn btn-primary btn-block mb-3">Send to Accountant</a>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="tile">
            <h3 class="tile-title"><i class="fa fa-glass"></i> Counter not received yet</h3>
            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Closed</th>
                            <th>Cash</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentRestaurantHandovers as $handover)
                        <tr>
                            <td>{{ $handover->staff->name ?? 'Counter' }}</td>
                            <td>{{ optional($handover->closed_at)->format('d M, H:i') ?? '-' }}</td>
                            <td>{{ number_format($handover->amount_submitted_tzs) }}</td>
                            <td class="text-right">
                                <form action="{{ route('cashier.shift-handovers.acknowledge', $handover->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Confirm you received cash TZS {{ number_format($handover->amount_submitted_tzs) }} from {{ $handover->staff->name ?? 'Counter' }}.')">
                                        Receive cash
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No Counter cash waiting.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="tile">
            <h3 class="tile-title"><i class="fa fa-bed"></i> Reception not received yet</h3>
            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Closed</th>
                            <th>Cash</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentReceptionHandovers as $handover)
                        <tr>
                            <td>{{ $handover->staff->name ?? 'Reception' }}</td>
                            <td>{{ optional($handover->closed_at)->format('d M, H:i') ?? '-' }}</td>
                            <td>{{ number_format($handover->amount_submitted_tzs) }}</td>
                            <td class="text-right">
                                <form action="{{ route('cashier.shift-handovers.acknowledge', $handover->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Confirm you received cash TZS {{ number_format($handover->amount_submitted_tzs) }} from {{ $handover->staff->name ?? 'Reception' }}.')">
                                        Receive cash
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No Reception cash waiting.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
