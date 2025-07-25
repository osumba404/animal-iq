<?php
require_once '../includes/db.php';
$stmt = $pdo->prepare("SELECT value FROM settings WHERE `key` = 'contact_email'");
$stmt->execute();
$email = $stmt->fetchColumn();
?>
<footer>
  <p>&copy; <?= date('Y') ?> Animal IQ. All rights reserved.</p>
  <?php if ($email): ?>
    <p>📧 Contact us: <a href="mailto:<?= htmlspecialchars($email) ?>"><?= htmlspecialchars($email) ?></a></p>
  <?php endif; ?>
</footer>
</body>
</html>
