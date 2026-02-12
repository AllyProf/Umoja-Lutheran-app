<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping List - {{ $shoppingList->name }} - PrimeLand Hotel</title>
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
            .receipt-container::before { opacity: 0.1 !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .receipt-container { border: none !important; }
            .print-button { display: none !important; }
            .checkmark { color: #007bff !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .missing-mark { color: #dc3545 !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
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
            padding: 6px 8px;
            text-align: left;
            border: 1px solid #ddd;
            font-weight: bold;
            font-size: 10px;
        }
        table td { padding: 6px 8px; border: 1px solid #ddd; font-size: 10px; }
        table tbody tr:nth-child(even) { background-color: #f9f9f9; }
        
        .print-button { text-align: center; margin: 15px 0; }
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
        .checkbox-cell { text-align: center; width: 50px; }
        .checkbox-box {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 1px solid #333;
            background: #fff;
        }
        .checkmark {
            color: #007bff;
            font-size: 18px;
            font-weight: bold;
        }
        .missing-mark {
            color: #dc3545;
            font-size: 14px;
            font-weight: bold;
        }
        .missing-row {
            background-color: #fff5f5 !important;
            color: #777;
        }
        .missing-row td {
            text-decoration: line-through;
        }
        .missing-row td:first-child, .missing-row td:last-child {
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- Header -->
        <div class="header">
            <div class="logo-container">
                <img src="{{ url('royal-master/image/logo/Logo.png') }}" alt="PrimeLand Hotel Logo" onerror="this.style.display='none';">
            </div>
            @php
                $uniqueDepts = $shoppingList->items->map(function($item) {
                    return $item->purchaseRequest ? $item->purchaseRequest->getDepartmentName() : null;
                })->filter()->unique()->sort()->values();
                
                $deptTitle = $uniqueDepts->isNotEmpty() ? '(' . $uniqueDepts->join(', ', ' & ') . ')' : '';
            @endphp
            <h1>PRIMELAND HOTEL - SHOPPING LIST <span style="font-size: 0.8em; font-weight: normal;">{{ $deptTitle }}</span></h1>
            <p style="margin-top: 5px; font-size: 11px;">Ref: SL-{{ $shoppingList->id }} | Date: {{ $shoppingList->created_at->format('d M Y') }}</p>

            @php
                $estTotal = $shoppingList->items->sum('estimated_price');
                $marketBudget = $shoppingList->budget_amount ?? $estTotal;
            @endphp
            
            <div style="margin-top: 10px; padding: 5px; border: 1px dotted #ccc; display: flex; justify-content: space-around; font-size: 10px;">
                <span><strong>Market:</strong> {{ $shoppingList->market_name ?? 'Any' }}</span>
                <span><strong>Budget to Market:</strong> {{ number_format($marketBudget, 0) }} TZS</span>
                <span><strong>Items:</strong> {{ $shoppingList->items->count() }}</span>
            </div>
        </div>
        
        
        <div class="receipt-title">{{ $shoppingList->name }}</div>
        
        <!-- Print Button -->
        <div class="print-button">
            <button onclick="window.print()">
                Print List
            </button>
        </div>
        


        <!-- Items Table -->
        <div class="info-section">
            <h3>Shopping Items</h3>
            <table>
                <thead>
                    <tr>
                        <th class="checkbox-cell"></th>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Qty</th>
                        <th>Unit</th>
                        @if($shoppingList->status === 'completed')
                            <th style="text-align: right;">Est. Price</th>
                            <th style="text-align: right;">Market Price</th>
                        @else
                            <th style="text-align: right;">Est. Price</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @php 
                        $grandTotal = 0; 
                        $actualTotal = 0; 
                        
                        // Consolidate identical items across all departments to save paper
                        $consolidated = [];
                        foreach ($shoppingList->items as $item) {
                            $key = strtolower($item->product_name) . '_' . strtolower($item->unit);
                            
                            if (!isset($consolidated[$key])) {
                                $consolidated[$key] = [
                                    'product_name' => $item->product_name,
                                    'category' => $item->category,
                                    'category_name' => $item->category_name,
                                    'quantity' => 0,
                                    'unit' => $item->unit,
                                    'estimated_price' => 0,
                                    'purchased_cost' => 0,
                                    'is_found' => true, // Default to true for display
                                    'departments' => []
                                ];
                            }
                            
                            $consolidated[$key]['quantity'] += (float)$item->quantity;
                            $consolidated[$key]['estimated_price'] += (float)$item->estimated_price;
                            $consolidated[$key]['purchased_cost'] += (float)$item->purchased_cost;
                            
                            if (isset($item->is_found) && !$item->is_found) {
                                $consolidated[$key]['is_found'] = false;
                            }

                            // Collect departments for reference
                            $dept = 'Other';
                            if ($item->purchaseRequest) {
                                $dept = substr($item->purchaseRequest->getDepartmentName(), 0, 1); // Short code: H, B, F, R
                            }
                            if (!in_array($dept, $consolidated[$key]['departments'])) {
                                $consolidated[$key]['departments'][] = $dept;
                            }
                        }

                        $itemsByCategory = collect($consolidated)->groupBy('category');
                        
                        // Sort categories
                        $categoryOrder = ['meat_poultry', 'seafood', 'pantry', 'dairy', 'baking', 'vegetables', 'spices', 'sauces', 'bakery', 'food', 'beverages', 'water'];
                        $sortedCategories = $itemsByCategory->keys()->sortBy(function($key) use ($categoryOrder) {
                            $pos = array_search($key, $categoryOrder);
                            return $pos === false ? 999 : $pos;
                        });
                    @endphp
                    
                    @foreach($sortedCategories as $categoryKey)
                        @php $items = $itemsByCategory[$categoryKey]; @endphp
                        <tr style="background-color: #e0763210;">
                            <td colspan="{{ $shoppingList->status === 'completed' ? '7' : '6' }}" style="font-weight: bold; color: #e07632; py-1;">
                                {{ $items->first()['category_name'] }}
                            </td>
                        </tr>
                        @foreach($items as $item)
                        <tr class="{{ ($shoppingList->status === 'completed' && !$item['is_found']) ? 'missing-row' : '' }}">
                            <td class="checkbox-cell">
                                @if($item['is_found'])
                                    <span class="checkmark">✓</span>
                                @else
                                    <span class="missing-mark">✕</span>
                                @endif
                            </td>
                            <td>
                                {{ $item['product_name'] }}
                                <small class="text-muted">({{ implode(',', $item['departments']) }})</small>
                            </td>
                            <td>{{ $item['category_name'] }}</td>
                            <td>{{ number_format($item['quantity'], 0) }}</td>
                            <td>{{ $item['unit'] == 'bottles' ? 'PIC' : $item['unit'] }}</td>
                            @if($shoppingList->status === 'completed')
                                <td style="text-align: right;">{{ number_format($item['estimated_price'], 0) }}</td>
                                <td style="text-align: right; font-weight: bold;">{{ number_format($item['purchased_cost'], 0) }}</td>
                            @else
                                <td style="text-align: right;">{{ number_format($item['estimated_price'], 0) }}</td>
                            @endif
                        </tr>
                        @php 
                            $grandTotal += $item['estimated_price']; 
                            $actualTotal += $item['purchased_cost'];
                        @endphp
                        @endforeach
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background-color: #f8f9fa; font-weight: bold;">
                        <td colspan="5" style="text-align: right; padding: 15px;">TOTAL ESTIMATED COST:</td>
                        @if($shoppingList->status === 'completed')
                            <td style="text-align: right; padding: 15px;">{{ number_format($grandTotal) }}</td>
                            <td style="text-align: right; padding: 15px; font-size: 14px; color: #e07632;">{{ number_format($actualTotal) }} TZS</td>
                        @else
                            <td style="text-align: right; padding: 15px; font-size: 14px; color: #e07632;">{{ number_format($grandTotal) }}</td>
                        @endif
                    </tr>
                </tfoot>
            </table>
            <div style="font-size: 10px; color: #666; margin-top: 5px; text-align: left; font-style: italic;">
                <strong>* Dept Ref:</strong> (B) Bar, (C) Chef, (H) Housekeeping, (R) Reception
            </div>
        </div>
        
        <div class="footer" style="margin-top: 50px; border-top: 1px solid #ddd; padding-top: 10px; text-align: center; color: #777; font-size: 9px;">
            <p style="margin-bottom: 5px;">Generated on {{ now()->format('d M Y H:i A') }} | Powered By <span style="color: #940000; font-weight: bold;">EmCa Technologies</span></p>
        </div>
    </div>
    
    <script>
        // Auto-open print dialog when page loads (only once)
        window.addEventListener('load', function() {
            // Use a flag to prevent multiple prints
            if (!sessionStorage.getItem('printTriggered_' + {{ $shoppingList->id }})) {
                sessionStorage.setItem('printTriggered_' + {{ $shoppingList->id }}, 'true');
                setTimeout(function() {
                    window.print();
                }, 500);
            }
        });
        
        // Reset flag when user closes print dialog (optional)
        window.addEventListener('afterprint', function() {
            // Keep the flag so it doesn't reprint if user navigates back
        });
    </script>
</body>
</html>
