<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Shift Sales Report</title>
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
            background: #f0f0f0;
            font-weight: bold;
        }
        .summary-wrap {
            margin-top: 18px;
            border: 1px solid #ddd;
            background: #f8f9fa;
            padding: 12px;
        }
        .summary-wrap h3 {
            text-align: center;
            margin: 0 0 10px;
            color: #333;
            font-size: 12px;
            border: 0;
        }
        .summary-table { width: 100%; border-collapse: collapse; }
        .summary-table td {
            text-align: center;
            padding: 8px 4px;
            vertical-align: top;
            width: 25%;
        }
        .summary-table .k {
            display: block;
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .summary-table .v {
            display: block;
            font-size: 13px;
            font-weight: bold;
        }
        .card {
            border: 2px solid #ccc;
            background: #fff;
            padding: 8px 6px;
        }
        .card.ok { border-color: #28a745; }
        .card.warn { border-color: #dc3545; }
        .card .v.ok { color: #28a745; }
        .card .v.warn { color: #dc3545; }
        .card .v.blue { color: #007bff; }
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
        .status-paid { color: #28a745; font-weight: bold; }
        .status-unpaid { color: #dc3545; font-weight: bold; }
    </style>
</head>
<body>
@php
    $items = $report['items'] ?? [];
    $unpaidGuests = $report['unpaid_guests'] ?? [];
    $reportNo = 'SHIFT-' . ($report['shift_id'] ?? date('Ymd')) . '-' . strtoupper(substr(md5(($report['staff_name'] ?? '') . ($report['closed_at'] ?? '')), 0, 6));
@endphp

<div class="header">
    <h1>Umoja Lutheran Hostel</h1>
    <p><strong>Location:</strong> Sokoine Road - Moshi, Kilimanjaro - Tanzania</p>
    <p><strong>Mobile/WhatsApp:</strong> 0677-155-156 / +255 677-155-157</p>
    <p><strong>Email:</strong> info@umojalutheranhostel.co.tz</p>
    <div class="doc-type">{{ ($report['shift_type'] ?? 'counter') === 'reception' ? 'Reception Operations Report' : 'Counter Operations Report' }}</div>
</div>

<div class="report-no"><strong>Report #:</strong> {{ $reportNo }}</div>

<div class="title-row">
    <span class="main">SHIFT CLOSE SALES SHEET</span>
    <span class="stamp">OFFICIAL</span>
</div>

<table class="cols">
    <tr>
        <td>
            <div class="box">
                <h3>Report Information</h3>
                <div class="row"><span class="label">Report Type:</span> <span class="value">Shift Close Report</span></div>
                <div class="row"><span class="label">{{ ($report['shift_type'] ?? 'counter') === 'reception' ? 'Reception:' : 'Counter:' }}</span> <span class="value">{{ $report['staff_name'] ?? 'Staff' }}</span></div>
                <div class="row"><span class="label">Opening Time:</span> <span class="value">{{ $report['opened_at'] ?? '—' }}</span></div>
                <div class="row"><span class="label">Closing Time:</span> <span class="value">{{ $report['closed_at'] ?? '—' }}</span></div>
                <div class="row"><span class="label">Duration:</span> <span class="value">{{ $report['duration_label'] ?? '—' }}</span></div>
                <div class="row"><span class="label">Generated:</span> <span class="value">{{ now()->format('d M Y H:i') }}</span></div>
            </div>
        </td>
        <td>
            <div class="box">
                <h3>Sales Statistics</h3>
                <div class="row"><span class="label">Orders Sold:</span> <span class="value" style="color:#28a745;">{{ number_format($report['count'] ?? 0) }}</span></div>
                <div class="row"><span class="label">Total Guests:</span> <span class="value">{{ number_format($report['total_guests'] ?? 0) }}</span></div>
                <div class="row"><span class="label">Total Sales:</span> <span class="value" style="color:#940000;">{{ number_format($report['total_sales'] ?? 0) }} TZS</span></div>
                <div class="row"><span class="label">Unpaid Orders:</span> <span class="value" style="color:#dc3545;">{{ number_format($report['unpaid_guest_count'] ?? 0) }} guests ({{ number_format($report['unpaid_total'] ?? 0) }} TZS)</span></div>
                <div class="row"><span class="label">Prior Debt Collected:</span> <span class="value" style="color:#856404;">{{ number_format($report['debt_collections_count'] ?? 0) }} ({{ number_format($report['debt_collections_total'] ?? 0) }} TZS)</span></div>
                <div class="row"><span class="label">Cash Submitted:</span> <span class="value">{{ number_format($report['amount_submitted'] ?? 0) }} TZS</span></div>
                <div class="row"><span class="label">Difference:</span> <span class="value">{{ number_format($report['difference'] ?? 0) }} TZS</span></div>
            </div>
        </td>
    </tr>
</table>

@if(!empty($report['reception_breakdown']))
@php $rb = $report['reception_breakdown']; @endphp
<div class="section">
    <h3>1. Collections by Source</h3>
    <table class="data">
        <thead>
            <tr>
                <th>Source</th>
                <th class="right">Cash</th>
                <th class="right">M-Pesa</th>
                <th class="right">Other</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Room Bookings</td>
                <td class="right">{{ number_format($rb['bookings']['cash'] ?? 0) }}</td>
                <td class="right">{{ number_format($rb['bookings']['mpesa'] ?? 0) }}</td>
                <td class="right">{{ number_format($rb['bookings']['other'] ?? 0) }}</td>
                <td class="right">{{ number_format($rb['bookings']['total'] ?? 0) }}</td>
            </tr>
            <tr>
                <td>Restaurant / Bar (paid at reception)</td>
                <td class="right">{{ number_format($rb['restaurant']['cash'] ?? 0) }}</td>
                <td class="right">{{ number_format($rb['restaurant']['mpesa'] ?? 0) }}</td>
                <td class="right">{{ number_format($rb['restaurant']['other'] ?? 0) }}</td>
                <td class="right">{{ number_format($rb['restaurant']['total'] ?? 0) }}</td>
            </tr>
            <tr>
                <td>Day Services</td>
                <td class="right">{{ number_format($rb['day_services']['cash'] ?? 0) }}</td>
                <td class="right">{{ number_format($rb['day_services']['mpesa'] ?? 0) }}</td>
                <td class="right">{{ number_format($rb['day_services']['other'] ?? 0) }}</td>
                <td class="right">{{ number_format($rb['day_services']['total'] ?? 0) }}</td>
            </tr>
            <tr>
                <td>Other Services</td>
                <td class="right">{{ number_format($rb['other_services']['cash'] ?? 0) }}</td>
                <td class="right">{{ number_format($rb['other_services']['mpesa'] ?? 0) }}</td>
                <td class="right">{{ number_format($rb['other_services']['other'] ?? 0) }}</td>
                <td class="right">{{ number_format($rb['other_services']['total'] ?? 0) }}</td>
            </tr>
            <tr class="total-row">
                <td class="right">TOTAL:</td>
                <td class="right">{{ number_format($rb['total_cash'] ?? 0) }}</td>
                <td class="right">{{ number_format($rb['total_mpesa'] ?? 0) }}</td>
                <td class="right">{{ number_format($rb['total_other'] ?? 0) }}</td>
                <td class="right" style="color:#940000;">{{ number_format($rb['grand_total'] ?? 0) }} TZS</td>
            </tr>
        </tbody>
    </table>
</div>
@endif

<div class="section">
    <h3>{{ !empty($report['reception_breakdown']) ? '2. Itemized Sales (if any)' : '1. Direct Sales (Paid Orders)' }}</h3>
    @if(count($items))
        <table class="data">
            <thead>
                <tr>
                    <th style="width:5%;">#</th>
                    <th style="width:10%;">Time</th>
                    <th>Item</th>
                    <th>Guest</th>
                    <th class="center" style="width:8%;">Qty</th>
                    <th style="width:14%;">Payment</th>
                    <th class="right" style="width:16%;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $i => $item)
                    @php $fromDebt = !empty($item['from_prior_debt']); @endphp
                    <tr @if($fromDebt) style="background:#fff8e6;" @endif>
                        <td class="center">{{ $i + 1 }}</td>
                        <td>{{ $item['time'] ?? '—' }}</td>
                        <td>
                            {{ $item['item'] ?? 'Item' }}
                            @if($fromDebt)
                                <div style="font-size:8px;color:#856404;font-weight:bold;">PAID FROM PRIOR DEBT
                                    @if(!empty($item['original_order_at']))
                                        (ordered {{ \Carbon\Carbon::parse($item['original_order_at'])->format('d M Y') }})
                                    @endif
                                </div>
                            @endif
                        </td>
                        <td>{{ $item['guest'] ?? '—' }}</td>
                        <td class="center">{{ rtrim(rtrim(number_format($item['qty'] ?? 0, 2, '.', ''), '0'), '.') }}</td>
                        <td><span class="status-paid">{{ $item['payment'] ?? 'PAID' }}</span></td>
                        <td class="right">{{ number_format($item['amount'] ?? 0) }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="6" class="right">TOTAL DIRECT SALES:</td>
                    <td class="right" style="color:#940000;">{{ number_format($report['total_sales'] ?? 0) }} TZS</td>
                </tr>
            </tbody>
        </table>
    @else
        <p class="muted">No paid items were recorded for this shift.</p>
    @endif
</div>

<div class="section">
    <h3>{{ !empty($report['reception_breakdown']) ? '3' : '2' }}. Unpaid Orders
        @if(($report['unpaid_guest_count'] ?? 0) > 0)
            — {{ $report['unpaid_guest_count'] }} guest(s), {{ number_format($report['unpaid_total'] ?? 0) }} TZS due
        @endif
    </h3>
    @if(count($unpaidGuests))
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
                @foreach($unpaidGuests as $i => $guest)
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
        <p class="muted">No unpaid orders for this shift.</p>
    @endif
</div>

<div class="summary-wrap">
    <h3>SHIFT FINANCIAL SUMMARY</h3>
    <table class="summary-table">
        <tr>
            <td>
                <div class="card">
                    <span class="k">Cash</span>
                    <span class="v blue">{{ number_format($report['total_cash'] ?? 0) }} TZS</span>
                </div>
            </td>
            <td>
                <div class="card">
                    <span class="k">M-Pesa</span>
                    <span class="v blue">{{ number_format($report['total_mpesa'] ?? 0) }} TZS</span>
                </div>
            </td>
            <td>
                <div class="card">
                    <span class="k">Other</span>
                    <span class="v blue">{{ number_format($report['total_other'] ?? 0) }} TZS</span>
                </div>
            </td>
            <td>
                <div class="card ok">
                    <span class="k">Total Sales</span>
                    <span class="v ok">{{ number_format($report['total_sales'] ?? 0) }} TZS</span>
                </div>
            </td>
        </tr>
        <tr>
            <td style="padding-top:10px;">
                <div class="card warn">
                    <span class="k">Unpaid Due</span>
                    <span class="v warn">{{ number_format($report['unpaid_total'] ?? 0) }} TZS</span>
                </div>
            </td>
            <td style="padding-top:10px;">
                <div class="card">
                    <span class="k">Prior Debt Collected</span>
                    <span class="v" style="color:#856404;">{{ number_format($report['debt_collections_total'] ?? 0) }} TZS</span>
                </div>
            </td>
            <td style="padding-top:10px;">
                <div class="card">
                    <span class="k">Cash Submitted</span>
                    <span class="v">{{ number_format($report['amount_submitted'] ?? 0) }} TZS</span>
                </div>
            </td>
            <td style="padding-top:10px;">
                <div class="card">
                    <span class="k">Difference</span>
                    <span class="v">{{ number_format($report['difference'] ?? 0) }} TZS</span>
                </div>
            </td>
        </tr>
    </table>
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
    <div>This is an official counter shift report generated for auditing purposes.</div>
    <div class="powered">Powered By EmCa Technologies</div>
</div>
</body>
</html>
