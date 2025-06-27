<?php
session_start(); // Ensure session is started at the very top

error_reporting(E_ALL);
ini_set('display_errors', 1);

$errors = [];
$success = "";

// Establish database connection
// IMPORTANT: Ensure your 'educarehub' database and 'users' table exist
// and that 'root' user has appropriate permissions with no password.
$db = mysqli_connect('localhost', 'root', '', 'educarehub');

// Check if connection was successful
if (!$db) {
    // If connection fails, log the error and display a generic message (avoid showing raw connection details)
    error_log("Database connection failed: " . mysqli_connect_error());
    $_SESSION['errors']['database_connection'] = "Could not connect to the database. Please try again later.";
    header("Location: add_users.php"); // Redirect back to the form page
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and retrieve form inputs
    $firstname = mysqli_real_escape_string($db, $_POST['firstname'] ?? '');
    $lastname = mysqli_real_escape_string($db, $_POST['lastname'] ?? '');
    $birthdate = mysqli_real_escape_string($db, $_POST['birthdate'] ?? '');
    $username = mysqli_real_escape_string($db, $_POST['username'] ?? '');
    $password_raw = $_POST['password'] ?? ''; // Get the raw password for validation and hashing
    $email = mysqli_real_escape_string($db, $_POST['email'] ?? '');
    $tier = mysqli_real_escape_string($db, $_POST['tier'] ?? '');
    $points = mysqli_real_escape_string($db, $_POST['points'] ?? '');

    // Store old inputs in session for re-population if there are errors
    $_SESSION['old'] = $_POST;

    // --- Input Validation ---
    if (empty($firstname)) $errors['firstname'] = "First name is required.";
    if (empty($lastname)) $errors['lastname'] = "Last name is required.";
    if (empty($birthdate)) $errors['birthdate'] = "Birthdate is required.";
    if (empty($username)) $errors['username'] = "Username is required.";
    if (empty($password_raw)) { // Validate the raw password input
        $errors['password'] = "Password is required.";
    } elseif (strlen($password_raw) < 6) { // Example: Minimum password length
        $errors['password'] = "Password must be at least 6 characters long.";
    }
    if (empty($email)) $errors['email'] = "Email is required.";
    // Basic email format validation
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    }
    if (empty($tier)) $errors['tier'] = "Tier is required.";
    if (!isset($_POST['points']) || $_POST['points'] === '') {
        $errors['points'] = "Points are required.";
    } elseif (!is_numeric($points)) {
        $errors['points'] = "Points must be a number.";
    }

    // If no initial validation errors, check for username/email duplication
    if (empty($errors)) {
        $check_query = "SELECT username, email FROM users WHERE username = '$username' OR email = '$email' LIMIT 1";
        $result = mysqli_query($db, $check_query);
        
        if ($result === false) {
             // Handle query error
            error_log("Duplication check query failed: " . mysqli_error($db));
            $errors['database'] = "Error checking for existing user. Please try again.";
        } else {
            $existingUser = mysqli_fetch_assoc($result);
            
            if ($existingUser) {
                if ($existingUser['username'] === $username) {
                    $errors['username'] = "Username already exists. Please choose a different one.";
                }
                if ($existingUser['email'] === $email) {
                    $errors['email'] = "Email already exists. Please use a different email.";
                }
            }
        }
    }

    // If there are no errors after all checks, proceed with insertion
    if (empty($errors)) {
        // Hash the password securely
        $hashed_password = password_hash($password_raw, PASSWORD_DEFAULT);

        $insert_query = "
            INSERT INTO users (firstname, lastname, birthdate, username, password, email, tier, points)
            VALUES (
                '$firstname', 
                '$lastname', 
                '$birthdate', 
                '$username', 
                '$hashed_password', 
                '$email', 
                '$tier', 
                '$points'
            )
        ";

        if (mysqli_query($db, $insert_query)) {
            $_SESSION['success'] = "User successfully added.";
            unset($_SESSION['old']); // Clear old input on success
        } else {
            // Log the specific database error for debugging
            error_log("User insertion failed: " . mysqli_error($db) . " Query: " . $insert_query);
            $errors['database_insert'] = "Failed to add user due to a database error. Please check server logs.";
        }
    }
    
    // Store errors in session to be displayed on the form page
    $_SESSION['errors'] = $errors;

    // Redirect back to the user management page (add_users.php)
    header("Location: add_users.php"); 
    exit(); // Always exit after a header redirect
}

// Close database connection if the script finishes without POST request
mysqli_close($db);
?>
