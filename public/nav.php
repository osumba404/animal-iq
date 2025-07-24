<!-- public/nav.php -->
<?php
require_once 'header.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session if not already started
}

// Simple login check: is user_id set in session?
$is_logged_in = isset($_SESSION['user_id']);
?>

<style>
nav ul {
    list-style-type: none;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    padding: 0;
}
nav ul li a {
    text-decoration: none;
    background: #2e8b57;
    color: white;
    padding: 6px 12px;
    border-radius: 5px;
}
nav ul li a:hover {
    background: #246b46;
}
</style>

<nav>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="encyclopedia.php">Encyclopedia</a></li>
        <li><a href="blog.php">Blog</a></li>
        <li><a href="quizzes.php">Quizzes</a></li>
        <li><a href="gallery.php">Gallery</a></li>
        <li><a href="learn.php">Learn</a></li>
        <li><a href="events.php">Events</a></li>
        <li><a href="forum.php">Forum</a></li>
        <li><a href="contribute.php">Contribute</a></li>
        <li><a href="support.php">Support</a></li>

        <?php if ($is_logged_in): ?>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="logout.php">Logout</a></li>
        <?php else: ?>
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>
        <?php endif; ?>
    </ul>
</nav>
