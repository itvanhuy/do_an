<?php
// File: pages/orders.php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/database.php';

// Tự động đăng nhập nếu có remember token
Auth::autoLogin();

// Lấy thông tin user nếu đã đăng nhập
$isLoggedIn = Auth::isLoggedIn();
if (!$isLoggedIn) {
    header('Location: ../login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$orders = [];

try {
    $db = Database::getInstance();
    
    // Lấy tất cả đơn hàng của user
    $orderStmt = $db->prepare("
        SELECT * FROM orders 
        WHERE user_id = ? 
        ORDER BY created_at DESC
    ");
    $orderStmt->execute([$userId]);
    $userOrders = $orderStmt->fetchAll(PDO::FETCH_ASSOC);

    // Với mỗi đơn hàng, lấy các sản phẩm tương ứng
    foreach ($userOrders as $order) {
        $itemStmt = $db->prepare("
            SELECT 
                oi.quantity, 
                oi.price, 
                p.name, 
                p.image 
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
        ");
        $itemStmt->execute([$order['id']]);
        $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
        
        $orders[] = [
            'details' => $order,
            'items' => $items
        ];
    }

} catch (Exception $e) {
    error_log("Error fetching orders: " . $e->getMessage());
    // Có thể hiển thị một thông báo lỗi ở đây
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/orders.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .orders-container { max-width: 1000px; margin: 40px auto; padding: 0 20px; min-height: 60vh; }
        .page-title { margin-bottom: 30px; color: #333; border-bottom: 2px solid #eee; padding-bottom: 15px; }
        .no-orders { text-align: center; padding: 50px; color: #777; background: #f9f9f9; border-radius: 10px; }
        .no-orders i { font-size: 50px; margin-bottom: 20px; color: #ddd; }
        .btn-shop { display: inline-block; background: #4361ee; color: white; padding: 10px 25px; border-radius: 25px; text-decoration: none; margin-top: 20px; transition: 0.3s; }
        .btn-shop:hover { background: #3f37c9; transform: translateY(-2px); }
        
        .order-card { background: white; border: 1px solid #eee; border-radius: 10px; margin-bottom: 25px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05); transition: 0.3s; }
        .order-card:hover { box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        
        .order-header { background: #f8f9fa; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; flex-wrap: wrap; gap: 10px; }
        .order-id { font-weight: bold; color: #333; }
        .order-date { color: #666; font-size: 0.9em; }
        .order-status { padding: 5px 12px; border-radius: 20px; font-size: 0.85em; font-weight: 600; text-transform: uppercase; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-confirmed { background: #cce5ff; color: #004085; }
        .status-shipped { background: #d1ecf1; color: #0c5460; }
        .status-delivered { background: #d4edda; color: #155724; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
        
        .order-items { padding: 20px; }
        .item { display: flex; align-items: center; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #f5f5f5; }
        .item:last-child { margin-bottom: 0; padding-bottom: 0; border-bottom: none; }
        .item img { width: 60px; height: 60px; object-fit: contain; border: 1px solid #eee; border-radius: 5px; margin-right: 15px; }
        .item-info h4 { margin: 0 0 5px; font-size: 1em; color: #333; }
        .item-info p { margin: 0; color: #666; font-size: 0.9em; }
        
        .order-footer { padding: 15px 20px; background: #fff; border-top: 1px solid #eee; text-align: right; }
        .order-total { font-size: 1.1em; font-weight: bold; color: #333; }
        .order-total span { color: #e63946; font-size: 1.2em; margin-left: 10px; }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main class="orders-container">
        <h1 class="page-title"><i class="fas fa-shopping-bag"></i> My Orders</h1>
        
        <?php if (empty($orders)): ?>
            <div class="no-orders">
                <i class="fas fa-box-open"></i>
                <p>You haven't placed any orders yet.</p>
                <a href="shop.php" class="btn-shop">Start Shopping</a>
            </div>
        <?php else: ?>
            <div class="orders-list">
                <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div class="order-id">Order #<?php echo str_pad($order['details']['id'], 6, '0', STR_PAD_LEFT); ?></div>
                            <div class="order-date"><i class="far fa-calendar-alt"></i> <?php echo date('d/m/Y H:i', strtotime($order['details']['created_at'])); ?></div>
                            <div class="order-status status-<?php echo $order['details']['status']; ?>">
                                <?php echo ucfirst($order['details']['status']); ?>
                            </div>
                        </div>
                        
                        <div class="order-items">
                            <?php foreach ($order['items'] as $item): ?>
                                <div class="item">
                                    <img src="../img/product/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" onerror="this.src='../img/product/default.jpg'">
                                    <div class="item-info">
                                        <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                        <p><?php echo $item['quantity']; ?> x <?php echo number_format($item['price'], 0, ',', '.'); ?>₫</p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="order-footer">
                            <div class="order-total">
                                Total: <span><?php echo number_format($order['details']['total'], 0, ',', '.'); ?>₫</span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <?php include '../includes/footer.php'; ?>
</body>
</html>