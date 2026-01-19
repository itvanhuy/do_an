<?php
// File: pages/cart_actions.php
session_start();
header('Content-Type: application/json');

require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/database.php';

// Kiểm tra đăng nhập
if (!Auth::isLoggedIn()) {
    echo json_encode([
        'success' => false,
        'message' => 'Please login first'
    ]);
    exit();
}

// Kiểm tra action
if (!isset($_POST['action'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid action'
    ]);
    exit();
}

$action = $_POST['action'];
$userId = $_SESSION['user_id'];

try {
    $db = Database::getInstance();
    
    switch ($action) {
        case 'update':
            // Cập nhật số lượng
            if (!isset($_POST['product_id']) || !isset($_POST['quantity'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Missing parameters'
                ]);
                exit();
            }
            
            $productId = (int)$_POST['product_id'];
            $quantity = (int)$_POST['quantity'];
            
            if ($quantity < 1) {
                // Xóa sản phẩm nếu số lượng = 0
                $stmt = $db->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
                $stmt->execute([$userId, $productId]);
            } else {
                // Cập nhật số lượng
                $stmt = $db->prepare("UPDATE cart SET quantity = ?, updated_at = NOW() WHERE user_id = ? AND product_id = ?");
                $stmt->execute([$quantity, $userId, $productId]);
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Cart updated'
            ]);
            break;
            
        case 'remove':
            // Xóa sản phẩm
            if (!isset($_POST['product_id'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Missing product ID'
                ]);
                exit();
            }
            
            $productId = (int)$_POST['product_id'];
            $stmt = $db->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$userId, $productId]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Product removed from cart'
            ]);
            break;

        case 'apply_discount':
            unset($_SESSION['discount_code']);
            unset($_SESSION['discount_percent']);
            unset($_SESSION['discount_amount']);
            unset($_SESSION['discount_value']);
            unset($_SESSION['discount_type']);
            unset($_SESSION['discount_error']);

            if (!isset($_POST['discount_code'])) {
                $_SESSION['discount_error'] = 'Please enter a discount code.';
                echo json_encode(['success' => false, 'message' => 'Please enter a discount code.']);
                break;
            }

            $discountCode = strtoupper(trim($_POST['discount_code']));

            if (empty($discountCode)) {
                echo json_encode(['success' => true, 'message' => 'Discount removed.']);
                break;
            }

            // Check database for coupon
            $stmt = $db->prepare("SELECT * FROM coupons WHERE code = ? AND is_active = 1 AND (expiry_date IS NULL OR expiry_date >= CURDATE())");
            $stmt->execute([$discountCode]);
            $coupon = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($coupon) {
                // Calculate subtotal to check min_order_amount
                $stmtCart = $db->prepare("SELECT SUM(c.quantity * p.price) as subtotal FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
                $stmtCart->execute([$userId]);
                $cartData = $stmtCart->fetch(PDO::FETCH_ASSOC);
                $subtotal = $cartData['subtotal'] ?? 0;

                if ($subtotal < $coupon['min_order_amount']) {
                    $_SESSION['discount_error'] = 'Minimum order amount for this code is ' . number_format($coupon['min_order_amount'], 0, ',', '.') . ' VND';
                    echo json_encode(['success' => false, 'message' => $_SESSION['discount_error']]);
                    break;
                }

                $_SESSION['discount_code'] = $discountCode;
                $_SESSION['discount_value'] = $coupon['discount_value'];
                $_SESSION['discount_type'] = $coupon['discount_type'];
                
                // Giữ lại discount_percent để tương thích ngược nếu cần, nhưng ưu tiên dùng type/value
                if ($coupon['discount_type'] == 'percent') $_SESSION['discount_percent'] = $coupon['discount_value'];
                
                echo json_encode(['success' => true, 'message' => 'Discount code applied!']);
            } else {
                $_SESSION['discount_error'] = 'Invalid or expired discount code.';
                echo json_encode(['success' => false, 'message' => 'Invalid or expired discount code.']);
            }
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action'
            ]);
    }
    
} catch (Exception $e) {
    // Nếu database lỗi, dùng session
    handleCartActionsWithSession($action);
}

// Xử lý với session nếu database lỗi
function handleCartActionsWithSession($action) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    switch ($action) {
        case 'update':
            $productId = (int)$_POST['product_id'];
            $quantity = (int)$_POST['quantity'];
            
            if ($quantity < 1) {
                unset($_SESSION['cart'][$productId]);
            } else {
                $_SESSION['cart'][$productId] = $quantity;
            }
            break;
            
        case 'remove':
            $productId = (int)$_POST['product_id'];
            unset($_SESSION['cart'][$productId]);
            break;
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Cart updated (session)'
    ]);
}
?>