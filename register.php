<?php
session_start(); // THÊM DÒNG NÀY - QUAN TRỌNG!

require_once 'includes/config.php';
require_once 'includes/auth.php';

// SỬA: $auth->requireLogout() thành Auth::requireGuest()
Auth::requireGuest();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data - THÊM NULL COALESCING OPERATOR
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $agree_terms = isset($_POST['agree_terms']);
    
    // Validation
    if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Vui lòng điền đầy đủ thông tin';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email không hợp lệ';
    } elseif (strlen($password) < 6) {
        $error = 'Mật khẩu phải có ít nhất 6 ký tự';
    } elseif ($password !== $confirm_password) {
        $error = 'Mật khẩu xác nhận không khớp';
    } elseif (!$agree_terms) {
        $error = 'Bạn phải đồng ý với điều khoản và điều kiện';
    } else {
        // SỬA: Tạo mảng dữ liệu đúng format cho Auth::register()
        $username = str_replace(['@', '.', '+', '-'], '_', $email);
        $username = preg_replace('/[^a-zA-Z0-9_]/', '', $username);
        
        // Nếu username quá ngắn, thêm số
        if (strlen($username) < 3) {
            $username .= '_' . rand(100, 999);
        }
        
        $userData = [
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'confirm_password' => $confirm_password,
            'fullname' => $full_name,
            'phone' => '',
            'address' => ''
        ];
        
        // SỬA: Gọi Auth::register() đúng cách (phương thức static)
        $result = Auth::register($userData);
        
        if ($result['success']) {
            $success = 'Đăng ký thành công! Đang chuyển hướng...';
            header('Refresh: 2; URL=pages/home.php');
            exit();
        } else {
            $error = isset($result['errors']) ? implode(', ', $result['errors']) : ($result['message'] ?? 'Đăng ký thất bại');
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - TechShop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/login.css"> <!-- SỬA: /css/login.css thành css/login.css -->
    <style>
        .alert {
            padding: 12px;
            border-radius: 5px;
            margin: 15px 0;
            text-align: center;
            font-size: 14px;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .forget {
            margin-bottom: 15px;
        }
        
        .forget label {
            cursor: pointer;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <section>
        <form action="" method="POST">
            <h1>Register</h1>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <div class="inputbox">
                <i class="fas fa-user"></i>
                <input type="text" name="full_name" placeholder=" " required 
                       value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
                <label for="">Full Name</label>
            </div>
            
            <div class="inputbox">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder=" " required
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                <label for="">Email</label>
            </div>
            
            <div class="inputbox">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder=" " required>
                <label for="">Password</label>
            </div>
            
            <div class="inputbox">
                <i class="fas fa-lock"></i>
                <input type="password" name="confirm_password" placeholder=" " required>
                <label for="">Confirm Password</label>
            </div>
            
            <div class="forget">
                <label>
                    <input type="checkbox" name="agree_terms" required>
                    I agree to the terms & conditions
                </label>
            </div>
            
            <button type="submit">Register</button>
            
            <div class="register">
                <p>Already have an account? <a href="login.php">Login</a></p>
            </div>
        </form>
    </section>

    <script>
        // Auto focus on first input
        document.querySelector('input[name="full_name"]').focus();
    </script>
</body>
</html>