<?php
require_once '../includes/db.php';
require_once 'admin_header.php';

// Delete thread (and all associated posts)
if (isset($_GET['delete_thread'])) {
    $thread_id = intval($_GET['delete_thread']);
    $pdo->prepare("DELETE FROM forum_posts WHERE thread_id = ?")->execute([$thread_id]);
    $pdo->prepare("DELETE FROM forum_threads WHERE id = ?")->execute([$thread_id]);
    header("Location: manage_forum.php");
    exit;
}

// Delete individual post
if (isset($_GET['delete_post'])) {
    $post_id = intval($_GET['delete_post']);
    $pdo->prepare("DELETE FROM forum_posts WHERE id = ?")->execute([$post_id]);
    header("Location: manage_forum.php");
    exit;
}

// Fetch all threads with author names
$threads = $pdo->query("
    SELECT ft.*, u.name AS author_name 
    FROM forum_threads ft 
    JOIN users u ON ft.author_id = u.id 
    ORDER BY ft.last_activity_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Fetch all posts and group them by thread_id
$posts_stmt = $pdo->query("
    SELECT fp.*, u.name AS post_author 
    FROM forum_posts fp 
    JOIN users u ON fp.author_id = u.id 
    ORDER BY fp.created_at ASC
");

$posts_by_thread = [];
foreach ($posts_stmt as $post) {
    $posts_by_thread[$post['thread_id']][] = $post;
}
?>

<h1>🧵 Forum Management</h1>

<?php if (empty($threads)): ?>
    <p>No threads found.</p>
<?php else: ?>
    <?php foreach ($threads as $thread): ?>
        <div style="border:1px solid #ccc; padding:10px; margin-bottom:20px;">
            <h3><?= htmlspecialchars($thread['title']) ?> 
                <?= $thread['is_pinned'] ? '📌' : '' ?>
            </h3>
            <p><strong>Category:</strong> <?= htmlspecialchars($thread['category']) ?></p>
            <p><strong>Author:</strong> <?= htmlspecialchars($thread['author_name']) ?> | 
               <strong>Created:</strong> <?= $thread['created_at'] ?> | 
               <strong>Last Activity:</strong> <?= $thread['last_activity_at'] ?> | 
               <strong>Views:</strong> <?= $thread['views'] ?></p>
            <a href="manage_forum.php?delete_thread=<?= $thread['id'] ?>" onclick="return confirm('Are you sure you want to delete this thread and all its posts?')">🗑️ Delete Thread</a>

            <div style="margin-top:15px; padding-left:20px; border-left:2px solid #ccc;">
                <h4>💬 Posts</h4>
                <?php if (!empty($posts_by_thread[$thread['id']])): ?>
                    <?php foreach ($posts_by_thread[$thread['id']] as $post): ?>
                        <div style="margin-bottom:10px;">
                            <p><strong><?= htmlspecialchars($post['post_author']) ?></strong> replied on <?= $post['created_at'] ?>:</p>
                            <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
                            <a href="manage_forum.php?delete_post=<?= $post['id'] ?>" onclick="return confirm('Delete this post?')">🗑️ Delete Post</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p><em>No posts yet in this thread.</em></p>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<p><a href="dashboard.php">🔙 Back to Dashboard</a></p>
