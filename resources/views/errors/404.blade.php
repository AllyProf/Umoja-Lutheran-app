@php
    // Check if user is authenticated (dashboard user)
    $isDashboard = auth()->guard('staff')->check() || auth()->guard('guest')->check();
    
    // Also check if the URL path suggests it's a dashboard route
    $path = request()->path();
    $isDashboardPath = str_starts_with($path, 'manager/') || 
                       str_starts_with($path, 'super-admin/') || 
                       str_starts_with($path, 'reception/') || 
                       str_starts_with($path, 'customer/') ||
                       str_starts_with($path, 'admin/');
    
    // Use dashboard layout if user is authenticated OR if path suggests dashboard route
    $isDashboard = $isDashboard || $isDashboardPath;
@endphp

@if($isDashboard)
    @php
        // For authenticated users, render dashboard 404
        $currentUser = auth()->guard('staff')->user() ?? auth()->guard('guest')->user();
        $userRole = null;
        $role = null;
        $userName = null;
        
        if ($currentUser) {
            $userName = $currentUser->name ?? ($currentUser->first_name ?? 'User');
            if ($currentUser instanceof \App\Models\Staff) {
                if ($currentUser->isSuperAdmin()) {
                    $userRole = 'super_admin';
                    $role = 'super_admin';
                } elseif ($currentUser->isManager()) {
                    $userRole = 'manager';
                    $role = 'manager';
                } elseif ($currentUser->isReception()) {
                    $userRole = 'reception';
                    $role = 'reception';
                }
            } else {
                $userRole = 'customer';
                $role = 'customer';
            }
        } else {
            // If not authenticated but path suggests dashboard, determine role from path
            if (str_starts_with($path, 'super-admin/')) {
                $userRole = 'super_admin';
                $role = 'super_admin';
            } elseif (str_starts_with($path, 'manager/') || str_starts_with($path, 'admin/')) {
                $userRole = 'manager';
                $role = 'manager';
            } elseif (str_starts_with($path, 'reception/')) {
                $userRole = 'reception';
                $role = 'reception';
            } elseif (str_starts_with($path, 'customer/')) {
                $userRole = 'customer';
                $role = 'customer';
            }
        }
        
        // Render dashboard 404 view and exit to prevent rendering landing page
        echo view('errors.404-dashboard', compact('userRole', 'role', 'userName'))->render();
        exit;
    @endphp
@endif

{{-- Landing Page 404 (following about-us page structure) --}}
<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="icon" href="{{ asset('royal-master/image/logo/Logo.png') }}" type="image/png">
        <title>404 - Page Not Found | PrimeLand Hotel</title>
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
            }
            
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
        </style>
    </head>
    <body>
        <!-- Loading spinner removed -->
        <!--================Header Area =================-->
        <header class="header_area">
            <div class="container">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <!-- Brand and toggle get grouped for better mobile display -->
                    <a class="navbar-brand logo_h" href="{{ url('/') }}">
                        <img src="{{ asset('royal-master/image/logo/Logo.png') }}" alt="PrimeLand Hotel Logo" style="height: 100px; max-width: 400px;">
                    </a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse offset" id="navbarSupportedContent">
                        <ul class="nav navbar-nav menu_nav ml-auto">
                            <li class="nav-item"><a class="nav-link" href="{{ url('/') }}">Home</a></li> 
                            <li class="nav-item"><a class="nav-link" href="{{ url('/about-us') }}">About us</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('booking.index') }}">Book Now</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ url('/gallery') }}">Gallery</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ url('/services') }}">Services</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ url('/contact') }}">Contact</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ url('/login') }}">Login</a></li>
                        </ul>
                    </div> 
                </nav>
            </div>
        </header>
        <!--================Header Area =================-->
        
        <!--================Breadcrumb Area =================-->
        <section class="breadcrumb_area" style="padding: 250px 0px 120px; min-height: 500px;">
            <div class="overlay bg-parallax" data-stellar-ratio="0.8" data-stellar-vertical-offset="0" data-background="" style="background-image: url('{{ asset("landing_page_backround_images/hotel view_.jpg") }}'); background-size: cover; background-position: center center; opacity: 1 !important;"></div>
            <div class="container">
                <div class="page-cover text-center">
                    <h2 class="page-cover-tittle" style="color: #e77a3a;">404 - Page Not Found</h2>
                    <p style="color: white; font-size: 16px; margin-top: 15px; margin-bottom: 20px;">The page you're looking for doesn't exist</p>
                    <ol class="breadcrumb">
                        <li><a href="{{ url('/') }}">Home</a></li>
                        <li class="active">404</li>
                    </ol>
                </div>
            </div>
        </section>
        <!--================Breadcrumb Area End =================-->
        
        <!--================404 Error Area =================-->
        <section class="about_history_area section_gap" style="padding: 60px 0; background: #f8f9fa;">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-12">
                        <div class="text-center" style="padding: 60px 40px;">
                            <div style="font-size: 150px; color: #e77a3a; margin-bottom: 30px; font-weight: 700; line-height: 1;">
                                404
                            </div>
                            <h2 style="color: #1a365d; margin-bottom: 20px; font-weight: 600; font-size: 36px;">Page Not Found</h2>
                            <p style="font-size: 18px; color: #666; margin-bottom: 40px; line-height: 1.8; max-width: 700px; margin-left: auto; margin-right: auto;">
                                Oops! The page you're looking for seems to have checked out. 
                                It might have been moved, deleted, or the URL might be incorrect.
                            </p>
                            <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap; margin-top: 40px;">
                                <a href="{{ url('/') }}" class="btn theme_btn button_hover" style="padding: 15px 40px; border-radius: 50px; text-decoration: none; display: inline-block; font-size: 16px; font-weight: 600;">
                                    <i class="fa fa-home"></i> Go to Homepage
                                </a>
                                <a href="javascript:history.back()" class="btn" style="padding: 15px 40px; border-radius: 50px; text-decoration: none; display: inline-block; background: #1a365d; color: white; border: 2px solid #1a365d; font-size: 16px; font-weight: 600;">
                                    <i class="fa fa-arrow-left"></i> Go Back
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--================404 Error Area End =================-->
        
        <!--================Footer Area =================-->
        @include('landing_page_views.partials.royal-footer')
        <!--================Footer Area End =================-->
        
        <!-- Scripts -->
        @include('landing_page_views.partials.royal-scripts')
    </body>
</html>
