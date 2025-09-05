<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header('Location: ../public/login.php');
    exit();
}

$userId = $_SESSION['user_id'];

// Get user info
$stmt = $pdo->prepare("SELECT * FROM user WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) die("User not found.");

$message = "";
$step = 1;

function isStrongPassword($password) {
    return preg_match('/[A-Z]/', $password) &&     // at least 1 uppercase
           preg_match('/[a-z]/', $password) &&     // at least 1 lowercase
           preg_match('/[0-9]/', $password) &&     // at least 1 number
           preg_match('/[^A-Za-z0-9]/', $password) && // at least 1 special char
           strlen($password) >= 8;                 // at least 8 characters
}

// Step 1: Validate current password 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['check_current'])) {
    $currentPassword = $_POST['current_password'] ?? '';

    if (password_verify($currentPassword, $user['password'])) {
        $step = 2; // show new password form
    } else {
        $message = "<p class='error-msg'>❌ Current password is incorrect.</p>";
        $step = 1; // stay on step 1
    }
}

// Step 2: Set new password 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    $newPassword     = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($newPassword !== $confirmPassword) {
        $message = "<p class='error-msg'>❌ New passwords do not match.</p>";
        $step = 2;
    } elseif (!isStrongPassword($newPassword)) {
        $message = "<p class='error-msg'>❌ Password must be at least 8 characters, include uppercase, lowercase, number, and special character.</p>";
        $step = 2;
    } else {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $update = $pdo->prepare("UPDATE user SET password = ? WHERE user_id = ?");
    $update->execute([$hashedPassword, $userId]);

    // Destroy session for security
    session_unset();
    session_destroy();

    // Show popup and redirect to login
    echo "<script>
        alert('✅ Password updated successfully! You will now be logged out. Please log in again with your new password.');
        window.location.href = '../public/login.php';
    </script>";
    exit;
    }
}

include '../includes/header.php';
?>

<div class="profile-page">
<div class="profile-edit-container">
    <h2>Change Password</h2>
    <?= $message ?>

    <?php if ($step === 1): ?>
        <!-- Step 1: Verify current password -->
        <form method="post">
            <div class="form-group">
                <label><strong>Current Password:</strong></label>
                <input type="password" name="current_password" required>
            </div>
            <button type="submit" name="check_current" class="btn-save">Verify</button>
        </form>
    <?php elseif ($step === 2): ?>
        <!-- Step 2: Enter new password -->
        <form method="post">
            <div class="form-group">
                <label><strong>New Password:</strong></label>
                <input type="password" id="new_password" name="new_password" required>
                <div class="password-strength">
                    <div class="strength-bar">
                        <div class="strength-fill" id="strength-fill"></div>
                    </div>
                    <span class="strength-text" id="strength-text">Password strength</span>
                </div>
            </div>


        <div class="form-group">
                <label><strong>Confirm New Password:</strong></label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <div class="password-requirements">
                <h4>Password Requirements:</h4>
                <ul>
                    <li id="req-length"><i class="fas fa-circle"></i> At least 8 characters</li>
                    <li id="req-uppercase"><i class="fas fa-circle"></i> One uppercase letter</li>
                    <li id="req-lowercase"><i class="fas fa-circle"></i> One lowercase letter</li>
                    <li id="req-number"><i class="fas fa-circle"></i> One number</li>
                    <li id="req-special"><i class="fas fa-circle"></i> One special character</li>
                </ul>
            </div>

            <button type="submit" name="update_password" class="btn-save">Update Password</button>
        </form>
    <?php endif; ?>

    <div class="return-link">
        <a href="profile.php" style="text-decoration:none; color:#007bff;">← Back to Profile</a>
    </div>
</div>
</div>

<script>
const newPasswordInput = document.getElementById("new_password");
const strengthFill = document.getElementById("strength-fill");
const strengthText = document.getElementById("strength-text");

const requirements = {
    length: document.getElementById("req-length"),
    uppercase: document.getElementById("req-uppercase"),
    lowercase: document.getElementById("req-lowercase"),
    number: document.getElementById("req-number"),
    special: document.getElementById("req-special")
};

newPasswordInput.addEventListener("input", () => {
    const value = newPasswordInput.value;
    let strength = 0;

    // Check requirements
    if (value.length >= 8) { requirements.length.classList.add("valid"); strength++; } 
    else { requirements.length.classList.remove("valid"); }

    if (/[A-Z]/.test(value)) { requirements.uppercase.classList.add("valid"); strength++; }
    else { requirements.uppercase.classList.remove("valid"); }

    if (/[a-z]/.test(value)) { requirements.lowercase.classList.add("valid"); strength++; }
    else { requirements.lowercase.classList.remove("valid"); }

    if (/[0-9]/.test(value)) { requirements.number.classList.add("valid"); strength++; }
    else { requirements.number.classList.remove("valid"); }

    if (/[^A-Za-z0-9]/.test(value)) { requirements.special.classList.add("valid"); strength++; }
    else { requirements.special.classList.remove("valid"); }

    // Update strength bar
    let percentage = (strength / 5) * 100;
    strengthFill.style.width = percentage + "%";
    if (strength <= 2) {
        strengthFill.style.background = "red";
        strengthText.textContent = "Weak";
    } else if (strength === 3) {
        strengthFill.style.background = "orange";
        strengthText.textContent = "Medium";
    } else if (strength >= 4) {
        strengthFill.style.background = "green";
        strengthText.textContent = "Strong";
    }
});
</script>

<?php include '../includes/footer.php'; ?>