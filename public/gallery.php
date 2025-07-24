<?php
// gallery.php - Multimedia Gallery
require_once '../includes/db.php';
require_once 'header.php';
require_once 'nav.php';
$media = getGalleryMedia($conn);
?>
<h1>Gallery</h1>
<div class="gallery">
<?php foreach($media as $item): ?>
  <img src="assets/images/gallery/<?php echo $item['file']; ?>" alt="Gallery item">
<?php endforeach; ?>
</div>
<?php require_once 'footer.php'; ?>