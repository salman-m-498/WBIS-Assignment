<?php
session_start();
require_once '../includes/db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=checkout.php');
    exit;
}

$user_id = $_SESSION['user_id'];

if (!empty($_POST['selected_items'])) {
    $_SESSION['selected_items'] = $_POST['selected_items'];
    // Store selected voucher if provided
    if (isset($_POST['selected_voucher_id'])) {
        $_SESSION['selected_voucher_id'] = $_POST['selected_voucher_id'];
    }
} elseif (!isset($_SESSION['selected_items']) || empty($_SESSION['selected_items'])) {
    // No items selected in POST or session, redirect back to cart
    header('Location: cart.php?error=no_items_selected');
    exit;
}

// Use the session version for further processing
$selected_items = $_SESSION['selected_items'];
$selected_voucher_id = $_SESSION['selected_voucher_id'] ?? '';

$placeholders = implode(',', array_fill(0, count($selected_items), '?'));

$stmt = $pdo->prepare("
    SELECT c.*, p.name, p.sale_price, p.price, p.image, p.stock_quantity, p.sku
    FROM cart c
    JOIN products p ON c.product_id = p.product_id
    WHERE c.user_id = ? AND c.product_id IN ($placeholders)
");

$stmt->execute(array_merge([$user_id], $selected_items));
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Redirect if cart is empty
if (empty($cart_items)) {
    header('Location: cart.php');
    exit;
}

// Calculate totals
$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['sale_price'] * $item['quantity'];
}
if ($subtotal >= 150) {
    $shipping = 0; // free shipping over RM150
} else {
    if (!empty($_POST['shipping_area']) && $_POST['shipping_area'] === 'east') {
        $shipping = 12.00; // Shipping fee East Malaysia
    } else {
        $shipping = 8.00; // Shipping fee West Malaysia
    }
}

// Initialize discount variables
$discount_amount = 0;
$voucher_id = null;
$voucher_error = '';
$voucher = null;

// Process voucher if selected
if (!empty($selected_voucher_id)) {
    // Validate voucher
    $voucherStmt = $pdo->prepare("
        SELECT v.*, uv.user_voucher_id, uv.used_at,
               (SELECT COUNT(*) FROM user_vouchers WHERE voucher_id = v.voucher_id AND used_at IS NOT NULL) as total_used,
               (SELECT COUNT(*) FROM user_vouchers WHERE voucher_id = v.voucher_id AND user_id = ? AND used_at IS NOT NULL) as user_used_count
        FROM vouchers v
        JOIN user_vouchers uv ON v.voucher_id = uv.voucher_id
        WHERE v.voucher_id = ? AND uv.user_id = ? AND uv.used_at IS NULL
        AND v.status = 'active' AND v.start_date <= NOW() AND v.end_date >= NOW()
    ");
    $voucherStmt->execute([$user_id, $selected_voucher_id, $user_id]);
    $voucher = $voucherStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($voucher) {
        // Check all voucher conditions
        $valid = true;
        
        // Check if order amount meets minimum
        if ($subtotal < $voucher['min_order_amount']) {
            $voucher_error = "Order amount must be at least RM" . number_format($voucher['min_order_amount'], 2);
            $valid = false;
        }
        
        // Check per-user limit
        if ($voucher['user_used_count'] >= $voucher['per_user_limit']) {
            $voucher_error = "You have exceeded the usage limit for this voucher";
            $valid = false;
        }
        
        // Check global usage limit
        if ($voucher['usage_limit'] > 0 && $voucher['total_used'] >= $voucher['usage_limit']) {
            $voucher_error = "This voucher has reached its usage limit";
            $valid = false;
        }
        
        if ($valid) {
            // Calculate discount
            if ($voucher['discount_type'] === 'percentage') {
                $discount_amount = ($subtotal * $voucher['discount_value']) / 100;
            } else {
                $discount_amount = $voucher['discount_value'];
            }
            
            // Ensure discount doesn't exceed subtotal
            $discount_amount = min($discount_amount, $subtotal);
            $voucher_id = $voucher['voucher_id'];
        }
    } else {
        $voucher_error = "Selected voucher is not valid or has already been used";
    }
}

$total = $subtotal + $shipping - $discount_amount;

// Get user's available vouchers for display
$availableVouchersStmt = $pdo->prepare("
    SELECT v.*, uv.user_voucher_id, uv.collected_at,
           (SELECT COUNT(*) FROM user_vouchers WHERE voucher_id = v.voucher_id AND user_id = ? AND used_at IS NOT NULL) as user_used_count
    FROM vouchers v
    JOIN user_vouchers uv ON v.voucher_id = uv.voucher_id
    WHERE uv.user_id = ? 
    AND uv.used_at IS NULL 
    AND v.status = 'active'
    AND v.start_date <= NOW() 
    AND v.end_date >= NOW()
    ORDER BY v.discount_value DESC
");
$availableVouchersStmt->execute([$user_id, $user_id]);
$available_vouchers = $availableVouchersStmt->fetchAll(PDO::FETCH_ASSOC);

// Filter out vouchers that have reached per-user limit
$available_vouchers = array_filter($available_vouchers, function($v) {
    return $v['user_used_count'] < $v['per_user_limit'];
});

// Get user info
$stmt = $pdo->prepare("SELECT * FROM user WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Page variables
$page_title = "Checkout";
$page_description = "Complete your order";
$show_breadcrumb = true;
$breadcrumb_items = [
    ['url' => 'cart.php', 'title' => 'Cart'],
    ['url' => 'checkout.php', 'title' => 'Checkout']
];

include '../includes/header.php';
?>

<section class="checkout-section">
    <div class="container">
        <div class="checkout-header">
            <h1>Checkout</h1>
            <div class="checkout-steps">
                <div class="step active">
                    <span class="step-number">1</span>
                    <span class="step-title">Information</span>
                </div>
                <div class="step">
                    <span class="step-number">2</span>
                    <span class="step-title">Payment</span>
                </div>
                <div class="step">
                    <span class="step-number">3</span>
                    <span class="step-title">Confirmation</span>
                </div>
            </div>
        </div>

        <form id="checkout-form" action="redirect_payment.php" method="POST">
            <?php foreach ($selected_items as $product_id): ?>
                <input type="hidden" name="selected_items[]" value="<?= htmlspecialchars($product_id) ?>">
            <?php endforeach; ?>

            <!-- Pass voucher information -->
            <input type="hidden" name="voucher_id" value="<?= htmlspecialchars($voucher_id ?? '') ?>">
            <input type="hidden" name="discount_amount" value="<?= htmlspecialchars($discount_amount) ?>">

            <div class="checkout-layout">
                <!-- Checkout Form -->
                <div class="checkout-form">
                    <!-- Voucher Selection Section -->
                    <div class="form-section">
                        <h2>Apply Voucher</h2>
                        <?php if (empty($available_vouchers)): ?>
                            <div class="no-vouchers">
                                <p style="color: #666; font-size: 14px; margin: 10px 0;">
                                    No vouchers available. 
                                    <a href="vouchers.php" style="color: #007bff;">Collect vouchers here!</a>
                                </p>
                            </div>
                        <?php else: ?>
                            <div class="voucher-selection">
                                <div class="voucher-option">
                                    <label>
                                        <input type="radio" name="selected_voucher" value="" 
                                               <?= empty($selected_voucher_id) ? 'checked' : '' ?>
                                               onchange="updateVoucherSelection()">
                                        <span class="voucher-details">
                                            <span class="voucher-title">No voucher</span>
                                            <span class="voucher-desc">Continue without discount</span>
                                        </span>
                                    </label>
                                </div>
                                
                                <?php foreach ($available_vouchers as $voucher_option): ?>
                                    <?php
                                    $discountText = $voucher_option['discount_type'] == 'percentage' 
                                        ? $voucher_option['discount_value'] . '% OFF' 
                                        : 'RM' . number_format($voucher_option['discount_value'], 2) . ' OFF';
                                    
                                    $isEligible = $subtotal >= $voucher_option['min_order_amount'];
                                    $isSelected = $voucher_option['voucher_id'] == $selected_voucher_id;
                                    ?>
                                    <div class="voucher-option <?= !$isEligible ? 'disabled' : '' ?>">
                                        <label>
                                            <input type="radio" name="selected_voucher" value="<?= $voucher_option['voucher_id'] ?>" 
                                                   <?= $isSelected ? 'checked' : '' ?>
                                                   <?= !$isEligible ? 'disabled' : '' ?>
                                                   data-discount-type="<?= $voucher_option['discount_type'] ?>"
                                                   data-discount-value="<?= $voucher_option['discount_value'] ?>"
                                                   data-min-order="<?= $voucher_option['min_order_amount'] ?>"
                                                   onchange="updateVoucherSelection()">
                                            <span class="voucher-details">
                                                <span class="voucher-title">
                                                    <?= htmlspecialchars($voucher_option['code']) ?> - <?= $discountText ?>
                                                </span>
                                                <span class="voucher-desc">
                                                    <?= htmlspecialchars($voucher_option['description']) ?>
                                                    <br>
                                                    <small style="color: <?= $isEligible ? '#28a745' : '#dc3545' ?>;">
                                                        Min order: RM<?= number_format($voucher_option['min_order_amount'], 2) ?>
                                                        <?= !$isEligible ? ' (Not eligible)' : '' ?>
                                                    </small>
                                                </span>
                                            </span>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <div style="margin-top: 15px;">
                                <button type="button" id="apply-voucher-btn" class="btn btn-outline">
                                    Update Voucher
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                    <!-- Contact Information -->
                    <div class="form-section">
                        <h2>Contact Information</h2>
                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" name="email" 
                                   value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number *</label>
                            <input type="tel" id="phone" name="phone" 
                                   value="<?= htmlspecialchars($user['phone'] ?? '') ?>" required>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="form-section">
                        <h2>Shipping Address</h2>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="first_name">First Name *</label>
                                <input type="text" id="first_name" name="first_name" 
                                       value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" required>
                                <small class="error-message" id="first-name-error"></small>
                            </div>
                            <div class="form-group">
                                <label for="last_name">Last Name *</label>
                                <input type="text" id="last_name" name="last_name" 
                                       value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" required>
                                <small class="error-message" id="last-name-error"></small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="address_line1">Address Line 1 *</label>
                            <input type="text" id="address_line1" name="address_line1" 
                                   value="<?= htmlspecialchars($user['address_line1'] ?? '') ?>" 
                                   placeholder="Street address" required>
                        </div>
                        <div class="form-group">
                            <label for="address_line2">Address Line 2</label>
                            <input type="text" id="address_line2" name="address_line2" 
                                   value="<?= htmlspecialchars($user['address_line2'] ?? '') ?>" 
                                   placeholder="Apartment, suite, etc. (optional)">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="city">City *</label>
                                <input type="text" id="city" name="city" 
                                       value="<?= htmlspecialchars($user['city'] ?? '') ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="state">State/Province *</label>
                                <input type="text" id="state" name="state" 
                                       value="<?= htmlspecialchars($user['state'] ?? '') ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="postal_code">Postal Code *</label>
                                <input type="text" id="postal_code" name="postal_code" 
                                       value="<?= htmlspecialchars($user['postal_code'] ?? '') ?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="shipping_area">Shipping Area *</label>
                                <select id="shipping_area" name="shipping_area" required>
                                <option value="">Select Shipping Area</option>
                                <option value="west" <?= ($user['shipping_area'] ?? '') === 'west' ? 'selected' : '' ?>>West Malaysia </option>
                                <option value="east" <?= ($user['shipping_area'] ?? '') === 'east' ? 'selected' : '' ?>>East Malaysia (Sabah & Sarawak)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Billing Address -->
                    <div class="form-section">
                        <h2>Billing Address</h2>
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" id="same_as_shipping" name="same_as_shipping" checked>
                                <span class="checkmark"></span>
                                Same as shipping address
                            </label>
                        </div>
                        
                        <div id="billing-fields" style="display: none;">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="billing_first_name">First Name *</label>
                                    <input type="text" id="billing_first_name" name="billing_first_name">
                                </div>
                                <div class="form-group">
                                    <label for="billing_last_name">Last Name *</label>
                                    <input type="text" id="billing_last_name" name="billing_last_name">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="billing_address_line1">Address Line 1 *</label>
                                <input type="text" id="billing_address_line1" name="billing_address_line1">
                            </div>
                            <div class="form-group">
                                <label for="billing_address_line2">Address Line 2</label>
                                <input type="text" id="billing_address_line2" name="billing_address_line2">
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="billing_city">City *</label>
                                    <input type="text" id="billing_city" name="billing_city">
                                </div>
                                <div class="form-group">
                                    <label for="billing_state">State/Province *</label>
                                    <input type="text" id="billing_state" name="billing_state">
                                </div>
                                <div class="form-group">
                                    <label for="billing_postal_code">Postal Code *</label>
                                    <input type="text" id="billing_postal_code" name="billing_postal_code">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="form-group">
                                    <label for="billing_area">Billing Area *</label>
                                    <select id="billing_area" name="billing_area" required>
                                        <option value="">Select Shipping Area</option>
                                        <option value="west" <?= ($user['billing_area'] ?? '') === 'west' ? 'selected' : '' ?>>West Malaysia </option>
                                        <option value="east" <?= ($user['billing_area'] ?? '') === 'east' ? 'selected' : '' ?>>East Malaysia (Sabah & Sarawak)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Information -->
                    <div class="form-section">
                        <h2>Payment Information</h2>
                        <div class="payment-methods">
                            <label class="payment-method">
                                <input type="radio" name="payment_method" value="credit_card" checked>
                                <span class="payment-icon">💳</span>
                                Credit Card
                            </label>
                            <label class="payment-method">
                                <input type="radio" name="payment_method" value="ewallet">
                                <span class="payment-icon">📱</span>
                                E-Wallet (Touch 'n Go / GrabPay)
                            </label>
                        </div>
                        
                        <div id="credit-card-fields">
                            <div class="form-group">
                                <label for="card_number">Card Number *</label>
                                <input type="text" id="card_number" name="card_number" 
                                       placeholder="1234 5678 9012 3456" maxlength="19" required>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="expiry_date">Expiry Date *</label>
                                    <input type="text" id="expiry_date" name="expiry_date" 
                                           placeholder="MM/YY" maxlength="5" required  pattern="^(0[1-9]|1[0-2])\/\d{2}$">
                                </div>
                                <div class="form-group">
                                    <label for="cvv">CVV *</label>
                                    <input type="text" id="cvv" name="cvv" 
                                           placeholder="123" maxlength="3" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="card_name">Name on Card *</label>
                                <input type="text" id="card_name" name="card_name" 
                                       value="<?= htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?>" required>
                            </div>
                        </div>
                    </div>

                    <!-- Order Notes -->
                    <div class="form-section">
                        <h2>Order Notes</h2>
                        <div class="form-group">
                            <label for="order_notes">Special Instructions (Optional)</label>
                            <textarea id="order_notes" name="order_notes" rows="3" 
                                      placeholder="Any special delivery instructions or notes..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="order-summary">
                    <div class="summary-card">
                        <h2>Order Summary</h2>
                        
                        <div class="cart-items">
                            <?php foreach ($cart_items as $item): ?>
                            <div class="cart-item">
                                <div class="item-image">
                                    <img src="../<?= htmlspecialchars($item['image']) ?>" 
                                         alt="<?= htmlspecialchars($item['name']) ?>">
                                </div>
                                <div class="item-details">
                                    <h4><?= htmlspecialchars($item['name']) ?></h4>
                                    <p class="item-quantity">Qty: <?= $item['quantity'] ?></p>
                                </div>
                                <div class="item-price">
                                    RM<?= number_format($item['sale_price'] * $item['quantity'], 2) ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="summary-totals">
                            <div class="total-row">
                                <span>Subtotal:</span>
                                <span>RM<?= number_format($subtotal, 2) ?></span>
                            </div>
                            <div class="total-row">
                                <span>Shipping:</span>
                                <span><?= $shipping == 0 ? 'FREE' : 'RM ' . number_format($shipping, 2) ?></span>
                            </div>
                            <?php if ($discount_amount > 0): ?>
                            <div class="total-row discount-row">
                                <span>Discount (<?= htmlspecialchars($voucher['code'] ?? 'Voucher') ?>):</span>
                                <span>- RM<?= number_format($discount_amount, 2) ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if ($subtotal >= 150): ?>
                            <div class="shipping-notice">
                                <small>🎉 You qualify for free shipping!</small>
                            </div>
                            <?php endif; ?>
                            <div class="total-row final-total">
                                <span>Total:</span>
                                <span>RM<?= number_format($total, 2) ?></span>
                            </div>
                        </div>

                        <input type="hidden" name="order_total" value="<?= htmlspecialchars($total) ?>">

                        <button type="submit" class="place-order-btn">
                            Place Order - RM<?= number_format($total, 2) ?>
                        </button>

                        <div class="security-notice">
                            <p>🔒 Your payment information is secure and encrypted</p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    try {
        // Elements
        const sameAsShipping = document.getElementById('same_as_shipping');
        const billingFields = document.getElementById('billing-fields');
        const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
        const creditCardFields = document.getElementById('credit-card-fields');
        const cardNumber = document.getElementById('card_number');
        const expiryDate = document.getElementById('expiry_date');
        const cvv = document.getElementById('cvv');
        const form = document.getElementById('checkout-form');
        const submitBtn = document.querySelector('.place-order-btn');
        const shippingArea = document.getElementById('shipping_area');
        const shippingRow = document.querySelector('.summary-totals .total-row:nth-child(2) span:last-child');
        const totalRow = document.querySelector('.summary-totals .final-total span:last-child');
        const subtotal = <?= $subtotal ?>;
        const discount = <?= $discount_amount ?>;

        // Voucher selection handler
        document.getElementById('apply-voucher-btn')?.addEventListener('click', function() {
            const selectedVoucher = document.querySelector('input[name="selected_voucher"]:checked');
            
            // Create temporary form to submit voucher selection
            const tempForm = document.createElement('form');
            tempForm.method = 'POST';
            tempForm.action = 'checkout.php';

            // Add selected items
            <?php foreach ($selected_items as $product_id): ?>
            const item<?= $product_id ?> = document.createElement('input');
            item<?= $product_id ?>.type = 'hidden';
            item<?= $product_id ?>.name = 'selected_items[]';
            item<?= $product_id ?>.value = '<?= $product_id ?>';
            tempForm.appendChild(item<?= $product_id ?>);
            <?php endforeach; ?>

            // Add selected voucher
            const voucherInput = document.createElement('input');
            voucherInput.type = 'hidden';
            voucherInput.name = 'selected_voucher_id';
            voucherInput.value = selectedVoucher ? selectedVoucher.value : '';
            tempForm.appendChild(voucherInput);

            // Add shipping area if selected
            if (shippingArea && shippingArea.value) {
                const shippingInput = document.createElement('input');
                shippingInput.type = 'hidden';
                shippingInput.name = 'shipping_area';
                shippingInput.value = shippingArea.value;
                tempForm.appendChild(shippingInput);
            }

            document.body.appendChild(tempForm);
            tempForm.submit();
        });

        // Shipping + total updater
        function updateShipping() {
            let shipping = 0;
            if (subtotal >= 150) {
                shippingRow.textContent = 'FREE';
            } else {
                shipping = (shippingArea.value === 'east') ? 12.00 : 8.00;
                shippingRow.textContent = 'RM ' + shipping.toFixed(2);
            }
            const total = subtotal + shipping - discount;
            totalRow.textContent = 'RM ' + total.toFixed(2);

            // Update hidden field
            const hiddenTotal = document.querySelector('input[name="order_total"]');
            if (hiddenTotal) hiddenTotal.value = total.toFixed(2);
        }

        if (shippingArea) {
            shippingArea.addEventListener('change', updateShipping);
            updateShipping(); // run on load
        }


        // Billing toggle
        if (sameAsShipping && billingFields) {
            function updateBillingVisibility() {
                if (sameAsShipping.checked) {
                    billingFields.style.display = 'none';
                    billingFields.querySelectorAll('input[required], select[required]').forEach(field => {
                        field.removeAttribute('required');
                    });
                } else {
                    billingFields.style.display = 'block';
                    billingFields.querySelectorAll('input[name*="billing_"], select[name*="billing_"]').forEach(field => {
                        if (!field.name.includes('address_line2')) {
                            field.setAttribute('required', 'required');
                        }
                    });
                }
            }
            updateBillingVisibility();
            sameAsShipping.addEventListener('change', updateBillingVisibility);
        }

        // Payment method toggle
        if (paymentMethods && creditCardFields) {
            function updatePaymentVisibility() {
                const selected = document.querySelector('input[name="payment_method"]:checked');
                if (selected && selected.value === 'credit_card') {
                    creditCardFields.style.display = 'block';
                    creditCardFields.querySelectorAll('input').forEach(field => field.setAttribute('required', 'required'));
                } else {
                    creditCardFields.style.display = 'none';
                    creditCardFields.querySelectorAll('input').forEach(field => field.removeAttribute('required'));
                }
            }
            updatePaymentVisibility();
            paymentMethods.forEach(method => method.addEventListener('change', updatePaymentVisibility));
        }

        // Card number formatter
        if (cardNumber) {
            cardNumber.addEventListener('input', function() {
                let value = this.value.replace(/\D/g, '');
                value = value.substring(0, 16); 
                this.value = value.replace(/(\d{4})(?=\d)/g, '$1 ');
            });
        }

        // Expiry date formatter
        if (expiryDate) {
            expiryDate.addEventListener('input', function() {
                let value = this.value.replace(/\D/g, '');
                if (value.length > 4) value = value.slice(0,4);
                if (value.length >= 2) {
                    let month = parseInt(value.substring(0, 2), 10);

                    if (month < 1) {
                        month = 1;
                    } else if (month > 12) {
                        month = 12;
                    }

            value = month.toString().padStart(2, '0') + value.substring(2);
            }

                if (value.length >= 3) {
                    value = value.substring(0, 2) + '/' + value.substring(2);
                }
                this.value = value;
            });
        }

        // CVV numeric only
        if (cvv) {
            cvv.addEventListener('input', function() {
                 this.value = this.value.replace(/[^0-9]/g, '').substring(0, 3);
            });
        }

        // Form submission validation
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                const style = window.getComputedStyle(field);
                if (style.display === 'none' || field.offsetParent === null) {
                    return;
                }
                const value = (field.value || '').toString().trim();
                if (!value) {
                    field.style.borderColor = '#dc3545';
                    isValid = false;
                } else {
                    field.style.borderColor = '';
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
                return;
            }

            // Email validation
            const emailEl = document.getElementById('email');
            if (emailEl) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailEl.value.trim())) {
                    e.preventDefault();
                    alert('Please enter a valid email address.');
                    emailEl.focus();
                    return;
                }
            }

            // Card validation (if credit card selected)
            const selectedPayment = document.querySelector('input[name="payment_method"]:checked')?.value;
            if (selectedPayment === 'credit_card') {
                const cardNum = document.getElementById('card_number')?.value.replace(/\s/g, '') || '';
                if (cardNum.length < 13 || cardNum.length > 19) {
                    e.preventDefault();
                    alert('Please enter a valid card number.');
                    return;
                }

                const expiry = document.getElementById('expiry_date')?.value || '';
                if (!/^\d{2}\/\d{2}$/.test(expiry)) {
                    e.preventDefault();
                    alert('Please enter expiry date in MM/YY format.');
                    return;
                }

                const [expMonth, expYear] = expiry.split('/').map(Number);
                if (expMonth < 1 || expMonth > 12) {
                    e.preventDefault();
                    alert('Expiry month must be between 01 and 12.');
                    return;
                }


                const cvvValue = document.getElementById('cvv')?.value || '';
                if (cvvValue.length !== 3) {
                    e.preventDefault();
                    alert('Please enter a valid CVV.');
                    return;
                }
            }

            // Name validation
            const firstNameField = document.getElementById('first_name');
            const lastNameField = document.getElementById('last_name');
            const nameRegex = /^[A-Za-z\s]+$/;

            if (firstNameField && !nameRegex.test(firstNameField.value.trim())) {
                e.preventDefault();
                alert("First name must contain only letters and spaces.");
                firstNameField.focus();
                return;
            }

            if (lastNameField && !nameRegex.test(lastNameField.value.trim())) {
                e.preventDefault();
                alert("Last name must contain only letters and spaces.");
                lastNameField.focus();
                return;
            }

            // Phone number validation (Malaysia standard)
            const phoneField = document.getElementById('phone');
            const phoneRegex = /^01\d{8,9}$/;
            if (phoneField) {
                const phoneValue = phoneField.value.trim();
                if (!phoneRegex.test(phoneValue)) {
                    e.preventDefault();
                    alert("Please enter a valid Malaysian phone number (e.g. 0123456789 or 01112345678).");
                    phoneField.focus();
                    return;
                }
            }

            // Show loading state
            try {
                if (submitBtn) {
                    submitBtn.innerHTML = 'Processing Order...';
                    submitBtn.disabled = true;
                }
            } catch (uiErr) {
                console.error('Error updating UI before submit:', uiErr);
            }
        });

    } catch (err) {
        console.error('Checkout page JS error:', err);
    }
});
</script>


<?php include '../includes/footer.php'; ?>