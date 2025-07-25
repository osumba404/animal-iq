<!-- public/take_quiz.php -->
 
 <?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once 'header.php';
require_once 'nav.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p>Invalid quiz selected.</p>";
    require_once 'footer.php';
    exit;
}

$quiz_id = (int)$_GET['id'];

// Fetch quiz details
$stmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ? AND is_published = 1");
$stmt->execute([$quiz_id]);
$quiz = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$quiz) {
    echo "<p>Quiz not found or not available.</p>";
    require_once 'footer.php';
    exit;
}

// Fetch questions
$stmt = $pdo->prepare("SELECT * FROM quiz_questions WHERE quiz_id = ?");
$stmt->execute([$quiz_id]);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1><?= htmlspecialchars($quiz['title']) ?> Quiz</h1>

<?php if (count($questions) > 0): ?>
<form action="submit_quiz.php" method="post">
    <input type="hidden" name="quiz_id" value="<?= $quiz_id ?>">
    <ol>
        <?php foreach ($questions as $index => $q): ?>
            <li>
                <p><?= htmlspecialchars($q['question']) ?></p>
                <label><input type="radio" name="answers[<?= $q['id'] ?>]" value="A" required> <?= htmlspecialchars($q['option_a']) ?></label><br>
                <label><input type="radio" name="answers[<?= $q['id'] ?>]" value="B"> <?= htmlspecialchars($q['option_b']) ?></label><br>
                <label><input type="radio" name="answers[<?= $q['id'] ?>]" value="C"> <?= htmlspecialchars($q['option_c']) ?></label><br>
                <label><input type="radio" name="answers[<?= $q['id'] ?>]" value="D"> <?= htmlspecialchars($q['option_d']) ?></label>
            </li>
        <?php endforeach; ?>
    </ol>
    <button type="submit">Submit Quiz</button>
</form>
<?php else: ?>
    <p>No questions available for this quiz yet.</p>
<?php endif; ?>

<p><a href="quizzes.php">🔙 Back to Quizzes</a></p>

<?php require_once 'footer.php'; ?>
