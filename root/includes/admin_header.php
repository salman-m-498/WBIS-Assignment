<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is admin
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header('Location: admin_login.php');
    exit();
}

require_once __DIR__ . '/../includes/db.php';

// Determine the correct base path for assets
$current_dir = basename(dirname($_SERVER['SCRIPT_NAME']));
$is_admin = ($current_dir === 'admin');
$assets_path = $is_admin ? '../assets' : 'assets';
$root_path = $is_admin ? '../' : '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>ToyLand Admin</title>
    <meta name="description" content="<?php echo isset($page_description) ? $page_description : 'ToyLand Store Administration Panel'; ?>">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@500;700&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo $assets_path; ?>/css/style.css">
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="admin-body">
    <div class="admin-layout">
    <!-- Admin Top Bar -->
    <div class="admin-top-bar">
        <div class="container">
            <div class="admin-top-left">
                <span><i class="fas fa-shield-alt"></i> Admin Panel</span>
                <span class="admin-user">Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
            </div>
            <div class="admin-top-right">
                <span class="current-time" id="current-time"></span>
                <a href="<?php echo $root_path; ?>index.php" target="_blank" class="view-site">
                    <i class="fas fa-external-link-alt"></i> View Site
                </a>
                <a href="<?php echo $root_path; ?>admin/admin_logout.php" class="admin-logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </div>

    <!-- Admin Header -->
    <header class="admin-header">
            <div class="admin-header-content">
                <div class="admin-logo">
                    <a href="dashboard.php">
                        <i class="fas fa-cogs"></i>
                        <div class="logo-text">
                            <h1>ToyLand</h1>
                            <span>Administration</span>
                        </div>
                    </a>
                </div>
                
                <div class="admin-search">
                    <form action="search.php" method="GET" class="admin-search-form">
                        <input type="text" name="q" placeholder="Search products, orders, members..." value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>
                    
                    <div class="admin-profile dropdown">
    <a href="javascript:void(0);" class="admin-profile-link" onclick="toggleDropdown('profileDropdown')">
        <i class="fas fa-user-circle"></i>
        <span><?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
        <i class="fas fa-chevron-down"></i>
    </a>
    <div id="profileDropdown" class="dropdown-menu admin-dropdown">
        <a href="<?php echo $root_path; ?>admin/admin_logout.php">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</div>

            
            <!-- Admin Navigation -->
            <nav class="admin-nav">
                <ul class="admin-nav-menu">
                    <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                        <a href="dashboard.php">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>">
                        <a href="products.php">
                            <i class="fas fa-box"></i>
                            <span>Products</span>
                        </a>
                    </li>
                    <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>">
                        <a href="orders.php">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Orders</span>
                        </a>
                    </li>
                    <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'members.php' ? 'active' : ''; ?>">
                        <a href="members.php">
                            <i class="fas fa-users"></i>
                            <span>Members</span>
                        </a>
                    </li>
                    <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>">
                        <a href="categories.php">
                            <i class="fas fa-tags"></i>
                            <span>Categories</span>
                        </a>
                    </li>
                    <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'reviews.php' ? 'active' : ''; ?>">
                        <a href="reviews.php">
                            <i class="fas fa-star"></i>
                            <span>Reviews</span>
                        </a>
                    </li>
                    <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'vouchers.php' ? 'active' : ''; ?>">
                        <a href="vouchers.php">
                            <i class="fas fa-ticket-alt"></i>
                            <span>Vouchers</span>
                        </a>
                    </li>
                </ul>
            </nav>
             </div>
    </header>


    <script>
    // Update current time
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString();
        document.getElementById('current-time').textContent = timeString;
    }
    
    // Update time every second
    setInterval(updateTime, 1000);
    updateTime(); // Initial call
    
    // Dropdown toggle function
    function toggleDropdown(id) {
    const dropdown = document.getElementById(id);

    // close all dropdowns first
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        if (menu.id !== id) menu.classList.remove('show');
    });

    // toggle clicked one
    dropdown.classList.toggle('show');
}

// close if clicked outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.admin-profile')) {
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.classList.remove('show');
        });
    }
});
    </script>