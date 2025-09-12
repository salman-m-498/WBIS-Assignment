<?php
session_start();

// DB connection
$pdo = new PDO("mysql:host=localhost;dbname=web_db;charset=utf8mb4", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$featured_products_stmt = $pdo->prepare("SELECT * FROM products WHERE status = 'active' AND featured = 1 ORDER BY RAND() LIMIT 8");
$featured_products_stmt->execute();
$featured_products = $featured_products_stmt->fetchAll(PDO::FETCH_ASSOC);

// New Arrivals (like new_arrivals.php)
$new_arrivals_stmt = $pdo->prepare("SELECT * FROM products WHERE status = 'active' ORDER BY created_at DESC LIMIT 8");
$new_arrivals_stmt->execute();
$new_arrivals = $new_arrivals_stmt->fetchAll(PDO::FETCH_ASSOC);

// Sale Products (like sale.php)
$sale_products_stmt = $pdo->prepare("SELECT * FROM products WHERE status = 'active' AND sale_price IS NOT NULL ORDER BY created_at DESC LIMIT 8");
$sale_products_stmt->execute();
$sale_products = $sale_products_stmt->fetchAll(PDO::FETCH_ASSOC);

$new_arrival_product = !empty($new_arrivals) ? $new_arrivals[array_rand($new_arrivals)] : null;
$sale_product = !empty($sale_products) ? $sale_products[array_rand($sale_products)] : null;

$assets_path = 'assets'; // adjust if needed

$reviews_stmt = $pdo->prepare("
    SELECT r.review_id, r.title, r.comment, r.rating, r.created_at, u.username, u.profile_pic
    FROM reviews r
    JOIN user u ON r.user_id = u.user_id
    WHERE r.status = 'approved'
    ORDER BY r.created_at DESC
    LIMIT 6
");
$reviews_stmt->execute();
$reviews = $reviews_stmt->fetchAll(PDO::FETCH_ASSOC);
$profile_base_path = 'assets/images/profile_pictures/';

// Page variables
$page_title = "Home";
$page_description = "Discover amazing toys and games for all ages at ToyLand Store. Free shipping on orders over $50!";
$show_breadcrumb = false;

// Include header
include 'includes/header.php';
?>

<!-- Flash message container -->
<div id="flash-message" style="display:none; position: fixed; top: 20px; right: 20px; min-width: 200px; padding: 12px 18px; border-radius: 8px; font-size: 14px; z-index: 9999; box-shadow: 0 4px 8px rgba(0,0,0,0.2);"></div>

<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-slider">
        <div class="hero-slide active">
            <div class="container">
                <div class="hero-layout">
                    <div class="hero-content">
                        <h1>Welcome to ToyLand Store</h1>
                        <p>Discover amazing toys and games that spark imagination and create lasting memories</p>
                        <div class="hero-buttons">
                            <a href="public/products.php" class="btn btn-primary">Shop Now</a>
                            <a href="public/sale.php" class="btn btn-secondary">View Sale</a>
                        </div>
                    </div>
                    <div class="hero-image">
                        <div class="hero-toy-collection">
                            <img src="assets/images/hero_bg.png" alt="Amazing Toy Collection" class="floating-toys">
                        </div>
                        <div class="hero-decorations">
                            <div class="decoration-circle circle-1"></div>
                            <div class="decoration-circle circle-2"></div>
                            <div class="decoration-circle circle-3"></div>
                            <div class="decoration-star star-1">⭐</div>
                            <div class="decoration-star star-2">✨</div>
                            <div class="decoration-star star-3">🌟</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section">
    <div class="container">
        <div class="features-grid">
            <div class="feature-item">
                <i class="fas fa-shipping-fast"></i>
                <h3>Free Shipping</h3>
                <p>Free shipping on orders over $50</p>
            </div>
            <div class="feature-item">
                <i class="fas fa-shield-alt"></i>
                <h3>Safe & Secure</h3>
                <p>All toys meet safety standards</p>
            </div>
            <div class="feature-item">
                <i class="fas fa-undo"></i>
                <h3>Easy Returns</h3>
                <p>30-day return policy</p>
            </div>
            <div class="feature-item">
                <i class="fas fa-headset"></i>
                <h3>24/7 Support</h3>
                <p>Customer service always available</p>
            </div>
        </div>
    </div>
</section>

<!-- Featured Banner - LEGO -->
<section class="banner-section">
    <div class="container">
        <div class="banner-container floating">
            <div class="banner-image">
                <img src="assets/images/banners/lego_banner.png" alt="LEGO Collection" loading="lazy">
                <div class="banner-overlay"></div>
            </div>
            <div class="banner-content">
                <h3>Build Your Dreams with LEGO</h3>
                <p>Endless creativity and imagination await with our premium LEGO collection</p>
                <a href="public/products.php" class="banner-btn">Explore LEGO Sets</a>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="categories-section">
    <div class="container">
        <div class="section-header">
            <h2>Shop by Category</h2>
            <p>Find the perfect toy for every age and interest</p>
        </div>
        
        <div class="categories-grid">
            <?php
            // Fetch parent categories from DB
            $stmt = $pdo->query("SELECT * FROM categories WHERE parent_id IS NULL AND status = 'active'");
            $parent_categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($parent_categories as $cat): 
                // Remove "root/" if needed (like you did for products)
                $image_path = str_replace("root/", "", $cat['image']);
            ?>
                <div class="category-card">
                    <div class="category-image">
                        <?php if (!empty($cat['image'])): ?>
                            <img src="<?= htmlspecialchars($image_path) ?>" alt="<?= htmlspecialchars($cat['name']) ?>">
                        <?php endif; ?>
                    </div>
                    <div class="category-content">
                        <h3><?= htmlspecialchars($cat['name']) ?></h3>
                        <p><?= htmlspecialchars($cat['description']) ?></p>
                        <a href="public/subcategories.php?parent_id=<?= urlencode($cat['category_id']) ?>" class="btn btn-outline">Shop Now</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Banners Grid -->
<section class="banner-section">
    <div class="container">
        <div class="featured-banners">
            <!-- Large Barbie Banner -->
            <div class="large-banner">
                <div class="banner-container banner-center">
                    <div class="banner-image">
                        <img src="assets/images/banners/barbie_banner.png" alt="Barbie Collection" loading="lazy">
                        <div class="banner-overlay"></div>
                    </div>
                    <div class="banner-content">
                        <h3>Discover Barbie's Magical World</h3>
                        <p>Fashion, fun, and endless adventures with the world's most beloved doll</p>
                        <a href="public/products.php" class="banner-btn">Shop Barbie Collection</a>
                    </div>
                </div>
            </div>
            
            <!-- Small Hot Wheels Banners -->
            <div class="small-banner">
                <div class="banner-container banner-reverse">
                    <div class="banner-image">
                        <img src="assets/images/banners/hotwheels_banner.png" alt="Hot Wheels Collection" loading="lazy">
                        <div class="banner-overlay"></div>
                    </div>
                    <div class="banner-content">
                        <h3>Hot Wheels Racing</h3>
                        <p>Speed into action with premium die-cast cars</p>
                        <a href="public/products.php" class="banner-btn">Race Now</a>
                    </div>
                </div>
            </div>
            
            <div class="small-banner">
                <div class="banner-container">
                    <div class="banner-image">
                        <img src="assets/images/banners/lego_banner.png" alt="LEGO Collection" loading="lazy">
                        <div class="banner-overlay"></div>
                    </div>
                    <div class="banner-content">
                        <h3>LEGO Adventures</h3>
                        <p>Build, create, and explore infinite possibilities</p>
                        <a href="public/products.php" class="banner-btn">Build Dreams</a>
                    </div>
                </div>
            </div>
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
                            <button class="quick-view" onclick="window.location='<?= $product_url ?>'">Quick View</button>
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

<!-- Hot Wheels Speed Banner -->
<section class="banner-section">
    <div class="container">
        <div class="banner-container banner-reverse floating">
            <div class="banner-image-left">
                <img src="assets/images/banners/hotwheels_banner.png" alt="Hot Wheels High-Speed Collection" loading="lazy">
                <div class="banner-overlay"></div>
            </div>
            <div class="banner-content">
                <h3>Feel the Need for Speed</h3>
                <p>Race into excitement with Hot Wheels' fastest and most thrilling car collection</p>
                <!-- TODO: Change link to redirect to Hot Wheels category when categories are set up -->
                <a href="public/products.php" class="banner-btn">Start Your Engines</a>
            </div>
        </div>
    </div>
</section>

<!-- Promotional Section (New Arrivals & Sale) -->
<section class="promotional-section">
    <div class="container">
        <div class="promo-grid">
            <!-- New Arrivals -->
            <div class="promo-card">
    <div class="promo-content">
        <h2>New Arrivals</h2>
        <p>Check out the latest toys and games that just arrived</p>
        <a href="public/new_arrivals.php" class="btn btn-white">Shop New</a>
    </div>
    <div class="promo-image">
        <?php if ($new_arrival_product): ?>
            <img src="<?= htmlspecialchars(str_replace("root/", "", $new_arrival_product['image'])) ?>" 
                 alt="<?= htmlspecialchars($new_arrival_product['name']) ?>">
        <?php endif; ?>
    </div>
</div>

<!-- Sale Items -->
<div class="promo-card">
    <div class="promo-content">
        <h2>Sale Items</h2>
        <p>Up to 50% off on selected toys and games</p>
        <a href="public/sale.php" class="btn btn-white">Shop Sale</a>
    </div>
    <div class="promo-image">
        <?php if ($sale_product): ?>
            <img src="<?= htmlspecialchars(str_replace("root/", "", $sale_product['image'])) ?>" 
                 alt="<?= htmlspecialchars($sale_product['name']) ?>">
        <?php endif; ?>
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
            <?php foreach ($reviews as $review): ?>
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <?php if ($review['title']): ?>
                            <strong><?= htmlspecialchars($review['title']) ?></strong>
                        <?php endif; ?>
                        <p><?= htmlspecialchars($review['comment']) ?></p>
                    </div>
                    <div class="testimonial-author">
                        <img src="<?= htmlspecialchars(!empty($review['profile_pic']) 
                        ? $review['profile_pic'] 
                        :  '/assets/images/profile_pictures/default_profile_pic.jpg') ?>"
                             alt="<?= htmlspecialchars($review['username']) ?>">
                        <div class="author-info">
                            <h4><?= htmlspecialchars($review['username']) ?></h4>
                            <div class="rating">
                                <?php for ($i=1; $i<=5; $i++): ?>
                                    <i class="fas fa-star <?= $i <= $review['rating'] ? 'filled' : '' ?>"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<script src="/assets/js/cart.js"></script>
<script>
function showFlashMessage(message, type = 'success') {
    const flash = document.getElementById('flash-message');
    flash.textContent = message;
    flash.style.display = 'block';
    flash.style.backgroundColor = (type === 'success') ? '#d4edda' : '#f8d7da';
    flash.style.color = (type === 'success') ? '#155724' : '#721c24';
    flash.style.border = (type === 'success') ? '1px solid #c3e6cb' : '1px solid #f5c6cb';

    // Hide after 3 seconds
    setTimeout(() => {
        flash.style.display = 'none';
    }, 3000);
}

document.addEventListener('DOMContentLoaded', () => {
    const wishlistButtons = document.querySelectorAll('.add-to-wishlist');
    
    wishlistButtons.forEach(button => {
        button.addEventListener('click', () => {
            const productId = button.dataset.productId;

            fetch('member/toggle-wishlist.php', {
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
});
</script>


<?php
// Include footer
include 'includes/footer.php';
?>
