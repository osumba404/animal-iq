<?php
// public/forum.php - Community Forum
require_once '../includes/db.php';
require_once 'header.php';
require_once 'nav.php';

// Fetch all forum threads with author info
$stmt = $pdo->prepare("
    SELECT ft.id, ft.title, ft.category, ft.views, ft.last_activity_at, u.name, u.profile_picture
    FROM forum_threads ft
    JOIN users u ON ft.author_id = u.id
    ORDER BY ft.last_activity_at DESC
");
$stmt->execute();
$topics = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="container">
  <h1>🐾 Animal IQ Community Forum</h1>

  <p><a href="new_thread.php" class="btn btn-primary">➕ Start New Discussion</a></p>

  <?php if (count($topics) > 0): ?>
    <ul class="forum-list">
      <?php foreach ($topics as $t): ?>
        <li class="forum-topic" style="margin-bottom: 1.5em;">
          <div style="display: flex; align-items: center;">
            <img src="assets/images/profiles/<?php echo htmlspecialchars($t['profile_picture']); ?>" 
                 alt="Profile of <?php echo htmlspecialchars($t['name']); ?>" 
                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%; margin-right: 10px;">
            <div>
              <h3 style="margin: 0;">
                <a href="topic.php?id=<?php echo $t['id']; ?>">
                  <?php echo htmlspecialchars($t['title']); ?>
                </a>
              </h3>
              <small>By <?php echo htmlspecialchars($t['name']); ?> | Last active: <?php echo date('F j, Y, g:i A', strtotime($t['last_activity_at'])); ?></small>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <p>No threads yet. Be the first to <a href="new_thread.php">start a discussion</a>!</p>
  <?php endif; ?>
</main>

<?php require_once 'footer.php'; ?>
