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
    $delete_id = trim($_POST['delete_id']);

    try {
        // Block delete if it has subcategories
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE parent_id = ?");
        $stmt->execute([$delete_id]);
        if ((int)$stmt->fetchColumn() > 0) {
            throw new Exception("Cannot delete: category has subcategories.");
        }

        // Block delete if products exist
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
        $stmt->execute([$delete_id]);
        if ((int)$stmt->fetchColumn() > 0) {
            throw new Exception("Cannot delete: category has products assigned.");
        }

        // Delete image file
        $stmt = $pdo->prepare("SELECT image FROM categories WHERE category_id = ?");
        $stmt->execute([$delete_id]);
        $imgPath = $stmt->fetchColumn();
        if ($imgPath && file_exists(__DIR__ . '/../' . $imgPath)) {
            unlink(__DIR__ . '/../' . $imgPath);
        }

        $stmt = $pdo->prepare("DELETE FROM categories WHERE category_id = ?");
        $stmt->execute([$delete_id]);

        $_SESSION['success'] = "Category deleted successfully!";
    } catch (Throwable $e) {
        $_SESSION['error'] = "Error deleting category: " . $e->getMessage();
    }

    header("Location: categories.php");
    exit;
}

// --- Handle Search & Sort ---
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'parent_first';
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 4;
$offset = ($page - 1) * $per_page;

$where = 'WHERE 1';
$params = [];
if ($search) {
    $where = "WHERE name LIKE ? OR description LIKE ?";
    $params = ["%$search%", "%$search%"];
}

// Sorting options
switch ($sort) {
    case 'name_desc':
        $orderBy = "name DESC";
        break;
    case 'status_asc':
        $orderBy = "status ASC";
        break;
    case 'status_desc':
        $orderBy = "status DESC";
        break;
    case 'parent_first':
        $orderBy = "name ASC"; 
        break;
    default: // name_asc
        $orderBy = "name ASC";
        break;
}

if ($sort === 'parent_first') {
    // Count only parents
    $count_sql = "SELECT COUNT(*) FROM categories WHERE parent_id IS NULL";
    $total_categories = $pdo->query($count_sql)->fetchColumn();
    $total_pages = ceil($total_categories / $per_page);

    // Fetch parent categories for this page
    $sql = "SELECT * FROM categories WHERE parent_id IS NULL ORDER BY $orderBy LIMIT $per_page OFFSET $offset";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $parentCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all categories for hierarchy
    $allCategories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

    // Group into hierarchy
    $categoriesByParent = [];
    foreach ($allCategories as $cat) {
        $categoriesByParent[$cat['parent_id']][] = $cat;
    }
} else {
    // Normal mode
    $count_sql = "SELECT COUNT(*) FROM categories $where";
    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->execute($params);
    $total_categories = $count_stmt->fetchColumn();
    $total_pages = ceil($total_categories / $per_page);

    $sql = "SELECT * FROM categories $where ORDER BY $orderBy LIMIT $per_page OFFSET $offset";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Recursive display
function displayCategories($parentId, $categoriesByParent, $level = 0) {
    if (!isset($categoriesByParent[$parentId])) return;

    foreach ($categoriesByParent[$parentId] as $cat) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($cat['category_id']) . "</td>";
        echo "<td>" . str_repeat("— ", $level) . htmlspecialchars($cat['name']) . "</td>";
        echo "<td>" . htmlspecialchars($cat['description']) . "</td>";
        echo "<td>";
        if (!empty($cat['image'])) {
            echo "<img src='../" . htmlspecialchars($cat['image']) . "' style='max-width:80px'>";
        } else {
            echo "No image";
        }
        echo "</td>";
        echo "<td>" . htmlspecialchars($cat['status']) . "</td>";
        echo "<td>
                <a href='categories_edit.php?id={$cat['category_id']}' class='btn btn-small btn-edit'>Edit</a>
                <form method='post' action='categories.php' style='display:inline;' 
                      onsubmit=\"return confirm('Delete this category?');\">
                    <input type='hidden' name='delete_id' value='{$cat['category_id']}'>
                    <button type='submit' class='btn btn-small btn-delete'>Delete</button>
                </form>
              </td>";
        echo "</tr>";

        // Show children
        displayCategories($cat['category_id'], $categoriesByParent, $level + 1);
    }
}

// Page variables
$page_title = "Manage Categories";
$page_description = "Add, edit, and delete product categories";


include '../includes/admin_header.php';
?>

<section class="admin-section categories-management">
    <div class="container">
            <h1>Manage Categories</h1>

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

      <div class="categories-filters-bar" style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:20px; align-items:center;">
    <form method="get" action="categories.php" class="categories-filters-form" style="display:flex; gap:10px; flex:1;">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search categories..." style="flex:1; padding:5px; border-radius:6px; border:1px solid #ccc;">
        <select name="sort" onchange="this.form.submit()" style="padding:5px; border-radius:6px; border:1px solid #ccc;">
             <option value="parent_first" <?= $sort=='parent_first'?'selected':'' ?>>Parent → Subcategories</option>
            <option value="name_asc" <?= $sort=='name_asc'?'selected':'' ?>>Name A–Z</option>
            <option value="name_desc" <?= $sort=='name_desc'?'selected':'' ?>>Name Z–A</option>
            <option value="status_asc" <?= $sort=='status_asc'?'selected':'' ?>>Status Active → Blocked</option>
            <option value="status_desc" <?= $sort=='status_desc'?'selected':'' ?>>Status Blocked → Active</option>
        </select>
        <button type="submit" class="btn btn-primary">Search</button>
    </form>
    <a href="categories_add.php" class="btn btn-success">+ Add New</a>
    </div>

       <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Image</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                    <?php if ($sort === 'parent_first'): ?>
                       <?php if ($parentCategories): ?>
        <?php foreach ($parentCategories as $parent): ?>
            <?php
                // show parent row
                echo "<tr>";
                echo "<td>" . htmlspecialchars($parent['category_id']) . "</td>";
                echo "<td>" . htmlspecialchars($parent['name']) . "</td>";
                echo "<td>" . htmlspecialchars($parent['description']) . "</td>";
                echo "<td>";
                if (!empty($parent['image'])) {
                    echo "<img src='../" . htmlspecialchars($parent['image']) . "' style='max-width:80px'>";
                } else {
                    echo "No image";
                }
                echo "</td>";
                echo "<td>" . htmlspecialchars($parent['status']) . "</td>";
                echo "<td>
                        <a href='categories_edit.php?id={$parent['category_id']}' class='btn btn-small btn-edit'>Edit</a>
                        <form method='post' action='categories.php' style='display:inline;' onsubmit=\"return confirm('Delete this category?');\">
                            <input type='hidden' name='delete_id' value='{$parent['category_id']}'>
                            <button type='submit' class='btn btn-small btn-delete'>Delete</button>
                        </form>
                      </td>";
                echo "</tr>";

                // show children
                displayCategories($parent['category_id'], $categoriesByParent, 1);
            ?>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="6">No categories found.</td></tr>
    <?php endif; ?>
<?php else: ?>
    <?php if ($categories): ?>
        <?php foreach ($categories as $cat): ?>
            <tr>
                <td><?= htmlspecialchars($cat['category_id']) ?></td>
                <td><?= htmlspecialchars($cat['name']) ?></td>
                <td><?= htmlspecialchars($cat['description']) ?></td>
                <td>
                    <?php if (!empty($cat['image'])): ?>
                        <img src="../<?= htmlspecialchars($cat['image']) ?>" style="max-width:80px">
                    <?php else: ?>
                        No image
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($cat['status']) ?></td>
                <td> ... actions ... </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="6">No categories found.</td></tr>
    <?php endif; ?>
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
</section>

<?php include '../includes/admin_footer.php'; ?>

