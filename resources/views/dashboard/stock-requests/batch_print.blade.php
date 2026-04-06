<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Batch Requisition/Issue Note — {{ $stockRequests->first()->batch_reference ?? 'N/A' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            color: #000;
            background: #fff;
            padding: 20px;
        }

        .page {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #000;
            padding: 20px 25px;
        }

        .hostel-header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }

        .hostel-header .logo-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .hostel-header .logo-circle {
            width: 55px;
            height: 55px;
            border: 2px solid #000;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8pt;
            font-weight: bold;
            text-align: center;
            padding: 4px;
        }

        .hostel-header .org-name {
            font-size: 9pt;
            margin-bottom: 3px;
        }

        .hostel-header h2 {
            font-size: 16pt;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .hostel-header .address {
            font-size: 9pt;
            margin-top: 2px;
        }

        .hostel-header .tax-row {
            display: flex;
            justify-content: space-between;
            font-size: 9pt;
            margin-top: 4px;
        }

        .doc-title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            border: 2px solid #000;
            padding: 5px 0;
            margin: 10px 0;
            letter-spacing: 1px;
        }

        .ref-number {
            text-align: right;
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            gap: 6px;
            margin-bottom: 10px;
        }

        .meta-line {
            display: flex;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
        }

        .meta-line .label {
            font-weight: bold;
            white-space: nowrap;
            margin-right: 5px;
        }

        .meta-line .value {
            flex: 1;
        }

        .meta-full {
            grid-column: 1 / -1;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: center;
            font-size: 10pt;
        }

        th {
            font-weight: bold;
            background: #f0f0f0;
        }

        td.item-name {
            text-align: left;
        }

        .price-subheader th {
            font-size: 8pt;
        }

        tfoot td {
            font-weight: bold;
        }

        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px;
            margin-top: 30px;
        }

        .sig-box {
            text-align: center;
        }

        .sig-box .sig-line {
            border-bottom: 1px solid #000;
            margin-bottom: 4px;
            height: 35px;
        }

        .sig-box .sig-label {
            font-weight: bold;
            font-size: 9pt;
        }

        .sig-box .sig-name {
            font-size: 8pt;
            color: #333;
            margin-top: 2px;
        }

        .print-actions {
            margin: 20px 0;
            text-align: right;
        }

        .print-actions a,
        .print-actions button {
            display: inline-block;
            padding: 8px 18px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 11pt;
            cursor: pointer;
            border: none;
            margin-left: 8px;
        }

        .btn-print {
            background: #007bff;
            color: #fff;
        }

        .btn-back {
            background: #6c757d;
            color: #fff;
        }

        @media print {
            .print-actions {
                display: none;
            }

            body {
                padding: 0;
            }

            .page {
                border: none;
                max-width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="print-actions">
        <a href="{{ route('stock-requests.index') }}" class="btn-back">← Back to List</a>
        <button onclick="window.print()" class="btn-print">🖨 Print Consolidated Note</button>
    </div>

    <div class="page">
        <div class="hostel-header">
            <div class="org-name">ELCT NORTHERN DIOCESE</div>
            <div class="logo-row">
                <div class="logo-circle">ELCT<br>ND</div>
                <div>
                    <h2>UMOJA LUTHERAN HOSTEL</h2>
                    <div class="address">P.O BOX 196 Tel: (027) 2750902 MOSHI.</div>
                </div>
            </div>
            <div class="tax-row">
                <span>TIN No. 103-440-106</span>
                <span>VAT 16-012573-L</span>
            </div>
        </div>

        <div class="ref-number">
            Batch Ref: {{ $stockRequests->first()->batch_reference ?? 'N/A' }}
        </div>

        <div class="doc-title">Store Requisition / Issue Note (Batch)</div>

        <div class="meta-grid">
            <div class="meta-line">
                <span class="label">From:</span>
                <span class="value">{{ $stockRequests->first()->requester->name ?? 'N/A' }}
                    ({{ $stockRequests->first()->requester->role ?? 'N/A' }})</span>
            </div>
            <div class="meta-line">
                <span class="label">Date:</span>
                <span class="value">{{ $stockRequests->first()->created_at->format('d / m / Y') }}</span>
            </div>
            <div class="meta-line meta-full">
                <span class="label">To:</span>
                <span class="value">Store Department</span>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th rowspan="2" style="width:30px">No</th>
                    <th rowspan="2" style="text-align:left; width:220px">Item</th>
                    <th rowspan="2">Qty Requested</th>
                    <th rowspan="2">UoM</th>
                    <th rowspan="2">Qty Issued</th>
                    <th colspan="2">Unit Price</th>
                    <th colspan="2">Amount</th>
                </tr>
                <tr class="price-subheader">
                    <th>Shs</th>
                    <th>Cts</th>
                    <th>Shs</th>
                    <th>Cts</th>
                </tr>
            </thead>
            <tbody>
                @php $grandTotalCost = 0; @endphp
                @foreach($stockRequests as $index => $request)
                    @php
                        $itemName = $request->productVariant->product->name ?? 'N/A';
                        if ($request->productVariant->variant_name && strtolower($request->productVariant->variant_name) !== 'standard') {
                            $itemName .= ' (' . $request->productVariant->variant_name . ')';
                        }
                        $isCompleted = $request->status === 'completed';
                        $qtyToUse = $isCompleted ? ($request->quantity_issued ?? 0) : $request->quantity;
                        $unitCost = $request->unit_cost ?? 0;
                        $totalCost = $isCompleted ? ($request->total_cost ?? ($qtyToUse * $unitCost)) : ($qtyToUse * $unitCost);
                        $grandTotalCost += $totalCost;

                        $unitShs = floor($unitCost);
                        $unitCts = round(($unitCost - $unitShs) * 100);
                        $totalShs = floor($totalCost);
                        $totalCts = round(($totalCost - $totalShs) * 100);
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="item-name">{{ $itemName }}</td>
                        <td>{{ number_format($request->quantity, 1) }}</td>
                        <td>{{ $request->unit }}</td>
                        <td>
                            @if($isCompleted)
                                <strong>{{ number_format($request->quantity_issued, 1) }}</strong>
                            @else
                                <span style="color: #666;">—</span>
                            @endif
                        </td>
                        <td>{{ number_format($unitShs) }}</td>
                        <td>{{ str_pad($unitCts, 2, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $isCompleted ? number_format($totalShs) : '—' }}</td>
                        <td>{{ $isCompleted ? str_pad($totalCts, 2, '0', STR_PAD_LEFT) : '—' }}</td>
                    </tr>
                @endforeach

                @for($i = count($stockRequests); $i < 12; $i++)
                    <tr style="height: 24px;">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endfor
            </tbody>
            <tfoot>
                @php
                    $allCompleted = $stockRequests->every(fn($r) => $r->status === 'completed');
                    $grandShs = floor($grandTotalCost);
                    $grandCts = round(($grandTotalCost - $grandShs) * 100);
                @endphp
                <tr>
                    <td colspan="7" style="text-align:right; padding-right: 10px;">TOTAL SHS</td>
                    <td>{{ $allCompleted ? number_format($grandShs) : '—' }}</td>
                    <td>{{ $allCompleted ? str_pad($grandCts, 2, '0', STR_PAD_LEFT) : '—' }}</td>
                </tr>
            </tfoot>
        </table>

        @php $first = $stockRequests->first(); @endphp
        <div class="signatures">
            <div class="sig-box">
                <div class="sig-line"></div>
                <div class="sig-label">Authorised by</div>
                <div class="sig-name">{{ $first->manager->name ?? '................................' }}</div>
                @if($first->manager_approved_at)
                    <div class="sig-name" style="font-size:7pt">
                        {{ \Carbon\Carbon::parse($first->manager_approved_at)->format('d/m/Y H:i') }}</div>
                @endif
            </div>
            <div class="sig-box">
                <div class="sig-line"></div>
                <div class="sig-label">Issued by</div>
                <div class="sig-name">{{ $first->storekeeper->name ?? '................................' }}</div>
                @if($first->distributed_at)
                    <div class="sig-name" style="font-size:7pt">
                        {{ \Carbon\Carbon::parse($first->distributed_at)->format('d/m/Y H:i') }}</div>
                @endif
            </div>
            <div class="sig-box">
                <div class="sig-line"></div>
                <div class="sig-label">Received by</div>
                <div class="sig-name">{{ $first->requester->name ?? '................................' }}</div>
            </div>
        </div>
    </div>
</body>

</html>