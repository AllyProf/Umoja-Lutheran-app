<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="icon" href="{{ asset('royal-master/image/logo/Logo.png') }}" type="image/png">
        <title>Booking Confirmation - PrimeLand Hotel</title>
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="{{ asset('royal-master/css/bootstrap.css') }}">
        <link rel="stylesheet" href="{{ asset('royal-master/vendors/linericon/style.css') }}">
        <link rel="stylesheet" href="{{ asset('royal-master/css/font-awesome.min.css') }}">
        <link rel="stylesheet" href="{{ asset('royal-master/vendors/owl-carousel/owl.carousel.min.css') }}">
        <link rel="stylesheet" href="{{ asset('royal-master/vendors/nice-select/css/nice-select.css') }}">
        <!-- main css -->
        <link rel="stylesheet" href="{{ asset('royal-master/css/style.css') }}">
        <link rel="stylesheet" href="{{ asset('royal-master/css/responsive.css') }}">
        
        <!-- Font Awesome Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        
        <!-- Meta CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>
        .confirmation-page {
            padding: 60px 0 80px;
            background: #f5f5f5;
            min-height: 70vh;
        }
        
        .confirmation-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Success Header */
        .success-box {
            text-align: center;
            background: #ffffff;
            padding: 50px 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .success-icon {
            width: 70px;
            height: 70px;
            background: #e77a3a;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        
        .success-icon i {
            font-size: 35px;
            color: #ffffff;
        }
        
        .success-title {
            font-size: 28px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 10px;
        }
        
        .success-subtitle {
            font-size: 16px;
            color: #666;
            margin-bottom: 25px;
        }
        
        .booking-ref {
            display: inline-block;
            background: #e77a3a;
            color: #ffffff;
            padding: 12px 25px;
            border-radius: 6px;
            font-size: 18px;
            font-weight: 600;
            letter-spacing: 1px;
        }
        
        /* Main Content */
        .content-wrapper {
            background: #ffffff;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e77a3a;
        }
        
        /* Details Grid */
        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .detail-item {
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #e77a3a;
        }
        
        .detail-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        .detail-value {
            font-size: 16px;
            color: #1a1a1a;
            font-weight: 500;
        }
        
        .detail-value .badge {
            background: #e77a3a;
            color: #ffffff;
            padding: 5px 12px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
        }
        
        /* Payment Section */
        .payment-section {
            background: linear-gradient(135deg, #e77a3a 0%, #d66a2a 100%);
            color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            text-align: center;
        }
        
        .payment-label {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 10px;
        }
        
        .payment-amount {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        .payment-status {
            font-size: 14px;
            opacity: 0.95;
        }
        
        .payment-status i {
            margin-right: 5px;
        }
        
        /* Action Buttons */
        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
            margin-bottom: 30px;
        }
        
        .action-buttons .btn {
            padding: 14px 30px;
            font-size: 15px;
            font-weight: 600;
            border-radius: 6px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            border: none;
        }
        
        .action-buttons .btn-primary {
            background: #e77a3a;
            color: #ffffff;
        }
        
        .action-buttons .btn-primary:hover {
            background: #d66a2a;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(231, 122, 58, 0.3);
        }
        
        .action-buttons .btn-secondary {
            background: #ffffff;
            color: #666;
            border: 2px solid #e0e0e0;
        }
        
        .action-buttons .btn-secondary:hover {
            background: #f8f9fa;
            border-color: #e77a3a;
            color: #e77a3a;
        }
        
        .action-buttons .btn-success {
            background: #28a745;
            color: #ffffff;
        }
        
        .action-buttons .btn-success:hover {
            background: #218838;
        }
        
        .action-buttons .btn-warning {
            background: #ffc107;
            color: #333;
        }
        
        .action-buttons .btn-warning:hover {
            background: #e0a800;
        }
        
        /* Email Notification */
        .email-notice {
            background: #e8f4f8;
            border: 1px solid #bee5eb;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
        }
        
        .email-notice i {
            color: #e77a3a;
            margin-right: 8px;
            font-size: 18px;
        }
        
        .email-notice strong {
            color: #1a1a1a;
            font-size: 15px;
            display: block;
            margin-bottom: 5px;
        }
        
        .email-notice small {
            color: #666;
            font-size: 13px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .confirmation-page {
                padding: 40px 0 60px;
            }
            
            .success-box {
                padding: 40px 20px;
            }
            
            .success-title {
                font-size: 24px;
            }
            
            .content-wrapper {
                padding: 25px 20px;
            }
            
            .details-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .action-buttons .btn {
                width: 100%;
                justify-content: center;
            }
            
            .payment-amount {
                font-size: 28px;
            }
        }
    </style>
</head>

<body>
    @include('landing_page_views.partials.royal-header')

    <!-- ##### Breadcrumb Area Start ##### -->
    <section class="breadcrumb_area">
        <div class="overlay bg-parallax" data-stellar-ratio="0.8" data-stellar-vertical-offset="0" data-background=""></div>
        <div class="container">
            <div class="page-cover text-center">
                <h2 class="page-cover-tittle" style="color: #e77a3a;">Booking Confirmation</h2>
                <ol class="breadcrumb">
                    <li><a href="{{ url('/') }}">Home</a></li>
                    <li class="active">Booking Confirmation</li>
                </ol>
            </div>
        </div>
    </section>
    <!-- ##### Breadcrumb Area End ##### -->

    <!-- ##### Confirmation Section Start ##### -->
    <section class="confirmation-page">
        <div class="confirmation-container">
            
            <!-- Success Header -->
            <div class="success-box">
                <div class="success-icon" style="background: {{ $booking->payment_status == 'paid' ? '#28a745' : '#ffc107' }};">
                    <i class="fas {{ $booking->payment_status == 'paid' ? 'fa-check' : 'fa-clock' }}"></i>
                </div>
                <h1 class="success-title">
                    @if($booking->payment_status == 'paid')
                        Booking Confirmed!
                    @else
                        Booking Received - Waiting for Payment
                    @endif
                </h1>
                <p class="success-subtitle">
                    @if($booking->payment_status == 'paid')
                        Thank you for choosing PrimeLand Hotel. We're excited to host you!
                    @else
                        Please complete your payment to confirm your booking. Your booking will expire if payment is not completed.
                    @endif
                </p>
                <div class="booking-ref">{{ $booking->booking_reference }}</div>
            </div>
            
            <!-- Main Content -->
            <div class="content-wrapper">
                <h2 class="section-title">Booking Details</h2>
                
                <div class="details-grid">
                    <div class="detail-item">
                        <div class="detail-label">Guest Name</div>
                        <div class="detail-value">{{ $booking->guest_name }}</div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Email Address</div>
                        <div class="detail-value">{{ $booking->guest_email }}</div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Phone Number</div>
                        <div class="detail-value">{{ $booking->guest_phone }}</div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Room Type</div>
                        <div class="detail-value"><span class="badge">{{ $booking->room->room_type }}</span></div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Room Number</div>
                        <div class="detail-value">{{ $booking->room->room_number }}</div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Number of Guests</div>
                        <div class="detail-value">{{ $booking->number_of_guests }} Guest(s)</div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Check-in Date</div>
                        <div class="detail-value">{{ $booking->check_in->format('M d, Y') }}</div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Check-out Date</div>
                        <div class="detail-value">{{ $booking->check_out->format('M d, Y') }}</div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Number of Nights</div>
                        <div class="detail-value">{{ $booking->check_in->diffInDays($booking->check_out) }} night(s)</div>
                    </div>
                    
                    @if($booking->arrival_time)
                    <div class="detail-item">
                        <div class="detail-label">Estimated Arrival Time</div>
                        <div class="detail-value">{{ $booking->arrival_time }}</div>
                    </div>
                    @endif
                </div>
                
                <!-- Payment Section -->
                <div class="payment-section">
                    <div class="payment-label">
                        @if($booking->payment_status == 'paid')
                            Total Amount Paid
                        @else
                            Total Amount Due
                        @endif
                    </div>
                    <div class="payment-amount">${{ number_format($booking->total_price, 2) }}</div>
                    <div class="payment-status" style="color: {{ $booking->payment_status == 'paid' ? '#28a745' : '#ffc107' }};">
                        @if($booking->payment_status == 'paid')
                            <i class="fas fa-check-circle"></i>
                            Payment completed via {{ ucfirst($booking->payment_method ?? 'PayPal') }}
                            @if($booking->paid_at)
                                <br><small style="opacity: 0.8; font-size: 12px; margin-top: 5px; display: block;">Paid on {{ $booking->paid_at->format('M d, Y \a\t g:i A') }}</small>
                            @endif
                        @else
                            <i class="fas fa-clock"></i>
                            <strong>Waiting for Payment</strong>
                            @if($booking->expires_at)
                                <br><small style="opacity: 0.8; font-size: 12px; margin-top: 5px; display: block;">
                                    Expires: {{ \Carbon\Carbon::parse($booking->expires_at)->setTimezone('Africa/Nairobi')->format('M d, Y \a\t g:i A') }} (East Africa Time)
                                </small>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="action-buttons">
                @if($booking->payment_status == 'pending')
                <a href="{{ route('payment.create', ['booking_id' => $booking->id]) }}" class="btn btn-primary" style="background: #e77a3a; border-color: #e77a3a;">
                    <i class="fas fa-credit-card"></i> Complete Payment Now
                </a>
                @endif
                <a href="{{ route('customer.login') }}" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Login to Dashboard
                </a>
                @if($booking->status == 'confirmed' && $booking->payment_status == 'paid' && $booking->check_in_status == 'pending')
                <a href="{{ route('check-in.index') }}?ref={{ $booking->booking_reference }}" class="btn btn-success">
                    <i class="fas fa-sign-in-alt"></i> Check In Now
                </a>
                @endif
                @if($booking->check_in_status == 'checked_in')
                <a href="{{ route('customer.bookings.checkout-bill', $booking) }}" class="btn btn-warning">
                    <i class="fas fa-sign-out-alt"></i> Check Out Now
                </a>
                @endif
                <a href="{{ route('booking.index') }}" class="btn btn-secondary">
                    <i class="fas fa-calendar"></i> Continue Booking
                </a>
                <a href="{{ url('/') }}" class="btn btn-secondary">
                    <i class="fas fa-home"></i> Back to Home
                </a>
            </div>
            
            @if($booking->payment_status == 'pending')
            <!-- Payment Warning Notice -->
            <div class="email-notice" style="background: #fff3cd; border-color: #ffc107; margin-bottom: 20px;">
                <i class="fas fa-exclamation-triangle" style="color: #ffc107;"></i>
                <strong style="color: #856404;">Payment Required</strong>
                <small style="color: #856404; display: block; margin-top: 8px;">
                    Your booking is pending payment. Please complete your payment to secure your reservation.
                    @if($booking->expires_at)
                        Your booking will expire on {{ \Carbon\Carbon::parse($booking->expires_at)->setTimezone('Africa/Nairobi')->format('M d, Y \a\t g:i A') }} (East Africa Time) if payment is not completed.
                    @endif
                </small>
            </div>
            @endif
            
            <!-- Email Notification -->
            <div class="email-notice">
                <i class="fas fa-envelope"></i>
                <strong>A confirmation email has been sent to {{ $booking->guest_email }}</strong>
                <small>Please check your inbox (and spam folder) for booking details and login instructions.</small>
            </div>
            
        </div>
    </section>
    <!-- ##### Confirmation Section End ##### -->

    @include('landing_page_views.partials.royal-footer')
    @include('landing_page_views.partials.royal-scripts')
    @include('landing_page_views.partials.chat-widgets')

</body>

</html>
