<?php
// File: admin/comments.php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/database.php';

Auth::requireAdmin();

$db = Database::getInstance();
$action = $_GET['action'] ?? 'list';
$message = '';

// Xử lý xóa bình luận
if ($action == 'delete' && isset($_GET['id'])) {
    try {
        $stmt = $db->prepare("DELETE FROM comments WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $message = '<div class="alert success">Comment deleted successfully!</div>';
    } catch (PDOException $e) {
        $message = '<div class="alert error">Error: ' . $e->getMessage() . '</div>';
    }
}

// Phân trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Lấy danh sách bình luận kèm thông tin user và bài viết
$query = "
    SELECT c.*, u.username, u.full_name, p.title as post_title 
    FROM comments c
    LEFT JOIN users u ON c.user_id = u.id
    LEFT JOIN posts p ON c.post_id = p.id
    ORDER BY c.created_at DESC
    LIMIT $limit OFFSET $offset
";
$stmt = $db->query($query);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Đếm tổng số bình luận để phân trang
$countStmt = $db->query("SELECT COUNT(*) as total FROM comments");
$totalComments = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalComments / $limit);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comments Management - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        <div class="main-content">
            <?php include 'includes/header.php'; ?>
            
            <div class="content-header">
                <h2>Comments Management</h2>
            </div>
            
            <?php echo $message; ?>
            
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Author</th>
                            <th>Comment</th>
                            <th>Post</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($comments as $c): ?>
                        <tr>
                            <td>#<?php echo $c['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($c['full_name'] ?: ($c['username'] ?? 'Unknown')); ?></strong>
                                <?php if(!empty($c['username'])): ?>
                                    <br><small class="description">@<?php echo htmlspecialchars($c['username']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <p style="max-width: 400px; word-wrap: break-word;"><?php echo nl2br(htmlspecialchars($c['content'])); ?></p>
                            </td>
                            <td>
                                <?php if ($c['post_title']): ?>
                                    <a href="../pages/news-detail.php?id=<?php echo $c['post_id']; ?>" target="_blank" style="color: var(--primary); text-decoration: none;">
                                        <?php echo htmlspecialchars(mb_strimwidth($c['post_title'], 0, 30, '...')); ?>
                                    </a>
                                <?php else: ?>
                                    <span style="color: #999;">(Post deleted)</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('M d, Y H:i', strtotime($c['created_at'])); ?></td>
                            <td class="actions">
                                <a href="?action=delete&id=<?php echo $c['id']; ?>" class="btn-icon delete" onclick="return confirm('Are you sure you want to delete this comment?')" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($comments)): ?>
                            <tr><td colspan="6" style="text-align:center; padding: 20px;">No comments found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?>"><i class="fas fa-chevron-left"></i> Previous</a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>" class="<?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?php echo $page + 1; ?>">Next <i class="fas fa-chevron-right"></i></a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>