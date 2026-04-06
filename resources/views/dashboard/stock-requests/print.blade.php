<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store Requisition/Issue Note #{{ $stockRequest->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            color: #000;
            background: #fff;
            padding: 20px;
        }

        .page {
            max-width: 700px;
            margin: 0 auto;
            border: 1px solid #000;
            padding: 20px 25px;
        }

        /* ===== HEADER ===== */
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

        /* ===== DOC TITLE ===== */
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
            font-size: 13pt;
            font-weight: bold;
            margin-bottom: 8px;
        }

        /* ===== FROM / TO / DATE ===== */
        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
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

        /* ===== TABLE ===== */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px 6px;
            text-align: center;
            font-size: 10.5pt;
        }

        th {
            font-weight: bold;
            background: #f0f0f0;
        }

        td.item-name {
            text-align: left;
        }

        .price-subheader th {
            font-size: 9pt;
        }

        tfoot td {
            font-weight: bold;
        }

        /* ===== SIGNATURE SECTION ===== */
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
            font-size: 10pt;
        }

        .sig-box .sig-name {
            font-size: 9pt;
            color: #333;
            margin-top: 2px;
        }

        /* ===== STATUS BADGES ===== */
        .status-issued {
            color: #155724;
            font-weight: bold;
        }

        .status-pending {
            color: #856404;
            font-weight: bold;
        }

        /* ===== PRINT BUTTON ===== */
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

    <!-- Print / Back Buttons (hidden on print) -->
    <div class="print-actions">
        <a href="{{ url()->previous() }}" class="btn-back">← Back</a>
        <button onclick="window.print()" class="btn-print">🖨 Print</button>
    </div>

    <div class="page">

        <!-- ===== HEADER ===== -->
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

        <!-- Reference Number -->
        <div class="ref-number">
            Req. No. {{ str_pad($stockRequest->id, 5, '0', STR_PAD_LEFT) }}
        </div>

        <!-- ===== DOCUMENT TITLE ===== -->
        <div class="doc-title">Store Requisition / Issue Note</div>

        <!-- ===== META INFO ===== -->
        <div class="meta-grid">
            <div class="meta-line">
                <span class="label">From:</span>
                <span class="value">{{ $stockRequest->requester->name ?? 'N/A' }}
                    ({{ ucwords(str_replace('_', ' ', $stockRequest->requester->role ?? '')) }})
                </span>
            </div>
            <div class="meta-line">
                <span class="label">Date:</span>
                <span
                    class="value">{{ $stockRequest->created_at ? $stockRequest->created_at->format('d / m / Y') : now()->format('d / m / Y') }}</span>
            </div>
            <div class="meta-line meta-full">
                <span class="label">To:</span>
                <span class="value">Store Department</span>
            </div>
        </div>

        <!-- ===== ITEMS TABLE ===== -->
        <table>
            <thead>
                <tr>
                    <th rowspan="2" style="width:30px">No</th>
                    <th rowspan="2" style="text-align:left; width:200px">Item</th>
                    <th rowspan="2">Quantity<br>Requested</th>
                    <th rowspan="2">Unit of<br>Measure</th>
                    <th rowspan="2">Quantity<br>Issued</th>
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
                @php
                    $itemName = $stockRequest->productVariant->product->name ?? 'N/A';
                    if ($stockRequest->productVariant->variant_name && strtolower($stockRequest->productVariant->variant_name) !== 'standard') {
                        $itemName .= ' (' . $stockRequest->productVariant->variant_name . ')';
                    }
                    $qtyIssued = $stockRequest->quantity_issued ?? $stockRequest->quantity;
                    $unitCost = $stockRequest->unit_cost ?? 0;
                    $totalCost = $stockRequest->total_cost ?? ($qtyIssued * $unitCost);
                    $unitShs = floor($unitCost);
                    $unitCts = round(($unitCost - $unitShs) * 100);
                    $totalShs = floor($totalCost);
                    $totalCts = round(($totalCost - $totalShs) * 100);
                @endphp
                <tr>
                    <td>1</td>
                    <td class="item-name">{{ $itemName }}</td>
                    <td>{{ number_format($stockRequest->quantity, 1) }}</td>
                    <td>{{ $stockRequest->unit }}</td>
                    <td>
                        @if($stockRequest->status === 'completed')
                            <span class="status-issued">{{ number_format($qtyIssued, 1) }}</span>
                        @else
                            <span class="status-pending">—</span>
                        @endif
                    </td>
                    <td>{{ $stockRequest->status === 'completed' ? number_format($unitShs) : '—' }}</td>
                    <td>{{ $stockRequest->status === 'completed' ? str_pad($unitCts, 2, '0', STR_PAD_LEFT) : '—' }}</td>
                    <td>{{ $stockRequest->status === 'completed' ? number_format($totalShs) : '—' }}</td>
                    <td>{{ $stockRequest->status === 'completed' ? str_pad($totalCts, 2, '0', STR_PAD_LEFT) : '—' }}
                    </td>
                </tr>

                <!-- Blank rows to pad the table like the physical form -->
                @for($i = 0; $i < 10; $i++)
                    <tr style="height: 22px;">
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
                <tr>
                    <td colspan="7" style="text-align:right; padding-right: 10px;">TOTAL SHS</td>
                    <td>{{ $stockRequest->status === 'completed' ? number_format($totalShs) : '' }}</td>
                    <td>{{ $stockRequest->status === 'completed' ? str_pad($totalCts, 2, '0', STR_PAD_LEFT) : '' }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- ===== STATUS BANNER (for approved-but-not-yet-issued) ===== -->
        @if($stockRequest->status === 'approved')
            <div
                style="border: 1px dashed orange; padding: 8px; text-align:center; color: #856404; font-weight:bold; margin-bottom: 10px;">
                ⚠ AUTHORISED — Awaiting Issue by Storekeeper
            </div>
        @endif

        <!-- ===== SIGNATURE SECTION ===== -->
        <div class="signatures">
            <div class="sig-box">
                <div class="sig-line"></div>
                <div class="sig-label">Authorised by</div>
                <div class="sig-name">{{ $stockRequest->manager->name ?? '................................' }}</div>
                @if($stockRequest->manager_approved_at)
                    <div class="sig-name" style="font-size:8pt">
                        {{ \Carbon\Carbon::parse($stockRequest->manager_approved_at)->format('d/m/Y') }}</div>
                @endif
            </div>
            <div class="sig-box">
                <div class="sig-line"></div>
                <div class="sig-label">Issued by</div>
                <div class="sig-name">{{ $stockRequest->storekeeper->name ?? '................................' }}</div>
                @if($stockRequest->distributed_at)
                    <div class="sig-name" style="font-size:8pt">
                        {{ \Carbon\Carbon::parse($stockRequest->distributed_at)->format('d/m/Y') }}</div>
                @endif
            </div>
            <div class="sig-box">
                <div class="sig-line"></div>
                <div class="sig-label">Received by</div>
                <div class="sig-name">{{ $stockRequest->requester->name ?? '................................' }}</div>
            </div>
        </div>

    </div><!-- .page -->

</body>

</html>