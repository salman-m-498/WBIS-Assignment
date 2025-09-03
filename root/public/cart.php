<?php
session_start();
require_once '../includes/db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=cart.php');
    exit;
}

$user_id = $_SESSION['user_id'];

$search = $_GET['search'] ?? '';
$params = [$user_id];

$where = '';
if ($search) {
    $where = " AND (p.name LIKE ? OR p.sku LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$stmt = $pdo->prepare("
    SELECT c.*, p.name, p.sale_price, p.price, p.image, p.stock_quantity, p.sku 
    FROM cart c 
    JOIN products p ON c.product_id = p.product_id 
    WHERE c.user_id = ? $where
    ORDER BY c.created_at DESC
");
$stmt->execute($params);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get cart items
$stmt = $pdo->prepare("
    SELECT c.*, p.name, p.sale_price, p.price, p.image, p.stock_quantity, p.sku 
    FROM cart c 
    JOIN products p ON c.product_id = p.product_id 
    WHERE c.user_id = ?
    ORDER BY c.created_at DESC
");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate totals
$subtotal = 0;
$total_items = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['sale_price'] * $item['quantity'];
    $total_items += $item['quantity'];
}

$shipping = $subtotal >= 150 ? 0 : 8.00;
$discount = 0; // Will be calculated based on promo code
$total = $subtotal + $shipping - $discount;

// Page variables
$page_title = "Shopping Cart";
$page_description = "Review and manage your shopping cart items";
$show_breadcrumb = true;
$breadcrumb_items = [
    ['url' => 'cart.php', 'title' => 'Shopping Cart']
];

include '../includes/header.php';
?>

<!-- Flash message container -->
<div id="flash-message" style="display:none; position: fixed; top: 20px; right: 20px; min-width: 200px; padding: 12px 18px; border-radius: 8px; font-size: 14px; z-index: 9999; box-shadow: 0 4px 8px rgba(0,0,0,0.1);"></div>

<!-- Cart Section -->
<section class="cart-section">
    <div class="container">
        <div class="cart-header">
            <h1>Shopping Cart</h1>
            <p>Review your items and proceed to checkout</p>
        </div>

        <!-- Search Form -->
        <form method="get" action="cart.php" style="margin:20px 0; display:flex; gap:10px; max-width:400px;">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                    placeholder="Search cart items..." style="flex:1; padding:8px; border:1px solid #ccc; border-radius:4px;">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        
        <?php if (empty($cart_items)): ?>
        <!-- Empty Cart -->
        <div class="empty-cart">
            <div class="empty-cart-content">
                <i class="fas fa-shopping-cart"></i>
                <h2>Your cart is empty</h2>
                <p>Looks like you haven't added any items to your cart yet.</p>
                <a href="products.php" class="btn btn-primary">Start Shopping</a>
            </div>
        </div>
        <?php else: ?>
        
    <form method="post" action="checkout.php" id="checkout-form">
        <div class="cart-layout">
            <!-- Cart Items -->
            <div class="cart-items">
                <div style="margin-bottom:10px;">
                    <label>
                        <input type="checkbox" id="select-all" checked> Select All
                    </label>
                </div>
                <div class="cart-table-header">
                    <div class="header-product">Product</div>
                    <div class="header-price">Price</div>
                    <div class="header-quantity">Quantity</div>
                    <div class="header-total">Total</div>
                    <div class="header-actions">Actions</div>
                </div>
                
                <div class="cart-items-list" id="cart-items-container">
                    <?php foreach ($cart_items as $item): ?>
                        <?php $image_path = str_replace("root/", "", $item['image']); ?>
                        <div class="cart-item" data-product-id="<?= $item['product_id'] ?>">
                             <div class="item-select">
                                <input type="checkbox" class="select-item" name="selected_items[]" value="<?= $item['product_id'] ?>" checked>
                                </div>
        
                            <div class="item-product">
                                <div class="item-image">
                                    <img src="/<?= htmlspecialchars($image_path) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                                </div>
                                <div class="item-details">
                                    <h3><a href="product.php?id=<?= $item['product_id'] ?>"><?= htmlspecialchars($item['name']) ?></a></h3>
                                    <p class="item-sku">SKU: <?= htmlspecialchars($item['sku']) ?></p>
                                    <p class="stock-info">Stock: <?= $item['stock_quantity'] ?> available</p>
                                </div>
                            </div>
                            
                            <div class="item-price">
                                <span class="current-price">RM<?= number_format($item['sale_price'], 2) ?></span>
                                <?php if ($item['sale_price'] < $item['price']): ?>
                                    <span class="original-price">RM<?= number_format($item['price'], 2) ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="item-quantity">
                                <div class="quantity-selector">
                                    <button class="quantity-btn minus" data-product-id="<?= $item['product_id'] ?>">-</button>
                                    <input type="number" class="quantity-input" value="<?= $item['quantity'] ?>" 
                                           min="1" max="<?= $item['stock_quantity'] ?>" data-product-id="<?= $item['product_id'] ?>">
                                    <button class="quantity-btn plus" data-product-id="<?= $item['product_id'] ?>">+</button>
                                </div>
                            </div>
                            
                            <div class="item-total">
                                <span class="total-price">RM<?= number_format($item['sale_price'] * $item['quantity'], 2) ?></span>
                            </div>
                            
                            <div class="item-actions">
                                <button class="remove-item" data-product-id="<?= $item['product_id'] ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <button class="save-for-later" data-product-id="<?= $item['product_id'] ?>">
                                    <i class="far fa-heart"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>

                     <?php if (empty($cart_items)): ?>
                         <div style="padding:20px; text-align:center;">
                            <p>No items found in your cart for "<?= htmlspecialchars($search) ?>".</p>
                            <a href="cart.php" class="btn btn-outline">Reset Search</a>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Cart Actions -->
                <div class="cart-actions">
                    <div class="cart-actions-left">
                        <a href="products.php" class="btn btn-outline">
                            <i class="fas fa-arrow-left"></i> Continue Shopping
                        </a>
                        <button class="btn btn-outline" id="clear-cart">
                            <i class="fas fa-trash"></i> Clear Cart
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Cart Summary -->
            <div class="cart-summary">
                <div class="summary-header">
                    <h3>Order Summary</h3>
                </div>
                
                <div class="summary-content">
                    <div class="summary-row">
                        <span>Subtotal (<span id="selected-items-count"><?= $total_items ?></span> items):</span>
                        <span id="subtotal">RM<?= number_format($subtotal, 2) ?></span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Shipping:</span>
                        <span class="shipping-cost" id="shipping-cost">
                            <?php if ($shipping == 0): ?>
                                <span style="color: green;">FREE</span>
                            <?php else: ?>
                                RM<?= number_format($shipping, 2) ?>
                            <?php endif; ?>
                        </span>
                    </div>
                    
                    <div class="summary-row discount" id="discount-row" style="<?= $discount > 0 ? '' : 'display:none;' ?>">
                        <span>Discount:</span>
                        <span id="discount-amount">-RM<?= number_format($discount, 2) ?></span>
                    </div>
                    
                    <div class="summary-row total">
                         <span>Total:</span>
                         <span id="total-amount">RM<?= number_format($total, 2) ?></span>
                    </div>
                </div>
                
                <!-- Promo Code -->
                <div class="promo-code">
                    <h4>Have a Promo Code?</h4>
                    <div class="promo-form">
                        <input type="text" placeholder="Enter promo code" id="promo-code">
                        <button type="button" class="btn btn-outline" id="apply-promo">Apply</button>
                    </div>
                    <div id="promo-message" style="margin-top: 10px; font-size: 14px;"></div>
                </div>
                
                <!-- Checkout Button -->
                <div class="checkout-section">
                <button type="submit" class="btn btn-primary btn-large">
                        <i class="fas fa-lock"></i> Proceed to Checkout
                </button>
                    <p class="secure-checkout">
                        <i class="fas fa-shield-alt"></i> Secure checkout powered by SSL
                    </p>
                </div>
                
                <!-- Payment Methods -->
                <div class="payment-methods">
                    <h4>We Accept:</h4>
                   <div class="payment-icons">
                        <img src="https://img.icons8.com/color/48/visa.png" alt="Visa">
                        <img src="https://img.icons8.com/color/48/mastercard.png" alt="Mastercard">
                        <img src="https://img.icons8.com/color/48/amex.png" alt="Amex">
                         <img
                            src="https://logowik.com/content/uploads/images/touchn-go-ewallet4107.logowik.com.webp"
                            alt="Touch 'n Go eWallet"
                        >
                        </div>
                </div>
            </div>
        </div>
        </form>
        <?php endif; ?>
</section>

<script>
// Cart Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    
    // Show flash message function
    function showFlashMessage(message, type = 'success') {
        const flash = document.getElementById('flash-message');
        if (!flash) return;
        
        flash.textContent = message;
        flash.style.display = 'block';
        flash.style.backgroundColor = (type === 'success') ? '#d4edda' : '#f8d7da';
        flash.style.color = (type === 'success') ? '#155724' : '#721c24';
        flash.style.border = (type === 'success') ? '1px solid #c3e6cb' : '1px solid #f5c6cb';

        setTimeout(() => {
            flash.style.display = 'none';
        }, 3000);
    }
    
    // Make showFlashMessage available globally for cart.js
    window.showFlashMessage = showFlashMessage;
    
    // Initialize: check all items and update totals on page load
    function initializeCart() {
        const selectAllCheckbox = document.getElementById('select-all');
        const itemCheckboxes = document.querySelectorAll('.select-item');
        
        if (selectAllCheckbox && itemCheckboxes.length > 0) {
            selectAllCheckbox.checked = true;
            itemCheckboxes.forEach(cb => {
                cb.checked = true;
            });
            updateCartTotals();
        }
    }
    
    // Update cart totals
    function updateCartTotals() {
        let subtotal = 0;
        let selectedItems = 0;

        const items = document.querySelectorAll('.cart-item');
        
        items.forEach(item => {
            const checkbox = item.querySelector('.select-item');
            if (checkbox && checkbox.checked) {
                const priceElement = item.querySelector('.current-price');
                const quantityElement = item.querySelector('.quantity-input');
                
                if (priceElement && quantityElement) {
                    const price = parseFloat(priceElement.textContent.replace('RM', ''));
                    const quantity = parseInt(quantityElement.value);
                    const total = price * quantity;

                    const totalElement = item.querySelector('.total-price');
                    if (totalElement) {
                        totalElement.textContent = 'RM' + total.toFixed(2);
                    }
                    
                    subtotal += total;
                    selectedItems += quantity;
                }
            }
        });

        // Update summary UI
        const selectedCountElement = document.getElementById('selected-items-count');
        const subtotalElement = document.getElementById('subtotal');
        
        if (selectedCountElement) selectedCountElement.textContent = selectedItems;
        if (subtotalElement) subtotalElement.textContent = 'RM' + subtotal.toFixed(2);

        // Shipping logic
        let shipping = 0;
        if (subtotal > 0) {
            shipping = subtotal >= 150 ? 0 : 8.00;
        }

        const shippingElement = document.getElementById('shipping-cost');
        if (shippingElement) {
            if (shipping === 0 && subtotal > 0) {
                shippingElement.innerHTML = '<span style="color: green;">FREE</span>';
            } else {
                shippingElement.textContent = 'RM' + shipping.toFixed(2);
            }
        }

        // Discount logic
        const discountElement = document.getElementById('discount-amount');
        const discount = discountElement ? 
            parseFloat(discountElement.textContent.replace('-RM', '') || 0) : 0;

        const total = subtotal + shipping - discount;
        const totalElement = document.getElementById('total-amount');
        if (totalElement) {
            totalElement.textContent = 'RM' + total.toFixed(2);
        }
        
        // Update select all checkbox state
        const allCheckboxes = document.querySelectorAll('.select-item');
        const checkedCheckboxes = document.querySelectorAll('.select-item:checked');
        const selectAllCheckbox = document.getElementById('select-all');
        
        if (selectAllCheckbox) {
            if (checkedCheckboxes.length === 0) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            } else if (checkedCheckboxes.length === allCheckboxes.length) {
                selectAllCheckbox.checked = true;
                selectAllCheckbox.indeterminate = false;
            } else {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = true;
            }
        }
    }
    
    // Initialize cart on page load
    initializeCart();
    
    // Event listeners for checkboxes
    document.querySelectorAll('.select-item').forEach(cb => {
        cb.addEventListener('change', updateCartTotals);
    });

    // Quantity change handlers
    document.querySelectorAll('.quantity-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            const input = document.querySelector(`input[data-product-id="${productId}"]`);
            const isPlus = this.classList.contains('plus');
            const currentVal = parseInt(input.value);
            const max = parseInt(input.getAttribute('max'));
            
            let newVal = isPlus ? currentVal + 1 : currentVal - 1;
            if (newVal < 1) newVal = 1;
            if (newVal > max) newVal = max;
            
            input.value = newVal;
            updateQuantity(productId, newVal);
        });
    });
    
    // Quantity input change
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            const productId = this.dataset.productId;
            const quantity = parseInt(this.value);
            updateQuantity(productId, quantity);
        });
    });
    
    // Update quantity function
    function updateQuantity(productId, quantity) {
        fetch('../api/cart.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `action=update&product_id=${productId}&quantity=${quantity}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                updateCartTotals();
                showFlashMessage('Cart updated');

                const cartCountEl = document.getElementById('cart-count') || document.querySelector('.cart-count');
                if (cartCountEl && typeof data.cart_count !== 'undefined') {
                    cartCountEl.textContent = data.cart_count;
                }
            } else {
                showFlashMessage(data.message, 'error');
            }
        })
        .catch(err => {
            console.error(err);
            showFlashMessage('Error updating cart', 'error');
        });
    }
    
    // Remove item handlers
    document.querySelectorAll('.remove-item').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            
            if (confirm('Are you sure you want to remove this item?')) {
                fetch('../api/cart.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `action=remove&product_id=${productId}`
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const itemElement = document.querySelector(`[data-product-id="${productId}"]`);
                        if (itemElement) {
                            itemElement.remove();
                        }
                        updateCartTotals();
                        showFlashMessage('Item removed from cart');

                    // ✅ Update cart icon count
                    const cartCountEl = document.getElementById('cart-count') || document.querySelector('.cart-count');
                    if (cartCountEl && typeof data.cart_count !== 'undefined') {
                        cartCountEl.textContent = data.cart_count;
                    }

                    if (document.querySelectorAll('.cart-item').length === 0) {
                        location.reload();
                    }
                    } else {
                        showFlashMessage(data.message, 'error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    showFlashMessage('Error removing item', 'error');
                });
            }
        });
    });
    
    // Clear cart
    document.getElementById('clear-cart')?.addEventListener('click', function(e) {
        e.preventDefault();
        if (confirm('Are you sure you want to clear your entire cart?')) {
            fetch('../api/cart.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=clear'
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const cartCountEl = document.getElementById('cart-count') || document.querySelector('.cart-count');
                    if (cartCountEl) {
                        cartCountEl.textContent = 0;
                    }

                    location.reload();

                } else {
                    showFlashMessage(data.message, 'error');
                }
            })
            .catch(err => {
                console.error(err);
                showFlashMessage('Error clearing cart', 'error');
            });
        }
    });
    
    // Apply promo code
    document.getElementById('apply-promo')?.addEventListener('click', function() {
        const promoCode = document.getElementById('promo-code').value.trim();
        if (!promoCode) {
            showFlashMessage('Please enter a promo code', 'error');
            return;
        }
        
        // Simulate promo code validation (you can implement real validation)
        const validCodes = {
            'SAVE10': 10,
            'WELCOME15': 15,
            'NEWUSER20': 20
        };
        
        if (validCodes[promoCode.toUpperCase()]) {
            const discountAmount = validCodes[promoCode.toUpperCase()];
            const discountRow = document.getElementById('discount-row');
            const discountAmountElement = document.getElementById('discount-amount');
            
            if (discountRow) discountRow.style.display = 'flex';
            if (discountAmountElement) discountAmountElement.textContent = '-RM' + discountAmount.toFixed(2);
            
            const totalElement = document.getElementById('total-amount');
            if (totalElement) {
                const currentTotal = parseFloat(totalElement.textContent.replace('RM', ''));
                const newTotal = currentTotal - discountAmount;
                totalElement.textContent = 'RM' + newTotal.toFixed(2);
            }
            
            const promoMessage = document.getElementById('promo-message');
            const promoCodeInput = document.getElementById('promo-code');
            
            if (promoMessage) promoMessage.innerHTML = '<span style="color: green;">Promo code applied successfully!</span>';
            if (promoCodeInput) promoCodeInput.disabled = true;
            
            this.disabled = true;
            this.textContent = 'Applied';
            
            showFlashMessage('Promo code applied!');
        } else {
            const promoMessage = document.getElementById('promo-message');
            if (promoMessage) promoMessage.innerHTML = '<span style="color: red;">Invalid promo code</span>';
            showFlashMessage('Invalid promo code', 'error');
        }
    });
    
    // Save for later (add to wishlist)
    document.querySelectorAll('.save-for-later').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            
            fetch('../member/toggle-wishlist.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `product_id=${productId}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showFlashMessage('Item saved to wishlist');
                } else {
                    showFlashMessage(data.message || 'Error saving item', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                showFlashMessage('Error saving item', 'error');
            });
        });
    });

    // Enhanced checkout form validation
    document.getElementById('checkout-form')?.addEventListener('submit', function(e) {
        const checkedItems = document.querySelectorAll('.select-item:checked');
        
        console.log('Checkout form submitted. Selected items:', checkedItems.length);
        
        if (checkedItems.length === 0) {
            e.preventDefault();
            alert('Please select at least one item before proceeding to checkout.');
            return false;
        }
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            submitBtn.disabled = true;
        }
        
        // Don't prevent default - let the form submit naturally
        return true;
    });

    // "Select All" checkbox with improved logic
    document.getElementById('select-all')?.addEventListener('change', function() {
        const isChecked = this.checked;
        document.querySelectorAll('.select-item').forEach(cb => {
            cb.checked = isChecked;
        });
        updateCartTotals();
    });
});
</script>

<?php include '../includes/footer.php'; ?>