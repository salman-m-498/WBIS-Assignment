<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php'; // for generateNextId()

header('Content-Type: application/json');

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$order_id = $_POST['order_id'] ?? null;

if (!$order_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

try {
    // Get order info
    $stmt = $pdo->prepare("SELECT order_status, total_amount, payment_id 
                           FROM orders 
                           WHERE order_id = ? AND user_id = ?");
    $stmt->execute([$order_id, $user_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo json_encode(['success' => false, 'message' => 'Order not found']);
        exit;
    }

    // Only allow cancellation if still pending
    if ($order['order_status'] !== 'pending') {
        echo json_encode(['success' => false, 'message' => 'Only pending orders can be cancelled']);
        exit;
    }

    // Update order to cancel_requested
    $stmt = $pdo->prepare("UPDATE orders 
                           SET order_status = 'cancel_requested', updated_at = NOW() 
                           WHERE order_id = ? AND user_id = ?");
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
        $order['payment_id'],
        $order['total_amount'],
        'original'
    ]);

    echo json_encode(['success' => true, 'message' => 'Cancellation request submitted']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
