<?php
// File: pages/product-detail.php
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

$product = null;
$relatedProducts = [];
$reviews = [];
$isInWishlist = false;
$recentProducts = []; // Khởi tạo biến sản phẩm đã xem

if (isset($_GET['id'])) {
    $productId = (int)$_GET['id'];
    
    try {
        $db = Database::getInstance();
        
        // ==================================================================
        // 2. LẤY THÔNG TIN SẢN PHẨM CHÍNH
        // ==================================================================
        $stmt = $db->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ? AND p.status = 'active'");
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($product) {
            // ==============================================================
            // 3. LẤY SẢN PHẨM LIÊN QUAN (RELATED)
            // ==============================================================
            $stmt = $db->prepare("SELECT * FROM products WHERE category_id = ? AND id != ? AND status = 'active' LIMIT 4");
            $stmt->execute([$product['category_id'], $productId]);
            $relatedProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // ==============================================================
            // 4. LẤY ĐÁNH GIÁ (REVIEWS)
            // ==============================================================
            $stmt = $db->prepare("
                SELECT r.*, u.full_name, u.username 
                FROM reviews r 
                JOIN users u ON r.user_id = u.id 
                WHERE r.product_id = ? AND r.status = 'approved' 
                ORDER BY r.created_at DESC
            ");
            $stmt->execute([$productId]);
            $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Kiểm tra trạng thái Wishlist
            if ($isLoggedIn) {
                $stmt = $db->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
                $stmt->execute([$_SESSION['user_id'], $productId]);
                $isInWishlist = $stmt->fetch() ? true : false;
            }

            // ==============================================================
            // 5. XỬ LÝ SẢN PHẨM ĐÃ XEM (RECENTLY VIEWED)
            // ==============================================================
            if (!isset($_SESSION['recently_viewed'])) {
                $_SESSION['recently_viewed'] = [];
            }
            
            // Xóa ID hiện tại nếu đã có (để đưa lên đầu danh sách)
            $key = array_search($productId, $_SESSION['recently_viewed']);
            if ($key !== false) {
                unset($_SESSION['recently_viewed'][$key]);
            }
            
            // Thêm ID hiện tại vào đầu danh sách
            array_unshift($_SESSION['recently_viewed'], $productId);
            
            // Giới hạn lưu 6 sản phẩm gần nhất
            if (count($_SESSION['recently_viewed']) > 6) {
                array_pop($_SESSION['recently_viewed']);
            }
            
            // Lấy danh sách sản phẩm để hiển thị (trừ sản phẩm đang xem)
            $recentIds = array_diff($_SESSION['recently_viewed'], [$productId]);
            
            if (!empty($recentIds)) {
                $placeholders = implode(',', array_fill(0, count($recentIds), '?'));
                // Lấy thông tin sản phẩm từ DB
                $stmt = $db->prepare("SELECT * FROM products WHERE id IN ($placeholders) AND status = 'active'");
                $stmt->execute(array_values($recentIds));
                $fetchedProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Sắp xếp lại kết quả theo đúng thứ tự trong session (gần nhất trước)
                $productMap = [];
                foreach ($fetchedProducts as $p) {
                    $productMap[$p['id']] = $p;
                }
                
                foreach ($recentIds as $id) {
                    if (isset($productMap[$id])) {
                        $recentProducts[] = $productMap[$id];
                    }
                }
            }
        }
    } catch (Exception $e) {
        // Handle error
        error_log("Error fetching product details: " . $e->getMessage());
    }
}

if (!$product) {
    header('Location: product.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - TechShop</title>
    <link rel="stylesheet" href="../css/detailproduct.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="../js/detailproduct.js"></script>
    <style>
        /* CSS từ file HTML của bạn */
        .product-clickable {
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .product-clickable:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        
        .btn-wishlist {
            background: none;
            border: 1px solid #ddd;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
            font-size: 1.2rem;
            color: #ccc;
            transition: all 0.3s;
            margin-left: 10px;
        }
        .btn-wishlist:hover, .btn-wishlist.active {
            color: #ff4757;
            border-color: #ff4757;
            background: rgba(255, 71, 87, 0.1);
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <nav class="breadcrumb">
        <ul>
            <li><a href="home.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="shop.php">Shop</a></li>
            <li><a href="product.php?category_id=<?php echo $product['category_id']; ?>"><?php echo htmlspecialchars($product['category_name']); ?></a></li>
            <li class="current"><?php echo htmlspecialchars($product['name']); ?></li>
        </ul>
    </nav>

    <main class="product-container">
        <!-- Product Images -->
        <section class="product-image">
            <?php
            // Lấy các hình ảnh sản phẩm
            $images = [];
            if (!empty($product['image'])) {
                $images[] = $product['image'];
            }
            // Lấy thêm ảnh từ bảng product_images
            $stmt = $db->prepare("SELECT image FROM product_images WHERE product_id = ?");
            $stmt->execute([$productId]);
            $extraImages = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $images = array_merge($images, $extraImages);
            ?>
            <img id="mainImage" class="main-image" src="../img/product/<?php echo htmlspecialchars($images[0]); ?>" 
                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                 onerror="this.src='../img/product/default.jpg'" />
            <div class="thumbnail-gallery">
                <?php foreach ($images as $index => $image): ?>
                <img src="../img/product/<?php echo htmlspecialchars($image); ?>" 
                     alt="Thumbnail <?php echo $index + 1; ?>" 
                     onclick="changeImage(this)"
                     onerror="this.src='../img/product/default.jpg'" />
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Product Information -->
        <section class="product-info">
            <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>

            <div class="product-rating" aria-label="Product rating: 4 out of 5 stars">
                <span class="stars" style="color: #f1c40f;">★★★★☆</span> 
                <span class="reviews-count">(<?php echo count($reviews); ?> reviews)</span>
            </div>

            <p class="product-price">
                <?php if ($product['discount'] > 0): ?>
                <span style="text-decoration: line-through; color: #999; font-size: 16px; display: block;">
                    <?php echo number_format($product['price'], 0, ',', '.'); ?> VND
                </span>
                <span style="color: #e60023; font-weight: bold; font-size: 24px;">
                    <?php echo number_format($product['price'] * (1 - $product['discount']/100), 0, ',', '.'); ?> VND
                </span>
                <?php else: ?>
                <span style="color: #333; font-weight: bold; font-size: 24px;">
                    <?php echo number_format($product['price'], 0, ',', '.'); ?> VND
                </span>
                <?php endif; ?>
            </p>

            <p class="product-description">
                <?php echo nl2br(htmlspecialchars($product['description'] ?? 'No description available.')); ?>
            </p>

            <div class="product-actions">
                <label for="quantity">Quantity:</label>
                <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo $product['stock_quantity'] ?? 10; ?>" />
                <button class="btn-add-cart" onclick="addToCart(<?php echo $product['id']; ?>)">Add to Cart</button>
                <button class="btn-buy-now" onclick="buyNow(<?php echo $product['id']; ?>)">Buy Now</button>
                <button class="btn-wishlist <?php echo $isInWishlist ? 'active' : ''; ?>" onclick="toggleWishlist(<?php echo $product['id']; ?>)" title="<?php echo $isInWishlist ? 'Remove from Wishlist' : 'Add to Wishlist'; ?>">
                    <i class="<?php echo $isInWishlist ? 'fas' : 'far'; ?> fa-heart"></i>
                </button>
            </div>

            <!-- Technical Specifications -->
            <div class="tech-specs">
                <h2>Technical Specifications</h2>
                <table>
                    <tbody>
                        <?php if (!empty($product['specifications'])): 
                            // Giả sử specifications được lưu dưới dạng JSON
                            $specs = json_decode($product['specifications'], true);
                            if ($specs && is_array($specs)):
                                foreach ($specs as $key => $value):
                        ?>
                        <tr>
                            <th><?php echo htmlspecialchars($key); ?></th>
                            <td><?php echo htmlspecialchars($value); ?></td>
                        </tr>
                        <?php 
                                endforeach;
                            endif;
                        else: // Nếu không có specs, hiển thị mặc định
                        ?>
                        <tr>
                            <th>Brand</th>
                            <td><?php echo htmlspecialchars($product['brand'] ?? 'Unknown'); ?></td>
                        </tr>
                        <tr>
                            <th>Model</th>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                        </tr>
                        <tr>
                            <th>Category</th>
                            <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <!-- Product Promotions -->
    <section class="product-promotions">
        <h2>Promotions when buying this product</h2>
        <ul class="promotion-list">
            <li>🚚 Free nationwide shipping</li>
            <li>🛡️ Official 12-month warranty</li>
            <li>💳 Cash on delivery available</li>
            <li>🎁 Comes with a premium carrying case</li>
            <li>🔥 5% off on your first order</li>
        </ul>
    </section>

    <!-- User Reviews -->
    <section class="review-section">
        <h2>⭐ Product Reviews</h2>

        <!-- Dynamic reviews -->
        <div class="review-list">
            <?php if (empty($reviews)): ?>
                <p style="color: #666; font-style: italic;">No reviews yet. Be the first to review this product!</p>
            <?php else: ?>
                <?php foreach ($reviews as $review): ?>
            <div class="review-item">
                <div class="review-header">
                    <span class="reviewer-name"><?php echo htmlspecialchars($review['full_name'] ?: $review['username']); ?></span>
                    <div class="review-stars" style="color: #f1c40f;"><?php echo str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']); ?></div>
                    <span class="review-date"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></span>
                </div>
                <p class="review-text"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
            </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- New review form -->
        <form class="review-form" id="reviewForm">
            <div class="rating-stars">
                <input type="radio" id="star5" name="rating" value="5" />
                <label for="star5" title="Excellent">&#9733;</label>

                <input type="radio" id="star4" name="rating" value="4" />
                <label for="star4" title="Good">&#9733;</label>

                <input type="radio" id="star3" name="rating" value="3" />
                <label for="star3" title="Average">&#9733;</label>

                <input type="radio" id="star2" name="rating" value="2" />
                <label for="star2" title="Poor">&#9733;</label>

                <input type="radio" id="star1" name="rating" value="1" />
                <label for="star1" title="Bad">&#9733;</label>
            </div>

            <textarea name="review" placeholder="Write your review..." rows="4" required></textarea>

            <button type="submit" class="btn-submit">Submit Review</button>
        </form>
    </section>

    <!-- Related Products -->
    <?php if (!empty($relatedProducts)): ?>
    <section class="related-products">
        <h2 class="section-title">🛒 Related Products</h2>
        <div class="product-grid">
            <?php foreach ($relatedProducts as $related): ?>
            <div class="product-card product-clickable" onclick="window.location.href='product-detail.php?id=<?php echo $related['id']; ?>'" style="position: relative;">
                <?php if (($related['discount'] ?? 0) > 0): ?>
                    <div style="position: absolute; top: 10px; left: 10px; background: #e60023; color: white; padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; z-index: 2;">-<?php echo $related['discount']; ?>%</div>
                <?php endif; ?>
                
                <img src="../img/product/<?php echo htmlspecialchars($related['image']); ?>" 
                     alt="<?php echo htmlspecialchars($related['name']); ?>"
                     onerror="this.src='../img/product/default.jpg'">
                <div class="product-info">
                    <h3><?php echo htmlspecialchars(mb_strimwidth($related['name'], 0, 30, '...')); ?></h3>
                    <div style="margin: 10px 0;">
                        <?php if (($related['discount'] ?? 0) > 0): ?>
                            <span style="text-decoration: line-through; color: #999; font-size: 0.9em; margin-right: 5px;">
                                <?php echo number_format($related['price'], 0, ',', '.'); ?> VND
                            </span>
                            <span style="color: #e60023; font-weight: bold;">
                                <?php echo number_format($related['price'] * (1 - $related['discount']/100), 0, ',', '.'); ?> VND
                            </span>
                        <?php else: ?>
                            <span style="color: #333; font-weight: bold;"><?php echo number_format($related['price'], 0, ',', '.'); ?> VND</span>
                        <?php endif; ?>
                    </div>
                    <button class="btn-primary" onclick="event.stopPropagation(); addToCart(<?php echo $related['id']; ?>, 1)"><i class="fas fa-cart-plus"></i> Add to Cart</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="see-more-container">
            <a href="product.php?category_id=<?php echo $product['category_id']; ?>" class="btn-see-more">🔍 See More Products</a>
        </div>
    </section>
    <?php endif; ?>

    <!-- Recently Viewed Products -->
    <?php if (!empty($recentProducts)): ?>
    <section class="related-products" style="background-color: #f9f9f9; margin-top: 0; padding-top: 40px;">
        <h2 class="section-title">🕒 Recently Viewed Products</h2>
        <div class="product-grid">
            <?php foreach ($recentProducts as $recent): ?>
            <div class="product-card product-clickable" onclick="window.location.href='product-detail.php?id=<?php echo $recent['id']; ?>'" style="position: relative;">
                <?php if (($recent['discount'] ?? 0) > 0): ?>
                    <div style="position: absolute; top: 10px; left: 10px; background: #e60023; color: white; padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; z-index: 2;">-<?php echo $recent['discount']; ?>%</div>
                <?php endif; ?>
                
                <img src="../img/product/<?php echo htmlspecialchars($recent['image']); ?>" 
                     alt="<?php echo htmlspecialchars($recent['name']); ?>"
                     onerror="this.src='../img/product/default.jpg'">
                <div class="product-info">
                    <h3><?php echo htmlspecialchars(mb_strimwidth($recent['name'], 0, 30, '...')); ?></h3>
                    <div style="margin: 10px 0;">
                        <?php if (($recent['discount'] ?? 0) > 0): ?>
                            <span style="text-decoration: line-through; color: #999; font-size: 0.9em; margin-right: 5px;">
                                <?php echo number_format($recent['price'], 0, ',', '.'); ?> VND
                            </span>
                            <span style="color: #e60023; font-weight: bold;">
                                <?php echo number_format($recent['price'] * (1 - $recent['discount']/100), 0, ',', '.'); ?> VND
                            </span>
                        <?php else: ?>
                            <span style="color: #333; font-weight: bold;"><?php echo number_format($recent['price'], 0, ',', '.'); ?> VND</span>
                        <?php endif; ?>
                    </div>
                    <button class="btn-primary" onclick="event.stopPropagation(); addToCart(<?php echo $recent['id']; ?>, 1)"><i class="fas fa-cart-plus"></i> Add to Cart</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php include '../includes/footer.php'; ?>

    <script>
        function toggleWishlist(productId) {
            <?php if (!$isLoggedIn): ?>
                alert('Please login to use wishlist.');
                window.location.href = '../login.php';
                return;
            <?php endif; ?>

            const formData = new URLSearchParams();
            formData.append('action', 'toggle');
            formData.append('product_id', productId);

            fetch('../includes/wishlist_actions.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const btn = document.querySelector('.btn-wishlist');
                    const icon = btn.querySelector('i');
                    
                    if (data.status === 'added') {
                        btn.classList.add('active');
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                        btn.title = 'Remove from Wishlist';
                    } else {
                        btn.classList.remove('active');
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                        btn.title = 'Add to Wishlist';
                    }
                } else {
                    alert(data.message);
                }
            });
        }

        function changeImage(img) {
            document.getElementById('mainImage').src = img.src;
        }

        function addToCart(productId, qty = null) {
            let quantity = qty;
            if (quantity === null) {
                quantity = document.getElementById('quantity').value;
            }
            
            <?php if (!$isLoggedIn): ?>
                alert('Please login to add items to cart.');
                window.location.href = '../login.php';
                return;
            <?php endif; ?>
            
            // Gọi API thêm vào giỏ hàng
            fetch('../includes/add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}&quantity=${quantity}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Product added to cart!');
                    // Update cart count
                    const cartCount = document.querySelector('.cart-count');
                    if (cartCount) {
                        cartCount.textContent = data.cart_count;
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

        function buyNow(productId) {
            const quantity = document.getElementById('quantity').value;
            
            <?php if (!$isLoggedIn): ?>
                alert('Please login to purchase.');
                window.location.href = '../login.php';
                return;
            <?php endif; ?>
            
            // Chuyển đến trang checkout
            window.location.href = `checkout.php?product_id=${productId}&quantity=${quantity}`;
        }

        // Xử lý form đánh giá
        document.getElementById('reviewForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            <?php if (!$isLoggedIn): ?>
                alert('Please login to submit a review.');
                window.location.href = '../login.php';
                return;
            <?php endif; ?>
            
            const formData = new FormData(this);
            const rating = formData.get('rating');
            const review = formData.get('review');
            
            // Gửi đánh giá lên server
            fetch('../includes/submit_review.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=<?php echo $product['id']; ?>&rating=${rating}&review=${encodeURIComponent(review)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Thank you for your review!');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });

        // Thêm hiệu ứng hover cho related products
        document.addEventListener('DOMContentLoaded', function() {
            const relatedCards = document.querySelectorAll('.product-card.product-clickable');
            relatedCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                    this.style.boxShadow = '0 10px 20px rgba(0,0,0,0.1)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = '0 2px 5px rgba(0,0,0,0.1)';
                });
            });
        });
    </script>
</body>
</html>