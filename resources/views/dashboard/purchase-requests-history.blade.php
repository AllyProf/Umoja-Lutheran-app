@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-history"></i> Purchase Requests & Received Items History</h1>
    <p>View your purchase requests and received items history</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route($routePrefix . '.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route($routePrefix . '.purchase-requests.my') }}">My Requests</a></li>
    <li class="breadcrumb-item"><a href="#">History</a></li>
  </ul>
</div>

<div class="row mb-3">
  <div class="col-md-12">
    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs" role="tablist" style="background: #f8f9fa; padding: 8px; border-radius: 8px 8px 0 0; margin-bottom: 0;">
      <li class="nav-item">
        <a class="nav-link active" id="received-tab" data-toggle="tab" href="#received" role="tab" aria-controls="received" aria-selected="true" style="color: #940000; font-weight: 600;">
          <i class="fa fa-check-circle"></i> Received Items History
          <span class="badge badge-success ml-2">{{ isset($receivedItems) ? $receivedItems->total() : 0 }}</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="requests-tab" data-toggle="tab" href="#requests" role="tab" aria-controls="requests" aria-selected="false" style="color: #6c757d;">
          <i class="fa fa-list"></i> Purchase Requests History
          <span class="badge badge-info ml-2">{{ isset($requests) ? $requests->total() : 0 }}</span>
        </a>
      </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" style="background: #fff; border: 1px solid #dee2e6; border-top: none; border-radius: 0 0 8px 8px; padding: 20px;">
      
      <!-- Received Items History Tab -->
      <div class="tab-pane fade show active" id="received" role="tabpanel" aria-labelledby="received-tab">
        @if(isset($receivedItems) && $receivedItems->count() > 0)
        <div class="alert alert-success mb-3" style="border-left: 4px solid #28a745;">
          <i class="fa fa-history"></i> 
          <strong>Received Items History:</strong> All items you have received and added to your inventory stock, ordered by received date.
        </div>
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>Item Name</th>
                <th>Category</th>
                <th>Quantity</th>
                <th>Unit</th>
                <th>Unit Price</th>
                <th>Total Cost</th>
                <th class="text-center">Selling Configuration</th>
                <th>From Shopping List</th>
                <th>Received Date</th>
              </tr>
            </thead>
            <tbody>
              @foreach($receivedItems as $item)
              <tr>
                <td><strong>{{ $item->product_name }}</strong></td>
                <td>{{ $item->category_name ?? ucfirst(str_replace('_', ' ', $item->category ?? '')) }}</td>
                <td><strong class="text-success">{{ number_format($item->purchased_quantity ?? $item->quantity, 0) }}</strong></td>
                <td>{{ $item->unit === 'bottles' ? 'PIC' : $item->unit }}</td>
                <td>{{ number_format($item->unit_price ?? 0, 2) }} TZS</td>
                <td><strong>{{ number_format($item->purchased_cost ?? 0, 2) }} TZS</strong></td>
                <td class="text-center">
                  @php 
                    $variant = $item->productVariant; 
                    $qty = $item->purchased_quantity ?? $item->quantity ?? 0;
                  @endphp
                  @if($variant && strpos(strtolower($item->transferred_to_department ?? ''), 'bar') !== false)
                    <div class="p-1 border rounded bg-light" style="font-size: 11px; min-width: 160px;">
                      @if($variant->can_sell_as_pic && $variant->can_sell_as_serving)
                        <div class="mb-1"><span class="badge badge-primary px-2">Mixed Selling</span></div>
                        <div class="d-flex justify-content-between text-info"><span>PIC Price:</span> <strong>{{ $variant->selling_price_per_pic > 0 ? number_format($variant->selling_price_per_pic, 0) : 'Not Set' }}</strong></div>
                        <div class="d-flex justify-content-between text-success"><span>{{ $variant->selling_unit_name }} Price:</span> <strong>{{ $variant->selling_price_per_serving > 0 ? number_format($variant->selling_price_per_serving, 0) : 'Not Set' }}</strong></div>
                        
                        <div class="mt-1 pt-1 border-top" style="font-size: 10px; border-top: 1px dashed #ccc !important; line-height: 1.4;">
                            <div class="d-flex justify-content-between"><span>Value (Bottles):</span> <strong>{{ number_format($qty * $variant->selling_price_per_pic, 0) }}</strong></div>
                            @if($variant->selling_price_per_serving > 0)
                            <div class="d-flex justify-content-between text-primary"><span>Value ({{ $variant->selling_unit === 'glass' ? 'glasses' : $variant->selling_unit . 's' }}):</span> <strong>{{ number_format($qty * $variant->servings_per_pic * $variant->selling_price_per_serving, 0) }}</strong></div>
                            <div class="text-muted d-flex justify-content-between border-top"><span>Total Yield:</span> <span>{{ $qty * $variant->servings_per_pic }} {{ $variant->selling_unit === 'glass' ? 'glasses' : $variant->selling_unit . 's' }}</span></div>
                            @endif
                        </div>
                      @elseif($variant->can_sell_as_pic)
                        <div class="mb-1"><span class="badge badge-info px-2">PIC Only</span></div>
                        <div class="d-flex justify-content-between"><span>Sale Price:</span> <strong>{{ $variant->selling_price_per_pic > 0 ? number_format($variant->selling_price_per_pic, 0) : 'Not Set' }}</strong></div>
                        <div class="mt-1 pt-1 border-top text-muted d-flex justify-content-between" style="font-size: 10px;">
                            <span>Est. Value:</span> <strong>{{ number_format($qty * $variant->selling_price_per_pic, 0) }}</strong>
                        </div>
                      @elseif($variant->can_sell_as_serving)
                        <div class="mb-1"><span class="badge badge-success px-2">Serving Only</span></div>
                        <div class="d-flex justify-content-between"><span>{{ $variant->selling_unit_name }} Price:</span> <strong>{{ $variant->selling_price_per_serving > 0 ? number_format($variant->selling_price_per_serving, 0) : 'Not Set' }}</strong></div>
                        <div class="mt-1 pt-1 border-top" style="font-size: 10px; border-top: 1px dashed #ccc !important;">
                            <div class="text-muted d-flex justify-content-between"><span>Total Units:</span> <strong>{{ $qty * $variant->servings_per_pic }} {{ $variant->selling_unit === 'glass' ? 'glasses' : $variant->selling_unit . 's' }}</strong></div>
                            @if($variant->selling_price_per_serving > 0)
                            <div class="text-dark d-flex justify-content-between"><span>Est. Value:</span> <strong class="text-primary">{{ number_format($qty * $variant->servings_per_pic * $variant->selling_price_per_serving, 0) }}</strong></div>
                            @endif
                        </div>
                      @else
                        <span class="text-muted italic">No Config</span>
                      @endif
                    </div>
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td>
                  @if($item->shoppingList)
                    <a href="{{ route('admin.restaurants.shopping-list.show', $item->shoppingList->id) }}" target="_blank">
                      {{ $item->shoppingList->name }}
                    </a>
                  @else
                    N/A
                  @endif
                </td>
                <td>
                  <strong class="text-success">{{ $item->received_by_department_at ? $item->received_by_department_at->format('d M Y H:i') : 'N/A' }}</strong>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        
        <div class="mt-3">
          @if($receivedItems->hasPages())
          <nav aria-label="Page navigation">
            <ul class="pagination pagination-sm justify-content-center">
              @php
                $receivedItems->appends(request()->except('received_page'));
              @endphp
              @if($receivedItems->onFirstPage())
                <li class="page-item disabled"><span class="page-link"><i class="fa fa-angle-left"></i> Prev</span></li>
              @else
                <li class="page-item"><a class="page-link" href="{{ $receivedItems->previousPageUrl() }}"><i class="fa fa-angle-left"></i> Prev</a></li>
              @endif
              @php
                $currentPage = $receivedItems->currentPage();
                $lastPage = $receivedItems->lastPage();
                $startPage = max(1, $currentPage - 2);
                $endPage = min($lastPage, $currentPage + 2);
              @endphp
              @if($startPage > 1)
                <li class="page-item"><a class="page-link" href="{{ $receivedItems->url(1) }}">1</a></li>
                @if($startPage > 2)<li class="page-item disabled"><span class="page-link">...</span></li>@endif
              @endif
              @for($page = $startPage; $page <= $endPage; $page++)
                <li class="page-item {{ $page == $currentPage ? 'active' : '' }}">
                  @if($page == $currentPage)
                    <span class="page-link">{{ $page }}</span>
                  @else
                    <a class="page-link" href="{{ $receivedItems->url($page) }}">{{ $page }}</a>
                  @endif
                </li>
              @endfor
              @if($endPage < $lastPage)
                @if($endPage < $lastPage - 1)<li class="page-item disabled"><span class="page-link">...</span></li>@endif
                <li class="page-item"><a class="page-link" href="{{ $receivedItems->url($lastPage) }}">{{ $lastPage }}</a></li>
              @endif
              @if($receivedItems->hasMorePages())
                <li class="page-item"><a class="page-link" href="{{ $receivedItems->nextPageUrl() }}">Next <i class="fa fa-angle-right"></i></a></li>
              @else
                <li class="page-item disabled"><span class="page-link">Next <i class="fa fa-angle-right"></i></span></li>
              @endif
            </ul>
          </nav>
          @endif
        </div>
        @else
        <div class="text-center" style="padding: 50px;">
          <i class="fa fa-check-circle fa-4x text-muted mb-3"></i>
          <h3>No Received Items Yet</h3>
          <p class="text-muted">Items you receive will appear here in your history.</p>
          <a href="{{ route($routePrefix . '.purchase-requests.my') }}" class="btn btn-primary">
            <i class="fa fa-inbox"></i> Go to My Requests
          </a>
        </div>
        @endif
      </div>

      <!-- Purchase Requests History Tab -->
      <div class="tab-pane fade" id="requests" role="tabpanel" aria-labelledby="requests-tab">
        @if(isset($requests) && $requests->count() > 0)
        <div class="alert alert-info mb-3" style="border-left: 4px solid #17a2b8;">
          <i class="fa fa-list"></i> 
          <strong>Purchase Requests History:</strong> All your purchase requests, ordered by purchase date (when items were purchased).
        </div>
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>Item Name</th>
                <th>Category</th>
                <th>Quantity</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Reason</th>
                <th>Submitted</th>
                <th>Purchase Date</th>
                <th>Last Edited</th>
                <th>Changes</th>
              </tr>
            </thead>
            <tbody>
              @foreach($requests as $request)
              <tr>
                <td><strong>{{ $request->item_name }}</strong></td>
                <td>{{ $request->category ? ucfirst(str_replace('_', ' ', $request->category)) : 'N/A' }}</td>
                <td>{{ number_format($request->quantity, 0) }} {{ $request->unit === 'bottles' ? 'PIC' : $request->unit }}</td>
                <td>
                  @if($request->priority === 'urgent')
                    <span class="badge badge-danger">Urgent</span>
                  @elseif($request->priority === 'high')
                    <span class="badge badge-warning">High</span>
                  @elseif($request->priority === 'medium')
                    <span class="badge badge-info">Medium</span>
                  @else
                    <span class="badge badge-secondary">Low</span>
                  @endif
                </td>
                <td>
                  @if($request->status === 'pending')
                    <span class="badge badge-warning">Pending</span>
                  @elseif($request->status === 'approved')
                    <span class="badge badge-success">Approved</span>
                  @elseif($request->status === 'on_list')
                    <span class="badge badge-primary">On List</span>
                  @elseif($request->status === 'rejected')
                    <span class="badge badge-danger">Rejected</span>
                    @if($request->rejection_reason)
                      <br><small class="text-muted">{{ Str::limit($request->rejection_reason, 30) }}</small>
                    @endif
                  @elseif($request->status === 'purchased')
                    <span class="badge badge-info">Purchased</span>
                  @elseif($request->status === 'completed')
                    <span class="badge badge-success">Completed</span>
                  @endif
                </td>
                <td>{{ Str::limit($request->reason, 40) }}</td>
                <td>{{ $request->created_at->format('M d, Y H:i') }}</td>
                <td>
                  @if($request->shoppingList)
                    <strong class="text-success">{{ $request->shoppingList->created_at->format('d M Y H:i') }}</strong>
                    <br><small class="text-muted">{{ $request->shoppingList->name }}</small>
                  @else
                    <span class="text-muted">Not purchased yet</span>
                  @endif
                </td>
                <td>
                  @if($request->last_edited_at)
                    <small class="text-muted">
                      <i class="fa fa-edit"></i> Edited by <strong>{{ $request->editor->name ?? 'Unknown' }}</strong><br>
                      <i class="fa fa-clock-o"></i> {{ $request->last_edited_at->format('M d, Y H:i') }}
                    </small>
                  @else
                    <small class="text-muted">Not edited</small>
                  @endif
                </td>
                <td>
                  @if($request->last_changes && count($request->last_changes) > 0)
                    <div class="changes-list" style="max-width: 250px; font-size: 11px;">
                      @foreach($request->last_changes as $change)
                        <div class="mb-1">
                          <strong>{{ $change['field'] }}:</strong><br>
                          <span class="text-danger" style="text-decoration: line-through;">{{ $change['old'] }}</span>
                          <i class="fa fa-arrow-right text-muted mx-1"></i>
                          <span class="text-success">{{ $change['new'] }}</span>
                        </div>
                      @endforeach
                    </div>
                  @else
                    <small class="text-muted">-</small>
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        
        <div class="mt-3">
          @if($requests->hasPages())
          <nav aria-label="Page navigation">
            <ul class="pagination pagination-sm justify-content-center">
              @php
                $requests->appends(request()->except('requests_page'));
              @endphp
              @if($requests->onFirstPage())
                <li class="page-item disabled"><span class="page-link"><i class="fa fa-angle-left"></i> Prev</span></li>
              @else
                <li class="page-item"><a class="page-link" href="{{ $requests->previousPageUrl() }}"><i class="fa fa-angle-left"></i> Prev</a></li>
              @endif
              @php
                $currentPage = $requests->currentPage();
                $lastPage = $requests->lastPage();
                $startPage = max(1, $currentPage - 2);
                $endPage = min($lastPage, $currentPage + 2);
              @endphp
              @if($startPage > 1)
                <li class="page-item"><a class="page-link" href="{{ $requests->url(1) }}">1</a></li>
                @if($startPage > 2)<li class="page-item disabled"><span class="page-link">...</span></li>@endif
              @endif
              @for($page = $startPage; $page <= $endPage; $page++)
                <li class="page-item {{ $page == $currentPage ? 'active' : '' }}">
                  @if($page == $currentPage)
                    <span class="page-link">{{ $page }}</span>
                  @else
                    <a class="page-link" href="{{ $requests->url($page) }}">{{ $page }}</a>
                  @endif
                </li>
              @endfor
              @if($endPage < $lastPage)
                @if($endPage < $lastPage - 1)<li class="page-item disabled"><span class="page-link">...</span></li>@endif
                <li class="page-item"><a class="page-link" href="{{ $requests->url($lastPage) }}">{{ $lastPage }}</a></li>
              @endif
              @if($requests->hasMorePages())
                <li class="page-item"><a class="page-link" href="{{ $requests->nextPageUrl() }}">Next <i class="fa fa-angle-right"></i></a></li>
              @else
                <li class="page-item disabled"><span class="page-link">Next <i class="fa fa-angle-right"></i></span></li>
              @endif
            </ul>
          </nav>
          @endif
        </div>
        @else
        <div class="text-center" style="padding: 50px;">
          <i class="fa fa-shopping-cart fa-4x text-muted mb-3"></i>
          <h3>No Purchase Requests</h3>
          <p class="text-muted">You haven't submitted any purchase requests yet.</p>
          <a href="{{ route($routePrefix . '.purchase-requests.create') }}" class="btn btn-primary">
            <i class="fa fa-plus"></i> Create Request
          </a>
        </div>
        @endif
      </div>
      <!-- End Tab Content -->
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Handle tab switching - update active states
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        // Update nav link styles
        $('.nav-link').css({
            'color': '#6c757d',
            'font-weight': '400',
            'background-color': 'transparent'
        });
        $(e.target).css({
            'color': '#940000',
            'font-weight': '600',
            'background-color': '#fff'
        });
    });
    
    // Set initial active tab styles
    $('.nav-link.active').css({
        'color': '#940000',
        'font-weight': '600',
        'background-color': '#fff'
    });
});
</script>
<style>
  /* Bootstrap pagination styling */
  .pagination-sm .page-link {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    line-height: 1.5;
    color: #940000;
    border-color: #dee2e6;
  }
  
  .pagination-sm .page-link:hover {
    color: #fff;
    background-color: #940000;
    border-color: #940000;
  }
  
  .pagination-sm .page-item.active .page-link {
    z-index: 2;
    color: #fff;
    background-color: #940000;
    border-color: #940000;
  }
  
  .pagination-sm .page-item.disabled .page-link {
    color: #6c757d;
    pointer-events: none;
    background-color: #fff;
    border-color: #dee2e6;
    cursor: not-allowed;
  }
  
  .pagination-sm .page-link i {
    font-size: 0.75rem;
  }
</style>
@endsection
