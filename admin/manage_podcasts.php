<!-- admin/manage_podcasts.php -->

<?php
require_once '../includes/db.php';
require_once 'admin_header.php';

// Delete a podcast
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $pdo->prepare("DELETE FROM podcasts WHERE id = ?")->execute([$id]);
    header("Location: manage_podcasts.php");
    exit;
}

// Fetch all podcasts with contributor names
$podcasts = $pdo->query("
    SELECT p.*, u.name AS contributor_name 
    FROM podcasts p 
    JOIN users u ON p.contributor_id = u.id 
    ORDER BY p.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<p><a href="dashboard.php">🔙 Back to Dashboard</a></p>
<h1>🎙️ Manage Podcasts</h1>

<?php if (empty($podcasts)): ?>
    <p>No podcasts uploaded yet.</p>
<?php else: ?>
    <table border="1" cellpadding="10" cellspacing="0" style="width:100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th>#</th>
                <th>Cover</th>
                <th>Title</th>
                <th>Contributor</th>
                <th>Tags</th>
                <th>Duration</th>
                <th>Audio</th>
                <th>Description</th>
                <th>Uploaded</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($podcasts as $index => $p): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td>
                        <?php if ($p['cover_image_url']): ?>
                            <img src="<?= htmlspecialchars($p['cover_image_url']) ?>" alt="Cover" width="60">
                        <?php else: ?>
                            <span>No Image</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($p['title']) ?></td>
                    <td><?= htmlspecialchars($p['contributor_name']) ?></td>
                    <td><?= htmlspecialchars($p['tags']) ?></td>
                    <td><?= floor($p['duration_seconds'] / 60) ?> min <?= $p['duration_seconds'] % 60 ?> sec</td>
                    <td>
                        <audio controls style="width:150px;">
                            <source src="<?= htmlspecialchars($p['file_url']) ?>" type="audio/mpeg">
                            Your browser does not support the audio element.
                        </audio>
                    </td>
                    <td><?= nl2br(htmlspecialchars($p['description'])) ?></td>
                    <td><?= $p['created_at'] ?></td>
                    <td>
                        <a href="manage_podcasts.php?delete=<?= $p['id'] ?>" onclick="return confirm('Delete this podcast?')">🗑️ Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>


