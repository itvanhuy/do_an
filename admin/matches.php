<?php
// File: admin/matches.php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/database.php';

Auth::requireAdmin();

$db = Database::getInstance();
$action = $_GET['action'] ?? 'list';
$message = '';

// Hàm upload ảnh đội tuyển
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
            $team1_logo = uploadTeamLogo('team1_logo');
            $team2_logo = uploadTeamLogo('team2_logo');
            
            // Giữ logo cũ nếu không upload mới khi edit
            if ($action == 'edit') {
                if (empty($team1_logo)) $team1_logo = $_POST['existing_team1_logo'] ?? '';
                if (empty($team2_logo)) $team2_logo = $_POST['existing_team2_logo'] ?? '';
            }

            $data = [
                $_POST['game_type'],
                $_POST['team1_name'],
                $team1_logo,
                $_POST['team2_name'],
                $team2_logo,
                $_POST['match_time'],
                $_POST['status'],
                $_POST['score_team1'] ?? 0,
                $_POST['score_team2'] ?? 0,
                $_POST['stream_link'] ?? ''
            ];

            if ($action == 'add') {
                $stmt = $db->prepare("INSERT INTO matches (game_type, team1_name, team1_logo, team2_name, team2_logo, match_time, status, score_team1, score_team2, stream_link) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute($data);
                $message = '<div class="alert success">Match added successfully!</div>';
            } else {
                $data[] = $_GET['id'];
                $stmt = $db->prepare("UPDATE matches SET game_type=?, team1_name=?, team1_logo=?, team2_name=?, team2_logo=?, match_time=?, status=?, score_team1=?, score_team2=?, stream_link=? WHERE id=?");
                $stmt->execute($data);
                $message = '<div class="alert success">Match updated successfully!</div>';
            }
        } catch (PDOException $e) {
            $message = '<div class="alert error">Error: ' . $e->getMessage() . '</div>';
        }
    }
}

if ($action == 'delete' && isset($_GET['id'])) {
    $stmt = $db->prepare("DELETE FROM matches WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $message = '<div class="alert success">Match deleted successfully!</div>';
    $action = 'list';
}

// Lấy dữ liệu cho Edit
$match = [];
if ($action == 'edit' && isset($_GET['id'])) {
    $stmt = $db->prepare("SELECT * FROM matches WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $match = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Lấy danh sách matches
$stmt = $db->query("SELECT * FROM matches ORDER BY match_time DESC");
$matches = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matches Management - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        <div class="main-content">
            <?php include 'includes/header.php'; ?>
            
            <div class="content-header">
                <h2>Matches Management</h2>
                <a href="?action=add" class="btn btn-primary"><i class="fas fa-plus"></i> Add Match</a>
            </div>
            
            <?php echo $message; ?>

            <?php if ($action == 'add' || $action == 'edit'): ?>
            <div class="form-container">
                <form method="POST" enctype="multipart/form-data">
                    <?php if ($action == 'edit'): ?>
                        <input type="hidden" name="existing_team1_logo" value="<?php echo $match['team1_logo']; ?>">
                        <input type="hidden" name="existing_team2_logo" value="<?php echo $match['team2_logo']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Game Type</label>
                            <select name="game_type" required>
                                <option value="valorant" <?php echo ($match['game_type'] ?? '') == 'valorant' ? 'selected' : ''; ?>>VALORANT</option>
                                <option value="dota" <?php echo ($match['game_type'] ?? '') == 'dota' ? 'selected' : ''; ?>>DOTA 2</option>
                                <option value="csgo" <?php echo ($match['game_type'] ?? '') == 'csgo' ? 'selected' : ''; ?>>CS:GO</option>
                                <option value="lol" <?php echo ($match['game_type'] ?? '') == 'lol' ? 'selected' : ''; ?>>League of Legends</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Match Time</label>
                            <input type="datetime-local" name="match_time" value="<?php echo isset($match['match_time']) ? date('Y-m-d\TH:i', strtotime($match['match_time'])) : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status">
                                <option value="upcoming" <?php echo ($match['status'] ?? '') == 'upcoming' ? 'selected' : ''; ?>>Upcoming</option>
                                <option value="live" <?php echo ($match['status'] ?? '') == 'live' ? 'selected' : ''; ?>>Live</option>
                                <option value="finished" <?php echo ($match['status'] ?? '') == 'finished' ? 'selected' : ''; ?>>Finished</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Stream Link</label>
                            <input type="text" name="stream_link" value="<?php echo $match['stream_link'] ?? ''; ?>">
                        </div>
                        
                        <!-- Team 1 -->
                        <div class="form-group">
                            <label>Team 1 Name</label>
                            <input type="text" name="team1_name" value="<?php echo $match['team1_name'] ?? ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Team 1 Logo</label>
                            <input type="file" name="team1_logo">
                            <?php if (!empty($match['team1_logo'])): ?>
                                <img src="../img/teams/<?php echo $match['team1_logo']; ?>" height="30">
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label>Team 1 Score</label>
                            <input type="number" name="score_team1" value="<?php echo $match['score_team1'] ?? 0; ?>">
                        </div>

                        <!-- Team 2 -->
                        <div class="form-group">
                            <label>Team 2 Name</label>
                            <input type="text" name="team2_name" value="<?php echo $match['team2_name'] ?? ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Team 2 Logo</label>
                            <input type="file" name="team2_logo">
                            <?php if (!empty($match['team2_logo'])): ?>
                                <img src="../img/teams/<?php echo $match['team2_logo']; ?>" height="30">
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label>Team 2 Score</label>
                            <input type="number" name="score_team2" value="<?php echo $match['score_team2'] ?? 0; ?>">
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Save Match</button>
                        <a href="matches.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
            <?php else: ?>
            
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Game</th>
                            <th>Match</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Score</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($matches as $m): ?>
                        <tr>
                            <td><span class="badge"><?php echo strtoupper($m['game_type']); ?></span></td>
                            <td>
                                <div style="display:flex; align-items:center; gap:5px;">
                                    <?php echo htmlspecialchars($m['team1_name']); ?> 
                                    <span style="color:#888">vs</span> 
                                    <?php echo htmlspecialchars($m['team2_name']); ?>
                                </div>
                            </td>
                            <td><?php echo date('M d, H:i', strtotime($m['match_time'])); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $m['status'] == 'live' ? 'active' : ($m['status'] == 'finished' ? 'inactive' : 'pending'); ?>">
                                    <?php echo ucfirst($m['status']); ?>
                                </span>
                            </td>
                            <td><?php echo $m['score_team1'] . ' - ' . $m['score_team2']; ?></td>
                            <td class="actions">
                                <a href="?action=edit&id=<?php echo $m['id']; ?>" class="btn-icon edit"><i class="fas fa-edit"></i></a>
                                <a href="?action=delete&id=<?php echo $m['id']; ?>" class="btn-icon delete" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></a>
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