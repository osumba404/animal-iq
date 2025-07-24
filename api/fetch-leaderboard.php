// api/fetch-leaderboard.php
<?php
require_once '../includes/db.php';

header('Content-Type: application/json');

$stmt = $pdo->query("SELECT users.name, MAX(score) as high_score FROM quiz_scores JOIN users ON quiz_scores.user_id = users.id GROUP BY user_id ORDER BY high_score DESC LIMIT 10");
$leaderboard = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['status' => 'success', 'leaderboard' => $leaderboard]);