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
    WHERE o.user_id = :userId
      AND o.order_status = 'delivered'
      AND NOT EXISTS (
          SELECT 1 
          FROM reviews r 
          WHERE r.product_id = oi.product_id 
            AND r.user_id = :userId
            AND r.order_id = oi.order_id
      )
");
$totalStmt->bindValue(':userId', $userId, PDO::PARAM_STR); 
$totalStmt->execute();
$totalItems = $totalStmt->fetchColumn();
$totalPages = ceil($totalItems / $limit);


$sql = "
    SELECT oi.order_id, oi.product_id, p.name, p.image,
           pi.image_path
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.order_id
    JOIN products p ON oi.product_id = p.product_id
    LEFT JOIN product_images pi 
           ON pi.product_id = p.product_id
    WHERE o.user_id = :userId
      AND o.order_status = 'delivered'
      AND NOT EXISTS (
          SELECT 1 
          FROM reviews r 
          WHERE r.product_id = oi.product_id 
            AND r.user_id = :userId
            AND r.order_id = oi.order_id
      )
    GROUP BY oi.order_id, oi.product_id
    ORDER BY o.created_at DESC
    LIMIT :limit OFFSET :offset
";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':userId', $userId, PDO::PARAM_STR); 
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$pendingReviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

// -----------------------------
// 2. Submitted Reviews Section
// -----------------------------
$sql = "
    SELECT r.review_id, r.rating, r.comment, r.created_at,
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

<?php if (isset($_GET['msg'])): ?>
    <?php if ($_GET['msg'] === 'deleted'): ?>
        <div id="flash-message" style="background:#d4edda; color:#155724; border:1px solid #c3e6cb; padding:10px; border-radius:5px; margin-bottom:15px;">
            ✅ Review deleted successfully.
        </div>
    <?php elseif ($_GET['msg'] === 'error'): ?>
        <div id="flash-message" style="background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; padding:10px; border-radius:5px; margin-bottom:15px;">
            ❌ Failed to delete review. Please try again.
        </div>
    <?php endif; ?>
<?php endif; ?>

<div class="reviews-page">
<div class="member-reviews-section">
    <div class="page-header">

    <!-- Pending Reviews -->
    <h2>Pending Reviews</h2>
    <p>Select a product from your completed orders to leave a review.</p>
    </div>

    <?php if (!empty($pendingReviews)): ?>
        <div class="reviews-list">
            <?php foreach ($pendingReviews as $item): ?>
                <div class="pending-review-card">
                        <a href="../public/product.php?id=<?= urlencode($item['product_id']) ?>&order_id=<?= urlencode($item['order_id']) ?>#reviews">
                            <img src="../<?= htmlspecialchars($item['image_path'] ?? $item['image']) ?>" 
                                alt="<?= htmlspecialchars($item['name']) ?>"
                                style="width:100px; height:100px; object-fit:cover; border-radius:10px;">
                        </a>
                    <div>
                        <h3><?= htmlspecialchars($item['name']) ?></h3>
                        <a href="../public/product.php?id=<?= urlencode($item['product_id']) ?>&order_id=<?= urlencode($item['order_id']) ?>#reviews"
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
        <div class="pagination">
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
    <div class="page-header">
    <h2>My Reviews</h2>
    </div>
    <?php if (!empty($submittedReviews)): ?>
        <div  class="reviews-list">
            <?php foreach ($submittedReviews as $review): ?>
                <div class="review-card">
                        <a href="../public/product.php?id=<?= urlencode($review['product_id']) ?>">
                            <img src="../<?= htmlspecialchars($review['image_path'] ?? $review['image']) ?>" 
                                alt="<?= htmlspecialchars($review['name']) ?>">
                        </a>
                        <div>
                            <h3><?= htmlspecialchars($review['name']) ?></h3>
                            <p>⭐ <?= str_repeat("★", (int)$review['rating']) ?></p>
                            <p><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                            <small style="color:#888;">
                                <?= date("F j, Y", strtotime($review['created_at'])) ?>
                            </small>

                        <?php
                        echo '<a href="review-deleter.php?id=' . urlencode($review['review_id']) . '" ' .
                            'onclick="return confirm(\'Are you sure you want to delete this review?\');" ' .
                            'style="display:inline-block; margin-top:10px; padding:5px 10px; background:#dc3545; color:#fff; text-decoration:none; border-radius:4px;">' .
                            'Delete</a>';
                        ?>
                        </div>
                    </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p style="color:#666;">You haven't written any reviews yet.</p>
    <?php endif; ?>
</div>
</div>
