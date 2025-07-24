<!-- public/blog_post.php -->

<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once 'header.php';
require_once 'nav.php';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT p.*, u.name AS author_name FROM posts p JOIN users u ON p.author_id = u.id WHERE p.id = ? AND p.type = 'blog' AND p.status = 'approved'");
$stmt->execute([$id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    echo "<p>Blog not found.</p>";
} else {
?>
  <article>
    <h1><?php echo htmlspecialchars($post['title']); ?></h1>
    <p><em>By <?php echo htmlspecialchars($post['author_name']); ?> | <?php echo date('F j, Y', strtotime($post['created_at'])); ?></em></p>
    <div><?php echo nl2br(htmlspecialchars($post['body'])); ?></div>
  </article>
<?php
}
require_once 'footer.php';
?>
