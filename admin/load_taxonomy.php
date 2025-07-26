<?php
// admin/load_taxonomy.php

require '../includes/db.php';

$level = $_POST['level'] ?? null;
$parent_id = $_POST['parent_id'] ?? null;

$map = [
    'phylum' => ['table' => 'phyla', 'column' => 'kingdom_id'],
    'class' => ['table' => 'classes', 'column' => 'phylum_id'],
    'order' => ['table' => 'orders', 'column' => 'class_id'],
    'family' => ['table' => 'families', 'column' => 'order_id'],
    'genus' => ['table' => 'genera', 'column' => 'family_id'],
    'species' => ['table' => 'species', 'column' => 'genus_id']
];

if ($level && isset($map[$level]) && $parent_id) {
    $table = $map[$level]['table'];
    $column = $map[$level]['column'];

    $stmt = $pdo->prepare("SELECT id, name FROM $table WHERE $column = ? ORDER BY name");
    $stmt->execute([$parent_id]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($results);
} else {
    echo json_encode([]);
}
?>


