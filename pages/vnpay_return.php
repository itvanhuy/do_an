<?php
// File: pages/vnpay_return.php
session_start();
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/vnpay_config.php';

$vnp_SecureHash = $_GET['vnp_SecureHash'];
$inputData = array();
foreach ($_GET as $key => $value) {
    if (substr($key, 0, 4) == "vnp_") {
        $inputData[$key] = $value;
    }
}

unset($inputData['vnp_SecureHash']);
ksort($inputData);
$i = 0;
$hashdata = "";
foreach ($inputData as $key => $value) {
    if ($i == 1) {
        $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
    } else {
        $hashdata .= urlencode($key) . "=" . urlencode($value);
        $i = 1;
    }
}

$secureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VNPAY Return</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { text-align: center; padding: 50px; background: #f5f7fb; }
        .result-box { background: white; padding: 30px; border-radius: 10px; max-width: 500px; margin: 0 auto; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .btn { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #4361ee; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="result-box">
        <?php
        if ($secureHash == $vnp_SecureHash) {
            if ($_GET['vnp_ResponseCode'] == '00') {
                // Giao dịch thành công
                $orderId = $_GET['vnp_TxnRef'];
                $db = Database::getInstance();
                
                // Cập nhật trạng thái đơn hàng thành 'processing' (hoặc 'paid')
                $stmt = $db->prepare("UPDATE orders SET status = 'processing', payment_method = 'vnpay' WHERE id = ?");
                $stmt->execute([$orderId]);
                
                // Gửi email xác nhận (vì lúc checkout chưa gửi)
                try {
                    // Lấy thông tin đơn hàng
                    $stmt = $db->prepare("SELECT * FROM orders WHERE id = ?");
                    $stmt->execute([$orderId]);
                    $order = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    // Lấy thông tin user
                    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
                    $stmt->execute([$order['user_id']]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    // Lấy chi tiết sản phẩm
                    $stmt = $db->prepare("SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
                    $stmt->execute([$orderId]);
                    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    require_once '../includes/email_helper.php';
                    sendOrderConfirmationEmail($orderId, $user['email'], $user['full_name'] ?? $user['username'], $order['total'], $order['shipping_address'], $items);
                } catch (Exception $e) {
                    // Bỏ qua lỗi gửi mail để không ảnh hưởng hiển thị
                }

                echo "<h2 class='success'>Thanh toán thành công!</h2>";
                echo "<p>Đơn hàng #$orderId của bạn đã được thanh toán qua VNPAY.</p>";
                echo "<a href='success.php?order_id=$orderId' class='btn'>Xem chi tiết đơn hàng</a>";
            } else {
                // Giao dịch không thành công
                echo "<h2 class='error'>Thanh toán thất bại</h2>";
                echo "<p>Mã lỗi: " . htmlspecialchars($_GET['vnp_ResponseCode']) . "</p>";
                echo "<p>Vui lòng thử lại hoặc chọn phương thức thanh toán khác.</p>";
                echo "<a href='checkout.php' class='btn'>Quay lại thanh toán</a>";
            }
        } else {
            echo "<h2 class='error'>Chữ ký không hợp lệ</h2>";
            echo "<p>Có lỗi xảy ra trong quá trình xác thực dữ liệu.</p>";
            echo "<a href='home.php' class='btn'>Về trang chủ</a>";
        }
        ?>
    </div>
</body>
</html>