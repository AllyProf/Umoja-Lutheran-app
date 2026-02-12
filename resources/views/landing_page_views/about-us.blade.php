<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="icon" href="{{ asset('royal-master/image/logo/Logo.png') }}" type="image/png">
        <title>About Us - PrimeLand Hotel</title>
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
            /* Loading spinner removed */
        </style>
    </head>
    <body>
        <!-- Loading spinner removed -->
        @include('landing_page_views.partials.royal-header')
        
        <!--================Breadcrumb Area =================-->
        <section class="breadcrumb_area" style="padding: 250px 0px 120px; min-height: 500px;">
            <div class="overlay bg-parallax" data-stellar-ratio="0.8" data-stellar-vertical-offset="0" data-background="" style="background-image: url('{{ asset('hotel_gallery/room_(5).jpg') }}'); background-size: cover; background-position: center center; opacity: 1 !important;"></div>
            <div class="container">
                <div class="page-cover text-center">
                    <h2 class="page-cover-tittle" style="color: #e77a3a;">About Us</h2>
                    <p style="color: white; font-size: 16px; margin-top: 15px; margin-bottom: 20px;">Discover our story, mission, and commitment to excellence</p>
                    <ol class="breadcrumb">
                        <li><a href="{{ url('/') }}">Home</a></li>
                        <li class="active">About</li>
                    </ol>
                </div>
            </div>
        </section>
        <!--================Breadcrumb Area =================-->
        
        <!--================ About Us Area  =================-->
        <section class="about_history_area section_gap" style="padding: 60px 0; background: #f8f9fa;">
            <div class="container">
                <!-- Section Title -->
                <div class="section_title text-center" style="margin-bottom: 50px;">
                    <h2 class="title_color" style="color: #e77a3a; font-size: 36px; margin-bottom: 15px; position: relative; display: inline-block; padding-bottom: 15px;">
                        About Us
                        <span style="position: absolute; bottom: 0; left: 50%; transform: translateX(-50%); width: 60px; height: 3px; background: #e77a3a; border-radius: 2px;"></span>
                    </h2>
                    <p style="font-size: 16px; color: #777; margin-top: 20px;">Welcome to Primeland Hotel, Comfort in every stay</p>
                </div>
                
                <!-- Main Content with Image -->
                <div class="row align-items-center mb-5" style="margin-bottom: 50px !important;">
                    <div class="col-lg-6 mb-4 mb-lg-0">
                        <div style="border-radius: 15px; overflow: hidden; box-shadow: 0 8px 30px rgba(0,0,0,0.12); position: relative;">
                            <div style="position: absolute; top: 20px; left: 20px; background: #e77a3a; color: #fff; padding: 8px 20px; border-radius: 25px; font-size: 13px; font-weight: 600; z-index: 2;">Premium Accommodation</div>
                            <img class="img-fluid" src="{{ asset('hotel_gallery/room_.jpg') }}" alt="PrimeLand Hotel" style="width: 100%; height: 450px; object-fit: cover;">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div style="padding-left: 20px;">
                            <div style="width: 50px; height: 4px; background: #e77a3a; margin-bottom: 20px; border-radius: 2px;"></div>
                            <h3 style="color: #222; font-size: 28px; font-weight: 700; margin-bottom: 20px;">Experience Luxury & Culture</h3>
                            <p style="color: #666; line-height: 1.95; font-size: 16px; margin-bottom: 20px;">
                                Our Hotel offers a state of the art accommodation mixed with a touch of Tanzanian culture. Located at a prime location of Moshi town, at the foothills of Mount Kilimanjaro only 1.5km from town center (approximately 7 minutes' drive from Moshi town center). Our hotel makes a delightful place to unwind and relax after or before Tanzania safari and Kilimanjaro trekking.
                            </p>
                            <p style="color: #666; line-height: 1.95; font-size: 16px; margin-bottom: 25px;">
                                Each of our room featuring cozy warm beds, air conditioning, Hot water showers, free toiletries, hair dryer, tea/coffee making facility, bottled mineral water, a mini refrigerator, a working desk, Free Wi-Fi, smart 4K flat TV screen. Our other popular services include a mix of local and international cuisine, drinks and swimming pool.
                            </p>
                            <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                                <div style="display: flex; align-items: center; color: #666; font-size: 14px;">
                                    <i class="lnr lnr-checkmark-circle" style="color: #e77a3a; font-size: 20px; margin-right: 8px;"></i>
                                    <span>Free Wi-Fi</span>
                                </div>
                                <div style="display: flex; align-items: center; color: #666; font-size: 14px;">
                                    <i class="lnr lnr-checkmark-circle" style="color: #e77a3a; font-size: 20px; margin-right: 8px;"></i>
                                    <span>Air Conditioning</span>
                                </div>
                                <div style="display: flex; align-items: center; color: #666; font-size: 14px;">
                                    <i class="lnr lnr-checkmark-circle" style="color: #e77a3a; font-size: 20px; margin-right: 8px;"></i>
                                    <span>Swimming Pool</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Divider -->
                <div style="text-align: center; margin: 40px 0;">
                    <div style="display: inline-block; width: 100px; height: 2px; background: linear-gradient(to right, transparent, #e77a3a, transparent);"></div>
                </div>
                
                <!-- Second Content Section -->
                <div class="row align-items-center" style="margin-bottom: 50px !important;">
                    <div class="col-lg-6 order-lg-2 mb-4 mb-lg-0">
                        <div style="border-radius: 15px; overflow: hidden; box-shadow: 0 8px 30px rgba(0,0,0,0.12); position: relative;">
                            <div style="position: absolute; top: 20px; right: 20px; background: #e77a3a; color: #fff; padding: 8px 20px; border-radius: 25px; font-size: 13px; font-weight: 600; z-index: 2;">Premium Services</div>
                            <img class="img-fluid" src="{{ asset('hotel_gallery/swimming floating tray_.jpg') }}" alt="PrimeLand Hotel Services" style="width: 100%; height: 450px; object-fit: cover;">
                        </div>
                    </div>
                    <div class="col-lg-6 order-lg-1">
                        <div style="padding-right: 20px;">
                            <div style="width: 50px; height: 4px; background: #e77a3a; margin-bottom: 20px; border-radius: 2px;"></div>
                            <h3 style="color: #222; font-size: 28px; font-weight: 700; margin-bottom: 20px;">Your Gateway to Adventure</h3>
                            <p style="color: #666; line-height: 1.95; font-size: 16px; margin-bottom: 25px;">
                                Our services are crafted with our guests in mind; that is to give travelers a beautiful place to relax & have good memories for a complete Tanzania unforgettable journey at reasonable rates. Being at the heart of Moshi- the base for most Kilimanjaro ascents, we are looking to make our guests experience the beauty of our home town, to have a sense of life at the very base of mount Kilimanjaro.
                            </p>
                            <a href="{{ route('booking.index') }}" class="btn theme_btn button_hover" style="margin-top: 10px;">Book Now</a>
                        </div>
                    </div>
                </div>
                
                <!-- Divider -->
                <div style="text-align: center; margin: 40px 0;">
                    <div style="display: inline-block; width: 100px; height: 2px; background: linear-gradient(to right, transparent, #e77a3a, transparent);"></div>
                </div>
                
                <!-- Mission, Vision, and Core Values Section -->
                <div class="row" style="margin-top: 20px;">
                    <div class="col-12">
                        <div class="section_title text-center" style="margin-bottom: 60px;">
                            <h2 class="title_color" style="color: #e77a3a; font-size: 36px; margin-bottom: 15px; position: relative; display: inline-block; padding-bottom: 15px;">
                                Our Mission, Vision & Core Values
                                <span style="position: absolute; bottom: 0; left: 50%; transform: translateX(-50%); width: 60px; height: 3px; background: #e77a3a; border-radius: 2px;"></span>
                            </h2>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Mission -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div style="background: #fff; border-radius: 15px; overflow: hidden; box-shadow: 0 8px 30px rgba(0,0,0,0.12); height: 100%; border-top: 4px solid #e77a3a;">
                            <div style="position: relative; height: 280px; overflow: hidden;">
                                <img src="{{ asset('hotel_gallery/reception_.jpg') }}" alt="Our Mission" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div style="padding: 35px 30px;">
                                <h3 style="color: #222; font-size: 24px; font-weight: 700; margin-bottom: 20px; position: relative; padding-bottom: 15px;">
                                    Our Mission
                                    <span style="position: absolute; bottom: 0; left: 0; width: 40px; height: 3px; background: #e77a3a; border-radius: 2px;"></span>
                                </h3>
                                <p style="color: #666; line-height: 1.9; font-size: 15px; margin: 0;">
                                    To provide exceptional hospitality experiences that blend modern comfort with authentic Tanzanian culture, creating unforgettable memories for our guests at the base of Mount Kilimanjaro.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Vision -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div style="background: #fff; border-radius: 15px; overflow: hidden; box-shadow: 0 8px 30px rgba(0,0,0,0.12); height: 100%; border-top: 4px solid #e77a3a;">
                            <div style="position: relative; height: 280px; overflow: hidden;">
                                <img src="{{ asset('hotel_gallery/night view_(1).jpg') }}" alt="Our Vision" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div style="padding: 35px 30px;">
                                <h3 style="color: #222; font-size: 24px; font-weight: 700; margin-bottom: 20px; position: relative; padding-bottom: 15px;">
                                    Our Vision
                                    <span style="position: absolute; bottom: 0; left: 0; width: 40px; height: 3px; background: #e77a3a; border-radius: 2px;"></span>
                                </h3>
                                <p style="color: #666; line-height: 1.9; font-size: 15px; margin: 0;">
                                    To be the premier destination hotel in Moshi, recognized for excellence in service, cultural authenticity, and as the preferred choice for travelers exploring Tanzania's natural wonders.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Core Values -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div style="background: #fff; border-radius: 15px; overflow: hidden; box-shadow: 0 8px 30px rgba(0,0,0,0.12); height: 100%; border-top: 4px solid #e77a3a;">
                            <div style="position: relative; height: 280px; overflow: hidden;">
                                <img src="{{ asset('hotel_gallery/restaurant_.jpg') }}" alt="Core Values" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div style="padding: 35px 30px;">
                                <h3 style="color: #222; font-size: 24px; font-weight: 700; margin-bottom: 20px; position: relative; padding-bottom: 15px;">
                                    Core Values
                                    <span style="position: absolute; bottom: 0; left: 0; width: 40px; height: 3px; background: #e77a3a; border-radius: 2px;"></span>
                                </h3>
                                <ul style="color: #666; line-height: 2.2; font-size: 15px; margin: 0; padding-left: 0; list-style: none;">
                                    <li style="margin-bottom: 14px; display: flex; align-items: center;">
                                        <i class="fa fa-check-circle" style="color: #e77a3a; margin-right: 12px; font-size: 18px;"></i>
                                        <span>Excellence in Service</span>
                                    </li>
                                    <li style="margin-bottom: 14px; display: flex; align-items: center;">
                                        <i class="fa fa-check-circle" style="color: #e77a3a; margin-right: 12px; font-size: 18px;"></i>
                                        <span>Cultural Authenticity</span>
                                    </li>
                                    <li style="margin-bottom: 14px; display: flex; align-items: center;">
                                        <i class="fa fa-check-circle" style="color: #e77a3a; margin-right: 12px; font-size: 18px;"></i>
                                        <span>Guest Satisfaction</span>
                                    </li>
                                    <li style="margin-bottom: 14px; display: flex; align-items: center;">
                                        <i class="fa fa-check-circle" style="color: #e77a3a; margin-right: 12px; font-size: 18px;"></i>
                                        <span>Environmental Responsibility</span>
                                    </li>
                                    <li style="display: flex; align-items: center;">
                                        <i class="fa fa-check-circle" style="color: #e77a3a; margin-right: 12px; font-size: 18px;"></i>
                                        <span>Community Engagement</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--================ About Us Area  =================-->
        
        <style>
            /* Responsive Styles for About Us Page */
            
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
                    margin-top: 10px !important;
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
            }
            
            /* About Content Area Responsive */
            @media (max-width: 991px) {
                .about_history_area {
                    padding: 50px 0 !important;
                }
                
                .about_history_area .section_title {
                    margin-bottom: 40px !important;
                }
                
                .about_history_area .section_title h2 {
                    font-size: 28px !important;
                }
                
                .about_history_area img {
                    height: 300px !important;
                }
                
                .about_history_area div[style*="padding-left: 20px"],
                .about_history_area div[style*="padding-right: 20px"] {
                    padding-left: 0 !important;
                    padding-right: 0 !important;
                    margin-top: 30px;
                }
                
                .about_history_area h3 {
                    font-size: 24px !important;
                }
                
                .about_history_area p {
                    font-size: 15px !important;
                }
            }
            
            @media (max-width: 768px) {
                .about_history_area {
                    padding: 40px 0 !important;
                }
                
                .about_history_area .section_title {
                    margin-bottom: 30px !important;
                }
                
                .about_history_area .section_title h2 {
                    font-size: 24px !important;
                }
                
                .about_history_area .section_title p {
                    font-size: 14px !important;
                }
                
                .about_history_area img {
                    height: 250px !important;
                }
                
                .about_history_area h3 {
                    font-size: 22px !important;
                    margin-bottom: 15px !important;
                }
                
                .about_history_area p {
                    font-size: 14px !important;
                    line-height: 1.8 !important;
                }
                
                .about_history_area .row {
                    margin-bottom: 30px !important;
                }
                
                /* Mission/Vision/Core Values Cards */
                .about_history_area .col-lg-4 {
                    margin-bottom: 25px !important;
                }
                
                .about_history_area .col-lg-4 div[style*="height: 280px"] {
                    height: 200px !important;
                }
                
                .about_history_area .col-lg-4 div[style*="padding: 35px 30px"] {
                    padding: 25px 20px !important;
                }
                
                .about_history_area .col-lg-4 h3 {
                    font-size: 20px !important;
                    margin-bottom: 15px !important;
                }
                
                .about_history_area .col-lg-4 p,
                .about_history_area .col-lg-4 ul {
                    font-size: 14px !important;
                }
            }
            
            @media (max-width: 480px) {
                .about_history_area {
                    padding: 30px 0 !important;
                }
                
                .about_history_area .section_title h2 {
                    font-size: 22px !important;
                }
                
                .about_history_area .section_title p {
                    font-size: 13px !important;
                }
                
                .about_history_area img {
                    height: 200px !important;
                }
                
                .about_history_area h3 {
                    font-size: 20px !important;
                }
                
                .about_history_area p {
                    font-size: 13px !important;
                }
                
                .about_history_area .col-lg-4 div[style*="height: 280px"] {
                    height: 180px !important;
                }
                
                .about_history_area .col-lg-4 div[style*="padding: 35px 30px"] {
                    padding: 20px 15px !important;
                }
                
                .about_history_area .col-lg-4 h3 {
                    font-size: 18px !important;
                }
                
                .about_history_area .col-lg-4 p,
                .about_history_area .col-lg-4 ul {
                    font-size: 13px !important;
                }
            }
        </style>
        
        @include('landing_page_views.partials.royal-footer')
        
        @include('landing_page_views.partials.royal-scripts')
        
        <!-- Loading spinner JavaScript removed -->
        
        @include('landing_page_views.partials.chat-widgets')
    </body>
</html>
