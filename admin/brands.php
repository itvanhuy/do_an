<?php
// File: admin/brands.php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/database.php';

Auth::requireAdmin();

$db = Database::getInstance();
$action = $_GET['action'] ?? 'list';
$message = '';

// Tự động tạo bảng brands nếu chưa tồn tại
try {
    $db->exec("CREATE TABLE IF NOT EXISTS brands (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        logo VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
} catch (PDOException $e) {
    die("Error creating table: " . $e->getMessage());
}

// Hàm upload logo thương hiệu
function uploadBrandLogo($fileInputName) {
    if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] === UPLOAD_ERR_NO_FILE) {
        return ''; 
    }
    $file = $_FILES[$fileInputName];
    if ($file['error'] !== UPLOAD_ERR_OK) return '';
    
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) return '';
    
    $newName = 'brand_' . uniqid() . '.' . $ext;
    $uploadDir = '../img/brands/';
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
            $logo = uploadBrandLogo('logo');
            
            if ($action == 'edit' && empty($logo)) {
                $logo = $_POST['existing_logo'] ?? '';
            }

            $name = trim($_POST['name']);

            if ($action == 'add') {
                $stmt = $db->prepare("INSERT INTO brands (name, logo) VALUES (?, ?)");
                $stmt->execute([$name, $logo]);
                $message = '<div class="alert success">Brand added successfully!</div>';
            } else {
                $id = $_GET['id'];
                $stmt = $db->prepare("UPDATE brands SET name=?, logo=? WHERE id=?");
                $stmt->execute([$name, $logo, $id]);
                $message = '<div class="alert success">Brand updated successfully!</div>';
            }
        } catch (PDOException $e) {
            $message = '<div class="alert error">Error: ' . $e->getMessage() . '</div>';
        }
    }
}

if ($action == 'delete' && isset($_GET['id'])) {
    try {
        $stmt = $db->prepare("DELETE FROM brands WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $message = '<div class="alert success">Brand deleted successfully!</div>';
        $action = 'list';
    } catch (PDOException $e) {
        $message = '<div class="alert error">Error: ' . $e->getMessage() . '</div>';
    }
}

// Lấy dữ liệu cho Edit
$brand = [];
if ($action == 'edit' && isset($_GET['id'])) {
    $stmt = $db->prepare("SELECT * FROM brands WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $brand = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Lấy danh sách brands
$stmt = $db->query("SELECT * FROM brands ORDER BY name ASC");
$brands = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brands Management - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        <div class="main-content">
            <?php include 'includes/header.php'; ?>
            
            <div class="content-header">
                <h2>Brands Management</h2>
                <a href="?action=add" class="btn btn-primary"><i class="fas fa-plus"></i> Add Brand</a>
            </div>
            
            <?php echo $message; ?>

            <?php if ($action == 'add' || $action == 'edit'): ?>
            <div class="form-container">
                <form method="POST" enctype="multipart/form-data">
                    <?php if ($action == 'edit'): ?>
                        <input type="hidden" name="existing_logo" value="<?php echo $brand['logo']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Brand Name</label>
                            <input type="text" name="name" value="<?php echo $brand['name'] ?? ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Logo</label>
                            <input type="file" name="logo">
                            <?php if (!empty($brand['logo'])): ?>
                                <div class="image-preview" style="width: 100px; height: 60px; margin-top: 10px;">
                                    <img src="../img/brands/<?php echo $brand['logo']; ?>" style="object-fit: contain;">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Save Brand</button>
                        <a href="brands.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
            <?php else: ?>
            
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Logo</th>
                            <th>Name</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($brands as $b): ?>
                        <tr>
                            <td>#<?php echo $b['id']; ?></td>
                            <td>
                                <?php if(!empty($b['logo'])): ?>
                                    <img src="../img/brands/<?php echo $b['logo']; ?>" style="width:60px; height:40px; object-fit:contain; border: 1px solid #eee; border-radius: 4px; background: #fff;">
                                <?php else: ?>
                                    <span style="color: #ccc;">No Logo</span>
                                <?php endif; ?>
                            </td>
                            <td><strong><?php echo htmlspecialchars($b['name']); ?></strong></td>
                            <td><?php echo date('M d, Y', strtotime($b['created_at'])); ?></td>
                            <td class="actions">
                                <a href="?action=edit&id=<?php echo $b['id']; ?>" class="btn-icon edit"><i class="fas fa-edit"></i></a>
                                <a href="?action=delete&id=<?php echo $b['id']; ?>" class="btn-icon delete" onclick="return confirm('Delete this brand?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($brands)): ?>
                            <tr><td colspan="5" style="text-align:center; padding: 20px;">No brands found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>