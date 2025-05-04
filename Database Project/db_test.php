<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Database Connection Test</h1>";

// Include the database file with proper path
require_once __DIR__ . '/src/db.php';

try {
    echo "<p>Connected to database: <strong>{$dbname}</strong></p>";
    
    // Test query - show tables
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h2>Tables in database:</h2>";
    if (count($tables) > 0) {
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>{$table}</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No tables found. Creating users table...</p>";
        
        // Create users table if it doesn't exist
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            email VARCHAR(100) NOT NULL UNIQUE,
            pass_hash VARCHAR(255) NOT NULL,
            fname VARCHAR(50) NOT NULL,
            lname VARCHAR(50) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            last_login TIMESTAMP NULL
        )";
        
        $pdo->exec($sql);
        echo "<p>Users table created successfully!</p>";
    }
    
    // Check if users table exists now
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    if (in_array('users', $tables)) {
        echo "<p>Users table exists. Testing insert...</p>";
        
        // Create test user
        $username = "test_" . time();
        $email = "test_" . time() . "@example.com";
        $password = password_hash("test123", PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO users (username, email, pass_hash, fname, lname) VALUES (?, ?, ?, ?, ?)");
        $result = $stmt->execute([$username, $email, $password, "Test", "User"]);
        
        if ($result) {
            echo "<p>Test user created successfully!</p>";
            echo "<p>Username: {$username}</p>";
            echo "<p>Email: {$email}</p>";
            echo "<p>Password: test123</p>";
        } else {
            echo "<p>Failed to create test user.</p>";
        }
    }
    
} catch (PDOException $e) {
    echo "<p style='color:red'>Database error: " . $e->getMessage() . "</p>";
}
?>