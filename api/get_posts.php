<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // allow Flutter to fetch

require_once '../includes/db.php';

try {
    $base_url = "http://192.168.0.110:8000/uploads/posts/"; 

    // Fetch approved blog posts only
    $stmt = $pdo->prepare("
        SELECT id, title, body, 
               CONCAT(:base_url, featured_image) AS featured_image, 
               region, likes, views, created_at
        FROM posts
        ORDER BY created_at DESC
    ");

    // Bind base URL to SQL query
    $stmt->bindValue(':base_url', $base_url);

    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "data" => $posts
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
