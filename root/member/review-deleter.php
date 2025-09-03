<?php
require_once '../includes/db.php';

if (isset($_GET['id'])) {
    $reviewId = $_GET['id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM reviews WHERE review_id = ?");
        $stmt->execute([$reviewId]); // PDO uses execute with an array

        header("Location: reviews.php?msg=deleted");
        exit();
    } catch (PDOException $e) {
        // you can log the error for debugging
        error_log("Delete review failed: " . $e->getMessage());
        header("Location: reviews.php?msg=error");
        exit();
    }
} else {
    header("Location: reviews.php");
    exit();
}



