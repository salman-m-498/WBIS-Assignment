<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', ''); // Default XAMPP MySQL password is empty
define('DB_NAME', 'web_db');

// Create connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Function to generate unique user ID
function generateUserId() {
    return 'USR' . str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT);
}

// Function to check if user ID already exists
function userIdExists($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM user WHERE user_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetchColumn() > 0;
}

// Function to generate unique user ID that doesn't exist
function getUniqueUserId($pdo) {
    do {
        $userId = generateUserId();
    } while (userIdExists($pdo, $userId));
    return $userId;
}

// Function to check if email already exists
function emailExists($pdo, $email) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM user WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetchColumn() > 0;
}

// Function to check if username already exists
function usernameExists($pdo, $username) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM user WHERE username = ?");
    $stmt->execute([$username]);
    return $stmt->fetchColumn() > 0;
}
?>