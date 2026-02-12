@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-file-text"></i> Day Services Reports</h1>
    <p>View and download day services reports</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ $role === 'reception' ? route('reception.dashboard') : route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ $role === 'reception' ? route('reception.day-services.index') : route('admin.day-services.index') }}">Day Services</a></li>
    <li class="breadcrumb-item"><a href="#">Reports</a></li>
  </ul>
</div>

<!-- Report Filter -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-body">
        <form method="GET" action="{{ $role === 'reception' ? route('reception.day-services.reports') : route('admin.day-services.reports') }}" id="reportForm">
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label for="report_type"><strong>Report Type:</strong></label>
                <select name="report_type" id="report_type" class="form-control" onchange="toggleDateInputs()">
                  <option value="daily" {{ ($reportType ?? 'daily') == 'daily' ? 'selected' : '' }}>Daily</option>
                  <option value="weekly" {{ ($reportType ?? '') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                  <option value="monthly" {{ ($reportType ?? '') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                  <option value="yearly" {{ ($reportType ?? '') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                  <option value="custom" {{ ($reportType ?? '') == 'custom' ? 'selected' : '' }}>Custom Date Range</option>
                </select>
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label for="service_type"><strong>Service Type:</strong></label>
                <select name="service_type" id="service_type" class="form-control">
                  <option value="all" {{ ($serviceType ?? 'all') == 'all' ? 'selected' : '' }}>All Services</option>
                  <option value="swimming" {{ ($serviceType ?? '') == 'swimming' ? 'selected' : '' }}>Swimming</option>
                  <option value="swimming_with_bucket" {{ ($serviceType ?? '') == 'swimming_with_bucket' ? 'selected' : '' }}>Swimming with Bucket</option>
                  <option value="ceremony" {{ ($serviceType ?? '') == 'ceremony' ? 'selected' : '' }}>Ceremony</option>
                </select>
              </div>
            </div>
            <div class="col-md-3" id="singleDateContainer" style="display: {{ ($reportType ?? 'daily') == 'custom' ? 'none' : 'block' }};">
              <div class="form-group">
                <label for="date"><strong>Select Date:</strong></label>
                <input type="date" name="date" id="date" class="form-control" value="{{ $reportDate ?? \Carbon\Carbon::today()->format('Y-m-d') }}">
              </div>
            </div>
            <div class="col-md-2" id="startDateContainer" style="display: {{ ($reportType ?? 'daily') == 'custom' ? 'block' : 'none' }};">
              <div class="form-group">
                <label for="start_date"><strong>Start Date:</strong></label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate ?? '' }}">
              </div>
            </div>
            <div class="col-md-2" id="endDateContainer" style="display: {{ ($reportType ?? 'daily') == 'custom' ? 'block' : 'none' }};">
              <div class="form-group">
                <label for="end_date"><strong>End Date:</strong></label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate ?? '' }}">
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary btn-block">
                  <i class="fa fa-file-text"></i> Generate Report
                </button>
              </div>
            </div>
          </div>
          <div class="row mt-3">
            <div class="col-md-12">
              <div class="form-group mb-0">
                <label><strong>Quick Actions:</strong></label>
                <div class="btn-group" role="group">
                  <button type="button" class="btn btn-sm btn-outline-primary" onclick="setQuickReport('daily', '{{ \Carbon\Carbon::today()->format('Y-m-d') }}')">
                    <i class="fa fa-calendar-day"></i> Today
                  </button>
                  <button type="button" class="btn btn-sm btn-outline-primary" onclick="setQuickReport('weekly', '{{ \Carbon\Carbon::today()->format('Y-m-d') }}')">
                    <i class="fa fa-calendar-week"></i> This Week
                  </button>
                  <button type="button" class="btn btn-sm btn-outline-primary" onclick="setQuickReport('monthly', '{{ \Carbon\Carbon::today()->format('Y-m-d') }}')">
                    <i class="fa fa-calendar"></i> This Month
                  </button>
                  <button type="button" class="btn btn-sm btn-outline-primary" onclick="setQuickReport('yearly', '{{ \Carbon\Carbon::today()->format('Y-m-d') }}')">
                    <i class="fa fa-calendar-alt"></i> This Year
                  </button>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@if(request()->has('report_type') && isset($dayServices) && $dayServices->count() > 0)
<!-- Summary Cards -->
<div class="row mb-3">
  <div class="col-md-3">
    <div class="widget-small primary coloured-icon">
      <i class="icon fa fa-list fa-2x"></i>
      <div class="info">
        <h4>Total Services</h4>
        <p><b>{{ $statistics['total_services'] }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small success coloured-icon">
      <i class="icon fa fa-check-circle fa-2x"></i>
      <div class="info">
        <h4>Paid Services</h4>
        <p><b>{{ $statistics['paid_services'] }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small warning coloured-icon">
      <i class="icon fa fa-clock-o fa-2x"></i>
      <div class="info">
        <h4>Pending Services</h4>
        <p><b>{{ $statistics['pending_services'] }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small info coloured-icon">
      <i class="icon fa fa-money fa-2x"></i>
      <div class="info">
        <h4>Total Revenue</h4>
        <p><b>{{ number_format($statistics['total_revenue'], 0) }} TZS</b></p>
        <p><small>${{ number_format($statistics['total_revenue_usd'], 2) }}</small></p>
      </div>
    </div>
  </div>
</div>

<!-- Report Period Info -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn">
        <h3 class="title"><i class="fa fa-calendar"></i> {{ $dateRange['label'] ?? 'Report' }}</h3>
        <div class="btn-group">
          <a href="{{ ($role === 'reception' ? route('reception.day-services.reports.download') : route('admin.day-services.reports.download')) . '?' . http_build_query(request()->all()) }}" 
             class="btn btn-sm btn-primary" target="_blank">
            <i class="fa fa-download"></i> Download Report
          </a>
          <button onclick="window.print()" class="btn btn-sm btn-secondary">
            <i class="fa fa-print"></i> Print
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Services List -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-list"></i> Day Services Details</h3>
      <div class="tile-body">
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>#</th>
                <th>Service Date</th>
                <th>Service Time</th>
                <th>Service Type</th>
                <th>Guest Name</th>
                <th>Guest Type</th>
                <th>Amount (TZS)</th>
                <th>Amount (USD)</th>
                <th>Payment Status</th>
                <th>Registered By</th>
              </tr>
            </thead>
            <tbody>
              @foreach($dayServices as $index => $service)
              <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($service->service_date)->format('M d, Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($service->service_time)->format('h:i A') }}</td>
                <td>
                  @php
                    $type = str_replace('_', ' ', $service->service_type);
                    $type = str_ireplace('ceremory', 'ceremony', $type);
                    $type = str_ireplace('trey', 'tray', $type);
                  @endphp
                  <span class="badge badge-info">{{ ucfirst($type) }}</span>
                </td>
                <td><strong>{{ $service->guest_name }}</strong></td>
                <td>
                  @if($service->guest_type === 'tanzanian')
                    <span class="badge badge-primary">Tanzanian</span>
                  @else
                    <span class="badge badge-secondary">International</span>
                  @endif
                </td>
                <td>
                  @php $rate = $service->exchange_rate ?: $exchangeRate; @endphp
                  @if($service->guest_type === 'tanzanian')
                    <strong>{{ number_format($service->amount_paid ?? $service->amount ?? 0, 0) }}</strong>
                  @else
                    <strong>{{ number_format(($service->amount_paid ?? $service->amount ?? 0) * $rate, 0) }}</strong>
                  @endif
                </td>
                <td>
                  @if($service->guest_type === 'tanzanian')
                    ${{ number_format(($service->amount_paid ?? $service->amount ?? 0) / $rate, 2) }}
                  @else
                    $<strong>{{ number_format($service->amount_paid ?? $service->amount ?? 0, 2) }}</strong>
                  @endif
                </td>
                <td>
                  @if($service->payment_status === 'paid')
                    <span class="badge badge-success">Paid</span>
                  @elseif($service->payment_status === 'partial')
                    <span class="badge badge-warning">Partial</span>
                  @else
                    <span class="badge badge-danger">Pending</span>
                  @endif
                </td>
                <td>{{ $service->registeredBy->name ?? 'N/A' }}</td>
              </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <th colspan="6" class="text-right">Total Revenue:</th>
                <th><strong>{{ number_format($statistics['total_revenue'], 0) }} TZS</strong></th>
                <th><strong>${{ number_format($statistics['total_revenue_usd'], 2) }}</strong></th>
                <th colspan="2"></th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

@elseif(request()->has('report_type') && isset($dayServices) && $dayServices->count() == 0)
<!-- No Data Message -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-body text-center py-5">
        <i class="fa fa-info-circle fa-3x text-muted mb-3"></i>
        <h4>No Services Found</h4>
        <p class="text-muted">No day services found for the selected period and filters.</p>
      </div>
    </div>
  </div>
</div>
@endif

@endsection

@section('scripts')
<script>
function toggleDateInputs() {
  const reportType = document.getElementById('report_type').value;
  const singleDateContainer = document.getElementById('singleDateContainer');
  const startDateContainer = document.getElementById('startDateContainer');
  const endDateContainer = document.getElementById('endDateContainer');
  
  if (reportType === 'custom') {
    singleDateContainer.style.display = 'none';
    startDateContainer.style.display = 'block';
    endDateContainer.style.display = 'block';
  } else {
    singleDateContainer.style.display = 'block';
    startDateContainer.style.display = 'none';
    endDateContainer.style.display = 'none';
  }
}

function setQuickReport(type, date) {
  document.getElementById('report_type').value = type;
  document.getElementById('date').value = date;
  toggleDateInputs();
  document.getElementById('reportForm').submit();
}
</script>
@endsection
