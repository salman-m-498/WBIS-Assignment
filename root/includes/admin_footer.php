<!-- Admin Footer -->
    <footer class="admin-footer">
        <div class="container">
            <div class="admin-footer-content">
                <div class="admin-footer-left">
                    <div class="admin-footer-section">
                        <h4>Administration</h4>
                        <ul class="admin-footer-links">
                            <li><a href="dashboard.php">Dashboard</a></li>
                            <li><a href="members.php">Manage Members</a></li>
                            <li><a href="reviews.php">Manage Reviews</a></li>
                            <li><a href="orders.php">Manage Orders</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="admin-footer-center">
                    <div class="admin-footer-section">
                        <h4>Quick Actions</h4>
                        <ul class="admin-footer-links">
                            <li><a href="products.php?action=add">Add Product</a></li>
                            <li><a href="categories.php?action=add">Add Category</a></li>
                            <li><a href="vouchers.php?action=add">Create Voucher</a></li>
                             <li><a href="sales_analytics.php">Sales Analytics</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="admin-footer-right">
                    <div class="admin-footer-section">
                        <h4>System Status</h4>
                        <div class="system-status">
                            <div class="status-item">
                                <span class="status-indicator online"></span>
                                <span>Database: Online</span>
                            </div>
                            <div class="status-item">
                                <span class="status-indicator online"></span>
                                <span>Server: Running</span>
                            </div>
                            <div class="status-item">
                                <span class="status-indicator online"></span>
                                <span>Cache: Active</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="admin-footer-bottom">
                <div class="admin-footer-bottom-left">
                    <p>&copy; <?php echo date('Y'); ?> ToyLand Admin Panel. All rights reserved.</p>
                    <p class="version-info">Version 2.1.0 | Last Updated: <?php echo date('M d, Y'); ?></p>
                </div>
                <div class="admin-footer-bottom-right">
                    <div class="admin-session-info">
                        <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                        <span><i class="fas fa-clock"></i> Session: <?php echo date('H:i:s'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Admin Back to Top Button -->
    <button id="admin-back-to-top" class="admin-back-to-top">
        <i class="fas fa-chevron-up"></i>
    </button>
    
    <?php if (isset($page_scripts)): ?>
        <?php foreach ($page_scripts as $script): ?>
            <script src="<?php echo $script; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

    <script>
    // Admin-specific JavaScript
    $(document).ready(function() {
        // Admin back to top functionality
        $('#admin-back-to-top').click(function() {
            $('html, body').animate({scrollTop: 0}, 500);
        });
        
        $(window).scroll(function() {
            if ($(this).scrollTop() > 200) {
                $('#admin-back-to-top').fadeIn();
            } else {
                $('#admin-back-to-top').fadeOut();
            }
        });
        
        // Auto-refresh session timer
        function refreshSession() {
            $.post('refresh_session.php', function(data) {
                console.log('Session refreshed');
            });
        }
        
        // Refresh session every 5 minutes
        setInterval(refreshSession, 300000);
        
        // Admin navigation active state
        const currentPage = '<?php echo basename($_SERVER['PHP_SELF']); ?>';
        $('.admin-nav-menu li').removeClass('active');
        $('.admin-nav-menu li a[href*="' + currentPage + '"]').parent().addClass('active');
        
        // Confirm dangerous actions
        $('.admin-delete, .admin-danger').click(function(e) {
            if (!confirm('Are you sure you want to perform this action? This cannot be undone.')) {
                e.preventDefault();
            }
        });
        
        // Auto-save drafts (if applicable)
        let autoSaveTimer;
        $('textarea, input[type="text"], input[type="email"]').on('input', function() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(function() {
                // Auto-save logic here if needed
                console.log('Auto-saving...');
            }, 2000);
        });
        
        // Show notification count updates
        function updateNotificationCount() {
            $.get('get_notification_count.php', function(count) {
                $('.notification-badge').text(count);
                if (count > 0) {
                    $('.notification-badge').show();
                } else {
                    $('.notification-badge').hide();
                }
            });
        }
        
        // Update notification count every 30 seconds
        setInterval(updateNotificationCount, 30000);
    });
    </script>
</body>
</html>