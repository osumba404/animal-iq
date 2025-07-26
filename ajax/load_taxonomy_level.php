<?php
require_once '../includes/db.php';

header('Content-Type: application/json');

$type = $_GET['type'] ?? '';
$parent_id = (int) ($_GET['parent_id'] ?? 0);

switch ($type) {
    case 'phyla':
        $stmt = $pdo->prepare("SELECT id, name FROM phyla WHERE kingdom_id = ?");
        $stmt->execute([$parent_id]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'classes':
        $stmt = $pdo->prepare("SELECT id, name FROM classes WHERE phylum_id = ?");
        $stmt->execute([$parent_id]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'orders':
        $stmt = $pdo->prepare("SELECT id, name FROM orders WHERE class_id = ?");
        $stmt->execute([$parent_id]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'families':
        $stmt = $pdo->prepare("SELECT id, name FROM families WHERE order_id = ?");
        $stmt->execute([$parent_id]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'genera':
        $stmt = $pdo->prepare("SELECT id, name FROM genera WHERE family_id = ?");
        $stmt->execute([$parent_id]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'species':
        $stmt = $pdo->prepare("SELECT id, name FROM species WHERE genus_id = ?");
        $stmt->execute([$parent_id]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    default:
        echo json_encode([]);
        break;
}
