<?php
// Bật session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Mặc định XAMPP không có password
define('DB_NAME', 'techshop');

// Site configuration
// Tự động phát hiện URL gốc của website
$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];

// Xử lý đường dẫn thư mục gốc
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$scriptDir = str_replace('/pages', '', $scriptDir);   // Loại bỏ /pages nếu đang ở trang con
$scriptDir = str_replace('/admin', '', $scriptDir);   // Loại bỏ /admin nếu đang ở admin
$scriptDir = str_replace('/includes', '', $scriptDir); // Loại bỏ /includes
$scriptDir = rtrim($scriptDir, '/');

define('SITE_URL', $protocol . $host . $scriptDir . '/');
define('SITE_NAME', 'TechShop');

// Debug mode
define('DEBUG', true);

// Hiển thị lỗi nếu debug mode
if (DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
?>