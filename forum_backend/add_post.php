<?php
// /educarehub/forum_backend/add_post.php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "unauthorized"; // Indicate user is not logged in
    exit;
}


// Adjust path relative to this file: go up one level, then to user_connection.php
include '../user_connection.php';

$user_id = $_SESSION['user_id'];
$content = trim($_POST['content'] ?? '');
$feeling = trim($_POST['feeling'] ?? '');

// Check if there's *any* content (text, feeling, or an successfully uploaded image)
$has_image_upload = (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK);

if (empty($content) && empty($feeling) && !$has_image_upload) {
    echo "empty"; // Indicate that no substantial content was provided
    exit;
}

$image_path = null; // Default to NULL for no image
if ($has_image_upload) {
    // The upload directory should be relative to the main 'educarehub' directory
    // so the path stored in DB is correct for access from peer_forum.php (e.g., ../uploads/image.jpg)
    $upload_dir = '../uploads/'; // Navigate up from forum_backend to educarehub, then into 'uploads'

    // Create the 'uploads' directory if it doesn't exist
    if (!is_dir($upload_dir)) {
        // Use recursive true to create nested directories if needed
        if (!mkdir($upload_dir, 0777, true)) {
            error_log("Failed to create upload directory: " . $upload_dir);
            // Decide if you want to proceed without image or return an error
            // For now, we'll log and proceed without the image
        }
    }

    $filename = uniqid() . "_" . basename($_FILES['image']['name']); // Generate a unique filename
    $filepath = $upload_dir . $filename;

    // Move the uploaded file to the target directory
    if (move_uploaded_file($_FILES['image']['tmp_name'], $filepath)) {
        // Store path relative to the main 'educarehub' directory in the database
        $image_path = 'uploads/' . $filename; // This is the path that peer_forum.php will use
    } else {
        error_log("Failed to move uploaded file: " . $_FILES['image']['tmp_name'] . " to " . $filepath);
        // Log error but allow post creation without image if other content exists
    }
}

// Prepare and execute the SQL statement to insert the post
$stmt = $conn->prepare("INSERT INTO posts (user_id, content, feeling, image_path) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isss", $user_id, $content, $feeling, $image_path);

if ($stmt->execute()) {
    echo "success"; // Signal success to the frontend
} else {
    // Log the database error for debugging purposes
    error_log("Database error during post insertion: " . $conn->error);
    echo "error: " . $conn->error; // Return the database error for debugging on frontend
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>