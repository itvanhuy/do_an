<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/database.php';

Auth::autoLogin();
$db = Database::getInstance();

// Lấy sản phẩm Featured (giả sử là sản phẩm giá cao hoặc có flag featured)
$featuredSql = "SELECT * FROM products WHERE status = 'active' ORDER BY price DESC LIMIT 9";
$featuredStmt = $db->query($featuredSql);
$featuredProducts = $featuredStmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy sản phẩm Flash Sale (giảm giá > 0)
$fsSql = "SELECT * FROM products WHERE discount > 0 AND status = 'active' ORDER BY discount DESC LIMIT 4";
$fsStmt = $db->query($fsSql);
$fsProducts = $fsStmt->fetchAll(PDO::FETCH_ASSOC);

// Fallback: Nếu không có sản phẩm giảm giá nào, lấy ngẫu nhiên 4 sản phẩm để hiển thị cho đẹp
if (empty($fsProducts)) {
    $fsSql = "SELECT * FROM products WHERE status = 'active' ORDER BY RAND() LIMIT 4";
    $fsStmt = $db->query($fsSql);
    $fsProducts = $fsStmt->fetchAll(PDO::FETCH_ASSOC);
}

// Lấy sản phẩm Recommendation (ngẫu nhiên)
$recSql = "SELECT * FROM products WHERE status = 'active' ORDER BY RAND() LIMIT 12";
$recStmt = $db->query($recSql);
$recProducts = $recStmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy danh mục sản phẩm (Chỉ lấy danh mục cha)
$stmt = $db->query("SELECT * FROM categories WHERE status = 'active' AND (parent_id IS NULL OR parent_id = 0) ORDER BY name");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy danh sách Brands từ database
$stmt = $db->query("SELECT * FROM brands ORDER BY name ASC");
$brands = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - TechShop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/shop.css">
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/shop.js"></script>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container">
        <div class="sidebar">
            <form action="search.php" method="GET" class="search-box">
                <input type="text" name="q" placeholder="Search products...">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
            <h4>Categories</h4>
            <ul class="category-list">
                <?php foreach ($categories as $cat): ?>
                <li class="category-item">
                    <a href="product.php?category_id=<?php echo $cat['id']; ?>" style="color: inherit; text-decoration: none; flex: 1;">
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>

            <h4>Filter by Brand</h4>
            <?php foreach ($brands as $brand): ?>
            <label>
                <input type="checkbox" onclick="window.location.href='product.php?brand=<?php echo urlencode($brand['name']); ?>'"> 
                <?php echo htmlspecialchars($brand['name']); ?>
            </label>
            <?php endforeach; ?>
        </div>

        <div class="content">
            <!-- Featured Products Slider -->
            <section class="featured-section">
                <h2>Featured Products</h2>
                <div class="carousel-container">
                    <div class="carousel-track">
                        <?php
                        // Chia sản phẩm thành các nhóm 3 (mỗi nhóm là 1 slide)
                        $chunks = array_chunk($featuredProducts, 3);
                        foreach ($chunks as $chunk):
                        ?>
                            <div class="carousel-slide">
                                <?php foreach ($chunk as $p): ?>
                                <div class="card">
                                    <span class="badge" style="position:absolute; top:10px; left:10px; background:var(--accent-color); color:white; padding:2px 8px; border-radius:4px; font-size:10px; font-weight:bold;">HOT</span>
                                    <a href="product-detail.php?id=<?php echo $p['id']; ?>">
                                        <img src="../img/product/<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" onerror="this.src='../img/product/default.jpg'">
                                    </a>
                                    <div class="card-content">
                                        <a href="product-detail.php?id=<?php echo $p['id']; ?>" style="text-decoration:none;">
                                            <h4><?php echo htmlspecialchars(mb_strimwidth($p['name'], 0, 40, '...')); ?></h4>
                                        </a>
                                        <p class="price"><?php echo number_format($p['price'], 0, ',', '.'); ?>₫</p>
                                        <button class="buy-button" onclick="addToCart(<?php echo $p['id']; ?>)">
                                            <i class="fas fa-cart-plus"></i> Add to Cart
                                        </button>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="carousel-dots">
                    <?php for($i = 0; $i < count($chunks); $i++): ?>
                        <span class="dot <?php echo $i === 0 ? 'active' : ''; ?>" data-index="<?php echo $i; ?>"></span>
                    <?php endfor; ?>
                </div>
            </section>

            <!-- Flash Sale -->
            <section class="fs-section">
                <div class="fs-header">
                    <h2 class="fs-title"><i class="fas fa-bolt"></i> Flash Sale</h2>
                    <div class="fs-countdown">
                        <div class="fs-countdown__item">
                            <div id="hours" class="fs-countdown__value">00</div>
                            <div class="fs-countdown__label">Hours</div>
                        </div>
                        <div class="fs-countdown__item">
                            <div id="minutes" class="fs-countdown__value">00</div>
                            <div class="fs-countdown__label">Minutes</div>
                        </div>
                        <div class="fs-countdown__item">
                            <div id="seconds" class="fs-countdown__value">00</div>
                            <div class="fs-countdown__label">Seconds</div>
                        </div>
                    </div>
                </div>
                <div class="fs-products">
                    <?php foreach ($fsProducts as $p): ?>
                    <div class="fs-card" onclick="window.location.href='product-detail.php?id=<?php echo $p['id']; ?>'">
                        <img src="../img/product/<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" onerror="this.src='../img/product/default.jpg'">
                        <div class="fs-card__content">
                            <h4><?php echo htmlspecialchars(mb_strimwidth($p['name'], 0, 30, '...')); ?></h4>
                            <p class="fs-price"><?php echo number_format($p['price'] * (1 - ($p['discount'] ?? 0)/100), 0, ',', '.'); ?> VND</p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- Top Searches -->
            <div class="section">
                <div class="section-header">
                    <h3>Top Searches</h3>
                    <button class="see-more-btn" style="background:none; border:none; color:#4361ee; cursor:pointer;">See more</button>
                </div>
                <div class="slider">
                    <div class="products-slide active">
                        <?php 
                        // Lấy 3 sản phẩm ngẫu nhiên làm top search
                        $topSearch = array_slice($recProducts, 0, 3);
                        foreach ($topSearch as $p): 
                        ?>
                        <div class="item-card" onclick="window.location.href='product-detail.php?id=<?php echo $p['id']; ?>'">
                            <img src="../img/product/<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" onerror="this.src='../img/product/default.jpg'">
                            <p class="item-name"><?php echo htmlspecialchars(mb_strimwidth($p['name'], 0, 40, '...')); ?></p>
                            <p class="product-price" style="color:#e63946;"><?php echo number_format($p['price'], 0, ',', '.'); ?>₫</p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Recommendations -->
            <div class="recommendation-section">
                <div class="header-bar" style="margin-bottom:20px;">
                    <h3>Today's Recommendations</h3>
                </div>
                <div class="product-list">
                    <?php foreach ($recProducts as $p): ?>
                    <div class="item-card" onclick="window.location.href='product-detail.php?id=<?php echo $p['id']; ?>'">
                        <img src="../img/product/<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" onerror="this.src='../img/product/default.jpg'">
                        <p class="item-name"><?php echo htmlspecialchars(mb_strimwidth($p['name'], 0, 40, '...')); ?></p>
                        <p class="item-price"><?php echo number_format($p['price'], 0, ',', '.'); ?>₫</p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script>
        // Toast Notification Function
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.style.cssText = `
                position: fixed;
                bottom: 20px;
                right: 20px;
                background: ${type === 'success' ? '#4CAF50' : '#f44336'};
                color: white;
                padding: 12px 24px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                z-index: 10000;
                animation: slideIn 0.3s ease-out forwards;
                display: flex;
                align-items: center;
                gap: 10px;
                font-weight: 500;
            `;
            toast.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> ${message}`;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.animation = 'slideOut 0.3s ease-in forwards';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Add to Cart Function
        function addToCart(productId) {
            fetch('../includes/add_to_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `product_id=${productId}&quantity=1`
            })
            .then(res => res.json())
            .then(data => showToast(data.message, data.success ? 'success' : 'error'))
            .catch(() => showToast('Something went wrong', 'error'));
        }
    </script>
</body>
</html>