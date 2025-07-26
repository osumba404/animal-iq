<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once 'admin_header.php';

if (!isset($_GET['id'])) {
    die("Animal ID is required.");
}

$animal_id = $_GET['id'];
$success = isset($_GET['updated']);

// Fetch current animal data
$stmt = $pdo->prepare("SELECT * FROM animals WHERE id = ?");
$stmt->execute([$animal_id]);
$animal = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$animal) {
    die("Animal not found.");
}

// Fetch species_id from taxonomy
$taxonomy_stmt = $pdo->prepare("SELECT species_id FROM taxonomy WHERE animal_id = ?");
$taxonomy_stmt->execute([$animal_id]);
$taxonomy_row = $taxonomy_stmt->fetch(PDO::FETCH_ASSOC);

if (!$taxonomy_row || !isset($taxonomy_row['species_id'])) {
    die("Taxonomy data not found for this animal.");
}

$species_id = $taxonomy_row['species_id'];

// Fetch taxonomy values
$species_stmt = $pdo->prepare("
    SELECT s.id AS species_id, g.id AS genus_id, f.id AS family_id,
           o.id AS order_id, c.id AS class_id, p.id AS phylum_id, k.id AS kingdom_id
    FROM species s
    JOIN genera g ON s.genus_id = g.id
    JOIN families f ON g.family_id = f.id
    JOIN orders o ON f.order_id = o.id
    JOIN classes c ON o.class_id = c.id
    JOIN phyla p ON c.phylum_id = p.id
    JOIN kingdoms k ON p.kingdom_id = k.id
    WHERE s.id = ?
");
$species_stmt->execute([$species_id]);
$taxonomy = $species_stmt->fetch(PDO::FETCH_ASSOC);

// Fetch dropdown options
$kingdoms = $pdo->query("SELECT * FROM kingdoms")->fetchAll(PDO::FETCH_ASSOC);
$phyla = $pdo->query("SELECT * FROM phyla")->fetchAll(PDO::FETCH_ASSOC);
$classes = $pdo->query("SELECT * FROM classes")->fetchAll(PDO::FETCH_ASSOC);
$orders = $pdo->query("SELECT * FROM orders")->fetchAll(PDO::FETCH_ASSOC);
$families = $pdo->query("SELECT * FROM families")->fetchAll(PDO::FETCH_ASSOC);
$genera = $pdo->query("SELECT * FROM genera")->fetchAll(PDO::FETCH_ASSOC);
$species = $pdo->query("SELECT * FROM species")->fetchAll(PDO::FETCH_ASSOC);
$species_statuses = $pdo->query("SELECT * FROM species_statuses")->fetchAll(PDO::FETCH_ASSOC);

// Fetch habits
$habit_stmt = $pdo->prepare("SELECT * FROM animal_habits WHERE animal_id = ?");
$habit_stmt->execute([$animal_id]);
$habits = $habit_stmt->fetch(PDO::FETCH_ASSOC);

// Fetch geography
$geo_stmt = $pdo->prepare("SELECT * FROM animal_geography WHERE animal_id = ?");
$geo_stmt->execute([$animal_id]);
$geo = $geo_stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Animal - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/admin-style.css">
    <link rel="stylesheet" href="../assets/css/tables.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-content-container {
            margin-left: var(--sidebar-width);
            padding: 2rem;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            border-radius: 4px;
            font-size: 0.95rem;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        
        .btn-back {
            background-color: var(--color-neutral-mid);
            color: var(--color-primary-dark);
        }
        
        .btn-back:hover {
            background-color: var(--color-primary-mid);
        }
        
        .btn-primary {
            background-color: var(--color-primary-accent);
            color: var(--color-primary-light);
        }
        
        .btn-primary:hover {
            background-color: var(--color-secondary-accent);
        }
        
        .form-container {
            background-color: var(--color-neutral-light);
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--color-primary-dark);
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--color-neutral-mid);
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .form-group textarea {
            min-height: 100px;
        }
        
        .animal-photo {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid var(--color-neutral-mid);
            margin-top: 0.5rem;
        }
        
        .alert-success {
            background-color: rgba(46, 204, 113, 0.2);
            color: #2ecc71;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(46, 204, 113, 0.3);
        }
        
        .section-divider {
            margin: 2rem 0;
            border: 0;
            height: 1px;
            background-color: var(--color-neutral-mid);
        }
        
        @media (max-width: 768px) {
            .admin-content-container {
                margin-left: 0;
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="admin-content-container">
        <div class="page-header">
            <h1><i class="fas fa-paw"></i> Edit Animal: <?= htmlspecialchars($animal['common_name']) ?></h1>
            <a href="manage_animals.php" class="btn btn-back">
                <i class="fas fa-arrow-left"></i> Back to Animals
            </a>
        </div>

        <?php if ($success): ?>
            <div class="alert-success">
                <i class="fas fa-check-circle"></i> Animal updated successfully.
            </div>
        <?php endif; ?>

        <div class="form-container">
            <form method="POST" action="update_animal.php" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $animal_id ?>">

                <div class="form-group">
                    <label>Scientific Name</label>
                    <input type="text" name="scientific_name" value="<?= htmlspecialchars($animal['scientific_name']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Common Name</label>
                    <input type="text" name="common_name" value="<?= htmlspecialchars($animal['common_name']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Population Estimate</label>
                    <input type="text" name="population_estimate" value="<?= htmlspecialchars($animal['population_estimate']) ?>">
                </div>

                <div class="form-group">
                    <label>Species Status</label>
                    <select name="species_status_id">
                        <?php foreach ($species_statuses as $status): ?>
                            <option value="<?= $status['id'] ?>" <?= $status['id'] == $animal['species_status_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars(ucfirst($status['label'])) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Average Weight (kg)</label>
                    <input type="number" step="0.01" name="avg_weight_kg" value="<?= htmlspecialchars($animal['avg_weight_kg']) ?>">
                </div>

                <div class="form-group">
                    <label>Average Length (cm)</label>
                    <input type="number" step="0.01" name="avg_length_cm" value="<?= htmlspecialchars($animal['avg_length_cm']) ?>">
                </div>

                <div class="form-group">
                    <label>Appearance</label>
                    <textarea name="appearance"><?= htmlspecialchars($animal['appearance']) ?></textarea>
                </div>

                <div class="form-group">
                    <label>Main Photo</label>
                    <input type="file" name="main_photo">
                    <?php if (!empty($animal['main_photo'])): ?>
                        <img src="../assets/images/animals/<?= htmlspecialchars($animal['main_photo']) ?>" class="animal-photo" alt="Animal Photo">
                    <?php endif; ?>
                </div>

                <hr class="section-divider">

                <h2><i class="fas fa-sitemap"></i> Taxonomy</h2>

                <div class="form-group">
                    <label>Kingdom</label>
                    <select name="kingdom_id">
                        <?php foreach ($kingdoms as $item): ?>
                            <option value="<?= $item['id'] ?>" <?= $taxonomy['kingdom_id'] == $item['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($item['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Phylum</label>
                    <select name="phylum_id" id="phylum-select">
                        <option value="">-- Select Phylum --</option>
                        <?php foreach ($phyla as $item): ?>
                            <option value="<?= $item['id'] ?>" <?= $taxonomy['phylum_id'] == $item['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($item['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Class</label>
                    <select name="class_id" id="class-select">
                        <option value="">-- Select Class --</option>
                        <?php foreach ($classes as $item): ?>
                            <option value="<?= $item['id'] ?>" <?= $taxonomy['class_id'] == $item['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($item['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Order</label>
                    <select name="order_id" id="order-select">
                        <option value="">-- Select Order --</option>
                        <?php foreach ($orders as $item): ?>
                            <option value="<?= $item['id'] ?>" <?= $taxonomy['order_id'] == $item['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($item['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Family</label>
                    <select name="family_id" id="family-select">
                        <option value="">-- Select Family --</option>
                        <?php foreach ($families as $item): ?>
                            <option value="<?= $item['id'] ?>" <?= $taxonomy['family_id'] == $item['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($item['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Genus</label>
                    <select name="genus_id" id="genus-select">
                        <option value="">-- Select Genus --</option>
                        <?php foreach ($genera as $item): ?>
                            <option value="<?= $item['id'] ?>" <?= $taxonomy['genus_id'] == $item['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($item['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Species</label>
                    <select name="species_id" required>
                        <option value="">-- Select Species --</option>
                        <?php foreach ($species as $item): ?>
                            <option value="<?= $item['id'] ?>" <?= $taxonomy['species_id'] == $item['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($item['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <hr class="section-divider">

                <h2><i class="fas fa-leaf"></i> Habits</h2>

                <div class="form-group">
                    <label>Diet</label>
                    <textarea name="habits[diet]"><?= htmlspecialchars($habits['diet'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label>Mating Habits</label>
                    <textarea name="habits[mating_habits]"><?= htmlspecialchars($habits['mating_habits'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label>Behavior</label>
                    <textarea name="habits[behavior]"><?= htmlspecialchars($habits['behavior'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label>Habitat</label>
                    <textarea name="habits[habitat]"><?= htmlspecialchars($habits['habitat'] ?? '') ?></textarea>
                </div>

                <hr class="section-divider">

                <h2><i class="fas fa-globe-americas"></i> Geography</h2>

                <div class="form-group">
                    <label>Continent</label>
                    <input type="text" name="geography[continent]" value="<?= htmlspecialchars($geo['continent'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label>Subcontinent</label>
                    <input type="text" name="geography[subcontinent]" value="<?= htmlspecialchars($geo['subcontinent'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label>Country</label>
                    <input type="text" name="geography[country]" value="<?= htmlspecialchars($geo['country'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label>Realm</label>
                    <input type="text" name="geography[realm]" value="<?= htmlspecialchars($geo['realm'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label>Biome</label>
                    <input type="text" name="geography[biome]" value="<?= htmlspecialchars($geo['biome'] ?? '') ?>">
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Animal
                </button>
            </form>
        </div>
    </div>

    <script>
        function loadTaxonomy(type, parentId, targetSelect, selectedId = null) {
            if (!parentId) {
                targetSelect.innerHTML = '<option value="">-- Select --</option>';
                return;
            }

            fetch(`../ajax/load_taxonomy_level.php?type=${type}&parent_id=${parentId}`)
                .then(res => res.json())
                .then(data => {
                    targetSelect.innerHTML = '<option value="">-- Select --</option>';
                    data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.id;
                        option.textContent = item.name;
                        if (selectedId && item.id == selectedId) {
                            option.selected = true;
                        }
                        targetSelect.appendChild(option);
                    });
                });
        }

        document.addEventListener('DOMContentLoaded', function () {
            const kingdomSelect = document.querySelector('select[name="kingdom_id"]');
            const phylumSelect = document.getElementById('phylum-select');
            const classSelect = document.getElementById('class-select');
            const orderSelect = document.getElementById('order-select');
            const familySelect = document.getElementById('family-select');
            const genusSelect = document.getElementById('genus-select');
            const speciesSelect = document.getElementById('species-select');

            kingdomSelect.addEventListener('change', function () {
                loadTaxonomy('phyla', this.value, phylumSelect);
                classSelect.innerHTML = '<option value="">-- Select --</option>';
                orderSelect.innerHTML = '<option value="">-- Select --</option>';
                familySelect.innerHTML = '<option value="">-- Select --</option>';
                genusSelect.innerHTML = '<option value="">-- Select --</option>';
                speciesSelect.innerHTML = '<option value="">-- Select --</option>';
            });

            phylumSelect.addEventListener('change', function () {
                loadTaxonomy('classes', this.value, classSelect);
                orderSelect.innerHTML = '<option value="">-- Select --</option>';
                familySelect.innerHTML = '<option value="">-- Select --</option>';
                genusSelect.innerHTML = '<option value="">-- Select --</option>';
                speciesSelect.innerHTML = '<option value="">-- Select --</option>';
            });

            classSelect.addEventListener('change', function () {
                loadTaxonomy('orders', this.value, orderSelect);
                familySelect.innerHTML = '<option value="">-- Select --</option>';
                genusSelect.innerHTML = '<option value="">-- Select --</option>';
                speciesSelect.innerHTML = '<option value="">-- Select --</option>';
            });

            orderSelect.addEventListener('change', function () {
                loadTaxonomy('families', this.value, familySelect);
                genusSelect.innerHTML = '<option value="">-- Select --</option>';
                speciesSelect.innerHTML = '<option value="">-- Select --</option>';
            });

            familySelect.addEventListener('change', function () {
                loadTaxonomy('genera', this.value, genusSelect);
                speciesSelect.innerHTML = '<option value="">-- Select --</option>';
            });

            genusSelect.addEventListener('change', function () {
                loadTaxonomy('species', this.value, speciesSelect);
            });
        });
    </script>
</body>
</html>