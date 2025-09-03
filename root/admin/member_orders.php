<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header('Location: admin_login.php');
    exit();
}

if (!isset($_GET['id'])) die("User ID missing.");

$userId = $_GET['id'];
$search = $_GET['search'] ?? '';
$sort   = $_GET['sort'] ?? 'newest';

$where = "WHERE user_id = :userId";
$params = [':userId' => $userId];
$statusFilter = $_GET['status'] ?? '';

// Add search filter
if (!empty($search)) {
    $where .= " AND (order_id LIKE :search OR order_status LIKE :search OR order_number LIKE :search)";
    $params[':search'] = "%$search%";
}

if (!empty($statusFilter)) {
    $where .= " AND order_status = :statusFilter";
    $params[':statusFilter'] = $statusFilter;
}

// Sorting
switch ($sort) {
    case 'oldest':
        $orderBy = "created_at ASC";
        break;
    case 'amount_high':
        $orderBy = "total_amount DESC";
        break;
    case 'amount_low':
        $orderBy = "total_amount ASC";
        break;
    default:
        $orderBy = "created_at DESC"; 
}

// Final query
$sql = "SELECT * FROM orders $where ORDER BY $orderBy";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalOrders = count($orders);

$statusCounts = [
    'pending'    => 0,
    'processing' => 0,
    'shipped'    => 0,
    'delivered'  => 0,
    'cancelled'  => 0
];

foreach ($orders as $o) {
    if (isset($statusCounts[$o['order_status']])) {
        $statusCounts[$o['order_status']]++;
    }
}

include '../includes/header.php';
?>

<div class="container" style="margin-top:30px;">
    <h2>Order History (User ID: <?= htmlspecialchars($userId) ?>)</h2>
    <a href="members.php" style="text-decoration:none; color:#007bff;">← Back to Members</a>

    <form method="get" class="filter-form" style="margin:20px 0;">
    <input type="hidden" name="id" value="<?= htmlspecialchars($userId) ?>">

    <!-- Search box -->
    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search orders...">

    <!-- Status filter -->
    <select name="status" onchange="this.form.submit()">
        <option value="">All Status</option>
        <option value="pending" <?= ($_GET['status'] ?? '')=='pending'?'selected':'' ?>>Pending</option>
        <option value="processing" <?= ($_GET['status'] ?? '')=='processing'?'selected':'' ?>>Processing</option>
        <option value="shipped" <?= ($_GET['status'] ?? '')=='shipped'?'selected':'' ?>>Shipped</option>
        <option value="delivered" <?= ($_GET['status'] ?? '')=='delivered'?'selected':'' ?>>Delivered</option>
        <option value="cancelled" <?= ($_GET['status'] ?? '')=='cancelled'?'selected':'' ?>>Cancelled</option>
    </select>

    <!-- Sorting -->
    <select name="sort" onchange="this.form.submit()">
        <option value="newest" <?= $sort=='newest'?'selected':'' ?>>Newest First</option>
        <option value="oldest" <?= $sort=='oldest'?'selected':'' ?>>Oldest First</option>
        <option value="amount_high" <?= $sort=='amount_high'?'selected':'' ?>>Amount High → Low</option>
        <option value="amount_low" <?= $sort=='amount_low'?'selected':'' ?>>Amount Low → High</option>
    </select>

    <button type="submit" class="btn btn-primary">Filter</button>
</form>
    
    <div class="order-summary">
    <strong>Total Orders:</strong> <?= $totalOrders ?> 
    <?php foreach ($statusCounts as $status => $count): ?>
        | <span class="status-label status-<?= $status ?>">
            <?= ucfirst($status) ?>: <?= $count ?>
        </span>
    <?php endforeach; ?>
    </div>

    <table class="orders-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Status</th>
                <th>Total(RM)</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $o): ?>
                <tr>
                    <td><?= htmlspecialchars($o['order_id']) ?></td>
                    <td><?= htmlspecialchars($o['order_status']) ?></td>
                    <td><?= htmlspecialchars($o['total_amount']) ?></td>
                    <td><?= htmlspecialchars($o['created_at']) ?></td>
                </tr>
            <?php endforeach; ?>

            <?php if (empty($orders)): ?>
                <tr><td colspan="4" class="no-orders">No orders found</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
