<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; }
        .header { background: #1a1a2e; padding: 30px; text-align: center; }
        .header h1 { color: #e94560; margin: 0; font-size: 24px; }
        .body { padding: 30px; color: #333; }
        .btn { display: inline-block; background: #e94560; color: #fff !important; padding: 12px 30px; border-radius: 5px; text-decoration: none; font-weight: bold; margin: 20px 0; }
        .footer { background: #f4f4f4; padding: 15px; text-align: center; font-size: 12px; color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>TechShop</h1>
        </div>
        <div class="body">
            <h2>Welcome, {{ $name }}!</h2>
            <p>Thank you for registering. Please verify your email address to activate your account.</p>
            <p style="text-align: center;">
                <a href="{{ $verifyUrl }}" class="btn">Verify Email Address</a>
            </p>
            <p>This link will expire in <strong>24 hours</strong>.</p>
            <p>If you did not create an account, no further action is required.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} TechShop. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
