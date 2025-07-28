<?php
require_once '../includes/db.php';
require_once 'admin_header.php';

// Handle deletion if requested via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_post_id'])) {
    $deleteId = $_POST['delete_post_id'];

    // Fetch featured image to delete file (optional)
    $stmt = $pdo->prepare("SELECT featured_image FROM posts WHERE id = ?");
    $stmt->execute([$deleteId]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($post && !empty($post['featured_image'])) {
        $imagePath = '../uploads/' . $post['featured_image'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    // Delete the post
    $delStmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
    $delStmt->execute([$deleteId]);

}



// Handle status change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status_post_id'], $_POST['new_status'])) {
    $statusPostId = intval($_POST['status_post_id']);
    $newStatus = $_POST['new_status'];

    // Validate status value
    $validStatuses = ['pending', 'approved', 'rejected'];
    if (in_array($newStatus, $validStatuses)) {
        $stmt = $pdo->prepare("UPDATE posts SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $statusPostId]);
        header("Location: manage_posts.php?success=Status+updated");
        exit;
    }
}


// Fetch posts
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
        .post-thumbnail img {
            margin-top: 5px;
            border-radius: 4px;
        }
        .table-actions form {
            display: inline;
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
                <th>Type</th>
                <th>Region</th>
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
                                    <img src="../uploads/posts/<?= htmlspecialchars($post['featured_image']) ?>" 
                                         alt="<?= htmlspecialchars($post['title']) ?>" width="60">
                                </div>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($post['author']) ?></td>
                        <td><?= htmlspecialchars($post['type'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($post['region'] ?? '-') ?></td>

                        <td><?= date('M j, Y', strtotime($post['created_at'])) ?></td>
                        <td>
                            <form method="POST" style="display:inline-flex; gap:4px;">
                                <input type="hidden" name="status_post_id" value="<?= $post['id'] ?>">
                                <select name="new_status" onchange="this.form.submit()" style="font-size: 0.8rem;">
                                    <option value="pending" <?= $post['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="approved" <?= $post['status'] === 'approved' ? 'selected' : '' ?>>Approved</option>
                                    <option value="rejected" <?= $post['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                </select>
                            </form>
                        </td>

                        <td>
                            <div class="table-actions">
                                <a href="edit_post.php?id=<?= $post['id'] ?>" class="btn-edit" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" onsubmit="return confirm('Are you sure you want to delete this post?');">
                                    <input type="hidden" name="delete_post_id" value="<?= $post['id'] ?>">
                                    <button type="submit" class="btn-delete" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                <a href="view_post.php?id=<?= $post['id'] ?>" class="btn-view" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="data-table-empty">
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
