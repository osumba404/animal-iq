<!-- includes/functions.php -->
<?php

// Sanitizes user input by trimming and converting special characters
function sanitize_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Redirects to another URL
function redirect($url) {
    header("Location: $url");
    exit();
}

// Formats a datetime string to a more readable format
function formatDateTime($datetime) {
    return date("F j, Y, g:i a", strtotime($datetime));
}

// Checks if a user is logged in by session email
function is_logged_in() {
    return isset($_SESSION['user_email']);
}


function getAllApprovedAnimals($pdo, $limit = 10, $offset = 0, $category = null, $search = '')
{
    $sql = "SELECT animals.*, taxonomy.family, taxonomy.genus
            FROM animals
            LEFT JOIN taxonomy ON animals.id = taxonomy.animal_id
            WHERE animals.status = 'approved'";
    
    $params = [];

    if ($category) {
        $sql .= " AND taxonomy.class = :category";
        $params[':category'] = $category;
    }

    if (!empty($search)) {
        $sql .= " AND (animals.common_name LIKE :search OR animals.scientific_name LIKE :search)";
        $params[':search'] = "%$search%";
    }

    $sql .= " ORDER BY animals.common_name ASC LIMIT :limit OFFSET :offset";


    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



// ==========gets user profile data
function getUserProfile($pdo, $id) {
    $stmt = $pdo->prepare("SELECT id, name, email, registered_at, profile_picture FROM users WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}







// ========HOME PAGe
function getRandomTrivia($pdo) {
    $stmt = $pdo->query("SELECT fact FROM trivia ORDER BY RAND() LIMIT 1");
    return $stmt->fetchColumn();
}

// function getLatestQuizzes($pdo, $limit = 3) {
//     $stmt = $pdo->prepare("SELECT id, title FROM quizzes ORDER BY created_at DESC LIMIT ?");
//     $stmt->bindValue(1, $limit, PDO::PARAM_INT);
//     $stmt->execute();
//     return $stmt->fetchAll(PDO::FETCH_ASSOC);
// }

// fetch quizes for public/quizzes.php
function getLatestQuizzes(PDO $pdo, int $limit = 5): array {
    $stmt = $pdo->prepare("SELECT * FROM quizzes WHERE is_published = 1 ORDER BY created_at DESC LIMIT :limit");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getApprovedBlogs($pdo, $limit = 3) {
    $stmt = $pdo->prepare("
        SELECT p.id, p.title, LEFT(p.body, 300) AS summary, u.name AS author_name
        FROM posts p
        JOIN users u ON p.author_id = u.id
        WHERE p.type = 'blog' AND p.status = 'approved'
        ORDER BY p.created_at DESC
        LIMIT ?
    ");
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getUpcomingEvents($pdo, $limit = 3) {
    $stmt = $pdo->prepare("
        SELECT id, title, event_date, location
        FROM events
        WHERE event_date >= NOW()
        ORDER BY event_date ASC
        LIMIT ?
    ");
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getHighlightedEndangeredSpecies($pdo, $limit = 3) {
    $stmt = $pdo->prepare("
        SELECT common_name, main_photo
        FROM animals
        WHERE species_status = 'endangered'
        ORDER BY RAND()
        LIMIT ?
    ");
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



?>
