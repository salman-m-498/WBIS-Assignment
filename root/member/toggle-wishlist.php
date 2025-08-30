<?php 
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$userId = $_SESSION['user_id'];
$productId = $_POST['product_id'] ?? null;

if (!$productId) {
    echo json_encode(['success' => false, 'message' => 'No product ID']);
    exit();
}

// Check if already in wishlist table
$stmt = $pdo->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = ? AND product_id = ?");
$stmt->execute([$userId, $productId]);
$exists = $stmt->fetchColumn() > 0;

if ($exists) {
    // Remove from wishlist
    $delete = $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
    $delete->execute([$userId, $productId]);
    echo json_encode(['success' => true, 'action' => 'removed']);
} else {
    // Check last id and generate new id
    $stmt = $pdo->query("SELECT wishlist_id FROM wishlist ORDER BY wishlist_id DESC LIMIT 1");
    $lastId = $stmt->fetchColumn();
    $newId = $lastId ? ('WL' . str_pad(((int)substr($lastId, 2)) + 1, 9, '0', STR_PAD_LEFT)) : 'WL000000001';

    // Insert into wishlist tabel
    $insert = $pdo->prepare("INSERT INTO wishlist (wishlist_id, user_id, product_id, created_at) VALUES (?, ?, ?, NOW())");
    $insert->execute([$newId, $userId, $productId]);
    echo json_encode(['success' => true, 'action' => 'added']);
}