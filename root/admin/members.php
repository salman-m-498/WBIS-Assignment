<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header('Location: admin_login.php');
    exit();
}

header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'newest';
$toggleRole = $_GET['toggleRole'] ?? '';

// Pagination 
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$where = '';
$params = [];

// Filter
if ($toggleRole == '1') {
    if ($search) {
        $where = "WHERE role = 'admin' AND (username LIKE :search OR email LIKE :search OR user_id LIKE :search)";
        $params[':search'] = "%$search%";
    } else {
        $where = "WHERE role = 'admin'";
    }
} else {
    if ($search) {
        $where = "WHERE username LIKE :search OR email LIKE :search OR user_id LIKE :search";
        $params[':search'] = "%$search%";
    }
}

switch ($sort) {
    case 'oldest': $orderBy = "created_at ASC"; break;
    case 'username_asc': $orderBy = "username ASC"; break;
    case 'username_desc': $orderBy = "username DESC"; break;
    default: $orderBy = "created_at DESC"; break;
}

$sql = "SELECT * FROM user $where ORDER BY $orderBy LIMIT :limit OFFSET :offset";
$stm = $pdo->prepare($sql);

foreach ($params as $key => $val) {
    $stm->bindValue($key, $val, PDO::PARAM_STR);
}

$stm->bindValue(':limit', $limit, PDO::PARAM_INT);
$stm->bindValue(':offset', $offset, PDO::PARAM_INT);

$stm->execute();
$users = $stm->fetchAll(PDO::FETCH_ASSOC);

$countSql = "SELECT COUNT(*) FROM user $where";
$countStm = $pdo->prepare($countSql);
$countStm->execute($params);
$totalUsers = $countStm->fetchColumn();
$totalPages = ceil($totalUsers / $limit);

include '../includes/admin_header.php';
?>


<div class="container">
    <div class="filters">
        <form method="get" action="members.php" class="filter-form">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search users...">
            
            <select name="sort" onchange="this.form.submit()">
                <option value="newest" <?= $sort=='newest'?'selected':'' ?>>Newest First</option>
                <option value="oldest" <?= $sort=='oldest'?'selected':'' ?>>Oldest First</option>
                <option value="username_asc" <?= $sort=='username_asc'?'selected':'' ?>>A - Z</option>
                <option value="username_desc" <?= $sort=='username_desc'?'selected':'' ?>>Z - A</option>
            </select>

            <label class="checkbox-inline">
                <input type="checkbox" name="toggleRole" value="1" <?= ($toggleRole == '1') ? 'checked' : '' ?> onchange="this.form.submit()">
                Admin only
            </label>

            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>
</div>

<div class="member-list">
    <div class="container">
        <small class="user-count">Users found: <?= $totalUsers ?>.</small>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Date Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= htmlspecialchars($u['user_id']) ?></td>
                        <td><?= htmlspecialchars($u['username']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><?= htmlspecialchars($u['role']) ?></td>
                        <td><?= htmlspecialchars($u['created_at']) ?></td>
                        <td class="actions">
                            <a href="member_profile.php?id=<?= urlencode($u['user_id']) ?>" class="btn btn-info">Profile</a>
                            <a href="member_edit.php?id=<?= urlencode($u['user_id']) ?>" class="btn btn-warning">Edit</a>
                            <a href="member_orders.php?id=<?= urlencode($u['user_id']) ?>" class="btn btn-secondary">Orders</a>

                            <?php if ($u['status'] === 'blocked'): ?>
                                <a href="member_action.php?action=unblock&id=<?= urlencode($u['user_id']) ?>" 
                                   class="btn btn-success"
                                   onclick="return confirm('Unblock this user?')">Unblock</a>
                            <?php else: ?>
                                <a href="member_action.php?action=block&id=<?= urlencode($u['user_id']) ?>" 
                                   class="btn btn-danger"
                                   onclick="return confirm('Block this user?')">Block</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="6" class="no-users">No users found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?search=<?= urlencode($search) ?>&sort=<?= $sort ?>&toggleRole=<?= $toggleRole ?>&page=<?= $page - 1 ?>">&laquo; Prev</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?search=<?= urlencode($search) ?>&sort=<?= $sort ?>&toggleRole=<?= $toggleRole ?>&page=<?= $i ?>" 
               class="<?= $i==$page?'active':'' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?search=<?= urlencode($search) ?>&sort=<?= $sort ?>&toggleRole=<?= $toggleRole ?>&page=<?= $page + 1 ?>">Next &raquo;</a>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/admin_footer.php'; ?>