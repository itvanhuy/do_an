<?php
// File: admin/rankings.php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/database.php';

Auth::requireAdmin();

$db = Database::getInstance();
$action = $_GET['action'] ?? 'list';
$message = '';

// Hàm upload ảnh đội tuyển (Tái sử dụng logic upload)
function uploadTeamLogo($fileInputName) {
    if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] === UPLOAD_ERR_NO_FILE) {
        return ''; 
    }
    $file = $_FILES[$fileInputName];
    if ($file['error'] !== UPLOAD_ERR_OK) return '';
    
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) return '';
    
    $newName = 'rank_' . uniqid() . '.' . $ext;
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
            $team_logo = uploadTeamLogo('team_logo');
            
            if ($action == 'edit' && empty($team_logo)) {
                $team_logo = $_POST['existing_team_logo'] ?? '';
            }

            $data = [
                $_POST['team_name'],
                $team_logo,
                $_POST['game_type'],
<<<<<<< HEAD
                $_POST['tournament_name'] ?? 'General',
=======
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
                $_POST['rank_position'],
                $_POST['wins'] ?? 0,
                $_POST['losses'] ?? 0
            ];

            if ($action == 'add') {
<<<<<<< HEAD
                $stmt = $db->prepare("INSERT INTO team_rankings (team_name, team_logo, game_type, tournament_name, rank_position, wins, losses) VALUES (?, ?, ?, ?, ?, ?, ?)");
=======
                $stmt = $db->prepare("INSERT INTO team_rankings (team_name, team_logo, game_type, rank_position, wins, losses) VALUES (?, ?, ?, ?, ?, ?)");
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
                $stmt->execute($data);
                $message = '<div class="alert success">Ranking added successfully!</div>';
            } else {
                $data[] = $_GET['id'];
<<<<<<< HEAD
                $stmt = $db->prepare("UPDATE team_rankings SET team_name=?, team_logo=?, game_type=?, tournament_name=?, rank_position=?, wins=?, losses=? WHERE id=?");
=======
                $stmt = $db->prepare("UPDATE team_rankings SET team_name=?, team_logo=?, game_type=?, rank_position=?, wins=?, losses=? WHERE id=?");
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
                $stmt->execute($data);
                $message = '<div class="alert success">Ranking updated successfully!</div>';
            }
        } catch (PDOException $e) {
            $message = '<div class="alert error">Error: ' . $e->getMessage() . '</div>';
        }
    }
}

if ($action == 'delete' && isset($_GET['id'])) {
    $stmt = $db->prepare("DELETE FROM team_rankings WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $message = '<div class="alert success">Ranking deleted successfully!</div>';
    $action = 'list';
}

// Lấy dữ liệu cho Edit
$ranking = [];
if ($action == 'edit' && isset($_GET['id'])) {
    $stmt = $db->prepare("SELECT * FROM team_rankings WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $ranking = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Lấy danh sách rankings
<<<<<<< HEAD
try {
    $stmt = $db->query("SELECT * FROM team_rankings ORDER BY game_type ASC, tournament_name DESC, rank_position ASC");
    $rankings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $stmt = $db->query("SELECT * FROM team_rankings ORDER BY game_type ASC, rank_position ASC");
    $rankings = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
=======
$stmt = $db->query("SELECT * FROM team_rankings ORDER BY game_type ASC, rank_position ASC");
$rankings = $stmt->fetchAll(PDO::FETCH_ASSOC);
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rankings Management - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        <div class="main-content">
            <?php include 'includes/header.php'; ?>
            
            <div class="content-header">
                <h2>Rankings Management</h2>
                <a href="?action=add" class="btn btn-primary"><i class="fas fa-plus"></i> Add Ranking</a>
            </div>
            
            <?php echo $message; ?>

            <?php if ($action == 'add' || $action == 'edit'): ?>
            <div class="form-container">
                <form method="POST" enctype="multipart/form-data">
                    <?php if ($action == 'edit'): ?>
                        <input type="hidden" name="existing_team_logo" value="<?php echo $ranking['team_logo']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Game Type</label>
                            <select name="game_type" required>
                                <option value="valorant" <?php echo ($ranking['game_type'] ?? '') == 'valorant' ? 'selected' : ''; ?>>VALORANT</option>
                                <option value="dota" <?php echo ($ranking['game_type'] ?? '') == 'dota' ? 'selected' : ''; ?>>DOTA 2</option>
                                <option value="csgo" <?php echo ($ranking['game_type'] ?? '') == 'csgo' ? 'selected' : ''; ?>>CS:GO</option>
                                <option value="lol" <?php echo ($ranking['game_type'] ?? '') == 'lol' ? 'selected' : ''; ?>>League of Legends</option>
                            </select>
                        </div>
                        <div class="form-group">
<<<<<<< HEAD
                            <label>Tournament Name</label>
                            <input type="text" name="tournament_name" value="<?php echo $ranking['tournament_name'] ?? 'General'; ?>" required placeholder="e.g. VCS Summer 2023">
                        </div>
                        <div class="form-group">
=======
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
                            <label>Rank Position</label>
                            <input type="number" name="rank_position" value="<?php echo $ranking['rank_position'] ?? ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Team Name</label>
                            <input type="text" name="team_name" value="<?php echo $ranking['team_name'] ?? ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Team Logo</label>
                            <input type="file" name="team_logo">
                            <?php if (!empty($ranking['team_logo'])): ?>
                                <img src="../img/teams/<?php echo $ranking['team_logo']; ?>" height="30">
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label>Wins</label>
                            <input type="number" name="wins" value="<?php echo $ranking['wins'] ?? 0; ?>">
                        </div>
                        <div class="form-group">
                            <label>Losses</label>
                            <input type="number" name="losses" value="<?php echo $ranking['losses'] ?? 0; ?>">
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Save Ranking</button>
                        <a href="rankings.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
            <?php else: ?>
            
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Game</th>
<<<<<<< HEAD
                            <th>Tournament</th>
=======
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
                            <th>Rank</th>
                            <th>Team</th>
                            <th>Record (W-L)</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rankings as $r): ?>
                        <tr>
                            <td><span class="badge"><?php echo strtoupper($r['game_type']); ?></span></td>
<<<<<<< HEAD
                            <td><?php echo htmlspecialchars($r['tournament_name'] ?? 'General'); ?></td>
=======
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
                            <td>#<?php echo $r['rank_position']; ?></td>
                            <td>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <?php if(!empty($r['team_logo'])): ?>
                                        <img src="../img/teams/<?php echo $r['team_logo']; ?>" style="width:30px; height:30px; object-fit:contain;">
                                    <?php endif; ?>
                                    <?php echo htmlspecialchars($r['team_name']); ?>
                                </div>
                            </td>
                            <td><?php echo $r['wins'] . ' - ' . $r['losses']; ?></td>
                            <td class="actions">
                                <a href="?action=edit&id=<?php echo $r['id']; ?>" class="btn-icon edit"><i class="fas fa-edit"></i></a>
                                <a href="?action=delete&id=<?php echo $r['id']; ?>" class="btn-icon delete" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>