<?php
// File: pages/payment.php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/database.php';

// Tự động đăng nhập nếu có remember token
Auth::autoLogin();

// Kiểm tra đăng nhập
if (!Auth::isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

// Khởi tạo giỏ hàng nếu chưa có
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit();
}

$userId = $_SESSION['user_id'];
$cartItems = [];
$total = 0;
$shippingFee = 30000; // Phí ship cố định

try {
    $db = Database::getInstance();
    
    // Lấy thông tin user
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Lấy sản phẩm trong giỏ
    $placeholders = str_repeat('?,', count($_SESSION['cart']) - 1) . '?';
    $stmt = $db->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute(array_keys($_SESSION['cart']));
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($products as $product) {
        $quantity = $_SESSION['cart'][$product['id']];
        $subtotal = $product['price'] * $quantity;
        $total += $subtotal;
        
        $cartItems[] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'image' => $product['image'],
            'quantity' => $quantity,
            'subtotal' => $subtotal
        ];
    }
    
    $grandTotal = $total + $shippingFee;
    
} catch (Exception $e) {
    die('Database error: ' . $e->getMessage());
}

// Xử lý form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shippingAddress = trim($_POST['shipping_address'] ?? '');
    $paymentMethod = $_POST['payment_method'] ?? '';
    
    if (empty($shippingAddress) || empty($paymentMethod)) {
        $error = 'Please fill in all required fields.';
    } else {
        try {
            $db->getConnection()->begin_transaction();
            
            // Tạo đơn hàng
            $stmt = $db->prepare("INSERT INTO orders (user_id, total, shipping_address, payment_method, status) VALUES (?, ?, ?, ?, 'pending')");
            $stmt->execute([$userId, $grandTotal, $shippingAddress, $paymentMethod]);
            $orderId = $db->getConnection()->insert_id;
            
            // Thêm order items
            $stmt = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            foreach ($cartItems as $item) {
                $stmt->execute([$orderId, $item['id'], $item['quantity'], $item['price']]);
                
                // Giảm stock
                $updateStmt = $db->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");
                $updateStmt->execute([$item['quantity'], $item['id']]);
            }
            
            $db->getConnection()->commit();
            
            // Xóa giỏ hàng
            unset($_SESSION['cart']);
            
            // Redirect đến trang success
            header('Location: success.php?order_id=' . $orderId);
            exit();
            
        } catch (Exception $e) {
            $db->getConnection()->rollback();
            $error = 'Failed to process order: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link rel="stylesheet" href="../css/payment.css">
      <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="../js/payment.js"></script>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container">
        <h1>Checkout</h1>
        
        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <div class="checkout-content">
            <div class="order-summary">
                <h2>Order Summary</h2>
                <div class="order-items">
                    <?php foreach ($cartItems as $item): ?>
                    <div class="order-item">
                        <img src="../img/product/<?php echo htmlspecialchars($item['image']); ?>" 
                             alt="<?php echo htmlspecialchars($item['name']); ?>">
                        <div class="item-details">
                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p>Quantity: <?php echo $item['quantity']; ?></p>
                            <p>Price: <?php echo number_format($item['price'], 0, ',', '.'); ?> VND</p>
                        </div>
                        <div class="item-total">
                            <?php echo number_format($item['subtotal'], 0, ',', '.'); ?> VND
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="order-totals">
                    <div class="total-row">
                        <span>Subtotal:</span>
                        <span><?php echo number_format($total, 0, ',', '.'); ?> VND</span>
                    </div>
                    <div class="total-row">
                        <span>Shipping:</span>
                        <span><?php echo number_format($shippingFee, 0, ',', '.'); ?> VND</span>
                    </div>
                    <div class="total-row grand-total">
                        <span>Total:</span>
                        <span><?php echo number_format($grandTotal, 0, ',', '.'); ?> VND</span>
                    </div>
                </div>
            </div>
            
            <div class="checkout-form">
                <h2>Shipping & Payment Information</h2>
                <form method="POST" action="">
                    <div class="form-section">
                        <h3>Shipping Address</h3>
                        <div class="form-group">
                            <label for="shipping_address">Full Address *</label>
                            <textarea id="shipping_address" name="shipping_address" required
                                placeholder="Enter your full shipping address"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3>Payment Method</h3>
                        <div class="payment-methods">
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="cod" required checked>
                                <span class="payment-label">
                                    <i class="fas fa-money-bill-wave"></i>
                                    Cash on Delivery
                                </span>
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="bank_transfer" required>
                                <span class="payment-label">
                                    <i class="fas fa-university"></i>
                                    Bank Transfer
                                </span>
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="credit_card" required>
                                <span class="payment-label">
                                    <i class="fas fa-credit-card"></i>
                                    Credit Card
                                </span>
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary place-order-btn">
                        <i class="fas fa-check"></i> Place Order
                    </button>
                </form>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
