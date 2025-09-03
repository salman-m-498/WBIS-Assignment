<?php
session_start();
require_once '../includes/db.php';

$root = dirname(__DIR__); // one level up from current folder
require_once $root . '/../vendor/autoload.php';

$order_id = $_GET['order_id'];
$user_id = $_SESSION['user_id'];

if (!isset($_GET['order_id']) || !isset($_SESSION['user_id'])) {
    die("Invalid request.");
}


// Get order details
$stmt = $pdo->prepare("
    SELECT o.*, u.username, p.payment_method, p.payment_status, p.transaction_id
    FROM orders o
    JOIN user u ON o.user_id = u.user_id
    LEFT JOIN payments p ON o.order_id = p.order_id
    WHERE o.order_id = ? AND o.user_id = ?
");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Order not found.");
}

// Get order items
$stmt = $pdo->prepare("
    SELECT oi.*, p.name, p.sku
    FROM order_items oi
    JOIN products p ON oi.product_id = p.product_id
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Basic document settings
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('ToyLand Store');
$pdf->SetTitle('Receipt - ' . ($order['order_number'] ?? $orderIdCanonical));
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(15, 20, 15);
$pdf->SetAutoPageBreak(TRUE, 15);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 10);

// Optional logo (place a logo at root/public/assets/logo.png if you want)
$logoPath = $root . '/public/assets/logo.png';
if (file_exists($logoPath)) {
    // x, y, width
    $pdf->Image($logoPath, 15, 12, 30, '', '', '', '', false, 300);
}

// Header bar
$pdf->SetY(10);
$pdf->SetFillColor(52, 152, 219); // #3498db
$pdf->SetTextColor(255,255,255);
$pdf->SetFont('helvetica', 'B', 18);
$pdf->Cell(0, 18, 'ToyLand Store', 0, 1, 'C', 1);
$pdf->Ln(4);

// Reset to black for content
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('helvetica', '', 10);

// Order + Customer block (two columns)
$shippingHtml = '<strong>Shipping Information</strong><br/>' .
    htmlspecialchars($order['shipping_first_name'] . ' ' . $order['shipping_last_name']) . '<br/>' .
    htmlspecialchars($order['shipping_address_line1']) . '<br/>';
if (!empty($order['shipping_address_line2'])) {
    $shippingHtml .= htmlspecialchars($order['shipping_address_line2']) . '<br/>';
}
$shippingHtml .= htmlspecialchars($order['shipping_city'] . ', ' . $order['shipping_state'] . ' ' . $order['shipping_postal_code']) . '<br/>';
if (!empty($order['shipping_area'])) {
      $shippingHtml .= 'Area: ' . htmlspecialchars(ucwords($order['shipping_area'])) . ' Malaysia, ' . htmlspecialchars($order['shipping_state']) . '<br/>';
}
$shippingHtml .= 'Email: ' . htmlspecialchars($order['contact_email']) . '<br/>';
$shippingHtml .= 'Phone: ' . htmlspecialchars($order['contact_phone']) . '<br/>';

$orderInfoHtml = '<table cellpadding="4" style="width:100%;">' .
    '<tr><td><strong>Order:</strong></td><td>' . htmlspecialchars($order['order_number'] ?? $orderIdCanonical) . '</td></tr>' .
    '<tr><td><strong>Transaction:</strong></td><td>' . htmlspecialchars($order['transaction_id']) . '</td></tr>' .
    '<tr><td><strong>Date:</strong></td><td>' . date('M j, Y g:i A', strtotime($order['created_at'])) . '</td></tr>' .
    '<tr><td><strong>Payment Method:</strong></td><td>' . ucfirst(str_replace('_', ' ', $order['payment_method'])) . '</td></tr>' .
    '<tr><td><strong>Payment Status:</strong></td><td>' . ucfirst($order['payment_status']) . '</td></tr>' .
    '</table>';

$twoColHtml = '
<table cellspacing="0" cellpadding="6" style="width:100%; border-collapse:collapse;">
<tr>
    <td style="width:60%; vertical-align:top;">' . $shippingHtml . '</td>
    <td style="width:40%; background-color:#f2f4f8; vertical-align:top;">' . $orderInfoHtml . '</td>
</tr>
</table>
<br/><br/>
';

$pdf->writeHTML($twoColHtml, true, false, true, false, '');

// Items table (styled)
$itemsHtml = '
<style>
    table.items { border-collapse: collapse; width: 100%; }
    table.items th { background-color: #3498db; color: #ffffff; padding: 8px; font-weight: bold; text-align: left; }
    table.items td { border: 1px solid #dddddd; padding: 6px; }
    .text-right { text-align: right; }
</style>

<table class="items" cellpadding="4">
<tr>
    <th style="width:15%;">SKU</th>
    <th style="width:45%;">Product</th>
    <th style="width:10%;">Qty</th>
    <th style="width:15%;" class="text-right">Unit Price</th>
    <th style="width:15%;" class="text-right">Total</th>
</tr>';

$alt = false;
foreach ($order_items as $item) {
    $rowBg = $alt ? ' style="background-color:#f7f7f7;"' : '';
    $itemsHtml .= '<tr' . $rowBg . '>
        <td>' . htmlspecialchars($item['sku']) . '</td>
        <td>' . htmlspecialchars($item['name']) . '</td>
        <td>' . (int)$item['quantity'] . '</td>
        <td class="text-right">RM ' . number_format($item['unit_price'], 2) . '</td>
        <td class="text-right">RM ' . number_format($item['total_price'], 2) . '</td>
    </tr>';
    $alt = !$alt;
}

$itemsHtml .= '</table><br/>';

$pdf->writeHTML($itemsHtml, true, false, true, false, '');

// Totals block (right aligned)
$totalsHtml = '
<table cellpadding="6" style="width:100%; border-collapse:collapse;">
<tr>
    <td style="width:60%;"></td>
    <td style="width:40%;">
        <table cellpadding="4" style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="border: none;">Subtotal:</td>
                <td style="text-align:right; border:none;">RM ' . number_format($order['subtotal'], 2) . '</td>
            </tr>
            <tr>
                <td style="border: none;">Shipping:</td>
                <td style="text-align:right; border:none;">' . ($order['shipping_cost'] == 0 ? 'FREE' : 'RM ' . number_format($order['shipping_cost'], 2)) . '</td>
            </tr>';
if (!empty($order['discount_amount']) && (float)$order['discount_amount'] > 0) {
    $totalsHtml .= '
            <tr>
                <td style="border: none;">Discount:</td>
                <td style="text-align:right; border:none;">-RM ' . number_format($order['discount_amount'], 2) . '</td>
            </tr>';
}
$totalsHtml .= '
            <tr>
                <td style="border: none; background-color:#eafaf1; font-weight:bold; padding:8px;">Total:</td>
                <td style="text-align:right; background-color:#eafaf1; font-weight:bold; padding:8px;">RM ' . number_format($order['total_amount'], 2) . '</td>
            </tr>
        </table>
    </td>
</tr>
</table>
<br/><br/>';

$pdf->writeHTML($totalsHtml, true, false, true, false, '');

// Notes / footer
$footerHtml = '<hr/><p style="font-size:10px; color:#666666;">If you have any questions, contact us at support@toylandstore.com. This receipt is computer generated and does not require a signature.</p>';
$pdf->writeHTML($footerHtml, true, false, true, false, '');

// Output
$pdf->Output('receipt_' . ($order['order_number'] ?? $orderIdCanonical) . '.pdf', 'I');
exit;
