<?php
// File: pages/tournament.php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/database.php';

// Tự động đăng nhập nếu có remember token
Auth::autoLogin();

$db = Database::getInstance();

// Lấy tất cả các trận đấu sắp xếp theo thời gian
$stmt = $db->query("SELECT * FROM matches ORDER BY match_time ASC");
$allMatches = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Nhóm trận đấu theo game_type và ngày
$matchesByGame = [];
foreach ($allMatches as $match) {
    $game = $match['game_type'];
    $date = date('l, F d, Y', strtotime($match['match_time']));
    
    if (!isset($matchesByGame[$game])) {
        $matchesByGame[$game] = [];
    }
    if (!isset($matchesByGame[$game][$date])) {
        $matchesByGame[$game][$date] = [];
    }
    $matchesByGame[$game][$date][] = $match;
}

// Danh sách các game có sẵn để hiển thị
$games = ['valorant' => 'VALORANT', 'dota' => 'DOTA 2', 'csgo' => 'CS:GO', 'lol' => 'League of Legends'];

// Lấy dữ liệu Ranking từ database
$stmt = $db->query("SELECT * FROM team_rankings ORDER BY game_type ASC, rank_position ASC");
$allRankings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Nhóm ranking theo game
$rankingsByGame = [];
foreach ($allRankings as $rank) {
    $rankingsByGame[$rank['game_type']][] = $rank;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Esports Schedule - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css"
    integrity="sha512-NhSC1YmyruXifcj/KFRWoC561YpHpc5Jtzgvbuzx5VozKpWvQ+4nXhPdFgmx8xqexRcpAglTj9sIBWINXa8x5w=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/tournament.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
      <script src="../js/tournament.js"></script>
</head>
<body class="bw-theme">
    <?php include '../includes/header.php'; ?>

     <main class="main-tournament-schedule">
        <div class="sidebar left-sidebar">
            <h3><i class="fas fa-list-ul"></i> Tournaments</h3>
            <ul class="tournament-categories">
                <li><a href="#" class="filter-btn active" data-filter="all">All Tournaments</a></li>
                <?php foreach ($games as $key => $label): ?>
                    <li><a href="#" class="filter-btn" data-filter="<?php echo $key; ?>"><?php echo $label; ?></a></li>
                <?php endforeach; ?>
            </ul>
            <h3 style="margin-top: 30px;"><i class="fas fa-cog"></i> Ranking</h3>
            <ul>
                <li><a href="#" class="filter-btn" data-filter="rankings_only">Team Rankings</a></li>
            </ul>
        </div>

        <div class="schedule-content">
            
            <div class="rankings-section">
                <!-- Dynamic Rankings from Database -->
                <h2><i class="fas fa-trophy"></i> Global Team Rankings</h2>
                
                <?php foreach ($games as $gameKey => $gameName): ?>
                    <?php if (isset($rankingsByGame[$gameKey]) && count($rankingsByGame[$gameKey]) > 0): ?>
                        <h3><?php echo $gameName; ?></h3>
                        <ul class="ranking-list-main">
                            <?php foreach ($rankingsByGame[$gameKey] as $team): ?>
                                <li class="ranking-item-main">
                                    <span class="rank"><?php echo $team['rank_position']; ?></span>
                                    <img class="team-logo" src="<?php echo !empty($team['team_logo']) ? '../img/teams/'.$team['team_logo'] : '../img/product/default.jpg'; ?>" alt="<?php echo htmlspecialchars($team['team_name']); ?>">
                                    <span class="team-name"><?php echo htmlspecialchars($team['team_name']); ?></span>
                                    <span class="team-game" style="font-size: 0.8em; color: #888; margin-left: auto;">W:<?php echo $team['wins']; ?> L:<?php echo $team['losses']; ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                <?php endforeach; ?>
                
                <?php if (empty($rankingsByGame)): ?>
                    <p style="color: #888; font-style: italic; padding: 10px;">No rankings available yet.</p>
                <?php endif; ?>
            </div>

            <!-- Dynamic Match Schedule -->
            <?php foreach ($matchesByGame as $gameType => $dates): ?>
            <div class="tournament-section" data-id="<?php echo $gameType; ?>">
                <h2><i class="far fa-calendar-alt"></i> Match Schedule - <?php echo $games[$gameType] ?? strtoupper($gameType); ?></h2>
                
                <?php foreach ($dates as $date => $matches): ?>
                <div class="match-day">
                    <h3><i class="fas fa-calendar-day"></i> <?php echo $date; ?></h3>
                    <ul class="matches">
                        <?php foreach ($matches as $match): ?>
                        <li class="match-item <?php echo $match['status']; ?>">
                            <div class="team team1">
                                <img src="<?php echo !empty($match['team1_logo']) ? '../img/teams/'.$match['team1_logo'] : '../img/product/default.jpg'; ?>" alt="<?php echo htmlspecialchars($match['team1_name']); ?>"> 
                                <span><?php echo htmlspecialchars($match['team1_name']); ?></span>
                            </div>
                            
                            <div class="match-details">
                                <?php if ($match['status'] == 'live'): ?>
                                    <span class="match-status live">LIVE</span>
                                <?php else: ?>
                                    <span class="match-time"><?php echo date('H:i', strtotime($match['match_time'])); ?></span>
                                <?php endif; ?>
                                
                                <span class="vs">VS</span>
                                
                                <?php if ($match['status'] != 'upcoming'): ?>
                                    <span class="score"><?php echo $match['score_team1']; ?> - <?php echo $match['score_team2']; ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="team team2">
                                <span><?php echo htmlspecialchars($match['team2_name']); ?></span>
                                <img src="<?php echo !empty($match['team2_logo']) ? '../img/teams/'.$match['team2_logo'] : '../img/product/default.jpg'; ?>" alt="<?php echo htmlspecialchars($match['team2_name']); ?>"> 
                            </div>
                            
                            <?php if (!empty($match['stream_link'])): ?>
                            <a href="<?php echo htmlspecialchars($match['stream_link']); ?>" class="watch-link" target="_blank">
                                <i class="fas fa-play-circle"></i> Watch
                            </a>
                            <?php endif; ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endforeach; ?>
            
            <?php if (empty($matchesByGame)): ?>
                <div class="no-matches">
                    <p>No upcoming matches scheduled.</p>
                </div>
            <?php endif; ?>
        </div>
        <div class="sidebar right-sidebar">
            <h3><i class="fas fa-medal"></i> Rankings</h3>
         <ul class="ranking-list">
                <li><a href="#">1. <img src="https://cdn-media.sforum.vn/storage/app/media/wp-content/uploads/2021/07/lol-t1-1.jpg" alt="T1" style="height:18px; vertical-align:middle; background-color:#3a3a3e; padding:1px; border-radius:3px;"> T1</a></li>
                <li><a href="#">2. <img src="https://images.seeklogo.com/logo-png/40/1/gen-g-logo-png_seeklogo-400443.png" alt="Gen.G" style="height:18px; vertical-align:middle; background-color:#3a3a3e; padding:1px; border-radius:3px;">
 Gen.G</a></li>
                <li><a href="#">3. <img src="https://scontent.fsgn2-10.fna.fbcdn.net/v/t39.30808-6/491810406_1098416458991736_8932614531938378371_n.jpg?_nc_cat=1&ccb=1-7&_nc_sid=6ee11a&_nc_ohc=hH7Cnq1GNnQQ7kNvwF26Gll&_nc_oc=Adll6FVw8v9d28rCvCP0kANbjhYljAS-WnzSgOU_a2nplasHY4E4csNk4WlWqh393bs&_nc_zt=23&_nc_ht=scontent.fsgn2-10.fna&_nc_gid=S_WVXovqdk27a7_yZW_UHA&oh=00_AfNkHKWNpo7Jq3BRVbgGIHpMJyxcuF25vlbm3b_FnT0DQA&oe=68535E8D" alt="GAM" style="height:18px; vertical-align:middle; background-color: #3a3a3e; padding:1px; border-radius:3px;"> GAM Esports</a></li>
            </ul>
            
            <h3 style="margin-top: 30px;"><i class="far fa-newspaper"></i> Featured News</h3>
            <ul class="news-list">
                <li><a href="#">MSI 2025 Finals: [Team A] becomes champion!</a></li>
                <li><a href="#">[Player X] wins MVP of the week.</a></li>
                <li><a href="#">Worlds 2025 schedule announced.</a></li>
                <li><a href="https://gamek.vn/esport.chn" class="watch-link"><i class="fas fa-play-circle"></i> Read more news</a></li>
            </ul>
            
            <h3 style="margin-top: 30px;"><i class="fas fa-video"></i> Live Streams</h3>
            <ul class="stream-list">
                <li><a href="https://www.twitch.tv/lec" target="_blank"><i class="fab fa-twitch"></i> Twitch Channel </a></li>
                <li><a href="https://www.facebook.com/ViRiuu2508"target="_blank"><i class="fab fa-facebook"></i> Facebook </a></li>
                <li><a href="#">More channels...</a></li>
            </ul>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>

    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>