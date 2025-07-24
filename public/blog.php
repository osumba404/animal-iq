<?php
// blog.php - Blog & Stories
require_once '../includes/db.php';
require_once '../includes/header.php';
require_once 'nav.php';
$blogs = getApprovedBlogs($conn);
?>
<h1>Blog & Stories</h1>
<?php foreach($blogs as $post): ?>
  <article>
    <h2><?php echo htmlspecialchars($post['title']); ?></h2>
    <p><?php echo nl2br(htmlspecialchars($post['summary'])); ?></p>
  </article>
<?php endforeach; ?>
<?php require_once '../includes/footer.php'; ?>