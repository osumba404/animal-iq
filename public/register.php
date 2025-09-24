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
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)");
        if ($stmt->execute([$name, $email, $password])) {
            header("Location: login.php?email=" . urlencode($email) . "&password=" . urlencode($_POST['password']));
            exit;
        } else {
            $message = "Registration failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - <?php echo htmlspecialchars($settings['site_name'] ?? 'Animal IQ'); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
    :root {
        /* Core Brand Colors */
        --color-primary: #013221;         /* Deep jungle green */
        --color-primary-light: #10593c;    /* Lighter green */
        --color-primary-lighter: #1a8a6a;  /* Highlight teal */
        --color-primary-dark: #002418;     /* Darker green */
        
        /* Accent Colors */
        --color-accent-primary: #e8b824;   /* Gold */
        --color-accent-secondary: #d5a91c; /* Darker gold */
        
        /* Text Colors */
        --color-text-primary: #1E1811;     /* Main text */
        --color-text-muted: #A8A293;       /* Muted text */
        --color-text-inverted: #FFFFFF;    /* White text */
        
        /* Background Colors */
        --color-bg-primary: #FFF8E8;       /* Main background */
        --color-bg-secondary: #F5F0E0;     /* Secondary background */
        
        /* Utility Colors */
        --color-error: #c23b22;            /* Error color */
        --color-border-light: #D5CFC0;     /* Border color */
    }
    
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body {
        font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        background-color: var(--color-bg-primary);
        color: var(--color-text-primary);
        line-height: 1.6;
        padding-top: 80px; /* Space for fixed navbar */
    }
    
    /* Register Container */
    .register-page {
        min-height: calc(100vh - 200px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
        background: var(--color-bg-secondary);
    }
    
    .register-container {
        width: 100%;
        max-width: 500px;
        background: var(--color-bg-primary);
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        position: relative;
        padding: 3rem 2.5rem;
        border: 1px solid var(--color-border-light);
    }
    
    .register-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 6px;
        background: linear-gradient(90deg, var(--color-primary), var(--color-accent-primary));
    }
    
    .register-header {
        text-align: center;
        margin-bottom: 2.5rem;
    }
    
    .register-header h1 {
        font-size: 2.2rem;
        color: var(--color-primary);
        margin-bottom: 0.5rem;
        font-weight: 700;
    }
    
    .register-header p {
        color: var(--color-text-muted);
        font-size: 1.1rem;
    }
    
    /* Form Styles */
    .register-form {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
    
    .form-group {
        position: relative;
        margin-bottom: 1.5rem;
    }
    
    .form-input {
        width: 100%;
        padding: 1rem 1rem 1rem 3rem;
        border: 2px solid var(--color-border-light);
        border-radius: 8px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background-color: var(--color-bg-secondary);
        font-family: inherit;
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
        font-size: 1.1rem;
    }
    
    .register-button {
        background: var(--color-primary);
        color: var(--color-text-inverted);
        border: none;
        border-radius: 8px;
        padding: 1rem;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 1rem;
        width: 100%;
        font-family: inherit;
    }
    
    .register-button:hover {
        background: var(--color-primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .error-message {
        color: var(--color-error);
        background: rgba(194, 59, 34, 0.1);
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        text-align: center;
        border-left: 4px solid var(--color-error);
        font-size: 0.95rem;
    }
    
    .login-prompt {
        text-align: center;
        margin-top: 1.5rem;
        color: var(--color-text-muted);
        font-size: 0.95rem;
    }
    
    .login-prompt a {
        color: var(--color-primary);
        font-weight: 600;
        text-decoration: none;
        transition: color 0.2s ease;
    }
    
    .login-prompt a:hover {
        color: var(--color-primary-dark);
        text-decoration: underline;
    }
    
    .password-requirements {
        background: var(--color-bg-secondary);
        padding: 1rem;
        border-radius: 8px;
        margin-top: -0.5rem;
        font-size: 0.9rem;
        color: var(--color-text-muted);
        border: 1px solid var(--color-border-light);
    }
    
    .password-requirements ul {
        margin: 0.5rem 0 0 1.5rem;
        padding: 0;
        line-height: 1.6;
    }
    
    /* Responsive Styles */
    @media (max-width: 768px) {
        body {
            padding-top: 70px;
        }
        
        .register-container {
            padding: 2rem 1.5rem;
            margin: 1rem;
        }
        
        .register-header h1 {
            font-size: 1.8rem;
        }
        
        .register-header p {
            font-size: 1rem;
        }
        
        .form-input {
            padding: 0.8rem 0.8rem 0.8rem 2.8rem;
        }
        
        .input-icon {
            left: 0.8rem;
        }
    }
    
    @media (max-width: 480px) {
        .register-container {
            padding: 1.5rem 1.25rem;
            margin: 0.5rem;
        }
        
        .register-header h1 {
            font-size: 1.6rem;
        }
        
        .register-form {
            gap: 1rem;
        }
        
        .form-group {
            margin-bottom: 1.25rem;
        }
        
        .form-input {
            padding: 0.75rem 0.75rem 0.75rem 2.75rem;
            font-size: 0.95rem;
        }
        
        .input-icon {
            left: 0.75rem;
            font-size: 1rem;
        }
        
        .register-button {
            padding: 0.9rem;
            font-size: 1rem;
        }
        
        .password-requirements {
            font-size: 0.85rem;
            padding: 0.75rem;
        }
        
        .login-prompt {
            font-size: 0.9rem;
        }
    }
    
    /* Navbar Styles */
    nav {
        background-color: var(--color-primary);
        color: var(--color-text-inverted);
        padding: 1rem;
        text-align: center;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
    }
    
    nav a {
        color: var(--color-text-inverted);
        text-decoration: none;
        transition: color 0.2s ease;
    }
    
    nav a:hover {
        color: var(--color-text-inverted);
    }
    </style>
</head>
<body>
      <div class="register-page">
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
                    <strong>Password must contain:</strong>
                    <ul>
                        <li>At least 8 characters</li>
                        <li>One uppercase letter</li>
                        <li>One number</li>
                        <li>One special character</li>
                    </ul>
                </div>
                
                <button type="submit" class="register-button">Create Account</button>
                
                <div class="login-prompt">
                    Already have an account? <a href="login.php">Sign in</a>
                </div>
            </form>
        </div>
    </div>
    
    <?php require_once 'footer.php'; ?>
    
    <script>
    // Form validation
    document.querySelector('.register-form').addEventListener('submit', function(e) {
        const password = this.querySelector('input[name="password"]').value;
        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
        
        if (!passwordRegex.test(password)) {
            e.preventDefault();
            alert('Password must be at least 8 characters long and include at least one uppercase letter, one number, and one special character.');
        }
    });
    </script>
</body>
</html>