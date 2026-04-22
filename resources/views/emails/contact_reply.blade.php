<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #e63946, #c1121f); color: white; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 22px; }
        .body { padding: 30px; }
        .original-msg { background: #f8f9fa; border-left: 4px solid #ccc; padding: 15px; margin: 20px 0; border-radius: 0 8px 8px 0; color: #666; }
        .reply-box { background: #e8f5e9; border-left: 4px solid #28a745; padding: 20px; border-radius: 0 8px 8px 0; margin: 20px 0; }
        .footer { background: #333; color: #ccc; padding: 20px; text-align: center; font-size: 13px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>💬 Phản hồi từ TechShop</h1>
    </div>
    <div class="body">
        <p>Xin chào <strong>{{ $contact->name }}</strong>,</p>
        <p>Cảm ơn bạn đã liên hệ với chúng tôi. Dưới đây là phản hồi của chúng tôi cho yêu cầu của bạn:</p>

        <div class="original-msg">
            <p><strong>Câu hỏi của bạn: </strong> {{ $contact->subject }}</p>
            <p>{{ $contact->message }}</p>
        </div>

        <div class="reply-box">
            <p><strong>Phản hồi của chúng tôi:</strong></p>
            <p>{{ $replyContent }}</p>
        </div>

        <p style="color:#666;">Nếu bạn cần thêm hỗ trợ, vui lòng liên hệ lại chúng tôi.</p>
    </div>
    <div class="footer">
        <p>© 2025 TechShop | support@techshop.vn | 0896 492 400</p>
    </div>
</div>
</body>
</html>
