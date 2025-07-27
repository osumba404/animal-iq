<?php
require_once '../includes/db.php';
$stmt = $pdo->prepare("SELECT value FROM settings WHERE `key` = 'contact_email'");
$stmt->execute();
$email = $stmt->fetchColumn();
?>

<style>
/* Premium Footer Styles */
footer {
    background: rgba(1, 50, 33, 0.9); /* Dark green with transparency */
    backdrop-filter: blur(10px);
    color: white;
    padding: 1.5rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    margin-top: 3rem;
}

footer p {
    margin: 0;
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

footer a {
    color: var(--color-accent-primary);
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
}

footer a:hover {
    color: var(--color-accent-secondary);
    text-decoration: underline;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    footer {
        flex-direction: column;
        text-align: center;
        padding: 1rem;
    }
    
    footer p {
        justify-content: center;
    }
}
</style>

<footer>
  <p>&copy; <?= date('Y') ?> Animal IQ. All rights reserved.</p>
  <?php if ($email): ?>
    <p>
      <span>📧</span>
      <span>Contact us:</span>
      <a href="mailto:<?= htmlspecialchars($email) ?>">
        <?= htmlspecialchars($email) ?>
      </a>
    </p>
  <?php endif; ?>
</footer>
</body>
</html>