<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header('Location: admin_login.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $voucher_id = generateNextId($pdo, 'vouchers', 'voucher_id', 'V', 10);
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
    
    // Check if code already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM vouchers WHERE code = ?");
    $stmt->execute([$code]);
    if ($stmt->fetchColumn() > 0) {
        $errors[] = "Voucher code already exists.";
    }
    
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO vouchers (voucher_id, code, description, discount_type, discount_value, min_order_amount, start_date, end_date, usage_limit, per_user_limit, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        if ($stmt->execute([$voucher_id, $code, $description, $discount_type, $discount_value, $min_order_amount, $start_date, $end_date, $usage_limit, $per_user_limit, $_SESSION['admin_id']])) {
            $_SESSION['success'] = "Voucher created successfully!";
            header('Location: vouchers.php');
            exit();
        } else {
            $errors[] = "Error creating voucher. Please try again.";
        }
    }
}

// Page variables
$page_title = "Add New Voucher";
$page_description = "Create a new discount voucher";
$show_breadcrumb = true;
$breadcrumb_items = [
    ['url' => 'dashboard.php', 'title' => 'Dashboard'],
    ['url' => 'vouchers.php', 'title' => 'Vouchers'],
    ['url' => 'voucher_add.php', 'title' => 'Add Voucher']
];

// Include header
include '../includes/admin_header.php';
?>

<section class="admin-section">
    <div class="container">
        <div class="vouchers-admin-header">
            <h1><i class="fas fa-plus"></i> Add New Voucher</h1>
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
                        <input type="text" id="code" name="code" value="<?php echo isset($_POST['code']) ? htmlspecialchars($_POST['code']) : ''; ?>" required>
                        <small>Enter a unique voucher code (e.g., SAVE20, WELCOME10)</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description *</label>
                        <input type="text" id="description" name="description" value="<?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?>" required>
                        <small>Brief description of the voucher</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="discount_type">Discount Type *</label>
                        <select id="discount_type" name="discount_type" required>
                            <option value="percentage" <?php echo (isset($_POST['discount_type']) && $_POST['discount_type'] == 'percentage') ? 'selected' : ''; ?>>Percentage (%)</option>
                            <option value="fixed" <?php echo (isset($_POST['discount_type']) && $_POST['discount_type'] == 'fixed') ? 'selected' : ''; ?>>Fixed Amount ($)</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="discount_value">Discount Value *</label>
                        <input type="number" id="discount_value" name="discount_value" step="0.01" min="0" value="<?php echo isset($_POST['discount_value']) ? $_POST['discount_value'] : ''; ?>" required>
                        <small>Enter percentage (1-100) or dollar amount</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="min_order_amount">Minimum Order Amount</label>
                        <input type="number" id="min_order_amount" name="min_order_amount" step="0.01" min="0" value="<?php echo isset($_POST['min_order_amount']) ? $_POST['min_order_amount'] : '0'; ?>">
                        <small>Minimum order amount to use this voucher</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="start_date">Start Date *</label>
                        <input type="datetime-local" id="start_date" name="start_date" value="<?php echo isset($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-d\TH:i'); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="end_date">End Date *</label>
                        <input type="datetime-local" id="end_date" name="end_date" value="<?php echo isset($_POST['end_date']) ? $_POST['end_date'] : ''; ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="usage_limit">Total Usage Limit</label>
                        <input type="number" id="usage_limit" name="usage_limit" min="0" value="<?php echo isset($_POST['usage_limit']) ? $_POST['usage_limit'] : '0'; ?>">
                        <small>Maximum number of times this voucher can be used (0 = unlimited)</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="per_user_limit">Per User Limit</label>
                        <input type="number" id="per_user_limit" name="per_user_limit" min="1" value="<?php echo isset($_POST['per_user_limit']) ? $_POST['per_user_limit'] : '1'; ?>">
                        <small>Maximum uses per customer</small>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Voucher
                    </button>
                    <a href="vouchers.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
// Auto-generate voucher code suggestion
document.addEventListener('DOMContentLoaded', function() {
    const codeInput = document.getElementById('code');
    const descInput = document.getElementById('description');
    
    if (codeInput.value === '') {
        // Generate a random code suggestion
        const randomCode = 'SAVE' + Math.floor(Math.random() * 90 + 10);
        codeInput.placeholder = 'e.g., ' + randomCode;
    }
});
</script>

<?php
include '../includes/admin_footer.php';
?>
