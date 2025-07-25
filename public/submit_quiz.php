<!-- public/submit_quiz.php -->

<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once 'header.php';
require_once 'nav.php';

if (!isset($_POST['quiz_id'], $_POST['answers']) || !is_array($_POST['answers'])) {
    echo "<p>Invalid submission.</p>";
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

?>
<h1>🎉 Quiz Completed!</h1>
<p>You scored <strong><?= $score ?></strong> out of <strong><?= $total ?></strong>.</p>

<?php if (!$user_id): ?>
    <p><em>Log in to save your scores and track your progress.</em></p>
<?php endif; ?>

<p><a href="quizzes.php">🔙 Try another quiz</a></p>

<?php require_once 'footer.php'; ?>
