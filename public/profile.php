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
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user['name']); ?> - Profile</title>
    <link href="assets/css/profile.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --success-color: #2ecc71;
            --danger-color: #e74c3c;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            --border-radius: 12px;
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: #333;
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
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            position: relative;
            margin-bottom: 2rem;
            transition: var(--transition);
        }

        .profile-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            padding: 2rem 2rem 6rem 2rem;
            color: white;
            text-align: center;
            position: relative;
        }

        .avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 4px solid white;
            margin: 0 auto;
            overflow: hidden;
            background: var(--light-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4em;
            font-weight: bold;
            color: var(--primary-color);
            position: relative;
            z-index: 2;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-body {
            padding: 5rem 2rem 2rem 2rem;
            margin-top: -4rem;
            position: relative;
            z-index: 1;
        }

        .profile-name {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-align: center;
            color: var(--dark-color);
        }

        .profile-role {
            color: var(--accent-color);
            text-align: center;
            font-weight: 600;
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
        }

        .profile-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .info-card {
            background: var(--light-color);
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .info-card h3 {
            margin-top: 0;
            color: var(--primary-color);
            border-bottom: 2px solid var(--accent-color);
            padding-bottom: 0.5rem;
            display: inline-block;
        }

        .badges-container {
            margin-top: 2rem;
        }

        .badges-title {
            text-align: center;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            color: var(--dark-color);
        }

        .badges {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 1rem;
        }

        .badge {
            display: flex;
            align-items: center;
            background: white;
            border-radius: 50px;
            padding: 0.5rem 1rem;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            transition: var(--transition);
        }

        .badge:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .badge-icon {
            width: 24px;
            height: 24px;
            margin-right: 0.5rem;
            border-radius: 50%;
        }

        .edit-btn {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            background: white;
            color: var(--primary-color);
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            transition: var(--transition);
            z-index: 3;
        }

        .edit-btn:hover {
            background: var(--primary-color);
            color: white;
            transform: rotate(15deg);
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 100;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .modal.show {
            display: flex;
            opacity: 1;
        }

        .modal-content {
            background: white;
            border-radius: var(--border-radius);
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transform: translateY(-20px);
            transition: transform 0.3s ease;
            position: relative;
        }

        .modal.show .modal-content {
            transform: translateY(0);
        }

        .modal-header {
            padding: 1.5rem;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            margin: 0;
            color: var(--primary-color);
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--dark-color);
            transition: var(--transition);
        }

        .close-btn:hover {
            color: var(--danger-color);
            transform: rotate(90deg);
        }

        .modal-body {
            padding: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--dark-color);
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: var(--accent-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }

        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }

        .file-upload {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }

        .file-upload-btn {
            width: 100%;
            padding: 0.75rem;
            background: var(--light-color);
            color: var(--dark-color);
            border: 1px dashed #ccc;
            border-radius: var(--border-radius);
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
        }

        .file-upload-btn:hover {
            background: #e9ecef;
        }

        .file-upload input[type="file"] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .submit-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            width: 100%;
        }

        .submit-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }

        .alert {
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-bottom: 1rem;
            font-weight: 500;
        }

        .alert-success {
            background-color: rgba(46, 204, 113, 0.1);
            border-left: 4px solid var(--success-color);
            color: var(--dark-color);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .profile-header {
                padding: 1.5rem 1.5rem 5rem 1.5rem;
            }
            
            .avatar {
                width: 120px;
                height: 120px;
                font-size: 3em;
            }
            
            .profile-body {
                padding: 4rem 1.5rem 1.5rem 1.5rem;
            }
            
            .profile-name {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .profile-header {
                padding: 1rem 1rem 4rem 1rem;
            }
            
            .avatar {
                width: 100px;
                height: 100px;
            }
            
            .profile-info {
                grid-template-columns: 1fr;
            }
            
            .modal-content {
                width: 95%;
            }
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="profile-card">
            <div class="profile-header">
                <?php if ($isOwner): ?>
                    <button class="edit-btn" id="editProfileBtn">
                        <i class="fas fa-pencil-alt"></i>
                    </button>
                <?php endif; ?>
                
                <div class="avatar">
                    <?php if (!empty($user['profile_picture']) && $user['profile_picture'] !== 'default.png'): ?>
                        <img src="../uploads/profile_pics/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture">
                    <?php else: ?>
                        <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="profile-body">
                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <h1 class="profile-name"><?php echo htmlspecialchars($user['name']); ?></h1>
                <p class="profile-role"><?php echo ucfirst($user['role']); ?></p>
                
                <div class="profile-info">
                    <div class="info-card">
                        <h3>Contact Information</h3>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                        <p><strong>Joined:</strong> <?php echo date('F j, Y', strtotime($user['registered_at'])); ?></p>
                    </div>
                    
                    <div class="info-card">
                        <h3>About Me</h3>
                        <p><?php echo nl2br(htmlspecialchars($user['bio'] ?? 'No bio available.')); ?></p>
                    </div>
                </div>
                
                <?php if (!empty($user['badges'])): ?>
                    <div class="badges-container">
                        <h3 class="badges-title">Achievements & Badges</h3>
                        <div class="badges">
                            <?php 
                            $badgeNames = explode(',', $user['badges']);
                            $badgeIcons = explode(',', $user['badge_icons']);
                            foreach ($badgeNames as $index => $badgeName): ?>
                                <div class="badge">
                                    <?php if (!empty($badgeIcons[$index])): ?>
                                        <img src="../uploads/icons/<?php echo htmlspecialchars($badgeIcons[$index]); ?>" alt="<?php echo htmlspecialchars($badgeName); ?>" class="badge-icon">
                                    <?php endif; ?>
                                    <span><?php echo htmlspecialchars($badgeName); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div class="modal" id="editProfileModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Profile</h2>
                <button class="close-btn" id="closeModalBtn">&times;</button>
            </div>
            <div class="modal-body">
                <form action="" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                    <input type="hidden" name="update_profile" value="1">
                    
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="bio">Bio</label>
                        <textarea name="bio" id="bio" class="form-control"><?php echo htmlspecialchars($user['bio']); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="profile_picture">Profile Picture</label>
                        <div class="file-upload">
                            <button type="button" class="file-upload-btn">Choose File</button>
                            <input type="file" name="profile_picture" id="profile_picture">
                        </div>
                    </div>
                    
                    <button type="submit" class="submit-btn">Save Changes</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Modal functionality
        const editProfileBtn = document.getElementById('editProfileBtn');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const editProfileModal = document.getElementById('editProfileModal');
        
        if (editProfileBtn) {
            editProfileBtn.addEventListener('click', () => {
                editProfileModal.classList.add('show');
                document.body.style.overflow = 'hidden';
            });
        }
        
        closeModalBtn.addEventListener('click', () => {
            editProfileModal.classList.remove('show');
            document.body.style.overflow = 'auto';
        });
        
        // Close modal when clicking outside
        editProfileModal.addEventListener('click', (e) => {
            if (e.target === editProfileModal) {
                editProfileModal.classList.remove('show');
                document.body.style.overflow = 'auto';
            }
        });
        
        // File upload button text
        const fileInput = document.getElementById('profile_picture');
        const uploadBtn = document.querySelector('.file-upload-btn');
        
        if (fileInput && uploadBtn) {
            fileInput.addEventListener('change', function() {
                if (this.files && this.files.length > 0) {
                    uploadBtn.textContent = this.files[0].name;
                } else {
                    uploadBtn.textContent = 'Choose File';
                }
            });
        }
    </script>
</body>
</html>