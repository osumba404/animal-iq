<?php
// require_once 'header.php';
require_once 'nav.php';
require_once '../includes/db.php';

$message = '';
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT email FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        $message = "This email is already registered.";
    } else {
        // Use correct column: password_hash
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)");
        if ($stmt->execute([$name, $email, $password])) {
            // Redirect with email & plain password
            header("Location: login.php?email=" . urlencode($email) . "&password=" . urlencode($_POST['password']));
            exit;
        } else {
            $message = "Registration failed. Please try again.";
        }
    }
}
?>

<style>
/* Premium Registration Page Styling */
.register-container {
  max-width: 500px;
  margin: 4rem auto;
  padding: 2.5rem;
  background: var(--color-bg-primary);
  border-radius: 16px;
  box-shadow: 0 12px 40px rgba(30, 24, 17, 0.1);
  position: relative;
  overflow: hidden;
}

.register-container::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 8px;
  background: linear-gradient(90deg, var(--color-primary), var(--color-accent-primary), var(--color-primary));
}

.register-header {
  text-align: center;
  margin-bottom: 2.5rem;
}

.register-header h1 {
  font-size: 2.5rem;
  color: var(--color-primary);
  margin-bottom: 0.5rem;
}

.register-header p {
  color: var(--color-text-muted);
}

.register-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-group {
  position: relative;
}

.form-input {
  width: 100%;
  padding: 1rem 1rem 1rem 3rem;
  border: 2px solid var(--color-border-light);
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s ease;
  background-color: var(--color-bg-secondary);
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
}

.register-button {
  background: var(--color-primary);
  color: white;
  border: none;
  border-radius: 50px;
  padding: 1rem;
  font-size: 1.1rem;
  font-weight: bold;
  cursor: pointer;
  transition: all 0.3s ease;
  margin-top: 1rem;
  box-shadow: 0 4px 12px rgba(1, 50, 33, 0.2);
}

.register-button:hover {
  background: var(--color-primary-dark);
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(1, 50, 33, 0.3);
}

.error-message {
  color: var(--color-error);
  background: rgba(194, 59, 34, 0.1);
  padding: 1rem;
  border-radius: 8px;
  margin-bottom: 1.5rem;
  text-align: center;
  border-left: 4px solid var(--color-error);
}

.login-prompt {
  text-align: center;
  margin-top: 2rem;
  color: var(--color-text-muted);
}

.login-prompt a {
  color: var(--color-primary);
  font-weight: bold;
  text-decoration: none;
}

.login-prompt a:hover {
  text-decoration: underline;
}

.password-requirements {
  background: var(--color-bg-secondary);
  padding: 1rem;
  border-radius: 8px;
  margin-top: -0.5rem;
  font-size: 0.9rem;
  color: var(--color-text-muted);
}

.password-requirements ul {
  margin: 0.5rem 0 0 1rem;
  padding: 0;
}

@media (max-width: 768px) {
  .register-container {
    margin: 2rem 1rem;
    padding: 1.5rem;
  }
  
  .register-header h1 {
    font-size: 2rem;
  }
}
</style>

<div class="register-container">
  <header class="register-header">
    <h1>Create Your Account</h1>
    <p>Join our community of animal lovers</p>
  </header>

  <?php if (!empty($message)): ?>
    <div class="error-message">
      <?php echo htmlspecialchars($message); ?>
    </div>
  <?php endif; ?>

  <form action="" method="post" class="register-form">
    <div class="form-group">
      <span class="input-icon">👤</span>
      <input type="text" name="name" class="form-input" required placeholder="Full Name" value="<?php echo htmlspecialchars($name); ?>">
    </div>
    
    <div class="form-group">
      <span class="input-icon">✉️</span>
      <input type="email" name="email" class="form-input" required placeholder="Email Address" value="<?php echo htmlspecialchars($email); ?>">
    </div>
    
    <div class="form-group">
      <span class="input-icon">🔒</span>
      <input type="password" name="password" class="form-input" required placeholder="Create Password">
    </div>
    
    <div class="password-requirements">
      <p>Password should contain:</p>
      <ul>
        <li>At least 8 characters</li>
        <li>One uppercase letter</li>
        <li>One number</li>
      </ul>
    </div>
    
    <button type="submit" class="register-button">Create Account</button>
  </form>

  <div class="login-prompt">
    Already have an account? <a href="login.php">Sign in</a>
  </div>
</div>

<?php require_once 'footer.php'; ?>