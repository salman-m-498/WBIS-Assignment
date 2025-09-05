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
    $_SESSION['error'] = 'Invalid reset link. Please request a new password reset.';
    header('Location: forgot_password.php');
    exit();
}

$token = trim($_GET['token']);
$token = urldecode($token);
$valid_token = false;
$user_email = '';

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
        error_log("RESET PAGE - Found reset record:");
        error_log("  Expires: " . $reset_record['expires_at']);
        error_log("  Current: " . $reset_record['current_time']);
        error_log("  Not expired: " . ($reset_record['not_expired'] ? 'YES' : 'NO'));
        
        if ($reset_record['not_expired']) {
            // Step 2: Get user record
            $stmt = $pdo->prepare("SELECT email FROM user WHERE user_id = ?");
            $stmt->execute([$reset_record['user_id']]);
            $user_record = $stmt->fetch();
            
            if ($user_record) {
                $valid_token = true;
                $user_email = $user_record['email'];
                error_log("RESET PAGE - Token validation successful");
            } else {
                error_log("RESET PAGE - User not found for user_id: " . $reset_record['user_id']);
            }
        } else {
            error_log("RESET PAGE - Token expired");
        }
    } else {
        error_log("RESET PAGE - Reset record not found");
    }
    
} catch (PDOException $e) {
    $_SESSION['error'] = 'An error occurred. Please try again.';
    header('Location: forgot_password.php');
    exit();
}

if (!$valid_token) {
    $_SESSION['error'] = 'Invalid or expired reset link. Please request a new password reset.';
    header('Location: forgot_password.php');
    exit();
}

// Page variables
$page_title = "Reset Password";
$page_description = "Create a new password for your ToyLand Store account";
$show_breadcrumb = true;
$breadcrumb_items = [
    ['url' => 'login.php', 'title' => 'Login'],
    ['url' => 'reset_password.php', 'title' => 'Reset Password']
];

// Include header
include '../includes/header.php';
?>

<!-- Reset Password Section -->
<section class="login-section">
    <div class="container">
        <div class="login-layout">
            <!-- Reset Password Form -->
            <div class="login-form-container">
                <div class="form-header">
                    <h1>Create New Password</h1>
                    <p>Enter your new password for <strong><?php echo htmlspecialchars($user_email); ?></strong></p>
                    
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
                
                <form class="login-form" action="password_reset_handler.php" method="POST">
                    <input type="hidden" name="action" value="reset_password">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    
                    <div class="form-group">
                        <label for="password">New Password</label>
                        <input type="password" id="password" name="password" required 
                               minlength="8" placeholder="Enter your new password">
                        <i class="fas fa-lock"></i>
                        <button type="button" class="password-toggle">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required 
                               minlength="8" placeholder="Confirm your new password">
                        <i class="fas fa-lock"></i>
                        <button type="button" class="password-toggle">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    
                    <!-- Password Requirements -->
                    <div class="password-requirements">
                        <h4>Password Requirements:</h4>
                        <ul>
                            <li id="length">At least 8 characters long</li>
                            <li id="uppercase">Contains uppercase letter</li>
                            <li id="lowercase">Contains lowercase letter</li>
                            <li id="number">Contains a number</li>
                            <li id="match">Passwords match</li>
                        </ul>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-large" id="reset-btn" disabled>
                        <i class="fas fa-key"></i> Reset Password
                    </button>
                </form>
                
                <!-- Back to Login -->
                <div class="signup-link">
                    <p>Remember your password? <a href="login.php">Sign in here</a></p>
                </div>
            </div>
            
            <!-- Security Tips -->
            <div class="login-benefits">
                <div class="benefits-header">
                    <h2>Password Security Tips</h2>
                    <p>Create a strong and secure password</p>
                </div>
                
                <div class="benefits-list">
                    <div class="benefit-item">
                        <div class="benefit-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="benefit-content">
                            <h3>Use Strong Passwords</h3>
                            <p>Combine uppercase, lowercase, numbers, and symbols</p>
                        </div>
                    </div>
                    
                    <div class="benefit-item">
                        <div class="benefit-icon">
                            <i class="fas fa-user-secret"></i>
                        </div>
                        <div class="benefit-content">
                            <h3>Keep It Private</h3>
                            <p>Never share your password with anyone</p>
                        </div>
                    </div>
                    
                    <div class="benefit-item">
                        <div class="benefit-icon">
                            <i class="fas fa-sync-alt"></i>
                        </div>
                        <div class="benefit-content">
                            <h3>Update Regularly</h3>
                            <p>Change your password periodically for better security</p>
                        </div>
                    </div>
                    
                    <div class="benefit-item">
                        <div class="benefit-icon">
                            <i class="fas fa-ban"></i>
                        </div>
                        <div class="benefit-content">
                            <h3>Avoid Common Words</h3>
                            <p>Don't use dictionary words or personal information</p>
                        </div>
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
    
    // Password validation
    function validatePassword() {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        
        let validCount = 0;
        
        // Check length
        if (password.length >= 8) {
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
        
        // Check match
        if (password.length > 0 && password === confirmPassword) {
            matchReq.classList.add('valid');
            validCount++;
        } else {
            matchReq.classList.remove('valid');
        }
        
        // Enable button if all requirements met
        resetBtn.disabled = validCount < 5;
    }
    
    passwordInput.addEventListener('input', validatePassword);
    confirmPasswordInput.addEventListener('input', validatePassword);
});
</script>

<style>
.password-requirements {
    margin: 20px 0;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #007bff;
}

.password-requirements h4 {
    margin: 0 0 10px 0;
    font-size: 14px;
    font-weight: 600;
    color: #333;
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
</style>

<?php
// Include footer
include '../includes/footer.php';
?>