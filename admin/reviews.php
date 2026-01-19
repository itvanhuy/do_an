<?php
// File: admin/reviews.php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/database.php';

Auth::requireAdmin();

$db = Database::getInstance();
$action = $_GET['action'] ?? 'list';
$message = '';

// Xử lý Approve / Delete
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    
    if ($action === 'approve') {
        try {
            $stmt = $db->prepare("UPDATE reviews SET status = 'approved' WHERE id = ?");
            $stmt->execute([$id]);
            $message = '<div class="alert success">Review approved successfully!</div>';
        } catch (PDOException $e) {
            $message = '<div class="alert error">Error: ' . $e->getMessage() . '</div>';
        }
    } elseif ($action === 'delete') {
        try {
            $stmt = $db->prepare("DELETE FROM reviews WHERE id = ?");
            $stmt->execute([$id]);
            $message = '<div class="alert success">Review deleted successfully!</div>';
        } catch (PDOException $e) {
            $message = '<div class="alert error">Error: ' . $e->getMessage() . '</div>';
        }
    }
}

// Phân trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$reviews = [];
$totalReviews = 0;
$totalPages = 0;

try {
    // Lấy danh sách reviews
    $query = "
        SELECT r.*, u.username, u.full_name, p.name as product_name, p.image as product_image
        FROM reviews r
        JOIN users u ON r.user_id = u.id
        JOIN products p ON r.product_id = p.id
        ORDER BY r.created_at DESC
        LIMIT $limit OFFSET $offset
    ";
    $stmt = $db->query($query);
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Đếm tổng số reviews
    $countStmt = $db->query("SELECT COUNT(*) as total FROM reviews");
    $totalReviews = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalReviews / $limit);
} catch (Exception $e) {
    // Bỏ qua lỗi nếu bảng chưa tồn tại
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviews Management - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        <div class="main-content">
            <?php include 'includes/header.php'; ?>
            
            <div class="content-header">
                <h2>Reviews Management</h2>
            </div>
            
            <?php echo $message; ?>
            
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>User</th>
                            <th>Rating</th>
                            <th>Comment</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reviews as $r): ?>
                        <tr>
                            <td>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <img src="../img/product/<?php echo htmlspecialchars($r['product_image']); ?>" style="width:40px; height:40px; object-fit:cover; border-radius:4px;">
                                    <span style="font-size:0.9em;"><?php echo htmlspecialchars(mb_strimwidth($r['product_name'], 0, 30, '...')); ?></span>
                                </div>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($r['full_name'] ?: $r['username']); ?></strong>
                            </td>
                            <td>
                                <span style="color: #f1c40f;">
                                    <?php echo str_repeat('★', $r['rating']) . str_repeat('☆', 5 - $r['rating']); ?>
                                </span>
                            </td>
                            <td>
                                <p style="font-size:0.9em; color:#555; max-width:300px;"><?php echo nl2br(htmlspecialchars($r['comment'])); ?></p>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $r['status'] == 'approved' ? 'delivered' : ($r['status'] == 'pending' ? 'pending' : 'cancelled'); ?>">
                                    <?php echo ucfirst($r['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($r['created_at'])); ?></td>
                            <td class="actions">
                                <?php if ($r['status'] == 'pending'): ?>
                                    <a href="?action=approve&id=<?php echo $r['id']; ?>" class="btn-icon view" title="Approve"><i class="fas fa-check"></i></a>
                                <?php endif; ?>
                                <a href="?action=delete&id=<?php echo $r['id']; ?>" class="btn-icon delete" onclick="return confirm('Delete this review?')" title="Delete"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($reviews)): ?>
                            <tr><td colspan="7" style="text-align:center; padding:20px;">No reviews found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>