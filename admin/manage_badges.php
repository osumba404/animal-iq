<?php
require_once '../includes/db.php';
require_once 'admin_header.php';

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
    $pdo->prepare("DELETE FROM user_badges WHERE badge_id = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM badges WHERE id = ?")->execute([$id]);
    header("Location: manage_badges.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Badges - Admin Panel</title>
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
        
        .btn-primary {
            background-color: var(--color-primary-accent);
            color: var(--color-primary-light);
        }
        
        .btn-primary:hover {
            background-color: var(--color-secondary-accent);
        }
        
        .btn-danger {
            background-color: #e74c3c;
            color: white;
        }
        
        .action-links {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .action-links a {
            text-decoration: none;
            font-weight: 500;
        }
        
        .form-container {
            background-color: var(--color-neutral-light);
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--color-primary-dark);
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--color-neutral-mid);
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .form-group textarea {
            min-height: 100px;
        }
        
        .badge-icon {
            width: 40px;
            height: 40px;
            object-fit: contain;
            border-radius: 50%;
            background-color: var(--color-neutral-light);
        }
        
        .badge-type {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .badge-type-milestone {
            background-color: rgba(46, 204, 113, 0.2);
            color: #2ecc71;
        }
        
        .badge-type-engagement {
            background-color: rgba(52, 152, 219, 0.2);
            color: #3498db;
        }
        
        .badge-type-special {
            background-color: rgba(155, 89, 182, 0.2);
            color: #9b59b6;
        }
        
        .alert-success {
            background-color: rgba(46, 204, 113, 0.2);
            color: #2ecc71;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(46, 204, 113, 0.3);
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
            <h1><i class="fas fa-award"></i> Badge Management</h1>
            <a href="dashboard.php" class="btn btn-back">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

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

                $stmt = $pdo->prepare("INSERT INTO badges (name, description, badge_type, icon) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $description, $badge_type, $icon]);
                header("Location: manage_badges.php");
                exit();
            }
            ?>

            <div class="form-container">
                <h2><i class="fas fa-plus"></i> Add New Badge</h2>
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="badge_type">Type</label>
                        <select id="badge_type" name="badge_type" required>
                            <option value="milestone">Milestone</option>
                            <option value="engagement">Engagement</option>
                            <option value="special">Special</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="icon">Icon</label>
                        <input type="file" id="icon" name="icon">
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Add Badge
                        </button>
                    </div>
                </form>
            </div>

        <?php elseif ($action === 'edit' && isset($_GET['id'])): ?>
            <?php
            $badge = fetchBadge($pdo, $_GET['id']);
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $name = $_POST['name'];
                $description = $_POST['description'];
                $badge_type = $_POST['badge_type'];
                $icon = $_FILES['icon']['name'] ?: $badge['icon'];

                if ($_FILES['icon']['name']) {
                    $uploadPath = "../uploads/icons/" . basename($icon);
                    move_uploaded_file($_FILES['icon']['tmp_name'], $uploadPath);
                }

                $stmt = $pdo->prepare("UPDATE badges SET name=?, description=?, badge_type=?, icon=? WHERE id=?");
                $stmt->execute([$name, $description, $badge_type, $icon, $_GET['id']]);
                header("Location: manage_badges.php");
                exit();
            }
            ?>

            <div class="form-container">
                <h2><i class="fas fa-edit"></i> Edit Badge</h2>
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" value="<?= htmlspecialchars($badge['name']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" required><?= htmlspecialchars($badge['description']) ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="badge_type">Type</label>
                        <select id="badge_type" name="badge_type" required>
                            <option value="milestone" <?= $badge['badge_type'] == 'milestone' ? 'selected' : '' ?>>Milestone</option>
                            <option value="engagement" <?= $badge['badge_type'] == 'engagement' ? 'selected' : '' ?>>Engagement</option>
                            <option value="special" <?= $badge['badge_type'] == 'special' ? 'selected' : '' ?>>Special</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="icon">Icon</label>
                        <input type="file" id="icon" name="icon">
                        <?php if (!empty($badge['icon'])): ?>
                            <div class="mt-1">
                                <img src="../uploads/icons/<?= htmlspecialchars($badge['icon']) ?>" class="badge-icon" alt="Current icon">
                                <p class="text-muted">Current icon</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Badge
                        </button>
                    </div>
                </form>
            </div>

        <?php elseif ($action === 'award'): ?>
            <?php
            $users = fetchUsers($pdo);
            $badges = fetchAllBadges($pdo);

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $user_id = $_POST['user_id'];
                $badge_id = $_POST['badge_id'];

                $stmt = $pdo->prepare("INSERT INTO user_badges (user_id, badge_id) VALUES (?, ?)");
                $stmt->execute([$user_id, $badge_id]);
                $success = "Badge awarded successfully!";
            }
            ?>

            <div class="form-container">
                <h2><i class="fas fa-medal"></i> Award Badge to User</h2>
                <?php if (!empty($success)): ?>
                    <div class="alert-success">
                        <i class="fas fa-check-circle"></i> <?= $success ?>
                    </div>
                <?php endif; ?>
                <form method="POST">
                    <div class="form-group">
                        <label for="user_id">User</label>
                        <select id="user_id" name="user_id" required>
                            <?php foreach ($users as $u): ?>
                                <option value="<?= $u['id'] ?>"><?= $u['name'] ?> (<?= $u['email'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="badge_id">Badge</label>
                        <select id="badge_id" name="badge_id" required>
                            <?php foreach ($badges as $b): ?>
                                <option value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-medal"></i> Award Badge
                        </button>
                    </div>
                </form>
            </div>

        <?php elseif ($action === 'leaderboard'): ?>
            <h2><i class="fas fa-trophy"></i> Leaderboard</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Category</th>
                        <th>Points</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (fetchLeaderboard($pdo) as $row): ?>
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
            <div class="action-links">
                <a href="manage_badges.php?action=add" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Badge
                </a>
                <a href="manage_badges.php?action=award" class="btn btn-primary">
                    <i class="fas fa-medal"></i> Award Badge
                </a>
                <a href="manage_badges.php?action=leaderboard" class="btn btn-primary">
                    <i class="fas fa-trophy"></i> View Leaderboard
                </a>
            </div>

            <table class="data-table">
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
                    <?php foreach (fetchAllBadges($pdo) as $badge): ?>
                        <tr>
                            <td>
                                <?php if (!empty($badge['icon'])): ?>
                                    <img src="../uploads/icons/<?= htmlspecialchars($badge['icon']) ?>" class="badge-icon" alt="Badge icon">
                                <?php else: ?>
                                    <span class="text-muted">No Icon</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($badge['name']) ?></td>
                            <td><?= htmlspecialchars($badge['description']) ?></td>
                            <td>
                                <span class="badge-type badge-type-<?= $badge['badge_type'] ?>">
                                    <?= ucfirst($badge['badge_type']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="manage_badges.php?action=edit&id=<?= $badge['id'] ?>" class="btn-edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="manage_badges.php?delete=<?= $badge['id'] ?>" 
                                       class="btn-delete" 
                                       title="Delete"
                                       onclick="return confirm('Are you sure you want to delete this badge?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>