<?php
// index.php - Homepage
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';
require_once '../cron/rotate_daily_animal.php';
require_once 'nav.php';

$animal = getAnimalOfTheDay($pdo);
?>
<main>
  <h1>Welcome to Animal IQ</h1>
  <section>
    <h2>Animal of the Day: <?php echo htmlspecialchars($animal['name']); ?></h2>
    <img src="assets/images/animals/<?php echo $animal['main_image']; ?>" alt="<?php echo htmlspecialchars($animal['name']); ?>">
    <p><?php echo nl2br(htmlspecialchars($animal['short_description'])); ?></p>
  </section>
</main>
<?php require_once '../includes/footer.php'; ?>