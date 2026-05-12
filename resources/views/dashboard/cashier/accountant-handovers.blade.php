@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-send"></i> Submit to Accountant</h1>
        <p>Manage and submit collected funds to the Accountant for final verification</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route('cashier.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item">Submit to Accountant</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="tile">
            <h3 class="tile-title">Collected Restaurant/Counter Shifts</h3>
            <div class="tile-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Counter Staff</th>
                                <th>Collected At</th>
                                <th>Amount (TZS)</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($shifts as $shift)
                            <tr>
                                <td>{{ $shift->id }}</td>
                                <td>{{ $shift->staff->name }}</td>
                                <td>{{ $shift->updated_at->format('M d, Y H:i') }}</td>
                                <td><strong>{{ number_format($shift->amount_submitted_tzs) }}</strong></td>
                                <td>
                                    <form action="{{ route('cashier.shift.submit_accountant', $shift->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to submit this shift cash to the Accountant?')">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fa fa-send"></i> Submit to Accountant
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No collected shifts ready for submission.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="tile">
            <h3 class="tile-title">Collected Reception (Day Services) Revenue</h3>
            <div class="tile-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>Service Date</th>
                                <th>Total Revenue (TZS)</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dayServices as $revenue)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($revenue->service_date)->format('M d, Y') }}</td>
                                <td><strong>{{ number_format($revenue->total_revenue) }}</strong></td>
                                <td>
                                    <span class="badge badge-warning">Ready for Accountant Verification</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center">No collected day revenue ready for verification.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="tile-footer">
                <p class="text-muted"><i class="fa fa-info-circle"></i> Once revenue is collected from Reception, it is automatically visible to the Accountant for final verification.</p>
            </div>
        </div>
    </div>
</div>
@endsection
