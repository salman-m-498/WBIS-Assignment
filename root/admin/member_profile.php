<?php
session_start();

require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header('Location: admin_login.php');
    exit();
}

if (!isset($_GET['id'])) {
    die("No user ID provided.");
}

$userId = $_GET['id'];

// Get all info from user and user_profiles
$sql = "
    SELECT u.*, up.* 
    FROM user u
    LEFT JOIN user_profiles up ON u.user_id = up.user_id
    WHERE u.user_id = ?
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

include '../includes/header.php';

//action bar
// delete, disable(block login)


?>
<!-- action -->
 <div>

 </div>


<div class="container" style="max-width:3000px; margin-top:30px;">
    <h2>User Profile</h2>
    <div style="border:1px solid #ccc; padding:20px; border-radius:8px;">
        <p><strong>Profile ID:</strong> <?= htmlspecialchars($user['profile_id']) ?></p>
        <p><strong>User ID:</strong> <?= htmlspecialchars($user['user_id']) ?></p>
        <p><strong>Role:</strong> <?= htmlspecialchars($user['role']) ?></p>
        <p><strong>Userame:</strong> <?= htmlspecialchars($user['username']) ?></p>
        <p><strong>First Name:</strong> <?= htmlspecialchars($user['first_name']?? '') ?></p>
        <p><strong>Last Name:</strong> <?= htmlspecialchars($user['last_name']?? '') ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>Phone Number:</strong> <?= htmlspecialchars($user['phone']?? '') ?></p>
        <p><strong>Birth Date:</strong> <?= htmlspecialchars($user['date_of_birth']?? '') ?></p>
        <p><strong>home Address:</strong> <?= htmlspecialchars($user['address']?? '') ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($user['status']) ?></p>
        <p>
            <strong>Newsletter Sub:</strong> 
            <?= $user['newsletter_subscription'] == 1 ? 'Yes' : 'No' ?>
        </p>
        <p>
            <strong>Marketing Emails:</strong> 
            <?= $user['marketing_emails'] == 1 ? 'Yes' : 'No' ?>
        </p>
        <p><strong>Created:</strong> <?= htmlspecialchars($user['created_at']) ?></p>
        <p><strong>Last update:</strong> <?= htmlspecialchars($user['updated_at']) ?></p>
    </div>
    <div style="display:flex; justify-content:space-between; margin-top:15px;">
        <a href="members.php" style="text-decoration:none; color:#007bff;">← Back to Members</a>
        <a href="member_edit.php?id=<?= urlencode($user['user_id']) ?>" style="text-decoration:none; color:#007bff;">Edit this member
    </div>
</div>

<?php include '../includes/footer.php'; ?>



