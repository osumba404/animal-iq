<?php
// public/animal.php
require_once '../includes/db.php';
require_once 'header.php';
require_once 'nav.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p>Invalid animal ID.</p>";
    require_once 'footer.php';
    exit;
}

$animal_id = (int) $_GET['id'];

// Fetch main animal data
$stmt = $pdo->prepare("SELECT * FROM animals WHERE id = ? AND status = 'approved'");
$stmt->execute([$animal_id]);
$animal = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$animal) {
    echo "<p>Animal not found or not approved.</p>";
    require_once 'footer.php';
    exit;
}

// Taxonomy
$taxonomy = $pdo->prepare("SELECT * FROM taxonomy WHERE animal_id = ?");
$taxonomy->execute([$animal_id]);
$tax = $taxonomy->fetch(PDO::FETCH_ASSOC);

// Habits
$habits = $pdo->prepare("SELECT * FROM animal_habits WHERE animal_id = ?");
$habits->execute([$animal_id]);
$habit = $habits->fetch(PDO::FETCH_ASSOC);

// Geography
$geo = $pdo->prepare("SELECT * FROM animal_geography WHERE animal_id = ?");
$geo->execute([$animal_id]);
$location = $geo->fetch(PDO::FETCH_ASSOC);

// Photos
$photos = $pdo->prepare("SELECT * FROM animal_photos WHERE animal_id = ?");
$photos->execute([$animal_id]);
$gallery = $photos->fetchAll(PDO::FETCH_ASSOC);
?>

<h1><?php echo htmlspecialchars($animal['common_name']); ?> (<?php echo htmlspecialchars($animal['scientific_name']); ?>)</h1>

<?php if (!empty($animal['main_photo'])): ?>
    <img src="uploads/<?php echo $animal['main_photo']; ?>" alt="Main photo" style="max-width:300px;">
<?php endif; ?>

<p><strong>Status:</strong> <?php echo ucfirst($animal['species_status']); ?></p>
<p><strong>Population:</strong> <?php echo htmlspecialchars($animal['population_estimate']); ?></p>
<p><strong>Avg Weight:</strong> <?php echo $animal['avg_weight_kg']; ?> kg</p>
<p><strong>Avg Length:</strong> <?php echo $animal['avg_length_cm']; ?> cm</p>
<p><strong>Appearance:</strong> <?php echo nl2br(htmlspecialchars($animal['appearance'])); ?></p>

<h2>Taxonomy</h2>
<?php if ($tax): ?>
    <ul>
        <li><strong>Kingdom:</strong> <?php echo $tax['kingdom']; ?></li>
        <li><strong>Phylum:</strong> <?php echo $tax['phylum']; ?></li>
        <li><strong>Class:</strong> <?php echo $tax['class']; ?></li>
        <li><strong>Order:</strong> <?php echo $tax['order']; ?></li>
        <li><strong>Family:</strong> <?php echo $tax['family']; ?></li>
        <li><strong>Genus:</strong> <?php echo $tax['genus']; ?></li>
        <li><strong>Species:</strong> <?php echo $tax['species']; ?></li>
    </ul>
<?php else: ?>
    <p>No taxonomy info available.</p>
<?php endif; ?>

<h2>Habits</h2>
<?php if ($habit): ?>
    <p><strong>Diet:</strong> <?php echo nl2br(htmlspecialchars($habit['diet'])); ?></p>
    <p><strong>Mating Habits:</strong> <?php echo nl2br(htmlspecialchars($habit['mating_habits'])); ?></p>
    <p><strong>Behavior:</strong> <?php echo nl2br(htmlspecialchars($habit['behavior'])); ?></p>
    <p><strong>Habitat:</strong> <?php echo nl2br(htmlspecialchars($habit['habitat'])); ?></p>
<?php else: ?>
    <p>No habit information available.</p>
<?php endif; ?>

<h2>Geography</h2>
<?php if ($location): ?>
    <p><strong>Continent:</strong> <?php echo $location['continent']; ?></p>
    <p><strong>Subcontinent:</strong> <?php echo $location['subcontinent']; ?></p>
    <p><strong>Country:</strong> <?php echo $location['country']; ?></p>
    <p><strong>Realm:</strong> <?php echo $location['realm']; ?></p>
    <p><strong>Biome:</strong> <?php echo $location['biome']; ?></p>
<?php else: ?>
    <p>No geography information available.</p>
<?php endif; ?>

<h2>Gallery</h2>
<?php if ($gallery): ?>
    <?php foreach ($gallery as $photo): ?>
        <div style="margin-bottom: 10px;">
            <img src="uploads/<?php echo $photo['photo_url']; ?>" style="max-width:200px;">
            <p><?php echo htmlspecialchars($photo['caption']); ?></p>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>No additional photos.</p>
<?php endif; ?>

<?php require_once 'footer.php'; ?>
