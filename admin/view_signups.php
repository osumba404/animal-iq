<!-- admin/view_signups.php -->
 <?php
require_once '../includes/db.php';

$event_id = $_GET['event_id'] ?? null;
if (!$event_id) {
    die("Event ID is required.");
}

$stmt = $pdo->prepare("SELECT title FROM events WHERE id = ?");
$stmt->execute([$event_id]);
$event_title = $stmt->fetchColumn();

$signups = [];
if ($event_title) {
    $signupStmt = $pdo->prepare("SELECT user_email, signed_up_at FROM event_signups WHERE event_id = ?");
    $signupStmt->execute([$event_id]);
    $signups = $signupStmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<h1>Signups for: <?= htmlspecialchars($event_title) ?></h1>

<?php if ($signups): ?>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>User Email</th>
                <th>Signed Up At</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($signups as $signup): ?>
                <tr>
                    <td><?= htmlspecialchars($signup['user_email']) ?></td>
                    <td><?= htmlspecialchars($signup['signed_up_at']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No one has signed up for this event yet.</p>
<?php endif; ?>

<p><a href="manage_events.php">🔙 Back to Events</a></p>
