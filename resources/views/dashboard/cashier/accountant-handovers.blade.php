@extends('dashboard.layouts.app')

@section('content')
@php
    $sourceOf = function ($shift) {
        $role = strtolower($shift->staff->role ?? '');
        return str_contains($role, 'reception') ? 'Reception' : 'Counter';
    };
@endphp
<div class="app-title">
    <div>
        <h1><i class="fa fa-send"></i> 3. Send to Accountant</h1>
        <p>Cash you already received. Send it so the Accountant can close the account.</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route('cashier.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item">Send to Accountant</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="widget-small warning coloured-icon"><i class="icon fa fa-glass fa-3x"></i>
            <div class="info">
                <h4>Counter in your hands</h4>
                <p><b>TZS {{ number_format($stats['counter_amount']) }}</b></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="widget-small info coloured-icon"><i class="icon fa fa-bed fa-3x"></i>
            <div class="info">
                <h4>Reception in your hands</h4>
                <p><b>TZS {{ number_format($stats['reception_amount']) }}</b></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="widget-small primary coloured-icon"><i class="icon fa fa-send fa-3x"></i>
            <div class="info">
                <h4>Total to send</h4>
                <p><b>TZS {{ number_format($stats['ready_amount']) }}</b></p>
                <small>{{ $stats['ready_count'] }} shift</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="tile p-0">
            <div class="d-flex border-bottom bg-white">
                <a href="{{ route('cashier.accountant.handovers', ['tab' => 'pending']) }}"
                   class="p-3 text-center flex-fill font-weight-bold text-decoration-none {{ $tab === 'pending' ? 'border-bottom border-primary text-primary' : 'text-muted' }}">
                    Not sent yet
                </a>
                <a href="{{ route('cashier.accountant.handovers', ['tab' => 'history']) }}"
                   class="p-3 text-center flex-fill font-weight-bold text-decoration-none {{ $tab === 'history' ? 'border-bottom border-primary text-primary' : 'text-muted' }}">
                    Already sent
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-12">
        <div class="tile">
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>From</th>
                            <th>Name</th>
                            <th>Time</th>
                            <th class="text-right">Cash handed over</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shifts as $shift)
                        <tr>
                            <td>
                                @if($sourceOf($shift) === 'Reception')
                                    <span class="badge badge-info">Reception</span>
                                @else
                                    <span class="badge badge-warning">Counter</span>
                                @endif
                            </td>
                            <td>{{ $shift->staff->name ?? '-' }}</td>
                            <td>{{ optional($shift->closed_at)->format('d M, H:i') ?? '-' }}</td>
                            <td class="text-right font-weight-bold">{{ number_format($shift->amount_submitted_tzs) }}</td>
                            <td>
                                @if($tab === 'pending')
                                    <form action="{{ route('cashier.shift.submit_accountant', $shift->id) }}" method="POST" onsubmit="return confirm('Send cash TZS {{ number_format($shift->amount_submitted_tzs) }} from {{ $shift->staff->name ?? 'this shift' }} to the Accountant?')">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fa fa-send"></i> Send to Accountant
                                        </button>
                                    </form>
                                @elseif($shift->status === 'finalized' || $shift->status === 'verified')
                                    <span class="badge badge-success">Closed by Accountant</span>
                                @else
                                    <span class="badge badge-info">Waiting for Accountant</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                @if($tab === 'pending')
                                    Nothing to send. Receive Counter or Reception cash first.
                                @else
                                    Nothing sent to the Accountant yet.
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
