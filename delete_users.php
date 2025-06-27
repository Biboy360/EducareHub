<?php
session_start();
include_once('connection.php');

$user_id = (int)($_GET['id'] ?? 0);

if ($user_id > 0) {
    try {
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['success_message'] = "User deleted successfully!";
        } else {
            $_SESSION['error_message'] = "User not found or could not be deleted.";
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error deleting user: " . $e->getMessage();
    }
} else {
    $_SESSION['error_message'] = "Invalid user ID.";
}

header("Location: add_users.php");
exit();
?>