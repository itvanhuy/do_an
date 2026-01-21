<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/database.php';

$error = '';
$token = $_GET['token'] ?? '';
$email = $_GET['email'] ?? '';

if (empty($token) || empty($email)) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $db = Database::getInstance();
        
        // Verify token
        $stmt = $db->prepare("SELECT * FROM password_resets WHERE email = ? AND token = ? AND expires_at > NOW() ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$email, $token]);
        $resetRequest = $stmt->fetch();
        
        if ($resetRequest) {
            // Update password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE users SET password = ? WHERE email = ?");
            $stmt->execute([$hashedPassword, $email]);
            
            // Delete token
            $stmt = $db->prepare("DELETE FROM password_resets WHERE email = ?");
            $stmt->execute([$email]);
            
            header('Location: login.php?reset=success');
            exit();
        } else {
            $error = "Invalid or expired reset link.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Password - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <section>
        <form action="" method="POST">
            <h1>New Password</h1>
            
            <?php if ($error): ?>
                <div style="background: rgba(255, 0, 0, 0.1); color: #ff6b6b; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <div class="inputbox">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder=" " required>
                <label for="">New Password</label>
            </div>

            <div class="inputbox">
                <i class="fas fa-lock"></i>
                <input type="password" name="confirm_password" placeholder=" " required>
                <label for="">Confirm Password</label>
            </div>

            <button type="submit">Reset Password</button>
            <div class="register">
                <p><a href="login.php">Back to Login</a></p>
            </div>
        </form>
    </section>
</body>
</html>