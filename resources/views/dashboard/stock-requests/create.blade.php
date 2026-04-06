@extends('dashboard.layouts.app')

@php
    $userRole = strtolower(trim(Auth::guard('staff')->user()->role ?? ''));
    $isBarKeeper = in_array($userRole, ['bar_keeper', 'bar keeper', 'bartender']);

    $requestType = $isBarKeeper ? 'Counter' : 'Beverage';
    $itemType = $isBarKeeper ? 'Item / Product' : 'Beverage';
    $cardItemLabel = $isBarKeeper ? 'Stock' : 'Beverage';

    if ($isChef) {
        $requestType = 'Internal';
        $cardItemLabel = 'Kitchen';
        $itemType = 'Ingredient / Product';
    } elseif (isset($isHousekeeper) && $isHousekeeper) {
        $requestType = 'Housekeeping';
        $cardItemLabel = 'Housekeeping';
        $itemType = 'Housekeeping Item';
    }
@endphp

@section('title', 'New ' . $requestType . ' Stock Request')

@section('content')
    <div class="container-fluid">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h4 class="text-themecolor">New {{ $requestType }} Stock Request</h4>
            </div>
            <div class="col-md-7 align-self-center text-end">
                <ol class="breadcrumb justify-content-end">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('stock-requests.index') }}">Requests</a></li>
                    <li class="breadcrumb-item active">New</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-9 col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title mb-0">Request {{ $cardItemLabel }} Items</h4>
                            <button type="button" class="btn btn-outline-success btn-sm" id="addRowBtn">
                                <i class="fa fa-plus"></i> Add Item
                            </button>
                        </div>

                        @if(session('error_list'))
                            <div class="alert alert-danger shadow-sm border-0 mb-4" style="border-left: 5px solid #d9534f;">
                                <h4 class="text-danger"><i class="fa fa-exclamation-triangle"></i> Insufficient Stock</h4>
                                <ul class="mb-0">
                                    @foreach(session('error_list') as $error)
                                        <li>{!! $error !!}</li>
                                    @endforeach
                                </ul>
                                <hr>
                                <p class="mb-0 small text-dark">Please adjust your requested quantities or contact the
                                    storekeeper to check inventory levels.</p>
                            </div>
                        @endif

                        <form action="{{ route('stock-requests.store') }}" method="POST" id="stockRequestForm">
                            @csrf

                            <div class="table-responsive mb-3">
                                <table class="table table-bordered align-middle" id="itemsTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="min-width:260px;"># &nbsp;
                                                {{ $itemType }}
                                            </th>
                                            <th style="min-width:110px;">Quantity</th>
                                            <th style="min-width:140px;">Unit</th>
                                            <th class="text-right" style="min-width:100px;">Unit Cost</th>
                                            @if(!$isChef && !(isset($isHousekeeper) && $isHousekeeper))
                                                <th class="text-right" style="min-width:120px;">Est. Revenue</th>
                                                <th class="text-right" style="min-width:120px;">Est. Profit</th>
                                            @endif
                                            <th class="text-right" style="min-width:120px;">Total Cost</th>
                                            <th style="width:50px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsBody">
                                        @include('dashboard.stock-requests._item_row', ['index' => 0, 'products' => $products, 'isChef' => $isChef, 'isHousekeeper' => $isHousekeeper])
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-light">
                                            <td colspan="4" class="text-right font-weight-bold">GRAND TOTAL:</td>
                                            <td class="text-right font-weight-bold">
                                                <span id="grandTotalDisplay">0.00</span> TSh
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <div class="row text-center">
                                                <div class="col-md-4">
                                                    <h6 class="text-muted">Total Items</h6>
                                                    <h4 id="grand-total-qty">0.00</h4>
                                                </div>
                                                <div class="col-md-4">
                                                    <h6 class="text-muted">Estimated Total Cost</h6>
                                                    <h4 id="grand-total-cost">0.00</h4>
                                                </div>
                                                @if(!$isChef && !(isset($isHousekeeper) && $isHousekeeper))
                                                    <div class="col-md-4">
                                                        <h6 class="text-muted">Estimated Total Profit</h6>
                                                        <h4 id="grand-total-profit" class="text-success">0.00</h4>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions mt-4">
                                <label class="font-weight-bold">Notes <span class="text-muted font-weight-normal">(Optional
                                        — applies to all items)</span></label>
                                <textarea name="notes" class="form-control" rows="2"
                                    placeholder="Additional details or reasons for request..."></textarea>
                            </div>

                            <div class="form-group m-b-0" style="display:flex; gap:0.5rem;">
                                <button type="submit" class="btn btn-success text-white">
                                    <i class="fa fa-paper-plane"></i> Submit Request
                                </button>
                                <a href="{{ route('stock-requests.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">How it works</h4>
                        <ul class="list-icons">
                            @php
                                $userRole = Auth::guard('staff')->user()->role ?? '';
                                $skipAccountant = in_array(strtolower($userRole), ['bar_keeper', 'bar keeper', 'bartender', 'head_chef', 'housekeeper']);
                            @endphp
                            @if(!$skipAccountant)
                                <li><i class="fa fa-chevron-right text-info"></i> Verified by <strong>Accountant</strong> first.
                                </li>
                            @endif
                            <li><i class="fa fa-chevron-right text-info"></i> <strong>Manager</strong> must approve.</li>
                            <li><i class="fa fa-chevron-right text-info"></i> <strong>Storekeeper</strong> issues
                                items.</li>
                            <li><i class="fa fa-chevron-right text-info"></i> You'll be notified once ready.</li>
                        </ul>
                        <div class="alert alert-success py-2 small mb-0">
                            <i class="fa fa-list-ul"></i>
                            Use <strong>Add Item</strong> to include multiple ingredients in a single request.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Hidden template row for cloning --}}
    <table style="display: none;">
        <tbody id="hiddenTemplateBody">
            <tr class="item-row template-row">
                <td>
                    <select class="form-control select-clone" required>
                        <option value="">-- Select --</option>
                        @foreach($products as $variant)
                            @php
                                $category = $variant->product->category ?? '';
                                $beverageCategories = ['spirits', 'wines', 'non_alcoholic_beverage', 'alcoholic_beverage', 'energy_drinks', 'juices', 'water', 'hot_beverages', 'cocktails'];
                                $isBeverage = in_array($category, $beverageCategories);
                                $foodCategories = ['food', 'meat_poultry', 'seafood', 'vegetables', 'dairy', 'pantry_baking', 'spices_herbs', 'oils_fats', 'kitchen', 'snacks'];
                                $isFood = in_array($category, $foodCategories);

                                $vName = $variant->variant_name;
                                if ($category === 'cleaning_supplies') {
                                    $vName = trim(str_ireplace(['(ml)', 'ml', '(l)', 'l'], '', $vName));
                                } else {
                                    $vName = trim(str_ireplace(['(Kg)', 'kg'], '', $vName));
                                }
                                $label = $variant->product->name ?? $variant->product->product_name ?? 'Item';
                                if (strtolower($vName) !== 'unit' && $vName !== '') {
                                    $label .= ' – ' . $vName;
                                }
                                if ($category === 'cleaning_supplies') {
                                    $vName = trim(str_ireplace(['(ml)', 'ml', '(l)', 'l'], '', $vName));
                                    $measure = '';
                                } else {
                                    $measure = trim($variant->measurement);
                                }
                                $details = [];
                                if (!$isFood) {
                                    $pName = trim($variant->packaging_name);
                                    if ($pName !== '' && !in_array(strtolower($pName), ['standard', 'unit']))
                                        $details[] = $pName;

                                    if ($measure !== '' && !in_array(strtolower($measure), ['0 ml', '0ml', '0', 'ml', 'litres', 'ltr', 'grams', 'g', 'kg']))
                                        $details[] = $measure;

                                    if ($details)
                                        $label .= ' (' . implode(' – ', $details) . ')';
                                }
                                $unitPrice = $variant->getLatestUnitCost();
                                $sellingPrice = $variant->selling_price_per_pic ?? 0;
                            @endphp
                            <option value="{{ $variant->id }}" data-unit-cost="{{ $unitPrice }}"
                                data-selling-price="{{ $sellingPrice }}" data-is-beverage="{{ $isBeverage ? 1 : 0 }}"
                                data-ratio="{{ $variant->items_per_package ?? 1 }}">
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="number" class="form-control qty-clone qty-input" step="0.01" min="0.01" placeholder="0.00"
                        required>
                </td>
                <td>
                    <select class="form-control unit-clone unit-select" required>
                        @if($isChef)
                            <option value="kg">Kilograms (Kg)</option>
                            <option value="grams">Grams (g)</option>
                            <option value="ltr">Litres (L)</option>
                            <option value="pcs">Pieces (Pcs)</option>
                            <option value="packets">Packets (Pkt)</option>
                            <option value="bags">Bags (Bag)</option>
                            <option value="boxes">Boxes (Box)</option>
                            <option value="packages">Bulk/Crates (Ratio)</option>
                            <option value="other">Other</option>
                        @elseif(isset($isHousekeeper) && $isHousekeeper)
                            <option value="packages">Boxes / Bundles</option>
                            <option value="bottles">Individual Units (Pcs)</option>
                            <option value="other">Other</option>
                        @else
                            <option value="packages">Crates / Packages</option>
                            <option value="bottles">Individual Bottles/Units</option>
                            <option value="other">Other</option>
                        @endif
                    </select>
                </td>
                <td class="text-right vertical-align-middle">
                    <span class="unit-cost-display">0.00</span>
                </td>
                @if(!$isChef && !(isset($isHousekeeper) && $isHousekeeper))
                    <td class="text-right vertical-align-middle text-info">
                        <span class="revenue-display">0.00</span>
                    </td>
                    <td class="text-right vertical-align-middle text-success">
                        <span class="profit-display">0.00</span>
                    </td>
                @endif
                <td class="text-right vertical-align-middle font-weight-bold">
                    <span class="subtotal-display">0.00</span>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-row-btn" title="Remove">
                        <i class="fa fa-times"></i>
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
@endsection

@section('styles')
    <link href="{{ asset('assets/node_modules/select2/dist/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        #itemsTable th {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .item-row td {
            vertical-align: middle;
        }
    </style>
@endsection

@section('scripts')
    <script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}" type="text/javascript"></script>
    <script>
        let rowIndex = 1;

        function initSelect2(row) {
            try {
                // Ensure we only initialize Select2 on the product dropdown, not the unit dropdown
                $(row).find('.select-clone, .select2-item').select2({ width: '100%' });
            } catch (e) {
                console.error("Select2 init error:", e);
            }
        }

        function updateRemoveButtons() {
            var rows = $('#itemsBody .item-row');
            rows.find('.remove-row-btn').prop('disabled', rows.length === 1);
        }

        $(document).ready(function () {
            try {
                initSelect2($('#itemsBody .item-row').first());
            } catch (e) {
                console.error("Initial row setup error:", e);
            }

            $('#addRowBtn').on('click', function (e) {
                e.preventDefault();

                try {
                    // Clone the entire row from the hidden table body
                    var $newRow = $('#hiddenTemplateBody .template-row').clone();
                    $newRow.removeClass('template-row');

                    // Assign proper names based on current rowIndex
                    $newRow.find('.select-clone').attr('name', 'items[' + rowIndex + '][product_variant_id]');
                    $newRow.find('.qty-clone').attr('name', 'items[' + rowIndex + '][quantity]');
                    $newRow.find('.unit-clone').attr('name', 'items[' + rowIndex + '][unit]');

                    // Append first so Select2 attaches to an element inside the DOM
                    $('#itemsBody').append($newRow);

                    // Initialize Select2 on the new row
                    initSelect2($newRow);

                    rowIndex++;
                    updateRemoveButtons();
                } catch (error) {
                    alert("Debug Error: " + error.message);
                    console.error("Add item error:", error);
                }
            });

            $(document).on('click', '.remove-row-btn', function () {
                $(this).closest('.item-row').remove();
                updateRemoveButtons();
                calculateGrandTotal();
            });

            // --- REAL-TIME COST CALCULATION ---
            function calculateRowCost(row) {
                const select = row.find('select[name*="product_variant_id"], .select-clone');
                const selectedOption = select.find('option:selected');
                const unitCost = parseFloat(selectedOption.data('unit-cost')) || 0;
                const sellingPrice = parseFloat(selectedOption.data('selling-price')) || 0;
                const ratio = parseFloat(selectedOption.data('ratio')) || 1;
                const isBeverage = parseInt(selectedOption.data('is-beverage')) || 0;

                const qtyInput = row.find('.qty-input');
                const qty = parseFloat(qtyInput.val()) || 0;

                const unitSelect = row.find('.unit-select');
                const unit = unitSelect.val();

                // Calculate subtotal and total items
                let totalItems = qty;
                let unitCostValue = unitCost;

                if (unit === 'packages' || unit === 'crates' || unit === 'carton') {
                    totalItems = qty * ratio;
                    unitCostValue = unitCost * ratio;
                } else if (unit === 'grams') {
                    totalItems = qty / 1000;
                    unitCostValue = unitCost * 1000; // Not really used for display but for consistency
                }

                // Show unit cost per selected unit (e.g. Crate price vs Bottle price)
                row.find('.unit-cost-display').text(unitCostValue.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

                // Calculate subtotal (Cost)
                const subtotal = totalItems * unitCost;
                row.find('.subtotal-display').text(subtotal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

                // Calculate Revenue and Profit for beverages
                if (isBeverage && sellingPrice > 0) {
                    const revenue = totalItems * sellingPrice;
                    const profit = revenue - subtotal;

                    row.find('.revenue-display').text(revenue.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                    row.find('.profit-display').text(profit.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                } else {
                    row.find('.revenue-display, .profit-display').text('—');
                }

                return subtotal;
            }

            function calculateGrandTotal() {
                let grandTotal = 0;
                $('#itemsTable .item-row').each(function () {
                    if (!$(this).hasClass('template-row')) {
                        grandTotal += calculateRowCost($(this));
                    }
                });
                $('#grandTotalDisplay').text(grandTotal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            }

            $(document).on('change', '.select2-item, .select-clone, .unit-select', function () {
                calculateGrandTotal();
            });

            $(document).on('input', '.qty-input', function () {
                calculateGrandTotal();
            });

            // Run once
            calculateGrandTotal();
        });
    </script>
@endsection