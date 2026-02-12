<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="icon" href="{{ asset('royal-master/image/logo/Logo.png') }}" type="image/png">
        <title>{{ config('app.name') }}</title>
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="{{ asset('royal-master/css/bootstrap.css') }}">
        <link rel="stylesheet" href="{{ asset('royal-master/vendors/linericon/style.css') }}">
        <link rel="stylesheet" href="{{ asset('royal-master/css/font-awesome.min.css') }}">
        <link rel="stylesheet" href="{{ asset('royal-master/vendors/owl-carousel/owl.carousel.min.css') }}">
        <link rel="stylesheet" href="{{ asset('royal-master/vendors/nice-select/css/nice-select.css') }}">
        <!-- Flatpickr - Advanced Date Picker -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <!-- main css -->
        <link rel="stylesheet" href="{{ asset('royal-master/css/style.css') }}">
        <link rel="stylesheet" href="{{ asset('royal-master/css/responsive.css') }}">
        <style>
            /* Prevent horizontal scrolling on mobile - Minimal fix */
            @media (max-width: 767px) {
                html, body {
                    overflow-x: hidden !important;
                }
                
                /* Only fix elements that commonly cause horizontal scroll */
                .hotel_booking_table .row {
                    margin-left: 0 !important;
                    margin-right: 0 !important;
                }
                
                .hotel_booking_table .row > div {
                    padding-left: 5px !important;
                    padding-right: 5px !important;
                }
                
                /* Ensure form elements don't overflow */
                .book_tabel_item input,
                .book_tabel_item select,
                .book_tabel_item .form-control,
                .date-wrapper .date-input {
                    max-width: 100% !important;
                    box-sizing: border-box !important;
                }
            }
            
            .header_area {
                margin-top: 0 !important;
                padding-top: 0 !important;
            }
            
            .navbar-nav .dropdown-menu .nav-link {
                padding-left: 40px;
            }
            
            @media (max-width: 480px) {
                .navbar-collapse {
                    width: 85%;
                }
                
                .navbar-nav .nav-link {
                    font-size: 15px;
                    padding: 16px 20px;
                }
            }
            
            /* Sliding Background Effect with Multiple Images */
            .banner_area .booking_table {
                position: relative;
                overflow: hidden;
            }
            
            /* Mobile Banner Size Reduction */
            @media (max-width: 767px) {
                .banner_area .booking_table {
                    min-height: 450px !important;
                    height: auto !important;
                    padding-bottom: 60px !important;
                }
                
                .banner_area {
                    min-height: 450px !important;
                    height: auto !important;
                }
            }
            
            @media (max-width: 480px) {
                .banner_area .booking_table {
                    min-height: 400px !important;
                    height: auto !important;
                    padding-bottom: 50px !important;
                }
                
                .banner_area {
                    min-height: 400px !important;
                    height: auto !important;
                }
            }
            
            .banner_area .bg-parallax {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-size: cover;
                background-position: center center;
                background-repeat: no-repeat;
                opacity: 0;
                z-index: 1;
                transition: opacity 2s ease-in-out;
                will-change: opacity;
                -webkit-backface-visibility: hidden;
                backface-visibility: hidden;
                transform: translateZ(0);
            }
            
            .banner_area .bg-parallax.active {
                opacity: 0.85;
            }
            
            /* Background Images */
            .banner_area .bg-image-1 {
                background-image: url('{{ asset("landing_page_backround_images/hotel view_.jpg") }}');
            }
            
            .banner_area .bg-image-2 {
                background-image: url('{{ asset("landing_page_backround_images/night view_.jpg") }}');
            }
            
            .banner_area .bg-image-3 {
                background-image: url('{{ asset("landing_page_backround_images/restaurant_.jpg") }}');
            }
            
            .banner_area .bg-image-4 {
                background-image: url('{{ asset("landing_page_backround_images/swimming view_.jpg") }}');
            }
            
            .banner_area .bg-image-5 {
                background-image: url('{{ asset("landing_page_backround_images/swimming view_(1).jpg") }}');
            }
            
            .banner_area .container {
                position: relative;
                z-index: 2;
            }
            
            /* Fix accommodation images to maintain original design */
            .accomodation_item .hotel_img {
                width: 100%;
                display: block;
                aspect-ratio: 4 / 3;
                overflow: hidden;
            }
            
            .accomodation_item .hotel_img img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }
            
            /* Ensure consistent sizing across all room images */
            @media (min-width: 992px) {
                .accomodation_item .hotel_img {
                    min-height: 250px;
                }
            }
            
            @media (max-width: 991px) {
                .accomodation_item .hotel_img {
                    min-height: 200px;
                }
            }
            
            /* Ensure all food category cards have the same size */
            .blog_categorie_area .categories_post {
                height: 100%;
                display: flex;
                flex-direction: column;
            }
            
            .blog_categorie_area .categories_post img {
                width: 100%;
                height: 300px;
                object-fit: cover;
                display: block;
            }
            
            .blog_categorie_area .row > div {
                display: flex;
                margin-bottom: 30px;
            }
            
            /* Make overlay smaller and remove hover color change */
            .blog_categorie_area .categories_post .categories_details {
                top: 80px;
                left: 40px;
                right: 40px;
                bottom: 80px;
            }
            
            .blog_categorie_area .categories_post:hover .categories_details {
                background: rgba(34, 34, 34, 0.8) !important;
            }
            
            @media (max-width: 768px) {
                .blog_categorie_area .categories_post img {
                    height: 250px;
                }
                
                .blog_categorie_area .categories_post .categories_details {
                    top: 70px;
                    left: 30px;
                    right: 30px;
                    bottom: 70px;
                }
            }
            
            /* Modern Testimonial Section with Professional Card Design */
            .testimonial_area {
                padding: 80px 0;
                position: relative;
            }
            
            .testimonial-bg-image {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-image: url('{{ asset("landing_page_backround_images/restaurant_.jpg") }}');
                background-size: cover;
                background-position: center center;
                background-repeat: no-repeat;
                z-index: 0;
            }
            
            .testimonial-overlay-dark {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.7);
                z-index: 1;
            }
            
            /* Professional Card Design */
            .testimonial_card_item {
                position: relative;
                overflow: hidden;
                border-radius: 12px;
                background: #fff;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
                margin: 15px;
                height: 100%;
                display: flex;
                flex-direction: column;
                border-top: 5px solid #e77a3a;
            }
            
            .testimonial_card_item:hover {
                transform: translateY(-8px);
                box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
            }
            
            .testimonial_card_details {
                padding: 30px;
                flex: 1;
                display: flex;
                flex-direction: column;
                position: relative;
            }
            
            /* Watermark Quote Icon */
            .testimonial_quote_icon {
                position: absolute;
                top: 20px;
                right: 20px;
                color: rgba(231, 122, 58, 0.08); /* More subtle */
                font-size: 80px;
                line-height: 1;
                z-index: 0;
            }
            
            /* Source Badge */
            .source_badge {
                position: absolute;
                top: 20px;
                right: 20px;
                background: #fff;
                border: 1px solid #eee;
                border-radius: 20px;
                padding: 5px 12px;
                font-size: 11px;
                color: #555;
                font-weight: 600;
                display: flex;
                align-items: center;
                box-shadow: 0 2px 5px rgba(0,0,0,0.05);
                z-index: 2;
            }
            
            .source_badge i {
                color: #4285F4; /* Google Blue */
                margin-right: 5px;
                font-size: 14px;
            }
            
            .testimonial_card_text_content {
                position: relative;
                z-index: 1;
                margin-bottom: 20px;
                flex: 1;
                margin-top: 20px; /* Space for badge/avatar */
            }
            
            .testimonial_card_text_content p {
                color: #555;
                font-size: 15px;
                line-height: 1.6;
                font-weight: 400;
                margin-bottom: 5px;
            }
            
            .read_more_btn {
                background: none;
                border: none;
                color: #e77a3a;
                font-size: 13px;
                font-weight: 600;
                padding: 0;
                cursor: pointer;
                transition: color 0.2s;
                text-decoration: underline;
                display: inline-block;
                margin-top: 5px;
            }
            
            .read_more_btn:hover {
                color: #c76123;
            }
            
            .testimonial_divider {
                height: 1px;
                background: linear-gradient(to right, transparent, #eee, transparent);
                width: 100%;
                margin-bottom: 15px;
            }
            
            .testimonial_card_author_info {
                display: flex;
                flex-direction: column;
                align-items: center;
                position: relative;
                z-index: 1;
                text-align: center;
            }
            
            /* Flag Avatar */
            .author_avatar_flag {
                width: 60px;
                height: 60px;
                border-radius: 50%;
                overflow: hidden;
                margin-bottom: 10px;
                border: 3px solid #fff;
                box-shadow: 0 5px 15px rgba(0,0,0,0.1);
                flex-shrink: 0;
                background: #f8f9fa;
            }
            
            .author_avatar_flag img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                object-position: center;
            }
            
            .author_details h5 {
                font-size: 17px;
                font-weight: 700;
                color: #222;
                margin-bottom: 5px;
            }
            
            .rating_stars {
                color: #ffc107;
                font-size: 12px;
                margin-bottom: 5px;
            }
            
            .guest_location {
                font-size: 12px;
                color: #888;
                font-weight: 500;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            
            /* Owl Carousel Customization */
            .testimonial_slider.owl-carousel .owl-dots {
                margin-top: 30px;
                text-align: center;
            }
            
            .testimonial_slider.owl-carousel .owl-dots .owl-dot {
                width: 10px;
                height: 10px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.3);
                margin: 0 5px;
                transition: all 0.3s ease;
            }
            
            .testimonial_slider.owl-carousel .owl-dots .owl-dot.active {
                background: #e77a3a;
                transform: scale(1.3);
            }
            
            /* Responsive Design */
            @media (max-width: 992px) {
                .testimonial_card_details {
                    padding: 25px;
                }
                
                .testimonial_quote_icon {
                    font-size: 50px;
                    top: 15px;
                    right: 15px;
                }
            }
            
            @media (max-width: 768px) {
                .testimonial_area {
                    padding: 60px 0;
                }
                
                .testimonial_card_item {
                    margin: 10px 5px;
                }
                
                .testimonial_card_text_content p {
                    font-size: 14px;
                }
                
                .author_avatar {
                    width: 50px;
                    height: 50px;
                }
            }
            
            /* Reduce spacing between sections */
            .about_history_area.section_gap {
                padding: 60px 0;
            }
            
            .blog_categorie_area.section_gap {
                padding: 40px 0 60px 0;
            }
            
            .testimonial_area.section_gap {
                padding: 35px 0 !important;
            }
            
            /* Spacing between Counter and Enjoy the Prime Food sections */
            .counter_area + .blog_categorie_area {
                margin-top: 20px;
            }
            
            /* Reduce spacing between Enjoy the Prime Food and Testimonial sections */
            .blog_categorie_area + .testimonial_area {
                margin-top: -40px;
            }
            
            /* Reduce spacing for other sections too */
            .section_gap {
                padding: 80px 0;
            }
            
            @media (max-width: 768px) {
                .about_history_area.section_gap {
                    padding: 50px 0;
                }
                
                .blog_categorie_area.section_gap {
                    padding: 35px 0 50px 0;
                }
                
                .about_history_area + .blog_categorie_area {
                    margin-top: -30px;
                }
                
                .blog_categorie_area + .testimonial_area {
                    margin-top: -30px;
                }
                
                .section_gap {
                    padding: 60px 0;
                }
                
                /* Enjoy the Prime Food - Mobile Responsive */
                .blog_categorie_area .row {
                    justify-content: center;
                    display: flex;
                    flex-wrap: wrap;
                }
                
                .blog_categorie_area .categories_post {
                    margin-bottom: 20px;
                    margin-left: auto;
                    margin-right: auto;
                    max-width: 100%;
                }
                
                .blog_categorie_area .categories_post img {
                    width: 100%;
                    height: auto;
                    max-height: 250px;
                    object-fit: cover;
                }
                
                .blog_categorie_area .categories_details {
                    padding: 20px 15px;
                }
                
                .blog_categorie_area .categories_text {
                    text-align: center;
                }
                
                .blog_categorie_area .categories_text h5 {
                    font-size: 18px;
                    margin-bottom: 10px;
                }
                
                .blog_categorie_area .categories_text p {
                    font-size: 14px;
                    line-height: 1.6;
                }
                
                .blog_categorie_area .section_title h2 {
                    font-size: 28px;
                    margin-bottom: 10px;
                }
                
                .blog_categorie_area .section_title p {
                    font-size: 14px;
                    padding: 0 15px;
                }
            }
            
            /* Professional Blog Section Design */
            .latest_blog_area {
                background: #f8f9fa;
            }
            
            .modern-blog-card {
                background: #fff;
                border-radius: 12px;
                overflow: hidden;
                box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
                transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                height: 100%;
                display: flex;
                flex-direction: column;
            }
            
            .modern-blog-card:hover {
                transform: translateY(-8px);
                box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
            }
            
            .modern-blog-card .thumb {
                position: relative;
                overflow: hidden;
                height: 250px;
                border-radius: 12px 12px 0 0;
            }
            
            .modern-blog-card .thumb img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform 0.6s ease;
                display: block;
            }
            
            .modern-blog-card:hover .thumb img {
                transform: scale(1.1);
            }
            
            .blog-overlay {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(to bottom, rgba(0,0,0,0) 0%, rgba(0,0,0,0.7) 100%);
                opacity: 0;
                transition: opacity 0.4s ease;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .modern-blog-card:hover .blog-overlay {
                opacity: 1;
            }
            
            .blog-read-more {
                width: 50px;
                height: 50px;
                background: #e77a3a;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #fff;
                font-size: 18px;
                transition: all 0.3s ease;
                text-decoration: none;
            }
            
            .blog-read-more:hover {
                background: #d65a1a;
                transform: scale(1.1) rotate(90deg);
                color: #fff;
            }
            
            .modern-blog-card .details {
                padding: 25px;
                flex: 1;
                display: flex;
                flex-direction: column;
            }
            
            .modern-blog-card .tags {
                margin-bottom: 15px;
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
            }
            
            .modern-tag {
                background: #f0f0f0 !important;
                border: none !important;
                border-radius: 20px !important;
                padding: 6px 16px !important;
                font-size: 11px !important;
                font-weight: 600 !important;
                color: #555 !important;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                transition: all 0.3s ease;
            }
            
            .modern-tag:hover {
                background: #e77a3a !important;
                color: #fff !important;
                transform: translateY(-2px);
            }
            
            .modern-blog-title {
                font-size: 20px !important;
                font-weight: 700 !important;
                line-height: 1.4 !important;
                margin: 0 0 12px 0 !important;
                color: #222 !important;
                transition: color 0.3s ease;
            }
            
            .modern-blog-card:hover .modern-blog-title {
                color: #e77a3a !important;
            }
            
            .modern-blog-excerpt {
                color: #666;
                font-size: 14px;
                line-height: 1.7;
                margin-bottom: 15px;
                flex: 1;
            }
            
            .blog-meta {
                margin-top: auto;
                padding-top: 15px;
                border-top: 1px solid #eee;
            }
            
            .modern-date {
                color: #888 !important;
                font-size: 13px !important;
                font-weight: 500 !important;
                display: flex;
                align-items: center;
                gap: 8px;
            }
            
            .modern-date i {
                color: #e77a3a;
                font-size: 14px;
            }
            
            /* Responsive Blog Design */
            @media (max-width: 768px) {
                .modern-blog-card .thumb {
                    height: 200px;
                }
                
                .modern-blog-card .details {
                    padding: 20px;
                }
                
                .modern-blog-title {
                    font-size: 18px !important;
                }
            }
            
            
            /* Counter Section Design */
            .counter_area {
                padding: 60px 0;
                position: relative;
                background-image: url('{{ asset("hotel_gallery/swimming view_(1).jpg") }}');
                background-size: cover;
                background-position: center center;
                background-repeat: no-repeat;
                background-attachment: fixed;
            }
            
            .counter-overlay {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.6);
                z-index: 1;
            }
            
            /* Reduce spacing around counter section */
            .about_history_area + .counter_area {
                margin-top: -40px;
            }
            
            .counter_area + .blog_categorie_area {
                margin-top: -40px;
            }
            
            .counter_item {
                padding: 30px 20px;
                transition: all 0.3s ease;
            }
            
            .counter_item:hover {
                transform: translateY(-5px);
            }
            
            .counter_icon {
                width: 80px;
                height: 80px;
                background: rgba(255, 255, 255, 0.15);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 20px;
                transition: all 0.3s ease;
                border: 2px solid rgba(255, 255, 255, 0.3);
            }
            
            .counter_item:hover .counter_icon {
                background: rgba(255, 255, 255, 0.25);
                transform: scale(1.1);
                border-color: rgba(255, 255, 255, 0.5);
            }
            
            .counter_icon i {
                font-size: 36px;
                color: #fff;
            }
            
            .counter_number {
                font-size: 48px;
                font-weight: 700;
                color: #fff;
                margin: 15px 0;
                text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
                line-height: 1.2;
            }
            
            .counter_item p {
                font-size: 16px;
                color: rgba(255, 255, 255, 0.95);
                font-weight: 500;
                margin: 0;
                text-transform: uppercase;
                letter-spacing: 1px;
            }
            
            /* Responsive Counter Design */
            @media (max-width: 992px) {
                .counter_number {
                    font-size: 40px;
                }
                
                .counter_icon {
                    width: 70px;
                    height: 70px;
                }
                
                .counter_icon i {
                    font-size: 30px;
                }
            }
            
            @media (max-width: 768px) {
                .counter_area {
                    padding: 50px 0;
                }
                
                .about_history_area + .counter_area {
                    margin-top: -30px;
                }
                
                .counter_area + .blog_categorie_area {
                    margin-top: -30px;
                }
                
                .counter_item {
                    padding: 25px 15px;
                    margin-bottom: 20px;
                }
                
                .counter_number {
                    font-size: 36px;
                }
                
                .counter_icon {
                    width: 60px;
                    height: 60px;
                }
                
                .counter_icon i {
                    font-size: 26px;
                }
                
                .counter_item p {
                    font-size: 14px;
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
            
            .payment-icon i {
                display: block;
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
            }
            
            /* Responsive Footer */
            @media (max-width: 768px) {
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
            
            /* Ensure all facility cards have equal heights */
            .facilities_area .row.mb_30 {
                display: flex;
                flex-wrap: wrap;
            }
            
            .facilities_area .row.mb_30 > div {
                display: flex;
                flex-direction: column;
            }
            
            .facilities_area .facilities_item {
                height: 100%;
                display: flex;
                flex-direction: column;
            }
            
            /* Reduce spacing below Facilities section title */
            .facilities_area .section_title {
                margin-bottom: 40px !important;
            }
            
            /* Reduce spacing above Facilities section */
            .facilities_area.section_gap {
                padding-top: 60px !important;
            }
            
            @media (max-width: 991px) {
                .facilities_area .row.mb_30 > div {
                    margin-bottom: 30px;
                }
                
                .facilities_area .section_title {
                    margin-bottom: 30px !important;
                }
                
                .facilities_area.section_gap {
                    padding-top: 50px !important;
                }
            }
            
            
            .contact_info_item h6 {
                transition: color 0.3s ease;
            }
            
            .contact_info_item:hover h6 {
                color: #e77a3a;
            }
            
            /* Responsive Contact Section */
            @media (max-width: 991px) {
                .contact_image_wrapper {
                    margin-bottom: 30px;
                }
            }
            
            /* ==================== COMPREHENSIVE MOBILE & TABLET RESPONSIVE STYLES ==================== */
            
            /* Tablet Styles (768px - 991px) */
            @media (max-width: 991px) and (min-width: 768px) {
                /* Booking Form - Tablet */
                .hotel_booking_table {
                    padding: 20px 15px;
                }
                
                .hotel_booking_table .col-md-3 h2 {
                    font-size: 24px;
                    margin-bottom: 20px;
                }
                
                .hotel_booking_table .col-md-9 {
                    width: 100%;
                }
                
                .hotel_booking_table .row > div {
                    margin-bottom: 15px;
                }
                
                /* Banner Area - Tablet */
                .banner_area {
                    min-height: 500px;
                }
                
                /* Accommodation Cards - Tablet */
                .accomodation_item {
                    margin-bottom: 30px;
                }
                
                /* Testimonials - Tablet */
                .testimonial_card_item {
                    margin: 8px;
                }
            }
            
            /* Mobile Styles (max-width: 767px) */
            @media (max-width: 767px) {
                /* Container padding */
                .container {
                    padding-left: 15px;
                    padding-right: 15px;
                }
                
                /* Header/Navigation - Mobile */
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
                
                /* Banner Area - Mobile */
                .banner_area {
                    min-height: 450px;
                    padding-top: 50px;
                    padding-bottom: 60px;
                }
                
                .banner_area .booking_table {
                    min-height: 450px;
                    padding-bottom: 60px;
                }
                
                .banner_area .banner_content h2 {
                    font-size: 28px !important;
                    line-height: 1.3;
                    margin-bottom: 12px !important;
                    font-weight: 700 !important;
                }
                
                .banner_area .banner_content h4 {
                    font-size: 18px !important;
                    margin-bottom: 18px !important;
                }
                
                .banner_area .banner_content .btn {
                    font-size: 15px !important;
                    padding: 12px 25px !important;
                }
                
                /* Booking Form - Mobile */
                .hotel_booking_area {
                    padding: 30px 0;
                    margin-top: -80px;
                }
                
                .hotel_booking_table {
                    padding: 20px 15px;
                    background: rgba(255, 255, 255, 0.95);
                    border-radius: 10px;
                    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
                }
                
                .hotel_booking_table .col-md-3 {
                    width: 100%;
                    text-align: center;
                    margin-bottom: 20px;
                }
                
                .hotel_booking_table .col-md-3 h2 {
                    font-size: 22px;
                    margin-bottom: 0;
                }
                
                .hotel_booking_table .col-md-9 {
                    width: 100%;
                }
                
                .hotel_booking_table .row {
                    margin: 0;
                }
                
                .hotel_booking_table .row > div {
                    width: 100%;
                    flex: 0 0 100%;
                    max-width: 100%;
                    margin-bottom: 15px;
                    padding: 0 5px;
                }
                
                .book_tabel_item {
                    margin-bottom: 15px;
                }
                
                .book_tabel_item .form-group {
                    margin-bottom: 15px;
                }
                
                .book_tabel_item .input-group {
                    width: 100%;
                    display: flex;
                    flex-wrap: nowrap;
                }
                
                .book_tabel_item .form-control {
                    font-size: 14px;
                    padding: 12px 15px;
                    height: auto;
                    width: 100%;
                    flex: 1 1 auto;
                    min-width: 0;
                    box-sizing: border-box;
                }
                
                .book_tabel_item .input-group-addon {
                    padding: 12px 15px;
                    flex: 0 0 auto;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    min-width: 45px;
                }
                
                /* Ensure input-group doesn't overflow */
                .book_tabel_item .input-group > * {
                    box-sizing: border-box;
                }
                
                /* Flatpickr calendar responsive on mobile */
                .flatpickr-calendar {
                    width: 100% !important;
                    max-width: 100% !important;
                    left: 50% !important;
                    transform: translateX(-50%) !important;
                    margin-left: 0 !important;
                }
                
                /* Ensure date inputs are touch-friendly */
                .book_tabel_item .form-control[readonly] {
                    cursor: pointer;
                    -webkit-tap-highlight-color: rgba(231, 122, 58, 0.2);
                }
                
                /* Make calendar icon more touch-friendly */
                .book_tabel_item .input-group-addon {
                    cursor: pointer;
                    -webkit-tap-highlight-color: rgba(231, 122, 58, 0.2);
                    touch-action: manipulation;
                }
                
                .book_tabel_item select.wide {
                    width: 100%;
                    font-size: 14px;
                    padding: 12px 15px;
                    height: auto;
                }
                
                .book_now_btn {
                    width: 100%;
                    padding: 14px 20px;
                    font-size: 16px;
                    margin-top: 10px;
                }
                
                /* Accommodation Section - Mobile */
                .accomodation_area {
                    padding: 40px 0;
                }
                
                .accomodation_area .section_title h2 {
                    font-size: 28px !important;
                    margin-bottom: 20px !important;
                }
                
                .accomodation_area .section_title h2 span {
                    width: 50px !important;
                    height: 2px !important;
                }
                
                .accomodation_area .section_title p {
                    font-size: 14px !important;
                    padding: 0 15px !important;
                }
                
                .accomodation_item {
                    margin-bottom: 30px;
                }
                
                .accomodation_item .hotel_img {
                    min-height: 220px;
                }
                
                .accomodation_item h4 {
                    font-size: 18px;
                    margin-top: 15px;
                }
                
                .accomodation_item h5 {
                    font-size: 20px;
                }
                
                /* Facilities Section - Mobile */
                .facilities_area {
                    padding: 40px 0;
                }
                
                .facilities_area .section_title h2 {
                    font-size: 24px;
                }
                
                .facilities_item {
                    margin-bottom: 25px;
                    text-align: center;
                }
                
                .facilities_item i {
                    font-size: 40px;
                    margin-bottom: 15px;
                }
                
                .facilities_item h4 {
                    font-size: 18px;
                }
                
                /* Testimonials - Mobile */
                .testimonial_area {
                    padding: 40px 0;
                }
                
                .testimonial_area .section_title h2 {
                    font-size: 24px;
                }
                
                .testimonial_card_item {
                    margin: 10px 5px;
                }
                
                .testimonial_card_details {
                    padding: 15px;
                }
                
                .testimonial_card_avatar_center {
                    width: 60px;
                    height: 60px;
                    margin-bottom: 10px;
                }
                
                .testimonial_card_text p {
                    font-size: 12px;
                }
                
                .testimonial_card_author h5 {
                    font-size: 14px;
                }
                
                /* Blog/Categories Section - Mobile */
                .blog_categorie_area {
                    padding: 40px 0;
                }
                
                .blog_categorie_area .section_title h2 {
                    font-size: 24px;
                }
                
                .blog_categorie_area .categories_post {
                    margin-bottom: 20px;
                }
                
                .blog_categorie_area .categories_post img {
                    height: 200px;
                }
                
                /* Counter Section - Mobile */
                .counter_area {
                    padding: 40px 0;
                }
                
                .counter_area .counter_item {
                    margin-bottom: 30px;
                    text-align: center;
                }
                
                .counter_area .counter_item h3 {
                    font-size: 32px;
                }
                
                .counter_area .counter_item p {
                    font-size: 14px;
                }
                
                /* Gallery Section - Mobile */
                .gallery_area {
                    padding: 40px 0;
                }
                
                .gallery_area .section_title h2 {
                    font-size: 24px;
                }
                
                .gallery_area .btn {
                    font-size: 14px !important;
                    padding: 10px 20px !important;
                    margin-top: 0 !important;
                }
                
                .gallery_item {
                    margin-bottom: 15px;
                }
                
                /* Footer - Mobile */
                .footer_area {
                    padding: 40px 0 20px 0;
                }
                
                .footer_area .footer_widget {
                    margin-bottom: 30px;
                }
                
                .footer_area .footer_widget h6 {
                    font-size: 16px;
                    margin-bottom: 15px;
                }
                
                .footer_area .footer_widget p,
                .footer_area .footer_widget ul li {
                    font-size: 13px;
                }
                
                /* General Mobile Adjustments */
                .section_title {
                    margin-bottom: 30px;
                }
                
                .section_title h2 {
                    font-size: 24px;
                }
                
                .section_title p {
                    font-size: 14px;
                    padding: 0 10px;
                }
                
                .section_gap {
                    padding: 40px 0;
                }
                
                /* Button adjustments */
                .btn, .theme_btn, .button_hover {
                    padding: 12px 20px;
                    font-size: 14px;
                }
                
                /* Text adjustments */
                h1 { font-size: 28px; }
                h2 { font-size: 24px; }
                h3 { font-size: 20px; }
                h4 { font-size: 18px; }
                h5 { font-size: 16px; }
                h6 { font-size: 14px; }
                p { font-size: 14px; }
            }
            
            /* Small Mobile Devices (max-width: 480px) */
            @media (max-width: 480px) {
                .container {
                    padding-left: 10px;
                    padding-right: 10px;
                }
                
                .navbar-brand.logo_h img {
                    height: 75px !important;
                    max-width: 250px !important;
                }
                
                .banner_area {
                    min-height: 400px;
                    padding-top: 40px;
                    padding-bottom: 50px;
                }
                
                .banner_area .booking_table {
                    min-height: 400px;
                    padding-bottom: 50px;
                }
                
                .banner_area .banner_content h2 {
                    font-size: 26px !important;
                    margin-bottom: 12px !important;
                    font-weight: 700 !important;
                }
                
                .banner_area .banner_content h4 {
                    font-size: 16px !important;
                    margin-bottom: 18px !important;
                }
                
                .banner_area .banner_content h1 {
                    font-size: 24px;
                }
                
                .hotel_booking_table {
                    padding: 15px 10px;
                }
                
                .hotel_booking_table .col-md-3 h2 {
                    font-size: 20px;
                }
                
                .book_tabel_item .form-control,
                .book_tabel_item select.wide {
                    font-size: 13px;
                    padding: 10px 12px;
                    width: 100%;
                    box-sizing: border-box;
                }
                
                .book_tabel_item .input-group {
                    width: 100%;
                    display: flex;
                    flex-wrap: nowrap;
                }
                
                .book_tabel_item .input-group .form-control {
                    flex: 1 1 auto;
                    min-width: 0;
                }
                
                .book_tabel_item .input-group-addon {
                    flex: 0 0 auto;
                    min-width: 40px;
                    padding: 10px 12px;
                }
                
                .section_title h2 {
                    font-size: 22px;
                }
                
                .accomodation_item .hotel_img {
                    min-height: 200px;
                }
                
                /* Flatpickr calendar on small mobile */
                .flatpickr-calendar {
                    width: calc(100vw - 20px) !important;
                    max-width: 100% !important;
                    left: 50% !important;
                    transform: translateX(-50%) !important;
                    font-size: 14px;
                }
                
                .flatpickr-day {
                    height: 35px;
                    line-height: 35px;
                    font-size: 13px;
                }
            }
            
            /* Additional responsive fixes for date inputs - Mobile */
            @media (max-width: 767px) {
                /* Ensure inputs don't zoom on iOS */
                .book_tabel_item input[type="text"],
                .book_tabel_item input[type="date"],
                .book_tabel_item .form-control {
                    font-size: 16px !important; /* Prevents iOS zoom */
                }
                
                /* Make sure input-group doesn't cause horizontal scroll */
                .hotel_booking_table .row {
                    margin-left: 0 !important;
                    margin-right: 0 !important;
                }
                
                .hotel_booking_table .row > div {
                    padding-left: 5px !important;
                    padding-right: 5px !important;
                }
                
                /* Ensure form-controls are properly sized and ALWAYS visible */
                .date-wrapper .date-input {
                    width: 100% !important;
                    max-width: 100% !important;
                    box-sizing: border-box !important;
                    display: block !important;
                    visibility: visible !important;
                    opacity: 1 !important;
                    font-size: 16px !important; /* Prevents iOS zoom */
                }
                
                /* Mobile-specific placeholder adjustments */
                .date-wrapper .placeholder-text {
                    font-size: 16px !important;
                    left: 15px !important;
                }
                
                /* Better touch targets on mobile */
                .date-wrapper .date-input {
                    min-height: 44px !important; /* Better touch target */
                    padding: 14px 15px !important;
                }
                
                /* Force date input containers to be visible */
                .hotel_booking_table .row > .col-md-4:first-child,
                .hotel_booking_table .row > .col-md-4:first-child .book_tabel_item,
                .hotel_booking_table .row > .col-md-4:first-child .form-group,
                .hotel_booking_table .row > .col-md-4:first-child .input-group {
                    display: block !important;
                    visibility: visible !important;
                    opacity: 1 !important;
                }
                
                .hotel_booking_table .row > .col-md-4:first-child .input-group {
                    display: flex !important;
                }
                
                /* Flatpickr calendar responsive on mobile */
                .flatpickr-calendar {
                    width: 100% !important;
                    max-width: calc(100vw - 20px) !important;
                    left: 50% !important;
                    transform: translateX(-50%) !important;
                    margin-left: 0 !important;
                }
                
                /* Ensure date inputs are touch-friendly */
                .book_tabel_item .form-control[readonly] {
                    cursor: pointer;
                    -webkit-tap-highlight-color: rgba(231, 122, 58, 0.2);
                }
                
                /* Make calendar icon more touch-friendly */
                .book_tabel_item .input-group-addon {
                    cursor: pointer;
                    -webkit-tap-highlight-color: rgba(231, 122, 58, 0.2);
                    touch-action: manipulation;
                }
            }
            /* Loading spinner removed */
            
            /* ==================== EXTRA SMALL MOBILE RESPONSIVE (max-width: 576px) ==================== */
            @media (max-width: 576px) {
                /* Enjoy the Prime Food - Extra Small Screens */
                .blog_categorie_area .row {
                    justify-content: center;
                    display: flex;
                    flex-wrap: wrap;
                }
                
                .blog_categorie_area .row > div {
                    width: 100%;
                    max-width: 100%;
                    flex: 0 0 100%;
                    padding-left: 15px;
                    padding-right: 15px;
                }
                
                .blog_categorie_area .categories_post {
                    margin-bottom: 25px;
                    margin-left: auto;
                    margin-right: auto;
                    max-width: 100%;
                }
                
                .blog_categorie_area .categories_post img {
                    max-height: 200px;
                }
                
                .blog_categorie_area .categories_details {
                    padding: 15px 10px;
                }
                
                .blog_categorie_area .categories_text {
                    text-align: center;
                }
                
                .blog_categorie_area .categories_text h5 {
                    font-size: 16px;
                }
                
                .blog_categorie_area .categories_text p {
                    font-size: 13px;
                }
                
                .blog_categorie_area .section_title h2 {
                    font-size: 24px;
                }
                
                .blog_categorie_area .section_title p {
                    font-size: 13px;
                    padding: 0 10px;
                }
                
                /* General Mobile Improvements */
                .container {
                    padding-left: 15px;
                    padding-right: 15px;
                }
                
                .section_title h2 {
                    font-size: 24px !important;
                }
                
                .section_title p {
                    font-size: 14px !important;
                }
                
                /* Banner Area Mobile */
                .banner_content h2 {
                    font-size: 28px !important;
                }
                
                .banner_content h4 {
                    font-size: 18px !important;
                }
                
                /* Accommodation Cards Mobile */
                .accomodation_item {
                    margin-bottom: 30px;
                }
                
                /* Facilities Mobile */
                .facilities_item {
                    margin-bottom: 25px;
                    padding: 20px 15px;
                }
                
                /* About Section Mobile */
                .about_history_area .about_content h2 {
                    font-size: 24px !important;
                }
                
                /* Counter Section Mobile */
                .counter_item {
                    margin-bottom: 25px;
                }
                
                .counter_number {
                    font-size: 36px !important;
                }
                
                /* Testimonial Mobile */
                .testimonial_item {
                    padding: 20px 15px;
                }
                
                /* Video Section Mobile */
                .video_wrapper {
                    margin-top: 20px !important;
                }
            }
            
            /* Tablet Responsive (768px - 991px) */
            @media (min-width: 768px) and (max-width: 991px) {
                .blog_categorie_area .row {
                    justify-content: center;
                }
                
                .blog_categorie_area .categories_post {
                    margin-bottom: 30px;
                }
                
                .blog_categorie_area .categories_post img {
                    max-height: 280px;
                }
            }
            
            /* Ensure cards are centered on all mobile sizes */
            @media (max-width: 991px) {
                .blog_categorie_area .row > div {
                    display: flex;
                    justify-content: center;
                }
                
                .blog_categorie_area .categories_post {
                    width: 100%;
                    max-width: 400px;
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
                color: #fff0; /* Hide built-in date placeholder - transparent hex */
                display: block !important;
                line-height: 1.5 !important;
                transition: all 0.3s ease !important;
                cursor: pointer !important;
            }
            
            /* Hover state for better UX */
            .date-wrapper .date-input:hover {
                border-color: #e77a3a !important;
                background-color: #fffaf7 !important;
            }
            
            /* Focus and valid states with smooth transitions */
            .date-wrapper .date-input:focus,
            .date-wrapper .date-input:valid {
                color: #000000 !important; /* Show typed date when focused or valid */
                border-color: #e77a3a !important;
                box-shadow: 0 0 0 0.2rem rgba(231, 122, 58, 0.25) !important;
                outline: none !important;
                background-color: #ffffff !important;
            }
            
            /* Hide date text when empty */
            .date-wrapper .date-input::-webkit-datetime-edit {
                color: transparent;
                transition: color 0.3s ease;
            }
            
            .date-wrapper .date-input:not(:valid)::-webkit-datetime-edit {
                color: transparent !important;
            }
            
            /* Show date text when focused or valid */
            .date-wrapper .date-input:focus::-webkit-datetime-edit,
            .date-wrapper .date-input:valid::-webkit-datetime-edit {
                color: #000000 !important;
            }
            
            /* Room select styling to match date inputs */
            .date-wrapper select.date-input {
                color: #000000 !important; /* Show text normally for select */
                appearance: auto !important;
                -webkit-appearance: menulist !important;
                -moz-appearance: menulist !important;
            }
            
            .date-wrapper select.date-input:hover {
                border-color: #e77a3a !important;
                background-color: #fffaf7 !important;
            }
            
            .date-wrapper select.date-input:focus {
                border-color: #e77a3a !important;
                box-shadow: 0 0 0 0.2rem rgba(231, 122, 58, 0.25) !important;
                outline: none !important;
                background-color: #ffffff !important;
            }
            
            /* Enhanced calendar picker indicator */
            .date-wrapper .date-input::-webkit-calendar-picker-indicator {
                cursor: pointer;
                opacity: 1;
                padding: 4px;
                margin-left: 8px;
                transition: transform 0.2s ease;
            }
            
            .date-wrapper .date-input:hover::-webkit-calendar-picker-indicator {
                transform: scale(1.1);
            }
            
            .date-wrapper .date-input:focus::-webkit-calendar-picker-indicator {
                transform: scale(1.1);
            }
            
            /* Improved placeholder styling */
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
            
            /* Smooth placeholder fade out */
            .date-wrapper .date-input:focus + .placeholder-text,
            .date-wrapper .date-input:valid + .placeholder-text {
                opacity: 0;
                transform: translateY(-50%) scale(0.95);
            }
            
            /* Placeholder animation on hover */
            .date-wrapper:hover .placeholder-text {
                color: #e77a3a;
            }
            
            /* Ensure date-wrapper form-group spacing matches other form groups */
            .book_tabel_item .date-wrapper {
                margin-bottom: 15px;
            }
            
            /* Smooth transitions for all date wrapper interactions */
            .date-wrapper * {
                transition: all 0.3s ease;
            }
            
            /* Better visual feedback for active state */
            .date-wrapper .date-input:active {
                transform: scale(0.998);
            }
            
            /* Disabled state styling */
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
            
            /* Better focus-visible for keyboard navigation */
            .date-wrapper .date-input:focus-visible {
                outline: 2px solid #e77a3a !important;
                outline-offset: 2px !important;
            }
            
            /* Improved date text formatting */
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
            
            /* Smooth calendar icon animation */
            .date-wrapper .date-input::-webkit-calendar-picker-indicator:hover {
                background-color: rgba(231, 122, 58, 0.1);
                border-radius: 3px;
            }
            
            /* Better spacing between date inputs */
            .book_tabel_item .date-wrapper:not(:last-child) {
                margin-bottom: 15px;
            }
            
            /* Subtle shadow on focus for depth */
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
            
            /* Input Group Wrapper Hover (for select inside input-group) */
            .book_tabel_item .input-group:hover select.wide,
            .book_tabel_item .input-group:hover select#room_type {
                border-color: #e77a3a !important;
                background-color: #fffaf7 !important;
            }
            
            /* Nice-Select Hover Effects (for transformed select) */
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
            
            /* Nice-Select Dropdown Arrow Hover */
            .book_tabel_item .nice-select:hover .nice-select-arrow {
                border-color: #e77a3a !important;
            }
            
            /* Nice-Select Dropdown Options Hover */
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
            
        </style>
    </head>
    <body>
        <!-- Loading spinner removed -->
        
        @include('landing_page_views.partials.royal-header')
        
        <!--================Banner Area =================-->
        <section class="banner_area">
            <div class="booking_table d_flex align-items-center">
            	<div class="overlay bg-parallax bg-image-1 active" data-stellar-ratio="0.9" data-stellar-vertical-offset="0" data-background=""></div>
            	<div class="overlay bg-parallax bg-image-2" data-stellar-ratio="0.9" data-stellar-vertical-offset="0" data-background=""></div>
            	<div class="overlay bg-parallax bg-image-3" data-stellar-ratio="0.9" data-stellar-vertical-offset="0" data-background=""></div>
            	<div class="overlay bg-parallax bg-image-4" data-stellar-ratio="0.9" data-stellar-vertical-offset="0" data-background=""></div>
            	<div class="overlay bg-parallax bg-image-5" data-stellar-ratio="0.9" data-stellar-vertical-offset="0" data-background=""></div>
				<div class="container">
					<div class="banner_content text-center">
						<h2 style="color: #e77a3a;">Welcome to {{ config('app.name') }}</h2>
						<h4>Comfort in every stay.</h4>
						<a href="{{ route('booking.index') }}" class="btn theme_btn button_hover" style="margin-top: 20px;">Book Now</a>
					</div>
				</div>
            </div>
            <div class="hotel_booking_area position">
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
            </div>
        </section>
        <!--================Banner Area =================-->
        
        <!--================ Accomodation Area  =================-->
        <section class="accomodation_area section_gap">
            <div class="container">
                <div class="section_title text-center">
                    <h2 class="title_color" style="color: #e77a3a; font-size: 36px; margin-bottom: 15px; position: relative; display: inline-block; padding-bottom: 15px;">
                        Available Rooms
                        <span style="position: absolute; bottom: 0; left: 50%; transform: translateX(-50%); width: 60px; height: 3px; background: #e77a3a; border-radius: 2px;"></span>
                    </h2>
                    <p style="font-size: 16px; color: #777; margin-top: 20px;">Choose from our comfortable and well-appointed rooms designed for your perfect stay</p>
                </div>
                <div class="row mb_30">
                    <div class="col-lg-4 col-sm-6">
                        <div class="accomodation_item text-center">
                            <div class="hotel_img">
                                <img src="{{ asset('rooms/single.jpg') }}" alt="Single Room">
                                <a href="{{ route('booking.index') }}" class="btn theme_btn button_hover">Book Now</a>
                            </div>
                            <a href="{{ route('booking.index') }}"><h4 class="sec_h4">Single Room</h4></a>
                            <h5>Cozy & Comfortable</h5>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <div class="accomodation_item text-center">
                            <div class="hotel_img">
                                <img src="{{ asset('rooms/double.jpg') }}" alt="Double Room">
                                <a href="{{ route('booking.index') }}" class="btn theme_btn button_hover">Book Now</a>
                            </div>
                            <a href="{{ route('booking.index') }}"><h4 class="sec_h4">Double Room</h4></a>
                            <h5>Premium Experience</h5>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <div class="accomodation_item text-center">
                            <div class="hotel_img">
                                <img src="{{ asset('rooms/twins.jpg') }}" alt="Standard Twin Room">
                                <a href="{{ route('booking.index') }}" class="btn theme_btn button_hover">Book Now</a>
                            </div>
                            <a href="{{ route('booking.index') }}"><h4 class="sec_h4">Standard Twin Room</h4></a>
                            <h5>Luxury Redefined</h5>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--================ Accomodation Area  =================-->
        
        <!--================ Facilities Area  =================-->
        <section class="facilities_area section_gap">
            <div class="overlay bg-parallax" data-stellar-ratio="0.8" data-stellar-vertical-offset="0" data-background="">  
            </div>
            <div class="container">
                <div class="section_title text-center">
                    <h2 class="title_w">OUR FEATURES</h2>
                    <p style="color: white;">Experience comfort and convenience at {{ config('app.name') }}</p>
                </div>
                <div class="row mb_30">
                    <div class="col-lg-4 col-md-6">
                        <div class="facilities_item">
                            <h4 class="sec_h4"><i class="lnr lnr-home"></i>Clean and Comfortable Rooms</h4>
                            <p>Cozy rooms with bedding, air conditioning, hot showers, and Wi-Fi.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="facilities_item">
                            <h4 class="sec_h4"><i class="fa fa-tint"></i>Swimming Pool</h4>
                            <p>Clean, well-maintained pool for refreshing dips and relaxing moments anytime.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="facilities_item">
                            <h4 class="sec_h4"><i class="lnr lnr-dinner"></i>Restaurant and Mini Bar</h4>
                            <p>Delicious meals and drinks from fresh ingredients served in atmosphere.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="facilities_item">
                            <h4 class="sec_h4"><i class="lnr lnr-phone"></i>24/7 Front Desk Service</h4>
                            <p>Friendly staff available 24/7 for check-ins, travel and guest support.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="facilities_item">
                            <h4 class="sec_h4"><i class="lnr lnr-lock"></i>24/7 Security</h4>
                            <p>CCTV surveillance, electric fencing, boxes and professional security ensure protection.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="facilities_item">
                            <h4 class="sec_h4"><i class="lnr lnr-car"></i>Airport Transfer Service</h4>
                            <p>Reliable airport pick-up and drop-off ensuring smooth safe comfortable travel.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--================ Facilities Area  =================-->
        
        <!--================ Prime Location Area  =================-->
        <section class="about_history_area section_gap">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 d_flex align-items-center">
                        <div class="about_content">
                            <h2 class="title title_color">Prime Location</h2>
                            <p>Located along Sokoine road 1.5km from Moshi Town center; Our hotel makes a convenient place for your stay. Whether you are a business traveler, adventure seeker, or visiting family and friends, our location makes a perfect spot for your stay with nearest proximity to the town center and its important amenities. Public transport is easily accessible to and from town center. The connection of tarmac road makes it easier to commute with any type of automotive.</p>
                            
                            <div class="location_summary" style="margin-top: 30px;">
                                <h4 class="title_color" style="margin-bottom: 20px; font-size: 20px;">Location Summary</h4>
                                <ul style="list-style: none; padding: 0;">
                                    <li style="margin-bottom: 15px; padding-left: 25px; position: relative;">
                                        <i class="lnr lnr-map-marker" style="position: absolute; left: 0; color: #e77a3a; font-size: 18px;"></i>
                                        <strong style="color: #222222;">1.5km</strong> approximately <strong style="color: #222222;">7 minutes' drive</strong> to Moshi town center
                                    </li>
                                    <li style="margin-bottom: 15px; padding-left: 25px; position: relative;">
                                        <i class="lnr lnr-map-marker" style="position: absolute; left: 0; color: #e77a3a; font-size: 18px;"></i>
                                        <strong style="color: #222222;">45 minutes' drive</strong> to Kilimanjaro International Airport
                                    </li>
                                    <li style="margin-bottom: 15px; padding-left: 25px; position: relative;">
                                        <i class="lnr lnr-map-marker" style="position: absolute; left: 0; color: #e77a3a; font-size: 18px;"></i>
                                        <strong style="color: #222222;">1-hour drive</strong> to Mount Kilimanjaro
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h2 class="title title_color">Take a Prime Tour</h2>
                        <div class="video_wrapper" style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 10px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15); margin-top: 30px;">
                            <video 
                                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; border: 0;"
                                controls
                                autoplay
                                muted
                                loop
                                playsinline
                                preload="auto">
                                <source src="{{ asset('royal-master/video/hotel_tour.mp4') }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                        <div style="text-align: center; margin-top: 20px;">
                            <a href="https://www.instagram.com/primeland_hotel?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" target="_blank" class="btn theme_btn button_hover">Explore More</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--================ Prime Location Area  =================-->
        
        <!--================ Counter Section  =================-->
        <section class="counter_area section_gap" style="position: relative; overflow: hidden;">
            <div class="counter-overlay"></div>
            <div class="container" style="position: relative; z-index: 2;">
                <div class="row">
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="counter_item text-center">
                            <div class="counter_icon">
                                <i class="fa fa-bed"></i>
                            </div>
                            <h2 class="counter_number" data-target="50" data-suffix="+">0+</h2>
                            <p>Rooms Available</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="counter_item text-center">
                            <div class="counter_icon">
                                <i class="fa fa-users"></i>
                            </div>
                            <h2 class="counter_number" data-target="5000" data-suffix="+">0+</h2>
                            <p>Happy Guests</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="counter_item text-center">
                            <div class="counter_icon">
                                <i class="fa fa-trophy"></i>
                            </div>
                            <h2 class="counter_number" data-target="15" data-suffix="+">0+</h2>
                            <p>Years Experience</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="counter_item text-center">
                            <div class="counter_icon">
                                <i class="fa fa-star"></i>
                            </div>
                            <h2 class="counter_number" data-target="4.8" data-suffix="">0</h2>
                            <p>Average Rating</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--================ Counter Section  =================-->
        
        <!--================ Enjoy the Prime Food Area  =================-->
        <section class="blog_categorie_area section_gap" style="margin-top: 20px;">
            <div class="container">
                <div class="section_title text-center">
                    <h2 class="title_color">Enjoy the Prime Food</h2>
                    <p>Savor delicious meals prepared with fresh, locally sourced ingredients</p>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                        <div class="categories_post">
                            <img src="{{ asset('hotel_gallery/coffee_.jpg') }}" alt="Breakfast">
                            <div class="categories_details">
                                <div class="categories_text">
                                    <a href="#"><h5>Breakfast</h5></a>
                                    <div class="border_line"></div>
                                    <p>Start your day with our delicious breakfast selection</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                        <div class="categories_post">
                            <img src="{{ asset('hotel_gallery/restaurant_.jpg') }}" alt="Lunch">
                            <div class="categories_details">
                                <div class="categories_text">
                                    <a href="#"><h5>Lunch</h5></a>
                                    <div class="border_line"></div>
                                    <p>Enjoy our hearty lunch menu with local and international dishes</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                        <div class="categories_post">
                            <img src="{{ asset('hotel_gallery/restaurant_.jpg') }}" alt="Dinner">
                            <div class="categories_details">
                                <div class="categories_text">
                                    <a href="#"><h5>Dinner</h5></a>
                                    <div class="border_line"></div>
                                    <p>Experience fine dining with our exquisite dinner menu</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--================ Enjoy the Prime Food Area  =================-->
        
        <!--================ Testimonial Area  =================-->
        <section class="testimonial_area section_gap" style="position: relative; overflow: hidden; padding: 80px 0;">
            <div class="testimonial-bg-image"></div>
            <div class="testimonial-overlay-dark" style="background: rgba(0, 0, 0, 0.75);"></div>
            <div class="container" style="position: relative; z-index: 2;">
                <div class="section_title text-center" style="margin-bottom: 50px;">
                    <h2 class="title_color" style="color: #fff; font-size: 36px; font-weight: 700; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 1px;">Our Clients Reviews</h2>
                    <p style="color: rgba(255,255,255,0.8); font-size: 16px; margin: 0; max-width: 600px; margin-left: auto; margin-right: auto;">Hear what our guests have to say</p>
                </div>
                <div class="testimonial_slider owl-carousel" id="testimonialCarousel">
                    
                    <!-- Review 1: Serah (Kenya) -->
                    <div class="testimonial_card_item box_shadow_hover">
                        <div class="source_badge"><i class="fa fa-google"></i> Google Review</div>
                        <div class="testimonial_card_details">
                            <div class="testimonial_quote_icon">
                                <i class="fa fa-quote-left"></i>
                            </div>
                            <div class="testimonial_card_author_info">
                                <div class="author_avatar_flag">
                                    <img src="https://flagcdn.com/w160/ke.png" alt="Kenya">
                                </div>
                                <div class="author_details">
                                    <h5>Serah</h5>
                                    <div class="rating_stars">
                                        <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                                    </div>
                                    <span class="guest_location">Kenya</span>
                                </div>
                            </div>
                            <div class="testimonial_card_text_content">
                                <p>"The staff are helpful and understanding. The hotel is very clean. The space is beautiful..."</p>
                                <button class="read_more_btn" 
                                        data-author="Serah" 
                                        data-avatar="https://flagcdn.com/w160/ke.png"
                                        data-location="Kenya"
                                        data-full-text="The staff are helpful and understanding. The hotel is very clean. The space is beautiful, and it offers calmness and is very quiet.">
                                    Read Full Review
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Review 2: Sharon Jeruto (Kenya) -->
                    <div class="testimonial_card_item box_shadow_hover">
                        <div class="source_badge"><i class="fa fa-google"></i> Google Review</div>
                        <div class="testimonial_card_details">
                            <div class="testimonial_quote_icon">
                                <i class="fa fa-quote-left"></i>
                            </div>
                            <div class="testimonial_card_author_info">
                                <div class="author_avatar_flag">
                                    <img src="https://flagcdn.com/w160/ke.png" alt="Kenya">
                                </div>
                                <div class="author_details">
                                    <h5>Sharon Jeruto</h5>
                                    <div class="rating_stars">
                                        <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                                    </div>
                                    <span class="guest_location">Kenya</span>
                                </div>
                            </div>
                            <div class="testimonial_card_text_content">
                                <p>"The staff from the gate, to the reception to the kitchen and the servers are just but amaazzzing..."</p>
                                <button class="read_more_btn" 
                                        data-author="Sharon Jeruto" 
                                        data-avatar="https://flagcdn.com/w160/ke.png"
                                        data-location="Kenya"
                                        data-full-text="The staff from the gate, to the reception to the kitchen and the servers are just but amaazzzing. Special mention to Elfas, Jackie, Godfrey, Nice, and Pamelina. The food is to die for, and the attention to the guests is top notch. The rooms are very clean and the toiletries and beddings are very high standard. By the way, you can see Mt. Kilimanjaro from the upper deck. My family has such an amazing amazing time here.">
                                    Read Full Review
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Review 3: Arina (Moldova) -->
                    <div class="testimonial_card_item box_shadow_hover">
                        <div class="source_badge"><i class="fa fa-google"></i> Google Review</div>
                        <div class="testimonial_card_details">
                            <div class="testimonial_quote_icon">
                                <i class="fa fa-quote-left"></i>
                            </div>
                            <div class="testimonial_card_author_info">
                                <div class="author_avatar_flag">
                                    <img src="https://flagcdn.com/w160/md.png" alt="Moldova">
                                </div>
                                <div class="author_details">
                                    <h5>Arina</h5>
                                    <div class="rating_stars">
                                        <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                                    </div>
                                    <span class="guest_location">Moldova</span>
                                </div>
                            </div>
                            <div class="testimonial_card_text_content">
                                <p>"The best food in Moshi. We liked it here. Very friendly staff, clean rooms, low prices for food..."</p>
                                <button class="read_more_btn" 
                                        data-author="Arina" 
                                        data-avatar="https://flagcdn.com/w160/md.png"
                                        data-location="Moldova"
                                        data-full-text="The best food in Moshi. We liked it here. Very friendly staff, clean rooms, low prices for food and more than that, the food was fresh and very delicious. The lady at the reception was very nice and allowed us to leave our luggage at the hotel while we were away in the mountains, without paying anything extra. I mention the food once again, which is super delicious!">
                                    Read Full Review
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Review 4: Maria Goretti (Norway) -->
                    <div class="testimonial_card_item box_shadow_hover">
                        <div class="source_badge"><i class="fa fa-google"></i> Google Review</div>
                        <div class="testimonial_card_details">
                            <div class="testimonial_quote_icon">
                                <i class="fa fa-quote-left"></i>
                            </div>
                            <div class="testimonial_card_author_info">
                                <div class="author_avatar_flag">
                                    <img src="https://flagcdn.com/w160/no.png" alt="Norway">
                                </div>
                                <div class="author_details">
                                    <h5>Maria Goretti</h5>
                                    <div class="rating_stars">
                                        <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                                    </div>
                                    <span class="guest_location">Norway</span>
                                </div>
                            </div>
                            <div class="testimonial_card_text_content">
                                <p>"A home away from home! Location, room decor, service, tasty local food and staff's friendliness..."</p>
                                <button class="read_more_btn" 
                                        data-author="Maria Goretti" 
                                        data-avatar="https://flagcdn.com/w160/no.png"
                                        data-location="Norway"
                                        data-full-text="A home away from home! Location, room decor, service, tasty local food and staff's friendliness and kindness. They take care of you as if you were family. Thanks to manager Jackie for taking me to the hospital, staying with me all the way through and ensuring that everything went well when I got sick during my stay. Blessings!">
                                    Read Full Review
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Review 5: Peter Heinz (United States) -->
                    <div class="testimonial_card_item box_shadow_hover">
                        <div class="source_badge"><i class="fa fa-google"></i> Google Review</div>
                        <div class="testimonial_card_details">
                            <div class="testimonial_quote_icon">
                                <i class="fa fa-quote-left"></i>
                            </div>
                            <div class="testimonial_card_author_info">
                                <div class="author_avatar_flag">
                                    <img src="https://flagcdn.com/w160/us.png" alt="United States">
                                </div>
                                <div class="author_details">
                                    <h5>Peter Heinz</h5>
                                    <div class="rating_stars">
                                        <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                                    </div>
                                    <span class="guest_location">United States</span>
                                </div>
                            </div>
                            <div class="testimonial_card_text_content">
                                <p>"Great location near public transportation. The location made it easy for me to visit nearby attractions..."</p>
                                <button class="read_more_btn" 
                                        data-author="Peter Heinz" 
                                        data-avatar="https://flagcdn.com/w160/us.png"
                                        data-location="United States"
                                        data-full-text="Great location near public transportation. The location made it easy for me to visit nearby attractions and the town center. Highly recommend if you are looking for a place where you can easily navigate to places in and around Moshi.">
                                    Read Full Review
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Review 6: Katie (United States) -->
                    <div class="testimonial_card_item box_shadow_hover">
                        <div class="source_badge"><i class="fa fa-google"></i> Google Review</div>
                        <div class="testimonial_card_details">
                            <div class="testimonial_quote_icon">
                                <i class="fa fa-quote-left"></i>
                            </div>
                            <div class="testimonial_card_author_info">
                                <div class="author_avatar_flag">
                                    <img src="https://flagcdn.com/w160/us.png" alt="United States">
                                </div>
                                <div class="author_details">
                                    <h5>Katie</h5>
                                    <div class="rating_stars">
                                        <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                                    </div>
                                    <span class="guest_location">United States</span>
                                </div>
                            </div>
                            <div class="testimonial_card_text_content">
                                <p>"Peaceful Oasis in Moshi. We loved this relaxing Oasis and stayed here the night before and after our Kili adventure..."</p>
                                <button class="read_more_btn" 
                                        data-author="Katie" 
                                        data-avatar="https://flagcdn.com/w160/us.png"
                                        data-location="United States"
                                        data-full-text="Peaceful Oasis in Moshi. We loved this relaxing Oasis and stayed here the night before and night after our Kili adventure. The staff are incredible and were super attentive, we left bags here while we were hiking with no issues, the free breakfast is great and we ate a few other meals there and they are decent and good value. Would 100% stay again.">
                                    Read Full Review
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Review 7: Jonathan (Belgium) -->
                    <div class="testimonial_card_item box_shadow_hover">
                        <div class="source_badge"><i class="fa fa-google"></i> Google Review</div>
                        <div class="testimonial_card_details">
                            <div class="testimonial_quote_icon">
                                <i class="fa fa-quote-left"></i>
                            </div>
                            <div class="testimonial_card_author_info">
                                <div class="author_avatar_flag">
                                    <img src="https://flagcdn.com/w160/be.png" alt="Belgium">
                                </div>
                                <div class="author_details">
                                    <h5>Jonathan</h5>
                                    <div class="rating_stars">
                                        <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                                    </div>
                                    <span class="guest_location">Belgium</span>
                                </div>
                            </div>
                            <div class="testimonial_card_text_content">
                                <p>"A comfortable and welcoming place to spend a few days in Moshi. The staff are incredibly helpful, kind and warm..."</p>
                                <button class="read_more_btn" 
                                        data-author="Jonathan" 
                                        data-avatar="https://flagcdn.com/w160/be.png"
                                        data-location="Belgium"
                                        data-full-text="A comfortable and welcoming place to spend a few days in Moshi. The staff are incredibly helpful, kind and warm. The bed is comfortable. Air conditioning works well. The hotel is very clean. Breakfast was varied each morning and of good quality. Great swimming pool too. Apart from the fact that there is no wardrobe/shelf in the room to store clothes, everything was spotless!">
                                    Read Full Review
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Review 8: Saara (Finland) -->
                    <div class="testimonial_card_item box_shadow_hover">
                        <div class="source_badge"><i class="fa fa-google"></i> Google Review</div>
                        <div class="testimonial_card_details">
                            <div class="testimonial_quote_icon">
                                <i class="fa fa-quote-left"></i>
                            </div>
                            <div class="testimonial_card_author_info">
                                <div class="author_avatar_flag">
                                    <img src="https://flagcdn.com/w160/fi.png" alt="Finland">
                                </div>
                                <div class="author_details">
                                    <h5>Saara</h5>
                                    <div class="rating_stars">
                                        <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                                    </div>
                                    <span class="guest_location">Finland</span>
                                </div>
                            </div>
                            <div class="testimonial_card_text_content">
                                <p>"A really good boutique hotel with REALLY good beds and pillows. The swimming pool was lovely!..."</p>
                                <button class="read_more_btn" 
                                        data-author="Saara" 
                                        data-avatar="https://flagcdn.com/w160/fi.png"
                                        data-location="Finland"
                                        data-full-text="A really good boutique hotel with REALLY good beds and pillows. The swimming pool was lovely! The breakfast was varied and international. The staff was always really friendly and you also got a breakfast box if you left early.">
                                    Read Full Review
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- Testimonial Modal -->
        <div class="modal fade" id="reviewModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 100000;">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content" style="border-radius: 15px; border: none; overflow: hidden;">
                    <div class="modal-header" style="background: #f8f9fa; border-bottom: 1px solid #eee; padding: 15px 25px;">
                        <h5 class="modal-title" style="font-weight: 700; color: #333;">Client Review</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 0.5;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="padding: 30px;">
                        <div class="text-center mb-4">
                            <div style="width: 80px; height: 80px; border-radius: 50%; border: 3px solid #e77a3a; padding: 2px; margin: 0 auto 15px; overflow: hidden;">
                                <img id="modalAvatar" src="" alt="Guest" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                            </div>
                            <h4 id="modalAuthor" style="font-weight: 700; margin-bottom: 5px;"></h4>
                            <p id="modalLocation" style="color: #777; font-size: 13px; text-transform: uppercase; letter-spacing: 1px;"></p>
                            <div class="rating_stars" style="font-size: 14px; margin-top: 10px;">
                                <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                            </div>
                        </div>
                        <div style="background: #fffaf7; padding: 20px; border-radius: 10px; border-left: 4px solid #e77a3a;">
                            <i class="fa fa-quote-left" style="color: #e77a3a; opacity: 0.3; font-size: 20px; display: block; margin-bottom: 10px;"></i>
                            <p id="modalText" style="font-size: 16px; line-height: 1.8; color: #444; font-style: italic; margin: 0;"></p>
                        </div>
                        <div class="text-center mt-4">
                            <span style="font-size: 12px; color: #999;"><i class="fa fa-google" style="color: #4285F4; margin-right: 5px;"></i> Verified Google Review</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--================ Testimonial Area  =================-->
        <!--================ Gallery Area  =================-->
        <section class="gallery_area" style="padding: 40px 0 0 0; margin-bottom: 0;">
            <div class="container" style="padding-bottom: 15px;">
                <div class="section_title text-center" style="margin-bottom: 20px;">
                    <h2 class="title_color">Hotel Gallery</h2>
                    <p>Take a visual tour of our beautiful hotel and facilities</p>
                </div>
            </div>
            <div class="gallery_slider owl-carousel" style="width: 100%; margin: 0; padding: 0;">
                    <div class="gallery_item" style="display: block;">
                        <div class="gallery_img" style="position: relative; overflow: hidden; width: 100%; height: 300px; display: block;">
                            <img class="img-fluid" src="{{ asset('hotel_gallery/coffee_.jpg') }}" alt="Hotel Coffee Area" style="width: 100%; height: 100%; object-fit: cover; display: block;" onerror="this.style.display='none';">
                            <div class="hover">
                                <i class="lnr lnr-frame-expand"></i>
                            </div>
                        </div>
                    </div>
                    <div class="gallery_item" style="display: block;">
                        <div class="gallery_img" style="position: relative; overflow: hidden; width: 100%; height: 300px; display: block;">
                            <img class="img-fluid" src="{{ asset('hotel_gallery/night view_(1).jpg') }}" alt="Hotel Night View" style="width: 100%; height: 100%; object-fit: cover; display: block;" onerror="this.style.display='none';">
                            <div class="hover">
                                <i class="lnr lnr-frame-expand"></i>
                            </div>
                        </div>
                    </div>
                    <div class="gallery_item" style="display: block;">
                        <div class="gallery_img" style="position: relative; overflow: hidden; width: 100%; height: 300px; display: block;">
                            <img class="img-fluid" src="{{ asset('hotel_gallery/reception_.jpg') }}" alt="Hotel Reception" style="width: 100%; height: 100%; object-fit: cover; display: block;" onerror="this.style.display='none';">
                            <div class="hover">
                                <i class="lnr lnr-frame-expand"></i>
                            </div>
                        </div>
                    </div>
                    <div class="gallery_item" style="display: block;">
                        <div class="gallery_img" style="position: relative; overflow: hidden; width: 100%; height: 300px; display: block;">
                            <img class="img-fluid" src="{{ asset('hotel_gallery/room_.jpg') }}" alt="Hotel Room" style="width: 100%; height: 100%; object-fit: cover; display: block;" onerror="this.style.display='none';">
                            <div class="hover">
                                <i class="lnr lnr-frame-expand"></i>
                            </div>
                        </div>
                    </div>
                    <div class="gallery_item" style="display: block;">
                        <div class="gallery_img" style="position: relative; overflow: hidden; width: 100%; height: 300px; display: block;">
                            <img class="img-fluid" src="{{ asset('hotel_gallery/room_(3).jpg') }}" alt="Hotel Room 3" style="width: 100%; height: 100%; object-fit: cover; display: block;" onerror="this.style.display='none';">
                            <div class="hover">
                                <i class="lnr lnr-frame-expand"></i>
                            </div>
                        </div>
                    </div>
                    <div class="gallery_item" style="display: block;">
                        <div class="gallery_img" style="position: relative; overflow: hidden; width: 100%; height: 300px; display: block;">
                            <img class="img-fluid" src="{{ asset('hotel_gallery/room_(4).jpg') }}" alt="Hotel Room 4" style="width: 100%; height: 100%; object-fit: cover; display: block;" onerror="this.style.display='none';">
                            <div class="hover">
                                <i class="lnr lnr-frame-expand"></i>
                            </div>
                        </div>
                    </div>
                    <div class="gallery_item" style="display: block;">
                        <div class="gallery_img" style="position: relative; overflow: hidden; width: 100%; height: 300px; display: block;">
                            <img class="img-fluid" src="{{ asset('hotel_gallery/room_(5).jpg') }}" alt="Hotel Room 5" style="width: 100%; height: 100%; object-fit: cover; display: block;" onerror="this.style.display='none';">
                            <div class="hover">
                                <i class="lnr lnr-frame-expand"></i>
                            </div>
                        </div>
                    </div>
                    <div class="gallery_item" style="display: block;">
                        <div class="gallery_img" style="position: relative; overflow: hidden; width: 100%; height: 300px; display: block;">
                            <img class="img-fluid" src="{{ asset('hotel_gallery/room_(6).jpg') }}" alt="Hotel Room 6" style="width: 100%; height: 100%; object-fit: cover; display: block;" onerror="this.style.display='none';">
                            <div class="hover">
                                <i class="lnr lnr-frame-expand"></i>
                            </div>
                        </div>
                    </div>
                    <div class="gallery_item" style="display: block;">
                        <div class="gallery_img" style="position: relative; overflow: hidden; width: 100%; height: 300px; display: block;">
                            <img class="img-fluid" src="{{ asset('hotel_gallery/room_(7).jpg') }}" alt="Hotel Room 7" style="width: 100%; height: 100%; object-fit: cover; display: block;" onerror="this.style.display='none';">
                            <div class="hover">
                                <i class="lnr lnr-frame-expand"></i>
                            </div>
                        </div>
                    </div>
                    <div class="gallery_item" style="display: block;">
                        <div class="gallery_img" style="position: relative; overflow: hidden; width: 100%; height: 300px; display: block;">
                            <img class="img-fluid" src="{{ asset('hotel_gallery/swimming floating tray_.jpg') }}" alt="Swimming Pool with Floating Tray" style="width: 100%; height: 100%; object-fit: cover; display: block;" onerror="this.style.display='none';">
                            <div class="hover">
                                <i class="lnr lnr-frame-expand"></i>
                            </div>
                        </div>
                    </div>
                    <div class="gallery_item" style="display: block;">
                        <div class="gallery_img" style="position: relative; overflow: hidden; width: 100%; height: 300px; display: block;">
                            <img class="img-fluid" src="{{ asset('hotel_gallery/swimming view_(1).jpg') }}" alt="Swimming Pool View 1" style="width: 100%; height: 100%; object-fit: cover; display: block;" onerror="this.style.display='none';">
                            <div class="hover">
                                <i class="lnr lnr-frame-expand"></i>
                            </div>
                        </div>
                    </div>
                    <div class="gallery_item" style="display: block;">
                        <div class="gallery_img" style="position: relative; overflow: hidden; width: 100%; height: 300px; display: block;">
                            <img class="img-fluid" src="{{ asset('hotel_gallery/swimming view_(2).jpg') }}" alt="Swimming Pool View 2" style="width: 100%; height: 100%; object-fit: cover; display: block;" onerror="this.style.display='none';">
                            <div class="hover">
                                <i class="lnr lnr-frame-expand"></i>
                            </div>
                        </div>
                    </div>
                </div>
            <div class="container" style="padding-top: 30px; padding-bottom: 40px; text-align: center;">
                <a href="https://www.instagram.com/primeland_hotel?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" target="_blank" class="btn theme_btn button_hover" style="display: inline-block;">
                    View Full Gallery
                    <i class="fa fa-arrow-right" style="margin-left: 8px;"></i>
                </a>
            </div>
        </section>
        <!--================ Gallery Area  =================-->
        
        @include('landing_page_views.partials.royal-footer')
        
        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="{{ asset('royal-master/js/jquery-3.2.1.min.js') }}"></script>
        <script src="{{ asset('royal-master/js/popper.js') }}"></script>
        <script src="{{ asset('royal-master/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('royal-master/vendors/owl-carousel/owl.carousel.min.js') }}"></script>
        <script src="{{ asset('royal-master/js/jquery.ajaxchimp.min.js') }}"></script>
        <script src="{{ asset('royal-master/js/mail-script.js') }}"></script>
        <script src="{{ asset('royal-master/vendors/nice-select/js/jquery.nice-select.js') }}"></script>
        <script src="{{ asset('royal-master/js/stellar.js') }}"></script>
        <script src="{{ asset('royal-master/vendors/lightbox/simpleLightbox.min.js') }}"></script>
        <script src="{{ asset('royal-master/js/custom.js') }}"></script>
        
        <script type="text/javascript">
        // Background Image Rotation
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
            
            // Form submission handler - ensure form submits properly to server
            var availabilityForm = document.getElementById('availabilityForm');
            if (availabilityForm) {
                availabilityForm.addEventListener('submit', function(e) {
                    // Validate dates before submission
                    var checkInInput = document.getElementById('check_in');
                    var checkOutInput = document.getElementById('check_out');
                    
                    if (!checkInInput.value || !checkOutInput.value) {
                        e.preventDefault();
                        alert('Please select both arrival and departure dates.');
                        return false;
                    }
                    
                    var checkInDate = new Date(checkInInput.value);
                    var checkOutDate = new Date(checkOutInput.value);
                    
                    if (checkOutDate <= checkInDate) {
                        e.preventDefault();
                        alert('Departure date must be after arrival date.');
                        return false;
                    }
                    
                    // Form will submit normally to server (GET request to booking.index)
                    // No need to prevent default - let it submit naturally
                });
            }
            
            var backgroundImages = document.querySelectorAll('.banner_area .bg-parallax');
            var currentIndex = 0;
            
            if (backgroundImages.length > 0) {
                function rotateBackground() {
                    // Remove active class from all images
                    backgroundImages.forEach(function(img) {
                        img.classList.remove('active');
                    });
                    
                    // Add active class to current image
                    backgroundImages[currentIndex].classList.add('active');
                    
                    // Move to next image
                    currentIndex = (currentIndex + 1) % backgroundImages.length;
                }
                
                // Start rotation after 3 seconds, then rotate every 5 seconds
                setTimeout(function() {
                    rotateBackground();
                    setInterval(rotateBackground, 5000);
                }, 3000);
            }
            
            // Animated Counter
            function animateCounter() {
                var counters = document.querySelectorAll('.counter_number');
                var hasAnimated = false;
                
                function updateCounter(counter) {
                    var target = parseFloat(counter.getAttribute('data-target'));
                    var suffix = counter.getAttribute('data-suffix') || '';
                    var currentText = counter.innerText.replace(/[^0-9.]/g, '');
                    var current = parseFloat(currentText);
                    if (isNaN(current)) current = 0;
                    
                    var isDecimal = target % 1 !== 0;
                    // For decimals, use smaller increments for smoother animation
                    var increment = isDecimal ? Math.max(target / 60, 0.08) : Math.max(target / 100, 1);
                    
                    if (current < target - 0.01) { // Use small threshold for decimals
                        current += increment;
                        // Make sure we don't exceed the target
                        if (current > target) {
                            current = target;
                        }
                        
                        if (isDecimal) {
                            counter.innerText = current.toFixed(1) + suffix;
                        } else {
                            counter.innerText = Math.floor(current) + suffix;
                        }
                        
                        setTimeout(function() {
                            updateCounter(counter);
                        }, 25);
                    } else {
                        // Final value - ensure exact target
                        if (isDecimal) {
                            counter.innerText = target.toFixed(1) + suffix;
                        } else {
                            counter.innerText = target + suffix;
                        }
                    }
                }
                
                function checkVisibility() {
                    var counterSection = document.querySelector('.counter_area');
                    if (!counterSection || hasAnimated) return;
                    
                    var rect = counterSection.getBoundingClientRect();
                    var isVisible = rect.top < window.innerHeight && rect.bottom > 0;
                    
                    if (isVisible) {
                        hasAnimated = true;
                        counters.forEach(function(counter) {
                            updateCounter(counter);
                        });
                    }
                }
                
                // Check on scroll
                window.addEventListener('scroll', checkVisibility);
                // Check on load
                checkVisibility();
            }
            
            
            animateCounter();
            
            // Testimonial Modal Logic
            $('.read_more_btn').on('click', function(e) {
                e.preventDefault();
                
                var author = $(this).data('author');
                var avatar = $(this).data('avatar');
                var location = $(this).data('location');
                var fullText = $(this).data('full-text');
                
                $('#modalAuthor').text(author);
                $('#modalAvatar').attr('src', avatar);
                $('#modalLocation').text(location);
                $('#modalText').text(fullText);
                
                $('#reviewModal').modal('show');
            });
            // Mobile Menu Improvements - MOVED TO royal-scripts.blade.php
        });
        </script>
        
        <!-- Loading spinner JavaScript removed -->
        
        @include('landing_page_views.partials.chat-widgets')
    </body>
</html>
