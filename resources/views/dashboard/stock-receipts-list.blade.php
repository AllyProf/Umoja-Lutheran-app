@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-clipboard-list"></i> {{ isset($type) ? ucfirst($type) : 'Stock' }} Receipts</h1>
    <p>View all {{ $type ?? '' }} stock receipts</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Stock Receipts</a></li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn mb-3">
        <h3 class="title">{{ isset($type) ? ucfirst($type) : 'All' }} Stock Receipts</h3>
        <div class="btn-group">
          <a class="btn btn-primary" href="{{ route('admin.stock-receipts.create', ['type' => $type ?? '']) }}">
            <i class="fa fa-plus"></i> New {{ $type ? ucfirst($type) : '' }} Stock Receipt
          </a>
        </div>
      </div>
      
      @if($stockReceipts->count() > 0)
      <div class="table-responsive">
        <table class="table table-hover table-bordered">
          <thead>
            <tr>
              <th>Date</th>
              <th>Product</th>
              <th>Variant</th>
              <th>Supplier</th>
              <th>Packages</th>
              <th>Total PIC</th>
              <th>Buying Price</th>
              <th>Selling Price</th>
              <th>Total Cost</th>
              <th>Total Profit</th>
              <th>Received By</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($stockReceipts as $receipt)
            <tr>
              <td>{{ $receipt->received_date->format('M d, Y') }}</td>
              <td><strong>{{ $receipt->product->name }}</strong></td>
              <td>
                {{ $receipt->productVariant->measurement }}
                <br><small class="text-muted">{{ $receipt->productVariant->packaging_name ?? $receipt->productVariant->packaging }} - {{ $receipt->productVariant->items_per_package }} PIC/{{ \Illuminate\Support\Str::singular($receipt->productVariant->packaging_name ?? $receipt->productVariant->packaging) }}</small>
              </td>
              <td>{{ $receipt->supplier->name }}</td>
              <td>{{ $receipt->quantity_received_packages }}</td>
              <td>{{ number_format($receipt->total_bottles) }}</td>
              <td>{{ number_format($receipt->buying_price_per_bottle, 2) }} TSh/PIC</td>
              <td>{{ number_format($receipt->selling_price_per_bottle, 2) }} TSh/PIC</td>
              <td>{{ number_format($receipt->total_buying_cost, 2) }} TSh</td>
              <td class="text-success"><strong>{{ number_format($receipt->total_profit, 2) }} TSh</strong></td>
              <td>{{ $receipt->receivedBy->name ?? 'N/A' }}</td>
              <td class="text-center">
                <a href="{{ route('admin.stock-receipts.download', $receipt->id) }}" target="_blank" class="btn btn-sm btn-info" title="Download Receipt">
                  <i class="fa fa-download"></i>
                </a>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      
      <div class="d-flex justify-content-center mt-3">
        {{ $stockReceipts->appends(request()->query())->links('pagination::bootstrap-4') }}
      </div>
      @else
      <div class="text-center" style="padding: 50px;">
        <i class="fa fa-clipboard-list fa-5x text-muted mb-3"></i>
        <h3>No Stock Receipts</h3>
        <p class="text-muted">Start by creating your first stock receipt.</p>
        <a href="{{ route('admin.stock-receipts.create') }}" class="btn btn-primary mt-3">
          <i class="fa fa-plus"></i> New Stock Receipt
        </a>
      </div>
      @endif
    </div>
  </div>
</div>

@endsection


