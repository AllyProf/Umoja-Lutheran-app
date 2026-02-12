@extends('dashboard.layouts.app')

@section('content')
<style>
    :root {
        --primary: #e77a31;
        --secondary: #2d3436;
        --success: #28a745;
        --info: #17a2b8;
        --warning: #ffc107;
        --danger: #dc3545;
        --bg-gray: #f8f9fa;
        --radius: 20px;
    }

    body { background-color: var(--bg-gray); }

    .summary-card {
        background: white;
        border-radius: var(--radius);
        padding: 25px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        margin-bottom: 25px;
        border: none;
    }

    .stat-box {
        text-align: center;
        padding: 20px;
        border-radius: 15px;
        transition: 0.3s;
    }

    .stat-value {
        font-size: 1.8rem;
        font-weight: 800;
        display: block;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 0.85rem;
        color: #636e72;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 700;
    }

    .revenue-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px;
        border-bottom: 1px solid #eee;
    }

    .revenue-item:last-child { border-bottom: none; }

    .item-rank {
        width: 30px;
        height: 30px;
        background: var(--primary-light);
        color: var(--primary);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        margin-right: 15px;
    }

    .btn-date-picker {
        background: white;
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 8px 15px;
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .stat-value { font-size: 1.4rem; }
        .summary-card { padding: 15px; }
        .header-stack { flex-direction: column; align-items: flex-start !important; gap: 15px; }
        .mobile-w-100 { width: 100%; }
        .mobile-btn-group { display: flex; width: 100%; gap: 10px; }
        .mobile-btn-group .form-control { flex: 1; }
        .card-header-title { font-size: 1.5rem; }
    }

    @media (min-width: 769px) {
        .card-header-title { font-size: 2rem; }
    }
</style>

<div class="container-fluid py-3 py-md-4">
    <!-- Header Area -->
    <div class="d-flex justify-content-between align-items-center mb-4 header-stack">
        <div>
            <h2 class="font-weight-bold mb-0 card-header-title">Sales Summary</h2>
            <p class="text-muted mb-0">Tracking your performance and sales impact</p>
        </div>
        <div class="d-flex align-items-center gap-2 mobile-w-100 mobile-btn-group">
            <form action="{{ route('waiter.sales-summary') }}" method="GET" class="d-flex gap-2 flex-grow-1">
                <input type="date" name="date" value="{{ $date }}" class="form-control btn-date-picker" onchange="this.form.submit()">
            </form>
            <a href="{{ route('waiter.dashboard') }}" class="btn btn-outline-secondary" style="border-radius: 10px; white-space: nowrap;">
                <i class="fa fa-arrow-left"></i> <span class="d-none d-sm-inline">Dashboard</span>
            </a>
        </div>
    </div>

    <!-- Main Stats Row -->
    <div class="row">
        <div class="col-md-4">
            <div class="summary-card">
                <div class="stat-box" style="background: #fff3e0;">
                    <span class="stat-value" style="color: #940000;">{{ number_format($totalSales) }} <small>TZS</small></span>
                    <span class="stat-label">Total Generated</span>
                </div>
                <div class="mt-4">
                    <div class="revenue-item">
                        <span><i class="fa fa-check-circle text-success mr-2"></i> Paid at Counter</span>
                        <strong class="text-success">{{ number_format($paidSales) }}</strong>
                    </div>
                    <div class="revenue-item">
                        <span><i class="fa fa-bed text-info mr-2"></i> Room Charges</span>
                        <strong class="text-info">{{ number_format($roomChargeSales) }}</strong>
                    </div>
                    <div class="revenue-item">
                        <span><i class="fa fa-clock-o text-warning mr-2"></i> Pending Settlement</span>
                        <strong class="text-warning">{{ number_format($pendingSales) }}</strong>
                    </div>
                </div>
            </div>

            <div class="summary-card">
                <h5 class="font-weight-bold mb-4">Activity Overview</h5>
                <div class="row text-center">
                    <div class="col-4">
                        <span class="d-block h4 font-weight-bold mb-0">{{ $totalOrders }}</span>
                        <small class="text-muted">Orders</small>
                    </div>
                    <div class="col-4 border-left border-right">
                        <span class="d-block h4 font-weight-bold mb-0">{{ $completedOrders }}</span>
                        <small class="text-muted">Served</small>
                    </div>
                    <div class="col-4">
                        <span class="d-block h4 font-weight-bold mb-0 text-danger">{{ $cancelledOrders }}</span>
                        <small class="text-muted">Cancelled</small>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Active (In Prep)</span>
                        <span class="badge badge-primary px-3 py-2" style="border-radius: 10px;">{{ $activeOrders }} orders</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="summary-card">
                <h5 class="font-weight-bold mb-4"><i class="fa fa-trophy text-warning mr-2"></i> Top Selling Items ({{ Carbon\Carbon::parse($date)->format('M d') }})</h5>
                
                @if(count($itemsBreakdown) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover border-0">
                            <thead>
                                <tr class="text-muted" style="font-size: 0.8rem; text-transform: uppercase;">
                                    <th>Rank</th>
                                    <th>Item Name</th>
                                    <th class="text-right">Qty & Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $rank = 1; @endphp
                                @foreach($itemsBreakdown as $name => $data)
                                <tr>
                                    <td><div class="item-rank">{{ $rank++ }}</div></td>
                                    <td>
                                        <div class="font-weight-bold" style="font-size: 1.05rem;">{{ $name }}</div>
                                        <div class="d-md-none text-muted small">Sold: {{ $data['qty'] }}</div>
                                    </td>
                                    <td class="text-right">
                                        <h6 class="font-weight-bold mb-0">{{ number_format($data['revenue']) }} <small>TZS</small></h6>
                                        <div class="d-none d-md-block text-muted small">Qty: {{ $data['qty'] }}</div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fa fa-coffee fa-3x text-light mb-3"></i>
                        <p class="text-muted">No sales recorded for this date yet.</p>
                        <a href="{{ route('waiter.dashboard') }}" class="btn btn-primary btn-sm px-4" style="border-radius: 10px;">Go to POS</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
