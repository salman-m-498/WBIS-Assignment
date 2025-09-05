<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/sale_functions.php';

// Page variables
$page_title = "Sale";
$page_description = "Amazing deals on toys and games - up to 70% off!";
$show_breadcrumb = true;
$breadcrumb_items = [
    ['url' => 'sale.php', 'title' => 'Sale']
];

// Fetch data
$saleCategories = getSaleCategories($pdo);
$featuredProducts = getFeaturedSaleProducts($pdo);
$flashProducts = getFlashSaleProducts($pdo);
$clearanceProducts = getClearanceProducts($pdo);

// Include header
include '../includes/header.php';
?>

<!-- Sale Categories Section -->
<div class="sale-page">
<section class="sale-categories-section">
    <div class="container">
        <div class="sale-categories-grid">
            <?php foreach ($saleCategories as $cat): ?>
                <?php $category_image_path = str_replace("root/", "", $cat['image']); ?>
                <div class="sale-category">
                    <div class="category-image">
                        <img src="/<?= htmlspecialchars($category_image_path) ?>" alt="<?= htmlspecialchars($cat['name']) ?>">
                    </div>
                    <div class="category-content">
                        <h3><?= htmlspecialchars($cat['name']) ?></h3>
                        <p>Up to RM<?= number_format($cat['discount_amount'], 2) ?> off</p>
                        <a href="products.php?category=<?= $cat['category_id'] ?>&sale=1" class="btn btn-primary">Shop Now</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Sale Products -->
<section class="featured-sale-products-section">
    <div class="container">
        <div class="products-grid">
            <?php foreach ($featuredProducts as $product): ?>
                <?php $product_image_path = str_replace("root/", "", $product['image']); ?>
                <div class="product-card sale-product">
                    <div class="product-image">
                        <img src="/<?= htmlspecialchars($product_image_path) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                        <div class="product-badge sale">
                            <?= round(100 - ($product['sale_price'] / $product['price']) * 100) ?>% OFF
                        </div>
                    </div>
                    <div class="product-content">
                        <h3><a href="product.php?id=<?= $product['product_id'] ?>"><?= htmlspecialchars($product['name']) ?></a></h3>
                        <div class="product-price">
                            <span class="current-price">RM<?= number_format($product['sale_price'], 2) ?></span>
                            <span class="original-price">RM<?= number_format($product['price'], 2) ?></span>
                        </div>
                        <button class="add-to-cart" data-product-id="<?= $product['product_id'] ?>">Add to Cart</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Flash Sale Section -->
<section class="flash-sale-section">
    <div class="container">
        <div class="flash-products-grid">
            <?php foreach ($flashProducts as $product): ?>
                <?php $product_image_path = str_replace("root/", "", $product['image']); ?>
                <div class="flash-product">
                    <div class="flash-product-image">
                        <img src="/<?= htmlspecialchars($product_image_path) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                        <div class="flash-badge">FLASH SALE</div>
                    </div>
                    <div class="flash-product-content">
                        <h3><?= htmlspecialchars($product['name']) ?></h3>
                        <div class="flash-price">
                            <span class="current-price">RM<?= number_format($product['sale_price'], 2) ?></span>
                            <span class="original-price">RM<?= number_format($product['price'], 2) ?></span>
                            <span class="discount-percent"><?= round(100 - ($product['sale_price'] / $product['price']) * 100) ?>% OFF</span>
                        </div>
                        <button class="btn btn-primary" data-product-id="<?= $product['product_id'] ?>">Add to Cart</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Clearance Section -->
<section class="clearance-section">
    <div class="container">
        <div class="products-grid">
            <?php foreach ($clearanceProducts as $product): ?>
                <?php $product_image_path = str_replace("root/", "", $product['image']); ?>
                <div class="product-card clearance-product">
                    <div class="product-image">
                        <img src="/<?= htmlspecialchars($product_image_path) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                        <div class="product-badge clearance">CLEARANCE</div>
                    </div>
                    <div class="product-content">
                        <h3><a href="product.php?id=<?= $product['product_id'] ?>"><?= htmlspecialchars($product['name']) ?></a></h3>
                        <div class="product-price">
                            <span class="current-price">RM<?= number_format($product['sale_price'], 2) ?></span>
                            <span class="original-price">RM<?= number_format($product['price'], 2) ?></span>
                        </div>
                        <button class="add-to-cart" data-product-id="<?= $product['product_id'] ?>">Add to Cart</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
</div>

<script src="/assets/js/cart.js"></script>

<?php include '../includes/footer.php'; ?>
