<?php
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $status = $_POST['status'] ?? null;

    if ($id && in_array($status, ['pending', 'approved', 'rejected'])) {
        $stmt = $pdo->prepare("UPDATE animals SET status = ? WHERE id = ?");
        if ($stmt->execute([$status, $id])) {
            echo "Status updated successfully.";
        } else {
            echo "Failed to update status.";
        }
    } else {
        echo "Invalid data.";
    }
} else {
    echo "Invalid request.";
}
