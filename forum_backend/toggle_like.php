<?php
// /educarehub/forum_backend/toggle_like.php
session_start();
// Adjust path relative to this file: go up one level, then to user_connection.php
require '../user_connection.php';

// Check if user is logged in and post_id is provided
if (!isset($_SESSION['user_id']) || !isset($_POST['post_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Invalid request or user not logged in.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$post_id = $_POST['post_id'];

// Basic validation for post_id
if (!filter_var($post_id, FILTER_VALIDATE_INT)) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Invalid Post ID.']);
    exit;
}

$conn->begin_transaction(); // Start transaction for atomicity

try {
    // Check if the like already exists for this user and post
    $stmt = $conn->prepare("SELECT id FROM likes WHERE user_id = ? AND post_id = ?");
    $stmt->bind_param("ii", $user_id, $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $like = $result->fetch_assoc();
    $stmt->close(); // Close statement

    $action = ''; // To indicate if it was liked or unliked
    if ($like) {
        // User has already liked the post, so delete the like (unlike)
        $delete_stmt = $conn->prepare("DELETE FROM likes WHERE id = ?");
        $delete_stmt->bind_param("i", $like['id']);
        $delete_stmt->execute();
        $delete_stmt->close(); // Close statement
        $action = 'unliked';
    } else {
        // User has not liked the post, so insert a new like
        $insert_stmt = $conn->prepare("INSERT INTO likes (user_id, post_id) VALUES (?, ?)");
        $insert_stmt->bind_param("ii", $user_id, $post_id);
        $insert_stmt->execute();
        $insert_stmt->close(); // Close statement
        $action = 'liked';
    }

    // Get the new total like count for the post
    $count_stmt = $conn->prepare("SELECT COUNT(*) as like_count FROM likes WHERE post_id = ?");
    $count_stmt->bind_param("i", $post_id);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $new_like_count = $count_result->fetch_assoc()['like_count'];
    $count_stmt->close(); // Close statement

    $conn->commit(); // Commit the transaction
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'action' => $action, 'like_count' => $new_like_count]);

} catch (mysqli_sql_exception $exception) {
    $conn->rollback(); // Rollback on error
    error_log("SQL Error in toggle_like.php: " . $exception->getMessage());
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $exception->getMessage()]);
} catch (Exception $e) {
    $conn->rollback();
    error_log("General Error in toggle_like.php: " . $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'An unexpected error occurred: ' . $e->getMessage()]);
} finally {
    $conn->close();
}
?>