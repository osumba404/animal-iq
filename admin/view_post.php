<?php
require_once '../includes/db.php';
require_once 'admin_header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage_posts.php?error=Invalid+Post+ID");
    exit;
}

$postId = (int)$_GET['id'];

// Fetch post details
$stmt = $pdo->prepare("
    SELECT posts.*, users.name AS author 
    FROM posts 
    JOIN users ON posts.author_id = users.id 
    WHERE posts.id = ?
");
$stmt->execute([$postId]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    header("Location: manage_posts.php?error=Post+not+found");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Post - Admin Panel</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/tables.css">
    <style>
        .admin-content-container {
            margin-left: var(--sidebar-width);
            padding: 2rem;
        }
        .post-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .post-image {
            margin-top: 20px;
        }
        .post-image img {
            max-width: 100%;
            border-radius: 4px;
        }
        .post-meta {
            margin: 1rem 0;
            font-size: 0.9rem;
            color: #555;
        }
        .post-meta span {
            margin-right: 1rem;
        }
        .post-content {
            margin-top: 1rem;
            line-height: 1.6;
        }
        .status {
            padding: 0.3rem 0.7rem;
            border-radius: 4px;
            font-size: 0.8rem;
        }
        .status.pending {
            background-color: rgba(52, 152, 219, 0.1);
            color: #3498db;
        }
        .status.approved {
            background-color: rgba(46, 204, 113, 0.1);
            color: #2ecc71;
        }
        .status.rejected {
            background-color: rgba(243, 156, 18, 0.1);
            color: #f39c12;
        }
    </style>
</head>
<body>
<div class="admin-content-container">
    <div class="post-header">
        <h1><?= htmlspecialchars($post['title']) ?></h1>
        <a href="manage_posts.php" class="btn btn-back"><i class="fas fa-arrow-left"></i> Back</a>
    </div>

    <div class="post-meta">
        <span><strong>Author:</strong> <?= htmlspecialchars($post['author']) ?></span>
        <span><strong>Type:</strong> <?= htmlspecialchars($post['type'] ?? '-') ?></span>
        <span><strong>Region:</strong> <?= htmlspecialchars($post['region'] ?? '-') ?></span>
        <span><strong>Date:</strong> <?= date('F j, Y', strtotime($post['created_at'])) ?></span>
        <span class="status <?= $post['status'] ?>"><?= ucfirst($post['status']) ?></span>
    </div>

    <?php if (!empty($post['featured_image'])): ?>
        <div class="post-image">
            <img src="../uploads/posts/<?= htmlspecialchars($post['featured_image']) ?>" alt="Featured Image">
        </div>
    <?php endif; ?>

    <div class="post-content">
        <?= nl2br(htmlspecialchars($post['body'])) ?>
    </div>
</div>
</body>
</html>
