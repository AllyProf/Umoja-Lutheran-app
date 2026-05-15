@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-send"></i> Submit to Accountant</h1>
        <p>Finalize collections and transfer funds for accountant verification</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route('cashier.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item">Finance Submission</li>
    </ul>
</div>

{{-- Top Summary Widgets --}}
<div class="row">
    <div class="col-md-6 col-lg-3">
        <div class="widget-small primary coloured-icon"><i class="icon fa fa-coffee fa-3x"></i>
            <div class="info">
                <h4>Restaurant Cash</h4>
                <p><b>TZS {{ number_format($stats['total_shifts_cash']) }}</b></p>
                <small>{{ $stats['pending_shifts_count'] }} shifts pending</small>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="widget-small info coloured-icon"><i class="icon fa fa-concierge-bell fa-3x"></i>
            <div class="info">
                <h4>Reception Cash</h4>
                <p><b>TZS {{ number_format($stats['total_reception_cash']) }}</b></p>
                <small>{{ $stats['pending_reception_days'] }} days pending</small>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-6">
        <div class="widget-small warning coloured-icon"><i class="icon fa fa-money fa-3x"></i>
            <div class="info">
                <h4>Total Ready for Submission</h4>
                <p><b>TZS {{ number_format($stats['total_shifts_cash'] + $stats['total_reception_cash']) }}</b></p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="tile p-0 shadow-sm" style="background: transparent;">
            <div class="d-flex border-bottom bg-white rounded-top">
                <a href="{{ route('cashier.accountant.handovers', ['tab' => 'pending']) }}" 
                   class="p-3 text-center flex-fill font-weight-bold text-decoration-none {{ $tab === 'pending' ? 'border-bottom border-primary text-primary' : 'text-muted' }}">
                    <i class="fa fa-clock-o"></i> Pending Submissions
                </a>
                <a href="{{ route('cashier.accountant.handovers', ['tab' => 'history']) }}" 
                   class="p-3 text-center flex-fill font-weight-bold text-decoration-none {{ $tab === 'history' ? 'border-bottom border-primary text-primary' : 'text-muted' }}">
                    <i class="fa fa-history"></i> Submission History
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    {{-- Restaurant Shifts Column --}}
    <div class="col-md-7">
        <div class="tile shadow-sm h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="tile-title text-primary mb-0">
                    <i class="fa fa-coffee"></i> {{ $tab === 'history' ? 'Verified Shifts' : 'Restaurant & Bar Shifts' }}
                </h3>
                <span class="badge badge-primary px-3 py-2">{{ $shifts->count() }} {{ $tab === 'history' ? 'Items' : 'Collected' }}</span>
            </div>
            
            <div class="tile-body">
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead class="bg-light">
                            <tr>
                                <th>Counter Staff</th>
                                <th>{{ $tab === 'history' ? 'Verified At' : 'Collected On' }}</th>
                                <th class="text-right">Amount (TZS)</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($shifts as $shift)
                            <tr>
                                <td>
                                    <div class="font-weight-bold">{{ $shift->staff->name }}</div>
                                    <small class="text-muted">ID: #{{ $shift->id }}</small>
                                </td>
                                <td>{{ $shift->updated_at->format('d M, H:i') }}</td>
                                <td class="text-right font-weight-bold text-dark">{{ number_format($shift->amount_submitted_tzs) }}</td>
                                <td class="text-center">
                                    @if($tab === 'pending')
                                        <form action="{{ route('cashier.shift.submit_accountant', $shift->id) }}" method="POST" onsubmit="return confirm('Submit this shift cash to Accountant?')">
                                            @csrf
                                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3">
                                                <i class="fa fa-send"></i> Submit
                                            </button>
                                        </form>
                                    @else
                                        <span class="badge badge-success"><i class="fa fa-check"></i> Verified</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <i class="fa fa-info-circle fa-2x mb-2 d-block"></i>
                                    No shifts found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Reception Revenue Column --}}
    <div class="col-md-5">
        <div class="tile shadow-sm h-100 border-left-info" style="border-left: 4px solid #17a2b8;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="tile-title text-info mb-0">
                    <i class="fa fa-concierge-bell"></i> {{ $tab === 'history' ? 'Verified Reception' : 'Reception Revenue' }}
                </h3>
            </div>
            
            <div class="tile-body">
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead class="bg-light">
                            <tr>
                                <th>Service Date</th>
                                <th class="text-right">Total Revenue</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($receptionRevenue as $revenue)
                            <tr>
                                <td>
                                    <div class="font-weight-bold text-info">{{ \Carbon\Carbon::parse($revenue->date)->format('D, d M Y') }}</div>
                                    @if($revenue->bk_revenue > 0) <small class="mr-2">Rooms: {{ number_format($revenue->bk_revenue) }}</small> @endif
                                    @if($revenue->ds_revenue > 0) <small>Svcs: {{ number_format($revenue->ds_revenue) }}</small> @endif
                                </td>
                                <td class="text-right font-weight-bold">{{ number_format($revenue->total_revenue) }}</td>
                                <td class="text-center">
                                    @if($tab === 'pending')
                                        <span class="badge badge-info" title="You have collected this revenue. It is now visible to the Accountant.">
                                            <i class="fa fa-check-circle"></i> Sent
                                        </span>
                                    @else
                                        <span class="badge badge-success"><i class="fa fa-check"></i> Verified</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">
                                    <i class="fa fa-info-circle fa-2x mb-2 d-block"></i>
                                    No records found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($tab === 'pending')
            <div class="tile-footer border-top-0 pt-0">
                <div class="alert alert-info py-2" style="font-size: 11px;">
                    <i class="fa fa-info-circle"></i> <strong>Note:</strong> Reception revenue you collect is automatically visible to the Accountant.
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
