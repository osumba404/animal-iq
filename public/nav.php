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
        <?php if (function_exists('is_logged_in') && is_logged_in()): ?>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="logout.php">Logout</a></li>
        <?php else: ?>
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>
        <?php endif; ?>
    </ul>
</nav>
