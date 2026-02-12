<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Day Services Report - {{ $dateRange['label'] }} - Umoj Lutheran Hostel</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Prevent browser from adding headers/footers when printing -->
    <style>
        @media print {
            @page {
                margin: 0 !important;
                size: A4;
            }
            
            /* Force remove all browser headers/footers */
            @page :first {
                margin-top: 0 !important;
            }
            
            @page :left {
                margin-left: 0 !important;
            }
            
            @page :right {
                margin-right: 0 !important;
            }
        }
    </style>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            background: #fff;
            padding: 20px;
        }
        
        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border: 1px solid #ddd;
            position: relative;
        }
        
        .receipt-container::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            width: 500px;
            height: 500px;
            background-image: url('{{ asset('royal-master/image/logo/Logo.png') }}');
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            opacity: 0.12;
            z-index: 0;
            pointer-events: none;
            filter: grayscale(100%) brightness(1.2);
        }
        
        .receipt-container > * {
            position: relative;
            z-index: 1;
        }
        
        /* Ensure watermark appears in print */
        @media print {
            .receipt-container::before {
                opacity: 0.1 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #e07632;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header .logo-container {
            margin-bottom: 15px;
        }
        
        .header .logo-container img {
            max-height: 80px;
            max-width: 300px;
            height: auto;
            width: auto;
        }
        
        .header h1 {
            color: #e07632;
            font-size: 28px;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .header p {
            color: #666;
            font-size: 14px;
        }
        
        .receipt-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 30px;
            color: #333;
        }
        
        .info-section {
            margin-bottom: 25px;
        }
        
        .info-section h3 {
            color: #e07632;
            font-size: 14px;
            border-bottom: 2px solid #e07632;
            padding-bottom: 8px;
            margin-bottom: 15px;
            font-weight: bold;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 5px 0;
        }
        
        .info-label {
            font-weight: bold;
            color: #555;
            width: 45%;
        }
        
        .info-value {
            color: #333;
            width: 55%;
            text-align: right;
        }
        
        .two-column {
            display: flex;
            gap: 30px;
            margin-bottom: 25px;
        }
        
        .column {
            flex: 1;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        table th {
            background-color: #f8f9fa;
            color: #333;
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
            font-weight: bold;
            font-size: 11px;
        }
        
        table td {
            padding: 12px;
            border: 1px solid #ddd;
            font-size: 11px;
        }
        
        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .total-row {
            background-color: #f8f9fa !important;
            font-weight: bold;
        }
        
        .total-row td {
            font-size: 13px;
            padding: 15px 12px;
        }
        
        .payment-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-left: 4px solid #e07632;
            margin-top: 20px;
        }
        
        .payment-info h4 {
            color: #e07632;
            margin-bottom: 15px;
            font-size: 14px;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
        
        .footer p {
            margin: 5px 0;
        }
        
        .receipt-number {
            text-align: right;
            margin-bottom: 20px;
            color: #666;
            font-size: 11px;
        }
        
        .amount-highlight {
            font-size: 20px;
            color: #e07632;
            font-weight: bold;
        }
        
        .print-button {
            text-align: center;
            margin: 30px 0;
        }
        
        .print-button button {
            background-color: #e07632;
            color: #fff;
            border: none;
            padding: 12px 30px;
            font-size: 14px;
            cursor: pointer;
            border-radius: 4px;
            font-weight: bold;
        }
        
        .print-button button:hover {
            background-color: #c86528;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .stat-box {
            background-color: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #e07632;
        }
        
        .stat-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .stat-value {
            font-size: 22px;
            font-weight: bold;
            color: #e07632;
        }
        
        .text-right {
            text-align: right;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .badge-paid {
            background-color: #28a745;
            color: white;
        }
        
        .badge-pending {
            background-color: #ffc107;
            color: #333;
        }
        
        @media print {
            /* Remove all margins and padding */
            @page {
                margin: 10mm !important;
                padding: 0 !important;
                size: A4;
            }
            
            /* Remove all page headers and footers */
            @page {
                @top-left { content: none !important; }
                @top-center { content: none !important; }
                @top-right { content: none !important; }
                @bottom-left { content: none !important; }
                @bottom-center { content: none !important; }
                @bottom-right { content: none !important; }
            }
            
            body {
                padding: 0 !important;
                margin: 0 !important;
                font-size: 11px !important;
                line-height: 1.4 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            html {
                margin: 0 !important;
                padding: 0 !important;
            }
            
            .receipt-container {
                border: none !important;
                padding: 10px 15px !important;
                margin: 0 !important;
                box-shadow: none !important;
                max-width: 100% !important;
                page-break-inside: avoid !important;
            }
            
            .print-button {
                display: none !important;
            }
            
            .header {
                padding-bottom: 10px !important;
                margin-bottom: 15px !important;
            }
            
            .header h1 {
                font-size: 22px !important;
                margin-bottom: 3px !important;
            }
            
            .header p {
                font-size: 11px !important;
                margin: 2px 0 !important;
            }
            
            .receipt-title {
                font-size: 16px !important;
                margin-bottom: 15px !important;
            }
            
            .info-section {
                margin-bottom: 15px !important;
                page-break-inside: avoid !important;
            }
            
            .info-section h3 {
                font-size: 12px !important;
                padding-bottom: 5px !important;
                margin-bottom: 8px !important;
            }
            
            .info-row {
                margin-bottom: 5px !important;
                padding: 2px 0 !important;
                font-size: 10px !important;
            }
            
            .two-column {
                gap: 15px !important;
                margin-bottom: 15px !important;
            }
            
            table {
                margin-bottom: 12px !important;
                font-size: 10px !important;
            }
            
            table th, table td {
                padding: 8px !important;
                font-size: 10px !important;
            }
            
            .total-row td {
                padding: 10px 8px !important;
                font-size: 11px !important;
            }
            
            .payment-info {
                padding: 12px !important;
                margin-top: 12px !important;
            }
            
            .payment-info h4 {
                font-size: 12px !important;
                margin-bottom: 10px !important;
            }
            
            .footer {
                margin-top: 20px !important;
                padding-top: 10px !important;
                font-size: 9px !important;
            }
            
            .footer p {
                margin: 3px 0 !important;
            }
            
            .receipt-number {
                margin-bottom: 10px !important;
                font-size: 10px !important;
            }
            
            .amount-highlight {
                font-size: 16px !important;
            }
            
            .stat-value {
                font-size: 18px !important;
            }
            
            /* Prevent page breaks inside sections */
            .two-column,
            .info-section,
            .payment-info {
                page-break-inside: avoid !important;
            }
            
            /* Hide any browser-added headers/footers */
            body::before,
            body::after,
            html::before,
            html::after {
                display: none !important;
                content: none !important;
            }
            
            /* Prevent URL from being printed */
            a[href]:after,
            a[href]:before {
                content: none !important;
            }
            
            /* Ensure clean print output */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            /* Remove any background elements */
            body > * {
                background: white !important;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- Header -->
        <div class="header">
            <div class="logo-container">
                <img src="{{ asset('royal-master/image/logo/Logo.png') }}" alt="Umoj Lutheran Hostel Logo" onerror="this.style.display='none';">
            </div>
            <h1>Umoj Lutheran Hostel</h1>
            <div style="margin-top: 10px; margin-bottom: 10px; line-height: 1.8;">
                <p style="margin: 5px 0; font-size: 13px; color: #555;">
                    <strong>Location:</strong> Sokoine Road-Moshi Kilimanjaro-Tanzania
                </p>
                <p style="margin: 5px 0; font-size: 13px; color: #555;">
                    <strong>Mobile/WhatsApp:</strong> 0677-155-156 / +255 677-155-157
                </p>
                <p style="margin: 5px 0; font-size: 13px; color: #555;">
                    <strong>Email:</strong> info@Umoj Lutheran Hostelhotel.co.tz / infoUmoj Lutheran Hostelhotel@gmail.com
                </p>
            </div>
            <p style="margin-top: 15px; font-size: 16px; font-weight: bold; color: #e07632;">Day Services Report</p>
        </div>
        
        <!-- Report Number -->
        <div class="receipt-number">
            <strong>Report #:</strong> DS-RPT-{{ date('Ymd') }}-{{ strtoupper(substr(md5($dateRange['label'] . now()), 0, 6)) }}
        </div>
        
        <!-- Receipt Title -->
        <div class="receipt-title">
            DAY SERVICES REPORT
        </div>
        
        <!-- Print Button -->
        <div class="print-button">
            <button onclick="printReceipt()">
                <i class="fa fa-print"></i> Print / Save as PDF
            </button>
            <div style="font-size: 11px; color: #333; margin-top: 10px; background: #fff3cd; padding: 10px; border-radius: 4px; border-left: 3px solid #ffc107;">
                <strong><i class="fa fa-info-circle"></i> Important:</strong> To remove URLs and headers from the printed report:<br>
                <strong>Chrome/Edge:</strong> In print dialog → More settings → Uncheck "Headers and footers"<br>
                <strong>Firefox:</strong> In print dialog → More Settings → Headers & Footers → Select "--blank--"<br>
                <strong>Safari:</strong> In print options → Uncheck "Print headers and footers"
            </div>
        </div>
        
        <!-- Report Information -->
        <div class="two-column">
            <div class="column">
                <div class="info-section">
                    <h3>Report Information</h3>
                    <div class="info-row">
                        <span class="info-label">Report Period:</span>
                        <span class="info-value"><strong>{{ $dateRange['label'] }}</strong></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">From Date:</span>
                        <span class="info-value">{{ $dateRange['start']->format('F d, Y') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">To Date:</span>
                        <span class="info-value">{{ $dateRange['end']->format('F d, Y') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Generated on:</span>
                        <span class="info-value">{{ \Carbon\Carbon::now()->format('F d, Y \a\t g:i A') }}</span>
                    </div>
                </div>
            </div>
            
            <div class="column">
                <div class="info-section">
                    <h3>Statistics Summary</h3>
                    <div class="info-row">
                        <span class="info-label">Total Services:</span>
                        <span class="info-value"><strong>{{ number_format($statistics['total_services'] ?? 0) }}</strong></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Paid Services:</span>
                        <span class="info-value" style="color: #28a745;"><strong>{{ number_format($statistics['paid_services'] ?? 0) }}</strong></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Pending Services:</span>
                        <span class="info-value" style="color: #ffc107;"><strong>{{ number_format($statistics['pending_services'] ?? 0) }}</strong></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Total Revenue:</span>
                        <span class="info-value"><span class="amount-highlight">{{ number_format($statistics['total_revenue'] ?? 0, 2) }} TZS</span></span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Service Details Table -->
        <div class="info-section">
            <h3>Service Details</h3>
            @if($dayServices->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Service Type</th>
                        <th>Guest Name</th>
                        <th>Date & Time</th>
                        <th style="text-align: right;">Amount</th>
                        <th>Payment Status</th>
                        <th>Payment Method</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dayServices as $service)
                    <tr>
                        <td><strong>{{ $service->service_reference }}</strong></td>
                        <td>{{ $service->service_type_name }}</td>
                        <td>{{ $service->guest_name }}</td>
                        <td>
                            {{ $service->service_date->format('M d, Y') }}<br>
                            <small style="color: #999;">{{ $service->service_time }}</small>
                        </td>
                        <td class="text-right">
                            @if($service->guest_type === 'tanzanian')
                                {{ number_format($service->amount, 2) }} TZS
                            @else
                                ${{ number_format($service->amount, 2) }}
                                @if($service->exchange_rate)
                                    <br><small style="color: #999;">≈ {{ number_format($service->amount * $service->exchange_rate, 2) }} TZS</small>
                                @endif
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-{{ $service->payment_status === 'paid' ? 'paid' : 'pending' }}">
                                {{ ucfirst($service->payment_status) }}
                            </span>
                        </td>
                        <td>{{ $service->payment_method_name ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="4"><strong>Total Revenue</strong></td>
                        <td class="text-right"><span class="amount-highlight">{{ number_format($statistics['total_revenue'] ?? 0, 2) }} TZS</span></td>
                        <td colspan="2"></td>
                    </tr>
                </tbody>
            </table>
            @else
            <p style="text-align: center; padding: 20px; color: #666;">No services found for the selected period.</p>
            @endif
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p><strong>Umoj Lutheran Hostel</strong></p>
            <p>This report has been generated automatically by the Umoj Lutheran Hostel Management System.</p>
            <p style="margin-top: 15px;">This is an official report. Please keep this for your records.</p>
            <p style="margin-top: 10px; font-size: 9px;">Generated on: {{ now()->format('F d, Y \a\t g:i A') }}</p>
            <p style="margin-top: 15px; font-size: 9px; color: #940000;">
                Powered By <strong style="color: #940000;">EmCa Techonologies</strong>
            </p>
        </div>
    </div>
    
    <script>
        function printReceipt() {
            // Hide the print button before printing
            var printButton = document.querySelector('.print-button');
            if (printButton) {
                printButton.style.display = 'none';
            }
            
            // Trigger print dialog
            window.print();
            
            // Show the print button again after print dialog closes
            setTimeout(function() {
                if (printButton) {
                    printButton.style.display = 'block';
                }
            }, 250);
        }
        
        // Prevent URLs from being printed by removing them from links
        window.onbeforeprint = function() {
            // Remove href attributes temporarily to prevent URL printing
            var links = document.querySelectorAll('a');
            links.forEach(function(link) {
                if (link.hasAttribute('data-href-stored')) {
                    link.href = link.getAttribute('data-href-stored');
                    link.removeAttribute('data-href-stored');
                }
            });
        };
        
        window.onafterprint = function() {
            // Restore href attributes if needed
            var links = document.querySelectorAll('a');
            links.forEach(function(link) {
                if (link.href && !link.hasAttribute('data-href-stored')) {
                    link.setAttribute('data-href-stored', link.href);
                    link.removeAttribute('href');
                }
            });
        };
    </script>
</body>
</html>
