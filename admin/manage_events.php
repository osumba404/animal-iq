<?php
require_once '../includes/db.php';
require_once 'admin_header.php';

$action = $_GET['action'] ?? null;
$eventId = $_GET['id'] ?? null;

// Handle deletion
if ($action === 'delete' && $eventId) {
    $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
    $stmt->execute([$eventId]);
    header("Location: event-manager.php");
    exit;
}

// Handle edit request
if ($action === 'edit' && $eventId) {
    $editStmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $editStmt->execute([$eventId]);
    $editEvent = $editStmt->fetch(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'];
        $type = $_POST['type'];
        $description = $_POST['description'];
        $event_date = $_POST['event_date'];
        $location = $_POST['location'];

        $updateStmt = $pdo->prepare("UPDATE events SET title=?, type=?, description=?, event_date=?, location=? WHERE id=?");
        $updateStmt->execute([$title, $type, $description, $event_date, $location, $eventId]);

        header("Location: manage_events.php");
        exit;
    }
}

// View signups
$signups = [];
if ($action === 'view_signups' && $eventId) {
    $signupStmt = $pdo->prepare("SELECT s.*, u.name FROM event_signups s JOIN users u ON s.user_id = u.id WHERE s.event_id = ?");
    $signupStmt->execute([$eventId]);
    $signups = $signupStmt->fetchAll(PDO::FETCH_ASSOC);
}

$stmt = $pdo->query("SELECT * FROM events ORDER BY event_date DESC");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<br><a href="dashboard.php">← Back to Dashboard</a>
<h1>Manage Events</h1>

<a href="add_event.php">➕ Add New Event</a><br><br>

<?php if ($action === 'edit' && $editEvent): ?>
    <h3>✏️ Edit Event</h3>
    <form method="post">
        <label>Title: <input type="text" name="title" value="<?= htmlspecialchars($editEvent['title']) ?>" required></label><br><br>
        <label>Type: <input type="text" name="type" value="<?= htmlspecialchars($editEvent['type']) ?>" required></label><br><br>
        <label>Description:<br>
            <textarea name="description" rows="5" cols="50" required><?= htmlspecialchars($editEvent['description']) ?></textarea>
        </label><br><br>
        <label>Date: <input type="date" name="event_date" value="<?= htmlspecialchars($editEvent['event_date']) ?>" required></label><br><br>
        <label>Location: <input type="text" name="location" value="<?= htmlspecialchars($editEvent['location']) ?>" required></label><br><br>
        <button type="submit">Update Event</button>
    </form>
    <hr>
<?php endif; ?>

<?php if ($action === 'view_signups' && $signups): ?>
    <h3>👥 Event Signups</h3>
    <table border="1" cellpadding="8">
        <tr><th>User Name</th><th>Signed Up At</th></tr>
        <?php foreach ($signups as $signup): ?>
            <tr>
                <td><?= htmlspecialchars($signup['name']) ?></td>
                <td><?= htmlspecialchars($signup['signed_up_at']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <br>
<?php elseif ($action === 'view_signups'): ?>
    <p>No signups found for this event.</p>
<?php endif; ?>

<h3>📅 All Events</h3>
<table border="1" cellpadding="10" cellspacing="0">
    <thead>
        <tr>
            <th>Title</th>
            <th>Type</th>
            <th>Description</th>
            <th>Date</th>
            <th>Location</th>
            <th>Created By</th>
            <th>Created At</th>
            <th>Signups</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($events): ?>
            <?php foreach ($events as $event): ?>
                <tr>
                    <td><?= htmlspecialchars($event['title']) ?></td>
                    <td><?= htmlspecialchars($event['type']) ?></td>
                    <td><?= htmlspecialchars($event['description']) ?></td>
                    <td><?= htmlspecialchars($event['event_date']) ?></td>
                    <td><?= htmlspecialchars($event['location']) ?></td>
                    <td><?= htmlspecialchars($event['created_by']) ?></td>
                    <td><?= htmlspecialchars($event['created_at']) ?></td>
                    <td>
                        <?php
                        $signupStmt = $pdo->prepare("SELECT COUNT(*) FROM event_signups WHERE event_id = ?");
                        $signupStmt->execute([$event['id']]);
                        echo $signupStmt->fetchColumn();
                        ?>
                        <a href="?action=view_signups&id=<?= $event['id'] ?>">👥 View</a>
                    </td>
                    <td>
                        <a href="?action=edit&id=<?= $event['id'] ?>">✏️ Edit</a> |
                        <a href="?action=delete&id=<?= $event['id'] ?>" onclick="return confirm('Delete this event?')">🗑️ Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="9">No events found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
