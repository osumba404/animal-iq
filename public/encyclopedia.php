<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once 'header.php';
require_once 'nav.php';

$page_title = "Animal Encyclopedia";

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

<style>
/* Encyclopedia Premium Styles */
.encyclopedia-container {
    display: flex;
    gap: 2rem;
    max-width: var(--content-max-width);
    margin: 2rem auto;
    padding: 0 2rem;
}

/* Main Content */
.main-content {
    flex: 3;
}

/* Sidebar */
.sidebar {
    flex: 1;
}

/* Filter Form */
.filter-form {
    display: flex;
    gap: 1rem;
    align-items: flex-end;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.filter-group {
    display: flex;
    flex-direction: column;
    flex: 1;
    min-width: 0;
}

.filter-group label {
    margin-bottom: 0.5rem;
    font-weight: bold;
    color: var(--color-primary-dark);
    font-size: 0.9rem;
}

.filter-form select,
.filter-form input[type="text"] {
    width: 100%;
    padding: 0.8rem;
    border: 1px solid var(--color-border-light);
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.8);
    font-family: inherit;
}

.filter-form button {
    background: var(--color-accent-primary);
    color: var(--color-primary-dark);
    border: none;
    padding: 0.8rem 1.5rem;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    transition: all 0.3s ease;
    white-space: nowrap;
    height: fit-content;
}

.filter-form button:hover {
    background: var(--color-accent-secondary);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(232, 184, 36, 0.3);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .filter-form {
        flex-wrap: wrap;
    }
    
    .filter-group {
        min-width: calc(50% - 0.5rem);
    }
}

@media (max-width: 576px) {
    .filter-form {
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
    }
    
    .filter-group {
        min-width: 100%;
    }
}

/* Animal Cards */
.animal-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.animal-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.animal-card h2 {
    margin-top: 0;
    color: var(--color-primary-dark);
    font-size: 1.8rem;
}

.animal-card h2 a {
    color: inherit;
    text-decoration: none;
    transition: color 0.3s ease;
}

.animal-card h2 a:hover {
    color: var(--color-accent-primary);
}

.animal-card-content {
    display: flex;
    gap: 1.5rem;
}

.animal-card-image {
    flex: 1;
    max-width: 300px;
}

.animal-card-image img {
    width: 100%;
    height: auto;
    border-radius: 8px;
    object-fit: cover;
    aspect-ratio: 4/3;
}

.animal-card-details {
    flex: 2;
}

.detail-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

.detail-item {
    margin-bottom: 0.5rem;
}

.detail-label {
    font-weight: bold;
    color: var(--color-primary-dark);
}

.detail-value {
    color: var(--color-text-secondary);
}

.status-badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: bold;
}

.status-endangered {
    background-color: #ff6b6b;
    color: white;
}

.status-vulnerable {
    background-color: #ffb347;
    color: white;
}

.status-least-concern {
    background-color: #77dd77;
    color: white;
}

/* Sidebar */
.sidebar-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.sidebar-card h3 {
    margin-top: 0;
    color: var(--color-primary-dark);
    border-bottom: 2px solid var(--color-accent-primary);
    padding-bottom: 0.5rem;
}

.related-animal {
    padding: 0.5rem 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.related-animal:last-child {
    border-bottom: none;
}

.related-animal a {
    color: var(--color-text-primary);
    text-decoration: none;
    transition: color 0.3s ease;
}

.related-animal a:hover {
    color: var(--color-accent-primary);
}

/* Pagination */
.pagination {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    margin-top: 2rem;
}

.pagination a {
    display: inline-block;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    background: rgba(255, 255, 255, 0.1);
    color: var(--color-text-primary);
    text-decoration: none;
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.pagination a:hover {
    background: var(--color-accent-primary);
    color: var(--color-primary-dark);
    transform: translateY(-2px);
}

.pagination .current {
    background: var(--color-primary-dark);
    color: white;
    font-weight: bold;
}

/* Responsive */
@media (max-width: 992px) {
    .encyclopedia-container {
        flex-direction: column;
    }
    
    .animal-card-content {
        flex-direction: column;
    }
    
    .animal-card-image {
        max-width: 100%;
    }
}

@media (max-width: 576px) {
    .detail-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="encyclopedia-container">
  <!-- Main content -->
  <div class="main-content">
    <h1>Animals</h1>

    <!-- Filter & Search Form -->
  <form method="GET" class="filter-form">
      <div class="filter-group">
          <label for="category">Category</label>
          <select name="category" id="category">
              <option value="">All Categories</option>
              <?php foreach($categories as $cat): ?>
                  <option value="<?= $cat ?>" <?= $category === $cat ? 'selected' : '' ?>><?= $cat ?></option>
              <?php endforeach; ?>
          </select>
      </div>
      
      <div class="filter-group">
          <label for="search">Search</label>
          <input type="text" name="search" id="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search animals...">
      </div>
      
      <button type="submit">Apply Filters</button>
  </form>

    <!-- Animal List -->
    <?php if (empty($animals)): ?>
      <div class="animal-card">
        <p>No animals found matching your criteria.</p>
      </div>
    <?php else: ?>
      <?php foreach($animals as $a): ?>
        <div class="animal-card">
          <h2><a href="animal.php?id=<?= $a['id'] ?>"><?= htmlspecialchars($a['common_name']) ?></a></h2>
          <div class="animal-card-content">
            <div class="animal-card-image">
              <img src="../uploads/<?= $a['main_photo'] ?>" alt="<?= htmlspecialchars($a['common_name']) ?>">
            </div>
            <div class="animal-card-details">
              <div class="detail-grid">
                <div class="detail-item">
                  <span class="detail-label">Scientific Name:</span>
                  <span class="detail-value"><em><?= htmlspecialchars($a['scientific_name']) ?></em></span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Conservation Status:</span>
                  <span class="detail-value">
                    <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $a['species_status'])) ?>">
                      <?= $a['species_status'] ?>
                    </span>
                  </span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Population:</span>
                  <span class="detail-value"><?= $a['population_estimate'] ?></span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Average Weight:</span>
                  <span class="detail-value"><?= $a['avg_weight_kg'] ?> kg</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Family:</span>
                  <span class="detail-value"><?= $a['family'] ?></span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Genus:</span>
                  <span class="detail-value"><?= $a['genus'] ?></span>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
      <div class="pagination">
        <?php for($i = 1; $i <= $totalPages; $i++): ?>
          <a href="?page=<?= $i ?>&category=<?= urlencode($category) ?>&search=<?= urlencode($search) ?>"
             class="<?= $i === $page ? 'current' : '' ?>">
            <?= $i ?>
          </a>
        <?php endfor; ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- Sidebar -->
  <div class="sidebar">
    <div class="sidebar-card">
      <h3>Related Animals</h3>
      <?php
      if (!empty($animals)) {
          $fam = $animals[0]['family'];
          $gen = $animals[0]['genus'];

          $stmt = $pdo->prepare("
                SELECT animals.id, animals.common_name 
                FROM animals
                LEFT JOIN taxonomy ON animals.id = taxonomy.animal_id
                LEFT JOIN species ON taxonomy.species_id = species.id
                LEFT JOIN genera ON species.genus_id = genera.id
                LEFT JOIN families ON genera.family_id = families.id

                WHERE (families.name = :fam OR genera.name = :gen)
                  AND animals.status = 'approved'
                  AND animals.id != :current
                LIMIT 5
            ");

          $stmt->execute([
              ':fam' => $fam,
              ':gen' => $gen,
              ':current' => $animals[0]['id']
          ]);

          $related = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (!empty($related)) {
              foreach($related as $rel) {
                  echo '<div class="related-animal"><a href="animal.php?id='.$rel['id'].'">' . htmlspecialchars($rel['common_name']) . '</a></div>';
              }
          } else {
              echo '<p>No related animals found.</p>';
          }
      } else {
          echo '<p>No related animals to show.</p>';
      }
      ?>
    </div>
  </div>
</div>

<?php require_once 'footer.php'; ?>