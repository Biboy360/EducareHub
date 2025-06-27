<?php
// /educarehub/forum_backend/load_comments.php
session_start();
// Adjust path relative to this file: go up one level, then to user_connection.php
require '../user_connection.php';

// Ensure post_id is provided
if (!isset($_GET['post_id'])) {
    echo '<p class="text-danger">Post ID not provided.</p>';
    exit;
}

$post_id = $_GET['post_id'];

// Basic validation for post_id
if (!filter_var($post_id, FILTER_VALIDATE_INT)) {
    echo '<p class="text-danger">Invalid Post ID.</p>';
    exit;
}

$stmt = $conn->prepare("
    SELECT
        c.comment,
        c.created_at,
        u.firstname,
        u.lastname,
        u.username
    FROM
        comments c
    JOIN
        users u ON c.user_id = u.user_id
    WHERE
        c.post_id = ?
    ORDER BY
        c.created_at ASC
");

$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

$output = '';
if ($result->num_rows > 0) {
    while ($comment = $result->fetch_assoc()) {
        $output .= '
        <div class="comment-card">
            <strong>' . htmlspecialchars($comment['firstname'] . ' ' . $comment['lastname']) . ':</strong>
            ' . nl2br(htmlspecialchars($comment['comment'])) . '
        </div>';
    }
} else {
    $output = '<p class="text-center text-muted mt-2">No comments yet. Be the first to comment!</p>';
}

echo $output;

$stmt->close();
$conn->close();
?>