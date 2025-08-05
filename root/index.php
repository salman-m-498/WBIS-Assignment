<?php
session_start();

// DB connection
$pdo = new PDO("mysql:host=localhost;dbname=web_db;charset=utf8mb4", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Get featured products
$stmt = $pdo->query("SELECT * FROM products WHERE featured = 1 ");
$featured_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Page variables
$page_title = "Home";
$page_description = "Discover amazing toys and games for all ages at ToyLand Store. Free shipping on orders over $50!";
$show_breadcrumb = false;

// Include header
include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="hero-content">
            <h1>Where Fun Comes to Life!</h1>
            <p>Discover toys that spark imagination and smiles.</p>
            <div class="hero-buttons">
                <button class="btn btn-primary" onclick="launchConfetti()">Shop Now</button>
                <a href="public/sale.php" class="btn btn-secondary">View Sale</a>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section">
    <div class="container">
        <div class="features-grid">
            <div class="feature-item">🚀 Fast Delivery</div>
            <div class="feature-item">🧸 Unique Characters</div>
            <div class="feature-item">🎁 Gift Ready</div>
            <div class="feature-item">🛡️ Safe & Secure</div>
        </div>
    </div>
</section>

<section class="categories-section">
    <div class="container">
        <div class="section-header">
            <h2>Shop by Category</h2>
            <p>Find the perfect toy for every age and interest</p>
        </div>

        <div class="categories-grid">
            <?php

            // Fetch top-level categories (no parent_id)
            $stmt = $pdo->query("SELECT * FROM categories WHERE parent_id IS NULL AND status = 'active' ORDER BY name");
            $categories = $stmt->fetchAll();

            foreach ($categories as $category):
                $category_url = 'public/categories.php?category_id=' . urlencode($category['category_id']);
                $image_path = htmlspecialchars($category['image'] ?? 'assets/images/default-category.jpg');
            ?>
                <div class="category-card">
                    <div class="category-image">
                        <img src="<?= $image_path ?>" alt="<?= htmlspecialchars($category['name']) ?>">
                    </div>
                    <div class="category-content">
                        <h3><?= htmlspecialchars($category['name']) ?></h3>
                        <p><?= htmlspecialchars($category['description']) ?></p>
                        <a href="<?= $category_url ?>" class="btn btn-outline">Shop Now</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="featured-products-section">
    <div class="container">
        <div class="section-header">
            <h2>Featured Products</h2>
            <p>Our most popular and highly-rated toys</p>
        </div>
        
        <div class="products-grid">
            <?php foreach ($featured_products as $product): ?>
                <?php
                    $image_path = str_replace("root/", "", $product['image']);
                    $product_url = "public/product.php?id=" . urlencode($product['product_id']);
                ?>
                <div class="product-card">
                    <div class="product-image">
                        <a href="<?= $product_url ?>">
                            <img src="<?= htmlspecialchars($image_path) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                        </a>
                        <div class="product-overlay">
                            <button class="quick-view" data-product-id="<?= $product['product_id'] ?>">Quick View</button>
                            <button class="add-to-wishlist" data-product-id="<?= $product['product_id'] ?>"><i class="far fa-heart"></i></button>
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

        <div class="view-all-products">
            <a href="public/products.php" class="btn btn-primary">View All Products</a>
        </div>
    </div>
</section>

<!-- Promotional Section -->
<section class="promotional-section">
    <div class="container">
        <div class="promo-grid">
            <div class="promo-card">
                <div class="promo-content">
                    <h2>New Arrivals</h2>
                    <p>Check out the latest toys and games that just arrived</p>
                    <a href="public/products.php?filter=new" class="btn btn-white">Shop New</a>
                </div>
                <div class="promo-image">
                    <img src="assets/images/promo-new-arrivals.jpg" alt="New Arrivals">
                </div>
            </div>
            
            <div class="promo-card">
                <div class="promo-content">
                    <h2>Sale Items</h2>
                    <p>Up to 50% off on selected toys and games</p>
                    <a href="public/sale.php" class="btn btn-white">Shop Sale</a>
                </div>
                <div class="promo-image">
                    <img src="assets/images/promo-sale.jpg" alt="Sale Items">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="testimonials-section">
    <div class="container">
        <div class="section-header">
            <h2>What Our Customers Say</h2>
            <p>Real reviews from happy families</p>
        </div>
        
        <div class="testimonials-grid">
            <div class="testimonial-card">
                <div class="testimonial-content">
                    <p>"Amazing selection of educational toys. My kids love learning through play!"</p>
                </div>
                <div class="testimonial-author">
                    <img src="assets/images/customer-1.jpg" alt="Sarah Johnson">
                    <div class="author-info">
                        <h4>Sarah Johnson</h4>
                        <span>Verified Customer</span>
                    </div>
                </div>
            </div>
            
            <div class="testimonial-card">
                <div class="testimonial-content">
                    <p>"Fast shipping and excellent customer service. Highly recommended!"</p>
                </div>
                <div class="testimonial-author">
                    <img src="assets/images/customer-2.jpg" alt="Mike Davis">
                    <div class="author-info">
                        <h4>Mike Davis</h4>
                        <span>Verified Customer</span>
                    </div>
                </div>
            </div>
            
            <div class="testimonial-card">
                <div class="testimonial-content">
                    <p>"Quality toys at great prices. Perfect for birthday gifts!"</p>
                </div>
                <div class="testimonial-author">
                    <img src="assets/images/customer-3.jpg" alt="Lisa Chen">
                    <div class="author-info">
                        <h4>Lisa Chen</h4>
                        <span>Verified Customer</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="newsletter-section">
    <div class="container">
        <div class="newsletter-content">
            <h2>Stay Updated</h2>
            <p>Subscribe to our newsletter for exclusive offers and new product announcements</p>
            <form class="newsletter-form" action="newsletter.php" method="POST">
                <input type="email" name="email" placeholder="Enter your email address" required>
                <button type="submit" class="btn btn-primary">Subscribe</button>
            </form>
        </div>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?>

<script>
function launchConfetti() {
    const emojis = ['🌟','🎈','🎉','✨','💃','🥳','🎊','🦄','🌈','🎯'];
    for (let i = 0; i < 60; i++) {
        const span = document.createElement('span');
        span.innerText = emojis[Math.floor(Math.random()*emojis.length)];
        span.className = 'confetti';
        span.style.left = Math.random()*100 + 'vw';
        span.style.animationDelay = Math.random() * 2 + 's';
        document.body.appendChild(span);
        setTimeout(() => span.remove(), 3000);
    }
    
    // Navigate to products page after confetti
    setTimeout(() => {
        window.location.href = 'public/products.php';
    }, 1500);
}
</script>
