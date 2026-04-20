@extends('dashboard.layouts.app')

@section('content')
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --glass-bg: rgba(255, 255, 255, 0.95);
            --glass-border: rgba(255, 255, 255, 0.2);
            --shadow-premium: 0 10px 30px -5px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --success-soft: #c6f6d5;
            --danger-soft: #fed7d7;
            --info-soft: #bee3f8;
        }

        .premium-card {
            background: white;
            border-radius: 20px;
            border: 1px solid #edf2f7;
            box-shadow: var(--shadow-premium);
            overflow: hidden;
            margin-bottom: 25px;
        }

        .premium-header {
            background: var(--primary-gradient);
            padding: 25px;
            color: white;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            padding: 20px;
            background: #f8fafc;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
            border: 1px solid #edf2f7;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        .stat-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #718096;
            margin-bottom: 5px;
            display: block;
        }

        .stat-value {
            font-size: 20px;
            font-weight: 800;
            color: #2d3748;
        }

        /* Table Styling */
        .premium-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
            padding: 0 20px 20px 20px;
        }

        .premium-table th {
            padding: 15px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #a0aec0;
            border: none;
        }

        .premium-table tr.item-row {
            background: white;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .premium-table tr.item-row td {
            padding: 20px 15px;
            border-top: 1px solid #edf2f7;
            border-bottom: 1px solid #edf2f7;
            vertical-align: middle;
        }

        .premium-table tr.item-row td:first-child {
            border-left: 1px solid #edf2f7;
            border-radius: 12px 0 0 12px;
        }

        .premium-table tr.item-row td:last-child {
            border-right: 1px solid #edf2f7;
            border-radius: 0 12px 12px 0;
        }

        .premium-table tr.item-row:hover {
            transform: scale(1.01);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
            z-index: 10;
        }

        .is-missing {
            opacity: 0.6;
            background: #fdf2f2 !important;
        }

        .is-missing .item-name {
            text-decoration: line-through;
            color: #e53e3e;
        }

        /* Checkbox Styling */
        .custom-checkbox {
            width: 24px;
            height: 24px;
            cursor: pointer;
            accent-color: #764ba2;
        }

        /* Inputs */
        .premium-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-weight: 600;
            color: #2d3748;
            outline: none;
            transition: border-color 0.2s;
        }

        .premium-input:focus {
            border-color: #764ba2;
            box-shadow: 0 0 0 3px rgba(118, 75, 162, 0.1);
        }

        .kg-badge {
            background: #fff5f5;
            color: #9b2c2c;
            border: 1px solid #feb2b2;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
        }

        .finalize-bar {
            position: sticky;
            bottom: 20px;
            left: 20px;
            right: 20px;
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 -10px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--glass-border);
            z-index: 100;
        }
    </style>

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

    <div class="app-title">
        <div>
            <h1><i class="fa fa-pencil-square-o"></i> Finalize Purchases</h1>
            <p>Record actual costs and quantities for <strong>{{ $shoppingList->name }}</strong></p>
        </div>
    </div>

    <form action="{{ $updateRoute }}" method="POST" id="purchaseForm">
        @csrf
        @method('PUT')

        <div class="premium-card">
            <div class="stats-grid">
                <div class="stat-card">
                    <span class="stat-label">Market / Supplier</span>
                    <input type="text" class="form-control form-control-sm" name="market_name"
                        value="{{ $shoppingList->market_name }}" placeholder="e.g. City Market">
                </div>
                <div class="stat-card">
                    <span class="stat-label">Initial Budget</span>
                    <div class="stat-value">
                        {{ number_format($shoppingList->budget_amount ?: $shoppingList->total_estimated_cost, 0) }}
                        <small>TZS</small></div>
                </div>
                <div class="stat-card">
                    <span class="stat-label">Amount Used</span>
                    <div class="stat-value text-primary" id="amount_used_display">0</div>
                </div>
                <div class="stat-card">
                    <span class="stat-label">Items Missing</span>
                    <div class="stat-value text-danger" id="missing_items_count">0</div>
                </div>
            </div>

            <div class="p-4 bg-white border-top">
                <table class="premium-table">
                    <thead>
                        <tr>
                            <th class="text-center">Found</th>
                            <th>Item Details</th>
                            <th class="text-center">Plan</th>
                            <th class="text-center">Actual Buy</th>
                            <th class="text-right">Unit Price</th>
                            <th class="text-right">Total Cost</th>
                            <th class="text-center">Weight (KG)</th>
                            <th>Expiry</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($shoppingList->items as $item)
                            <tr class="item-row {{ $item->is_found ? '' : 'is-missing' }}" data-item-id="{{ $item->id }}">
                                <td class="text-center">
                                    <input type="checkbox" class="custom-checkbox is-found-checkbox"
                                        name="items[{{ $item->id }}][is_found]" value="1" {{ $item->is_found !== false ? 'checked' : '' }}>
                                </td>
                                <td>
                                    <div class="item-name font-weight-bold">{{ $item->product_name }}</div>
                                    <span class="badge badge-light border" style="font-size: 9px;">{{ $item->category }}</span>
                                    @if($item->product_variant_id)
                                        <input type="hidden" name="items[{{ $item->id }}][product_variant_id]"
                                            value="{{ $item->product_variant_id }}">
                                    @endif
                                </td>
                                <td class="text-center text-muted">
                                    {{ number_format($item->quantity, 0) }} {{ $item->unit }}
                                </td>
                                <td>
                                    <div class="input-group input-group-sm" style="width: 120px; margin: 0 auto;">
                                        <input type="number" step="0.1" class="form-control text-center purchased-quantity"
                                            name="items[{{ $item->id }}][purchased_quantity]"
                                            value="{{ round($item->purchased_quantity ?? $item->quantity, 1) }}">
                                        <div class="input-group-append">
                                            <span class="input-group-text">{{ $item->unit }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-right">
                                    <input type="number" step="1" class="premium-input text-right unit-price"
                                        style="width: 100px; padding: 5px;"
                                        value="{{ $item->purchased_quantity > 0 ? round($item->purchased_cost / $item->purchased_quantity) : round($item->estimated_price / ($item->quantity ?: 1)) }}">
                                </td>
                                <td class="text-right">
                                    <input type="number" step="1" class="premium-input text-right total-cost"
                                        name="items[{{ $item->id }}][purchased_cost]"
                                        style="width: 120px; border-color: #cbd5e0;"
                                        value="{{ round($item->purchased_cost ?? $item->estimated_price) }}">
                                </td>
                                <td class="text-center">
                                    @if(in_array($item->category, ['meat_poultry', 'seafood', 'vegetables', 'dairy', 'food']))
                                        <div class="kg-badge">
                                            <input type="number" step="0.01"
                                                class="received-quantity-kg border-0 bg-transparent text-center font-weight-bold"
                                                style="width: 50px; color: inherit; outline: none;"
                                                name="items[{{ $item->id }}][received_quantity_kg]"
                                                value="{{ $item->received_quantity_kg > 0 ? $item->received_quantity_kg : '' }}"
                                                placeholder="0.00"> KG
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <input type="date" class="form-control form-control-sm"
                                        name="items[{{ $item->id }}][expiry_date]"
                                        value="{{ $item->expiry_date ? $item->expiry_date->format('Y-m-d') : '' }}">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="finalize-bar">
            <div>
                <a href="{{ $indexRoute }}" class="btn btn-link text-muted font-weight-bold"><i
                        class="fa fa-arrow-left"></i> Return to Lists</a>
            </div>
            <div class="d-flex align-items-center gap-4">
                <div class="text-right mr-4">
                    <div class="text-muted small text-uppercase letter-spacing-1">Grand Total</div>
                    <div class="h4 mb-0 font-weight-bold text-primary" id="total_cost_display">0 TZS</div>
                </div>
                <button type="button" id="finalizeBtn" class="btn btn-primary p-3 px-5 font-weight-bold"
                    style="border-radius: 12px; box-shadow: 0 4px 15px rgba(118, 75, 162, 0.3);">
                    <i class="fa fa-check-circle mr-2"></i> FINALIZE & SUBMIT
                </button>
            </div>
        </div>
    </form>

    <script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            function calculateTotals() {
                let totalCost = 0;
                let missingCount = 0;

                $('.item-row').each(function () {
                    const isFound = $(this).find('.is-found-checkbox').is(':checked');
                    if (!isFound) {
                        missingCount++;
                        $(this).addClass('is-missing');
                        $(this).find('input:not(.is-found-checkbox)').prop('disabled', true);
                    } else {
                        $(this).removeClass('is-missing');
                        $(this).find('input').prop('disabled', false);
                        const cost = parseFloat($(this).find('.total-cost').val()) || 0;
                        totalCost += cost;
                    }
                });

                $('#total_cost_display').text(totalCost.toLocaleString() + ' TZS');
                $('#amount_used_display').text(totalCost.toLocaleString());
                $('#missing_items_count').text(missingCount);
            }

            // Unit Price and Total Cost Sync
            $(document).on('input', '.unit-price', function () {
                const row = $(this).closest('.item-row');
                const qty = parseFloat(row.find('.purchased-quantity').val()) || 0;
                const unitPrice = parseFloat($(this).val()) || 0;
                if (qty > 0) {
                    row.find('.total-cost').val(Math.round(qty * unitPrice));
                }
                calculateTotals();
            });

            $(document).on('input', '.total-cost', function () {
                const row = $(this).closest('.item-row');
                const qty = parseFloat(row.find('.purchased-quantity').val()) || 0;
                const totalCost = parseFloat($(this).val()) || 0;
                if (qty > 0) {
                    row.find('.unit-price').val(Math.round(totalCost / qty));
                }
                calculateTotals();
            });

            $(document).on('input', '.purchased-quantity', function () {
                const row = $(this).closest('.item-row');
                const qty = parseFloat($(this).val()) || 0;
                const unitPrice = parseFloat(row.find('.unit-price').val()) || 0;
                if (qty > 0) {
                    row.find('.total-cost').val(Math.round(qty * unitPrice));
                }
                calculateTotals();
            });

            $(document).on('change', '.is-found-checkbox', function () {
                calculateTotals();
            });

            calculateTotals();

            $('#finalizeBtn').on('click', function () {
                // Re-enable everything for form submission
                $('.item-row input').prop('disabled', false);

                swal({
                    title: "Submit for Verification?",
                    text: "The budget used and actual quantities will be sent for final verification.",
                    type: "info",
                    showCancelButton: true,
                    confirmButtonText: "Yes, submit it!",
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true
                }, function () {
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
                                swal("Success!", data.message, "success");
                                setTimeout(() => window.location.href = data.redirect_url, 1500);
                            } else {
                                swal("Error", data.message, "error");
                            }
                        })
                        .catch(err => swal("Error", "Server connection failed", "error"));
                });
            });
        });
    </script>
@endsection