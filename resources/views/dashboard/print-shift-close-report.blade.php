<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Shift Sales Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #111; font-size: 12px; }
        h1 { font-size: 18px; margin: 0 0 4px; }
        .sub { color: #555; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #333; padding: 8px 10px; }
        th { background: #f3f3f3; text-align: left; width: 45%; }
        td { text-align: right; font-weight: bold; }
        .footer { margin-top: 20px; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <h1>UMOJA LUTHERAN HOSTEL</h1>
    <div class="sub">Shift Sales Report — {{ $report['staff_name'] ?? '' }}</div>
    <table>
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
    </table>
    <div class="footer">Generated {{ now()->format('d M Y H:i') }}</div>
</body>
</html>
