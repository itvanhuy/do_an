<?php
// File: pages/news-detail.php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/database.php';

Auth::autoLogin();
$db = Database::getInstance();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Lấy bài viết
$stmt = $db->prepare("SELECT * FROM posts WHERE id = ? AND status = 'published'");
$stmt->execute([$id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    header('Location: news.php');
    exit();
}

// Tăng lượt xem
$db->prepare("UPDATE posts SET views = views + 1 WHERE id = ?")->execute([$id]);

// Bài viết liên quan
$stmt = $db->prepare("SELECT * FROM posts WHERE status = 'published' AND id != ? ORDER BY created_at DESC LIMIT 3");
$stmt->execute([$id]);
$relatedPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy comments
$comments = [];
try {
    $stmt = $db->prepare("
        SELECT c.*, u.username, u.full_name 
        FROM comments c 
        JOIN users u ON c.user_id = u.id 
        WHERE c.post_id = ? 
        ORDER BY c.created_at DESC
    ");
    $stmt->execute([$id]);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Bỏ qua lỗi nếu bảng chưa tồn tại
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?> - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/news.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .post-detail-container { max-width: 800px; margin: 0 auto; padding: 40px 20px; }
        .post-header { text-align: center; margin-bottom: 40px; }
        .post-header h1 { font-size: 2.5rem; margin-bottom: 15px; color: #222; }
        .post-main-image { width: 100%; height: auto; border-radius: 12px; margin-bottom: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .post-content { font-size: 1.1rem; line-height: 1.8; color: #333; margin-bottom: 50px; }
        .related-section { margin-top: 60px; border-top: 1px solid #eee; padding-top: 40px; }
        .related-section h3 { font-size: 1.8rem; margin-bottom: 25px; }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main class="post-detail-container">
        <article>
            <header class="post-header">
                <h1><?php echo htmlspecialchars($post['title']); ?></h1>
                <div class="post-meta">
                    <span><i class="far fa-calendar-alt"></i> <?php echo date('F d, Y', strtotime($post['created_at'])); ?></span>
                    <span><i class="far fa-eye"></i> <?php echo $post['views']; ?> views</span>
                </div>
            </header>

            <img src="../img/<?php echo htmlspecialchars($post['image']); ?>" 
                 alt="<?php echo htmlspecialchars($post['title']); ?>" 
                 class="post-main-image"
                 onerror="this.src='../img/background/baiviet1.png'">

            <div class="post-content">
                <?php echo nl2br(htmlspecialchars($post['content'])); ?>
            </div>
        </article>

        <!-- Comments Section -->
        <section class="comments-section">
            <h3>Comments (<?php echo count($comments); ?>)</h3>
            
            <?php if (Auth::isLoggedIn()): ?>
                <form id="commentForm" class="comment-form">
                    <input type="hidden" name="post_id" value="<?php echo $id; ?>">
                    <textarea name="content" placeholder="Write a comment..." required></textarea>
                    <button type="submit">Post Comment</button>
                </form>
            <?php else: ?>
                <p class="login-prompt">Please <a href="../login.php">login</a> to leave a comment.</p>
            <?php endif; ?>

            <div class="comments-list">
                <?php foreach ($comments as $comment): ?>
                    <div class="comment-item">
                        <div class="comment-avatar">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="comment-content">
                            <div class="comment-header">
                                <span class="author"><?php echo htmlspecialchars($comment['full_name'] ?: $comment['username']); ?></span>
                                <span class="date"><?php echo date('M d, Y H:i', strtotime($comment['created_at'])); ?></span>
                            </div>
                            <p><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <?php if (!empty($relatedPosts)): ?>
        <section class="related-section">
            <h3>Related Articles</h3>
            <div class="news-grid">
                <?php foreach ($relatedPosts as $related): ?>
                <article class="news-card">
                    <div class="card-image">
                        <img src="../img/<?php echo htmlspecialchars($related['image']); ?>" 
                             alt="<?php echo htmlspecialchars($related['title']); ?>"
                             onerror="this.src='../img/background/baiviet2.png'">
                    </div>
                    <div class="card-content">
                        <h3><?php echo htmlspecialchars($related['title']); ?></h3>
                        <a href="news-detail.php?id=<?php echo $related['id']; ?>" class="read-more">Read More</a>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
    </main>

    <?php include '../includes/footer.php'; ?>

    <script>
        document.getElementById('commentForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('../includes/submit_comment.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message);
                }
            });
        });
    </script>
</body>
</html>