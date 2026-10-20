@extends('dashboard.layouts.app')

@section('content')
<style>
  .cx-hero {
    background: linear-gradient(135deg, #0f766e 0%, #134e4a 55%, #042f2e 100%);
    color: #fff;
    border-radius: 14px;
    padding: 22px 24px;
    margin-bottom: 18px;
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    gap: 16px;
    flex-wrap: wrap;
  }
  .cx-hero h1 { margin: 0 0 6px; font-size: 1.45rem; font-weight: 700; }
  .cx-hero p { margin: 0; opacity: .82; font-size: .92rem; max-width: 520px; }
  .cx-hero .breadcrumb { margin: 0; background: transparent; }
  .cx-hero .breadcrumb-item,
  .cx-hero .breadcrumb-item a,
  .cx-hero .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,.72); }

  .cx-steps {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 14px;
    margin-bottom: 18px;
  }
  @media (max-width: 991px) { .cx-steps { grid-template-columns: 1fr; } }

  .cx-step {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 18px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 1px 2px rgba(0,0,0,.04);
  }
  .cx-step::before {
    content: '';
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 4px;
  }
  .cx-step.is-1::before { background: #f59e0b; }
  .cx-step.is-2::before { background: #0ea5e9; }
  .cx-step.is-3::before { background: #0f766e; }
  .cx-step-label {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #6b7280;
    font-weight: 700;
    margin-bottom: 6px;
  }
  .cx-step h3 { margin: 0 0 4px; font-size: 1.05rem; font-weight: 700; color: #111827; }
  .cx-step .cx-amount { font-size: 1.35rem; font-weight: 800; color: #0f766e; margin: 8px 0 4px; }
  .cx-step .cx-meta { font-size: 13px; color: #6b7280; margin-bottom: 12px; }
  .cx-step .btn { border-radius: 8px; font-weight: 600; }

  .cx-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    margin-bottom: 16px;
    overflow: hidden;
  }
  .cx-card-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    padding: 14px 16px;
    border-bottom: 1px solid #f3f4f6;
  }
  .cx-card-head h3 { margin: 0; font-size: 1rem; font-weight: 700; }
  .cx-quick {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 18px;
  }
  @media (max-width: 991px) { .cx-quick { grid-template-columns: repeat(2, 1fr); } }
  .cx-quick a {
    display: block;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 14px;
    color: #111827;
    text-decoration: none;
    transition: border-color .15s, box-shadow .15s;
  }
  .cx-quick a:hover { border-color: #0f766e; box-shadow: 0 4px 12px rgba(15,118,110,.12); text-decoration: none; color: #111827; }
  .cx-quick i { color: #0f766e; margin-right: 6px; }
  .cx-quick strong { display: block; margin-top: 4px; font-size: .95rem; }
  .cx-quick span { font-size: 12px; color: #6b7280; }

  html[data-theme="dark"] .cx-step,
  html[data-theme="dark"] .cx-card,
  html[data-theme="dark"] .cx-quick a { background: #161b22; border-color: #30363d; }
  html[data-theme="dark"] .cx-step h3,
  html[data-theme="dark"] .cx-card-head h3,
  html[data-theme="dark"] .cx-quick a,
  html[data-theme="dark"] .cx-quick strong { color: #e6edf3; }
  html[data-theme="dark"] .cx-card-head { border-bottom-color: #30363d; }
</style>

<div class="cx-hero">
  <div>
    <h1><i class="fa fa-university"></i> Cashier Desk</h1>
    <p>Receive cash from Counter &amp; Reception, review closed shift reports, then send collections to the Accountant.</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
  </ul>
</div>

<div class="cx-steps">
  <div class="cx-step is-1">
    <div class="cx-step-label">Step 1</div>
    <h3><i class="fa fa-glass text-warning"></i> Receive Counter</h3>
    <div class="cx-amount">{{ number_format($stats['counter_waiting_amount']) }} <small style="font-size:.7em;">TZS</small></div>
    <div class="cx-meta">{{ $stats['counter_waiting_count'] }} shift(s) waiting</div>
    <a href="{{ route('cashier.shift-handovers', ['type' => 'restaurant']) }}" class="btn btn-warning btn-sm btn-block">
      Open Counter queue
    </a>
  </div>
  <div class="cx-step is-2">
    <div class="cx-step-label">Step 2</div>
    <h3><i class="fa fa-bed text-info"></i> Receive Reception</h3>
    <div class="cx-amount">{{ number_format($stats['reception_waiting_amount']) }} <small style="font-size:.7em;">TZS</small></div>
    <div class="cx-meta">{{ $stats['reception_waiting_count'] }} shift(s) waiting</div>
    <a href="{{ route('cashier.shift-handovers', ['type' => 'reception']) }}" class="btn btn-info btn-sm btn-block">
      Open Reception queue
    </a>
  </div>
  <div class="cx-step is-3">
    <div class="cx-step-label">Step 3</div>
    <h3><i class="fa fa-send text-success"></i> Send to Accountant</h3>
    <div class="cx-amount">{{ number_format($stats['ready_amount']) }} <small style="font-size:.7em;">TZS</small></div>
    <div class="cx-meta">{{ $stats['ready_count'] }} shift(s) ready</div>
    <a href="{{ route('cashier.accountant.handovers') }}" class="btn btn-success btn-sm btn-block">
      Send cash now
    </a>
  </div>
</div>

<div class="cx-quick">
  <a href="{{ route('cashier.shift-reports') }}">
    <i class="fa fa-file-pdf-o fa-lg"></i>
    <strong>Shift Reports</strong>
    <span>View &amp; print closed shifts</span>
  </a>
  <a href="{{ route('cashier.order-summary') }}">
    <i class="fa fa-bar-chart fa-lg"></i>
    <strong>Order Summary</strong>
    <span>Live &amp; period sales</span>
  </a>
  <a href="{{ route('cashier.debts') }}">
    <i class="fa fa-handshake-o fa-lg"></i>
    <strong>Debts</strong>
    <span>Unpaid counter guests</span>
  </a>
  <a href="{{ route('cashier.reception.collections') }}">
    <i class="fa fa-calendar-check-o fa-lg"></i>
    <strong>Reception Days</strong>
    <span>Verify day collections</span>
  </a>
</div>

<div class="row">
  <div class="col-lg-6">
    <div class="cx-card">
      <div class="cx-card-head">
        <h3><i class="fa fa-glass text-warning"></i> Counter — waiting</h3>
        <a href="{{ route('cashier.shift-handovers', ['type' => 'restaurant']) }}" class="btn btn-sm btn-outline-warning">View all</a>
      </div>
      <div class="table-responsive">
        <table class="table table-hover table-sm mb-0">
          <thead>
            <tr>
              <th>Name</th>
              <th>Closed</th>
              <th class="text-right">Cash</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentRestaurantHandovers as $handover)
            <tr>
              <td>{{ $handover->staff->name ?? 'Counter' }}</td>
              <td>{{ optional($handover->closed_at)->format('d M, H:i') ?? '-' }}</td>
              <td class="text-right font-weight-bold">{{ number_format($handover->amount_submitted_tzs) }}</td>
              <td class="text-right text-nowrap">
                <a href="{{ route('cashier.shift-reports.pdf', $handover->id) }}" class="btn btn-xs btn-outline-danger" title="Print PDF">
                  <i class="fa fa-print"></i>
                </a>
                <a href="{{ route('cashier.shift-handovers.sales', $handover->id) }}" class="btn btn-xs btn-outline-info" title="Details">
                  <i class="fa fa-list"></i>
                </a>
                <form action="{{ route('cashier.shift-handovers.acknowledge', $handover->id) }}" method="POST" class="d-inline">
                  @csrf
                  <button type="submit" class="btn btn-xs btn-success"
                    onclick="return confirm('Confirm you received cash TZS {{ number_format($handover->amount_submitted_tzs) }} from {{ $handover->staff->name ?? 'Counter' }}.')">
                    Receive
                  </button>
                </form>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="4" class="text-center text-muted py-4">No Counter cash waiting.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="cx-card">
      <div class="cx-card-head">
        <h3><i class="fa fa-bed text-info"></i> Reception — waiting</h3>
        <a href="{{ route('cashier.shift-handovers', ['type' => 'reception']) }}" class="btn btn-sm btn-outline-info">View all</a>
      </div>
      <div class="table-responsive">
        <table class="table table-hover table-sm mb-0">
          <thead>
            <tr>
              <th>Name</th>
              <th>Closed</th>
              <th class="text-right">Cash</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentReceptionHandovers as $handover)
            <tr>
              <td>{{ $handover->staff->name ?? 'Reception' }}</td>
              <td>{{ optional($handover->closed_at)->format('d M, H:i') ?? '-' }}</td>
              <td class="text-right font-weight-bold">{{ number_format($handover->amount_submitted_tzs) }}</td>
              <td class="text-right text-nowrap">
                <a href="{{ route('cashier.shift-reports.pdf', $handover->id) }}" class="btn btn-xs btn-outline-danger" title="Print PDF">
                  <i class="fa fa-print"></i>
                </a>
                <a href="{{ route('cashier.shift-handovers.sales', $handover->id) }}" class="btn btn-xs btn-outline-info" title="Details">
                  <i class="fa fa-list"></i>
                </a>
                <form action="{{ route('cashier.shift-handovers.acknowledge', $handover->id) }}" method="POST" class="d-inline">
                  @csrf
                  <button type="submit" class="btn btn-xs btn-success"
                    onclick="return confirm('Confirm you received cash TZS {{ number_format($handover->amount_submitted_tzs) }} from {{ $handover->staff->name ?? 'Reception' }}.')">
                    Receive
                  </button>
                </form>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="4" class="text-center text-muted py-4">No Reception cash waiting.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
