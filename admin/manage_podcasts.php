<?php
// admin/manage_podcasts.php
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Podcasts - Admin Panel</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/tables.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-content-container {
            margin-left: var(--sidebar-width);
            padding: 2rem;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            border-radius: 4px;
            font-size: 0.95rem;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        
        .btn-back {
            background-color: var(--color-neutral-mid);
            color: var(--color-primary-dark);
        }
        
        .btn-back:hover {
            background-color: var(--color-primary-mid);
        }
        
        .btn-primary {
            background-color: var(--color-primary-accent);
            color: var(--color-primary-light);
        }
        
        .btn-primary:hover {
            background-color: var(--color-secondary-accent);
        }
        
        .podcast-cover {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid var(--color-neutral-mid);
        }
        
        .audio-player {
            width: 150px;
            height: 40px;
        }
        
        .description-cell {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .description-cell:hover {
            white-space: normal;
            overflow: visible;
        }
        
        @media (max-width: 768px) {
            .admin-content-container {
                margin-left: 0;
                padding: 1rem;
            }
            
            .data-table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <div class="admin-content-container">
        <div class="page-header">
            <h1><i class="fas fa-podcast"></i> Manage Podcasts</h1>
            <div class="header-actions">
                <a href="dashboard.php" class="btn btn-back">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
                <a href="add_podcast.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Podcast
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="data-table">
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
                    <?php if (count($podcasts) > 0): ?>
                        <?php foreach ($podcasts as $index => $p): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td>
                                    <?php if ($p['cover_image_url']): ?>
                                        <img src="<?= htmlspecialchars($p['cover_image_url']) ?>" 
                                             alt="Podcast Cover" 
                                             class="podcast-cover">
                                    <?php else: ?>
                                        <span class="light-text">No Image</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($p['title']) ?></td>
                                <td><?= htmlspecialchars($p['contributor_name']) ?></td>
                                <td><?= htmlspecialchars($p['tags']) ?></td>
                                <td>
                                    <?= floor($p['duration_seconds'] / 60) ?> min <?= $p['duration_seconds'] % 60 ?> sec
                                </td>
                                <td>
                                    <audio controls class="audio-player">
                                        <source src="<?= htmlspecialchars($p['file_url']) ?>" type="audio/mpeg">
                                        Your browser does not support the audio element.
                                    </audio>
                                </td>
                                <td class="description-cell" title="<?= htmlspecialchars($p['description']) ?>">
                                    <?= nl2br(htmlspecialchars($p['description'])) ?>
                                </td>
                                <td><?= date('M j, Y', strtotime($p['created_at'])) ?></td>
                                <td>
                                    <div class="table-actions">
                                        <a href="edit_podcast.php?id=<?= $p['id'] ?>" class="btn-edit" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="manage_podcasts.php?delete=<?= $p['id'] ?>" 
                                           class="btn-delete" 
                                           title="Delete"
                                           onclick="return confirm('Are you sure you want to delete this podcast?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <a href="../podcast.php?id=<?= $p['id'] ?>" class="btn-view" title="View" target="_blank">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="data-table-empty">
                                <i class="fas fa-podcast"></i>
                                <p>No podcasts uploaded yet</p>
                                <a href="add_podcast.php" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add New Podcast
                                </a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>