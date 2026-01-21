<?php
// File: admin/coupons.php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/database.php';

Auth::requireAdmin();

$db = Database::getInstance();
$action = $_GET['action'] ?? 'list';
$message = '';

// Xử lý Action
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action == 'add' || $action == 'edit') {
        try {
            $data = [
                strtoupper(trim($_POST['code'])),
                $_POST['discount_value'],
                $_POST['discount_type'],
                $_POST['min_order_amount'] ?? 0,
                !empty($_POST['expiry_date']) ? $_POST['expiry_date'] : NULL,
                isset($_POST['is_active']) ? 1 : 0
            ];

            if ($action == 'add') {
                $stmt = $db->prepare("INSERT INTO coupons (code, discount_value, discount_type, min_order_amount, expiry_date, is_active) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute($data);
                $message = '<div class="alert success">Coupon added successfully!</div>';
            } else {
                $data[] = $_GET['id'];
                $stmt = $db->prepare("UPDATE coupons SET code=?, discount_value=?, discount_type=?, min_order_amount=?, expiry_date=?, is_active=? WHERE id=?");
                $stmt->execute($data);
                $message = '<div class="alert success">Coupon updated successfully!</div>';
            }
        } catch (PDOException $e) {
            $message = '<div class="alert error">Error: ' . $e->getMessage() . '</div>';
        }
    }
}

if ($action == 'delete' && isset($_GET['id'])) {
    $stmt = $db->prepare("DELETE FROM coupons WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $message = '<div class="alert success">Coupon deleted successfully!</div>';
    $action = 'list';
}

// Lấy dữ liệu cho Edit
$coupon = [];
if ($action == 'edit' && isset($_GET['id'])) {
    $stmt = $db->prepare("SELECT * FROM coupons WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $coupon = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Lấy danh sách coupons
$coupons = [];
try {
    $stmt = $db->query("SELECT * FROM coupons ORDER BY created_at DESC");
    $coupons = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Bỏ qua lỗi nếu bảng chưa tồn tại
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coupons Management - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        <div class="main-content">
            <?php include 'includes/header.php'; ?>
            
            <div class="content-header">
                <h2>Coupons Management</h2>
                <a href="?action=add" class="btn btn-primary"><i class="fas fa-plus"></i> Add Coupon</a>
            </div>
            
            <?php echo $message; ?>

            <?php if ($action == 'add' || $action == 'edit'): ?>
            <div class="form-container">
                <form method="POST">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Coupon Code</label>
                            <input type="text" name="code" value="<?php echo htmlspecialchars($coupon['code'] ?? ''); ?>" required placeholder="e.g. SALE10">
                        </div>
                        <div class="form-group">
                            <label>Discount Type</label>
                            <select name="discount_type">
                                <option value="percent" <?php echo ($coupon['discount_type'] ?? '') == 'percent' ? 'selected' : ''; ?>>Percentage (%)</option>
                                <option value="fixed" <?php echo ($coupon['discount_type'] ?? '') == 'fixed' ? 'selected' : ''; ?>>Fixed Amount (VND)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Discount Value</label>
                            <input type="number" name="discount_value" value="<?php echo $coupon['discount_value'] ?? ''; ?>" required step="0.01">
                        </div>
                        <div class="form-group">
                            <label>Min Order Amount</label>
                            <input type="number" name="min_order_amount" value="<?php echo $coupon['min_order_amount'] ?? 0; ?>">
                        </div>
                        <div class="form-group">
                            <label>Expiry Date</label>
                            <input type="date" name="expiry_date" value="<?php echo $coupon['expiry_date'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label class="checkbox-label" style="margin-top: 30px;">
                                <input type="checkbox" name="is_active" <?php echo ($coupon['is_active'] ?? 1) ? 'checked' : ''; ?>>
                                <span>Active</span>
                            </label>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Save Coupon</button>
                        <a href="coupons.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
            <?php else: ?>
            
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Discount</th>
                            <th>Min Order</th>
                            <th>Expiry</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($coupons as $c): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($c['code']); ?></strong></td>
                            <td>
                                <?php 
                                    if ($c['discount_type'] == 'percent') {
                                        echo $c['discount_value'] . '%';
                                    } else {
                                        echo number_format($c['discount_value'], 0, ',', '.') . ' VND';
                                    }
                                ?>
                            </td>
                            <td><?php echo number_format($c['min_order_amount'], 0, ',', '.'); ?> VND</td>
                            <td>
                                <?php 
                                    if ($c['expiry_date']) {
                                        $isExpired = strtotime($c['expiry_date']) < time();
                                        echo '<span style="' . ($isExpired ? 'color:red' : '') . '">' . date('M d, Y', strtotime($c['expiry_date'])) . '</span>';
                                    } else {
                                        echo 'Never';
                                    }
                                ?>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $c['is_active'] ? 'active' : 'inactive'; ?>">
                                    <?php echo $c['is_active'] ? 'Active' : 'Inactive'; ?>
                                </span>
                            </td>
                            <td class="actions">
                                <a href="?action=edit&id=<?php echo $c['id']; ?>" class="btn-icon edit"><i class="fas fa-edit"></i></a>
                                <a href="?action=delete&id=<?php echo $c['id']; ?>" class="btn-icon delete" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($coupons)): ?>
                            <tr><td colspan="6" style="text-align:center; padding:20px;">No coupons found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>