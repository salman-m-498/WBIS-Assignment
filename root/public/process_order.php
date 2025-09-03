<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=checkout.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Get checkout data from session
$data = $_SESSION['checkout_post'] ?? null;
$selected_items = $_SESSION['selected_items'] ?? [];
$order_total = $_SESSION['order_total'] ?? 0;

// If billing is same as shipping, copy values
if (!empty($data['same_as_shipping'])) {
    $data['billing_first_name']  = $data['first_name'];
    $data['billing_last_name']   = $data['last_name'];
    $data['billing_address_line1'] = $data['address_line1'];
    $data['billing_address_line2'] = $data['address_line2'] ?? '';
    $data['billing_city']        = $data['city'];
    $data['billing_state']       = $data['state'];
    $data['billing_postal_code'] = $data['postal_code'];
    $data['billing_area']        = $data['shipping_area'];
}


if (!$data || empty($selected_items)) {
    header('Location: checkout.php?error=no_data');
    exit;
}

// Calculate subtotal and shipping again if needed
$cart_items_stmt = $pdo->prepare("
    SELECT c.*, p.sale_price
    FROM cart c
    JOIN products p ON c.product_id = p.product_id
    WHERE c.user_id = ? AND c.product_id IN (" . implode(',', array_fill(0, count($selected_items), '?')) . ")
");
$cart_items_stmt->execute(array_merge([$user_id], $selected_items));
$cart_items = $cart_items_stmt->fetchAll(PDO::FETCH_ASSOC);

$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['sale_price'] * $item['quantity'];
}

$shipping_area = $data['shipping_area'] ?? 'west';
$shipping = ($subtotal >= 150) ? 0 : (($shipping_area === 'east') ? 12 : 8);
$total = $subtotal + $shipping;

// Insert into orders table
$order_id = generateNextId($pdo, 'orders', 'order_id', 'ORD', 10);
$order_number = 'ORD' . date('Ymd') . rand(100,999);
$stmt = $pdo->prepare("
    INSERT INTO orders (
        order_id, user_id, order_number, subtotal, shipping_cost, total_amount,
        shipping_first_name, shipping_last_name, shipping_address_line1, shipping_address_line2,
        shipping_city, shipping_state, shipping_postal_code, shipping_area,
        billing_first_name, billing_last_name, billing_address_line1, billing_address_line2,
        billing_city, billing_state, billing_postal_code, billing_area,
        contact_email, contact_phone, order_notes
    ) VALUES (
        ?, ?, ?, ?, ?, ?,
        ?, ?, ?, ?,
        ?, ?, ?, ?,
        ?, ?, ?, ?,
        ?, ?, ?, ?,
        ?, ?, ?
    )
");
$stmt->execute([
    $order_id, $user_id, $order_number, $subtotal, $shipping, $total,
    $data['first_name'], $data['last_name'], $data['address_line1'], $data['address_line2'] ?? '',
    $data['city'], $data['state'], $data['postal_code'], $shipping_area,
    $data['billing_first_name'] ?? $data['first_name'], $data['billing_last_name'] ?? $data['last_name'],
    $data['billing_address_line1'] ?? $data['address_line1'],
    $data['billing_address_line2'] ?? $data['address_line2'] ?? '',
    $data['billing_city'] ?? $data['city'], $data['billing_state'] ?? $data['state'],
    $data['billing_postal_code'] ?? $data['postal_code'],
    $data['billing_area'] ?? $shipping_area,
    $data['email'], $data['phone'], $data['order_notes'] ?? ''
]);

// Insert order items
foreach ($cart_items as $item) {
    $stmt = $pdo->prepare("
        INSERT INTO order_items (order_id, product_id, quantity, unit_price, total_price)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $order_id,
        $item['product_id'],
        $item['quantity'],
        $item['sale_price'],
        $item['sale_price'] * $item['quantity']
    ]);
}

// Insert payment record
$payment_id = generateNextId($pdo, 'payments', 'payment_id', 'PAY', 10);
$transaction_id = generateNextId($pdo, 'payments', 'transaction_id', 'TXN', 10);
$stmt = $pdo->prepare("
    INSERT INTO payments (
        payment_id, order_id, user_id, amount, payment_method, payment_status, transaction_id, wallet_id
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");
$stmt->execute([
    $payment_id, $order_id, $user_id, $total,
    $data['payment_method'] ?? 'ewallet',
    'success',
    $transaction_id,
    $data['wallet_id'] ?? null
]);

$updateStmt = $pdo->prepare("UPDATE orders SET payment_id = ? WHERE order_id = ?");
$updateStmt->execute([$payment_id, $order_id]);

// Clear session cart
unset($_SESSION['checkout_post']);
unset($_SESSION['selected_items']);
unset($_SESSION['order_total']);

$_SESSION['order_success'] = [
    'order_id' => $order_id,
    'user_id' => $user_id
];

// Remove purchased items from cart
$deleteCartStmt = $pdo->prepare("
    DELETE FROM cart 
    WHERE user_id = ? AND product_id IN (" . implode(',', array_fill(0, count($selected_items), '?')) . ")
");
$deleteCartStmt->execute(array_merge([$user_id], $selected_items));



// Redirect to confirmation page
header("Location: payment_success.php?order_id={$order_id}");
exit;
