<?php
error_reporting(E_ALL); // Display all PHP errors
ini_set('display_errors', 1); // Ensure errors are displayed
session_start();

// Include your database connection file
include('db/connection.php'); // Ensure this file returns the $db connection object

// Check if a database connection was established
if (!isset($db) || !$db) {
    $_SESSION['message_type'] = 'error';
    $_SESSION['message'] = 'Database connection error. Please try again later.';
    header("Location: add_admins.php");
    exit();
}

// SuperAdmin check before processing
if (!isset($_SESSION['admins']['role']) || $_SESSION['admins']['role'] !== 'super_admin') {
    $_SESSION['message_type'] = 'error';
    $_SESSION['message'] = 'Access denied. Only Super Admin can create admins.';
    header('Location: add_admins.php');
    exit();
}

$errors = [];
$old_input = $_POST; // Store all POST data for repopulation

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Retrieve and Sanitize Input
    $fullname = trim($_POST['fullname'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password_raw = $_POST['password'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $salary = trim($_POST['salary'] ?? ''); // Corrected to 'salary' matching form name
    $role = trim($_POST['role'] ?? '');

    // 2. Validate Input
    if (empty($fullname)) {
        $errors['fullname'] = "Full name is required.";
    } elseif (strlen($fullname) < 3) {
        $errors['fullname'] = "Full name must be at least 3 characters.";
    }

    if (empty($username)) {
        $errors['username'] = "Username is required.";
    } elseif (strlen($username) < 3) {
        $errors['username'] = "Username must be at least 3 characters.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors['username'] = "Username can only contain letters, numbers, and underscores.";
    }

    if (empty($password_raw)) {
        $errors['password'] = "Password is required.";
    } elseif (strlen($password_raw) < 6) {
        $errors['password'] = "Password must be at least 6 characters.";
    }

    if (empty($email)) {
        $errors['email'] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    }

    if (empty($salary) || !is_numeric($salary) || $salary < 0) {
        $errors['salary'] = "Salary is required and must be a non-negative number.";
    }

    $allowed_roles = ['admin', 'super_admin', 'editor', 'viewer'];
    if (empty($role) || !in_array($role, $allowed_roles)) {
        $errors['role'] = "Please select a valid role.";
    }

    // 3. Check for existing username or email ONLY if initial validation passes
    if (empty($errors)) {
        $check_query = "SELECT username, email FROM admins WHERE username = ? OR email = ?";
        $stmt_check = mysqli_prepare($db, $check_query);

        if ($stmt_check) {
            mysqli_stmt_bind_param($stmt_check, "ss", $username, $email);
            mysqli_stmt_execute($stmt_check);
            $result_check = mysqli_stmt_get_result($stmt_check);

            while ($existingUser = mysqli_fetch_assoc($result_check)) {
                if ($existingUser['username'] === $username) {
                    $errors['username'] = "Username already exists. Please choose a different one.";
                }
                if ($existingUser['email'] === $email) {
                    $errors['email'] = "Email address already exists. Please use a different one.";
                }
            }
            mysqli_stmt_close($stmt_check);
        } else {
            // Log this error, but don't show specific details to the user for security
            error_log("Failed to prepare check statement: " . mysqli_error($db));
            $_SESSION['message_type'] = 'error';
            $_SESSION['message'] = 'A database error occurred. Please try again.';
            $_SESSION['old_admin_input'] = $old_input;
            $_SESSION['admin_form_errors'] = $errors; // Pass potential other errors
            header("Location: add_admins.php");
            exit();
        }
    }

    // 4. If no errors, proceed with insertion
    if (empty($errors)) {
        $hashed_password = password_hash($password_raw, PASSWORD_DEFAULT);

        $insert_query = "
            INSERT INTO admins (fullname, username, password, email, Salary, role)
            VALUES (?, ?, ?, ?, ?, ?)
        ";
        $stmt_insert = mysqli_prepare($db, $insert_query);

        if ($stmt_insert) {
            mysqli_stmt_bind_param($stmt_insert, "ssssds", $fullname, $username, $hashed_password, $email, $salary, $role); // 'd' for double (salary)
            
            if (mysqli_stmt_execute($stmt_insert)) {
                $_SESSION['message_type'] = 'success';
                $_SESSION['message'] = 'Admin "' . htmlspecialchars($username) . '" successfully added!';
                mysqli_stmt_close($stmt_insert);
                mysqli_close($db);
                header("Location: add_admins.php");
                exit();
            } else {
                $_SESSION['message_type'] = 'error';
                $_SESSION['message'] = 'Failed to add admin: ' . mysqli_error($db); // For debugging, refine for production
                error_log("Error inserting admin: " . mysqli_error($db));
            }
            mysqli_stmt_close($stmt_insert);
        } else {
            $_SESSION['message_type'] = 'error';
            $_SESSION['message'] = 'Database query preparation failed: ' . mysqli_error($db);
            error_log("Error preparing insert statement: " . mysqli_error($db));
        }
    } else {
        // If there are validation errors, store them and old input in session
        $_SESSION['admin_form_errors'] = $errors;
        $_SESSION['old_admin_input'] = $old_input;
        $_SESSION['message_type'] = 'error';
        $_SESSION['message'] = 'Please correct the form errors.';
    }
}

// Close the database connection if it's still open (it might have been closed on success)
if (isset($db) && $db) {
    mysqli_close($db);
}

// Redirect back to add_admins.php in case of errors or if accessed directly without POST
header("Location: add_admins.php");
exit();
?>