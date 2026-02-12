@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-pencil-square-o"></i> Record Purchases</h1>
        <p>Enter actual quantities and costs for: <strong>{{ $shoppingList->name }}</strong></p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.restaurants.shopping-list.index') }}">Shopping Lists</a></li>
        <li class="breadcrumb-item active"><a href="#">Record</a></li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <form action="{{ route('admin.restaurants.shopping-list.update-purchase', $shoppingList->id) }}" method="POST" id="purchaseForm">
            @csrf
            @method('PUT')
            
            <div class="tile">
                <div class="tile-title-w-btn">
                    <h3 class="title">Items List</h3>
                    <div class="btn-group">
                        <button class="btn btn-secondary" type="submit" name="save_draft" value="1"><i class="fa fa-save"></i> Save Draft</button>
                    </div>
                </div>
                
                <div class="tile-body">
                    <!-- Budget Section -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="budget_amount">Budget Amount (TZS)</label>
                                @php
                                    $defaultBudget = $shoppingList->budget_amount ?? $shoppingList->total_estimated_cost ?? $shoppingList->items->sum('estimated_price');
                                @endphp
                                <input type="number" step="0.01" class="form-control" id="budget_amount" name="budget_amount" value="{{ old('budget_amount', $defaultBudget) }}" placeholder="Enter budget">
                                <small class="form-text text-muted">Default: Total Estimated Cost ({{ number_format($shoppingList->items->sum('estimated_price'), 2) }} TZS)</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="alert alert-info mb-0">
                                <strong>Amount Used:</strong><br>
                                <span id="amount_used_display">0.00</span> TZS
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="alert {{ ($shoppingList->budget_amount ?? 0) > 0 && ($shoppingList->amount_used ?? 0) > ($shoppingList->budget_amount ?? 0) ? 'alert-danger' : 'alert-success' }} mb-0">
                                <strong>Amount Remaining:</strong><br>
                                <span id="amount_remaining_display">{{ number_format(($shoppingList->budget_amount ?? 0) - ($shoppingList->amount_used ?? 0), 2) }}</span> TZS
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="alert alert-warning mb-0">
                                <strong>Missing Items:</strong><br>
                                <span id="missing_items_count">0</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> 
                        <strong>Instructions:</strong> 
                        <ul class="mb-0">
                            <li>Check <strong>Found</strong> if the item was purchased, uncheck if missing</li>
                            <li>Enter <strong>Planned Amount</strong> (price per unit) or <strong>Market Price</strong> (total for all units)</li>
                            <li>If an item was skipped, uncheck "Found" and leave quantities as 0</li>
                        </ul>
                    </div>
                    
                    <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="text-center" width="5%">Found</th>
                                <th width="40%">Item Name</th>
                                <th class="text-center" width="10%">Plan</th>
                                <th class="text-center" width="15%">Bought</th>
                                <th class="text-center" width="10%">Unit</th>
                                <th class="text-right" width="15%">Total Cost</th>
                                <th class="text-center" width="15%">Expiry</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($shoppingList->items as $item)
                                <tr class="item-row {{ old("items.{$item->id}.is_found", $item->is_found ?? true) ? '' : 'is-missing' }}" data-item-id="{{ $item->id }}">
                                    <td class="text-center vertical-align-middle">
                                        <input type="checkbox" class="is-found-checkbox" name="items[{{ $item->id }}][is_found]" value="1" {{ old("items.{$item->id}.is_found", $item->is_found ?? true) ? 'checked' : '' }}>
                                    </td>
                                    <td class="vertical-align-middle">
                                        <div class="item-name-wrapper">
                                            <strong>{{ $item->product_name }}</strong><br>
                                            <span class="badge badge-light border">{{ $item->category_name }}</span>
                                        </div>
                                        @if($item->product_variant_id)
                                        <input type="hidden" name="items[{{ $item->id }}][product_variant_id]" value="{{ $item->product_variant_id }}">
                                        @endif
                                    </td>
                                    <td class="text-center vertical-align-middle planned-quantity">{{ number_format($item->quantity, 0) }}</td>
                                    <td class="vertical-align-middle">
                                        <input type="number" step="1" class="form-control text-center purchased-quantity" name="items[{{ $item->id }}][purchased_quantity]" value="{{ old("items.{$item->id}.purchased_quantity", round($item->purchased_quantity ?? $item->quantity)) }}" min="0">
                                        <div class="qty-diff-indicator small text-center mt-1"></div>
                                    </td>
                                    <td class="text-center vertical-align-middle">{{ $item->unit == 'bottles' ? 'PIC' : $item->unit }}</td>
                                    <!-- Costing -->
                                    <td class="text-right vertical-align-middle" style="background-color: #f9f9f9;">
                                        <input type="hidden" class="planned-price" value="{{ $item->estimated_price }}">
                                        <input type="number" step="1" class="form-control text-right total-cost" name="items[{{ $item->id }}][purchased_cost]" value="{{ old("items.{$item->id}.purchased_cost", round($item->purchased_cost ?? ($item->estimated_price > 0 ? $item->estimated_price : 0))) }}" placeholder="Cost" min="0">
                                        <div class="cost-diff-indicator small text-right mt-1"></div>
                                        <small class="text-muted d-block mt-1">Unit Cost: <span class="unit-cost-display">-</span></small>
                                    </td>
                                    <td class="vertical-align-middle">
                                        <input type="date" class="form-control" name="items[{{ $item->id }}][expiry_date]" value="{{ old("items.{$item->id}.expiry_date", $item->expiry_date ? $item->expiry_date->format('Y-m-d') : '') }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-light">
                                <td colspan="5" class="text-right">
                                    <strong>TOTALS:</strong>
                                    <span class="text-muted ml-2">(Planned: {{ number_format($shoppingList->items->sum('estimated_price'), 0) }} TZS)</span>
                                </td>
                                <td colspan="2" class="text-right"><strong id="total_cost_display" class="text-primary" style="font-size: 16px;">0</strong> <small>TZS</small></td>
                            </tr>
                        </tfoot>
                    </table>
                    </div>
                </div>
                <div class="tile-footer">
                    <button class="btn btn-primary" type="button" id="finalizeBtn">
                        <i class="fa fa-check-circle"></i> Finalize & Update Stock
                    </button>
                    <a class="btn btn-secondary" href="{{ route('admin.restaurants.shopping-list.index') }}"><i class="fa fa-times-circle"></i> Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<script>
$(document).ready(function() {
    function calculateTotals() {
        var totalCost = 0;
        var budgetAmount = parseFloat($('#budget_amount').val()) || 0;
        var missingItemsCount = 0;
        
        $('.item-row').each(function() {
            var isFound = $(this).find('.is-found-checkbox').is(':checked');
            var totalCostInput = $(this).find('.total-cost');
            var totalCostRaw = totalCostInput.val().trim();
            var totalCostValue = totalCostRaw === '' ? 0 : parseFloat(totalCostRaw) || 0;
            
            // Count missing items (not found)
            if (!isFound) {
                missingItemsCount++;
            }
            
            if (isFound && totalCostValue > 0) {
                totalCost += totalCostValue;
            }
        });
        
        $('#total_cost_display').text(Math.round(totalCost).toLocaleString());
        $('#amount_used_display').text(Math.round(totalCost).toLocaleString());
        $('#missing_items_count').text(missingItemsCount);
        
        var amountRemaining = budgetAmount - totalCost;
        $('#amount_remaining_display').text(Math.round(amountRemaining).toLocaleString());
        
        // Change color based on budget
        var remainingAlert = $('#amount_remaining_display').closest('.alert');
        if (budgetAmount > 0) {
            if (amountRemaining < 0) {
                remainingAlert.removeClass('alert-success').addClass('alert-danger');
            } else {
                remainingAlert.removeClass('alert-danger').addClass('alert-success');
            }
        }
    }
    
    // Style missing items logic
    function styleMissingRows() {
        $('.item-row').each(function() {
            var isFound = $(this).find('.is-found-checkbox').is(':checked');
            if (isFound) {
                $(this).removeClass('is-missing');
                $(this).find('input').prop('disabled', false);
            } else {
                $(this).addClass('is-missing');
                $(this).find('.purchased-quantity, .total-cost').prop('disabled', true).val(0);
            }
        });
    }

    // Difference Tracking Logic
    function trackDifferences() {
        $('.item-row').each(function() {
            var row = $(this);
            if (row.hasClass('is-missing')) {
                row.find('.qty-diff-indicator').html('<span class="text-danger">MISSING ✕</span>');
                row.find('.cost-diff-indicator').hide();
                return;
            }

            // Qty Diff
            var planQty = parseFloat(row.find('.planned-quantity').text()) || 0;
            var boughtQty = parseFloat(row.find('.purchased-quantity').val()) || 0;
            var qtyIndicator = row.find('.qty-diff-indicator');

            if (boughtQty > planQty) {
                qtyIndicator.html('<span class="text-success">+' + (boughtQty - planQty).toFixed(1) + ' Extra</span>').show();
            } else if (boughtQty < planQty && boughtQty > 0) {
                qtyIndicator.html('<span class="text-warning">-' + (planQty - boughtQty).toFixed(1) + ' Less</span>').show();
            } else {
                qtyIndicator.hide();
            }

            // Cost Diff
            var planPrice = parseFloat(row.find('.planned-price').val()) || 0;
            var totalCost = parseFloat(row.find('.total-cost').val()) || 0;
            var costIndicator = row.find('.cost-diff-indicator');

            if (totalCost > 0 && planPrice > 0) {
                var diff = totalCost - planPrice;
                if (diff > 0) {
                    costIndicator.html('<span class="text-danger">+' + diff.toLocaleString() + ' Over</span>').show();
                } else if (diff < 0) {
                    costIndicator.html('<span class="text-success">-' + Math.abs(diff).toLocaleString() + ' Saved</span>').show();
                } else {
                    costIndicator.hide();
                }
            } else {
                costIndicator.hide();
            }
        });
    }
    
    // Calculate on input changes
    $(document).on('input', '.purchased-quantity, .total-cost, #budget_amount', function() {
        calculateTotals();
        trackDifferences();
    });
    
    $(document).on('change', '.is-found-checkbox', function() {
        styleMissingRows();
        calculateTotals();
        trackDifferences();
    });
    
    // Initial calculation
    styleMissingRows();
    calculateTotals();
    trackDifferences();

    // Bind calculation to inputs
    $(document).on('input', '.purchased-quantity, .total-cost', function() {
        var row = $(this).closest('.item-row');
        var qty = parseFloat(row.find('.purchased-quantity').val()) || 0;
        var cost = parseFloat(row.find('.total-cost').val()) || 0;
        
        if (qty > 0 && cost > 0) {
            var unitCost = cost / qty;
            row.find('.unit-cost-display').text(unitCost.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0}));
        } else {
            row.find('.unit-cost-display').text('-');
        }
    });

    // Finalize confirmation with SweetAlert
    $('#finalizeBtn').on('click', function(e) {
        e.preventDefault();
        
        // Before submitting, we need to re-enable disabled inputs so they are sent in request (if needed by backend)
        // Actually, we want them sent as 0 for is_found=false
        $('.item-row.is-missing').find('input').prop('disabled', false);

        swal({
            title: "Are you sure?",
            text: "This will mark the list as completed and update stock levels.",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, finalize it!",
            cancelButtonText: "No, cancel!",
            closeOnConfirm: false,
            closeOnCancel: true
        }, function(isConfirm) {
            if (isConfirm) {
                // Add hidden input for finalize
                $('<input>').attr({
                    type: 'hidden',
                    name: 'finalize',
                    value: '1'
                }).appendTo('#purchaseForm');
                $('#purchaseForm').submit();
            } else {
                // Re-style if cancelled
                styleMissingRows();
            }
        });
    });
});
</script>
<style>
    .item-row.is-missing {
        background-color: #fff5f5 !important;
        opacity: 0.8;
    }
    .item-row.is-missing .item-name-wrapper {
        text-decoration: line-through;
        color: #dc3545;
    }
    .status-missed { color: #dc3545; }
    .status-bought { color: #28a745; }
    .status-partial { color: #ffc107; }
</style>
@endsection
