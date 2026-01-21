<?php
// File: pages/home.php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/database.php';

// ==========================================================================
// 1. KHỞI TẠO & AUTH
// ==========================================================================

// Tự động đăng nhập nếu có remember token
Auth::autoLogin();

// Lấy thông tin user nếu đã đăng nhập
$isLoggedIn = Auth::isLoggedIn();
$username = $isLoggedIn ? $_SESSION['username'] : '';

$db = Database::getInstance();

// ==========================================================================
// 2. LẤY DỮ LIỆU HIỂN THỊ
// ==========================================================================

// 2.1. Lấy 3 bài viết mới nhất (News)
$stmt = $db->query("SELECT * FROM posts WHERE status = 'published' ORDER BY created_at DESC LIMIT 3");
$latestNews = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2.2. Lấy 3 sản phẩm nổi bật (Featured Products) - Dựa trên lượt xem
$stmt = $db->query("SELECT * FROM products WHERE status = 'active' ORDER BY views DESC LIMIT 3");
$featuredProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2.3. Lấy giải đấu sắp tới gần nhất (Upcoming Tournament)
$stmt = $db->query("SELECT * FROM matches WHERE status = 'upcoming' ORDER BY match_time ASC LIMIT 1");
$upcomingMatch = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css"
    integrity="sha512-NhSC1YmyruXifcj/KFRWoC561YpHpc5Jtzgvbuzx5VozKpWvQ+4nXhPdFgmx8xqexRcpAglTj9sIBWINXa8x5w=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <title>Home - <?php echo SITE_NAME; ?></title>
  <link rel="stylesheet" href="../css/footer.css">
  <link rel="stylesheet" href="../css/header.css">
  <link rel="stylesheet" href="../css/home.css">
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
    /* Fix Responsive Grid */
    .grid {
        display: grid;
        gap: 30px;
    }
    .grid-3 {
        grid-template-columns: repeat(3, 1fr);
    }
    .grid-2 {
        grid-template-columns: repeat(2, 1fr);
    }
    
    @media (max-width: 992px) {
        .grid-3 {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (max-width: 768px) {
        .grid-3, .grid-2 {
            grid-template-columns: 1fr;
        }
        .hero-content h1 {
            font-size: 1.8rem;
        }
        .hero-buttons {
            flex-direction: column;
            gap: 15px;
        }
        .newsletter-form {
            flex-direction: column;
            gap: 10px;
        }
        .newsletter-input, .newsletter-btn {
            width: 100%;
            border-radius: 5px;
        }
    }
    
    /* Căn giữa ảnh trong card */
    .card img {
        display: block;
        margin: 0 auto 15px;
        object-fit: contain;
        width: 100%;
    }
    
    /* Tournament Styles Update */
    .tournament-banner {
        position: relative;
        height: 100%;
        min-height: 300px;
        border-radius: 10px;
        overflow: hidden;
    }
    .tournament-banner img.main-banner {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .match-teams-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        background: linear-gradient(to top, rgba(0,0,0,0.9), transparent);
        padding: 20px;
        display: flex;
        justify-content: space-around;
        align-items: center;
        color: white;
    }
    .match-teams-overlay .team img {
        width: 60px;
        height: 60px;
        object-fit: contain;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
        padding: 5px;
        margin-bottom: 5px;
    }
    .countdown-timer {
        display: flex;
        gap: 15px;
        margin: 20px 0;
    }
    .time-box {
        background: #333; color: #fff; padding: 10px; border-radius: 8px; text-align: center; min-width: 60px;
    }
    .time-box span { display: block; font-size: 20px; font-weight: bold; color: #ff6b6b; }
  </style>
</head>

<body>
  <?php include '../includes/header.php'; ?>

  <section class="hero">
    <div class="hero-slider">
      <img src="../img/slide1.png" class="active" alt="Esports Slide 1" />
      <img src="../img/anh_login.png" alt="Esports Slide 2" />
      <img src="../img/slide3.png" alt="Esports Slide 3" />
      <button class="prev-btn">&#10094;</button>
      <button class="next-btn">&#10095;</button>
    </div>
    <div class="hero-content">
      <h1>Welcome to TECHSHOP – The World of Technology & Esports</h1>
      <p>Discover high-quality gaming gear, join thrilling tournaments, and connect with the gaming community.</p>
      <div class="hero-buttons">
        <a href="shop.php" class="btn shop-btn">Shop Now</a>
        <a href="tournament.php" class="btm tour-btn">View Tournament</a>
      </div>
    </div>
  </section>

  <section class="main-content">
    <section class="featured-products enhanced">
      <h2>Featured Products</h2>
      <div class="grid grid-3">
        <?php foreach ($featuredProducts as $product): ?>
            <div class="card">
              <img src="../img/product/<?php echo htmlspecialchars($product['image']); ?>" 
                   alt="<?php echo htmlspecialchars($product['name']); ?>"
                   onerror="this.src='../img/product/default.jpg'">
              <h3><?php echo htmlspecialchars(mb_strimwidth($product['name'], 0, 25, '...')); ?></h3>
              <?php if (($product['discount'] ?? 0) > 0): ?>
                  <p style="margin-bottom: 10px;">
                      <span style="text-decoration: line-through; color: #999; font-size: 0.9em; margin-right: 5px;"><?php echo number_format($product['price'], 0, ',', '.'); ?> VND</span>
                      <span style="color: #e63946; font-weight: bold;"><?php echo number_format($product['price'] * (1 - $product['discount']/100), 0, ',', '.'); ?> VND</span>
                  </p>
              <?php else: ?>
                  <p style="color: #e63946; font-weight: bold; margin-bottom: 10px;"><?php echo number_format($product['price'], 0, ',', '.'); ?> VND</p>
              <?php endif; ?>
              <a href="product-detail.php?id=<?php echo $product['id']; ?>"><button>Buy Now</button></a>
            </div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- News & Blog -->
    <section class="news-section">
      <h2>News & Blog</h2>
      <div class="grid grid-3 dark-bg">
        <?php if (!empty($latestNews)): ?>
            <?php foreach ($latestNews as $news): ?>
            <div class="card">
              <img src="../img/<?php echo htmlspecialchars($news['image']); ?>" 
                   alt="<?php echo htmlspecialchars($news['title']); ?>"
                   onerror="this.src='../img/background/baiviet1.png'">
              <h4><?php echo htmlspecialchars($news['title']); ?></h4>
              <p><?php echo htmlspecialchars(mb_strimwidth($news['excerpt'], 0, 100, '...')); ?></p>
              <a href="news-detail.php?id=<?php echo $news['id']; ?>"><button class="more">Read More</button></a>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center; color: #ccc; width: 100%;">No news available.</p>
        <?php endif; ?>
      </div>
    </section>

    <!-- Upcoming Tournament -->
    <section class="tournament enhanced">
      <h2>Upcoming Tournament</h2>
      <?php if ($upcomingMatch): ?>
      <div class="tournament-content grid grid-2">
        <div class="tournament-banner">
            <img src="../img/background/bannergiaidau.jpg" alt="Tournament Banner" class="main-banner">
            <div class="match-teams-overlay">
                <div class="team" style="text-align: center;">
                    <img src="../img/teams/<?php echo htmlspecialchars($upcomingMatch['team1_logo'] ?? ''); ?>" onerror="this.src='../img/product/default.jpg'" alt="Team 1">
                    <div style="font-weight: bold;"><?php echo htmlspecialchars($upcomingMatch['team1_name']); ?></div>
                </div>
                <div class="vs" style="font-size: 24px; color: #ff4757; font-weight: bold; font-style: italic;">VS</div>
                <div class="team" style="text-align: center;">
                    <img src="../img/teams/<?php echo htmlspecialchars($upcomingMatch['team2_logo'] ?? ''); ?>" onerror="this.src='../img/product/default.jpg'" alt="Team 2">
                    <div style="font-weight: bold;"><?php echo htmlspecialchars($upcomingMatch['team2_name']); ?></div>
                </div>
            </div>
        </div>
        <div class="info">
          <h3><?php echo htmlspecialchars($upcomingMatch['team1_name']); ?> vs <?php echo htmlspecialchars($upcomingMatch['team2_name']); ?></h3>
          <p><i class="fas fa-gamepad"></i> <strong>Game:</strong> <?php echo strtoupper($upcomingMatch['game_type']); ?></p>
          <p><i class="far fa-clock"></i> <strong>Time:</strong> <?php echo date('F j, Y - H:i', strtotime($upcomingMatch['match_time'])); ?></p>
          
          <div class="countdown-timer" data-time="<?php echo $upcomingMatch['match_time']; ?>">
            <div class="time-box"><span class="days">00</span><small>Days</small></div>
            <div class="time-box"><span class="hours">00</span><small>Hours</small></div>
            <div class="time-box"><span class="minutes">00</span><small>Mins</small></div>
            <div class="time-box"><span class="seconds">00</span><small>Secs</small></div>
          </div>
          
          <a href="tournament.php"><button>View Tournament</button></a>
          <?php if (!empty($upcomingMatch['stream_link'])): ?>
            <a href="<?php echo htmlspecialchars($upcomingMatch['stream_link']); ?>" target="_blank"><button style="background: #6441a5; margin-left: 10px;"><i class="fab fa-twitch"></i> Watch Stream</button></a>
          <?php endif; ?>
        </div>
      </div>
      <?php else: ?>
      <div class="tournament-content" style="text-align: center;">
          <p>No upcoming tournaments scheduled at the moment.</p>
          <a href="tournament.php"><button>Check Schedule</button></a>
      </div>
      <?php endif; ?>
    </section>
  </section>

  <section class="newsletter">
    <div class="newsletter-container">
      <h3>Subscribe to our newsletter</h3>
      <p>Enter your email to receive the latest promotions and tournament news</p>
      <form class="newsletter-form" action="../includes/newsletter.php" method="POST">
        <input type="email" name="email" class="newsletter-input" placeholder="Enter your email address" required />
        <button type="submit" class="newsletter-btn">Subscribe</button>
      </form>
    </div>
  </section>

  <?php include '../includes/footer.php'; ?>

  <script src="../js/home.js"></script>
  
  <!-- JavaScript bổ sung -->
  <script>
  document.addEventListener('DOMContentLoaded', function() {
    // Welcome message nếu đã đăng nhập
    <?php if ($isLoggedIn): ?>
      console.log('Welcome back, <?php echo $username; ?>!');
      
      // Có thể thêm thông báo chào mừng
      const welcomeMsg = document.createElement('div');
      welcomeMsg.className = 'welcome-message';
      welcomeMsg.innerHTML = `
        <div style="position: fixed; top: 80px; right: 20px; background: #4CAF50; color: white; padding: 10px 20px; border-radius: 5px; z-index: 1000; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
          <i class="fas fa-check-circle"></i> Welcome back, <?php echo $username; ?>!
        </div>
      `;
      document.body.appendChild(welcomeMsg);
      
      // Tự động ẩn sau 5 giây
      setTimeout(() => {
        welcomeMsg.remove();
      }, 5000);
    <?php endif; ?>
    
    // Tournament Countdown Logic
    const countdownEl = document.querySelector('.countdown-timer');
    if (countdownEl) {
        const targetDate = new Date(countdownEl.dataset.time).getTime();
        
        const updateTimer = () => {
            const now = new Date().getTime();
            const distance = targetDate - now;
            
            if (distance < 0) {
                countdownEl.innerHTML = '<div style="width:100%; text-align:center; color:#e63946; font-weight:bold; font-size: 1.2rem;">MATCH IS LIVE!</div>';
                return;
            }
            
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            countdownEl.querySelector('.days').innerText = String(days).padStart(2, '0');
            countdownEl.querySelector('.hours').innerText = String(hours).padStart(2, '0');
            countdownEl.querySelector('.minutes').innerText = String(minutes).padStart(2, '0');
            countdownEl.querySelector('.seconds').innerText = String(seconds).padStart(2, '0');
        };
        
        setInterval(updateTimer, 1000);
        updateTimer();
    }
    
    // Newsletter form handling
    const newsletterForm = document.querySelector('.newsletter-form');
    if (newsletterForm) {
      newsletterForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('.newsletter-btn');
        const originalText = submitBtn.innerHTML;
        
        // Show loading
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Subscribing...';
        submitBtn.disabled = true;
        
        // Send request
        fetch(this.action, {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Show success message
            const successMsg = document.createElement('div');
            successMsg.className = 'newsletter-success';
            successMsg.innerHTML = `
              <div style="color: #4CAF50; margin-top: 10px; font-size: 14px;">
                <i class="fas fa-check-circle"></i> ${data.message}
              </div>
            `;
            
            // Remove existing message
            const existingMsg = this.querySelector('.newsletter-success');
            if (existingMsg) existingMsg.remove();
            
            this.appendChild(successMsg);
            this.reset();
            
            // Hide message after 5 seconds
            setTimeout(() => {
              successMsg.remove();
            }, 5000);
          } else {
            alert(data.message || 'Subscription failed. Please try again.');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred. Please try again.');
        })
        .finally(() => {
          submitBtn.innerHTML = originalText;
          submitBtn.disabled = false;
        });
      });
    }
  });
  </script>
</body>

</html>