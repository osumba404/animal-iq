<?php
require_once '../includes/db.php';
// require_once 'admin_header.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request");
}

$id = $_POST['id'] ?? die("Missing animal ID");

// Sanitize fields
$scientific_name = $_POST['scientific_name'] ?? '';
$common_name = $_POST['common_name'] ?? '';
$population_estimate = $_POST['population_estimate'] ?? null;
$species_status_id = $_POST['species_status_id'] ?? null;
$avg_weight_kg = $_POST['avg_weight_kg'] ?? null;
$avg_length_cm = $_POST['avg_length_cm'] ?? null;
$appearance = $_POST['appearance'] ?? '';

$species_id = $_POST['species_id'] ?? null;
$geo = $_POST['geography'] ?? [];
$habits = $_POST['habits'] ?? [];

$main_photo = '';
if (!empty($_FILES['main_photo']['name'])) {
    $ext = pathinfo($_FILES['main_photo']['name'], PATHINFO_EXTENSION);
    $main_photo = uniqid('animal_') . '.' . $ext;
    move_uploaded_file($_FILES['main_photo']['tmp_name'], "../uploads/animals/" . $main_photo);
}

$pdo->beginTransaction();

try {
    // Dynamically build SET clause
    $fields = [
        'scientific_name = ?',
        'common_name = ?',
        'population_estimate = ?',
        'species_status_id = ?',
        'avg_weight_kg = ?',
        'avg_length_cm = ?',
        'appearance = ?'
    ];
    $params = [
        $scientific_name, $common_name, $population_estimate, $species_status_id,
        $avg_weight_kg, $avg_length_cm, $appearance
    ];

    if ($main_photo) {
        $fields[] = 'main_photo = ?';
        $params[] = $main_photo;
    }

    $sql = "UPDATE animals SET " . implode(', ', $fields) . " WHERE id = ?";
    $params[] = $id;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    if (empty($species_id) || !is_numeric($species_id)) {
    throw new Exception("Invalid species selected.");
    }

    $check = $pdo->prepare("SELECT COUNT(*) FROM species WHERE id = ?");
    $check->execute([$species_id]);
    if ($check->fetchColumn() == 0) {
        throw new Exception("Selected species ID does not exist.");
    }


    // Update taxonomy
    $stmt = $pdo->prepare("UPDATE taxonomy SET species_id = ? WHERE animal_id = ?");
    $stmt->execute([$species_id, $id]);

    // Update geography
    $stmt = $pdo->prepare("UPDATE animal_geography SET continent=?, subcontinent=?, country=?, realm=?, biome=? WHERE animal_id=?");
    $stmt->execute([
        $geo['continent'], $geo['subcontinent'], $geo['country'], $geo['realm'], $geo['biome'], $id
    ]);

    // Update habits
    $stmt = $pdo->prepare("UPDATE animal_habits SET diet=?, mating_habits=?, behavior=?, habitat=? WHERE animal_id=?");
    $stmt->execute([
        $habits['diet'], $habits['mating_habits'], $habits['behavior'], $habits['habitat'], $id
    ]);

    $pdo->commit();
    header("Location: manage_animals.php?updated=1");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    echo "Error: " . $e->getMessage();
}
?>
