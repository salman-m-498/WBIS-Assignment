<?php
session_start();
require_once '../includes/db.php';
require_once 'send_status_email.php';

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header('Location: admin_login.php');
    exit();
}

// Handle order status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        $valid_statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancel_requested', 'cancelled'];
        $allowed_transitions = [
        'pending'           => ['processing', 'cancel_requested'],
        'processing'        => ['shipped', 'cancel_requested'],
        'shipped'           => ['delivered'],
        'cancel_requested'  => ['cancelled'],
        'delivered'         => [],   // locked
        'cancelled'         => []    // locked
        ];
        if ($_POST['action'] === 'update_status') {
           // ---------- SINGLE ORDER UPDATE ----------
            $order_id = $_POST['order_id'] ?? null;
            $new_status = $_POST['new_status'] ?? '';

            if (empty($order_id) || !in_array($new_status, $valid_statuses)) {
                throw new Exception("Invalid order update request");
            }

            $stmt = $pdo->prepare("SELECT order_status FROM orders WHERE order_id = ?");
            $stmt->execute([$order_id]);
            $current_status = $stmt->fetchColumn();

           if (!in_array($new_status, $allowed_transitions[$current_status] ?? [])) {
                throw new Exception("Invalid status transition: $current_status → $new_status");
            }

             // Handle different status types
            if ($new_status === 'shipped') {
                handleShippedOrder($pdo, $order_id, $new_status);
            } else {
                handleRegularStatusUpdate($pdo, $order_id, $new_status);
            }

            $_SESSION['success_message'] = "Order #$order_id updated successfully. ✉️ Status email sent to customer.";
        }

        elseif ($_POST['action'] === 'bulk_update') {
            // ---------- BULK UPDATE (true multi-order) ----------
            $order_ids = $_POST['order_ids'] ?? [];
            $new_status = $_POST['bulk_status'] ?? $_POST['new_status'] ?? '';

            if (empty($order_ids) || !in_array($new_status, $valid_statuses)) {
                throw new Exception("Please select orders and a valid status");
            }

            // Protect shipped orders from status regression
            validateBulkStatusChange($pdo, $order_ids, $new_status, $allowed_transitions);

            if ($new_status === 'shipped') {
                handleBulkShippedOrders($pdo, $order_ids, $new_status);
            } else {
                handleBulkRegularUpdate($pdo, $order_ids, $new_status);
            }

            $_SESSION['success_message'] = count($order_ids) . " order(s) updated successfully. ✉️ Emails sent to customers.";
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
        error_log("Order update error: " . $e->getMessage());
        if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
    }

    header("Location: orders.php");
    exit;
}

function handleShippedOrder($pdo, $order_id, $new_status) {
    $courier = trim($_POST['courier'] ?? '');
    $tracking = trim($_POST['tracking_number'] ?? '');
    
    if (empty($courier) || empty($tracking)) {
        throw new Exception("Courier and tracking number are required for shipped orders");
    }

    $stmt = $pdo->prepare("
        UPDATE orders 
        SET order_status = ?, shipping_courier = ?, tracking_number = ?, updated_at = NOW()
        WHERE order_id = ?
    ");
    $stmt->execute([$new_status, $courier, $tracking, $order_id]);

    sendOrderStatusEmail($pdo, $order_id);
}

function handleRegularStatusUpdate($pdo, $order_id, $new_status) {
    $stmt = $pdo->prepare("
        UPDATE orders 
        SET order_status = ?, updated_at = NOW()
        WHERE order_id = ?
    ");
    $stmt->execute([$new_status, $order_id]);

    sendOrderStatusEmail($pdo, $order_id);
}

function validateBulkStatusChange($pdo, $order_ids, $new_status, $allowed_transitions) {
    $placeholders = str_repeat('?,', count($order_ids) - 1) . '?';
    $status_check = $pdo->prepare("SELECT order_id, order_status FROM orders WHERE order_id IN ($placeholders)");
    $status_check->execute($order_ids);
    
    foreach ($status_check->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $current_status = $row['order_status'];

    if (!in_array($new_status, $allowed_transitions[$current_status] ?? [])) {
        throw new Exception("Order ID {$row['order_id']} cannot transition from $current_status → $new_status.");
    }
}
}

function handleBulkShippedOrders($pdo, $order_ids, $new_status) {
    $bulk_type = $_POST['bulk_shipping_type'] ?? 'same';
    $placeholders = str_repeat('?,', count($order_ids) - 1) . '?';

    if ($bulk_type === 'same') {
        $courier = trim($_POST['courier'] ?? '');
        $tracking = trim($_POST['tracking_number'] ?? '');
        if (empty($courier) || empty($tracking)) {
            throw new Exception("Courier and tracking number are required");
        }

        $stmt = $pdo->prepare("
            UPDATE orders
            SET order_status = ?, shipping_courier = ?, tracking_number = ?, updated_at = NOW()
            WHERE order_id IN ($placeholders)
        ");
        $stmt->execute(array_merge([$new_status, $courier, $tracking], $order_ids));

        foreach ($order_ids as $id) {
            sendOrderStatusEmail($pdo, $id);
        }
    } else {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("
            UPDATE orders
            SET order_status = ?, shipping_courier = ?, tracking_number = ?, updated_at = NOW()
            WHERE order_id = ?
        ");

        foreach ($order_ids as $id) {
            $courier = trim($_POST['individual_courier'][$id] ?? '');
            $tracking = trim($_POST['individual_tracking'][$id] ?? '');
            if (empty($courier) || empty($tracking)) {
                throw new Exception("Missing courier/tracking for order $id");
            }
            $stmt->execute([$new_status, $courier, $tracking, $id]);
            sendOrderStatusEmail($pdo, $id);
        }
        $pdo->commit();
    }
}


function handleBulkRegularUpdate($pdo, $order_ids, $new_status) {
    $placeholders = str_repeat('?,', count($order_ids) - 1) . '?';
    $stmt = $pdo->prepare("
        UPDATE orders 
        SET order_status = ?, updated_at = NOW()
        WHERE order_id IN ($placeholders)
    ");
    $stmt->execute(array_merge([$new_status], $order_ids));

    foreach ($order_ids as $id) {
        sendOrderStatusEmail($pdo, $id);
    }
}

// Search and filter parameters
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';
$sort = $_GET['sort'] ?? 'newest';

// Build WHERE clause
$where_conditions = [];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(o.order_number LIKE ? OR CONCAT(u.first_name, ' ', u.last_name) LIKE ? OR u.email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($status_filter)) {
    $where_conditions[] = "o.order_status = ?";
    $params[] = $status_filter;
}

if (!empty($date_from)) {
    $where_conditions[] = "DATE(o.created_at) >= ?";
    $params[] = $date_from;
}

if (!empty($date_to)) {
    $where_conditions[] = "DATE(o.created_at) <= ?";
    $params[] = $date_to;
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Sorting
$order_by = match($sort) {
    'newest' => 'o.created_at DESC',
    'oldest' => 'o.created_at ASC',
    'amount_high' => 'o.total_amount DESC',
    'amount_low' => 'o.total_amount ASC',
    'status' => 'o.order_status ASC, o.created_at DESC',
    default => 'o.created_at DESC'
};

// Pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 15;
$offset = ($page - 1) * $per_page;

// Get total count
$count_query = "
    SELECT COUNT(*) 
    FROM orders o 
    JOIN user u ON o.user_id = u.user_id 
    $where_clause
";
$stmt = $pdo->prepare($count_query);
$stmt->execute($params);
$total_orders = $stmt->fetchColumn();
$total_pages = ceil($total_orders / $per_page);

$per_page = (int)$per_page;
$offset = (int)$offset;
// Get orders
$query = "
    SELECT o.*, 
           u.username, u.email,
           pay.payment_method,
           pay.payment_status,
           pay.transaction_id,
           COUNT(oi.product_id) as item_count,
           GROUP_CONCAT(DISTINCT CONCAT(oi.product_name, ' (x', oi.quantity, ')') 
           ORDER BY oi.product_name SEPARATOR ', ') as product_items,
           SUM(oi.total_price) as order_items_total
    FROM orders o
    JOIN user u ON o.user_id = u.user_id
    LEFT JOIN order_items oi ON o.order_id = oi.order_id
    LEFT JOIN payments pay ON o.payment_id = pay.payment_id
    $where_clause
    GROUP BY o.order_id
    ORDER BY $order_by
    LIMIT $per_page OFFSET $offset
";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get status counts for quick stats
$stats_stmt = $pdo->prepare("
    SELECT 
        order_status,
        COUNT(*) as count,
        SUM(total_amount) as total_value
    FROM orders 
    GROUP BY order_status
");
$stats_stmt->execute();
$status_stats = $stats_stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = "Order Management";
$page_description = "Manage and track all customer orders";

include '../includes/admin_header.php';
?>

<!-- Flash Messages -->
<?php if (isset($_SESSION['success_message'])): ?>
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i>
    <?= htmlspecialchars($_SESSION['success_message']) ?>
    <?php unset($_SESSION['success_message']); ?>
</div>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
<div class="alert alert-error">
    <i class="fas fa-exclamation-circle"></i>
    <?= htmlspecialchars($_SESSION['error_message']) ?>
    <?php unset($_SESSION['error_message']); ?>
</div>
<?php endif; ?>

<section class="admin-orders-section">
    <div class="container">
        <div class="page-header">
            <h1>Order Management</h1>
            <div class="header-actions">
                <button class="btn btn-primary" onclick="exportOrders()">
                    <i class="fas fa-download"></i> Export Orders
                </button>
                <a href="sales_analytics.php" class="btn btn-outline">
                    <i class="fas fa-chart-line"></i> Sales Analytics
                </a>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="quick-stats">
            <?php
            $total_revenue = 0;
            $pending_count = 0;
            $processing_count = 0;
            $delivered_count = 0;
            $cancel_requested_count = 0;
            
            foreach ($status_stats as $stat) {
                $total_revenue += $stat['total_value'];
                switch ($stat['order_status']) {
                    case 'pending': $pending_count = $stat['count']; break;
                    case 'processing': $processing_count = $stat['count']; break;
                    case 'delivered': $delivered_count = $stat['count']; break;
                    case 'cancel_requested': $cancel_requested_count = $stat['count']; break;
                }
            }
            ?>
            <div class="stat-card">
                <div class="stat-icon revenue">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-info">
                    <h3>RM<?= number_format($total_revenue, 2) ?></h3>
                    <p>Total Revenue</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon pending">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $pending_count ?></h3>
                    <p>Pending Orders</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon processing">
                    <i class="fas fa-cog"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $processing_count ?></h3>
                    <p>Processing</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon delivered">
                    <i class="fas fa-check"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $delivered_count ?></h3>
                    <p>Delivered</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon cancel-requested">
                    <i class="fas fa-ban"></i>
                </div>
            <div class="stat-info">
                <h3><?= $cancel_requested_count ?></h3>
                <p>Cancel Requested</p>
            </div>
        </div>
    </div>

        <!-- Filters and Search -->
        <div class="filters-section">
            <form method="GET" class="filters-form" id="filters-form">
                <div class="filter-group">
                    <label for="search">Search Orders:</label>
                    <input type="text" id="search" name="search" 
                           value="<?= htmlspecialchars($search) ?>" 
                           placeholder="Order number, customer name, email...">
                </div>
                
                <div class="filter-group">
                    <label for="status">Status:</label>
                    <select id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="pending" <?= $status_filter === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="processing" <?= $status_filter === 'processing' ? 'selected' : '' ?>>Processing</option>
                        <option value="shipped" <?= $status_filter === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                        <option value="delivered" <?= $status_filter === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                        <option value="cancel_requested" <?= $status_filter === 'cancel_requested' ? 'selected' : '' ?>>Cancel Requested</option>
                        <option value="cancelled" <?= $status_filter === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="date_from">From Date:</label>
                    <input type="date" id="date_from" name="date_from" value="<?= htmlspecialchars($date_from) ?>">
                </div>
                
                <div class="filter-group">
                    <label for="date_to">To Date:</label>
                    <input type="date" id="date_to" name="date_to" value="<?= htmlspecialchars($date_to) ?>">
                </div>
                
                <div class="filter-group">
                    <label for="sort">Sort By:</label>
                    <select id="sort" name="sort">
                        <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest First</option>
                        <option value="oldest" <?= $sort === 'oldest' ? 'selected' : '' ?>>Oldest First</option>
                        <option value="amount_high" <?= $sort === 'amount_high' ? 'selected' : '' ?>>Amount (High to Low)</option>
                        <option value="amount_low" <?= $sort === 'amount_low' ? 'selected' : '' ?>>Amount (Low to High)</option>
                        <option value="status" <?= $sort === 'status' ? 'selected' : '' ?>>Status</option>
                    </select>
                </div>
                
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="orders.php" class="btn btn-outline">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Bulk Actions -->
        <div class="bulk-actions" id="bulk-actions" style="display: none;">
            <form method="POST" id="bulk-form">
                <input type="hidden" name="action" value="bulk_update">
                <div class="bulk-controls">
                    <span class="selected-count">0 orders selected</span>
                    <select name="bulk_status" required>
                        <option value="">Select Status</option>
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="shipped">Shipped</option>
                        <option value="delivered" disabled>Delivered (Locked)</option>
                        <option value="cancelled" disabled>Cancelled (Locked)</option>
                    </select>
                    <button type="submit" class="btn btn-primary">Update Selected</button>
                    <button type="button" class="btn btn-outline" onclick="clearSelection()">Cancel</button>
                </div>
            </form>
        </div>

        <!-- Orders Table -->
        <div class="orders-table-container">
            <?php if (empty($orders)): ?>
            <div class="no-orders">
                <i class="fas fa-inbox"></i>
                <h3>No Orders Found</h3>
                <p>No orders match your current filters.</p>
            </div>
            <?php else: ?>
            
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="select-all">
                        </th>
                        <th>Order Number</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                    <tr class="order-row" data-order-id="<?= $order['order_id'] ?>">
                        <td>
                            <input type="checkbox" class="order-checkbox" value="<?= $order['order_id'] ?>">
                        </td>
                        <td>
                            <div class="order-number">
                                <a href="order_details.php?id=<?= $order['order_id'] ?>">
                                    <?= htmlspecialchars($order['order_number']) ?>
                                </a>
                            </div>
                        </td>
                        <td>
                            <div class="customer-info">
                                <div class="customer-name">
                                    <?= htmlspecialchars($order['shipping_first_name'] . ' ' . $order['shipping_last_name']) ?>
                                </div>
                                <div class="customer-email">
                                    <?= htmlspecialchars($order['contact_email']) ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="items-info">
                                <div class="item-count"><?= $order['item_count'] ?> item(s)</div>
                                <div class="item-preview" title="<?= htmlspecialchars($order['product_items']) ?>">
                                    <?= htmlspecialchars(substr($order['product_items'], 0, 50)) ?>
                                    <?= strlen($order['product_items']) > 50 ? '...' : '' ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="order-total">
                                RM<?= number_format($order['total_amount'], 2) ?>
                            </div>
                        </td>
                        <td>

                            <div class="admin-orders">
                            <div class="status-container">
                                <span class="status-badge status-<?= $order['order_status'] ?>">
                                    <?= ucfirst($order['order_status']) ?>
                                </span>
                            </div>
                            </div>
                        </td>
                        <td>
                            <div class="payment-info">
                                <div class="payment-method">
                                    <?= ucfirst(str_replace('_', ' ', $order['payment_method'])) ?>
                                </div>
                                <div class="payment-status status-<?= $order['payment_status'] ?>">
                                    <?= ucfirst($order['payment_status']) ?>
                                </div>
                                <?php if (!empty($order['transaction_id'])): ?>
                                <div class="transaction-id">
                                    Txn: <?= htmlspecialchars($order['transaction_id']) ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <div class="order-date">
                                <div class="date"><?= date('M j, Y', strtotime($order['created_at'])) ?></div>
                                <div class="time"><?= date('g:i A', strtotime($order['created_at'])) ?></div>
                            </div>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="order_details.php?id=<?= $order['order_id'] ?>" 
                                   class="btn btn-sm btn-outline" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button class="btn btn-sm btn-outline" 
                                         onclick="printOrder('<?= $order['order_id'] ?>')" title="Print">
                                    <i class="fas fa-print"></i></button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Shipping Info Modal -->
            <div id="shipping-modal" class="modal">
                <div class="modal-content">
                    
                    <h3>Enter Shipping Information</h3>
                    <form id="shipping-form" method="POST">
                        <input type="hidden" name="action" id="shipping-action" value="update_status">
                        <input type="hidden" name="order_id" id="shipping-order-id">
                        <input type="hidden" name="new_status" value="shipped">
                        
                        <!-- Bulk shipping options will be injected here dynamically -->
      <div class="form-group" id="default-courier-group">
        <label for="courier">Shipping Courier</label>
        <select name="courier" id="courier">
          <option value="">Select Carrier</option>
          <option value="pos_laju">Pos Laju</option>
          <option value="gdex">GDEX</option>
          <option value="dhl">DHL</option>
          <option value="fedex">FedEx</option>
          <option value="citylink">City-Link</option>
        </select>
      </div>

      <div class="form-group" id="default-tracking-group">
        <label for="tracking_number">Tracking Number</label>
        <input type="text" name="tracking_number" id="tracking_number">
      </div>

      <div class="modal-actions">
        <button type="submit" class="btn btn-primary">Update Status</button>
        <button type="button" class="btn btn-outline" onclick="closeShippingModal()">Cancel</button>
      </div>
                    </form>
                </div>
            </div>

            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="pagination">
                  <ul class="pagination-list">
        <?php if ($page > 1): ?>
            <li><a href="?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>">First</a></li>
            <li><a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">Prev</a></li>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li>
                <a class="<?= $page == $i ? 'active' : '' ?>" 
                   href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>">
                   <?= $i ?>
                </a>
            </li>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <li><a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">Next</a></li>
            <li><a href="?<?= http_build_query(array_merge($_GET, ['page' => $total_pages])) ?>">Last</a></li>
        <?php endif; ?>
    </ul>
</div>
<?php endif; ?>         
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
// JavaScript for orders.php
document.addEventListener('DOMContentLoaded', function() {
    initializeOrderManagement();
    initializeFilterEnhancements();
    initializeModalHandlers();
});

// ===========================================
// INITIALIZATION FUNCTIONS
// ===========================================

function initializeOrderManagement() {
    const selectAllCheckbox = document.getElementById('select-all');
    const orderCheckboxes = document.querySelectorAll('.order-checkbox');
    const bulkForm = document.getElementById('bulk-form');

    // Setup select all functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            orderCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActions();
        });
    }

    // Setup individual checkbox handlers
    orderCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });

    // Setup bulk form submission
    if (bulkForm) {
        bulkForm.addEventListener('submit', handleBulkFormSubmission);
    }
}

function initializeFilterEnhancements() {
    const filterInputs = document.querySelectorAll('#filters-form input[type="text"]');
    let filterTimeout;

    filterInputs.forEach(input => {
        input.addEventListener('input', function() {
            clearTimeout(filterTimeout);
            filterTimeout = setTimeout(() => {
                document.getElementById('filters-form').submit();
            }, 500);
        });
    });
}

function initializeModalHandlers() {
    document.addEventListener('click', function(e) {
        const modal = document.getElementById('shipping-modal');
        if (e.target === modal) {
            closeShippingModal();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeShippingModal();
        }
    });
}

// ===========================================
// BULK ACTIONS MANAGEMENT
// ===========================================

function updateBulkActions() {
    const selected = document.querySelectorAll('.order-checkbox:checked');
    const count = selected.length;
    const bulkActions = document.getElementById('bulk-actions');
    const selectedCount = document.querySelector('.selected-count');
    const selectAllCheckbox = document.getElementById('select-all');
    
    if (count > 0) {
        bulkActions.style.display = 'block';
        selectedCount.textContent = `${count} order${count !== 1 ? 's' : ''} selected`;
        updateBulkFormInputs(selected);
    } else {
        bulkActions.style.display = 'none';
    }
    
    // Update select all checkbox state
    if (selectAllCheckbox) {
        selectAllCheckbox.checked = count === document.querySelectorAll('.order-checkbox').length;
        selectAllCheckbox.indeterminate = count > 0 && count < document.querySelectorAll('.order-checkbox').length;
    }
}

function updateBulkFormInputs(selectedCheckboxes) {
    const bulkForm = document.getElementById('bulk-form');
    
    // Remove existing hidden inputs
    bulkForm.querySelectorAll('input[name="order_ids[]"]').forEach(input => input.remove());
    
    // Add new hidden inputs
    selectedCheckboxes.forEach(checkbox => {
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'order_ids[]';
        hiddenInput.value = checkbox.value;
        bulkForm.appendChild(hiddenInput);
    });
}

function handleBulkFormSubmission(e) {
    const bulkStatus = this.bulk_status.value;
    const selectedOrders = Array.from(document.querySelectorAll('.order-checkbox:checked')).map(cb => cb.value);

    if (!bulkStatus) {
        e.preventDefault();
        alert('Please select a status for bulk update.');
        return;
    }

    if (selectedOrders.length === 0) {
        e.preventDefault();
        alert('Please select at least one order.');
        return;
    }

    if (bulkStatus === 'shipped') {
        e.preventDefault();
        openShippingModal(selectedOrders, true);
        return;
    }
    
    if (!confirm(`Are you sure you want to update ${selectedOrders.length} order(s) to "${bulkStatus}" status?`)) {
        e.preventDefault();
    }
}

function clearSelection() {
    document.querySelectorAll('.order-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    const selectAll = document.getElementById('select-all');
    if (selectAll) {
        selectAll.checked = false;
        selectAll.indeterminate = false;
    }
    document.getElementById('bulk-actions').style.display = 'none';
}

// ===========================================
// INDIVIDUAL ORDER STATUS UPDATES
// ===========================================

function updateOrderStatus(selectElement) {
    const orderId = selectElement.dataset.orderId;
    const newStatus = selectElement.value;
    const statusBadge = selectElement.closest('.status-container').querySelector('.status-badge');
    const originalStatus = statusBadge.className.match(/status-(\w+)/)[1];
    
    if (newStatus === 'shipped') {
        openShippingModal([orderId], false);
        selectElement.value = originalStatus;
        return;
    }

    if (confirm(`Are you sure you want to update this order status to "${newStatus}"?`)) {
        showLoadingState(selectElement, statusBadge);
        submitStatusUpdate(orderId, newStatus);
    } else {
        selectElement.value = originalStatus;
    }
}

function showLoadingState(selectElement, statusBadge) {
    selectElement.disabled = true;
    statusBadge.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
}

function submitStatusUpdate(orderId, newStatus) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.style.display = 'none';
    
    const inputs = [
        { name: 'action', value: 'update_status' },
        { name: 'order_id', value: orderId },
        { name: 'new_status', value: newStatus }
    ];
    
    inputs.forEach(inputData => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = inputData.name;
        input.value = inputData.value;
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
}

// ===========================================
// SHIPPING MODAL MANAGEMENT
// ===========================================

function openShippingModal(orderIds, isBulk = false) {
    const modal = document.getElementById('shipping-modal');
    const form = document.getElementById('shipping-form');
    
    cleanupModalForm(form);
    setupModalForType(form, orderIds, isBulk);
    clearFormFields();
    showModal(modal, isBulk);
}

function cleanupModalForm(form) {
    // Remove any previous dynamic order_ids inputs inserted into this form
    form.querySelectorAll('input[name="order_ids[]"]').forEach(input => input.remove());

    // Remove bulk options (if present)
    const existingBulkOptions = document.getElementById('bulk-shipping-options');
    if (existingBulkOptions) {
        existingBulkOptions.remove();
    }
}

function setupModalForType(form, orderIds, isBulk) {
    const actionInput = document.getElementById('shipping-action');
    const orderIdInput = document.getElementById('shipping-order-id');
    const modal = document.getElementById('shipping-modal');

    if (isBulk && orderIds.length > 1) {
        // true bulk update
        actionInput.value = 'bulk_update';

        // Insert hidden inputs for all order_ids[] into the shipping form so PHP receives them
        orderIds.forEach(id => {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'order_ids[]';
            hidden.value = id;
            form.appendChild(hidden);
        });

        modal.querySelector('h3').textContent = `Enter Shipping Information (${orderIds.length} orders)`;
        showBulkShippingOptions(form, orderIds);

    } else {
        // single order -> keep update_status flow
        actionInput.value = 'update_status';
        orderIdInput.value = orderIds[0] ?? ''; // safe fallback if undefined
        modal.querySelector('h3').textContent = 'Enter Shipping Information';
    }
}

function clearFormFields() {
    document.getElementById('courier').value = '';
    document.getElementById('tracking_number').value = '';
}

function showModal(modal, isBulk) {
        modal.classList.add('show');
}
    
    
function showBulkShippingOptions(form, orderIds) {
    const bulkOptionsContainer = document.createElement('div');
    bulkOptionsContainer.id = 'bulk-shipping-options';
    bulkOptionsContainer.innerHTML = createBulkOptionsHTML(orderIds);
    
    const courierGroup = form.querySelector('.form-group');
    form.insertBefore(bulkOptionsContainer, courierGroup);
}

function createBulkOptionsHTML(orderIds) {
    return `
        <div class="form-group">
            <label>Shipping Method:</label>
            <div class="radio-group">
                <label class="radio-option">
                    <input type="radio" name="bulk_shipping_type" value="same" checked onchange="toggleBulkShippingFields()">
                    <span>Same courier and tracking for all orders</span>
                </label>
                <label class="radio-option">
                    <input type="radio" name="bulk_shipping_type" value="individual" onchange="toggleBulkShippingFields()">
                    <span>Individual courier and tracking for each order</span>
                </label>
            </div>
        </div>
        
        <div id="individual-shipping-section" style="display: none;">
            <div class="individual-orders-container">
                ${orderIds.map(orderId => createIndividualOrderHTML(orderId)).join('')}
            </div>
        </div>
    `;
}

function createIndividualOrderHTML(orderId) {
    return `
        <div class="individual-order-shipping">
            <h4>Order ${getOrderNumber(orderId)} (ID: ${orderId})</h4>
            <div class="shipping-fields-row">
                <div class="form-group">
                    <label for="individual_courier_${orderId}">Courier</label>
                    <select name="individual_courier[${orderId}]" id="individual_courier_${orderId}">
                        <option value="">Select Carrier</option>
                        <option value="pos_laju">Pos Laju</option>
                        <option value="gdex">GDEX</option>
                        <option value="dhl">DHL</option>
                        <option value="fedex">FedEx</option>
                        <option value="citylink">City-Link</option>
                </select>
                </div>
                <div class="form-group">
                    <label for="individual_tracking_${orderId}">Tracking Number</label>
                    <input type="text" name="individual_tracking[${orderId}]" id="individual_tracking_${orderId}">
                </div>
            </div>
        </div>
    `;
}

function toggleBulkShippingFields() {
    const shippingType = document.querySelector('input[name="bulk_shipping_type"]:checked').value;
    const individualSection = document.getElementById('individual-shipping-section');
    const regularFields = document.querySelectorAll('#shipping-form > .form-group');
    
    if (shippingType === 'same') {
        showRegularFields(regularFields, individualSection);
    } else {
        showIndividualFields(regularFields, individualSection);
    }
}

function showRegularFields(regularFields, individualSection) {
    regularFields.forEach(field => field.style.display = 'block');
    individualSection.style.display = 'none';
    
    individualSection.querySelectorAll('input').forEach(input => {
        input.value = '';
        input.removeAttribute('required');
    });
    
    document.getElementById('courier').setAttribute('required', '');
    document.getElementById('tracking_number').setAttribute('required', '');
}

function showIndividualFields(regularFields, individualSection) {
    regularFields.forEach(field => field.style.display = 'none');
    individualSection.style.display = 'block';
    
    document.getElementById('courier').removeAttribute('required');
    document.getElementById('tracking_number').removeAttribute('required');
    
    individualSection.querySelectorAll('input').forEach(input => {
        input.setAttribute('required', '');
    });
}

function closeShippingModal() {
    const modal = document.getElementById('shipping-modal');
    
    modal.classList.remove('show');
}

function resetBulkStatusDropdown() {
    const bulkStatusSelect = document.querySelector('select[name="bulk_status"]');
    if (bulkStatusSelect && bulkStatusSelect.value === 'shipped') {
        bulkStatusSelect.value = '';
    }
}

// ===========================================
// UTILITY FUNCTIONS
// ===========================================

function getOrderNumber(orderId) {
    const orderRow = document.querySelector(`tr[data-order-id="${orderId}"]`);
    if (orderRow) {
        const orderNumberLink = orderRow.querySelector('.order-number a');
        if (orderNumberLink) {
            return orderNumberLink.textContent.trim();
        }
    }
    return `#${orderId}`;
}

function exportOrders() {
    const currentUrl = new URL(window.location);
    currentUrl.pathname = currentUrl.pathname.replace('orders.php', 'export_orders.php');
    window.location.href = currentUrl.toString();
}

function printOrder(orderId) {
    window.open(`print_order.php?id=${orderId}`, '_blank');
}
</script>


<?php include '../includes/admin_footer.php'; ?>