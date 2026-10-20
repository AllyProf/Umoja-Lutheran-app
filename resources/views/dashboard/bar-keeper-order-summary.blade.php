@extends('dashboard.layouts.app')

@section('content')
@php
    $liveCards = $liveCards ?? collect();
    $unpaidGuests = $unpaidGuests ?? [];
    $byCounter = $byCounter ?? collect();
    $cancelledOrders = $cancelledOrders ?? collect();
    $pastShifts = $pastShifts ?? collect();
    $paidRevenue = (float) ($summary['paid_revenue'] ?? 0);
    $pendingRevenue = (float) ($summary['pending_revenue'] ?? 0);
    $totalRevenue = (float) ($summary['total_revenue'] ?? 0);
@endphp

<style>
  .osx {
    --osx-ink: #1a2332;
    --osx-muted: #6b7280;
    --osx-line: #e8ecf1;
    --osx-soft: #f6f8fb;
    --osx-accent: #0f766e;
    --osx-accent-soft: #ecfdf8;
    --osx-ok: #15803d;
    --osx-warn: #b45309;
    --osx-danger: #b91c1c;
    --osx-radius: 14px;
  }

  .osx-hero {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 16px;
    align-items: flex-end;
    margin-bottom: 18px;
    padding: 18px 20px;
    background: linear-gradient(135deg, #0f172a 0%, #134e4a 100%);
    border-radius: var(--osx-radius);
    color: #fff;
  }
  .osx-hero h1 {
    margin: 0 0 4px;
    font-size: 1.45rem;
    font-weight: 700;
    letter-spacing: -0.02em;
  }
  .osx-hero p { margin: 0; opacity: .78; font-size: .92rem; }
  .osx-hero .breadcrumb { margin: 0; background: transparent; }
  .osx-hero .breadcrumb-item,
  .osx-hero .breadcrumb-item a,
  .osx-hero .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,.7); }

  .osx-filter {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: end;
    padding: 14px 16px;
    background: #fff;
    border: 1px solid var(--osx-line);
    border-radius: var(--osx-radius);
    margin-bottom: 16px;
  }
  .osx-filter label {
    display: block;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: var(--osx-muted);
    margin-bottom: 4px;
  }
  .osx-filter .form-control {
    min-width: 130px;
    border-radius: 8px;
    border-color: var(--osx-line);
    height: 36px;
  }
  .osx-filter-range {
    margin-left: auto;
    font-size: 13px;
    color: var(--osx-muted);
    padding-bottom: 6px;
  }
  .osx-filter-range strong { color: var(--osx-ink); }

  .osx-kpis {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 12px;
    margin-bottom: 18px;
  }
  @media (max-width: 991px) { .osx-kpis { grid-template-columns: repeat(2, 1fr); } }
  .osx-kpi {
    background: #fff;
    border: 1px solid var(--osx-line);
    border-radius: var(--osx-radius);
    padding: 16px 18px;
    position: relative;
    overflow: hidden;
  }
  .osx-kpi::after {
    content: '';
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 4px;
    background: var(--osx-accent);
  }
  .osx-kpi.is-ok::after { background: var(--osx-ok); }
  .osx-kpi.is-warn::after { background: var(--osx-warn); }
  .osx-kpi.is-danger::after { background: var(--osx-danger); }
  .osx-kpi-label {
    display: block;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .05em;
    color: var(--osx-muted);
    margin-bottom: 6px;
    font-weight: 600;
  }
  .osx-kpi-value {
    display: block;
    font-size: 1.45rem;
    font-weight: 750;
    color: var(--osx-ink);
    letter-spacing: -0.03em;
    line-height: 1.15;
  }
  .osx-kpi-sub { display: block; margin-top: 4px; font-size: 12px; color: var(--osx-muted); }

  .osx-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 14px;
    padding: 6px;
    background: var(--osx-soft);
    border-radius: 12px;
    border: 1px solid var(--osx-line);
  }
  .osx-tab {
    border: 0;
    background: transparent;
    color: var(--osx-muted);
    font-weight: 600;
    font-size: 13px;
    padding: 9px 14px;
    border-radius: 9px;
    cursor: pointer;
    transition: .15s ease;
  }
  .osx-tab:hover { color: var(--osx-ink); background: rgba(255,255,255,.7); }
  .osx-tab.is-active {
    background: #fff;
    color: var(--osx-accent);
    box-shadow: 0 1px 3px rgba(15,23,42,.08);
  }
  .osx-tab .osx-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 20px;
    height: 20px;
    padding: 0 6px;
    margin-left: 6px;
    border-radius: 999px;
    background: #e5e7eb;
    color: #374151;
    font-size: 11px;
  }
  .osx-tab.is-active .osx-count { background: var(--osx-accent-soft); color: var(--osx-accent); }
  .osx-tab.has-alert .osx-count { background: #fef3c7; color: #92400e; }

  .osx-panel { display: none; }
  .osx-panel.is-active { display: block; }

  .osx-card {
    background: #fff;
    border: 1px solid var(--osx-line);
    border-radius: var(--osx-radius);
    padding: 16px 18px;
    margin-bottom: 12px;
  }
  .osx-card-head {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    margin-bottom: 12px;
  }
  .osx-card-head h3 {
    margin: 0;
    font-size: 15px;
    font-weight: 700;
    color: var(--osx-ink);
  }
  .osx-empty {
    text-align: center;
    padding: 28px 12px;
    color: var(--osx-muted);
    font-size: 14px;
  }

  .osx-shift {
    border: 1px solid var(--osx-line);
    border-radius: 12px;
    background: #fff;
    margin-bottom: 10px;
    overflow: hidden;
  }
  .osx-shift.is-live { border-color: #fecaca; background: linear-gradient(180deg, #fff7f7 0%, #fff 40%); }
  .osx-shift-toggle {
    width: 100%;
    border: 0;
    background: transparent;
    text-align: left;
    padding: 14px 16px;
    cursor: pointer;
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 10px;
    align-items: center;
  }
  .osx-shift-title {
    margin: 0 0 4px;
    font-size: 14px;
    font-weight: 700;
    color: var(--osx-ink);
  }
  .osx-shift-meta { font-size: 12px; color: var(--osx-muted); }
  .osx-shift-meta span { margin-right: 10px; }
  .osx-shift-stats { display: flex; flex-wrap: wrap; gap: 6px; align-items: center; }
  .osx-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 9px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    background: var(--osx-soft);
    color: #374151;
  }
  .osx-pill.is-ok { background: #dcfce7; color: #166534; }
  .osx-pill.is-warn { background: #fef3c7; color: #92400e; }
  .osx-pill.is-live { background: #fee2e2; color: #991b1b; }
  .osx-pill.is-muted { background: #f3f4f6; color: #6b7280; }
  .osx-pill-reception { background: #dbeafe; color: #1e40af; }
  .osx-shift-body {
    display: none;
    border-top: 1px solid var(--osx-line);
    padding: 0;
  }
  .osx-shift.is-open .osx-shift-body { display: block; }
  .osx-shift.is-open .osx-chevron { transform: rotate(180deg); }
  .osx-chevron { transition: transform .15s ease; color: var(--osx-muted); }
  .osx-actions { display: flex; gap: 6px; flex-wrap: wrap; }

  .osx-table { margin: 0; }
  .osx-table th {
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: .05em;
    color: var(--osx-muted);
    border-top: 0;
    background: var(--osx-soft);
    font-weight: 700;
    white-space: nowrap;
  }
  .osx-table td { vertical-align: middle; font-size: 13px; }
  .osx-item-line { font-size: 12px; color: #4b5563; line-height: 1.45; }
  .osx-item-line .t { color: #9ca3af; margin-right: 4px; }

  /* Prior-debt rows: readable in light + dark */
  .row-prior-debt > td {
    background: #fff8e6 !important;
    color: #1c1917 !important;
    border-color: #f0e0b2 !important;
  }
  .row-prior-debt .text-muted { color: #78716c !important; }
  .os-table .row-prior-debt > td {
    background: #fff8e6 !important;
    color: #1c1917 !important;
  }

  .live-badge .fa-circle { font-size: 8px; margin-right: 3px; animation: osx-pulse 1.4s infinite; }
  @keyframes osx-pulse {
    0% { opacity: 1; }
    50% { opacity: .35; }
    100% { opacity: 1; }
  }

  html[data-theme="dark"] .osx-filter,
  html[data-theme="dark"] .osx-kpi,
  html[data-theme="dark"] .osx-card,
  html[data-theme="dark"] .osx-shift { background: #161b22; border-color: #30363d; }
  html[data-theme="dark"] .osx-tabs { background: #0d1117; border-color: #30363d; }
  html[data-theme="dark"] .osx-tab.is-active { background: #21262d; color: #2dd4bf; }
  html[data-theme="dark"] .osx-kpi-value,
  html[data-theme="dark"] .osx-shift-title,
  html[data-theme="dark"] .osx-card-head h3 { color: #e6edf3; }
  html[data-theme="dark"] .osx-table th { background: #0d1117; }
  html[data-theme="dark"] .osx-table td { color: #e6edf3; }
  html[data-theme="dark"] .osx-shift.is-live {
    background: linear-gradient(180deg, #2a1a1a 0%, #161b22 45%);
    border-color: #7f1d1d;
  }
  html[data-theme="dark"] .row-prior-debt > td {
    background: #3d2e12 !important;
    color: #fde68a !important;
    border-color: #854d0e !important;
  }
  html[data-theme="dark"] .row-prior-debt .text-muted { color: #fbbf24 !important; }
  html[data-theme="dark"] .row-prior-debt .osx-pill.is-warn {
    background: #854d0e;
    color: #fef3c7;
  }
  html[data-theme="dark"] .os-table .row-prior-debt > td {
    background: #3d2e12 !important;
    color: #fde68a !important;
  }
  .osx-guest-group > td {
    border-top: 2px solid #e5e7eb;
    padding-top: 12px !important;
    padding-bottom: 12px !important;
  }
  html[data-theme="dark"] .osx-guest-group > td {
    border-top-color: #30363d;
  }
  html[data-theme="dark"] .osx-item-line { color: #c9d1d9; }
  html[data-theme="dark"] .osx-item-line .t { color: #8b949e; }
  html[data-theme="dark"] .osx-pill-reception {
    background: #1e3a5f !important;
    color: #93c5fd !important;
  }
</style>

@php
  $isCashierCtx = str_starts_with(optional(request()->route())->getName() ?? '', 'cashier.');
  $osxHome = $isCashierCtx ? 'cashier.dashboard' : 'bar-keeper.dashboard';
  $osxSummary = $isCashierCtx ? 'cashier.order-summary' : 'bar-keeper.order-summary';
  $osxUnpaidPdf = $isCashierCtx ? 'cashier.order-summary.unpaid-report' : 'bar-keeper.order-summary.unpaid-report';
  $osxPeriodPdf = $isCashierCtx ? 'cashier.order-summary.period-report' : 'bar-keeper.order-summary.period-report';
@endphp

<div class="osx">
  <div class="osx-hero">
    <div>
      <h1><i class="fa fa-bar-chart"></i> Order Summary</h1>
      <p>{{ $isManager ? 'Counter sales, unpaid guests, and shift history' : 'Your sales, unpaid guests, and cancellations' }}</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
      <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
      <li class="breadcrumb-item"><a href="{{ route($osxHome) }}">Dashboard</a></li>
      <li class="breadcrumb-item">Order Summary</li>
    </ul>
  </div>

  {{-- Filter --}}
  <form method="GET" action="{{ route($osxSummary) }}" class="osx-filter" id="orderSummaryFilterForm">
    <div>
      <label>Period</label>
      <select name="date_type" id="orderSummaryDateType" class="form-control form-control-sm" onchange="onOrderSummaryPeriodChange(this)">
        <option value="daily"   {{ $dateType === 'daily'   ? 'selected' : '' }}>Daily</option>
        <option value="weekly"  {{ $dateType === 'weekly'  ? 'selected' : '' }}>Weekly</option>
        <option value="monthly" {{ $dateType === 'monthly' ? 'selected' : '' }}>Monthly</option>
      </select>
    </div>
    <div>
      <label>{{ $dateType === 'weekly' ? 'Week of' : ($dateType === 'monthly' ? 'Month of' : 'Date') }}</label>
      <input type="date" name="date" id="orderSummaryDate" class="form-control form-control-sm"
             value="{{ $date->toDateString() }}" onchange="this.form.submit()">
    </div>
    <div class="d-flex" style="gap:6px; flex-wrap:wrap;">
      <button type="submit" class="btn btn-sm btn-primary" style="height:36px;border-radius:8px;"><i class="fa fa-search"></i></button>
      <a href="{{ route($osxSummary) }}" class="btn btn-sm btn-outline-light border" style="height:36px;border-radius:8px;color:#374151;border-color:#d1d5db !important;"><i class="fa fa-refresh"></i> Today</a>
      <a href="{{ route($osxPeriodPdf, ['date_type' => $dateType, 'date' => $date->toDateString()]) }}"
         class="btn btn-sm btn-danger"
         style="height:36px;border-radius:8px;">
        <i class="fa fa-file-pdf-o"></i>
        Download {{ ucfirst($dateType) }} PDF
      </a>
    </div>
    <div class="osx-filter-range">
      Showing <strong>{{ $startDate->format('d M Y') }}</strong>
      @if(!$startDate->isSameDay($endDate))
        – <strong>{{ $endDate->format('d M Y') }}</strong>
      @endif
    </div>
  </form>

  {{-- KPIs --}}
  <div class="osx-kpis">
    <div class="osx-kpi">
      <span class="osx-kpi-label">Period Sales</span>
      <span class="osx-kpi-value">{{ number_format($totalRevenue) }}</span>
      <span class="osx-kpi-sub">{{ number_format($summary['total_sold'] ?? 0) }} orders</span>
    </div>
    <div class="osx-kpi is-ok">
      <span class="osx-kpi-label">Paid</span>
      <span class="osx-kpi-value">{{ number_format($paidRevenue) }}</span>
      <span class="osx-kpi-sub">TZS collected</span>
    </div>
    <div class="osx-kpi is-warn">
      <span class="osx-kpi-label">Unpaid</span>
      <span class="osx-kpi-value">{{ number_format($pendingRevenue) }}</span>
      <span class="osx-kpi-sub">{{ number_format($summary['unpaid_guests'] ?? 0) }} guests</span>
    </div>
    <div class="osx-kpi is-danger">
      <span class="osx-kpi-label">Cancelled</span>
      <span class="osx-kpi-value">{{ number_format($summary['total_cancelled'] ?? 0) }}</span>
      <span class="osx-kpi-sub">{{ $liveCards->count() }} live counter(s)</span>
    </div>
  </div>

  @php
    $osxDefaultTab = ($liveCards->count() === 0 && $byCounter->count() > 0) ? 'history' : 'live';
  @endphp

  {{-- Tabs --}}
  <div class="osx-tabs" role="tablist">
    <button type="button" class="osx-tab {{ $osxDefaultTab === 'live' ? 'is-active' : '' }}" data-osx-tab="live">
      Live Now <span class="osx-count">{{ $liveCards->count() }}</span>
    </button>
    <button type="button" class="osx-tab {{ count($unpaidGuests) ? 'has-alert' : '' }}" data-osx-tab="unpaid">
      Unpaid <span class="osx-count">{{ count($unpaidGuests) }}</span>
    </button>
    <button type="button" class="osx-tab {{ $osxDefaultTab === 'history' ? 'is-active' : '' }}" data-osx-tab="history">
      Sales History <span class="osx-count">{{ $byCounter->count() }}</span>
    </button>
    <button type="button" class="osx-tab" data-osx-tab="cancelled">
      Cancelled <span class="osx-count">{{ $cancelledOrders->count() }}</span>
    </button>
  </div>

  {{-- LIVE --}}
  <div class="osx-panel {{ $osxDefaultTab === 'live' ? 'is-active' : '' }}" id="osx-panel-live">
    <div class="osx-card">
      <div class="osx-card-head">
        <h3><i class="fa fa-circle text-danger" style="font-size:10px;"></i> Open shifts right now</h3>
        <span class="text-muted small">Not mixed with weekly / monthly totals</span>
      </div>
      @forelse($liveCards as $data)
        @include('dashboard.partials.order-summary-shift-card', ['data' => $data, 'isManager' => $isManager, 'premium' => true])
      @empty
        <div class="osx-empty">
          No counter has an open shift right now.
          @if($byCounter->count() > 0)
            <div class="mt-2 small text-muted">Sales for this period are in <strong>Sales History</strong>.</div>
          @endif
        </div>
      @endforelse
    </div>
  </div>

  {{-- UNPAID --}}
  <div class="osx-panel" id="osx-panel-unpaid">
    <div class="osx-card">
      <div class="osx-card-head">
        <h3><i class="fa fa-user-times text-warning"></i> Unpaid Guests</h3>
        <div class="osx-actions">
          <span class="osx-pill is-warn">Due {{ number_format($pendingRevenue) }} TZS</span>
          <a href="{{ route($osxUnpaidPdf, request()->only(['date', 'date_type'])) }}"
             class="btn btn-sm btn-warning" style="border-radius:8px;">
            <i class="fa fa-download"></i> PDF
          </a>
        </div>
      </div>
      @if(empty($unpaidGuests))
        <div class="osx-empty">No unpaid guests for this period.</div>
      @else
        <div class="table-responsive">
          <table class="table table-sm table-hover mb-0 osx-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Guest / Room</th>
                <th>Items</th>
                <th class="text-center">Orders</th>
                <th>Pay Status</th>
                <th class="text-right">Amount Due</th>
              </tr>
            </thead>
            <tbody>
              @foreach($unpaidGuests as $i => $guest)
                <tr>
                  <td>{{ $i + 1 }}</td>
                  <td>
                    <strong>{{ $guest['guest'] }}</strong>
                    @if(!empty($guest['pay_at_reception']))
                      <div class="mt-1"><span class="osx-pill osx-pill-reception" style="font-size:9px;">PAY AT RECEPTION</span></div>
                    @endif
                  </td>
                  <td>
                    @foreach(($guest['items'] ?? []) as $item)
                      <div class="osx-item-line d-flex justify-content-between" style="gap:8px;">
                        <span>
                          <span class="t">{{ $item['time'] ?? '' }}</span>
                          {{ $item['item'] ?? 'Item' }} ×{{ $item['qty'] ?? 0 }}
                          — {{ number_format($item['amount'] ?? 0) }}
                        </span>
                        @if(($item['pay_status'] ?? '') === 'pay_at_reception')
                          <span class="osx-pill osx-pill-reception" style="font-size:8px;">Reception · UNPAID</span>
                        @else
                          <span class="osx-pill is-warn" style="font-size:8px;">UNPAID</span>
                        @endif
                      </div>
                    @endforeach
                  </td>
                  <td class="text-center">{{ $guest['orders'] }}</td>
                  <td>
                    @if(!empty($guest['all_reception']))
                      <span class="osx-pill osx-pill-reception">Pay at Reception</span>
                      <div class="mt-1"><span class="osx-pill is-warn">UNPAID</span></div>
                    @elseif(!empty($guest['pay_at_reception']))
                      <span class="osx-pill is-warn">UNPAID</span>
                      <div class="mt-1"><span class="osx-pill osx-pill-reception" style="font-size:9px;">+ Reception</span></div>
                    @else
                      <span class="osx-pill is-warn">UNPAID</span>
                    @endif
                  </td>
                  <td class="text-right font-weight-bold" style="color:var(--osx-warn);">{{ number_format($guest['amount']) }} TZS</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>
  </div>

  {{-- HISTORY --}}
  <div class="osx-panel {{ $osxDefaultTab === 'history' ? 'is-active' : '' }}" id="osx-panel-history">
    <div class="osx-card">
      <div class="osx-card-head">
        <h3><i class="fa fa-history"></i> Period Sales &amp; Closed Shifts</h3>
        <div class="osx-actions">
          <span class="text-muted small">Selected date range only</span>
          <a href="{{ route($osxPeriodPdf, ['date_type' => $dateType, 'date' => $date->toDateString()]) }}"
             class="btn btn-sm btn-danger" style="border-radius:8px;">
            <i class="fa fa-download"></i> Combined {{ ucfirst($dateType) }} PDF
          </a>
        </div>
      </div>

      @if($pastShifts->count() > 0)
        <div class="table-responsive mb-3">
          <table class="table table-sm table-hover mb-0 osx-table">
            <thead>
              <tr>
                @if($isManager)<th>Counter</th>@endif
                <th>Opened</th>
                <th>Closed</th>
                <th>Duration</th>
                <th class="text-right">Total</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @foreach($pastShifts as $shift)
                @php
                  $opened = $shift->opened_at ?? $shift->created_at;
                  $closed = $shift->closed_at;
                  $mins = ($opened && $closed) ? $opened->diffInMinutes($closed) : 0;
                  $hours = intdiv($mins, 60);
                  $rem = $mins % 60;
                  $duration = $hours > 0 ? ($hours.'h'.($rem ? ' '.$rem.'m' : '')) : ($rem.'m');
                  $totalSales = (float) $shift->total_cash_tzs + (float) $shift->total_mpesa_tzs + (float) $shift->total_other_tzs;
                @endphp
                <tr>
                  @if($isManager)
                    <td><strong>{{ $shift->staff->name ?? 'Counter' }}</strong></td>
                  @endif
                  <td>{{ $opened ? $opened->format('d M H:i') : '—' }}</td>
                  <td>{{ $closed ? $closed->format('d M H:i') : '—' }}</td>
                  <td>{{ $duration }}</td>
                  <td class="text-right font-weight-bold">{{ number_format($totalSales) }}</td>
                  <td class="text-nowrap">
                    <a href="{{ route($isCashierCtx ? 'cashier.shift-reports.pdf' : 'bar-keeper.shifts.report', $shift->id, false) }}"
                       class="btn btn-sm btn-outline-primary" style="border-radius:8px;" target="_blank">
                      <i class="fa fa-print"></i> PDF
                    </a>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif

      @forelse($byCounter as $data)
        @include('dashboard.partials.order-summary-shift-card', ['data' => $data, 'isManager' => $isManager, 'premium' => true])
      @empty
        @if($pastShifts->isEmpty())
          <div class="osx-empty">No sales for this period.</div>
        @endif
      @endforelse
    </div>
  </div>

  {{-- CANCELLED --}}
  <div class="osx-panel" id="osx-panel-cancelled">
    <div class="osx-card">
      <div class="osx-card-head">
        <h3><i class="fa fa-ban text-danger"></i> Cancelled Orders</h3>
      </div>
      @if($cancelledOrders->isEmpty())
        <div class="osx-empty">No cancelled orders for this period.</div>
      @else
        <div class="table-responsive">
          <table class="table table-sm table-hover mb-0 osx-table">
            <thead>
              <tr>
                <th>Time</th>
                <th>Item</th>
                <th>Guest / Room</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Value</th>
                @if($isManager)<th>By</th>@endif
                <th>Reason</th>
              </tr>
            </thead>
            <tbody>
              @foreach($cancelledOrders as $order)
                <tr>
                  <td class="text-nowrap">
                    {{ $order->cancelled_at ? $order->cancelled_at->format('H:i') : '-' }}
                    <small class="d-block text-muted">{{ $order->cancelled_at ? $order->cancelled_at->format('d M') : '' }}</small>
                  </td>
                  <td><strong>{{ $order->service_specific_data['item_name'] ?? $order->service->name ?? 'Item' }}</strong></td>
                  <td>
                    @if($order->is_walk_in)
                      Walk-in: {{ str_ireplace('Walk-in ', '', $order->walk_in_name ?? '') }}
                    @elseif($order->booking)
                      Room {{ $order->booking->room->room_number ?? 'N/A' }}
                    @else
                      —
                    @endif
                  </td>
                  <td class="text-center">{{ $order->quantity }}</td>
                  <td class="text-right text-danger font-weight-bold">{{ number_format($order->total_price_tsh) }}</td>
                  @if($isManager)
                    <td>{{ $order->cancelledBy->name ?? 'N/A' }}</td>
                  @endif
                  <td class="small">{{ $order->cancellation_reason ?: '—' }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>
  </div>
</div>

<script>
  function onOrderSummaryPeriodChange(sel) {
    const dateInput = document.getElementById('orderSummaryDate');
    const today = new Date();
    const pad = (n) => String(n).padStart(2, '0');
    const toYmd = (d) => d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());

    if (sel.value === 'weekly') {
      const d = new Date(today.getFullYear(), today.getMonth(), today.getDate());
      const day = d.getDay();
      const toMonday = day === 0 ? 6 : day - 1;
      d.setDate(d.getDate() - toMonday - 7);
      dateInput.value = toYmd(d);
    } else if (sel.value === 'daily') {
      dateInput.value = toYmd(today);
    } else if (sel.value === 'monthly') {
      dateInput.value = toYmd(new Date(today.getFullYear(), today.getMonth(), 1));
    }
    sel.form.submit();
  }

  document.querySelectorAll('[data-osx-tab]').forEach(function (tab) {
    tab.addEventListener('click', function () {
      var target = tab.getAttribute('data-osx-tab');
      document.querySelectorAll('[data-osx-tab]').forEach(function (t) { t.classList.remove('is-active'); });
      document.querySelectorAll('.osx-panel').forEach(function (p) { p.classList.remove('is-active'); });
      tab.classList.add('is-active');
      var panel = document.getElementById('osx-panel-' + target);
      if (panel) panel.classList.add('is-active');
    });
  });

  document.querySelectorAll('.js-osx-shift-toggle').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      if (e.target.closest('a,button.js-force-close-shift')) return;
      var card = btn.closest('.osx-shift');
      if (card) card.classList.toggle('is-open');
    });
  });

  document.querySelectorAll('.js-force-close-shift').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.stopPropagation();
      var shiftId = btn.getAttribute('data-shift-id');
      var counterName = btn.getAttribute('data-counter-name') || 'this counter';

      Swal.fire({
        title: 'Force Close Shift',
        html: 'Close shift for <strong>' + counterName + '</strong>?',
        input: 'text',
        inputLabel: 'Reason (optional)',
        inputValue: 'Forgot to close shift',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, close shift',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#dc3545',
        reverseButtons: true
      }).then(function (result) {
        if (!result.isConfirmed) return;
        var reason = (result.value || '').trim() || 'Forgot to close shift';
        btn.disabled = true;
        Swal.fire({ title: 'Closing shift...', allowOutsideClick: false, didOpen: function () { Swal.showLoading(); } });

        fetch('{{ url('/bar-keeper/shifts') }}/' + shiftId + '/force-close', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({ notes: reason })
        })
        .then(function (res) { return res.json().then(function (data) { return { ok: res.ok, data: data }; }); })
        .then(function (result) {
          if (!result.ok || !result.data.success) throw new Error((result.data && result.data.message) || 'Failed');
          return Swal.fire({ icon: 'success', title: 'Shift Closed', text: result.data.message || 'Done', timer: 1800, showConfirmButton: false });
        })
        .then(function () { window.location.reload(); })
        .catch(function (err) {
          Swal.fire({ icon: 'error', title: 'Failed', text: err.message || 'Could not close shift.' });
          btn.disabled = false;
        });
      });
    });
  });
</script>
@endsection
