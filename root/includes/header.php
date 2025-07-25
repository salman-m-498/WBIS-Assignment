<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>ToyLand Store</title>
    <meta name="description" content="<?php echo isset($page_description) ? $page_description : 'Your one-stop shop for amazing toys and games for all ages'; ?>">
    
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div class="top-bar-left">
                <span><i class="fas fa-phone"></i> Call us: (555) 123-4567</span>
                <span><i class="fas fa-envelope"></i> info@toylandstore.com</span>
            </div>
            <div class="top-bar-right">
                <a href="shipping.php">Free Shipping on Orders Over $50</a>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="main-header">
        <div class="container">
            <div class="header-top">
                <div class="logo">
                    <a href="index.php">
                        <h1>ToyLand Store</h1>
                        <span>Where Imagination Comes to Life</span>
                    </a>
                </div>
                
                <div class="search-bar">
                    <form action="search.php" method="GET">
                        <input type="text" name="q" placeholder="Search for toys, games, and more..." value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>
                
                <div class="header-actions">
                    <div class="user-account">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="account.php"><i class="fas fa-user"></i> My Account</a>
                            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        <?php else: ?>
                            <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                            <a href="register.php"><i class="fas fa-user-plus"></i> Register</a>
                        <?php endif; ?>
                    </div>
                    
                    <div class="cart">
                        <a href="cart.php" class="cart-icon">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="cart-count"><?php echo isset($_SESSION['cart_count']) ? $_SESSION['cart_count'] : 0; ?></span>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Navigation -->
            <nav class="main-nav">
                <ul class="nav-menu">
                    <li><a href="index.php">Home</a></li>
                    <li class="dropdown">
                        <a href="products.php">Categories <i class="fas fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="products.php?category=action-figures">Action Figures</a></li>
                            <li><a href="products.php?category=board-games">Board Games</a></li>
                            <li><a href="products.php?category=educational">Educational Toys</a></li>
                            <li><a href="products.php?category=outdoor">Outdoor Toys</a></li>
                            <li><a href="products.php?category=arts-crafts">Arts & Crafts</a></li>
                            <li><a href="products.php?category=babies-toddlers">Babies & Toddlers</a></li>
                        </ul>
                    </li>
                    <li><a href="new-arrivals.php">New Arrivals</a></li>
                    <li><a href="sale.php">Sale</a></li>
                    <li><a href="brands.php">Brands</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Breadcrumb -->
    <?php if (isset($show_breadcrumb) && $show_breadcrumb): ?>
    <div class="breadcrumb">
        <div class="container">
            <ul>
                <li><a href="index.php">Home</a></li>
                <?php if (isset($breadcrumb_items)): ?>
                    <?php foreach ($breadcrumb_items as $item): ?>
                        <li><a href="<?php echo $item['url']; ?>"><?php echo $item['title']; ?></a></li>
                    <?php endforeach; ?>
                <?php endif; ?>
                <li class="current"><?php echo $page_title; ?></li>
            </ul>
        </div>
    </div>
    <?php endif; ?>
