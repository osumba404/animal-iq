<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once 'header.php';
require_once 'nav.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("<div class='error-message'>Invalid thread ID.</div>");
}

$thread_id = (int) $_GET['id'];

// Edit post
if (isset($_GET['edit']) && isset($_SESSION['user']['id'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM forum_posts WHERE id = ? AND author_id = ?");
    $stmt->execute([$edit_id, $_SESSION['user']['id']]);
    $edit_post = $stmt->fetch();
    if (!$edit_post) {
        die("<div class='error-message'>Edit not allowed.</div>");
    }
}

// Delete post
if (isset($_GET['delete']) && isset($_SESSION['user']['id'])) {
    $delete_id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM forum_posts WHERE id = ? AND author_id = ?");
    $stmt->execute([$delete_id, $_SESSION['user']['id']]);
}

// Handle reply submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user']['id'])) {
    $content = trim($_POST['content'] ?? '');
    if (!empty($content)) {
        if (isset($_POST['edit_id'])) {
            $edit_id = (int)$_POST['edit_id'];
            $stmt = $pdo->prepare("UPDATE forum_posts SET content = ? WHERE id = ? AND author_id = ?");
            $stmt->execute([$content, $edit_id, $_SESSION['user']['id']]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO forum_posts (thread_id, author_id, content) VALUES (?, ?, ?)");
            $stmt->execute([$thread_id, $_SESSION['user']['id'], $content]);
            $pdo->prepare("UPDATE forum_threads SET last_activity_at = NOW() WHERE id = ?")
                ->execute([$thread_id]);
        }

    }
}

// Fetch thread
$stmt = $pdo->prepare("SELECT ft.*, u.name, u.profile_picture FROM forum_threads ft 
                        JOIN users u ON ft.author_id = u.id 
                        WHERE ft.id = ?");
$stmt->execute([$thread_id]);
$thread = $stmt->fetch();
if (!$thread) die("<div class='error-message'>Thread not found.</div>");

// Update views
$pdo->prepare("UPDATE forum_threads SET views = views + 1 WHERE id = ?")->execute([$thread_id]);

// Fetch posts
$stmt = $pdo->prepare("SELECT p.*, u.name, u.profile_picture FROM forum_posts p 
                        JOIN users u ON p.author_id = u.id 
                        WHERE p.thread_id = ? ORDER BY p.created_at ASC");
$stmt->execute([$thread_id]);
$posts = $stmt->fetchAll();
?>



<style>
/* Premium Thread Page Styling */
.thread-container {
  max-width: 900px;
  margin: 2rem auto;
  padding: 0 1.5rem;
}

.thread-header {
  margin-bottom: 2.5rem;
  padding-bottom: 1.5rem;
  border-bottom: 2px solid var(--color-border-light);
}

.thread-title {
  font-size: 2.2rem;
  color: var(--color-primary);
  margin-bottom: 0.5rem;
}

.thread-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 1.5rem;
  color: var(--color-text-muted);
  margin-bottom: 1rem;
}

.thread-meta-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.post-list {
  margin: 3rem 0;
}

.post-card {
  background: var(--color-bg-primary);
  border-radius: 12px;
  padding: 1.5rem;
  margin-bottom: 1.5rem;
  box-shadow: 0 4px 12px var(--color-shadow);
  border: 1px solid var(--color-border-light);
  transition: all 0.3s ease;
}

.post-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 24px rgba(30, 24, 17, 0.15);
}

.post-header {
  display: flex;
  align-items: center;
  margin-bottom: 1rem;
}

.post-avatar {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid var(--color-primary-light);
  margin-right: 1rem;
}

.post-author {
  font-weight: bold;
  color: var(--color-primary);
}

.post-time {
  color: var(--color-text-muted);
  font-size: 0.9rem;
}

.post-content {
  line-height: 1.7;
  margin: 1.5rem 0;
  color: var(--color-text-primary);
}

.post-actions {
  display: flex;
  gap: 1rem;
  margin-top: 1rem;
}

.post-action {
  color: var(--color-primary);
  text-decoration: none;
  font-size: 0.9rem;
  display: flex;
  align-items: center;
  gap: 0.3rem;
  transition: color 0.2s ease;
}

.post-action:hover {
  color: var(--color-primary-dark);
}

.reply-form-container {
  background: var(--color-bg-secondary);
  padding: 2rem;
  border-radius: 12px;
  margin-top: 3rem;
}

.reply-form-title {
  font-size: 1.5rem;
  color: var(--color-primary);
  margin-bottom: 1.5rem;
}

.reply-textarea {
  width: 100%;
  min-height: 150px;
  padding: 1rem;
  border: 2px solid var(--color-border-light);
  border-radius: 8px;
  font-family: inherit;
  margin-bottom: 1rem;
  transition: all 0.3s ease;
}

.reply-textarea:focus {
  outline: none;
  border-color: var(--color-primary);
  box-shadow: 0 0 0 3px rgba(1, 50, 33, 0.1);
}

.reply-submit {
  background: var(--color-primary);
  color: white;
  border: none;
  border-radius: 50px;
  padding: 0.8rem 2rem;
  font-weight: bold;
  cursor: pointer;
  transition: all 0.3s ease;
}

.reply-submit:hover {
  background: var(--color-primary-dark);
  transform: translateY(-2px);
}

.login-prompt {
  text-align: center;
  padding: 2rem;
  background: var(--color-bg-secondary);
  border-radius: 12px;
  margin-top: 2rem;
}

.login-prompt a {
  color: var(--color-primary);
  font-weight: bold;
}

.error-message {
  background: var(--color-error);
  color: white;
  padding: 1.5rem;
  text-align: center;
  border-radius: 8px;
  margin: 2rem auto;
  max-width: 800px;
}

@media (max-width: 768px) {
  .thread-title {
    font-size: 1.8rem;
  }
  
  .thread-meta {
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .post-card {
    padding: 1rem;
  }
}


/* Container for edit/delete buttons */
.post-actions {
    margin-top: 8px;
    display: flex;
    gap: 10px;
    font-size: 0.9rem;
}

/* Links style */
.post-actions a {
    text-decoration: none;
    color: #0077cc;
    padding: 4px 8px;
    border-radius: 4px;
    transition: background 0.2s ease, color 0.2s ease;
}

/* Hover effect */
.post-actions a:hover {
    background: #0077cc;
    color: #fff;
}

/* Optional: make delete link red */
.post-actions a[href*="delete"] {
    color: #cc0000;
}

.post-actions a[href*="delete"]:hover {
    background: #cc0000;
    color: #fff;
}

</style>

<div class="thread-container">
  <header class="thread-header">
    <h1 class="thread-title"><?php echo htmlspecialchars($thread['title']); ?></h1>
    <div class="thread-meta">
      <div class="thread-meta-item">👤 <?php echo htmlspecialchars($thread['name']); ?></div>
      <div class="thread-meta-item">👁️ <?php echo number_format($thread['views']); ?> views</div>
      <div class="thread-meta-item">🕒 <?php echo date('F j, Y \a\t g:i A', strtotime($thread['last_activity_at'])); ?></div>
    </div>
  </header>

  <div class="post-list">
    <?php foreach ($posts as $post): ?>
      <div class="post-card" id="post-<?php echo $post['id']; ?>">
        <div class="post-header">
          <img src="../uploads/profile_pics/<?php echo htmlspecialchars($post['profile_picture']); ?>" class="post-avatar" alt="<?php echo htmlspecialchars($post['name']); ?>">
          <div>
            <div class="post-author"><?php echo htmlspecialchars($post['name']); ?></div>
            <div class="post-time"><?php echo date('F j, Y \a\t g:i A', strtotime($post['created_at'])); ?></div>
          </div>
        </div>
        <div class="post-content"><?php echo nl2br(htmlspecialchars($post['content'])); ?></div>
        <?php if (isset($_SESSION['user']['id']) && $_SESSION['user']['id'] == $post['author_id']): ?>
          <div class="post-actions">
            <a href="#" class="edit-link" data-post-id="<?php echo $post['id']; ?>" data-post-content="<?php echo htmlspecialchars($post['content']); ?>">✏️ Edit</a>
            <a href="?id=<?php echo $thread_id; ?>&delete=<?php echo $post['id']; ?>" onclick="return confirm('Are you sure?')">🗑️ Delete</a>
          </div>
        <?php endif; ?>

      </div>
    <?php endforeach; ?>
  </div>

  <div id="participation-section">
    <?php if (isset($_SESSION['user']['id'])): ?>
      <button id="show-reply-form" class="reply-submit">💬 Reply to This Thread</button>
      <div id="reply-form" style="display:none; margin-top:10px;">
        <form method="post">
          <textarea name="content" class="reply-textarea" required><?php echo isset($edit_post) ? htmlspecialchars($edit_post['content']) : ''; ?></textarea>
          <?php if (isset($edit_post)): ?>
            <input type="hidden" name="edit_id" value="<?php echo $edit_post['id']; ?>">
          <?php endif; ?>
          <button type="submit" class="reply-submit"><?php echo isset($edit_post) ? "Update Post" : "Post Reply"; ?></button>
        </form>
      </div>
    <?php else: ?>
      <div class="login-prompt">
        <p><a href="login.php">Login</a> to participate in this discussion</p>
      </div>
    <?php endif; ?>
  </div>

  <!-- Edit Post Modal -->
<div id="editModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; justify-content:center; align-items:center;">
  <div style="background:white; padding:20px; border-radius:10px; max-width:500px; width:90%; position:relative;">
    <h3>Edit Post</h3>
    <form method="post" id="editPostForm">
      <textarea name="content" id="editContent" class="reply-textarea" required></textarea>
      <input type="hidden" name="edit_id" id="editId">
      <div style="margin-top:10px; display:flex; gap:10px;">
        <button type="submit" class="reply-submit">Update Post</button>
        <button type="button" id="closeEditModal" style="background:#ccc; border:none; padding:0.8rem 1.5rem; border-radius:50px; cursor:pointer;">Cancel</button>
      </div>
    </form>
  </div>
</div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const replyBtn = document.getElementById("show-reply-form");
    const replyForm = document.getElementById("reply-form");
    if (replyBtn) {
        replyBtn.addEventListener("click", function() {
            const isHidden = replyForm.style.display === "none";
            replyForm.style.display = isHidden ? "block" : "none";
            if (isHidden) {
                replyForm.scrollIntoView({ behavior: "smooth", block: "start" });
            }
        });
    }
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Edit modal elements
    const editModal = document.getElementById("editModal");
    const editContent = document.getElementById("editContent");
    const editId = document.getElementById("editId");
    const closeEditModal = document.getElementById("closeEditModal");

    // Open modal on edit click
    document.querySelectorAll(".edit-link").forEach(link => {
        link.addEventListener("click", function(e) {
            e.preventDefault();
            editContent.value = this.getAttribute("data-post-content");
            editId.value = this.getAttribute("data-post-id");
            editModal.style.display = "flex";
        });
    });

    // Close modal
    closeEditModal.addEventListener("click", function() {
        editModal.style.display = "none";
    });

    // Close modal on outside click
    editModal.addEventListener("click", function(e) {
        if (e.target === editModal) {
            editModal.style.display = "none";
        }
    });
});
</script>


<?php require_once 'footer.php'; ?>
