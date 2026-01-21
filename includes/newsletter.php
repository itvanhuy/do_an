<?php
// File: includes/newsletter.php
session_start();
require_once 'config.php';
require_once 'database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address']);
    exit();
}

try {
    $db = Database::getInstance();
    
    // Kiểm tra email đã đăng ký chưa
    $stmt = $db->prepare("SELECT id FROM newsletter_subscribers WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
        echo json_encode(['success' => true, 'message' => 'You are already subscribed!']);
        exit();
    }
    
    // Thêm vào database
    $stmt = $db->prepare("INSERT INTO newsletter_subscribers (email, subscribed_at) VALUES (?, NOW())");
    $stmt->execute([$email]);
    
    echo json_encode(['success' => true, 'message' => 'Thank you for subscribing to our newsletter!']);
    
} catch (PDOException $e) {
    error_log("Newsletter subscription error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again later.']);
}