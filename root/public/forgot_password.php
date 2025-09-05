<?php
session_start();

// Page variables
$page_title = "Forgot Password";
$page_description = "Reset your ToyLand Store account password";
$show_breadcrumb = true;
$breadcrumb_items = [
    ['url' => 'login.php', 'title' => 'Login'],
    ['url' => 'forgot_password.php', 'title' => 'Forgot Password']
];

// Include header
include '../includes/header.php';
?>

<!-- Forgot Password Section -->
<section class="login-section">
    <div class="container">
        <div class="login-layout">
            <!-- Forgot Password Form -->
            <div class="login-form-container">
                <div class="form-header">
                    <h1>Forgot Your Password?</h1>
                    <p>Enter your email address and we'll send you a link to reset your password</p>
                    
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
                    <input type="hidden" name="action" value="forgot_password">
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required 
                               placeholder="Enter your registered email address">
                        <i class="fas fa-envelope"></i>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-large">
                        <i class="fas fa-paper-plane"></i> Send Reset Link
                    </button>
                </form>
                
                <!-- Back to Login -->
                <div class="signup-link">
                    <p>Remember your password? <a href="login.php">Sign in here</a></p>
                </div>
            </div>
            
            <!-- Reset Instructions -->
            <div class="login-benefits">
                <div class="benefits-header">
                    <h2>How Password Reset Works</h2>
                    <p>Follow these simple steps to regain access</p>
                </div>
                
                <div class="benefits-list">
                    <div class="benefit-item">
                        <div class="benefit-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="benefit-content">
                            <h3>Step 1: Enter Email</h3>
                            <p>Provide the email address associated with your account</p>
                        </div>
                    </div>
                    
                    <div class="benefit-item">
                        <div class="benefit-icon">
                            <i class="fas fa-link"></i>
                        </div>
                        <div class="benefit-content">
                            <h3>Step 2: Check Your Email</h3>
                            <p>We'll send you a secure reset link to your email</p>
                        </div>
                    </div>
                    
                    <div class="benefit-item">
                        <div class="benefit-icon">
                            <i class="fas fa-key"></i>
                        </div>
                        <div class="benefit-content">
                            <h3>Step 3: Create New Password</h3>
                            <p>Click the link and set your new secure password</p>
                        </div>
                    </div>
                    
                    <div class="benefit-item">
                        <div class="benefit-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="benefit-content">
                            <h3>Step 4: Login</h3>
                            <p>Use your new password to access your account</p>
                        </div>
                    </div>
                </div>
                
                <!-- Security Notice -->
                <div class="customer-testimonial">
                    <div class="testimonial-content">
                        <p><strong>Security Note:</strong> The reset link is valid for 1 hour only and can only be used once for your security.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Help Section -->
<section class="security-notice">
    <div class="container">
        <div class="security-content">
            <div class="security-icon">
                <i class="fas fa-question-circle"></i>
            </div>
            <div class="security-text">
                <h3>Need Help?</h3>
                <p>If you don't receive the reset email within a few minutes, check your spam folder or contact our support team.</p>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
include '../includes/footer.php';
?>