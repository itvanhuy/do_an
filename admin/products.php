<?php
// File: admin/products.php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/database.php';

<<<<<<< HEAD
// ==========================================================================
// 1. KHỞI TẠO & CẤU HÌNH DATABASE
// ==========================================================================

=======
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
Auth::requireAdmin();

$db = Database::getInstance();
$action = $_GET['action'] ?? 'list';
$message = '';

<<<<<<< HEAD
// Tạo bảng product_images nếu chưa có (cho tính năng Gallery)
=======
// Tự động tạo bảng product_images nếu chưa tồn tại
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
try {
    $db->exec("CREATE TABLE IF NOT EXISTS product_images (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT NOT NULL,
        image VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )");
} catch (PDOException $e) {}

<<<<<<< HEAD
// Cập nhật bảng products (thêm cột discount)
try {
    $db->exec("ALTER TABLE products ADD COLUMN discount INT DEFAULT 0 AFTER price");
} catch (Exception $e) {}

// ==========================================================================
// 2. HÀM HỖ TRỢ (HELPER FUNCTIONS)
// ==========================================================================

// Hàm upload ảnh đại diện sản phẩm
=======
// Hàm xử lý upload ảnh
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
function uploadImage($fileInputName)
{
    // Kiểm tra có file được upload không
    if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] === UPLOAD_ERR_NO_FILE) {
        return 'default.jpg'; // Không có file upload, dùng ảnh mặc định
    }

    $file = $_FILES[$fileInputName];

    // Kiểm tra lỗi upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return 'default.jpg';
    }

    // Kiểm tra định dạng file (đơn giản bằng extension)
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($fileExtension, $allowedExtensions)) {
        return 'default.jpg';
    }

    // Kiểm tra kích thước file (max 5MB)
    $maxSize = 5 * 1024 * 1024; // 5MB
    if ($file['size'] > $maxSize) {
        return 'default.jpg';
    }

    // Tạo tên file mới để tránh trùng
    $newFileName = uniqid() . '_' . time() . '.' . $fileExtension;

    // Thư mục lưu ảnh
    $uploadDir = '../img/product/';

    // Tạo thư mục nếu chưa tồn tại
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Đường dẫn đầy đủ
    $uploadPath = $uploadDir . $newFileName;

    // Di chuyển file tạm vào thư mục đích
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return $newFileName;
    }

    return 'default.jpg';
}

<<<<<<< HEAD
// ==========================================================================
// 3. XỬ LÝ CÁC ACTION (THÊM, SỬA, XÓA)
// ==========================================================================

switch ($action) {
    // --- ACTION: THÊM SẢN PHẨM ---
=======
// Xử lý các action
switch ($action) {
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
    case 'add':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $stmt = $db->prepare("
                    INSERT INTO products 
<<<<<<< HEAD
                    (name, description, price, discount, category_id, brand, stock_quantity, image, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
=======
                    (name, description, price, category_id, brand, stock_quantity, image, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
                ");

                // Upload ảnh
                $image = uploadImage('image');
                $stock_quantity = $_POST['stock_quantity'] ?? 0;
<<<<<<< HEAD
                $discount = $_POST['discount'] ?? 0;
=======
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695

                $stmt->execute([
                    $_POST['name'] ?? '',
                    $_POST['description'] ?? '',
                    $_POST['price'] ?? 0,
<<<<<<< HEAD
                    $discount,
=======
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
                    $_POST['category_id'] ?? 0,
                    $_POST['brand'] ?? '',
                    $stock_quantity,
                    $image
                ]);

                $productId = $db->getConnection()->lastInsertId();

                // Xử lý upload Gallery (Nhiều ảnh)
                if (isset($_FILES['gallery']) && !empty($_FILES['gallery']['name'][0])) {
                    $files = $_FILES['gallery'];
                    $count = count($files['name']);
                    $stmtImg = $db->prepare("INSERT INTO product_images (product_id, image) VALUES (?, ?)");
                    
                    for ($i = 0; $i < $count; $i++) {
                        if ($files['error'][$i] === UPLOAD_ERR_OK) {
                            $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
                            $newName = uniqid() . '_gallery_' . $i . '.' . $ext;
                            if (move_uploaded_file($files['tmp_name'][$i], '../img/product/' . $newName)) {
                                $stmtImg->execute([$productId, $newName]);
                            }
                        }
                    }
                }

                $message = '<div class="alert success">Product added successfully!</div>';
            } catch (PDOException $e) {
                $message = '<div class="alert error">Error: ' . $e->getMessage() . '</div>';
            }
        }
        break;

<<<<<<< HEAD
    // --- ACTION: SỬA SẢN PHẨM ---
=======
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
    case 'edit':
        $id = $_GET['id'] ?? 0;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $stmt = $db->prepare("
                    UPDATE products SET 
<<<<<<< HEAD
                    name = ?, description = ?, price = ?, discount = ?,
=======
                    name = ?, description = ?, price = ?, 
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
                    category_id = ?, brand = ?, stock_quantity = ?, image = ?
                    WHERE id = ?
                ");

                // THÊM: Xử lý ảnh mới nếu có upload
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $image = uploadImage('image');
                } else {
                    // Giữ ảnh cũ
                    $image = $_POST['existing_image'] ?? 'default.jpg';
                }

                $stock_quantity = $_POST['stock_quantity'] ?? 0;
<<<<<<< HEAD
                $discount = $_POST['discount'] ?? 0;
=======
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695

                $stmt->execute([
                    $_POST['name'] ?? '',
                    $_POST['description'] ?? '',
                    $_POST['price'] ?? 0,
<<<<<<< HEAD
                    $discount,
=======
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
                    $_POST['category_id'] ?? 0,
                    $_POST['brand'] ?? '',
                    $stock_quantity,
                    $image,
                    $id
                ]);

                // Xử lý upload Gallery (Thêm ảnh mới vào gallery)
                if (isset($_FILES['gallery']) && !empty($_FILES['gallery']['name'][0])) {
                    $files = $_FILES['gallery'];
                    $count = count($files['name']);
                    $stmtImg = $db->prepare("INSERT INTO product_images (product_id, image) VALUES (?, ?)");
                    
                    for ($i = 0; $i < $count; $i++) {
                        if ($files['error'][$i] === UPLOAD_ERR_OK) {
                            $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
                            $newName = uniqid() . '_gallery_' . $i . '.' . $ext;
                            if (move_uploaded_file($files['tmp_name'][$i], '../img/product/' . $newName)) {
                                $stmtImg->execute([$id, $newName]);
                            }
                        }
                    }
                }

                $message = '<div class="alert success">Product updated successfully!</div>';
            } catch (PDOException $e) {
                $message = '<div class="alert error">Error: ' . $e->getMessage() . '</div>';
            }
        }

        $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Lấy danh sách ảnh gallery
        $stmtImg = $db->prepare("SELECT * FROM product_images WHERE product_id = ?");
        $stmtImg->execute([$id]);
        $galleryImages = $stmtImg->fetchAll(PDO::FETCH_ASSOC);
        break;

<<<<<<< HEAD
    // --- ACTION: XÓA ẢNH GALLERY ---
=======
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
    case 'delete_image':
        $imgId = $_GET['img_id'] ?? 0;
        $productId = $_GET['product_id'] ?? 0;
        if ($imgId) {
            $stmt = $db->prepare("SELECT image FROM product_images WHERE id = ?");
            $stmt->execute([$imgId]);
            $img = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($img) {
                if (file_exists('../img/product/' . $img['image'])) unlink('../img/product/' . $img['image']);
                $db->prepare("DELETE FROM product_images WHERE id = ?")->execute([$imgId]);
            }
        }
        header("Location: products.php?action=edit&id=" . $productId);
        exit;
        break;

<<<<<<< HEAD
    // --- ACTION: XÓA SẢN PHẨM ---
=======
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
    case 'delete':
        $id = $_GET['id'] ?? 0;

        try {
            $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $message = '<div class="alert success">Product deleted successfully!</div>';
        } catch (PDOException $e) {
            $message = '<div class="alert error">Error: ' . $e->getMessage() . '</div>';
        }
        break;
}

<<<<<<< HEAD
// ==========================================================================
// 4. LẤY DỮ LIỆU HIỂN THỊ (LIST VIEW)
// ==========================================================================

// Xử lý tìm kiếm và phân trang
=======
// Lấy danh sách sản phẩm
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
$search = $_GET['search'] ?? '';
$category_id = $_GET['category'] ?? 0;
$page = $_GET['page'] ?? 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$query = "SELECT p.*, c.name as category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          WHERE 1=1";
$params = [];

if ($search) {
    $query .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($category_id) {
    $query .= " AND p.category_id = ?";
    $params[] = $category_id;
}

$query .= " ORDER BY p.created_at DESC LIMIT $limit OFFSET $offset";

$stmt = $db->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

<<<<<<< HEAD
// Tính tổng số trang
=======
// Lấy tổng số sản phẩm cho phân trang
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
$countQuery = "SELECT COUNT(*) as total FROM products WHERE 1=1";
if ($search) {
    $countQuery .= " AND (name LIKE ? OR description LIKE ?)";
}
if ($category_id) {
    $countQuery .= " AND category_id = ?";
}

$stmt = $db->prepare($countQuery);
$stmt->execute($params);
$totalProducts = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalProducts / $limit);

<<<<<<< HEAD
// Lấy danh sách danh mục (cho dropdown filter)
$stmt = $db->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy danh sách thương hiệu (cho dropdown filter)
=======
// Lấy danh mục
$stmt = $db->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy danh sách brands
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
$stmt = $db->query("SELECT * FROM brands ORDER BY name ASC");
$brands = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products Management - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>

<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <?php include 'includes/header.php'; ?>

            <div class="content-header">
                <h2>Products Management</h2>
                <div class="actions">
                    <a href="?action=add" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Product
                    </a>
                </div>
            </div>

            <?php echo $message; ?>

            <!-- Form Add/Edit Product -->
            <?php if ($action == 'add' || $action == 'edit'): ?>
                <div class="form-container">
                    <form method="POST" enctype="multipart/form-data">
                        <!-- THÊM: Hidden field cho ảnh hiện tại (chỉ trong edit mode) -->
                        <?php if ($action == 'edit' && isset($product['image'])): ?>
                            <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($product['image']); ?>">
                        <?php endif; ?>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="name">Product Name *</label>
                                <input type="text" id="name" name="name"
                                    value="<?php echo $product['name'] ?? ''; ?>"
                                    required>
                            </div>

                            <div class="form-group">
<<<<<<< HEAD
                                <label for="price">Price (VNĐ) *</label>
                                <input type="number" id="price" name="price" step="1000"
=======
                                <label for="price">Price ($) *</label>
                                <input type="number" id="price" name="price" step="0.01"
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
                                    value="<?php echo $product['price'] ?? ''; ?>"
                                    required>
                            </div>

                            <div class="form-group">
<<<<<<< HEAD
                                <label for="discount">Discount (%)</label>
                                <input type="number" id="discount" name="discount" min="0" max="100"
                                    value="<?php echo $product['discount'] ?? 0; ?>">
                            </div>

                            <div class="form-group">
=======
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
                                <label for="category_id">Category</label>
                                <select id="category_id" name="category_id">
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>"
                                            <?php echo (($product['category_id'] ?? '') == $cat['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="brand">Brand</label>
                                <select id="brand" name="brand">
                                    <option value="">-- Select Brand --</option>
                                    <?php foreach ($brands as $b): ?>
                                        <option value="<?php echo htmlspecialchars($b['name']); ?>"
                                            <?php echo (($product['brand'] ?? '') == $b['name']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($b['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="stock_quantity">Stock Quantity</label>
                                <input type="number" id="stock_quantity" name="stock_quantity"
                                    value="<?php echo $product['stock_quantity'] ?? 0; ?>">
                            </div>

                            <div class="form-group full-width">
                                <label for="description">Description</label>
                                <textarea id="description" name="description" rows="5"><?php echo $product['description'] ?? ''; ?></textarea>
                            </div>

                            <div class="form-group">
                                <label for="image">Product Image</label>
                                <div class="image-upload">
                                    <input type="file" id="image" name="image" accept="image/*">
                                    <div class="image-preview">
                                        <?php if (!empty($product['image']) && $product['image'] !== 'default.jpg'): ?>
                                            <img src="../img/product/<?php echo $product['image']; ?>" alt="Preview">
                                            <p class="current-image">Current: <?php echo $product['image']; ?></p>
                                        <?php else: ?>
                                            <div class="no-image">No image selected</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Gallery Images -->
                            <div class="form-group full-width">
                                <label>Product Gallery (Select multiple images)</label>
                                <input type="file" name="gallery[]" multiple accept="image/*" style="padding: 10px; border: 1px solid #ddd; width: 100%;">
                                <small style="color: #666; display: block; margin-top: 5px;">Hold Ctrl (Windows) or Command (Mac) to select multiple files.</small>
                                
                                <?php if (!empty($galleryImages)): ?>
                                    <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 15px;">
                                        <?php foreach ($galleryImages as $img): ?>
                                            <div style="position: relative; border: 1px solid #ddd; padding: 5px; border-radius: 4px;">
                                                <img src="../img/product/<?php echo $img['image']; ?>" style="width: 80px; height: 80px; object-fit: cover;">
                                                <a href="?action=delete_image&img_id=<?php echo $img['id']; ?>&product_id=<?php echo $product['id']; ?>" 
                                                   onclick="return confirm('Delete this image?')"
                                                   style="position: absolute; top: -8px; right: -8px; background: red; color: white; border-radius: 50%; width: 20px; height: 20px; text-align: center; line-height: 20px; text-decoration: none; font-size: 12px;">&times;</a>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Product
                            </button>
                            <a href="products.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            <?php else: ?>

                <!-- Search and Filter -->
                <div class="search-filter">
                    <form method="GET" class="search-form">
                        <div class="search-input">
                            <i class="fas fa-search"></i>
                            <input type="text" name="search" placeholder="Search products..."
                                value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <select name="category">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>"
                                    <?php echo ($category_id == $cat['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-secondary">Filter</button>
                        <a href="products.php" class="btn btn-light">Clear</a>
                    </form>
                </div>

                <!-- Products Table -->
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Brand</th>
                                <th>Price</th>
                                <th>Stock</th> <!-- SỬA: Đổi từ "stock_quality" thành "Stock" -->
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td>#<?php echo $product['id']; ?></td>
                                    <td class="product-image">
                                        <img src="../img/product/<?php echo $product['image'] ?: 'default.jpg'; ?>"
                                            alt="<?php echo htmlspecialchars($product['name']); ?>">
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                                        <p class="description"><?php echo substr($product['description'] ?? '', 0, 50) . '...'; ?></p>
                                    </td>
                                    <td><?php echo htmlspecialchars($product['category_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($product['brand'] ?? 'N/A'); ?></td>
<<<<<<< HEAD
                                    <td class="price">
                                        <?php if (($product['discount'] ?? 0) > 0): ?>
                                            <span style="text-decoration: line-through; color: #999; font-size: 0.9em;">
                                                <?php echo number_format($product['price'] ?? 0, 0, ',', '.'); ?> VNĐ
                                            </span>
                                            <br>
                                            <span style="color: #e60023; font-weight: bold;">
                                                <?php echo number_format(($product['price'] ?? 0) * (1 - ($product['discount'] / 100)), 0, ',', '.'); ?> VNĐ
                                            </span>
                                            <span class="badge" style="background-color: #e60023; font-size: 0.8em;">-<?php echo $product['discount']; ?>%</span>
                                        <?php else: ?>
                                            <?php echo number_format($product['price'] ?? 0, 0, ',', '.'); ?> VNĐ
                                        <?php endif; ?>
                                    </td>
=======
                                    <td class="price">$<?php echo number_format($product['price'] ?? 0, 2); ?></td>
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
                                    <td>
                                        <span class="stock <?php echo ($product['stock_quantity'] ?? 0) > 0 ? 'in-stock' : 'out-stock'; ?>">
                                            <?php echo $product['stock_quantity'] ?? 0; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($product['created_at'] ?? 'now')); ?></td>
                                    <td class="actions">
                                        <a href="?action=edit&id=<?php echo $product['id']; ?>"
                                            class="btn-icon edit" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?action=delete&id=<?php echo $product['id']; ?>"
                                            class="btn-icon delete"
                                            onclick="return confirm('Delete this product?')"
                                            title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <a href="../pages/product-detail.php?id=<?php echo $product['id']; ?>"
                                            class="btn-icon view" target="_blank" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <div class="pagination">
                            <?php if ($page > 1): ?>
                                <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category_id; ?>">
                                    <i class="fas fa-chevron-left"></i> Previous
                                </a>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category_id; ?>"
                                    class="<?php echo $i == $page ? 'active' : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($page < $totalPages): ?>
                                <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category_id; ?>">
                                    Next <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Menu toggle
        document.getElementById('menuToggle')?.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('collapsed');
            document.querySelector('.main-content').classList.toggle('expanded');
        });

        // Image preview
        document.getElementById('image')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.querySelector('.image-preview');

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                };
                reader.readAsDataURL(file);
            }
        });

        // Confirm delete
        document.querySelectorAll('.delete').forEach(link => {
            link.addEventListener('click', function(e) {
                if (!confirm('Are you sure you want to delete this product?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>

</html>