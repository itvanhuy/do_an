<?php
// File: pages/search.php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/database.php';

// Tự động đăng nhập nếu có remember token
Auth::autoLogin();

// Lấy thông tin user nếu đã đăng nhập
$isLoggedIn = Auth::isLoggedIn();

$query = trim($_GET['q'] ?? '');
$products = [];
$searchPerformed = false;

if (!empty($query)) {
    $searchPerformed = true;
    try {
        $db = Database::getInstance();
        
        // Tìm kiếm sản phẩm theo tên hoặc mô tả
        $stmt = $db->prepare("
            SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.status = 'active' 
            AND (p.name LIKE ? OR p.description LIKE ?)
            ORDER BY p.name
        ");
        $searchTerm = '%' . $query . '%';
        $stmt->execute([$searchTerm, $searchTerm]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (Exception $e) {
        // Handle error
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link rel="stylesheet" href="../css/shop.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main class="search-container">
        <?php if ($searchPerformed): ?>
            <div class="search-header">
                <h1 class="search-title">Search Results for "<span class="search-term"><?php echo htmlspecialchars($query); ?></span>"</h1>
                <p class="search-count">Found <?php echo count($products); ?> product(s)</p>
            </div>
            
            <?php if (!empty($products)): ?>
                <div class="products">
                    <?php foreach ($products as $product): ?>
                    <div class="product-card product-clickable" onclick="window.location.href='product-detail.php?id=<?php echo $product['id']; ?>'">
                        <?php if ($product['discount'] > 0): ?>
                        <div class="badge">-<?php echo $product['discount']; ?>%</div>
                        <?php endif; ?>
                        
                        <img src="../img/product/<?php echo htmlspecialchars($product['image']); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                             onerror="this.src='../img/product/default.jpg'">
                        
                        <div class="card-content">
                            <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                            
                            <div class="product-price">
                                <?php if ($product['discount'] > 0): ?>
                                <span class="old-price">
                                    <?php echo number_format($product['price'], 0, ',', '.'); ?> VND
                                </span>
                                <span>
                                    <?php echo number_format($product['price'] * (1 - $product['discount']/100), 0, ',', '.'); ?> VND
                                </span>
                                <?php else: ?>
                                <span>
                                    <?php echo number_format($product['price'], 0, ',', '.'); ?> VND
                                </span>
                                <?php endif; ?>
                            </div>
                            
                            <button class="btn-buy" onclick="event.stopPropagation(); addToCart(<?php echo $product['id']; ?>)">
                                <i class="fas fa-cart-plus"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-results">
                    <i class="fas fa-search"></i>
                    <h3>No products found</h3>
                    <p>We couldn't find any products matching your search. Try different keywords.</p>
                    <a href="shop.php" class="btn-back">Browse All Products</a>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="no-results">
                <i class="fas fa-search"></i>
                <h3>Search for products</h3>
                <p>Use the search bar above to find products by name or description.</p>
            </div>
        <?php endif; ?>
    </main>

    <?php include '../includes/footer.php'; ?>

    <script>
        function addToCart(productId) {
            <?php if (!$isLoggedIn): ?>
                alert('Please login to add items to cart.');
                window.location.href = '../login.php';
                return;
            <?php endif; ?>
            
            const formData = new URLSearchParams();
            formData.append('product_id', productId);
            formData.append('quantity', 1);

            fetch('../includes/add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Product added to cart!');
                    // Update cart count if element exists
                    const cartCount = document.querySelector('.cart-count');
                    if (cartCount) {
                        cartCount.textContent = data.cart_count;
                    } else {
                        location.reload();
                    }
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        }
    </script>
</body>
</html>