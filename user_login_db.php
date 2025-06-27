<?php
// /educarehub/user_login_db.php
session_start();
include('user_connection.php'); // change to your actual db connection file

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare the SQL statement
    $stmt = $conn->prepare("SELECT user_id, username, firstname, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();

    // Bind result variables
    $stmt->bind_result($user_id, $db_username, $firstname, $hashed_password);

    // Fetch the result
    if ($stmt->fetch()) {
        if (password_verify($password, $hashed_password)) {
            // Login successful
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $db_username;
            $_SESSION['firstname'] = $firstname;

            // Redirect to the main index page without forcing a specific 'dashboard' page.
            // index.php will by default load 'dashboard' if no 'page' parameter is provided.
            header("Location: index.php");
            exit();
        } else {
            // Password incorrect
            header("Location: user_login.php?status=error&msg=Incorrect password");
            exit();
        }
    } else {
        // Username not found
        header("Location: user_login.php?status=error&msg=Username not found");
        exit();
    }

    $stmt->close();
} else {
    // If accessed directly without form submission
    header("Location: user_login.php?status=error&msg=Please login");
    exit();
}
?>