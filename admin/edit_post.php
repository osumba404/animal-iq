<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

if (!isset($_GET['id'])) {
    die('Post ID is required.');
}

$post_id = intval($_GET['id']);

// Fetch post
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$post) {
    die('Post not found.');
}

// Fetch admins
$admins = $pdo->query("SELECT id, full_name FROM admins")->fetchAll(PDO::FETCH_ASSOC);
?>



<h2>Edit Post</h2>

<form action="update_post.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">

    <label>Title:</label>
    <input type="text" name="title" required value="<?= htmlspecialchars($post['title']) ?>">

    <label>Content:</label>
    <textarea name="body" rows="10"><?= htmlspecialchars($post['body']) ?></textarea>

    <label>Post Type:</label>
    <input type="text" name="type" required value="<?= htmlspecialchars($post['type']) ?>">

    <label>Region:</label>
    <input type="text" name="region" required value="<?= htmlspecialchars($post['region']) ?>">

    <label>Status:</label>
    <select name="status" required>
        <option value="pending" <?= $post['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
        <option value="approved" <?= $post['status'] === 'approved' ? 'selected' : '' ?>>Approved</option>
        <option value="rejected" <?= $post['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
    </select>

    <label>Author:</label>
    <select name="author_id" required>
        <?php foreach ($admins as $admin): ?>
            <option value="<?= $admin['id'] ?>" <?= $admin['id'] == $post['author_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($admin['full_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Current Featured Image:</label>
    <?php if ($post['featured_image']): ?>
        <div><img src="../uploads/posts/<?= htmlspecialchars($post['featured_image']) ?>" width="120" alt="Current Image"></div>
    <?php else: ?>
        <div><em>No image uploaded</em></div>
    <?php endif; ?>

    <label>Change Featured Image:</label>
    <input type="file" name="featured_image">

    <button type="submit">Update Post</button>
</form>

