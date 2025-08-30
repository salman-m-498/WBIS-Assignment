<?php

// ryan_lim member email and role always missing for unknown reason

session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
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
    case 'oldest':
        $orderBy = "created_at ASC";
        break;
    case 'username_asc':
        $orderBy = "username ASC";
        break;
    case 'username_desc':
        $orderBy = "username DESC";
        break;
    default:
        $orderBy = "created_at DESC";
        break;
}

// Final query with LIMIT & OFFSET
$sql = "SELECT * FROM user $where ORDER BY $orderBy LIMIT :limit OFFSET :offset";
$stm = $pdo->prepare($sql);

// Bind search 
foreach ($params as $key => $val) {
    $stm->bindValue($key, $val, PDO::PARAM_STR);
}

// Bind pagination
$stm->bindValue(':limit', $limit, PDO::PARAM_INT);
$stm->bindValue(':offset', $offset, PDO::PARAM_INT);

$stm->execute();
$users = $stm->fetchAll(PDO::FETCH_ASSOC);

// Count total rows for pagination
$countSql = "SELECT COUNT(*) FROM user $where";
$countStm = $pdo->prepare($countSql);
$countStm->execute($params);
$totalUsers = $countStm->fetchColumn();
$totalPages = ceil($totalUsers / $limit);

include '../includes/header.php';
?>

<div class="container">
    <div class="filters" style="margin-bottom:20px; display:flex; gap:10px;">
        <form method="get" action="members.php" style="flex:1; display:flex; gap:10px; align-items:center;">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                placeholder="Search" style="flex:1; padding:5px;">
            
            <select name="sort" onchange="this.form.submit()">
                <option value="newest" <?= $sort=='newest'?'selected':'' ?>>Newest First</option>
                <option value="oldest" <?= $sort=='oldest'?'selected':'' ?>>Oldest First</option>
                <option value="username_asc" <?= $sort=='username_asc'?'selected':'' ?>>A - Z</option>
                <option value="username_desc" <?= $sort=='username_desc'?'selected':'' ?>>Z - A</option>
            </select>

            <label style="margin-left:10px;">
                <input type="checkbox" name="toggleRole" value="1" <?= ($toggleRole == '1') ? 'checked' : '' ?> onchange="this.form.submit()">
                Admin only
            </label>

            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>
</div>

<body class="member list">
    <div class="container">
        <small style="solid #000000ff;">Users selected: <?= $totalUsers ?>.</small>
        <table class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th style="border:1px solid black; width:120px; height:40px; text-align:center;">User ID</th>
                    <th style="border:1px solid black; width:120px; height:40px; text-align:center;">Username</th>
                    <th style="border:1px solid black; width:120px; height:40px; text-align:center;">Email</th>
                    <th style="border:1px solid black; width:120px; height:40px; text-align:center;">Role</th>
                    <th style="border:1px solid black; width:120px; height:40px; text-align:center;">Date Created</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr onclick="window.location='member_profile.php?id=<?= urlencode($u['user_id']) ?>'" style="cursor:pointer;">
                        <td style="border:1px solid black; width:120px; height:40px; text-align:center;"><?= htmlspecialchars($u['user_id']) ?></td>
                        <td style="border:1px solid black; width:350px; height:40px; text-align:center;"><?= htmlspecialchars($u['username']) ?></td>
                        <td style="border:1px solid black; width:350px; height:40px; text-align:center;"><?= htmlspecialchars($u['email']) ?></td>
                        <td style="border:1px solid black; width:100px; height:40px; text-align:center;"><?= htmlspecialchars($u['role']) ?></td>
                        <td style="border:1px solid black; width:200px; height:40px; text-align:center;"><?= htmlspecialchars($u['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="5" style="border:1px solid black; padding:8px; text-align:center;">No user exist.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div style="margin-top:20px; text-align:center;">
        <?php if ($page > 1): ?>
            <a href="?search=<?= urlencode($search) ?>&sort=<?= $sort ?>&toggleRole=<?= $toggleRole ?>&page=<?= $page - 1 ?>">&laquo; Prev</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?search=<?= urlencode($search) ?>&sort=<?= $sort ?>&toggleRole=<?= $toggleRole ?>&page=<?= $i ?>" 
               style="margin:0 5px; <?= $i==$page?'font-weight:bold;':'' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?search=<?= urlencode($search) ?>&sort=<?= $sort ?>&toggleRole=<?= $toggleRole ?>&page=<?= $page + 1 ?>">Next &raquo;</a>
        <?php endif; ?>
    </div>
</body>

<div style="height:100px;"></div>







