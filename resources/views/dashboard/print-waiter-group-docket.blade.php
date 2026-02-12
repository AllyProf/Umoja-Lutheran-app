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
            max-width: 400px;
            margin: 0 auto;
            background: #fff;
        }
        .docket {
            border: 2px solid #e07632;
            padding: 15px;
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
            margin: 10px 0;
            font-size: 14px;
            padding: 8px;
            background: #f9f9f9;
            border-left: 3px solid #e07632;
        }
        .item-header {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .item-qty {
            color: #e07632;
        }
        .item-note {
            font-size: 11px;
            font-style: italic;
            color: #666;
            margin-top: 5px;
        }
        .total-section {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 2px solid #e07632;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 18px;
            font-weight: bold;
            color: #e07632;
            margin: 10px 0;
        }
        .footer {
            text-align: center;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 2px dashed #e07632;
            font-size: 11px;
            color: #666;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
        .watermark {
            position: absolute;
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-35deg);
            font-size: 60px;
            color: rgba(40, 167, 69, 0.3);
            border: 8px solid rgba(40, 167, 69, 0.3);
            padding: 5px 30px;
            font-weight: 900;
            z-index: 999;
            pointer-events: none;
            white-space: nowrap;
            letter-spacing: 5px;
        }
    </style>
</head>
<body>
    <div class="docket">
        @php
            $isPaid = $orders->every(fn($o) => in_array($o->payment_status, ['paid', 'room_charge']));
        @endphp
        
        @if($isPaid)
            <div class="watermark">PAID</div>
        @endif
        <div class="header">
            <h1>🏨 Umoj Lutheran Hostel</h1>
            <p>Moshi, Kilimanjaro, Tanzania</p>
            <p>Tel: +255 XXX XXX XXX</p>
            <p style="margin-top: 10px; font-weight: bold; font-size: 13px;">GUEST BILL</p>
        </div>

        <div class="section">
            <div class="info-row">
                <span><strong>Guest:</strong></span>
                <span>{{ $guestName }}</span>
            </div>
            <div class="info-row">
                <span><strong>Location:</strong></span>
                <span>{{ $destination }}</span>
            </div>
            <div class="info-row">
                <span><strong>Served By:</strong></span>
                <span>{{ $requestedBy }}</span>
            </div>
            <div class="info-row">
                <span><strong>Date:</strong></span>
                <span>{{ $first->requested_at->format('M d, Y H:i') }}</span>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Items Ordered</div>
            @php
                // Group items by Name AND Payment Status to separate Paid vs Pending
                $groupedItems = $orders->groupBy(function($item) {
                     $name = $item->service_specific_data['item_name'] ?? $item->service->name;
                     $isPaid = in_array($item->payment_status, ['paid', 'room_charge']);
                     return $name . '|' . ($isPaid ? 'PAID' : 'PENDING');
                });
            @endphp

            @foreach($groupedItems as $groupKey => $items)
                @php
                    list($itemName, $status) = explode('|', $groupKey);
                    
                    $qty = $items->sum('quantity');
                    $total = $items->sum('total_price_tsh');
                    $unitPrice = $qty > 0 ? $total / $qty : 0;
                    
                    // Collect and clean notes
                    $notes = [];
                    foreach($items as $item) {
                        $rawNote = $item->guest_request;
                        
                        // Fallback to reception notes if guest_request is empty
                        if (!$rawNote && $item->reception_notes && str_contains($item->reception_notes, '- Msg: ')) {
                             $parts = explode('- Msg: ', $item->reception_notes);
                             $rawNote = $parts[1] ?? null;
                        }
                        
                        if ($rawNote) {
                            // Clean system messages (everything after |)
                            $cleanNote = trim(explode('|', $rawNote)[0]);
                            // Also remove "Completed by Kitchen" if it appears directly
                            $cleanNote = trim(explode('Completed by', $cleanNote)[0]);
                            
                            if ($cleanNote && !in_array($cleanNote, $notes)) {
                                $notes[] = $cleanNote;
                            }
                        }
                    }
                @endphp
                <div class="item-row">
                    <div class="item-header">
                        <span>
                            {{ $itemName }}
                            @if($status === 'PAID')
                                <span style="font-size: 10px; color: green; border: 1px solid green; padding: 1px 3px; border-radius: 2px; margin-left: 5px;">PAID</span>
                            @else
                                <span style="font-size: 10px; color: #e07632; border: 1px solid #e07632; padding: 1px 3px; border-radius: 2px; margin-left: 5px;">PENDING</span>
                            @endif
                        </span>
                        <span>{{ number_format($total) }} TZS</span>
                    </div>
                    <div class="info-row">
                        <span class="item-qty">Qty: {{ $qty }}</span>
                        <span>@ {{ number_format($unitPrice) }} TZS</span>
                    </div>
                    @foreach($notes as $note)
                        <div class="item-note">Note: {{ $note }}</div>
                    @endforeach
                </div>
            @endforeach
        </div>

        <div class="total-section">
            <div class="total-row">
                <span>TOTAL:</span>
                <span>{{ number_format($totalAmount) }} TZS</span>
            </div>
            
            @php
                $paidAmount = $orders->whereIn('payment_status', ['paid', 'room_charge'])->sum('total_price_tsh');
                $pendingAmount = $totalAmount - $paidAmount;
            @endphp

            @if($paidAmount > 0 && $pendingAmount > 0)
                <div class="info-row" style="color: green; font-weight: bold; justify-content: space-between; font-size: 14px;">
                    <span>PAID:</span>
                    <span>{{ number_format($paidAmount) }} TZS</span>
                </div>
            @endif

            @if($pendingAmount > 0)
                <div class="total-row" style="color: #d35400; border-top: 1px dashed #ccc; padding-top: 5px; margin-top: 5px;">
                    <span>PENDING:</span>
                    <span>{{ number_format($pendingAmount) }} TZS</span>
                </div>
            @endif
        </div>

        <div class="footer">
            <p>Thank you for dining with us!</p>
            <p style="margin-top: 5px;">Printed: {{ now()->format('M d, Y H:i') }}</p>
        </div>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #e07632; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px;">
            🖨️ Print Bill
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #666; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; margin-left: 10px;">
            Close
        </button>
    </div>

    <script>
        // Auto-print on load (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
