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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Logs - Admin Panel</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/tables.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-content-container {
            margin-left: var(--sidebar-width);
            padding: 2rem;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            border-radius: 4px;
            font-size: 0.95rem;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        
        .btn-back {
            background-color: var(--color-neutral-mid);
            color: var(--color-primary-dark);
        }
        
        .btn-back:hover {
            background-color: var(--color-primary-mid);
        }
        
        .empty-state {
            text-align: center;
            padding: 2rem;
            color: var(--color-primary-mid);
            background-color: var(--color-neutral-light);
            border-radius: 8px;
        }
        
        .log-type {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .log-type-login {
            background-color: rgba(46, 204, 113, 0.2);
            color: #2ecc71;
        }
        
        .log-type-action {
            background-color: rgba(52, 152, 219, 0.2);
            color: #3498db;
        }
        
        .log-type-error {
            background-color: rgba(231, 76, 60, 0.2);
            color: #e74c3c;
        }
        
        .log-type-system {
            background-color: rgba(155, 89, 182, 0.2);
            color: #9b59b6;
        }
        
        @media (max-width: 768px) {
            .admin-content-container {
                margin-left: 0;
                padding: 1rem;
            }
            
            .data-table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <div class="admin-content-container">
        <div class="page-header">
            <h1><i class="fas fa-scroll"></i> System Logs</h1>
            <a href="dashboard.php" class="btn btn-back">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <?php if (empty($logs)): ?>
            <div class="empty-state">
                <i class="fas fa-scroll" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                <p>No logs available</p>
            </div>
        <?php else: ?>
            <table class="data-table">
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
                            <td>
                                <?php 
                                $logClass = 'log-type-action';
                                if (strpos($log['action'], 'login') !== false) {
                                    $logClass = 'log-type-login';
                                } elseif (strpos($log['action'], 'error') !== false) {
                                    $logClass = 'log-type-error';
                                } elseif (strpos($log['action'], 'system') !== false) {
                                    $logClass = 'log-type-system';
                                }
                                ?>
                                <span class="log-type <?= $logClass ?>">
                                    <?= htmlspecialchars($log['action']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($log['ip_address']) ?></td>
                            <td title="<?= htmlspecialchars($log['user_agent']) ?>">
                                <?= substr(htmlspecialchars($log['user_agent']), 0, 50) ?>...
                            </td>
                            <td><?= date('M j, Y H:i', strtotime($log['occurred_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>