<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="icon" href="{{ asset('royal-master/image/logo/Logo.png') }}" type="image/png">
        <title>Contact Us - PrimeLand Hotel</title>
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
            /* Loading spinner removed */
        </style>
    </head>
    <body>
        <!-- Loading spinner removed -->
        @include('landing_page_views.partials.royal-header')
        
        <!--================Breadcrumb Area =================-->
        <section class="breadcrumb_area" style="padding: 250px 0px 120px; min-height: 500px; position: relative;">
            <div class="overlay bg-parallax" data-stellar-ratio="0.8" data-stellar-vertical-offset="0" data-background="" style="background-image: url('{{ asset('hotel_gallery/swimming view_(1).jpg') }}'); background-size: cover; background-position: center center; opacity: 1 !important;"></div>
            <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.6); z-index: 0;"></div>
            <div class="container" style="position: relative; z-index: 1;">
                <div class="page-cover text-center">
                    <h2 class="page-cover-tittle" style="color: #e77a3a;">Contact Us</h2>
                    <p style="color: white; font-size: 16px; margin-top: 15px; margin-bottom: 20px;">Get in touch with us for reservations and inquiries</p>
                    <ol class="breadcrumb">
                        <li><a href="{{ url('/') }}">Home</a></li>
                        <li class="active">Contact Us</li>
                    </ol>
                </div>
            </div>
        </section>
        <!--================Breadcrumb Area =================-->
        
        <!--================Contact Area =================-->
        <section class="contact_area section_gap" style="padding: 60px 0;">
            <div class="container">
                <!-- Contact Details and Map in One Row -->
                <div class="row">
                    <!-- Left Column: Contact Details -->
                    <div class="col-lg-6 col-md-6 mb-4 mb-lg-0">
                        <h3 class="contact-title" style="color: #222; font-size: 22px; font-weight: 700; margin-bottom: 30px; padding-bottom: 15px; border-bottom: 2px solid #e77a3a;">Contact Information</h3>
                        
                        <!-- First Row: Location and Mobile/WhatsApp -->
                        <div class="row" style="margin-bottom: 30px;">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <h4 style="color: #e77a3a; font-size: 16px; font-weight: 600; margin-bottom: 10px; display: flex; align-items: center;">
                                    <i class="lnr lnr-map-marker" style="margin-right: 10px; font-size: 18px;"></i>
                                    Location
                                </h4>
                                <p style="color: #666; line-height: 1.8; margin: 0; padding-left: 28px;">Sokoine Road<br>Moshi Kilimanjaro<br>Tanzania</p>
                            </div>
                            
                            <div class="col-md-6">
                                <h4 style="color: #e77a3a; font-size: 16px; font-weight: 600; margin-bottom: 10px; display: flex; align-items: center;">
                                    <i class="lnr lnr-phone-handset" style="margin-right: 10px; font-size: 18px;"></i>
                                    Mobile/WhatsApp
                                </h4>
                                <p style="color: #666; line-height: 1.8; margin: 0; padding-left: 28px;">
                                    <a href="tel:+255677155156" style="color: #666; text-decoration: none; transition: color 0.3s;">0677-155-156</a><br>
                                    <a href="tel:+255677155157" style="color: #666; text-decoration: none; transition: color 0.3s;">+255 677-155-157</a>
                                </p>
                            </div>
                        </div>
                        
                        <div style="margin-bottom: 30px;">
                            <h4 style="color: #e77a3a; font-size: 16px; font-weight: 600; margin-bottom: 10px; display: flex; align-items: center;">
                                <i class="lnr lnr-envelope" style="margin-right: 10px; font-size: 18px;"></i>
                                Email
                            </h4>
                            <p style="color: #666; line-height: 1.8; margin: 0; padding-left: 28px;">
                                <a href="mailto:info@primelandhotel.co.tz" style="color: #666; text-decoration: none; transition: color 0.3s;">info@primelandhotel.co.tz</a><br>
                                <a href="mailto:infoprimelandhotel@gmail.com" style="color: #666; text-decoration: none; transition: color 0.3s;">infoprimelandhotel@gmail.com</a>
                            </p>
                        </div>
                        
                        <div>
                            <h4 style="color: #e77a3a; font-size: 16px; font-weight: 600; margin-bottom: 10px; display: flex; align-items: center;">
                                <i class="lnr lnr-clock" style="margin-right: 10px; font-size: 18px;"></i>
                                Business Hours
                            </h4>
                            <p style="color: #666; line-height: 1.8; margin: 0; padding-left: 28px;">
                                Monday - Sunday: 24 Hours<br>
                                Always available for your convenience
                            </p>
                        </div>
                    </div>
                    
                    <!-- Right Column: Map -->
                    <div class="col-lg-6 col-md-6">
                        <h3 class="contact-title" style="color: #222; font-size: 22px; font-weight: 700; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 2px solid #e77a3a;">Our Prime Location</h3>
                        <div class="map-container">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d4366.224696276086!2d37.33295041043378!3d-3.3290562106369452!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2stz!4v1763832924736!5m2!1sen!2stz" width="100%" height="400" style="border:0; border-radius: 8px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--================Contact Area =================-->
        
        <style>
            .contact_area a:hover {
                color: #e77a3a !important;
            }
            
            .contact_area a[href^="tel:"]:hover,
            .contact_area a[href^="mailto:"]:hover {
                color: #e77a3a !important;
                padding-left: 5px;
            }
            
            /* Responsive Styles */
            
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
            
            /* Contact Area Responsive */
            @media (max-width: 991px) {
                .contact_area {
                    padding: 40px 0 !important;
                }
                
                .contact_area h3 {
                    font-size: 20px !important;
                    margin-bottom: 20px !important;
                }
                
                .contact_area h4 {
                    font-size: 15px !important;
                }
                
                .contact_area iframe {
                    height: 350px !important;
                }
                
                .contact_area .row > .col-lg-6:first-child {
                    margin-bottom: 40px;
                }
            }
            
            @media (max-width: 768px) {
                .contact_area {
                    padding: 30px 0 !important;
                }
                
                .contact_area h3 {
                    font-size: 18px !important;
                    margin-bottom: 20px !important;
                }
                
                .contact_area h4 {
                    font-size: 14px !important;
                    margin-bottom: 8px !important;
                }
                
                .contact_area p {
                    font-size: 14px !important;
                    line-height: 1.6 !important;
                }
                
                .contact_area iframe {
                    height: 300px !important;
                }
                
                .contact_area .row > div {
                    margin-bottom: 30px !important;
                }
                
                .contact_area .row > div[class*="col-md-6"] {
                    margin-bottom: 20px !important;
                }
            }
            
            @media (max-width: 576px) {
                .contact_area {
                    padding: 25px 0 !important;
                }
                
                .contact_area .contact-title {
                    font-size: 16px !important;
                    margin-bottom: 15px !important;
                    padding-bottom: 10px !important;
                }
                
                .contact_area h4 {
                    font-size: 13px !important;
                }
                
                .contact_area p {
                    font-size: 13px !important;
                    padding-left: 25px !important;
                }
                
                .contact_area .map-container iframe {
                    height: 250px !important;
                }
                
                .contact_area .row > div[class*="col-"] {
                    margin-bottom: 25px !important;
                }
                
                .contact_area .row > div[class*="col-md-6"] {
                    margin-bottom: 15px !important;
                }
            }
        </style>
        
        @include('landing_page_views.partials.royal-footer')
        
        <!--================Contact Success and Error message Area =================-->
        <div id="success" class="modal modal-message fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i class="fa fa-close"></i>
                        </button>
                        <h2>Thank you</h2>
                        <p>Your message is successfully sent...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modals error -->
        <div id="error" class="modal modal-message fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i class="fa fa-close"></i>
                        </button>
                        <h2>Sorry !</h2>
                        <p> Something went wrong </p>
                    </div>
                </div>
            </div>
        </div>
        <!--================End Contact Success and Error message Area =================-->
        
        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="{{ asset('royal-master/js/jquery-3.2.1.min.js') }}"></script>
        <script src="{{ asset('royal-master/js/popper.js') }}"></script>
        <script src="{{ asset('royal-master/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('royal-master/vendors/owl-carousel/owl.carousel.min.js') }}"></script>
        <script src="{{ asset('royal-master/js/jquery.ajaxchimp.min.js') }}"></script>
        <script src="{{ asset('royal-master/vendors/bootstrap-datepicker/bootstrap-datetimepicker.min.js') }}"></script>
        <script src="{{ asset('royal-master/vendors/nice-select/js/jquery.nice-select.js') }}"></script>
        <script src="{{ asset('royal-master/js/mail-script.js') }}"></script>
        <script src="{{ asset('royal-master/js/stellar.js') }}"></script>
        <script src="{{ asset('royal-master/vendors/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
        <script src="{{ asset('royal-master/vendors/isotope/isotope-min.js') }}"></script>
        <script src="{{ asset('royal-master/vendors/lightbox/simpleLightbox.min.js') }}"></script>
        <!--gmaps Js-->
        <!-- Note: To remove "For development purposes only" watermark, enable billing in Google Cloud Console -->
        <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}"></script>
        <script src="{{ asset('royal-master/js/gmaps.min.js') }}"></script>
        <!-- contact js -->
        <script src="{{ asset('royal-master/js/jquery.form.js') }}"></script>
        <script src="{{ asset('royal-master/js/jquery.validate.min.js') }}"></script>
        <script src="{{ asset('royal-master/js/contact.js') }}"></script>
        <script src="{{ asset('royal-master/js/custom.js') }}"></script>
        
        <script>
        // Mobile Menu Improvements
        $(document).ready(function() {
            // Close menu when clicking overlay
            $('.navbar-collapse').on('click', function(e) {
                if (e.target === this) {
                    $('.navbar-toggler').click();
                }
            });
            
            // Close menu when clicking a nav link
            $('.navbar-nav .nav-link').on('click', function() {
                if (window.innerWidth < 768) {
                    $('.navbar-collapse').collapse('hide');
                }
            });
            
            // Update aria-expanded on collapse events
            $('#navbarSupportedContent').on('show.bs.collapse', function() {
                $('.navbar-toggler').attr('aria-expanded', 'true');
            });
            
            $('#navbarSupportedContent').on('hide.bs.collapse', function() {
                $('.navbar-toggler').attr('aria-expanded', 'false');
            });
        });
        </script>
        
        <!-- Loading spinner JavaScript removed -->
        
        @include('landing_page_views.partials.chat-widgets')
    </body>
</html>


