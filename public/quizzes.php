<?php
// quizzes.php - Quizzes & Games
require_once 'header.php';
require_once 'nav.php';
require_once '../includes/db.php';         // Ensure DB connection is loaded
require_once '../includes/functions.php';  // include getLatestQuizzes()


// function getLatestQuizzes(PDO $pdo, int $limit = 5): array {
//     $stmt = $pdo->prepare("SELECT * FROM quizzes WHERE is_published = 1 ORDER BY created_at DESC LIMIT :limit");
//     $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
//     $stmt->execute();
//     return $stmt->fetchAll(PDO::FETCH_ASSOC);
// }

$quizzes = getLatestQuizzes($pdo);


?>
<h1>Quizzes & Games</h1>

<section>
  <h2>🧠 Try a New Quiz</h2>
  <?php if (count($quizzes) > 0): ?>
    <ul>
      <?php foreach ($quizzes as $quiz): ?>
        <li>
          <a href="take_quiz.php?id=<?= $quiz['id'] ?>">
            <?= htmlspecialchars($quiz['title']) ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <p>No quizzes available yet. Check back soon!</p>
  <?php endif; ?>
</section>

<?php require_once 'footer.php'; ?>