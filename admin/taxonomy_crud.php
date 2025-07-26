<?php
// admin/taxonomy_crud.php
session_start();
require '../includes/db.php';

header('Content-Type: application/json');

$level = $_POST['level'] ?? null;
$action = $_POST['action'] ?? null;
$id = $_POST['id'] ?? null;
$name = trim($_POST['name'] ?? '');
$parent_id = $_POST['parent_id'] ?? null;
$created_by = $_SESSION['admin_id'] ?? 1;
$timestamp = date('Y-m-d H:i:s');

$tables = [
    'kingdom' => 'kingdoms',
    'phylum' => 'phyla',
    'class' => 'classes',
    'order' => 'orders',
    'family' => 'families',
    'genus' => 'genera',
    'species' => 'species'
];

$parent_fields = [
    'phylum' => 'kingdom_id',
    'class' => 'phylum_id',
    'order' => 'class_id',
    'family' => 'order_id',
    'genus' => 'family_id',
    'species' => 'genus_id'
];

if (!isset($tables[$level])) {
    echo json_encode(['success' => false, 'message' => 'Invalid taxonomy level']);
    exit;
}

$table = $tables[$level];

try {
    if ($action === 'create') {
        $query = "INSERT INTO $table (name, created_by, updated_at";
        $values = "VALUES (?, ?, ?";

        $params = [$name, $created_by, $timestamp];

        if (isset($parent_fields[$level])) {
            $query .= ", {$parent_fields[$level]}";
            $values .= ", ?";
            $params[] = $parent_id;
        }

        $query .= ") $values)";
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        echo json_encode(['success' => true, 'message' => 'Created successfully']);
    }

    elseif ($action === 'update') {
        $query = "UPDATE $table SET name = ?, updated_at = ?";
        $params = [$name, $timestamp];

        if (isset($parent_fields[$level])) {
            $query .= ", {$parent_fields[$level]} = ?";
            $params[] = $parent_id;
        }

        $query .= " WHERE id = ?";
        $params[] = $id;

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        echo json_encode(['success' => true, 'message' => 'Updated successfully']);
    }

    elseif ($action === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM $table WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'Deleted successfully']);
    }

    else {
        echo json_encode(['success' => false, 'message' => 'Unknown action']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
