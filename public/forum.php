<?php
// forum.php - Community Forum
require_once '../includes/db.php';
require_once '../includes/header.php';
require_once 'nav.php';
$topics = getForumTopics($conn);
?>
<h1>Community Forum</h1>
<?php foreach($topics as $t): ?>
  <h2><a href="topic.php?id=<?php echo $t['id']; ?>"><?php echo htmlspecialchars($t['title']); ?></a></h2>
<?php endforeach; ?>
<?php require_once '../includes/footer.php'; ?>