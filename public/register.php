<?php
require_once 'header.php';
require_once 'nav.php';
require_once '../includes/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT email FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        $message = "Email already registered.";
    } else {
        // Use correct column: password_hash
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)");
        if ($stmt->execute([$name, $email, $password])) {
            // Redirect with email & plain password
            header("Location: login.php?email=" . urlencode($email) . "&password=" . urlencode($_POST['password']));
            exit;
        } else {
            $message = "Registration failed. Try again.";
        }
    }
}
?>

<h1>Register</h1>

<?php if (!empty($message)) echo "<p style='color:red;'>$message</p>"; ?>

<form action="" method="post">
  <input type="text" name="name" required placeholder="Full Name">
  <input type="email" name="email" required placeholder="Email">
  <input type="password" name="password" required placeholder="Password">
  <button type="submit">Register</button>
</form>

<?php require_once 'footer.php'; ?>
