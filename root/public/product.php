<?php
session_start();

require_once '../includes/db.php';

// Get product ID
$product_id = isset($_GET['id']) ? $_GET['id'] : '';

// Get product
$stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    echo "<h2>Product not found</h2>";
    exit;
}

// Main image comes from products table
$main_image = $product['image'];

// Gallery images come from product_images table
$image_stmt = $pdo->prepare("SELECT image_path FROM product_images WHERE product_id = ?");
$image_stmt->execute([$product_id]);
$thumbnail_images = $image_stmt->fetchAll(PDO::FETCH_COLUMN);

// Page variables
$page_title = $product['name'];
$page_description = $product['description'];
$show_breadcrumb = true;
$breadcrumb_items = [['url' => 'products.php', 'title' => 'Products']];

// Fetch related products (same category, exclude current product)
$related_stmt = $pdo->prepare("
    SELECT * 
    FROM products 
    WHERE category_id = ? 
      AND product_id != ? 
      AND status = 'active'
    LIMIT 4
");
$related_stmt->execute([$product['category_id'], $product_id]);
$related_products = $related_stmt->fetchAll(PDO::FETCH_ASSOC);


// REVIEWS SECTION
// Review list pagination
$GLOB_status = 'pending';
$limit = 5; 
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Count total reviews (once only)
$count_stmt = $pdo->prepare("
    SELECT COUNT(*) AS total_reviews
    FROM reviews
    WHERE product_id = ? AND status = ?
");
$count_stmt->execute([$product_id, $GLOB_status]);
$count_result = $count_stmt->fetch(PDO::FETCH_ASSOC);

$total_reviews = $count_result['total_reviews'] ?? 0;
$total_reviews_pages = ceil($total_reviews / $limit);

// Review sorting
$sort = $_GET['sort'] ?? 'newest';
switch ($sort) {
    case 'oldest':
        $orderBy = "r.created_at ASC";
        break;
    case 'high':
        $orderBy = "r.rating DESC";
        break;
    case 'low':
        $orderBy = "r.rating ASC";
        break;
    case 'newest':
    default:
        $orderBy = "r.created_at DESC";
        break;
}

// Fetch review info
$stmt = $pdo->prepare("
    SELECT r.*, u.username, u.profile_pic
    FROM reviews r
    JOIN user u ON r.user_id = u.user_id
    WHERE r.product_id = :product_id
      AND r.status = :status
    ORDER BY $orderBy
    LIMIT :limit OFFSET :offset
");

$stmt->bindValue(':product_id', $product_id, PDO::PARAM_STR);
$stmt->bindValue(':status', $GLOB_status, PDO::PARAM_STR);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Writing review section 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $rating  = $_POST['rating'] ?? null;
    $title   = $_POST['title'] ?? null;
    $comment = $_POST['content'] ?? null;

    if ($rating && $title && $comment) {
        // Get last review_id
        $sql = "SELECT review_id FROM reviews ORDER BY review_id DESC LIMIT 1";
        $stmt = $pdo->query($sql);
        $lastId = $stmt->fetchColumn();

        if ($lastId) {
            // Extract the numeric part and increment
            $num = (int)substr($lastId, 2);
            $num++;
        } else {
            $num = 1;
        }

        $review_id = 'UR' . str_pad($num, 9, '0', STR_PAD_LEFT);
        $sql = "INSERT INTO reviews (review_id, user_id, product_id, rating, title, comment, status)
                VALUES (:review_id, :user_id, :product_id, :rating, :title, :comment, 'pending')";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':review_id'  => $review_id,
            ':user_id'    => $user_id,
            ':product_id' => $product_id,
            ':rating'     => $rating,
            ':title'      => $title,
            ':comment'    => $comment
        ]);
        
        $_SESSION['review_success'] = "✅ Your review has been submitted successfully!";
    }

    header("Location: product.php?id=$product_id");
    exit;
}



// Include header
include '../includes/header.php';
?>

<!-- Product Details Section -->
<section class="product-details-section">
    <div class="container">
        <div class="product-details-layout">
            <!-- Product Images -->
            <div class="product-images">
                <div class="main-image">
                    <?php if (!empty($main_image)): ?>
                        <img src="/<?= htmlspecialchars($main_image) ?>" alt="<?= htmlspecialchars($product['name']) ?>" id="main-product-image" onerror="this.src='https://via.placeholder.com/500x400/f93c64/ffffff?text=<?= urlencode($product['name']) ?>'">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/500x400/f93c64/ffffff?text=<?= urlencode($product['name']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" id="main-product-image">
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($thumbnail_images)): ?>
                <div class="thumbnail-images">
                    <div class="thumbnail active" data-image="/<?= htmlspecialchars($main_image) ?>">
                        <img src="/<?= htmlspecialchars($main_image) ?>" alt="Main view" onerror="this.src='https://via.placeholder.com/100x80/f93c64/ffffff?text=Main'">
                    </div>
                    <?php foreach ($thumbnail_images as $img): ?>
                        <div class="thumbnail" data-image="/<?= htmlspecialchars($img) ?>">
                           <img src="/<?= htmlspecialchars($img) ?>" alt="Alternative view" onerror="this.src='https://via.placeholder.com/100x80/f93c64/ffffff?text=Alt'">
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Product Info -->
            <div class="product-info">
                <div class="product-header">
                    <h1><?= htmlspecialchars($product['name']) ?></h1>
                    
                    <div class="product-rating">
                        <div class="stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <span>5.0 out of 5</span>
                        <span>(24 reviews)</span>
                    </div>
                    
                    <div class="product-badges">
                        <?php if ($product['price'] > $product['sale_price']): ?>
                            <span class="badge sale">Sale</span>
                        <?php endif; ?>
                        <span class="badge new">New</span>
                    </div>
                </div>
                
                <div class="product-price">
                    <span class="current-price">RM <?= number_format($product['sale_price'], 2) ?></span>
                    <?php if ($product['price'] > $product['sale_price']): ?>
                        <span class="original-price">RM <?= number_format($product['price'], 2) ?></span>
                        <span class="discount">Save RM <?= number_format($product['price'] - $product['sale_price'], 2) ?> (<?= round((($product['price'] - $product['sale_price']) / $product['price']) * 100) ?>% off)</span>
                    <?php endif; ?>
                </div>
                
                <div class="product-description">
                    <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                </div>
                
                <div class="product-options">
                    <div class="option-group">
                        <label>Available Colors:</label>
                        <div class="color-options">
                            <button class="color-option active" data-color="blue" style="background-color: #0066cc;" title="Blue"></button>
                            <button class="color-option" data-color="red" style="background-color: #cc0000;" title="Red"></button>
                            <button class="color-option" data-color="green" style="background-color: #00cc00;" title="Green"></button>
                        </div>
                    </div>
                </div>
                
                <div class="product-actions">
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity-<?= $product_id ?>" value="1" min="1" max="<?= $product['stock_quantity'] ?>">
                <button 
                    class="add-to-cart btn btn-primary" 
                    data-product-id="<?= $product_id ?>"
                    onclick="this.setAttribute('data-quantity', document.getElementById('quantity-<?= $product_id ?>').value)">
                    <i class="fas fa-shopping-cart"></i> Add to Cart
                </button>
            </div>
                
                <div class="product-meta">
                    <div><strong>SKU:</strong> <?= htmlspecialchars($product['sku']) ?></div>
                    <div><strong>Stock:</strong> <?= htmlspecialchars($product['stock_quantity']) ?> items available</div>
                    <div><strong>Category:</strong> <?= htmlspecialchars($product['category_id']) ?></div>
                    <div><strong>Brand:</strong> Toy Land</div>
                </div>
                
                <div class="product-shipping">
                    <div class="shipping-info">
                        <i class="fas fa-shipping-fast"></i>
                        <div>
                            <strong>Free Shipping</strong>
                            <span>On orders over RM 150</span>
                        </div>
                    </div>
                    <div class="shipping-info">
                        <i class="fas fa-undo"></i>
                        <div>
                            <strong>30-Day Returns</strong>
                            <span>Easy returns & exchanges</span>
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
                <button class="tab-btn" data-tab="reviews">Reviews (<?php echo $total_reviews; ?>)</button>
                <button class="tab-btn" data-tab="shipping">Shipping & Returns</button>
            </div>
            
            <div class="tab-content">
                <!-- Description Tab -->
                <div class="tab-pane active" id="description">
                    <div class="product-description-content">
                        <h3>Product Description</h3>
                        <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                        
                        <h4>Key Features:</h4>
                        <ul>
                          <?php 
                          $features = explode("\n", $product['product_features']);
                          foreach ($features as $feature): 
                             $clean = trim($feature, "-• \t\r\n");
                             if ($clean): ?>
                             <li><?= htmlspecialchars($clean) ?></li>
                          <?php endif; endforeach; ?>
                        </ul>
                    </div>
                </div>
                
                <!-- Specifications Tab -->
                <div class="tab-pane" id="specifications">
                    <div class="product-specifications">
                        <br>
                         <table class="specs-table">
                         <?php
                         $lines = explode("\n", $product['product_specifications']);
                         foreach ($lines as $line) {
                              if (strpos($line, ':') !== false) {
                                 list($label, $value) = explode(':', $line, 2);
                                 echo '<tr>';
                                 echo '<td><strong>' . htmlspecialchars(trim($label)) . ':</strong></td>';
                                 echo '<td>' . htmlspecialchars(trim($value)) . '</td>';
                                 echo '</tr>';}       
                                }
                        ?>
                        </table>
                    </div>
                </div>
                
                <!-- Reviews Tab -->
                <div class="tab-pane" id="reviews">
                    <?php
                    // Step 1: Check if user has purchased this product
                    $canReview = false;

                    if (isset($_SESSION['user_id'])) {
                    $user_id = $_SESSION['user_id'];

                    $check_stmt = $pdo->prepare("
                        SELECT COUNT(*) 
                        FROM order_items oi
                        JOIN orders o ON oi.order_id = o.order_id
                        WHERE o.user_id = :user_id
                        AND oi.product_id = :product_id
                        AND o.order_status = 'delivered'
                    ");
                    $check_stmt->execute([
                        ':user_id' => $user_id,
                        ':product_id' => $product_id
                    ]);
                    $hasPurchased = $check_stmt->fetchColumn();

                    if ($hasPurchased > 0) {
                        $canReview = true;
                    }
                }
                ?>
                    <div class="product-reviews">
        
                    <?php if (isset($_SESSION['review_success'])): ?>
                        <div class="alert-success" style="padding:12px; margin:15px 0; border:1px solid #28a745; background:#eaf9f0; color:#155724; border-radius:6px;">
                            <?= $_SESSION['review_success']; ?>
                        </div>
                        <?php unset($_SESSION['review_success']); ?>
                    <?php endif; ?>
                        <div class="reviews-header">
                            <h3>Customer Reviews</h3>
                            <div class="reviews-summary">
                                <?php
                                try {
                                    $avg_stmt = $pdo->prepare("
                                        SELECT ROUND(AVG(rating),1) AS avg_rating, COUNT(*) AS total_reviews
                                        FROM reviews
                                        WHERE product_id = ? AND status = 'pending'
                                    ");
                                    $avg_stmt->execute([$product_id]);
                                    $avg_result = $avg_stmt->fetch(PDO::FETCH_ASSOC);

                                    $average = $avg_result['avg_rating'] ?? 0;
                                    $total   = $avg_result['total_reviews'] ?? 0;

                                    if ($total > 0) {
                                        echo '<div class="average-rating">';
                                        $filled = floor($average);
                                        for ($i = 1; $i <= 5; $i++) {
                                            echo $i <= $filled ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
                                        }
                                        echo " <strong>{$average}</strong> out of 5 ({$total} reviews)";
                                        echo '</div>';
                                    } else {
                                        echo "<p>No reviews yet. Be the first to write one!</p>";
                                    }
                                } catch (Throwable $e) {
                                    echo "<p style='color:#c00;'>Error loading summary.</p>";
                                }
                                ?>
                        </div>
                        
                            <!-- Review sorting -->
                            <form method="get" action="#reviews" style="margin-bottom:10px; float:right;">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($product_id); ?>">
                                <select name="sort" onchange="this.form.submit()" style="padding:5px; margin-right:5px;">
                                    <option value="newest" <?php if($sort=='newest') echo 'selected'; ?>>Newest</option>
                                    <option value="oldest" <?php if($sort=='oldest') echo 'selected'; ?>>Oldest</option>
                                    <option value="rating_high" <?php if($sort=='rating_high') echo 'selected'; ?>>Rating: High → Low</option>
                                    <option value="rating_low" <?php if($sort=='rating_low') echo 'selected'; ?>>Rating: Low → High</option>
                                </select>
                            </form>

                            <div style="height:30px;"></div>

                            <div class="reviews-list">
                                <?php if ($reviews): ?>
                                    <?php foreach ($reviews as $review): ?>
                                        <div class="review-item">
                                            <!-- reviewer info -->
                                            <div class="review-header">
                                                <?php 
                                                    $profilePic = !empty($review['profile_pic']) 
                                                        ? $review['profile_pic'] 
                                                        : 'default_profile_pic.jpg';
                                                ?>
                                                <img src="../assets/images/profile_pictures/<?= htmlspecialchars($profilePic) ?>" 
                                                    alt="Profile Picture" 
                                                    style="width:75px; height:75px; object-fit:cover; border-radius:50%; margin-right:8px; vertical-align:middle;">

                                                <strong><?= htmlspecialchars($review['username']); ?></strong>
                                                <span class="review-date">
                                                    <?= date("F j, Y", strtotime($review['created_at'])); ?>
                                                </span>
                                            </div>

                                            <!-- rating stars -->
                                            <div class="review-rating">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <?= $i <= $review['rating'] ? '★' : '☆'; ?>
                                                <?php endfor; ?>
                                            </div>

                                            <!-- review content -->
                                            <h5><?= htmlspecialchars($review['title']); ?></h5>
                                            <p><?= nl2br(htmlspecialchars($review['comment'])); ?></p>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Review pagination -->
                            <?php if ($total_reviews_pages > 1): ?>
                                <div class="pagination" style="margin-top:20px; text-align:center;">
                                    <?php if ($page > 1): ?>
                                        <a href="?id=<?php echo urlencode($product_id); ?>&sort=<?php echo urlencode($sort); ?>&page=<?php echo $page-1; ?>#reviews" 
                                        class="page-btn" style="margin:0 5px;">Prev</a>
                                    <?php endif; ?>

                                    <?php for ($i = 1; $i <= $total_reviews_pages; $i++): ?>
                                        <a href="?id=<?php echo urlencode($product_id); ?>&sort=<?php echo urlencode($sort); ?>&page=<?php echo $i; ?>#reviews" 
                                        class="page-btn <?php echo $i == $page ? 'active' : ''; ?>" 
                                        style="margin:0 5px;">
                                            <?php echo $i; ?>
                                        </a>
                                    <?php endfor; ?>

                                    <?php if ($page < $total_reviews_pages): ?>
                                        <a href="?id=<?php echo urlencode($product_id); ?>&sort=<?php echo urlencode($sort); ?>&page=<?php echo $page+1; ?>#reviews" 
                                        class="page-btn" style="margin:0 5px;">Next</a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                        
                        <!-- Writing review section -->
                        <?php if ($canReview): ?>
                            <div class="write-review-section" style="max-width:700px; margin:20px auto; padding:20px; border:1px solid #ddd; border-radius:10px; background:#f9f9f9;">
                                <h4 style="margin-bottom:15px;">Write a Review</h4>
                                <form class="review-form" method="POST" action="product.php?id=<?php echo $product_id; ?>">
                                     <!-- Rating -->
                                     <div class="rating-input" style="margin-bottom:15px;">
                                        <label style="display:block; margin-bottom:8px;">Your Rating:</label>
                                        <input type="radio" name="rating" value="5" id="star5" style="position:absolute; left:-9999px;">
                                        <input type="radio" name="rating" value="4" id="star4" style="position:absolute; left:-9999px;">
                                        <input type="radio" name="rating" value="3" id="star3" style="position:absolute; left:-9999px;">
                                        <input type="radio" name="rating" value="2" id="star2" style="position:absolute; left:-9999px;">
                                        <input type="radio" name="rating" value="1" id="star1" style="position:absolute; left:-9999px;">
                                        <div class="star-rating" style="display:flex; flex-direction:row-reverse; gap:8px; justify-content:flex-start;">
                                        <label for="star5" style="cursor:pointer; font-size:28px; color:#ccc;" onclick="setStars(5)">★</label>
                                        <label for="star4" style="cursor:pointer; font-size:28px; color:#ccc;" onclick="setStars(4)">★</label>
                                        <label for="star3" style="cursor:pointer; font-size:28px; color:#ccc;" onclick="setStars(3)">★</label>
                                        <label for="star2" style="cursor:pointer; font-size:28px; color:#ccc;" onclick="setStars(2)">★</label>
                                        <label for="star1" style="cursor:pointer; font-size:28px; color:#ccc;" onclick="setStars(1)">★</label>
                                    </div>
                                </div>

                                <!-- Title -->
                                <div class="form-group" style="margin-bottom:15px;">
                                    <label for="review-title">Review Title:</label>
                                    <input type="text" id="review-title" name="title" required>
                                </div>

                                <!-- Review Content -->
                                <div class="form-group" style="margin-bottom:15px;">
                                    <label for="review-content">Your Review:</label>
                                    <textarea id="review-content" name="content" rows="8" required></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary">Submit Review</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <div style="max-width:700px; margin:20px auto; padding:20px; text-align:center; border:1px solid #f5c2c7; border-radius:10px; background:#f8d7da; color:#842029;">
                            <p style="margin:0; font-size:15px; font-weight:500;">
                                ⚠️ Only verified customers who have completed an order for this product can write a review.
                            </p>
                        </div>
                    <?php endif; ?>
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
<section class="related-products">
    <div class="container">
        <h3>You Might Also Like</h3>
        
        <div class="related-grid">
            <?php if ($related_products): ?>
                <?php foreach ($related_products as $rel): ?>
                    <div class="related-item">
                        <img src="/<?= htmlspecialchars($rel['image']) ?>" 
                             alt="<?= htmlspecialchars($rel['name']) ?>" 
                             onerror="this.src='https://via.placeholder.com/200x150/f93c64/ffffff?text=<?= urlencode($rel['name']) ?>'">
                        <h4><a href="product.php?id=<?= urlencode($rel['product_id']) ?>">
                            <?= htmlspecialchars($rel['name']) ?>
                        </a></h4>
                        <div class="price">
                            RM <?= number_format($rel['sale_price'] ?? $rel['price'], 2) ?>
                            <?php if ($rel['sale_price'] && $rel['sale_price'] < $rel['price']): ?>
                                <span class="original-price">RM <?= number_format($rel['price'], 2) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No related products found.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
// Enhanced Product Page JavaScript

// For review rating stars
function setStars(rating) {
    let stars = document.querySelectorAll(".star-rating label");
    stars.forEach((star, i) => {
        star.style.color = (stars.length - i) <= rating ? "#FFD700" : "#ccc";
    });
    document.getElementById("star" + rating).checked = true;
}

document.addEventListener('DOMContentLoaded', function() {
    // Image Viewer Functionality
    const mainImage = document.getElementById('main-product-image');
    const thumbnails = document.querySelectorAll('.thumbnail');
    
    thumbnails.forEach(thumb => {
        thumb.addEventListener('click', function() {
            // Remove active class from all thumbnails
            thumbnails.forEach(t => t.classList.remove('active'));
            
            // Add active class to clicked thumbnail
            this.classList.add('active');
            
            // Update main image
            const newSrc = this.getAttribute('data-image');
            if (mainImage) {
                mainImage.style.opacity = '0.5';
                setTimeout(() => {
                    mainImage.src = newSrc;
                    mainImage.style.opacity = '1';
                }, 150);
            }
        });
    });
    
    // Color Option Selection
    const colorOptions = document.querySelectorAll('.color-option');
    colorOptions.forEach(option => {
        option.addEventListener('click', function() {
            colorOptions.forEach(opt => opt.classList.remove('active'));
            this.classList.add('active');
        });
    });
    
    // Tab Functionality
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            // Remove active class from all buttons and panes
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabPanes.forEach(pane => pane.classList.remove('active'));
            
            // Add active class to clicked button and corresponding pane
            this.classList.add('active');
            const targetPane = document.getElementById(targetTab);
            if (targetPane) {
                targetPane.classList.add('active');
            }
        });
    });
    
    // Quantity Input Validation
    const quantityInput = document.getElementById('quantity');
    if (quantityInput) {
        quantityInput.addEventListener('change', function() {
            const min = parseInt(this.getAttribute('min'));
            const max = parseInt(this.getAttribute('max'));
            let value = parseInt(this.value);
            
            if (value < min) this.value = min;
            if (value > max) this.value = max;
        });
    }
    
    // Add to Cart Animation
   const addToCartBtn = document.querySelector('.product-actions .btn-primary');
    if (addToCartBtn) {
    addToCartBtn.addEventListener('click', function(e) {
        // Only run animation
        const originalText = this.innerHTML;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
        this.disabled = true;

        if (typeof launchConfetti === 'function') {
            launchConfetti();
        }

        // Reset after cart.js finishes 
        setTimeout(() => {
            this.innerHTML = originalText;
            this.disabled = false;
        }, 1500);
    });
}
    
    // Image Zoom on Hover (Desktop)
    if (window.innerWidth > 768) {
        const mainImageContainer = document.querySelector('.main-image');
        if (mainImageContainer) {
            mainImageContainer.addEventListener('mouseenter', function() {
                this.style.cursor = 'zoom-in';
            });
            
            mainImageContainer.addEventListener('mouseleave', function() {
                this.style.cursor = 'default';
            });
        }
    }
});
</script>

<script src="/assets/js/cart.js"></script>

<?php
// Include footer
include '../includes/footer.php';
?> 