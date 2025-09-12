<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header('Location: admin_login.php');
    exit();
}

header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

$search = $_GET['search'] ?? '';
$sort   = $_GET['sort'] ?? 'newest';
$status = $_GET['status'] ?? 'all';
$where  = "WHERE 1=1";
$params = [];

// Pagination
$limit = 8;
$page  = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;


if ($status !== 'all') {
    $where .= " AND r.status = :status";
    $params[':status'] = $status;
}

if (!empty($search)) {
    $where .= " AND (
        CAST(r.review_id AS CHAR) LIKE :search
        OR UPPER(r.user_id) LIKE :search
        OR UPPER(r.product_id) LIKE :search
        OR r.comment LIKE :search
    )";
    $params[':search'] = "%" . strtoupper($search) . "%";
}

// Sort
switch ($sort) {
    case 'oldest':      $orderBy = "r.created_at ASC"; break;
    case 'rating_high': $orderBy = "r.rating DESC"; break;
    case 'rating_low':  $orderBy = "r.rating ASC"; break;
    default:            $orderBy = "r.created_at DESC"; break;
}

// Review query
$sql = "SELECT r.review_id, r.user_id, r.product_id,r.order_id, r.rating, r.comment, r.created_at, r.status, p.image_path
        FROM reviews r
        LEFT JOIN product_images p ON r.product_id = p.product_id
        $where
        GROUP BY r.review_id
        ORDER BY $orderBy
        LIMIT :limit OFFSET :offset";

$stm = $pdo->prepare($sql);

foreach ($params as $k => $v) {
    $stm->bindValue($k, $v, PDO::PARAM_STR);
}
$stm->bindValue(':limit', $limit, PDO::PARAM_INT);
$stm->bindValue(':offset', $offset, PDO::PARAM_INT);

$stm->execute();
$reviews = $stm->fetchAll(PDO::FETCH_ASSOC);

// Count query
$countSql = "SELECT COUNT(DISTINCT r.review_id)
             FROM reviews r
             LEFT JOIN product_images p ON r.product_id = p.product_id
             $where";
$countStm = $pdo->prepare($countSql);
$countStm->execute($params);
$totalReviews = (int) $countStm->fetchColumn();
$totalPages   = ceil($totalReviews / $limit); 

include '../includes/admin_header.php';
?>

<div class="container">
     <div id="flash-message" style="display:none; margin-bottom:10px; padding:10px; border-radius:5px;"></div>
    <div class="filters">
        <form method="get" action="reviews.php" class="filter-form">
            <form method="get" action="members.php" class="filter-form">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search by Review ID, User ID, Product ID...">

                <select name="sort" onchange="this.form.submit()">
                    <option value="newest" <?= $sort=='newest'?'selected':'' ?>>Newest First</option>
                    <option value="oldest" <?= $sort=='oldest'?'selected':'' ?>>Oldest First</option>
                    <option value="rating_high" <?= $sort=='rating_high'?'selected':'' ?>>Rating High → Low</option>
                    <option value="rating_low" <?= $sort=='rating_low'?'selected':'' ?>>Rating Low → High</option>
                </select>

                <select name="status" onchange="this.form.submit()">
                    <option value="all" <?= ($status ?? 'all')=='all'?'selected':'' ?>>All Reviews</option>
                    <option value="pending" <?= ($status ?? '')=='pending'?'selected':'' ?>>Pending</option>
                    <option value="approved" <?= ($status ?? '')=='approved'?'selected':'' ?>>Approved</option>
                    <option value="rejected" <?= ($status ?? '')=='rejected'?'selected':'' ?>>Rejected</option>
                </select>
                <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>
</div>

<div class="review-list">
    <div class="container">
        <div><small>Note: Click on row to get detail information</small></div>
        <small class="review-count">Reviews found: <?= $totalReviews ?>.</small>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Product Image</th>
                    <th>Product ID</th>
                    <th>Order ID </th>
                    <th>User ID</th>
                    <th>Rating</th>
                    <th>Date Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reviews as $r): ?>
                    <tr id="review-<?= $r['review_id'] ?>">
                        <td>
                            <?php if (!empty($r['image_path'])): ?>
                            <img src="../<?= htmlspecialchars($r['image_path']) ?>" 
                                    alt="Product" 
                                    style="width:60px; height:60px; object-fit:cover; border-radius:5px; border:1px solid #ddd;">
                            <?php else: ?>
                                <span>No image</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($r['product_id']) ?></td>
                        <td><?= htmlspecialchars($r['order_id']) ?></td>
                        <td><?= htmlspecialchars($r['user_id']) ?></td>
                        <td><?= htmlspecialchars($r['rating']) ?></td>
                        <td><?= htmlspecialchars($r['created_at']) ?></td>
                        <td class="actions">
                            <?php if ($r['status'] === 'pending'): ?>
                                <button class="btn-approve" data-id="<?= $r['review_id'] ?>">Approve</button>
                                <button class="btn-reject" data-id="<?= $r['review_id'] ?>">Reject</button>
                            <?php elseif ($r['status'] === 'approved'): ?>
                                <button class="btn-reject" data-id="<?= $r['review_id'] ?>">Reject</button>
                            <?php elseif ($r['status'] === 'rejected'): ?>
                                <button class="btn-approve" data-id="<?= $r['review_id'] ?>">Approve</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($reviews)): ?>
                    <tr>
                    <td colspan="6" class="no-records">No reviews found.</td>
                    </tr>
                <?php endif; ?>
                
                <script>
                // Ajax for buttons
                document.addEventListener('DOMContentLoaded', () => {
                    const tbody = document.querySelector('table.admin-table tbody');
                    const flash = document.getElementById('flash-message'); 
                    
                    tbody.addEventListener('click', e => {
                        const btn = e.target.closest('.btn-approve, .btn-reject');
                        const tr = e.target.closest('tr');
                        if (!tr) return;

                        if (btn) {
                            // If button clicked, do Ajaxx update
                            e.stopPropagation();
                            const reviewId = btn.dataset.id;
                            const action = btn.classList.contains('btn-approve') ? 'approve' : 'reject';

                            fetch('review_action.php', {
                                method: 'POST',
                                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                                body: `id=${reviewId}&action=${action}`
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    // Update only the button
                                    if (action === 'approve') {
                                        tr.querySelector('.actions').innerHTML = `<button class="btn-reject" data-id="${reviewId}">Reject</button>`;
                                    } else {
                                        tr.querySelector('.actions').innerHTML = `<button class="btn-approve" data-id="${reviewId}">Approve</button>`;
                                    }
                                   flash.textContent = `Review ${data.newStatus}`;
        flash.style.display = 'block';
        flash.style.background = '#d4edda';
        flash.style.color = '#155724';
        flash.style.border = '1px solid #c3e6cb';
        setTimeout(() => { flash.style.display = 'none'; }, 3000);

                                    setTimeout(() => { flash.style.display = 'none'; }, 3000);
                                } else {
                                    flash.textContent = data.message || 'Failed to update review';
                                    flash.style.display = 'block';
                                    flash.style.background = '#f8d7da';
                                    flash.style.color = '#721c24';
                                    flash.style.border = '1px solid #f5c6cb';

                                    setTimeout(() => { flash.style.display = 'none'; }, 4000);
                                }
                            })
                            .catch(err => console.error(err));

                        } else {
                            // Row clicked, redirect to review_detail.php preserving page, sort, status
                            const reviewId = tr.id.replace('review-', '');
                            const page = '<?= $page ?>';
                            const sort = '<?= $sort ?>';
                            const status = '<?= $status ?>';
                            window.location = `review_detail.php?id=${reviewId}&page=${page}&sort=${sort}&status=${status}`;
                        }
                    });
                });
                </script>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div style="text-align:center; margin-top:20px;">
        <?php
        $query = $_GET;

        if ($page > 1) {
            $query['page'] = $page - 1;
            echo '<a href="?' . http_build_query($query) . '" style="display:inline-block; margin:0 5px;">&laquo; Prev</a>';
        }

        for ($i = 1; $i <= $totalPages; $i++) {
            $query['page'] = $i;
            $active = $i == $page ? 'font-weight:bold; text-decoration:underline;' : '';
            echo '<a href="?' . http_build_query($query) . '" style="display:inline-block; margin:0 5px; ' . $active . '">' . $i . '</a>';
        }

        if ($page < $totalPages) {
            $query['page'] = $page + 1;
            echo '<a href="?' . http_build_query($query) . '" style="display:inline-block; margin:0 5px;">Next &raquo;</a>';
        }
        ?>
    </div>
    </div>


<div style="height:100px;"></div>
<?php include '../includes/admin_footer.php'; ?>