<?php
require_once '../includes/db.php';
require_once 'admin_header.php';

// Delete thread (and all associated posts)
if (isset($_GET['delete_thread'])) {
    $thread_id = intval($_GET['delete_thread']);
    $pdo->prepare("DELETE FROM forum_posts WHERE thread_id = ?")->execute([$thread_id]);
    $pdo->prepare("DELETE FROM forum_threads WHERE id = ?")->execute([$thread_id]);
    header("Location: manage_forum.php");
    exit;
}

// Delete individual post
if (isset($_GET['delete_post'])) {
    $post_id = intval($_GET['delete_post']);
    $pdo->prepare("DELETE FROM forum_posts WHERE id = ?")->execute([$post_id]);
    header("Location: manage_forum.php");
    exit;
}

// Fetch all threads with author names
$threads = $pdo->query("
    SELECT ft.*, u.name AS author_name 
    FROM forum_threads ft 
    JOIN users u ON ft.author_id = u.id 
    ORDER BY ft.last_activity_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Fetch all posts and group them by thread_id
$posts_stmt = $pdo->query("
    SELECT fp.*, u.name AS post_author 
    FROM forum_posts fp 
    JOIN users u ON fp.author_id = u.id 
    ORDER BY fp.created_at ASC
");

$posts_by_thread = [];
foreach ($posts_stmt as $post) {
    $posts_by_thread[$post['thread_id']][] = $post;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum Management - Admin Panel</title>
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
        
        .thread-container {
            border: 1px solid var(--color-neutral-mid);
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            background-color: var(--color-primary-light);
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .thread-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }
        
        .thread-title {
            margin: 0;
            color: var(--color-primary-dark);
            font-size: 1.25rem;
        }
        
        .thread-meta {
            color: var(--color-primary-mid);
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        
        .thread-meta span {
            margin-right: 1rem;
        }
        
        .thread-actions {
            margin-bottom: 1rem;
        }
        
        .btn-delete {
            background-color: #e74c3c;
            color: white;
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
        }
        
        .posts-container {
            margin-top: 1.5rem;
            padding-left: 1.5rem;
            border-left: 2px solid var(--color-neutral-mid);
        }
        
        .post {
            padding: 1rem;
            margin-bottom: 1rem;
            background-color: var(--color-neutral-light);
            border-radius: 6px;
        }
        
        .post-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }
        
        .post-author {
            font-weight: 600;
            color: var(--color-primary-dark);
        }
        
        .post-date {
            color: var(--color-primary-mid);
            font-size: 0.85rem;
        }
        
        .post-content {
            line-height: 1.6;
            color: var(--color-primary-dark);
        }
        
        .empty-state {
            text-align: center;
            padding: 2rem;
            color: var(--color-primary-mid);
        }
        
        @media (max-width: 768px) {
            .admin-content-container {
                margin-left: 0;
                padding: 1rem;
            }
            
            .thread-header {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .thread-meta span {
                display: block;
                margin-bottom: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="admin-content-container">
        <div class="page-header">
            <h1><i class="fas fa-comments"></i> Forum Management</h1>
            <a href="dashboard.php" class="btn btn-back">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <?php if (empty($threads)): ?>
            <div class="empty-state">
                <i class="fas fa-comments" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                <p>No forum threads found</p>
            </div>
        <?php else: ?>
            <?php foreach ($threads as $thread): ?>
                <div class="thread-container">
                    <div class="thread-header">
                        <div>
                            <h3 class="thread-title">
                                <?= htmlspecialchars($thread['title']) ?> 
                                <?= $thread['is_pinned'] ? '<i class="fas fa-thumbtack"></i>' : '' ?>
                            </h3>
                            <div class="thread-meta">
                                <span><strong>Category:</strong> <?= htmlspecialchars($thread['category']) ?></span>
                                <span><strong>Author:</strong> <?= htmlspecialchars($thread['author_name']) ?></span>
                                <span><strong>Created:</strong> <?= date('M j, Y', strtotime($thread['created_at'])) ?></span>
                                <span><strong>Last Activity:</strong> <?= date('M j, Y', strtotime($thread['last_activity_at'])) ?></span>
                                <span><strong>Views:</strong> <?= $thread['views'] ?></span>
                            </div>
                        </div>
                        <div class="thread-actions">
                            <a href="manage_forum.php?delete_thread=<?= $thread['id'] ?>" 
                               class="btn btn-delete"
                               onclick="return confirm('Are you sure you want to delete this thread and all its posts?')">
                                <i class="fas fa-trash"></i> Delete Thread
                            </a>
                        </div>
                    </div>

                    <div class="posts-container">
                        <h4><i class="fas fa-comment-dots"></i> Posts</h4>
                        <?php if (!empty($posts_by_thread[$thread['id']])): ?>
                            <?php foreach ($posts_by_thread[$thread['id']] as $post): ?>
                                <div class="post">
                                    <div class="post-header">
                                        <span class="post-author"><?= htmlspecialchars($post['post_author']) ?></span>
                                        <span class="post-date"><?= date('M j, Y g:i a', strtotime($post['created_at'])) ?></span>
                                    </div>
                                    <div class="post-content">
                                        <?= nl2br(htmlspecialchars($post['content'])) ?>
                                    </div>
                                    <div style="margin-top: 0.5rem; text-align: right;">
                                        <a href="manage_forum.php?delete_post=<?= $post['id'] ?>" 
                                           class="btn-delete"
                                           onclick="return confirm('Are you sure you want to delete this post?')">
                                            <i class="fas fa-trash"></i> Delete Post
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p style="color: var(--color-primary-mid);"><em>No posts yet in this thread.</em></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>