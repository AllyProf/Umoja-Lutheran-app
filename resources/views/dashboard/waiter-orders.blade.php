@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-history"></i> My Order History</h1>
        <p>View and track the status of orders you have submitted</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route('waiter.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item">History</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="tile">
            <h3 class="tile-title">Recent Orders</h3>
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>DateTime</th>
                            <th>Guest / Room</th>
                            <th colspan="3">Items Ordered</th>
                            <th>Payment</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                          $groupedOrders = $orders->groupBy(function($item) {
                              if ($item->is_walk_in) {
                                  return 'w_' . ($item->walk_in_name ?? 'General');
                              }
                              return 'b_' . ($item->booking_id ?? 'unknown');
                          });
                        @endphp

                        @forelse($groupedOrders as $groupKey => $orderGroup)
                          @php
                            $first = $orderGroup->first();
                            $latestRequest = $orderGroup->sortByDesc('requested_at')->first()->requested_at;
                            $totalAmount = $orderGroup->sum('total_price_tsh');
                          @endphp
                          
                          <tr style="border-top: 3px solid #e77a31;">
                            <td style="vertical-align: top;">
                              <strong>{{ $latestRequest->format('M d, H:i') }}</strong>
                            </td>
                            <td style="vertical-align: top;">
                              @if($first->is_walk_in)
                                  <span class="badge badge-info">Walk-in</span><br>
                                  <strong>{{ $first->walk_in_name ?? 'General Walk-in' }}</strong>
                              @else
                                  <span class="badge badge-primary">Room {{ $first->booking->room->room_number ?? 'N/A' }}</span><br>
                                  {{ $first->booking->guest_name ?? '' }}
                              @endif
                            </td>
                            <td colspan="3" class="p-0">
                              <table class="table table-sm mb-0" style="background: transparent;">
                                @foreach($orderGroup as $order)
                                <tr style="background: transparent;">
                                  <td style="width: 40%; border-top: none;">
                                    <strong>{{ $order->service_specific_data['item_name'] ?? $order->service->name }}</strong>
                                  </td>
                                  <td style="width: 15%; border-top: none;">
                                    Qty: <strong>{{ $order->quantity }}</strong>
                                  </td>
                                  <td style="width: 20%; border-top: none;">
                                    {{ number_format($order->total_price_tsh) }} TZS
                                  </td>
                                  <td style="width: 25%; border-top: none;">
                                    @php
                                        $statusClass = [
                                            'pending' => 'badge-secondary',
                                            'preparing' => 'badge-info',
                                            'ready' => 'badge-warning',
                                            'completed' => 'badge-success',
                                            'cancelled' => 'badge-danger'
                                        ][$order->status] ?? 'badge-secondary';
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ strtoupper($order->status) }}</span>
                                    
                                    @if($order->status === 'pending' || $order->status === 'preparing')
                                      <button type="button" class="btn btn-sm btn-outline-danger ml-1 p-0" 
                                              style="width: 22px; height: 22px; line-height: 20px;" 
                                              onclick="openCancelModal({{ $order->id }}, '{{ $order->service_specific_data['item_name'] ?? $order->service->name }}')" 
                                              title="Cancel Item">
                                        <i class="fa fa-times"></i>
                                      </button>
                                    @endif
                                  </td>
                                </tr>
                                @endforeach
                                <tr style="background: #f8f9fa;">
                                  <td colspan="2" style="border-top: 2px solid #dee2e6; text-align: right;"><strong>Total:</strong></td>
                                  <td style="border-top: 2px solid #dee2e6;"><strong>{{ number_format($totalAmount) }} TZS</strong></td>
                                  <td style="border-top: 2px solid #dee2e6;"></td>
                                </tr>
                              </table>
                            </td>
                            <td style="vertical-align: top;">
                              @if($first->payment_status === 'paid')
                                  <span class="badge badge-success">PAID</span><br>
                                  <small>{{ strtoupper(str_replace('_', ' ', $first->payment_method ?? '')) }}</small>
                              @elseif($first->payment_status === 'room_charge')
                                  <span class="badge badge-info">ROOM CHARGE</span>
                              @else
                                  <span class="badge badge-warning">PENDING</span>
                              @endif
                            </td>
                            <td style="vertical-align: top;">
                              @php
                                $printUrl = route('waiter.orders.print-group', [
                                    'is_walk_in' => $first->is_walk_in ? 1 : 0,
                                    'identifier' => $first->is_walk_in ? $first->walk_in_name : $first->booking_id
                                ]);
                              @endphp
                              
                              <button class="btn btn-sm btn-info mb-2" onclick="window.open('{{ $printUrl }}', 'Print', 'width=800,height=600')" title="Print All Items">
                                <i class="fa fa-print"></i> Print Bill
                              </button>
                              
                              @if($first->payment_status !== 'paid' && $first->payment_status !== 'room_charge')
                                  @if($first->is_walk_in)
                                      <a href="{{ route('waiter.dashboard', ['walk_in' => $first->walk_in_name]) }}" class="btn btn-sm btn-warning mb-1">
                                          <i class="fa fa-plus"></i> Add Items
                                      </a>
                                  @else
                                      <a href="{{ route('waiter.dashboard', ['room_id' => $first->booking_id]) }}" class="btn btn-sm btn-warning mb-1">
                                          <i class="fa fa-plus"></i> Add Items
                                      </a>
                                  @endif
                              @endif
                            </td>
                          </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No orders found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal for Cancellation Reason -->
<div class="modal fade" id="cancelOrderModal" tabindex="-1" role="dialog" aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="cancelOrderForm" method="POST">
        @csrf
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="cancelOrderModalLabel"><i class="fa fa-times-circle"></i> Cancel Order Item</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to cancel <strong id="cancelItemName"></strong>?</p>
          <div class="form-group">
            <label for="cancelReason">Reason for Cancellation <span class="text-danger">*</span></label>
            <textarea class="form-control" id="cancelReason" name="reason" rows="3" required placeholder="e.g., Guest changed mind, Out of stock, Wrong entry..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-danger">Confirm Cancellation</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
function openCancelModal(orderId, itemName) {
    const form = document.getElementById('cancelOrderForm');
    document.getElementById('cancelItemName').textContent = itemName;
    form.action = `/waiter/orders/${orderId}/cancel`;
    $('#cancelOrderModal').modal('show');
}
</script>
@endsection
