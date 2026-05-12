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
                                <th>Service Date</th>
                                <th>Total Services</th>
                                <th>Total Revenue (TZS)</th>
                                <th>Cashier Collection</th>
                                <th>Accountant Verification</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dayServices as $day)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($day->service_date)->format('M d, Y') }}</td>
                                <td>{{ $day->total_services }}</td>
                                <td><strong>{{ number_format($day->total_revenue) }}</strong></td>
                                <td>
                                    @if($day->collected_at)
                                        <span class="text-success"><i class="fa fa-check-circle"></i> {{ \Carbon\Carbon::parse($day->collected_at)->format('M d, H:i') }}</span>
                                    @else
                                        <span class="text-muted"><i class="fa fa-clock-o"></i> Pending Collection</span>
                                    @endif
                                </td>
                                <td>
                                    @if($day->verified_at)
                                        <span class="text-success"><i class="fa fa-check-circle"></i> {{ \Carbon\Carbon::parse($day->verified_at)->format('M d, H:i') }}</span>
                                    @else
                                        <span class="text-muted"><i class="fa fa-hourglass-half"></i> Pending Verification</span>
                                    @endif
                                </td>
                                <td>
                                    @if($day->verified_at)
                                        <span class="badge badge-success">Finalized</span>
                                    @elseif($day->collected_at)
                                        <span class="badge badge-info">Collected by Cashier</span>
                                    @else
                                        <span class="badge badge-warning">At Reception</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No revenue records found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $dayServices->links() }}
                </div>
            </div>
            <div class="tile-footer">
                <p class="text-muted">
                    <i class="fa fa-info-circle"></i> 
                    This list shows your daily revenue from Day Services and tracks when the Cashier collects it and when the Accountant verifies it.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
