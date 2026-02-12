<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="icon" href="{{ asset('royal-master/image/logo/Logo.png') }}" type="image/png">
        <title>Blog - PrimeLand Hotel</title>
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
            
            /* Blog Page Specific Styles */
            .blog_area {
                padding: 60px 0;
                background: #f8f9fa;
            }
            
            .blog_left_sidebar .blog_item {
                transition: all 0.3s ease;
            }
            
            .blog_left_sidebar .blog_item:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 30px rgba(0,0,0,0.15) !important;
            }
            
            .blog_left_sidebar .blog_item .blog_post img {
                transition: transform 0.5s ease;
            }
            
            .blog_left_sidebar .blog_item:hover .blog_post img {
                transform: scale(1.03);
            }
            
            .blog_right_sidebar .single_sidebar_widget {
                transition: all 0.3s ease;
            }
            
            .blog_right_sidebar .single_sidebar_widget:hover {
                transform: translateY(-3px);
                box-shadow: 0 8px 25px rgba(0,0,0,0.12) !important;
            }
            
            .blog_right_sidebar .popular_post_widget .post_item:hover h3 {
                color: #e77a3a;
            }
            
            .blog_right_sidebar .author_widget .social_icon a:hover {
                background: #e77a3a !important;
                color: #fff !important;
                transform: translateY(-3px);
            }
            
            .blog-pagination .pagination {
                justify-content: center;
            }
            
            .blog-pagination .pagination .page-link {
                color: #e77a3a;
                border-color: #e77a3a;
                padding: 10px 20px;
                margin: 0 5px;
                border-radius: 8px;
            }
            
            .blog-pagination .pagination .page-item.active .page-link {
                background: #e77a3a;
                border-color: #e77a3a;
                color: #fff;
            }
            
            .blog-pagination .pagination .page-link:hover {
                background: #e77a3a;
                color: #fff;
                border-color: #e77a3a;
            }
            
            /* Responsive Blog Page */
            @media (max-width: 991px) {
                .blog_area {
                    padding: 40px 0;
                }
                
                .blog_left_sidebar .blog_item .blog_post img {
                    height: 180px;
                }
                
                .blog_left_sidebar .blog_item .blog_post .blog_details {
                    padding: 18px;
                }
                
                .blog_left_sidebar .blog_item .blog_post .blog_details h2 {
                    font-size: 17px;
                }
                
                .blog_right_sidebar {
                    margin-top: 40px;
                }
            }
            
            @media (max-width: 768px) {
                .blog_area {
                    padding: 30px 0;
                }
                
                .blog_left_sidebar .row > div {
                    margin-bottom: 20px;
                }
                
                .blog_left_sidebar .blog_item .blog_post img {
                    height: 200px;
                }
                
                .blog_left_sidebar .blog_item .blog_post .blog_details {
                    padding: 20px;
                }
                
                .blog_left_sidebar .blog_item .blog_post .blog_details h2 {
                    font-size: 18px;
                }
                
                .blog_left_sidebar .blog_item .blog_post .blog_details p {
                    font-size: 14px;
                }
                
                .blog_left_sidebar .blog_item .blog_post .blog_details > div {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 12px;
                }
                
                .blog_left_sidebar .blog_item .blog_post .blog_details > div a {
                    width: 100%;
                    text-align: center;
                }
                
                .blog_right_sidebar .widget_title {
                    font-size: 20px;
                }
            }
            
            @media (max-width: 576px) {
                .blog_area {
                    padding: 25px 0;
                }
                
                .blog_left_sidebar .blog_item .blog_post img {
                    height: 180px;
                }
                
                .blog_left_sidebar .blog_item .blog_post .blog_details {
                    padding: 18px;
                }
                
                .blog_left_sidebar .blog_item .blog_post .blog_details h2 {
                    font-size: 16px;
                }
                
                .blog_left_sidebar .blog_item .blog_post .blog_details p {
                    font-size: 13px;
                }
                
                .blog_right_sidebar .widget_title {
                    font-size: 18px;
                }
                
                .blog_right_sidebar .popular_post_widget .post_item img {
                    width: 70px;
                    height: 60px;
                }
                
                .blog_right_sidebar .popular_post_widget .post_item .media-body h3 {
                    font-size: 14px;
                }
            }
        </style>
    </head>
    <body>
        <!-- Loading Spinner -->
        <div class="page-loading" id="pageLoading">
            <div class="spinner"></div>
        </div>
        @include('landing_page_views.partials.royal-header')
        
        <!--================Breadcrumb Area =================-->
        <section class="breadcrumb_area" style="padding: 250px 0px 120px; min-height: 500px; position: relative;">
            <div class="overlay bg-parallax" data-stellar-ratio="0.8" data-stellar-vertical-offset="0" data-background="" style="background-image: url('{{ asset('hotel_gallery/coffee_.jpg') }}'); background-size: cover; background-position: center center; opacity: 1 !important;"></div>
            <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.3); z-index: 0;"></div>
            <div class="container" style="position: relative; z-index: 1;">
                <div class="page-cover text-center">
                    <h2 class="page-cover-tittle" style="color: #e77a3a;">Blog</h2>
                    <p style="color: white; font-size: 16px; margin-top: 15px; margin-bottom: 20px;">Stay updated with our latest news, events, and travel tips</p>
                    <ol class="breadcrumb">
                        <li><a href="{{ url('/') }}">Home</a></li>
                        <li class="active">Blog</li>
                    </ol>
                </div>
            </div>
        </section>
        <!--================Breadcrumb Area =================-->
        
        <!--================Blog Area =================-->
        <section class="blog_area" style="padding: 60px 0; background: #f8f9fa;">
            <div class="container">
                <!-- Section Title -->
                <div class="section_title text-center" style="margin-bottom: 50px;">
                    <h2 class="title_color" style="color: #e77a3a; font-size: 36px; margin-bottom: 15px; position: relative; display: inline-block; padding-bottom: 15px;">
                        Latest Blog Posts
                        <span style="position: absolute; bottom: 0; left: 50%; transform: translateX(-50%); width: 60px; height: 3px; background: #e77a3a; border-radius: 2px;"></span>
                    </h2>
                    <p style="font-size: 16px; color: #777; margin-top: 20px;">Read our latest articles, travel guides, and hotel updates</p>
                </div>
                
                <div class="row">
                    <div class="col-lg-8">
                        <div class="blog_left_sidebar">
                            <div class="row">
                                @forelse($posts ?? [] as $post)
                                <div class="col-md-6 mb-4">
                                    <article class="blog_item modern-blog-card" id="post-{{ $post->id }}" style="background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.1); height: 100%; display: flex; flex-direction: column;">
                                        <div class="blog_post" style="position: relative; flex: 1; display: flex; flex-direction: column;">
                                            @if($post->featured_image)
                                                <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" style="width: 100%; height: 200px; object-fit: cover;">
                                            @else
                                                <img src="{{ asset('royal-master/image/blog/main-blog/m-blog-' . (($loop->index % 3) + 1) . '.jpg') }}" alt="{{ $post->title }}" style="width: 100%; height: 200px; object-fit: cover;">
                                            @endif
                                            <div class="blog_details" style="padding: 20px; flex: 1; display: flex; flex-direction: column;">
                                                <div style="margin-bottom: 10px;">
                                                    @if($post->category)
                                                        <a href="{{ route('blog.index') }}?category={{ urlencode($post->category) }}" style="display: inline-block; background: #e77a3a; color: #fff; padding: 4px 10px; border-radius: 20px; font-size: 10px; font-weight: 600; text-decoration: none; margin-right: 5px;">{{ $post->category }}</a>
                                                    @endif
                                                    @if($post->tags && is_array($post->tags) && count($post->tags) > 0)
                                                        @foreach(array_slice($post->tags, 0, 1) as $tag)
                                                            <a href="{{ route('blog.index') }}?tag={{ urlencode($tag) }}" style="display: inline-block; background: #f0f0f0; color: #666; padding: 4px 10px; border-radius: 20px; font-size: 10px; font-weight: 600; text-decoration: none; margin-right: 5px;">{{ $tag }}</a>
                                                        @endforeach
                                                    @endif
                                                </div>
                                                <a href="#post-{{ $post->id }}" style="text-decoration: none;"><h2 style="color: #222; font-size: 18px; font-weight: 700; margin-bottom: 10px; line-height: 1.4;">{{ $post->title }}</h2></a>
                                                <p style="color: #666; line-height: 1.6; font-size: 13px; margin-bottom: 15px; flex: 1;">{{ $post->excerpt ?: Str::limit(strip_tags($post->content), 120) }}</p>
                                                <div style="display: flex; align-items: center; justify-content: space-between; margin-top: auto; padding-top: 15px; border-top: 1px solid #eee;">
                                                    <div style="display: flex; align-items: center; gap: 10px; color: #999; font-size: 11px; flex-wrap: wrap;">
                                                        <span><i class="lnr lnr-calendar-full" style="margin-right: 3px; color: #e77a3a;"></i>{{ $post->formatted_date }}</span>
                                                        <span><i class="lnr lnr-eye" style="margin-right: 3px; color: #e77a3a;"></i>{{ number_format($post->views) }}</span>
                                                    </div>
                                                    <a href="#post-{{ $post->id }}" class="btn theme_btn button_hover" style="padding: 6px 15px; font-size: 12px; white-space: nowrap;">Read More</a>
                                                </div>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                                @empty
                                <div class="col-12">
                                    <article class="blog_item" style="background: #fff; border-radius: 12px; padding: 60px 20px; text-align: center; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
                                        <h2 style="color: #222; margin-bottom: 15px;">No Blog Posts Yet</h2>
                                        <p style="color: #666;">Check back soon for exciting content!</p>
                                    </article>
                                </div>
                                @endforelse
                            </div>
                            
                            @if(isset($posts) && $posts->hasPages())
                            <nav class="blog-pagination justify-content-center d-flex" style="margin-top: 50px;">
                                {{ $posts->links() }}
                            </nav>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="blog_right_sidebar">
                            <!-- Search Widget -->
                            <aside class="single_sidebar_widget search_widget" style="background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); margin-bottom: 30px;">
                                <h3 class="widget_title" style="color: #222; font-size: 22px; font-weight: 700; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #e77a3a;">Search Posts</h3>
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Search Posts" style="border: 2px solid #e0e0e0; border-radius: 8px 0 0 8px; padding: 12px 15px; font-size: 14px;">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button" style="background: #e77a3a; color: #fff; border: 2px solid #e77a3a; border-radius: 0 8px 8px 0; padding: 12px 20px;"><i class="lnr lnr-magnifier"></i></button>
                                    </span>
                                </div>
                            </aside>
                            
                            <!-- Author Widget -->
                            <aside class="single_sidebar_widget author_widget" style="background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); margin-bottom: 30px; text-align: center;">
                                <img class="author_img rounded-circle" src="{{ asset('royal-master/image/logo/Logo.png') }}" alt="PrimeLand Hotel" style="width: 120px; height: 120px; object-fit: contain; margin-bottom: 20px; border: 3px solid #e77a3a; padding: 10px;">
                                <h4 style="color: #222; font-size: 22px; font-weight: 700; margin-bottom: 10px;">PrimeLand Hotel</h4>
                                <p style="color: #e77a3a; font-size: 14px; font-weight: 600; margin-bottom: 20px;">Your Premier Hospitality Destination</p>
                                <div class="social_icon" style="display: flex; justify-content: center; gap: 10px; margin-bottom: 20px;">
                                    <a href="#" style="display: inline-block; width: 40px; height: 40px; line-height: 40px; text-align: center; border-radius: 50%; background: #f0f0f0; color: #e77a3a; transition: all 0.3s ease;"><i class="fa fa-facebook"></i></a>
                                    <a href="#" style="display: inline-block; width: 40px; height: 40px; line-height: 40px; text-align: center; border-radius: 50%; background: #f0f0f0; color: #e77a3a; transition: all 0.3s ease;"><i class="fa fa-twitter"></i></a>
                                    <a href="#" style="display: inline-block; width: 40px; height: 40px; line-height: 40px; text-align: center; border-radius: 50%; background: #f0f0f0; color: #e77a3a; transition: all 0.3s ease;"><i class="fa fa-instagram"></i></a>
                                    <a href="#" style="display: inline-block; width: 40px; height: 40px; line-height: 40px; text-align: center; border-radius: 50%; background: #f0f0f0; color: #e77a3a; transition: all 0.3s ease;"><i class="fa fa-linkedin"></i></a>
                                </div>
                                <p style="color: #666; line-height: 1.7; font-size: 14px; margin: 0;">Welcome to PrimeLand Hotel's blog! Stay updated with our latest news, travel tips, local insights, and exclusive offers.</p>
                            </aside>
                            
                            <!-- Latest Posts Widget -->
                            @if(isset($latestPosts) && $latestPosts->count() > 0)
                            <aside class="single_sidebar_widget popular_post_widget" style="background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); margin-bottom: 30px;">
                                <h3 class="widget_title" style="color: #222; font-size: 22px; font-weight: 700; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 2px solid #e77a3a;">Latest Posts</h3>
                                @foreach($latestPosts->take(5) as $latestPost)
                                <div class="media post_item" style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #f0f0f0; display: flex; align-items: flex-start;">
                                    @if($latestPost->featured_image)
                                        <img src="{{ asset('storage/' . $latestPost->featured_image) }}" alt="{{ $latestPost->title }}" style="width: 90px; height: 70px; object-fit: cover; border-radius: 8px; margin-right: 15px; flex-shrink: 0;">
                                    @else
                                        <img src="{{ asset('royal-master/image/blog/post' . (($loop->index % 4) + 1) . '.jpg') }}" alt="{{ $latestPost->title }}" style="width: 90px; height: 70px; object-fit: cover; border-radius: 8px; margin-right: 15px; flex-shrink: 0;">
                                    @endif
                                    <div class="media-body" style="flex: 1;">
                                        <a href="{{ route('blog.index') }}#post-{{ $latestPost->id }}" style="text-decoration: none;"><h3 style="color: #222; font-size: 15px; font-weight: 600; margin-bottom: 5px; line-height: 1.4;">{{ Str::limit($latestPost->title, 50) }}</h3></a>
                                        <p style="color: #999; font-size: 12px; margin: 0;"><i class="lnr lnr-calendar-full" style="margin-right: 5px;"></i>{{ $latestPost->published_at ? $latestPost->published_at->diffForHumans() : $latestPost->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                @endforeach
                            </aside>
                            @endif
                            
                            <!-- Popular Posts Widget -->
                            @if(isset($latestPosts) && $latestPosts->count() > 0)
                            <aside class="single_sidebar_widget popular_post_widget" style="background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
                                <h3 class="widget_title" style="color: #222; font-size: 22px; font-weight: 700; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 2px solid #e77a3a;">Popular Posts</h3>
                                @foreach($latestPosts->take(4) as $popularPost)
                                <div class="media post_item" style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #f0f0f0; display: flex; align-items: flex-start;">
                                    @if($popularPost->featured_image)
                                        <img src="{{ asset('storage/' . $popularPost->featured_image) }}" alt="{{ $popularPost->title }}" style="width: 90px; height: 70px; object-fit: cover; border-radius: 8px; margin-right: 15px; flex-shrink: 0;">
                                    @else
                                        <img src="{{ asset('royal-master/image/blog/post' . (($loop->index % 4) + 1) . '.jpg') }}" alt="{{ $popularPost->title }}" style="width: 90px; height: 70px; object-fit: cover; border-radius: 8px; margin-right: 15px; flex-shrink: 0;">
                                    @endif
                                    <div class="media-body" style="flex: 1;">
                                        <a href="{{ route('blog.index') }}#post-{{ $popularPost->id }}" style="text-decoration: none;"><h3 style="color: #222; font-size: 15px; font-weight: 600; margin-bottom: 5px; line-height: 1.4;">{{ Str::limit($popularPost->title, 50) }}</h3></a>
                                        <p style="color: #999; font-size: 12px; margin: 0;"><i class="lnr lnr-calendar-full" style="margin-right: 5px;"></i>{{ $popularPost->published_at ? $popularPost->published_at->diffForHumans() : $popularPost->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                @endforeach
                            </aside>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--================Blog Area =================-->
        
        @include('landing_page_views.partials.royal-footer')
        @include('landing_page_views.partials.royal-scripts')
        @include('landing_page_views.partials.chat-widgets')
        
        <script>
            // Hide loading spinner when page is fully loaded
            window.addEventListener('load', function() {
                setTimeout(function() {
                    var loading = document.getElementById('pageLoading');
                    if (loading) {
                        loading.classList.add('hidden');
                    }
                }, 300);
            });
        </script>
    </body>
</html>
