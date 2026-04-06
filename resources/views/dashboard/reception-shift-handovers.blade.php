@extends('dashboard.layouts.app')

@section('content')
    <div class="app-title">
        <div>
            <h1><i class="fa fa-handshake-o"></i> Shift Handovers</h1>
            <p>Verify and acknowledge staff shift closures</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="{{ route('reception.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item">Shift Handovers</li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="tile shadow-sm" style="border-radius: 12px;">
                <h3 class="tile-title">Recent Shift Closures</h3>
                <div class="tile-body">
                    @if($handovers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Closed At</th>
                                        <th>Staff Member</th>
                                        <th>Total Sales (TZS)</th>
                                        <th>Cash Submitted</th>
                                        <th>Difference</th>
                                        <th>Status</th>
                                        <th>Receiver</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($handovers as $handover)
                                        @php
                                            $totalSales = $handover->total_cash_tzs + $handover->total_mpesa_tzs + $handover->total_other_tzs;
                                            $diff = $handover->difference_tzs;
                                            $diffClass = $diff < 0 ? 'text-danger font-weight-bold' : ($diff > 0 ? 'text-success' : 'text-muted');
                                        @endphp
                                        <tr>
                                            <td>
                                                <strong>{{ $handover->closed_at->format('M d, H:i') }}</strong><br>
                                                <small class="text-muted">{{ $handover->opened_at->format('H:i') }} -
                                                    {{ $handover->closed_at->format('H:i') }}</small>
                                            </td>
                                            <td>{{ $handover->staff->name ?? 'Unknown' }}</td>
                                            <td>
                                                <span
                                                    title="Cash: {{ number_format($handover->total_cash_tzs) }}, M-Pesa: {{ number_format($handover->total_mpesa_tzs) }}, Other: {{ number_format($handover->total_other_tzs) }}">
                                                    {{ number_format($totalSales, 0) }}
                                                </span>
                                            </td>
                                            <td class="font-weight-bold text-primary">
                                                {{ number_format($handover->amount_submitted_tzs, 0) }}
                                            </td>
                                            <td class="{{ $diffClass }}">{{ number_format($diff, 0) }}</td>
                                            <td>
                                                @if($handover->status === 'acknowledged')
                                                    <span class="badge badge-success"><i class="fa fa-check-circle"></i>
                                                        Acknowledged</span>
                                                @else
                                                    <span class="badge badge-warning"><i class="fa fa-clock-o"></i> Pending
                                                        Verification</span>
                                                @endif
                                            </td>
                                            <td>{{ $handover->receiver->name ?? '-' }}</td>
                                            <td>
                                                <a href="{{ route('reception.shift-handovers.sales', $handover->id) }}"
                                                    class="btn btn-sm btn-info" title="View Individual Sales Records">
                                                    <i class="fa fa-list"></i> View Sales
                                                </a>

                                                @if($handover->status === 'pending_reception')
                                                    <form action="{{ route('reception.shift-handovers.acknowledge', $handover->id) }}"
                                                        method="POST" style="display:inline;"
                                                        onsubmit="return confirm('Confirm that you have received {{ number_format($handover->amount_submitted_tzs) }} TZS from {{ $handover->staff->name }}?')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success">
                                                            <i class="fa fa-check"></i> Receive
                                                        </button>
                                                    </form>
                                                @endif

                                                @if($handover->notes)
                                                    <button class="btn btn-sm btn-secondary"
                                                        onclick="Swal.fire('Shift Notes', '{{ addslashes($handover->notes) }}', 'info')">
                                                        <i class="fa fa-sticky-note-o"></i>
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $handovers->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fa fa-handshake-o fa-4x text-muted mb-3"></i>
                            <h4>No shift handovers found</h4>
                            <p class="text-muted">When counter staff close their shifts, they will appear here for verification.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection