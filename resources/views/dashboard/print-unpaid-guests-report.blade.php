<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Unpaid Guests Report</title>
    <style>
        @page { margin: 24px 28px; }
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #222;
            font-size: 11px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #940000;
            padding-bottom: 12px;
            margin-bottom: 14px;
        }
        .header img { max-height: 52px; margin-bottom: 6px; }
        .header h1 {
            color: #940000;
            font-size: 20px;
            margin: 0 0 4px;
        }
        .header p { margin: 1px 0; color: #555; font-size: 10px; }
        .header .doc-type {
            margin-top: 8px;
            font-size: 14px;
            font-weight: bold;
            color: #940000;
        }
        .report-no {
            text-align: right;
            color: #666;
            font-size: 10px;
            margin-bottom: 8px;
        }
        .title-row {
            text-align: center;
            margin: 8px 0 16px;
        }
        .title-row .main {
            font-size: 15px;
            font-weight: bold;
            color: #333;
            letter-spacing: 0.4px;
        }
        .stamp {
            display: inline-block;
            margin-left: 8px;
            color: #28a745;
            border: 2px solid #28a745;
            padding: 1px 8px;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .cols { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .cols td { width: 50%; vertical-align: top; padding: 0 6px; }
        .box {
            border: 1px solid #ddd;
            padding: 10px 12px;
            background: #fafafa;
        }
        .box h3 {
            margin: 0 0 8px;
            font-size: 12px;
            color: #940000;
            border-bottom: 1px solid #e5e5e5;
            padding-bottom: 4px;
        }
        .row { margin-bottom: 4px; }
        .label { color: #666; display: inline-block; min-width: 110px; }
        .value { font-weight: bold; color: #222; }
        .section { margin: 16px 0 10px; }
        .section h3 {
            margin: 0 0 8px;
            font-size: 12px;
            color: #940000;
            border-bottom: 2px solid #940000;
            padding-bottom: 4px;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
        }
        table.data th {
            background: #f8f9fa;
            border: 1px solid #000;
            padding: 5px 6px;
            font-size: 9px;
            text-align: left;
            text-transform: uppercase;
        }
        table.data td {
            border: 1px solid #000;
            padding: 5px 6px;
            font-size: 10px;
            vertical-align: top;
        }
        .center { text-align: center; }
        .right { text-align: right; }
        .total-row td {
            background: #fff5f5;
            font-weight: bold;
        }
        .summary-wrap {
            margin-top: 18px;
            border: 1px solid #ddd;
            background: #f8f9fa;
            padding: 12px;
            text-align: center;
        }
        .summary-wrap h3 {
            margin: 0 0 8px;
            color: #333;
            font-size: 12px;
        }
        .due-box {
            display: inline-block;
            border: 2px solid #dc3545;
            background: #fff;
            padding: 10px 18px;
        }
        .due-box .k {
            display: block;
            font-size: 9px;
            color: #dc3545;
            text-transform: uppercase;
            font-weight: bold;
        }
        .due-box .v {
            display: block;
            font-size: 18px;
            font-weight: bold;
            color: #dc3545;
        }
        .sigs { width: 100%; margin-top: 36px; border-collapse: collapse; }
        .sigs td { width: 50%; text-align: center; padding: 0 24px; }
        .sig-line {
            border-top: 1px solid #333;
            margin: 40px 20px 6px;
        }
        .footer {
            margin-top: 28px;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            font-size: 10px;
            color: #666;
        }
        .footer .powered { color: #940000; font-weight: bold; margin-top: 6px; }
        .muted { color: #666; font-style: italic; }
        .status-unpaid { color: #dc3545; font-weight: bold; }
    </style>
</head>
<body>
@php
    $guests = $report['guests'] ?? [];
    $scope = !empty($report['is_manager']) ? 'All counters' : ($report['staff_name'] ?? 'Counter');
    $reportNo = 'UNPAID-' . date('Ymd') . '-' . strtoupper(substr(md5(($report['range_label'] ?? '') . $scope), 0, 6));
@endphp

<div class="header">
    <h1>Umoja Lutheran Hostel</h1>
    <p><strong>Location:</strong> Sokoine Road - Moshi, Kilimanjaro - Tanzania</p>
    <p><strong>Mobile/WhatsApp:</strong> 0677-155-156 / +255 677-155-157</p>
    <p><strong>Email:</strong> info@umojalutheranhostel.co.tz</p>
    <div class="doc-type">Counter Operations Report</div>
</div>

<div class="report-no"><strong>Report #:</strong> {{ $reportNo }}</div>

<div class="title-row">
    <span class="main">UNPAID ORDERS SHEET</span>
    <span class="stamp">OFFICIAL</span>
</div>

<table class="cols">
    <tr>
        <td>
            <div class="box">
                <h3>Report Information</h3>
                <div class="row"><span class="label">Report Type:</span> <span class="value">Unpaid Guests Report</span></div>
                <div class="row"><span class="label">Period:</span> <span class="value">{{ $report['period_label'] ?? 'Daily' }}</span></div>
                <div class="row"><span class="label">Date Range:</span> <span class="value">{{ $report['range_label'] ?? '—' }}</span></div>
                <div class="row"><span class="label">Scope:</span> <span class="value">{{ $scope }}</span></div>
                <div class="row"><span class="label">Generated:</span> <span class="value">{{ now()->format('d M Y H:i') }}</span></div>
            </div>
        </td>
        <td>
            <div class="box">
                <h3>Sales Statistics</h3>
                <div class="row"><span class="label">Unpaid Guests:</span> <span class="value" style="color:#dc3545;">{{ number_format($report['guest_count'] ?? 0) }}</span></div>
                <div class="row"><span class="label">Unpaid Orders:</span> <span class="value">{{ number_format($report['order_count'] ?? 0) }}</span></div>
                <div class="row"><span class="label">Total Amount Due:</span> <span class="value" style="color:#940000;">{{ number_format($report['unpaid_total'] ?? 0) }} TZS</span></div>
            </div>
        </td>
    </tr>
</table>

<div class="section">
    <h3>1. Unpaid Guests
        @if(($report['guest_count'] ?? 0) > 0)
            — {{ $report['guest_count'] }} guest(s), {{ number_format($report['unpaid_total'] ?? 0) }} TZS due
        @endif
    </h3>
    @if(count($guests))
        <table class="data">
            <thead>
                <tr>
                    <th style="width:5%;">#</th>
                    <th>Guest / Room</th>
                    <th>Items</th>
                    <th class="center" style="width:10%;">Orders</th>
                    <th class="right" style="width:16%;">Amount Due</th>
                </tr>
            </thead>
            <tbody>
                @foreach($guests as $i => $guest)
                    <tr>
                        <td class="center">{{ $i + 1 }}</td>
                        <td><strong>{{ $guest['guest'] ?? '—' }}</strong></td>
                        <td>
                            @foreach(($guest['items'] ?? []) as $item)
                                <div>
                                    {{ $item['time'] ?? '' }}
                                    {{ $item['item'] ?? 'Item' }}
                                    ×{{ rtrim(rtrim(number_format($item['qty'] ?? 0, 2, '.', ''), '0'), '.') }}
                                    — {{ number_format($item['amount'] ?? 0) }}
                                </div>
                            @endforeach
                        </td>
                        <td class="center">{{ $guest['orders'] ?? 0 }}</td>
                        <td class="right status-unpaid">{{ number_format($guest['amount'] ?? 0) }} TZS</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="4" class="right">TOTAL UNPAID:</td>
                    <td class="right status-unpaid">{{ number_format($report['unpaid_total'] ?? 0) }} TZS</td>
                </tr>
            </tbody>
        </table>
    @else
        <p class="muted">No unpaid guests for this period.</p>
    @endif
</div>

<div class="summary-wrap">
    <h3>FINANCIAL SUMMARY</h3>
    <div class="due-box">
        <span class="k">Unpaid Due</span>
        <span class="v">{{ number_format($report['unpaid_total'] ?? 0) }} TZS</span>
    </div>
</div>

<table class="sigs">
    <tr>
        <td>
            <div class="sig-line"></div>
            <strong>COUNTER</strong><br>
            <span style="font-size:10px;">(Signature &amp; Date)</span>
        </td>
        <td>
            <div class="sig-line"></div>
            <strong>HOTEL MANAGER</strong><br>
            <span style="font-size:10px;">(Signature &amp; Date)</span>
        </td>
    </tr>
</table>

<div class="footer">
    <div><strong>Umoja Lutheran Hostel Management System</strong></div>
    <div>This is an official unpaid orders report generated for auditing purposes.</div>
    <div class="powered">Powered By EmCa Technologies</div>
</div>
</body>
</html>
