@extends('dashboard.layouts.app')

@section('content')
@php
  // Determine route prefix from current route
  $currentRouteName = request()->route()->getName();
  $routePrefix = 'housekeeper'; // default
  if (str_contains($currentRouteName, 'reception.')) {
    $routePrefix = 'reception';
  } elseif (str_contains($currentRouteName, 'housekeeper.')) {
    $routePrefix = 'housekeeper';
  } elseif (str_contains($currentRouteName, 'bar-keeper.')) {
    $routePrefix = 'bar-keeper';
  } elseif (str_contains($currentRouteName, 'chef-master.')) {
    $routePrefix = 'chef-master';
  }
  
  // Determine dashboard route
  $dashboardRoute = $routePrefix . '.dashboard';
  $templatesRoute = $routePrefix . '.purchase-requests.templates';
  $storeRoute = $routePrefix . '.purchase-requests.store';
  $myRequestsRoute = $routePrefix . '.purchase-requests.my';
  
  // Determine placeholder based on route/role
  $itemPlaceholder = "e.g., Soap, Towels, Toilet Paper";
  $templatePlaceholder = "e.g., Weekly Housekeeping Supplies";
  
  if ($routePrefix === 'chef-master') {
    $itemPlaceholder = "e.g., Beef, Onions, Flour, Cooking Oil";
    $templatePlaceholder = "e.g., Kitchen Daily Vegetables";
  } elseif ($routePrefix === 'bar-keeper') {
    $itemPlaceholder = "e.g., Soda, Beers, Wine, Ice";
    $templatePlaceholder = "e.g., Weekly Bar Restock";
  }
@endphp

<div class="app-title">
  <div>
    <h1><i class="fa fa-shopping-cart"></i> Create Purchase Request</h1>
    <p>Submit a request for items you need</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route($dashboardRoute) }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Purchase Request</a></li>
  </ul>
</div>

@if($deadline)
<div class="row mb-3">
  <div class="col-md-12">
    <div class="alert alert-info">
      <i class="fa fa-info-circle"></i> 
      <strong>Purchase Deadline:</strong> 
      Next purchase day is <strong>{{ ucfirst($deadline->day_of_week) }}</strong> at <strong>{{ \Carbon\Carbon::parse($deadline->deadline_time)->format('H:i') }}</strong>.
      @if($nextDeadline)
        <br>Next deadline: <strong>{{ $nextDeadline->format('F d, Y H:i') }}</strong>
      @endif
    </div>
  </div>
</div>
@endif

<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn mb-3">
        <h3 class="title"><i class="fa fa-plus-circle"></i> New Purchase Request</h3>
        <div>
          <a href="{{ route($templatesRoute) }}" class="btn btn-sm btn-outline-primary">
            <i class="fa fa-book"></i> Manage Templates
          </a>
        </div>
      </div>
      <div class="tile-body">
        @if($templates && $templates->count() > 0)
        <div class="row mb-3">
          <div class="col-md-12">
            <div class="alert alert-info">
              <div class="row align-items-center">
                <div class="col-md-8">
                  <label for="templateSelect" class="mb-0"><strong>Use Template:</strong></label>
                  <select id="templateSelect" class="form-control mt-2">
                    <option value="">-- Select a template to load items -- ({{ $templatePlaceholder }})</option>
                    @foreach($templates as $template)
                    <option value="{{ $template->id }}" data-items="{{ json_encode($template->items) }}">
                      {{ $template->name }} ({{ count($template->items) }} item(s))
                    </option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-4 text-right">
                  <small class="text-muted">Templates help you quickly create requests with frequently used items.</small>
                </div>
              </div>
            </div>
          </div>
        </div>
        @endif
        <form id="purchaseRequestForm">
          <div id="itemsContainer">
            @if(isset($preFilledItems) && count($preFilledItems) > 0)
              @foreach($preFilledItems as $index => $preItem)
              <div class="item-row mb-4" style="border: 1px solid #dee2e6; padding: 20px; border-radius: 8px; background: #fdfdfd; border-left: 5px solid #e77a31;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h5 class="mb-0"><i class="fa fa-cube"></i> Item <span class="item-number">{{ $index + 1 }}</span></h5>
                  <button type="button" class="btn btn-sm btn-danger remove-item-btn">
                    <i class="fa fa-trash"></i> Remove
                  </button>
                </div>
                
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Item Name <span class="text-danger">*</span></label>
                      <input type="text" class="form-control item-name" name="items[{{ $index }}][item_name]" value="{{ $preItem['item_name'] }}" required>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Category</label>
                      <select class="form-control item-category" name="items[{{ $index }}][category]">
                        <option value="">Select Category</option>
                        @if($routePrefix === 'admin' || $routePrefix === 'chef-master')
                        <optgroup label="Food & Kitchen">
                            <option value="meat_poultry" {{ $preItem['category'] == 'meat_poultry' ? 'selected' : '' }}>Meat & Poultry</option>
                            <option value="seafood" {{ $preItem['category'] == 'seafood' ? 'selected' : '' }}>Seafood & Fish</option>
                            <option value="vegetables" {{ $preItem['category'] == 'vegetables' ? 'selected' : '' }}>Vegetables & Fruits</option>
                            <option value="dairy" {{ $preItem['category'] == 'dairy' ? 'selected' : '' }}>Dairy & Eggs</option>
                            <option value="pantry_baking" {{ $preItem['category'] == 'pantry_baking' || $preItem['category'] == 'pantry' || $preItem['category'] == 'baking' ? 'selected' : '' }}>Pantry & Baking</option>
                            <option value="spices_herbs" {{ $preItem['category'] == 'spices_herbs' || $preItem['category'] == 'spices' || $preItem['category'] == 'sauces' ? 'selected' : '' }}>Spices, Herbs & Sauces</option>
                            <option value="bakery" {{ $preItem['category'] == 'bakery' ? 'selected' : '' }}>Bakery & Bread</option>
                            <option value="oils_fats" {{ $preItem['category'] == 'oils_fats' ? 'selected' : '' }}>Cooking Oil & Fats</option>
                        </optgroup>
                        @endif
                        @if($routePrefix === 'admin' || $routePrefix === 'bar-keeper')
                        <optgroup label="Bar & Beverages">
                            <option value="non_alcoholic_beverage" {{ $preItem['category'] == 'non_alcoholic_beverage' || $preItem['category'] == 'drinks' || $preItem['category'] == 'juices' ? 'selected' : '' }}>Soda / Soft Drinks / Juices</option>
                            <option value="alcoholic_beverage" {{ $preItem['category'] == 'alcoholic_beverage' || $preItem['category'] == 'beer' ? 'selected' : '' }}>Beer / Cider</option>
                            <option value="spirits" {{ $preItem['category'] == 'spirits' ? 'selected' : '' }}>Spirits</option>
                            <option value="wines" {{ $preItem['category'] == 'wines' ? 'selected' : '' }}>Wines</option>
                            <option value="water" {{ $preItem['category'] == 'water' ? 'selected' : '' }}>Water</option>
                        </optgroup>
                        @endif
                        <optgroup label="Other">
                            <option value="cleaning_supplies" {{ $preItem['category'] == 'cleaning_supplies' ? 'selected' : '' }}>Cleaning Supplies</option>
                            <option value="linens" {{ $preItem['category'] == 'linens' ? 'selected' : '' }}>Linens / Housekeeping</option>
                            <option value="other" {{ $preItem['category'] == 'other' ? 'selected' : '' }}>Other</option>
                        </optgroup>
                      </select>
                    </div>
                  </div>
                </div>
                
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>Unit <span class="text-danger">*</span></label>
                      <select class="form-control item-unit" name="items[{{ $index }}][unit]" required onchange="toggleCustomUnit(this)">
                        <option value="">Select Unit</option>
                        <option value="kg" {{ strtolower($preItem['unit']) == 'kg' || strtolower($preItem['unit']) == 'kilograms' ? 'selected' : '' }}>Kilograms (kg)</option>
                        <option value="g" {{ strtolower($preItem['unit']) == 'g' || strtolower($preItem['unit']) == 'grams' ? 'selected' : '' }}>Grams (g)</option>
                        <option value="liters" {{ strtolower($preItem['unit']) == 'liters' || strtolower($preItem['unit']) == 'l' ? 'selected' : '' }}>Liters (L)</option>
                        <option value="ml" {{ strtolower($preItem['unit']) == 'ml' || strtolower($preItem['unit']) == 'milliliters' ? 'selected' : '' }}>Milliliters (ml)</option>
                        <option value="pcs" {{ strtolower($preItem['unit']) == 'pcs' || strtolower($preItem['unit']) == 'pieces' ? 'selected' : '' }}>Pieces (pcs)</option>
                        <option value="bottles" {{ strtolower($preItem['unit']) == 'bottles' || strtolower($preItem['unit']) == 'bottle' || strtolower($preItem['unit']) == 'pic' ? 'selected' : '' }}>Bottles / PIC</option>
                        <option value="packs" {{ strtolower($preItem['unit']) == 'packs' || strtolower($preItem['unit']) == 'pack' ? 'selected' : '' }}>Packs</option>
                        <option value="boxes" {{ strtolower($preItem['unit']) == 'boxes' || strtolower($preItem['unit']) == 'box' ? 'selected' : '' }}>Boxes</option>
                        <option value="cartons" {{ strtolower($preItem['unit']) == 'cartons' || strtolower($preItem['unit']) == 'carton' ? 'selected' : '' }}>Cartons</option>
                        <option value="trays" {{ strtolower($preItem['unit']) == 'trays' || strtolower($preItem['unit']) == 'tray' ? 'selected' : '' }}>Trays</option>
                        <option value="custom">Custom Unit</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-3 custom-unit-field" style="display: none;">
                    <div class="form-group">
                      <label>Specify Unit <span class="text-danger">*</span></label>
                      <input type="text" class="form-control item-custom-unit" name="items[{{ $index }}][custom_unit]" placeholder="e.g., gallons, ounces, etc.">
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>Quantity <span class="text-danger">*</span></label>
                      <input type="number" step="1" class="form-control item-quantity" name="items[{{ $index }}][quantity]" value="{{ $preItem['quantity'] }}" required min="1">
                    </div>
                  </div>
                  <div class="col-md-3 water-size-field" style="display: none;">
                    <div class="form-group">
                      <label>Water Size <span class="text-danger">*</span></label>
                      <select class="form-control item-water-size" name="items[{{ $index }}][water_size]">
                        <option value="small">Small</option>
                        <option value="large">Large</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>Priority <span class="text-danger">*</span></label>
                      <select class="form-control item-priority" name="items[{{ $index }}][priority]" required>
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                      </select>
                    </div>
                  </div>
                </div>
                
                <div class="form-group">
                  <label>Reason <span class="text-danger">*</span></label>
                  <textarea class="form-control item-reason" name="items[{{ $index }}][reason]" rows="2" required placeholder="Stocking up for bar inventory">Restocking low inventory</textarea>
                </div>
              </div>
              @endforeach
            @else
              <!-- First Item Row (Default) -->
              <div class="item-row mb-4" style="border: 1px solid #dee2e6; padding: 20px; border-radius: 8px; background: #f8f9fa;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h5 class="mb-0"><i class="fa fa-cube"></i> Item <span class="item-number">1</span></h5>
                  <button type="button" class="btn btn-sm btn-danger remove-item-btn" style="display: none;">
                    <i class="fa fa-trash"></i> Remove
                  </button>
                </div>
                
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Item Name <span class="text-danger">*</span></label>
                      <input type="text" class="form-control item-name" name="items[0][item_name]" required placeholder="{{ $itemPlaceholder }}">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Category</label>
                      <select class="form-control item-category" name="items[0][category]" onchange="toggleWaterSizeField(this)">
                        <option value="">Select Category</option>
                        @if($routePrefix === 'admin' || $routePrefix === 'chef-master')
                        <optgroup label="Food & Kitchen">
                            <option value="meat_poultry">Meat & Poultry</option>
                            <option value="seafood">Seafood & Fish</option>
                            <option value="vegetables">Vegetables & Fruits</option>
                            <option value="dairy">Dairy & Eggs</option>
                            <option value="pantry_baking">Pantry & Baking</option>
                            <option value="spices_herbs">Spices, Herbs & Sauces</option>
                            <option value="bakery">Bakery & Bread</option>
                            <option value="oils_fats">Cooking Oil & Fats</option>
                        </optgroup>
                        @endif
                        @if($routePrefix === 'admin' || $routePrefix === 'bar-keeper')
                        <optgroup label="Bar & Beverages">
                            <option value="non_alcoholic_beverage">Soda / Soft Drinks / Juices</option>
                            <option value="alcoholic_beverage">Beer / Cider</option>
                            <option value="spirits">Spirits</option>
                            <option value="wines">Wines</option>
                            <option value="water">Water</option>
                        </optgroup>
                        @endif
                        <optgroup label="Other">
                            <option value="cleaning_supplies">Cleaning Supplies</option>
                            <option value="linens">Linens / Housekeeping</option>
                            <option value="other">Other</option>
                        </optgroup>
                      </select>
                    </div>
                  </div>
                </div>
                
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>Unit <span class="text-danger">*</span></label>
                      <select class="form-control item-unit" name="items[0][unit]" required onchange="toggleCustomUnit(this)">
                        <option value="">Select Unit</option>
                        <option value="kg">Kilograms (kg)</option>
                        <option value="g">Grams (g)</option>
                        <option value="liters">Liters (L)</option>
                        <option value="ml">Milliliters (ml)</option>
                        <option value="pcs">Pieces (pcs)</option>
                        <option value="bottles">Bottles / PIC</option>
                        <option value="packs">Packs</option>
                        <option value="boxes">Boxes</option>
                        <option value="cartons">Cartons</option>
                        <option value="trays">Trays</option>
                        <option value="custom">Custom Unit</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>Quantity <span class="text-danger">*</span></label>
                      <input type="number" step="1" class="form-control item-quantity" name="items[0][quantity]" required min="1">
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>Priority <span class="text-danger">*</span></label>
                      <select class="form-control item-priority" name="items[0][priority]" required>
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                      </select>
                    </div>
                  </div>
                </div>
                
                <div class="form-group">
                  <label>Reason <span class="text-danger">*</span></label>
                  <textarea class="form-control item-reason" name="items[0][reason]" rows="2" required placeholder="Why do you need this item?"></textarea>
                </div>
              </div>
            @endif
          </div>
          
          <div class="form-group">
            <button type="button" class="btn btn-success" id="addItemBtn">
              <i class="fa fa-plus"></i> Add Another Item
            </button>
          </div>
          
          <hr>
          
          <div class="form-group">
            <button type="submit" class="btn btn-primary">
              <i class="fa fa-paper-plane"></i> Submit All Requests
            </button>
            <a href="{{ route($myRequestsRoute) }}" class="btn btn-secondary">
              <i class="fa fa-list"></i> View My Requests
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<script>
$(document).ready(function() {
    let itemIndex = 1;
    
    // Add new item row
    $('#addItemBtn').on('click', function() {
        const newRow = `
            <div class="item-row mb-4" style="border: 1px solid #dee2e6; padding: 20px; border-radius: 8px; background: #f8f9fa;">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0"><i class="fa fa-cube"></i> Item <span class="item-number">${itemIndex + 1}</span></h5>
                <button type="button" class="btn btn-sm btn-danger remove-item-btn">
                  <i class="fa fa-trash"></i> Remove
                </button>
              </div>
              
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Item Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control item-name" name="items[${itemIndex}][item_name]" required placeholder="e.g., Soap, Towel, Drinking Water">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Category</label>
                    <select class="form-control item-category" name="items[${itemIndex}][category]" onchange="toggleWaterSizeField(this)">
                      <option value="">Select Category</option>
                      @if($routePrefix === 'bar-keeper')
                        <optgroup label="Bar & Beverages">
                          <option value="non_alcoholic_beverage">Soda / Soft Drinks</option>
                          <option value="energy_drinks">Energy Drinks</option>
                          <option value="juices">Juices</option>
                          <option value="water">Water</option>
                          <option value="alcoholic_beverage">Beer / Cider</option>
                          <option value="wines">Wines</option>
                          <option value="spirits">Spirits</option>
                          <option value="hot_beverages">Hot Beverages</option>
                          <option value="cocktails">Cocktails</option>
                        </optgroup>
                      @elseif($routePrefix === 'chef-master')
                        <optgroup label="Kitchen & Food">
                          <option value="meat_poultry">Meat & Poultry</option>
                          <option value="seafood">Seafood & Fish</option>
                          <option value="vegetables">Vegetables & Fruits</option>
                          <option value="dairy">Dairy & Eggs</option>
                          <option value="pantry_baking">Pantry & Baking</option>
                          <option value="food">General Food</option>
                        </optgroup>
                      @else
                        <option value="cleaning_supplies">Cleaning Supplies</option>
                        <option value="linens">Linens</option>
                        <option value="other">Other</option>
                      @endif
                      <option value="other">Other</option>
                    </select>
                  </div>
                </div>
              </div>
              
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label>Unit <span class="text-danger">*</span></label>
                    <select class="form-control item-unit" name="items[${itemIndex}][unit]" required onchange="toggleCustomUnit(this)">
                      <option value="pcs">Pieces (pcs)</option>
                      <option value="liters">Liters (L)</option>
                      <option value="ml">Milliliters (ml)</option>
                      <option value="kg">Kilograms (kg)</option>
                      <option value="g">Grams (g)</option>
                      <option value="boxes">Boxes</option>
                      <option value="bottles">PIC (Bottle)</option>
                      <option value="rolls">Rolls</option>
                      <option value="packs">Packs</option>
                      <option value="cartons">Cartons</option>
                      <option value="bags">Bags</option>
                      <option value="custom">Custom Unit</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-3 custom-unit-field" style="display: none;">
                  <div class="form-group">
                    <label>Specify Unit <span class="text-danger">*</span></label>
                    <input type="text" class="form-control item-custom-unit" name="items[${itemIndex}][custom_unit]" placeholder="e.g., gallons, ounces, etc.">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label>Quantity <span class="text-danger">*</span></label>
                    <input type="number" step="1" class="form-control item-quantity" name="items[${itemIndex}][quantity]" required min="1">
                  </div>
                </div>
                <div class="col-md-3 water-size-field" style="display: none;">
                  <div class="form-group">
                    <label>Water Size <span class="text-danger">*</span></label>
                    <select class="form-control item-water-size" name="items[${itemIndex}][water_size]">
                      <option value="small">Small</option>
                      <option value="large">Large</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label>Priority <span class="text-danger">*</span></label>
                    <select class="form-control item-priority" name="items[${itemIndex}][priority]" required>
                      <option value="low">Low</option>
                      <option value="medium" selected>Medium</option>
                      <option value="high">High</option>
                      <option value="urgent">Urgent</option>
                    </select>
                  </div>
                </div>
              </div>
              
              <div class="form-group">
                <label>Reason <span class="text-danger">*</span></label>
                <textarea class="form-control item-reason" name="items[${itemIndex}][reason]" rows="2" required placeholder="Why do you need this item? (e.g., Running low, about to finish, etc.)"></textarea>
              </div>
            </div>
        `;
        
        $('#itemsContainer').append(newRow);
        itemIndex++;
        updateItemNumbers();
        updateRemoveButtons();
    });
    
    // Remove item row
    $(document).on('click', '.remove-item-btn', function() {
        $(this).closest('.item-row').remove();
        updateItemNumbers();
        updateRemoveButtons();
    });
    
    // Update item numbers
    function updateItemNumbers() {
        $('.item-row').each(function(index) {
            $(this).find('.item-number').text(index + 1);
            // Update all input names with new index
            const newIndex = index;
            $(this).find('input, select, textarea').each(function() {
                const name = $(this).attr('name');
                if (name) {
                    const newName = name.replace(/items\[\d+\]/, `items[${newIndex}]`);
                    $(this).attr('name', newName);
                }
            });
        });
    }
    
    // Show/hide remove buttons (hide if only one item)
    function updateRemoveButtons() {
        const itemCount = $('.item-row').length;
        if (itemCount > 1) {
            $('.remove-item-btn').show();
        } else {
            $('.remove-item-btn').hide();
        }
    }
    
    // Toggle custom unit field
    function toggleCustomUnit(selectElement) {
        const $row = $(selectElement).closest('.item-row');
        const $customUnitField = $row.find('.custom-unit-field');
        const $customUnitInput = $row.find('.item-custom-unit');
        
        if ($(selectElement).val() === 'custom') {
            $customUnitField.show();
            $customUnitInput.prop('required', true);
        } else {
            $customUnitField.hide();
            $customUnitInput.prop('required', false);
            $customUnitInput.val('');
        }
    }
    
    // Toggle water size field (show when category is "water" and unit is "pcs")
    function toggleWaterSizeField(selectElement) {
        const $row = $(selectElement).closest('.item-row');
        const $waterSizeField = $row.find('.water-size-field');
        const $waterSizeSelect = $row.find('.item-water-size');
        const category = $(selectElement).val();
        const unit = $row.find('.item-unit').val();
        
        if (category === 'water' && unit === 'pcs') {
            $waterSizeField.show();
            $waterSizeSelect.prop('required', true);
        } else {
            $waterSizeField.hide();
            $waterSizeSelect.prop('required', false);
        }
    }
    
    // Handle custom unit toggle for dynamically added items
    $(document).on('change', '.item-unit', function() {
        toggleCustomUnit(this);
        // Also check if we need to show water size field
        const $row = $(this).closest('.item-row');
        const category = $row.find('.item-category').val();
        const unit = $(this).val();
        if (category === 'water' && unit === 'pcs') {
            $row.find('.water-size-field').show();
            $row.find('.item-water-size').prop('required', true);
        } else {
            $row.find('.water-size-field').hide();
            $row.find('.item-water-size').prop('required', false);
        }
    });
    
    // Handle category change for water size field
    $(document).on('change', '.item-category', function() {
        toggleWaterSizeField(this);
    });
    
    // Form submission
    $('#purchaseRequestForm').on('submit', function(e) {
        e.preventDefault();
        
        // Validate all items
        let isValid = true;
        $('.item-row').each(function() {
            const itemName = ($(this).find('.item-name').val() || '').trim();
            const quantity = $(this).find('.item-quantity').val();
            const unit = $(this).find('.item-unit').val();
            const customUnit = ($(this).find('.item-custom-unit').val() || '').trim();
            const category = $(this).find('.item-category').val();
            const waterSize = $(this).find('.item-water-size').val();
            const priority = $(this).find('.item-priority').val();
            const reason = ($(this).find('.item-reason').val() || '').trim();
            
            // Check if custom unit is selected but not filled
            if (unit === 'custom' && !customUnit) {
                isValid = false;
                $(this).css('border-color', '#dc3545');
            } 
            // Check if water category with pcs unit but no size selected
            else if (category === 'water' && unit === 'pcs' && !waterSize) {
                isValid = false;
                $(this).css('border-color', '#dc3545');
            }
            else if (!itemName || !quantity || !unit || !priority || !reason) {
                isValid = false;
                $(this).css('border-color', '#dc3545');
            } else {
                $(this).css('border-color', '#dee2e6');
            }
        });
        
        if (!isValid) {
            Swal.fire({
                icon: "error",
                title: "Validation Error!",
                text: "Please fill in all required fields for all items. If you selected 'Custom Unit', please specify the unit name. If category is 'Water' and unit is 'Pieces', please select water size (Small or Large)."
            });
            return;
        }
        
        // Collect all items data
        const items = [];
        $('.item-row').each(function() {
            const unit = $(this).find('.item-unit').val();
            const finalUnit = unit === 'custom' ? ($(this).find('.item-custom-unit').val() || '').trim() : unit;
            const category = $(this).find('.item-category').val();
            const waterSize = $(this).find('.item-water-size').val();
            
            // Build item name with water size if applicable
            let itemName = ($(this).find('.item-name').val() || '').trim();
            // Remove any existing size suffix to avoid duplication
            itemName = itemName.replace(/\s*\(Small\)\s*$/i, '').replace(/\s*\(Large\)\s*$/i, '');
            
            if (category === 'water' && unit === 'pcs' && waterSize) {
                itemName = itemName + ' (' + waterSize.charAt(0).toUpperCase() + waterSize.slice(1) + ')';
            }
            
            items.push({
                item_name: itemName,
                category: category,
                quantity: $(this).find('.item-quantity').val(),
                unit: finalUnit,
                priority: $(this).find('.item-priority').val(),
                reason: ($(this).find('.item-reason').val() || '').trim(),
                water_size: (category === 'water' && unit === 'pcs') ? waterSize : null
            });
        });
        
        const formData = {
            _token: '{{ csrf_token() }}',
            items: items
        };
        
        $.ajax({
            url: '{{ route($storeRoute) }}',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: "success",
                        title: "Success!",
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    setTimeout(function() {
                        window.location.href = '{{ route($myRequestsRoute) }}';
                    }, 2000);
                }
            },
            error: function(xhr) {
                var errorMsg = xhr.responseJSON?.message || 'Failed to submit request.';
                Swal.fire({
                    icon: "error",
                    title: "Error!",
                    text: errorMsg
                });
            }
        });
    });
    
    // Initialize remove buttons visibility
    updateRemoveButtons();
    
    // Handle template selection
    $('#templateSelect').on('change', function() {
        const templateId = $(this).val();
        if (!templateId) {
            return;
        }
        
        const option = $(this).find('option:selected');
        let items = [];
        try {
            items = option.data('items');
            if (typeof items === 'string') {
                items = JSON.parse(items);
            }
        } catch (e) {
            console.error("Error parsing template items:", e);
            Swal.fire({
                icon: "error",
                title: "Error!",
                text: "Failed to load template items."
            });
            return;
        }
        
        if (!items || items.length === 0) {
            Swal.fire({
                icon: "error",
                title: "Error!",
                text: "Template has no items."
            });
            return;
        }
        
        Swal.fire({
            icon: "warning",
            title: "Load Template?",
            text: "This will clear any items you've already added and load items from the template. Proceed?",
            showCancelButton: true,
            confirmButtonColor: "#f39c12",
            confirmButtonText: "Yes, load it!",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                // Clear existing items
                $('#itemsContainer').empty();
                
                // Reset itemIndex to match template items
                itemIndex = items.length;
                
                // Add items from template
                items.forEach(function(item, index) {
                    addItemRow(item, index);
                });
                
                // Update item numbers and remove buttons
                updateItemNumbers();
                updateRemoveButtons();
                
                Swal.fire({
                    icon: "success",
                    title: "Template Loaded!",
                    text: "Template items have been loaded. You can edit quantities or add more items before submitting.",
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                // Reset select dropdown if cancelled
                $('#templateSelect').val('');
            }
        });
    });
    
    // Helper function to add item row from template
    function addItemRow(itemData, index) {
        // Handle unit logic - check if it's a standard unit
        const standardUnits = ['pcs', 'liters', 'ml', 'kg', 'g', 'boxes', 'bottles', 'rolls', 'packs', 'cartons', 'bags'];
        const isCustomUnit = itemData.unit && !standardUnits.includes(itemData.unit);
        
        const itemRow = `
            <div class="item-row mb-4" style="border: 1px solid #dee2e6; padding: 20px; border-radius: 8px; background: #f8f9fa;">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0"><i class="fa fa-cube"></i> Item <span class="item-number">${index + 1}</span></h5>
                <button type="button" class="btn btn-sm btn-danger remove-item-btn">
                  <i class="fa fa-trash"></i> Remove
                </button>
              </div>
              
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Item Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control item-name" name="items[${index}][item_name]" required value="${itemData.item_name || ''}">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Category</label>
                    <select class="form-control item-category" name="items[${index}][category]" onchange="toggleWaterSizeField(this)">
                      <option value="">Select Category</option>
                      @if($routePrefix === 'bar-keeper')
                        <optgroup label="Bar & Beverages">
                          <option value="non_alcoholic_beverage" ${itemData.category === 'non_alcoholic_beverage' ? 'selected' : ''}>Soda / Soft Drinks</option>
                          <option value="energy_drinks" ${itemData.category === 'energy_drinks' ? 'selected' : ''}>Energy Drinks</option>
                          <option value="juices" ${itemData.category === 'juices' ? 'selected' : ''}>Juices</option>
                          <option value="water" ${itemData.category === 'water' ? 'selected' : ''}>Water</option>
                          <option value="alcoholic_beverage" ${itemData.category === 'alcoholic_beverage' ? 'selected' : ''}>Beer / Cider</option>
                          <option value="wines" ${itemData.category === 'wines' ? 'selected' : ''}>Wines</option>
                          <option value="spirits" ${itemData.category === 'spirits' ? 'selected' : ''}>Spirits</option>
                          <option value="hot_beverages" ${itemData.category === 'hot_beverages' ? 'selected' : ''}>Hot Beverages</option>
                          <option value="cocktails" ${itemData.category === 'cocktails' ? 'selected' : ''}>Cocktails</option>
                        </optgroup>
                      @elseif($routePrefix === 'chef-master')
                        <optgroup label="Kitchen & Food">
                          <option value="meat_poultry" ${itemData.category === 'meat_poultry' ? 'selected' : ''}>Meat & Poultry</option>
                          <option value="seafood" ${itemData.category === 'seafood' ? 'selected' : ''}>Seafood & Fish</option>
                          <option value="vegetables" ${itemData.category === 'vegetables' ? 'selected' : ''}>Vegetables & Fruits</option>
                          <option value="dairy" ${itemData.category === 'dairy' ? 'selected' : ''}>Dairy & Eggs</option>
                          <option value="pantry_baking" ${itemData.category === 'pantry_baking' ? 'selected' : ''}>Pantry & Baking</option>
                          <option value="food" ${itemData.category === 'food' ? 'selected' : ''}>General Food</option>
                          <option value="oils_fats" ${itemData.category === 'oils_fats' ? 'selected' : ''}>Cooking Oil & Fats</option>
                        </optgroup>
                      @else
                        <option value="cleaning_supplies" ${itemData.category === 'cleaning_supplies' ? 'selected' : ''}>Cleaning Supplies</option>
                        <option value="linens" ${itemData.category === 'linens' ? 'selected' : ''}>Linens</option>
                      @endif
                      <option value="other" ${itemData.category === 'other' || !itemData.category ? 'selected' : ''}>Other</option>
                    </select>
                  </div>
                </div>
              </div>
              
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label>Unit <span class="text-danger">*</span></label>
                    <select class="form-control item-unit" name="items[${index}][unit]" required>
                      <option value="pcs" ${itemData.unit === 'pcs' ? 'selected' : ''}>Pieces (pcs)</option>
                      <option value="liters" ${itemData.unit === 'liters' ? 'selected' : ''}>Liters (L)</option>
                      <option value="ml" ${itemData.unit === 'ml' ? 'selected' : ''}>Milliliters (ml)</option>
                      <option value="kg" ${itemData.unit === 'kg' ? 'selected' : ''}>Kilograms (kg)</option>
                      <option value="g" ${itemData.unit === 'g' ? 'selected' : ''}>Grams (g)</option>
                      <option value="boxes" ${itemData.unit === 'boxes' ? 'selected' : ''}>Boxes</option>
                      <option value="bottles" ${itemData.unit === 'bottles' ? 'selected' : ''}>Bottles</option>
                      <option value="rolls" ${itemData.unit === 'rolls' ? 'selected' : ''}>Rolls</option>
                      <option value="packs" ${itemData.unit === 'packs' ? 'selected' : ''}>Packs</option>
                      <option value="cartons" ${itemData.unit === 'cartons' ? 'selected' : ''}>Cartons</option>
                      <option value="bags" ${itemData.unit === 'bags' ? 'selected' : ''}>Bags</option>
                      <option value="custom" ${isCustomUnit ? 'selected' : ''}>Custom Unit</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-3 custom-unit-field" style="display: ${isCustomUnit ? 'block' : 'none'};">
                  <div class="form-group">
                    <label>Specify Unit <span class="text-danger">*</span></label>
                    <input type="text" class="form-control item-custom-unit" name="items[${index}][custom_unit]" value="${isCustomUnit ? itemData.unit : ''}" ${isCustomUnit ? 'required' : ''} placeholder="e.g., gallons, ounces, etc.">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label>Quantity <span class="text-danger">*</span></label>
                    <input type="number" step="1" class="form-control item-quantity" name="items[${index}][quantity]" required min="1" value="${itemData.quantity || ''}">
                  </div>
                </div>
                <div class="col-md-3 water-size-field" style="display: ${itemData.category === 'water' && itemData.unit === 'pcs' ? 'block' : 'none'};">
                  <div class="form-group">
                    <label>Water Size</label>
                    <select class="form-control item-water-size" name="items[${index}][water_size]" ${itemData.category === 'water' && itemData.unit === 'pcs' ? 'required' : ''}>
                      <option value="">Select Size</option>
                      <option value="small" ${itemData.water_size === 'small' ? 'selected' : ''}>Small</option>
                      <option value="large" ${itemData.water_size === 'large' ? 'selected' : ''}>Large</option>
                    </select>
                  </div>
                </div>
              </div>
              
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Priority <span class="text-danger">*</span></label>
                    <select class="form-control item-priority" name="items[${index}][priority]" required>
                      <option value="low" ${itemData.priority === 'low' ? 'selected' : ''}>Low</option>
                      <option value="medium" ${itemData.priority === 'medium' ? 'selected' : ''}>Medium</option>
                      <option value="high" ${itemData.priority === 'high' ? 'selected' : ''}>High</option>
                      <option value="urgent" ${itemData.priority === 'urgent' ? 'selected' : ''}>Urgent</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Reason <span class="text-danger">*</span></label>
                    <textarea class="form-control item-reason" name="items[${index}][reason]" rows="2" required placeholder="Why do you need this item?">${itemData.reason || ''}</textarea>
                  </div>
                </div>
              </div>
            </div>
        `;
        
        $('#itemsContainer').append(itemRow);
    }
});
</script>
@endsection
