<?php
// events.php - Events Calendar
require_once '../includes/db.php';
require_once '../includes/header.php';
require_once 'nav.php';
$events = getUpcomingEvents($conn);
?>
<h1>Events</h1>
<ul>
<?php foreach($events as $e): ?>
  <li><?php echo htmlspecialchars($e['title']) . ' on ' . $e['event_date']; ?></li>
<?php endforeach; ?>
</ul>
<?php require_once '../includes/footer.php'; ?>