<!-- admin/dashboard.php -->
<?php
require '../includes/db.php';
require '../includes/auth.php';

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

<h2>Admin Dashboard</h2>
<p>Total Users: <?= $total_users ?></p>
<p>Total Animals: <?= $total_animals ?></p>
<p>Total Posts: <?= $total_posts ?> (<?= $pending_posts ?> pending)</p>
<p>Total Quizzes: <?= $total_quizzes ?></p>
<p>Total Events: <?= $total_events ?></p>
<p>Total Gallery Items: <?= $total_gallery ?></p>
<p>Total Forum Threads: <?= $total_forum_threads ?></p>
<p>Total Badges: <?= $total_badges ?></p>
<p>Total Indigenous Knowledge: <?= $total_knowledge ?></p>
<p>Total Podcasts: <?= $total_podcasts ?></p>

<h3>Recent Users</h3>
<ul>
    <?php foreach ($recent_users as $user): ?>
        <li><?= htmlspecialchars($user['name']) ?> (<?= $user['email'] ?>)</li>
    <?php endforeach; ?>
</ul>

<h3>Recent Animal Submissions</h3>
<ul>
    <?php foreach ($recent_animals as $animal): ?>
        <li><?= htmlspecialchars($animal['common_name']) ?> (<?= $animal['status'] ?>)</li>
    <?php endforeach; ?>
</ul>

<h3>Recent Posts</h3>
<ul>
    <?php foreach ($recent_posts as $post): ?>
        <li><?= htmlspecialchars($post['title']) ?> (<?= $post['status'] ?>)</li>
    <?php endforeach; ?>
</ul>

<h3>Recent Events</h3>
<ul>
    <?php foreach ($recent_events as $event): ?>
        <li><?= htmlspecialchars($event['title']) ?> - <?= $event['event_date'] ?></li>
    <?php endforeach; ?>
</ul>

<h3>Recent Forum Threads</h3>
<ul>
    <?php foreach ($recent_threads as $thread): ?>
        <li><?= htmlspecialchars($thread['title']) ?> - <?= $thread['category'] ?></li>
    <?php endforeach; ?>
</ul>

<p><a href="manage-users.php">Manage Users</a></p>
<p><a href="manage_animals.php">Manage Animals</a></p>
<p><a href="manage_posts.php">Manage Posts</a></p>
<p><a href="manage_quizzes.php">Manage Quizzes</a></p>
<p><a href="manage_gallery.php">Manage Gallery</a></p>
<p><a href="manage_events.php">Manage Events</a></p>
<p><a href="manage_forum.php">Manage Forum</a></p>
<!-- <p><a href="manage_knowledge.php">Manage Indigenous Knowledge</a></p> -->
<p><a href="manage_podcasts.php">Manage Podcasts</a></p>
<p><a href="manage_badges.php">Manage Badges</a></p>
<p><a href="site_settings.php">Site Settings</a></p>
<p><a href="logs.php">View Logs</a></p>
