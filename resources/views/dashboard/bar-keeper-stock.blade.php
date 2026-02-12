@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-cubes"></i> My Stock & Revenue Potential</h1>
        <p>Manage your bar inventory and maximize revenue</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route('bar-keeper.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Stock</li>
    </ul>
</div>

<!-- Stats Row -->
<div class="row mb-4">
  <div class="col-md col-sm-6 mb-2">
    <div class="widget-small primary coloured-icon"><i class="icon fa fa-wine-bottle fa-3x"></i>
      <div class="info">
        <h4>Products</h4>
        <p><b>{{ $myStock->count() }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md col-sm-6 mb-2">
    <div class="widget-small info coloured-icon"><i class="icon fa fa-cubes fa-3x"></i>
      <div class="info">
        <h4>In Stock</h4>
        <p><b>{{ number_format($myStock->sum('current_stock_pics'), 1) }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md col-sm-6 mb-2">
    <div class="widget-small warning coloured-icon" style="background-color: #e77a31;"><i class="icon fa fa-money fa-3x"></i>
      <div class="info">
        <h4>Revenue</h4>
        <p><b>{{ number_format($myStock->sum('revenue_generated'), 0) }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md col-sm-6 mb-2">
    <div class="widget-small primary coloured-icon" style="background-color: #764ba2;"><i class="icon fa fa-money-bill-wave fa-3x"></i>
      <div class="info">
        <h4>Value</h4>
        <p><b>{{ number_format($myStock->sum('revenue_serving'), 0) }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md col-sm-6 mb-2">
    <div class="widget-small success coloured-icon"><i class="icon fa fa-chart-line fa-3x"></i>
      <div class="info">
        <h4>Potential</h4>
        <p><b>{{ number_format($myStock->sum('current_profit'), 0) }}</b></p>
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
                <!-- Search Area -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-search"></i></span>
                            </div>
                            <input type="text" id="inventorySearch" class="form-control" placeholder="Search products...">
                        </div>
                    </div>
                    <div class="col-md-6 text-right">
                        <div id="searchResults" class="text-muted pt-2">
                            <span id="resultCount">{{ $myStock->count() }}</span> items found
                        </div>
                    </div>
                </div>

                @if($myStock->isEmpty())
                <div class="text-center p-5">
                    <i class="fa fa-box-open fa-3x text-muted mb-3"></i>
                    <h3>No Stock Available</h3>
                    <p>You haven't received any stock transfers yet.</p>
                </div>
                @else
                <!-- Enhanced Category Tabs -->
                <ul class="nav nav-pills nav-fill mb-3" role="tablist" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 10px; border-radius: 10px;">
                    <li class="nav-item">
                        <a class="nav-link active" id="all-tab" data-toggle="tab" href="#all" role="tab" style="border-radius: 8px; font-weight: 600; color: white;">
                            <i class="fa fa-th-large"></i> All
                            <span class="badge badge-light ml-1">{{ $myStock->count() }}</span>
                        </a>
                    </li>
                    @php
                        $categoryIcons = [
                            'spirits' => 'fa-wine-bottle',
                            'wines' => 'fa-wine-glass-alt',
                            'alcoholic_beverage' => 'fa-beer',
                            'non_alcoholic_beverage' => 'fa-glass-whiskey',
                            'water' => 'fa-tint',
                            'juices' => 'fa-lemon',
                            'energy_drinks' => 'fa-bolt',
                            'hot_beverages' => 'fa-mug-hot',
                            'cocktails' => 'fa-cocktail',
                        ];
                    @endphp
                    @foreach($categories as $category => $items)
                    <li class="nav-item">
                        <a class="nav-link" id="{{ $category }}-tab" data-toggle="tab" href="#{{ $category }}" role="tab" style="border-radius: 8px; font-weight: 600; color: rgba(255,255,255,0.8);">
                            <i class="fa {{ $categoryIcons[$category] ?? 'fa-tag' }}"></i> {{ $items->first()['category_name'] ?? ucfirst(str_replace('_', ' ', $category)) }}
                            <span class="badge badge-light ml-1">{{ $items->count() }}</span>
                        </a>
                    </li>
                    @endforeach
                </ul>

                <div class="tab-content" style="background: #fff; padding: 20px 0;">
                    <!-- All Products Tab -->
                    <div class="tab-pane fade show active" id="all" role="tabpanel">
                        <div class="row" id="inventoryCards">
                            @foreach($myStock as $item)
                            @include('dashboard.partials.bar-stock-card', ['item' => $item])
                            @endforeach
                        </div>
                    </div>

                    <!-- Category Tabs -->
                    @foreach($categories as $category => $items)
                    <div class="tab-pane fade" id="{{ $category }}" role="tabpanel">
                        <div class="row">
                            @foreach($items as $item)
                            @include('dashboard.partials.bar-stock-card', ['item' => $item])
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Product Settings Modal -->
<div class="modal fade" id="productSettingsModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title"><i class="fa fa-cog"></i> Product Settings & Pricing</h5>
        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
          <!-- Item Info -->
          <div class="mb-4 text-center">
              <h4 id="settings_item_name_display" class="mb-1"></h4>
              <span class="badge badge-info shadow-sm" id="variant_pill"></span>
          </div>

          <!-- Section 1: Stock Alert (Only for Bar Keepers, not Managers) -->
          @if($role !== 'manager')
          <div class="p-3 mb-3 rounded" style="background: #f8f9fa; border-left: 5px solid #17a2b8;">
              <h6 class="font-weight-bold text-info"><i class="fa fa-bell"></i> Inventory Alert</h6>
              <form id="minimumStockForm">
                  <input type="hidden" class="settings_variant_id" name="variant_id">
                  <div class="form-group mb-0 mt-2">
                    <label class="small font-weight-bold">Min. Stock Alert Level (PICs)</label>
                    <div class="input-group">
                        <input type="number" step="0.01" class="form-control" id="minimum_stock" name="minimum_stock" required min="0">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-info">Update Alert</button>
                        </div>
                    </div>
                  </div>
              </form>
          </div>
          @endif

          <!-- Section 2: Pricing (Crucial for manager) -->
          <div class="p-3 rounded" style="background: #fff3e0; border-left: 5px solid #e77a31;">
              <h6 class="font-weight-bold text-warning" style="color: #cc6a27 !important;"><i class="fa fa-money-bill-wave"></i> Active Pricing (Daily Charges)</h6>
              <form id="priceUpdateForm">
                  <input type="hidden" class="settings_variant_id" name="variant_id">
                  
                  <div class="form-row mt-2">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="small font-weight-bold">Bottle/PIC Price (TSH)</label>
                            <input type="number" class="form-control border-warning" id="price_pic" name="selling_price_per_pic" required min="0">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label class="small font-weight-bold">Glass/Tot Price (TSH)</label>
                            <input type="number" class="form-control border-warning" id="price_glass" name="selling_price_per_serving" min="0">
                        </div>
                    </div>
                  </div>
                  <button type="submit" class="btn btn-block text-white" style="background: #e77a31;">Update All Prices</button>
              </form>
          </div>
      </div>
      <div class="modal-footer bg-light">
          <button type="button" class="btn btn-link text-muted" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

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

@endsection

@section('styles')
<style>
  .card { transition: all 0.3s cubic-bezier(.25,.8,.25,1); border-width: 2px !important; }
  .card:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.15) !important; }
  .badge { font-size: 0.85rem; }
  .nav-pills .nav-link.active { 
    background: white !important; 
    color: #667eea !important; 
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
  }
  .nav-pills .nav-link:hover { 
    background: rgba(255,255,255,0.2); 
    color: white !important;
  }
  .nav-pills .nav-link {
    transition: all 0.3s ease;
  }
</style>
@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<script>
$(document).ready(function() {
    // Bulk Selection Logic
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
        var activeTabId = $('.tab-pane.active').attr('id');
        var visibleCheckboxes = $('#' + activeTabId).find('.inventory-card:not([style*="display: none"]) .stock-checkbox');
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
            window.location.href = "{{ route('bar-keeper.purchase-requests.create') }}?ids=" + selectedIds;
        }
    });

    // Real-time Search
    function filterCards() {
        var searchTerm = $('#inventorySearch').val().toLowerCase().trim();
        var visibleCount = 0;
        
        $('.inventory-card').each(function() {
            var $card = $(this);
            var name = $card.data('name') || '';
            var variant = $card.data('variant') || '';
            var brand = $card.data('brand') || '';
            
            var matchesSearch = searchTerm === '' || name.includes(searchTerm) || variant.includes(searchTerm) || brand.includes(searchTerm);
            
            if (matchesSearch) {
                $card.show();
                visibleCount++;
            } else {
                $card.hide();
            }
        });
        $('#resultCount').text(visibleCount);
    }

    $('#inventorySearch').on('input', filterCards);

    // Product Settings Trigger
    $(document).on('click', '.settings-stock-btn', function() {
        var btn = $(this);
        $('.settings_variant_id').val(btn.data('variant-id'));
        $('#settings_item_name_display').text(btn.data('item-name'));
        $('#minimum_stock').val(btn.data('minimum-stock'));
        $('#price_pic').val(btn.data('price-pic'));
        $('#price_glass').val(btn.data('price-glass'));
        $('#productSettingsModal').modal('show');
    });

    // Min Stock Submit
    $('#minimumStockForm').on('submit', function(e) {
        e.preventDefault();
        var variantId = $(this).find('.settings_variant_id').val();
        
        $.ajax({
            url: '/bar-keeper/stock/update-minimum/' + variantId,
            method: 'POST',
            data: $(this).serialize() + '&_token={{ csrf_token() }}',
            success: function(response) {
                if(response.success) {
                    swal("Updated!", "Inventory threshold has been adjusted.", "success");
                    setTimeout(function() { location.reload(); }, 1000);
                }
            },
            error: function(xhr) {
                swal("Error!", xhr.responseJSON.message || "Failed to update threshold", "error");
            }
        });
    });

    // Price Update Submit
    $('#priceUpdateForm').on('submit', function(e) {
        e.preventDefault();
        var variantId = $(this).find('.settings_variant_id').val();
        
        $.ajax({
            url: '/bar-keeper/stock/update-prices/' + variantId,
            method: 'POST',
            data: $(this).serialize() + '&_token={{ csrf_token() }}',
            success: function(response) {
                if(response.success) {
                    swal("Success!", "Daily prices updated successfully.", "success");
                    setTimeout(function() { location.reload(); }, 1000);
                }
            },
            error: function(xhr) {
                swal("Error!", xhr.responseJSON.message || "Failed to update prices", "error");
            }
        });
    });

    // Usage Tracking Logic
    $(document).on('click', '.view-track-btn', function() {
        var variantId = $(this).data('variant-id');
        var itemName = $(this).data('item-name');
        
        $('#track_item_name').text(itemName);
        $('#usageTrackContent').html('<tr><td colspan="6" class="text-center font-italic">Loading tracking data...</td></tr>');
        $('#noUsageData').addClass('d-none');
        $('#usageTrackModal').modal('show');
        
        $.ajax({
            url: '/bar-keeper/stock/' + variantId + '/usage-track',
            method: 'GET',
            success: function(response) {
                if(response.success && response.movements.length > 0) {
                    var html = '';
                    response.movements.forEach(function(m) {
                        var badgeClass = 'secondary';
                        var icon = '';
                        var typeLower = m.type.toLowerCase();
                        
                        if(typeLower.indexOf('sale') !== -1 || typeLower.indexOf('service') !== -1) {
                            badgeClass = 'info';
                        }
                        if(typeLower.indexOf('receive') !== -1) {
                            badgeClass = 'success';
                        }
                        if(m.is_price_change) {
                            badgeClass = 'primary';
                            icon = '<i class="fa fa-dollar-sign"></i> ';
                        }
                        
                        var colorClass = 'danger';
                        if (m.is_price_change) {
                            colorClass = 'dark';
                        } else if (m.is_addition) {
                            colorClass = 'success';
                        }
                        
                        html += '<tr>' +
                            '<td>' + m.date + '</td>' +
                            '<td><span class="badge badge-' + badgeClass + '">' + icon + m.type + '</span></td>' +
                            '<td class="text-' + colorClass + '"><strong>' + m.quantity + '</strong></td>' +
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
                $('#usageTrackContent').html('<tr><td colspan="6" class="text-center text-danger">Failed to load tracking data.</td></tr>');
            }
        });
    });
});
</script>
@endsection

@section('styles')
<style>
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

/* Mobile Optimizations */
@media (max-width: 768px) {
    /* Make Action Bar Responsive */
    #bulkActionBar {
        width: 95%;
        min-width: auto;
        padding: 10px 15px;
        bottom: 60px; /* Above bottom nav if exists, or just spaced */
        border-radius: 15px;
        flex-direction: column;
        align-items: stretch !important;
    }
    
    #bulkActionBar .d-flex {
        flex-direction: column;
        align-items: center;
        width: 100%;
    }
    
    #bulkActionBar .mr-4 {
        margin-right: 0 !important;
        margin-bottom: 10px;
        text-align: center;
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    
    #bulkActionBar .d-flex.align-items-center {
        width: 100%;
        justify-content: space-between;
    }
    
    #bulkActionBar button {
        flex: 1;
        margin: 0 5px;
        font-size: 11px;
        padding: 8px 5px;
        white-space: nowrap;
    }
    
    #bulkActionBar button i {
        display: none; /* Hide icons to save space */
    }

    /* Scrollable Tabs */
    .nav-pills {
        display: flex;
        flex-wrap: nowrap;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        padding-bottom: 5px; /* Space for scrollbar */
    }
    
    .nav-pills .nav-item {
        flex: 0 0 auto;
        margin-right: 5px;
    }
    
    .nav-pills .nav-link {
        font-size: 13px;
        padding: 8px 12px;
        white-space: nowrap;
    }
    
    /* Stats Widgets */
    .widget-small {
        margin-bottom: 10px;
    }
    .widget-small .icon {
        min-width: 60px;
        width: 60px;
    }
    .widget-small .info {
        padding: 10px;
    }
    .widget-small .info h6 {
        font-size: 10px;
    }
    .widget-small .info p {
        font-size: 14px;
    }
}
</style>

<div id="bulkActionBar" class="animated fadeInUp">
    <div class="d-flex justify-content-between align-items-center">
        <div class="mr-4">
            <span class="badge badge-warning mr-2" id="selectedCount" style="font-size: 16px;">0</span>
            <strong style="font-size: 14px;">Selected</strong>
        </div>
        <div class="d-flex align-items-center w-100 justify-content-center">
            <button class="btn btn-sm btn-light mr-2" id="selectAllBtn"><i class="fa fa-check-square"></i> All</button>
            <button class="btn btn-sm btn-outline-danger mr-2" id="clearSelectionBtn" style="color: #ff9d9d; border-color: #ff9d9d;"><i class="fa fa-times"></i> Clear</button>
            <button class="btn btn-sm btn-warning font-weight-bold flex-grow-1" id="bulkRequestBtn" style="background: #e77a31; border-color: #e77a31; color: white;">
                <i class="fa fa-shopping-cart"></i> Restock
            </button>
        </div>
    </div>
</div>
