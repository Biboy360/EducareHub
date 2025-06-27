<?php
// /educarehub/forum_backend/add_comment.php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "unauthorized";
    exit;
}

// Adjust path relative to this file: go up one level, then to user_connection.php
include '../user_connection.php';

$user_id = $_SESSION['user_id'];
$post_id = $_POST['post_id'] ?? '';
$comment_content = trim($_POST['comment'] ?? '');

if (empty($post_id) || empty($comment_content)) {
    echo "empty";
    exit;
}

// Basic validation for post_id to ensure it's an integer
if (!filter_var($post_id, FILTER_VALIDATE_INT)) {
    echo "invalid_post_id";
    exit;
}

$stmt = $conn->prepare("INSERT INTO comments (user_id, post_id, comment) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $user_id, $post_id, $comment_content);

if ($stmt->execute()) {
    echo "success";
} else {
    error_log("Database error adding comment: " . $conn->error);
    echo "error: " . $conn->error;
}

$stmt->close();
$conn->close();
?>