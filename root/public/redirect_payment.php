<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: checkout.php");
    exit;
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=checkout.php");
    exit;
}

// Save POST data
$_SESSION['checkout_post'] = $_POST;
$_SESSION['selected_items'] = $_POST['selected_items'] ?? [];

// Calculate totals and store in session
$user_id = $_SESSION['user_id'];
$cart_items_stmt = $pdo->prepare("
    SELECT c.*, p.sale_price 
    FROM cart c 
    JOIN products p ON c.product_id = p.product_id 
    WHERE c.user_id = ?
");
$cart_items_stmt->execute([$user_id]);
$cart_items = $cart_items_stmt->fetchAll(PDO::FETCH_ASSOC);

$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['sale_price'] * $item['quantity'];
}
$shipping_area = $_POST['shipping_area'] ?? 'west';
$shipping = ($subtotal >= 150) ? 0 : (($shipping_area === 'east') ? 12.00 : 8.00);
$_SESSION['order_total'] = $subtotal + $shipping;

// Redirect based on payment method
$payment_method = $_POST['payment_method'] ?? 'credit_card';
if ($payment_method === 'ewallet') {
    header("Location: ewallet_payment.php");
    exit;
} else {
    // Credit card: forward via auto-submitting POST form
    echo '<form id="forwardForm" method="POST" action="process_order.php">';
    foreach ($_POST as $key => $value) {
        if (is_array($value)) {
            foreach ($value as $v) {
                echo '<input type="hidden" name="'.htmlspecialchars($key).'[]" value="'.htmlspecialchars($v).'">';
            }
        } else {
            echo '<input type="hidden" name="'.htmlspecialchars($key).'" value="'.htmlspecialchars($value).'">';
        }
    }

    if (!empty($_SESSION['selected_items'])) {
    foreach ($_SESSION['selected_items'] as $product_id) {
        echo '<input type="hidden" name="selected_items[]" value="'.htmlspecialchars($product_id).'">';
    }
}
    echo '</form>';
    echo '<script>document.getElementById("forwardForm").submit();</script>';
    exit;
}