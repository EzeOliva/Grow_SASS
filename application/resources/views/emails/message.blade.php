<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Message from {{ $sender_name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .message-content {
            background: #ffffff;
            padding: 20px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .footer {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            font-size: 12px;
            color: #6c757d;
        }
        .sender-info {
            margin-bottom: 15px;
        }
        .sender-name {
            font-weight: bold;
            color: #007bff;
        }
        .message-text {
            white-space: pre-wrap;
            line-height: 1.8;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>New Message Received</h2>
        <div class="sender-info">
            <p><strong>From:</strong> <span class="sender-name">{{ $sender_name }}</span></p>
            <p><strong>Email:</strong> {{ $sender_email }}</p>
            <p><strong>Date:</strong> {{ now()->format('F j, Y \a\t g:i A') }}</p>
        </div>
    </div>

    <div class="message-content">
        <h3>Message:</h3>
        <div class="message-text">{{ $message_text }}</div>
    </div>

    <div class="footer">
        <p>This message was sent through the GrowSass messaging system.</p>
        <p>Please do not reply directly to this email. Use the messaging system to respond.</p>
    </div>
</body>
</html>
