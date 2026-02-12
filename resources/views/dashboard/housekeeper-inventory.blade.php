@extends('dashboard.layouts.app')

@php
  $isManager = Auth::guard('staff')->check() && in_array(strtolower(Auth::guard('staff')->user()->role ?? ''), ['manager', 'super_admin']);
  $isReadOnly = $isManager && request()->routeIs('admin.housekeeping-inventory');
@endphp

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-cubes"></i> {{ $isReadOnly ? 'Housekeeping Inventory' : 'Inventory Management' }}</h1>
    <p>{{ $isReadOnly ? 'View housekeeping inventory stock levels' : 'Manage housekeeping inventory items' }}</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ $isReadOnly ? route('admin.dashboard') : route('housekeeper.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Inventory</a></li>
  </ul>
</div>

<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-cubes"></i> Inventory Items</h3>
      <div class="tile-body">
        <!-- Search Input -->
        <div class="row mb-4 align-items-center">
          <div class="col-md-5">
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa fa-search"></i></span>
              </div>
              <input type="text" id="inventorySearch" class="form-control" placeholder="Search by item name, category, or unit...">
            </div>
          </div>
          <div class="col-md-7 d-flex justify-content-between align-items-center">
            <div id="searchResults" class="text-muted">
              <span id="resultCount">{{ $items->count() }}</span> item(s) found
            </div>
            <div class="bulk-actions d-flex align-items-center">
              <div class="animated-checkbox mr-3">
                <label class="mb-0">
                  <input type="checkbox" id="selectAllInventory"><span class="label-text font-weight-bold text-primary">Select All</span>
                </label>
              </div>
              <button class="btn btn-warning btn-sm" id="bulkRestockBtn" disabled>
                <i class="fa fa-shopping-cart"></i> Restock Selected (<span id="selectedCount">0</span>)
              </button>
            </div>
          </div>
        </div>
        
        <div class="row" id="inventoryCards">
@foreach($items as $item)
@php
    $stockStatus = $item->getStockStatus();
    $statusColor = $item->getStockStatusColor();
    $borderClass = $stockStatus === 'critical' ? 'border-danger' : ($stockStatus === 'low' ? 'border-warning' : 'border-success');
    $headerClass = $stockStatus === 'critical' ? 'bg-danger text-white' : ($stockStatus === 'low' ? 'bg-warning text-dark' : 'bg-success text-white');
    
    // Map categories to icons
    $categoryIcons = [
        'cleaning' => 'fa-broom',
        'toiletries' => 'fa-soap',
        'linen' => 'fa-bed',
        'bedding' => 'fa-bed',
        'towel' => 'fa-bath',
        'water' => 'fa-tint',
        'drinks' => 'fa-coffee',
        'pantry' => 'fa-shopping-basket',
    ];
    $catKey = strtolower($item->category);
    $icon = 'fa-cube';
    foreach ($categoryIcons as $key => $val) {
        if (strpos($catKey, $key) !== false) {
            $icon = $val;
            break;
        }
    }
@endphp
<div class="col-md-4 col-sm-6 mb-4 inventory-card" 
     data-name="{{ strtolower($item->name) }}" 
     data-category="{{ strtolower(str_replace('_', ' ', $item->category)) }}"
     data-unit="{{ strtolower($item->unit) }}"
     data-item-id="{{ $item->id }}"
     data-current-stock="{{ $item->current_stock }}"
     data-minimum-stock="{{ $item->minimum_stock }}">
  
  <div class="card h-100 {{ $borderClass }} shadow-sm" id="card-{{ $item->id }}" style="border-width: 2px !important; transition: transform 0.2s; border-radius: 8px; overflow: hidden;">
    <div class="card-header {{ $headerClass }} py-2">
      <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
          <div class="animated-checkbox mr-2">
            <label class="mb-0">
              <input type="checkbox" class="inventory-checkbox" value="{{ $item->id }}">
              <span class="label-text"></span>
            </label>
          </div>
          <h6 class="card-title mb-0 text-truncate font-weight-bold" title="{{ $item->name }}" style="max-width: 150px;">
            <i class="fa {{ $icon }}"></i> {{ $item->name }}
          </h6>
        </div>
        <div class="btn-group">
          <button class="btn btn-sm btn-info view-track-btn" 
                  data-item-id="{{ $item->id }}" 
                  data-item-name="{{ $item->name }}"
                  title="Track History"
                  style="background: #17a2b8; border-color: #17a2b8; color: white;">
            <i class="fa fa-history"></i>
          </button>
          <a href="{{ route('housekeeper.purchase-requests.create', ['housekeeping_ids' => $item->id]) }}" 
             class="btn btn-sm btn-light" 
             title="Request Restock">
            <i class="fa fa-shopping-cart text-warning"></i>
          </a>
          @if(!$isReadOnly)
          <button class="btn btn-sm btn-light settings-stock-btn" 
                  data-item-id="{{ $item->id }}" 
                  data-item-name="{{ $item->name }}" 
                  data-minimum-stock="{{ $item->minimum_stock }}"
                  title="Settings">
            <i class="fa fa-cog"></i>
          </button>
          @endif
        </div>
      </div>
    </div>
    <div class="card-body p-3">
      <div class="row">
        <div class="col-4 text-center border-right">
          <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 55px; height: 55px; border: 2px solid #eee;">
            <i class="fa {{ $icon }} fa-2x text-{{ $statusColor }}"></i>
          </div>
          <small class="d-block text-muted text-truncate" title="{{ $item->category }}">{{ ucfirst($item->category) }}</small>
        </div>
        <div class="col-8">
          <div class="mb-1">
            <small class="text-muted d-block" style="font-size: 10px;">TOTAL RECEIVED</small>
            <h5 class="mb-0 text-info" style="font-weight: bold;">
              {{ number_format($item->total_received ?? 0, 1) }} <small>{{ $item->unit }}</small>
            </h5>
          </div>
          <div class="mb-1">
            <small class="text-muted d-block" style="font-size: 10px;">CURRENT STOCK</small>
            <h4 class="mb-0 text-{{ $statusColor }}" id="current-stock-text-{{ $item->id }}" style="font-weight: 800;">
              {{ number_format($item->current_stock, 1) }} <small>{{ $item->unit }}</small>
            </h4>
          </div>
          <div id="status-badge-{{ $item->id }}">
            @if($stockStatus === 'critical')
              <span class="badge badge-danger badge-pill" style="font-size: 10px;"><i class="fa fa-exclamation-circle"></i> Out of Stock</span>
            @elseif($stockStatus === 'low')
              <span class="badge badge-warning badge-pill" style="font-size: 10px;"><i class="fa fa-exclamation-triangle"></i> Low Stock</span>
            @else
              <span class="badge badge-success badge-pill" style="font-size: 10px;"><i class="fa fa-check-circle"></i> In Stock</span>
            @endif
          </div>
        </div>
      </div>
    </div>
    @if(!$isReadOnly)
    <div class="p-2 border-top bg-light">
      <button class="btn btn-block btn-{{ $statusColor }} btn-sm update-stock-btn" 
              data-item-id="{{ $item->id }}" 
              data-item-name="{{ $item->name }}" 
              data-current-stock="{{ $item->current_stock }}">
        <i class="fa fa-refresh"></i> Update Stock
      </button>
    </div>
    @endif
  </div>
</div>
@endforeach
          
          @if($items->count() === 0)
          <div class="col-md-12">
            <div class="alert alert-info text-center">
              <i class="fa fa-info-circle"></i> No inventory items found.
            </div>
          </div>
          @endif
          
          <!-- No Results Message (hidden by default) -->
          <div class="col-md-12" id="noResultsMessage" style="display: none;">
            <div class="alert alert-warning text-center">
              <i class="fa fa-search"></i> No items found matching your search criteria.
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Pending to Receive Items -->
@if(isset($pendingToReceive) && $pendingToReceive->count() > 0)
<div class="row mb-4">
  <div class="col-md-12">
    <div class="tile shadow-sm" style="border-left: 5px solid #009688;">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="tile-title mb-0" style="color: #009688;"><i class="fa fa-truck"></i> Items Ready to Receive</h3>
        <button class="btn btn-primary btn-sm" id="receiveAllBtn">
          <i class="fa fa-check-square"></i> Receive All Selected
        </button>
      </div>
      <div class="tile-body">
        <div class="table-responsive">
          <table class="table table-hover table-bordered" id="pendingReceiveTable">
            <thead class="bg-light">
              <tr>
                <th style="width: 40px;">
                  <div class="animated-checkbox">
                    <label>
                      <input type="checkbox" id="selectAllItems"><span class="label-text"></span>
                    </label>
                  </div>
                </th>
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Purchased From</th>
                <th>Purchase Date</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($pendingToReceive as $item)
              <tr>
                <td>
                  <div class="animated-checkbox">
                    <label>
                      <input type="checkbox" class="item-checkbox" value="{{ $item->id }}"><span class="label-text"></span>
                    </label>
                  </div>
                </td>
                <td>
                  <strong>{{ $item->product_name }}</strong><br>
                  <small class="text-muted">{{ $item->category_name ?? $item->category }}</small>
                </td>
                <td><strong class="text-info">{{ number_format($item->purchased_quantity ?? $item->quantity, 0) }} {{ $item->unit }}</strong></td>
                <td><span class="badge badge-light border">{{ $item->shoppingList->name ?? 'N/A' }}</span></td>
                <td>{{ $item->updated_at->format('M d, Y') }}</td>
                <td>
                  <button class="btn btn-sm btn-success receive-single-btn" data-id="{{ $item->id }}">
                    <i class="fa fa-download"></i> Receive
                  </button>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endif

@if(isset($recentlyReceived) && $recentlyReceived->count() > 0)
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-check-circle"></i> Recently Received Items (Last 30 Days)</h3>
      <div class="tile-body">
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>Item Name</th>
                <th>Category</th>
                <th>Quantity Received</th>
                <th>Unit</th>
                <th>Received Date</th>
                <th>From Shopping List</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @foreach($recentlyReceived as $received)
              <tr>
                <td><strong>{{ $received->product_name }}</strong></td>
                <td>{{ ucfirst(str_replace('_', ' ', $received->category ?? 'other')) }}</td>
                <td><strong class="text-success">{{ number_format($received->purchased_quantity ?? $received->quantity, 2) }}</strong></td>
                <td>{{ $received->unit }}</td>
                <td>{{ $received->received_by_department_at ? $received->received_by_department_at->format('d M Y H:i') : 'N/A' }}</td>
                <td>
                  @if($received->shoppingList)
                    <a href="{{ route('admin.restaurants.shopping-list.receiving-report', $received->shoppingList->id) }}" target="_blank">
                      {{ $received->shoppingList->name }}
                    </a>
                  @else
                    N/A
                  @endif
                </td>
                <td>
                  <span class="badge badge-success">
                    <i class="fa fa-check"></i> Received
                  </span>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endif

@if(!$isReadOnly)
<!-- Update Stock Modal -->
<div class="modal fade" id="updateStockModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-edit"></i> Update Stock</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="updateStockForm">
        <div class="modal-body">
          <input type="hidden" id="item_id" name="item_id">
          <p>Updating stock for: <strong id="item_name_display"></strong></p>
          <p>Current Stock: <strong id="current_stock_display"></strong></p>
          
          <input type="hidden" id="movement_type" name="movement_type" value="consumption">
          
          <div class="form-group">
            <label for="quantity">Quantity Used <span class="text-danger">*</span></label>
            <input type="number" step="0.01" class="form-control" id="quantity" name="quantity" required min="0" placeholder="Quantity used total or per room">
          </div>
          
          <div class="form-group" id="room_select_group">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="room_id" class="mb-0">Selected Rooms (Optional)</label>
                <button type="button" class="btn btn-sm btn-outline-info" id="selectOccupiedRooms" style="padding: 2px 8px; font-size: 11px;">
                    <i class="fa fa-users"></i> Select All Checked-In
                </button>
            </div>
            <select class="form-control select2" id="room_id" name="room_id[]" multiple="multiple" style="width: 100%;">
              @php
                $checkedInRoomIds = \App\Models\Booking::where('check_in_status', 'checked_in')
                    ->pluck('room_id')
                    ->toArray();
                $checkedInRooms = \App\Models\Room::whereIn('id', $checkedInRoomIds)
                    ->orderBy('room_number')
                    ->get();
              @endphp
              @foreach($checkedInRooms as $room)
              <option value="{{ $room->id }}" data-status="occupied">
                Room {{ $room->room_number }} ({{ $room->room_type }})
              </option>
              @endforeach
            </select>
            <small class="form-text text-muted">Only rooms with active check-ins are shown. Leave blank for general cleaning use.</small>
          </div>
          
          <div class="form-group">
            <label for="notes">Notes (Optional)</label>
            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Add notes about this stock movement..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="fa fa-save"></i> Update Stock
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Minimum Stock Settings Modal -->
<div class="modal fade" id="minimumStockModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-cog"></i> Stock Level Settings</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="minimumStockForm">
        <div class="modal-body">
          <input type="hidden" id="settings_item_id" name="item_id">
          <p>Setting minimum stock level for: <strong id="settings_item_name_display"></strong></p>
          
          <div class="form-group">
            <label for="minimum_stock">Minimum Stock Level <span class="text-danger">*</span></label>
            <input type="number" step="0.01" class="form-control" id="minimum_stock" name="minimum_stock" required min="0">
            <small class="form-text text-muted">When stock falls below this level, you will receive email notifications.</small>
          </div>
          
          <div class="alert alert-info">
            <i class="fa fa-info-circle"></i> <strong>Note:</strong> Email notifications will be sent to managers and housekeeping staff when stock goes below this level.
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="fa fa-save"></i> Save Settings
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- Usage Track Modal -->
<div class="modal fade" id="usageTrackModal" tabindex="-1" role="dialog" aria-labelledby="usageTrackModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="usageTrackModalLabel">
                    <i class="fa fa-history"></i> Stock Movement Tracking: <span id="track_item_name"></span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="bg-light">
                            <tr>
                                <th>Date</th>
                                <th>Action Type</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Staff</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody id="usageTrackContent">
                            <!-- Content loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
                <div id="noUsageData" class="text-center py-4 d-none">
                    <i class="fa fa-info-circle fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No stock movement history found for this item.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<style>
  .select2-container--default .select2-selection--multiple {
    border: 1px solid #ced4da;
    border-radius: 4px;
  }
  .select2-container .select2-selection--multiple {
    min-height: 38px;
  }
  .card {
    transition: transform 0.2s, box-shadow 0.2s;
  }
  
  .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15) !important;
  }
  
  .badge-lg {
    font-size: 0.9rem;
    padding: 0.5rem 0.75rem;
  }
  
  .card-header h5 {
    font-size: 1.1rem;
  }
  
  .card-body h4 {
    font-size: 1.5rem;
  }
  
  .inventory-card {
    transition: opacity 0.3s ease;
  }
  
  .inventory-card[style*="display: none"] {
    opacity: 0;
  }
  
  #inventorySearch {
    border-radius: 0.25rem;
  }
  
  #searchResults {
    padding-top: 0.5rem;
    font-size: 0.9rem;
  }
</style>
@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        placeholder: "Select Rooms",
        allowClear: true,
        dropdownParent: $('#updateStockModal')
    });

    // Select All Occupied Rooms button
    $('#selectOccupiedRooms').on('click', function() {
        var occupiedRoomIds = [];
        $('#room_id option').each(function() {
            if ($(this).data('status') === 'occupied') {
                occupiedRoomIds.push($(this).val());
            }
        });
        
        if (occupiedRoomIds.length > 0) {
            $('#room_id').val(occupiedRoomIds).trigger('change');
            swal({
                title: "Rooms Selected!",
                text: "Selected " + occupiedRoomIds.length + " checked-in rooms.",
                type: "info",
                timer: 1500,
                showConfirmButton: false
            });
        } else {
            swal("Notification", "No rooms currently marked as checked-in.", "info");
        }
    });

    // Real-time search functionality
    $('#inventorySearch').on('input', function() {
        var searchTerm = $(this).val().toLowerCase().trim();
        var visibleCount = 0;
        
        if (searchTerm === '') {
            // Show all cards if search is empty
            $('.inventory-card').show();
            visibleCount = $('.inventory-card').length;
            $('#noResultsMessage').hide();
        } else {
            // Filter cards based on search term
            $('.inventory-card').each(function() {
                var $card = $(this);
                var name = $card.data('name') || '';
                var category = $card.data('category') || '';
                var unit = $card.data('unit') || '';
                
                // Check if search term matches any field
                if (name.includes(searchTerm) || 
                    category.includes(searchTerm) || 
                    unit.includes(searchTerm)) {
                    $card.show();
                    visibleCount++;
                } else {
                    $card.hide();
                }
            });
            
            // Show/hide no results message
            if (visibleCount === 0) {
                $('#noResultsMessage').show();
            } else {
                $('#noResultsMessage').hide();
            }
        }
        
        // Update result count
        $('#resultCount').text(visibleCount);
        
        // Update "Select All" checkbox state based on current search results
        var allChecked = $('.inventory-card:visible .inventory-checkbox:checked').length;
        $('#selectAllInventory').prop('checked', visibleCount > 0 && visibleCount === allChecked);
    });
    
    // Settings button click handler
    $(document).on('click', '.settings-stock-btn', function() {
        var itemId = $(this).data('item-id');
        var itemName = $(this).data('item-name');
        var minimumStock = $(this).data('minimum-stock');
        
        $('#settings_item_id').val(itemId);
        $('#settings_item_name_display').text(itemName);
        $('#minimum_stock').val(minimumStock);
        $('#minimumStockModal').modal('show');
    });
    
    // Function to update card colors based on stock status
    function updateCardColors(itemId, currentStock, minimumStock) {
        var $card = $('#card-' + itemId);
        var $header = $('#card-header-' + itemId);
        var $currentStockText = $('#current-stock-text-' + itemId);
        var $statusBadge = $('#status-badge-' + itemId);
        var $cardElement = $('.inventory-card[data-item-id="' + itemId + '"]');
        
        // Determine stock status
        var stockStatus;
        var statusColor;
        var borderClass;
        var headerClass;
        
        if (currentStock <= 0) {
            stockStatus = 'critical';
            statusColor = 'danger';
            borderClass = 'border-danger';
            headerClass = 'bg-danger text-white';
        } else if (currentStock <= minimumStock) {
            stockStatus = 'low';
            statusColor = 'warning';
            borderClass = 'border-warning';
            headerClass = 'bg-warning text-dark';
        } else {
            stockStatus = 'normal';
            statusColor = 'success';
            borderClass = 'border-success';
            headerClass = 'bg-success text-white';
        }
        
        // Update card border
        $card.removeClass('border-danger border-warning border-success').addClass(borderClass);
        
        // Update header
        $header.removeClass('bg-danger bg-warning bg-success text-white text-dark').addClass(headerClass);
        
        // Update current stock text color
        $currentStockText.removeClass('text-danger text-warning text-success').addClass('text-' + statusColor);
        
        // Update status badge
        var badgeHtml = '';
        if (stockStatus === 'critical') {
            badgeHtml = '<span class="badge badge-danger badge-lg"><i class="fa fa-exclamation-circle"></i> Critical</span>';
        } else if (stockStatus === 'low') {
            badgeHtml = '<span class="badge badge-warning badge-lg"><i class="fa fa-exclamation-triangle"></i> Low Stock</span>';
        } else {
            badgeHtml = '<span class="badge badge-success badge-lg"><i class="fa fa-check-circle"></i> In Stock</span>';
        }
        $statusBadge.html(badgeHtml);
        
        // Update data attributes
        $cardElement.attr('data-current-stock', currentStock);
        $cardElement.attr('data-minimum-stock', minimumStock);
    }
    
    // Minimum stock form submission
    $('#minimumStockForm').on('submit', function(e) {
        e.preventDefault();
        
        var itemId = $('#settings_item_id').val();
        var newMinimumStock = parseFloat($('#minimum_stock').val());
        var formData = {
            _token: '{{ csrf_token() }}',
            minimum_stock: newMinimumStock
        };
        
        $.ajax({
            url: '/housekeeper/inventory/' + itemId + '/update-minimum-stock',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    // Get current stock from the card
                    var $cardElement = $('.inventory-card[data-item-id="' + itemId + '"]');
                    var currentStock = parseFloat($cardElement.attr('data-current-stock')) || 0;
                    
                    // Update minimum stock display
                    $('#minimum-stock-display-' + itemId).text(newMinimumStock.toFixed(2) + ' ' + $cardElement.attr('data-unit'));
                    
                    // Update card colors immediately
                    updateCardColors(itemId, currentStock, newMinimumStock);
                    
                    // Update settings button data attribute
                    $('.settings-stock-btn[data-item-id="' + itemId + '"]').attr('data-minimum-stock', newMinimumStock);
                    
                    swal({
                        title: "Success!",
                        text: response.message,
                        type: "success",
                        timer: 2000,
                        showConfirmButton: false
                    });
                    $('#minimumStockModal').modal('hide');
                }
            },
            error: function(xhr) {
                var errorMsg = xhr.responseJSON?.message || 'Failed to update minimum stock.';
                swal("Error!", errorMsg, "error");
            }
        });
    });
    
    $('.update-stock-btn').on('click', function() {
        var itemId = $(this).data('item-id');
        var itemName = $(this).data('item-name');
        var currentStock = $(this).data('current-stock');
        
        $('#item_id').val(itemId);
        $('#item_name_display').text(itemName);
        $('#current_stock_display').text(currentStock);
        $('#quantity').val('');
        $('#notes').val('');
        $('#movement_type').val('consumption'); // Always default to consumption (deduction)
        $('#room_id').val(null).trigger('change');
        $('#updateStockModal').modal('show');
    });
    
    $('#updateStockForm').on('submit', function(e) {
        e.preventDefault();
        
        var itemId = $('#item_id').val();
        var formData = {
            _token: '{{ csrf_token() }}',
            movement_type: $('#movement_type').val(),
            quantity: $('#quantity').val(),
            notes: $('#notes').val()
        };
        
        if ($('#room_id').val() && $('#room_id').val().length > 0) {
            formData.room_id = $('#room_id').val();
        }
        
        $.ajax({
            url: '/housekeeper/inventory/' + itemId + '/update-stock',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    swal({
                        title: "Success!",
                        text: response.message,
                        type: "success",
                        timer: 2000,
                        showConfirmButton: false
                    });
                    $('#updateStockModal').modal('hide');
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                }
            },
            error: function(xhr) {
                var errorMsg = xhr.responseJSON?.message || 'Failed to update stock.';
                swal("Error!", errorMsg, "error");
            }
        });
    });
    // Handle Receive All
    $('#selectAllItems').on('change', function() {
        $('.item-checkbox').prop('checked', $(this).is(':checked'));
    });

    $('#receiveAllBtn').on('click', function() {
        var selectedIds = [];
        $('.item-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) {
            swal("No items selected", "Please select at least one item to receive.", "warning");
            return;
        }

        receiveItems(selectedIds);
    });

    $('.receive-single-btn').on('click', function() {
        var id = $(this).data('id');
        receiveItems([id]);
    });

    function receiveItems(ids) {
        swal({
            title: "Receive Items?",
            text: "This will add " + ids.length + " item(s) to your inventory stock.",
            type: "info",
            showCancelButton: true,
            confirmButtonText: "Yes, receive",
            cancelButtonText: "Cancel",
            showLoaderOnConfirm: true,
            closeOnConfirm: false
        }, function(isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: '{{ route("housekeeper.purchase-requests.receive") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        item_ids: ids
                    },
                    success: function(response) {
                        if (response.success) {
                            swal({
                                title: "Received!",
                                text: response.message,
                                type: "success"
                            }, function() {
                                location.reload();
                            });
                        } else {
                            swal("Error", response.message, "error");
                        }
                    },
                    error: function(xhr) {
                        var msg = xhr.responseJSON ? xhr.responseJSON.message : "Error receiving items";
                        swal("Error", msg, "error");
                    }
                });
            }
        });
    }
    // Bulk Restock Logic
    $('#selectAllInventory').on('change', function() {
        // Target the parent card's visibility because the checkbox itself might be hidden by the theme's CSS
        $('.inventory-card:visible .inventory-checkbox').prop('checked', $(this).is(':checked'));
        updateSelectedCount();
    });

    $(document).on('change', '.inventory-checkbox', function() {
        updateSelectedCount();
        
        // Update "Select All" checkbox state
        var allVisible = $('.inventory-card:visible .inventory-checkbox').length;
        var allChecked = $('.inventory-card:visible .inventory-checkbox:checked').length;
        $('#selectAllInventory').prop('checked', allVisible > 0 && allVisible === allChecked);
    });

    function updateSelectedCount() {
        var count = $('.inventory-checkbox:checked').length;
        $('#selectedCount').text(count);
        $('#bulkRestockBtn').prop('disabled', count === 0);
    }

    $('#bulkRestockBtn').on('click', function() {
        var selectedIds = [];
        $('.inventory-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length > 0) {
            var url = '{{ route("housekeeper.purchase-requests.create") }}?housekeeping_ids=' + selectedIds.join(',');
            window.location.href = url;
        }
    });
    // Usage Tracking Logic
    $(document).on('click', '.view-track-btn', function() {
        var itemId = $(this).data('item-id');
        var itemName = $(this).data('item-name');
        
        $('#track_item_name').text(itemName);
        $('#usageTrackContent').html('<tr><td colspan="5" class="text-center font-italic">Loading tracking data...</td></tr>');
        $('#noUsageData').addClass('d-none');
        $('#usageTrackModal').modal('show');
        
        $.ajax({
            url: '/housekeeper/inventory/' + itemId + '/usage-track',
            method: 'GET',
            success: function(response) {
                if(response.success && response.movements.length > 0) {
                    var html = '';
                    response.movements.forEach(function(m) {
                        var colorClass = m.is_addition ? 'success' : 'danger';
                        var badgeClass = m.is_addition ? 'success' : 'info';
                        
                        html += '<tr>' +
                            '<td>' + m.date + '</td>' +
                            '<td><span class="badge badge-pill badge-' + badgeClass + '">' + m.type + '</span></td>' +
                            '<td class="text-' + colorClass + '"><strong>' + m.quantity + '</strong> <small>' + m.unit + '</small></td>' +
                            '<td><strong>' + m.balance + '</strong> <small>' + m.unit + '</small></td>' +
                            '<td>' + m.user + '</td>' +
                            '<td class="small">' + (m.notes || '-') + '</td>' +
                            '</tr>';
                    });
                    $('#usageTrackContent').html(html);
                } else {
                    $('#usageTrackContent').empty();
                    $('#noUsageData').removeClass('d-none');
                }
            },
            error: function(xhr) {
                swal("Error!", "Failed to load tracking data", "error");
            }
        });
    });
});
</script>
@endsection
