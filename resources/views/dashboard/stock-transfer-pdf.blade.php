<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Transfer Note - {{ $transfer->transfer_reference }} - Umoj Lutheran Hostel</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        @media print {
            @page {
                margin: 0 !important;
                size: A4;
            }
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
        .header h1 { color: #e07632; font-size: 28px; margin-bottom: 5px; font-weight: bold; }
        .header p { color: #666; font-size: 12px; margin: 2px 0; }
        
        .title-box {
            text-align: center;
            margin-bottom: 30px;
        }
        .title-box h2 {
            display: inline-block;
            border: 2px solid #333;
            padding: 5px 20px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .info-grid {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .info-col { flex: 1; }
        .info-col h3 {
            color: #e07632;
            font-size: 14px;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        .info-item { margin-bottom: 5px; font-size: 11px; }

        .details-table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        .details-table th, .details-table td { padding: 12px; text-align: left; border: 1px solid #ddd; }
        .details-table th { background-color: #f8f9fa; font-weight: bold; }

        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 200px;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-bottom: 5px;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            border-top: 2px solid #e07632;
            padding-top: 10px;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="header">
            <h1>Umoj Lutheran Hostel</h1>
            <p>Sokoine Road - Moshi Kilimanjaro - Tanzania</p>
            <p>Phone: 0677-155-156 / +255 677-155-157</p>
        </div>

        <div class="title-box">
            <h2>Stock Transfer Note</h2>
            <p>Ref: #{{ $transfer->transfer_reference }}</p>
        </div>

        <div class="info-grid">
            <div class="info-col">
                <h3>Transfer Details</h3>
                <div class="info-item"><strong>Date:</strong> {{ $transfer->transfer_date->format('M d, Y') }}</div>
                <div class="info-item"><strong>Status:</strong> {{ ucfirst($transfer->status) }}</div>
                <div class="info-item"><strong>Transferred By:</strong> {{ $transfer->transferredBy->name ?? 'N/A' }}</div>
            </div>
            <div class="info-col" style="padding-left: 50px;">
                <h3>Receiver</h3>
                <div class="info-item"><strong>Name:</strong> {{ $transfer->receivedBy->name ?? 'N/A' }}</div>
                <div class="info-item"><strong>Role:</strong> Bar Keeper</div>
            </div>
        </div>

        <table class="details-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Package Details</th>
                    <th style="text-align: center;">Quantity</th>
                    <th style="text-align: right;">Valuation (Selling)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>{{ $transfer->product->name }}</strong>
                        <br><small>{{ $transfer->productVariant->measurement }}</small>
                    </td>
                    <td>
                        {{ $transfer->productVariant->packaging_name ?? $transfer->productVariant->packaging }}
                        <br><small>({{ $transfer->productVariant->items_per_package }} Bottles/{{ \Illuminate\Support\Str::singular($transfer->productVariant->packaging_name ?? $transfer->productVariant->packaging) }})</small>
                    </td>
                    <td style="text-align: center;">
                        {{ number_format($transfer->quantity_transferred) }} {{ $transfer->quantity_unit_name }}
                        <br><small class="text-info">({{ number_format($transfer->total_bottles) }} Bottles)</small>
                    </td>
                    <td style="text-align: right;">
                        {{ number_format($transfer->selling_price, 2) }} / Bottle
                        <br><strong>Collection: {{ number_format($transfer->expected_revenue, 2) }} TSh</strong>
                        <br><small class="text-success">Est. Profit: {{ number_format($transfer->expected_profit, 2) }} TSh</small>
                    </td>
                </tr>
            </tbody>
        </table>

        @if($transfer->notes)
        <div style="margin-bottom: 30px;">
            <h3>Notes:</h3>
            <p style="font-size: 11px; font-style: italic;">{{ $transfer->notes }}</p>
        </div>
        @endif

        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line"></div>
                <p>Issued By (Store)</p>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <p>Received By (Bar)</p>
            </div>
        </div>

        <div class="footer">
            <p>Umoj Lutheran Hostel - Excellence in Hospitality</p>
            <p>Powered By EmCa Technologies</p>
        </div>
    </div>

    <script>
        window.onload = function() { window.print(); };
    </script>
</body>
</html>
