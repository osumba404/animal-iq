<?php
// admin/manage_species_statuses.php
session_start();
require '../includes/db.php';

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    if ($action === 'add') {
        $label = trim($_POST['label']);
        $created_by = $_SESSION['admin_id'];
        $stmt = $pdo->prepare("INSERT INTO species_statuses (label, created_by) VALUES (?, ?)");
        $stmt->execute([$label, $created_by]);
    } elseif ($action === 'edit') {
        $id = $_POST['id'];
        $label = trim($_POST['label']);
        $stmt = $pdo->prepare("UPDATE species_statuses SET label = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$label, $id]);
    } elseif ($action === 'delete') {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM species_statuses WHERE id = ?");
        $stmt->execute([$id]);
    }
    header('Location: manage_species_statuses.php');
    exit;
}

$statuses = $pdo->query("SELECT * FROM species_statuses ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Species Statuses</title>
</head>
<body>
<h2>Species Statuses</h2>
<form method="POST">
    <input type="text" name="label" required placeholder="New status">
    <input type="hidden" name="action" value="add">
    <button type="submit">Add</button>
</form>
<table border="1">
    <tr><th>ID</th><th>Label</th><th>Actions</th></tr>
    <?php foreach ($statuses as $status): ?>
    <tr>
        <td><?= $status['id'] ?></td>
        <td>
            <form method="POST" style="display:inline">
                <input type="text" name="label" value="<?= htmlspecialchars($status['label']) ?>">
                <input type="hidden" name="id" value="<?= $status['id'] ?>">
                <input type="hidden" name="action" value="edit">
                <button type="submit">Save</button>
            </form>
        </td>
        <td>
            <form method="POST" style="display:inline" onsubmit="return confirm('Delete this status?')">
                <input type="hidden" name="id" value="<?= $status['id'] ?>">
                <input type="hidden" name="action" value="delete">
                <button type="submit">Delete</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
</body>
</html>
