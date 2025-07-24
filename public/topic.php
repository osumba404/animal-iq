<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once 'header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid thread ID.");
}

$thread_id = (int) $_GET['id'];

// UPDATE: Edit post if edit mode triggered
if (isset($_GET['edit']) && is_logged_in()) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM forum_posts WHERE id = ? AND author_id = ?");
    $stmt->execute([$edit_id, $_SESSION['user_id']]);
    $edit_post = $stmt->fetch();
    if (!$edit_post) {
        die("Edit not allowed.");
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
if (!$thread) die("Thread not found.");

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

<h1><?php echo htmlspecialchars($thread['title']); ?></h1>
<p><strong>Started by:</strong> <?php echo htmlspecialchars($thread['name']); ?> |
   <strong>Views:</strong> <?php echo $thread['views']; ?> |
   <strong>Last activity:</strong> <?php echo $thread['last_activity_at']; ?></p>
<hr>

<?php foreach ($posts as $post): ?>
    <div style="margin-bottom: 15px; background: #f9f9f9; padding: 10px;">
        <img src="<?php echo htmlspecialchars($post['profile_picture']); ?>" width="35" height="35" style="border-radius:50%;vertical-align:middle;">
        <strong><?php echo htmlspecialchars($post['name']); ?></strong>
        <span style="color: gray; font-size: 0.9em;">@ <?php echo $post['created_at']; ?></span>
        <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
        <?php if (is_logged_in() && $_SESSION['user_id'] == $post['author_id']): ?>
            <a href="?id=<?php echo $thread_id; ?>&edit=<?php echo $post['id']; ?>">✏️ Edit</a>
            <a href="?id=<?php echo $thread_id; ?>&delete=<?php echo $post['id']; ?>" onclick="return confirm('Delete this post?')">🗑️ Delete</a>
        <?php endif; ?>
    </div>
<?php endforeach; ?>

<hr>

<?php if (is_logged_in()): ?>
    <h3><?php echo isset($edit_post) ? "Edit Post" : "Reply to Thread"; ?></h3>
    <form method="post">
        <textarea name="content" rows="5" required><?php echo isset($edit_post) ? htmlspecialchars($edit_post['content']) : ''; ?></textarea><br>
        <?php if (isset($edit_post)): ?>
            <input type="hidden" name="edit_id" value="<?php echo $edit_post['id']; ?>">
        <?php endif; ?>
        <button type="submit"><?php echo isset($edit_post) ? "Update" : "Post Reply"; ?></button>
    </form>
<?php else: ?>
    <p><a href="login.php">Login</a> to reply.</p>
<?php endif; ?>

<?php require_once 'footer.php'; ?>
