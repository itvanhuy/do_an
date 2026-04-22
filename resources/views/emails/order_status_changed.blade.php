<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 30px auto; background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #3498db, #2980b9); color: white; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .body { padding: 30px; }
        .status-box { border-radius: 8px; padding: 25px; margin: 20px 0; text-align: center; }
        .status-pending { background: #fff3cd; border: 2px solid #ffc107; color: #856404; }
        .status-processing { background: #cce5ff; border: 2px solid #0d6efd; color: #004085; }
        .status-shipped { background: #d4edda; border: 2px solid #28a745; color: #155724; }
        .status-delivered { background: #d1ecf1; border: 2px solid #17a2b8; color: #0c5460; }
        .status-cancelled { background: #f8d7da; border: 2px solid #dc3545; color: #721c24; }
        .status-icon { font-size: 40px; margin-bottom: 10px; display: block; }
        .status-text { font-size: 20px; font-weight: bold; }
        .order-info { background: #f8f9fa; border-radius: 8px; padding: 20px; margin-bottom: 25px; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 8px; }
        .info-label { color: #666; font-size: 14px; }
        .info-value { font-weight: bold; color: #333; font-size: 14px; }
        .footer { background: #333; color: #ccc; padding: 20px; text-align: center; font-size: 13px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>🔔 Cập nhật đơn hàng</h1>
        <p>Đơn hàng #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }} của bạn đã được cập nhật</p>
    </div>

    <div class="body">
        <p>Xin chào <strong>{{ $customer->username ?? $customer->name }}</strong>,</p>
        <p>Trạng thái đơn hàng của bạn vừa được cập nhật:</p>

        @php
            $statusConfig = [
                'pending'    => ['class' => 'status-pending',    'icon' => '⏳', 'label' => 'Chờ xử lý'],
                'processing' => ['class' => 'status-processing', 'icon' => '🔄', 'label' => 'Đang chuẩn bị'],
                'shipped'    => ['class' => 'status-shipped',    'icon' => '🚚', 'label' => 'Đang giao hàng'],
                'delivered'  => ['class' => 'status-delivered',  'icon' => '✅', 'label' => 'Đã giao thành công'],
                'cancelled'  => ['class' => 'status-cancelled',  'icon' => '❌', 'label' => 'Đã hủy'],
            ];
            $cfg = $statusConfig[$newStatus] ?? ['class' => 'status-pending', 'icon' => 'ℹ️', 'label' => $newStatus];
        @endphp

        <div class="status-box {{ $cfg['class'] }}">
            <span class="status-icon">{{ $cfg['icon'] }}</span>
            <div class="status-text">{{ $cfg['label'] }}</div>
        </div>

        <div class="order-info">
            <div class="info-row">
                <span class="info-label">Mã đơn hàng:</span>
                <span class="info-value">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tổng tiền:</span>
                <span class="info-value" style="color:#e63946">{{ number_format($order->total, 0, ',', '.') }}₫</span>
            </div>
            <div class="info-row">
                <span class="info-label">Địa chỉ giao hàng:</span>
                <span class="info-value">{{ $order->shipping_address }}</span>
            </div>
            <div class="info-row" style="margin-top: 10px; border-top: 1px dashed #ccc; padding-top: 10px;">
                <span class="info-label" style="color:#2e7d32; font-weight:bold;">Giao dự kiến:</span>
                <span class="info-value" style="color:#2e7d32;">{{ date('d/m/Y', strtotime($order->created_at . ' + 3 days')) }}</span>
            </div>
        </div>

        @if($newStatus === 'shipped')
        <p style="color:#155724; background:#d4edda; padding:15px; border-radius:8px;">
            🚚 Đơn hàng của bạn đang trên đường giao đến! Vui lòng để ý điện thoại để nhận hàng.
        </p>
        @elseif($newStatus === 'delivered')
        <p style="color:#0c5460; background:#d1ecf1; padding:15px; border-radius:8px;">
            ✅ Đơn hàng đã giao thành công! Cảm ơn bạn đã mua sắm tại TechShop.
        </p>
        @elseif($newStatus === 'cancelled')
        <p style="color:#721c24; background:#f8d7da; padding:15px; border-radius:8px;">
            ❌ Đơn hàng đã bị hủy. Nếu bạn cần hỗ trợ, vui lòng liên hệ chúng tôi.
        </p>
        @endif

        <p style="color:#666; font-size:13px;">Nếu bạn có câu hỏi, liên hệ: support@techshop.vn | 0896 492 400</p>
    </div>

    <div class="footer">
        <p>© 2025 TechShop – All rights reserved</p>
    </div>
</div>
</body>
</html>
