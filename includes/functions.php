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



// FETCHING APPROVED ANImals
function getAllApprovedAnimals($pdo, $limit = 10, $offset = 0, $category = null, $search = '')
{
    $sql = "SELECT 
                a.id,
                a.common_name,
                a.scientific_name,
                a.population_estimate,
                a.avg_weight_kg,
                a.avg_length_cm,
                a.appearance,
                a.main_photo,
                a.status,
              
                a.created_at,
                ss.label AS species_status,
                
                sp.name AS species,
                g.name AS genus,
                f.name AS family,
                o.name AS `order`,
                c.name AS class,
                p.name AS phylum,
                k.name AS kingdom

            FROM animals a
            LEFT JOIN species_statuses ss ON a.species_status_id = ss.id
            LEFT JOIN taxonomy t ON a.id = t.animal_id
            LEFT JOIN species sp ON t.species_id = sp.id
            LEFT JOIN genera g ON sp.genus_id = g.id
            LEFT JOIN families f ON g.family_id = f.id
            LEFT JOIN orders o ON f.order_id = o.id
            LEFT JOIN classes c ON o.class_id = c.id
            LEFT JOIN phyla p ON c.phylum_id = p.id
            LEFT JOIN kingdoms k ON p.kingdom_id = k.id
            WHERE a.status = 'approved'";

    $params = [];

    if ($category) {
        $sql .= " AND c.name = :category"; // filtering by class name
        $params[':category'] = $category;
    }

    if (!empty($search)) {
        $sql .= " AND (a.common_name LIKE :search OR a.scientific_name LIKE :search)";
        $params[':search'] = "%$search%";
    }

    $sql .= " ORDER BY a.common_name ASC LIMIT :limit OFFSET :offset";

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
    $stmt = $pdo->prepare("SELECT id, name, email, role, profile_pic AS profile_picture, registered_at FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}








// ========HOME PAGe
function getRandomTrivia($pdo) {
    $stmt = $pdo->query("SELECT fact FROM trivia ORDER BY RAND() LIMIT 1");
    return $stmt->fetchColumn();
}


// fetch quizes for public/quizzes.php
function getLatestQuizzes(PDO $pdo, int $limit = 5): array {
    $stmt = $pdo->prepare("SELECT * FROM quizzes WHERE is_published = 1 ORDER BY created_at DESC LIMIT :limit");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// fetching blogs/posts
function getApprovedBlogs($pdo, $search = null) {
    $query = "
         SELECT p.id, p.title, p.featured_image, LEFT(p.body, 300) AS summary, p.created_at, a.full_name AS author_name
        FROM posts p
        JOIN admins a ON p.author_id = a.id
        -- WHERE p.type = 'article' AND p.status = 'approved'
    ";

    $params = [];

    if (!empty($search)) {
        $query .= " AND (p.title LIKE ? OR p.body LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    $query .= " ORDER BY p.created_at DESC";

    // if ($limit !== null) {
    //     $query .= " LIMIT ?";
    //     $params[] = (int)$limit;
    // }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);

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
        SELECT animals.common_name, animals.main_photo
        FROM animals
        JOIN species_statuses ON animals.species_status_id = species_statuses.id
        WHERE species_status_id = 2
        ORDER BY RAND()
        LIMIT ?
    ");
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}




?>
