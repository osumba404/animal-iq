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

// Life Data
$life_stmt = $pdo->prepare("SELECT * FROM animal_life_data WHERE animal_id = ?");
$life_stmt->execute([$animal_id]);
$life = $life_stmt->fetch(PDO::FETCH_ASSOC);

// Human Interaction
$interaction_stmt = $pdo->prepare("SELECT * FROM animal_human_interaction WHERE animal_id = ?");
$interaction_stmt->execute([$animal_id]);
$interaction = $interaction_stmt->fetch(PDO::FETCH_ASSOC);

// Defense
$defense_stmt = $pdo->prepare("SELECT * FROM animal_defense WHERE animal_id = ?");
$defense_stmt->execute([$animal_id]);
$defense = $defense_stmt->fetch(PDO::FETCH_ASSOC);

// Health Risks
$health_stmt = $pdo->prepare("SELECT * FROM animal_health_risks WHERE animal_id = ?");
$health_stmt->execute([$animal_id]);
$health = $health_stmt->fetch(PDO::FETCH_ASSOC);

// Facts (multiple rows)
$facts_stmt = $pdo->prepare("SELECT fact FROM animal_facts WHERE animal_id = ?");
$facts_stmt->execute([$animal_id]);
$facts = $facts_stmt->fetchAll(PDO::FETCH_COLUMN);

?>


<head>
    <link rel="stylesheet" href="assets/css/pages.css">
</head>

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

    <?php if ($life): ?>
<div class="section">
    <h2 class="section-title">Life Data</h2>
    <ul>
        <?php if ($life['lifespan_years']): ?><li><strong>Lifespan:</strong> <?= $life['lifespan_years'] ?> years</li><?php endif; ?>
        <?php if ($life['gestation_period_days']): ?><li><strong>Gestation Period:</strong> <?= $life['gestation_period_days'] ?> days</li><?php endif; ?>
        <?php if ($life['litter_size_avg']): ?><li><strong>Average Litter Size:</strong> <?= $life['litter_size_avg'] ?></li><?php endif; ?>
        <?php if ($life['maturity_age_years']): ?><li><strong>Maturity Age:</strong> <?= $life['maturity_age_years'] ?> years</li><?php endif; ?>
    </ul>
</div>
<?php endif; ?>

<?php if ($interaction): ?>
<div class="section">
    <h2 class="section-title">Human Interaction</h2>
    <?php if (!empty($interaction['threats'])): ?>
        <p><strong>Threats:</strong> <?= nl2br(htmlspecialchars($interaction['threats'])) ?></p>
    <?php endif; ?>
    <?php if (!empty($interaction['conservation_efforts'])): ?>
        <p><strong>Conservation Efforts:</strong> <?= nl2br(htmlspecialchars($interaction['conservation_efforts'])) ?></p>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php if ($defense): ?>
<div class="section">
    <h2 class="section-title">Defense & Adaptations</h2>
    <?php if (!empty($defense['defense_mechanisms'])): ?>
        <p><strong>Defense Mechanisms:</strong> <?= nl2br(htmlspecialchars($defense['defense_mechanisms'])) ?></p>
    <?php endif; ?>
    <?php if (!empty($defense['notable_adaptations'])): ?>
        <p><strong>Notable Adaptations:</strong> <?= nl2br(htmlspecialchars($defense['notable_adaptations'])) ?></p>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php if ($health): ?>
<div class="section">
    <h2 class="section-title">Health & Disease</h2>
    <?php if (!empty($health['common_diseases'])): ?>
        <p><strong>Common Diseases:</strong> <?= nl2br(htmlspecialchars($health['common_diseases'])) ?></p>
    <?php endif; ?>
    <?php if (!empty($health['known_parasites'])): ?>
        <p><strong>Known Parasites:</strong> <?= nl2br(htmlspecialchars($health['known_parasites'])) ?></p>
    <?php endif; ?>
    <p><strong>Zoonotic Potential:</strong> <?= $health['zoonotic_potential'] ? 'Yes' : 'No' ?></p>
</div>
<?php endif; ?>

<?php if (!empty($facts)): ?>
<div class="section">
    <h2 class="section-title">Interesting Facts</h2>
    <ul>
        <?php foreach ($facts as $fact): ?>
            <li><?= htmlspecialchars($fact) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>


    <?php if ($gallery): ?>
    <div class="section">
        <h2 class="section-title">Gallery</h2>
        <div class="gallery">
            <?php foreach ($gallery as $photo): ?>
            <div class="gallery-item">
                <img src="../uploads/animals/<?= $photo['photo_url'] ?>" alt="<?= htmlspecialchars($photo['caption']) ?>">
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