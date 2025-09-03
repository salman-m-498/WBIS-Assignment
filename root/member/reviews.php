<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header('Location: ../public/login.php');
    exit();
}

$userId = $_SESSION['user_id'];

// -----------------------------
// 1. Pending Reviews Section
// -----------------------------
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$totalStmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.order_id
    JOIN products p ON oi.product_id = p.product_id
    WHERE o.user_id = :userId
      AND o.order_status = 'delivered'
      AND NOT EXISTS (
          SELECT 1 FROM reviews r 
          WHERE r.product_id = oi.product_id 
            AND r.user_id = :userId
      )
");
$totalStmt->execute(['userId' => $userId]);
$totalItems = $totalStmt->fetchColumn();
$totalPages = ceil($totalItems / $limit);

$sql = "
    SELECT oi.product_id, p.name, p.image,
           (SELECT pi.image_path 
            FROM product_images pi 
            WHERE pi.product_id = p.product_id 
            ORDER BY pi.image_id ASC LIMIT 1) AS image_path
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.order_id
    JOIN products p ON oi.product_id = p.product_id
    WHERE o.user_id = :userId
      AND o.order_status = 'delivered'
      AND NOT EXISTS (
          SELECT 1 FROM reviews r 
          WHERE r.product_id = oi.product_id 
            AND r.user_id = :userId
      )
    GROUP BY oi.product_id, p.name, p.image
    ORDER BY o.created_at DESC
    LIMIT :limit OFFSET :offset
";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$pendingReviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

// -----------------------------
// 2. Submitted Reviews Section
// -----------------------------
$sql = "
    SELECT r.rating, r.comment, r.created_at,
           p.product_id, p.name, p.image,
           (SELECT pi.image_path 
            FROM product_images pi 
            WHERE pi.product_id = p.product_id 
            ORDER BY pi.image_id ASC LIMIT 1) AS image_path
    FROM reviews r
    JOIN products p ON r.product_id = p.product_id
    WHERE r.user_id = :userId
    ORDER BY r.created_at DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute(['userId' => $userId]);
$submittedReviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<div class="container" style="max-width:1200px; margin-top:30px;">

    <!-- Pending Reviews -->
    <h2>Pending Reviews</h2>
    <p>Select a product from your completed orders to leave a review.</p>

    <?php if (!empty($pendingReviews)): ?>
        <div style="display:flex; flex-direction:column; gap:20px; padding:10px;">
            <?php foreach ($pendingReviews as $item): ?>
                <div style="display:flex; align-items:center; background:#fff; border:1px solid #ddd; border-radius:12px; padding:15px; box-shadow:0 2px 8px rgba(0,0,0,0.1);">
                    <div style="margin-right:20px; flex-shrink:0;">
                        <a href="../public/product.php?id=<?= urlencode($item['product_id']) ?>#reviews">
                            <img src="../<?= htmlspecialchars($item['image_path'] ?? $item['image']) ?>" 
                                alt="<?= htmlspecialchars($item['name']) ?>"
                                style="width:100px; height:100px; object-fit:cover; border-radius:10px;">
                        </a>
                    </div>
                    <div style="flex:1;">
                        <h3 style="margin:0 0 10px;"><?= htmlspecialchars($item['name']) ?></h3>
                        <a href="../public/product.php?id=<?= urlencode($item['product_id']) ?>#reviews" 
                           class="btn btn-primary"
                           style="padding:8px 15px; background:#f93c64; color:#fff; border-radius:6px; text-decoration:none;">
                            Leave a Review
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p style="color:#666;">🎉 No products left to review.</p>
    <?php endif; ?>

    <?php if ($totalPages > 1): ?>
        <div class="pagination" style="margin-top:20px; text-align:center;">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>" class="page-btn">Prev</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>" 
                   class="page-btn <?= ($i == $page) ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?>" class="page-btn">Next</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <hr style="margin:40px 0;">

    <!-- Submitted Reviews -->
    <h2>My Reviews</h2>
    <?php if (!empty($submittedReviews)): ?>
        <div style="display:flex; flex-direction:column; gap:20px; padding:10px;">
            <?php foreach ($submittedReviews as $review): ?>
                <div style="background:#fff; border:1px solid #ddd; border-radius:12px; padding:15px; box-shadow:0 2px 8px rgba(0,0,0,0.1);">
                    <div style="display:flex; align-items:center; gap:15px;">
                        <a href="../public/product.php?id=<?= urlencode($review['product_id']) ?>">
                            <img src="../<?= htmlspecialchars($review['image_path'] ?? $review['image']) ?>" 
                                alt="<?= htmlspecialchars($review['name']) ?>"
                                style="width:80px; height:80px; object-fit:cover; border-radius:10px;">
                        </a>
                        <div style="flex:1;">
                            <h3 style="margin:0;"><?= htmlspecialchars($review['name']) ?></h3>
                            <p style="margin:5px 0; color:#666;">
                                ⭐ <?= str_repeat("★", (int)$review['rating']) ?>
                            </p>
                            <p style="margin:5px 0;"><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                            <small style="color:#888;">
                                <?= date("F j, Y", strtotime($review['created_at'])) ?>
                            </small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p style="color:#666;">You haven't written any reviews yet.</p>
    <?php endif; ?>
</div>

<div style="height:80px;"></div>
