<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header('Location: admin_login.php');
    exit();
}

$reviewId = $_GET['id'] ?? null;
$page = $_GET['page'] ?? 1;
$sort = $_GET['sort'] ?? 'newest';
$status = $_GET['status'] ?? 'all';

if (!$reviewId) {
    die("No review selected.");
}

$sql = "SELECT r.review_id, r.user_id, r.product_id, r.rating, r.status, r.comment, r.created_at, p.image_path
        FROM reviews r
        LEFT JOIN product_images p ON r.product_id = p.product_id
        WHERE r.review_id = :review_id
        LIMIT 1";

$stm = $pdo->prepare($sql);
$stm->execute([':review_id' => $reviewId]);
$review = $stm->fetch(PDO::FETCH_ASSOC);

if (!$review) {
    die("Review not found.");
}

include '../includes/admin_header.php';
?>
<body class="review-list">
      <div class="container">
        <div id="flash-message" style="display:none; margin-bottom:15px; padding:10px; border-radius:6px;"></div>
    <div class="review-detail">
        <div class="review-box">
            <div class="review-meta">
                <?php if (!empty($review['image_path'])): ?>
                    <img src="../<?= htmlspecialchars($review['image_path']) ?>" 
                        alt="Product" 
                        class="product-img">
                <?php else: ?>
                    <div class="no-img">No Image</div>
                <?php endif; ?>

                <div class="review-info">
                    <p><strong>Review ID:</strong> <?= htmlspecialchars($review['review_id']) ?></p>
                    <p><strong>User ID:</strong> <?= htmlspecialchars($review['user_id']) ?></p>
                    <p><strong>Status:</strong> <?= htmlspecialchars($review['status']) ?></p>
                    <p><strong>Rating:</strong> <?= htmlspecialchars($review['rating']) ?> / 5</p>
                    <p><strong>Date:</strong> <?= htmlspecialchars($review['created_at']) ?></p>
                </div>
            </div>
            <hr> 
            <div class="review-comment">
                    <h3>Comment</h3>
                    <p><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
            </div>
            <div class="review-actions" style="margin-top:20px;">
                <?php if ($review['status'] === 'pending'): ?>
                    <a href="#" class="btn-approve" data-id="<?= $review['review_id'] ?>">Approve</a>
                    <a href="#" class="btn-reject" data-id="<?= $review['review_id'] ?>">Reject</a>
                <?php elseif ($review['status'] === 'approved'): ?>
                    <a href="#" class="btn-reject" data-id="<?= $review['review_id'] ?>">Reject</a>
                <?php elseif ($review['status'] === 'rejected'): ?>
                    <a href="#" class="btn-approve" data-id="<?= $review['review_id'] ?>">Approve</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const flash = document.getElementById("flash-message");

   document.querySelectorAll('.btn-approve, .btn-reject').forEach(btn => {
            btn.addEventListener('click', function(e){
                e.preventDefault(); 
                const id = this.dataset.id;
                const action = this.classList.contains('btn-approve') ? 'approve' : 'reject';

                fetch('review_action.php', {
                    method: 'POST',
                    headers: {'Content-Type':'application/x-www-form-urlencoded'},
                    body: `action=${action}&id=${id}`
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success){
                        flash.textContent = `Review ${data.newStatus}`;
                        flash.style.display = 'block';
                        flash.style.background = '#d4edda';
                        flash.style.color = '#155724';
                        flash.style.border = '1px solid #c3e6cb';
                        setTimeout(() => {
                            window.location.href = `reviews.php?page=<?= $page ?>&sort=<?= $sort ?>&status=<?= $status ?>`;
                        }, 1500);
                    } else {
                        flash.textContent = data.message || 'Failed to update review';
                        flash.style.display = 'block';
                        flash.style.background = '#f8d7da';
                        flash.style.color = '#721c24';
                        flash.style.border = '1px solid #f5c6cb';
                    }
                })
                .catch(err => console.error(err));
            });
        });
    });
    </script>
</body>

<?php include '../includes/admin_footer.php'; ?>