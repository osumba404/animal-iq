<?php
// public/profile.php

require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once 'header.php';
require_once 'nav.php';

// Determine which profile to show (own profile or someone else's)
$id = $_GET['id'] ?? $_SESSION['user']['id'];

// Fetch user and badges
$stmt = $pdo->prepare("
    SELECT 
        u.id,
        u.name,
        u.email,
        u.bio,
        u.role,
        u.profile_picture,
        u.registered_at,
        GROUP_CONCAT(b.name SEPARATOR ',') AS badges,
        GROUP_CONCAT(b.icon SEPARATOR ',') AS badge_icons
    FROM users u
    LEFT JOIN user_badges ub ON u.id = ub.user_id
    LEFT JOIN badges b ON ub.badge_id = b.id
    WHERE u.id = :id
    GROUP BY u.id
");
$stmt->execute(['id' => $id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

$isOwner = ($id == $_SESSION['user']['id']);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $bio = $_POST['bio'];
    
    // Handle file upload
    $profile_picture = $user['profile_picture']; // Keep existing if no new file uploaded
    if (isset($_FILES['profile_picture'])) {
        $uploadDir = '../uploads/profile_pics/';
        $uploadFile = $uploadDir . basename($_FILES['profile_picture']['name']);
        
        // Generate unique filename
        $ext = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
        $newFilename = uniqid() . '.' . $ext;
        $uploadFile = $uploadDir . $newFilename;
        
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadFile)) {
            $profile_picture = $newFilename;
            // Delete old profile picture if not default
            if ($user['profile_picture'] !== 'default.png') {
                @unlink($uploadDir . $user['profile_picture']);
            }
        }
    }
    
    // Update database
    $stmt = $pdo->prepare("UPDATE users SET name = ?, bio = ?, profile_picture = ? WHERE id = ?");
    $stmt->execute([$name, $bio, $profile_picture, $id]);
    
    // Refresh user data
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Show success message
    $success = "Profile updated successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user['name']); ?> - Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            /* Core Brand Colors */
            --color-primary: #013221;         /* Deep jungle green */
            --color-primary-light: #10593c;    /* Lighter green */
            --color-primary-lighter: #1a8a6a;  /* Highlight teal */
            --color-primary-dark: #002418;     /* Darker green */
            
            /* Accent Colors */
            --color-accent-primary: #e8b824;   /* Gold */
            --color-accent-secondary: #d5a91c; /* Darker gold */
            
            /* Text Colors */
            --color-text-primary: #1E1811;     /* Main text */
            --color-text-muted: #A8A293;       /* Muted text */
            --color-text-inverted: #FFFFFF;    /* White text */
            
            /* Background Colors */
            --color-bg-primary: #FFF8E8;       /* Main background */
            --color-bg-secondary: #F5F0E0;     /* Secondary background */
            
            /* Utility Colors */
            --color-error: #c23b22;            /* Error color */
            --color-border-light: #D5CFC0;     /* Border color */
            
            /* Shadows */
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.1);
            
            /* Border Radius */
            --radius-sm: 4px;
            --radius-md: 8px;
            --radius-lg: 12px;
            
            /* Transitions */
            --transition: all 0.3s ease;
        }
        
        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            background-color: var(--color-bg-primary);
            color: var(--color-text-primary);
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        
        .profile-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .profile-card {
            background: var(--color-bg-primary);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            overflow: hidden;
            margin-bottom: 2rem;
            border: 1px solid var(--color-border-light);
        }
        
        .profile-header {
            background: var(--color-primary);
            color: var(--color-text-inverted);
            padding: 2rem;
            text-align: center;
            position: relative;
        }
        
        .profile-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 4px solid var(--color-accent-primary);
            object-fit: cover;
            margin: 0 auto 1rem;
            display: block;
            background: var(--color-bg-primary);
        }
        
        .profile-name {
            font-size: 2rem;
            margin: 0.5rem 0;
            color: var(--color-text-inverted);
        }
        
        .profile-role {
            display: inline-block;
            background: var(--color-accent-primary);
            color: var(--color-primary-dark);
            padding: 0.25rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            margin: 0.5rem 0;
        }
        
        .profile-bio {
            max-width: 600px;
            margin: 1rem auto 0;
            color: var(--color-text-inverted);
            opacity: 0.9;
        }
        
        .profile-content {
            display: flex;
            flex-wrap: wrap;
            padding: 2rem;
            gap: 2rem;
        }
        
        .profile-section {
            flex: 1;
            min-width: 300px;
        }
        
        .section-title {
            font-size: 1.25rem;
            color: var(--color-primary);
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid var(--color-accent-primary);
            display: inline-block;
        }
        
        /* Badges Section */
        .badges-container {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .badge {
            display: flex;
            align-items: center;
            background: var(--color-bg-secondary);
            border-radius: var(--radius-md);
            padding: 0.75rem 1rem;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            border: 1px solid var(--color-border-light);
            min-width: 200px;
        }
        
        .badge:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
            border-color: var(--color-accent-primary);
        }
        
        .badge-icon {
            font-size: 1.5rem;
            margin-right: 1rem;
            color: var(--color-accent-primary);
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(232, 184, 36, 0.1);
            border-radius: 50%;
        }
        
        .badge-info {
            flex: 1;
        }
        
        .badge-title {
            font-weight: 600;
            margin: 0 0 0.25rem 0;
            color: var(--color-primary);
        }
        
        .badge-description {
            font-size: 0.85rem;
            color: var(--color-text-muted);
            margin: 0;
        }
        
        /* Edit Profile Button */
        .edit-profile-btn {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            background: var(--color-accent-primary);
            color: var(--color-primary-dark);
            border: none;
            border-radius: var(--radius-md);
            padding: 0.5rem 1rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: var(--transition);
        }
        
        .edit-profile-btn:hover {
            background: var(--color-accent-secondary);
            transform: translateY(-2px);
        }
        
        /* Modal Popup Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .modal.show {
            display: flex;
            opacity: 1;
        }
        
        .modal-popup {
            background: var(--color-bg-primary);
            border-radius: var(--radius-lg);
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: var(--shadow-lg);
            transform: translateY(-20px);
            transition: transform 0.3s ease;
            position: relative;
        }
        
        .show .modal-popup {
            transform: translateY(0);
        }
        
        .modal-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--color-border-light);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-header h2 {
            margin: 0;
            font-size: 1.5rem;
            color: var(--color-primary);
        }
        
        .close-modal {
            background: none;
            border: none;
            font-size: 1.75rem;
            cursor: pointer;
            color: var(--color-text-muted);
            transition: var(--transition);
            line-height: 1;
            padding: 0.5rem;
            margin: -0.5rem -0.5rem -0.5rem auto;
        }
        
        .close-modal:hover {
            color: var(--color-error);
        }
        
        .modal-body {
            padding: 1.5rem;
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid var(--color-border-light);
        }
        
        .btn {
            padding: 0.5rem 1.25rem;
            border-radius: var(--radius-md);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            border: none;
        }
        
        .btn-primary {
            background: var(--color-primary);
            color: var(--color-text-inverted);
        }
        
        .btn-primary:hover {
            background: var(--color-primary-dark);
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: var(--color-bg-secondary);
            color: var(--color-text-primary);
        }
        
        .btn-secondary:hover {
            background: #e6e1d2;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .profile-header {
                padding: 1.5rem 1rem;
            }
            
            .profile-avatar {
                width: 120px;
                height: 120px;
            }
            
            .profile-name {
                font-size: 1.75rem;
            }
            
            .profile-content {
                flex-direction: column;
                padding: 1.5rem 1rem;
            }
            
            .edit-profile-btn {
                position: static;
                margin: 1rem auto 0;
                display: inline-flex;
            }
        }
        
        @media (max-width: 480px) {
            .profile-header {
                padding: 1.5rem 0.5rem;
            }
            
            .profile-avatar {
                width: 100px;
                height: 100px;
            }
            
            .profile-name {
                font-size: 1.5rem;
            }
            
            .badge {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="profile-card">
            <div class="profile-header">
                <?php if ($isOwner): ?>
                <button class="edit-profile-btn" id="editProfileBtn">
                    <i class="fas fa-edit"></i> Edit Profile
                </button>
                <?php endif; ?>
                
                <img src="../uploads/profile_pics/<?php echo htmlspecialchars($user['profile_picture'] ?? 'default.png'); ?>" 
                     alt="Profile Picture" class="profile-avatar">
                
                <h1 class="profile-name"><?php echo htmlspecialchars($user['name']); ?></h1>
                
                <?php if (!empty($user['role'])): ?>
                    <span class="profile-role"><?php echo htmlspecialchars(ucfirst($user['role'])); ?></span>
                <?php endif; ?>
                
                <?php if (!empty($user['bio'])): ?>
                    <p class="profile-bio"><?php echo nl2br(htmlspecialchars($user['bio'])); ?></p>
                <?php endif; ?>
            </div>
            
            <div class="profile-content">
                <div class="profile-section">
                    <h2 class="section-title">Badges Earned</h2>
                    <div class="badges-container">
                        <?php 
                        if (!empty($user['badges'])) {
                            $badges = explode(',', $user['badges']);
                            $badgeIcons = explode(',', $user['badge_icons']);
                            
                            foreach ($badges as $index => $badge) {
                                $icon = $badgeIcons[$index] ?? 'fa-award';
                                echo "
                                <div class='badge'>
                                    <div class='badge-icon'><i class='fas fa-{$icon}'></i></div>
                                    <div class='badge-info'>
                                        <h4 class='badge-title'>{$badge}</h4>
                                        <p class='badge-description'>Earned for your contributions</p>
                                    </div>
                                </div>";
                            }
                        } else {
                            echo "<p>No badges earned yet. Stay active to earn badges!</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Profile Modal Popup -->
    <div id="editProfileModal" class="modal">
        <div class="modal-popup">
            <div class="modal-header">
                <h2>Edit Profile</h2>
                <button class="close-modal" id="closeModalBtn">&times;</button>
            </div>
            <div class="modal-body">
                <form action="" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                    
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="bio">Bio</label>
                        <textarea id="bio" name="bio" rows="4"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="profile_picture">Profile Picture</label>
                        <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
                        <small>Leave blank to keep current image</small>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" id="cancelEdit">Cancel</button>
                        <button type="submit" name="update_profile" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Modal functionality
        const editProfileBtn = document.getElementById('editProfileBtn');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const cancelEditBtn = document.getElementById('cancelEdit');
        const editProfileModal = document.getElementById('editProfileModal');
        
        function showModal() {
            editProfileModal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
        
        function hideModal() {
            editProfileModal.classList.remove('show');
            document.body.style.overflow = 'auto';
        }
        
        if (editProfileBtn) {
            editProfileBtn.addEventListener('click', showModal);
        }
        
        if (closeModalBtn) {
            closeModalBtn.addEventListener('click', hideModal);
        }
        
        if (cancelEditBtn) {
            cancelEditBtn.addEventListener('click', hideModal);
        }
        
        // Close modal when clicking outside
        editProfileModal.addEventListener('click', (e) => {
            if (e.target === editProfileModal) {
                hideModal();
            }
        });
        
        // Close modal with Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && editProfileModal.classList.contains('show')) {
                hideModal();
            }
        });
    </script>
</body>
</html>

<?php require_once 'footer.php'; ?>