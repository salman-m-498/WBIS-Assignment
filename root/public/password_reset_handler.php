<?php

session_start();
date_default_timezone_set('Asia/Kuala_Lumpur');
require_once '../includes/db.php';
require_once '../includes/functions.php';

$root = dirname(__DIR__); // one level up from current folder
require_once $root . '/../vendor/autoload.php';

// Set MySQL timezone to match Malaysia
try {
    $pdo->exec("SET time_zone = '+08:00'");
} catch (PDOException $e) {
    error_log("Timezone setting error: " . $e->getMessage());
}

// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit();
}

$action = $_POST['action'] ?? '';

if ($action === 'forgot_password') {
    handleForgotPassword();
} elseif ($action === 'reset_password') {
    handleResetPassword();
} else {
    header('Location: login.php');
    exit();
}

function handleForgotPassword() {
    global $pdo;
    
    $email = trim($_POST['email'] ?? '');
    
    // Validate input
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Please enter a valid email address.';
        header('Location: forgot_password.php');
        exit();
    }
    
    // Check rate limit
    if (!checkRateLimit($email)) {
        $_SESSION['error'] = 'Too many reset requests. Please wait before trying again.';
        header('Location: forgot_password.php');
        exit();
    }
    
    try {
        // Set timezone for this connection
        $pdo->exec("SET time_zone = '+08:00'");
        
        // Check if user exists
        $stmt = $pdo->prepare("SELECT user_id, username FROM user WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if (!$user) {
            // Don't reveal if email exists or not for security
            $_SESSION['success'] = 'If an account with this email exists, you will receive a password reset link shortly.';
            header('Location: forgot_password.php');
            exit();
        } 

        // Generate unique token
        $token = bin2hex(random_bytes(32));

        // Delete any existing reset tokens for this user
        $stmt = $pdo->prepare("DELETE FROM password_resets WHERE user_id = ?");
        $stmt->execute([$user['user_id']]);
        
        $reset_id = generateNextId($pdo, 'password_resets', 'reset_id', 'R', 6);
        
        // Insert new reset token using database functions for time
        $stmt = $pdo->prepare("
            INSERT INTO password_resets (reset_id, user_id, token, expires_at, created_at) 
            VALUES (?, ?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR), NOW())
        ");
        $stmt->execute([$reset_id, $user['user_id'], $token]);
        
        // Verify the token was inserted correctly
        $stmt = $pdo->prepare("SELECT token, expires_at,NOW() AS `current_time` FROM password_resets WHERE reset_id = ?");
        $stmt->execute([$reset_id]);
        $verify_result = $stmt->fetch();
        
        // Test the exact query used in reset_password.php
        $stmt = $pdo->prepare("
            SELECT user_id, expires_at, used 
            FROM password_resets 
            WHERE token = ? AND expires_at > NOW() AND used = 0
        ");
        $stmt->execute([$token]);
        $test_result = $stmt->fetch();
    
        
        // Send reset email
        if (sendResetEmail($email, $user['username'], $token)) {
            $_SESSION['success'] = 'Password reset link has been sent to your email address.';
        } else {
            $_SESSION['error'] = 'Failed to send reset email. Please try again later.';
        }
        
    } catch (PDOException $e) {
        $_SESSION['error'] = 'An error occurred. Please try again.';
    }
    
    header('Location: forgot_password.php');
    exit();
}

function handleResetPassword() {
    global $pdo;
    
    $token = trim($_POST['token'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    
    // Validate input
    if (empty($token) || empty($password) || empty($confirm_password)) {
        $_SESSION['error'] = 'Please fill in all fields.';
        header('Location: reset_password.php?token=' . urlencode($token));
        exit();
    }
    
    // Check password match
    if ($password !== $confirm_password) {
        $_SESSION['error'] = 'Passwords do not match.';
        header('Location: reset_password.php?token=' . urlencode($token));
        exit();
    }
    
    // Validate password strength
    if (strlen($password) < 8) {
        $_SESSION['error'] = 'Password must be at least 8 characters long.';
        header('Location: reset_password.php?token=' . urlencode($token));
        exit();
    }
    
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', $password)) {
        $_SESSION['error'] = 'Password must contain at least one uppercase letter, one lowercase letter, and one number.';
        header('Location: reset_password.php?token=' . urlencode($token));
        exit();
    }
    
    try {
        // Set timezone for this connection
        $pdo->exec("SET time_zone = '+08:00'");
        
        $pdo->beginTransaction();
        
        // Two-step verification instead of JOIN
        // Step 1: Get the reset record
        $stmt = $pdo->prepare("
            SELECT user_id, expires_at, used 
            FROM password_resets 
            WHERE token = ? AND expires_at > NOW() AND used = 0
        ");
        $stmt->execute([$token]);
        $reset_record = $stmt->fetch();
        
        if (!$reset_record) {
            $_SESSION['error'] = 'Invalid or expired reset link.';
            header('Location: forgot_password.php');
            exit();
        }
        
        // Step 2: Get the user record
        $stmt = $pdo->prepare("SELECT user_id, email, username FROM user WHERE user_id = ?");
        $stmt->execute([$reset_record['user_id']]);
        $user = $stmt->fetch();
        
        if (!$user) {
            $_SESSION['error'] = 'Invalid reset link.';
            header('Location: forgot_password.php');
            exit();
        }
        
        
        // Hash new password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Update user password
        $stmt = $pdo->prepare("UPDATE user SET password = ?, updated_at = NOW() WHERE user_id = ?");
        $stmt->execute([$hashed_password, $user['user_id']]);
        
        // Mark token as used
        $stmt = $pdo->prepare("UPDATE password_resets SET used = 1 WHERE token = ?");
        $stmt->execute([$token]);
        
        // Delete all remember tokens for security
        $stmt = $pdo->prepare("UPDATE user SET remember_token = NULL WHERE user_id = ?");
        $stmt->execute([$user['user_id']]);
        
        // Clean up old password reset tokens
        $stmt = $pdo->prepare("DELETE FROM password_resets WHERE user_id = ? AND token != ?");
        $stmt->execute([$user['user_id'], $token]);
        
        $pdo->commit();
        
        // Send confirmation email
        sendPasswordChangeConfirmation($user['email'], $user['username']);
        
        $_SESSION['success'] = 'Your password has been reset successfully. You can now login with your new password.';
        header('Location: login.php');
        exit();
        
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $_SESSION['error'] = 'An error occurred while resetting your password. Please try again.';
        header('Location: reset_password.php?token=' . urlencode($token));
        exit();
    }
}

function sendResetEmail($email, $username, $token) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->SMTPDebug = 0;
        $mail->Debugoutput = 'error_log';
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'toylandstore97@gmail.com';
        $mail->Password   = 'jowu egyx vsga cghe';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        // Recipients
        $mail->setFrom('toylandstore97@gmail.com', 'ToyLand Store');
        $mail->addAddress($email, $username);
        $mail->addReplyTo('support@toyland.com', 'ToyLand Support');
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Reset Your ToyLand Store Password';
        
        $reset_link = 'http://localhost/WBIS-Assignment/root/public/reset_password.php?token=' . urlencode($token);
        
        $mail->Body = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Password Reset - ToyLand Store</title>
        </head>
        <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
            <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
                <div style="background: #007bff; color: white; padding: 20px; text-align: center;">
                    <h1 style="margin: 0;">ToyLand Store</h1>
                </div>
                
                <div style="background: #f8f9fa; padding: 30px;">
                    <h2>Password Reset Request</h2>
                    <p>Hello <strong>' . htmlspecialchars($username) . '</strong>,</p>
                    
                    <p>We received a request to reset the password for your ToyLand Store account associated with this email address.</p>
                    
                    <p>To reset your password, please click the button below:</p>
                    
                    <div style="text-align: center; margin: 30px 0;">
                        <a href="' . $reset_link . '" 
                           style="background: #007bff; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;">
                            Reset My Password
                        </a>
                    </div>
                    
                    <p>If the button doesn\'t work, you can copy and paste this link into your browser:</p>
                    <p style="word-break: break-all; background: #e9ecef; padding: 10px; border-radius: 3px;">
                        ' . $reset_link . '
                    </p>
                    
                    <p><strong>Important:</strong></p>
                    <ul>
                        <li>This link will expire in 1 hour</li>
                        <li>The link can only be used once</li>
                        <li>If you didn\'t request this reset, please ignore this email</li>
                    </ul>
                </div>
                
                <div style="background: #6c757d; color: white; padding: 20px; text-align: center; font-size: 12px;">
                    <p style="margin: 0;">This is an automated message from ToyLand Store. Please do not reply to this email.</p>
                    <p style="margin: 5px 0 0 0;">If you have questions, contact our support team at support@toyland.com</p>
                </div>
            </div>
        </body>
        </html>
        ';
        
        $mail->AltBody = 'Hello ' . $username . ',\n\n'
                       . 'We received a request to reset your ToyLand Store password.\n\n'
                       . 'Please visit this link to reset your password:\n'
                       . $reset_link . '\n\n'
                       . 'This link will expire in 1 hour and can only be used once.\n\n'
                       . 'If you didn\'t request this reset, please ignore this email.\n\n'
                       . 'ToyLand Store Team';
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

function sendPasswordChangeConfirmation($email, $username) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'toylandstore97@gmail.com';
        $mail->Password   = 'jowu egyx vsga cghe';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        // Recipients
        $mail->setFrom('toylandstore97@gmail.com', 'ToyLand Store');
        $mail->addAddress($email, $username);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Password Changed Successfully - ToyLand Store';
        
        $mail->Body = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Password Changed - ToyLand Store</title>
        </head>
        <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
            <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
                <div style="background: #28a745; color: white; padding: 20px; text-align: center;">
                    <h1 style="margin: 0;">ToyLand Store</h1>
                </div>
                
                <div style="background: #f8f9fa; padding: 30px;">
                    <h2>Password Changed Successfully</h2>
                    <p>Hello <strong>' . htmlspecialchars($username) . '</strong>,</p>
                    
                    <p>Your ToyLand Store account password has been successfully changed on ' . date('F j, Y \a\t g:i A') . '.</p>
                    
                    <p>If you made this change, no further action is required.</p>
                    
                    <p><strong>If you did NOT make this change:</strong></p>
                    <ul>
                        <li>Contact our support team immediately at support@toyland.com</li>
                        <li>Reset your password again for security</li>
                        <li>Review your account activity</li>
                    </ul>
                    
                    <div style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 20px 0;">
                        <p style="margin: 0;"><strong>Security Tips:</strong></p>
                        <ul style="margin: 10px 0 0 0;">
                            <li>Never share your password with anyone</li>
                            <li>Use a unique password for your ToyLand account</li>
                            <li>Log out when using shared computers</li>
                            <li>Monitor your account for unusual activity</li>
                        </ul>
                    </div>
                    
                    <p>Thank you for keeping your account secure!</p>
                </div>
                
                <div style="background: #6c757d; color: white; padding: 20px; text-align: center; font-size: 12px;">
                    <p style="margin: 0;">This is an automated message from ToyLand Store. Please do not reply to this email.</p>
                    <p style="margin: 5px 0 0 0;">If you have questions, contact our support team at support@toyland.com</p>
                </div>
            </div>
        </body>
        </html>
        ';
        
        $mail->AltBody = 'Hello ' . $username . ',\n\n'
                       . 'Your ToyLand Store account password has been successfully changed on ' . date('F j, Y \a\t g:i A') . '.\n\n'
                       . 'If you made this change, no further action is required.\n\n'
                       . 'If you did NOT make this change:\n'
                       . '- Contact our support team immediately at support@toyland.com\n'
                       . '- Reset your password again for security\n'
                       . '- Review your account activity\n\n'
                       . 'Security Tips:\n'
                       . '- Never share your password with anyone\n'
                       . '- Use a unique password for your ToyLand account\n'
                       . '- Log out when using shared computers\n'
                       . '- Monitor your account for unusual activity\n\n'
                       . 'Thank you for keeping your account secure!\n\n'
                       . 'ToyLand Store Team';
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function cleanupExpiredTokens() {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("DELETE FROM password_resets WHERE expires_at < NOW()");
        $stmt->execute();
        
        $deleted = $stmt->rowCount();
        
        return $deleted;
    } catch (PDOException $e) {
        return false;
    }
}

function checkRateLimit($email) {
    global $pdo;
    
    try {
        // Allow max 10 reset requests per email per hour
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as reset_count 
            FROM password_resets pr 
            WHERE pr.user_id IN (SELECT user_id FROM user WHERE email = ?) 
            AND pr.created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
        ");
        $stmt->execute([$email]);
        $result = $stmt->fetch();
        
        return $result['reset_count'] < 10;
    } catch (PDOException $e) {
        return true; // Allow on error to not block legitimate users
    }
}

?>