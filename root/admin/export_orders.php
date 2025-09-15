<?php
session_start();
require_once '../includes/db.php';

// Check admin authentication
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header('Location: admin_login.php');
    exit();
}

$root = dirname(__DIR__);
require_once $root . '/../vendor/autoload.php';

// Get the same filters from orders.php
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';
$sort = $_GET['sort'] ?? 'newest';

// Build WHERE clause (same as orders.php)
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

// Get all orders (without pagination for export)
$query = "
    SELECT o.*, 
           u.username, u.email,
           pay.payment_method,
           pay.payment_status,
           pay.transaction_id,
           COUNT(oi.product_id) as item_count,
           GROUP_CONCAT(DISTINCT oi.product_name ORDER BY oi.product_name SEPARATOR ', ') as product_names
    FROM orders o
    JOIN user u ON o.user_id = u.user_id
    LEFT JOIN order_items oi ON o.order_id = oi.order_id
    LEFT JOIN payments pay ON o.payment_id = pay.payment_id
    $where_clause
    GROUP BY o.order_id
    ORDER BY $order_by
";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get summary statistics
$stats_query = "
    SELECT 
        COUNT(*) as total_orders,
        SUM(total_amount) as total_revenue,
        AVG(total_amount) as avg_order_value,
        SUM(CASE WHEN order_status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
        SUM(CASE WHEN order_status = 'processing' THEN 1 ELSE 0 END) as processing_orders,
        SUM(CASE WHEN order_status = 'shipped' THEN 1 ELSE 0 END) as shipped_orders,
        SUM(CASE WHEN order_status = 'delivered' THEN 1 ELSE 0 END) as delivered_orders,
        SUM(CASE WHEN order_status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_orders
    FROM orders o
    JOIN user u ON o.user_id = u.user_id
    $where_clause
";

$stats_stmt = $pdo->prepare($stats_query);
$stats_stmt->execute($params);
$stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

// Initialize PDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Document settings
$pdf->SetCreator('ToyLand Store Admin');
$pdf->SetAuthor('ToyLand Store');
$pdf->SetTitle('Orders Export - ' . date('Y-m-d H:i:s'));
$pdf->SetSubject('Order Management Export');
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(10, 15, 10);
$pdf->SetAutoPageBreak(TRUE, 15);
$pdf->AddPage('L'); // Landscape for better table display
$pdf->SetFont('helvetica', '', 9);

// Optional logo
$logoPath = $root . '/public/assets/logo.png';
if (file_exists($logoPath)) {
    $pdf->Image($logoPath, 15, 10, 25, '', '', '', '', false, 300);
}

// Header
$pdf->SetY(10);
$pdf->SetFillColor(44, 62, 80); // Dark blue-gray
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('helvetica', 'B', 20);
$pdf->Cell(0, 15, 'ToyLand Store - Orders Export', 0, 1, 'C', 1);

// Export info
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('helvetica', '', 10);
$pdf->Ln(5);

$export_info = sprintf(
    "Export Date: %s | Total Orders: %d | Date Range: %s to %s | Status Filter: %s",
    date('M j, Y g:i A'),
    count($orders),
    !empty($date_from) ? $date_from : 'All',
    !empty($date_to) ? $date_to : 'All',
    !empty($status_filter) ? ucfirst($status_filter) : 'All'
);

$pdf->Cell(0, 8, $export_info, 0, 1, 'C');
$pdf->Ln(5);

// Summary Statistics
if (!empty($orders)) {
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetFillColor(236, 240, 241);
    $pdf->Cell(0, 10, 'Summary Statistics', 0, 1, 'L', 1);
    $pdf->SetFont('helvetica', '', 10);
    
    $summaryHtml = '
    <table cellpadding="8" style="width:100%; border-collapse:collapse;">
    <tr>
        <td style="width:25%; background-color:#ecf0f1; border:1px solid #bdc3c7; font-weight:bold;">Total Revenue:</td>
        <td style="width:25%; border:1px solid #bdc3c7;">RM ' . number_format($stats['total_revenue'], 2) . '</td>
        <td style="width:25%; background-color:#ecf0f1; border:1px solid #bdc3c7; font-weight:bold;">Avg Order Value:</td>
        <td style="width:25%; border:1px solid #bdc3c7;">RM ' . number_format($stats['avg_order_value'], 2) . '</td>
    </tr>
    <tr>
        <td style="background-color:#ecf0f1; border:1px solid #bdc3c7; font-weight:bold;">Pending Orders:</td>
        <td style="border:1px solid #bdc3c7;">' . $stats['pending_orders'] . '</td>
        <td style="background-color:#ecf0f1; border:1px solid #bdc3c7; font-weight:bold;">Processing Orders:</td>
        <td style="border:1px solid #bdc3c7;">' . $stats['processing_orders'] . '</td>
    </tr>
    <tr>
        <td style="background-color:#ecf0f1; border:1px solid #bdc3c7; font-weight:bold;">Shipped Orders:</td>
        <td style="border:1px solid #bdc3c7;">' . $stats['shipped_orders'] . '</td>
        <td style="background-color:#ecf0f1; border:1px solid #bdc3c7; font-weight:bold;">Delivered Orders:</td>
        <td style="border:1px solid #bdc3c7;">' . $stats['delivered_orders'] . '</td>
    </tr>
    </table>
    <br/>';
    
    $pdf->writeHTML($summaryHtml, true, false, true, false, '');
}

// Orders Table Header
$pdf->SetFont('helvetica', 'B', 10);
$pdf->SetFillColor(52, 152, 219); // Blue
$pdf->SetTextColor(255, 255, 255);

$ordersTableHtml = '
<style>
    table.orders { border-collapse: collapse; width: 100%; font-size: 8px; }
    table.orders th { 
        background-color: #3498db; 
        color: #ffffff; 
        padding: 6px; 
        font-weight: bold; 
        text-align: center; 
        border: 1px solid #2980b9;
    }
    table.orders td { 
        border: 1px solid #dddddd; 
        padding: 4px; 
        text-align: center;
        vertical-align: middle;
    }
    .text-left { text-align: left; }
    .text-right { text-align: right; }
    .status-pending { color: #f39c12; font-weight: bold; }
    .status-processing { color: #3498db; font-weight: bold; }
    .status-shipped { color: #9b59b6; font-weight: bold; }
    .status-delivered { color: #27ae60; font-weight: bold; }
    .status-cancelled { color: #e74c3c; font-weight: bold; }
</style>

<table class="orders" cellpadding="4" cellspacing="0">
<tr>
    <th style="width:8%;">Order #</th>
    <th style="width:12%;">Customer</th>
    <th style="width:10%;">Email</th>
    <th style="width:8%;">Items</th>
    <th style="width:10%;">Total</th>
    <th style="width:8%;">Status</th>
    <th style="width:10%;">Payment</th>
    <th style="width:12%;">Transaction ID</th>
    <th style="width:10%;">Date</th>
    <th style="width:12%;">Shipping Info</th>
</tr>';

// Add orders data
$alt = false;
foreach ($orders as $order) {
    $rowBg = $alt ? ' style="background-color:#f8f9fa;"' : '';
    
    $customer_name = htmlspecialchars($order['shipping_first_name'] . ' ' . $order['shipping_last_name']);
    $customer_email = htmlspecialchars($order['contact_email']);
    $order_number = htmlspecialchars($order['order_number']);
    $total_amount = 'RM ' . number_format($order['total_amount'], 2);
    $payment_method = ucfirst(str_replace('_', ' ', $order['payment_method'] ?? 'N/A'));
    $transaction_id = htmlspecialchars($order['transaction_id'] ?? 'N/A');
    $order_date = date('M j, Y', strtotime($order['created_at']));
    
    // Shipping info
    $shipping_info = '';
    if (!empty($order['shipping_courier']) && !empty($order['tracking_number'])) {
        $shipping_info = ucfirst(str_replace('_', ' ', $order['shipping_courier'])) . '<br/>' . 
                        htmlspecialchars($order['tracking_number']);
    } else {
        $shipping_info = 'N/A';
    }
    
    $ordersTableHtml .= '<tr' . $rowBg . '>
        <td class="text-left">' . $order_number . '</td>
        <td class="text-left">' . $customer_name . '</td>
        <td class="text-left" style="font-size:7px;">' . $customer_email . '</td>
        <td>' . $order['item_count'] . '</td>
        <td class="text-right">' . $total_amount . '</td>
        <td><span class="status-' . $order['order_status'] . '">' . ucfirst($order['order_status']) . '</span></td>
        <td class="text-left">' . $payment_method . '</td>
        <td class="text-left" style="font-size:7px;">' . $transaction_id . '</td>
        <td>' . $order_date . '</td>
        <td class="text-left" style="font-size:7px;">' . $shipping_info . '</td>
    </tr>';
    
    $alt = !$alt;
}

$ordersTableHtml .= '</table>';

$pdf->SetTextColor(0, 0, 0);
$pdf->writeHTML($ordersTableHtml, true, false, true, false, '');

// Add new page for order details if there are orders
if (!empty($orders)) {
    $pdf->AddPage('L');
    
    // Order Items Details Header
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetFillColor(44, 62, 80);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(0, 12, 'Detailed Order Items', 0, 1, 'C', 1);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Ln(5);
    
    // Get detailed order items for each order
    foreach ($orders as $order) {
        // Get order items for this order
        $items_stmt = $pdo->prepare("
            SELECT oi.*, p.sku
            FROM order_items oi
            JOIN products p ON oi.product_id = p.product_id
            WHERE oi.order_id = ?
            ORDER BY oi.product_name
        ");
        $items_stmt->execute([$order['order_id']]);
        $order_items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($order_items)) {
            // Order header
            $pdf->SetFont('helvetica', 'B', 11);
            $pdf->SetFillColor(189, 195, 199);
            $pdf->Cell(0, 8, 'Order: ' . htmlspecialchars($order['order_number']) . ' - ' . 
                      htmlspecialchars($order['shipping_first_name'] . ' ' . $order['shipping_last_name']) .
                      ' (RM ' . number_format($order['total_amount'], 2) . ')', 0, 1, 'L', 1);
            
            // Items table for this order
            $itemsHtml = '
            <table cellpadding="4" cellspacing="0" style="width:100%; border-collapse:collapse; margin-bottom:15px;">
            <tr>
                <th style="width:15%; background-color:#34495e; color:white; padding:6px; border:1px solid #2c3e50;">SKU</th>
                <th style="width:45%; background-color:#34495e; color:white; padding:6px; border:1px solid #2c3e50;">Product Name</th>
                <th style="width:10%; background-color:#34495e; color:white; padding:6px; border:1px solid #2c3e50; text-align:center;">Qty</th>
                <th style="width:15%; background-color:#34495e; color:white; padding:6px; border:1px solid #2c3e50; text-align:right;">Unit Price</th>
                <th style="width:15%; background-color:#34495e; color:white; padding:6px; border:1px solid #2c3e50; text-align:right;">Total</th>
            </tr>';
            
            $item_alt = false;
            foreach ($order_items as $item) {
                $item_bg = $item_alt ? ' style="background-color:#f8f9fa;"' : '';
                $itemsHtml .= '<tr' . $item_bg . '>
                    <td style="border:1px solid #ddd; padding:4px;">' . htmlspecialchars($item['sku']) . '</td>
                    <td style="border:1px solid #ddd; padding:4px;">' . htmlspecialchars($item['product_name']) . '</td>
                    <td style="border:1px solid #ddd; padding:4px; text-align:center;">' . (int)$item['quantity'] . '</td>
                    <td style="border:1px solid #ddd; padding:4px; text-align:right;">RM ' . number_format($item['unit_price'], 2) . '</td>
                    <td style="border:1px solid #ddd; padding:4px; text-align:right;">RM ' . number_format($item['total_price'], 2) . '</td>
                </tr>';
                $item_alt = !$item_alt;
            }
            
            $itemsHtml .= '</table>';
            
            $pdf->SetFont('helvetica', '', 9);
            $pdf->writeHTML($itemsHtml, true, false, true, false, '');
            
            // Check if we need a new page
            if ($pdf->GetY() > 180) {
                $pdf->AddPage('L');
            }
        }
    }
}

// Footer
$pdf->SetY(-20);
$pdf->SetFont('helvetica', 'I', 8);
$pdf->SetTextColor(128, 128, 128);
$pdf->Cell(0, 10, 'Generated on ' . date('M j, Y g:i A') . ' | ToyLand Store Admin Panel | Page ' . $pdf->getAliasNumPage() . ' of ' . $pdf->getAliasNbPages(), 0, 0, 'C');

// Generate filename
$filename = 'orders_export_' . date('Y-m-d_H-i-s');
if (!empty($status_filter)) {
    $filename .= '_' . $status_filter;
}
if (!empty($date_from) || !empty($date_to)) {
    $filename .= '_' . (!empty($date_from) ? $date_from : 'start') . '_to_' . (!empty($date_to) ? $date_to : 'end');
}
$filename .= '.pdf';

// Output PDF
$pdf->Output($filename, 'D'); // 'D' for download
exit;
?>