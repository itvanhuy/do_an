<?php
// File: includes/submit_review.php
session_start();
require_once 'config.php';
require_once 'auth.php';
require_once 'database.php';

header('Content-Type: application/json');

if (!Auth::isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login to submit a review']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
    $review = trim($_POST['review'] ?? '');

    if ($product_id <= 0 || $rating < 1 || $rating > 5) {
        echo json_encode(['success' => false, 'message' => 'Invalid rating or product']);
        exit;
    }

    if (empty($review)) {
        echo json_encode(['success' => false, 'message' => 'Please write a review']);
        exit;
    }

    try {
        $db = Database::getInstance();
        // Mặc định status là 'pending' chờ admin duyệt, hoặc 'approved' nếu muốn hiện ngay
        $stmt = $db->prepare("INSERT INTO reviews (product_id, user_id, rating, comment, status) VALUES (?, ?, ?, ?, 'pending')");
        $stmt->execute([$product_id, $_SESSION['user_id'], $rating, $review]);

        echo json_encode(['success' => true, 'message' => 'Review submitted successfully! It will be visible after approval.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error submitting review: ' . $e->getMessage()]);
    }
}
?>