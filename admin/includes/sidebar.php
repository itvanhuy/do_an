<?php
// File: admin/includes/sidebar.php
$current_page = basename($_SERVER['PHP_SELF']);

// Đảm bảo có kết nối database để lấy số liệu thống kê cho sidebar
if (!isset($db)) {
    require_once '../includes/database.php';
    $db = Database::getInstance();
}

// Lấy số lượng cho sidebar (dùng biến riêng để tránh xung đột với các trang khác)
$sb_products_count = $db->query("SELECT COUNT(*) as total FROM products")->fetch()['total'];
$sb_categories_count = $db->query("SELECT COUNT(*) as total FROM categories")->fetch()['total'];
// Đếm đơn hàng pending (chờ xử lý) để admin chú ý
$sb_orders_count = $db->query("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'")->fetch()['total'];
?>

<div class="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <img src="../img/logo.png" alt="Logo">
            <h2><?php echo SITE_NAME; ?></h2>
        </div>
        <div class="admin-info">
            <div class="admin-avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="admin-details">
                <h4><?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></h4>
                <p>Administrator</p>
            </div>
        </div>
    </div>
    
    <nav class="sidebar-menu">
        <ul>
            <li class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
                <a href="index.php">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <li class="menu-header">MANAGEMENT</li>
            
            <li class="<?php echo $current_page == 'products.php' ? 'active' : ''; ?>">
                <a href="products.php">
                    <i class="fas fa-box"></i>
                    <span>Products</span>
                    <span class="badge"><?php echo $sb_products_count; ?></span>
                </a>
            </li>
            
            <li class="<?php echo $current_page == 'categories.php' ? 'active' : ''; ?>">
                <a href="categories.php">
                    <i class="fas fa-list"></i>
                    <span>Categories</span>
                    <span class="badge"><?php echo $sb_categories_count; ?></span>
                </a>
            </li>
            
            <li class="<?php echo $current_page == 'brands.php' ? 'active' : ''; ?>">
                <a href="brands.php">
                    <i class="fas fa-copyright"></i>
                    <span>Brands</span>
                </a>
            </li>
            
            <li class="<?php echo $current_page == 'orders.php' ? 'active' : ''; ?>">
                <a href="orders.php">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Orders</span>
                    <span class="badge badge-danger"><?php echo $sb_orders_count; ?></span>
                </a>
            </li>
            
            <li class="<?php echo $current_page == 'coupons.php' ? 'active' : ''; ?>">
                <a href="coupons.php">
                    <i class="fas fa-tags"></i>
                    <span>Coupons</span>
                </a>
            </li>
            
            <li class="<?php echo $current_page == 'users.php' ? 'active' : ''; ?>">
                <a href="users.php">
                    <i class="fas fa-users"></i>
                    <span>Users</span>
                </a>
            </li>
            
            <li class="menu-header">TOURNAMENT</li>
            
<<<<<<< HEAD
            <li class="<?php echo $current_page == 'tournaments.php' ? 'active' : ''; ?>">
                <a href="tournaments.php">
                    <i class="fas fa-gamepad"></i>
                    <span>Tournaments</span>
                </a>
            </li>
            
            <li class="<?php echo $current_page == 'teams.php' ? 'active' : ''; ?>">
                <a href="teams.php">
                    <i class="fas fa-users-cog"></i>
                    <span>Teams</span>
=======
            <li class="<?php echo $current_page == 'matches.php' ? 'active' : ''; ?>">
                <a href="matches.php">
                    <i class="fas fa-gamepad"></i>
                    <span>Matches</span>
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
                </a>
            </li>
            
            <li class="<?php echo $current_page == 'rankings.php' ? 'active' : ''; ?>">
                <a href="rankings.php">
                    <i class="fas fa-trophy"></i>
                    <span>Rankings</span>
                </a>
            </li>
            
            <li class="menu-header">CONTENT</li>
            
            <li class="<?php echo $current_page == 'blog.php' ? 'active' : ''; ?>">
                <a href="blog.php">
                    <i class="fas fa-blog"></i>
                    <span>Blog</span>
                </a>
            </li>
            
            <li class="<?php echo $current_page == 'comments.php' ? 'active' : ''; ?>">
                <a href="comments.php">
                    <i class="fas fa-comments"></i>
                    <span>Comments</span>
                </a>
            </li>
            
            <li class="<?php echo $current_page == 'reviews.php' ? 'active' : ''; ?>">
                <a href="reviews.php">
                    <i class="fas fa-star"></i>
                    <span>Reviews</span>
                </a>
            </li>
            
            <li class="menu-header">SYSTEM</li>
            
            <li class="<?php echo $current_page == 'settings.php' ? 'active' : ''; ?>">
                <a href="settings.php">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </li>
            
            <li>
                <a href="analytics.php">
                    <i class="fas fa-chart-bar"></i>
                    <span>Analytics</span>
                </a>
            </li>
        </ul>
    </nav>
    
    <div class="sidebar-footer">
        <a href="../pages/home.php" class="btn-view-site">
            <i class="fas fa-external-link-alt"></i>
            <span>View Site</span>
        </a>
        <a href="../logout.php" class="btn-logout">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>
</div>