<!-- admin/logs.php -->

<?php
require_once '../includes/db.php';
require_once 'admin_header.php';

$stmt = $pdo->query("
    SELECT l.*, u.name AS user_name
    FROM logs l
    LEFT JOIN users u ON l.user_id = u.id
    ORDER BY occurred_at DESC
    LIMIT 100
");
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>📜 System Logs</h1>

<?php if (empty($logs)): ?>
    <p>No logs available.</p>
<?php else: ?>
    <table border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse; width: 100%;">
        <thead>
            <tr>
                <th>#</th>
                <th>User</th>
                <th>Action</th>
                <th>IP</th>
                <th>Browser</th>
                <th>Time</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $i => $log): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($log['user_name'] ?? 'System') ?></td>
                    <td><?= htmlspecialchars($log['action']) ?></td>
                    <td><?= $log['ip_address'] ?></td>
                    <td><?= substr($log['user_agent'], 0, 50) ?>...</td>
                    <td><?= $log['occurred_at'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<p><a href="dashboard.php">🔙 Back to Dashboard</a></p>
