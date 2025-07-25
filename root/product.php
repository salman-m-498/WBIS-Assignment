<?php
session_start();

// Get product ID
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

// Page variables
$page_title = "Super Robot Action Figure";
$page_description = "High-quality action figure with multiple articulation points and accessories";
$show_breadcrumb = true;
$breadcrumb_items = [
    ['url' => 'products.php', 'title' => 'Products'],
    ['url' => 'products.php?category=action-figures', 'title' => 'Action Figures']
];

// Include header
include 'includes/header.php';
?>

<!-- Product Details Section -->
<section class="product-details-section">
    <div class="container">
        <div class="product-details-layout">
            <!-- Product Images -->
            <div class="product-images">
                <div class="main-image">
                    <img src="assets/images/product-1-large.jpg" alt="Super Robot Action Figure" id="main-product-image">
                </div>
                <div class="thumbnail-images">
                    <div class="thumbnail active" data-image="assets/images/product-1-large.jpg">
                        <img src="assets/images/product-1-thumb.jpg" alt="Super Robot Action Figure">
                    </div>
                    <div class="thumbnail" data-image="assets/images/product-1-2.jpg">
                        <img src="assets/images/product-1-2-thumb.jpg" alt="Super Robot Action Figure - Side View">
                    </div>
                    <div class="thumbnail" data-image="assets/images/product-1-3.jpg">
                        <img src="assets/images/product-1-3-thumb.jpg" alt="Super Robot Action Figure - Back View">
                    </div>
                    <div class="thumbnail" data-image="assets/images/product-1-4.jpg">
                        <img src="assets/images/product-1-4-thumb.jpg" alt="Super Robot Action Figure - Accessories">
                    </div>
                </div>
            </div>
            
            <!-- Product Info -->
            <div class="product-info">
                <div class="product-header">
                    <h1>Super Robot Action Figure</h1>
                    <div class="product-rating">
                        <div class="stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <span class="rating-text">5.0 out of 5</span>
                        <span class="review-count">(24 reviews)</span>
                        <a href="#reviews" class="write-review">Write a Review</a>
                    </div>
                    <div class="product-badges">
                        <span class="badge sale">Sale</span>
                        <span class="badge new">New</span>
                    </div>
                </div>
                
                <div class="product-price">
                    <span class="current-price">$24.99</span>
                    <span class="original-price">$34.99</span>
                    <span class="discount">Save $10.00 (29% off)</span>
                </div>
                
                <div class="product-description">
                    <p>This high-quality Super Robot Action Figure features multiple articulation points, allowing for dynamic poses and endless play possibilities. Perfect for collectors and kids alike!</p>
                    
                    <ul class="product-features">
                        <li>Highly articulated with 20+ points of movement</li>
                        <li>Includes 3 interchangeable accessories</li>
                        <li>Made from durable, child-safe materials</li>
                        <li>Recommended for ages 6+</li>
                        <li>Dimensions: 8" tall</li>
                    </ul>
                </div>
                
                <div class="product-options">
                    <div class="option-group">
                        <label>Color:</label>
                        <div class="color-options">
                            <button class="color-option active" data-color="blue" style="background-color: #0066cc;"></button>
                            <button class="color-option" data-color="red" style="background-color: #cc0000;"></button>
                            <button class="color-option" data-color="green" style="background-color: #00cc00;"></button>
                        </div>
                    </div>
                    
                    <div class="option-group">
                        <label for="quantity">Quantity:</label>
                        <div class="quantity-selector">
                            <button class="quantity-btn minus">-</button>
                            <input type="number" id="quantity" name="quantity" value="1" min="1" max="10">
                            <button class="quantity-btn plus">+</button>
                        </div>
                    </div>
                </div>
                
                <div class="product-actions">
                    <button class="btn btn-primary add-to-cart-btn" data-product-id="<?php echo $product_id; ?>">
                        <i class="fas fa-shopping-cart"></i> Add to Cart
                    </button>
                    <button class="btn btn-outline add-to-wishlist-btn" data-product-id="<?php echo $product_id; ?>">
                        <i class="far fa-heart"></i> Add to Wishlist
                    </button>
                    <button class="btn btn-outline quick-buy-btn">
                        <i class="fas fa-bolt"></i> Buy Now
                    </button>
                </div>
                
                <div class="product-meta">
                    <div class="meta-item">
                        <span class="meta-label">SKU:</span>
                        <span class="meta-value">ROB-001</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Category:</span>
                        <span class="meta-value"><a href="products.php?category=action-figures">Action Figures</a></span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Brand:</span>
                        <span class="meta-value"><a href="brands.php?brand=super-toys">Super Toys</a></span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Availability:</span>
                        <span class="meta-value in-stock">In Stock</span>
                    </div>
                </div>
                
                <div class="product-shipping">
                    <div class="shipping-info">
                        <i class="fas fa-shipping-fast"></i>
                        <div>
                            <strong>Free Shipping</strong> on orders over $50
                        </div>
                    </div>
                    <div class="shipping-info">
                        <i class="fas fa-undo"></i>
                        <div>
                            <strong>30-Day Returns</strong> - Easy returns and exchanges
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Product Tabs Section -->
<section class="product-tabs-section">
    <div class="container">
        <div class="product-tabs">
            <div class="tab-nav">
                <button class="tab-btn active" data-tab="description">Description</button>
                <button class="tab-btn" data-tab="specifications">Specifications</button>
                <button class="tab-btn" data-tab="reviews">Reviews (24)</button>
                <button class="tab-btn" data-tab="shipping">Shipping & Returns</button>
            </div>
            
            <div class="tab-content">
                <!-- Description Tab -->
                <div class="tab-pane active" id="description">
                    <div class="product-description-content">
                        <h3>Product Description</h3>
                        <p>The Super Robot Action Figure is the ultimate collectible for robot enthusiasts and action figure collectors. This meticulously crafted figure features exceptional detail and articulation that brings your robot fantasies to life.</p>
                        
                        <h4>Key Features:</h4>
                        <ul>
                            <li><strong>Premium Articulation:</strong> 20+ points of articulation for dynamic posing</li>
                            <li><strong>Interchangeable Accessories:</strong> Includes 3 different weapon attachments</li>
                            <li><strong>Durable Construction:</strong> Made from high-quality, child-safe materials</li>
                            <li><strong>Detailed Design:</strong> Intricate sculpting and paint applications</li>
                            <li><strong>Display Stand:</strong> Included for perfect display positioning</li>
                        </ul>
                        
                        <h4>Perfect For:</h4>
                        <ul>
                            <li>Action figure collectors</li>
                            <li>Robot and sci-fi enthusiasts</li>
                            <li>Creative play and storytelling</li>
                            <li>Display and photography</li>
                        </ul>
                    </div>
                </div>
                
                <!-- Specifications Tab -->
                <div class="tab-pane" id="specifications">
                    <div class="product-specifications">
                        <h3>Product Specifications</h3>
                        <table class="specs-table">
                            <tr>
                                <td><strong>Dimensions:</strong></td>
                                <td>8" tall x 3" wide x 2" deep</td>
                            </tr>
                            <tr>
                                <td><strong>Weight:</strong></td>
                                <td>0.5 lbs</td>
                            </tr>
                            <tr>
                                <td><strong>Material:</strong></td>
                                <td>ABS Plastic, PVC</td>
                            </tr>
                            <tr>
                                <td><strong>Articulation Points:</strong></td>
                                <td>20+ points</td>
                            </tr>
                            <tr>
                                <td><strong>Age Range:</strong></td>
                                <td>6+ years</td>
                            </tr>
                            <tr>
                                <td><strong>Includes:</strong></td>
                                <td>Action figure, 3 accessories, display stand</td>
                            </tr>
                            <tr>
                                <td><strong>Battery Required:</strong></td>
                                <td>No</td>
                            </tr>
                            <tr>
                                <td><strong>Safety Certifications:</strong></td>
                                <td>ASTM F963, EN71, CPSIA</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <!-- Reviews Tab -->
                <div class="tab-pane" id="reviews">
                    <div class="product-reviews">
                        <div class="reviews-header">
                            <h3>Customer Reviews</h3>
                            <div class="reviews-summary">
                                <div class="average-rating">
                                    <span class="rating-number">5.0</span>
                                    <div class="stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <span>out of 5</span>
                                </div>
                                <div class="rating-breakdown">
                                    <div class="rating-bar">
                                        <span>5 stars</span>
                                        <div class="bar"><div class="fill" style="width: 85%;"></div></div>
                                        <span>20</span>
                                    </div>
                                    <div class="rating-bar">
                                        <span>4 stars</span>
                                        <div class="bar"><div class="fill" style="width: 10%;"></div></div>
                                        <span>2</span>
                                    </div>
                                    <div class="rating-bar">
                                        <span>3 stars</span>
                                        <div class="bar"><div class="fill" style="width: 5%;"></div></div>
                                        <span>1</span>
                                    </div>
                                    <div class="rating-bar">
                                        <span>2 stars</span>
                                        <div class="bar"><div class="fill" style="width: 0%;"></div></div>
                                        <span>0</span>
                                    </div>
                                    <div class="rating-bar">
                                        <span>1 star</span>
                                        <div class="bar"><div class="fill" style="width: 0%;"></div></div>
                                        <span>0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="reviews-list">
                            <!-- Review 1 -->
                            <div class="review-item">
                                <div class="review-header">
                                    <div class="reviewer-info">
                                        <img src="assets/images/reviewer-1.jpg" alt="John D.">
                                        <div>
                                            <h4>John D.</h4>
                                            <div class="stars">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="review-date">March 15, 2024</span>
                                </div>
                                <div class="review-content">
                                    <h5>Excellent Quality!</h5>
                                    <p>This action figure exceeded my expectations. The articulation is smooth, the paint job is clean, and the accessories are well-made. My son loves playing with it and I enjoy displaying it on my shelf.</p>
                                </div>
                            </div>
                            
                            <!-- Review 2 -->
                            <div class="review-item">
                                <div class="review-header">
                                    <div class="reviewer-info">
                                        <img src="assets/images/reviewer-2.jpg" alt="Sarah M.">
                                        <div>
                                            <h4>Sarah M.</h4>
                                            <div class="stars">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="review-date">March 10, 2024</span>
                                </div>
                                <div class="review-content">
                                    <h5>Perfect Gift</h5>
                                    <p>Bought this for my nephew's birthday and he absolutely loves it! The figure is sturdy and the joints hold poses well. Great value for the price.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="write-review-section">
                            <h4>Write a Review</h4>
                            <form class="review-form">
                                <div class="rating-input">
                                    <label>Your Rating:</label>
                                    <div class="star-rating">
                                        <input type="radio" name="rating" value="5" id="star5">
                                        <label for="star5"><i class="far fa-star"></i></label>
                                        <input type="radio" name="rating" value="4" id="star4">
                                        <label for="star4"><i class="far fa-star"></i></label>
                                        <input type="radio" name="rating" value="3" id="star3">
                                        <label for="star3"><i class="far fa-star"></i></label>
                                        <input type="radio" name="rating" value="2" id="star2">
                                        <label for="star2"><i class="far fa-star"></i></label>
                                        <input type="radio" name="rating" value="1" id="star1">
                                        <label for="star1"><i class="far fa-star"></i></label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="review-title">Review Title:</label>
                                    <input type="text" id="review-title" name="title" required>
                                </div>
                                <div class="form-group">
                                    <label for="review-content">Your Review:</label>
                                    <textarea id="review-content" name="content" rows="5" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit Review</button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Shipping Tab -->
                <div class="tab-pane" id="shipping">
                    <div class="shipping-returns">
                        <h3>Shipping & Returns</h3>
                        
                        <div class="shipping-info">
                            <h4>Shipping Information</h4>
                            <ul>
                                <li><strong>Free Shipping:</strong> On orders over $50</li>
                                <li><strong>Standard Shipping:</strong> 3-5 business days ($5.99)</li>
                                <li><strong>Express Shipping:</strong> 1-2 business days ($12.99)</li>
                                <li><strong>Overnight Shipping:</strong> Next business day ($19.99)</li>
                            </ul>
                        </div>
                        
                        <div class="returns-info">
                            <h4>Returns & Exchanges</h4>
                            <ul>
                                <li><strong>Return Period:</strong> 30 days from delivery</li>
                                <li><strong>Return Shipping:</strong> Free for defective items</li>
                                <li><strong>Refund Method:</strong> Original payment method</li>
                                <li><strong>Condition:</strong> Must be unused and in original packaging</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Products Section -->
<section class="related-products-section">
    <div class="container">
        <div class="section-header">
            <h2>Related Products</h2>
            <p>You might also like these products</p>
        </div>
        
        <div class="products-grid">
            <!-- Related Product 1 -->
            <div class="product-card">
                <div class="product-image">
                    <img src="assets/images/product-9.jpg" alt="Robot Companion Figure">
                    <div class="product-overlay">
                        <button class="quick-view" data-product-id="9">Quick View</button>
                        <button class="add-to-wishlist" data-product-id="9"><i class="far fa-heart"></i></button>
                    </div>
                </div>
                <div class="product-content">
                    <h3><a href="product.php?id=9">Robot Companion Figure</a></h3>
                    <div class="product-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="far fa-star"></i>
                        <span>(16 reviews)</span>
                    </div>
                    <div class="product-price">
                        <span class="current-price">$19.99</span>
                    </div>
                    <button class="add-to-cart" data-product-id="9">Add to Cart</button>
                </div>
            </div>
            
            <!-- Related Product 2 -->
            <div class="product-card">
                <div class="product-image">
                    <img src="assets/images/product-10.jpg" alt="Space Warrior Figure">
                    <div class="product-overlay">
                        <button class="quick-view" data-product-id="10">Quick View</button>
                        <button class="add-to-wishlist" data-product-id="10"><i class="far fa-heart"></i></button>
                    </div>
                    <div class="product-badge sale">Sale</div>
                </div>
                <div class="product-content">
                    <h3><a href="product.php?id=10">Space Warrior Figure</a></h3>
                    <div class="product-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <span>(21 reviews)</span>
                    </div>
                    <div class="product-price">
                        <span class="current-price">$22.99</span>
                        <span class="original-price">$29.99</span>
                    </div>
                    <button class="add-to-cart" data-product-id="10">Add to Cart</button>
                </div>
            </div>
            
            <!-- Related Product 3 -->
            <div class="product-card">
                <div class="product-image">
                    <img src="assets/images/product-11.jpg" alt="Action Figure Display Case">
                    <div class="product-overlay">
                        <button class="quick-view" data-product-id="11">Quick View</button>
                        <button class="add-to-wishlist" data-product-id="11"><i class="far fa-heart"></i></button>
                    </div>
                </div>
                <div class="product-content">
                    <h3><a href="product.php?id=11">Action Figure Display Case</a></h3>
                    <div class="product-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="far fa-star"></i>
                        <span>(8 reviews)</span>
                    </div>
                    <div class="product-price">
                        <span class="current-price">$34.99</span>
                    </div>
                    <button class="add-to-cart" data-product-id="11">Add to Cart</button>
                </div>
            </div>
            
            <!-- Related Product 4 -->
            <div class="product-card">
                <div class="product-image">
                    <img src="assets/images/product-12.jpg" alt="Robot Accessory Pack">
                    <div class="product-overlay">
                        <button class="quick-view" data-product-id="12">Quick View</button>
                        <button class="add-to-wishlist" data-product-id="12"><i class="far fa-heart"></i></button>
                    </div>
                </div>
                <div class="product-content">
                    <h3><a href="product.php?id=12">Robot Accessory Pack</a></h3>
                    <div class="product-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="far fa-star"></i>
                        <span>(12 reviews)</span>
                    </div>
                    <div class="product-price">
                        <span class="current-price">$14.99</span>
                    </div>
                    <button class="add-to-cart" data-product-id="12">Add to Cart</button>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?> 