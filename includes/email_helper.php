<?php
// File: includes/email_helper.php

function sendOrderConfirmationEmail($orderId, $userEmail, $userName, $total, $address, $items) {
    $subject = "Order Confirmation - Order #$orderId";
    $siteName = defined('SITE_NAME') ? SITE_NAME : 'TechShop';
    
    // Format currency
    $formattedTotal = number_format($total, 0, ',', '.');
    $date = date('d/m/Y H:i');
    
    $message = "
    <html>
    <head>
        <title>Order Confirmation</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
            .header { background-color: #f8f9fa; padding: 15px; text-align: center; border-bottom: 1px solid #eee; }
            .header h2 { margin: 0; color: #007bff; }
            .content { padding: 20px 0; }
            .order-info { margin-bottom: 20px; background: #f9f9f9; padding: 15px; border-radius: 5px; }
            .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
            .table th { background-color: #eee; text-align: left; padding: 10px; border-bottom: 2px solid #ddd; }
            .table td { padding: 10px; border-bottom: 1px solid #ddd; }
            .total-row td { font-weight: bold; font-size: 1.1em; background-color: #f8f9fa; }
            .footer { margin-top: 30px; font-size: 12px; color: #777; text-align: center; border-top: 1px solid #eee; padding-top: 15px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>$siteName</h2>
                <p>Order Confirmation</p>
            </div>
            <div class='content'>
                <p>Hello <strong>" . htmlspecialchars($userName) . "</strong>,</p>
                <p>Thank you for your order! We are pleased to confirm that we have received your order.</p>
                
                <div class='order-info'>
                    <p><strong>Order ID:</strong> #$orderId</p>
                    <p><strong>Date:</strong> $date</p>
                    <p><strong>Shipping Address:</strong><br>" . nl2br(htmlspecialchars($address)) . "</p>
                </div>
                
                <h3>Order Summary</h3>
                <table class='table'>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th style='text-align: center;'>Qty</th>
                            <th style='text-align: right;'>Price</th>
                            <th style='text-align: right;'>Total</th>
                        </tr>
                    </thead>
                    <tbody>";
    
    foreach ($items as $item) {
        $itemName = htmlspecialchars($item['name']);
        $itemPrice = number_format($item['price'], 0, ',', '.');
        $itemQty = $item['quantity'];
        $itemSubtotal = number_format($item['price'] * $item['quantity'], 0, ',', '.');
        
        $message .= "
                        <tr>
                            <td>$itemName</td>
                            <td style='text-align: center;'>$itemQty</td>
                            <td style='text-align: right;'>$itemPrice ₫</td>
                            <td style='text-align: right;'>$itemSubtotal ₫</td>
                        </tr>";
    }
    
    $message .= "
                        <tr class='total-row'>
                            <td colspan='3' style='text-align: right;'>Grand Total:</td>
                            <td style='text-align: right;'>$formattedTotal ₫</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class='footer'>
                <p>If you have any questions, please reply to this email or contact us at support@techshop.com</p>
                <p>&copy; " . date('Y') . " $siteName. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ";

    // Headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: $siteName <no-reply@techshop.com>" . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // Send email (sử dụng @ để ẩn lỗi nếu server chưa cấu hình mail)
    return @mail($userEmail, $subject, $message, $headers);
}

function sendPasswordResetEmail($email, $token) {
    $subject = "Reset Your Password - " . SITE_NAME;
    $resetLink = SITE_URL . "create_new_password.php?token=" . $token . "&email=" . urlencode($email);
    
    $message = "
    <html>
    <head>
        <title>Password Reset</title>
    </head>
    <body>
        <h2>Password Reset Request</h2>
        <p>We received a request to reset your password. Click the link below to create a new password:</p>
        <p><a href='$resetLink' style='background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Reset Password</a></p>
        <p>Or copy and paste this link into your browser:</p>
        <p>$resetLink</p>
        <p>This link will expire in 1 hour.</p>
        <p>If you did not request this, please ignore this email.</p>
    </body>
    </html>
    ";

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: " . SITE_NAME . " <no-reply@techshop.com>" . "\r\n";

    return @mail($email, $subject, $message, $headers);
}
?>