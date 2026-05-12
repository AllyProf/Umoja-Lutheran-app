@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-bank"></i> Cashier Collections</h1>
        <p>Acknowledge and verify funds received from the Cashier</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route('accountant.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item">Cashier Collections</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="tile">
            <div class="tile-body">
                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link {{ $tab === 'pending' ? 'active' : '' }}" href="{{ route('accountant.cashier-collections', ['tab' => 'pending']) }}">Pending Verification</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $tab === 'verified' ? 'active' : '' }}" href="{{ route('accountant.cashier-collections', ['tab' => 'verified']) }}">Verification History</a>
                    </li>
                </ul>

                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active">
                        <h4 class="mb-3">Restaurant/Counter Shift Handovers</h4>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Counter Staff</th>
                                        <th>Cashier</th>
                                        <th>Amount (TZS)</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($shifts as $shift)
                                    <tr>
                                        <td>{{ $shift->id }}</td>
                                        <td>{{ $shift->staff->name }}</td>
                                        <td>{{ $shift->receiver->name ?? 'N/A' }}</td>
                                        <td><strong>{{ number_format($shift->amount_submitted_tzs) }}</strong></td>
                                        <td>
                                            @if($shift->status === 'pending_accountant')
                                                <span class="badge badge-warning">Pending Accountant</span>
                                            @else
                                                <span class="badge badge-success">Finalized</span>
                                            @endif
                                        </td>
                                        <td>
                                             @if($shift->status === 'pending_accountant')
                                             <form id="shift-form-{{ $shift->id }}" action="{{ route('accountant.cashier.shift.acknowledge', $shift->id) }}" method="POST">
                                                 @csrf
                                                 <button type="button" class="btn btn-success btn-sm" onclick="confirmShiftReceipt('{{ number_format($shift->amount_submitted_tzs) }}', 'shift-form-{{ $shift->id }}')">
                                                     <i class="fa fa-check"></i> Acknowledge Receipt
                                                 </button>
                                             </form>
                                             @else
                                                 <span class="text-success"><i class="fa fa-check-circle"></i> Received</span>
                                             @endif
                                         </td>
                                     </tr>
                                     @empty
                                     <tr>
                                         <td colspan="6" class="text-center">No cashier shift handovers found.</td>
                                     </tr>
                                     @endforelse
                                 </tbody>
                            </table>
                            {{ $shifts->links() }}
                        </div>

                        <hr class="my-4">

                        <h4 class="mb-3">Reception (Day Services) Revenue</h4>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th>Service Date</th>
                                        <th>Total Services</th>
                                        <th>Total Revenue (TZS)</th>
                                        <th>Cashier Collection</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($dayServices as $day)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($day->service_date)->format('M d, Y') }}</td>
                                        <td>{{ $day->total_services }}</td>
                                        <td><strong>{{ number_format($day->total_revenue) }}</strong></td>
                                        <td>{{ $day->collected_at ? \Carbon\Carbon::parse($day->collected_at)->format('M d, H:i') : 'N/A' }}</td>
                                        <td>
                                            @if(!$day->verified_at)
                                                <span class="badge badge-warning">Pending Verification</span>
                                            @else
                                                <span class="badge badge-success">Verified</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!$day->verified_at)
                                            <div class="btn-group">
                                                <form id="day-verify-form-{{ $loop->index }}" action="{{ route('accountant.cashier.day_revenue.verify') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="service_date" value="{{ $day->service_date }}">
                                                    <button type="button" class="btn btn-primary btn-sm" onclick="confirmDayVerification('{{ $day->service_date }}', '{{ number_format($day->total_revenue) }}', 'day-verify-form-{{ $loop->index }}')">
                                                        <i class="fa fa-check-square-o"></i> Final Verify
                                                    </button>
                                                </form>
                                                <a href="{{ route('reception.day-services.index', ['date' => $day->service_date]) }}" class="btn btn-info btn-sm">
                                                    <i class="fa fa-eye"></i> Details
                                                </a>
                                            </div>
                                            @else
                                                <div class="btn-group">
                                                    <span class="text-success mr-2"><i class="fa fa-check-circle"></i> Verified</span>
                                                    <a href="{{ route('reception.day-services.index', ['date' => $day->service_date]) }}" class="btn btn-info btn-sm">
                                                        <i class="fa fa-eye"></i> Details
                                                    </a>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No day services revenue records found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            {{ $dayServices->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<script>
    function confirmShiftReceipt(amount, formId) {
        swal({
            title: "Confirm Receipt?",
            text: "You are acknowledging receipt of TZS " + amount + " from the Cashier.",
            type: "info",
            showCancelButton: true,
            confirmButtonText: "Yes, Received!",
            cancelButtonText: "No, cancel!",
            closeOnConfirm: false,
            closeOnCancel: true
        }, function(isConfirm) {
            if (isConfirm) {
                document.getElementById(formId).submit();
            }
        });
    }

    function confirmDayVerification(date, amount, formId) {
        swal({
            title: "Final Verification?",
            text: "Verify and record receipt of TZS " + amount + " for " + date + ". This will finalize the revenue collection for this day.",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, Finalize!",
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
