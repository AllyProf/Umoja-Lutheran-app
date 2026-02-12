<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="icon" href="{{ asset('royal-master/image/logo/Logo.png') }}" type="image/png">
        <title>Gallery - PrimeLand Hotel</title>
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="{{ asset('royal-master/css/bootstrap.css') }}">
        <link rel="stylesheet" href="{{ asset('royal-master/vendors/linericon/style.css') }}">
        <link rel="stylesheet" href="{{ asset('royal-master/css/font-awesome.min.css') }}">
        <link rel="stylesheet" href="{{ asset('royal-master/vendors/owl-carousel/owl.carousel.min.css') }}">
        <link rel="stylesheet" href="{{ asset('royal-master/vendors/nice-select/css/nice-select.css') }}">
        <!-- main css -->
        <link rel="stylesheet" href="{{ asset('royal-master/css/style.css') }}">
        <link rel="stylesheet" href="{{ asset('royal-master/css/responsive.css') }}">
        <!-- Lightbox CSS -->
        <link rel="stylesheet" href="{{ asset('royal-master/vendors/lightbox/simpleLightbox.css') }}">
        
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
            
            /* Professional Gallery Styles */
            /* Professional Gallery Styles */
            .gallery-page {
                padding: 40px 0 40px;
                background: #f8f9fa;
            }
            
            
            .gallery-filter {
                text-align: center;
                margin-bottom: 30px;
                padding: 0;
                display: flex;
                justify-content: center;
                flex-wrap: wrap;
                gap: 12px;
            }
            
            .gallery-filter button {
                background: #fff;
                border: 2px solid #e0e0e0;
                color: #666;
                padding: 14px 32px;
                margin: 0;
                border-radius: 8px;
                font-weight: 600;
                font-size: 15px;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                cursor: pointer;
                outline: none;
                position: relative;
                overflow: hidden;
                text-transform: capitalize;
                letter-spacing: 0.3px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            }
            
            .gallery-filter button::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
                transition: left 0.5s ease;
            }
            
            .gallery-filter button:hover::before {
                left: 100%;
            }
            
            .gallery-filter button:focus {
                outline: none;
                box-shadow: 0 0 0 3px rgba(231, 122, 58, 0.15);
            }
            
            .gallery-filter button:hover {
                background: #f8f9fa;
                border-color: #e77a3a;
                color: #e77a3a;
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(231, 122, 58, 0.2);
            }
            
            .gallery-filter button.active {
                background: linear-gradient(135deg, #e77a3a 0%, #d66a2a 100%);
                border-color: #e77a3a;
                color: #fff;
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(231, 122, 58, 0.4);
                font-weight: 700;
            }
            
            .gallery-filter button.active:hover {
                background: linear-gradient(135deg, #d66a2a 0%, #c55a1a 100%);
                box-shadow: 0 8px 25px rgba(231, 122, 58, 0.5);
            }
            
            .gallery-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                gap: 25px;
                padding: 20px 0;
            }
            
            .gallery-item {
                position: relative;
                overflow: hidden;
                border-radius: 12px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                background: white;
                cursor: pointer;
                aspect-ratio: 4/3;
                display: block;
            }
            
            .gallery-item:hover {
                transform: translateY(-10px);
                box-shadow: 0 12px 30px rgba(0,0,0,0.2);
            }
            
            .gallery-item img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform 0.5s ease;
                display: block;
            }
            
            .gallery-item:hover img {
                transform: scale(1.1);
            }
            
            .gallery-overlay {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.7) 100%);
                opacity: 1;
                transition: opacity 0.3s ease;
                display: flex;
                align-items: flex-end;
                padding: 20px;
            }
            
            .gallery-overlay-content {
                color: white;
                width: 100%;
            }
            
            .gallery-overlay-content h4 {
                font-size: 18px;
                font-weight: 600;
                margin: 0 0 5px 0;
                text-transform: capitalize;
            }
            
            .gallery-overlay-content p {
                font-size: 14px;
                margin: 0;
                opacity: 0.9;
            }
            
            .gallery-icon {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 60px;
                height: 60px;
                background: rgba(231, 122, 58, 0.9);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                opacity: 0;
                transition: all 0.3s ease;
                z-index: 10;
            }
            
            .gallery-item:hover .gallery-icon {
                opacity: 1;
            }
            
            .gallery-icon i {
                color: white;
                font-size: 24px;
            }
            
            /* Responsive Design */
            
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
                }
                
                .gallery-page {
                    padding: 30px 0 !important;
                }
                
                .gallery-grid {
                    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                    gap: 15px;
                }
                
                .gallery-filter {
                    gap: 8px;
                    padding: 15px 0;
                }
                
                .gallery-filter button {
                    padding: 12px 24px;
                    font-size: 14px;
                }
                
                .gallery-overlay-content h4 {
                    font-size: 16px !important;
                }
                
                .gallery-overlay-content p {
                    font-size: 13px !important;
                }
            }
            
            @media (max-width: 576px) {
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
                
                .gallery-page {
                    padding: 25px 0 !important;
                }
                
                .gallery-grid {
                    grid-template-columns: 1fr;
                    gap: 12px;
                }
                
                .gallery-filter {
                    gap: 6px;
                    padding: 10px 0;
                }
                
                .gallery-filter button {
                    padding: 10px 18px;
                    font-size: 13px;
                }
                
                .gallery-icon {
                    width: 50px !important;
                    height: 50px !important;
                }
                
                .gallery-icon i {
                    font-size: 20px !important;
                }
                
                .gallery-overlay {
                    padding: 15px !important;
                }
                
                .gallery-overlay-content h4 {
                    font-size: 15px !important;
                }
                
                .gallery-overlay-content p {
                    font-size: 12px !important;
                }
            }
            
            /* Loading spinner removed */
            
            .gallery-item img {
                opacity: 1;
                transition: opacity 0.3s ease;
            }
            
            .gallery-item img.loaded {
                opacity: 1;
            }
            
            /* Loading Animation */
            .gallery-item {
                animation: fadeInUp 0.6s ease forwards;
                opacity: 1;
            }
            
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .gallery-item:nth-child(1) { animation-delay: 0.1s; }
            .gallery-item:nth-child(2) { animation-delay: 0.2s; }
            .gallery-item:nth-child(3) { animation-delay: 0.3s; }
            .gallery-item:nth-child(4) { animation-delay: 0.4s; }
            .gallery-item:nth-child(5) { animation-delay: 0.5s; }
            .gallery-item:nth-child(6) { animation-delay: 0.6s; }
            .gallery-item:nth-child(7) { animation-delay: 0.7s; }
            .gallery-item:nth-child(8) { animation-delay: 0.8s; }
            .gallery-item:nth-child(9) { animation-delay: 0.9s; }
            .gallery-item:nth-child(10) { animation-delay: 1.0s; }
        </style>
    </head>
    <body>
        @include('landing_page_views.partials.royal-header')
        
        <!--================Breadcrumb Area =================-->
        <section class="breadcrumb_area" style="padding: 250px 0px 120px; min-height: 500px; position: relative;">
            <div class="overlay bg-parallax" data-stellar-ratio="0.8" data-stellar-vertical-offset="0" data-background="" style="background-image: url('{{ asset('hotel_gallery/room_(4).jpg') }}'); background-size: cover; background-position: center center; opacity: 1 !important;"></div>
            <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.6); z-index: 0;"></div>
            <div class="container" style="position: relative; z-index: 1;">
                <div class="page-cover text-center">
                    <h2 class="page-cover-tittle" style="color: #e77a3a;">Hotel Gallery</h2>
                    <p style="color: white; font-size: 16px; margin-top: 15px; margin-bottom: 20px;">Explore the beauty and elegance of PrimeLand Hotel through our photo collection</p>
                    <ol class="breadcrumb">
                        <li><a href="{{ url('/') }}">Home</a></li>
                        <li class="active">Gallery</li>
                    </ol>
                </div>
            </div>
        </section>
        <!--================Breadcrumb Area =================-->
        
        <!--================Gallery Content Area =================-->
        <section class="gallery-page">
            <div class="container">
                <!-- Section Title -->
                <div class="section_title text-center" style="margin-bottom: 30px;">
                    <h2 class="title_color" style="color: #e77a3a; font-size: 36px; margin-bottom: 10px; position: relative; display: inline-block; padding-bottom: 15px;">
                        Photo Collection
                        <span style="position: absolute; bottom: 0; left: 50%; transform: translateX(-50%); width: 60px; height: 3px; background: #e77a3a; border-radius: 2px;"></span>
                    </h2>
                    <p style="font-size: 16px; color: #777; margin-top: 10px; margin-bottom: 0;">Explore the beauty and elegance of PrimeLand Hotel through our photo collection</p>
                </div>
                
                <!-- Gallery Filter -->
                <div class="gallery-filter" style="margin-top: 25px;">
                    <button type="button" class="active" data-filter="all">All Photos</button>
                    <button type="button" data-filter="room">Rooms</button>
                    <button type="button" data-filter="restaurant">Restaurant</button>
                    <button type="button" data-filter="swimming">Swimming Pool</button>
                    <button type="button" data-filter="reception">Reception</button>
                    <button type="button" data-filter="outside">Exterior</button>
                </div>
                
                <!-- Loading spinner removed -->
                
                <!-- Gallery Grid -->
                <div class="gallery-grid" id="galleryGrid">
                    @if(count($images) > 0)
                        @foreach($images as $index => $image)
                            @php
                                $imageName = strtolower(pathinfo($image, PATHINFO_FILENAME));
                                $category = 'all';
                                
                                // Improved categorization based on actual image names
                                if (strpos($imageName, 'room') !== false || strpos($imageName, 'bathroom') !== false || strpos($imageName, 'housekeeping') !== false) {
                                    $category = 'room';
                                } elseif (strpos($imageName, 'restaurant') !== false || strpos($imageName, 'bar') !== false || strpos($imageName, 'coffee') !== false) {
                                    $category = 'restaurant';
                                } elseif (strpos($imageName, 'swimming') !== false) {
                                    $category = 'swimming';
                                } elseif (strpos($imageName, 'reception') !== false) {
                                    $category = 'reception';
                                } elseif (strpos($imageName, 'outside') !== false || strpos($imageName, 'night view') !== false || strpos($imageName, 'hotel view') !== false) {
                                    $category = 'outside';
                                }
                                
                                $displayName = str_replace(['_', '(1)', '(2)', '(3)', '(4)', '(5)', '(6)', '(7)', 'primeland hotel '], [' ', '', '', '', '', '', '', '', ''], $imageName);
                                $displayName = ucwords($displayName);
                            @endphp
                            <div class="gallery-item" data-category="{{ $category }}">
                                <a href="{{ asset('gallery_photos/' . $image) }}" class="gallery-link" data-lightbox="gallery" data-title="{{ $displayName }}">
                                    <img src="{{ asset('gallery_photos/' . $image) }}" alt="{{ $displayName }}" loading="lazy">
                                    <div class="gallery-overlay">
                                        <div class="gallery-overlay-content">
                                            <h4>{{ $displayName }}</h4>
                                            <p>Click to view full size</p>
                                        </div>
                                    </div>
                                    <div class="gallery-icon">
                                        <i class="fa fa-expand"></i>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    @else
                        <!-- No Images Found Message -->
                        <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
                            <div style="max-width: 600px; margin: 0 auto;">
                                <i class="fa fa-image" style="font-size: 64px; color: #e77a3a; margin-bottom: 20px; opacity: 0.5;"></i>
                                <h3 style="color: #333; margin-bottom: 15px; font-size: 24px;">No Gallery Images Found</h3>
                                <p style="color: #666; font-size: 16px; line-height: 1.6; margin-bottom: 0;">
                                    We're currently updating our gallery. Please check back soon or 
                                    <a href="{{ url('/contact') }}" style="color: #e77a3a; text-decoration: underline;">contact us</a> 
                                    for more information about our facilities.
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
        <!--================Gallery Content Area =================-->
        
        @include('landing_page_views.partials.royal-footer')
        
        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="{{ asset('royal-master/js/jquery-3.2.1.min.js') }}"></script>
        <script src="{{ asset('royal-master/js/popper.js') }}"></script>
        <script src="{{ asset('royal-master/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('royal-master/vendors/nice-select/js/jquery.nice-select.js') }}"></script>
        <script src="{{ asset('royal-master/vendors/lightbox/simpleLightbox.min.js') }}"></script>
        <script src="{{ asset('royal-master/js/custom.js') }}"></script>
        
        <!-- Gallery Filter Script -->
        <script>
            jQuery(document).ready(function($) {
                // Initialize lightbox immediately
                if (typeof SimpleLightbox !== 'undefined') {
                    var lightbox = new SimpleLightbox('.gallery-link', {
                        captions: true,
                        captionSelector: 'self',
                        captionType: 'data',
                        captionPosition: 'bottom',
                        animationSpeed: 200,
                        fadeSpeed: 300,
                        showCounter: true,
                        fileExt: 'jpg|jpeg|png|gif|webp'
                    });
                }
                
                // Gallery Filter Functionality
                function filterGallery(filterValue) {
                    var $items = $('.gallery-item');
                    var $buttons = $('.gallery-filter button');
                    
                    // Update button active states
                    $buttons.removeClass('active');
                    $buttons.filter('[data-filter="' + filterValue + '"]').addClass('active');
                    
                    // Filter items
                    $items.each(function(index) {
                        var $item = $(this);
                        var itemCategory = $item.attr('data-category');
                        
                        if (filterValue === 'all' || itemCategory === filterValue) {
                            // Show item with delay for animation
                            setTimeout(function() {
                                $item.stop(true, true).fadeIn(400);
                            }, index * 20);
                        } else {
                            // Hide item
                            $item.stop(true, true).fadeOut(300);
                        }
                    });
                }
                
                // Attach click event to filter buttons
                $(document).on('click', '.gallery-filter button', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    var filterValue = $(this).attr('data-filter');
                    
                    if (filterValue) {
                        filterGallery(filterValue);
                    }
                    
                    return false;
                });
                
                // Initialize - show all items on page load
                filterGallery('all');
            });
        </script>
        
        @include('landing_page_views.partials.chat-widgets')
    </body>
</html>

