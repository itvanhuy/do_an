<?php
// File: index.php (thư mục gốc)
session_start();

// Load configuration và auth
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Tự động đăng nhập nếu có remember token
Auth::autoLogin();

// Kiểm tra nếu user đã đăng nhập
if (Auth::isLoggedIn()) {
    // Redirect dựa trên role của user
    if (Auth::isAdmin()) {
        header('Location: admin/index.php');
    } else {
        header('Location: pages/home.php');
    }
    exit();
} else {
    // Chưa đăng nhập, redirect đến trang home cho guest
    header('Location: pages/home.php');
    exit();
}
?>