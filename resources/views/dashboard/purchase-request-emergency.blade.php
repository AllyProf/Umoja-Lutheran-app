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
    } elseif (str_contains($currentRouteName, 'admin.')) {
      $routePrefix = 'admin';
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
      <h1><i class="fa fa-exclamation-triangle text-danger"></i> Emergency Purchase Request</h1>
      <p>Submit an urgent request for unregistered items</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
      <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
      <li class="breadcrumb-item"><a href="{{ route($dashboardRoute) }}">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="#">Emergency Purchase</a></li>
    </ul>
  </div>

  <div class="row mb-3">
    <div class="col-md-12">
      <div class="tile">
        <div class="tile-title-w-btn mb-3">
          <h3 class="title"><i class="fa fa-plus-circle"></i> New Emergency Request</h3>
        </div>
        <div class="tile-body">
          <div class="alert alert-warning">
            <i class="fa fa-warning"></i> <strong>Note:</strong> Emergency requests are sent directly to Managers via SMS
            and bypass standard scheduled deadlines. Use this only for critical items that are out of stock and urgently
            needed.
          </div>
          <form id="emergencyPurchaseForm">
            <div id="itemsContainer">
              @if(isset($preFilledItems) && count($preFilledItems) > 0)
                @foreach($preFilledItems as $index => $preItem)
                  <div class="item-row mb-4"
                    style="border: 1px solid #dee2e6; padding: 20px; border-radius: 8px; background: #fdfdfd; border-left: 5px solid #e77a31;">
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
                          <input type="text" class="form-control item-name" name="items[{{ $index }}][item_name]"
                            value="{{ $preItem['item_name'] }}" required>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label>Category</label>
                          <select class="form-control item-category" name="items[{{ $index }}][category]">
                            <option value="">Select Category</option>
                            @if($routePrefix === 'admin' || $routePrefix === 'chef-master')
                              <optgroup label="Food & Kitchen">
                                <option value="meat_poultry" {{ $preItem['category'] == 'meat_poultry' ? 'selected' : '' }}>Meat &
                                  Poultry</option>
                                <option value="seafood" {{ $preItem['category'] == 'seafood' ? 'selected' : '' }}>Seafood & Fish
                                </option>
                                <option value="vegetables" {{ $preItem['category'] == 'vegetables' ? 'selected' : '' }}>Vegetables
                                  & Fruits</option>
                                <option value="dairy" {{ $preItem['category'] == 'dairy' ? 'selected' : '' }}>Dairy & Eggs
                                </option>
                                <option value="pantry_baking" {{ $preItem['category'] == 'pantry_baking' || $preItem['category'] == 'pantry' || $preItem['category'] == 'baking' ? 'selected' : '' }}>Pantry &
                                  Baking</option>
                                <option value="spices_herbs" {{ $preItem['category'] == 'spices_herbs' || $preItem['category'] == 'spices' || $preItem['category'] == 'sauces' ? 'selected' : '' }}>Spices,
                                  Herbs & Sauces</option>
                                <option value="bakery" {{ $preItem['category'] == 'bakery' ? 'selected' : '' }}>Bakery & Bread
                                </option>
                                <option value="oils_fats" {{ $preItem['category'] == 'oils_fats' ? 'selected' : '' }}>Cooking Oil
                                  & Fats</option>
                              </optgroup>
                            @endif
                            @if($routePrefix === 'admin' || $routePrefix === 'bar-keeper')
                              <optgroup label="Bar & Beverages">
                                <option value="non_alcoholic_beverage" {{ $preItem['category'] == 'non_alcoholic_beverage' || $preItem['category'] == 'drinks' || $preItem['category'] == 'juices' ? 'selected' : '' }}>Soda /
                                  Soft Drinks / Juices</option>
                                <option value="alcoholic_beverage" {{ $preItem['category'] == 'alcoholic_beverage' || $preItem['category'] == 'beer' ? 'selected' : '' }}>Beer / Cider</option>
                                <option value="spirits" {{ $preItem['category'] == 'spirits' ? 'selected' : '' }}>Spirits</option>
                                <option value="wines" {{ $preItem['category'] == 'wines' ? 'selected' : '' }}>Wines</option>
                                <option value="water" {{ $preItem['category'] == 'water' ? 'selected' : '' }}>Water</option>
                              </optgroup>
                            @endif
                            <optgroup label="Other">
                              <option value="cleaning_supplies" {{ $preItem['category'] == 'cleaning_supplies' ? 'selected' : '' }}>Cleaning Supplies</option>
                              <option value="linens" {{ $preItem['category'] == 'linens' ? 'selected' : '' }}>Linens /
                                Housekeeping</option>
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
                          <select class="form-control item-unit" name="items[{{ $index }}][unit]" required
                            onchange="toggleCustomUnit(this)">
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
                          <input type="text" class="form-control item-custom-unit" name="items[{{ $index }}][custom_unit]"
                            placeholder="e.g., gallons, ounces, etc.">
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <label>Quantity <span class="text-danger">*</span></label>
                          <input type="number" step="1" class="form-control item-quantity"
                            name="items[{{ $index }}][quantity]" value="{{ $preItem['quantity'] }}" required min="1">
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

                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label>Estimated Cost (Optional)</label>
                          <input type="number" step="0.01" class="form-control item-cost"
                            name="items[{{ $index }}][estimated_cost]" placeholder="e.g., 50000">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label>Reason <span class="text-danger">*</span></label>
                          <textarea class="form-control item-reason" name="items[{{ $index }}][reason]" rows="1" required
                            placeholder="Why is this an emergency?">Restocking low inventory</textarea>
                        </div>
                      </div>
                    </div>
                  </div>
                @endforeach
              @else
                <!-- First Item Row (Default) -->
                <div class="item-row mb-4"
                  style="border: 1px solid #dee2e6; padding: 20px; border-radius: 8px; background: #f8f9fa;">
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
                        <input type="text" class="form-control item-name" name="items[0][item_name]" required
                          placeholder="{{ $itemPlaceholder }}">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Category</label>
                        <select class="form-control item-category" name="items[0][category]"
                          onchange="toggleWaterSizeField(this)">
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
                        <select class="form-control item-unit" name="items[0][unit]" required
                          onchange="toggleCustomUnit(this)">
                          <option value="">Select Unit</option>
                          <option value="kg">Kilograms (kg)</option>
                          <option value="g">Grams (g)</option>
                          <option value="liters">Liters (L)</option>
                          <option value="ml">Milliliters (ml)</option>
                          <option value="pcs">Pieces (pcs)</option>
                          <option value="bottles">Bottles / PIC</option>
                          <option value="packs">Packs</option>
                          <option value="boxes">Boxes</option>
                          <option value="trays">Trays</option>
                          <option value="other">Other</option>
                          <option value="custom">Custom Unit</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <label>Quantity <span class="text-danger">*</span></label>
                        <input type="number" step="1" class="form-control item-quantity" name="items[0][quantity]" required
                          min="1">
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <label>Priority <span class="text-danger">*</span></label>
                        <select class="form-control item-priority" name="items[0][priority]" required readonly disabled>
                          <option value="urgent" selected>Urgent (Emergency)</option>
                        </select>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Estimated Cost (Optional)</label>
                        <input type="number" step="0.01" class="form-control item-cost" name="items[0][estimated_cost]"
                          placeholder="e.g., 50000">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Reason for Emergency <span class="text-danger">*</span></label>
                        <textarea class="form-control item-reason" name="items[0][reason]" rows="1" required
                          placeholder="Why is it an emergency?"></textarea>
                      </div>
                    </div>
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

            <div class="form-group mt-4">
              <button type="submit" class="btn btn-danger">
                <i class="fa fa-exclamation-triangle"></i> Submit Emergency Request
              </button>
              <a href="{{ route($myRequestsRoute) }}" class="btn btn-secondary">
                <i class="fa fa-list"></i> Cancel
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
    $(document).ready(function () {
      let itemIndex = 1;

      // Add new item row
      $('#addItemBtn').on('click', function () {
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
                          <option value="other">Other</option>
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
                        <select class="form-control item-priority" name="items[${itemIndex}][priority]" required readonly disabled>
                          <option value="urgent" selected>Urgent (Emergency)</option>
                        </select>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Estimated Cost (Optional)</label>
                        <input type="number" step="0.01" class="form-control item-cost" name="items[${itemIndex}][estimated_cost]" placeholder="e.g., 50000">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Reason for Emergency <span class="text-danger">*</span></label>
                        <textarea class="form-control item-reason" name="items[${itemIndex}][reason]" rows="1" required placeholder="Why is it an emergency?"></textarea>
                      </div>
                    </div>
                  </div>
                </div>
            `;

        $('#itemsContainer').append(newRow);
        itemIndex++;
        updateItemNumbers();
        updateRemoveButtons();
      });

      // Remove item row
      $(document).on('click', '.remove-item-btn', function () {
        $(this).closest('.item-row').remove();
        updateItemNumbers();
        updateRemoveButtons();
      });

      // Update item numbers
      function updateItemNumbers() {
        $('.item-row').each(function (index) {
          $(this).find('.item-number').text(index + 1);
          // Update all input names with new index
          const newIndex = index;
          $(this).find('input, select, textarea').each(function () {
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
      $(document).on('change', '.item-unit', function () {
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
      $(document).on('change', '.item-category', function () {
        toggleWaterSizeField(this);
      });

      // Form submission
      $('#purchaseRequestForm').on('submit', function (e) {
        e.preventDefault();

        // Validate all items
        let isValid = true;
        $('.item-row').each(function () {
          const itemName = ($(this).find('.item-name').val() || '').trim();
          const quantity = $(this).find('.item-quantity').val();
          const unit = $(this).find('.item-unit').val();
          const customUnit = ($(this).find('.item-custom-unit').val() || '').trim();
          const category = $(this).find('.item-category').val();
          const waterSize = $(this).find('.item-water-size').val();
          const reason = ($(this).find('.item-reason').val() || '').trim();
          const priority = 'urgent'; // always urgent

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
        $('.item-row').each(function () {
          const unit = $(this).find('.item-unit').val();
          const finalUnit = unit === 'custom' ? ($(this).find('.item-custom-unit').val() || '').trim() : unit;
          const category = $(this).find('.item-category').val();
          const waterSize = $(this).find('.item-water-size').val();
          const estimatedCost = $(this).find('.item-cost').val() || null;

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
            priority: 'urgent',
            reason: ($(this).find('.item-reason').val() || '').trim(),
            estimated_cost: estimatedCost,
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
          success: function (response) {
            if (response.success) {
              Swal.fire({
                icon: "success",
                title: "Success!",
                text: response.message,
                timer: 2000,
                showConfirmButton: false
              });
              setTimeout(function () {
                window.location.href = '{{ route($myRequestsRoute) }}';
              }, 2000);
            }
          },
          error: function (xhr) {
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
    });
  </script>
@endsection
