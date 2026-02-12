<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Receipt - {{ $receipt->id }} - Umoj Lutheran Hostel</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        @media print {
            @page {
                margin: 0 !important;
                size: A4;
            }
            @page :first { margin-top: 0 !important; }
            @page :left { margin-left: 0 !important; }
            @page :right { margin-right: 0 !important; }
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
            background-image: url('{{ asset('royal-master/image/logo/Logo.png') }}');
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            opacity: 0.12;
            z-index: 0;
            pointer-events: none;
            filter: grayscale(100%) brightness(1.2);
        }
        .receipt-container > * { position: relative; z-index: 1; }
        .header {
            text-align: center;
            border-bottom: 3px solid #e07632;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header .logo-container img { max-height: 80px; max-width: 300px; }
        .header h1 { color: #e07632; font-size: 28px; margin-bottom: 5px; font-weight: bold; }
        .header p { color: #666; font-size: 12px; margin: 2px 0; }
        .receipt-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        .receipt-info .info-block { flex: 1; min-width: 200px; margin-bottom: 15px; }
        .receipt-info .info-block h3 {
            color: #e07632;
            font-size: 14px;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .receipt-info .info-block p { margin: 5px 0; font-size: 11px; }
        .details-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .details-table th, .details-table td { padding: 12px; text-align: left; border: 1px solid #ddd; }
        .details-table th { background-color: #f8f9fa; color: #333; font-weight: bold; font-size: 11px; }
        .details-table td { font-size: 11px; }
        .total-section { margin-top: 20px; padding-top: 20px; border-top: 2px solid #e07632; }
        .total-row { display: flex; justify-content: space-between; margin: 8px 0; font-size: 13px; }
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
        .footer p { font-size: 10px; color: #666; margin: 5px 0; }
        .powered-by { color: #940000; font-weight: bold; margin-top: 10px; }
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
        
        <div style="text-align: center; margin-bottom: 20px;">
            <h2 style="color: #333; text-transform: uppercase; letter-spacing: 2px;">Stock Receipt</h2>
            <p style="color: #666;">Ref: #SR-{{ str_pad($receipt->id, 5, '0', STR_PAD_LEFT) }}</p>
        </div>

        <div class="receipt-info">
            <div class="info-block">
                <h3>General Information</h3>
                <p><strong>Date Received:</strong> {{ $receipt->received_date->format('M d, Y') }}</p>
                <p><strong>Supplier:</strong> {{ $receipt->supplier->name }}</p>
                <p><strong>Received By:</strong> {{ $receipt->receivedBy->name ?? 'N/A' }}</p>
            </div>
            <div class="info-block">
                <h3>Product Details</h3>
                <p><strong>Product:</strong> {{ $receipt->product->name }}</p>
                <p><strong>Variant:</strong> {{ $receipt->productVariant->measurement }}</p>
                <p><strong>Packaging:</strong> {{ $receipt->productVariant->packaging_name ?? $receipt->productVariant->packaging }} ({{ $receipt->productVariant->items_per_package }} Bottles/{{ \Illuminate\Support\Str::singular($receipt->productVariant->packaging_name ?? $receipt->productVariant->packaging) }})</p>
            </div>
        </div>
        
        <table class="details-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: center;">Quantity</th>
                    <th style="text-align: right;">Unit Price (Buying)</th>
                    <th style="text-align: right;">Total Buying Cost</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        {{ $receipt->product->name }} - {{ $receipt->productVariant->measurement }}
                        <br><small class="text-muted">{{ number_format($receipt->total_bottles) }} Total Bottles</small>
                    </td>
                    <td style="text-align: center;">{{ number_format($receipt->quantity_received_packages) }} {{ str_plural($receipt->productVariant->packaging_name ?? $receipt->productVariant->packaging) }}</td>
                    <td style="text-align: right;">{{ number_format($receipt->buying_price_per_bottle, 2) }} TSh</td>
                    <td style="text-align: right;">{{ number_format($receipt->total_buying_cost, 2) }} TSh</td>
                </tr>
            </tbody>
        </table>

        <div class="receipt-info">
            <div class="info-block">
                <h3>Pricing & Valuation</h3>
                <p><strong>Selling Price:</strong> {{ number_format($receipt->selling_price_per_bottle, 2) }} TSh / Bottle</p>
                <p><strong>Expected Revenue:</strong> {{ number_format($receipt->total_bottles * $receipt->selling_price_per_bottle, 2) }} TSh</p>
                <p><strong>Projected Profit:</strong> {{ number_format($receipt->total_profit, 2) }} TSh</p>
            </div>
            @if($receipt->notes)
            <div class="info-block">
                <h3>Notes</h3>
                <p>{{ $receipt->notes }}</p>
            </div>
            @endif
        </div>
        
        <div class="total-section">
            <div class="total-row total">
                <span>Total Buying Amount:</span>
                <span>{{ number_format($receipt->total_buying_cost, 2) }} TSh</span>
            </div>
        </div>
        
        <div class="footer">
            <p>This is a computer-generated stock receipt.</p>
            <p>Umoj Lutheran Hostel - Excellence in Hospitality</p>
            <p class="powered-by">Powered By EmCa Technologies</p>
        </div>
    </div>
    
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
