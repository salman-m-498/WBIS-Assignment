<?php
session_start();
require_once '../includes/db.php';

// Get products by category_id if provided
if (isset($_GET['category_id'])) {
    $category_id = $_GET['category_id'];
    $stmt = $pdo->prepare("SELECT * FROM products WHERE category_id = ? AND status = 'active'");
    $stmt->execute([$category_id]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Default: get all products
    $stmt = $pdo->query("SELECT * FROM products WHERE status = 'active'");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$category = $_GET['category_id'] ?? $_GET['category'] ?? '';

// Page variables
$page_title = "Products";
$page_description = "Browse our complete collection of toys and games for all ages";
$show_breadcrumb = true;
$breadcrumb_items = [
    ['url' => 'products.php', 'title' => 'Products']
];

// Get filter parameters
$sort = $_GET['sort'] ?? 'name';
$price_range = $_GET['price_range'] ?? '';
$price_min = '';
$price_max = '';

$page =  max(1, (int)($_GET['page'] ?? 1));
$per_page = 24;
$offset = ($page - 1) * $per_page;

$where = ["status = 'active'"];
$params = [];

if ($category) {
    // Check if selected category has subcategories
    $subQuery = $pdo->prepare("SELECT category_id FROM categories WHERE parent_id = ?");
    $subQuery->execute([$category]);
    $children = $subQuery->fetchAll(PDO::FETCH_COLUMN);

    if ($children) {
        // If category has children, filter by all child category_ids
        $placeholders = implode(',', array_fill(0, count($children), '?'));
        $where[] = "category_id IN ($placeholders)";
        $params = array_merge($params, $children);
    } else {
        // Otherwise, filter directly
        $where[] = "category_id = ?";
        $params[] = $category;
    }
}
if ($price_range) {
     if (strpos($price_range, '+') !== false) {
        // e.g. "1000+"
        $min = (int) rtrim($price_range, '+');
        $where[] = "sale_price >= ?";
        $params[] = $min;
        $price_min = (string)$min;
        $price_max = '';
    } else {
        // standard "min-max"
        [$min, $max] = explode('-', $price_range);
        $min = (int)$min;
        $max = (int)$max;
        $where[] = "sale_price BETWEEN ? AND ?";
        $params[] = $min;
        $params[] = $max;
        $price_min = (string)$min;
        $price_max = (string)$max;
    }
}
$where_sql = implode(" AND ", $where);

// Sorting
$sort_sql = match($sort) {
    "price_asc" => "sale_price ASC",
    "price_desc" => "sale_price DESC",
    default => "name ASC"
};

// Count total
$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE $where_sql");
$count_stmt->execute($params);
$total_products = $count_stmt->fetchColumn();
$total_pages = ceil($total_products / $per_page);

// Fetch products
$sql = "SELECT * FROM products WHERE $where_sql ORDER BY $sort_sql LIMIT $per_page OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../includes/header.php'; ?>

<!-- Flash message container --> 
 <div id="flash-message" style="display:none; position: fixed; top: 20px; right: 20px; min-width: 200px; padding: 12px 18px; border-radius: 8px; font-size: 14px; z-index: 9999; box-shadow: 0 4px 8px #000;"> </div>
<!-- Page Header -->
<section class="page-header" style="background: linear-gradient(to right, #fddde6, #fceabb); padding: 3em 0; border-radius: 0 0 50px 50px;">
    <div class="container">
        <h1 style="color: #333;">All Products</h1>
        <p style="color: #333;">Discover amazing toys and games for every age and interest</p>
    </div>
</section>

<!-- Filter Toggle Button -->
<div class="filter-toggle-wrapper">
    <button type="button" class="filter-toggle">☰ Filters</button>
</div>
<div class="sidebar-overlay"></div>


<!-- Products Section -->
<section class="products-section">
    <div class="container">
        <div class="products-layout">

            <!-- Main Products Content -->
             <main class="products-content">
                <div class="products-header">
                    <h2>All Products</h2>
                </div>
                <div class="products-grid">
                    <?php foreach ($products as $product): ?>
                        <?php
                            $image_path = str_replace("root/", "", $product['image']);
                            $product_url = "product.php?id=" . urlencode($product['product_id']);
                        ?>
                        <?php
                        $isIn = false;
                        if (isset($_SESSION['user_id'])) {
                        $inWishlist = $pdo->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = ? AND product_id = ?");
                        $inWishlist->execute([$_SESSION['user_id'], $product['product_id']]);
                        $isIn = $inWishlist->fetchColumn() > 0;
                        }
                        ?>

                        <div class="product-card">
                            <div class="product-image">
                                <a href="<?= $product_url ?>">
                                    <img src="/<?= htmlspecialchars($image_path) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                                </a>
                               <div class="product-overlay">
                                 <button class="quick-view" onclick="window.location='<?= $product_url ?>'">Quick View</button>
                                 <button class="add-to-wishlist <?= $isIn ? 'in-wishlist' : '' ?>" data-product-id="<?= $product['product_id'] ?>"><i class="<?= $isIn ? 'fas fa-heart' : 'far fa-heart' ?>"></i></button>
                                </div>
                               <?php if ($product['sale_price'] < $product['price']): ?>
                                  <div class="product-badge sale">Sale</div>
                              <?php endif; ?>
                           </div>

                        <div class="product-content">
                             <h3><a href="<?= $product_url ?>"><?= htmlspecialchars($product['name']) ?></a></h3>
                             <div class="product-price">
                                  <span class="current-price">RM<?= number_format($product['sale_price'], 2) ?></span>
                                  <?php if ($product['sale_price'] < $product['price']): ?>
                                    <span class="original-price">RM<?= number_format($product['price'], 2) ?></span>
                                  <?php endif; ?>
                            </div>
                            <button class="add-to-cart" data-product-id="<?= $product['product_id'] ?>">Add to Cart</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

            <!-- Pagination -->
            <div class="pagination">
                <ul class="pagination-list">
                    <?php if ($page > 1): ?>
                        <li><a href="?<?= http_build_query(array_merge($_GET, ['page'=>1])) ?>">First</a></li>
                        <li><a href="?<?= http_build_query(array_merge($_GET, ['page'=>$page-1])) ?>">Prev</a></li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li><a class="<?= $page==$i?'active':'' ?>" href="?<?= http_build_query(array_merge($_GET, ['page'=>$i])) ?>"><?= $i ?></a></li>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <li><a href="?<?= http_build_query(array_merge($_GET, ['page'=>$page+1])) ?>">Next</a></li>
                        <li><a href="?<?= http_build_query(array_merge($_GET, ['page'=>$total_pages])) ?>">Last</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </main>
        
            <!-- Sidebar Filters -->
            <aside class="products-sidebar">
                <form method="get" id="filterForm">
                    <div class="filter-section">
                    <h3>Filters</h3>

                    <!-- Preserve current sort and reset to page 1 when applying filters -->
                    <input type="hidden" name="page" value="1">
                    <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">
                    
                    <!-- Category Filter -->
                    <div class="filter-group">
                        <h4>Category</h4>
                        <ul class="filter-list">
                            <li><label><input type="radio" name="category" value="C001" <?= $category=="C001"?"checked":"" ?>> Action Figures</label></li>
                            <li><label><input type="radio" name="category" value="C002" <?= $category=="C002"?"checked":"" ?>> Building Blocks</label></li>
                            <li><label><input type="radio" name="category" value="C003" <?= $category=="C003"?"checked":"" ?>> Cars, Trucks, Trains</label></li>
                            <li><label><input type="radio" name="category" value="C004" <?= $category=="C004"?"checked":"" ?>> Dolls</label></li>
                            <li><label><input type="radio" name="category" value="C005" <?= $category=="C005"?"checked":"" ?>> Games & Puzzles</label></li>
                            <li><label><input type="radio" name="category" value="C006" <?= $category=="C006"?"checked":"" ?>> Outdoor & Sports</label></li>
                            <li><label><input type="radio" name="category" value="C007" <?= $category=="C007"?"checked":"" ?>> Pretend Play & Costumes</label></li>
                            <li><label><input type="radio" name="category" value="C008" <?= $category=="C008"?"checked":"" ?>> Blind Box</label></li>
                            <li><label><input type="radio" name="category" value="C009" <?= $category=="C009"?"checked":"" ?>> Soft Toys</label></li>
                            <li><label><input type="radio" name="category" value="" <?= $category==''?"checked":"" ?>> Any Category</label></li>
                        </ul>
                    </div>
                    
                    <!-- Price Filter -->
                   <div class="filter-group">
                        <h4>Price Range</h4>
                        <ul class="filter-list">
                            <li><label><input type="radio" name="price_range" value="0-100" <?= $price_range=="0-100"?"checked":"" ?>> RM0 - RM100</label></li>
                            <li><label><input type="radio" name="price_range" value="100-200" <?= $price_range=="100-200"?"checked":"" ?>> RM100 - RM200</label></li>
                            <li><label><input type="radio" name="price_range" value="200-500" <?= $price_range=="200-500"?"checked":"" ?>> RM200 - RM500</label></li>
                            <li><label><input type="radio" name="price_range" value="500-1000" <?= $price_range=="500-1000"?"checked":"" ?>> RM500 - RM1000</label></li>
                            <li><label><input type="radio" name="price_range" value="" <?= $price_range=='' ? "checked" : "" ?>> Any Price</label></li>
                        </ul>
                    </div>
                    
            
                    <!-- Filter actions -->
                    <div class="filter-actions" style="margin-top:1em; display:flex; gap:10px;">
                        <button type="submit" class="btn btn-primary apply-filters">Apply Filters</button>
                        <button type="button" class="btn btn-outline clear-filters">Clear All Filters</button>
                    </div>
                </div>
            </form>
            </aside>
        </div>
    </div>
</section>

<!-- Quick View Modal -->
<div id="quick-view-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div class="quick-view-content">
            <!-- Quick view content will be loaded here -->
        </div>
    </div>
</div>

<!-- Adding to wishlist button -->
<script>
function showFlashMessage(message, type = 'success') {
    const flash = document.getElementById('flash-message');
    flash.textContent = message;
    flash.style.display = 'block';
    flash.style.backgroundColor = (type === 'success') ? '#d4edda' : '#f8d7da';
    flash.style.color = (type === 'success') ? '#155724' : '#721c24';
    flash.style.border = (type === 'success') ? '1px solid #c3e6cb' : '1px solid #f5c6cb';

    setTimeout(() => {
        flash.style.display = 'none';
    }, 3000); // Not working for remove for some reason
}
// Wishlist flash
document.addEventListener('DOMContentLoaded', () => {
    const wishlistButtons = document.querySelectorAll('.add-to-wishlist');
    
    wishlistButtons.forEach(button => {
        button.addEventListener('click', () => {
            const productId = button.dataset.productId;

            fetch('/member/toggle-wishlist.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `product_id=${productId}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (data.action === 'added') {
                        button.innerHTML = '<i class="fas fa-heart"></i>';
                        button.classList.add('in-wishlist');
                        showFlashMessage('Added to wishlist!', 'success');
                    } else if (data.action === 'removed') {
                        button.innerHTML = '<i class="far fa-heart"></i>';
                        button.classList.remove('in-wishlist');
                        showFlashMessage('Removed from wishlist', 'error');
                    }
                } else {
                    showFlashMessage(data.message || 'Something went wrong', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                showFlashMessage('Unexpected error', 'error');
            });
        });

    });

     // ===== Sidebar Toggle =====
    const toggleBtn = document.querySelector('.filter-toggle');
    const sidebar = document.querySelector('.products-sidebar');
    const overlay = document.querySelector('.sidebar-overlay');

    if (toggleBtn && sidebar && overlay) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.add('active');
            overlay.classList.add('active');
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });
    }

    // ===== Filter form submit: close drawer (and let GET submit happen) =====
    const filterForm = document.getElementById('filterForm');
    if (filterForm) {
        filterForm.addEventListener('submit', () => {
            if (sidebar && overlay) {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            }
            // allow normal GET submission to proceed
        });
    }

    // ===== Clear All Filters button =====
    const clearBtn = document.querySelector('.clear-filters');
    if (clearBtn) {
        clearBtn.addEventListener('click', (e) => {
            e.preventDefault();
            // close the sidebar first for immediate UX feedback
            if (sidebar && overlay) {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            }
            // redirect to base products page (clears all GET params)
            window.location.href = 'products.php';
        });
    }
});
</script>

<script src="/assets/js/cart.js"></script>

<?php
// Include footer
include '../includes/footer.php';
?> 