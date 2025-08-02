<?php
session_start();
require_once '../includes/db.php';

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
        $stmt = $pdo->prepare("SELECT user_id, username, email, password, role, profile_pic FROM user WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
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
        } else {
            $_SESSION['error'] = 'Invalid email or password.';
            header('Location: login.php');
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'An error occurred. Please try again.';
        header('Location: login.php');
        exit();
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
    $country = $_POST['country'] ?? '';
    $newsletter = isset($_POST['newsletter']);
    $marketing = isset($_POST['marketing']);
    $terms = isset($_POST['terms']);
    $age_verification = isset($_POST['age_verification']);
    
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
        'postal_code' => $postal_code,
        'country' => $country
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
    if (!$terms || !$age_verification) {
        $_SESSION['error'] = 'You must agree to the terms and conditions and confirm your age.';
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
        
        // Prepare full address
        $full_address = $address_line1;
        if (!empty($address_line2)) {
            $full_address .= ', ' . $address_line2;
        }
        $full_address .= ', ' . $city . ', ' . $state . ' ' . $postal_code . ', ' . $country;
        
        // Insert user into database
        $stmt = $pdo->prepare("
            INSERT INTO user (user_id, username, email, password, role, profile_pic) 
            VALUES (?, ?, ?, ?, 'member', NULL)
        ");
        
        $stmt->execute([$user_id, $username, $email, $hashed_password]);
        
        // Create user profile table if it doesn't exist (optional - for additional user data)
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS user_profiles (
                profile_id INT AUTO_INCREMENT PRIMARY KEY,
                user_id VARCHAR(11) NOT NULL,
                first_name VARCHAR(50) NOT NULL,
                last_name VARCHAR(50) NOT NULL,
                phone VARCHAR(20),
                date_of_birth DATE,
                address TEXT,
                newsletter_subscription BOOLEAN DEFAULT FALSE,
                marketing_emails BOOLEAN DEFAULT FALSE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE
            )
        ");
        
        // Insert user profile
        $stmt = $pdo->prepare("
            INSERT INTO user_profiles (user_id, first_name, last_name, phone, date_of_birth, address, newsletter_subscription, marketing_emails) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $user_id, 
            $first_name, 
            $last_name, 
            $phone, 
            $date_of_birth, 
            $full_address, 
            $newsletter, 
            $marketing
        ]);
        
        // Auto-login the user
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        $_SESSION['role'] = 'member';
        $_SESSION['profile_pic'] = null;
        
        // Clear form data
        unset($_SESSION['form_data']);
        
        $_SESSION['success'] = 'Registration successful! Welcome to ToyLand Store, ' . $first_name . '!';
        header('Location: ../member/dashboard.php');
        exit();
        
    } catch (PDOException $e) {
        $_SESSION['error'] = 'An error occurred during registration. Please try again.';
        $_SESSION['form_data'] = $_POST;
        header('Location: register.php');
        exit();
    }
}
?>
