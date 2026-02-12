<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Umoj Lutheran Hostel - Guest Identity Card</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #f5f5f5;
            padding: 40px 20px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .print-container {
            background: white;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            max-width: 700px;
            width: 100%;
        }
        
        /* Mobile Responsive Styles - Scale proportionally, keep exact design */
        @media (max-width: 768px) {
            body {
                padding: 20px 10px;
                align-items: center;
            }
            
            .print-container {
                padding: 20px 15px;
                border-radius: 8px;
            }
            
            .print-header {
                margin-bottom: 25px;
                padding-bottom: 15px;
            }
            
            .print-header h1 {
                font-size: 24px !important;
                margin-bottom: 8px;
            }
            
            .print-header .hotel-name {
                font-size: 20px !important;
            }
            
            .print-header p {
                font-size: 12px;
            }
            
            /* Scale card proportionally - maintain exact design and aspect ratio */
            .card-wrapper {
                width: 90% !important;
                max-width: 400px;
                height: auto !important;
                aspect-ratio: 3.375 / 2.125;
                margin: 0 auto;
            }
            
            /* Scale all internal elements proportionally */
            .card-container {
                padding: calc(8px * 0.85);
                gap: calc(10px * 0.85);
            }
            
            .card-left-section {
                width: calc(1.1in * 0.9);
                padding-right: calc(8px * 0.85);
            }
            
            .hotel-logo-img {
                height: calc(45px * 0.85) !important;
                max-width: calc(110px * 0.85);
            }
            
            .hotel-logo {
                font-size: calc(13px * 0.9);
            }
            
            .hotel-subtitle {
                font-size: calc(8px * 0.9);
            }
            
            .guest-photo-placeholder {
                width: calc(0.95in * 0.9);
                height: calc(1.15in * 0.9);
            }
            
            .photo-label {
                font-size: calc(6px * 0.9);
            }
            
            .guest-name {
                font-size: calc(14px * 0.9);
            }
            
            .guest-details {
                font-size: calc(9px * 0.9);
                gap: calc(3px * 0.9);
            }
            
            .detail-label {
                font-size: calc(8px * 0.9);
                min-width: calc(55px * 0.9);
            }
            
            .detail-value {
                font-size: calc(9px * 0.9);
            }
            
            .qr-code {
                width: calc(0.35in * 0.9);
                height: calc(0.35in * 0.9);
            }
            
            .qr-label {
                font-size: calc(4px * 0.9);
            }
            
            .booking-ref-label {
                font-size: calc(6px * 0.9);
            }
            
            .booking-ref-code {
                font-size: calc(10px * 0.9);
            }
            
            .room-label {
                font-size: calc(6px * 0.9);
            }
            
            .room-number {
                font-size: calc(16px * 0.9);
            }
            
            .room-type {
                font-size: calc(8px * 0.9);
            }
            
            .validity-info {
                font-size: calc(7px * 0.9);
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 10px;
                margin-top: 30px;
            }
            
            .btn {
                width: 100%;
                padding: 12px 20px;
                font-size: 13px;
                justify-content: center;
            }
            
            .action-buttons > div {
                font-size: 10px !important;
                padding: 12px !important;
                line-height: 1.5;
            }
            
            .action-buttons > div strong {
                font-size: 11px !important;
            }
        }
        
        /* Small Mobile Devices - Scale down more but keep design */
        @media (max-width: 480px) {
            body {
                padding: 15px 8px;
            }
            
            .print-container {
                padding: 15px 10px;
            }
            
            .print-header h1 {
                font-size: 20px !important;
            }
            
            .print-header .hotel-name {
                font-size: 18px !important;
            }
            
            /* Scale card more on very small screens */
            .card-wrapper {
                width: 95% !important;
                max-width: 350px;
            }
            
            .card-container {
                padding: calc(8px * 0.75);
                gap: calc(10px * 0.75);
            }
            
            .card-left-section {
                width: calc(1.1in * 0.8);
                padding-right: calc(8px * 0.75);
            }
            
            .hotel-logo-img {
                height: calc(45px * 0.75) !important;
                max-width: calc(110px * 0.75);
            }
            
            .hotel-logo {
                font-size: calc(13px * 0.8);
            }
            
            .hotel-subtitle {
                font-size: calc(8px * 0.8);
            }
            
            .guest-photo-placeholder {
                width: calc(0.95in * 0.8);
                height: calc(1.15in * 0.8);
            }
            
            .photo-label {
                font-size: calc(6px * 0.8);
            }
            
            .guest-name {
                font-size: calc(14px * 0.8);
            }
            
            .guest-details {
                font-size: calc(9px * 0.8);
                gap: calc(3px * 0.8);
            }
            
            .detail-label {
                font-size: calc(8px * 0.8);
                min-width: calc(55px * 0.8);
            }
            
            .detail-value {
                font-size: calc(9px * 0.8);
            }
            
            .qr-code {
                width: calc(0.35in * 0.8);
                height: calc(0.35in * 0.8);
            }
            
            .qr-label {
                font-size: calc(4px * 0.8);
            }
            
            .booking-ref-label {
                font-size: calc(6px * 0.8);
            }
            
            .booking-ref-code {
                font-size: calc(10px * 0.8);
            }
            
            .room-label {
                font-size: calc(6px * 0.8);
            }
            
            .room-number {
                font-size: calc(16px * 0.8);
            }
            
            .room-type {
                font-size: calc(8px * 0.8);
            }
            
            .validity-info {
                font-size: calc(7px * 0.8);
            }
            
            .btn {
                padding: 10px 15px;
                font-size: 12px;
            }
        }
        
        /* Landscape Mobile - Keep horizontal layout, scale proportionally */
        @media (max-width: 768px) and (orientation: landscape) {
            .card-wrapper {
                width: 70% !important;
                max-width: 500px;
            }
        }
        
        .print-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e07632;
        }
        
        .print-header h1 {
            color: #363636;
            font-size: 32px;
            font-weight: 400;
            margin-bottom: 10px;
            letter-spacing: 2px;
        }
        
        .print-header .hotel-name {
            color: #e07632;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .print-header p {
            color: #7d7d7d;
            font-size: 14px;
        }
        
        .card-wrapper {
            background: white;
            border: 2px solid #e07632;
            border-radius: 8px;
            padding: 0;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(224, 118, 50, 0.15);
            margin: 0 auto;
            width: 3.375in;
            height: 2.125in;
            position: relative;
        }
        
        .card-container {
            width: 100%;
            height: 100%;
            background: #ffffff;
            position: relative;
            padding: 8px;
            display: flex;
            flex-direction: row;
            gap: 10px;
        }
        
        .card-left-section {
            width: 1.1in;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-right: 1px solid #e0e0e0;
            padding-right: 8px;
        }
        
        .card-right-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding-left: 4px;
        }
        
        .card-header {
            margin-bottom: 4px;
            padding-bottom: 4px;
            border-bottom: 1px solid #e07632;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .hotel-logo-img {
            height: 45px;
            width: auto;
            max-width: 110px;
            object-fit: contain;
        }
        
        .hotel-text-brand {
            margin-top: 4px;
            text-align: center;
        }
        
        .hotel-logo {
            font-size: 13px;
            font-weight: 700;
            color: #363636;
            letter-spacing: 1px;
            margin-bottom: 1px;
            line-height: 1.2;
        }
        
        .hotel-subtitle {
            font-size: 8px;
            color: #7d7d7d;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            line-height: 1.1;
        }
        
        .guest-photo-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 4px;
        }
        
        .guest-photo-placeholder {
            width: 0.95in;
            height: 1.15in;
            background: linear-gradient(to bottom, #f8f8f8 0%, #e8e8e8 100%);
            border: 1.5px solid #e07632;
            border-radius: 4px;
            display: flex !important;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        .guest-photo-placeholder #guestPhotoImg {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none !important;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 2;
        }
        
        .guest-photo-placeholder.has-image #guestPhotoImg {
            display: block !important;
        }
        
        .placeholder-default-img {
            width: 85% !important;
            height: 85% !important;
            object-fit: contain !important;
            display: block !important;
            position: absolute !important;
            top: 50% !important;
            left: 50% !important;
            transform: translate(-50%, -50%) !important;
            z-index: 1 !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        .guest-photo-placeholder.has-image .placeholder-default-img {
            display: none !important;
        }
        
        .photo-icon,
        .photo-text {
            display: none !important;
        }
        
        .photo-label {
            font-size: 6px;
            color: #999;
            text-align: center;
            margin-top: 2px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .guest-info-section {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }
        
        .guest-name-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
            gap: 8px;
        }
        
        .guest-name {
            font-size: 14px;
            font-weight: 700;
            color: #363636;
            letter-spacing: 0.3px;
            line-height: 1.3;
            text-transform: uppercase;
            flex: 1;
        }
        
        .guest-details {
            display: flex;
            flex-direction: column;
            gap: 3px;
            font-size: 9px;
            margin-bottom: 6px;
        }
        
        .detail-item {
            display: flex;
            align-items: baseline;
            gap: 6px;
            line-height: 1.4;
        }
        
        .detail-label {
            color: #7d7d7d;
            font-weight: 600;
            min-width: 55px;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .detail-value {
            color: #363636;
            font-weight: 500;
            font-size: 9px;
            flex: 1;
        }
        
        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: auto;
            padding-top: 4px;
            border-top: 1px solid #e07632;
            gap: 8px;
        }
        
        .booking-reference {
            flex: 1;
        }
        
        .booking-ref-label {
            font-size: 6px;
            font-weight: 600;
            color: #7d7d7d;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        
        .booking-ref-code {
            color: #e07632;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.5px;
            line-height: 1.2;
        }
        
        .room-section {
            text-align: right;
            flex-shrink: 0;
        }
        
        .room-label {
            font-size: 6px;
            color: #7d7d7d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        
        .room-number {
            font-size: 16px;
            font-weight: 700;
            color: #e07632;
            margin-bottom: 1px;
            line-height: 1.2;
        }
        
        .room-type {
            font-size: 8px;
            color: #7d7d7d;
            font-weight: 500;
        }
        
        .validity-info {
            font-size: 7px;
            color: #999;
            text-align: center;
            margin-top: 4px;
            padding-top: 4px;
            border-top: 1px solid #e0e0e0;
            width: 100%;
        }
        
        .photo-label {
            font-size: 6px;
            color: #999;
            text-align: center;
            margin-top: 2px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .qr-code-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1px;
            flex-shrink: 0;
        }
        
        .qr-code {
            width: 0.35in;
            height: 0.35in;
            border: 1px solid #e0e0e0;
            border-radius: 2px;
            background: white;
            padding: 1px;
        }
        
        .qr-code img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        .qr-label {
            font-size: 4px;
            color: #999;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            font-weight: 600;
        }
        
        .security-pattern {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            opacity: 0.02;
            background-image: 
                repeating-linear-gradient(45deg, transparent, transparent 8px, #e07632 8px, #e07632 16px),
                repeating-linear-gradient(-45deg, transparent, transparent 8px, #e07632 8px, #e07632 16px);
            pointer-events: none;
            z-index: 0;
        }
        
        .card-container > * {
            position: relative;
            z-index: 1;
        }
        
        .action-buttons {
            text-align: center;
            margin-top: 40px;
            display: flex;
            gap: 15px;
            justify-content: center;
        }
        
        .btn {
            padding: 14px 35px;
            border: 2px solid #e07632;
            border-radius: 0;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 500ms;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn-primary {
            background-color: #e07632;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #c8651f;
            border-color: #c8651f;
        }
        
        .btn-secondary {
            background: transparent;
            color: #363636;
        }
        
        .btn-secondary:hover {
            background-color: #e07632;
            color: white;
        }
        
        @media print {
            /* Remove all margins and padding from page */
            @page {
                margin: 0 !important;
                padding: 0 !important;
                size: A4;
                
                /* Remove all page headers and footers */
                @top-left { content: none !important; }
                @top-center { content: none !important; }
                @top-right { content: none !important; }
                @bottom-left { content: none !important; }
                @bottom-center { content: none !important; }
                @bottom-right { content: none !important; }
                
                margin-top: 0 !important;
                margin-bottom: 0 !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
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
            
            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            html {
                margin: 0 !important;
                padding: 0 !important;
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
            
            /* Ensure clean print output with colors */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            
            /* Remove any background elements except card */
            body > * {
                background: white !important;
            }
            
            .card-wrapper {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            
            /* Keep existing body styles but ensure margins are 0 */
            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                min-height: 100vh !important;
            }
            
            .print-container {
                box-shadow: none !important;
                padding: 0 !important;
                max-width: 100% !important;
                margin: 0 auto !important;
                background: white !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                width: 100% !important;
                height: 100vh !important;
            }
            
            .print-header,
            .action-buttons {
                display: none !important;
            }
            
            .card-wrapper {
                margin: 0 auto !important;
                border: 2px solid #e07632 !important;
                background: white !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                width: 3.375in !important;
                height: 2.125in !important;
                position: relative !important;
                page-break-after: avoid !important;
                page-break-before: avoid !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            
            .card-container {
                box-shadow: none !important;
                background: #ffffff !important;
                padding: 8px !important;
                width: 100% !important;
                height: 100% !important;
            }
            
            .card-header {
                border-bottom: 2px solid #e07632 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            .hotel-logo-img {
                height: 45px !important;
                width: auto !important;
                max-width: 120px !important;
                object-fit: contain !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            .hotel-logo {
                color: #363636 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            .hotel-subtitle {
                color: #7d7d7d !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            .card-footer {
                border-top: 2px solid #e07632 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            .card-type-badge {
                background: #e07632 !important;
                color: white !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            .room-number {
                color: #e07632 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            .booking-ref-code {
                color: #e07632 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            .guest-name {
                color: #363636 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            .detail-label {
                color: #7d7d7d !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            .detail-value {
                color: #363636 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            .room-type {
                color: #7d7d7d !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            .validity-info {
                color: #999 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            .photo-label {
                color: #999 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            .qr-label {
                color: #999 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            .security-pattern {
                opacity: 0.03 !important;
            }
            
            .qr-code-section {
                display: flex !important;
            }
            
            .qr-code {
                border: 1px solid #e0e0e0 !important;
                background: white !important;
            }
            
            .guest-photo-placeholder {
                border: 2px solid #e07632 !important;
                background: linear-gradient(to bottom, #f8f8f8 0%, #e8e8e8 100%) !important;
            }
            
            .photo-icon {
                color: #e07632 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>
    <div class="print-container">
        <div class="print-header">
            <div class="hotel-name">Umoj Lutheran Hostel</div>
            <h1>GUEST IDENTITY CARD</h1>
            <p>Official Guest Identification Card</p>
        </div>
        
        <div class="card-wrapper">
            <div class="security-pattern"></div>
            <div class="card-container">
                <div class="card-left-section">
                    <div class="card-header">
                        <img src="{{ asset('royal-master/image/logo/Logo.png') }}" alt="Umoj Lutheran Hostel Logo" class="hotel-logo-img" onerror="this.style.display='none';">
                    </div>
                    <div class="hotel-text-brand">
                        <div class="hotel-logo">Umoj Lutheran Hostel</div>
                        <div class="hotel-subtitle">Hotel</div>
                    </div>
                    <div class="guest-photo-section">
                        <div class="guest-photo-placeholder {{ $guestPhotoUrl ? 'has-image' : '' }}" id="guestPhotoPlaceholder">
                            @if($guestPhotoUrl)
                                <img src="{{ $guestPhotoUrl }}" alt="Guest Photo" id="guestPhotoImg" onerror="this.style.display='none'; this.parentElement.classList.remove('has-image');">
                            @else
                                <img src="" alt="Guest Photo" id="guestPhotoImg" style="display: none;" onerror="this.style.display='none'; this.parentElement.classList.remove('has-image');">
                            @endif
                            <img src="{{ asset('dashboard_assets/images/img placeholder.jpg') }}" alt="Photo Placeholder" class="placeholder-default-img" onerror="console.error('Placeholder image failed to load:', this.src); this.style.backgroundColor='#e0e0e0';">
                            <i class="fas fa-user photo-icon"></i>
                            <div class="photo-text">Photo</div>
                        </div>
                        <div class="photo-label">Guest Photo</div>
                    </div>
                </div>
                
                <div class="card-right-section">
                    <div class="guest-info-section">
                        <div class="guest-name-wrapper">
                            <div class="guest-name">{{ strtoupper($booking->guest_name) }}</div>
                            <div class="qr-code-section">
                                <div class="qr-code">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data={{ urlencode(route('booking.public.details', $booking)) }}" alt="QR Code" onerror="this.style.display='none';">
                                </div>
                                <div class="qr-label">Scan</div>
                            </div>
                        </div>
                        <div class="guest-details">
                            <div class="detail-item">
                                <span class="detail-label">Email:</span>
                                <span class="detail-value">{{ $booking->guest_email }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Phone:</span>
                                <span class="detail-value">{{ $booking->guest_phone }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Check-in:</span>
                                <span class="detail-value">{{ $booking->check_in->format('M d, Y') }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Check-out:</span>
                                <span class="detail-value">{{ $booking->check_out->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <div class="booking-reference">
                            <div class="booking-ref-label">Booking Ref</div>
                            <div class="booking-ref-code">{{ $booking->booking_reference }}</div>
                        </div>
                        <div class="room-section">
                            <div class="room-label">Room</div>
                            <div class="room-number">{{ $booking->room->room_number ?? 'N/A' }}</div>
                            <div class="room-type">{{ $booking->room->room_type ?? 'N/A' }}</div>
                        </div>
                    </div>
                    
                    <div class="validity-info">
                        Valid: {{ $booking->check_in->format('M d') }} - {{ $booking->check_out->format('M d, Y') }}
                    </div>
                </div>
            </div>
        </div>
        
        <div class="action-buttons">
            <button onclick="printCard()" class="btn btn-primary">
                <i class="fas fa-print"></i> Print Card
            </button>
            <a href="{{ route('customer.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
    
    <script>
        // Initialize guest photo display on page load
        document.addEventListener('DOMContentLoaded', function() {
            var guestPhotoImg = document.getElementById('guestPhotoImg');
            var guestPhotoPlaceholder = document.getElementById('guestPhotoPlaceholder');
            
            @if($guestPhotoUrl)
                // Photo URL is provided from server
                if (guestPhotoImg) {
                    guestPhotoImg.src = '{{ $guestPhotoUrl }}';
                    guestPhotoImg.onload = function() {
                        if (guestPhotoPlaceholder) {
                            guestPhotoPlaceholder.classList.add('has-image');
                        }
                    };
                    guestPhotoImg.onerror = function() {
                        if (guestPhotoPlaceholder) {
                            guestPhotoPlaceholder.classList.remove('has-image');
                        }
                    };
                }
            @endif
        });
        
        function printCard() {
            // Hide the action buttons before printing
            var actionButtons = document.querySelector('.action-buttons');
            if (actionButtons) {
                actionButtons.style.display = 'none';
            }
            
            // Trigger print dialog
            window.print();
            
            // Show the action buttons again after print dialog closes
            setTimeout(function() {
                if (actionButtons) {
                    actionButtons.style.display = 'flex';
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
        
        // Optional: Auto-print on load
        // window.onload = function() {
        //     setTimeout(function() {
        //         printCard();
        //     }, 500);
        // };
    </script>
</body>
</html>
