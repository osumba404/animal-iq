<?php
session_start();
// require_once 'header.php';
require_once '../includes/db.php';

$email = $_POST['email'] ?? ($_GET['email'] ?? '');
$password = $_POST['password'] ?? ($_GET['password'] ?? '');
$error = '';

// Check if there’s a redirect target
$redirect_to = $_SESSION['redirect_to'] ?? 'index.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        // Update last login timestamp
        $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")->execute([$user['id']]);

        // Set session data
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role']
        ];

        // Redirect to the originally requested page or index
        $redirect = $redirect_to;
        unset($_SESSION['redirect_to']);
        header("Location: $redirect");
        exit;
    } else {
        $error = 'Invalid email or password.';
    }
}
?>

<!-- Your CSS here (unchanged) -->

<div class="login-container">
  <header class="login-header">
    <h1>Welcome Back</h1>
    <p>Sign in to continue to Animal IQ</p>
  </header>

  <?php if ($error): ?>
    <p style="color: red; text-align: center;"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="post" class="login-form">
    <div class="form-group">
      <span class="input-icon">✉️</span>
      <input type="email" name="email" class="form-input" required placeholder="Email address" value="<?= htmlspecialchars($email) ?>">
    </div>

    <div class="form-group">
      <span class="input-icon">🔒</span>
      <input type="password" name="password" class="form-input" required placeholder="Password">
    </div>

    <button type="submit" class="login-button">Sign In</button>
  </form>

  <div class="forgot-link">
    <a href="forgot_password.php">Forgot password?</a>
  </div>

  <div class="register-prompt">
    Don't have an account? <a href="register.php">Sign up</a>
  </div>
</div>

<?php require_once 'footer.php'; ?>
