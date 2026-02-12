<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Details - {{ $booking->booking_reference }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 20px;
            min-height: 100vh;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #e07632 0%, #f4a261 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .header .booking-ref {
            font-size: 18px;
            opacity: 0.9;
            font-weight: 500;
        }
        
        .content {
            padding: 30px;
        }
        
        .section {
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 20px;
            color: #e07632;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e07632;
            font-weight: 600;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }
        
        .info-item {
            display: flex;
            flex-direction: column;
        }
        
        .info-label {
            font-size: 12px;
            color: #7d7d7d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .info-value {
            font-size: 16px;
            color: #363636;
            font-weight: 500;
        }
        
        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        
        .badge-info {
            background-color: #17a2b8;
            color: white;
        }
        
        .badge-warning {
            background-color: #ffc107;
            color: #333;
        }
        
        .room-image {
            width: 100%;
            max-width: 400px;
            height: auto;
            border-radius: 8px;
            margin-top: 15px;
        }
        
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #7d7d7d;
            font-size: 14px;
        }
        
        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-calendar-check"></i> Booking Details</h1>
            <div class="booking-ref">Reference: {{ $booking->booking_reference }}</div>
        </div>
        
        <div class="content">
            <div class="section">
                <h2 class="section-title"><i class="fas fa-user"></i> Guest Information</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Guest Name</span>
                        <span class="info-value">{{ $booking->guest_name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email</span>
                        <span class="info-value">{{ $booking->guest_email }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Phone</span>
                        <span class="info-value">{{ $booking->guest_phone ?? 'N/A' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Country</span>
                        <span class="info-value">{{ $booking->country ?? 'N/A' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Number of Guests</span>
                        <span class="info-value">{{ $booking->number_of_guests ?? 1 }}</span>
                    </div>
                </div>
            </div>
            
            <div class="section">
                <h2 class="section-title"><i class="fas fa-calendar"></i> Booking Information</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Check-in Date</span>
                        <span class="info-value">{{ $booking->check_in->format('M d, Y') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Check-out Date</span>
                        <span class="info-value">{{ $booking->check_out->format('M d, Y') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Nights</span>
                        <span class="info-value">{{ $booking->check_in->diffInDays($booking->check_out) }} night(s)</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Status</span>
                        <span class="info-value">
                            @if($booking->status === 'confirmed')
                                <span class="badge badge-success">Confirmed</span>
                            @elseif($booking->status === 'pending')
                                <span class="badge badge-warning">Pending</span>
                            @else
                                <span class="badge badge-info">{{ ucfirst($booking->status) }}</span>
                            @endif
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Check-in Status</span>
                        <span class="info-value">
                            @if($booking->check_in_status === 'checked_in')
                                <span class="badge badge-success">Checked In</span>
                            @elseif($booking->check_in_status === 'checked_out')
                                <span class="badge badge-info">Checked Out</span>
                            @else
                                <span class="badge badge-warning">Pending</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="section">
                <h2 class="section-title"><i class="fas fa-bed"></i> Room Information</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Room Number</span>
                        <span class="info-value">{{ $booking->room->room_number ?? 'N/A' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Room Type</span>
                        <span class="info-value">{{ $booking->room->room_type ?? 'N/A' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Capacity</span>
                        <span class="info-value">{{ $booking->room->capacity ?? 'N/A' }} guest(s)</span>
                    </div>
                    @if($booking->room && $booking->room->images)
                        @php
                            $images = is_array($booking->room->images) ? $booking->room->images : json_decode($booking->room->images, true);
                            $firstImage = is_array($images) && count($images) > 0 ? $images[0] : null;
                        @endphp
                        @if($firstImage)
                            <div class="info-item" style="grid-column: 1 / -1;">
                                <span class="info-label">Room Image</span>
                                <img src="{{ asset('storage/' . $firstImage) }}" alt="Room Image" class="room-image" onerror="this.style.display='none';">
                            </div>
                        @endif
                    @endif
                </div>
            </div>
            
            <div class="section">
                <h2 class="section-title"><i class="fas fa-dollar-sign"></i> Payment Information</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Total Price</span>
                        <span class="info-value">${{ number_format($booking->total_price, 2) }} USD</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Total Price (TZS)</span>
                        <span class="info-value">{{ number_format($booking->total_price * $exchangeRate, 2) }} TZS</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Amount Paid</span>
                        <span class="info-value">${{ number_format($booking->amount_paid ?? 0, 2) }} USD</span>
                    </div>
                    @if($booking->payment_status === 'partial')
                        <div class="info-item">
                            <span class="info-label">Remaining Amount</span>
                            <span class="info-value" style="color: #dc3545; font-weight: 600;">${{ number_format($booking->total_price - ($booking->amount_paid ?? 0), 2) }} USD</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Payment Percentage</span>
                            <span class="info-value"><span class="badge badge-info">{{ number_format($booking->payment_percentage ?? 0, 0) }}%</span></span>
                        </div>
                    @endif
                    <div class="info-item">
                        <span class="info-label">Payment Status</span>
                        <span class="info-value">
                            @if($booking->payment_status === 'paid')
                                <span class="badge badge-success">Paid</span>
                            @elseif($booking->payment_status === 'partial')
                                <span class="badge badge-info">Partial</span>
                            @else
                                <span class="badge badge-warning">Pending</span>
                            @endif
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Payment Method</span>
                        <span class="info-value">{{ ucfirst($booking->payment_method ?? 'N/A') }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="footer">
            <p><strong>PrimeLand Hotel</strong> | Official Booking Details</p>
            <p style="margin-top: 5px; font-size: 12px;">This is an official booking confirmation. Please keep this information secure.</p>
        </div>
    </div>
</body>
</html>









