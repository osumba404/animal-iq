<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once 'header.php';
require_once 'nav.php';

$page_title = "Events";

$category = $_GET['category'] ?? null;
$search = trim($_GET['search'] ?? '');
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 5;
$offset = ($page - 1) * $limit;

// Fetch filtered + searched animals
$animals = getAllApprovedAnimals($pdo, $limit, $offset, $category, $search);

// Count total for pagination
$totalAnimals = count(getAllApprovedAnimals($pdo, PHP_INT_MAX, 0, $category, $search));
$totalPages = ceil($totalAnimals / $limit);

// Get available categories
$categories = ['Mammalia', 'Reptilia', 'Aves', 'Amphibia', 'Pisces', 'Insecta'];
?>

<div style="display: flex; gap: 20px;">
  <!-- Main content -->
  <div style="flex: 3;">
    <h1>Animal Encyclopedia</h1>

    <!-- Filter & Search Form -->
    <form method="GET" style="margin-bottom: 20px;">
      <label>Category:</label>
      <select name="category">
        <option value="">-- All --</option>
        <?php foreach($categories as $cat): ?>
          <option value="<?= $cat ?>" <?= $category === $cat ? 'selected' : '' ?>><?= $cat ?></option>
        <?php endforeach; ?>
      </select>

      <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search name..." />
      <button type="submit">Apply</button>
    </form>

    <!-- Animal List -->
    <?php foreach($animals as $a): ?>
      <div style="border-bottom: 1px solid #ccc; margin-bottom: 15px;">
        <h2><a href="animal.php?id=<?= $a['id'] ?>"><?= htmlspecialchars($a['common_name']) ?></a></h2>
        <p><strong>Scientific:</strong> <?= htmlspecialchars($a['scientific_name']) ?></p>
        <img src="../uploads/<?= $a['main_photo'] ?>" alt="" style="max-width: 200px;">
        <p><strong>Status:</strong> <?= $a['species_status'] ?></p>
        <p><strong>Population:</strong> <?= $a['population_estimate'] ?></p>
        <p><strong>Avg Weight:</strong> <?= $a['avg_weight_kg'] ?> kg</p>
        <p><strong>Family:</strong> <?= $a['family'] ?> | <strong>Genus:</strong> <?= $a['genus'] ?></p>
      </div>
    <?php endforeach; ?>

    <!-- Pagination -->
    <div>
      <?php for($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?= $i ?>&category=<?= urlencode($category) ?>&search=<?= urlencode($search) ?>"
           style="margin-right: 10px;<?= $i === $page ? 'font-weight: bold;' : '' ?>">
          <?= $i ?>
        </a>
      <?php endfor; ?>
    </div>
  </div>

  <!-- Sidebar -->
  <div style="flex: 1;">
    <h3>Related Animals</h3>
    <?php
    if (!empty($animals)) {
        $fam = $animals[0]['family'];
        $gen = $animals[0]['genus'];

        $stmt = $pdo->prepare("SELECT animals.id, animals.common_name 
                               FROM animals 
                               LEFT JOIN taxonomy ON animals.id = taxonomy.animal_id
                               WHERE (taxonomy.family = :fam OR taxonomy.genus = :gen)
                                 AND animals.status = 'approved'
                                 AND animals.id != :current
                               LIMIT 5");
        $stmt->execute([
            ':fam' => $fam,
            ':gen' => $gen,
            ':current' => $animals[0]['id']
        ]);
        $related = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach($related as $rel) {
            echo "<p><a href='animal.php?id={$rel['id']}'>" . htmlspecialchars($rel['common_name']) . "</a></p>";
        }
    }
    ?>
  </div>
</div>

<?php require_once 'footer.php'; ?>
