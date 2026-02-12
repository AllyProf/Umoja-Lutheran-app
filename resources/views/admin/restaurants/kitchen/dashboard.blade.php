@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-cutlery"></i> Kitchen Dashboard</h1>
        <p>Overview of Kitchen Operations, Stock, and Shopping Lists</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="#">Kitchen Dashboard</a></li>
    </ul>
</div>

<div class="row">
    <!-- Stats Cards -->
    @if(!$isChef)
    <div class="col-md-6 col-lg-3">
        <div class="widget-small primary coloured-icon shadow-sm"><i class="icon fa fa-shopping-basket fa-3x"></i>
            <div class="info">
                <h4>Shopping Lists</h4>
                <p><b>{{ $stats['shopping_lists'] }}</b></p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="widget-small warning coloured-icon shadow-sm"><i class="icon fa fa-clock-o fa-3x"></i>
            <div class="info">
                <h4>Pending Lists</h4>
                <p><b>{{ $stats['pending_lists'] }}</b></p>
            </div>
        </div>
    </div>
    @endif
    <div class="{{ $isChef ? 'col-md-6 col-lg-3' : 'col-md-6 col-lg-3' }}">
        <div class="widget-small danger coloured-icon shadow-sm"><i class="icon fa fa-cutlery fa-3x"></i>
            <div class="info">
                <h4>Pending Orders</h4>
                <p><b>{{ $totalPendingOrders }}</b></p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="widget-small success coloured-icon shadow-sm"><i class="icon fa fa-check fa-3x"></i>
            <div class="info">
                <h4>Today's Served</h4>
                <p><b>{{ $stats['today_orders'] }}</b></p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="widget-small primary coloured-icon shadow-sm"><i class="icon fa fa-money fa-3x"></i>
            <div class="info">
                <h4>Today's Revenue</h4>
                <p><b>{{ number_format($stats['total_revenue']) }}</b></p>
            </div>
        </div>
    </div>
    <div class="{{ $isChef ? 'col-md-6 col-lg-3' : 'col-md-6 col-lg-3' }}">
        <div class="widget-small info coloured-icon shadow-sm"><i class="icon fa fa-cubes fa-3x"></i>
            <div class="info">
                <h4>Stock Items</h4>
                <p><b>{{ $stats['stock_items'] }}</b></p>
            </div>
        </div>
    </div>
</div>

<!-- Live Food Orders -->
<div class="row mb-4 mt-4" id="orders">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn">
        <h3 class="title">Live Food Orders</h3>
        @if(!$isChef)
        <button class="btn btn-primary" onclick="openWalkInModal()"><i class="fa fa-plus"></i> New Walk-in Order</button>
        @endif
      </div>
      
      @if($pendingOrders->count() > 0)
      <h4 class="mb-3" style="font-size: 16px; color: #666;">Orders Queue</h4>
      <div class="table-responsive">
        <table class="table table-hover table-bordered">
          <thead>
            <tr>
              <th>Requested At</th>
              <th>By</th>
              <th>Room / Guest</th>
              <th>Item Name</th>
              <th>Qty</th>
              <th>Notes</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @php
              $groupedOrders = $pendingOrders->groupBy(function($item) {
                  return $item->is_walk_in ? 'w_' . ($item->walk_in_name ?? 'General') : 'b_' . ($item->booking_id ?? 'unknown');
              });
            @endphp

            @foreach($groupedOrders as $groupKey => $orders)
              @php
                $first = $orders->first();
                $guestTotal = $orders->where('payment_status', 'pending')->sum('total_price_tsh');
                // Use the most recent request time for the group
                $latestRequest = $orders->sortByDesc('requested_at')->first()->requested_at;
                
                // Get unique waiters
                $waiters = [];
                foreach($orders as $o) {
                    if ($o->reception_notes && str_contains($o->reception_notes, 'Waiter: ')) {
                        $parts = explode('Waiter: ', $o->reception_notes);
                        $byRaw = $parts[1] ?? 'Waiter';
                        $byNameOnly = explode(' | ', explode(' - Msg:', $byRaw)[0] ?? 'Waiter')[0];
                        $waiters[] = trim($byNameOnly);
                    }
                }
                $waiters = array_unique($waiters);
              @endphp
              
              <tr style="border-top: 3px solid #e77a31;">
                <td style="vertical-align: top;">
                  <strong>{{ $latestRequest->format('H:i') }}</strong><br>
                  <small class="text-muted">{{ $latestRequest->diffForHumans() }}</small>
                </td>
                <td style="vertical-align: top;">
                  @foreach($waiters as $w)
                    <span class="badge badge-info mb-1">{{ $w }}</span><br>
                  @endforeach
                </td>
                <td style="vertical-align: top;">
                  @if($first->is_walk_in)
                      <span class="badge badge-secondary mb-1">WALK-IN</span><br>
                      <strong>{{ $first->walk_in_name ?? 'General Walk-in' }}</strong>
                  @else
                      <strong>{{ $first->booking->room->room_number ?? 'N/A' }}</strong><br>
                      <small>{{ $first->booking->guest_name }}</small>
                  @endif
                </td>
                <td colspan="5" class="p-0">
                  <table class="table table-sm mb-0 no-border" style="background: transparent;">
                    @foreach($orders as $order)
                    <tr style="background: transparent;">
                      <td style="width: 30%; border-top: none;">
                        <strong>{{ $order->service_specific_data['item_name'] ?? $order->service->name }}</strong>
                      </td>
                      <td style="width: 10%; border-top: none;"><strong>{{ $order->quantity }}</strong></td>
                      <td style="width: 25%; border-top: none;">
                        @php
                          $note = $order->guest_request;
                          if (!$note && $order->reception_notes && str_contains($order->reception_notes, '- Msg: ')) {
                              $parts = explode('- Msg: ', $order->reception_notes);
                              $note = $parts[1] ?? null;
                          }
                        @endphp
                        <small class="text-muted">{{ $note ?: '---' }}</small>
                      </td>
                      <td style="width: 15%; border-top: none;">
                        @if($order->status === 'pending')
                            <span class="badge badge-warning">Pending</span>
                        @elseif($order->status === 'approved')
                            <span class="badge badge-info">Approved</span>
                        @elseif($order->status === 'preparing')
                            <span class="badge badge-primary">Preparing</span>
                        @elseif($order->status === 'completed' && ($order->payment_status === 'pending' || $order->payment_status === 'unpaid'))
                            <span class="badge badge-danger shadow-sm" style="font-size: 8px;"><i class="fa fa-money"></i> Unpaid</span>
                        @else
                            <span class="badge badge-secondary">{{ ucfirst($order->status) }}</span>
                        @endif
                      </td>
                      <td style="width: 20%; border-top: none; text-align: right;">
                        <div class="btn-group">
                          @if($order->status === 'pending')
                            <button class="btn btn-xs btn-primary p-1 px-2 mr-1" onclick="startPreparingDashboard({{ $order->id }}, '{{ $order->service_specific_data['item_name'] ?? $order->service->name }}')" title="Start Preparing">
                              <i class="fa fa-fire"></i>
                            </button>
                          @endif
                          @if($order->status !== 'completed')
                          <button class="btn btn-xs btn-success p-1 px-2" onclick="markAsServedOnly({{ $order->id }}, '{{ $order->service_specific_data['item_name'] ?? $order->service->name }}')" title="Mark Served">
                            <i class="fa fa-check"></i>
                          </button>
                          @endif
                        </div>
                      </td>
                    </tr>
                    @endforeach
                  </table>
                </td>
                <td style="vertical-align: top;">
                  <div class="btn-group-vertical btn-group-sm w-100">
                    @php
                      $printUrl = route('admin.restaurants.kitchen.orders.print-group', [
                          'is_walk_in' => $first->is_walk_in ? 1 : 0,
                          'identifier' => $first->is_walk_in ? $first->walk_in_name : $first->booking_id
                      ]);
                    @endphp
                    
                    <button class="btn btn-sm btn-info mb-1" onclick="window.open('{{ $printUrl }}', 'Print', 'width=800,height=600')" title="Print All Items">
                      <i class="fa fa-print"></i> Print Bill
                    </button>
                    
                    @if($first->is_walk_in && $first->payment_status === 'pending' && !$isChef)
                        <button class="btn btn-sm btn-warning mb-1" onclick="openWalkInModal(null, '{{ addslashes($first->walk_in_name) }}')" title="Add More Items">
                          <i class="fa fa-plus"></i> Add Items
                        </button>
                    @endif

                    @if($first->payment_status === 'pending')
                        <button class="btn btn-sm btn-outline-success mb-1" onclick="openPaymentModal({{ $first->id }}, {{ $guestTotal }}, '{{ addslashes($first->walk_in_name ?? $first->booking->room->room_number ?? '') }}', {{ $first->is_walk_in ? 1 : 0 }})" title="Record Total Payment">
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
                                        ->filter(function($req) {
                                            return $req->service && in_array($req->service->category, ['food', 'restaurant', 'kitchen']);
                                        })
                                        ->sum('total_price_tsh');
                                @endphp
                                <div class="btn-group">
                                    @if(!$isChef)
                                    <button class="btn btn-primary btn-sm rounded-pill px-3" onclick="openWalkInModal(null, '{{ $ceremony->guest_name }}', {{ $ceremony->id }})">
                                        <i class="fa fa-plus-circle mr-1"></i> Add Usage
                                    </button>
                                    @endif
                                    <a href="{{ route('chef-master.day-services.docket', $ceremony->id) }}" target="_blank" class="btn btn-secondary btn-sm rounded-pill px-3 ml-2" title="Print Docket">
                                        <i class="fa fa-print mr-1"></i> Print Docket
                                    </a>
                                    @if($unpaidUsage > 0)
                                        <button class="btn btn-success btn-sm rounded-pill px-3 ml-2" onclick="openCeremonyPaymentModal({{ $ceremony->id }}, '{{ $ceremony->guest_name }}', {{ $unpaidUsage }})">
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

<div class="row">
@if(!$isChef)
    <!-- Recent Shopping Lists -->
    <div class="col-md-6">
        <div class="tile shadow-sm">
            <h3 class="tile-title">Recent Shopping Lists</h3>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentLists as $list)
                        <tr>
                            <td>{{ $list->name }}</td>
                            <td>{{ $list->created_at->format('d M') }}</td>
                            <td>
                                @if($list->status == 'completed')
                                    <span class="badge badge-success">Completed</span>
                                @else
                                    <span class="badge badge-warning">Pending</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.restaurants.shopping-list.show', $list->id) }}" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="tile-footer border-top">
                <a href="{{ route('admin.restaurants.shopping-list.index') }}" class="btn btn-primary btn-block shadow-sm">View All Lists</a>
            </div>
        </div>
    </div>
@endif

    <!-- Quick Actions -->
    <div class="{{ $isChef ? 'col-md-12' : 'col-md-6' }}">
        <div class="tile shadow-sm h-100">
            <h3 class="tile-title">Quick Operational Actions</h3>
            <div class="row">
                @if(!$isChef)
                <div class="col-md-6 mb-3">
                    <a href="{{ route('admin.restaurants.shopping-list.create') }}" class="btn btn-outline-primary btn-block p-4 shadow-sm h-100 d-flex flex-column align-items-center justify-content-center">
                        <i class="fa fa-plus-square fa-2x mb-2"></i>
                        <span>New Shopping List</span>
                    </a>
                </div>
                @endif
                <div class="{{ $isChef ? 'col-md-3' : 'col-md-6' }} mb-3">
                    <a href="{{ route('admin.restaurants.kitchen.orders') }}" class="btn btn-warning btn-block p-4 shadow-sm h-100 d-flex flex-column align-items-center justify-content-center">
                        <i class="fa fa-bell fa-2x mb-2"></i>
                        <span>Live Food Orders</span>
                    </a>
                </div>
                <div class="{{ $isChef ? 'col-md-3' : 'col-md-6' }} mb-3">
                    <a href="{{ route('admin.recipes.index') }}" class="btn btn-success btn-block p-4 shadow-sm h-100 d-flex flex-column align-items-center justify-content-center">
                        <i class="fa fa-book fa-2x mb-2"></i>
                        <span>Recipes Catalog</span>
                    </a>
                </div>
                <div class="{{ $isChef ? 'col-md-3' : 'col-md-6' }} mb-3">
                    <a href="{{ $isChef ? route('chef-master.inventory') : route('admin.restaurants.kitchen.stock') }}" class="btn btn-info btn-block p-4 shadow-sm h-100 d-flex flex-column align-items-center justify-content-center">
                        <i class="fa fa-cubes fa-2x mb-2"></i>
                        <span>Inventory Stock</span>
                    </a>
                </div>
                <div class="{{ $isChef ? 'col-md-3' : 'col-md-6' }} mb-3">
                    <a href="{{ $isChef ? route('chef-master.reports') : route('admin.restaurants.reports') }}" class="btn btn-danger btn-block p-4 shadow-sm h-100 d-flex flex-column align-items-center justify-content-center">
                        <i class="fa fa-bar-chart fa-2x mb-2"></i>
                        <span>Kitchen Reports</span>
                    </a>
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
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text bg-white border-right-0"><i class="fa fa-search text-muted"></i></span>
                </div>
                <input type="text" id="itemSearch" class="form-control border-left-0" placeholder="Search food items..." onkeyup="filterItems()">
              </div>
            </div>
            
            <div class="p-3" id="posItemList">
              <div class="mb-4">
                <h6 class="text-primary mb-3 font-weight-bold d-flex align-items-center">
                    <i class="fa fa-cutlery mr-2"></i> Food & Restaurant
                </h6>
                <div class="row">
                  @foreach($recipes as $food)
                  <div class="col-6 mb-3 pos-item-card" data-name="{{ strtolower($food->name) }}">
                    <div class="card h-100 border rounded-lg hover-shadow transition-all" onclick="addToPosCart('{{ addslashes($food->name) }}', {{ $food->price_tsh }}, null, null, 'food', '{{ $food->id }}', '{{ $food->image }}')" style="cursor: pointer;">
                      <div class="card-body p-2 text-center">
                        <img src="{{ $food->image }}" class="img-fluid mb-2 rounded" style="height: 60px; object-fit: contain;">
                        <h6 class="card-title mb-1 text-dark" style="font-size: 0.85rem; height: 1.5rem; overflow: hidden;">{{ $food->name }}</h6>
                        <div class="badge badge-light-info px-2 py-1">{{ number_format($food->price_tsh) }} TZS</div>
                      </div>
                    </div>
                  </div>
                  @endforeach
                </div>
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
               <textarea id="walkInSpecialNotes" class="form-control" rows="2" placeholder="Special Notes (e.g. No spicy, Well done)"></textarea>
            </div>
            
            <div class="flex-grow-1 p-3" id="posCartList" style="overflow-y: auto;">
              <div class="text-center text-muted mt-5">
                <i class="fa fa-shopping-basket fa-3x mb-3 opacity-50"></i>
                <p>Click items on the left to add to cart</p>
              </div>
            </div>
            
            <div class="p-3 border-top bg-white mt-auto">
              <div class="d-flex justify-content-between mb-3">
                <span class="font-weight-bold text-muted">Total Amount:</span>
                <span class="font-weight-bold text-primary h5 mb-0" id="posTotalAmount">0 TZS</span>
              </div>
              <button class="btn btn-success btn-lg btn-block shadow-sm py-3" onclick="processWalkInCheckout()" id="btnConfirmSale" disabled>
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
.hover-shadow:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border-color: #007bff !important;
}
.transition-all {
    transition: all 0.2s ease-in-out;
}
.badge-light-primary {
    background-color: #e3f2fd;
    color: #1976d2;
}
.badge-light-info {
    background-color: #e0f7fa;
    color: #0097a7;
}
</style>



<!-- Payment Modal HTML -->
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
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
                <p class="h4 text-center mb-4">Amount: <span id="paymentAmountDisplay" class="font-weight-bold text-primary"></span></p>
                
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
<div class="modal fade" id="ceremonyPaymentModal" tabindex="-1" role="dialog" aria-labelledby="ceremonyPaymentModalLabel" aria-hidden="true">
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
                <p class="h5 text-center mb-2">Guest: <span id="ceremonyGuestName" class="font-weight-bold text-info"></span></p>
                <p class="h4 text-center mb-4">Unpaid Amount: <span id="ceremonyUnpaidAmount" class="font-weight-bold text-danger"></span></p>
                
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

@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<script>
// POS Logic
let posCart = [];

function openWalkInModal(residentRoom = null, residentName = null, dayServiceId = null) {
    $('#walkInModal').modal('show');
    posCart = [];
    
    // Reset inputs
    document.getElementById('walkInGuestName').value = residentName || '';
    if(document.getElementById('walkInSpecialNotes')) {
        document.getElementById('walkInSpecialNotes').value = '';
    }
    
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

function addToPosCart(name, price, pid, vid, type, foodId = null, image = null) {
    const key = foodId ? foodId : `${pid}_${vid}`;
    const existing = posCart.find(item => item.key === key);
    
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
            qty: 1
        });
    }
    renderPosCart();
}

function removeFromPosCart(key) {
    posCart = posCart.filter(item => item.key !== key);
    renderPosCart();
}

function updatePosQty(key, delta) {
    const item = posCart.find(i => i.key === key);
    if (item) {
        item.qty += delta;
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
                    <div class="text-right mr-2" style="min-width: 70px;">
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

function filterItems() {
    const query = document.getElementById('itemSearch').value.toLowerCase();
    const cards = document.querySelectorAll('.pos-item-card');
    cards.forEach(card => {
        if (card.dataset.name.includes(query)) card.style.display = '';
        else card.style.display = 'none';
    });
}

async function processWalkInCheckout() {
    const guestName = document.getElementById('walkInGuestName').value.trim() || 'General Walk-in';
    const specialNotes = document.getElementById('walkInSpecialNotes').value.trim();
    
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
    for (const item of posCart) {
        const payload = {
            service_id: 48, // Restaurant Food Order service ID
            quantity: item.qty,
            is_walk_in: 1,
            walk_in_name: guestName,
            day_service_id: window.currentDayServiceId || null,
            payment_timing: 'later',
            item_name: item.name,
            service_specific_data: {
                food_id: item.foodId,
                item_name: item.name,
                special_notes: specialNotes
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
            if (res.success) successCount++;
        } catch (e) {
            console.error(e);
        }
    }

    if (successCount === posCart.length) {
        await Swal.fire({
            title: "Success!",
            text: "Walk-in order recorded successfully!",
            icon: "success",
            timer: 2000,
            showConfirmButton: false
        });
        location.reload();
    } else {
        await Swal.fire("Completed with issues", `Only ${successCount} of ${posCart.length} items were recorded.`, "warning");
        location.reload();
    }
}

function completeOrder(orderId, paymentMethod = 'room_charge', reference = '') {
    let title = paymentMethod === 'cash' ? "Record Cash Payment" : "Charge to Room";
    let text = paymentMethod === 'cash' ? "Record this walk-in sale as PAID (Cash)?" : "Mark this order as served and charge to the ROOM bill?";
    let icon = paymentMethod === 'cash' ? "success" : "info";
    let btnColor = paymentMethod === 'cash' ? "#28a745" : "#007bff";

    if (paymentMethod !== 'cash' && paymentMethod !== 'room_charge') {
        title = "Record " + paymentMethod.replace('_', ' ').toUpperCase() + " Payment";
        text = "Record this sale as PAID via " + paymentMethod.replace('_', ' ').concat(reference ? " (Ref: " + reference + ")" : "") + "?";
        icon = "warning";
    }
    
    swal({
        title: title,
        text: text,
        type: icon,
        showCancelButton: true,
        confirmButtonColor: btnColor,
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, Proceed",
        cancelButtonText: "Cancel",
        closeOnConfirm: false
    }, function(isConfirm) {
        if (isConfirm) {
            // Use the kitchen order complete route
            const url = `/restaurant/food/orders/${orderId}/complete`;
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    payment_method: paymentMethod,
                    payment_reference: reference
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    swal({
                        title: "Success!",
                        text: data.message,
                        type: "success",
                        timer: 2000,
                        showConfirmButton: false
                    });
                    setTimeout(() => location.reload(), 1500);
                } else {
                    swal("Error!", data.message, "error");
                }
            })
            .catch(error => {
                console.error('Error:', error);
                swal("Error!", "An error occurred. Please try again.", "error");
            });
        }
    });
}

function markAsServedOnly(orderId, itemName) {
    swal({
        title: "Confirm Service",
        text: "Confirm that " + itemName + " has been served? (Payment status will not be changed)",
        type: "success",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        confirmButtonText: "Yes, Served",
        closeOnConfirm: false
    }, function(isConfirm) {
        if (isConfirm) {
            const url = `/restaurant/food/orders/${orderId}/complete`;
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({}) // Empty body to skip payment update
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    swal("Success", "Order marked as served!", "success");
                    setTimeout(() => location.reload(), 1500);
                } else {
                    swal("Error!", data.message, "error");
                }
            })
            .catch(e => {
                console.error(e);
                swal("Error", "Failed to communicate with server", "error");
            });
        }
    });
}

function startPreparingDashboard(orderId, itemName) {
    swal({
        title: "Start Preparation?",
        text: "Begin preparing " + itemName + "?",
        type: "info",
        showCancelButton: true,
        confirmButtonColor: "#3498db",
        confirmButtonText: "Yes, Start!",
        closeOnConfirm: false
    }, function(isConfirm) {
        if (isConfirm) {
            const url = `/restaurant/food/orders/${orderId}/preparing`;
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
                    swal("Started!", "Preparation timer begun.", "success");
                    setTimeout(() => location.reload(), 1500);
                } else {
                    swal("Error!", data.message, "error");
                }
            })
            .catch(e => {
                console.error(e);
                swal("Error", "Failed to communicate with server", "error");
            });
        }
    });
}

function openPaymentModal(orderId, amount, guestName = '', isWalkIn = 0) {
    document.getElementById('paymentOrderId').value = orderId;
    
    let displayAmount = Number(amount).toLocaleString() + ' TZS';
    if (guestName) {
        displayAmount += ' (Guest Total)';
        console.log("Group payment for: " + guestName);
    }
    
    document.getElementById('paymentAmountDisplay').innerText = displayAmount;
    
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
    if (method === 'cash' || method === 'room_charge') {
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
        swal("Missing Info", "Please enter a reference number for " + method.replace('_', ' ').toUpperCase(), "warning");
        return;
    }
    
    $('#paymentModal').modal('hide');
    
    // For the dashboard, we use a unified settlement function
    settlePOSPayment(orderId, method, reference);
}

function settlePOSPayment(orderId, method, reference = '') {
    swal({
        title: "Confirm Payment?",
        text: `Record ${method.toUpperCase()} payment of ${document.getElementById('paymentAmountDisplay').innerText}?`,
        type: "success",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        confirmButtonText: "Yes, Paid!",
        closeOnConfirm: false
    }, function(isConfirm) {
        if (isConfirm) {
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
                    swal("Success!", data.message, "success");
                    setTimeout(() => location.reload(), 1500);
                } else {
                    swal("Error!", data.message, "error");
                }
            })
            .catch(error => {
                console.error('Error:', error);
                swal("Error!", "Failed to record payment.", "error");
            });
        }
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
        swal("Missing Info", "Please enter a reference number for " + method.replace('_', ' ').toUpperCase(), "warning");
        return;
    }

    swal({
        title: "Confirm Settlement",
        text: "Mark all unpaid consumption for this ceremony as paid?",
        type: "info",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, Settle!",
        cancelButtonText: "Cancel",
        closeOnConfirm: false
    }, function(isConfirm) {
        if (isConfirm) {
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
                    swal({
                        title: "Success!",
                        text: data.message + ` (${data.count} items settled)`,
                        type: "success",
                        timer: 2000,
                        showConfirmButton: false
                    });
                    setTimeout(() => location.reload(), 1500);
                } else {
                    swal("Error!", data.message, "error");
                }
            })
            .catch(error => {
                console.error('Error:', error);
                swal("Error!", "Failed to settle payment. Please try again.", "error");
            });
        }
    });
}
    function printDocket(orderId) {
        const url = `/restaurant/food/orders/${orderId}/print-docket`;
        window.open(url, 'KitchenDocketPrint', 'width=400,height=600');
    }
</script>
@endsection
