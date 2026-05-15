@extends('dashboard.layouts.app')

@section('content')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-maroon: #940000;
            --primary-maroon-dark: #7b0000;
            --secondary-gray: #f8f9fa;
            --accent-gold: #d4af37;
            --glass-bg: rgba(255, 255, 255, 0.9);
            --card-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
            --transition-speed: 0.3s;
        }

        .shopping-list-details {
            font-family: 'Inter', sans-serif;
            animation: fadeIn 0.5s ease-out;
            color: #333;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .premium-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #edf2f7;
            box-shadow: var(--card-shadow);
            transition: all var(--transition-speed);
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .stats-item {
            text-align: center;
            padding: 1.5rem;
            border-right: 1px solid #edf2f7;
        }

        .stats-item:last-child {
            border-right: none;
        }

        .stats-label {
            font-size: 0.75rem;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .stats-value {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--primary-maroon);
        }

        .category-header {
            background-color: #f8f9fa;
            border-left: 5px solid var(--primary-maroon);
            padding: 12px 20px;
            margin: 25px 0 10px 0;
            font-weight: 700;
            color: #2d3748;
            display: flex;
            align-items: center;
            border-radius: 0 6px 6px 0;
        }

        .category-icon {
            margin-right: 12px;
            color: var(--primary-maroon);
            font-size: 1.1rem;
        }

        .table-modern {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
        }

        .table-modern thead th {
            background: transparent;
            border: none;
            color: #718096;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            padding: 10px 20px;
        }

        .item-row {
            background: #fff;
            transition: all 0.2s;
        }

        .item-row td {
            padding: 15px 20px;
            border-top: 1px solid #edf2f7;
            border-bottom: 1px solid #edf2f7;
            vertical-align: middle;
        }

        .item-row td:first-child {
            border-left: 1px solid #edf2f7;
            border-radius: 10px 0 0 10px;
        }

        .item-row td:last-child {
            border-right: 1px solid #edf2f7;
            border-radius: 0 10px 10px 0;
        }

        .item-row:hover {
            transform: scale(1.005);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            background-color: #fff !important;
        }

        .status-badge-custom {
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.7rem;
            text-transform: uppercase;
            display: inline-block;
        }

        .badge-pending {
            background: #fffaf0;
            color: #975a16;
            border: 1px solid #fbd38d;
        }

        .badge-approved {
            background: #f0fff4;
            color: #22543d;
            border: 1px solid #9ae6b4;
        }

        .badge-purchased {
            background: #ebf8ff;
            color: #2a4365;
            border: 1px solid #90cdf4;
        }

        .badge-ready_for_purchase {
            background: #faf5ff;
            color: #553c9a;
            border: 1px solid #d6bcfa;
        }

        .dept-tag {
            font-size: 0.65rem;
            padding: 2px 8px;
            background: #edf2f7;
            border-radius: 4px;
            margin-left: 6px;
            color: #4a5568;
            font-weight: 600;
            border: 1px solid #e2e8f0;
        }

        .tile-premium {
            background: #fff;
            border-radius: 15px;
            padding: 40px;
            box-shadow: var(--card-shadow);
            border: none;
        }

        .action-button {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .action-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        @media print {

            .app-header,
            .app-sidebar,
            .app-title,
            .d-print-none {
                display: none !important;
            }

            .content-wrapper {
                margin-left: 0 !important;
                padding: 0 !important;
            }

            .tile-premium {
                box-shadow: none !important;
                padding: 20px !important;
                border: 1px solid #eee !important;
            }

            .premium-card {
                box-shadow: none !important;
                border: 1px solid #eee !important;
                margin-bottom: 20px !important;
            }

            .stats-item {
                padding: 10px !important;
            }

            body {
                background: white !important;
                -webkit-print-color-adjust: exact !important;
            }
        }
    </style>

    <div class="app-title d-print-none">
        <div>
            <h1><i class="fa fa-shopping-basket"></i> Shopping List Details</h1>
            <p>Refined oversight for kitchen procurement</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.restaurants.shopping-list.index') }}">Shopping Lists</a>
            </li>
            <li class="breadcrumb-item active">View Details</li>
        </ul>
    </div>

    <div class="shopping-list-details container-fluid">
        <!-- Quick Actions Toolbar -->
        <div class="row d-print-none mb-4">
            <div class="col-md-12">
                <div class="tile p-3"
                    style="border-radius: 12px; display: flex; justify-content: space-between; align-items: center; background: white; border: 1px solid #edf2f7;">
                    <div>
                        <h4 class="mb-0" style="color: #2d3748; font-weight: 800;">
                            <span style="color: var(--primary-maroon);">#</span> {{ $shoppingList->name }}
                        </h4>
                    </div>
                    <div class="d-flex align-items: center gap-2">
                        <button class="btn btn-light action-button" onclick="window.print();">
                            <i class="fa fa-print"></i> Print View
                        </button>
                        <a href="{{ route('admin.restaurants.shopping-list.download', $shoppingList->id) }}"
                            class="btn btn-light action-button" target="_blank">
                            <i class="fa fa-file-pdf-o"></i> Export PDF
                        </a>

                        @if($shoppingList->status === 'accountant_checked' && (Auth::guard('staff')->user()->role == 'manager' || Auth::guard('staff')->user()->role == 'super_admin'))
                            <form action="{{ route('admin.shopping-list.manager-approve', $shoppingList->id) }}" method="POST"
                                class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-primary action-button"
                                    style="background: var(--primary-maroon); border: none;">
                                    <i class="fa fa-check-circle"></i> Manager Approve
                                </button>
                            </form>
                        @endif

                        @if($shoppingList->status === 'ready_for_purchase' && Auth::guard('staff')->user()->role == 'storekeeper')
                            <a href="{{ route('admin.restaurants.shopping-list.record', $shoppingList->id) }}"
                                class="btn btn-success action-button">
                                <i class="fa fa-credit-card"></i> Record Purchase
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @php
            $estTotal = $shoppingList->items->sum('estimated_price');
            $actTotal = $shoppingList->items->sum('purchased_cost');
            $marketBudget = $shoppingList->budget_amount ?? $estTotal;
            $remaining = $marketBudget - $actTotal;
        @endphp

        <!-- Metric Overview Cards -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="premium-card">
                    <div class="row no-gutters">
                        <div class="col-md-3 stats-item pl-md-4 text-md-left">
                            <div class="stats-label">Current Status</div>
                            <div>
                                <span class="status-badge-custom badge-{{ $shoppingList->status }}">
                                    <i class="fa fa-circle mr-1" style="font-size: 8px;"></i>
                                    {{ str_replace('_', ' ', $shoppingList->status) }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-3 stats-item">
                            <div class="stats-label">Items Count</div>
                            <div class="stats-value text-dark">{{ $shoppingList->items->count() }}</div>
                        </div>
                        <div class="col-md-3 stats-item">
                            <div class="stats-label">Estimated Budget</div>
                            <div class="stats-value">{{ number_format($marketBudget, 0) }} <small
                                    class="text-muted">TZS</small></div>
                        </div>
                        <div class="col-md-3 stats-item pr-md-4 text-md-right">
                            <div class="stats-label">Remaining Balance</div>
                            <div class="stats-value" style="color: {{ $remaining >= 0 ? '#38a169' : '#e53e3e' }};">
                                {{ number_format($remaining, 0) }} <small>TZS</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Items Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="tile tile-premium">
                    <div class="text-center mb-5 border-bottom pb-4">
                        <div
                            style="background: var(--primary-maroon); color: white; width: 50px; height: 50px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 800; margin-bottom: 15px;">
                            U</div>
                        <h2 style="font-weight: 800; color: #1a202c; letter-spacing: -0.5px; margin-bottom: 8px;">SHOPPING
                            REQUISITION</h2>
                        <div class="text-muted d-flex justify-content-center gap-3 align-items: center;">
                            <span><i class="fa fa-tag mr-1"></i> SL-{{ $shoppingList->id }}</span>
                            <span>&bull;</span>
                            <span><i class="fa fa-map-marker mr-1"></i>
                                {{ $shoppingList->market_name ?? 'Local Vendor' }}</span>
                            <span>&bull;</span>
                            <span><i class="fa fa-calendar mr-1"></i>
                                {{ $shoppingList->created_at->format('d M Y') }}</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table-modern">
                            <thead>
                                <tr>
                                    <th width="5%" class="text-center">#</th>
                                    <th>Ingredient Details</th>
                                    <th width="15%" class="text-center">Quantity</th>
                                    <th width="15%" class="text-center">Unit</th>
                                    <th width="20%" class="text-right">Est. Cost</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $consolidated = [];
                                    foreach ($shoppingList->items as $item) {
                                        $key = ($item->product_id ?? '0') . '-' . ($item->product_variant_id ?? '0') . '-' . strtolower($item->unit);
                                        if (!isset($consolidated[$key])) {
                                            $displayName = $item->product_name;
                                            if ($item->product_variant_id && $item->product_variant) {
                                                $vName = $item->product_variant->variant_name;
                                                if ($vName && strtolower($vName) !== 'standard' && strtolower($vName) !== 'unit' && !str_contains(strtolower($displayName), strtolower($vName))) {
                                                    $displayName .= ' - ' . $vName;
                                                }
                                            }
                                            $consolidated[$key] = [
                                                'product_name' => $displayName,
                                                'category' => $item->category,
                                                'quantity' => 0,
                                                'unit' => $item->unit,
                                                'estimated_subtotal' => 0,
                                                'ingredient_name' => ($item->product && strtolower($item->product->name) !== strtolower($item->product_name)) ? $item->product->name : null,
                                                'departments' => []
                                            ];
                                        }
                                        $consolidated[$key]['quantity'] += (float) $item->quantity;
                                        $consolidated[$key]['estimated_subtotal'] += (float) $item->estimated_price;
                                        $dept = $item->purchaseRequest ? substr($item->purchaseRequest->getDepartmentName(), 0, 1) : null;
                                        if ($dept && !in_array($dept, $consolidated[$key]['departments'])) {
                                            $consolidated[$key]['departments'][] = $dept;
                                        }
                                    }
                                    $groupedItems = collect($consolidated)->groupBy('category');
                                    $grandTotal = 0;
                                    $counter = 1;

                                    $icons = [
                                        'meat_poultry' => 'fa-cutlery',
                                        'vegetables' => 'fa-leaf',
                                        'beverages' => 'fa-glass',
                                        'alcoholic_beverage' => 'fa-beer',
                                        'food' => 'fa-spoon',
                                        'dairy' => 'fa-tint',
                                        'pantry' => 'fa-archive',
                                        'snacks' => 'fa-cookie'
                                    ];
                                @endphp

                                @foreach($groupedItems as $category => $items)
                                    <tr>
                                        <td colspan="5">
                                            <div class="category-header">
                                                <i class="fa {{ $icons[$category] ?? 'fa-folder-open' }} category-icon"></i>
                                                {{ ucfirst(str_replace('_', ' ', $category)) }}
                                            </div>
                                        </td>
                                    </tr>
                                    @foreach($items as $item)
                                        <tr class="item-row">
                                            <td class="text-center text-muted small" style="font-weight: 600;">
                                                {{ str_pad($counter++, 2, '0', STR_PAD_LEFT) }}</td>
                                            <td style="font-weight: 700; color: #2d3748;">
                                                {{ $item['product_name'] }}
                                                @if(isset($item['ingredient_name']) && $item['ingredient_name'])
                                                    <br><small class="text-success" style="font-weight: 800;">Ingredient:
                                                        {{ $item['ingredient_name'] }}</small>
                                                @endif
                                                @foreach($item['departments'] as $d)
                                                    <span class="dept-tag">{{ $d }}</span>
                                                @endforeach
                                            </td>
                                            <td class="text-center font-weight-bold">{{ number_format($item['quantity'], 0) }}</td>
                                            <td class="text-center">
                                                <span
                                                    class="text-muted font-weight-bold small">{{ $item['unit'] == 'bottles' ? 'PIC' : strtoupper($item['unit']) }}</span>
                                            </td>
                                            <td class="text-right font-weight-bold" style="color: #2d3748; font-size: 1.05rem;">
                                                {{ number_format($item['estimated_subtotal'], 0) }}
                                            </td>
                                        </tr>
                                        @php $grandTotal += $item['estimated_subtotal']; @endphp
                                    @endforeach
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-right py-4"
                                        style="font-size: 1.1rem; font-weight: 600; color: #718096; border-top: 2px solid #edf2f7;">
                                        Estimated Total Amount</td>
                                    <td class="text-right py-4"
                                        style="font-size: 1.5rem; color: var(--primary-maroon); font-weight: 800; border-top: 2px solid #edf2f7;">
                                        {{ number_format($grandTotal, 0) }} <small
                                            style="font-size: 0.9rem; font-weight: 600;">TZS</small>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if($shoppingList->notes)
                        <div class="mt-5 p-4" style="background: #f7fafc; border-radius: 12px; border: 1px dashed #cbd5e0;">
                            <h5
                                style="font-size: 0.8rem; font-weight: 800; color: #4a5568; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px;">
                                <i class="fa fa-sticky-note mr-1"></i> Manager's Notes
                            </h5>
                            <p class="mb-0" style="color: #4a5568; line-height: 1.6; font-style: italic;">
                                {{ $shoppingList->notes }}</p>
                        </div>
                    @endif

                    <div class="row mt-5 text-center d-print-none border-top pt-4">
                        <div class="col-12">
                            <p class="text-muted small mb-0">List generated on {{ date('d M Y') }} at {{ date('H:i A') }}
                            </p>
                            <p class="text-muted small" style="font-size: 10px;">&copy; {{ date('Y') }} Umoja Lutheran
                                Hostel. Management System</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
