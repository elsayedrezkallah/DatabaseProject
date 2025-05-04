<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection with correct path
require_once 'db.php';

try {
    echo "<h1>Database Structure Check</h1>";
    
    // Check connection
    echo "<p>Connected to database: <strong>{$dbname}</strong></p>";
    
    // List all tables
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "<h2>Tables in database:</h2>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>{$table}</li>";
    }
    echo "</ul>";
    
    // Check if users table exists
    if (in_array('users', $tables)) {
        echo "<h2>Structure of 'users' table:</h2>";
        $columns = $pdo->query("DESCRIBE users")->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        foreach ($columns as $column) {
            echo "<tr>";
            echo "<td>{$column['Field']}</td>";
            echo "<td>{$column['Type']}</td>";
            echo "<td>{$column['Null']}</td>";
            echo "<td>{$column['Key']}</td>";
            echo "<td>{$column['Default']}</td>";
            echo "<td>{$column['Extra']}</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        
        // Test insert query
        echo "<h2>Test Insert Query:</h2>";
        
        // Create test data
        $test_username = "testuser_" . time();
        $test_email = "test_" . time() . "@example.com";
        $test_password = password_hash("test123", PASSWORD_DEFAULT);
        $test_fname = "Test";
        $test_lname = "User";
        
        // Show the query we're about to execute
        echo "<p>Executing query: <code>INSERT INTO users (username, email, pass_hash, fname, lname) VALUES ('{$test_username}', '{$test_email}', '[HASHED PASSWORD]', '{$test_fname}', '{$test_lname}')</code></p>";
        
        // Try the insert
        $stmt = $pdo->prepare("INSERT INTO users (username, email, pass_hash, fname, lname) VALUES (?, ?, ?, ?, ?)");
        $result = $stmt->execute([$test_username, $test_email, $test_password, $test_fname, $test_lname]);
        
        if ($result) {
            $id = $pdo->lastInsertId();
            echo "<p style='color:green'>✓ Insert successful! New user ID: {$id}</p>";
            
            // Show the inserted record
            echo "<h3>Inserted Record:</h3>";
            $user = $pdo->query("SELECT * FROM users WHERE id = {$id}")->fetch(PDO::FETCH_ASSOC);
            
            echo "<table border='1' cellpadding='5'>";
            echo "<tr>";
            foreach ($user as $key => $value) {
                echo "<th>{$key}</th>";
            }
            echo "</tr><tr>";
            foreach ($user as $value) {
                echo "<td>" . (is_null($value) ? "NULL" : htmlspecialchars($value)) . "</td>";
            }
            echo "</tr></table>";
        } else {
            echo "<p style='color:red'>✗ Insert failed!</p>";
        }
    } else {
        echo "<p style='color:red'>✗ 'users' table does not exist in the database!</p>";
        
        // Show SQL to create the table
        echo "<h3>SQL to create users table:</h3>";
        echo "<pre>" . htmlspecialchars("
CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `pass_hash` varchar(255) NOT NULL,
  `fname` varchar(50) DEFAULT NULL,
  `lname` varchar(50) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        ") . "</pre>";
        
        // Try to create the table
        echo "<h3>Attempting to create users table:</h3>";
        try {
            $pdo->exec("
                CREATE TABLE `users` (
                  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                  `username` varchar(50) NOT NULL,
                  `email` varchar(100) NOT NULL,
                  `pass_hash` varchar(255) NOT NULL,
                  `fname` varchar(50) DEFAULT NULL,
                  `lname` varchar(50) DEFAULT NULL,
                  `phone` varchar(30) DEFAULT NULL,
                  `is_active` tinyint(1) NOT NULL DEFAULT 1,
                  `email_verified_at` timestamp NULL DEFAULT NULL,
                  `last_login` timestamp NULL DEFAULT NULL,
                  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                  PRIMARY KEY (`id`),
                  UNIQUE KEY `users_username_unique` (`username`),
                  UNIQUE KEY `users_email_unique` (`email`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ");
            echo "<p style='color:green'>✓ Table created successfully!</p>";
        } catch (PDOException $e) {
            echo "<p style='color:red'>✗ Failed to create table: " . $e->getMessage() . "</p>";
        }
    }
    
} catch (PDOException $e) {
    echo "<p style='color:red'>Database error: " . $e->getMessage() . "</p>";
}
?>


