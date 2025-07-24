
<!-- admin/update_animal.php -->
<?php
require_once '../includes/db.php';
require_once 'admin_header.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request");
}

$id = $_POST['id'];

// Sanitize inputs
$scientific_name = $_POST['scientific_name'];
$common_name = $_POST['common_name'];
$population_estimate = $_POST['population_estimate'] ?? null;
$species_status = $_POST['species_status'];
$avg_weight_kg = $_POST['avg_weight_kg'] ?? null;
$avg_length_cm = $_POST['avg_length_cm'] ?? null;
$appearance = $_POST['appearance'] ?? '';

$taxonomy = $_POST['taxonomy'];
$geo = $_POST['geography'];
$habits = $_POST['habits'];

// Handle image upload if any
$main_photo = '';
if (!empty($_FILES['main_photo']['name'])) {
    $ext = pathinfo($_FILES['main_photo']['name'], PATHINFO_EXTENSION);
    $main_photo = uniqid('animal_') . '.' . $ext;
    move_uploaded_file($_FILES['main_photo']['tmp_name'], "../assets/images/animals/" . $main_photo);
}

// Begin transaction
$pdo->beginTransaction();

try {
    // Update main animal info
    $sql = "UPDATE animals SET 
        scientific_name = ?, 
        common_name = ?, 
        population_estimate = ?, 
        species_status = ?, 
        avg_weight_kg = ?, 
        avg_length_cm = ?, 
        appearance = ?";

    $params = [$scientific_name, $common_name, $population_estimate, $species_status, $avg_weight_kg, $avg_length_cm, $appearance];

    if ($main_photo) {
        $sql .= ", main_photo = ?";
        $params[] = $main_photo;
    }

    $sql .= " WHERE id = ?";
    $params[] = $id;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    // Update taxonomy
    $stmt = $pdo->prepare("UPDATE taxonomy SET kingdom=?, phylum=?, class=?, `order`=?, family=?, genus=?, species=? WHERE animal_id=?");
    $stmt->execute([
        $taxonomy['kingdom'], $taxonomy['phylum'], $taxonomy['class'], $taxonomy['order'],
        $taxonomy['family'], $taxonomy['genus'], $taxonomy['species'], $id
    ]);

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
