<?php
require_once '../includes/db.php';
$root = dirname(__DIR__); // one level up from current folder
require_once $root . '/../vendor/autoload.php';

// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Send status update email to customer using PHPMailer
 */
function sendStatusEmail($order, $order_items) {
    $mail = new PHPMailer(true);
    
    try {
        if ($order['order_status'] === 'cancel_requested') {
            return true;
        }
        // Server settings
        $mail->isSMTP(); 
        $mail->Host       = 'smtp.gmail.com';           
        $mail->SMTPAuth   = true;
        $mail->Username   = 'toylandstore97@gmail.com';    
        $mail->Password   = 'jowu egyx vsga cghe';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        // Recipients
        $mail->setFrom('toylandstore97@gmail.com', 'ToyLand Store');
        $mail->addAddress($order['contact_email'], 
                         $order['shipping_first_name'] . ' ' . $order['shipping_last_name']);
        $mail->addReplyTo('support@toyland.com', 'ToyLand Support');
        
        // Content
        $mail->isHTML(true);
        
        $customer_name = $order['shipping_first_name'] . ' ' . $order['shipping_last_name'];
        $order_number = $order['order_number'];
        $status = ucfirst($order['order_status']);
        
        // Email subject based on status
        $subjects = [
            'pending' => 'Order Confirmation',
            'processing' => 'Order Being Processed',
            'shipped' => 'Order Shipped - Tracking Information',
            'delivered' => 'Order Delivered',
            'cancelled' => 'Order Cancelled'
        ];
        
        $mail->Subject = ($subjects[$order['order_status']] ?? 'Order Status Update') . " - Order #$order_number";
        $mail->Body = generateEmailContent($order, $order_items, $customer_name, $status);
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Email send failed: {$mail->ErrorInfo}");
        return false;
    }
}

/**
 * Alternative: Send email using basic mail() function (fallback)
 */
function sendStatusEmailBasic($order, $order_items) {
    $to = $order['contact_email'];
    $customer_name = $order['shipping_first_name'] . ' ' . $order['shipping_last_name'];
    $order_number = $order['order_number'];
    $status = ucfirst($order['order_status']);

    if ($order['order_status'] === 'cancel_requested') {
    return true;
}
    
    // Email subject based on status
    $subjects = [
        'pending' => 'Order Confirmation',
        'processing' => 'Order Being Processed',
        'shipped' => 'Order Shipped - Tracking Information',
        'delivered' => 'Order Delivered',
        'cancelled' => 'Order Cancelled'
    ];
    
    $subject = ($subjects[$order['order_status']] ?? 'Order Status Update') . " - Order #$order_number";
    
    // Generate email content
    $message = generateEmailContent($order, $order_items, $customer_name, $status);
    
    // Email headers
    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        'From: ToyLandStore <noreply@toyland.com>',
        'Reply-To: support@toyland.com',
        'X-Mailer: PHP/' . phpversion()
    ];
    
    return mail($to, $subject, $message, implode("\r\n", $headers));
}

/**
 * Generate HTML email content based on order status
 */
function generateEmailContent($order, $order_items, $customer_name, $status) {
    $order_number = $order['order_number'];
    $total_amount = number_format($order['total_amount'], 2);
    
    // Start building email HTML
    $html = getEmailHeader();
    
    $html .= "<h1>Order Status Update</h1>";
    $html .= "<p>Dear $customer_name,</p>";
    
    // Status-specific content
    switch ($order['order_status']) {
        case 'shipped':
            $html .= getShippedEmailContent($order);
            break;
        case 'delivered':
            $html .= getDeliveredEmailContent($order);
            break;
        case 'processing':
            $html .= getProcessingEmailContent($order);
            break;
        case 'cancelled':
            $html .= getCancelledEmailContent($order);
            break;
        default:
            $html .= "<p>Your order #$order_number status has been updated to: <strong>$status</strong></p>";
    }
    
    // Add order details
    $html .= getOrderDetailsSection($order, $order_items);
    
    // Add footer
    $html .= getEmailFooter();
    
    return $html;
}

/**
 * Generate shipped status email content with tracking info
 */
function getShippedEmailContent($order) {
    $order_number = $order['order_number'];
    $courier = formatCourierName($order['shipping_courier']);
    $tracking_number = $order['tracking_number'];
    $tracking_url = getTrackingUrl($order['shipping_courier'], $tracking_number);
    
    $html = "<p>Great news! Your order #$order_number has been shipped and is on its way to you.</p>";
    
    $html .= '<div style="background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px; padding: 20px; margin: 20px 0;">';
    $html .= "<h3 style='margin-top: 0; color: #28a745;'>Shipping Information</h3>";
    $html .= "<p><strong>Courier:</strong> $courier</p>";
    $html .= "<p><strong>Tracking Number:</strong> $tracking_number</p>";
    
    if ($tracking_url) {
        $html .= "<p><a href='$tracking_url' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 10px;'>Track Your Package</a></p>";
    }
    
    $html .= "</div>";
    
    $html .= "<p>Your order should arrive within the estimated delivery timeframe. You can use the tracking number above to monitor your package's progress.</p>";
    
    return $html;
}

/**
 * Generate delivered status email content
 */
function getDeliveredEmailContent($order) {
    $order_number = $order['order_number'];
    
    $html = "<p>Wonderful! Your order #$order_number has been successfully delivered.</p>";
    $html .= "<p>We hope you're satisfied with your purchase. If you have any questions or concerns, please don't hesitate to contact our customer support team.</p>";
    $html .= "<p>Thank you for choosing us for your shopping needs!</p>";
    
    return $html;
}

/**
 * Generate processing status email content
 */
function getProcessingEmailContent($order) {
    $order_number = $order['order_number'];
    
    $html = "<p>Your order #$order_number is currently being processed by our team.</p>";
    $html .= "<p>We're carefully preparing your items for shipment. You'll receive another email with tracking information once your order has been shipped.</p>";
    $html .= "<p>Thank you for your patience!</p>";
    
    return $html;
}

function formatPaymentMethod($method) {
    if (empty($method)) return 'Original Payment Method';
    $m = strtolower($method);
    // common normalizations
    if (strpos($m, 'credit') !== false) return 'Credit Card';
    if (strpos($m, 'ewallet') !== false) return 'E WALLET';

    return ucwords(str_replace('_',' ', $method));
}


/**
 * Generate cancelled status email content
 */
function getCancelledEmailContent($order) {
    $order_number = $order['order_number'];
    
    $html = "<p>We regret to inform you that your order #$order_number has been cancelled.</p>";

    if (!empty($order['refund_amount'])) {
        $refund_amount = number_format($order['refund_amount'], 2);

        if (!empty($order['refund_method']) && $order['refund_method'] === 'original') {
        $refund_method = formatPaymentMethod($order['payment_method'] ?? '');
    } elseif (!empty($order['refund_method']) && $order['refund_method'] === 'manual') {
        $refund_method = 'Manual Transfer';
    } else {
        $refund_method = ucwords(str_replace('_',' ', $order['refund_method'] ?? 'Payment Method'));
    }

        
        $html .= "<p>A refund of <strong>RM$refund_amount</strong> ";
        $html .= "via <strong>$refund_method</strong> has been processed.</p>";
        $html .= "<p>The refund should appear within 3 to 5 business days depending on your bank/payment provider.</p>";
    }

    $html .= "<p>If you have any questions, please contact our support team.</p>";

    return $html;
}

/**
 * Generate order details section for email
 */
function getOrderDetailsSection($order, $order_items) {
    $order_number = $order['order_number'];
    $order_date = date('F j, Y', strtotime($order['created_at']));
    $total_amount = number_format($order['total_amount'], 2);
    
    $html = '<div style="border-top: 2px solid #e9ecef; margin-top: 30px; padding-top: 20px;">';
    $html .= "<h3>Order Details</h3>";
    $html .= "<p><strong>Order Number:</strong> $order_number</p>";
    $html .= "<p><strong>Order Date:</strong> $order_date</p>";
    
    // Order items table
    $html .= '<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">';
    $html .= '<thead>';
    $html .= '<tr style="background: #f8f9fa;">';
    $html .= '<th style="border: 1px solid #ddd; padding: 12px; text-align: left;">Product</th>';
    $html .= '<th style="border: 1px solid #ddd; padding: 12px; text-align: center;">Qty</th>';
    $html .= '<th style="border: 1px solid #ddd; padding: 12px; text-align: right;">Price</th>';
    $html .= '<th style="border: 1px solid #ddd; padding: 12px; text-align: right;">Total</th>';
    $html .= '</tr>';
    $html .= '</thead>';
    $html .= '<tbody>';
    
    foreach ($order_items as $item) {
        $item_total = $item['quantity'] * $item['unit_price'];
        $html .= '<tr>';
        $html .= '<td style="border: 1px solid #ddd; padding: 12px;">' . htmlspecialchars($item['product_name']) . '</td>';
        $html .= '<td style="border: 1px solid #ddd; padding: 12px; text-align: center;">' . $item['quantity'] . '</td>';
        $html .= '<td style="border: 1px solid #ddd; padding: 12px; text-align: right;">RM' . number_format($item['unit_price'], 2) . '</td>';
        $html .= '<td style="border: 1px solid #ddd; padding: 12px; text-align: right;">RM' . number_format($item_total, 2) . '</td>';
        $html .= '</tr>';
    }
    
    $html .= '</tbody>';
    $html .= '<tfoot>';
    $html .= '<tr>';
    $html .= '<td colspan="3" style="border: 1px solid #ddd; padding: 12px; text-align: right;"><strong>Subtotal:</strong></td>';
    $html .= '<td style="border: 1px solid #ddd; padding: 12px; text-align: right;"><strong>RM' . number_format($order['subtotal'], 2) . '</strong></td>';
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<td colspan="3" style="border: 1px solid #ddd; padding: 12px; text-align: right;"><strong>Shipping:</strong></td>';
    $html .= '<td style="border: 1px solid #ddd; padding: 12px; text-align: right;"><strong>RM' . number_format($order['shipping_cost'], 2) . '</strong></td>';
    $html .= '</tr>';
    if ($order['discount_amount'] > 0) {
        $html .= '<tr>';
        $html .= '<td colspan="3" style="border: 1px solid #ddd; padding: 12px; text-align: right;"><strong>Discount:</strong></td>';
        $html .= '<td style="border: 1px solid #ddd; padding: 12px; text-align: right;"><strong>-RM' . number_format($order['discount_amount'], 2) . '</strong></td>';
        $html .= '</tr>';
    }
    $html .= '<tr style="background: #f8f9fa;">';
    $html .= '<td colspan="3" style="border: 1px solid #ddd; padding: 12px; text-align: right;"><strong>Total:</strong></td>';
    $html .= '<td style="border: 1px solid #ddd; padding: 12px; text-align: right;"><strong>RM' . $total_amount . '</strong></td>';
    $html .= '</tr>';
    $html .= '</tfoot>';
    $html .= '</table>';
    
    // Shipping address
    if (!empty($order['address_line_1'])) {
        $html .= "<h4>Shipping Address:</h4>";
        $html .= "<p>";
        $html .= htmlspecialchars($order['shipping_first_name'] . ' ' . $order['shipping_last_name']) . "<br>";
        $html .= htmlspecialchars($order['address_line_1']) . "<br>";
        if (!empty($order['address_line_2'])) {
            $html .= htmlspecialchars($order['address_line_2']) . "<br>";
        }
        $html .= htmlspecialchars($order['city']) . ", " . htmlspecialchars($order['state']) . " " . htmlspecialchars($order['postal_code']) . "<br>";
        $html .= ($order['shipping_area'] === 'West' ? 'West Malaysia' : 'East Malaysia');
        $html .= "</p>";
    }
    
    $html .= "</div>";
    
    return $html;
}

/**
 * Get email header HTML
 */
function getEmailHeader() {
    return '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Order Status Update</title>
    </head>
    <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
            <div style="text-align: center; margin-bottom: 30px;">
                <h2 style="color: #007bff; margin: 0;">ToyLandStore</h2>
                <p style="color: #6c757d; margin: 5px 0 0 0;">Your trusted online store</p>
            </div>
    ';
}

/**
 * Get email footer HTML
 */
function getEmailFooter() {
    return '
            <div style="border-top: 1px solid #e9ecef; margin-top: 30px; padding-top: 20px; text-align: center; color: #6c757d; font-size: 14px;">
                <p>If you have any questions, please contact our support team:</p>
                <p>Email: <a href="mailto:support@ytoyland.com">support@toyland.com</a><br>
                Phone: +60 12-345 6789</p>
                <p style="margin-top: 20px; font-size: 12px;">
                    © 2024 ToyLand. All rights reserved.<br>
                    This is an automated email, please do not reply directly to this email.
                </p>
            </div>
        </div>
    </body>
    </html>
    ';
}

/**
 * Format courier name for display
 */
function formatCourierName($courier) {
    $courier_names = [
        'pos_laju' => 'Pos Laju',
        'gdex' => 'GDEX',
        'dhl' => 'DHL',
        'fedex' => 'FedEx',
        'citylink' => 'City-Link'
    ];
    
    return $courier_names[$courier] ?? ucfirst(str_replace('_', ' ', $courier));
}

/**
 * Get tracking URL for courier
 */
function getTrackingUrl($courier, $tracking_number) {
    $tracking_urls = [
        'pos_laju' => "https://www.pos.com.my/postal-services/quick-access?track-trace=$tracking_number",
        'gdex' => "https://www.gdexpress.com/track/$tracking_number",
        'dhl' => "https://www.dhl.com/my-en/home/tracking/tracking-express.html?submit=1&tracking-id=$tracking_number",
        'fedex' => "https://www.fedex.com/fedextrack/?tracknumbers=$tracking_number",
        'citylink' => "https://www.citylinkexpress.com/track-parcel/?tracking_no=$tracking_number"
    ];
    
    return $tracking_urls[$courier] ?? null;
}

function sendOrderStatusEmail($pdo, $order_id) {
    // Fetch order
    $stmt = $pdo->prepare("
        SELECT o.*, 
               u.username, u.email,
               up.first_name AS shipping_first_name, up.last_name AS shipping_last_name, up.phone,
               pay.payment_method, pay.payment_status, pay.transaction_id
        FROM orders o
        JOIN user u ON o.user_id = u.user_id
        LEFT JOIN user_profiles up ON u.user_id = up.user_id
        LEFT JOIN payments pay ON o.payment_id = pay.payment_id
        WHERE o.order_id = ?
    ");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($order) {
        // Fetch items
        $item_stmt = $pdo->prepare("
            SELECT oi.*, p.name AS product_name, p.sku, p.image
            FROM order_items oi
            JOIN products p ON oi.product_id = p.product_id
            WHERE oi.order_id = ?
        ");
        $item_stmt->execute([$order_id]);
        $order_items = $item_stmt->fetchAll(PDO::FETCH_ASSOC);

        // Send email using the existing function
        sendStatusEmail($order, $order_items);
    }
}
?>