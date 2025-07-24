<!-- admin/site_settings.php -->


<?php
require_once '../includes/db.php';
require_once 'admin_header.php';

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['settings'] as $key => $value) {
        $stmt = $pdo->prepare("INSERT INTO settings (`key`, `value`) VALUES (?, ?) 
                               ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)");
        $stmt->execute([$key, $value]);
    }
    $success = "Settings updated successfully.";
}

// Load settings
$stmt = $pdo->query("SELECT * FROM settings");
$settings = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $settings[$row['key']] = $row['value'];
}
?>

<h1>⚙️ Site Settings</h1>

<?php if (isset($success)): ?>
    <p style="color: green;"><?= $success ?></p>
<?php endif; ?>

<form method="post">
    <table>
        <tr>
            <td>Site Name:</td>
            <td><input type="text" name="settings[site_name]" value="<?= htmlspecialchars($settings['site_name'] ?? '') ?>" required></td>
        </tr>
        <tr>
            <td>Contact Email:</td>
            <td><input type="email" name="settings[contact_email]" value="<?= htmlspecialchars($settings['contact_email'] ?? '') ?>"></td>
        </tr>
        <tr>
            <td>Homepage Message:</td>
            <td><textarea name="settings[homepage_message]"><?= htmlspecialchars($settings['homepage_message'] ?? '') ?></textarea></td>
        </tr>
    </table>
    <button type="submit">💾 Save Settings</button>
</form>

<p><a href="dashboard.php">🔙 Back to Dashboard</a></p>

