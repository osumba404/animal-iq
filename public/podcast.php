<?php
// podcast.php - Podcasts Page
require_once '../includes/db.php';
require_once 'header.php';
require_once 'nav.php';

/* Commenting out the original content
// Fetch podcasts from DB
$sql = "SELECT p.*, u.name AS contributor_name
        FROM podcasts p
        LEFT JOIN users u ON p.contributor_id = u.id
        ORDER BY p.created_at DESC";
$stmt = $pdo->query($sql);
$podcasts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Podcasts</h1>

<style>
.podcast-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
  padding: 20px;
}

.podcast-card {
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  display: flex;
  flex-direction: column;
  transition: transform 0.2s ease;
}

.podcast-card:hover {
  transform: translateY(-5px);
}

.podcast-cover {
  width: 100%;
  height: 180px;
  object-fit: cover;
}

.podcast-content {
  padding: 15px;
  flex: 1;
  display: flex;
  flex-direction: column;
}

.podcast-title {
  font-size: 1.2em;
  font-weight: bold;
  margin-bottom: 8px;
}

.podcast-description {
  font-size: 0.9em;
  color: #555;
  margin-bottom: 10px;
  flex: 1;
}

.podcast-tags {
  margin-bottom: 10px;
}

.podcast-tags span {
  display: inline-block;
  background: #eee;
  color: #555;
  padding: 3px 8px;
  border-radius: 6px;
  font-size: 0.8em;
  margin-right: 5px;
}

.podcast-footer {
  font-size: 0.8em;
  color: #777;
  margin-top: auto;
}

audio {
  width: 100%;
  margin-top: 8px;
}
</style>

<div class="podcast-grid">
<?php foreach ($podcasts as $podcast): ?>
  <div class="podcast-card">
    <?php if (!empty($podcast['cover_image_url'])): ?>
      <img src="../uploads/podcasts/<?php echo htmlspecialchars($podcast['cover_image_url']); ?>" 
           alt="<?php echo htmlspecialchars($podcast['title']); ?>" 
           class="podcast-cover">
    <?php else: ?>
      <img src="../uploads/podcasts/default-cover.jpg" alt="Default Podcast Cover" class="podcast-cover">
    <?php endif; ?>

    <div class="podcast-content">
      <div class="podcast-title"><?php echo htmlspecialchars($podcast['title']); ?></div>
      <div class="podcast-description"><?php echo nl2br(htmlspecialchars($podcast['description'])); ?></div>
      
      <?php if (!empty($podcast['tags'])): ?>
        <div class="podcast-tags">
          <?php foreach (explode(',', $podcast['tags']) as $tag): ?>
            <span>#<?php echo htmlspecialchars(trim($tag)); ?></span>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <audio controls>
        <source src="../uploads/podcasts/<?php echo htmlspecialchars($podcast['file_url']); ?>" type="audio/mpeg">
        Your browser does not support the audio element.
      </audio>

      <div class="podcast-footer">
        Duration: <?php echo gmdate("H:i:s", $podcast['duration_seconds']); ?> |
        Uploaded by: <?php echo htmlspecialchars($podcast['contributor_name'] ?? 'Unknown'); ?>
      </div>
    </div>
  </div>
<?php endforeach; ?>
</div>
*/
?>

<style>
.coming-soon {
  min-height: 60vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 2rem;
}

.coming-soon h1 {
  font-size: 3rem;
  color: var(--color-primary);
  margin-bottom: 1rem;
  font-weight: 700;
}

.coming-soon p {
  font-size: 1.25rem;
  color: var(--color-text-muted);
  max-width: 600px;
  margin: 0 auto 2rem;
  line-height: 1.6;
}

.coming-soon .icon {
  font-size: 4rem;
  margin-bottom: 1.5rem;
  color: var(--color-accent-primary);
}

@media (max-width: 768px) {
  .coming-soon h1 {
    font-size: 2.5rem;
  }
  
  .coming-soon p {
    font-size: 1.1rem;
  }
}
</style>

<div class="coming-soon">
  <div class="icon">
    <i class="fas fa-podcast"></i>
  </div>
  <h1>Coming Soon</h1>
  <p>We're working hard to bring you amazing podcast content about wildlife, conservation, and animal intelligence. Stay tuned for updates!</p>
  <a href="index.php" class="btn btn-primary">Return Home</a>
</div>

<?php require_once 'footer.php'; ?>
