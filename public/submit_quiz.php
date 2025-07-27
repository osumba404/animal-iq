<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once 'header.php';
require_once 'nav.php';

if (!isset($_POST['quiz_id'], $_POST['answers']) || !is_array($_POST['answers'])) {
    echo "<p class='result-error'>Invalid submission.</p>";
    require_once 'footer.php';
    exit;
}

$quiz_id = (int)$_POST['quiz_id'];
$submitted_answers = $_POST['answers'];

// Fetch questions and correct answers
$stmt = $pdo->prepare("SELECT id, correct_option FROM quiz_questions WHERE quiz_id = ?");
$stmt->execute([$quiz_id]);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$score = 0;
$total = count($questions);

// Compare each answer
foreach ($questions as $question) {
    $qid = $question['id'];
    $correct = $question['correct_option'];

    if (isset($submitted_answers[$qid]) && $submitted_answers[$qid] === $correct) {
        $score++;
    }
}

// Optionally get user ID (if logged in)
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Save score if user is logged in
if ($user_id) {
    $stmt = $pdo->prepare("INSERT INTO quiz_scores (user_id, quiz_id, score) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $quiz_id, $score]);
}

// Calculate percentage and determine performance level
$percentage = round(($score / $total) * 100);
$performance_class = '';
$performance_emoji = '';

if ($percentage >= 90) {
    $performance_class = 'excellent';
    $performance_emoji = '🏆';
} elseif ($percentage >= 70) {
    $performance_class = 'good';
    $performance_emoji = '👍';
} elseif ($percentage >= 50) {
    $performance_class = 'average';
    $performance_emoji = '😊';
} else {
    $performance_class = 'poor';
    $performance_emoji = '📚';
}
?>

<style>
/* Results Page Premium Styling */
.results-container {
  max-width: 800px;
  margin: 2rem auto;
  padding: 3rem 2rem;
  background: var(--color-bg-primary);
  border-radius: 16px;
  box-shadow: 0 12px 40px rgba(30, 24, 17, 0.1);
  text-align: center;
  position: relative;
  overflow: hidden;
}

.results-container::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 8px;
  background: linear-gradient(90deg, var(--color-primary), var(--color-accent-primary), var(--color-primary));
}

.results-header {
  margin-bottom: 2rem;
}

.results-header h1 {
  color: var(--color-primary);
  font-size: 2.8rem;
  margin-bottom: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.score-display {
  font-size: 5rem;
  font-weight: bold;
  margin: 1rem 0;
  position: relative;
  display: inline-block;
}

.score-display::after {
  content: '';
  position: absolute;
  bottom: -10px;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, var(--color-primary), var(--color-accent-primary), var(--color-primary));
  border-radius: 2px;
}

.score-details {
  font-size: 1.5rem;
  margin-bottom: 2rem;
  color: var(--color-text-primary);
}

.performance-message {
  font-size: 1.8rem;
  margin: 2rem 0;
  padding: 1rem;
  border-radius: 8px;
  display: inline-block;
}

.excellent {
  background-color: rgba(26, 138, 106, 0.1);
  color: var(--color-success);
  border: 2px solid var(--color-success);
}

.good {
  background-color: rgba(232, 184, 36, 0.1);
  color: var(--color-accent-primary);
  border: 2px solid var(--color-accent-primary);
}

.average {
  background-color: rgba(58, 125, 157, 0.1);
  color: var(--color-info);
  border: 2px solid var(--color-info);
}

.poor {
  background-color: rgba(194, 59, 34, 0.1);
  color: var(--color-error);
  border: 2px solid var(--color-error);
}

.login-prompt {
  margin: 2rem 0;
  padding: 1.5rem;
  background: var(--color-bg-secondary);
  border-radius: 8px;
  font-size: 1.1rem;
}

.action-links {
  margin-top: 3rem;
  display: flex;
  justify-content: center;
  gap: 1.5rem;
  flex-wrap: wrap;
}

.action-link {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.8rem 1.5rem;
  border-radius: 50px;
  text-decoration: none;
  font-weight: bold;
  transition: all 0.3s ease;
}

.primary-action {
  background: var(--color-primary);
  color: var(--color-text-inverted);
  box-shadow: 0 4px 12px rgba(1, 50, 33, 0.2);
}

.primary-action:hover {
  background: var(--color-primary-dark);
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(1, 50, 33, 0.3);
}

.secondary-action {
  background: var(--color-bg-secondary);
  color: var(--color-primary);
  border: 2px solid var(--color-primary);
}

.secondary-action:hover {
  background: var(--color-primary-light);
  color: var(--color-text-inverted);
}

.result-error {
  text-align: center;
  padding: 2rem;
  color: var(--color-error);
  font-size: 1.2rem;
  background: rgba(194, 59, 34, 0.1);
  border-radius: 8px;
  max-width: 800px;
  margin: 2rem auto;
}

@media (max-width: 768px) {
  .results-container {
    padding: 2rem 1.5rem;
    margin: 1rem;
  }
  
  .results-header h1 {
    font-size: 2.2rem;
  }
  
  .score-display {
    font-size: 4rem;
  }
  
  .action-links {
    flex-direction: column;
    gap: 1rem;
    align-items: center;
  }
  
  .action-link {
    width: 100%;
    max-width: 300px;
    justify-content: center;
  }
}
</style>

<div class="results-container">
  <div class="results-header">
    <h1>🎉 Quiz Completed!</h1>
  </div>
  
  <div class="score-display <?= $performance_class ?>">
    <?= $score ?>/<?= $total ?>
  </div>
  
  <div class="score-details">
    That's <?= $percentage ?>% correct!
  </div>
  
  <div class="performance-message <?= $performance_class ?>">
    <?= $performance_emoji ?> <?php
    if ($percentage >= 90) echo "Outstanding Performance!";
    elseif ($percentage >= 70) echo "Great Job!";
    elseif ($percentage >= 50) echo "Good Effort!";
    else echo "Keep Practicing!";
    ?>
  </div>
  
  <?php if (!$user_id): ?>
    <div class="login-prompt">
      <p><em>Log in to save your scores and track your progress over time.</em></p>
    </div>
  <?php endif; ?>
  
  <div class="action-links">
    <a href="quizzes.php" class="action-link primary-action">
      🏆 Try Another Quiz
    </a>
    <?php if (!$user_id): ?>
      <a href="login.php" class="action-link secondary-action">
        🔐 Login to Save Results
      </a>
    <?php else: ?>
      <a href="dashboard.php" class="action-link secondary-action">
        📊 View Your Dashboard
      </a>
    <?php endif; ?>
  </div>
</div>

<?php require_once 'footer.php'; ?>