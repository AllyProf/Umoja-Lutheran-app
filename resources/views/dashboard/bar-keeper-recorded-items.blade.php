@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-list"></i> Recorded Items</h1>
    <p>View all your recorded sales and ceremony consumption</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="#">Recorded Items</a></li>
  </ul>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
  <div class="col-md-2">
    <div class="widget-small primary coloured-icon">
      <i class="icon fa fa-shopping-cart fa-3x"></i>
      <div class="info">
        <h4>Total Items</h4>
        <p><b>{{ $stats['total_items'] }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-2">
    <div class="widget-small info coloured-icon">
      <i class="icon fa fa-check-circle fa-3x"></i>
      <div class="info">
        <h4>Paid</h4>
        <p><b>{{ $stats['total_paid'] }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-2">
    <div class="widget-small warning coloured-icon">
      <i class="icon fa fa-clock-o fa-3x"></i>
      <div class="info">
        <h4>Unpaid</h4>
        <p><b>{{ $stats['total_unpaid'] }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small success coloured-icon">
      <i class="icon fa fa-money fa-3x"></i>
      <div class="info">
        <h4>Total Revenue</h4>
        <p><b>{{ number_format($stats['total_revenue']) }} TZS</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small danger coloured-icon">
      <i class="icon fa fa-birthday-cake fa-3x"></i>
      <div class="info">
        <h4>Ceremony / Walk-in</h4>
        <p><b>{{ $stats['ceremony_items'] }} / {{ $stats['walk_in_items'] }}</b></p>
      </div>
    </div>
  </div>
</div>

<!-- Filters -->
<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn">
        <h3 class="title"><i class="fa fa-filter mr-2"></i>Filters</h3>
      </div>
      <div class="tile-body">
        <form method="GET" action="{{ route('bar-keeper.recorded-items') }}" class="row">
          <div class="col-md-3">
            <div class="form-group">
              <label>Start Date</label>
              <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label>End Date</label>
              <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label>Payment Status</label>
              <select name="payment_status" class="form-control">
                <option value="">All</option>
                <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Unpaid</option>
              </select>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label>Item Type</label>
              <select name="item_type" class="form-control">
                <option value="">All</option>
                <option value="ceremony" {{ request('item_type') == 'ceremony' ? 'selected' : '' }}>Ceremony</option>
                <option value="walk_in" {{ request('item_type') == 'walk_in' ? 'selected' : '' }}>Walk-in</option>
              </select>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label>&nbsp;</label>
              <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-search"></i> Filter</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Recorded Items Table -->
<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn">
        <h3 class="title"><i class="fa fa-list mr-2"></i>All Recorded Items ({{ $recordedItems->total() }})</h3>
      </div>
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Date & Time</th>
              <th>Item Name</th>
              <th>Type</th>
              <th>Ceremony/Guest</th>
              <th>Qty</th>
              <th>Unit Price</th>
              <th>Total</th>
              <th>Payment</th>
              <th>Method</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recordedItems as $item)
              @php
                $itemName = ($item->service_specific_data['item_name'] ?? null) 
                            ? $item->service_specific_data['item_name'] 
                            : ($item->service->name ?? 'Unknown Item');
              @endphp
              <tr>
                <td>
                  <small>{{ $item->created_at->format('M d, Y') }}</small><br>
                  <small class="text-muted">{{ $item->created_at->format('h:i A') }}</small>
                </td>
                <td><strong>{{ $itemName }}</strong></td>
                <td>
                  @if($item->day_service_id)
                    <span class="badge badge-info"><i class="fa fa-birthday-cake"></i> Ceremony</span>
                  @else
                    <span class="badge badge-secondary"><i class="fa fa-user"></i> Walk-in</span>
                  @endif
                </td>
                <td>
                  @if($item->dayService)
                    <strong>{{ $item->dayService->guest_name }}</strong><br>
                    <small class="text-muted">{{ $item->dayService->service_reference }}</small>
                  @else
                    <span class="text-muted">{{ $item->guest_name ?? 'Walk-in Guest' }}</span>
                  @endif
                </td>
                <td><strong>{{ $item->quantity }}</strong></td>
                <td>{{ number_format($item->unit_price_tsh) }} TZS</td>
                <td><strong>{{ number_format($item->total_price_tsh) }} TZS</strong></td>
                <td>
                  @if($item->payment_status === 'paid')
                    <span class="badge badge-success"><i class="fa fa-check"></i> Paid</span>
                  @else
                    <span class="badge badge-warning"><i class="fa fa-clock-o"></i> Unpaid</span>
                  @endif
                </td>
                <td>
                  @if($item->payment_method)
                    <small>{{ ucwords(str_replace('_', ' ', $item->payment_method)) }}</small>
                    @if($item->payment_reference)
                      <br><small class="text-muted">{{ $item->payment_reference }}</small>
                    @endif
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="text-center py-4">
                  <i class="fa fa-info-circle mr-2"></i>No recorded items found.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      
      @if($recordedItems->hasPages())
      <div class="d-flex justify-content-center mt-4">
        {{ $recordedItems->appends(request()->query())->links() }}
      </div>
      @endif
    </div>
  </div>
</div>
@endsection
