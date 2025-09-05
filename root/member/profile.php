<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header('Location: ../public/login.php');
    exit();
}

$userId = $_SESSION['user_id'];

// Get user and profile info
$sql = "
    SELECT u.*, up.* 
    FROM user u
    INNER JOIN user_profiles up ON u.user_id = up.user_id
    WHERE u.user_id = ?
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) die("User not found.");

// Always build full path for display
$profilePicPath = !empty($user['profile_pic'])
    ? '/assets/images/profile_pictures/' . $user['profile_pic']
    : '/assets/images/profile_pictures/default_profile_pic.jpg';

include '../includes/header.php';
?>

<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars($_SESSION['success_message']); ?>
    </div>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>


<div class="profile-page">
    <div class="profile-container">
        <h2>My Profile</h2>

        <div class="profile-info">
            <div class="profile-picture">
                <img src="<?= htmlspecialchars($profilePicPath) ?>" alt="Profile Picture">
            </div>
            <div class="profile-details">
                <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                <p><strong>Account Status:</strong> <?= htmlspecialchars($user['status']) ?></p>
                <p><strong>First Name:</strong> <?= htmlspecialchars($user['first_name']) ?></p>
                <p><strong>Last Name:</strong> <?= htmlspecialchars($user['last_name']) ?></p>
                <p><strong>Phone:</strong> <?= htmlspecialchars($user['phone']) ?></p>
                <p><strong>Home Address:</strong> <?= htmlspecialchars($user['address']) ?></p>
                <p><strong>Birth Date:</strong> <?= htmlspecialchars($user['date_of_birth']) ?></p>
            </div>
        </div>

        <div class="profile-actions">
            <a href="profile_edit.php" class="btn btn-edit">Edit Profile</a>
            <a href="change_password.php" class="btn btn-password">Change Password</a>
            <a href="../public/logout.php" class="btn btn-logout" onclick="return confirm('Are you sure you want to log out?');">Log Out</a>
            <a href="delete_account.php" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete your account? This cannot be undone.')">Delete Account</a>
        </div>
    </div>
</div>
