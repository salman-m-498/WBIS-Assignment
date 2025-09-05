<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header('Location: ../public/login.php');
    exit();
}

$userId = $_SESSION['user_id'];

// Get current profile pic
$stmt = $pdo->prepare("SELECT profile_pic FROM user WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$profilePic = $user['profile_pic'] ?? 'default_profile_pic.jpg';

// Handle deletion after POST confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {

    // Delete profile picture if not default
    $profileDir = "../assets/images/profile_pictures/";
    if (!empty($profilePic) && $profilePic !== 'default_profile_pic.jpg' && file_exists($profileDir . $profilePic)) {
        unlink($profileDir . $profilePic);
    }

    // Delete user_profiles entry
    $stmt = $pdo->prepare("DELETE FROM user_profiles WHERE user_id = ?");
    $stmt->execute([$userId]);

    // Delete user entry
    $stmt = $pdo->prepare("DELETE FROM user WHERE user_id = ?");
    $stmt->execute([$userId]);

    // Clear session
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();

    // Redirect to home/login with message
    session_start();
    $_SESSION['success'] = "Your account has been permanently deleted.";
    header('Location: ../public/login.php');
    exit();
}

include '../includes/header.php';
?>