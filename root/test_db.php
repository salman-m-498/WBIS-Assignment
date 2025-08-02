<?php
// Simple database connection test
// This file can be deleted after testing

try {
    $pdo = new PDO("mysql:host=localhost;dbname=test;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Test if user table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'user'");
    $table_exists = $stmt->rowCount() > 0;
    
    if ($table_exists) {
        // Count users
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM user");
        $user_count = $stmt->fetch()['count'];
        
        echo "<h2>Database Connection Test</h2>";
        echo "<p style='color: green;'>✅ Database connection successful!</p>";
        echo "<p>✅ User table exists</p>";
        echo "<p>👥 Current users in database: $user_count</p>";
        
        // Show existing users (for testing only)
        $stmt = $pdo->query("SELECT user_id, username, email, role FROM user");
        $users = $stmt->fetchAll();
        
        if (!empty($users)) {
            echo "<h3>Existing Users:</h3>";
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>User ID</th><th>Username</th><th>Email</th><th>Role</th></tr>";
            foreach ($users as $user) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($user['user_id']) . "</td>";
                echo "<td>" . htmlspecialchars($user['username']) . "</td>";
                echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                echo "<td>" . htmlspecialchars($user['role']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "<h2>Database Connection Test</h2>";
        echo "<p style='color: green;'>✅ Database connection successful!</p>";
        echo "<p style='color: orange;'>⚠️ User table does not exist. Please run the SQL setup script.</p>";
    }
    
} catch(PDOException $e) {
    echo "<h2>Database Connection Test</h2>";
    echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
    echo "<p>Please make sure:</p>";
    echo "<ul>";
    echo "<li>XAMPP is running</li>";
    echo "<li>MySQL service is started</li>";
    echo "<li>Database 'test' exists</li>";
    echo "</ul>";
}
?>
