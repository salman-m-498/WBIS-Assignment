<?php
session_start();
require_once '../includes/db.php';

// Page variables
$page_title = "New Arrivals";
$page_description = "Discover the latest toys and games just arrived in our store!";
$show_breadcrumb = true;
$breadcrumb_items = [
    ['url' => 'new_arrivals.php', 'title' => 'New Arrivals']
];

// Fetch latest products (limit 12)
$limit = 12;
$stmt = $pdo->prepare("
    SELECT * 
    FROM products 
    WHERE status = 'active'
    ORDER BY created_at DESC
    LIMIT ?
");
$stmt->bindValue(1, $limit, PDO::PARAM_INT);
$stmt->execute();
$newProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Include header
include '../includes/header.php';
?>

<!-- Flash message container -->
<div id="flash-message" style="display:none; position: fixed; top: 20px; right: 20px; min-width: 200px; padding: 12px 18px; border-radius: 8px; font-size: 14px; z-index: 9999; box-shadow: 0 4px 8px #000;"> </div>
<!-- New Arrivals Section -->
<section class="new-arrivals-page">
    <div class="container">
        <div class="page-header">
            <h1>New Arrivals</h1>
            <p>Check out the latest toys and games for all ages!</p>
        </div>

        <div class="products-grid">
            <?php foreach ($newProducts as $product): ?>
                <?php $product_image_path = str_replace("root/", "", $product['image']); ?>
                <div class="product-card new-product">
                    <div class="product-image">
                        <a href="product.php?id=<?= $product['product_id'] ?>">
                            <img src="/<?= htmlspecialchars($product_image_path) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                        </a>
                        <div class="product-badge new">NEW</div>
                    </div>
                    <div class="product-content">
                        <h3><a href="product.php?id=<?= $product['product_id'] ?>"><?= htmlspecialchars($product['name']) ?></a></h3>
                        <div class="product-price">
                            <?php if ($product['sale_price'] && $product['sale_price'] < $product['price']): ?>
                                <span class="current-price">RM<?= number_format($product['sale_price'], 2) ?></span>
                                <span class="original-price">RM<?= number_format($product['price'], 2) ?></span>
                            <?php else: ?>
                                <span class="current-price">RM<?= number_format($product['price'], 2) ?></span>
                            <?php endif; ?>
                        </div>
                        <button class="add-to-cart" data-product-id="<?= $product['product_id'] ?>">Add to Cart</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

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
    }, 3000);
}
</script>
<script src="/assets/js/cart.js"></script>
<?php include '../includes/footer.php'; ?>
.