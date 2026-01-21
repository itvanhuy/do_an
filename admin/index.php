<?php
// File: admin/index.php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/database.php';

// Chỉ admin mới được truy cập
Auth::requireAdmin();

// Lấy thống kê
try {
    $db = Database::getInstance();
    
    // Tổng số users
    $stmt = $db->getConnection()->query("SELECT COUNT(*) as total FROM users");
    $totalUsers = $stmt->fetch()['total'];
    
    // Tổng số products
    $stmt = $db->getConnection()->query("SELECT COUNT(*) as total FROM products");
    $totalProducts = $stmt->fetch()['total'];
    
    // Tổng số orders
    $stmt = $db->getConnection()->query("SELECT COUNT(*) as total FROM orders");
    $totalOrders = $stmt->fetch()['total'];
    
    // Doanh thu tháng này
    $currentMonth = date('Y-m');
    $stmt = $db->getConnection()->prepare("
        SELECT SUM(total) as revenue 
        FROM orders 
        WHERE DATE_FORMAT(created_at, '%Y-%m') = ? 
        AND status IN ('delivered', 'shipped')
    ");
    $stmt->execute([$currentMonth]);
    $monthlyRevenue = $stmt->fetch()['revenue'] ?? 0;
    
    // Orders mới (7 ngày gần đây)
    $stmt = $db->getConnection()->query("
        SELECT COUNT(*) as new_orders 
        FROM orders 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    ");
    $newOrders = $stmt->fetch()['new_orders'];
    
    // Top 5 sản phẩm bán chạy
    $stmt = $db->getConnection()->query("
        SELECT p.name, SUM(oi.quantity) as sold, p.price
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        GROUP BY p.id, p.name, p.price
        ORDER BY sold DESC
        LIMIT 5
    ");
    $topProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Orders gần đây
    $stmt = $db->getConnection()->query("
        SELECT o.id, u.username, o.total, o.status, o.created_at
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        ORDER BY o.created_at DESC
        LIMIT 10
    ");
    $recentOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $error = "Lỗi kết nối database: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <?php include 'includes/header.php'; ?>
            
            <!-- Stats Cards -->
            <div class="stats-cards">
                <div class="card">
                    <div class="card-icon users">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="card-info">
                        <h3><?php echo number_format($totalUsers); ?></h3>
                        <p>Total Users</p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-icon products">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="card-info">
                        <h3><?php echo number_format($totalProducts); ?></h3>
                        <p>Total Products</p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-icon orders">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="card-info">
                        <h3><?php echo number_format($totalOrders); ?></h3>
                        <p>Total Orders</p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-icon revenue">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="card-info">
<<<<<<< HEAD
                        <h3><?php echo number_format($monthlyRevenue, 0, ',', '.'); ?> VNĐ</h3>
=======
                        <h3>$<?php echo number_format($monthlyRevenue, 2); ?></h3>
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
                        <p>Monthly Revenue</p>
                    </div>
                </div>
            </div>
            
            <!-- Charts & Recent Orders -->
            <div class="dashboard-grid">
                <div class="chart-container">
                    <h3>Sales Overview</h3>
                    <canvas id="salesChart"></canvas>
                </div>
                
                <div class="recent-orders">
                    <h3>Recent Orders</h3>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentOrders as $order): ?>
                                <tr>
                                    <td>#<?php echo $order['id']; ?></td>
                                    <td><?php echo htmlspecialchars($order['username']); ?></td>
<<<<<<< HEAD
                                    <td><?php echo number_format($order['total'], 0, ',', '.'); ?> VNĐ</td>
=======
                                    <td>$<?php echo number_format($order['total'], 2); ?></td>
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
                                    <td>
                                        <span class="status-badge status-<?php echo $order['status']; ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Top Products -->
            <div class="top-products">
                <h3>Top Selling Products</h3>
                <div class="products-grid">
                    <?php foreach ($topProducts as $product): ?>
                    <div class="product-item">
                        <div class="product-name">
                            <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                            <p><?php echo number_format($product['sold']); ?> sold</p>
                        </div>
                        <div class="product-revenue">
<<<<<<< HEAD
                            <h4><?php echo number_format($product['price'] * $product['sold'], 0, ',', '.'); ?> VNĐ</h4>
=======
                            <h4>$<?php echo number_format($product['price'] * $product['sold'], 2); ?></h4>
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Sales Chart
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
            datasets: [{
<<<<<<< HEAD
                label: 'Sales (VNĐ)',
=======
                label: 'Sales ($)',
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
                data: [12000, 19000, 15000, 25000, 22000, 30000, 28000],
                borderColor: '#4CAF50',
                backgroundColor: 'rgba(76, 175, 80, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
<<<<<<< HEAD
                            return value.toLocaleString() + ' VNĐ';
=======
                            return '$' + value.toLocaleString();
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
                        }
                    }
                }
            }
        }
    });
    
    // Update chart with real data (you can fetch from API)
    function updateChart() {
        // Fetch data from API endpoint
        fetch('api/stats.php?type=sales')
            .then(response => response.json())
            .then(data => {
                salesChart.data.labels = data.labels;
                salesChart.data.datasets[0].data = data.values;
                salesChart.update();
            });
    }
    
    // Auto-refresh every 5 minutes
    setInterval(updateChart, 300000);
    </script>
</body>
</html>