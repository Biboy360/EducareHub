<?php
// /educarehub/forum_backend/load_posts.php
session_start();
// Adjust path relative to this file: go up one level, then to user_connection.php
require '../user_connection.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    // For partial HTML loads, just output a message rather than JSON error
    echo '<p class="text-danger text-center">You need to be logged in to view posts.</p>';
    exit;
}

$current_user_id = $_SESSION['user_id'];
$limit = 5; // Number of posts per load
$offset = isset($_GET['page']) ? intval($_GET['page']) * $limit : 0;

$stmt = $conn->prepare("
    SELECT
        p.id,
        p.content,
        p.image_path,
        p.feeling,
        p.created_at,
        u.username,
        u.firstname,
        u.lastname,
        (SELECT COUNT(*) FROM likes WHERE post_id = p.id) AS like_count,
        (SELECT COUNT(*) FROM comments WHERE post_id = p.id) AS comment_count,
        (SELECT COUNT(*) FROM likes WHERE post_id = p.id AND user_id = ?) AS user_has_liked
    FROM
        posts p
    JOIN
        users u ON p.user_id = u.user_id
    ORDER BY
        p.created_at DESC
    LIMIT ? OFFSET ?
");

$stmt->bind_param("iii", $current_user_id, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

$output = '';
if ($result->num_rows > 0) {
    while ($post = $result->fetch_assoc()) {
        $like_btn_class = $post['user_has_liked'] > 0 ? 'btn-primary' : 'btn-outline-primary';
        $like_btn_text = $post['user_has_liked'] > 0 ? 'Liked!' : 'Like';
        $feeling_html = $post['feeling'] ? '<span class="feeling">is feeling ' . htmlspecialchars($post['feeling']) . '</span>' : '';
        
        $image_html = '';
        if ($post['image_path']) {
            // Split the path into directory and filename parts
            $path_parts = explode('/', $post['image_path']);
            $filename = array_pop($path_parts); // Get the filename (e.g., "684ee9f762309_Screenshot 2025-02-25 080321.png")
            $directory_path = implode('/', $path_parts); // Get the directory path (e.g., "uploads")

            // URL-encode ONLY the filename part to handle spaces
            $encoded_filename = urlencode($filename);

            // Reconstruct the full path with the encoded filename
            // Ensure there's a slash between directory and filename if a directory exists
            $final_image_url_path = $directory_path ? $directory_path . '/' . $encoded_filename : $encoded_filename;

            // Construct the <img> tag
            // Use htmlspecialchars on the final URL to be safe, though urlencode handles most issues for URLs
            $image_html = '<img src="../' . htmlspecialchars($final_image_url_path) . '" alt="Post image" class="img-fluid mt-2">';
        }

        // Time formatting (simple for now, can be expanded)
        $time_ago = time_ago_short(strtotime($post['created_at']));

        $output .= '
        <div class="post-card" id="post-' . $post['id'] . '">
            <div class="post-header">
                <div class="author">' . htmlspecialchars($post['firstname'] . ' ' . $post['lastname']) . '</div>
                ' . $feeling_html . '
                <div class="timestamp">' . $time_ago . '</div>
            </div>
            <div class="post-content">
                <p>' . nl2br(htmlspecialchars($post['content'])) . '</p>
                ' . $image_html . '
            </div>
            <div class="post-stats">
                <span id="likes-count-' . $post['id'] . '">' . $post['like_count'] . ' Likes</span>
                <span id="comments-count-' . $post['id'] . '">' . $post['comment_count'] . ' Comments</span>
            </div>
            <div class="post-actions d-flex justify-content-around">
                <button class="btn ' . $like_btn_class . ' likeBtn w-50 me-1" data-id="' . $post['id'] . '">👍 ' . $like_btn_text . '</button>
                <button class="btn btn-outline-secondary toggleCommentsBtn w-50 ms-1" data-id="' . $post['id'] . '">💬 Comment</button>
            </div>
            <div id="comments-section-' . $post['id'] . '" class="comments-section" style="display: none;">
                <div id="comments-' . $post['id'] . '" class="comments-list">
                    </div>
                <form class="commentForm mt-2" data-id="' . $post['id'] . '">
                    <textarea name="comment" class="form-control" placeholder="Write a comment..." rows="1" required></textarea>
                    <button type="submit" class="btn btn-primary">Post</button>
                </form>
            </div>
        </div>';
    }
} else {

}

echo $output;

function time_ago_short($timestamp) {
    $diff = time() - $timestamp;
    if ($diff < 60) return $diff . 's ago';
    $diff = round($diff / 60);
    if ($diff < 60) return $diff . 'm ago';
    $diff = round($diff / 60);
    if ($diff < 24) return $diff . 'h ago';
    $diff = round($diff / 24);
    if ($diff < 7) return $diff . 'd ago';
    return date('M j', $timestamp); // e.g., Jan 15
}

$stmt->close();
$conn->close();
?>