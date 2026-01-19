<?php
// File: admin/categories.php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/database.php';

Auth::requireAdmin();

$db = Database::getInstance();
$action = $_GET['action'] ?? 'list';
$message = '';

// Xử lý các action
switch ($action) {
    case 'add':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $stmt = $db->prepare("
                INSERT INTO categories (name, description, created_at) 
                VALUES (?, ?, NOW())
            ");

                $stmt->execute([
                    $_POST['name'],
                    $_POST['description']
                ]);

                $message = '<div class="alert success">Category added successfully!</div>';
            } catch (PDOException $e) {
                $message = '<div class="alert error">Error: ' . $e->getMessage() . '</div>';
            }
        }
        break;
    case 'edit':
        $id = $_GET['id'] ?? 0;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $stmt = $db->prepare("
                UPDATE categories SET 
                name = ?, description = ?
                WHERE id = ?
            ");

                $stmt->execute([
                    $_POST['name'],
                    $_POST['description'],
                    $id
                ]);

                $message = '<div class="alert success">Category updated successfully!</div>';
            } catch (PDOException $e) {
                $message = '<div class="alert error">Error: ' . $e->getMessage() . '</div>';
            }
        }

        $stmt = $db->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);
        break;

    case 'delete':
        $id = $_GET['id'] ?? 0;

        try {
            // Kiểm tra có sản phẩm nào trong category này không
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM products WHERE category_id = ?");
            $stmt->execute([$id]);
            $product_result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($product_result['count'] > 0) {
                $message = '<div class="alert error">Cannot delete category with existing products!</div>';
            } else {
                $stmt = $db->prepare("DELETE FROM categories WHERE id = ?");
                $stmt->execute([$id]);
                $message = '<div class="alert success">Category deleted successfully!</div>';
            }
        } catch (PDOException $e) {
            $message = '<div class="alert error">Error: ' . $e->getMessage() . '</div>';
        }
        break;
}

// Lấy danh sách categories với parent name
$stmt = $db->query("
    SELECT c.*, 
           (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id) as product_count
    FROM categories c 
    ORDER BY c.name
");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories Management - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>

<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-content">
            <?php include 'includes/header.php'; ?>

            <div class="content-header">
                <h2>Categories Management</h2>
                <div class="actions">
                    <a href="?action=add" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Category
                    </a>
                </div>
            </div>

            <?php echo $message; ?>

            <!-- Form Add/Edit Category -->
            <?php if ($action == 'add' || $action == 'edit'): ?>
                <div class="form-container">
                    <form method="POST">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="name">Category Name *</label>
                                <input type="text" id="name" name="name"
                                    value="<?php echo $category['name'] ?? ''; ?>"
                                    required>
                            </div>
                            <!-- SỬA: Xóa phần slug input -->

                            <div class="form-group full-width">
                                <label for="description">Description</label>
                                <textarea id="description" name="description" rows="4"><?php echo $category['description'] ?? ''; ?></textarea>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Category
                            </button>
                            <a href="categories.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>

            <?php else: ?>

                <!-- Categories Table -->
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Products</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($categories as $cat): ?>
                                <tr>
                                    <td>#<?php echo $cat['id']; ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($cat['name']); ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge"><?php echo $cat['product_count']; ?></span>
                                    </td>
                                    <td><?php echo htmlspecialchars(substr($cat['description'] ?? '', 0, 50) . '...'); ?></td>
                                    <td class="actions">
                                        <a href="?action=edit&id=<?php echo $cat['id']; ?>"
                                            class="btn-icon edit" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?action=delete&id=<?php echo $cat['id']; ?>"
                                            class="btn-icon delete"
                                            onclick="return confirm('Delete this category?')"
                                            title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- SỬA: Xóa script auto-generate slug -->
</body>

</html>