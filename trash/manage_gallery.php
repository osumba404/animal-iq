<?php
// admin/manage_gallery.php
require_once '../includes/db.php';
require_once 'admin_header.php';

$stmt = $pdo->query("SELECT * FROM gallery ORDER BY created_at DESC");
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Gallery - Admin Panel</title>
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
        
        .status-published {
            color: #2ecc71;
            background-color: rgba(46, 204, 113, 0.1);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
        }
        
        .status-pending {
            color: #3498db;
            background-color: rgba(52, 152, 219, 0.1);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
        }
        
        .status-rejected {
            color: #e74c3c;
            background-color: rgba(231, 76, 60, 0.1);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
        }
        
        .media-preview {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .media-preview img {
            max-width: 60px;
            max-height: 60px;
            border-radius: 4px;
            object-fit: cover;
            border: 1px solid var(--color-neutral-mid);
        }
        
        .media-preview.video {
            color: var(--color-primary-accent);
            font-weight: 500;
        }
        
        @media (max-width: 768px) {
            .admin-content-container {
                margin-left: 0;
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="admin-content-container">
        <div class="page-header">
            <h1><i class="fas fa-images"></i> Manage Gallery</h1>
            <div class="header-actions">
                <a href="dashboard.php" class="btn btn-back">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
                <a href="upload_image.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Upload New Item
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="data-table">
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
                    <?php if (count($items) > 0): ?>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td>
                                    <div class="media-preview <?= $item['type'] === 'video' ? 'video' : '' ?>">
                                        <?php if ($item['type'] === 'video'): ?>
                                            <i class="fas fa-video"></i> Video
                                        <?php else: ?>
                                            <img src="../uploads/<?= htmlspecialchars($item['file_url']) ?>" 
                                                 alt="<?= htmlspecialchars($item['title']) ?>">
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($item['title']) ?></td>
                                <td><?= ucfirst(htmlspecialchars($item['type'])) ?></td>
                                <td><?= htmlspecialchars($item['caption']) ?></td>
                                <td><?= htmlspecialchars($item['submitted_by']) ?></td>
                                <td>
                                    <?php if ($item['status'] == 'published'): ?>
                                        <span class="status-published">Published</span>
                                    <?php elseif ($item['status'] == 'pending'): ?>
                                        <span class="status-pending">Pending</span>
                                    <?php else: ?>
                                        <span class="status-rejected">Rejected</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('M j, Y', strtotime($item['created_at'])) ?></td>
                                <td>
                                    <div class="table-actions">
                                        <a href="edit_image.php?id=<?= $item['id'] ?>" class="btn-edit" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="delete_image.php?id=<?= $item['id'] ?>" 
                                           class="btn-delete" 
                                           title="Delete"
                                           onclick="return confirm('Are you sure you want to delete this item?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <?php if ($item['status'] == 'published'): ?>
                                        <a href="../gallery/view.php?id=<?= $item['id'] ?>" class="btn-view" title="View" target="_blank">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="data-table-empty">
                                <i class="fas fa-images"></i>
                                <p>No gallery items found</p>
                                <a href="upload_image.php" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Upload New Item
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