
<?php
// event.php - Single Event Details

require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once 'header.php';
require_once 'nav.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p>Invalid event ID.</p>";
    require_once 'footer.php';
    exit;
}

$event_id = intval($_GET['id']);
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$event_id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    echo "<p>Event not found.</p>";
    require_once 'footer.php';
    exit;
}
?>

<main style="max-width:800px;margin:2rem auto;padding:2rem;background:#fff;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.08);">
    <h1><?= htmlspecialchars($event['title']) ?></h1>
    <p><strong>Date:</strong> <?= date('F j, Y', strtotime($event['event_date'])) ?> at <?= date('g:i A', strtotime($event['event_date'])) ?></p>
    <p><strong>Location:</strong> <?= htmlspecialchars($event['location']) ?></p>
    <hr>
    <div>
        <?= nl2br(htmlspecialchars($event['description'])) ?>
    </div>
    <a href="events.php" style="display:inline-block;margin-top:2rem;color:#e8b824;text-decoration:none;font-weight:600;">
        &larr; Back to Events
    </a>
</main>