<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header('Location: admin_login.php');
    exit();
}

// Use POST for ajax
$action = $_POST['action'] ?? '';
$reviewId = $_POST['id'] ?? '';

if ($reviewId && in_array($action, ['approve', 'reject'])) {
    $status = ($action === 'approve') ? 'approved' : 'rejected';

    $sql = "UPDATE reviews SET status = :status WHERE review_id = :id";
    $stm = $pdo->prepare($sql);
    $stm->execute([
        ':status' => $status,
        ':id'     => $reviewId
    ]);
    // Return JSON response for JS
    echo json_encode(['success' => true, 'newStatus' => $status]);
    exit();
}

// If invalid request
http_response_code(400);
echo json_encode(['success' => false, 'message' => 'Invalid request']);
