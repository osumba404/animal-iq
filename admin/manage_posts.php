<!-- admin/manage_posts.php -->

<?php
require_once '../includes/db.php';
require_once 'admin_header.php';


$stmt = $pdo->query("
  SELECT posts.*, users.name AS author 
  FROM posts 
  JOIN users ON posts.author_id = users.id 
  ORDER BY posts.created_at DESC
");


$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Posts - Admin Panel</title>
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
        
        .status-draft {
            color: #f39c12;
            background-color: rgba(243, 156, 18, 0.1);
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
            <h1><i class="fas fa-newspaper"></i> Manage Posts</h1>
            <div class="header-actions">
                <a href="dashboard.php" class="btn btn-back">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
                <a href="add_post.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Post
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Published On</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($posts) > 0): ?>
                        <?php foreach ($posts as $post): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($post['title']) ?></strong>
                                    <?php if (!empty($post['featured_image'])): ?>
                                        <div class="post-thumbnail">
                                            <img src="../assets/images/posts/<?= htmlspecialchars($post['featured_image']) ?>" 
                                                 alt="<?= htmlspecialchars($post['title']) ?>" width="60">
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($post['author']) ?></td>
                                <td><?= htmlspecialchars($post['category_name'] ?? 'Uncategorized') ?></td>
                                <td><?= date('M j, Y', strtotime($post['created_at'])) ?></td>
                                <td>
                                    <?php if ($post['status'] == 'published'): ?>
                                        <span class="status-published">Published</span>
                                    <?php elseif ($post['status'] == 'draft'): ?>
                                        <span class="status-draft">Draft</span>
                                    <?php else: ?>
                                        <span class="status-pending">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="edit_post.php?id=<?= $post['id'] ?>" class="btn-edit" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="delete_post.php?id=<?= $post['id'] ?>" 
                                           class="btn-delete" 
                                           title="Delete"
                                           onclick="return confirm('Are you sure you want to delete this post?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <a href="../post.php?id=<?= $post['id'] ?>" class="btn-view" title="View" target="_blank">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="data-table-empty">
                                <i class="fas fa-newspaper"></i>
                                <p>No posts found</p>
                                <a href="add_post.php" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add New Post
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