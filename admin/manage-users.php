<?php
require_once '../includes/db.php';
require_once 'admin_header.php';

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?");
    $stmt->execute([
        $_POST['name'],
        $_POST['email'],
        $_POST['role'],
        $_POST['user_id']
    ]);
    header("Location: manage-users.php");
    exit;
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$_POST['delete_id']]);
    header("Location: manage-users.php");
    exit;
}

// Fetch users
$stmt = $pdo->query("SELECT * FROM users ORDER BY registered_at DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin Panel</title>
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
        
        .btn-edit {
            background-color: var(--color-primary-mid);
            color: var(--color-primary-light);
        }
        
        .btn-delete {
            background-color: #e74c3c;
            color: white;
        }
        
        /* Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
        }
        
        .modal-content {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
            width: 90%;
            max-width: 500px;
            z-index: 1001;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--color-neutral-mid);
        }
        
        .modal-header h2 {
            margin: 0;
            color: var(--color-primary-dark);
        }
        
        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--color-primary-mid);
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--color-primary-dark);
            font-weight: 500;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--color-neutral-mid);
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        
        @media (max-width: 768px) {
            .admin-content-container {
                margin-left: 0;
                padding: 1rem;
            }
            
            .modal-content {
                width: 95%;
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="admin-content-container">
        <div class="page-header">
            <h1><i class="fas fa-users"></i> Manage Users</h1>
            <div class="header-actions">
                <a href="dashboard.php" class="btn btn-back">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Registered</th>
                        <th>Last Login</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td>
                            <span class="status-badge"><?= htmlspecialchars($user['role']) ?></span>
                        </td>
                        <td><?= date('M j, Y', strtotime($user['registered_at'])) ?></td>
                        <td><?= $user['last_login'] ? date('M j, Y', strtotime($user['last_login'])) : 'Never' ?></td>
                        <td>
                            <div class="table-actions">
                                <button onclick="openModal(<?= htmlspecialchars(json_encode($user)) ?>)" 
                                        class="btn-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="delete_id" value="<?= $user['id'] ?>">
                                    <button type="submit" 
                                            class="btn-delete"
                                            onclick="return confirm('Are you sure you want to delete this user?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div id="editModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit User</h2>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <form method="POST">
                <input type="hidden" name="user_id" id="modalUserId">
                <div class="form-group">
                    <label for="modalEmail">Email:</label>
                    <input type="email" name="email" id="modalEmail" required>
                </div>
                <div class="form-group">
                    <label for="modalName">Name:</label>
                    <input type="text" name="name" id="modalName" required>
                </div>
                <div class="form-group">
                    <label for="modalRole">Role:</label>
                    <select name="role" id="modalRole" required>
                        <?php foreach (['visitor', 'enthusiast', 'contributor', 'researcher', 'moderator', 'admin', 'super_admin'] as $role): ?>
                            <option value="<?= $role ?>"><?= ucfirst(str_replace('_', ' ', $role)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-back" onclick="closeModal()">Cancel</button>
                    <button type="submit" name="update_user" class="btn btn-primary">Update User</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(user) {
            document.getElementById('modalUserId').value = user.id;
            document.getElementById('modalEmail').value = user.email;
            document.getElementById('modalName').value = user.name;
            document.getElementById('modalRole').value = user.role;
            document.getElementById('editModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Close modal when clicking outside
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
</body>
</html>