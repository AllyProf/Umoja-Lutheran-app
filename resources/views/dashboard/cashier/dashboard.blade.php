@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-dashboard"></i> Cashier Dashboard</h1>
        <p>Financial Collection Overview</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
    </ul>
</div>

<div class="row">
    <div class="col-md-6 col-lg-3">
        <div class="widget-small primary coloured-icon"><i class="icon fa fa-money fa-3x"></i>
            <div class="info">
                <h4>Today's Restaurant</h4>
                <p><b>TZS {{ number_format($stats['today_restaurant_collected']) }}</b></p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="widget-small info coloured-icon"><i class="icon fa fa-bank fa-3x"></i>
            <div class="info">
                <h4>Today's Reception</h4>
                <p><b>TZS {{ number_format($stats['today_reception_collected']) }}</b></p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="widget-small warning coloured-icon"><i class="icon fa fa-clock-o fa-3x"></i>
            <div class="info">
                <h4>Pending Handovers</h4>
                <p><b>{{ $stats['pending_restaurant_handovers'] }}</b></p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="widget-small danger coloured-icon"><i class="icon fa fa-exclamation-triangle fa-3x"></i>
            <div class="info">
                <h4>Unverified Revenue</h4>
                <p><b>{{ $stats['pending_reception_collections'] }}</b></p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="tile">
            <h3 class="tile-title">Recent Restaurant Handovers</h3>
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>Counter Staff</th>
                            <th>Closed At</th>
                            <th>Submitted Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentHandovers as $handover)
                        <tr>
                            <td>{{ $handover->staff->name }}</td>
                            <td>{{ $handover->closed_at->format('d M Y, H:i') }}</td>
                            <td>TZS {{ number_format($handover->amount_submitted_tzs) }}</td>
                            <td>
                                @if($handover->status === 'pending_cashier')
                                    <span class="badge badge-warning">Pending Collection</span>
                                @elseif($handover->status === 'received')
                                    <span class="badge badge-success">Received</span>
                                @else
                                    <span class="badge badge-secondary">{{ ucfirst($handover->status) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($handover->status === 'pending_cashier')
                                <form action="{{ route('cashier.shift-handovers.acknowledge', $handover->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-primary" onclick="return confirm('Confirm cash collection for this shift?')">
                                        <i class="fa fa-check"></i> Collect
                                    </button>
                                </form>
                                @endif
                                <a href="{{ route('cashier.shift-handovers.sales', $handover->id) }}" class="btn btn-sm btn-info">
                                    <i class="fa fa-eye"></i> View Sales
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No recent handovers found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                <a href="{{ route('cashier.shift-handovers') }}" class="btn btn-primary btn-block">View All Handovers</a>
            </div>
        </div>
    </div>
</div>
@endsection
