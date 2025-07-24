<?php
// encyclopedia.php - Animal Encyclopedia
require_once '../includes/db.php';
require_once '../includes/header.php';
require_once 'nav.php';
$animals = getAllApprovedAnimals($conn);
?>
<h1>Animal Encyclopedia</h1>
<ul>
<?php foreach($animals as $a): ?>
  <li><a href="animal.php?id=<?php echo $a['id']; ?>"><?php echo htmlspecialchars($a['name']); ?></a></li>
<?php endforeach; ?>
</ul>
<?php require_once '../includes/footer.php'; ?>