<?php
// File: login.php
session_start();
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Nếu đã đăng nhập, redirect về trang chủ
if (Auth::isLoggedIn()) {
    if (Auth::isAdmin()) {
        header('Location: admin/index.php');
    } else {
        header('Location: pages/home.php');
    }
    exit();
}

// Biến lưu thông báo lỗi
$error = '';
$email = '';

// Xử lý đăng nhập
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    if (empty($email) || empty($password)) {
        $error = 'Vui lòng nhập email và mật khẩu';
    } else {
        $result = Auth::login($email, $password, $remember);

        if ($result['success']) {
            $_SESSION['login_success'] = true; // Lưu flag vào session
            // Redirect ngay lập tức
            header('Location: pages/home.php');
            exit();
        } else {
            $error = $result['message'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/login.css">
    <style>
        /* Thêm CSS cho thông báo lỗi */
        .error-message {
            background: rgba(255, 0, 0, 0.1);
            color: #ff6b6b;
            padding: 10px 15px;
            border-radius: 5px;
            margin: 15px 0;
            text-align: center;
            border: 1px solid rgba(255, 0, 0, 0.2);
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .success-message {
            background: rgba(0, 255, 0, 0.1);
            color: #4CAF50;
            padding: 10px 15px;
            border-radius: 5px;
            margin: 15px 0;
            text-align: center;
            border: 1px solid rgba(0, 255, 0, 0.2);
        }
    </style>
</head>

<body>
    <section>
        <form action="" method="POST">
            <h1>Login</h1>

            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['registered']) && $_GET['registered'] == 'success'): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i> Đăng ký thành công! Vui lòng đăng nhập.
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['reset']) && $_GET['reset'] == 'success'): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i> Mật khẩu đã được đặt lại thành công! Vui lòng đăng nhập.
                </div>
            <?php endif; ?>

            <div class="inputbox">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder=" "
                    value="<?php echo htmlspecialchars($email); ?>"
                    required>
                <label for="">Email</label>
            </div>

            <div class="inputbox">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder=" " required>
                <label for="">Password</label>
            </div>

            <div class="forget">
                <label for="remember">
                    <input type="checkbox" id="remember" name="remember"> Remember Me
                </label>
                <a href="resetpassword.php">Forgot Password</a>
            </div>

            <button type="submit">Log in</button>

            <div class="register">
                <p>Don't have an account? <a href="register.php">Register</a></p>
            </div>
        </form>
    </section>
</body>

</html>