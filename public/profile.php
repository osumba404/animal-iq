<?php
// public/profile.php
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once 'header.php';
require_once 'nav.php';

// Get ID from URL and sanitize
$id = $_GET['id'] ?? null;

if (!$id) {
    echo "<p>User ID is missing.</p>";
    exit;
}

$user = getUserProfile($pdo, $id);

?>

<h1>User Profile</h1>

<?php if ($user): ?>
<div style="display: flex; align-items: center; gap: 20px;">
    <img src="uploads/profile_pics/<?php echo htmlspecialchars($user['profile_picture'] ?? 'default.png'); ?>" alt="Profile Picture" width="100" height="100" style="border-radius: 50%; object-fit: cover;">
    <div>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Joined:</strong> <?php echo htmlspecialchars($user['registered_at']); ?></p>
    </div>
</div>
<br>

<?php if ($user['id'] === $_SESSION['user_id']): ?>
    <button onclick="document.getElementById('editModal').style.display='block'">Edit Profile</button>

    <!-- Edit Modal -->
    <div id="editModal" style="display:none; position:fixed; top:20%; left:30%; background:#fff; padding:20px; border:1px solid #ccc; border-radius:10px; z-index:1000;">
        <form method="POST" action="update_profile.php" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['id']); ?>">
            <label>Name:</label><br>
            <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>"><br><br>

            <label>Change Profile Picture:</label><br>
            <input type="file" name="profile_picture"><br><br>

            <button type="submit">Save</button>
            <button type="button" onclick="document.getElementById('editModal').style.display='none'">Cancel</button>
        </form>
    </div>
<?php endif; ?>

<?php else: ?>
    <p style="color:red;">User profile not found.</p>
<?php endif; ?>

<?php require_once 'footer.php'; ?>
