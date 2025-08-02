<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../public/login.php');
    exit();
}

// Page variables
$page_title = "Admin Dashboard";
$page_description = "ToyLand Store administration dashboard";
$show_breadcrumb = true;
$breadcrumb_items = [
    ['url' => 'dashboard.php', 'title' => 'Admin Dashboard']
];

// Include header
include '../includes/header.php';
?>

<!-- Admin Dashboard Section -->
<section class="dashboard-section admin-dashboard">
    <div class="container">
        <div class="dashboard-header">
            <h1>Admin Dashboard</h1>
            <p>Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>! Manage your store</p>
        </div>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <div class="dashboard-grid">
            <div class="dashboard-card">
                <div class="card-icon">
                    <i class="fas fa-box"></i>
                </div>
                <div class="card-content">
                    <h3>Products</h3>
                    <p>Manage store products and inventory</p>
                    <a href="products.php" class="btn btn-primary">Manage Products</a>
                </div>
            </div>
            
            <div class="dashboard-card">
                <div class="card-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="card-content">
                    <h3>Orders</h3>
                    <p>View and manage customer orders</p>
                    <a href="orders.php" class="btn btn-primary">View Orders</a>
                </div>
            </div>
            
            <div class="dashboard-card">
                <div class="card-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="card-content">
                    <h3>Members</h3>
                    <p>Manage customer accounts</p>
                    <a href="members.php" class="btn btn-primary">View Members</a>
                </div>
            </div>
            
            <div class="dashboard-card">
                <div class="card-icon">
                    <i class="fas fa-tags"></i>
                </div>
                <div class="card-content">
                    <h3>Categories</h3>
                    <p>Manage product categories</p>
                    <a href="categories.php" class="btn btn-primary">Manage Categories</a>
                </div>
            </div>
        </div>
        
        <div class="admin-info">
            <h2>Administrator Information</h2>
            <div class="info-grid">
                <div class="info-item admin">
                    <strong>Admin ID:</strong> <?php echo htmlspecialchars($_SESSION['user_id']); ?>
                </div>
                <div class="info-item admin">
                    <strong>Username:</strong> <?php echo htmlspecialchars($_SESSION['username']); ?>
                </div>
                <div class="info-item admin">
                    <strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?>
                </div>
                <div class="info-item admin">
                    <strong>Role:</strong> <?php echo ucfirst($_SESSION['role']); ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
include '../includes/footer.php';
?>
