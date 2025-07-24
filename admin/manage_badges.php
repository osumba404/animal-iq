<!-- admin/manage_badges.php -->

<?php
require_once '../config/db.php';
require_once 'admin_header.php';
session_start();

if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'super_admin'])) {
    header("Location: ../public/login.php");
    exit();
}

$action = $_GET['action'] ?? 'list';

function fetchAllBadges($conn) {
    $stmt = $conn->query("SELECT * FROM badges ORDER BY id DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function fetchBadge($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM badges WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function fetchUsers($conn) {
    $stmt = $conn->query("SELECT id, name, email FROM users ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function fetchLeaderboard($conn) {
    $stmt = $conn->query("
        SELECT u.name, u.email, l.category, l.points
        FROM leaderboard l
        JOIN users u ON l.user_id = u.id
        ORDER BY l.points DESC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $conn->prepare("DELETE FROM user_badges WHERE badge_id = ?")->execute([$id]);
    $conn->prepare("DELETE FROM badges WHERE id = ?")->execute([$id]);

    header("Location: manage_badges.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Manage Badges - Animal IQ</title>
    <link rel="stylesheet" href="admin_styles.css">
</head>
<body>
    <br><a href="dashboard.php">← Back to Dashboard</a>
<h1>🏅 Badge Management</h1>

<?php if ($action === 'add'): ?>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $badge_type = $_POST['badge_type'];
        $icon = $_FILES['icon']['name'];

        if ($icon) {
            $uploadPath = "../uploads/icons/" . basename($icon);
            move_uploaded_file($_FILES['icon']['tmp_name'], $uploadPath);
        }

        $stmt = $conn->prepare("INSERT INTO badges (name, description, badge_type, icon) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $description, $badge_type, $icon]);
        header("Location: manage_badges.php");
        exit();
    }
    ?>

    <h2>➕ Add New Badge</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>Name:</label><input type="text" name="name" required><br>
        <label>Description:</label><textarea name="description" required></textarea><br>
        <label>Type:</label>
        <select name="badge_type" required>
            <option value="milestone">Milestone</option>
            <option value="engagement">Engagement</option>
            <option value="special">Special</option>
        </select><br>
        <label>Icon:</label><input type="file" name="icon"><br>
        <button type="submit">Add Badge</button>
    </form>

<?php elseif ($action === 'edit' && isset($_GET['id'])): ?>

    <?php
    $badge = fetchBadge($conn, $_GET['id']);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $badge_type = $_POST['badge_type'];
        $icon = $_FILES['icon']['name'] ?: $badge['icon'];

        if ($_FILES['icon']['name']) {
            $uploadPath = "../uploads/icons/" . basename($icon);
            move_uploaded_file($_FILES['icon']['tmp_name'], $uploadPath);
        }

        $stmt = $conn->prepare("UPDATE badges SET name=?, description=?, badge_type=?, icon=? WHERE id=?");
        $stmt->execute([$name, $description, $badge_type, $icon, $_GET['id']]);
        header("Location: manage_badges.php");
        exit();
    }
    ?>

    <h2>✏ Edit Badge</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>Name:</label><input type="text" name="name" value="<?= htmlspecialchars($badge['name']) ?>" required><br>
        <label>Description:</label><textarea name="description" required><?= htmlspecialchars($badge['description']) ?></textarea><br>
        <label>Type:</label>
        <select name="badge_type" required>
            <option value="milestone" <?= $badge['badge_type'] == 'milestone' ? 'selected' : '' ?>>Milestone</option>
            <option value="engagement" <?= $badge['badge_type'] == 'engagement' ? 'selected' : '' ?>>Engagement</option>
            <option value="special" <?= $badge['badge_type'] == 'special' ? 'selected' : '' ?>>Special</option>
        </select><br>
        <label>Icon:</label><input type="file" name="icon"><br>
        <button type="submit">Update Badge</button>
    </form>

<?php elseif ($action === 'award'): ?>

    <?php
    $users = fetchUsers($conn);
    $badges = fetchAllBadges($conn);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user_id = $_POST['user_id'];
        $badge_id = $_POST['badge_id'];

        $stmt = $conn->prepare("INSERT INTO user_badges (user_id, badge_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $badge_id]);
        echo "<p style='color:green'>Badge awarded successfully!</p>";
    }
    ?>

    <h2>🎖 Award Badge to User</h2>
    <form method="POST">
        <label>User:</label>
        <select name="user_id" required>
            <?php foreach ($users as $u): ?>
                <option value="<?= $u['id'] ?>"><?= $u['name'] ?> (<?= $u['email'] ?>)</option>
            <?php endforeach; ?>
        </select><br>
        <label>Badge:</label>
        <select name="badge_id" required>
            <?php foreach ($badges as $b): ?>
                <option value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
            <?php endforeach; ?>
        </select><br>
        <button type="submit">Award Badge</button>
    </form>

<?php elseif ($action === 'leaderboard'): ?>

    <h2>🏆 Leaderboard</h2>
    <table>
        <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Category</th>
            <th>Points</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach (fetchLeaderboard($conn) as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['category']) ?></td>
                <td><?= htmlspecialchars($row['points']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

<?php else: ?>

    <a href="manage_badges.php?action=add">➕ Add Badge</a> |
    <a href="manage_badges.php?action=award">🎖 Award Badge</a> |
    <a href="manage_badges.php?action=leaderboard">🏆 View Leaderboard</a><br><br>

    <table>
        <thead>
            <tr>
                <th>Icon</th>
                <th>Name</th>
                <th>Description</th>
                <th>Type</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach (fetchAllBadges($conn) as $badge): ?>
            <tr>
                <td>
                    <?php if (!empty($badge['icon'])): ?>
                        <img src="../uploads/icons/<?= htmlspecialchars($badge['icon']) ?>" width="40">
                    <?php else: ?> No Icon <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($badge['name']) ?></td>
                <td><?= htmlspecialchars($badge['description']) ?></td>
                <td><?= htmlspecialchars($badge['badge_type']) ?></td>
                <td>
                    <a href="manage_badges.php?action=edit&id=<?= $badge['id'] ?>">Edit</a> |
                    <a href="manage_badges.php?delete=<?= $badge['id'] ?>" onclick="return confirm('Delete this badge?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>

<br><a href="dashboard.php">← Back to Dashboard</a>
</body>
</html>
