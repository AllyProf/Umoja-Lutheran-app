<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Receipt - {{ $dayService->service_reference }} - Umoj Lutheran Hostel</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        @media print {
            @page {
                margin: 0 !important;
                size: A4;
            }
            
            @page :first {
                margin-top: 0 !important;
            }
            
            @page :left {
                margin-left: 0 !important;
            }
            
            @page :right {
                margin-right: 0 !important;
            }
        }
    </style>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
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
            background-image: url('{{ asset('royal-master/image/logo/Logo.png') }}');
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            opacity: 0.12;
            z-index: 0;
            pointer-events: none;
            filter: grayscale(100%) brightness(1.2);
        }
        
        .receipt-container > * {
            position: relative;
            z-index: 1;
        }
        
        @media print {
            .receipt-container::before {
                opacity: 0.1 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #e07632;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header .logo-container {
            margin-bottom: 15px;
        }
        
        .header .logo-container img {
            max-height: 80px;
            max-width: 300px;
        }
        
        .header h1 {
            color: #e07632;
            font-size: 28px;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .header p {
            color: #666;
            font-size: 12px;
            margin: 2px 0;
        }
        
        .receipt-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        
        .receipt-info .info-block {
            flex: 1;
            min-width: 200px;
            margin-bottom: 15px;
        }
        
        .receipt-info .info-block h3 {
            color: #e07632;
            font-size: 14px;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        
        .receipt-info .info-block p {
            margin: 5px 0;
            font-size: 11px;
        }
        
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .details-table th,
        .details-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .details-table th {
            background-color: #f8f9fa;
            color: #333;
            font-weight: bold;
            font-size: 11px;
        }
        
        .details-table td {
            font-size: 11px;
        }
        
        .total-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #e07632;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            font-size: 13px;
        }
        
        .total-row.total {
            font-size: 16px;
            font-weight: bold;
            color: #e07632;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e07632;
            text-align: center;
        }
        
        .footer p {
            font-size: 10px;
            color: #666;
            margin: 5px 0;
        }
        
        .footer .powered-by {
            color: #940000;
            font-weight: bold;
            margin-top: 10px;
        }
        
        @media print {
            body {
                padding: 10px;
            }
            
            .receipt-container {
                padding: 20px;
                border: none;
            }
            
            .header h1 {
                font-size: 24px;
            }
            
            .details-table th,
            .details-table td {
                padding: 6px;
                font-size: 10px;
            }
            
            .total-row {
                font-size: 11px;
            }
            
            .total-row.total {
                font-size: 14px;
            }
            
            .footer p {
                font-size: 9px;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="header">
            <div class="logo-container">
                <img src="{{ asset('royal-master/image/logo/Logo.png') }}" alt="Umoj Lutheran Hostel Logo">
            </div>
            <h1>Umoj Lutheran Hostel</h1>
            <p>Sokoine Road - Moshi Kilimanjaro - Tanzania</p>
            <p>Phone: 0677-155-156 / +255 677-155-157</p>
            <p>Email: info@Umoj Lutheran Hostelhotel.co.tz | infoUmoj Lutheran Hostelhotel@gmail.com</p>
        </div>
        
        <div class="receipt-info">
            <div class="info-block">
                <h3>Service Information</h3>
                <p><strong>Reference:</strong> {{ $dayService->service_reference }}</p>
                <p><strong>Service Type:</strong> 
                    @php
                        $serviceTypeKey = strtolower($dayService->service_type ?? '');
                        $serviceTypeName = $dayService->service_type_name;
                        // Ensure ceremony-related services show correct name
                        if (str_contains($serviceTypeKey, 'ceremory') || str_contains($serviceTypeKey, 'ceremony') || str_contains($serviceTypeKey, 'birthday') || str_contains($serviceTypeKey, 'package')) {
                            $serviceTypeName = 'Ceremony/Birthday Package';
                        }
                    @endphp
                    {{ $serviceTypeName }}
                </p>
                <p><strong>Date:</strong> {{ $dayService->service_date->format('M d, Y') }}</p>
                <p><strong>Time:</strong> 
                    @if($dayService->service_time instanceof \Carbon\Carbon)
                        {{ $dayService->service_time->format('h:i A') }}
                    @else
                        {{ \Carbon\Carbon::parse($dayService->service_time)->format('h:i A') }}
                    @endif
                </p>
            </div>
            <div class="info-block">
                <h3>Guest Information</h3>
                <p><strong>Name:</strong> {{ $dayService->guest_name }}</p>
                @if($dayService->guest_phone)
                <p><strong>Phone:</strong> {{ str_replace('+255+255', '+255', $dayService->guest_phone) }}</p>
                @endif
                @if($dayService->guest_email)
                <p><strong>Email:</strong> {{ $dayService->guest_email }}</p>
                @endif
                <p><strong>Number of People:</strong> {{ $dayService->number_of_people }}</p>
            </div>
        </div>
        
        @if($dayService->items_ordered)
        <table class="details-table">
            <thead>
                <tr>
                    <th>Items Ordered</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $dayService->items_ordered }}</td>
                </tr>
            </tbody>
        </table>
        @endif
        
        <table class="details-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $packageItems = $dayService->package_items ?? [];
                    $packageItemLabels = [
                        'food' => 'Food',
                        'swimming' => 'Swimming',
                        'drinks' => 'Drinks',
                        'photos' => 'Photos',
                        'decoration' => 'Decoration',
                    ];
                @endphp
                @if(!empty($packageItems))
                    {{-- Show package items breakdown for ceremony packages --}}
                    @foreach($packageItems as $itemKey => $itemPrice)
                        @if(is_numeric($itemPrice) && $itemPrice > 0)
                            <tr>
                                <td>{{ $packageItemLabels[$itemKey] ?? ucfirst(str_replace('_', ' ', $itemKey)) }}</td>
                                <td style="text-align: right;">
                                    @if($dayService->guest_type === 'tanzanian')
                                        {{ number_format($itemPrice, 2) }} TZS
                                    @else
                                        ${{ number_format($itemPrice, 2) }}
                                        @php
                                            $rate = $dayService->exchange_rate ?? $exchangeRate ?? null;
                                        @endphp
                                        @if($rate)
                                            <br><small>(≈ {{ number_format($itemPrice * $rate, 2) }} TZS)</small>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @endforeach
                @else
                    {{-- Show single line item for non-package services --}}
                    <tr>
                        <td>{{ $dayService->service_type_name }} Service</td>
                        <td style="text-align: right;">
                            @if($dayService->guest_type === 'tanzanian')
                                {{ number_format($dayService->amount, 2) }} TZS
                            @else
                                ${{ number_format($dayService->amount, 2) }}
                                @php
                                    $rate = $dayService->exchange_rate ?? $exchangeRate ?? null;
                                @endphp
                                @if($rate)
                                    <br><small>(≈ {{ number_format($dayService->amount * $rate, 2) }} TZS)</small>
                                @endif
                            @endif
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
        
        {{-- Bar & Restaurant Consumption Section --}}
        @php
            $barConsumption = $dayService->serviceRequests ?? collect();
            $totalConsumption = $barConsumption->sum('total_price_tsh');
            $paidConsumption = $barConsumption->where('payment_status', 'paid')->sum('total_price_tsh');
            $unpaidConsumption = $barConsumption->where('payment_status', 'pending')->sum('total_price_tsh');
        @endphp
        
        @if($barConsumption->isNotEmpty())
        <table class="details-table">
            <thead>
                <tr>
                    <th colspan="4" style="background-color: #e07632; color: white; text-align: center;">Bar & Restaurant Consumption</th>
                </tr>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th style="text-align: right;">Unit Price</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($barConsumption as $item)
                    @php
                        $itemName = ($item->service_specific_data['item_name'] ?? null) 
                                    ? $item->service_specific_data['item_name'] 
                                    : ($item->service->name ?? 'Consumption Item');
                    @endphp
                    <tr>
                        <td>
                            {{ $itemName }}
                            @if($item->payment_status === 'paid')
                                <span style="color: #388e3c; font-size: 9px;"> ✓ PAID</span>
                            @else
                                <span style="color: #d32f2f; font-size: 9px;"> ⚠ UNPAID</span>
                            @endif
                        </td>
                        <td>{{ $item->quantity }}</td>
                        <td style="text-align: right;">{{ number_format($item->unit_price_tsh, 2) }} TZS</td>
                        <td style="text-align: right;">{{ number_format($item->total_price_tsh, 2) }} TZS</td>
                    </tr>
                @endforeach
                <tr style="background-color: #f8f9fa; font-weight: bold;">
                    <td colspan="3" style="text-align: right;">Total Consumption:</td>
                    <td style="text-align: right;">{{ number_format($totalConsumption, 2) }} TZS</td>
                </tr>
                @if($paidConsumption > 0)
                <tr style="color: #388e3c;">
                    <td colspan="3" style="text-align: right;">Amount Paid:</td>
                    <td style="text-align: right;">{{ number_format($paidConsumption, 2) }} TZS</td>
                </tr>
                @endif
                @if($unpaidConsumption > 0)
                <tr style="color: #d32f2f; font-weight: bold;">
                    <td colspan="3" style="text-align: right;">AMOUNT UNPAID:</td>
                    <td style="text-align: right;">{{ number_format($unpaidConsumption, 2) }} TZS</td>
                </tr>
                @endif
            </tbody>
        </table>
        @endif
        
        <div class="total-section">
            @if($dayService->discount_amount > 0)
            <div class="total-row">
                <span>Subtotal:</span>
                <span>
                    @if($dayService->guest_type === 'tanzanian')
                        {{ number_format($dayService->amount + $dayService->discount_amount, 2) }} TZS
                    @else
                        ${{ number_format($dayService->amount + $dayService->discount_amount, 2) }}
                    @endif
                </span>
            </div>
            <div class="total-row" style="color: #d32f2f;">
                <span>Discount @if($dayService->discount_type === 'percentage')({{ $dayService->discount_value }}%)@endif:</span>
                <span>
                    @if($dayService->guest_type === 'tanzanian')
                        -{{ number_format($dayService->discount_amount, 2) }} TZS
                    @else
                        -${{ number_format($dayService->discount_amount, 2) }}
                    @endif
                </span>
            </div>
            @if($dayService->discount_reason)
            <div class="total-row" style="font-size: 11px; color: #666; font-style: italic;">
                <span>Discount Reason:</span>
                <span>{{ $dayService->discount_reason }}</span>
            </div>
            @endif
            @endif
            
            <div class="total-row total">
                <span>Total Amount:</span>
                <span>
                    @if($dayService->guest_type === 'tanzanian')
                        {{ number_format($dayService->amount, 2) }} TZS
                    @else
                        ${{ number_format($dayService->amount, 2) }}
                        @php
                            $rate = $dayService->exchange_rate ?? $exchangeRate ?? null;
                        @endphp
                        @if($rate)
                            <br><small>(≈ {{ number_format($dayService->amount * $rate, 2) }} TZS)</small>
                        @endif
                    @endif
                </span>
            </div>
            <div class="total-row">
                <span>Payment Status:</span>
                <span>{{ ucfirst($dayService->payment_status) }}</span>
            </div>
            <div class="total-row">
                <span>Payment Method:</span>
                <span>{{ $dayService->payment_method_name }}</span>
            </div>
            @if($dayService->payment_provider)
            <div class="total-row">
                <span>Payment Provider:</span>
                <span>{{ $dayService->payment_provider }}</span>
            </div>
            @endif
            @if($dayService->payment_reference)
            <div class="total-row">
                <span>Reference Number:</span>
                <span>{{ $dayService->payment_reference }}</span>
            </div>
            @endif
            <div class="total-row">
                <span>Amount Paid:</span>
                <span>
                    @if($dayService->guest_type === 'tanzanian')
                        {{ number_format($dayService->amount_paid, 2) }} TZS
                    @else
                        ${{ number_format($dayService->amount_paid, 2) }}
                        @php
                            $rate = $dayService->exchange_rate ?? $exchangeRate ?? null;
                        @endphp
                        @if($rate)
                            <br><small>(≈ {{ number_format($dayService->amount_paid * $rate, 2) }} TZS)</small>
                        @endif
                    @endif
                </span>
            </div>
            @php
                $remainingAmount = $dayService->amount - $dayService->amount_paid;
            @endphp
            @if($remainingAmount > 0)
            <div class="total-row" style="color: #d32f2f; font-weight: bold;">
                <span>Remaining Amount:</span>
                <span>
                    @if($dayService->guest_type === 'tanzanian')
                        {{ number_format($remainingAmount, 2) }} TZS
                    @else
                        ${{ number_format($remainingAmount, 2) }}
                        @php
                            $rate = $dayService->exchange_rate ?? $exchangeRate ?? null;
                        @endphp
                        @if($rate)
                            <br><small>(≈ {{ number_format($remainingAmount * $rate, 2) }} TZS)</small>
                        @endif
                    @endif
                </span>
            </div>
            @elseif($remainingAmount < 0)
            <div class="total-row" style="color: #388e3c; font-weight: bold;">
                <span>Overpaid:</span>
                <span>
                    @if($dayService->guest_type === 'tanzanian')
                        {{ number_format(abs($remainingAmount), 2) }} TZS
                    @else
                        ${{ number_format(abs($remainingAmount), 2) }}
                        @php
                            $rate = $dayService->exchange_rate ?? $exchangeRate ?? null;
                        @endphp
                        @if($rate)
                            <br><small>(≈ {{ number_format(abs($remainingAmount) * $rate, 2) }} TZS)</small>
                        @endif
                    @endif
                </span>
            </div>
            @endif
            <div class="total-row">
                <span>Payment Date:</span>
                <span>{{ $dayService->paid_at->format('M d, Y H:i') }}</span>
            </div>
        </div>
        
        <div class="footer">
            <p>Thank you for choosing Umoj Lutheran Hostel!</p>
            <p>For inquiries, please contact us at the information above.</p>
            <p class="powered-by">Powered By EmCa Techonologies</p>
        </div>
    </div>
    
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

