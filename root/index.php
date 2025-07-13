<?php
session_start();

// Page variables
$page_title = "Home";
$page_description = "Discover amazing toys and games for all ages at ToyLand Store. Free shipping on orders over $50!";
$show_breadcrumb = false;

// Include header
include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-slider">
        <div class="hero-slide active">
            <div class="container">
                <div class="hero-content">
                    <h1>Welcome to ToyLand Store</h1>
                    <p>Discover amazing toys and games that spark imagination and create lasting memories</p>
                    <div class="hero-buttons">
                        <a href="products.php" class="btn btn-primary">Shop Now</a>
                        <a href="sale.php" class="btn btn-secondary">View Sale</a>
                    </div>
                </div>
                <div class="hero-image">
                    <img src="assets/images/hero-toy-collection.jpg" alt="Amazing Toy Collection">
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

<!-- Categories Section -->
<section class="categories-section">
    <div class="container">
        <div class="section-header">
            <h2>Shop by Category</h2>
            <p>Find the perfect toy for every age and interest</p>
        </div>
        
        <div class="categories-grid">
            <div class="category-card">
                <div class="category-image">
                    <img src="assets/images/category-action-figures.jpg" alt="Action Figures">
                </div>
                <div class="category-content">
                    <h3>Action Figures</h3>
                    <p>Superheroes, characters, and collectibles</p>
                    <a href="products.php?category=action-figures" class="btn btn-outline">Shop Now</a>
                </div>
            </div>
            
            <div class="category-card">
                <div class="category-image">
                    <img src="assets/images/category-board-games.jpg" alt="Board Games">
                </div>
                <div class="category-content">
                    <h3>Board Games</h3>
                    <p>Family fun and strategy games</p>
                    <a href="products.php?category=board-games" class="btn btn-outline">Shop Now</a>
                </div>
            </div>
            
            <div class="category-card">
                <div class="category-image">
                    <img src="assets/images/category-educational.jpg" alt="Educational Toys">
                </div>
                <div class="category-content">
                    <h3>Educational Toys</h3>
                    <p>Learning through play</p>
                    <a href="products.php?category=educational" class="btn btn-outline">Shop Now</a>
                </div>
            </div>
            
            <div class="category-card">
                <div class="category-image">
                    <img src="assets/images/category-outdoor.jpg" alt="Outdoor Toys">
                </div>
                <div class="category-content">
                    <h3>Outdoor Toys</h3>
                    <p>Active play and adventure</p>
                    <a href="products.php?category=outdoor" class="btn btn-outline">Shop Now</a>
                </div>
            </div>
            
            <div class="category-card">
                <div class="category-image">
                    <img src="assets/images/category-arts-crafts.jpg" alt="Arts & Crafts">
                </div>
                <div class="category-content">
                    <h3>Arts & Crafts</h3>
                    <p>Creative expression and DIY fun</p>
                    <a href="products.php?category=arts-crafts" class="btn btn-outline">Shop Now</a>
                </div>
            </div>
            
            <div class="category-card">
                <div class="category-image">
                    <img src="assets/images/category-babies-toddlers.jpg" alt="Babies & Toddlers">
                </div>
                <div class="category-content">
                    <h3>Babies & Toddlers</h3>
                    <p>Safe and engaging early development</p>
                    <a href="products.php?category=babies-toddlers" class="btn btn-outline">Shop Now</a>
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
            <!-- Featured Product 1 -->
            <div class="product-card">
                <div class="product-image">
                    <img src="assets/images/product-1.jpg" alt="Super Robot Action Figure">
                    <div class="product-overlay">
                        <button class="quick-view" data-product-id="1">Quick View</button>
                        <button class="add-to-wishlist" data-product-id="1"><i class="far fa-heart"></i></button>
                    </div>
                    <div class="product-badge sale">Sale</div>
                </div>
                <div class="product-content">
                    <h3><a href="product.php?id=1">Super Robot Action Figure</a></h3>
                    <div class="product-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <span>(24 reviews)</span>
                    </div>
                    <div class="product-price">
                        <span class="current-price">$24.99</span>
                        <span class="original-price">$34.99</span>
                    </div>
                    <button class="add-to-cart" data-product-id="1">Add to Cart</button>
                </div>
            </div>
            
            <!-- Featured Product 2 -->
            <div class="product-card">
                <div class="product-image">
                    <img src="assets/images/product-2.jpg" alt="Educational Building Blocks">
                    <div class="product-overlay">
                        <button class="quick-view" data-product-id="2">Quick View</button>
                        <button class="add-to-wishlist" data-product-id="2"><i class="far fa-heart"></i></button>
                    </div>
                    <div class="product-badge new">New</div>
                </div>
                <div class="product-content">
                    <h3><a href="product.php?id=2">Educational Building Blocks</a></h3>
                    <div class="product-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="far fa-star"></i>
                        <span>(18 reviews)</span>
                    </div>
                    <div class="product-price">
                        <span class="current-price">$39.99</span>
                    </div>
                    <button class="add-to-cart" data-product-id="2">Add to Cart</button>
                </div>
            </div>
            
            <!-- Featured Product 3 -->
            <div class="product-card">
                <div class="product-image">
                    <img src="assets/images/product-3.jpg" alt="Family Board Game">
                    <div class="product-overlay">
                        <button class="quick-view" data-product-id="3">Quick View</button>
                        <button class="add-to-wishlist" data-product-id="3"><i class="far fa-heart"></i></button>
                    </div>
                </div>
                <div class="product-content">
                    <h3><a href="product.php?id=3">Family Board Game</a></h3>
                    <div class="product-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <span>(31 reviews)</span>
                    </div>
                    <div class="product-price">
                        <span class="current-price">$29.99</span>
                    </div>
                    <button class="add-to-cart" data-product-id="3">Add to Cart</button>
                </div>
            </div>
            
            <!-- Featured Product 4 -->
            <div class="product-card">
                <div class="product-image">
                    <img src="assets/images/product-4.jpg" alt="Art & Craft Kit">
                    <div class="product-overlay">
                        <button class="quick-view" data-product-id="4">Quick View</button>
                        <button class="add-to-wishlist" data-product-id="4"><i class="far fa-heart"></i></button>
                    </div>
                </div>
                <div class="product-content">
                    <h3><a href="product.php?id=4">Art & Craft Kit</a></h3>
                    <div class="product-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="far fa-star"></i>
                        <span>(15 reviews)</span>
                    </div>
                    <div class="product-price">
                        <span class="current-price">$19.99</span>
                    </div>
                    <button class="add-to-cart" data-product-id="4">Add to Cart</button>
                </div>
            </div>
        </div>
        
        <div class="view-all-products">
            <a href="products.php" class="btn btn-primary">View All Products</a>
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
                    <a href="new-arrivals.php" class="btn btn-white">Shop New</a>
                </div>
                <div class="promo-image">
                    <img src="assets/images/promo-new-arrivals.jpg" alt="New Arrivals">
                </div>
            </div>
            
            <div class="promo-card">
                <div class="promo-content">
                    <h2>Sale Items</h2>
                    <p>Up to 50% off on selected toys and games</p>
                    <a href="sale.php" class="btn btn-white">Shop Sale</a>
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
