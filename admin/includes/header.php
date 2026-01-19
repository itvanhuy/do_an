<?php
// File: admin/includes/header.php

// Đảm bảo có kết nối database
if (!isset($db)) {
    require_once '../includes/database.php';
    $db = Database::getInstance();
}

// Đếm đơn hàng chờ xử lý
$stmt = $db->query("SELECT COUNT(*) as count FROM orders WHERE status = 'pending'");
$pendingOrdersCount = $stmt->fetch()['count'];

// Đếm sản phẩm sắp hết hàng (< 5)
$stmt = $db->query("SELECT COUNT(*) as count FROM products WHERE stock_quantity < 5");
$lowStockCount = $stmt->fetch()['count'];

$totalNotifications = $pendingOrdersCount + $lowStockCount;

// Lấy thông tin admin
$adminUser = Auth::getUser();
?>

<div class="admin-header">
    <div class="header-left">
        <button class="menu-toggle" id="menuToggle">
            <i class="fas fa-bars"></i>
        </button>
        <div class="page-title">
            <h1>
                <?php
                $pageTitles = [
                    'index.php' => 'Dashboard',
                    'products.php' => 'Products Management',
                    'categories.php' => 'Categories',
                    'orders.php' => 'Orders Management',
                    'users.php' => 'Users Management',
                    'settings.php' => 'Settings',
                    // Add more pages as needed
                ];
                $currentPage = basename($_SERVER['PHP_SELF']);
                echo $pageTitles[$currentPage] ?? 'Admin Panel';
                ?>
            </h1>
            <p><?php echo date('l, F j, Y'); ?></p>
        </div>
    </div>
    
    <div class="header-right">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Search...">
        </div>
        
        <div class="header-icons">
            <div class="icon-item notification">
                <i class="fas fa-bell"></i>
                <?php if ($totalNotifications > 0): ?>
                    <span class="badge"><?php echo $totalNotifications; ?></span>
                <?php endif; ?>
                <div class="dropdown-notifications">
                    <h4>Notifications</h4>
                    <?php if ($pendingOrdersCount > 0): ?>
                        <div class="notification-item new">
                            <i class="fas fa-shopping-cart"></i>
                            <div>
                                <p>You have <?php echo $pendingOrdersCount; ?> pending orders</p>
                                <a href="orders.php?status=pending"><small>View Orders</small></a>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if ($lowStockCount > 0): ?>
                        <div class="notification-item">
                            <i class="fas fa-exclamation-triangle" style="color: orange;"></i>
                            <div>
                                <p><?php echo $lowStockCount; ?> products low in stock</p>
                                <a href="products.php"><small>Check Inventory</small></a>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if ($totalNotifications == 0): ?>
                        <div class="notification-item"><p>No new notifications</p></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="user-dropdown">
                <div class="user-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="user-info">
                    <h4><?php echo htmlspecialchars($adminUser['username'] ?? 'Admin'); ?></h4>
                    <p>Administrator</p>
                </div>
                <i class="fas fa-chevron-down"></i>
                
                <div class="dropdown-menu">
                    <a href="profile.php"><i class="fas fa-user"></i> My Profile</a>
                    <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
                    <div class="divider"></div>
                    <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </div>
    </div>
</div>