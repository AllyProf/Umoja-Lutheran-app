<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Unpaid Guests Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #111; font-size: 11px; }
        h1 { font-size: 18px; margin: 0 0 4px; }
        h2 { font-size: 13px; margin: 16px 0 6px; }
        .sub { color: #555; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        th, td { border: 1px solid #333; padding: 6px 8px; }
        .summary th { background: #f3f3f3; text-align: left; width: 45%; }
        .summary td { text-align: right; font-weight: bold; }
        .guests th { background: #f3f3f3; text-align: left; font-size: 10px; }
        .guests td { font-size: 10px; vertical-align: top; }
        .guests .num { text-align: right; white-space: nowrap; }
        .guests .center { text-align: center; }
        .empty { color: #666; font-style: italic; margin-top: 8px; }
        .footer { margin-top: 18px; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <h1>UMOJA LUTHERAN HOSTEL</h1>
    <div class="sub">
        Unpaid Guests Report
        @if(!empty($report['is_manager']))
            — All counters
        @else
            — {{ $report['staff_name'] ?? 'Counter' }}
        @endif
    </div>

    <table class="summary">
        <tr><th>Period</th><td>{{ $report['period_label'] ?? 'Daily' }}</td></tr>
        <tr><th>Date range</th><td>{{ $report['range_label'] ?? '—' }}</td></tr>
        <tr><th>Unpaid guests</th><td>{{ $report['guest_count'] ?? 0 }}</td></tr>
        <tr><th>Unpaid orders</th><td>{{ $report['order_count'] ?? 0 }}</td></tr>
        <tr><th>Total amount due</th><td>{{ number_format($report['unpaid_total'] ?? 0) }} TZS</td></tr>
    </table>

    <h2>Guests Who Owe</h2>
    @php $guests = $report['guests'] ?? []; @endphp
    @if(count($guests))
        <table class="guests">
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
                @foreach($guests as $i => $guest)
                    <tr>
                        <td class="center">{{ $i + 1 }}</td>
                        <td><strong>{{ $guest['guest'] ?? '—' }}</strong></td>
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
        <div class="empty">Hakuna mgeni anayedaiwa kwa kipindi hiki.</div>
    @endif

    <div class="footer">Generated {{ now()->format('d M Y H:i') }}</div>
</body>
</html>
