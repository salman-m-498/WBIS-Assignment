<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php'; 

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../public/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle order cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancel_order') {
    try {
        $order_id = $_POST['order_id'];

        $stmt = $pdo->prepare("SELECT order_status, total_amount, payment_id FROM orders WHERE order_id = ? AND user_id = ?");
        $stmt->execute([$order_id, $user_id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) throw new Exception("Order not found.");
        if ($order['order_status'] !== 'pending') {
            throw new Exception("Only processing orders can be cancelled.");
        }
        
        $pdo->prepare("UPDATE orders SET order_status = 'cancel_requested', updated_at = NOW() WHERE order_id = ? AND user_id = ?")
            ->execute([$order_id, $user_id]);

        $refund_id = generateNextId($pdo, 'refunds', 'refund_id', 'RF', 8);
        $stmt = $pdo->prepare("
            INSERT INTO refunds (refund_id, order_id, payment_id, refund_amount, refund_method, refund_status, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, 'requested', NOW(), NOW())
        ");
        $stmt->execute([
            $refund_id,
            $order_id,
            $order['payment_id'],
            $order['total_amount'],
            'original'
        ]);

        $_SESSION['success_message'] = "Your cancellation request has been submitted. Awaiting admin approval.";
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
    }

    header('Location: orders.php');
    exit;
}

// Handle marking order as delivered
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'mark_delivered') {
    try {
        $order_id = $_POST['order_id'];

        $stmt = $pdo->prepare("SELECT order_status FROM orders WHERE order_id = ? AND user_id = ?");
        $stmt->execute([$order_id, $user_id]);
        $order = $stmt->fetch();

        if (!$order) throw new Exception("Order not found.");
        if ($order['order_status'] !== 'shipped') {
            throw new Exception("Only shipped orders can be marked as delivered.");
        }

        $stmt = $pdo->prepare("UPDATE orders SET order_status = 'delivered', updated_at = NOW() WHERE order_id = ? AND user_id = ?");
        $stmt->execute([$order_id, $user_id]);

        $_SESSION['success_message'] = "Order marked as delivered successfully.";
        
        header("Location: orders.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
        header('Location: orders.php');
    exit;
    }
}

// Pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

$stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
$stmt->execute([$user_id]);
$total_orders = $stmt->fetchColumn();
$total_pages = ceil($total_orders / $per_page);

$stmt = $pdo->prepare("
    SELECT o.*, 
           COUNT(oi.product_id) AS item_count,
           GROUP_CONCAT(p.name SEPARATOR ', ') AS product_names,
           pay.payment_method,
           pay.payment_status,
           pay.transaction_id
    FROM orders o
    LEFT JOIN order_items oi ON o.order_id = oi.order_id
    LEFT JOIN products p ON oi.product_id = p.product_id
    LEFT JOIN payments pay ON o.payment_id = pay.payment_id
    WHERE o.user_id = :user_id
    GROUP BY o.order_id
    ORDER BY o.created_at DESC
    LIMIT :per_page OFFSET :offset
");

$stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
$stmt->bindValue(':per_page', (int)$per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = "My Orders";
$page_description = "View and manage your order history";

include '../includes/header.php';
?>

<!-- Flash Messages -->
<?php if (isset($_SESSION['success_message'])): ?>
<div class="flash-message success">
    <?= htmlspecialchars($_SESSION['success_message']) ?>
</div>
<?php unset($_SESSION['success_message']); endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
<div class="flash-message error">
    <?= htmlspecialchars($_SESSION['error_message']) ?>
</div>
<?php unset($_SESSION['error_message']); endif; ?>

<section class="member-orders-section">
    <div class="container">
        <div class="page-header">
            <h1>My Orders</h1>
            <p>Track and manage your order history</p>
        </div>

        <?php if (empty($orders)): ?>
        <div class="no-orders">
            <div class="no-orders-content">
                <h2>No Orders Yet</h2>
                <p>You haven't placed any orders yet.</p>
                <a href="../public/products.php">Start Shopping</a>
            </div>
        </div>
        <?php else: ?>
        
        <div class="orders-list">
        <?php foreach ($orders as $order): ?>
            <div class="order-card">
                <div class="order-header">
                    <div class="order-info">
                        <h3>Order #<?= htmlspecialchars($order['order_number']) ?></h3>
                        <p class="order-date">Placed on <?= date('M j, Y', strtotime($order['created_at'])) ?></p>
                    </div>
                    <span class="status-badge status-<?= htmlspecialchars($order['order_status']) ?>">
                        <?= ucfirst(str_replace('_', ' ', $order['order_status'])) ?>
                    </span>
                </div>
                <div class="order-details">
                    <div class="order-summary">
                        <div class="summary-item">
                            <span class="label">Items</span>
                            <span class="value"><?= (int)$order['item_count'] ?></span>
                        </div>
                        <div class="summary-item">
                            <span class="label">Total</span>
                            <span class="amount">RM<?= number_format($order['total_amount'], 2) ?></span>
                        </div>
                    </div>
                    <div class="order-products">
                        <p class="products-preview"><?= htmlspecialchars($order['product_names']) ?></p>
                    </div>
                </div>
                <div class="order-actions">
                    <a href="order_details.php?id=<?= $order['order_id'] ?>">
                         <i class="fas fa-eye"></i> View Details
                    </a>
                    <?php if ($order['order_status'] === 'pending'): ?>
                    <button type="button" 
                            class="cancel-order-btn" 
                            data-order-id="<?= $order['order_id'] ?>"
                            data-order-number="<?= htmlspecialchars($order['order_number']) ?>">
                        Cancel Order
                    </button>
                    <?php endif; ?>

                    <?php if ($order['order_status'] === 'shipped'): ?>
                    <form method="POST" action="orders.php">
                        <input type="hidden" name="action" value="mark_delivered">
                        <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                        <button type="submit">Mark as Delivered</button>
                    </form>
                    <?php elseif ($order['order_status'] === 'delivered'): ?>
                        <a href="reviews.php?order_id=<?= $order['order_id'] ?>" class="review-btn">Leave a Review</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Cancel Order Modal -->
<div id="cancel-modal">
    <div class="modal-overlay"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3>Cancel Order</h3>
            <button type="button" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to cancel order 
                <strong>#<span id="cancel-order-number"></span></strong>?
            </p>
            <p class="warning-text">
                This action will submit a cancellation request that requires admin approval.
            </p>
        </div>
        <div class="modal-footer">
            <form method="POST" id="cancel-form" action="orders.php">
                <input type="hidden" name="action" value="cancel_order">
                <input type="hidden" name="order_id" id="cancel-order-id">
                <button type="button" class="keep-order-btn">Keep Order</button>
                <button type="submit" class="btn-danger">Submit Cancellation</button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('cancel-modal');
    const modalOverlay = modal.querySelector('.modal-overlay');
    const modalCloseBtn = modal.querySelector('.modal-close');
    const cancelButtons = document.querySelectorAll('.cancel-order-btn');
    const keepOrderBtn = modal.querySelector('.keep-order-btn');

    cancelButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('cancel-order-id').value = this.dataset.orderId;
            document.getElementById('cancel-order-number').textContent = this.dataset.orderNumber;
            showModal();
        });
    });

    modalOverlay.addEventListener('click', hideModal);
    modalCloseBtn.addEventListener('click', hideModal);
    keepOrderBtn.addEventListener('click', hideModal);

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && modal.style.display === 'flex') hideModal();
    });

    function showModal() {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    function hideModal() {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
});
</script>

<?php include '../includes/footer.php'; ?>
