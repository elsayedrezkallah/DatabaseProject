<?php
// Database connection configuration
$host = 'localhost';
$dbname = '3dmodel'; // Changed from 'fashion3d' to '3dmodel'
$username = 'root';    // Default XAMPP username
$password = '';        // Default XAMPP password is empty

// Create a PDO instance for database connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>



