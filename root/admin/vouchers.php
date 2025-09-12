<?php
session_start();
require_once '../includes/db.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header('Location: admin_login.php');
    exit();
}

// Handle status toggle
if (isset($_POST['toggle_status']) && isset($_POST['voucher_id'])) {
    $voucher_id = $_POST['voucher_id'];
    $action = $_POST['action'];
    
    if ($action == 'activate') {
        $stmt = $pdo->prepare("UPDATE vouchers SET status = 'active' WHERE voucher_id = ?");
    } elseif ($action == 'deactivate') {
        $stmt = $pdo->prepare("UPDATE vouchers SET status = 'inactive' WHERE voucher_id = ?");
    }
    
    if ($stmt->execute([$voucher_id])) {
        $_SESSION['success'] = "Voucher status updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating voucher status.";
    }
    
    header('Location: vouchers.php');
    exit();
}

// Handle delete
if (isset($_POST['delete_voucher']) && isset($_POST['voucher_id'])) {
    $voucher_id = $_POST['voucher_id'];
    
    $stmt = $pdo->prepare("DELETE FROM vouchers WHERE voucher_id = ?");
    if ($stmt->execute([$voucher_id])) {
        $_SESSION['success'] = "Voucher deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting voucher.";
    }
    
    header('Location: vouchers.php');
    exit();
}

// Fetch vouchers 
$sql = "SELECT v.*, 
        (SELECT COUNT(*) FROM user_vouchers WHERE voucher_id = v.voucher_id) as total_collected,
        (SELECT COUNT(*) FROM user_vouchers WHERE voucher_id = v.voucher_id AND used_at IS NOT NULL) as total_used
        FROM vouchers v
        ORDER BY v.created_at DESC";

$vouchers = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// Page variables
$page_title = "Manage Vouchers";
$page_description = "Manage discount vouchers and promotions";
$show_breadcrumb = true;
$breadcrumb_items = [
    ['url' => 'dashboard.php', 'title' => 'Dashboard'],
    ['url' => 'vouchers.php', 'title' => 'Vouchers']
];

// Include header
include '../includes/admin_header.php';
?>

<section class="admin-section vouchers-management">
    <div class="container">
        <div class="vouchers-admin-header">
            <h1><i class="fas fa-ticket-alt"></i> Manage Vouchers</h1>
            <div class="admin-actions">
                <a href="voucher_add.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Voucher
                </a>
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Description</th>
                        <th>Discount</th>
                        <th>Min Order</th>
                        <th>Valid Period</th>
                        <th>Usage</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($vouchers)): ?>
                        <tr>
                            <td colspan="8" class="text-center">No vouchers found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($vouchers as $voucher): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($voucher['code']); ?></strong>
                                </td>
                                <td><?php echo htmlspecialchars($voucher['description']); ?></td>
                                <td>
                                    <?php if ($voucher['discount_type'] == 'percentage'): ?>
                                        <?php echo $voucher['discount_value']; ?>%
                                    <?php else: ?>
                                        RM<?php echo number_format($voucher['discount_value'], 2); ?>
                                    <?php endif; ?>
                                </td>
                                <td>RM<?php echo number_format($voucher['min_order_amount'], 2); ?></td>
                                <td>
                                    <small>
                                        <?php echo date('M j, Y', strtotime($voucher['start_date'])); ?><br>
                                        to <?php echo date('M j, Y', strtotime($voucher['end_date'])); ?>
                                    </small>
                                </td>
                                <td>
                                    <small>
                                        Collected: <?php echo $voucher['total_collected']; ?><br>
                                        Used: <?php echo $voucher['total_used']; ?><br>
                                        <?php if ($voucher['usage_limit'] > 0): ?>
                                            Limit: <?php echo $voucher['usage_limit']; ?>
                                        <?php else: ?>
                                            Unlimited
                                        <?php endif; ?>
                                    </small>
                                </td>
                                <td>
                                    <span class="status-badge <?php echo $voucher['status']; ?>">
                                        <?php echo ucfirst($voucher['status']); ?>
                                    </span>
                                </td>
                                <td class="actions">
                                    <a href="voucher_edit.php?id=<?php echo $voucher['voucher_id']; ?>" 
                                       class="btn btn-sm btn-edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <?php if (in_array($voucher['status'], ['scheduled', 'expired', 'inactive'])): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="voucher_id" value="<?php echo $voucher['voucher_id']; ?>">
                                            <input type="hidden" name="action" value="activate">
                                            <button type="submit" name="toggle_status" class="btn btn-sm btn-success">Activate</button>
                                        </form>
                                    <?php endif; ?>
                                    
                                    <?php if ($voucher['status'] == 'active'): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="voucher_id" value="<?php echo $voucher['voucher_id']; ?>">
                                            <input type="hidden" name="action" value="deactivate">
                                            <button type="submit" name="toggle_status" class="btn btn-sm btn-warning">Deactivate</button>
                                        </form>
                                    <?php endif; ?>

                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this voucher?')">
                                        <input type="hidden" name="voucher_id" value="<?php echo $voucher['voucher_id']; ?>">
                                        <button type="submit" name="delete_voucher" class="btn btn-sm btn-delete" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
    </div>
</section>

<?php include '../includes/admin_footer.php'; ?>
