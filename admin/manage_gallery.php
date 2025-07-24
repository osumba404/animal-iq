<?php
// admin/manage_gallery.php
require_once '../includes/db.php';
require_once 'admin_header.php';

$stmt = $pdo->query("SELECT * FROM gallery ORDER BY created_at DESC");
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<br><a href="dashboard.php">← Back to Dashboard</a>
<h1>Manage Gallery</h1>

<a href="upload_image.php">➕ Upload New Item</a>

<table border="1" cellpadding="10" cellspacing="0">
    <thead>
        <tr>
            <th>Preview</th>
            <th>Title</th>
            <th>Type</th>
            <th>Caption</th>
            <th>Submitted By</th>
            <th>Status</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td>
                    <?php if ($item['type'] === 'video'): ?>
                        🎥 Video
                    <?php else: ?>
                        <img src="../uploads/<?= htmlspecialchars($item['file_url']) ?>" width="100">
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($item['title']) ?></td>
                <td><?= htmlspecialchars($item['type']) ?></td>
                <td><?= htmlspecialchars($item['caption']) ?></td>
                <td><?= htmlspecialchars($item['submitted_by']) ?></td>
                <td><?= htmlspecialchars($item['status']) ?></td>
                <td><?= htmlspecialchars($item['created_at']) ?></td>
                <td>
                    <a href="edit_image.php?id=<?= $item['id'] ?>">✏️ Edit</a> |
                    <a href="delete_image.php?id=<?= $item['id'] ?>" onclick="return confirm('Delete this item?')">🗑️ Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

