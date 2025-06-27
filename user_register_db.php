<?php
// Include the database connection
include 'user_connection.php';
session_start();

if (isset($_POST['signup'])) {
    $firstname = $_POST['fname'];
    $lastname = $_POST['lname'];
    $birthdate = $_POST['bdate'];
    $username = $_POST['uname'];
    $password = password_hash($_POST['pword'], PASSWORD_DEFAULT); // ✅ Use hashed password
    $email = $_POST['em'];

    // Check for duplicate username or email
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    $usernameExists = false;
    $emailExists = false;

    while ($row = $result->fetch_assoc()) {
        if ($row['username'] == $username) $usernameExists = true;
        if ($row['email'] == $email) $emailExists = true;
    }

    if ($usernameExists || $emailExists) {
        $errorMsg = [];
        if ($usernameExists) $errorMsg[] = "Username already exists!";
        if ($emailExists) $errorMsg[] = "Email already exists!";
        $msg = urlencode(implode(' ', $errorMsg));
        header("Location: user_register.php?status=error&msg=$msg");
        exit();
    }

    // Insert user
    $stmt = $conn->prepare("INSERT INTO users (firstname, lastname, birthdate, username, password, email) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $firstname, $lastname, $birthdate, $username, $password, $email);
    
    if ($stmt->execute()) {
        $msg = urlencode("Account successfully created! Please login.");
        header("Location: user_login.php?status=success&msg=$msg");
        exit();
    } else {
        $msg = urlencode("Registration failed.");
        header("Location: user_register.php?status=error&msg=$msg");
        exit();
    }
}
?>
