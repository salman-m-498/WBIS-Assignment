<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

$user_id = $_SESSION['user_id'] ?? null;

// Handle voucher collection
if ($user_id && isset($_POST['collect_voucher'])) {
    $voucher_id = $_POST['voucher_id'];
    
    try {
        // Check if user already has this voucher
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM user_vouchers WHERE user_id = ? AND voucher_id = ?");
        $checkStmt->execute([$user_id, $voucher_id]);
        $hasVoucher = $checkStmt->fetchColumn() > 0;
        
        if ($hasVoucher) {
            $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'You already have this voucher!'];
        } else {
            // Check voucher limits
            $voucherStmt = $pdo->prepare("
                SELECT v.*, 
                       (SELECT COUNT(*) FROM user_vouchers WHERE voucher_id = v.voucher_id) as current_usage
                FROM vouchers v 
                WHERE v.voucher_id = ? AND v.status = 'active' 
                AND v.start_date <= NOW() AND v.end_date >= NOW()
            ");
            $voucherStmt->execute([$voucher_id]);
            $voucher = $voucherStmt->fetch();
            
            if (!$voucher) {
                $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Voucher is not available!'];
            } elseif ($voucher['usage_limit'] > 0 && $voucher['current_usage'] >= $voucher['usage_limit']) {
                $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Voucher usage limit exceeded!'];
            } else {

                // Generate user_voucher_id and collect voucher
                $user_voucher_id = generateNextId($pdo, "user_vouchers", "user_voucher_id", "UV", 11);
                
                $insertStmt = $pdo->prepare("
                    INSERT INTO user_vouchers (user_voucher_id, user_id, voucher_id, collected_at) 
                    VALUES (?, ?, ?, NOW())
                ");
                $insertStmt->execute([$user_voucher_id, $user_id, $voucher_id]);
                
                $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Voucher collected successfully!'];
            }
        }
    } catch (Exception $e) {
        $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Error collecting voucher: ' . $e->getMessage()];
    }
    
    header('Location: vouchers.php');
    exit;
}

// Get available vouchers
$stmt = $pdo->prepare("
    SELECT v.*, 
           (SELECT COUNT(*) FROM user_vouchers WHERE voucher_id = v.voucher_id) as current_usage
    FROM vouchers v
    WHERE v.status = 'active' 
      AND v.start_date <= NOW() 
      AND v.end_date >= NOW()
      AND NOT EXISTS (
          SELECT 1 FROM user_vouchers uv 
          WHERE uv.voucher_id = v.voucher_id AND uv.user_id = ?
      )
    ORDER BY v.created_at DESC
");
$stmt->execute([$user_id]);
$vouchers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get user's collected vouchers
$myVouchersStmt = $pdo->prepare("
    SELECT v.*, uv.collected_at, uv.used_at
    FROM user_vouchers uv
    JOIN vouchers v ON uv.voucher_id = v.voucher_id
    WHERE uv.user_id = ?
    ORDER BY uv.collected_at DESC
");
$myVouchersStmt->execute([$user_id]);
$myVouchers = $myVouchersStmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = "Vouchers";
$page_description = "Collect and manage your vouchers";
$show_breadcrumb = true;
$breadcrumb_items = [
    ['url' => 'vouchers.php', 'title' => 'Vouchers']
];

include '../includes/header.php';
?>

<!-- Flash message container -->
<div id="flash-message" class="flash-message"></div>

<section class="vouchers-section">
    <div class="container">
        <div class="vouchers-header">
            <h1>Vouchers</h1>
            <p>Collect vouchers and save on your orders</p>
        </div>

        <!-- Tabs -->
        <div class="vouchers-tabs">
            <button class="tab-btn active" data-tab="available">Available Vouchers</button>
            <button class="tab-btn" data-tab="my-vouchers">My Vouchers</button>
        </div>

        <!-- Available Vouchers Tab -->
        <div class="tab-content active" id="available-tab">
            <?php if (empty($vouchers)): ?>
                <div class="empty-state">
                    <i class="fas fa-ticket-alt"></i>
                    <h3>No vouchers available</h3>
                    <p>Check back later for new vouchers!</p>
                </div>
            <?php else: ?>
                <div class="vouchers-grid">
                    <?php foreach ($vouchers as $voucher): ?>
                        <?php
                        $canCollect = ($voucher['usage_limit'] == 0 || $voucher['current_usage'] < $voucher['usage_limit']);
                        $discountText = $voucher['discount_type'] == 'percentage' 
                            ? $voucher['discount_value'] . '% OFF' 
                            : 'RM' . number_format($voucher['discount_value'], 2) . ' OFF';
                        ?>
                        <div class="voucher-card available">
                             <div class="voucher-header">
                                <div class="voucher-code"><?= htmlspecialchars($voucher['code']) ?></div>
                                <div class="voucher-badge available">
                                    Available
                                </div>
                            </div>
                            
                            <div class="discount-amount"><?= $discountText ?></div>
                            
                            <div class="voucher-description">
                                <?= htmlspecialchars($voucher['description']) ?>
                            </div>
                            
                            <div class="voucher-details">
                                <div class="detail-item">
                                    <span class="detail-label">Min Order</span>
                                    <span class="detail-value">RM<?= number_format($voucher['min_order_amount'], 2) ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Per User Limit</span>
                                    <span class="detail-value"><?= $voucher['per_user_limit'] ?></span>
                                </div>
                                <?php if ($voucher['usage_limit'] > 0): ?>
                                <div class="detail-item">
                                    <span class="detail-label">Remaining</span>
                                    <span class="detail-value"><?= $voucher['usage_limit'] - $voucher['current_usage'] ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="voucher-expiry">
                                Expires: <?= date('M j, Y', strtotime($voucher['end_date'])) ?>
                            </div>
                            
                            <?php if ($canCollect): ?>
                                <?php if ($user_id): ?>
                                <form method="post" style="margin: 0;">
                                    <input type="hidden" name="voucher_id" value="<?= $voucher['voucher_id'] ?>">
                                    <button type="submit" name="collect_voucher" class="collect-btn">
                                        Collect Voucher
                                    </button>
                                </form>
                                <?php else: ?>
                                    <!-- Guest: show login button -->
                                    <a href="login.php?redirect=vouchers.php" class="collect-btn">
                                        Login to collect
                                    </a>
                                <?php endif; ?>
                            <?php else: ?>
                                <button class="collect-btn" disabled>
                                    Not Available
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- My Vouchers Tab -->
        <div class="tab-content" id="my-vouchers-tab">
            <?php if (empty($myVouchers)): ?>
                <div class="empty-state">
                    <i class="fas fa-wallet"></i>
                    <h3>No vouchers collected</h3>
                    <p>Start collecting vouchers to save on your orders!</p>
                </div>
            <?php else: ?>
                <div class="vouchers-grid">
                    <?php foreach ($myVouchers as $voucher): ?>
                        <?php
                        $isUsed = !empty($voucher['used_at']);
                        $isExpired = strtotime($voucher['end_date']) < time();
                        
                        $discountText = $voucher['discount_type'] == 'percentage' 
                            ? $voucher['discount_value'] . '% OFF' 
                            : 'RM' . number_format($voucher['discount_value'], 2) . ' OFF';
                        ?>
                        <div class="voucher-card <?= $isUsed ? 'used' : ($isExpired ? 'used' : 'collected') ?>">
                            <div class="voucher-header">
                                <div class="voucher-code"><?= htmlspecialchars($voucher['code']) ?></div>
                                <div class="voucher-badge <?= $isUsed ? 'used' : ($isExpired ? 'used' : 'collected') ?>">
                                    <?= $isUsed ? 'Used' : ($isExpired ? 'Expired' : 'Collected') ?>
                                </div>
                            </div>
                            
                            <div class="discount-amount"><?= $discountText ?></div>
                            
                            <div class="voucher-description">
                                <?= htmlspecialchars($voucher['description']) ?>
                            </div>
                            
                            <div class="voucher-details">
                                <div class="detail-item">
                                    <span class="detail-label">Min Order</span>
                                    <span class="detail-value">RM<?= number_format($voucher['min_order_amount'], 2) ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Collected</span>
                                    <span class="detail-value"><?= date('M j, Y', strtotime($voucher['collected_at'])) ?></span>
                                </div>
                            </div>
                            
                            <?php if ($isUsed): ?>
                                <div class="voucher-expiry">
                                    Used: <?= date('M j, Y', strtotime($voucher['used_at'])) ?>
                                </div>
                            <?php elseif ($isExpired): ?>
                                <div class="voucher-expiry">
                                    Expired: <?= date('M j, Y', strtotime($voucher['end_date'])) ?>
                                </div>
                            <?php else: ?>
                                <div class="voucher-expiry">
                                    Expires: <?= date('M j, Y', strtotime($voucher['end_date'])) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab functionality
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const tabName = btn.dataset.tab;
            
            // Update tab buttons
            tabBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            // Update tab contents
            tabContents.forEach(content => {
                content.classList.remove('active');
                if (content.id === `${tabName}-tab`) {
                    content.classList.add('active');
                }
            });
        });
    });
    
    // Flash message handling
    <?php if (isset($_SESSION['flash_message'])): ?>
        showFlashMessage(
            '<?= addslashes($_SESSION['flash_message']['message']) ?>',
            '<?= $_SESSION['flash_message']['type'] ?>'
        );
        <?php unset($_SESSION['flash_message']); ?>
    <?php endif; ?>
    
    function showFlashMessage(message, type = 'success') {
        const flash = document.getElementById('flash-message');
        if (!flash) return;
        
        flash.textContent = message;
        flash.className = `flash-message ${type}`;
        flash.style.display = 'block';
        
        setTimeout(() => {
            flash.style.display = 'none';
        }, 5000);
    }
});
</script>

<?php include '../includes/footer.php'; ?>