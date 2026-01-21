<?php
// File: pages/cart.php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/database.php';

<<<<<<< HEAD
// ==========================================================================
// 1. KHỞI TẠO & AUTH
// ==========================================================================

=======
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
// Tự động đăng nhập nếu có remember token
Auth::autoLogin();

// Lấy thông tin user nếu đã đăng nhập
$isLoggedIn = Auth::isLoggedIn();
$username = $isLoggedIn ? $_SESSION['username'] : '';

if (!$isLoggedIn) {
    header('Location: ../login.php');
    exit();
}

// Lấy user_id
$userId = $_SESSION['user_id'];

$cartItems = [];
$subtotal = 0;
$total = 0;
$discountAmount = 0;
$discountError = null;

// Lấy và xóa lỗi discount từ session
if (isset($_SESSION['discount_error'])) {
    $discountError = $_SESSION['discount_error'];
    unset($_SESSION['discount_error']);
}

<<<<<<< HEAD
// ==========================================================================
// 2. LẤY DỮ LIỆU GIỎ HÀNG (DATABASE)
// ==========================================================================

=======
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
try {
    $db = Database::getInstance();
    
    // Lấy các sản phẩm trong giỏ hàng của user từ DATABASE
    // Đồng thời lấy giá mới nhất từ bảng products để đảm bảo giá không bị lỗi thời
    $stmt = $db->prepare("
        SELECT 
            c.product_id as id,
            p.name,
            p.price,
            p.image,
            c.quantity,
            p.stock_quantity
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ?
        ORDER BY c.created_at DESC
    ");
    $stmt->execute([$userId]);
    $itemsFromDb = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($itemsFromDb as $item) {
        $itemTotal = $item['price'] * $item['quantity'];
        $subtotal += $itemTotal;
        
        $cartItems[] = [
            'id' => $item['id'],
            'name' => $item['name'],
            'price' => $item['price'],
            'image' => $item['image'],
            'quantity' => $item['quantity'],
            'total' => $itemTotal,
            'stock_quantity' => $item['stock_quantity']
        ];
    }

} catch (Exception $e) {
    // Nếu có lỗi, ví dụ table 'cart' chưa tồn tại, thì coi như giỏ hàng trống
    // Log lỗi này ra để debug
    error_log("Error fetching cart from database: " . $e->getMessage());
    $cartItems = [];
    $subtotal = 0;
}

<<<<<<< HEAD
// ==========================================================================
// 3. FALLBACK: LẤY TỪ SESSION (Nếu DB trống)
// ==========================================================================
=======
// Fallback: Nếu DB cart trống, kiểm tra session cart (đề phòng trường hợp add_to_cart dùng session)
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
if (empty($cartItems) && isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $pId => $sessItem) {
        $itemTotal = $sessItem['price'] * $sessItem['quantity'];
        $subtotal += $itemTotal;
        
        $cartItems[] = [
            'id' => $pId,
            'name' => $sessItem['name'],
            'price' => $sessItem['price'],
            'image' => $sessItem['image'],
            'quantity' => $sessItem['quantity'],
            'total' => $itemTotal,
            'stock_quantity' => $sessItem['stock_quantity'] ?? 100
        ];
    }
}

<<<<<<< HEAD
// ==========================================================================
// 4. TÍNH TOÁN MÃ GIẢM GIÁ
// ==========================================================================
=======
// Áp dụng mã giảm giá nếu có
>>>>>>> 3be3e54cf790d1b58872b3ae93f5796e18941695
if (isset($_SESSION['discount_code']) && $subtotal > 0) {
    if (isset($_SESSION['discount_type']) && $_SESSION['discount_type'] == 'fixed') {
        $discountAmount = $_SESSION['discount_value'];
    } elseif (isset($_SESSION['discount_value'])) { // percent
        $discountAmount = ($subtotal * $_SESSION['discount_value']) / 100;
    }
    
    if ($discountAmount > $subtotal) $discountAmount = $subtotal; // Không giảm quá tổng tiền
    $total = $subtotal - $discountAmount;
    $_SESSION['discount_amount'] = $discountAmount; // Lưu lại số tiền giảm
} else {
    // Nếu không có giảm giá, tổng tiền bằng tổng phụ
    $total = $subtotal;
    unset($_SESSION['discount_code']);
    unset($_SESSION['discount_percent']);
    unset($_SESSION['discount_value']);
    unset($_SESSION['discount_type']);
    unset($_SESSION['discount_amount']);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link rel="stylesheet" href="../css/cart.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

<main class="cart-container">
  <h1 class="page-title" aria-label="Shopping Cart">🛒 Your Shopping Cart</h1>

  <div class="cart-content">
    <?php if (empty($cartItems)): ?>
        <div class="empty-cart">
            <i class="fas fa-shopping-cart"></i>
            <h2>Your cart is empty</h2>
            <p>Add some products to your cart to get started!</p>
            <a href="product.php" class="btn btn-primary">Continue Shopping</a>
        </div>
    <?php else: ?>
    <section class="cart-items" aria-label="Items in your cart">
      <?php foreach ($cartItems as $item): ?>
      <div class="cart-item" data-product-id="<?php echo $item['id']; ?>">
        <img src="../img/product/<?php echo htmlspecialchars($item['image']); ?>" 
             alt="<?php echo htmlspecialchars($item['name']); ?>">
        <div class="item-info">
          <h2><?php echo htmlspecialchars($item['name']); ?></h2>
          <p class="price"><?php echo number_format($item['price'], 0, ',', '.'); ?> VND</p>
          <div class="quantity-remove-wrapper">
            <span class="quantity-label">Quantity:</span>
            <input 
              type="number" 
              value="<?php echo $item['quantity']; ?>" 
              min="1" 
              max="<?php echo $item['stock_quantity']; ?>"
              class="quantity-input"
              onchange="updateQuantity(<?php echo $item['id']; ?>, this.value)"
              aria-label="Quantity of <?php echo htmlspecialchars($item['name']); ?>"
            />
            <button class="remove-btn" onclick="removeItem(<?php echo $item['id']; ?>)" aria-label="Remove product">
              <i class="fas fa-trash"></i>
            </button>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </section>

    <aside class="cart-summary" aria-label="Order summary">
      <h2>Order Summary</h2>
      <div class="summary-row">
        <span>Subtotal:</span>
        <span id="subtotal"><?php echo number_format($subtotal, 0, ',', '.'); ?> VND</span>
      </div>
      <div class="summary-row">
        <span>Shipping Fee:</span>
        <span class="free-ship">Free 🚚</span>
      </div>

    <?php if (isset($_SESSION['discount_code']) && $discountAmount > 0): ?>
      <div class="summary-row discount-applied">
        <span>Discount (<?php echo htmlspecialchars($_SESSION['discount_code']); ?>):</span>
        <span>-<?php echo number_format($discountAmount, 0, ',', '.'); ?> VND</span>
      </div>
    <?php endif; ?>

      <div class="discount-code">
        <label for="discount-input">
          Discount Code
          <span
            class="tooltip-icon"
            data-tooltip="Enter 'TECHSHOP10' for a 10% discount."
            >ℹ️</span
          >
        </label>
        <div class="discount-input-group">
            <input type="text" id="discount-input" placeholder="Enter discount code" value="<?php echo isset($_SESSION['discount_code']) ? htmlspecialchars($_SESSION['discount_code']) : ''; ?>"/>
            <button onclick="checkDiscount()" id="apply-discount">Apply</button>
        </div>
        <div class="discount-message" id="discount-message" style="color: #e63946;">
            <?php echo $discountError ?? ''; ?>
        </div>
      </div>

      <div class="summary-total">
        <span>Total:</span>
        <span id="total"><?php echo number_format($total, 0, ',', '.'); ?> VND</span>
      </div>

      <a href="checkout.php"><button class="checkout-btn">🧾 Proceed to Checkout</button></a>
      <a href="product.php" class="continue-shopping">← Continue Shopping</a>

      <div class="support-info">
        <p>
          📞 Need help? Call <a href="tel:0896492400">0896 492 400</a> or email
          <a href="mailto:support@yourshop.com">levanhuy06042003@gmail.com</a>
        </p>
      </div>
    </aside>
  </div>
  <?php endif; ?>
</main>

<?php if (!empty($cartItems)): ?>
<section class="related-products" aria-label="Products you may like">
  <h2>✨ Products You May Like</h2>
  <div class="products-list">
    <?php foreach ($suggestedProducts as $p): ?>
        <div class="product-card">
          <img src="../img/product/<?php echo htmlspecialchars($p['image']); ?>" 
               alt="<?php echo htmlspecialchars($p['name']); ?>"
               onerror="this.src='../img/product/default.jpg'" />
          <h4><?php echo htmlspecialchars(mb_strimwidth($p['name'], 0, 20, '...')); ?></h4>
          <p class="price"><?php echo number_format($p['price'], 0, ',', '.'); ?> VND</p>
          <button onclick="addToCartFromRelated(<?php echo $p['id']; ?>)">➕ Add to Cart</button>
        </div>
    <?php endforeach; ?>
    <div class="see-more-wrapper">
      <a href="product.php" class="circle-arrow-btn" aria-label="See more products">
        <i class="fas fa-arrow-right"></i>
      </a>
    </div>
  </div>
</section>
<?php endif; ?>

    <?php include '../includes/footer.php'; ?>

    <script>
        function updateQuantity(productId, newQuantity) {
            if (newQuantity < 1) return;
            
            fetch('cart_actions.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=update&product_id=' + productId + '&quantity=' + newQuantity
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        }

        function removeItem(productId) {
            if (confirm('Are you sure you want to remove this item from your cart?')) {
                fetch('cart_actions.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=remove&product_id=' + productId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
            }
        }

        function addToCartFromRelated(productId) {
            fetch('../includes/add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'product_id=' + productId + '&quantity=1'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Product added to cart!');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        }

        function checkDiscount() {
            const discountCode = document.getElementById('discount-input').value;

            const formData = new URLSearchParams();
            formData.append('action', 'apply_discount');
            formData.append('discount_code', discountCode);

            fetch('cart_actions.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Reload the page to see the changes from the server
                location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                // Even on error, reload to see any server-side error messages
                location.reload();
            });
        }
    </script>
</body>
</html>