<?php
// File: pages/success.php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/database.php';

// Tự động đăng nhập nếu có remember token
Auth::autoLogin();

if (!isset($_GET['order_id'])) {
    header('Location: home.php');
    exit();
}

$orderId = (int)$_GET['order_id'];
$order = null;
$items = [];

try {
    $db = Database::getInstance();
    
    // Lấy thông tin đơn hàng
    $stmt = $db->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($order) {
        // Kiểm tra quyền xem đơn hàng (nếu đã đăng nhập)
        if (Auth::isLoggedIn() && $order['user_id'] != $_SESSION['user_id'] && !Auth::isAdmin()) {
            // Nếu không phải đơn của mình và không phải admin -> redirect
            header('Location: home.php');
            exit();
        }
        
        // Lấy chi tiết sản phẩm
        $stmt = $db->prepare("
            SELECT oi.*, p.name, p.image 
            FROM order_items oi
            LEFT JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$orderId]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    error_log("Error fetching order success: " . $e->getMessage());
}

if (!$order) {
    echo "Order not found.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/success.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main class="container">
        <div class="success-message">
            <i class="fas fa-check-circle success-icon"></i>
            <h1>Thank You!</h1>
            <p>Your order has been placed successfully.</p>
            <p>Order ID: <strong>#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></strong></p>
        </div>

        <div class="order-details">
            <h2>Order Details</h2>
            <div class="order-info-grid">
                <div class="info-item">
                    <strong>Order Date:</strong>
                    <span><?php echo date('F j, Y, g:i a', strtotime($order['created_at'])); ?></span>
                </div>
                <div class="info-item">
                    <strong>Payment Method:</strong>
                    <!-- SỬA LỖI: Thêm ?? 'COD' để xử lý trường hợp null -->
                    <span><?php echo htmlspecialchars($order['payment_method'] ?? 'COD'); ?></span>
                </div>
                <div class="info-item">
                    <strong>Shipping Address:</strong>
                    <!-- SỬA LỖI: Thêm ?? '' để xử lý trường hợp null -->
                    <span><?php echo nl2br(htmlspecialchars($order['shipping_address'] ?? '')); ?></span>
                </div>
                <div class="info-item total">
                    <strong>Total Amount:</strong>
                    <span><?php echo number_format($order['total'], 0, ',', '.'); ?> VND</span>
                </div>
            </div>
        </div>

        <!-- Hiển thị danh sách sản phẩm đã mua -->
        <?php if (!empty($items)): ?>
        <div class="order-details" style="margin-top: 20px;">
            <h2>Ordered Items</h2>
            <ul style="list-style: none; padding: 0;">
                <?php foreach ($items as $item): ?>
                <li style="display: flex; align-items: center; padding: 10px 0; border-bottom: 1px solid #eee;">
                    <img src="../img/product/<?php echo htmlspecialchars($item['image'] ?? 'default.jpg'); ?>" 
                         alt="<?php echo htmlspecialchars($item['name'] ?? 'Product'); ?>" 
                         style="width: 50px; height: 50px; object-fit: contain; margin-right: 15px; border: 1px solid #eee; border-radius: 5px;"
                         onerror="this.src='../img/product/default.jpg'">
                    <div style="flex: 1;">
                        <!-- SỬA LỖI: Thêm ?? '' cho tên sản phẩm -->
                        <div style="font-weight: 600;"><?php echo htmlspecialchars($item['name'] ?? 'Unknown Product'); ?></div>
                        <div style="color: #666; font-size: 0.9em;">
                            <?php echo $item['quantity']; ?> x <?php echo number_format($item['price'], 0, ',', '.'); ?> VND
                        </div>
                    </div>
                    <div style="font-weight: 600;">
                        <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?> VND
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <div class="next-steps">
            <h3>What's Next?</h3>
            <ul>
                <li><i class="fas fa-envelope"></i> You will receive an order confirmation email shortly.</li>
                <li><i class="fas fa-box"></i> We will process your order and ship it within 1-2 business days.</li>
                <li><i class="fas fa-truck"></i> You can track your order status in "My Orders".</li>
            </ul>
        </div>

        <div class="action-buttons">
            <a href="orders.php" class="btn btn-primary">View My Orders</a>
            <a href="shop.php" class="btn btn-secondary">Continue Shopping</a>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
</body>
</html>