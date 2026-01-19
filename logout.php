<?php
// File: logout.php
session_start();

// Load configuration và auth
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Đăng xuất user
Auth::logout();

// Redirect về trang chủ
header('Location: index.php');
exit();
?>