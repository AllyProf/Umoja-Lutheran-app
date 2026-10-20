<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $report['title'] ?? 'Period Sales Report' }}</title>
    <style>
        @page { margin: 22px 26px; }
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #222;
            font-size: 10.5px;
            line-height: 1.35;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #940000;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }
        .header h1 {
            color: #940000;
            font-size: 18px;
            margin: 0 0 4px;
        }
        .header p { margin: 1px 0; color: #555; font-size: 9.5px; }
        .header .doc-type {
            margin-top: 6px;
            font-size: 13px;
            font-weight: bold;
            color: #940000;
        }
        .report-no {
            text-align: right;
            color: #666;
            font-size: 9.5px;
            margin-bottom: 6px;
        }
        .title-row {
            text-align: center;
            margin: 6px 0 12px;
        }
        .title-row .main {
            font-size: 14px;
            font-weight: bold;
            color: #333;
            letter-spacing: 0.3px;
        }
        .stamp {
            display: inline-block;
            margin-left: 8px;
            color: #28a745;
            border: 2px solid #28a745;
            padding: 1px 7px;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .cols { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .cols td { width: 50%; vertical-align: top; padding: 0 5px; }
        .box {
            border: 1px solid #ddd;
            padding: 8px 10px;
            background: #fafafa;
        }
        .box h3 {
            margin: 0 0 6px;
            font-size: 11px;
            color: #940000;
            border-bottom: 1px solid #e5e5e5;
            padding-bottom: 3px;
        }
        .row { margin-bottom: 3px; }
        .label { color: #666; display: inline-block; min-width: 105px; }
        .value { font-weight: bold; color: #222; }
        .section { margin: 12px 0 8px; page-break-inside: avoid; }
        .section h3 {
            margin: 0 0 6px;
            font-size: 11px;
            color: #940000;
            border-bottom: 2px solid #940000;
            padding-bottom: 3px;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
        }
        table.data th {
            background: #f8f9fa;
            border: 1px solid #000;
            padding: 4px 5px;
            font-size: 8.5px;
            text-align: left;
            text-transform: uppercase;
        }
        table.data td {
            border: 1px solid #000;
            padding: 4px 5px;
            font-size: 9.5px;
            vertical-align: top;
        }
        .center { text-align: center; }
        .right { text-align: right; }
        .total-row td { background: #f0f0f0; font-weight: bold; }
        .summary-wrap {
            margin-top: 14px;
            border: 1px solid #ddd;
            background: #f8f9fa;
            padding: 10px;
        }
        .summary-wrap h3 {
            text-align: center;
            margin: 0 0 8px;
            color: #333;
            font-size: 11px;
            border: 0;
        }
        .summary-table { width: 100%; border-collapse: collapse; }
        .summary-table td {
            text-align: center;
            padding: 6px 3px;
            vertical-align: top;
            width: 25%;
        }
        .card {
            border: 2px solid #ccc;
            background: #fff;
            padding: 7px 5px;
        }
        .card.ok { border-color: #28a745; }
        .card.warn { border-color: #dc3545; }
        .card .k {
            display: block;
            font-size: 8.5px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .card .v {
            display: block;
            font-size: 12px;
            font-weight: bold;
        }
        .card .v.ok { color: #28a745; }
        .card .v.warn { color: #dc3545; }
        .card .v.blue { color: #007bff; }
        .sigs { width: 100%; margin-top: 28px; border-collapse: collapse; }
        .sigs td { width: 50%; text-align: center; padding: 0 20px; }
        .sig-line {
            border-top: 1px solid #333;
            margin: 34px 16px 5px;
        }
        .footer {
            margin-top: 22px;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 8px;
            font-size: 9.5px;
            color: #666;
        }
        .footer .powered { color: #940000; font-weight: bold; margin-top: 5px; }
        .muted { color: #666; font-style: italic; }
        .status-paid { color: #28a745; font-weight: bold; }
        .status-unpaid { color: #dc3545; font-weight: bold; }
    </style>
</head>
<body>
@php
    $items = $report['items'] ?? [];
    $unpaidGuests = $report['unpaid_guests'] ?? [];
    $shifts = $report['shifts'] ?? [];
    $cancelledItems = $report['cancelled_items'] ?? [];
    $reportNo = 'PERIOD-' . strtoupper(substr(($report['period_label'] ?? 'R'), 0, 3)) . '-' . date('Ymd') . '-' . strtoupper(substr(md5(($report['range_label'] ?? '') . ($report['generated_by'] ?? '')), 0, 6));
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
    <span class="main">{{ $report['title'] ?? 'PERIOD COUNTER SALES REPORT' }}</span>
    <span class="stamp">OFFICIAL</span>
</div>

<table class="cols">
    <tr>
        <td>
            <div class="box">
                <h3>Report Information</h3>
                <div class="row"><span class="label">Report Type:</span> <span class="value">{{ $report['period_label'] ?? 'Period' }} Combined Report</span></div>
                <div class="row"><span class="label">Period Start:</span> <span class="value">{{ $report['start_date'] ?? '—' }}</span></div>
                <div class="row"><span class="label">Period End:</span> <span class="value">{{ $report['end_date'] ?? '—' }}</span></div>
                <div class="row"><span class="label">Scope:</span> <span class="value">{{ !empty($report['is_manager']) ? 'All counters' : ($report['staff_name'] ?? 'Counter') }}</span></div>
                <div class="row"><span class="label">Generated By:</span> <span class="value">{{ $report['generated_by'] ?? '—' }}</span></div>
                <div class="row"><span class="label">Generated:</span> <span class="value">{{ now()->format('d M Y H:i') }}</span></div>
            </div>
        </td>
        <td>
            <div class="box">
                <h3>Sales Statistics</h3>
                <div class="row"><span class="label">Shifts:</span> <span class="value">{{ number_format($report['shift_count'] ?? 0) }}</span></div>
                <div class="row"><span class="label">Paid Orders:</span> <span class="value" style="color:#28a745;">{{ number_format($report['order_count'] ?? 0) }}</span></div>
                <div class="row"><span class="label">Guests:</span> <span class="value">{{ number_format($report['guest_count'] ?? 0) }}</span></div>
                <div class="row"><span class="label">Total Sales:</span> <span class="value" style="color:#940000;">{{ number_format($report['total_sales'] ?? 0) }} TZS</span></div>
                <div class="row"><span class="label">Unpaid:</span> <span class="value" style="color:#dc3545;">{{ number_format($report['unpaid_guest_count'] ?? 0) }} guests ({{ number_format($report['unpaid_total'] ?? 0) }} TZS)</span></div>
                <div class="row"><span class="label">Prior Debt Collected:</span> <span class="value" style="color:#856404;">{{ number_format($report['debt_collections_count'] ?? 0) }} ({{ number_format($report['debt_collections_total'] ?? 0) }} TZS)</span></div>
                <div class="row"><span class="label">Cancelled:</span> <span class="value">{{ number_format($report['cancelled_count'] ?? 0) }} ({{ number_format($report['cancelled_value'] ?? 0) }} TZS)</span></div>
            </div>
        </td>
    </tr>
</table>

<div class="section">
    <h3>1. Shift Times</h3>
    @if(count($shifts))
        <table class="data">
            <thead>
                <tr>
                    <th style="width:5%;">#</th>
                    <th>Counter</th>
                    <th>Opening Time</th>
                    <th>Closing Time</th>
                    <th class="center">Duration</th>
                    <th class="center">Status</th>
                    <th class="right">Sales</th>
                </tr>
            </thead>
            <tbody>
                @foreach($shifts as $i => $shift)
                    <tr>
                        <td class="center">{{ $i + 1 }}</td>
                        <td><strong>{{ $shift['staff_name'] ?? 'Counter' }}</strong></td>
                        <td>{{ $shift['opened_at'] ?? '—' }}</td>
                        <td>{{ $shift['closed_at'] ?? 'Still open' }}</td>
                        <td class="center">{{ $shift['duration'] ?? '—' }}</td>
                        <td class="center">{{ strtoupper($shift['status'] ?? '—') }}</td>
                        <td class="right">{{ number_format($shift['total_sales'] ?? 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="muted">No shifts found for this period.</p>
    @endif
</div>

<div class="section">
    <h3>2. Direct Sales (Paid Orders)</h3>
    @if(count($items))
        <table class="data">
            <thead>
                <tr>
                    <th style="width:5%;">#</th>
                    <th style="width:9%;">Time</th>
                    <th>Item</th>
                    <th>Guest</th>
                    <th class="center" style="width:7%;">Qty</th>
                    <th style="width:12%;">Payment</th>
                    <th class="right" style="width:14%;">Amount</th>
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
                                <div style="font-size:8px;color:#856404;font-weight:bold;">PAID FROM PRIOR DEBT</div>
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
        <p class="muted">No paid sales for this period.</p>
    @endif
</div>

<div class="section">
    <h3>3. Unpaid Orders
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
                    <th class="center" style="width:9%;">Orders</th>
                    <th class="right" style="width:14%;">Amount Due</th>
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
        <p class="muted">No unpaid orders for this period.</p>
    @endif
</div>

@if(count($cancelledItems))
<div class="section">
    <h3>4. Cancelled Orders</h3>
    <table class="data">
        <thead>
            <tr>
                <th style="width:5%;">#</th>
                <th style="width:12%;">Time</th>
                <th>Item</th>
                <th>Guest</th>
                <th class="center" style="width:7%;">Qty</th>
                <th class="right" style="width:12%;">Value</th>
                <th>Reason</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cancelledItems as $i => $item)
                <tr>
                    <td class="center">{{ $i + 1 }}</td>
                    <td>{{ $item['time'] ?? '—' }}</td>
                    <td>{{ $item['item'] ?? 'Item' }}</td>
                    <td>{{ $item['guest'] ?? '—' }}</td>
                    <td class="center">{{ rtrim(rtrim(number_format($item['qty'] ?? 0, 2, '.', ''), '0'), '.') }}</td>
                    <td class="right">{{ number_format($item['amount'] ?? 0) }}</td>
                    <td>{{ $item['reason'] ?? '—' }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="5" class="right">TOTAL CANCELLED VALUE:</td>
                <td class="right" colspan="2">{{ number_format($report['cancelled_value'] ?? 0) }} TZS</td>
            </tr>
        </tbody>
    </table>
</div>
@endif

<div class="summary-wrap">
    <h3>{{ strtoupper($report['period_label'] ?? 'PERIOD') }} FINANCIAL SUMMARY</h3>
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
            <td style="padding-top:8px;" colspan="2">
                <div class="card warn">
                    <span class="k">Unpaid Due</span>
                    <span class="v warn">{{ number_format($report['unpaid_total'] ?? 0) }} TZS</span>
                </div>
            </td>
            <td style="padding-top:8px;" colspan="2">
                <div class="card">
                    <span class="k">Cancelled Value</span>
                    <span class="v">{{ number_format($report['cancelled_value'] ?? 0) }} TZS</span>
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
            <span style="font-size:9.5px;">(Signature &amp; Date)</span>
        </td>
        <td>
            <div class="sig-line"></div>
            <strong>HOTEL MANAGER</strong><br>
            <span style="font-size:9.5px;">(Signature &amp; Date)</span>
        </td>
    </tr>
</table>

<div class="footer">
    <div><strong>Umoja Lutheran Hostel Management System</strong></div>
    <div>This is an official combined period report for auditing purposes.</div>
    <div class="powered">Powered By EmCa Technologies</div>
</div>
</body>
</html>
