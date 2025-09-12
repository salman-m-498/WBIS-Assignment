<?php
session_start();
require_once '../includes/db.php';

// Check if order was successful
if (!isset($_SESSION['order_success'])) {
    header('Location: cart.php');
    exit;
}

$order_data = $_SESSION['order_success'];
unset($_SESSION['order_success']); // Clear the session data

// Get complete order details including voucher information
$stmt = $pdo->prepare("
    SELECT o.*, p.payment_id, p.payment_method, p.payment_status, p.transaction_id, p.payment_date, 
           u.username,
           v.code as voucher_code, v.description as voucher_description, v.discount_type, v.discount_value
    FROM orders o
    LEFT JOIN payments p ON o.order_id = p.order_id
    LEFT JOIN user u ON o.user_id = u.user_id
    LEFT JOIN vouchers v ON o.voucher_id = v.voucher_id
    WHERE o.order_id = ? AND o.user_id = ?
");
$stmt->execute([$order_data['order_id'], $order_data['user_id']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: cart.php');
    exit;
}

// Get order items
$stmt = $pdo->prepare("
    SELECT oi.*, p.name, p.image, p.sku
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.product_id 
    WHERE oi.order_id = ?
");
$stmt->execute([$order_data['order_id']]);
$order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Page variables
$page_title = "Order Confirmation";
$page_description = "Your order has been placed successfully";
$show_breadcrumb = true;
$breadcrumb_items = [
    ['url' => 'cart.php', 'title' => 'Cart'],
    ['url' => 'checkout.php', 'title' => 'Checkout'],
    ['url' => 'payment_success.php', 'title' => 'Confirmation']
];

include '../includes/header.php';
?>

<section class="payment-success-section">
    <div class="container">
        <div class="success-header">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h1>Order Placed Successfully!</h1>
            <p>Thank you for your purchase. Your order has been received and is being processed.</p>
        </div>

        <?php if ($order['voucher_code']): ?>
            <div class="voucher-success-notice">
                <i class="fas fa-ticket-alt"></i>
                <span>Voucher "<?= htmlspecialchars($order['voucher_code']) ?>" was applied successfully!</span>
            </div>
            <?php endif; ?>
        </div>

        <div class="order-confirmation">
            <div class="confirmation-details">
                <div class="order-info-card">
                    <h2>Order Information</h2>
                    <div class="info-grid">
                        <div class="info-item">
                            <label>Order Number:</label>
                            <span class="order-number"><?= htmlspecialchars($order['order_number']) ?></span>
                        </div>
                        <div class="info-item">
                            <label>Transaction ID:</label>
                            <span><?= htmlspecialchars($order['transaction_id'] ?? '-') ?></span>
                        </div>
                        <div class="info-item">
                            <label>Order Date:</label>
                            <span><?= date('M j, Y g:i A', strtotime($order['created_at'])) ?></span>
                        </div>
                        <div class="info-item">
                            <label>Payment Method:</label>
                            <span><?= ucfirst(str_replace('_', ' ', $order['payment_method'])) ?></span>
                        </div>
                        <div class="info-item">
                            <label>Payment Status:</label>
                            <span><?= ucfirst($order['payment_status'] ?? '-') ?></span>
                        </div>
                        <div class="info-item">
                            <label>Order Status:</label>
                            <span class="status-badge status-<?= $order['order_status'] ?>">
                                <?= ucfirst($order['order_status']) ?>
                            </span>
                        </div>
                         <?php if ($order['voucher_code']): ?>
                        <div class="info-item">
                            <label>Voucher Used:</label>
                            <span class="voucher-used">
                                <?= htmlspecialchars($order['voucher_code']) ?>
                                <?php
                                $discountText = $order['discount_type'] == 'percentage' 
                                    ? $order['discount_value'] . '% OFF' 
                                    : 'RM' . number_format($order['discount_value'], 2) . ' OFF';
                                ?>
                                <small>(<?= $discountText ?>)</small>
                            </span>
                        </div>
                        <?php endif; ?>
                        <div class="info-item">
                            <label>Total Amount:</label>
                            <span class="total-amount">RM<?= number_format($order['total_amount'], 2) ?></span>
                        </div>
                    </div>
                </div>

        <div class="shipping-info-card">
            <h2>Shipping Information</h2>
                <div class="address-block">
                    <p><strong><?= htmlspecialchars($order['shipping_first_name'] . ' ' . $order['shipping_last_name']) ?></strong></p>
                    <p><?= htmlspecialchars($order['shipping_address_line1']) ?></p>
                    <?php if (!empty($order['shipping_address_line2'])): ?>
                    <p><?= htmlspecialchars($order['shipping_address_line2']) ?></p>
                <?php endif; ?>
                    <p><?= htmlspecialchars($order['shipping_city'] . ', ' . $order['shipping_state'] . ' ' . $order['shipping_postal_code']) ?></p>
                <div class="contact-info">
                    <p><strong>Email:</strong> <?= htmlspecialchars($order['contact_email']) ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($order['contact_phone']) ?></p>
                </div>
        </div>

                <div class="order-items-card">
                    <h2>Order Items</h2>
                    <div class="items-list">
                        <?php foreach ($order_items as $item): ?>
                            <?php $image_path = str_replace("root/", "", $item['image']); ?>
                            <div class="order-item">
                                <div class="item-image">
                                    <img src="/<?= htmlspecialchars($image_path) ?>" 
                                         alt="<?= htmlspecialchars($item['name']) ?>">
                                </div>
                                <div class="item-details">
                                    <h3><?= htmlspecialchars($item['name']) ?></h3>
                                    <p class="item-sku">SKU: <?= htmlspecialchars($item['sku']) ?></p>
                                    <p class="item-quantity">Quantity: <?= $item['quantity'] ?></p>
                                </div>
                                <div class="item-pricing">
                                    <p class="unit-price">RM<?= number_format($item['unit_price'], 2) ?> each</p>
                                    <p class="total-price">RM<?= number_format($item['total_price'], 2) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="order-summary-card">
                    <h2>Order Summary</h2>
                    <div class="summary-totals">
                        <div class="total-row">
                            <span>Subtotal:</span>
                            <span>RM<?= number_format($order['subtotal'], 2) ?></span>
                        </div>
                        <div class="total-row">
                            <span>Shipping:</span>
                            <span><?= $order['shipping_cost'] == 0 ? 'FREE' : 'RM' . number_format($order['shipping_cost'], 2) ?></span>
                        </div>
                        <?php if ($order['discount_amount'] > 0): ?>
                        <div class="total-row discount">
                            <span>Discount:</span>
                            <span>-RM<?= number_format($order['discount_amount'], 2) ?></span>
                        </div>
                        <?php if ($order['voucher_description']): ?>
                        <div class="voucher-description">
                            <small><i class="fas fa-info-circle"></i> <?= htmlspecialchars($order['voucher_description']) ?></small>
                        </div>
                        <?php endif; ?>
                        <?php endif; ?>
                        <div class="total-row final-total">
                            <span>Total:</span>
                            <span>RM<?= number_format($order['total_amount'], 2) ?></span>
                        </div>
                    </div>
                    <?php if ($order['voucher_code']): ?>
                    <div class="savings-highlight">
                        <i class="fas fa-piggy-bank"></i>
                        <span>You saved RM<?= number_format($order['discount_amount'], 2) ?> with voucher <?= htmlspecialchars($order['voucher_code']) ?>!</span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="action-buttons">
                <a href="generate_receipt.php?order_id=<?= $order['order_id'] ?>" class="btn btn-primary" target="_blank">
                    <i class="fas fa-download"></i> Download Receipt (PDF)
                </a>
                <a href="../member/orders.php" class="btn btn-outline">
                    <i class="fas fa-list"></i> View Order History
                </a>
                 <a href="vouchers.php" class="btn btn-outline">
                    <i class="fas fa-ticket-alt"></i> Get More Vouchers
                </a>
                <a href="products.php" class="btn btn-outline">
                    <i class="fas fa-shopping-bag"></i> Continue Shopping
                </a>
            </div>

            <div class="next-steps">
                <h3>What's Next?</h3>
                <div class="steps-timeline">
                    <div class="timeline-step completed">
                        <div class="step-icon"><i class="fas fa-check"></i></div>
                        <div class="step-content">
                            <h4>Order Placed</h4>
                            <p>Your order has been received and payment confirmed</p>
                        </div>
                    </div>
                    <div class="timeline-step">
                        <div class="step-icon"><i class="fas fa-box"></i></div>
                        <div class="step-content">
                            <h4>Order Processing</h4>
                            <p>We're preparing your items for shipment (1-2 business days)</p>
                        </div>
                    </div>
                    <div class="timeline-step">
                        <div class="step-icon"><i class="fas fa-truck"></i></div>
                        <div class="step-content">
                            <h4>Shipped</h4>
                            <p>Your order will be shipped and tracking info will be provided</p>
                        </div>
                    </div>
                    <div class="timeline-step">
                        <div class="step-icon"><i class="fas fa-home"></i></div>
                        <div class="step-content">
                            <h4>Delivered</h4>
                            <p>Estimated delivery: 3-5 business days</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="support-info">
                <h3>Need Help?</h3>
                <p>If you have any questions about your order, please don't hesitate to contact us:</p>
                <div class="contact-methods">
                    <div class="contact-method">
                        <i class="fas fa-envelope"></i>
                        <span> support@toylandstore.com.my</span>
                    </div>
                    <div class="contact-method">
                        <i class="fas fa-phone"></i>
                        <span>+60 3-2123 4567</span>
                    </div>
                    <div class="contact-method">
                        <i class="fas fa-clock"></i>
                        <span>Mon-Fri: 9AM-6PM MYT</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add some celebration animation
    const successIcon = document.querySelector('.success-icon');
    if (successIcon) {
        successIcon.style.animation = 'bounce 1s ease-in-out';
    }
    
    // Auto-scroll to top
    window.scrollTo(0, 0);
    
    // Add fade-in animation for cards
    const cards = document.querySelectorAll('.confirmation-details > div');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 200);
    });
    // Add special animation for voucher success notice
    const voucherNotice = document.querySelector('.voucher-success-notice');
    if (voucherNotice) {
        voucherNotice.style.animation = 'slideInDown 0.8s ease-out';
    }

});

// Add bounce animation
const style = document.createElement('style');
style.textContent = `
    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% {
            transform: translateY(0);
        }
        40% {
            transform: translateY(-30px);
        }
        60% {
            transform: translateY(-15px);
        }
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
`;
document.head.appendChild(style);
</script>

<?php include '../includes/footer.php'; ?>