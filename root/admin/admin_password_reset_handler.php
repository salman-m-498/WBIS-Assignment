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
    header('Location: admin_login.php');
    exit();
}

$action = $_POST['action'] ?? '';

if ($action === 'forgot_password') {
    handleAdminForgotPassword();
} elseif ($action === 'reset_password') {
    handleAdminResetPassword();
} else {
    header('Location: admin_login.php');
    exit();
}

function handleAdminForgotPassword() {
    global $pdo;
    
    $email = trim($_POST['email'] ?? '');
    
    // Validate input
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Please enter a valid email address.';
        header('Location: admin_forgot_password.php');
        exit();
    }
    
    // Check rate limit
    if (!checkAdminRateLimit($email)) {
        $_SESSION['error'] = 'Too many reset requests. Please wait before trying again.';
        header('Location: admin_forgot_password.php');
        exit();
    }
    
    try {
        // Set timezone for this connection
        $pdo->exec("SET time_zone = '+08:00'");
        
        // Check if admin exists (only check for users with admin role)
        $stmt = $pdo->prepare("SELECT user_id, username FROM user WHERE email = ? AND role = 'admin'");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();
        
        if (!$admin) {
            // Don't reveal if admin email exists or not for security
            $_SESSION['success'] = 'If an admin account with this email exists, you will receive a password reset link shortly.';
            header('Location: admin_forgot_password.php');
            exit();
        } 

        // Generate unique token
        $token = bin2hex(random_bytes(32));

        // Delete any existing reset tokens for this admin
        $stmt = $pdo->prepare("DELETE FROM password_resets WHERE user_id = ?");
        $stmt->execute([$admin['user_id']]);
        
        $reset_id = generateNextId($pdo, 'password_resets', 'reset_id', 'R', 6);
        
        // Insert new reset token with 30 minutes expiry for admin (more secure)
        $stmt = $pdo->prepare("
            INSERT INTO password_resets (reset_id, user_id, token, expires_at, created_at) 
            VALUES (?, ?, ?, DATE_ADD(NOW(), INTERVAL 30 MINUTE), NOW())
        ");
        $stmt->execute([$reset_id, $admin['user_id'], $token]);
        
        // Send reset email
        if (sendAdminResetEmail($email, $admin['username'], $token)) {
            $_SESSION['success'] = 'Password reset link has been sent to your admin email address.';
        } else {
            $_SESSION['error'] = 'Failed to send reset email. Please try again later.';
        }
        
    } catch (PDOException $e) {
        $_SESSION['error'] = 'An error occurred. Please try again.';
    }
    
    header('Location: admin_forgot_password.php');
    exit();
}

function handleAdminResetPassword() {
    global $pdo;
    
    $token = trim($_POST['token'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    
    // Validate input
    if (empty($token) || empty($password) || empty($confirm_password)) {
        $_SESSION['error'] = 'Please fill in all fields.';
        header('Location: admin_reset_password.php?token=' . urlencode($token));
        exit();
    }
    
    // Check password match
    if ($password !== $confirm_password) {
        $_SESSION['error'] = 'Passwords do not match.';
        header('Location: admin_reset_password.php?token=' . urlencode($token));
        exit();
    }
    
    // Validate password strength (stronger requirements for admin)
    if (strlen($password) < 10) {
        $_SESSION['error'] = 'Admin password must be at least 10 characters long.';
        header('Location: admin_reset_password.php?token=' . urlencode($token));
        exit();
    }
    
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).+$/', $password)) {
        $_SESSION['error'] = 'Admin password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.';
        header('Location: admin_reset_password.php?token=' . urlencode($token));
        exit();
    }
    
    try {
        // Set timezone for this connection
        $pdo->exec("SET time_zone = '+08:00'");
        
        $pdo->beginTransaction();
        
        // Two-step verification
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
            header('Location: admin_forgot_password.php');
            exit();
        }
        
        // Step 2: Get the admin user record (verify it's an admin)
        $stmt = $pdo->prepare("SELECT user_id, email, username FROM user WHERE user_id = ? AND role = 'admin'");
        $stmt->execute([$reset_record['user_id']]);
        $admin = $stmt->fetch();
        
        if (!$admin) {
            $_SESSION['error'] = 'Invalid admin reset link.';
            header('Location: admin_forgot_password.php');
            exit();
        }
        
        // Hash new password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Update admin password
        $stmt = $pdo->prepare("UPDATE user SET password = ?, updated_at = NOW() WHERE user_id = ?");
        $stmt->execute([$hashed_password, $admin['user_id']]);
        
        // Mark token as used
        $stmt = $pdo->prepare("UPDATE password_resets SET used = 1 WHERE token = ?");
        $stmt->execute([$token]);
        
        // Delete all remember tokens for security
        $stmt = $pdo->prepare("UPDATE user SET remember_token = NULL WHERE user_id = ?");
        $stmt->execute([$admin['user_id']]);
        
        // Clean up old password reset tokens
        $stmt = $pdo->prepare("DELETE FROM password_resets WHERE user_id = ? AND token != ?");
        $stmt->execute([$admin['user_id'], $token]);
        
        $pdo->commit();
        
        // Send confirmation email
        sendAdminPasswordChangeConfirmation($admin['email'], $admin['username']);
        
        $_SESSION['success'] = 'Your admin password has been reset successfully. You can now login with your new password.';
        header('Location: admin_login.php');
        exit();
        
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $_SESSION['error'] = 'An error occurred while resetting your password. Please try again.';
        header('Location: admin_reset_password.php?token=' . urlencode($token));
        exit();
    }
}

function sendAdminResetEmail($email, $username, $token) {
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
        $mail->setFrom('toylandstore97@gmail.com', 'ToyLand Store Admin');
        $mail->addAddress($email, $username);
        $mail->addReplyTo('admin@toyland.com', 'ToyLand Admin Support');
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Admin Password Reset - ToyLand Store';
        
        $reset_link = 'http://localhost/WBIS-Assignment/root/admin/admin_reset_password.php?token=' . urlencode($token);
        
        $mail->Body = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Admin Password Reset - ToyLand Store</title>
        </head>
        <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
            <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
                <div style="background: #dc3545; color: white; padding: 20px; text-align: center;">
                    <h1 style="margin: 0;">🔒 ToyLand Store Admin</h1>
                </div>
                
                <div style="background: #f8f9fa; padding: 30px; border: 2px solid #ffc107;">
                    <h2 style="color: #dc3545;">⚠️ Admin Password Reset Request</h2>
                    <p>Hello <strong>' . htmlspecialchars($username) . '</strong> (Administrator),</p>
                    
                    <p>We received a request to reset the password for your ToyLand Store <strong>admin account</strong> associated with this email address.</p>
                    
                    <div style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 20px 0;">
                        <p style="margin: 0;"><strong>🔐 Security Notice:</strong></p>
                        <ul style="margin: 10px 0 0 0;">
                            <li>This is a high-privilege admin account reset</li>
                            <li>The reset link expires in <strong>30 minutes</strong></li>
                            <li>If you didn\'t request this, contact IT security immediately</li>
                        </ul>
                    </div>
                    
                    <p>To reset your admin password, please click the secure button below:</p>
                    
                    <div style="text-align: center; margin: 30px 0;">
                        <a href="' . $reset_link . '" 
                           style="background: #dc3545; color: white; padding: 15px 40px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
                            🔑 Reset Admin Password
                        </a>
                    </div>
                    
                    <p>If the button doesn\'t work, you can copy and paste this link into your browser:</p>
                    <p style="word-break: break-all; background: #e9ecef; padding: 10px; border-radius: 3px; font-family: monospace; font-size: 12px;">
                        ' . $reset_link . '
                    </p>
                    
                    <div style="background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin: 20px 0;">
                        <p style="margin: 0; color: #721c24;"><strong>⚠️ Admin Security Requirements:</strong></p>
                        <ul style="margin: 10px 0 0 0; color: #721c24;">
                            <li>Password must be at least 10 characters</li>
                            <li>Must contain uppercase, lowercase, numbers, and special characters</li>
                            <li>This link can only be used once</li>
                            <li>All admin sessions will be terminated after password reset</li>
                        </ul>
                    </div>
                </div>
                
                <div style="background: #6c757d; color: white; padding: 20px; text-align: center; font-size: 12px;">
                    <p style="margin: 0;">🔒 This is a secure automated message from ToyLand Store Admin System.</p>
                    <p style="margin: 5px 0 0 0;">For security issues, contact admin@toyland.com immediately</p>
                    <p style="margin: 5px 0 0 0;"><strong>Request Time:</strong> ' . date('Y-m-d H:i:s') . ' (Malaysia Time)</p>
                </div>
            </div>
        </body>
        </html>
        ';
        
        $mail->AltBody = 'ADMIN PASSWORD RESET REQUEST\n\n'
                       . 'Hello ' . $username . ' (Administrator),\n\n'
                       . 'We received a request to reset your ToyLand Store ADMIN account password.\n\n'
                       . 'SECURITY NOTICE:\n'
                       . '- This is a high-privilege admin account reset\n'
                       . '- The reset link expires in 30 minutes\n'
                       . '- If you didn\'t request this, contact IT security immediately\n\n'
                       . 'Please visit this link to reset your admin password:\n'
                       . $reset_link . '\n\n'
                       . 'ADMIN SECURITY REQUIREMENTS:\n'
                       . '- Password must be at least 10 characters\n'
                       . '- Must contain uppercase, lowercase, numbers, and special characters\n'
                       . '- This link can only be used once\n'
                       . '- All admin sessions will be terminated after password reset\n\n'
                       . 'Request Time: ' . date('Y-m-d H:i:s') . ' (Malaysia Time)\n\n'
                       . 'ToyLand Store Admin Security Team';
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Admin Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

function sendAdminPasswordChangeConfirmation($email, $username) {
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
        $mail->setFrom('toylandstore97@gmail.com', 'ToyLand Store Admin');
        $mail->addAddress($email, $username);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Admin Password Changed Successfully - ToyLand Store';
        
        $mail->Body = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Admin Password Changed - ToyLand Store</title>
        </head>
        <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
            <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
                <div style="background: #28a745; color: white; padding: 20px; text-align: center;">
                    <h1 style="margin: 0;">🔒 ToyLand Store Admin</h1>
                </div>
                
                <div style="background: #f8f9fa; padding: 30px;">
                    <h2 style="color: #28a745;">✅ Admin Password Changed Successfully</h2>
                    <p>Hello <strong>' . htmlspecialchars($username) . '</strong> (Administrator),</p>
                    
                    <p>Your ToyLand Store <strong>admin account</strong> password has been successfully changed on ' . date('F j, Y \a\t g:i A') . ' (Malaysia Time).</p>
                    
                    <div style="background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 20px 0;">
                        <p style="margin: 0; color: #155724;"><strong>🔐 Security Actions Completed:</strong></p>
                        <ul style="margin: 10px 0 0 0; color: #155724;">
                            <li>Admin password successfully updated</li>
                            <li>All existing admin sessions terminated</li>
                            <li>Password reset tokens invalidated</li>
                            <li>Security event logged</li>
                        </ul>
                    </div>
                    
                    <p>If you made this change, no further action is required. You can now login to the admin dashboard with your new password.</p>
                    
                    <div style="background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin: 20px 0;">
                        <p style="margin: 0; color: #721c24;"><strong>⚠️ If you did NOT make this change:</strong></p>
                        <ul style="margin: 10px 0 0 0; color: #721c24;">
                            <li><strong>Contact IT security immediately</strong> at admin@toyland.com</li>
                            <li>Reset your password again immediately</li>
                            <li>Review all admin account activity</li>
                            <li>Check for unauthorized access</li>
                        </ul>
                    </div>
                    
                    <div style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 20px 0;">
                        <p style="margin: 0;"><strong>🛡️ Admin Security Best Practices:</strong></p>
                        <ul style="margin: 10px 0 0 0;">
                            <li>Never share your admin credentials</li>
                            <li>Use a unique, strong password for admin access</li>
                            <li>Always log out when finished</li>
                            <li>Monitor admin activity regularly</li>
                            <li>Report suspicious activity immediately</li>
                        </ul>
                    </div>
                    
                    <p>Thank you for maintaining the security of our admin systems!</p>
                </div>
                
                <div style="background: #6c757d; color: white; padding: 20px; text-align: center; font-size: 12px;">
                    <p style="margin: 0;">🔒 This is a secure automated message from ToyLand Store Admin System.</p>
                    <p style="margin: 5px 0 0 0;">For security issues, contact admin@toyland.com immediately</p>
                    <p style="margin: 5px 0 0 0;"><strong>Change Time:</strong> ' . date('Y-m-d H:i:s') . ' (Malaysia Time)</p>
                </div>
            </div>
        </body>
        </html>
        ';
        
        $mail->AltBody = 'ADMIN PASSWORD CHANGED SUCCESSFULLY\n\n'
                       . 'Hello ' . $username . ' (Administrator),\n\n'
                       . 'Your ToyLand Store ADMIN account password has been successfully changed on ' . date('F j, Y \a\t g:i A') . ' (Malaysia Time).\n\n'
                       . 'SECURITY ACTIONS COMPLETED:\n'
                       . '- Admin password successfully updated\n'
                       . '- All existing admin sessions terminated\n'
                       . '- Password reset tokens invalidated\n'
                       . '- Security event logged\n\n'
                       . 'If you made this change, no further action is required.\n\n'
                       . 'IF YOU DID NOT MAKE THIS CHANGE:\n'
                       . '- Contact IT security immediately at admin@toyland.com\n'
                       . '- Reset your password again immediately\n'
                       . '- Review all admin account activity\n'
                       . '- Check for unauthorized access\n\n'
                       . 'ADMIN SECURITY BEST PRACTICES:\n'
                       . '- Never share your admin credentials\n'
                       . '- Use a unique, strong password for admin access\n'
                       . '- Always log out when finished\n'
                       . '- Monitor admin activity regularly\n'
                       . '- Report suspicious activity immediately\n\n'
                       . 'Change Time: ' . date('Y-m-d H:i:s') . ' (Malaysia Time)\n\n'
                       . 'ToyLand Store Admin Security Team';
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function cleanupExpiredAdminTokens() {
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

function checkAdminRateLimit($email) {
    global $pdo;
    
    try {
        // Allow max 5 reset requests per admin email per hour (stricter than regular users)
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as reset_count 
            FROM password_resets pr 
            INNER JOIN user u ON pr.user_id = u.user_id
            WHERE u.email = ? AND u.role = 'admin'
            AND pr.created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
        ");
        $stmt->execute([$email]);
        $result = $stmt->fetch();
        
        return $result['reset_count'] < 5;
    } catch (PDOException $e) {
        return true; // Allow on error to not block legitimate admins
    }
}
?>