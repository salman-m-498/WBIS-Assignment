<!-- Footer -->
<footer class="main-footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h3>About ToyLand Store</h3>
                <p>We're passionate about bringing joy and imagination to children of all ages. Our carefully curated selection of toys and games ensures quality, safety, and endless fun for your little ones.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                    <li><a href="faq.php">FAQ</a></li>
                </ul>
            </div>
            
            <?php if (isset($_SESSION['user_id'])): ?>
            <div class="footer-section">
                <h3>Member</h3>
            <ul>
                <li><a href="/member/orders.php">My Orders</a></li>
                <li><a href="/member/wishlist.php">Wishlist</a></li>
                <li><a href="/member/reviews.php">My Reviews</a></li>
                <li><a href="/member/profile.php">Profile Settings</a></li>
            </ul>
            </div>
            <?php endif; ?>
            <div class="footer-section">
                <h3>Newsletter</h3>
                <p>Subscribe to get special offers, free giveaways, and once-in-a-lifetime deals.</p>
                <form class="newsletter-form" action="/public/newsletter.php" method="POST">
                    <input type="email" name="email" placeholder="Enter your email" required>
                    <button type="submit">Subscribe</button>
                </form>
            </div>
        </div>
        
        <div class="footer-bottom">
            <div class="footer-bottom-left">
                <p>&copy; <?php echo date('Y'); ?> ToyLand Store. All rights reserved.</p>
            </div>
            <div class="footer-bottom-right">
                    <div class="payment-methods">
                    <h4>We Accept:</h4>
                   <div class="payment-icons">
                        <img src="https://img.icons8.com/color/48/visa.png" alt="Visa">
                        <img src="https://img.icons8.com/color/48/mastercard.png" alt="Mastercard">
                        <img src="https://img.icons8.com/color/48/amex.png" alt="Amex">
                         <img
                            src="https://logowik.com/content/uploads/images/touchn-go-ewallet4107.logowik.com.webp"
                            alt="Touch 'n Go eWallet"
                        >
                        </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Back to Top Button -->
<button id="back-to-top" class="back-to-top">
    <i class="fas fa-chevron-up"></i>
</button>

<!-- JavaScript -->
<script src="/assets/js/toyland.js"></script>
<script src="/assets/js/cart.js"></script>

<?php if (isset($page_scripts)): ?>
    <?php foreach ($page_scripts as $script): ?>
        <script src="<?php echo $script; ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (isset($_SESSION['flash_message'])): ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        showFlashMessage(
            <?= json_encode($_SESSION['flash_message']['text']) ?>,
            <?= json_encode($_SESSION['flash_message']['type']) ?>
        );
    });
</script>
<?php unset($_SESSION['flash_message']); endif; ?>

</body>
</html>
