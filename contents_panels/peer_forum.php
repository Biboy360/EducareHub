
<div class="p-3" style="background-color: #f8f9fa; border-radius: 10px;">
    <h4 class="mb-3">Peer Forum</h4>
    
    <form id="postForm" method="POST" enctype="multipart/form-data">
        <textarea name="content" class="form-control mb-2" placeholder="Share something..." rows="3"></textarea>
        <div class="d-flex justify-content-between align-items-center mb-2">
            <select name="feeling" class="form-select w-auto me-2">
                <option value="">+ Feeling</option>
                <option value="😄 Happy">😄 Happy</option>
                <option value="😢 Sad">😢 Sad</option>
                <option value="🤩 Excited">🤩 Excited</option>
                <option value="🤔 Thinking">🤔 Thinking</option>
                <option value="❤️ Loved">❤️ Loved</option>
                <option value="😴 Sleepy">😴 Sleepy</option>
                <option value="😊 Content">😊 Content</option>
                <option value="😠 Angry">😠 Angry</option>
                <option value="🥳 Celebratory">🥳 Celebratory</option>
            </select>
            <label for="post-image-input" class="btn btn-outline-secondary w-auto">
                📷 Add Photo
                <input type="file" name="image" id="post-image-input" accept="image/*" style="display: none;">
            </label>
            <button class="btn btn-primary ms-auto" type="submit">Post</button>
        </div>
    </form>

    <hr>

    <div id="postContainer"></div>
    <div id="loader" class="text-center text-muted py-3" style="display: none;">Loading more posts...</div>
</div>

<script>
let page = 0; // Start at page 0 for initial load
let loading = false;
let allPostsLoaded = false; // Flag to indicate if all posts have been fetched

async function loadPosts(reset = false) {
    if (loading || allPostsLoaded) return; // Prevent multiple simultaneous loads or if all are loaded
    loading = true;
    document.getElementById('loader').style.display = 'block';

    if (reset) {
        page = 0; // Reset page to 0 when refreshing all posts
        allPostsLoaded = false; // Allow loading again
        document.getElementById('postContainer').innerHTML = ''; // Clear existing posts
        document.getElementById('loader').textContent = 'Loading posts...'; // Reset loader text
    }

    try {
        // Corrected Path: Using root-relative path
        const res = await fetch(`/EducareHub/forum_backend/load_posts.php?page=${page}`);
        if (!res.ok) {
            const errorText = await res.text();
            throw new Error(`HTTP error! status: ${res.status} - ${errorText}`);
        }
        const postsHtml = await res.text();

        const container = document.getElementById('postContainer');
        if (reset) {
            container.innerHTML = postsHtml;
        } else {
            container.insertAdjacentHTML('beforeend', postsHtml);
        }
        
        if (postsHtml.trim() === '') {
            allPostsLoaded = true;
            document.getElementById('loader').textContent = 'No more posts to load.';
            if (reset && container.innerHTML.trim() === '') {
                container.innerHTML = '<p class="text-center text-muted">No posts yet. Be the first to share something!</p>';
            }
        } else {
            page++;
            document.getElementById('loader').style.display = 'none';
        }
    } catch (error) {
        console.error("Failed to load posts:", error);
        document.getElementById('loader').textContent = 'Error loading posts.';
        document.getElementById('loader').style.color = 'red';
    } finally {
        loading = false;
    }
}

document.getElementById('postForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    const postButton = this.querySelector('button[type="submit"]');
    postButton.disabled = true;
    const originalButtonText = postButton.textContent;
    postButton.textContent = 'Posting...';

    try {
        // Corrected Path: Using root-relative path
        const res = await fetch('/EducareHub/forum_backend/add_post.php', {
            method: 'POST',
            body: formData
        });
        const responseText = (await res.text()).trim();

        if (responseText === 'success') {
            this.reset();
            document.getElementById('post-image-input').value = '';
            loadPosts(true);
        } else if (responseText === 'unauthorized') {
            alert('You need to be logged in to post.');
        } else if (responseText === 'empty') {
            alert('Post cannot be empty. Please add content, a feeling, or an image.');
        } else {
            alert('Post failed: ' + responseText);
            console.error('Add Post Backend Error:', responseText);
        }
    } catch (error) {
        console.error("Error submitting post:", error);
        alert('An error occurred while submitting your post.');
    } finally {
        postButton.disabled = false;
        postButton.textContent = originalButtonText;
    }
});

// Infinite scrolling logic
window.addEventListener('scroll', () => {
    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 300) {
        loadPosts();
    }
});

document.addEventListener('click', async function(e) {
    // Like/Unlike button
    if (e.target.classList.contains('likeBtn')) {
        const button = e.target;
        const postId = button.dataset.id;
        
        try {
            // Corrected Path: Using root-relative path
            const res = await fetch('/EducareHub/forum_backend/toggle_like.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'post_id=' + encodeURIComponent(postId)
            });
            const result = await res.json();

            if (result.status === 'success') {
                button.textContent = result.action === 'liked' ? '👍 Liked!' : '👍 Like';
                button.classList.toggle('btn-primary', result.action === 'liked');
                button.classList.toggle('btn-outline-primary', result.action !== 'liked');

                const likeCountSpan = document.getElementById(`likes-count-${postId}`);
                if (likeCountSpan) {
                    likeCountSpan.textContent = `${result.like_count} Likes`;
                }
            } else {
                console.error("Like toggle error:", result.message);
                alert('Failed to toggle like: ' + result.message);
            }
        } catch (error) {
            console.error("Error toggling like:", error);
            alert('An error occurred while liking/unliking the post.');
        }
    }

    // Toggle comments section
    if (e.target.classList.contains('toggleCommentsBtn')) {
        const postId = e.target.dataset.id;
        const commentsSection = document.getElementById(`comments-section-${postId}`);
        if (commentsSection.style.display === 'none' || commentsSection.style.display === '') {
            commentsSection.style.display = 'block';
            loadComments(postId);
        } else {
            commentsSection.style.display = 'none';
        }
    }
});

document.addEventListener('submit', async function(e) {
    // Comment form submission
    if (e.target.classList.contains('commentForm')) {
        e.preventDefault();
        const form = e.target;
        const postId = form.dataset.id;
        const commentInput = form.querySelector('textarea[name="comment"]');
        const commentText = commentInput.value.trim();

        if (commentText === '') {
            alert('Comment cannot be empty.');
            return;
        }

        const formData = new FormData();
        formData.append('post_id', postId);
        formData.append('comment', commentText);

        try {
            // Corrected Path: Using root-relative path
            const res = await fetch('/EducareHub/forum_backend/add_comment.php', {
                method: 'POST',
                body: formData
            });
            const responseText = (await res.text()).trim();

            if (responseText === 'success') {
                commentInput.value = '';
                loadComments(postId);
                const commentCountSpan = document.getElementById(`comments-count-${postId}`);
                if (commentCountSpan) {
                    let currentCount = parseInt(commentCountSpan.textContent.split(' ')[0]);
                    commentCountSpan.textContent = `${currentCount + 1} Comments`;
                }
            } else if (responseText === 'unauthorized') {
                alert('You need to be logged in to comment.');
            } else {
                alert('Failed to add comment: ' + responseText);
                console.error('Add Comment Backend Error:', responseText);
            }
        } catch (error) {
            console.error("Error adding comment:", error);
            alert('An error occurred while adding your comment.');
        }
    }
});

function loadComments(postId) {
    const commentsListContainer = document.getElementById('comments-' + postId);
    if (!commentsListContainer) return;

    commentsListContainer.innerHTML = '<p class="text-center text-muted mt-2">Loading comments...</p>';

    // Corrected Path: Using root-relative path
    fetch(`/EducareHub/forum_backend/load_comments.php?post_id=${postId}`)
        .then(res => {
            if (!res.ok) {
                const errorText = res.text();
                throw new Error(`HTTP error! status: ${res.status} - ${errorText}`);
            }
            return res.text();
        })
        .then(html => {
            commentsListContainer.innerHTML = html;
        })
        .catch(error => {
            console.error("Failed to load comments:", error);
            commentsListContainer.innerHTML = '<p class="text-danger">Error loading comments.</p>';
        });
}

document.addEventListener('DOMContentLoaded', () => {
    loadPosts(true);
});
</script>
<style>
/* Add some basic styling for better appearance with Bootstrap classes if available */
.post-card {
    background-color: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}
.post-header {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
    flex-wrap: wrap;
}
.post-header .author {
    font-weight: bold;
    color: #333;
    margin-right: 5px;
}
.post-header .feeling {
    font-size: 0.9em;
    color: #666;
    margin-right: 10px;
}
.post-header .timestamp {
    font-size: 0.8em;
    color: #999;
    margin-left: auto;
}
.post-content {
    margin-bottom: 15px;
    word-wrap: break-word;
}
.post-content img {
    max-width: 100%;
    height: auto;
    border-radius: 5px;
    margin-top: 10px;
    display: block;
}
.post-stats {
    display: flex;
    justify-content: space-between;
    font-size: 0.9em;
    color: #666;
    border-bottom: 1px solid #eee;
    padding-bottom: 8px;
    margin-bottom: 8px;
}
.post-actions {
    display: flex;
    justify-content: space-around;
    padding-top: 8px;
    border-top: 1px solid #eee;
}
.likeBtn, .toggleCommentsBtn {
    flex-grow: 1;
    text-align: center;
    padding: 8px 0;
    cursor: pointer;
    border-radius: 5px;
    transition: background-color 0.2s;
    font-weight: 500;
}
.likeBtn:hover, .toggleCommentsBtn:hover {
    background-color: #f0f0f0;
}
/* Styling for Bootstrap-like buttons if Bootstrap is not linked */
.btn {
    display: inline-block;
    font-weight: 400;
    line-height: 1.5;
    color: #212529;
    text-align: center;
    text-decoration: none;
    vertical-align: middle;
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    user-select: none;
    background-color: transparent;
    border: 1px solid transparent;
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    border-radius: 0.25rem;
    transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
}
.btn-primary {
    color: #fff;
    background-color: #0d6efd;
    border-color: #0d6efd;
}
.btn-primary:hover {
    color: #fff;
    background-color: #0b5ed7;
    border-color: #0a58ca;
}
.btn-outline-primary {
    color: #0d6efd;
    border-color: #0d6efd;
}
.btn-outline-primary:hover {
    color: #fff;
    background-color: #0d6efd;
    border-color: #0d6efd;
}
.btn-outline-secondary {
    color: #6c757d;
    border-color: #6c757d;
}
.btn-outline-secondary:hover {
    color: #fff;
    background-color: #6c757d;
    border-color: #6c757d;
}
.form-control {
    display: block;
    width: 100%;
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #212529;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    border-radius: 0.25rem;
    transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
}
.form-control:focus {
    color: #212529;
    background-color: #fff;
    border-color: #86b7fe;
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13,110,253,.25);
}
.form-select {
    display: block;
    width: 100%;
    padding: 0.375rem 2.25rem 0.375rem 0.75rem;
    -moz-padding-start: calc(0.75rem - 3px);
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #212529;
    background-color: #fff;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 16px 12px;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
}
.d-flex { display: flex !important; }
.justify-content-between { justify-content: space-between !important; }
.align-items-center { align-items: center !important; }
.mb-2 { margin-bottom: 0.5rem !important; }
.me-2 { margin-right: 0.5rem !important; }
.ms-auto { margin-left: auto !important; }
.w-auto { width: auto !important; }
.text-center { text-align: center !important; }
.text-muted { color: #6c757d !important; }
.py-3 { padding-top: 1rem !important; padding-bottom: 1rem !important; }
.d-none { display: none !important; } /* For hiding loader initially */

/* Comments specific styles */
.comments-section {
    border-top: 1px solid #eee;
    padding-top: 10px;
    margin-top: 10px;
}
.comment-card {
    background-color: #f0f2f5;
    border-radius: 15px;
    padding: 8px 12px;
    margin-bottom: 5px;
    font-size: 0.9em;
}
.comment-card strong {
    color: #333;
    margin-right: 5px;
}
.comment-form {
    display: flex;
    gap: 5px;
    margin-top: 10px;
}
.comment-form textarea {
    flex-grow: 1;
    border: 1px solid #ddd;
    border-radius: 15px;
    padding: 8px 12px;
    resize: vertical;
    min-height: 38px; /* Match single-line input height */
    font-size: 0.9em;
}
.comment-form .btn {
    border-radius: 15px;
    padding: 8px 15px;
    font-size: 0.9em;
}
</style>