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
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title mb-0">Request {{ $cardItemLabel }} Items (Quick Entry)</h4>
                            <div class="search-box" style="width: 300px;">
                                <input type="text" id="itemSearch" class="form-control"
                                    placeholder="Search {{ $itemType }}...">
                            </div>
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
                                    storekeeper.</p>
                            </div>
                        @endif

                        <form action="{{ route('stock-requests.store') }}" method="POST" id="stockRequestForm">
                            @csrf

                            <div class="table-responsive mb-3" style="max-height: 600px; overflow-y: auto;">
                                <table class="table table-bordered table-hover align-middle" id="itemsTable">
                                    <thead class="thead-light sticky-top bg-white">
                                        <tr>
                                            <th style="min-width:260px;">{{ $itemType }}</th>
                                            <th style="min-width:110px;">Store Stock</th>
                                            <th style="min-width:120px;">Quantity</th>
                                            <th style="min-width:140px;">Unit</th>
                                            <th class="text-right" style="min-width:100px;">Est. Revenue</th>
                                            <th class="text-right" style="min-width:120px;">Subtotal (Cost)</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsBody">
                                        @foreach($products as $index => $variant)
                                            @php
                                                $category = $variant->product->category ?? '';
                                                $beverageCategories = ['spirits', 'wines', 'non_alcoholic_beverage', 'alcoholic_beverage', 'energy_drinks', 'juices', 'water', 'hot_beverages', 'cocktails', 'drinks', 'beverage'];
                                                $isBeverage = in_array(strtolower($category), $beverageCategories);

                                                $vName = $variant->variant_name;
                                                $label = $variant->product->name ?? 'Item';
                                                if (strtolower($vName) !== 'standard' && $vName !== '') {
                                                    $label .= ' – ' . $vName;
                                                }

                                                $unitPrice = $variant->getLatestUnitCost();
                                                $sellingPrice = $variant->selling_price_per_pic ?? 0;
                                                $currentStock = $variant->getCurrentStock();
                                                $baseUnit = ($isChef) ? ($variant->receiving_unit ?? 'kg') : 'units';
                                            @endphp
                                            <tr class="item-row" data-search="{{ strtolower($label) }}">
                                                <td>
                                                    <strong>{{ $label }}</strong>
                                                    <input type="hidden" name="items[{{ $index }}][product_variant_id]"
                                                        value="{{ $variant->id }}">
                                                    <input type="hidden" class="row-unit-cost" value="{{ $unitPrice }}">
                                                    <input type="hidden" class="row-selling-price" value="{{ $sellingPrice }}">
                                                    <input type="hidden" class="row-ratio"
                                                        value="{{ $variant->items_per_package ?? 1 }}">
                                                    <input type="hidden" class="row-is-beverage"
                                                        value="{{ $isBeverage ? 1 : 0 }}">
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge {{ $currentStock <= ($variant->minimum_stock_level ?? 0) ? 'badge-danger' : 'badge-info' }}">
                                                        {{ number_format($currentStock, 2) }} {{ $baseUnit }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <input type="number" name="items[{{ $index }}][quantity]"
                                                        class="form-control qty-input" step="0.01" min="0" placeholder="0.00">
                                                </td>
                                                <td>
                                                    <select name="items[{{ $index }}][unit]" class="form-control unit-select">
                                                        @if($isChef)
                                                            <option value="kg">Kg</option>
                                                            <option value="grams">Grams</option>
                                                            <option value="ltr">Litres</option>
                                                            <option value="pcs">Pieces</option>
                                                            <option value="packets">Packets</option>
                                                            <option value="packages">Bulk/Ratio</option>
                                                        @elseif(isset($isHousekeeper) && $isHousekeeper)
                                                            <option value="packages">Boxes/Bundles</option>
                                                            <option value="bottles">Pcs</option>
                                                        @else
                                                            @if($isBeverage)
                                                                <option value="packages">Crates/Cartons</option>
                                                                <option value="bottles">Individual Bottles</option>
                                                            @else
                                                                <option value="pcs">Pieces (Pcs)</option>
                                                                <option value="packets">Packets (Pkt)</option>
                                                                <option value="packages">Boxes (Box)</option>
                                                                <option value="other">Other</option>
                                                            @endif
                                                        @endif
                                                    </select>
                                                </td>
                                                <td class="text-right text-info">
                                                    <span class="revenue-display">0.00</span>
                                                </td>
                                                <td class="text-right font-weight-bold">
                                                    <span class="subtotal-display">0.00</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="card bg-light border">
                                        <div class="card-body">
                                            <div class="row text-center">
                                                <div class="col-md-6">
                                                    <h6 class="text-muted">Total Estimated Cost</h6>
                                                    <h3 id="grandTotalDisplay">0.00</h3>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6 class="text-muted">Total Estimated Revenue</h6>
                                                    <h3 id="grandRevenueDisplay" class="text-info">0.00</h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions mt-4">
                                <label class="font-weight-bold">Notes <span
                                        class="text-muted font-weight-normal">(Optional)</span></label>
                                <textarea name="notes" class="form-control mb-3" rows="2"
                                    placeholder="Additional details..."></textarea>
                            </div>

                            <div class="form-group m-b-0" style="display:flex; gap:0.5rem;">
                                <button type="submit" class="btn btn-success btn-lg text-white">
                                    <i class="fa fa-paper-plane"></i> Submit Request
                                </button>
                                <a href="{{ route('stock-requests.index') }}" class="btn btn-secondary btn-lg">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        #itemsTable th {
            font-size: 11px;
            text-transform: uppercase;
        }

        .sticky-top {
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .item-row.hidden {
            display: none;
        }

        #itemsTable td {
            padding: 8px;
        }

        .qty-input:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, .25);
        }

        .qty-input {
            border: 1px solid #ced4da;
        }

        .item-row:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }
    </style>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // --- SEARCH FUNCTIONALITY ---
            $('#itemSearch').on('keyup', function () {
                const value = $(this).val().toLowerCase();
                $("#itemsBody tr").filter(function () {
                    $(this).toggle($(this).data('search').indexOf(value) > -1)
                });
            });

            // --- REAL-TIME CALCULATION ---
            function calculateRow(row) {
                const qty = parseFloat(row.find('.qty-input').val()) || 0;
                const unit = row.find('.unit-select').val();
                const unitCost = parseFloat(row.find('.row-unit-cost').val()) || 0;
                const sellingPrice = parseFloat(row.find('.row-selling-price').val()) || 0;
                const ratio = parseFloat(row.find('.row-ratio').val()) || 1;
                const isBeverage = parseInt(row.find('.row-is-beverage').val()) || 0;

                let totalItems = qty;
                if (unit === 'packages' || unit === 'crates' || unit === 'carton' || unit === 'boxes' || unit === 'packets') {
                    totalItems = qty * ratio;
                }

                const subtotal = totalItems * unitCost;
                row.find('.subtotal-display').text(subtotal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

                let revenue = 0;
                if (isBeverage && sellingPrice > 0) {
                    revenue = totalItems * sellingPrice;
                    row.find('.revenue-display').text(revenue.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                } else {
                    row.find('.revenue-display').text('—');
                }

                return { cost: subtotal, revenue: revenue };
            }

            function calculateTotals() {
                let grandCost = 0;
                let grandRevenue = 0;

                $('#itemsBody tr').each(function () {
                    const result = calculateRow($(this));
                    grandCost += result.cost;
                    grandRevenue += result.revenue;
                });

                $('#grandTotalDisplay').text(grandCost.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                $('#grandRevenueDisplay').text(grandRevenue.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            }

            $(document).on('input', '.qty-input', function () {
                calculateTotals();
            });

            $(document).on('change', '.unit-select', function () {
                calculateTotals();
            });

            // Initial calculation
            calculateTotals();
        });
    </script>
@endsection