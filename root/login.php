<?php
session_start();

// Page variables
$page_title = "Login";
$page_description = "Sign in to your ToyLand Store account";
$show_breadcrumb = true;
$breadcrumb_items = [
    ['url' => 'login.php', 'title' => 'Login']
];

// Include header
include 'includes/header.php';
?>

<!-- Login Section -->
<section class="login-section">
    <div class="container">
        <div class="login-layout">
            <!-- Login Form -->
            <div class="login-form-container">
                <div class="form-header">
                    <h1>Welcome Back</h1>
                    <p>Sign in to your account to continue shopping</p>
                </div>
                
                <form class="login-form" action="auth.php" method="POST">
                    <input type="hidden" name="action" value="login">
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required>
                        <i class="fas fa-envelope"></i>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                        <i class="fas fa-lock"></i>
                        <button type="button" class="password-toggle">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    
                    <div class="form-options">
                        <label class="checkbox-label">
                            <input type="checkbox" name="remember" id="remember">
                            <span class="checkmark"></span>
                            Remember me
                        </label>
                        <a href="forgot-password.php" class="forgot-password">Forgot Password?</a>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-large">
                        <i class="fas fa-sign-in-alt"></i> Sign In
                    </button>
                </form>
                
                <!-- Social Login -->
                <div class="social-login">
                    <div class="divider">
                        <span>or continue with</span>
                    </div>
                    
                    <div class="social-buttons">
                        <button class="btn btn-social btn-google">
                            <i class="fab fa-google"></i>
                            Continue with Google
                        </button>
                        <button class="btn btn-social btn-facebook">
                            <i class="fab fa-facebook-f"></i>
                            Continue with Facebook
                        </button>
                    </div>
                </div>
                
                <!-- Sign Up Link -->
                <div class="signup-link">
                    <p>Don't have an account? <a href="register.php">Sign up here</a></p>
                </div>
            </div>
            
            <!-- Login Benefits -->
            <div class="login-benefits">
                <div class="benefits-header">
                    <h2>Why Create an Account?</h2>
                    <p>Join thousands of happy customers</p>
                </div>
                
                <div class="benefits-list">
                    <div class="benefit-item">
                        <div class="benefit-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="benefit-content">
                            <h3>Quick Checkout</h3>
                            <p>Save your information for faster purchases</p>
                        </div>
                    </div>
                    
                    <div class="benefit-item">
                        <div class="benefit-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div class="benefit-content">
                            <h3>Wishlist</h3>
                            <p>Save your favorite items for later</p>
                        </div>
                    </div>
                    
                    <div class="benefit-item">
                        <div class="benefit-icon">
                            <i class="fas fa-truck"></i>
                        </div>
                        <div class="benefit-content">
                            <h3>Order Tracking</h3>
                            <p>Track your orders and view order history</p>
                        </div>
                    </div>
                    
                    <div class="benefit-item">
                        <div class="benefit-icon">
                            <i class="fas fa-gift"></i>
                        </div>
                        <div class="benefit-content">
                            <h3>Exclusive Offers</h3>
                            <p>Get access to member-only discounts and promotions</p>
                        </div>
                    </div>
                </div>
                
                <!-- Customer Testimonial -->
                <div class="customer-testimonial">
                    <div class="testimonial-content">
                        <p>"I love how easy it is to shop and track my orders. The customer service is amazing!"</p>
                        <div class="testimonial-author">
                            <img src="assets/images/customer-1.jpg" alt="Sarah Johnson">
                            <div>
                                <h4>Sarah Johnson</h4>
                                <span>Verified Customer</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Security Notice -->
<section class="security-notice">
    <div class="container">
        <div class="security-content">
            <div class="security-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div class="security-text">
                <h3>Your Security is Our Priority</h3>
                <p>We use industry-standard encryption to protect your personal information. Your data is never shared with third parties.</p>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?> 