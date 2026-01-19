<?php
// File: includes/wishlist_actions.php
session_start();
require_once 'config.php';
require_once 'auth.php';
require_once 'database.php';

header('Content-Type: application/json');

if (!Auth::isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login to use wishlist']);
    exit;
}

$userId = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';
$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

if ($productId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product']);
    exit;
}

$db = Database::getInstance();

try {
    if ($action === 'toggle') {
        // Check if exists
        $stmt = $db->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$userId, $productId]);
        $exists = $stmt->fetch();

        if ($exists) {
            // Remove
            $stmt = $db->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$userId, $productId]);
            echo json_encode(['success' => true, 'status' => 'removed', 'message' => 'Removed from wishlist']);
        } else {
            // Add
            $stmt = $db->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
            $stmt->execute([$userId, $productId]);
            echo json_encode(['success' => true, 'status' => 'added', 'message' => 'Added to wishlist']);
        }
    } elseif ($action === 'remove') {
        $stmt = $db->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$userId, $productId]);
        echo json_encode(['success' => true, 'message' => 'Removed from wishlist']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>