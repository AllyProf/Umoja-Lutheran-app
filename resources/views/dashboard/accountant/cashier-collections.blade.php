@extends('dashboard.layouts.app')

@section('content')
@php
    $category = $category ?? 'restaurant';
@endphp
<div class="app-title">
    <div>
        <h1><i class="fa fa-bank"></i> Receive Cashier Revenue</h1>
        <p>
            @if($category === 'reception')
                Acknowledge reception (rooms &amp; day services) collections
            @else
                Acknowledge restaurant / counter shift collections
            @endif
        </p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route('accountant.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item">Cashier Revenue</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="tile">
            <div class="tile-body">
                <ul class="nav nav-pills mb-3">
                    <li class="nav-item">
                        <a class="nav-link {{ $category === 'restaurant' ? 'active' : '' }}"
                            href="{{ route('accountant.cashier-collections', ['category' => 'restaurant', 'tab' => $tab]) }}">
                            <i class="fa fa-cutlery"></i> Restaurant
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $category === 'reception' ? 'active' : '' }}"
                            href="{{ route('accountant.cashier-collections', ['category' => 'reception', 'tab' => $tab]) }}">
                            <i class="fa fa-bed"></i> Reception
                        </a>
                    </li>
                </ul>

                <ul class="nav nav-tabs mb-3">
                    <li class="nav-item">
                        <a class="nav-link {{ $tab === 'pending' ? 'active' : '' }}"
                            href="{{ route('accountant.cashier-collections', ['category' => $category, 'tab' => 'pending']) }}">
                            Pending Verification
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $tab === 'verified' ? 'active' : '' }}"
                            href="{{ route('accountant.cashier-collections', ['category' => $category, 'tab' => 'verified']) }}">
                            Verification History
                        </a>
                    </li>
                </ul>

                @if($category === 'restaurant')
                    <h4 class="mb-3">Restaurant / Counter Shift Handovers</h4>
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
                                     <td colspan="6" class="text-center">No restaurant shift handovers found.</td>
                                 </tr>
                                 @endforelse
                             </tbody>
                        </table>
                        @include('dashboard.partials.bootstrap-pagination', ['paginator' => $shifts])
                    </div>
                @else
                    <h4 class="mb-3"><i class="fa fa-concierge-bell"></i> Reception Revenue (Rooms &amp; Services)</h4>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Day Services</th>
                                    <th>Room Bookings</th>
                                    <th>Total Revenue</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dayServices as $day)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($day->date)->format('D, d M Y') }}</td>
                                    <td>
                                        @if($day->ds_count > 0)
                                            TZS {{ number_format($day->ds_revenue) }} <br>
                                            <small class="text-muted">({{ $day->ds_count }} items)</small>
                                        @else
                                            <span class="text-muted">0</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($day->bk_count > 0)
                                            TZS {{ number_format($day->bk_revenue) }} <br>
                                            <small class="text-muted">({{ $day->bk_count }} bookings)</small>
                                        @else
                                            <span class="text-muted">0</span>
                                        @endif
                                    </td>
                                    <td class="font-weight-bold text-primary">
                                        TZS {{ number_format($day->total_revenue) }}
                                    </td>
                                    <td>
                                        @if($tab === 'pending')
                                            <span class="badge badge-warning">Pending Accountant</span>
                                        @else
                                            <span class="badge badge-success">Verified</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            @if($tab === 'pending')
                                            <form id="day-verify-form-{{ $loop->index }}" action="{{ route('accountant.cashier.day_revenue.verify') }}" method="POST" style="display:inline;">
                                                @csrf
                                                <input type="hidden" name="service_date" value="{{ $day->date }}">
                                                <button type="button" class="btn btn-primary btn-sm" onclick="confirmDayVerification('{{ $day->date }}', '{{ number_format($day->total_revenue) }}', 'day-verify-form-{{ $loop->index }}')">
                                                    <i class="fa fa-check"></i> Verify
                                                </button>
                                            </form>
                                            @endif
                                            <a href="{{ route('reception.day-services.index', ['date' => $day->date]) }}" class="btn btn-info btn-sm">
                                                <i class="fa fa-eye"></i> Details
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No reception revenue records found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        @include('dashboard.partials.bootstrap-pagination', ['paginator' => $dayServices])
                    </div>
                @endif
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
            title: "Verify Day Revenue?",
            text: "Confirm receipt of TZS " + amount + " for " + date + ".",
            type: "info",
            showCancelButton: true,
            confirmButtonText: "Yes, Verify!",
            cancelButtonText: "Cancel",
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
