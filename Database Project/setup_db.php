<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Database Setup</h1>";

// Connect without database first
try {
    $host = 'localhost';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p>Connected to MySQL server</p>";
    
    // Create database if it doesn't exist
    $dbname = '3dmodel';
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
    echo "<p>Database '$dbname' created or already exists</p>";
    
    // Select the database
    $pdo->exec("USE `$dbname`");
    
    // Create users table
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
    echo "<p>Users table created or already exists</p>";
    
    // Create products table
    $sql = "CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        category VARCHAR(50) NOT NULL,
        image_url VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);
    echo "<p>Products table created or already exists</p>";
    
    echo "<h2>Database setup completed successfully!</h2>";
    echo "<p>You can now <a href='index.html'>go to the homepage</a> or <a href='db_test.php'>test the database connection</a>.</p>";
    
} catch(PDOException $e) {
    echo "<p style='color:red'>Database setup failed: " . $e->getMessage() . "</p>";
}
?>