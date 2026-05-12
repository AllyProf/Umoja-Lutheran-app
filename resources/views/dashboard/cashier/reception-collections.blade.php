@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-bank"></i> Reception Collections</h1>
        <p>Verify and collect daily revenue from Reception (Day Services)</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route('cashier.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item">Reception Collections</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="widget-small warning coloured-icon"><i class="icon fa fa-calendar-check-o fa-3x"></i>
            <div class="info">
                <h4>Unverified Days</h4>
                <p><b>{{ $stats['unverified_days'] }}</b></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="widget-small primary coloured-icon"><i class="icon fa fa-money fa-3x"></i>
            <div class="info">
                <h4>Uncollected Cash</h4>
                <p><b>TZS {{ number_format($stats['total_unverified_cash']) }}</b></p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="tile">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="tile-title">Daily Revenue Summary</h3>
                <div class="btn-group" role="group">
                    <a href="{{ route('cashier.reception.collections', ['tab' => 'unverified']) }}" 
                       class="btn btn-sm {{ $tab === 'unverified' ? 'btn-primary' : 'btn-outline-primary' }}">
                       Pending Verification
                    </a>
                    <a href="{{ route('cashier.reception.collections', ['tab' => 'verified']) }}" 
                       class="btn btn-sm {{ $tab === 'verified' ? 'btn-primary' : 'btn-outline-primary' }}">
                       Verification History
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>Service Date</th>
                            <th>Total Services</th>
                            <th>Total Paid Revenue</th>
                            <th>Verification Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dailyRevenues as $revenue)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($revenue->service_date)->format('D, d M Y') }}</td>
                            <td>{{ $revenue->total_services }}</td>
                            <td class="font-weight-bold">TZS {{ number_format($revenue->total_revenue) }}</td>
                            <td>
                                @if($revenue->uncollected_count > 0)
                                    <span class="badge badge-warning">
                                        {{ $revenue->uncollected_count }} pending services
                                    </span>
                                @elseif($revenue->unverified_count > 0)
                                    <span class="badge badge-info">
                                        <i class="fa fa-clock-o"></i> Collected - Pending Accountant
                                    </span>
                                @else
                                    <span class="badge badge-success">
                                        <i class="fa fa-check"></i> Finalized by Accountant
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($revenue->uncollected_count > 0)
                                    <form id="verify-form-{{ $loop->index }}" action="{{ route('cashier.reception.verify-day') }}" method="POST" style="display:inline;">
                                        @csrf
                                        <input type="hidden" name="service_date" value="{{ $revenue->service_date }}">
                                        <button type="button" class="btn btn-sm btn-primary" 
                                                onclick="confirmVerification('{{ $revenue->service_date }}', '{{ number_format($revenue->total_revenue) }}', 'verify-form-{{ $loop->index }}')">
                                            Collect & Verify
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('reception.day-services.index', ['date' => $revenue->service_date]) }}" class="btn btn-sm btn-info">
                                    View Details
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No revenue records found for this category.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $dailyRevenues->appends(['tab' => $tab])->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<script>
    function confirmVerification(date, amount, formId) {
        swal({
            title: "Are you sure?",
            text: "You are about to collect and verify TZS " + amount + " for " + date + ". This action will mark all payments for this day as collected by you.",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, Collect & Verify!",
            cancelButtonText: "No, cancel!",
            closeOnConfirm: false,
            closeOnCancel: true
        }, function(isConfirm) {
            if (isConfirm) {
                document.getElementById(formId).submit();
            }
        });
    }
</script>
@endsection
