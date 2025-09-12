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
$order_id = $_GET['id'] ?? 0;

// Handle order cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'cancel_order') {
    try {
        // Get order info
        $stmt = $pdo->prepare("SELECT order_status, total_amount, payment_id FROM orders WHERE order_id = ? AND user_id = ?");
        $stmt->execute([$order_id, $user_id]);
        $current_order = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($current_order && $current_order['order_status'] === 'pending') {
            // Update order to cancel_requested
            $stmt = $pdo->prepare("UPDATE orders SET order_status = 'cancel_requested', updated_at = NOW() WHERE order_id = ? AND user_id = ?");
            $stmt->execute([$order_id, $user_id]);

            // Insert refund request
            $refund_id = generateNextId($pdo, 'refunds', 'refund_id', 'RF', 8);
            $stmt = $pdo->prepare("
                INSERT INTO refunds (refund_id, order_id, payment_id, refund_amount, refund_method, refund_status, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, 'requested', NOW(), NOW())
            ");
            $stmt->execute([
                $refund_id,
                $order_id,
                $current_order['payment_id'],
                $current_order['total_amount'],
                'original'
            ]);

            $_SESSION['success_message'] = "Your cancellation request has been submitted.";
            
            // Re-fetch order to show updated status
            $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
            $stmt->execute([$order_id, $user_id]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            // Fetch updated refund info
            $stmt = $pdo->prepare("SELECT * FROM refunds WHERE order_id = ?");
            $stmt->execute([$order_id]);
            $refund = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {

            $_SESSION['error_message'] = "Only pending orders can be cancelled.";
        }

        header('Location: order_details.php?id=' . $order_id);
        exit;
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error submitting cancellation request.";
        header('Location: order_details.php?id=' . $order_id);
        exit;
    }
}

// Handle marking order as delivered
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'mark_delivered') {
    try {
        $stmt = $pdo->prepare("SELECT order_status FROM orders WHERE order_id = ? AND user_id = ?");
        $stmt->execute([$order_id, $user_id]);
        $current_order = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($current_order && $current_order['order_status'] === 'shipped') {
            $stmt = $pdo->prepare("UPDATE orders SET order_status = 'delivered', updated_at = NOW() WHERE order_id = ? AND user_id = ?");
            $stmt->execute([$order_id, $user_id]);
            $_SESSION['success_message'] = "Order marked as delivered.";
        } else {
            $_SESSION['error_message'] = "Only shipped orders can be marked as delivered.";
        }

        header('Location: order_details.php?id=' . $order_id);
        exit;
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error updating order.";
        header('Location: order_details.php?id=' . $order_id);
        exit;
    }
}

$refund = null; // default value

// Fetch the latest refund info for this order
$stmt = $pdo->prepare("
    SELECT r.*, u.username AS admin_name
    FROM refunds r
    LEFT JOIN user u ON r.processed_by = u.user_id
    WHERE r.order_id = ?
    ORDER BY r.created_at DESC
    LIMIT 1
");
$stmt->execute([$order_id]);
$refund = $stmt->fetch(PDO::FETCH_ASSOC);

// Get order details
$stmt = $pdo->prepare("
     SELECT o.*, 
           u.username AS user_name, u.email AS user_email,
           pay.payment_method, pay.payment_status, pay.transaction_id,
           v.code AS voucher_code, v.description AS voucher_description, 
           v.discount_type, v.discount_value
    FROM orders o
    JOIN user u ON o.user_id = u.user_id
    LEFT JOIN payments pay ON o.payment_id = pay.payment_id
    LEFT JOIN vouchers v ON o.voucher_id = v.voucher_id
    WHERE o.order_id=? AND o.user_id=?
");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$order) {
    header('Location: orders.php');
    exit;
}

// Get order items
$stmt = $pdo->prepare("
    SELECT oi.*, p.name, p.image, p.sku
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.product_id 
    WHERE oi.order_id = ?
    ORDER BY oi.product_id
");
$stmt->execute([$order_id]);
$order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Page variables
$page_title = "Order Details - " . $order['order_number'];
$page_description = "View detailed information about your order";

include '../includes/header.php';
?>

<section class="order-detail-section">
    <div class="container">
        <div class="page-header">
            <div class="breadcrumb">
                <a href="orders.php">My Orders</a> / Order Details
            </div>
            <h1>Order #<?= htmlspecialchars($order['order_number']) ?></h1>
            <div class="order-meta">
                <span class="order-date">Placed on <?= date('M j, Y g:i A', strtotime($order['created_at'])) ?></span>
                <span class="status-badge status-<?= $order['order_status'] ?>">
                    <?= ucfirst($order['order_status']) ?>
                </span>
            </div>
        </div>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['success_message']) ?>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($_SESSION['error_message']) ?>
                <?php unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>

        <div class="order-content">
            <!-- Order Actions -->
            <div class="order-actions-card">
                <div class="actions">
                    <?php if ($order['order_status'] === 'pending'): ?>
                        <button class="btn btn-danger" onclick="confirmCancelOrder()">
                            <i class="fas fa-times"></i> Cancel Order
                        </button>
                    <?php endif; ?>
                    <a href="../public/generate_receipt.php?order_id=<?= $order['order_id'] ?>" 
                       target="_blank" 
                       class="btn btn-secondary">
                        <i class="fas fa-print"></i> Print Receipt
                    </a>

                    <?php if ($order['order_status'] === 'shipped'): ?>
                    <form method="POST" action="order_details.php?id=<?= $order['order_id'] ?>" style="display:inline;">
                        <input type="hidden" name="action" value="mark_delivered">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check"></i> Mark as Delivered
                        </button>
                    </form>
                    <?php endif; ?>
                    <a href="orders.php" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i> Back to Orders
                    </a>
                </div>
            </div>

            <!-- Order Status Timeline -->
            <div class="status-timeline-card">
                <h2>Order Status</h2>
                <div class="timeline">
                    <?php
                    $statuses = [
                        'pending' => ['icon' => 'fas fa-clock', 'title' => 'Order Pending', 'desc' => 'Order received and awaiting processing'],
                        'processing' => ['icon' => 'fas fa-cog', 'title' => 'Processing', 'desc' => 'Order is being prepared'],
                        'shipped' => ['icon' => 'fas fa-truck', 'title' => 'Shipped', 'desc' => 'Order has been shipped'],
                        'delivered' => ['icon' => 'fas fa-check-circle', 'title' => 'Delivered', 'desc' => 'Order has been delivered'],
                        'cancel_requested' => ['icon' => 'fas fa-hourglass-half', 'title' => 'Cancellation Requested', 'desc' => 'Awaiting admin approval'],
                        'cancelled' => ['icon' => 'fas fa-times-circle', 'title' => 'Cancelled', 'desc' => 'Order was cancelled and refunded']
                    ];
                    
                    $current_status = $order['order_status'];
                    $status_order = ['pending', 'processing', 'shipped', 'delivered'];
                    
                    foreach ($status_order as $status):
                        $is_active = $status === $current_status;
                        $is_completed = array_search($status, $status_order) < array_search($current_status, $status_order);
                        $is_cancelled = $current_status === 'cancelled';
                        
                        if ($is_cancelled && $status !== 'pending') continue;
                    ?>
                    <div class="timeline-item <?= $is_active ? 'active' : '' ?> <?= $is_completed ? 'completed' : '' ?>">
                        <div class="timeline-icon">
                            <i class="<?= $statuses[$status]['icon'] ?>"></i>
                        </div>
                        <div class="timeline-content">
                            <h4><?= $statuses[$status]['title'] ?></h4>
                            <p><?= $statuses[$status]['desc'] ?></p>
                            <?php if ($is_active && isset($order['updated_at'])): ?>
                                <small>Updated: <?= date('M j, Y g:i A', strtotime($order['updated_at'])) ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <?php if ($current_status === 'cancelled'): ?>
                    <div class="timeline-item active">
                        <div class="timeline-icon">
                            <i class="<?= $statuses['cancelled']['icon'] ?>"></i>
                        </div>
                        <div class="timeline-content">
                            <h4><?= $statuses['cancelled']['title'] ?></h4>
                            <p><?= $statuses['cancelled']['desc'] ?></p>
                            <?php if (isset($order['updated_at'])): ?>
                                <small>Cancelled: <?= date('M j, Y g:i A', strtotime($order['updated_at'])) ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Order Items -->
            <div class="order-items-card">
                <h2>Order Items</h2>
                <div class="items-table">
                    <div class="table-header">
                        <div class="col-product">Product</div>
                        <div class="col-price">Unit Price</div>
                        <div class="col-quantity">Quantity</div>
                        <div class="col-total">Total</div>
                    </div>
                    
                    <?php foreach ($order_items as $item): ?>
                        <?php $image_path = str_replace("root/", "", $item['image']); ?>
                        <div class="table-row">
                            <div class="col-product">
                                <div class="product-info">
                                    <div class="product-image">
                                        <img src="/<?= htmlspecialchars($image_path) ?>" 
                                             alt="<?= htmlspecialchars($item['name']) ?>">
                                    </div>
                                    <div class="product-details">
                                        <h4><?= htmlspecialchars($item['name']) ?></h4>
                                        <p class="sku">SKU: <?= htmlspecialchars($item['sku']) ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-price">
                                RM<?= number_format($item['unit_price'], 2) ?>
                            </div>
                            <div class="col-quantity">
                                <?= $item['quantity'] ?>
                            </div>
                            <div class="col-total">
                                <strong>RM<?= number_format($item['total_price'], 2) ?></strong>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="order-totals-card">
                <h2>Order Summary</h2>
                <div class="totals-table">
                    <div class="total-row">
                        <span>Subtotal:</span>
                        <span>RM<?= number_format($order['subtotal'], 2) ?></span>
                    </div>
                    <div class="total-row">
                        <span>Shipping:</span>
                        <span><?= $order['shipping_cost'] == 0 ? 'FREE' : 'RM' . number_format($order['shipping_cost'], 2) ?></span>
                    </div>
                    <?php if (!empty($order['voucher_code'])): ?>
                    <div class="total-row">
                        <span>Voucher:</span>
                        <span><?= htmlspecialchars($order['voucher_code']) ?>
                    </span>
                </div>
                <?php endif; ?>


                    <?php if ($order['discount_amount'] > 0): ?>
                    <div class="total-row">
                        <span>Discount:</span>
                        <span class="discount-text">-RM<?= number_format($order['discount_amount'], 2) ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="total-row final">
                        <span>Total:</span>
                        <span><strong>RM<?= number_format($order['total_amount'], 2) ?></strong></span>
                    </div>
                </div>
                <?php if (!empty($order['shipping_courier']) || !empty($order['tracking_number'])): ?>
                <div class="shipping-tracking">
                    <?php if (!empty($order['shipping_courier'])): ?>
                        <p><strong>Courier:</strong> <?= htmlspecialchars($order['shipping_courier']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($order['tracking_number'])): ?>
                        <p><strong>Tracking No:</strong> <?= htmlspecialchars($order['tracking_number']) ?></p>
                 <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
            </div>

            <!-- Shipping & Billing Info -->
            <div class="addresses-grid">
                <div class="address-card">
                    <h3>Shipping Address</h3>
                    <div class="address">
                        <p><strong><?= htmlspecialchars($order['shipping_first_name'] . ' ' . $order['shipping_last_name']) ?></strong></p>
                        <p><?= htmlspecialchars($order['shipping_address_line1']) ?></p>
                        <?php if (!empty($order['shipping_address_line2'])): ?>
                            <p><?= htmlspecialchars($order['shipping_address_line2']) ?></p>
                        <?php endif; ?>
                        <p><?= htmlspecialchars($order['shipping_city'] . ', ' . $order['shipping_state'] . ' ' . $order['shipping_postal_code']) ?></p>
                        <p><?php 
                            if ($order['shipping_area'] === 'West') { echo 'West Malaysia';
                            } else { echo 'East Malaysia';}?>
                        </p>
                    </div>
                    <div class="contact-info">
                        <p><i class="fas fa-envelope"></i> <?= htmlspecialchars($order['contact_email']) ?></p>
                        <p><i class="fas fa-phone"></i> <?= htmlspecialchars($order['contact_phone']) ?></p>
                    </div>
                </div>

                <div class="address-card">
                    <h3>Billing Address</h3>
                    <div class="address">
                        <p><strong><?= htmlspecialchars($order['billing_first_name'] . ' ' . $order['billing_last_name']) ?></strong></p>
                        <p><?= htmlspecialchars($order['billing_address_line1']) ?></p>
                        <?php if (!empty($order['billing_address_line2'])): ?>
                            <p><?= htmlspecialchars($order['billing_address_line2']) ?></p>
                        <?php endif; ?>
                        <p><?= htmlspecialchars($order['billing_city'] . ', ' . $order['billing_state'] . ' ' . $order['billing_postal_code']) ?></p>
                        <p><?php 
                            if ($order['billing_area'] === 'West') { echo 'West Malaysia';
                            } else { echo 'East Malaysia';}?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="payment-info-card">
                <h3>Payment Information</h3>
                <div class="payment-details">
                    <div class="payment-method">
                        <p><strong>Payment Method:</strong>  <?= htmlspecialchars($order['payment_method'] ?? 'N/A') ?></p>
                        <p><strong>Payment Status:</strong>  <?= htmlspecialchars($order['payment_status'] ?? 'pending') ?></p>
                         <p><strong>Transaction ID:</strong> <?= htmlspecialchars($order['transaction_id'] ?? '-') ?></p>
                    </div>
                </div>
            </div>

             <!-- Refund Info -->
    <?php if($refund): ?>
        <div class="order-totals-card refund-info-card">
            <h3>Refund Information</h3>
            <div class="refund-details">
            <p>Status:
                <span class="refund-status <?= $refund['refund_status'] ?>">
                    <?= ucfirst($refund['refund_status']) ?>
                </span>
            </p>
            <p>Amount: RM<?= number_format($refund['refund_amount'],2) ?></p>
            <p>Requested: <?= date('M j, Y g:i A', strtotime($refund['created_at'])) ?></p>
        <?php if ($refund['updated_at']): ?>
            <p><strong>Last Updated:</strong> <?= date('M j, Y g:i A', strtotime($refund['updated_at'])) ?></p>
        <?php endif; ?>
        <?php if (!empty($refund['admin_notes'])): ?>
            <p><strong>Admin Notes:</strong> <?= htmlspecialchars($refund['admin_notes']) ?></p>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

        </div> <!-- closes .order-content -->
    </div> <!-- closes .container -->
</section>
<?php if ($order['order_status'] === 'pending'): ?>
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
                <strong>#<?= htmlspecialchars($order['order_number']) ?></strong>?
            </p>
            <p class="warning-text">
                This action will submit a cancellation request that requires admin approval.
            </p>
        </div>
        <div class="modal-footer">
            <form method="POST" action="order_details.php?id=<?= $order['order_id'] ?>">
                <input type="hidden" name="action" value="cancel_order">
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
    const cancelBtn = document.querySelector('.btn.btn-danger'); // your Cancel Order button
    const keepOrderBtn = modal.querySelector('.keep-order-btn');

    cancelBtn.addEventListener('click', function(e) {
        e.preventDefault();
        showModal();
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
<?php endif; ?>

<?php include '../includes/footer.php'; ?>