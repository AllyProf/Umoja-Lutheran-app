@extends('dashboard.layouts.app')

@section('content')
  <div class="tile-title-w-btn">
    <div>
      <h1><i class="fa fa-dashboard"></i> Counter Dashboard</h1>
      @if($activeShift)
        <p class="text-success mb-0 font-weight-bold"><i class="fa fa-clock-o"></i> Shift Active since
          {{ $activeShift->opened_at->format('H:i') }} ({{ $activeShift->opened_at->diffForHumans() }})
        </p>
      @else
        <p class="text-danger mb-0 font-weight-bold"><i class="fa fa-warning"></i> Shift is CLOSED. Please open a shift to
          record sales.</p>
      @endif
    </div>
    <div>
      @if($activeShift)
        <button class="btn btn-warning shadow-sm" onclick="openShiftClosureModal()">
          <i class="fa fa-power-off"></i> Funga Hesabu (End Shift)
        </button>
      @else
        <button class="btn btn-success btn-lg shadow-sm" onclick="openShift()">
          <i class="fa fa-play-circle"></i> FUNGUA SHIFT (Check-in)
        </button>
      @endif
    </div>
  </div>
  <ul class="app-breadcrumb breadcrumb">
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
          <button class="btn btn-primary {{ !$activeShift ? 'disabled' : '' }}" {{ !$activeShift ? 'disabled' : '' }}
            onclick="openWalkInModal()"><i class="fa fa-plus"></i> New Walk-in Sale</button>
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
                    $guestTotal = $orderGroup->where('payment_status', 'pending')->sum('total_price_tsh');
                    $latestRequest = $orderGroup->sortByDesc('requested_at')->first()->requested_at;
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
                        <span class="badge badge-primary mb-1">Room {{ $first->booking->room->room_number ?? 'N/A' }}</span><br>
                        <strong>{{ $first->booking->guest_name }}</strong>
                      @endif
                    </td>
                    <td colspan="4" class="p-0">
                      <table class="table table-sm mb-0" style="background: transparent;">
                        @foreach($orderGroup as $order)
                          <tr style="background: transparent;">
                            <td style="width: 35%; border-top: none;">
                              <strong>{{ $order->service_specific_data['item_name'] ?? $order->service->name }}</strong>
                              @if($order->payment_status === 'pending')
                                <br><span class="badge badge-warning" style="font-size: 9px;">UNPAID</span>
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

                              @if(!$order->is_walk_in)
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
                                Pending:</strong></td>
                            <td colspan="2" style="border-top: 1px solid #b2dfdb;"><strong>{{ number_format($guestTotal) }}
                                TZS</strong></td>
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

  <!-- Today's Verified Sales -->
  <div class="row mb-4">
    <div class="col-md-12">
      <div class="tile shadow-sm" style="border-top: 4px solid #28a745;">
        <div class="tile-title-w-btn">
          <h3 class="title"><i class="fa fa-history text-success"></i> Today's Sales (Verification History)</h3>
          <span class="badge badge-success">{{ $completedSales->count() }} Orders</span>
        </div>

        @if($completedSales->count() > 0)
          <div class="table-responsive">
            <table class="table table-sm table-hover table-bordered">
              <thead class="bg-light">
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
                  <tr>
                    <td>{{ \Carbon\Carbon::parse($sale->completed_at)->format('H:i') }}</td>
                    <td>
                      @if($sale->is_walk_in)
                        <span class="badge badge-info">Walk-in: {{ $sale->walk_in_name }}</span>
                      @else
                        <strong>Room {{ $sale->booking->room->room_number ?? 'N/A' }}</strong><br>
                        <small>{{ $sale->booking->guest_name ?? '' }}</small>
                      @endif
                    </td>
                    <td>{{ $sale->service_specific_data['item_name'] ?? $sale->service->name }}</td>
                    <td>{{ $sale->quantity }}</td>
                    <td class="font-weight-bold">{{ number_format($sale->total_price_tsh) }}</td>
                    <td><span class="badge badge-outline-secondary">{{ strtoupper($sale->payment_method) }}</span></td>
                    <td><small class="text-muted">{{ $sale->payment_reference ?: '-' }}</small></td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <div class="text-center py-4">
            <p class="text-muted"><i class="fa fa-info-circle"></i> Hakuna mauzo yaliyokamilishwa bado kwa leo.</p>
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
                    <td><strong>{{ $transfer->product->name }}</strong></td>
                    <td>{{ $transfer->productVariant->measurement }} ({{ $transfer->productVariant->packaging }})</td>
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
                    <td><strong>{{ $transfer->product->name }}</strong></td>
                    <td>{{ $transfer->productVariant->measurement }} ({{ $transfer->productVariant->packaging }})</td>
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
              <span>New Walk-in Sale</span>
            </button>
          </div>
          <div class="col-md-3 mb-3">
            <a href="{{ route('bar-keeper.stock.index') }}"
              class="btn btn-info btn-block p-4 shadow-sm h-100 d-flex flex-column align-items-center justify-content-center {{ !$activeShift ? 'disabled' : '' }}"
              {{ !$activeShift ? 'onclick="return false;"' : '' }}>
              <i class="fa fa-cubes fa-2x mb-2"></i>
              <span>Bar Inventory</span>
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
                <input type="text" id="walkInGuestName" class="form-control mb-2" placeholder="Guest Name (Optional)">

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

                {{-- Print Kitchen Docket Button --}}
                <button class="btn btn-warning btn-block mb-2 shadow-sm py-2" onclick="printKitchenDocket()"
                  id="btnPrintDocket" disabled>
                  <i class="fa fa-print mr-1"></i> <strong>PRINT KITCHEN DOCKET</strong>
                </button>

                <button class="btn btn-success btn-lg btn-block shadow-sm py-3" onclick="processWalkInCheckout()"
                  id="btnConfirmSale" disabled>
                  <i class="fa fa-check-circle mr-1"></i> <strong>RECORD WALK-IN ORDER</strong>
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
  <div class="modal fade" id="shiftClosureModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title font-weight-bold"><i class="fa fa-calculator"></i> Muhtasari wa Shift Yako</h5>
          <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div id="shiftSummaryLoading" class="text-center py-4">
            <i class="fa fa-spinner fa-spin fa-2x text-warning"></i>
            <p class="mt-2">Inapakia mauzo...</p>
          </div>
          <div id="shiftSummaryContent" style="display: none;">
            <table class="table table-bordered mb-4">
              <tr>
                <th class="bg-light">Jumla ya Cash (Hela Mkononi)</th>
                <td id="summaryTotalCash" class="text-right font-weight-bold text-success" style="font-size: 1.2em;">0
                </td>
              </tr>
              <tr>
                <th class="bg-light">Mauzo ya M-Pesa/Mobile</th>
                <td id="summaryTotalMpesa" class="text-right">0</td>
              </tr>
              <tr>
                <th class="bg-light">Mauzo Mengine (Bank)</th>
                <td id="summaryTotalOther" class="text-right">0</td>
              </tr>
              <tr class="table-info">
                <th>Idadi ya Huduma Zote</th>
                <td id="summaryTotalCount" class="text-right">0</td>
              </tr>
            </table>

            <form id="shiftClosureForm">
              @csrf
              <div class="form-group">
                <label><b>Kiasi cha Cash Unachokabidhi (TZS)</b></label>
                <input type="number" name="amount_submitted" id="amountSubmitted"
                  class="form-control form-control-lg border-primary" required>
                <small class="text-muted">Andika kiasi cha hela ghafi unachopeleka Reception hivi sasa.</small>
              </div>
              <div class="form-group">
                <label>Maelezo ya Ziada (Notes)</label>
                <textarea name="notes" class="form-control" rows="2"
                  placeholder="Mfano: Cash imepungua kidogo sababu ya chenji..."></textarea>
              </div>
              <div class="alert alert-warning py-2 small">
                <i class="fa fa-info-circle"></i> Ukibonyeza "Kamilisha Handover", mauzo yako ya leo yatafungwa na
                hayawezi kubadilishwa tena.
              </div>
              <button type="submit" class="btn btn-warning btn-block btn-lg font-weight-bold" id="submitShiftBtn">
                <i class="fa fa-check-circle"></i> Kamilisha Handover
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

    // Data for Dynamic Food selection
    const foodBasesData = @json($foodBases ?? []);

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

      // Reset inputs
      document.getElementById('walkInGuestName').value = residentName || '';

      // Store dayServiceId in a global variable for checkout
      window.currentDayServiceId = dayServiceId;

      // If it's for a ceremony, update title/label
      if (dayServiceId) {
        document.getElementById('posModalTitle').innerText = 'Record Usage: ' + residentName;
        document.getElementById('btnConfirmSale').innerHTML = '<i class="fa fa-check-circle mr-1"></i> <strong>RECORD CEREMONY USAGE</strong>';
      } else {
        document.getElementById('posModalTitle').innerText = 'New Walk-in Order';
        document.getElementById('btnConfirmSale').innerHTML = '<i class="fa fa-check-circle mr-1"></i> <strong>RECORD WALK-IN ORDER</strong>';
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
      const docketBtn = document.getElementById('btnPrintDocket');

      if (posCart.length === 0) {
        list.innerHTML = `<div class="text-center text-muted mt-5"><i class="fa fa-shopping-basket fa-3x mb-2"></i><p>Cart is empty</p></div>`;
        totalEl.innerText = '0 TZS';
        btn.disabled = true;
        if (docketBtn) docketBtn.disabled = true;
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
      if (docketBtn) docketBtn.disabled = false;
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

      let rows = '';
      posCart.forEach(item => {
        rows += `<tr>
                              <td style="padding:4px 0; font-size:15px; font-weight:bold; width:30px; border-bottom:1px solid #eee;">${item.qty}x</td>
                              <td style="padding:4px 0; font-size:15px; border-bottom:1px solid #eee;">${item.name}</td>
                            </tr>`;
      });

      const docketHtml = `<!DOCTYPE html>
                    <html><head>
                    <meta charset="UTF-8"><title>Kitchen Docket</title>
                    <style>
                      * { margin:0; padding:0; box-sizing:border-box; }
                      body { font-family:'Courier New',monospace; width:80mm; padding:10px; color:#000; background:#white; }
                      .center { text-align:center; }
                      .divider { border-top:2px dashed #000; margin:8px 0; }
                      .hotel { font-size:13px; font-weight:bold; }
                      .title { font-size:20px; font-weight:bold; margin:4px 0; letter-spacing:2px; }
                      .info { font-size:13px; margin:3px 0; }
                      table { width:100%; border-collapse:collapse; margin-top:5px; }
                      td { vertical-align:top; }
                      .footer { font-size:12px; margin-top:10px; font-style:italic; }
                      @media print { body { margin:0; padding:10mm; } }
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
                      <table><tbody>${rows}</tbody></table>
                      <div class="divider"></div>
                      <div class="center footer">-- Tafadhali andaa haraka --</div>
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
      const guestName = document.getElementById('walkInGuestName').value.trim() || 'Walk-in (' + timeStr + ')';

      const confirm = await Swal.fire({
        title: "Confirm Sale?",
        text: `Record order for ${guestName}?`,
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Yes, Record Sale"
      });

      if (!confirm.isConfirmed) return;

      $('#walkInModal').modal('hide');
      Swal.fire({
        title: 'Processing...',
        didOpen: () => { Swal.showLoading(); }
      });

      let successCount = 0;
      let lastError = "An unknown error occurred.";

      for (const item of posCart) {
        const payload = {
          service_id: item.type === 'food' ? "{{ $foodServiceId ?? '' }}" : "{{ $barServiceId ?? '' }}",
          product_id: item.pid,
          product_variant_id: item.vid,
          quantity: item.qty,
          is_walk_in: 1,
          walk_in_name: guestName,
          day_service_id: window.currentDayServiceId || null,
          payment_timing: 'later',
          item_name: item.name,
          service_specific_data: {
            food_id: item.foodId,
            item_name: item.name,
            product_id: item.pid,
            product_variant_id: item.vid,
            selling_method: item.unit || 'pic'
          }
        };

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
              lastError += ": " + JSON.stringify(res.errors);
            }
            console.error("Item failed:", item.name, res);
          }
        } catch (e) {
          console.error(e);
          lastError = e.message;
        }
      }

      if (successCount === posCart.length) {
        await Swal.fire({
          title: "Success!",
          text: "Walk-in sale recorded successfully!",
          icon: "success",
          timer: 2000,
          showConfirmButton: false
        });
        window.location.reload();
      } else {
        await Swal.fire({
          title: "Error",
          text: "Some items failed to process. Reason: " + lastError,
          icon: "error"
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
        inputPlaceholder: 'Ingiza sababu ya ku-cancel order hapa (Mandatory)...',
        inputAttributes: {
          'aria-label': 'Sababu ya ku-cancel'
        },
        showCancelButton: true,
        confirmButtonColor: "#dc3545",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, Cancel Order!",
        cancelButtonText: "No, Keep It",
        preConfirm: (reason) => {
          if (!reason || reason.trim().length < 5) {
            Swal.showValidationMessage(`Tafadhali ingiza sababu yenye maana (angalau herufi 5)`);
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
            $('#summaryTotalCash').text(new Intl.NumberFormat().format(data.summary.total_cash));
            $('#summaryTotalMpesa').text(new Intl.NumberFormat().format(data.summary.total_mpesa));
            $('#summaryTotalOther').text(new Intl.NumberFormat().format(data.summary.total_other));
            $('#summaryTotalCount').text(data.summary.count);

            // Pre-fill amount submitted with total cash as a helpful default
            $('#amountSubmitted').val(data.summary.total_cash);

            $('#shiftSummaryLoading').hide();
            $('#shiftSummaryContent').show();
          } else {
            Swal.fire("Error", "Imeshindwa kupata muhtasari wa shift.", "error");
            $('#shiftClosureModal').modal('hide');
          }
        })
        .catch(err => {
          console.error(err);
          Swal.fire("Error", "Kuna tatizo la mtandao.", "error");
          $('#shiftClosureModal').modal('hide');
        });
    }

    function openShift() {
      Swal.fire({
        title: "Fungua Shift Leo?",
        text: "Hii itarekodi muda wako wa kuingia sasa hivi.",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Ndiyo, Fungua!",
        cancelButtonText: "Badae"
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
              Swal.fire("Error", "Kuna tatizo limetokea.", "error");
            });
        }
      });
    }

    $('#shiftClosureForm').on('submit', function (e) {
      e.preventDefault();
      const btn = $('#submitShiftBtn');
      const originalText = btn.html();
      btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Inatuma...');

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
            Swal.fire("Shift Imefungwa!", data.message, "success")
              .then(() => {
                location.reload();
              });
          } else {
            Swal.fire("Attention", data.message, "warning");
            btn.prop('disabled', false).html(originalText);
          }
        })
        .catch(err => {
          console.error(err);
          Swal.fire("Error", "Kuna tatizo limetokea.", "error");
          btn.prop('disabled', false).html(originalText);
        });
    });
  </script>
@endsection