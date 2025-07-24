<!-- admin/blog-editor.php -->
 <?php
 $posts = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC")->fetchAll();
?>