
<?php
require_once '../includes/header.php';
require_once 'nav.php';

$email = $_GET['email'] ?? '';
$password = $_GET['password'] ?? '';
?>

<h1>Login</h1>
<form action="../api/login.php" method="post">
  <input type="email" name="email" required placeholder="Email" value="<?= htmlspecialchars($email) ?>">
  <input type="password" name="password" required placeholder="Password" value="<?= htmlspecialchars($password) ?>">
  <button type="submit">Login</button>
</form>

<a href="https://accounts.google.com/o/oauth2/auth?...">Login with Google</a>
<?php require_once '../includes/footer.php'; ?>