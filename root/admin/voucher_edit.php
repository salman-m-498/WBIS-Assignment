<?php
session_start();
require_once '../includes/db.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header('Location: admin_login.php');
    exit();
}

// Check if voucher ID is provided
if (!isset($_GET['id'])) {
    $_SESSION['error'] = "Voucher not found.";
    header('Location: vouchers.php');
    exit();
}

$voucher_id = $_GET['id'];

// Fetch voucher details
$stmt = $pdo->prepare("SELECT * FROM vouchers WHERE voucher_id = ?");
$stmt->execute([$voucher_id]);
$voucher = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$voucher) {
    $_SESSION['error'] = "Voucher not found.";
    header('Location: vouchers.php');
    exit();
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM user_vouchers WHERE voucher_id = ? AND used_at IS NOT NULL");
$stmt->execute([$voucher_id]);
$voucher['times_used'] = $stmt->fetchColumn();

// 🔹 Calculate last updated dynamically
$stmt = $pdo->prepare("SELECT MAX(used_at) FROM user_vouchers WHERE voucher_id = ?");
$stmt->execute([$voucher_id]);
$voucher['updated_at'] = $stmt->fetchColumn();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = strtoupper(trim($_POST['code']));
    $description = trim($_POST['description']);
    $discount_type = $_POST['discount_type'];
    $discount_value = (float)$_POST['discount_value'];
    $min_order_amount = (float)$_POST['min_order_amount'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $usage_limit = (int)$_POST['usage_limit'];
    $per_user_limit = (int)$_POST['per_user_limit'];
    
    // Validation
    $errors = [];
    
    if (empty($code)) {
        $errors[] = "Voucher code is required.";
    }
    
    if (empty($description)) {
        $errors[] = "Description is required.";
    }
    
    if ($discount_value <= 0) {
        $errors[] = "Discount value must be greater than 0.";
    }
    
    if ($discount_type == 'percentage' && $discount_value > 100) {
        $errors[] = "Percentage discount cannot exceed 100%.";
    }
    
    if ($min_order_amount < 0) {
        $errors[] = "Minimum order amount cannot be negative.";
    }
    
    if (strtotime($start_date) >= strtotime($end_date)) {
        $errors[] = "End date must be after start date.";
    }
    
    // Check if code already exists for other vouchers
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM vouchers WHERE code = ? AND voucher_id != ?");
    $stmt->execute([$code, $voucher_id]);
    if ($stmt->fetchColumn() > 0) {
        $errors[] = "Voucher code already exists.";
    }
    
    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE vouchers SET code = ?, description = ?, discount_type = ?, discount_value = ?, min_order_amount = ?, start_date = ?, end_date = ?, usage_limit = ?, per_user_limit = ? WHERE voucher_id = ?");
        
        if ($stmt->execute([$code, $description, $discount_type, $discount_value, $min_order_amount, $start_date, $end_date, $usage_limit, $per_user_limit, $voucher_id])) {
            $_SESSION['success'] = "Voucher updated successfully!";
            header('Location: vouchers.php');
            exit();
        } else {
            $errors[] = "Error updating voucher. Please try again.";
        }
    }
}

// Page variables
$page_title = "Edit Voucher";
$page_description = "Edit voucher details";
$show_breadcrumb = true;
$breadcrumb_items = [
    ['url' => 'dashboard.php', 'title' => 'Dashboard'],
    ['url' => 'vouchers.php', 'title' => 'Vouchers'],
    ['url' => 'voucher_edit.php?id='.$voucher_id, 'title' => 'Edit Voucher']
];

// Include header
include '../includes/admin_header.php';
?>

<section class="admin-section">
    <div class="container">
        <div class="vouchers-admin-header">
            <h1><i class="fas fa-edit"></i> Edit Voucher</h1>
            <div class="admin-actions">
                <a href="vouchers.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Vouchers
                </a>
            </div>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="admin-form-container">
            <form method="POST" class="admin-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="code">Voucher Code *</label>
                        <input type="text" id="code" name="code" value="<?php echo htmlspecialchars(isset($_POST['code']) ? $_POST['code'] : $voucher['code']); ?>" required>
                        <small>Enter a unique voucher code (e.g., SAVE20, WELCOME10)</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description *</label>
                        <input type="text" id="description" name="description" value="<?php echo htmlspecialchars(isset($_POST['description']) ? $_POST['description'] : $voucher['description']); ?>" required>
                        <small>Brief description of the voucher offer</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="discount_type">Discount Type *</label>
                        <select id="discount_type" name="discount_type" required>
                            <option value="percentage" <?php echo (isset($_POST['discount_type']) ? $_POST['discount_type'] : $voucher['discount_type']) == 'percentage' ? 'selected' : ''; ?>>Percentage (%)</option>
                            <option value="fixed" <?php echo (isset($_POST['discount_type']) ? $_POST['discount_type'] : $voucher['discount_type']) == 'fixed' ? 'selected' : ''; ?>>Fixed Amount</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="discount_value">Discount Value *</label>
                        <input type="number" id="discount_value" name="discount_value" step="0.01" min="0" value="<?php echo htmlspecialchars(isset($_POST['discount_value']) ? $_POST['discount_value'] : $voucher['discount_value']); ?>" required>
                        <small>Enter percentage (1-100) or fixed amount</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="min_order_amount">Minimum Order Amount</label>
                        <input type="number" id="min_order_amount" name="min_order_amount" step="0.01" min="0" value="<?php echo htmlspecialchars(isset($_POST['min_order_amount']) ? $_POST['min_order_amount'] : $voucher['min_order_amount']); ?>">
                        <small>Minimum order amount required to use this voucher (0 = no minimum)</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="usage_limit">Usage Limit</label>
                        <input type="number" id="usage_limit" name="usage_limit" min="1" value="<?php echo htmlspecialchars(isset($_POST['usage_limit']) ? $_POST['usage_limit'] : $voucher['usage_limit']); ?>">
                        <small>Total number of times this voucher can be used (leave empty for unlimited)</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="per_user_limit">Per User Limit</label>
                        <input type="number" id="per_user_limit" name="per_user_limit" min="1" value="<?php echo htmlspecialchars(isset($_POST['per_user_limit']) ? $_POST['per_user_limit'] : $voucher['per_user_limit']); ?>">
                        <small>Maximum times a single user can use this voucher (leave empty for unlimited)</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="start_date">Start Date *</label>
                        <input type="datetime-local" id="start_date" name="start_date" value="<?php echo htmlspecialchars(isset($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-d\TH:i', strtotime($voucher['start_date']))); ?>" required>
                        <small>When this voucher becomes active</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="end_date">End Date *</label>
                        <input type="datetime-local" id="end_date" name="end_date" value="<?php echo htmlspecialchars(isset($_POST['end_date']) ? $_POST['end_date'] : date('Y-m-d\TH:i', strtotime($voucher['end_date']))); ?>" required>
                        <small>When this voucher expires</small>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Voucher
                    </button>
                    <a href="vouchers.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Voucher Status Information -->
        <div class="admin-info-box">
            <h3><i class="fas fa-info-circle"></i> Voucher Status</h3>
            <div class="info-grid">
                <div class="info-item">
                    <label>Current Status:</label>
                    <span class="status-badge status-<?php echo htmlspecialchars($voucher['status']); ?>">
                        <?php echo ucfirst($voucher['status']); ?>
                    </span>
                </div>
                <div class="info-item">
                    <label>Times Used:</label>
                    <span><?php echo $voucher['times_used'] ?? 0; ?></span>
                </div>
                <div class="info-item">
                    <label>Created:</label>
                    <span><?php echo date('M d, Y H:i', strtotime($voucher['created_at'])); ?></span>
                </div>
                <?php if ($voucher['updated_at']): ?>
                <div class="info-item">
                    <label>Last Updated:</label>
                    <span><?php echo date('M d, Y H:i', strtotime($voucher['updated_at'])); ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
// Auto-format voucher code to uppercase
document.getElementById('code').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});

// Dynamic discount value label based on type
document.getElementById('discount_type').addEventListener('change', function() {
    const discountValueInput = document.getElementById('discount_value');
    const discountValueLabel = discountValueInput.previousElementSibling;
    const discountValueSmall = discountValueInput.nextElementSibling;
    
    if (this.value === 'percentage') {
        discountValueInput.setAttribute('max', '100');
        discountValueSmall.textContent = 'Enter percentage (1-100)';
    } else {
        discountValueInput.removeAttribute('max');
        discountValueSmall.textContent = 'Enter fixed discount amount';
    }
});

// Validate dates
document.getElementById('start_date').addEventListener('change', validateDates);
document.getElementById('end_date').addEventListener('change', validateDates);

function validateDates() {
    const startDate = new Date(document.getElementById('start_date').value);
    const endDate = new Date(document.getElementById('end_date').value);
    
    if (startDate && endDate && startDate >= endDate) {
        alert('End date must be after start date.');
        document.getElementById('end_date').focus();
    }
}
</script>

<?php include '../includes/admin_footer.php'; ?>