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

<script src="/assets/js/cart.js"></script>
<?php include '../includes/footer.php'; ?>
.