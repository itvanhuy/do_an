<?php
// File: includes/auth.php
// Xác thực và quản lý người dùng - PDO version

class Auth {
    private static $db;
    
    /**
     * Khởi tạo kết nối database
     */
    private static function initDB() {
        if (!self::$db) {
            require_once 'database.php';
            self::$db = Database::getInstance()->getConnection();
        }
        return self::$db;
    }
    
    /**
     * Kiểm tra user đã đăng nhập chưa
     */
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    /**
     * Kiểm tra user có phải admin không
     */
    public static function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }
    
    /**
     * Lấy thông tin user hiện tại
     */
    public static function getUser() {
        if (!self::isLoggedIn()) {
            return null;
        }
        
        return [
            'id' => $_SESSION['user_id'] ?? null,
            'username' => $_SESSION['username'] ?? null,
            'email' => $_SESSION['email'] ?? null,
            'full_name' => $_SESSION['full_name'] ?? null,
            'avatar' => $_SESSION['avatar'] ?? null,
            'role' => $_SESSION['role'] ?? 'user'
        ];
    }
    
    /**
     * Đăng nhập - PDO version
     */
    public static function login($email, $password, $remember = false) {
        $db = self::initDB();
        
        try {
            // Tìm user theo email - SỬA: Dùng PDO
            $stmt = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                return ['success' => false, 'message' => 'Email không tồn tại'];
            }
            
            // Kiểm tra trạng thái tài khoản
            if (isset($user['is_active']) && $user['is_active'] != 1) {
                return ['success' => false, 'message' => 'Tài khoản chưa được kích hoạt'];
            }
            
            // Kiểm tra mật khẩu
            $login_success = false;
            
            // 1. Thử password_verify trước (mật khẩu đã hash)
            if (password_verify($password, $user['password'])) {
                $login_success = true;
            }
            // 2. Nếu không, thử so sánh trực tiếp (mật khẩu plain text)
            else if ($user['password'] === $password) {
                $login_success = true;
                
                // Hash lại mật khẩu và cập nhật vào DB
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashedPassword, $user['id']]);
            }
            // 3. Nếu không, thử md5 (trường hợp cũ)
            else if (md5($password) === $user['password']) {
                $login_success = true;
            }
            
            if (!$login_success) {
                return ['success' => false, 'message' => 'Mật khẩu không đúng'];
            }
            
            // Tạo session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['full_name'] = $user['full_name'] ?? '';
            $_SESSION['role'] = $user['role'] ?? 'user';
            
            // Remember me (nếu có)
            if ($remember) {
                self::setRememberMeToken($user['id']);
            }
            
            return ['success' => true, 'user' => $user];
            
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()];
        }
    }
    
    /**
     * Đăng ký user mới - PDO version
     */
    public static function register($data) {
        $db = self::initDB();
        
        // Validate dữ liệu
        $errors = self::validateRegistration($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        try {
            // Kiểm tra email đã tồn tại chưa - SỬA: Dùng PDO
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$data['email']]);
            if ($stmt->fetch()) {
                return ['success' => false, 'errors' => ['email' => 'Email đã được sử dụng']];
            }
            
            // Kiểm tra username đã tồn tại chưa
            $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$data['username']]);
            if ($stmt->fetch()) {
                // Nếu username đã tồn tại, thêm số ngẫu nhiên
                $data['username'] = $data['username'] . '_' . rand(100, 999);
            }
            
            // Hash password
            $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
            
            // Insert user - SỬA: Dùng PDO
            $sql = "INSERT INTO users (username, email, password, full_name, phone, address, created_at, is_active) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW(), 1)";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                $data['username'],
                $data['email'],
                $hashed_password,
                $data['full_name'] ?? '',
                $data['phone'] ?? '',
                $data['address'] ?? ''
            ]);
            
            $userId = $db->lastInsertId();
            
            // Tự động đăng nhập sau khi đăng ký
            $_SESSION['user_id'] = $userId;
            $_SESSION['username'] = $data['username'];
            $_SESSION['email'] = $data['email'];
            $_SESSION['full_name'] = $data['full_name'] ?? '';
            $_SESSION['role'] = 'user';
            
            return ['success' => true, 'user_id' => $userId];
            
        } catch (Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()];
        }
    }
    
    /**
     * Validate dữ liệu đăng ký
     */
    private static function validateRegistration($data) {
        $errors = [];
        
        // Username validation
        if (empty($data['username'])) {
            $errors['username'] = 'Tên đăng nhập là bắt buộc';
        } elseif (strlen($data['username']) < 3 || strlen($data['username']) > 50) {
            $errors['username'] = 'Tên đăng nhập phải từ 3 đến 50 ký tự';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $data['username'])) {
            $errors['username'] = 'Tên đăng nhập chỉ được chứa chữ cái, số và dấu gạch dưới';
        }
        
        // Email validation
        if (empty($data['email'])) {
            $errors['email'] = 'Email là bắt buộc';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không hợp lệ';
        }
        
        // Password validation
        if (empty($data['password'])) {
            $errors['password'] = 'Mật khẩu là bắt buộc';
        } elseif (strlen($data['password']) < 6) {
            $errors['password'] = 'Mật khẩu phải có ít nhất 6 ký tự';
        }
        
        // Confirm password
        if ($data['password'] !== $data['confirm_password']) {
            $errors['confirm_password'] = 'Mật khẩu xác nhận không khớp';
        }
        
        return $errors;
    }
    
    /**
     * Đăng xuất
     */
    public static function logout() {
        // Xóa remember me token nếu có
        if (isset($_COOKIE['remember_token'])) {
            self::deleteRememberMeToken($_COOKIE['remember_token']);
            setcookie('remember_token', '', time() - 3600, '/');
        }
        
        // Xóa session
        session_unset();
        session_destroy();
        
        return true;
    }
    
    /**
     * Tự động đăng nhập bằng remember token - PDO version
     */
    public static function autoLogin() {
        if (isset($_COOKIE['remember_token']) && !self::isLoggedIn()) {
            $db = self::initDB();
            
            try {
                // Kiểm tra xem bảng remember_tokens có tồn tại không
                $stmt = $db->query("SHOW TABLES LIKE 'remember_tokens'");
                $tableExists = $stmt->fetch();
                
                if (!$tableExists) {
                    return false;
                }
                
                // Tìm user bằng token
                $stmt = $db->prepare("
                    SELECT u.* FROM users u 
                    JOIN remember_tokens rt ON u.id = rt.user_id 
                    WHERE rt.token = ? AND rt.expires_at > NOW()
                ");
                $stmt->execute([$_COOKIE['remember_token']]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($user) {
                    // Tạo session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['full_name'] = $user['full_name'] ?? '';
                    $_SESSION['role'] = $user['role'] ?? 'user';
                    
                    // Gia hạn token
                    $expires = time() + (30 * 24 * 60 * 60);
                    $stmt = $db->prepare("UPDATE remember_tokens SET expires_at = ? WHERE token = ?");
                    $stmt->execute([date('Y-m-d H:i:s', $expires), $_COOKIE['remember_token']]);
                    
                    // Cập nhật cookie
                    setcookie('remember_token', $_COOKIE['remember_token'], $expires, '/');
                    
                    return true;
                }
            } catch (Exception $e) {
                error_log("Auto login error: " . $e->getMessage());
                return false;
            }
        }
        return false;
    }
    
    /**
     * Remember me functionality - PDO version
     */
    private static function setRememberMeToken($userId) {
        $db = self::initDB();
        
        try {
            // Check if remember_tokens table exists
            $stmt = $db->query("SHOW TABLES LIKE 'remember_tokens'");
            if ($stmt->rowCount() == 0) {
                // Create table if it does not exist
                $db->exec("CREATE TABLE remember_tokens (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    token VARCHAR(255) NOT NULL,
                    expires_at DATETIME NOT NULL,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                )");
            }

            $token = bin2hex(random_bytes(32));
            $expires = time() + (30 * 24 * 60 * 60); // 30 ngày
            
            // Lưu token vào database
            $stmt = $db->prepare("INSERT INTO remember_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$userId, $token, date('Y-m-d H:i:s', $expires)]);
            
            // Set cookie
            setcookie('remember_token', $token, $expires, '/');
        } catch (Exception $e) {
            error_log("Set remember me token error: " . $e->getMessage());
        }
    }
    
    /**
     * Xóa remember me token - PDO version
     */
    private static function deleteRememberMeToken($token) {
        $db = self::initDB();
        try {
            $stmt = $db->prepare("DELETE FROM remember_tokens WHERE token = ?");
            $stmt->execute([$token]);
        } catch (Exception $e) {
            error_log("Delete remember token error: " . $e->getMessage());
        }
    }
    
    /**
     * Middleware: Yêu cầu đăng nhập
     */
    public static function requireLogin($redirect = 'login.php') {
        if (!self::isLoggedIn()) {
            $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
            header("Location: " . $redirect);
            exit();
        }
    }
    
    /**
     * Middleware: Yêu cầu quyền admin
     */
    public static function requireAdmin($redirect = 'pages/home.php') {
        self::requireLogin();
        
        if (!self::isAdmin()) {
            header("Location: " . $redirect);
            exit();
        }
    }
    
    /**
     * Middleware: Chỉ cho phép guest (chưa đăng nhập)
     */
    public static function requireGuest($redirect = 'pages/home.php') {
        if (self::isLoggedIn()) {
            header("Location: " . $redirect);
            exit();
        }
    }
}
?>