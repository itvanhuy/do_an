<?php
// File: includes/vnpay_config.php
date_default_timezone_set('Asia/Ho_Chi_Minh');

/*
 * Cấu hình VNPAY
 * Đăng ký tài khoản sandbox tại: https://sandbox.vnpayment.vn/devdashboard/
 */
  
$vnp_TmnCode = "YOUR_TMN_CODE"; // Thay bằng Website ID của bạn (VD: CGXZLS0Z)
$vnp_HashSecret = "YOUR_HASH_SECRET"; // Thay bằng Secret Key của bạn (VD: XNBCJFAKRN2...)
$vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";

// Tự động phát hiện giao thức (http/https) và tên miền
$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
// Lấy đường dẫn thư mục hiện tại của file đang chạy (thường là /pages/checkout.php -> /pages)
$path = dirname($_SERVER['PHP_SELF']);
$path = str_replace('\\', '/', $path); // Fix lỗi đường dẫn trên Windows
$path = rtrim($path, '/');

// Đường dẫn trả về tự động
$vnp_Returnurl = $protocol . $host . $path . "/vnpay_return.php";

$vnp_apiUrl = "http://sandbox.vnpayment.vn/merchant_webapi/merchant.html";
$vnp_Api = "https://sandbox.vnpayment.vn/merchant_webapi/api/transaction";

// Thời gian hết hạn thanh toán (15 phút)
$startTime = date("YmdHis");
$expire = date('YmdHis',strtotime('+15 minutes',strtotime($startTime)));
?>