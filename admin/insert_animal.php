<?php
// admin/insert_animal.php

require_once '../includes/db.php';          // PDO connection
require_once '../includes/functions.php';   // Custom helper functions (e.g., sanitize)

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Required fields (you could later use a validate() helper)
        $scientific_name = $_POST['scientific_name'];
        $common_name = $_POST['common_name'];
        $population_estimate = $_POST['population_estimate'];
        $species_status_id = $_POST['species_status_id'];
        $avg_weight_kg = $_POST['avg_weight_kg'];
        $avg_length_cm = $_POST['avg_length_cm'];
        $appearance = $_POST['appearance'];
        $species_id = $_POST['species_id'];
        $is_animal_of_the_day = isset($_POST['is_animal_of_the_day']) ? 1 : 0;
        $submitted_by = $_SESSION['admin_id'] ?? null;
        $approved_by = null;

        if (!$submitted_by) {
            throw new Exception("You must be logged in as admin to perform this action.");
        }

        // 📷 Handle main photo
        $main_photo = null;
        if (!empty($_FILES['main_photo']['name']) && $_FILES['main_photo']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['main_photo']['name'], PATHINFO_EXTENSION);
            // $filename = uniqid() . '.' . $ext;
            $main_photo = uniqid('animal_') . '.' . $ext;
            move_uploaded_file($_FILES['main_photo']['tmp_name'], '../uploads/animals/' . $main_photo);
            //$main_photo = 'uploads/animals/' . $filename;
        }

        $pdo->beginTransaction();

        // 🐾 Insert into animals
        $stmt = $pdo->prepare("INSERT INTO animals 
            (scientific_name, common_name, population_estimate, species_status_id, avg_weight_kg, avg_length_cm, appearance, main_photo, submitted_by, approved_by, is_animal_of_the_day) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $scientific_name,
            $common_name,
            $population_estimate,
            $species_status_id,
            $avg_weight_kg,
            $avg_length_cm,
            $appearance,
            $main_photo,
            $submitted_by,
            $approved_by,
            $is_animal_of_the_day
        ]);

        $animal_id = $pdo->lastInsertId();

        // 🧬 Link to taxonomy
        $stmt = $pdo->prepare("INSERT INTO taxonomy (animal_id, species_id) VALUES (?, ?)");
        $stmt->execute([$animal_id, $species_id]);

        // 🌍 Insert into animal_geography
        $stmt = $pdo->prepare("INSERT INTO animal_geography (animal_id, continent, subcontinent, country, realm, biome) 
                               VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $animal_id,
            $_POST['continent'],
            $_POST['subcontinent'],
            $_POST['country'],
            $_POST['realm'],
            $_POST['biome']
        ]);

        // 🧠 Insert into animal_habits
        $stmt = $pdo->prepare("INSERT INTO animal_habits (animal_id, diet, mating_habits, behavior, habitat) 
                               VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $animal_id,
            $_POST['diet'],
            $_POST['mating_habits'],
            $_POST['behavior'],
            $_POST['habitat']
        ]);

        // 📷 Handle additional photos
        if (!empty($_FILES['additional_photos']['name'][0])) {
            foreach ($_FILES['additional_photos']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['additional_photos']['error'][$key] === UPLOAD_ERR_OK) {
                    $ext = pathinfo($_FILES['additional_photos']['name'][$key], PATHINFO_EXTENSION);
                    $filename = uniqid() . '.' . $ext;
                    move_uploaded_file($tmp_name, '../uploads/animals/' . $filename);

                    $photo_url = 'uploads/animals/' . $filename;
                    $caption = $_POST['photo_captions'][$key] ?? '';

                    $stmt = $pdo->prepare("INSERT INTO animal_photos (animal_id, photo_url, caption) VALUES (?, ?, ?)");
                    $stmt->execute([$animal_id, $photo_url, $caption]);
                }
            }
        }

        $pdo->commit();
        header("Location: manage_animals.php?success=1");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "<h3>Error inserting animal: " . htmlspecialchars($e->getMessage()) . "</h3>";
    }
} else {
    echo "<h3>Invalid request method.</h3>";
}
?>
