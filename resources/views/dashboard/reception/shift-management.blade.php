@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-clock-o"></i> Shift Management</h1>
        <p>Manage your daily revenue and hand over to Cashier</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route('reception.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item">Shift Management</li>
    </ul>
</div>

<div class="row">
    @if(!$activeShift)
    <div class="col-md-12">
        <div class="tile text-center p-5">
            <div class="mb-4">
                <i class="fa fa-lock fa-5x text-muted"></i>
            </div>
            <h3>No Open Shift</h3>
            <p class="text-muted">You need to open a shift first before continuing with today's sales so they can be tracked properly.</p>
            <form action="{{ route('reception.shift.open') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary btn-lg px-5">
                    <i class="fa fa-play"></i> Open New Shift
                </button>
            </form>
        </div>
    </div>
    @else
    <div class="col-md-4">
        <div class="tile">
            <h3 class="tile-title text-primary"><i class="fa fa-info-circle"></i> Active Shift Info</h3>
            <div class="tile-body">
                <table class="table table-sm">
                    <tr>
                        <th>Opened By:</th>
                        <td>{{ Auth::guard('staff')->user()->name }}</td>
                    </tr>
                    <tr>
                        <th>Start Time:</th>
                        <td>{{ $activeShift->opened_at->format('d M Y, H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td><span class="badge badge-success">ACTIVE</span></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="tile">
            <h3 class="tile-title text-danger"><i class="fa fa-power-off"></i> Close Shift</h3>
            <div class="tile-body">
                <form action="{{ route('reception.shift.close') }}" method="POST" id="closeShiftForm">
                    @csrf
                    <div class="form-group">
                        <label class="control-label font-weight-bold">Total Cash to Submit (TZS)</label>
                        <input type="number" name="amount_submitted" class="form-control form-control-lg border-danger" required placeholder="0.00" step="0.01" value="{{ $summary['total_cash'] ?? '' }}">
                        <small class="text-muted">Enter the cash amount you are handing over to the Cashier now.</small>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Any additional notes about this shift..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger btn-block btn-lg" onclick="return confirm('Are you sure you want to close this shift?')">
                        <i class="fa fa-check-circle"></i> Close &amp; Send to Cashier
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="widget-small primary coloured-icon"><i class="icon fa fa-bed fa-3x"></i>
                    <div class="info">
                        <h4>Room Bookings</h4>
                        <p><b>{{ $summary['bookings_count'] }}</b> — TZS {{ number_format($summary['bookings_total']) }}</p>
                        <small class="text-muted">Cash: {{ number_format($summary['bookings_cash']) }}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="widget-small danger coloured-icon"><i class="icon fa fa-cutlery fa-3x"></i>
                    <div class="info">
                        <h4>Restaurant / Bar</h4>
                        <p><b>{{ $summary['restaurant_count'] }}</b> — TZS {{ number_format($summary['restaurant_total']) }}</p>
                        <small class="text-muted">Cash: {{ number_format($summary['restaurant_cash']) }}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="widget-small info coloured-icon"><i class="icon fa fa-car fa-3x"></i>
                    <div class="info">
                        <h4>Day Services</h4>
                        <p><b>{{ $summary['day_services_count'] }}</b> — TZS {{ number_format($summary['day_services_total']) }}</p>
                        <small class="text-muted">Cash: {{ number_format($summary['day_services_cash']) }}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="widget-small warning coloured-icon"><i class="icon fa fa-list fa-3x"></i>
                    <div class="info">
                        <h4>Other Services</h4>
                        <p><b>{{ $summary['other_services_count'] }}</b> — TZS {{ number_format($summary['other_services_total']) }}</p>
                        <small class="text-muted">Cash: {{ number_format($summary['other_services_cash']) }}</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="tile">
            <h3 class="tile-title"><i class="fa fa-pie-chart"></i> Revenue Breakdown by Source</h3>
            <div class="table-responsive mb-3">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Source</th>
                            <th class="text-right">Cash</th>
                            <th class="text-right">M-Pesa</th>
                            <th class="text-right">Other</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $b = $summary['breakdown']; @endphp
                        <tr>
                            <td><strong>Room Bookings</strong></td>
                            <td class="text-right">{{ number_format($b['bookings']['cash']) }}</td>
                            <td class="text-right">{{ number_format($b['bookings']['mpesa']) }}</td>
                            <td class="text-right">{{ number_format($b['bookings']['other']) }}</td>
                            <td class="text-right font-weight-bold">{{ number_format($b['bookings']['total']) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Restaurant / Bar</strong> <small class="text-muted">(paid at reception)</small></td>
                            <td class="text-right">{{ number_format($b['restaurant']['cash']) }}</td>
                            <td class="text-right">{{ number_format($b['restaurant']['mpesa']) }}</td>
                            <td class="text-right">{{ number_format($b['restaurant']['other']) }}</td>
                            <td class="text-right font-weight-bold">{{ number_format($b['restaurant']['total']) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Day Services</strong></td>
                            <td class="text-right">{{ number_format($b['day_services']['cash']) }}</td>
                            <td class="text-right">{{ number_format($b['day_services']['mpesa']) }}</td>
                            <td class="text-right">{{ number_format($b['day_services']['other']) }}</td>
                            <td class="text-right font-weight-bold">{{ number_format($b['day_services']['total']) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Other Services</strong></td>
                            <td class="text-right">{{ number_format($b['other_services']['cash']) }}</td>
                            <td class="text-right">{{ number_format($b['other_services']['mpesa']) }}</td>
                            <td class="text-right">{{ number_format($b['other_services']['other']) }}</td>
                            <td class="text-right font-weight-bold">{{ number_format($b['other_services']['total']) }}</td>
                        </tr>
                        <tr class="table-active">
                            <td><strong>TOTAL</strong></td>
                            <td class="text-right text-success font-weight-bold">{{ number_format($summary['total_cash']) }}</td>
                            <td class="text-right text-info font-weight-bold">{{ number_format($summary['total_mpesa']) }}</td>
                            <td class="text-right font-weight-bold">{{ number_format($summary['total_other']) }}</td>
                            <td class="text-right font-weight-bold" style="color:#940000;">{{ number_format($summary['grand_total']) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="text-center p-3 border rounded bg-light mb-2">
                        <h6 class="text-muted text-uppercase" style="font-size:11px;">Expected CASH</h6>
                        <h4 class="text-success mb-0">TZS {{ number_format($summary['total_cash']) }}</h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center p-3 border rounded bg-light mb-2">
                        <h6 class="text-muted text-uppercase" style="font-size:11px;">M-PESA / BANK</h6>
                        <h4 class="text-info mb-0">TZS {{ number_format($summary['total_mpesa']) }}</h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center p-3 border rounded bg-light mb-2">
                        <h6 class="text-muted text-uppercase" style="font-size:11px;">OTHER (POS/ETC)</h6>
                        <h4 class="mb-0">TZS {{ number_format($summary['total_other']) }}</h4>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                <p class="text-muted small mb-0"><i class="fa fa-info-circle"></i> <strong>Room Bookings</strong> = room payments. <strong>Restaurant / Bar</strong> = food/drinks paid at the counter then settled at reception. The cashier will see this breakdown too.</p>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
