<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Reply - <?php echo e($ticket_id); ?></title>
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
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .content {
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .message {
            background-color: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #007bff;
            margin: 20px 0;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            font-size: 14px;
            color: #6c757d;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }
        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>📧 Ticket Reply</h2>
        <p><strong>Ticket ID:</strong> #<?php echo e($ticket_id); ?></p>
        <p><strong>From:</strong> <?php echo e($agent_name); ?></p>
    </div>

    <div class="content">
        <p>Hello <?php echo e($contact_name); ?>,</p>
        
        <p>You have received a reply to your ticket. Here's the message:</p>
        
        <div class="message">
            <?php echo e($message); ?>

        </div>
        
        <p>To view the full ticket and continue the conversation, please click the button below:</p>
        
        <a href="<?php echo e($ticket_url); ?>" class="button">View Ticket</a>
        
        <p>If the button doesn't work, you can copy and paste this link into your browser:</p>
        <p style="word-break: break-all; color: #007bff;"><?php echo e($ticket_url); ?></p>
    </div>

    <div class="footer">
        <p>This is an automated message from your support system.</p>
        <p>Please do not reply to this email. Use the ticket system to respond.</p>
    </div>
</body>
</html>
<?php /**PATH E:\GrowSass\application\resources\views/emails/whatsapp/ticket-reply.blade.php ENDPATH**/ ?>