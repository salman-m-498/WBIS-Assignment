<?php
session_start();

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
    <title>Admin Forgot Password - ToyLand Store</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <section class="login-section">
        <div class="container">
            <div class="login-layout">
                <!-- Forgot Password Form -->
                <div class="login-form-container">
                    <div class="form-header">
                        <h1>Admin Password Recovery</h1>
                        <p>Enter your admin email address and we'll send you a secure reset link</p>
                        
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
                        <input type="hidden" name="action" value="forgot_password">
                        
                        <div class="form-group">
                            <label for="email">Admin Email Address</label>
                            <input type="email" id="email" name="email" required 
                                   placeholder="Enter your admin email address">
                            <i class="fas fa-envelope"></i>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-large">
                            <i class="fas fa-paper-plane"></i> Send Reset Link
                        </button>
                    </form>
                    
                    <!-- Back to Login -->
                    <div class="signup-link">
                        <p>Remember your password? <a href="admin_login.php">Admin Login</a></p>
                    </div>
                </div>
                
                <!-- Reset Instructions -->
                <div class="login-benefits">
                    <div class="benefits-header">
                        <h2>Admin Password Reset Process</h2>
                        <p>Secure recovery steps for administrators</p>
                    </div>
                    
                    <div class="benefits-list">
                        <div class="benefit-item">
                            <div class="benefit-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="benefit-content">
                                <h3>Step 1: Verify Email</h3>
                                <p>Provide the email address associated with your admin account</p>
                            </div>
                        </div>
                        
                        <div class="benefit-item">
                            <div class="benefit-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="benefit-content">
                                <h3>Step 2: Secure Link</h3>
                                <p>We'll send you an encrypted reset link to your email</p>
                            </div>
                        </div>
                        
                        <div class="benefit-item">
                            <div class="benefit-icon">
                                <i class="fas fa-key"></i>
                            </div>
                            <div class="benefit-content">
                                <h3>Step 3: New Password</h3>
                                <p>Click the link and create your new secure password</p>
                            </div>
                        </div>
                        
                        <div class="benefit-item">
                            <div class="benefit-icon">
                                <i class="fas fa-sign-in-alt"></i>
                            </div>
                            <div class="benefit-content">
                                <h3>Step 4: Admin Access</h3>
                                <p>Use your new password to access the admin dashboard</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Security Notice -->
                    <div class="customer-testimonial">
                        <div class="testimonial-content">
                            <p><strong>Security Note:</strong> Admin reset links are valid for 30 minutes only and include additional security verification for your protection.</p>
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
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="security-text">
                    <h3>Need Assistance?</h3>
                    <p>If you don't receive the reset email within a few minutes, check your spam folder or contact the system administrator.</p>
                </div>
            </div>
        </div>
    </section>
</body>
</html>