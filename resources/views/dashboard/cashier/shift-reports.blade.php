@extends('dashboard.layouts.app')

@section('content')
<style>
  .cr-hero {
    background: linear-gradient(135deg, #7f1d1d 0%, #991b1b 45%, #450a0a 100%);
    color: #fff;
    border-radius: 14px;
    padding: 20px 22px;
    margin-bottom: 16px;
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    gap: 12px;
    flex-wrap: wrap;
  }
  .cr-hero h1 { margin: 0 0 4px; font-size: 1.35rem; font-weight: 700; }
  .cr-hero p { margin: 0; opacity: .8; font-size: .9rem; }
  .cr-hero .breadcrumb { margin: 0; background: transparent; }
  .cr-hero .breadcrumb-item,
  .cr-hero .breadcrumb-item a,
  .cr-hero .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,.7); }

  .cr-kpis {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 16px;
  }
  @media (max-width: 991px) { .cr-kpis { grid-template-columns: repeat(2, 1fr); } }
  .cr-kpi {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 14px;
  }
  .cr-kpi span { display: block; font-size: 11px; text-transform: uppercase; color: #6b7280; font-weight: 700; }
  .cr-kpi strong { font-size: 1.25rem; color: #111827; }

  .cr-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    overflow: hidden;
  }
  .cr-filters {
    padding: 14px 16px;
    border-bottom: 1px solid #f3f4f6;
    background: #fafafa;
  }
  .cr-status {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
  }
  .cr-status.is-pending { background: #fef3c7; color: #92400e; }
  .cr-status.is-received { background: #dcfce7; color: #166534; }
  .cr-status.is-acct { background: #dbeafe; color: #1e40af; }
  .cr-status.is-other { background: #f3f4f6; color: #4b5563; }

  html[data-theme="dark"] .cr-kpi,
  html[data-theme="dark"] .cr-card { background: #161b22; border-color: #30363d; }
  html[data-theme="dark"] .cr-kpi strong { color: #e6edf3; }
  html[data-theme="dark"] .cr-filters { background: #0d1117; border-bottom-color: #30363d; }
</style>

<div class="cr-hero">
  <div>
    <h1><i class="fa fa-file-text-o"></i> Closed Shift Reports</h1>
    <p>All closed Counter &amp; Reception shifts — open details or print the PDF report.</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('cashier.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item">Shift Reports</li>
  </ul>
</div>

<div class="cr-kpis">
  <div class="cr-kpi">
    <span>Closed shifts</span>
    <strong>{{ number_format($stats['total_closed']) }}</strong>
  </div>
  <div class="cr-kpi">
    <span>Waiting for you</span>
    <strong>{{ number_format($stats['pending_cashier']) }}</strong>
  </div>
  <div class="cr-kpi">
    <span>Received (not sent)</span>
    <strong>{{ number_format($stats['received']) }}</strong>
  </div>
  <div class="cr-kpi">
    <span>Cash closed today</span>
    <strong>{{ number_format($stats['cash_today']) }}</strong>
  </div>
</div>

<div class="cr-card">
  <div class="cr-filters">
    <form method="GET" action="{{ route('cashier.shift-reports') }}" class="form-row align-items-end">
      <div class="form-group col-md-3 mb-2">
        <label class="small font-weight-bold">Source</label>
        <select name="source" class="form-control form-control-sm">
          <option value="all" {{ $source === 'all' ? 'selected' : '' }}>All</option>
          <option value="counter" {{ $source === 'counter' ? 'selected' : '' }}>Counter only</option>
          <option value="reception" {{ $source === 'reception' ? 'selected' : '' }}>Reception only</option>
        </select>
      </div>
      <div class="form-group col-md-3 mb-2">
        <label class="small font-weight-bold">Status</label>
        <select name="status" class="form-control form-control-sm">
          <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All statuses</option>
          <option value="pending_cashier" {{ $status === 'pending_cashier' ? 'selected' : '' }}>Pending cashier</option>
          <option value="received" {{ $status === 'received' ? 'selected' : '' }}>Received</option>
          <option value="pending_accountant" {{ $status === 'pending_accountant' ? 'selected' : '' }}>With accountant</option>
          <option value="finalized" {{ $status === 'finalized' ? 'selected' : '' }}>Finalized</option>
          <option value="verified" {{ $status === 'verified' ? 'selected' : '' }}>Verified</option>
        </select>
      </div>
      <div class="form-group col-md-4 mb-2">
        <label class="small font-weight-bold">Search staff</label>
        <input type="text" name="q" value="{{ $q }}" class="form-control form-control-sm" placeholder="Name...">
      </div>
      <div class="form-group col-md-2 mb-2">
        <button type="submit" class="btn btn-sm btn-danger btn-block"><i class="fa fa-filter"></i> Filter</button>
      </div>
    </form>
  </div>

  <div class="table-responsive">
    <table class="table table-hover table-sm mb-0">
      <thead>
        <tr>
          <th>Staff</th>
          <th>Source</th>
          <th>Opened</th>
          <th>Closed</th>
          <th class="text-right">Cash</th>
          <th class="text-right">M-Pesa</th>
          <th class="text-right">Submitted</th>
          <th>Status</th>
          <th class="text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($shifts as $shift)
          @php
            $role = strtolower($shift->staff->role ?? '');
            $isReception = $role === 'reception';
            $statusClass = match ($shift->status) {
              'pending_cashier' => 'is-pending',
              'received' => 'is-received',
              'pending_accountant', 'finalized', 'verified' => 'is-acct',
              default => 'is-other',
            };
            $statusLabel = str_replace('_', ' ', $shift->status);
          @endphp
          <tr>
            <td>
              <strong>{{ $shift->staff->name ?? 'Staff' }}</strong>
            </td>
            <td>
              @if($isReception)
                <span class="badge badge-info">Reception</span>
              @else
                <span class="badge badge-warning">Counter</span>
              @endif
            </td>
            <td>{{ optional($shift->opened_at)->format('d M H:i') ?? '—' }}</td>
            <td>{{ optional($shift->closed_at)->format('d M H:i') ?? '—' }}</td>
            <td class="text-right">{{ number_format($shift->total_cash_tzs) }}</td>
            <td class="text-right">{{ number_format($shift->total_mpesa_tzs) }}</td>
            <td class="text-right font-weight-bold">{{ number_format($shift->amount_submitted_tzs) }}</td>
            <td><span class="cr-status {{ $statusClass }}">{{ ucwords($statusLabel) }}</span></td>
            <td class="text-right text-nowrap">
              <a href="{{ route('cashier.shift-handovers.sales', $shift->id) }}"
                 class="btn btn-xs btn-outline-info" title="View details">
                <i class="fa fa-eye"></i> View
              </a>
              <a href="{{ route('cashier.shift-reports.pdf', $shift->id) }}"
                 class="btn btn-xs btn-danger" title="Download / print PDF"
                 target="_blank">
                <i class="fa fa-print"></i> Print
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="9" class="text-center text-muted py-5">
              No closed shifts match your filters.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($shifts->hasPages())
    <div class="p-3">
      {{ $shifts->links() }}
    </div>
  @endif
</div>
@endsection
