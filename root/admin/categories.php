<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: admin_login.php');
    exit();
}

require_once '../includes/db.php';

// Handle category actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $name = trim($_POST['category_name']);
                $description = trim($_POST['description']);
                try {
                    $stmt = $pdo->prepare("INSERT INTO category (name, description) VALUES (?, ?)");
                    $stmt->execute([$name, $description]);
                    $_SESSION['success'] = "Category added successfully!";
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Error adding category: " . $e->getMessage();
                }
                break;

            case 'edit':
                $id = $_POST['category_id'];
                $name = trim($_POST['category_name']);
                $description = trim($_POST['description']);
                try {
                    $stmt = $pdo->prepare("UPDATE category SET name = ?, description = ? WHERE category_id = ?");
                    $stmt->execute([$name, $description, $id]);
                    $_SESSION['success'] = "Category updated successfully!";
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Error updating category: " . $e->getMessage();
                }
                break;

            case 'delete':
                $id = $_POST['category_id'];
                try {
                    $stmt = $pdo->prepare("DELETE FROM category WHERE category_id = ?");
                    $stmt->execute([$id]);
                    $_SESSION['success'] = "Category deleted successfully!";
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Error deleting category: " . $e->getMessage();
                }
                break;
        }
        header('Location: categories.php');
        exit();
    }
}

// Get sorting parameters
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'name';
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

// Fetch categories with sorting
try {
    $stmt = $pdo->prepare("SELECT * FROM category ORDER BY $sort $order");
    $stmt->execute();
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    $_SESSION['error'] = "Error fetching categories: " . $e->getMessage();
    $categories = [];
}

// Page variables
$page_title = "Manage Categories";
$page_description = "Add, edit, and delete product categories";
include '../includes/header.php';
?>

<section class="admin-section categories-management">
    <div class="container">
        <div class="section-header">
            <h1>Manage Categories</h1>
            <button class="btn btn-primary" onclick="showAddCategoryModal()">
                <i class="fas fa-plus"></i> Add New Category
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
                        <th>
                            <a href="?sort=name&order=<?php echo $sort === 'name' && $order === 'ASC' ? 'DESC' : 'ASC'; ?>">
                                Name <?php echo $sort === 'name' ? ($order === 'ASC' ? '↑' : '↓') : ''; ?>
                            </a>
                        </th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($category['category_id']); ?></td>
                        <td><?php echo htmlspecialchars($category['name']); ?></td>
                        <td><?php echo htmlspecialchars($category['description']); ?></td>
                        <td class="actions">
                            <button class="btn btn-small btn-edit" onclick="showEditCategoryModal(<?php echo htmlspecialchars(json_encode($category)); ?>)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-small btn-delete" onclick="confirmDeleteCategory(<?php echo $category['category_id']; ?>)">
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

<!-- Add Category Modal -->
<div id="addCategoryModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Add New Category</h2>
        <form action="categories.php" method="POST">
            <input type="hidden" name="action" value="add">
            <div class="form-group">
                <label for="category_name">Category Name</label>
                <input type="text" id="category_name" name="category_name" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3"></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Add Category</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Category Modal -->
<div id="editCategoryModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Edit Category</h2>
        <form action="categories.php" method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="category_id" id="edit_category_id">
            <div class="form-group">
                <label for="edit_category_name">Category Name</label>
                <input type="text" id="edit_category_name" name="category_name" required>
            </div>
            <div class="form-group">
                <label for="edit_description">Description</label>
                <textarea id="edit_description" name="description" rows="3"></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update Category</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Category Form -->
<form id="deleteCategoryForm" action="categories.php" method="POST" style="display: none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="category_id" id="delete_category_id">
</form>

<script>
// Modal functionality
function showAddCategoryModal() {
    document.getElementById('addCategoryModal').style.display = 'block';
}

function showEditCategoryModal(category) {
    document.getElementById('edit_category_id').value = category.category_id;
    document.getElementById('edit_category_name').value = category.name;
    document.getElementById('edit_description').value = category.description;
    document.getElementById('editCategoryModal').style.display = 'block';
}

function confirmDeleteCategory(categoryId) {
    if (confirm('Are you sure you want to delete this category? This action cannot be undone.')) {
        document.getElementById('delete_category_id').value = categoryId;
        document.getElementById('deleteCategoryForm').submit();
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
