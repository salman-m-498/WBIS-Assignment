<?php
session_start();

// Page variables
$page_title = "Products";
$page_description = "Browse our complete collection of toys and games for all ages";
$show_breadcrumb = true;
$breadcrumb_items = [
    ['url' => 'products.php', 'title' => 'Products']
];

// Get filter parameters
$category = isset($_GET['category']) ? $_GET['category'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'name';
$price_min = isset($_GET['price_min']) ? $_GET['price_min'] : '';
$price_max = isset($_GET['price_max']) ? $_GET['price_max'] : '';
$age_group = isset($_GET['age_group']) ? $_GET['age_group'] : '';
$brand = isset($_GET['brand']) ? $_GET['brand'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 12;

// Include header
include 'includes/header.php';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>All Products</h1>
        <p>Discover amazing toys and games for every age and interest</p>
    </div>
</section>

<!-- Products Section -->
<section class="products-section">
    <div class="container">
        <div class="products-layout">
            <!-- Sidebar Filters -->
            <aside class="products-sidebar">
                <div class="filter-section">
                    <h3>Filters</h3>
                    
                    <!-- Category Filter -->
                    <div class="filter-group">
                        <h4>Category</h4>
                        <ul class="filter-list">
                            <li>
                                <label>
                                    <input type="checkbox" name="category" value="action-figures" <?php echo ($category == 'action-figures') ? 'checked' : ''; ?>>
                                    Action Figures
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="checkbox" name="category" value="board-games" <?php echo ($category == 'board-games') ? 'checked' : ''; ?>>
                                    Board Games
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="checkbox" name="category" value="educational" <?php echo ($category == 'educational') ? 'checked' : ''; ?>>
                                    Educational Toys
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="checkbox" name="category" value="outdoor" <?php echo ($category == 'outdoor') ? 'checked' : ''; ?>>
                                    Outdoor Toys
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="checkbox" name="category" value="arts-crafts" <?php echo ($category == 'arts-crafts') ? 'checked' : ''; ?>>
                                    Arts & Crafts
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="checkbox" name="category" value="babies-toddlers" <?php echo ($category == 'babies-toddlers') ? 'checked' : ''; ?>>
                                    Babies & Toddlers
                                </label>
                            </li>
                        </ul>
                    </div>
                    
                    <!-- Price Filter -->
                    <div class="filter-group">
                        <h4>Price Range</h4>
                        <div class="price-range">
                            <input type="number" name="price_min" placeholder="Min" value="<?php echo $price_min; ?>">
                            <span>to</span>
                            <input type="number" name="price_max" placeholder="Max" value="<?php echo $price_max; ?>">
                        </div>
                    </div>
                    
                    <!-- Age Group Filter -->
                    <div class="filter-group">
                        <h4>Age Group</h4>
                        <ul class="filter-list">
                            <li>
                                <label>
                                    <input type="checkbox" name="age_group" value="0-2" <?php echo ($age_group == '0-2') ? 'checked' : ''; ?>>
                                    0-2 years
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="checkbox" name="age_group" value="3-5" <?php echo ($age_group == '3-5') ? 'checked' : ''; ?>>
                                    3-5 years
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="checkbox" name="age_group" value="6-8" <?php echo ($age_group == '6-8') ? 'checked' : ''; ?>>
                                    6-8 years
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="checkbox" name="age_group" value="9-12" <?php echo ($age_group == '9-12') ? 'checked' : ''; ?>>
                                    9-12 years
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="checkbox" name="age_group" value="13+" <?php echo ($age_group == '13+') ? 'checked' : ''; ?>>
                                    13+ years
                                </label>
                            </li>
                        </ul>
                    </div>
                    
                    <!-- Brand Filter -->
                    <div class="filter-group">
                        <h4>Brand</h4>
                        <ul class="filter-list">
                            <li>
                                <label>
                                    <input type="checkbox" name="brand" value="lego" <?php echo ($brand == 'lego') ? 'checked' : ''; ?>>
                                    LEGO
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="checkbox" name="brand" value="hasbro" <?php echo ($brand == 'hasbro') ? 'checked' : ''; ?>>
                                    Hasbro
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="checkbox" name="brand" value="mattel" <?php echo ($brand == 'mattel') ? 'checked' : ''; ?>>
                                    Mattel
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="checkbox" name="brand" value="fisher-price" <?php echo ($brand == 'fisher-price') ? 'checked' : ''; ?>>
                                    Fisher-Price
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="checkbox" name="brand" value="melissa-doug" <?php echo ($brand == 'melissa-doug') ? 'checked' : ''; ?>>
                                    Melissa & Doug
                                </label>
                            </li>
                        </ul>
                    </div>
                    
                    <!-- Clear Filters -->
                    <button class="btn btn-outline clear-filters">Clear All Filters</button>
                </div>
            </aside>
            
            <!-- Products Content -->
            <main class="products-content">
                <!-- Products Header -->
                <div class="products-header">
                    <div class="products-count">
                        <span>Showing 1-12 of 48 products</span>
                    </div>
                    
                    <div class="products-controls">
                        <div class="sort-controls">
                            <label for="sort">Sort by:</label>
                            <select id="sort" name="sort">
                                <option value="name" <?php echo ($sort == 'name') ? 'selected' : ''; ?>>Name A-Z</option>
                                <option value="name_desc" <?php echo ($sort == 'name_desc') ? 'selected' : ''; ?>>Name Z-A</option>
                                <option value="price" <?php echo ($sort == 'price') ? 'selected' : ''; ?>>Price Low to High</option>
                                <option value="price_desc" <?php echo ($sort == 'price_desc') ? 'selected' : ''; ?>>Price High to Low</option>
                                <option value="rating" <?php echo ($sort == 'rating') ? 'selected' : ''; ?>>Highest Rated</option>
                                <option value="newest" <?php echo ($sort == 'newest') ? 'selected' : ''; ?>>Newest First</option>
                            </select>
                        </div>
                        
                        <div class="view-controls">
                            <button class="view-btn grid-view active" data-view="grid">
                                <i class="fas fa-th"></i>
                            </button>
                            <button class="view-btn list-view" data-view="list">
                                <i class="fas fa-list"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Products Grid -->
                <div class="products-grid" id="products-container">
                    <!-- Product 1 -->
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
                    
                    <!-- Product 2 -->
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
                    
                    <!-- Product 3 -->
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
                    
                    <!-- Product 4 -->
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
                    
                    <!-- Product 5 -->
                    <div class="product-card">
                        <div class="product-image">
                            <img src="assets/images/product-5.jpg" alt="Outdoor Play Set">
                            <div class="product-overlay">
                                <button class="quick-view" data-product-id="5">Quick View</button>
                                <button class="add-to-wishlist" data-product-id="5"><i class="far fa-heart"></i></button>
                            </div>
                        </div>
                        <div class="product-content">
                            <h3><a href="product.php?id=5">Outdoor Play Set</a></h3>
                            <div class="product-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <span>(22 reviews)</span>
                            </div>
                            <div class="product-price">
                                <span class="current-price">$89.99</span>
                            </div>
                            <button class="add-to-cart" data-product-id="5">Add to Cart</button>
                        </div>
                    </div>
                    
                    <!-- Product 6 -->
                    <div class="product-card">
                        <div class="product-image">
                            <img src="assets/images/product-6.jpg" alt="Baby Rattle Set">
                            <div class="product-overlay">
                                <button class="quick-view" data-product-id="6">Quick View</button>
                                <button class="add-to-wishlist" data-product-id="6"><i class="far fa-heart"></i></button>
                            </div>
                        </div>
                        <div class="product-content">
                            <h3><a href="product.php?id=6">Baby Rattle Set</a></h3>
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
                            <button class="add-to-cart" data-product-id="6">Add to Cart</button>
                        </div>
                    </div>
                    
                    <!-- Product 7 -->
                    <div class="product-card">
                        <div class="product-image">
                            <img src="assets/images/product-7.jpg" alt="Science Experiment Kit">
                            <div class="product-overlay">
                                <button class="quick-view" data-product-id="7">Quick View</button>
                                <button class="add-to-wishlist" data-product-id="7"><i class="far fa-heart"></i></button>
                            </div>
                            <div class="product-badge sale">Sale</div>
                        </div>
                        <div class="product-content">
                            <h3><a href="product.php?id=7">Science Experiment Kit</a></h3>
                            <div class="product-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <span>(28 reviews)</span>
                            </div>
                            <div class="product-price">
                                <span class="current-price">$44.99</span>
                                <span class="original-price">$59.99</span>
                            </div>
                            <button class="add-to-cart" data-product-id="7">Add to Cart</button>
                        </div>
                    </div>
                    
                    <!-- Product 8 -->
                    <div class="product-card">
                        <div class="product-image">
                            <img src="assets/images/product-8.jpg" alt="Puzzle Set">
                            <div class="product-overlay">
                                <button class="quick-view" data-product-id="8">Quick View</button>
                                <button class="add-to-wishlist" data-product-id="8"><i class="far fa-heart"></i></button>
                            </div>
                        </div>
                        <div class="product-content">
                            <h3><a href="product.php?id=8">Puzzle Set</a></h3>
                            <div class="product-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="far fa-star"></i>
                                <span>(19 reviews)</span>
                            </div>
                            <div class="product-price">
                                <span class="current-price">$16.99</span>
                            </div>
                            <button class="add-to-cart" data-product-id="8">Add to Cart</button>
                        </div>
                    </div>
                </div>
                
                <!-- Pagination -->
                <div class="pagination">
                    <ul class="pagination-list">
                        <li class="pagination-item">
                            <a href="?page=1" class="pagination-link">First</a>
                        </li>
                        <li class="pagination-item">
                            <a href="?page=<?php echo $page - 1; ?>" class="pagination-link <?php echo ($page <= 1) ? 'disabled' : ''; ?>">Previous</a>
                        </li>
                        <li class="pagination-item">
                            <a href="?page=1" class="pagination-link <?php echo ($page == 1) ? 'active' : ''; ?>">1</a>
                        </li>
                        <li class="pagination-item">
                            <a href="?page=2" class="pagination-link <?php echo ($page == 2) ? 'active' : ''; ?>">2</a>
                        </li>
                        <li class="pagination-item">
                            <a href="?page=3" class="pagination-link <?php echo ($page == 3) ? 'active' : ''; ?>">3</a>
                        </li>
                        <li class="pagination-item">
                            <a href="?page=4" class="pagination-link <?php echo ($page == 4) ? 'active' : ''; ?>">4</a>
                        </li>
                        <li class="pagination-item">
                            <a href="?page=<?php echo $page + 1; ?>" class="pagination-link <?php echo ($page >= 4) ? 'disabled' : ''; ?>">Next</a>
                        </li>
                        <li class="pagination-item">
                            <a href="?page=4" class="pagination-link">Last</a>
                        </li>
                    </ul>
                </div>
            </main>
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

<?php
// Include footer
include 'includes/footer.php';
?> 