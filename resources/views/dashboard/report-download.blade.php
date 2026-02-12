<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operational Report - {{ $dateRange['label'] }} - Umoj Lutheran Hostel</title>
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
            .info-section {
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
            <p style="margin-top: 15px; font-size: 16px; font-weight: bold; color: #e07632;">Operational Report</p>
        </div>
        
        <!-- Report Number -->
        <div class="receipt-number">
            <strong>Report #:</strong> OPR-RPT-{{ date('Ymd') }}-{{ strtoupper(substr(md5($dateRange['label'] . now()), 0, 6)) }}
        </div>
        
        <!-- Receipt Title -->
        <div class="receipt-title">
            OPERATIONAL REPORT
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
                        <span class="info-label">Checked In:</span>
                        <span class="info-value" style="color: #007bff;"><strong>{{ $bookings['checked_in'] ?? 0 }}</strong></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Checked Out:</span>
                        <span class="info-value" style="color: #dc3545;"><strong>{{ $bookings['checked_out'] ?? 0 }}</strong></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">New Bookings:</span>
                        <span class="info-value" style="color: #28a745;"><strong>{{ $bookings['new_bookings'] ?? 0 }}</strong></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Total Revenue:</span>
                        <span class="info-value"><span class="amount-highlight">{{ number_format(($payments['total_revenue'] ?? 0) * $exchangeRate, 2) }} TZS</span></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"></span>
                        <span class="info-value" style="color: #666; font-size: 12px;">≈ ${{ number_format($payments['total_revenue'] ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Service Requests -->
        <div class="info-section">
            <h3>Service Requests</h3>
            <table>
                <thead>
                    <tr>
                        <th>Status</th>
                        <th style="text-align: right;">Count</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Total Requests</td>
                        <td style="text-align: right;"><strong>{{ $serviceRequests['total'] ?? 0 }}</strong></td>
                    </tr>
                    <tr>
                        <td>Pending</td>
                        <td style="text-align: right;"><strong style="color: #ffc107;">{{ $serviceRequests['pending'] ?? 0 }}</strong></td>
                    </tr>
                    <tr>
                        <td>Approved</td>
                        <td style="text-align: right;"><strong style="color: #17a2b8;">{{ $serviceRequests['approved'] ?? 0 }}</strong></td>
                    </tr>
                    <tr>
                        <td>Completed</td>
                        <td style="text-align: right;"><strong style="color: #28a745;">{{ $serviceRequests['completed'] ?? 0 }}</strong></td>
                    </tr>
                    <tr class="total-row">
                        <td><strong>Service Revenue</strong></td>
                        <td style="text-align: right;"><strong style="color: #e07632; font-size: 14px;">{{ number_format($serviceRequests['revenue'] ?? 0, 2) }} TZS</strong><br><small style="color: #666;">≈ ${{ number_format(($serviceRequests['revenue'] ?? 0) / $exchangeRate, 2) }}</small></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Recent Bookings -->
        @if($recentBookings->count() > 0)
        <div class="info-section">
            <h3>Recent Bookings</h3>
            <table>
                <thead>
                    <tr>
                        <th>Booking Reference</th>
                        <th>Guest</th>
                        <th>Room</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th style="text-align: right;">Total Price</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentBookings as $booking)
                    <tr>
                        <td><strong>{{ $booking->booking_reference }}</strong></td>
                        <td>{{ $booking->guest_name }}</td>
                        <td>
                            {{ $booking->room->room_type ?? 'N/A' }}<br>
                            <small style="color: #999;">{{ $booking->room->room_number ?? 'N/A' }}</small>
                        </td>
                        <td>{{ $booking->check_in->format('M d, Y') }}</td>
                        <td>{{ $booking->check_out->format('M d, Y') }}</td>
                        <td style="text-align: right;">
                            <strong>{{ number_format($booking->total_price * $exchangeRate, 2) }} TZS</strong><br>
                            <small style="color: #999;">≈ ${{ number_format($booking->total_price, 2) }}</small>
                        </td>
                        <td>{{ ucfirst($booking->status) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        
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
</body>
</html>
