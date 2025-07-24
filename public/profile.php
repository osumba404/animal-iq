<?php
// profile.php
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/header.php';
require_once 'nav.php';
$user = getUserProfile($conn, $_SESSION['email']);
?>
<h1>Your Profile</h1>
<p>Name: <?php echo htmlspecialchars($user['name']); ?></p>
<p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
<p>Joined: <?php echo htmlspecialchars($user['created_at']); ?></p>
<?php require_once '../includes/footer.php'; ?>