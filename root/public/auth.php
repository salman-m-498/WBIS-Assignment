<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit();
}

$action = $_POST['action'] ?? '';

if ($action === 'login') {
    handleLogin();
} elseif ($action === 'register') {
    handleRegister();
} else {
    header('Location: login.php');
    exit();
}


function handleLogin() {
    global $pdo;
    
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    // Validate input
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = 'Please fill in all fields.';
        header('Location: login.php');
        exit();
    }
    
    try {
        // Check if user exists and get user data
        $stmt = $pdo->prepare("SELECT user_id, username, email, password, role, profile_pic, status 
                               FROM user 
                               WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

         if ($user) {
            // Check if blocked
            if ($user['status'] === 'blocked') {
                $_SESSION['error'] = 'Your account has been blocked. Please contact support.';
                header('Location: login.php');
                exit();
            }
        
        if (password_verify($password, $user['password'])) {
            // Login successful
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['profile_pic'] = $user['profile_pic'];
            
            // Set remember me cookie if requested
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/'); // 30 days
                
                // Store token in database (you might want to create a separate table for this)
                $stmt = $pdo->prepare("UPDATE user SET remember_token = ? WHERE user_id = ?");
                $stmt->execute([$token, $user['user_id']]);
            }
            
            $_SESSION['success'] = 'Login successful! Welcome back, ' . $user['username'] . '!';
            
            // Redirect based on role
            if ($user['role'] === 'admin') {
                header('Location: ../admin/dashboard.php');
            } else {
                header('Location: ../member/dashboard.php');
            }
            exit();
        }
        }
         $_SESSION['error'] = 'Invalid email or password.';
        header('Location: login.php');
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = 'An error occurred. Please try again.';
        header('Location: login.php');
        exit();
    }
}

function assignWelcomeVoucher($pdo, $user_id) {
    try {
        // Get the welcome voucher (NEWUSER voucher)
        $stmt = $pdo->prepare("
            SELECT voucher_id 
            FROM vouchers 
            WHERE code = 'NEWUSER' 
            AND status = 'active' 
            AND start_date <= NOW() 
            AND end_date >= NOW()
            LIMIT 1
        ");
        $stmt->execute();
        $voucher = $stmt->fetch();
        
        if ($voucher) {
            // Check if user doesn't already have this voucher
            $checkStmt = $pdo->prepare("
                SELECT COUNT(*) 
                FROM user_vouchers 
                WHERE user_id = ? AND voucher_id = ?
            ");
            $checkStmt->execute([$user_id, $voucher['voucher_id']]);
            $hasVoucher = $checkStmt->fetchColumn() > 0;
            
            if (!$hasVoucher) {
                // Generate user_voucher_id and collect voucher
                $user_voucher_id = generateNextId($pdo, "user_vouchers", "user_voucher_id", "UV", 11);
                
                // Assign voucher to user
                $insertStmt = $pdo->prepare("
                    INSERT INTO user_vouchers (user_voucher_id, user_id, voucher_id) 
                    VALUES (?, ?, ?)
                ");
                $insertStmt->execute([$user_voucher_id, $user_id, $voucher['voucher_id']]);
                
                return true;
            }
        }
        
        return false;
    } catch (Exception $e) {
        error_log("Error assigning welcome voucher: " . $e->getMessage());
        return false;
    }
}

function handleRegister() {
    global $pdo;
    
    // Get form data
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $date_of_birth = $_POST['date_of_birth'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $address_line1 = trim($_POST['address_line1'] ?? '');
    $address_line2 = trim($_POST['address_line2'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $state = trim($_POST['state'] ?? '');
    $postal_code = trim($_POST['postal_code'] ?? '');
    $newsletter = isset($_POST['newsletter']);
    $marketing = isset($_POST['marketing']);
    $terms = isset($_POST['terms']);
    $age_verification = isset($_POST['age_verification']);

     function handleProfilePicUpload($file) {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) return null;

        $allowed = ['jpg','jpeg','png','gif','webp'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) return null;

        $uploadDirAbs = rtrim(__DIR__ . '/../assets/images/profile_pictures/', '/\\') . DIRECTORY_SEPARATOR;
        $uploadDirWeb = '/assets/images/profile_pictures/';

        if (!is_dir($uploadDirAbs)) @mkdir($uploadDirAbs, 0775, true);

        $filename = uniqid('user_', true) . '.' . $ext;
        $targetAbs = $uploadDirAbs . $filename;

        if (move_uploaded_file($file['tmp_name'], $targetAbs)) {
             return $uploadDirWeb . $filename;
        }

        return null;
    }

    $profile_pic = null;
    if (isset($_FILES['profile_pic'])) {
        $profile_pic = handleProfilePicUpload($_FILES['profile_pic']);
    }

    
    // Validate required fields
    $required_fields = [
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $email,
        'phone' => $phone,
        'date_of_birth' => $date_of_birth,
        'password' => $password,
        'confirm_password' => $confirm_password,
        'address_line1' => $address_line1,
        'city' => $city,
        'state' => $state,
        'postal_code' => $postal_code
    ];
    
    foreach ($required_fields as $field => $value) {
        if (empty($value)) {
            $_SESSION['error'] = 'Please fill in all required fields.';
            $_SESSION['form_data'] = $_POST; // Preserve form data
            header('Location: register.php');
            exit();
        }
    }
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Please enter a valid email address.';
        $_SESSION['form_data'] = $_POST;
        header('Location: register.php');
        exit();
    }
    
    // Validate password strength
    if (strlen($password) < 8) {
        $_SESSION['error'] = 'Password must be at least 8 characters long.';
        $_SESSION['form_data'] = $_POST;
        header('Location: register.php');
        exit();
    }
    
    // Check password match
    if ($password !== $confirm_password) {
        $_SESSION['error'] = 'Passwords do not match.';
        $_SESSION['form_data'] = $_POST;
        header('Location: register.php');
        exit();
    }
    
    // Check terms acceptance
    if (!$terms) {
    $_SESSION['error'] = 'You must agree to the Terms and Conditions and Privacy Policy.';
    $_SESSION['form_data'] = $_POST;
    header('Location: register.php');
    exit();
    }

    // Check age verification
    if (!$age_verification) {
    $_SESSION['error'] = 'You must confirm that you are at least 13 years old.';
    $_SESSION['form_data'] = $_POST;
    header('Location: register.php');
    exit();
    }
    
    try {
        // Check if email already exists
        if (emailExists($pdo, $email)) {
            $_SESSION['error'] = 'An account with this email already exists.';
            $_SESSION['form_data'] = $_POST;
            header('Location: register.php');
            exit();
        }

        $pdo->beginTransaction();
        
        // Generate username from first and last name
        $username = strtolower($first_name . '_' . $last_name);
        $original_username = $username;
        $counter = 1;
        
        // Make sure username is unique
        while (usernameExists($pdo, $username)) {
            $username = $original_username . '_' . $counter;
            $counter++;
        }
        
        // Generate unique user ID
        $user_id = getUniqueUserId($pdo);
        
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert user into database
        $stmt = $pdo->prepare("
            INSERT INTO user (user_id, username, email, password, role, profile_pic) 
            VALUES (?, ?, ?, ?, 'member', ?)
        ");
        
        $stmt->execute([$user_id, $username, $email, $hashed_password,$profile_pic]);
        
        // Insert user profile
        $stmt = $pdo->prepare("
    INSERT INTO user_profiles 
        (user_id, first_name, last_name, phone, date_of_birth, 
         address_line1, address_line2, city, state, postal_code, 
         newsletter_subscription, marketing_emails) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

        
    $stmt->execute([
        $user_id, 
        $first_name, 
        $last_name, 
        $phone, 
        $date_of_birth, 
        $address_line1, 
        $address_line2, 
        $city, 
        $state, 
        $postal_code, 
        $newsletter ? 1 : 0, 
        $marketing ? 1 : 0
    ]);
         
        $pdo->commit();

        assignWelcomeVoucher($pdo, $user_id);
        
        // Clear form data
        unset($_SESSION['form_data']);
        
        $_SESSION['success'] = 'Registration successful! Welcome to ToyLand Store, ' . $first_name . '!';
        header('Location: login.php');
        exit();
        
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        $_SESSION['error'] = 'An error occurred during registration. Please try again.';
        $_SESSION['form_data'] = $_POST;
        header('Location: register.php');
        exit();
    }
}
?>
