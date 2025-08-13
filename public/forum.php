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
<!-- Your HTML/CSS below is unchanged -->


<style>
/* Premium Forum Styling */
.forum-container {
  max-width: 1200px;
  margin: 2rem auto;
  padding: 0 2rem;
}

.forum-header {
  text-align: center;
  margin-bottom: 3rem;
  position: relative;
}

.forum-header h1 {
  font-size: 2.8rem;
  color: var(--color-primary);
  margin-bottom: 1.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
}

.forum-header h1::after {
  content: '';
  position: absolute;
  bottom: -10px;
  left: 25%;
  width: 50%;
  height: 3px;
  background: linear-gradient(90deg, var(--color-primary), var(--color-accent-primary), var(--color-primary));
  border-radius: 3px;
}

.new-thread-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: var(--color-primary);
  color: white;
  padding: 0.8rem 1.5rem;
  border-radius: 50px;
  text-decoration: none;
  font-weight: bold;
  transition: all 0.3s ease;
  margin-bottom: 2rem;
  box-shadow: 0 4px 12px rgba(1, 50, 33, 0.2);
}

.new-thread-btn:hover {
  background: var(--color-primary-dark);
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(1, 50, 33, 0.3);
}

.forum-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.forum-topic {
  background: var(--color-bg-primary);
  border-radius: 12px;
  padding: 1.5rem;
  margin-bottom: 1.5rem;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px var(--color-shadow);
  border: 1px solid var(--color-border-light);
}

.forum-topic:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 24px rgba(30, 24, 17, 0.15);
}

.topic-content {
  display: flex;
  gap: 1.5rem;
}

.topic-avatar {
  flex: 0 0 60px;
}

.topic-avatar img {
  width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: 50%;
  border: 3px solid var(--color-primary-light);
}

.topic-details {
  flex: 1;
}

.topic-title {
  font-size: 1.3rem;
  margin: 0 0 0.5rem 0;
}

.topic-title a {
  color: var(--color-primary);
  text-decoration: none;
  transition: color 0.2s ease;
}

.topic-title a:hover {
  color: var(--color-primary-dark);
  text-decoration: underline;
}

.topic-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  color: var(--color-text-muted);
  font-size: 0.9rem;
  margin-bottom: 0.5rem;
}

.topic-category {
  display: inline-block;
  background: var(--color-primary-light);
  color: white;
  padding: 0.3rem 0.8rem;
  border-radius: 50px;
  font-size: 0.8rem;
}

.topic-stats {
  display: flex;
  gap: 1.5rem;
  margin-top: 0.8rem;
  font-size: 0.9rem;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 0.3rem;
  color: var(--color-text-muted);
}

.stat-item i {
  color: var(--color-primary);
}

.no-threads {
  text-align: center;
  padding: 3rem;
  background: var(--color-bg-secondary);
  border-radius: 12px;
  color: var(--color-text-muted);
  font-size: 1.1rem;
}

.no-threads a {
  color: var(--color-primary);
  font-weight: bold;
}

@media (max-width: 768px) {
  .forum-header h1 {
    font-size: 2rem;
  }
  
  .topic-content {
    flex-direction: column;
    gap: 1rem;
  }
  
  .topic-avatar {
    align-self: center;
  }
}
</style>

<div class="forum-container">
  <header class="forum-header">
    <h1>🐾 Animal IQ Community Forum</h1>
    <p>Connect with fellow animal lovers and share your knowledge</p>
  </header>

  <a href="new_thread.php" class="new-thread-btn">
    <span>➕</span> Start New Discussion
  </a>

  <?php if (count($topics) > 0): ?>
    <ul class="forum-list">
      <?php foreach ($topics as $t): ?>
        <li class="forum-topic">
          <div class="topic-content">
            <div class="topic-avatar">
              <img src="../uploads/profile_pics/<?php echo htmlspecialchars($t['profile_picture']); ?>" 
                   alt="Profile of <?php echo htmlspecialchars($t['name']); ?>">


            </div>
            
            <div class="topic-details">
              <h3 class="topic-title">
                <a href="topic.php?id=<?php echo $t['id']; ?>">
                  <?php echo htmlspecialchars($t['title']); ?>
                </a>
              </h3>
              
              <div class="topic-meta">
                <span>By <?php echo htmlspecialchars($t['name']); ?></span>
                <span>•</span>
                <span>Last active: <?php echo date('F j, Y, g:i A', strtotime($t['last_activity_at'])); ?></span>
                <span class="topic-category"><?php echo htmlspecialchars($t['category']); ?></span>
              </div>
              
              <div class="topic-stats">
                <span class="stat-item">
                  <i>👁️</i> <?php echo number_format($t['views']); ?> views
                </span>
              </div>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <div class="no-threads">
      <p>No discussions yet. Be the first to <a href="new_thread.php">start a conversation</a>!</p>
    </div>
  <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>