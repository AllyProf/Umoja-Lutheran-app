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

{{-- Top Stats --}}
<div class="row">
    <div class="col-md-3">
        <div class="widget-small warning coloured-icon"><i class="icon fa fa-calendar-check-o fa-3x"></i>
            <div class="info">
                <h4>Uncollected Days</h4>
                <p><b>{{ $stats['unverified_days'] }}</b></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="widget-small primary coloured-icon"><i class="icon fa fa-money fa-3x"></i>
            <div class="info">
                <h4>Uncollected Cash (Day Svcs)</h4>
                <p><b>TZS {{ number_format($stats['total_unverified_cash']) }}</b></p>
            </div>
        </div>
    </div>
    @if($roomRevenue && $roomRevenue->count > 0)
    <div class="col-md-4">
        <div class="widget-small danger coloured-icon"><i class="icon fa fa-bed fa-3x"></i>
            <div class="info">
                <h4>Uncollected Room Revenue</h4>
                <p><b>TZS {{ number_format($roomRevenue->revenue) }}</b> ({{ $roomRevenue->count }} bookings)</p>
            </div>
        </div>
    </div>
    @endif
</div>

{{-- Revenue Breakdown by Category --}}
@if($serviceBreakdown->count() > 0 || ($roomRevenue && $roomRevenue->count > 0))
<div class="row">
    <div class="col-md-12">
        <div class="tile">
            <h3 class="tile-title"><i class="fa fa-pie-chart"></i> Revenue Breakdown (Pending Collection)</h3>
            <div class="row">
                {{-- Room Bookings --}}
                @if($roomRevenue && $roomRevenue->count > 0)
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card border-left-danger h-100 py-2" style="border-left: 4px solid #dc3545; border-radius:6px;">
                        <div class="card-body py-2">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1" style="font-size:11px;">Room Bookings</div>
                                    <div class="h6 mb-0 font-weight-bold">TZS {{ number_format($roomRevenue->revenue) }}</div>
                                    <small class="text-muted">{{ $roomRevenue->count }} booking(s)</small>
                                </div>
                                <div class="col-auto"><i class="fa fa-bed fa-2x text-muted"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Day Service Breakdown --}}
                @php
                $categoryIcons = [
                    'Parking'          => ['icon' => 'fa-car',          'color' => '#17a2b8'],
                    'Garden'           => ['icon' => 'fa-leaf',         'color' => '#28a745'],
                    'Conference Room'  => ['icon' => 'fa-briefcase',    'color' => '#6f42c1'],
                    'Swimming'         => ['icon' => 'fa-tint',         'color' => '#007bff'],
                    'Ceremony / Events'=> ['icon' => 'fa-star',         'color' => '#fd7e14'],
                    'Projector'        => ['icon' => 'fa-video-camera', 'color' => '#20c997'],
                    'Music / Sound'    => ['icon' => 'fa-music',        'color' => '#e83e8c'],
                ];
                @endphp

                @foreach($serviceBreakdown as $label => $data)
                @php
                $icon  = $categoryIcons[$label]['icon']  ?? 'fa-tag';
                $color = $categoryIcons[$label]['color'] ?? '#6c757d';
                @endphp
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card h-100 py-2" style="border-left: 4px solid {{ $color }}; border-radius:6px;">
                        <div class="card-body py-2">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-uppercase mb-1" style="font-size:11px; color:{{ $color }};">{{ $label }}</div>
                                    <div class="h6 mb-0 font-weight-bold">TZS {{ number_format($data['revenue']) }}</div>
                                    <small class="text-muted">{{ $data['count'] }} service(s)</small>
                                </div>
                                <div class="col-auto"><i class="fa {{ $icon }} fa-2x text-muted"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

<div class="row">
    <div class="col-md-12">
        <div class="tile">
            {{-- Date Filter Form --}}
            <form method="GET" action="{{ route('cashier.reception.collections') }}" class="mb-3" id="dateFilterForm">
                <input type="hidden" name="tab" value="{{ $tab }}">

                {{-- Quick Select Buttons --}}
                <div class="mb-2">
                    <small class="text-muted font-weight-bold mr-2">Quick Select:</small>
                    <button type="button" class="btn btn-outline-primary btn-xs mr-1 quick-date" data-range="today">
                        <i class="fa fa-sun-o"></i> Today
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-xs mr-1 quick-date" data-range="yesterday">
                        <i class="fa fa-arrow-left"></i> Yesterday
                    </button>
                    <button type="button" class="btn btn-outline-info btn-xs mr-1 quick-date" data-range="week">
                        <i class="fa fa-calendar-o"></i> This Week
                    </button>
                    <button type="button" class="btn btn-outline-success btn-xs mr-1 quick-date" data-range="month">
                        <i class="fa fa-calendar"></i> This Month
                    </button>
                    @if($dateFrom || $dateTo)
                        <a href="{{ route('cashier.reception.collections', ['tab' => $tab]) }}" class="btn btn-outline-danger btn-xs">
                            <i class="fa fa-times"></i> Clear
                        </a>
                    @endif
                </div>

                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label class="form-label font-weight-bold" style="font-size:12px;">FROM DATE</label>
                        <input type="date" name="date_from" id="date_from" class="form-control form-control-sm"
                               value="{{ $dateFrom ?? '' }}" max="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label font-weight-bold" style="font-size:12px;">TO DATE</label>
                        <input type="date" name="date_to" id="date_to" class="form-control form-control-sm"
                               value="{{ $dateTo ?? '' }}" max="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary btn-sm mr-1">
                            <i class="fa fa-filter"></i> Filter
                        </button>
                        <a href="{{ route('cashier.reception.collections', ['tab' => $tab]) }}" class="btn btn-secondary btn-sm">
                            <i class="fa fa-times"></i> Reset
                        </a>
                    </div>
                    <div class="col-md-3 text-right">
                        @if($dateFrom || $dateTo)
                            <span class="badge badge-info" style="font-size:12px; padding:6px 10px;">
                                <i class="fa fa-calendar"></i>
                                {{ $dateFrom ? \Carbon\Carbon::parse($dateFrom)->format('d M Y') : 'All' }}
                                &rarr; {{ $dateTo ? \Carbon\Carbon::parse($dateTo)->format('d M Y') : 'Today' }}
                            </span>
                        @endif
                    </div>
                </div>
            </form>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="tile-title">Daily Revenue Summary</h3>
                <div class="btn-group" role="group">
                    <a href="{{ route('cashier.reception.collections', array_filter(['tab' => 'unverified', 'date_from' => $dateFrom, 'date_to' => $dateTo])) }}" 
                       class="btn btn-sm {{ $tab === 'unverified' ? 'btn-primary' : 'btn-outline-primary' }}">
                       Pending Verification
                    </a>
                    <a href="{{ route('cashier.reception.collections', array_filter(['tab' => 'verified', 'date_from' => $dateFrom, 'date_to' => $dateTo])) }}" 
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

    // Quick-select date shortcuts
    document.querySelectorAll('.quick-date').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var range = this.getAttribute('data-range');
            var today = new Date();
            var fromDate, toDate;

            var fmt = function(d) {
                var mm = String(d.getMonth() + 1).padStart(2, '0');
                var dd = String(d.getDate()).padStart(2, '0');
                return d.getFullYear() + '-' + mm + '-' + dd;
            };

            if (range === 'today') {
                fromDate = toDate = fmt(today);
            } else if (range === 'yesterday') {
                var y = new Date(today);
                y.setDate(today.getDate() - 1);
                fromDate = toDate = fmt(y);
            } else if (range === 'week') {
                var day  = today.getDay();
                var diff = today.getDate() - day + (day === 0 ? -6 : 1);
                var mon  = new Date(today.setDate(diff));
                fromDate = fmt(mon);
                toDate   = fmt(new Date());
            } else if (range === 'month') {
                var firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
                fromDate = fmt(firstDay);
                toDate   = fmt(new Date());
            }

            document.getElementById('date_from').value = fromDate;
            document.getElementById('date_to').value   = toDate;
            document.getElementById('dateFilterForm').submit();
        });
    });
</script>
@endsection
