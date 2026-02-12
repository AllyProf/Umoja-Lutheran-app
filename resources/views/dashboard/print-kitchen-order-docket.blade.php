<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest Bill - {{ $guestName }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', monospace;
            padding: 20px;
            max-width: 400px; /* Thinner for thermal printer style */
            margin: 0 auto;
            background: #fff;
            position: relative;
        }
        .docket {
            border: 2px solid #e07632; /* Primary Color */
            padding: 15px;
            position: relative;
            background: #fff;
        }
        .header {
            text-align: center;
            border-bottom: 2px dashed #e07632;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header h1 {
            font-size: 20px;
            margin-bottom: 5px;
            color: #e07632;
            text-transform: uppercase;
        }
        .header p {
            font-size: 11px;
            margin: 2px 0;
            color: #333;
        }
        .section {
            margin: 10px 0;
            border-bottom: 1px dashed #e07632;
            padding-bottom: 10px;
        }
        .section-title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 8px;
            text-align: center;
            text-transform: uppercase;
            color: #e07632;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
            font-size: 13px;
        }
        .item-row {
            margin: 15px 0;
            font-size: 16px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .item-details {
            display: flex;
            align-items: center;
        }
        .item-qty {
            font-size: 16px;
            color: #e07632;
            margin-right: 10px;
            font-weight: bold;
        }
        .notes {
            margin-top: 10px;
            padding: 8px;
            background: #fff9f5;
            border: 1px dashed #ddd;
            font-style: italic;
            font-size: 12px;
            color: #666;
        }
        .payment-info {
            margin: 15px 0;
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            border: 2px solid #e07632;
            background: #fff3e0;
            padding: 10px;
            color: #e07632;
        }
        .total-pay {
            font-size: 20px;
            margin-top: 5px;
            display: block;
            border-top: 1px solid #e07632;
            padding-top: 5px;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 11px;
            color: #333;
        }
        .emca-credit {
            margin-top: 15px;
            font-weight: bold;
            color: #940000;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
            .docket { border-width: 1px; }
        }
    </style>
</head>
<body>
    <div class="docket">
        <!-- Header -->
        <div class="header">
            <h1 style="font-size: 24px;">Umoj Lutheran Hostel</h1>
            <p style="font-weight: bold; letter-spacing: 2px; color: #e07632;">
                @if(in_array(($order->payment_status ?? 'pending'), ['paid', 'room_charge']))
                    GUEST BILL RECEIPT
                @else
                    KITCHEN ORDER DOCKET
                @endif
            </p>
            <p>{{ now()->format('M d, Y - h:i A') }}</p>
        </div>

        <!-- Order Information -->
        <div class="section">
            <div class="info-row">
                <span>Guest Name:</span>
                <strong>{{ $guestName }}</strong>
            </div>
            <div class="info-row">
                <span>Location:</span>
                <strong>{{ str_replace('WALK-IN (', '', str_replace(')', '', $destination)) }}</strong>
            </div>
            <div class="info-row">
                <span>Bill #:</span>
                <strong>{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</strong>
            </div>
            <div class="info-row">
                <span>Requested By:</span>
                <strong>{{ $requestedBy }}</strong>
            </div>
        </div>

        <!-- Billing Items -->
        <div class="section" style="border-bottom: none;">
            <div class="section-title">Order Details</div>
            <div class="item-row">
                <div class="item-details">
                    <span class="item-qty">{{ $order->quantity }}x</span>
                    <span>{{ $itemName }}</span>
                </div>
                <span>{{ number_format($order->unit_price_tsh) }}</span>
            </div>
            
            @if($note)
            <div class="notes">
                <strong>Notes:</strong> {{ $note }}
            </div>
            @endif
        </div>

        <!-- Total Section -->
        <div class="payment-info">
            <div>
                @php
                    $status = strtoupper($order->payment_status ?? 'PENDING');
                    if($status === 'ROOM_CHARGE') $status = 'CHARGED TO ROOM';
                @endphp
                ORDER STATUS: {{ $status }}
            </div>
            <div class="total-pay">
                TOTAL BILL: {{ number_format($order->total_price_tsh) }} TZS
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="font-weight: bold; font-size: 13px; margin-bottom: 5px;">Thank you for dining with us!</p>
            <p>Please keep this receipt for your records.</p>
            <p>Visit again soon!</p>
            <div class="emca-credit">Powered By EmCa Technologies</div>
        </div>
    </div>

    <!-- Print Button -->
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 12px 30px; font-size: 16px; background: #e07632; color: #fff; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <i class="fa fa-print"></i> Print Bill
        </button>
        <button onclick="window.close()" style="padding: 12px 30px; font-size: 16px; background: #6c757d; color: #fff; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px; font-weight: bold;">
            Close
        </button>
    </div>

    <script>
        window.onload = function() {
            // Optional: window.print();
        };
    </script>
</body>
</html>
