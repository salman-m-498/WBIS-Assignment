<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header('Location: admin_login.php');
    exit();
}

$userId = $_GET['id'];

// Handle POST update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // From form
    $username           = $_POST['username'];       
    $email              = $_POST['email'];          
    $role               = $_POST['role'];           
    $first_name         = $_POST['first_name'];     
    $last_name          = $_POST['last_name'];
    $address_line1      = $_POST['address_line1'];
    $address_line2      = $_POST['address_line2'];
    $city               = $_POST['city'];
    $state              = $_POST['state'];
    $postal_code        = $_POST['postal_code'];
    $phone              = $_POST['phone'];
    $newsLetterSub      = isset($_POST['newsletter_subscription']) ? 1 : 0;
    $marketingEmails    = isset($_POST['marketing_emails']) ? 1 : 0;
    $dateOfBirth        = $_POST['date_of_birth'];

    // Update user table
    $updateUsersSql = "UPDATE user SET username = ?, email = ?, role = ? WHERE user_id = ?";
    $stmt = $pdo->prepare($updateUsersSql);
    $stmt->execute([$username, $email, $role, $userId]);

    // Update user_profiles table
    $updateProfilesSql = "UPDATE user_profiles 
                          SET first_name = ?, last_name = ?, address_line1 = ?, address_line2 = ?, city = ?, state = ?, postal_code = ?, phone = ?, newsletter_subscription = ?, marketing_emails = ?, date_of_birth = ?
                          WHERE user_id = ?";
    $stmt = $pdo->prepare($updateProfilesSql);
    $stmt->execute([$first_name, $last_name, $address_line1, $address_line2, $city, $state, $postal_code, $phone, $newsLetterSub, $marketingEmails, $dateOfBirth, $userId]);

    $message = "User updated successfully!";
    header("Location: member_edit.php?id=" . urlencode($userId) . "&updated=1");
    exit();
}

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

if (!$user) die("User not found.");

include '../includes/header.php';
?>

<div class="container" style="max-width:600px; margin-top:30px;">
    <h2>Edit User Profile</h2>

    <?php if (isset($_GET['updated'])) : ?>
        <p style="color:green;">User updated successfully!</p>
    <?php endif; ?>

    <form method="post">

        <div style="margin-bottom:10px;">
            <label>
                <input type="hidden" name="role" value="member">
                <input type="checkbox" name="role" value="admin" <?= (!empty($user['role']) && ($user['role'] === 'admin')) ? 'checked' : '' ?>> Admin
            </label>
        </div>

        <div style="margin-bottom:10px;">
            <label><strong>Username:</strong></label>
            <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" style="width:100%; padding:5px;">
        </div>

        <div style="margin-bottom:10px;">
            <label><strong>First Name:</strong></label>
            <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" style="width:100%; padding:5px;">
        </div>

        <div style="margin-bottom:10px;">
            <label><strong>Last Name:</strong></label>
            <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" style="width:100%; padding:5px;">
        </div>

        <div style="margin-bottom:10px;">
            <label><strong>Email address:</strong></label>
            <input type="text" name="email" value="<?= htmlspecialchars($user['email']) ?>" style="width:100%; padding:5px;">
        </div>

        <h4>Address</h4>
        <div style="margin-bottom:10px;">
            <label>Address Line 1:</label>
            <input type="text" name="address_line1" value="<?= htmlspecialchars($user['address_line1'] ?? '') ?>" style="width:100%; padding:5px;">
        </div>
        <div style="margin-bottom:10px;">
            <label>Address Line 2:</label>
            <input type="text" name="address_line2" value="<?= htmlspecialchars($user['address_line2'] ?? '') ?>" style="width:100%; padding:5px;">
        </div>
        <div style="margin-bottom:10px;">
            <label>City:</label>
            <input type="text" name="city" value="<?= htmlspecialchars($user['city'] ?? '') ?>" style="width:100%; padding:5px;">
        </div>
        <div style="margin-bottom:10px;">
            <label>State:</label>
            <input type="text" name="state" value="<?= htmlspecialchars($user['state'] ?? '') ?>" style="width:100%; padding:5px;">
        </div>
        <div style="margin-bottom:10px;">
            <label>Postal Code:</label>
            <input type="text" name="postal_code" value="<?= htmlspecialchars($user['postal_code'] ?? '') ?>" style="width:100%; padding:5px;">
        </div>

        <div style="margin-bottom:10px;">
            <label><strong>Phone:</strong></label>
            <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" style="width:100%; padding:5px;">
        </div>

        <div style="margin-bottom:10px;">
            <label>
                <input type="checkbox" name="newsletter_subscription" value="1" <?= !empty($user['newsletter_subscription']) ? 'checked' : '' ?>> Newsletter Subscription
            </label>
        </div>

        <div style="margin-bottom:10px;">
            <label>
                <input type="checkbox" name="marketing_emails" value="1" <?= !empty($user['marketing_emails']) ? 'checked' : '' ?>> Receive marketing emails
            </label>
        </div>

        <div style="margin-bottom:10px;">
            <label><strong>Birth Date:</strong></label>
            <input type="date" name="date_of_birth" value="<?= htmlspecialchars($user['date_of_birth']) ?>" style="width:100%; padding:5px;">
        </div>

        <button type="submit" style="padding:8px 15px; margin-top:10px;">Save Changes</button>
    </form>

    <div style="margin-top:15px;">
        <a href="member_profile.php?id=<?= urlencode($user['user_id']) ?>" style="text-decoration:none; color:#007bff;">Return to member profile
    </div>
</div>

<div style="height:100px;"></div>

<?php include '../includes/admin_footer.php'; ?>