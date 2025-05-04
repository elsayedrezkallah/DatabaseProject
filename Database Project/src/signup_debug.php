<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Create a log file
$logFile = __DIR__ . '/signup_log.txt';
file_put_contents($logFile, "--- New signup attempt: " . date('Y-m-d H:i:s') . " ---\n", FILE_APPEND);

// Log function
function logMessage($message) {
    global $logFile;
    file_put_contents($logFile, $message . "\n", FILE_APPEND);
}

// Include database connection
require_once __DIR__ . '/db.php';
logMessage("Database connection included");

// Set headers for JSON response
header('Content-Type: application/json');

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    logMessage("POST request received");
    
    // Log all POST data
    logMessage("POST data: " . print_r($_POST, true));
    
    // Get form data
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    logMessage("Name: $name, Email: $email, Password length: " . strlen($password));
    
    // Basic validation
    if (empty($name) || empty($email) || empty($password)) {
        logMessage("Validation failed: Empty fields");
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }
    
    try {
        logMessage("Starting database operations");
        
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        logMessage("Email check query executed");
        
        if ($stmt->rowCount() > 0) {
            logMessage("Email already exists in database");
            echo json_encode(['success' => false, 'message' => 'Email already in use']);
            exit;
        }
        
        // Split name into first and last name
        $nameParts = explode(' ', $name, 2);
        $fname = $nameParts[0];
        $lname = isset($nameParts[1]) ? $nameParts[1] : '';
        logMessage("Name split into: First='$fname', Last='$lname'");
        
        // Generate username from email
        $username = strtolower(explode('@', $email)[0]);
        logMessage("Generated username: $username");
        
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        logMessage("Password hashed");
        
        // Insert user data
        $sql = "INSERT INTO users (username, email, pass_hash, fname, lname) VALUES (?, ?, ?, ?, ?)";
        logMessage("Preparing SQL: $sql");
        
        $stmt = $pdo->prepare($sql);
        logMessage("Statement prepared");
        
        $result = $stmt->execute([$username, $email, $hashed_password, $fname, $lname]);
        logMessage("Execute result: " . ($result ? "SUCCESS" : "FAILURE"));
        
        if ($result) {
            $userId = $pdo->lastInsertId();
            logMessage("User created with ID: $userId");
            echo json_encode(['success' => true, 'message' => 'Account created successfully', 'userId' => $userId]);
        } else {
            logMessage("Database insert failed without exception");
            echo json_encode(['success' => false, 'message' => 'Failed to create account']);
        }
    } catch (PDOException $e) {
        logMessage("PDO Exception: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()]);
    } catch (Exception $e) {
        logMessage("General Exception: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
    }
    exit;
}

logMessage("Not a POST request, redirecting to signup.html");
// If not a POST request, redirect to signup page
header('Location: signup.html');
?>