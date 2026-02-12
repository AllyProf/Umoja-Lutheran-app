<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receiving Report - {{ $shoppingList->name }} - PrimeLand Hotel</title>
    <style>
        @media print {
            @page { margin: 0 !important; size: A4; }
            @page :first { margin-top: 0 !important; }
            body { margin: 0 !important; padding: 0 !important; }
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            background: #fff;
            padding: 20px;
        }
        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border: 1px solid #ddd;
            position: relative;
        }
        .receipt-container::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            width: 500px;
            height: 500px;
            background-image: url('{{ url('royal-master/image/logo/Logo.png') }}');
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            opacity: 0.12;
            z-index: 0;
            pointer-events: none;
            filter: grayscale(100%) brightness(1.2);
        }
        .receipt-container > * { position: relative; z-index: 1; }
        @media print {
            .receipt-container { border: none !important; }
            .print-button { display: none !important; }
            
            /* Preserve status stamp */
            .status-stamp {
                position: absolute !important;
                bottom: 220px !important;
                right: 80px !important;
                opacity: 0.12 !important;
                border: 4px solid var(--success) !important;
                color: var(--success) !important;
                transform: rotate(-15deg) !important;
                pointer-events: none !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
        
        .status-stamp {
            position: absolute;
            bottom: 220px;
            right: 80px;
            border: 4px solid #28a745;
            color: #28a745;
            padding: 10px 30px;
            font-size: 32px;
            font-weight: 900;
            text-transform: uppercase;
            transform: rotate(-15deg);
            opacity: 0.12;
            border-radius: 10px;
            pointer-events: none;
            z-index: 0;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #e07632;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header .logo-container img { max-height: 80px; max-width: 300px; height: auto; width: auto; }
        .header h1 { color: #e07632; font-size: 28px; margin-bottom: 5px; font-weight: bold; }
        .receipt-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 30px;
            color: #333;
            text-transform: uppercase;
        }
        .info-section { margin-bottom: 25px; }
        .info-section h3 {
            color: #e07632;
            font-size: 14px;
            border-bottom: 2px solid #e07632;
            padding-bottom: 8px;
            margin-bottom: 15px;
            font-weight: bold;
        }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 10px; padding: 5px 0; }
        .info-label { font-weight: bold; color: #555; width: 45%; }
        .info-value { color: #333; width: 55%; text-align: right; }
        .two-column { display: flex; gap: 30px; margin-bottom: 25px; }
        .column { flex: 1; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table th {
            background-color: #f8f9fa;
            color: #333;
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
            font-weight: bold;
            font-size: 11px;
        }
        table td { padding: 12px; border: 1px solid #ddd; font-size: 11px; }
        table tbody tr:nth-child(even) { background-color: #f9f9f9; }
        table tbody tr.missing-item { background-color: #ffe6e6; opacity: 0.7; }
        
        .print-button { text-align: center; margin: 30px 0; }
        .print-button button {
            background-color: #e07632;
            color: #fff;
            border: none;
            padding: 12px 30px;
            font-size: 14px;
            cursor: pointer;
            border-radius: 4px;
            font-weight: bold;
        }
        .print-button button:hover { background-color: #c86528; }
        .checkbox-cell { text-align: center; width: 40px; }
        .checkbox-box {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 1px solid #333;
            background: #fff;
        }
        .checkbox-checked {
            background-color: #007bff;
            border-color: #007bff;
            position: relative;
        }
        .checkbox-checked::after {
            content: '✓';
            color: #fff;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 12px;
            font-weight: bold;
        }
        .checkbox-missed {
            background-color: #dc3545;
            border-color: #dc3545;
            position: relative;
        }
        .checkbox-missed::after {
            content: '✕';
            color: #fff;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 10px;
            font-weight: bold;
        }
        .status-badge {
            font-size: 9px;
            font-weight: bold;
            padding: 2px 5px;
            border-radius: 3px;
            text-transform: uppercase;
        }
        .status-bought { color: #007bff; }
        .status-partial { color: #e07632; }
        .status-missed { color: #dc3545; }
        table tbody tr.missing-item { background-color: #fff5f5; }
        .receipt-number {
            text-align: center;
            margin-bottom: 15px;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- Header -->
        <!-- Header -->
        <div class="header" style="display: flex; justify-content: center; align-items: center; flex-direction: column; margin-bottom: 20px; position: relative; z-index: 1;">
            <div class="logo-container" style="text-align: center; margin-bottom: 10px;">
                <img src="{{ url('royal-master/image/logo/Logo.png') }}" alt="Logo" style="max-height: 50px;">
            </div>
            <h4 style="margin: 0; color: #000; font-weight: bold; text-align: center;">PRIMELAND HOTEL</h4>
            <p style="font-size: 11px; font-weight: bold; margin: 0; color: #e07632; text-align: center;">RECEIVING REPORT</p>
            <p style="font-size: 9px; margin: 0; text-align: center;">Ref: SL-{{ $shoppingList->id }} | Date: {{ now()->format('d/m/Y') }} | Time: {{ now()->format('H:i') }}</p>
        </div>
        
        <!-- Print Button -->
        <div class="print-button">
            <button onclick="window.print()">
                Print Receiving Report
            </button>
            <button onclick="window.location.href='{{ route('admin.restaurants.shopping-list.transfer', $shoppingList->id) }}'" style="margin-left: 10px; background-color: #28a745;">
                Continue to Transfer Items
            </button>
        </div>
        
        <!-- Main Details -->
        @php
            $marketPrice = $shoppingList->total_actual_cost ?? $shoppingList->amount_used ?? 0;
            $budgetAmount = $shoppingList->budget_amount ?? $shoppingList->total_estimated_cost ?? 0;
            $remaining = $budgetAmount - $marketPrice;
        @endphp
        <div style="margin: 10px 0; padding: 5px; border: 1px dotted #ccc; display: flex; justify-content: space-around; font-size: 9px; line-height: 1.2;">
            <span><strong>Market:</strong> {{ $shoppingList->market_name ?? 'Any' }}</span>
            <span><strong>Budget:</strong> {{ number_format($budgetAmount, 0) }} TZS</span>
            <span><strong>Spent:</strong> {{ number_format($marketPrice, 0) }} TZS</span>
            <span><strong>Remaining:</strong> {{ number_format($remaining, 0) }} TZS</span>
            <span><strong>Missing:</strong> {{ $shoppingList->items->where('is_found', false)->count() }}</span>
        </div>

        @if($shoppingList->notes)
        <div class="info-section">
            <h3>Notes</h3>
            <p>{{ $shoppingList->notes }}</p>
        </div>
        @endif

        <!-- Items Table -->
        <div class="info-section">
            <table style="font-size: 9px;">
                <thead>
                    <tr style="background-color: #f4f4f4;">
                        <th width="3%" style="text-align: center;">✓</th>
                        <th width="30%">Item Name</th>
                        <th width="10%" style="text-align: center;">Status</th>
                        <th width="8%" style="text-align: center;">Plan</th>
                        <th width="8%" style="text-align: center;">Bought</th>
                        <th width="8%" style="text-align: center;">Diff</th>
                        <th width="8%" style="text-align: center;">Unit</th>
                        <th style="text-align: right;" width="12%">Est. Price</th>
                        <th style="text-align: right;" width="13%">Market</th>
                        <th width="10%" style="text-align: center;">Expiry</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                        $grandTotal = 0;
                        $plannedGrandTotal = 0;
                        $groupedItems = $shoppingList->items->groupBy('category');
                        
                        // Define kitchen operational order
                        $foodOrder = ['meat_poultry', 'seafood', 'pantry', 'dairy', 'baking', 'vegetables', 'spices', 'sauces', 'bakery', 'food', 'alcoholic_beverage', 'non_alcoholic_beverage', 'water', 'juices', 'energy_drinks', 'cleaning_supplies', 'linens', 'other'];
                        
                        $sortedCategories = $groupedItems->keys()->sortBy(function($key) use ($foodOrder) {
                            $pos = array_search($key, $foodOrder);
                            return $pos === false ? 999 : $pos;
                        });
                    @endphp
                    
                    @foreach($sortedCategories as $categoryKey)
                        @php $items = $groupedItems[$categoryKey]; @endphp
                        <tr style="background-color: #e0763220;">
                            <td colspan="10" style="font-weight: bold; color: #e07632;">
                                {{ $items->first()->category_name ?? ucfirst(str_replace('_', ' ', $categoryKey)) }}
                            </td>
                        </tr>
                        @foreach($items as $item)
                        @php 
                            $isFound = $item->is_found ?? true;
                            $purchasedQty = (float)($item->purchased_quantity ?? 0);
                            $planQty = (float)($item->quantity ?? 0);
                            $unitPrice = (float)($item->unit_price ?? 0);
                            $marketPrice = (float)($item->purchased_cost ?? ($unitPrice * $purchasedQty));
                            $plannedPrice = (float)($item->estimated_price ?? 0);
                            
                            $grandTotal += $marketPrice;
                            $plannedGrandTotal += $plannedPrice;
                            
                            $qtyDiff = $purchasedQty - $planQty;
                            $priceDiff = $marketPrice - $plannedPrice;
                        @endphp
                        <tr class="{{ !$isFound ? 'missing-item' : '' }}">
                            <td class="checkbox-cell" style="text-align: center;">
                                <div class="checkbox-box {{ $isFound ? 'checkbox-checked' : 'checkbox-missed' }}"></div>
                            </td>
                            <td>
                                <strong>{{ $item->product_name }}</strong>
                            </td>
                            <td style="text-align: center;">
                                @if(!$isFound)
                                    <span class="status-badge status-missed">MISSED</span>
                                @elseif($purchasedQty < $planQty)
                                    <span class="status-badge status-partial">UNDER</span>
                                @elseif($purchasedQty > $planQty)
                                    <span class="status-badge status-bought">EXTRA</span>
                                @else
                                    <span class="status-badge status-bought">BOUGHT</span>
                                @endif
                            </td>
                            <td style="text-align: center;">{{ number_format($planQty, 0) }}</td>
                            <td style="text-align: center;">{{ number_format($purchasedQty, 0) }}</td>
                            <td style="text-align: center;">
                                @if($qtyDiff > 0)
                                    <span style="color: #28a745; font-weight: bold;">+{{ number_format($qtyDiff, 0) }}</span>
                                @elseif($qtyDiff < 0)
                                    <span style="color: #dc3545; font-weight: bold;">{{ number_format($qtyDiff, 0) }}</span>
                                @else
                                    <span style="color: #777;">-</span>
                                @endif
                            </td>
                            <td style="text-align: center;">{{ $item->unit == 'bottles' ? 'PIC' : $item->unit }}</td>
                            <td style="text-align: right;">{{ number_format($plannedPrice, 0) }}</td>
                            <td style="text-align: right;">{{ number_format($marketPrice, 0) }}</td>
                            <td style="text-align: center;">
                                {{ $item->expiry_date ? $item->expiry_date->format('d/m/Y') : '-' }}
                            </td>
                        </tr>
                        @endforeach
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background-color: #f8f9fa; font-weight: bold;">
                        <td colspan="7" style="text-align: right; padding: 15px;">TOTALS:</td>
                        <td style="text-align: right; padding: 15px; background: #fdfdfd;">{{ number_format($plannedGrandTotal, 0) }}</td>
                        <td colspan="2" style="text-align: right; padding: 15px; font-size: 14px; color: #e07632;">{{ number_format($grandTotal, 0) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <div class="status-stamp">RECEIVED</div>
        
        <div class="footer" style="margin-top: 30px; border-top: 1px solid #ddd; padding-top: 10px; text-align: center; color: #777; font-size: 8px; position: relative;">
            <p style="margin-bottom: 5px;">Generated on {{ now()->format('d M Y H:i A') }} | Powered By EmCa Techonologies</p>
        </div>
    </div>
    
    <script>
        // Auto-open print dialog when page loads (only once)
        window.addEventListener('load', function() {
            // Use a flag to prevent multiple prints
            if (!sessionStorage.getItem('printTriggered_report_' + {{ $shoppingList->id }})) {
                sessionStorage.setItem('printTriggered_report_' + {{ $shoppingList->id }}, 'true');
                setTimeout(function() {
                    window.print();
                }, 500);
            }
        });
    </script>
</body>
</html>
