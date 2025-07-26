<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
// adminAuth();

$pageTitle = "Manage Taxonomy";
require_once 'admin_header.php';

function getTaxonomy($pdo, $table, $parent_id_column = null, $parent_id = null) {
    if ($parent_id_column && $parent_id !== null) {
        $stmt = $pdo->prepare("SELECT * FROM $table WHERE $parent_id_column = ? ORDER BY name");
        $stmt->execute([$parent_id]);
    } else {
        $stmt = $pdo->query("SELECT * FROM $table ORDER BY name");
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function createTaxonomyItem($pdo, $table, $name, $parent_column = null, $parent_id = null, $admin_id) {
    $sql = "INSERT INTO $table (name, created_by" . ($parent_column ? ", $parent_column" : "") . ") VALUES (?, ?" . ($parent_column ? ", ?" : "") . ")";
    $stmt = $pdo->prepare($sql);
    $params = [$name, $admin_id];
    if ($parent_column && $parent_id) $params[] = $parent_id;
    return $stmt->execute($params);
}

$taxonomy_levels = [
    'kingdoms' => [],
    'phyla' => ['parent' => 'kingdoms', 'column' => 'kingdom_id'],
    'classes' => ['parent' => 'phyla', 'column' => 'phylum_id'],
    'orders' => ['parent' => 'classes', 'column' => 'class_id'],
    'families' => ['parent' => 'orders', 'column' => 'order_id'],
    'genera' => ['parent' => 'families', 'column' => 'family_id'],
    'species' => ['parent' => 'genera', 'column' => 'genus_id'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $level = $_POST['level'];
    $name = trim($_POST['name']);
    $admin_id = $_SESSION['admin_id'];

    $parent_id = $_POST['parent_id'] ?? null;
    $parent_column = $taxonomy_levels[$level]['column'] ?? null;

    if ($name !== '') {
        createTaxonomyItem($pdo, $level, $name, $parent_column, $parent_id, $admin_id);
    }
    header("Location: manage_taxonomy.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Taxonomy - Admin Panel</title>
    <link rel="stylesheet" href="css/main.css">
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
        
        .taxonomy-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }
        
        .taxonomy-card {
            background-color: var(--color-primary-light);
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .taxonomy-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--color-primary-dark);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--color-neutral-mid);
            text-transform: capitalize;
        }
        
        .taxonomy-list {
            list-style: none;
            padding: 0;
            margin-bottom: 1.5rem;
            max-height: 200px;
            overflow-y: auto;
        }
        
        .taxonomy-list li {
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--color-neutral-light);
            color: var(--color-primary-dark);
        }
        
        .taxonomy-form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .form-group label {
            font-size: 0.9rem;
            color: var(--color-primary-mid);
        }
        
        .form-group select,
        .form-group input {
            padding: 0.75rem;
            border: 1px solid var(--color-neutral-mid);
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.25rem;
            border-radius: 4px;
            font-size: 0.95rem;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
            border: none;
        }
        
        .btn-primary {
            background-color: var(--color-primary-accent);
            color: var(--color-primary-light);
        }
        
        .btn-primary:hover {
            background-color: var(--color-secondary-accent);
        }
        
        @media (max-width: 768px) {
            .admin-content-container {
                margin-left: 0;
                padding: 1rem;
            }
            
            .taxonomy-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-content-container">
        <div class="page-header">
            <h1><i class="fas fa-sitemap"></i> Taxonomy Manager</h1>
            <a href="dashboard.php" class="btn btn-back">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <div class="taxonomy-grid">
            <?php foreach ($taxonomy_levels as $level => $info): ?>
                <div class="taxonomy-card">
                    <h3 class="taxonomy-title">
                        <i class="fas fa-layer-group"></i> <?= str_replace('_', ' ', $level) ?>
                    </h3>

                    <?php
                    $items = getTaxonomy($pdo, $level);
                    ?>

                    <ul class="taxonomy-list">
                        <?php foreach ($items as $item): ?>
                            <li><?= htmlspecialchars($item['name']) ?></li>
                        <?php endforeach; ?>
                    </ul>

                    <form method="POST" class="taxonomy-form">
                        <input type="hidden" name="level" value="<?= $level ?>">
                        <?php if (isset($info['parent'])): ?>
                            <div class="form-group">
                                <label for="parent_id_<?= $level ?>"><?= ucfirst(rtrim($info['parent'], 's')) ?></label>
                                <select name="parent_id" id="parent_id_<?= $level ?>" required>
                                    <option value="">-- Select <?= ucfirst(rtrim($info['parent'], 's')) ?> --</option>
                                    <?php foreach (getTaxonomy($pdo, $info['parent']) as $parent): ?>
                                        <option value="<?= $parent['id'] ?>"><?= htmlspecialchars($parent['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>
                        <div class="form-group">
                            <label for="name_<?= $level ?>">Add new <?= rtrim($level, 's') ?></label>
                            <input type="text" name="name" id="name_<?= $level ?>" placeholder="Enter name" required>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>