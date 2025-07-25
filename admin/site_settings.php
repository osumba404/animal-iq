<?php
// admin/site_settings.php

require_once '../includes/db.php';
require_once 'admin_header.php';

function saveSetting($key, $value, $pdo) {
    $stmt = $pdo->prepare("INSERT INTO settings (`key`, `value`) 
                           VALUES (:key, :value) 
                           ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)");
    $stmt->execute([':key' => $key, ':value' => $value]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Logo Upload
    if (!empty($_FILES['site_logo']['name'])) {
        $fileTmp = $_FILES['site_logo']['tmp_name'];
        $fileName = basename($_FILES['site_logo']['name']);
        $targetPath = '../uploads/images/' . $fileName;
        move_uploaded_file($fileTmp, $targetPath);
        $_POST['settings']['site_logo'] = $fileName;
    }

    // Carousel image uploads
    for ($i = 1; $i <= 3; $i++) {
        if (!empty($_FILES["carousel_image_$i"]['name'])) {
            $fileTmp = $_FILES["carousel_image_$i"]['tmp_name'];
            $fileName = basename($_FILES["carousel_image_$i"]['name']);
            $targetPath = '../uploads/images/' . $fileName;
            move_uploaded_file($fileTmp, $targetPath);
            $_POST['settings']["carousel_image_$i"] = $fileName;
        }
    }

    // Save all settings
    if (isset($_POST['settings'])) {
        foreach ($_POST['settings'] as $key => $value) {
            saveSetting($key, $value, $pdo);
        }
        $success = "✅ Settings updated successfully.";
    }
}

// Load current settings
$stmt = $pdo->query("SELECT `key`, `value` FROM settings");
$settings = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $settings[$row['key']] = $row['value'];
}
?>

<h1>⚙️ Site Settings</h1>

<?php if (!empty($success)): ?>
    <p style="color: green;"><?= $success ?></p>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <table>
        <tr>
            <td><label for="site_name">🌍 Site Name:</label></td>
            <td><input type="text" id="site_name" name="settings[site_name]" value="<?= htmlspecialchars($settings['site_name'] ?? '') ?>" required></td>
        </tr>
        <tr>
            <td><label for="contact_email">📧 Contact Email:</label></td>
            <td><input type="email" id="contact_email" name="settings[contact_email]" value="<?= htmlspecialchars($settings['contact_email'] ?? '') ?>"></td>
        </tr>
        <tr>
            <td><label for="homepage_message">🏠 Homepage Message:</label></td>
            <td><textarea id="homepage_message" name="settings[homepage_message]" rows="3"><?= htmlspecialchars($settings['homepage_message'] ?? '') ?></textarea></td>
        </tr>
        <tr>
            <td><label for="site_vision">🌟 Vision:</label></td>
            <td><textarea id="site_vision" name="settings[site_vision]" rows="3"><?= htmlspecialchars($settings['site_vision'] ?? '') ?></textarea></td>
        </tr>
        <tr>
            <td><label for="site_mission">🎯 Mission:</label></td>
            <td><textarea id="site_mission" name="settings[site_mission]" rows="3"><?= htmlspecialchars($settings['site_mission'] ?? '') ?></textarea></td>
        </tr>
        <tr>
            <td><label for="site_logo">🖼️ Upload Logo:</label></td>
            <td>
                <input type="file" name="site_logo" id="site_logo">
                <?php if (!empty($settings['site_logo'])): ?>
                    <br><img src="../uploads/images/<?= htmlspecialchars($settings['site_logo']) ?>" alt="Logo" height="50">
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <hr>
    <h2>🎠 Carousel Slides (Up to 3)</h2>
    <?php for ($i = 1; $i <= 5; $i++): ?>
        <fieldset>
            <legend>Slide <?= $i ?></legend>
            <label>Image:</label>
            <input type="file" name="carousel_image_<?= $i ?>">
            <?php if (!empty($settings["carousel_image_$i"])): ?>
                <br><img src="../uploads/images/<?= htmlspecialchars($settings["carousel_image_$i"]) ?>" alt="Slide <?= $i ?>" height="50">
            <?php endif; ?>
            <br>
            <label>Headline:</label><br>
            <input type="text" name="settings[carousel_headline_<?= $i ?>]" value="<?= htmlspecialchars($settings["carousel_headline_$i"] ?? '') ?>" style="width: 100%;"><br>
            <label>Subtext:</label><br>
            <textarea name="settings[carousel_text_<?= $i ?>]" rows="2" style="width: 100%;"><?= htmlspecialchars($settings["carousel_text_$i"] ?? '') ?></textarea><br>
            <label>CTA Button Text:</label>
            <input type="text" name="settings[carousel_cta_text_<?= $i ?>]" value="<?= htmlspecialchars($settings["carousel_cta_text_$i"] ?? '') ?>"><br>
            <label>CTA Button Link:</label>
            <input type="text" name="settings[carousel_cta_link_<?= $i ?>]" value="<?= htmlspecialchars($settings["carousel_cta_link_$i"] ?? '') ?>">
        </fieldset>
        <br>
    <?php endfor; ?>

    <button type="submit">💾 Save All Settings</button>
</form>

<p><a href="dashboard.php">🔙 Back to Dashboard</a></p>
