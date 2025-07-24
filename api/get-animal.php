// api/get-animal.php
<?php
require_once '../includes/db.php';

header('Content-Type: application/json');

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM animals WHERE id = ? AND approved = 1");
$stmt->execute([$id]);
$animal = $stmt->fetch(PDO::FETCH_ASSOC);

if ($animal) {
    echo json_encode(['status' => 'success', 'animal' => $animal]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Animal not found']);
}