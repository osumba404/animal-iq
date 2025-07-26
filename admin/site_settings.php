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
        $success = "Settings updated successfully.";
    }
}

// Load current settings
$stmt = $pdo->query("SELECT `key`, `value` FROM settings");
$settings = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $settings[$row['key']] = $row['value'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Settings - Admin Panel</title>
    <link rel="stylesheet" href="css/main.css">
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
        
        .card {
            border: 1px solid var(--color-neutral-mid);
            border-radius: 8px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .card-header {
            background-color: var(--color-primary-accent);
            color: var(--color-primary-light);
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--color-neutral-mid);
            font-weight: 600;
        }
        
        .card-body {
            padding: 1.5rem;
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
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--color-neutral-mid);
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .form-group textarea {
            min-height: 100px;
        }
        
        .img-preview {
            max-height: 100px;
            margin-top: 0.5rem;
            border: 1px solid var(--color-neutral-mid);
            border-radius: 4px;
            padding: 0.25rem;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            font-size: 1rem;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
            border: none;
        }
        
        .btn-primary {
            background-color: var(--color-primary-accent);
            color: var(--color-primary-light);
        }
        
        .btn-primary:hover {
            background-color: var(--color-secondary-accent);
        }
        
        .btn-back {
            background-color: var(--color-neutral-mid);
            color: var(--color-primary-dark);
        }
        
        .btn-back:hover {
            background-color: var(--color-primary-mid);
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 1.5rem;
        }
        
        .settings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        
        .settings-card {
            background-color: var(--color-neutral-light);
            border-radius: 6px;
            padding: 1rem;
            height: 100%;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
            background-color: rgba(46, 204, 113, 0.2);
            color: #2ecc71;
            border: 1px solid rgba(46, 204, 113, 0.3);
        }
        
        @media (max-width: 768px) {
            .admin-content-container {
                margin-left: 0;
                padding: 1rem;
            }
            
            .settings-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-content-container">
        <div class="page-header">
            <h1><i class="fas fa-cog"></i> Site Settings</h1>
            <a href="dashboard.php" class="btn btn-back">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <?php if (!empty($success)): ?>
            <div class="alert">
                <i class="fas fa-check-circle"></i> <?= $success ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-globe"></i> General Settings
                </div>
                <div class="card-body">
                    <div class="settings-grid">
                        <div class="form-group">
                            <label for="site_name">Site Name</label>
                            <input type="text" id="site_name" name="settings[site_name]" 
                                   value="<?= htmlspecialchars($settings['site_name'] ?? '') ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="contact_email">Contact Email</label>
                            <input type="email" id="contact_email" name="settings[contact_email]" 
                                   value="<?= htmlspecialchars($settings['contact_email'] ?? '') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="site_logo">Site Logo</label>
                            <input type="file" id="site_logo" name="site_logo">
                            <?php if (!empty($settings['site_logo'])): ?>
                                <div class="mt-1">
                                    <img src="../uploads/images/<?= htmlspecialchars($settings['site_logo']) ?>" 
                                         alt="Current Logo" class="img-preview">
                                    <p class="text-muted">Current logo</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="homepage_message">Homepage Message</label>
                        <textarea id="homepage_message" name="settings[homepage_message]" 
                                  rows="3"><?= htmlspecialchars($settings['homepage_message'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="settings-grid">
                        <div class="form-group">
                            <label for="site_vision">Vision Statement</label>
                            <textarea id="site_vision" name="settings[site_vision]" 
                                      rows="3"><?= htmlspecialchars($settings['site_vision'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="site_mission">Mission Statement</label>
                            <textarea id="site_mission" name="settings[site_mission]" 
                                      rows="3"><?= htmlspecialchars($settings['site_mission'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <i class="fas fa-images"></i> Carousel Slides
                </div>
                <div class="card-body">
                    <div class="settings-grid">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <div class="settings-card">
                                <h3 class="card-title">Slide <?= $i ?></h3>
                                <div class="form-group">
                                    <label>Slide Image</label>
                                    <input type="file" name="carousel_image_<?= $i ?>">
                                    <?php if (!empty($settings["carousel_image_$i"])): ?>
                                        <div class="mt-1">
                                            <img src="../uploads/images/<?= htmlspecialchars($settings["carousel_image_$i"]) ?>" 
                                                 alt="Slide <?= $i ?>" class="img-preview">
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="form-group">
                                    <label>Headline</label>
                                    <input type="text" name="settings[carousel_headline_<?= $i ?>]" 
                                           value="<?= htmlspecialchars($settings["carousel_headline_$i"] ?? '') ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label>Subtext</label>
                                    <textarea rows="2" name="settings[carousel_text_<?= $i ?>]"><?= htmlspecialchars($settings["carousel_text_$i"] ?? '') ?></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label>Button Text</label>
                                    <input type="text" name="settings[carousel_cta_text_<?= $i ?>]" 
                                           value="<?= htmlspecialchars($settings["carousel_cta_text_$i"] ?? '') ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label>Button Link</label>
                                    <input type="text" name="settings[carousel_cta_link_<?= $i ?>]" 
                                           value="<?= htmlspecialchars($settings["carousel_cta_link_$i"] ?? '') ?>">
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save All Settings
                </button>
            </div>
        </form>
    </div>
</body>
</html>