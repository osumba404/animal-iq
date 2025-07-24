// api/quiz-score.php
<?php
require_once '../includes/db.php';
session_start();

header('Content-Type: application/json');

$user_id = $_SESSION['user']['id'] ?? null;
$quiz_id = $_POST['quiz_id'] ?? 0;
$score = $_POST['score'] ?? 0;

if ($user_id) {
    $stmt = $pdo->prepare("INSERT INTO quiz_scores (user_id, quiz_id, score, attempted_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$user_id, $quiz_id, $score]);
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Login required']);
}