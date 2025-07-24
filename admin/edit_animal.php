<!-- admin/edit_animal.php -->

<?php
require_once '../includes/db.php';
require_once '../includes/header.php';

if (!isset($_GET['id'])) {
    echo "<p style='color:red;'>No animal ID provided.</p>";
    exit;
}

$animal_id = $_GET['id'];

// Fetch animal data
$animal = $pdo->prepare("SELECT * FROM animals WHERE id = ?");
$animal->execute([$animal_id]);
$animal = $animal->fetch();

if (!$animal) {
    echo "<p style='color:red;'>Animal not found.</p>";
    exit;
}

// Fetch related taxonomy, geography, and habits
$taxonomy = $pdo->prepare("SELECT * FROM taxonomy WHERE animal_id = ?");
$taxonomy->execute([$animal_id]);
$taxonomy = $taxonomy->fetch();

$geo = $pdo->prepare("SELECT * FROM animal_geography WHERE animal_id = ?");
$geo->execute([$animal_id]);
$geo = $geo->fetch();

$habits = $pdo->prepare("SELECT * FROM animal_habits WHERE animal_id = ?");
$habits->execute([$animal_id]);
$habits = $habits->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $common_name = $_POST['common_name'];
    $scientific_name = $_POST['scientific_name'];
    $population_estimate = $_POST['population_estimate'];
    $species_status = $_POST['species_status'];
    $avg_weight_kg = $_POST['avg_weight_kg'];
    $avg_length_cm = $_POST['avg_length_cm'];
    $appearance = $_POST['appearance'];

    // Handle optional photo update
    if (isset($_FILES['main_photo']) && $_FILES['main_photo']['error'] == 0) {
        $upload_dir = '../assets/images/animals/';
        $filename = time() . '_' . basename($_FILES['main_photo']['name']);
        $filepath = $upload_dir . $filename;
        move_uploaded_file($_FILES['main_photo']['tmp_name'], $filepath);
        $main_photo = $filename;

        $pdo->prepare("UPDATE animals SET main_photo = ? WHERE id = ?")->execute([$main_photo, $animal_id]);
    }

    // Update animal table
    $pdo->prepare("UPDATE animals SET scientific_name=?, common_name=?, population_estimate=?, species_status=?, avg_weight_kg=?, avg_length_cm=?, appearance=? WHERE id=?")
        ->execute([$scientific_name, $common_name, $population_estimate, $species_status, $avg_weight_kg, $avg_length_cm, $appearance, $animal_id]);

    // Update taxonomy
    $pdo->prepare("UPDATE taxonomy SET kingdom=?, phylum=?, class=?, `order`=?, family=?, genus=?, species=? WHERE animal_id=?")
        ->execute([
            $_POST['kingdom'], $_POST['phylum'], $_POST['class'], $_POST['order'],
            $_POST['family'], $_POST['genus'], $_POST['species'], $animal_id
        ]);

    // Update geography
    $pdo->prepare("UPDATE animal_geography SET continent=?, subcontinent=?, country=?, realm=?, biome=? WHERE animal_id=?")
        ->execute([
            $_POST['continent'], $_POST['subcontinent'], $_POST['country'],
            $_POST['realm'], $_POST['biome'], $animal_id
        ]);

    // Update habits
    $pdo->prepare("UPDATE animal_habits SET diet=?, mating_habits=?, behavior=?, habitat=? WHERE animal_id=?")
        ->execute([
            $_POST['diet'], $_POST['mating_habits'], $_POST['behavior'], $_POST['habitat'], $animal_id
        ]);

    echo "<p style='color:green;'>Animal updated successfully!</p>";
    // Refresh data
    header("Location: edit_animal.php?id=$animal_id&updated=1");
    exit;
}

if (isset($_GET['updated'])) {
    echo "<p style='color:green;'>Changes saved successfully!</p>";
}
?>

<h2>Edit Animal: <?= htmlspecialchars($animal['common_name']) ?></h2>
<form method="post" enctype="multipart/form-data">
    <h3>Basic Info</h3>
    <label>Common Name: <input type="text" name="common_name" value="<?= htmlspecialchars($animal['common_name']) ?>" required></label><br>
    <label>Scientific Name: <input type="text" name="scientific_name" value="<?= htmlspecialchars($animal['scientific_name']) ?>" required></label><br>
    <label>Population Estimate: <input type="text" name="population_estimate" value="<?= htmlspecialchars($animal['population_estimate']) ?>"></label><br>
    <label>Status:
        <select name="species_status">
            <?php
            $statuses = ['least concern', 'vulnerable', 'endangered', 'extinct'];
            foreach ($statuses as $status) {
                $selected = ($animal['species_status'] === $status) ? 'selected' : '';
                echo "<option value='$status' $selected>$status</option>";
            }
            ?>
        </select>
    </label><br>
    <label>Avg Weight (kg): <input type="number" step="0.1" name="avg_weight_kg" value="<?= htmlspecialchars($animal['avg_weight_kg']) ?>"></label><br>
    <label>Avg Length (cm): <input type="number" step="0.1" name="avg_length_cm" value="<?= htmlspecialchars($animal['avg_length_cm']) ?>"></label><br>
    <label>Appearance:<br><textarea name="appearance" rows="4" cols="40"><?= htmlspecialchars($animal['appearance']) ?></textarea></label><br>
    <label>Main Photo (leave blank to keep current): <input type="file" name="main_photo" accept="image/*"></label><br>

    <h3>Taxonomy</h3>
    <label>Kingdom: <input type="text" name="kingdom" value="<?= $taxonomy['kingdom'] ?>"></label><br>
    <label>Phylum: <input type="text" name="phylum" value="<?= $taxonomy['phylum'] ?>"></label><br>
    <label>Class: <input type="text" name="class" value="<?= $taxonomy['class'] ?>"></label><br>
    <label>Order: <input type="text" name="order" value="<?= $taxonomy['order'] ?>"></label><br>
    <label>Family: <input type="text" name="family" value="<?= $taxonomy['family'] ?>"></label><br>
    <label>Genus: <input type="text" name="genus" value="<?= $taxonomy['genus'] ?>"></label><br>
    <label>Species: <input type="text" name="species" value="<?= $taxonomy['species'] ?>"></label><br>

    <h3>Geography</h3>
    <label>Continent: <input type="text" name="continent" value="<?= $geo['continent'] ?>"></label><br>
    <label>Subcontinent: <input type="text" name="subcontinent" value="<?= $geo['subcontinent'] ?>"></label><br>
    <label>Country: <input type="text" name="country" value="<?= $geo['country'] ?>"></label><br>
    <label>Realm: <input type="text" name="realm" value="<?= $geo['realm'] ?>"></label><br>
    <label>Biome: <input type="text" name="biome" value="<?= $geo['biome'] ?>"></label><br>

    <h3>Habits</h3>
    <label>Diet:<br><textarea name="diet" rows="3" cols="40"><?= $habits['diet'] ?></textarea></label><br>
    <label>Mating Habits:<br><textarea name="mating_habits" rows="3" cols="40"><?= $habits['mating_habits'] ?></textarea></label><br>
    <label>Behavior:<br><textarea name="behavior" rows="3" cols="40"><?= $habits['behavior'] ?></textarea></label><br>
    <label>Habitat:<br><textarea name="habitat" rows="3" cols="40"><?= $habits['habitat'] ?></textarea></label><br>

    <button type="submit">Update Animal</button>
</form>

<p><a href="manage_animals.php">← Back to Animals</a></p>

<?php require_once '../includes/footer.php'; ?>
