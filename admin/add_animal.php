<!-- admin/add_animal.php -->

<?php
require_once '../includes/db.php';
require_once '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $common_name = $_POST['common_name'];
    $scientific_name = $_POST['scientific_name'];
    $population_estimate = $_POST['population_estimate'];
    $species_status = $_POST['species_status'];
    $avg_weight_kg = $_POST['avg_weight_kg'];
    $avg_length_cm = $_POST['avg_length_cm'];
    $appearance = $_POST['appearance'];
    $submitted_by = 1; // Replace with admin ID from session if using authentication

    // Handle image upload
    $main_photo = '';
    if (isset($_FILES['main_photo']) && $_FILES['main_photo']['error'] == 0) {
        $upload_dir = '../assets/images/animals/';
        $filename = time() . '_' . basename($_FILES['main_photo']['name']);
        $filepath = $upload_dir . $filename;
        move_uploaded_file($_FILES['main_photo']['tmp_name'], $filepath);
        $main_photo = $filename;
    }

    // Insert into animals table
    $stmt = $pdo->prepare("INSERT INTO animals 
        (scientific_name, common_name, population_estimate, species_status, avg_weight_kg, avg_length_cm, appearance, main_photo, submitted_by) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $scientific_name, $common_name, $population_estimate, $species_status,
        $avg_weight_kg, $avg_length_cm, $appearance, $main_photo, $submitted_by
    ]);

    $animal_id = $pdo->lastInsertId();

    // Taxonomy
    $stmt = $pdo->prepare("INSERT INTO taxonomy (animal_id, kingdom, phylum, class, `order`, family, genus, species)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $animal_id,
        $_POST['kingdom'], $_POST['phylum'], $_POST['class'], $_POST['order'],
        $_POST['family'], $_POST['genus'], $_POST['species']
    ]);

    // Geography
    $stmt = $pdo->prepare("INSERT INTO animal_geography (animal_id, continent, subcontinent, country, realm, biome)
        VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $animal_id,
        $_POST['continent'], $_POST['subcontinent'], $_POST['country'], $_POST['realm'], $_POST['biome']
    ]);

    // Habits
    $stmt = $pdo->prepare("INSERT INTO animal_habits (animal_id, diet, mating_habits, behavior, habitat)
        VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $animal_id,
        $_POST['diet'], $_POST['mating_habits'], $_POST['behavior'], $_POST['habitat']
    ]);

    echo "<p style='color:green;'>Animal added successfully!</p>";
}
?>

<h2>Add New Animal</h2>
<form method="post" enctype="multipart/form-data">
    <h3>Basic Info</h3>
    <label>Common Name: <input type="text" name="common_name" required></label><br>
    <label>Scientific Name: <input type="text" name="scientific_name" required></label><br>
    <label>Population Estimate: <input type="text" name="population_estimate"></label><br>
    <label>Status:
        <select name="species_status">
            <option value="least concern">Least Concern</option>
            <option value="vulnerable">Vulnerable</option>
            <option value="endangered">Endangered</option>
            <option value="extinct">Extinct</option>
        </select>
    </label><br>
    <label>Avg Weight (kg): <input type="number" step="0.1" name="avg_weight_kg"></label><br>
    <label>Avg Length (cm): <input type="number" step="0.1" name="avg_length_cm"></label><br>
    <label>Appearance:<br><textarea name="appearance" rows="4" cols="40"></textarea></label><br>
    <label>Main Photo: <input type="file" name="main_photo" accept="image/*"></label><br>

    <h3>Taxonomy</h3>
    <label>Kingdom: <input type="text" name="kingdom"></label><br>
    <label>Phylum: <input type="text" name="phylum"></label><br>
    <label>Class: <input type="text" name="class"></label><br>
    <label>Order: <input type="text" name="order"></label><br>
    <label>Family: <input type="text" name="family"></label><br>
    <label>Genus: <input type="text" name="genus"></label><br>
    <label>Species: <input type="text" name="species"></label><br>

    <h3>Geography</h3>
    <label>Continent: <input type="text" name="continent"></label><br>
    <label>Subcontinent: <input type="text" name="subcontinent"></label><br>
    <label>Country: <input type="text" name="country"></label><br>
    <label>Realm: <input type="text" name="realm"></label><br>
    <label>Biome: <input type="text" name="biome"></label><br>

    <h3>Habits</h3>
    <label>Diet:<br><textarea name="diet" rows="3" cols="40"></textarea></label><br>
    <label>Mating Habits:<br><textarea name="mating_habits" rows="3" cols="40"></textarea></label><br>
    <label>Behavior:<br><textarea name="behavior" rows="3" cols="40"></textarea></label><br>
    <label>Habitat:<br><textarea name="habitat" rows="3" cols="40"></textarea></label><br>

    <button type="submit">Save Animal</button>
</form>

<p><a href="manage_animals.php">← Back to Animals</a></p>

<?php require_once '../includes/footer.php'; ?>
