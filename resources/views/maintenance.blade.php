<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Under maintenance</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: #f6f3f0;
            color: #1c1c1c;
            font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }
        .card {
            width: 100%;
            max-width: 640px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(80, 20, 20, 0.08);
            padding: 36px 32px;
            border-top: 6px solid #940000;
        }
        .badge {
            display: inline-block;
            background: #940000;
            color: #fff;
            font-size: 12px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 6px 10px;
            border-radius: 999px;
            margin-bottom: 16px;
        }
        h1 {
            margin: 0 0 18px;
            font-size: 28px;
            line-height: 1.3;
            color: #1c1c1c;
        }
        .message {
            margin: 0;
            font-size: 18px;
            line-height: 1.65;
            color: #1c1c1c;
            white-space: pre-wrap;
            word-wrap: break-word;
            overflow-wrap: anywhere;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="badge">Umoja Lutheran Hostel</div>
        <h1>Under maintenance</h1>
        <p class="message">{{ $message }}</p>
    </div>
</body>
</html>
