<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header('Location: ../public/login.php');
    exit();
}

$userId = $_SESSION['user_id'];

// Get all info from user and user_profiles
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
    $username           = $_POST['username'];       
    $email              = $_POST['email'];                    
    $first_name         = $_POST['first_name'];     
    $last_name          = $_POST['last_name'];
    $homeAddress        = $_POST['address'];
    $phone              = $_POST['phone'];
    $newsLetterSub      = isset($_POST['newsletter_subscription']) ? 1 : 0;
    $marketingEmails    = isset($_POST['marketing_emails']) ? 1 : 0;
    $dateOfBirth        = $_POST['date_of_birth'];
    $password           = $_POST['password'];

    $profilePicName = $user['profile_pic'];

    // Handle upload if new file is chosen
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

                $profilePicName = $fileName;
            } else {
                die("Error uploading file.");
            }
        } else {
            die("Invalid file type. Only JPG, JPEG, PNG & GIF allowed.");
        }
    }


    // Update user table
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $updateUsersSql = "UPDATE user SET username = ?, email = ?, password = ?, profile_pic = ? WHERE user_id = ?";
        $stmt = $pdo->prepare($updateUsersSql);
        $stmt->execute([$username, $email, $hashedPassword, $profilePicName, $userId]);
    } else {
        $updateUsersSql = "UPDATE user SET username = ?, email = ?, profile_pic = ? WHERE user_id = ?";
        $stmt = $pdo->prepare($updateUsersSql);
        $stmt->execute([$username, $email, $profilePicName, $userId]);
    }

    // Update user_profiles table
    $updateProfilesSql = "UPDATE user_profiles 
                          SET first_name = ?, last_name = ?, address = ?, phone = ?, newsletter_subscription = ?, marketing_emails = ?, date_of_birth = ?
                          WHERE user_id = ?";
    $stmt = $pdo->prepare($updateProfilesSql);
    $stmt->execute([$first_name, $last_name, $homeAddress, $phone, $newsLetterSub, $marketingEmails, $dateOfBirth, $userId]);

    $message = "User updated successfully!";
    header("Location: dashboard.php?id=" . urlencode($userId));
    exit();
}

include '../includes/header.php';
?>

<div class="container" style="max-width:600px; margin-top:30px;">
    <h2>Edit User Profile</h2>

    <?php if (!empty($message)) : ?>
        <p style="color:green;"><?= $message ?></p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">




        <?php
        $profilePic = 'default_profile_pic.jpg';
        if (!empty($user['profile_pic'])) {
            $filePath = "../assets/images/profile_pictures/" . $user['profile_pic'];
            if (file_exists($filePath)) {
                $profilePic = $user['profile_pic'];
            }
        }
        ?>
        <div style="margin-bottom:10px;">
            <label><strong>Profile Picture:</strong></label><br>
            <img src="../assets/images/profile_pictures/<?= htmlspecialchars($profilePic) ?>" 
                alt="Profile Picture" 
                style="width:120px; height:120px; object-fit:cover; border-radius:50%; margin-bottom:10px;">
            <br>
            <input type="file" name="profile_pic" accept="image/*">
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

        <div style="margin-bottom:10px;">
            <label><strong>New Password:</strong></label>
            <input type="password" name="password" placeholder="Leave blank to keep current password" style="width:100%; padding:5px;">
        </div>

        <div style="margin-bottom:10px;">
            <label><strong>Home address:</strong></label>
            <input type="text" name="address" value="<?= htmlspecialchars($user['address']) ?>" style="width:100%; padding:5px;">
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
        <a href="dashboard.php?id=<?= urlencode($user['user_id']) ?>" style="text-decoration:none; color:#007bff;">Return to dashboard
    </div>
</div>

<div style="height:100px;"></div>