<!-- admin/event-manager.php -->
<?php
require_once '../includes/db.php';

$stmt = $pdo->query("SELECT * FROM events ORDER BY event_date DESC");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<br><a href="dashboard.php">← Back to Dashboard</a>
<h1>Manage Events</h1>

<a href="add_event.php">➕ Add New Event</a>

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
                        <a href="view_signups.php?event_id=<?= $event['id'] ?>">👥 View</a>
                    </td>
                    <td>
                        <a href="edit_event.php?id=<?= $event['id'] ?>">✏️ Edit</a> |
                        <a href="delete_event.php?id=<?= $event['id'] ?>" onclick="return confirm('Delete this event?')">🗑️ Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="9">No events found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>


