<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = intval($_POST['post_id']);
    $title = $_POST['title'];
    $body = $_POST['body'];
    $type = $_POST['type'];
    $region = $_POST['region'];
    $status = $_POST['status'];
    $author_id = intval($_POST['author_id']);

    // Handle image upload if provided
    $featured_image = null;
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION);
        $filename = 'post_' . time() . '.' . $ext;
        $destination = '../uploads/posts/' . $filename;

        if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $destination)) {
            $featured_image = $filename;
        }
    }

    // Update query
    if ($featured_image) {
        $sql = "UPDATE posts SET 
                    title = ?, 
                    body = ?, 
                    type = ?, 
                    region = ?, 
                    status = ?, 
                    author_id = ?, 
                    featured_image = ? 
                WHERE id = ?";
        $params = [$title, $body, $type, $region, $status, $author_id, $featured_image, $post_id];
    } else {
        $sql = "UPDATE posts SET 
                    title = ?, 
                    body = ?, 
                    type = ?, 
                    region = ?, 
                    status = ?, 
                    author_id = ? 
                WHERE id = ?";
        $params = [$title, $body, $type, $region, $status, $author_id, $post_id];
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    header("Location: manage_posts.php?updated=1");
    exit;
} else {
    die("Invalid request.");
}
