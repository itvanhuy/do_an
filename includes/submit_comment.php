<?php
// File: includes/submit_comment.php
session_start();
require_once 'config.php';
require_once 'auth.php';
require_once 'database.php';

header('Content-Type: application/json');

if (!Auth::isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login to comment']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
    $content = trim($_POST['content'] ?? '');

    if (empty($content)) {
        echo json_encode(['success' => false, 'message' => 'Comment cannot be empty']);
        exit;
    }

    try {
        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
        $stmt->execute([$post_id, $_SESSION['user_id'], $content]);

        echo json_encode(['success' => true, 'message' => 'Comment posted successfully']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error posting comment']);
    }
}
?>