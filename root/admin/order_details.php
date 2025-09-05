<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php'; 

// Check if user is admin
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header('Location: admin_login.php');
    exit();
}

$order_id = $_GET['id'] ?? null;
if (!$order_id) {
    header('Location: orders.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: orders.php');
    exit;
}


// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
         if ($_POST['action'] === 'update_status') {
            $new_status = $_POST['new_status'];
            $valid_statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
            if (!in_array($new_status, $valid_statuses)) {
                throw new Exception("Invalid status");
            }

            $stmt = $pdo->prepare("
                UPDATE orders 
                SET order_status = ?, updated_at = NOW() 
                WHERE order_id = ?
            ");
            $stmt->execute([$new_status, $order_id]);

            $_SESSION['success_message'] = "Order status updated successfully";
        }

        
            if ($_POST['action'] === 'update_tracking') {
            $tracking_number = $_POST['tracking_number'] ?? '';
            $shipping_courier = $_POST['shipping_courier'] ?? '';

             $stmt = $pdo->prepare("
                UPDATE orders 
                SET tracking_number = ?, shipping_courier = ?, updated_at = NOW() 
                WHERE order_id = ?
            ");
            $stmt->execute([$tracking_number, $shipping_courier, $order_id]);

            $_SESSION['success_message'] = "Tracking info updated successfully";
        }

    if ($_POST['action'] === 'process_refund') {
    try {
        // Only allow if order is cancel_requested
        if ($order['order_status'] !== 'cancel_requested') {
            throw new Exception("Refund can only be processed for cancel requested orders.");
        }

        $refund_method = $_POST['refund_method'] ?? 'original';
        $notes = $_POST['notes'] ?? '';
        $refund_amount = $order['total_amount'];

         $stmt = $pdo->prepare("SELECT * FROM refunds WHERE order_id = ?");
        $stmt->execute([$order_id]);
        $refund = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($refund) {
            // Update existing refund to completed
            $stmt = $pdo->prepare("
                UPDATE refunds 
                SET refund_status = 'completed',
                    refund_method = ?,
                    processed_by = ?,
                    notes = ?,
                    updated_at = NOW()
                WHERE refund_id = ?
            ");
            $stmt->execute([
                $refund_method,
                $_SESSION['admin_id'],
                $notes,
                $refund['refund_id']
            ]);
        } else {
            // Create a new refund record (if none exists)
            $refund_id = generateNextId($pdo, 'refunds', 'refund_id', 'RF', 8);
            $stmt = $pdo->prepare("
                INSERT INTO refunds (refund_id, order_id, payment_id, refund_amount, refund_method, refund_status, processed_by, notes, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, 'completed', ?, ?, NOW(), NOW())
            ");
            $stmt->execute([
                $refund_id,
                $order_id,
                $order['payment_id'],
                $refund_amount,
                $refund_method,
                $_SESSION['admin_id'],
                $notes
            ]);
        }
        
        // Update order status → cancelled
        $stmt = $pdo->prepare("UPDATE orders SET order_status = 'cancelled', updated_at = NOW() WHERE order_id = ?");
        $stmt->execute([$order_id]);

        $_SESSION['success_message'] = "Refund of RM" . number_format($refund_amount, 2) . " processed successfully.";
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Refund failed: " . $e->getMessage();
    }

    header("Location: order_details.php?id=$order_id");
    exit;
    }
        
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
    }
    
    header("Location: order_details.php?id=$order_id");
    exit;
}

// Fetch order details
try {
    $stmt = $pdo->prepare("
    SELECT o.*, 
           u.username, u.email, u.role,
           up.first_name, up.last_name, up.phone,
           pay.payment_method, pay.payment_status, pay.transaction_id
    FROM orders o
    JOIN user u ON o.user_id = u.user_id
    LEFT JOIN user_profiles up ON u.user_id = up.user_id
    LEFT JOIN payments pay ON o.payment_id = pay.payment_id
    WHERE o.order_id = ?
");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        header('Location: orders.php');
        exit;
    }
    
    // Fetch order items
     $stmt = $pdo->prepare("
        SELECT oi.*, p.name as product_name, p.sku, p.image
        FROM order_items oi
        JOIN products p ON oi.product_id = p.product_id
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$order_id]);
    $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $_SESSION['error_message'] = "Error fetching order details: " . $e->getMessage();
    header('Location: orders.php');
    exit;
}

$refunds = [];
try {
    $stmt = $pdo->prepare("
        SELECT r.*, u.username AS processed_by_name
        FROM refunds r
        LEFT JOIN user u ON r.processed_by = u.user_id
        WHERE r.order_id = ?
        ORDER BY r.refund_date DESC
    ");
    $stmt->execute([$order_id]);
    $refunds = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $refunds = [];
}

$page_title = "Order #" . $order['order_number'];
$page_description = "Order details and management";

include '../includes/header.php';
?>

<!-- Flash Messages -->
<?php if (isset($_SESSION['success_message'])): ?>
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i>
    <?= htmlspecialchars($_SESSION['success_message']) ?>
    <?php unset($_SESSION['success_message']); ?>
</div>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
<div class="alert alert-error">
    <i class="fas fa-exclamation-circle"></i>
    <?= htmlspecialchars($_SESSION['error_message']) ?>
    <?php unset($_SESSION['error_message']); ?>
</div>
<?php endif; ?>

<section class="order-details-section">
    <div class="container">
        <div class="page-header">
            <div class="header-left">
                <a href="orders.php" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Back to Orders
                </a>
                <h1>Order #<?= htmlspecialchars($order['order_number']) ?></h1>
            </div>
            <div class="header-actions">
                <a href="../admin/print_order.php?id=<?= urlencode($order['order_id']) ?>" 
                    target="_blank" 
                    class="btn btn-outline">
                    <i class="fas fa-file-invoice"></i> Print Invoice
                </a>
            </div>
        </div>

        <div class="order-details-grid">
            <!-- Order Overview -->
            <div class="detail-card order-overview">
                <div class="card-header">
                    <h3><i class="fas fa-info-circle"></i> Order Overview</h3>
                    <span class="status-badge status-<?= $order['order_status'] ?>">
                        <?= ucfirst($order['order_status']) ?>
                    </span>
                </div>
                <div class="card-content">
                    <div class="overview-grid">
                        <div class="overview-item">
                            <label>Order Date:</label>
                            <span><?= date('F j, Y g:i A', strtotime($order['created_at'])) ?></span>
                        </div>
                        <div class="overview-item">
                            <label>Total Amount:</label>
                            <span class="amount">RM<?= number_format($order['total_amount'], 2) ?></span>
                        </div>
                        <div class="overview-item">
                            <label>Payment Method:</label>
                            <span><?= ucfirst(str_replace('_', ' ', $order['payment_method'])) ?></span>
                        </div>
                        <div class="overview-item">
                            <label>Payment Status:</label>
                            <span class="payment-status status-<?= $order['payment_status'] ?>">
                                <?= ucfirst($order['payment_status']) ?>
                            </span>
                        </div>
                        <?php if (!empty($order['transaction_id'])): ?>
                        <div class="overview-item">
                            <label>Transaction ID:</label>
                            <span><?= htmlspecialchars($order['transaction_id']) ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($order['tracking_number'])): ?>
                        <div class="overview-item">
                            <label>Tracking Number:</label>
                            <span class="tracking-number"><?= htmlspecialchars($order['tracking_number']) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="detail-card customer-info">
                <div class="card-header">
                    <h3><i class="fas fa-user"></i> Customer Information</h3>
                </div>
                <div class="card-content">
                    <div class="customer-details">
                        <div class="customer-item">
                            <label>Name:</label>
                            <span><?= htmlspecialchars($order['shipping_first_name'] . ' ' . $order['shipping_last_name']) ?></span>
                        </div>
                        <div class="customer-item">
                            <label>Email:</label>
                            <span><a href="mailto:<?= htmlspecialchars($order['contact_email']) ?>"><?= htmlspecialchars($order['contact_email']) ?></a></span>
                        </div>
                        <?php if (!empty($order['contact_phone'])): ?>
                        <div class="customer-item">
                            <label>Phone:</label>
                            <span><a href="tel:<?= htmlspecialchars($order['contact_phone']) ?>"><?= htmlspecialchars($order['contact_phone']) ?></a></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Shipping Address -->
            <?php if (!empty($order['address_line_1'])): ?>
            <div class="detail-card shipping-address">
                <div class="card-header">
                    <h3><i class="fas fa-map-marker-alt"></i> Shipping Address</h3>
                </div>
                <div class="card-content">
                    <div class="address">
                        <p><?= htmlspecialchars($order['address_line_1']) ?></p>
                        <?php if (!empty($order['address_line_2'])): ?>
                        <p><?= htmlspecialchars($order['address_line_2']) ?></p>
                        <?php endif; ?>
                        <p><?= htmlspecialchars($order['city']) ?>, <?= htmlspecialchars($order['state']) ?> <?= htmlspecialchars($order['postal_code']) ?></p>
                        <p><?php 
                            if ($order['shipping_area'] === 'West') { echo 'West Malaysia';
                            } else { echo 'East Malaysia';}?>
                        </p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Order Items -->
            <div class="detail-card order-items full-width">
                <div class="card-header">
                    <h3><i class="fas fa-shopping-cart"></i> Order Items</h3>
                </div>
                <div class="card-content">
                    <div class="items-table-container">
                        <table class="items-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order_items as $item): ?>
                                <tr>
                                    <td>
                                        <div class="product-info">
                                            <?php if (!empty($item['image_url'])): ?>
                                            <img src="<?= htmlspecialchars($item['image_url']) ?>" 
                                                 alt="<?= htmlspecialchars($item['product_name']) ?>" 
                                                 class="product-image">
                                            <?php endif; ?>
                                            <div class="product-details">
                                                <span class="product-name"><?= htmlspecialchars($item['product_name']) ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="sku"><?= htmlspecialchars($item['sku']) ?></td>
                                    <td class="quantity"><?= $item['quantity'] ?></td>
                                    <td class="unit-price">RM<?= number_format($item['unit_price'], 2) ?></td>
                                    <td class="total-price">RM<?= number_format($item['quantity'] * $item['unit_price'], 2) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="total-row">
                                    <td colspan="4"><strong>Total:</strong></td>
                                    <td><strong>RM<?= number_format($order['subtotal'], 2) ?></strong></td>
                                </tr>
                                <tr class="total-row">
                                    <td colspan="4"><strong>Shipping:</strong></td>
                                    <td><strong>RM<?= number_format($order['shipping_cost'], 2) ?></strong></td>
                                </tr>
                                <tr class="total-row">
                                    <td colspan="4"><strong>Discount:</strong></td>
                                    <td><strong>-RM<?= number_format($order['discount_amount'], 2) ?></strong></td>
                                </tr>
                                <tr class="total-row">
                                    <td colspan="4"><strong>Total:</strong></td>
                                    <td><strong>RM<?= number_format($order['total_amount'], 2) ?></strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Status Management -->
            <div class="detail-card status-management">
                <div class="card-header">
                <h3><i class="fas fa-edit"></i> Update Status</h3>
            </div>
            <div class="card-content">
                <form method="POST" class="status-form">
                    <input type="hidden" name="action" value="update_status">
            
            <div class="form-group">
                <label for="new_status">Order Status:</label>
                <select id="new_status" name="new_status" required>
                    <option value="pending" <?= $order['order_status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="processing" <?= $order['order_status'] === 'processing' ? 'selected' : '' ?>>Processing</option>
                    <option value="shipped" <?= $order['order_status'] === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                    <option value="delivered" <?= $order['order_status'] === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                    <option value="cancel_requested" <?= $status_filter === 'cancel_requested' ? 'selected' : '' ?>>Cancel Requested</option>
                    <option value="cancelled" <?= $order['order_status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Status
            </button>
        </form>
    </div>
</div>

    <!-- Refund Management -->
<?php if ($order['order_status'] === 'cancel_requested'): ?>
<div class="detail-card refund-management">
    <div class="card-header">
        <h3><i class="fas fa-undo"></i> Process Refund</h3>
    </div>
    <div class="card-content">
        <!-- Refund Action Button -->
        <button type="button" class="btn btn-danger" onclick="toggleRefundForm()">
            <i class="fas fa-undo-alt"></i> Process Refund
        </button>

        <!-- Hidden Refund Form -->
        <form method="POST" class="refund-form" id="refund-form" style="display:none; margin-top:15px;">
            <input type="hidden" name="action" value="process_refund">

            <div class="form-group">
                <label for="refund_method">Refund Method:</label>
                <select id="refund_method" name="refund_method" required>
                    <option value="original">Original Payment Method</option>
                    <option value="manual">Manual Transfer</option>
                </select>
            </div>

            <div class="form-group">
                <label for="notes">Notes (optional):</label>
                <textarea id="notes" name="notes" rows="3" placeholder="Enter any remarks..."></textarea>
            </div>

            <button type="submit" class="btn btn-danger">
                <i class="fas fa-check"></i> Confirm Refund
            </button>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Refund History -->
<?php if (!empty($refunds)): ?>
<div class="detail-card refund-history full-width">
    <div class="card-header">
        <h3><i class="fas fa-history"></i> Refund History</h3>
    </div>
    <div class="card-content">
        <table class="items-table">
            <thead>
                <tr>
                    <th>Refund ID</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Status</th>
                    <th>Processed By</th>
                    <th>Date</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($refunds as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['refund_id']) ?></td>
                    <td>RM<?= number_format($r['refund_amount'], 2) ?></td>
                    <td><?= ucfirst($r['refund_method']) ?></td>
                    <td>
                        <span class="status-badge status-<?= $r['refund_status'] ?>">
                            <?= ucfirst($r['refund_status']) ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($r['processed_by_name'] ?? 'System') ?></td>
                    <td><?= date('M j, Y g:i A', strtotime($r['refund_date'])) ?></td>
                    <td><?= nl2br(htmlspecialchars($r['notes'] ?? '')) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

            <!-- Tracking Information -->
            <div class="detail-card tracking-info">
                 <div class="card-header">
                     <h3><i class="fas fa-truck"></i> Tracking Information</h3>
            </div>
            <div class="card-content">
                <form method="POST" class="tracking-form">
                    <input type="hidden" name="action" value="update_tracking">
            
            <div class="form-group">
                <label for="tracking_number">Tracking Number:</label>
                <input type="text" id="tracking_number" name="tracking_number" 
                       value="<?= htmlspecialchars($order['tracking_number'] ?? '') ?>"
                       placeholder="Enter tracking number...">
            </div>
            
            <div class="form-group">
                <label for="shipping_courier">Shipping Courier:</label>
                <select id="shipping_courier" name="shipping_courier">
                    <option value="">Select Courier</option>
                    <option value="pos_laju" <?= ($order['shipping_courier'] ?? '') === 'pos_laju' ? 'selected' : '' ?>>Pos Laju</option>
                    <option value="gdex" <?= ($order['shipping_courier'] ?? '') === 'gdex' ? 'selected' : '' ?>>GDEX</option>
                    <option value="dhl" <?= ($order['shipping_courier'] ?? '') === 'dhl' ? 'selected' : '' ?>>DHL</option>
                    <option value="fedex" <?= ($order['shipping_courier'] ?? '') === 'fedex' ? 'selected' : '' ?>>FedEx</option>
                    <option value="citylink" <?= ($order['shipping_courier'] ?? '') === 'citylink' ? 'selected' : '' ?>>City-Link</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-outline">
                <i class="fas fa-save"></i> Update Tracking
            </button>
        </form>
    </div>
</div>    
        </div>
    </div>
</section>

<script>
function printOrder() {
    // Create a print-friendly version of the order
    const printWindow = window.open('', '_blank');
    const orderContent = document.querySelector('.order-details-grid').cloneNode(true);
    
    // Remove interactive elements for printing
    const forms = orderContent.querySelectorAll('form');
    forms.forEach(form => form.remove());
    
    const buttons = orderContent.querySelectorAll('button');
    buttons.forEach(button => button.remove());
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Order #<?= htmlspecialchars($order['order_number']) ?></title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .detail-card { margin-bottom: 20px; border: 1px solid #ddd; }
                .card-header { background: #f5f5f5; padding: 10px; font-weight: bold; }
                .card-content { padding: 15px; }
                .items-table { width: 100%; border-collapse: collapse; }
                .items-table th, .items-table td { border: 1px solid #ddd; padding: 8px; }
                .status-badge { background: #f0f0f0; padding: 5px 10px; border-radius: 5px; }
                @media print { .no-print { display: none; } }
            </style>
        </head>
        <body>
            <h1>Order #<?= htmlspecialchars($order['order_number']) ?></h1>
            ${orderContent.innerHTML}
        </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.print();
}

// Auto-refresh page every 5 minutes to keep data current
setInterval(function() {
    if (document.hidden === false) {
        // Only refresh if page is visible
        location.reload();
    }
}, 300000); // 5 minutes

// Handle tracking number updates
document.addEventListener('DOMContentLoaded', function() {
    const trackingForm = document.querySelector('.tracking-form');
    if (trackingForm) {
        trackingForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const trackingNumber = formData.get('tracking_number');
            const shippingCourier = formData.get('shipping_courier');
            
            if (!trackingNumber.trim()) {
                alert('Please enter a tracking number');
                return;
            }
            
            // Add the form data and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.style.display = 'none';
            
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'update_tracking';
            
            const trackingInput = document.createElement('input');
            trackingInput.type = 'hidden';
            trackingInput.name = 'tracking_number';
            trackingInput.value = trackingNumber;
            
            const courierInput = document.createElement('input');
            courierInput.type = 'hidden';
            courierInput.name = 'shipping_courier';
            courierInput.value = shippingCourier;
            
            form.appendChild(actionInput);
            form.appendChild(trackingInput);
            form.appendChild(courierInput);
            document.body.appendChild(form);
            form.submit();
        });
    }
    
    // Status change confirmation
    const statusForm = document.querySelector('.status-form');
    if (statusForm) {
        statusForm.addEventListener('submit', function(e) {
            const newStatus = document.getElementById('new_status').value;
            const currentStatus = '<?= $order['order_status'] ?>';
            
            if (newStatus === 'cancelled' && currentStatus !== 'cancelled') {
                if (!confirm('Are you sure you want to cancel this order? This action should be carefully considered.')) {
                    e.preventDefault();
                    return;
                }
            }
            
            if (newStatus === 'delivered' && currentStatus !== 'delivered') {
                if (!confirm('Mark this order as delivered? This will notify the customer.')) {
                    e.preventDefault();
                    return;
                }
            }
        });
    }
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey || e.metaKey) {
        switch(e.key) {
            case 'p':
                e.preventDefault();
                printOrder();
                break;
            case 'e':
                e.preventDefault();
                break;
        }
    }
});

function toggleRefundForm() {
    const form = document.getElementById('refund-form');
    form.style.display = (form.style.display === 'none') ? 'block' : 'none';
}

</script>

<?php include '../includes/footer.php'; ?>