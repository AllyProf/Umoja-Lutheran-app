<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Walk-in Docket - {{ $guestName }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', monospace;
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            position: relative;
        }
        .docket {
            border: 2px solid #e77a31;
            padding: 20px;
            position: relative;
            background: #fff;
        }
        .docket::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 650px;
            height: 650px;
            background-image: url('/dashboard_assets/images/Logo.png');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            opacity: 0.05;
            z-index: 0;
            pointer-events: none;
        }
        .docket > * {
            position: relative;
            z-index: 1;
        }
        .header {
            text-align: center;
            border-bottom: 2px dashed #e77a31;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
            color: #e77a31;
        }
        .header p {
            font-size: 12px;
            margin: 2px 0;
        }
        .section {
            margin: 15px 0;
        }
        .section-title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 8px;
            text-decoration: underline;
            color: #e77a31;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
            font-size: 13px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            padding: 8px 5px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            font-size: 13px;
        }
        th {
            background: #fff3e0;
            font-weight: bold;
            color: #e77a31;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-section {
            border-top: 2px solid #e77a31;
            margin-top: 15px;
            padding-top: 10px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 16px;
            font-weight: bold;
            margin: 5px 0;
            color: #e77a31;
        }
        .footer {
            margin-top: 20px;
            border-top: 2px dashed #e77a31;
            padding-top: 15px;
            text-align: center;
            font-size: 11px;
        }
        .footer .emca-credit {
            margin-top: 10px;
            font-size: 10px;
            color: #940000;
            font-weight: bold;
        }
        .payment-status {
            display: inline-block;
            padding: 5px 15px;
            background: #ffc107;
            color: #000;
            font-weight: bold;
            border-radius: 3px;
            margin: 10px 0;
        }
        .payment-status.paid {
            background: #28a745;
            color: #fff;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
            .docket {
                border-color: #000;
            }
            .header {
                border-bottom-color: #000;
            }
            .total-section {
                border-top-color: #000;
            }
            .footer {
                border-top-color: #000;
            }
        }
    </style>
</head>
<body>
    <div class="docket">
        <!-- Header -->
        <div class="header">
            <h1>Umoj Lutheran Hostel</h1>
            <p>Sokoine Road - Moshi, Kilimanjaro - Tanzania</p>
            <p>Mobile/WhatsApp: 0677-155-156 / +255 677-155-157</p>
            <p>Email: info@Umoj Lutheran Hostelhotel.co.tz</p>
        </div>

        <!-- Docket Info -->
        <div class="section">
            <div class="section-title">WALK-IN SALE DOCKET</div>
            <div class="info-row">
                <span>Guest Name:</span>
                <strong>{{ $guestName }}</strong>
            </div>
            <div class="info-row">
                <span>Date & Time:</span>
                <strong>{{ $serviceRequest->created_at->format('M d, Y - h:i A') }}</strong>
            </div>
            <div class="info-row">
                <span>Docket #:</span>
                <strong>{{ str_pad($serviceRequest->id, 6, '0', STR_PAD_LEFT) }}</strong>
            </div>
            <div class="info-row">
                <span>Served By:</span>
                <strong>Bar Keeper</strong>
            </div>
        </div>

        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 10%;">#</th>
                    <th style="width: 50%;">Item Description</th>
                    <th style="width: 10%;" class="text-center">Qty</th>
                    <th style="width: 15%;" class="text-right">Unit Price</th>
                    <th style="width: 15%;" class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->service_specific_data['item_name'] ?? $item->service->name ?? 'Item' }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->unit_price_tsh) }}</td>
                    <td class="text-right">{{ number_format($item->total_price_tsh) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Total Section -->
        <div class="total-section">
            <div class="total-row">
                <span>TOTAL AMOUNT:</span>
                <span>{{ number_format($totalAmount) }} TZS</span>
            </div>
            <div class="text-center" style="margin-top: 10px;">
                @if($serviceRequest->payment_status === 'paid')
                    <div class="payment-status paid">✓ PAID</div>
                @else
                    <div class="payment-status">UNPAID - PENDING PAYMENT</div>
                @endif
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Thank you for your patronage!</strong></p>
            <p>This is a computer-generated docket. No signature required.</p>
            <p style="margin-top: 10px;">For inquiries, please contact us at the numbers above.</p>
            <p class="emca-credit">Powered By EmCa Technologies</p>
        </div>
    </div>

    <!-- Print Button -->
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 30px; font-size: 16px; background: #007bff; color: #fff; border: none; border-radius: 5px; cursor: pointer;">
            <i class="fa fa-print"></i> Print Docket
        </button>
        <button onclick="window.close()" style="padding: 10px 30px; font-size: 16px; background: #6c757d; color: #fff; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">
            Close
        </button>
    </div>

    <script>
        // Auto-focus print dialog on load
        window.onload = function() {
            // Uncomment the line below to auto-print on load
            // window.print();
        };
    </script>
</body>
</html>
