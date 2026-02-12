@extends('dashboard.layouts.app')

@php
  $isManager = Auth::guard('staff')->check() && in_array(strtolower(Auth::guard('staff')->user()->role ?? ''), ['manager', 'super_admin']);
  $isChef = Auth::guard('staff')->check() && strtolower(Auth::guard('staff')->user()->role ?? '') === 'head_chef';
  $isReadOnly = $isManager && !$isChef;
@endphp

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-cubes"></i> Kitchen Inventory</h1>
    <p>Manage kitchen ingredients and supplies</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ $isChef ? route('chef-master.dashboard') : route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Inventory</a></li>
  </ul>
</div>

<!-- Stats Row -->
<div class="row mb-4">
  <div class="col-md-4">
    <div class="widget-small primary coloured-icon"><i class="icon fa fa-list fa-3x"></i>
      <div class="info">
        <h4>Total Items</h4>
        <p><b>{{ $stats['total_items'] }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="widget-small warning coloured-icon"><i class="icon fa fa-exclamation-triangle fa-3x"></i>
      <div class="info">
        <h4>Low Stock</h4>
        <p><b>{{ $stats['low_stock'] }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="widget-small danger coloured-icon"><i class="icon fa fa-times-circle fa-3x"></i>
      <div class="info">
        <h4>Out of Stock</h4>
        <p><b>{{ $stats['out_of_stock'] }}</b></p>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="tile-title mb-0"><i class="fa fa-cubes"></i> Inventory Balance</h3>
      </div>



      <div class="tile-body">
        <!-- Search & Filter Area -->
        <div class="row mb-4">
          <div class="col-md-6">
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa fa-search"></i></span>
              </div>
              <input type="text" id="inventorySearch" class="form-control" placeholder="Search ingredients, categories, or units...">
            </div>
          </div>
          <div class="col-md-3">
            <select id="categoryFilter" class="form-control">
              <option value="">All Categories</option>
              @foreach($categories as $cat)
                <option value="{{ $cat }}">{{ ucfirst(str_replace('_', ' ', $cat)) }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3 text-right">
            <div id="searchResults" class="text-muted pt-2 text-capitalize">
              <span id="resultCount">{{ $inventoryItems->total() }}</span> items found
            </div>
          </div>
        </div>
        
        <div class="row" id="inventoryCards">
          @foreach($inventoryItems as $item)
          <div class="col-md-4 col-sm-6 mb-4 inventory-card" 
               data-name="{{ strtolower($item->name) }}" 
               data-category="{{ strtolower($item->category) }}"
               data-item-id="{{ $item->id }}"
               data-current-stock="{{ $item->current_stock }}"
               data-minimum-stock="{{ $item->minimum_stock }}">
            
            @php
              $stockStatus = $item->current_stock <= 0 ? 'critical' : ($item->current_stock <= $item->minimum_stock ? 'low' : 'normal');
              $statusColor = $stockStatus === 'critical' ? 'danger' : ($stockStatus === 'low' ? 'warning' : 'success');
              $borderClass = $stockStatus === 'critical' ? 'border-danger' : ($stockStatus === 'low' ? 'border-warning' : 'border-success');
              $headerClass = $stockStatus === 'critical' ? 'bg-danger text-white' : ($stockStatus === 'low' ? 'bg-warning text-dark' : 'bg-success text-white');
            @endphp

            <div class="card h-100 {{ $borderClass }}" id="card-{{ $item->id }}" style="box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
              <div class="card-header {{ $headerClass }}" id="card-header-{{ $item->id }}">
                <div class="d-flex justify-content-between align-items-center">
                  <div class="d-flex align-items-center">
                    <div class="custom-control custom-checkbox mr-2">
                        <input type="checkbox" class="custom-control-input stock-checkbox" id="check-{{ $item->id }}" value="{{ $item->id }}" data-category="{{ $item->category }}" data-name="{{ $item->name }}">
                        <label class="custom-control-label" for="check-{{ $item->id }}"></label>
                    </div>
                    <h5 class="card-title mb-0">
                      <i class="fa fa-cube"></i> {{ $item->name }}
                    </h5>
                  </div>
                  <div class="btn-group">
                    <a href="{{ route('chef-master.purchase-requests.create', ['kitchen_ids' => $item->id]) }}" 
                       class="btn btn-sm btn-light" 
                       title="Request Restock">
                      <i class="fa fa-shopping-cart text-warning"></i> Restock
                    </a>
                    <button class="btn btn-sm btn-light view-track-btn" 
                            data-item-id="{{ $item->id }}" 
                            data-item-name="{{ $item->name }}"
                            title="View Track">
                      <i class="fa fa-history"></i> Track
                    </button>
                    @if(!$isReadOnly)
                    <button class="btn btn-sm btn-light settings-stock-btn" 
                            data-item-id="{{ $item->id }}" 
                            data-item-name="{{ $item->name }}" 
                            data-minimum-stock="{{ $item->minimum_stock }}"
                            title="Stock Settings">
                      <i class="fa fa-cog"></i>
                    </button>
                    @endif
                  </div>
                </div>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-6">
                    <div class="mb-3">
                      <small class="text-muted d-block">Total Received</small>
                      <h5 class="mb-0 text-info">
                        {{ number_format($item->total_received ?? 0, 0) }} <small class="text-muted">{{ $item->unit }}</small>
                      </h5>
                    </div>

                    <div class="mb-3">
                      <small class="text-muted d-block">Total Consumed</small>
                      <h5 class="mb-0 text-secondary">
                        {{ number_format($item->total_consumed ?? 0, 0) }} <small class="text-muted">{{ $item->unit }}</small>
                      </h5>
                    </div>
                    
                    <div class="mb-3">
                      <small class="text-muted d-block">Available Stock</small>
                      <h5 class="mb-0 font-weight-bold text-{{ $statusColor }}" id="current-stock-text-{{ $item->id }}">
                        {{ number_format($item->current_stock, 0) }} <small class="text-muted">{{ $item->unit }}</small>
                      </h5>
                    </div>
                  </div>
                  
                  <div class="col-6 border-left">
                    <div class="mb-3">
                      <small class="text-muted d-block">Category</small>
                      <strong class="text-capitalize">{{ $item->category_name }}</strong>
                    </div>
                    
                    <div class="mb-3">
                      <small class="text-muted d-block">Min. Level</small>
                      <strong id="minimum-stock-display-{{ $item->id }}">{{ number_format($item->minimum_stock, 0) }} {{ $item->unit }}</strong>
                    </div>

                    @if($item->expiry_date)
                    <div class="mb-0">
                      <small class="text-muted d-block">Expires On</small>
                      @php
                        $daysToExpiry = now()->diffInDays($item->expiry_date, false);
                        $isExpiringSoon = $daysToExpiry <= 10 && $daysToExpiry >= 0;
                        $isExpired = $daysToExpiry < 0;
                      @endphp
                      
                      @if($isExpired)
                        <span class="badge badge-danger">EXPIRED ({{ $item->expiry_date->format('d M, Y') }})</span>
                      @elseif($isExpiringSoon)
                        <span class="text-danger font-weight-bold animated pulse infinite">
                          <i class="fa fa-clock-o"></i> EXPIRING SOON ({{ $item->expiry_date->format('d M, Y') }})
                        </span>
                      @else
                        <span class="text-warning font-weight-bold">{{ $item->expiry_date->format('d M, Y') }}</span>
                      @endif
                    </div>
                    @endif
                  </div>
                </div>
                
                <div class="mt-2" id="status-badge-{{ $item->id }}">
                  @if($stockStatus === 'critical')
                    <span class="badge badge-danger w-100 py-2"><i class="fa fa-exclamation-circle"></i> Out of Stock</span>
                  @elseif($stockStatus === 'low')
                    <span class="badge badge-warning w-100 py-2"><i class="fa fa-warning"></i> Low Stock</span>
                  @else
                    <span class="badge badge-success w-100 py-2"><i class="fa fa-check-circle"></i> healthy Balance</span>
                  @endif
                </div>
              </div>
              @if(!$isReadOnly)
              <div class="card-footer bg-transparent p-2">
                <div class="row">
                  <div class="col-7 pr-1">
                    <button class="btn btn-primary btn-block update-stock-btn shadow-sm" 
                            data-item-id="{{ $item->id }}" 
                            data-item-name="{{ $item->name }}" 
                            data-current-stock="{{ $item->current_stock }}"
                            data-unit="{{ $item->unit }}"
                            data-default-type="guest_use">
                      <i class="fa fa-cutlery"></i> Use
                    </button>
                  </div>
                  <div class="col-5 pl-1">
                    <button class="btn btn-warning btn-block update-stock-btn shadow-sm" 
                            style="background: #e77a31; border-color: #e77a31; color: white;"
                            data-item-id="{{ $item->id }}" 
                            data-item-name="{{ $item->name }}" 
                            data-current-stock="{{ $item->current_stock }}"
                            data-unit="{{ $item->unit }}"
                            data-default-type="destroyed">
                      <i class="fa fa-trash"></i> Lost
                    </button>
                  </div>
                </div>
              </div>
              @endif
            </div>
          </div>
          @endforeach

          @if($inventoryItems->count() === 0)
          <div class="col-md-12">
            <div class="alert alert-info text-center py-5">
              <i class="fa fa-info-circle fa-3x d-block mb-3"></i>
              <h3>No Inventory Items Found</h3>
              <p>Items will appear here once they are received from Shopping Lists.</p>
            </div>
          </div>
          @endif
        </div>
        
        <div class="d-flex justify-content-center mt-4">
          {{ $inventoryItems->appends(request()->input())->links() }}
        </div>
      </div>
    </div>
  </div>
</div>

@if(!$isReadOnly)
<!-- Adjustment Modal -->
<div class="modal fade" id="updateStockModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
      <div class="modal-header bg-primary text-white" style="border-radius: 15px 15px 0 0;">
        <h5 class="modal-title" id="stockModalTitle"><i class="fa fa-cutlery"></i> Record Consumption</h5>
        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <form id="updateStockForm">
        <div class="modal-body">
          <input type="hidden" id="item_id" name="item_id">
          <input type="hidden" id="adjustment_type" name="type">
          <div class="alert alert-light border mb-3">
            Item: <strong id="item_name_display"></strong><br>
            Current Balance: <strong id="current_stock_display"></strong> <span id="unit_display"></span>
          </div>
          

          
          <div class="form-group">
            <label id="quantityLabel">Quantity <span class="text-danger">*</span></label>
            <div class="input-group">
                <input type="number" step="1" class="form-control" name="quantity" required placeholder="0" min="1">
                <div class="input-group-append">
                    <span class="input-group-text modal-unit">unit</span>
                </div>
            </div>
            <small class="text-muted">Enter how much of this item was used or lost.</small>
          </div>
          
          <div class="form-group">
            <label>Notes (Optional)</label>
            <textarea class="form-control" name="notes" rows="2" placeholder="e.g. Breakfast service, Staff lunch, etc."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" id="saveBtn" class="btn btn-primary px-4">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Min Stock Modal -->
<div class="modal fade" id="minimumStockModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title"><i class="fa fa-cog"></i> Min. Stock Level Setting</h5>
        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <form id="minimumStockForm">
        <div class="modal-body">
          <input type="hidden" id="settings_item_id" name="item_id">
          <p>Update alert threshold for: <strong id="settings_item_name_display"></strong></p>
          
          <div class="form-group">
            <label>Minimum Stock Threshold <span class="text-danger">*</span></label>
            <input type="number" step="1" class="form-control" id="minimum_stock" name="minimum_stock" required min="0">
            <small class="text-muted text-capitalize">You will see a "LOW STOCK" alert once the balance hits this number.</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-dark">Apply Setting</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endif

<!-- Usage Tracking Modal -->
<div class="modal fade" id="usageTrackModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
      <div class="modal-header bg-dark text-white" style="border-radius: 15px 15px 0 0;">
        <h5 class="modal-title"><i class="fa fa-history"></i> Usage Tracking: <span id="track_item_name"></span></h5>
        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-hover table-striped" id="usageTimelineTable">
            <thead class="bg-light">
              <tr>
                <th>Date & Time</th>
                <th>Type</th>
                <th>Change</th>
                <th>Balance</th>
                <th>Staff</th>
                <th>Notes</th>
              </tr>
            </thead>
            <tbody id="usageTrackContent">
              <!-- Content loaded via AJAX -->
            </tbody>
          </table>
          <div id="noUsageData" class="text-center py-4 d-none">
            <i class="fa fa-info-circle fa-2x text-muted mb-2"></i>
            <p>No recorded movements for this item yet.</p>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div id="bulkActionBar" class="animated fadeInUp">
    <div class="d-flex justify-content-between align-items-center">
        <div class="mr-4">
            <span class="badge badge-warning mr-2" id="selectedCount" style="font-size: 16px;">0</span>
            <strong style="font-size: 14px;">Items Selected</strong>
        </div>
        <div class="d-flex align-items-center">
            <button class="btn btn-sm btn-light mr-2" id="selectAllBtn"><i class="fa fa-check-square"></i> Select Visible</button>
            <button class="btn btn-sm btn-outline-danger mr-3" id="clearSelectionBtn" style="color: #ff9d9d; border-color: #ff9d9d;"><i class="fa fa-times"></i> Clear</button>
            <button class="btn btn-sm btn-warning font-weight-bold px-3" id="bulkRequestBtn" style="background: #e77a31; border-color: #e77a31; color: white;">
                <i class="fa fa-shopping-cart"></i> Request Restock for Selected
            </button>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
  .card { transition: all 0.3s cubic-bezier(.25,.8,.25,1); border-width: 2px !important; }
  .card:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
  .badge { font-size: 0.85rem; }
  .btn-outline-primary:hover { color: white; }
  
  #bulkActionBar {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 2000;
    background: #333;
    color: white;
    padding: 15px 30px;
    border-radius: 50px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    display: none;
    width: auto;
    min-width: 450px;
  }
  .custom-checkbox .custom-control-input:checked ~ .custom-control-label::before {
    background-color: #e77a31;
    border-color: #e77a31;
  }
</style>
@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    $('.select2').select2();

    // Selection Logic
    function updateBulkBar() {
        var checkedCount = $('.stock-checkbox:checked').length;
        if (checkedCount > 0) {
            $('#bulkActionBar').fadeIn();
            $('#selectedCount').text(checkedCount);
        } else {
            $('#bulkActionBar').fadeOut();
        }
    }

    $(document).on('change', '.stock-checkbox', function() {
        if ($(this).is(':checked')) {
            $(this).closest('.inventory-card').find('.card').css('border-color', '#e77a31');
            $(this).closest('.inventory-card').find('.card').addClass('shadow-lg');
        } else {
            $(this).closest('.inventory-card').find('.card').css('border-color', '');
            $(this).closest('.inventory-card').find('.card').removeClass('shadow-lg');
        }
        updateBulkBar();
    });

    $('#selectAllBtn').on('click', function() {
        var visibleCheckboxes = $('.inventory-card:not([style*="display: none"]) .stock-checkbox');
        visibleCheckboxes.prop('checked', true).trigger('change');
    });

    $('#clearSelectionBtn').on('click', function() {
        $('.stock-checkbox').prop('checked', false).trigger('change');
    });

    $('#bulkRequestBtn').on('click', function() {
        var selectedIds = $('.stock-checkbox:checked').map(function() {
            return $(this).val();
        }).get().join(',');
        
        if (selectedIds) {
            window.location.href = "{{ route('chef-master.purchase-requests.create') }}?kitchen_ids=" + selectedIds;
        }
    });

    // Search and Filter
    function filterCards() {
        var searchTerm = $('#inventorySearch').val().toLowerCase().trim();
        var categoryTerm = $('#categoryFilter').val().toLowerCase();
        var visibleCount = 0;
        
        $('.inventory-card').each(function() {
            var $card = $(this);
            var name = $card.data('name') || '';
            var category = $card.data('category') || '';
            
            var matchesSearch = searchTerm === '' || name.includes(searchTerm);
            var matchesCategory = categoryTerm === '' || category === categoryTerm;
            
            if (matchesSearch && matchesCategory) {
                $card.show();
                visibleCount++;
            } else {
                $card.hide();
            }
        });
        $('#resultCount').text(visibleCount);
    }

    $('#inventorySearch, #categoryFilter').on('input change', filterCards);

    // Release Stock Submission

    
    // Manual Consumption Trigger
    $('.update-stock-btn').on('click', function() {
        var itemId = $(this).data('item-id');
        var itemName = $(this).data('item-name');
        var currentStock = $(this).data('current-stock');
        var unit = $(this).data('unit');
        var defaultType = $(this).data('default-type');
        
        $('#item_id').val(itemId);
        $('#item_name_display').text(itemName);
        $('#current_stock_display').text(currentStock);
        $('#unit_display').text(unit);
        $('.modal-unit').text(unit);
        
        if (defaultType) {
            $('#adjustment_type').val(defaultType);
            
            // Customize modal look based on type
            if (defaultType === 'destroyed') {
                $('#stockModalTitle').html('<i class="fa fa-trash"></i> Record Item Loss');
                $('.modal-header').removeClass('bg-primary').addClass('bg-danger');
                $('#quantityLabel').html('Quantity Lost / Spoiled <span class="text-danger">*</span>');
                $('#saveBtn').text('Record Loss').removeClass('btn-primary').addClass('btn-danger');
            } else {
                $('#stockModalTitle').html('<i class="fa fa-cutlery"></i> Record Item Usage');
                $('.modal-header').removeClass('bg-danger').addClass('bg-primary');
                $('#quantityLabel').html('Quantity Used <span class="text-danger">*</span>');
                $('#saveBtn').text('Save Usage').removeClass('btn-danger').addClass('btn-primary');
            }
        }
        
        $('#updateStockModal').modal('show');
    });

    // Manual Consumption Submit
    $('#updateStockForm').on('submit', function(e) {
        e.preventDefault();
        var itemId = $('#item_id').val();
        var formData = $(this).serialize();
        
        $.ajax({
            url: '/chef-master/inventory/' + itemId + '/update-stock',
            method: 'POST',
            data: formData + '&_token={{ csrf_token() }}',
            success: function(response) {
                if(response.success) {
                    swal("Success!", response.message, "success");
                    setTimeout(() => location.reload(), 1500);
                }
            },
            error: function(xhr) {
                swal("Error!", xhr.responseJSON.message || "Failed to update stock", "error");
            }
        });
    });

    // Min Stock Trigger
    $('.settings-stock-btn').on('click', function() {
        $('#settings_item_id').val($(this).data('item-id'));
        $('#settings_item_name_display').text($(this).data('item-name'));
        $('#minimum_stock').val($(this).data('minimum-stock'));
        $('#minimumStockModal').modal('show');
    });

    // Min Stock Submit
    $('#minimumStockForm').on('submit', function(e) {
        e.preventDefault();
        var itemId = $('#settings_item_id').val();
        
        $.ajax({
            url: '/chef-master/inventory/' + itemId + '/update-minimum-stock',
            method: 'POST',
            data: $(this).serialize() + '&_token={{ csrf_token() }}',
            success: function(response) {
                if(response.success) {
                    swal("Updated!", response.message, "success");
                    setTimeout(() => location.reload(), 1500);
                }
            },
            error: function(xhr) {
                swal("Error!", xhr.responseJSON.message || "Failed to update threshold", "error");
            }
        });
    });
    
    // Usage Tracking Logic
    $(document).on('click', '.view-track-btn', function() {
        var itemId = $(this).data('item-id');
        var itemName = $(this).data('item-name');
        
        $('#track_item_name').text(itemName);
        $('#usageTrackContent').html('<tr><td colspan="7" class="text-center font-italic">Loading tracking data...</td></tr>');
        $('#noUsageData').addClass('d-none');
        $('#usageTrackModal').modal('show');
        
        $.ajax({
            url: '/chef-master/inventory/' + itemId + '/usage-track',
            method: 'GET',
            success: function(response) {
                if(response.success && response.movements.length > 0) {
                    var html = '';
                    response.movements.forEach(function(m) {
                        var badgeClass = 'secondary';
                        if(m.type.toLowerCase().includes('guest') || m.type.toLowerCase().includes('sale')) badgeClass = 'info';
                        if(m.type.toLowerCase().includes('staff') || m.type.toLowerCase().includes('internal')) badgeClass = 'warning';
                        if(m.type.toLowerCase().includes('supply') || m.type.toLowerCase().includes('add')) badgeClass = 'success';
                        
                        html += '<tr>' +
                            '<td>' + m.date + '</td>' +
                            '<td><span class="badge badge-' + badgeClass + '">' + m.type + '</span></td>' +
                            '<td class="text-' + (m.is_addition ? 'success' : 'danger') + '"><strong>' + m.quantity + '</strong></td>' +
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
            error: function() {
                $('#usageTrackContent').html('<tr><td colspan="7" class="text-center text-danger">Failed to load tracking data.</td></tr>');
            }
        });
    });
});
</script>
@endsection
