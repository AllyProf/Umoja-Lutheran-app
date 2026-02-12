
@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-cubes"></i> Bar Stock Overview (Global)</h1>
    <p>Global visual overview of current restaurant and bar stock levels</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Restaurants</a></li>
    <li class="breadcrumb-item"><a href="#">Stock Overview</a></li>
  </ul>
</div>

<div class="row">
  @if(count($allStock) > 0)
    @foreach($allStock as $item)
    <div class="col-md-3">
      <div class="tile">
        <div class="tile-title-w-btn">
             <!-- Status Badge -->
            @if($item['current_stock'] <= 5) 
                <span class="badge badge-danger">Low Stock</span>
            @else
                <span class="badge badge-success">In Stock</span>
            @endif
        </div>
        
        <div class="text-center mt-2 mb-3">
            <!-- Product Image -->
            @if(!empty($item['product_image']) && file_exists(public_path('storage/' . $item['product_image'])))
                <img src="{{ asset('storage/' . $item['product_image']) }}" 
                     alt="{{ $item['product_name'] }}" 
                     class="img-fluid rounded" 
                     style="max-height: 150px; object-fit: cover;">
            @else
                <div class="d-flex align-items-center justify-content-center bg-light rounded" style="height: 150px; width: 100%;">
                    <i class="fa fa-cube fa-4x text-muted" style="opacity: 0.3;"></i>
                </div>
            @endif
        </div>
        
        <h4 class="tile-title text-center text-truncate" title="{{ $item['product_name'] }}">
            {{ $item['product_name'] }}
        </h4>
        
        <p class="text-center text-muted mb-3">
            {{ $item['variant_name'] }} ({{ $item['packaging'] }})
        </p>
        
        <hr>
        
        <div class="row text-center mt-3">
            <div class="col-12 mb-2">
                <span class="d-block text-muted small uppercase">Selling Price</span>
                <span class="h6 font-weight-bold text-dark">
                    {{ $item['selling_price'] ? number_format($item['selling_price']) . ' TZS' : 'N/A' }}
                </span>
            </div>
            
            <div class="col-6 border-right">
                <small class="text-muted d-block uppercase">Sold (Global)</small>
                <span class="h5 {{ $item['total_sold'] > 0 ? 'text-primary' : 'text-muted' }}">
                    {{ number_format($item['total_sold']) }}
                </span>
                <small class="d-block text-success">
                    <i class="fa fa-money"></i> {{ number_format($item['generated_revenue']) }}
                </small>
            </div>
            <div class="col-6">
                <small class="text-muted d-block uppercase">Stock</small>
                <span class="h5 font-weight-bold {{ $item['current_stock'] <= 5 ? 'text-danger' : 'text-success' }}">
                    {{ $item['stock_breakdown'] }}
                </span>
                @if(!empty($item['total_bottles_display']))
                <small class="d-block text-muted">
                    {{ $item['total_bottles_display'] }}
                </small>
                @endif
            </div>
        </div>
      </div>
    </div>
    @endforeach
  @else
    <div class="col-md-12">
        <div class="tile text-center p-5">
            <i class="fa fa-cubes fa-5x text-muted mb-4" style="opacity: 0.5;"></i>
            <h3>No Stock Data</h3>
            <p class="text-muted">No restaurant/bar stock activity found.</p>
        </div>
    </div>
  @endif
</div>
@endsection
