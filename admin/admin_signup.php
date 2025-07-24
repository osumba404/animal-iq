<!-- admin/admin_signup.php -->
<?php
require_once '../includes/db.php';
// require_once 'admin_header.php';

// if ($_SESSION['admin_role'] !== 'super_admin') {
//     echo "⛔ Only Super Admins can create other admins.";
//     exit;
// }

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    $role = $_POST['role'];

    if ($password !== $confirm) {
        $error = "❌ Passwords do not match.";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare("INSERT INTO admins (full_name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$full_name, $email, $hashed, $role]);
            $success = "✅ Admin created successfully.";
        } catch (PDOException $e) {
            $error = "⚠️ Error: " . $e->getMessage();
        }
    }
}
?>

<h2>➕ Create New Admin</h2>

<?php if ($success): ?><p style="color:green;"><?= $success ?></p><?php endif; ?>
<?php if ($error): ?><p style="color:red;"><?= $error ?></p><?php endif; ?>

<form method="post">
    <label>Full Name:<br><input type="text" name="full_name" required></label><br><br>
    <label>Email:<br><input type="email" name="email" required></label><br><br>
    <label>Password:<br><input type="password" name="password" required></label><br><br>
    <label>Confirm Password:<br><input type="password" name="confirm_password" required></label><br><br>
    <label>Role:<br>
        <select name="role" required>
            <option value="moderator">Moderator</option>
            <option value="super_admin">Super Admin</option>
        </select>
    </label><br><br>
    <button type="submit">✅ Create Admin</button>
</form>

<p><a href="dashboard.php">🔙 Back to Dashboard</a></p>
