<?php
// File: pages/wishlist.php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/database.php';

Auth::autoLogin();

if (!Auth::isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

$db = Database::getInstance();
$userId = $_SESSION['user_id'];

// Get wishlist items
$wishlistItems = [];
try {
    $stmt = $db->prepare("
        SELECT p.*, w.created_at as added_at 
        FROM wishlist w 
        JOIN products p ON w.product_id = p.id 
        WHERE w.user_id = ? 
        ORDER BY w.created_at DESC
    ");
    $stmt->execute([$userId]);
    $wishlistItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Bỏ qua lỗi nếu bảng chưa tồn tại, danh sách sẽ rỗng
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/product.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .wishlist-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
            min-height: 60vh;
        }
        .wishlist-header {
            margin-bottom: 30px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }
        .wishlist-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 30px;
        }
        .empty-wishlist {
            text-align: center;
            padding: 50px;
            color: #666;
        }
        .empty-wishlist i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #ddd;
        }
        .btn-remove {
            background: #ff4757;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            margin-top: 10px;
        }
        .btn-remove:hover {
            background: #ff6b81;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main class="wishlist-container">
        <div class="wishlist-header">
            <h1>My Wishlist <i class="fas fa-heart" style="color: #ff4757;"></i></h1>
        </div>

        <?php if (empty($wishlistItems)): ?>
            <div class="empty-wishlist">
                <i class="far fa-heart"></i>
                <h2>Your wishlist is empty</h2>
                <p>Save items you love to buy later!</p>
                <a href="shop.php" class="btn btn-primary" style="margin-top: 20px; display: inline-block; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Browse Products</a>
            </div>
        <?php else: ?>
            <div class="wishlist-grid">
                <?php foreach ($wishlistItems as $product): ?>
                <div class="product-card" id="wishlist-item-<?php echo $product['id']; ?>">
                    <div class="product-clickable" onclick="window.location.href='product-detail.php?id=<?php echo $product['id']; ?>'">
                        <img src="../img/product/<?php echo htmlspecialchars($product['image']); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                             onerror="this.src='../img/product/default.jpg'">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="price"><?php echo number_format($product['price'], 0, ',', '.'); ?> VND</p>
                    </div>
                    <div style="padding: 15px; text-align: center;">
                        <button class="btn-primary" onclick="addToCart(<?php echo $product['id']; ?>)">Add to Cart</button>
                        <button class="btn-remove" onclick="removeFromWishlist(<?php echo $product['id']; ?>)">Remove</button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <?php include '../includes/footer.php'; ?>

    <script>
        function removeFromWishlist(productId) {
            if (!confirm('Remove this item from wishlist?')) return;

            const formData = new URLSearchParams();
            formData.append('action', 'remove');
            formData.append('product_id', productId);

            fetch('../includes/wishlist_actions.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const item = document.getElementById('wishlist-item-' + productId);
                    if (item) {
                        item.remove();
                        if (document.querySelectorAll('.product-card').length === 0) {
                            location.reload();
                        }
                    }
                } else {
                    alert(data.message);
                }
            });
        }

        function addToCart(productId) {
             fetch('../includes/add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}&quantity=1`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Product added to cart!');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }
    </script>
</body>
</html>