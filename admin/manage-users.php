<?php
require_once '../includes/db.php';
require_once '../includes/header.php';
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

<br><a href="dashboard.php">← Back to Dashboard</a>
<h1>Manage Users</h1>
<table border="1" cellpadding="10">
  <tr>
    <th>Email</th>
    <th>Name</th>
    <th>Role</th>
    <th>Registered</th>
    <th>Last Login</th>
    <th>Actions</th>
  </tr>
  <?php foreach ($users as $user): ?>
  <tr>
    <td><?= htmlspecialchars($user['email']) ?></td>
    <td><?= htmlspecialchars($user['name']) ?></td>
    <td><?= htmlspecialchars($user['role']) ?></td>
    <td><?= $user['registered_at'] ?></td>
    <td><?= $user['last_login'] ?></td>
    <td>
      <button onclick="openModal(<?= htmlspecialchars(json_encode($user)) ?>)">Edit</button>
      <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this user?');">
        <input type="hidden" name="delete_id" value="<?= $user['id'] ?>">
        <button type="submit">Delete</button>
      </form>
    </td>
  </tr>
  <?php endforeach; ?>
</table>

<!-- Modal -->
<div id="editModal" style="display:none; position:fixed; top:20%; left:30%; width:40%; background:#fff; border:1px solid #333; padding:20px; box-shadow:0 0 10px #000;">
  <h2>Edit User</h2>
  <form method="POST">
    <input type="hidden" name="user_id" id="modalUserId">
    <label>Email:</label><br>
    <input type="email" name="email" id="modalEmail" required><br><br>
    <label>Name:</label><br>
    <input type="text" name="name" id="modalName" required><br><br>
    <label>Role:</label><br>
    <select name="role" id="modalRole" required>
      <?php foreach (['visitor', 'enthusiast', 'contributor', 'researcher', 'moderator', 'admin', 'super_admin'] as $role): ?>
        <option value="<?= $role ?>"><?= $role ?></option>
      <?php endforeach; ?>
    </select><br><br>
    <button type="submit" name="update_user">Update</button>
    <button type="button" onclick="closeModal()">Cancel</button>
  </form>
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
</script>

<?php require_once '../includes/footer.php'; ?>
