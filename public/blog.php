<?php
// public/blog.php
require_once '../includes/db.php';
require_once '../includes/functions.php'; // <-- make sure this is added
require_once 'header.php';
require_once 'nav.php';

function getApprovedBlogs($pdo) {
    $stmt = $pdo->prepare("
        SELECT p.id, p.title, LEFT(p.body, 300) AS summary, p.created_at, u.name AS author_name
        FROM posts p
        JOIN users u ON p.author_id = u.id
       -- WHERE p.type = 'blog' AND p.status = 'approved'
        ORDER BY p.created_at DESC
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


$blogs = getApprovedBlogs($pdo);
?>

<h1>Blogs & Stories</h1>

<?php foreach($blogs as $post): ?>
  <article style="border-bottom:1px solid #ccc; padding:1em 0;">
    <h2><?php echo htmlspecialchars($post['title']); ?></h2>
    <p><em>By <?php echo htmlspecialchars($post['author_name']); ?> on <?php echo date('F j, Y', strtotime($post['created_at'])); ?></em></p>
    <p><?php echo nl2br(htmlspecialchars($post['summary'])) . '...'; ?></p>
    <a href="blog_post.php?id=<?php echo $post['id']; ?>">Read more</a>
  </article>
<?php endforeach; ?>

<?php require_once 'footer.php'; ?>
