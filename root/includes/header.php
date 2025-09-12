<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/db.php';

// Determine the correct base path for assets
$current_dir = basename(dirname($_SERVER['SCRIPT_NAME']));
$is_subdirectory = in_array($current_dir, ['public', 'member', 'admin']);
$assets_path = $is_subdirectory ? '../assets' : 'assets';
$root_path = $is_subdirectory ? '../' : '';

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT SUM(quantity) AS total FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $cart_count = (int) $stmt->fetchColumn();
} else {
    $cart_count = 0;
}

// Fetch main categories with their subcategories
$navStmt = $pdo->query("SELECT category_id, name FROM categories WHERE parent_id IS NULL ORDER BY name ASC");
$mainCategories = $navStmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Toy Land</title>
    <meta name="description" content="<?php echo isset($page_description) ? $page_description : 'Where Fun Comes to Life! Discover toys that spark imagination and smiles'; ?>">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@500;700&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo $assets_path; ?>/css/style.css">
    
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
                <span>🚀 Fast Delivery</span>
                <span>🧸 Unique Characters</span>
                <span>🎁 Gift Ready</span>
            </div>
            <div class="top-bar-right">
                <a href="shipping.php">Free Shipping on Orders Over RM150</a>
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
                    <a href="<?php echo $root_path; ?>index.php">
                        <h1>Toy Land</h1>
                        <span>Where Fun Comes to Life!</span>
                    </a>
                </div>
                
                <div class="search-bar">
                    <form action="<?php echo $root_path; ?>public/search.php" method="GET">
                        <input type="text" name="q" placeholder="Search for toys, games, and more..." value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>
                
                <div class="header-actions">
                    <div class="user-account">
                        <?php if (isset($_SESSION['user_id'])): ?>
                                <?php if ($_SESSION['role'] === 'admin'): ?> 
                                    <a href="<?php echo $root_path; ?>admin/dashboard.php"><i class="fas fa-user"></i> My Account</a>
                                <?php else: ?>
                                    <a href="<?php echo $root_path; ?>member/dashboard.php"><i class="fas fa-user"></i> My Account</a>
                                <?php endif; ?>
                            <a href="<?php echo $root_path; ?>public/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        <?php else: ?>
                            <a href="<?php echo $root_path; ?>public/login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                            <a href="<?php echo $root_path; ?>public/register.php"><i class="fas fa-user-plus"></i> Register</a>
                        <?php endif; ?>
                    </div>
                    
                    <div class="cart">
                        <a href="<?php echo $root_path; ?>public/cart.php" class="cart-icon">
                            <i class="fas fa-shopping-cart"></i>
                            <span id="cart-count" class="cart-count">
                                <?php echo  $cart_count; ?>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
            
           <!-- Navigation -->
<nav class="main-nav">
    <ul class="nav-menu">
        <li><a href="<?php echo $root_path; ?>index.php">Home</a></li>
        <li class="dropdown">
            <a href="<?php echo $root_path; ?>public/products.php">Categories <i class="fas fa-chevron-down"></i></a>
            <ul class="dropdown-menu">
             <?php
                // Fetch only main categories
                $stmt = $pdo->query("SELECT category_id, name FROM categories WHERE parent_id IS NULL ORDER BY name ASC");
                $mainCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($mainCategories as $main):
                ?>
                    <li>
                        <a href="<?php echo $root_path; ?>public/products.php?category=<?= $main['category_id'] ?>">
                            <?= htmlspecialchars($main['name']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </li>
        <li><a href="<?php echo $root_path; ?>public/new_arrivals.php">New Arrivals</a></li>
        <li><a href="<?php echo $root_path; ?>public/sale.php">Sale</a></li>
        <li><a href="<?php echo $root_path; ?>public/vouchers.php">Vouchers</a></li>
        <li><a href="<?php echo $root_path; ?>public/about.php">About Us</a></li>
        <li><a href="<?php echo $root_path; ?>public/contact.php">Contact</a></li>
    </ul>
</nav>
        </div>
    </header>


    <!-- Breadcrumb -->
    <?php if (isset($show_breadcrumb) && $show_breadcrumb): ?>
    <div class="breadcrumb">
        <div class="container">
            <ul>
                <li><a href="<?php echo $root_path; ?>index.php">Home</a></li>
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
