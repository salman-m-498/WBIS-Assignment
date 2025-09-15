<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header('Location: admin_login.php');
    exit();
}

require_once '../includes/db.php';

 // --- Handle Delete ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];

    try {
      //  1. Fetch the product to get the main image path
        $stmt = $pdo->prepare("SELECT image FROM products WHERE product_id = ?");
        $stmt->execute([$delete_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        // 2. Delete main image file (skip placeholder)
        if (!empty($product['image']) && strpos($product['image'], 'no-image.png') === false) {
            $mainImagePath = __DIR__ . '/../' . $product['image'];
            if (file_exists($mainImagePath)) {
                unlink($mainImagePath);
            }
        }

        // 3. Delete gallery images (files + DB records)
        $stmt = $pdo->prepare("SELECT image_path FROM product_images WHERE product_id = ?");
        $stmt->execute([$delete_id]);
        $images = $stmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($images as $img) {
            $filePath = __DIR__ . '/../' . $img;
            if ($img && file_exists($filePath)) {
                unlink($filePath);
            }
        }
        $pdo->prepare("DELETE FROM product_images WHERE product_id = ?")->execute([$delete_id]);

        // 4. Delete product record
        $pdo->prepare("DELETE FROM products WHERE product_id = ?")->execute([$delete_id]);

        $_SESSION['success'] = "Product deleted successfully!";
    } catch (Throwable $e) {
        $_SESSION['error'] = "Error deleting product: " . $e->getMessage();
    }

    header("Location: products.php");
    exit;
}


// --- Handle Search & Sort ---
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'newest';
$category_filter = $_GET['category'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 15; 
$offset = ($page - 1) * $per_page;

$where = [];
$params = [];
if ($search) {
    $where[] = "(p.name LIKE ? OR p.description LIKE ? OR c.name LIKE ? OR p.product_id LIKE ?)";
    $params = ["%$search%", "%$search%", "%$search%", "%$search%"];
}

if ($category_filter) {
     // Find all subcategories if it's a main category
    $sub_stmt = $pdo->prepare("SELECT category_id FROM categories WHERE parent_id = ?");
    $sub_stmt->execute([$category_filter]);
    $subcategories = $sub_stmt->fetchAll(PDO::FETCH_COLUMN);

    if ($subcategories) {
        // Include main category + its subcategories
        $placeholders = implode(',', array_fill(0, count($subcategories) + 1, '?'));
        $where[] = "p.category_id IN ($placeholders)";
        $params = array_merge($params, [$category_filter], $subcategories);
    } else {
        // No subcategories, just filter by this category
        $where[] = "p.category_id = ?";
        $params[] = $category_filter;
    }
}

$where_sql = $where ? "WHERE " . implode(" AND ", $where) : "";

switch ($sort) {
    case 'oldest':
        $orderBy = "p.created_at ASC";
        break;
    case 'name_asc':
        $orderBy = "p.name ASC";
        break;
    case 'name_desc':
        $orderBy = "p.name DESC";
        break;
    case 'price_asc':
        $orderBy = "p.price ASC";
        break;
    case 'price_desc':
        $orderBy = "p.price DESC";
        break;
    case 'stock_asc':
        $orderBy = "p.stock_quantity ASC";
        break;
    case 'stock_desc':
        $orderBy = "p.stock_quantity DESC";
        break;
    default: // newest
        $orderBy = "p.created_at DESC";
        break;
}

// Count total
$count_sql = "SELECT COUNT(*) FROM products p LEFT JOIN categories c ON p.category_id = c.category_id $where_sql";
$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total_products = $count_stmt->fetchColumn();
$total_pages = ceil($total_products / $per_page);

$sql = "SELECT p.*, c.name AS category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.category_id
        $where_sql
        ORDER BY $orderBy
        LIMIT $per_page OFFSET $offset";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$cat_stmt = $pdo->query("SELECT category_id, name FROM categories ORDER BY name ASC");
$categories = $cat_stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = "Manage Products";
include '../includes/admin_header.php';
?>

<section class="admin-section products-management">
    <div class="container">
        <div class="section-header">
           <div class="container">
    <h2>Manage Products</h2>

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <!-- Actions -->
    <div class="products-filters-bar">
            <form method="get" action="products.php" class="products-filters-form">
                <select name="category" onchange="this.form.submit()">
    <option value="">All Categories</option>
    <?php foreach ($categories as $cat): ?>
        <option value="<?= $cat['category_id'] ?>" 
            <?= $category_filter == $cat['category_id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat['name']) ?>
        </option>
    <?php endforeach; ?>
</select>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                       placeholder="Search products..." style="flex:1; padding:5px;">
                <select name="sort" onchange="this.form.submit()">
                    <option value="newest" <?= $sort=='newest'?'selected':'' ?>>Newest First</option>
                    <option value="oldest" <?= $sort=='oldest'?'selected':'' ?>>Oldest First</option>
                    <option value="name_asc" <?= $sort=='name_asc'?'selected':'' ?>>Name A–Z</option>
                    <option value="name_desc" <?= $sort=='name_desc'?'selected':'' ?>>Name Z–A</option>
                    <option value="price_asc" <?= $sort=='price_asc'?'selected':'' ?>>Price Low → High</option>
                    <option value="price_desc" <?= $sort=='price_desc'?'selected':'' ?>>Price High → Low</option>
                    <option value="stock_asc" <?= $sort=='stock_asc'?'selected':'' ?>>Stock Low → High</option>
                    <option value="stock_desc" <?= $sort=='stock_desc'?'selected':'' ?>>Stock High → Low</option>
                </select>
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
            <a href="product_add.php" class="btn btn-success">+ Add New</a>
        </div>

    <!-- Products Table -->
    <table class="admin-table">
        <thead>
            <tr>
                <th>Image</th>
                <th>ID</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $p): ?>
                <tr>
                    <td>
                        <?php if ($p['image']): ?>
                            <img src="../<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" width="80">
                        <?php else: ?>
                            <span>No Image</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($p['product_id']) ?></td>
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td><?= htmlspecialchars($p['category_name'] ?? 'Uncategorized') ?></td>
                    <td>RM <?= number_format($p['price'], 2) ?></td>
                    <td><?= isset($p['stock_quantity']) ?(int)$p['stock_quantity'] : 0 ?></td>
                    <td><?= ucfirst($p['status']) ?></td>
                    <td class="action-buttons">
                        <a href="product_edit.php?id=<?= urlencode($p['product_id']) ?>" class="btn-edit">Edit</a>
                        <form method="post" action="products.php" style="display:inline;" 
                              onsubmit="return confirm('Are you sure you want to delete this product?');">
                            <input type="hidden" name="delete_id" value="<?= $p['product_id'] ?>">
                            <button type="submit" class="btn-delete">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php if (empty($products)): ?>
                <tr>
                    <td colspan="8">No products found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
     <!-- Pagination -->
            <div class="pagination">
                <ul class="pagination-list">
                    <?php if ($page > 1): ?>
                        <li><a href="?<?= http_build_query(array_merge($_GET, ['page'=>1])) ?>">First</a></li>
                        <li><a href="?<?= http_build_query(array_merge($_GET, ['page'=>$page-1])) ?>">Prev</a></li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li><a class="<?= $page==$i?'active':'' ?>" href="?<?= http_build_query(array_merge($_GET, ['page'=>$i])) ?>"><?= $i ?></a></li>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <li><a href="?<?= http_build_query(array_merge($_GET, ['page'=>$page+1])) ?>">Next</a></li>
                        <li><a href="?<?= http_build_query(array_merge($_GET, ['page'=>$total_pages])) ?>">Last</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/admin_footer.php'; ?>