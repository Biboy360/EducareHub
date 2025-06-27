<?php
// api/get_user_data.php
session_start(); // Start session if user data depends on login

// Set the content type header to JSON
header('Content-Type: application/json');

// Enable error reporting, but be careful in production; log errors instead
ini_set('display_errors', 0); // Hide errors from output for API endpoints
ini_set('log_errors', 1);     // Log errors to PHP error log
error_reporting(E_ALL);

// Your database connection (copy from rewards.php, or use a shared connection)
$host = 'localhost';
$db_name = 'educarehub';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db_name);

if ($conn->connect_error) {
    // Output JSON error for connection failure
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit(); // Stop execution
}

// Example: Fetch user data (assuming 'users' table and session user_id)
$user_id = $_SESSION['user_id'] ?? null; // Get user ID from session

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    $conn->close();
    exit();
}

$stmt = $conn->prepare("SELECT username, email, points FROM users WHERE user_id = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
    $conn->close();
    exit();
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $userData = $result->fetch_assoc();
    echo json_encode(['success' => true, 'data' => $userData]);
} else {
    echo json_encode(['success' => false, 'message' => 'User data not found.']);
}

$stmt->close();
$conn->close();
?>