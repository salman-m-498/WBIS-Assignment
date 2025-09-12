<?php
session_start();
require_once '../includes/db.php'; // adjust path to your db connection

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 15; // number of products per page
$offset = ($page - 1) * $limit;

$total_products = 0;
$total_pages = 1;
$products = [];

if (!empty($q)) {
    // Count total results
    $count_stmt = $pdo->prepare("
        SELECT COUNT(DISTINCT p.product_id)
        FROM products p
        JOIN categories c ON p.category_id = c.category_id
        LEFT JOIN categories pc ON c.parent_id = pc.category_id
        WHERE p.status = 'active'
          AND (
              p.name LIKE :q
              OR c.name LIKE :q
              OR pc.name LIKE :q
          )
    ");
    $count_stmt->execute(['q' => "%$q%"]);
    $total_products = $count_stmt->fetchColumn();
    $total_pages = ceil($total_products / $limit);

    // Fetch results with LIMIT and OFFSET
    $stmt = $pdo->prepare("
          SELECT p.*, c.name AS subcategory, pc.name AS main_category
        FROM products p
        JOIN categories c ON p.category_id = c.category_id
        LEFT JOIN categories pc ON c.parent_id = pc.category_id
        WHERE p.status = 'active'
          AND (
              p.name LIKE :q
              OR c.name LIKE :q
              OR pc.name LIKE :q
          )
        GROUP BY p.product_id
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue(':q', "%$q%", PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

include '../includes/header.php';
?>

<div class="container search-results">
    <h2>Search Results for "<?php echo htmlspecialchars($q); ?>"</h2>

    <?php if (empty($products)): ?>
        <p>No products found.</p>
    <?php else: ?>
        <div class="products-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <a href="product.php?id=<?php echo urlencode($product['product_id']); ?>">
                        <img src="../<?php echo htmlspecialchars($product['image']); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>" 
                             onerror="this.src='https://via.placeholder.com/200x150/f93c64/ffffff?text=<?= urlencode($product['name']) ?>'">
                    </a>
                    <h3>
                        <a href="product.php?id=<?php echo urlencode($product['product_id']); ?>">
                            <?php echo htmlspecialchars($product['name']); ?>
                        </a>
                    </h3>
                    <p><?php echo htmlspecialchars($product['description']); ?></p>
                    <p>
                        <?php if (!empty($product['sale_price'])): ?>
                            <span class="old-price">RM<?php echo $product['price']; ?></span>
                            <span class="sale-price">RM<?php echo $product['sale_price']; ?></span>
                        <?php else: ?>
                            <span class="price">RM<?php echo $product['price']; ?></span>
                        <?php endif; ?>
                    </p>
                    <a class="btn" href="product.php?id=<?php echo urlencode($product['product_id']); ?>">
                        View Product
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
         <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="search-pagination">
                <?php if ($page > 1): ?>
                    <a href="?q=<?= urlencode($q) ?>&page=<?= $page - 1 ?>">Prev</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?q=<?= urlencode($q) ?>&page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?q=<?= urlencode($q) ?>&page=<?= $page + 1 ?>">Next</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

