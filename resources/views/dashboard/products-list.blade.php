@extends('dashboard.layouts.app')

@php
use Illuminate\Support\Facades\Storage;
$routePrefix = request()->is('bar-keeper*') ? 'bar-keeper' : 'admin';
@endphp

@section('content')
<style>
    .product-card {
        border: none !important;
        background: #ffffff !important;
    }
    .product-card:hover {
        cursor: pointer;
    }
    .btn-white {
        background: #ffffff !important;
        border: none !important;
        background-color: #fff !important;
        transition: all 0.2s ease;
    }
    .btn-white:hover {
        background: #f8f9fa !important;
        color: #000 !important;
        transform: scale(1.05);
    }
    .category-section h4 {
        color: #333 !important;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 1.1rem;
    }
</style>
<div class="app-title">
  <div>
    <h1><i class="fa fa-cube"></i> Products</h1>
    <p>Manage restaurant products (drinks and food)</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route($routePrefix === 'admin' ? 'admin.dashboard' : 'bar-keeper.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Products</a></li>
  </ul>
</div>

<!-- Statistics Section -->
<div class="row mb-4">
  <div class="col-md-3">
    <div class="widget-small primary coloured-icon text-white" style="background: linear-gradient(135deg, #4b6cb7 0%, #182848 100%);">
      <i class="icon fa fa-tags fa-3x"></i>
      <div class="info">
        <h4 class="text-white">Product Brands</h4>
        <p class="text-white"><b>{{ $summaryStats['brands'] }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small info coloured-icon text-white" style="background: linear-gradient(135deg, #00c6ff 0%, #0072ff 100%);">
      <i class="icon fa fa-barcode fa-3x"></i>
      <div class="info">
        <h4 class="text-white">Total SKUs</h4>
        <p class="text-white"><b>{{ $summaryStats['variants'] }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small warning coloured-icon text-white" style="background: linear-gradient(135deg, #f2994a 0%, #f2c94c 100%);">
      <i class="icon fa fa-folder fa-3x"></i>
      <div class="info">
        <h4 class="text-white">Categories</h4>
        <p class="text-white"><b>{{ $summaryStats['categories'] }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small success coloured-icon text-white" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
      <i class="icon fa fa-check-circle fa-3x"></i>
      <div class="info">
        <h4 class="text-white">Active Items</h4>
        <p class="text-white"><b>{{ $summaryStats['active'] }}</b></p>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="tile shadow-sm">
      <div class="tile-title-w-btn mb-4">
        <h3 class="title">Product Inventory</h3>
        <div class="btn-group">
          <a class="btn btn-primary shadow-sm" href="{{ route($routePrefix . '.products.create') }}">
            <i class="fa fa-plus"></i> Register New Product
          </a>
        </div>
      </div>
      
      <!-- Filters -->
      <div class="row mb-4 bg-light p-3 rounded mx-0 border">
        <div class="col-md-3">
          <label class="small font-weight-bold text-muted">FILTER BY CATEGORY</label>
          <select class="form-control" id="categoryFilter" onchange="filterProducts()">
            <option value="">All Categories</option>
            @php
                $uniqueCategories = $products->unique('category')->pluck('category_name', 'category')->sort();
            @endphp
            @foreach($uniqueCategories as $key => $name)
                <option value="{{ $key }}">{{ $name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4">
          <label class="small font-weight-bold text-muted">SEARCH NAME / SUPPLIER / BRAND</label>
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text bg-white"><i class="fa fa-search"></i></span>
            </div>
            <input type="text" class="form-control" id="searchInput" placeholder="Type to search..." 
                   value="{{ request('search') }}" 
                   oninput="filterProducts()">
          </div>
        </div>
        <div class="col-md-5 text-right d-flex align-items-end justify-content-end">
          <button class="btn btn-secondary" onclick="resetFilters()">
            <i class="fa fa-refresh"></i> Reset View
          </button>
        </div>
      </div>
      
      @if($products->count() > 0)
      <div id="productsWrapper">
        @php 
          $type = request('type');
          
          // Merge specific categories into "Soft Drinks" as requested
          $groupedProducts = $products->groupBy(function($item) {
              if (in_array($item->category, ['non_alcoholic_beverage', 'energy_drinks', 'juices'])) {
                  return 'soft_drinks';
              }
              return $item->category;
          });
          
          $categoryOrder = ['soft_drinks', 'water', 'alcoholic_beverage', 'wines', 'spirits', 'hot_beverages', 'cocktails'];
          
          $sortedCategories = $groupedProducts->keys()->sortBy(function($key) use ($categoryOrder) {
              $pos = array_search($key, $categoryOrder);
              return $pos === false ? 999 : $pos;
          });
        @endphp

        @foreach($sortedCategories as $categoryKey)
          @php 
            $items = $groupedProducts[$categoryKey]; 
            $categoryIcons = [
                'soft_drinks' => 'fa-flask',
                'water' => 'fa-tint',
                'alcoholic_beverage' => 'fa-beer',
                'wines' => 'fa-vine',
                'spirits' => 'fa-glass',
                'hot_beverages' => 'fa-coffee',
                'cocktails' => 'fa-magic'
            ];
            $icon = $categoryIcons[$categoryKey] ?? 'fa-folder-open-o';
            $displayName = ($categoryKey === 'soft_drinks') ? 'Soft Drinks & Sodas' : (ucfirst(str_replace('_', ' ', $categoryKey)));
          @endphp
          
          <div class="category-section mb-5">
            <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
              <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px;">
                <i class="fa {{ $icon }} fa-lg"></i>
              </div>
              <h4 class="m-0 font-weight-bold" style="letter-spacing: 0.5px;">
                {{ $displayName }}
                <span class="badge badge-pill badge-light border ml-2 text-muted" style="font-size: 14px; font-weight: normal;">{{ $items->count() }} items</span>
              </h4>
            </div>
            
            <div class="row">
              @foreach($items as $product)
                @foreach($product->variants as $variant)
                  <div class="col-md-4 col-lg-3 mb-4 product-card-wrapper" 
                       data-product-name="{{ strtolower($variant->variant_name . ' ' . $product->name) }}"
                       data-product-supplier="{{ strtolower($product->supplier->name ?? '') }}"
                       data-product-brand="{{ strtolower($product->brand_or_type ?? '') }}"
                       data-product-category-name="{{ strtolower($product->category_name) }}"
                       data-category="{{ $product->category }}"
                       data-product-type="{{ strtolower($product->type) }}">
                    <div class="card h-100 product-card border-0 shadow-sm" style="transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1); border-radius: 15px; overflow: hidden; background: #fff;" 
                         onmouseover="this.style.transform='translateY(-8px)'; this.style.boxShadow='0 15px 30px rgba(0,0,0,0.1)';" 
                         onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.05)';" 
                         onclick="viewProduct({{ $product->id }})">
                      
                      <div class="card-header border-0 p-0 position-relative" style="height: 180px; background: #f0f2f5; cursor: pointer;">
                        @if($variant->image)
                          <img src="{{ Storage::url($variant->image) }}" alt="{{ $variant->variant_name }}" 
                               class="w-100 h-100" style="object-fit: cover;"
                               onerror="this.onerror=null; this.src='{{ asset('dashboard_assets/img/placeholder-product.png') }}';">
                        @else
                          <div class="d-flex align-items-center justify-content-center h-100" style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);">
                            <i class="fa {{ $icon }} fa-4x text-white opacity-50"></i>
                          </div>
                        @endif
                        
                        <!-- Category Bubble -->
                        <div class="position-absolute" style="top: 12px; left: 12px;">
                            <span class="badge badge-light shadow-sm px-2 py-1" style="font-size: 10px; text-transform: uppercase; font-weight: 700; color: #555; background: rgba(255,255,255,0.9);">
                               <i class="fa {{ $icon }} mr-1"></i> {{ substr($product->category_name, 0, 15) }}
                            </span>
                        </div>
                        
                        <!-- Measurement Badge -->
                         <div class="position-absolute" style="bottom: 12px; right: 12px;">
                            <span class="badge badge-primary shadow-sm px-2 py-1" style="font-size: 11px;">
                               {{ $variant->measurement }}
                            </span>
                        </div>
                      </div>

                      <div class="card-body p-3 d-flex flex-column">
                        <h5 class="card-title mb-1 font-weight-bold text-dark" style="font-size: 1.05rem; line-height: 1.2;">
                          {{ $variant->variant_name }}
                        </h5>
                        <p class="small text-muted mb-2 font-italic">{{ $product->name }}</p>
                        
                        <div class="small text-muted mb-3">
                           <i class="fa fa-building-o mr-1"></i> {{ $product->supplier->name ?? 'Direct Supply' }}
                        </div>
                        
                        <div class="mt-auto pt-2 d-flex justify-content-between align-items-center border-top">
                            <div class="small font-weight-bold">
                               @if($variant->can_sell_as_pic) <span class="badge badge-success">BOTTLE</span> @endif
                               @if($variant->can_sell_as_serving) <span class="badge badge-info">GLASS</span> @endif
                            </div>
                            <!-- Price or Status could go here -->
                        </div>
                      </div>

                      <div class="card-footer bg-white border-top-0 p-3 d-flex">
                        <div class="btn-group w-100 shadow-sm border rounded">
                            <button class="btn btn-sm btn-white text-primary rounded-left py-2" onclick="event.stopPropagation(); viewProduct({{ $product->id }})" title="View Details" style="border: none;">
                              <i class="fa fa-eye"></i>
                            </button>
                            <a href="{{ route($routePrefix . '.products.edit', $product) }}" class="btn btn-sm btn-white text-info py-2" onclick="event.stopPropagation();" title="Edit Family" style="border: none; border-left: 1px solid #eee; border-right: 1px solid #eee;">
                              <i class="fa fa-edit"></i>
                            </a>
                            <!-- Deleting single variant via main list is tricky, might delete main product if not careful. For now keeping link to main deletion or hiding -->
                             <button class="btn btn-sm btn-white text-danger rounded-right py-2" onclick="event.stopPropagation(); deleteVariant({{ $variant->id }})" title="Delete Variant" style="border: none;">
                              <i class="fa fa-trash"></i>
                            </button>
                        </div>
                      </div>
                    </div>
                  </div>
                @endforeach
              @endforeach
            </div>
          </div>
        @endforeach
      </div>
      
      <div class="d-flex justify-content-center mt-4">
        {{ $products->appends(request()->input())->links() }}
      </div>
      @else
      <div class="text-center py-5">
        <i class="fa fa-cube fa-5x text-muted mb-4 opacity-25"></i>
        <h3 class="mb-2">No Products Registered Yet</h3>
        <p class="text-muted mb-4">Start your bar inventory by registering your products and sizes.</p>
        <a href="{{ route($routePrefix . '.products.create') }}" class="btn btn-primary btn-lg px-5 shadow">
          <i class="fa fa-plus"></i> Register First Product
        </a>
      </div>
      @endif
      
      <div id="noResultsMessage" class="text-center py-5" style="display: none;">
        <i class="fa fa-search fa-5x text-muted mb-4 opacity-25"></i>
        <h3 class="mb-2">No Matches Found</h3>
        <p class="text-muted mb-4">Try adjusting your search terms or filters.</p>
        <button class="btn btn-secondary btn-lg px-4" onclick="resetFilters()">
          <i class="fa fa-refresh"></i> Show All Products
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Details Modal -->
<div class="modal fade" id="productDetailsModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header bg-light">
        <h5 class="modal-title font-weight-bold">
          <i class="fa fa-cube text-primary mr-2"></i> Product Details
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body p-4" id="productDetailsContent">
        <!-- Load via JS -->
      </div>
      <div class="modal-footer bg-light border-0">
        <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Close Window</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
// Filter logic
let searchTimeout;
function filterProducts() {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(function() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const categoryFilter = document.getElementById('categoryFilter').value;
    const cards = document.querySelectorAll('.product-card-wrapper');
    let count = 0;
    
    cards.forEach(card => {
      const name = card.dataset.productName || '';
      const supplier = card.dataset.productSupplier || '';
      const brand = card.dataset.productBrand || '';
      const categoryName = card.dataset.productCategoryName || '';
      const categoryCode = card.dataset.category || '';
      
      const match = (!searchTerm || name.includes(searchTerm) || supplier.includes(searchTerm) || brand.includes(searchTerm) || categoryName.includes(searchTerm)) &&
                    (!categoryFilter || categoryCode === categoryFilter);
      
      card.style.display = match ? 'block' : 'none';
      if (match) count++;
    });
    
    document.getElementById('noResultsMessage').style.display = (count === 0) ? 'block' : 'none';
    
    // Also toggle the section headers if all items in them are hidden
    document.querySelectorAll('.category-section').forEach(section => {
        const visibleItems = section.querySelectorAll('.product-card-wrapper[style*="display: block"]');
        if(section.querySelectorAll('.product-card-wrapper').length > 0) { // If section has items originally
             section.style.display = (visibleItems.length > 0) ? 'block' : 'none';
        }
    });
    
    document.getElementById('productsWrapper').style.display = (count === 0) ? 'none' : 'block';
  }, 250);
}

function resetFilters() {
  document.getElementById('searchInput').value = '';
  document.getElementById('categoryFilter').value = '';
  filterProducts();
}

function viewProduct(id) {
  const content = $('#productDetailsContent');
  $('#productDetailsModal').modal('show');
  content.html('<div class="text-center py-5"><i class="fa fa-spinner fa-spin fa-3x text-primary mb-3"></i><p>Loading information...</p></div>');
  
  fetch(`{{ route($routePrefix . ".products.show", ":id") }}`.replace(':id', id), {
    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
  })
  .then(res => res.json())
  .then(data => {
    if (!data.success) throw new Error('Failed');
    const p = data.product;
    const variants = p.variants || [];
    
    const mainImage = p.image ? '/storage/' + p.image : (variants.length > 0 && variants[0].image ? '/storage/' + variants[0].image : '{{ asset("dashboard_assets/img/placeholder-product.png") }}');
    
    let html = `
      <div class="row align-items-center">
        <div class="col-md-4 text-center mb-4 mb-md-0">
          <img src="${mainImage}" 
               class="img-fluid rounded shadow-sm border" style="max-height: 250px;">
        </div>
        <div class="col-md-8">
          <h3 class="font-weight-bold mb-3">${p.name}</h3>
          <ul class="list-group list-group-flush border rounded">
            <li class="list-group-item"><strong>Category:</strong> ${p.category_name || (p.category ? p.category.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) : 'N/A')}</li>
            <li class="list-group-item"><strong>Supplier:</strong> ${p.supplier ? p.supplier.name : 'Direct'}</li>
            <li class="list-group-item"><strong>Type:</strong> <span class="badge badge-light border px-2">${p.type.toUpperCase()}</span></li>
            <li class="list-group-item"><strong>Registration Date:</strong> ${new Date(p.created_at).toLocaleDateString()}</li>
          </ul>
        </div>
      </div>
      <div class="mt-5">
        <h5 class="font-weight-bold border-bottom pb-2 mb-3"><i class="fa fa-tags mr-2 text-primary"></i> Configured Sizes</h5>
        <div class="table-responsive">
          <table class="table table-bordered table-hover bg-white shadow-sm">
            <thead class="thead-light">
              <tr>
                <th style="width: 50px;">Img</th>
                <th>Variant Name & Size</th>
                <th>Selling Method</th>
                <th>Servings (Glass/Mixed)</th>
              </tr>
            </thead>
            <tbody>
              ${variants.map(v => `
                <tr>
                  <td>
                    ${v.image ? `<img src="/storage/${v.image}" class="rounded border" style="width: 40px; height: 40px; object-fit: cover;">` : '<div class="bg-light rounded d-flex align-items-center justify-content-center border" style="width: 40px; height: 40px;"><i class="fa fa-image text-muted"></i></div>'}
                  </td>
                  <td class="font-weight-bold align-middle">
                      ${v.variant_name || 'Standard'} <span class="text-muted small">(${v.measurement})</span>
                  </td>
                  <td class="align-middle">
                      ${v.can_sell_as_pic ? '<span class="badge badge-success mr-1">Bottle</span>' : ''}
                      ${v.can_sell_as_serving ? '<span class="badge badge-info">Glass/Tot</span>' : ''}
                  </td>
                  <td class="align-middle">${v.can_sell_as_serving ? (v.servings_per_pic || 1) + ' servings' : '-'}</td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        </div>
      </div>
    `;
    content.html(html);
  })
  .catch(err => {
    content.html('<div class="alert alert-danger">Error loading product details. Please try again.</div>');
  });
}

function deleteVariant(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You are about to delete this specific product variant.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete variant!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`{{ route($routePrefix . ".products.variants.destroy", ":id") }}`.replace(':id', id), {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Deleted!', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Failed!', data.message || 'Error occurred', 'error');
                }
            });
        }
    });
}

function deleteProduct(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this! This product will be removed from inventory lists.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`{{ route($routePrefix . ".products.destroy", ":id") }}`.replace(':id', id), {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Deleted!', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Failed!', data.message || 'Error occurred', 'error');
                }
            });
        }
    });
}
</script>
@endsection
