<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header('Location: ../public/login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$errorMessage = "";

// Fetch user + profile info
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

// Handle POST update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username        = $_POST['username'];
    $email           = $_POST['email'];
    $first_name      = $_POST['first_name'];
    $last_name       = $_POST['last_name'];
    $address_line1   = $_POST['address_line1'];
    $address_line2   = $_POST['address_line2'];
    $city            = $_POST['city'];
    $state           = $_POST['state'];
    $postal_code     = $_POST['postal_code'];
    $phone           = $_POST['phone'];
    $newsLetterSub   = isset($_POST['newsletter_subscription']) ? 1 : 0;
    $marketingEmails = isset($_POST['marketing_emails']) ? 1 : 0;
    $dateOfBirth     = $_POST['date_of_birth'];

    // Keep current picture by default
    $profilePicName = $user['profile_pic'];

    // Handle new upload
    if (!empty($_FILES['profile_pic']['name'])) {
        $targetDir = "../assets/images/profile_pictures/";
        $fileName  = uniqid() . "_" . basename($_FILES["profile_pic"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType  = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        $allowedTypes = ['jpg','jpeg','png','gif'];
        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $targetFilePath)) {
                // Delete old file if not default
                if (!empty($user['profile_pic']) 
                    && $user['profile_pic'] !== 'default_profile_pic.jpg' 
                    && file_exists($targetDir . $user['profile_pic'])) {
                    unlink($targetDir . $user['profile_pic']);
                }
                // Save only filename
                $profilePicName = '/assets/images/profile_pictures/' .  $fileName;
            } else {
                 $errorMessage = "Error uploading file.";
            }
        } else {
             $errorMessage = "Invalid file type. Only JPG, JPEG, PNG & GIF allowed.";
        }
    }


    // Update profile table
     $updateProfilesSql = "UPDATE user_profiles 
                           SET first_name = ?, last_name = ?, phone = ?, 
                              address_line1 = ?, address_line2 = ?, city = ?, state = ?, postal_code = ?, 
                              newsletter_subscription = ?, marketing_emails = ?, date_of_birth = ? 
                          WHERE user_id = ?";
    $stmt = $pdo->prepare($updateProfilesSql);
    $stmt->execute([
        $first_name, $last_name,$phone,
        $address_line1, $address_line2, $city, $state, $postal_code,
        $newsLetterSub, $marketingEmails, $dateOfBirth, $userId
    ]);

    // Update user table
    $updateUserSql = "UPDATE user 
                  SET username = ?, email = ?, profile_pic = ? 
                  WHERE user_id = ?";
    $stmt = $pdo->prepare($updateUserSql);
    $stmt->execute([$username, $email, $profilePicName, $userId]);

    if (empty($errorMessage)) {
    $_SESSION['success_message'] = "Profile updated successfully!";
    header("Location: profile.php?id=" . urlencode($userId));
    exit();
}
}

include '../includes/header.php';
?>

<?php if (!empty($errorMessage)): ?>
<script>
    alert("<?= htmlspecialchars($errorMessage) ?>");
</script>
<?php endif; ?>

<div class="profile-page">
    <div class="profile-edit-container">
        <h2>Edit User Profile</h2>

        <form method="post" enctype="multipart/form-data">
            <?php
            $defaultPic = '/assets/images/profile_pictures/default_profile_pic.jpg';
            $profilePicPath = !empty($user['profile_pic']) 
                ?  $user['profile_pic'] 
                : $defaultPic;
            ?>
            
            <div class="form-group profile-pic-group">
                <label><strong>Profile Picture:</strong></label><br>
                <img src="<?= htmlspecialchars($profilePicPath) ?>" alt="Profile Picture" class="profile-pic-preview">
                <br>
                <input type="file" name="profile_pic" accept="image/*">
            </div>

             <div class="form-group">
                <label><strong>Username:</strong></label>
                <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>">
            </div>

            <div class="form-group">
                <label><strong>First Name:</strong></label>
                <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>">
            </div>

            <div class="form-group">
                <label><strong>Last Name:</strong></label>
                <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>">
            </div>

            <div class="form-group">
                <label><strong>Email address:</strong></label>
                <input type="text" name="email" value="<?= htmlspecialchars($user['email']) ?>">
            </div>

            <div class="form-group">
                <label><strong>Address Line 1:</strong></label>
                <input type="text" name="address_line1" value="<?= htmlspecialchars($user['address_line1']) ?>">
            </div>

            <div class="form-group">
                <label><strong>Address Line 2:</strong></label>
                <input type="text" name="address_line2" value="<?= htmlspecialchars($user['address_line2']) ?>">
            </div>

            <div class="form-group">
                <label><strong>City:</strong></label>
                <input type="text" name="city" value="<?= htmlspecialchars($user['city']) ?>">
            </div>

            <div class="form-group">
                <label><strong>State:</strong></label>
                <input type="text" name="state" value="<?= htmlspecialchars($user['state']) ?>">
            </div>

            <div class="form-group">
                <label><strong>Postal Code:</strong></label>
                <input type="text" name="postal_code" value="<?= htmlspecialchars($user['postal_code']) ?>">
            </div>

            <div class="form-group">
                <label><strong>Phone:</strong></label>
                <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>">
            </div>

            <div class="form-group checkbox-group">
                <label class="checkbox-label">
                    Newsletter Subscription
                    <input type="checkbox" name="newsletter_subscription" value="1" <?= !empty($user['newsletter_subscription']) ? 'checked' : '' ?>> 
                    <span class="checkmark"></span>
                </label>
            </div>

            <div class="form-group checkbox-group">
                <label class="checkbox-label">
                    Receive marketing emails
                    <input type="checkbox" name="marketing_emails" value="1" <?= !empty($user['marketing_emails']) ? 'checked' : '' ?>> 
                    <span class="checkmark"></span>
                </label>
            </div>

            <div class="form-group">
                <label><strong>Birth Date:</strong></label>
                <input type="date" name="date_of_birth" value="<?= htmlspecialchars($user['date_of_birth']) ?>">
            </div>

            <button type="submit" class="btn-save">Save Changes</button>
        </form>

        <div class="return-link">
            <a href="dashboard.php?id=<?= urlencode($user['user_id']) ?>" style="text-decoration:none; color:#007bff;">
                Return to dashboard
            </a>
        </div>
    </div>
</div>
