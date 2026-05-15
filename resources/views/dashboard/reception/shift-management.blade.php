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
            <h3>Hakuna Shift Iliyofunguliwa</h3>
            <p class="text-muted">Unahitaji kufungua shift kwanza kabla ya kuendelea na mauzo ya leo ili yaweze kufuatiliwa vizuri.</p>
            <form action="{{ route('reception.shift.open') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary btn-lg px-5">
                    <i class="fa fa-play"></i> Fungua Shift Mpya
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
                        <small class="text-muted">Ingiza kiasi cha pesa taslimu (cash) unachokabidhi kwa Cashier sasa hivi.</small>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Maelezo yoyote ya ziada kuhusu shift hii..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger btn-block btn-lg" onclick="return confirm('Je, una uhakika unataka kufunga shift hii?')">
                        <i class="fa fa-check-circle"></i> Funga & Tuma kwa Cashier
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="row">
            <div class="col-md-6">
                <div class="widget-small primary coloured-icon"><i class="icon fa fa-bed fa-3x"></i>
                    <div class="info">
                        <h4>Room Bookings</h4>
                        <p><b>{{ $summary['bookings_count'] }}</b> (TZS {{ number_format($summary['bookings_total']) }})</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="widget-small info coloured-icon"><i class="icon fa fa-car fa-3x"></i>
                    <div class="info">
                        <h4>Day Services</h4>
                        <p><b>{{ $summary['day_services_count'] }}</b> (TZS {{ number_format($summary['day_services_total']) }})</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="tile">
            <h3 class="tile-title"><i class="fa fa-money"></i> Expected Collections Summary</h3>
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
            <div class="mt-4 p-3 border rounded" style="background-color: #f8f9fa; border-left: 5px solid #009688 !important;">
                <h5 class="mb-1">Jumla ya Mapato (Total): <span class="pull-right">TZS {{ number_format($summary['bookings_total'] + $summary['day_services_total']) }}</span></h5>
            </div>
            <div class="mt-3">
                <p class="text-muted small"><i class="fa fa-info-circle"></i> Mapato haya ni yale yote yaliyofanyika tangu shift yako ilipofunguliwa au tangu handover iliyopita iliyofungwa.</p>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
