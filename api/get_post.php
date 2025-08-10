<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../includes/db.php';

try {
    if (!isset($_GET['id'])) {
        echo json_encode(["status" => "error", "message" => "No ID provided"]);
        exit;
    }

    $id = (int) $_GET['id'];
    $base_url = "http://192.168.0.110:8000/uploads/posts/";

    $stmt = $pdo->prepare("
        SELECT id, title, body, 
               CONCAT(:base_url, featured_image) AS featured_image,
               region, likes, views, created_at
        FROM posts
        WHERE id = :id AND status = 'approved'
        LIMIT 1
    ");
    $stmt->bindValue(':base_url', $base_url);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($post) {
        echo json_encode(["status" => "success", "data" => $post]);
    } else {
        echo json_encode(["status" => "error", "message" => "Post not found"]);
    }
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
