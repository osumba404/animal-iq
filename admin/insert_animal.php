<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $scientific_name = $_POST['scientific_name'];
        $common_name = $_POST['common_name'];
        $population_estimate = $_POST['population_estimate'];
        $species_status_id = $_POST['species_status_id'];
        $avg_weight_kg = $_POST['avg_weight_kg'];
        $avg_length_cm = $_POST['avg_length_cm'];
        $appearance = $_POST['appearance'];
        $species_id = $_POST['species_id'];
        $submitted_by = $_SESSION['admin_id'] ?? null;
        $approved_by = null;

        if (!$submitted_by) {
            throw new Exception("You must be logged in as admin to perform this action.");
        }

        // 📷 Handle main photo
        $main_photo = null;
        if (!empty($_FILES['main_photo']['name']) && $_FILES['main_photo']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['main_photo']['name'], PATHINFO_EXTENSION);
            $main_photo = uniqid('animal_') . '.' . $ext;
            move_uploaded_file($_FILES['main_photo']['tmp_name'], '../uploads/animals/' . $main_photo);
        }

        $pdo->beginTransaction();

        // 🐾 Insert into animals
        $stmt = $pdo->prepare("INSERT INTO animals 
            (scientific_name, common_name, population_estimate, species_status_id, avg_weight_kg, avg_length_cm, appearance, main_photo, submitted_by, approved_by) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
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
  
        ]);

        $animal_id = $pdo->lastInsertId();

        // 🧬 Taxonomy
        $stmt = $pdo->prepare("INSERT INTO taxonomy (animal_id, species_id) VALUES (?, ?)");
        $stmt->execute([$animal_id, $species_id]);

        // 🌍 Geography
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

        // 🧠 Habits
        $stmt = $pdo->prepare("INSERT INTO animal_habits (animal_id, diet, mating_habits, behavior, habitat) 
                               VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $animal_id,
            $_POST['diet'],
            $_POST['mating_habits'],
            $_POST['behavior'],
            $_POST['habitat']
        ]);

        // 📈 Life Data
        $stmt = $pdo->prepare("INSERT INTO animal_life_data (animal_id, lifespan_years, gestation_period_days, litter_size_avg, maturity_age_years)
                               VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $animal_id,
            $_POST['lifespan_years'] ?: null,
            $_POST['gestation_period_days'] ?: null,
            $_POST['litter_size_avg'] ?: null,
            $_POST['maturity_age_years'] ?: null
        ]);

        // 👥 Human Interaction
        $stmt = $pdo->prepare("INSERT INTO animal_human_interaction (animal_id, threats, conservation_efforts)
                               VALUES (?, ?, ?)");
        $stmt->execute([
            $animal_id,
            $_POST['threats'],
            $_POST['conservation_efforts']
        ]);

        // 🛡️ Defense
        $stmt = $pdo->prepare("INSERT INTO animal_defense (animal_id, defense_mechanisms, notable_adaptations)
                               VALUES (?, ?, ?)");
        $stmt->execute([
            $animal_id,
            $_POST['defense_mechanisms'],
            $_POST['notable_adaptations']
        ]);

        // ⚕️ Health Risks
        $stmt = $pdo->prepare("INSERT INTO animal_health_risks (animal_id, common_diseases, known_parasites, zoonotic_potential)
                               VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $animal_id,
            $_POST['common_diseases'],
            $_POST['known_parasites'],
            isset($_POST['zoonotic_potential']) ? 1 : 0
        ]);

        // 🧠 Fun Facts
        if (!empty($_POST['facts'])) {
            $stmt = $pdo->prepare("INSERT INTO animal_facts (animal_id, fact) VALUES (?, ?)");
            foreach ($_POST['facts'] as $fact) {
                if (!empty(trim($fact))) {
                    $stmt->execute([$animal_id, $fact]);
                }
            }
        }

        // 📷 Additional photos
        if (!empty($_FILES['photos']['name'][0])) {
            foreach ($_FILES['photos']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['photos']['error'][$key] === UPLOAD_ERR_OK) {
                    $ext = pathinfo($_FILES['photos']['name'][$key], PATHINFO_EXTENSION);
                    $filename = uniqid('photo_') . '.' . $ext;
                    move_uploaded_file($tmp_name, '../uploads/animals/' . $filename);
                    $caption = $_POST['captions'][$key] ?? '';
                    $stmt = $pdo->prepare("INSERT INTO animal_photos (animal_id, photo_url, caption) VALUES (?, ?, ?)");
                    $stmt->execute([$animal_id, $filename, $caption]);
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
