<?php
session_start();

// Only clear admin session variables
unset($_SESSION['admin_id']);
unset($_SESSION['admin_username']);
unset($_SESSION['admin_email']);
unset($_SESSION['admin_role']);

 session_destroy();

// Redirect to admin login page
header('Location: admin_login.php');
exit();
