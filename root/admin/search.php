<?php
// admin/search.php
session_start();
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header('Location: admin_login.php');
    exit();
}

$q = trim($_GET['q'] ?? '');
$scope = $_GET['scope'] ?? 'all'; // allowed: all, products, orders, members
$page_title = "Search Results";
include '../includes/admin_header.php';
?>

<section class="admin-search-results">
  <div class="container">
    <h1>Search Results<?php if ($q) echo ' for "' . htmlspecialchars($q) . '"'; ?></h1>

    <?php if (!$q): ?>
      <p>Please enter a search term in the admin search box.</p>
    <?php else: ?>

      <?php
      // Prepare results container
      $results = [
        'products' => [],
        'orders' => [],
        'members' => [],
      ];

      // --- Products ---
      if ($scope === 'all' || $scope === 'products') {
          $sql = "SELECT product_id, name, sku, status FROM products
                  WHERE name LIKE ? OR sku LIKE ? LIMIT 20";
          $stmt = $pdo->prepare($sql);
          $like = "%$q%";
          $stmt->execute([$like, $like]);
          $results['products'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
      }

      // --- Orders ---
      if ($scope === 'all' || $scope === 'orders') {
          $sql = "SELECT order_id, order_number, contact_email, shipping_first_name, shipping_last_name, order_status, created_at
                  FROM orders
                  WHERE order_number LIKE ? OR contact_email LIKE ? OR shipping_first_name LIKE ? OR shipping_last_name LIKE ?
                  ORDER BY created_at DESC
                  LIMIT 20";
          $stmt = $pdo->prepare($sql);
          $stmt->execute([$like, $like, $like, $like]);
          $results['orders'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
      }

      // --- Members (user table) ---
      if ($scope === 'all' || $scope === 'members') {
          $sql = "SELECT user_id, username, email, role, status, created_at
                  FROM user
                  WHERE username LIKE ? OR email LIKE ?
                  LIMIT 20";
          $stmt = $pdo->prepare($sql);
          $stmt->execute([$like, $like]);
          $results['members'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
      }
      ?>

      <!-- Products section -->
      <div class="search-section">
        <h2><i class="fas fa-box"></i> Products (<?= count($results['products']) ?>)</h2>
        <ul>
          <?php if ($results['products']): ?>
            <?php foreach ($results['products'] as $p): ?>
              <li>
                <a href="product_edit.php?id=<?= urlencode($p['product_id']) ?>">
                  <?= htmlspecialchars($p['name']) ?>
                </a>
                <small> (SKU: <?= htmlspecialchars($p['sku']) ?>) <?= htmlspecialchars($p['status']) ?></small>
              </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li>No products found.</li>
          <?php endif; ?>
        </ul>
      </div>

      <!-- Orders section -->
      <div class="search-section">
        <h2><i class="fas fa-shopping-cart"></i> Orders (<?= count($results['orders']) ?>)</h2>
        <ul>
          <?php if ($results['orders']): ?>
            <?php foreach ($results['orders'] as $o): ?>
              <li>
                <a href="order_details.php?id=<?= urlencode($o['order_id']) ?>">
                  <?= htmlspecialchars($o['order_number']) ?>
                </a>
                <small>
                  — <?= htmlspecialchars($o['shipping_first_name'] . ' ' . $o['shipping_last_name']) ?>
                  (<?= htmlspecialchars($o['contact_email']) ?>)
                  — <?= htmlspecialchars($o['order_status']) ?>
                </small>
              </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li>No orders found.</li>
          <?php endif; ?>
        </ul>
      </div>

      <!-- Members section -->
      <div class="search-section">
        <h2><i class="fas fa-users"></i> Members (<?= count($results['members']) ?>)</h2>
        <ul>
          <?php if ($results['members']): ?>
            <?php foreach ($results['members'] as $m): ?>
              <li>
                <a href="member_profile.php?id=<?= urlencode($m['user_id']) ?>">
                  <?= htmlspecialchars($m['username']) ?>
                </a>
                <small>(<?= htmlspecialchars($m['email']) ?>) — <?= htmlspecialchars($m['role']) ?></small>
              </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li>No members found.</li>
          <?php endif; ?>
        </ul>
      </div>

    <?php endif; ?>
  </div>
</section>

<?php include '../includes/admin_footer.php'; ?>
