<?php
require '../includes/db.php';
require '../includes/auth.php';
require_once 'admin_header.php';

function countTable($table, $where = '') {
    global $pdo;
    $query = "SELECT COUNT(*) FROM $table";
    if ($where) $query .= " WHERE $where";
    return $pdo->query($query)->fetchColumn();
}

function fetchRecent($table, $limit = 5) {
    global $pdo;
    return $pdo->query("SELECT * FROM $table ORDER BY created_at DESC LIMIT $limit")->fetchAll(PDO::FETCH_ASSOC);
}

$total_users = countTable('users');
$total_animals = countTable('animals');
$total_posts = countTable('posts');
$pending_posts = countTable('posts', "status='pending'");
$total_quizzes = countTable('quizzes');
$total_events = countTable('events');
$total_gallery = countTable('gallery');
$total_forum_threads = countTable('forum_threads');
$total_badges = countTable('badges');
$total_knowledge = countTable('indigenous_knowledge');
$total_podcasts = countTable('podcasts');

$recent_users = fetchRecent('users');
$recent_animals = fetchRecent('animals');
$recent_posts = fetchRecent('posts');
$recent_events = fetchRecent('events');
$recent_threads = fetchRecent('forum_threads');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Admin Panel</title>
    <link rel="stylesheet" href="css/main.css">
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
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background-color: var(--color-primary-light);
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: transform 0.2s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card h3 {
            font-size: 1rem;
            color: var(--color-primary-mid);
            margin-bottom: 0.5rem;
        }
        
        .stat-card p {
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--color-primary-dark);
            margin: 0;
        }
        
        .stat-card small {
            font-size: 0.85rem;
            color: var(--color-primary-mid);
        }
        
        .recent-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        
        .recent-card {
            background-color: var(--color-primary-light);
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .recent-card h3 {
            font-size: 1.1rem;
            color: var(--color-primary-dark);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--color-neutral-mid);
        }
        
        .recent-card ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .recent-card li {
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--color-neutral-light);
            color: var(--color-primary-dark);
        }
        
        .recent-card li:last-child {
            border-bottom: none;
        }
        
        .status-pending {
            color: #f39c12;
        }
        
        @media (max-width: 768px) {
            .admin-content-container {
                margin-left: 0;
                padding: 1rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-content-container">
        <div class="page-header">
            <h1><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h1>
            <div class="last-updated">
                <span class="text-muted">Last updated: <?= date('M j, Y g:i a') ?></span>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3><i class="fas fa-users"></i> Users</h3>
                <p><?= $total_users ?></p>
            </div>
            <div class="stat-card">
                <h3><i class="fas fa-paw"></i> Animals</h3>
                <p><?= $total_animals ?></p>
            </div>
            <div class="stat-card">
                <h3><i class="fas fa-newspaper"></i> Posts</h3>
                <p><?= $total_posts ?> <small class="status-pending">(<?= $pending_posts ?> pending)</small></p>
            </div>
            <div class="stat-card">
                <h3><i class="fas fa-question-circle"></i> Quizzes</h3>
                <p><?= $total_quizzes ?></p>
            </div>
            <div class="stat-card">
                <h3><i class="fas fa-calendar-alt"></i> Events</h3>
                <p><?= $total_events ?></p>
            </div>
            <div class="stat-card">
                <h3><i class="fas fa-images"></i> Gallery Items</h3>
                <p><?= $total_gallery ?></p>
            </div>
            <div class="stat-card">
                <h3><i class="fas fa-comments"></i> Forum Threads</h3>
                <p><?= $total_forum_threads ?></p>
            </div>
            <div class="stat-card">
                <h3><i class="fas fa-award"></i> Badges</h3>
                <p><?= $total_badges ?></p>
            </div>
            <div class="stat-card">
                <h3><i class="fas fa-book"></i> Indigenous Knowledge</h3>
                <p><?= $total_knowledge ?></p>
            </div>
            <div class="stat-card">
                <h3><i class="fas fa-podcast"></i> Podcasts</h3>
                <p><?= $total_podcasts ?></p>
            </div>
        </div>

        <!-- Recent Activity -->
        <h2><i class="fas fa-clock"></i> Recent Activity</h2>
        <div class="recent-grid">
            <div class="recent-card">
                <h3><i class="fas fa-users"></i> Recent Users</h3>
                <ul>
                    <?php foreach ($recent_users as $user): ?>
                        <li><?= htmlspecialchars($user['name']) ?> <small>(<?= $user['email'] ?>)</small></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="recent-card">
                <h3><i class="fas fa-paw"></i> Recent Animal Submissions</h3>
                <ul>
                    <?php foreach ($recent_animals as $animal): ?>
                        <li><?= htmlspecialchars($animal['common_name']) ?> <small>(<?= $animal['status'] ?>)</small></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="recent-card">
                <h3><i class="fas fa-newspaper"></i> Recent Posts</h3>
                <ul>
                    <?php foreach ($recent_posts as $post): ?>
                        <li><?= htmlspecialchars($post['title']) ?> <small class="<?= $post['status'] === 'pending' ? 'status-pending' : '' ?>">(<?= $post['status'] ?>)</small></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="recent-card">
                <h3><i class="fas fa-calendar-alt"></i> Recent Events</h3>
                <ul>
                    <?php foreach ($recent_events as $event): ?>
                        <li><?= htmlspecialchars($event['title']) ?> <small>(<?= date('M j', strtotime($event['event_date'])) ?>)</small></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="recent-card">
                <h3><i class="fas fa-comments"></i> Recent Forum Threads</h3>
                <ul>
                    <?php foreach ($recent_threads as $thread): ?>
                        <li><?= htmlspecialchars($thread['title']) ?> <small>(<?= $thread['category'] ?>)</small></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>