@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-history"></i> Kitchen Order History</h1>
        <p>Record of all served meals</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.restaurants.kitchen.orders') }}">Orders</a></li>
        <li class="breadcrumb-item">History</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="tile">
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>Completed At</th>
                            <th>Requested By</th>
                            <th>Room / Guest</th>
                            <th>Item Name</th>
                            <th>Qty</th>
                            <th>Total Price</th>
                            <th>Served By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($completedOrders as $order)
                        @php 
                            $foodId = $order->service_specific_data['food_id'] ?? null;
                            $recipe = $foodId ? \App\Models\Recipe::find($foodId) : null;
                        @endphp
                        <tr>
                            <td>{{ $order->completed_at->format('M d, Y H:i') }}</td>
                            <td>
                                @php
                                  $by = 'N/A';
                                  if ($order->reception_notes && str_contains($order->reception_notes, 'Waiter: ')) {
                                      $parts = explode('Waiter: ', $order->reception_notes);
                                      $byParts = explode(' - Msg:', $parts[1] ?? '');
                                      $by = $byParts[0] ?? 'Waiter';
                                  }
                                @endphp
                                <span class="badge badge-info">{{ $by }}</span>
                            </td>
                            <td>
                                @if($order->is_walk_in)
                                    <span class="badge badge-secondary mb-1">WALK-IN</span><br>
                                    <strong>{{ $order->walk_in_name ?? 'General Walk-in' }}</strong>
                                @else
                                    <strong>{{ $order->booking->room->room_number ?? 'Wait List' }}</strong><br>
                                    <small>{{ $order->booking->guest_name }}</small>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $order->service_specific_data['item_name'] ?? ($recipe ? $recipe->name : ($order->service->name ?? 'Unknown dish')) }}</strong>
                            </td>
                            <td>{{ $order->quantity }}</td>
                            <td>{{ number_format($order->total_price_tsh) }} TZS</td>
                            <td>{{ $order->approvedBy->name ?? 'Staff' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $completedOrders->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
