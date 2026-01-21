<?php
// File: pages/product.php
session_start();
require_once '../includes/database.php';

$db = Database::getInstance();

// ==========================================================================
// 1. NHẬN THAM SỐ LỌC (FILTER PARAMETERS)
// ==========================================================================

// Lấy các tham số từ URL, gán giá trị mặc định nếu không có
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 1; // Mặc định là Laptop
$brand = isset($_GET['brand']) ? $_GET['brand'] : '';
$min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : 0;
$max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : 0;
$tag = isset($_GET['tag']) ? $_GET['tag'] : '';
$chip = isset($_GET['chip']) ? $_GET['chip'] : '';
$screen = isset($_GET['screen']) ? $_GET['screen'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';

// ==========================================================================
// 2. XÂY DỰNG TRUY VẤN SẢN PHẨM (QUERY BUILDER)
// ==========================================================================

$query = "SELECT p.*, c.name as category_name, c.parent_id as category_parent_id 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          WHERE p.status = 'active'";

$params = [];

// --- Lọc theo Danh mục (Category) ---
if ($category_id > 0) {
    // Lấy tất cả sub-categories của category này
    $subCategoryIds = [$category_id];
    $stmt = $db->prepare("SELECT id FROM categories WHERE parent_id = :parent_id");
    $stmt->bindValue(':parent_id', $category_id);
    $stmt->execute();
    $subCats = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    $subCategoryIds = array_merge($subCategoryIds, $subCats);
    
    $placeholders = implode(',', array_fill(0, count($subCategoryIds), '?'));
    $query .= " AND p.category_id IN ($placeholders)";
    
    foreach ($subCategoryIds as $index => $catId) {
        $params[$index] = $catId;
    }
}

// --- Lọc theo Thương hiệu (Brand) ---
if (!empty($brand)) {
    $query .= " AND p.brand = ?";
    $params[] = $brand;
}

// --- Lọc theo Khoảng giá (Price Range) ---
if ($min_price > 0) {
    $query .= " AND p.price >= ?";
    $params[] = $min_price;
}
if ($max_price > 0) {
    $query .= " AND p.price <= ?";
    $params[] = $max_price;
}

// --- Lọc theo Từ khóa (Tag/Name) ---
if (!empty($tag)) {
    $query .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = '%' . $tag . '%';
    $params[] = '%' . $tag . '%';
}

// --- Lọc theo Chip (CPU) ---
if (!empty($chip)) {
    $query .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = '%' . $chip . '%';
    $params[] = '%' . $chip . '%';
}

// --- Lọc theo Màn hình (Screen) ---
if (!empty($screen)) {
    $query .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = '%' . $screen . '"%';
    $params[] = '%' . $screen . ' inch%';
}

// --- Sắp xếp (Sorting) ---
switch ($sort) {
    case 'price_asc':
        $query .= " ORDER BY p.price ASC";
        break;
    case 'price_desc':
        $query .= " ORDER BY p.price DESC";
        break;
    case 'newest':
        $query .= " ORDER BY p.created_at DESC";
        break;
    case 'popular':
        $query .= " ORDER BY p.views DESC";
        break;
    default:
        $query .= " ORDER BY p.created_at DESC";
        break;
}

// --- Thực thi truy vấn ---
$stmt = $db->prepare($query);
foreach ($params as $index => $value) {
    $stmt->bindValue($index + 1, $value);
}
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ==========================================================================
// 3. LẤY DỮ LIỆU BỔ TRỢ (SIDEBAR & BREADCRUMB)
// ==========================================================================

// Lấy thông tin category hiện tại để hiển thị tiêu đề/breadcrumb
$category_query = "SELECT * FROM categories WHERE id = ?";
$stmt = $db->prepare($category_query);
$stmt->bindValue(1, $category_id);
$stmt->execute();
$current_category = $stmt->fetch(PDO::FETCH_ASSOC);

// Nếu là sub-category, lấy thông tin parent category
if ($current_category && $current_category['parent_id']) {
    $parent_query = "SELECT * FROM categories WHERE id = ?";
    $stmt = $db->prepare($parent_query);
    $stmt->bindValue(1, $current_category['parent_id']);
    $stmt->execute();
    $parent_category = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Lấy danh sách Category cha cho Sidebar
$stmt = $db->query("SELECT * FROM categories WHERE status = 'active' AND (parent_id IS NULL OR parent_id = 0) ORDER BY name");
$parentCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy danh sách tất cả thương hiệu cho bộ lọc
$stmt = $db->query("SELECT * FROM brands ORDER BY name ASC");
$brands = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy danh sách thương hiệu nổi bật (có logo) cho Sidebar
$stmt = $db->query("SELECT * FROM brands WHERE logo IS NOT NULL AND logo != '' ORDER BY RAND() LIMIT 4");
$featuredBrands = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Xác định title trang
$page_title = $current_category ? $current_category['name'] . ' - TechShop' : 'Products - TechShop';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/shop.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/product.js"></script>
</head>
<body>
    <?php 
    require_once '../includes/header.php'; 
    ?>

<div class="container">
  <div class="sidebar">
    <form action="search.php" method="GET" class="search-box">
      <input type="text" name="q" placeholder="Search products...">
      <button type="submit"><i class="fas fa-search"></i></button>
    </form>
    <h4>Categories</h4>
    <ul class="category-list">
      <?php foreach ($parentCategories as $parentCategory): 
          $is_current = ($current_category && 
                        ($current_category['id'] == $parentCategory['id'] || 
                         ($current_category['parent_id'] && $current_category['parent_id'] == $parentCategory['id'])));
      ?>
      <li class="category-item <?php echo $is_current ? 'active' : ''; ?>">
        <a href="product.php?category_id=<?php echo $parentCategory['id']; ?>">
          <?php echo htmlspecialchars($parentCategory['name']); ?>
        </a>
      </li>
      <?php endforeach; ?>
    </ul>

    <h4>Filter by Brand</h4>
    <?php foreach ($brands as $brandItem): 
        $isSelected = ($brand == $brandItem['name']);
        $urlParams = $_GET;
        if ($isSelected) {
            unset($urlParams['brand']); // Bỏ chọn nếu đang active
        } else {
            $urlParams['brand'] = $brandItem['name']; // Chọn brand mới
        }
        $brandUrl = '?' . http_build_query($urlParams);
    ?>
    <label>
      <input type="checkbox" onclick="window.location.href='<?php echo $brandUrl; ?>'"
             <?php echo $isSelected ? 'checked' : ''; ?>>
      <?php echo htmlspecialchars($brandItem['name']); ?>
    </label>
    <?php endforeach; ?>
    
    <div class="brand-logos">
      <h4>Featured Brands</h4>
      <div class="logo-list">
        <?php foreach ($featuredBrands as $fb): ?>
        <a class="brand" href="product.php?brand=<?php echo urlencode($fb['name']); ?>">
            <img src="../img/brands/<?php echo htmlspecialchars($fb['logo']); ?>" alt="<?php echo htmlspecialchars($fb['name']); ?>" />
        </a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <div class="content">
    <div class="top-bar">
      <nav class="breadcrumb">
        <ul>
          <li><a href="shop.php"><i class="fas fa-home"></i> Shop</a></li>
          <?php if (isset($parent_category)): ?>
          <li><a href="product.php?category_id=<?php echo $parent_category['id']; ?>"><?php echo htmlspecialchars($parent_category['name']); ?></a></li>
          <?php endif; ?>
          <li class="current"><a href="product.php?category_id=<?php echo $category_id; ?>"><?php echo $current_category ? htmlspecialchars($current_category['name']) : 'Products'; ?></a></li>
          <?php if (!empty($brand)): ?>
          <li class="current"><?php echo htmlspecialchars($brand); ?></li>
          <?php endif; ?>
        </ul>
      </nav>
      
      <select id="sortSelect" onchange="sortProducts(this.value)">
        <option value="">Sort by</option>
        <option value="price_asc" <?php echo $sort == 'price_asc' ? 'selected' : ''; ?>>Price: Low to High</option>
        <option value="price_desc" <?php echo $sort == 'price_desc' ? 'selected' : ''; ?>>Price: High to Low</option>
        <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Newest</option>
        <option value="popular" <?php echo $sort == 'popular' ? 'selected' : ''; ?>>Most Popular</option>
      </select>
    </div>
    
    <div class="product-list">
      <?php if (empty($products)): ?>
        <div class="no-products">
          <h3>No products found in this category.</h3>
          <p>Try selecting different filters or browse other categories.</p>
          <a href="shop.php" class="btn-back">Back to Shop</a>
        </div>
      <?php else: ?>
        <?php foreach ($products as $product): ?>
        <!-- ĐÃ SỬA: Sử dụng class item-card từ shop.css -->
        <div class="item-card" onclick="window.location.href='product-detail.php?id=<?php echo $product['id']; ?>'">
          <?php if ($product['discount'] > 0): ?>
          <div class="discount-badge">-<?php echo $product['discount']; ?>%</div>
          <?php endif; ?>
          
          <img src="../img/product/<?php echo htmlspecialchars($product['image'] ?? 'default.jpg'); ?>" 
               alt="<?php echo htmlspecialchars($product['name']); ?>"
               onerror="this.src='../img/product/default.jpg'">
          <h3 class="item-name"><?php echo htmlspecialchars($product['name']); ?></h3>
          <div class="item-price">
            <?php if ($product['discount'] > 0): ?>
            <span style="text-decoration: line-through; color: #999; font-size: 14px; margin-right: 5px; font-weight: normal;">
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
          <!-- ĐÃ SỬA: Thêm event.stopPropagation() để ngăn chặn click lan ra ngoài -->
          <button class="add-to-cart-btn" onclick="event.stopPropagation(); addToCart(<?php echo $product['id']; ?>)">Add to Cart</button>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
    
    <div class="pagination">
      <button class="page-btn">1</button>
      <button class="page-btn">2</button>
      <button class="page-btn">3</button>
      <button class="page-btn next">&gt;</button>
    </div>
  </div>
</div>

<?php 
require_once '../includes/footer.php'; 
?>

<script>
// Debug: Kiểm tra xem script có chạy không
console.log('=== PRODUCT PAGE SCRIPT LOADED ===');

function addToCart(productId, quantity = 1) {
    console.log(`Adding product ${productId} with quantity ${quantity} to cart.`);

    const formData = new URLSearchParams();
    formData.append('product_id', productId);
    formData.append('quantity', quantity);

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
            alert(data.message || 'Product added to cart!');
            // Reload the page to update header cart count and other elements
            window.location.reload();
        } else {
            alert('Error: ' + (data.message || 'Could not add product to cart.'));
        }
    })
    .catch(error => {
        console.error('Fetch Error:', error);
        alert('An error occurred while adding the item to the cart.');
    });

    return false; // Prevent default form submission or link navigation
}

// Update cart count display
function updateCartCount(count) {
    console.log('Updating cart count to:', count);
    
    // Tìm hoặc tạo cart count element
    let cartCount = document.querySelector('.cart-count');
    
    if (!cartCount) {
        // Tìm cart icon
        const cartIcons = document.querySelectorAll('.fa-shopping-cart');
        const cartIcon = cartIcons[0]?.parentElement;
        
        if (cartIcon) {
            cartCount = document.createElement('span');
            cartCount.className = 'cart-count';
            cartCount.style.cssText = `
                position: absolute;
                top: -5px;
                right: -5px;
                background: red;
                color: white;
                font-size: 12px;
                width: 18px;
                height: 18px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
            `;
            cartIcon.style.position = 'relative';
            cartIcon.appendChild(cartCount);
        }
    }
    
    if (cartCount) {
        cartCount.textContent = count;
        cartCount.style.display = count > 0 ? 'flex' : 'none';
    }
}

// Sort products function
function sortProducts(sortValue) {
    const url = new URL(window.location.href);
    
    if (sortValue) {
        url.searchParams.set('sort', sortValue);
    } else {
        url.searchParams.delete('sort');
    }
    
    window.location.href = url.toString();
}

// Category mega menu
document.addEventListener('DOMContentLoaded', () => console.log('DOM fully loaded'));
</script>

</body>
</html>