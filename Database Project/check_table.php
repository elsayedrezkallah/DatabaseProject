<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection
require_once __DIR__ . '/src/db.php';

echo "<h1>Users Table Structure</h1>";

try {
    // Check if table exists
    $tables = $pdo->query("SHOW TABLES LIKE 'users'")->fetchAll();
    
    if (count($tables) === 0) {
        echo "<p style='color:red'>The 'users' table does not exist!</p>";
        
        // Create the table
        echo "<h2>Creating users table...</h2>";
        
        $sql = "CREATE TABLE users (
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
        echo "<p style='color:green'>Users table created successfully!</p>";
    } else {
        echo "<p style='color:green'>The 'users' table exists.</p>";
        
        // Show table structure
        echo "<h2>Table Structure:</h2>";
        $columns = $pdo->query("DESCRIBE users")->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        foreach ($columns as $column) {
            echo "<tr>";
            foreach ($column as $key => $value) {
                echo "<td>" . (is_null($value) ? "NULL" : htmlspecialchars($value)) . "</td>";
            }
            echo "</tr>";
        }
        
        echo "</table>";
        
        // Show any records
        echo "<h2>Existing Records:</h2>";
        $users = $pdo->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($users) > 0) {
            echo "<table border='1' cellpadding='5'>";
            echo "<tr>";
            foreach ($users[0] as $key => $value) {
                echo "<th>" . htmlspecialchars($key) . "</th>";
            }
            echo "</tr>";
            
            foreach ($users as $user) {
                echo "<tr>";
                foreach ($user as $value) {
                    echo "<td>" . (is_null($value) ? "NULL" : htmlspecialchars($value)) . "</td>";
                }
                echo "</tr>";
            }
            
            echo "</table>";
        } else {
            echo "<p>No records found in the users table.</p>";
        }
    }
} catch (PDOException $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}
?>