<?php
require_once '../includes/db.php';
require_once '../includes/header.php';
require_once 'admin_header.php';

// Fetch all animals with taxonomy info (join with taxonomy table)
$stmt = $pdo->query("
    SELECT 
        animals.id,
        animals.common_name,
        animals.scientific_name,
        animals.main_photo,
        animals.created_at,
        taxonomy.kingdom, 
        taxonomy.phylum, 
        taxonomy.class
    FROM animals
    LEFT JOIN taxonomy ON animals.id = taxonomy.animal_id
    ORDER BY animals.created_at DESC
");
$animals = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<br><a href="dashboard.php">← Back to Dashboard</a>
<h1>Manage Animals</h1>
<p><a href="add_animal.php">+ Add New Animal</a></p>

<table border="2" cellpadding="10">
  <tr>
    <th>Name</th>
    <th>Scientific Name</th>
    <th>Image</th>
    <th>Category (Taxonomy)</th>
    <th>Added</th>
    <th>Actions</th>
  </tr>
  <?php foreach ($animals as $animal): ?>
  <tr>
    <td><?= htmlspecialchars($animal['common_name']) ?></td>
    <td><i><?= htmlspecialchars($animal['scientific_name']) ?></i></td>
    <td>
      <?php if ($animal['main_photo']): ?>
        <img src="../assets/images/animals/<?= htmlspecialchars($animal['main_photo']) ?>" width="80">
      <?php else: ?>
        <span>No Image</span>
      <?php endif; ?>
    </td>
    <td>
      <?= htmlspecialchars($animal['kingdom']) ?>, 
      <?= htmlspecialchars($animal['phylum']) ?>, 
      <?= htmlspecialchars($animal['class']) ?>
    </td>
    <td><?= $animal['created_at'] ?></td>
    <td>
      <a href="edit_animal.php?id=<?= $animal['id'] ?>">Edit</a> |
      <a href="delete_animal.php?id=<?= $animal['id'] ?>" onclick="return confirm('Delete this animal?')">Delete</a>
    </td>
  </tr>
  <?php endforeach; ?>
</table>

<br><a href="dashboard.php">← Back to Dashboard</a>
<?php require_once '../includes/footer.php'; ?>
