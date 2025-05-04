<?php
// Include database connection
require_once __DIR__ . '/db.php';

// Set headers for JSON response
header('Content-Type: application/json');

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Basic validation
    if (empty($name) || empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }
    
    try {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => false, 'message' => 'Email already in use']);
            exit;
        }
        
        // Split name into first and last name
        $nameParts = explode(' ', $name, 2);
        $fname = $nameParts[0];
        $lname = isset($nameParts[1]) ? $nameParts[1] : '';
        
        // Generate username from email
        $username = strtolower(explode('@', $email)[0]);
        
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert user data
        $stmt = $pdo->prepare("INSERT INTO users (username, email, pass_hash, fname, lname) VALUES (?, ?, ?, ?, ?)");
        $result = $stmt->execute([$username, $email, $hashed_password, $fname, $lname]);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Account created successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create account']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()]);
    }
    exit;
}

// If not a POST request, redirect to signup page
header('Location: signup.html');
?>




