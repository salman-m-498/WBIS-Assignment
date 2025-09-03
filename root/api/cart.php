<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../includes/db.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch($action) {
    case 'add':
        addToCart($pdo);
        break;
    case 'update':
        updateCartItem($pdo);
        break;
    case 'remove':
        removeFromCart($pdo);
        break;
    case 'get':
        getCartItems($pdo);
        break;
    case 'clear':
        clearCart($pdo);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

function addToCart($pdo) {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        return;
    }

    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'] ?? '';
    $quantity = max(1, (int)($_POST['quantity'] ?? 1));

    if (empty($product_id) || $quantity <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid product or quantity']);
        return;
    }

    try {
        // Begin transaction to avoid race conditions (optional but safe)
        $pdo->beginTransaction();

        // Fetch product (lock not guaranteed with MyISAM; InnoDB gives better atomicity)
        $stmt = $pdo->prepare("SELECT product_id, stock_quantity FROM products WHERE product_id = ? AND status = 'active' FOR UPDATE");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            return;
        }

        if ($product['stock_quantity'] < $quantity) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Not enough stock available']);
            return;
        }

        // Check existing cart item
        $stmt = $pdo->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            $new_quantity = $existing['quantity'] + $quantity;
            if ($new_quantity > $product['stock_quantity']) {
                $pdo->rollBack();
                echo json_encode(['success' => false, 'message' => 'Not enough stock available']);
                return;
            }

            $stmt = $pdo->prepare("UPDATE cart SET quantity = ?, updated_at = NOW() WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$new_quantity, $user_id, $product_id]);
        } else {
            if ($quantity > $product['stock_quantity']) {
                $pdo->rollBack();
                echo json_encode(['success' => false, 'message' => 'Not enough stock available']);
                return;
            }
            $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$user_id, $product_id, $quantity]);
        }
        // Commit
        $pdo->commit();

        // Update cart count
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(quantity),0) FROM cart WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $cart_count = (int)$stmt->fetchColumn();
        $_SESSION['cart_count'] = $cart_count;

        echo json_encode(['success' => true, 'message' => 'Product added to cart', 'cart_count' => $cart_count]);

    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        error_log("addToCart error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Server error' .$e->getMessage()]);
        return;
    }
}

function updateCartItem($pdo) {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        return;
    }
    
    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'] ?? '';
    $quantity = (int)($_POST['quantity'] ?? 1);
    
    if (empty($product_id) || $quantity <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid product or quantity']);
        return;
    }
    
    // Check stock
    $stmt = $pdo->prepare("SELECT stock_quantity, sale_price FROM products WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product || $product['stock_quantity'] < $quantity) {
        echo json_encode(['success' => false, 'message' => 'Not enough stock available']);
        return;
    }
    
    // Update cart
    $stmt = $pdo->prepare("UPDATE cart SET quantity = ?, updated_at = NOW() WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$quantity, $user_id, $product_id]);
    
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(quantity),0) FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $cart_count = (int)$stmt->fetchColumn();
    $_SESSION['cart_count'] = $cart_count;

    $item_total = $product['sale_price'] * $quantity;
    
    echo json_encode([
        'success' => true,
        'message' => 'Cart updated',
        'item_total' => number_format($item_total, 2),
        'cart_count' => $cart_count
    ]);
}

function removeFromCart($pdo) {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        return;
    }
    
    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'] ?? '';
    
    if (empty($product_id)) {
        echo json_encode(['success' => false, 'message' => 'Invalid product']);
        return;
    }
    
    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);

    $stmt = $pdo->prepare("SELECT COALESCE(SUM(quantity),0) FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $cart_count = (int)$stmt->fetchColumn();
    $_SESSION['cart_count'] = $cart_count;
    
    echo json_encode(['success' => true, 
    'message' => 'Item removed from cart',
    'cart_count' => $cart_count
]);
}

function getCartItems($pdo) {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        return;
    }
    
    $user_id = $_SESSION['user_id'];
    
    $stmt = $pdo->prepare("
        SELECT c.*, p.name, p.sale_price, p.image, p.stock_quantity 
        FROM cart c 
        JOIN products p ON c.product_id = p.product_id 
        WHERE c.user_id = ?
        ORDER BY c.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $total = 0;
    foreach ($items as &$item) {
        $item['total'] = $item['sale_price'] * $item['quantity'];
        $total += $item['total'];
        $item['image'] = str_replace("root/", "", $item['image']);
    }
    
    echo json_encode([
        'success' => true,
        'items' => $items,
        'total' => $total,
        'count' => count($items)
    ]);
}

function clearCart($pdo) {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        return;
    }
    
    $user_id = $_SESSION['user_id'];
    
    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $_SESSION['cart_count'] = 0;
    
    echo json_encode(['success' => true, 'message' => 'Cart cleared','cart_count' => 0]);
}
?>