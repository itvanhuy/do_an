<?php
// File: admin/users.php
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
    case 'edit':
        $id = $_GET['id'] ?? 0;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $stmt = $db->prepare("
                    UPDATE users SET 
                    username = ?, email = ?, full_name = ?, 
                    phone = ?, address = ?, role = ?, is_active = ?
                    WHERE id = ?
                ");
                
                $stmt->execute([
                    $_POST['username'],
                    $_POST['email'],
                    $_POST['fullname'],
                    $_POST['phone'],
                    $_POST['address'],
                    $_POST['role'],
                    ($_POST['status'] == 'active') ? 1 : 0,
                    $id
                ]);
                
                $message = '<div class="alert success">User updated successfully!</div>';
            } catch (PDOException $e) {
                $message = '<div class="alert error">Error: ' . $e->getMessage() . '</div>';
            }
        }
        
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        break;
        
    case 'delete':
        $id = $_GET['id'] ?? 0;
        
        try {
            $stmt = $db->prepare("DELETE FROM users WHERE id = ? AND id != ?");
            $stmt->execute([$id, $_SESSION['user_id']]);
            $message = '<div class="alert success">User deleted successfully!</div>';
        } catch (PDOException $e) {
            $message = '<div class="alert error">Error: ' . $e->getMessage() . '</div>';
        }
        break;
}

// Lấy danh sách users
$search = $_GET['search'] ?? '';
$role = $_GET['role'] ?? '';
$page = $_GET['page'] ?? 1;
$limit = 15;
$offset = ($page - 1) * $limit;

$query = "SELECT * FROM users WHERE 1=1";
$params = [];

if ($search) {
    $query .= " AND (username LIKE ? OR email LIKE ? OR full_name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($role) {
    $query .= " AND role = ?";
    $params[] = $role;
}

// SỬA: Nối trực tiếp LIMIT và OFFSET để tránh lỗi PDO
$query .= " ORDER BY created_at DESC LIMIT $limit OFFSET $offset";

$stmt = $db->prepare($query);
$stmt->execute($params);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Thống kê - SỬA: Dùng is_active thay vì status
$statsQuery = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as admins,
    SUM(CASE WHEN role = 'user' THEN 1 ELSE 0 END) as users,
    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active,
    SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive
    FROM users";

$stmt = $db->query($statsQuery);
$stats = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Management - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <?php include 'includes/header.php'; ?>
            
            <div class="content-header">
                <h2>Users Management</h2>
                <div class="user-stats">
                    <div class="stat-item">
                        <span class="stat-label">Total Users</span>
                        <span class="stat-value"><?php echo $stats['total']; ?></span>
                    </div>
                    <div class="stat-item admin">
                        <span class="stat-label">Admins</span>
                        <span class="stat-value"><?php echo $stats['admins']; ?></span>
                    </div>
                    <div class="stat-item user">
                        <span class="stat-label">Users</span>
                        <span class="stat-value"><?php echo $stats['users']; ?></span>
                    </div>
                    <div class="stat-item active">
                        <span class="stat-label">Active</span>
                        <span class="stat-value"><?php echo $stats['active']; ?></span>
                    </div>
                </div>
            </div>
            
            <?php echo $message; ?>
            
            <!-- User Tabs -->
            <div class="user-tabs">
                <a href="users.php" class="<?php echo !$role ? 'active' : ''; ?>">
                    All Users (<?php echo $stats['total']; ?>)
                </a>
                <a href="?role=admin" class="<?php echo $role == 'admin' ? 'active' : ''; ?>">
                    Admins (<?php echo $stats['admins']; ?>)
                </a>
                <a href="?role=user" class="<?php echo $role == 'user' ? 'active' : ''; ?>">
                    Users (<?php echo $stats['users']; ?>)
                </a>
            </div>
            
            <!-- Search -->
            <div class="search-filter">
                <form method="GET" class="search-form">
                    <?php if ($role): ?>
                    <input type="hidden" name="role" value="<?php echo $role; ?>">
                    <?php endif; ?>
                    <div class="search-input">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" placeholder="Search by username, email or name..."
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <button type="submit" class="btn btn-secondary">Search</button>
                </form>
            </div>
            
            <?php if ($action == 'edit' && isset($user)): ?>
            <!-- Edit User Form -->
            <div class="form-container">
                <form method="POST">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="username">Username *</label>
                            <input type="text" id="username" name="username" 
                                   value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" 
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" 
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="fullname">Full Name</label>
                            <input type="text" id="fullname" name="fullname" 
                                   value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="text" id="phone" name="phone" 
                                   value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="role">Role</label>
                            <select id="role" name="role">
                                <option value="user" <?php echo ($user['role'] ?? '') == 'user' ? 'selected' : ''; ?>>User</option>
                                <option value="admin" <?php echo ($user['role'] ?? '') == 'admin' ? 'selected' : ''; ?>>Admin</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status">
                                <option value="active" <?php echo ($user['is_active'] ?? 0) == 1 ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo ($user['is_active'] ?? 0) == 0 ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                        
                        <div class="form-group full-width">
                            <label for="address">Address</label>
                            <textarea id="address" name="address" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update User
                        </button>
                        <a href="users.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
            
            <?php else: ?>
            
            <!-- Users Table -->
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Full Name</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                        <tr>
                            <td>#<?php echo $u['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($u['username']); ?></strong>
                                <?php if ($u['id'] == $_SESSION['user_id']): ?>
                                <span class="badge badge-primary">You</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($u['email']); ?></td>
                            <td><?php echo htmlspecialchars($u['full_name'] ?: '-'); ?></td>
                            <td>
                                <span class="role-badge role-<?php echo $u['role']; ?>">
                                    <?php echo ucfirst($u['role']); ?>
                                </span>
                            </td>
                            <td>
                                <?php 
                                $statusText = ($u['is_active'] ?? 0) == 1 ? 'Active' : 'Inactive';
                                $statusClass = ($u['is_active'] ?? 0) == 1 ? 'active' : 'inactive';
                                ?>
                                <span class="status-badge status-<?php echo $statusClass; ?>">
                                    <?php echo $statusText; ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($u['created_at'])); ?></td>
                            <td class="actions">
                                <a href="?action=edit&id=<?php echo $u['id']; ?>" 
                                   class="btn-icon edit" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                <a href="?action=delete&id=<?php echo $u['id']; ?>" 
                                   class="btn-icon delete" 
                                   onclick="return confirm('Delete this user?')" 
                                   title="Delete">
                                    <i class="fas fa-trash"></i>
                                </a>
                                <?php endif; ?>
                                <a href="../pages/profile.php?id=<?php echo $u['id']; ?>" 
                                   class="btn-icon view" target="_blank" title="View">
                                    <i class="fas fa-eye"></i>
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
    
    <script>
    // Confirm delete
    document.querySelectorAll('.delete').forEach(link => {
        link.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this user?')) {
                e.preventDefault();
            }
        });
    });
    </script>
</body>
</html>