<?php
session_start();
date_default_timezone_set('Asia/Kuala_Lumpur');
require_once '../includes/db.php';

// Set MySQL timezone to match Malaysia
try {
    $pdo->exec("SET time_zone = '+08:00'");
} catch (PDOException $e) {
    error_log("Timezone setting error: " . $e->getMessage());
}

// Check if token is provided
if (!isset($_GET['token']) || empty($_GET['token'])) {
    $_SESSION['error'] = 'Invalid admin reset link. Please request a new password reset.';
    header('Location: admin_forgot_password.php');
    exit();
}

$token = trim($_GET['token']);
$token = urldecode($token);
$valid_token = false;
$admin_email = '';

try {
    // Debug current times
    $stmt = $pdo->prepare("SELECT NOW() as db_time");
    $stmt->execute();
    $time_check = $stmt->fetch();

    
    // Two-step verification instead of problematic JOIN
    // Step 1: Get the reset record
    $stmt = $pdo->prepare("
        SELECT user_id, expires_at, used, 
               NOW() AS `current_time`,
               (expires_at > NOW()) AS not_expired
        FROM password_resets 
        WHERE token = ? AND used = 0
    ");
    $stmt->execute([$token]);
    $reset_record = $stmt->fetch();
    
    if ($reset_record) {
        error_log("ADMIN RESET PAGE - Found reset record:");
        error_log("  Expires: " . $reset_record['expires_at']);
        error_log("  Current: " . $reset_record['current_time']);
        error_log("  Not expired: " . ($reset_record['not_expired'] ? 'YES' : 'NO'));
        
        if ($reset_record['not_expired']) {
            // Step 2: Get admin user record (verify it's an admin)
            $stmt = $pdo->prepare("SELECT email FROM user WHERE user_id = ? AND role = 'admin'");
            $stmt->execute([$reset_record['user_id']]);
            $admin_record = $stmt->fetch();
            
            if ($admin_record) {
                $valid_token = true;
                $admin_email = $admin_record['email'];
                error_log("ADMIN RESET PAGE - Token validation successful for admin");
            } else {
                error_log("ADMIN RESET PAGE - User not found or not admin for user_id: " . $reset_record['user_id']);
            }
        } else {
            error_log("ADMIN RESET PAGE - Token expired");
        }
    } else {
        error_log("ADMIN RESET PAGE - Reset record not found");
    }
    
} catch (PDOException $e) {
    $_SESSION['error'] = 'An error occurred. Please try again.';
    header('Location: admin_forgot_password.php');
    exit();
}

if (!$valid_token) {
    $_SESSION['error'] = 'Invalid or expired admin reset link. Please request a new password reset.';
    header('Location: admin_forgot_password.php');
    exit();
}

// If already logged in as admin, redirect to dashboard
if (isset($_SESSION['admin_id']) && $_SESSION['admin_role'] === 'admin') {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Reset Password - ToyLand Store</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <section class="login-section">
        <div class="container">
            <div class="login-layout">
                <!-- Reset Password Form -->
                <div class="login-form-container">
                    <div class="form-header">
                        <h1>Create New Admin Password</h1>
                        <p>Set a new secure password for <strong><?php echo htmlspecialchars($admin_email); ?></strong></p>
                        
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-error">
                                <i class="fas fa-exclamation-circle"></i>
                                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i>
                                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <form class="login-form" action="admin_password_reset_handler.php" method="POST">
                        <input type="hidden" name="action" value="reset_password">
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                        
                        <div class="form-group">
                            <label for="password">New Admin Password</label>
                            <input type="password" id="password" name="password" required 
                                   minlength="10" placeholder="Enter your new admin password">
                            <i class="fas fa-lock"></i>
                            <button type="button" class="password-toggle">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" required 
                                   minlength="10" placeholder="Confirm your new admin password">
                            <i class="fas fa-lock"></i>
                            <button type="button" class="password-toggle">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        
                        <!-- Admin Password Requirements -->
                        <div class="password-requirements admin-requirements">
                            <h4>Admin Password Requirements:</h4>
                            <ul>
                                <li id="length">At least 10 characters long</li>
                                <li id="uppercase">Contains uppercase letter</li>
                                <li id="lowercase">Contains lowercase letter</li>
                                <li id="number">Contains a number</li>
                                <li id="special">Contains special character (@$!%*?&)</li>
                                <li id="match">Passwords match</li>
                            </ul>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-large" id="reset-btn" disabled>
                            <i class="fas fa-key"></i> Reset Admin Password
                        </button>
                    </form>
                    
                    <!-- Back to Login -->
                    <div class="signup-link">
                        <p>Remember your password? <a href="admin_login.php">Admin Login</a></p>
                    </div>
                </div>
                
                <!-- Security Tips -->
                <div class="login-benefits">
                    <div class="benefits-header">
                        <h2>Admin Security Guidelines</h2>
                        <p>Enhanced security for administrative access</p>
                    </div>
                    
                    <div class="benefits-list">
                        <div class="benefit-item">
                            <div class="benefit-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="benefit-content">
                                <h3>Strong Authentication</h3>
                                <p>Use complex passwords with multiple character types</p>
                            </div>
                        </div>
                        
                        <div class="benefit-item">
                            <div class="benefit-icon">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <div class="benefit-content">
                                <h3>Administrative Privacy</h3>
                                <p>Never share admin credentials with anyone</p>
                            </div>
                        </div>
                        
                        <div class="benefit-item">
                            <div class="benefit-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="benefit-content">
                                <h3>Session Management</h3>
                                <p>Always log out when finished with admin tasks</p>
                            </div>
                        </div>
                        
                        <div class="benefit-item">
                            <div class="benefit-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="benefit-content">
                                <h3>Security Monitoring</h3>
                                <p>Report any suspicious activity immediately</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Admin Security Notice -->
                    <div class="customer-testimonial">
                        <div class="testimonial-content">
                            <p><strong>Admin Security:</strong> After password reset, all existing admin sessions will be terminated and you'll need to log in again.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


<script>
// Password toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const passwordToggles = document.querySelectorAll('.password-toggle');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const resetBtn = document.getElementById('reset-btn');
    
    // Password requirements elements
    const lengthReq = document.getElementById('length');
    const uppercaseReq = document.getElementById('uppercase');
    const lowercaseReq = document.getElementById('lowercase');
    const numberReq = document.getElementById('number');
    const specialReq = document.getElementById('special');
    const matchReq = document.getElementById('match');
    
    passwordToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const passwordInput = this.previousElementSibling;
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
    
    // Admin password validation (stricter requirements)
    function validateAdminPassword() {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        
        let validCount = 0;
        
        // Check length (10 characters for admin)
        if (password.length >= 10) {
            lengthReq.classList.add('valid');
            validCount++;
        } else {
            lengthReq.classList.remove('valid');
        }
        
        // Check uppercase
        if (/[A-Z]/.test(password)) {
            uppercaseReq.classList.add('valid');
            validCount++;
        } else {
            uppercaseReq.classList.remove('valid');
        }
        
        // Check lowercase
        if (/[a-z]/.test(password)) {
            lowercaseReq.classList.add('valid');
            validCount++;
        } else {
            lowercaseReq.classList.remove('valid');
        }
        
        // Check number
        if (/[0-9]/.test(password)) {
            numberReq.classList.add('valid');
            validCount++;
        } else {
            numberReq.classList.remove('valid');
        }
        
        // Check special character
        if (/[@$!%*?&]/.test(password)) {
            specialReq.classList.add('valid');
            validCount++;
        } else {
            specialReq.classList.remove('valid');
        }
        
        // Check match
        if (password.length > 0 && password === confirmPassword) {
            matchReq.classList.add('valid');
            validCount++;
        } else {
            matchReq.classList.remove('valid');
        }
        
        // Enable button if all requirements met (6 requirements for admin)
        resetBtn.disabled = validCount < 6;
    }
    
    passwordInput.addEventListener('input', validateAdminPassword);
    confirmPasswordInput.addEventListener('input', validateAdminPassword);
});
</script>

<style>
.password-requirements {
    margin: 20px 0;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #dc3545;
}

.admin-requirements {
    border-left-color: #dc3545;
    background: #fff5f5;
}

.password-requirements h4 {
    margin: 0 0 10px 0;
    font-size: 14px;
    font-weight: 600;
    color: #dc3545;
}

.password-requirements ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.password-requirements li {
    padding: 5px 0;
    font-size: 13px;
    color: #666;
    position: relative;
    padding-left: 25px;
}

.password-requirements li:before {
    content: "✗";
    position: absolute;
    left: 0;
    color: #dc3545;
    font-weight: bold;
}

.password-requirements li.valid {
    color: #28a745;
}

.password-requirements li.valid:before {
    content: "✓";
    color: #28a745;
}

#reset-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.admin-requirements li {
    font-weight: 500;
}

.customer-testimonial {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    border-radius: 8px;
}

.customer-testimonial .testimonial-content p {
    color: #856404;
    font-weight: 500;
}
</style>

</body>
</html>