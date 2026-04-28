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
        .btn { display: inline-block; background: #e94560; color: #fff; padding: 12px 30px; border-radius: 5px; text-decoration: none; font-weight: bold; margin: 20px 0; }
        .footer { background: #f4f4f4; padding: 15px; text-align: center; font-size: 12px; color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>TechShop</h1>
        </div>
        <div class="body">
            <h2>Đặt lại mật khẩu</h2>
            <p>Bạn nhận được email này vì chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn.</p>
            <p style="text-align: center;">
                <a href="{{ $resetUrl }}" class="btn">Đặt lại mật khẩu</a>
            </p>
            <p>Link này sẽ hết hạn sau <strong>60 phút</strong>.</p>
            <p>Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} TechShop. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
