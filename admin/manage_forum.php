<!-- admin/manage_forum.php -->
<?php
$threads = $pdo->query("SELECT * FROM forum_threads ORDER BY created_at DESC")->fetchAll();
$posts = $pdo->query("SELECT * FROM forum_posts ORDER BY created_at DESC")->fetchAll();
?>