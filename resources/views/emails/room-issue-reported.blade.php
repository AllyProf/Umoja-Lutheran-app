<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Room Issue Reported</title>
</head>
<body>
    <h2>Room Issue Reported</h2>
    
    <p>Hello Manager,</p>
    
    <p>A room issue has been reported:</p>
    
    <ul>
        <li><strong>Room Number:</strong> {{ $issue->room->room_number }}</li>
        <li><strong>Issue Type:</strong> {{ $issue->issue_type }}</li>
        <li><strong>Priority:</strong> {{ ucfirst($issue->priority) }}</li>
        <li><strong>Reported By:</strong> {{ $issue->reportedBy->name }}</li>
        <li><strong>Description:</strong> {{ $issue->description }}</li>
        <li><strong>Reported At:</strong> {{ $issue->created_at->format('F d, Y H:i') }}</li>
    </ul>
    
    <p>Please review and take appropriate action.</p>
    
    <p>Thank you!</p>
    
    <hr>
    <p><small>This is an automated notification from PrimeLand Hotel Management System.</small></p>
</body>
</html>
