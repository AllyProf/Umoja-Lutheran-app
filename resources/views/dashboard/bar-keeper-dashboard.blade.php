@extends('dashboard.layouts.app')

@section('content')
  <style>
    .shift-management-card {
      background: #ffffff;
      border-radius: 15px;
      padding: 20px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.05);
      margin-bottom: 25px;
      border-left: 5px solid #009688;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .shift-status-info h1 {
      font-size: 1.5rem;
      font-weight: 700;
      color: #333;
      margin-bottom: 5px;
    }
    .shift-btn {
      padding: 12px 25px;
      border-radius: 10px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
      border: none;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 10px;
      color: white !important;
    }
    .shift-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }
    .shift-btn-open {
      background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }
    .shift-btn-close {
      background: linear-gradient(135deg, #FF512F 0%, #DD2476 100%);
    }
    .pulse-animation {
      animation: pulse-green 2s infinite;
    }
    @keyframes pulse-green {
      0% { box-shadow: 0 0 0 0 rgba(56, 239, 125, 0.7); }
      70% { box-shadow: 0 0 0 10px rgba(56, 239, 125, 0); }
      100% { box-shadow: 0 0 0 0 rgba(56, 239, 125, 0); }
    }
  </style>

  @php
    $isProxySeller = $isProxySeller ?? false;
    $activeCounterShifts = $activeCounterShifts ?? collect();
  @endphp
  <div class="shift-management-card shadow-sm">
    <div class="shift-status-info">
      <h1><i class="fa fa-dashboard text-primary"></i> {{ $isProxySeller ? 'Restaurant Counter (Proxy)' : 'Counter Dashboard' }}</h1>
      @if($isProxySeller)
        @if($activeShift)
          <div class="d-flex align-items-center flex-wrap">
            <span class="badge badge-success pulse-animation mr-2" style="width: 10px; height: 10px; border-radius: 50%; padding: 0;">&nbsp;</span>
            <p class="text-success mb-0 font-weight-bold">
              <i class="fa fa-user"></i> Selling on behalf of:
              <strong>{{ $activeShift->staff->name ?? 'Counter' }}</strong>
              <span class="text-muted font-weight-normal small ml-1">(shift {{ $activeShift->opened_at->format('H:i') }})</span>
            </p>
          </div>
        @else
          <p class="text-danger mb-0 font-weight-bold">
            <i class="fa fa-warning"></i> Select a counter with an open shift to sell.
          </p>
        @endif
      @elseif($activeShift)
        <div class="d-flex align-items-center">
          <span class="badge badge-success pulse-animation mr-2" style="width: 10px; height: 10px; border-radius: 50%; padding: 0;">&nbsp;</span>
          <p class="text-success mb-0 font-weight-bold">
            <i class="fa fa-clock-o"></i> Shift Active: {{ $activeShift->opened_at->format('H:i') }}
            <span class="text-muted font-weight-normal small ml-1">({{ $activeShift->opened_at->diffForHumans() }})</span>
          </p>
        </div>
      @else
        <p class="text-danger mb-0 font-weight-bold">
          <i class="fa fa-warning pulse-animation"></i> Shift is CLOSED. Please open to start sales.
        </p>
      @endif
    </div>
    <div>
      @if($isProxySeller)
        {{-- No personal shift open/close for restaurant manager --}}
      @elseif($activeShift)
        <button class="shift-btn shift-btn-close shadow-sm" onclick="openShiftClosureModal()">
          <i class="fa fa-power-off"></i> CLOSE SHIFT
        </button>
      @else
        <button class="shift-btn shift-btn-open shadow-sm" onclick="openShift()">
          <i class="fa fa-play-circle fa-lg"></i> OPEN SHIFT
        </button>
      @endif
    </div>
  </div>

  @if($isProxySeller)
  <div class="tile mb-4">
    <div class="tile-body">
      <label class="font-weight-bold mb-2 d-block"><i class="fa fa-exchange"></i> Counter you are selling for</label>
      @if($activeCounterShifts->isEmpty())
        <div class="alert alert-warning mb-0">No counter has an active shift right now. A counter must open a shift first.</div>
      @else
        <div class="form-row align-items-end">
          <div class="col-md-8">
            <select id="proxyCounterShift" class="form-control form-control-lg">
              <option value="">— Select counter —</option>
              @foreach($activeCounterShifts as $shiftOpt)
                <option value="{{ $shiftOpt->id }}" {{ ($activeShift && (int)$activeShift->id === (int)$shiftOpt->id) ? 'selected' : '' }}>
                  {{ $shiftOpt->staff->name ?? 'Counter' }} — since {{ optional($shiftOpt->opened_at)->format('H:i') }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <button type="button" class="btn btn-primary btn-lg btn-block" onclick="selectProxyCounterShift()">
              <i class="fa fa-check"></i> Tumia Counter Hii
            </button>
          </div>
        </div>
        <small class="text-muted d-block mt-2">Orders will appear on that counter's shift and show that <strong>you</strong> placed them.</small>
      @endif
    </div>
  </div>
  @endif

  <ul class="app-breadcrumb breadcrumb mb-4">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
  </ul>
  </div>

  <!-- Statistics Cards -->
  <div class="row mb-4">
    <div class="col-md-3">
      <div class="widget-small success coloured-icon">
        <i class="icon fa fa-money fa-3x"></i>
        <div class="info">
          <h4 style="font-size: 13px; text-transform: uppercase; font-weight: 700; color: #555;">Today's Revenue</h4>
          <p style="font-size: 20px; color: #1e7e34; margin-bottom: 0;"><b>{{ number_format($todayRevenue) }} TZS</b></p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="widget-small danger coloured-icon">
        <i class="icon fa fa-cutlery fa-3x"></i>
        <div class="info">
          <h4 style="font-size: 13px; text-transform: uppercase; font-weight: 700; color: #555;">Pending Orders</h4>
          <p style="font-size: 22px; color: #dc3545; margin-bottom: 0;"><b>{{ $totalPendingOrders }}</b></p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="widget-small primary coloured-icon">
        <i class="icon fa fa-truck fa-3x"></i>
        <div class="info">
          <h4 style="font-size: 13px; text-transform: uppercase; font-weight: 700; color: #555;">Incoming Stock</h4>
          <p style="font-size: 22px; color: #007bff; margin-bottom: 0;"><b>{{ $totalPending }}</b></p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="widget-small warning coloured-icon">
        <i class="icon fa fa-cubes fa-3x"></i>
        <div class="info">
          <h4 style="font-size: 13px; text-transform: uppercase; font-weight: 700; color: #555;">Total Products</h4>
          <p style="font-size: 22px; color: #e67e22; margin-bottom: 0;"><b>{{ $totalProducts }}</b></p>
        </div>
      </div>
    </div>
  </div>

  <!-- Pending Guest Orders -->
  <div class="row mb-4" id="orders">
    <div class="col-md-12">
      <div class="tile">
        <div class="tile-title-w-btn">
          <h3 class="title">Pending Guest Orders</h3>
          <div class="btn-group">
            @if(in_array(strtolower(auth()->guard('staff')->user()->role ?? ''), ['manager', 'super_admin']))
              <a href="{{ route('bar-keeper.order-summary') }}" class="btn btn-info mr-2">
                <i class="fa fa-bar-chart"></i> View Order Summary
              </a>
            @endif
            <button class="btn btn-primary {{ !$activeShift ? 'disabled' : '' }}" {{ !$activeShift ? 'disabled' : '' }}
              onclick="openWalkInModal()"><i class="fa fa-plus"></i> New Sale</button>
          </div>
        </div>

        @if($pendingOrders->count() > 0)
          <div class="table-responsive">
            <table class="table table-hover table-bordered">
              <thead>
                <tr>
                  <th>Time</th>
                  <th>By</th>
                  <th>Guest / Room</th>
                  <th>Item</th>
                  <th>Qty</th>
                  <th>Price (TZS)</th>
                  <th>Notes</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @php
                  $groupedOrders = $pendingOrders->groupBy(function ($item) {
                    if ($item->is_walk_in) {
                      return 'w_' . ($item->walk_in_name ?? 'General');
                    }
                    return 'b_' . ($item->booking_id ?? 'unknown');
                  });
                @endphp

                @foreach($groupedOrders as $groupKey => $orderGroup)
                  @php
                    $first = $orderGroup->first();
                    $counterPending = $orderGroup->where('payment_status', 'pending');
                    $receptionUnpaid = $orderGroup->where('payment_status', 'room_charge');
                    $receptionPaid = $orderGroup->filter(fn($o) => $o->wasPaidAtReception());
                    $guestTotal = $counterPending->sum('total_price_tsh');
                    $receptionTotal = $receptionUnpaid->sum('total_price_tsh');
                    $receptionPaidTotal = $receptionPaid->sum('total_price_tsh');
                    $latestRequest = $orderGroup->sortByDesc('requested_at')->first()->requested_at;
                    $allOnReception = $orderGroup->every(fn($o) => $o->payment_status === 'room_charge' || $o->wasPaidAtReception());
                    $allReceptionPaid = $receptionUnpaid->isEmpty() && $receptionPaid->isNotEmpty()
                        && $orderGroup->every(fn($o) => $o->wasPaidAtReception() || $o->countsAsCounterCash());
                  @endphp
                  <tr style="border-top: 3px solid #009688;">
                    <td style="vertical-align: top;">
                      <strong>{{ $latestRequest->format('H:i') }}</strong><br>
                      <small class="text-muted">({{ $latestRequest->diffForHumans() }})</small>
                    </td>
                    <td style="vertical-align: top;">
                      @php
                        $by = 'N/A';
                        if ($first->reception_notes && str_contains($first->reception_notes, 'Waiter: ')) {
                          $parts = explode('Waiter: ', $first->reception_notes);
                          $byParts = explode(' - Msg:', $parts[1] ?? '');
                          $by = $byParts[0] ?? 'Waiter';
                        }
                       @endphp
                      <span class="badge badge-info">{{ $by }}</span>
                    </td>
                    <td style="vertical-align: top;">
                      @if($first->is_walk_in)
                        <span class="badge badge-secondary mb-1">WALK-IN</span><br>
                        <strong>{{ $first->walk_in_name ?? 'General Walk-in' }}</strong>
                      @else
                        <span class="badge badge-primary mb-1">Room {{ $first->booking?->room?->room_number ?? 'N/A' }}</span><br>
                        <strong>{{ $first->booking?->guest_name ?? 'Guest' }}</strong>
                        @if($receptionTotal > 0 || $receptionPaidTotal > 0)
                          <br><span class="badge badge-info mt-1" style="font-size: 10px;">PAY AT RECEPTION</span>
                        @endif
                      @endif
                    </td>
                    <td colspan="4" class="p-0">
                      <table class="table table-sm mb-0" style="background: transparent;">
                        @foreach($orderGroup as $order)
                          <tr style="background: transparent;">
                            <td style="width: 35%; border-top: none;">
                              <strong>{{ $order->service_specific_data['item_name'] ?? $order->service?->name ?? 'Deleted Service' }}</strong>
                              @php $placedBy = $order->service_specific_data['placed_by_name'] ?? null; @endphp
                              @if($placedBy)
                                <div><small class="text-info"><i class="fa fa-user"></i> Placed by: {{ $placedBy }}</small></div>
                              @endif
                              @if($order->payment_status === 'pending')
                                <br><span class="badge badge-warning" style="font-size: 9px;">UNPAID</span>
                              @elseif($order->payment_status === 'room_charge')
                                <br><span class="badge badge-info" style="font-size: 9px;">PAY AT RECEPTION</span>
                                <span class="badge badge-warning" style="font-size: 9px;">UNPAID</span>
                              @elseif($order->wasPaidAtReception())
                                <br><span class="badge badge-info" style="font-size: 9px;">PAY AT RECEPTION</span>
                                <span class="badge badge-success" style="font-size: 9px;">PAID</span>
                                <br><small class="text-muted">Not counter cash</small>
                              @elseif($order->payment_status === 'paid')
                                <br><span class="badge badge-success" style="font-size: 9px;">PAID</span>
                              @endif
                            </td>
                            <td style="width: 10%; border-top: none;">x {{ $order->quantity }}</td>
                            <td style="width: 20%; border-top: none;">{{ number_format($order->total_price_tsh) }}</td>
                            <td style="width: 35%; border-top: none;">
                              @php
                                $note = $order->guest_request;
                                $recNote = $order->reception_notes ?? '';

                                // Extract message part if exists
                                $msgFromRec = '';
                                if (str_contains($recNote, '- Msg: ')) {
                                  $parts = explode('- Msg: ', $recNote);
                                  $msgFromRec = $parts[1] ?? '';
                                }

                                // Use extracted message if no guest request
                                if (!$note)
                                  $note = $msgFromRec;

                                // Append "Served by" info if present
                                if (str_contains($recNote, '| Served by')) {
                                  $servedInfo = substr($recNote, strpos($recNote, '| Served by') + 2);
                                  if (!str_contains($note, $servedInfo)) {
                                    $note = $note ? ($note . ' | ' . $servedInfo) : $servedInfo;
                                  }
                                }
                              @endphp
                              <span class="text-muted" style="font-size: 11px;">
                                {!! str_replace('(Pending Payment)', '<span class="text-danger font-weight-bold" style="color: #dc3545 !important;">(Pending Payment)</span>', e($note ?: '-')) !!}
                              </span>

                              @if($order->status === 'pending' || $order->status === 'approved')
                                <button class="btn btn-xs btn-outline-info pull-right ml-1 {{ !$activeShift ? 'disabled' : '' }}"
                                  {{ !$activeShift ? 'disabled' : '' }}
                                  onclick="serveOrder({{ $order->id }}, '{{ $order->service_specific_data['item_name'] ?? 'Item' }}')"
                                  title="Mark as Served (Taken)">
                                  <i class="fa fa-hand-holding-water"></i> Serve
                                </button>

                                <button class="btn btn-xs btn-outline-danger pull-right ml-1 {{ !$activeShift ? 'disabled' : '' }}"
                                  {{ !$activeShift ? 'disabled' : '' }}
                                  onclick="cancelOrder({{ $order->id }}, '{{ $order->service_specific_data['item_name'] ?? 'Item' }}')"
                                  title="Cancel Order">
                                  <i class="fa fa-times-circle"></i> Cancel
                                </button>
                              @endif

                              @if(!$order->is_walk_in && $order->payment_status === 'pending' && in_array($order->status, ['pending', 'approved', 'completed'], true))
                                <button class="btn btn-xs btn-primary pull-right {{ !$activeShift ? 'disabled' : '' }}" {{ !$activeShift ? 'disabled' : '' }} onclick="completeOrder({{ $order->id }}, 'room_charge')"
                                  title="Charge to Room">
                                  <i class="fa fa-bed"></i> Room Charge
                                </button>
                              @endif
                            </td>
                          </tr>
                        @endforeach
                        @if($guestTotal > 0)
                          <tr style="background: #e0f2f1;">
                            <td colspan="2" style="border-top: 1px solid #b2dfdb; text-align: right;"><strong>Total
                                Pending (Counter):</strong></td>
                            <td colspan="2" style="border-top: 1px solid #b2dfdb;"><strong>{{ number_format($guestTotal) }}
                                TZS</strong></td>
                          </tr>
                        @endif
                        @if($receptionTotal > 0)
                          <tr style="background: #fff3cd;">
                            <td colspan="2" style="border-top: 1px solid #ffeaa7; text-align: right;"><strong>Unpaid at
                                Reception:</strong></td>
                            <td colspan="2" style="border-top: 1px solid #ffeaa7;"><strong>{{ number_format($receptionTotal) }}
                                TZS</strong>
                              <span class="badge badge-warning ml-1">UNPAID</span>
                            </td>
                          </tr>
                        @endif
                        @if($receptionPaidTotal > 0)
                          <tr style="background: #e8f5e9;">
                            <td colspan="2" style="border-top: 1px solid #c8e6c9; text-align: right;"><strong>Paid at
                                Reception:</strong></td>
                            <td colspan="2" style="border-top: 1px solid #c8e6c9;">
                              <span class="badge badge-success">PAID</span>
                              <br><small class="text-muted">Not counted in counter cash</small>
                            </td>
                          </tr>
                        @endif
                      </table>
                    </td>
                    <td style="vertical-align: top;">
                      <div class="btn-group-vertical btn-group-sm w-100">
                        @php
                          $printUrl = route('bar-keeper.orders.print-group', [
                            'is_walk_in' => $first->is_walk_in ? 1 : 0,
                            'identifier' => $first->is_walk_in ? $first->walk_in_name : $first->booking_id
                          ]);
                        @endphp

                        <button class="btn btn-sm btn-info mb-1"
                          onclick="window.open('{{ $printUrl }}', 'Print', 'width=800,height=600')" title="Print Group Bill">
                          <i class="fa fa-print"></i> Print Bill
                        </button>

                        @if($guestTotal > 0)
                          <button class="btn btn-success btn-lg btn-block shadow-sm {{ !$activeShift ? 'disabled' : '' }}" {{ !$activeShift ? 'disabled' : '' }}
                            onclick="openPaymentModal({{ $first->id }}, {{ $guestTotal }}, {{ $first->is_walk_in ? 1 : 0 }})">
                            <i class="fa fa-money"></i> PAY: {{ number_format($guestTotal) }}
                          </button>
                        @elseif($receptionTotal > 0)
                          <div class="alert alert-warning py-2 px-2 mb-0 text-center" style="font-size: 11px;">
                            <i class="fa fa-building"></i><br>
                            <strong>Pay at Reception</strong><br>
                            <span class="badge badge-warning">UNPAID</span>
                          </div>
                        @elseif($receptionPaidTotal > 0 && $guestTotal <= 0)
                          <div class="alert alert-success py-2 px-2 mb-0 text-center" style="font-size: 11px;">
                            <i class="fa fa-building"></i><br>
                            <strong>Pay at Reception</strong><br>
                            <span class="badge badge-success">PAID</span>
                            <div class="small mt-1">Cash via reception → cashier</div>
                          </div>
                        @endif
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <div class="text-center" style="padding: 30px;">
            <i class="fa fa-coffee fa-4x text-muted mb-3"></i>
            <h3>No Pending Orders</h3>
            <p class="text-muted">New orders from guests will appear here.</p>
          </div>
        @endif
      </div>
    </div>
  </div>

  <!-- Shift / Today's Verified Sales -->
  <style>
    #sales-history .sales-table thead th,
    #sales-history .sales-table tfoot th {
      background: #f1f3f5 !important;
      color: #1c1c1c !important;
      border-color: #ced4da !important;
    }
    #sales-history .sales-table td,
    #sales-history .sales-table th {
      color: #1c1c1c !important;
      border-color: #ced4da !important;
    }
    #sales-history h5 {
      color: inherit;
    }
    html[data-theme="dark"] #sales-history .sales-table thead th,
    html[data-theme="dark"] #sales-history .sales-table tfoot th {
      background: #21262d !important;
      color: #f0f6fc !important;
      border-color: #30363d !important;
    }
    html[data-theme="dark"] #sales-history .sales-table td,
    html[data-theme="dark"] #sales-history .sales-table th {
      background: #0d1117 !important;
      color: #e6edf3 !important;
      border-color: #30363d !important;
    }
    html[data-theme="dark"] #sales-history .sales-table tbody tr:hover td {
      background: #161b22 !important;
    }
    html[data-theme="dark"] #sales-history h5,
    html[data-theme="dark"] #sales-history .title,
    html[data-theme="dark"] #sales-history .text-muted {
      color: #e6edf3 !important;
    }

    /* Prior-debt sale rows */
    .row-prior-debt > td {
      background: #fff8e6 !important;
      color: #1c1917 !important;
      border-color: #f0e0b2 !important;
    }
    .row-prior-debt .text-muted { color: #78716c !important; }
    html[data-theme="dark"] .row-prior-debt > td {
      background: #3d2e12 !important;
      color: #fde68a !important;
      border-color: #854d0e !important;
    }
    html[data-theme="dark"] .row-prior-debt .text-muted {
      color: #fbbf24 !important;
    }
    html[data-theme="dark"] .row-prior-debt .badge-warning {
      background: #854d0e !important;
      color: #fef3c7 !important;
    }
  </style>
  <div class="row mb-4" id="sales-history">
    <div class="col-md-12">
      <div class="tile shadow-sm" style="border-top: 4px solid #28a745;">
        <div class="tile-title-w-btn">
          <h3 class="title">
            <i class="fa fa-history text-success"></i>
            {{ !empty($activeShift) ? 'Shift Sales (Verification History)' : "Today's Sales (Verification History)" }}
          </h3>
          <span class="badge badge-success">{{ $salesForSummary->count() }} Orders · {{ number_format($salesForSummary->sum('total_price_tsh')) }} TZS</span>
        </div>

        @if($salesForSummary->count() > 0)
          <div class="mb-3">
            <h5 class="mb-2"><i class="fa fa-users"></i> Summary by guest</h5>
            <div class="table-responsive">
              <table class="table table-sm table-bordered mb-0 sales-table">
                <thead>
                  <tr>
                    <th>Guest / Room</th>
                    <th class="text-center">Orders</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Total (TZS)</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($salesSummary as $row)
                    <tr>
                      <td><strong>{{ $row['label'] }}</strong></td>
                      <td class="text-center">{{ $row['orders'] }}</td>
                      <td class="text-center">{{ number_format($row['qty']) }}</td>
                      <td class="text-right font-weight-bold">{{ number_format($row['total']) }}</td>
                    </tr>
                  @endforeach
                </tbody>
                <tfoot>
                  <tr>
                    <th>All guests</th>
                    <th class="text-center">{{ $salesForSummary->count() }}</th>
                    <th class="text-center">{{ number_format($salesForSummary->sum('quantity')) }}</th>
                    <th class="text-right">{{ number_format($salesForSummary->sum('total_price_tsh')) }}</th>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>

          <h5 class="mb-2"><i class="fa fa-list"></i> Sale details</h5>
          <div class="table-responsive">
            <table class="table table-sm table-hover table-bordered sales-table">
              <thead>
                <tr>
                  <th>Time</th>
                  <th>Guest/Room</th>
                  <th>Service/Item</th>
                  <th>Qty</th>
                  <th>Total (TZS)</th>
                  <th>Method</th>
                  <th>Reference</th>
                </tr>
              </thead>
              <tbody>
                @foreach($completedSales as $sale)
                  @php $fromPriorDebt = $sale->wasPaidFromPriorDebt(); @endphp
                  <tr class="{{ $fromPriorDebt ? 'row-prior-debt' : '' }}">
                    <td>{{ \Carbon\Carbon::parse($sale->completed_at)->format('H:i') }}</td>
                    <td>
                      @if($sale->is_walk_in)
                        <span class="badge badge-info">Walk-in: {{ $sale->walk_in_name }}</span>
                      @else
                        <strong>Room {{ $sale->booking?->room?->room_number ?? 'N/A' }}</strong><br>
                        <small>{{ $sale->booking?->guest_name ?? '' }}</small>
                      @endif
                      @if($fromPriorDebt)
                        <br><span class="badge badge-warning" style="font-size:9px;">PAID FROM PRIOR DEBT</span>
                        @if(!empty($sale->service_specific_data['original_order_at']))
                          <br><small class="text-muted">Ordered: {{ \Carbon\Carbon::parse($sale->service_specific_data['original_order_at'])->format('d M Y H:i') }}</small>
                        @endif
                      @endif
                    </td>
                    <td>
                      {{ $sale->service_specific_data['item_name'] ?? $sale->service?->name ?? 'Deleted Service' }}
                      @if(!empty($sale->service_specific_data['placed_by_name']))
                        <br><small class="text-info"><i class="fa fa-user"></i> Placed by: {{ $sale->service_specific_data['placed_by_name'] }}</small>
                      @endif
                    </td>
                    <td>{{ $sale->quantity }}</td>
                    <td class="font-weight-bold">{{ number_format($sale->total_price_tsh) }}</td>
                    <td>
                      <span class="badge badge-outline-secondary">{{ strtoupper($sale->payment_method) }}</span>
                      @if($fromPriorDebt)
                        <br><span class="badge badge-warning mt-1" style="font-size:9px;">Debt collection</span>
                      @endif
                    </td>
                    <td><small class="text-muted">{{ $sale->payment_reference ?: '-' }}</small></td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <div class="d-flex flex-wrap justify-content-between align-items-center mt-3">
            <small class="text-muted mb-2">
              Showing {{ $completedSales->firstItem() }}–{{ $completedSales->lastItem() }} of {{ $completedSales->total() }} sales
            </small>
            <div class="mb-2">
              {{ $completedSales->onEachSide(1)->links() }}
            </div>
          </div>
        @else
          <div class="text-center py-4">
            <p class="text-muted"><i class="fa fa-info-circle"></i> No completed sales yet in this shift.</p>
          </div>
        @endif
      </div>
    </div>
  </div>

  <!-- Pending Transfers -->
  <div class="row" id="transfers">
    <div class="col-md-12">
      <div class="tile">
        <div class="tile-title-w-btn">
          <h3 class="title">Pending Stock Transfers</h3>
        </div>

        @if($pendingTransfers->count() > 0)
          <div class="table-responsive">
            <table class="table table-hover table-bordered">
              <thead>
                <tr>
                  <th>Transfer Ref</th>
                  <th>Transfer Date</th>
                  <th>Product</th>
                  <th>Variant</th>
                  <th>Quantity</th>
                  <th>Transferred By</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($pendingTransfers as $transfer)
                  <tr>
                    <td><strong>{{ $transfer->transfer_reference }}</strong></td>
                    <td>{{ $transfer->transfer_date->format('M d, Y') }}</td>
                    <td><strong>{{ $transfer->product->name ?? 'Deleted Product' }}</strong></td>
                    <td>{{ $transfer->productVariant->measurement ?? 'N/A' }} ({{ $transfer->productVariant->packaging ?? 'N/A' }})</td>
                    <td>
                      {{ number_format($transfer->quantity_transferred) }}
                      <span
                        class="text-muted">{{ $transfer->quantity_unit === 'packages' ? ($transfer->productVariant->packaging_name ?? 'packages') : 'PIC' }}</span>
                    </td>
                    <td>{{ $transfer->transferredBy->name ?? 'N/A' }}</td>
                    <td>
                      <div class="btn-group">
                        <button class="btn btn-sm btn-success" onclick="receiveTransfer({{ $transfer->id }})"
                          title="Mark as Received">
                          <i class="fa fa-check"></i> Receive
                        </button>
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <div class="d-flex justify-content-center mt-4">
            {{ $pendingTransfers->links() }}
          </div>
        @else
          <div class="text-center" style="padding: 50px;">
            <i class="fa fa-check-circle fa-5x text-success mb-3"></i>
            <h3>All Transfers Received</h3>
            <p class="text-muted">No pending transfers at the moment.</p>
          </div>
        @endif
      </div>
    </div>
  </div>

  <!-- Recent Completed Transfers -->
  @if($completedTransfers->count() > 0)
    <div class="row mt-4">
      <div class="col-md-12">
        <div class="tile">
          <div class="tile-title-w-btn">
            <h3 class="title">Recent Completed Transfers</h3>
          </div>

          <div class="table-responsive">
            <table class="table table-hover table-bordered">
              <thead>
                <tr>
                  <th>Transfer Ref</th>
                  <th>Product</th>
                  <th>Variant</th>
                  <th>Quantity</th>
                  <th>Received At</th>
                  <th>Transferred By</th>
                </tr>
              </thead>
              <tbody>
                @foreach($completedTransfers as $transfer)
                  <tr>
                    <td><strong>{{ $transfer->transfer_reference }}</strong></td>
                    <td><strong>{{ $transfer->product->name ?? 'Deleted Product' }}</strong></td>
                    <td>{{ $transfer->productVariant->measurement ?? 'N/A' }} ({{ $transfer->productVariant->packaging ?? 'N/A' }})</td>
                    <td>
                      {{ number_format($transfer->quantity_transferred) }}
                      <span
                        class="text-muted">{{ $transfer->quantity_unit === 'packages' ? ($transfer->productVariant->packaging_name ?? 'packages') : 'PIC' }}</span>
                    </td>
                    <td>{{ $transfer->received_at ? $transfer->received_at->format('M d, Y h:i A') : 'N/A' }}</td>
                    <td>{{ $transfer->transferredBy->name ?? 'N/A' }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  @endif

  {{-- Active Ceremonies Section --}}
  <div class="row mt-4">
    <div class="col-md-12">
      <div class="tile shadow-sm border-0 mb-4">
        <div class="tile-title-w-btn">
          <h3 class="title"><i class="fa fa-birthday-cake mr-2 text-primary"></i> Active Ceremonies</h3>
          <div class="btn-group">
            <span class="badge badge-info p-2">{{ $activeCeremonies->count() }} ACTIVE TODAY</span>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Reference</th>
                <th>Guest Name</th>
                <th>Ceremony Type</th>
                <th>Pax</th>
                <th>Notes</th>
                <th class="text-center">Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse($activeCeremonies as $ceremony)
                <tr>
                  <td><span class="badge badge-light border">{{ $ceremony->service_reference }}</span></td>
                  <td><strong>{{ $ceremony->guest_name }}</strong></td>
                  <td>{{ $ceremony->service_type_name }}</td>
                  <td>{{ $ceremony->number_of_people }}</td>
                  <td><small class="text-muted">{{ Str::limit($ceremony->notes, 40) }}</small></td>
                  <td class="text-center">
                    @php
                      $unpaidUsage = $ceremony->serviceRequests
                        ->where('payment_status', 'pending')
                        ->filter(function ($req) {
                          return $req->service && in_array($req->service->category, ['alcoholic_beverage', 'non_alcoholic_beverage', 'water', 'juices', 'energy_drinks', 'soft_drinks', 'beers', 'wines', 'spirits', 'cocktails', 'drinks', 'liquor', 'food', 'restaurant', 'traditional', 'bites', 'snacks', 'chai', 'fruit_salad']);
                        })
                        ->sum('total_price_tsh');
                    @endphp
                    <div class="btn-group">
                      <button class="btn btn-primary btn-sm rounded-pill px-3 {{ !$activeShift ? 'disabled' : '' }}" {{ !$activeShift ? 'disabled' : '' }}
                        onclick="openWalkInModal(null, '{{ $ceremony->guest_name }}', {{ $ceremony->id }})">
                        <i class="fa fa-plus-circle mr-1"></i> Add Usage
                      </button>
                      <a href="{{ route('bar-keeper.day-services.docket', $ceremony->id) }}" target="_blank"
                        class="btn btn-secondary btn-sm rounded-pill px-3 ml-2" title="Print Docket">
                        <i class="fa fa-print mr-1"></i> Print Docket
                      </a>
                      @if($unpaidUsage > 0)
                        <button class="btn btn-success btn-sm rounded-pill px-3 ml-2 {{ !$activeShift ? 'disabled' : '' }}" {{ !$activeShift ? 'disabled' : '' }}
                          onclick="openCeremonyPaymentModal({{ $ceremony->id }}, '{{ $ceremony->guest_name }}', {{ $unpaidUsage }})">
                          <i class="fa fa-money mr-1"></i> Settle Bill ({{ number_format($unpaidUsage) }} TZS)
                        </button>
                      @endif
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center py-4 text-muted italic">
                    <i class="fa fa-info-circle mr-1"></i> No active ceremonies registered for today.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Quick Operational Actions -->
  <div class="row mt-4">
    <div class="col-md-12">
      <div class="tile shadow-sm h-100">
        <h3 class="tile-title">Quick Operational Actions</h3>
        <div class="row">
          <div class="col-md-3 mb-3">
            <button onclick="openWalkInModal()"
              class="btn btn-primary btn-block p-4 shadow-sm h-100 d-flex flex-column align-items-center justify-content-center {{ !$activeShift ? 'disabled' : '' }}"
              {{ !$activeShift ? 'disabled' : '' }}>
              <i class="fa fa-glass fa-2x mb-2"></i>
              <span>New Sale</span>
            </button>
          </div>
          <div class="col-md-3 mb-3">
            <a href="{{ route('bar-keeper.stock.index') }}"
              class="btn btn-info btn-block p-4 shadow-sm h-100 d-flex flex-column align-items-center justify-content-center {{ !$activeShift ? 'disabled' : '' }}"
              {{ !$activeShift ? 'onclick="return false;"' : '' }}>
              <i class="fa fa-cubes fa-2x mb-2"></i>
              <span>Inventory</span>
            </a>
          </div>
          <div class="col-md-3 mb-3">
            <a href="{{ route('bar-keeper.reports') }}"
              class="btn btn-danger btn-block p-4 shadow-sm h-100 d-flex flex-column align-items-center justify-content-center {{ !$activeShift ? 'disabled' : '' }}"
              {{ !$activeShift ? 'onclick="return false;"' : '' }}>
              <i class="fa fa-bar-chart fa-2x mb-2"></i>
              <span>Daily Stock Sheet</span>
            </a>
          </div>
          <div class="col-md-3 mb-3">
            <a href="{{ route('bar-keeper.transfers.index') }}"
              class="btn btn-warning btn-block p-4 shadow-sm h-100 d-flex flex-column align-items-center justify-content-center text-white {{ !$activeShift ? 'disabled' : '' }}"
              {{ !$activeShift ? 'onclick="return false;"' : '' }}>
              <i class="fa fa-exchange fa-2x mb-2"></i>
              <span>Stock Transfers</span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
  </div>

  <!-- Walk-in POS Modal -->
  <div class="modal fade" id="walkInModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="max-width: 900px;">
      <div class="modal-content border-0 shadow-lg">
        <div class="modal-header bg-primary text-white border-0 py-3">
          <h5 class="modal-title font-weight-bold" id="posModalTitle">
            <i class="fa fa-shopping-cart mr-2"></i> New Walk-in Order
          </h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body p-0">
          <div class="row no-gutters">
            <!-- Item Selection -->
            <div class="col-md-7 border-right" style="max-height: 70vh; overflow-y: auto; background: #fff;">
              <div class="p-3 sticky-top bg-white border-bottom shadow-sm">
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text bg-white border-right-0"><i class="fa fa-search text-muted"></i></span>
                  </div>
                  <input type="text" id="itemSearch" class="form-control border-left-0" placeholder="Search drinks..."
                    onkeyup="filterPosItems()">
                </div>

                <div class="d-flex flex-wrap pb-2" id="posCategoryTabs">
                  <button class="btn btn-sm btn-primary rounded-pill mr-2 mb-2 px-3 active"
                    onclick="filterByCategory('all', this)">All Items</button>
                  <button class="btn btn-sm btn-outline-primary rounded-pill mr-2 mb-2 px-3"
                    onclick="filterByCategory('food', this)">Food</button>
                  <button class="btn btn-sm btn-outline-primary rounded-pill mr-2 mb-2 px-3"
                    onclick="filterByCategory('chai', this)">Chai / Tea</button>
                  <button class="btn btn-sm btn-outline-primary rounded-pill mr-2 mb-2 px-3"
                    onclick="filterByCategory('bites', this)">Bites</button>
                  <button class="btn btn-sm btn-outline-primary rounded-pill mr-2 mb-2 px-3"
                    onclick="filterByCategory('drinks', this)">Drinks</button>
                  <button class="btn btn-sm btn-outline-primary rounded-pill mr-2 mb-2 px-3"
                    onclick="filterByCategory('juices', this)">Juices</button>
                  <button class="btn btn-sm btn-outline-primary rounded-pill mr-2 mb-2 px-3"
                    onclick="filterByCategory('traditional', this)">Traditional</button>
                </div>
              </div>

              <div class="p-3" id="posItemList">
                <div class="row">
                  {{-- Render Dynamic Food Bases --}}
                  @if(isset($foodBases))
                    @foreach($foodBases as $baseName => $sides)
                      <div class="col-6 mb-3 pos-item-card" data-name="{{ strtolower($baseName) }}" data-category="food">
                        <div class="card h-100 border-0 shadow-sm rounded-lg transition-all"
                          style="background: #fff; overflow: hidden; position: relative; border: 1px solid #17a2b8 !important;">
                          <div class="card-body p-3 d-flex flex-column h-100">
                            <div class="bg-light rounded p-2 mb-2 mx-auto d-flex align-items-center justify-content-center"
                              style="width: 100px; height: 100px;">
                              <img
                                src="{{ isset($sides[0]['image']) && $sides[0]['image'] ? asset('storage/' . $sides[0]['image']) : 'https://img.icons8.com/color/144/restaurant-.png' }}"
                                class="img-fluid" style="max-height: 80px; object-fit: contain;">
                            </div>

                            <div class="mb-3 text-center">
                              <h6 class="font-weight-bold mb-1" style="font-size: 0.95rem; line-height: 1.2;">{{ $baseName }}
                              </h6>
                              <span class="badge badge-info bg-light text-primary border border-primary px-2 py-1 mt-1"
                                style="font-size: 0.7rem; letter-spacing: 0.5px;">+ {{ count($sides) }} Sides</span>
                            </div>

                            <div class="mt-auto">
                              <button class="btn btn-sm btn-primary rounded px-3 py-2 w-100 font-weight-bold shadow-sm"
                                onclick="showFoodBuilderModal('{{ $baseName }}')">
                                <i class="fa fa-hand-pointer-o mr-1"></i> Choose Add-On
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>
                    @endforeach
                  @endif

                  @foreach($drinks as $drink)
                    @php
                      $catClass = strtolower($drink->category ?? 'food');

                      // Normalize drink product categories
                      if (in_array($catClass, ['non alcoholic beverage', 'non_alcoholic_beverage', 'soft drinks', 'soft_drinks', 'water', 'energy_drinks', 'whiskey', 'liquor', 'beers', 'wines']) || strpos($catClass, 'drinks') !== false)
                        $catClass = 'drinks';
                      if (strpos($catClass, 'juice') !== false)
                        $catClass = 'juices';

                      // For standalone food recipes: use DB category if already specific, else fall back to name keywords
                      if (!isset($drink->is_product) || $drink->is_product == false) {
                        // If DB category is already one of our known tabs, use it directly
                        $knownTabs = ['food', 'bites', 'chai', 'juices', 'drinks', 'traditional'];
                        if (!in_array($catClass, $knownTabs)) {
                          $catClass = 'food'; // default fallback
                        }

                        // Only do name-based mapping when DB category is generic 'food'
                        if ($catClass === 'food') {
                          $lowName = strtolower($drink->name);

                          if (strpos($lowName, 'samosa') !== false || strpos($lowName, 'kababu') !== false || strpos($lowName, 'donati') !== false || strpos($lowName, 'andazi') !== false || strpos($lowName, 'shankeli') !== false || strpos($lowName, 'sausage') !== false || strpos($lowName, 'mayai') !== false || strpos($lowName, 'bite') !== false || strpos($lowName, 'mkate') !== false || strpos($lowName, 'kokoto') !== false) {
                            $catClass = 'bites';
                          } else if (strpos($lowName, 'chai') !== false || strpos($lowName, 'tea') !== false || strpos($lowName, 'coffee') !== false || strpos($lowName, 'hot lemon') !== false) {
                            $catClass = 'chai';
                          } else if (strpos($lowName, 'ngararimo') !== false || strpos($lowName, 'kiburu') !== false || strpos($lowName, 'ngande') !== false || strpos($lowName, 'mtori') !== false || strpos($lowName, 'soup kienyeji') !== false) {
                            $catClass = 'traditional';
                          } else if (strpos($lowName, 'juice') !== false || strpos($lowName, 'tende') !== false || strpos($lowName, 'parachichi') !== false) {
                            $catClass = 'juices';
                          }
                        }
                      }
                    @endphp
                    <div class="col-6 mb-3 pos-item-card" data-name="{{ strtolower($drink->name) }}"
                      data-category="{{ $catClass }}">
                      <div
                        class="card h-100 border-0 shadow-sm rounded-lg transition-all {{ $drink->current_stock <= 0 && $drink->is_product ? 'opacity-75' : '' }}"
                        style="background: #fff; overflow: hidden; position: relative; border: 1px solid #eee !important;">

                        @if($drink->current_stock <= 0 && $drink->is_product)
                          <div class="out-of-stock-overlay d-flex align-items-center justify-content-center"
                            style="position: absolute; top:0; left:0; width:100%; height:100%; background: rgba(255,255,255,0.7); z-index: 5;">
                            <span class="badge badge-danger px-3 py-2 shadow-sm">OUT OF STOCK</span>
                          </div>
                        @endif

                        <div class="card-body p-3 d-flex flex-column h-100">
                          <div class="bg-light rounded p-2 mb-3 mx-auto d-flex align-items-center justify-content-center"
                            style="width: 100px; height: 100px;">
                            <img
                              src="{{ $drink->image ? asset('storage/' . $drink->image) : (isset($drink->recipe_id) ? 'https://img.icons8.com/color/144/restaurant-.png' : 'https://img.icons8.com/color/144/glass-with-straw.png') }}"
                              class="img-fluid" style="max-height: 80px; object-fit: contain;">
                          </div>

                          <div class="mb-3 text-center">
                            <h6 class="mb-1 text-dark font-weight-bold truncate" style="font-size: 0.95rem;">
                              {{ $drink->name }}
                            </h6>
                            @if($drink->is_product)
                              <span
                                class="badge {{ $drink->current_stock > 10 ? 'badge-success' : ($drink->current_stock > 0 ? 'badge-warning' : 'badge-danger') }} px-2"
                                style="font-size: 0.65rem;">Stock: {{ number_format($drink->current_stock) }}</span>
                            @endif
                          </div>

                          <div class="options-area mt-auto">
                            @foreach($drink->options as $option)
                              <div class="d-flex align-items-center justify-content-between bg-light rounded p-2 mb-1 border"
                                style="font-size: 0.8rem;">
                                <div>
                                  <div class="font-weight-bold text-muted"
                                    style="font-size: 0.65rem; text-transform: uppercase;">{{ $option->type }}</div>
                                  <div class="text-primary font-weight-bold">{{ number_format($option->price) }} TSH</div>
                                </div>
                                <button class="btn btn-sm btn-primary rounded px-2"
                                  onclick="addToPosCart('{{ addslashes($drink->name) }} ({{ $option->type }})', {{ $option->price }}, {{ $drink->product_id ?? 'null' }}, {{ $drink->variant_id ?? 'null' }}, '{{ isset($drink->recipe_id) ? 'food' : 'bar' }}', {{ $drink->recipe_id ?? 'null' }}, '{{ ($drink->image ? asset('storage/' . $drink->image) : (isset($drink->recipe_id) ? 'https://img.icons8.com/color/144/restaurant-.png' : 'https://img.icons8.com/color/144/glass-with-straw.png')) }}', '{{ $option->method }}', {{ $drink->current_stock ?? 0 }}, {{ $drink->servings_per_pic ?? 1 }})"
                                  {{ ($drink->current_stock ?? 0) <= 0 && $drink->is_product ? 'disabled' : '' }}>
                                  <i class="fa fa-plus"></i>
                                </button>
                              </div>
                            @endforeach
                          </div>
                        </div>
                      </div>
                    </div>
                  @endforeach
                </div>
              </div>
            </div>

            <!-- Cart/Summary -->
            <div class="col-md-5 d-flex flex-column bg-light" style="height: 70vh;">
              <div class="p-3 border-bottom bg-white">
                <h6 class="font-weight-bold mb-2 d-flex align-items-center">
                  <i class="fa fa-user mr-2 text-primary"></i> Guest Information
                </h6>

                <div class="btn-group btn-group-sm btn-block mb-2" role="group">
                  <button type="button" class="btn btn-primary" id="guestTypeWalkIn" onclick="setPosGuestType('walk_in')">
                    Walk-in
                  </button>
                  <button type="button" class="btn btn-outline-primary" id="guestTypeInHouse" onclick="setPosGuestType('in_house')">
                    In-house Guest
                  </button>
                </div>

                <div id="walkInGuestFields">
                  <input type="text" id="walkInGuestName" class="form-control mb-2" placeholder="Guest Name (Optional)">
                </div>

                <div id="inHouseGuestFields" style="display: none;">
                  <div class="input-group mb-2">
                    <input type="text" id="inHouseGuestSearch" class="form-control" placeholder="Search name or room..."
                      autocomplete="off" oninput="searchInHouseGuests()">
                    <div class="input-group-append">
                      <button class="btn btn-outline-secondary" type="button" onclick="searchInHouseGuests()">
                        <i class="fa fa-search"></i>
                      </button>
                    </div>
                  </div>
                  <div id="inHouseGuestResults" class="border rounded bg-white mb-2"
                    style="display:none; max-height: 160px; overflow-y: auto;"></div>
                  <div id="selectedInHouseGuest" class="alert alert-info py-2 px-3 mb-0" style="display:none;">
                    <div class="d-flex justify-content-between align-items-start">
                      <div>
                        <strong id="selectedGuestName"></strong>
                        <div class="small mb-0">Room <span id="selectedGuestRoom"></span></div>
                      </div>
                      <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="clearSelectedInHouseGuest()">
                        Clear
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <div class="flex-grow-1 p-3" id="posCartList" style="overflow-y: auto;">
                <div class="text-center text-muted mt-5">
                  <i class="fa fa-shopping-basket fa-3x mb-3 opacity-50"></i>
                  <p>Click items on the left to add to cart</p>
                </div>
              </div>

              <div class="p-3 border-top bg-white mt-auto">
                <div class="d-flex justify-content-between mb-2">
                  <span class="font-weight-bold text-muted">Total Amount:</span>
                  <span class="font-weight-bold text-primary h5 mb-0" id="posTotalAmount">0 TZS</span>
                </div>



                <button class="btn btn-success btn-lg btn-block shadow-sm py-3" onclick="processWalkInCheckout()"
                  id="btnConfirmSale" disabled>
                  <i class="fa fa-print mr-1"></i> <strong>PLACE ORDER & PRINT DOCKET</strong>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <style>
    .truncate {
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .hover-shadow:hover {
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
      transform: translateY(-2px);
    }

    .transition-all {
      transition: all 0.2s ease-in-out;
    }

    .badge-success {
      background-color: #d4edda;
      color: #155724;
    }

    .badge-warning {
      background-color: #fff3cd;
      color: #856404;
    }

    .badge-danger {
      background-color: #f8d7da;
      color: #721c24;
    }
  </style>

  <!-- Payment Modal HTML -->
  <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="paymentModalLabel">Record Payment</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="paymentOrderId">
          <p class="h4 text-center mb-4">Amount: <span id="paymentAmountDisplay"
              class="font-weight-bold text-primary"></span></p>

          <div class="form-group">
            <label for="paymentMethod">Payment Method</label>
            <select class="form-control" id="paymentMethod" onchange="toggleRefField()">
              <option value="cash">Cash</option>
              <option value="room_charge">Room Charge</option>
              <option value="mpesa">M-Pesa</option>
              <option value="halopesa">Halopesa</option>
              <option value="airtel_money">Airtel Money</option>
              <option value="mixx_by_yass">Mixx by Yass</option>
              <option value="nmb">NMB Bank</option>
              <option value="crdb">CRDB Bank</option>
              <option value="kcb">KCB Bank</option>
            </select>
          </div>
          <div class="form-group" id="refFieldContainer" style="display: none;">
            <label for="paymentReference">Reference Number</label>
            <input type="text" class="form-control" id="paymentReference" placeholder="Enter reference number">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" onclick="submitPayment()">Record Payment</button>
        </div>
      </div>
    </div>
  </div>
  <!-- Ceremony Payment Modal HTML -->
  <div class="modal fade" id="ceremonyPaymentModal" tabindex="-1" role="dialog"
    aria-labelledby="ceremonyPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="ceremonyPaymentModalLabel">Settle Ceremony Usage</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="ceremonyDayServiceId">
          <p class="h5 text-center mb-2">Guest: <span id="ceremonyGuestName" class="font-weight-bold text-info"></span>
          </p>
          <p class="h4 text-center mb-4">Unpaid Amount: <span id="ceremonyUnpaidAmount"
              class="font-weight-bold text-danger"></span></p>

          <div class="form-group">
            <label for="ceremonyPaymentMethod">Payment Method</label>
            <select class="form-control" id="ceremonyPaymentMethod" onchange="toggleCeremonyRefField()">
              <option value="cash">Cash</option>
              <option value="room_charge">Room Charge</option>
              <option value="mpesa">M-Pesa</option>
              <option value="halopesa">Halopesa</option>
              <option value="airtel_money">Airtel Money</option>
              <option value="mixx_by_yass">Mixx by Yass</option>
              <option value="nmb">NMB Bank</option>
              <option value="crdb">CRDB Bank</option>
              <option value="kcb">KCB Bank</option>
            </select>
          </div>
          <div class="form-group" id="ceremonyRefFieldContainer" style="display: none;">
            <label for="ceremonyPaymentReference">Reference Number</label>
            <input type="text" class="form-control" id="ceremonyPaymentReference" placeholder="Enter reference number">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" onclick="submitCeremonyPayment()">Settle Payment</button>
        </div>
      </div>
    </div>
  </div>
  <!-- Shift Closure Modal -->
  <style>
    #shiftClosureModal .shift-summary-table th,
    #shiftClosureModal .shift-summary-table td {
      color: #1c1c1c !important;
      background: #fff !important;
      border-color: #ced4da !important;
      vertical-align: middle;
    }
    #shiftClosureModal .shift-summary-table th {
      background: #f1f3f5 !important;
      font-weight: 700;
      width: 55%;
    }
    #shiftClosureModal .shift-summary-table tr.shift-row-sales th,
    #shiftClosureModal .shift-summary-table tr.shift-row-sales td {
      background: #e8f5e9 !important;
      color: #1b5e20 !important;
    }
    #shiftClosureModal .shift-summary-table tr.shift-row-count th,
    #shiftClosureModal .shift-summary-table tr.shift-row-count td {
      background: #e3f2fd !important;
      color: #0d47a1 !important;
    }
    #shiftClosureModal .shift-summary-table .shift-cash {
      color: #1b5e20 !important;
    }
    #shiftClosureModal .shift-summary-table .shift-duration {
      color: #940000 !important;
    }
    html[data-theme="dark"] #shiftClosureModal .modal-content {
      background: #161b22 !important;
      color: #e6edf3 !important;
    }
    html[data-theme="dark"] #shiftClosureModal .modal-body,
    html[data-theme="dark"] #shiftClosureModal .modal-body label,
    html[data-theme="dark"] #shiftClosureModal .modal-body .text-muted,
    html[data-theme="dark"] #shiftClosureModal #shiftSummaryLoading {
      color: #e6edf3 !important;
    }
    html[data-theme="dark"] #shiftClosureModal .shift-summary-table th,
    html[data-theme="dark"] #shiftClosureModal .shift-summary-table td {
      background: #0d1117 !important;
      color: #e6edf3 !important;
      border-color: #30363d !important;
    }
    html[data-theme="dark"] #shiftClosureModal .shift-summary-table th {
      background: #21262d !important;
      color: #f0f6fc !important;
    }
    html[data-theme="dark"] #shiftClosureModal .shift-summary-table tr.shift-row-sales th,
    html[data-theme="dark"] #shiftClosureModal .shift-summary-table tr.shift-row-sales td {
      background: #12332f !important;
      color: #b2dfdb !important;
    }
    html[data-theme="dark"] #shiftClosureModal .shift-summary-table tr.shift-row-count th,
    html[data-theme="dark"] #shiftClosureModal .shift-summary-table tr.shift-row-count td {
      background: #12263a !important;
      color: #90caf9 !important;
    }
    html[data-theme="dark"] #shiftClosureModal .shift-summary-table .shift-cash {
      color: #81c784 !important;
    }
    html[data-theme="dark"] #shiftClosureModal .shift-summary-table .shift-duration {
      color: #ff8a80 !important;
    }
    html[data-theme="dark"] #shiftClosureModal .alert-warning {
      background: #3a2a00 !important;
      color: #ffe082 !important;
      border-color: #5c4500 !important;
    }
  </style>
  <div class="modal fade" id="shiftClosureModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title font-weight-bold"><i class="fa fa-calculator"></i> Your Shift Summary</h5>
          <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div id="shiftSummaryLoading" class="text-center py-4">
            <i class="fa fa-spinner fa-spin fa-2x text-warning"></i>
            <p class="mt-2">Loading sales...</p>
          </div>
          <div id="shiftSummaryContent" style="display: none;">
            <table class="table table-bordered mb-3 shift-summary-table">
              <tr>
                <th>Opened at</th>
                <td id="summaryOpenedAt" class="text-right font-weight-bold">—</td>
              </tr>
              <tr>
                <th>Closing time (now)</th>
                <td id="summaryClosingAt" class="text-right font-weight-bold">—</td>
              </tr>
              <tr>
                <th>Duration</th>
                <td id="summaryDuration" class="text-right font-weight-bold shift-duration">—</td>
              </tr>
              <tr>
                <th>Total Guests</th>
                <td id="summaryTotalGuests" class="text-right font-weight-bold">0</td>
              </tr>
              <tr class="shift-row-sales">
                <th>Total Sales</th>
                <td id="summaryTotalSales" class="text-right font-weight-bold" style="font-size: 1.15em;">0</td>
              </tr>
              <tr>
                <th>Total Cash (on hand)</th>
                <td id="summaryTotalCash" class="text-right font-weight-bold shift-cash" style="font-size: 1.2em;">0
                </td>
              </tr>
              <tr>
                <th>M-Pesa / Mobile sales</th>
                <td id="summaryTotalMpesa" class="text-right">0</td>
              </tr>
              <tr>
                <th>Other sales (Bank)</th>
                <td id="summaryTotalOther" class="text-right">0</td>
              </tr>
              <tr class="shift-row-count">
                <th>Idadi ya Huduma Zote</th>
                <td id="summaryTotalCount" class="text-right">0</td>
              </tr>
            </table>

            <form id="shiftClosureForm">
              @csrf
              <div class="form-group">
                <label><b>Cash amount you are handing over (TZS)</b></label>
                <input type="number" name="amount_submitted" id="amountSubmitted"
                  class="form-control form-control-lg border-primary" required>
                <small class="text-muted">Enter the cash amount you are taking to Reception now.</small>
              </div>
              <div class="form-group">
                <label>Additional notes</label>
                <textarea name="notes" class="form-control" rows="2"
                  placeholder="Example: Cash is slightly short due to change given..."></textarea>
              </div>
              <div class="alert alert-warning py-2 small">
                <i class="fa fa-info-circle"></i> When you click "Complete Handover", today's sales will be locked and
                cannot be changed again.
              </div>
              <button type="submit" class="btn btn-warning btn-block btn-lg font-weight-bold" id="submitShiftBtn">
                <i class="fa fa-check-circle"></i> Complete Handover
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection

@section('scripts')
  <script>
    window.isProxySeller = @json($isProxySeller ?? false);
    window.proxyCounterShiftId = @json(optional($activeShift)->id);
    async function selectProxyCounterShift() {
      const sel = document.getElementById('proxyCounterShift');
      if (!sel || !sel.value) {
        Swal.fire('Select counter', 'Please select a counter with an active shift.', 'warning');
        return;
      }
      try {
        const res = await fetch('{{ route("bar-keeper.proxy-shift") }}', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ shift_id: parseInt(sel.value, 10) })
        });
        const data = await res.json();
        if (data.success) {
          window.proxyCounterShiftId = data.shift_id;
          Swal.fire('Done', data.message, 'success').then(() => location.reload());
        } else {
          Swal.fire('Error', data.message || 'Failed', 'error');
        }
      } catch (e) {
        Swal.fire('Error', e.message || 'Failed', 'error');
      }
    }

    function receiveTransfer(transferId) {
      Swal.fire({
        title: "Receive Transfer?",
        text: "Mark this transfer as received?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, received!",
        cancelButtonText: "Cancel"
      }).then((result) => {
        if (result.isConfirmed) {
          callApi(`{{ route("bar-keeper.transfers.receive", ":id") }}`.replace(':id', transferId), 'PUT', { status: 'completed' });
        }
      });
    }


    // POS Logic
    let posCart = [];
    let posGuestType = 'walk_in'; // walk_in | in_house
    let selectedInHouseBooking = null;
    let inHouseSearchTimer = null;

    // Data for Dynamic Food selection
    const foodBasesData = @json($foodBases ?? []);

    function setPosGuestType(type) {
      posGuestType = type;
      const walkBtn = document.getElementById('guestTypeWalkIn');
      const houseBtn = document.getElementById('guestTypeInHouse');
      const walkFields = document.getElementById('walkInGuestFields');
      const houseFields = document.getElementById('inHouseGuestFields');

      if (type === 'in_house') {
        walkBtn.classList.remove('btn-primary');
        walkBtn.classList.add('btn-outline-primary');
        houseBtn.classList.remove('btn-outline-primary');
        houseBtn.classList.add('btn-primary');
        walkFields.style.display = 'none';
        houseFields.style.display = '';
        document.getElementById('posModalTitle').innerHTML = '<i class="fa fa-bed mr-2"></i> In-house Guest Order';
        document.getElementById('inHouseGuestSearch').focus();
      } else {
        houseBtn.classList.remove('btn-primary');
        houseBtn.classList.add('btn-outline-primary');
        walkBtn.classList.remove('btn-outline-primary');
        walkBtn.classList.add('btn-primary');
        houseFields.style.display = 'none';
        walkFields.style.display = '';
        clearSelectedInHouseGuest();
        if (!window.currentDayServiceId) {
          document.getElementById('posModalTitle').innerHTML = '<i class="fa fa-shopping-cart mr-2"></i> New Walk-in Order';
        }
      }
    }

    function searchInHouseGuests() {
      clearTimeout(inHouseSearchTimer);
      const q = (document.getElementById('inHouseGuestSearch').value || '').trim();
      const resultsEl = document.getElementById('inHouseGuestResults');

      if (q.length < 1) {
        resultsEl.style.display = 'none';
        resultsEl.innerHTML = '';
        return;
      }

      inHouseSearchTimer = setTimeout(async () => {
        try {
          const url = `{{ route('bar-keeper.in-house-guests') }}?q=${encodeURIComponent(q)}`;
          const response = await fetch(url, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
          });
          const data = await response.json();
          const guests = data.guests || [];

          if (!guests.length) {
            resultsEl.innerHTML = '<div class="p-2 text-muted small">No checked-in guest found</div>';
            resultsEl.style.display = '';
            return;
          }

          window._inHouseGuestCache = window._inHouseGuestCache || {};
          resultsEl.innerHTML = guests.map(g => {
            window._inHouseGuestCache[g.id] = g;
            const safeName = String(g.guest_name || '').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            return `
            <button type="button" class="btn btn-light btn-block text-left border-0 rounded-0 py-2 px-3"
              onclick="selectInHouseGuestById(${Number(g.id)})">
              <strong>${safeName}</strong>
              <span class="badge badge-primary ml-1">Room ${g.room_number}</span>
            </button>`;
          }).join('');
          resultsEl.style.display = '';
        } catch (e) {
          console.error(e);
          resultsEl.innerHTML = '<div class="p-2 text-danger small">Search failed</div>';
          resultsEl.style.display = '';
        }
      }, 300);
    }

    function selectInHouseGuestById(bookingId) {
      const guest = (window._inHouseGuestCache || {})[bookingId];
      if (!guest) return;
      selectedInHouseBooking = guest;
      document.getElementById('selectedGuestName').innerText = guest.guest_name;
      document.getElementById('selectedGuestRoom').innerText = guest.room_number;
      document.getElementById('selectedInHouseGuest').style.display = '';
      document.getElementById('inHouseGuestResults').style.display = 'none';
      document.getElementById('inHouseGuestSearch').value = '';
      document.getElementById('walkInGuestName').value = guest.guest_name + ' (Rm ' + guest.room_number + ')';
      document.getElementById('posModalTitle').innerHTML =
        `<i class="fa fa-bed mr-2"></i> ${guest.guest_name} — Room ${guest.room_number}`;
    }

    function clearSelectedInHouseGuest() {
      selectedInHouseBooking = null;
      const box = document.getElementById('selectedInHouseGuest');
      if (box) box.style.display = 'none';
      const results = document.getElementById('inHouseGuestResults');
      if (results) {
        results.style.display = 'none';
        results.innerHTML = '';
      }
      const search = document.getElementById('inHouseGuestSearch');
      if (search) search.value = '';
    }

    function showFoodBuilderModal(baseName) {
      const sides = foodBasesData[baseName];
      if (!sides || sides.length === 0) return;

      let optionsHtml = '<div class="row">';
      sides.forEach((side, index) => {
        optionsHtml += `
                                              <div class="col-12 mb-2">
                                                  <button class="btn btn-outline-primary btn-block text-left p-3 d-flex justify-content-between align-items-center"
                                                      onclick="selectFoodAddOn(${index}, '${baseName}')">
                                                      <span class="font-weight-bold" style="font-size: 1.1rem;">${side.side}</span>
                                                      <span class="badge badge-primary px-2 py-1" style="font-size: 0.9rem;">${Number(side.price).toLocaleString()} TSH</span>
                                                  </button>
                                              </div>
                                          `;
      });
      optionsHtml += '</div>';

      Swal.fire({
        title: `<i class="fa fa-cutlery text-primary mr-2"></i> How would you like the ${baseName}?`,
        html: optionsHtml,
        showConfirmButton: false,
        showCloseButton: true,
        customClass: {
          popup: 'rounded-lg'
        }
      });
    }

    function selectFoodAddOn(sideIndex, baseName) {
      const item = foodBasesData[baseName][sideIndex];
      Swal.close();
      let defaultImage = 'https://img.icons8.com/color/144/restaurant-.png';
      let actImg = item.image ? '/storage/' + item.image : defaultImage;
      // Same logic as addToPosCart
      addToPosCart(item.name, item.price, null, null, 'food', item.id, actImg, 'plate', 999, 1);
    }

    function openWalkInModal(residentRoom = null, residentName = null, dayServiceId = null) {
      $('#walkInModal').modal('show');
      posCart = [];
      clearSelectedInHouseGuest();
      setPosGuestType('walk_in');

      // Reset inputs
      document.getElementById('walkInGuestName').value = residentName || '';

      // Store dayServiceId in a global variable for checkout
      window.currentDayServiceId = dayServiceId;

      // If it's for a ceremony, update title/label
      if (dayServiceId) {
        document.getElementById('posModalTitle').innerText = 'Record Usage: ' + residentName;
        document.getElementById('btnConfirmSale').innerHTML = '<i class="fa fa-print mr-1"></i> <strong>PLACE ORDER & PRINT DOCKET</strong>';
      } else {
        document.getElementById('posModalTitle').innerHTML = '<i class="fa fa-shopping-cart mr-2"></i> New Order';
        document.getElementById('btnConfirmSale').innerHTML = '<i class="fa fa-print mr-1"></i> <strong>PLACE ORDER & PRINT DOCKET</strong>';
      }

      renderPosCart();
    }

    function addToPosCart(name, price, pid, vid, type, foodId = null, image = null, unit = 'pic', currentStock = null, servingsPerPic = 1) {
      const key = foodId ? String(foodId) : `${pid}_${vid}_${unit}`;
      const existing = posCart.find(item => String(item.key) === String(key));

      // Calculate how much we're trying to add
      let newQty = existing ? existing.qty + 1 : 1;

      // Convert to bottles if selling by serving/glass
      let bottlesNeeded = newQty;
      if (unit === 'serving' && servingsPerPic > 0) {
        bottlesNeeded = newQty / servingsPerPic;
      }

      // Check stock availability (only for products with stock tracking)
      if (currentStock !== null && currentStock > 0 && bottlesNeeded > currentStock) {
        const maxAllowed = unit === 'serving' ? Math.floor(currentStock * servingsPerPic) : Math.floor(currentStock);
        Swal.fire({
          title: 'Stock Limit Exceeded',
          html: `<p>Cannot add more of this item.</p>
                                                     <p><strong>Current Stock:</strong> ${currentStock.toFixed(1)} bottles</p>
                                                     <p><strong>Maximum ${unit === 'serving' ? 'Glasses' : 'Units'}:</strong> ${maxAllowed}</p>`,
          icon: 'warning',
          confirmButtonText: 'OK'
        });
        return;
      }

      if (existing) {
        existing.qty++;
      } else {
        posCart.push({
          key: key,
          name: name,
          price: price,
          pid: pid,
          vid: vid,
          type: type,
          foodId: foodId,
          image: image,
          unit: unit,
          qty: 1,
          currentStock: currentStock,
          servingsPerPic: servingsPerPic
        });
      }
      renderPosCart();
    }

    function removeFromPosCart(key) {
      posCart = posCart.filter(item => String(item.key) !== String(key));
      renderPosCart();
    }

    function updatePosQty(key, delta) {
      const item = posCart.find(i => String(i.key) === String(key));
      if (item) {
        const newQty = item.qty + delta;

        // If increasing quantity, check stock
        if (delta > 0 && item.currentStock !== null && item.currentStock !== undefined) {
          let bottlesNeeded = newQty;
          if (item.unit === 'serving' && item.servingsPerPic > 0) {
            bottlesNeeded = newQty / item.servingsPerPic;
          }

          if (bottlesNeeded > item.currentStock) {
            const maxAllowed = item.unit === 'serving' ? Math.floor(item.currentStock * item.servingsPerPic) : Math.floor(item.currentStock);
            Swal.fire({
              title: 'Stock Limit Exceeded',
              html: `<p>Cannot add more of this item.</p>
                                                             <p><strong>Current Stock:</strong> ${item.currentStock.toFixed(1)} bottles</p>
                                                             <p><strong>Maximum ${item.unit === 'serving' ? 'Glasses' : 'Bottles'}:</strong> ${maxAllowed}</p>`,
              icon: 'warning',
              confirmButtonText: 'OK'
            });
            return;
          }
        }

        item.qty = newQty;
        if (item.qty <= 0) removeFromPosCart(key);
        else renderPosCart();
      }
    }

    function renderPosCart() {
      const list = document.getElementById('posCartList');
      const totalEl = document.getElementById('posTotalAmount');
      const btn = document.getElementById('btnConfirmSale');

      if (posCart.length === 0) {
        list.innerHTML = `<div class="text-center text-muted mt-5"><i class="fa fa-shopping-basket fa-3x mb-2"></i><p>Cart is empty</p></div>`;
        totalEl.innerText = '0 TZS';
        btn.disabled = true;
        return;
      }

      let total = 0;
      let html = '';
      posCart.forEach(item => {
        const itemTotal = item.price * item.qty;
        total += itemTotal;
        html += `
                                              <div class="d-flex justify-content-between align-items-center mb-3 bg-white p-2 rounded shadow-sm">
                                                  <div class="d-flex align-items-center" style="max-width: 65%;">
                                                      ${item.image ? `<img src="${item.image}" class="mr-2 rounded" style="width: 40px; height: 40px; object-fit: cover;">` : ''}
                                                      <div>
                                                          <div class="font-weight-bold" style="font-size: 0.85rem; line-height: 1.2;">${item.name}</div>
                                                          <small class="text-muted">${item.price.toLocaleString()} x ${item.qty}</small>
                                                      </div>
                                                  </div>
                                                  <div class="d-flex align-items-center">
                                                      <div class="btn-group btn-group-sm mr-2">
                                                          <button class="btn btn-outline-secondary px-2" onclick="updatePosQty('${item.key}', -1)">-</button>
                                                          <button class="btn btn-outline-secondary px-2" onclick="updatePosQty('${item.key}', 1)">+</button>
                                                      </div>
                                                      <div class="text-right mr-2" style="min-width: 80px;">
                                                          <span class="font-weight-bold d-block" style="font-size: 0.85rem;">${itemTotal.toLocaleString()}</span>
                                                      </div>
                                                      <button class="btn btn-sm btn-outline-danger border-0" onclick="removeFromPosCart('${item.key}')">
                                                          <i class="fa fa-trash"></i>
                                                      </button>
                                                  </div>
                                              </div>
                                          `;
      });

      list.innerHTML = html;
      totalEl.innerText = `${total.toLocaleString()} TZS`;
      btn.disabled = false;
    }

    function printKitchenDocket() {
      if (posCart.length === 0) {
        Swal.fire("Cart Empty", "Please add items to the cart before printing.", "info");
        return;
      }

      const guestName = document.getElementById('walkInGuestName').value.trim() || 'Walk-in Guest';
      const now = new Date();
      const timeStr = now.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' });
      const dateStr = now.toLocaleDateString('en-GB');
      const orderTotal = posCart.reduce((sum, item) => sum + (Number(item.price) * Number(item.qty)), 0);



      const docketHtml = `<!DOCTYPE html>
                    <html><head>
                    <meta charset="UTF-8"><title>Kitchen Docket</title>
                    <style>
                      * { margin:0; padding:0; box-sizing:border-box; color:#000 !important; }
                      body { font-family:'Courier New',monospace; width:100%; max-width: 75mm; padding:2mm; color:#000; background:#fff; font-weight:900 !important; overflow-x: hidden; }
                      .center { text-align:center; }
                      .divider { border-top:4px dashed #000; margin:10px 0; }
                      .hotel { font-size:16px; font-weight:900; border: 2px solid #000; padding: 2px; }
                      .title { font-size:20px; font-weight:900; margin:8px 0; letter-spacing:1px; border: 3px solid #000; }
                      .info { font-size:14px; margin:5px 0; font-weight:900; }
                      table { width:100%; border-collapse:collapse; margin-top:10px; table-layout: fixed; }
                      td { vertical-align:top; border-bottom: 2px solid #000; word-wrap: break-word; }
                      .footer { font-size:14px; margin-top:15px; font-weight:900; text-transform: uppercase; }
                      .total { font-size:22px; font-weight:900; margin-top:8px; }
                      @media print { 
                        body { margin:0; padding:2mm; width: 100%; }
                        @page { margin: 0; }
                      }
                    </style>
                    </head>
                    <body>
                      <div class="center">
                        <div class="hotel">UMOJA LUTHERAN HOSTEL</div>
                        <div class="title">*** KITCHEN ORDER ***</div>
                      </div>
                      <div class="divider"></div>
                      <div class="info">Guest : <strong>${guestName}</strong></div>
                      <div class="info">Time  : ${timeStr} &nbsp;&nbsp; Date: ${dateStr}</div>
                      <div class="divider"></div>
                      <table>
                        <tbody>
                          ${posCart.map(item => `
                            <tr>
                              <td style="padding:8px 0; font-size:22px; font-weight:900; width:45px;">${item.qty}x</td>
                              <td style="padding:8px 0; font-size:22px; font-weight:900;">${item.name}</td>
                            </tr>
                          `).join('')}
                        </tbody>
                      </table>
                      <div class="divider"></div>
                      <div class="center total">TOTAL : ${orderTotal.toLocaleString()} TZS</div>
                      <div class="divider"></div>
                      <div class="center footer">-- Please prepare quickly --</div>
                    </body></html>`;

      // Create hidden iframe for printing
      let printFrame = document.getElementById('posPrintFrame');
      if (!printFrame) {
        printFrame = document.createElement('iframe');
        printFrame.id = 'posPrintFrame';
        printFrame.style.position = 'fixed';
        printFrame.style.right = '0';
        printFrame.style.bottom = '0';
        printFrame.style.width = '0';
        printFrame.style.height = '0';
        printFrame.style.border = 'none';
        document.body.appendChild(printFrame);
      }

      const doc = printFrame.contentWindow.document;
      doc.open();
      doc.write(docketHtml);
      doc.close();

      setTimeout(() => {
        printFrame.contentWindow.focus();
        printFrame.contentWindow.print();
      }, 500);
    }



    let currentCategory = 'all';

    function filterByCategory(category, btn) {
      currentCategory = category;

      // Update UI active state
      const tabs = document.getElementById('posCategoryTabs').querySelectorAll('button');
      tabs.forEach(t => {
        t.classList.remove('btn-primary', 'active');
        t.classList.add('btn-outline-primary');
      });
      btn.classList.remove('btn-outline-primary');
      btn.classList.add('btn-primary', 'active');

      filterPosItems();
    }

    function filterPosItems() {
      const query = document.getElementById('itemSearch').value.toLowerCase();
      const cards = document.querySelectorAll('.pos-item-card');

      cards.forEach(card => {
        const name = card.dataset.name;
        const category = card.dataset.category;

        const matchesQuery = name.includes(query);
        const matchesCategory = currentCategory === 'all' || category === currentCategory;

        if (matchesQuery && matchesCategory) {
          card.style.display = '';
        } else {
          card.style.display = 'none';
        }
      });
    }

    async function processWalkInCheckout() {
      const now = new Date();
      const timeStr = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
      const isInHouse = posGuestType === 'in_house';

      if (isInHouse && !selectedInHouseBooking) {
        await Swal.fire('Select Guest', 'Search and select an in-house guest registered at reception.', 'warning');
        return;
      }

      const guestName = isInHouse
        ? `${selectedInHouseBooking.guest_name} (Rm ${selectedInHouseBooking.room_number})`
        : (document.getElementById('walkInGuestName').value.trim() || ('Walk-in (' + timeStr + ')'));

      // Ensure docket uses the resolved guest name
      document.getElementById('walkInGuestName').value = guestName;

      let paymentTiming = 'later';
      let paymentMethod = null;

      if (isInHouse) {
        const payChoice = await Swal.fire({
          title: 'How will they pay?',
          html: `
            <p class="mb-3"><strong>${selectedInHouseBooking.guest_name}</strong> — Room ${selectedInHouseBooking.room_number}</p>
            <div class="text-left">
              <label class="d-block p-2 border rounded mb-2" style="cursor:pointer;">
                <input type="radio" name="posPayChoice" value="now" checked class="mr-2">
                <strong>Pay now at counter</strong>
                <div class="small text-muted ml-4">Cash / M-Pesa / Card here at the restaurant</div>
              </label>
              <label class="d-block p-2 border rounded" style="cursor:pointer;">
                <input type="radio" name="posPayChoice" value="reception" class="mr-2">
                <strong>Pay at reception</strong>
                <div class="small text-muted ml-4">The bill will appear on the guest's room bill</div>
              </label>
            </div>
          `,
          showCancelButton: true,
          confirmButtonText: 'Continue',
          preConfirm: () => {
            const chosen = document.querySelector('input[name="posPayChoice"]:checked');
            return chosen ? chosen.value : 'now';
          }
        });

        if (!payChoice.isConfirmed) return;

        if (payChoice.value === 'reception') {
          paymentTiming = 'room_charge';
          paymentMethod = 'room_charge';
        } else {
          const methodChoice = await Swal.fire({
            title: 'Payment Method',
            input: 'select',
            inputOptions: {
              cash: 'Cash',
              mpesa: 'M-Pesa',
              card: 'Card',
              bank_transfer: 'Bank Transfer'
            },
            inputValue: 'cash',
            showCancelButton: true,
            confirmButtonText: 'Record Payment'
          });
          if (!methodChoice.isConfirmed) return;
          paymentTiming = 'immediate';
          paymentMethod = methodChoice.value;
        }
      } else {
        const confirm = await Swal.fire({
          title: 'Confirm Sale?',
          text: `Record order for ${guestName}?`,
          icon: 'question',
          showCancelButton: true,
          confirmButtonText: 'Yes, Record Sale'
        });
        if (!confirm.isConfirmed) return;
      }

      // Print docket automatically
      printKitchenDocket();

      $('#walkInModal').modal('hide');
      Swal.fire({
        title: 'Processing...',
        didOpen: () => { Swal.showLoading(); }
      });

      let successCount = 0;
      let lastError = 'An unknown error occurred.';

      for (const item of posCart) {
        const payload = {
          service_id: item.type === 'food' ? "{{ $foodServiceId ?? '' }}" : "{{ $barServiceId ?? '' }}",
          product_id: item.pid,
          product_variant_id: item.vid,
          quantity: item.qty,
          day_service_id: window.currentDayServiceId || null,
          payment_timing: paymentTiming,
          item_name: item.name,
          on_behalf_shift_id: (window.proxyCounterShiftId
            || (document.getElementById('proxyCounterShift') && document.getElementById('proxyCounterShift').value)
            || null),
          service_specific_data: {
            food_id: item.foodId,
            item_name: item.name,
            product_id: item.pid,
            product_variant_id: item.vid,
            selling_method: item.unit || 'pic'
          }
        };

        if (isInHouse) {
          payload.is_walk_in = 0;
          payload.booking_id = selectedInHouseBooking.id;
          if (paymentMethod) payload.payment_method = paymentMethod;
        } else {
          payload.is_walk_in = 1;
          payload.walk_in_name = guestName;
        }

        try {
          const response = await fetch("{{ route('customer.services.request') }}", {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
              'Accept': 'application/json',
              'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
          });
          const res = await response.json();
          if (res.success) {
            successCount++;
          } else {
            lastError = res.message;
            if (res.errors) {
              lastError += ': ' + JSON.stringify(res.errors);
            }
            console.error('Item failed:', item.name, res);
          }
        } catch (e) {
          console.error(e);
          lastError = e.message;
        }
      }

      if (successCount === posCart.length) {
        let msg = 'Order recorded successfully!';
        if (isInHouse && paymentTiming === 'room_charge') {
          msg = 'Order charged to room. Reception will see it on the guest bill.';
        } else if (isInHouse && paymentTiming === 'immediate') {
          msg = 'Order recorded and paid at counter.';
        }
        await Swal.fire({
          title: 'Success!',
          text: msg,
          icon: 'success',
          timer: 2200,
          showConfirmButton: false
        });
        window.location.reload();
      } else {
        await Swal.fire({
          title: 'Error',
          text: 'Some items failed to process. Reason: ' + lastError,
          icon: 'error'
        });
      }
    }

    function openPaymentModal(orderId, amount, isWalkIn = 0) {
      document.getElementById('paymentOrderId').value = orderId;
      document.getElementById('paymentAmountDisplay').innerText = amount.toLocaleString() + ' TZS';

      const methodSelect = document.getElementById('paymentMethod');
      const roomChargeOption = methodSelect.querySelector('option[value="room_charge"]');

      if (isWalkIn) {
        if (roomChargeOption) roomChargeOption.style.display = 'none';
        if (methodSelect.value === 'room_charge') methodSelect.value = 'cash';
      } else {
        if (roomChargeOption) roomChargeOption.style.display = 'block';
      }

      document.getElementById('paymentMethod').value = 'cash';
      document.getElementById('paymentReference').value = '';
      toggleRefField();
      $('#paymentModal').modal('show');
    }

    function toggleRefField() {
      const method = document.getElementById('paymentMethod').value;
      const container = document.getElementById('refFieldContainer');
      if (method === 'cash' || method === 'room_charge') { // Room charge also doesn't need a ref
        container.style.display = 'none';
      } else {
        container.style.display = 'block';
      }
    }

    function submitPayment() {
      const orderId = document.getElementById('paymentOrderId').value;
      const method = document.getElementById('paymentMethod').value;
      const reference = document.getElementById('paymentReference').value.trim();

      if (method !== 'cash' && method !== 'room_charge' && !reference) {
        Swal.fire("Missing Info", "Please enter a reference number for " + method.replace('_', ' ').toUpperCase(), "warning");
        return;
      }

      $('#paymentModal').modal('hide');

      // If it's a room charge, use the legacy completeOrder
      if (method === 'room_charge') {
        completeOrder(orderId, method, reference);
      } else {
        // Use the new POS settlement route
        settlePOSPayment(orderId, method, reference);
      }
    }

    function settlePOSPayment(orderId, method, reference = '') {
      Swal.fire({
        title: "Confirm Payment?",
        text: `Record ${method.toUpperCase()} payment of ${document.getElementById('paymentAmountDisplay').innerText}?`,
        icon: "success",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        confirmButtonText: "Yes, Paid!"
      }).then((result) => {
        if (result.isConfirmed) {
          const url = `/customer/pos/settle-payment/${orderId}`;
          fetch(url, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
              'Accept': 'application/json',
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              payment_method: method,
              payment_reference: reference
            })
          })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                Swal.fire("Success!", data.message, "success");
                setTimeout(() => location.reload(), 1500);
              } else {
                Swal.fire("Error!", data.message, "error");
              }
            })
            .catch(error => {
              console.error('Error:', error);
              Swal.fire("Error!", "Failed to record payment.", "error");
            });
        }
      });
    }


    function serveOrder(orderId, itemName) {
      Swal.fire({
        title: "Mark as Served?",
        text: "Confirm that '" + itemName + "' has been taken/served? Payment will remain PENDING.",
        icon: "info",
        showCancelButton: true,
        confirmButtonColor: "#17a2b8",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, Served!",
        cancelButtonText: "Cancel"
      }).then((result) => {
        if (result.isConfirmed) {
          const url = `/bar-keeper/orders/${orderId}/serve`;
          fetch(url, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
              'Accept': 'application/json',
              'Content-Type': 'application/json'
            }
          })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                Swal.fire({
                  title: "Served!",
                  text: data.message,
                  icon: "success",
                  timer: 1500,
                  showConfirmButton: false
                });
                setTimeout(() => location.reload(), 1500);
              } else {
                Swal.fire("Error!", data.message, "error");
              }
            })
            .catch(error => {
              console.error('Error:', error);
              Swal.fire("Error!", "Failed to update order. Please try again.", "error");
            });
        }
      });
    }

    function cancelOrder(orderId, itemName) {
      Swal.fire({
        title: "Cancel Order?",
        text: "Are you sure you want to cancel '" + itemName + "'? This will return items to stock.",
        icon: "warning",
        input: 'textarea',
        inputPlaceholder: 'Enter the reason for cancelling this order (required)...',
        inputAttributes: {
          'aria-label': 'Cancellation reason'
        },
        showCancelButton: true,
        confirmButtonColor: "#dc3545",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, Cancel Order!",
        cancelButtonText: "No, Keep It",
        preConfirm: (reason) => {
          if (!reason || reason.trim().length < 5) {
            Swal.showValidationMessage(`Please enter a meaningful reason (at least 5 characters)`);
          }
          return reason;
        }
      }).then((result) => {
        if (result.isConfirmed) {
          const url = `/bar-keeper/orders/${orderId}/cancel`;
          fetch(url, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
              'Accept': 'application/json',
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              cancellation_reason: result.value
            })
          })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                Swal.fire({
                  title: "Cancelled!",
                  text: data.message,
                  icon: "success",
                  timer: 2000,
                  showConfirmButton: false
                });
                setTimeout(() => location.reload(), 1500);
              } else {
                Swal.fire("Error!", data.message, "error");
              }
            })
            .catch(error => {
              console.error('Error:', error);
              Swal.fire("Error!", "Failed to cancel order. Please try again.", "error");
            });
        }
      });
    }

    function completeOrder(orderId, method, reference = '') {
      let title = "Confirm Payment?";
      let text = "Record payment via " + method.replace('_', ' ').toUpperCase() + "?";
      let icon = "warning";
      let btnColor = "#28a745";

      if (method === 'room_charge') {
        title = "Charge to Room?";
        text = "This will add the amount to the guest's room bill.";
        icon = "info";
        btnColor = "#007bff";
      }

      Swal.fire({
        title: title,
        text: text,
        icon: icon,
        showCancelButton: true,
        confirmButtonColor: btnColor,
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, Confirm!",
        cancelButtonText: "No, Cancel"
      }).then((result) => {
        if (result.isConfirmed) {
          const url = `/bar-keeper/orders/${orderId}/complete`;
          fetch(url, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
              'Accept': 'application/json',
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              payment_method: method,
              payment_reference: reference
            })
          })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                Swal.fire({
                  title: "Success!",
                  text: data.message,
                  icon: "success",
                  timer: 2000,
                  showConfirmButton: false
                });
                setTimeout(() => location.reload(), 1500);
              } else {
                Swal.fire("Error!", data.message, "error");
              }
            })
            .catch(error => {
              console.error('Error:', error);
              Swal.fire("Error!", "An error occurred. Please try again.", "error");
            });
        }
      });
    }

    function printWalkInDocket(orderId) {
      const url = `/bar-keeper/orders/${orderId}/print-docket`;
      window.open(url, 'DocketPrint', 'width=800,height=600');
    }

    function callApi(url, method, data) {
      fetch(url, {
        method: method,
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            Swal.fire({
              title: "Success!",
              text: data.message || "Action completed successfully!",
              icon: "success",
              timer: 2000,
              showConfirmButton: false
            });
            setTimeout(() => location.reload(), 1500);
          } else {
            Swal.fire("Error!", data.message || "Action failed.", "error");
          }
        })
        .catch(error => {
          console.error('Error:', error);
          Swal.fire("Error!", "An error occurred. Please try again.", "error");
        });
    }
    function openCeremonyPaymentModal(dayServiceId, guestName, unpaidAmount) {
      document.getElementById('ceremonyDayServiceId').value = dayServiceId;
      document.getElementById('ceremonyGuestName').innerText = guestName;
      document.getElementById('ceremonyUnpaidAmount').innerText = unpaidAmount.toLocaleString() + ' TZS';

      const methodSelect = document.getElementById('ceremonyPaymentMethod');
      const roomChargeOption = methodSelect.querySelector('option[value="room_charge"]');
      if (roomChargeOption) roomChargeOption.style.display = 'none'; // Ceremonies are walk-ins

      document.getElementById('ceremonyPaymentMethod').value = 'cash';
      document.getElementById('ceremonyPaymentReference').value = '';
      toggleCeremonyRefField();
      $('#ceremonyPaymentModal').modal('show');
    }

    function toggleCeremonyRefField() {
      const method = document.getElementById('ceremonyPaymentMethod').value;
      const container = document.getElementById('ceremonyRefFieldContainer');
      if (method === 'cash' || method === 'room_charge') {
        container.style.display = 'none';
      } else {
        container.style.display = 'block';
      }
    }

    function submitCeremonyPayment() {
      const dayServiceId = document.getElementById('ceremonyDayServiceId').value;
      const method = document.getElementById('ceremonyPaymentMethod').value;
      const reference = document.getElementById('ceremonyPaymentReference').value.trim();

      if (method !== 'cash' && method !== 'room_charge' && !reference) {
        Swal.fire("Missing Info", "Please enter a reference number for " + method.replace('_', ' ').toUpperCase(), "warning");
        return;
      }

      Swal.fire({
        title: "Confirm Settlement",
        text: "Mark all unpaid consumption for this ceremony as paid?",
        icon: "info",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, Settle!",
        cancelButtonText: "Cancel"
      }).then((result) => {
        if (result.isConfirmed) {
          const url = `/customer/ceremonies/settle-usage`;
          fetch(url, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
              'Accept': 'application/json',
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              day_service_id: dayServiceId,
              payment_method: method,
              payment_reference: reference
            })
          })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                Swal.fire({
                  title: "Success!",
                  text: data.message + ` (${data.count} items settled)`,
                  icon: "success",
                  timer: 2000,
                  showConfirmButton: false
                });
                setTimeout(() => location.reload(), 1500);
              } else {
                Swal.fire("Error!", data.message, "error");
              }
            })
            .catch(error => {
              console.error('Error:', error);
              Swal.fire("Error!", "Failed to settle payment. Please try again.", "error");
            });
        }
      });
    }
    function openShiftClosureModal() {
      $('#shiftClosureModal').modal('show');
      $('#shiftSummaryLoading').show();
      $('#shiftSummaryContent').hide();

      fetch('{{ route("bar-keeper.shift-summary") }}')
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            const s = data.summary;
            $('#summaryOpenedAt').text(s.opened_at || '—');
            $('#summaryClosingAt').text(s.closing_at || '—');
            $('#summaryDuration').text(s.duration_label || '—');
            $('#summaryTotalGuests').text(s.total_guests || 0);
            $('#summaryTotalSales').text(new Intl.NumberFormat().format(s.total_sales || 0) + ' TZS');
            $('#summaryTotalCash').text(new Intl.NumberFormat().format(s.total_cash));
            $('#summaryTotalMpesa').text(new Intl.NumberFormat().format(s.total_mpesa));
            $('#summaryTotalOther').text(new Intl.NumberFormat().format(s.total_other));
            $('#summaryTotalCount').text(s.count);

            // Pre-fill amount submitted with total cash as a helpful default
            $('#amountSubmitted').val(s.total_cash);

            $('#shiftSummaryLoading').hide();
            $('#shiftSummaryContent').show();
          } else {
            Swal.fire("Error", data.message || "Failed to load shift summary.", "error");
            $('#shiftClosureModal').modal('hide');
          }
        })
        .catch(err => {
          console.error(err);
          Swal.fire("Error", "A network error occurred.", "error");
          $('#shiftClosureModal').modal('hide');
        });
    }

    function buildShiftReportHtml(report) {
      const money = (n) => new Intl.NumberFormat().format(Number(n || 0));
      const qty = (n) => {
        const v = Number(n || 0);
        return Number.isInteger(v) ? String(v) : String(v);
      };
      const items = Array.isArray(report.items) ? report.items : [];
      const unpaidGuests = Array.isArray(report.unpaid_guests) ? report.unpaid_guests : [];
      const itemRows = items.length
        ? items.map((item, i) => `
    <tr${item.from_prior_debt ? ' style="background:#fff8e6;"' : ''}>
      <td class="center">${i + 1}</td>
      <td>${item.time || '—'}</td>
      <td>${item.item || 'Item'}${item.from_prior_debt ? '<div style="font-size:10px;color:#856404;font-weight:700;">PAID FROM PRIOR DEBT</div>' : ''}</td>
      <td>${item.guest || '—'}</td>
      <td class="center">${qty(item.qty)}</td>
      <td>${item.payment || '—'}</td>
      <td class="num">${money(item.amount)}</td>
    </tr>`).join('') + `
    <tr>
      <td colspan="6" style="text-align:right;font-weight:700;">Total</td>
      <td class="num" style="font-weight:700;">${money(report.total_sales)}</td>
    </tr>`
        : `<tr><td colspan="7" style="text-align:center;font-style:italic;color:#666;">No paid items were recorded for this shift.</td></tr>`;

      const unpaidRows = unpaidGuests.length
        ? unpaidGuests.map((guest, i) => {
            const itemLines = (guest.items || []).map((item) => `
    <tr>
      <td></td>
      <td colspan="3" style="color:#444;">${item.time || ''} — ${item.item || 'Item'} (x${qty(item.qty)})</td>
      <td class="num">${money(item.amount)}</td>
    </tr>`).join('');
            return `
    <tr>
      <td class="center">${i + 1}</td>
      <td>${guest.guest || '—'}</td>
      <td class="center">${guest.orders || 0}</td>
      <td class="center">${qty(guest.qty)}</td>
      <td class="num">${money(guest.amount)}</td>
    </tr>${itemLines}`;
          }).join('') + `
    <tr>
      <td colspan="4" style="text-align:right;font-weight:700;">Total unpaid</td>
      <td class="num" style="font-weight:700;">${money(report.unpaid_total)}</td>
    </tr>`
        : `<tr><td colspan="5" style="text-align:center;font-style:italic;color:#666;">No unpaid guests for this shift.</td></tr>`;

      return `<!DOCTYPE html>
<html><head>
<meta charset="UTF-8"><title>Shift Sales Report</title>
<style>
  body { font-family: Arial, sans-serif; padding: 24px; color: #111; font-size: 12px; }
  h1 { font-size: 20px; margin: 0 0 4px; }
  h2 { font-size: 14px; margin: 20px 0 8px; }
  .sub { color: #555; margin-bottom: 18px; }
  table { width: 100%; border-collapse: collapse; margin-top: 12px; }
  th, td { border: 1px solid #333; padding: 8px 10px; text-align: left; }
  .summary th { background: #f3f3f3; width: 45%; }
  .summary td { text-align: right; font-weight: 700; }
  .items th { background: #f3f3f3; font-size: 11px; }
  .items td { font-size: 11px; vertical-align: top; }
  .items .num { text-align: right; white-space: nowrap; }
  .items .center { text-align: center; }
  .footer { margin-top: 24px; font-size: 12px; color: #666; }
</style>
</head><body>
  <h1>UMOJA LUTHERAN HOSTEL</h1>
  <div class="sub">Shift Sales Report — ${report.staff_name || ''}</div>
  <table class="summary">
    <tr><th>Opened at</th><td>${report.opened_at || '—'}</td></tr>
    <tr><th>Closed at</th><td>${report.closed_at || '—'}</td></tr>
    <tr><th>Duration</th><td>${report.duration_label || '—'}</td></tr>
    <tr><th>Total Guests</th><td>${report.total_guests || 0}</td></tr>
    <tr><th>Total Orders</th><td>${report.count || 0}</td></tr>
    <tr><th>Total Sales</th><td>${money(report.total_sales)} TZS</td></tr>
    <tr><th>Cash</th><td>${money(report.total_cash)} TZS</td></tr>
    <tr><th>M-Pesa</th><td>${money(report.total_mpesa)} TZS</td></tr>
    <tr><th>Other</th><td>${money(report.total_other)} TZS</td></tr>
    <tr><th>Cash submitted</th><td>${money(report.amount_submitted)} TZS</td></tr>
    <tr><th>Difference</th><td>${money(report.difference)} TZS</td></tr>
    <tr><th>Unpaid guests</th><td>${report.unpaid_guest_count || 0}</td></tr>
    <tr><th>Unpaid total</th><td>${money(report.unpaid_total)} TZS</td></tr>
    <tr><th>Prior debt collected</th><td>${money(report.debt_collections_total)} TZS (${report.debt_collections_count || 0})</td></tr>
  </table>
  <h2>Items Sold</h2>
  <table class="items">
    <thead>
      <tr>
        <th>#</th><th>Time</th><th>Item</th><th>Guest</th><th>Qty</th><th>Payment</th><th>Amount (TZS)</th>
      </tr>
    </thead>
    <tbody>${itemRows}</tbody>
  </table>
  <h2>Unpaid Guests</h2>
  <table class="items">
    <thead>
      <tr>
        <th>#</th><th>Guest / Room</th><th>Orders</th><th>Qty</th><th>Amount Due (TZS)</th>
      </tr>
    </thead>
    <tbody>${unpaidRows}</tbody>
  </table>
  <div class="footer">Printed ${new Date().toLocaleString()}</div>
</body></html>`;
    }

    function printShiftCloseReport(report) {
      if (!report) return;
      const html = buildShiftReportHtml(report);
      let frame = document.getElementById('shiftReportPrintFrame');
      if (!frame) {
        frame = document.createElement('iframe');
        frame.id = 'shiftReportPrintFrame';
        frame.style.cssText = 'position:fixed;right:0;bottom:0;width:0;height:0;border:0;';
        document.body.appendChild(frame);
      }
      const doc = frame.contentWindow.document;
      doc.open();
      doc.write(html);
      doc.close();
      setTimeout(() => {
        frame.contentWindow.focus();
        frame.contentWindow.print();
      }, 400);
    }

    async function downloadShiftReportFile(report, downloadUrl) {
      if (!downloadUrl) {
        Swal.fire('Error', 'PDF link was not found. Close the shift again and try download.', 'error');
        return false;
      }

      try {
        const response = await fetch(downloadUrl, {
          method: 'GET',
          credentials: 'same-origin',
          headers: { 'Accept': 'application/pdf' }
        });

        if (!response.ok) {
          throw new Error('Server returned ' + response.status);
        }

        const blob = await response.blob();
        const type = (blob.type || response.headers.get('Content-Type') || '').toLowerCase();
        if (type.includes('text/html') && !type.includes('pdf')) {
          throw new Error('Server did not return a PDF file');
        }

        const stamp = (report && report.closed_at ? report.closed_at : '').replace(/[^\d]/g, '') || Date.now();
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'shift-report-' + stamp + '.pdf';
        document.body.appendChild(a);
        a.click();
        a.remove();
        setTimeout(() => URL.revokeObjectURL(url), 2000);
        return true;
      } catch (err) {
        console.error(err);
        Swal.fire('Download failed', (err && err.message) ? err.message : 'Could not download PDF.', 'error');
        return false;
      }
    }

    function openShift() {
      Swal.fire({
        title: "Open shift for today?",
        text: "This will record your clock-in time now.",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, open!",
        cancelButtonText: "Later"
      }).then((result) => {
        if (result.isConfirmed) {
          fetch('{{ route("bar-keeper.open-shift") }}', {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
              'Accept': 'application/json'
            }
          })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                Swal.fire("Success!", data.message, "success")
                  .then(() => location.reload());
              } else {
                Swal.fire("Error", data.message, "error");
              }
            })
            .catch(err => {
              console.error(err);
              Swal.fire("Error", "Something went wrong.", "error");
            });
        }
      });
    }

    $('#shiftClosureForm').on('submit', function (e) {
      e.preventDefault();
      const btn = $('#submitShiftBtn');
      const originalText = btn.html();
      btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Submitting...');

      const formData = new FormData(this);

      fetch('{{ route("bar-keeper.close-shift") }}', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: formData
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            $('#shiftClosureModal').modal('hide');
            const report = data.report || null;
            const money = (n) => new Intl.NumberFormat().format(Number(n || 0));
            const html = report ? `
              <div style="text-align:left;font-size:0.95rem;">
                <p><b>Opened:</b> ${report.opened_at || '—'}</p>
                <p><b>Closed:</b> ${report.closed_at || '—'}</p>
                <p><b>Duration:</b> ${report.duration_label || '—'}</p>
                <p><b>Total Guests:</b> ${report.total_guests || 0}</p>
                <p><b>Total Sales:</b> ${money(report.total_sales)} TZS</p>
                <p><b>Cash / M-Pesa / Other:</b> ${money(report.total_cash)} / ${money(report.total_mpesa)} / ${money(report.total_other)}</p>
              </div>` : data.message;

            const downloadUrl = data.download_url || null;
            Swal.fire({
              title: 'Shift Sales Report',
              html: html,
              icon: 'success',
              showDenyButton: true,
              showCancelButton: true,
              confirmButtonText: 'Print report',
              denyButtonText: 'Download report',
              cancelButtonText: 'Continue',
              allowOutsideClick: false
            }).then(async (result) => {
              if (result.isConfirmed) {
                printShiftCloseReport(report);
                setTimeout(() => location.reload(), 1500);
              } else if (result.isDenied) {
                const ok = await downloadShiftReportFile(report, downloadUrl);
                setTimeout(() => location.reload(), ok ? 2000 : 500);
              } else {
                location.reload();
              }
            });
          } else {
            Swal.fire("Attention", data.message, "warning");
            btn.prop('disabled', false).html(originalText);
          }
        })
        .catch(err => {
          console.error(err);
          Swal.fire("Error", "Something went wrong.", "error");
          btn.prop('disabled', false).html(originalText);
        });
    });
  </script>
@endsection
