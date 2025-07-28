<?php
// public/animal.php
require_once '../includes/db.php';
require_once 'header.php';
require_once 'nav.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p class='error-message'>Invalid animal ID.</p>";
    require_once 'footer.php';
    exit;
}

$animal_id = (int) $_GET['id'];

// Fetch main animal data
$stmt = $pdo->prepare("SELECT * FROM animals WHERE id = ? AND status = 'approved'");
$stmt->execute([$animal_id]);
$animal = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$animal) {
    echo "<p class='error-message'>Animal not found or not approved.</p>";
    require_once 'footer.php';
    exit;
}

$stmt = $pdo->prepare("
    SELECT a.*, ss.label AS species_status_label
    FROM animals a
    LEFT JOIN species_statuses ss ON a.species_status_id = ss.id
    WHERE a.id = ?
");
$stmt->execute([$animal_id]);
$animal = $stmt->fetch(PDO::FETCH_ASSOC);


// Taxonomy
// $taxonomy = $pdo->prepare("SELECT * FROM taxonomy WHERE animal_id = ?");
// $taxonomy->execute([$animal_id]);
// $tax = $taxonomy->fetch(PDO::FETCH_ASSOC);

// Fetch taxonomy values
// $species_stmt = $pdo->prepare("
//     SELECT s.id AS species_id, g.id AS genus_id, f.id AS family_id,
//            o.id AS order_id, c.id AS class_id, p.id AS phylum_id, k.id AS kingdom_id
//     FROM species s
//     JOIN genera g ON s.genus_id = g.id
//     JOIN families f ON g.family_id = f.id
//     JOIN orders o ON f.order_id = o.id
//     JOIN classes c ON o.class_id = c.id
//     JOIN phyla p ON c.phylum_id = p.id
//     JOIN kingdoms k ON p.kingdom_id = k.id
//     WHERE s.id = ?
// ");

$taxonomy_sql = "
    SELECT 
        species.name AS species,
        genera.name AS genus,
        families.name AS family,
        orders.name AS order_name,
        classes.name AS class,
        phyla.name AS phylum,
        kingdoms.name AS kingdom
    FROM taxonomy
    JOIN species ON taxonomy.species_id = species.id
    JOIN genera ON species.genus_id = genera.id
    JOIN families ON genera.family_id = families.id
    JOIN orders ON families.order_id = orders.id
    JOIN classes ON orders.class_id = classes.id
    JOIN phyla ON classes.phylum_id = phyla.id
    JOIN kingdoms ON phyla.kingdom_id = kingdoms.id
    WHERE taxonomy.animal_id = ?
";
$tax_stmt = $pdo->prepare($taxonomy_sql);
$tax_stmt->execute([$animal_id]);
$tax = $tax_stmt->fetch(PDO::FETCH_ASSOC);






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

<style>
/* Premium Animal Page Styles */
.animal-container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 2rem;
}

.animal-header {
    display: flex;
    gap: 3rem;
    margin-bottom: 3rem;
}

.animal-image {
    flex: 1;
    min-width: 300px;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 15px 30px rgba(0,0,0,0.1);
}

.animal-image img {
    width: 100%;
    height: auto;
    object-fit: cover;
}

.animal-basic-info {
    flex: 2;
}

.animal-title {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    color: var(--color-primary-dark);
}

.animal-scientific {
    font-style: italic;
    color: var(--color-text-secondary);
    margin-bottom: 1.5rem;
    display: block;
}

.animal-stats {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 2fr));
    gap: 0.8rem;
    margin-bottom: 1rem;
}

.stat-card {
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
    border-radius: 8px;
    padding: 1rem;
    border: 1px solid rgba(255,255,255,0.2);
}

.stat-label {
    font-size: 0.9rem;
    color: var(--color-text-secondary);
    margin-bottom: 0.5rem;
}

.stat-value {
    font-size: 1.2rem;
    font-weight: bold;
    color: var(--color-primary-dark);
}

.status-badge {
    display: inline-block;
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    font-weight: bold;
    font-size: 0.9rem;
}

.status-endangered { background: #ff6b6b; color: white; }
.status-vulnerable { background: #ffb347; color: white; }
.status-least-concern { background: #77dd77; color: white; }

.section {
    margin-bottom: 3rem;
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
    border-radius: 12px;
    padding: 1.5rem;
    padding-top: 0;
    border: 1px solid rgba(255,255,255,0.2);
}

.section-title {
    font-size: 1.8rem;
    color: var(--color-primary-dark);
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--color-accent-primary);
}

.grid-2 {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
}

.gallery {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1.5rem;
}

.gallery-item {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.gallery-item:hover {
    transform: translateY(-5px);
}

.gallery-item img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.gallery-caption {
    padding: 1rem;
    background: white;
}

.error-message {
    text-align: center;
    padding: 2rem;
    color: #ff6b6b;
    font-size: 1.2rem;
}

@media (max-width: 768px) {
    .animal-header {
        flex-direction: column;
    }
    
    .animal-image {
        width: 100%;
    }
}
</style>

<div class="animal-container">
    <div class="animal-header">
        <?php if (!empty($animal['main_photo'])): ?>
        <div class="animal-image">
            <img src="../uploads/animals/<?= $animal['main_photo'] ?>" alt="<?= htmlspecialchars($animal['common_name']) ?>">
        </div>
        <?php endif; ?>
        
        <div class="animal-basic-info">
            <h1 class="animal-title"><?= htmlspecialchars($animal['common_name']) ?></h1>
            <span class="animal-scientific"><?= htmlspecialchars($animal['scientific_name']) ?></span>
            
            <div class="animal-stats">
                <div class="stat-card">
                    <div class="stat-label">Conservation Status</div>
                    <div class="stat-value">
                       <?php if (!empty($animal['species_status_label'])): ?>
                            <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $animal['species_status_label'])) ?>">
                                <?= htmlspecialchars(ucfirst($animal['species_status_label'])) ?>
                            </span>
                        <?php endif; ?>

                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-label">Population</div>
                    <div class="stat-value"><?= htmlspecialchars($animal['population_estimate']) ?></div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-label">Average Weight</div>
                    <div class="stat-value"><?= $animal['avg_weight_kg'] ?> kg</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-label">Average Length</div>
                    <div class="stat-value"><?= $animal['avg_length_cm'] ?> cm</div>
                </div>
            </div>
            
            <?php if (!empty($animal['appearance'])): ?>
            <div class="section">
                <h2 class="section-title">Appearance</h2>
                <p><?= nl2br(htmlspecialchars($animal['appearance'])) ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid-2">
       <?php if ($tax): ?>
            <div class="section">
                <h2 class="section-title">Taxonomy</h2>
                <ul>
                    <p><strong>Kingdom:</strong> <?= htmlspecialchars($tax['kingdom']) ?></p>
                    <p><strong>Phylum:</strong> <?= htmlspecialchars($tax['phylum']) ?></p>
                    <p><strong>Class:</strong> <?= htmlspecialchars($tax['class']) ?></p>
                    <p><strong>Order:</strong> <?= htmlspecialchars($tax['order_name']) ?></p>
                    <p><strong>Family:</strong> <?= htmlspecialchars($tax['family']) ?></p>
                    <p><strong>Genus:</strong> <?= htmlspecialchars($tax['genus']) ?></p>
                    <p><strong>Species:</strong> <?= htmlspecialchars($tax['species']) ?></p>
                </ul>
            </div>
        <?php else: ?>
            <p><em>Taxonomy information is not available.</em></p>
        <?php endif; ?>



        <?php if ($habit): ?>
        <div class="section">
            <h2 class="section-title">Habits</h2>
            <?php if (!empty($habit['diet'])): ?>
                <p><strong>Diet:</strong> <?= nl2br(htmlspecialchars($habit['diet'])) ?></p>
            <?php endif; ?>
            <?php if (!empty($habit['mating_habits'])): ?>
                <p><strong>Mating Habits:</strong> <?= nl2br(htmlspecialchars($habit['mating_habits'])) ?></p>
            <?php endif; ?>
            <?php if (!empty($habit['behavior'])): ?>
                <p><strong>Behavior:</strong> <?= nl2br(htmlspecialchars($habit['behavior'])) ?></p>
            <?php endif; ?>
            <?php if (!empty($habit['habitat'])): ?>
                <p><strong>Habitat:</strong> <?= nl2br(htmlspecialchars($habit['habitat'])) ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <?php if ($location): ?>
    <div class="section">
        <h2 class="section-title">Geography</h2>
        <div class="grid-2">
            <?php if (!empty($location['continent'])): ?>
                <p><strong>Continent:</strong> <?= $location['continent'] ?></p>
            <?php endif; ?>
            <?php if (!empty($location['subcontinent'])): ?>
                <p><strong>Subcontinent:</strong> <?= $location['subcontinent'] ?></p>
            <?php endif; ?>
            <?php if (!empty($location['country'])): ?>
                <p><strong>Country:</strong> <?= $location['country'] ?></p>
            <?php endif; ?>
            <?php if (!empty($location['realm'])): ?>
                <p><strong>Realm:</strong> <?= $location['realm'] ?></p>
            <?php endif; ?>
            <?php if (!empty($location['biome'])): ?>
                <p><strong>Biome:</strong> <?= $location['biome'] ?></p>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($gallery): ?>
    <div class="section">
        <h2 class="section-title">Gallery</h2>
        <div class="gallery">
            <?php foreach ($gallery as $photo): ?>
            <div class="gallery-item">
                <img src="uploads/<?= $photo['photo_url'] ?>" alt="<?= htmlspecialchars($photo['caption']) ?>">
                <?php if (!empty($photo['caption'])): ?>
                <div class="gallery-caption">
                    <?= htmlspecialchars($photo['caption']) ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>