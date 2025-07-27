<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once 'header.php';
require_once 'nav.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p class='quiz-error'>Invalid quiz selected.</p>";
    require_once 'footer.php';
    exit;
}

$quiz_id = (int)$_GET['id'];

// Fetch quiz details
$stmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ? AND is_published = 1");
$stmt->execute([$quiz_id]);
$quiz = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$quiz) {
    echo "<p class='quiz-error'>Quiz not found or not available.</p>";
    require_once 'footer.php';
    exit;
}

// Fetch questions
$stmt = $pdo->prepare("SELECT * FROM quiz_questions WHERE quiz_id = ?");
$stmt->execute([$quiz_id]);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
/* Quiz Page Premium Styling */
.quiz-container {
  max-width: 800px;
  margin: 2rem auto;
  padding: 2rem;
  background: var(--color-bg-primary);
  border-radius: 12px;
  box-shadow: 0 8px 32px rgba(30, 24, 17, 0.1);
}

.quiz-header {
  text-align: center;
  margin-bottom: 3rem;
  position: relative;
  padding-bottom: 1.5rem;
}

.quiz-header h1 {
  color: var(--color-primary);
  font-size: 2.5rem;
  margin-bottom: 0.5rem;
}

.quiz-header::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 25%;
  width: 50%;
  height: 3px;
  background: linear-gradient(90deg, var(--color-primary), var(--color-accent-primary), var(--color-primary));
  border-radius: 3px;
}

.quiz-form {
  margin-top: 2rem;
}

.question-list {
  list-style: none;
  padding: 0;
  counter-reset: question-counter;
}

.question-list li {
  counter-increment: question-counter;
  margin-bottom: 2.5rem;
  padding: 1.5rem;
  background: var(--color-bg-secondary);
  border-radius: 8px;
  box-shadow: 0 2px 8px var(--color-shadow);
  transition: transform 0.3s ease;
  position: relative;
  overflow: hidden;
}

.question-list li:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 16px rgba(30, 24, 17, 0.15);
}

.question-list li::before {
  content: counter(question-counter);
  position: absolute;
  top: 0;
  left: 0;
  background: var(--color-primary);
  color: var(--color-text-inverted);
  width: 2rem;
  height: 2rem;
  display: flex;
  align-items: center;
  justify-content: center;
  border-bottom-right-radius: 8px;
  font-weight: bold;
}

.question-text {
  font-size: 1.2rem;
  font-weight: bold;
  color: var(--color-text-primary);
  margin-bottom: 1.5rem;
  padding-left: 1rem;
}

.options-container {
  display: grid;
  grid-template-columns: 1fr;
  gap: 0.8rem;
  padding-left: 1rem;
}

.option-label {
  display: flex;
  align-items: center;
  padding: 0.8rem 1rem;
  background: var(--color-bg-primary);
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s ease;
  border: 1px solid var(--color-border-light);
}

.option-label:hover {
  background: var(--color-neutral-lighter);
  border-color: var(--color-primary-lighter);
}

.option-label input[type="radio"] {
  margin-right: 1rem;
  accent-color: var(--color-primary);
  transform: scale(1.2);
}

.submit-btn {
  display: block;
  width: 100%;
  max-width: 300px;
  margin: 3rem auto 2rem;
  padding: 1rem;
  background: var(--color-primary);
  color: var(--color-text-inverted);
  border: none;
  border-radius: 50px;
  font-size: 1.1rem;
  font-weight: bold;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(1, 50, 33, 0.2);
}

.submit-btn:hover {
  background: var(--color-primary-dark);
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(1, 50, 33, 0.3);
}

.back-link {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--color-primary-light);
  text-decoration: none;
  font-weight: bold;
  transition: color 0.2s ease;
}

.back-link:hover {
  color: var(--color-primary);
}

.quiz-error {
  text-align: center;
  padding: 2rem;
  color: var(--color-error);
  font-size: 1.2rem;
}

.no-questions {
  text-align: center;
  padding: 2rem;
  color: var(--color-text-muted);
  font-style: italic;
}

@media (max-width: 768px) {
  .quiz-container {
    padding: 1.5rem;
    margin: 1rem;
  }
  
  .quiz-header h1 {
    font-size: 2rem;
  }
  
  .question-text {
    font-size: 1.1rem;
  }
}
</style>

<div class="quiz-container">
  <div class="quiz-header">
    <h1><?= htmlspecialchars($quiz['title']) ?> Quiz</h1>
  </div>

  <?php if (count($questions) > 0): ?>
  <form action="submit_quiz.php" method="post" class="quiz-form">
    <input type="hidden" name="quiz_id" value="<?= $quiz_id ?>">
    <ol class="question-list">
      <?php foreach ($questions as $index => $q): ?>
        <li>
          <p class="question-text"><?= htmlspecialchars($q['question']) ?></p>
          <div class="options-container">
            <label class="option-label">
              <input type="radio" name="answers[<?= $q['id'] ?>]" value="A" required>
              <?= htmlspecialchars($q['option_a']) ?>
            </label>
            <label class="option-label">
              <input type="radio" name="answers[<?= $q['id'] ?>]" value="B">
              <?= htmlspecialchars($q['option_b']) ?>
            </label>
            <label class="option-label">
              <input type="radio" name="answers[<?= $q['id'] ?>]" value="C">
              <?= htmlspecialchars($q['option_c']) ?>
            </label>
            <label class="option-label">
              <input type="radio" name="answers[<?= $q['id'] ?>]" value="D">
              <?= htmlspecialchars($q['option_d']) ?>
            </label>
          </div>
        </li>
      <?php endforeach; ?>
    </ol>
    <button type="submit" class="submit-btn">Submit Quiz ✨</button>
  </form>
  <?php else: ?>
    <p class="no-questions">No questions available for this quiz yet.</p>
  <?php endif; ?>

  <p><a href="quizzes.php" class="back-link">🔙 Back to Quizzes</a></p>
</div>

<?php require_once 'footer.php'; ?>