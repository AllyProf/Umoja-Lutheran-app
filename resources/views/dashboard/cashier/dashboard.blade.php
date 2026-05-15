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
                <h4>Restaurant Pending</h4>
                <p><b>{{ $stats['pending_restaurant_handovers'] }}</b></p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="widget-small danger coloured-icon"><i class="icon fa fa-clock-o fa-3x"></i>
            <div class="info">
                <h4>Reception Pending</h4>
                <p><b>{{ $stats['pending_reception_handovers'] }}</b></p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="tile">
            <h3 class="tile-title text-primary"><i class="fa fa-cutlery"></i> Recent Restaurant Handovers</h3>
            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead>
                        <tr>
                            <th>Counter Staff</th>
                            <th>Time</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentRestaurantHandovers as $handover)
                        <tr>
                            <td>{{ $handover->staff->name }}</td>
                            <td>{{ $handover->closed_at->format('d M, H:i') }}</td>
                            <td>{{ number_format($handover->amount_submitted_tzs) }}</td>
                            <td>
                                @if($handover->status === 'pending_cashier')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($handover->status === 'received')
                                    <span class="badge badge-success">Received</span>
                                @endif
                            </td>
                            <td>
                                @if($handover->status === 'pending_cashier')
                                <form action="{{ route('cashier.shift-handovers.acknowledge', $handover->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-xs btn-primary p-1" title="Collect Cash">
                                        <i class="fa fa-check"></i>
                                    </button>
                                </form>
                                @endif
                                <a href="{{ route('cashier.shift-handovers.sales', $handover->id) }}" class="btn btn-xs btn-info p-1" title="View Sales">
                                    <i class="fa fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No recent restaurant handovers.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="tile">
            <h3 class="tile-title text-info"><i class="fa fa-concierge-bell"></i> Recent Reception Handovers</h3>
            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead>
                        <tr>
                            <th>Receptionist</th>
                            <th>Time</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentReceptionHandovers as $handover)
                        <tr>
                            <td>{{ $handover->staff->name }}</td>
                            <td>{{ $handover->closed_at->format('d M, H:i') }}</td>
                            <td>{{ number_format($handover->amount_submitted_tzs) }}</td>
                            <td>
                                @if($handover->status === 'pending_cashier')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($handover->status === 'received')
                                    <span class="badge badge-success">Received</span>
                                @endif
                            </td>
                            <td>
                                @if($handover->status === 'pending_cashier')
                                <form action="{{ route('cashier.shift-handovers.acknowledge', $handover->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-xs btn-primary p-1" title="Collect Cash">
                                        <i class="fa fa-check"></i>
                                    </button>
                                </form>
                                @endif
                                <a href="{{ route('cashier.shift-handovers.sales', $handover->id) }}" class="btn btn-xs btn-info p-1" title="View Sales">
                                    <i class="fa fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No recent reception handovers.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <a href="{{ route('cashier.shift-handovers', ['type' => 'restaurant']) }}" class="btn btn-outline-primary btn-block btn-sm">View All Restaurant Handovers</a>
    </div>
    <div class="col-md-6">
        <a href="{{ route('cashier.shift-handovers', ['type' => 'reception']) }}" class="btn btn-outline-info btn-block btn-sm">View All Reception Handovers</a>
    </div>
</div>
<div class="row mt-3">
    <div class="col-md-12">
        <a href="{{ route('cashier.reception.collections') }}" class="btn btn-primary btn-block">View Daily Reception Collections (Day Services)</a>
    </div>
</div>
@endsection
