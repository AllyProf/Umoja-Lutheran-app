<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="icon" href="{{ asset('royal-master/image/logo/Logo.png') }}" type="image/png">
        <title>Accommodation - PrimeLand Hotel</title>
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
        </style>
    </head>
    <body>
        @include('landing_page_views.partials.royal-header')
        
        <!--================Breadcrumb Area =================-->
        <section class="breadcrumb_area">
            <div class="overlay bg-parallax" data-stellar-ratio="0.8" data-stellar-vertical-offset="0" data-background=""></div>
            <div class="container">
                <div class="page-cover text-center">
                    <h2 class="page-cover-tittle" style="color: #e77a3a;">Accomodation</h2>
                    <ol class="breadcrumb">
                        <li><a href="{{ url('/') }}">Home</a></li>
                        <li class="active">Accomodation</li>
                    </ol>
                </div>
            </div>
        </section>
        <!--================Breadcrumb Area =================-->
        
        <!--================ Accomodation Area  =================-->
        <section class="accomodation_area section_gap">
            <div class="container">
                <div class="section_title text-center">
                    <h2 class="title_color">Special Accomodation</h2>
                    <p>We all live in an age that belongs to the young at heart. Life that is becoming extremely fast,</p>
                </div>
                <div class="row mb_30">
                    <div class="col-lg-3 col-sm-6">
                        <div class="accomodation_item text-center">
                            <div class="hotel_img">
                                <img src="{{ asset('royal-master/image/rooms/room1.jpg') }}" alt="">
                                <a href="{{ route('booking.index') }}" class="btn theme_btn button_hover">Book Now</a>
                            </div>
                            <a href="{{ route('booking.index') }}"><h4 class="sec_h4">Double Deluxe Room</h4></a>
                            <h5>$250<small>/night</small></h5>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="accomodation_item text-center">
                            <div class="hotel_img">
                                <img src="{{ asset('royal-master/image/rooms/room2.jpg') }}" alt="">
                                <a href="{{ route('booking.index') }}" class="btn theme_btn button_hover">Book Now</a>
                            </div>
                            <a href="{{ route('booking.index') }}"><h4 class="sec_h4">Single Deluxe Room</h4></a>
                            <h5>$200<small>/night</small></h5>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="accomodation_item text-center">
                            <div class="hotel_img">
                                <img src="{{ asset('royal-master/image/rooms/room3.jpg') }}" alt="">
                                <a href="{{ route('booking.index') }}" class="btn theme_btn button_hover">Book Now</a>
                            </div>
                            <a href="{{ route('booking.index') }}"><h4 class="sec_h4">Honeymoon Suit</h4></a>
                            <h5>$750<small>/night</small></h5>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="accomodation_item text-center">
                            <div class="hotel_img">
                                <img src="{{ asset('royal-master/image/rooms/room4.jpg') }}" alt="">
                                <a href="{{ route('booking.index') }}" class="btn theme_btn button_hover">Book Now</a>
                            </div>
                            <a href="{{ route('booking.index') }}"><h4 class="sec_h4">Economy Double</h4></a>
                            <h5>$200<small>/night</small></h5>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--================ Accomodation Area  =================-->
        
        <!--================Booking Tabel Area =================-->
        <section class="hotel_booking_area">
            <div class="container">
                <div class="row hotel_booking_table">
                    <div class="col-md-3">
                        <h2 style="color: #e77a3a;">Book<br> Your Room</h2>
                    </div>
                    <div class="col-md-9">
                        <div class="boking_table">
                            <form action="{{ route('booking.index') }}" method="GET">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="book_tabel_item">
                                            <div class="form-group">
                                                <div class='input-group date' id='datetimepicker11'>
                                                    <input type='text' class="form-control" name="check_in" placeholder="Arrival Date" required/>
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-calendar" aria-hidden="true"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class='input-group date' id='datetimepicker1'>
                                                    <input type='text' class="form-control" name="check_out" placeholder="Departure Date" required/>
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-calendar" aria-hidden="true"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="book_tabel_item">
                                            <div class="input-group">
                                                <select class="wide" name="room_type">
                                                    <option value="">All Room Types</option>
                                                    <option value="Single">Single</option>
                                                    <option value="Double">Double</option>
                                                    <option value="Twins">Standard Twin Room</option>
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
        
        <!--================ Accomodation Area  =================-->
        <section class="accomodation_area section_gap">
            <div class="container">
                <div class="section_title text-center">
                    <h2 class="title_color">Normal Accomodation</h2>
                    <p>We all live in an age that belongs to the young at heart. Life that is becoming extremely fast,</p>
                </div>
                <div class="row accomodation_two">
                    <div class="col-lg-3 col-sm-6">
                        <div class="accomodation_item text-center">
                            <div class="hotel_img">
                                <img src="{{ asset('royal-master/image/rooms/room1.jpg') }}" alt="">
                                <a href="{{ route('booking.index') }}" class="btn theme_btn button_hover">Book Now</a>
                            </div>
                            <a href="{{ route('booking.index') }}"><h4 class="sec_h4">Double Deluxe Room</h4></a>
                            <h5>$250<small>/night</small></h5>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="accomodation_item text-center">
                            <div class="hotel_img">
                                <img src="{{ asset('royal-master/image/rooms/room2.jpg') }}" alt="">
                                <a href="{{ route('booking.index') }}" class="btn theme_btn button_hover">Book Now</a>
                            </div>
                            <a href="{{ route('booking.index') }}"><h4 class="sec_h4">Single Deluxe Room</h4></a>
                            <h5>$200<small>/night</small></h5>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="accomodation_item text-center">
                            <div class="hotel_img">
                                <img src="{{ asset('royal-master/image/rooms/room3.jpg') }}" alt="">
                                <a href="{{ route('booking.index') }}" class="btn theme_btn button_hover">Book Now</a>
                            </div>
                            <a href="{{ route('booking.index') }}"><h4 class="sec_h4">Honeymoon Suit</h4></a>
                            <h5>$750<small>/night</small></h5>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="accomodation_item text-center">
                            <div class="hotel_img">
                                <img src="{{ asset('royal-master/image/rooms/room4.jpg') }}" alt="">
                                <a href="{{ route('booking.index') }}" class="btn theme_btn button_hover">Book Now</a>
                            </div>
                            <a href="{{ route('booking.index') }}"><h4 class="sec_h4">Economy Double</h4></a>
                            <h5>$200<small>/night</small></h5>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="accomodation_item text-center">
                            <div class="hotel_img">
                                <img src="{{ asset('royal-master/image/rooms/room1.jpg') }}" alt="">
                                <a href="{{ route('booking.index') }}" class="btn theme_btn button_hover">Book Now</a>
                            </div>
                            <a href="{{ route('booking.index') }}"><h4 class="sec_h4">Double Deluxe Room</h4></a>
                            <h5>$250<small>/night</small></h5>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="accomodation_item text-center">
                            <div class="hotel_img">
                                <img src="{{ asset('royal-master/image/rooms/room2.jpg') }}" alt="">
                                <a href="{{ route('booking.index') }}" class="btn theme_btn button_hover">Book Now</a>
                            </div>
                            <a href="{{ route('booking.index') }}"><h4 class="sec_h4">Single Deluxe Room</h4></a>
                            <h5>$200<small>/night</small></h5>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="accomodation_item text-center">
                            <div class="hotel_img">
                                <img src="{{ asset('royal-master/image/rooms/room3.jpg') }}" alt="">
                                <a href="{{ route('booking.index') }}" class="btn theme_btn button_hover">Book Now</a>
                            </div>
                            <a href="{{ route('booking.index') }}"><h4 class="sec_h4">Honeymoon Suit</h4></a>
                            <h5>$750<small>/night</small></h5>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="accomodation_item text-center">
                            <div class="hotel_img">
                                <img src="{{ asset('royal-master/image/rooms/room4.jpg') }}" alt="">
                                <a href="{{ route('booking.index') }}" class="btn theme_btn button_hover">Book Now</a>
                            </div>
                            <a href="{{ route('booking.index') }}"><h4 class="sec_h4">Economy Double</h4></a>
                            <h5>$200<small>/night</small></h5>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--================ Accomodation Area  =================-->
        
        @include('landing_page_views.partials.royal-footer')
        
        @include('landing_page_views.partials.royal-scripts')
        @include('landing_page_views.partials.chat-widgets')
    </body>
</html>


