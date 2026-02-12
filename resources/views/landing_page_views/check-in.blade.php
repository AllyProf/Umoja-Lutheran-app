<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="icon" href="{{ asset('royal-master/image/logo/Logo.png') }}" type="image/png">
        <title>Guest Check-In - PrimeLand Hotel</title>
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
        .checkin-page {
            padding: 60px 0 80px;
            background: #f5f5f5;
            min-height: 70vh;
        }
        
        .checkin-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .checkin-box {
            background: #ffffff;
            border-radius: 10px;
            padding: 50px 40px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        
        .checkin-title {
            font-size: 28px;
            font-weight: 600;
            color: #1a1a1a;
            text-align: center;
            margin-bottom: 10px;
        }
        
        .checkin-subtitle {
            font-size: 16px;
            color: #666;
            text-align: center;
            margin-bottom: 40px;
        }
        
        .checkin-subtitle a {
            color: #e77a3a;
            font-weight: 600;
            text-decoration: none;
        }
        
        .checkin-subtitle a:hover {
            text-decoration: underline;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #1a1a1a;
            font-size: 14px;
        }
        
        .form-group label i {
            margin-right: 8px;
            color: #e77a3a;
        }
        
        .form-group input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #ffffff;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #e77a3a;
            box-shadow: 0 0 0 3px rgba(231, 122, 58, 0.1);
        }
        
        .btn-checkin {
            width: 100%;
            padding: 14px 20px;
            background: #e77a3a;
            color: #ffffff;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-checkin:hover {
            background: #d66a2a;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(231, 122, 58, 0.3);
        }
        
        .btn-checkin:disabled {
            background: #95a5a6;
            cursor: not-allowed;
            transform: none;
        }
        
        .btn-secondary {
            background: #ffffff;
            color: #666;
            border: 2px solid #e0e0e0;
        }
        
        .btn-secondary:hover {
            background: #f8f9fa;
            border-color: #e77a3a;
            color: #e77a3a;
        }
        
        .alert {
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 6px;
            border-left: 4px solid;
        }
        
        .alert-success {
            background-color: #d4edda;
            border-color: #28a745;
            color: #155724;
        }
        
        .alert-error {
            background-color: #f8d7da;
            border-color: #dc3545;
            color: #721c24;
        }
        
        .alert-info {
            background-color: #d1ecf1;
            border-color: #17a2b8;
            color: #0c5460;
        }
        
        .booking-details-box {
            background: #f8f9fa;
            padding: 30px;
            margin-top: 30px;
            border-radius: 8px;
            border-left: 4px solid #e77a3a;
        }
        
        .booking-details-box h4 {
            color: #e77a3a;
            margin-bottom: 20px;
            font-size: 20px;
            font-weight: 600;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            font-weight: 600;
            color: #555;
            font-size: 14px;
        }
        
        .detail-value {
            color: #1a1a1a;
            font-weight: 500;
            font-size: 14px;
        }
        
        .checkin-status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .badge-success {
            background-color: #28a745;
            color: #ffffff;
        }
        
        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }
        
        .badge-info {
            background-color: #17a2b8;
            color: #ffffff;
        }
        
        .badge-danger {
            background-color: #dc3545;
            color: #ffffff;
        }
        
        @media (max-width: 768px) {
            .checkin-box {
                padding: 30px 20px;
            }
            
            .checkin-title {
                font-size: 24px;
            }
            
            .detail-row {
                flex-direction: column;
                gap: 5px;
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
                <h2 class="page-cover-tittle" style="color: #e77a3a;">Guest Check-In</h2>
                <ol class="breadcrumb">
                    <li><a href="{{ url('/') }}">Home</a></li>
                    <li class="active">Check-In</li>
                </ol>
            </div>
        </div>
    </section>
    <!-- ##### Breadcrumb Area End ##### -->

    <!-- ##### Check-In Section Start ##### -->
    <section class="checkin-page">
        <div class="checkin-container">
            <div class="checkin-box">
                <h2 class="checkin-title"><i class="fas fa-sign-in-alt"></i> Online Check-In</h2>
                <p class="checkin-subtitle">
                    @auth
                        Welcome back, {{ auth()->user()->name }}! You can check in to your bookings below.
                    @else
                        Enter your booking details to check in, or <a href="{{ route('customer.login') }}">log in to your account</a>
                    @endauth
                </p>
                
                <div id="alertContainer"></div>
                
                @auth
                <!-- Logged In: Show User's Bookings -->
                <div id="userBookingsSection">
                    <p style="text-align: center; margin-bottom: 20px; color: #666;">
                        Loading your bookings...
                    </p>
                </div>
                @else
                <!-- Step 1: Find Booking Form -->
                <div id="findBookingForm">
                    <form id="checkInForm">
                        @csrf
                        <div class="form-group">
                            <label for="booking_reference">
                                <i class="fas fa-hashtag"></i> Booking Reference
                            </label>
                            <input type="text" id="booking_reference" name="booking_reference" 
                                   placeholder="Enter your booking reference (e.g., BKMWFFLHXF)" 
                                   value="{{ request('ref') }}" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">
                                <i class="fas fa-envelope"></i> Email Address
                            </label>
                            <input type="email" id="email" name="email" 
                                   placeholder="Enter the email used for booking" required>
                        </div>
                        
                        <button type="submit" class="btn-checkin" id="findBookingBtn">
                            <i class="fas fa-search"></i> Find My Booking
                        </button>
                    </form>
                </div>
                
                <!-- Step 2: Booking Details & Check-In -->
                <div id="bookingDetailsBox" style="display: none;">
                    <div class="booking-details-box">
                        <h4><i class="fas fa-info-circle"></i> Booking Details</h4>
                        
                        <div class="detail-row">
                            <span class="detail-label">Guest Name:</span>
                            <span class="detail-value" id="detail_guest_name"></span>
                        </div>
                        
                        <div class="detail-row">
                            <span class="detail-label">Room:</span>
                            <span class="detail-value" id="detail_room"></span>
                        </div>
                        
                        <div class="detail-row">
                            <span class="detail-label">Check-in Date:</span>
                            <span class="detail-value" id="detail_check_in"></span>
                        </div>
                        
                        <div class="detail-row">
                            <span class="detail-label">Check-out Date:</span>
                            <span class="detail-value" id="detail_check_out"></span>
                        </div>
                        
                        <div class="detail-row">
                            <span class="detail-label">Status:</span>
                            <span class="detail-value" id="detail_status"></span>
                        </div>
                        
                        <div class="detail-row">
                            <span class="detail-label">Payment Status:</span>
                            <span class="detail-value" id="detail_payment"></span>
                        </div>
                        
                        <div class="detail-row">
                            <span class="detail-label">Check-in Status:</span>
                            <span class="detail-value" id="detail_checkin_status"></span>
                        </div>
                    </div>
                    
                    <button type="button" class="btn-checkin" id="checkInBtn" style="display: none;">
                        <i class="fas fa-check-circle"></i> Complete Check-In
                    </button>
                    
                    <button type="button" class="btn-checkin btn-secondary" id="backBtn" style="margin-top: 10px;">
                        <i class="fas fa-arrow-left"></i> Back to Search
                    </button>
                </div>
                @endauth
            </div>
        </div>
    </section>
    <!-- ##### Check-In Section End ##### -->

    @include('landing_page_views.partials.royal-footer')
    @include('landing_page_views.partials.royal-scripts')

    <script>
        let currentBooking = null;
        
        @auth
        // Load user's bookings if logged in
        document.addEventListener('DOMContentLoaded', function() {
            const bookings = @json($bookings ?? []);
            const userBookingsSection = document.getElementById('userBookingsSection');
            
            if (bookings && bookings.length > 0) {
                let bookingsHtml = '<h4 style="margin-bottom: 20px; color: #1a1a1a; font-weight: 600;"><i class="fas fa-calendar-check"></i> Your Bookings</h4>';
                
                bookings.forEach(function(booking) {
                    const checkInDate = new Date(booking.check_in);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    
                    const canCheckIn = checkInDate <= today;
                    
                    bookingsHtml += `
                        <div class="booking-details-box" style="margin-bottom: 20px;">
                            <div class="detail-row">
                                <span class="detail-label">Booking Reference:</span>
                                <span class="detail-value"><strong>${booking.booking_reference}</strong></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Room:</span>
                                <span class="detail-value">${booking.room.room_number} (${booking.room.room_type})</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Check-in:</span>
                                <span class="detail-value">${new Date(booking.check_in).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Check-out:</span>
                                <span class="detail-value">${new Date(booking.check_out).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</span>
                            </div>
                            ${canCheckIn ? `
                                <button type="button" class="btn-checkin" onclick="checkInFromAccount(${booking.id}, '${booking.booking_reference}')" style="margin-top: 15px;">
                                    <i class="fas fa-check-circle"></i> Check In Now
                                </button>
                            ` : `
                                <p style="text-align: center; color: #666; margin-top: 15px; font-size: 14px;">
                                    Check-in available on ${new Date(booking.check_in).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}
                                </p>
                            `}
                        </div>
                    `;
                });
                
                userBookingsSection.innerHTML = bookingsHtml;
            } else {
                userBookingsSection.innerHTML = '<p style="text-align: center; color: #666;">No bookings available for check-in at this time.</p>';
            }
        });
        
        function checkInFromAccount(bookingId, bookingReference) {
            if (!confirm('Are you sure you want to check in now?')) {
                return;
            }
            
            fetch('{{ url("/check-in") }}/' + bookingId, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    booking_reference: bookingReference,
                    email: '{{ auth()->user()->email }}'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success');
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else {
                    showAlert(data.message || 'Check-in failed. Please try again.', 'error');
                }
            })
            .catch(error => {
                showAlert('An error occurred. Please try again.', 'error');
                console.error('Error:', error);
            });
        }
        @endauth
        
        // Find booking form submission (only if form exists - not logged in users)
        @guest
        const checkInForm = document.getElementById('checkInForm');
        if (checkInForm) {
            checkInForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const bookingRef = document.getElementById('booking_reference').value.trim();
            const email = document.getElementById('email').value.trim();
            const alertContainer = document.getElementById('alertContainer');
            const findBtn = document.getElementById('findBookingBtn');
            
            if (!bookingRef || !email) {
                showAlert('Please fill in all fields.', 'error');
                return;
            }
            
            findBtn.disabled = true;
            findBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Searching...';
            
            fetch('{{ route("check-in.find") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    booking_reference: bookingRef,
                    email: email
                })
            })
            .then(response => response.json())
            .then(data => {
                findBtn.disabled = false;
                findBtn.innerHTML = '<i class="fas fa-search"></i> Find My Booking';
                
                if (data.success) {
                    currentBooking = data.booking;
                    displayBookingDetails(data.booking);
                    showAlert('Booking found! Please review your details below.', 'success');
                } else {
                    showAlert(data.message || 'Booking not found. Please check your details.', 'error');
                }
            })
            .catch(error => {
                findBtn.disabled = false;
                findBtn.innerHTML = '<i class="fas fa-search"></i> Find My Booking';
                showAlert('An error occurred. Please try again.', 'error');
                console.error('Error:', error);
            });
            });
        }
        @endguest
        
        // Display booking details
        function displayBookingDetails(booking) {
            document.getElementById('detail_guest_name').textContent = booking.guest_name;
            document.getElementById('detail_room').textContent = booking.room.room_number + ' (' + booking.room.room_type + ')';
            document.getElementById('detail_check_in').textContent = new Date(booking.check_in).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
            document.getElementById('detail_check_out').textContent = new Date(booking.check_out).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
            
            // Status badges
            const statusBadges = {
                'pending': '<span class="checkin-status-badge badge-warning">Pending</span>',
                'confirmed': '<span class="checkin-status-badge badge-success">Confirmed</span>',
                'cancelled': '<span class="checkin-status-badge badge-danger">Cancelled</span>',
                'completed': '<span class="checkin-status-badge badge-info">Completed</span>'
            };
            document.getElementById('detail_status').innerHTML = statusBadges[booking.status] || booking.status;
            
            const paymentBadges = {
                'pending': '<span class="checkin-status-badge badge-warning">Pending</span>',
                'paid': '<span class="checkin-status-badge badge-success">Paid</span>',
                'cancelled': '<span class="checkin-status-badge badge-danger">Cancelled</span>'
            };
            document.getElementById('detail_payment').innerHTML = paymentBadges[booking.payment_status] || booking.payment_status;
            
            const checkinBadges = {
                'pending': '<span class="checkin-status-badge badge-warning">Not Checked In</span>',
                'checked_in': '<span class="checkin-status-badge badge-success">Checked In</span>',
                'checked_out': '<span class="checkin-status-badge badge-info">Checked Out</span>'
            };
            document.getElementById('detail_checkin_status').innerHTML = checkinBadges[booking.check_in_status] || booking.check_in_status;
            
            // Show check-in button if eligible
            const checkInBtn = document.getElementById('checkInBtn');
            if (booking.status === 'confirmed' && 
                booking.payment_status === 'paid' && 
                booking.check_in_status === 'pending') {
                checkInBtn.style.display = 'block';
            } else {
                checkInBtn.style.display = 'none';
            }
            
            // Show booking details box
            document.getElementById('findBookingForm').style.display = 'none';
            document.getElementById('bookingDetailsBox').style.display = 'block';
        }
        
        // Check-in button click
        const checkInBtn = document.getElementById('checkInBtn');
        if (checkInBtn) {
            checkInBtn.addEventListener('click', function() {
                if (!currentBooking) return;
                
                const checkInBtn = document.getElementById('checkInBtn');
                checkInBtn.disabled = true;
                checkInBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                
                fetch('{{ url("/check-in") }}/' + currentBooking.id, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        booking_reference: currentBooking.booking_reference,
                        email: document.getElementById('email').value
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert(data.message, 'success');
                        checkInBtn.style.display = 'none';
                        // Update check-in status display
                        document.getElementById('detail_checkin_status').innerHTML = '<span class="checkin-status-badge badge-success">Checked In</span>';
                        currentBooking = data.booking;
                    } else {
                        showAlert(data.message || 'Check-in failed. Please try again or contact the hotel.', 'error');
                        checkInBtn.disabled = false;
                        checkInBtn.innerHTML = '<i class="fas fa-check-circle"></i> Complete Check-In';
                    }
                })
                .catch(error => {
                    showAlert('An error occurred. Please try again.', 'error');
                    checkInBtn.disabled = false;
                    checkInBtn.innerHTML = '<i class="fas fa-check-circle"></i> Complete Check-In';
                    console.error('Error:', error);
                });
            });
        }
        
        // Back button
        const backBtn = document.getElementById('backBtn');
        if (backBtn) {
            backBtn.addEventListener('click', function() {
                document.getElementById('findBookingForm').style.display = 'block';
                document.getElementById('bookingDetailsBox').style.display = 'none';
                currentBooking = null;
                document.getElementById('alertContainer').innerHTML = '';
            });
        }
        
        // Show alert function
        function showAlert(message, type) {
            const alertContainer = document.getElementById('alertContainer');
            const alertClass = type === 'success' ? 'alert-success' : type === 'error' ? 'alert-error' : 'alert-info';
            alertContainer.innerHTML = '<div class="alert ' + alertClass + '">' + message + '</div>';
            
            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    </script>
    @include('landing_page_views.partials.chat-widgets')

</body>

</html>
