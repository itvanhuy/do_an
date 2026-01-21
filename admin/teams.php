<?php
// File: admin/teams.php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/database.php';

Auth::requireAdmin();

$db = Database::getInstance();
$action = $_GET['action'] ?? 'list';
$message = '';

// Tự động tạo bảng teams nếu chưa tồn tại
try {
    $db->exec("CREATE TABLE IF NOT EXISTS teams (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        logo VARCHAR(255),
        game_type VARCHAR(50) DEFAULT 'lol',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Cập nhật thêm cột tournament_name cho bảng teams nếu chưa có
    try {
        $db->exec("ALTER TABLE teams ADD COLUMN tournament_name VARCHAR(255) AFTER game_type");
    } catch (Exception $e) {}
} catch (PDOException $e) {}

// Hàm upload logo đội tuyển
function uploadTeamLogo($fileInputName) {
    if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] === UPLOAD_ERR_NO_FILE) {
        return ''; 
    }
    $file = $_FILES[$fileInputName];
    if ($file['error'] !== UPLOAD_ERR_OK) return '';
    
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) return '';
    
    $newName = 'team_' . uniqid() . '.' . $ext;
    $uploadDir = '../img/teams/';
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
            $logo = uploadTeamLogo('logo');
            
            if ($action == 'edit' && empty($logo)) {
                $logo = $_POST['existing_logo'] ?? '';
            }

            $data = [
                $_POST['name'],
                $logo,
                $_POST['game_type'],
                $_POST['tournament_name'] ?? ''
            ];

            if ($action == 'add') {
                $stmt = $db->prepare("INSERT INTO teams (name, logo, game_type, tournament_name) VALUES (?, ?, ?, ?)");
                $stmt->execute($data);
                $message = '<div class="alert success">Team added successfully!</div>';
            } else {
                $data[] = $_GET['id'];
                $stmt = $db->prepare("UPDATE teams SET name=?, logo=?, game_type=?, tournament_name=? WHERE id=?");
                $stmt->execute($data);
                $message = '<div class="alert success">Team updated successfully!</div>';
            }
        } catch (PDOException $e) {
            $message = '<div class="alert error">Error: ' . $e->getMessage() . '</div>';
        }
    }
}

if ($action == 'delete' && isset($_GET['id'])) {
    try {
        $stmt = $db->prepare("DELETE FROM teams WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $message = '<div class="alert success">Team deleted successfully!</div>';
        $action = 'list';
    } catch (PDOException $e) {
        $message = '<div class="alert error">Error: ' . $e->getMessage() . '</div>';
    }
}

// Lấy dữ liệu cho Edit
$team = [];
if ($action == 'edit' && isset($_GET['id'])) {
    $stmt = $db->prepare("SELECT * FROM teams WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $team = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Lấy danh sách teams
$stmt = $db->query("SELECT * FROM teams ORDER BY name ASC");
$teams = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teams Management - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        <div class="main-content">
            <?php include 'includes/header.php'; ?>
            
            <div class="content-header">
                <h2>Teams Management</h2>
                <?php if ($action == 'list'): ?>
                <a href="?action=add" class="btn btn-primary"><i class="fas fa-plus"></i> Add Team</a>
                <?php endif; ?>
            </div>
            
            <?php echo $message; ?>

            <?php if ($action == 'add' || $action == 'edit'): ?>
            <div class="form-container">
                <form method="POST" enctype="multipart/form-data">
                    <?php if ($action == 'edit'): ?>
                        <input type="hidden" name="existing_logo" value="<?php echo $team['logo']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Team Name</label>
                            <input type="text" name="name" value="<?php echo $team['name'] ?? ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Game Type</label>
                            <select name="game_type">
                                <option value="lol" <?php echo ($team['game_type'] ?? '') == 'lol' ? 'selected' : ''; ?>>League of Legends</option>
                                <option value="csgo" <?php echo ($team['game_type'] ?? '') == 'csgo' ? 'selected' : ''; ?>>CS:GO</option>
                                <option value="valorant" <?php echo ($team['game_type'] ?? '') == 'valorant' ? 'selected' : ''; ?>>Valorant</option>
                                <option value="dota2" <?php echo ($team['game_type'] ?? '') == 'dota2' ? 'selected' : ''; ?>>Dota 2</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Tournament Name</label>
                            <input type="text" name="tournament_name" value="<?php echo $team['tournament_name'] ?? ''; ?>" placeholder="e.g. Worlds 2023">
                        </div>
                        <div class="form-group">
                            <label>Logo</label>
                            <input type="file" name="logo">
                            <?php if (!empty($team['logo'])): ?>
                                <div style="margin-top: 10px;">
                                    <img src="../img/teams/<?php echo $team['logo']; ?>" style="height: 50px; object-fit: contain;">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Save Team</button>
                        <a href="teams.php" class="btn btn-secondary">Cancel</a>
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
                            <th>Game</th>
                            <th>Tournament</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($teams as $t): ?>
                        <tr>
                            <td>#<?php echo $t['id']; ?></td>
                            <td>
                                <?php if(!empty($t['logo'])): ?>
                                    <img src="../img/teams/<?php echo $t['logo']; ?>" style="width:40px; height:40px; object-fit:contain; background: #f0f0f0; border-radius: 4px; padding: 2px;">
                                <?php else: ?>
                                    <span style="color: #ccc;">No Logo</span>
                                <?php endif; ?>
                            </td>
                            <td><strong><?php echo htmlspecialchars($t['name']); ?></strong></td>
                            <td><span class="badge"><?php echo strtoupper($t['game_type']); ?></span></td>
                            <td><?php echo htmlspecialchars($t['tournament_name'] ?? 'N/A'); ?></td>
                            <td><?php echo date('M d, Y', strtotime($t['created_at'])); ?></td>
                            <td class="actions">
                                <a href="?action=edit&id=<?php echo $t['id']; ?>" class="btn-icon edit"><i class="fas fa-edit"></i></a>
                                <a href="?action=delete&id=<?php echo $t['id']; ?>" class="btn-icon delete" onclick="return confirm('Delete this team?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($teams)): ?>
                            <tr><td colspan="6" style="text-align:center; padding: 20px;">No teams found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>