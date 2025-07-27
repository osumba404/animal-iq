<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once 'header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("<div class='error-message'>Invalid thread ID.</div>");
}

$thread_id = (int) $_GET['id'];

// UPDATE: Edit post if edit mode triggered
if (isset($_GET['edit']) && is_logged_in()) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM forum_posts WHERE id = ? AND author_id = ?");
    $stmt->execute([$edit_id, $_SESSION['user_id']]);
    $edit_post = $stmt->fetch();
    if (!$edit_post) {
        die("<div class='error-message'>Edit not allowed.</div>");
    }
}

// UPDATE: Delete post if delete request triggered
if (isset($_GET['delete']) && is_logged_in()) {
    $delete_id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM forum_posts WHERE id = ? AND author_id = ?");
    $stmt->execute([$delete_id, $_SESSION['user_id']]);
    header("Location: topic.php?id=$thread_id");
    exit;
}

// Handle reply submit (new or edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && is_logged_in()) {
    $content = trim($_POST['content'] ?? '');
    if (!empty($content)) {
        if (isset($_POST['edit_id'])) {
            // Update existing post
            $edit_id = (int)$_POST['edit_id'];
            $stmt = $pdo->prepare("UPDATE forum_posts SET content = ? WHERE id = ? AND author_id = ?");
            $stmt->execute([$content, $edit_id, $_SESSION['user_id']]);
        } else {
            // Insert new reply
            $stmt = $pdo->prepare("INSERT INTO forum_posts (thread_id, author_id, content) VALUES (?, ?, ?)");
            $stmt->execute([$thread_id, $_SESSION['user_id'], $content]);

            $pdo->prepare("UPDATE forum_threads SET last_activity_at = NOW() WHERE id = ?")
                 ->execute([$thread_id]);
        }
        header("Location: topic.php?id=$thread_id");
        exit;
    }
}

// Fetch thread info
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

require_once 'header.php';
require_once 'nav.php';
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
</style>

<div class="thread-container">
  <header class="thread-header">
    <h1 class="thread-title"><?php echo htmlspecialchars($thread['title']); ?></h1>
    <div class="thread-meta">
      <div class="thread-meta-item">
        <span>👤</span>
        <span><?php echo htmlspecialchars($thread['name']); ?></span>
      </div>
      <div class="thread-meta-item">
        <span>👁️</span>
        <span><?php echo number_format($thread['views']); ?> views</span>
      </div>
      <div class="thread-meta-item">
        <span>🕒</span>
        <span><?php echo date('F j, Y \a\t g:i A', strtotime($thread['last_activity_at'])); ?></span>
      </div>
    </div>
  </header>

  <div class="post-list">
    <?php foreach ($posts as $post): ?>
      <div class="post-card" id="post-<?php echo $post['id']; ?>">
        <div class="post-header">
          <img src="<?php echo htmlspecialchars($post['profile_picture']); ?>" class="post-avatar" alt="<?php echo htmlspecialchars($post['name']); ?>">
          <div>
            <div class="post-author"><?php echo htmlspecialchars($post['name']); ?></div>
            <div class="post-time"><?php echo date('F j, Y \a\t g:i A', strtotime($post['created_at'])); ?></div>
          </div>
        </div>
        
        <div class="post-content">
          <?php echo nl2br(htmlspecialchars($post['content'])); ?>
        </div>
        
        <?php if (is_logged_in() && $_SESSION['user_id'] == $post['author_id']): ?>
          <div class="post-actions">
            <a href="?id=<?php echo $thread_id; ?>&edit=<?php echo $post['id']; ?>" class="post-action">
              <span>✏️</span> Edit
            </a>
            <a href="?id=<?php echo $thread_id; ?>&delete=<?php echo $post['id']; ?>" class="post-action" onclick="return confirm('Are you sure you want to delete this post?')">
              <span>🗑️</span> Delete
            </a>
          </div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>

  <?php if (is_logged_in()): ?>
    <div class="reply-form-container">
      <h3 class="reply-form-title"><?php echo isset($edit_post) ? "✏️ Edit Your Post" : "💬 Reply to This Thread"; ?></h3>
      <form method="post">
        <textarea name="content" class="reply-textarea" required><?php echo isset($edit_post) ? htmlspecialchars($edit_post['content']) : ''; ?></textarea>
        <?php if (isset($edit_post)): ?>
          <input type="hidden" name="edit_id" value="<?php echo $edit_post['id']; ?>">
        <?php endif; ?>
        <button type="submit" class="reply-submit">
          <?php echo isset($edit_post) ? "Update Post" : "Post Reply"; ?>
        </button>
      </form>
    </div>
  <?php else: ?>
    <div class="login-prompt">
      <p><a href="login.php">Login</a> to participate in this discussion</p>
    </div>
  <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>