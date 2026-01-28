<?php
// File: pages/tournament.php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/database.php';

// Tự động đăng nhập nếu có remember token
Auth::autoLogin();

$isLoggedIn = Auth::isLoggedIn();
$username = $isLoggedIn ? $_SESSION['username'] : '';
$db = Database::getInstance();

// Lấy tham số filter game từ URL
$gameFilter = $_GET['game'] ?? '';
$tournamentFilter = $_GET['tournament'] ?? '';

// Lấy danh sách giải đấu để tạo filter dropdown
$tournamentsQuery = "SELECT DISTINCT tournament_name FROM matches WHERE tournament_name IS NOT NULL AND tournament_name != ''";
$tournamentsParams = [];
if ($gameFilter) {
    $tournamentsQuery .= " AND game_type = ?";
    $tournamentsParams[] = $gameFilter;
}
$tournamentsQuery .= " ORDER BY tournament_name DESC";
try {
    $stmt = $db->prepare($tournamentsQuery);
    $stmt->execute($tournamentsParams);
    $tournamentList = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    $tournamentList = [];
}

// 1. Lấy danh sách trận đấu
$query = "SELECT * FROM matches WHERE 1=1";
$params = [];

if ($gameFilter) {
    $query .= " AND game_type = ?";
    $params[] = $gameFilter;
}
if ($tournamentFilter) {
    $query .= " AND tournament_name = ?";
    $params[] = $tournamentFilter;
}

// Sắp xếp theo thời gian (cũ nhất đến mới nhất để xem lịch trình)
$query .= " ORDER BY match_time ASC";

try {
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $allMatches = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Fallback: Nếu lỗi (ví dụ thiếu cột tournament_name), chạy query không lọc tournament
    $query = "SELECT * FROM matches WHERE 1=1";
    $params = [];
    if ($gameFilter) { $query .= " AND game_type = ?"; $params[] = $gameFilter; }
    $query .= " ORDER BY match_time ASC";
    
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $allMatches = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Phân nhóm matches theo ngày để hiển thị đẹp hơn
$groupedMatches = [];
foreach ($allMatches as $match) {
    $date = date('Y-m-d', strtotime($match['match_time']));
    $groupedMatches[$date][] = $match;
}

// 2. Lấy bảng xếp hạng (Right Sidebar)
$rankingQuery = "SELECT * FROM team_rankings WHERE 1=1";
$rankingParams = [];

if ($gameFilter) {
    $rankingQuery .= " AND game_type = ?";
    $rankingParams[] = $gameFilter;
} else {
    // Mặc định lấy LOL nếu không chọn game (hoặc game phổ biến nhất)
    $rankingQuery .= " AND game_type = 'lol'";
}
if ($tournamentFilter) {
    $rankingQuery .= " AND tournament_name = ?";
    $rankingParams[] = $tournamentFilter;
}

// Thử truy vấn với cột tournament_name (nếu đã cập nhật DB)
try {
    $fullQuery = $rankingQuery . " ORDER BY tournament_name DESC, rank_position ASC";
    $stmt = $db->prepare($fullQuery);
    $stmt->execute($rankingParams);
    $rankings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Nếu lỗi "Column not found" (1054), fallback về query cũ
    if ($e->errorInfo[1] == 1054) {
        // Xây dựng lại query từ đầu, bỏ qua tournament_name để tránh lỗi WHERE clause
        $fallbackQuery = "SELECT * FROM team_rankings WHERE 1=1";
        $fallbackParams = [];
        
        if ($gameFilter) {
            $fallbackQuery .= " AND game_type = ?";
            $fallbackParams[] = $gameFilter;
        } else {
            $fallbackQuery .= " AND game_type = 'lol'";
        }
        $fallbackQuery .= " ORDER BY rank_position ASC";
        
        $stmt = $db->prepare($fallbackQuery);
        $stmt->execute($fallbackParams);
        $rankings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        throw $e; // Ném lỗi khác nếu không phải lỗi thiếu cột
    }
}

// Nhóm rankings theo giải đấu
$groupedRankings = [];
foreach ($rankings as $r) {
    $tName = isset($r['tournament_name']) && $r['tournament_name'] ? $r['tournament_name'] : 'General Standings';
    $groupedRankings[$tName][] = $r;
}

// 3. Lấy tin tức giải đấu mới nhất (Right Sidebar)
// Chỉ lấy bài viết có post_type là 'tournament'
try {
    $stmt = $db->prepare("SELECT * FROM posts WHERE status = 'published' AND post_type = 'tournament' ORDER BY created_at DESC LIMIT 5");
    $stmt->execute();
    $sideNews = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Nếu chưa có cột post_type (lỗi 1054), fallback về query cũ (lấy tất cả tin tức)
    if (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1054) {
        $stmt = $db->query("SELECT * FROM posts WHERE status = 'published' ORDER BY created_at DESC LIMIT 5");
        $sideNews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        throw $e; // Ném lỗi khác nếu không phải lỗi thiếu cột
    }
}

// 4. Lấy danh sách đội tuyển (Teams)
$teamsQuery = "SELECT * FROM teams WHERE 1=1";
$teamsParams = [];

if ($gameFilter) {
    $teamsQuery .= " AND game_type = ?";
    $teamsParams[] = $gameFilter;
}

if ($tournamentFilter) {
    // Thử lọc theo cột tournament_name trong bảng teams hoặc quan hệ cũ
    $teamsQuery .= " AND (tournament_name = ? OR name IN (
        SELECT team_name FROM team_rankings WHERE tournament_name = ?
        UNION
        SELECT team1_name FROM matches WHERE tournament_name = ?
        UNION
        SELECT team2_name FROM matches WHERE tournament_name = ?
    ))";
    $teamsParams[] = $tournamentFilter; // cho teams.tournament_name
    $teamsParams[] = $tournamentFilter; // cho rankings
    $teamsParams[] = $tournamentFilter; // cho matches team1
    $teamsParams[] = $tournamentFilter; // cho matches team2
}

$teamsQuery .= " ORDER BY name ASC";

try {
    $stmt = $db->prepare($teamsQuery);
    $stmt->execute($teamsParams);
    $teamsList = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Nếu lỗi thiếu cột (1054), fallback về query cũ
    if (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1054) {
        $fallbackQuery = "SELECT * FROM teams WHERE 1=1";
        $fallbackParams = [];
        if ($gameFilter) {
            $fallbackQuery .= " AND game_type = ?";
            $fallbackParams[] = $gameFilter;
        }
        if ($tournamentFilter) {
            $fallbackQuery .= " AND name IN (
                SELECT team_name FROM team_rankings WHERE tournament_name = ?
                UNION
                SELECT team1_name FROM matches WHERE tournament_name = ?
                UNION
                SELECT team2_name FROM matches WHERE tournament_name = ?
            )";
            $fallbackParams[] = $tournamentFilter;
            $fallbackParams[] = $tournamentFilter;
            $fallbackParams[] = $tournamentFilter;
        }
        $fallbackQuery .= " ORDER BY name ASC";
        
        try {
            $stmt = $db->prepare($fallbackQuery);
            $stmt->execute($fallbackParams);
            $teamsList = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            // Fallback cấp 2: Nếu vẫn lỗi (do bảng liên quan thiếu cột), hiển thị tất cả team theo game
            $fallbackQuery = "SELECT * FROM teams WHERE 1=1";
            $fallbackParams = [];
            if ($gameFilter) { $fallbackQuery .= " AND game_type = ?"; $fallbackParams[] = $gameFilter; }
            $fallbackQuery .= " ORDER BY name ASC";
            
            $stmt = $db->prepare($fallbackQuery);
            $stmt->execute($fallbackParams);
            $teamsList = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } else {
        $teamsList = [];
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tournament - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/tournament.css">
</head>
<body class="bw-theme">
    <?php include '../includes/header.php'; ?>

    <div class="main-tournament-schedule">
        <!-- Left Sidebar -->
        <aside class="sidebar left-sidebar">
            <h3><i class="fas fa-trophy"></i> Games</h3>
            <ul>
                <li><a href="tournament.php" class="<?php echo $gameFilter == '' ? 'active' : ''; ?>"><i class="fas fa-gamepad"></i> All Games</a></li>
                <li><a href="?game=lol" class="<?php echo $gameFilter == 'lol' ? 'active' : ''; ?>"><i class="fas fa-gamepad"></i> League of Legends</a></li>
                <li><a href="?game=csgo" class="<?php echo $gameFilter == 'csgo' ? 'active' : ''; ?>"><i class="fas fa-gamepad"></i> CS:GO</a></li>
                <li><a href="?game=valorant" class="<?php echo $gameFilter == 'valorant' ? 'active' : ''; ?>"><i class="fas fa-gamepad"></i> Valorant</a></li>
                <li><a href="?game=dota2" class="<?php echo $gameFilter == 'dota2' ? 'active' : ''; ?>"><i class="fas fa-gamepad"></i> Dota 2</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="schedule-content">
            <div class="tournament-section">
                <h2><i class="fas fa-calendar-alt"></i> Match Schedule <?php echo $gameFilter ? '- ' . strtoupper($gameFilter) : ''; ?></h2>
                
                <!-- Tournament Filter -->
                <div class="filter-container" style="margin-bottom: 20px;">
                    <form method="GET" action="tournament.php">
                        <?php if($gameFilter): ?><input type="hidden" name="game" value="<?php echo htmlspecialchars($gameFilter); ?>"><?php endif; ?>
                        <select name="tournament" onchange="this.form.submit()" class="tournament-select">
                            <option value="">-- All Tournaments --</option>
                            <?php foreach ($tournamentList as $t): ?>
                                <option value="<?php echo htmlspecialchars($t); ?>" <?php echo $tournamentFilter === $t ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($t); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>

                <?php if (empty($groupedMatches)): ?>
                    <div class="no-matches">No matches found.</div>
                <?php else: ?>
                <?php foreach ($groupedMatches as $date => $matches): 
                    $dayLabel = date('l, M d', strtotime($date));
                    if ($date == date('Y-m-d')) $dayLabel = 'Today, ' . date('M d');
                    elseif ($date == date('Y-m-d', strtotime('+1 day'))) $dayLabel = 'Tomorrow, ' . date('M d');
                ?>
                <div class="match-day">
                    <h3><?php echo $dayLabel; ?></h3>
                    <ul class="matches">
                        <?php foreach ($matches as $m): ?>
                        <li class="match-item <?php echo $m['status'] == 'live' ? 'live' : ''; ?>">
                            <div class="team team1">
                                <span><?php echo htmlspecialchars($m['team1_name']); ?></span>
                                <img src="../img/teams/<?php echo htmlspecialchars($m['team1_logo']); ?>" onerror="this.src='../img/product/default.jpg'" alt="<?php echo htmlspecialchars($m['team1_name']); ?>">
                            </div>
                            <div class="match-details">
                                <?php if ($m['status'] == 'live'): ?>
                                    <span class="match-status live">LIVE</span>
                                    <span class="score"><?php echo $m['score_team1'] . ' - ' . $m['score_team2']; ?></span>
                                <?php elseif ($m['status'] == 'finished'): ?>
                                    <span class="match-status finished">Finished</span>
                                    <span class="score"><?php echo $m['score_team1'] . ' - ' . $m['score_team2']; ?></span>
                                <?php else: ?>
                                    <span class="match-time"><?php echo date('H:i', strtotime($m['match_time'])); ?></span>
                                    <span class="match-status upcoming">Upcoming</span>
                                <?php endif; ?>
                                <span class="vs"><?php echo isset($m['tournament_name']) ? htmlspecialchars($m['tournament_name']) : strtoupper($m['game_type']); ?></span>
                            </div>
                            <div class="team team2">
                                <img src="../img/teams/<?php echo htmlspecialchars($m['team2_logo']); ?>" onerror="this.src='../img/product/default.jpg'" alt="<?php echo htmlspecialchars($m['team2_name']); ?>">
                                <span><?php echo htmlspecialchars($m['team2_name']); ?></span>
                            </div>
                            <?php if ($m['status'] == 'live' && !empty($m['stream_link'])): ?>
                                <a href="<?php echo htmlspecialchars($m['stream_link']); ?>" target="_blank" class="watch-link"><i class="fab fa-twitch"></i> Watch</a>
                            <?php endif; ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Teams Section -->
            <div class="tournament-section">
                <h2><i class="fas fa-users"></i> Participating Teams <?php echo $tournamentFilter ? '- ' . htmlspecialchars($tournamentFilter) : ''; ?></h2>
                <?php if (empty($teamsList)): ?>
                    <div class="no-matches">No teams found.</div>
                <?php else: ?>
                    <div class="teams-grid">
                        <?php foreach ($teamsList as $team): ?>
                        <div class="team-card">
                            <div class="team-logo-wrapper">
                                <img src="../img/teams/<?php echo htmlspecialchars($team['logo']); ?>" onerror="this.src='../img/product/default.jpg'" alt="<?php echo htmlspecialchars($team['name']); ?>">
                            </div>
                            <h4><?php echo htmlspecialchars($team['name']); ?></h4>
                            <span class="game-badge"><?php echo strtoupper($team['game_type']); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>

        <!-- Right Sidebar -->
        <aside class="sidebar right-sidebar">
            <?php if (empty($groupedRankings)): ?>
                <h3><i class="fas fa-chart-bar"></i> Standings</h3>
                <p style="text-align:center; padding:10px; color:#70707a;">No rankings available.</p>
            <?php else: ?>
                <?php foreach ($groupedRankings as $tournamentName => $teams): ?>
                    <h3><i class="fas fa-chart-bar"></i> <?php echo htmlspecialchars($tournamentName); ?></h3>
                    <table class="ranking-table" style="margin-bottom: 30px;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Team</th>
                                <th style="text-align: right;">W-L</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($teams as $team): ?>
                            <tr>
                                <td class="rank-num"><?php echo $team['rank_position']; ?></td>
                                <td>
                                    <div class="team-cell">
                                        <img src="../img/teams/<?php echo htmlspecialchars($team['team_logo']); ?>" onerror="this.src='../img/product/default.jpg'" alt="<?php echo htmlspecialchars($team['team_name']); ?>">
                                        <span><?php echo htmlspecialchars($team['team_name']); ?></span>
                                    </div>
                                </td>
                                <td class="wl-cell"><?php echo $team['wins'] . '-' . $team['losses']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endforeach; ?>
            <?php endif; ?>

            <h3 style="margin-top: 30px;"><i class="fas fa-bolt"></i> Latest News</h3>
            <ul class="side-news-list">
                <?php foreach ($sideNews as $news): ?>
                <li class="side-news-item">
                    <a href="news-detail.php?id=<?php echo $news['id']; ?>">
                        <span class="side-news-title"><?php echo htmlspecialchars($news['title']); ?></span>
                        <span class="side-news-time"><i class="far fa-clock"></i> <?php echo date('M d', strtotime($news['created_at'])); ?></span>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </aside>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>