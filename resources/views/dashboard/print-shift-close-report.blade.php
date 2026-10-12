<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Shift Sales Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #111; font-size: 11px; }
        h1 { font-size: 18px; margin: 0 0 4px; }
        h2 { font-size: 13px; margin: 18px 0 6px; }
        .sub { color: #555; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        th, td { border: 1px solid #333; padding: 6px 8px; }
        .summary th { background: #f3f3f3; text-align: left; width: 45%; }
        .summary td { text-align: right; font-weight: bold; }
        .items th { background: #f3f3f3; text-align: left; font-size: 10px; }
        .items td { font-size: 10px; vertical-align: top; }
        .items .num { text-align: right; white-space: nowrap; }
        .items .center { text-align: center; }
        .empty { color: #666; font-style: italic; margin-top: 8px; }
        .footer { margin-top: 18px; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <h1>UMOJA LUTHERAN HOSTEL</h1>
    <div class="sub">Shift Sales Report — {{ $report['staff_name'] ?? '' }}</div>

    <table class="summary">
        <tr><th>Opened at</th><td>{{ $report['opened_at'] ?? '—' }}</td></tr>
        <tr><th>Closed at</th><td>{{ $report['closed_at'] ?? '—' }}</td></tr>
        <tr><th>Duration</th><td>{{ $report['duration_label'] ?? '—' }}</td></tr>
        <tr><th>Total Guests</th><td>{{ $report['total_guests'] ?? 0 }}</td></tr>
        <tr><th>Total Orders</th><td>{{ $report['count'] ?? 0 }}</td></tr>
        <tr><th>Total Sales</th><td>{{ number_format($report['total_sales'] ?? 0) }} TZS</td></tr>
        <tr><th>Cash</th><td>{{ number_format($report['total_cash'] ?? 0) }} TZS</td></tr>
        <tr><th>M-Pesa</th><td>{{ number_format($report['total_mpesa'] ?? 0) }} TZS</td></tr>
        <tr><th>Other</th><td>{{ number_format($report['total_other'] ?? 0) }} TZS</td></tr>
        <tr><th>Cash submitted</th><td>{{ number_format($report['amount_submitted'] ?? 0) }} TZS</td></tr>
        <tr><th>Difference</th><td>{{ number_format($report['difference'] ?? 0) }} TZS</td></tr>
        <tr><th>Unpaid guests</th><td>{{ $report['unpaid_guest_count'] ?? 0 }}</td></tr>
        <tr><th>Unpaid total</th><td>{{ number_format($report['unpaid_total'] ?? 0) }} TZS</td></tr>
    </table>

    <h2>Items Sold</h2>
    @php $items = $report['items'] ?? []; @endphp
    @if(count($items))
        <table class="items">
            <thead>
                <tr>
                    <th style="width:6%;">#</th>
                    <th style="width:10%;">Time</th>
                    <th>Item</th>
                    <th>Guest</th>
                    <th style="width:8%;" class="center">Qty</th>
                    <th style="width:12%;">Payment</th>
                    <th style="width:16%;" class="num">Amount (TZS)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $i => $item)
                    <tr>
                        <td class="center">{{ $i + 1 }}</td>
                        <td>{{ $item['time'] ?? '—' }}</td>
                        <td>{{ $item['item'] ?? 'Item' }}</td>
                        <td>{{ $item['guest'] ?? '—' }}</td>
                        <td class="center">{{ rtrim(rtrim(number_format($item['qty'] ?? 0, 2, '.', ''), '0'), '.') }}</td>
                        <td>{{ $item['payment'] ?? '—' }}</td>
                        <td class="num">{{ number_format($item['amount'] ?? 0) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="6" style="text-align:right;font-weight:bold;">Total</td>
                    <td class="num" style="font-weight:bold;">{{ number_format($report['total_sales'] ?? 0) }}</td>
                </tr>
            </tbody>
        </table>
    @else
        <div class="empty">No paid items were recorded for this shift.</div>
    @endif

    <h2>Unpaid Guests</h2>
    @php $unpaidGuests = $report['unpaid_guests'] ?? []; @endphp
    @if(count($unpaidGuests))
        <table class="items">
            <thead>
                <tr>
                    <th style="width:6%;">#</th>
                    <th>Guest / Room</th>
                    <th style="width:12%;" class="center">Orders</th>
                    <th style="width:12%;" class="center">Qty</th>
                    <th style="width:18%;" class="num">Amount Due (TZS)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($unpaidGuests as $i => $guest)
                    <tr>
                        <td class="center">{{ $i + 1 }}</td>
                        <td>{{ $guest['guest'] ?? '—' }}</td>
                        <td class="center">{{ $guest['orders'] ?? 0 }}</td>
                        <td class="center">{{ rtrim(rtrim(number_format($guest['qty'] ?? 0, 2, '.', ''), '0'), '.') }}</td>
                        <td class="num">{{ number_format($guest['amount'] ?? 0) }}</td>
                    </tr>
                    @foreach(($guest['items'] ?? []) as $item)
                        <tr>
                            <td></td>
                            <td colspan="3" style="color:#444;">
                                {{ $item['time'] ?? '' }}
                                — {{ $item['item'] ?? 'Item' }}
                                (x{{ rtrim(rtrim(number_format($item['qty'] ?? 0, 2, '.', ''), '0'), '.') }})
                            </td>
                            <td class="num">{{ number_format($item['amount'] ?? 0) }}</td>
                        </tr>
                    @endforeach
                @endforeach
                <tr>
                    <td colspan="4" style="text-align:right;font-weight:bold;">Total unpaid</td>
                    <td class="num" style="font-weight:bold;">{{ number_format($report['unpaid_total'] ?? 0) }}</td>
                </tr>
            </tbody>
        </table>
    @else
        <div class="empty">No unpaid guests for this shift.</div>
    @endif

    <div class="footer">Generated {{ now()->format('d M Y H:i') }}</div>
</body>
</html>
