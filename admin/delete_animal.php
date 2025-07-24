<!-- admin/delete_animal.php -->

<?php
require_once '../includes/db.php';

if (!isset($_GET['id'])) {
    die("No animal ID provided.");
}

$animal_id = $_GET['id'];

// Begin transaction
$pdo->beginTransaction();

try {
    // Optionally delete the image file
    $stmt = $pdo->prepare("SELECT main_photo FROM animals WHERE id = ?");
    $stmt->execute([$animal_id]);
    $animal = $stmt->fetch();
    if ($animal && $animal['main_photo']) {
        $image_path = "../assets/images/animals/" . $animal['main_photo'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }

    // Delete related records
    $pdo->prepare("DELETE FROM taxonomy WHERE animal_id = ?")->execute([$animal_id]);
    $pdo->prepare("DELETE FROM animal_geography WHERE animal_id = ?")->execute([$animal_id]);
    $pdo->prepare("DELETE FROM animal_habits WHERE animal_id = ?")->execute([$animal_id]);

    // Finally delete the animal
    $pdo->prepare("DELETE FROM animals WHERE id = ?")->execute([$animal_id]);

    $pdo->commit();
    header("Location: manage_animals.php?deleted=1");
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Error deleting animal: " . $e->getMessage();
}
?>
