<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Room Needs Cleaning</title>
</head>
<body>
    <h2>Room Cleaning Notification</h2>
    
    <p>Hello,</p>
    
    <p>A room has been checked out and requires cleaning:</p>
    
    <ul>
        <li><strong>Room Number:</strong> {{ $room->room_number }}</li>
        <li><strong>Room Type:</strong> {{ $room->room_type }}</li>
        <li><strong>Guest Name:</strong> {{ $booking->guest_name }}</li>
        <li><strong>Check-out Date:</strong> {{ \Carbon\Carbon::parse($booking->checked_out_at)->format('F d, Y H:i') }}</li>
    </ul>
    
    <p>Please proceed to clean the room and mark it as cleaned in the system.</p>
    
    <p>Thank you!</p>
    
    <hr>
    <p><small>This is an automated notification from PrimeLand Hotel Management System.</small></p>
</body>
</html>
