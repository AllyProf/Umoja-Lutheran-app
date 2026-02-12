<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Bill - {{ $dayService->service_reference }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            background: #fff;
            padding: 20px;
        }
        .container {
            max-width: 400px;
            margin: 0 auto;
            border: 1px dashed #000;
            padding: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 16px;
            text-transform: uppercase;
        }
        .header p {
            margin: 2px 0;
            font-size: 11px;
        }
        .info-section {
            margin-bottom: 15px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        .items-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .items-table th {
            text-align: left;
            border-bottom: 1px solid #000;
            padding: 5px 0;
        }
        .items-table td {
            padding: 5px 0;
            vertical-align: top;
        }
        .totals {
            margin-top: 15px;
            border-top: 1px solid #000;
            padding-top: 10px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .signature-section {
            margin-top: 40px;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 40px;
            width: 100%;
        }
        @media print {
            body { padding: 0; }
            .container { border: none; width: 100%; max-width: none; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Umoj Lutheran Hostel</h2>
            <p>Sokoine Road - Moshi</p>
            <p>Tel: 0677-155-156</p>
            <h3>SERVICE DOCKET / BILL</h3>
        </div>

        <div class="info-section">
            <div class="info-row">
                <span>Ref:</span>
                <span>{{ $dayService->service_reference }}</span>
            </div>
            <div class="info-row">
                <span>Date:</span>
                <span>{{ $dayService->service_date->format('d/m/Y') }}</span>
            </div>
            <div class="info-row">
                <span>Guest:</span>
                <span>{{ strtoupper($dayService->guest_name) }}</span>
            </div>
            @if($dayService->guest_phone)
            <div class="info-row">
                <span>Phone:</span>
                <span>{{ str_replace('+255+255', '+255', $dayService->guest_phone) }}</span>
            </div>
            @endif
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th style="text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                {{-- Package Items --}}
                @if($showPackage ?? true)
                @php
                    $packageItems = $dayService->package_items ?? [];
                    $commonLabels = ['food'=>'Food', 'swimming'=>'Swimming', 'drinks'=>'Drinks', 'decoration'=>'Decor'];
                @endphp
                @if(!empty($packageItems))
                    @foreach($packageItems as $key => $price)
                        @if($price > 0)
                        <tr>
                            <td>{{ $commonLabels[$key] ?? ucfirst($key) }}</td>
                            <td style="text-align: right;">{{ number_format($price) }}</td>
                        </tr>
                        @endif
                    @endforeach
                @else
                    <tr>
                        <td>{{ $dayService->service_type_name }}</td>
                        <td style="text-align: right;">{{ number_format($dayService->amount) }}</td>
                    </tr>
                @endif
                @endif

                {{-- Consumption --}}
                @php
                    $barConsumption = $dayService->serviceRequests ?? collect();
                    if(isset($filterCategories) && !empty($filterCategories)) {
                        $barConsumption = $barConsumption->filter(function($item) use ($filterCategories) {
                            return $item->service && in_array($item->service->category, $filterCategories);
                        });
                    }
                    $totalConsumption = $barConsumption->sum('total_price_tsh');
                @endphp
                @if($barConsumption->isNotEmpty())
                    <tr><td colspan="2" style="padding-top:10px; font-weight:bold; font-style:italic;">-- Consumption --</td></tr>
                    @foreach($barConsumption as $item)
                    <tr>
                        <td>
                            {{ $item->service_specific_data['item_name'] ?? $item->service->name ?? 'Item' }} (x{{ $item->quantity }})
                        </td>
                        <td style="text-align: right;">{{ number_format($item->total_price_tsh) }}</td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>

        <div class="totals">
            @php
                $mainAmount = ($showPackage ?? true) ? $dayService->amount : 0;
                
                // Ensure we use filtered list for totals
                $calcConsumption = $dayService->serviceRequests ?? collect();
                if(isset($filterCategories) && !empty($filterCategories)) {
                    $calcConsumption = $calcConsumption->filter(function($item) use ($filterCategories) {
                        return $item->service && in_array($item->service->category, $filterCategories);
                    });
                }
                
                $totalConsumption = $calcConsumption->sum('total_price_tsh');
                $totalBill = $mainAmount + $totalConsumption;
                
                $packagePaid = ($showPackage ?? true) ? ($dayService->amount_paid ?? 0) : 0;
                $consumptionPaid = $calcConsumption->where('payment_status', 'paid')->sum('total_price_tsh');
                $totalPaid = $packagePaid + $consumptionPaid;
                
                $balanceDue = $totalBill - $totalPaid;
            @endphp

            <div class="total-row">
                <span>Total Bill:</span>
                <span>{{ number_format($totalBill) }} TZS</span>
            </div>
            
            @if($totalPaid > 0)
            <div class="total-row">
                <span>Paid:</span>
                <span>{{ number_format($totalPaid) }} TZS</span>
            </div>
            @endif

            @if($balanceDue > 0)
            <div class="total-row" style="font-size: 14px;">
                <span>BALANCE DUE:</span>
                <span>{{ number_format($balanceDue) }} TZS</span>
            </div>
            @endif
        </div>

        <div class="signature-section">
            <p style="margin-bottom: 30px;">Guest Signature</p>
            <div class="signature-line" style="margin-top: 30px;"></div>
            
            <p style="margin-bottom: 30px; margin-top: 20px;">Receptionist: {{ auth()->user()->name ?? 'Staff' }}</p>
            <div class="signature-line" style="margin-top: 30px;"></div>
            
            <p style="font-size: 10px; margin-top: 15px;">Thank you for visiting Umoj Lutheran Hostel!</p>
            <p style="font-size: 10px; margin-top: 5px; color: #940000;">Powered By EmCa Techonologies</p>
        </div>
    </div>
    <script>
        window.onload = function() { window.print(); };
    </script>
</body>
</html>
