<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: admin_login.php');
    exit();
}

require_once '../includes/db.php';

// Handle product actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                try {
                    $stmt = $pdo->prepare("INSERT INTO product (name, description, price, stock, category_id) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([
                        trim($_POST['name']),
                        trim($_POST['description']),
                        floatval($_POST['price']),
                        intval($_POST['stock']),
                        $_POST['category_id']
                    ]);
                    
                    // Handle image upload if present
                    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                        $product_id = $pdo->lastInsertId();
                        $image_path = handleImageUpload($_FILES['image'], $product_id);
                        if ($image_path) {
                            $stmt = $pdo->prepare("UPDATE product SET image = ? WHERE product_id = ?");
                            $stmt->execute([$image_path, $product_id]);
                        }
                    }
                    
                    $_SESSION['success'] = "Product added successfully!";
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Error adding product: " . $e->getMessage();
                }
                break;

            case 'edit':
                try {
                    $stmt = $pdo->prepare("UPDATE product SET name = ?, description = ?, price = ?, stock = ?, category_id = ? WHERE product_id = ?");
                    $stmt->execute([
                        trim($_POST['name']),
                        trim($_POST['description']),
                        floatval($_POST['price']),
                        intval($_POST['stock']),
                        $_POST['category_id'],
                        $_POST['product_id']
                    ]);
                    
                    // Handle image upload if present
                    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                        $image_path = handleImageUpload($_FILES['image'], $_POST['product_id']);
                        if ($image_path) {
                            $stmt = $pdo->prepare("UPDATE product SET image = ? WHERE product_id = ?");
                            $stmt->execute([$image_path, $_POST['product_id']]);
                        }
                    }
                    
                    $_SESSION['success'] = "Product updated successfully!";
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Error updating product: " . $e->getMessage();
                }
                break;

            case 'delete':
                try {
                    $stmt = $pdo->prepare("DELETE FROM product WHERE product_id = ?");
                    $stmt->execute([$_POST['product_id']]);
                    $_SESSION['success'] = "Product deleted successfully!";
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Error deleting product: " . $e->getMessage();
                }
                break;
        }
        header('Location: products.php');
        exit();
    }
}

// Function to handle image upload
function handleImageUpload($file, $product_id) {
    $target_dir = "../assets/images/products/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $new_filename = "product_" . $product_id . "_" . time() . "." . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return "assets/images/products/" . $new_filename;
    }
    return false;
}

// Get sorting parameters
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'name';
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

// Fetch products with category information and sorting
try {
    $stmt = $pdo->prepare("
        SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.category_id 
        ORDER BY p.$sort $order
    ");
    $stmt->execute();
    $products = $stmt->fetchAll();
    
    // Fetch categories for the form
    $stmt = $pdo->prepare("SELECT * FROM categories ORDER BY name");
    $stmt->execute();
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    $_SESSION['error'] = "Error fetching data: " . $e->getMessage();
    $products = [];
    $categories = [];
}

// Page variables
$page_title = "Manage Products";
$page_description = "Add, edit, and delete products";
include '../includes/header.php';
?>

<section class="admin-section products-management">
    <div class="container">
        <div class="section-header">
            <h1>Manage Products</h1>
            <button class="btn btn-primary" onclick="showAddProductModal()">
                <i class="fas fa-plus"></i> Add New Product
            </button>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>
                            <a href="?sort=name&order=<?php echo $sort === 'name' && $order === 'ASC' ? 'DESC' : 'ASC'; ?>">
                                Name <?php echo $sort === 'name' ? ($order === 'ASC' ? '↑' : '↓') : ''; ?>
                            </a>
                        </th>
                        <th>Description</th>
                        <th>
                            <a href="?sort=price&order=<?php echo $sort === 'price' && $order === 'ASC' ? 'DESC' : 'ASC'; ?>">
                                Price <?php echo $sort === 'price' ? ($order === 'ASC' ? '↑' : '↓') : ''; ?>
                            </a>
                        </th>
                        <th>
                            <a href="?sort=stock&order=<?php echo $sort === 'stock' && $order === 'ASC' ? 'DESC' : 'ASC'; ?>">
                                Stock <?php echo $sort === 'stock' ? ($order === 'ASC' ? '↑' : '↓') : ''; ?>
                            </a>
                        </th>
                        <th>Category</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['product_id']); ?></td>
                        <td>
                            <?php if ($product['image']): ?>
                                <img src="../<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width: 50px; height: 50px; object-fit: cover;">
                            <?php else: ?>
                                <span class="no-image">No Image</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><?php echo htmlspecialchars(substr($product['description'], 0, 100)) . '...'; ?></td>
                        <td>$<?php echo number_format($product['price'], 2); ?></td>
                        <td><?php echo htmlspecialchars($product['stock']); ?></td>
                        <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                        <td class="actions">
                            <button class="btn btn-small btn-edit" onclick="showEditProductModal(<?php echo htmlspecialchars(json_encode($product)); ?>)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-small btn-delete" onclick="confirmDeleteProduct(<?php echo $product['product_id']; ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- Add Product Modal -->
<div id="addProductModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Add New Product</h2>
        <form action="products.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add">
            <div class="form-group">
                <label for="name">Product Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" id="price" name="price" step="0.01" min="0" required>
            </div>
            <div class="form-group">
                <label for="stock">Stock</label>
                <input type="number" id="stock" name="stock" min="0" required>
            </div>
            <div class="form-group">
                <label for="category_id">Category</label>
                <select id="category_id" name="category_id" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['category_id']; ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="image">Product Image</label>
                <input type="file" id="image" name="image" accept="image/*">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Add Product</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Product Modal -->
<div id="editProductModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Edit Product</h2>
        <form action="products.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="product_id" id="edit_product_id">
            <div class="form-group">
                <label for="edit_name">Product Name</label>
                <input type="text" id="edit_name" name="name" required>
            </div>
            <div class="form-group">
                <label for="edit_description">Description</label>
                <textarea id="edit_description" name="description" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label for="edit_price">Price</label>
                <input type="number" id="edit_price" name="price" step="0.01" min="0" required>
            </div>
            <div class="form-group">
                <label for="edit_stock">Stock</label>
                <input type="number" id="edit_stock" name="stock" min="0" required>
            </div>
            <div class="form-group">
                <label for="edit_category_id">Category</label>
                <select id="edit_category_id" name="category_id" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['category_id']; ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="edit_image">Product Image</label>
                <input type="file" id="edit_image" name="image" accept="image/*">
                <small>Leave empty to keep current image</small>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update Product</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Product Form -->
<form id="deleteProductForm" action="products.php" method="POST" style="display: none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="product_id" id="delete_product_id">
</form>

<script>
function showAddProductModal() {
    document.getElementById('addProductModal').style.display = 'block';
}

function showEditProductModal(product) {
    document.getElementById('edit_product_id').value = product.product_id;
    document.getElementById('edit_name').value = product.name;
    document.getElementById('edit_description').value = product.description;
    document.getElementById('edit_price').value = product.price;
    document.getElementById('edit_stock').value = product.stock;
    document.getElementById('edit_category_id').value = product.category_id;
    document.getElementById('editProductModal').style.display = 'block';
}

function confirmDeleteProduct(productId) {
    if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
        document.getElementById('delete_product_id').value = productId;
        document.getElementById('deleteProductForm').submit();
    }
}

// Close modal when clicking on X or outside the modal
document.querySelectorAll('.modal .close').forEach(function(closeBtn) {
    closeBtn.onclick = function() {
        this.closest('.modal').style.display = 'none';
    }
});

window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}
</script>

<?php include '../includes/footer.php'; ?>
