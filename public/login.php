<?php
session_start();
require_once '../includes/db.php';
require_once 'header.php';
require_once 'nav.php';

$email = $_POST['email'] ?? ($_GET['email'] ?? '');
$password = $_POST['password'] ?? ($_GET['password'] ?? '');
$error = '';

// Check if there's a redirect target
$redirect_to = $_SESSION['redirect_to'] ?? 'index.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")->execute([$user['id']]);

        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role']
        ];

        $redirect = $redirect_to;
        unset($_SESSION['redirect_to']);
        header("Location: $redirect");
        exit;
    } else {
        $error = 'Invalid email or password.';
    }
}
?>

<style>
/* Login Page Styling */
.login-page {
  min-height: calc(100vh - 200px);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  background: var(--color-bg-secondary);
}

.login-container {
  width: 100%;
  max-width: 450px;
  background: var(--color-bg-primary);
  border-radius: 16px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
  overflow: hidden;
  position: relative;
  padding: 3rem 2.5rem;
  border: 1px solid var(--color-border-light);
}

.login-container::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 6px;
  background: linear-gradient(90deg, var(--color-primary), var(--color-accent-primary));
}

.login-header {
  text-align: center;
  margin-bottom: 2.5rem;
}

.login-header h1 {
  font-size: 2.2rem;
  color: var(--color-primary);
  margin-bottom: 0.5rem;
  font-weight: 700;
}

.login-header p {
  color: var(--color-text-muted);
  font-size: 1.1rem;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-group {
  position: relative;
  margin-bottom: 1rem;
}

.form-input {
  width: 100%;
  padding: 1rem 1rem 1rem 3rem;
  border: 2px solid var(--color-border-light);
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s ease;
  background-color: var(--color-bg-secondary);
  color: var(--color-text-primary);
}

.form-input:focus {
  outline: none;
  border-color: var(--color-primary);
  box-shadow: 0 0 0 3px rgba(1, 50, 33, 0.1);
}

.input-icon {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: var(--color-primary);
  font-size: 1.2rem;
}

.login-button {
  background: var(--color-primary);
  color: white;
  border: none;
  padding: 1rem;
  border-radius: 8px;
  font-size: 1.1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  margin-top: 1rem;
}

.login-button:hover {
  background: var(--color-primary-dark);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(1, 50, 33, 0.2);
}

.forgot-link {
  text-align: center;
  margin: 1.5rem 0;
}

.forgot-link a {
  color: var(--color-primary);
  text-decoration: none;
  font-size: 0.95rem;
  transition: color 0.2s ease;
}

.forgot-link a:hover {
  text-decoration: underline;
  color: var(--color-primary-dark);
}

.register-prompt {
  text-align: center;
  margin-top: 2rem;
  color: var(--color-text-muted);
  font-size: 0.95rem;
}

.register-prompt a {
  color: var(--color-primary);
  text-decoration: none;
  font-weight: 600;
  margin-left: 0.3rem;
  transition: color 0.2s ease;
}

.register-prompt a:hover {
  color: var(--color-primary-dark);
  text-decoration: underline;
}

.error-message {
  background-color: #FEE2E2;
  color: #B91C1C;
  padding: 0.75rem 1rem;
  border-radius: 8px;
  margin-bottom: 1.5rem;
  text-align: center;
  font-size: 0.95rem;
  border: 1px solid #FECACA;
}

@media (max-width: 576px) {
  .login-container {
    padding: 2rem 1.5rem;
  }
  
  .login-header h1 {
    font-size: 1.8rem;
  }
  
  .form-input {
    padding-left: 2.5rem;
  }
}
</style>

<div class="login-page">
  <div class="login-container">
    <header class="login-header">
      <h1>Welcome Back</h1>
      <p>Sign in to continue to Animal IQ</p>
    </header>

    <?php if ($error): ?>
      <div class="error-message">
        <?= htmlspecialchars($error) ?>
      </div>
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
</div>

<?php require_once 'footer.php'; ?>
