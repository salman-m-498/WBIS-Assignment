<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header('Location: admin_login.php');
    exit();
}

if (!isset($_GET['id'], $_GET['action'])) {
    die("Invalid request.");
}

$userId = $_GET['id'];
$action = $_GET['action'];

if ($action === 'block') {
    $stmt = $pdo->prepare("UPDATE user SET status = 'blocked' WHERE user_id = ?");
    $stmt->execute([$userId]);
} elseif ($action === 'unblock') {
    $stmt = $pdo->prepare("UPDATE user SET status = 'active' WHERE user_id = ?");
    $stmt->execute([$userId]);
}

header("Location: members.php");
exit();
