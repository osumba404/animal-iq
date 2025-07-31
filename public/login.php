<?php
require_once 'header.php';
require_once 'nav.php';

$email = $_GET['email'] ?? '';
$password = $_GET['password'] ?? '';
?>

<style>
/* Premium Login Page Styling */
.login-container {
  max-width: 500px;
  margin: 4rem auto;
  padding: 2.5rem;
  background: var(--color-bg-primary);
  border-radius: 16px;
  box-shadow: 0 12px 40px rgba(30, 24, 17, 0.1);
  position: relative;
  overflow: hidden;
}

.login-container::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 8px;
  background: linear-gradient(90deg, var(--color-primary), var(--color-accent-primary), var(--color-primary));
}

.login-header {
  text-align: center;
  margin-bottom: 2.5rem;
}

.login-header h1 {
  font-size: 2.5rem;
  color: var(--color-primary);
  margin-bottom: 0.5rem;
}

.login-header p {
  color: var(--color-text-muted);
}

.login-form {
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

.login-button {
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

.login-button:hover {
  background: var(--color-primary-dark);
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(1, 50, 33, 0.3);
}

.social-login {
  text-align: center;
  margin: 2rem 0;
  position: relative;
}

.social-login::before,
.social-login::after {
  content: '';
  position: absolute;
  top: 50%;
  width: 30%;
  height: 1px;
  background: var(--color-border-light);
}

.social-login::before {
  left: 0;
}

.social-login::after {
  right: 0;
}

.social-login span {
  background: var(--color-bg-primary);
  position: relative;
  padding: 0 1rem;
  color: var(--color-text-muted);
}

.google-login {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.8rem;
  background: white;
  color: #4285F4;
  border: 1px solid var(--color-border-light);
  border-radius: 50px;
  padding: 0.8rem 1.5rem;
  text-decoration: none;
  font-weight: bold;
  transition: all 0.3s ease;
}

.google-login:hover {
  background: #f8f8f8;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.forgot-link {
  text-align: center;
  margin-top: 1.5rem;
}

.forgot-link a {
  color: var(--color-primary);
  text-decoration: none;
  transition: color 0.2s ease;
}

.forgot-link a:hover {
  text-decoration: underline;
}

.register-prompt {
  text-align: center;
  margin-top: 2rem;
  color: var(--color-text-muted);
}

.register-prompt a {
  color: var(--color-primary);
  font-weight: bold;
  text-decoration: none;
}

.register-prompt a:hover {
  text-decoration: underline;
}

@media (max-width: 768px) {
  .login-container {
    margin: 2rem 1rem;
    padding: 1.5rem;
  }
  
  .login-header h1 {
    font-size: 2rem;
  }
}
</style>

<div class="login-container">
  <header class="login-header">
    <h1>Welcome Back</h1>
    <p>Sign in to continue to Animal IQ</p>
  </header>

  <form action="../api/login.php" method="post" class="login-form">
    <div class="form-group">
      <span class="input-icon">✉️</span>
      <input type="email" name="email" class="form-input" required placeholder="Email address" value="<?= htmlspecialchars($email) ?>">
    </div>
    
    <div class="form-group">
      <span class="input-icon">🔒</span>
      <input type="password" name="password" class="form-input" required placeholder="Password" value="<?= htmlspecialchars($password) ?>">
    </div>
    
    <button type="submit" class="login-button">Sign In</button>
  </form>

  <div class="forgot-link">
    <a href="forgot_password.php">Forgot password?</a>
  </div>

  <!-- <div class="social-login">
    <span>Or continue with</span>
  </div>

  <a href="https://accounts.google.com/o/oauth2/auth?..." class="google-login">
    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M17.64 9.2045C17.64 8.5663 17.5827 7.9527 17.4764 7.3636H9V10.845H13.8436C13.635 11.97 13.0009 12.9231 12.0477 13.5613V15.8195H14.9564C16.6582 14.2527 17.64 11.9454 17.64 9.2045Z" fill="#4285F4"/>
      <path d="M9 18C11.43 18 13.4673 17.1941 14.9564 15.8195L12.0477 13.5613C11.2418 14.1013 10.2109 14.4204 9 14.4204C6.65591 14.4204 4.67182 12.8372 3.96409 10.71H0.957275V13.0418C2.43818 15.9831 5.48182 18 9 18Z" fill="#34A853"/>
      <path d="M3.96409 10.71C3.78409 10.17 3.68182 9.5931 3.68182 9C3.68182 8.4069 3.78409 7.83 3.96409 7.29V4.9582H0.957273C0.347727 6.1731 0 7.5477 0 9C0 10.4523 0.347727 11.8269 0.957273 13.0418L3.96409 10.71Z" fill="#FBBC05"/>
      <path d="M9 3.57955C10.3214 3.57955 11.5077 4.03364 12.4405 4.92545L15.0218 2.34409C13.4632 0.891818 11.4259 0 9 0C5.48182 0 2.43818 2.01682 0.957275 4.95818L3.96409 7.29C4.67182 5.16273 6.65591 3.57955 9 3.57955Z" fill="#EA4335"/>
    </svg>
    Continue with Google
  </a> -->

  <div class="register-prompt">
    Don't have an account? <a href="register.php">Sign up</a>
  </div>
</div>

<?php require_once 'footer.php'; ?>