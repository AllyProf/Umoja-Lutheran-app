<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="icon" href="{{ asset('royal-master/image/logo/Logo.png') }}" type="image/png">
        <title>Online Booking Coming Soon - PrimeLand Hotel</title>
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
            
            /* Coming Soon Section Styles */
            .about_history_area a:hover {
                color: #e77a3a !important;
            }
            
            .about_history_area a[href^="tel:"]:hover,
            .about_history_area a[href^="mailto:"]:hover,
            .about_history_area a[href^="https:"]:hover {
                color: #e77a3a !important;
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
                
                .about_history_area .section_title h2 {
                    font-size: 28px !important;
                }
                
                .about_history_area h3 {
                    font-size: 24px !important;
                }
                
                .about_history_area .col-md-6 {
                    margin-bottom: 20px !important;
                }
            }
            /* Loading spinner removed */
        </style>
    </head>
    <body>
        <!-- Loading spinner removed -->
        @include('landing_page_views.partials.royal-header')
        
        <!--================Breadcrumb Area =================-->
        <section class="breadcrumb_area" style="padding: 250px 0px 120px; min-height: 500px; position: relative;">
            <div class="overlay bg-parallax" data-stellar-ratio="0.8" data-stellar-vertical-offset="0" data-background="" style="background-image: url('{{ asset('hotel_gallery/coffee_.jpg') }}'); background-size: cover; background-position: center center; opacity: 1 !important;"></div>
            <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.5); z-index: 0;"></div>
            <div class="container" style="position: relative; z-index: 1;">
                <div class="page-cover text-center">
                    <h2 class="page-cover-tittle" style="color: #e77a3a; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">Online Booking Coming Soon</h2>
                    <p style="color: white; font-size: 16px; margin-top: 15px; margin-bottom: 20px; text-shadow: 1px 1px 3px rgba(0,0,0,0.7);">We're working on our online booking system</p>
                    <ol class="breadcrumb" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">
                        <li><a href="{{ url('/') }}" style="color: rgba(255,255,255,0.9);">Home</a></li>
                        <li class="active" style="color: rgba(255,255,255,0.9);">Book Now</li>
                    </ol>
                </div>
            </div>
        </section>
        <!--================Breadcrumb Area =================-->
        
        <!--================Coming Soon Section =================-->
        <section class="about_history_area section_gap" style="padding: 60px 0; background: #f8f9fa;">
            <div class="container">
                <!-- Section Title -->
                <div class="section_title text-center" style="margin-bottom: 50px;">
                    <h2 class="title_color" style="color: #e77a3a; font-size: 36px; margin-bottom: 15px; position: relative; display: inline-block; padding-bottom: 15px;">
                        Online Booking Coming Soon
                        <span style="position: absolute; bottom: 0; left: 50%; transform: translateX(-50%); width: 60px; height: 3px; background: #e77a3a; border-radius: 2px;"></span>
                    </h2>
                    <p style="font-size: 16px; color: #777; margin-top: 20px;">We're working on our online booking system to provide you with a seamless reservation experience</p>
                </div>
                
                <!-- Main Content with Image -->
                <div class="row align-items-center mb-5" style="margin-bottom: 50px !important;">
                    <div class="col-lg-6 mb-4 mb-lg-0">
                        <div style="border-radius: 15px; overflow: hidden; box-shadow: 0 8px 30px rgba(0,0,0,0.12); position: relative;">
                            <div style="position: absolute; top: 20px; left: 20px; background: #e77a3a; color: #fff; padding: 8px 20px; border-radius: 25px; font-size: 13px; font-weight: 600; z-index: 2;">Coming Soon</div>
                            <img class="img-fluid" src="{{ asset('hotel_gallery/room_(5).jpg') }}" alt="PrimeLand Hotel" style="width: 100%; height: 450px; object-fit: cover;">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div style="padding-left: 20px;">
                            <div style="width: 50px; height: 4px; background: #e77a3a; margin-bottom: 20px; border-radius: 2px;"></div>
                            <h3 style="color: #222; font-size: 28px; font-weight: 700; margin-bottom: 20px;">Book Your Stay Directly</h3>
                            <p style="color: #666; line-height: 1.95; font-size: 16px; margin-bottom: 20px;">
                                While we're building our online booking platform, we're here to help you make your reservation directly. Our friendly staff is available 24/7 to assist you with booking your perfect stay at PrimeLand Hotel.
                            </p>
                            <p style="color: #666; line-height: 1.95; font-size: 16px; margin-bottom: 25px;">
                                Experience luxury accommodation at the base of Mount Kilimanjaro. Contact us today to secure your room and start planning your unforgettable Tanzanian adventure.
                            </p>
                            <div style="display: flex; gap: 15px; flex-wrap: wrap; margin-bottom: 25px;">
                                <div style="display: flex; align-items: center; color: #666; font-size: 14px;">
                                    <i class="lnr lnr-checkmark-circle" style="color: #e77a3a; font-size: 20px; margin-right: 8px;"></i>
                                    <span>24/7 Support</span>
                                </div>
                                <div style="display: flex; align-items: center; color: #666; font-size: 14px;">
                                    <i class="lnr lnr-checkmark-circle" style="color: #e77a3a; font-size: 20px; margin-right: 8px;"></i>
                                    <span>Instant Confirmation</span>
                                </div>
                                <div style="display: flex; align-items: center; color: #666; font-size: 14px;">
                                    <i class="lnr lnr-checkmark-circle" style="color: #e77a3a; font-size: 20px; margin-right: 8px;"></i>
                                    <span>Best Rates</span>
                                </div>
                            </div>
                            <a href="{{ url('/contact') }}" class="btn theme_btn button_hover" style="margin-top: 10px;">Contact Us Now</a>
                        </div>
                    </div>
                </div>
                
                <!-- Divider -->
                <div style="text-align: center; margin: 40px 0;">
                    <div style="display: inline-block; width: 100px; height: 2px; background: linear-gradient(to right, transparent, #e77a3a, transparent);"></div>
                </div>
                
                <!-- Contact Information Section -->
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <div style="background: #fff; border-radius: 15px; overflow: hidden; box-shadow: 0 8px 30px rgba(0,0,0,0.12); padding: 40px;">
                            <h3 style="color: #222; font-size: 24px; font-weight: 700; margin-bottom: 30px; text-align: center; position: relative; padding-bottom: 15px;">
                                Contact Us to Book
                                <span style="position: absolute; bottom: 0; left: 50%; transform: translateX(-50%); width: 60px; height: 3px; background: #e77a3a; border-radius: 2px;"></span>
                            </h3>
                            
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div style="display: flex; align-items: flex-start;">
                                        <div style="background: #e77a3a; color: #fff; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px; flex-shrink: 0;">
                                            <i class="fa fa-phone" style="font-size: 20px;"></i>
                                        </div>
                                        <div>
                                            <h4 style="color: #222; font-size: 18px; font-weight: 600; margin-bottom: 8px;">Phone</h4>
                                            <p style="color: #666; margin: 0; line-height: 1.8;">
                                                <a href="tel:+255677155156" style="color: #666; text-decoration: none; transition: color 0.3s;">+255 677-155-156</a><br>
                                                <a href="tel:+255677155157" style="color: #666; text-decoration: none; transition: color 0.3s;">+255 677-155-157</a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-4">
                                    <div style="display: flex; align-items: flex-start;">
                                        <div style="background: #e77a3a; color: #fff; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px; flex-shrink: 0;">
                                            <i class="fa fa-whatsapp" style="font-size: 20px;"></i>
                                        </div>
                                        <div>
                                            <h4 style="color: #222; font-size: 18px; font-weight: 600; margin-bottom: 8px;">WhatsApp</h4>
                                            <p style="color: #666; margin: 0; line-height: 1.8;">
                                                <a href="https://wa.me/255677155156" style="color: #666; text-decoration: none; transition: color 0.3s;">+255 677-155-156</a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-4">
                                    <div style="display: flex; align-items: flex-start;">
                                        <div style="background: #e77a3a; color: #fff; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px; flex-shrink: 0;">
                                            <i class="fa fa-envelope" style="font-size: 20px;"></i>
                                        </div>
                                        <div>
                                            <h4 style="color: #222; font-size: 18px; font-weight: 600; margin-bottom: 8px;">Email</h4>
                                            <p style="color: #666; margin: 0; line-height: 1.8;">
                                                <a href="mailto:info@primelandhotel.co.tz" style="color: #666; text-decoration: none; transition: color 0.3s;">info@primelandhotel.co.tz</a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-4">
                                    <div style="display: flex; align-items: flex-start;">
                                        <div style="background: #e77a3a; color: #fff; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px; flex-shrink: 0;">
                                            <i class="fa fa-clock-o" style="font-size: 20px;"></i>
                                        </div>
                                        <div>
                                            <h4 style="color: #222; font-size: 18px; font-weight: 600; margin-bottom: 8px;">Front Desk</h4>
                                            <p style="color: #666; margin: 0; line-height: 1.8;">Available 24/7</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div style="text-align: center; margin-top: 30px;">
                                <a href="{{ url('/') }}" class="btn theme_btn button_hover">
                                    <i class="fa fa-arrow-left"></i> Back to Homepage
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--================Coming Soon Section =================-->
        
        @include('landing_page_views.partials.royal-footer')
        
        @include('landing_page_views.partials.royal-scripts')
        
        @include('landing_page_views.partials.chat-widgets')
    </body>
</html>
