<?php
session_start();

// Retrieve checkout data from session
if (!isset($_SESSION['checkout_post'])) {
    header("Location: checkout.php");
    exit;
}

$data = $_SESSION['checkout_post'];
$order_total = $_SESSION['order_total'] ?? 0;
$page_title = "E-Wallet Payment";

include '../includes/header.php';
?>

<div class="payment-container">
    <h2 class="payment-title">E-Wallet Payment</h2>
    <p class="order-amount">Order Amount: <strong>RM<?= number_format($order_total, 2) ?></strong></p>

    <form action="process_order.php" method="POST" class="payment-form">
        <div class="form-group">
            <label for="wallet_id">Wallet ID / Phone Number</label>
            <input type="text" id="wallet_id" name="wallet_id" 
                placeholder="0123456789" 
                pattern="01\d{8,9}" 
                title="Must be a valid Malaysian phone number (e.g., 0123456789)" 
                required>
        </div>

        <div class="form-group">
            <label for="wallet_pin">PIN</label>
            <input type="password" id="wallet_pin" name="wallet_pin" 
                pattern="\d{6}" 
                maxlength="6" 
                title="Must be exactly 6 digits" 
                required>
        </div>

       <?php foreach ($data as $key => $value): ?>
    <?php if (is_array($value)): ?>
        <?php foreach ($value as $subVal): ?>
            <input type="hidden" name="<?= htmlspecialchars($key) ?>[]" value="<?= htmlspecialchars($subVal) ?>">
        <?php endforeach; ?>
    <?php else: ?>
        <input type="hidden" name="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($value) ?>">
    <?php endif; ?>
<?php endforeach; ?>
        <input type="hidden" name="payment_method" value="ewallet">

        <!-- ✅ Ensure button is styled and visible -->
        <button type="submit" class="btn-confirm">
            Confirm Payment
        </button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
