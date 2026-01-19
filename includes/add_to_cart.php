<?php
// File: includes/add_to_cart.php
session_start();
header('Content-Type: application/json');

// Debug
error_log("=== ADD TO CART REQUEST ===");
error_log("POST Data: " . print_r($_POST, true));
error_log("SESSION Data: " . print_r($_SESSION, true));

require_once 'config.php';
require_once 'auth.php';
require_once 'database.php';

// 1. Kiểm tra đăng nhập
if (!Auth::isLoggedIn()) {
    error_log("User not logged in");
    echo json_encode([
        'success' => false,
        'message' => 'Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng',
        'cart_count' => 0
    ]);
    exit();
}

// 2. Kiểm tra dữ liệu
if (!isset($_POST['product_id']) || empty($_POST['product_id'])) {
    error_log("Missing product_id");
    echo json_encode([
        'success' => false,
        'message' => 'Thiếu thông tin sản phẩm',
        'cart_count' => 0
    ]);
    exit();
}

$productId = (int)$_POST['product_id'];
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
$userId = $_SESSION['user_id'];

error_log("Processing: user_id=$userId, product_id=$productId, quantity=$quantity");

// 3. Kiểm tra số lượng
if ($quantity < 1) {
    $quantity = 1;
}

try {
    $db = Database::getInstance();
    
    // 4. Kiểm tra sản phẩm có tồn tại không
    $stmt = $db->prepare("SELECT id, name, price, stock_quantity, image FROM products WHERE id = ? AND status = 'active'");
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        error_log("Product not found: $productId");
        echo json_encode([
            'success' => false,
            'message' => 'Sản phẩm không tồn tại',
            'cart_count' => 0
        ]);
        exit();
    }
    
    error_log("Product found: " . $product['name']);
    
    // 5. Kiểm tra số lượng tồn kho
    if ($product['stock_quantity'] < $quantity) {
        error_log("Not enough stock");
        echo json_encode([
            'success' => false,
            'message' => 'Số lượng tồn kho không đủ',
            'cart_count' => 0
        ]);
        exit();
    }
    
    // 6. Kiểm tra xem table cart có tồn tại không
    $tableExists = false;
    try {
        $db->query("SELECT 1 FROM cart LIMIT 1");
        $tableExists = true;
        error_log("Cart table exists");
    } catch (Exception $e) {
        error_log("Cart table doesn't exist, creating...");
        // Tạo table cart nếu chưa tồn tại
        $sql = "CREATE TABLE IF NOT EXISTS cart (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            product_id INT NOT NULL,
            quantity INT NOT NULL DEFAULT 1,
            price DECIMAL(10,2) NOT NULL,
            product_name VARCHAR(255),
            product_image VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_cart_item (user_id, product_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        try {
            $db->exec($sql);
            $tableExists = true;
            error_log("Cart table created successfully");
        } catch (Exception $e2) {
            error_log("Failed to create cart table: " . $e2->getMessage());
        }
    }
    
    if (!$tableExists) {
        // Nếu không thể tạo table, dùng session
        return addToCartSession($userId, $product, $quantity);
    }
    
    // 7. Kiểm tra sản phẩm đã có trong giỏ hàng chưa
    $stmt = $db->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$userId, $productId]);
    $existingItem = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existingItem) {
        // Cập nhật số lượng
        $newQuantity = $existingItem['quantity'] + $quantity;
        
        // Kiểm tra tồn kho
        if ($product['stock_quantity'] < $newQuantity) {
            error_log("Not enough stock after update");
            echo json_encode([
                'success' => false,
                'message' => 'Số lượng tồn kho không đủ',
                'cart_count' => 0
            ]);
            exit();
        }
        
        $stmt = $db->prepare("UPDATE cart SET quantity = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$newQuantity, $existingItem['id']]);
        error_log("Cart updated: new quantity = $newQuantity");
    } else {
        // Thêm mới vào giỏ hàng
        $stmt = $db->prepare("INSERT INTO cart (user_id, product_id, quantity, price, product_name, product_image, created_at) 
                             VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([
            $userId, 
            $productId, 
            $quantity, 
            $product['price'],
            $product['name'],
            $product['image']
        ]);
        error_log("New item added to cart");
    }
    
    // 8. Đếm số lượng sản phẩm trong giỏ hàng
    $stmt = $db->prepare("SELECT SUM(quantity) as total_items FROM cart WHERE user_id = ?");
    $stmt->execute([$userId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $cartCount = $result['total_items'] ?? 0;
    $_SESSION['cart_count'] = $cartCount;
    
    error_log("Cart count: $cartCount");
    
    // 9. Trả về kết quả thành công
    echo json_encode([
        'success' => true,
        'message' => 'Đã thêm sản phẩm vào giỏ hàng',
        'cart_count' => (int)$cartCount,
        'product_name' => $product['name']
    ]);
    
} catch (Exception $e) {
    error_log("Add to cart error: " . $e->getMessage());
    error_log("Error trace: " . $e->getTraceAsString());
    
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi hệ thống: ' . $e->getMessage(),
        'cart_count' => 0
    ]);
}

// Hàm dự phòng: Lưu giỏ hàng vào session nếu database có vấn đề
function addToCartSession($userId, $product, $quantity) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    $productId = $product['id'];
    
    // Kiểm tra sản phẩm đã có trong session cart chưa
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$productId] = [
            'id' => $productId,
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $quantity,
            'image' => $product['image'],
            'stock_quantity' => $product['stock_quantity']
        ];
    }
    
    // Tính tổng số lượng
    $cartCount = 0;
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += $item['quantity'];
    }
    
    error_log("Added to session cart. Total items: $cartCount");
    
    echo json_encode([
        'success' => true,
        'message' => 'Đã thêm sản phẩm vào giỏ hàng (session)',
        'cart_count' => $cartCount,
        'product_name' => $product['name']
    ]);
    exit();
}
?>