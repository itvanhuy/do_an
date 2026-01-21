<?php
// File: pages/news.php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/database.php';

Auth::autoLogin();
$db = Database::getInstance();

// Lấy bài viết mới nhất làm Featured
$stmt = $db->query("SELECT * FROM posts WHERE status = 'published' ORDER BY created_at DESC LIMIT 1");
$featuredPost = $stmt->fetch(PDO::FETCH_ASSOC);

// Lấy các bài viết còn lại
$excludeId = $featuredPost ? $featuredPost['id'] : 0;
$stmt = $db->prepare("SELECT * FROM posts WHERE status = 'published' AND id != ? ORDER BY created_at DESC");
$stmt->execute([$excludeId]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News & Blog - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/news.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main class="news-container">
        <h1 class="page-title">Latest News</h1>

        <?php if ($featuredPost): ?>
        <!-- Featured Post -->
        <article class="featured-post">
            <div class="featured-image">
                <img src="../img/<?php echo htmlspecialchars($featuredPost['image']); ?>" 
                     alt="<?php echo htmlspecialchars($featuredPost['title']); ?>"
                     onerror="this.src='../img/background/baiviet1.png'">
            </div>
            <div class="featured-content">
                <div class="post-meta">
                    <span><i class="far fa-calendar-alt"></i> <?php echo date('M d, Y', strtotime($featuredPost['created_at'])); ?></span>
                    <span><i class="far fa-eye"></i> <?php echo $featuredPost['views']; ?> views</span>
                </div>
                <h2><?php echo htmlspecialchars($featuredPost['title']); ?></h2>
                <p><?php echo htmlspecialchars($featuredPost['excerpt']); ?></p>
                <a href="news-detail.php?id=<?php echo $featuredPost['id']; ?>" class="read-more">Read Article <i class="fas fa-arrow-right"></i></a>
            </div>
        </article>
        <?php endif; ?>

        <!-- News Grid -->
        <div class="news-grid">
            <?php foreach ($posts as $post): ?>
            <article class="news-card">
                <div class="card-image">
                    <img src="../img/<?php echo htmlspecialchars($post['image']); ?>" 
                         alt="<?php echo htmlspecialchars($post['title']); ?>"
                         onerror="this.src='../img/background/baiviet2.png'">
                </div>
                <div class="card-content">
                    <div class="post-meta">
                        <span><i class="far fa-calendar-alt"></i> <?php echo date('M d, Y', strtotime($post['created_at'])); ?></span>
                    </div>
                    <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                    <p><?php echo htmlspecialchars(mb_strimwidth($post['excerpt'], 0, 100, '...')); ?></p>
                    <a href="news-detail.php?id=<?php echo $post['id']; ?>" class="read-more">Read More</a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <?php if (empty($featuredPost) && empty($posts)): ?>
            <div style="text-align: center; padding: 50px; color: #666;">
                <h2>No news available at the moment.</h2>
            </div>
        <?php endif; ?>
    </main>

    <?php include '../includes/footer.php'; ?>
</body>
</html>