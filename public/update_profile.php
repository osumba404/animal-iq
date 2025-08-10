<?php
// public/update_profile.php
require_once '../includes/auth.php';
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $name = trim($_POST['name']);
    $bio = trim($_POST['bio'] ?? '');

    // Ensure only the logged-in user can update their own profile
    if ((int)$id !== (int)$_SESSION['user']['id']) {
        die("Unauthorized access.");
    }

    // Handle image upload
    $uploadFile = null;
    if (!empty($_FILES['profile_picture']['name'])) {
        $targetDir = "../uploads/profile_pics/";
        // Ensure uploads directory exists
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $fileName = basename($_FILES['profile_picture']['name']);
        $safeFileName = preg_replace("/[^a-zA-Z0-9_\.-]/", "_", $fileName); // sanitize filename
        $uploadFile = $targetDir . time() . "_" . $safeFileName;

        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $uploadFile)) {
            $uploadFile = basename($uploadFile); // store only filename
        } else {
            $uploadFile = null;
        }
    }

    // Build update query
    if ($uploadFile) {
        $stmt = $pdo->prepare("
            UPDATE users 
            SET name = :name, bio = :bio, profile_picture = :pic 
            WHERE id = :id
        ");
        $stmt->execute([
            'name' => $name,
            'bio' => $bio,
            'pic' => $uploadFile,
            'id' => $id
        ]);
    } else {
        $stmt = $pdo->prepare("
            UPDATE users 
            SET name = :name, bio = :bio 
            WHERE id = :id
        ");
        $stmt->execute([
            'name' => $name,
            'bio' => $bio,
            'id' => $id
        ]);
    }

    // Update session data so changes reflect immediately
    $_SESSION['user']['name'] = $name;
    $_SESSION['user']['bio'] = $bio;
    if ($uploadFile) {
        $_SESSION['user']['profile_picture'] = $uploadFile;
    }

    header("Location: profile.php?id=$id");
    exit;
}
?>
