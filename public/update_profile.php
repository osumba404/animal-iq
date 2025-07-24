<?php
// public/update_profile.php
require_once '../includes/auth.php';
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];

    // Ensure only the logged-in user can update their own profile
    if ($id !== $_SESSION['user_id']) {
        die("Unauthorized access.");
    }

    // Handle image upload
    $uploadFile = null;
    if (!empty($_FILES['profile_picture']['name'])) {
        $targetDir = "uploads/profile_pics/";
        $fileName = basename($_FILES['profile_picture']['name']);
        $uploadFile = $targetDir . time() . "_" . $fileName;

        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $uploadFile)) {
            $uploadFile = basename($uploadFile); // store only filename
        } else {
            $uploadFile = null;
        }
    }

    // Update
    if ($uploadFile) {
        $stmt = $pdo->prepare("UPDATE users SET name = :name, profile_picture = :pic WHERE id = :id");
        $stmt->execute([
            'name' => $name,
            'pic' => $uploadFile,
            'id' => $id
        ]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET name = :name WHERE id = :id");
        $stmt->execute([
            'name' => $name,
            'id' => $id
        ]);
    }

    header("Location: profile.php?id=$id");
    exit;
}
?>
