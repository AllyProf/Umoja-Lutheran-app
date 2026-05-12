@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-money"></i> Counter Handovers</h1>
        <p>Manage cash submissions from Restaurant & Bar staff</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route('cashier.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item">Handovers</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="tile">
            <h3 class="tile-title">Pending Handovers</h3>
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>Staff Name</th>
                            <th>Shift Opened</th>
                            <th>Shift Closed</th>
                            <th>Sales (Cash)</th>
                            <th>Submitted Amount</th>
                            <th>Difference</th>
                            <th>Notes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($handovers as $handover)
                        <tr>
                            <td>{{ $handover->staff->name }}</td>
                            <td>{{ $handover->opened_at->format('d M, H:i') }}</td>
                            <td>{{ $handover->closed_at->format('d M, H:i') }}</td>
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
                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Confirm receipt of TZS {{ number_format($handover->amount_submitted_tzs) }}?')">
                                            <i class="fa fa-check"></i> Collect Cash
                                        </button>
                                    </form>
                                    <a href="{{ route('cashier.shift-handovers.sales', $handover->id) }}" class="btn btn-sm btn-info ml-1">
                                        <i class="fa fa-list"></i> Details
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No pending handovers to collect.</td>
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
            <h3 class="tile-title">Recent Collection History</h3>
            <div class="table-responsive">
                <table class="table table-sm table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Staff</th>
                            <th>Amount Collected</th>
                            <th>Received By</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($history as $item)
                        <tr>
                            <td>{{ $item->closed_at->format('d M Y, H:i') }}</td>
                            <td>{{ $item->staff->name }}</td>
                            <td>TZS {{ number_format($item->amount_submitted_tzs) }}</td>
                            <td>{{ $item->receiver->name ?? 'N/A' }}</td>
                            <td>
                                <a href="{{ route('cashier.shift-handovers.sales', $item->id) }}" class="btn btn-xs btn-outline-info">
                                    View Sales
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
@endsection
