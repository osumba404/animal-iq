<!-- admin/manage_posts.php -->

<?php
require_once '../includes/db.php';
require_once '../includes/header.php';


$stmt = $pdo->query("
  SELECT posts.*, users.name AS author 
  FROM posts 
  JOIN users ON posts.author_email = users.email 
  ORDER BY posts.created_at DESC
");

$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<br><a href="dashboard.php">← Back to Dashboard</a>
<h1>Manage Posts</h1>
<p><a href="add_post.php">+ Add New Post</a></p>

<table border="1" cellpadding="10">
  <tr>
    <th>Title</th>
    <th>Author</th>
    <th>Category</th>
    <th>Published On</th>
    <th>Status</th>
    <th>Actions</th>
  </tr>
  <?php foreach ($posts as $post): ?>
  <tr>
    <td><?= htmlspecialchars($post['title']) ?></td>
    <td><?= htmlspecialchars($post['author']) ?></td>
    <td><?= htmlspecialchars($post['category']) ?></td>
    <td><?= $post['created_at'] ?></td>
    <td><?= $post['status'] ?></td>
    <td>
      <a href="edit_post.php?id=<?= $post['id'] ?>">Edit</a> |
      <a href="delete_post.php?id=<?= $post['id'] ?>" onclick="return confirm('Delete this post?')">Delete</a>
    </td>
  </tr>
  <?php endforeach; ?>
</table>

<br><a href="dashboard.php">← Back to Dashboard</a>
<?php require_once '../includes/footer.php'; ?>
