<?php
// File: admin/get_notifications.php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/database.php';

// Kiểm tra quyền Admin
if (!Auth::isAdmin()) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$db = Database::getInstance();

// 1. Đếm đơn hàng chờ xử lý
$stmt = $db->query("SELECT COUNT(*) as count FROM orders WHERE status = 'pending'");
$pendingOrdersCount = $stmt->fetch()['count'];

// 2. Đếm sản phẩm sắp hết hàng
$stmt = $db->query("SELECT COUNT(*) as count FROM products WHERE stock_quantity < 5");
$lowStockCount = $stmt->fetch()['count'];

// 3. Đếm đánh giá chờ duyệt
$pendingReviewsCount = 0;
try {
    $stmt = $db->query("SELECT COUNT(*) as count FROM reviews WHERE status = 'pending'");
    $pendingReviewsCount = $stmt->fetch()['count'];
} catch (Exception $e) {}

// 4. Đếm người dùng mới hôm nay
$newUsersCount = 0;
try {
    $stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE DATE(created_at) = CURDATE()");
    $newUsersCount = $stmt->fetch()['count'];
} catch (Exception $e) {}

// 5. Đếm bình luận chờ duyệt
$pendingCommentsCount = 0;
try {
    $stmt = $db->query("SELECT COUNT(*) as count FROM comments WHERE status = 'pending'");
    $pendingCommentsCount = $stmt->fetch()['count'];
} catch (Exception $e) {}

$total = $pendingOrdersCount + $lowStockCount + $pendingReviewsCount + $newUsersCount + $pendingCommentsCount;

header('Content-Type: application/json');
echo json_encode([
    'total' => $total,
    'orders' => $pendingOrdersCount,
    'stock' => $lowStockCount,
    'reviews' => $pendingReviewsCount,
    'users' => $newUsersCount,
    'comments' => $pendingCommentsCount
]);