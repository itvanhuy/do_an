<?php
// File: pages/checkout.php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/database.php';

<<<<<<< HEAD
// ==========================================================================
// 1. KHỞI TẠO & KIỂM TRA ĐĂNG NHẬP
// ==========================================================================

// Tự động đăng nhập nếu có remember token
Auth::autoLogin();

// Bắt buộc đăng nhập để thanh toán
=======
// Tự động đăng nhập nếu có remember token
Auth::autoLogin();

// Kiểm tra đăng nhập
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
if (!Auth::isLoggedIn()) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header('Location: ../login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$db = Database::getInstance();
$user = Auth::getUser();

<<<<<<< HEAD
// ==========================================================================
// 2. LẤY DỮ LIỆU NGƯỜI DÙNG & GIỎ HÀNG
// ==========================================================================

// Lấy thông tin địa chỉ/sđt user để điền sẵn vào form
=======
// Lấy thông tin địa chỉ user để điền sẵn
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
$stmt = $db->prepare("SELECT address, phone, full_name FROM users WHERE id = ?");
$stmt->execute([$userId]);
$userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

$cartItems = [];
$subtotal = 0;
$total = 0;
$shippingFee = 30000; // Phí ship cố định
$discountAmount = 0;
$isBuyNow = false;

<<<<<<< HEAD
// --- TRƯỜNG HỢP 1: MUA NGAY (BUY NOW) ---
=======
// Kiểm tra xem là "Mua ngay" (Buy Now) hay "Thanh toán giỏ hàng"
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
if (isset($_GET['product_id']) && isset($_GET['quantity'])) {
    // --- TRƯỜNG HỢP MUA NGAY ---
    $isBuyNow = true;
    $productId = (int)$_GET['product_id'];
    $quantity = (int)$_GET['quantity'];
    
    $stmt = $db->prepare("SELECT * FROM products WHERE id = ? AND status = 'active'");
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($product) {
        $itemTotal = $product['price'] * $quantity;
        $subtotal += $itemTotal;
        
        $cartItems[] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'image' => $product['image'],
            'quantity' => $quantity,
            'total' => $itemTotal
        ];
    }
<<<<<<< HEAD
} 
// --- TRƯỜNG HỢP 2: THANH TOÁN TỪ GIỎ HÀNG (CART) ---
else {
=======
} else {
    // --- TRƯỜNG HỢP THANH TOÁN GIỎ HÀNG ---
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
    $stmt = $db->prepare("
        SELECT c.product_id as id, p.name, p.price, p.image, c.quantity 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = ?
    ");
    $stmt->execute([$userId]);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($cartItems as $key => $item) {
        $itemTotal = $item['price'] * $item['quantity'];
        $cartItems[$key]['total'] = $itemTotal;
        $subtotal += $itemTotal;
    }
    
    // Áp dụng giảm giá từ session (chỉ áp dụng cho giỏ hàng)
    if (isset($_SESSION['discount_amount'])) {
        $discountAmount = $_SESSION['discount_amount'];
    }
}

// Nếu không có sản phẩm nào
if (empty($cartItems)) {
    header('Location: cart.php');
    exit();
}

$total = $subtotal + $shippingFee - $discountAmount;

<<<<<<< HEAD
// ==========================================================================
// 3. XỬ LÝ ĐẶT HÀNG (POST REQUEST)
// ==========================================================================
=======
// Xử lý đặt hàng (POST Request)
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shippingAddress = trim($_POST['shipping_address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $paymentMethod = $_POST['payment_method'] ?? 'cod';
    
    if (empty($shippingAddress) || empty($phone)) {
        $error = 'Please fill in all required fields.';
    } else {
        try {
            $conn = $db->getConnection();
            $conn->beginTransaction();
            
<<<<<<< HEAD
            // BƯỚC 1: Tạo đơn hàng trong bảng orders
            $stmt = $conn->prepare("INSERT INTO orders (user_id, total, shipping_address, payment_method, status, created_at) VALUES (?, ?, ?, ?, 'pending', NOW())");
=======
            // 1. Tạo đơn hàng
            $stmt = $conn->prepare("INSERT INTO orders (user_id, total, shipping_address, payment_method, status, created_at) VALUES (?, ?, ?, ?, 'pending', NOW())");
            // Lưu ý: Có thể lưu thêm phone vào bảng orders nếu cần, ở đây gộp vào address hoặc cần alter table
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
            $fullAddress = $shippingAddress . " (Phone: " . $phone . ")";
            $stmt->execute([$userId, $total, $fullAddress, $paymentMethod]);
            $orderId = $conn->lastInsertId();
            
<<<<<<< HEAD
            // BƯỚC 2: Thêm chi tiết sản phẩm (order_items) & Trừ tồn kho
=======
            // 2. Thêm chi tiết đơn hàng & Trừ tồn kho
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
            $stmtItem = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmtStock = $conn->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");
            
            foreach ($cartItems as $item) {
                $stmtItem->execute([$orderId, $item['id'], $item['quantity'], $item['price']]);
                $stmtStock->execute([$item['quantity'], $item['id']]);
            }
            
<<<<<<< HEAD
            // BƯỚC 3: Xóa giỏ hàng (nếu không phải mua ngay)
=======
            // 3. Xóa giỏ hàng (nếu không phải mua ngay)
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
            if (!$isBuyNow) {
                $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
                $stmt->execute([$userId]);
                
<<<<<<< HEAD
                // Xóa mã giảm giá đã dùng
=======
                // Xóa session giảm giá
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
                unset($_SESSION['discount_code']);
                unset($_SESSION['discount_percent']);
                unset($_SESSION['discount_amount']);
            }
            
            $conn->commit();
            
<<<<<<< HEAD
            // ==================================================================
            // BƯỚC 4: XỬ LÝ THANH TOÁN (VNPAY / COD)
            // ==================================================================
            
            /* 
             * NOTE: Tính năng VNPAY đang bảo trì.
             * Code VNPAY cũ đã được ẩn để tránh lỗi.
             * Hiện tại tất cả đơn hàng sẽ được xử lý như COD/Chuyển khoản.
             */

            // Gửi email xác nhận đơn hàng
            require_once '../includes/email_helper.php';
            $userEmail = $user['email'];
            $userName = $userInfo['full_name'] ?? $user['username'];
            sendOrderConfirmationEmail($orderId, $userEmail, $userName, $total, $fullAddress, $cartItems);
            
            // Chuyển hướng đến trang thành công
            header('Location: success.php?order_id=' . $orderId);
            exit();
=======
            // --- XỬ LÝ THANH TOÁN VNPAY ---
            if ($paymentMethod == 'vnpay') {
                require_once '../includes/vnpay_config.php';
                
                $vnp_TxnRef = $orderId; // Mã đơn hàng
                $vnp_OrderInfo = 'Thanh toan don hang #' . $orderId;
                $vnp_OrderType = 'billpayment';
                $vnp_Amount = $total * 100; // VNPAY tính bằng đồng (nhân 100)
                $vnp_Locale = 'vn';
                $vnp_BankCode = ''; // Để trống để người dùng chọn ngân hàng
                $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
                
                $inputData = array(
                    "vnp_Version" => "2.1.0",
                    "vnp_TmnCode" => $vnp_TmnCode,
                    "vnp_Amount" => $vnp_Amount,
                    "vnp_Command" => "pay",
                    "vnp_CreateDate" => date('YmdHis'),
                    "vnp_CurrCode" => "VND",
                    "vnp_IpAddr" => $vnp_IpAddr,
                    "vnp_Locale" => $vnp_Locale,
                    "vnp_OrderInfo" => $vnp_OrderInfo,
                    "vnp_OrderType" => $vnp_OrderType,
                    "vnp_ReturnUrl" => $vnp_Returnurl,
                    "vnp_TxnRef" => $vnp_TxnRef,
                    "vnp_ExpireDate" => $expire
                );
                
                if (isset($vnp_BankCode) && $vnp_BankCode != "") {
                    $inputData['vnp_BankCode'] = $vnp_BankCode;
                }
                
                ksort($inputData);
                $query = "";
                $i = 0;
                $hashdata = "";
                foreach ($inputData as $key => $value) {
                    if ($i == 1) {
                        $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
                    } else {
                        $hashdata .= urlencode($key) . "=" . urlencode($value);
                        $i = 1;
                    }
                    $query .= urlencode($key) . "=" . urlencode($value) . '&';
                }
                
                $vnp_Url = $vnp_Url . "?" . $query;
                if (isset($vnp_HashSecret)) {
                    $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
                    $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
                }
                
                header('Location: ' . $vnp_Url);
                exit();
            } 
            // --- KẾT THÚC VNPAY ---
            else {
                // Gửi email xác nhận đơn hàng (cho COD/Bank Transfer)
                require_once '../includes/email_helper.php';
                $userEmail = $user['email'];
                $userName = $userInfo['full_name'] ?? $user['username'];
                sendOrderConfirmationEmail($orderId, $userEmail, $userName, $total, $fullAddress, $cartItems);
                
                header('Location: success.php?order_id=' . $orderId);
                exit();
            }
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
            
        } catch (Exception $e) {
            $conn->rollBack();
            $error = 'Order failed: ' . $e->getMessage();
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
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/payment.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container">
        <h1>Checkout</h1>
        <?php if (isset($error)): ?>
            <div class="error-message" style="background:#f8d7da; color:#721c24; padding:10px; margin-bottom:20px; border-radius:5px;"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <div class="checkout-content">
            <div class="checkout-form">
                <h2>Shipping Information</h2>
                <form method="POST">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" value="<?php echo htmlspecialchars($userInfo['full_name'] ?? ''); ?>" readonly style="background:#f9f9f9;">
                    </div>
                    <div class="form-group">
                        <label>Phone Number *</label>
                        <input type="text" name="phone" required value="<?php echo htmlspecialchars($userInfo['phone'] ?? ''); ?>" placeholder="Enter your phone number">
                    </div>
                    <div class="form-group">
                        <label>Shipping Address *</label>
                        <textarea name="shipping_address" required rows="3" placeholder="Enter full address"><?php echo htmlspecialchars($userInfo['address'] ?? ''); ?></textarea>
                    </div>
                    
                    <h2>Payment Method</h2>
                    <div class="payment-methods">
                        <label class="payment-option"><input type="radio" name="payment_method" value="cod" checked> Cash on Delivery (COD)</label>
<<<<<<< HEAD
                        <label class="payment-option" style="opacity: 0.6; cursor: not-allowed;" title="Đang bảo trì">
                            <input type="radio" name="payment_method" value="vnpay" disabled> 
                            Thanh toán qua VNPAY / Ngân hàng (Đang bảo trì) <img src="https://sandbox.vnpayment.vn/paymentv2/images/assets/merchant-logo.png" alt="VNPAY" style="height: 20px; margin-left: 10px;">
=======
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="vnpay"> 
                            Thanh toán qua VNPAY / Ngân hàng <img src="https://sandbox.vnpayment.vn/paymentv2/images/assets/merchant-logo.png" alt="VNPAY" style="height: 20px; margin-left: 10px;">
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary place-order-btn">Place Order</button>
                </form>
            </div>
            
            <div class="order-summary">
                <h2>Order Summary</h2>
                <div class="order-items">
                    <?php foreach ($cartItems as $item): ?>
                    <div class="order-item">
                        <img src="../img/product/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        <div class="item-details">
                            <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                            <p><?php echo $item['quantity']; ?> x <?php echo number_format($item['price'], 0, ',', '.'); ?>₫</p>
                        </div>
                        <div class="item-total"><?php echo number_format($item['total'], 0, ',', '.'); ?>₫</div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="order-totals">
                    <div class="total-row"><span>Subtotal</span> <span><?php echo number_format($subtotal, 0, ',', '.'); ?>₫</span></div>
                    <div class="total-row"><span>Shipping</span> <span><?php echo number_format($shippingFee, 0, ',', '.'); ?>₫</span></div>
                    <?php if ($discountAmount > 0): ?>
                    <div class="total-row discount"><span>Discount</span> <span>-<?php echo number_format($discountAmount, 0, ',', '.'); ?>₫</span></div>
                    <?php endif; ?>
                    <div class="total-row grand-total"><span>Total</span> <span><?php echo number_format($total, 0, ',', '.'); ?>₫</span></div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>