<?php
session_start();
require_once '../includes/db.php';

// Check admin authentication
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header('Location: admin_login.php');
    exit();
}

$root = dirname(__DIR__);
require_once $root . '/../vendor/autoload.php'; // TCPDF autoload

// Get order_id
$order_id = $_GET['id'] ?? null;
if (!$order_id) {
    die("No order ID provided.");
}

// Fetch order details
$stmt = $pdo->prepare("
    SELECT o.*, 
           u.username, u.email,
           pay.payment_method,
           pay.payment_status,
           pay.transaction_id
    FROM orders o
    JOIN user u ON o.user_id = u.user_id
    LEFT JOIN payments pay ON o.payment_id = pay.payment_id
    WHERE o.order_id = ?
");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Order not found.");
}

// Fetch order items
$items_stmt = $pdo->prepare("
    SELECT oi.*, p.name, p.sku
    FROM order_items oi
    JOIN products p ON oi.product_id = p.product_id
    WHERE oi.order_id = ?
");
$items_stmt->execute([$order_id]);
$order_items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);

// ======================
//  Build PDF
// ======================
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator('ToyLand Store Admin');
$pdf->SetAuthor('ToyLand Store');
$pdf->SetTitle('Order Invoice #' . $order['order_number']);
$pdf->SetSubject('Order Invoice');
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(15, 15, 15);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 10);

// Optional Logo
$logoPath = $root . '/public/assets/logo.png';
if (file_exists($logoPath)) {
    $pdf->Image($logoPath, 15, 10, 30);
    $pdf->Ln(20);
}

// Header with styling
$pdf->SetFont('helvetica', 'B', 18);
$pdf->SetFillColor(41, 128, 185); // blue header
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(0, 12, 'Order Invoice', 0, 1, 'C', 1);
$pdf->Ln(5);

$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('helvetica', '', 10);

// ======================
// Order Info Section
// ======================
$order_info = '
<style>
.info-table td { padding: 6px; border: 1px solid #ccc; }
.info-table th { background-color: #f2f2f2; padding: 6px; border: 1px solid #ccc; }
</style>

<table class="info-table" cellpadding="4" cellspacing="0">
<tr>
  <th width="25%">Order Number</th>
  <td width="25%">' . htmlspecialchars($order['order_number']) . '</td>
  <th width="25%">Order Date</th>
  <td width="25%">' . date('M j, Y g:i A', strtotime($order['created_at'])) . '</td>
</tr>
<tr>
  <th>Status</th>
  <td>' . ucfirst($order['order_status']) . '</td>
  <th>Payment</th>
  <td>' . ucfirst($order['payment_method'] ?? 'N/A') . ' (' . ucfirst($order['payment_status'] ?? 'N/A') . ')</td>
</tr>
<tr>
  <th>Transaction ID</th>
  <td colspan="3">' . htmlspecialchars($order['transaction_id'] ?? 'N/A') . '</td>
</tr>
</table>
';
$pdf->writeHTML($order_info, true, false, true, false, '');

// ======================
// Customer Info
// ======================
$shipping_address = trim(($order['shipping_address_line1'] ?? '') . ' ' . ($order['shipping_address_line2'] ?? ''));
$shipping_city    = $order['shipping_city'] ?? '';
$shipping_state   = $order['shipping_state'] ?? '';
$shipping_postal  = $order['shipping_postal_code'] ?? '';

if (empty($shipping_address) && empty($shipping_city) && empty($shipping_state)) {
    $shipping_address = trim(($order['billing_address_line1'] ?? '') . ' ' . ($order['billing_address_line2'] ?? ''));
    $shipping_city    = $order['billing_city'] ?? '';
    $shipping_state   = $order['billing_state'] ?? '';
    $shipping_postal  = $order['billing_postal_code'] ?? '';
}

$customer_info = '
<style>
.info-table td { padding: 6px; border: 1px solid #ccc; }
.info-table th { background-color: #f2f2f2; padding: 6px; border: 1px solid #ccc; }
</style>

<h3 style="margin-top:10px;">Customer Information</h3>
<table class="info-table" cellpadding="4" cellspacing="0">
<tr>
  <th width="25%">Name</th>
  <td width="75%">' . htmlspecialchars(($order['shipping_first_name'] ?? $order['billing_first_name']) . ' ' . ($order['shipping_last_name'] ?? $order['billing_last_name'])) . '</td>
</tr>
<tr>
  <th>Email</th>
  <td>' . htmlspecialchars($order['contact_email'] ?? '') . '</td>
</tr>
<tr>
  <th>Phone</th>
  <td>' . htmlspecialchars($order['contact_phone'] ?? '') . '</td>
</tr>
<tr>
  <th>Shipping Address</th>
  <td>' . htmlspecialchars($shipping_address) . ', ' . htmlspecialchars($shipping_city) . ', ' . htmlspecialchars($shipping_state) . ', ' . htmlspecialchars($shipping_postal) . '</td>
</tr>
</table>
';
$pdf->writeHTML($customer_info, true, false, true, false, '');

// ======================
// Order Items Table
// ======================
$pdf->Ln(5);
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 8, 'Order Items', 0, 1);

$itemsHtml = '
<style>
.items-table th {
    background-color:#2980b9; 
    color:white; 
    font-weight:bold; 
    text-align:center;
    padding:6px;
}
.items-table td {
    border:1px solid #ccc;
    padding:5px;
}
</style>

<table class="items-table" cellpadding="4" cellspacing="0" width="100%">
<tr>
    <th width="15%">SKU</th>
    <th width="45%">Product</th>
    <th width="10%">Qty</th>
    <th width="15%">Unit Price</th>
    <th width="15%">Total</th>
</tr>';

foreach ($order_items as $item) {
    $itemsHtml .= '
    <tr>
        <td style="text-align:center;">' . htmlspecialchars($item['sku']) . '</td>
        <td>' . htmlspecialchars($item['name']) . '</td>
        <td style="text-align:center;">' . (int)($item['quantity'] ?? 0) . '</td>
        <td style="text-align:right;">RM ' . number_format($item['unit_price'] ?? 0, 2) . '</td>
        <td style="text-align:right;">RM ' . number_format($item['total_price'] ?? 0, 2) . '</td>
    </tr>';
}

$itemsHtml .= '</table>';
$pdf->writeHTML($itemsHtml, true, false, true, false, '');

// ======================
// Order Total
// ======================
$pdf->Ln(5);
$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetFillColor(44, 62, 80);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(0, 10, 'Order Summary', 0, 1, 'C', 1);

$pdf->SetTextColor(0, 0, 0);
$summaryHtml = '
<style>
.summary-table th, .summary-table td {
    border:1px solid #ccc; padding:6px;
}
.summary-table th {
    background:#f2f2f2; text-align:left;
}
.summary-table td {
    text-align:right;
}
</style>

<table class="summary-table" cellpadding="4" cellspacing="0" width="100%">
<tr>
  <th width="80%">Total Amount</th>
  <td width="20%">RM ' . number_format($order['total_amount'], 2) . '</td>
</tr>
</table>
';
$pdf->writeHTML($summaryHtml, true, false, true, false, '');

// Footer
$pdf->Ln(10);
$pdf->SetFont('helvetica', 'I', 9);
$pdf->SetTextColor(100, 100, 100);
$pdf->Cell(0, 10, 'Thank you for your purchase! | Generated on ' . date('M j, Y g:i A'), 0, 1, 'C');

// Output PDF
$pdf->Output('invoice_' . $order['order_number'] . '.pdf', 'I');
exit;
