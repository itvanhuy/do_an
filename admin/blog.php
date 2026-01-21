<?php
// File: admin/blog.php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/database.php';

Auth::requireAdmin();

$db = Database::getInstance();
$action = $_GET['action'] ?? 'list';
$message = '';

// Cập nhật thêm cột post_type cho bảng posts nếu chưa có
try {
    $db->exec("ALTER TABLE posts ADD COLUMN post_type ENUM('news', 'tournament') DEFAULT 'news' AFTER status");
} catch (Exception $e) {}

// Hàm upload ảnh bài viết
function uploadPostImage($fileInputName) {
    if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] === UPLOAD_ERR_NO_FILE) {
        return ''; 
    }
    $file = $_FILES[$fileInputName];
    if ($file['error'] !== UPLOAD_ERR_OK) return '';
    
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) return '';
    
    $newName = 'post_' . uniqid() . '.' . $ext;
    $uploadDir = '../img/';
    if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
    
    if (move_uploaded_file($file['tmp_name'], $uploadDir . $newName)) {
        return $newName;
    }
    return '';
}

// Xử lý Action
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action == 'add' || $action == 'edit') {
        try {
            $image = uploadPostImage('image');
            
            // Giữ ảnh cũ nếu không upload mới khi edit
            if ($action == 'edit') {
                if (empty($image)) $image = $_POST['existing_image'] ?? '';
            }

            $data = [
                $_POST['title'],
                $_POST['excerpt'],
                $_POST['content'],
                $image,
                $_POST['status'],
                $_POST['post_type'] ?? 'news'
            ];

            if ($action == 'add') {
                $data[] = $_SESSION['user_id'] ?? 1; // Author ID
                $stmt = $db->prepare("INSERT INTO posts (title, excerpt, content, image, status, post_type, author_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute($data);
                $message = '<div class="alert success">Post created successfully!</div>';
            } else {
                $data[] = $_GET['id'];
                $stmt = $db->prepare("UPDATE posts SET title=?, excerpt=?, content=?, image=?, status=?, post_type=? WHERE id=?");
                $stmt->execute($data);
                $message = '<div class="alert success">Post updated successfully!</div>';
            }
        } catch (PDOException $e) {
            $message = '<div class="alert error">Error: ' . $e->getMessage() . '</div>';
        }
    }
}

if ($action == 'delete' && isset($_GET['id'])) {
    try {
        $stmt = $db->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $message = '<div class="alert success">Post deleted successfully!</div>';
        $action = 'list';
    } catch (PDOException $e) {
        $message = '<div class="alert error">Error: ' . $e->getMessage() . '</div>';
    }
}

// Lấy dữ liệu cho Edit
$post = [];
if ($action == 'edit' && isset($_GET['id'])) {
    $stmt = $db->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Phân trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Lấy danh sách posts
$stmt = $db->query("SELECT COUNT(*) as total FROM posts");
$totalPosts = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalPosts / $limit);

$stmt = $db->query("SELECT * FROM posts ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Management - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        <div class="main-content">
            <?php include 'includes/header.php'; ?>
            
            <div class="content-header">
                <h2>Blog Management</h2>
                <a href="?action=add" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Post</a>
            </div>
            
            <?php echo $message; ?>

            <?php if ($action == 'add' || $action == 'edit'): ?>
            <div class="form-container">
                <form method="POST" enctype="multipart/form-data">
                    <?php if ($action == 'edit'): ?>
                        <input type="hidden" name="existing_image" value="<?php echo $post['image']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label>Title</label>
                            <input type="text" name="title" value="<?php echo htmlspecialchars($post['title'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status">
                                <option value="published" <?php echo ($post['status'] ?? '') == 'published' ? 'selected' : ''; ?>>Published</option>
                                <option value="draft" <?php echo ($post['status'] ?? '') == 'draft' ? 'selected' : ''; ?>>Draft</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Post Type</label>
                            <select name="post_type">
                                <option value="news" <?php echo ($post['post_type'] ?? '') == 'news' ? 'selected' : ''; ?>>General / Product News</option>
                                <option value="tournament" <?php echo ($post['post_type'] ?? '') == 'tournament' ? 'selected' : ''; ?>>Tournament News</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Featured Image</label>
                            <input type="file" name="image">
                            <?php if (!empty($post['image'])): ?>
                                <div style="margin-top: 5px;">
                                    <img src="../img/<?php echo $post['image']; ?>" height="50" style="border-radius: 4px;">
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group full-width">
                            <label>Excerpt (Short Description)</label>
                            <textarea name="excerpt" rows="3"><?php echo htmlspecialchars($post['excerpt'] ?? ''); ?></textarea>
                        </div>

                        <div class="form-group full-width">
                            <label>Content</label>
                            <textarea name="content" rows="15" required><?php echo htmlspecialchars($post['content'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Save Post</button>
                        <a href="blog.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
            <?php else: ?>
            
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Views</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($posts as $p): ?>
                        <tr>
                            <td>#<?php echo $p['id']; ?></td>
                            <td>
                                <?php if(!empty($p['image'])): ?>
                                    <img src="../img/<?php echo $p['image']; ?>" style="width: 50px; height: 30px; object-fit: cover; border-radius: 4px;">
                                <?php else: ?>
                                    <span style="color: #ccc;">No image</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($p['title']); ?></strong>
                            </td>
                            <td>
                                <?php if (($p['post_type'] ?? 'news') == 'tournament'): ?>
                                    <span class="badge" style="background: #a970ff;">Tournament</span>
                                <?php else: ?>
                                    <span class="badge" style="background: #6c757d;">General</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $p['status'] == 'published' ? 'active' : 'inactive'; ?>">
                                    <?php echo ucfirst($p['status']); ?>
                                </span>
                            </td>
                            <td><?php echo $p['views']; ?></td>
                            <td><?php echo date('M d, Y', strtotime($p['created_at'])); ?></td>
                            <td class="actions">
                                <a href="../pages/news-detail.php?id=<?php echo $p['id']; ?>" class="btn-icon view" target="_blank"><i class="fas fa-eye"></i></a>
                                <a href="?action=edit&id=<?php echo $p['id']; ?>" class="btn-icon edit"><i class="fas fa-edit"></i></a>
                                <a href="?action=delete&id=<?php echo $p['id']; ?>" class="btn-icon delete" onclick="return confirm('Delete this post?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($posts)): ?>
                            <tr><td colspan="7" style="text-align:center; padding: 20px;">No posts found.</td></tr>
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
            <?php endif; ?>
        </div>
    </div>
</body>
</html>