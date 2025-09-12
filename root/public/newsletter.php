<?php
session_start();
require_once '../includes/db.php';
$root = dirname(__DIR__); // one level up from current folder
require_once $root . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Please enter a valid email address.'];
        header('Location: ../index.php');
        exit;
    }

    try {

        // Send confirmation email
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'toylandstore97@gmail.com';
        $mail->Password   = 'jowu egyx vsga cghe'; // app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('toylandstore97@gmail.com', 'ToyLand Store');
        $mail->addAddress($email);
        $mail->addReplyTo('support@toyland.com', 'ToyLand Support');

        $mail->isHTML(true);
        $mail->Subject = "Thanks for Subscribing to ToyLand!";
        $mail->Body = "
            <h2>Welcome to ToyLand!</h2>
            <p>Thanks for subscribing to our newsletter 🎉</p>
            <p>We'll keep you updated with <strong>exclusive offers</strong>, <strong>new arrivals</strong>, and more!</p>
            <p style='margin-top:20px;'>– The ToyLand Team</p>
        ";

        $mail->send();

        $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'Thanks for subscribing! Please check your email.'];
    } catch (Exception $e) {
        error_log("Newsletter send failed: {$mail->ErrorInfo}");
        $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Subscription failed. Please try again later.'];
    }
}

// Always redirect back
header('Location: /index.php');
exit;
