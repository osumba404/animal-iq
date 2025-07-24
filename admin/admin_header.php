<!-- admin/admin_header.php -->
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

$admin_name = $_SESSION['admin_name'] ?? 'Admin';
$admin_role = $_SESSION['admin_role'] ?? '';
?>

<!-- Top banner (optional) -->
<div style="background:#f0f0f0;padding:10px 20px;border-bottom:1px solid #ccc;">
    <strong>👋 Welcome, <?= htmlspecialchars($admin_name) ?> (<?= $admin_role ?>)</strong>
    <span style="float:right;">
        <a href="dashboard.php">🏠 Dashboard</a> | 
        <a href="admin_logout.php">🚪 Logout</a>
    </span>
</div>
<br>
