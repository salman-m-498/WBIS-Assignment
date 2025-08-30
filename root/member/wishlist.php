<?php
session_start();
require_once '../includes/db.php'; 

if (!isset($_SESSION['user_id'])) {
    header('Location: ../public/login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$limit = 15; // items per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Count total wishlist items
$countSql = "SELECT COUNT(*) FROM wishlist WHERE user_id = ?";
$stmt = $pdo->prepare($countSql);
$stmt->execute([$userId]);
$total_items = $stmt->fetchColumn();
$total_pages = ceil($total_items / $limit);

// Fetch current page wishlist
$sql = "SELECT w.product_id, w.created_at, p.image, p.sale_price, p.stock_quantity
        FROM wishlist w
        LEFT JOIN products p ON w.product_id = p.product_id
        WHERE w.user_id = ?
        ORDER BY w.created_at DESC
        LIMIT ? OFFSET ?";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(1, $userId, PDO::PARAM_STR);
$stmt->bindValue(2, $limit, PDO::PARAM_INT);
$stmt->bindValue(3, $offset, PDO::PARAM_INT);
$stmt->execute();
$wishlist = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Include header
include '../includes/header.php';
?>

<section class="dashboard-section">
    <div class="container">
        <div class="dashboard-header">
            <h1>Wishlist</h1>
            <p>Track all your wishlist here</p>
        </div>

        <div class="dashboard-grid">
            <?php foreach ($wishlist as $w): ?>
                <div class="dashboard-card" data-product-id="<?= htmlspecialchars($w['product_id']) ?>">
                    <button class="remove-wishlist" 
                            style="position:absolute; top:5px; right:5px; background:none; border:none; color:red; font-size:18px; cursor:pointer;">×</button>
                    <div class="card-icon">
                        <?php if (!empty($w['image'])): ?>
                            <img src="../<?= htmlspecialchars($w['image']) ?>" 
                                alt="Product Image" 
                                style="width:100px; height:100px; object-fit:cover; border-radius:8px;">
                        <?php else: ?>
                            <i class="fas fa-box"></i>
                        <?php endif; ?>
                    </div>
                    <div class="card-content" style="line-height:1.2;">
                        <h2 style="margin:0; font-size:14px;">Added: <?= htmlspecialchars($w['created_at']) ?></h2>
                        <?php if (isset($w['sale_price'])): ?>
                            <h2 style="margin:0; font-size:14px;">Price: $<?= number_format($w['sale_price'],2) ?></h2>
                        <?php endif; ?>
                        <?php if (isset($w['stock_quantity'])): ?>
                            <h2 style="margin:0; font-size:14px;">Stock: <?= htmlspecialchars($w['stock_quantity']) ?></h2>
                        <?php endif; ?>
                        <a href="/public/product.php?id=<?= urlencode($w['product_id']) ?>" 
                        class="btn btn-primary" style="margin-top:4px; display:inline-block;">View Product</a>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (empty($wishlist)): ?>
                <div class="dashboard-card">
                    <div class="card-icon">
                        <i class="fas fa-heart-broken"></i>
                    </div>
                    <div class="card-content">
                        <h3>No Wishlist Items</h3>
                        <p>You haven't added anything to your wishlist yet.</p>
                        <a href="../public/products.php" class="btn btn-primary">Browse Products</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination" style="margin-top:20px; text-align:center;">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>" 
                    style="display:inline-block; margin:2px; padding:8px 12px; background:#f3f3f3; color:#000; text-decoration:none; border-radius:4px;">
                    Prev
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?= $i ?>" 
                    style="display:inline-block; margin:2px; padding:8px 12px; 
                            background: <?= ($i == $page) ? '#f93c64' : '#f3f3f3' ?>; 
                            color: <?= ($i == $page) ? '#fff' : '#000' ?>; 
                            text-decoration:none; border-radius:4px;">
                    <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?= $page + 1 ?>" 
                    style="display:inline-block; margin:2px; padding:8px 12px; background:#f3f3f3; color:#000; text-decoration:none; border-radius:4px;">
                    Next
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include '../includes/footer.php'; ?>



<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.remove-wishlist').forEach(btn => {
        btn.addEventListener('click', function() {
            const card = this.closest('.dashboard-card');
            const productId = card.dataset.productId;

            fetch('toggle-wishlist.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `product_id=${productId}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.action === 'removed') {
                    card.remove(); // remove card from UI
                }
            })
            .catch(err => console.error(err));
        });
    });
});
</script>
