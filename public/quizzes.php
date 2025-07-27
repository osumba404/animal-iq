<?php
// quizzes.php - Quizzes & Games
require_once 'header.php';
require_once 'nav.php';
require_once '../includes/db.php';         // Ensure DB connection is loaded
require_once '../includes/functions.php';  // include getLatestQuizzes()

$quizzes = getLatestQuizzes($pdo);

?>
<style>
/* Quizzes Page Specific Styles */
.quizzes-page {
  padding: 2rem 1rem;
  max-width: 1200px;
  margin: 0 auto;
}

.quizzes-page h1 {
  color: var(--color-primary);
  text-align: center;
  margin-bottom: 2rem;
  font-size: 2.5rem;
}

.quizzes-section {
  background-color: var(--color-bg-secondary);
  padding: 2rem;
  border-radius: 8px;
  margin-bottom: 2rem;
  box-shadow: 0 2px 8px var(--color-shadow);
}

.quizzes-section h2 {
  color: var(--color-primary);
  margin-bottom: 1.5rem;
  font-size: 1.8rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.quizzes-list {
  list-style: none;
  padding: 0;
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1rem;
}

.quizzes-list li {
  background-color: var(--color-bg-primary);
  padding: 1.5rem;
  border-radius: 6px;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  border: 1px solid var(--color-border-light);
}

.quizzes-list li:hover {
  transform: translateY(-3px);
  box-shadow: 0 4px 12px var(--color-shadow);
}

.quizzes-list a {
  color: var(--color-primary-light);
  text-decoration: none;
  font-weight: bold;
  font-size: 1.1rem;
  display: block;
  transition: color 0.2s ease;
}

.quizzes-list a:hover {
  color: var(--color-primary);
}

.no-quizzes {
  color: var(--color-text-muted);
  font-style: italic;
  padding: 1rem;
  text-align: center;
}
</style>

<div class="quizzes-page main-background">
  <h1>Quizzes & Games</h1>

  <section class="quizzes-section">
    <h2>🧠 Try a New Quiz</h2>
    <?php if (count($quizzes) > 0): ?>
      <ul class="quizzes-list">
        <?php foreach ($quizzes as $quiz): ?>
          <li class="card">
            <a href="take_quiz.php?id=<?= $quiz['id'] ?>">
              <?= htmlspecialchars($quiz['title']) ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <p class="no-quizzes">No quizzes available yet. Check back soon!</p>
    <?php endif; ?>
  </section>
</div>

<?php require_once 'footer.php'; ?>