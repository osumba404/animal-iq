// api/submit-comment.php
<?php
require_once '../includes/db.php';
session_start();

header('Content-Type: application/json');

$user_id = $_SESSION['user']['id'] ?? null;
$content_id = $_POST['content_id'] ?? 0;
$comment = $_POST['comment'] ?? '';
$type = $_POST['type'] ?? 'blog';

if ($user_id && $comment) {
    $stmt = $pdo->prepare("INSERT INTO comments (user_id, content_id, comment, type, posted_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$user_id, $content_id, $comment, $type]);
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Missing data']);
}