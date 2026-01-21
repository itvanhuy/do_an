<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/email_helper.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    
    if (empty($email)) {
        $error = "Please enter your email address.";
    } else {
        $db = Database::getInstance();
        
        // Check if email exists
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            // Tự động tạo bảng password_resets nếu chưa tồn tại
            $db->exec("CREATE TABLE IF NOT EXISTS password_resets (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) NOT NULL,
                token VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                expires_at DATETIME NOT NULL
            )");
            
            // Generate token
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hour
            
            // Save to DB
            $stmt = $db->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$email, $token, $expires]);
            
            // Send email
            if (sendPasswordResetEmail($email, $token)) {
                $message = "We have sent a password reset link to your email.";
            } else {
                $error = "Failed to send email. Please try again later.";
            }
        } else {
            $message = "If an account exists with this email, we have sent a password reset link.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <section>
        <form action="" method="POST">
            <h1>Reset Password</h1>
            
            <?php if ($error): ?>
                <div style="background: rgba(255, 0, 0, 0.1); color: #ff6b6b; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($message): ?>
                <div style="background: rgba(0, 255, 0, 0.1); color: #4CAF50; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center;">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <div class="inputbox">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder=" " required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                <label for="">Email</label>
               
            </div>
            <button type="submit">Send Reset Link</button>
            <div class="register">
                <p>Remember your password ? <a href="login.php">Login</a></p>
            </div>
        </form>
    </section>
</body>
</html>