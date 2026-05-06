<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 30px auto; background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #e63946, #c1121f); color: white; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .header p { margin: 5px 0 0; opacity: 0.9; }
        .body { padding: 30px; }
        .greeting { font-size: 18px; margin-bottom: 20px; color: #333; }
        .order-info { background: #f8f9fa; border-radius: 8px; padding: 20px; margin-bottom: 25px; border-left: 4px solid #e63946; }
        .order-info h3 { margin: 0 0 15px; color: #333; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 8px; }
        .info-label { color: #666; font-size: 14px; }
        .info-value { font-weight: bold; color: #333; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #333; color: white; padding: 12px; text-align: left; font-size: 13px; }
        td { padding: 12px; border-bottom: 1px solid #eee; font-size: 13px; }
        .total-row td { font-weight: bold; background: #f8f9fa; font-size: 15px; }
        .status-badge { display: inline-block; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; background: #fff3cd; color: #856404; }
        .footer { background: #333; color: #ccc; padding: 20px; text-align: center; font-size: 13px; }
        .footer a { color: #e63946; text-decoration: none; }
        .thank-you { text-align: center; padding: 20px; background: #e8f5e9; border-radius: 8px; color: #2e7d32; margin-bottom: 20px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>🎉 Đặt hàng thành công!</h1>
        <p>Cảm ơn bạn đã tin tưởng mua sắm tại TechShop</p>
    </div>
    
    <div class="body">
        <div class="thank-you">
            <strong>Xin chào, {{ $customer->username ?? $customer->name }}!</strong><br>
            Đơn hàng của bạn đã được xác nhận. Chúng tôi sẽ liên hệ sớm để xác nhận.
        </div>

        <div class="order-info">
            <h3>📦 Thông tin đơn hàng</h3>
            <div class="info-row">
                <span class="info-label">Mã đơn hàng:</span>
                <span class="info-value">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Ngày đặt:</span>
                <span class="info-value">{{ date('d/m/Y H:i', strtotime($order->created_at)) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Địa chỉ giao hàng:</span>
                <span class="info-value">{{ $order->shipping_address }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Phương thức thanh toán:</span>
                <span class="info-value">{{ strtoupper($order->payment_method) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Trạng thái:</span>
                <span class="info-value"><span class="status-badge">Đang xử lý</span></span>
            </div>
            <div class="info-row" style="margin-top: 10px; border-top: 1px dashed #ccc; padding-top: 10px;">
                <span class="info-label" style="color:#2e7d32; font-weight:bold;">Giao dự kiến:</span>
                <span class="info-value" style="color:#2e7d32;">{{ date('d/m/Y', strtotime($order->created_at . ' + 3 days')) }}</span>
            </div>
        </div>

        <h3>🛒 Chi tiết sản phẩm</h3>
        <table>
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th style="text-align:center">SL</th>
                    <th style="text-align:right">Đơn giá</th>
                    <th style="text-align:right">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orderItems as $item)
                <tr>
                    <td>{{ $item->name ?? 'Sản phẩm' }}</td>
                    <td style="text-align:center">{{ $item->quantity }}</td>
                    <td style="text-align:right">$\{\{ number_format($item->price / 25000, 2) \}\}</td>
                    <td style="text-align:right">$\{\{ number_format(($item->price * $item->quantity) / 25000, 2) \}\}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="3" style="text-align:right">Tổng cộng:</td>
                    <td style="text-align:right; color: #e63946;">${{ number_format($order->total / 25000, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <p style="color:#666; font-size:13px;">Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ chúng tôi qua email hoặc hotline.</p>
    </div>

    <div class="footer">
        <p>© 2025 TechShop – All rights reserved</p>
        <p>📧 support@techshop.vn | 📞 0896 492 400</p>
    </div>
</div>
</body>
</html>
