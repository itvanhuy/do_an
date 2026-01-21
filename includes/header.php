<?php
// File: includes/header.php
// Không cần session_start() ở đây vì đã gọi trong file config.php

// Kiểm tra xem auth.php đã được include chưa
if (!function_exists('Auth::isLoggedIn')) {
    require_once 'auth.php';
}

// Đảm bảo Database class được load để đếm giỏ hàng
if (!class_exists('Database')) {
    require_once 'database.php';
}

$isLoggedIn = Auth::isLoggedIn();
$username = $isLoggedIn ? ($_SESSION['username'] ?? '') : '';
$userRole = $isLoggedIn ? ($_SESSION['role'] ?? 'user') : 'user';

// Tính toán số lượng giỏ hàng
$cart_count = 0;
if ($isLoggedIn) {
    try {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $cart_count = $result['total'] ?? 0;
    } catch (Exception $e) {
        // Xử lý lỗi nếu cần
    }
} else {
    // Nếu chưa đăng nhập, đếm từ session cart (nếu có)
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $cart_count += isset($item['quantity']) ? $item['quantity'] : 0;
        }
    }
}
// Cập nhật session
$_SESSION['cart_count'] = $cart_count;
?>

<header class="header">
    <a href="<?php echo $isLoggedIn ? 'pages/home.php' : '../index.php'; ?>">
        <div class="logo">
            <img src="<?php echo $isLoggedIn ? '../img/logo.png' : '../img/logo.png'; ?>" alt="Techshop Logo" style="height: 80px; width: auto; object-fit: contain;" />
        </div>
    </a>
    
    <nav class="navbar">
        <?php
        // Xác định trang hiện tại để thêm class 'active'
        $current_page = basename($_SERVER['PHP_SELF']);
        ?>
        <a href="<?php echo $isLoggedIn ? 'home.php' : '../index.php'; ?>" 
           class="<?php echo ($current_page == 'home.php' || $current_page == 'index.php') ? 'active' : ''; ?>">
           HOME
        </a>
        <a href="shop.php" 
           class="<?php echo $current_page == 'shop.php' ? 'active' : ''; ?>">
           SHOP
        </a>
        <a href="tournament.php" 
           class="<?php echo $current_page == 'tournament.php' ? 'active' : ''; ?>">
           TOURNAMENT
        </a>
        <a href="about.php" 
           class="<?php echo $current_page == 'about.php' ? 'active' : ''; ?>">
           ABOUT
        </a>
        <a href="news.php" 
           class="<?php echo ($current_page == 'news.php' || $current_page == 'news-detail.php') ? 'active' : ''; ?>">
           NEWS
        </a>
        <a href="https://www.facebook.com/huylv.2k3">CONTACT</a>
    </nav>
    
    <div class="header-right">
        <div class="search-bar">
            <form action="search.php" method="GET" class="search-form">
                <input type="text" name="q" placeholder="Search products..." 
                       value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>

        <div class="user-menu">
            <?php if ($isLoggedIn): ?>
                <!-- Hiển thị khi đã đăng nhập -->
                <div class="user-dropdown">
                    <button class="user-btn">
                        <i class="fas fa-user-circle"></i> 
                        <span class="username"><?php echo htmlspecialchars($username); ?></span>
                        <i class="fas fa-caret-down"></i>
                    </button>
                    <div class="dropdown-content">
                        <a href="profile.php"><i class="fas fa-user"></i> My Profile</a>
                        <a href="orders.php"><i class="fas fa-shopping-bag"></i> My Orders</a>
                        <a href="wishlist.php"><i class="fas fa-heart"></i> My Wishlist</a>
                        <a href="cart.php"><i class="fas fa-shopping-cart"></i> My Cart</a>
                        <?php if ($userRole === 'admin'): ?>
                            <div class="dropdown-divider"></div>
                            <a href="../admin/index.php"><i class="fas fa-cog"></i> Admin Panel</a>
                        <?php endif; ?>
                        <div class="dropdown-divider"></div>
                        <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
                
                <!-- Icon giỏ hàng -->
                <a href="cart.php" class="cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <?php
                    if ($cart_count > 0) {
                        echo '<span class="cart-count">' . $cart_count . '</span>';
                    }
                    ?>
                </a>
            <?php else: ?>
                <!-- Hiển thị khi chưa đăng nhập -->
                <div class="login-btn">
                    <a href="../login.php" class="btn-login">Login</a>
                </div>
                <div class="register-btn">
                    <a href="../register.php" class="btn-register">Register</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Mobile menu toggle -->
    <button class="menu-toggle" aria-label="Toggle menu">
        <i class="fas fa-bars"></i>
    </button>
</header>

<!-- JavaScript cho header -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const menuToggle = document.querySelector('.menu-toggle');
<<<<<<< HEAD
    const mobileMenu = document.querySelector('.navbar');
=======
    const mobileMenu = document.querySelector('.mobile-menu');
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
    
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('active');
            this.classList.toggle('active');
        });
    }
    
    // User dropdown
    const userBtn = document.querySelector('.user-btn');
    if (userBtn) {
        userBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.parentElement.classList.toggle('active');
        });
    }
    
    // Đóng dropdown khi click ra ngoài
    document.addEventListener('click', function(e) {
        const dropdown = document.querySelector('.user-dropdown');
        if (dropdown && !dropdown.contains(e.target)) {
            dropdown.classList.remove('active');
        }
        
        // Đóng mobile menu khi click ra ngoài
        if (mobileMenu.classList.contains('active') && 
            !mobileMenu.contains(e.target) && 
            !menuToggle.contains(e.target)) {
            mobileMenu.classList.remove('active');
            menuToggle.classList.remove('active');
        }
    });
    
    // Active link cho mobile menu
    const currentPage = '<?php echo $current_page; ?>';
    const mobileLinks = document.querySelectorAll('.mobile-nav a');
    mobileLinks.forEach(link => {
        if (link.getAttribute('href').includes(currentPage) || 
            (currentPage === 'index.php' && link.getAttribute('href').includes('home.php'))) {
            link.classList.add('active');
        }
    });
    
    // Tìm kiếm khi nhấn Enter
    const searchInput = document.querySelector('.search-bar input');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                this.closest('form').submit();
            }
        });
    }
});
</script>