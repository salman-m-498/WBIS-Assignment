<?php
session_start();

// Page variables
$page_title = "Register";
$page_description = "Create your ToyLand Store account";
$show_breadcrumb = true;
$breadcrumb_items = [
    ['url' => 'register.php', 'title' => 'Register']
];

// Include header
include 'includes/header.php';
?>

<!-- Register Section -->
<section class="register-section">
    <div class="container">
        <div class="register-layout">
            <!-- Register Form -->
            <div class="register-form-container">
                <div class="form-header">
                    <h1>Create Your Account</h1>
                    <p>Join our community and start shopping today</p>
                </div>
                
                <form class="register-form" action="auth.php" method="POST">
                    <input type="hidden" name="action" value="register">
                    
                    <!-- Personal Information -->
                    <div class="form-section">
                        <h3>Personal Information</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="first_name">First Name</label>
                                <input type="text" id="first_name" name="first_name" required>
                                <i class="fas fa-user"></i>
                            </div>
                            
                            <div class="form-group">
                                <label for="last_name">Last Name</label>
                                <input type="text" id="last_name" name="last_name" required>
                                <i class="fas fa-user"></i>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" required>
                            <i class="fas fa-envelope"></i>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" required>
                            <i class="fas fa-phone"></i>
                        </div>
                        
                        <div class="form-group">
                            <label for="date_of_birth">Date of Birth</label>
                            <input type="date" id="date_of_birth" name="date_of_birth" required>
                            <i class="fas fa-calendar"></i>
                        </div>
                    </div>
                    
                    <!-- Account Security -->
                    <div class="form-section">
                        <h3>Account Security</h3>
                        
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required>
                            <i class="fas fa-lock"></i>
                            <button type="button" class="password-toggle">
                                <i class="fas fa-eye"></i>
                            </button>
                            <div class="password-strength">
                                <div class="strength-bar">
                                    <div class="strength-fill" id="strength-fill"></div>
                                </div>
                                <span class="strength-text" id="strength-text">Password strength</span>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                            <i class="fas fa-lock"></i>
                            <button type="button" class="password-toggle">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        
                        <div class="password-requirements">
                            <h4>Password Requirements:</h4>
                            <ul>
                                <li id="req-length"><i class="fas fa-circle"></i> At least 8 characters</li>
                                <li id="req-uppercase"><i class="fas fa-circle"></i> One uppercase letter</li>
                                <li id="req-lowercase"><i class="fas fa-circle"></i> One lowercase letter</li>
                                <li id="req-number"><i class="fas fa-circle"></i> One number</li>
                                <li id="req-special"><i class="fas fa-circle"></i> One special character</li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Address Information -->
                    <div class="form-section">
                        <h3>Address Information</h3>
                        
                        <div class="form-group">
                            <label for="address_line1">Address Line 1</label>
                            <input type="text" id="address_line1" name="address_line1" required>
                            <i class="fas fa-home"></i>
                        </div>
                        
                        <div class="form-group">
                            <label for="address_line2">Address Line 2 (Optional)</label>
                            <input type="text" id="address_line2" name="address_line2">
                            <i class="fas fa-home"></i>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="city">City</label>
                                <input type="text" id="city" name="city" required>
                                <i class="fas fa-city"></i>
                            </div>
                            
                            <div class="form-group">
                                <label for="state">State/Province</label>
                                <input type="text" id="state" name="state" required>
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="postal_code">Postal Code</label>
                                <input type="text" id="postal_code" name="postal_code" required>
                                <i class="fas fa-mail-bulk"></i>
                            </div>
                            
                            <div class="form-group">
                                <label for="country">Country</label>
                                <select id="country" name="country" required>
                                    <option value="">Select Country</option>
                                    <option value="US">United States</option>
                                    <option value="CA">Canada</option>
                                    <option value="UK">United Kingdom</option>
                                    <option value="AU">Australia</option>
                                    <option value="DE">Germany</option>
                                    <option value="FR">France</option>
                                    <option value="JP">Japan</option>
                                </select>
                                <i class="fas fa-globe"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Preferences -->
                    <div class="form-section">
                        <h3>Preferences</h3>
                        
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="newsletter" id="newsletter">
                                <span class="checkmark"></span>
                                Subscribe to our newsletter for updates and special offers
                            </label>
                        </div>
                        
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="marketing" id="marketing">
                                <span class="checkmark"></span>
                                Receive promotional emails and offers
                            </label>
                        </div>
                    </div>
                    
                    <!-- Terms and Conditions -->
                    <div class="form-section">
                        <div class="form-group">
                            <label class="checkbox-label required">
                                <input type="checkbox" name="terms" id="terms" required>
                                <span class="checkmark"></span>
                                I agree to the <a href="terms.php" target="_blank">Terms and Conditions</a> and <a href="privacy.php" target="_blank">Privacy Policy</a>
                            </label>
                        </div>
                        
                        <div class="form-group">
                            <label class="checkbox-label required">
                                <input type="checkbox" name="age_verification" id="age_verification" required>
                                <span class="checkmark"></span>
                                I confirm that I am at least 13 years old
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-large">
                        <i class="fas fa-user-plus"></i> Create Account
                    </button>
                </form>
                
                <!-- Social Registration -->
                <div class="social-register">
                    <div class="divider">
                        <span>or sign up with</span>
                    </div>
                    
                    <div class="social-buttons">
                        <button class="btn btn-social btn-google">
                            <i class="fab fa-google"></i>
                            Sign up with Google
                        </button>
                        <button class="btn btn-social btn-facebook">
                            <i class="fab fa-facebook-f"></i>
                            Sign up with Facebook
                        </button>
                    </div>
                </div>
                
                <!-- Login Link -->
                <div class="login-link">
                    <p>Already have an account? <a href="login.php">Sign in here</a></p>
                </div>
            </div>
            
            <!-- Registration Benefits -->
            <div class="registration-benefits">
                <div class="benefits-header">
                    <h2>Join Our Community</h2>
                    <p>Get exclusive benefits and rewards</p>
                </div>
                
                <div class="benefits-list">
                    <div class="benefit-item">
                        <div class="benefit-icon">
                            <i class="fas fa-percentage"></i>
                        </div>
                        <div class="benefit-content">
                            <h3>Member Discounts</h3>
                            <p>Get 10% off your first order and exclusive member-only deals</p>
                        </div>
                    </div>
                    
                    <div class="benefit-item">
                        <div class="benefit-icon">
                            <i class="fas fa-shipping-fast"></i>
                        </div>
                        <div class="benefit-content">
                            <h3>Free Shipping</h3>
                            <p>Free shipping on all orders over $50 for members</p>
                        </div>
                    </div>
                    
                    <div class="benefit-item">
                        <div class="benefit-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="benefit-content">
                            <h3>Rewards Program</h3>
                            <p>Earn points on every purchase and redeem for rewards</p>
                        </div>
                    </div>
                    
                    <div class="benefit-item">
                        <div class="benefit-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <div class="benefit-content">
                            <h3>Priority Support</h3>
                            <p>Get priority customer service and faster response times</p>
                        </div>
                    </div>
                </div>
                
                <!-- Trust Indicators -->
                <div class="trust-indicators">
                    <h3>Why Trust Us?</h3>
                    <div class="trust-stats">
                        <div class="trust-stat">
                            <span class="stat-number">50,000+</span>
                            <span class="stat-label">Happy Customers</span>
                        </div>
                        <div class="trust-stat">
                            <span class="stat-number">4.8/5</span>
                            <span class="stat-label">Customer Rating</span>
                        </div>
                        <div class="trust-stat">
                            <span class="stat-number">24/7</span>
                            <span class="stat-label">Support</span>
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
                <h3>Your Data is Protected</h3>
                <p>We use bank-level encryption to secure your personal information. Your privacy is our top priority.</p>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?> 