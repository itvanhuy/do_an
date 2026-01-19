<?php
// File: admin/orders.php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/database.php';

Auth::requireAdmin();

$db = Database::getInstance();
$action = $_GET['action'] ?? 'list';
$message = '';

// Xử lý các action
if ($action == 'update_status' && isset($_POST['order_id'])) {
    try {
        $stmt = $db->getConnection()->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$_POST['status'], $_POST['order_id']]);
        $message = '<div class="alert success">Order status updated!</div>';
    } catch (PDOException $e) {
        $message = '<div class="alert error">Error: ' . $e->getMessage() . '</div>';
    }
}

// Lấy danh sách đơn hàng
$status = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';
$page = $_GET['page'] ?? 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$query = "SELECT o.*, u.username, u.email, 
          COUNT(oi.id) as items_count,
          SUM(oi.quantity) as total_quantity
          FROM orders o
          LEFT JOIN users u ON o.user_id = u.id
          LEFT JOIN order_items oi ON o.id = oi.order_id
          WHERE 1=1";
$params = [];

if ($status) {
    $query .= " AND o.status = ?";
    $params[] = $status;
}

if ($search) {
    $query .= " AND (o.id LIKE ? OR u.username LIKE ? OR u.email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query .= " GROUP BY o.id ORDER BY o.created_at DESC LIMIT $limit OFFSET $offset";

$stmt = $db->getConnection()->prepare($query);
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Thống kê orders
$statsQuery = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing,
    SUM(CASE WHEN status = 'shipped' THEN 1 ELSE 0 END) as shipped,
    SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered,
    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
    FROM orders";

$stmt = $db->getConnection()->query($statsQuery);
$stats = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Management - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <?php include 'includes/header.php'; ?>
            
            <div class="content-header">
                <h2>Orders Management</h2>
                <div class="order-stats">
                    <div class="stat-item">
                        <span class="stat-label">Total Orders</span>
                        <span class="stat-value"><?php echo $stats['total']; ?></span>
                    </div>
                    <div class="stat-item pending">
                        <span class="stat-label">Pending</span>
                        <span class="stat-value"><?php echo $stats['pending']; ?></span>
                    </div>
                    <div class="stat-item processing">
                        <span class="stat-label">Processing</span>
                        <span class="stat-value"><?php echo $stats['processing']; ?></span>
                    </div>
                    <div class="stat-item delivered">
                        <span class="stat-label">Delivered</span>
                        <span class="stat-value"><?php echo $stats['delivered']; ?></span>
                    </div>
                </div>
            </div>
            
            <?php echo $message; ?>
            
            <!-- Order Stats Tabs -->
            <div class="order-tabs">
                <a href="orders.php" class="<?php echo !$status ? 'active' : ''; ?>">
                    All Orders (<?php echo $stats['total']; ?>)
                </a>
                <a href="?status=pending" class="<?php echo $status == 'pending' ? 'active' : ''; ?>">
                    Pending (<?php echo $stats['pending']; ?>)
                </a>
                <a href="?status=processing" class="<?php echo $status == 'processing' ? 'active' : ''; ?>">
                    Processing (<?php echo $stats['processing']; ?>)
                </a>
                <a href="?status=shipped" class="<?php echo $status == 'shipped' ? 'active' : ''; ?>">
                    Shipped (<?php echo $stats['shipped']; ?>)
                </a>
                <a href="?status=delivered" class="<?php echo $status == 'delivered' ? 'active' : ''; ?>">
                    Delivered (<?php echo $stats['delivered']; ?>)
                </a>
            </div>
            
            <!-- Search -->
            <div class="search-filter">
                <form method="GET" class="search-form">
                    <?php if ($status): ?>
                    <input type="hidden" name="status" value="<?php echo $status; ?>">
                    <?php endif; ?>
                    <div class="search-input">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" placeholder="Search by order ID, username or email..."
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <button type="submit" class="btn btn-secondary">Search</button>
                </form>
            </div>
            
            <!-- Orders Table -->
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($order['username']); ?></strong>
                                <p><?php echo htmlspecialchars($order['email']); ?></p>
                            </td>
                            <td>
                                <?php echo $order['items_count']; ?> items
                                <p class="small"><?php echo $order['total_quantity']; ?> units</p>
                            </td>
                            <td class="price">$<?php echo number_format($order['total'], 2); ?></td>
                            <td>
                                <form method="POST" action="?action=update_status" class="status-form">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <select name="status" class="status-select status-<?php echo $order['status']; ?>"
                                            onchange="this.form.submit()">
                                        <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                        <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                        <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                        <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                </form>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                            <td class="actions">
                                <a href="order-details.php?id=<?php echo $order['id']; ?>" 
                                   class="btn-icon view" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="#" class="btn-icon print" title="Print Invoice">
                                    <i class="fas fa-print"></i>
                                </a>
                                <a href="#" class="btn-icon email" title="Email Customer">
                                    <i class="fas fa-envelope"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>