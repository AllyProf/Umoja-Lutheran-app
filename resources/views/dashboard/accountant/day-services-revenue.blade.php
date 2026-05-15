@extends('dashboard.layouts.app')

@section('content')
    <div class="app-title">
        <div>
            <h1><i class="fa fa-money"></i> Day Services Revenue</h1>
            <p>Verify and accept daily cash collections from Reception</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="{{ route('accountant.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="#">Day Services Revenue</a></li>
        </ul>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <div class="col-md-6">
            <div class="widget-small warning coloured-icon">
                <i class="icon fa fa-clock-o fa-3x"></i>
                <div class="info">
                    <h4>Days Pending Verification</h4>
                    <p><b>{{ $stats['unverified_days'] }}</b></p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="widget-small info coloured-icon">
                <i class="icon fa fa-money fa-3x"></i>
                <div class="info">
                    <h4>Total Unverified Cash</h4>
                    <p><b>TZS {{ number_format($stats['total_unverified_cash'], 2) }}</b></p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-title-w-btn">
                    <h3 class="title">Daily Collections</h3>
                    <div class="btn-group">
                        <a class="btn btn-primary {{ $tab === 'unverified' ? 'active' : '' }}"
                            href="{{ route('accountant.day-services.revenue', ['tab' => 'unverified']) }}"><i
                                class="fa fa-exclamation-circle"></i> Unverified</a>
                        <a class="btn btn-primary {{ $tab === 'verified' ? 'active' : '' }}"
                            href="{{ route('accountant.day-services.revenue', ['tab' => 'verified']) }}"><i
                                class="fa fa-check-circle"></i> Verified</a>
                    </div>
                </div>

                <div class="tile-body">
                    @if($dailyRevenues->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="revenueTable">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Total Services</th>
                                        <th>Total Revenue (Paid)</th>
                                        <th>Pending Revenue (Unpaid)</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dailyRevenues as $day)
                                        <tr>
                                            <td><strong>{{ \Carbon\Carbon::parse($day->service_date)->format('M d, Y') }}</strong>
                                            </td>
                                            <td>{{ $day->total_services }}</td>
                                            <td><strong>TZS {{ number_format($day->total_revenue, 2) }}</strong></td>
                                            <td class="text-danger">TZS {{ number_format($day->pending_revenue, 2) }}</td>
                                            <td>
                                                @if($day->unverified_count > 0)
                                                    <span class="badge badge-warning">Pending Verification</span>
                                                @else
                                                    <span class="badge badge-success">Verified</span><br>
                                                    <small
                                                        class="text-muted">{{ \Carbon\Carbon::parse($day->verified_at)->format('M d, Y H:i') }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($day->unverified_count > 0 && $day->total_revenue > 0)
                                                    <form action="{{ route('accountant.day-services.verify-day') }}" method="POST"
                                                        class="d-inline"
                                                        onsubmit="return confirm('Verify that you have received TZS {{ number_format($day->total_revenue, 2) }} for {{ \Carbon\Carbon::parse($day->service_date)->format('M d, Y') }}?');">
                                                        @csrf
                                                        <input type="hidden" name="service_date"
                                                            value="{{ \Carbon\Carbon::parse($day->service_date)->format('Y-m-d') }}">
                                                        <button type="submit" class="btn btn-sm btn-success">
                                                            <i class="fa fa-check"></i> Accept Payment
                                                        </button>
                                                    </form>
                                                @elseif($day->unverified_count == 0 && $day->total_revenue > 0)
                                                    <button class="btn btn-sm btn-secondary" disabled><i class="fa fa-check"></i>
                                                        Accepted</button>
                                                @else
                                                    <span class="text-muted">No Revenue</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $dailyRevenues->appends(['tab' => $tab])->links('pagination::bootstrap-4') }}
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="fa fa-info-circle fa-2x mb-2"></i><br>
                            No {{ $tab }} days found.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
