<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="icon" href="{{ asset('royal-master/image/logo/Logo.png') }}" type="image/png">
        <title>Services - PrimeLand Hotel</title>
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="{{ asset('royal-master/css/bootstrap.css') }}">
        <link rel="stylesheet" href="{{ asset('royal-master/vendors/linericon/style.css') }}">
        <link rel="stylesheet" href="{{ asset('royal-master/css/font-awesome.min.css') }}">
        <link rel="stylesheet" href="{{ asset('royal-master/vendors/owl-carousel/owl.carousel.min.css') }}">
        <link rel="stylesheet" href="{{ asset('royal-master/vendors/bootstrap-datepicker/bootstrap-datetimepicker.min.css') }}">
        <link rel="stylesheet" href="{{ asset('royal-master/vendors/nice-select/css/nice-select.css') }}">
        <!-- main css -->
        <link rel="stylesheet" href="{{ asset('royal-master/css/style.css') }}">
        <link rel="stylesheet" href="{{ asset('royal-master/css/responsive.css') }}">
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
            
            /* Header Logo Responsive */
            @media (max-width: 767px) {
                .header_area {
                    padding: 10px 0;
                }
                
                .navbar-brand.logo_h img {
                    height: 80px !important;
                    max-width: 280px !important;
                }
                
                .navbar-nav {
                    text-align: center;
                    padding: 20px 0;
                    background: rgba(255, 255, 255, 0.98);
                    border-radius: 5px;
                    margin-top: 10px;
                }
                
                .navbar-nav .nav-item {
                    margin: 5px 0;
                    border-bottom: 1px solid #f0f0f0;
                }
                
                .navbar-nav .nav-item:last-child {
                    border-bottom: none;
                }
                
                .navbar-nav .nav-link {
                    padding: 12px 15px;
                    font-size: 16px;
                    color: #333 !important;
                    display: block;
                }
                
                .navbar-nav .nav-link:hover {
                    background: #f8f9fa;
                    color: #e77a3a !important;
                }
                
                .navbar-nav .nav-item.active .nav-link {
                    color: #e77a3a !important;
                    font-weight: 600;
                }
                
                .navbar-nav .dropdown-menu {
                    position: static !important;
                    float: none;
                    width: 100%;
                    margin-top: 0;
                    background: #f8f9fa;
                    border: none;
                    box-shadow: none;
                }
                
                .navbar-nav .dropdown-menu .nav-link {
                    padding-left: 30px;
                }
                
                .navbar-toggler {
                    border-color: #e77a3a;
                    padding: 8px 12px;
                }
                
                .navbar-toggler .icon-bar {
                    background-color: #e77a3a;
                    display: block;
                    width: 22px;
                    height: 2px;
                    border-radius: 1px;
                    margin: 4px 0;
                }
            }
            
            @media (max-width: 480px) {
                .navbar-brand.logo_h img {
                    height: 75px !important;
                    max-width: 250px !important;
                }
                
                .navbar-nav .nav-link {
                    font-size: 15px;
                    padding: 10px 15px;
                }
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
            /* Loading Spinner */
            .page-loading {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(255, 255, 255, 0.95);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 9999;
                transition: opacity 0.3s ease, visibility 0.3s ease;
            }
            
            .page-loading.hidden {
                opacity: 0;
                visibility: hidden;
            }
            
            .spinner {
                width: 60px;
                height: 60px;
                border: 5px solid #f3f3f3;
                border-top: 5px solid #e77a3a;
                border-radius: 50%;
                animation: spin 1s linear infinite;
            }
            
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        </style>
    </head>
    <body>
        <!-- Loading spinner removed -->
        @include('landing_page_views.partials.royal-header')
        
        <!--================Breadcrumb Area =================-->
        <section class="breadcrumb_area" style="padding: 250px 0px 120px; min-height: 500px; background: transparent !important; position: relative;">
            <div class="overlay bg-parallax" data-stellar-ratio="0.8" data-stellar-vertical-offset="0" data-background="" style="background-image: url('{{ asset('hotel_gallery/swimming floating tray_.jpg') }}'); background-size: cover; background-position: center center; opacity: 1 !important; filter: none !important;"></div>
            <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.4); z-index: 0;"></div>
            <div class="container" style="position: relative; z-index: 1;">
                <div class="page-cover text-center">
                    <h2 class="page-cover-tittle" style="color: #e77a3a;">Our Services</h2>
                    <p style="color: white; font-size: 16px; margin-top: 15px; margin-bottom: 20px;">Experience comfort and convenience at PrimeLand Hotel</p>
                    <ol class="breadcrumb">
                        <li><a href="{{ url('/') }}">Home</a></li>
                        <li class="active">Services</li>
                    </ol>
                </div>
            </div>
        </section>
        <!--================Breadcrumb Area =================-->
        
        <!--================ Services Area  =================-->
        <section class="services_area section_gap" style="padding: 100px 0; background: #f8f9fa;">
            <div class="container">
                <div class="section_title text-center" style="margin-bottom: 60px;">
                    <h2 class="title_color" style="color: #e77a3a; font-size: 36px; margin-bottom: 15px;">Our Premium Services</h2>
                    <p style="font-size: 16px; color: #777;">Experience the best of PrimeLand Hotel with our exceptional amenities</p>
                </div>
                <div class="row">
                    <!-- Hotel Accommodation Service -->
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="service_card" style="background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.1); transition: transform 0.3s ease, box-shadow 0.3s ease; height: 100%; display: flex; flex-direction: column;">
                            <div class="service_image" style="height: 250px; overflow: hidden; position: relative; flex-shrink: 0;">
                                <img src="{{ asset('hotel_gallery/room_(5).jpg') }}" alt="Hotel Rooms" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
                                <div class="service_overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.5) 100%);"></div>
                                <div class="service_icon" style="position: absolute; top: 20px; right: 20px; width: 60px; height: 60px; background: rgba(231, 122, 58, 0.9); border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 2;">
                                    <i class="lnr lnr-home" style="font-size: 28px; color: #fff;"></i>
                                </div>
                            </div>
                            <div class="service_content" style="padding: 30px; flex-grow: 1; display: flex; flex-direction: column;">
                                <h3 style="color: #e77a3a; font-size: 22px; font-weight: 700; margin-bottom: 15px; min-height: 54px;">Hotel Accommodation</h3>
                                <p style="color: #666; line-height: 1.8; margin-bottom: 20px; flex-grow: 1; min-height: 90px;">Comfortable and well-appointed rooms designed for your relaxation. Each room features modern amenities and complimentary Wi-Fi.</p>
                                <a href="{{ route('booking.index') }}" class="service_link" style="color: #e77a3a; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; margin-top: auto;">
                                    Book Now <i class="fa fa-arrow-right" style="margin-left: 8px;"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Swimming Pool Service -->
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="service_card" style="background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.1); transition: transform 0.3s ease, box-shadow 0.3s ease; height: 100%; display: flex; flex-direction: column;">
                            <div class="service_image" style="height: 250px; overflow: hidden; position: relative; flex-shrink: 0;">
                                <img src="{{ asset('hotel_gallery/swimming view_(1).jpg') }}" alt="Swimming Pool" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
                                <div class="service_overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.5) 100%);"></div>
                                <div class="service_icon" style="position: absolute; top: 20px; right: 20px; width: 60px; height: 60px; background: rgba(231, 122, 58, 0.9); border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 2;">
                                    <i class="fa fa-tint" style="font-size: 28px; color: #fff;"></i>
                                </div>
                            </div>
                            <div class="service_content" style="padding: 30px; flex-grow: 1; display: flex; flex-direction: column;">
                                <h3 style="color: #e77a3a; font-size: 22px; font-weight: 700; margin-bottom: 15px; min-height: 54px;">Swimming Pool</h3>
                                <p style="color: #666; line-height: 1.8; margin-bottom: 20px; flex-grow: 1; min-height: 90px;">Take a refreshing dip in our clean, well-maintained swimming pool. Perfect for relaxation and exercise throughout your stay.</p>
                                <a href="https://www.instagram.com/primeland_hotel?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" target="_blank" class="service_link" style="color: #e77a3a; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; margin-top: auto;">
                                    View Gallery <i class="fa fa-arrow-right" style="margin-left: 8px;"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Restaurant Service -->
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="service_card" style="background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.1); transition: transform 0.3s ease, box-shadow 0.3s ease; height: 100%; display: flex; flex-direction: column;">
                            <div class="service_image" style="height: 250px; overflow: hidden; position: relative; flex-shrink: 0;">
                                <img src="{{ asset('hotel_gallery/restaurant_.jpg') }}" alt="Restaurant" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
                                <div class="service_overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.5) 100%);"></div>
                                <div class="service_icon" style="position: absolute; top: 20px; right: 20px; width: 60px; height: 60px; background: rgba(231, 122, 58, 0.9); border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 2;">
                                    <i class="lnr lnr-dinner" style="font-size: 28px; color: #fff;"></i>
                                </div>
                            </div>
                            <div class="service_content" style="padding: 30px; flex-grow: 1; display: flex; flex-direction: column;">
                                <h3 style="color: #e77a3a; font-size: 22px; font-weight: 700; margin-bottom: 15px; min-height: 54px;">Restaurant</h3>
                                <p style="color: #666; line-height: 1.8; margin-bottom: 20px; flex-grow: 1; min-height: 90px;">Savor delicious meals prepared with fresh, local ingredients. Our restaurant offers diverse menu featuring local and international cuisine.</p>
                                <a href="{{ url('/contact') }}" class="service_link" style="color: #e77a3a; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; margin-top: auto;">
                                    Contact Us <i class="fa fa-arrow-right" style="margin-left: 8px;"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Bar Service -->
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="service_card" style="background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.1); transition: transform 0.3s ease, box-shadow 0.3s ease; height: 100%; display: flex; flex-direction: column;">
                            <div class="service_image" style="height: 250px; overflow: hidden; position: relative; flex-shrink: 0;">
                                <img src="{{ asset('hotel_gallery/coffee_.jpg') }}" alt="Bar" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
                                <div class="service_overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.5) 100%);"></div>
                                <div class="service_icon" style="position: absolute; top: 20px; right: 20px; width: 60px; height: 60px; background: rgba(231, 122, 58, 0.9); border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 2;">
                                    <i class="lnr lnr-coffee-cup" style="font-size: 28px; color: #fff;"></i>
                                </div>
                            </div>
                            <div class="service_content" style="padding: 30px; flex-grow: 1; display: flex; flex-direction: column;">
                                <h3 style="color: #e77a3a; font-size: 22px; font-weight: 700; margin-bottom: 15px; min-height: 54px;">Bar & Lounge</h3>
                                <p style="color: #666; line-height: 1.8; margin-bottom: 20px; flex-grow: 1; min-height: 90px;">Unwind at our bar with a selection of premium beverages, cocktails, and light snacks. The perfect place to relax and socialize.</p>
                                <a href="{{ url('/contact') }}" class="service_link" style="color: #e77a3a; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; margin-top: auto;">
                                    Contact Us <i class="fa fa-arrow-right" style="margin-left: 8px;"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--================ Services Area  =================-->
        
        <style>
            .service_card:hover {
                transform: translateY(-10px);
                box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            }
            
            .service_card:hover .service_image img {
                transform: scale(1.1);
            }
            
            .service_link:hover {
                color: #d66a2a !important;
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
            
            /* Header Logo Responsive */
            @media (max-width: 767px) {
                .header_area {
                    padding: 10px 0;
                }
                
                .navbar-brand.logo_h img {
                    height: 80px !important;
                    max-width: 280px !important;
                }
                
                .navbar-nav {
                    text-align: center;
                    padding: 20px 0;
                }
                
                .navbar-nav .nav-item {
                    margin: 5px 0;
                }
                
                .navbar-nav .nav-link {
                    padding: 10px 15px;
                    font-size: 16px;
                }
            }
            
            @media (max-width: 480px) {
                .navbar-brand.logo_h img {
                    height: 75px !important;
                    max-width: 250px !important;
                }
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
                
                .services_area {
                    padding: 60px 0 !important;
                }
                
                .services_area .section_title h2 {
                    font-size: 28px !important;
                }
                
                .services_area .section_title p {
                    font-size: 14px !important;
                }
                
                .service_card {
                    margin-bottom: 30px;
                }
                
                .service_image {
                    height: 220px !important;
                }
                
                .service_content {
                    padding: 25px 20px !important;
                }
                
                .service_content h3 {
                    font-size: 20px !important;
                    min-height: auto !important;
                }
                
                .service_content p {
                    font-size: 14px !important;
                    min-height: auto !important;
                    margin-bottom: 15px !important;
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
                
                .services_area {
                    padding: 40px 0 !important;
                }
                
                .services_area .section_title {
                    margin-bottom: 40px !important;
                }
                
                .services_area .section_title h2 {
                    font-size: 24px !important;
                }
                
                .services_area .section_title p {
                    font-size: 13px !important;
                }
                
                .service_image {
                    height: 200px !important;
                }
                
                .service_content {
                    padding: 20px 15px !important;
                }
                
                .service_content h3 {
                    font-size: 18px !important;
                    margin-bottom: 12px !important;
                }
                
                .service_content p {
                    font-size: 13px !important;
                    line-height: 1.7 !important;
                }
                
                .service_icon {
                    width: 50px !important;
                    height: 50px !important;
                    top: 15px !important;
                    right: 15px !important;
                }
                
                .service_icon i {
                    font-size: 22px !important;
                }
            }
        </style>
        
        @include('landing_page_views.partials.royal-footer')
        
        @include('landing_page_views.partials.royal-scripts')
        @include('landing_page_views.partials.chat-widgets')
        
        <!-- Loading spinner JavaScript removed -->
    </body>
</html>
