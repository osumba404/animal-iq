<?php
// admin/load_taxonomy_table.php
require '../includes/db.php';

$level = $_GET['level'] ?? null;
$parent_id = $_GET['parent_id'] ?? null;

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
    echo "<tr><td colspan='3'>Invalid taxonomy level</td></tr>";
    exit;
}

$table = $tables[$level];
$parent_field = $parent_fields[$level] ?? null;

try {
    $query = "SELECT id, name FROM $table";
    $params = [];

    if ($parent_field && $parent_id) {
        $query .= " WHERE $parent_field = ?";
        $params[] = $parent_id;
    }

    $query .= " ORDER BY name ASC";
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$results) {
        echo "<tr><td colspan='3'>No records found</td></tr>";
    } else {
        foreach ($results as $row) {
            echo "<tr>
                <td>" . htmlspecialchars($row['name']) . "</td>
                <td>
                    <button class='edit-btn btn btn-sm btn-warning' data-id='{$row['id']}' data-name=\"" . htmlspecialchars($row['name']) . "\">Edit</button>
                    <button class='delete-btn btn btn-sm btn-danger' data-id='{$row['id']}'>Delete</button>
                </td>
            </tr>";
        }
    }

} catch (PDOException $e) {
    echo "<tr><td colspan='3'>Error: " . $e->getMessage() . "</td></tr>";
}
