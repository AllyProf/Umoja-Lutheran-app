@extends('dashboard.layouts.app')

@section('content')
    <div class="app-title">
        <div>
            <h1><i class="fa fa-pencil-square-o"></i> Record Purchases</h1>
            <p>Enter actual quantities and costs for: <strong>{{ $shoppingList->name }}</strong></p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="{{ Auth::guard('staff')->user()->role === 'accountant' ? route('accountant.shopping-lists') : route('admin.restaurants.shopping-list.index') }}">Shopping Lists</a>
            </li>
            <li class="breadcrumb-item active"><a href="#">Record</a></li>
        </ul>
    </div>

    @php
        $role = strtolower(Auth::guard('staff')->user()->role ?? '');
        $isAccountant = $role === 'accountant';
        
        $indexRoute = $isAccountant 
            ? route('accountant.shopping-lists') 
            : route('admin.restaurants.shopping-list.index');
            
        $updateRoute = $isAccountant
            ? route('accountant.shopping-list.update-purchase', $shoppingList->id)
            : route('admin.restaurants.shopping-list.update-purchase', $shoppingList->id);
    @endphp

    <div class="row">
        <div class="col-md-12">
            <form action="{{ $updateRoute }}" method="POST"
                id="purchaseForm">
                @csrf
                @method('PUT')

                <div class="tile">
                    <div class="tile-title-w-btn">
                        <h3 class="title">Items List</h3>
                        <div class="btn-group">
                            <button class="btn btn-secondary" type="submit" name="save_draft" value="1"><i
                                    class="fa fa-save"></i> Save Draft</button>
                        </div>
                    </div>

                    <div class="tile-body">
                        <!-- Budget Section -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="market_name">Market Name / Supplier</label>
                                    <input type="text" class="form-control" id="market_name" name="market_name"
                                        value="{{ old('market_name', $shoppingList->market_name) }}"
                                        placeholder="e.g. City Market">
                                    <small class="form-text text-muted">Confirm where you purchased these items.</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="budget_amount">Budget Amount (TZS)</label>
                                    @php
                                        $defaultBudget = $shoppingList->budget_amount ?? $shoppingList->total_estimated_cost ?? $shoppingList->items->sum('estimated_price');
                                    @endphp
                                    <input type="number" step="0.01" class="form-control" id="budget_amount"
                                        name="budget_amount" value="{{ old('budget_amount', $defaultBudget) }}"
                                        placeholder="Enter budget">
                                    <small class="form-text text-muted">Approved Budget:
                                        {{ number_format($shoppingList->budget_amount, 2) }} TZS</small>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="alert alert-info mb-0">
                                    <strong>Used:</strong><br>
                                    <span id="amount_used_display">0</span>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="alert alert-warning mb-0">
                                    <strong>Missing:</strong><br>
                                    <span id="missing_items_count">0</span>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i>
                            <strong>Instructions:</strong>
                            <ul class="mb-0">
                                <li>Check <strong>Found</strong> if the item was purchased, uncheck if missing</li>
                                <li>Enter the <strong>Unit Price</strong> (per item) OR the <strong>Total Cost</strong>. The other will calculate automatically.</li>
                                <li>For fruits (Passion, Oranges, etc.), you can leave "Bought" as 1 and just enter the **Total Cost** and **Measured (KG)**.</li>
                                <li>If an item was skipped, uncheck "Found" and leave quantities as 0</li>
                            </ul>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="5%">Found</th>
                                        <th width="30%">Item Name</th>
                                        <th class="text-center" width="8%">Plan</th>
                                        <th class="text-center" width="10%">Bought</th>
                                        <th class="text-center" width="10%">Unit</th>
                                        <th class="text-right" width="12%">Unit Price</th>
                                        <th class="text-right" width="15%">Total Cost</th>
                                        <th class="text-center" width="12%">Measured (KG)</th>
                                        <th class="text-center" width="10%">Expiry</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($shoppingList->items as $item)
                                        <tr class="item-row {{ old("items.{$item->id}.is_found", $item->is_found ?? true) ? '' : 'is-missing' }}"
                                            data-item-id="{{ $item->id }}">
                                            <td class="text-center vertical-align-middle">
                                                <input type="checkbox" class="is-found-checkbox"
                                                    name="items[{{ $item->id }}][is_found]" value="1" {{ old("items.{$item->id}.is_found", $item->is_found ?? true) ? 'checked' : '' }}>
                                            </td>
                                            <td class="vertical-align-middle">
                                                <div class="item-name-wrapper">
                                                    @php 
                                                        $productName = $item->product->name ?? null;
                                                        $requestName = $item->product_name;
                                                        $variantName = $item->productVariant->variant_name ?? null;
                                                    @endphp
                                                    
                                                    <strong>{{ $requestName }}</strong>
                                                    @if($productName && strtolower($productName) !== strtolower($requestName))
                                                        <br><small class="text-success" style="font-weight: bold;">Ingredient: {{ $productName }}</small>
                                                    @endif
                                                    
                                                    @if($variantName && strtolower($variantName) !== 'standard' && !str_contains(strtolower($requestName), strtolower($variantName)) && ($productName ? !str_contains(strtolower($productName), strtolower($variantName)) : true))
                                                        <br><small class="text-info">{{ $variantName }}</small>
                                                    @endif
                                                    <br>
                                                    <span class="badge badge-light border">{{ $item->category_name }}</span>
                                                    
                                                    @php $pInfo = $item->purchasing_info; @endphp
                                                    @if($pInfo['has_package'] && !in_array($item->category, ['meat_poultry', 'seafood', 'vegetables', 'dairy', 'food']))
                                                        <div class="text-info small mt-1">
                                                            <i class="fa fa-shopping-basket"></i> 
                                                            Buying Package: <strong>{{ $pInfo['purchasing_unit'] }}</strong> 
                                                            <span class="text-muted">(1 {{ $pInfo['purchasing_unit'] }} = {{ $pInfo['ratio'] }} {{ $pInfo['receiving_unit'] }})</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                @if($item->product_variant_id)
                                                    <input type="hidden" name="items[{{ $item->id }}][product_variant_id]"
                                                        value="{{ $item->product_variant_id }}">
                                                @endif
                                            </td>
                                            <td class="text-center vertical-align-middle planned-quantity">
                                                {{ number_format($item->quantity, 0) }}</td>
                                            <td class="vertical-align-middle">
                                                @php $pInfo = $item->purchasing_info; @endphp
                                                @if($pInfo['has_package'])
                                                    <div class="input-group">
                                                        <input type="number" step="0.1"
                                                            class="form-control text-center package-quantity"
                                                            placeholder="{{ $pInfo['purchasing_unit'] }}"
                                                            data-ratio="{{ $pInfo['ratio'] }}"
                                                            value="{{ $item->purchased_quantity > 0 ? round($item->purchased_quantity / $pInfo['ratio'], 1) : '' }}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">{{ $pInfo['purchasing_unit'] }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="text-center mt-1">
                                                        <small class="text-muted">=</small>
                                                        <input type="number" step="0.1"
                                                            class="form-control form-control-sm d-inline-block text-center purchased-quantity"
                                                            name="items[{{ $item->id }}][purchased_quantity]"
                                                            style="width: 70px;"
                                                            value="{{ old("items.{$item->id}.purchased_quantity", round($item->purchased_quantity ?? $item->quantity)) }}"
                                                            min="0">
                                                        <small class="text-muted">{{ $item->unit }}</small>
                                                    </div>
                                                @else
                                                    <input type="number" step="0.1"
                                                        class="form-control text-center purchased-quantity"
                                                        name="items[{{ $item->id }}][purchased_quantity]"
                                                        value="{{ old("items.{$item->id}.purchased_quantity", round($item->purchased_quantity ?? $item->quantity)) }}"
                                                        min="0">
                                                @endif
                                            </td>
                                            <td class="text-center vertical-align-middle">
                                                <span class="badge badge-secondary">{{ $item->unit == 'bottles' ? 'PIC' : $item->unit }}</span>
                                            </td>
                                            
                                            <!-- Costing -->
                                            <td class="vertical-align-middle">
                                                @if($pInfo['has_package'])
                                                    <div class="input-group mb-1">
                                                        <input type="number" step="1" class="form-control text-right package-price"
                                                            placeholder="Price/{{ $pInfo['purchasing_unit'] }}"
                                                            value="{{ ($item->purchased_quantity > 0 && $pInfo['ratio'] > 0) ? round(($item->purchased_cost / $item->purchased_quantity) * $pInfo['ratio']) : '' }}">
                                                    </div>
                                                    <div class="text-right">
                                                        <small class="text-muted">Unit ({{ $item->unit }}):</small>
                                                        <input type="number" step="1" class="form-control form-control-sm d-inline-block text-right unit-price"
                                                            style="width: 80px;"
                                                            value="{{ $item->purchased_quantity > 0 ? round($item->purchased_cost / $item->purchased_quantity) : round($item->estimated_price / ($item->quantity > 0 ? $item->quantity : 1)) }}"
                                                            placeholder="Price/{{ $item->unit }}">
                                                    </div>
                                                @else
                                                    <input type="number" step="1" class="form-control text-right unit-price"
                                                        value="{{ $item->purchased_quantity > 0 ? round($item->purchased_cost / $item->purchased_quantity) : round($item->estimated_price / ($item->quantity > 0 ? $item->quantity : 1)) }}"
                                                        placeholder="Price">
                                                @endif
                                            </td>
                                            <td class="vertical-align-middle" style="background-color: #f9f9f9;">
                                                <input type="hidden" class="planned-price" value="{{ $item->estimated_price }}">
                                                <input type="number" step="1" class="form-control text-right total-cost"
                                                    name="items[{{ $item->id }}][purchased_cost]"
                                                    value="{{ old("items.{$item->id}.purchased_cost", round($item->purchased_cost ?? ($item->estimated_price > 0 ? $item->estimated_price : 0))) }}"
                                                    placeholder="Total" min="0">
                                            </td>

                                            <td class="vertical-align-middle text-center">
                                                @if(in_array($item->category, ['meat_poultry', 'seafood', 'vegetables', 'dairy', 'food']))
                                                    <div class="input-group input-group-sm">
                                                        <input type="number" step="0.01" class="form-control text-center font-weight-bold received-quantity-kg"
                                                            name="items[{{ $item->id }}][received_quantity_kg]"
                                                            style="border: 1px solid #940000; color: #940000;"
                                                            value="{{ old("items.{$item->id}.received_quantity_kg", $item->received_quantity_kg > 0 ? $item->received_quantity_kg : '') }}"
                                                            placeholder="0.00">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text bg-white" style="border: 1px solid #940000; border-left: none; color: #940000; font-weight: 700;">KG</span>
                                                        </div>
                                                    </div>
                                                    <!-- Real-time price per KG calculator -->
                                                    <div class="text-center mt-1 kg-price-calc" style="font-size: 11px; display: none; line-height: 1;">
                                                        <span class="text-danger font-weight-bold">1 KG = <span class="kg-price-val">0</span> TSh</span>
                                                    </div>
                                                @else
                                                    <span class="text-muted small">-</span>
                                                @endif
                                            </td>
                                            
                                            <td class="vertical-align-middle">
                                                <input type="date" class="form-control"
                                                    name="items[{{ $item->id }}][expiry_date]"
                                                    value="{{ old("items.{$item->id}.expiry_date", $item->expiry_date ? $item->expiry_date->format('Y-m-d') : '') }}">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light">
                                        <td colspan="6" class="text-right">
                                            <strong>TOTALS:</strong>
                                            <span class="text-muted ml-2">(Budget:
                                                {{ number_format($shoppingList->budget_amount, 0) }}
                                                TZS)</span>
                                        </td>
                                        <td colspan="2" class="text-right"><strong id="total_cost_display"
                                                class="text-primary" style="font-size: 16px;">0</strong> <small>TZS</small>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="tile-footer">
                        <button class="btn btn-primary" type="button" id="finalizeBtn">
                            <i class="fa fa-paper-plane"></i> Submit for Verification
                        </button>
                        <a class="btn btn-secondary" href="{{ $indexRoute }}"><i
                                class="fa fa-times-circle"></i> Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            function calculateTotals() {
                var totalCost = 0;
                var budgetAmount = parseFloat($('#budget_amount').val()) || 0;
                var missingItemsCount = 0;

                $('.item-row').each(function () {
                    var isFound = $(this).find('.is-found-checkbox').is(':checked');
                    var totalCostInput = $(this).find('.total-cost');
                    var totalCostValue = parseClean(totalCostInput.val());

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
                $('.item-row').each(function () {
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
                $('.item-row').each(function () {
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
                });
            }

            // Calculate on input changes
            $(document).on('input', '.purchased-quantity, .total-cost, #budget_amount', function () {
                calculateTotals();
                trackDifferences();
            });

            $(document).on('change', '.is-found-checkbox', function () {
                styleMissingRows();
                calculateTotals();
                trackDifferences();
            });

            // Initial calculation
            styleMissingRows();
            calculateTotals();
            trackDifferences();

            // Helper for parsing clean numbers in JS
            function parseClean(val) {
                if (!val) return 0;
                return parseFloat(val.toString().replace(/,/g, '')) || 0;
            }

            // Unit Price and Total Cost Sync
            $(document).on('input keyup change', '.unit-price', function () {
                var row = $(this).closest('.item-row');
                var qty = parseClean(row.find('.purchased-quantity').val());
                var unitPrice = parseClean($(this).val());
                
                if (qty > 0) {
                    var totalCost = qty * unitPrice;
                    row.find('.total-cost').val(Math.round(totalCost));
                }
                syncBackToPackage(row);
                calculateTotals();
                trackDifferences();
            });

            $(document).on('input keyup change', '.total-cost', function () {
                var row = $(this).closest('.item-row');
                var qty = parseClean(row.find('.purchased-quantity').val());
                var totalCost = parseClean($(this).val());
                
                if (qty > 0) {
                    var unitPrice = totalCost / qty;
                    row.find('.unit-price').val(Math.round(unitPrice));
                }
                syncBackToPackage(row);
                calculateTotals();
                trackDifferences();
            });

            $(document).on('input keyup change', '.purchased-quantity', function () {
                var row = $(this).closest('.item-row');
                var qty = parseClean($(this).val());
                var unitPrice = parseClean(row.find('.unit-price').val());
                
                if (qty > 0) {
                    var totalCost = qty * unitPrice;
                    row.find('.total-cost').val(Math.round(totalCost));
                }
                syncBackToPackageQty(row, qty);
                calculateTotals();
                trackDifferences();
            });

            // --- PACKAGE-BASED ENTRY SYNC ---
            
            // 1. Package Quantity -> Kg Quantity
            $(document).on('input keyup change', '.package-quantity', function () {
                var row = $(this).closest('.item-row');
                var pkgQty = parseClean($(this).val());
                var ratio = parseFloat(row.find('.package-quantity').data('ratio')) || 1;
                var kgQty = pkgQty * ratio;
                
                row.find('.purchased-quantity').val(kgQty % 1 === 0 ? kgQty : kgQty.toFixed(1));
                
                // Trigger recalculation of price/total
                updateCostsFromQty(row);
            });

            // 2. Package Price -> Unit Price (Kg) & Total Cost
            $(document).on('input keyup change', '.package-price', function () {
                var row = $(this).closest('.item-row');
                var pkgQty = parseClean(row.find('.package-quantity').val());
                var pkgPrice = parseClean($(this).val());
                var ratio = parseFloat(row.find('.package-quantity').data('ratio')) || 1;
                
                // Unit Price (Kg) = Pkg Price / Ratio
                var unitPrice = pkgPrice / ratio;
                row.find('.unit-price').val(Math.round(unitPrice));
                
                // Total Cost = Pkg Qty * Pkg Price
                if (pkgQty > 0) {
                    var totalCost = pkgQty * pkgPrice;
                    row.find('.total-cost').val(Math.round(totalCost));
                } else {
                    // Fallback to Kg Qty if Pkg Qty is empty
                    var kgQty = parseClean(row.find('.purchased-quantity').val());
                    row.find('.total-cost').val(Math.round(kgQty * unitPrice));
                }
                
                calculateTotals();
                trackDifferences();
            });

            function updateCostsFromQty(row) {
                var qty = parseClean(row.find('.purchased-quantity').val());
                var unitPrice = parseClean(row.find('.unit-price').val());
                if (qty > 0) {
                   var totalCost = qty * unitPrice;
                   row.find('.total-cost').val(Math.round(totalCost));
                }
                calculateTotals();
                trackDifferences();
            }

            function syncBackToPackage(row) {
                var pkgInput = row.find('.package-price');
                if (!pkgInput.length) return;
                var ratio = parseFloat(row.find('.package-quantity').data('ratio'));
                var unitPrice = parseClean(row.find('.unit-price').val());
                pkgInput.val(Math.round(unitPrice * ratio));
            }

            function syncBackToPackageQty(row, kgQty) {
                var pkgInput = row.find('.package-quantity');
                if (!pkgInput.length) return;
                var ratio = parseFloat(pkgInput.data('ratio'));
                if (ratio > 0) {
                    var pkgQty = kgQty / ratio;
                    pkgInput.val(pkgQty % 1 === 0 ? pkgQty : pkgQty.toFixed(1));
                }
            }

            // --- KG PRICE CALCULATION ---
            function updateKgPrice(row) {
                var kgQty = parseClean(row.find('.received-quantity-kg').val());
                var totalCost = parseClean(row.find('.total-cost').val());
                var calcDiv = row.find('.kg-price-calc');
                var valSpan = row.find('.kg-price-val');
                var unitPriceInput = row.find('.unit-price');

                if (kgQty > 0 && totalCost > 0) {
                    var kgPrice = totalCost / kgQty;
                    valSpan.text(Math.round(kgPrice).toLocaleString());
                    calcDiv.show();
                    
                    // Also update the unit price field if it's a food item
                    // to ensure historical data is saved per KG
                    unitPriceInput.val(Math.round(kgPrice));
                } else {
                    calcDiv.hide();
                }
            }

            // Sync KG Price on relevant inputs
            $(document).on('input keyup change', '.received-quantity-kg, .total-cost', function () {
                var row = $(this).closest('.item-row');
                updateKgPrice(row);
            });

            // Run once for existing values
            $('.item-row').each(function() {
                updateKgPrice($(this));
            });

            // Finalize confirmation with SweetAlert
            $('#finalizeBtn').on('click', function (e) {
                e.preventDefault();

                // Before submitting, we need to re-enable disabled inputs so they are sent in request
                $('.item-row.is-missing').find('input').prop('disabled', false);

                swal({
                    title: "Submit for Verification?",
                    text: "This will send the actual purchase costs to the Accountant for final verification.",
                    type: "info",
                    showCancelButton: true,
                    confirmButtonText: "Yes, submit it!",
                    cancelButtonText: "No, cancel!",
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true
                }, function (isConfirm) {
                    if (isConfirm) {
                        const form = $('#purchaseForm');
                        const formData = new FormData(form[0]);
                        formData.append('finalize', '1');

                        fetch(form.attr('action'), {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': $('input[name="_token"]').val()
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                swal({
                                    title: "Success!",
                                    text: data.message,
                                    type: "success",
                                    timer: 1000,
                                    showConfirmButton: false
                                });
                                setTimeout(() => {
                                    window.location.href = data.redirect_url || '{{ $indexRoute }}';
                                }, 1000);
                            } else {
                                swal("Error", data.message || "An error occurred", "error");
                                styleMissingRows();
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            swal("Error", "An unexpected error occurred", "error");
                            styleMissingRows();
                        });
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

        .status-missed {
            color: #dc3545;
        }

        .status-bought {
            color: #28a745;
        }

        .status-partial {
            color: #ffc107;
        }
    </style>
@endsection