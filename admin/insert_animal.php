<!-- admin/insert_animal.php -->

<?php
require_once '../includes/db.php'; // PDO connection
require_once '../includes/functions.php'; // Custom helper functions (e.g., sanitize)

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Basic animal data
        $scientific_name = $_POST['scientific_name'];
        $common_name = $_POST['common_name'];
        $population_estimate = $_POST['population_estimate'];
        $species_status_id = $_POST['species_status_id'];
        $avg_weight_kg = $_POST['avg_weight_kg'];
        $avg_length_cm = $_POST['avg_length_cm'];
        $appearance = $_POST['appearance'];
        $is_animal_of_the_day = isset($_POST['is_animal_of_the_day']) ? 1 : 0;
        $submitted_by = $_SESSION['admin_id']; // Assuming admin is logged in
        $approved_by = null; // Optional for now

        // Main photo (assume uploaded)
        $main_photo = null;
        if (isset($_FILES['main_photo']) && $_FILES['main_photo']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['main_photo']['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['main_photo']['tmp_name'], '../uploads/animals/' . $filename);
            $main_photo = 'uploads/animals/' . $filename;
        }

        $pdo->beginTransaction();

        // Insert into animals
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

        // 🧬 Taxonomy mapping
        $species_id = $_POST['species_id'];
        $stmt = $pdo->prepare("INSERT INTO taxonomy (animal_id, species_id) VALUES (?, ?)");
        $stmt->execute([$animal_id, $species_id]);

        // 🌍 Animal Geography
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

        // 🧠 Animal Habits
        $stmt = $pdo->prepare("INSERT INTO animal_habits (animal_id, diet, mating_habits, behavior, habitat) 
                               VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $animal_id,
            $_POST['diet'],
            $_POST['mating_habits'],
            $_POST['behavior'],
            $_POST['habitat']
        ]);

        // 📷 Additional photos (optional)
        if (!empty($_FILES['additional_photos']['name'][0])) {
            foreach ($_FILES['additional_photos']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['additional_photos']['error'][$key] === UPLOAD_ERR_OK) {
                    $ext = pathinfo($_FILES['additional_photos']['name'][$key], PATHINFO_EXTENSION);
                    $filename = uniqid() . '.' . $ext;
                    move_uploaded_file($tmp_name, '../uploads/animal_photos/' . $filename);

                    $photo_url = 'uploads/animal_photos/' . $filename;
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
        echo "Error inserting animal: " . $e->getMessage();
    }
} else {
    echo "Invalid request.";
}
?>
