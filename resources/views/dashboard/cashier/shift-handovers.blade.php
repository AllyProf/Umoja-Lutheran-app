@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-money"></i> {{ $type === 'reception' ? '2. Receive Reception cash' : '1. Receive Counter cash' }}</h1>
        <p>
            {{ $type === 'reception' ? 'Reception closed a shift and brought you cash.' : 'Counter closed a shift and brought you cash.' }}
            Count it, then click Receive.
        </p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route('cashier.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item">{{ $type === 'reception' ? 'Reception' : 'Counter' }}</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="alert alert-warning">
            You receive <strong>physical cash</strong> only. M-Pesa stays on the sales record and is not handed over.
            After you receive it, go to <a href="{{ route('cashier.accountant.handovers') }}">step 3: Send to Accountant</a>.
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="tile">
            <h3 class="tile-title">Waiting to be received</h3>
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>{{ $type === 'reception' ? 'Reception' : 'Counter' }}</th>
                            <th>Opened</th>
                            <th>Closed</th>
                            <th>System cash</th>
                            <th>Brought to you</th>
                            <th>Difference</th>
                            <th>Notes</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($handovers as $handover)
                        <tr>
                            <td>{{ $handover->staff->name }}</td>
                            <td>{{ optional($handover->opened_at)->format('d M, H:i') ?? '-' }}</td>
                            <td>{{ optional($handover->closed_at)->format('d M, H:i') ?? '-' }}</td>
                            <td>TZS {{ number_format($handover->total_cash_tzs) }}</td>
                            <td class="font-weight-bold text-primary">TZS {{ number_format($handover->amount_submitted_tzs) }}</td>
                            <td>
                                @if($handover->difference_tzs > 0)
                                    <span class="text-success">+{{ number_format($handover->difference_tzs) }}</span>
                                @elseif($handover->difference_tzs < 0)
                                    <span class="text-danger">{{ number_format($handover->difference_tzs) }}</span>
                                @else
                                    <span class="text-muted">0</span>
                                @endif
                            </td>
                            <td><small>{{ $handover->notes }}</small></td>
                            <td>
                                <div class="btn-group">
                                    <form action="{{ route('cashier.shift-handovers.acknowledge', $handover->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Confirm you received cash TZS {{ number_format($handover->amount_submitted_tzs) }}.')">
                                            <i class="fa fa-check"></i> Receive cash
                                        </button>
                                    </form>
                                    <a href="{{ route('cashier.shift-handovers.sales', $handover->id) }}" class="btn btn-sm btn-info ml-1">
                                        <i class="fa fa-list"></i> View sales
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">Nothing waiting. It appears here when someone closes a shift.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-2">
                {{ $handovers->links() }}
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="tile">
            <h3 class="tile-title">Received, not sent yet</h3>
            <div class="table-responsive">
                <table class="table table-sm table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>Closed</th>
                            <th>Name</th>
                            <th>You received</th>
                            <th>Next</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($history as $item)
                        <tr>
                            <td>{{ optional($item->closed_at)->format('d M Y, H:i') ?? '-' }}</td>
                            <td>{{ $item->staff->name ?? '-' }}</td>
                            <td>TZS {{ number_format($item->amount_submitted_tzs) }}</td>
                            <td>
                                <a href="{{ route('cashier.accountant.handovers') }}" class="btn btn-xs btn-primary">Send to Accountant</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">You have not received any cash on this list yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
