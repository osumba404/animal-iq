 // api/submit-contribution.php
<?php
require_once '../includes/db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$title = $_POST['title'] ?? '';
$content = $_POST['content'] ?? '';
$type = $_POST['type'] ?? 'blog';
$user_id = $_SESSION['user']['id'];

$sql = "INSERT INTO contributions (user_id, title, content, type, status, submitted_at) VALUES (?, ?, ?, ?, 'pending', NOW())";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id, $title, $content, $type]);

echo json_encode(['status' => 'success', 'message' => 'Contribution submitted']);