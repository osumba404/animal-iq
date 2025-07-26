<?php
// admin/manage_animals.php
require_once '../includes/db.php';
require_once 'admin_header.php';

// Handle search
$search = $_GET['search'] ?? '';

// Base SQL
$sql = "
    SELECT 
      a.id,
      a.common_name,
      a.scientific_name,
      a.main_photo,
      a.appearance,
      a.avg_weight_kg,
      a.avg_length_cm,
      a.created_at,
      ss.label AS species_status,

      -- Taxonomy
      k.name AS kingdom,
      p.name AS phylum,
      c.name AS class,
      o.name AS order_name,
      f.name AS family,
      g.name AS genus,
      s.name AS species,

      -- Geography
      geo.continent,
      geo.subcontinent,
      geo.country,
      geo.realm,
      geo.biome,

      -- Habits
      h.diet,
      h.mating_habits,
      h.behavior,
      h.habitat

  FROM animals a
  LEFT JOIN species_statuses ss ON a.species_status_id = ss.id
  LEFT JOIN taxonomy t ON a.id = t.animal_id
  LEFT JOIN species s ON t.species_id = s.id
  LEFT JOIN genera g ON s.genus_id = g.id
  LEFT JOIN families f ON g.family_id = f.id
  LEFT JOIN orders o ON f.order_id = o.id
  LEFT JOIN classes c ON o.class_id = c.id
  LEFT JOIN phyla p ON c.phylum_id = p.id
  LEFT JOIN kingdoms k ON p.kingdom_id = k.id
  LEFT JOIN animal_geography geo ON a.id = geo.animal_id
  LEFT JOIN animal_habits h ON a.id = h.animal_id
";

// Add search condition
$params = [];
if (!empty($search)) {
    $sql .= " WHERE 
        a.common_name LIKE :search OR
        a.scientific_name LIKE :search OR
        ss.label LIKE :search OR
        k.name LIKE :search OR
        p.name LIKE :search OR
        c.name LIKE :search OR
        o.name LIKE :search OR
        f.name LIKE :search OR
        g.name LIKE :search OR
        s.name LIKE :search
    ";
    $params[':search'] = '%' . $search . '%';
}

$sql .= " ORDER BY a.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$animals = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch animal photos
$photos = [];
if ($animals) {
    $animalIds = array_column($animals, 'id');
    $in  = str_repeat('?,', count($animalIds) - 1) . '?';
    $photoStmt = $pdo->prepare("SELECT * FROM animal_photos WHERE animal_id IN ($in)");
    $photoStmt->execute($animalIds);
    $photosRaw = $photoStmt->fetchAll(PDO::FETCH_ASSOC);

    // Group photos by animal_id
    foreach ($photosRaw as $photo) {
        $photos[$photo['animal_id']][] = $photo;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Animals - Admin Panel</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/tables.css">
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
        
        .btn-search {
            background-color: var(--color-primary-dark);
            color: var(--color-primary-light);
        }
        
        .btn-clear {
            background-color: var(--color-neutral-mid);
            color: var(--color-primary-dark);
        }
        
        .search-form {
            margin-bottom: 1.5rem;
        }
        
        .search-input-group {
            display: flex;
            gap: 0.5rem;
        }
        
        .search-input-group input {
            flex: 1;
            padding: 0.75rem;
            border: 1px solid var(--color-neutral-mid);
            border-radius: 4px;
        }
        
        .animal-thumbnail img {
            max-width: 60px;
            max-height: 60px;
            border-radius: 4px;
            object-fit: cover;
            border: 1px solid var(--color-neutral-mid);
            margin-top: 0.5rem;
        }
        
        .animal-photos {
            display: flex;
            gap: 0.5rem;
        }
        
        .animal-photos img {
            max-width: 40px;
            max-height: 40px;
            border-radius: 4px;
            object-fit: cover;
            border: 1px solid var(--color-neutral-mid);
        }
        
        .taxonomy {
            font-size: 0.85rem;
            color: var(--color-primary-mid);
        }
        
        .animal-size {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
            background-color: rgba(79, 93, 47, 0.1);
            color: var(--color-primary-accent);
        }
        
        @media (max-width: 768px) {
            .admin-content-container {
                margin-left: 0;
                padding: 1rem;
            }
            
            .search-input-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="admin-content-container">
        <div class="page-header">
            <h1><i class="fas fa-paw"></i> Manage Animals</h1>
            <div class="header-actions">
                <a href="dashboard.php" class="btn btn-back">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
                <a href="add_animal.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Animal
                </a>
            </div>
        </div>

        <form method="GET" class="search-form">
            <div class="search-input-group">
                <input type="text" name="search" placeholder="Search animals..." 
                       value="<?= htmlspecialchars($search) ?>">
                <button type="submit" class="btn btn-search">
                    <i class="fas fa-search"></i> Search
                </button>
                <?php if (!empty($search)): ?>
                    <a href="manage_animals.php" class="btn btn-clear">
                        <i class="fas fa-times"></i> Clear
                    </a>
                <?php endif; ?>
            </div>
        </form>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Common Name</th>
                        <th>Scientific Name</th>
                        <th>Image</th>
                        <th>Taxonomy</th>
                        <th>Status</th>
                        <th>Size</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($animals) > 0): ?>
                        <?php foreach ($animals as $animal): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($animal['common_name']) ?></strong>
                                    <?php if ($animal['main_photo']): ?>
                                        <div class="animal-thumbnail">
                                            <img src="../assets/images/animals/<?= htmlspecialchars($animal['main_photo']) ?>" 
                                                 alt="<?= htmlspecialchars($animal['common_name']) ?>">
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><em><?= htmlspecialchars($animal['scientific_name']) ?></em></td>
                                <td>
                                    <?php if (!empty($photos[$animal['id']])): ?>
                                        <div class="animal-photos">
                                            <?php foreach (array_slice($photos[$animal['id']], 0, 3) as $photo): ?>
                                                <img src="../assets/images/animals/<?= htmlspecialchars($photo['photo_url']) ?>" 
                                                     alt="<?= htmlspecialchars($photo['caption']) ?>">
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="taxonomy">
                                    <?= implode(' → ', array_filter([
                                        htmlspecialchars($animal['kingdom']),
                                        htmlspecialchars($animal['phylum']),
                                        htmlspecialchars($animal['class']),
                                        htmlspecialchars($animal['order_name']),
                                        htmlspecialchars($animal['family']),
                                        htmlspecialchars($animal['genus']),
                                        htmlspecialchars($animal['species'])
                                    ])) ?>
                                </td>
                                <td>
                                    <span class="status-badge"><?= htmlspecialchars($animal['species_status']) ?: '—' ?></span>
                                </td>
                                <td>
                                    <?php if ($animal['avg_weight_kg'] || $animal['avg_length_cm']): ?>
                                        <div class="animal-size">
                                            <?php if ($animal['avg_weight_kg']): ?>
                                                <span><?= htmlspecialchars($animal['avg_weight_kg']) ?> kg</span>
                                            <?php endif; ?>
                                            <?php if ($animal['avg_length_cm']): ?>
                                                <span><?= htmlspecialchars($animal['avg_length_cm']) ?> cm</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="edit_animal.php?id=<?= $animal['id'] ?>" class="btn-edit" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="delete_animal.php?id=<?= $animal['id'] ?>" 
                                           class="btn-delete" 
                                           title="Delete"
                                           onclick="return confirm('Are you sure you want to delete this animal?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <a href="animal_detail.php?id=<?= $animal['id'] ?>" class="btn-view" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="data-table-empty">
                                <i class="fas fa-paw"></i>
                                <p>No animals found <?= !empty($search) ? 'for "' . htmlspecialchars($search) . '"' : '' ?></p>
                                <a href="add_animal.php" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add New Animal
                                </a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>