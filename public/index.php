<?php
// index.php - Homepage
require_once '../includes/db.php';
require_once '../includes/functions.php';

require_once '../cron/rotate_daily_animal.php';
require_once 'header.php';
require_once 'nav.php';

$animal = getAnimalOfTheDay($pdo);
?>
<main>
  <h1>Welcome to Animal IQ</h1>
  <section>
    <?php if ($animal && isset($animal['name'], $animal['main_image'], $animal['short_description'])): ?>
      <h2>Animal of the Day: <?php echo htmlspecialchars($animal['name']); ?></h2>
      <img src="assets/images/animals/<?php echo $animal['main_image']; ?>" alt="<?php echo htmlspecialchars($animal['name']); ?>">
      <p><?php echo nl2br(htmlspecialchars($animal['short_description'])); ?></p>
    <?php else: ?>
      <h2>No Animal of the Day</h2>
      <p>Please check back later or contact an admin to configure animals.</p>
    <?php endif; ?>

    <?php require_once 'events.php'; ?>
  </section>
</main>

<?php require_once 'footer.php'; ?>