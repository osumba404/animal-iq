<?php
// gallery.php - Multimedia Gallery
require_once '../includes/db.php';
require_once 'header.php';
require_once 'nav.php';

// Fetch both main_photo and extra photos, map them to animals
$sql = "
    SELECT a.id AS animal_id, a.common_name, a.main_photo AS photo, 'main' AS type, NULL AS caption
    FROM animals a
    WHERE a.main_photo IS NOT NULL AND a.main_photo != ''

    UNION ALL

    SELECT a.id AS animal_id, a.common_name, ap.photo_url AS photo, 'extra' AS type, ap.caption
    FROM animal_photos ap
    JOIN animals a ON ap.animal_id = a.id
    WHERE ap.photo_url IS NOT NULL AND ap.photo_url != ''

    ORDER BY animal_id DESC, type ASC
";
$stmt = $pdo->query($sql);
$media = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Gallery</h1>

<style>
/* Pinterest-like masonry grid */
.gallery {
  column-count: 4;
  column-gap: 15px;
  padding: 10px;
}

.gallery-item {
  break-inside: avoid;
  margin-bottom: 15px;
  display: block;
  overflow: hidden;
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.15);
  transition: transform 0.3s ease;
  background: #fff;
}

.gallery-item img {
  width: 100%;
  display: block;
  border-radius: 10px;
}

.gallery-item:hover {
  transform: scale(1.03);
}

.gallery-caption {
  padding: 8px 10px;
  font-size: 14px;
  color: #555;
  text-align: center;
}

/* Responsive columns */
@media (max-width: 1200px) {
  .gallery { column-count: 3; }
}
@media (max-width: 768px) {
  .gallery { column-count: 2; }
}
@media (max-width: 480px) {
  .gallery { column-count: 1; }
}
</style>

<div class="gallery">
<?php foreach ($media as $item): ?>
  <div class="gallery-item">
    <a href="../uploads/animals/<?php echo htmlspecialchars($item['photo']); ?>" target="_blank" 
       title="<?php echo htmlspecialchars($item['common_name']); ?> (<?php echo $item['type'] === 'main' ? 'Main Photo' : 'Extra Photo'; ?>)">
      <img src="../uploads/animals/<?php echo htmlspecialchars($item['photo']); ?>" 
           alt="<?php echo htmlspecialchars($item['common_name']); ?>">
    </a>
    <div class="gallery-caption">
      <?php echo htmlspecialchars($item['common_name']); ?>
      <?php if (!empty($item['caption'])): ?>
        - <?php echo htmlspecialchars($item['caption']); ?>
      <?php endif; ?>
    </div>
  </div>
<?php endforeach; ?>
</div>

<?php require_once 'footer.php'; ?>
