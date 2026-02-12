<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
        <link rel="icon" href="{{ asset('royal-master/image/logo/Logo.png') }}" type="image/png">
        <title>Book a Room - PrimeLand Hotel</title>
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="{{ asset('royal-master/css/bootstrap.css') }}">
        <link rel="stylesheet" href="{{ asset('royal-master/vendors/linericon/style.css') }}">
        <link rel="stylesheet" href="{{ asset('royal-master/css/font-awesome.min.css') }}">
        <link rel="stylesheet" href="{{ asset('royal-master/vendors/owl-carousel/owl.carousel.min.css') }}">
        <link rel="stylesheet" href="{{ asset('royal-master/vendors/nice-select/css/nice-select.css') }}">
        <!-- Bootstrap Datepicker - Original template uses this -->
        <!-- main css -->
        <link rel="stylesheet" href="{{ asset('royal-master/css/style.css') }}">
        <link rel="stylesheet" href="{{ asset('royal-master/css/responsive.css') }}">
        
        <!-- Font Awesome Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        
        <!-- Meta CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        /* Modern Footer Design with Background Image */
        .modern-footer {
            position: relative;
            overflow: hidden;
            padding: 80px 0 40px !important;
        }
        
        .footer-bg-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('{{ asset("landing_page_backround_images/night view_.jpg") }}');
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            z-index: 0;
        }
        
        .footer-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.75);
            z-index: 1;
        }
        
        .modern-footer .footer_title {
            color: #e77a3a !important;
            font-size: 18px !important;
            font-weight: 700 !important;
            margin-bottom: 20px !important;
            margin-top: 20px !important;
            padding-bottom: 12px !important;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            display: inline-block;
            width: 100%;
        }
        
        .modern-footer .footer_title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: #e77a3a;
            border-radius: 2px;
        }
        
        .modern-footer .single-footer-widget {
            padding-top: 20px;
        }
        
        .footer-logo-section {
            padding-top: 0 !important;
        }
        
        .footer-logo-section .footer_title {
            margin-top: 0 !important;
        }
        
        .footer-logo {
            margin-bottom: 20px;
            position: relative;
            z-index: 3;
        }
        
        .footer-logo img {
            max-width: 200px;
            height: auto;
            display: block;
            position: relative;
            z-index: 3;
            background: rgba(255, 255, 255, 0.1);
            padding: 10px;
            border-radius: 8px;
        }
        
        /* Payment Gateways Styling */
        .payment-gateways {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 15px;
        }
        
        .payment-icon {
            background: rgba(255, 255, 255, 0.1);
            padding: 12px;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 70px;
            height: 50px;
            cursor: pointer;
        }
        
        .payment-icon:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-3px);
        }
        
        .payment-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }
        
        /* Social Media Icons Styling */
        .footer-social-icons {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 15px;
        }
        
        .social-media-icon {
            display: inline-block;
            transition: all 0.3s ease;
            border-radius: 50%;
            padding: 8px;
            background: rgba(255, 255, 255, 0.1);
        }
        
        .social-media-icon:hover {
            transform: translateY(-5px) scale(1.1);
            background: rgba(255, 255, 255, 0.2);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        
        .social-media-icon img {
            display: block;
            width: 40px;
            height: 40px;
            transition: transform 0.3s ease;
        }
        
        .social-media-icon:hover img {
            transform: scale(1.1);
        }
        
        /* Newsletter Input Styling */
        .modern-footer .subscribe_form input {
            color: #fff !important;
        }
        
        .modern-footer .subscribe_form input::placeholder {
            color: rgba(255, 255, 255, 0.6) !important;
        }
        
        /* Footer Bottom */
        .modern-footer .footer-bottom {
            padding-top: 20px;
        }
        
        .modern-footer .footer-bottom .text-right {
            text-align: right;
        }
        
        .modern-footer .footer-bottom .text-right span {
            color: #ff0000 !important;
            font-weight: 600;
        }
        
        @media (max-width: 768px) {
            .modern-footer .footer-bottom .text-right {
                text-align: center;
                margin-top: 15px;
            }
            
            .modern-footer .footer-bottom p {
                text-align: center !important;
            }
            
            .modern-footer {
                padding: 60px 0 30px !important;
            }
            
            .payment-gateways {
                gap: 10px;
            }
            
            .payment-icon {
                width: 60px;
                height: 40px;
                padding: 8px;
            }
            
            .footer-social-icons {
                gap: 10px;
            }
            
            .social-media-icon {
                padding: 6px;
            }
            
            .social-media-icon img {
                width: 35px;
                height: 35px;
            }
        }
        
        /* Remove purple gradient - match home page styling */
        .booking-search-section {
            background: transparent;
            padding: 0;
            margin-bottom: 0;
        }
        
        /* ============================================
           MOBILE-FIRST BASE STYLES
           Start with mobile, enhance for larger screens
           ============================================ */
        
        .available-rooms-section {
            padding: 20px 0 40px 0;
            min-height: 300px;
        }
        
        .available-rooms-section .section-heading {
            margin-top: 0;
            padding-top: 0;
            margin-bottom: 25px;
        }
        
        .available-rooms-section .section-heading h2 {
            font-size: 22px;
            margin-bottom: 10px;
        }
        
        .available-rooms-section .container {
            padding-left: 15px;
            padding-right: 15px;
        }
        
        /* Breadcrumb Mobile-First */
        .breadcumb-area {
            padding: 30px 0;
            min-height: 120px;
        }
        
        .bradcumbContent h2 {
            font-size: 20px;
            margin-bottom: 5px;
        }
        
        /* Booking Form Mobile-First */
        .book-now-area {
            padding: 25px 0;
        }
        
        .book-now-form {
            padding: 25px 18px;
        }
        
        .book-now-form .form-group {
            margin-bottom: 20px;
        }
        
        .book-now-form label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            font-size: 15px;
        }
        
        .book-now-form input[type="date"],
        .book-now-form select {
            width: 100%;
            padding: 16px 18px;
            font-size: 16px; /* Prevents zoom on iOS */
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            background: white;
            box-sizing: border-box;
            min-height: 48px; /* Touch target */
        }
        
        .book-now-form input[type="date"]:focus,
        .book-now-form select:focus {
            outline: none;
            border-color: #e77a3a;
            box-shadow: 0 0 0 4px rgba(231, 122, 58, 0.15);
        }
        
        .book-now-form button[type="submit"] {
            width: 100%;
            padding: 18px;
            font-size: 17px;
            font-weight: 700;
            background: linear-gradient(135deg, #e77a3a 0%, #d66a2a 100%);
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            margin-top: 15px;
            min-height: 52px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 15px rgba(231, 122, 58, 0.4);
        }
        
        /* Book Now Button - Add pointer cursor */
        .book_now_btn {
            cursor: pointer !important;
        }
        
        .book_now_btn:hover {
            cursor: pointer !important;
        }
        
        /* Use existing hotel design classes */
        .room-card {
            /* Will use single-rooms-area class from existing CSS */
        }
        
        .room-image-container {
            position: relative;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }
        
        /* Override bg-thumbnail to support multiple images */
        .single-rooms-area .bg-thumbnail {
            position: absolute !important;
            width: 100% !important;
            height: 100% !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            z-index: 10 !important;
            background-size: cover !important;
            background-position: center !important;
            background-repeat: no-repeat !important;
            background-color: #000 !important;
        }
        
        .bg-thumbnail-slider {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 11;
            pointer-events: none;
        }
        
        .bg-thumbnail-slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.6s ease-in-out;
            background-size: cover !important;
            background-position: center !important;
            background-repeat: no-repeat !important;
            z-index: 1;
            pointer-events: none;
        }
        
        .bg-thumbnail-slide.active {
            opacity: 1 !important;
            z-index: 2;
        }
        
        .room-image-slider {
            position: relative;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }
        
        .room-image-slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.6s ease-in-out;
            object-fit: cover;
        }
        
        .room-image-slide.active {
            opacity: 1;
            z-index: 1;
        }
        
        .room-image-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.95);
            border: 2px solid rgba(224, 118, 50, 0.3);
            width: 45px;
            height: 45px;
            border-radius: 50%;
            cursor: pointer;
            z-index: 20 !important;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            font-size: 20px;
            font-weight: bold;
            color: #e77a3a;
            box-shadow: 0 3px 12px rgba(0,0,0,0.25);
            pointer-events: auto;
            opacity: 0.9;
        }
        
        .room-image-nav:hover {
            background: #ffffff;
            border-color: #e77a3a;
            transform: translateY(-50%) scale(1.15);
            box-shadow: 0 5px 20px rgba(224, 118, 50, 0.4);
            opacity: 1;
            color: #d66a2a;
        }
        
        .room-image-nav:active {
            transform: translateY(-50%) scale(1.05);
        }
        
        .room-image-nav.prev {
            left: 12px;
        }
        
        .room-image-nav.next {
            right: 12px;
        }
        
        .room-image-indicators {
            position: absolute;
            bottom: 12px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 6px;
            z-index: 20 !important;
            background: rgba(0, 0, 0, 0.3);
            padding: 6px 12px;
            border-radius: 20px;
            backdrop-filter: blur(5px);
        }
        
        .room-image-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.4);
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.6);
        }
        
        .room-image-indicator:hover {
            background: rgba(255, 255, 255, 0.7);
            transform: scale(1.2);
        }
        
        .room-image-indicator.active {
            background: #ffffff;
            width: 26px;
            border-radius: 4px;
            border-color: #ffffff;
            box-shadow: 0 0 8px rgba(255, 255, 255, 0.5);
        }
        
        .room-image-count {
            position: absolute;
            top: 12px;
            right: 12px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            z-index: 20 !important;
            backdrop-filter: blur(10px);
            pointer-events: none;
            border: 1px solid rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .room-image-count::before {
            content: '📷';
            font-size: 12px;
        }
        
        /* Room Title Wrapper - Flex container for title and button */
        .room-title-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .room-title-wrapper h4 {
            margin: 0;
            flex: 1;
            min-width: 0;
        }
        
        /* View Gallery Button - Right aligned next to title */
        .view-gallery-btn {
            background: #e77a3a;
            color: white;
            border: 2px solid #e77a3a;
            padding: 8px 18px;
            border-radius: 25px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(231, 122, 58, 0.3);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
            flex-shrink: 0;
        }
        
        .view-gallery-btn:hover {
            background: #d66a2a;
            border-color: #d66a2a;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(231, 122, 58, 0.4);
        }
        
        .view-gallery-btn::before {
            content: '🖼️';
            font-size: 14px;
        }
        
        /* Terms & Conditions Modal Styles */
        .terms-modal {
            display: none;
            position: fixed;
            z-index: 10000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
        }
        
        .terms-modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 0;
            border: none;
            border-radius: 10px;
            width: 90%;
            max-width: 800px;
            max-height: 85vh;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            display: flex;
            flex-direction: column;
        }
        
        .terms-modal-header {
            padding: 20px 30px;
            background: #e77a3a;
            color: white;
            border-radius: 10px 10px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .terms-modal-header h2 {
            margin: 0;
            font-size: 24px;
        }
        
        .close-terms-modal {
            color: white;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            line-height: 1;
        }
        
        .close-terms-modal:hover {
            opacity: 0.7;
        }
        
        .terms-modal-body {
            padding: 30px;
            overflow-y: auto;
            flex: 1;
        }
        
        .terms-modal-body h3 {
            color: #e77a3a;
            margin-top: 20px;
            margin-bottom: 10px;
            font-size: 18px;
        }
        
        .terms-modal-body h3:first-child {
            margin-top: 0;
        }
        
        .terms-modal-body p {
            margin-bottom: 15px;
            line-height: 1.6;
            color: #333;
        }
        
        .terms-modal-footer {
            padding: 20px 30px;
            background: #f8f9fa;
            border-radius: 0 0 10px 10px;
            text-align: right;
        }
        
        .terms-modal-footer .btn {
            padding: 10px 30px;
            background: #e77a3a;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
        }
        
        .terms-modal-footer .btn:hover {
            background: #d6692a;
        }
        
        @media only screen and (max-width: 767px) {
            .terms-modal-content {
                width: 95%;
                margin: 10% auto;
                max-height: 90vh;
            }
            
            .terms-modal-header,
            .terms-modal-body,
            .terms-modal-footer {
                padding: 15px 20px;
            }
            
            .terms-modal-header h2 {
                font-size: 20px;
            }
        }
        
        /* Mobile adjustments */
        @media only screen and (max-width: 767px) {
            .room-title-wrapper {
                flex-direction: row;
                align-items: center;
                gap: 8px;
                margin-bottom: 12px;
            }
            
            .room-title-wrapper h4 {
                font-size: 20px !important;
                flex: 1;
                min-width: 0;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
            
            /* Hide original text and show shorter version on mobile */
            .view-gallery-btn {
                width: auto;
                min-width: auto;
                padding: 6px 12px;
                font-size: 0; /* Hide original text */
                flex-shrink: 0;
                white-space: nowrap;
                letter-spacing: 0.3px;
                max-width: 45%;
                position: relative;
            }
            
            .view-gallery-btn::before {
                content: '🖼️';
                font-size: 12px;
                margin-right: 4px;
                display: inline-block;
            }
            
            .view-gallery-btn::after {
                content: 'Gallery';
                font-size: 11px;
                display: inline-block;
            }
            
            /* Even shorter on very small screens */
            @media only screen and (max-width: 400px) {
                .view-gallery-btn {
                    padding: 6px 10px;
                    max-width: 40%;
                }
                
                .view-gallery-btn::after {
                    content: 'Photos';
                    font-size: 10px;
                }
            }
        }
        
        /* Extra small mobile devices */
        @media only screen and (max-width: 480px) {
            .room-title-wrapper {
                gap: 6px;
                margin-bottom: 10px;
            }
            
            .room-title-wrapper h4 {
                font-size: 18px !important;
            }
            
            .view-gallery-btn {
                padding: 5px 10px;
                font-size: 10px;
            }
            
            .view-gallery-btn::before {
                font-size: 11px;
            }
        }
        
        /* Very small screens - stack if needed */
        @media only screen and (max-width: 360px) {
            .room-title-wrapper {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
            
            .view-gallery-btn {
                width: 100%;
                justify-content: center;
                padding: 8px 16px;
                font-size: 11px;
            }
        }
        
        /* Make the entire image area clickable */
        .single-rooms-area .bg-thumbnail {
            cursor: pointer;
            position: relative;
        }
        
        /* Optional: Add subtle hint on hover that image is also clickable */
        .single-rooms-area .bg-thumbnail::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0);
            transition: background 0.3s ease;
            z-index: 10;
            pointer-events: none;
        }
        
        .single-rooms-area .bg-thumbnail:hover::before {
            background: rgba(0, 0, 0, 0.1);
        }
        
        /* Ensure navigation buttons and indicators don't trigger gallery */
        .room-image-nav,
        .room-image-indicator,
        .room-image-count,
        .view-gallery-btn {
            pointer-events: auto;
        }
        
        /* Image Count with Current/Total Format */
        .room-image-count-current {
            font-weight: 700;
        }
        
        /* Currency Tabs Styling */
        .currency-tabs {
            position: relative;
        }
        
        .currency-tab {
            position: relative;
            z-index: 1;
        }
        
        .currency-tab:hover {
            opacity: 0.9;
        }
        
        .currency-tab.active {
            box-shadow: 0 2px 8px rgba(231, 122, 58, 0.3);
        }
        
        @media only screen and (max-width: 767px) {
            .currency-tabs {
                width: 100%;
            }
            
            .currency-tab {
                padding: 10px 15px !important;
                font-size: 14px;
            }
        }
        
        /* Compact Modern Room Card Design */
        .single-rooms-area {
            height: auto !important;
            min-height: auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            flex-direction: column;
        }
        
        .single-rooms-area:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(231, 122, 58, 0.15);
        }
        
        /* Image Section - More Compact */
        .single-rooms-area .bg-thumbnail {
            position: relative !important;
            width: 100% !important;
            height: 220px !important;
            border-radius: 12px 12px 0 0;
            overflow: hidden;
        }
        
        /* Compact Price Badge */
        .single-rooms-area .price-from {
            position: absolute !important;
            z-index: 25 !important;
            top: 12px !important;
            right: 12px !important;
            left: auto !important;
            transform: none !important;
            padding: 8px 14px !important;
            background: linear-gradient(135deg, #e77a3a 0%, #d66a2a 100%) !important;
            border: none !important;
            border-radius: 8px !important;
            display: inline-block !important;
            margin-bottom: 0 !important;
            line-height: 1.2 !important;
            color: #ffffff !important;
            font-size: 15px !important;
            font-weight: 700 !important;
            white-space: nowrap;
            box-shadow: 0 2px 8px rgba(231, 122, 58, 0.4);
            text-align: center;
        }
        
        .price-currency-symbol {
            font-size: 13px;
            vertical-align: top;
            margin-right: 1px;
        }
        
        .price-amount {
            font-size: 20px;
            font-weight: 700;
        }
        
        .room-price-per-night {
            font-size: 12px !important;
            color: rgba(255, 255, 255, 0.9) !important;
            margin-left: 4px;
            font-weight: 500;
            display: inline;
        }
        
        /* Compact Content Section */
        .single-rooms-area .rooms-text {
            position: relative !important;
            z-index: 1 !important;
            background-color: #ffffff !important;
            padding: 18px !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            width: 100% !important;
            border: none !important;
            text-align: left !important;
            max-height: none !important;
            overflow: visible !important;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .single-rooms-area .rooms-text h4 {
            color: #1a1a1a !important;
            margin-bottom: 10px;
            font-size: 20px;
            font-weight: 700;
            line-height: 1.3;
        }
        
        .single-rooms-area .rooms-text p {
            color: #666666 !important;
            margin-bottom: 12px;
            line-height: 1.5;
            font-size: 13px;
        }
        
        .single-rooms-area .line {
            display: none;
        }
        
        /* Room details section - visible on dark background */
        .room-details-section {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #f0f0f0;
        }
        
        .room-details-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
            margin-bottom: 12px;
        }
        
        .room-detail-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: #333333 !important;
            line-height: 1.4;
            padding: 6px 8px;
            background: #f8f9fa;
            border-radius: 6px;
            transition: all 0.2s ease;
        }
        
        .room-detail-item:hover {
            background: #eef2f5;
        }
        
        .room-detail-item i {
            color: #e77a3a !important;
            font-size: 14px;
            width: 16px;
            text-align: center;
            flex-shrink: 0;
        }
        
        .room-detail-item span {
            flex: 1;
            color: #333333 !important;
        }
        
        .room-detail-item strong {
            color: #1a1a1a !important;
            font-weight: 600;
        }
        
        .room-amenities-list {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #f0f0f0;
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            position: relative !important;
            z-index: 1 !important;
        }
        
        .room-amenities-list h6 {
            font-size: 12px;
            font-weight: 700;
            color: #1a1a1a !important;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            display: block !important;
            visibility: visible !important;
        }
        
        .room-amenities-list h6 i {
            color: #e77a3a !important;
            margin-right: 6px;
            font-size: 11px;
        }
        
        .room-amenities-grid {
            display: flex !important;
            flex-wrap: wrap;
            gap: 5px;
            visibility: visible !important;
            margin-bottom: 0;
            max-height: 60px;
            overflow: hidden;
        }
        
        .room-amenity-badge {
            background: #f8f9fa !important;
            border: 1px solid #e77a3a;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 10px;
            color: #e77a3a !important;
            font-weight: 600;
            display: inline-block !important;
            visibility: visible !important;
            opacity: 1 !important;
            white-space: nowrap;
            transition: all 0.2s ease;
            line-height: 1.3;
        }
        
        .room-amenity-badge:hover {
            background: #e77a3a !important;
            color: #ffffff !important;
        }
        
        .room-badges {
            display: flex;
            gap: 8px;
            margin-top: 12px;
            flex-wrap: wrap;
        }
        
        .room-badge {
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .room-badge.pet-friendly {
            background: rgba(46, 125, 50, 0.8) !important;
            color: #ffffff !important;
            border: 1px solid #4caf50;
        }
        
        .room-badge.smoking {
            background: rgba(230, 81, 0, 0.8) !important;
            color: #ffffff !important;
            border: 1px solid #ff9800;
        }
        
        .room-room-number {
            font-size: 11px;
            color: #999999 !important;
            margin-bottom: 8px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .room-availability-badge {
            font-size: 12px;
            color: #28a745 !important;
            margin-bottom: 0;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
            padding: 4px 10px;
            background: rgba(40, 167, 69, 0.1);
            border-radius: 12px;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }
        
        .room-availability-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }
        
        .view-more-btn {
            background: transparent;
            border: 1px solid #e77a3a;
            color: #e77a3a;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .view-more-btn:hover {
            background: #e77a3a;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(231, 122, 58, 0.3);
        }
        
        .room-title-wrapper {
            margin-bottom: 10px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .room-amenities-inline {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-top: 0;
        }
        
        /* Scrollbar styling for overflow */
        .single-rooms-area .rooms-text::-webkit-scrollbar {
            width: 6px;
        }
        
        .single-rooms-area .rooms-text::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
        }
        
        .single-rooms-area .rooms-text::-webkit-scrollbar-thumb {
            background: #e07632;
            border-radius: 3px;
        }
        
        .single-rooms-area .rooms-text::-webkit-scrollbar-thumb:hover {
            background: #d66a2a;
        }
        
        /* ============================================
           BOOKING MODAL - FRESH MOBILE-FIRST DESIGN
           ============================================ */
        
        .booking-modal {
            display: none;
            position: fixed;
            z-index: 10000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            background-color: rgba(0, 0, 0, 0.8);
            animation: fadeIn 0.25s ease;
        }
        
        .booking-modal-content {
            background-color: #ffffff;
            width: 100%;
            height: 100%;
            max-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            overflow-x: hidden;
            position: relative;
        }
        
        .modal-header {
            padding: 20px;
            background: #ffffff;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            z-index: 100;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        
        .modal-header h2 {
            font-size: 20px;
            font-weight: 700;
            margin: 0;
            color: #1a1a1a;
        }
        
        .close-modal {
            width: 40px;
            height: 40px;
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #666666;
            border-radius: 8px;
            transition: all 0.2s ease;
            background: transparent;
        }
        
        .close-modal:active {
            background: #f0f0f0;
        }
        
        .modal-content-wrapper {
            flex: 1;
            overflow: visible;
            -webkit-overflow-scrolling: touch;
            background: #f5f5f5;
            padding-bottom: 20px;
        }
        
        /* Booking Summary Bar - Top */
        .modal-booking-summary {
            background: linear-gradient(135deg, #e77a3a 0%, #d66a2a 100%);
            padding: 16px 20px;
            color: #ffffff;
            position: relative;
            z-index: 90;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .booking-summary-compact {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }
        
        .booking-summary-compact .summary-total {
            font-size: 24px;
            font-weight: 700;
            color: #ffffff;
            margin: 0;
        }
        
        .booking-summary-compact .summary-dates {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.95);
            text-align: right;
        }
        
        .booking-summary-compact .summary-dates strong {
            color: #ffffff;
            display: block;
            font-size: 14px;
            margin-bottom: 2px;
        }
        
        /* Booking Summary Breakdown */
        .booking-summary-breakdown {
            font-size: 13px;
        }
        
        .breakdown-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            color: rgba(255, 255, 255, 0.95);
        }
        
        .breakdown-item:last-child {
            margin-bottom: 0;
        }
        
        .breakdown-label {
            flex: 1;
        }
        
        .breakdown-value {
            font-weight: 600;
            color: #ffffff;
        }
        
        .breakdown-subtotal {
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            font-weight: 600;
        }
        
        .breakdown-total {
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid rgba(255, 255, 255, 0.3);
            font-size: 16px;
            font-weight: 700;
        }
        
        .breakdown-total .breakdown-value {
            font-size: 18px;
        }
        
        /* Form Section - Clean Card Design */
        .modal-form-section {
            width: 100%;
            padding: 20px;
        }
        
        .form-section {
            margin-bottom: 20px;
            background: #ffffff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }
        
        .form-section-title {
            font-size: 17px;
            margin-bottom: 18px;
            font-weight: 700;
            color: #1a1a1a;
            padding-bottom: 12px;
            border-bottom: 3px solid #e77a3a;
        }
        
        .form-group {
            margin-bottom: 18px;
        }
        
        .form-group:last-child {
            margin-bottom: 0;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 14px;
            color: #333333;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 14px 16px;
            font-size: 16px;
            min-height: 50px;
            border-radius: 10px;
            border: 1.5px solid #d0d0d0;
            box-sizing: border-box;
            transition: all 0.2s ease;
            background: #ffffff;
            color: #1a1a1a;
            font-family: inherit;
        }
        
        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: #999999;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #e77a3a;
            box-shadow: 0 0 0 3px rgba(231, 122, 58, 0.15);
        }
        
        .form-row {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        
        .form-group textarea {
            min-height: 90px;
            resize: vertical;
            line-height: 1.5;
        }
        
        .label-hint {
            display: block;
            margin-top: 6px;
            font-size: 12px;
            color: #666666;
            font-weight: 400;
            line-height: 1.4;
        }
        
        /* Inline Error Messages */
        .error-message {
            display: none;
            color: #e74c3c;
            font-size: 12px;
            margin-top: 6px;
            font-weight: 500;
            line-height: 1.4;
            min-height: 18px;
        }
        
        .error-message:not(:empty) {
            display: block;
        }
        
        .form-group input.error,
        .form-group select.error,
        .form-group textarea.error {
            border-color: #e74c3c;
            box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.15);
        }
        
        .form-group input.error:focus,
        .form-group select.error:focus,
        .form-group textarea.error:focus {
            border-color: #e74c3c;
            box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.25);
        }
        
        /* Password Input with Toggle */
        .password-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }
        
        .password-input-wrapper input {
            padding-right: 50px;
        }
        
        .password-toggle {
            position: absolute;
            right: 16px;
            cursor: pointer;
            color: #666666;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            transition: color 0.2s ease;
            z-index: 10;
        }
        
        .password-toggle:hover {
            color: #e77a3a;
        }
        
        .password-toggle.active i.fa-eye {
            display: none;
        }
        
        .password-toggle.active i.fa-eye-slash {
            display: inline-block;
        }
        
        .password-toggle i.fa-eye-slash {
            display: none;
        }
        
        /* Password Strength Indicator */
        .password-strength-indicator {
            margin-top: 8px;
        }
        
        .strength-bar {
            width: 100%;
            height: 4px;
            background-color: #e0e0e0;
            border-radius: 2px;
            overflow: hidden;
            margin-bottom: 6px;
        }
        
        .strength-fill {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }
        
        .strength-fill.weak {
            width: 33%;
            background-color: #e74c3c;
        }
        
        .strength-fill.medium {
            width: 66%;
            background-color: #f39c12;
        }
        
        .strength-fill.strong {
            width: 100%;
            background-color: #27ae60;
        }
        
        .strength-text {
            font-size: 12px;
            font-weight: 600;
            display: block;
        }
        
        .strength-text.weak {
            color: #e74c3c;
        }
        
        .strength-text.medium {
            color: #f39c12;
        }
        
        .strength-text.strong {
            color: #27ae60;
        }
        
        /* Phone Input */
        .phone-input-wrapper {
            display: flex;
            gap: 10px;
            flex-direction: column;
        }
        
        .country-code-select {
            padding: 14px 16px;
            font-size: 16px;
            min-height: 50px;
            border-radius: 10px;
            border: 1.5px solid #d0d0d0;
            background: #ffffff;
            color: #1a1a1a;
            cursor: pointer;
            transition: all 0.2s ease;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23e77a3a' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            padding-right: 40px;
        }
        
        .country-code-select:focus {
            outline: none;
            border-color: #e77a3a;
            box-shadow: 0 0 0 3px rgba(231, 122, 58, 0.15);
        }
        
        .phone-number-input {
            flex: 1;
        }
        
        /* Country Search */
        .country-search-wrapper {
            position: relative;
        }
        
        .country-search-input {
            width: 100%;
            padding: 14px 16px;
            font-size: 16px;
            min-height: 50px;
            border-radius: 10px;
            border: 1.5px solid #d0d0d0;
            background: #ffffff;
            color: #1a1a1a;
            transition: all 0.2s ease;
        }
        
        .country-search-input:focus {
            outline: none;
            border-color: #e77a3a;
            box-shadow: 0 0 0 3px rgba(231, 122, 58, 0.15);
        }
        
        .country-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #ffffff;
            border: 1.5px solid #e77a3a;
            border-top: none;
            border-radius: 0 0 10px 10px;
            max-height: 250px;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            display: none;
        }
        
        .country-dropdown-item {
            padding: 14px 16px;
            cursor: pointer;
            transition: all 0.2s ease;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .country-dropdown-item:hover,
        .country-dropdown-item:active {
            background: #f8f9fa;
            color: #e77a3a;
        }
        
        .country-dropdown-item:last-child {
            border-bottom: none;
        }
        
        /* Arrival Time Section */
        .arrival-time-section {
            background: #f8f9fa;
            padding: 16px;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
        }
        
        .arrival-time-info {
            margin-bottom: 16px;
        }
        
        .arrival-time-info h4 {
            color: #1a1a1a;
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .arrival-time-info p {
            color: #666666;
            font-size: 13px;
            line-height: 1.5;
            margin-bottom: 6px;
        }
        
        .info-icon {
            color: #e77a3a;
            margin-right: 6px;
        }
        
        /* Checkbox */
        .form-group input[type="checkbox"] {
            width: 20px;
            height: 20px;
            min-height: 20px;
            margin-right: 10px;
            cursor: pointer;
            accent-color: #e77a3a;
        }
        
        /* Modal Footer */
        .modal-footer {
            padding: 20px;
            background: #ffffff;
            border-top: 1px solid #e0e0e0;
            display: flex;
            flex-direction: column;
            gap: 12px;
            position: relative;
            z-index: 100;
            box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.05);
        }
        
        .btn-cancel,
        .btn-book {
            width: 100%;
            padding: 16px 20px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            min-height: 52px;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-cancel {
            background: #f5f5f5;
            color: #666666;
            order: 1;
        }
        
        .btn-cancel:active {
            background: #e8e8e8;
        }
        
        .btn-book {
            background: #e77a3a;
            color: #ffffff;
            order: 2;
        }
        
        .btn-book:active {
            background: #d66a2a;
        }
        
        .modal-room-details {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e0e0e0;
        }
        
        .modal-room-detail-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            font-size: 13px;
            color: #555;
        }
        
        .modal-room-detail-item i {
            color: #e77a3a;
            width: 20px;
            margin-right: 10px;
        }
        
        /* Remove old summary styles - using compact version */
        
        /* ============================================
           TABLET ENHANCEMENTS (768px+)
           Progressive enhancement for larger screens
           ============================================ */
        @media only screen and (min-width: 768px) {
            .available-rooms-section {
                padding: 30px 0 60px 0;
            }
            
            .available-rooms-section .section-heading h2 {
                font-size: 28px;
            }
            
            .breadcumb-area {
                padding: 50px 0;
                min-height: 180px;
            }
            
            .bradcumbContent h2 {
                font-size: 32px;
            }
            
            .book-now-area {
                padding: 30px 0;
            }
            
            .book-now-form {
                padding: 30px 25px;
            }
            
            .booking-modal {
                padding: 20px;
                align-items: center;
            }
            
            .booking-modal-content {
                width: 90%;
                max-width: 700px;
                margin: 2% auto;
                border-radius: 16px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
                max-height: 95vh;
            }
            
            .modal-header {
                padding: 24px;
                border-radius: 16px 16px 0 0;
            }
            
            .modal-header h2 {
                font-size: 22px;
            }
            
            .modal-booking-summary {
                padding: 20px 24px;
            }
            
            .modal-form-section {
                padding: 24px;
            }
            
            .form-section {
                padding: 24px;
            }
            
            .form-section-title {
                font-size: 18px;
            }
            
            .form-row {
                flex-direction: row;
                gap: 16px;
            }
            
            .form-row .form-group {
                flex: 1;
            }
            
            .phone-input-wrapper {
                flex-direction: row;
            }
            
            .country-code-select {
                width: 150px;
                flex-shrink: 0;
            }
            
            .modal-footer {
                flex-direction: row;
                justify-content: flex-end;
                gap: 12px;
                padding: 24px;
                border-radius: 0 0 16px 16px;
            }
            
            .btn-cancel,
            .btn-book {
                width: auto;
                min-width: 150px;
            }
        }
        
        /* ============================================
           DESKTOP ENHANCEMENTS (992px+)
           ============================================ */
        @media only screen and (min-width: 992px) {
            .available-rooms-section {
                padding: 50px 0;
            }
            
            .book-now-form {
                padding: 40px 35px;
            }
            
            .booking-modal-content {
                max-width: 700px;
            }
        }
        
        @media only screen and (max-width: 480px) {
            .modal-header {
                padding: 15px 18px;
            }
            
            .modal-header h2 {
                font-size: 20px;
            }
            
            .close-modal {
                width: 40px;
                height: 40px;
                font-size: 24px;
            }
            
            .modal-room-main-image {
                height: 200px;
            }
            
            .modal-room-thumbnail {
                width: 60px;
                height: 60px;
                min-width: 60px;
                min-height: 60px;
            }
            
            .modal-room-info,
            .modal-form-section,
            .modal-booking-summary {
                padding: 18px;
            }
            
            .modal-room-info h3 {
                font-size: 18px;
            }
            
            .form-section-title {
                font-size: 16px;
            }
            
            .form-group input,
            .form-group select,
            .form-group textarea {
                padding: 14px 16px;
                font-size: 16px; /* Prevents zoom on iOS */
                min-height: 48px;
            }
            
            .phone-input-wrapper {
                flex-direction: column;
                gap: 12px;
            }
            
            .country-code-select {
                width: 100%;
                margin-bottom: 0;
            }
            
            .phone-number-input {
                width: 100%;
            }
            
            .arrival-time-section {
                padding: 18px;
            }
            
            .arrival-time-info h4 {
                font-size: 16px;
            }
            
            .arrival-time-info p {
                font-size: 14px;
            }
            
            .booking-summary-box {
                padding: 18px;
            }
            
            .modal-footer {
                padding: 15px 18px;
            }
            
            .btn-book,
            .btn-cancel {
                padding: 15px;
                font-size: 15px;
                min-height: 50px;
            }
        }
        
        /* Touch-friendly improvements */
        @media (hover: none) and (pointer: coarse) {
            .modal-room-thumbnail {
                min-width: 60px;
                min-height: 60px;
            }
            
            .btn-book,
            .btn-cancel {
                min-height: 48px; /* Minimum touch target size */
            }
            
            .close-modal {
                min-width: 44px;
                min-height: 44px;
            }
            
            .form-group input,
            .form-group select,
            .form-group textarea {
                min-height: 44px; /* Minimum touch target size */
            }
        }
        
        /* ============================================
           MOBILE OPTIMIZATIONS (max-width: 767px)
           Additional refinements for mobile devices
           ============================================ */
        @media only screen and (max-width: 767px) {
            /* Container adjustments */
            .container {
                padding-left: 15px;
                padding-right: 15px;
            }
            
            /* Breadcrumb Mobile */
            .breadcumb-area {
                padding: 30px 0;
                min-height: 120px;
            }
            
            .bradcumbContent h2 {
                font-size: 20px;
            }
            
            /* Available Rooms Section Mobile */
            .available-rooms-section {
                padding: 20px 0 40px 0;
            }
            
            .available-rooms-section .container {
                padding-left: 10px;
                padding-right: 10px;
            }
            
            .available-rooms-section .section-heading h2 {
                font-size: 22px;
            }
            
            /* Room Cards Mobile */
            .col-12.col-sm-6.col-md-6.col-lg-4,
            .col-12.col-md-6.col-lg-3 {
                margin-bottom: 30px;
                padding-left: 5px !important;
                padding-right: 5px !important;
            }
            
            /* Currency Tabs Mobile */
            .currency-tabs {
                padding: 4px;
                margin-bottom: 20px;
            }
            
            .currency-tab {
                padding: 10px 16px;
                font-size: 14px;
                min-height: 44px;
            }
            
            /* No Rooms Message Mobile */
            .no-rooms {
                padding: 40px 15px;
            }
            
            .no-rooms-icon {
                font-size: 48px;
            }
            
            .no-rooms h3 {
                font-size: 20px;
            }
            
            .no-rooms p {
                font-size: 14px;
            }
            
            /* Alert Messages Mobile */
            .alert {
                padding: 12px 15px;
                font-size: 14px;
                margin-bottom: 15px;
                border-radius: 8px;
            }
            
            /* Airport Pickup Section Mobile */
            #airportPickupFields {
                margin-top: 15px;
            }
            
            #airportPickupFields .form-row {
                flex-direction: column;
                gap: 0;
            }
            
            /* Terms checkbox mobile */
            .form-section .form-group label {
                font-size: 14px;
                line-height: 1.5;
            }
            
            /* Booking summary mobile */
            .booking-summary-box {
                padding: 18px;
            }
            
            .summary-item {
                font-size: 14px;
                margin-bottom: 10px;
            }
            
            .summary-total {
                font-size: 18px;
                padding-top: 12px;
            }
        }
        
        /* Prevent Horizontal Scroll */
        body {
            overflow-x: hidden;
        }
        
        img {
            max-width: 100%;
            height: auto;
        }
        
        /* Prevent text size adjustment on iOS */
        @media screen and (max-width: 480px) {
            input[type="text"],
            input[type="email"],
            input[type="tel"],
            input[type="password"],
            input[type="date"],
            select,
            textarea {
                font-size: 16px !important;
            }
        }
        
        /* Smooth scrolling for mobile */
        .modal-room-info,
        .modal-form-section,
        .modal-booking-summary {
            -webkit-overflow-scrolling: touch;
            scroll-behavior: smooth;
        }
        
        /* Better mobile modal backdrop - Using main CSS breakpoint */
        @media only screen and (max-width: 767px) {
            .booking-modal {
                padding: 0;
            }
            
            .booking-modal-content {
                border-radius: 0;
            }
        }
        
        /* Improve form visibility on mobile - Using main CSS breakpoint */
        @media only screen and (max-width: 767px) {
            .form-section {
                margin-bottom: 25px;
            }
            
            .arrival-time-section {
                background: #f8f9fa;
                border-radius: 8px;
                margin-top: 15px;
            }
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideDown {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        .modal-header {
            background: linear-gradient(135deg, #e77a3a 0%, #e07632 100%);
            color: white;
            padding: 30px 40px;
            border-radius: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .modal-header h2 {
            margin: 0;
            font-size: 26px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        
        .close-modal {
            color: white;
            font-size: 32px;
            font-weight: 300;
            cursor: pointer;
            background: rgba(255,255,255,0.15);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            line-height: 1;
        }
        
        .close-modal:hover {
            background: rgba(255,255,255,0.25);
            transform: rotate(90deg);
        }
        
        .modal-body {
            padding: 0;
            background: #ffffff;
        }
        
        .form-section {
            margin-bottom: 35px;
        }
        
        .form-section-title {
            color: #e77a3a;
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 3px solid #e77a3a;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .form-group {
            margin-bottom: 22px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #2c3e50;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .form-group .label-hint {
            font-size: 11px;
            color: #7f8c8d;
            font-weight: 400;
            margin-top: 4px;
            text-transform: none;
            letter-spacing: 0;
        }
        
        .form-row {
            display: flex;
            gap: 20px;
        }
        
        .form-row .form-group {
            flex: 1;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 14px 18px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 15px;
            transition: all 0.3s ease;
            font-family: inherit;
            background-color: #ffffff;
            color: #2c3e50;
        }
        
        .form-group input:hover,
        .form-group select:hover {
            border-color: #e77a3a;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #e77a3a;
            box-shadow: 0 0 0 3px rgba(231, 122, 58, 0.08);
            background-color: #fff;
        }
        
        .form-group input::placeholder {
            color: #95a5a6;
        }
        
        /* Phone input with country code */
        .phone-input-wrapper {
            display: flex;
            gap: 12px;
        }
        
        .country-code-select {
            flex: 0 0 140px;
        }
        
        .phone-number-input {
            flex: 1;
        }
        
        /* Inline Searchable Country Input */
        .country-search-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }
        
        .country-flag-display {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 20px;
            z-index: 10;
            pointer-events: none;
            line-height: 1;
        }
        
        .country-search-input {
            width: 100%;
            padding: 14px 18px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 15px;
            transition: all 0.3s ease;
            background-color: #ffffff;
            color: #2c3e50;
        }
        
        .country-search-input.has-flag {
            padding-left: 50px;
        }
        
        .country-search-input:focus {
            outline: none;
            border-color: #e77a3a;
            box-shadow: 0 0 0 3px rgba(231, 122, 58, 0.08);
        }
        
        .country-dropdown {
            position: absolute;
            top: calc(100% + 5px);
            left: 0;
            right: 0;
            background: #ffffff;
            border: 1px solid #e77a3a;
            border-radius: 6px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            max-height: 300px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }
        
        .country-dropdown.show {
            display: block;
        }
        
        .country-option {
            padding: 12px 18px;
            cursor: pointer;
            transition: background-color 0.2s;
            border-bottom: 1px solid #f5f5f5;
            display: flex;
            align-items: center;
            font-size: 14px;
            color: #2c3e50;
        }
        
        .country-option:last-child {
            border-bottom: none;
        }
        
        .country-option:hover,
        .country-option.highlighted {
            background-color: rgba(231, 122, 58, 0.08);
        }
        
        .country-option.selected {
            background-color: rgba(231, 122, 58, 0.12);
            font-weight: 600;
            color: #e77a3a;
        }
        
        .country-option-no-results {
            padding: 20px;
            text-align: center;
            color: #95a5a6;
            font-size: 14px;
        }
        
        /* Arrival time section */
        .arrival-time-section {
            background: linear-gradient(135deg, #fff5f0 0%, #ffe8d6 100%);
            padding: 25px;
            border-radius: 8px;
            margin-top: 15px;
            border: 2px solid #ffe0cc;
        }
        
        .arrival-time-info {
            margin-bottom: 20px;
        }
        
        .arrival-time-info h4 {
            color: #e77a3a;
            font-size: 17px;
            margin-bottom: 12px;
            font-weight: 700;
        }
        
        .arrival-time-info p {
            color: #555;
            font-size: 14px;
            margin: 8px 0;
            line-height: 1.7;
        }
        
        .arrival-time-info .info-icon {
            color: #e77a3a;
            margin-right: 10px;
            font-size: 16px;
        }
        
        /* Scrollbar for country dropdown */
        .country-dropdown::-webkit-scrollbar {
            width: 8px;
        }
        
        .country-dropdown::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        .country-dropdown::-webkit-scrollbar-thumb {
            background: #e77a3a;
            border-radius: 4px;
        }
        
        .country-dropdown::-webkit-scrollbar-thumb:hover {
            background: #e07632;
        }
        
        .modal-footer {
            padding: 25px 40px;
            background: #f8f9fa;
            border-top: 1px solid #e0e0e0;
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            flex-shrink: 0;
        }
        
        .btn-book {
            background: linear-gradient(135deg, #e77a3a 0%, #e07632 100%);
            color: white;
            border: none;
            padding: 14px 35px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 4px 15px rgba(231, 122, 58, 0.3);
        }
        
        .btn-book:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(231, 122, 58, 0.4);
            background: linear-gradient(135deg, #e07632 0%, #d66a2a 100%);
        }
        
        .btn-book:active {
            transform: translateY(0);
        }
        
        .btn-cancel {
            background: #95a5a6;
            color: white;
            border: none;
            padding: 14px 35px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .btn-cancel:hover {
            background: #7f8c8d;
            transform: translateY(-1px);
        }
        
        /* Remove default spacing above header */
        html, body {
            margin: 0 !important;
            padding: 0 !important;
        }
        
        .header_area {
            margin-top: 0 !important;
            padding-top: 0 !important;
        }
        
        /* Ensure header starts at the very top */
        body > .header_area {
            margin-top: 0 !important;
        }
        
        /* Breadcrumb Area Responsive */
        @media (max-width: 991px) {
            .breadcrumb_area {
                padding: 180px 0px 80px !important;
                min-height: 400px !important;
            }
            
            .page-cover-tittle {
                font-size: 32px !important;
            }
            
            .page-cover p {
                font-size: 15px !important;
            }
        }
        
        @media (max-width: 768px) {
            .breadcrumb_area {
                padding: 150px 0px 60px !important;
                min-height: 350px !important;
            }
            
            .page-cover-tittle {
                font-size: 28px !important;
            }
            
            .page-cover p {
                font-size: 14px !important;
            }
        }
        
        @media (max-width: 480px) {
            .breadcrumb_area {
                padding: 120px 0px 50px !important;
                min-height: 300px !important;
            }
            
            .page-cover-tittle {
                font-size: 24px !important;
            }
            
            .page-cover p {
                font-size: 13px !important;
            }
        }
        
        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }
            
            .phone-input-wrapper {
                flex-direction: column;
            }
            
            .country-code-select {
                flex: 1;
            }
        }
        
        .loading-spinner {
            display: none;
            text-align: center;
            padding: 40px;
        }
        
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .no-rooms {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        
        .no-rooms-icon {
            font-size: 64px;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        /* Compact Book Room Button */
        .single-rooms-area .book-room-btn {
            width: auto;
            min-width: 120px;
            max-width: 80%;
            padding: 10px 20px !important;
            background: linear-gradient(135deg, #e77a3a 0%, #d66a2a 100%);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(231, 122, 58, 0.25);
            margin: 12px auto 0;
            display: block;
            text-align: center;
            text-decoration: none;
        }
        
        .single-rooms-area .book-room-btn:hover {
            background: linear-gradient(135deg, #d66a2a 0%, #c55a1a 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(231, 122, 58, 0.35);
            color: #ffffff;
        }
        
        .single-rooms-area .book-room-btn:active {
            transform: translateY(0);
        }
        
        /* Compact Room Rating Section */
        .room-rating {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .star-rating {
            color: #ffc107;
            font-size: 13px;
        }
        
        .star-rating i {
            margin-right: 1px;
        }
        
        /* Use existing book-room-btn class */
        
        /* Responsive Design - Tablet */
        @media (max-width: 992px) {
            .single-rooms-area {
                min-height: auto !important;
            }
            
            .single-rooms-area .rooms-text {
                padding: 16px !important;
            }
            
            .single-rooms-area .bg-thumbnail {
                height: 200px !important;
            }
            
            .room-details-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
            }
            
            .room-detail-item {
                font-size: 11px;
            }
            
            .room-image-nav {
                width: 38px;
                height: 38px;
                font-size: 17px;
            }
        }
        
        /* Responsive Design - Mobile Tablet - Using main CSS breakpoint */
        @media only screen and (max-width: 767px) {
            .available-rooms-section {
                padding: 30px 0;
            }
            
            .single-rooms-area {
                min-height: 550px !important;
                margin-bottom: 60px !important;
                max-width: 100% !important;
                width: 100% !important;
            }
            
            /* Ensure cards don't overflow on mobile but use full width */
            .col-12.col-sm-6.col-md-6.col-lg-4 {
                max-width: 100% !important;
                width: 100% !important;
                padding-left: 5px !important;
                padding-right: 5px !important;
            }
            
            .row {
                margin-left: 0 !important;
                margin-right: 0 !important;
            }
            
            .room-image-container {
                height: 280px !important;
            }
            
            .single-rooms-area .bg-thumbnail {
                height: 280px !important;
            }
            
            .room-image-nav {
                width: 36px;
                height: 36px;
                font-size: 16px;
                opacity: 0.85;
            }
            
            .room-image-nav.prev {
                left: 10px;
            }
            
            .room-image-nav.next {
                right: 10px;
            }
            
            .room-image-count {
                top: 10px;
                right: 10px;
                padding: 5px 10px;
                font-size: 10px;
            }
            
            .room-image-indicators {
                bottom: 10px;
                padding: 5px 10px;
                gap: 5px;
            }
            
            .room-image-indicator {
                width: 6px;
                height: 6px;
            }
            
            .room-image-indicator.active {
                width: 20px;
            }
            
            .single-rooms-area .price-from {
                font-size: 18px !important;
                padding: 12px 20px !important;
                top: -15px !important;
            }
            
            .price-total-label {
                font-size: 12px !important;
                margin-top: 4px;
            }
            
            .room-price-per-night {
                font-size: 12px !important;
            }
            
            .single-rooms-area .rooms-text {
                padding: 20px 20px 55px !important;
                left: 15px !important;
                right: 15px !important;
                bottom: 15px !important;
                width: calc(100% - 30px) !important;
                max-height: 80% !important;
            }
            
            .single-rooms-area .rooms-text h4 {
                font-size: 24px !important;
                margin-bottom: 10px;
            }
            
            .single-rooms-area .rooms-text p {
                font-size: 15px !important;
                margin-bottom: 15px;
                line-height: 1.6;
            }
            
            .room-details-grid {
                grid-template-columns: 1fr;
                gap: 10px;
                margin-bottom: 12px;
            }
            
            .room-detail-item {
                font-size: 14px;
                gap: 8px;
            }
            
            .room-detail-item i {
                font-size: 16px;
            }
            
            .room-badges {
                gap: 8px;
                margin-top: 12px;
            }
            
            .room-badge {
                padding: 6px 12px;
                font-size: 12px;
            }
            
            .room-amenities-list {
                margin-top: 12px;
                padding-top: 12px;
            }
            
            .room-amenities-list h6 {
                font-size: 14px;
                margin-bottom: 10px;
            }
            
            .room-amenities-grid {
                gap: 6px;
            }
            
            .room-amenity-badge {
                font-size: 12px;
                padding: 6px 10px;
            }
            
            /* Remove custom button overrides - use original home page styling */
        }
        
        /* Responsive Design - Mobile - Using main CSS breakpoint */
        @media only screen and (max-width: 767px) {
            
            .available-rooms-section {
                padding: 25px 0;
            }
            
            /* Restore original card dimensions from home page */
            .single-rooms-area {
                height: 515px;
                margin-bottom: 100px;
                position: relative;
                z-index: 10;
                width: 100%;
            }
            
            /* Ensure cards don't overflow on mobile but use full width */
            .col-12.col-sm-6.col-md-6.col-lg-4 {
                max-width: 100% !important;
                width: 100% !important;
                padding-left: 5px !important;
                padding-right: 5px !important;
            }
            
            .row {
                margin-left: 0 !important;
                margin-right: 0 !important;
            }
            
            .room-image-container {
                height: 280px !important;
            }
            
            .single-rooms-area .bg-thumbnail {
                height: 280px !important;
            }
            
            .room-image-nav {
                width: 40px;
                height: 40px;
                font-size: 18px;
                opacity: 0.9;
            }
            
            .room-image-nav.prev {
                left: 10px;
            }
            
            .room-image-nav.next {
                right: 10px;
            }
            
            .room-image-count {
                top: 10px;
                right: 10px;
                padding: 6px 12px;
                font-size: 11px;
            }
            
            .room-image-indicators {
                bottom: 8px;
                padding: 4px 8px;
                gap: 4px;
            }
            
            .room-image-indicator {
                width: 5px;
                height: 5px;
            }
            
            .room-image-indicator.active {
                width: 18px;
            }
            
            .single-rooms-area .price-from {
                font-size: 16px !important;
                padding: 12px 16px !important;
                top: 15px !important;
                right: 15px !important;
                line-height: 1.2;
            }
            
            .price-amount {
                font-size: 20px;
            }
            
            .price-total-label {
                font-size: 11px !important;
                margin-top: 3px;
                display: block;
            }
            
            .room-price-per-night {
                font-size: 11px !important;
                margin-top: 3px;
                display: block;
            }
            
            /* Restore original rooms-text styling - let main CSS handle it */
            .single-rooms-area .rooms-text h4 {
                font-size: 20px;
            }
            
            .single-rooms-area .rooms-text p {
                font-size: 14px !important;
                margin-bottom: 12px;
                line-height: 1.5;
            }
            
            /* Fix Book Room button on mobile - center and resize */
            .single-rooms-area .book-room-btn {
                position: absolute;
                bottom: -15px;
                left: 50%;
                transform: translateX(-50%);
                -webkit-transform: translateX(-50%);
                z-index: 50;
                font-size: 14px;
                padding: 10px 20px;
                min-width: auto;
                width: auto;
                max-width: 90%;
                white-space: nowrap;
            }
            
            .room-room-number {
                font-size: 13px;
                margin-bottom: 8px;
            }
            
            .room-details-section {
                margin-top: 12px;
                padding-top: 12px;
            }
            
            .room-details-grid {
                grid-template-columns: 1fr;
                gap: 8px;
                margin-bottom: 10px;
            }
            
            .room-detail-item {
                font-size: 13px;
                gap: 6px;
            }
            
            .room-detail-item i {
                font-size: 15px;
                width: 16px;
            }
            
            .room-detail-item span {
                font-size: 13px;
            }
            
            .room-badges {
                gap: 6px;
                margin-top: 10px;
            }
            
            .room-badge {
                padding: 5px 10px;
                font-size: 11px;
            }
            
            .room-amenities-list {
                margin-top: 10px;
                padding-top: 10px;
            }
            
            .room-amenities-list h6 {
                font-size: 12px;
                margin-bottom: 8px;
            }
            
            .room-amenities-grid {
                gap: 5px;
            }
            
            .room-amenity-badge {
                font-size: 11px;
                padding: 5px 9px;
            }
            
            /* Fix Book Room button on mobile - ensure centered and properly sized */
            .single-rooms-area .book-room-btn {
                position: absolute !important;
                bottom: -15px !important;
                left: 50% !important;
                transform: translateX(-50%) !important;
                -webkit-transform: translateX(-50%) !important;
                z-index: 50 !important;
                font-size: 14px !important;
                padding: 10px 20px !important;
                min-width: auto !important;
                width: auto !important;
                max-width: 90% !important;
                margin: 0 !important;
                white-space: nowrap !important;
            }
            
            .single-rooms-area .rooms-text .line {
                width: 60px;
                height: 2px;
                margin-bottom: 12px;
            }
            
            /* Increase rating text size on mobile */
            .room-rating {
                font-size: 14px !important;
            }
            
            .room-rating .star-rating {
                font-size: 16px !important;
            }
            
            .room-rating .star-rating i {
                font-size: 16px !important;
            }
        }
        
        /* Extra Small Mobile */
        @media (max-width: 400px) {
            .single-rooms-area {
                min-height: 500px !important;
            }
            
            .room-image-container {
                height: 250px !important;
            }
            
            .single-rooms-area .bg-thumbnail {
                height: 250px !important;
            }
            
            .single-rooms-area .rooms-text {
                padding: 18px 18px 50px !important;
                left: 12px !important;
                right: 12px !important;
                bottom: 12px !important;
                width: calc(100% - 24px) !important;
                max-height: 78% !important;
            }
            
            .single-rooms-area .rooms-text h4 {
                font-size: 20px !important;
            }
            
            .single-rooms-area .rooms-text p {
                font-size: 13px !important;
            }
            
            .room-detail-item {
                font-size: 12px !important;
            }
            
            .room-image-nav {
                width: 36px;
                height: 36px;
                font-size: 16px;
            }
            
            .single-rooms-area .price-from {
                font-size: 14px !important;
                padding: 8px 14px !important;
            }
            
            /* Fix Book Room button on extra small mobile - center and resize */
            .single-rooms-area .book-room-btn {
                position: absolute;
                bottom: -15px;
                left: 50%;
                transform: translateX(-50%);
                -webkit-transform: translateX(-50%);
                z-index: 50;
                font-size: 13px;
                padding: 9px 18px;
                min-width: auto;
                width: auto;
                max-width: 85%;
                white-space: nowrap;
            }
        }
        
        /* Ensure cards stack properly on all screen sizes - Using main CSS breakpoint */
        @media only screen and (max-width: 767px) {
            .col-12.col-sm-6.col-md-6.col-lg-4 {
                margin-bottom: 30px;
                max-width: 100% !important;
                width: 100% !important;
                flex: 0 0 100% !important;
            }
            
            /* Only apply to room cards, not booking form */
            .available-rooms-section .row {
                margin-left: 0;
                margin-right: 0;
            }
            
            .available-rooms-section .row > [class*="col-"] {
                padding-left: 0;
                padding-right: 0;
                max-width: 100% !important;
                box-sizing: border-box !important;
            }
            
            /* Ensure room cards don't exceed container width but use full available space */
            .single-rooms-area {
                max-width: 100% !important;
                width: 100% !important;
                box-sizing: border-box !important;
            }
            
            /* Search form responsive */
            .booking-search-form {
                padding: 20px 15px;
            }
            
            .booking-search-form .form-group {
                margin-bottom: 15px;
            }
            
            .booking-search-form .form-control {
                padding: 10px 12px;
                font-size: 14px;
            }
            
            .booking-search-form button {
                padding: 12px 20px;
                font-size: 14px;
                width: 100%;
            }
            
            /* Room cards full width on mobile - prevent overflow */
            .col-12.col-md-6.col-lg-4,
            .col-12.col-md-6.col-lg-3 {
                flex: 0 0 100% !important;
                max-width: 100% !important;
                width: 100% !important;
                box-sizing: border-box !important;
            }
            
            /* Ensure container uses full width on mobile with minimal padding */
            .available-rooms-section .container {
                max-width: 100% !important;
                padding-left: 10px !important;
                padding-right: 10px !important;
                box-sizing: border-box !important;
            }
            
            /* Give cards more space on mobile */
            .col-12.col-sm-6.col-md-6.col-lg-4,
            .col-12.col-md-6.col-lg-3 {
                padding-left: 5px !important;
                padding-right: 5px !important;
            }
        }
        
        @media only screen and (min-width: 768px) and (max-width: 991px) {
            .col-12.col-sm-6.col-md-6.col-lg-4 {
                margin-bottom: 40px;
            }
            
            .booking-search-form {
                padding: 25px 20px;
            }
        }
        
        @media only screen and (min-width: 992px) {
            .col-12.col-sm-6.col-md-6.col-lg-4 {
                margin-bottom: 30px;
            }
        }
        
        /* Touch-friendly buttons on mobile - Using main CSS breakpoint */
        @media only screen and (max-width: 767px) {
            .room-image-nav {
                min-width: 44px;
                min-height: 44px;
            }
            
            .room-image-indicator {
                min-width: 44px;
                min-height: 44px;
                padding: 8px;
            }
            
            .single-rooms-area .book-room-btn {
                min-height: 52px;
                padding: 16px 28px;
                font-size: 18px;
                font-weight: 700;
                width: calc(100% - 30px);
                margin: 0 15px;
                box-shadow: 0 4px 12px rgba(231, 122, 58, 0.4);
            }
        }
        
        /* Native HTML5 Date Input Styling */
        input[type="date"] {
            position: relative;
            padding: 12px 15px;
            font-size: 16px;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            background-color: white;
            color: #333;
            cursor: pointer;
        }
        
        input[type="date"]:focus {
            border-color: #e77a3a;
            outline: none;
            box-shadow: 0 0 0 0.2rem rgba(231, 122, 58, 0.25);
        }
        
        input[type="date"]::-webkit-calendar-picker-indicator {
            cursor: pointer;
            opacity: 0;
            position: absolute;
            width: 100%;
            height: 100%;
            left: 0;
        }
        
        /* Style the calendar icon */
        .input-group-addon {
            pointer-events: none;
        }
        
        input[type="date"]::-webkit-inner-spin-button,
        input[type="date"]::-webkit-clear-button {
            display: none;
        }
        /* Advanced Flatpickr Date Picker Styling */
        .flatpickr-calendar {
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            border: 1px solid #e0e0e0;
            font-family: inherit;
            z-index: 99999 !important;
            position: absolute !important;
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        .flatpickr-calendar.open {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        .flatpickr-calendar.animate.open {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        .flatpickr-months {
            background: linear-gradient(135deg, #e77a3a 0%, #d66a2a 100%) !important;
            border-radius: 10px 10px 0 0;
            padding: 10px 0;
            position: relative;
            z-index: 1;
        }

        .flatpickr-month {
            color: white !important;
            background: transparent !important;
        }

        .flatpickr-prev-month,
        .flatpickr-next-month {
            color: white !important;
            fill: white !important;
            background: transparent !important;
        }

        .flatpickr-prev-month:hover,
        .flatpickr-next-month:hover {
            background: rgba(255, 255, 255, 0.2) !important;
            border-radius: 5px;
        }

        .flatpickr-current-month {
            color: white !important;
            font-weight: 600;
            background: transparent !important;
        }

        .flatpickr-current-month .flatpickr-monthDropdown-months {
            background: transparent !important;
            color: white !important;
            font-weight: 600;
        }

        .flatpickr-current-month .flatpickr-monthDropdown-months option {
            background: #e77a3a !important;
            color: white !important;
        }

        .flatpickr-current-month input.cur-year,
        .flatpickr-current-month .flatpickr-monthDropdown-months {
            color: white !important;
            background: transparent !important;
            font-weight: 600 !important;
        }

        .flatpickr-current-month input.cur-year {
            color: white !important;
            background: transparent !important;
        }

        .flatpickr-monthDropdown-months {
            background: transparent !important;
            color: white !important;
            border: none !important;
        }

        .flatpickr-weekdays {
            background: #f8f9fa;
            padding: 10px 0;
        }

        .flatpickr-weekday {
            color: #000000;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
        }

        .flatpickr-day {
            border-radius: 5px;
            transition: all 0.2s ease;
            color: #000000;
        }

        .flatpickr-day:hover {
            background: #e77a3a;
            color: white;
            border-color: #e77a3a;
        }

        .flatpickr-day.selected,
        .flatpickr-day.startRange,
        .flatpickr-day.endRange {
            background: #e77a3a;
            border-color: #e77a3a;
            color: white;
            font-weight: 600;
        }

        .flatpickr-day.today {
            border-color: #e77a3a;
            color: #000000;
            font-weight: 600;
        }

        .flatpickr-day.disabled,
        .flatpickr-day.flatpickr-disabled {
            color: #ccc;
            cursor: not-allowed;
            background: #f5f5f5;
        }

        .flatpickr-day.disabled:hover {
            background: #f5f5f5;
            color: #ccc;
        }

        #check_in,
        #check_out {
            background-color: white;
            cursor: pointer !important;
            pointer-events: auto !important;
            user-select: none;
        }
        
        #check_in:focus,
        #check_out:focus {
            outline: none !important;
            border-color: #e77a3a !important;
            box-shadow: 0 0 0 0.2rem rgba(231, 122, 58, 0.25) !important;
            color: #000000 !important;
        }

        #check_in[readonly],
        #check_out[readonly] {
            color: #000000;
        }

        .flatpickr-time,
        .flatpickr-time-wrapper,
        .flatpickr-am-pm {
            display: none !important;
        }
        
        /* INLINE CSS SOLUTION - Force placeholder visibility with inline styles via JavaScript */
        /* All styling is now inline on the elements themselves */
        
        /* CRITICAL FIX: External CSS targets .book_tabel_item .form-group .input-group .form-control */
        /* But we don't have form-group, so we need to match the select structure */
        /* Select works because it becomes .nice-select div, inputs need .form-control class */
        .book_tabel_item .input-group .form-control,
        .book_tabel_item .input-group input.form-control,
        .book_tabel_item .input-group input.wide,
        .book_tabel_item .input-group .wide {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            width: 100% !important;
            box-sizing: border-box !important;
            background: #ffffff !important;
            border: 1px solid #2b3146 !important;
            border-radius: 0px !important;
            font-size: 13px !important;
            line-height: 40px !important;
            height: 40px !important;
            color: #777777 !important;
            padding: 0px 20px !important;
            z-index: 0 !important;
        }
        
        /* Force visibility on mobile - override any external CSS that might hide inputs */
        @media screen and (max-width: 767px),
               screen and (max-device-width: 767px),
               (max-width: 767px),
               (max-device-width: 767px) {
            .book_tabel_item .input-group .form-control,
            .book_tabel_item .input-group input.form-control,
            .book_tabel_item .input-group input.wide,
            #check_in,
            #check_out {
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
                width: 100% !important;
                min-width: 0 !important;
                max-width: none !important;
                flex: 1 1 0% !important;
            }
            
            .book_tabel_item .input-group {
                display: flex !important;
                visibility: visible !important;
                width: 100% !important;
            }
        }
        
        /* CRITICAL: Placeholder styles - Override external CSS that expects .form-group */
        /* Apply to ALL possible selectors for maximum compatibility */
        .book_tabel_item .input-group input.wide::placeholder,
        .book_tabel_item .input-group .wide::placeholder,
        input.wide::placeholder,
        #check_in::placeholder,
        #check_out::placeholder {
            color: #777777 !important;
            opacity: 1 !important;
        }
        
        .book_tabel_item .input-group input.wide::-webkit-input-placeholder,
        .book_tabel_item .input-group .wide::-webkit-input-placeholder,
        input.wide::-webkit-input-placeholder,
        #check_in::-webkit-input-placeholder,
        #check_out::-webkit-input-placeholder {
            color: #777777 !important;
            opacity: 1 !important;
        }
        
        .book_tabel_item .input-group input.wide::-moz-placeholder,
        .book_tabel_item .input-group .wide::-moz-placeholder,
        input.wide::-moz-placeholder,
        #check_in::-moz-placeholder,
        #check_out::-moz-placeholder {
            color: #777777 !important;
            opacity: 1 !important;
        }
        
        .book_tabel_item .input-group input.wide:-ms-input-placeholder,
        .book_tabel_item .input-group .wide:-ms-input-placeholder,
        input.wide:-ms-input-placeholder,
        #check_in:-ms-input-placeholder,
        #check_out:-ms-input-placeholder {
            color: #777777 !important;
            opacity: 1 !important;
        }
        
        .book_tabel_item .input-group input.wide:-moz-placeholder,
        .book_tabel_item .input-group .wide:-moz-placeholder,
        input.wide:-moz-placeholder,
        #check_in:-moz-placeholder,
        #check_out:-moz-placeholder {
            color: #777777 !important;
            opacity: 1 !important;
        }
        
        /* Mobile specific - ensure visibility and placeholder */
        @media screen and (max-width: 767px) {
            .book_tabel_item .input-group input.wide {
                font-size: 16px !important;
                padding: 12px 20px !important;
                min-height: 48px !important;
                height: auto !important;
                line-height: normal !important;
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
                color: #777777 !important;
            }
            
            /* Label placeholder overlay for mobile - ALWAYS SHOW when input is empty */
            .date-placeholder-label {
                font-size: 16px !important;
                left: 20px !important;
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
            }
            
            /* On mobile, show label if input is empty or has no value */
            #check_in:empty + .date-placeholder-label,
            #check_in[value=""] + .date-placeholder-label,
            #check_in:not([value]) + .date-placeholder-label,
            #check_out:empty + .date-placeholder-label,
            #check_out[value=""] + .date-placeholder-label,
            #check_out:not([value]) + .date-placeholder-label {
                display: block !important;
            }
            
            /* Mobile placeholder - ensure it's visible */
            .book_tabel_item .input-group input.wide::placeholder,
            #check_in::placeholder,
            #check_out::placeholder {
                color: #777777 !important;
                opacity: 1 !important;
                font-size: 16px !important;
            }
            
            .book_tabel_item .input-group input.wide::-webkit-input-placeholder,
            #check_in::-webkit-input-placeholder,
            #check_out::-webkit-input-placeholder {
                color: #777777 !important;
                opacity: 1 !important;
                font-size: 16px !important;
            }
            
            .book_tabel_item .input-group input.wide::-moz-placeholder,
            #check_in::-moz-placeholder,
            #check_out::-moz-placeholder {
                color: #777777 !important;
                opacity: 1 !important;
                font-size: 16px !important;
            }
            
            .book_tabel_item .input-group input.wide:-ms-input-placeholder,
            #check_in:-ms-input-placeholder,
            #check_out:-ms-input-placeholder {
                color: #777777 !important;
                opacity: 1 !important;
                font-size: 16px !important;
            }
            
            /* Ensure the column and items are visible */
            .hotel_booking_table .row > .col-12.col-sm-6.col-md-4:first-child {
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
                width: 100% !important;
                max-width: 100% !important;
                flex: 0 0 100% !important;
            }
            
            .hotel_booking_table .row > .col-12.col-sm-6.col-md-4:first-child .book_tabel_item {
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
                width: 100% !important;
                margin-bottom: 10px !important;
            }
        }
        
        /* CRITICAL: Force booking form date fields to be visible on ALL devices - Base rules */
        #check_in,
        #check_out {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            width: auto !important;
            flex: 1 1 0% !important;
            min-width: 0 !important;
        }
        
        .book_tabel_item {
            display: block !important;
            visibility: visible !important;
        }
        
        /* CRITICAL: Force first column to always be visible on ALL devices */
        .hotel_booking_table .row > .col-12.col-sm-6.col-md-4:first-child,
        .hotel_booking_table .boking_table .row > .col-12.col-sm-6.col-md-4:first-child,
        .hotel_booking_table form .row > .col-12.col-sm-6.col-md-4:first-child {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        /* CRITICAL: Force date inputs in first column to always be visible */
        .hotel_booking_table .row > .col-12.col-sm-6.col-md-4:first-child #check_in,
        .hotel_booking_table .row > .col-12.col-sm-6.col-md-4:first-child #check_out,
        .hotel_booking_table .boking_table .row > .col-12.col-sm-6.col-md-4:first-child #check_in,
        .hotel_booking_table .boking_table .row > .col-12.col-sm-6.col-md-4:first-child #check_out,
        .hotel_booking_table form .row > .col-12.col-sm-6.col-md-4:first-child #check_in,
        .hotel_booking_table form .row > .col-12.col-sm-6.col-md-4:first-child #check_out {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        /* CRITICAL: Ensure the container div with date fields is visible on mobile */
        .hotel_booking_table .row > .col-12.col-sm-6.col-md-4:first-child,
        .hotel_booking_table .boking_table .row > .col-12.col-sm-6.col-md-4:first-child,
        .hotel_booking_table form .row > .col-12.col-sm-6.col-md-4:first-child {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            width: 100% !important;
            max-width: 100% !important;
            flex: 0 0 100% !important;
        }
        
        /* CRITICAL: Mobile override - ensure date fields container is never hidden */
        /* Use multiple media query formats for better device compatibility */
        @media screen and (max-width: 767px),
               (max-width: 767px),
               (max-device-width: 767px),
               screen and (max-device-width: 767px) {
            /* Ensure all parent containers are visible */
            .hotel_booking_area,
            .hotel_booking_table,
            .hotel_booking_table .col-md-9,
            .hotel_booking_table .boking_table,
            .hotel_booking_table form,
            .hotel_booking_table form .row {
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
                height: auto !important;
                min-height: auto !important;
                overflow: visible !important;
            }
            
            .hotel_booking_table form .row {
                display: flex !important;
                flex-wrap: wrap !important;
            }
            
            .hotel_booking_table .boking_table form .row .col-12.col-sm-6.col-md-4:first-child,
            .hotel_booking_table form .row .col-12.col-sm-6.col-md-4:first-child {
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
                height: auto !important;
                min-height: auto !important;
                overflow: visible !important;
                width: 100% !important;
                max-width: 100% !important;
                flex: 0 0 100% !important;
                position: relative !important;
                z-index: 1 !important;
            }
            
            .hotel_booking_table .boking_table form .row .col-12.col-sm-6.col-md-4:first-child .book_tabel_item,
            .hotel_booking_table form .row .col-12.col-sm-6.col-md-4:first-child .book_tabel_item {
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
                height: auto !important;
                width: 100% !important;
            }
            
            /* Ensure input groups are visible - NO form-group anymore */
            .hotel_booking_table form .row .col-12.col-sm-6.col-md-4:first-child .book_tabel_item .input-group {
                display: flex !important;
                visibility: visible !important;
                opacity: 1 !important;
                width: 100% !important;
                height: auto !important;
                min-height: 48px !important;
                margin-bottom: 15px !important;
            }
            
            /* Force date inputs to be visible */
            .hotel_booking_table form .row .col-12.col-sm-6.col-md-4:first-child #check_in,
            .hotel_booking_table form .row .col-12.col-sm-6.col-md-4:first-child #check_out {
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
                width: auto !important;
                flex: 1 1 0% !important;
                min-width: 0 !important;
                height: auto !important;
                min-height: 48px !important;
            }
        }
        
        /* Mobile-specific styles for booking form arrival and departure date fields */
        /* Use multiple media query formats for better device compatibility */
        @media screen and (max-width: 767px),
               (max-width: 767px),
               (max-device-width: 767px),
               screen and (max-device-width: 767px) {
            /* CRITICAL: Force first column to display - override ALL Bootstrap classes */
            .hotel_booking_table .row .col-12.col-sm-6.col-md-4:first-child,
            .hotel_booking_table .boking_table .row .col-12.col-sm-6.col-md-4:first-child,
            .hotel_booking_table form .row .col-12.col-sm-6.col-md-4:first-child {
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
                width: 100% !important;
                max-width: 100% !important;
                flex: 0 0 100% !important;
                float: none !important;
                position: relative !important;
                height: auto !important;
                min-height: auto !important;
                margin: 0 !important;
                padding: 0 7.5px 15px 7.5px !important;
            }
            
            /* Ensure container doesn't cause overflow */
            .hotel_booking_area .container {
                padding-left: 15px;
                padding-right: 15px;
                max-width: 100%;
                overflow-x: hidden;
            }
            
            /* Ensure booking form section is properly styled */
            .hotel_booking_area {
                padding: 30px 0;
                overflow-x: hidden;
            }
            
            .hotel_booking_table {
                padding: 20px 15px;
                background: rgba(255, 255, 255, 0.95);
                border-radius: 10px;
                box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
                width: 100%;
                max-width: 100%;
                box-sizing: border-box;
                display: block !important;
            }
            
            /* Title section */
            .hotel_booking_table .col-md-3 {
                width: 100% !important;
                flex: 0 0 100% !important;
                max-width: 100% !important;
                text-align: center;
                margin-bottom: 20px;
                display: block !important;
            }
            
            .hotel_booking_table .col-md-3 h2 {
                font-size: 22px;
                margin-bottom: 0;
            }
            
            /* Form container */
            .hotel_booking_table .col-md-9 {
                width: 100% !important;
                flex: 0 0 100% !important;
                max-width: 100% !important;
                display: block !important;
            }
            
            /* Ensure row works properly with Bootstrap */
            .hotel_booking_table .row {
                margin-left: -7.5px;
                margin-right: -7.5px;
                display: flex !important;
                flex-wrap: wrap !important;
            }
            
            /* Date fields container - work with Bootstrap grid - CRITICAL: Ensure visibility */
            .hotel_booking_table .row > .col-12,
            .hotel_booking_table .row > .col-12.col-sm-6,
            .hotel_booking_table .row > .col-12.col-sm-6.col-md-4,
            .hotel_booking_table .row > .col-12.col-sm-12 {
                padding-left: 7.5px !important;
                padding-right: 7.5px !important;
                margin-bottom: 15px !important;
                width: 100% !important;
                flex: 0 0 100% !important;
                max-width: 100% !important;
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
            }
            
            /* Ensure book_tabel_item is properly styled */
            .book_tabel_item {
                margin-bottom: 0;
                display: block !important;
                visibility: visible !important;
                width: 100% !important;
            }
            
            /* Input groups - match room type select behavior exactly */
            .book_tabel_item .input-group {
                width: 100% !important;
                max-width: 100% !important;
                display: flex !important;
                flex-wrap: nowrap !important;
                visibility: visible !important;
                align-items: stretch !important;
                box-sizing: border-box !important;
                margin-bottom: 15px !important;
            }
            
            .book_tabel_item .input-group:last-child {
                margin-bottom: 0 !important;
            }
            
            /* Arrival and departure date inputs - Match room type select exactly */
            #check_in,
            #check_out {
                flex: 1 1 0% !important;
                min-width: 0 !important;
                width: auto !important;
                max-width: none !important;
                box-sizing: border-box !important;
                padding: 12px 15px !important;
                font-size: 16px !important;
                min-height: 48px !important;
                height: auto !important;
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
                border: 1px solid #ced4da !important;
                border-radius: 4px 0 0 4px !important;
                border-right: none !important;
                background-color: white !important;
                color: #000 !important;
            }
            
            /* Ensure placeholder text is visible */
            #check_in::placeholder,
            #check_out::placeholder {
                color: #6c757d !important;
                opacity: 1 !important;
            }
            
            #check_in::-webkit-input-placeholder,
            #check_out::-webkit-input-placeholder {
                color: #6c757d !important;
                opacity: 1 !important;
            }
            
            #check_in::-moz-placeholder,
            #check_out::-moz-placeholder {
                color: #6c757d !important;
                opacity: 1 !important;
            }
            
            /* Input group addon (calendar icon) */
            .book_tabel_item .input-group-addon {
                padding: 12px 15px !important;
                flex: 0 0 auto !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                min-width: 45px !important;
                background-color: #f8f9fa !important;
                border: 1px solid #ced4da !important;
                border-left: none !important;
                border-radius: 0 4px 4px 0 !important;
                visibility: visible !important;
                opacity: 1 !important;
                height: auto !important;
            }
            
            /* Ensure input group has proper border radius on left side */
            .book_tabel_item .input-group .form-control:first-child {
                border-radius: 4px 0 0 4px !important;
                border-right: none !important;
            }
            
            /* Ensure form-controls inside input-group work properly - don't use 100% width in flex container */
            .book_tabel_item .input-group .form-control {
                flex: 1 1 0% !important;
                min-width: 0 !important;
                width: auto !important; /* Let flex control the width */
                max-width: none !important; /* Remove max-width constraint in flex */
            }
            
            /* Room type select - this works, so date inputs should match this pattern */
            .book_tabel_item select.wide {
                width: 100% !important;
                padding: 12px 15px;
                font-size: 16px;
                min-height: 48px;
                display: block !important;
                visibility: visible !important;
            }
            
            /* CRITICAL: Force first column book_tabel_item to display */
            .hotel_booking_table .row > .col-12.col-sm-6.col-md-4:first-child .book_tabel_item,
            .hotel_booking_table .boking_table .row > .col-12.col-sm-6.col-md-4:first-child .book_tabel_item,
            .hotel_booking_table form .row > .col-12.col-sm-6.col-md-4:first-child .book_tabel_item {
                display: block !important;
                visibility: visible !important;
                width: 100% !important;
                max-width: 100% !important;
                height: auto !important;
                min-height: auto !important;
                opacity: 1 !important;
            }
            
            /* CRITICAL: Force input-groups in first column to display */
            .hotel_booking_table .row > .col-12.col-sm-6.col-md-4:first-child .book_tabel_item .input-group,
            .hotel_booking_table .boking_table .row > .col-12.col-sm-6.col-md-4:first-child .book_tabel_item .input-group,
            .hotel_booking_table form .row > .col-12.col-sm-6.col-md-4:first-child .book_tabel_item .input-group {
                width: 100% !important;
                max-width: 100% !important;
                display: flex !important;
                visibility: visible !important;
                opacity: 1 !important;
                margin-bottom: 15px !important;
                height: auto !important;
                min-height: 48px !important;
            }
            
            /* CRITICAL: Force date inputs themselves to display */
            .hotel_booking_table .row > .col-12.col-sm-6.col-md-4:first-child #check_in,
            .hotel_booking_table .row > .col-12.col-sm-6.col-md-4:first-child #check_out,
            .hotel_booking_table .boking_table .row > .col-12.col-sm-6.col-md-4:first-child #check_in,
            .hotel_booking_table .boking_table .row > .col-12.col-sm-6.col-md-4:first-child #check_out,
            .hotel_booking_table form .row > .col-12.col-sm-6.col-md-4:first-child #check_in,
            .hotel_booking_table form .row > .col-12.col-sm-6.col-md-4:first-child #check_out {
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
                width: auto !important;
                flex: 1 1 0% !important;
                min-width: 0 !important;
                height: auto !important;
                min-height: 48px !important;
                background-color: #ffffff !important;
                border: 2px solid #ced4da !important;
                color: #000000 !important;
                font-size: 16px !important;
            }
            
            /* Submit button */
            .book_now_btn {
                width: 100% !important;
                padding: 12px 20px;
                font-size: 16px;
                min-height: 48px;
                display: block !important;
                visibility: visible !important;
            }
        }
        
        /* Extra small mobile devices */
        @media screen and (max-width: 480px),
               (max-width: 480px),
               (max-device-width: 480px),
               screen and (max-device-width: 480px) {
            .hotel_booking_table {
                padding: 15px 10px;
            }
            
            .hotel_booking_table .col-md-3 h2 {
                font-size: 20px;
            }
            
            #check_in,
            #check_out,
            .book_tabel_item select.wide {
                font-size: 16px;
                padding: 12px 14px;
            }
            
            .book_tabel_item .input-group-addon {
                padding: 12px 14px;
                min-width: 40px;
            }
        }
        
        /* Date Input Placeholder Overlay - Enhanced Implementation */
        .date-wrapper {
            position: relative;
            width: 100%;
            display: block;
        }
        
        .date-wrapper .date-input {
            position: relative;
            width: 100% !important;
            padding: 12px 15px 12px 15px !important;
            font-size: 14px !important;
            height: auto !important;
            min-height: 40px !important;
            border: 1px solid #2b3146 !important;
            border-radius: 0px !important;
            box-sizing: border-box !important;
            background-color: #ffffff !important;
            color: #fff0;
            display: block !important;
            line-height: 1.5 !important;
            transition: all 0.3s ease !important;
            cursor: pointer !important;
        }
        
        .date-wrapper .date-input:hover {
            border-color: #e77a3a !important;
            background-color: #fffaf7 !important;
        }
        
        .date-wrapper .date-input:focus,
        .date-wrapper .date-input:valid {
            color: #000000 !important;
            border-color: #e77a3a !important;
            box-shadow: 0 0 0 0.2rem rgba(231, 122, 58, 0.25) !important;
            outline: none !important;
            background-color: #ffffff !important;
        }
        
        .date-wrapper .date-input::-webkit-datetime-edit {
            color: transparent;
            transition: color 0.3s ease;
        }
        
        .date-wrapper .date-input:not(:valid)::-webkit-datetime-edit {
            color: transparent !important;
        }
        
        .date-wrapper .date-input:focus::-webkit-datetime-edit,
        .date-wrapper .date-input:valid::-webkit-datetime-edit {
            color: #000000 !important;
        }
        
        .date-wrapper .date-input::-webkit-calendar-picker-indicator {
            cursor: pointer;
            opacity: 1;
            padding: 4px;
            margin-left: 8px;
            transition: transform 0.2s ease;
        }
        
        .date-wrapper .date-input:hover::-webkit-calendar-picker-indicator,
        .date-wrapper .date-input:focus::-webkit-calendar-picker-indicator {
            transform: scale(1.1);
        }
        
        .date-wrapper .placeholder-text {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            pointer-events: none;
            font-size: 14px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1;
            font-weight: 400;
            letter-spacing: 0.3px;
        }
        
        .date-wrapper .date-input:focus + .placeholder-text,
        .date-wrapper .date-input:valid + .placeholder-text {
            opacity: 0;
            transform: translateY(-50%) scale(0.95);
        }
        
        .date-wrapper:hover .placeholder-text {
            color: #e77a3a;
        }
        
        .book_tabel_item .date-wrapper {
            margin-bottom: 15px;
        }
        
        .date-wrapper * {
            transition: all 0.3s ease;
        }
        
        .date-wrapper .date-input:active {
            transform: scale(0.998);
        }
        
        .date-wrapper .date-input:disabled {
            background-color: #f5f5f5 !important;
            border-color: #ddd !important;
            cursor: not-allowed !important;
            opacity: 0.6;
        }
        
        .date-wrapper .date-input:disabled + .placeholder-text {
            color: #999 !important;
            opacity: 0.6;
        }
        
        .date-wrapper .date-input:focus-visible {
            outline: 2px solid #e77a3a !important;
            outline-offset: 2px !important;
        }
        
        .date-wrapper .date-input::-webkit-datetime-edit-text {
            color: transparent;
            padding: 0 2px;
        }
        
        .date-wrapper .date-input:not(:valid)::-webkit-datetime-edit-text {
            color: transparent !important;
        }
        
        .date-wrapper .date-input::-webkit-datetime-edit-month-field,
        .date-wrapper .date-input::-webkit-datetime-edit-day-field,
        .date-wrapper .date-input::-webkit-datetime-edit-year-field {
            color: transparent;
            font-weight: 500;
        }
        
        .date-wrapper .date-input:not(:valid)::-webkit-datetime-edit-month-field,
        .date-wrapper .date-input:not(:valid)::-webkit-datetime-edit-day-field,
        .date-wrapper .date-input:not(:valid)::-webkit-datetime-edit-year-field {
            color: transparent !important;
        }
        
        .date-wrapper .date-input:valid::-webkit-datetime-edit-text {
            color: #000000;
        }
        
        .date-wrapper .date-input:valid::-webkit-datetime-edit-month-field,
        .date-wrapper .date-input:valid::-webkit-datetime-edit-day-field,
        .date-wrapper .date-input:valid::-webkit-datetime-edit-year-field {
            color: #000000;
        }
        
        .date-wrapper .date-input::-webkit-calendar-picker-indicator:hover {
            background-color: rgba(231, 122, 58, 0.1);
            border-radius: 3px;
        }
        
        .book_tabel_item .date-wrapper:not(:last-child) {
            margin-bottom: 15px;
        }
        
        .date-wrapper .date-input:focus {
            box-shadow: 0 0 0 0.2rem rgba(231, 122, 58, 0.25), 0 2px 4px rgba(0, 0, 0, 0.1) !important;
        }
        
        /* Room Type Select Hover Effects */
        .book_tabel_item select.wide,
        .book_tabel_item select#room_type {
            transition: all 0.3s ease !important;
            cursor: pointer !important;
        }
        
        .book_tabel_item select.wide:hover,
        .book_tabel_item select#room_type:hover {
            border-color: #e77a3a !important;
            background-color: #fffaf7 !important;
            box-shadow: 0 2px 4px rgba(231, 122, 58, 0.15) !important;
        }
        
        .book_tabel_item select.wide:focus,
        .book_tabel_item select#room_type:focus {
            border-color: #e77a3a !important;
            box-shadow: 0 0 0 0.2rem rgba(231, 122, 58, 0.25) !important;
            outline: none !important;
        }
        
        .book_tabel_item .input-group:hover select.wide,
        .book_tabel_item .input-group:hover select#room_type {
            border-color: #e77a3a !important;
            background-color: #fffaf7 !important;
        }
        
        .book_tabel_item .nice-select {
            transition: all 0.3s ease !important;
        }
        
        .book_tabel_item .nice-select:hover {
            border-color: #e77a3a !important;
            background-color: #fffaf7 !important;
            box-shadow: 0 2px 4px rgba(231, 122, 58, 0.15) !important;
        }
        
        .book_tabel_item .nice-select.open {
            border-color: #e77a3a !important;
            box-shadow: 0 0 0 0.2rem rgba(231, 122, 58, 0.25) !important;
        }
        
        .book_tabel_item .nice-select .current {
            transition: color 0.3s ease !important;
        }
        
        .book_tabel_item .nice-select:hover .current {
            color: #e77a3a !important;
        }
        
        .book_tabel_item .nice-select:hover .nice-select-arrow {
            border-color: #e77a3a !important;
        }
        
        .book_tabel_item .nice-select .list .option:hover,
        .book_tabel_item .nice-select .list .option.focus {
            background-color: #e77a3a !important;
            color: #ffffff !important;
        }
        
        .book_tabel_item .nice-select .list .option.selected {
            background-color: rgba(231, 122, 58, 0.1) !important;
            color: #e77a3a !important;
            font-weight: 600 !important;
        }
        
        .book_tabel_item .nice-select .list .option.selected:hover {
            background-color: #e77a3a !important;
            color: #ffffff !important;
        }
        
        /* Prevent horizontal scrolling on mobile */
        @media (max-width: 767px) {
            html, body {
                overflow-x: hidden !important;
            }
            
            .hotel_booking_table .row {
                margin-left: 0 !important;
                margin-right: 0 !important;
            }
            
            .hotel_booking_table .row > div {
                padding-left: 5px !important;
                padding-right: 5px !important;
            }
            
            .book_tabel_item input,
            .book_tabel_item select,
            .book_tabel_item .form-control,
            .date-wrapper .date-input {
                max-width: 100% !important;
                box-sizing: border-box !important;
            }
            
            .date-wrapper .date-input {
                font-size: 16px !important;
            }
            
            .date-wrapper .placeholder-text {
                font-size: 16px !important;
                left: 15px !important;
            }
            
            .date-wrapper .date-input {
                min-height: 44px !important;
                padding: 14px 15px !important;
            }
        }
    </style>
</head>

    <body>
        @include('landing_page_views.partials.royal-header')
        
        <!--================Breadcrumb Area =================-->
        <section class="breadcrumb_area" style="padding: 250px 0px 120px; min-height: 500px; position: relative;">
            <div class="overlay bg-parallax" data-stellar-ratio="0.8" data-stellar-vertical-offset="0" data-background="" style="background-image: url('{{ asset('hotel_gallery/room_.jpg') }}'); background-size: cover; background-position: center center; opacity: 1 !important;"></div>
            <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.25); z-index: 0;"></div>
            <div class="container" style="position: relative; z-index: 1;">
                <div class="page-cover text-center">
                    <h2 class="page-cover-tittle" style="color: #e77a3a;">Book Your Room</h2>
                    <p style="color: white; font-size: 16px; margin-top: 15px; margin-bottom: 20px;">Reserve your perfect stay at PrimeLand Hotel</p>
                    <ol class="breadcrumb">
                        <li><a href="{{ url('/') }}">Home</a></li>
                        <li class="active">Book Now</li>
                    </ol>
                </div>
            </div>
        </section>
        <!--================Breadcrumb Area =================-->

    <!--================Booking Tabel Area =================-->
    <section class="hotel_booking_area position">
        <div class="container">
            <div class="hotel_booking_table">
                <div class="col-md-3">
                    <h2 style="color: #e77a3a;">Book<br> Your Room</h2>
                </div>
                <div class="col-md-9">
                    <div class="boking_table">
                        <form id="availabilityForm" action="{{ route('booking.index') }}" method="GET">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="book_tabel_item">
                                        <div class="form-group date-wrapper">
                                            <input type="date"
                                                   class="form-control date-input"
                                                   name="check_in"
                                                   id="check_in"
                                                   required
                                                   inputmode="none"
                                                   min="{{ date('Y-m-d') }}"
                                                   value="{{ isset($initial_check_in) ? date('Y-m-d', strtotime($initial_check_in)) : '' }}">
                                            <span class="placeholder-text">Arrival Date</span>
                                            </div>
                                        <div class="form-group date-wrapper">
                                            <input type="date"
                                                   class="form-control date-input"
                                                   name="check_out"
                                                   id="check_out"
                                                   required
                                                   inputmode="none"
                                                   min="{{ date('Y-m-d') }}"
                                                   value="{{ isset($initial_check_out) ? date('Y-m-d', strtotime($initial_check_out)) : '' }}">
                                            <span class="placeholder-text">Departure Date</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="book_tabel_item">
                                        <div class="input-group">
                                            <select class="wide" name="room_type" id="room_type">
                                                <option value="">All Room Types</option>
                                                <option value="Single" {{ isset($initial_room_type) && $initial_room_type == 'Single' ? 'selected' : '' }}>Single Room</option>
                                                <option value="Double" {{ isset($initial_room_type) && $initial_room_type == 'Double' ? 'selected' : '' }}>Double Room</option>
                                                <option value="Twins" {{ isset($initial_room_type) && $initial_room_type == 'Twins' ? 'selected' : '' }}>Standard Twin Room</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="book_tabel_item">
                                        <button type="submit" class="book_now_btn button_hover">Check Availability</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--================Booking Tabel Area  =================-->

    <!-- ##### Available Rooms Area Start ##### -->
    <section class="available-rooms-section section-padding-0-100">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="section-heading text-center mb-50" style="margin-top: 0;">
                        <div class="line-"></div>
                        <h2 style="color: #e77a3a;">AVAILABLE ROOMS</h2>
                    </div>
                </div>
            </div>
            
            <!-- Currency Tabs -->
            <div class="row justify-content-end mb-3" id="currencyToggleContainer" style="display: none;">
                <div class="col-12 col-md-4 col-lg-3">
                    <div class="currency-tabs" style="display: flex; align-items: center; justify-content: flex-end; gap: 0; background: #f8f9fa; padding: 5px; border-radius: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                        <button class="currency-tab active" data-currency="USD" style="background: #e77a3a; color: white; border: none; padding: 8px 20px; border-radius: 20px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; flex: 1;">USD</button>
                        <button class="currency-tab" data-currency="TZS" style="background: transparent; color: #363636; border: none; padding: 8px 20px; border-radius: 20px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; flex: 1;">TZS</button>
                    </div>
                </div>
            </div>
            
            <div id="alertContainer"></div>
            
            <div id="loadingSpinner" class="loading-spinner">
                <div class="spinner"></div>
                <p style="margin-top: 20px; color: #666;">Checking availability...</p>
            </div>
            
            <div id="roomsContainer">
                <div class="no-rooms">
                    <div class="no-rooms-icon">🏨</div>
                    <h3>Search for Available Rooms</h3>
                    <p>Please select your Arrival dates and Departure dates to see available rooms.</p>
                </div>
            </div>
        </div>
    </section>
    <!-- ##### Available Rooms Area End ##### -->

    <!-- ##### Booking Modal ##### -->
    <div id="bookingModal" class="booking-modal">
        <div class="booking-modal-content">
            <div class="modal-header">
                <h2>Complete Your Booking</h2>
                <span class="close-modal">&times;</span>
            </div>
            
            <!-- Booking Summary - Compact (Top) -->
            <div class="modal-booking-summary">
                <div class="booking-summary-compact">
                    <div class="summary-total" id="summaryTotal">$0.00</div>
                    <div class="summary-dates">
                        <strong id="summaryCheckIn">-</strong>
                        <span id="summaryNights">-</span>
                    </div>
                </div>
                <!-- Detailed Booking Summary Breakdown -->
                <div class="booking-summary-breakdown" id="bookingSummaryBreakdown" style="display: none; margin-top: 16px; padding-top: 16px; border-top: 1px solid rgba(255, 255, 255, 0.2);">
                    <div class="breakdown-item">
                        <span class="breakdown-label">Room rate per night:</span>
                        <span class="breakdown-value" id="breakdownPricePerNight">$0.00</span>
                    </div>
                    <div class="breakdown-item">
                        <span class="breakdown-label">Number of nights:</span>
                        <span class="breakdown-value" id="breakdownNights">0</span>
                    </div>
                    <div class="breakdown-item breakdown-subtotal">
                        <span class="breakdown-label">Subtotal:</span>
                        <span class="breakdown-value" id="breakdownSubtotal">$0.00</span>
                    </div>
                    <div class="breakdown-item" id="breakdownTaxes" style="display: none;">
                        <span class="breakdown-label">Taxes & fees:</span>
                        <span class="breakdown-value" id="breakdownTaxesValue">$0.00</span>
                    </div>
                    <div class="breakdown-item breakdown-total">
                        <span class="breakdown-label">Total:</span>
                        <span class="breakdown-value" id="breakdownTotal">$0.00</span>
                    </div>
                </div>
            </div>
            
            <div class="modal-content-wrapper">
                <!-- Booking Form -->
                <div class="modal-form-section">
                <form id="bookingForm">
                    @csrf
                    <input type="hidden" id="booking_room_type" name="room_type">
                    <input type="hidden" id="booking_check_in" name="check_in">
                    <input type="hidden" id="booking_check_out" name="check_out">
                    <input type="hidden" id="number_of_guests" name="number_of_guests" value="1">
                    <input type="hidden" name="booking_for" value="me">
                    
                    <!-- Personal Information Section -->
                    <div class="form-section">
                        <div class="form-section-title">Personal Information</div>
                        
                        <!-- First Name and Last Name -->
                        <div class="form-row">
                            <div class="form-group">
                                <label for="first_name">First name *</label>
                                <input type="text" id="first_name" name="first_name" placeholder="Enter your first name" required oninput="clearFieldError('first_name')">
                                <span class="error-message" id="first_name_error"></span>
                            </div>
                            <div class="form-group">
                                <label for="last_name">Last name *</label>
                                <input type="text" id="last_name" name="last_name" placeholder="Enter your last name" required oninput="clearFieldError('last_name')">
                                <span class="error-message" id="last_name_error"></span>
                            </div>
                        </div>
                        
                            <!-- Email Address -->
                            <div class="form-group">
                                <label for="guest_email">Email address *</label>
                                <input type="email" id="guest_email" name="guest_email" placeholder="your.email@example.com" required oninput="clearFieldError('guest_email')">
                                <span class="error-message" id="guest_email_error"></span>
                                <span class="label-hint">Confirmation email goes to this address (This will be your username)</span>
                            </div>
                            
                            <!-- Account Information Note -->
                            <div class="form-row">
                                <div class="form-group" style="width: 100%;">
                                    <div class="alert alert-info" style="margin: 0; padding: 15px; background-color: #e7f3ff; border: 1px solid #b3d9ff; border-radius: 4px;">
                                        <i class="fa fa-info-circle" style="margin-right: 8px;"></i>
                                        <strong>Account Information:</strong> Your account will be created automatically. You will receive your login credentials (Username: Email, Password: First Name in CAPITALS) via email and SMS after booking confirmation.
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                    <!-- Contact Information Section -->
                    <div class="form-section">
                        <div class="form-section-title">Contact Information</div>
                        
                        <!-- Country/Region - Inline Searchable Input -->
                        <div class="form-group">
                            <label for="country_search">Country/region *</label>
                            <div class="country-search-wrapper">
                                <span id="country_flag_display" class="country-flag-display" style="display: none;"></span>
                                <input type="text" id="country_search" class="country-search-input" placeholder="Search or type country name..." autocomplete="off" required oninput="clearFieldError('country')">
                                <input type="hidden" id="country" name="country" required>
                                <div class="country-dropdown" id="countryDropdown"></div>
                            </div>
                            <span class="error-message" id="country_error"></span>
                        </div>
                        
                        <!-- Phone Number with Country Code -->
                        <div class="form-group">
                            <label for="guest_phone">Phone number with country code *</label>
                            <div class="phone-input-wrapper">
                                <select id="country_code" name="country_code" class="country-code-select" required onchange="clearFieldError('guest_phone')">
                                    <option value="+255">🇹🇿 +255 (TZ)</option>
                                    <option value="+254">🇰🇪 +254 (KE)</option>
                                    <option value="+256">🇺🇬 +256 (UG)</option>
                                    <option value="+250">🇷🇼 +250 (RW)</option>
                                    <option value="+27">🇿🇦 +27 (ZA)</option>
                                    <option value="+1">🇺🇸 +1 (US)</option>
                                    <option value="+44">🇬🇧 +44 (UK)</option>
                                    <option value="+49">🇩🇪 +49 (DE)</option>
                                    <option value="+33">🇫🇷 +33 (FR)</option>
                                    <option value="+39">🇮🇹 +39 (IT)</option>
                                    <option value="+34">🇪🇸 +34 (ES)</option>
                                    <option value="+61">🇦🇺 +61 (AU)</option>
                                    <option value="+91">🇮🇳 +91 (IN)</option>
                                    <option value="+86">🇨🇳 +86 (CN)</option>
                                    <option value="+81">🇯🇵 +81 (JP)</option>
                                    <option value="+93">🇦🇫 +93 (AF)</option>
                                    <option value="+355">🇦🇱 +355 (AL)</option>
                                    <option value="+213">🇩🇿 +213 (DZ)</option>
                                    <option value="+54">🇦🇷 +54 (AR)</option>
                                    <option value="+43">🇦🇹 +43 (AT)</option>
                                    <option value="+880">🇧🇩 +880 (BD)</option>
                                    <option value="+32">🇧🇪 +32 (BE)</option>
                                    <option value="+55">🇧🇷 +55 (BR)</option>
                                    <option value="+45">🇩🇰 +45 (DK)</option>
                                    <option value="+20">🇪🇬 +20 (EG)</option>
                                    <option value="+358">🇫🇮 +358 (FI)</option>
                                    <option value="+233">🇬🇭 +233 (GH)</option>
                                    <option value="+30">🇬🇷 +30 (GR)</option>
                                    <option value="+62">🇮🇩 +62 (ID)</option>
                                    <option value="+353">🇮🇪 +353 (IE)</option>
                                    <option value="+60">🇲🇾 +60 (MY)</option>
                                    <option value="+52">🇲🇽 +52 (MX)</option>
                                    <option value="+212">🇲🇦 +212 (MA)</option>
                                    <option value="+31">🇳🇱 +31 (NL)</option>
                                    <option value="+64">🇳🇿 +64 (NZ)</option>
                                    <option value="+234">🇳🇬 +234 (NG)</option>
                                    <option value="+47">🇳🇴 +47 (NO)</option>
                                    <option value="+92">🇵🇰 +92 (PK)</option>
                                    <option value="+63">🇵🇭 +63 (PH)</option>
                                    <option value="+48">🇵🇱 +48 (PL)</option>
                                    <option value="+351">🇵🇹 +351 (PT)</option>
                                    <option value="+7">🇷🇺 +7 (RU)</option>
                                    <option value="+966">🇸🇦 +966 (SA)</option>
                                    <option value="+65">🇸🇬 +65 (SG)</option>
                                    <option value="+82">🇰🇷 +82 (KR)</option>
                                    <option value="+46">🇸🇪 +46 (SE)</option>
                                    <option value="+41">🇨🇭 +41 (CH)</option>
                                    <option value="+66">🇹🇭 +66 (TH)</option>
                                    <option value="+90">🇹🇷 +90 (TR)</option>
                                    <option value="+380">🇺🇦 +380 (UA)</option>
                                    <option value="+971">🇦🇪 +971 (AE)</option>
                                    <option value="+84">🇻🇳 +84 (VN)</option>
                                    <option value="+260">🇿🇲 +260 (ZM)</option>
                                    <option value="+263">🇿🇼 +263 (ZW)</option>
                                </select>
                                <input type="tel" id="guest_phone" name="guest_phone" class="phone-number-input" placeholder="Phone number" required oninput="clearFieldError('guest_phone')">
                            </div>
                            <span class="error-message" id="guest_phone_error"></span>
                        </div>
                    </div>
                    
                    <!-- Arrival Information Section -->
                    <div class="form-section">
                        <div class="form-section-title">Arrival Information</div>
                        
                        <!-- Your arrival time -->
                        <div class="form-group">
                            <div class="arrival-time-section">
                                <div class="arrival-time-info">
                                    <h4><i class="fa fa-clock-o info-icon"></i> Check-in Information</h4>
                                    <p><strong>Your room will be ready for check-in between 14:00 and 23:00</strong></p>
                                    <p><i class="fa fa-info-circle info-icon"></i> <strong>24-hour front desk</strong> – Help whenever you need it!</p>
                                </div>
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label for="arrival_time">Add your estimated arrival time (optional)</label>
                                    <select id="arrival_time" name="arrival_time">
                                        <option value="">Please select</option>
                                        <option value="14:00">14:00</option>
                                        <option value="15:00">15:00</option>
                                        <option value="16:00">16:00</option>
                                        <option value="17:00">17:00</option>
                                        <option value="18:00">18:00</option>
                                        <option value="19:00">19:00</option>
                                        <option value="20:00">20:00</option>
                                        <option value="21:00">21:00</option>
                                        <option value="22:00">22:00</option>
                                        <option value="23:00">23:00</option>
                                        <option value="After 23:00">After 23:00</option>
                                        <option value="I don't know">I don't know</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Airport Pickup Section (Optional) -->
                    <div class="form-section" id="airportPickupSection">
                        <div class="form-section-title">
                            <label style="margin: 0; cursor: pointer;">
                                <input type="checkbox" id="airport_pickup_required" name="airport_pickup_required" value="1" style="margin-right: 8px;">
                                Airport Pickup Service (Optional)
                            </label>
                        </div>
                        <div id="airportPickupFields" style="display: none; margin-top: 15px;">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="flight_number">Flight Number</label>
                                    <input type="text" id="flight_number" name="flight_number" placeholder="e.g., AA123">
                                </div>
                                <div class="form-group">
                                    <label for="airline">Airline</label>
                                    <input type="text" id="airline" name="airline" placeholder="e.g., American Airlines">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="arrival_time_pickup">Arrival Time</label>
                                    <input type="datetime-local" id="arrival_time_pickup" name="arrival_time_pickup">
                                </div>
                                <div class="form-group">
                                    <label for="pickup_passengers">Number of Passengers</label>
                                    <input type="number" id="pickup_passengers" name="pickup_passengers" min="1" placeholder="1">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="luggage_info">Luggage Information</label>
                                <textarea id="luggage_info" name="luggage_info" rows="2" placeholder="Number of bags, special items, etc."></textarea>
                            </div>
                            <div class="form-group">
                                <label for="pickup_contact_number">Contact Number for Pickup</label>
                                <input type="tel" id="pickup_contact_number" name="pickup_contact_number" placeholder="Phone number for pickup coordination">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Terms and Conditions -->
                    <div class="form-section">
                        <div class="form-group" style="margin-top: 20px; text-align: center;">
                            <label style="display: inline-flex; align-items: center; justify-content: center; cursor: pointer; font-weight: normal; text-align: center;">
                                <input type="checkbox" id="terms_accepted" name="terms_accepted" value="1" required style="margin-right: 10px; flex-shrink: 0;" onchange="clearFieldError('terms_accepted')">
                                <span>I have read and agree to the <a href="#" onclick="event.preventDefault(); openTermsModal();" style="color: #e77a3a; text-decoration: underline;">Terms & Conditions</a> *</span>
                            </label>
                            <span class="error-message" id="terms_accepted_error" style="display: block; text-align: center; margin-top: 8px;"></span>
                        </div>
                    </div>
                    
                    <div id="bookingAlertContainer"></div>
                </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeBookingModal()">Cancel</button>
                <button type="button" class="btn-book" onclick="submitBooking()">Confirm Booking</button>
            </div>
        </div>
    </div>
    <!-- ##### Booking Modal End ##### -->

    <!-- ##### Room Details Modal ##### -->
    <div id="roomDetailsModal" class="booking-modal" style="display: none;">
        <div class="booking-modal-content" style="max-width: 800px;">
            <div class="modal-header">
                <h2 id="roomDetailsTitle">Room Details</h2>
                <span class="close-modal" onclick="closeRoomDetailsModal()">&times;</span>
            </div>
            
            <div class="modal-content-wrapper" style="padding: 20px;">
                <!-- Room Images -->
                <div id="roomDetailsImages" style="margin-bottom: 20px;">
                    <img id="roomDetailsMainImage" src="" alt="Room Image" style="width: 100%; max-height: 400px; object-fit: cover; border-radius: 8px; margin-bottom: 10px;">
                    <div id="roomDetailsThumbnails" style="display: flex; gap: 10px; flex-wrap: wrap;"></div>
                </div>
                
                <!-- Room Description -->
                <div style="margin-bottom: 20px;">
                    <h3 style="color: #e77a3a; margin-bottom: 10px;">Description</h3>
                    <p id="roomDetailsDescription" style="color: #666; line-height: 1.6;"></p>
                </div>
                
                <!-- Room Details Grid -->
                <div style="margin-bottom: 20px;">
                    <h3 style="color: #e77a3a; margin-bottom: 15px;">Room Information</h3>
                    <div id="roomDetailsInfo" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;"></div>
                </div>
                
                <!-- Amenities -->
                <div style="margin-bottom: 20px;">
                    <h3 style="color: #e77a3a; margin-bottom: 15px;">Amenities</h3>
                    <div id="roomDetailsAmenities" style="display: flex; flex-wrap: wrap; gap: 10px;"></div>
                </div>
                
                <!-- Badges -->
                <div id="roomDetailsBadges" style="margin-bottom: 20px;"></div>
                
                <!-- Pricing -->
                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <h3 style="color: #e77a3a; margin-bottom: 10px;">Pricing</h3>
                    <div id="roomDetailsPricing"></div>
                </div>
                
                <!-- Action Buttons -->
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button onclick="closeRoomDetailsModal()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">Close</button>
                    <button id="roomDetailsBookBtn" onclick="bookFromDetails()" style="padding: 10px 20px; background: #e77a3a; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">Book This Room</button>
                </div>
            </div>
        </div>
    </div>
    <!-- ##### Room Details Modal End ##### -->

    <!-- ##### Terms & Conditions Modal ##### -->
    <div id="termsModal" class="terms-modal">
        <div class="terms-modal-content">
            <div class="terms-modal-header">
                <h2>Terms & Conditions</h2>
                <span class="close-terms-modal">&times;</span>
            </div>
            <div class="terms-modal-body">
                <h3>1. Booking and Payment</h3>
                <p><strong>All bookings will be confirmed by receiving 50% deposit before guests check in, remaining balance to be cleared on arrival.</strong></p>
                <p>If payment is not received on time, PrimeLand Hotel reserves the right to collect cash from your guests.</p>
                <ul>
                    <li><strong>Full pre-payment required:</strong> Not later than 30 days prior to guests' arrival.</li>
                    <li><strong>For bookings within 20 days of arrival:</strong> Full pre-payment (i.e., 50% of the total booking amount) will be required within 48 hours of booking time.</li>
                </ul>
                
                <h3>2. Cancellation Policy</h3>
                <p><strong>All cancellations must be received with written confirmation.</strong></p>
                <ul>
                    <li><strong>Free cancellation:</strong> Clients may cancel their booking free of charges 14 days prior to arrival date.</li>
                    <li><strong>50% cancellation fee:</strong> Cancellations made within 13 days to 3 days (72 hours) of guest arrival date are subject to a cancellation fee of 50% of total deposit.</li>
                    <li><strong>100% cancellation fee:</strong> Cancellations made within 2 days (48 hours) prior to guests' arrival date and no show are subject to a 100% of total deposit.</li>
                </ul>
                
                <h3>3. Check-in and Check-out</h3>
                <p>Check-in time is from 16:00 (4:00 PM). Check-out time is before 16:00 (4:00 PM). Early check-in and late check-out may be available upon request and subject to availability.</p>
                
                <h3>4. Guest Responsibilities</h3>
                <p>Guests are responsible for any damage to hotel property. Smoking is prohibited in all rooms. Pets are only allowed in designated pet-friendly rooms.</p>
                
                <h3>5. Hotel Policies</h3>
                <p>The hotel reserves the right to refuse service to anyone. Guests must provide valid identification upon check-in. The hotel is not responsible for personal belongings left in rooms.</p>
                
                <h3>6. Force Majeure</h3>
                <p>The hotel is not liable for any failure to perform due to unforeseen circumstances or causes beyond our reasonable control.</p>
                
                <h3>7. Privacy Policy</h3>
                <p>Your personal information will be used solely for booking purposes and will not be shared with third parties without your consent.</p>
                
                <h3>8. Contact Information</h3>
                <p>For any questions or concerns, please contact us at:</p>
                <p><strong>PrimeLand Hotel</strong><br>
                Sokoine Road, Moshi, Kilimanjaro, Tanzania<br>
                Phone: +255 677-155-156 / +255 677-155-157<br>
                Email: info@primelandhotel.co.tz</p>
            </div>
            <div class="terms-modal-footer">
                <button type="button" class="btn btn-primary" onclick="closeTermsModal()">I Understand</button>
            </div>
        </div>
    </div>
    <!-- ##### Terms & Conditions Modal End ##### -->

        @include('landing_page_views.partials.royal-footer')
        
        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="{{ asset('royal-master/js/jquery-3.2.1.min.js') }}"></script>
        <script src="{{ asset('royal-master/js/popper.js') }}"></script>
        <script src="{{ asset('royal-master/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('royal-master/vendors/owl-carousel/owl.carousel.min.js') }}"></script>
        <script src="{{ asset('royal-master/js/jquery.ajaxchimp.min.js') }}"></script>
        <script src="{{ asset('royal-master/vendors/nice-select/js/jquery.nice-select.js') }}"></script>
        <script src="{{ asset('royal-master/js/stellar.js') }}"></script>
        <script src="{{ asset('royal-master/vendors/lightbox/simpleLightbox.min.js') }}"></script>
        <!-- Bootstrap Datepicker - Original template uses this -->
        <script src="{{ asset('royal-master/js/custom.js') }}"></script>
    
    <script type="text/javascript">
        // Date Input Restrictions and localStorage - Same as landing page
        $(document).ready(function() {
            // Date Input Restrictions - Prevent past dates and enforce check-out after check-in
            var checkInInput = document.getElementById('check_in');
            var checkOutInput = document.getElementById('check_out');
            var roomTypeSelect = document.getElementById('room_type');
            
            if (checkInInput && checkOutInput) {
                // Set minimum date to today for both inputs
                var today = new Date();
                today.setHours(0, 0, 0, 0);
                var todayStr = today.toISOString().split('T')[0];
                
                // Restore dates from localStorage if they exist and are valid
                var savedCheckIn = localStorage.getItem('booking_check_in');
                var savedCheckOut = localStorage.getItem('booking_check_out');
                var savedRoomType = localStorage.getItem('booking_room_type');
                
                // Restore check-in date if saved and valid
                if (savedCheckIn && savedCheckIn >= todayStr) {
                    checkInInput.value = savedCheckIn;
                }
                
                // Restore check-out date if saved and valid
                if (savedCheckOut && savedCheckOut >= todayStr) {
                    // Only restore if check-in is also set and check-out is after check-in
                    if (checkInInput.value) {
                        var checkInDate = new Date(checkInInput.value);
                        var checkOutDate = new Date(savedCheckOut);
                        if (checkOutDate > checkInDate) {
                            checkOutInput.value = savedCheckOut;
                        }
                    } else if (savedCheckOut >= todayStr) {
                        checkOutInput.value = savedCheckOut;
                    }
                }
                
                // Restore room type if saved
                if (savedRoomType && roomTypeSelect) {
                    roomTypeSelect.value = savedRoomType;
                    // Trigger nice-select update if it's being used
                    if (typeof $ !== 'undefined' && $.fn.niceSelect) {
                        $(roomTypeSelect).niceSelect('update');
                    }
                }
                
                // Ensure min attribute is set (in case PHP didn't set it)
                if (!checkInInput.getAttribute('min') || checkInInput.getAttribute('min') < todayStr) {
                    checkInInput.setAttribute('min', todayStr);
                }
                
                // Initially set check-out min to today
                checkOutInput.setAttribute('min', todayStr);
                
                // Function to update check-out minimum based on check-in date
                function updateCheckOutMin() {
                    if (checkInInput.value) {
                        var checkInDate = new Date(checkInInput.value);
                        checkInDate.setDate(checkInDate.getDate() + 1); // Check-out must be at least 1 day after check-in
                        var minCheckOut = checkInDate.toISOString().split('T')[0];
                        checkOutInput.setAttribute('min', minCheckOut);
                        
                        // If current check-out date is invalid, clear it
                        if (checkOutInput.value) {
                            var checkOutDate = new Date(checkOutInput.value);
                            var minDate = new Date(minCheckOut);
                            
                            if (checkOutDate <= checkInDate) {
                                checkOutInput.value = '';
                            }
                        }
            } else {
                        // If no check-in date, set min to today
                        checkOutInput.setAttribute('min', todayStr);
                    }
                }
                
                // When check-in date changes, immediately update check-out minimum
                checkInInput.addEventListener('change', function() {
                    // Save to localStorage
                    if (this.value) {
                        localStorage.setItem('booking_check_in', this.value);
                    } else {
                        localStorage.removeItem('booking_check_in');
                    }
                    
                    updateCheckOutMin();
                    
                    // If check-out has a value, validate it
                    if (checkOutInput.value) {
                        var checkInDate = new Date(this.value);
                        var checkOutDate = new Date(checkOutInput.value);
                        
                        if (checkOutDate <= checkInDate) {
                            checkOutInput.value = '';
                            localStorage.removeItem('booking_check_out');
                            // Show user-friendly message
                setTimeout(function() {
                                alert('Please select a departure date that is after the arrival date.');
                            }, 100);
                        }
                    }
                });
                
                // When check-out date changes, validate it's after check-in
                checkOutInput.addEventListener('change', function() {
                    if (checkInInput.value && this.value) {
                        var checkInDate = new Date(checkInInput.value);
                        var checkOutDate = new Date(this.value);
                        
                        if (checkOutDate <= checkInDate) {
                            alert('Departure date must be after arrival date! Please select a date after ' + checkInInput.value);
                            this.value = '';
                            localStorage.removeItem('booking_check_out');
                            this.focus();
                            return false;
                        } else {
                            // Save to localStorage
                            localStorage.setItem('booking_check_out', this.value);
                        }
                    } else if (this.value && !checkInInput.value) {
                        // If check-out is selected but check-in is not, warn user
                        alert('Please select an arrival date first.');
                        this.value = '';
                        localStorage.removeItem('booking_check_out');
                        this.focus();
                        return false;
                    } else if (this.value) {
                        // Save to localStorage even if check-in is not set yet
                        localStorage.setItem('booking_check_out', this.value);
                    } else {
                        localStorage.removeItem('booking_check_out');
                    }
                });
                
                // Also validate on input event for immediate feedback
                checkOutInput.addEventListener('input', function() {
                    if (checkInInput.value && this.value) {
                        var checkInDate = new Date(checkInInput.value);
                        var checkOutDate = new Date(this.value);
                        
                        if (checkOutDate <= checkInDate) {
                            this.setCustomValidity('Departure date must be after arrival date');
                        } else {
                            this.setCustomValidity('');
                        }
                    }
                });
                
                // Initialize on page load - update check-out min if check-in has a value
                if (checkInInput.value) {
                    updateCheckOutMin();
                }
                
                // Also update after a short delay to ensure all values are restored
                setTimeout(function() {
                    if (checkInInput.value) {
                        updateCheckOutMin();
                    }
                }, 100);
            }
            
            // Save room type selection to localStorage
            if (roomTypeSelect) {
                roomTypeSelect.addEventListener('change', function() {
                    if (this.value) {
                        localStorage.setItem('booking_room_type', this.value);
                    } else {
                        localStorage.removeItem('booking_room_type');
                    }
                });
            }
        }); // End of $(document).ready
        
        // Rest of the JavaScript - wrapped in try-catch to prevent syntax errors from breaking everything
        try {
        // Check availability form submission - wait for form to exist
        function setupFormHandler() {
            var availabilityForm = document.getElementById('availabilityForm');
            if (availabilityForm) {
                availabilityForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Validate dates before checking availability
                    var checkInInput = document.getElementById('check_in');
                    var checkOutInput = document.getElementById('check_out');
                    
                    if (checkInInput && checkOutInput && checkInInput.value && checkOutInput.value) {
                        var checkIn = new Date(checkInInput.value);
                        var checkOut = new Date(checkOutInput.value);
                        
                        if (checkOut <= checkIn) {
                            if (typeof showAlert === 'function') {
                                showAlert('Check-out date must be after check-in date.', 'error');
                            }
                            return false;
                        }
                    }
                    
                    if (typeof checkAvailability === 'function') {
                        checkAvailability();
                    }
                });
            } else {
                setTimeout(setupFormHandler, 100);
            }
        }
        
        // Setup form handler when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setupFormHandler);
        } else {
            setupFormHandler();
        }

        // Auto-trigger search if page loaded with initial dates
        function autoSearchIfNeeded() {
            var checkInInput = document.getElementById('check_in');
            var checkOutInput = document.getElementById('check_out');
            
            if (checkInInput && checkOutInput && checkInInput.value && checkOutInput.value) {
                // Check if dates are valid
                var checkIn = new Date(checkInInput.value);
                var checkOut = new Date(checkOutInput.value);
                
                if (checkOut > checkIn) {
                    // Execute immediately - no delay
                    if (typeof checkAvailability === 'function') {
                        checkAvailability();
                    }
                }
            }
        }

        // Trigger auto-search when DOM is ready - execute immediately
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                autoSearchIfNeeded();
            });
        } else {
            // Execute immediately if DOM is already ready
            autoSearchIfNeeded();
        }

        function checkAvailability() {
            // Get date values from Flatpickr inputs
            var checkIn = document.getElementById('check_in').value;
            var checkOut = document.getElementById('check_out').value;
            
            if (!checkIn || !checkOut) {
                showAlert('Please select both check-in and check-out dates.', 'error');
                return;
            }
            
            if (new Date(checkOut) <= new Date(checkIn)) {
                showAlert('Check-out date must be after check-in date.', 'error');
                return;
            }
            
            // Create form data with proper date format
            const formData = new FormData();
            formData.append('check_in', checkIn);
            formData.append('check_out', checkOut);
            formData.append('room_type', document.getElementById('room_type').value);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            // Show loading spinner and clear container immediately
            var loadingSpinner = document.getElementById('loadingSpinner');
            var roomsContainer = document.getElementById('roomsContainer');
            
            if (loadingSpinner) {
                loadingSpinner.style.display = 'block';
            }
            if (roomsContainer) {
                roomsContainer.innerHTML = '';
            }

            var checkAvailabilityUrl = '{{ route("booking.check-availability") }}';
            if (!checkAvailabilityUrl) {
                console.error('Check availability URL not found');
                if (loadingSpinner) loadingSpinner.style.display = 'none';
                return;
            }
            
            // Use fetch with immediate execution - no artificial delays
            fetch(checkAvailabilityUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(async response => {
                // Try to parse JSON even if response is not ok to get error message
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    const data = await response.json();
                    if (!response.ok) {
                        // Return error data with status
                        return { error: true, status: response.status, ...data };
                    }
                    return data;
                } else {
                    // Not JSON response
                    if (!response.ok) {
                        throw new Error('Server error: ' + response.status + ' ' + response.statusText);
                    }
                    return response.json();
                }
            })
            .then(data => {
                // Hide loading spinner immediately
                if (loadingSpinner) {
                    loadingSpinner.style.display = 'none';
                }
                
                // Check if it's an error response
                if (data.error) {
                    const errorMessage = data.message || data.errors || 'An error occurred while checking availability.';
                    showAlert(typeof errorMessage === 'string' ? errorMessage : Object.values(errorMessage).flat().join(', '), 'error');
                    return;
                }
                
                if (data.success) {
                    // Store exchange rate globally
                    window.exchangeRate = data.exchange_rate || 2500;
                    // Display rooms immediately - no delay
                    displayRooms(data.rooms, data.check_in, data.check_out, data.nights);
                } else {
                    showAlert(data.message || 'An error occurred while checking availability.', 'error');
                }
            })
            .catch(error => {
                if (loadingSpinner) {
                    loadingSpinner.style.display = 'none';
                }
                const errorMessage = error.message || 'An error occurred. Please try again.';
                showAlert(errorMessage, 'error');
                console.error('Error:', error);
            });
        }

        // Store rooms data globally for modal access
        let currentRoomsData = {};
        let currentCheckIn = '';
        let currentCheckOut = '';
        let currentNights = 0;
        
        // Helper function to format room type for display
        function formatRoomTypeName(roomType) {
            if (roomType === 'Twins') {
                return 'Standard Twin Room';
            }
            return roomType + ' Room';
        }

        function displayRooms(rooms, checkIn, checkOut, nights) {
            const container = document.getElementById('roomsContainer');
            if (!container) return;
            
            // Store data globally
            currentCheckIn = checkIn;
            currentCheckOut = checkOut;
            currentNights = nights;
            currentRoomsData = {};
            
            if (rooms.length === 0) {
                container.innerHTML = `
                    <div class="no-rooms">
                        <div class="no-rooms-icon">😔</div>
                        <h3>No Rooms Available</h3>
                        <p>Sorry, there are no available rooms for the selected dates. Please try different dates.</p>
                    </div>
                `;
                // Show currency toggle container even when no rooms
                var currencyToggle = document.getElementById('currencyToggleContainer');
                if (currencyToggle) currencyToggle.style.display = 'none';
                return;
            }

            // Show currency toggle container
            var currencyToggle = document.getElementById('currencyToggleContainer');
            if (currencyToggle) currencyToggle.style.display = 'block';

            // Store room data for modal access - optimized loop
            rooms.forEach(room => {
                // Ensure images is an array
                if (room.images && !Array.isArray(room.images)) {
                    try {
                        room.images = typeof room.images === 'string' ? JSON.parse(room.images) : [];
                    } catch (e) {
                        console.warn('Error parsing room images for ' + room.room_type + ' room type:', e);
                        room.images = [];
                    }
                }
                if (!room.images) {
                    room.images = [];
                }
                // Store by room_type since we're grouping by type (no individual room IDs for guests)
                currentRoomsData[room.room_type] = room;
            });

            let html = '<div class="row">';
            
            rooms.forEach((room, roomIndex) => {
                // Get room images from database
                let roomImages = [];
                var storageBase = '{{ asset("storage") }}';
                var defaultImage = '{{ asset("royal-master/image/rooms/room1.jpg") }}';
                
                if (room.images && Array.isArray(room.images) && room.images.length > 0) {
                    roomImages = room.images.map(img => {
                        // Images are stored as 'rooms/filename.jpg' in storage
                        // Handle different path formats
                        let imgPath = img;
                        if (img.startsWith('rooms/') || img.startsWith('storage/rooms/')) {
                            // Already has correct format
                            imgPath = img.replace(/^storage\//, '');
                        } else if (!img.startsWith('http') && !img.startsWith('/')) {
                            // Add rooms/ prefix if missing
                            imgPath = 'rooms/' + img;
                        }
                        // Use asset helper for storage path
                        return imgPath.startsWith('http') ? imgPath : storageBase + '/' + imgPath;
                    });
                } else {
                    // Fallback to default image
                    roomImages = [defaultImage];
                }
                
                // Debug: Log images for troubleshooting
                console.log('Room Type: ' + room.room_type + ' (Available: ' + (room.available_count || 0) + '):');
                console.log('  - Raw images from API:', room.images);
                console.log('  - Processed images count:', roomImages.length);
                console.log('  - Processed images:', roomImages);
                if (roomImages.length === 1) {
                    console.warn('⚠️ Only 1 image found for ' + room.room_type + ' room type. Check database if you uploaded multiple images.');
                }
                
                const hasMultipleImages = roomImages.length > 1;
                const roomId = 'room-' + roomIndex;
                const firstImage = roomImages[0];
                const fallbackImage = defaultImage;
                
                // Build thumbnail HTML with carousel support (using existing bg-thumbnail class)
                // Make image clickable to open gallery - store images in data attribute for reliability
                const imagesJson = JSON.stringify(roomImages);
                let thumbnailHtml = '<div class="bg-thumbnail bg-img" style="background-image: url(\'' + firstImage + '\'), url(\'' + fallbackImage + '\');" id="bg-thumb-' + roomId + '" data-room-images=\'' + imagesJson.replace(/'/g, '&#39;') + '\' onclick="openRoomGalleryFromElement(this)">';
                
                // Only add slider if multiple images
                if (hasMultipleImages) {
                    thumbnailHtml += '<div class="bg-thumbnail-slider" id="slider-' + roomId + '">';
                    
                    // Add all slides as background images
                    roomImages.forEach((imgUrl, imgIndex) => {
                        const activeClass = imgIndex === 0 ? 'active' : '';
                        thumbnailHtml += '<div class="bg-thumbnail-slide ' + activeClass + '" style="background-image: url(\'' + imgUrl + '\'), url(\'' + fallbackImage + '\');" data-index="' + imgIndex + '"></div>';
                    });
                    
                    thumbnailHtml += '</div>';
                }
                
                // Add navigation buttons if multiple images
                if (hasMultipleImages) {
                    thumbnailHtml += '<button class="room-image-nav prev" onclick="event.stopPropagation(); changeRoomImage(\'' + roomId + '\', -1)" aria-label="Previous image">&#8249;</button>';
                    thumbnailHtml += '<button class="room-image-nav next" onclick="event.stopPropagation(); changeRoomImage(\'' + roomId + '\', 1)" aria-label="Next image">&#8250;</button>';
                    thumbnailHtml += '<div class="room-image-count"><span class="room-image-count-current">1</span>/' + roomImages.length + ' Photos</div>';
                    thumbnailHtml += '<div class="room-image-indicators">';
                    roomImages.forEach((_, idx) => {
                        const activeClass = idx === 0 ? 'active' : '';
                        thumbnailHtml += '<span class="room-image-indicator ' + activeClass + '" onclick="event.stopPropagation(); goToRoomImage(\'' + roomId + '\', ' + idx + ')"></span>';
                    });
                    thumbnailHtml += '</div>';
                }
                
                thumbnailHtml += '</div>';
                
                // Calculate price per night
                const pricePerNight = parseFloat(room.price_per_night || 0);
                const totalPrice = parseFloat(room.calculated_price || 0);
                
                // Get amenities - handle both array and JSON string
                let amenities = [];
                if (room.amenities !== null && room.amenities !== undefined) {
                    if (Array.isArray(room.amenities)) {
                        amenities = room.amenities;
                    } else if (typeof room.amenities === 'string') {
                        try {
                            const parsed = JSON.parse(room.amenities);
                            amenities = Array.isArray(parsed) ? parsed : [];
                        } catch (e) {
                            // If parsing fails, try splitting by comma
                            amenities = room.amenities.split(',').map(a => a.trim()).filter(a => a.length > 0);
                        }
                    } else if (typeof room.amenities === 'object') {
                        // Convert object to array if needed
                        amenities = Object.values(room.amenities);
                    }
                }
                
                // Debug: log amenities to console (remove in production)
                // console.log('Room amenities for room ' + room.id + ':', amenities);
                
                // Build details HTML
                let detailsHtml = '<div class="room-details-section">';
                detailsHtml += '<div class="room-details-grid">';
                
                // Availability Count (instead of room number for guest privacy)
                if (room.available_count !== undefined) {
                    const availabilityText = room.available_count > 1 
                        ? room.available_count + ' Available' 
                        : 'Available';
                    detailsHtml += '<div class="room-detail-item"><i class="fa fa-check-circle"></i><span><strong>Availability:</strong> ' + availabilityText + '</span></div>';
                }
                
                // Capacity/Guests
                if (room.capacity) {
                    detailsHtml += '<div class="room-detail-item"><i class="fa fa-users"></i><span><strong>Guests:</strong> ' + room.capacity + '</span></div>';
                }
                
                // Bed Type
                if (room.bed_type) {
                    detailsHtml += '<div class="room-detail-item"><i class="fa fa-bed"></i><span><strong>Bed:</strong> ' + room.bed_type + '</span></div>';
                }
                
                // Floor Location
                if (room.floor_location) {
                    detailsHtml += '<div class="room-detail-item"><i class="fa fa-layer-group"></i><span><strong>Floor:</strong> ' + room.floor_location + '</span></div>';
                }
                
                // Bathroom Type
                if (room.bathroom_type) {
                    detailsHtml += '<div class="room-detail-item"><i class="fa fa-bath"></i><span><strong>Bathroom:</strong> ' + room.bathroom_type + '</span></div>';
                }
                
                // Check-in/Check-out times
                if (room.checkin_time || room.checkout_time) {
                    const checkinTime = room.checkin_time || 'N/A';
                    const checkoutTime = room.checkout_time || 'N/A';
                    detailsHtml += '<div class="room-detail-item" style="grid-column: 1 / -1;"><i class="fa fa-clock-o"></i><span><strong>Check-in:</strong> ' + checkinTime + ' | <strong>Check-out:</strong> ' + checkoutTime + '</span></div>';
                }
                
                detailsHtml += '</div>';
                
                // Badges (Pet Friendly, Smoking)
                if (room.pet_friendly || room.smoking_allowed) {
                    detailsHtml += '<div class="room-badges">';
                    if (room.pet_friendly) {
                        detailsHtml += '<span class="room-badge pet-friendly"><i class="fa fa-paw"></i> Pet Friendly</span>';
                    }
                    if (room.smoking_allowed) {
                        detailsHtml += '<span class="room-badge smoking"><i class="fa fa-smoking"></i> Smoking Allowed</span>';
                    }
                    detailsHtml += '</div>';
                }
                
                detailsHtml += '</div>';
                
                // Use existing hotel design structure
                const roomDelay = (roomIndex * 100) + 100;
                // Show availability count instead of room number (guest privacy)
                const availabilityText = room.available_count !== undefined 
                    ? (room.available_count > 1 ? room.available_count + ' Available' : 'Available')
                    : 'Available';
                const availabilityBadge = '<div class="room-availability-row">' +
                    '<div class="room-availability-badge">' + availabilityText + '</div>' +
                    '<button class="view-more-btn" onclick="showRoomDetails(\'' + room.room_type + '\', ' + roomIndex + ')">View Details</button>' +
                    '</div>';
                const nightsText = nights > 1 ? 's' : '';
                
                // Amenities - ensure it's an array and has items
                // Filter out empty amenities first
                const validAmenities = amenities.filter(function(a) {
                    if (!a) return false;
                    const text = a.toString().trim();
                    return text.length > 0 && text !== 'null' && text !== 'undefined';
                });
                
                // Build amenities HTML for title area
                let amenitiesHtml = '';
                if (validAmenities.length > 0) {
                    amenitiesHtml += '<div class="room-amenities-inline">';
                    // Show only up to 4 amenities to keep cards compact
                    const maxAmenities = 4;
                    validAmenities.slice(0, maxAmenities).forEach(function(amenity) {
                        const amenityText = amenity.toString().trim();
                        if (amenityText) {
                            // Escape HTML to prevent XSS
                            const escapedText = amenityText.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                            amenitiesHtml += '<span class="room-amenity-badge">' + escapedText + '</span>';
                        }
                    });
                    
                    if (validAmenities.length > maxAmenities) {
                        amenitiesHtml += '<span class="room-amenity-badge">+' + (validAmenities.length - maxAmenities) + ' more</span>';
                    }
                    amenitiesHtml += '</div>';
                }
                
                // Responsive columns - full width on mobile, 2 columns on tablet, 3 on desktop
                html += '<div class="col-12 col-sm-6 col-md-6 col-lg-4">';
                html += '<div class="single-rooms-area wow fadeInUp" data-wow-delay="' + roomDelay + 'ms">';
                html += thumbnailHtml;
                // Calculate prices in both currencies (only per night)
                const exchangeRate = window.exchangeRate || 2500;
                const pricePerNightTZS = pricePerNight * exchangeRate;
                
                // Check for discount (per night only)
                const originalPrice = parseFloat(room.original_price || room.price_per_night || 0);
                const hasDiscount = originalPrice > pricePerNight && originalPrice > 0;
                const discountPerNight = hasDiscount ? (originalPrice - pricePerNight) : 0;
                const discountPerNightTZS = discountPerNight * exchangeRate;
                
                // Display only per-night price
                html += '<p class="price-from" data-usd="' + pricePerNight + '" data-tzs="' + pricePerNightTZS + '">';
                html += '<span class="price-currency-symbol">$</span>';
                html += '<span class="price-amount">' + pricePerNight.toFixed(2) + '</span>';
                html += '<span class="room-price-per-night">/night</span>';
                if (hasDiscount) {
                    html += '<span class="discount-badge" data-usd="' + discountPerNight + '" data-tzs="' + discountPerNightTZS + '" style="display: block; margin-top: 5px; color: #28a745; font-size: 11px; font-weight: 600;">';
                    html += '<i class="fa fa-tag"></i> Save $' + discountPerNight.toFixed(2) + '/night';
                    html += '</span>';
                }
                html += '</p>';
                html += '<div class="rooms-text">';
                html += '<div class="line"></div>';
                html += availabilityBadge;
                // Room title with amenities
                html += '<div class="room-title-wrapper">';
                html += '<h4>' + formatRoomTypeName(room.room_type) + '</h4>';
                html += amenitiesHtml;
                html += '</div>';
                html += detailsHtml;
                
                // Add Customer Reviews/Rating
                const rating = parseFloat(room.rating || (4.5 + Math.random() * 0.5).toFixed(1));
                const reviewCount = parseInt(room.review_count || Math.floor(Math.random() * 50) + 10);
                html += '<div class="room-rating">';
                html += '<div class="star-rating">';
                for (let i = 1; i <= 5; i++) {
                    if (i <= Math.floor(rating)) {
                        html += '<i class="fa fa-star"></i>';
                    } else if (i - 0.5 <= rating) {
                        html += '<i class="fa fa-star-half-o"></i>';
                    } else {
                        html += '<i class="fa fa-star-o"></i>';
                    }
                }
                html += '</div>';
                html += '<span style="font-weight: 700; color: #1a1a1a; font-size: 15px;">' + rating + '</span>';
                html += '<span style="color: #666666; font-size: 13px;">(' + reviewCount + ' reviews)</span>';
                html += '</div>';
                
                html += '</div>';
                html += '<a href="javascript:void(0);" class="book-room-btn btn palatin-btn" onclick="openBookingModal(\'' + room.room_type + '\', \'' + checkIn + '\', \'' + checkOut + '\', ' + nights + ')">Book Room</a>';
                html += '</div>';
                html += '</div>';
            });
            
            html += '</div>';
            container.innerHTML = html;
            
            // Show currency toggle
            document.getElementById('currencyToggleContainer').style.display = 'flex';
            
            // Initialize currency toggle
            initializeCurrencyToggle();
            
            // Initialize auto-slide for rooms with multiple images
            rooms.forEach((room, roomIndex) => {
                if (room.images && Array.isArray(room.images) && room.images.length > 1) {
                    const roomId = 'room-' + roomIndex;
                    startRoomImageAutoSlide(roomId, room.images.length);
                }
            });
        }
        
        // Room image carousel functions
        const roomImageSliders = {};
        
        function changeRoomImage(roomId, direction) {
            const bgThumbnail = document.getElementById('bg-thumb-' + roomId);
            if (!bgThumbnail) return;
            
            const slider = document.getElementById('slider-' + roomId);
            if (!slider) return;
            
            const slides = slider.querySelectorAll('.bg-thumbnail-slide');
            const container = bgThumbnail.closest('.single-rooms-area');
            const indicators = container ? container.querySelectorAll('.room-image-indicator') : [];
            const imageCount = container ? container.querySelector('.room-image-count') : null;
            
            let currentIndex = 0;
            slides.forEach((slide, index) => {
                if (slide.classList.contains('active')) {
                    currentIndex = index;
                }
            });
            
            // Remove active class from current slide
            slides[currentIndex].classList.remove('active');
            if (indicators[currentIndex]) {
                indicators[currentIndex].classList.remove('active');
            }
            
            // Calculate new index
            let newIndex = currentIndex + direction;
            if (newIndex < 0) {
                newIndex = slides.length - 1;
            } else if (newIndex >= slides.length) {
                newIndex = 0;
            }
            
            // Add active class to new slide and update background
            slides[newIndex].classList.add('active');
            const newImageUrl = slides[newIndex].style.backgroundImage.replace(/url\(['"]?([^'"]+)['"]?\)/i, '$1');
            bgThumbnail.style.backgroundImage = 'url(\'' + newImageUrl + '\')';
            
            if (indicators[newIndex]) {
                indicators[newIndex].classList.add('active');
            }
            
            // Update image count display (1/5 format)
            if (imageCount) {
                const countCurrent = imageCount.querySelector('.room-image-count-current');
                if (countCurrent) {
                    countCurrent.textContent = (newIndex + 1);
                }
            }
            
            // Reset auto-slide
            if (roomImageSliders[roomId]) {
                clearInterval(roomImageSliders[roomId]);
                startRoomImageAutoSlide(roomId, slides.length);
            }
        }
        
        function goToRoomImage(roomId, index) {
            const bgThumbnail = document.getElementById('bg-thumb-' + roomId);
            if (!bgThumbnail) return;
            
            const slider = document.getElementById('slider-' + roomId);
            if (!slider) return;
            
            const slides = slider.querySelectorAll('.bg-thumbnail-slide');
            const container = bgThumbnail.closest('.single-rooms-area');
            const indicators = container ? container.querySelectorAll('.room-image-indicator') : [];
            const imageCount = container ? container.querySelector('.room-image-count') : null;
            
            // Remove active from all
            slides.forEach(slide => slide.classList.remove('active'));
            indicators.forEach(indicator => indicator.classList.remove('active'));
            
            // Add active to selected
            slides[index].classList.add('active');
            const newImageUrl = slides[index].style.backgroundImage.replace(/url\(['"]?([^'"]+)['"]?\)/i, '$1');
            bgThumbnail.style.backgroundImage = 'url(\'' + newImageUrl + '\')';
            
            if (indicators[index]) {
                indicators[index].classList.add('active');
            }
            
            // Update image count display (1/5 format)
            if (imageCount) {
                const countCurrent = imageCount.querySelector('.room-image-count-current');
                if (countCurrent) {
                    countCurrent.textContent = (index + 1);
                }
            }
            
            // Reset auto-slide
            if (roomImageSliders[roomId]) {
                clearInterval(roomImageSliders[roomId]);
                startRoomImageAutoSlide(roomId, slides.length);
            }
        }
        
        // Open Room Gallery Modal from element (new improved method)
        function openRoomGalleryFromElement(element) {
            // Get images from data attribute
            const imagesJson = element.getAttribute('data-room-images');
            console.log('Gallery opened - Images JSON from data attribute:', imagesJson);
            
            if (!imagesJson) {
                console.error('❌ No images found in data attribute');
                return;
            }
            
            let imageArray;
            try {
                imageArray = JSON.parse(imagesJson);
                console.log('✅ Parsed images array:', imageArray);
                console.log('✅ Images count:', imageArray.length);
            } catch (e) {
                console.error('❌ Error parsing images JSON:', e);
                return;
            }
            
            if (!Array.isArray(imageArray)) {
                console.error('❌ Images is not an array:', typeof imageArray);
                return;
            }
            
            if (imageArray.length === 0) {
                console.error('❌ Images array is empty');
                return;
            }
            
            console.log('✅ Opening gallery with ' + imageArray.length + ' images');
            openRoomGallery(imageArray);
        }
        
        // Open Room Gallery Modal (legacy support)
        function openRoomGallery(roomIdOrImages, images) {
            // Handle both old and new calling patterns
            let imageArray;
            if (Array.isArray(roomIdOrImages)) {
                // New pattern: openRoomGallery(imageArray)
                imageArray = roomIdOrImages;
            } else if (typeof roomIdOrImages === 'string' && images) {
                // Old pattern: openRoomGallery(roomId, images)
                imageArray = typeof images === 'string' ? JSON.parse(images.replace(/&quot;/g, '"')) : images;
            } else {
                console.error('Invalid arguments to openRoomGallery');
                return;
            }
            
            // Create gallery modal
            const modal = document.createElement('div');
            modal.className = 'room-gallery-modal';
            modal.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.95); z-index: 9999; display: flex; align-items: center; justify-content: center;';
            
            let currentIndex = 0;
            
            const galleryContent = document.createElement('div');
            galleryContent.style.cssText = 'position: relative; max-width: 90%; max-height: 90%; text-align: center;';
            
            const mainImage = document.createElement('img');
            mainImage.src = imageArray[0];
            mainImage.style.cssText = 'max-width: 100%; max-height: 80vh; object-fit: contain; border-radius: 8px;';
            
            const closeBtn = document.createElement('button');
            closeBtn.innerHTML = '&times;';
            closeBtn.style.cssText = 'position: absolute; top: -40px; right: 0; background: #e77a3a; color: white; border: none; width: 40px; height: 40px; border-radius: 50%; font-size: 24px; cursor: pointer;';
            closeBtn.onclick = () => document.body.removeChild(modal);
            
            const prevBtn = document.createElement('button');
            prevBtn.innerHTML = '&#8249;';
            prevBtn.style.cssText = 'position: absolute; left: -60px; top: 50%; transform: translateY(-50%); background: rgba(231,122,58,0.8); color: white; border: none; width: 50px; height: 50px; border-radius: 50%; font-size: 24px; cursor: pointer;';
            prevBtn.onclick = () => {
                currentIndex = (currentIndex - 1 + imageArray.length) % imageArray.length;
                mainImage.src = imageArray[currentIndex];
                updateCounter();
            };
            
            const nextBtn = document.createElement('button');
            nextBtn.innerHTML = '&#8250;';
            nextBtn.style.cssText = 'position: absolute; right: -60px; top: 50%; transform: translateY(-50%); background: rgba(231,122,58,0.8); color: white; border: none; width: 50px; height: 50px; border-radius: 50%; font-size: 24px; cursor: pointer;';
            nextBtn.onclick = () => {
                currentIndex = (currentIndex + 1) % imageArray.length;
                mainImage.src = imageArray[currentIndex];
                updateCounter();
            };
            
            const counter = document.createElement('div');
            counter.style.cssText = 'position: absolute; bottom: -40px; left: 50%; transform: translateX(-50%); color: white; font-size: 16px;';
            const updateCounter = () => {
                counter.textContent = (currentIndex + 1) + ' / ' + imageArray.length;
            };
            updateCounter();
            
            galleryContent.appendChild(mainImage);
            galleryContent.appendChild(closeBtn);
            if (imageArray.length > 1) {
                galleryContent.appendChild(prevBtn);
                galleryContent.appendChild(nextBtn);
            }
            galleryContent.appendChild(counter);
            modal.appendChild(galleryContent);
            
            modal.onclick = (e) => {
                if (e.target === modal) document.body.removeChild(modal);
            };
            
            document.body.appendChild(modal);
        }
        
        // Currency Tabs Functionality
        let currentCurrency = 'USD';
        
        function initializeCurrencyToggle() {
            const currencyTabs = document.querySelectorAll('.currency-tab');
            if (!currencyTabs || currencyTabs.length === 0) return;
            
            currencyTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const selectedCurrency = this.getAttribute('data-currency');
                    
                    // Update active state
                    currencyTabs.forEach(t => {
                        t.classList.remove('active');
                        if (t.getAttribute('data-currency') === selectedCurrency) {
                            t.style.background = '#e77a3a';
                            t.style.color = 'white';
                        } else {
                            t.style.background = 'transparent';
                            t.style.color = '#363636';
                        }
                    });
                    
                    // Update current currency
                    currentCurrency = selectedCurrency;
                    updateAllPrices();
                });
            });
        }
        
        function updateAllPrices() {
            const exchangeRate = window.exchangeRate || 2500;
            const rooms = document.querySelectorAll('.single-rooms-area');
            
            rooms.forEach(room => {
                // Update per-night price (main price display)
                const priceFrom = room.querySelector('.price-from');
                if (priceFrom) {
                    const usdAmount = parseFloat(priceFrom.getAttribute('data-usd') || 0);
                    const tzsAmount = parseFloat(priceFrom.getAttribute('data-tzs') || 0);
                    const amount = currentCurrency === 'USD' ? usdAmount : tzsAmount;
                    const symbol = currentCurrency === 'USD' ? '$' : '';
                    const formatted = currentCurrency === 'USD' 
                        ? amount.toFixed(2) 
                        : Math.round(amount).toLocaleString('en-US');
                    
                    const symbolSpan = priceFrom.querySelector('.price-currency-symbol');
                    const amountSpan = priceFrom.querySelector('.price-amount');
                    if (symbolSpan) symbolSpan.textContent = symbol;
                    if (amountSpan) amountSpan.textContent = formatted;
                }
                
                // Update discount badge
                const discountBadge = room.querySelector('.discount-badge');
                if (discountBadge) {
                    const usdAmount = parseFloat(discountBadge.getAttribute('data-usd') || 0);
                    const tzsAmount = parseFloat(discountBadge.getAttribute('data-tzs') || 0);
                    const amount = currentCurrency === 'USD' ? usdAmount : tzsAmount;
                    const symbol = currentCurrency === 'USD' ? '$' : '';
                    const formatted = currentCurrency === 'USD' 
                        ? amount.toFixed(2) 
                        : Math.round(amount).toLocaleString('en-US');
                    
                    const discountAmount = discountBadge.querySelector('.discount-amount');
                    if (discountAmount) {
                        discountAmount.innerHTML = symbol + formatted;
                    }
                }
            });
        }
        
        function startRoomImageAutoSlide(roomId, imageCount) {
            if (imageCount <= 1) return;
            
            // Clear existing interval
            if (roomImageSliders[roomId]) {
                clearInterval(roomImageSliders[roomId]);
            }
            
            // Start new interval (change image every 5 seconds)
            roomImageSliders[roomId] = setInterval(() => {
                changeRoomImage(roomId, 1);
            }, 5000);
        }
        
        // Pause auto-slide on hover
        document.addEventListener('mouseenter', function(e) {
            if (e.target.closest('.single-rooms-area')) {
                const container = e.target.closest('.single-rooms-area');
                const slider = container.querySelector('.bg-thumbnail-slider');
                if (slider && slider.id) {
                    const roomId = slider.id.replace('slider-room-', 'room-');
                    if (roomImageSliders[roomId]) {
                        clearInterval(roomImageSliders[roomId]);
                    }
                }
            }
        }, true);
        
        // Resume auto-slide on mouse leave
        document.addEventListener('mouseleave', function(e) {
            if (e.target.closest('.single-rooms-area')) {
                const container = e.target.closest('.single-rooms-area');
                const slider = container.querySelector('.bg-thumbnail-slider');
                if (slider && slider.id) {
                    const roomId = slider.id.replace('slider-room-', 'room-');
                    const slides = slider.querySelectorAll('.bg-thumbnail-slide');
                    if (slides.length > 1) {
                        startRoomImageAutoSlide(roomId, slides.length);
                    }
                }
            }
        }, true);

        function openBookingModal(roomType, checkIn, checkOut, nights) {
            // Store room_type instead of room_id (for guest privacy)
            document.getElementById('booking_room_type').value = roomType;
            document.getElementById('booking_check_in').value = checkIn;
            document.getElementById('booking_check_out').value = checkOut;
            
            // Get room data by type
            const room = currentRoomsData[roomType];
            if (room) {
                populateRoomInfo(room, checkIn, checkOut, nights);
                populateBookingSummary(room, checkIn, checkOut, nights);
            }
            
            document.getElementById('bookingModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
            
            // On mobile, scroll to top of modal and ensure smooth experience
            if (window.innerWidth <= 767) {
                const modal = document.getElementById('bookingModal');
                const modalContent = modal.querySelector('.booking-modal-content');
                const modalBody = modal.querySelector('.modal-body');
                
                // Scroll page to top
                window.scrollTo({ top: 0, behavior: 'smooth' });
                
                // Scroll modal content to top
                setTimeout(() => {
                    if (modalContent) {
                        modalContent.scrollTop = 0;
                    }
                    if (modalBody) {
                        modalBody.scrollTop = 0;
                    }
                    modal.scrollTop = 0;
                }, 100);
            }
            
            // Initialize inline country search after modal opens
            setTimeout(function() {
                initCountrySearch();
            }, 100);
        }
        
        function populateRoomInfo(room, checkIn, checkOut, nights) {
            // Check if room info elements exist (they may be hidden on mobile)
            const modalRoomType = document.getElementById('modalRoomType');
            if (!modalRoomType) {
                // Room info section doesn't exist, skip population
                return;
            }
            
            // Room Type
            modalRoomType.textContent = formatRoomTypeName(room.room_type);
            
            // Room Images
            var storageBase = '{{ asset("storage") }}';
            var defaultImage = '{{ asset("royal-master/image/banner/banner-1.jpg") }}';
            
            let roomImages = [];
            if (room.images && Array.isArray(room.images) && room.images.length > 0) {
                roomImages = room.images.map(img => {
                    let imgPath = img;
                    if (img.startsWith('rooms/') || img.startsWith('storage/rooms/')) {
                        imgPath = img.replace(/^storage\//, '');
                    } else if (!img.startsWith('http') && !img.startsWith('/')) {
                        imgPath = 'rooms/' + img;
                    }
                    return imgPath.startsWith('http') ? imgPath : storageBase + '/' + imgPath;
                });
            } else {
                roomImages = [defaultImage];
            }
            
            // Set main image
            const mainImage = document.getElementById('modalRoomMainImage');
            if (mainImage) {
                mainImage.src = roomImages[0];
                mainImage.alt = formatRoomTypeName(room.room_type);
            }
            
            // Create thumbnails
            const thumbnailsContainer = document.getElementById('modalRoomThumbnails');
            if (thumbnailsContainer) {
                thumbnailsContainer.innerHTML = '';
                
                if (roomImages.length > 1) {
                    roomImages.forEach((imgUrl, index) => {
                        const thumbnail = document.createElement('img');
                        thumbnail.src = imgUrl;
                        thumbnail.className = 'modal-room-thumbnail' + (index === 0 ? ' active' : '');
                        thumbnail.alt = 'Room image ' + (index + 1);
                        thumbnail.onclick = function() {
                            if (mainImage) {
                                mainImage.src = imgUrl;
                            }
                            // Update active thumbnail
                            thumbnailsContainer.querySelectorAll('.modal-room-thumbnail').forEach(t => t.classList.remove('active'));
                            thumbnail.classList.add('active');
                        };
                        thumbnailsContainer.appendChild(thumbnail);
                    });
                }
            }
            
            // Room Description
            const modalRoomDescription = document.getElementById('modalRoomDescription');
            if (modalRoomDescription) {
                const description = room.description || 'Comfortable and well-appointed room for your stay. Features modern amenities and elegant furnishings for a relaxing experience.';
                modalRoomDescription.textContent = description;
            }
            
            // Room Details
            const detailsContainer = document.getElementById('modalRoomDetails');
            if (detailsContainer) {
                detailsContainer.innerHTML = '';
                
                // Show availability count instead of room number (guest privacy)
                if (room.available_count !== undefined) {
                    const detail = document.createElement('div');
                    detail.className = 'modal-room-detail-item';
                    const availabilityText = room.available_count > 1 
                        ? room.available_count + ' Available' 
                        : 'Available';
                    detail.innerHTML = '<i class="fa fa-check-circle"></i><span><strong>Availability:</strong> ' + availabilityText + '</span>';
                    detailsContainer.appendChild(detail);
                }
                
                if (room.capacity) {
                    const detail = document.createElement('div');
                    detail.className = 'modal-room-detail-item';
                    detail.innerHTML = '<i class="fa fa-users"></i><span><strong>Capacity:</strong> ' + room.capacity + ' guests</span>';
                    detailsContainer.appendChild(detail);
                }
                
                if (room.bed_type) {
                    const detail = document.createElement('div');
                    detail.className = 'modal-room-detail-item';
                    detail.innerHTML = '<i class="fa fa-bed"></i><span><strong>Bed:</strong> ' + room.bed_type + '</span>';
                    detailsContainer.appendChild(detail);
                }
                
                if (room.floor_location) {
                    const detail = document.createElement('div');
                    detail.className = 'modal-room-detail-item';
                    detail.innerHTML = '<i class="fa fa-layer-group"></i><span><strong>Floor:</strong> ' + room.floor_location + '</span>';
                    detailsContainer.appendChild(detail);
                }
                
                // Get amenities
                let amenities = [];
                if (room.amenities !== null && room.amenities !== undefined) {
                    if (Array.isArray(room.amenities)) {
                        amenities = room.amenities;
                    } else if (typeof room.amenities === 'string') {
                        try {
                            const parsed = JSON.parse(room.amenities);
                            amenities = Array.isArray(parsed) ? parsed : [];
                        } catch (e) {
                            amenities = room.amenities.split(',').map(a => a.trim()).filter(a => a.length > 0);
                        }
                    }
                }
                
                if (amenities.length > 0) {
                    const validAmenities = amenities.filter(a => a && a.toString().trim().length > 0 && a.toString().trim() !== 'null' && a.toString().trim() !== 'undefined');
                    if (validAmenities.length > 0) {
                        const detail = document.createElement('div');
                        detail.className = 'modal-room-detail-item';
                        detail.style.gridColumn = '1 / -1';
                        detail.innerHTML = '<i class="fa fa-star"></i><span><strong>Amenities:</strong> ' + validAmenities.slice(0, 5).join(', ') + (validAmenities.length > 5 ? '...' : '') + '</span>';
                        detailsContainer.appendChild(detail);
                    }
                }
            }
        }
        
        // Show room details modal
        function showRoomDetails(roomType, roomIndex) {
            const room = currentRoomsData[roomType];
            if (!room) {
                console.error('Room data not found for type:', roomType);
                return;
            }
            
            // Get current check-in/check-out dates
            var checkInElem = document.getElementById('check_in');
            var checkOutElem = document.getElementById('check_out');
            const checkIn = currentCheckIn || (checkInElem ? checkInElem.value : '');
            const checkOut = currentCheckOut || (checkOutElem ? checkOutElem.value : '');
            const checkInDate = checkIn ? new Date(checkIn) : null;
            const checkOutDate = checkOut ? new Date(checkOut) : null;
            const nights = (checkInDate && checkOutDate) ? Math.ceil((checkOutDate - checkInDate) / (1000 * 60 * 60 * 24)) : 1;
            
            // Set title
            document.getElementById('roomDetailsTitle').textContent = formatRoomTypeName(room.room_type) + ' - Full Details';
            
            // Handle images
            var storageBase = '{{ asset("storage") }}';
            var defaultImage = '{{ asset("royal-master/image/banner/banner-1.jpg") }}';
            
            let roomImages = [];
            if (room.images && Array.isArray(room.images) && room.images.length > 0) {
                roomImages = room.images.map(img => {
                    let imgPath = img;
                    if (img.startsWith('rooms/') || img.startsWith('storage/rooms/')) {
                        imgPath = img.replace(/^storage\//, '');
                    } else if (!img.startsWith('http') && !img.startsWith('/')) {
                        imgPath = 'rooms/' + img;
                    }
                    return imgPath.startsWith('http') ? imgPath : storageBase + '/' + imgPath;
                });
            } else {
                roomImages = [defaultImage];
            }
            
            // Set main image
            const mainImage = document.getElementById('roomDetailsMainImage');
            if (mainImage && roomImages.length > 0) {
                mainImage.src = roomImages[0];
                mainImage.alt = formatRoomTypeName(room.room_type);
            }
            
            // Create thumbnails
            const thumbnailsContainer = document.getElementById('roomDetailsThumbnails');
            if (thumbnailsContainer) {
                thumbnailsContainer.innerHTML = '';
                if (roomImages.length > 1) {
                    roomImages.forEach((imgUrl, index) => {
                        const thumbnail = document.createElement('img');
                        thumbnail.src = imgUrl;
                        thumbnail.style.width = '80px';
                        thumbnail.style.height = '80px';
                        thumbnail.style.objectFit = 'cover';
                        thumbnail.style.borderRadius = '4px';
                        thumbnail.style.cursor = 'pointer';
                        thumbnail.style.border = index === 0 ? '2px solid #e77a3a' : '2px solid transparent';
                        thumbnail.onclick = function() {
                            mainImage.src = imgUrl;
                            thumbnailsContainer.querySelectorAll('img').forEach(t => t.style.border = '2px solid transparent');
                            thumbnail.style.border = '2px solid #e77a3a';
                        };
                        thumbnailsContainer.appendChild(thumbnail);
                    });
                }
            }
            
            // Description
            const description = document.getElementById('roomDetailsDescription');
            if (description) {
                description.textContent = room.description || 'Comfortable and well-appointed room for your stay. Features modern amenities and elegant furnishings for a relaxing experience.';
            }
            
            // Room Information
            const infoContainer = document.getElementById('roomDetailsInfo');
            if (infoContainer) {
                infoContainer.innerHTML = '';
                
                if (room.available_count !== undefined) {
                    const availabilityText = room.available_count > 1 ? room.available_count + ' Available' : 'Available';
                    infoContainer.innerHTML += '<div style="padding: 10px; background: #f8f9fa; border-radius: 4px;"><i class="fa fa-check-circle" style="color: #28a745; margin-right: 8px;"></i><strong>Availability:</strong> ' + availabilityText + '</div>';
                }
                
                if (room.capacity) {
                    infoContainer.innerHTML += '<div style="padding: 10px; background: #f8f9fa; border-radius: 4px;"><i class="fa fa-users" style="color: #e77a3a; margin-right: 8px;"></i><strong>Capacity:</strong> ' + room.capacity + ' guests</div>';
                }
                
                if (room.bed_type) {
                    infoContainer.innerHTML += '<div style="padding: 10px; background: #f8f9fa; border-radius: 4px;"><i class="fa fa-bed" style="color: #e77a3a; margin-right: 8px;"></i><strong>Bed Type:</strong> ' + room.bed_type + '</div>';
                }
                
                if (room.floor_location) {
                    infoContainer.innerHTML += '<div style="padding: 10px; background: #f8f9fa; border-radius: 4px;"><i class="fa fa-layer-group" style="color: #e77a3a; margin-right: 8px;"></i><strong>Floor:</strong> ' + room.floor_location + '</div>';
                }
                
                if (room.bathroom_type) {
                    infoContainer.innerHTML += '<div style="padding: 10px; background: #f8f9fa; border-radius: 4px;"><i class="fa fa-bath" style="color: #e77a3a; margin-right: 8px;"></i><strong>Bathroom:</strong> ' + room.bathroom_type + '</div>';
                }
                
                if (room.checkin_time || room.checkout_time) {
                    const checkinTime = room.checkin_time || 'N/A';
                    const checkoutTime = room.checkout_time || 'N/A';
                    infoContainer.innerHTML += '<div style="padding: 10px; background: #f8f9fa; border-radius: 4px; grid-column: 1 / -1;"><i class="fa fa-clock-o" style="color: #e77a3a; margin-right: 8px;"></i><strong>Check-in:</strong> ' + checkinTime + ' | <strong>Check-out:</strong> ' + checkoutTime + '</div>';
                }
            }
            
            // Amenities
            const amenitiesContainer = document.getElementById('roomDetailsAmenities');
            if (amenitiesContainer) {
                amenitiesContainer.innerHTML = '';
                let amenities = [];
                if (room.amenities !== null && room.amenities !== undefined) {
                    if (Array.isArray(room.amenities)) {
                        amenities = room.amenities;
                    } else if (typeof room.amenities === 'string') {
                        try {
                            const parsed = JSON.parse(room.amenities);
                            amenities = Array.isArray(parsed) ? parsed : [];
                        } catch (e) {
                            amenities = room.amenities.split(',').map(a => a.trim()).filter(a => a.length > 0);
                        }
                    }
                }
                
                const validAmenities = amenities.filter(a => a && a.toString().trim().length > 0 && a.toString().trim() !== 'null' && a.toString().trim() !== 'undefined');
                if (validAmenities.length > 0) {
                    validAmenities.forEach(amenity => {
                        const badge = document.createElement('span');
                        badge.style.cssText = 'padding: 6px 12px; background: #e77a3a; color: white; border-radius: 20px; font-size: 12px; font-weight: 600;';
                        badge.textContent = amenity.toString().trim();
                        amenitiesContainer.appendChild(badge);
                    });
                } else {
                    amenitiesContainer.innerHTML = '<span style="color: #999;">No amenities listed</span>';
                }
            }
            
            // Badges
            const badgesContainer = document.getElementById('roomDetailsBadges');
            if (badgesContainer) {
                badgesContainer.innerHTML = '';
                if (room.pet_friendly || room.smoking_allowed) {
                    if (room.pet_friendly) {
                        badgesContainer.innerHTML += '<span style="padding: 6px 12px; background: #28a745; color: white; border-radius: 20px; font-size: 12px; font-weight: 600; margin-right: 10px;"><i class="fa fa-paw"></i> Pet Friendly</span>';
                    }
                    if (room.smoking_allowed) {
                        badgesContainer.innerHTML += '<span style="padding: 6px 12px; background: #6c757d; color: white; border-radius: 20px; font-size: 12px; font-weight: 600;"><i class="fa fa-smoking"></i> Smoking Allowed</span>';
                    }
                }
            }
            
            // Pricing
            const pricingContainer = document.getElementById('roomDetailsPricing');
            if (pricingContainer) {
                const pricePerNight = parseFloat(room.price_per_night || 0);
                const exchangeRate = window.exchangeRate || 2500;
                const pricePerNightTZS = pricePerNight * exchangeRate;
                const totalPrice = pricePerNight * nights;
                const totalPriceTZS = totalPrice * exchangeRate;
                
                pricingContainer.innerHTML = '<div style="display: flex; justify-content: space-between; margin-bottom: 10px;"><span><strong>Price per night:</strong></span><span style="font-size: 18px; font-weight: 700; color: #e77a3a;">$' + pricePerNight.toFixed(2) + ' / ≈ ' + pricePerNightTZS.toFixed(0) + ' TZS</span></div>';
                if (nights > 0) {
                    pricingContainer.innerHTML += '<div style="display: flex; justify-content: space-between; padding-top: 10px; border-top: 1px solid #ddd;"><span><strong>Total for ' + nights + ' night' + (nights > 1 ? 's' : '') + ':</strong></span><span style="font-size: 20px; font-weight: 700; color: #e77a3a;">$' + totalPrice.toFixed(2) + ' / ≈ ' + totalPriceTZS.toFixed(0) + ' TZS</span></div>';
                }
            }
            
            // Store room type for booking button
            const bookBtn = document.getElementById('roomDetailsBookBtn');
            if (bookBtn) {
                bookBtn.setAttribute('data-room-type', roomType);
            }
            
            // Show modal
            document.getElementById('roomDetailsModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
        
        // Close room details modal
        function closeRoomDetailsModal() {
            document.getElementById('roomDetailsModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        
        // Book from details modal
        function bookFromDetails() {
            const bookBtn = document.getElementById('roomDetailsBookBtn');
            const roomType = bookBtn ? bookBtn.getAttribute('data-room-type') : null;
            if (roomType) {
                closeRoomDetailsModal();
                var checkInElem = document.getElementById('check_in');
                var checkOutElem = document.getElementById('check_out');
                const checkIn = currentCheckIn || (checkInElem ? checkInElem.value : '');
                const checkOut = currentCheckOut || (checkOutElem ? checkOutElem.value : '');
                const checkInDate = checkIn ? new Date(checkIn) : null;
                const checkOutDate = checkOut ? new Date(checkOut) : null;
                const nights = (checkInDate && checkOutDate) ? Math.ceil((checkOutDate - checkInDate) / (1000 * 60 * 60 * 24)) : 1;
                openBookingModal(roomType, checkIn, checkOut, nights);
            }
        }
        
        function populateBookingSummary(room, checkIn, checkOut, nights) {
            // Format dates for compact summary
            const checkInDate = new Date(checkIn);
            const checkOutDate = new Date(checkOut);
            const options = { month: 'short', day: 'numeric' };
            
            const checkInStr = checkInDate.toLocaleDateString('en-US', options);
            const checkOutStr = checkOutDate.toLocaleDateString('en-US', options);
            
            // Update compact summary at top
            document.getElementById('summaryCheckIn').textContent = checkInStr + ' - ' + checkOutStr;
            document.getElementById('summaryNights').textContent = nights + (nights === 1 ? ' night' : ' nights');
            
            // Calculate prices
            const pricePerNight = parseFloat(room.price_per_night || 0);
            const totalPrice = parseFloat(room.calculated_price || pricePerNight * nights);
            const subtotal = pricePerNight * nights;
            
            // Total equals subtotal (no tax)
            const finalTotal = subtotal;
            
            // Update compact total
            document.getElementById('summaryTotal').textContent = '$' + finalTotal.toFixed(2);
            
            // Update detailed breakdown
            const breakdown = document.getElementById('bookingSummaryBreakdown');
            document.getElementById('breakdownPricePerNight').textContent = '$' + pricePerNight.toFixed(2);
            document.getElementById('breakdownNights').textContent = nights + (nights === 1 ? ' night' : ' nights');
            document.getElementById('breakdownSubtotal').textContent = '$' + subtotal.toFixed(2);
            document.getElementById('breakdownTotal').textContent = '$' + finalTotal.toFixed(2);
            
            // Show breakdown
            breakdown.style.display = 'block';
        }
        
        // Clear field error - Make sure it's globally accessible
        window.clearFieldError = function(fieldName) {
            try {
                const errorElement = document.getElementById(fieldName + '_error');
                const inputElement = document.getElementById(fieldName);
                
                if (errorElement) {
                    errorElement.textContent = '';
                    errorElement.style.display = 'none';
                }
                
                if (inputElement) {
                    inputElement.classList.remove('error');
                }
            } catch (e) {
                console.error('Error clearing field error:', e);
            }
        };
        
        // Show field error - Make sure it's globally accessible
        window.showFieldError = function(fieldName, message) {
            try {
                const errorElement = document.getElementById(fieldName + '_error');
                const inputElement = document.getElementById(fieldName);
                
                if (errorElement) {
                    errorElement.textContent = message;
                    errorElement.style.display = 'block';
                }
                
                if (inputElement) {
                    inputElement.classList.add('error');
                    // Scroll to first error
                    const errorCount = document.querySelectorAll('input.error, select.error, textarea.error').length;
                    if (errorCount === 1) {
                        setTimeout(() => {
                            inputElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }, 100);
                    }
                }
            } catch (e) {
                console.error('Error showing field error:', e);
            }
        };
        
        // Clear all errors - Make sure it's globally accessible
        window.clearAllErrors = function() {
            try {
                const errorElements = document.querySelectorAll('.error-message');
                const errorInputs = document.querySelectorAll('input.error, select.error, textarea.error');
                
                errorElements.forEach(el => {
                    el.textContent = '';
                    el.style.display = 'none';
                });
                errorInputs.forEach(el => el.classList.remove('error'));
            } catch (e) {
                console.error('Error clearing all errors:', e);
            }
        };

        function closeBookingModal() {
            document.getElementById('bookingModal').style.display = 'none';
            document.body.style.overflow = 'auto';
            document.getElementById('bookingForm').reset();
            
            // Password fields removed - password is auto-generated from first name
            // No need to reset password fields
            
            // Clear all errors
            clearAllErrors();
            document.getElementById('bookingAlertContainer').innerHTML = '';
            
            // Hide booking summary breakdown
            const breakdown = document.getElementById('bookingSummaryBreakdown');
            if (breakdown) {
                breakdown.style.display = 'none';
            }
            
            // Reset country search
            const countrySearch = document.getElementById('country_search');
            const countryDropdown = document.getElementById('countryDropdown');
            const countryHidden = document.getElementById('country');
            if (countrySearch) countrySearch.value = '';
            if (countryHidden) countryHidden.value = '';
            if (countryDropdown) countryDropdown.classList.remove('show');
        }

        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('bookingModal');
            if (event.target == modal) {
                closeBookingModal();
            }
        });

        // Close modal with X button
        var closeModalBtn = document.querySelector('.close-modal');
        if (closeModalBtn) {
            closeModalBtn.addEventListener('click', closeBookingModal);
        }
        
        // Close room details modal when clicking outside
        window.addEventListener('click', function(event) {
            const roomDetailsModal = document.getElementById('roomDetailsModal');
            if (event.target === roomDetailsModal) {
                closeRoomDetailsModal();
            }
        });

        // Country list with country codes
        const countries = [
            { name: 'Afghanistan', flag: '🇦🇫', code: '+93' },
            { name: 'Albania', flag: '🇦🇱', code: '+355' },
            { name: 'Algeria', flag: '🇩🇿', code: '+213' },
            { name: 'Argentina', flag: '🇦🇷', code: '+54' },
            { name: 'Australia', flag: '🇦🇺', code: '+61' },
            { name: 'Austria', flag: '🇦🇹', code: '+43' },
            { name: 'Bangladesh', flag: '🇧🇩', code: '+880' },
            { name: 'Belgium', flag: '🇧🇪', code: '+32' },
            { name: 'Brazil', flag: '🇧🇷', code: '+55' },
            { name: 'Canada', flag: '🇨🇦', code: '+1' },
            { name: 'China', flag: '🇨🇳', code: '+86' },
            { name: 'Denmark', flag: '🇩🇰', code: '+45' },
            { name: 'Egypt', flag: '🇪🇬', code: '+20' },
            { name: 'Finland', flag: '🇫🇮', code: '+358' },
            { name: 'France', flag: '🇫🇷', code: '+33' },
            { name: 'Germany', flag: '🇩🇪', code: '+49' },
            { name: 'Ghana', flag: '🇬🇭', code: '+233' },
            { name: 'Greece', flag: '🇬🇷', code: '+30' },
            { name: 'India', flag: '🇮🇳', code: '+91' },
            { name: 'Indonesia', flag: '🇮🇩', code: '+62' },
            { name: 'Ireland', flag: '🇮🇪', code: '+353' },
            { name: 'Italy', flag: '🇮🇹', code: '+39' },
            { name: 'Japan', flag: '🇯🇵', code: '+81' },
            { name: 'Kenya', flag: '🇰🇪', code: '+254' },
            { name: 'Malaysia', flag: '🇲🇾', code: '+60' },
            { name: 'Mexico', flag: '🇲🇽', code: '+52' },
            { name: 'Morocco', flag: '🇲🇦', code: '+212' },
            { name: 'Netherlands', flag: '🇳🇱', code: '+31' },
            { name: 'New Zealand', flag: '🇳🇿', code: '+64' },
            { name: 'Nigeria', flag: '🇳🇬', code: '+234' },
            { name: 'Norway', flag: '🇳🇴', code: '+47' },
            { name: 'Pakistan', flag: '🇵🇰', code: '+92' },
            { name: 'Philippines', flag: '🇵🇭', code: '+63' },
            { name: 'Poland', flag: '🇵🇱', code: '+48' },
            { name: 'Portugal', flag: '🇵🇹', code: '+351' },
            { name: 'Rwanda', flag: '🇷🇼', code: '+250' },
            { name: 'Russia', flag: '🇷🇺', code: '+7' },
            { name: 'Saudi Arabia', flag: '🇸🇦', code: '+966' },
            { name: 'Singapore', flag: '🇸🇬', code: '+65' },
            { name: 'South Africa', flag: '🇿🇦', code: '+27' },
            { name: 'South Korea', flag: '🇰🇷', code: '+82' },
            { name: 'Spain', flag: '🇪🇸', code: '+34' },
            { name: 'Sweden', flag: '🇸🇪', code: '+46' },
            { name: 'Switzerland', flag: '🇨🇭', code: '+41' },
            { name: 'Tanzania', flag: '🇹🇿', code: '+255' },
            { name: 'Thailand', flag: '🇹🇭', code: '+66' },
            { name: 'Turkey', flag: '🇹🇷', code: '+90' },
            { name: 'Uganda', flag: '🇺🇬', code: '+256' },
            { name: 'Ukraine', flag: '🇺🇦', code: '+380' },
            { name: 'United Arab Emirates', flag: '🇦🇪', code: '+971' },
            { name: 'United Kingdom', flag: '🇬🇧', code: '+44' },
            { name: 'United States', flag: '🇺🇸', code: '+1' },
            { name: 'Vietnam', flag: '🇻🇳', code: '+84' },
            { name: 'Zambia', flag: '🇿🇲', code: '+260' },
            { name: 'Zimbabwe', flag: '🇿🇼', code: '+263' },
            { name: 'Other', flag: '🌍', code: '+1' }
        ];
        
        // Initialize inline country search
        function initCountrySearch() {
            const countrySearchInput = document.getElementById('country_search');
            const countryHiddenInput = document.getElementById('country');
            const countryDropdown = document.getElementById('countryDropdown');
            
            if (!countrySearchInput || !countryDropdown) return;
            
            let selectedCountry = null;
            let highlightedIndex = -1;
            
            // Render country options
            function renderCountries(filterTerm = '') {
                countryDropdown.innerHTML = '';
                const filtered = countries.filter(c => 
                    c.name.toLowerCase().includes(filterTerm.toLowerCase())
                );
                
                if (filtered.length === 0) {
                    countryDropdown.innerHTML = '<div class="country-option-no-results">No countries found</div>';
                    return;
                }
                
                filtered.forEach((country, index) => {
                    const option = document.createElement('div');
                    option.className = 'country-option' + (selectedCountry === country.name ? ' selected' : '');
                    option.dataset.value = country.name;
                    option.dataset.index = index;
                    option.innerHTML = `<span style="margin-right: 10px; font-size: 18px;">${country.flag}</span>${country.name}`;
                    
                    option.addEventListener('click', function() {
                        selectedCountry = country.name;
                        countrySearchInput.value = country.name;
                        countryHiddenInput.value = country.name;
                        
                        // Display flag visually
                        const flagDisplay = document.getElementById('country_flag_display');
                        if (flagDisplay) {
                            flagDisplay.textContent = country.flag;
                            flagDisplay.style.display = 'block';
                            countrySearchInput.classList.add('has-flag');
                        }
                        
                        countryDropdown.classList.remove('show');
                        renderCountries('');
                        highlightedIndex = -1;
                        
                        // Clear country error when country is selected
                        if (window.clearFieldError) {
                            window.clearFieldError('country');
                        }
                        
                        // Auto-select country code
                        const countryCodeSelect = document.getElementById('country_code');
                        if (countryCodeSelect && country.code) {
                            // Find and select the matching country code
                            const options = countryCodeSelect.options;
                            for (let i = 0; i < options.length; i++) {
                                if (options[i].value === country.code) {
                                    countryCodeSelect.selectedIndex = i;
                                    break;
                                }
                            }
                        }
                    });
                    
                    option.addEventListener('mouseenter', function() {
                        highlightedIndex = index;
                        updateHighlight();
                    });
                    
                    countryDropdown.appendChild(option);
                });
            }
            
            function updateHighlight() {
                const options = countryDropdown.querySelectorAll('.country-option');
                options.forEach((opt, idx) => {
                    opt.classList.toggle('highlighted', idx === highlightedIndex);
                });
            }
            
            // Show dropdown on focus
            countrySearchInput.addEventListener('focus', function() {
                countryDropdown.classList.add('show');
                renderCountries(this.value);
            });
            
            // Filter on input
            countrySearchInput.addEventListener('input', function(e) {
                const value = e.target.value;
                countryDropdown.classList.add('show');
                renderCountries(value);
                highlightedIndex = -1;
                
                // Hide flag when user starts typing
                const flagDisplay = document.getElementById('country_flag_display');
                if (flagDisplay) {
                    flagDisplay.style.display = 'none';
                    countrySearchInput.classList.remove('has-flag');
                }
                
                // If exact match, set hidden input and auto-select country code
                // But don't change the visible input value to avoid interfering with typing
                const exactMatch = countries.find(c => c.name.toLowerCase() === value.toLowerCase());
                if (exactMatch) {
                    countryHiddenInput.value = exactMatch.name;
                    selectedCountry = exactMatch.name;
                    
                    // Show flag for exact match
                    if (flagDisplay) {
                        flagDisplay.textContent = exactMatch.flag;
                        flagDisplay.style.display = 'block';
                        countrySearchInput.classList.add('has-flag');
                    }
                    
                    // Auto-select country code
                    const countryCodeSelect = document.getElementById('country_code');
                    if (countryCodeSelect && exactMatch.code) {
                        const options = countryCodeSelect.options;
                        for (let i = 0; i < options.length; i++) {
                            if (options[i].value === exactMatch.code) {
                                countryCodeSelect.selectedIndex = i;
                                break;
                            }
                        }
                    }
                } else {
                    countryHiddenInput.value = '';
                    selectedCountry = null;
                }
            });
            
            // Keyboard navigation
            countrySearchInput.addEventListener('keydown', function(e) {
                const options = countryDropdown.querySelectorAll('.country-option');
                
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    highlightedIndex = Math.min(highlightedIndex + 1, options.length - 1);
                    updateHighlight();
                    if (options[highlightedIndex]) {
                        options[highlightedIndex].scrollIntoView({ block: 'nearest' });
                    }
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    highlightedIndex = Math.max(highlightedIndex - 1, -1);
                    updateHighlight();
                    if (options[highlightedIndex]) {
                        options[highlightedIndex].scrollIntoView({ block: 'nearest' });
                    }
                } else if (e.key === 'Enter' && highlightedIndex >= 0 && options[highlightedIndex]) {
                    e.preventDefault();
                    options[highlightedIndex].click();
                } else if (e.key === 'Escape') {
                    countryDropdown.classList.remove('show');
                }
            });
            
            // Close on outside click
            document.addEventListener('click', function(e) {
                const wrapper = countrySearchInput.closest('.country-search-wrapper');
                if (!wrapper.contains(e.target)) {
                    countryDropdown.classList.remove('show');
                }
            });
            
            // Initial render
            renderCountries('');
        }
        
        // Password functions removed - password is auto-generated from first name
        
        function submitBooking() {
            const form = document.getElementById('bookingForm');
            const alertContainer = document.getElementById('bookingAlertContainer');
            
            // Clear all previous errors
            clearAllErrors();
            
            // Get form values
            const firstName = document.getElementById('first_name').value.trim();
            const lastName = document.getElementById('last_name').value.trim();
            const email = document.getElementById('guest_email').value.trim();
            const country = document.getElementById('country').value || document.getElementById('country_search').value;
            const countryCode = document.getElementById('country_code').value;
            const phone = document.getElementById('guest_phone').value.trim();
            var bookingForInput = document.querySelector('input[name="booking_for"]');
            const bookingFor = (bookingForInput ? bookingForInput.value : null) || 'me';
            const arrivalTime = document.getElementById('arrival_time').value;
            const termsAccepted = document.getElementById('terms_accepted').checked;
            
            let hasErrors = false;
            
            // Validate first name
            if (!firstName) {
                showFieldError('first_name', 'First name is required');
                hasErrors = true;
            }
            
            // Validate last name
            if (!lastName) {
                showFieldError('last_name', 'Last name is required');
                hasErrors = true;
            }
            
            // Validate email
            if (!email) {
                showFieldError('guest_email', 'Email address is required');
                hasErrors = true;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                showFieldError('guest_email', 'Please enter a valid email address');
                hasErrors = true;
            }
            
            // Validate country
            if (!country) {
                showFieldError('country', 'Country/region is required');
                hasErrors = true;
            }
            
            // Validate phone
            if (!phone) {
                showFieldError('guest_phone', 'Phone number is required');
                hasErrors = true;
            } else if (!/^\d{6,15}$/.test(phone.replace(/\s+/g, ''))) {
                showFieldError('guest_phone', 'Please enter a valid phone number');
                hasErrors = true;
            }
            
            // Validate terms acceptance
            if (!termsAccepted) {
                showFieldError('terms_accepted', 'You must accept the Terms & Conditions to proceed');
                hasErrors = true;
            }
            
            // If there are errors, stop submission
            if (hasErrors) {
                alertContainer.innerHTML = '<div class="alert alert-error"><strong>Please fix the errors above to continue.</strong></div>';
                return;
            }
            
            
            // Combine first and last name
            const guestName = firstName + ' ' + lastName;
            
            // Combine country code and phone
            const fullPhone = countryCode + phone;
            
            // Prepare form data
            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            formData.append('room_type', document.getElementById('booking_room_type').value);
            formData.append('check_in', document.getElementById('booking_check_in').value);
            formData.append('check_out', document.getElementById('booking_check_out').value);
            formData.append('guest_name', guestName);
            formData.append('first_name', firstName);
            formData.append('last_name', lastName);
            formData.append('guest_email', email);
            formData.append('country', country);
            formData.append('guest_phone', fullPhone);
            formData.append('country_code', countryCode);
            formData.append('booking_for', bookingFor);
            formData.append('arrival_time', arrivalTime);
            formData.append('number_of_guests', document.getElementById('number_of_guests').value);
            formData.append('terms_accepted', termsAccepted ? '1' : '0');
            
            // Airport pickup data (optional)
            const airportPickupRequired = document.getElementById('airport_pickup_required').checked;
            formData.append('airport_pickup_required', airportPickupRequired ? '1' : '0');
            
            if (airportPickupRequired) {
                formData.append('flight_number', document.getElementById('flight_number').value || '');
                formData.append('airline', document.getElementById('airline').value || '');
                formData.append('arrival_time_pickup', document.getElementById('arrival_time_pickup').value || '');
                formData.append('pickup_passengers', document.getElementById('pickup_passengers').value || '');
                formData.append('luggage_info', document.getElementById('luggage_info').value || '');
                formData.append('pickup_contact_number', document.getElementById('pickup_contact_number').value || '');
            }

            alertContainer.innerHTML = '<div class="alert alert-info">Processing your booking...</div>';

            fetch('{{ route("booking.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alertContainer.innerHTML = '<div class="alert alert-success"><strong>Success!</strong> Redirecting to PayPal for payment...</div>';
                    
                    // Redirect to PayPal payment page
                    if (data.redirect_url) {
                        setTimeout(function() {
                            window.location.href = data.redirect_url;
                        }, 1500);
                    } else {
                        alertContainer.innerHTML = '<div class="alert alert-error"><strong>Error!</strong> Payment redirect URL not found.</div>';
                    }
                } else {
                    const errorMsg = data.message || 'Failed to submit booking. Please try again.';
                    alertContainer.innerHTML = '<div class="alert alert-error"><strong>Error!</strong> ' + errorMsg + '</div>';
                }
            })
            .catch(error => {
                alertContainer.innerHTML = '<div class="alert alert-error"><strong>Error!</strong> An error occurred. Please try again.</div>';
                console.error('Error:', error);
            });
        }

        function showAlert(message, type) {
            const alertContainer = document.getElementById('alertContainer');
            const alertClass = type === 'success' ? 'alert-success' : type === 'error' ? 'alert-error' : 'alert-info';
            alertContainer.innerHTML = '<div class="alert ' + alertClass + '">' + message + '</div>';
            
            setTimeout(() => {
                alertContainer.innerHTML = '';
            }, 5000);
        }
        
        // REMOVED DUPLICATE - Using the main Flatpickr initialization above
        
        // Airport Pickup Toggle
        document.addEventListener('DOMContentLoaded', function() {
            const airportPickupCheckbox = document.getElementById('airport_pickup_required');
            const airportPickupFields = document.getElementById('airportPickupFields');
            
            if (airportPickupCheckbox && airportPickupFields) {
                airportPickupCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        airportPickupFields.style.display = 'block';
                    } else {
                        airportPickupFields.style.display = 'none';
                        // Clear fields when unchecked
                        document.getElementById('flight_number').value = '';
                        document.getElementById('airline').value = '';
                        document.getElementById('arrival_time_pickup').value = '';
                        document.getElementById('pickup_passengers').value = '';
                        document.getElementById('luggage_info').value = '';
                        document.getElementById('pickup_contact_number').value = '';
                    }
                });
            }
        });
        
        // Terms & Conditions Modal Functions
        function openTermsModal() {
            const modal = document.getElementById('termsModal');
            if (modal) {
                modal.style.display = 'block';
                document.body.style.overflow = 'hidden';
            }
        }
        
        function closeTermsModal() {
            const modal = document.getElementById('termsModal');
            if (modal) {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        }
        
        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            const termsModal = document.getElementById('termsModal');
            if (event.target == termsModal) {
                closeTermsModal();
            }
        });
        
        // Close modal with X button
        document.addEventListener('DOMContentLoaded', function() {
            const closeBtn = document.querySelector('.close-terms-modal');
            if (closeBtn) {
                closeBtn.addEventListener('click', closeTermsModal);
            }
        });
        } catch (e) {
            console.error('JavaScript error in booking page:', e);
        }
    </script>
    
    <!-- Date Picker Initialization - Separate script to avoid syntax errors -->
    <script type="text/javascript">
        // Date picker initialization removed - using native HTML5 date inputs with placeholder overlay
        /*
        (function() {
            function initDatePickers() {
                if (typeof jQuery !== 'undefined' && typeof jQuery.fn.datetimepicker !== 'undefined') {
                    jQuery('#datetimepicker11,#datetimepicker1').datetimepicker({
                        daysOfWeekDisabled: [0, 6],
                        format: 'YYYY-MM-DD',
                        minDate: new Date()
                    });
                } else {
                    setTimeout(initDatePickers, 100);
                }
            }
            
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initDatePickers);
            } else {
                initDatePickers();
            }
        })();
        */
    </script>
    
    @include('landing_page_views.partials.chat-widgets')
</body>

</html>







