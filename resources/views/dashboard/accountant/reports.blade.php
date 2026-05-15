@extends('dashboard.layouts.app')

@section('content')
    <div class="app-title">
        <div>
            <h1><i class="fa fa-bar-chart"></i> Purchase Reports</h1>
            <p>Financial summary of purchases and budgets</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="{{ route('accountant.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item">Reports</li>
        </ul>
    </div>

    <!-- Filters -->
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <form method="GET" action="{{ route('accountant.reports') }}" class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>From Date</label>
                            <input type="date" name="from" class="form-control" value="{{ $fromDate }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>To Date</label>
                            <input type="date" name="to" class="form-control" value="{{ $toDate }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="d-block">&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-filter mr-1"></i> Generate
                            Report</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <div class="col-md-4">
            <div class="tile shadow-sm text-center border-left border-info">
                <h6 class="text-muted uppercase mb-2">Total Approved Budget</h6>
                <h3 class="text-info">TZS {{ number_format($summary['total_approved_budget']) }}</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="tile shadow-sm text-center border-left border-primary">
                <h6 class="text-muted uppercase mb-2">Total Actual Spending</h6>
                <h3 class="text-primary">TZS {{ number_format($summary['total_spent']) }}</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="tile shadow-sm text-center border-left border-success">
                <h6 class="text-muted uppercase mb-2">Total Verified (Final)</h6>
                <h3 class="text-success">TZS {{ number_format($summary['total_verified']) }}</h3>
            </div>
        </div>
    </div>

    <!-- Detailed Table -->
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <h3 class="tile-title">Detailed Expenditure ({{ $summary['lists_count'] }} lists)</h3>
                @if($lists->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Shopping List</th>
                                    <th>Status</th>
                                    <th>Budget</th>
                                    <th>Actual</th>
                                    <th>Variance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lists as $list)
                                    @php
                                        $variance = ($list->budget_amount ?? 0) - ($list->total_actual_cost ?? 0);
                                    @endphp
                                    <tr>
                                        <td>{{ $list->created_at->format('d/m/Y') }}</td>
                                        <td><strong>{{ $list->name }}</strong></td>
                                        <td>
                                            <span
                                                class="badge badge-{{ $list->status === 'completed' ? 'success' : ($list->status === 'approved' ? 'info' : 'secondary') }}">
                                                {{ ucfirst($list->status) }}
                                            </span>
                                        </td>
                                        <td>{{ number_format($list->budget_amount) }}</td>
                                        <td>{{ number_format($list->total_actual_cost) }}</td>
                                        <td class="{{ $variance < 0 ? 'text-danger' : 'text-success' }}">
                                            {{ number_format($variance) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $lists->appends(request()->query())->links() }}
                    </div>
                @else
                    <p class="text-center py-5">No purchase data for this period.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
