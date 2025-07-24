// api/upgrade-role.php
<?php
require_once '../includes/db.php';
session_start();

header('Content-Type: application/json');

if ($_SESSION['user']['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Forbidden']);
    exit;
}

$user_id = $_POST['user_id'] ?? 0;
$new_role = $_POST['role'] ?? 'user';

$stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
$stmt->execute([$new_role, $user_id]);

echo json_encode(['status' => 'success']);