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

<<<<<<< HEAD
// Đếm đánh giá chờ duyệt (nếu có bảng reviews)
$pendingReviewsCount = 0;
try {
    $stmt = $db->query("SELECT COUNT(*) as count FROM reviews WHERE status = 'pending'");
    $pendingReviewsCount = $stmt->fetch()['count'];
} catch (Exception $e) {}

// Đếm người dùng mới đăng ký hôm nay
$newUsersCount = 0;
try {
    $stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE DATE(created_at) = CURDATE()");
    $newUsersCount = $stmt->fetch()['count'];
} catch (Exception $e) {}

// Đếm bình luận chờ duyệt (nếu có bảng comments)
$pendingCommentsCount = 0;
try {
    $stmt = $db->query("SELECT COUNT(*) as count FROM comments WHERE status = 'pending'");
    $pendingCommentsCount = $stmt->fetch()['count'];
} catch (Exception $e) {}

$totalNotifications = $pendingOrdersCount + $lowStockCount + $pendingReviewsCount + $newUsersCount + $pendingCommentsCount;
=======
$totalNotifications = $pendingOrdersCount + $lowStockCount;
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695

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
<<<<<<< HEAD
                <span class="badge" id="notification-badge" style="<?php echo $totalNotifications > 0 ? '' : 'display:none;'; ?>"><?php echo $totalNotifications; ?></span>
                <div class="dropdown-notifications" id="notification-dropdown">
                    <h4>Notifications</h4>
                    <div id="notification-list">
=======
                <?php if ($totalNotifications > 0): ?>
                    <span class="badge"><?php echo $totalNotifications; ?></span>
                <?php endif; ?>
                <div class="dropdown-notifications">
                    <h4>Notifications</h4>
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
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
<<<<<<< HEAD
                    <?php if ($pendingReviewsCount > 0): ?>
                        <div class="notification-item">
                            <i class="fas fa-star" style="color: #f1c40f;"></i>
                            <div>
                                <p><?php echo $pendingReviewsCount; ?> reviews pending approval</p>
                                <a href="reviews.php"><small>Review Now</small></a>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if ($newUsersCount > 0): ?>
                        <div class="notification-item">
                            <i class="fas fa-user-plus" style="color: #3498db;"></i>
                            <div>
                                <p><?php echo $newUsersCount; ?> new users today</p>
                                <a href="users.php"><small>View Users</small></a>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if ($pendingCommentsCount > 0): ?>
                        <div class="notification-item">
                            <i class="fas fa-comments" style="color: #9b59b6;"></i>
                            <div>
                                <p><?php echo $pendingCommentsCount; ?> comments pending</p>
                                <a href="comments.php"><small>Moderate Comments</small></a>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if ($totalNotifications == 0): ?>
                        <div class="notification-item"><p>No new notifications</p></div>
                    <?php endif; ?>
                    </div>
=======
                    <?php if ($totalNotifications == 0): ?>
                        <div class="notification-item"><p>No new notifications</p></div>
                    <?php endif; ?>
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
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
<<<<<<< HEAD
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function updateNotifications() {
        fetch('get_notifications.php')
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById('notification-badge');
                const list = document.getElementById('notification-list');
                
                // Cập nhật số lượng trên badge
                if (data.total > 0) {
                    badge.style.display = 'flex';
                    badge.innerText = data.total;
                } else {
                    badge.style.display = 'none';
                }
                
                // Xây dựng lại danh sách thông báo
                let html = '';
                
                if (data.orders > 0) {
                    html += `
                        <div class="notification-item new">
                            <i class="fas fa-shopping-cart"></i>
                            <div>
                                <p>You have ${data.orders} pending orders</p>
                                <a href="orders.php?status=pending"><small>View Orders</small></a>
                            </div>
                        </div>`;
                }
                
                if (data.stock > 0) {
                    html += `
                        <div class="notification-item">
                            <i class="fas fa-exclamation-triangle" style="color: orange;"></i>
                            <div>
                                <p>${data.stock} products low in stock</p>
                                <a href="products.php"><small>Check Inventory</small></a>
                            </div>
                        </div>`;
                }
                
                if (data.reviews > 0) {
                    html += `
                        <div class="notification-item">
                            <i class="fas fa-star" style="color: #f1c40f;"></i>
                            <div>
                                <p>${data.reviews} reviews pending approval</p>
                                <a href="reviews.php"><small>Review Now</small></a>
                            </div>
                        </div>`;
                }
                
                if (data.users > 0) {
                    html += `
                        <div class="notification-item">
                            <i class="fas fa-user-plus" style="color: #3498db;"></i>
                            <div>
                                <p>${data.users} new users today</p>
                                <a href="users.php"><small>View Users</small></a>
                            </div>
                        </div>`;
                }
                
                if (data.comments > 0) {
                    html += `
                        <div class="notification-item">
                            <i class="fas fa-comments" style="color: #9b59b6;"></i>
                            <div>
                                <p>${data.comments} comments pending</p>
                                <a href="comments.php"><small>Moderate Comments</small></a>
                            </div>
                        </div>`;
                }
                
                if (data.total == 0) {
                    html = '<div class="notification-item"><p>No new notifications</p></div>';
                }
                
                list.innerHTML = html;
            })
            .catch(err => console.error('Error fetching notifications:', err));
    }

    // Tự động cập nhật mỗi 10 giây
    setInterval(updateNotifications, 10000);
});
</script>
=======
</div>
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
