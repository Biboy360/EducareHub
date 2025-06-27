<?php
// login.php - Using mysqli

session_start(); // Start the session at the very beginning of the script

// --- Error Reporting (for Development ONLY) ---
// In production, set display_errors to Off and log errors to a file.
error_reporting(E_ALL);
ini_set('display_errors', 1);

// --- Include Database Connection ---
include('connection.php'); // This line will define $db using mysqli_connect

// --- Input Sanitization and Retrieval ---
$username = $_POST['username'] ?? '';
$password_raw = $_POST['password'] ?? ''; // Keep password_raw for now, even if not used directly

// --- Basic Input Validation ---
if (empty($username) || empty($password_raw)) {
    $_SESSION['login_message'] = 'Please enter both username and password.';
    header('location: ../admin_login.php'); // Redirect back to login page
    exit; // Stop script execution
}

// --- Check if connection is valid before proceeding ---
if (!$db) {
    $_SESSION['login_message'] = 'Database connection error. Please try again later.';
    header('location: ../admin_login.php');
    exit;
}

// --- Prepare SQL Query for Admin Authentication ---
$query = 'SELECT admin_id, fullname, username, password, email, Salary, role FROM admins WHERE username = ? LIMIT 1';

// Prepare the statement
$stmt = mysqli_prepare($db, $query);

if ($stmt) {
    // Bind parameters: "s" for string
    mysqli_stmt_bind_param($stmt, 's', $username);

    // Execute the statement
    mysqli_stmt_execute($stmt);

    // Get the result set
    $result = mysqli_stmt_get_result($stmt);

    // Fetch the admin row as an associative array
    $admin = mysqli_fetch_assoc($result);

    // Close the statement
    mysqli_stmt_close($stmt);

    // --- TEMPORARY DEBUGGING: Remove password_verify ---
    // !!! IMPORTANT: THIS IS INSECURE FOR PRODUCTION. REVERT AFTER DEBUGGING !!!
    if ($admin) { // Check if an admin record was found at all for the given username
        // At this point, we know the username exists in the DB.
        // For debugging, we're skipping password check.
        // You would normally have: if ($admin && password_verify($password_raw, $admin['password'])) {

        // Log what's happening for debugging
        error_log("Login Debug: Admin found for username: " . $username);
        // If password_verify was failing, let's see why:
        // if ($admin && !password_verify($password_raw, $admin['password'])) {
        //     error_log("Login Debug: Password mismatch for username: " . $username);
        //     error_log("Login Debug: Raw password: " . $password_raw);
        //     error_log("Login Debug: Stored hash: " . $admin['password']);
        // }


        // Password is (temporarily assumed) correct!

        // Regenerate session ID to prevent session fixation attacks.
        session_regenerate_id(true);

        // Store relevant admin data in the session for later use.
        $_SESSION['admins'] = [
            'admin_id' => $admin['admin_id'],
            'fullname' => $admin['fullname'],
            'username' => $admin['username'],
            'email'    => $admin['email'],
            'salary'   => $admin['Salary'], // Make sure 'Salary' matches your DB column name case
            'role'     => $admin['role']
        ];

        $_SESSION['admin_id'] = $admin['admin_id']; // Direct access for admin_id
        $_SESSION['login_message'] = 'Welcome Admin! (Temporary Debug Login)'; // Success message

        // Update last_login
        $now = date('Y-m-d H:i:s');
        $updateLogin = $db->prepare("UPDATE admins SET last_login = ? WHERE admin_id = ?");
        $updateLogin->bind_param("si", $now, $admin['admin_id']);
        $updateLogin->execute();
        $updateLogin->close();

        // Log activity
        $action = "Login";
        $details = "Admin logged in";
        $log = $db->prepare("INSERT INTO activity_log (admin_id, action, details) VALUES (?, ?, ?)");
        $log->bind_param("iss", $admin['admin_id'], $action, $details);
        $log->execute();
        $log->close();

        // Redirect to the admin dashboard
        header('Location: ../admin_dashboard.php');
        exit; // Stop script execution
    } else {
        // No admin found with that username
        $_SESSION['login_message'] = 'Invalid username or password.'; // Keep generic message
        header('location: ../admin_login.php'); // Redirect back to login page
        exit; // Stop script execution
    }

} else {
    // Error preparing the statement
    $error_message = "Database error: Could not prepare statement. " . mysqli_error($db);
    error_log("Login MySQLi Prepare Error: " . $error_message); // Log the error

    $_SESSION['login_message'] = 'An error occurred during login. Please try again later.';
    header('location: ../admin_login.php'); // Redirect back to login page
    exit; // Stop script execution
}

// --- Close Database Connection (Important for mysqli) ---
if (isset($db) && $db) {
    mysqli_close($db);
}

?>