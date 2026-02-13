<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Receipt - {{ $booking->booking_reference }} - Umoj Lutheran Hostel</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #e07632;
            --primary-dark: #c86528;
            --secondary: #17a2b8;
            --success: #28a745;
            --danger: #dc3545;
            --text-main: #333;
            --text-light: #666;
            --border: #e2e8f0;
            --bg-light: #f8fafc;
        }

        @media print {
            @page { 
                margin: 0.5cm; 
                size: A4; 
            }
            
            body { 
                margin: 0; 
                padding: 0;
                background: white !important;
            }
            
            /* Hide print buttons */
            .print-button, .info-alert, .no-print, .no-print-bar { 
                display: none !important; 
            }
            
            /* Preserve all colors and backgrounds */
            * { 
                -webkit-print-color-adjust: exact !important; 
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            
            /* Receipt container */
            .receipt-container { 
                box-shadow: none !important; 
                width: 100% !important; 
                margin: 0 !important; 
                padding: 40px !important;
                border: 1px solid var(--border) !important;
                page-break-inside: avoid;
            }
            
            /* Preserve top gradient bar */
            .receipt-container::before {
                content: '' !important;
                background: linear-gradient(90deg, var(--primary), #f6ad55) !important;
            }
            
            /* Preserve watermark */
            .watermark {
                position: absolute !important;
                top: 50% !important;
                left: 50% !important;
                transform: translate(-50%, -50%) rotate(-30deg) !important;
                opacity: 1 !important;
                color: rgba(0,0,0,0.02) !important;
                pointer-events: none !important;
            }
            
            /* Preserve hotel stamp */
            .hotel-stamp {
                position: absolute !important;
                bottom: 50px !important;
                right: 60px !important;
                width: 150px !important;
                height: 150px !important;
                opacity: 0.15 !important;
                display: block !important;
                pointer-events: none !important;
                z-index: 0 !important;
            }
            
            /* Preserve status stamp */
            .status-stamp {
                position: absolute !important;
                bottom: 220px !important;
                right: 80px !important;
                opacity: 0.12 !important;
                border: 4px solid var(--success) !important;
                color: var(--success) !important;
                transform: rotate(-15deg) !important;
                pointer-events: none !important;
            }
            
            /* Preserve policy cards backgrounds */
            .policy-column {
                background: var(--bg-light) !important;
                border: 1px solid var(--border) !important;
                page-break-inside: avoid;
            }
            
            /* Preserve info cards */
            .info-card {
                background: #fff !important;
                border: 1px solid var(--border) !important;
                page-break-inside: avoid;
            }
            
            /* Preserve summary box */
            .summary-box {
                background: var(--bg-light) !important;
                page-break-inside: avoid;
            }
            
            .summary-wrapper {
                z-index: 2 !important;
            }
            
            /* Preserve table styling */
            .items-table th {
                background: #1e293b !important;
                color: #fff !important;
            }
            
            .items-table tr:hover {
                background-color: transparent !important;
            }
            
            /* Preserve all text colors */
            .card-title {
                color: var(--primary) !important;
            }
            
            h1, h2, h4 {
                color: inherit !important;
            }
            
            /* Ensure signature line prints */
            .sig-line {
                border-top: 2px solid #333 !important;
            }
            
            /* Prevent page breaks in critical sections */
            .header, .grid-info, .items-table, .summary-wrapper, .footer-grid {
                page-break-inside: avoid;
            }
            
            /* Ensure policies section stays together */
            .policies-section {
                page-break-inside: avoid;
            }
            
            /* PDF-specific optimizations */
            img {
                image-rendering: -webkit-optimize-contrast !important;
                image-rendering: crisp-edges !important;
            }
            
            /* Ensure all fonts render properly */
            body, * {
                font-family: 'Outfit', 'DejaVu Sans', Arial, sans-serif !important;
            }
            
            /* Better rendering for PDF */
            .receipt-container {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Outfit', 'DejaVu Sans', sans-serif; }
        
        body {
            background-color: var(--bg-light);
            color: var(--text-main);
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            padding: 40px 20px;
        }

        .receipt-container {
            max-width: 850px;
            margin: 0 auto;
            background: #fff;
            padding: 50px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            position: relative;
            overflow: hidden;
            border: 1px solid var(--border);
        }

        .receipt-container::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; height: 6px;
            background: linear-gradient(90deg, var(--primary), #f6ad55);
        }

        .watermark {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 10rem; font-weight: 900;
            color: rgba(0,0,0,0.02);
            pointer-events: none; z-index: 0;
            text-transform: uppercase;
            text-align: center;
            line-height: 1.2;
        }
        
        .watermark-subtitle {
            font-size: 3rem;
            font-weight: 400;
            display: block;
            margin-top: 10px;
            font-style: italic;
        }

        .header {
            display: flex; justify-content: space-between; align-items: flex-start;
            margin-bottom: 40px; position: relative; z-index: 1;
        }

        .hotel-info { max-width: 50%; }
        .logo-container img { max-height: 60px; margin-bottom: 15px; }
        .hotel-info h1 { font-size: 24px; font-weight: 700; color: var(--primary); letter-spacing: 1px; margin-bottom: 5px; }
        .hotel-info p { font-size: 13px; color: var(--text-light); margin-bottom: 3px; }

        .receipt-meta { text-align: right; }
        .receipt-meta h2 { font-size: 28px; font-weight: 700; color: #1e293b; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 15px; }

        .meta-pill {
            background: var(--bg-light); padding: 12px 20px; border-radius: 8px;
            border: 1px solid var(--border); display: inline-block; text-align: left;
        }

        .meta-item label { display: block; font-size: 10px; text-transform: uppercase; color: var(--text-light); font-weight: 700; margin-bottom: 2px; }
        .meta-item span { font-size: 14px; font-weight: 700; color: var(--text-main); }

        .grid-info {
            display: grid; grid-template-columns: 1fr 1fr; gap: 20px;
            margin-bottom: 35px; position: relative; z-index: 1;
        }

        .info-card {
            background: #fff; border: 1px solid var(--border);
            border-radius: 10px; padding: 18px;
        }

        .card-title {
            font-size: 11px; font-weight: 700; text-transform: uppercase;
            color: var(--primary); border-bottom: 1px solid var(--bg-light);
            padding-bottom: 10px; margin-bottom: 12px; display: flex; align-items: center;
        }
        .card-title i { margin-right: 8px; }

        .info-row { display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 13px; }
        .info-row b { color: var(--text-light); font-weight: 500; }
        .info-row span { font-weight: 600; color: var(--text-main); }

        .items-table {
            width: 100%; border-collapse: collapse; margin-bottom: 30px; position: relative; z-index: 1;
        }

        .items-table th {
            background: #1e293b; color: #fff; text-align: left;
            padding: 12px 15px; font-size: 11px; text-transform: uppercase; letter-spacing: 1px;
        }

        .items-table td { padding: 12px 15px; border-bottom: 1px solid var(--border); font-size: 13px; }
        .items-table tr:hover { background-color: #fbfcfd; }

        .summary-wrapper { display: flex; justify-content: flex-end; margin-bottom: 40px; position: relative; z-index: 2; }
        .summary-box { width: 320px; background: var(--bg-light); border-radius: 10px; padding: 18px; }
        .summary-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 13px; }
        .summary-row.total {
            border-top: 2px solid var(--border); margin-top: 10px; padding-top: 12px;
            color: var(--primary); font-weight: 700; font-size: 18px;
        }
        .summary-row.paid { color: var(--success); font-weight: 700; }

        .status-stamp {
            position: absolute; bottom: 220px; right: 80px;
            border: 4px solid var(--success); color: var(--success);
            padding: 10px 30px; font-size: 32px; font-weight: 900;
            text-transform: uppercase; transform: rotate(-15deg);
            opacity: 0.12; border-radius: 10px; pointer-events: none; z-index: 0;
        }
        
        .hotel-stamp {
            position: absolute;
            bottom: 50px;
            right: 60px;
            width: 150px;
            height: 150px;
            opacity: 0.15;
            pointer-events: none;
            z-index: 0;
        }

        .footer-grid {
            border-top: 1px solid var(--border); padding-top: 25px;
            position: relative; z-index: 1;
        }
        
        .policies-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .policy-column {
            background: var(--bg-light);
            padding: 15px;
            border-radius: 8px;
            border: 1px solid var(--border);
        }

        .terms h4 { font-size: 11px; font-weight: 700; margin-bottom: 8px; text-transform: uppercase; color: var(--primary); }
        .terms p { font-size: 10px; color: var(--text-light); margin-bottom: 0; line-height: 1.6; }

        .signature-area { 
            text-align: center;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px dashed var(--border);
        }
        .sig-line { 
            border-top: 2px solid #333; 
            margin: 0 auto;
            margin-top: 60px; 
            padding-top: 8px; 
            font-size: 11px; 
            font-weight: 600;
            max-width: 250px;
        }

        .no-print-bar { max-width: 850px; margin: 20px auto; display: flex; justify-content: center; gap: 15px; }
        .btn {
            border: none; padding: 12px 25px; border-radius: 8px; font-weight: 700; cursor: pointer;
            transition: all 0.2s; display: flex; align-items: center; gap: 8px;
        }
        .btn-primary { background: var(--primary); color: #fff; box-shadow: 0 4px 6px rgba(224, 118, 50, 0.2); }
        .btn-primary:hover { background: var(--primary-dark); transform: translateY(-1px); }
        .btn-success { background: #28a745; color: #fff; box-shadow: 0 4px 6px rgba(40, 167, 69, 0.2); }
        .btn-success:hover { background: #218838; transform: translateY(-1px); }
    </style>
    <script>
        function saveAsPDF() {
            // Show helpful message
            alert('In the print dialog that opens:\n\n1. Select "Save as PDF" or "Microsoft Print to PDF" as your printer\n2. Click "Print" or "Save"\n3. Choose where to save your PDF file');
            
            // Trigger print dialog which allows saving as PDF
            setTimeout(function() {
                window.print();
            }, 100);
        }
    </script>
</head>
<body>
    @php
        $isTanzanian = ($booking->guest_type ?? '') === 'tanzanian';
        $isCorporate = $booking->is_corporate_booking && $booking->company_id;
        $isStaffView = auth()->guard('staff')->check();
        
        // Decide what bookings to show
        $displayBookings = collect([$booking]);
        if ($isCorporate && isset($allCompanyBookings) && $isStaffView) {
            $displayBookings = $allCompanyBookings;
        }
        
        $groupRoomTotalBase = 0;
        foreach($displayBookings as $b) { $groupRoomTotalBase += $b->total_price; }

        $totalPaidBase = ($isCorporate && isset($totalCompanyPaid)) ? $totalCompanyPaid : ($booking->amount_paid ?? 0);
        
        // If it's a guest viewing self-paid services, we override totals
        if (isset($isGuestWithSelfPaidServices) && $isGuestWithSelfPaidServices) {
            $grandTotalBase = $guestServicePayments ?? 0;
            $grandPaidBase = $guestServicePayments ?? 0;
        } else {
            $grandTotalBase = $groupRoomTotalBase;
            $grandPaidBase = $totalPaidBase;
        }
        
        $balanceBase = max(0, $grandTotalBase - $grandPaidBase);
        $currentExchangeRate = $exchangeRate ?? 2500;
    @endphp

    <div class="no-print-bar no-print">
        <button class="btn btn-primary" onclick="window.print()">
            <i class="fa fa-print"></i> PRINT RECEIPT
        </button>
        <button class="btn btn-success" onclick="saveAsPDF()">
            <i class="fa fa-file-pdf-o"></i> SAVE AS PDF
        </button>
        <button class="btn" onclick="window.close()" style="background: #e2e8f0; color: #475569;">
            <i class="fa fa-times"></i> CLOSE
        </button>
    </div>

    <div class="receipt-container">
        <div class="watermark">
            Umoj Lutheran Hostel
            <span class="watermark-subtitle">Comfort in every Stay</span>
        </div>
        <img src="{{ asset('royal-master/image/Umoj Lutheran Hostel_stamp.png') }}" alt="Hotel Stamp" class="hotel-stamp">
        @if($balanceBase < 0.0001) <div class="status-stamp">PAID</div> @endif

        <div class="header">
            <div class="hotel-info">
                <div class="logo-container">
                    <img src="{{ asset('royal-master/image/logo/Logo.png') }}" alt="Logo">
                </div>
                <h1>Umoj Lutheran Hostel</h1>
                <p><i class="fa fa-map-marker"></i> Moshi-Kilimanjaro, Tanzania</p>
                <p><i class="fa fa-phone"></i> +255 677 155 156 / 157</p>
                <p><i class="fa fa-envelope"></i> info@Umoj Lutheran Hostelhotel.co.tz</p>
            </div>
            <div class="receipt-meta">
                <h2>RECEIPT</h2>
                <div class="meta-pill">
                    <div class="meta-item" style="margin-bottom: 15px;">
                        <label>Receipt No.</label>
                        <span>{{ $booking->booking_reference }}-{{ $booking->paid_at ? $booking->paid_at->format('Ymd') : date('Ymd') }}</span>
                    </div>
                    <div class="meta-item">
                        <label>Date Issued</label>
                        <span>{{ now()->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid-info">
            <div class="info-card">
                <div class="card-title"><i class="fa fa-building"></i> Billed To</div>
                @if($isCorporate && $booking->company)
                    <div class="info-row"><b>Company:</b> <span>{{ $booking->company->name }}</span></div>
                    <div class="info-row"><b>Contact:</b> <span>{{ $booking->company->contact_person ?? 'Account Dept.' }}</span></div>
                    <div class="info-row"><b>Email:</b> <span>{{ $booking->company->email }}</span></div>
                @else
                    <div class="info-row"><b>Guest:</b> <span>{{ $booking->guest_name }}</span></div>
                    <div class="info-row"><b>Email:</b> <span>{{ $booking->guest_email }}</span></div>
                    <div class="info-row"><b>Phone:</b> <span>{{ $booking->guest_phone ?? 'N/A' }}</span></div>
                @endif
            </div>
            
            @if($isCorporate && $booking->company)
            <div class="info-card">
                <div class="card-title"><i class="fa fa-user-circle"></i> Leader / Guider Info</div>
                <div class="info-row"><b>Full Name:</b> <span>{{ $booking->company->contact_person ?? $booking->guest_name }}</span></div>
                <div class="info-row"><b>Email:</b> <span>{{ $booking->company->guider_email ?? $booking->guest_email }}</span></div>
                <div class="info-row"><b>Phone:</b> <span>{{ $booking->company->guider_phone ?? ($booking->guest_phone ?? 'N/A') }}</span></div>
            </div>
            @else
            <div class="info-card">
                <div class="card-title"><i class="fa fa-calendar"></i> Stay Details</div>
                <div class="info-row"><b>Reference:</b> <span>{{ $booking->booking_reference }}</span></div>
                <div class="info-row"><b>Dates:</b> <span>{{ $booking->check_in->format('M d') }} - {{ $booking->check_out->format('M d, Y') }}</span></div>
                <div class="info-row"><b>Room:</b> <span>{{ $booking->room->room_number }} ({{ $booking->room->room_type }})</span></div>
            </div>
            @endif
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Description</th>
                    <th style="text-align: center;">Nights</th>
                    <th style="text-align: right;">Rate (TZS)</th>
                    <th style="text-align: right;">Total (TZS)</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($isGuestWithSelfPaidServices) && $isGuestWithSelfPaidServices)
                    {{-- Show only services for this guest --}}
                    @php
                        $guestServices = $booking->serviceRequests()->where('payment_status', 'paid')->with('service')->get();
                    @endphp
                    @foreach($guestServices as $gs)
                    <tr>
                        <td>{{ $gs->service->name }} @if($gs->quantity > 1) <small>(x{{ $gs->quantity }})</small> @endif</td>
                        <td style="text-align: center;">-</td>
                        <td style="text-align: right;">-</td>
                        <td style="text-align: right;">{{ number_format($gs->total_price_tsh, 0) }} TZS</td>
                    </tr>
                    @endforeach
                @else
                    {{-- Default: Show Rooms listed --}}
                    @foreach($displayBookings as $b)
                    @php
                        $bookingExchangeRate = $b->locked_exchange_rate ?? $currentExchangeRate;
                        $ratePerNightTZS = $b->room->price_per_night * $bookingExchangeRate;
                        $totalPriceTZS = $b->total_price * $bookingExchangeRate;
                    @endphp
                    <tr>
                        <td>
                            <strong>Room Accommodation: {{ $b->room->room_type }}</strong><br>
                            <small style="color: grey;">Guest: {{ $b->guest_name }} | Ref: {{ $b->booking_reference }}</small>
                        </td>
                        <td style="text-align: center;">{{ $b->check_in->diffInDays($b->check_out) }}</td>
                        <td style="text-align: right;">{{ number_format($ratePerNightTZS, 0) }} TZS</td>
                        <td style="text-align: right;">{{ number_format($totalPriceTZS, 0) }} TZS</td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>

        <div class="summary-wrapper">
            <div class="summary-box">
                <div class="summary-row">
                    <b>Subtotal:</b>
                    <span>{{ number_format($grandTotalBase * $currentExchangeRate, 0) }} TZS</span>
                </div>
                <div class="summary-row total">
                    <b>Grand Total:</b>
                    <span>{{ number_format($grandTotalBase * $currentExchangeRate, 0) }} TZS</span>
                </div>
                @if(isset($isGuestWithCompanyPaidServices) && $isGuestWithCompanyPaidServices)
                    {{-- For company-billed bookings viewed by guest, show clear message --}}
                    <div class="summary-row" style="color: var(--secondary); font-weight: 700; border-top: 1px dashed #ddd; margin-top: 10px; padding-top: 10px;">
                        <b>Payment Responsibility:</b>
                        <span>Company</span>
                    </div>
                    <div class="summary-row" style="font-size: 11px; color: var(--text-light);">
                        <b>Payable by:</b>
                        <span>{{ $booking->company->name ?? 'Company' }}</span>
                    </div>
                @else
                    {{-- For individual or self-pay bookings, show normal payment details --}}
                    <div class="summary-row paid">
                        <b>Total Amount Paid:</b>
                        <span>{{ number_format($grandPaidBase * $currentExchangeRate, 0) }} TZS</span>
                    </div>
                    @if($balanceBase > 0.1)
                    <div class="summary-row" style="color: var(--danger); font-weight: 700; border-top: 1px dashed #ddd; margin-top: 10px; padding-top: 10px;">
                        <b>Remaining Balance:</b>
                        <span>{{ number_format($balanceBase * $currentExchangeRate, 0) }} TZS</span>
                    </div>
                    @endif
                @endif
            </div>
        </div>

        <div class="footer-grid">
            <!-- Payment Context -->
            <div style="background: var(--bg-light); padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <h4 style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--primary); margin-bottom: 8px;">
                    <i class="fa fa-credit-card"></i> Payment Context
                </h4>
                <p style="font-size: 11px; color: var(--text-light); margin: 0; line-height: 1.6;">
                    @if(isset($isGuestWithCompanyPaidServices) && $isGuestWithCompanyPaidServices)
                        <i class="fa fa-building" style="color: var(--secondary);"></i> 
                        <b>Company Billed</b><br>
                        Room charges for this booking are billed directly to <b>{{ $booking->company->name ?? 'the Company' }}</b>. 
                        No payment is required from you for accommodation.
                    @elseif(isset($isGuestWithSelfPaidServices) && $isGuestWithSelfPaidServices)
                        <i class="fa fa-user" style="color: var(--primary);"></i> 
                        <b>Self-Pay Services</b><br>
                        This receipt reflects only the services you paid for personally. 
                        Room charges are billed to <b>{{ $booking->company->name ?? 'the Company' }}</b>.
                    @else
                        <i class="fa fa-check-circle" style="color: var(--success);"></i> 
                        <b>{{ ucfirst($booking->payment_method ?? 'Mobile') }}</b>
                        @if($booking->payment_transaction_id) <br><b>Ref:</b> {{ $booking->payment_transaction_id }} @endif
                        @if($booking->paid_at) <br><b>Date:</b> {{ $booking->paid_at->format('M d, Y H:i') }} @endif
                    @endif
                </p>
            </div>
            
            <!-- Policies Section -->
            <div class="policies-section">
                <div class="policy-column">
                    <h4 style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--primary); margin-bottom: 8px;">
                        <i class="fa fa-ban"></i> Cancellation & No Show
                    </h4>
                    <p style="font-size: 10px; color: var(--text-light); margin: 0; line-height: 1.6;">
                        All cancellations must be received with written confirmation. Free cancellation 30 days prior. 50% fee for 14 days prior. 100% fee for 7 days prior. Full charge for 1 day before or no-show.
                    </p>
                </div>
                
                <div class="policy-column">
                    <h4 style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--primary); margin-bottom: 8px;">
                        <i class="fa fa-info-circle"></i> General Policy
                    </h4>
                    <p style="font-size: 10px; color: var(--text-light); margin: 0; line-height: 1.6;">
                        Room charges are inclusive of VAT. Guests are individually responsible for services marked as 'Self-Paid' (e.g., drinks, food, extensions) not included in the corporate bill.
                    </p>
                </div>
            </div>
            
            <!-- Signature Section -->
            <div class="signature-area">
                <div class="sig-line">Authorized Signatory</div>
                <p style="font-size: 10px; color: grey; margin-top: 5px;">Umoj Lutheran Hostel Management</p>
                <p style="font-size: 9px; color: #cbd5e1; margin-top: 20px;">Receipt Generated: {{ now()->format('Y-m-d H:i') }}</p>
            </div>
        </div>

        <div style="text-align: center; margin-top: 40px; border-top: 1px dashed #eee; padding-top: 20px;">
            <p style="font-size: 13px; font-weight: 600; color: var(--primary);">Thank you for choosing Umoj Lutheran Hostel!</p>
            <p style="font-size: 10px; color: #94a3b8; margin-top: 5px;">System Developed By <span style="color: #940000; font-weight: 700;">EmCa Technologies</span></p>
        </div>
    </div>
</body>
</html>
